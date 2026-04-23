<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');

class AdminShippingZones_view
{

	static function displayGrid()
	{
		global $gConstants;
		global $gSession;

        $smarty = SmartyObj::newSmarty('AdminShippingZones');
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

        $smarty->displayLocale('admin/shippingzones/shippingzonesgrid.tpl');
	}

	static function getGridData($pResultArray)
	{
		global $gConstants;
		$smarty = SmartyObj::newSmarty('AdminShippingZones');

		echo '[';

		$itemCount = count($pResultArray);

		echo '[' . $itemCount . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];
			$name = UtilsObj::encodeString($item['name'], false);
			echo "['" . $item['id'] . "',";
			if ($gConstants['optionms'])
			{
				echo "'" . $item['companycode'] . "',";
			}
			echo "'" . $item['localcode'] . "',";
			echo "'" . $name . "',";
			echo "'" . UtilsObj::encodeString($item['countrycodes'], true) . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
	}


    static function displayEntry($pTitle, $pID, $pCompanyCode, $pCode, $pLocalCode, $pName, $pCountryCodes, $pActionButtonName, $pCompanyHasROW, $pError = '')
    {
    	global $gSession;
    	global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminShippingZones');
        $smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('shippingzoneid', $pID);
        $smarty->assign('shippingzonename',  UtilsObj::encodeString($pName, true));
        $smarty->assign('shippingzonecompanycode', $pCompanyCode);
        $smarty->assign('shippingzonecodemain', $pCode);
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('companyLogin', false);
        $smarty->assign('companyHasROW', false);

        if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
        {
        	$smarty->assign('companyLogin', true);
        	$smarty->assign('shippingzonecompanycode', $gSession['userdata']['companycode']);

        	if ($pCompanyHasROW == 1)
        	{
        		$smarty->assign('companyHasROW', true);
        	}
        }

        if ($gConstants['optionms'])
        {
        	if (($pCompanyCode != '' && $pLocalCode == '') || ($pCompanyCode == '' && $pLocalCode == ''))
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

       if($pID == 0)
       {
       	$smarty->assign('isEdit', 0);
       }
       else
       {
       	$smarty->assign('isEdit', 1);
       }

        // setup the default shipping zone code
        if (($pID > 0) && ($pLocalCode == ''))
        {
            $smarty->assign('shippingzonecode', $smarty->get_config_vars('str_LabelDefault'));
            $smarty->assign('isdefault', true);
        }
        else
        {
            $smarty->assign('shippingzonecode', $pLocalCode);
            $smarty->assign('isdefault', false);
        }

        // list of country and region codes / names used in this zone
		// e.g. NO,AL,US_TX,US_AL,GB_DEVON,GB_FIVE,GR,CH
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
        $smarty->displayLocale('admin/shippingzones/shippingzonesedit.tpl');
    }

    static function shippingZoneSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('AdminShippingZones');

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
        		"countries":"' . $pResultArray['countries'] . '"}}';
        }
    }

    static function shippingZoneDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['shippingzoneids']);
        $smarty = SmartyObj::newSmarty('AdminShippingZones');

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

	static function displayAdd()
	{
        $companyHasROW = $_GET['companyHasROW'];
        self::displayEntry('str_TitleNewShippingZone', 0, '', '', '', '', '', 'str_ButtonAdd', $companyHasROW);
    }

    static function displayEdit($pResultArray)
	{
         $companyHasROW = $_GET['companyHasROW'];
        self::displayEntry('str_TitleEditShippingZone', $pResultArray['id'], $pResultArray['companycode'], $pResultArray['code'], $pResultArray['localcode'], $pResultArray['name'],
            $pResultArray['countries'], 'str_ButtonUpdate', $companyHasROW);
    }
}

?>
