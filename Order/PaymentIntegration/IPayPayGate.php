<?php
class IPayPayGateObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();
        AuthenticateObj::clearSessionCCICookie();

        // iPayPayGate supported currencies
        $currencyList = '764,840';
        $platnosciConfig = PaymentIntegrationObj::readCCIConfigFile('../config/IPayPayGate.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

        if (strpos($currencyList, $gSession['order']['currencyisonumber']) === false)
        {
			$active = false;
        }
        else
        {
			$active = true;
			$locale = strtolower($gSession['browserlanguagecode']);
			$locale = substr($locale, 0, 2);
        }

		$resultArray['gateways'] = '';
        $resultArray['active'] = $active;
        $resultArray['form'] = "";
        $resultArray['scripturl'] = '';
        $resultArray['action'] = "";
        return $resultArray;
    }

    static function initialize()
    {

        global $gSession;
        $timestamp = time();


        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser

        if ($gSession['order']['ccidata'] == '')
        {

			$ipayPayGate = PaymentIntegrationObj::readCCIConfigFile('../config/IPayPayGate.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
			$server = $ipayPayGate['SERVER'];

			// selected payment gateway
			$merchantID 	= $ipayPayGate['MERCHANTID'];

			$amount 		= number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');
			$orderRef   	= $gSession['ref'];
			$currencyCode 	= $gSession['order']['currencyisonumber'];

			$successUrl 	= UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback';
			$cancelUrl 		= UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref='.$orderRef;
			$failUrl 		= UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref='.$orderRef;

			$lang			= strtoupper($gSession['browserlanguagecode']);
			$lang 			= substr($lang, 0, 1);
			$payType 		= "N";
			$timestamp 		= time();
			$remark			= "";
			$payMethod 		= "CC";
			$redirect 		= "3";

			$params = array(
				'orderRef'			=> $orderRef,
				'amount'			=> $amount,
				'currCode'			=> $currencyCode,
				'lang'				=> $lang,
				'cancelUrl'			=> $cancelUrl,
				'failUrl'			=> $failUrl,
				'successUrl'		=> $successUrl,
				'merchantId'		=> $merchantID,
				'payType'			=> $payType,
				'payMethod'			=> $payMethod,
				'remark'			=> $remark,
				'redirect'			=> $redirect,

			);

			// define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('cancelurl', $cancelReturnPath);
			$smarty->assign('parameter', $params);
			$smarty->assign('script', '');
			$smarty->assign('method', "POST");

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

        $resultArray = Array();
        $result = '';
        $resultArray['result'] = '';

        $resultArray['ref'] = $_GET['Ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function manualCallback()
    {

        global $ac_config;
        global $gSession;

        $resultArray = Array();
        $result = '';
        $authorised = false;
		$active = true;
		$error = '';
		$status = '';
		$description = '';

        $ref = $_GET['Ref'];

		$resultArray = PaymentIntegrationObj::getCciLogEntry($ref);

		if (!empty($resultArray))
		{
			if($resultArray['responsecode'] == 0)
			{
				$paymentReceived = 1;
				$authorised = true;
			}else{
				$paymentReceived = 0;
			}
		}
      	$resultArray['authorised'] = $authorised;
		$resultArray['update'] = false;
        $resultArray['ref'] = $ref;
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
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $showError = false;
        $update = false;
        $sendEmail = 0;
        $statusMessage = '';

		$ipayPayGate = PaymentIntegrationObj::readCCIConfigFile('../config/IPayPayGate.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
		$timestamp = time();
     		// Session Reference
		$ref 		 = UtilsObj::getPOSTParam('Ref');
		$prc 		 = UtilsObj::getPOSTParam('prc');
		$successcode = UtilsObj::getPOSTParam('successcode');
		$orderNumber = UtilsObj::getPOSTParam('Ref');
		$payRef 	 = UtilsObj::getPOSTParam('PayRef');
		$transactionCurrency = UtilsObj::getPOSTParam('Cur');
		$remark      = UtilsObj::getPOSTParam('remark');
		$AuthId      = UtilsObj::getPOSTParam('AuthId');
		$eci      	 = UtilsObj::getPOSTParam('eci');
		$amount      = UtilsObj::getPOSTParam('Amt');
		$payerAuth   = UtilsObj::getPOSTParam('payerAuth');
		$sourceIp    = UtilsObj::getPOSTParam('sourceIp');
		$ipCountry   = UtilsObj::getPOSTParam('ipCountry');
		$cc1316		 = UtilsObj::getPOSTParam('cc1316');
		$cc0104		 = UtilsObj::getPOSTParam('cc0104');

		// Send emails if status changed
		switch ($successcode)
		{
			case '0':
				$statusMessage = "Order ".$orderNumber." has been completed";
				$sendEmail = 1;
				$paymentReceived = 1;
			break;
			case '1':
				$statusMessage = "There was a problem with order ".$orderNumber;
				$sendEmail = 0;
				$paymentReceived = 0;
			break;
			default:
				$statusMessage = "There was a problem with order ".$orderNumber;
				$sendEmail = 0;
				$paymentReceived = 0;
		}


		switch ($payerAuth)
		{
			case 'Y':
				$authorised = true;
				break;

			case 'N':
				$authorised = false;
				break;

			case 'P':
				$authorised = false;
				break;

			case 'A':
				$authorised = false;
				break;

			case 'U':
				$authorised = false;
				break;

		}


		if($sendEmail == 1 && $ipayPayGate['OFFLINECONFIRMATIONEMAILADDRESS']!='' && $ipayPayGate['OFFLINECONFIRMATIONNAME'] !='')
		{
			$emailContent = $statusMessage;
			$emailObj = new TaopixMailer();

			$emailObj->sendTemplateEmail('admin_offlinepaymentupdate', $gSession['webbrandcode'], '', '', '',
				$ipayPayGate['OFFLINECONFIRMATIONNAME'], $ipayPayGate['OFFLINECONFIRMATIONEMAILADDRESS'], '', '',
				0,
				Array('data' => $emailContent),
				false);
		}


     	// Check CCILOG to see if this is an update
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
			$amount = $cciLogEntry['formattedamount'];
			$update = true;
			$parentLogId = $cciLogEntry['id'];
			$orderId = $cciLogEntry['orderid'];
		}


		$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime =  date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($ipayPayGate, $serverTimestamp);

        $resultArray['result'] 					 = $result;
        $resultArray['ref'] 					 = $ref;
        $resultArray['amount'] 					 = $amount;
        $resultArray['formattedamount'] 		 = $amount;
        $resultArray['charges'] 				 = '';
        $resultArray['formattedcharges'] 		 = '';
    	$resultArray['authorised'] 				 = $authorised;
    	$resultArray['authorisedstatus'] 		 = $authorised;
        $resultArray['transactionid'] 			 = $payRef;
        $resultArray['formattedtransactionid'] 	 = $payRef;
        $resultArray['responsecode'] 			 = $successcode;
        $resultArray['responsedescription'] 	 = '';
        $resultArray['authorisationid'] 		 = $AuthId;
        $resultArray['formattedauthorisationid'] = $AuthId;
        $resultArray['bankresponsecode'] 		 = $eci;
        $resultArray['cardnumber'] 				 = '';
        $resultArray['formattedcardnumber'] 	 = '';
        $resultArray['cvvflag'] 				 = '';
        $resultArray['cvvresponsecode'] 		 = '';
        $resultArray['paymentcertificate'] 		 = '';
        $resultArray['paymentdate'] 			 = $serverDate;
        $resultArray['paymentmeans'] 			 = 'CREDIT CARD';
        $resultArray['paymenttime'] 			 = $serverTime;
		$resultArray['paymentreceived'] 		 = $paymentReceived;
        $resultArray['formattedpaymentdate'] 	 = $serverTimestamp;
        $resultArray['addressstatus'] 			 = '';
        $resultArray['postcodestatus'] 			 = '';
        $resultArray['payerid'] 				 = '';
        $resultArray['payerstatus'] 			 = $payerAuth;
        $resultArray['payeremail'] 				 = '';
        $resultArray['business'] 				 = '';
        $resultArray['receiveremail'] 			 = '';
        $resultArray['receiverid'] 				 = '';
        $resultArray['pendingreason'] 			 = '';
        $resultArray['transactiontype'] 		 = '';
        $resultArray['settleamount'] 			 = '';
        $resultArray['currencycode'] 			 = $transactionCurrency;
        $resultArray['webbrandcode'] 			 = $webbrandcode;

        $resultArray['charityflag'] 			 = '';
        $resultArray['threedsecurestatus'] 		 = '';
        $resultArray['cavvresponsecode'] 		 = '';
        $resultArray['update'] 					 = $update;
        $resultArray['orderid'] 				 = $orderNumber;
        $resultArray['parentlogid'] 			 = $parentLogId;
        $resultArray['resultisarray']			 = false;
        $resultArray['resultlist'] 				 = Array();
    	$resultArray['showerror'] 				 = $showError;

        return $resultArray;
    }
}

?>