<?php

class AdminBranding_view
{
	static function initialize($pBrandCount)
	{
		global $gConstants;

		$addBrands = true;

		$smarty = SmartyObj::newSmarty('AdminBranding');

		$adminLabel = SmartyObj::getParamValue('Admin', 'str_Title');
		$smarty->assign('adminlabel', $adminLabel);

		if ($gConstants['optionbc'] != 0)
		{
			if ($pBrandCount >= $gConstants['optionbc'])
			{
				$addBrands = false;
			}
		}

		$smarty->assign('bc', $addBrands);

        $smarty->displayLocale('admin/branding/branding.tpl');
	}

    static function unsubscribeAllUsers($pResultArray)
    {
        $jsonArray = array('success' => true, 'msg' => '');

        if ($pResultArray['result'] != '')
        {
            $jsonArray['success'] = false;
            $jsonArray['msg'] = $pResultArray['resultparam'];
        }

        echo json_encode($jsonArray);
    }

	static function displayList($pResultArray, $pError = '')
	{
	    global $ac_config;

	    $smarty = SmartyObj::newSmarty('AdminBranding');
	    $defaults = DatabaseObj::getBrandingFromCode('');

	    $brandItem = array();
	    $resultArray = array();

        $itemCount = count($pResultArray);

        for ($i = 0; $i < $itemCount; $i++)
        {
            $id = $pResultArray[$i]['id'];
            $code = $pResultArray[$i]['code'];
            $name = $pResultArray[$i]['name'];
			if ($name == '')
			{
				$name = '<i>' . $smarty->get_config_vars('str_LabelDefault'). '</i>';
			}

            $applicationName = $pResultArray[$i]['applicationname'];
            $displayURL = $pResultArray[$i]['displayurl'];
            $isActive = $pResultArray[$i]['isactive'];

        	if ($displayURL == '')
        	{
        	    $displayURL = '<i>' . UtilsObj::correctPath($defaults['displayurl']) . ($ac_config['WEBBRANDFOLDERNAME'] == '' ? 'Branding' : $ac_config['WEBBRANDFOLDERNAME']) . '/' . $name . '/' . '</i>';
        	}

            $brandItem['recordid'] = "'" . UtilsObj::ExtJSEscape($id) . "'";
            $brandItem['company'] = "'" . UtilsObj::ExtJSEscape($pResultArray[$i]['companycode']) . "'";
            $brandItem['foldername'] = "'" . UtilsObj::ExtJSEscape($name) . "'";
            $brandItem['appname'] = "'" . UtilsObj::ExtJSEscape($applicationName) . "'";
            $brandItem['displayurl'] = "'" . UtilsObj::ExtJSEscape($displayURL) . "'";
            $brandItem['isactive'] = "'" . UtilsObj::ExtJSEscape($isActive) . "'";
            array_push($resultArray, '['.join(',', $brandItem).']');
	    }

		$summaryArray = join(',', $resultArray);
        if ($summaryArray != '')
        {
        	$summaryArray = ', ' . $summaryArray;
        }

        echo '[['.count($pResultArray).']'.$summaryArray.']';
        return;
    }

	static function authenticate($details)
	{
		global $gConstants;
		global $gSession;

		$defaultBrand = DatabaseObj::getBrandingFromCode('');
		$smarty = SmartyObj::newSmarty('AdminBranding');
		foreach ($details as $key => $value) {
			$smarty->assign($key, $value);
		}

		$smarty->displayLocale('admin/branding/authentication.tpl', '', true);
	}

