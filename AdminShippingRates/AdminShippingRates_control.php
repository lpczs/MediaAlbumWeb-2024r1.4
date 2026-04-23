<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminShippingRates/AdminShippingRates_model.php');
require_once('../AdminShippingRates/AdminShippingRates_view.php');

use Security\RequestValidationTrait;

class AdminShippingRates_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminShippingRates_view::displayGrid();
		}
	} 
	
	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingRates_model::getGridData();
			AdminShippingRates_view::getGridData($resultArray);
		}
	}
	
	static function shippingRateActivate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingRates_model::shippingRateActivate();
			AdminShippingRates_view::shippingRateActivate($resultArray);
		}
	}  

	static function shippingRateAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminShippingRates_model::addInitialize();
            AdminShippingRates_view::displayAdd($resultArray);
		}
	}    

	static function shippingRateAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingRates_model::shippingRateAdd();
			AdminShippingRates_view::shippingRateSave($resultArray);
		}
	}

	static function shippingRateEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminShippingRates_model::editInitialize();
            AdminShippingRates_view::displayEdit($resultArray);
		}	
	}    

	static function shippingRateEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingRates_model::shippingRateEdit();
			AdminShippingRates_view::shippingRateSave($resultArray);
			
			/*if ($resultArray['result'] == '')
            {
                self::initialize();
            }
            else
            {
                $shippingRateArray = AdminShippingRates_model::editInitialize();
                $resultArray['productslist'] = $shippingRateArray['productslist'];
                $resultArray['existinggroupcodes'] = $shippingRateArray['existinggroupcodes'];
                $resultArray['allgroupcodes'] = $shippingRateArray['allgroupcodes'];
                $resultArray['shippingmethodslist'] = $shippingRateArray['shippingmethodslist'];
                $resultArray['shippingzoneslist'] = $shippingRateArray['shippingzoneslist'];
                AdminShippingRates_view::displayEdit($resultArray, $resultArray['result'], $resultArray['resultparam']);
            }*/
		}
	}

	static function shippingRateDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingRates_model::shippingRateDelete();
			AdminShippingRates_view::shippingRateDelete($resultArray);
		}
	}  
	
	static function getShippingZonesFromCompany()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingRates_model::getShippingZonesFromCompanyCode();
			AdminShippingRates_view::getShippingZonesFromCompanyCode($resultArray);
		}
	}  
	
	static function getProductsFromCompany()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingRates_model::getProductsFromCompany();
			AdminShippingRates_view::getProductsFromCompany($resultArray);
		}
	} 
	
	static function getLicenseKeyFromCompany()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingRates_model::getLicenseKeyFromCompany();
			AdminShippingRates_view::getLicenseKeyFromCompany($resultArray);
		}
	} 

}

?>