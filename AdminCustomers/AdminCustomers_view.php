<?php

require_once('../Utils/UtilsAddress.php');

class AdminCustomers_view
{
	static function initialize()
	{
		$smarty = SmartyObj::newSmarty('AdminCustomers');
		$smarty->displayLocale('admin/customers/customers.tpl');
	}

    static function displayEntry($pTitle, $pResultArray, $pActionButtonName, $pError = '')
    {
    	global $gSession;

        $smarty = SmartyObj::newSmarty('AdminCustomers');
        $smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('addresstitle', $smarty->get_config_vars('str_LabelContactInformation'));
        $smarty->assign('customerid', $pResultArray['recordid']);
        $smarty->assign('groupcode', $pResultArray['groupcode']);

        // setup the license key group list
        // and javascript array of payment methods
        $itemList = $pResultArray['grouplist'];

        $itemCount = count($itemList);
        $paymentMethodDefaults = $pResultArray['paymentmethoddefaults'];
        $javascriptVar = "paymentMethodDefaults = new Array();";
        for ($i = 0; $i < $itemCount; $i++)
        {
            $groupCode = $itemList[$i];
      		$javascriptVar .= "paymentMethodDefaults['" . $groupCode['id'] . "'] = '" . $paymentMethodDefaults[$i] ."';";
        }

        $smarty->assign('grouplist', $pResultArray['grouplist'] );
		$smarty->assign('paymentmethoddefaults', $javascriptVar);

        // setup payment methods
        if ($pResultArray['usedefaultpaymentmethods'] == 1)
        {
            $smarty->assign('usedefaultpaymentmethodschecked', 'checked');
        }
        else
        {
             $smarty->assign('usedefaultpaymentmethodschecked', '');
        }

        $paymentMethodsHTML = array();
        $userPaymentMethodsList = explode(',', $pResultArray['paymentmethods']);
        $smarty->assign('userPaymentMethodsList', $pResultArray['paymentmethods']);
        $itemList = $pResultArray['paymentmethodslist'];
        $itemCount = count($itemList);

        for ($i = 0; $i < $itemCount; $i++)
        {
            $paymentMethodCode = $itemList[$i]['code'];
            $paymentMethodsHTML[] =  '{ style:"margin-left: 17px", name: "' .$paymentMethodCode . '", id: "paymentmethod' . $i.'", checked: false, hideLabel: true, boxLabel: "' . UtilsObj::encodeString(LocalizationObj::getLocaleString($itemList[$i]['name'], '', true)) . '"}';
        }
        $smarty->assign('paymentmethodshtml', '['.join(',', $paymentMethodsHTML).']');
        $smarty->assign('paymentmethodcount', $itemCount);

        // setup licence key address preferences in form of a Javascript array
        $addressDefaultsJS = "var addressDefaults = new Array();\n";
        $itemList = $pResultArray['addressdefaults'];
        $itemCount = count($itemList);
        for ($i = 0; $i < $itemCount; $i++)
        {
			$groupcode = $itemList[$i]['groupcode'];
			$addressDefaultsJS .= "addressDefaults['$groupcode'] = new Array();\n";
			foreach ($itemList[$i] as $key => $value) {
				if ($key != 'groupcode')
				{
					$addressDefaultsJS .= "addressDefaults['$groupcode']['$key'] = $value;\n";
				}
			}
        }
		$smarty->assign('addressdefaultsjs', $addressDefaultsJS);

        // setup the other fields
		$smarty->assign('session', $gSession['ref']);
        $smarty->assign('login', UtilsObj::encodeString($pResultArray['login'], true));
        $smarty->assign('password', '**UNCHANGED**');
        $smarty->assign('contactfname', UtilsObj::encodeString($pResultArray['contactfirstname'], true));
        $smarty->assign('contactlname', UtilsObj::encodeString($pResultArray['contactlastname'], true));
        $smarty->assign('companyname', UtilsObj::encodeString($pResultArray['companyname'], true));
        $smarty->assign('address1', UtilsObj::encodeString($pResultArray['address1'], true));
        $smarty->assign('address2', UtilsObj::encodeString($pResultArray['address2'], true));
        $smarty->assign('address3', UtilsObj::encodeString($pResultArray['address3'], true));
        $smarty->assign('address4', UtilsObj::encodeString($pResultArray['address4'], true));
        $smarty->assign('add41', UtilsObj::encodeString($pResultArray['add41'], true));
        $smarty->assign('add42', UtilsObj::encodeString($pResultArray['add42'], true));
        $smarty->assign('add43', UtilsObj::encodeString($pResultArray['add43'], true));
        $smarty->assign('city', UtilsObj::encodeString($pResultArray['city'], true));
        $smarty->assign('state', UtilsObj::encodeString($pResultArray['state'], true));
        $smarty->assign('county', UtilsObj::encodeString($pResultArray['county'], true));
        $smarty->assign('country', $pResultArray['countrycode']);
        $smarty->assign('regioncode', $pResultArray['regioncode']);
        $smarty->assign('postcode', UtilsObj::encodeString($pResultArray['postcode'],true ));
        $smarty->assign('telephonenumber', UtilsObj::encodeString($pResultArray['telephonenumber']));
        $smarty->assign('email', UtilsObj::encodeString($pResultArray['emailaddress']));
        $smarty->assign('accountcode', UtilsObj::encodeString($pResultArray['accountcode'], true));
        $smarty->assign('creditlimit', UtilsObj::formatNumber($pResultArray['creditlimit'], 2));
        $smarty->assign('accountbalance', UtilsObj::formatNumber($pResultArray['accountbalance'], 2));
        $smarty->assign('giftcardbalance', UtilsObj::formatNumber($pResultArray['giftcardbalance'], 2));
        $smarty->assign('paymentmethods', $pResultArray['paymentmethods']);
        $smarty->assign('useremaildestination', $pResultArray['useremaildestination']);

        $smarty->assign('registeredtaxnumbertype', $pResultArray['registeredtaxnumbertype']);
		$smarty->assign('registeredtaxnumber',  UtilsObj::encodeString($pResultArray['registeredtaxnumber'], true));

		$smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_NA', TPX_REGISTEREDTAXNUMBERTYPE_NA);
		$smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL', TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL);
		$smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE', TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE);

        if ($pResultArray['defaultaddresscontrol'] == 1)
        {
            $smarty->assign('defaultaddresscontrolchecked', 'checked');
        }
        else
        {
             $smarty->assign('defaultaddresscontrolchecked', '');
        }

        if ($pResultArray['uselicensekeyforshippingaddress'] == 1)
        {
            $smarty->assign('uselicensekeyforshippingaddresschecked', 'checked');
        }
        else
        {
             $smarty->assign('uselicensekeyforshippingaddresschecked', '');
        }

        if ($pResultArray['canmodifyshippingaddress'] == 1)
        {
            $smarty->assign('canmodifyshippingaddresschecked', 'checked');
        }
        else
        {
             $smarty->assign('canmodifyshippingaddresschecked', '');
        }

        if ($pResultArray['canmodifyshippingcontactdetails'] == 1)
        {
            $smarty->assign('canmodifyshippingcontactdetailschecked', 'checked');
        }
        else
        {
             $smarty->assign('canmodifyshippingcontactdetailschecked', '');
        }

        if ($pResultArray['uselicensekeyforbillingaddress'] == 1)
        {
            $smarty->assign('uselicensekeyforbillingaddresschecked', 'checked');
        }
        else
        {
             $smarty->assign('uselicensekeyforbillingaddresschecked', '');
        }

        if ($pResultArray['canmodifybillingaddress'] == 1)
        {
            $smarty->assign('canmodifybillingaddresschecked', 'checked');
        }
        else
        {
             $smarty->assign('canmodifybillingaddresschecked', '');
        }

        if ($pResultArray['canmodifypassword'] == 1)
        {
            $smarty->assign('canmodifypasswordchecked', 'checked');
        }
        else
        {
             $smarty->assign('canmodifypasswordchecked', '');
        }

        if ($pResultArray['sendmarketinginfo'] == 1)
        {
            $smarty->assign('sendmarketinginfochecked', 'checked');
        }
        else
        {
             $smarty->assign('sendmarketinginfochecked', '');
        }

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
            $smarty->assign('error', $smarty->get_config_vars($pError));
        }
        else
        {
            $smarty->assign('error', $pError);
        }

