<?php

class AdminCurrencies_view
{
	static function displayGrid()
	{
		global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminCurrencies');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));

        $smarty->displayLocale('admin/currencies/currenciesgrid.tpl');
	}


	static function getGridData($pResultArray)
	{
		global $gConstants;

		$smarty = SmartyObj::newSmarty('AdminCurrencies');

		echo '[';

		$itemCount = count($pResultArray);

		echo '[' . $itemCount . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$code = $pResultArray[$i]['code'];

            if ($gConstants['defaultcurrencycode'] == $code)
            {
                $code .= ' ('. $smarty->get_config_vars('str_LabelDefault') .')';
            }

			$item = $pResultArray[$i];
			$name = LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $item['name'], 'black');
			echo "['" . $item['code'] . "',";
			echo "'" . $code . "',";
			echo "'" . $name . "',";
			echo "'" .  UtilsObj::encodeString($item['isonumber'], true) . "',";
			echo "'" . UtilsObj::encodeString($item['symbol'], true) . "',";
			echo "'" . $item['symbolatfront'] . "',";
			echo "'" . $item['decimalplaces'] . "',";
			echo "'" . $item['exchangerate'] . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';

	}

    static function displayEntry($pID, $pTitle, $pResultArray, $pActionButtonName, $pError = '')
    {
        global $gConstants;

    	// get default currency
		$constantsArray = DatabaseObj::getConstants();
		$defaultcurrencycode = $constantsArray['defaultcurrencycode'];
		$isDefault = 0;

		if ($defaultcurrencycode == $pResultArray['code'])
		{
			 $isDefault = 1;
		}

        $smarty = SmartyObj::newSmarty('AdminCurrencies');
        $smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('currencyid', $pResultArray['id']);
        $smarty->assign('code', $pResultArray['code']);
        $smarty->assign('isdefault', $isDefault);
        $smarty->assign('defaultcurrencycode', $defaultcurrencycode);
        $smarty->assign('isonumber',  UtilsObj::encodeString($pResultArray['isonumber'],true));
        $smarty->assign('symbol', UtilsObj::encodeString($pResultArray['symbol'],true));
        $smarty->assign('decimalplaces', $pResultArray['decimalplaces']);

        $smarty->assign('exchangeratedateset', LocalizationObj::formatLocaleDateTime($pResultArray['exchangeratedateset']));

        $smarty->assign('exchangerate', $pResultArray['exchangerate']);

        LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', $pResultArray['name']);

        $smarty->assign('symbolatfrontchecked', $pResultArray['symbolatfront']);
        $smarty->assign('defaultlanguagecode', $gConstants['defaultlanguagecode']);

		if ($pID == 0)
      	{
      		$smarty->assign('isEdit', 0);
      	}
      	else
      	{
      		$smarty->assign('isEdit', 1);
      	}

        $smarty->displayLocale('admin/currencies/currencyedit.tpl');
    }

    static function currencyDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['currencyids']);
        $smarty = SmartyObj::newSmarty('AdminCurrencies');

        if ($pResultArray['alldeleted'] == 0)
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');
			$alldeleted = '0';
        }
        else
        {
			$msg = $smarty->get_config_vars('str_MessageCurrencyDeleted');
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$alldeleted = '1';
        }

		echo '{"success":true, "title":"' . $title . '", "msg":"' . $msg . '", "alldeleted":"' . $alldeleted . '", "idlist":"' . $deleteList . '"}';
	}

	static function currencySave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('AdminCurrencies');

    	if ($pResultArray['result'] != '')
        {
            $msg = $smarty->get_config_vars($pResultArray['result']);
            $msg = str_replace('^0', $pResultArray['resultparam'], $msg);
            $title = $smarty->get_config_vars('str_TitleError');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			$name = UtilsObj::encodeString($pResultArray['name'],false);
			echo '{"success":true,"data":{"id":"' . $pResultArray['id'] . '","code":"' . $pResultArray['code'] . '","name":"' . $name . '",
        		"iso":"' . UtilsObj::encodeString($pResultArray['isonumber'],true) . '","symbol":"' . UtilsObj::encodeString($pResultArray['symbol'],true)  . '","symbolfront":"' . $pResultArray['symbolatfront'] . '",
        		"decimalplaces":"' . $pResultArray['decimalplaces'] . '","exchangerate":"' . $pResultArray['exchangerate'] . '"}}';
        }
    }

    static function displayAdd($pResultArray)
	{
        self::displayEntry($pResultArray['id'], 'str_TitleNewCurrency', $pResultArray, 'str_ButtonAdd', '');
    }

    static function displayEdit($pResultArray)
	{
	   self::displayEntry($pResultArray['id'], 'str_TitleEditCurrency', $pResultArray, 'str_ButtonUpdate', '');
    }
}

?>
