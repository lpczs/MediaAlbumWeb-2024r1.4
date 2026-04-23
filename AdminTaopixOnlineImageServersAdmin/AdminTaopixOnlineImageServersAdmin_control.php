<?php

require_once('../AdminTaopixOnlineImageServersAdmin/AdminTaopixOnlineImageServersAdmin_model.php');
require_once('../AdminTaopixOnlineImageServersAdmin/AdminTaopixOnlineImageServersAdmin_view.php');
require_once('../Utils/UtilsAuthenticate.php');

use Security\RequestValidationTrait;

class AdminTaopixOnlineImageServersAdmin_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminTaopixOnlineImageServersAdmin_view::displayGrid();
		}
	}

	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaopixOnlineImageServersAdmin_model::getGridData();
			AdminTaopixOnlineImageServersAdmin_view::getGridData($resultArray);
		}
	}

	static function imageServerAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminTaopixOnlineImageServersAdmin_view::displayAdd();
		}
	}

	static function addEditImageServer()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaopixOnlineImageServersAdmin_model::addEditImageServer();
        	AdminTaopixOnlineImageServersAdmin_view::imageServerSave($resultArray);
		}
	}

	static function imageServerEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$serverID = $_GET['serverid'];
			$resultArray = AdminTaopixOnlineImageServersAdmin_model::displayEdit($serverID);
			AdminTaopixOnlineImageServersAdmin_view::displayEdit($resultArray);
		}
	}

	static function activateImageServer()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaopixOnlineImageServersAdmin_model::activateImageServer();
        	AdminTaopixOnlineImageServersAdmin_view::updateImageServerGrid($resultArray);
		}
	}

	static function deleteImageServer()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaopixOnlineImageServersAdmin_model::deleteImageServer();
        	AdminTaopixOnlineImageServersAdmin_view::updateImageServerGrid($resultArray);
		}
	}
}

?>