<?php

require_once 'TaopixAbstractGateway.php';

if (! class_exists('CurlHandler'))
{
	require_once 'Request/CurlHandler.php';
}

class Platnosci extends TaopixAbstractGateway
{
	/**
 	 * handler for curl calls so we can mock this when unit testing
 	 * @var object Request/CurlHandler
 	 */
 	protected $curlConnection = null;

	public function __construct($pConfig, &$pSession, &$pGetVars, &$pPostVars)
	{
		parent::__construct($pConfig, $pSession, $pGetVars, $pPostVars);
		$this->curlConnection = new CurlHandler('', $this->defaultCurlOptions());
	}

	/**
 	* configure
 	*
 	* {@inheritDoc}
 	*/
    public function configure()
    {
        $resultArray = array();
        AuthenticateObj::clearSessionCCICookie();

        // planosci supported currencies
        $currencyList = '985, 203';
        $first2Digits = substr(UtilsObj::getArrayParam($this->config, 'MD5KEY1'), 0, 2);

        if (strpos($currencyList,UtilsObj::getArrayParam($this->session['order'], 'currencyisonumber')) === false)
        {
			$active = false;
        }
        else
        {
			$active = true;
        }

		$nonceValue = '[nonce]';
		// If CSP is active add the directives specified for the gateway, also populate the nonce value for the injected script tag.
		if (($this->cspBuilder !== null) && ($this->session['ismobile'] != true))
		{
			$this->addCSPDetails();
			$nonceValue = $this->cspBuilder->nonce();
		}

		$resultArray['gateways'] = '';
        $resultArray['active'] = $active;
        $resultArray['form'] = "
	      	var paymenthod = document.getElementsByName('paymentmethods');
	      	for(var i=0;i < paymenthod.length; i++)
	      	{
	      		if(paymenthod[i].value=='CARD')
	      		{
					creditCardContainer = paymenthod[i].parentNode;
					creditCardContainer.appendChild(document.createTextNode('\u00A0\u00A0\u00A0'));
					newscript = document.createElement('script');
					newscript.type = 'text/javascript';
					newscript.text = 'PlnDrawSelect1();';
					" . ($nonceValue = '' ? '' : "newscript.setAttribute('nonce', '" . $nonceValue . "');") . "
					creditCardContainer.appendChild(newscript);
	      		}
	      	}";

        $resultArray['scripturl'] = UtilsObj::getArrayParam($this->config, 'SERVER') . "/" . UtilsObj::getArrayParam($this->config, 'ENCODING') . "/js/" . UtilsObj::getArrayParam($this->config, 'POSID') . "/" . $first2Digits . "/paytype.js";
		$resultArray['script'] = "

    	function PlnDrawSelect1()
    	{
			var selectorOuterDiv = document.createElement('div');
            selectorOuterDiv.setAttribute('class', 'wizard-dropdown');

            var selector = document.createElement('select');
			selector.id = 'paymentgatewaycode';
			selector.name = 'paymentgatewaycode';
            selector.setAttribute('class', 'wizard-dropdown');
			selector.setAttribute('data-decorator', 'forceSelectCard');
			selector.addEventListener('change', function(event) {
				forceSelectCard();
			});

            selectorOuterDiv.appendChild(selector);
            creditCardContainer.appendChild(selectorOuterDiv);

			var option = document.createElement('option');
			option.value = '';
			option.appendChild(document.createTextNode('-- " . SmartyObj::getParamValue('CreditCardPayment', 'str_DropDownPleaseSelectAPaymentType') . " --'));
			selector.appendChild(option);

			if (PlnPayTypeArray) {
		        for (var i = 0; i < PlnPayTypeArray.length; i++) {
					if (PlnPayTypeArray[i].enable == true) {
						var option = document.createElement('option');

						option.value = PlnPayTypeArray[i].type;
						if (option.value == '" . UtilsObj::getArrayParam($this->session['order'], 'paymentgatewaycode') . "')
						{
							option.selected='selected';
						}
						option.appendChild(document.createTextNode(PlnPayTypeArray[i].name));
						selector.appendChild(option);
					}
				}
			}
		}";

        $resultArray['action'] = "validatePayType('" . SmartyObj::getParamValue('CreditCardPayment', 'str_DropDownPleaseSelectAPaymentType') . "')";
		return $resultArray;
    }

