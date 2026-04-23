<?php
class EasyPay2Obj
{
    static function configure()
    {
        global $gSession;

		$gateways = '';
        $resultArray = array();
		$currency = $gSession['order']['currencycode'];
		$active = false;

        AuthenticateObj::clearSessionCCICookie();

        // read config file
		$EasyPay2Config = PaymentIntegrationObj::readCCIConfigFile('../config/EasyPay2.conf',$currency,$gSession['webbrandcode']);

		// currencies supported: SGD, MYR, USD, AUD, JPY, THB, CNY, BND, VND
		$currencyList = '702,458,840,036,392,764,156,096,704';

		// make sure merchant details are set
		if ($EasyPay2Config['MERCHANTID'] == '' || $EasyPay2Config['SERVER'] == '')
		{
			$active = false;
		}
		elseif (strpos($currencyList, $gSession['order']['currencyisonumber']) === false)
        {
			$active = false;
        }
        else
        {
	        $locale = strtolower($gSession['browserlanguagecode']);
			$locale = substr($locale, 0, 2);
        	$active = true;
        }

        $resultArray['gateways'] = $gateways;
        $resultArray['active'] = $active;
        $resultArray['form'] = "";
        $resultArray['scripturl'] = '';
        $resultArray['script'] = "";
        $resultArray['action'] = "";

        return $resultArray;
    }


