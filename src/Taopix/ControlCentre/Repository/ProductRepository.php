<?php

namespace Taopix\ControlCentre\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query;
use Taopix\ControlCentre\Entity\ProductCollectionLink;
class ProductRepository extends EntityRepository
{
	public function getAutoUpdateProductList(array $criteria): array
	{
		$rsm = (new ResultSetMapping())
			->addScalarResult('id', 'id')
			->addScalarResult('companycode', 'companyCode')
			->addScalarResult('code', 'code')
			->addScalarResult('name', 'name')
			->addScalarResult('taxlevel', 'taxLevel')
			->addScalarResult('createnewprojects', 'createNewProjects')
			->addScalarResult('productoptions', 'productOptions')
			->addScalarResult('pricetransformationstage', 'priceTransformationStage')
			->addScalarResult('active', 'active')
			->addScalarResult('deleted', 'deleted')
			->addScalarResult('collectioncode', 'collectionCode')
			->addScalarResult('collectiontype', 'collectionType')
			->addScalarResult('availabledesktop', 'availableDesktop')
			->addScalarResult('hasbeenavailabledesktop', 'hasBeenAvailableDesktop',)
			->addScalarResult('publishversion', 'publishVersion')
			->addScalarResult('collectionthumbnailresourceref', 'collectionThumbnailResourceRef')
			->addScalarResult('collectionthumbnailresourcedatauid', 'collectionThumbnailResourceDataUid')
			->addScalarResult('collectionpreviewresourceref', 'collectionPreviewResourceRef')
			->addScalarResult('collectionpreviewresourcedatauid', 'collectionPreviewResourceDataUid')
			->addScalarResult('collectionsortlevel', 'collectionSortLevel')
			->addScalarResult('collectiontextengineversion', 'collectionTextEngineVersion')
			->addScalarResult('producttarget', 'productTarget')
			->addScalarResult('productminpagecount', 'productMinPageCount')
			->addScalarResult('productaimodedesktop', 'productAiModeDesktop')
			->addScalarResult('productselectormodedesktop', 'productSelectorModeDesktop');
		$rawQuery = 'SELECT p.id, p.companycode, p.code, p.name, p.taxlevel, p.createnewprojects, p.productoptions, p.pricetransformationstage, p.active, p.deleted, ' .
					'pcl.collectioncode, pcl.collectiontype, pcl.availabledesktop, pcl.hasbeenavailabledesktop, pcl.publishversion, ' .
					'pcl.collectionthumbnailresourceref, pcl.collectionthumbnailresourcedatauid, pcl.collectionpreviewresourceref, ' .
					'pcl.collectionpreviewresourcedatauid, pcl.collectionsortlevel, pcl.collectiontextengineversion, ' .
					'pcl.producttarget, pcl.productminpagecount, pcl.productaimodedesktop, pcl.productselectormodedesktop ' .
					'FROM `' . $this->_em->getClassMetadata($this->getClassName())->getSchemaName() . '`.`' . $this->_em->getClassMetadata($this->getClassName())->getTableName() . '` as p ' .
					'LEFT JOIN `' . $this->_em->getClassMetadata(ProductCollectionLink::class)->getSchemaName() . '`.`' . $this->_em->getClassMetadata(ProductCollectionLink::class)->getTableName() . '` as pcl ON pcl.productcode = p.code';
		$whereAdded = false;
		$params = [];

		// Validate that we have a companyCode criteria that is not empty.
		if ('' !== ($criteria['companyCode'] ?? '')) {
			$rawQuery .= ' WHERE p.companycode = :companyCode';
			$params[':companyCode'] = $criteria['companyCode'];
			$whereAdded = true;
		}

		// Validate that we have a collectionCode criteria that is not empty.
		if ('' !== ($criteria['collectionCode'] ?? '')) {
			$rawQuery .= ' ' . ($whereAdded ? 'AND ' : 'WHERE ') . 'pcl.collectioncode = :collectionCode';
			$params[':collectionCode'] = $criteria['collectionCode'];
			$whereAdded = true;
		}

		// Validate that we have an active criteria
		if (null !== ($criteria['active'] ?? null)) {
			$rawQuery .= ' ' . ($whereAdded ? 'AND ' : 'WHERE ') . 'p.active = :active';
			$params[':active'] = (int) $criteria['active'];
			$whereAdded = true;
		}

		if (null !== ($criteria['createNewProjects'] ?? null)) {
			$rawQuery .= ' ' . ($whereAdded ? 'AND ' : 'WHERE ') . 'p.createnewprojects = :createNewProjects';
			$params[':createNewProjects'] = $criteria['createNewProjects'];
			$whereAdded = true;
		}

		if (null !== ($criteria['deleted'] ?? null)) {
			$rawQuery .= ' ' . ($whereAdded ? 'AND ' : 'WHERE ') . 'p.deleted = :deleted';
			$params[':deleted'] = (int) $criteria['deleted'];
			$whereAdded = true;
		}

		$rawQuery .= ' ORDER BY pcl.collectioncode, p.code';

		$query = $this->_em->createNativeQuery($rawQuery, $rsm);

		if (!empty($params)) {
			$query->setParameters($params);
		}
		return $query->getArrayResult();
	}

	public function getAllProductCodes(): array
	{
		return $this->createQueryBuilder('p')
				->select('p.code')
				->getQuery()
				->getArrayResult();
	}

	public function getExperienceUpgradeData(): array
	{
		$qb = $this->_em->createQueryBuilder();
		$qb = $qb
			->select('
						pcl.collectionCode
					,	p.code
					,	p.useDefaultImageScalingBefore
					,	p.imageScalingBeforeEnabled 
					,	p.imageScalingBefore 
					,	p.retroPrints
			')
			->from(ProductCollectionLink::class, 'pcl')
			->innerJoin($this->getEntityName(), 'p', Join::WITH, 'p.code = pcl.productCode')
			->where('p.useDefaultImageScalingBefore = 0')
			->orWhere('p.retroPrints = 1')
			->getQuery()
			->setHint(Query::HINT_FORCE_PARTIAL_LOAD, 1)
			->getResult();

		return $qb;
	}
}