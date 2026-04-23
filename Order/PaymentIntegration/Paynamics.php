<?php

require_once 'TaopixAbstractGateway.php';
if(! class_exists('SoapHandler'))
{
	require_once __DIR__ . '/Request/SoapHandler.php';
}

/**
 * Paynamics payment gateway integration
 *
 * @author Anthony Dodds <anthony.dodds@taopix.com>
 * @date 18th March 2017
 * @version 1
 * @since 2017r2
 */
class Paynamics extends TaopixAbstractGateway
{
	/**
	 * Default constructor
	 */
	public function __construct($pConfig, &$pSession, &$pGetVars, &$pPostVars)
	{
		parent::__construct($pConfig, $pSession, $pGetVars, $pPostVars);
		$this->keySuffix = ($this->config['TRANSACTIONMODE'] === 'TEST') ? 'TEST' : '';
	}
	/**
	 * Paynamics Configure
	 *
	 * {@inheritDoc}
	 */
	public function configure()
	{
		$resultArray = [
			'active' => true,
			'form' => '',
			'scripturl' => '',
			'script' => '',
			'action' => ''
		];

		// if the transactionmode is set to test config keys will be appended with test
		if (($this->config['MERCHANTID' . $this->keySuffix] == '')
			|| ($this->config['CERTIFICATE' . $this->keySuffix] == '')
			|| ($this->config['MERCHANT' . $this->keySuffix] == '')
			|| ($this->config['COMPANYNAME' . $this->keySuffix] == ''))
		{
			$resultArray['active'] = false;
		}

		// accepted currencies are not dependant on which transaction mode we are using
		if (strpos($this->config['CURRENCYLIST'], $this->session['order']['currencycode']) === false)
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
		$processedInformation = null;
		// build default array to return
		$resultArray = $this->cciEmptyResultArray();
		$resultArray['showerror'] = false;

		$checkVariables = ($pCallbackType === 'automatic' ? $this->post : $this->get);

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
			$this->updateStatus = (($pCallbackType === 'manual') ? false : true);
		}

		if (isset($checkVariables['paymentresponse']))
		{
			// payment response info passed
			$processedInformation = $this->processResponse($checkVariables['paymentresponse']);
		}
		else
		{
			// check if we have a request/response id set if we do make a soap request to get the information
			if ((isset($checkVariables['requestid'])) && (trim($checkVariables['requestid']) !== ''))
			{
				$processedInformation = $this->soapResponse($cciRef, $checkVariables['requestid'], $checkVariables['responseid']);
			}
		}

		if (($processedInformation !== null)
			&& ($this->verifyHash($processedInformation->application->signature, $processedInformation, 'server') === true))
		{
			// hash success
			$responseStatus = $processedInformation->responseStatus;
			if ((strtolower($responseStatus->response_code) === 'gr001') || (strtolower($responseStatus->response_code) === 'gr002') || (strtolower($responseStatus->response_code) === 'gr033'))
			{
				// success transaction
				$resultArray['authorised'] = true;
				$resultArray['authorisedstatus'] = 1;
				// set auth status to 0 if transaction is pending
				if(strtolower($responseStatus->response_code) === 'gr033')
				{
					$resultArray['authorisedstatus'] = 0;
				}
				
				$serverTimestamp = DatabaseObj::getServerTime();
				$serverDate = date('Y-m-d');
				$serverTime = date('H:i:s');
					
				$resultArray['formattedamount'] = $resultArray['amount'];
				$resultArray['transactionid'] = $processedInformation->application->response_id;
				$resultArray['formattedtransactionid'] = $resultArray['transactionid']; //set to auth trans number
				$resultArray['responsecode'] = $responseStatus->response_code;
				$resultArray['authorisationid'] = $responseStatus->processor_response_authcode;  // this is our unique ID, not the real order ID
				$resultArray['formattedauthorisationid'] = $resultArray['authorisationid'];
				$resultArray['bankresponsecode'] = $responseStatus->processor_response_id;
				$resultArray['cardnumber'] = '';
				$resultArray['formattedcardnumber'] = $resultArray['cardnumber']; //set to card number
				$resultArray['paymentdate'] = $serverDate;
				$resultArray['paymenttime'] = $serverTime;
				$resultArray['paymentreceived'] = ($resultArray['authorisedstatus'] == 1) ? 1 : 0;
				$resultArray['formattedpaymentdate'] = $serverTimestamp;
				$resultArray['resultisarray'] = false;
				$resultArray['resultlist'] = [];
			}
			else
			{
				// fail transaction
				$errorMessage = $this->getErrorMessage($responseStatus->response_code);
				$resultArray['showerror'] = true;
				$resultArray['authorised'] = false;
				$resultArray['authorisedstatus'] = 0;
				$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': Payment Error';
				$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $errorMessage;
				$resultArray['errorform'] = 'error.tpl';
			}
		}
		else
		{
			// fail hash check
			$resultArray['showerror'] = true;
			$resultArray['authorised'] = 0;
			$resultArray['authorisedstatus'] = false;
			// error messages for hash fail
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': Payment Error';
			// the language string str_orderadyensignaturefailed gives the correct error message of signature check failed 
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . SmartyObj::getParamValue('CreditCardPayment', 'str_OrderTransactionSignatureFailed');
			$resultArray['errorform'] = 'error.tpl';
		}

