<?php
require_once __DIR__ . '/TaopixAbstractGateway.php';
require_once __DIR__ . '/Request/CurlHandler.php';

class Klarna extends TaopixAbstractGateway
{
	private $baseURL = '';
	private $licenseKeyAddress = '';

	private $baseURLS = [
		'TESTEU' => 'https://api.playground.klarna.com/',
		'TESTUS' => 'https://api-na.playground.klarna.com/',
		'LIVEEU' => 'https://api.klarna.com/',
		'LIVEUS' => 'https://api-na.klarna.com/'
	];

	/**
	 * Sets the base URL of the API endpoint.
	 *
	 * @param string $pBaseURL Base URL to set.
	 */
	public function setBaseURL($pBaseURL)
	{
		$this->baseURL = $pBaseURL;
	}

	/**
	 * Gets the base URL of the API endpoint.
	 *
	 * @return string $pBaseURL Base URL.
	 */
	public function getBaseURL()
	{
		return $this->baseURL;
	}

	/**
	 * Gets the base URLs array.
	 * 
	 * @return array Array containing the different API endpoints.
	 */
	public function getBaseURLs()
	{
		return $this->baseURLS;
	}

	public function __construct($pConfig, &$pSession, &$pGetVars, &$pPostVars)
	{
		parent::__construct($pConfig, $pSession, $pGetVars, $pPostVars);
		$this->setBaseURL($this->getKlarnaAPIEndPoint());
		$this->curlConnection = new CurlHandler('json', $this->defaultCurlOptions($this->config['USERNAME'], $this->config['PASSWORD']));
		$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($this->session['licensekeydata']['groupcode']);
		$this->licenseKeyAddress = $licenseKeyArray['countrycode'];
	}

	/**
	 * @inheritDoc
	 */
	public function configure()
	{
		$resultArray = [
			'active' => false,
			'form' => '',
			'scripturl' => '',
			'script' => '',
			'action' => '',
			'gateways' => [],
			'requestpaymentparamsremotely' => true
		];

		$sslEnabled = $_SERVER['HTTPS'] ? true : false;

		AuthenticateObj::clearSessionCCICookie();

		if ($sslEnabled)
		{
			$smarty = SmartyObj::newSmarty('CreditCardPayment', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

			// If CSP is active add the directives specified for the gateway.
			if ($this->cspBuilder !== null)
			{
				$this->addCSPDetails();
			}

			if ($this->session['ismobile'])
			{
				$script = $smarty->fetchLocale('order/PaymentIntegration/Klarna/Klarna_small.tpl');
			}
			else
			{
				$script = $smarty->fetchLocale('order/PaymentIntegration/Klarna/Klarna_large.tpl');
			}

			// Supported purchase countries : AU,AT,BE,CA,DK,FI,FR,DE,IE,IT,NL,NO,PL,PT,ES,SE,CH,GB,US
			if (in_array(strtoupper($this->licenseKeyAddress), explode(',', $this->config['PURCHASECOUNTRIES'])))
			{
				$resultArray['active'] = true;
			}

			$resultArray['script'] = $script;
			$resultArray['scripturl'] = 'https://x.klarnacdn.net/kp/lib/v1/api.js';
		}

		return $resultArray;
	}

	/**
	 * @inheritDoc
	 */
	public function initialize()
	{
		$returnArray = array('result' => 1, 'clienttoken' => '', 'errormessage' => '', 'paymentmethodcategories' => array(), 'paymentmethodcount' => 0);

		if ($this->session['order']['ordergiftcardtotal'] == 0)
		{
			$order = $this->generateKlarnaOrderData();

			try {
				$availablePaymentMethods = array();
				$paymentMethodCount = 0;
				
				$session = $this->createSession($order);

				if (array_key_exists('payment_method_categories', $session))
				{
						// Create an array of the available payment methods for the Klarna session.
						$availablePaymentMethods = array_reduce($session['payment_method_categories'], function($output, $item) {
						$output[$item['identifier']] = array('name' => $item['name'], 'asseturl' => $item['asset_urls']['descriptive']);
						return $output;
					}, []);

					$paymentMethodCount = count($availablePaymentMethods);
					$returnArray['clienttoken'] = $session['client_token'];
					$returnArray['paymentmethodcategories'] = $availablePaymentMethods;
					$returnArray['paymentmethodcount'] = $paymentMethodCount;
				}
				
				if ($paymentMethodCount == 0)
				{
					$smarty = SmartyObj::newSmarty('CreditCardPayment', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);
					$returnArray['error'] = 'str_OrderNoKlarnaPaymentMethodsAvailable';
					$returnArray['errormessage'] = $smarty->get_config_vars('str_OrderNoKlarnaPaymentMethodsAvailable');
				}
			} 
			catch (\Exception $e) 
			{
				$smarty = SmartyObj::newSmarty('CreditCardPayment', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);
				$serverTimestamp = DatabaseObj::getServerTime();

				$returnArray['error'] = $e->getMessage();
				$returnArray['errormessage'] = $smarty->get_config_vars('str_OrderKlarnaExceptionMessageInvalidRequest');

				PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimestamp, $e->getCode(), $e->getMessage());
			}

		}
		else
		{
			$smarty = SmartyObj::newSmarty('CreditCardPayment', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);
			$giftCardError = $smarty->get_config_vars('str_OrderKlarnaUnavailableGiftCardApplied');
			$returnArray['error'] = $giftCardError;
			$returnArray['errormessage'] = $giftCardError;

		}

		return json_encode($returnArray);
	}

