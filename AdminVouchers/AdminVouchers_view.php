<?php

require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');

class AdminVouchers_view
{
	static function displayList($pResultArray)
	{
	    global $gSession;
	    global $gConstants;

	    $tableData = '';
	    $rowCount = 1;

	    $smarty = SmartyObj::newSmarty('AdminVouchers');

        if ($pResultArray['promotionname'] == '')
        {
            $smarty->assign('title', $smarty->get_config_vars('str_VoucherTitleSingleVouchers'));
        }
        else
        {
            SmartyObj::replaceParams($smarty, 'str_TitlePromotionVouchers', $pResultArray['promotioncode'] . ' - ' . UtilsObj::encodeString($pResultArray['promotionname'], true));
            $smarty->assign('title', $smarty->get_template_vars('str_TitlePromotionVouchers'));
        }

        $smarty->assign('promotionid', $pResultArray['promotionid'] );

		$smarty->displayLocale('admin/vouchers/voucherslistwindow.tpl');
	}

    static function displayEntry($pTitle, $pResultArray, $pActionButtonName, $pError = '', $pTemplate = '', $pDestAction = '')
    {
        global $gSession, $gConstants;

        $smarty = SmartyObj::newSmarty('AdminVouchers');
        $smarty->assign('optionms', $gConstants['optionms']);
        $smarty->assign('showProd', ($gConstants['optionms']) && (($gSession['userdata']['usertype'] == TPX_LOGIN_SYSTEM_ADMIN) || ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN) ));
        $smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('voucherid', $pResultArray['id']);
        $smarty->assign('promotionid', $pResultArray['promotionid']);
        $smarty->assign('promotioncode', $pResultArray['promotioncode']);
        $smarty->assign('code', $pResultArray['code']);
        $smarty->assign('defaultlanguagecode', $gConstants['defaultlanguagecode']);

        // What template fields to display
        $smarty->assign('displayMode', $pResultArray['displayMode']);
        $smarty->assign('voucherusedinorder', $pResultArray['voucherusedinorder']);

        // Calculate the difference in days.
		$date1 = strtotime($pResultArray['startdate']);
		$date2 = strtotime('2000-01-01');

		    // Which is the latest?
	    if ($date1 < $date2) {
	      $pResultArray['startdate'] = '2000-01-01';
	    }

        if ($pResultArray['enddate'] == '1970-01-01 01:00:00' || $pResultArray['enddate'] == '')
        {
        	$pResultArray['enddate'] = date('Y-m-d');
        }

        $smarty->assign('startdate', LocalizationObj::formatLocaleDateTime($pResultArray['startdate']));
        $smarty->assign('enddate', LocalizationObj::formatLocaleDateTime($pResultArray['enddate']));
        $smarty->assign('usergroup', $pResultArray['groupcode']);
        $smarty->assign('userid', $pResultArray['userid']);
        $smarty->assign('earliestdate', LocalizationObj::formatLocaleDateTime('1999-01-01 00:00:00'));
        $smarty->assign('latestdate', LocalizationObj::formatLocaleDateTime('2038-01-01 00:00:00'));

        $smarty->assign('minqty', $pResultArray['minqty']);
        $smarty->assign('maxqty', $pResultArray['maxqty']);
        $smarty->assign('defaultdiscount', $pResultArray['defaultdiscount']);

        if ($pResultArray['lockqty'] == 1)
        {
            $smarty->assign('lockqtychecked', 'checked');
        }
        else
        {
             $smarty->assign('lockqtychecked', '');
        }

        if ($pResultArray['defaultdiscount'] == 1)
        {
            $smarty->assign('defaultdiscountchecked', 'checked');
        }
        else
        {
            $smarty->assign('defaultdiscountchecked', '');
        }


        //setup a production sites list
        $itemList = $pResultArray['productionsites'];
        $itemCount = count($itemList);


