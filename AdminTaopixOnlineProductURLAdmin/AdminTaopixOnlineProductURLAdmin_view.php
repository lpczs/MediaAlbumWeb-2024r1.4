<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');
require_once('../Utils/UtilsRoute.php');

class AdminTaopixOnlineProductURLAdmin_view
{
	static function displayEntry()
	{
		global $gConstants;

		$showAll = 0;
		$smarty = SmartyObj::newSmarty('AdminTaopixOnlineProductURLS');
		$smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
		$smarty->assign('optionai', ($gConstants['optionai'] ? true : false));
		$smarty->assign('includeglobal', '1');
		$smarty->assign('showall', $showAll);

		$smarty->displayLocale('admin/taopixonlineproducturl/producturl.tpl');
	}

	static function getGridData($pResultArray)
	{
		echo '[';

		$itemCount = count($pResultArray['urldata']);
		echo '[' . $itemCount . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray['urldata'][$i];
			$productName = UtilsObj::encodeString(LocalizationObj::getLocaleString($item['productname'], '', true), true);

			echo "['" . $item['id'] . "',";
			echo "'" . $item['collectioncode'] . "',";
			echo "'" . $item['productcode'] . "',";
			echo "'" . $productName . "',";
			echo "'" . $item['url'] . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
	}

	static function productURLExport($pResultArray)
	{
        $smarty = SmartyObj::newSmarty('AdminTaopixOnlineProductURLS');
		$fileContents = "";
        $itemCount = count($pResultArray);
        $fileName = 'ProductURLExport_' . $pResultArray['groupcode'] . '_' . $pResultArray['filter'];

        $fileName .= '_' . date('d_M_Y_His');

		header('Content-Type: text/plain; charset=utf-8');
		header('Content-Disposition: Attachment; filename=' . $fileName . '.txt');
		header('Pragma: no-cache');
        header('Expires: 0');

        $itemCount = count($pResultArray['urldata']);
        $fileContents .= $smarty->get_config_vars('str_LabelLicenseKey') . chr(9);
		$fileContents .= $smarty->get_config_vars('str_LabelCollectionCode') . chr(9);
		$fileContents .= $smarty->get_config_vars('str_LabelLayoutCode') . chr(9);
		$fileContents .= $smarty->get_config_vars('str_LabelOnlineURL');
		$fileContents .= "\n";

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray['urldata'][$i];

			$fileContents .=  $pResultArray['groupcode'] . chr(9) . $item['collectioncode'] . chr(9) . $item['productcode'] . chr(9) .  $item['url'];
			$fileContents .=  "\n";
		}

		echo $fileContents;
	}

	static function productURLDecrypt($pResultArray)
	{
		$smarty = SmartyObj::newSmarty('AdminTaopixOnlineProductURLS');

		if ($pResultArray['result'] != '')
		{
			echo "{'success': 'false', 'error': '". $smarty->get_config_vars($pResultArray['result']) . "'}";
		}
		else
		{
			echo "{'success': 'true',"
							. "'uioverridemode': '" . $pResultArray['urldata']['uioverridemode']
							. "', 'aimodeoverride': '" . $pResultArray['urldata']['aimodeoverride']
							. "', 'collectioncode': '" . $pResultArray['urldata']['collectioncode']
							. "', 'layoutcode': '" . $pResultArray['urldata']['layoutcode']
							. "', 'groupcode': '" . $pResultArray['urldata']['groupcode']
							. "', 'groupdatastatus': '" . $pResultArray['urldata']['groupdata']['status']
							. "', 'groupdata': '" . $pResultArray['urldata']['groupdata']['code']
							. "', 'customparastatus': '" . $pResultArray['urldata']['customparams']['status']
							. "', 'customparamdata': '" . json_encode($pResultArray['urldata']['customparams']['params'])
							. "', 'wizardoverridestatus': '" . $pResultArray['urldata']['wizardmodeoverride']['status']
							. "', 'wizardoverrideparams': '" . json_encode($pResultArray['urldata']['wizardmodeoverride']['params'])
				. "'}";
		}
	}
}

?>