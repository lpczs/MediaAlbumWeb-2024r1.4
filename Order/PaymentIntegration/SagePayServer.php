<?php

if (! class_exists('CurlHandler'))
{
	require_once __DIR__ . '/Request/CurlHandler.php';
}

class SagePayObj
{
    static function configure()
    {
		AuthenticateObj::clearSessionCCICookie();

        $resultArray = array();
		$resultArray['active'] = true;
        $resultArray['form'] = '';
        $resultArray['scripturl'] = '';
        $resultArray['script'] = '';
        $resultArray['action'] = '';

        return $resultArray;
    }

    static function initialize()
    {
        global $gSession;

		$curlResponseCode = 200;
		$curlResponseMessage = '';
		$showError = false;
        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$sagePayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/SagePay.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

			// default to the test url if servermode is configured incorrectly
			if (strtolower($sagePayConfig['SERVERMODE']) == 'live')
			{
				$serverURL = $sagePayConfig['SERVERLIVE'];
			}
			else
			{
				$serverURL = $sagePayConfig['SERVERTEST'];
			}

			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$notificationURL = UtilsObj::correctPath($gSession['webbrandwebroot']) . 'PaymentIntegration/SagePayServer/sagePayServerCallback.php?ref=' . $gSession['ref'];

			$vendorName = $sagePayConfig['VENDORNAME'];
			$orderArray = UtilsObj::getArrayParam($gSession, 'order');
			$shippingArray = UtilsObj::getArrayParam($gSession, 'shipping')[0];
			$timestamp = time();

			if (array_key_exists('TRANSPREFIX', $sagePayConfig))
        	{
				if ($sagePayConfig['TRANSPREFIX'] == '')
				{
					$vendorTxCode = $vendorName . $gSession['ref'] . $timestamp;
				}
				else
				{
					$transactionPrefix = UtilsObj::LeftChars($sagePayConfig['TRANSPREFIX'], 20);

					$vendorTxCode = $transactionPrefix . $gSession['ref'] . $timestamp;
				}
			}
			else
			{
				$vendorTxCode = $vendorName . $gSession['ref'] . $timestamp;
			}

			// billing state is required if countrycode is united states
			$billingState = '';

			if (strtolower($orderArray['billingcustomercountrycode']) == 'us')
			{
				$billingState = $orderArray['billingcustomerregioncode'];
			}

			// devlivery state is required if countrycode is united states
			$deliveryState = '';

			if (strtolower($shippingArray['shippingcustomercountrycode']) == 'us')
			{
				$deliveryState = $shippingArray['shippingcustomerregioncode'];
			}

			// amount must be in gbp format
			$amount = self::formatPrice($gSession['order']['ordertotaltopay']);

			// parameters to pass to sagepay
			$parameters = array(
				'VPSProtocol' => '3.00',
				'TxType' => strtoupper($sagePayConfig['TRANSACTIONTYPE']),
				'VendorTxCode' => $vendorTxCode,
				'Vendor' => $vendorName,
				'VendorData' => $gSession['webbrandcode'],
				'Amount' => $amount,
				'Currency' => $gSession['order']['currencycode'],
				'Description' => $sagePayConfig['SERVER_DESCRIPTION'],
				'NotificationURL' => $notificationURL,
				'BillingSurname' => $orderArray['billingcontactlastname'],
				'BillingFirstnames' => $orderArray['billingcontactfirstname'],
				'BillingAddress1' => $orderArray['billingcustomeraddress1'],
				'BillingAddress2' => $orderArray['billingcustomeraddress2'],
				'BillingCity' => $orderArray['billingcustomercity'],
				'BillingPostCode' => $orderArray['billingcustomerpostcode'],
				'BillingState' => $billingState,
				'BillingCountry' => $orderArray['billingcustomercountrycode'],
				'BillingPhone' => $orderArray['billingcustomertelephonenumber'],
				'DeliverySurname' => $shippingArray['shippingcontactlastname'],
				'DeliveryFirstnames' => $shippingArray['shippingcontactfirstname'],
				'DeliveryAddress1' => $shippingArray['shippingcustomeraddress1'],
				'DeliveryAddress2' => $shippingArray['shippingcustomeraddress2'],
				'DeliveryCity' => $shippingArray['shippingcustomercity'],
				'DeliveryPostCode' => $shippingArray['shippingcustomerpostcode'],
				'DeliveryState' => $deliveryState,
				'DeliveryCountry' => $shippingArray['shippingcustomercountrycode'],
				'DeliveryPhone' => $shippingArray['shippingcustomertelephonenumber'],
				'Profile' => $sagePayConfig['SERVER_PROFILE'],
				'AccountType' => 'E', // use e-commerce merchant account
				'CreateToken' => 1,
				'StoreToken' => 1,
				'CustomerName' => ($orderArray['billingcustomername'] != '') ? $orderArray['billingcustomername'] : $orderArray['billingcontactfirstname'] . ' ' . $orderArray['billingcontactlastname'],
				'CustomerEMail' => $orderArray['billingcustomeremailaddress'],
				'ApplyAVSCV2' => $sagePayConfig['APPLYAVSCV2'],
				'Apply3DSecure' => $sagePayConfig['APPLY3DSECURE'],
				'BasketXML' => self::buildBasketXML(UtilsObj::getArrayParam($gSession, 'items'), $shippingArray, $gSession['browserlanguagecode']),
				'BillingAgreement' => 0,
				'AllowGiftAid' => $sagePayConfig['ALLOWGIFTAID'],
				'Website' => UtilsObj::correctPath($gSession['webbrandweburl']),
				'Language' => $gSession['browserlanguagecode']
			);

			$cURLHandler = self::getCurlHandler();
			$cURLResponse = $cURLHandler->connectionSend($serverURL, '', 'POST', $parameters, 2);

			// check if there was an error with the curl request
			if (substr($cURLResponse, 0, 16) == 'errordescription')
			{
				// curl request was not, parse the response and get the error code
				$cURLResponseArray = array();
				parse_str($cURLResponse, $cURLResponseArray);
				$curlResponseCode = $cURLResponseArray['info']['http_code'];
				$curlResponseMessage = $cURLResponseArray['errordescription'];
			}

			if ($curlResponseCode != 200)
			{
				// curl error
				$showError = true;
				$smarty->assign('data1', SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . 'cURL error ' . $curlResponseCode);
				$smarty->assign('data2', SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $curlResponseMessage);
			}
			else
			{
				$parseResponseResult = self::parseResponse($cURLResponse);

				if (count($parseResponseResult) > 0)
				{
					$status = strtolower($parseResponseResult['Status']);

					if (($status != 'ok') && ($status != 'ok repeated'))
					{
						// display error form
						$showError = true;
						$smarty->assign('data1', SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $parseResponseResult['StatusDetail']);
					}
					else
					{
						$securityKey = UtilsObj::getArrayParam($parseResponseResult, 'SecurityKey');

						if ($securityKey != '')
						{
							$gSession['sagepayserversecuritykey'] = $securityKey;
						}

						$smarty->assign('payment_url', UtilsObj::getArrayParam($parseResponseResult, 'NextURL'));
					}
				}
				else
				{
					// curl error
					$showError = true;
					$smarty->assign('data1', SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . SmartyObj::getParamValue('Order', 'str_OrderSagePayServerErrorProcessingResponseFromGateway'));
				}
			}

			if ($showError)
			{
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
			else
			{
				// assign smarty variables
				$smarty->assign('parameter', $parameters);
				$smarty->assign('method', 'post');
				$smarty->assign('cancel_url', $cancelReturnPath);

				AuthenticateObj::defineSessionCCICookie();
				$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
				$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

				// set the ccidata to remember we have jumped to SagePay
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
		}
		else
		{
			// the user has clicked the back button
            AuthenticateObj::clearSessionCCICookie();

            $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
            $smarty->assign('payment_url', $cancelReturnPath);

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
        $resultArray['ref'] = UtilsObj::getGETParam('ref');
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['authorisedstatus'] = 0;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

	static function automaticCallback()
	{
		global $gSession;

		$resultArray = array();
		$authorised = false;
		$authorisedStatus = 0;
		$paymentReceived = 0;
		$cciOrderID = 0;
		$cciParentLog = 0;
		$cciUpdate = false;

		$sagePayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/SagePay.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

		// read parameters from post
		$txType = UtilsObj::getPOSTParam('TxType');
		$bankAuthCode = UtilsObj::getPOSTParam('BankAuthCode');
		$cardType = UtilsObj::getPOSTParam('CardType');
		$status = UtilsObj::getPOSTParam('Status');
		$statusDetail = UtilsObj::getPOSTParam('StatusDetail');
		$txAuthNo = UtilsObj::getPOSTParam('TxAuthNo');
		$TxId = UtilsObj::getPOSTParam('VPSTxId');
		$token = UtilsObj::getPOSTParam('token');
		$avscv2 = UtilsObj::getPOSTParam('AVSCV2');
		$cv2Result = UtilsObj::getPOSTParam('CV2Result');
		$threeDSecureStatus = UtilsObj::getPOSTParam('3DSecureStatus');
		$cardNumber = UtilsObj::getPOSTParam('Last4Digits');
		$addressResult = UtilsObj::getPOSTParam('AddressResult');
		$addressStatus = UtilsObj::getPOSTParam('AddressStatus');
		$postCodeResult = UtilsObj::getPOSTParam('PostCodeResult');
		$giftAid = UtilsObj::getPOSTParam('GiftAid');
		$surcharge = UtilsObj::getPOSTParam('Surcharge', 0.00);
		$declineCode = UtilsObj::getPOSTParam('DeclineCode');
		$venderTransactionCode = UtilsObj::getPOSTParam('VendorTxCode');
		$cavv = UtilsObj::getPOSTParam('CAVV');

		$serverTimeStamp = DatabaseObj::getServerTime();

		switch (strtolower($status))
		{
			case 'ok':
			case 'ok repeated':
			case 'authenticated':
			case 'registered':
			{
				$authorised = true;
				$authorisedStatus = 1;
				$paymentReceived = 1;

				break;
			}
			case 'pending': // european payment types only
			{
				$authorised = true;
				$authorisedStatus = 1;
				$paymentReceived = 0;

				break;
			}
			// other possible status codes:
			//		notauthed
			//		abort
			//		rejected
			//		error
		}

		$cciEntry = PaymentIntegrationObj::getCciLogEntry($gSession['ref']);

		if (count($cciEntry) > 0)
		{
			// an entry exists, this may be because the status pending was returned previously

			$cciOrderID = $cciEntry['orderid'];
			$cciParentLog = $cciEntry['id'];
			$cciUpdate = true;
		}

        // write log
        PaymentIntegrationObj::logPaymentGatewayData($sagePayConfig, $serverTimeStamp);

		$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
		$resultArray['result'] = '';
        $resultArray['ref'] = $gSession['ref'];
        $resultArray['amount'] = $gSession['order']['ordertotaltopay'];
        $resultArray['formattedamount'] = self::formatPrice($gSession['order']['ordertotaltopay']);
        $resultArray['charges'] = $surcharge;
        $resultArray['formattedcharges'] = self::formatPrice($surcharge);
        $resultArray['transactionid'] = $TxId;
        $resultArray['responsecode'] = $status;
        $resultArray['responsedescription'] = $statusDetail;
        $resultArray['authorisationid'] = $txAuthNo;
        $resultArray['bankresponsecode'] = trim($bankAuthCode . ' ' . $declineCode);
        $resultArray['cardnumber'] = $cardNumber;
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = $cv2Result;
        $resultArray['paymentcertificate'] = $token;
        $resultArray['paymentdate'] = $serverTimeStamp;
        $resultArray['paymentmeans'] = $cardType;
        $resultArray['paymenttime'] = '';
		$resultArray['paymentreceived'] = $paymentReceived;
        $resultArray['formattedpaymentdate'] = $serverTimeStamp;
        $resultArray['formattedtransactionid'] = $venderTransactionCode;
        $resultArray['formattedauthorisationid'] = $txAuthNo;
        $resultArray['addressstatus'] = trim($addressResult . ' ' . $addressStatus);
        $resultArray['postcodestatus'] = $postCodeResult;
        $resultArray['payerid'] = '';
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = '';
        $resultArray['business'] = $sagePayConfig['VENDORNAME'];
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = $txType;
        $resultArray['settleamount'] = '';
        $resultArray['currencycode'] = $gSession['order']['currencycode'];
		$resultArray['webbrandcode'] = $gSession['webbrandcode'];
        $resultArray['charityflag'] = $giftAid;
        $resultArray['threedsecurestatus'] = $threeDSecureStatus;
        $resultArray['cavvresponsecode'] = $avscv2 . ' ' . $cavv;
		$resultArray['paymentreceived'] = $paymentReceived;
		$resultArray['update'] = $cciUpdate;
		$resultArray['orderid'] = $cciOrderID;
        $resultArray['parentlogid'] = $cciParentLog;
        $resultArray['resultisarray'] = false;
        $resultArray['showerror'] = false;
        $resultArray['resultlist'] = array();

		return $resultArray;
	}

	static function manualCallback()
	{
		global $gSession;

		$resultArray = array();
		$resultArray['authorised'] = false;
		$resultArray['authorisedstatus'] = 0;
		$resultArray['showerror'] = false;
		$resultArray['transactionid'] = 0;
		$resultArray['update'] = false;
		$resultArray['ref'] = $gSession['ref'];
		$resultArray['orderid'] = 0;
		$resultArray['data1'] = '';
		$resultArray['data2'] = '';
		$resultArray['data3'] = '';
		$resultArray['data4'] = '';

		$status = UtilsObj::getGETParam('status', '__NOSTATUS__');

		if ($status != 'OK')
		{
			// display error

			$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

			$statusDetail = urldecode(UtilsObj::getGETParam('statusdetail', '__NOSTATUSDETAIL__'));

			$resultArray['showerror'] = true;
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $statusDetail;
			$resultArray['errorform'] = 'error.tpl';
		}
		else
		{
			// payment was ok

			// a cci log entry should have been created by the automatic callback at this point, if one doesn't exist then an error occured
			$cciEntry = PaymentIntegrationObj::getCciLogEntry($gSession['ref']);

			if (count($cciEntry) > 0)
			{
				$resultArray['update'] = true;
				$resultArray['authorised'] = $cciEntry['authorised'];
				$resultArray['parentlogid'] = $cciEntry['id'];
				$resultArray['showerror'] = false;
				$resultArray['resultlist'] = array();
			}
			else
			{
				// display error

				$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

				$resultArray['showerror'] = true;
				$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . SmartyObj::getParamValue('Order', 'str_OrderSagePayServerUnableToFindCCIRecord');
				$resultArray['errorform'] = 'error.tpl';
			}
		}

		// write log
		$sagePayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/SagePay.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
		$serverTimeStamp = DatabaseObj::getServerTime();

        PaymentIntegrationObj::logPaymentGatewayData($sagePayConfig, $serverTimeStamp);

        return $resultArray;
	}

	static function parseResponse($pData, $pDelimiter = "\r\n")
	{
		$pairs = explode($pDelimiter, $pData);
		$queryArray = array();

		// explode pairs by "="
		foreach ($pairs as $pair)
		{
			$keyValue = explode('=', $pair);

			// use first value as key
			$key = array_shift($keyValue);

			// implode others as value for $key
			$queryArray[$key] = implode('=', $keyValue);
		}

		return $queryArray;
	}

	static function buildBasketXML($pOrderItems, $pShippingArray, $pBrowserLanguage)
	{
		$basketXML = '<basket>';

		foreach ($pOrderItems as $orderItem)
		{
			$basketXML .= '<item>';
			$basketXML .= '<description>' . substr(LocalizationObj::getLocaleString($orderItem['itemproductcollectionname'], $pBrowserLanguage, true), 0, 100) . '</description>';

			// optional
			if ($orderItem['itemproductskucode'] != '')
			{
				$basketXML .= '<productSku>' . substr($orderItem['itemproductskucode'], 0, 12) . '</productSku>';
			}

			// optional
			if ($orderItem['itemproductcode'] != '')
			{
				$basketXML .= '<productCode>' . substr(str_replace('_', '-', $orderItem['itemproductcode']), 0, 12) . '</productCode>';
			}

			$basketXML .= '<quantity>' . $orderItem['itemqty'] . '</quantity>';
			$basketXML .= '<unitNetAmount>' . self::formatPrice($orderItem['itemproducttotalsellnotax']) . '</unitNetAmount>';
			$basketXML .= '<unitTaxAmount>' .  self::formatPrice($orderItem['itemproducttotaltax']) . '</unitTaxAmount>';
			$basketXML .= '<unitGrossAmount>' .  self::formatPrice($orderItem['itemproducttotalsellwithtax']) . '</unitGrossAmount>';
			$basketXML .= '<totalGrossAmount>' .  self::formatPrice($orderItem['itemproducttotalsell']) . '</totalGrossAmount>';
			$basketXML .= '</item>';
		}

		$basketXML .= '<deliveryNetAmount>' . $pShippingArray['shippingratetotalsellnotax'] . '</deliveryNetAmount>';
		$basketXML .= '<deliveryTaxAmount>' . $pShippingArray['shippingratetaxtotal'] . '</deliveryTaxAmount>';
		$basketXML .= '<deliveryGrossAmount>' . $pShippingArray['shippingratetotalsellwithtax'] . '</deliveryGrossAmount>';
		$basketXML .= '</basket>';

		return $basketXML;
	}

	static function formatPrice($pAmount)
	{
		// sagepay requires currency to be formated to gbp (2 decimal points)
		return number_format($pAmount, 2, '.', ',');
	}

	static function generateHash($pData, $pVendorName, $pSecurityKey)
	{
		$status = UtilsObj::getArrayParam($pData, 'Status');

		// some parameters may not always be passed back from the gateway so they need to be calculated as an empty string
		$stringToHash = UtilsObj::getArrayParam($pData, 'VPSTxId');
		$stringToHash .= UtilsObj::getArrayParam($pData, 'VendorTxCode');
		$stringToHash .= $status;
		$stringToHash .= UtilsObj::getArrayParam($pData, 'TxAuthNo');
		$stringToHash .= $pVendorName;
		$stringToHash .= UtilsObj::getArrayParam($pData, 'AVSCV2');
		$stringToHash .= $pSecurityKey;
		$stringToHash .= UtilsObj::getArrayParam($pData, 'AddressResult');
		$stringToHash .= UtilsObj::getArrayParam($pData, 'PostCodeResult');
		$stringToHash .= UtilsObj::getArrayParam($pData, 'CV2Result');
		$stringToHash .= UtilsObj::getArrayParam($pData, 'GiftAid');
		$stringToHash .= UtilsObj::getArrayParam($pData, '3DSecureStatus');
		$stringToHash .= UtilsObj::getArrayParam($pData, 'CAVV');
		$stringToHash .= UtilsObj::getArrayParam($pData, 'AddressStatus');
		$stringToHash .= UtilsObj::getArrayParam($pData, 'PayerStatus');
		$stringToHash .= UtilsObj::getArrayParam($pData, 'CardType');
		$stringToHash .= UtilsObj::getArrayParam($pData, 'Last4Digits');
		$stringToHash .= UtilsObj::getArrayParam($pData, 'DeclineCode');
		$stringToHash .= UtilsObj::getArrayParam($pData, 'ExpiryDate');
		$stringToHash .= UtilsObj::getArrayParam($pData, 'FraudResponse');
		$stringToHash .= UtilsObj::getArrayParam($pData, 'BankAuthCode');

		return strtoupper(hash('md5', $stringToHash));
	}

	static function getCurlHandler()
	{
		$curlOptions = array(
			CURLOPT_HEADER => 0,
			CURLOPT_POST => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_SSL_VERIFYPEER => 1,
			CURLOPT_CAINFO => UtilsObj::getCurlPEMFilePath()
		);

		return new CurlHandler('', $curlOptions);
	}
}
?>