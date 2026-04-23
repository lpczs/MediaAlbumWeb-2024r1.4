<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');

class AdminShippingRates_view
{
	static function displayGrid()
	{
		global $gConstants;
		global $gSession;

        $smarty = SmartyObj::newSmarty('AdminShippingRates');
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
        $smarty->displayLocale('admin/shippingrates/shippingratesgrid.tpl');
	}


	static function getGridData($pResultArray)
	{
		global $gConstants;
		global $gSession;
		$smarty = SmartyObj::newSmarty('AdminShippingRates');

		echo '[';

		$itemCount = count($pResultArray['shippingrates']);

		echo '[' . $pResultArray['total'] . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray['shippingrates'][$i];
			$productCode = $item['productcode'];
	        $productName = LocalizationObj::getLocaleString($item['productname'], $gSession['browserlanguagecode'], true);
	        $productDisplayName = $productCode . ' - ' . $productName;

	        $shippingZoneCode = $item['shippingzonecode'];
	        $shippingZoneName = UtilsObj::encodeString($item['shippingzonename']);
	        $shippingMethodName = UtilsObj::encodeString($item['shippingmethodame'],true);

	        if ($shippingZoneCode !='')
        	{
        	   $shippingZoneDisplayName = $shippingZoneCode . ' - ' . $shippingZoneName;
        	}
        	else
        	{
        	   $shippingZoneDisplayName = '<i>' . $shippingZoneName . '</i>';
        	}

	        if ($productCode !='')
        	{
        	   $productDisplayName = $productCode . ' - ' . $productName;
        	}
        	else
        	{
        	   $productDisplayName = '<i>' . $smarty->get_config_vars('str_LabelDefault') . '</i>';
        	}

	        $groupArray = explode(',', $item['groupcodes']);
            $groupCount = count($groupArray);
            $groupList = Array();

            for ($j = 0; $j < $groupCount; $j++)
            {
                array_push($groupList, $groupArray[$j]);
            }
            sort($groupList);

            $name = '';
            for ($j = 0; $j < $groupCount; $j++)
            {
                $code = $groupList[$j];

                if ($code =='')
                {
                    $name .= '<i>' . $smarty->get_config_vars('str_LabelDefault') . '</i>';
                }
                else
                {
                   $name .= $code;
                }

                $name .= '<br>';
            }

            $name = substr($name, 0, strlen($name) - 4);
            $info = $item['productinfo'];

            $additionalInformation = LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $info, 'black');
            $shippingRate = $item['shippingrate'];

            $shippingRatesArray = DatabaseObj::shippingRateStringToArray($shippingRate);
            $shippingRateCount = count($shippingRatesArray);
            $formattedShippingRate = '';
            for ($j = 0; $j < $shippingRateCount; $j++)
            {
            	$formattedShippingRate .= $shippingRatesArray[$j]['start'] . ' - ' . $shippingRatesArray[$j]['end'] . ' = '.$shippingRatesArray[$j]['sell'].'<br />';
            }

            $active = $item['isactive'];
			echo "['" . $item['recordid'] . "',";

			if ($gConstants['optionms'])
			{
				echo "'" . $item['companycode'] . "',";
			}

