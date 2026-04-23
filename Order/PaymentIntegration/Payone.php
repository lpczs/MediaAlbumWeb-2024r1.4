<?php
class PayoneObj
{
    static function configure()
    {
        global $gSession;

		$gateways = Array();
        $resultArray = Array();
		$currency = $gSession['order']['currencycode'];
		$active = false;

		$smarty = SmartyObj::newSmarty('CreditCardPayment');

        AuthenticateObj::clearSessionCCICookie();

        // read config file
		$PayoneConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Payone.conf',$currency,$gSession['webbrandcode']);
		$gatewaysArray = explode(',', $PayoneConfig['PAYONEGATEWAYS']);

		foreach ($gatewaysArray as $gateway)
		{
			// test for Payone supported countries

			switch ($gateway)
        	{
				case 'ELV':
					// only in Germany, Austria and the Netherlands
					if (in_array($gSession['order']['billingcustomercountrycode'], array('DE','AT','NL')))
					{
						$gateways[$gateway] = $smarty->get_config_vars('str_OrderPayone_' . $gateway);
						$active = true;
					}
					break;
				default:
					$gateways[$gateway] = $smarty->get_config_vars('str_OrderPayone_' . $gateway);
					$active = true;
					break;
			}
		}

		// make sure merchant details are set
		if (($PayoneConfig['AID'] == '') or ($PayoneConfig['PORTALID'] == ''))
		{
			$active = false;
		}

        $resultArray['gateways'] = $gateways;
        $resultArray['active'] = $active;
        $resultArray['form'] = '';
        $resultArray['scripturl'] = '';
        $resultArray['script'] = '';
        $resultArray['action'] = '';

        return $resultArray;
    }

    static function initialize()
    {
        global $ac_config;
        global $gConstants;
        global $gSession;

        $parameters = Array();

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$PayoneConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Payone.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

			$successReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

			// amount in smallest unit
			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
			$description = LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);

			$parameters['aid'] = $PayoneConfig['AID'];
			$parameters['portalid'] = $PayoneConfig['PORTALID'];
			$parameters['mode'] = ($PayoneConfig['MODE'] == 'LIVE') ? 'live' : 'test';
			$parameters['request'] = 'authorization';
			$parameters['encoding'] = 'UTF-8';
			$parameters['clearingtype'] = strtolower($gSession['order']['paymentgatewaycode']);
			$parameters['reference'] = $gSession['ref'] . '_' . time();
			$parameters['customerid'] = $gSession['userid'];
			$parameters['param'] = $gSession['ref'];
			$parameters['display_name'] = ($PayoneConfig['DISPLAYNAME'] == 0) ? 'no' : 'yes';
			$parameters['display_address'] = ($PayoneConfig['DISPLAYADDRESS'] == 0) ? 'no' : 'yes';
			$parameters['successurl'] = $successReturnPath;
			$parameters['backurl'] = $cancelReturnPath;
			$parameters['amount'] = $amount;
			$parameters['currency'] = $gSession['order']['currencycode'];
			$parameters['id[1]'] = UtilsObj::leftChars($description, 32);
			$parameters['pr[1]'] = $amount;
			$parameters['no[1]'] = 1;
			$parameters['de[1]'] = $gSession['items'][0]['itemqty'] . ' x ' . $description;

			// parameters have to be sorted before they are hashed
			ksort($parameters);

			// build hash
			$parameters['hash'] = md5(implode('', $parameters) . $PayoneConfig['MD5KEY']);

			// Personendaten / personal data
			$parameters['firstname'] = $gSession['order']['billingcontactfirstname'];
			$parameters['lastname'] = $gSession['order']['billingcontactlastname'];
			$parameters['company'] = $gSession['order']['billingcustomername'];
			$parameters['street'] = $gSession['order']['billingcustomeraddress1'];
			$parameters['zip'] = $gSession['order']['billingcustomerpostcode'];
			$parameters['city'] = $gSession['order']['billingcustomercity'];
			$parameters['country'] = $gSession['order']['billingcustomercountrycode'];
			$parameters['email'] = $gSession['order']['billingcustomeremailaddress'];
			$parameters['telephonenumber'] = $gSession['order']['billingcustomertelephonenumber'];
			$parameters['language'] = $gSession['browserlanguagecode'];