	/**
	 * @inheritDoc
	 */
	public function confirm($pCallbackType)
	{
		// build required array
		$resultArray = $this->cciEmptyResultArray();
		$resultArray['showerror'] = false;
		$resultArray['resultisarray'] = false;

		$authorised = false;
		$authorisedStatus = 0;
		$pendingReason = '';
		$ref = 0;

		// the automatic callback is called by the TAOPIX server after it has successfully performed a charge to the card.
		// in order to replicate a server to server call we must pull the payment params from the POST.
		if ($pCallbackType == 'automatic')
		{
			$pendingReason = 'PENDING';
			$authorised = true;
			$authorisedStatus = 1;

			$ref = $this->post['ref'];
			$transactionID = $this->post['transactionid'];
			$paymentMeans = $this->post['paymentmeans'];
			$addressStatus = $this->post['addressstatus'];
			
			$parentLogID = -1;
			$orderID = -1;
			$update = false;
			$this->updateStatus = false;
		}
		else
		{
			// as this is a manual callback then we can rely on the data in the ccilog to continue
			// so the customer is redirected to the order confirmation page. This is due to the fact that an entry
			// logged by the automatic callnback is only logged when the card has successfully been charged.
			$ref = $this->get['ref'];
			$cciEntry = PaymentIntegrationObj::getCciLogEntry($ref);

			$authorised = true;
			$authorisedStatus = 1;

			$transactionID = $cciEntry['transactionid'];
			$pendingReason = $cciEntry['pendingreason'];
			$paymentMeans = $cciEntry['paymentmeans'];
			$addressStatus = $cciEntry['addressstatus'];
			$parentLogID = $cciEntry['id'];
			$orderID = $cciEntry['orderid'];
			$update = true;
			$this->updateStatus = true;
		}

		$serverTimeStamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime = date('H:i:s');

		//Assign the rest of the details sent back
		$resultArray['ref'] = $ref;
		$resultArray['transactionid'] = $transactionID;
		$resultArray['pendingreason'] = $pendingReason;
		$resultArray['paymentmeans'] = $paymentMeans;
		$resultArray['addressstatus'] = $addressStatus;
		$resultArray['authorised'] = $authorised;
		$resultArray['authorisedstatus'] = $authorisedStatus;
		$resultArray['paymentreceived'] = 1;
		$resultArray['webbrandcode'] = $this->session['webbrandcode'];
		$resultArray['currencycode'] = $this->session['order']['currencycode'];
		$resultArray['amount'] = $this->session['order']['ordertotaltopay'];
		$resultArray['formattedamount'] = $this->session['order']['ordertotaltopay'];
		$resultArray['parentlogid'] = $parentLogID;
		$resultArray['orderid'] = $orderID;
		$resultArray['update'] = $update;
		$resultArray['formattedamount'] = $this->session['order']['ordertotaltopay'];
		$resultArray['paymentdate'] = $serverDate;
		$resultArray['paymentime'] = $serverTime;
		$resultArray['formattedpaymentdate'] = $serverTimeStamp;
		$resultArray['resultisarray'] = false;
		$resultArray['resultlist'] = [];

		return $resultArray;
	}

