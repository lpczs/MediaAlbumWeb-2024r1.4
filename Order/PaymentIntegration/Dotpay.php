<?php

require_once 'TaopixAbstractGateway.php';

/**
 * Dotpay payment gateway integration
 *
 */
class Dotpay extends TaopixAbstractGateway
{
	/**
	 * Configure payment gateway.
	 *
	 * @return array
	 */
	public function configure()
	{
		$resultArray = [
			'active' => false,
			'form' => '',
			'scripturl' => '',
			'script' => '',
			'action' => '',
			'gateways' => []
		];

		AuthenticateObj::clearSessionCCICookie();

        // test for supported currencies
		$acceptedCurrencies = [
			'PLN' => 985,
			'EUR' => 978,
			'USD' => 840,
			'GBP' => 826,
			'JPY' => 392];

		$currency = $this->session['order']['currencyisonumber'];

		$active = in_array($currency, $acceptedCurrencies);

        $resultArray['active'] = ($active && PaymentIntegrationObj::checkSSL()) ? true : false;

		return $resultArray;
	}


	/**
	 * Initialize the payment gateway.
	 *
	 * @return array
	 */
	public function initialize()
    {
        $smarty = SmartyObj::newSmarty('Order', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

        // first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($this->session['order']['ccidata'] == '')
        {
            $URLPath = UtilsObj::correctPath($this->session['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'];
            $automaticReturnPath = UtilsObj::correctPath($this->session['webbrandweburl']) . '?fsaction=Order.ccAutomaticCallback&ref=' . $this->session['ref'];

            $server = $this->config['DPSERVER'];
            $merchantId = $this->config['DPMERCHANTID'];

            $returnButton = $this->config['DPRETURNBUTTONTEXT'];
            $returnButton = UtilsObj::encodeString($returnButton);

            $presentationInfo = $this->config['DPPRESENTATIONINFO'];
            $presentationInfo = UtilsObj::encodeString($presentationInfo);

            $sellersEmail = $this->config['DPSELLERSEMAIL'];

            // amount in smallest unit, e.g. pence or cents
            $amount = number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '.', '');

            // build transaction id
            $control = $this->session['ref'] . '_' . time();

            $currency = $this->session['order']['currencycode'];
            $description = $this->session['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($this->session['items'][0]['itemproductname'],
                            $this->session['browserlanguagecode'], true);

            // get language code and see if it is supported by Dotpay
            $locale = strtolower($this->session['browserlanguagecode']);
            $locale = substr($locale, 0, 2);

            $languageList = 'pl,en,de,it,fr,es,cz,ru';
            if (strpos($languageList, $locale) === false)
            {
                $displayLang = 'pl';
            }
            else
            {
                $displayLang = $locale;
            }

            $forename = $this->session['order']['billingcontactfirstname'];
            $surname = $this->session['order']['billingcontactlastname'];
            $email = $this->session['order']['billingcustomeremailaddress'];
            $billingAddress1 = $this->session['order']['billingcustomeraddress1'];
            $billingAddress2 = $this->session['order']['billingcustomeraddress2'];
            $billingAddress3 = $this->session['order']['billingcustomeraddress3'];
            $billingCity = $this->session['order']['billingcustomercity'];
            $billingPostCode = $this->session['order']['billingcustomerpostcode'];
            $billingcustomercountrycode = $this->session['order']['billingcustomercountrycode'];
            $billingtelephonenumber = $this->session['order']['billingcustomertelephonenumber'];

            $parameters = array(
                'api_version' => 'dev',
                'URLC' => $automaticReturnPath,
                'id' => $merchantId,
                'amount' => $amount,
                'description' => $description,
                'lang' => $displayLang,
                'currency' => $currency,
                'control' => $control,
                'URL' => $URLPath,
                'buttontext' => $returnButton,
                'p_info' => $presentationInfo,
                'forename' => $forename,
                'surname' => $surname,
                'email' => $email,
                'street' => $billingAddress1,
                'addr2' => $billingAddress2,
                'addr3' => $billingAddress3,
                'city' => $billingCity,
                'postcode' => $billingPostCode,
                'phone' => $billingtelephonenumber,
                'country' => $billingcustomercountrycode,
                'p_email' => $sellersEmail,
                'onlinetransfer' => 0,
                'ch_lock' => 0,
                'channel' => 0,
                'type' => 3
            );

            // define Smarty variables
            $smarty->assign('payment_url', $server);
            $smarty->assign('method', 'POST');
			$smarty->assign('parameter', $parameters);

            AuthenticateObj::defineSessionCCICookie();
            $smarty->assign('ccicookiename', 'mawebcci' . $this->session['ref']);
            $smarty->assign('ccicookievalue', $this->session['order']['ccicookie']);

            // set the ccidata to remember we have jumped to Dotpay
            $this->session['order']['ccidata'] = 'start';
            DatabaseObj::updateSession();

            $smarty->cachePage = true; // allow the page to be cached so that the browser back button works correctly
        }
        else
        {
            // the user has clicked the back button
            AuthenticateObj::clearSessionCCICookie();

            $cancelReturnPath = UtilsObj::correctPath($this->session['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];
            $smarty->assign('payment_url', $cancelReturnPath);
			$smarty->assign('method', 'POST');
            $smarty->assign('cancel_url', $cancelReturnPath);
        }


		if ($this->session['ismobile'] == true)
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


	/**
	 * The payment request has been cancelled.
	 *
	 * @return boolean
	 */
    public function cancel()
    {
		$this->cciLogUpdate = false;

		$resultArray = [
			'result' => '',
			'ref' => UtilsObj::getGETParam('ref', ''),
			'transactionid' => '',
			'authorised' => false,
			'showerror' => false
		];

        return $resultArray;
    }


	/**
	 * Process the data returned from the payment gateway
	 *
	 * @param type $pCallbackType
	 */
	public function confirm($pCallbackType)
	{
		$ref = $this->session['ref'];

		if ('automatic' == $pCallbackType)
		{
			$resultArray = $this->automaticCallback($ref);
		}
		else
		{
			$resultArray = $this->manualCallback($ref);
		}

		return $resultArray;
	}


	/**
	 * process the manual callback
	 *
	 * @param type $pRef
	 */
	protected function manualCallback($pRef)
	{
		$resultArray = PaymentIntegrationObj::getCciLogEntry($pRef);
		$resultArray['ref'] = $pRef;
		$resultArray['result'] = '';

		// use the data from the automatic callback to display the order status to the user
		if ($resultArray['authorised'])
		{
			// payment was OK
			$resultArray['showerror'] = false;
		}
		else
		{
			// payment error
			if ($resultArray['responsecode'] == '')
			{
				// if the gateway has returned with no response, use the transaction failed message
				$resultArray['responsecode'] = 'ABORT';
				$resultArray['responsedescription'] = 'Transaction failed.';
			}

			$resultArray['authorisedstatus'] = 0;
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $resultArray['responsecode'];
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $resultArray['responsedescription'];
			if ($resultArray['transactionid'] != '')
			{
				$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $resultArray['transactionid'];
			}
			else
			{
				$resultArray['data3'] = '';
			}
			$resultArray['data4'] = '';
			$resultArray['errorform'] = 'error.tpl';
			$resultArray['showerror'] = true;
		}

		return $resultArray;
	}


	/**
	 * process the automatic callback
	 *
	 * @param type $pRef
	 */
	protected function automaticCallback($pRef)
	{
		$resultArray = $this->cciEmptyResultArray();

		$error = '';
		$authorisedStatus = '0';
		$authorised = false;
		$showError = false;
		$paymentReceived = 0;

		$serverTimestamp = DatabaseObj::getServerTime();
		$timestampParts = explode(" ", $serverTimestamp);
		$serverDate = $timestampParts[0];
		$serverTime = $timestampParts[1];

		$sellersId = UtilsObj::getPOSTParam('id');
		$status = UtilsObj::getPOSTParam('operation_status');
		$transactionStatus = $status;
		$transactionID = UtilsObj::getPOSTParam('operation_number');
		$amount = UtilsObj::getPOSTParam('operation_amount');
		$opOrAmount = UtilsObj::getPOSTParam('operation_original_amount');
		$email = UtilsObj::getPOSTParam('email');
		$opEmail = UtilsObj::getPOSTParam('p_email');
		$t_date = UtilsObj::getPOSTParam('operation_datetime');
		$channel = UtilsObj::getPOSTParam('channel');
		$sha = UtilsObj::getPOSTParam('signature');
		$control = UtilsObj::getPOSTParam('control');
		$opType = UtilsObj::getPOSTParam('operation_type');
		$opCurr = UtilsObj::getPOSTParam('operation_currency');
		$opOrCurr = UtilsObj::getPOSTParam('operation_original_currency');
		$opDesc = UtilsObj::getPOSTParam('description');
		$opInfo = UtilsObj::getPOSTParam('p_info');

		$processed = 0;

		//Check if the transaction has gone through ok.
		if ($transactionStatus == "completed")
		{
			$authorised = false;
			$authorisedStatus = 0;

			// do some comparisons
			if ($sellersId != $this->config['DPMERCHANTID'])
			{
				$error = 'Merchant ID mismatch.';
				$authorisedStatus = 2;
			}

			// calculate sha signature
			$signatureParams = [
				$this->config['DPSHAPIN'],
				$sellersId,
				$transactionID,
				$opType,
				$status,
				$amount,
				$opCurr,
				$opOrAmount,
				$opOrCurr,
				$t_date,
				$control,
				$opDesc,
				$email,
				$opInfo,
				$opEmail,
				$channel];

			$verifySignatureString = $this->hashString($signatureParams, 'automatic');

			if (! $this->verifyHash($sha, $verifySignatureString, 'automatic'))
			{
				$error = 'Signature check failed.';
				$authorisedStatus = 3;
			}
			else
			{
				$authorised = true;
				$authorisedStatus = 1;
				$processed = 1;
				$paymentReceived = 1;
			}
		}
		else
		{
			$error = 'Transaction failed.';
			$authorisedStatus = 0;
		}

		// write to log file.
		PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimestamp, $error);

		$originalAmount = number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '.', '');
		$ipAddress = $this->session['order']['useripaddress'];
		$currency = $this->session['order']['currencycode'];

		$this->cciLogUpdate = false;

		$resultArray['processed'] = $processed;
		$resultArray['authorised'] = $authorised;
		$resultArray['authorisedstatus'] = $authorisedStatus;
		$resultArray['result'] = $status;
		$resultArray['ref'] = $pRef;
		$resultArray['amount'] = $originalAmount;
		$resultArray['formattedamount'] = $originalAmount;
		$resultArray['charges'] = '0.00';
		$resultArray['formattedcharges'] = 0.00;
		$resultArray['paymentdate'] = $serverDate;
		$resultArray['paymenttime'] = $serverTime;
		$resultArray['authorisationid'] = $control;
		$resultArray['transactionid'] = $transactionID;
		$resultArray['paymentmeans'] = $channel;
		$resultArray['addressstatus'] = $t_date;
		$resultArray['payerid'] = $ipAddress;
		$resultArray['payerstatus'] = $status;
		$resultArray['payeremail'] = $email;
		$resultArray['currencycode'] = $currency;
		$resultArray['webbrandcode'] = $this->session['webbrandcode'];
		$resultArray['settleamount'] = $amount; // this will be in PLN
		$resultArray['paymentreceived'] = $paymentReceived;
		$resultArray['formattedpaymentdate'] = $serverTimestamp;
		$resultArray['formattedtransactionid'] = $control;
		$resultArray['responsecode'] = $transactionStatus;
		$resultArray['bankresponsecode'] = $transactionStatus;
		$resultArray['update'] = false;
		$resultArray['orderid'] = 0;
		$resultArray['parentlogid'] = 0;
		$resultArray['responsedescription'] = $error;
		$resultArray['showerror'] = $showError;
		$resultArray['resultisarray'] = false;
		$resultArray['resultlist'] = Array();
		$resultArray['acknowledgement'] = 'OK';

		return $resultArray;
	}




	/**
	 * Create the string used to generate the signature
	 *
	 * @param array $pParams The collection of values used to generate the string to be used to sign the transaction
	 * @param string $pType The signature being generated, either sending of data or verifying received data
	 *
	 * @return string
	 */
	public function hashString($pParams, $pType)
	{
		$hashString = join('', $pParams);

		return $hashString;
	}


	/**
	 * Generate the signature required for the iPay88 payment gateway
	 *
	 * @param string $pSource The string used to generate the signature
	 *
	 * @return string Signature used to sign the transaction
	 */
	public function generateHash($pSource)
	{
		$hash = hash('sha256', $pSource);

		return $hash;
	}


	/**
	 * Make sure the signature sent from the payment gateway is correct.
	 *
	 * @param string $pSuppliedHash The signature from the gateway.
	 * @param string $pVerifyString The string which was used to create the signature.
	 * @param string $pType The signature being generated, either sending of data or verifying received data
	 *
	 * @return boolean Does the signature sent match that generate using the data
	 */
	public function verifyHash($pSuppliedHash, $pVerifyString, $pType)
	{
		$signature = $this->generateHash($pVerifyString);
		return ($signature == $pSuppliedHash);
	}
}

?>