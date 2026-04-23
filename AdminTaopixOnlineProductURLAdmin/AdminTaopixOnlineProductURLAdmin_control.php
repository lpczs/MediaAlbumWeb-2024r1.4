<?php

require_once('../AdminTaopixOnlineProductURLAdmin/AdminTaopixOnlineProductURLAdmin_model.php');
require_once('../AdminTaopixOnlineProductURLAdmin/AdminTaopixOnlineProductURLAdmin_view.php');
require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsRoute.php');

use Security\RequestValidationTrait;

class AdminTaopixOnlineProductURLAdmin_control
{
	use RequestValidationTrait;
	
	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminTaopixOnlineProductURLAdmin_view::displayEntry();
		}
	}
	
	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaopixOnlineProductURLAdmin_model::getGridData();
			AdminTaopixOnlineProductURLAdmin_view::getGridData($resultArray);
		}
	}
	
	static function productURLExport()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaopixOnlineProductURLAdmin_model::productURLExport();
			AdminTaopixOnlineProductURLAdmin_view::productURLExport($resultArray);
		}
	}
	
	static function productURLDecrypt()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaopixOnlineProductURLAdmin_model::productURLDecrypt();
			AdminTaopixOnlineProductURLAdmin_view::productURLDecrypt($resultArray);
		}
	}
}

?>