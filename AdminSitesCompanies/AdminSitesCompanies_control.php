<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminSitesCompanies/AdminSitesCompanies_model.php');
require_once('../AdminSitesCompanies/AdminSitesCompanies_view.php');

use Security\RequestValidationTrait;

class AdminSitesCompanies_control
{
	use RequestValidationTrait;
	
	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminSitesCompanies_view::displayGrid();
		}
	} 
	
	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesCompanies_model::getGridData();
			AdminSitesCompanies_view::getGridData($resultArray);
		}
	}
	
	static function companyEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$companyID = $_GET['id'];
			if ($companyID)
			{
			    $resultArray = AdminSitesCompanies_model::displayEdit($companyID);
				AdminSitesCompanies_view::displayEdit($resultArray);
			}
		}	
	}    

	static function companyEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesCompanies_model::companyEdit();
			AdminSitesCompanies_view::companyEdit($resultArray);
		}
	}

}

?>