<?php

/**
* @class AdminAutoUpdate_view
*
* @version 3.0.0
* @since Version 1.0.0
* @author Kevin Gale
*
* @addtogroup AutoUpdate
* @{
*/
class AdminAutoUpdate_view
{

    static function initialize()
    {
        $smarty = SmartyObj::newSmarty('AdminAutoUpdate');
        $smarty->displayLocale('admin/autoupdate/autoupdateframe.tpl');
    }


    static function initialize2()
    {
        $smarty = SmartyObj::newSmarty('AdminAutoUpdate');
        $smarty->displayLocale('admin/autoupdate/autoupdate.tpl');
    }


	static function initializeApplication()
	{
	    global $gSession;
	    $smarty = SmartyObj::newSmarty('AdminAutoUpdateApplication');
	    $smarty->assign('companyCode', $gSession['userdata']['companycode']);
	    $smarty->displayLocale('admin/autoupdate/autoupdateapplication.tpl');
    }


	static function initializeProducts()
	{
        global $gSession;
        $smarty = SmartyObj::newSmarty('AdminAutoUpdateProducts');
        $smarty->assign('companyCode', $gSession['userdata']['companycode']);
        $smarty->displayLocale('admin/autoupdate/autoupdateproducts.tpl');
    }


	static function initializeApplicationFiles($type)
	{
        global $gSession;
        global $gConstants;
        $smarty = SmartyObj::newSmarty('AdminAutoUpdateApplicationFiles');
	    if ($type == TPX_APPLICATION_FILE_TYPE_MASK)
        {
            $title = $smarty->get_config_vars('str_AutoUpdateTitleMasks');
            $smarty->assign('optiondesol', ($gConstants['optiondesol'] ? true : false));
        }
        else if ($type == TPX_APPLICATION_FILE_TYPE_BACKGROUND)
        {
            $title = $smarty->get_config_vars('str_AutoUpdateTitleBackgrounds');
            $smarty->assign('optiondesol', ($gConstants['optiondesol'] ? true : false));
        }
        else if ($type == TPX_APPLICATION_FILE_TYPE_PICTURE)
        {
            $title = $smarty->get_config_vars('str_AutoUpdateTitleScrapbook');
            $smarty->assign('optiondesol', ($gConstants['optiondesol'] ? true : false));
        }
        else if ($type == TPX_APPLICATION_FILE_TYPE_FRAME)
        {
            $title = $smarty->get_config_vars('str_AutoUpdateTitleFrames');
            $smarty->assign('optiondesol', false);
        }

	    $smarty->assign('title', $title);
        $smarty->assign('filestype', $type);
        $smarty->assign('companyCode', $gSession['userdata']['companycode']);
        $smarty->assign('optiondesdt', ($gConstants['optiondesdt'] ? true : false));
        $smarty->displayLocale('admin/autoupdate/autoupdateapplicationfiles.tpl');
    }


	static function initializeLicenseKeys()
	{
        global $gConstants;
        global $gSession;

        $smarty = SmartyObj::newSmarty('AdminAutoUpdateLicenseKeys');
        $smarty->assign('companyCode', $gSession['userdata']['companycode']);
        $smarty->assign('optiondesdt', ($gConstants['optiondesdt'] ? true : false));
        $smarty->assign('optiondesol', ($gConstants['optiondesol'] ? true : false));

	    $smarty->displayLocale('admin/autoupdate/autoupdatelicensekeys.tpl');
    }

    static function listProducts($pResultArray)
    {
    	$smarty = SmartyObj::newSmarty('AdminAutoUpdateProducts');
    	$itemList = $pResultArray['productlist'];
        $totalItemCount = count($itemList);
    	$productHtml = '';
    	$sortedArray = array();

    	for ($i = 0; $i < $totalItemCount; $i ++)
    	{
    		$theItem = $itemList[$i];

    		if (!isset($sortedArray[$theItem['company']][$theItem['collectioncode']]))
    		{
	    		$sortedArray[$theItem['company']][$theItem['collectioncode']]['id'] = $theItem['id'];
	    		$sortedArray[$theItem['company']][$theItem['collectioncode']]['collectioncode'] = $theItem['collectioncode'];
	    		$sortedArray[$theItem['company']][$theItem['collectioncode']]['name'] = $name = LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $theItem['name'], 'black');
	    		$sortedArray[$theItem['company']][$theItem['collectioncode']]['products'] = array();
	    		$sortedArray[$theItem['company']][$theItem['collectioncode']]['products'][] = $theItem['productcode'];
	    		$sortedArray[$theItem['company']][$theItem['collectioncode']]['version'] = $theItem['datemodified'];
	    		$sortedArray[$theItem['company']][$theItem['collectioncode']]['isactive'] = $theItem['isactive'];
	    		$sortedArray[$theItem['company']][$theItem['collectioncode']]['priority'] = $theItem['updatepriority'];
	    		$sortedArray[$theItem['company']][$theItem['collectioncode']]['company'] = $theItem['company'];

    		}
    		else
    		{
    			$sortedArray[$theItem['company']][$theItem['collectioncode']]['products'][] = $theItem['productcode'];
    		}

    	}

