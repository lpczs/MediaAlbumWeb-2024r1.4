<?php

use Security\RequestValidationTrait;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminTaxRates/AdminTaxRates_model.php');
require_once('../AdminTaxRates/AdminTaxRates_view.php');

class AdminTaxRates_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminTaxRates_view::displayGrid();
		}
	}

	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaxRates_model::getGridData();
			AdminTaxRates_view::getGridData($resultArray);
		}
	}

	static function taxratesAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminTaxRates_view::displayAdd();
		}
	}

	static function taxRatesAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaxRates_model::taxRatesAdd();
            AdminTaxRates_view::taxRateSave($resultArray);
		}
	}

	static function taxRatesEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$taxID = $_GET['id'];
			if ($taxID)
			{
			    $resultArray = AdminTaxRates_model::displayEdit($taxID);
				AdminTaxRates_view::displayEdit($resultArray);
			}
		}
	}

	static function taxRatesEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaxRates_model::taxRatesEdit();
			AdminTaxRates_view::taxRateSave($resultArray);
		}
	}

	static function taxRatesDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaxRates_model::taxRatesDelete();
			AdminTaxRates_view::taxRateDelete($resultArray);
		}
	}

}

?>