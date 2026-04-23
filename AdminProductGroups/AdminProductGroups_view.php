<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');

class AdminProductGroups_view
{
    static function displayGrid()
	{
		global $gConstants;
        global $gSession;

        $smarty = SmartyObj::newSmarty('AdminProductGroups');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));

        if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
        {
        	$smarty->assign('companyLogin', true);
        	$smarty->assign('companycode', $gSession['userdata']['companycode']);
        }
        else
        {
        	$smarty->assign('companyLogin', false);
        }

        $smarty->displayLocale('admin/productgroups/productgroupsgrid.tpl');
	}

    static function getGridData($pProductGroupsArray)
    {
        $dataArray = array();
        $groups = $pProductGroupsArray['data']['groups'];
        $smarty = SmartyObj::newSmarty('AdminProductGroups');

        echo '[';
		echo '[' . $pProductGroupsArray['data']['count'] . '],';

		foreach ($groups as $group)
		{
			$dataArray[] = "['" . $group['id'] . "','" . UtilsObj::ExtJSEscape($group['name']) . "','" . $group['active'] . "','" . $pProductGroupsArray['error'] . "','" . $group['companycode'] . "']";
		}

		echo implode(',', $dataArray);

		echo ']';
    }

    static function displayAdd()
	{
        self::displayEntry(0, 0);
    }

    static function editDisplay()
    {
        self::displayEntry(1, 0);
    }

    static function displayEntry($pIsEdit, $pIsDuplicate)
    {
        $smarty = SmartyObj::newSmarty('AdminProductGroups');
        $smarty->assign('isedit', $pIsEdit);
        $smarty->assign('isduplicate', $pIsDuplicate);

        $smarty->displayLocale('admin/productgroups/productgroupsedit.tpl');
    }

    static function previewDisplay()
    {
        $smarty = SmartyObj::newSmarty('AdminProductGroups');

        $smarty->displayLocale('admin/productgroups/productgroupspreview.tpl');
    }

    static function duplicate()
    {
        self::displayEntry(0, 1);
    }

    static function displaysave($pResultArray)
    {
        $smarty = SmartyObj::newSmarty('AdminProductGroups');

        if ($pResultArray['error'] === 'str_DuplicateEntryError')
        {
            // we need to degenericise the string and send it back
            $msg = $smarty->getConfigVars('str_DuplicateEntryError');
            $title = $smarty->getConfigVars('str_TitleError');

            $msg = str_replace('^0', $smarty->getConfigVars('str_LabelProductGroup'), $msg);
            $msg = str_replace('^1', $smarty->getConfigVars('str_LabelName'), $msg);

            echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        elseif ($pResultArray['error'] != '')
        {

            $msg = str_replace('^0', $pResultArray['errorparam'], $smarty->get_config_vars($pResultArray['error']));
            $title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			echo '{"success":true}';
        }
    }

    static function deleteProductGroup($pResultArray)
    {
        $smarty = SmartyObj::newSmarty('AdminProductGroups');

		if ($pResultArray['error'] == '')
		{
			echo '{"success":true}';
		}
		else
		{
			$msg = $smarty->get_config_vars($pResultArray['error']);
			$title = $smarty->get_config_vars('str_TitleWarning');

            echo '{"success":false, "title":"' . $title . '","msg":"' . $msg . '"}';
		}


    }

    static function checkDelete($pResultArray)
    {
        $smarty = SmartyObj::newSmarty('AdminProductGroups');
        if ($pResultArray['error'] === '')
        {
            echo '{"success":true, "title":"", "msg":""}';
        }
        elseif ($pResultArray['error'] === 'str_WarningDeleteGroupAssignedToVoucher')
        {
            $msg = $smarty->get_config_vars($pResultArray['error']);
            $msg = str_replace("^0", $pResultArray['data'], $msg);
			$title = $smarty->get_config_vars('str_TitleWarning');
            echo '{"success":true, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
            $msg = $smarty->get_config_vars($pResultArray['error']);
			$title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
    }

    static function getCollectionGridData($pResultArray)
    {
        $dataArray = array();
        $data = $pResultArray['data'];
        $rowID = 0;

        echo '[';

		foreach ($data as $collectionCode => $collection)
		{
            $escapedCollectionCode = UtilsObj::ExtJSEscape($collectionCode);
            $collectionRowID = $rowID;

			$dataArray[] = "[" . $rowID . "," . $collection['selected'] . ", '" . $escapedCollectionCode . "','" . UtilsObj::ExtJSEscape(LocalizationObj::getLocaleString($collection['name'], '')) . "', true,-1]";
            $rowID++;

            foreach ($collection['products'] as $layout)
            {
                $dataArray[] =  "[" . $rowID . "," . $layout['selected'] . ",'" . UtilsObj::ExtJSEscape($layout['code']) . "','" . UtilsObj::ExtJSEscape(LocalizationObj::getLocaleString($layout['name'], '')) . "', false," . $collectionRowID . "]";
                $rowID++;
            }
		}
        echo '[' . ($rowID + 1) . '],';
		echo implode(',', $dataArray);

		echo ']';
    }

    static function getPreviewGridData($pResultArray)
    {
        $smarty = SmartyObj::newSmarty('AdminProductGroups');

        $dataArray = array();
        $data = $pResultArray['data'];
        $layoutCode = '';
        $rowID = 0;

        echo '[';

        foreach ($data as $collectionCode => $collection)
        {
            $dataArray[] = "[" . $rowID . ",'" . UtilsObj::ExtJSEscape($collectionCode) . "','" . UtilsObj::ExtJSEscape(LocalizationObj::getLocaleString($collection['name'], '')) . "',true,-1]";
            $collectionRowID = $rowID;
            $rowID++;
            foreach ($collection['layouts'] as $layout)
            {
                if ($layout['name'] === '')
                {
                    // if the name is empty we want to localise the string as it means that the record is referring to all layouts in a collection
                    $layoutCode = UtilsObj::ExtJSEscape($smarty->get_config_vars($layout['code']));
                }
                else
                {
                    $layoutCode = UtilsObj::ExtJSEscape($layout['code']);
                }

                $dataArray[] =  "[" . $rowID . ",'" . $layoutCode . "','" . UtilsObj::ExtJSEscape(LocalizationObj::getLocaleString($layout['name'], '')) . "',false," . $collectionRowID ."]";
                $rowID++;
            }
        }

        echo '[' . ($rowID + 1) . '],';
		echo implode(',', $dataArray);

		echo ']';
    }

    static function getLayoutPreviewData($pLayoutCode, $pResultArray)
    {
        $jsonArray = array('success' => false, 'layoutsfound' => false, 'previews' => '', 'layoutcode' => $pLayoutCode);

        if ($pResultArray['error'] === '')
        {
            $jsonArray['success'] = true;

            if (count($pResultArray['data']['descriptions']) > 0)
            {
                $jsonArray['previews'] = $pResultArray['data']['descriptions'];
                $jsonArray['layoutsfound'] = true;
            }
        }

        echo json_encode($jsonArray);
    }

    static function getMultipleLayoutPreviewData($pResultArray)
    {
        $jsonArray = array('success' => false, 'layoutsfound' => false, 'previews' => '');

        if ($pResultArray['error'] === '')
        {
            $jsonArray['success'] = true;

            if (count($pResultArray['data']['descriptions']) > 0)
            {
                $jsonArray['previews'] = $pResultArray['data']['descriptions'];
                $jsonArray['layoutsfound'] = true;
            }
        }

        echo json_encode($jsonArray);
    }

    static function getLayoutGridData($pResultArray)
    {
        $smarty = SmartyObj::newSmarty('AdminProductGroups');

        $dataArray = array();
        $data = $pResultArray['data'];
        $rowID = 0;
        $summary = '';
        $escapedLayoutCode = '';

        echo '[';

        foreach ($data as $layoutRule)
        {
            $escapedLayoutCode = UtilsObj::ExtJSEscape($layoutRule['code']);

            if ($layoutRule['count'] == 1)
            {
                $summary = $smarty->get_config_vars("str_LabelMatchesOneLayout");
            }
            else
            {
                $summary = $smarty->get_config_vars("str_LabelMatchesLayouts");
                $summary = str_replace("^0", $layoutRule['count'], $summary);
            }

            $dataArray[] = "[" . $rowID . ",'" . $escapedLayoutCode . "','" . $summary . "']";
            $rowID++;
        }

        echo '[' . ($rowID + 1) . '],';
		echo implode(',', $dataArray);

		echo ']';
    }

}

?>
