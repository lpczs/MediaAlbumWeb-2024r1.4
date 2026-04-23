<?php

require_once 'TaopixAbstractGateway.php';

use Maksekeskus\Maksekeskus;

class MakeCommerce extends TaopixAbstractGateway
{
    protected $keySuffix = '';
    protected $makeCommerceObj;

	/**
	 * Default constructor
	 */
    public function __construct($pConfig, &$pSession, &$pGetVars, &$pPostVars)
    {
        parent::__construct($pConfig, $pSession, $pGetVars, $pPostVars);
        $this->keySuffix = ($this->config['TRANSACTIONMODE'] === 'TEST') ? 'TEST' : '';

        // Set up the MakeCommerce object prior to connection.
        $shopID = $this->config['MERCHANTID' . $this->keySuffix];
        $KeyPublishable = $this->config['PUBLISHABLEKEY' . $this->keySuffix];
        $KeySecret = $this->config['SECRETKEY' . $this->keySuffix];

        $this->makeCommerceObj = new Maksekeskus($shopID, $KeyPublishable, $KeySecret, ('TEST' === $this->keySuffix));
    }

	/**
	 * Returns the config array for the gateway.
	 * @return array
	 */
	public function configure()
	{
        $resultArray = $this->getConfigureDefaultResultArray();

        AuthenticateObj::clearSessionCCICookie();

        // If the transactionmode is set to test config keys will be appended with test.
        // Accepted currencies are not dependant on which transaction mode we are using.
        if (($this->config['MERCHANTID' . $this->keySuffix] == '') ||
            ($this->config['SECRETKEY' . $this->keySuffix] == '') ||
            ($this->config['PUBLISHABLEKEY' . $this->keySuffix] == '') ||
            (strpos($this->config['MCCURRENCIES'], $this->session['order']['currencycode']) === false))
        {
            $resultArray['active'] = false;
        }

		return $resultArray;
	}

	/**
	 * Gets the locale for this request, in most instances this will be from the browser language code.
	 * There are instances where the locale passed in the session is not what it seams, this is when
	 * a licensee has changed a language to be a none supported taopix language.
	 *
	 * @return string Locale for this request.
	 */
	private function getLocale()
	{
		$returnLocale = '';
		$localeMap = [];

		// If we have remaps for the locale generate the map for them.
		if ($this->config['REMAPLOCALE'] !== '')
		{
			// Split based on | to get each locale that has been remapped.
			$localeMapItems = explode('|', $this->config['REMAPLOCALE']);

			// For each of the mapped items, generate the map.
			foreach ($localeMapItems as $key => $details)
			{
				$mapDetails = explode(',', $details);
				$localeMap[$mapDetails[0]] = $mapDetails[1];
			}
		}

		if (array_key_exists($this->session['browserlanguagecode'], $localeMap))
		{
			$returnLocale = $localeMap[$this->session['browserlanguagecode']];
		}
		else
		{
			$browserLang = substr($this->session['browserlanguagecode'], 0, 2);

			if (array_key_exists($browserLang, $localeMap))
			{
				$returnLocale = $localeMap[$browserLang];
			}
			else
			{
				$returnLocale = $browserLang;
			}
		}

		return $returnLocale;
	}