		return $resultArray;
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
	 * @param array|simplexmlelement pParams Depending on the signature check pParams will either be an array or an object
	 */
	public function hashString($pParams, $pType)
	{
		$return = '';

		if ($pType === 'server')
		{
			// use xml object
			$app = $pParams->application;
			$response = $pParams->responseStatus;

			$return = $app->merchantid . $app->request_id . $app->response_id . $response->response_code
					. $response->response_message . $response->response_advise . $app->timestamp
					. (isset($app->rebill_id) ? $app->rebill_id : '') . $this->config['CERTIFICATE' . $this->keySuffix];
		}
		else if ($pType === 'soap')
		{
			foreach ($pParams as $key => $value)
			{
				$return .= ($key !== 'signature') ? $value : '';
			}
			$return .= $this->config['CERTIFICATE' . $this->keySuffix];
		}
		else
		{
			// use array keys
			$requiredKeys = ['mid', 'requestid', 'ipaddress', 'notificationurl', 'responseurl',
				'fname', 'lname', 'mname', 'address1', 'address2', 'city', 'state', 'country', 'zip',
				'email', 'phone', 'clientip', 'amount', 'currency', 'sec3d'];

			foreach ($requiredKeys as $requiredKey)
			{
				$return .= $pParams[$requiredKey];
			}
			$return .= $this->config['CERTIFICATE' . $this->keySuffix];
		}

		return $return;
	}

	/**
	 * Paynamics Initialize
	 * {@inheritDoc}
	 */
	public function initialize()
	{
		$resultArray = [];

		$smarty = SmartyObj::newSmarty('Order', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

		$fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl']);
		$cancelReturnPath = $fixedUrlPath . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];

		// first check if we have any ccidata. this is set when the call is made the first time.
		// if the data is set then the user must have hit the back button on their browser
		if ($this->session['order']['ccidata'] == '')
		{
			$paramFields = [
				'items' => $this->generateProductList(),
				'mid' => $this->config['MERCHANTID' . $this->keySuffix],
				'requestid' => $this->session['ref'] . '_' . time(),
				'notificationurl' => $fixedUrlPath . '?fsaction=Order.ccAutomaticCallback&ref=' . $this->session['ref'],
				'responseurl' => $fixedUrlPath . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'],
				'cancelurl' => $cancelReturnPath,
				'fname' => $this->session['order']['billingcontactfirstname'],
				'lname' => $this->session['order']['billingcontactlastname'],
				'mname' => '',
				'address1' => $this->session['order']['billingcustomeraddress1'],
				'address2' => (isset($this->session['order']['billingcustomeraddress2']) ? $this->session['order']['billingcustomeraddress2'] : ''),
				'city' => $this->session['order']['billingcustomercity'],
				'state' => $this->session['order']['billingcustomerstate'],
				'country' => $this->session['order']['billingcustomercountrycode'],
				'zip' => $this->session['order']['billingcustomerpostcode'],
				'sec3d' => $this->config['SEC3D'],
				'email' => $this->session['order']['billingcustomeremailaddress'],
				'phone' => $this->session['order']['billingcustomertelephonenumber'],
				'clientip' => $_SERVER['REMOTE_ADDR'],
				'amount' => number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '.', ''),
				'currency' => $this->session['order']['currencycode'],
				'merchantlogo' => '',
				'ipaddress' => $_SERVER['SERVER_ADDR'],
				'paymentdescription' => $this->config['PAYMENTDESCRIPTION']
			];
			$paramFields['signature'] = $this->generateHash($this->hashString($paramFields, 'client'));
			$formFields = [
				'paymentrequest' => base64_encode($this->generateXml($paramFields))
			];

			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('payment_url', $this->config['SERVER' . $this->keySuffix]);

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
		return ($pSuppliedHash === $this->generateHash($hashString)) ? true : false;
	}

	/**
	 * Function to generate the xml Doc, used for testing
	 * 
	 * @param array $pParams items to generate xml doc used in payment request
	 */
	public function getXml($pParams)
	{
		$pParams['items'] = $this->generateProductList();
		$pParams['signature'] = $this->generateHash($this->hashString($pParams, 'client'));
		return $this->generateXml($pParams);
	}