	/**
	 * @inheritDoc
	 */
	public function hashString($pParams, $pType)
	{
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function verifyHash($pSuppliedHash, $pParams, $pType)
	{
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function generateHash($pString)
	{
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function processPaymentToken($pPaymentToken)
	{
		$resultArray = array();
		$resultArray['error'] = '';
		$resultArray['errormessage'] = '';
		$resultArray['redirecturl'] = '';
		$resultArray['data'] = array();
		$authorizationToken = $pPaymentToken;

		$this->curlConnection = new CurlHandler('json', $this->defaultCurlOptions($this->config['USERNAME'], $this->config['PASSWORD']));

		// Generate the order data array.
		$data = $this->generateKlarnaOrderData();

		try {
			$data = $this->createOrder($authorizationToken, $data);
			
			// we need to set the pm in the GET to Klarna so that the automatic callback can work correctly
			$_GET['pm'] = 'KLARNA';

			$fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl']);
			$resultArray['redirecturl'] = $fixedUrlPath . '?fsaction=Order.ccManualCallback&pm=KLARNA&ref=' . $this->session['ref'];

			$resultArray['data']['ref'] = $this->session['ref'];
			$resultArray['data']['transactionid'] = $data['order_id'];
			$resultArray['data']['paymentmeans'] = $data['authorized_payment_method']['type'];
			$resultArray['data']['addressstatus'] = $data['fraud_status'];

		} 
		catch (\Exception $e) 
		{
			$smarty = SmartyObj::newSmarty('CreditCardPayment', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);
			$serverTimestamp = DatabaseObj::getServerTime();

			$returnArray['error'] = $e->getMessage();
			$returnArray['errormessage'] = $smarty->get_config_vars('str_OrderKlarnaExceptionMessageInvalidRequest');

			PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimestamp, $e->getCode(), $e->getMessage());
		}

		return $resultArray;
	}

	/**
	 * @inheritDoc
	 *
	 * @param string $pMerchantID Merchant ID.
	 * @param string $pSharedSecret Shared secret from Klarna.
	 */
	protected function defaultCurlOptions($pMerchantID, $pSharedSecret)
	{
		$returnArray = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
				'Accept: application/json',
				'Authorization: Basic '. base64_encode($pMerchantID . ":" . $pSharedSecret)
			],
			CURLOPT_ENCODING => '',
			CURLOPT_TIMEOUT => TPX_CURL_TIMEOUT,
			CURLOPT_MAXREDIRS => 1,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CAINFO => UtilsObj::getCurlPEMFilePath()
		];
		
		return $returnArray;
	}

	/**
	 * Send the API request to Klarna to create a session.
	 *
	 * @param array $pSessionData Session data to send to Klarna.
	 * @return array Array containing result data from Klarna.
	 */
	private function createSession($pSessionData)
	{
		$endPoint = $this->getBaseURL() . '/payments/v1/sessions';
		return $this->apiCall($endPoint, $pSessionData);
	}

	/**
	 * Send the API request to Klarna to create the order.
	 *
	 * @param string $pAuthorizationToken Authorisation token from Klarna.
	 * @param array $pOrderData Order data to send to Klarna.
	 * @return array Array containing result data from Klarna.
	 */
	private function createOrder($pAuthorizationToken, $pOrderData)
	{
		$endPoint = $this->getBaseURL() . '/payments/v1/authorizations/' . $pAuthorizationToken . '/order';
		return $this->apiCall($endPoint, $pOrderData);
	}

	/**
	 * Send the API request to Klarna to capture the order.
	 *
	 * @param string $pOrderID Order ID to capture payment for.
	 * @param array $pCaptureData Capture data to send to Klarna.
	 * @return string This API call does not return a response (only on success).
	 */
	public function createCapture($pOrderID, $pCaptureData)
	{
		$endPoint = $this->getBaseURL() . '/ordermanagement/v1/orders/' . $pOrderID . '/captures';
		return $this->apiCall($endPoint, $pCaptureData);
	}

