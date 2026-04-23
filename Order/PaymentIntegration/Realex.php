<?php

class RealexObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();
        $active = false;
		$currency = $gSession['order']['currencycode'];
		
        AuthenticateObj::clearSessionCCICookie();

        // Read config file
        $RealexConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Realex.conf', $currency, $gSession['webbrandcode']);

        $currencyArray = explode(',', $RealexConfig['CURRENCY']);

        // Check if currency in session is supported by the gateway
        if (in_array(strtoupper($gSession['order']['currencycode']), $currencyArray))
        {
            $active = true;
        }

        $resultArray['active'] = $active;
        $resultArray['form'] = "";
        $resultArray['script'] = "";
		$resultArray['scripturl'] = "";
        $resultArray['action'] = "";

        return $resultArray;
    }

    static function initialize()
    {
		global $gSession;
        global $gConstants;
		
        $parameters = Array();
		$resultArray = Array();
		$resultArray['script'] = "";
		$resultArray['scripturl'] = "";

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
        $returnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

        // Read config file
        $RealexConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Realex.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

        if ($gSession['order']['ccidata'] == '')
        {
            // Read from config file
            $server_url = $RealexConfig['SERVERURL'];
			$merchantID = $RealexConfig['MERCHANTID'];
			$secret = $RealexConfig['SECRET'];
			$subaccount = $RealexConfig['SUBACCOUNT'];
			
			$productCollectionCode = $gSession['items'][0]['itemproductcode'];
			$productName = $gSession['items'][0]['itemprojectname'];
			$productDescription = LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
			$timestamp = date('YmdHis'); // timestamp format: YYYYMMDDHHMMSS
			$orderID = $gSession['ref'] . '_' . time();
			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
			$payerRef = '';
			$paymentRef = '';
			
			if ($RealexConfig['CARD_STORAGE_ENABLE'] == '1')
			{
				$payerRef = $gSession['order']['defaultbillingcontactlastname'] . '_' . $gSession['userid'];
				$paymentRef = $gSession['order']['defaultbillingcontactlastname'] . '_' .  $gSession['userid'] . '_' . $gSession['ref']  . '_card';
			}


			if (strpos($gSession['browserlanguagecode'], 'en_') !== false)
			{
				$gSession['browserlanguagecode'] = 'en';
			}

			$hash = RealexObj::generateRequestHash($timestamp, $merchantID, $orderID, $amount, $gSession['order']['currencycode'], $payerRef, $paymentRef, $secret);

			$parameters = array(
				'MERCHANT_ID' => $merchantID,
				'ORDER_ID' => $orderID,
				'ACCOUNT' => $subaccount,
				'AMOUNT' => $amount,
				'CURRENCY' => $gSession['order']['currencycode'],
				'TIMESTAMP' => $timestamp,
				'SHA1HASH' => $hash,
				'AUTO_SETTLE_FLAG' => '1',
				'CUST_NUM' => $gSession['userid'],
				'VAR_REF' => $gSession['ref'],
				'PROD_ID' => $productCollectionCode,
				'RETURN_TSS' => 0,
				'HPP_LANG' => $gSession['browserlanguagecode'],
				'MERCHANT_RESPONSE_URL' => UtilsObj::correctPath($gSession['webbrandwebroot']) . 'PaymentIntegration/Realex/callBack.php?ref=' . $gSession['ref'],
				'COMMENT1' => $productName,
				'COMMENT2' => $productDescription,
				
				// PayPal settings
				'SHIPPING_ADDRESS_ENABLE' => 1,
				'ADDRESS_OVERRIDE' => 1,
				'HPP_NAME' => $gSession['shipping'][0]['shippingcontactfirstname'] . ' ' . $gSession['shipping'][0]['shippingcontactlastname'],
				'HPP_STREET' => $gSession['shipping'][0]['shippingcustomeraddress1'],
				'HPP_STREET2' => $gSession['shipping'][0]['shippingcustomeraddress2'],
				'HPP_CITY' => $gSession['shipping'][0]['shippingcustomercity'],
				'HPP_STATE' => $gSession['shipping'][0]['shippingcustomercounty'],
				'HPP_ZIP' => $gSession['shipping'][0]['shippingcustomerpostcode'],
				'HPP_COUNTRY' => $gSession['shipping'][0]['shippingcustomercountrycode'],
				'HPP_PHONE' => $gSession['shipping'][0]['shippingcustomertelephonenumber']
			);
			
			// Card storage settings only needed if option in enabled in the config
			if ($RealexConfig['CARD_STORAGE_ENABLE'] == '1')
			{
				$parameters['CARD_STORAGE_ENABLE'] = $RealexConfig['CARD_STORAGE_ENABLE'];
				$parameters['OFFER_SAVE_CARD'] = $RealexConfig['OFFER_SAVE_CARD'];
				$parameters['PAYER_REF'] = $payerRef;
				$parameters['PMT_REF'] = $paymentRef;
				$parameters['PAYER_EXIST'] = 1;
			}

            // Define Smarty parameters
            $smarty->assign('method', 'POST');
            $smarty->assign('parameter', $parameters);
            $smarty->assign('cancel_url', $cancelReturnPath);
            $smarty->assign('payment_url', $server_url);

            AuthenticateObj::defineSessionCCICookie();
            $smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
            $smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

            // Set the ccidata to remember we have jumped to LiqPay
            $gSession['order']['ccidata'] = 'start';
            DatabaseObj::updateSession();

            $smarty->cachePage = true; // Allow the page to be cached so that the browser back button works correctly
        }
		else
        {
            // the user has clicked the back button
            AuthenticateObj::clearSessionCCICookie();

            $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
            $smarty->assign('payment_url', $cancelReturnPath);
            $smarty->assign('cancel_url', $cancelReturnPath);
        }

        // Display template
        if ($gSession['ismobile'] == true)
        {
            $resultArray['template'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest_small.tpl');
            $resultArray['javascript'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest.tpl');
        }
		else
        {
            $smarty->displayLocale('order/PaymentIntegration/PaymentRequest_large.tpl');
        }
		 return $resultArray;
    }

	static function generateRequestHash($pTimestamp, $pMerchantID, $pOrderID, $pAmount, $pCurrency, $pPayerRef, $pPaymentRef, $pSecret)
    {
		$strToHash = $pTimestamp . '.' . $pMerchantID  . '.' . $pOrderID  . '.' . $pAmount  . '.' . $pCurrency;
		
		if ($pPayerRef != '')
		{
			$strToHash .= '.' . $pPayerRef;
		}
		
		if ($pPaymentRef != '')
		{
			$strToHash .= '.' . $pPaymentRef;
		}
		
        $hash = sha1($strToHash);

		// make sure the hash is all in lowercase otherwise the second part will fail
		$hash = strtolower($hash);

		// sha1 the first hash with the secret value
		return sha1($hash . '.' . $pSecret);
    }
	
	static function generateResponseHash($pTimestamp, $pMerchantID, $pOrderID, $pResult, $pMessage, $pPasRef, $pAuthCode, $pSecret)
	{
		$strToHash = $pTimestamp . '.' . $pMerchantID . '.'.  $pOrderID . '.'. $pResult . '.'. $pMessage . '.' . $pPasRef . '.' . $pAuthCode;
		
		$hash = sha1($strToHash);
		
		// make sure the hash is all in lowercase otherwise the second part will fail
		$hash = strtolower($hash);
		
		// sha1 the first hash with the secret value
		return sha1($hash . '.' . $pSecret); 
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
		$resultArray['script'] = '';
		$resultArray['scripturl'] = '';

        return $resultArray;
    }

	static function manualCallback()
    {
        global $gSession;

    	// all we have is the session reference
    	$ref = $gSession['ref'];
		$tempArray = PaymentIntegrationObj::getCciLogEntry($ref);
		$resultArray = Array();
        $resultArray['ref'] = $ref;
		$resultArray['authorised'] = $tempArray['authorised'];
		$resultArray['transactionid'] = $tempArray['transactionid'];
		$resultArray['formattedpaymentdate'] = $tempArray['formattedpaymentdate'];
		$resultArray['orderid'] = $tempArray['orderid'];
		
		if ($tempArray['responsecode'] == '00')
		{
			$resultArray['showerror'] = false;
		}
		else
		{
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $tempArray['responsecode'];
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $tempArray['responsedescription'];
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $tempArray['transactionid'];
			$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $tempArray['orderid'];
			$resultArray['errorform'] = 'error.tpl';
			$resultArray['showerror'] = true;
		}

        return $resultArray;
    }

    static function automaticCallback()
    {
        global $gSession;

        $resultArray = Array();
        $error = '';
        $showError = false;
        $update = false;
        $authorisedStatus = 0;
        $result = '';
        $order_id = 0;
        $paymentReceived = 0;
        $amount = $gSession['order']['ordertotaltopay'];
        $currency = $gSession['order']['currencycode'];
        $webBrandCode = $gSession['webbrandcode'];
		$ref = $gSession['ref'];

        // Read config file
        $RealexConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Realex.conf', $currency, $gSession['webbrandcode']);

		$merchantID = UtilsObj::getPOSTParam('MERCHANT_ID');
		$orderID = UtilsObj::getPOSTParam('ORDER_ID');
		$amount = UtilsObj::getPOSTParam('AMOUNT');
		$timestamp = UtilsObj::getPOSTParam('TIMESTAMP');
		$result = UtilsObj::getPOSTParam('RESULT');
		$message = UtilsObj::getPOSTParam('MESSAGE');
		$transaction_id = UtilsObj::getPOSTParam('PASREF');
		$authCode = UtilsObj::getPOSTParam('AUTHCODE');
		$responseHash = UtilsObj::getPOSTParam('SHA1HASH');
		
		$cvnResult = '';
		if (isset($_POST['CVNRESULT']))
		{
			$cvnResult = UtilsObj::getPOSTParam('CVNRESULT');
		}

		if ($result == '00')
		{
			$authorised = true;
			$authorisedStatus = 1;
			
			// generate hash from response data to compare
			$hash = RealExObj::generateResponseHash($timestamp, $merchantID, $orderID, $result, $message, $transaction_id, $authCode, $RealexConfig['SECRET']);
		
			if ($responseHash != $hash)
			{
				$paymentReceived = 0;
				$message .= ' - Security hash match failed';
			}
			else
			{
				$paymentReceived = 1;
			}
		}
		else
		{
			$authorised = false;
			$authorisedStatus = 0;
			$paymentReceived = 0;
		}
		
        // Result data
        // Write to log file.
        $serverTimestamp = DatabaseObj::getServerTime();
        PaymentIntegrationObj::logPaymentGatewayData($RealexConfig, $serverTimestamp);

        $serverDate = date('Y-m-d');
        $serverTime = date("H:i:s");

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] = '';
        $resultArray['authorised'] = $authorised;
        $resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $transaction_id;
        $resultArray['formattedtransactionid'] = $transaction_id;
        $resultArray['responsecode'] = $result;
        $resultArray['responsedescription'] = $message;
        $resultArray['authorisationid'] = $orderID;
        $resultArray['formattedauthorisationid'] = $orderID;
        $resultArray['bankresponsecode'] = $authCode;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = $cvnResult;
        $resultArray['paymentcertificate'] = '';
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
        $resultArray['transactiontype'] = '';
        $resultArray['settleamount'] = '';
        $resultArray['currencycode'] = $currency;
        $resultArray['webbrandcode'] = $webBrandCode;
		$resultArray['script'] = '';
		$resultArray['scripturl'] = '';
        $resultArray['charityflag'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = $order_id;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
        $resultArray['showerror'] = $showError;

        return $resultArray;
    }
}