			echo "'" . $item['shippingratecode'] . "',";
			echo "'" . $item['shippingmethodcode'] . ' - ' . LocalizationObj::getLocaleString($shippingMethodName, '', true) .  "',";
			echo "'" . UtilsObj::encodeString($shippingZoneDisplayName, true) . "',";
			echo "'" . UtilsObj::encodeString($productDisplayName, true) . "',";
			echo "'" . UtilsObj::encodeString($name, true) . "',";
			echo "'" . $additionalInformation . "',";
			echo "'" . $formattedShippingRate . "',";
			echo "'" . $active . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
	}


    static function displayEntry($pTitle, $pDataArray, $pActionButtonName, $pError = '', $pErrorInfo = '')
    {
        global $gSession;
        global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminShippingRates');
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('loginType', $gSession['userdata']['usertype']);

        $smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('shippingrateid', $pDataArray['recordid']);
        $smarty->assign('shippingratecode', $pDataArray['shippingratecode']);
        $smarty->assign('shippingmethod', $pDataArray['shippingmethodcode']);
        $smarty->assign('shippingzone', $pDataArray['shippingzonecode']);
        $smarty->assign('productcode', $pDataArray['productcode']);
        $smarty->assign('parentcode', $pDataArray['groupcode']);
        $smarty->assign('payinstoreallowed', $pDataArray['payinstoreallowed']);
		$smarty->assign('defaultlanguagecode', $gConstants['defaultlanguagecode']);
		$smarty->assign('taxcode', $pDataArray['taxcode']);
        $smarty->assign('companyLogin', false);

        if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
        {
        	$smarty->assign('companyLogin', true);
        }

        if ($pDataArray['companycode'] == '')
        {
        	$smarty->assign('companycode', 'GLOBAL');
        }
        else
        {
        	$smarty->assign('companycode', $pDataArray['companycode']);
        }

        if ($pActionButtonName == 'str_ButtonAdd')
      	{
      		$smarty->assign('isEdit', 0);
      	}
      	else
      	{
      		$smarty->assign('isEdit', 1);
      	}

        $itemsList = Array();
        $itemsArray = $pDataArray['items'];
        $itemCount = count($itemsArray);
        for ($i = 0; $i < $itemCount; $i++)
        {
            array_push($itemsList, $itemsArray[$i]['groupcode']);
        }

        $shippingMethodsList = $pDataArray['shippingmethodslist'];
        $shippingMethodCount = count($shippingMethodsList);

        for ($i = 0; $i < $shippingMethodCount; $i++)
        {
            $shippingMethodsList[$i]['name'] =  UtilsObj::encodeString(LocalizationObj::getLocaleString($shippingMethodsList[$i]['name'], '', true),true);
        }

        $smarty->assign('shippingmethodslist', $shippingMethodsList);

        $productNameList = $pDataArray['productslist'];
        $defaultArrayItem['id'] = 0;
        $defaultArrayItem['code'] = '';
        $defaultArrayItem['name'] = $smarty->get_config_vars('str_LabelDefault');

        $productNameList = array_merge(Array($defaultArrayItem), $productNameList);
        $productCount = count($productNameList);

        for ($i = 0; $i < $productCount; $i++)
        {
            $productCode = $productNameList[$i]['code'];

            // stripping language code from the front of the product code for the product name dropdown list
            if ($productNameList[$i]['name'] != $smarty->get_config_vars('str_LabelDefault'))
            {
               $productNameList[$i]['name'] =  UtilsObj::encodeString(LocalizationObj::getLocaleString($productNameList[$i]['name'], $gSession['browserlanguagecode'], true),true);
            }
            else
            {
            	$productNameList[$i]['name'] = $smarty->get_config_vars('str_LabelDefault');
            }

        }

        $smarty->assign('productlist', $productNameList);

        // build the license keys list
        $existingCodesList = $pDataArray['existinggroupcodes'];
        $allCodesArray = $pDataArray['allgroupcodes'];
        $itemCount = count($allCodesArray);
        $licensekeyIndeces = Array();

        for ($i = 0; $i < $itemCount; $i++)
        {
            $groupCode = $allCodesArray[$i]['code'];
            if ($groupCode == '')
            {
                $name = $smarty->get_config_vars('str_LabelDefault');
            }
            else
            {
                $name = $groupCode;
            }

            if (! in_array($groupCode, $existingCodesList))
            {
			    if (in_array($groupCode, $itemsList))
			    {
		    		array_push($licensekeyIndeces, $i);
		    	}
            }
        }

		 $smarty->assign('assignedLicenseKeys', $licensekeyIndeces);
		 $smarty->assign('rows', $allCodesArray);

		 //build the site groups list
		 $existingStoreGroupsList = $pDataArray['sitegroups'];
		 $itemCount = count($existingStoreGroupsList);

         $allStoreGroupsArray = $pDataArray['allsitegroups'];
         $allStoreGroupsItemCount = count($allStoreGroupsArray);

         $siteGroupIndices = Array();
         $exisitingItemsList = Array();

         for ($i = 0; $i < $itemCount; $i++)
         {
            array_push($exisitingItemsList, $existingStoreGroupsList[$i]['sitegroupcode']);
         }

         for ($i = 0; $i < $allStoreGroupsItemCount; $i++)
         {
		    $siteGroupCode = $allStoreGroupsArray[$i]['code'];
		    $allStoreGroupsArray[$i]['name'] = UtilsObj::encodeString($allStoreGroupsArray[$i]['name'],true);
		    if (in_array($siteGroupCode, $exisitingItemsList))
		    {
	    		array_push($siteGroupIndices, $i);
	    	}
         }

         $smarty->assign('storeGroups', $allStoreGroupsArray);

	     $smarty->assign('assignedSiteGroups', $siteGroupIndices);

	     /* explode the shipping rates */
	     $shippingRates = $pDataArray['shippingrates'];
	     $shippingRatesList = explode(" ",$shippingRates);
	   	 $itemCount = count($shippingRatesList);

		$weights = '[';
		for ($i = 0; $i < $itemCount; $i++)
		{
		    $shippingItem = $shippingRatesList[$i];
		    if ($shippingItem != "")
		    {
		        $shippingWeights = explode("-", $shippingItem);

		        $weights .= "['".$shippingWeights[0] . "',";
		        $weights .= "'".$shippingWeights[1] . "',";
		        $weights .= "'".$shippingWeights[2] . "',";
		        $weights .= "'".$shippingWeights[3] . "',";
		        $weights .= "'".$shippingWeights[4] . "']";
		    }

		    if ($i != $itemCount - 1)
			{
				$weights.= ",";
			}
		}
		$weights .= ']';

       	$smarty->assign('shippingrates', $weights);

        $smarty->assign('ordervaluerange', $pDataArray['ordervaluetype']);
        $smarty->assign('orderminvalue', $pDataArray['orderminvalue']);
        $smarty->assign('ordermaxvalue', $pDataArray['ordermaxvalue']);
        $smarty->assign('ordervalueincludesdiscountchecked', $pDataArray['ordervalueincludesdiscount']);

		$smarty->assign('activechecked', $pDataArray['isactive']);

	    LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', $pDataArray['shippingrateinfo']);
	    $smarty->assign('localizedinfomaxchars', 35);
	    $smarty->assign('localizedinfowidth', 260);
	    $smarty->assign('localizedinfocodesvar', 'gLocalizedCodesArray');
	    $smarty->assign('localizednamelabel', $smarty->get_config_vars('str_LabelInformation'));

        $smarty->displayLocale('admin/shippingrates/shippingratesedit.tpl');
    }

    static function shippingRateActivate($pRates)
    {
        global $gSession;

        $itemCount = count($pRates);
        $resultData = '{"success":true, "data":[';

        for ($i = 0; $i < $itemCount; $i++)
        {
        	$item = $pRates[$i]['items'];
        	$itemsCount = count($item);

        	for ($j = 0; $j < $itemsCount; $j++)
        	{
        		$data = $item[$j];
        		$resultData .= '{"id":' . $data['recordid'] . ',"active":"' . $data['isactive'] . '"}';

	       		if ($j != $itemsCount - 1)
				{
					$resultData .= ",";
				}
        	}

        	if ($i != $itemCount - 1)
			{
				$resultData .= ",";
			}
        }

        $resultData .= ']}';

        echo $resultData;
	}

	static function shippingRateDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['shippingrateids']);
        $smarty = SmartyObj::newSmarty('AdminShippingRates');

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

	static function getShippingZonesFromCompanyCode($pResultArray)
    {
		global $gConstants;

		echo '[';

		$itemCount = count($pResultArray);

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];

			echo "['" . $item['code'] . "',";
			echo "'" . UtilsObj::encodeString($item['name'],true) . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
    }

    static function getProductsFromCompany($pResultArray)
    {
		global $gConstants;

		$smarty = SmartyObj::newSmarty('AdminShippingRates');

		echo '[';

        $defaultArrayItem['code'] = '';
        $defaultArrayItem['name'] = $smarty->get_config_vars('str_LabelDefault');

        $productNameList = array_merge(Array($defaultArrayItem), $pResultArray);

		$itemCount = count($productNameList);

		for ($i = 1; $i < $itemCount; $i++)
		{
			$productNameList[$i]['name'] = UtilsObj::encodeString(LocalizationObj::getLocaleString($productNameList[$i]['name'], '', true),true);
			$productNameList[$i]['code'] = UtilsObj::encodeString($productNameList[$i]['code'], '', true);
		}

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $productNameList[$i];

			echo "['" . $item['code'] . "',";

			if ($item['code'] != '')
			{
				echo "'(" . $item['code'] .") ". $item['name'] . "']";
			}
			else
			{
				echo "'". $item['name'] . "']";
			}

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
    }

    static function getLicenseKeyFromCompany($pResultArray)
    {
    	global $gConstants;

		$existingCodesList = $pResultArray['existingcodes'];
		$allCodesArray = $pResultArray['groupcodes'];

		echo '[';

		$itemCount = count($allCodesArray);

		for ($i = 0; $i < $itemCount; $i++)
		{
			echo "['" . $allCodesArray[$i]['id'] . "',";
			echo "'" . $allCodesArray[$i]['code'] . "',";
			echo "'" . $allCodesArray[$i]['active'] . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
    }

    static function shippingRateSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('AdminShippingRates');

    	if ($pResultArray['result'] != '')
        {
            $msg = str_replace('^0', $pResultArray['resultparam'], $smarty->get_config_vars($pResultArray['result']));
			$title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			echo '{"success":true}';
        }
    }


	static function displayAdd($pDataArray, $pError = '', $pErrorInfo = '')
	{
        self::displayEntry('str_TitleNewShippingRate', $pDataArray, 'str_ButtonAdd', $pError, $pErrorInfo);
    }

    static function displayEdit($pDataArray, $pError = '', $pErrorInfo = '')
	{
        self::displayEntry('str_TitleEditShippingRate', $pDataArray, 'str_ButtonUpdate', $pError, $pErrorInfo);
    }

}

?>
