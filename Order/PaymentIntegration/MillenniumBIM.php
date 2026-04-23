<?php

class MillenniumBIMObj
{
    static function configure()
    {
        global $gSession;
        $resultArray = Array();
        $active = true;

        AuthenticateObj::clearSessionCCICookie();

        // test for MILLENNIUM BIM supported currencies
        // Mozambique Meticals is the only supported currency

        $currencyList = '943,480';

        if (strpos($currencyList, $gSession['order']['currencyisonumber']) === false)
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
        global $gConstants;
        global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$millenniumBIMConfig = PaymentIntegrationObj::readCCIConfigFile('../config/MillenniumBIM.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$returnURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

			$server = $millenniumBIMConfig['MILLENNIUMBIMSERVER'];
			$merchant = $millenniumBIMConfig['MERCHANTID'];
			$accessCode = $millenniumBIMConfig['ACCESSCODE'];
			$secureHashSecret = $millenniumBIMConfig['SECUREHASHSECRET'];
			$defaultLanguage = $gConstants['defaultlanguagecode'];
			$orderID = $gSession['ref'] . '_'. time();

			//Initialise payment parameters
			// amount in smallest unit, e.g. pence or cents
			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
			//$lang = substr($gSession['browserlanguagecode'], 0, 2);
			$lang = 'en';

			//All of the payment parameters are passed as an array
			$parameters = array(
				'vpc_Version'		 => '1',
				'vpc_Command'		 => 'pay',
				'vpc_AccessCode'	 => $accessCode,
				'vpc_MerchTxnRef' => $orderID,
				'vpc_Merchant' 		 => $merchant,
				'vpc_Amount'		 => $amount,
				'vpc_Locale' 		 => $lang,
				'vpc_ReturnURL'		 => $returnURL
			);

			//Calculate MD5 hash
			$md5HashData = $secureHashSecret;
			$hashedvalue = '';
			ksort($parameters);

			foreach($parameters as $key => $value)
			{
				// create the md5 input and URL leaving out any fields that have no value
				if (strlen($value) > 0)
				{
					$md5HashData .= $value;
				}
			}

			// Create the secure hash and append it to the Virtual Payment Client Data if
			// the merchant secret has been provided.
			if (strlen($secureHashSecret) > 0)
			{
				$hashedvalue .= strtoupper(md5($md5HashData));
			}

			$hashArray = array('vpc_SecureHash' => $hashedvalue);

			//append the $hashedvalue to the parameters array to be looped through on the paymentRequest.tpl
			$params = array_merge($parameters, $hashArray);

			// define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('parameter', $params);
			$smarty->assign('method', "GET");

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to MilenniumBIM
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
        $result = '';
        $resultArray['result'] = '';
        $resultArray['ref'] = $gSession['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

   	static function confirm()
    {
   		global $ac_config;
        global $gSession;

        $resultArray = Array();
        $result = '';
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $showError = false;
        $update = false;

		$millenniumBIMConfig = PaymentIntegrationObj::readCCIConfigFile('../config/MillenniumBIM.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

		$merchant = $millenniumBIMConfig['MERCHANTID'];
		$accessCode = $millenniumBIMConfig['ACCESSCODE'];
		$secureHashSecret = $millenniumBIMConfig['SECUREHASHSECRET'];

		//Return Paramters
		$ref = UtilsObj::getGETParam('ref');
		$secretHashReturnParamter = UtilsObj::getGETParam("vpc_SecureHash");
		$amount          = UtilsObj::getGETParam("vpc_Amount");
		$locale          = UtilsObj::getGETParam("vpc_Locale");
		$batchNo         = UtilsObj::getGETParam("vpc_BatchNo");
		$command         = UtilsObj::getGETParam("vpc_Command");
		$message         = UtilsObj::getGETParam("vpc_Message");
		$version         = UtilsObj::getGETParam("vpc_Version");
		$cardType        = UtilsObj::getGETParam("vpc_Card");
		$orderInfo       = UtilsObj::getGETParam("vpc_OrderInfo");
		$receiptNo       = UtilsObj::getGETParam("vpc_ReceiptNo");
		$merchantID      = UtilsObj::getGETParam("vpc_Merchant");
		$authorizeID     = UtilsObj::getGETParam("vpc_AuthorizeId");
		$merchTxnRef     = UtilsObj::getGETParam("vpc_MerchTxnRef");
		$transactionNo   = UtilsObj::getGETParam("vpc_TransactionNo");
		$acqResponseCode = UtilsObj::getGETParam("vpc_AcqResponseCode");
		$txnResponseCode = UtilsObj::getGETParam("vpc_TxnResponseCode");
		$amount = $gSession['order']['ordertotaltopay'];
        $currenCycode = $gSession['order']['currencycode'];

		// Get the transaction response text based on the transaction response code from the payment server.
		$responseReasonText = self::getResponseDescription($txnResponseCode);

		// If transaction response code is either 7, 1 or a ? then an error has occured.
		if ($txnResponseCode != '7' && $txnResponseCode != '1' && $txnResponseCode != '?')
		{
			// start building the md5 hash string to compare against the returned hash parameter.
			$md5HashData = $secureHashSecret;

			foreach($_GET as $key => $value)
			{
				if ($key != "vpc_SecureHash" && $key != "fsaction" && $key != "ref")
				{
					$md5HashData .= $value;
				}
			}

			// Check to see if returned hash code matches the hash returned from the payment server
			if (strtoupper($secretHashReturnParamter) == strtoupper(md5($md5HashData)))
			{
				//If transaction code is 0 then the transaction is successful
				if ($txnResponseCode == '0')
				{
					$authorised = true;
        			$authorisedStatus = 1;
				}
			}
			else
			{
				// md5 check failed
				$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': MD5KEY';
				$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': MD5 check failed';
				$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $transactionNo;
				$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $merchTxnRef;
				$resultArray['errorform'] = 'error.tpl';
				$showError = true;
			}
		}
		else
		{
				// md5 check failed
				$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': Payment Error';
				$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': '.$responseReasonText;
				//$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $transactionID;
				//$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $transactionID;
				$resultArray['errorform'] = 'error.tpl';
				$showError = true;
		}

		// write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime =  date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($millenniumBIMConfig, $serverTime, $responseReasonText);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '000';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $transactionNo;
        $resultArray['formattedtransactionid'] = $transactionNo;
        $resultArray['responsecode'] = $txnResponseCode;
        $resultArray['responsedescription'] = $responseReasonText;
        $resultArray['authorisationid'] = $receiptNo;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $receiptNo;
        $resultArray['bankresponsecode'] = $responseReasonText;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = $receiptNo;
        $resultArray['paymentmeans'] = $cardType;
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymenttime'] = $serverTime;
        $resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $serverDate;
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = '';
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = '';
        $resultArray['business'] = '';
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = $cardType;
        $resultArray['settleamount'] = '';
        $resultArray['currencycode'] = $gSession['order']['currencycode'];
        $resultArray['webbrandcode'] = $gSession['webbrandcode'];

        $resultArray['charityflag'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['update'] = false;
        $resultArray['orderid'] = 0;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = $showError;

        return $resultArray;
    }

    static function getResponseDescription($responseCode)
    {

    	switch ($responseCode)
    	{
			case "0" : $result = "Transaction Successful"; break;
			case "?" : $result = "Transaction status is unknown"; break;
			case "1" : $result = "Unknown Error"; break;
			case "2" : $result = "Bank Declined Transaction"; break;
			case "3" : $result = "No Reply from Bank"; break;
			case "4" : $result = "Expired Card"; break;
			case "5" : $result = "Insufficient funds"; break;
			case "6" : $result = "Error Communicating with Bank"; break;
			case "7" : $result = "Payment Server System Error"; break;
			case "8" : $result = "Transaction Type Not Supported"; break;
			case "9" : $result = "Bank declined transaction (Do not contact Bank)"; break;
			case "A" : $result = "Transaction Aborted"; break;
			case "C" : $result = "Transaction Cancelled"; break;
			case "D" : $result = "Deferred transaction has been received and is awaiting processing"; break;
			case "F" : $result = "3D Secure Authentication failed"; break;
			case "I" : $result = "Card Security Code verification failed"; break;
			case "L" : $result = "Shopping Transaction Locked (Please try the transaction again later)"; break;
			case "N" : $result = "Cardholder is not enrolled in Authentication scheme"; break;
			case "P" : $result = "Transaction has been received by the Payment Adaptor and is being processed"; break;
			case "R" : $result = "Transaction was not processed - Reached limit of retry attempts allowed"; break;
			case "S" : $result = "Duplicate SessionID (OrderInfo)"; break;
			case "T" : $result = "Address Verification Failed"; break;
			case "U" : $result = "Card Security Code Failed"; break;
			case "V" : $result = "Address Verification and Card Security Code Failed"; break;
			default  : $result = "Unable to be determined";
    	}

    	return $result;
	}
}

?>