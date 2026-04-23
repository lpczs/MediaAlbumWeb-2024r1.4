<?php

use Security\RequestValidationTrait;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminUsers/AdminUsers_model.php');
require_once('../AdminUsers/AdminUsers_view.php');

class AdminUsers_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminUsers_view::displayGrid();
		}
	}

	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminUsers_model::getGridData();
			AdminUsers_view::getGridData($resultArray);
		}
	} 

	static function userActivate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminUsers_model::userActivate();
			AdminUsers_view::userActivate($resultArray);
        }
	}
	
	static function userAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminUsers_view::displayAdd();
        }
	}

	static function userAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminUsers_model::userAdd();
            AdminUsers_view::userSave($resultArray);
        }
	}

	static function userEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $userID = $_GET['id'];
            AdminUsers_view::displayEdit($userID);
        }
	}

	static function userEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminUsers_model::userEdit();
            AdminUsers_view::userSave($resultArray);
        }
	}

	static function userDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminUsers_model::userDelete();
			AdminUsers_view::userDelete($resultArray);
        }
	}

}

?>