	/**
	 * Send the API request to Klarna.
	 *
	 * @param string $pEndPoint Full URL to API endpoint. 
	 * @param array $pDataArray Data to send as part of the API.
	 * @return array Response data array, or empty array if the request does not return anything.
	 * @throws \Exception If the API result returns with an error.
	 */
	private function apiCall($pEndPoint, $pDataArray)
	{
		$apiResponseParsed = [];
		$apiResponse = $this->curlConnection->connectionSend($pEndPoint, '', 'POST', $pDataArray, 1);

		// Some API call do not return a response so the $apiResponse will be null.
		if ($apiResponse !== '')
		{
			$apiResponseParsed = json_decode($apiResponse, true);

			if (array_key_exists('error_code', $apiResponseParsed))
			{
				throw new \Exception($apiResponseParsed['error_code'] . "\n" . implode("\n", $apiResponseParsed['error_messages']) . "\n" . $apiResponseParsed['correlation_id']);
			}
		}

		return $apiResponseParsed;
	}

	/**
	 * @inheritDoc
	 */
	public function getCSPDetails()
	{
		return [
			'default-src' => [
				'https://*.klarna.com/'
			],
			'script-src' => [
				'https://*.klarna.com/',
				'https://*.klarnacdn.net/'
			],
			'frame-src' => [
				'https://*.klarna.com/'
			],
			'img-src' => [
				'https://*.klarna.com/',
				'https://*.klarnacdn.net/',
				'https://*.klarnaevt.com'
			],
			'connect-src' => [
				'https://*.klarna.com/',
				'https://*.klarnaevt.com'
			]
		];
	}

