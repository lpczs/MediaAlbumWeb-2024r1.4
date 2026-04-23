<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');

class AdminShippingMethods_view
{

	static function displayGrid()
	{
		global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminShippingMethods');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->displayLocale('admin/shippingmethods/shippingmethodsgrid.tpl');
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
			$name = LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $item['name'], 'black');
			echo "['" . $item['id'] . "',";
			echo "'" . $item['code'] . "',";
			echo "'" .  UtilsObj::encodeString($name,true) . "',";
			echo "'" . $item['requiresdelivery'] . "',";

			if ($gConstants['optioncfs'])
			{
				echo "'" . $item['collectfromstore'] . "',";
				echo "'" . $item['isdefault'] . "']";
			}
			else
			{
				echo "'" . $item['isdefault'] . "']";
			}

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
	}


    static function displayEntry($pTitle, $pID, $pCode, $pName, $pUseDefaultBillingAddress, $pUseDefaultShippingAddress, $pCanModifyContactDetails,
		$pRequiresDelivery, $pIsDefault, $pOrderValueRange, $pOrderValueMin, $pOrderValueMax, $pOrderValueIncludesDiscount, $pActionButtonName,
		$pCollectFromStore, $pSiteGroupLabel, $pAssetID, $pAllowGroupingByCountry = 0, $pAllowGroupingByRegion = 0, $pAllowGroupingByStoreGroup = 0,
		$pShowStoreList = 1, $pError = '')
    {
        global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminShippingMethods');
		$smarty->left_delimiter = '|:';
		$smarty->right_delimiter = ':|';

        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('shippingmethodid', $pID);
        $smarty->assign('shippingmethodcode', $pCode);

        LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', $pName);
        $smarty->assign('localizedinfomaxchars', 30);
        $smarty->assign('localizedinfowidth', 200);
        $smarty->assign('localizedinfocodesvar', 'gLocalizedCodesArray');
        $smarty->assign('localizednamelabel', $smarty->get_config_vars('str_LabelName'));

        //site group label
        LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', $pSiteGroupLabel, true, true);

        $smarty->assign('usedefaultbillingaddress', $pUseDefaultBillingAddress);
        $smarty->assign('usedefaultshippingaddress', $pUseDefaultShippingAddress);
        $smarty->assign('canmodifycontactdetails', $pCanModifyContactDetails);
        $smarty->assign('requiresdeliverychecked', $pRequiresDelivery);
        $smarty->assign('defaultchecked', $pIsDefault);
        $smarty->assign('ordervaluerange', $pOrderValueRange);
        $smarty->assign('collectfromstore', $pCollectFromStore);
        $smarty->assign('sitegrouplabel', $pSiteGroupLabel);
        $smarty->assign('allowgroupingbycountry', $pAllowGroupingByCountry);
        $smarty->assign('allowgroupingbyregion', $pAllowGroupingByRegion);
        $smarty->assign('allowgroupingbystoregroup', $pAllowGroupingByStoreGroup);
		$smarty->assign('showstorelistonopen', $pShowStoreList);
        $smarty->assign('assetID', $pAssetID);
        $smarty->assign('defaultlanguagecode', $gConstants['defaultlanguagecode']);

      	if ($pID == 0)
      	{
      		$smarty->assign('isEdit', 0);

      	}
      	else
      	{
      		$smarty->assign('isEdit', 1);
      	}

        $smarty->assign('orderminvalue', $pOrderValueMin);
        $smarty->assign('ordermaxvalue', $pOrderValueMax);

        $smarty->assign('ordervalueincludesdiscountchecked', $pOrderValueIncludesDiscount);


        if (substr($pError, 0, 4) == 'str_')
        {
            $smarty->assign('error', $smarty->get_config_vars($pError));
        }
        else
        {
            $smarty->assign('error', $pError);
        }

        $smarty->assign('actionbutton', $smarty->get_config_vars($pActionButtonName));

        $smarty->displayLocale('admin/shippingmethods/shippingmethodsedit.tpl');
    }

	static function displayAdd()
	{
        self::displayEntry('str_TitleNewShippingMethod', 0, '', '', 0, 0, 0, 1, 0, '', 0.00, 0.00, 0, 'str_ButtonAdd', 0, '', 0);
    }

    static function displayEdit($pResultArray)
	{
        self::displayEntry('str_TitleEditShippingMethod', $pResultArray['id'], $pResultArray['code'], $pResultArray['name'],
			$pResultArray['usedefaultbillingaddress'], $pResultArray['usedefaultshippingaddress'], $pResultArray['canmodifycontactdetails'],
			$pResultArray['requiresdelivery'], $pResultArray['isdefault'], $pResultArray['ordervaluetype'], $pResultArray['orderminvalue'],
			$pResultArray['ordermaxvalue'], $pResultArray['ordervalueincludesdiscount'], 'str_ButtonUpdate', $pResultArray['collectfromstore'],
			$pResultArray['sitegrouplabel'], $pResultArray['assetid'], $pResultArray['allowgroupingbycountryname'],
			$pResultArray['allowgroupingbyregionname'], $pResultArray['allowgroupingbystoregroupname'], $pResultArray['showstorelistonopen']);
    }

    static function shippingMethodDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['shippingmethodids']);
        $smarty = SmartyObj::newSmarty('AdminShippingMethods');

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

    static function shippingMethodSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('AdminShippingMethods');

    	if ($pResultArray['result'] != '')
        {
            $msg = str_replace('^0', $pResultArray['resultparam'], $smarty->get_config_vars($pResultArray['result']));
            $title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			$name = UtilsObj::encodeString($pResultArray['name'],false);
			echo '{"success":true,"data":{"id":' . $pResultArray['id'] . ',"code":"' . $pResultArray['code'] . '","name":"' . $name . '",
        		"reqdelivery":"' . $pResultArray['requiresdelivery'] . '","default":"' . $pResultArray['isdefault'] . '"}}';
        }
    }

    static function uploadLogo($pResultArray)
    {
    	if ($pResultArray['result'] == '')
    	{
			$width = $pResultArray['width'];
			$height = $pResultArray['height'];
			$message = '';
			if (($width > $pResultArray['recommendedwidth']) || ($height > $pResultArray['recommendedheight']))
			{
		       	$smarty = SmartyObj::newSmarty('AdminShippingMethods');
				$message = $smarty->get_config_vars('str_MessageLogoDimensions');
				$searchFor = ['^w', '^h', '^rw', '^rh'];
				$replaceWith = [$width, $height, $pResultArray['recommendedwidth'], $pResultArray['recommendedheight']];
				$message = str_replace($searchFor, $replaceWith, $message);
				echo '{"success":true, "msg":"' . $message . '"}';
			}
			else
			{
				echo '{"success":true, "msg":""}';
			}
    	}
    	else
    	{
			echo '{success: false, "msg":""}';
    	}
    }

}

?>
