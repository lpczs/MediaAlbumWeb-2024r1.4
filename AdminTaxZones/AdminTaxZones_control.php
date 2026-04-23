<?php

use Security\RequestValidationTrait;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminTaxZones/AdminTaxZones_model.php');
require_once('../AdminTaxZones/AdminTaxZones_view.php');

class AdminTaxZones_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminTaxZones_view::displayGrid();
		}
	}

	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaxZones_model::getGridData();
			AdminTaxZones_view::getGridData($resultArray);
		}
	}

	static function taxZoneAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminTaxZones_view::displayAdd();
		}
	}

	static function taxZoneAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaxZones_model::taxZoneAdd();
			AdminTaxZones_view::taxZoneSave($resultArray);
		}
	}

	static function taxZoneEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$taxZoneID = $_GET['id'];
			if ($taxZoneID)
			{
                $resultArray = AdminTaxZones_model::displayEdit($taxZoneID);
				AdminTaxZones_view::displayEdit($resultArray);
			}
		}
	}

	static function taxZoneEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaxZones_model::taxZoneEdit();
			AdminTaxZones_view::taxZoneSave($resultArray);
		}
	}

	static function taxZoneDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaxZones_model::taxZoneDelete();
			AdminTaxZones_view::taxZoneDelete($resultArray);
		}
	}

}

?>