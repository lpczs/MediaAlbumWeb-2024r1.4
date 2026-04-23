<?php

class PayUObj
{
    static function configure()
    {
        global $gSession;

		$active = false;
        $resultArray = Array();
        AuthenticateObj::clearSessionCCICookie();

		$PayUConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayU.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

        if (in_array($gSession['order']['currencycode'], explode(',', $PayUConfig['CURRENCIES'])))
        {
			$active = true;
        }

		$resultArray['gateways'] = '';
        $resultArray['active'] = $active;
        $resultArray['form'] = "";
        $resultArray['scripturl'] = "";
		$resultArray['script'] = "";
		$resultArray['action'] = '';

		return $resultArray;
    }

    static function initialize()
    {
        global $gSession;
        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
		$returnPath = UtilsObj::correctPath($gSession['webbrandwebroot']) . 'PaymentIntegration/PayU/PayUCallback.php?ref=' . $gSession['ref'];
		$confirmPath = UtilsObj::correctPath($gSession['webbrandwebroot']) . 'PaymentIntegration/PayU/PayUCallback.php?ref=' . $gSession['ref'] . '&callback=automatic';

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$PayUConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayU.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

			$server = $PayUConfig['SERVER'];
			$testMode = $PayUConfig['TESTMODE'];
			$accountId = $PayUConfig['ACCOUNTID'];
			$apiLogin = $PayUConfig['APILOGIN'];
			$apiKey = $PayUConfig['APIKEY'];
			$merchantId = $PayUConfig['MERCHANTID'];
			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');
			$currencyCode = $gSession['order']['currencycode'];
			$ref = $gSession['ref'] . '_' . time();

			$signature = self::generateSignature($apiKey, $merchantId, $ref, $amount, $currencyCode);

			$language = $gSession['browserlanguagecode'];

			if (strpos($language, 'en_') !== false)
			{
				$language = 'en';
			}

			// supported languages: English, Spanish and Portuguese
			if (!in_array($gSession['browserlanguagecode'], array('en', 'es', 'pt')))
			{
				$language = $PayUConfig['DEFAULTLANGUAGE'];

				// if DEFAULTLANGUAGE is empty or not set in the config then default to English
				if ($language == '')
				{
					$language = 'en';
				}
			}

			$parameters = array(
				'test' => $testMode,
				'accountId' => $accountId,
				'merchantId' => $merchantId,
				'referenceCode' => $ref,
				'description' => $gSession['items'][0]['itemprojectname'],
				'amount' => $amount,
				'signature' => $signature,
				'algorithmSignature' => 'md5',
				'currency' => $currencyCode,
				'lng' => $language,
				'tax' => 0,
				'taxReturnBase' => 0,
				'responseUrl' => $returnPath,
				'confirmationUrl' => $confirmPath,
				'payerFullName' => $gSession['order']['billingcontactfirstname'] . ' ' . $gSession['order']['billingcontactlastname'],
				'payerEmail' => $gSession['order']['billingcustomeremailaddress'],
				'payerPhone' => $gSession['order']['billingcustomertelephonenumber'],
				'billingAddress' => $gSession['order']['billingcustomeraddress1'] . ' ' . $gSession['order']['billingcustomeraddress2'] . ' ' . $gSession['order']['billingcustomeraddress3'] . ' ' . $gSession['order']['billingcustomeraddress4'],
				'billingCity' => $gSession['order']['billingcustomercity'],
				'billingCountry' => $gSession['order']['billingcustomercountrycode'],
				'shippingAddress' => $gSession['shipping'][0]['shippingcustomeraddress1'] . ' ' . $gSession['shipping'][0]['shippingcustomeraddress2'] . ' ' . $gSession['shipping'][0]['shippingcustomeraddress3'] . ' ' . $gSession['shipping'][0]['shippingcustomeraddress4'],
				'shippingCity' => $gSession['shipping'][0]['shippingcustomercity'],
				'shippingCountry' => $gSession['shipping'][0]['shippingcustomercountrycode'],
				'zipCode' => $gSession['shipping'][0]['shippingcustomerpostcode'],
				'extra1' => LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true)
			);