	/**
	 * Generates the order data to sent to Klarna.
	 * 
	 * @return array Formatted order data array.
	 */
	public function generateKlarnaOrderData()
	{
		$orderTaxBreakDownArray = array();
		$orderLinesArray = array();

		foreach ($this->session['items'] as $item)
		{
			$lineItem = array(
				"type" => "physical",
				"name" => LocalizationObj::getLocaleString($item['itemproductname'], $this->session['browserlanguagecode'], true),
				"quantity" => $item['itemqty'],
				"unit_price" => (int) bcmul((($this->licenseKeyAddress == 'US') ? $item['itemtotalsellnotaxnodiscount'] : $item['itemtotalsellwithtaxnodiscount']) / $item['itemqty'], 100),
				"total_amount" => (int) bcmul((($this->licenseKeyAddress == 'US') ? $item['itemtotalsellnotaxalldiscounted'] : $item['itemtotalsellwithtaxalldiscounted']), 100),
				"total_discount_amount" => (int) bcmul((($this->licenseKeyAddress == 'US') ? $item['itemdiscountvaluenotax'] : $item['itemdiscountvaluenwithtax']), 100)
			);
			
			// For European Countries and Australia we must report tax information on an orderline
			if ($this->licenseKeyAddress != 'US')
			{
				$lineItem["tax_rate"] = (int) bcmul($item['itemtaxrate'], 100);
				$lineItem["total_tax_amount"] = (int) bcmul($item['itemtaxtotal'], 100);
			}
			else
			{
				// For the US we must report tax totals seperate sales tax order lines
				$orderTaxBreakDownArray[] = (int) bcmul($item['itemtaxtotal'], 100);
			}

			$orderLinesArray[] = $lineItem;
		}

		$orderLinesArray = $this->addOrderFooterComponentsToOrderLines($orderLinesArray, $orderTaxBreakDownArray, $this->session['order']['orderFooterSections']);

		foreach($this->session['order']['orderFooterCheckboxes'] as $checkbox)
		{
			if ($checkbox['checked'])
			{
				$lineItem = array(
					"type" => "physical",
					"name" => LocalizationObj::getLocaleString($checkbox['categoryname'], $this->session['browserlanguagecode'], true),
					"quantity" => ($checkbox['orderfooterusesproductquantity'] == 1) ? $checkbox['itemqty'] * $checkbox['quantity'] : $checkbox['quantity'],
					"unit_price" => ($checkbox['orderfooterusesproductquantity'] == 1) ? (int) bcmul(($checkbox['totalsell']) / ($checkbox['itemqty'] * $checkbox['quantity']), 100) : (int) bcmul($checkbox['totalsell'] / $checkbox['quantity'], 100),
					"total_amount" => (int) bcmul((($this->licenseKeyAddress == 'US') ? $checkbox['totalsellnotax'] : $checkbox['totalsellwithtax']), 100),
					"total_discount_amount" => (int) bcmul((($this->licenseKeyAddress == 'US') ? $checkbox['discountvaluenotax'] : $checkbox['discountvaluewithtax']), 100),
				);
				

				// For European Countries and Australia we must report tax information on an orderline
				if ($this->licenseKeyAddress != 'US')
				{
					$lineItem["unit_price"] =  ($checkbox['orderfooterusesproductquantity'] == 1) ? (int) bcmul(($checkbox['totalsell']) / ($checkbox['itemqty'] * $checkbox['quantity']), 100) : (int) bcmul($checkbox['totalsell'] / $checkbox['quantity'], 100);
					$lineItem["tax_rate"] = (int) bcmul($checkbox['orderfootertaxrate'], 100);
					$lineItem["total_tax_amount"] = (int) bcmul($checkbox['totalsellwithtax'] - $checkbox['totalsellnotax'], 100);
				}
				else
				{
					// For the US we must report tax totals seperate sales tax order lines
					$orderTaxBreakDownArray[] = (int) bcmul($checkbox['totalsellwithtax'] - $checkbox['totalsellnotax'], 100);
				}

				$orderLinesArray[] = $lineItem;
			}
		}

		$shippingLineItem = array(
			"type" => "shipping_fee",
			"name" => LocalizationObj::getLocaleString($this->session['shipping'][0]['shippingmethodname'], $this->session['browserlanguagecode'], true),
			"quantity" => 1,
			"unit_price" => (int) bcmul((($this->licenseKeyAddress == 'US') ? $this->session['shipping'][0]['shippingratesellnotax'] : $this->session['shipping'][0]['shippingratesellwithtax']), 100),
			"total_amount" => (int) bcmul((($this->licenseKeyAddress == 'US') ? $this->session['shipping'][0]['shippingratetotalsellnotax'] : $this->session['shipping'][0]['shippingratetotalsellwithtax']), 100),
			"total_discount_amount" => ($this->session['shipping'][0]['shippingratediscountvalue'] > 0 && ($this->licenseKeyAddress != 'US')) ? (int) bcmul($this->session['shipping'][0]['shippingratesellwithtax'] - $this->session['shipping'][0]['shippingratetotalsellwithtax'], 100) : (int) bcmul($this->session['shipping'][0]['shippingratediscountvalue'], 100),
		);

		// For European Countries and Australia we must report tax information on an orderline
		if ($this->licenseKeyAddress != 'US')
		{
			$shippingLineItem["tax_rate"] = (int) bcmul($this->session['shipping'][0]['shippingratetaxrate'], 100);
			$shippingLineItem["total_tax_amount"] = (int) bcmul($this->session['shipping'][0]['shippingratetaxtotal'], 100);
		}
		else
		{
			// For the US we must report tax totals seperate sales tax order lines
			$orderTaxBreakDownArray[] = (int) bcmul($this->session['shipping'][0]['shippingratetaxtotal'], 100);
		}

		$orderLinesArray[] = $shippingLineItem;

		// if all tax rates are equal and a voucher is applied we must report the discount value as a lineitem
		// this is because the pricing engine does not spread the amounts across each line item including shipping
		if ($this->session['order']['orderalltaxratesequal'] && $this->session['order']['vouchercode'] != '')
        {
            $discountLine = array(
                "type" => "discount",
                "name" => $this->session['order']['vouchername'],
                "quantity" => 1,
                "unit_price" => (int) bcmul($this->session['order']['ordertotaldiscount'] * -1, 100),
                "total_amount" => (int) bcmul($this->session['order']['ordertotaldiscount'] * -1, 100)
            );
            $orderLinesArray[] = $discountLine;
        }

		// For the US we must report Tax in the order as seperate line items
		if ($this->licenseKeyAddress == 'US')
		{
			foreach ($orderTaxBreakDownArray as $taxRate)
			{
				$orderLinesArray[] = array(
					"type" => "sales_tax",
					"name" => "Sales Tax",
					"quantity" => 1,
					"unit_price" => $taxRate,
					"total_amount" => $taxRate
				);
			}
		}

		$data = [
			"purchase_country" => $this->licenseKeyAddress == $this->session['order']['billingcustomercountrycode'] ? $this->licenseKeyAddress: $this->session['order']['billingcustomercountrycode'],
			"purchase_currency" => $this->session['order']['currencycode'],
			"locale" => $this->getKlarnaLocaleFromPurchaseCountry($this->session['order']['billingcustomercountrycode']),
			"order_amount" => (int) bcmul($this->session['order']['ordertotaltopay'], 100),
			"order_lines" => $orderLinesArray,
			"merchant_reference1" => $this->session['ref']
		];

		// For the US we must report the order tax amount at order level as tax info is not reported at an item level
		if ($this->licenseKeyAddress == 'US')
		{
			$data['order_tax_amount'] = (int) bcmul($this->session['order']['ordertotaltax'], 100);
		}

		if ($this->licenseKeyAddress == $this->session['order']['billingcustomercountrycode'])
		{
			$data["billing_address"] = [
				"given_name" => $this->session['order']['billingcontactfirstname'],
				"family_name" => $this->session['order']['billingcontactlastname'],
				"email" => $this->session['order']['billingcustomeremailaddress'],
				"street_address" => $this->session['order']['billingcustomeraddress1'],
				"postal_code" => $this->session['order']['billingcustomerpostcode'],
				"city" => $this->session['order']['billingcustomercity'],
				"country" => $this->session['order']['billingcustomercountrycode']
			];
		}

		return $data;
	}

