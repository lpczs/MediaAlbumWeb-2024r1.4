<?php

use Security\RequestValidationTrait;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminCurrencies/AdminCurrencies_model.php');
require_once('../AdminCurrencies/AdminCurrencies_view.php');

class AdminCurrencies_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminCurrencies_view::displayGrid();
		}
	} 

	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminCurrencies_model::getGridData();
			AdminCurrencies_view::getGridData($resultArray);
		}
	}

    static function currencyAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminCurrencies_model::displayEdit(-1);
			AdminCurrencies_view::displayAdd($resultArray);
		}
	}

	static function currencyAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminCurrencies_model::currencyAdd();
			AdminCurrencies_view::currencySave($resultArray);	
		}
	}

	static function currencyEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $currencyID = $_GET['id'];

            if ($currencyID)
            {
                $resultArray = AdminCurrencies_model::displayEdit($currencyID);
                AdminCurrencies_view::displayEdit($resultArray);
            }
            else
            {
                self::initialize();
            }
        }
	}

	static function currencyEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminCurrencies_model::currencyEdit();
          	AdminCurrencies_view::currencySave($resultArray);
        }
	}

	static function currencyDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $deleteResultArray = AdminCurrencies_model::currencyDelete();
			AdminCurrencies_view::currencyDelete($deleteResultArray);
        }
	}
}

?>