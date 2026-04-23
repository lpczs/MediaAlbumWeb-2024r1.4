<?php

class CPayObj
{
	static function configure()
	{
		global $gSession;

		$resultArray = array();

		AuthenticateObj::clearSessionCCICookie();

		$cpayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/cPay.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
		$currencyList = $cpayConfig['CURRENCIES'];
		$currency = $gSession['order']['basecurrencycode'];
		$active = true;

		// Test for cPay supported currencies (this applies to the currency defined in the System Constants, NOT the order currency)
		if (strpos($currencyList, $currency) === false)
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
		global $gSession;

		$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
		$returnPath = UtilsObj::correctPath($gSession['webbrandwebroot']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$cpayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/cPay.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

			$language = $gSession['browserlanguagecode'];

			if (strpos($language, 'en_') !== false)
			{
				$language = 'en';
			}	

			// Select the server url based on the language
			if ($language == 'mk')
			{
				// Use Macedonian lanuage
				$server = $cpayConfig['SERVERMK'];
			}
			else if ($language == 'bg')
			{
				// Use Bulgarian language
				$server = $cpayConfig['SERVERBG'];
			}
			else
			{
				// Use English language
				$server = $cpayConfig['SERVEREN'];
			}

			// Create address string
			$addressList = array(
				$gSession['order']['billingcustomeraddress1'],
				$gSession['order']['billingcustomeraddress2'],
				$gSession['order']['billingcustomeraddress3'],
				$gSession['order']['billingcustomeraddress4']
			);

			$address = '';

			for ($i = 0; $i < 4; $i++)
			{
				if (($address != '') && ($addressList[$i] != ''))
				{
					$address .= ', ';
				}

				$address .= $addressList[$i];
			}

			// If the order currency is not MKD, convert the order total price into MKD using the exchange rate (System Constants Currency needs to be set to MKD)
			if ($gSession['order']['currencycode'] != 'MKD')
			{
				$orderTotal = self::convertCurrency($gSession['order']['ordertotaltopay']);
			}
			else
			{
				$orderTotal = $gSession['order']['ordertotaltopay'];
			}

			$parameters = array(
				'PaymentOKURL' => $returnPath, 
				'PaymentFailURL' => $cancelReturnPath, 
				'AmountToPay' => round($orderTotal * 100, 0), // price must be multiplied by 100
				'AmountCurrency' => 'MKD', // only MKD are accepted according to cPay support
				'PayToMerchant' => $cpayConfig['MERCHANTID'],
				'Details1' => self::stripInvalidCharacters($gSession['items'][0]['itemprojectname'], false, true),
				'Details2' => $gSession['ref'] . '_' . time(),
				'MerchantName' => $cpayConfig['MERCHANTNAME'],
				'FirstName' => self::stripInvalidCharacters($gSession['order']['billingcontactfirstname']),
				'LastName' => self::stripInvalidCharacters($gSession['order']['billingcontactlastname']),
				'Telephone' => self::stripInvalidCharacters($gSession['order']['billingcustomertelephonenumber']),
				'Email' => self::stripInvalidCharacters($gSession['order']['billingcustomeremailaddress'], false, true),
				'Zip' => self::stripInvalidCharacters($gSession['shipping'][0]['shippingcustomerpostcode'], true),
				'Address' => self::stripInvalidCharacters($address, false, true),
				'City' => self::stripInvalidCharacters($gSession['order']['billingcustomercity']),
				'Country' => $gSession['shipping'][0]['shippingcustomercountrycode'],
				'OriginalAmount' => $gSession['order']['ordertotaltopay'],
				'OriginalCurrency' => $gSession['order']['currencycode'],
			);

			// Add the checksum data
			$checkSum = self::generateCheckSum($parameters, $cpayConfig['PASSWORD']);	
			$parameters['CheckSumHeader'] = $checkSum['header'];
			$parameters['CheckSum'] = $checkSum['hash'];

			// Define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('parameter', $parameters);
			$smarty->assign('method', 'POST');

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped
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

	static function manualCallback()
	{
     	global $gSession;

		$cpayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/cPay.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);			

		$resultArray = array();
        $update = false;
		$ref = $gSession['ref'];
		$parentID = 0;
		$showError = false;
		$responseCode = '';

		// Create a checksum using the returned data and compare this to the ReturnCheckSum		
		$parameters = array(
			'PaymentFailURL' => UtilsObj::getPOSTParam('PaymentFailURL'),
			'PaymentOKURL' => UtilsObj::getPOSTParam('PaymentOKURL'),
			'AmountToPay' => UtilsObj::getPOSTParam('AmountToPay'),
			'AmountCurrency' => UtilsObj::getPOSTParam('AmountCurrency'),
			'PayToMerchant' => $cpayConfig['MERCHANTID'],
			'Details1' => UtilsObj::getPOSTParam('Details1'),
			'Details2' => UtilsObj::getPOSTParam('Details2'),
			'MerchantName' => $cpayConfig['MERCHANTNAME'],
			'FirstName' => UtilsObj::getPOSTParam('FirstName'),
			'LastName' => UtilsObj::getPOSTParam('LastName'),
			'Telephone' => UtilsObj::getPOSTParam('Telephone'),
			'Email' => UtilsObj::getPOSTParam('Email'),
			'Zip' => UtilsObj::getPOSTParam('Zip'),
			'Address' => UtilsObj::getPOSTParam('Address'),
			'City' => UtilsObj::getPOSTParam('City'),
			'Country' => UtilsObj::getPOSTParam('Country'),
			'OriginalAmount' => UtilsObj::getPOSTParam('OriginalAmount'),
			'OriginalCurrency' => UtilsObj::getPOSTParam('OriginalCurrency'),
			'cPayPaymentRef' => UtilsObj::getPOSTParam('cPayPaymentRef'),
		);

		$returnCheckSum = self::generateCheckSum($parameters, $cpayConfig['PASSWORD']);

		if ($returnCheckSum['hash'] == UtilsObj::getPOSTParam('ReturnCheckSum'))
		{
			$authorised = true;
			$paymentReceived = 1;
			$responseCode = 'SUCCESS';
		}
		else
		{
			$authorised = false;
			$paymentReceived = 0;
			$showError = true;
			$responseCode = 'INVALID';

			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $responseCode;
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . self::getResponseDescription($responseCode);
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . UtilsObj::getPOSTParam('Details2');
			$resultArray['data4'] = '';
			$resultArray['errorform'] = 'error.tpl';
			$resultArray['update'] = true;
		}

		$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime = date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($cpayConfig, $serverTimestamp);

		// Check if there is an existing CCI Log entry for this reference. 
		$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

		// CCI Log exists, so get the parent log id and set update to true
		if (count($cciLogEntry) > 0)
		{
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

		// Set results
		$resultArray['result'] = '';
		$resultArray['ref'] = $ref;
		$resultArray['amount'] = UtilsObj::getPOSTParam('AmountToPay');
		$resultArray['formattedamount'] = round(UtilsObj::getPOSTParam('AmountToPay') / 100, 2);
		$resultArray['charges'] = '';
		$resultArray['formattedcharges'] = '';
		$resultArray['authorised'] = $authorised;
		$resultArray['authorisedstatus'] = ($authorised  == true) ? 1 : 0;
		$resultArray['transactionid'] = UtilsObj::getPOSTParam('Details2');
		$resultArray['formattedtransactionid'] = UtilsObj::getPOSTParam('Details2');
		$resultArray['responsecode'] = $responseCode;
		$resultArray['responsedescription'] = self::getResponseDescription($responseCode);
		$resultArray['authorisationid'] = UtilsObj::getPOSTParam('cPayPaymentRef');
		$resultArray['formattedauthorisationid'] = UtilsObj::getPOSTParam('cPayPaymentRef');
		$resultArray['bankresponsecode'] = UtilsObj::getPOSTParam('ReturnCheckSum');
		$resultArray['cardnumber'] = '';
		$resultArray['formattedcardnumber'] = '';
		$resultArray['cvvflag'] = '';
		$resultArray['cvvresponsecode'] = '';
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
		$resultArray['transactiontype'] = 'One Step Payment';
		$resultArray['settleamount'] = '';
		$resultArray['currencycode'] = UtilsObj::getPOSTParam('AmountCurrency');
		$resultArray['webbrandcode'] = $gSession['webbrandcode'];
		$resultArray['script'] = '';
		$resultArray['scripturl'] = '';
		$resultArray['charityflag'] = '';
		$resultArray['threedsecurestatus'] = '';
		$resultArray['cavvresponsecode'] = '';
		$resultArray['update'] = $update;
		$resultArray['orderid'] = 0;
		$resultArray['parentlogid'] = $parentID;
		$resultArray['resultisarray'] = false;
		$resultArray['resultlist'] = array();
		$resultArray['showerror'] = $showError;

        return $resultArray;
	}

	static function cancel()
	{
     	global $gSession;

		$cpayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/cPay.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

		$resultArray = array();
        $update = false;
		$ref = $gSession['ref'];
		$parentID = 0;
		$responseCode = '';

		// Create a checksum using the returned data and compare this to the ReturnCheckSum		
		$parameters = array(
			'PaymentFailURL' => UtilsObj::getPOSTParam('PaymentFailURL'),
			'PaymentOKURL' => UtilsObj::getPOSTParam('PaymentOKURL'),
			'AmountToPay' => UtilsObj::getPOSTParam('AmountToPay'),
			'AmountCurrency' => UtilsObj::getPOSTParam('AmountCurrency'),
			'PayToMerchant' => $cpayConfig['MERCHANTID'],
			'Details1' => UtilsObj::getPOSTParam('Details1'),
			'Details2' => UtilsObj::getPOSTParam('Details2'),
			'MerchantName' => $cpayConfig['MERCHANTNAME'],
			'FirstName' => UtilsObj::getPOSTParam('FirstName'),
			'LastName' => UtilsObj::getPOSTParam('LastName'),
			'Telephone' => UtilsObj::getPOSTParam('Telephone'),
			'Email' => UtilsObj::getPOSTParam('Email'),
			'Zip' => UtilsObj::getPOSTParam('Zip'),
			'Address' => UtilsObj::getPOSTParam('Address'),
			'City' => UtilsObj::getPOSTParam('City'),
			'Country' => UtilsObj::getPOSTParam('Country'),
			'OriginalAmount' => UtilsObj::getPOSTParam('OriginalAmount'),
			'OriginalCurrency' => UtilsObj::getPOSTParam('OriginalCurrency'),
			'cPayPaymentRef' => UtilsObj::getPOSTParam('cPayPaymentRef'),
		);

		$returnCheckSum = self::generateCheckSum($parameters, $cpayConfig['PASSWORD']);

		if ($returnCheckSum['hash'] == UtilsObj::getPOSTParam('ReturnCheckSum'))
		{
			$responseCode = 'CANCEL';
		}
		else
		{
			$responseCode = 'INVALID';
		}

		$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime = date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($cpayConfig, $serverTimestamp);		

		// Check if there is an existing CCI Log entry for this reference. 
		$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

		// CCI Log exists, so get the parent log id and set update to true
		if (count($cciLogEntry) > 0)
		{
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

		// Set results
		$resultArray['result'] = '';
		$resultArray['ref'] = $ref;
		$resultArray['amount'] = UtilsObj::getPOSTParam('AmountToPay');
		$resultArray['formattedamount'] = round(UtilsObj::getPOSTParam('AmountToPay') / 100, 2);
		$resultArray['charges'] = '';
		$resultArray['formattedcharges'] = '';
		$resultArray['authorised'] = false;
		$resultArray['authorisedstatus'] = 0;
		$resultArray['transactionid'] = UtilsObj::getPOSTParam('Details2');
		$resultArray['formattedtransactionid'] = UtilsObj::getPOSTParam('Details2');
		$resultArray['responsecode'] = $responseCode;
		$resultArray['responsedescription'] = self::getResponseDescription($responseCode);
		$resultArray['authorisationid'] = UtilsObj::getPOSTParam('cPayPaymentRef');
		$resultArray['formattedauthorisationid'] = UtilsObj::getPOSTParam('cPayPaymentRef');
		$resultArray['bankresponsecode'] = UtilsObj::getPOSTParam('ReturnCheckSum');
		$resultArray['cardnumber'] = '';
		$resultArray['formattedcardnumber'] = '';
		$resultArray['cvvflag'] = '';
		$resultArray['cvvresponsecode'] = '';
		$resultArray['paymentcertificate'] = '';
		$resultArray['paymentdate'] = $serverDate;
		$resultArray['paymentmeans'] = '';
		$resultArray['paymenttime'] = $serverTime;
		$resultArray['paymentreceived'] = 0;
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
		$resultArray['transactiontype'] = 'One Step Payment';
		$resultArray['settleamount'] = '';
		$resultArray['currencycode'] = UtilsObj::getPOSTParam('AmountCurrency');
		$resultArray['webbrandcode'] = $gSession['webbrandcode'];
		$resultArray['script'] = '';
		$resultArray['scripturl'] = '';
		$resultArray['charityflag'] = '';
		$resultArray['threedsecurestatus'] = '';
		$resultArray['cavvresponsecode'] = '';
		$resultArray['update'] = $update;
		$resultArray['orderid'] = 0;
		$resultArray['parentlogid'] = $parentID;
		$resultArray['resultisarray'] = false;
		$resultArray['resultlist'] = array();
		$resultArray['showerror'] = true;
		$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $responseCode;
		$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . self::getResponseDescription($responseCode);
		$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . UtilsObj::getPOSTParam('Details2');
		$resultArray['data4'] = '';
		$resultArray['errorform'] = 'error.tpl';
		$resultArray['update'] = $update;

        return $resultArray;
	}

	/**
	 * Generate a check sum for cPay
	 * 
	 * @param array $pCheckSumParams
	 * 
	 * @return string
	 */
	static function generateCheckSum($pCheckSumParams, $merchantPassword)
	{
		$checkSumHeader = null;
		$checkSumLength = null;
		$checkSumValues = null;
		$fieldCount = 0;	

		foreach ($pCheckSumParams as $key => $value)
		{
			if ($value != '')
			{
				$checkSumHeader .= $key . ',';
				$checkSumLength .= self::mb_str_pad(mb_strlen($value, 'UTF-8'), 3, 0, STR_PAD_LEFT, null);
				$checkSumValues .= $value;
				$fieldCount++;
			}
		}

		$finalCheckSumHeader = str_pad($fieldCount, 2, 0, STR_PAD_LEFT) . $checkSumHeader . $checkSumLength;

		$checkSum = hash('md5', $finalCheckSumHeader . $checkSumValues . $merchantPassword);

		return array(
			'header' => $finalCheckSumHeader, 
			'hash' => strtoupper($checkSum)
		);
	}

	/**
	 * Gets the response description for the specified code
	 * 
	 * @param string $responseCode
	 * 
	 * @return string
	 */
	static function getResponseDescription($responseCode)
	{
		$responseDescription = Array
		(
			'SUCCESS' => 'Authentication Successful',
			'INVALID' => 'The security checksum was invalid',
			'CANCEL' => 'The order was cancelled due to an error with processing your payment'
		);

		if (isset($responseDescription[$responseCode]))
		{
			return $responseDescription[$responseCode];
		}
		else
		{
			return 'Unknown cPay Error';
		}
	}

	/**
	 * Multi-byte version of str_pad.
	 * http://php.net/manual/en/function.str-pad.php
	 * 
	 * @param string $str
	 * @param int $pad_len
	 * @param string $pad_str
	 * @param string $dir
	 * @param string $encoding
	 * 
	 * @return string
	 */
	static function mb_str_pad($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT, $encoding = NULL)
	{
		$encoding = $encoding === NULL ? mb_internal_encoding() : $encoding;
		$padBefore = $dir === STR_PAD_BOTH || $dir === STR_PAD_LEFT;
		$padAfter = $dir === STR_PAD_BOTH || $dir === STR_PAD_RIGHT;
		$pad_len -= mb_strlen($str, $encoding);
		$targetLen = $padBefore && $padAfter ? $pad_len / 2 : $pad_len;
		$strToRepeatLen = mb_strlen($pad_str, $encoding);
		$repeatTimes = ceil($targetLen / $strToRepeatLen);
		$repeatedString = str_repeat($pad_str, max(0, $repeatTimes)); // safe if used with valid utf-8 strings
		$before = $padBefore ? mb_substr($repeatedString, 0, floor($targetLen), $encoding) : '';
		$after = $padAfter ? mb_substr($repeatedString, 0, ceil($targetLen), $encoding) : '';

		return $before . $str . $after;
	}

	/**
	 * Convert order total price to MKD
	 *
	 * @global array $gSession
	 *
	 * @param float $pOrderTotal
	 *
	 * @return float
	 */
	static function convertCurrency($pOrderTotal)
	{
		global $gSession;

		$exchangeRate = $gSession['order']['currencyexchangerate'];
		$decimalPlaces = $gSession['order']['currencydecimalplaces'];

		$currencyItem = DatabaseObj::getCurrency('MKD');

		if (($currencyItem['result'] == '') || ($exchangeRate == 0))
		{
			$exchangeRate = $currencyItem['exchangerate'] / $exchangeRate;
			$decimalPlaces = $currencyItem['decimalplaces'];
		}

		return UtilsObj::bround($pOrderTotal * $exchangeRate, $decimalPlaces);
	}

	/**
	 * Strip out invalid characters
	 *
	 * @param string $pString
	 * @param boolean $pAllowSpaceCharacters
	 *
	 * @return string
	 */
	static function stripInvalidCharacters($pString, $pNumericOnly = false, $pAllowSpaceCharacters = false)
	{
		if ($pNumericOnly)
		{
			// strip out non-numeric characters
			$regex = '/[^\d]/u';
		}
		else if ($pAllowSpaceCharacters)
		{
			// strip out anything not a-z, 0-9 or space character
			// space characters = " ", @, /, -
			$regex = '/[^a-z\d\s@\/-]/ui';
		}
		else
		{
			// strip out anything not a-z, 0-9
			$regex = '/[^a-z\d]/ui';
		}

		$newString = preg_replace($regex, '', $pString);
		$newString = str_replace('@@', '@', $newString);

		return $newString;
	}
}