<?php

class PayFortObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();

        $active = true;

        $payfortConfig = PaymentIntegrationObj::readCCIConfigFile('../config/payfort.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
        $currencyList = $payfortConfig['PAYFORTCURRENCIES'];

        if (($payfortConfig['SERVER'] == '') || ($payfortConfig['MERCHANTID'] == '') || ($payfortConfig['ACCESSCODE'] == '') ||
			($payfortConfig['SHAREQUESTPHRASE'] == '') || ($payfortConfig['SHAREPONSEPHRASE'] == ''))
        {
            $active = false;
        }

        if (strpos($currencyList, $gSession['order']['currencyisonumber']) === false)
        {
            $active = false;
        }

        AuthenticateObj::clearSessionCCICookie();

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

        $requestParameters = Array();

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$payfortConfig = PaymentIntegrationObj::readCCIConfigFile('../config/payfort.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$returnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

			//Read settings from the config file.
			$server = $payfortConfig['SERVER'];
			$merchantID = $payfortConfig['MERCHANTID'];
			$accessCode = $payfortConfig['ACCESSCODE'];
			$passPhrase = $payfortConfig['SHAREQUESTPHRASE'];

			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
			$description = $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
			$currency = $gSession['order']['currencycode'];

			// specify the language that will be used on the payment page.
			$locale = strtolower($gSession['browserlanguagecode']);
			$locale = substr($locale, 0, 2);
			$languageList = 'en,ar';

			if (strpos($languageList, $locale) === false)
			{
				$displayLang = 'en';
			}
			else
			{
				$displayLang = $locale;
			}

			$email = $gSession['order']['billingcustomeremailaddress'];
			$ordernumber = $gSession['ref'] . '_' . time();

			$requestParameters = array(
				'access_code' => $accessCode,
				'amount' => $amount,
				'currency' => $currency,
				'customer_email' => $email,
				'merchant_reference' => $ordernumber,
				'order_description' => $description,
				'language' => $displayLang,
				'return_url' => $returnPath,
				'merchant_identifier' => $merchantID,
				'merchant_extra' => $gSession['ref'],
				'command' => 'PURCHASE'
			);

			$hash = self::generateHash($requestParameters, $passPhrase);

			$requestParameters['signature'] = $hash;

			// define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('method', "POST");
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('parameter', $requestParameters);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to Payfort
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
        $resultArray = Array();

        $resultArray['result'] = '';
        $resultArray['ref'] = $_GET['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['authorisedstatus'] = 0;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function confirm($pCallBack)
    {
     	global $gSession;

     	$resultArray = Array();
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $showError = false;
        $update = false;

     	$payfortConfig = PaymentIntegrationObj::readCCIConfigFile('../config/payfort.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
		$passPhraseOut = $payfortConfig['SHAREPONSEPHRASE'];

     	//Put return parameters into an array.
     	$returnParams = self::getReturnParams($pCallBack);

		//Session Reference
		$ref = $returnParams['merchant_extra'];

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

     	if ($returnHash == $returnParams['signature'])
     	{
     		switch ($returnParams['status'])
			{
				case '14':
					$authorised = true;
					$authorisedStatus = 1;
					break;

				default:
					// Unexpected status code
					// Note that some status codes still mean success (e.g. 06 - Refund Success), but they're not what we're expecting
					$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $returnParams['status'];
					$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . SmartyObj::getParamValue('CreditCardPayment', 'str_OrderTransactionFailed');
					$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $returnParams['fort_id'];
					$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $returnParams['merchant_reference'];
					$resultArray['errorform'] = 'error.tpl';
					$showError = true;
					$authorisedStatus = 0;
					break;
			}
     	}
     	else
     	{
     		// SHA256 check failed
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': SHA256KEY';
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': SHA256 check failed';
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $returnParams['fort_id'];
			$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $returnParams['merchant_reference'];
			$resultArray['errorform'] = 'error.tpl';
			$showError = true;
			$authorisedStatus = 0;
     	}

		$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime =  date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($payfortConfig, $serverTimestamp);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] ='';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $returnParams['fort_id'];
        $resultArray['formattedtransactionid'] = $returnParams['fort_id'];
        $resultArray['responsecode'] = $returnParams['response_code'];
        $resultArray['responsedescription'] = '';
        $resultArray['authorisationid'] = $returnParams['fort_id'];  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $returnParams['fort_id'];
        $resultArray['bankresponsecode'] = $returnParams['response_code'];
        $resultArray['cardnumber'] = $returnParams['card_number'];
        $resultArray['formattedcardnumber'] = $returnParams['card_number'];
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = '';
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
        $resultArray['resultlist'] = array();
    	$resultArray['showerror'] = $showError;

        return $resultArray;
    }

    static function generateHash($pParams, $pPassPhrase)
    {
        $hash = '';
        ksort($pParams);

        foreach ($pParams as $key => $val)
        {
    		if ($key != 'signature')
    		{
    			if ($val != '')
    			{
    				$hash .= $key . '=' . $val;
    			}
			}
		}

		$hash = $pPassPhrase . $hash . $pPassPhrase;

		$generatedHash = hash('sha256', $hash);

		$generatedHash = strtolower($generatedHash);

        return $generatedHash;
    }

	static function getReturnParams($pCallback)
    {
		$resultArray = Array();

		$fortParams = array_merge($_GET, $_POST);
		foreach($fortParams as $key => $value)
		{
			if ($key != 'ref' && $key != 'fsaction')
			{
				$resultArray[$key] = $value;
			}
		}

		ksort($resultArray);

		return $resultArray;
    }
}

?>