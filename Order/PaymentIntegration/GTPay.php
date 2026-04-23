<?php

require_once 'TaopixAbstractGateway.php';

if (! class_exists('CurlHandler'))
{
	require_once __DIR__ . '/Request/CurlHandler.php';
}

class GTPay extends TaopixAbstractGateway
{
	/**
	 * handler for curl calls so we can mock this when unit testing
	 * @var object Request/CurlHandler
	 */
	protected $curlConnection = null;

	/**
	 * Returns the config array for the gateway.
	 * @return array
	 */
	public function configure()
	{
		$resultArray = [
			'active' => true,
			'form' => '',
			'scripturl' => '',
			'script' => '',
			'action' => '',
			'gateways' => []
		];


		// if the transactionmode is set to test config keys will be appended with test
		if (($this->config['MERCHANTID'] == '')
			|| ($this->config['HASHKEY'] == ''))
		{
			$resultArray['active'] = false;
		}

		// accepted currencies are not dependant on which transaction mode we are using
		if (strpos($this->config['GTPAYCURRENCIES'], $this->session['order']['currencyisonumber']) === false)
		{
			$resultArray['active'] = false;
		}

		AuthenticateObj::clearSessionCCICookie();
		return $resultArray;
	}

	/**
	 *
	 * {@inheritDoc}
	 */
	public function confirm($pCallbackType)
	{
		// build default array to return
		$resultArray = $this->cciEmptyResultArray();
		$resultArray['showerror'] = false;

		$cciRef = isset($this->get['ref']) ? $this->get['ref'] : $this->session['ref'];

		$cciEntry = PaymentIntegrationObj::getCciLogEntry($cciRef);
		if ($cciEntry === [])
		{
			// empty first callback
			$resultArray['ref'] = $cciRef;
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
			$resultArray['ref'] = $cciRef;
			$resultArray['webbrandcode'] = $cciEntry['webbrandcode'];
			$resultArray['currencycode'] = $cciEntry['currencycode'];
			$resultArray['amount'] = $cciEntry['formattedamount'];
			$resultArray['parentlogid'] = $cciEntry['id'];
			$resultArray['orderid'] = $cciEntry['orderid'];
			$resultArray['update'] = true;
			$this->updateStatus = ($pCallbackType === 'manual');
		}

		// Verify that the hash we get back is correct.
		if ($this->verifyHash($this->post['gtpay_verification_hash'], $this->post, 'server'))
		{
			$verifyResponse = $this->verifyResponse();

			if ((! array_key_exists('errorname', $verifyResponse)) && ([] !== $verifyResponse))
			{
				// Response code 00 is a success, everything else is an error.
				if ('00' === $verifyResponse['ResponseCode'])
				{
					// Validate that the amount at the gateway and order match.
					if (number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '', '') === $verifyResponse['Amount'])
					{
						// Authed
						$resultArray['authorised'] = 1;
						$resultArray['authorisedstatus'] = true;
						$resultArray['showerror'] = false;

						// Configure details to return based on the returned data
						$resultArray['formattedamount'] = $this->session['order']['ordertotaltopay'];
						$resultArray['transactionid'] = $this->post['gtpay_tranx_id'];
						$resultArray['formattedtransactionid'] = $resultArray['transactionid']; //set to auth trans number
						$resultArray['responsecode'] = $verifyResponse['ResponseCode'];
						$resultArray['authorisationid'] = $resultArray['transactionid'];  // this is our unique ID, not the real order ID
						$resultArray['formattedauthorisationid'] = $resultArray['transactionid'];
						$resultArray['bankresponsecode'] = $verifyResponse['ResponseCode'];
					}
					else
					{
						$resultArray['showerror'] = true;
						$resultArray['authorised'] = 0;
						$resultArray['authorisedstatus'] = false;
						// error messages for hash fail
						$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $verifyResponse['ResultCode'];
						$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . SmartyObj::getParamValue('Order', 'str_ErrorPaymentFailed1');
						$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_ErrorPaymentFailed1');
						$resultArray['data4'] = 'Amount incorrect';
						$resultArray['errorform'] = 'error.tpl';
					}
				}
				else
				{
					// Not Authed
					// Payment was not successful.
					$resultArray['showerror'] = true;
					$resultArray['authorised'] = 0;
					$resultArray['authorisedstatus'] = false;
					// error messages for hash fail
					$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $verifyResponse['ResultCode'];
					$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $verifyResponse['resultMsg'];
					$resultArray['data3'] = '';
					$resultArray['data4'] = '';
					$resultArray['errorform'] = 'error.tpl';
				}
			}
		}
		else
		{
			// Invalid hash show error
			$resultArray['showerror'] = true;
			$resultArray['authorised'] = 0;
			$resultArray['authorisedstatus'] = false;
			// error messages for hash fail
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode');
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . SmartyObj::getParamValue('CreditCardPayment', 'str_OrderAdyenSignatureFailed');
			$resultArray['data3'] = '';
			$resultArray['data4'] = '';
			$resultArray['errorform'] = 'error.tpl';
		}

