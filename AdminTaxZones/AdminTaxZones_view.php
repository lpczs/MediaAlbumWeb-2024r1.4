<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');

class AdminTaxZones_view
{
	static function displayGrid()
	{
		global $gConstants;
		global $gSession;

        $smarty = SmartyObj::newSmarty('AdminTaxZones');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
		$smarty->assign('companycode', '');

        if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
        {
        	$smarty->assign('companyLogin', true);
        	$smarty->assign('companycode', $gSession['userdata']['companycode']);
        }
        else
        {
        	$smarty->assign('companyLogin', false);
        }

        $smarty->displayLocale('admin/taxzones/taxzonesgrid.tpl');
	}

	static function getGridData($pResultArray)
	{
		global $gConstants;

		$smarty = SmartyObj::newSmarty('AdminTaxZones');

		echo '[';

		$itemCount = count($pResultArray);

		echo '[' . $itemCount . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];

			echo "['" . $item['id'] . "',";
			if ($gConstants['optionms'])
			{
				echo "'" . $item['companycode'] . "',";
			}
			echo "'" . $item['localcode'] . "',";
			echo "'" . UtilsObj::encodeString($item['name'], true) . "',";
			echo "'" . $item['taxlevel1'] . "',";
			echo "'" . $item['taxlevel2'] . "',";
			echo "'" . $item['taxlevel3'] . "',";
			echo "'" . $item['taxlevel4'] . "',";
			echo "'" . $item['taxlevel5'] . "',";
			echo "'" . $item['shippingtaxcode'] . "',";
			echo "'" . UtilsObj::encodeString($item['countrycodes'], true) . "']";
			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
	}

    static function displayEntry($pTitle, $pID, $pCompanyCode, $pCode, $localCode, $pName, $pTaxLevel1, $pTaxLevel2, $pTaxLevel3, $pTaxLevel4, $pTaxLevel5, $pShippingTaxCode, $pCountryCodes, $pActionButtonName, $pCompanyHasROW, $pError = '')
    {
    	global $gSession;
    	global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminTaxZones');
        $smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('taxzoneid', $pID);
        $smarty->assign('taxzonename', UtilsObj::encodeString($pName, true));
        $smarty->assign('taxzonecompanycode', $pCompanyCode);
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('companyHasROW', false);
        $smarty->assign('companyLogin', false);

        if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
        {
        	$smarty->assign('companyLogin', true);
        	$smarty->assign('taxzonecompanycode', $gSession['userdata']['companycode']);

        	if ($pCompanyHasROW == 1)
        	{
        		$smarty->assign('companyHasROW', true);
        	}
        	else
        	{
        		$smarty->assign('companyHasROW', false);
        	}
        }

		if ($gConstants['optionms'])
        {
        	if (($pCompanyCode != '' && $localCode == '') || ($pCompanyCode == '' && $localCode == '') )
        	{
        		//This is rest of world system admin cannot change company
        		$isCompanyROW = 1;
        	}
        	else
        	{
        		$isCompanyROW = 0;
        	}
        }
        else
        {
        	$isCompanyROW = 0;

        }

        $smarty->assign('isCompanyROW', $isCompanyROW);



        if ($pID == 0)
        {
        	$smarty->assign('isEdit', 0);
        }
        else
        {
        	$smarty->assign('isEdit', 1);
        }

	    $smarty->assign('taxlevel1', UtilsObj::encodeString($pTaxLevel1, true));
	    $smarty->assign('taxlevel2', UtilsObj::encodeString($pTaxLevel2, true));
	    $smarty->assign('taxlevel3', UtilsObj::encodeString($pTaxLevel3, true));
	    $smarty->assign('taxlevel4', UtilsObj::encodeString($pTaxLevel4, true));
	    $smarty->assign('taxlevel5', UtilsObj::encodeString($pTaxLevel5, true));
	    $smarty->assign('shippingTaxCode', $pShippingTaxCode);
      	$smarty->assign('taxzonecodemain', $pCode);
        // setup the default tax zone code
        if (($pID > 0) && ($localCode == ''))
        {
            $smarty->assign('taxzonecode', $smarty->get_config_vars('str_LabelDefault'));
            $smarty->assign('isdefault', true);
        }
        else
        {
            $smarty->assign('taxzonecode', $localCode);
            $smarty->assign('isdefault', false);
        }

        // setup the tax rate lists
        $taxRatesArray = Array();
        $taxRatesArray = DatabaseObj::getTaxRatesList();
        $itemCount = count($taxRatesArray);

        for ($i = 0; $i < $itemCount; $i++)
        {
            $taxRateCode = $taxRatesArray[$i]['code'];
            $localTaxRateName = LocalizationObj::getLocaleString($taxRatesArray[$i]['name'], '', true);
        	$taxRatesArray[$i]['name'] = $taxRateCode . ' - ' . UtilsObj::encodeString($localTaxRateName,true);
        }

		$taxRatesArray2 = $taxRatesArray;
		array_unshift($taxRatesArray2, Array('id' => 0, 'code' => '', 'name' => $smarty->get_config_vars('str_LabelNone'), 'rate' => '0.0000'));

        $smarty->assign('taxcodelist1', $taxRatesArray);
        $smarty->assign('taxcodelist2', $taxRatesArray2);


        $smarty->assign('shippingtaxcodelist', $taxRatesArray);

       	$combinedList = UtilsAddressObj::getCombinedCountryRegionList(true, $gSession['browserlanguagecode']);
		$fullCountryList  = UtilsAddressObj::getPanelCountryList($combinedList);
		$bufList = array();
		for ($i=0; $i < count($fullCountryList); $i++)
		{
		   if ($fullCountryList[$i]['regionLabel'] != '')
		   {
		       $fullCountryList[$i]['regionLabel'] = $smarty->get_config_vars($fullCountryList[$i]['regionLabel']);
		   }
		   $bufList[] = '['.$i.',"'.$fullCountryList[$i]['countryCode'].'","'.UtilsObj::ExtJSEscape($fullCountryList[$i]['countryName']).'","'.$fullCountryList[$i]['regionLabel'].'","'.$fullCountryList[$i]['hasRegions'].'"]';
		}

		$smarty->assign('fullCountryList', '['.join(',', $bufList).']');

        $smarty->assign('session', $gSession['ref']);
        $smarty->displayLocale('admin/taxzones/taxzonesedit.tpl');
    }

    static function taxZoneDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['taxzonesids']);
        $smarty = SmartyObj::newSmarty('AdminTaxZones');

        if ($pResultArray['alldeleted'] == 0)
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');
			$alldeleted = '0';
        }
        else
        {
			$msg = $smarty->get_config_vars('str_MessageTaxZonesDeleted');
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$alldeleted = '1';
        }

		echo '{"success":true, "title":"' . $title . '", "msg":"' . $msg . '", "alldeleted":"' . $alldeleted . '", "idlist":"' . $deleteList . '"}';
	}

	static function taxZoneSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('AdminTaxZones');

    	if ($pResultArray['result'] != '')
        {
            $msg = str_replace('^0', $pResultArray['resultparam'], $smarty->get_config_vars($pResultArray['result']));
			$title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			$name = UtilsObj::encodeString($pResultArray['name'],false);
			echo '{"success":true, "data":{"id":' . $pResultArray['id'] . ',"companycode":"' . $pResultArray['companycode'] . '","code":"' . $pResultArray['code'] . '","name":"' . $name . '",
        		"taxlevel1":"' . $pResultArray['taxlevel1'] . '","shiptaxcode":"' . $pResultArray['shippingtaxcode'] . '","countries":"' . $pResultArray['countrycodes'] . '"}}';
        }
    }

	static function displayAdd()
	{
        $companyHasROW = $_GET['companyHasROW'];

        self::displayEntry('str_TitleNewTaxZone', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'str_ButtonAdd', $companyHasROW);
    }

    static function displayEdit($pResult)
	{
        self::displayEntry('str_TitleEditTaxZone', $pResult['id'], $pResult['companycode'], $pResult['code'],
                $pResult['localcode'], $pResult['name'],
                $pResult['level1'], $pResult['level2'], $pResult['level3'], $pResult['level4'], $pResult['level5'],
                $pResult['shippingcode'], $pResult['countrycode'], 'str_ButtonUpdate', $pResult['companyhasrow']);
    }
}

?>
