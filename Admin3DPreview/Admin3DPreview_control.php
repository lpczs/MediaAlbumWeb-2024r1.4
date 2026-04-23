<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../Admin3DPreview/Admin3DPreview_model.php');
require_once('../Admin3DPreview/Admin3DPreview_view.php');

use Security\RequestValidationTrait;

class Admin3DPreview_control
{
	use Security\RequestValidationTrait;

	static function modelListGrid()
	{		
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = Admin3DPreview_model::modelListGrid();
			Admin3DPreview_view::modelListGrid($resultArray);
		}
	}

	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = Admin3DPreview_model::getGridData();
			Admin3DPreview_view::getGridData($resultArray);
		}
	}

	static function addDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$modelID = UtilsObj::getGETParam('modelid', 0);

			$resultArray = Admin3DPreview_model::addDisplay($modelID);
			Admin3DPreview_view::addDisplay($resultArray);
		}
	}

	static function addModel()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = Admin3DPreview_model::addModel();
			Admin3DPreview_view::addModelDisplay($resultArray);
		}
	}

	static function editModel()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = Admin3DPreview_model::editModel();
			Admin3DPreview_view::addModelDisplay($resultArray);
		}
	}

	static function deleteModel()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = Admin3DPreview_model::deleteModel();
			Admin3DPreview_view::deleteModelDisplay($resultArray);
		}
	}

	static function upload3DPreviewModel()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$action = UtilsObj::getGETParam('action');

			$modelData = array();
			$modelData['modelid'] = UtilsObj::getPOSTParam('modelid');
			$modelData['modelcode'] = UtilsObj::getPOSTParam('modelcode');
			$modelData['modelname'] = UtilsObj::getPOSTParam('modelname');
			$modelData['modelfilename'] = UtilsObj::getArrayParam($_FILES['modelfile'], 'name', '');
			$modelData['active'] = UtilsObj::getPOSTParam('active');

			$resultArray = Admin3DPreview_model::upload3DPreviewModel($modelData, 
																		$_FILES['modelfile'], 
																		$action);

			Admin3DPreview_view::display($resultArray);
		}
	}

	static function link3DPreviewModelToProducts()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$modelCode = UtilsObj::getPOSTParam('modelcode', array());
			$productCodes = UtilsObj::getPOSTParam('productcodes', array());

			$resultArray = Admin3DPreview_model::link3DPreviewModelToProducts($modelCode, $productCodes);
			Admin3DPreview_view::display($resultArray);
		}
	}

	static function unLink3DPreviewModelToProducts()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$productCodes = UtilsObj::getPOSTParam('productcodes', array());

			$resultArray = Admin3DPreview_model::unLink3DPreviewModelToProducts($productCodes);
			Admin3DPreview_view::display($resultArray);
		}
	}

	static function setModelActivateStatus()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$modelID = UtilsObj::getPOSTParam('ids', '');
			$active = UtilsObj::getPOSTParam('active', 0);

			$resultArray = Admin3DPreview_model::setModelActivateStatus($modelID, $active);
			Admin3DPreview_view::display($resultArray);
		}
	}

	static function get3DModelList()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$_POST['sort'] = 'resourcecode';
			$resultArray = Admin3DPreview_model::getGridData();
			Admin3DPreview_view::assignModelToProductsList($resultArray);
		}
	}
}
