<?php

class OgoneObj
{
    static function configure()
    {
        global $gSession;
        $resultArray = Array();

        AuthenticateObj::clearSessionCCICookie();

        $OgoneConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Ogone.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
        $currencyList = $OgoneConfig['OGONECURRENCIES'];
        $currency = $gSession['order']['currencyisonumber'];
        $active = true;

        // test for Ogone supported currencies
        if (strpos($currencyList, $currency) === false)
        {
            $active = false;
        }

        $resultArray['active'] = $active;
        $resultArray['form'] = '';
        $resultArray['scripturl'] = '';
        $resultArray['script'] = '';
        $resultArray['action'] = '';

        return $resultArray;
    }

    static function initialize()
    {
        global $gSession;

        $requestParameters = array();
        $parameters = array();

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$OgoneConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Ogone.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$returnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

			//Read settings from the config file.
			$server = $OgoneConfig['OGONESERVER'];
			$merchant = $OgoneConfig['PSPID'];
			$passPhrase = $OgoneConfig['PASSPHRASEIN'];
			$merchant = $OgoneConfig['PSPID'];
			$TITLE = $OgoneConfig['TITLE'];
			$BGCOLOR = $OgoneConfig['BGCOLOR'];
			$TXTCOLOR = $OgoneConfig['TXTCOLOR'];
			$TBLBGCOLOR = $OgoneConfig['TBLBGCOLOR'];
			$TBLTXTCOLOR = $OgoneConfig['TBLTXTCOLOR'];
			$BUTTONBGCOLOR = $OgoneConfig['BUTTONBGCOLOR'];
			$BUTTONTXTCOLOR = $OgoneConfig['BUTTONTXTCOLOR'];

			//Initialise variables
			$orderID = $gSession['ref'] . '_'. time();

			if (array_key_exists('ORDERDESCRIPTION', $OgoneConfig))
        	{
				if ($OgoneConfig['ORDERDESCRIPTION'] == '')
				{
					$orderData = '';
				}
				else
				{
					$orderData = UtilsObj::LeftChars($OgoneConfig['ORDERDESCRIPTION'], 20);
				}
			}
			else
			{
				$orderData = '';
			}

			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
			$currency = $gSession['order']['currencycode'];

			// specify the language that will be used on the payment page.
			$locale = strtolower($gSession['browserlanguagecode']);
			$locale = substr($locale, 0, 2);
			$languageList = 'en,nl,fr,de';

			if (strpos($languageList, $locale) === false)
			{
				$displayLang = 'en_US';
			}
			else
			{
				switch ($locale)
				{
					case 'en':
						$displayLang = 'en_US';
					break;
					case 'nl':
						$displayLang = 'nl_NL';
					break;
					case 'fr':
						$displayLang = 'fr_FR';
					break;
					case 'de':
						$displayLang = 'de_DE';
					break;
				}
			}

			$customerName = $gSession['order']['billingcontactfirstname'].''.$gSession['order']['billingcontactlastname'];
			$address1 = $gSession['order']['billingcustomeraddress1'];
			$postCode = $gSession['order']['billingcustomerpostcode'];
			$city = $gSession['order']['billingcustomercity'];
			$telephone = $gSession['order']['billingcustomertelephonenumber'];
			$email = $gSession['order']['billingcustomeremailaddress'];
			$country = $gSession['order']['billingcustomercountrycode'];

			$requestParameters = array(
				'PSPID'	=> $merchant,
				'ORDERID' => $orderID,
				'AMOUNT' => $amount,
				'CURRENCY' => $currency,
				'LANGUAGE' => $displayLang,
				'CN' => $customerName,
				'EMAIL' => $email,
				'OWNERADDRESS' => $address1,
				'OWNERZIP' => $postCode,
				'OWNERTOWN' => $city,
				'OWNERCTY' => $country,
				'OWNERTELNO' => $telephone,
				'COM' => $orderData,
				'ACCEPTURL' => $returnPath,
				'DECLINEURL' => $returnPath,
				'EXCEPTIONURL' => $returnPath,
				'CANCELURL' => $cancelReturnPath,
				'COMPLUS' => $gSession['ref'],
				'TITLE'	=> $TITLE,
				'BGCOLOR' => $BGCOLOR,
				'TXTCOLOR' => $TXTCOLOR,
				'TBLBGCOLOR' => $TBLBGCOLOR,
				'TBLTXTCOLOR' => $TBLTXTCOLOR,
				'BUTTONBGCOLOR' => $BUTTONBGCOLOR,
				'BUTTONTXTCOLOR' => $BUTTONTXTCOLOR
			);

			// Filter out empty values in the array. Empty values maybe due to
			// the parameters in the config file having no values.
			// Passing empty values in the array causes Ogone to fail.
			foreach($requestParameters as $key => $value)
			{
				if ($value != '')
				{
					$key = strtoupper($key);
					$parameters[$key] = $value;
				}
			}

			$hash = self::generateHash($parameters, $passPhrase);

			$shaHashArray = array('SHASIGN'	=> $hash);

			$params = array_merge($parameters, $shaHashArray);

			ksort($params);

			// define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('method', "POST");
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('parameter', $params);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to DIBS
			$gSession['order']['ccidata'] = 'start';
			DatabaseObj::updateSession();

			$smarty->cachePage = true; // allow the page to be cached so that the browser back button works correctly
			if ($gSession['ismobile'] == true)
            {
                $resultArray['template'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest_small.tpl');
                $resultArray['javascript'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest.tpl');
                return $resultArray;
            }
            else
            {
                $smarty->displayLocale('order/PaymentIntegration/PaymentRequest_large.tpl');
            }
    	}
        else
        {
            // the user has clicked the back button
            AuthenticateObj::clearSessionCCICookie();

            $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
            $smarty->assign('server', $cancelReturnPath);

            if ($gSession['ismobile'] == true)
            {
                $resultArray['template'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest_small.tpl');
                $resultArray['javascript'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest.tpl');
                return $resultArray;
            }
            else
            {
                $smarty->displayLocale('order/PaymentIntegration/PaymentRequest_large.tpl');
            }
        }
    }

    static function cancel()
    {
        global $gSession;

        $resultArray = Array();
        $resultArray['result'] = '';
        $resultArray['ref'] = $gSession['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function confirm($callback)
    {
     	global $gSession;

     	$resultArray = Array();
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $showError = false;
        $update = false;

     	$OgoneConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Ogone.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
		$passPhraseOut = $OgoneConfig['PASSPHRASEOUT'];

     	//Put return parameters into an array.
     	$returnParams = self::getReturnParams($callback);

		//Session Reference
		$ref = $returnParams['COMPLUS'];

		//Generate Return Hash
     	$returnHash = self::generateHash($returnParams, $passPhraseOut);

     	//Check CCILOG to see if this is an update
     	$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

		if (empty($cciLogEntry))
		{
			// no entry yet, this must be the first callback
			// we do have a session
			$webbrandcode = $gSession['webbrandcode'];
			$currencyCode = $gSession['order']['currencycode'];
			$amount = $gSession['order']['ordertotaltopay'];
			$update = false;
			$parentLogId = -1;
			$orderId = -1;
		}
		else
		{
			// we already have an entry, this must be a status update
			// we won't have a session
			$webbrandcode = $cciLogEntry['webbrandcode'];
			$currencyCode = $cciLogEntry['currencycode'];
			$amount = $cciLogEntry['formattedamount'];
			$update = true;
			$parentLogId = $cciLogEntry['id'];
			$orderId = $cciLogEntry['orderid'];
		}

     	if ($returnHash == $returnParams['SHASIGN'])
     	{
     		switch ($returnParams['STATUS'])
			{
				case '0':
					 $authorised = true;
					 $authorisedStatus = 0;
				break;
				case '2':
					 $authorisedStatus = 2;
				break;
				case '5':
					 $authorised = true;
					 $authorisedStatus = 3;
				break;
				case '9':
					 $authorised = true;
					 $authorisedStatus = 1;
				break;
				case '51':
					 $authorisedStatus = 4;
				break;
				case '52':
					 $authorisedStatus = 5;
				break;
				case '91':
					 $authorisedStatus = 6;
				break;
				case '92':
					 $authorisedStatus = 7;
				break;
				case '93':
					 $authorisedStatus = 8;
				break;
			}
     	}
     	else
     	{
     		// SHA1 check failed
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': SHA1KEY';
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': SHA1 check failed';
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $returnParams['PAYID'];
			$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $returnParams['ORDERID'];
			$resultArray['errorform'] = 'error.tpl';
			$showError = true;
			$authorisedStatus = 0;
     	}

		$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime =  date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($OgoneConfig, $serverTimestamp);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] ='';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $returnParams['PAYID'];
        $resultArray['formattedtransactionid'] = $returnParams['PAYID'];
        $resultArray['responsecode'] = $returnParams['ACCEPTANCE'];
        $resultArray['responsedescription'] = '';
        $resultArray['authorisationid'] = $returnParams['PAYID'];  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $returnParams['PAYID'];
        $resultArray['bankresponsecode'] = $returnParams['ACCEPTANCE'];
        $resultArray['cardnumber'] = $returnParams['CARDNO'];
        $resultArray['formattedcardnumber'] = $returnParams['CARDNO'];
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = $returnParams['PM'];
        $resultArray['paymenttime'] = $serverTime;
		$resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $serverTimestamp;
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = '';
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = '';
        $resultArray['business'] = '';
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = '';
        $resultArray['settleamount'] = '';
        $resultArray['currencycode'] = $currencyCode;
        $resultArray['webbrandcode'] = $webbrandcode;

        $resultArray['charityflag'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = $orderId;
        $resultArray['parentlogid'] = $parentLogId;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = $showError;

        return $resultArray;

    }

    static function generateHash($pParams, $pPassPhrase)
    {
        $hash = '';
        ksort($pParams);

        foreach ($pParams as $key => $val)
        {
    		if ($key != 'SHASIGN')
    		{
    			if ($val != "")
    			{
    				$hash .= $key."=" . $val . $pPassPhrase;
    			}
			}
		}

		$generatedHash = sha1($hash);

		$generatedHash = strtoupper($generatedHash);

        return $generatedHash;
    }


    static function getReturnParams($pCallback)
    {
		$resultArray = Array();

		if ($pCallback == 'automatic')
		{
			$requestType = $_POST;
		}
		else
		{
			$requestType = $_GET;
		}

		foreach($requestType as $key => $value)
		{
			if ($key != 'ref' && $key != 'fsaction')
			{
				$key = strtoupper($key);
				$resultArray[$key] = $value;
			}
		}

		ksort($resultArray);

		return $resultArray;
    }
}

?>