    static function displayEntry($pTitle, $pResultArray, $pActionButtonName, $pDefaultOwner, $pError = '')
    {
        global $gConstants;
        global $gSession;

        $defaultBrand = DatabaseObj::getBrandingFromCode('');

        $smarty = SmartyObj::newSmarty('AdminBranding');
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->assign('owner', $gSession['userdata']['userowner']);
        $smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('brandingid', $pResultArray['id']);
        $smarty->assign('code', $pResultArray['code']);
        $smarty->assign('name', UtilsObj::encodeString($pResultArray['name'], true));
        $smarty->assign('applicationname', UtilsObj::encodeString($pResultArray['applicationname'], true));
        $smarty->assign('displayurl', $pResultArray['displayurl']);
        $smarty->assign('weburl', $pResultArray['weburl']);
        $smarty->assign('onlinedesignerurl', $pResultArray['onlinedesignerurl']);
        $smarty->assign('onlineuiurl', $pResultArray['onlineuiurl']);
        $smarty->assign('onlineapiurl', $pResultArray['onlineapiurl']);
        $smarty->assign('entropy', $pResultArray['entropy']);
        $smarty->assign('regenerateVisible', $pResultArray['regenerateVisible']);

        $smarty->assign('onlinedesignerlogouturl', $pResultArray['onlinedesignerlogouturl']);
        $smarty->assign('googleanalyticscode', $pResultArray['googleanalyticscode']);
        $smarty->assign('googletagmanageronlinecode', $pResultArray['googletagmanageronlinecode']);
        $smarty->assign('googletagmanagercccode', $pResultArray['googletagmanagercccode']);
		$smarty->assign('useridtrackingchecked', $pResultArray['googleanalyticsuseridtracking']);
        $smarty->assign('nagdelay', $pResultArray['onlinedesignersigninregisterpromptdelay']);
        $smarty->assign('mainwebsiteurl', $pResultArray['mainwebsiteurl']);
        $smarty->assign('macdownloadurl', $pResultArray['macdownloadurl']);
        $smarty->assign('win32downloadurl', $pResultArray['win32downloadurl']);
        $smarty->assign('supporttelephonenumber', $pResultArray['supporttelephonenumber']);
        $smarty->assign('supportemailaddress', $pResultArray['supportemailaddress']);
        $smarty->assign('sharebyemailmethod', $pResultArray['sharebyemailmethod']);
        $smarty->assign('orderfrompreview', $pResultArray['orderfrompreview']);
        $smarty->assign('sharehidebranding', $pResultArray['sharehidebranding']);
        $smarty->assign('previewdomainurl', $pResultArray['previewdomainurl']);
        $smarty->assign('dateformat', LocalizationObj::getLocaleFormatValue('str_DateFormat'));
        $smarty->assign('previewexpire', $pResultArray['previewexpires']);
        $smarty->assign('previewexpiredays', $pResultArray['previewexpiresdays']);
        $smarty->assign('defaultcommunicationpreference' , $pResultArray['defaultcommunicationpreference']);
		// Push on the none option to the oauth providers.
		array_unshift($pResultArray['oauthproviders'], [0, $smarty->get_config_vars("str_LabelNone")]);
		$smarty->assign('oauthproviders', json_encode($pResultArray['oauthproviders']));
		$smarty->assign('oauthprovider', $pResultArray['oauthprovider']);
		$smarty->assign('oauthtokenid', $pResultArray['oauthtoken']);
        $smarty->assign('onlineabouturl', $pResultArray['onlineabouturl']);
        $smarty->assign('onlinehelpurl', $pResultArray['onlinehelpurl']);
        $smarty->assign('onlinetermsandconditionsurl', $pResultArray['onlinetermsandconditionsurl']);
		//used for deciding whether to show designer settings tab
		if ($gConstants['optiondesol'])
		{
			$smarty->assign('hasonlinedesigner' , 1);
		}
		else
		{
			$smarty->assign('hasonlinedesigner' , 0);
		}


		if ($gConstants['optionholdes'])
		{
			$smarty->assign('onlinedataretentionpolicy' , 0);
			$smarty->assign('onlinedataretentionpolicyoptions' , array());
		}
		else
		{
			$smarty->assign('onlinedataretentionpolicy' , $pResultArray['onlinedataretentionpolicy']);
			$smarty->assign('onlinedataretentionpolicyoptions' , $pResultArray['datapolicies']['data']);
		}

        $smarty->assign('previewlicensekey', $pResultArray['previewlicensekey']);

        // setup payment methods
        if ($pResultArray['usedefaultpaymentmethods'] == 1)
        {
            $smarty->assign('usedefaultpaymentmethodschecked', 'true');
        }
        else
        {
             $smarty->assign('usedefaultpaymentmethodschecked', 'false');
        }

        $paymentMethodsHTML = array();
        $userPaymentMethodsList = explode(',', $pResultArray['paymentmethods']);

        $itemList = $pResultArray['paymentmethodslist'];
        $itemCount = count($itemList);
        for ($i = 0; $i < $itemCount; $i++)
        {
            $paymentMethodCode = $itemList[$i]['code'];

            if (in_array($paymentMethodCode, $userPaymentMethodsList))
            {
                $optionSelected = 'true';
            }
            else
            {
                $optionSelected = 'false';
            }

            $paymentMethodsHTML[] = array('id' => 'paymentmethod' . $i, 'value' => $paymentMethodCode, 'selected' => $optionSelected, 'text' => LocalizationObj::getLocaleString(UtilsObj::encodeString($itemList[$i]['name'], true), '', true));
        }
        $smarty->assign('paymentmethodcount', $itemCount);

        $smarty->assign('paymentmethodslist', $paymentMethodsHTML);

        $smarty->assign('allowgiftcards', $pResultArray['allowgiftcards']);

        $smarty->assign('allowvouchers', $pResultArray['allowvouchers']);

        // setup payment integrations
        $defaultPaymentIntegration = $defaultBrand['paymentintegration'];
        $sllRequiredIntegrations = '';	// integrations that require SSL to be loaded
        $sslLoaded = true;
        $currentPaymentIntegration = $pResultArray['paymentintegration'];
        $paymentIntegrationList = array();
        $defaultPaymentIntegrationCode = '';

        $itemList = $pResultArray['paymentintegrationslist'];
        $itemCount = count($itemList);
        for ($i = 0; $i < $itemCount; $i++)
        {
            $theCode = $itemList[$i]['code'];

			if ($defaultPaymentIntegration == $itemList[$i]['code'])
			{
				$defaultPaymentIntegrationCode = $itemList[$i]['code'];
			}

            if ($currentPaymentIntegration == $theCode)
            {
                $optionSelected = 'selected ';
            }
            else
            {
                $optionSelected = '';
            }
            $paymentIntegrationList[] = array('id' => $theCode, 'name' => UtilsObj::encodeString($itemList[$i]['name']));
            if ($itemList[$i]['ssl'] != 0)
            {
            	$sllRequiredIntegrations .= $itemList[$i]['code'] . ',';
            	$sslLoaded = ($itemList[$i]['ssl'] == 1) ? true : false;
            }
        }
        $smarty->assign('sslloaded', $sslLoaded);
        $smarty->assign('sllrequiredintegrations', $sllRequiredIntegrations);
        $smarty->assign('integrationlist', $paymentIntegrationList);
        $smarty->assign('defaultintegration', $defaultPaymentIntegrationCode);
        $smarty->assign('currentintegration', $currentPaymentIntegration);

        $productionSiteList = array();
        $itemList = $pResultArray['productionsites'];
        array_unshift($itemList, Array('code' => '', 'name' => $smarty->get_config_vars('str_LabelNone')));
        $itemCount = count($itemList);
        $siteCompany = array();
        $siteSelected = '';

        for ($i = 0; $i < $itemCount; $i++)
        {
        	$productionSiteCode = $itemList[$i]['code'];
        	if (isset($itemList[$i]['companyCode']))
        	{
        		$siteCompany[] = '"' . $productionSiteCode . '":' . '"' . $itemList[$i]['companyCode'] . '"';
        	}
        	if ($pDefaultOwner == $productionSiteCode )
        	{
        		$optionSelected = 'selected ';
        		$siteSelected = $productionSiteCode;
        	}
        	else
        	{
        		$optionSelected = '';
        	}
        	$productionSiteList[] = array('id' => $productionSiteCode, 'name' => UtilsObj::encodeString($itemList[$i]['name']));
        }
        $siteCompany = '{' . join(',', $siteCompany) . '}';

        $smarty->assign('productionsites', $productionSiteList);
        $smarty->assign('productionSitesCompanies', $siteCompany);
        $smarty->assign('productionSitesSelected', $pDefaultOwner);

        // Register using email address options for combo box.
        $registerWithEmailOptionsArray = array(
            array('id' => TPX_REGISTER_USING_EMAIL, 'name' => $smarty->get_config_vars('str_LabelEmailAddress')),
            array('id' => TPX_REGISTER_USING_USERNAME, 'name' => $smarty->get_config_vars('str_LabelUserName'))
        );

        $smarty->assign('registerwithemailoptions', $registerWithEmailOptionsArray);
        $smarty->assign('registerusingselected', $pResultArray['registerusingemail']);

        if ($pResultArray['isactive'] == 1)
        {
            $smarty->assign('activechecked', 'checked');
        }
        else
        {
             $smarty->assign('activechecked', '');
        }

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

        // default payment methods from constants
        $smarty->assign('defaultpaymentmethods', $defaultBrand['paymentmethods']);
        $smarty->assign('usedefaultemailsettings', $pResultArray['usedefaultemailsettings']);
        $smarty->assign('smtpport', $pResultArray['smtpport']);
        $smarty->assign('smtpauth', $pResultArray['smtpauth']);
        $smarty->assign('smtpaddress',$pResultArray['smtpaddress']);
        $smarty->assign('smtpauthuser', $pResultArray['smtpauthusername']);
        $smarty->assign('smtpauthpass', $pResultArray['smtpauthpassword']);
        $smarty->assign('smtptype', $pResultArray['smtptype']);
        $smarty->assign('smtpsysfromname', $pResultArray['smtpsystemfromname']);
        $smarty->assign('smtpsysfromaddress', $pResultArray['smtpsystemfromaddress']);
        $smarty->assign('smtpreplyname', $pResultArray['smtpsystemreplytoname']);
        $smarty->assign('smtpreplyaddress',$pResultArray['smtpsystemreplytoaddress']);
        $smarty->assign('smtpadminname', $pResultArray['smtpadminname']);
        $smarty->assign('smtpadminaddress', $pResultArray['smtpadminaddress']);
        $smarty->assign('smtpadminactive', $pResultArray['smtpadminactive']);
        $smarty->assign('smtpprodname', $pResultArray['smtpproductionname']);
        $smarty->assign('smtpprodaddress', $pResultArray['smtpproductionaddress']);
        $smarty->assign('smtpprodactive', $pResultArray['smtpproductionactive']);
        $smarty->assign('smtporderconfname', $pResultArray['smtporderconfirmationname']);
        $smarty->assign('smtporderconfaddress', $pResultArray['smtporderconfirmationaddress']);
        $smarty->assign('smtporderconfactive', $pResultArray['smtporderconfirmationactive']);
        $smarty->assign('smtpsaveordername', $pResultArray['smtpsaveordername']);
        $smarty->assign('smtpsaveorderaddress', $pResultArray['smtpsaveorderaddress']);
        $smarty->assign('smtpsaveorderactive', $pResultArray['smtpsaveorderactive']);
        $smarty->assign('smtpshippingname', $pResultArray['smtpshippingname']);
        $smarty->assign('smtpshippingaddress', $pResultArray['smtpshippingaddress']);
        $smarty->assign('smtpshippingactive', $pResultArray['smtpshippingactive']);
        $smarty->assign('smtpnewaccountname', $pResultArray['smtpnewaccountname']);
        $smarty->assign('smtpnewaccountaddress', $pResultArray['smtpnewaccountaddress']);
        $smarty->assign('smtpnewaccountactive', $pResultArray['smtpnewaccountactive']);
        $smarty->assign('smtpresetpasswordname', $pResultArray['smtpresetpasswordname']);
        $smarty->assign('smtpresetpasswordaddress', $pResultArray['smtpresetpasswordaddress']);
        $smarty->assign('smtpresetpasswordactive', $pResultArray['smtpresetpasswordactive']);
        $smarty->assign('smtporderuploadedname', $pResultArray['smtporderuploadedname']);
        $smarty->assign('smtporderuploadedaddress', $pResultArray['smtporderuploadedaddress']);
        $smarty->assign('smtporderuploadedactive', $pResultArray['smtporderuploadedactive']);

		// default payment methods from constants
		$smarty->assign('gsmtpport', $defaultBrand['smtpport']);
		$smarty->assign('gsmtpauth', $defaultBrand['smtpauth']);
		$smarty->assign('gsmtpaddress', $defaultBrand['smtpaddress']);
		$smarty->assign('gsmtpauthuser', $defaultBrand['smtpauthusername']);
		$smarty->assign('gsmtpauthpass', $defaultBrand['smtpauthpassword']);
        $smarty->assign('gsmtptype', $defaultBrand['smtptype']);
		$smarty->assign('gsmtpsysfromname', $defaultBrand['smtpsystemfromname']);
		$smarty->assign('gsmtpsysfromaddress',$defaultBrand['smtpsystemfromaddress']);
		$smarty->assign('gsmtpreplyname', $defaultBrand['smtpsystemreplytoname']);
		$smarty->assign('gsmtpreplyaddress',$defaultBrand['smtpsystemreplytoaddress']);
		$smarty->assign('gsmtpadminname', $defaultBrand['smtpadminname']);
		$smarty->assign('gsmtpadminaddress', $defaultBrand['smtpadminaddress']);
		$smarty->assign('gsmtpadminactive', $defaultBrand['smtpadminactive']);
		$smarty->assign('gsmtpprodname', $defaultBrand['smtpproductionname']);
		$smarty->assign('gsmtpprodaddress', $defaultBrand['smtpproductionaddress']);
		$smarty->assign('gsmtpprodactive', $defaultBrand['smtpproductionactive']);
		$smarty->assign('gsmtporderconfname', $defaultBrand['smtporderconfirmationname']);
		$smarty->assign('gsmtporderconfaddress', $defaultBrand['smtporderconfirmationaddress']);
		$smarty->assign('gsmtporderconfactive', $defaultBrand['smtporderconfirmationactive']);
		$smarty->assign('gsmtpsaveordername', $defaultBrand['smtpsaveordername']);
		$smarty->assign('gsmtpsaveorderaddress', $defaultBrand['smtpsaveorderaddress']);
		$smarty->assign('gsmtpsaveorderactive', $defaultBrand['smtpsaveorderactive']);
		$smarty->assign('gsmtpshippingname', $defaultBrand['smtpshippingname']);
        $smarty->assign('gsmtpshippingaddress', $defaultBrand['smtpshippingaddress']);
        $smarty->assign('gsmtpshippingactive', $defaultBrand['smtpshippingactive']);
        $smarty->assign('gsmtpnewaccountname', $defaultBrand['smtpnewaccountname']);
        $smarty->assign('gsmtpnewaccountaddress', $defaultBrand['smtpnewaccountaddress']);
        $smarty->assign('gsmtpnewaccountactive', $defaultBrand['smtpnewaccountactive']);
        $smarty->assign('gsmtpresetpasswordname', $defaultBrand['smtpresetpasswordname']);
        $smarty->assign('gsmtpresetpasswordaddress', $defaultBrand['smtpresetpasswordaddress']);
        $smarty->assign('gsmtpresetpasswordactive', $defaultBrand['smtpresetpasswordactive']);
        $smarty->assign('gsmtporderuploadedname', $defaultBrand['smtporderuploadedname']);
        $smarty->assign('gsmtporderuploadedaddress', $defaultBrand['smtporderuploadedaddress']);
        $smarty->assign('gsmtporderuploadedactive', $defaultBrand['smtporderuploadedactive']);
		$smarty->assign('goauthprovider', $defaultBrand['oauthprovider']);
		$smarty->assign('goauthtokenid', $defaultBrand['oauthtoken']);

        $redactionModeOptions = array(
			'disabled' => TPX_REDACTION_MODE_DISABLED,
			'administrator' => TPX_REDACTION_MODE_ADMINISTRATOR,
			'request' => TPX_REDACTION_MODE_REQUEST,
			'allow' => TPX_REDACTION_MODE_ALLOW,
			'immediate' => TPX_REDACTION_MODE_IMMEDIATE,
		);

        $smarty->assign('redactionModeOptions', $redactionModeOptions);
        $smarty->assign('redactionMode', $pResultArray['redactionmode']);
        $smarty->assign('automaticredactionenabled', $pResultArray['automaticredactionenabled']);
        $smarty->assign('automaticredactiondays', $pResultArray['automaticredactiondays']);
        $smarty->assign('redactionnotificationdays', $pResultArray['redactionnotificationdays']);

        $smarty->assign('orderredactionmode', $pResultArray['orderredactionmode']);
        $smarty->assign('orderredactiondays', $pResultArray['orderredactiondays']);

        $smarty->assign('desktopthumbnaildeletionenabled', $pResultArray['desktopthumbnaildeletionenabled']);
        $smarty->assign('desktopthumbnaildeletionordereddays', $pResultArray['desktopthumbnaildeletionordereddays']);

        $smarty->assign('allowimagescalingbefore', $pResultArray['allowimagescalingbefore']);
        $smarty->assign('imagescalingbefore', 0);
		$smarty->assign('imagescalingbeforeenabled', '');

        if ($pResultArray['allowimagescalingbefore'])
        {
            $smarty->assign('imagescalingbefore', $pResultArray['imagescalingbefore']);

            if ($pResultArray['imagescalingbeforeenabled'] == 1)
            {
                $smarty->assign('imagescalingbeforeenabled', 'checked');
            }
        }

        $smarty->assign('imagescalingafter', $pResultArray['imagescalingafter']);

        if ($pResultArray['imagescalingafterenabled'] == 1)
        {
            $smarty->assign('imagescalingafterenabled', 'checked');
        }
        else
        {
            $smarty->assign('imagescalingafterenabled', '');
        }

        $smarty->assign('shufflelayout', $pResultArray['shufflelayout']);

        if ($pResultArray['showshufflelayoutoptions'] == 1)
        {
            $smarty->assign('showshufflelayoutoptions', 'checked');
        }
        else
        {
            $smarty->assign('showshufflelayoutoptions', '');
        }

        $smarty->assign('onlineeditormode', $pResultArray['onlineeditormode']);

		if ($pResultArray['enableswitchingeditor'] == 1)
        {
            $smarty->assign('enableswitchingeditor', 'checked');
        }
        else
        {
             $smarty->assign('enableswitchingeditor', '');
        }

        if ($pResultArray['automaticallyapplyperfectlyclear'] == 1)
        {
            $smarty->assign('automaticallyapplyperfectlyclear', 'checked');
        }
        else
        {
             $smarty->assign('automaticallyapplyperfectlyclear', '');
        }

        if ($pResultArray['allowuserstotoggleperfectlyclear'] == 1)
        {
            $smarty->assign('allowuserstotoggleperfectlyclear', 'checked');
        }
        else
        {
             $smarty->assign('allowuserstotoggleperfectlyclear', '');
        }

        if ($pResultArray['massunsubscribetaskforbrandrunning'])
        {
            $smarty->assign('massunsubscribetaskforbrandrunning', 1);
        }
        else
        {
            $smarty->assign('massunsubscribetaskforbrandrunning', 0);
        }

		$redactByAdmin = 0;
		$redactByRequest = 0;
		$redactByUser = 0;

		switch($pResultArray['redactionmode'])
		{
			case TPX_REDACTION_MODE_ADMINISTRATOR:
			{
				$redactByAdmin = 1;
				break;
			}

			case TPX_REDACTION_MODE_REQUEST:
			{
				$redactByAdmin = 1;
				$redactByRequest = 1;
				break;
			}

			case TPX_REDACTION_MODE_ALLOW:
			{
				$redactByAdmin = 1;
				$redactByUser = 1;
				break;
			}

			case TPX_REDACTION_MODE_IMMEDIATE:
			{
				$redactByAdmin = 1;
				$redactByUser = 1;
				break;
			}
		}

        $smarty->assign('redactByAdmin', $redactByAdmin);
        $smarty->assign('redactByRequest', $redactByRequest);
        $smarty->assign('redactByUser', $redactByUser);

		$smarty->assign('usemultilinebasketworkflow',  $pResultArray['usemultilinebasketworkflow']);

		$smarty->assign('actionbutton', $smarty->get_config_vars($pActionButtonName));
		//URL for the online designer logo link
		$smarty->assign('onlinedesignerlogolinkurl', $pResultArray['onlinedesignerlogolinkurl']);
		$smarty->assign('onlinedesignerlogolinktooltip', $pResultArray['onlinedesignerlogolinktooltip']);

        $smarty->assign('onlinedesignercdnurl', $pResultArray['onlinedesignercdnurl']);

		$smarty->assign('smartguidesenable', $pResultArray['smartguidesenable']);
		$smarty->assign('smartguidesobjectguidecolour', $pResultArray['smartguidesobjectguidecolour']);
		$smarty->assign('smartguidespageguidecolour', $pResultArray['smartguidespageguidecolour']);
		$smarty->assign('smartguidesdefaultobjectguidecolour', TPX_SMARTGUIDES_OBJECT_GUIDECOLOUR);
		$smarty->assign('smartguidesdeafultpageguidecolour', TPX_SMARTGUIDES_PAGE_GUIDECOLOUR);


		$smarty->assign('sizeandpositionmeasurementunits', $pResultArray['sizeandpositionmeasurementunits']);
		$measurementUnits = array(
			array(TPX_COORDINATE_SCALE_INCHES, $smarty->get_config_vars('str_MeasurementUnitInches')),
			array(TPX_COORDINATE_SCALE_MILLIMETRES, $smarty->get_config_vars('str_MeasurementUnitMillimetres')),
			array(TPX_COORDINATE_SCALE_CENTIMETRES, $smarty->get_config_vars('str_MeasurementUnitCentimetres'))
		);
		$smarty->assign('measurementunitoptions', json_encode($measurementUnits));

        //Component Upsell Settings
        $smarty->assign('componentupsellenabled', $pResultArray['componentupsellenabled']);
        $smarty->assign('componentupsellproductquantity', $pResultArray['componentupsellproductquantity']);

        $smarty->assign('TPX_COMPONENT_UPSELL_ENABLED', TPX_COMPONENT_UPSELL_ENABLED);
        $smarty->assign('TPX_COMPONENT_UPSELL_ALLOW_PRODUCT_QTY', TPX_COMPONENT_UPSELL_ALLOW_PRODUCT_QTY);

		LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizedonlinedesignerlogolinktooltip', '', $pResultArray['onlinedesignerlogolinktooltip'], true, false, true);

		// Page controls panel.
		$smarty->assign('insertdeletebuttonsvisibilitychecked', (($pResultArray['insertdeletebuttonsvisibility'] === TPX_INSERTDELETEBUTTONS_VISIBILITY_VISIBLE) ? 'true' : 'false'));
		$smarty->assign('totalpagesdropdownmodechecked', (($pResultArray['totalpagesdropdownmode'] === TPX_TOTALPAGES_DROPDOWN_MODE_ENABLED) ? 'true' : 'false'));

		// Customisation Panel
		$brandcustomTypes = array(
			TPX_BRANDING_FILE_TYPE_OL_LOGO => 'onlineLogoType',
            TPX_BRANDING_FILE_TYPE_OL_LOGO_DARK => 'onlineLogoTypeDark',
			TPX_BRANDING_FILE_TYPE_CC_LOGO => 'controlLogoType',
			TPX_BRANDING_FILE_TYPE_MARKETING => 'marketingType',
			TPX_BRANDING_FILE_TYPE_EMAIL_LOGO => 'emailLogoType'
		);

		$maximums = array();
		$recommended = array();
		$maximumsMessage = $smarty->get_config_vars('str_LabelMaximumSize');
		$recommendedMessage = $smarty->get_config_vars('str_LabelRecommendedSize');

		$sizes = UtilsObj::getBrandAssetDetails(0);

		foreach ($brandcustomTypes as $key => $value)
		{
			switch ($key)
			{
				case TPX_BRANDING_FILE_TYPE_CC_LOGO:
				case TPX_BRANDING_FILE_TYPE_MARKETING:
				case TPX_BRANDING_FILE_TYPE_EMAIL_LOGO:
				case TPX_BRANDING_FILE_TYPE_OL_LOGO:
                case TPX_BRANDING_FILE_TYPE_OL_LOGO_DARK:
				{
					$smarty->assign($value, $key);

					$recommended[$key] = str_replace(array('^rw', '^rh'), $sizes[$key]['recommended'], $recommendedMessage);

					$maximums[$key] = str_replace(array('^mw', '^mh'), $sizes[$key]['maximums'], $maximumsMessage);

					break;
				}
			}
		}

		$smarty->assign('maximums', json_encode($maximums));
		$smarty->assign('recommended', json_encode($recommended));
		$smarty->assign('brandassetsdata', json_encode($pResultArray['brandassetsdata']));

		// Customisation text
		$brandCustomTextTypes = array(
			TPX_BRANDING_TEXT_TYPE_SIGNATURE => 'emailsignature'
		);
		$brandAssetStringsArray = array();
		$brandAssetStringEnabledArray = array();
		$brandAssetStringDefaultsArray = array();

		$defaultBrandAssetStringsArray = array();

		foreach ($brandCustomTextTypes as $typeKey => $typedescription)
		{
			$brandAssetStringEnabledArray[$typeKey] = $pResultArray['cusomisedtext'][$typeKey]['enabled'];
			$brandAssetStringDefaultsArray[$typeKey] = $pResultArray['cusomisedtext'][$typeKey]['default'];
			$brandAssetStringsArray[$typeKey] = $pResultArray['cusomisedtext'][$typeKey]['data'];

			$defaultBrandAssetStringsArray[$typeKey] = $pResultArray['cusomisedtext'][$typeKey]['defaultdata'];
		}

		$smarty->assign('brandassetstringsenabled', json_encode($brandAssetStringEnabledArray));
		$smarty->assign('brandassetstringsusedefault', json_encode($brandAssetStringDefaultsArray));
		$smarty->assign('brandassetstrings', json_encode($brandAssetStringsArray));
		$smarty->assign('defaultbrandstrings', json_encode($defaultBrandAssetStringsArray));

        // Average pictures per page.
        $smarty->assign('averagepicturesperpage', $pResultArray['averagepicturesperpage']);
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
		$smarty->assign('picturesperpagevalues', json_encode($picturesPerPageValues));

		$smarty->assign('fontlists', json_encode($pResultArray['fontlists'] ?? []));
		$smarty->assign('selectedfontlist', $pResultArray['fontlistselected']);

        $smarty->assign('usedefaultaccountpagesurl', $pResultArray['usedefaultaccountpagesurl']);
        $smarty->assign('accountpagesurl', $pResultArray['accountpagesurl']);

        $smarty->displayLocale('admin/branding/brandingedit.tpl');
    }

    static function displayAdd($pResultArray)
	{
		global $gSession;
        self::displayEntry('str_TitleNewBranding', $pResultArray, 'str_ButtonAdd', $gSession['userdata']['userowner'], '');
    }

    static function displayEdit($pResultArray)
	{
		if ($pResultArray['code'] == '')
		{
			self::displayEntry('str_TitleEditDefaultBranding', $pResultArray, 'str_ButtonUpdate',$pResultArray['owner'], '');
		}
		else
		{
			self::displayEntry('str_TitleEditBranding', $pResultArray, 'str_ButtonUpdate',$pResultArray['owner'], '');
		}
    }

	static function uploadBrandFile($pResultArray)
    {
        $jsonArray = array('success' => true, 'msg' => '', 'tempfilepath' => $pResultArray['data']['tmppath']);

        if ($pResultArray['result'] != '')
        {
            $jsonArray['success'] = false;
            $jsonArray['msg'] = $pResultArray['resultparam'];
            $jsonArray['tempfilepath'] = '';
        }

        echo json_encode($jsonArray);
    }
}
?>
