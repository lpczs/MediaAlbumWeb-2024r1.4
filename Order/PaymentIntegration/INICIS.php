<?php

require_once 'TaopixAbstractGateway.php';

if (! class_exists('CurlHandler'))
{
	require_once __DIR__ . '/Request/CurlHandler.php';
}

/**
 * INICIS payment gateway integration
 *
 * @author anthony dodds <anthony.dodds@taopix.com>
 * @author Christopher Steel <christopher.steel@taopix.com>
 */
class INICIS extends TaopixAbstractGateway
{
	/**
	 * handler for curl calls so we can mock this when unit testing
	 * @var object Request/CurlHandler
	 */
	protected $curlConnection = null;

	protected $mobileNotificationIPAddresses = [];

	/**
	 * Method called to configure the payment gateway
	 *
	 * @return array Array used to configure view, consists of the following definition
	 * [
	 * active => boolean,
	 * form => string,
	 * scripturl => string,
	 * script => string,
	 * action => string,
	 * gateways => string|array
	 * ]
	 */
	public function configure()
	{
		$resultArray = [
			'active' => true,
			'form' => '',
			'scripturl' => '',
			'script' => '',
			'action' => '',
			'gateways' => [],
			'requestpaymentparamsremotely' => false
		];

		AuthenticateObj::clearSessionCCICookie();

		/**
		 * ISO number from the order.
		 * 'currencycode' can be substituted with 'currencyisonumber' if the gateway uses the ISO number in the list of currencies
		 */
		$currency = $this->session['order']['currencycode'];

		/**
		 * Set the gateway as active if the order currency is in the list of accepted currencies
		 */
		if (strpos($this->config['CURRENCIES'], $currency) === false)
		{
			$currencyAccepted = false;
		}
		else
		{
			/*
			 * Additional check for mobile, Mobile does not allow currency to be changed
			 * All transactions on mobile appear to be in WON so set the currencyAccepted to be 
			 * false if we are on mobile and the order currency is USD.
			 */
			if (($this->session['ismobile'] === true) && ($currency === 'USD'))
			{
				$currencyAccepted = false;
			}
			else
			{
				$currencyAccepted = true;
			}
		}

		if ($currencyAccepted)
		{
			$smarty = SmartyObj::newSmarty('CreditCardPayment');

			if ($this->session['ismobile'] === false)
			{
				// This is a large screen order, this should use the INICIS overlay payment method.
				$resultArray['requestpaymentparamsremotely'] = true;
				$resultArray['script'] = $smarty->fetchLocale('order/PaymentIntegration/INICIS/INICIS_large.tpl');
				$resultArray['scripturl'] = 'https://' . ($this->config['TRANSACTIONMODE'] === 'TEST' ? $this->config['TESTSERVER'] : $this->config['LIVESERVER']) . '/stdjs/INIStdPay.js';
			}
			else
			{
				$this->mobileNotificationIPAddresses = explode(',', $this->config['MOBILENOTIFICATIONIPS']);
				
				// Small screen has a number of available payment options.
				$paymentList = explode(',', $this->config['MOBILEPAYMENTOPTIONS']);

				foreach ($paymentList as $gateway)
				{
					$stringName = 'str_OrderINICIS_' . strtoupper($gateway);
					$resultArray['gateways'][$gateway] = $smarty->get_config_vars($stringName);
				}
			}

			// If CSP is active add the directives specified for the gateway.
			if ($this->cspBuilder !== null)
			{
				$this->addCSPDetails();
			}
		}

		/**
		 * set the payment gateway as active based on previous tests
		 */
        $resultArray['active'] = $currencyAccepted;

		return $resultArray;
	}

