<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminShippingMethods/AdminShippingMethods_model.php');
require_once('../AdminShippingMethods/AdminShippingMethods_view.php');

use Security\RequestValidationTrait;

class AdminShippingMethods_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminShippingMethods_view::displayGrid();
		}
	} 
	
	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingMethods_model::getGridData();
			AdminShippingMethods_view::getGridData($resultArray);
		}
	}

	static function shippingMethodAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminShippingMethods_view::displayAdd();
		}
	}    

	static function shippingMethodAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingMethods_model::shippingMethodAdd();
			AdminShippingMethods_view::shippingMethodSave($resultArray);
			
		}
	}

	static function shippingMethodEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$shippingMethodID = $_GET['id'];
			if ($shippingMethodID)
			{
			    $resultArray = AdminShippingMethods_model::displayEdit($shippingMethodID);
				AdminShippingMethods_view::displayEdit($resultArray);
			}
		}	
	}    

	static function shippingMethodEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingMethods_model::shippingMethodEdit();
			AdminShippingMethods_view::shippingMethodSave($resultArray);
		}
	}

	static function shippingMethodDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingMethods_model::shippingMethodDelete();
			AdminShippingMethods_view::shippingMethodDelete($resultArray);
		}
	}
	
	static function getPreviewImage()
	{
        if (AuthenticateObj::WebSessionActive() == 1)
        {
			$resultArray = AdminShippingMethods_model::getPreviewImage();
			UtilsObj::getPreviewImage($resultArray);
        }
	}    

	static function uploadLogo()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingMethods_model::uploadLogo('shippingmethods');
			AdminShippingMethods_view::uploadLogo($resultArray);
		}
	}    

}

?>