			// Lieferdaten / shipping data
			$parameters['shipping_firstname'] = $gSession['shipping'][0]['shippingcontactfirstname'];
			$parameters['shipping_lastname'] = $gSession['shipping'][0]['shippingcontactlastname'];
			$parameters['shipping_company'] = $gSession['shipping'][0]['shippingcustomername'];
			$parameters['shipping_street'] = $gSession['shipping'][0]['shippingcustomeraddress1'];
			$parameters['shipping_zip'] = $gSession['shipping'][0]['shippingcustomerpostcode'];
			$parameters['shipping_city'] = $gSession['shipping'][0]['shippingcustomercity'];
			$parameters['shipping_country'] = $gSession['shipping'][0]['shippingcustomercountrycode'];

			// do the payment request
			$active = true;
			$error = '';

			// define Smarty variables
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('payment_url', $PayoneConfig['SERVER']);
			$smarty->assign('method', 'POST');
			$smarty->assign('parameter', $parameters);


			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to Payone
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

    static function cancel() // is this ever being called?
    {
        $resultArray = Array();
        $result = '';
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

		$resultArray = PaymentIntegrationObj::getCciLogEntry($ref);
        $resultArray['result'] = '';

		switch ($resultArray['bankresponsecode']) // alias 'txaction'
		{
			case 'appointed':
			case 'paid':
				$paymentReceived = 1;
				break;
			case 'capture':
			case 'underpaid':
			case 'cancelation':
			case 'refund':
			case 'debit':
			case 'reminder':
			default:
				$paymentReceived = 0;
		}

        $resultArray['ref'] = $ref;
        $resultArray['showerror'] = false;
        $resultArray['paymentreceived'] = $paymentReceived;

        return $resultArray;
    }

