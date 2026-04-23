<?php

require_once __DIR__ . '/TaopixAbstractGateway.php';
if (! class_exists('CurlHandler'))
{
	require_once __DIR__ . '/Request/CurlHandler.php';
}

/**
 * Integration for Tap v2 - Card JS Library.
 * 
 * @link https://tappayments.api-docs.io/2.0/card-js-library
 * @version 2
 * @since 2020r6
 */
class Tap extends TaopixAbstractGateway
{
	protected $keySuffix = '';

	public function __construct($pConfig, &$pSession, &$pGetVars, &$pPostVars)
	{
		parent::__construct($pConfig, $pSession, $pGetVars, $pPostVars);
		$this->curlConnection = new CurlHandler('json', $this->defaultCurlOptions());
	}

	/**
	 * {@inheritDoc}
	 */
	public function configure()
	{
		$resultArray = [
			'active' => true,
			'form' => '',
			'scripturl' => '',
			'script' => '',
			'action' => '',
			'requestpaymentparamsremotely' => false
		];

		// Check that the API keys have beeen configured.
		if ((UtilsObj::getArrayParam($this->config, 'APIKEY') === '') || (UtilsObj::getArrayParam($this->config, 'APISECRET') === ''))
		{
			$resultArray['active'] = false;
		}

		// Check that the currency used in the cart is supported.
		if (strpos(UtilsObj::getArrayParam($this->config, 'CURRENCYLIST'), UtilsObj::getArrayParam($this->session['order'], 'currencycode')) === false)
		{
			$resultArray['active'] = false;
		}

		// Check the connection is via SSL.
		if ((! isset($_SERVER['HTTPS'])) || ($_SERVER['HTTPS'] == 'off'))
		{
			$resultArray['active'] = false;
		}

		AuthenticateObj::clearSessionCCICookie();
		
		return $resultArray;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function confirm($pCallbackType)
	{
		$resultArray = $this->cciEmptyResultArray();
		$apiResponseArray = [];
		$ref = UtilsObj::getArrayParam($this->get, 'ref');
		$chargeID = '';
		$authorised = false;
		$authorisedStatus = 0;
		$update = false;
		$showError = false;
		$paymentReceived = 0;
		$transactionID = '';
		$responseCode = -1;
		$responseDescription = '';
		$authorisationID = -1;
		$last4CardNumber = 0000;
		$paymentMeans = '';
		$threeDSecureStatus = 'N';
		$logErrorMessage = '';
		$parentLogID = 0;
		$orderID = 0;

		if ($pCallbackType === 'manual')
		{
			$chargeID = UtilsObj::getArrayParam($this->get, 'tap_id');
		}
		else
		{
			// processPaymentToken() will trigger an automatic callback, we just need to log in the CCILog at that point that the initial charge has been created.
			if (strtolower(UtilsObj::getArrayParam($this->get, 'cmd')) !== 'processpaymenttoken')
			{
				// Get the hash string sent from Tap from the header.
				// The hashstring gets sent back in different cases so force it to lowercase.
				$hashString = UtilsObj::getArrayParam(array_change_key_case(apache_request_headers(), CASE_LOWER), 'hashstring');

				// Get the JSON data as an array.
				$jsonDataArray = json_decode(file_get_contents('php://input'), true);

				// Verify that the call came from Tap.
				$hashValid = $this->verifyHash($hashString, $jsonDataArray, 'charge');

				if ($hashValid)
				{
					$chargeID = $jsonDataArray['id'];
				}
				else
				{
					$logErrorMessage = 'Hash not valid';
				}
			}
		}

		// We will have a chargeID if we got here after being redirected by Tap or from an automatic callback.
		if ($chargeID !== '')
		{
			// Get the charge status from Tap.
			$apiResponseArray = $this->apiCall('/charges/' . $chargeID, 'GET', []);

			// Check the API call was successful.
			if (! array_key_exists('errors', $apiResponseArray))
			{
				$status = strtolower(UtilsObj::getArrayParam($apiResponseArray, 'status'));
				$transactionID = UtilsObj::getArrayParam($apiResponseArray, 'id');
				$responseCode = UtilsObj::getArrayParam($apiResponseArray['response'], 'code');
				$responseDescription = UtilsObj::getArrayParam($apiResponseArray['response'], 'message');
				$authorisationID = UtilsObj::getArrayParam($apiResponseArray['transaction'], 'authorization_id');
				$last4CardNumber = (isset($apiResponseArray['card'])) ? UtilsObj::getArrayParam($apiResponseArray['card'], 'last_four') : '';
				$paymentMeans = UtilsObj::getArrayParam($apiResponseArray['source'], 'payment_type') . ' ' . UtilsObj::getArrayParam($apiResponseArray['source'], 'payment_method');

				if (UtilsObj::getArrayParam($apiResponseArray, 'threeDSecure', false))
				{
					$threeDSecureStatus = (isset($apiResponseArray['security'])) ? UtilsObj::getArrayParam($apiResponseArray['security']['threeDSecure'], 'status') : '';
				}

				// The automatic and manual may arrive at the same time.
				// We need to have the second automatic callback to wait until the manual is completed.
				// if after a minute the ccilog is empty then it is likely that the manualcallback has not happened.
				// if this is the case let the automatic callback attempt to create the order.
				set_time_limit(120);
				$retryCount = 30;

				while ($retryCount > 0)
				{
					$cciEntry = PaymentIntegrationObj::getCciLogEntry($ref);

					if ($pCallbackType === 'automatic') 
					{
						if (empty($cciEntry) && (($threeDSecureStatus === 'N') && ($status !== 'captured')))
						{
							$retryCount = 0;
						}
						else  if (empty($cciEntry))
						{
							// The automatic needs to wait for the manual to complete.
							$retryCount--;
							UtilsObj::wait(2);
						}
						else if ((!empty($cciEntry)) && (UtilsObj::getArrayParam($cciEntry, 'responsecode') !== 000) && (UtilsObj::getArrayParam($cciEntry, 'mode') === 'AUTOMATIC'))
						{
							// If a previous payment has returned with an error status then there will be a CCILOG entry so we need to wait for the next manual callback.
							$retryCount--;
							UtilsObj::wait(2);
						}
						else
						{
							$retryCount--;
							UtilsObj::wait(2);
						}
					}
					else if ($pCallbackType === 'manual') 
					{
						if (empty($cciEntry))
						{
							$retryCount = 0;
						}
						else if (!empty($cciEntry) && (UtilsObj::getArrayParam($cciEntry, 'orderid', -1) != -1))
						{
							$retryCount = 0;
						}
						else
						{
							$retryCount--;
							UtilsObj::wait(2);
						}
					}
				}

				if (! empty($cciEntry))
				{
					// Reload the session, another call may have updated the session with an order number while this one was waiting.
					$sessionData = DatabaseObj::getSessionData($ref);

					if ( (isset($sessionData['order'])) && ($sessionData['order']['ordernumber'] !== $this->session['order']['ordernumber']) )
					{
						// Another call has created the order, we need to update the session with the correct order number.
						// This is mainly to prevent a blank order number on the confirmation page.
						$this->session['order']['ordernumber'] = $sessionData['order']['ordernumber'];

						$orderID = $sessionData['order']['id'];
					}
					else
					{
						$orderID = UtilsObj::getArrayParam($cciEntry, 'orderid', 0);
					}

					// Get some values from the CCILog.
					$parentLogID = UtilsObj::getArrayParam($cciEntry, 'id', 0);

					$update = ($orderID > 0);
					$resultArray['update'] = $update;
					$this->updateStatus = $update;
					$this->cciLogUpdate = true;
					$this->logCCIEntryForSameTransactionID = true;
				}
				else
				{
					$resultArray['parentlogid'] = -1;
					$resultArray['orderid'] = -1;
					$resultArray['update'] = false;
					$this->updateStatus = false;
				}
				
				if ($status === 'captured')
				{
					$authorised = true;
					$authorisedStatus = 1;
					$paymentReceived = 1;
				}
				else if ($pCallbackType === 'manual')
				{
					// We got an error status so we need to display the error page.
					$showError = true;
					$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $responseCode;
					$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $responseDescription;
					$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $transactionID;
					$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . UtilsObj::getArrayParam($apiResponseArray['reference'], 'order');
				}
			}
			else if ($pCallbackType === 'manual')
			{
				// API call failed so we need to display the error page.
				$showError = true;
				$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . UtilsObj::getArrayParam($apiResponseArray['errors'][0], 'code');
				$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . UtilsObj::getArrayParam($apiResponseArray['errors'][0], 'description');
				$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $chargeID;
				$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $ref;
			}
		}
		
		$serverTimeStamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime = date('H:i:s');
		
		// Write to log.
		PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimeStamp, $logErrorMessage, $apiResponseArray);
		
		if ($showError)
		{
			$resultArray['errorform'] = 'error.tpl';
		}

		$resultArray['showerror'] = $showError;
		$resultArray['ref'] = $ref;
		$resultArray['update'] = $update;
		$resultArray['authorised'] = $authorised;
		$resultArray['authorisedstatus'] = $authorisedStatus;
		$resultArray['paymentreceived'] = $paymentReceived;
		$resultArray['transactionid'] = $transactionID;
		$resultArray['responsecode'] = $responseCode;
		$resultArray['responsedescription'] = $responseDescription;
		$resultArray['authorisationid'] = $authorisationID;
		$resultArray['cardnumber'] = $last4CardNumber;
		$resultArray['formattedcardnumber'] = $last4CardNumber;
		$resultArray['cvvresponsecode'] = '';
		$resultArray['paymentcertificate'] =  $chargeID;
		$resultArray['paymentmeans'] = $paymentMeans;
		$resultArray['addressstatus'] = '';
		$resultArray['postcodestatus'] = '';
		$resultArray['transactiontype'] = '';
		$resultArray['threedsecurestatus'] = $threeDSecureStatus;
		$resultArray['webbrandcode'] = UtilsObj::getArrayParam($this->session, 'webbrandcode');
		$resultArray['currencycode'] = UtilsObj::getArrayParam($this->session['order'], 'currencycode');
		$resultArray['amount'] = UtilsObj::getArrayParam($this->session['order'], 'ordertotaltopay');
		$resultArray['formattedamount'] = number_format(UtilsObj::getArrayParam($this->session['order'], 'ordertotaltopay'), UtilsObj::getArrayParam($this->session['order'], 'currencydecimalplaces'), '.', '');
		$resultArray['parentlogid'] = $parentLogID;
		$resultArray['orderid'] = $orderID;
		$resultArray['formattedamount'] = UtilsObj::getArrayParam($this->session['order'], 'ordertotaltopay');
		$resultArray['paymentdate'] = $serverDate;
		$resultArray['paymenttime'] = $serverTime;
		$resultArray['formattedpaymentdate'] = $serverTimeStamp;
		$resultArray['resultisarray'] = false;
		$resultArray['resultlist'] = [];
		return $resultArray;
	}