		return $resultArray;
	}

	private function verifyResponse()
	{
		$returnArray = [];

		$this->curlConnection = new CurlHandler('GET', $this->getCurlParams());

		$validationParams = [
			"mertid" => $this->config['MERCHANTID'],
			"amount" => number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '', ''),
			"transxid" => $this->post['gtpay_tranx_id'],
		];

		$validationParams['hash'] = $this->generateHash($this->hashString($validationParams, 'verify'));

		$endPoint = $this->config['GTPAYVERIFYENDPOINT'] . '?' . http_build_query($validationParams);

		$response = $this->curlConnection->connectionSend($this->config['GTPAYVERIFYSERVER'], $endPoint, 'GET', [], 3);

		// We have an error from some point during the config of the curl connection.
		if (! is_array($response))
		{
			$returnArray = json_decode($response, true);
		}
		else
		{
			$returnArray = $response;
		}

		return $returnArray;
	}

	private function getCurlParams()
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
			$logHandle = fopen(UtilsObj::getTaopixWebInstallPath('/logs') . '/gtpay-curl.log', 'a+');
			$return += [CURLOPT_VERBOSE => true, CURLOPT_STDERR => $logHandle];
		}

		return $return;
	}

	/**
	 * GTPay Initialize
	 * {@inheritDoc}
	 */
	public function initialize()
	{
		$resultArray = [];

		$smarty = SmartyObj::newSmarty('Order', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

		$fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl'], false);
		$brandFolderName = $GLOBALS['ac_config']['WEBBRANDFOLDERNAME'] . '/';

		$notificationUrl = '';
		$returnParams = $this->session['ref'];
		$folderPos = null;

		/**
		 * Check if we have a brand url that uses the BRANDFOLDER/BrandCode format.
		 * If we do we need to adjust the notification url correctly, or we would need to copy the callback script into the brand.
		 */
		if (false !== ($folderPos = strpos($fixedUrlPath, $brandFolderName)))
		{
			// Get the base part of the url.
			$baseUrl = substr($fixedUrlPath, 0, $folderPos);

			// Configure the notificationUrl.
			$notificationUrl = $baseUrl;
		}
		else
		{
			// We have a unique brand url already so we can use that.
			$notificationUrl = $fixedUrlPath;
		}
		$notificationUrl .=  '/PaymentIntegration/GTPay/Callback.php';

		$cancelReturnPath = $fixedUrlPath . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];

		// first check if we have any ccidata. this is set when the call is made the first time.
		// if the data is set then the user must have hit the back button on their browser
		if ($this->session['order']['ccidata'] == '')
		{
			$orderData = $this->session['items'][0]['itemqty'] . ' x ' . substr(LocalizationObj::getLocaleString($this->session['items'][0]['itemproductname'], $this->session['browserlanguagecode'], true), 0, 110);
			$formFields = [
				'gtpay_mert_id' => $this->config['MERCHANTID'],
				'gtpay_tranx_id' => $this->session['ref'] . '_' . time(),
				'gtpay_tranx_amt' => number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '', ''),
				'gtpay_tranx_curr' => $this->session['order']['currencyisonumber'],
				'gtpay_cust_id' => $this->session['userid'],
				'gtpay_cust_name' => $this->session['order']['billingcontactfirstname'] . ' ' .$this->session['order']['billingcontactlastname'],
				'gtpay_tranx_memo' => $orderData,
				'gtpay_tranx_noti_url' => $notificationUrl,
				'gtpay_echo_data' => base64_encode($returnParams),
			];

			$formFields['gtpay_hash'] = $this->generateHash($this->hashString($formFields, 'client'));

			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('payment_url', $this->config['GTPAYSERVER']);

			$smarty->assign('parameter', $formFields);
			$smarty->assign('method', 'post');

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $this->session['ref']);
			$smarty->assign('ccicookievalue', $this->session['order']['ccicookie']);

			// set the ccidata to remember we have started
			$this->session['order']['ccidata'] = 'start';
			DatabaseObj::updateSession();
		}
		else
		{
			// automatic cancel action from back button press
			AuthenticateObj::clearSessionCCICookie();

			$smarty->assign('server', $cancelReturnPath);
		}


		// mobile browser check then return appropriate content
		if ($this->session['ismobile'] === true)
		{
			$resultArray = [
				'template' => $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest_small.tpl'),
				'javascript' => $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest.tpl')
			];
			return $resultArray;
		}
		else
		{
			$smarty->displayLocale('order/PaymentIntegration/PaymentRequest_large.tpl');
		}
	}

	/**
	 *
	 * {@inheritDoc}
	 */
	public function verifyHash($pSuppliedHash, $pParams, $pType)
	{
		$hashString = $this->hashString($pParams, $pType);
		return ($pSuppliedHash === $this->generateHash($hashString));
	}

	/**
	 *
	 * {@inheritDoc}
	 */
	public function generateHash($pString)
	{
		return hash("sha512", $pString);
	}

	/**
	 *
	 * {@inheritDoc}
	 * @param array pParams Depending on the signature check pParams will be an array
	 */
	public function hashString($pParams, $pType)
	{
		$hashedFields = [];
		$rawKeys = '';

		// Get the keys we are hashing on based on the type of hash we are verifying
		switch (strtolower($pType))
		{
			case 'server':
				$rawKeys = $this->config['HASHFIELDSSEVER'];
				break;
			case 'verify':
				$rawKeys = $this->config['HASHFIELDSVERIFY'];
				break;
			case 'client':
			default:
				$rawKeys = $this->config['HASHFIELDSCLIENT'];
				break;
		}

		$keys = explode(',', $rawKeys);

		foreach ($keys as $key)
		{
			$hashedFields[] = $pParams[$key];
		}

		$hashedFields[] = $this->config['HASHKEY'];

		return join('', $hashedFields);
	}
}