	/**
 	* initialize
 	*
 	* {@inheritDoc}
 	*/
    public function initialize()
    {
        $smarty = SmartyObj::newSmarty('Order', UtilsObj::getArrayParam($this->session, 'webbrandcode'), UtilsObj::getArrayParam($this->session, 'webbrandapplicationname'));

    	$cancelReturnPath = UtilsObj::correctPath(UtilsObj::getArrayParam($this->session, 'webbrandweburl')) . '?fsaction=Order.ccCancelCallback&ref=' . UtilsObj::getArrayParam($this->session, 'ref');

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser

        if (UtilsObj::getArrayParam($this->session['order'], 'ccidata') == '')
        {
			$server = UtilsObj::getArrayParam($this->config, 'SERVER') . '/' . UtilsObj::getArrayParam($this->config, 'ENCODING') . '/NewPayment';

			// selected payment gateway
			$pos_id = UtilsObj::getArrayParam($this->config, 'POSID');
			$pay_type = UtilsObj::getArrayParam($this->session['order'], 'paymentgatewaycode');
			$timestamp = time();
			// session ref and brand code are required to deal with their return especially if session has expired when automatic callback returns
			$session_id = UtilsObj::getArrayParam($this->session, 'ref') . '_' . $timestamp . '_' . UtilsObj::getArrayParam($this->session, 'webbrandcode');
			$pos_auth_key = UtilsObj::getArrayParam($this->config, 'POSAUTHKEY');
			$amount = number_format(UtilsObj::getArrayParam($this->session['order'], 'ordertotaltopay'), $this->session['order']['currencydecimalplaces'], '', '');
			$desc = $session_id;
			$desc2 = '';
			$order_id = UtilsObj::getArrayParam($this->session, 'ref');
			$first_name = UtilsObj::getArrayParam($this->session['order'], 'billingcontactfirstname');
			$last_name =  UtilsObj::getArrayParam($this->session['order'], 'billingcontactlastname');
			$payback_login = '';
			$street = UtilsObj::getArrayParam($this->session['order'], 'billingcustomeraddress1');
			$street_hn = '';
			$street_an = '';
			$city = UtilsObj::getArrayParam($this->session['order'], 'billingcustomercity');
			$post_code = UtilsObj::getArrayParam($this->session['order'], 'billingcustomerpostcode');
			$country = UtilsObj::getArrayParam($this->session['order'], 'billingcustomercountrycode');
			$email = UtilsObj::getArrayParam($this->session['order'], 'billingcustomeremailaddress');
			$phone = UtilsObj::getArrayParam($this->session['order'], 'billingcustomertelephonenumber');
			$language = UtilsObj::getArrayParam($this->session, 'browserlanguagecode');
			$client_ip = UtilsObj::getArrayParam($_SERVER, 'REMOTE_ADDR');

			$params = array(
				'pos_id' => $pos_id,
				'pay_type' => $pay_type,
				'session_id' => $session_id,
				'pos_auth_key' => $pos_auth_key,
				'amount' => $amount,
				'desc' => $desc,
				'desc2' => $desc2,
				'order_id' => $order_id,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'payback_login' => $payback_login,
				'street' => $street,
				'street_hn' => $street_hn,
				'street_an' => $street_an,
				'city' => $city,
				'post_code' => $post_code,
				'country' => $country,
				'email' => $email,
				'phone' => $phone,
				'language' => $language,
				'client_ip' => $client_ip,
				'js' => 1,
				'ts' => $timestamp
			);

			// calculate signature
			$signature = self::generateHash($this->hashString($params, strtolower(__FUNCTION__)));
			$params['sig'] = $signature;

			// define smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('parameter', $params);
			$smarty->assign('method', 'POST');

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . UtilsObj::getArrayParam($this->session, 'ref'));
			$smarty->assign('ccicookievalue', UtilsObj::getArrayParam($this->session['order'], 'ccicookie'));

			// set the ccidata to remember we have jumped to the payment gateway
			$this->session['order']['ccidata'] = 'start';
			DatabaseObj::updateSession();

			$smarty->cachePage = true; // allow the page to be cached so that the browser back button works correctly
			if (UtilsObj::getArrayParam($this->session, 'ismobile') == true)
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

            $cancelReturnPath = UtilsObj::correctPath(UtilsObj::getArrayParam($this->session, 'webbrandweburl')) . '?fsaction=Order.ccCancelCallback&ref=' . UtilsObj::getArrayParam($this->session, 'ref');
            $smarty->assign('server', $cancelReturnPath);

            if (UtilsObj::getArrayParam($this->session, 'ismobile') == true)
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

	/**
 	* cancel
 	*
 	* {@inheritDoc}
 	*/
	public function cancel()
    {
		$this->loadSession = true;
		$this->cciLogUpdate = true;
		$ref = UtilsObj::getGETParam('ref', 0);
		$error = UtilsObj::getGETParam('error', '__NOERROR__');
		$resultArray = $this->cciEmptyResultArray();
		$resultArray['showerror'] = false;
		$resultArray['update'] = false;

		$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

		if (count($cciLogEntry) > 0)
		{
			$this->updateStatus = true;
			$resultArray['update'] = true;
			$resultArray['parentlogid'] = UtilsObj::getArrayParam($cciLogEntry, 'id', 0);
			$resultArray['orderid'] = UtilsObj::getArrayParam($cciLogEntry, 'orderid', 0);
		}
		else
		{
			$resultArray['parentlogid'] = 0;
			$resultArray['orderid'] = 0;
		}

		// display error template if there was an error
		if ($error != '__NOERROR__')
		{
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . UtilsObj::getGETParam('error');
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . UtilsObj::getGetParam('session_id');
			$resultArray['data3'] = '';
			$resultArray['data4'] = '';
			$resultArray['errorform'] = 'error.tpl';
			$resultArray['showerror'] = true;

			// set error message if the cancel button was pressed in the payment gateway pages
			if ($error == 508)
			{
				$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_TitleOrderCancellation');
			}
		}

        $resultArray['result'] = '';
        $resultArray['ref'] = $ref;
        $resultArray['authorised'] = false;
        $resultArray['authorisedstatus'] = 0;
        $resultArray['responsecode'] = UtilsObj::getGETParam('error');
        $resultArray['responsedescription'] = UtilsObj::getGETParam('session_id');
        $resultArray['transactionid'] = UtilsObj::getGETParam('session_id');

        return $resultArray;
    }

	/**
	 *  get status message string
	 */
	protected function getReturnedMessage($pResultArray)
  	{
		$returnArray = array('code' => '', 'message' => '');
		$status = UtilsObj::getArrayParam($pResultArray, 'status');
        $smarty = SmartyObj::newSmarty('CreditCardPayment');

		// check the pos number matches
		if ($pResultArray['pos_id'] != $this->config['POSID'])
		{
			$returnArray = array('code' => false, 'message' => $smarty->get_config_vars('str_OrderPlanosciIncorrectPOS'));  // incorrect pos number
		}

		if ($returnArray['code'] === false)
		{
			switch ($pResultArray['status'])
			{
				case 1: // new
				{
					$returnArray = array('code' => $status, 'message' => $smarty->get_config_vars('str_OrderPlanosci_1'));
					break;
				}
				case 2: // cancelled
				{
					$returnArray =  array('code' => $status, 'message' => $smarty->get_config_vars('str_OrderPlanosci_2'));
					break;
				}
				case 3: // rejected
				{
					$returnArray =  array('code' => $status, 'message' => $smarty->get_config_vars('str_OrderPlanosci_3'));
					break;
				}
				case 4: // started
				{
					$returnArray =  array('code' => $status, 'message' => $smarty->get_config_vars('str_OrderPlanosci_4'));
					break;
				}
				case 5: // waiting
				{
					$returnArray =  array('code' => $status, 'message' => $smarty->get_config_vars('str_OrderPlanosci_5'));
					break;
				}
				case 6: // denied
				{
					$returnArray =  array('code' => $status, 'message' => $smarty->get_config_vars('str_OrderPlanosci_6'));
					break;
				}
				case 7: // payment rejected
				{
					$returnArray = array('code' => $status, 'message' => $smarty->get_config_vars('str_OrderPlanosci_7'));
					break;
				}
				case 99: // payment ended
				{
					$returnArray = array('code' => $status, 'message' => $smarty->get_config_vars('str_OrderPlanosci_99'));
					break;
				}
				case 888: // error
				{
					$returnArray = array('code' => $status, 'message' => $smarty->get_config_vars('str_OrderPlanosci_888'));
					break;
				}
				default:
				{
					$returnArray =  array('code' => '', 'message' => $smarty->get_config_vars('str_OrderPlanosci_0'));
					break;
				}
			}
		}

		return $returnArray;
	}


	/**
	 * get the details of the transaction from the payment gateway
	 *
	 * @param array $pParams
	 * @return array
	 */
	protected function getTransactionDetails($pParams)
	{
		$resultArray = array();
		$timestamp = time();
		$sessionID = UtilsObj::getArrayParam($pParams, 'session_id');

		// calculate sig
		$sig = $this->generateHash(UtilsObj::getArrayParam($this->config, 'POSID') . $sessionID . $timestamp . UtilsObj::getArrayParam($this->config, 'MD5KEY1'));
		$url = UtilsObj::getArrayParam($this->config, 'SERVER');
		$endPoint = '/' . UtilsObj::getArrayParam($this->config, 'ENCODING') . '/Payment/get';

		$paramArray = array(
			'pos_id' => UtilsObj::getArrayParam($this->config, 'POSID'),
			'session_id' => $sessionID,
			'ts' =>	$timestamp,
			'sig' => $sig,
		);

		$returnedXML = $this->curlConnection->connectionSend($this->config['SERVER'], $endPoint, 'POST', $paramArray, TPX_CURL_RETRY);
		$returnedXML = str_replace("\r\n", '', $returnedXML);
		$xml = simplexml_load_string($returnedXML);

		if ($xml)
		{
			$resultArray['status'] = (string) $xml->status;
			$resultArray['id'] = (string) $xml->trans->id;
			$resultArray['pos_id'] = (string) $xml->trans->pos_id;
			$resultArray['session_id'] = (string) $xml->trans->session_id;
			$resultArray['order_id'] = (string) $xml->trans->order_id;
			$resultArray['amount'] = (string) $xml->trans->amount;
			$resultArray['status'] = (string) $xml->trans->status;
			$resultArray['pay_type'] = (string) $xml->trans->pay_type;
			$resultArray['pay_gw_name'] = (string) $xml->trans->pay_gw_name;
			$resultArray['desc'] = (string) $xml->trans->desc;
			$resultArray['desc2'] = (string) $xml->trans->desc2;
			$resultArray['create'] = (string) $xml->trans->create;
			$resultArray['init'] = (string) $xml->trans->init;
			$resultArray['sent'] = (string) $xml->trans->sent;
			$resultArray['recv'] = (string) $xml->trans->recv;
			$resultArray['cancel'] = (string) $xml->trans->cancel;
			$resultArray['auth_fraud'] = (string) $xml->trans->auth_fraud;
			$resultArray['ts'] = (string) $xml->trans->ts;
			$resultArray['sig'] = (string) $xml->trans->sig;
		}

		return $resultArray;
	}

	/**
	 *
	 * {@inheritDoc}
	 */
	public function hashString($pParams, $pType)
	{
		$returnString = '';

		switch ($pType)
		{
			case 'initialize':
			{
				$returnString = UtilsObj::getArrayParam($pParams, 'pos_id');
				$returnString .= UtilsObj::getArrayParam($pParams, 'pay_type');
				$returnString .= UtilsObj::getArrayParam($pParams, 'session_id');
				$returnString .= UtilsObj::getArrayParam($pParams, 'pos_auth_key');
				$returnString .= UtilsObj::getArrayParam($pParams, 'amount');
				$returnString .= UtilsObj::getArrayParam($pParams, 'desc');
				$returnString .= UtilsObj::getArrayParam($pParams, 'desc2');
				$returnString .= UtilsObj::getArrayParam($pParams, 'order_id');
				$returnString .= UtilsObj::getArrayParam($pParams, 'first_name');
				$returnString .= UtilsObj::getArrayParam($pParams, 'last_name');
				$returnString .= UtilsObj::getArrayParam($pParams, 'payback_login');
				$returnString .= UtilsObj::getArrayParam($pParams, 'street');
				$returnString .= UtilsObj::getArrayParam($pParams, 'street_hn');
				$returnString .= UtilsObj::getArrayParam($pParams, 'street_an');
				$returnString .= UtilsObj::getArrayParam($pParams, 'city');
				$returnString .= UtilsObj::getArrayParam($pParams, 'post_code');
				$returnString .= UtilsObj::getArrayParam($pParams, 'country');
				$returnString .= UtilsObj::getArrayParam($pParams, 'email');
				$returnString .= UtilsObj::getArrayParam($pParams, 'phone');
				$returnString .= UtilsObj::getArrayParam($pParams, 'language');
				$returnString .= UtilsObj::getArrayParam($pParams, 'client_ip');
				$returnString .= UtilsObj::getArrayParam($pParams, 'ts');
				$returnString .= UtilsObj::getArrayParam($this->config, 'MD5KEY1');

				break;
			}
			case 'getreturnedmessage':
			{
				$returnString = UtilsObj::getArrayParam($pParams, 'pos_id');
				$returnString .= UtilsObj::getArrayParam($pParams, 'session_id');
				$returnString .= UtilsObj::getArrayParam($pParams, 'order_id');
				$returnString .= UtilsObj::getArrayParam($pParams, 'status');
				$returnString .= UtilsObj::getArrayParam($pParams, 'amount');
				$returnString .= UtilsObj::getArrayParam($pParams, 'desc');
				$returnString .= UtilsObj::getArrayParam($pParams, 'ts');
				$returnString .= UtilsObj::getArrayParam($this->config, 'MD5KEY1');

				break;
			}
			case 'confirm':
			{
				$returnString = UtilsObj::getArrayParam($pParams, 'pos_id');
				$returnString .= UtilsObj::getArrayParam($pParams, 'session_id');
				$returnString .= UtilsObj::getArrayParam($pParams, 'order_id');
				$returnString .= UtilsObj::getArrayParam($pParams, 'status');
				$returnString .= UtilsObj::getArrayParam($pParams, 'amount');
				$returnString .= UtilsObj::getArrayParam($pParams, 'desc');
				$returnString .= UtilsObj::getArrayParam($pParams, 'ts');
				$returnString .= UtilsObj::getArrayParam($this->config, 'MD5KEY2');

				break;
			}
		}

		return $returnString;
	}

	/**
	 *
	 * {@inheritDoc}
	 */
	public function generateHash($pString)
	{
		return hash('md5', $pString);
	}

	/**
	 *
	 * {@inheritDoc}
	 */
	public function verifyHash($pSuppliedHash, $pParams, $pType)
	{
		$signature = $this->generateHash($this->hashString($pParams, $pType));
		return ($signature == $pSuppliedHash);
	}

	/**
	 *
	 * {@inheritDoc}
	 */
	public function confirm($pCallbackType)
	{
		// include the email creation module
		require_once('../Utils/UtilsEmail.php');

		$ref = UtilsObj::getArrayParam($this->session, 'ref');
		$id = 0;
		$authorised = false;
		$authorisedStatus = 0;
		$paymentReceived = 0;
		$amount = 0.00;
		$orderID = 0;
		$status = 999;
		$desc = '';
		$paymentMethod = '';
		$sendEmail = 0;
		$statusMessage = '';

		$smarty = SmartyObj::newSmarty('CreditCardPayment');

		// build default array to return
 	 	$resultArray = $this->cciEmptyResultArray();
 	 	$resultArray['showerror'] = false;
		$resultArray['update'] = false;

		$checkVariables = ($pCallbackType == 'automatic') ? $this->post : $this->get;

		// put return parameters into an array.
     	$transactionDetails = $this->getTransactionDetails($checkVariables);

		// check we got a response from the paymet gateway
		if (count($transactionDetails) > 0)
		{
			if ($this->verifyHash(UtilsObj::getArrayParam($transactionDetails, 'sig'), $transactionDetails, strtolower(__FUNCTION__)))
			{
				$id = UtilsObj::getArrayParam($transactionDetails, 'id');
				$amount = UtilsObj::getArrayParam($transactionDetails, 'amount');
				$orderID = UtilsObj::getArrayParam($transactionDetails, 'order_id', 0);
				$status = UtilsObj::getArrayParam($transactionDetails, 'status', 999);
				$desc = UtilsObj::getArrayParam($transactionDetails, 'desc', '');
				$paymentMethod = UtilsObj::getArrayParam($transactionDetails, 'pay_type', '');

				switch ($status)
				{
					case '1': // new
					{
						$authorised = true;
						$authorisedStatus = 1;
						break;
					}
					case '2': // cancelled
					{
						$authorised = false;
						$authorisedStatus = 2;
						$sendEmail = 1;
						$statusMessage = $smarty->get_config_vars('str_LabelTransactionRejected');
						break;
					}
					case '3': // rejected
					{
						 $authorised = false;
						 $authorisedStatus = 3;
						 $sendEmail = 1;
						 $statusMessage = $smarty->get_config_vars('str_LabelTransactionRejected');
						 break;
					}
					case '4': // started
					{
						$authorised = true;
						$authorisedStatus = 4;
						break;
					}
					case '5': // waiting
					{
						$authorised = true;
						$authorisedStatus = 5;
						break;
					}
					case '7': // payment rejected
					{
						$authorised = false;
						$authorisedStatus = 7;
						$sendEmail = 1;
						$statusMessage = $smarty->get_config_vars('str_LabelTransactionRejected');
						break;
					}
					case '99': // payment ended
					{
						$paymentReceived = 1;
						$authorised = true;
						$authorisedStatus = 99;
						$sendEmail = 1;
						$statusMessage = $smarty->get_config_vars('str_LabelTransactionAccepted');
						break;
					}
					case '888': // error
					{
						$authorised = false;
						$authorisedStatus = 888;
						$sendEmail = 1;
						$statusMessage = $smarty->get_config_vars('str_ErrorCannotProcessOrder');
						break;
					}
				}

				if (($sendEmail == 1) && (UtilsObj::getArrayParam($this->config, 'OFFLINECONFIRMATIONEMAILADDRESS') != '') && (UtilsObj::getArrayParam($this->config, 'OFFLINECONFIRMATIONNAME') !=''))
				{
					$emailContent = $smarty->get_config_vars('str_LabelTransactionID') . ': ' . $desc . ' ' . $statusMessage;
					$emailObj = new TaopixMailer();

					$emailObj->sendTemplateEmail('admin_offlinepaymentupdate', UtilsObj::getArrayParam($this->session, 'webbrandcode'), '', '', '',
						UtilsObj::getArrayParam($this->config, 'OFFLINECONFIRMATIONNAME'), UtilsObj::getArrayParam($this->config, 'OFFLINECONFIRMATIONEMAILADDRESS'), '', '',
						0,
						array('data' => $emailContent),
						false);
				}

				$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);
				$resultArray['orderid'] = UtilsObj::getArrayParam($cciLogEntry, 'orderid', 0);
				$resultArray['parentlogid'] = UtilsObj::getArrayParam($cciLogEntry, 'id', 0);

				if ($resultArray['orderid'] > 0)
				{
					// an order exists so this is an update
					$this->updateStatus = true;
					$resultArray['update'] = true;
				}
				else
				{
					// no order found so this is the first time
					$this->updateStatus = false;
					$resultArray['update'] = false;
				}

				// insert an entry in the cilog if
				if ($pCallbackType == 'manual')
				{
					$this->cciLogUpdate = true;
				}

				$returnedMessage = $this->getReturnedMessage($transactionDetails);
			}
			else
			{
				// signature does not match

				if ($pCallbackType == 'manual')
				{
					$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': 999';
					$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . SmartyObj::getParamValue('Order', 'str_OrderPlanosciBadSignature');
					$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . UtilsObj::getArrayParam($transactionDetails, 'order_id');
					$resultArray['data4'] = '';
					$resultArray['errorform'] = 'error.tpl';
					$resultArray['showerror'] = true;
				}

				$returnedMessage['code'] = 999;
				$returnedMessage['message'] = SmartyObj::getParamValue('Order', 'str_OrderPlanosciBadSignature');
			}
		}
		else
		{
			if ($pCallbackType == 'manual')
			{
				$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': 998';
				$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . SmartyObj::getParamValue('Order', 'str_OrderPlanosciInvalidResponse');
				$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . UtilsObj::getArrayParam($transactionDetails, 'order_id');
				$resultArray['data4'] = '';
				$resultArray['errorform'] = 'error.tpl';
				$resultArray['showerror'] = true;
			}

			$returnedMessage['code'] = 999;
			$returnedMessage['message'] = SmartyObj::getParamValue('Order', 'str_OrderPlanosciInvalidResponse');
		}

		$serverTimestamp = DatabaseObj::getServerTime();
		$timestamp = strtotime($serverTimestamp);
		$serverDate = date('Y-m-d', $timestamp);
		$serverTime =  date('H:i:s', $timestamp);
		PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimestamp, UtilsObj::getArrayParam($returnedMessage, 'message'), $transactionDetails);

