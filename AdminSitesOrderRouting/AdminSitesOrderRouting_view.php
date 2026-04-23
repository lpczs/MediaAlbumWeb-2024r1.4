<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');
require_once('../Utils/UtilsRoute.php');

class AdminSitesOrderRouting_view
{
	static function displayGrid()
	{
		global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminSitesOrderRouting');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->displayLocale('admin/sitesorderrouting/orderroutinggrid.tpl');
	}

	static function getGridData($pResultArray)
	{
		global $gConstants;

		echo '[';

		$itemCount = count($pResultArray);

		echo '[' . $itemCount . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];

			echo "['" . $item['recordid'] . "',";
			echo "'" . $item['rule'] . "',";
			echo "'" . $item['condition'] . "',";

			$item['routingname'] = UtilsObj::encodeString($item['routingname'], true);
			$item['value'] = UtilsObj::encodeString($item['value'], true);
			$item['sitename'] = UtilsObj::encodeString($item['sitename'], true);

			if ($item['rule'] != TPX_ROUTE_BY_SHIPPING_COUNTRY_CODE && $item['rule'] != TPX_ROUTE_TO_VOUCHER_OWNER_CODE)
			{
				if ($item['rule'] == TPX_ROUTE_BY_PRODUCT_CODE)
				{
					$routingName = LocalizationObj::getLocaleString($item['routingname'], '', true);
				}
				else
				{
					$routingName = $item['routingname'];
				}

				if ($item['rule'] == TPX_ROUTE_BY_BRAND_CODE)
				{
					echo "'" . $item['routingname'] . "',";
				}
				else
				{
					echo "'" . $item['value'] . ' - '.$routingName. "',";
				}

			}
			else
			{
				echo "'" . $item['value'] . "',";
			}

			if ($item['sitecode'] != '')
			{
				$siteColumnValue = $item['sitecode'].' - '.$item['sitename'];
			}
			else
			{
				$siteColumnValue = $item['sitecode'];
			}

			echo "'" . $siteColumnValue . "',";
			echo "'" . $item['priority'] . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';

	}

    static function routingRuleDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['sitesids']);
        $smarty = SmartyObj::newSmarty('AdminSitesOrderRouting');

        if ($pResultArray['alldeleted'] == 1)
        {
			$msg = $smarty->get_config_vars('str_MessageRulesDeleted');
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$alldeleted = '1';
        }

		echo '{"success":true, "title":"' . $title . '", "msg":"' . $msg . '", "alldeleted":"' . $alldeleted . '", "idlist":"' . $deleteList . '"}';
	}

    static function displayEntry($pTitle, $pID, $pRule, $pCondition, $pValue, $pSiteCode)
    {
        global $gConstants;
		global $gSession;

        $smarty = SmartyObj::newSmarty('AdminSitesOrderRouting');
        $smarty->assign('title', $smarty->get_config_vars($pTitle));

		$routingConstants = RoutingObj::getRoutingConstants();
		$routingConditions = RoutingObj::getRoutingConditions();

        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->assign('ref', $gSession['ref']);
        $smarty->assign('routingRules', $routingConstants);
        $smarty->assign('routingConditions', $routingConditions);
        $smarty->assign('siteDefault', '');

        if ($pID == 0)
        {
        	// add rule
        	$smarty->assign('isEdit', 0);
        	$smarty->assign('ruleDefault', $smarty->get_config_vars('str_LabelBrand'));
        	$smarty->assign('conditionDefault', $smarty->get_config_vars('str_LabelIs'));
        	$smarty->assign('conditionValueDefault', $smarty->get_config_vars('str_LabelMakeSelection'));
        }
        else
        {
			// edit rule
        	$smarty->assign('isEdit', 1);
        	$smarty->assign('ruleDefault', $pRule);
        	$smarty->assign('siteDefault', $pSiteCode);
        	$smarty->assign('conditionDefault', $pCondition);
        	$smarty->assign('conditionValueDefault', $pValue);
        }

        $smarty->displayLocale('admin/sitesorderrouting/orderroutingedit.tpl');
    }

    static function routingRuleSave($pResultArray)
    {
       $smarty = SmartyObj::newSmarty('AdminSitesOrderRouting');

    	if ($pResultArray['result'] != '')
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			echo '{"success":true,"data":{"id":' . $pResultArray['id'] . ',"rule":"' . $pResultArray['rule'] . '","condition":"' . $pResultArray['condition'] . '",
        		"value":"' . $pResultArray['conditionValue'] . '","sitecode":"' . $pResultArray['site'] . '"}}';
        }
    }

    static function routingRulesTogglePriority($pResultArray)
    {
    	$smarty = SmartyObj::newSmarty('AdminSitesOrderRouting');

    	if ($pResultArray['result'] != '')
        {
			$msg = $smarty->get_config_vars('str_WarningPriorityNotUpdated');
			$title = $smarty->get_config_vars('str_TitleWarning');
			$updated = '0';
        }
        else
        {
			$msg = '';
			$title = '';
        	$updated = '1';
        }
		echo '{"success":true, "title":"' . $title . '", "msg":"' . $msg . '", "updated":"' . $updated . '"}';
    }

    static function getConditionValueStore($pResultArray)
    {
		global $gConstants;

		echo '[';

		$itemCount = count($pResultArray);

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];

			echo "['" . $item['id'] . "',";
			echo "'" . $item['name'] . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
    }

	static function displayAdd()
	{
        self::displayEntry('str_TitleSitesOrderAdd', 0, '', '', '', '');
    }

    static function displayEdit($pResultArray)
	{
		self::displayEntry('str_TitleSitesOrderEdit', $pResultArray['id'], $pResultArray['rule'], $pResultArray['condition'], $pResultArray['value'],
            $pResultArray['sitecode']);
    }
}

?>