<?php

namespace Taopix\ControlCentre\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Taopix\ControlCentre\Entity\Brand;

class LicenseKeyRepository extends EntityRepository
{
	public function getKeyAndCache(string $groupCode): array
	{
		$brandRepository = $this->_em->getRepository(Brand::class);
		$query = 'SELECT lk.cacheversion, b.datelastmodified FROM `' . $this->getClassMetadata()->getSchemaName() . '`.`' . $this->getClassMetadata()->getTableName() . '` as lk ' .
			'LEFT JOIN `' . ' . $brandRepository->getClassMetadata()->getSchemaName() . ' . '`.`' . $brandRepository->getClassMetadata()->getTableName() . '` as b ON b.code=lk.webBrandCode ' .
			'WHERE lk.groupcode = :groupCode AND lk.active = :active';
		$rsm = (new ResultSetMapping())
			->addScalarResult('cacheversion', 'cacheVersion')
			->addScalarResult('datelastmodified', 'dateLastModified');

		return $this->_em->createNativeQuery($query, $rsm)
			->setParameter(":groupCode", $groupCode)
			->setParameter(":active", 1)
			->getArrayResult();
	}

	public function getExperienceUpgradeData()
	{
		$qb = $this->_em->createQueryBuilder();
		$qb = $qb
			->select('
						l.groupCode
						,   l.webBrandCode
						,	b.onlineDesignerSigninRegisterPromptDelay
						,	l.onlineDesignerGuestWorkflowMode

						,	CASE WHEN (l.useDefaultAveragePicturesPerPage = 1) THEN
								b.averagePicturesPerPage
							ELSE
								l.averagePicturesPerPage
							END as averagePicturesPerPage
						
						,	CASE WHEN (l.useDefaultImageScalingAfter = 1) THEN
								b.imageScalingAfterEnabled 
							ELSE
								l.imageScalingAfterEnabled 
							END as imageScalingAfterEnabled
						
						,	CASE WHEN (l.useDefaultImageScalingAfter = 1) THEN
								b.imageScalingAfter
							ELSE
								l.imageScalingAfter
							END as imageScalingAfter
						
						,	CASE WHEN (l.useDefaultImageScalingBefore = 1) THEN
								b.imageScalingBeforeEnabled 
							ELSE
								l.imageScalingBeforeEnabled 
							END as imageScalingBeforeEnabled
						
						,	CASE WHEN (l.useDefaultImageScalingBefore = 1) THEN
								b.imageScalingBefore 
							ELSE
								l.imageScalingBefore 
							END as imageScalingBefore
						
						,	CASE WHEN (l.useDefaultAutomaticallyApplyPerfectlyClear = 1) THEN
								b.automaticallyApplyPerfectlyClear 
							ELSE
								l.automaticallyApplyPerfectlyClear 
							END as automaticallyApplyPerfectlyClear
						
						,	CASE WHEN (l.useDefaultAutomaticallyApplyPerfectlyClear = 1) THEN
								b.allowUsersToTogglePerfectlyClear 
							ELSE
								l.allowUsersToTogglePerfectlyClear 
							END as allowUsersToTogglePerfectlyClear
						
						,	CASE WHEN (l.useDefaultInsertDeleteButtonsVisibility = 1) THEN
								b.insertDeleteButtonsVisibility
							ELSE
								l.insertDeleteButtonsVisibility
							END as insertDeleteButtonsVisibility
							
						,	CASE WHEN (l.useDefaultOnlineEditorMode = 1) THEN
								b.onlineEditorMode
							ELSE
								l.onlineEditorMode
							END as onlineEditorMode
							
						,	CASE WHEN (l.useDefaultOnlineEditorMode = 1) THEN
								b.enableSwitchingEditor 
							ELSE
								l.enableSwitchingEditor 
							END as enableSwitchingEditor
							
						, 	l.onlineDesignerGuestWorkflowMode
						
						, 	CASE WHEN (l.useDefaultOnlineDesignerLogoLinkUrl = 1) THEN
								b.onlineDesignerLogoLinkUrl
							ELSE
								l.onlineDesignerLogoLinkUrl
							END as onlineDesignerLogoLinkUrl

						,	CASE WHEN (l.useDefaultOnlineDesignerLogoLinkUrl = 1) THEN
								b.onlineDesignerLogoLinkTooltip
							ELSE
								l.onlineDesignerLogoLinkTooltip
							END as onlineDesignerLogoLinkTooltip
						
						,	CASE WHEN (l.useDefaultSizeAndPositionSettings = 1) THEN
								b.sizeAndPositionMeasurementUnits 
							ELSE
								l.sizeAndPositionMeasurementUnits 
							END as sizeAndPositionMeasurementUnits
								
			')
			->from($this->getEntityName(), 'l')
			->innerJoin(Brand::class, 'b', Join::WITH, 'b.code = l.webBrandCode')
			->getQuery()
			->setHint(Query::HINT_FORCE_PARTIAL_LOAD, 1)
			->getResult();

		return $qb;
	}
}
