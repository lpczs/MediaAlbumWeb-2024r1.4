<?php

namespace Taopix\ControlCentre\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Taopix\ControlCentre\Entity\Product;

class PriceLinkRepository extends EntityRepository
{
	public function getComponentPaths(array $criteria): array
	{
		$expBuilder = $this->_em->getExpressionBuilder();
		return $this->createQueryBuilder('pl')
				->select("CONCAT(pl.parentPath, pl.productCode) as componentProductPath, count(pl.id)")
				->where(
					$expBuilder->in('pl.productCode',
						$this->_em->createQueryBuilder()
							->select('p.code')
							->where('p.code IN (:productCodes)')
							->from(Product::class, 'p')
							->getDQL()
					)
				)
				->andWhere('pl.parentPath IN (:paths)')
				->andWhere('pl.groupCode IN (:groupCodes)')
				->groupBy('componentProductPath')
				->setParameters([
					'paths' => [
						'$COVER\\',
						'$PAPER\\',
						'$SINGLEPRINT\\',
						'$CALENDARCUSTOMISATION\\',
						'$TAOPIXAI\\',
					],
					'groupCodes' => [
						"",
						$criteria['groupCode']
					],
					'productCodes' => $criteria['productCodes']
				])
				->getQuery()
				->getArrayResult();
	}

	public function getComponentLinkAndPriceInfo(array $criteria): array
	{
		return $this->createQueryBuilder('pl')
				->select('pl.linkedProductCode, pl.productCode, pl.componentCode, pl.priceId, pl.groupCode, pl.parentPath, pl.priceDescription, pl.inheritParentQty, pl.sortOrder, pl.default')
				->where('pl.productCode in (:productCodes)')
				->andWhere('pl.groupCode in (:groupCodes)')
                ->andWhere('pl.active = :active')
				->orderBy('pl.productCode')
				->addOrderBy('pl.groupCode')
				->addOrderBy('pl.componentCode')
				->setParameter('productCodes', $criteria['productCodes'])
				->setParameter('groupCodes', $criteria['groupCodes'])
                ->setParameter('active', true)
				->getQuery()
				->getArrayResult();
	}

    public function getLinkedProductCode(string $productCode): string
    {
        $result = $this->createQueryBuilder('pl')
            ->select('pl.linkedProductCode')
            ->where('pl.productCode = :productCode')
            ->andWhere('pl.linkedProductCode != :empty OR pl.componentCode != :empty')
            ->orderBy('pl.linkedProductCode', 'DESC')
            ->setParameter('productCode', $productCode)
            ->setParameter('empty', '')
            ->setMaxResults(1)
            ->getQuery()
            ->getArrayResult();

        return empty($result) ? '' : $result[0]['linkedProductCode'];
    }

