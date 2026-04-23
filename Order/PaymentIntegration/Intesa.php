<?php
class IntesaObj
{
    static function configure()
    {
        global $gSession;

		$gateways = Array();
        $resultArray = Array();
		$currency = $gSession['order']['currencycode'];
		$active = true;

		$smarty = SmartyObj::newSmarty('CreditCardPayment');

        AuthenticateObj::clearSessionCCICookie();

        // read config file
		$IntesaConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Intesa.conf',$currency,$gSession['webbrandcode']);

		// make sure merchant details are set
		if (($IntesaConfig['SERVER'] == '') || ($IntesaConfig['TRANPORTALID'] == '') || ($IntesaConfig['TRANPORTALPWD'] == ''))
		{
			$active = false;
		}

		// make sure currency is EUR
		if ($gSession['order']['currencyisonumber'] != 978)
		{
			$active = false;
		}

        $resultArray['gateways'] = '';
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
			$IntesaConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Intesa.conf',
			$gSession['order']['currencycode'],$gSession['webbrandcode']);

			$successReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) .
			'?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) .
			'?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

		    $responseURL = UtilsObj::correctPath($gSession['webbrandweburl'])."PaymentIntegration/Intesa/IntesaCallback.php";

			$description = LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'],
			$gSession['browserlanguagecode'], true);

			// Convert normal language code to Intesa's code.
			switch ($gSession['browserlanguagecode'])
			{
				case "it":
					$languageCode = "ITA";
					break;
				case "en":
					$languageCode = "USA";
					break;
				case "fr":
					$languageCode = "FRA";
					break;
				case "de":
					$languageCode = "DEU";
					break;
				case "es":
					$languageCode = "ESP";
					break;
				case "sl":
					$languageCode = "SLO";
					break;
			}

			$parameters['id'] = $IntesaConfig['TRANPORTALID'];
			$parameters['password'] = $IntesaConfig['TRANPORTALPWD'];
			$parameters['action'] = 1; // 1 means Purchase, 4 means Authorisation
			$parameters['amt'] = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');
			$parameters['currencycode'] = $gSession['order']['currencyisonumber'];
			$parameters['langid'] = $languageCode;
			$parameters['responseURL'] = $responseURL;
			$parameters['errorURL'] = $cancelReturnPath;
			$parameters['trackid'] = $gSession['ref'] . '_' . time();
			$parameters['udf1'] = "PURCHASE"; // to be saved in ccilog as transactiontype (TAOPIX only support PURChASE)
			$parameters['udf2'] = $gSession['ref'];

			$hashString = sha1($parameters['id'].$parameters['password'].$parameters['amt'].
						  $parameters['currencycode'].$parameters['trackid'].$parameters['udf2']);
			$parameters['udf4'] = $hashString;

			$result = self::cURLPost($IntesaConfig['SERVER'], $parameters);

			// If Intesa returned Error then redirect customer to cancel page & display error
			if (substr($result, 0 , 7) == "!ERROR!")
			{
				$error = str_replace("!ERROR!-","", $result);
				header('Location:' . $cancelReturnPath . "&error=" . urlencode($error));
				exit;
			}
			else // If no error, split the paymentID & payment URL from data returned then redirect customer to payment URL
			{
				$dataReturned = explode(":", $result, 2);
				$paymentID = $dataReturned[0];
				$HPPUrl = $dataReturned[1];
				$paymentURL = $HPPUrl."?PaymentID=" . $paymentID;
			}

			// do the payment request
			$active = true;
			$error = '';

			// define Smarty variables
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('payment_url', $paymentURL);
			$smarty->assign('method', 'POST');
			$smarty->assign('parameter', '');

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

    static function cancel()
    {
    	$resultArray = Array();
    	$error = '';

    	if ($_GET['transid'])
    	{
	    	$resultArray['transactionid'] = $_GET['transid'];
    	}

       	// payment error
    	if ($_GET['error'])
    	{
			$error = urldecode($_GET['error']);
			$pieces = explode("-", $error, 2);
			$resultArray['responsecode'] = $pieces[0];
    		$resultArray['responsedescription'] = $pieces[1];
    	}
    	else
    	{
    		$resultArray['responsecode'] = 'ABORT';
    		$resultArray['responsedescription'] = 'TAOPIX: User pressed back button.';
    	}

    	$resultArray['ref'] = $_GET['ref'];
    	$resultArray['authorised'] = false;
    	$resultArray['authorisedstatus'] = 0;


		$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $resultArray['responsecode'];
		$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $resultArray['responsedescription'];
		if ($resultArray['transactionid'] != '')
		{
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $resultArray['transactionid'];
		}
		else
		{
			$resultArray['data3'] = '';
		}

		$resultArray['data4'] = '';
		$resultArray['errorform'] = 'error.tpl';
		$resultArray['showerror'] = true;


    	return $resultArray;
    }

    static function manualCallback()
    {
        global $gSession;

    	// all we have is the session reference
    	$ref = $gSession['ref'];

		$resultArray = PaymentIntegrationObj::getCciLogEntry($ref);
        $resultArray['result'] = '';

		switch ($resultArray['bankresponsecode'])
		{
			case 'CAPTURED':
				$paymentReceived = 1;
				break;
			case 'APPROVED':
			case 'NOT APPROVED':
			case 'NOT CAPTURED':
			case 'DENIED BY RISK':
			case 'HOST TIMEOUT':
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

		$IntesaConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Intesa.conf',$currencyCode,$webbrandcode);

        // read POST variables
		$paymentid = UtilsObj::getPOSTParam('paymentid');
		$tranid = UtilsObj::getPOSTParam('tranid');
		$result = UtilsObj::getPOSTParam('result');
		$auth = UtilsObj::getPOSTParam('auth');
		$postdate = UtilsObj::getPOSTParam('postdate');
		$trackid = UtilsObj::getPOSTParam('trackid');
		$ref = UtilsObj::getPOSTParam('ref');	// this ref provided by Intesa
		$taopixRef = UtilsObj::getPOSTParam('udf2'); // this is ref generated by Taopix
		$cardtype = UtilsObj::getPOSTParam('cardtype');
		$payinst = UtilsObj::getPOSTParam('payinst');
		$liability = UtilsObj::getPOSTParam('liability');
		$returnedHash = UtilsObj::getPOSTParam('udf4');
		$transactionType = UtilsObj::getPOSTParam('udf1');

		$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');

		switch ($result)
		{
			case 'APPROVED':
				$active = true;
				$authorised = true;
				$authorisedStatus = 1;
				$paymentReceived = 0;
				break;
			case 'NOT APPROVED':
				$active = true;
				$authorised = false;
				$authorisedStatus = 2;
				$paymentReceived = 0;
				break;
			case 'CAPTURED':
				$active = true;
				$authorised = true;
				$authorisedStatus = 3;
				$paymentReceived = 1;
				break;
			case 'NOT CAPTURED':
				$active = true;
				$authorised = false;
				$authorisedStatus = 4;
				$paymentReceived = 0;
				break;
			case 'DENIED BY RISK':
				$active = true;
				$authorised = false;
				$authorisedStatus = 5;
				$paymentReceived = 0;
				break;
			case 'HOST TIMEOUT':
				$active = false;
				$authorised = false;
				$authorisedStatus = 6;
				$paymentReceived = 0;
				break;
		}


		// send status change notification email if
		// 1. this is an update
		// 2. the status has indeed changed
		if ($update && $result != $cciLogEntry['responsedescription'])
		{
			$offlineConfirmationName = $IntesaConfig['OFFLINECONFIRMATIONNAME'];
			$offlineConfirmationEmailAddress = $IntesaConfig['OFFLINECONFIRMATIONEMAILADDRESS'];
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


        // write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
		PaymentIntegrationObj::logPaymentGatewayData($IntesaConfig, $serverTimestamp, $error);

		$serverDate = date('Y-m-d');
		$serverTime = date('H:i:s');

        $resultArray['result'] = $result;
        $resultArray['ref'] = $taopixRef;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '000';
        $resultArray['formattedcharges'] = '000';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $tranid;
        $resultArray['formattedtransactionid'] = $tranid;
        $resultArray['responsecode'] = $trackid;
        $resultArray['responsedescription'] = $result;
        $resultArray['authorisationid'] = $auth;
        $resultArray['formattedauthorisationid'] = $auth;
        $resultArray['bankresponsecode'] = $result;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = $cardtype;
        $resultArray['cvvresponsecode'] = $auth;
        $resultArray['paymentcertificate'] = $paymentid;
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = '';
        $resultArray['paymenttime'] = $serverTime;
        $resultArray['paymentreceived'] = $paymentReceived;
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
        $resultArray['transactiontype'] = $transactionType;
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

	static function cURLPost($pURL, $pParamArray)
    {
    	//url-ify the data for the POST
		$paramterString = http_build_query($pParamArray);

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL,$pURL);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $paramterString);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($ch);
    	//close connection
		curl_close($ch);

		return $result;
    }


}

?>