	/**
	 * MakeCommerce Initialize
	 * {@inheritDoc}
	 */
	public function initialize()
	{
		$smarty = SmartyObj::newSmarty('CreditCardPayment', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

        // Generate the return urls for the gateway.
		$fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl']);
		$cancelReturnPath = $fixedUrlPath . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];
        $autoReturnPath = $fixedUrlPath . '?fsaction=Order.ccAutomaticCallback&ref=' . $this->session['ref'];
        $manualReturnPath = $fixedUrlPath . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'];

		// Get the locale for this payment.
		$locale = $this->getLocale();

		// First check if we have any ccidata. This is set when the call is made the first time.
		// If the data is set then the user must have hit the back button on their browser.
		if ($this->session['order']['ccidata'] == '')
		{
            $makeCommerceTransaction = [
                'shop' => $this->config['MERCHANTID' . $this->keySuffix],
                'amount' => $this->session['order']['ordertotaltopay'],
                'reference' => $this->session['ref'],
                'country' => $this->session['order']['billingcustomercountrycode'],
                'locale' => $locale,
                'currency' => $this->session['order']['currencycode'],
                'transaction_url' => [
                    'cancel_url' => [
                        "method" => "POST",
                        "url" => $cancelReturnPath
                    ],
                    'notification_url' => [
                        "method" => "POST",
                        "url" => $autoReturnPath
                    ],
                    'return_url' => [
                        "method" => "POST",
                        "url" => $manualReturnPath
                    ]
                ]
            ];

            // Define the form fields required for MakeCommerce
            $makeCommerceRequest = [
                'customer' => [
                    'country' => $this->session['order']['billingcustomercountrycode'],
                    'email' => $this->session['order']['billingcustomeremailaddress'],
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'locale' => $locale,
                ],
                'transaction' => $makeCommerceTransaction,
            ];

            // Create the MakeCommerce transaction.
            $transaction = $this->makeCommerceObj->createTransaction($makeCommerceRequest);

            // Check that the transaction was created.
            if ('CREATED' === $transaction->status)
            {
                // Get the details of the transaction
                $transactionID = $transaction->id;
                $paymentMethods = $transaction->payment_methods;

                // Locate the redirect url in the returned list of payment methods.
                $redirectURL = '';

                foreach ($paymentMethods->other as $methodOption)
                {
                    if ('redirect' === $methodOption->name)
                    {
                        $redirectURL = $methodOption->url;
                        break;
                    }
                }

                // Remove the parameters from the rediret URL (this is the transaction ID which is in the return data).
                $url = strtok($redirectURL, '?');

                // Add the transaction ID to the list of parameters for the payment gateway.
                $formFields = ['trx' => $transactionID];

                // Set the form data for redirect to gateway.
                $smarty = SmartyObj::newSmarty('Order', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);
                $smarty->assign('method', 'GET');
                $smarty->assign('payment_url', $url);
                $smarty->assign('parameter', $formFields);

                AuthenticateObj::defineSessionCCICookie();
                $smarty->assign('ccicookiename', 'mawebcci' . $this->session['ref']);
                $smarty->assign('ccicookievalue', $this->session['order']['ccicookie']);

                // Set the ccidata in the session.
                $this->session['order']['ccidata'] = 'start';
                $this->session['order']['ccitransactionid'] = $transactionID;

                // Update the session.
                DatabaseObj::updateSession();
            }
            else
            {
                // Transaction could not be created.
                AuthenticateObj::clearSessionCCICookie();

                $smarty->assign('server', $cancelReturnPath);
            }
        }
        else
        {
            // User has clicked the back button.
            AuthenticateObj::clearSessionCCICookie();

            $smarty->assign('server', $cancelReturnPath);
        }

        // Mobile browser check then return appropriate content.
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
	public function confirm($pCallbackType)
	{
        // build default array to return
        $resultArray = $this->cciEmptyResultArray();
		$resultArray['authorised'] = false;
		$resultArray['authorisedstatus'] = 0;
        $resultArray['paymentreceived'] = 0;
        $resultArray['showerror'] = false;
        $resultArray['errorform'] = '';
        $resultArray['data1'] = '';
        $resultArray['data2'] = '';
        $resultArray['data3'] = '';
        $resultArray['data4'] = '';
        $paymentChannel = '';

        $this->logCCIEntryForSameTransactionID = true;

        // Read the returned POST data.
        $jsonData = $this->post['json'];
        $mac = $this->post['mac'];

		// Verify that the hash we get back is correct.
		if ($this->verifyHash($mac, $jsonData, ''))
		{
    		$cciRef = isset($this->get['ref']) ? $this->get['ref'] : $this->session['ref'];

			// Convert the returned json into a php object.
            $transaction = json_decode($jsonData);

            // Get the transaction ID.
            $transactionID = $transaction->transaction;

            // Get the details of the transaction.
            $transactionData = $this->makeCommerceObj->getTransaction($transactionID);

          	// The automatic and manual may arrive at the same time.
			// We need to have the second automatic callback to wait until the manual is completed.
			set_time_limit(120);
			$retryCount = 30;
			while ($retryCount > 0)
			{
				$cciEntry = PaymentIntegrationObj::getCciLogEntryFromTransactionID($transactionID);
                $responseCode = UtilsObj::getArrayParam($cciEntry, 'responsecode');
                $modeAutomatic = (UtilsObj::getArrayParam($cciEntry, 'mode') === 'AUTOMATIC');
                $noCciEntry = (count($cciEntry) === 0);

				if (strToLower($transactionData->type) === 'banklink')
				{
					// Bank transfer requires the user to press the return to merchant link, however the automatic is fired in the background.
					if (($pCallbackType === 'automatic') && ($noCciEntry))
					{
						// Let the first automatic fire as normal.
						$retryCount = 0;
					}
					else if (($pCallbackType === 'automatic') && ($modeAutomatic))
					{
						// The second automatic needs to wait for the manual to complete as both can fire at the same time.
						$retryCount--;
						UtilsObj::wait(2);
					}
					else if (($pCallbackType === 'manual') && ($noCciEntry))
					{
						// Have the manual wait for a ccilog from the automatic and that it's the same transaction.
						$retryCount--;
						UtilsObj::wait(2);
					}
					else if ((in_array($responseCode, ['COMPLETED', 'APPROVED'])) && (UtilsObj::getArrayParam($cciEntry, 'orderid') === -1))
					{
						// We have a completed order but there is no orderid assigned yet in the ccilog, wait until it is updated.
						$retryCount--;
						UtilsObj::wait(2);
					}
					else
					{
						// Manual and a cciEntry exists.
						$retryCount = 0;
					}
				}
				else
				{
					// Card payments. Customer is redirected automatically.
					if (($pCallbackType === 'automatic') && ($transactionData->status === 'CANCELLED'))
					{
						// Allow cancel updates through.
						$retryCount = 0;
					} 
                    else if (($noCciEntry) && ($pCallbackType === 'automatic'))
					{
						// The automatic needs to wait for the manual to complete as both can fire at the same time.
						$retryCount--;
						UtilsObj::wait(2);
					}
					else if ((! $noCciEntry) && ($modeAutomatic) && ($pCallbackType === 'automatic') && (in_array($responseCode, ['CANCELLED', 'EXPIRED'])))
					{
						// If a previous payment has returned with an error status then there will be a CCILOG entry so we need to wait for the next manual callback.
						// Also check that the payment method matches.
						$retryCount--;
						UtilsObj::wait(2);
					}
					else
					{
						// Can continue.
						$retryCount = 0;
					}
				}
			}

            $resultArray['ref'] = $cciRef;
        
            if ($noCciEntry)
            {
                // Empty -> first callback.
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
				// Reload the session, another call may have updated the session with an order number while this one was waiting.
				$sessionData = DatabaseObj::getSessionData($resultArray['ref']);

				if ($sessionData['order']['ordernumber'] !== $this->session['order']['ordernumber'])
				{
					// Another call has created the order, we need to update the session with the correct order number.
					// This is mainly to prevent a blank order number on the confirmation page.
					$this->session['order']['ordernumber'] = $sessionData['order']['ordernumber'];
				}

                // Populated -> additional callback.
                $resultArray['webbrandcode'] = $cciEntry['webbrandcode'];
                $resultArray['currencycode'] = $cciEntry['currencycode'];
                $resultArray['amount'] = $cciEntry['formattedamount'];
                $resultArray['parentlogid'] = $cciEntry['id'];
                $resultArray['orderid'] = $cciEntry['orderid'];
                $resultArray['update'] = ($cciEntry['orderid'] > -1);
				$this->updateStatus = ($cciEntry['orderid'] > -1);
            }

            switch ($transactionData->status)
            {
                case 'COMPLETED':
                case 'APPROVED':
                {
                    // Payment authorised.
                    $resultArray['authorised'] = true;
                    $resultArray['authorisedstatus'] = 1;
                    $resultArray['paymentreceived'] = 1;
                    break;
                }
                case 'PENDING':
                case 'CANCELLED':
                {
                    break;
                }
                default:
                {
                    // Payment failed.
                    if ('manual' === $pCallbackType)
                    {
                        $resultArray['showerror'] = true;
                        $resultArray['errorform'] = 'error.tpl';
                        $resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $transaction->status;
                    }
                    break;
                }
            }

            if (property_exists($transactionData, "channel") === true)
            {
                $paymentChannel = $transactionData->channel;
            }
            else
            {
                $paymentChannel = "UNSPECIFIED";
            }

			$serverTimeStamp = DatabaseObj::getServerTime();
			$serverDate = date('Y-m-d');
			$serverTime = date('H:i:s');
		
			// Write to log.
			PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimeStamp, '', $transactionData);

            // Assign the rest of the details sent back.
            $resultArray['responsecode'] = $transactionData->status;
            $resultArray['bankresponsecode'] = $transactionData->status;
            $resultArray['paymentmeans'] = $paymentChannel;
            $resultArray['transactionid'] = $transactionID;
            $resultArray['transactiontype'] = $transactionData->method;
            $resultArray['formattedamount'] = $this->session['order']['ordertotaltopay'];
            $resultArray['paymentdate'] = $serverDate;
            $resultArray['paymentime'] = $serverTime;
            $resultArray['formattedpaymentdate'] = $serverTimeStamp;
            $resultArray['resultisarray'] = false;
            $resultArray['resultlist'] = [];
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
			$resultArray['errorform'] = 'error.tpl';
        }

		return $resultArray;
    }


