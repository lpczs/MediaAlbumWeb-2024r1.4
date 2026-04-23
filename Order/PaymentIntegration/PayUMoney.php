<?php

require_once __DIR__ . '/TaopixAbstractGateway.php';

class PayUMoney extends TaopixAbstractGateway
{
	public function configure()
	{
		$resultArray = $this->getConfigureDefaultResultArray();

		$currencyList = $this->config['CURRENCIES'];
		$currency = $this->session['order']['currencycode'];
		$script = '';
		$scriptURL = '';

		if (strpos($currencyList, $currency) === false)
		{
			$resultArray['active'] = false;
		}

		if ($this->config['TESTMODE'] == 0)
		{
			$scriptURL = $this->config['LIVEURL'];
		}
		else
		{
			$scriptURL = $this->config['SANDBOXURL'];
		}


		$smarty = SmartyObj::newSmarty('CreditCardPayment', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

		if ($this->session['ismobile'])
        {
			$smarty->assign("boltUrl", $scriptURL);
			$scriptURL = '';
			$script = $smarty->fetchLocale('order/PaymentIntegration/PayUMoney/PayUMoney_small.tpl');
		}
		else
		{
			// only set attributes on large screen as the way we assign the script url means we need to assign these via javascript on small screen
			$scriptURL .= '"bolt-color="e34524" bolt-logo="" id="bolt';
			$script = $smarty->fetchLocale('order/PaymentIntegration/PayUMoney/PayUMoney_large.tpl');
		}

		$resultArray['script'] = $script;
		$resultArray['scripturl'] = $scriptURL;
		$resultArray['requestpaymentparamsremotely'] = true;

		// If CSP is active add the directives specified for the gateway.
		if ($this->cspBuilder !== null)
		{
			$this->addCSPDetails();
		}
		
		return $resultArray;
	}

	public function initialize()
	{

		$fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl']);
		$manualCallBackUrl = $fixedUrlPath . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'];
		$cancelCallBackUrl = $fixedUrlPath . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];
		//Transaction ID can be at max 30 characters long so only take the first 30 characters
		$txnID = substr(($this->session['ref'] . 'a' . rand()), 0, 30);
		
		$paymentParamArray = [
			'key' => $this->config['MERCHANTKEY'],
			'txnid' => $txnID,
			'amount' => $this->session['order']['ordertotaltopay'],
			'productinfo' => LocalizationObj::getLocaleString($this->session['items'][0]['itemproductname'], $this->session['browserlanguagecode'], true),
			'firstname' => $this->session['order']['billingcontactfirstname'],
			'lastname' => $this->session['order']['billingcontactlastname'],
			'email' => $this->session['order']['billingcustomeremailaddress'],
			'phone' => $this->session['order']['billingcustomertelephonenumber'],
			'udf1' => '',
			'udf2' => '',
			'udf3' => '',
			'udf4' => '',
			'udf5' => '',
			'surl' => $manualCallBackUrl,
			'furl' => $cancelCallBackUrl,
			'address1' => $this->session['order']['billingcustomeraddress1'],
			'address2' => $this->session['order']['billingcustomeraddress2'],
			'city' => $this->session['order']['billingcustomercity'],
			'country' => $this->session['order']['billingcustomercountrycode'],
			'mode' => 'dropout'
		];

		$hash = $this->hashString($paymentParamArray, 'sha512');
		$paymentParamArray['hash'] = $hash;
		$paymentParamJson = json_encode($paymentParamArray);

		return $paymentParamJson;
	}

