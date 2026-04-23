<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminSitesSitesAdmin/AdminSitesSitesAdmin_model.php');
require_once('../AdminSitesSitesAdmin/AdminSitesSitesAdmin_view.php');

use Security\RequestValidationTrait;

class AdminSitesSitesAdmin_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminSitesSitesAdmin_view::displayGrid();
		}
	} 

	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesSitesAdmin_model::getGridData();
			AdminSitesSitesAdmin_view::getGridData($resultArray);
		}
	} 

	static function siteActivate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesSitesAdmin_model::siteActivate();
			AdminSitesSitesAdmin_view::siteActivate($resultArray);
		}
	} 

	static function siteDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesSitesAdmin_model::siteDelete();
			AdminSitesSitesAdmin_view::siteDelete($resultArray);
		}
	}    

	static function siteAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesSitesAdmin_model::displayAdd();
			AdminSitesSitesAdmin_view::displayEntry($resultArray);
		}
	}    

	static function siteEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesSitesAdmin_model::displayEdit();
			AdminSitesSitesAdmin_view::displayEntry($resultArray);
		}	
	}    

	static function siteAdd()
	{
		self::siteEdit();
	}

	static function siteEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesSitesAdmin_model::siteEdit();
			AdminSitesSitesAdmin_view::siteEdit($resultArray);
		}
	}

}

?>