	/**
	 * {@inheritDoc}
	 */
	public function generateHash($pString)
	{
		return hash_hmac('sha256', $pString, UtilsObj::getArrayParam($this->config, 'APISECRET'));
	}

	/**
	 * {@inheritDoc}
	 */
	public function hashString($pParams, $pType)
	{
		$id = UtilsObj::getArrayParam($pParams, 'id');
		// Format the amount as Tap will strip off any trailing zeroes causing the hash check to fail.
		$amount = number_format(UtilsObj::getArrayParam($pParams, 'amount'), UtilsObj::getArrayParam($this->session['order'], 'currencydecimalplaces'), '.', '');
		$currency = UtilsObj::getArrayParam($pParams, 'currency');
		$gateway_reference = UtilsObj::getArrayParam($pParams['reference'], 'gateway');
		$payment_reference = UtilsObj::getArrayParam($pParams['reference'], 'payment');
		$status = UtilsObj::getArrayParam($pParams, 'status');
		$created = UtilsObj::getArrayParam($pParams['transaction'], 'created');

		return 'x_id'. $id . 'x_amount' . $amount. 'x_currency' . $currency . 'x_gateway_reference' . $gateway_reference . 'x_payment_reference' . $payment_reference . 'x_status'
			. $status . 'x_created' . $created;
	}