	public function confirm($pCallbackType)
	{
		$resultArray = $this->cciEmptyResultArray();
		$resultArray['showerror'] = false;

		// Get the ref from values that are passed or the session.
		$cciRef = array_key_exists('ref', $this->get) ? $this->get['ref'] : $this->session['ref'];

		// Check for a cciEntry for this ref.
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

			if ($this->post['txnStatus'] === "SUCCESS")
			{
				//payment has succeeded
				//we need to validate that the remainder of the response is valid
				if ($this->verifyHash($this->post['hash'], $this->post, 'sha512'))
				{
					$resultArray['authorised'] = 1;
					$resultArray['authorisedstatus'] = 1;
					$resultArray['paymentreceived'] = 1;
					$resultArray['transactionid'] = $this->post['txnid'];
					$resultArray['amount'] = $this->post['amount'];
					$resultArray['responsecode'] = $this->post['status'];
					$resultArray['responsedescription'] = $this->post['txnMessage'];
					$resultArray['authorisationid'] = $this->post['payuMoneyId'];
					$resultArray['bankresponsecode'] = $this->post['bank_ref_num'];
					$resultArray['paymentdate'] = $this->post['addedon'];
					$resultArray['paymenttime'] = date('H:i:s');
					$resultArray['paymentmeans'] = $this->post['mode'];
					$resultArray['payeremail'] = $this->post['email'];
					$resultArray['receiverid'] = $this->post['key'];
					$resultArray['formattedpaymentdate'] = $this->post['addedon'];
					$resultArray['formattedtransactionid'] = $this->post['txnid'];
					$resultArray['formattedauthorisationid'] = $this->post['payuMoneyId'];
					$resultArray['formattedamount'] = $this->post['amount'];
				}
				else 
				{
					$resultArray['showerror'] = true;
					$resultArray['authorised'] = 0;
					$resultArray['authorisedstatus'] = false;
					$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $this->post['error'];
					$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' .  SmartyObj::getParamValue('CreditCardPayment', 'str_OrderAdyenSignatureFailed');
					$resultArray['data3'] = '';
					$resultArray['data4'] = '';
					$resultArray['errorform'] = 'error.tpl';
				}
			}
			else
			{
				//payment has failed or returned an invalid status code, we need to display an error
				$resultArray['showerror'] = true;
				$resultArray['authorised'] = 0;
				$resultArray['authorisedstatus'] = false;
				$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $this->post['error'];
				$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $this->post['error_Message'];
				$resultArray['data3'] = '';
				$resultArray['data4'] = '';
				$resultArray['errorform'] = 'error.tpl';
			}
		}
		else
		{
			for ($i = 0; $i < 10; $i++) 
			{ 
				// the gateway will return double manual callbacks at random
				// in this scenario we want to be sure that the order number has been generated and inserted into the database before we continue
				// otherwise the user will be displayed a blank order number
				// we don't wait long as we are waiting for TAOPIX to process the first callback not for the gateway
				if (empty($cciEntry['ordernumber']))
				{
					usleep(500000);
					$cciEntry = PaymentIntegrationObj::getCciLogEntry($cciRef);
				}
				else
				{
					// ordernumber exists, exit the loop
					break;
				}
			}

			// the user has refreshed the final page, we just want to return the pre-existing data
			$resultArray = $cciEntry;
			$resultArray['ref'] = $cciRef;
			$resultArray['showerror'] = false;
			$resultArray['update'] = true;
			$this->updateStatus = true;
		}

		PaymentIntegrationObj::logPaymentGatewayData($this->config, DatabaseObj::getServerTime(), '', $resultArray);

		return $resultArray;
	}

	public function verifyHash($pSuppliedHash, $pParams, $pType)
	{
		$isHashValid = false;
		$hash = "";

		//the params are in reverse order on the response to the request
		$stringToHash = $this->config['SALT'] . "|" . $pParams['status'] . "|||||||||||" . $pParams['email'] . "|" . $pParams['firstname'];
		$stringToHash .= "|" . $pParams['productinfo'] . "|" . $pParams['amount'] . "|" . $pParams['txnid'] . '|' .  $pParams['key'];

		$hash = $this->generateHash($stringToHash);
		$isHashValid = $hash === $pSuppliedHash;

		return $isHashValid;
	}
	
	public function hashString($pParams, $pType)
	{
		$endPipes = "|||||||||||";

		$stringToHash = $pParams['key'] . "|" . $pParams['txnid'] . "|" . $pParams['amount'] . "|" . $pParams['productinfo'] . "|" . $pParams['firstname'];
		$stringToHash .= "|" . $pParams['email'] . $endPipes . $this->config['SALT'];

		return $this->generateHash($stringToHash);
	}

	public function generateHash($pString)
	{
		return hash("sha512", $pString);
	}

	public function getCSPDetails()
	{
		$prefix = $this->config['TESTMODE'] ? 'SANDBOX' : 'LIVE';

		$urlInfo = parse_url($this->config[$prefix . 'URL']);

		return [
			'script-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host']
			],
			'connect-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'],
				$urlInfo['scheme'] . '://' . (str_replace('-static', '', $urlInfo['host'])),
			],
			'frame-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'],
				$urlInfo['scheme'] . '://' . (str_replace('-static', '', $urlInfo['host'])),
			],
			'image-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'],
				$urlInfo['scheme'] . '://' . (str_replace('-static', '', $urlInfo['host'])),
			],
		];
	}
}