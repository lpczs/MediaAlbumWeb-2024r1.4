<?php

class CNPKZObj
{   
    static function configure()
    {
        global $gSession;
        $resultArray = Array();
        $active = true;
        AuthenticateObj::clearSessionCCICookie();
        $currency = $gSession['order']['currencycode'];        
        $CNPKZConfig = PaymentIntegrationObj::readCCIConfigFile('../config/CNPKZ.conf', $currency, $gSession['webbrandcode']);

        // make sure merchant details are set
        if (($CNPKZConfig['MERCHANTID'] == '') || ($CNPKZConfig['TERMINALID'] == ''))
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
        require_once(__DIR__.'/CNPprocessingKZ/CNPMerchantWebServiceClient.php');
        
        global $gConstants;
        global $gSession;
        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$CNPKZConfig = PaymentIntegrationObj::readCCIConfigFile('../config/CNPKZ.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
			
            $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$redirectPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
			$serverToServerPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccAutomaticCallback&ref=' . $gSession['ref'];
            $testReturnPath = UtilsObj::correctPath($gSession['webbrandwebroot']) . '/PaymentIntegration/CNPKZ/CNPKZCallBack.php';

			$merchantID = $CNPKZConfig['MERCHANTID'];
			$terminalID = $CNPKZConfig['TERMINALID'];
			$defaultLanguage = $CNPKZConfig['LANGUAGE'];

			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
			$currency = $gSession['order']['currencyisonumber'];

			$orderID = $gSession['ref']. time();
			$orderID = substr($orderID, 0, 12);
			$gSession['order']['id'] = $orderID;
            $gSession['order']['callbackCount'] = 0;
			DatabaseObj::updateSession();
			
			// SIS requires order text
			$orderDescription = $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
			$orderDescription = substr($orderDescription, 0, 125);

			$customerName = $gSession['order']['billingcontactfirstname'].' '.$gSession['order']['billingcontactlastname'];
			$typeOfTransaction = '0';
			$merchantData = $gSession['ref'];
          
            $client=new CNPMerchantWebServiceClient();
            $transactionDetails = new TransactionDetails();
            $transactionDetails->merchantId = $merchantID;
            $transactionDetails->terminalId = $terminalID;
            $transactionDetails->totalAmount = $amount;
            $transactionDetails->customerReference=$orderID;
            $transactionDetails->currencyCode = $currency;
            $transactionDetails->description = $orderDescription;
            $transactionDetails->returnURL = $redirectPath;
            $transactionDetails->languageCode = $defaultLanguage;
            $transactionDetails->merchantLocalDateTime = date("d.m.Y H:i:s");
            $transactionDetails->orderId = $orderID;			
            
            $st = new startTransaction();
            $st->transaction = $transactionDetails;
            $startTransactionResult=$client->startTransaction($st);

            if ($startTransactionResult->return->success == true) 
            {
                $_SESSION["customerReference"]=$startTransactionResult->return->customerReference;
                header("Location: " . $startTransactionResult->return->redirectURL);
            } else 
            {
                $errors='Error: ' . $startTransactionResult->return->errorDescription;
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
		require_once(__DIR__.'/CNPprocessingKZ/CNPMerchantWebServiceClient.php');
		global $gSession;
        
        $resultArray = Array();
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $responseCode = 0;
        $showError = false;
        $orderId = -1;
        $parentLogId = -1;
        $update = false;
        
        $CNPKZConfig = PaymentIntegrationObj::readCCIConfigFile('../config/CNPKZ.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
        $merchantID = $CNPKZConfig['MERCHANTID'];
        
        $ref = UtilsObj::getGETParam('ref');
        $orderID = $gSession['order']['id'];
        $authorisationID = $orderID;
        $transactionID = $orderID;
        
        $client=new CNPMerchantWebServiceClient();
        $params = new getTransactionStatus();
        $params->merchantId = $merchantID;
        $params->referenceNr = $orderID;
        $tranResult=$client->getTransactionStatus($params);

        if($gSession['order']['callbackCount'] ==0)
        {
            if ($tranResult==null) 
            {  
                $authorised = false;
                $authorisedStatus = 0;
                $responseCode = 0;
            }
            else
            {
                if($tranResult->return->transactionStatus == 'AUTHORISED')
                {
                    $authorised = true;
                    $authorisedStatus = 1;
                    $responseCode = 1;
                    $result = 'AUTHORISED';
                }
                else
                {
                    $authorised = false;
                    $authorisedStatus = 0;
                    $responseCode = 0;
                }
            }

            $gSession['order']['callback_ref'] = $ref;
            $gSession['order']['callback_authorised'] = $authorised;
            $gSession['order']['callback_authorisedStatus'] = $authorisedStatus;
            $gSession['order']['callback_responseCode'] = $responseCode;
            $gSession['order']['callback_result'] = $result;

        }
        else
        {
            $ref = $gSession['order']['callback_ref'];
            $authorised = $gSession['order']['callback_authorised'];
            $authorisedStatus = $gSession['order']['callback_authorisedStatus'];
            $responseCode = $gSession['order']['callback_responseCode'];
            $result = $gSession['order']['callback_result'];
            
            $update = true;
        }
        $gSession['order']['callbackCount'] = $gSession['order']['callbackCount'] + 1;
        DatabaseObj::updateSession();
        
        
        $smarty = SmartyObj::newSmarty('Order', '', '');
		$currencyCode = $gSession['order']['currencycode'];
		$currency = $gSession['order']['currencyisonumber'];
		$webbrandcode = $gSession['webbrandcode'];
		$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
		$typeOfTransaction = 0;	

        $serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime =  date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($CNPKZConfig, $serverTimestamp);

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
        $resultArray['bankresponsecode'] = '';
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
    	$resultArray['showerror'] = $showError;

        return $resultArray;
    }
}

?>