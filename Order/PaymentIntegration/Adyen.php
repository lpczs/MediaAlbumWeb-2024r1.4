<?php

require_once 'TaopixAbstractGateway.php';

class Adyen extends TaopixAbstractGateway
{

    public function configure()
    {
        $resultArray = $this->getConfigureDefaultResultArray();

        AuthenticateObj::clearSessionCCICookie();

		// Make sure the active currency is supported by the payment gateway.
        if (strpos($this->config['CURRENCYLIST'], $this->session['order']['currencycode']) === false)
        {
            $resultArray['active'] = false;
        }

        return $resultArray;
    }

    public function initialize()
    {
        $smarty = SmartyObj::newSmarty('Order', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

        // First check if we have any ccidata. this is set when the call is made the first time.
        // If the data is set then the user must have hit the back button on their browser
        if ($this->session['order']['ccidata'] == '')
        {
            $server = $this->config['ADYENSERVER'];
            $merchant = $this->config['MERCHANTID'];
            $skinCode = $this->config['ADYENSKIN'];
            $shipBeforeDays = $this->config['SHIPBEFOREDATE'];
            $sessionId = $this->session['ref'];
            $sessionValidity = date(DATE_ATOM, mktime(date("H") + 1, date("i"), date("s"), date("m"), date("j"), date("Y"))); // example: shopper has one hour to complete
            $shipBeforeDate = date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("m"), date("j") + $shipBeforeDays, date("Y"))); // example: ship in 5 days
			
            // Adyen requires order text to be GZIP compressed and base64 encoded.
			$orderDataRaw = '';
			$countItems = count($this->session['items']);

			for ($i = 0; $i < $countItems; $i++)
			{
				$orderDataRaw = $this->session['items'][0]['itemqty'] . ' x ' .
					LocalizationObj::getLocaleString($this->session['items'][0]['itemproductname'], $this->session['browserlanguagecode'], true) . '<br />';
			}
            $orderData = base64_encode(gzencode($orderDataRaw));
			
			if ($this->session['order']['currencycode'] == 'IDR')
			{
            	$amount = $this->session['order']['ordertotaltopay'];
			}
			else
			{
				// Amount in smallest unit, e.g. pence or cents.
            	$amount = number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '', '');
			}
            
            $currency = $this->session['order']['currencycode'];

            // Get language code.
            $locale = strtolower($this->session['browserlanguagecode']);
            $locale = substr($locale, 0, 2);

			// Force English to be GB English.
            if ($locale == 'en')
            {
                $locale = 'en_GB';
            }

