<?php
require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminShippingZones/AdminShippingZones_model.php');
require_once('../AdminShippingZones/AdminShippingZones_view.php');

use Security\RequestValidationTrait;

class AdminShippingZones_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminShippingZones_view::displayGrid();
		}
	}
	
	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingZones_model::getGridData();
			AdminShippingZones_view::getGridData($resultArray);
		}
	} 
	
	static function shippingZoneAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminShippingZones_view::displayAdd();
		}
	}    

	static function shippingZoneAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingZones_model::shippingZoneAdd();
			AdminShippingZones_view::shippingZoneSave($resultArray);
			
		}
	}

	static function shippingZoneEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$shippingZoneID = $_GET['id'];
			
			if ($shippingZoneID)
			{
			    $resultArray = AdminShippingZones_model::displayEdit($shippingZoneID);
				AdminShippingZones_view::displayEdit($resultArray);
			}
		}	
	}    

	static function shippingZoneEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingZones_model::shippingZoneEdit();
			AdminShippingZones_view::shippingZoneSave($resultArray);
		}
	}

	static function shippingZoneDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminShippingZones_model::shippingZoneDelete();
			AdminShippingZones_view::shippingZoneDelete($resultArray);
		}
	}    

}

?>