	/**
	 * Method used to render the correct smarty template
	 *
	 * @return array|string Returns an array of smarty items or a smarty item
	 */
	public function initialize()
	{
		// Configure the urls used in the payment integration.
		$baseUrl = UtilsObj::correctPath($this->session['webbrandweburl']);
		$manualUrl = $baseUrl . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'];
		$automaticUrl = $baseUrl . '?fsaction=Order.ccAutomaticCallback&ref=' . $this->session['ref'];
		$mobileUrl = $baseUrl . '/PaymentIntegration/INICIS/mobile-next.php?ref=' . $this->session['ref'];

		// Configure the price and order text.
		$price = number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '', '');
		$orderText = $this->session['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($this->session['items'][0]['itemproductname'], $this->session['browserlanguagecode'], true);
		$buyerName = $this->session['order']['billingcontactlastname'] . ' ' . $this->session['order']['billingcontactfirstname'];

		// Set fields depending on if we are on mobile or large screen.
		if ($this->session['ismobile'] === true)
		{
			$smarty = SmartyObj::newSmarty('CreditCardPayment');

			// Configure the appBase value if the paymengatewaycode is either bank or wcard.
			$appBase = (($this->post['paymentgatewaycode'] == 'bank') || ($this->post['paymentgatewaycode'] == 'wcard')) ? 'On' : '';

			// Set the parameters
			$params = [
				'P_OID' => $this->session['ref'],
				'P_GOODS' => $orderText,
				'P_AMT' => $price,
				'P_UNAME' => $buyerName,
				'P_MNAME' => $this->session['webbrandapplicationname'],
				'P_MOBILE' => $this->session['order']['billingcustomertelephonenumber'],
				'P_EMAIL' => $this->session['order']['billingcustomeremailaddress'],
				'P_MID' => $this->config['MERCHANTID'],
				'P_RETURN_URL' => $mobileUrl,
				'P_NOTI_URL' => $automaticUrl,
				'P_HPP_METHOD' => '1',
				'P_APP_BASE' => $appBase,
				'P_RESERVED' => ($price < 1000 ? 'below1000=y' : ''),
			];

			// Assign the params to the parameter value.
			$smarty->assign('parameter', $params);
			$smarty->assign('method', 'POST');
			$smarty->assign('payment_url', $this->config['MOBILEPAYMENTURL'] . $this->post['paymentgatewaycode']);

			// Set the return values for template and javascript.
			$resultArray = [
				'template' => $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest_small.tpl'),
				'javascript' => $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest.tpl')
			];

			return $resultArray;
		}
		else
		{
			// Format the large screen options.
			$timestamp = $this->getTimestamp();
			$signOrderId = $this->config['MERCHANTID'] . '_' . $timestamp;
			$price = number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '', '');

			// Generate the signing parameters.
			$signParams = [
				'oid' => $signOrderId,
				'price' => $price,
				'timestamp' => $timestamp
			];

			// As the large screen method uses an overlay the cancel needs to close the overlay.
			$cancelUrl = $baseUrl . '/PaymentIntegration/INICIS/cancel.php';

			// Generate the signature and mkey values.
			$signature = $this->hashString($this->generateHashString($signParams), 'sha256');
			$mKey = $this->hashString($this->config['SIGNINGKEY'], 'sha256');

			// Set the currency values correctly.
			$currencyValue = ($this->session['order']['currencycode'] == 'KRW') ? 'WON' : 'USD';

			// Generate the return array.
			$returnArray = [
				'version' => '1.0',
				'mid' => $this->config['MERCHANTID'],
				'merchantData' => $this->session['ref'],
				'oid' => $signParams['oid'],
				'goodname' => $orderText,
				'price' => $price,
				'currency' => $currencyValue,
				'buyername' => $buyerName,
				'buyertel' => $this->session['order']['billingcustomertelephonenumber'],
				'buyeremail' => $this->session['order']['billingcustomeremailaddress'],
				'timestamp' => $timestamp,
				'returnUrl' => $manualUrl,
				'closeUrl' => $cancelUrl,
				'signature' => $signature,
				'mKey' => $mKey,
				'result' => 1,
				'gopaymethod' => '',
				'acceptmethod' => 'HPP(1):no_receipt:va_receipt:vbanknoreg(0)' . ($price < 1000 ? ':below1000' : ''),
			];

			// Return the json encode return array.
			return json_encode($returnArray);
		}
	}

	/**
	 * Method called when a successful payment notification is provided from the
	 * gateway to the Taopix cart
	 *
	 * @param string $pCallbackType Call back type this is normally automatic or manual
	 * @return array Array with structure that at minimum matches the defined structure from TaopixAbstractGateway see structure returned from ../TaopixAbstractGateway::cciEmptyResultArray
	 */
	public function confirm($pCallbackType)
	{
		$resultArray = [];

		if ($this->session['ismobile'] === true)
		{
			// Process the mobile response.
			$resultArray = $this->confirmSmallScreen($pCallbackType);
		}
		else
		{
			// Process the large screen response.
			$resultArray = $this->confirmLargeScreen();
		}
		
		// Get the server timestamp.
		$serverTimestamp = DatabaseObj::getServerTime();

		// Log the confirm result.
		PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimestamp, '', $resultArray);

		return $resultArray;
	}

	/**
	 * Confirm method used on large screen, this validates the response via a curl request.
	 *
	 * @return array
	 */
	private function confirmLargeScreen()
	{
		$validatedData = null;
		$resultArray = $this->cciEmptyResultArray();
		$resultArray['showerror'] = false;

		// Get the ref from values that are passed or the session.
		$cciRef = array_key_exists('ref', $this->get) ? $this->get['ref'] : $this->session['ref'];

		// Check for a cciEntry for this ref.
		$cciEntry = PaymentIntegrationObj::getCciLogEntry($cciRef);

		if ($cciEntry === [])
		{
			// empty first callback
			$resultArray['webbrandcode'] = $this->session['webbrandcode'];
			$resultArray['currencycode'] = $this->session['order']['currencycode'];
			$resultArray['amount'] = $this->session['order']['ordertotaltopay'];
			$resultArray['parentlogid'] = -1;
			$resultArray['orderid'] = -1;
			$resultArray['update'] = false;
			$this->updateStatus = false;
		}
		else
		{
			// additional callback
			$resultArray['webbrandcode'] = $cciEntry['webbrandcode'];
			$resultArray['currencycode'] = $cciEntry['currencycode'];
			$resultArray['amount'] = $cciEntry['formattedamount'];
			$resultArray['parentlogid'] = $cciEntry['id'];
			$resultArray['orderid'] = $cciEntry['orderid'];
			$resultArray['update'] = true;
			$this->updateStatus = false;
		}

		// A resultCode of 0000 is a success so we need to validate the response, everything else is a failed payment.
		if ($this->post['resultCode'] === "0000")
		{
			$timestamp = $this->getTimestamp();

			// Generate the signature.
			$signature = $this->makeSignature([
				'authToken' => $this->post['authToken'],
				'timestamp' => $timestamp,
			]);

			// Set the authValues used to validate the response.
			$authMap = [
				'mid' => $this->post['mid'],
				'authToken' => $this->post['authToken'],
				'signature' => $signature,
				'timestamp' => $timestamp,
				'charset' => 'UTF-8',
				'format' => 'JSON',
			];

			$authUrl = $this->post['authUrl'];
			$cancelUrl = $this->post['netCancelUrl'];

			$this->curlConnection = new CurlHandler('query', $this->defaultCurlOptions());

			$serverTimestamp = DatabaseObj::getServerTime();

			$authUrlDetails = parse_url($authUrl);
			$server = $authUrlDetails['scheme'] . '://' . $authUrlDetails['host'];

			// Pass the auth details to the INICIS API to get the actual payment status.
			$rawResponse = $this->curlConnection->connectionSend($server, $authUrlDetails['path'], 'POST', $authMap, 3);

			// Only log the curl response if we are verbose logging
			if ($this->config['VERBOSELOGGING'] === 1)
			{
				// Log the return from the API request.
				PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimestamp, '', $rawResponse);
			}

			$response = json_decode($rawResponse, true);

			// If the resultCode in the response is 0000 then this is a successful payment.
			if ($response['resultCode'] === '0000')
			{
				// Make the signature to validate the response from the api call.
				$secureSignature = $this->makeSignatureAuth([
					'mid' => $authMap['mid'],
					'tstamp' => $timestamp,
					'MOID' => $response['MOID'],
					'TotPrice' => $response['TotPrice']
				]);

				// Check that the authorisation signature matches,
				if ($response['authSignature'] === $secureSignature)
				{
					$validatedData['transactionid'] = $response['tid'];
					$validatedData['responsecode'] = $response['resultCode'];

					// Set the payment means to be a bankCode cardCode.
					// Values for what these relate to can be found in INICIS documentation.
					$validatedData['paymentmeans'] = $response['CARD_BankCode'] . ' ' . $response['CARD_Code'];

					if (isset($response['CARD_Num']))
					{
						$validatedData['cardnumber'] = $response['CARD_Num'];
					}

					$resultArray['authorised'] = 1;
					$resultArray['authorisedstatus'] = true;
					$resultArray['showerror'] = false;
				}
				else
				{
					// Signature mismatch
					$resultArray['showerror'] = true;
					$resultArray['authorised'] = 0;
					$resultArray['authorisedstatus'] = false;
					// error messages for hash fail
					$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $response['resultCode'];
					// the language string str_orderadyensignaturefailed gives the correct error message of signature check failed
					$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . SmartyObj::getParamValue('CreditCardPayment', 'str_OrderAdyenSignatureFailed');
					$resultArray['data3'] = '';
					$resultArray['data4'] = '';
					$resultArray['errorform'] = 'error.tpl';

					// Cancel url details
					$cancelUrlDetails = parse_url($cancelUrl);
					$cancelServer = $cancelUrlDetails['scheme'] . '://' . $cancelUrlDetails['host'];

					// Send the cancel operation for the transaction.
					$this->curlConnection->connectionSend($cancelServer, $cancelUrlDetails['path'], 'POST', $authMap, 3);
				}
			}
			else
			{
				// Payment was not successful.
				$resultArray['showerror'] = true;
				$resultArray['authorised'] = 0;
				$resultArray['authorisedstatus'] = false;
				// error messages for hash fail
				$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $response['resultCode'];
				// the language string str_orderadyensignaturefailed gives the correct error message of signature check failed
				$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $response['resultMsg'];
				$resultArray['data3'] = '';
				$resultArray['data4'] = '';
				$resultArray['errorform'] = 'error.tpl';

				// Cancel url details.
				$cancelUrlDetails = parse_url($cancelUrl);
				$cancelServer = $cancelUrlDetails['scheme'] . '://' . $cancelUrlDetails['host'];

				// Send the cancel operation for the transaction.
				$this->curlConnection->connectionSend($cancelServer, $cancelUrlDetails['path'], 'POST', $authMap, 3);
			}
		}
		else
		{
			// Payment failed for some reason.
			$resultArray['showerror'] = true;
			$resultArray['authorised'] = 0;
			$resultArray['authorisedstatus'] = false;
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $response['resultCode'];
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $response['resultMsg'];
			$resultArray['data3'] = '';
			$resultArray['data4'] = '';
			$resultArray['errorform'] = 'error.tpl';
		}

		$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime = date('H:i:s');

		$resultArray['ref'] = $cciRef;
		$resultArray['formattedamount'] = $resultArray['amount'];
		$resultArray['transactionid'] = (($validatedData !== null) ? $validatedData['transactionid'] : '');
		$resultArray['formattedtransactionid'] = $resultArray['transactionid']; //set to auth trans number
		$resultArray['responsecode'] = (($validatedData !== null) ? $validatedData['responsecode'] : '');
		$resultArray['authorisationid'] = $resultArray['transactionid'];  // this is our unique ID, not the real order ID
		$resultArray['formattedauthorisationid'] = $resultArray['transactionid'];
		$resultArray['bankresponsecode'] = (($validatedData !== null) ? $validatedData['responsecode'] : '');
		$resultArray['cardnumber'] = (($validatedData !== null) ? $validatedData['cardnumber'] : '');
		$resultArray['paymentmeans'] = (($validatedData !== null) ? $validatedData['paymentmeans'] : '');
		$resultArray['formattedcardnumber'] = $resultArray['cardnumber']; //set to card number
		$resultArray['paymentdate'] = $serverDate;
		$resultArray['paymenttime'] = $serverTime;
		$resultArray['paymentreceived'] = ($resultArray['authorisedstatus'] == 1) ? 1 : 0;
		$resultArray['formattedpaymentdate'] = $serverTimestamp;
		$resultArray['resultisarray'] = false;
		$resultArray['resultlist'] = [];

		$this->curlConnection->connectionClose();

		return $resultArray;
	}

	/**
	 * Small screen confirm process. Gateway will only send result back from 1 of 3 IP addresses.
	 * INICIS do not provide any checking mechanism for mobile payment notifications.
	 *
	 * @param string $pCallbackType Callback type we are dealing with
	 * @return array
	 */
	private function confirmSmallScreen($pCallbackType)
	{
		$resultArray = $this->cciEmptyResultArray();
		$cciRef = array_key_exists('ref', $this->get) ? $this->get['ref'] : $this->session['ref'];

		// Check for a cciEntry for this ref.
		$cciEntry = PaymentIntegrationObj::getCciLogEntry($cciRef);

		// Process the response based on the callback type.
		if ($pCallbackType == 'automatic')
		{
			if (in_array($_SERVER['REMOTE_ADDR'], $this->mobileNotificationIPAddresses))
			{
				$resultArray['showerror'] = false;
				$resultArray['ref'] = $cciRef;

				$serverTimestamp = DatabaseObj::getServerTime();
				$serverDate = date('Y-m-d');
				$serverTime = date('H:i:s');

				if ($cciEntry === [])
				{
					// empty first callback
					$resultArray['webbrandcode'] = $this->session['webbrandcode'];
					$resultArray['currencycode'] = $this->session['order']['currencycode'];
					$resultArray['amount'] = $this->session['order']['ordertotaltopay'];
					$resultArray['parentlogid'] = -1;
					$resultArray['orderid'] = -1;
					$resultArray['update'] = false;
					$resultArray['resultisarray'] = false;
					$this->updateStatus = false;
				}
				else
				{
					// additional callback
					$resultArray['webbrandcode'] = $cciEntry['webbrandcode'];
					$resultArray['currencycode'] = $cciEntry['currencycode'];
					$resultArray['amount'] = $cciEntry['formattedamount'];
					$resultArray['parentlogid'] = $cciEntry['id'];
					$resultArray['orderid'] = $cciEntry['orderid'];
					$resultArray['update'] = true;
					$resultArray['resultisarray'] = true;
					$this->updateStatus = false;
				}

				if ($this->post['P_STATUS'] == '00')
				{
					// OK
					$resultArray['authorised'] = 1;
					$resultArray['authorisedstatus'] = true;
					$resultArray['responsecode'] = $this->post['P_STATUS'];
					$resultArray['transactionid'] = $this->post['P_TID'];
					$resultArray['formattedtransactionid'] = $this->post['P_TID'];
					$resultArray['authorisationid'] = $this->post['P_TID'];  // this is our unique ID, not the real order ID
					$resultArray['formattedauthorisationid'] = $this->post['P_TID'];
					$resultArray['bankresponsecode'] = $this->post['P_FN_CD1'] . ' ' . $this->post['P_FN_CD2'];
					$resultArray['cardnumber'] = $this->post['P_CARD_NUM'];
					$resultArray['formattedcardnumber'] = $this->post['P_CARD_NUM']; //set to card number
					$resultArray['paymentmeans'] = $this->post['P_TYPE']; // Set the payment means.
					$resultArray['paymentreceived'] = 1;
					$resultArray['paymentdate'] = $serverDate;
					$resultArray['paymenttime'] = $serverTime;
					$resultArray['formattedpaymentdate'] = $serverTimestamp;
				}
				else
				{
					$resultArray['showerror'] = false;
					$resultArray['authorised'] = 0;
					$resultArray['authorisedstatus'] = false;
				}

				$resultArray['acknowledgement'] = ($resultArray['authorised'] == 1) ? 'OK' : 'FAILED';
			}
		}
		else
		{
			// Process a manual callback,
			if ($cciEntry !== [])
			{
				$resultArray['webbrandcode'] = $cciEntry['webbrandcode'];
				$resultArray['currencycode'] = $cciEntry['currencycode'];
				$resultArray['amount'] = $cciEntry['formattedamount'];
				$resultArray['parentlogid'] = $cciEntry['id'];
				$resultArray['orderid'] = $cciEntry['orderid'];
				$resultArray['update'] = true;
				$this->updateStatus = false;

				$resultArray['ref'] = $cciRef;
				$resultArray['formattedamount'] = $cciEntry['formattedamount'];
				$resultArray['transactionid'] = $cciEntry['transactionid'];
				$resultArray['formattedtransactionid'] = $cciEntry['formattedtransactionid'];
				$resultArray['responsecode'] = $cciEntry['responsecode'];
				$resultArray['authorisationid'] = $cciEntry['authorisationid'];  // this is our unique ID, not the real order ID
				$resultArray['formattedauthorisationid'] = $cciEntry['formattedauthorisationid'];
				$resultArray['bankresponsecode'] = $cciEntry['bankresponsecode'];
				$resultArray['cardnumber'] = $cciEntry['cardnumber'];
				$resultArray['formattedcardnumber'] = $cciEntry['formattedcardnumber']; //set to card number
				$resultArray['paymentdate'] = $cciEntry['paymentdate'];
				$resultArray['paymenttime'] = $cciEntry['paymenttime'];
				$resultArray['paymentreceived'] = ($cciEntry['authorised'] == 1) ? 1 : 0;
				$resultArray['formattedpaymentdate'] = $cciEntry['formattedpaymentdate'];
				$resultArray['resultisarray'] = false;
				$resultArray['resultlist'] = [];
			}
		}

		return $resultArray;
	}

	/**
	 * UNUSED
	 */
	public function verifyHash($pSuppliedHash, $pParams, $pType)
	{
		return null;
	}

	/**
	 * UNUSED
	 */
	public function generateHash($pString)
	{
		return null;
	}

	/**
	 * Generates a sha256 string signature.
	 *
	 * @param array $pSignParams
	 * @return string
	 */
	private function makeSignature($pSignParams) {
		ksort($pSignParams);
		$values = [];

		foreach($pSignParams as $key=> $value)
		{
			$values[] = $key . '=' . $value;
		}

		return $this->hashString(implode("&", $values), "sha256");
	}

	/**
	 * Generate the hash string.
	 *
	 * @param string $pParams String of the parameters to hash.
	 * @param string $pType Type of hash we are generating.
	 * @return string
	 */
	public function hashString($pParams, $pType)
	{
		return openssl_digest($pParams, $pType);
	}

	/**
	 * Converts an associative array to a string.
	 *
	 * @param array $pArray Associative array of keyname => values to convert to a string.
	 * @return string
	 */
	private function generateHashString($pArray)
	{
		$returnArray = [];
		ksort($pArray);
		
		foreach($pArray as $key => $value)
		{
			$returnArray[] = $key . '=' . $value;
		}
		return implode('&', $returnArray);
	}

	/**
	 * Returns the timestamp in the format the gateway wants.
	 *
	 * @return string.
	 */
	private function getTimestamp()
	{
		$milliseconds = round(microtime(true) * 1000);
		$tempValue1 = round($milliseconds/1000);
		$tempValue2 = round((float)microtime(false) * 1000);

		return $tempValue1 . str_pad($tempValue2, 3, '0', STR_PAD_LEFT);
	}

	/**
	 * Configures the default curl options.
	 *
	 * @return array Array containing the curl config settings.
	 */
	private function defaultCurlOptions()
	{
		$return = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_TIMEOUT => 30,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_HTTPHEADER => [
				'content-type: application/x-www-form-urlencoded'
			]
		];

		if($this->config['TRANSACTIONMODE'] === 'TEST')
		{
			// disable ssl verification for test transactions
			$return += [CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0];
		}
		else
		{
			$return += [CURLOPT_CAINFO => UtilsObj::getCurlPEMFilePath()];
		}

		if((isset($this->config['LOGGING'])) && ($this->config['LOGGING'] === 'true'))
		{
			$logHandle = fopen(UtilsObj::getTaopixWebInstallPath('/logs') . '/inicis-curl.log', 'a+');
			$return += [CURLOPT_VERBOSE => true, CURLOPT_STDERR => $logHandle];
		}

		return $return;
	}

	/**
	 * Returns an inicis auth signature key, this is generated based on the first character
	 * of the request tstamp param.
	 *
	 * @param array $parameters
	 * @return string
	 */
	private function makeSignatureAuth($parameters)
	{
		$stringToSign = "";
		$mid = $parameters["mid"];
		$tstamp = $parameters["tstamp"];
		$MOID = $parameters["MOID"];
		$TotPrice = $parameters["TotPrice"];

		// Get the last character of the passed timestamp
		$tstampKey = substr($tstamp, -1);

		// Use the last value of the timestamp to calculate the signature we are validating against.
		switch (intval($tstampKey))
		{
			case 1:
			{
				$stringToSign = "MOID=" . $MOID . "&mid=" . $mid . "&tstamp=" . $tstamp;
				break;
			}
			case 2:
			{
				$stringToSign = "MOID=" . $MOID . "&tstamp=" . $tstamp . "&mid=" . $mid;
				break;
			}
			case 3:
			{
				$stringToSign = "mid=" . $mid . "&MOID=" . $MOID . "&tstamp=" . $tstamp;
				break;
			}
			case 4:
			{
				$stringToSign = "mid=" . $mid . "&tstamp=" . $tstamp . "&MOID=" . $MOID;
				break;
			}
			case 5:
			{
				$stringToSign = "tstamp=" . $tstamp . "&mid=" . $mid . "&MOID=" . $MOID;
				break;
			}
			case 6:
			{
				$stringToSign = "tstamp=" . $tstamp . "&MOID=" . $MOID . "&mid=" . $mid;
				break;
			}
			case 7:
			{
				$stringToSign = "TotPrice=" . $TotPrice . "&mid=" . $mid . "&tstamp=" . $tstamp;
				break;
			}
			case 8:
			{
				$stringToSign = "TotPrice=" . $TotPrice . "&tstamp=" . $tstamp . "&mid=" . $mid;
				break;
			}
			case 9:
			{
				$stringToSign = "TotPrice=" . $TotPrice . "&MOID=" . $MOID . "&tstamp=" . $tstamp;
				break;
			}
			case 0:
			{
				$stringToSign = "TotPrice=" . $TotPrice . "&tstamp=" . $tstamp . "&MOID=" . $MOID;
				break;
			}
		}

		$signature = hash("sha256", $stringToSign);

		return $signature;
	}

	public function getCSPDetails()
	{
		$prefix = $this->config['TRANSACTIONMODE'] !== 'TEST' ? 'LIVE' : 'TEST';
		
		$return = [
			'frame-src' => [
				$this->config[$prefix . 'SERVER'],
				"self"
			],
			'img-src' => [
				$this->config[$prefix . 'SERVER'],
				'https://stdux.inicis.com',
			],
			'style-src' => [
				$this->config[$prefix . 'SERVER'],
			],
		];

		if ($this->session['ismobile'] === false)
		{

			$return['script-src'][] = $this->config[$prefix . 'SERVER'];
		}
		
		return $return;
	}
}
