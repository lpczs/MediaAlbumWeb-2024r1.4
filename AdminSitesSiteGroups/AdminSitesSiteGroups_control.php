<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminSitesSiteGroups/AdminSitesSiteGroups_model.php');
require_once('../AdminSitesSiteGroups/AdminSitesSiteGroups_view.php');

use Security\RequestValidationTrait;

class AdminSitesSiteGroups_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminSitesSiteGroups_view::displayGrid();
		}
	} 
	
	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesSiteGroups_model::getGridData();
			AdminSitesSiteGroups_view::getGridData($resultArray);
		}
	}
	
	static function siteGroupAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminSitesSiteGroups_view::displayAdd();
		}
	}    

	static function siteGroupAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesSiteGroups_model::siteGroupAdd();
            AdminSitesSiteGroups_view::siteGroupSave($resultArray);
		}
	}

	static function siteGroupEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$siteGroupID = $_GET['id'];
			if ($siteGroupID)
			{
			    $resultArray = AdminSitesSiteGroups_model::displayEdit($siteGroupID);
				AdminSitesSiteGroups_view::displayEdit($resultArray);
			}
		}	
	}    

	static function siteGroupEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesSiteGroups_model::siteGroupEdit();
			AdminSitesSiteGroups_view::siteGroupSave($resultArray);
		}
	}

	static function siteGroupsDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesSiteGroups_model::siteGroupsDelete();
			AdminSitesSiteGroups_view::siteGroupsDelete($resultArray);
		}
	}    

}

?>