        $smarty->assign('tablewidth', 550);
        $smarty->assign('strictmode', '1'); // enforce compulsory fields

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

        $smarty->assign('taxcode',  $pResultArray['taxcode']);
        $smarty->assign('shippingtaxcode', $pResultArray['shippingtaxcode']);

        $smarty->assign('taxcodelist', $taxRatesArray);
        $smarty->assign('shippingtaxcodelist', $taxRatesArray);

        $smarty->assign('lastlogindate', $pResultArray['lastlogindate']);
        $smarty->assign('lastloginip', $pResultArray['lastloginip']);
        $smarty->assign('protectedfromredaction', $pResultArray['protectedfromredaction']);

		// Check if the account is locked.
		if (strtotime($pResultArray['nextvalidlogindate']) > strtotime(DatabaseObj::getServerTimeUTC()))
		{
			$smarty->assign('accountnotlocked', 0);
		}
		else
		{
			$smarty->assign('accountnotlocked', 1);
		}

		// additions for user gift/voucher controls
		$useDefaults = 'checked';
		$userSettings = array();

		if($pResultArray['recordid'] !== '')
		{
			// we have a user account they may have their own gift/voucher preferences
			if($pResultArray['usedefaultvouchersettings'] === 0)
			{
				$useDefaults = '';
			}
			if($pResultArray['allowvouchers'] === 1)
			{
				$userSettings[] = 'allowvouchers';
			}
			if($pResultArray['allowgiftcards'] === 1)
			{
				$userSettings[] = 'allowgiftcards';
			}
		}
		$smarty->assign('usedefaultgiftvouchersettings', $useDefaults);

