<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminPaymentMethods/AdminPaymentMethods_model.php');
require_once('../AdminPaymentMethods/AdminPaymentMethods_view.php');

use Security\RequestValidationTrait;

class AdminPaymentMethods_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminPaymentMethods_view::displayGrid();
		}
	}

	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminPaymentMethods_model::getGridData();
			AdminPaymentMethods_view::getGridData($resultArray);
		}
	}

	static function paymentMethodActivate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminPaymentMethods_model::paymentMethodActivate();
            AdminPaymentMethods_view::paymentMethodActivate($resultArray);
        }
	}

	static function paymentMethodEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $paymentMethodID = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            if ($paymentMethodID)
            {
                $resultArray = AdminPaymentMethods_model::displayEdit($paymentMethodID);
                AdminPaymentMethods_view::displayEdit($resultArray);
            }
        }
	}

	static function paymentMethodEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminPaymentMethods_model::paymentMethodEdit();
            AdminPaymentMethods_view::paymentMethodSave($resultArray);
        }
	}
}

?>