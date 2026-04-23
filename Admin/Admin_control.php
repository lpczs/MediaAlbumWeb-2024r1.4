<?php

use Security\RequestValidationTrait;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../Admin/Admin_model.php');
require_once('../Admin/Admin_view.php');
require_once('../Welcome/Welcome_control.php');

class Admin_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			Admin_view::initialize();
		}
	}

	static function logout()
	{
		Admin_model::logout();
	}

	static function saveAsPriceList()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			Admin_view::saveAsPriceList();
		}
	}

	static function priceListAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = Admin_model::priceListAdd();
			Admin_view::priceListAdd($resultArray);
		}
	}

	static function priceListEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = Admin_model::priceListEditDisplay();
			Admin_view::displayPriceListEdit($resultArray);
		}
	}

	static function  priceListEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = Admin_model::priceListEdit();
			Admin_view::priceListSave($resultArray);
		}
	}

	static function priceListActivate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = Admin_model::activatePriceList();
			Admin_view::activatePriceList($resultArray);
		}
	}

	static function  adminPriceListDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = Admin_model::adminPriceListDelete();
			Admin_view::adminPriceListDelete($resultArray);
		}
	}

	static function searchCustomers()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = Admin_model::ExtJsSearchCustomers();
			Admin_view::ExtJsSearchCustomers($resultArray);
		}
	}

	/**
	 * Reauthenticates the current signed in user.
	 */
	static function reauthenticate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$reason = UtilsObj::getPOSTParam('reason', '__unknown__');
			$sessionRef = UtilsObj::getGETParam('ref', -1);
			$password = UtilsObj::getPOSTParam('reauth_password', '');
			$format = UtilsObj::getPOSTParam('format', TPX_PASSWORDFORMAT_MD5);
			$resultArray = Admin_model::reauthenticate($reason, $sessionRef, $password, $format);
			Admin_view::outputJSON($resultArray);
		}
	}

	/**
	 * Unlocks a customer or user account.
	 */
	static function unlockAccount()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$recordID = UtilsObj::getPOSTParam('id', 0);
			$resultArray = Admin_model::unlockAccount($recordID);
			Admin_view::outputJSON($resultArray);
		}
	}
}

?>