	/**
	 * Convert the returned string from the gateway to a simplexmlelement
	 * 
	 * @param string $pReturnString return string from gateway
	 */
	protected function processResponse($pReturnString)
	{
		$decoded = base64_decode(str_replace(' ', '+', $pReturnString));
		return new SimpleXMLElement($decoded);
	}

	/**
	 * Generate paynamics xml document
	 * 
	 * @param array $pParams array of parameters used for xml generation
	 * @return string generated xml
	 */
	protected function generateXml($pParams)
	{
		$return = '';
		$file = __DIR__ . '/Paynamics/xml-template.txt';
		if(file_exists($file))
		{
			$return = file_get_contents($file);

			foreach ($pParams as $key => $value)
			{
				// replace html entities in all keys apart from items
				$value = ($key !== 'items') ? htmlentities($value) : $value;
				$return = str_replace('{' . $key . '}', $value, $return);
			}
		}
		return $return;
	}

	/**
	 * Generates Paynamics xml format for listing products
	 * 
	 * @return string
	 */
	protected function generateProductList()
	{
		$return = '';

		if (count($this->session['items']) > 0)
		{
			$file = __DIR__ . '/Paynamics/item-template.txt';
			if(file_exists($file))
			{
				$itemTemplate = file_get_contents($file);
				$replace = ['{itemname}', '{quantity}', '{amount}'];
				for ($i = 0; $i < count($this->session['items']); $i++)
				{
					$replacements = [
						htmlentities(LocalizationObj::getLocaleString($this->session['items'][$i]['itemproductname'], $this->session['browserlanguagecode'], true)),
						$this->session['items'][$i]['itemqty'],
						number_format(($this->session['items'][$i]['itemtotalsellwithtaxalldiscounted']/$this->session['items'][$i]['itemqty']), $this->session['order']['currencydecimalplaces'], '.', '')
					];
					$return .= $itemTemplate;
					$return = str_replace($replace, $replacements, $return);
				}
				// shipping item
				$shippingReplacements = [
					htmlentities(LocalizationObj::getLocaleString($this->session['shipping'][0]['shippingmethodname'], $this->session['browserlanguagecode'], true)),
					'1',
					number_format($this->session['shipping'][0]['shippingratetotalsellwithtax'], $this->session['order']['currencydecimalplaces'], '.', '')
				];
				$return .= $itemTemplate;
				$return = str_replace($replace, $shippingReplacements, $return);
			}
		}

		return $return;
	}

	/**
	 * Returns an error string based on the reason code
	 * 
	 * @param string $pReasonCode
	 * @return string
	 */
	protected function getErrorMessage($pReasonCode)
	{
		// known error codes all others should result in a generic error message
		$knownErrors = ['gr003', 'gr004', 'gr005', 'gr006', 'gr007', 'gr008', 'gr009', 'gr010', 'gr011'];
		// transaction declined/failed
		if (in_array(strtolower($pReasonCode), $knownErrors))
		{
			$errorMessage = SmartyObj::getParamValue('CreditCardPayment', 'str_OrderTransactionFailed');
		}
		else
		{
			$errorMessage = SmartyObj::getParamValue('', 'str_Error') . ': ' . $pReasonCode;
		}
		return $errorMessage;
	}

	/**
	 * Returns the payment status of the request/response via a soap action
	 * 
	 * @param string $pRef taopix cart reference
	 * @param string $pRequestId base64 encode request id from paynamics
	 * @param string $pResponseId base64 encoded response id from paynamics
	 * @return type
	 */
	protected function soapResponse($pRef, $pRequestId, $pResponseId)
	{
		$return = null;
		$params = [
			'query' => [
				'merchantid' => $this->config['MERCHANTID' . $this->keySuffix],
				'request_id' => $pRef . '-' . time(),
				'org_trxid' => base64_decode($pResponseId),
				'org_trxid2' => base64_decode($pRequestId),
			]
		];
		$hashString = $this->hashString($params['query'], 'soap');
		$params['query']['signature'] = $this->generateHash($hashString);

		$client = new SoapHandler($this->config['SOAP' . $this->keySuffix]);
		$rawResponse = $client->soapSend('query', $params);
		
		if (isset($rawResponse->queryResult->txns))
		{
			if (isset($rawResponse->queryResult->txns->ServiceResponse))
			{
				$return = $rawResponse->queryResult->txns->ServiceResponse;

				// regenerate signature as the soap gateway does not recreate this
				$return->application->signature = $this->generateHash($this->hashString($return, 'server'));
			}
		}
		
		return $return;
	}
}
?>