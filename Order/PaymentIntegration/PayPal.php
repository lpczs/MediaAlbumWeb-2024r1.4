<?php

class PayPalObj
{
	static $apiVersion = '98';
	static $paymentAction = 'Sale';
	static $serverURLString = '?cmd=_express-checkout&token=%s&useraction=commit';
	static $defaultSolutionType = 'Mark';

    static function configure()
    {
        global $gSession;

        $resultArray = array();
        $active = true;
        $payPalExpressConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayPal.conf', $gSession['order']['currencycode'], $gSession['webbrandcode'], 'PAYPAL');

		// check the config exists and has been configured correctly
		if (count($payPalExpressConfig) > 0)
		{

			// make sure the currency is valid
			$currenciesArray = array
			(
				'AUD', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HUF', 'JPY', 'NOK', 'NZD', 'PLN', 'SEK', 'SGD', 'USD', 'MYR', 'TWD', 'ILS',
				'MXN', 'PHP', 'THB', 'BRL'
			);

			// check the currency conversion
			$orderCurrencyArray = self::getOrderCurrency();

			if ($orderCurrencyArray['ordercurrency'] != '')
			{
				// currency has been transformed so add the order currency code to the $currencyListArray
				if ($orderCurrencyArray['currencyexchanged'] == 1)
				{
					$currenciesArray[] = $gSession['order']['currencycode'];
				}

				// make sure the currency is valid
				if (! in_array($orderCurrencyArray['ordercurrency'], $currenciesArray))
				{
					$active = false;
				}
			}
			else
			{
				$active = false;
			}

			// check HTTPS is enabled for live servers

			if (($active) && ((! isset($_SERVER['HTTPS'])) || ($_SERVER['HTTPS'] == 'off')) && ($payPalExpressConfig['EXPRESS_SANDBOX'] == 0))
			{
				$active = false;
			}
		}
		else
		{
			$active = false;
		}

        AuthenticateObj::clearSessionCCICookie();

        $resultArray['active'] = $active;
        $resultArray['script'] = '';
        $resultArray['form'] = '';
        $resultArray['scripturl'] = '';
        $resultArray['action'] = '';

        return $resultArray;
    }

	static function paymentOptionsCallback()
	{
		return array();
	}

    static function initialize()
    {
        global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        // first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
            $payPalExpressConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayPal.conf', $gSession['order']['currencycode'], $gSession['webbrandcode'], 'PAYPAL');

            $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&pm=PAYPAL&ref=' . $gSession['ref'];
            $normalReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&pm=PAYPAL&ref=' . $gSession['ref'];

			// do the currency conversion if needed
			$orderCurrencyArray = self::getOrderCurrency();

            if ($orderCurrencyArray['currencyexchanged'] == true)
            {
                $orderTotal = UtilsObj::bround($gSession['order']['ordertotaltopay'] * $orderCurrencyArray['exchangerate'], $orderCurrencyArray['decimalplaces']);
                $amount = number_format($orderTotal, $orderCurrencyArray['decimalplaces'], '.', '');
            }
            else
            {
                $amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');
            }

			$setExpressCheckoutResultParsed = array();
			
			$description = LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);

			if ($gSession['items'][0]['itemqty'] > 1)
            {
                $description = $gSession['items'][0]['itemqty'] . ' x ' . $description;
            }

			$state = '';
			if (strtolower($gSession['order']['billingcustomerregion']) == 'state')
			{
				if ($gSession['order']['billingcustomerstate'] != '')
				{
					if (strtolower($gSession['order']['billingcustomercountrycode']) == 'us')
					{
						$state = $gSession['order']['billingcustomerregioncode'];
					}
					else
					{
						$state = $gSession['order']['billingcustomerstate'];
					}
				}
				else
				{
					if (strtolower($gSession['order']['defaultbillingcustomercountrycode']) == 'us')
					{
						$state = $gSession['order']['defaultbillingcustomerregioncode'];
					}
					else
					{
						$state = $gSession['order']['defaultbillingcustomerstate'];
					}
				}
			}
			else
			{
				if ($gSession['order']['billingcustomercounty'] != '')
				{
					$state = $gSession['order']['billingcustomercounty'];
				}
				else
				{
					$state = $gSession['order']['defaultbillingcustomercounty'];
				}
			}

			$localeCode = '';

			// try to map the selected language to a PayPal locale