	/**
	 * Method used to determine if the hash supplied is correct
	 * 
	 * @param string $pSuppliedHash Hash supplied to verify
	 * @param array $pParams Parameters passed to generateHash
	 * @param string $pType Hash we are verifying - not used by MakeCommerce
	 * @return bool
	 */
	public function verifyHash($pSuppliedHash, $pParams, $pType)
	{
        $generatedHMAC = $this->generateHash($pParams);

        // Check the mac from makecommerce and the mac calculated are the same.
        return hash_equals($generatedHMAC, $pSuppliedHash);
	}

	/**
	 * System to generate the appropriate pre hashed string as request/response hashes use different keys
     * Not used by MakeCommerce.
	 * 
	 * @param array $pParams Array containing the parameters used to generate the hash, these could be get,post, or session values
	 * @param string $pType some gateways hash will be based on different endpoints use this to specify the hash we are generating so we can build the correct hash
	 * @return string pre hashed string
	 */
    public function hashString($pParams, $pType)
	{
		return null;
	}

	/**
	 * Method called to generate any security hash mechanism used by the gateway
	 * 
	 * @param string $pString the string to be hashed
	 * @return string The hashed string
	 */
	public function generateHash($pString)
	{
        // Create the mac based on the data returned.
        $hmac = $pString . $this->config['SECRETKEY' . $this->keySuffix];
        $hmac = hash('sha512', $hmac, true);
        $hmac = bin2hex($hmac);
        $hmac = strtoupper($hmac);

		return $hmac;
	}

	/**
	 * Returns CSP information for the gateway.
	 *
	 * @returns array
	 */
	public function getCSPDetails()
	{
        return [];
	}
}