	/**
	 * Returns the Klarna API endpoint base URL based on the config setting.
	 * 
	 * @return string Klarna API endpoint base URL. Defaults to TESTEU endpoint.
	 */
	public function getKlarnaAPIEndPoint()
	{
		return UtilsObj::getArrayParam($this->getBaseURLs(), $this->config['ENDPOINT'], $this->baseURLS['TESTEU']);
	}

	/**
	 * Returns the locale formatted for Klarna.
	 *
	 * @param string $pPurchaseCountry Country.
	 * @return string Formatted locale string.
	 */
	public function getKlarnaLocaleFromPurchaseCountry($pPurchaseCountry)
	{
		switch ($pPurchaseCountry) 
		{ 
			case 'AT': 
			case 'CH': 
			case 'DE': 
			{ 
				$locale = 'de-' . $pPurchaseCountry; 
				break; 
			}
			case 'BE': 
			{ 
				$locale = 'nl-BE'; 
				break; 
			}
			case 'DK': 
			{ 
				$locale = 'da-DK'; 
				break; 
			}
			case 'NO': 
			{ 
				$locale = 'nb-NO'; 
				break; 
			}
			case 'SE': 
			{ 
				$locale = 'sv-SE'; 
				break; 
			}
			case 'PT':
			case 'IT': 
			case 'ES':
			case 'FR':  
			case 'FI': 
			case 'NL':
			case 'PL':
			{ 
				$locale = strtolower($pPurchaseCountry) . '-' . $pPurchaseCountry; 
				break; 
			} 
			case 'GB':
			case 'US': 
			case 'AU':
			case 'CA':
			case 'IE':
			{ 
				$locale = 'en-' . $pPurchaseCountry; 
				break; 
			} 
			default: 
			{ 
				$locale = 'en-GB'; 
				break;
			}
		}

		return $locale;
	}

