<?php

namespace Taopix\ControlCentre\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Taopix\ControlCentre\Entity\ApplicationFile;
use Taopix\ControlCentre\Entity\Product;

class ProductCollectionLinkRepository extends EntityRepository
{
    public function getProductsForLiveSearch(string $query): array
    {
        $qb = $this->createQueryBuilder('pcl');
        return $qb
            ->select('pcl.id, pcl.collectionCode, pcl.collectionName, pcl.productCode, pcl.productName, af.versionDate, pcl.collectionPreviewResourceRef, pcl.productPreviewResourceRef')
            ->innerJoin(ApplicationFile::class, 'af', Join::WITH, 'af.ref = pcl.collectionCode')
            ->where($qb->expr()->like('pcl.productCode', ':searchquery'))
            ->orWhere($qb->expr()->like('pcl.collectionCode', ':searchquery'))
            ->orWhere($qb->expr()->like('pcl.productName', ':searchquery'))
            ->orWhere($qb->expr()->like('pcl.collectionName', ':searchquery'))
            ->setParameter('searchquery', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    public function getProductsForExperienceOverview(array $criteria, int $page = 1, bool $countOnlyMode = false): array
    {
        $limit = 100;

        $qb = $this->createQueryBuilder('pcl');
        $qb->select(
            ($countOnlyMode)
                ?
                'count(pcl.id) as count'
                :
                'pcl.id, pcl.collectionCode, pcl.collectionName, pcl.productCode, pcl.productName, 
                    pcl.collectionType, pcl.availableOnline'
        )
        ->innerJoin(Product::class, 'p', Join::WITH, 'p.code = pcl.productCode');


        if ($criteria['searchTerm'] !== '') {
            $qb->where($qb->expr()->like('pcl.productCode', ':searchquery'))
                ->orWhere($qb->expr()->like('pcl.collectionCode', ':searchquery'))
                ->orWhere($qb->expr()->like('pcl.productName', ':searchquery'))
                ->orWhere($qb->expr()->like('pcl.collectionName', ':searchquery'));
        }

        $qb->andWhere('p.retroPrints = :retroprints')
            ->andWhere('pcl.collectionType = :collectiontype')
            ->andWhere('pcl.availableOnline = :availableonline');

        if ($criteria['searchTerm'] !== '') {
            $qb->setParameter('searchquery', '%' . str_replace('_', '\_', $criteria['searchTerm']) . '%');
        }

        $qb
            ->setParameter('availableonline', $criteria['availableOnline'])
            ->setParameter('collectiontype', $criteria['collectionType'])
            ->setParameter('retroprints', $criteria['retroPrints']);

        if (!$countOnlyMode) {
            $qb 
                ->setFirstResult((($page - 1) * $limit))
                ->setMaxResults($limit);
        }

        return $qb
            ->addOrderBy('pcl.collectionCode', 'ASC')
            ->addOrderBy('pcl.productCode', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