    public function getComponentTree(array $criteria): array
    {
        $query = 'SELECT `pl`.`id`, `pl`.`parentid`, `pl`.`companycode`, `pl`.`productcode`, `pl`.`groupcode`, `pl`.`parentpath`,
            `pl`.`sectioncode`, `pl`.`sortorder`, `pl`.`isdefault`, `pl`.`priceid`, `pl`.`inheritparentqty`, `cmp`.`companycode`, `cmp`.`code`, `cmp`.`localcode`,
            `cmp`.`name`, `cmp`.`info`, `cmp`.`categorycode`, `cc`.`pricingmodel`, `pr`.`price`, `cc`.`islist`, `cc`.`componentpricingdecimalplaces`, `cc`.`active`, `cmp`.`active` as `componentactive`,
            `cmp`.`keywordgroupheaderid`, `cmp`.`minimumpagecount`, `cmp`.`maximumpagecount`, `cmp`.`moreinfolinkurl`, `cmp`.`moreinfolinktext`, `cc`.`requirespagecount`,
            ifnull(`tr`.`code`, "") as taxcode, ifnull(`tr`.`rate`,0) as taxrate, `pr`.`quantityisdropdown`, `cc`.`name` as `categoryname`, `cc`.`prompt` as `categoryprompt`,
            `pcc`.`name` as `parentsectionname`, `pcc`.`prompt` as `parentsectionprompt`, `cc`.`displaystage`, `pcc`.`displaystage` as `parentsectiondisplaystage`,
            (`pl`.`priceid` < 0) AS isdefaultprice
            FROM `PRICELINK` as pl
            INNER JOIN `PRICELINK` `p2` ON `p2`.`componentcode` = `pl`.`componentcode` AND ((`pl`.`priceid` = -1 AND `p2`.`productcode` = "") OR (`pl`.`priceid` > 0 AND `pl`.`id` = `p2`.`id`))
                AND (`p2`.`companycode` = :companyCode OR `p2`.`companycode` = "") AND (`p2`.`groupcode` = :groupCode OR `p2`.`groupcode` = "") AND (`p2`.`active` = 1)
            LEFT JOIN `COMPONENTS` cmp ON `cmp`.`code` = `pl`.`componentcode`
            LEFT JOIN `PRICES` pr ON `pr`.`id` = `p2`.`priceid`
            LEFT JOIN `TAXRATES` tr ON `tr`.`code` = `pr`.`taxcode`
            LEFT JOIN `COMPONENTCATEGORIES` cc ON `cc`.`code` = `cmp`.`categorycode`
            LEFT JOIN `COMPONENTCATEGORIES` pcc ON `pl`.`sectioncode` = `pcc`.`code`
            WHERE (( `pl`.`productcode` IN (:productCodes) ) ) AND (`pl`.`componentcode` <> "") AND ((`pl`.`companycode` = :companyCode) OR (`pl`.`companycode` = ""))
                AND ((`pl`.`groupcode` = :groupCode) OR (`pl`.`groupcode` = "")) AND (`cmp`.`active` = 1) AND (`pl`.`active` = 1) AND (`cc`.`active` = 1) AND (`pr`.`active` = 1)
                AND (`pl`.`parentpath` NOT LIKE "$ORDERFOOTER%") ORDER BY `productcode`, `sortorder`, `parentpath`';
        $resultSetMap = (new ResultSetMapping())
            ->addScalarResult('id', 'id', 'integer')
            ->addScalarResult('parentid', 'parentid', 'integer')
            ->addScalarResult('companycode', 'companycode', 'string')
            ->addScalarResult('productcode', 'productcode', 'string')
            ->addScalarResult('groupcode', 'groupcode', 'string')
            ->addScalarResult('parentpath', 'parentpath', 'string')
            ->addScalarResult('sectioncode', 'sectioncode', 'string')
            ->addScalarResult('sortorder', 'sortorder', 'integer')
            ->addScalarResult('isdefault', 'isdefault', 'boolean')
            ->addScalarResult('priceid', 'priceid', 'integer')
            ->addScalarResult('inheritparentqty', 'inheritparentqty', 'boolean')
            ->addScalarResult('companycode', 'componentcompanycode', 'string')
            ->addScalarResult('code', 'code', 'string')
            ->addScalarResult('localcode', 'localcode', 'string')
            ->addScalarResult('name', 'name', 'string')
            ->addScalarResult('info', 'info', 'string')
            ->addScalarResult('categorycode', 'categorycode', 'string')
            ->addScalarResult('pricingmodel', 'pricingmodel', 'string')
            ->addScalarResult('price', 'price', 'string')
            ->addScalarResult('islist', 'islist', 'boolean')
            ->addScalarResult('componentdecimalplaces', 'decimalplaces', 'integer')
            ->addScalarResult('active', 'categoryactive', 'boolean')
            ->addScalarResult('componentactive', 'componentactive', 'boolean')
            ->addScalarResult('keywordgroupheaderid', 'keywordgroupheaderid', 'integer')
            ->addScalarResult('minimumpagecount', 'minimumpagecount', 'string')
            ->addScalarResult('maximumpagecount', 'maximumpagecount', 'string')
            ->addScalarResult('moreinfolinkurl', 'moreinfolinkurl', 'string')
            ->addScalarResult('moreinfolinktext', 'moreinfolinktext', 'string')
            ->addScalarResult('requirespagecount', 'requirespagecount', 'boolean')
            ->addScalarResult('taxcode', 'taxcode', 'string')
            ->addScalarResult('taxrate', 'taxrate', 'string')
            ->addScalarResult('quantityisdropdown', 'quantityisdropdown', 'boolean')
            ->addScalarResult('categoryname', 'categoryname', 'string')
            ->addScalarResult('categoryprompt', 'categoryprompt', 'string')
            ->addScalarResult('parentsectionname', 'parentsectionname', 'string')
            ->addScalarResult('parentsectionprompt', 'parentsectionprompt', 'string')
            ->addScalarResult('displaystage', 'displaystage', 'string')
            ->addScalarResult('parentsectiondisplaystage', 'parentsectiondisplaystage', 'string')
            ->addScalarResult('isdefaultprice', 'isdefaultprice', 'boolean');
        return $this->getEntityManager()->createNativeQuery($query, $resultSetMap)
            ->setParameters($criteria)
            ->getResult();
    }
}