    static function automaticCallback()
    {
    	// include the email creation module
    	require_once('../Utils/UtilsEmail.php');

        global $ac_config;
        global $gSession;

        $resultArray = Array();
        $result = '';
        $authorised = false;
		$active = true;
		$error = '';
		$status = '';
		$description = '';

        $ref = $_GET['ref'];

		$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

		if (empty($cciLogEntry))
		{
			// no entry yet, this must be the first callback
			// we do have a session
			$webbrandcode = $gSession['webbrandcode'];
			$currencyCode = $gSession['order']['currencycode'];
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
			$update = true;
			$parentLogId = $cciLogEntry['id'];
			$orderId = $cciLogEntry['orderid'];
		}

		$PayoneConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Payone.conf',$currencyCode,$webbrandcode);

        // read POST variables
		$key = UtilsObj::getPOSTParam('key');
		$txaction = UtilsObj::getPOSTParam('txaction');
		$mode = UtilsObj::getPOSTParam('mode');
		$portalid = UtilsObj::getPOSTParam('portalid');
		$aid = UtilsObj::getPOSTParam('aid');
		$clearingtype = UtilsObj::getPOSTParam('clearingtype');
		$txid = UtilsObj::getPOSTParam('txid');
		$reference = UtilsObj::getPOSTParam('reference');
		$sequencenumber = UtilsObj::getPOSTParam('sequencenumber');
		$txtime = UtilsObj::getPOSTParam('txtime');
		$receivable = UtilsObj::getPOSTParam('receivable');
		$balance = UtilsObj::getPOSTParam('balance');
		$userid = UtilsObj::getPOSTParam('userid');

		$price = UtilsObj::getPOSTParam('price');
		$customerid = UtilsObj::getPOSTParam('customerid');
		$failedcause = UtilsObj::getPOSTParam('failedcause');

		$hashArray = compact($key, $txaction, $mode, $portalid, $aid, $clearingtype, $txid, $reference, $sequencenumber,
							$txtime, $currencyCode, $receivable, $balance, $userid);

		// parameters have to be sorted before they are hashed
		ksort($hashArray);

		// build hash
		$hash = md5(implode('', $hashArray) . $PayoneConfig['MD5KEY']);

		if ($hash != $key)
		{
			// we have discrepancies
			$error = 'Incorrect Signature.';
			$active = false;
			$authorised = false;
			$authorisedStatus = 10;
			$paymentReceived = 0;
		}

		// no errors? proceed!
		if ($error == '')
		{
			// we continue because there was no error so far
			// and we have a successful payment
			switch ($txaction)
			{
				case 'appointed':
					$active = true;
					$authorised = true;
					$authorisedStatus = 1;
					$paymentReceived = 0;
					break;
				case 'capture':
					$active = true;
					$authorised = true;
					$authorisedStatus = 2;
					$paymentReceived = 0;
					break;
				case 'paid':
					$active = true;
					$authorised = true;
					$authorisedStatus = 3;
					$paymentReceived = 1;
					break;
				case 'underpaid':
					$active = true;
					$authorised = true;
					$authorisedStatus = 4;
					$paymentReceived = 0;
					break;
				case 'cancelation':
					$active = true;
					$authorised = true;
					$authorisedStatus = 5;
					$paymentReceived = 0;
					break;
				case 'refund':
					$active = true;
					$authorised = true;
					$authorisedStatus = 6;
					$paymentReceived = 0;
					break;
				case 'debit':
					$active = true;
					$authorised = true;
					$authorisedStatus = 7;
					$paymentReceived = 0;
					break;
				case 'reminder':
					$active = true;
					$authorised = true;
					$authorisedStatus = 8;
					$paymentReceived = 0;
					break;
				default:
					$error = 'Unexpected result.';
					$active = false;
					$authorised = false;
					$authorisedStatus = 9;
					$paymentReceived = 0;
			}

			// send status change notification email if
			// 1. this is an update
			// 2. the status has indeed changed
			// 3. new status is not 'paid' (otherwise we get an email on every payment)
			if ($update && ($txaction != $cciLogEntry['responsecode']) && ($txaction != 'paid'))
			{
				$offlineConfirmationName = $PayoneConfig['OFFLINECONFIRMATIONNAME'];
				$offlineConfirmationEmailAddress = $PayoneConfig['OFFLINECONFIRMATIONEMAILADDRESS'];
				if ($offlineConfirmationEmailAddress != '')
				{
					$smarty = SmartyObj::newSmarty('Order');
					$emailContent = $smarty->get_config_vars('str_LabelOrderNumber') . ': ' . $cciLogEntry['ordernumber'] . "\n" .
									$smarty->get_config_vars('str_LabelTransactionID') . ': ' . $cciLogEntry['transactionid'] . "\n" .
									$smarty->get_config_vars('str_LabelStatus') . ': ' . $txaction . "\n\n";
					$emailObj = new TaopixMailer();

					$emailObj->sendTemplateEmail('admin_offlinepaymentupdate', $webbrandcode, '', '', '',
						$offlineConfirmationName, $offlineConfirmationEmailAddress, '', '',
						0,
						Array('data' => $emailContent));
				}
			}
		}

		// write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d',$txtime);
		$serverTime = date('H:i:s',$txtime);

		PaymentIntegrationObj::logPaymentGatewayData($PayoneConfig, $serverTimestamp, $error);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $price;
        $resultArray['formattedamount'] = $price;
        $resultArray['charges'] = '000';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $reference;  // this is our unique ID, not the real order ID
        $resultArray['formattedtransactionid'] = $reference;
        $resultArray['responsecode'] = $txaction;
        $resultArray['responsedescription'] = $failedcause;
        $resultArray['authorisationid'] = $txid;
        $resultArray['formattedauthorisationid'] = $txid;
        $resultArray['bankresponsecode'] = '';
        $resultArray['cardnumber'] = UtilsObj::getPOSTParam('cardpan');
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = UtilsObj::getPOSTParam('cardtype');
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = '';
        $resultArray['paymenttime'] = $serverTime;
        $resultArray['paymentreceived'] = $paymentReceived;
        $resultArray['formattedpaymentdate'] = $serverTimestamp;
        $resultArray['addressstatus'] = $receivable;
        $resultArray['postcodestatus'] = $balance;
        $resultArray['payerid'] = $customerid;
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = '';
        $resultArray['business'] = $portalid;
        $resultArray['receiveremail'] = $aid;
        $resultArray['receiverid'] = $mode;
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = $clearingtype;
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
    	$resultArray['showerror'] = false;

        return $resultArray;
    }

	static function PayoneSignature($pSource)
	{
		$pSource = sha1($pSource);
		$len = strlen($pSource);
		$bin = '';
		for ($i = 0 ; $i < $len ; $i += 2)
		{
			$bin .= chr(hexdec(substr($pSource,$i,2)));
		}
		return base64_encode($bin);
	}

}

?>