        $itemListHTML = '';
        $prodSiteValue = '';
        $itemProductionListArray = array();
        if (($gConstants['optionms']) && (($gSession['userdata']['usertype'] == TPX_LOGIN_SYSTEM_ADMIN) || ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN) ))
		{
        	//$itemListHTML = '<select id="productionsitelist" name="productionsitelist" class="text">';
        	//$itemListHTML .= '<option value="">'.$smarty->get_config_vars('str_Global').'</option>';
        	$itemProductionListArray[] = array('id' => '', 'name' => $smarty->get_config_vars('str_Global'));
			for ($i = 0; $i < $itemCount; $i++)
        	{
            	$prodSiteCode = $itemList[$i]['code'];
            	$prodSiteName = $itemList[$i]['name'];

            	$displayName = $prodSiteCode . ' - ' . UtilsObj::encodeString($prodSiteName,true);
            	$itemProductionListArray[] = array('id' => $prodSiteCode, 'name' => $displayName);
        	}
		}
		else
		{
			if (isset($itemList[0])) $prodSiteValue = $itemList[0]['code'];
		}
		$smarty->assign('productionsiteslist', $itemProductionListArray);
        $smarty->assign('prodSiteValue', $prodSiteValue);
        $smarty->assign('prodSiteCode', $pResultArray['owner']);


        // setup the products list
        $itemList = $pResultArray['products'];
        $itemCount = count($itemList);
        $smarty->assign('productcount', $itemCount);

        //$itemListHTML = '<select id="productslist" name="productslist" class="text" onChange="return productsListEvent();">';
        // [ [],[],[] ]
        $itemListArray = array();

        for ($i = 0; $i < $itemCount; $i++)
        {
            $productCode = $itemList[$i]['code'];

         	$productName = LocalizationObj::getLocaleString($itemList[$i]['name'], $gSession['browserlanguagecode'], true);

            if ($pResultArray['productcode'] == $productCode)
            {
                $optionSelected = 'selected';
            }
            else
            {
                $optionSelected = '';
            }

            if ($productCode != '')
            {
                $displayName = $productCode . ' - ' . UtilsObj::encodeString($productName,true);
            }
            else
            {
                $displayName = $productName;
            }

            $itemListHTML .= '<option ' . $optionSelected. ' value="' . $productCode . '">' . $displayName;

            $itemListArray[] = array('id' => $productCode, 'name' => $displayName);

        }

        $itemListHTML .= '<option value=->-';

        $itemListHTML .= '</select>';

        $optionSelected = $pResultArray['productcode'];

        $smarty->assign('productlist', $itemListArray);
        $smarty->assign('selectedProduct', $optionSelected);

        $productGroupsArray = $pResultArray['productgroups'];
        $productGroupsCount = count($productGroupsArray);
        $productGroupsArrayToSend = array();
        
        for ($i = 0; $i < $productGroupsCount; $i++)
        {
            $theProductGroup = $productGroupsArray[$i];

            $productGroupsArrayToSend[] = array('id' => $theProductGroup['id'], 'name' => $theProductGroup['name']);
        }

        $smarty->assign('productgroupslist', $productGroupsArrayToSend);
        $smarty->assign('selectedproductgroup', $pResultArray['productgroupid']);

        if ($pResultArray['productgroupid'] != 0)
        {
            $smarty->assign('selectedproductsradio', 'PRODUCTGROUP');
        }
        elseif($pResultArray['productcode'] != '')
        {
            $smarty->assign('selectedproductsradio', 'PRODUCT');
        }
        else
        {
            $smarty->assign('selectedproductsradio', 'ALL');
        }

        // setup the license key group list
        $itemList = $pResultArray['groups'];
        array_unshift($itemList, '');
        $itemCount = count($itemList);
        $itemGroupArray = array();
        for ($i = 0; $i < $itemCount; $i++)
        {
            $groupCode = $itemList[$i];

            if ($groupCode != '')
            {
                $displayName = $groupCode;
            }
            else
            {
                $displayName = $smarty->get_config_vars('str_LabelAll');
            }

            //$itemListHTML .= '<option ' . $optionSelected. ' value="' . $groupCode . '">' . $displayName;
            $itemGroupArray[] = array('id' => $groupCode, 'name' => $displayName);
        }
        //$itemListHTML .= '</select>';
        $smarty->assign('grouplist', $itemGroupArray);
        $smarty->assign('selectedLicenseCode', $pResultArray['groupcode']);

        // setup the repeat types list
        $repeatTypeListArray = array();
        $repeatTypeListArray[] = array('id' => 'SINGLE', 'name' => $smarty->get_config_vars('str_LabelRepeatTypeSINGLE'));
        $repeatTypeListArray[] = array('id' => 'MULTI', 'name' => $smarty->get_config_vars('str_LabelRepeatTypeMULTI'));
        $repeatTypeListArray[] = array('id' => 'MULTIONCECUSTOMER', 'name' => $smarty->get_config_vars('str_LabelRepeatTypeMULTIONCECUSTOMER'));
        $repeatTypeListArray[] = array('id' => 'MULTIONCEKEY', 'name' => $smarty->get_config_vars('str_LabelRepeatTypeMULTIONCEKEY'));
        $smarty->assign('repeattypelist', $repeatTypeListArray);
        $smarty->assign('repeattypecode', $pResultArray['repeattype']);

        // setup the discount section list
        $discountSectionListArray = array();
        $discountSectionListArray[] = array('id' => 'PRODUCT', 'name' => $smarty->get_config_vars('str_LabelDiscountSectionPRODUCT'));
        $discountSectionListArray[] = array('id' => 'SHIPPING', 'name' => $smarty->get_config_vars('str_LabelDiscountSectionSHIPPING'));
        $discountSectionListArray[] = array('id' => 'TOTAL', 'name' => $smarty->get_config_vars('str_LabelDiscountSectionTOTAL'));
        $smarty->assign('discountsectionlist', $discountSectionListArray);
        $smarty->assign('discountsectioncode', $pResultArray['discountsection']);

        // setup the discount type list
        $discountTypeListArray = array();
        $discountTypeListArray[] = array('id' => 'VALUESET', 'name' => $smarty->get_config_vars('str_LabelDiscountTypeVALUESET'));
        $discountTypeListArray[] = array('id' => 'VALUE', 'name' => $smarty->get_config_vars('str_LabelDiscountTypeVALUE'));
        $discountTypeListArray[] = array('id' => 'PERCENT', 'name' => $smarty->get_config_vars('str_LabelDiscountTypePERCENT'));
        $discountTypeListArray[] = array('id' => 'FOC', 'name' => $smarty->get_config_vars('str_LabelDiscountTypeFOC'));
        $discountTypeListArray[] = array('id' => 'BOGOF', 'name' => $smarty->get_config_vars('str_LabelDiscountTypeBOGOF'));
        $discountTypeListArray[] = array('id' => 'BOGPOFF', 'name' => $smarty->get_config_vars('str_LabelDiscountTypeBOGPOFF'));
        $discountTypeListArray[] = array('id' => 'BOGVOFF', 'name' => $smarty->get_config_vars('str_LabelDiscountTypeBOGVOFF'));
        $smarty->assign('discounttypelist', $discountTypeListArray);
        $smarty->assign('discounttypecode', $pResultArray['discounttype']);
        $smarty->assign('discountvalue', UtilsObj::formatNumber($pResultArray['discountvalue'], 2));


        // Create a set of options to select how the product voucher should be applied to the order
        $discountApplicationMethodListArray = array();
        $discountApplicationMethodListArray[] = array('id' => TPX_VOUCHER_APPLY_EACH_MATCHING_PRODUCT, 'name' => $smarty->get_config_vars('str_LabelDiscountMethodMATCHINGPRODUCT'));
        $discountApplicationMethodListArray[] = array('id' => TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER, 'name' => $smarty->get_config_vars('str_LabelDiscountMethodSPREADORDER'));
        $discountApplicationMethodListArray[] = array('id' => TPX_VOUCHER_APPLY_LOWEST_PRICED, 'name' => $smarty->get_config_vars('str_LabelDiscountMethodLOWEST'));
        $discountApplicationMethodListArray[] = array('id' => TPX_VOUCHER_APPLY_HIGHEST_PRICED, 'name' => $smarty->get_config_vars('str_LabelDiscountMethodHIGHEST'));
        $smarty->assign('discountapplicationmethodlist', $discountApplicationMethodListArray);
        $smarty->assign('discountapplicationmethodcode', $pResultArray['applicationmethod']);

        $smarty->assign('discountapplytoqty', $pResultArray['applytoqty']);

        $dataList = array();
        $langList = array();

        $langs = array();

        $languageList = explode(',', $smarty->get_config_vars('str_LanguageList'));

        foreach($languageList as $lang)
        {
            $lang = trim($lang);
            $charPos = strpos($lang, ' ');
            $langs[substr($lang, 0, $charPos)] = substr($lang, $charPos + 1);
        }

        if (strlen($pResultArray['name'])>0)
        {
            $localizedStringList = explode('<p>', $pResultArray['name']);

            foreach ($localizedStringList as $name)
            {
                $charPos = strpos($name, ' ');
                $code = substr($name, 0, $charPos);
                $name = UtilsObj::encodeString(substr($name, $charPos + 1),false);
                $langname = $langs[$code];

                $dataList[] = '["'.$code.'","'.$langname.'","'.$name.'"]';

                unset($langs[$code]);
            }
        }

        foreach($langs as $langCode=>$langName)
            $langList[] = '["'.$langCode.'","'.$langName.'"]';

        $smarty->assign('dataList', '['.join(',', $dataList).']');
        $smarty->assign('langList', '['.join(',', $langList).']');

        $description = isset($pResultArray['description']) ? $pResultArray['description'] : '';
        $smarty->assign('description', UtilsObj::ExtJSEscape($description));

        $voucherTypes = "[['" . TPX_VOUCHER_TYPE_DISCOUNT . "','" . UtilsObj::ExtJSEscape(LocalizationObj::getConstantName($smarty, TPX_VOUCHER_TYPE_DISCOUNT, 'VOUCHERTYPE')) ."'],";
        $voucherTypes .= "['" . TPX_VOUCHER_TYPE_PREPAID . "','" . UtilsObj::ExtJSEscape(LocalizationObj::getConstantName($smarty, TPX_VOUCHER_TYPE_PREPAID, 'VOUCHERTYPE')) ."']";

        if ($gConstants['optionwscrp'])
        {
        	$voucherTypes .= ",['" . TPX_VOUCHER_TYPE_SCRIPT . "','" . UtilsObj::ExtJSEscape(LocalizationObj::getConstantName($smarty, TPX_VOUCHER_TYPE_SCRIPT, 'VOUCHERTYPE')) ."']";
        }

        $voucherTypes .= "]";

        $smarty->assign('voucherTypes', $voucherTypes);
		$smarty->assign('voucherTypeDiscount', TPX_VOUCHER_TYPE_DISCOUNT);
		$smarty->assign('voucherTypePrepaid', TPX_VOUCHER_TYPE_PREPAID);
		$smarty->assign('voucherTypeScript', TPX_VOUCHER_TYPE_SCRIPT);

        if ($pResultArray['isactive'] == 1)
        {
            $smarty->assign('activechecked', 'checked');
        }
        else
        {
             $smarty->assign('activechecked', '');
        }

        $smarty->assign('dateformat', LocalizationObj::getLocaleFormatValue('str_DateTimeFormat'));

        if (substr($pError, 0, 4) == 'str_')
        {
			$message = $smarty->get_config_vars($pError);
			if (strpos($message, '^0') !== false)
			{
				$message = str_replace('^0', $pResultArray['resultparam'], $message);
			}
            $smarty->assign('error', $message);
        }
        else
        {
            $smarty->assign('error', $pError);
        }
        $smarty->assign('actionbutton', $smarty->get_config_vars($pActionButtonName));
        $smarty->assign('destaction', $pDestAction);

        $smarty->assign('type', $pResultArray['type']);
        $smarty->assign('sellprice', UtilsObj::formatNumber($pResultArray['sellprice'],2));
        $smarty->assign('agentfee', UtilsObj::formatNumber($pResultArray['agentfee'],2));
        $smarty->assign('licenseevalue', UtilsObj::formatNumber($pResultArray['licenseevalue'],2));
        $smarty->assign('minimumordervalue', UtilsObj::formatNumber($pResultArray['minimumordervalue'], 2));

        if ($pResultArray['minordervalueincludesshipping'] == 1)
        {
            $smarty->assign('minordervalueincludesshipping', 'checked');
        }
        else
        {
             $smarty->assign('minordervalueincludesshipping', '');
        }

        if ($pResultArray['minordervalueincludestax'] == 1)
        {
            $smarty->assign('minordervalueincludestax', 'checked');
        }
        else
        {
             $smarty->assign('minordervalueincludestax', '');
        }
        
        if ($pTemplate == '')
        {
            $smarty->displayLocale('admin/vouchers/voucheredit.tpl');
        }
        else
        {

            $smarty->displayLocale($pTemplate);
        }
    }

	static function displayAdd($pResultArray)
	{
	    $pResultArray['displayMode'] = 0; // add or edit window
	    $pResultArray['voucherusedinorder'] = 0; // not used in order so can change company
	    $pResultArray['id'] = 0;
	    $pResultArray['owner'] = '';
	    $pResultArray['promotionid'] = $pResultArray['promotionId'];
	    $pResultArray['promotioncode'] = '';
        $pResultArray['code'] = $pResultArray['code'];
        $pResultArray['name'] = '';

        if ($pResultArray['promotionid'] == 0)
        {
            $pResultArray['startdate'] = '2000-01-01';
            $pResultArray['enddate'] = date('Y-m-d');

        }
        else
        {
            $pResultArray['startdate'] = $pResultArray['promotion']['startdate'];
            $pResultArray['enddate'] = $pResultArray['promotion']['enddate'];
        }

        $pResultArray['productcode'] = '';
        $pResultArray['productgroupid'] = 0;
        $pResultArray['groupcode'] = '';
        $pResultArray['userid'] = 0;
        $pResultArray['minqty'] = 1;
        $pResultArray['maxqty'] = 9999;
        $pResultArray['lockqty'] = 0;
        $pResultArray['repeattype'] = 'SINGLE';
        $pResultArray['discountsection'] = 'TOTAL';
        $pResultArray['discounttype'] = 'VALUE';
        $pResultArray['discountvalue'] = 0.00;
        $pResultArray['isactive'] = 1;
        $pResultArray['sellprice'] = 0.00;
        $pResultArray['agentfee'] = 0.00;
        $pResultArray['licenseevalue'] = 0.00;
        $pResultArray['type'] = TPX_VOUCHER_TYPE_DISCOUNT;
        $pResultArray['defaultdiscount'] = 0;
        $pResultArray['applicationmethod'] = TPX_VOUCHER_APPLY_EACH_MATCHING_PRODUCT;
        $pResultArray['applytoqty'] = 9999;
        $pResultArray['minimumordervalue'] = 0.00;
        $pResultArray['minordervalueincludesshipping'] = 0;
        $pResultArray['minordervalueincludestax'] = 0;


        self::displayEntry('str_TitleNewVoucher', $pResultArray, 'str_ButtonAdd', '', '', 'AdminVouchers');
    }

    static function displayCreate($pResultArray)
	{
	    $pResultArray['displayMode'] = 1; // create window
	    $pResultArray['voucherusedinorder'] = 0; // not used in order so can change company
	    $pResultArray['id'] = 0;
	    $pResultArray['owner'] = '';
	    $pResultArray['promotionid'] = $pResultArray['promotionid'];
	    $pResultArray['promotioncode'] = '';
        $pResultArray['code'] = '';
        $pResultArray['name'] = '';

        if ($pResultArray['promotionid'] == 0)
        {
            $pResultArray['startdate'] = '2000-01-01';
            $pResultArray['enddate'] = date('Y-m-d');
        }
        else
        {
            $pResultArray['startdate'] = $pResultArray['promotion']['startdate'];
            $pResultArray['enddate'] = $pResultArray['promotion']['enddate'];
        }

        $pResultArray['productcode'] = '';
        $pResultArray['productgroupid'] = 0;
        $pResultArray['groupcode'] = '';
        $pResultArray['userid'] = 0;
        $pResultArray['minqty'] = 1;
        $pResultArray['maxqty'] = 9999;
        $pResultArray['lockqty'] = 0;
        $pResultArray['repeattype'] = 'SINGLE';
        $pResultArray['discountsection'] = 'TOTAL';
        $pResultArray['discounttype'] = 'VALUE';
        $pResultArray['discountvalue'] = 0.00;
        $pResultArray['isactive'] = 1;
        $pResultArray['sellprice'] = 0.00;
        $pResultArray['agentfee'] = 0.00;
        $pResultArray['licenseevalue'] = 0.00;
        $pResultArray['type'] = TPX_VOUCHER_TYPE_PREPAID;
        $pResultArray['defaultdiscount'] = 0;
        $pResultArray['applicationmethod'] = TPX_VOUCHER_APPLY_EACH_MATCHING_PRODUCT;
        $pResultArray['applytoqty'] = 9999;
        $pResultArray['minimumordervalue'] = 0.00;
        $pResultArray['minordervalueincludesshipping'] = 0;
        $pResultArray['minordervalueincludestax'] = 0;

        self::displayEntry('str_TitleCreateVouchers', $pResultArray, 'str_ButtonCreate', '', 'admin/vouchers/voucheredit.tpl', 'AdminVouchers');
    }

    static function displayImport($pResultArray)
	{
	    $pResultArray['displayMode'] = 2; // import window
	    $pResultArray['voucherusedinorder'] = 0; // not used in order so can change company
	    $pResultArray['id'] = 0;
	    $pResultArray['owner'] = '';
	    $pResultArray['promotionid'] = $pResultArray['promotionid'];
	    $pResultArray['promotioncode'] = '';
        $pResultArray['code'] = '';
        $pResultArray['name'] = '';

        if ($pResultArray['promotionid'] == 0)
        {
            $pResultArray['startdate'] = '';
            $pResultArray['enddate'] = '';
        }
        else
        {
            $pResultArray['startdate'] = $pResultArray['promotion']['startdate'];
            $pResultArray['enddate'] = $pResultArray['promotion']['enddate'];
        }

        $pResultArray['productcode'] = '';
        $pResultArray['productgroupid'] = 0;
        $pResultArray['groupcode'] = '';
        $pResultArray['userid'] = 0;
        $pResultArray['minqty'] = 1;
        $pResultArray['maxqty'] = 9999;
        $pResultArray['lockqty'] = 0;
        $pResultArray['repeattype'] = 'SINGLE';
        $pResultArray['discountsection'] = 'TOTAL';
        $pResultArray['discounttype'] = 'VALUE';
        $pResultArray['discountvalue'] = 0.00;
        $pResultArray['isactive'] = 1;
        $pResultArray['sellprice'] = 0.00;
        $pResultArray['agentfee'] = 0.00;
        $pResultArray['licenseevalue'] = 0.00;
        $pResultArray['type'] = TPX_VOUCHER_TYPE_PREPAID;
        $pResultArray['defaultdiscount'] = 0;
        $pResultArray['applicationmethod'] = TPX_VOUCHER_APPLY_EACH_MATCHING_PRODUCT;
        $pResultArray['applytoqty'] = 9999;
        $pResultArray['minimumordervalue'] = 0.00;
        $pResultArray['minordervalueincludesshipping'] = 0;
        $pResultArray['minordervalueincludestax'] = 0;

        self::displayEntry('str_TitleCreateVouchers', $pResultArray, 'str_ButtonCreate', '', 'admin/vouchers/voucheredit.tpl', 'AdminVouchers');
    }

    static function voucherExport($pResultArray)
    {
        global $gSession;

        $smarty = SmartyObj::newSmarty('AdminVouchers');

        $itemCount = count($pResultArray);
        $fileName = 'Vouchers_';
        if(($itemCount > 0) && ($pResultArray[0]['promotioncode'] != ''))
        {
            $fileName .= $pResultArray[0]['promotioncode'] . '_';
        }
        $fileName .= date('d_M_Y_His');

		header('Content-Type: text/plain; charset=utf-8');
		header('Content-Disposition: Attachment; filename=' . $fileName . '.txt');
		header('Pragma: no-cache');
        header('Expires: 0');

        $separator = "\t";

        // write the file header

        echo $smarty->get_config_vars('str_LabelPromotionCode') . $separator;
        echo $smarty->get_config_vars('str_LabelCode') . $separator;
        echo $smarty->get_config_vars('str_LabelVoucherType') . $separator;
        echo $smarty->get_config_vars('str_LabelDefaultDiscount') . $separator;
        echo $smarty->get_config_vars('str_LabelName') . $separator;
        echo $smarty->get_config_vars('str_LabelDescription') . $separator;
        echo $smarty->get_config_vars('str_LabelStartDate') . $separator;
        echo $smarty->get_config_vars('str_LabelEndDate') . $separator;
        echo $smarty->get_config_vars('str_LabelGroupName') . $separator;
        echo $smarty->get_config_vars('str_LabelProductCode') . $separator;
        echo $smarty->get_config_vars('str_LabelProductName') . $separator;
        echo $smarty->get_config_vars('str_LabelLicenseKey') . $separator;
        echo $smarty->get_config_vars('str_LabelUserID') . $separator;
        echo $smarty->get_config_vars('str_LabelFirstName') . $separator;
        echo $smarty->get_config_vars('str_LabelLastName') . $separator;
        echo $smarty->get_config_vars('str_LabelEmailAddress') . $separator;
        echo $smarty->get_config_vars('str_LabelRepeatType') . $separator;
        echo $smarty->get_config_vars('str_LabelDiscountSection') . $separator;
        echo $smarty->get_config_vars('str_LabelDiscountType') . $separator;
        echo $smarty->get_config_vars('str_LabelDiscountValue'). $separator;
        echo $smarty->get_config_vars('str_LabelMinimumOrderValue'). $separator;
        echo $smarty->get_config_vars('str_LabelIncludesShipping'). $separator;
        echo $smarty->get_config_vars('str_LabelIncludesTax'). $separator;
        echo $smarty->get_config_vars('str_LabelSellPrice'). $separator;
        echo $smarty->get_config_vars('str_LabelAgentFee'). $separator;
        echo $smarty->get_config_vars('str_LabelLicenseeValue'). $separator;
        echo $smarty->get_config_vars('str_LabelDiscountMethod');
        echo "\n";

        for ($i = 0; $i < $itemCount; $i++)
        {
            $productCode = $pResultArray[$i]['productcode'];
            $groupCode = $pResultArray[$i]['groupcode'];
            $userID = $pResultArray[$i]['userid'];

            $repeatType = 'str_LabelRepeatType' . $pResultArray[$i]['repeattype'];
	        $discountSection = 'str_LabelDiscountSection' . $pResultArray[$i]['discountsection'];
	        $discountType = 'str_LabelDiscountType' . $pResultArray[$i]['discounttype'];

            if ($productCode == '')
            {
                $productCode = $smarty->get_config_vars('str_LabelAll');
            }

            if ($groupCode == '')
            {
                $groupCode = $smarty->get_config_vars('str_LabelAll');
            }

            if ($userID == 0)
            {
                $userID = '';
            }

            echo $pResultArray[$i]['promotioncode'] . $separator;
            echo $pResultArray[$i]['code'] . $separator;
            echo UtilsObj::ExtJSEscape(LocalizationObj::getConstantName($smarty, $pResultArray[$i]['type'] , 'VOUCHERTYPE')) . $separator;
            echo $smarty->get_config_vars($pResultArray[$i]['defaultdiscount']) . $separator;
            echo LocalizationObj::getLocaleString($pResultArray[$i]['name'], $gSession['browserlanguagecode'], true) . $separator;
            echo $pResultArray[$i]['description'] . $separator;
            echo $pResultArray[$i]['startdate'] . $separator;
            echo $pResultArray[$i]['enddate'] . $separator;
            echo $pResultArray[$i]['productgroupname'] . $separator;
            echo $productCode . $separator;
            echo LocalizationObj::getLocaleString($pResultArray[$i]['productname'], $gSession['browserlanguagecode'], true) . $separator;
            echo $groupCode . $separator;
            echo $userID . $separator;
            echo $pResultArray[$i]['usercontactfirstname'] . $separator;
            echo $pResultArray[$i]['usercontactlastname'] . $separator;
            echo $pResultArray[$i]['useremailaddress'] . $separator;
            echo $smarty->get_config_vars($repeatType) . $separator;
            echo $smarty->get_config_vars($discountSection) . $separator;
            echo $smarty->get_config_vars($discountType) . $separator;
            echo $pResultArray[$i]['discountvalue']. $separator;
            echo $pResultArray[$i]['minimumordervalue']. $separator;
            echo $pResultArray[$i]['minordervalueincludesshipping']. $separator;
            echo $pResultArray[$i]['minordervalueincludestax']. $separator;
            echo $pResultArray[$i]['sellprice']. $separator;
            echo $pResultArray[$i]['agentfee']. $separator;
            echo $pResultArray[$i]['licenseevalue']. $separator;
            echo UtilsObj::ExtJSEscape(LocalizationObj::getConstantName($smarty, $pResultArray[$i]['applicationmethod'] , 'VOUCHERAPPLYMETHOD')) . $separator;
            echo $pResultArray[$i]['applytoqty'];

            echo "\n";
        }
    }

}

?>