    static function initialize()
    {
        global $gSession;

        $parameters = array();

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$EasyPay2Config = PaymentIntegrationObj::readCCIConfigFile('../config/EasyPay2.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

  		    $responseURL = UtilsObj::correctPath($gSession['webbrandweburl'])."?fsaction=Order.ccManualCallback&ref=".$gSession['ref'];
			$automaticCallbackURL = UtilsObj::correctPath($gSession['webbrandwebroot'])."PaymentIntegration/EasyPay2/EasyPay2Callback.php?ref=".$gSession['ref'];

			$cancelReturnURL  = UtilsObj::correctPath($gSession['webbrandweburl'])."?fsaction=Order.ccCancelCallback&ref=".$gSession['ref'];

			// Convert language code to EasyPay2's format
			switch($gSession['browserlanguagecode'])
			{
				case 'ja':
					$langCode = 'ja_JP';
					break;
				case 'zh_cn':
					$langCode = 'zh_CN';
					break;
				case 'zh_tw':
					$langCode = 'zh_TW';
					break;
				case 'es':
					$langCode = 'es_ES';
					break;
				case 'ko':
					$langCode = 'ko_KR';
					break;
				default:
					$langCode = 'en_US';
			}


			$parameters['mid'] = $EasyPay2Config['MERCHANTID'];
			$parameters['ref'] = $gSession['ref'] . "_" . time();
			$parameters['cur'] = $gSession['order']['currencycode'];
			$parameters['amt'] = $gSession['order']['ordertotaltopay'];
			$parameters['transtype'] = 'sale';
			$parameters['version'] = '2';

			$parameters['locale'] = $langCode;

			$parameters['returnurl'] = $responseURL;
			$parameters['statusurl'] = $automaticCallbackURL;

			$parameters['paytype'] = '';
			$parameters['userfield1'] = '';
			$parameters['userfield2'] = '';
			$parameters['userfield3'] = '';
			$parameters['userfield4'] = '';
			$parameters['userfield5'] = '';
			$parameters['skipstatuspage'] = 'Y'; // Don't Display EasyPay Status Page so customers will be transfered back to Taopix
			$parameters['rcard'] = '04'; // show only the last 4 digits of the credit card
			$parameters['firstname'] = $gSession['order']['billingcontactfirstname'];
			$parameters['lastname'] = $gSession['order']['billingcontactlastname'];
			$parameters['phone'] = $gSession['order']['billingcustomertelephonenumber'];
			$parameters['email'] = $gSession['order']['billingcustomeremailaddress'];
			$parameters['signature'] = self::createSignature($parameters);

			// define Smarty variables
			$smarty->assign('cancel_url', $cancelReturnURL);
			$smarty->assign('payment_url', $EasyPay2Config['SERVER']);
			$smarty->assign('method', 'POST');
			$smarty->assign('parameter', $parameters);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to EasyPay2
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
            $smarty->assign('payment_url', $cancelReturnPath);
            $smarty->assign('cancel_url', $cancelReturnPath);

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


	static function createSignature($pParams)
	{
        global $gSession;

		$signature = '';

		$EasyPay2Config = PaymentIntegrationObj::readCCIConfigFile('../config/EasyPay2.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

		$securitySequence = $EasyPay2Config['SECURITYSEQUENCE'];

		$pieces = explode(',', $securitySequence);
		foreach ($pieces as $paramKey)
		{
			if (array_key_exists(trim($paramKey), $pParams))
			{
				$signature .= $pParams[$paramKey];
			}
		}

		$signature = hash('sha512', $signature.$EasyPay2Config['SECURITYKEY']);

		return $signature;
	}


    static function cancel()
    {
        $resultArray = array();
        $resultArray['result'] = '';

        $resultArray['ref'] = $_GET['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }


    static function manualCallback()
    {
        global $gSession;

    	// all we have is the session reference
    	$ref = $gSession['ref'];
		$authorised = 0;
		$resultArray = PaymentIntegrationObj::getCciLogEntry($ref);
        $resultArray['result'] = '';

		switch ($resultArray['payerstatus'])
		{
			case 'YES':
				$paymentReceived = 1;
				$authorised = 1;
				break;
			case 'NO':
			default:
				$paymentReceived = 0;
				$authorised = 0;
		}

		$resultArray['ref'] = $ref;
        $resultArray['authorisedstatus'] = $authorised;
        $resultArray['showerror'] = false;
        $resultArray['paymentreceived'] = $paymentReceived;

        return $resultArray;
    }


    static function automaticCallback()
    {
		// include the email creation module
    	require_once('../Utils/UtilsEmail.php');

        global $gSession;

        $resultArray = Array();
        $authorised = false;
		$active = true;
		$showError = false;
		$status = '';
		$paymentReceived = 0;

        $ref = $_GET['ref'];

		$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);
		if (empty($cciLogEntry))
		{
			// no entry yet, this must be the first callback
			// we do have a session
			$webbrandcode = $gSession['webbrandcode'];
			$currencyCode = $gSession['order']['currencycode'];
			$update = false;
			$parentLogId = 0;
			$orderId = 0;
		}
		else
		{
			// we already have an entry, this must be a status update
			// we won't have a session
			$webbrandcode = $cciLogEntry['webbrandcode'];
			$currencyCode = $cciLogEntry['currencycode'];
			$update = true;
			$parentLogId = $cciLogEntry['id'];
			$orderId = $cciLogEntry['orderid'];
		}

		$EasyPay2Config = PaymentIntegrationObj::readCCIConfigFile('../config/EasyPay2.conf', $currencyCode,$gSession['webbrandcode']);

        // read POST variables
		$transactionID = UtilsObj::getPOSTParam('TM_RefNo');
		$amount = UtilsObj::getPOSTParam('TM_DebitAmt');
		$status = UtilsObj::getPOSTParam('TM_Status');
		$errorMsg = UtilsObj::getPOSTParam('TM_ErrorMsg');
		$paymentType = UtilsObj::getPOSTParam('TM_PaymentType');
		$approvalCode = UtilsObj::getPOSTParam('TM_ApprovalCode');
		$bankResponseCode = UtilsObj::getPOSTParam('TM_BankRespCode');
		$errorCode = UtilsObj::getPOSTParam('TM_Error');
		$creditCardNumber = UtilsObj::getPOSTParam('TM_CCNum');

		if ($errorCode == '')
		{
			switch ($status)
			{
				// These code means transaction has successed or currently under validation.
				case "YES":
					$active = true;
					$authorised = true;
					$paymentReceived = 1;
					break;
				case "NO":
					$active = true;
					$authorised = false;
					$paymentReceived = 0;
					break;
			}

			// send status tion email if
			// 1. this is an update
			// 2. the status has indeed changed
			// 3. new status is not 'paid' (otherwise we get an email on every payment)
			if ($update && ($status != $cciLogEntry['payerstatus']))
			{
				$offlineConfirmationName = $EasyPay2Config['OFFLINECONFIRMATIONNAME'];
				$offlineConfirmationEmailAddress = $EasyPay2Config['OFFLINECONFIRMATIONEMAILADDRESS'];
				if ($offlineConfirmationEmailAddress != '')
				{
					$smarty = SmartyObj::newSmarty('Order');
					$emailContent = $smarty->get_config_vars('str_LabelOrderNumber') . ': ' . $cciLogEntry['ordernumber'] . "\n" .
									$smarty->get_config_vars('str_LabelTransactionID') . ': ' . $cciLogEntry['transactionid'] . "\n" .
									$smarty->get_config_vars('str_LabelStatus') . ': ' . $status . "\n\n";
					$emailObj = new TaopixMailer();

					$emailObj->sendTemplateEmail('admin_offlinepaymentupdate', $webbrandcode, '', '', '', $offlineConfirmationName,
						$offlineConfirmationEmailAddress, '', '', 0, array('data' => $emailContent), false);
				}
			}
		}
		else
		{
     		// sig check failed
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . $errorCode;
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $errorMsg;
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $ref;
			$resultArray['data4'] = '';
			$resultArray['errorform'] = 'error.tpl';
			$showError = true;
     	}

		// write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
		PaymentIntegrationObj::logPaymentGatewayData($EasyPay2Config, $serverTimestamp);

		$serverTime = date('H:i:s');

        $resultArray['result'] = $status;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '000';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorised;
        $resultArray['transactionid'] = $transactionID;  // this is our unique ID, not the real order ID
        $resultArray['formattedtransactionid'] = $transactionID;
        $resultArray['responsecode'] = $bankResponseCode;
        $resultArray['responsedescription'] = $bankResponseCode;
        $resultArray['authorisationid'] = $approvalCode;
        $resultArray['formattedauthorisationid'] = $approvalCode;
        $resultArray['bankresponsecode'] = $bankResponseCode;
        $resultArray['cardnumber'] = $creditCardNumber;
        $resultArray['formattedcardnumber'] = $creditCardNumber;
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $serverTimestamp;
        $resultArray['paymentmeans'] = $paymentType;
        $resultArray['paymenttime'] = $serverTime;
        $resultArray['paymentreceived'] = $paymentReceived;
        $resultArray['formattedpaymentdate'] = $serverTimestamp;
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = '';
        $resultArray['payerstatus'] = $status;
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

}

?>