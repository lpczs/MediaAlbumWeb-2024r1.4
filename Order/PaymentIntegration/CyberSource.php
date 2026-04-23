<?php

require_once 'TaopixAbstractGateway.php';

/**
 * CyberSource payment gateway integration 
 *
 * @author Anthony Dodds <anthony.dodds@taopix.com>
 * @version 1
 * @since 2017r2
 */
class CyberSource extends TaopixAbstractGateway
{
	/**
	 * Cyber Source Configure
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
		if (($this->config['PROFILEID' . (($this->config['TRANSACTIONMODE'] === 'TEST') ? 'TEST' : '')] == '')
				|| ($this->config['ACCESSKEY' . (($this->config['TRANSACTIONMODE'] === 'TEST')  ? 'TEST' : '')] == '')
				|| ($this->config['SECRETKEY' . (($this->config['TRANSACTIONMODE'] === 'TEST') ? 'TEST' : '')] == ''))
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
	 * Cyber Source Confirm
	 *
	 * {@inheritDoc}
	 */
	public function confirm($pCallbackType)
	{
		// build default array to return
		$resultArray = $this->cciEmptyResultArray();
		$resultArray['showerror'] = false;

		$checkVariables = $this->post;
		
		$cciRef = array_key_exists('req_reference_number', $checkVariables) ? $checkVariables['req_reference_number'] : $this->session['ref'];
		// hash success
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
			$this->updateStatus = (($pCallbackType === 'manual') ? false : true);
		}

		if ((isset($checkVariables['signature'])) && ($this->verifyHash($checkVariables['signature'], $checkVariables, '')))
		{
			// check status of the transaction
			if (strtolower($checkVariables['decision']) === 'accept')
			{
				$resultArray['authorised'] = true;
				$resultArray['authorisedstatus'] = 1;
			}
			else
			{
				// cast reason code to be an int
				$reasonCodeInt = (int) $checkVariables['reason_code'];
				$errorMessage = $this->errorReason($reasonCodeInt);
				$resultArray['showerror'] = true;
				$resultArray['authorised'] = false;
				$resultArray['authorisedstatus'] = 0;
				$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': Payment Error';
				$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' .  $errorMessage;
				$resultArray['errorform'] = 'error.tpl';
			}
		}
		else
		{
			// fail hash check
			$resultArray['showerror'] = true;
			$resultArray['authorised'] = false;
			$resultArray['authorisedstatus'] = 0;
			// error messages for hash fail
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': Payment Error';
			// the language string str_OrderAdyenSignatureFailed gives the correct error message of signature check failed 
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . SmartyObj::getParamValue('CreditCardPayment', 'str_OrderAdyenSignatureFailed');
			$resultArray['errorform'] = 'error.tpl';
		}
		$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime = date('H:i:s');

		PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimestamp);

		// assign additional key => values based on current values or values returned from the gateway
		$resultArray['ref'] = (isset($checkVariables['req_reference_number']) ? $checkVariables['req_reference_number'] : '');
		$resultArray['formattedamount'] = $resultArray['amount'];
		$resultArray['transactionid'] = (isset($checkVariables['auth_trans_ref_no']) ? $checkVariables['auth_trans_ref_no'] : '');
		$resultArray['formattedtransactionid'] = $resultArray['transactionid']; //set to auth trans number
		$resultArray['responsecode'] = (isset($checkVariables['reason_code']) ? $checkVariables['reason_code'] : '');
		$resultArray['authorisationid'] = $resultArray['transactionid'];  // this is our unique ID, not the real order ID
		$resultArray['formattedauthorisationid'] = $resultArray['transactionid'];
		$resultArray['bankresponsecode'] = $resultArray['responsecode'];
		$resultArray['cardnumber'] = (isset($checkVariables['req_card_number']) ? $checkVariables['req_card_number'] : '');
		$resultArray['formattedcardnumber'] = $resultArray['cardnumber']; //set to card number
		$resultArray['paymentdate'] = $serverDate;
		$resultArray['paymenttime'] = $serverTime;
		$resultArray['paymentreceived'] = ($resultArray['authorisedstatus'] == 1) ? 1 : 0;
		$resultArray['formattedpaymentdate'] = $serverTimestamp;
		$resultArray['resultisarray'] = false;
		$resultArray['resultlist'] = [];

		return $resultArray;
	}

	/**
	 *
	 * {@inheritDoc}
	 */
	public function generateHash($pString)
	{
		return base64_encode(hash_hmac('sha256', $pString, $this->config['SECRETKEY' . (($this->config['TRANSACTIONMODE'] === 'TEST') ? 'TEST' : '')], true));
	}

	/**
	 *
	 * {@inheritDoc}
	 * @param $pType not used in Cyber Source as map keys are passed through in the pParams
	 */
	public function hashString($pParams, $pType)
	{
		$return = '';
		if (array_key_exists('signed_field_names', $pParams))
		{
			$fieldList = explode(',', $pParams['signed_field_names']);
			$temp = [];
			foreach ($fieldList as $fieldName)
			{
				$temp[] = $fieldName . '=' . $pParams[$fieldName];
			}
			$return = join(',', $temp);
			unset($temp);
		}
		return $return;
	}

	/**
	 *
	 * {@inheritDoc}
	 */
	public function initialize()
	{
		$resultArray = [];

		$smarty = SmartyObj::newSmarty('Order', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

		$fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl']);
		$cancelReturnPath = $fixedUrlPath . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];

		// Cybersource requires the locale string to use hyphens instead of underscores so we need to convert our own language code to this format
		$correctedLocale = str_replace("_", "-", $this->session['browserlanguagecode']);

		// first check if we have any ccidata. this is set when the call is made the first time.
		// if the data is set then the user must have hit the back button on their browser
		if ($this->session['order']['ccidata'] == '')
		{
			$autoReturnPath = $fixedUrlPath . '?fsaction=Order.ccAutomaticCallback&ref=' . $this->session['ref'];
			$manualReturnPath = $fixedUrlPath . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'];
			// define the form fields required
			// if the transactionmode is set to test config keys will be appended with test
			$formFields = [
				'access_key' => $this->config['ACCESSKEY' . (($this->config['TRANSACTIONMODE'] === 'TEST') ? 'TEST' : '')],
				'profile_id' => $this->config['PROFILEID' . (($this->config['TRANSACTIONMODE'] === 'TEST') ? 'TEST' : '')],
				'transaction_uuid' => $this->session['ref'] . '_' . time(),
				'signed_field_names' => '',
				'unsigned_field_names' => '',
				'signed_date_time' => gmdate("Y-m-d\TH:i:s\Z"),
				'locale' => $correctedLocale,
				'transaction_type' => $this->config['TRANSACTIONTYPE'],
				'reference_number' => $this->session['ref'],
				'amount' => number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '.', ''),
				'currency' => $this->session['order']['currencycode'],
				'override_backoffice_post_url' => $autoReturnPath,
				'override_custom_receipt_page' => $manualReturnPath,
				'override_custom_cancel_page' => $cancelReturnPath,
				'bill_to_address_line1' => substr($this->session['order']['billingcustomeraddress1'], 0, 40), // cybersource through visanet has a 40 character limit on address fields
				'bill_to_address_line2' => (isset($this->session['order']['billingcustomeraddress2']) ? substr($this->session['order']['billingcustomeraddress2'], 0, 40) : ''),
				'bill_to_address_city' => strtoupper($this->session['order']['billingcustomercountrycode']) == 'SG' ? $this->session['order']['billingcustomercountryname'] : $this->session['order']['billingcustomercity'],
				'bill_to_address_country' => $this->session['order']['billingcustomercountrycode'], // cybersource uses iso 2 character country code
				'bill_to_address_postal_code' => $this->session['order']['billingcustomerpostcode'],
				'bill_to_email' => $this->session['order']['billingcustomeremailaddress'],
				'bill_to_forename' => $this->session['order']['billingcontactfirstname'],
				'bill_to_surname' => $this->session['order']['billingcontactlastname'],
				'ship_to_address_city' => strtoupper($this->session['order']['shippingcustomercountrycode']) == 'SG' ? $this->session['order']['shippingcustomercountryname'] : $this->session['shipping'][0]['shippingcustomercity'],
				'ship_to_address_country' => $this->session['shipping'][0]['shippingcustomercountrycode'], // cybersource uses iso 2 character country code
				'ship_to_address_line1' => substr($this->session['shipping'][0]['shippingcustomeraddress1'], 0, 60), // shipping address line 1 has a limit of 60 characters through cybersource
				'ship_to_address_postal_code' => $this->session['shipping'][0]['shippingcustomerpostcode'],
				'ship_to_address_state' => (strlen($this->session['shipping'][0]['shippingcustomerstate']) > 2) ? '' : $this->session['shipping'][0]['shippingcustomerstate'],
				'ship_to_forname' => $this->session['shipping'][0]['shippingcontactfirstname'],
				'ship_to_surname' => $this->session['shipping'][0]['shippingcontactlastname'],
				'ship_to_phone' => '',
				'customer_ip_address' => $_SERVER['REMOTE_ADDR']
			];

			$formFields = $this->addItemFields($formFields);
			// set signed fields
			$fieldNames = array_keys($formFields);
			$formFields['signed_field_names'] = join(',', $fieldNames);
			unset($fieldNames);

			// generate string to hash
			$hashString = $this->hashString($formFields, '');

			// attach hashed string as the request signature
			$formFields['signature'] = $this->generateHash($hashString);

			// define Smarty variables
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('payment_url', $this->config['SERVER' . (($this->config['TRANSACTIONMODE'] === 'TEST') ? 'TEST' : '')]);

			$smarty->assign('parameter', $formFields);
			$smarty->assign('method', 'post'); //should be post request

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
	 * Add item information to the form fields
	 * 
	 * @param array $fields reference to the array of fields allready defined 
	 */
	protected function addItemFields($pFields)
	{
		$fields = $pFields;
		$itemCount = count($this->session['items']);
		if ($itemCount > 0)
		{
			// loop through all line items in the cart and add the appropriate line item details
			for ($i = 0; $i < $itemCount; $i++)
			{
				$itemFields = [
					'item_' . $i .'_code' => $this->session['items'][$i]['itemproductcode'],
					'item_' . $i . '_name' => str_replace(array('"', "'"), '', LocalizationObj::getLocaleString($this->session['items'][$i]['itemproductname'], $this->session['browserlanguagecode'], true)),
					'item_' . $i . '_quantity' => $this->session['items'][$i]['itemqty'],
					'item_' . $i . '_sku' => (trim($this->session['items'][$i]['itemproductskucode']) !== '' ? $this->session['items'][$i]['itemproductskucode'] : $this->session['items'][$i]['itemproductcode']),
					'item_' . $i . '_unit_price' => number_format(($this->session['items'][$i]['itemtotalsellwithtaxalldiscounted']/$this->session['items'][$i]['itemqty']), $this->session['order']['currencydecimalplaces'], '.', ''),
				];
				$fields = array_merge($fields, $itemFields);
			}
			// include shipping item
			$itemFields = [
					'item_' . $i .'_code' => $this->session['shipping'][0]['shippingmethodcode'],
					'item_' . $i . '_name' => str_replace(array('"', "'"), '', LocalizationObj::getLocaleString($this->session['shipping'][0]['shippingmethodname'], $this->session['browserlanguagecode'], true)),
					'item_' . $i . '_quantity' => 1,
					'item_' . $i . '_sku' => $this->session['shipping'][0]['shippingmethodcode'],
					'item_' . $i . '_unit_price' => number_format($this->session['shipping'][0]['shippingratetotalsellwithtax'], $this->session['order']['currencydecimalplaces'], '.', ''),
				];
			$fields = array_merge($fields, $itemFields);
			
			$fields['line_item_count'] = ($itemCount + 1);
		}
		return $fields;
	}

	/**
	 *
	 * {@inheritDoc}
	 */
	public function verifyHash($pSuppliedHash, $pParams, $pType)
	{
		$hashString = $this->hashString($pParams, $pType);
		return $this->generateHash($hashString, $this->config['SECRETKEY' . (($this->config['TRANSACTIONMODE'] === 'TEST') ? 'TEST' : '')]) === $pSuppliedHash ? true : false;
	}
	
	/**
	 * Generate an error message based on an error code
	 * 
	 * @param int $pReasonCode reason code
	 */
	protected function errorReason($pReasonCode)
	{
		$errorMessage = '';
		// generate error message based on reasonCode these can be found at
		// http://apps.cybersource.com/library/documentation/dev_guides/CC_Svcs_SO_API/Credit_Cards_SO_API.pdf
		// codes between 101 - 110
		if(($pReasonCode >= 101) && ($pReasonCode <= 110))
		{
			// invalid parameter
			$errorMessage = SmartyObj::getParamValue('', 'str_ErrorInvalidParameter');
		}
		else if((($pReasonCode >= 150) && ($pReasonCode <= 152)) // codes betweeb 150 and 152
				|| (($pReasonCode >= 200) && ($pReasonCode <= 254))) // codes between 200 -  254
		{
			// transaction declined/failed 
			$errorMessage = SmartyObj::getParamValue('CreditCardPayment', 'str_OrderAdyenTransactionFailed');
		}
		else // any other code
		{
			// show error of the error code
			$errorMessage = SmartyObj::getParamValue('', 'str_Error') . ': '. $pReasonCode;
		}
		return $errorMessage;
	}
}
?>