			switch(strtolower($gSession['browserlanguagecode']))
			{
				case 'de':
				case 'es':
				case 'fr':
				case 'it':
				case 'pl':
				case 'pt':
				case 'nl':
				case 'ru':
				{
					$localeCode = strtoupper($gSession['browserlanguagecode']);
					break;
				}
				case 'cs':
				{
					$localeCode = 'sv_SE';
					break;
				}
				case 'da':
				{
					$localeCode = 'da_DK';
					break;
				}
				case 'en':
				{
					// try to work out to use American, Australian or British English

					$browserLanguageArray = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
					$browserLanguage = strtolower($browserLanguageArray[0]);

					if (strpos($browserLanguage, 'au') !== false)
					{
						$localeCode = 'AU';
					}
					else if (strpos($browserLanguage, 'gb') !== false)
					{
						$localeCode = 'GB';
					}
					else if (strpos($browserLanguage, 'us') !== false)
					{
						$localeCode = 'US';
					}
					else
					{
						$localeCode = 'GB';
					}
					break;
				}
				case 'ja':
				{
					$localeCode = 'ja_JP';
					break;
				}
				case 'no':
				{
					$localeCode = 'no_NO';
					break;
				}
				case 'th':
				{
					$localeCode = 'th_TH';
					break;
				}
				case 'zh_cn':
				{
					$localeCode = 'zh_CN';
					break;
				}
				case 'zh_tw':
				{
					$localeCode = 'zh_TW';
					break;
				}
				default:
				{
					$localeCode = 'GB';
					break;
				}
			}

			// check if guest checkout is allowed. Mark = disabled. Sole = enabled.
			$solutionType = UtilsObj::getArrayParam($payPalExpressConfig, 'EXPRESS_SOLUTIONTYPE');

			if ($solutionType == '')
			{
				$solutionType = self::$defaultSolutionType;
			}

			$setExpressCheckoutParams = array(
				'VERSION' => self::$apiVersion, // API version
				'USER' => urlencode($payPalExpressConfig['EXPRESS_API_USERNAME']),
				'PWD' => urlencode($payPalExpressConfig['EXPRESS_API_PASSWORD']),
				'SIGNATURE' => urlencode($payPalExpressConfig['EXPRESS_API_SIGNATURE']),
				'PAYMENTREQUEST_0_AMT' => $amount,
				'PAYMENTREQUEST_0_PAYMENTACTION' => self::$paymentAction,
				'PAYMENTREQUEST_0_CURRENCYCODE' => $orderCurrencyArray['ordercurrency'],
				'PAYMENTREQUEST_0_DESC' =>  $description,
				'NOSHIPPING' => 1, // disable showing/editing of shipping address
				'ALLOWNOTE' => 0, // disable notes feature
				'GIFTMESSAGEENABLE' => 0, // disable gift message option
				'GIFTWRAPENABLE' => 0, // disable gift wrap option
				'BUYEREMAILOPTINENABLE' => 0, // disable marketing option
				'GIFTRECEIPTENABLE' => 0, //disable gift receipt option
				'LOCALECODE' => $localeCode,
				'ADDROVERRIDE'=> 1, // enable address override
				'PAYMENTREQUEST_0_CUSTOM' => $gSession['ref'] . '_' . time(),
				'PAYMENTREQUEST_0_SHIPTONAME' => $gSession['order']['billingcontactfirstname'] . ' ' . $gSession['order']['billingcontactlastname'],
				'PAYMENTREQUEST_0_SHIPTOSTREET' => $gSession['order']['billingcustomeraddress1'],
				'PAYMENTREQUEST_0_SHIPTOSTREET2' => $gSession['order']['billingcustomeraddress2'],
				'PAYMENTREQUEST_0_SHIPTOCITY' => $gSession['order']['billingcustomercity'],
				'PAYMENTREQUEST_0_SHIPTOSTATE' => $state,
				'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => $gSession['order']['billingcustomercountrycode'],
				'PAYMENTREQUEST_0_SHIPTOZIP' => $gSession['order']['billingcustomerpostcode'],
				'PAYMENTREQUEST_0_SHIPTOPHONENUM' => $gSession['order']['billingcustomertelephonenumber'],
				'EMAIL' => $gSession['order']['billingcustomeremailaddress'],
				'HDRIMG' => (isset($payPalExpressConfig['EXPRESS_HEADER_IMG']) ? $payPalExpressConfig['EXPRESS_HEADER_IMG'] : ''),
				'LOGOIMG' => (isset($payPalExpressConfig['EXPRESS_LOGO_IMG']) ? $payPalExpressConfig['EXPRESS_LOGO_IMG'] : ''),
				'BRANDNAME' => (isset($payPalExpressConfig['EXPRESS_BRAND_NAME']) ? $payPalExpressConfig['EXPRESS_BRAND_NAME'] : ''),
				'CHANNELTYPE' => 'Merchant',
				'RETURNURL' => $normalReturnPath,
				'CANCELURL' => $cancelReturnPath,
				'SOLUTIONTYPE' => $solutionType
			);

