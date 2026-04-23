<?php

class REDSYSObj
{   
    static function configure()
    {
        global $gSession;
        $resultArray = Array();
        $active = true;

        AuthenticateObj::clearSessionCCICookie();

        // test for SIS supported currencies
        $currencyList = '978';

        if (strpos($currencyList, $gSession['order']['currencyisonumber']) === false)
        {
            $active = false;
        }

        $resultArray['active'] = $active;
        $resultArray['form'] = '';
        $resultArray['script'] = '';
        $resultArray['action'] = '';
		$resultArray['scripturl'] = '';

        return $resultArray;
    }

    static function initialize()
    {
        require_once(__DIR__.'/redsys5.2.0/apiRedsys.php');
        
        global $gConstants;
        global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$SISConfig = PaymentIntegrationObj::readCCIConfigFile('../config/redsys.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$redirectPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
			$serverToServerPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccAutomaticCallback&ref=' . $gSession['ref'];

			$server = $SISConfig['SISSERVER'];
			$merchant = $SISConfig['MERCHANTID'];
			$terminalNumber = $SISConfig['TERMINALNUMBER'];
			$secretCode =  $SISConfig['SECRETCODE'];

			//Initialize order variables.
			$defaultLanguage = $gConstants['defaultlanguagecode'];

			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
			$currency = $gSession['order']['currencyisonumber'];

			$orderID = $gSession['ref']. time();
			$orderID = substr($orderID, 0, 12);
			$gSession['order']['id'] = $orderID;
			DatabaseObj::updateSession();
			
			// SIS requires order text
			$orderDescription = $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
			$orderDescription = substr($orderDescription, 0, 125);

			$customerName = $gSession['order']['billingcontactfirstname'].' '.$gSession['order']['billingcontactlastname'];
			$typeOfTransaction = '0';
			$merchantData = $gSession['ref'];
          
            //Create and calculate the Redsys object with its various parameters
            $miObj = new RedsysAPI;
            $miObj->setParameter("DS_MERCHANT_AMOUNT", $amount);
            $miObj->setParameter("DS_MERCHANT_ORDER", $orderID);
            $miObj->setParameter("DS_MERCHANT_MERCHANTCODE", $merchant);
            $miObj->setParameter("DS_MERCHANT_CURRENCY", $currency);
            $miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE", $typeOfTransaction);
            $miObj->setParameter("DS_MERCHANT_TERMINAL", $terminalNumber);
            $miObj->setParameter("DS_MERCHANT_MERCHANTURL", $serverToServerPath);
            $miObj->setParameter("DS_MERCHANT_URLOK", $redirectPath);
            $miObj->setParameter("DS_MERCHANT_URLKO", $cancelReturnPath);

            $params = $miObj->createMerchantParameters();
            $key = $SISConfig['SECRETCODE'];
            $signature = $miObj->createMerchantSignature($key);

			// specify the language that will be used on the payment page.
			$locale = strtolower($gSession['browserlanguagecode']);
			$locale = substr($locale, 0, 2);
			$languageList = 'en,de,es,fr,it,nl';

			if (strpos($languageList, $locale) === false)
			{
				$displayLang = '002';
			}
			else
			{
				switch ($locale)
				{
					case 'es':
						$displayLang = '001';
					break;
					case 'en':
						$displayLang = '002';
					break;
					case 'fr':
						$displayLang = '004';
					break;
					case 'de':
						$displayLang = '005';
					break;
					case 'nl':
						$displayLang = '006';
					break;
					case 'it':
						$displayLang = '007';
					break;
				}
			}
            
            $requestParameters = array(
			'Ds_SignatureVersion'	=> 'HMAC_SHA256_V1',
			'Ds_MerchantParameters'		=> $params,
            'Ds_Signature'		=> $signature
            );

			// define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('method', "POST");
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('parameter', $requestParameters);
            
			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to SIS
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

    static function confirm($callBack)
    {
		require_once(__DIR__.'/redsys5.2.0/apiRedsys.php');
		
     	global $gSession;
        
        $resultArray = Array();
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $showError = false;
        $orderId = -1;
        $parentLogId = -1;
        $update = false;

       	$SISConfig = PaymentIntegrationObj::readCCIConfigFile('../config/redsys.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
		$server = $SISConfig['SISSERVER'];
		$merchant = $SISConfig['MERCHANTID'];
		$terminalNumber = $SISConfig['TERMINALNUMBER'];
		$secretCode =  $SISConfig['SECRETCODE'];
		$currencyCode = $gSession['order']['currencycode'];
		$currency = $gSession['order']['currencyisonumber'];
		$webbrandcode = $gSession['webbrandcode'];
		$responseCode = 0;
		$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
		$typeOfTransaction = 0;
		$authorisationCode = '';

		if ($callBack == 'automatic')
		{
			$ref = UtilsObj::getGETParam('ref');
			$version = UtilsObj::getPOSTParam('Ds_SignatureVersion');
			$params = UtilsObj::getPOSTParam('Ds_MerchantParameters');
			$signatureRecibida = UtilsObj::getPOSTParam('Ds_Signature');
			
            $miObj = new RedsysAPI;
			$decode = $miObj->decodeMerchantParameters($params); 
			$merchantArray = json_decode($decode, true);
			$authorisationCode = $merchantArray['Ds_AuthorisationCode'];
		
			$orderID = $gSession['order']['id'];
			$transactionID = $orderID;
			$authorisationID = $orderID;
			$signatureCalculada = $miObj->createMerchantSignatureNotif($secretCode, $params);
			
			if($signatureCalculada == $signatureRecibida)
			{
				$responseCode = (int) $miObj->getParameter("Ds_Response");
				
				if ($responseCode <= 99)
				{
					$authorised = true;
					$authorisedStatus = 1;
					$responseCode = 1;
				}
			}
		}

		//Handle the manual callback as No parameters are returned on the Manual Callback
		if ($callBack == 'manual')
		{
			$ref = UtilsObj::getGETParam('ref');
			//Get CCILOG data for manual callback
			$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

			if (!empty($cciLogEntry))
			{
				// we already have an entry, this must be a status update
				// we won't have a session
				$webbrandcode = $cciLogEntry['webbrandcode'];
				$currencyCode = $cciLogEntry['currencycode'];
				$amount = $cciLogEntry['formattedamount'];
				$parentLogId = $cciLogEntry['id'];
				$orderId = $cciLogEntry['orderid'];
				$authorised = true;
				$authorisedStatus = $cciLogEntry['authorised'];
				$transactionID = $cciLogEntry['transactionid'];
				$responseCode = $cciLogEntry['responsecode'];
				$authorisationID = $cciLogEntry['authorisationid'];
				$authorisationCode = $cciLogEntry['bankresponsecode'];
			}
		}

        $serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime =  date("H:i:s");
		
		PaymentIntegrationObj::logPaymentGatewayData($SISConfig, $serverTimestamp);
		
		$smarty = SmartyObj::newSmarty('CreditCardPayment', '', '');
	
        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] ='';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $transactionID;
        $resultArray['formattedtransactionid'] = $transactionID;
        $resultArray['responsecode'] = $responseCode;
        $resultArray['responsedescription'] = $responseCode;
        $resultArray['authorisationid'] = $authorisationID;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $authorisationID;
        $resultArray['bankresponsecode'] = $authorisationCode;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
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
        $resultArray['resultlist'] = Array();
		$resultArray['ccicompletionmessage'] =  $smarty->get_config_vars('str_RedSysAuthCode') . ' ' . $authorisationCode;;
    	$resultArray['showerror'] = $showError;
		
        return $resultArray;
    }
}

?>