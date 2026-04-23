<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminProductGroups/AdminProductGroups_model.php');
require_once('../AdminProductGroups/AdminProductGroups_view.php');

class AdminProductGroups_control
{
    use Security\RequestValidationTrait;

    static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminProductGroups_view::displayGrid();
		}
	}

    static function getGridData()
    {
        if (AuthenticateObj::adminSessionActive() == 1)
		{
            $start = UtilsObj::getPOSTParam('start', 0);
            $limit = UtilsObj::getPOSTParam('limit', 0);
            $query = UtilsObj::getPOSTParam('query', '');

			$resultArray = AdminProductGroups_model::getGridData($start, $limit, $query);
			AdminProductGroups_view::getGridData($resultArray);
		}
    }

    static function addDisplay()
    {
        if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray['id'] = 0;
			AdminProductGroups_view::displayAdd($resultArray);
		}
    }

    static function productGroupAdd()
    {
        if (AuthenticateObj::adminSessionActive() == 1)
		{
            $groupID = 0;
            $groupName = UtilsObj::getPOSTParam('groupname', '');
            $layoutRules = UtilsObj::getPOSTParam('layoutrules', '');
            $collectionRules = UtilsObj::getPOSTParam('collectionrules', '');

            $resultArray = AdminProductGroups_model::saveProductGroup($groupID, $groupName, $layoutRules, $collectionRules);
			AdminProductGroups_view::displaysave($resultArray);
		}
    }

    static function productGroupEdit()
    {
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $groupID = UtilsObj::getGETParam('id', 0);
            $groupName = UtilsObj::getPOSTParam('groupname', '');
            $layoutRules = UtilsObj::getPOSTParam('layoutrules', '');
            $collectionRules = UtilsObj::getPOSTParam('collectionrules', '');

            $resultArray = AdminProductGroups_model::saveProductGroup($groupID, $groupName, $layoutRules, $collectionRules);
			AdminProductGroups_view::displaysave($resultArray);
        }
    }

    static function duplicate()
    {
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            AdminProductGroups_view::duplicate();
        }
    }

    static function deleteProductGroup()
    {
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $groupID = UtilsObj::getPOSTParam('id', 0);

            if ($groupID !== 0)
            {
                $resultArray = AdminProductGroups_model::deleteProductGroup($groupID);
            }
            else
            {
                // the client has somehow managed to send no group up, return as if it succeeded to allow the client to continue
                $resultArray = UtilsObj::getReturnArray();
            }

            AdminProductGroups_view::deleteProductGroup($resultArray);
        }
    }

    static function checkDelete()
    {
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $groupID = UtilsObj::getPOSTParam('id', '');
            $resultArray = AdminProductGroups_model::checkDelete($groupID);
            AdminProductGroups_view::checkDelete($resultArray);
        }
    }

    static function getCollectionGridData()
    {
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $groupID = UtilsObj::getGETParam('groupid', 0);
            $resultArray = AdminProductGroups_model::getCollectionGridData($groupID);
            AdminProductGroups_view::getCollectionGridData($resultArray);
        }
    }

    static function getPreviewGridData()
    {
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $resultArray = AdminProductGroups_model::getpreviewGridData(UtilsObj::getGETParam('groupid', 0));
            AdminProductGroups_view::getPreviewGridData($resultArray);
        }
    }

    static function previewDisplay()
    {
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            AdminProductGroups_view::previewDisplay();
        }
    }

    static function editDisplay()
    {
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            AdminProductGroups_view::editDisplay();
        }
    }

    static function getLayoutPreviewData()
    {
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $layoutCode = UtilsObj::getPOSTParam('layoutcode', '');
            $resultArray = AdminProductGroups_model::getLayoutPreviewData($layoutCode);
            AdminProductGroups_view::getLayoutPreviewData($layoutCode, $resultArray);
        }
    }

    static function getLayoutGridData()
    {
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $groupID = UtilsObj::getGETParam('groupid', 0);
            $resultArray = AdminProductGroups_model::getLayoutGridData($groupID);
            AdminProductGroups_view::getLayoutGridData($resultArray);
        }
    }

    static function getMultipleLayoutPreviewData()
    {
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            $layoutCodes = UtilsObj::getPOSTParam('layoutcodes', '');
            $collectionFilter = UtilsObj::getPOSTParam('collectioncode', '');
            $layoutCodesArray = explode(",", $layoutCodes);
            $resultArray = AdminProductGroups_model::getMultipleLayoutPreviewData($layoutCodesArray, $collectionFilter);
            AdminProductGroups_view::getMultipleLayoutPreviewData($resultArray);
        }
    }
}
?>