		// user specific gift card and voucher settings
		$smarty->assign('userGiftVoucherMethods', json_encode($userSettings, true));
		// brand specific, this will be a multidimensional array of settings for each brand
        $smarty->assign('defaultAllGiftVoucherMethods', json_encode($pResultArray['defaultgiftcardandvouchersettings']));
        
        $customerCurrencyCode = $pResultArray['currencycode'];
        $itemList = $pResultArray['currencylist'];
        $itemCount = count($itemList);
        $currencyLocaleArray = array();

        // loop round the currency list to build an array for the currency control
        // localise each currecny name
        for ($i = 0; $i < $itemCount; $i++)
        {
            $currencyCode = $itemList[$i]['code'];
            $currencyLocale = UtilsObj::encodeString(LocalizationObj::getLocaleString($itemList[$i]['name'], '', true), true);
            $currencyLocaleArray[$currencyCode] = $currencyLocale;

            $curListBuf[] = '["' . $currencyCode . '","' . $currencyCode  . ' - ' . $currencyLocale . '"]';
        }        
        
        $currencyList = '[' . join(',', $curListBuf) . ']';

        // localise lkey currencies
        foreach ($pResultArray['licensekeycurrencysettings'] as &$lkCurrencyArray)
        {
            $lkCurrencyArray['currencylocale'] = $currencyLocaleArray[$lkCurrencyArray['currencycode']];
        }

        $smarty->assign('currencylist', $currencyList);
        $smarty->assign('currencySelected', $customerCurrencyCode);
        $smarty->assign('usedefaultcurrency', $pResultArray['usedefaultcurrency']);
        $smarty->assign('licensekeycurrencysettings', json_encode($pResultArray['licensekeycurrencysettings']));