			$manualReturnPath =  UtilsObj::correctPath($this->session['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'];

            // All of the payment parameters are passed.
			$parameters = array(
                'currencyCode' => $currency,
                'merchantAccount' => $merchant,
                'merchantReference' => $sessionId,
                'merchantReturnData' => $sessionId,
                'orderData' => $orderData,
                'paymentAmount' => $amount,
                'resURL' => $manualReturnPath,
                'sessionValidity' => $sessionValidity,
                'shipBeforeDate' => $shipBeforeDate,
                'shopperLocale' => $locale,
                'skinCode' => $skinCode
            );

			// Add the hash to the request.
			$hasString = $this->hashString($parameters, 'call');
			$parameters['merchantSig'] = $this->generateHash($hasString);

            // Define Smarty variables.
            $smarty->assign('payment_url', $server);
			$smarty->assign('method', 'POST');
			$smarty->assign('parameter', $parameters);

            AuthenticateObj::defineSessionCCICookie();
            $smarty->assign('ccicookiename', 'mawebcci' . $this->session['ref']);
            $smarty->assign('ccicookievalue', $this->session['order']['ccicookie']);

            // Set the ccidata to remember we have jumped to DIBS.
            $this->session['order']['ccidata'] = 'start';
            DatabaseObj::updateSession();

			// Allow the page to be cached so that the browser back button works correctly.
            $smarty->cachePage = true; 
        }
        else
        {
            // The user has clicked the back button so we force the server URL to be the cancel URL to go back to the cart.
            AuthenticateObj::clearSessionCCICookie();

			$cancelReturnPath = UtilsObj::correctPath($this->session['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];
            $smarty->assign('server', $cancelReturnPath);
        }

		// Load the correct template.
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

    public function confirm($pCallBack)
    {
		$resultArray = $this->cciEmptyResultArray();
		$resultArray['showerror'] = false;
        $authorisedStatus = 0;
        $authorised = false;
        $update = false;
        $paymentReceived = 0;
        $eventCode = '';
        $pspReference = '';
        $merchantReference = '';
        $eventDate = '';
        $success = '';
        $authResult = '';
        $returnMerchantSig = '';
        $orderID = 0;
        $logID = 0;
        $userID = 0;

        // Get the transaction reference number.
        if ($pCallBack == 'automatic')
        {
            $pspReference = UtilsObj::getPOSTParam('pspReference');
        }
        else
        {
            $pspReference = UtilsObj::getGETParam('pspReference');
        }

		// Make sure a transaction ref has been passed by paramater.
		$cciRef = ($pspReference != '') ? $pspReference : $this->session['ref'];

		// Get active CCI record.
		$cciEntry = PaymentIntegrationObj::getCciLogEntry($cciRef);

		if ($cciEntry === [])
		{
			// Empty first callback.
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
			// Additional callback.
			$resultArray['webbrandcode'] = $cciEntry['webbrandcode'];
			$resultArray['currencycode'] = $cciEntry['currencycode'];
			$resultArray['amount'] = $cciEntry['formattedamount'];
			$resultArray['parentlogid'] = $cciEntry['id'];
			$resultArray['orderid'] = $cciEntry['orderid'];
			$resultArray['update'] = true;
			$this->updateStatus = (($pCallBack === 'manual') ? false : true);
		}

        // Write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
		PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimestamp);

        // Automatic call back has occurred
        if ($pCallBack == 'automatic')
        {
            // intialise variables
            $eventCode = UtilsObj::getPOSTParam('eventCode');
            $merchantReference = UtilsObj::getPOSTParam('merchantReference');
            $eventDate = UtilsObj::getPOSTParam('eventDate');
            $success = UtilsObj::getPOSTParam('success');

            // check to see if the payment was authorised successfully
            if (($eventCode == 'AUTHORISATION') && ($success == 'true'))
            {
                $authorised = true;
                $authorisedStatus = 1;
            }
            else
            {
                $authorised = false;
                $authorisedStatus = 0;
            }

            // notify adyen that we have recieved the notification
            echo "[accepted]";
        }
        else
        {
			// Manual callback has occurred.
            $authResult = UtilsObj::getGETParam('authResult');
            $merchantReference = UtilsObj::getGETParam('merchantReference');
			$eventDate = date('Y-m-d H:i:s');

            // check to see what the result of the payment was or handle a cancel
            switch ($authResult)
            {
                case 'AUTHORISED':
				{
					// If the payment was authorised check the return signature.
                    $authorised = true;
                    $authorisedStatus = 2;

					// Create an array to generate the hash to compare to.
					$hashParams = array();
					$listOfParams = array('authResult', 'merchantReference', 'merchantReturnData', 'paymentMethod', 'pspReference', 'reason', 'shopperLocale', 'skinCode');
					$countListOfParams = count($listOfParams);

					// Generate an array from sever parameters repecting the list bellow.
					for ($i = 0; $i < $countListOfParams; $i++)
					{
						$key = $listOfParams[$i];

						// Make sure the value has been returned.
						$value = UtilsObj::getGETParam($key, null);

						if ($value != null)
						{
							$hashParams[$key] = $value;
						}
					}

					// Get the hash from the server.
					$returnMerchantSig = UtilsObj::getGETParam('merchantSig');

                    // Check the hash is correct returned.
					if (! $this->verifyHash($returnMerchantSig, $hashParams, 'response'))
                    {
						$authorised = false;
                        $this->showError($resultArray, 'str_OrderAdyenSignatureFailed', $pspReference, $merchantReference);
                    }
                    break;
				}
                case 'REFUSED':
				{
                    $authorised = false;
                    $authorisedStatus = 3;
					$this->showError($resultArray, 'str_OrderAdyenPaymentRefused', $pspReference, $merchantReference);
                    break;
				}
                case 'CANCELLED':
				{
					// The user has cancelled the payment process.
                    $authorised = false;
                    break;
				}
                case 'PENDING':
				{
					// Payment has been authorised however payment has not been captured.
                    $authorised = true;
                    $authorisedStatus = 4;
                    break;
				}
                case 'ERROR':
				{
					// There was an error whilst processing the payment.
                    $authorised = false;
                    $authorisedStatus = 5;
					$this->showError($resultArray, 'str_OrderAdyenTransactionFailed', $pspReference, $merchantReference);
                    break;
				}
            }
        }

		// Flag the payment has received for the correct status.
        if (($authorisedStatus == 1) || ($authorisedStatus == 2))
        {
            $paymentReceived = 1;
        }

        $resultArray['result'] = $eventCode;
        $resultArray['ref'] = $cciRef;
        $resultArray['amount'] = $this->session['order']['ordertotaltopay'];
        $resultArray['formattedamount'] = $this->session['order']['ordertotaltopay'];
        $resultArray['authorised'] = $authorised;
        $resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $pspReference;
        $resultArray['formattedtransactionid'] = $pspReference;
        $resultArray['responsecode'] = $eventCode;
        $resultArray['responsedescription'] = $eventCode;
        $resultArray['authorisationid'] = $merchantReference;
        $resultArray['formattedauthorisationid'] = $merchantReference;
        $resultArray['bankresponsecode'] = $pspReference;
        $resultArray['paymentcertificate'] = $pspReference;
        $resultArray['paymentdate'] = date('Y-m-d', strtotime($eventDate));
        $resultArray['paymenttime'] = date('H:i:s', strtotime($eventDate));
        $resultArray['paymentreceived'] = $paymentReceived;
        $resultArray['formattedpaymentdate'] = $eventDate;
        $resultArray['business'] = $this->config['MERCHANTID'];
        $resultArray['currencycode'] = $this->session['order']['currencycode'];
        $resultArray['webbrandcode'] = $this->session['webbrandcode'];
        $resultArray['orderid'] = $orderID;
        $resultArray['parentlogid'] = $logID;
        $resultArray['userid'] = $userID;
        $resultArray['resultisarray'] = false;
        $resultArray['update'] = $update;

        return $resultArray;
    }

	public function generateHash($pHashString)
    {
		$binaryHmacKey = pack("H*",  $this->config['ADYENHMACKEY']);
		return base64_encode(hash_hmac('sha256', $pHashString, $binaryHmacKey, true));
    }

    /**
	 * Format a string to be used to generate a hash.
	 *
	 * @param string $pType Type of call for which the hash is required.
	 */
	public function hashString($pParams, $pType)
	{
		$hashStringArray = $pParams;

		// Characters need to be escaped on the call to the payment gateway.
		if ($pType == 'call')
		{
			ksort($pParams, SORT_STRING);

			$escapedPairs = array();
			foreach ($pParams as $key => $value)
			{
				$escapedPairs[$key] = str_replace(':','\\:', str_replace('\\', '\\\\', $value));
			}

			$hashStringArray = $escapedPairs;
		}

		$signingString = implode(":", array_merge(array_keys($hashStringArray), array_values($hashStringArray)));

		return $signingString;
	}

	/**
	 *
	 * @param type $pSuppliedHash
	 * @param type $pParams
	 * @param type $pType
	 * @return type
	 */
    public function verifyHash($pSuppliedHash, $pParams, $pType)
	{
		$hashString = $this->hashString($pParams, $pType);
		return ($this->generateHash($hashString) === $pSuppliedHash) ? true : false;
	}

	public function showError(&$resultArray, $pKey, $pspReference, $merchantReference)
	{
		$smarty = SmartyObj::newSmarty('CreditCardPayment');
		$error = $smarty->get_config_vars($pKey);

		if (! array_key_exists('data1', $resultArray))
		{
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': Payment Error';
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $error;
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $pspReference;
			$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $merchantReference;
		}
		$resultArray['errorform'] = 'error.tpl';
		$resultArray['showerror'] = true;
	}
}
?>
