<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminComponentsPricing/AdminComponentsPricing_model.php');
require_once('../AdminComponentsPricing/AdminComponentsPricing_view.php');

use Security\RequestValidationTrait;

class AdminComponentsPricing_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponentsPricing_model::initialize();
			AdminComponentsPricing_view::displayGrid($resultArray);
		}
	} 
	
	static function priceListsInitialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminComponentsPricing_view::displayPriceListGrid();
		}
	} 
	
	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponentsPricing_model::getGridData();
			AdminComponentsPricing_view::getGridData($resultArray);
		}
	}
	
	static function getPriceListGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponentsPricing_model::getPriceListGridData();
			AdminComponentsPricing_view::getPriceListGridData($resultArray);
		}
	}
	
	static function AddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponentsPricing_model::addInitialize();
			AdminComponentsPricing_view::displayAdd($resultArray);
		}
	}
	
	static function priceListAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminComponentsPricing_view::displayAddPriceList();
		}
	}      

	static function componentsPricingAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponentsPricing_model::componentPricingAdd();
            AdminComponentsPricing_view::componentPriceSave($resultArray);
		}
	}
	
	static function addPriceList()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponentsPricing_model::componentPriceListAdd();
            AdminComponentsPricing_view::componentPriceListSave($resultArray);
		}
	}
	
	static function editDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{			
		    $resultArray = AdminComponentsPricing_model::editInitialize();
			AdminComponentsPricing_view::displayEdit($resultArray);
			
		}	
	}   
	
	static function priceListEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{			
		    $resultArray = AdminComponentsPricing_model::priceListEditDisplay();
			AdminComponentsPricing_view::displayPriceListEdit($resultArray);
			
		}	
	}  

	static function componentsPricingEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponentsPricing_model::componentPricingEdit();
			AdminComponentsPricing_view::componentPriceSave($resultArray);
		}
	}
	
	static function  priceListEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponentsPricing_model::componentPriceListEdit();
			AdminComponentsPricing_view::componentPriceListSave($resultArray);
		}
	}
	
	static function  priceListDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponentsPricing_model::priceListDelete();
			AdminComponentsPricing_view::priceListDelete($resultArray);
		}
	}
	
	static function  defaultPriceDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponentsPricing_model::defaultPriceDelete();
			AdminComponentsPricing_view::defaultPriceDelete($resultArray);
		}
	}
	
	static function componentPricingActivate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminComponentsPricing_model::componentPricingActivate();
            AdminComponentsPricing_view::componentPricingActivate($resultArray);
        }
	} 
	
	static function activatePriceList()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminComponentsPricing_model::activatePriceList();
            AdminComponentsPricing_view::activatePriceList($resultArray);
        }
	} 
	
	static function getLicenseKeyFromCompany()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponentsPricing_model::getLicenseKeyFromCompany();
			AdminComponentsPricing_view::getLicenseKeyFromCompany($resultArray);
		}
	}  
	
}

?>