		$resultArray['ref'] = $ref;
		$resultArray['amount'] = $amount;
		$resultArray['formattedamount'] = $amount;
		$resultArray['authorised'] = $authorised;
		$resultArray['authorisedstatus'] = $authorisedStatus;
		$resultArray['transactionid'] = $id;
		$resultArray['formattedtransactionid'] = $id;
		$resultArray['responsecode'] = $status;
		$resultArray['responsedescription'] = $desc;
		$resultArray['authorisationid'] = $orderID;
		$resultArray['formattedauthorisationid'] = $orderID;
		$resultArray['bankresponsecode'] = $status;
		$resultArray['paymentdate'] = $serverDate;
		$resultArray['paymentmeans'] = UtilsObj::getArrayParam($transactionDetails, 'pay_gw_name') . ' ' . UtilsObj::getArrayParam($transactionDetails, 'pay_type');
		$resultArray['paymenttime'] = $serverTime;
		$resultArray['paymentreceived'] = $paymentReceived;
		$resultArray['formattedpaymentdate'] = $serverTimestamp;
		$resultArray['currencycode'] = UtilsObj::getArrayParam($this->session['order'], 'currencycode');
        $resultArray['webbrandcode']  = UtilsObj::getArrayParam($this->session, 'webbrandcode');
        $resultArray['resultisarray']  = false;

		return $resultArray;
	}

	/**
	 *
	 * {@inheritDoc}
	 */
	protected function defaultCurlOptions()
	{
		return array(
			CURLOPT_HEADER => 0,
			CURLOPT_TIMEOUT => 20,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_SSL_VERIFYPEER => 1,
			CURLOPT_CAINFO => UtilsObj::getCurlPEMFilePath()
		);
	}

	/**
	 *  close the connection to curl
	 */
	public function __destruct()
	{
		$this->curlConnection->connectionClose();
	}

	/**
	 * Array of CSP Rules and domains for platnosci
	 * @return array
	 */
	public function getCSPDetails()
	{
		$serverInfo = parse_url($this->config['SERVER']);

		return [
			'script-src' => [
				$serverInfo['scheme'] . '://' . $serverInfo['host'],
				'https://static.payu.com',
			],
			'style-src' => [
				'https://static.payu.com',
			],
		];
	}
}
?>