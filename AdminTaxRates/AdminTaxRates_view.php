<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');

class AdminTaxRates_view
{
	static function displayGrid()
	{
		global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminTaxRates');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->displayLocale('admin/taxrates/taxratesgrid.tpl');
	}

	static function getGridData($pResultArray)
	{
		global $gConstants;
		$smarty = SmartyObj::newSmarty('AdminTaxRates');

		echo '[';

		$itemCount = count($pResultArray);

		echo '[' . $itemCount . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];

			if ($item['code'] != TPX_CUSTOMTAX)
			{
				$name = LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $item['name'], 'black');

				echo "['" . $item['id'] . "',";
				echo "'" . $item['code'] . "',";
				echo "'" . $name . "',";
				echo "'" . $item['rate'] . "']";

				if ($i != $itemCount - 1)
				{
					echo ",";
				}
			}
		}
		echo ']';
	}

    static function displayEntry($pTitle, $pID, $pCode, $pName, $pRate, $pError = '')
    {
        global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminTaxRates');
        $smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('taxrateid', $pID);
        $smarty->assign('taxratecode', $pCode);
        $smarty->assign('taxrate', $pRate);

       	if ($pID == 0)
       	{
       		$smarty->assign('isEdit', 0);

       	}
       	else
       	{
       		$smarty->assign('isEdit', 1);
       	}

        LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', $pName);
        $smarty->assign('localizedinfomaxchars', 30);
        $smarty->assign('localizedinfowidth', 200);
        $smarty->assign('localizedinfocodesvar', 'gLocalizedCodesArray');
        $smarty->assign('localizednamelabel', $smarty->get_config_vars('str_LabelName'));
        $smarty->assign('defaultlanguagecode', $gConstants['defaultlanguagecode']);

        $smarty->displayLocale('admin/taxrates/taxratesedit.tpl');
    }

    static function taxRateDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['taxrateids']);
        $smarty = SmartyObj::newSmarty('AdminTaxRates');

        if ($pResultArray['alldeleted'] == 0)
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');
			$alldeleted = '0';
        }
        else
        {
			$msg = $smarty->get_config_vars('str_MessageTaxRatesDeleted');
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$alldeleted = '1';
        }

		echo '{"success":true, "title":"' . $title . '", "msg":"' . $msg . '", "alldeleted":"' . $alldeleted . '", "idlist":"' . $deleteList . '"}';
	}

	static function taxRateSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('AdminTaxRates');

    	if ($pResultArray['result'] != '')
        {
            $msg = str_replace('^0', $pResultArray['resultparam'], $smarty->get_config_vars($pResultArray['result']));
			$title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			$name = UtilsObj::encodeString($pResultArray['name'],false);
			echo '{"success":true, "data":{"id":' . $pResultArray['id'] . ',"code":"' . $pResultArray['code'] . '","name":"' . $name . '",
        		"rate":"' . $pResultArray['rate'] . '"}}';
        }
    }

	static function displayAdd()
	{
        self::displayEntry('str_TitleNewTaxRate', 0, '', '', '0.00');
    }

    static function displayEdit($pResult)
	{
        self::displayEntry('str_TitleEditTaxRate', $pResult['id'], $pResult['code'], $pResult['name'], $pResult['rate']);
    }
}

?>
