<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminProducts/AdminProducts_model.php');
require_once('../AdminProducts/AdminProducts_view.php');

use Security\RequestValidationTrait;

class AdminProducts_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    AdminProducts_view::displayGrid();
		}
	}

	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminProducts_model::getGridData();
			AdminProducts_view::getGridData($resultArray);
		}
	}

	static function productActivate()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $resultArray = AdminProducts_model::productActivate();
            AdminProducts_view::productActivate($resultArray);
        }
	}

	static function productEditDisplay()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $productID = $_GET['id'];
            if ($productID)
            {
                $resultArray = AdminProducts_model::displayEdit($productID);
                AdminProducts_view::displayEdit($resultArray);
            }
        }
	}

	static function productConfigDisplay()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $productID = UtilsObj::getGETParam('id', 0);
			
            if ($productID)
            {
                $resultArray = AdminProducts_model::productConfigDisplay($productID);
                AdminProducts_view::productConfigDisplay($resultArray);
            }
        }
	}

	static function product3DPreviewDisplay()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $productID = $_GET['id'];
            if ($productID)
            {
                $resultArray = AdminProducts_model::productConfigDisplay($productID);
                AdminProducts_view::productConfigDisplay($resultArray);
            }
        }
	}

	// static function getProductTree()
	// {
    //     if (AuthenticateObj::adminSessionActive() == 1)
    //     {
	//         $resultArray = AdminProducts_model::getProductTree('','');
	//         AdminProducts_view::getProductTree($resultArray);
    //     }
	// }

	static function getComponentsFromCategory()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
	        $resultArray = AdminProducts_model::getComponentsFromCategory();
	        AdminProducts_view::getComponentsFromCategory($resultArray);
        }
	}

	static function productEdit()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $resultArray = AdminProducts_model::productEdit();
            AdminProducts_view::productSave($resultArray);
        }
	}

	static function productDelete()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $resultArray = AdminProducts_model::productDelete();
            AdminProducts_view::productDelete($resultArray);
        }
	}

	static function getProductsConfigPricingGridData()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $resultArray = AdminProducts_model::getProductsConfigPricingGridData();
            AdminProducts_view::getProductsConfigPricingGridData($resultArray);
        }
	}

	static function refreshProductTree()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$getLinkedTree = UtilsObj::getGETParam('getLinkedTree', 0);
			$resultArray = AdminProducts_model::refreshProductTree($getLinkedTree);
			AdminProducts_view::refreshProductTree($resultArray);
		}
	}

	static function getPreviewImage()
	{
        if (AuthenticateObj::WebSessionActive() == 1)
        {
			$resultArray = AdminProducts_model::getPreviewImage();
			UtilsObj::getPreviewImage($resultArray);
        }
	}

	static function uploadPreviewImage()
	{
        if (AuthenticateObj::WebSessionActive() == 1)
        {
			$resultArray = AdminProducts_model::uploadPreviewImage('products');
			AdminProducts_view::uploadPreviewImage($resultArray);
        }
	}

	static function saveProductConfig()
	{
		if (AuthenticateObj::WebSessionActive() == 1)
		{
				$linkedProductCode = UtilsObj::getPOSTParam('linkedproductcode', '');
				$resultArray = AdminProducts_model::saveProductConfig($linkedProductCode);
				AdminProducts_view::productConfigSave($resultArray);
		}
	}

	// static function deleteNodesFromProductConfig()
	// {
    //     if (AuthenticateObj::WebSessionActive() == 1)
    //     {
	// 		AdminProducts_model::deleteNodesFromProductConfig('');
    //     }
	// }

	static function productLinkingList()
	{
		if (AuthenticateObj::WebSessionActive() == 1)
		{
				$productCode = UtilsObj::getGETParam('productcode', '');
				$resultArray = AdminProducts_model::productLinkingList($productCode);
				AdminProducts_view::productLinkingList($resultArray);
		}
	}

	static function getLinkedProductCode()
	{
		if (AuthenticateObj::WebSessionActive() == 1)
		{
			$productCode = UtilsObj::getGETParam('productcode', '');
			$resultArray = AdminProducts_model::getLinkedProductCode($productCode);
			AdminProducts_view::getLinkedProductCode($resultArray);
		}
	}

	static function checkProductDeletionWarnings()
	{
		$productCodes = UtilsObj::getPOSTParam('productcodes', '');
		$resultArray = AdminProducts_model::checkProductDeletionWarnings($productCodes);
		AdminProducts_view::checkProductDeletionWarnings($resultArray, $productCodes);
	}

	static function getLinkingPreviewGridData()
	{
		$productCode = UtilsObj::getGETParam('productcode', '');
		$resultArray = AdminProducts_model::getLinkingPreviewGridData($productCode);
		AdminProducts_view::getLinkingPreviewGridData($resultArray);
	}

	static function linkingPreviewDisplay()
	{
		AdminProducts_view::linkingPreviewDisplay();
	}
}

?>