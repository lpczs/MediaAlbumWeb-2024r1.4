<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');

class AdminSitesSiteGroups_view
{
	static function displayGrid()
	{
		global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminSitesSiteGroups');
        $smarty->displayLocale('admin/sitessitegroups/sitegroupsgrid.tpl');
	}

	static function getGridData($pResultArray)
	{
		global $gConstants;
		$smarty = SmartyObj::newSmarty('AdminSitesSiteGroups');

		echo '[';

		$itemCount = count($pResultArray);

		echo '[' . $itemCount . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];
			$name = LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $item['name'], 'black');
			echo "['" . $item['id'] . "',";
			echo "'" . UtilsObj::encodeString($item['code'], true) . "',";
			echo "'" . ($name) . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
	}

    static function displayEntry($pTitle, $pID, $pCode, $pName)
    {
        global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminSitesSiteGroups');

        $smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('sitegroupid', $pID);
        $smarty->assign('sitegroupcode', UtilsObj::encodeString($pCode, true));

       	if ($pID == 0)
       	{
       		$smarty->assign('defaultDisplay', 0);

       	}
       	else
       	{
       		$smarty->assign('defaultDisplay', 1);
       	}

        LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', $pName);
        $smarty->assign('localizedinfomaxchars', 30);
        $smarty->assign('localizedinfowidth', 200);
        $smarty->assign('localizedinfocodesvar', 'gLocalizedCodesArray');
        $smarty->assign('localizednamelabel', $smarty->get_config_vars('str_LabelName'));
        $smarty->assign('defaultlanguagecode', $gConstants['defaultlanguagecode']);

        $smarty->displayLocale('admin/sitessitegroups/sitegroupsedit.tpl');
    }

    static function siteGroupsDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['sitegroupids']);
        $smarty = SmartyObj::newSmarty('AdminSitesSiteGroups');

        if ($pResultArray['alldeleted'] == 0)
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');
			$alldeleted = '0';
        }
        else
        {
			$msg = $smarty->get_config_vars('str_MessageSiteGroupsDeleted');
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$alldeleted = '1';
        }

		echo '{"success":true, "title":"' . $title . '", "msg":"' . $msg . '", "alldeleted":"' . $alldeleted . '", "idlist":"' . $deleteList . '"}';
	}

	static function siteGroupSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('AdminSitesSiteGroups');

    	if ($pResultArray['result'] != '')
        {
            $msg = str_replace('^0', $pResultArray['resultparam'], $smarty->get_config_vars($pResultArray['result']));
			$title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			$name = UtilsObj::encodeString($pResultArray['name'],false);
			echo '{"success":true, "data":{"id":' . $pResultArray['id'] . ',"code":"' . UtilsObj::encodeString($pResultArray['code'], true) . '","name":"' . UtilsObj::encodeString($name, true) . '"}}';
        }
    }

	static function displayAdd()
	{
        self::displayEntry('str_TitleNewStoreGroup', 0, '', '');
    }

    static function displayEdit($pResult)
	{
        self::displayEntry('str_TitleEditStoreGroup', $pResult['id'], $pResult['code'], $pResult['name']);
    }
}

?>
