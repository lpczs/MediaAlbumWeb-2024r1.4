<?php
require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminMetadataKeywordsGroups/AdminMetadataKeywordsGroups_model.php');
require_once('../AdminMetadataKeywordsGroups/AdminMetadataKeywordsGroups_view.php');

use Security\RequestValidationTrait;

class AdminMetadataKeywordsGroups_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminMetadataKeywordsGroups_view::initialize();
		}
	}

	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminMetadataKeywordsGroups_model::getGridData();
		}
	}

	static function addDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminMetadataKeywordsGroups_model::addDisplay();
			AdminMetadataKeywordsGroups_view::displayEntry($resultArray);
		}
	}

	static function editDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminMetadataKeywordsGroups_model::editDisplay();
			AdminMetadataKeywordsGroups_view::displayEntry($resultArray);
		}
	}

	static function keywordGroupAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminMetadataKeywordsGroups_model::keywordGroupAdd();
		}
	}

	static function keywordGroupEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminMetadataKeywordsGroups_model::keywordGroupEdit();
		}
	}

	static function getProductList()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$groupcode = UtilsObj::getPOSTParam('groupcode', '');
			$orderBy = UtilsObj::getPOSTParam('sort', '');
			$orderByDir = UtilsObj::getPOSTParam('dir', 'ASC');
			AdminMetadataKeywordsGroups_model::getProductListByGroupCode($groupcode, $orderBy, $orderByDir);
		}
	}

	static function getAssociatedComponentList()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$headerGroupID = UtilsObj::getPOSTParam('headergroupid', '');
			AdminMetadataKeywordsGroups_model::getAssociatedComponentList($headerGroupID);
		}
	}

}

?>