	/**
	 * {@inheritDoc}
	 */
	public function verifyHash($pSuppliedHash, $pParams, $pType)
	{
		$hashString = $this->hashString($pParams, $pType);
		return ($this->generateHash($hashString) === $pSuppliedHash) ? true : false;
	}

	/**
	 * {@inheritDoc}
	 */
	public function initialize()
	{
		global $gSession;

		$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		// first check if we have any ccidata. this is set when the call is made the first time.
		// if the data is set then the user must have hit the back button on their browser
		if ($gSession['order']['ccidata'] == '') 
		{
			
			// Optional if the funds are to go to a specific merchant account (for a different brand).
			$merchantID = ($this->config['MERCHANTID'] !== null) ? $this->config['MERCHANTID'] : '';

			$fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl']);
			$postURL = $fixedUrlPath . '?fsaction=Order.ccAutomaticCallback&ref=' . $this->session['ref'];
			$redirectURL = $fixedUrlPath . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'];
		
			$paramArray = [
				'publickey' => $this->config['APIKEY'],
				'amount' => number_format(UtilsObj::getArrayParam($this->session['order'], 'ordertotaltopay'), UtilsObj::getArrayParam($this->session['order'], 'currencydecimalplaces'), '.', ''),
				'currency' => UtilsObj::getArrayParam($this->session['order'], 'currencycode'),
				'threeDSecure' => true,
				'save_card' => false,
				'description' => LocalizationObj::getLocaleString(UtilsObj::getArrayParam($this->session['items'][0], 'itemproductname'), UtilsObj::getArrayParam($this->session, 'browserlanguagecode'), true),
				'statement_descriptor' => '',
				'metadata' => [],
				'reference' => [
					'transaction' => UtilsObj::getArrayParam($this->session, 'ref'),
					'order' => UtilsObj::getArrayParam($this->session, 'ref')
				],
				'receipt' => [
					'email' => (((int) UtilsObj::getArrayParam($this->config, 'EMAILRECEIPT') === 1) ? true : false),
					// We can't provide SMS receipt as we do not capture country code for phone numbers.
					'sms' => false
				],
				'customer' => [
					'first_name' => UtilsObj::getArrayParam($this->session['order'], 'billingcontactfirstname'),
					'last_name' => UtilsObj::getArrayParam($this->session['order'], 'billingcontactlastname'),
					'email' => UtilsObj::getArrayParam($this->session['order'], 'billingcustomeremailaddress'),
					'phone' => [
						'country_code' => '',
						'number' => UtilsObj::getArrayParam($this->session['order'], 'billingcustomertelephonenumber')
					]
				],
				'source' => [
					'id' => 'src_all'
				],
				'post' => [
					'url' => $postURL
				],
				'redirect' => [
					'url' => $redirectURL
				],
				'MerchantId' => $merchantID
			];


			// Create the charge request.
			$apiCallResult = $this->apiCall('charges', 'POST', $paramArray);
			$redirectURL = '';
			$error = '';
			$errorMessage = '';
			
			// Check the API call was successful.
			if (! array_key_exists('errors', $apiCallResult))
			{
				$status = strtolower(UtilsObj::getArrayParam($apiCallResult, 'status'));

				if ($status === 'initiated')
				{
					// If 3D secure is used, then we need to redirect to Tap.
					$redirectURL = UtilsObj::getArrayParam($apiCallResult['transaction'], 'url');
				}
				else if ($status === 'captured')
				{
					// If 3D secure is not used, then payment is captured immediately so we can redirect to the manualCallback/order confirmation page.
					// Append on the chargeID to the URL.
					$redirectURL = UtilsObj::getArrayParam($apiCallResult['redirect'], 'url') . '&tap_id=' . UtilsObj::getArrayParam($apiCallResult, 'id');
				}
				else
				{
					$error = UtilsObj::getArrayParam($apiCallResult['response'], 'code');
					$errorMessage =  UtilsObj::getArrayParam($apiCallResult['response'], 'message');
				}
			}
			else
			{
				$error = UtilsObj::getArrayParam($apiCallResult['errors'][0], 'code');
				$errorMessage = UtilsObj::getArrayParam($apiCallResult['errors'][0], 'description');
			}

			if ($error == '')
			{
				// Assign Smarty variables
				$smarty->assign('parameter', []);
				$smarty->assign('payment_url', $redirectURL);
				$smarty->assign('method', 'post');

				AuthenticateObj::defineSessionCCICookie();
				$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
				$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

				// set the ccidata to remember we have jumped to Tap
				$gSession['order']['ccidata'] = 'start';
				DatabaseObj::updateSession();

				$smarty->cachePage = true; // allow the page to be cached so that the browser back button works correctly
				if ($gSession['ismobile'] == true) {
					$resultArray['template'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest_small.tpl');
					$resultArray['javascript'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest.tpl');
					return $resultArray;
				} else {
					$smarty->displayLocale('order/PaymentIntegration/PaymentRequest_large.tpl');
				}
			}
			else
			{
				$smarty->assign('data1', SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $error);
				$smarty->assign('data2', SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $errorMessage);
				$smarty->assign('data3', SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . UtilsObj::getArrayParam($this->session, 'ref'));
				
				if ($this->session['ismobile'] == true)
				{
					$smarty->assign('displayInline', true);
					$smarty->assign('homeurl', UtilsObj::correctPath($this->session['webbrandweburl']));
					$smarty->assign('ref', $this->session['ref']);

					$resultArray['template'] = $smarty->fetchLocale('order/PaymentIntegration/error_small.tpl', $this->session['browserlanguagecode']);
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
			$smarty->assign('payment_url', $cancelReturnPath);

			if ($gSession['ismobile'] == true) {
				$resultArray['template'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest_small.tpl');
				$resultArray['javascript'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest.tpl');
				return $resultArray;
			} else {
				$smarty->displayLocale('order/PaymentIntegration/PaymentRequest_large.tpl');
			}
		}
	}

	/**
	 * Send a request to the Tap REST API.
	 *
	 * @param string $pCommand Which endpoint to call on the API.
	 * @param string $pMethod HTTP request method e.g. POST or GET.
	 * @param array $pParams Array of params to send.
	 * @return array JSON decoded array with the results of the API call.
	 */
	private function apiCall($pCommand, $pMethod, $pParams)
	{
		$apiResponse = $this->curlConnection->connectionSend(UtilsObj::getArrayParam($this->config, 'APISERVER'), $pCommand, $pMethod, $pParams, TPX_CURL_RETRY);
		return json_decode($apiResponse, true);
	}

	/**
	 * {@inheritDoc}
	 */
	public function defaultCurlOptions()
	{
		$return = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_TIMEOUT => 30,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_HTTPHEADER => [
				'content-type: application/json',
				'Authorization: Bearer ' . UtilsObj::getArrayParam($this->config, 'APISECRET')
			],
			CURLOPT_CAINFO => UtilsObj::getCurlPEMFilePath()
		];

		if (UtilsObj::getArrayParam($this->config, 'LOGAPIREQUESTS') === '1')
		{
			$logHandle = fopen(__DIR__ . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'tap-curl.log', 'a+');
			$return += [CURLOPT_VERBOSE => true, CURLOPT_STDERR => $logHandle];
		}

		return $return;
	}
}