			// generate api token

			$SetExpressCheckoutResult = self::APICall($payPalExpressConfig['EXPRESS_API_SERVER'], 'SetExpressCheckout', $setExpressCheckoutParams);

			parse_str($SetExpressCheckoutResult, $setExpressCheckoutResultParsed);
			
			$token = '';
			if ($setExpressCheckoutResultParsed['ACK'] == 'Success')
			{
				$token = $setExpressCheckoutResultParsed['TOKEN'];
			}

			if ($token != '')
			{
				$smarty->assign('payment_url', $payPalExpressConfig['EXPRESS_SERVER'] . sprintf(self::$serverURLString, $token));
				$smarty->assign('cancel_url', $cancelReturnPath);
				$smarty->assign('parameter', array());
				$smarty->assign('method', 'post');

				AuthenticateObj::defineSessionCCICookie();
				$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
				$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

				// set the ccidata to remember we have jumped to PayPal

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
				$errorMessageShort = $setExpressCheckoutResultParsed['L_SHORTMESSAGE0'];
				$errorMessageLong = $setExpressCheckoutResultParsed['L_LONGMESSAGE0'];
				$errorCode = $setExpressCheckoutResultParsed['L_ERRORCODE0'];

				$smarty->assign('data1', SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $errorCode);
				$smarty->assign('data2', SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $errorMessageShort . ' - ' . $errorMessageLong);

				if ($gSession['ismobile'] == true)
				{
					$smarty->assign('displayInline', true);
					$smarty->assign('homeurl', UtilsObj::correctPath($gSession['webbrandweburl']));
					$smarty->assign('ref', $gSession['ref']);

					$resultArray['template'] = $smarty->fetchLocale('order/PaymentIntegration/error_small.tpl', $gSession['browserlanguagecode']);
					$resultArray['javascript'] = '';
					$resultArray['showerror'] = true;
					return $resultArray;
				}
				else
				{
					$smarty->displayLocale('order/PaymentIntegration/error_large.tpl');
				}
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

    static function cancel()
    {
        $resultArray = array();

        $resultArray['result'] = '';
        $resultArray['ref'] = $_GET['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['authorisedstatus'] = 0;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

	static function getAdditionalPaymentInfo($pParamArray)
	{
		return '';
	}

	// PayPal Express has manual callback only

    static function confirm()
    {
        global $gSession;

        $resultArray = array();
		$error = false;
		$errorCode = '';
		$errorMessageShort = '';
		$errorMessageLong = '';
		$correlationID = '';

        $authorised = false;
        $authorisedStatus = 0;
        $responseCode = '';
        $paymentReceived = 0;

        $payPalExpressConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayPal.conf', $gSession['order']['currencycode'], $gSession['webbrandcode'], 'PAYPAL');

		$APIUsername = urlencode($payPalExpressConfig['EXPRESS_API_USERNAME']);
		$APIPassword = urlencode($payPalExpressConfig['EXPRESS_API_PASSWORD']);
		$APISignature = urlencode($payPalExpressConfig['EXPRESS_API_SIGNATURE']);

		$token = UtilsObj::getGETParam('token', 'NO_TOKEN');
		$payerID = UtilsObj::getGETParam('PayerID', 'NO_PAYERID');

		$doExpressCheckoutPaymentParsed = array();
		$getExpressCheckoutDetailsParsed = array();
		$getExpressCheckoutDetailsParsed2 = array();
		$payerStatus = '';
		$payerEmail = '';
		$addressStatus = '';
		$pendingReason = '';
		$PayPalFees = '0.00';
		$transactionType = '';
		$paymentType = '';
		$paymentTransactionID = '';
		$transactionID = 0;
		$update = false;

		// do the currency conversion if needed
		$orderCurrencyArray = self::getOrderCurrency();

		if ($orderCurrencyArray['currencyexchanged'] == true)
		{
			$orderTotal = UtilsObj::bround($gSession['order']['ordertotaltopay'] * $orderCurrencyArray['exchangerate'], $orderCurrencyArray['decimalplaces']);
			$amount = number_format($orderTotal, $orderCurrencyArray['decimalplaces'], '.', '');
		}
		else
		{
			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');
		}

		if ($token != 'NO_TOKEN')
		{
			// call GetExpressCheckoutDetailsParams to get payment status and additional payment info

			$getExpressCheckoutDetailsParams = array(
				"VERSION" => self::$apiVersion,
				"USER" => $APIUsername,
				"PWD" => $APIPassword,
				"SIGNATURE" => $APISignature,
				"TOKEN" => $token
			);

			$getExpressCheckoutDetailsResult = self::APICall($payPalExpressConfig['EXPRESS_API_SERVER'], 'GetExpressCheckoutDetails', $getExpressCheckoutDetailsParams);

			parse_str($getExpressCheckoutDetailsResult, $getExpressCheckoutDetailsParsed);

			// check the result of the GetExpressCheckoutDetailsParams API call

			if (strtolower($getExpressCheckoutDetailsParsed['ACK']) == 'success')
			{
				// check the value of CHECKOUTSTATUS to check if payment has already been captured

				if (strtolower($getExpressCheckoutDetailsParsed['CHECKOUTSTATUS']) == 'paymentactionnotinitiated')
				{
					// call DoExpressCheckoutPayment to capture the payment

					$doExpressCheckoutPaymentParams = array(
						"VERSION" => self::$apiVersion,
						"USER" => $APIUsername,
						"PWD" => $APIPassword,
						"SIGNATURE" => $APISignature,
						"TOKEN" => $token,
						"PAYERID" => $payerID,
						"PAYMENTREQUEST_0_PAYMENTACTION" => self::$paymentAction,
						"PAYMENTREQUEST_0_AMT" => $amount,
						'PAYMENTREQUEST_0_CURRENCYCODE' => $orderCurrencyArray['ordercurrency']
					);

					$doExpressCheckoutPaymentResult = self::APICall($payPalExpressConfig['EXPRESS_API_SERVER'], 'DoExpressCheckoutPayment', $doExpressCheckoutPaymentParams);

					parse_str($doExpressCheckoutPaymentResult, $doExpressCheckoutPaymentParsed);

					// check the result of DoExpressCheckoutPayment API call

					if (strtolower($doExpressCheckoutPaymentParsed['ACK']) == 'success')
					{
						// check the payment was successful

						if (strtolower($doExpressCheckoutPaymentParsed['PAYMENTINFO_0_ACK']) == 'success')
						{
							$PayPalFees = $doExpressCheckoutPaymentParsed['PAYMENTINFO_0_FEEAMT'];
							$transactionType = $doExpressCheckoutPaymentParsed['PAYMENTINFO_0_TRANSACTIONTYPE'];
							$paymentType = $doExpressCheckoutPaymentParsed['PAYMENTINFO_0_PAYMENTTYPE'];
							$paymentTransactionID = $doExpressCheckoutPaymentParsed['PAYMENTINFO_0_TRANSACTIONID'];
							$pendingReason = $doExpressCheckoutPaymentParsed['PAYMENTINFO_0_PENDINGREASON'];

							$authorised = true;
							$authorisedStatus = 1;

							if (strtolower($doExpressCheckoutPaymentParsed['PAYMENTINFO_0_PAYMENTSTATUS']) == 'completed')
							{
								$paymentReceived = 1;
							}

							$getExpressCheckoutDetailsResult2 = self::APICall($payPalExpressConfig['EXPRESS_API_SERVER'], 'GetExpressCheckoutDetails', $getExpressCheckoutDetailsParams);

							parse_str($getExpressCheckoutDetailsResult2, $getExpressCheckoutDetailsParsed2);

							if (strtolower($getExpressCheckoutDetailsParsed2['ACK']) == 'success')
							{
								// use details from GetExpressCheckoutDetail

								$transactionID = $getExpressCheckoutDetailsParsed2['PAYMENTREQUEST_0_CUSTOM'];
								$payerEmail = $getExpressCheckoutDetailsParsed2['EMAIL'];
								$payerStatus = $getExpressCheckoutDetailsParsed2['PAYERSTATUS'];

								// PAYMENTREQUEST_0_ADDRESSSTATUS may not always be returned by PayPal

								if (array_key_exists('PAYMENTREQUEST_0_ADDRESSSTATUS', $getExpressCheckoutDetailsParsed2))
								{
									$addressStatus = $getExpressCheckoutDetailsParsed2['PAYMENTREQUEST_0_ADDRESSSTATUS'];
								}
								else
								{
									$addressStatus = '';
								}

								$responseCode = $getExpressCheckoutDetailsParsed2['CHECKOUTSTATUS'];
							}
							else
							{
								// payment has already been captured so don't show an error
								$message = 'Ref: ' . UtilsObj::getGetParam('ref', 0) . ' ' .$doExpressCheckoutPaymentParsed['CORRELATIONID'] . ' - ' . $doExpressCheckoutPaymentParsed['L_SHORTMESSAGE0'] . ' - ' . $doExpressCheckoutPaymentParsed['L_ERRORCODE0'];
								error_log($message);
							}
						}
						else
						{
							// some error codes such as 10486 or 10422 we can redirect back to PayPal for the real error message
							// otherwise default to our error template

							switch ($doExpressCheckoutPaymentParsed['L_ERRORCODE0'])
							{
								case '10486':
								case '10422':
								{
									header('Location:' . $payPalExpressConfig['EXPRESS_SERVER'] . sprintf(self::$serverURLString, $token));
									exit;
								}
								default:
								{
									$error = true;
									$correlationID = $doExpressCheckoutPaymentParsed['CORRELATIONID'];
									$errorMessageShort = $doExpressCheckoutPaymentParsed['L_SHORTMESSAGE0'];
									$errorMessageLong = $doExpressCheckoutPaymentParsed['L_LONGMESSAGE0'];
									$errorCode = $doExpressCheckoutPaymentParsed['L_ERRORCODE0'];
									break;
								}
							}
						}
					}
					else
					{
						// some error codes such as 10486 or 10422 we can redirect back to PayPal for the real error message
						// otherwise default to our error template

						switch ($doExpressCheckoutPaymentParsed['L_ERRORCODE0'])
						{
							case '10486':
							case '10422':
							{
								header('Location:' . $payPalExpressConfig['EXPRESS_SERVER'] . sprintf(self::$serverURLString, $token));
								exit;
							}
							default:
							{
								$error = true;
								$correlationID = $doExpressCheckoutPaymentParsed['CORRELATIONID'];
								$errorMessageShort = $doExpressCheckoutPaymentParsed['L_SHORTMESSAGE0'];
								$errorMessageLong = $doExpressCheckoutPaymentParsed['L_LONGMESSAGE0'];
								$errorCode = $doExpressCheckoutPaymentParsed['L_ERRORCODE0'];
								break;
							}
						}
					}
				}
				else
				{
					// payment capture as already happened

					if (strtolower($getExpressCheckoutDetailsParsed['CHECKOUTSTATUS']) == 'paymentactioncompleted')
					{
						$authorised = true;
						$authorisedStatus = 1;
						$update = true;
					}
					else
					{
						$error = true;
						$correlationID = $doExpressCheckoutPaymentParsed['CORRELATIONID'];
						$errorMessageShort = $getExpressCheckoutDetailsParsed['CHECKOUTSTATUS'];
						$errorMessageLong = '';
						$errorCode = $getExpressCheckoutDetailsParsed['CHECKOUTSTATUS'];
					}
				}
			}
			else
			{
				$error = true;
				$correlationID = $getExpressCheckoutDetailsParsed['CORRELATIONID'];
				$errorMessageShort = 'GetExpressCheckoutDetails - ' . $getExpressCheckoutDetailsParsed['L_SHORTMESSAGE0'];
				$errorMessageLong = $getExpressCheckoutDetailsParsed['L_LONGMESSAGE0'];
				$errorCode = $getExpressCheckoutDetailsParsed['L_ERRORCODE0'];
			}
		}

		if ($error)
		{
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $errorCode;
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $errorMessageShort . ': ' . $errorMessageLong;
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $gSession['ref'];
			$resultArray['data4'] = '';
			$resultArray['errorform'] = 'error.tpl';
			$resultArray['showerror'] = true;
		}

		// check if a CCILog already exists

		$logID = 0;
        $orderID = $transactionID;

		$CCILogResult = PaymentIntegrationObj::getCciLogEntry(UtilsObj::getGETParam('ref'));

		if (count($CCILogResult) > 0)
		{
			$logID = $CCILogResult['id'];
		}

        // write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
		PaymentIntegrationObj::logPaymentGatewayData($payPalExpressConfig, $serverTimestamp, '', $doExpressCheckoutPaymentParsed);

		$serverDate = date('Y-m-d');
        $serverTime = date("H:i:s");

        $resultArray['authorised'] = $authorised;
        $resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['result'] = '';
        $resultArray['ref'] = UtilsObj::getGETParam('ref');
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = $PayPalFees;
        $resultArray['formattedcharges'] = $resultArray['charges'];
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymenttime'] = $serverTime;
        $resultArray['authorisationid'] = $token;
        $resultArray['transactionid'] = $paymentTransactionID;
        $resultArray['paymentmeans'] = $paymentType;
        $resultArray['addressstatus'] = $addressStatus;
        $resultArray['payerid'] = $payerID;
        $resultArray['payerstatus'] = $payerStatus;
        $resultArray['payeremail'] = $payerEmail;
        $resultArray['business'] = $payPalExpressConfig['EXPRESS_API_USERNAME'];
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = $pendingReason;
        $resultArray['transactiontype'] = $transactionType;
        $resultArray['currencycode'] = $orderCurrencyArray['ordercurrency'];;
		$resultArray['webbrandcode'] = $gSession['webbrandcode'];
        $resultArray['settleamount'] = $amount;
		$resultArray['paymentreceived'] = $paymentReceived;
        $resultArray['formattedpaymentdate'] = $serverTimestamp;
        $resultArray['formattedtransactionid'] = '';
        $resultArray['formattedauthorisationid'] = '';
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['responsecode'] = $responseCode;
        $resultArray['bankresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['responsedescription'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['charityflag'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = $orderID;
        $resultArray['parentlogid'] = $logID;
		$resultArray['error'] = $error;
        $resultArray['showerror'] = $resultArray['error'];
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = array();

        return $resultArray;
    }

	static function APICall($pURL, $pAction, $pPostParamArray)
	{
		$pPostParamArray['METHOD'] = $pAction;

		$postData = http_build_query($pPostParamArray);

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $pURL);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($curl);

		curl_close($curl);

		return $result;
	}

	static function getOrderCurrency()
    {
        global $gSession;

        $resultArray = array();

        $orderCurrency = $gSession['order']['currencycode'];
        $decimalPlaces = $gSession['order']['currencydecimalplaces'];
        $currencyExchanged = false;
        $exchangeRateDateSet = '';
        $exchangeRate = $gSession['order']['currencyexchangerate'];

		$payPalExpressConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayPal.conf', $orderCurrency, $gSession['webbrandcode'], 'PAYPAL');
		
        if (array_key_exists('CURRENCYTRANSFORM', $payPalExpressConfig))
        {
        	$currencyTransform = $payPalExpressConfig['CURRENCYTRANSFORM'];
        }
        else
        {
        	$currencyTransform = '';
        }

        if (($currencyTransform != '') && ($currencyTransform != $orderCurrency))
        {
            $orderCurrency = $currencyTransform;
            $currencyExchanged = true;

            $currencyItem = DatabaseObj::getCurrency($orderCurrency);
			
            if ($currencyItem['result'] == '')
            {
                $exchangeRateDateSet = $currencyItem['exchangeratedateset'];
                $decimalPlaces = $currencyItem['decimalplaces'];

				// don't want to divide by zero
                if ($exchangeRate == 0)
                {	
                    $orderCurrency = '';
                }
                else
                {
					$exchangeRate = $currencyItem['exchangerate'] / $exchangeRate;
                }

                if ($exchangeRate == 0)
                {
                    $orderCurrency = '';
                }
            }
            else
            {
                $orderCurrency = '';
            }
        }

        $resultArray['ordercurrency'] = $orderCurrency;
        $resultArray['currencyexchanged'] = $currencyExchanged;
        $resultArray['exchangeratedateset'] = $exchangeRateDateSet;
        $resultArray['exchangerate'] = $exchangeRate;
        $resultArray['decimalplaces'] = $decimalPlaces;

        return $resultArray;
    }
}

?>