    	$counter = 0;
		$string  = '';
  		foreach ($sortedArray as $productCollection)
  		{
            foreach( $productCollection as $value){
                $counter ++;

                $string .= '[';

                foreach ($value as $item => $value)
                {

                    if ($item == 'products')
                    {
                        $productItemCount = count($value);

                        $productHtml = '';

                        for ($i = 0; $i < $productItemCount; $i++)
                        {
                            $productHtml .= $value[$i] . '<br>';
                        }

                        $string .= "'" . $productHtml . "',";
                    }
                    else
                    {
                        $string .= "'" . $value . "',";
                    }
                }

                $string = substr($string, 0, -1);
                $string .= '],';
            }
  		}

  		$string = substr($string, 0, -1);
		$string .= ']';

		$productString = '[';
		$productString .= '[' . $counter . '],' . $string;

		echo $productString;
    }

    static function editLicenseKeyDisplay($resultArray)
    {
        global $gSession;

        $smarty = SmartyObj::newSmarty('AdminAutoUpdateLicenseKeys');
        $licencekeycurrencycode = $resultArray['currencycode'];

		$smarty->assign('session', $gSession['ref']);
		$smarty->assign('strictmode', '1');
        $smarty->assign('id', $resultArray['recordid']);
        $smarty->assign('groupcode', $resultArray['groupcode']);
        $smarty->assign('login', UtilsObj::encodeString($resultArray['login'],true));
        $smarty->assign('password', '**UNCHANGED**');
        $smarty->assign('contactfname', UtilsObj::encodeString($resultArray['contactfirstname'], true));
        $smarty->assign('contactlname', UtilsObj::encodeString($resultArray['contactlastname'], true));
        $smarty->assign('companyname', UtilsObj::encodeString($resultArray['companyname'], true));
        if(isset($resultArray['address1']))
        {
            $smarty->assign('address1', UtilsObj::encodeString($resultArray['address1'], true));
        }
        if(isset($resultArray['address2']))
        {
            $smarty->assign('address2', UtilsObj::encodeString($resultArray['address2'], true));
        }
        if(isset($resultArray['address3']))
        {
            $smarty->assign('address3', UtilsObj::encodeString($resultArray['address3'], true));
        }
        if(isset($resultArray['address4']))
        {
            $smarty->assign('address4', UtilsObj::encodeString($resultArray['address4'], true));
        }
        if(isset($resultArray['add41']))
        {
            $smarty->assign('add41', UtilsObj::encodeString($resultArray['add41'], true));
        }
        if(isset($resultArray['add42']))
        {
            $smarty->assign('add42', UtilsObj::encodeString($resultArray['add42'], true));
        }
        if(isset($resultArray['add43']))
        {
            $smarty->assign('add43', UtilsObj::encodeString($resultArray['add43'], true));
        }
        $smarty->assign('city', UtilsObj::encodeString($resultArray['city'], true));
        $smarty->assign('state', UtilsObj::encodeString($resultArray['state'], true));
        $smarty->assign('county', UtilsObj::encodeString($resultArray['county'], true));
		$smarty->assign('country', $resultArray['countrycode']);
		$smarty->assign('regioncode', $resultArray['regioncode']);
        $smarty->assign('postcode', UtilsObj::encodeString($resultArray['postcode'], true));
        $smarty->assign('telephonenumber', UtilsObj::encodeString($resultArray['telephonenumber']));
        $smarty->assign('email', UtilsObj::encodeString($resultArray['emailaddress']));
        $smarty->assign('useremaildestination', $resultArray['useremaildestination']);
        $smarty->assign('registeredtaxnumbertype', $resultArray['registeredtaxnumbertype']);
		$smarty->assign('registeredtaxnumber',  UtilsObj::encodeString($resultArray['registeredtaxnumber'], true));

        $itemList = $resultArray['webbrandinglist'];
        $itemCount = count($itemList);

        for ($i = 0; $i < $itemCount; $i++)
        {
            $brandCode = $itemList[$i]['code'];

            if ($brandCode == '')
            {
            	$brandCodeDisplay = $smarty->get_config_vars('str_LabelDefault');
            }
            else
            {
            	$brandCodeDisplay = $brandCode;
            }

            $brandListBuf[] = '["'.$brandCode.'","'.$brandCodeDisplay . " - " . $itemList[$i]['applicationname'].'"]';
        }
        $webBrandingList = '['.join(',',$brandListBuf) .']';

        $smarty->assign('webbrandinglist', $webBrandingList);
        $smarty->assign('webbrandcode', $resultArray['webbrandcode']);
		$smarty->assign('usedefaultcurrency', $resultArray['usedefaultcurrency']);

        if ($resultArray['usedefaultpaymentmethods'] == 1)
        {
            $smarty->assign('usedefaultpaymentmethodschecked', 'checked');
        }
        else
        {
             $smarty->assign('usedefaultpaymentmethodschecked', '');
        }
        $userPaymentMethodsList = explode(',', $resultArray['paymentmethods']);

		$itemList = $resultArray['paymentmethodslist'];
        $itemCount = count($itemList);
        for ($i = 0; $i < $itemCount; $i++)
        {
            $paymentMethodCode = $itemList[$i]['code'];
            if (in_array($paymentMethodCode, $userPaymentMethodsList))
            {
                $optionSelected = ',checked:true';
            }
            else
            {
                $optionSelected = ',checked:false';
            }
            $paymentMethodsHTML[] = "new Ext.form.Checkbox({boxLabel: '".UtilsObj::encodeString(LocalizationObj::getLocaleString($itemList[$i]['name'], '', true))."', name:'".$paymentMethodCode."', id: '".'paymentmethod'.$i."' ".$optionSelected."})";
        }
        $paymentMethodsHTML = join(';;',$paymentMethodsHTML);

        $smarty->assign('paymentmethodshtml', $paymentMethodsHTML);
        $smarty->assign('paymentmethodcount', $itemCount);

        // default payment methods
        $defaultBrand = DatabaseObj::getBrandingFromCode('');
        $smarty->assign('defaultpaymentmethods', $defaultBrand['paymentmethods']);

        // default payment methods from constants
        $javascriptVar = "brandingPaymentMethodList = new Array();\n";
		$javascriptVar .= "brandingPaymentMethodList[''] = '" . $defaultBrand['paymentmethods'] ."';\n";

		// payment methods per brand
		foreach ($resultArray['webbrandinglist'] as $a)
		{
			$javascriptVar .= "brandingPaymentMethodList['" . $a['code'] . "'] = '" . $a['paymentmethods'] ."';\n";
		}
		$smarty->assign('brandingpaymentmethodlist', $javascriptVar);

        if ($resultArray['useaddressforbilling'] == 1)
        {
            $smarty->assign('useaddressforbillingchecked', 'checked');
        }
        else
        {
             $smarty->assign('useaddressforbillingchecked', '');
        }

        if ($resultArray['cancreateaccounts'] == 1)
        {
            $smarty->assign('cancreateaccountschecked', 'checked');
        }
        else
        {
             $smarty->assign('cancreateaccountschecked', '');
        }

        if ($resultArray['useaddressforshipping'] == 1)
        {
            $smarty->assign('useaddressforshippingchecked', 'checked');
        }
        else
        {
             $smarty->assign('useaddressforshippingchecked', '');
        }

        if ($resultArray['canmodifyshippingaddress'] == 1)
        {
            $smarty->assign('canmodifyshippingaddresschecked', 'checked');
        }
        else
        {
             $smarty->assign('canmodifyshippingaddresschecked', '');
        }

        if ($resultArray['canmodifybillingaddress'] == 1)
        {
            $smarty->assign('canmodifybillingaddresschecked', 'checked');
        }
        else
        {
             $smarty->assign('canmodifybillingaddresschecked', '');
        }

        if ($resultArray['canmodifyshippingcontactdetails'] == 1)
        {
            $smarty->assign('canmodifyshippingcontactdetailschecked', 'checked');
        }
        else
        {
             $smarty->assign('canmodifyshippingcontactdetailschecked', '');
        }

        // order option
        $smarty->assign('orderfrompreview', $resultArray['orderfrompreview']);

        if ($resultArray['showpriceswithtax'] == 1)
        {
            $smarty->assign('showpriceswithtaxchecked', 'checked');
        }
        else
        {
             $smarty->assign('showpriceswithtaxchecked', '');
        }

        if ($resultArray['showtaxbreakdown'] == 1)
        {
            $smarty->assign('showtaxbreakdownchecked', 'checked');
        }
        else
        {
             $smarty->assign('showtaxbreakdownchecked', '');
        }

        if ($resultArray['showzerotax'] == 1)
        {
            $smarty->assign('showzerotaxchecked', 'checked');
        }
        else
        {
             $smarty->assign('showzerotaxchecked', '');
        }

        if ($resultArray['showalwaystaxtotal'] == 1)
        {
            $smarty->assign('showalwaystaxtotalchecked', 'checked');
        }
        else
        {
             $smarty->assign('showalwaystaxtotalchecked', '');
        }

        if ($resultArray['isactive'] == 1)
        {
            $smarty->assign('activechecked', 'checked');
        }
        else
        {
             $smarty->assign('activechecked', '');
        }

        $constantsArray = DatabaseObj::getConstants();
        $itemList = $resultArray['currencylist'];
        $itemCount = count($itemList);
        $defaultcurrency = '';

        for($i = 0; $i < $itemCount; $i++)
        {
            $currencyCode = $itemList[$i]['code'];
            if ($constantsArray['defaultcurrencycode'] == $currencyCode)
            {
                $defaultcurrency = UtilsObj::encodeString(LocalizationObj::getLocaleString($itemList[$i]['name'], '', true), true);
            }
            $curListBuf[] = '["'.$currencyCode.'","'.$currencyCode . ' - ' . UtilsObj::encodeString(LocalizationObj::getLocaleString($itemList[$i]['name'], '', true), true) .'"]';
        }
        $currencyList = '['.join(',',$curListBuf) .']';

        $smarty->assign('currencylist', $currencyList);
        $smarty->assign('currencySelected', $licencekeycurrencycode);
        $smarty->assign('defaultcurrency', $defaultcurrency);

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

        $smarty->assign('taxcode',  $resultArray['taxcode']);
        $smarty->assign('shippingtaxcode', $resultArray['shippingtaxcode']);

        $smarty->assign('taxcodelist', $taxRatesArray);
        $smarty->assign('shippingtaxcodelist', $taxRatesArray);

        $error = $resultArray['result'];
        if (substr($error, 0, 4) == 'str_')
        {
            SmartyObj::replaceParams($smarty, $error, $resultArray['resultparam']);
            $smarty->assign('error', $smarty->get_template_vars($error));
        }
        else
        {
            $smarty->assign('error', $error);
        }

        $smarty->assign('dateformat', LocalizationObj::getLocaleFormatValue('str_DateFormat'));

        // Calculate the difference in days.
		$date1 = strtotime($resultArray['designersplashscreenstartdate']);
		$date2 = strtotime('2000-01-01');

		// Which is the latest?
	    if ($date1 < $date2) {
	      $resultArray['designersplashscreenstartdate'] = '2000-01-01';
	    }

        if ($resultArray['designersplashscreenenddate'] == '0000-00-00 00:00:00' || $resultArray['designersplashscreenenddate'] == '')
        {
        	$resultArray['designersplashscreenenddate'] = date('Y-m-d');
        }

        // Calculate the difference in days.
		$date1 = strtotime($resultArray['designerbannerstartdate']);
		$date2 = strtotime('2000-01-01');

		// Which is the latest?
	    if ($date1 < $date2) {
	      $resultArray['designerbannerstartdate'] = '2000-01-01';
	    }

        if ($resultArray['designerbannerenddate'] == '0000-00-00 00:00:00' || $resultArray['designerbannerenddate'] == '')
        {
        	$resultArray['designerbannerenddate'] = date('Y-m-d');
        }

        $smarty->assign('splashstartdate', LocalizationObj::formatLocaleDate($resultArray['designersplashscreenstartdate']));
        $smarty->assign('splashenddate', LocalizationObj::formatLocaleDate($resultArray['designersplashscreenenddate']));

        $smarty->assign('bannerstartdate', LocalizationObj::formatLocaleDate($resultArray['designerbannerstartdate']));
        $smarty->assign('bannerenddate', LocalizationObj::formatLocaleDate($resultArray['designerbannerenddate']));

		$smarty->assign('splashScreenAssetID',  $resultArray['designersplashscreenassetid']);
		$smarty->assign('bannerAssetID',  $resultArray['designerbannerassetid']);

		$smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_NA', TPX_REGISTEREDTAXNUMBERTYPE_NA);
		$smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL', TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL);
		$smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE', TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE);

        $smarty->assign('tablewidth', 650);

        $guestWorkflowData = array
					(
						array('id' => TPX_GUESTWORKFLOWMODE_DISABLED, 'name' =>  $smarty->get_config_vars('str_LabelDisabled')),
						array('id' => TPX_GUESTWORKFLOWMODE_OFF, 'name' => $smarty->get_config_vars('str_LabelOff')),
						array('id' => TPX_GUESTWORKFLOWMODE_PROMPT, 'name' => $smarty->get_config_vars('str_LabelPrompt')),
						array('id' => TPX_GUESTWORKFLOWMODE_AUTOMATIC, 'name' => $smarty->get_config_vars('str_LabelAutomatic'))
					);

        $smarty->assign('guestworkflowdata', $guestWorkflowData);
        $smarty->assign('guestworkflowmode', $resultArray['onlinedesignerguestworkflowmode']);
        $smarty->assign('guestworkmode', 0);

        $smarty->assign('allowimagescalingbefore', $resultArray['allowimagescalingbefore']);
		$smarty->assign('imagescalingbefore', 0);
		$smarty->assign('imagescalingbeforeenabled', '');
		$smarty->assign('usedefaultimagescalingbefore', '');

        if ($resultArray['allowimagescalingbefore'])
        {
            $smarty->assign('imagescalingbefore', $resultArray['imagescalingbefore']);

            if ($resultArray['imagescalingbeforeenabled'] == 1)
            {
                $smarty->assign('imagescalingbeforeenabled', 'checked');
            }

            if ($resultArray['usedefaultimagescalingbefore'] == 1)
            {
                $smarty->assign('usedefaultimagescalingbefore', 'checked');
            }
        }

        $smarty->assign('imagescalingafter', $resultArray['imagescalingafter']);

        if ($resultArray['usedefaultimagescalingafter'] == 1)
        {
            $smarty->assign('usedefaultimagescalingafter', 'checked');
        }
        else
        {
             $smarty->assign('usedefaultimagescalingafter', '');
        }

        if ($resultArray['imagescalingafterenabled'] == 1)
        {
            $smarty->assign('imagescalingafterenabled', 'checked');
        }
        else
        {
             $smarty->assign('imagescalingafterenabled', '');
        }

        $imageScaling = array();

        // image scaling methods per brand
        foreach ($resultArray['webbrandinglist'] as $a)
        {
            $key = $a['code'];

            if ($key == '')
            {
                $key = '__DEFAULT__';
            }

            $imageScaling[$key] = array(
                                'before'=>array(
                                                'enabled' => $a['imagescalingbeforeenabled'],
                                                'value' => $a['imagescalingbefore']
                                                ),
                                'after'=>array(
                                                'enabled' => $a['imagescalingafterenabled'],
                                                'value' => $a['imagescalingafter']
                                            )
                                );
        }

        $smarty->assign('imagescalingjs', json_encode($imageScaling));

        $smarty->assign('shufflelayout', $resultArray['shufflelayout']);

        if ($resultArray['usedefaultshufflelayout'] == 1)
        {
            $smarty->assign('usedefaultshufflelayout', 'checked');
        }
        else
        {
            $smarty->assign('usedefaultshufflelayout', '');
        }

        if ($resultArray['showshufflelayoutoptions'] == 1)
        {
            $smarty->assign('showshufflelayoutoptions', 'checked');
        }
        else
        {
            $smarty->assign('showshufflelayoutoptions', '');
        }

        $layoutArray = array();

        // shuffle per brand
        foreach ($resultArray['webbrandinglist'] as $a)
        {
            $key = $a['code'];

            if ($key == '')
            {
                $key = '__DEFAULT__';
            }

            $layoutArray[$key] = array(
                                'shuffle' => array(
                                                    'showoptions' => $a['showshufflelayoutoptions'],
                                                    'value' => $a['shufflelayout']
                                                )
                                );
        }

        $smarty->assign('layoutsjs', json_encode($layoutArray));

		// Online editor mode.
		$smarty->assign('usedefaultonlineeditormode', $resultArray['usedefaultonlineeditormode']);
		$smarty->assign('onlineeditormode', $resultArray['onlineeditormode']);

		if ($resultArray['enableswitchingeditor'] == 1)
        {
            $smarty->assign('enableswitchingeditor', 'checked');
        }
        else
        {
             $smarty->assign('enableswitchingeditor', '');
        }

		$onlineEditorSettingsArray = array();
		foreach ($resultArray['webbrandinglist'] as $brand)
		{
			$key = $brand['code'];

			// If the key is empty we force default to use it as default brand in template file.
			if ($key == '')
			{
				$key = '__DEFAULT__';
			}

			$onlineEditorSettingsArray[$key] = array(
				'onlineeditormode' => $brand['onlineeditormode'],
				'enableswitchingeditor' => $brand['enableswitchingeditor']
			);
		}
		$smarty->assign('onlineeditorsettings', json_encode($onlineEditorSettingsArray));

        // Logo link urls.
		$smarty->assign('onlinedesignerlogolinkurl', $resultArray['onlinedesignerlogolinkurl']);
		$onlineDesignerLogoLinks = array();

        foreach ($resultArray['webbrandinglist'] as $a)
        {
            $key = $a['code'];

            if ($key == '')
            {
                $key = '__DEFAULT__';
            }

			// process the localized string
			$codesJavaScript = 'new Array(';
			$namesJavaScript = 'new Array(';

			$localizedStringList = explode('<p>', $resultArray['onlinedesignerlogolinktooltip']);
			$localizedCount = count($localizedStringList);

			if ($localizedStringList[$localizedCount - 1] == '')
			{
				$localizedCount--;
			}

			for ($i = 0; $i < $localizedCount; $i++)
			{
				// split each language item into its code and name
				$charPos = strpos($localizedStringList[$i], ' ');
				$localizedItemCode = substr($localizedStringList[$i], 0, $charPos);
				$localizedItemString = substr($localizedStringList[$i], $charPos + 1);
				$localizedItemString = UtilsObj::encodeString($localizedItemString, true);
				$codesJavaScript .= '"' . $localizedItemCode . '"';
				if ($i < ($localizedCount -1))
				{
					$codesJavaScript .= ',';
				}

				$namesJavaScript .= '"' . $localizedItemString . '"';
				if ($i < ($localizedCount -1))
				{
					$namesJavaScript .= ',';
				}
			}
			$codesJavaScript .= ');';
			$namesJavaScript .= ');';

            $onlineDesignerLogoLinks[$key] = array(
				'before' => array(
					'url' => $a['onlinedesignerlogolinkurl'],
					'tooltip' => array(
						'string' => $resultArray['onlinedesignerlogolinktooltip'],
						'codes' => $codesJavaScript,
						'names' => $namesJavaScript
					)
				),
				'after' => array(
					'url' => $a['onlinedesignerlogolinkurl'],
					'tooltip' => array(
						'string' => $resultArray['onlinedesignerlogolinktooltip'],
						'codes' => $codesJavaScript,
						'names' => $namesJavaScript
					)
				)
			);
        }

        $smarty->assign('onlinedesignerlogolinkurlbrands', json_encode($onlineDesignerLogoLinks));

		if ($resultArray['usedefaultonlinedesignerlogolinkurl'] == 1)
		{
			$smarty->assign('usedefaultlogolinkurlchecked', 'checked');
		}
		else
		{
			$smarty->assign('usedefaultlogolinkurlchecked', '');
		}

		$smarty->assign('onlinedesignerlogolinktooltip', $resultArray['onlinedesignerlogolinktooltip']);

		$sizeAndPositionBrandSettings = array();
		foreach ($resultArray['webbrandinglist'] as $brand)
		{
			$key = $brand['code'];

			if ($key == '')
			{
				$key = '__DEFAULT__';
			}

			$sizeAndPositionBrandSettings[$key] = array(
				'unitID' => $brand['sizeandpositionmeasurementunits']
			);
		}

		$measurementUnits = array(
			array(TPX_COORDINATE_SCALE_INCHES, $smarty->get_config_vars('str_MeasurementUnitInches')),
			array(TPX_COORDINATE_SCALE_MILLIMETRES, $smarty->get_config_vars('str_MeasurementUnitMillimetres')),
			array(TPX_COORDINATE_SCALE_CENTIMETRES, $smarty->get_config_vars('str_MeasurementUnitCentimetres'))
		);

		$smarty->assign('measurementunitoptions', json_encode($measurementUnits));
		$smarty->assign('sizeandpositionbrandsettings', json_encode($sizeAndPositionBrandSettings));
		$smarty->assign('usedefaultsizeandpositionsettings', $resultArray['usedefaultsizeandpositionsettings']);
		$smarty->assign('sizeandpositionmeasurementunits', $resultArray['sizeandpositionmeasurementunits']);

		$smarty->assign('usedefaultvouchersettings', $resultArray['usedefaultvouchersettings']);
		$smarty->assign('allowvouchers', $resultArray['allowvouchers']);
		$smarty->assign('allowgiftcards', $resultArray['allowgiftcards']);

        $brandVoucherSettings = array();

        // Voucher settings by brand
        foreach ($resultArray['webbrandinglist'] as $theBrand)
        {
            $key = $theBrand['code'];

            if ($key == '')
            {
                $key = '__DEFAULT__';
            }

            $brandVoucherSettings[$key] = array(
				'allowgiftcards' => $theBrand['allowgiftcards'],
				'allowvouchers' => $theBrand['allowvouchers']
            );
        }

		$smarty->assign('brandvouchersettings', json_encode($brandVoucherSettings));

		$brandPerfectlyClearSettings = array();

        // Perfectly Clear settings by brand
        foreach ($resultArray['webbrandinglist'] as $theBrand)
        {
            $key = $theBrand['code'];

            if ($key == '')
            {
                $key = '__DEFAULT__';
            }

            $brandPerfectlyClearSettings[$key] = array(
				'automaticallyapplyperfectlyclear' => $theBrand['automaticallyapplyperfectlyclear'],
				'allowuserstotoggleperfectlyclear' => $theBrand['allowuserstotoggleperfectlyclear']
            );
        }

		$smarty->assign('brandperfectlyclearsettings', json_encode($brandPerfectlyClearSettings));

		LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizedonlinedesignerlogolinktooltip', '', $resultArray['onlinedesignerlogolinktooltip'], true, false, true);

		// Smart guide setings per brand.
		$smartGuidesBrandSettings = array();

        foreach ($resultArray['webbrandinglist'] as $a)
        {
			$key = $a['code'];

			if ($key == '')
            {
                $key = '__DEFAULT__';
            }

			$smartGuidesBrandSettings[$key] = array(
				'enable' => $a['smartguidesenable'],
				'objectguidecolour' => $a['smartguidesobjectguidecolour'],
				'pageguidecolour' => $a['smartguidespageguidecolour']
			);
		}

		$smarty->assign('smartguidesbrandsettings', json_encode($smartGuidesBrandSettings));
		$smarty->assign('smartguidesdefaultobjectguidecolour', TPX_SMARTGUIDES_OBJECT_GUIDECOLOUR);
		$smarty->assign('smartguidesdefaultpageguidecolour', TPX_SMARTGUIDES_PAGE_GUIDECOLOUR);
		$smarty->assign('smartguidesenable', $resultArray['smartguidesenable']);
		$smarty->assign('smartguidesobjectguidecolour', $resultArray['smartguidesobjectguidecolour']);
		$smarty->assign('smartguidespageguidecolour', $resultArray['smartguidespageguidecolour']);
		$smarty->assign('usedefaultsmartguidessettings', $resultArray['usedefaultsmartguidessettings']);
		$smarty->assign('usedefaultautomaticallyapplyperfectlyclear', $resultArray['usedefaultautomaticallyapplyperfectlyclear']);
		$smarty->assign('automaticallyapplyperfectlyclear', $resultArray['automaticallyapplyperfectlyclear']);
		$smarty->assign('allowuserstotoggleperfectlyclear', $resultArray['allowuserstotoggleperfectlyclear']);
		
		// Page controls panel.
		$pageControlsSettings = array();

        // Page controls settings by brand.
        foreach ($resultArray['webbrandinglist'] as $theBrand)
        {
            $key = $theBrand['code'];

            if ($key == '')
            {
                $key = '__DEFAULT__';
            }

            $pageControlsSettings[$key] = array(
				'insertdeletebuttonsvisibility' => $theBrand['insertdeletebuttonsvisibility'],
				'totalpagesdropdownmode' => $theBrand['totalpagesdropdownmode']
            );
        }

		$smarty->assign('pagecontrolssettings', json_encode($pageControlsSettings));
		$smarty->assign('usedefaultinsertdeletebuttonsvisibilitychecked', ($resultArray['usedefaultinsertdeletebuttonsvisibility'] ? 'true': 'false'));
		$smarty->assign('usedefaulttotalpagesdropdownmodechecked', ($resultArray['usedefaulttotalpagesdropdownmode'] ? 'true': 'false'));
		$smarty->assign('insertdeletebuttonsvisibilitychecked', ($resultArray['insertdeletebuttonsvisibility'] ? 'true': 'false'));
        $smarty->assign('totalpagesdropdownmodechecked', ($resultArray['totalpagesdropdownmode'] ? 'true': 'false'));

        $brandPicturesPerPage = [];
        foreach ($resultArray['webbrandinglist'] as $brand)
		{
			$key = $brand['code'];

			if ($key == '')
			{
				$key = '__DEFAULT__';
			}

			$brandPicturesPerPage[$key] = $brand['averagepicturesperpage'];
		}

        $picturesPerPageValues = array(
			array(0, $smarty->get_config_vars('str_LabelOff')),
			array(1, 1),
            array(2, 2),
            array(3, 3),
            array(4, 4),
            array(5, 5),
            array(6, 6),
            array(7, 7),
            array(8, 8),
            array(9, 9),
            array(10, 10),
        );
        
        $smarty->assign('brandpicturesperpage', json_encode($brandPicturesPerPage));
        $smarty->assign('usedefaultaveragepicturesperpagechecked', ($resultArray['usedefaultaveragepicturesperpage'] ? 'true': 'false'));
        $smarty->assign('averagepicturesperpage', $resultArray['averagepicturesperpage']);
        $smarty->assign('picturesperpagevalues', json_encode($picturesPerPageValues));

        //Component Upsell Settings
        $smarty->assign('componentupsellenabled', $resultArray['componentupsellenabled']);
        $smarty->assign('componentupsellproductquantity', $resultArray['componentupsellproductquantity']);

        $smarty->assign('TPX_COMPONENT_UPSELL_ENABLED', TPX_COMPONENT_UPSELL_ENABLED);
        $smarty->assign('TPX_COMPONENT_UPSELL_ALLOW_PRODUCT_QTY', TPX_COMPONENT_UPSELL_ALLOW_PRODUCT_QTY);

        // Brand Defaults
		$brandComponentUpsellSettings = array();
        $brandDefaultTypeArray = array();

        // Page controls settings by brand.
        foreach ($resultArray['webbrandinglist'] as $theBrand)
        {
            $key = $theBrand['code'];

            if ($key == '')
            {
                $key = '__DEFAULT__';
            }

            $brandComponentUpsellSettings[$key] = array(
				'componentupsellenabled' => $theBrand['componentupsellenabled'],
				'componentupsellproductquantity' => $theBrand['componentupsellproductquantity']
            );

            $brandDefaultTypeArray[$key] = $theBrand['usedefaultaccountpagesurl'];
        }

		$smarty->assign('brandcomponentupsellsettings', json_encode($brandComponentUpsellSettings));
        $smarty->assign('usedefaultcomponentupsellsettings', $resultArray['usedefaultcomponentupsellsettings']);

        $smarty->assign('fontlists', json_encode($resultArray['fontlists'] ?? []));
        $smarty->assign('selectedfontlist', $resultArray['fontlistselected']);

        $smarty->assign('usedefaultaccountpagesurl', $resultArray['usedefaultaccountpagesurl']);
        $smarty->assign('accountpagesurl', $resultArray['accountpagesurl']);
        $smarty->assign('brandaccountpagesurldefaulttypes', json_encode($brandDefaultTypeArray));


        // Calculate the difference in days.
		$date1 = strtotime($resultArray['promopaneloverridestartdate']);
		$date2 = strtotime('2000-01-01');

		// Which is the latest?
	    if ($date1 < $date2) {
	      $resultArray['promopaneloverridestartdate'] = '2000-01-01';
	    }

        if ($resultArray['promopaneloverrideenddate'] == '0000-00-00 00:00:00' || $resultArray['promopaneloverrideenddate'] == '')
        {
        	$resultArray['promopaneloverrideenddate'] = date('Y-m-d');
        }

        $smarty->assign('promopaneloverridemode', $resultArray['promopaneloverridemode']);
        $smarty->assign('promopaneloverridestartdate', LocalizationObj::formatLocaleDate($resultArray['promopaneloverridestartdate']));
        $smarty->assign('promopaneloverrideenddate', LocalizationObj::formatLocaleDate($resultArray['promopaneloverrideenddate']));
        $smarty->assign('promopaneloverrideurl', $resultArray['promopaneloverrideurl']);
        $smarty->assign('promopaneloverridepixelratio', $resultArray['promopaneloverridepixelratio']);
        $smarty->assign('promopaneloverrideheight', $resultArray['promopaneloverrideheight']);
        $smarty->assign('promopaneloverriderequirehidpi', $resultArray['promopaneloverridecantoggle']);

        $smarty->displayLocale('admin/autoupdate/autoupdatelicensekeysedit.tpl');
    }

	static function uploadDesignerBannerImage($pResultArray)
	{
		if($pResultArray['result'] == '')
		{
			echo '{"success":true, "msg": ""}';
		}
		else
		{
			echo '{"success":false, "msg": ""}';
		}
	}

    static function uploadDesignerSplashScreenImage($pResultArray)
    {
    	if ($pResultArray['result'] == '')
    	{
			$width = $pResultArray['width'];
			$height = $pResultArray['height'];
			$message = '';
			if (($width > $pResultArray['recommendedwidth']) || ($height > $pResultArray['recommendedheight']))
			{
		       	$smarty = SmartyObj::newSmarty('Components');
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

    static function changeApplicationPriority($pResultArray)
    {
        if ($pResultArray['error'] === false)
        {
            echo '{"success":true, "msg":""}';
        }
        else
        {
            echo '{"success":false, "msg":"' . $pResultArray['errorparam'] . '"}';
        }
    }

    static function getPromoPanelImage($pResultArray)
    {
        if ($pResultArray['image'] != '')
        {
            Header('Content-type:' . 'image/jpeg');
            echo $pResultArray['image'];
        }
        else
        {
            Header('Location: ' . $pResultArray['url']);
        }
    }

    static function uploadPromoPanelImage($pResultArray)
	{
        $result = $pResultArray['result'];
		if ($result == '')
		{
			echo '{"success":true, "msg": "", "hidpi":' . $pResultArray['hidpi'] . '}';
		}
		else
		{
            if ($result == "INVALIDDIMENSIONS")
            {
                // replace the error code with an error message
                $smarty = SmartyObj::newSmarty('AdminAutoUpdateLicenseKeys');
                $result = $smarty->get_config_vars('str_ErrorPromoPanelDimensions');
                $searchFor = ['^w', '^h'];
                $replaceWith = [$pResultArray['width'], $pResultArray['height']];
                $result = str_replace($searchFor, $replaceWith, $result);
            }

            echo '{"success":false, "msg":"' . $result .'"}';
		}
	}
}
/**
 * @} End of "addtogroup AutoUpdate".
 */
?>