			// define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('parameter', $parameters);
			$smarty->assign('method', 'post');

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

	static function generateSignature($pApiKey, $pMerchantId, $pReferenceCode, $pAmount, $pCurrency)
	{
		return md5($pApiKey . '~' . $pMerchantId . '~' . $pReferenceCode . '~' . $pAmount . '~' . $pCurrency);
	}

	static function generateConfirmSignature($pApikey, $pMerchantId, $pRefSale, $pValue, $pCurrency, $pState)
	{
		// format the amount correctly so signature can be generated correctly

		$amountFormatted = number_format($pValue, 2, '.', '');
		$amountFormatted2 = '';

		$amountArray = explode('.', $amountFormatted);

		// amounts ending in .00 must be converted to .0 (i.e. 5.00 to 5.0)
		if (count($amountArray) > 0)
		{
			if (trim($amountArray[1]) == '00')
			{
				$amountFormatted2 = number_format($amountArray[0], 1, '.', '');
			}
			else
			{
				$amountFormatted2 = $amountFormatted;
			}
		}

		return md5($pApikey . '~' . $pMerchantId . '~' . $pRefSale . '~' . $amountFormatted2 . '~' . $pCurrency . '~' . $pState);
	}

    static function cancel()
    {
        $ref = UtilsObj::getGETParam('ref');

        $resultArray = array();
        $resultArray['result'] = '';
        $resultArray['ref'] = $ref;
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

	// we don't know when the automatic callback is fired so this function will be called
	// by the callback script once the CCiLog is created by the automatic callback first
    static function manualCallback()
    {
		global $gSession;

		$resultArray = Array();
		$resultArray['result'] = '';
		$showError = false;
        $webBrandCode = $gSession['webbrandcode'];

		$ref = UtilsObj::getGETParam('ref');
		$merchantId = UtilsObj::getPOSTParam('merchantId');
		$merchant_name = UtilsObj::getPOSTParam('merchant_name');
		$merchant_address = UtilsObj::getPOSTParam('merchant_address');
		$telephone = UtilsObj::getPOSTParam('telephone');
		$transactionState = UtilsObj::getPOSTParam('transactionState');
		$lapTransactionState = UtilsObj::getPOSTParam('lapTransactionState');
		$message = UtilsObj::getPOSTParam('message');
		$referenceCode = UtilsObj::getPOSTParam('referenceCode');
		$reference_pol = UtilsObj::getPOSTParam('reference_pol');
		$transactionId = UtilsObj::getPOSTParam('transactionId');
		$description = UtilsObj::getPOSTParam('description');
		$trazabilityCode = UtilsObj::getPOSTParam('trazabilityCode');
		$cus = UtilsObj::getPOSTParam('cus');
		$orderLanguage = UtilsObj::getPOSTParam('orderLanguage');
		$extra1 = UtilsObj::getPOSTParam('extra1');
		$extra2 = UtilsObj::getPOSTParam('extra2');
		$extra3 = UtilsObj::getPOSTParam('extra3');
		$polTransactionState = UtilsObj::getPOSTParam('polTransactionState');
		$signature = UtilsObj::getPOSTParam('signature');
		$polResponseCode = UtilsObj::getPOSTParam('polResponseCode');
		$lapResponseCode = UtilsObj::getPOSTParam('lapResponseCode');
		$risk = UtilsObj::getPOSTParam('risk');
		$polPaymentMethod = UtilsObj::getPOSTParam('polPaymentMethod');
		$lapPaymentMethod = UtilsObj::getPOSTParam('lapPaymentMethod');
		$polPaymentMethodType = UtilsObj::getPOSTParam('polPaymentMethodType');
		$lapPaymentMethodType = UtilsObj::getPOSTParam('lapPaymentMethodType');
		$installmentsNumber = UtilsObj::getPOSTParam('installmentsNumber');
		$TX_VALUE = UtilsObj::getPOSTParam('TX_VALUE');
		$TX_TAX = UtilsObj::getPOSTParam('TX_TAX');
		$currency = UtilsObj::getPOSTParam('currency');
		$lng = UtilsObj::getPOSTParam('lng');
		$pseCycle = UtilsObj::getPOSTParam('pseCycle');
		$pseBank = UtilsObj::getPOSTParam('pseBank');
		$pseReference1 = UtilsObj::getPOSTParam('pseReference1');
		$pseReference2 = UtilsObj::getPOSTParam('pseReference2');
		$pseReference3 = UtilsObj::getPOSTParam('pseReference3');
		$authorizationCode = UtilsObj::getPOSTParam('authorizationCode');

		$PayUConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayU.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

		switch ($transactionState)
		{
			case 4: // Approved
			{
				$authorised = true;
				$paymentReceived = 0;
				$showError = false;

				// check signatures match
				$apiKey = $PayUConfig['APIKEY'];
				$merchantId = $PayUConfig['MERCHANTID'];

				// generate signature to check
				$sign = self::generateConfirmSignature($apiKey, $merchantId, $referenceCode, $TX_VALUE, $currency, $transactionState);

				if ($signature != $sign)
				{
					$message .= ' - security signatures did not match';
					$paymentReceived = 0;
				}
				else
				{
					$paymentReceived = 1;
				}

				break;
			}
			case 5: // expired
			case 6: // declined
			case 104: // Error
			{
				$authorised = false;
				$paymentReceived = 0;
				break;
			}
			case 7: // Pending
			{
				$authorised = true;
				$paymentReceived = 0;
				$showError = false;

				// check signatures match
				$apiKey = $PayUConfig['APIKEY'];
				$merchantId = $PayUConfig['MERCHANTID'];

				// generate signature to check
				$sign = self::generateConfirmSignature($apiKey, $merchantId, $referenceCode, $TX_VALUE, $currency, $transactionState);

				if ($signature != $sign)
				{
					$message .= ' - security signatures did not match';
				}

				break;
			}
			default:
			{
				$authorised = false;
				$paymentReceived = 0;
				break;
			}
		}


		$serverTimestamp = DatabaseObj::getServerTime();
        $serverDate = date('Y-m-d');
        $serverTime = date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($PayUConfig, $serverTimestamp);

		$resultArray = PaymentIntegrationObj::getCciLogEntryFromTransactionID($transactionId);

		if (count($resultArray) == 0)
		{
			// no cci log entry found, create a new entry
			$resultArray['result'] = '';
			$resultArray['ref'] = $ref;
			$resultArray['amount'] = $TX_VALUE;
			$resultArray['formattedamount'] = $TX_VALUE;
			$resultArray['charges'] = '';
			$resultArray['formattedcharges'] = '';
			$resultArray['authorised'] = $authorised;
			$resultArray['authorisedstatus'] = $transactionState;
			$resultArray['transactionid'] = $transactionId;
			$resultArray['formattedtransactionid'] = $transactionId;
			$resultArray['responsecode'] = $reference_pol;
			$resultArray['responsedescription'] = $message;
			$resultArray['authorisationid'] = $authorizationCode;
			$resultArray['formattedauthorisationid'] = $authorizationCode;
			$resultArray['bankresponsecode'] = '';
			$resultArray['cardnumber'] = '';
			$resultArray['formattedcardnumber'] = '';
			$resultArray['cvvflag'] = '';
			$resultArray['cvvresponsecode'] = '';
			$resultArray['paymentcertificate'] = '';
			$resultArray['paymentdate'] = $serverDate;
			$resultArray['paymentmeans'] = $lapPaymentMethod;
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
			$resultArray['update'] = false;
			$resultArray['orderid'] = -1;
			$resultArray['parentlogid'] = 0;
			$resultArray['resultisarray'] = false;
			$resultArray['resultlist'] = array();
			$resultArray['showerror'] = false;
		}

		switch($transactionState)
		{
			case 5: // Transaction expired
			case 6: // Declined / Rejected
			case 104: // Error
			{
				$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $transactionState . ' ' . $polResponseCode . ' ' . $lapResponseCode;
				$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $message;
				$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $transactionId;
				$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $referenceCode;
				$resultArray['errorform'] = 'error.tpl';
				$showError = true;
				break;
			}
		}

		$resultArray['showerror'] = $showError;
        $resultArray['ref'] = $ref;

		return $resultArray;
    }

    static function automaticCallback()
    {
		global $gSession;

		$PayUConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayU.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

		$resultArray = Array();
        $error = '';
        $showError = false;
        $update = false;
        $result = '';
        $order_id = 0;
        $paymentReceived = 0;
        $amount = $gSession['order']['ordertotaltopay'];
        $webBrandCode = $gSession['webbrandcode'];
		$ref = $gSession['ref'];
		$authorised = false;
		$parentID = 0;

		$state = UtilsObj::getPOSTParam('state_pol');
		$authorisedStatus = $state;
		$response_code_pol = UtilsObj::getPOSTParam('response_code_pol');
		$response_message_pol = UtilsObj::getPOSTParam('response_message_pol');
		$order_id = UtilsObj::getPOSTParam('reference_sale');
		$reference_pol = UtilsObj::getPOSTParam('reference_pol');
		$sign = UtilsObj::getPOSTParam('sign');
		$payment_method = UtilsObj::getPOSTParam('payment_method');
		$payment_method_type = UtilsObj::getPOSTParam('payment_method_type');
		$value = UtilsObj::getPOSTParam('value');
		$currency =  UtilsObj::getPOSTParam('currency');
		$cus = UtilsObj::getPOSTParam('cus', '');
		$pse_bank = UtilsObj::getPOSTParam('pse_bank', '');
		$authorization_code = UtilsObj::getPOSTParam('authorization_code');
		$transaction_id = UtilsObj::getPOSTParam('transaction_id');
		$payment_method_name = UtilsObj::getPOSTParam('payment_method_name');
		$message = UtilsObj::getPOSTParam('description');
		$transaction_bank_id = UtilsObj::getPOSTParam('transaction_bank_id');
		$error_message_bank = UtilsObj::getPOSTParam('error_message_bank');

		$PayUConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayU.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

		switch ($state)
		{
			case 4: // Approved
			{
				$authorised = true;
				$paymentReceived = 0;

				// check signatures match
				$apiKey = $PayUConfig['APIKEY'];
				$merchantId = $PayUConfig['MERCHANTID'];

				// generate signature to check
				$signature = self::generateConfirmSignature($apiKey, $merchantId, $order_id, $value, $currency, $state);

				if ($signature != $sign)
				{
					$message .= ' - security signatures did not match';
					$paymentReceived = 0;
				}
				else
				{
					$paymentReceived = 1;
				}

				break;
			}
			case 7: // Pending
			{
				$authorised = true;
				$paymentReceived = 0;

				// check signatures match
				$apiKey = $PayUConfig['APIKEY'];
				$merchantId = $PayUConfig['MERCHANTID'];

				// generate signature to check
				$signature = self::generateConfirmSignature($apiKey, $merchantId, $order_id, $value, $currency, $state);

				if ($signature != $sign)
				{
					$message .= ' - security signatures did not match';
				}

				break;
			}
			case 5: // expired
			case 6: // declined
			default:
			{
				$authorised = false;
				$paymentReceived = 0;
				break;
			}
		}

		$serverTimestamp = DatabaseObj::getServerTime();
        $serverDate = date('Y-m-d');
        $serverTime = date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($PayUConfig, $serverTimestamp);

		// Check if there is an existing CCI Log entry for this reference.
		$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

		// CCI Log exists, so get the parent log id and set update to true
		if (count($cciLogEntry) > 0)
		{
			// May need to update the CCI Log Entry here
			if ($cciLogEntry['parentlogid'] != 0)
			{
				$parentID = $cciLogEntry['parentlogid'];
			}
			else
			{
				$parentID = $cciLogEntry['id'];
			}

			$update = true;
		}

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
        $resultArray['responsecode'] = $reference_pol;
        $resultArray['responsedescription'] = $message;
        $resultArray['authorisationid'] = $authorization_code;
        $resultArray['formattedauthorisationid'] = $authorization_code;
        $resultArray['bankresponsecode'] = $transaction_bank_id;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = $payment_method_name;
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
        $resultArray['parentlogid'] = $parentID;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
        $resultArray['showerror'] = false;

        return $resultArray;
	}
}
?>