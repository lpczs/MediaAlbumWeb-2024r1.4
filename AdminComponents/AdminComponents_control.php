<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminComponents/AdminComponents_model.php');
require_once('../AdminComponents/AdminComponents_view.php');

use Security\RequestValidationTrait;

class AdminComponents_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminComponents_view::displayGrid();
		}
	}

	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponents_model::getGridData();
			AdminComponents_view::getGridData($resultArray);
		}
	}

	static function componentTypesAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$categoryCode = UtilsObj::getGETParam('categorycode');
			AdminComponents_view::displayAdd($categoryCode);
		}
	}

	static function componentsAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponents_model::componentAdd();
            AdminComponents_view::componentSave($resultArray);
		}
	}

	static function componentsEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$componentID = $_GET['id'];

		    $resultArray = AdminComponents_model::editDisplay($componentID);
			AdminComponents_view::displayEdit($resultArray);

		}
	}

	static function componentsEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponents_model::componentEdit();
			AdminComponents_view::componentSave($resultArray);
		}
	}

	static function componentsDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponents_model::componentsDelete();
			AdminComponents_view::componentsDelete($resultArray);
		}
	}

	static function componentTypesActivate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminComponents_model::componentTypesActivate();
            AdminComponents_view::componentTypesActivate($resultArray);

        }
	}

	static function getPreviewImage()
	{
        if (AuthenticateObj::WebSessionActive() == 1)
        {
			$resultArray = AdminComponents_model::getPreviewImage();
			UtilsObj::getPreviewImage($resultArray);
        }
	}

	static function uploadPreviewImage()
	{
        if (AuthenticateObj::WebSessionActive() == 1)
        {
			$resultArray = AdminComponents_model::uploadPreviewImage('components');
			AdminComponents_view::uploadPreviewImage($resultArray);
        }
	}
}

?>