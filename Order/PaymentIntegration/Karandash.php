<?php
class KarandashObj
{
    static function configure()
    {
        $resultArray = Array();

        AuthenticateObj::clearSessionCCICookie();

		$resultArray['gateways'] = '';
        $resultArray['active'] = true;
        $resultArray['form'] = "";
        $resultArray['scripturl'] = '';
        $resultArray['script'] = "";
        $resultArray['action'] = "";
        return $resultArray;
    }

    static function initialize()
    {

        global $gSession;
        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	$returnURL = UtilsObj::correctPath($gSession['webbrandwebroot']) . 'PaymentIntegration/Karandash/KarandashCallback.php';
        $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser

        if ($gSession['order']['ccidata'] == '')
        {

			$KarandashConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Karandash.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

			$secretkey = $gSession['ref'];
			$totalsum = $gSession['order']['ordertotaltopay'];

			$params = array(
				'SecretKey'	=> $secretkey,
				'TotalSum'	=> $totalsum,
				'ReturnUrl'	=> $returnURL
			);

			// define Smarty variables
			$smarty->assign('payment_url', $KarandashConfig['SERVER']);
			$smarty->assign('cancelurl', $cancelReturnPath);
			$smarty->assign('parameter', $params);
			$smarty->assign('method', "POST");


			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to Karandash
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
        $_POST['session_id'] = $_GET['ref'];
    	$returnParamArray = $_POST;

        $resultArray = Array();

        $resultArray['result'] = '';
        $resultArray['ref'] = $returnParamArray['secretkey'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function manualCallback()
    {
    	// include the email creation module
    	require_once('../Utils/UtilsEmail.php');

     	global $gSession;

     	$resultArray = Array();
        $authorised = false;
        $authorisedStatus = 0;
        $showError = false;
        $update = false;
        $sendEmail = 0;
        $statusMessage = '';

     	$KarandashConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Karandash.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

     	$smarty = SmartyObj::newSmarty('Order', '', '');

     	//Put return parameters into an array.
     	$returnParams = $_POST;

		// Send emails if status changed
		switch ($returnParams['Status'])
		{
			case '0':
				$sendEmail = 0;
			break;
			case '1':
				$statusMessage = $smarty->get_config_vars('str_ErrorCannotProcessOrder');
				$sendEmail = 1;
			break;
		}

		if($sendEmail == 1 && $KarandashConfig['OFFLINECONFIRMATIONEMAILADDRESS']!='' && $KarandashConfig['OFFLINECONFIRMATIONNAME'] !='')
		{
			$emailContent = $statusMessage;
			$emailObj = new TaopixMailer();

			$emailObj->sendTemplateEmail('admin_offlinepaymentupdate', $gSession['webbrandcode'], '', '', '',
				$KarandashConfig['OFFLINECONFIRMATIONNAME'], $KarandashConfig['OFFLINECONFIRMATIONEMAILADDRESS'], '', '',
				0,
				Array('data' => $emailContent),
				false);

		}

		//Session Reference
		$ref = $returnParams['SecretKey'];

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


   		switch ($returnParams['Status'])
		{
			case '1':
			     $authorised = true;
				 $authorisedStatus = 1;
			break;
			case '2':
				 $authorised = false;
				 $authorisedStatus = 0;
			break;
			case '3':
				 $authorised = true;
				 $authorisedStatus = 0;
			break;

		}


		$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime =  date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($KarandashConfig, $serverTimestamp);

        $resultArray['result'] 					 = $returnParams['Status'];
        $resultArray['ref'] 					 = $ref;
        $resultArray['amount'] 					 = $amount;
        $resultArray['formattedamount'] 		 = $amount;
        $resultArray['charges'] 				 = '';
        $resultArray['formattedcharges'] 		 = '';
    	$resultArray['authorised'] 				 = $authorised;
    	$resultArray['authorisedstatus'] 		 = $authorisedStatus;
        $resultArray['transactionid'] 			 = $ref;
        $resultArray['formattedtransactionid'] 	 = $ref;
        $resultArray['responsecode'] 			 = $returnParams['Status'];
        $resultArray['responsedescription'] 	 = '';
        $resultArray['authorisationid'] 		 = '';
        $resultArray['formattedauthorisationid'] = '';
        $resultArray['bankresponsecode'] 		 = '';
        $resultArray['cardnumber'] 				 = '';
        $resultArray['formattedcardnumber'] 	 = '';
        $resultArray['cvvflag'] 				 = '';
        $resultArray['cvvresponsecode'] 		 = '';
        $resultArray['paymentcertificate'] 		 = '';
        $resultArray['paymentdate'] 			 = $serverDate;
        $resultArray['paymentmeans'] 			 = '';
        $resultArray['paymenttime'] 			 = $serverTime;
		$resultArray['paymentreceived'] 		 = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] 	 = $serverTimestamp;
        $resultArray['addressstatus'] 			 = '';
        $resultArray['postcodestatus'] 			 = '';
        $resultArray['payerid'] 				 = '';
        $resultArray['payerstatus'] 			 = '';
        $resultArray['payeremail'] 				 = '';
        $resultArray['business'] 				 = '';
        $resultArray['receiveremail'] 			 = '';
        $resultArray['receiverid'] 				 = '';
        $resultArray['pendingreason'] 			 = '';
        $resultArray['transactiontype'] 		 = '';
        $resultArray['settleamount'] 			 = '';
        $resultArray['currencycode'] 			 = $currencyCode;
        $resultArray['webbrandcode'] 			 = $webbrandcode;

        $resultArray['charityflag'] 			 = '';
        $resultArray['threedsecurestatus'] 		 = '';
        $resultArray['cavvresponsecode'] 		 = '';
        $resultArray['update'] 					 = $update;
        $resultArray['orderid'] 				 = $orderId;
        $resultArray['parentlogid'] 			 = $parentLogId;
        $resultArray['resultisarray']			 = false;
        $resultArray['resultlist'] 				 = Array();
    	$resultArray['showerror'] 				 = $showError;

        return $resultArray;
    }
}

?>