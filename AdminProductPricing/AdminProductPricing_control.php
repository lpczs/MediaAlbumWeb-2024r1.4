<?php

use Security\RequestValidationTrait;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminProductPricing/AdminProductPricing_model.php');
require_once('../AdminProductPricing/AdminProductPricing_view.php');

class AdminProductPricing_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminProductPricing_model::displayGrid();
			AdminProductPricing_view::displayGrid($resultArray);
		}
	}

	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminProductPricing_model::getGridData();
			AdminProductPricing_view::getGridData($resultArray);
		}
	}

	static function getPriceListGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminProductPricing_model::getPriceListGridData();
			AdminProductPricing_view::getPriceListGridData($resultArray);
		}
	}

	static function pricingActivate()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $resultArray = AdminProductPricing_model::pricingActivate();
            AdminProductPricing_view::pricingActivate($resultArray);
        }
	}

	static function activatePriceList()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminProductPricing_model::activatePriceList();
            AdminProductPricing_view::activatePriceList($resultArray);
        }
	}

	static function pricingAddDisplay()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $resultArray = AdminProductPricing_model::addInitialize();
            AdminProductPricing_view::displayAdd($resultArray);
        }
	}

	static function priceListAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminProductPricing_view::displayAddPriceList();
		}
	}

	static function priceListsInitialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminProductPricing_view::displayPriceListGrid();
		}
	}

	static function pricingAdd()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $resultArray = AdminProductPricing_model::pricingAdd();
            AdminProductPricing_view::ProductPricingSave($resultArray);
        }
	}

	static function addPriceList()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminProductPricing_model::productPriceListAdd();
            AdminProductPricing_view::productPriceListSave($resultArray);
		}
	}

	static function pricingEditDisplay()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
           $resultArray = AdminProductPricing_model::editInitialize();
           AdminProductPricing_view::displayEdit($resultArray);
        }
	}

	static function priceListEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{			
		    $resultArray = AdminProductPricing_model::priceListEditDisplay();
			AdminProductPricing_view::displayPriceListEdit($resultArray);
		}
	}

	static function pricingEdit()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $resultArray = AdminProductPricing_model::pricingEdit();
            AdminProductPricing_view::ProductPricingSave($resultArray);
        }
	}

	static function  priceListEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminProductPricing_model::productPriceListEdit();
			AdminProductPricing_view::productPriceListSave($resultArray);
		}
	}

	static function pricingDelete()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $resultArray = AdminProductPricing_model::pricingDelete();
            AdminProductPricing_view::productPricingDelete($resultArray);
        }
	}

	static function  productPriceListDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminProductPricing_model::productPriceListDelete();
			AdminProductPricing_view::productPriceListDelete($resultArray);
		}
	}

	static function getLicenseKeyFromCompany()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminProductPricing_model::getLicenseKeyFromCompany();
			AdminProductPricing_view::getLicenseKeyFromCompany($resultArray);
		}
	}

}

?>