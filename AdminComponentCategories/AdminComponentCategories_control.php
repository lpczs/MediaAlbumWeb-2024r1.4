<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminComponentCategories/AdminComponentCategories_model.php');
require_once('../AdminComponentCategories/AdminComponentCategories_view.php');

use Security\RequestValidationTrait;

class AdminComponentCategories_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminComponentCategories_view::displayGrid();
		}
	} 
	
	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponentCategories_model::getGridData();
			AdminComponentCategories_view::getGridData($resultArray);
		}
	}
	
	static function componentCategoriesAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminComponentCategories_view::displayAdd();
		}
	}    

	static function componentCategoriesAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponentCategories_model::componentCategoriesAdd();
           AdminComponentCategories_view::componentCategoriesSave($resultArray);
		}
	}

	static function componentCategoriesEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$catID = $_GET['id'];
			if ($catID)
			{
			    $resultArray = AdminComponentCategories_model::componentCategoriesdisplayEdit($catID);
				AdminComponentCategories_view::displayEdit($resultArray);
			}
		}	
	}    

	static function  componentCategoriesEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponentCategories_model::componentCategoriesEdit();
			AdminComponentCategories_view::componentCategoriesSave($resultArray);
		}
	}

	static function componentCategoriesDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminComponentCategories_model::componentCategoriesDelete();
			AdminComponentCategories_view::componentCategoriesDelete($resultArray);
		}
	}
	
	static function componentCategoriesActivate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminComponentCategories_model::componentCategoriesActivate();
            AdminComponentCategories_view::componentCategoriesActivate($resultArray);
        }
	}    

}

?>