        $smarty->assign('actionbutton', $smarty->get_config_vars($pActionButtonName));
        $smarty->displayLocale('admin/customers/customeredit.tpl');
    }

	static function displayAdd($pResultArray)
	{
        self::displayEntry('str_TitleNewCustomer', $pResultArray, 'str_ButtonAdd');
    }

    static function displayEdit($pResultArray)
	{
	   self::displayEntry('str_TitleEditCustomer', $pResultArray, 'str_ButtonUpdate');
    }

    static function displayDeletionResults($pResultArray)
    {
        if ($pResultArray['result'] == '')
        {
            $smarty = SmartyObj::newSmarty('AdminCustomers');
			$messageArray = array('session' => '', 'order' => '', 'protected' => '', 'activeonline' => '');

            if (count($pResultArray['session']) > 0)
            {
                $messageArray['session'] = str_replace("'^0'", join(', ', $pResultArray['session']), $smarty->get_config_vars('str_ErrorUsedInSession'));
            }
            if (count($pResultArray['order']) > 0)
            {
                $messageArray['order'] = str_replace("'^0'", join(', ', $pResultArray['order']), $smarty->get_config_vars('str_ErrorUsedInOrder'));
            }
            if (count($pResultArray['protected']) > 0)
            {
                $messageArray['protected'] = str_replace("'^0'", join(', ', $pResultArray['protected']), $smarty->get_config_vars('str_ErrorProtectedFromRedaction'));
            }
            if (count($pResultArray['activeonline']) > 0)
            {
                $messageArray['activeonline'] = str_replace("'^0'", join(', ', $pResultArray['activeonline']), $smarty->get_config_vars('str_ErrorExistingOnlineData'));
            }

            // create a message if any accounts cannot be deleted
			$message = join('<br /><br />', array_filter($messageArray, 'strlen'));
			$title = $smarty->get_config_vars('str_TitleWarning');

            if ($message === '')
			{
				// display the default delete message and title
	            $title = $smarty->get_config_vars('str_TitleConfirmation');
				$message = $smarty->get_config_vars('str_CustomersDeleted');
			}

            echo "{'success':'true', 'title':'" . UtilsObj::ExtJSEscape($title) . "', 'msg':'" . UtilsObj::ExtJSEscape($message) . "' }";
        }
        else
        {
            $smarty = SmartyObj::newSmarty('AdminCustomers');
		
            if (substr($pResultArray['result'], 0, 4) == 'str_')
            {
                $message = $smarty->get_config_vars($pResultArray['result']);
            }
            else
            {
                $message = $pResultArray['result'];
            }
            
            echo '{"success":false,	"msg":"' . $message . '"}';

        }
    }

	static function displayCustomerExport()
	{
		global $ac_config;
		global $gSession;

		$smarty = SmartyObj::newSmarty('AdminCustomers');
		$smarty->assign('title', $smarty->get_config_vars('str_TitleExportCustomers'));

		// Build the export path for display to the user. Strip any trailing forward slashes that might be
		// included in the configuration parameter.
		$dataExportPath = preg_replace('/\/$/', '', $ac_config['PRIVATEDATAEXPORTPATH']) . '/Customer';
		$smarty->assign('dataExportPath', $dataExportPath);

        $smarty->assign('companyLogin', false);
        $smarty->assign('company', '');

		if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
		{
			$smarty->assign('companyLogin', true);
			$smarty->assign('company', $gSession['userdata']['companycode']);
		}

		$smarty->displayLocale('admin/customers/customerexport.tpl');
    }

	static function customerActivate($pResult)
	{
		$smarty = SmartyObj::newSmarty('AdminCustomers');

		if ($pResult === '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			//localise string for display
			$errorToDisplay = $smarty->get_config_vars($pResult);

			echo '{"success":false,	"msg":"' . $errorToDisplay . '"}';
		}
	}

}

?>
