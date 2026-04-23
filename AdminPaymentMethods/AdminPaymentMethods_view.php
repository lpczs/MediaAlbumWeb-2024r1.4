<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');

class AdminPaymentMethods_view
{

	static function displayGrid()
	{
		global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminPaymentMethods');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->displayLocale('admin/paymentmethods/paymentmethodsgrid.tpl');
	}

	static function getGridData($pResultArray)
	{
		global $gConstants;
		$smarty = SmartyObj::newSmarty('AdminPaymentMethods');

		echo '[';

		$itemCount = count($pResultArray);

		echo '[' . $itemCount . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];
			$name = LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $item['name'], 'black');
			echo "['" . $item['id'] . "',";
			echo "'" . $item['code'] . "',";
			echo "'" . $name . "',";
			echo "'" . $item['availablewhenshipping'] . "',";
			echo "'" . $item['availablewhennotshipping'] . "',";
			echo "'" . $item['isactive'] . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
	}

    static function displayEntry($pID, $pCode, $pName, $pAvailableWhenShipping, $pAvailableWhenNotShipping, $pIsActive, $pError = '')
    {
        global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminPaymentMethods');
        $smarty->assign('paymentmethodid', $pID);
        $smarty->assign('code', $pCode);
        $smarty->assign('PIS', 0);

		if ($pCode == 'PAYINSTORE')
		{
			$smarty->assign('PIS', 1);
		}

        $name = LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', $pName);
        $smarty->assign('localizedinfomaxchars', 30);
        $smarty->assign('localizedinfowidth', 200);
        $smarty->assign('localizedinfocodesvar', 'gLocalizedCodesArray');
        $smarty->assign('localizednamelabel', $smarty->get_config_vars('str_LabelName'));

        $smarty->assign('availablewhenshippingchecked', $pAvailableWhenShipping);

        $smarty->assign('availablewhennotshippingchecked', $pAvailableWhenNotShipping);

        $smarty->assign('activechecked', $pIsActive);

        $smarty->assign('defaultlanguagecode', $gConstants['defaultlanguagecode']);

        $smarty->displayLocale('admin/paymentmethods/paymentmethodsedit.tpl');
    }

    static function paymentMethodActivate($paymentMethods)
    {
        global $gSession;

        $itemCount = count($paymentMethods);

        $resultData = '{"success":true, "data":[';

        for ($i = 0; $i < $itemCount; $i++)
        {
			$paymentMethod = $paymentMethods[$i];
			$resultData .= '{"id":' . $paymentMethod['recordid'] . ',"active":"' . $paymentMethod['isactive'] . '"}';

        	if ($i != $itemCount - 1)
        	{
        		$resultData .= ",";
        	}
        }

        $resultData .= ']}';
        echo $resultData;
	}

	static function paymentMethodSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('AdminPaymentMethods');

    	if ($pResultArray['result'] != '')
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			$name = UtilsObj::encodeString($pResultArray['name'],false);
			echo '{"success":true, "data":{"id":' . $pResultArray['id'] . ', "name":"' . $name . '",
        		"availablewhenshipping":"' . $pResultArray['availablewhennotshipping'] . '","active":"' . $pResultArray['availablewhennotshipping'] . '","rate":"' . $pResultArray['isactive'] . '"}}';
        }
    }

    static function displayEdit($pResultArray)
	{
	   self::displayEntry($pResultArray['id'], $pResultArray['code'], $pResultArray['name'], $pResultArray['availablewhenshipping'],
	    $pResultArray['availablewhennotshipping'], $pResultArray['isactive'], '');
    }
}

?>