	public function addOrderFooterComponentsToOrderLines(&$pOrderLinesArray, &$pOrderTaxBreakDownArray, $orderFooterItems)
	{
		foreach ($orderFooterItems as $section)
		{
			$lineItem = array(
				"type" => "physical",
				"name" => LocalizationObj::getLocaleString($section['sectionname'], $this->session['browserlanguagecode'], true),
				"quantity" => ($section['orderfooterusesproductquantity'] == 1) ? $section['itemqty'] * $section['quantity'] : $section['quantity'],
				"unit_price" => ($section['orderfooterusesproductquantity'] == 1) ? (int) bcmul(($section['totalsell']) / ($section['itemqty'] * $section['quantity']), 100) : (int) bcmul($section['totalsell'] / $section['quantity'], 100),
				"total_amount" => (int) bcmul((($this->licenseKeyAddress == 'US') ? $section['totalsellnotax'] : $section['totalsellwithtax']), 100),
				"total_discount_amount" => (int) bcmul((($this->licenseKeyAddress == 'US') ? $section['discountvaluenotax'] : $section['discountvaluewithtax']), 100),
			);

			// For European Countries and Australia we must report tax information on an orderline
			if ($this->licenseKeyAddress != 'US')
			{
				$lineItem["unit_price"] =  ($section['orderfooterusesproductquantity'] == 1) ? (int) bcmul(($section['totalsell']) / ($section['itemqty'] * $section['quantity']), 100) : (int) bcmul($section['totalsell'] / $section['quantity'], 100);
				$lineItem["tax_rate"] = (int) bcmul($section['orderfootertaxrate'], 100);
				$lineItem["total_tax_amount"] = (int) bcmul($section['totalsellwithtax'] - $section['totalsellnotax'], 100);
			}
			else
			{
				// For the US we must report tax totals seperate sales tax order lines
				$pOrderTaxBreakDownArray[] = (int) bcmul($section['totalsellwithtax'] - $section['totalsellnotax'], 100);
			}

			$pOrderLinesArray[] = $lineItem;

			foreach ($section['checkboxes'] as $checkbox)
			{
				if ($checkbox['checked'])
				{
					$lineItem = array(
						"type" => "physical",
						"name" => LocalizationObj::getLocaleString($checkbox['categoryname'], $this->session['browserlanguagecode'], true),
						"quantity" => ($checkbox['orderfooterusesproductquantity'] == 1) ? $checkbox['itemqty'] * $checkbox['quantity'] : $checkbox['quantity'],
						"unit_price" => ($checkbox['orderfooterusesproductquantity'] == 1) ? (int) bcmul(($checkbox['totalsell']) / ($checkbox['itemqty'] * $checkbox['quantity']), 100) : (int) bcmul($checkbox['totalsell'] / $checkbox['quantity'], 100),
						"total_amount" => (int) bcmul((($this->licenseKeyAddress == 'US') ? $checkbox['totalsellnotax'] : $checkbox['totalsellwithtax']), 100),
						"total_discount_amount" => (int) bcmul((($this->licenseKeyAddress == 'US') ? $checkbox['discountvaluenotax'] : $checkbox['discountvaluewithtax']), 100),
					);
										// For European Countries and Australia we must report tax information on an orderline
					if ($this->licenseKeyAddress != 'US')
					{
						$lineItem["unit_price"] =  ($checkbox['orderfooterusesproductquantity'] == 1) ? (int) bcmul(($checkbox['totalsell']) / ($checkbox['itemqty'] * $checkbox['quantity']), 100) : (int) bcmul($checkbox['totalsell'] / $checkbox['quantity'], 100);
						$lineItem["tax_rate"] = (int) bcmul($checkbox['orderfootertaxrate'], 100);
						$lineItem["total_tax_amount"] = (int) bcmul($checkbox['totalsellwithtax'] - $checkbox['totalsellnotax'], 100);
					}
					else
					{
						// For the US we must report tax totals seperate sales tax order lines
						$pOrderTaxBreakDownArray[] = (int) bcmul($checkbox['totalsellwithtax'] - $checkbox['totalsellnotax'], 100);
					}

					$pOrderLinesArray[] = $lineItem;
				}
			}

			if (!empty($section['subsections']))
			{
				$this->addOrderFooterComponentsToOrderLines($pOrderLinesArray, $pOrderTaxBreakDownArray, $section['subsections']);
			}
		}

		return $pOrderLinesArray;
	}
}

?>