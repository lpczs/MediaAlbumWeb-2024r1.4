<?php
require_once('../Utils/UtilsDatabase.php');

class AdminProductGroups_model
{
    static function getGridData($pStart, $pLimit, $pQuery)
    {
        return DatabaseObj::getProductGroupList($pStart, $pLimit, $pQuery);
    }

    static function duplicate($pGroupID)
    {
        $error = '';

        $originalGroupArray = DatabaseObj::getProductGroup($pGroupID);
        $originalGroupArray['data']['name'] = $originalGroupArray['data']['name'] . " (Copy)"; 
        $error = $originalGroupArray['error'];

        if ($error === '')
        {
            $insertResultArray = DatabaseObj::insertProductGroupAndConfiguration($originalGroupArray['data']);

            $error = $insertResultArray['error'];
        }

        return $error;
    }

    static function deleteProductGroup($pGroupID)
    {
        return DatabaseObj::deleteProductGroup($pGroupID);
    }

    static function checkDelete($pGroupID)
    {
        return DatabaseObj::checkProductGroupCanBeDeleted($pGroupID);
    }

    static function getCollectionGridData($pGroupID)
    {
        return DatabaseObj::getCollectionGridData($pGroupID);
    }

    static function getpreviewGridData($pGroupID)
    {
        return DatabaseObj::getProductGroupPreviewDataFromGroupID($pGroupID);
    }

    static function getLayoutPreviewData($pLayoutCode)
    {
        return DatabaseObj::getProductGroupPreviewInformationFromLayoutCode($pLayoutCode);
    }

    static function getLayoutGridData($pGroupID)
    {
        $resultArray = UtilsObj::getReturnArray();

        // if we have a group ID of 0 we have a new group and have no need to look it up in the database
        if ($pGroupID != 0)
        {
            $resultArray = DatabaseObj::getProductGroupLayoutGridData($pGroupID);
        }

        return $resultArray;
    }

    static function getMultipleLayoutPreviewData($pLayoutCodes, $pCollectionFilter)
    {
        return DatabaseObj::getMultipleLayoutPreviewData($pLayoutCodes, $pCollectionFilter);
    }


    static function saveProductGroup($pGroupID, $pGroupName, $pLayoutRules, $pCollectionRules)
    {
        global $gSession;

        $resultArray = array();
        $configurationsArray = array();
        $layoutCodesToDeleteArray = array();
        $rawCollectionRulesToDeleteArray = array();
        $layoutRulesArray = array();
        $collectionRulesArray = array();
        $collectionRulesToDeleteArray = array();
        $groupArray = array();
        $companyCode = $gSession['userdata']['companycode'];

        if ($companyCode == '**ALL**')
        {
            $companyCode = '';
        }

        if ($pLayoutRules !== '')
        {
            $layoutRulesArray = explode(',', $pLayoutRules);
        }

        if ($pCollectionRules !== '')
        {
            $collectionRulesArray = explode(',', $pCollectionRules);
        }

        if ($pGroupID != 0)
        {
            $preexistingLayoutRuleArray = DatabaseObj::getProductGroupLayoutRules($pGroupID);
            $layoutCodesToDeleteArray = array_diff($preexistingLayoutRuleArray['data'], $layoutRulesArray);
            // filter out any pre-existing rules so we don't double save
            $layoutRulesArray = array_diff($layoutRulesArray, $preexistingLayoutRuleArray['data']);

            $preexistingCollectionRuleArray = DatabaseObj::getCollectionRulesArray($pGroupID);
            $rawCollectionRulesToDeleteArray = array_diff($preexistingCollectionRuleArray['data'], $collectionRulesArray);

            $collectionRulesArray = array_diff($collectionRulesArray, $preexistingCollectionRuleArray['data']);

            foreach($rawCollectionRulesToDeleteArray as $theCollectionRule)
            {
                $theCollectionRuleArray = explode('.', $theCollectionRule);
                array_push($collectionRulesToDeleteArray, array('collectioncode' => $theCollectionRuleArray[0], 'productcode' => $theCollectionRuleArray[1]));
            }        
        }

        foreach($layoutRulesArray as $theLayout)
        {
            array_push($configurationsArray, array('collectioncode' => '*', 'productcode' => $theLayout));  
        }

        foreach($collectionRulesArray as $theCollectionRule)
        {
            $theCollectionRuleArray = explode('.', $theCollectionRule);
            array_push($configurationsArray, array('collectioncode' => $theCollectionRuleArray[0], 'productcode' => $theCollectionRuleArray[1]));
        }

        $groupArray['name'] = $pGroupName;
        $groupArray['companycode'] = $companyCode;
        $groupArray['active'] = 1;
        $groupArray['configurations'] = $configurationsArray;

        if ($pGroupID == 0)
        {
            $resultArray = DatabaseObj::insertProductGroupAndConfiguration($groupArray);
        }
        else
        {
            $resultArray = DatabaseObj::updateProductGroup($groupArray, $pGroupID, $layoutCodesToDeleteArray, $collectionRulesToDeleteArray);
        }


        return $resultArray;
    }
}
?>