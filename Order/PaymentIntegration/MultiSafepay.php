<?php

require_once 'TaopixAbstractGateway.php';

/**
 * MultiSafepay Payment
 */
class MultiSafepay extends TaopixAbstractGateway
{
    /**
     * Configure the MultiSafepay gateway
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

		$supportedCurrencies = array('EUR', 'USD', 'GBP');
		$resultArray['active'] = in_array($this->session['order']['currencycode'], $supportedCurrencies);

        AuthenticateObj::clearSessionCCICookie();

        return $resultArray;
    }

    public function initialize()
    {
        $resultArray = [];

        $smarty = SmartyObj::newSmarty('Order', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

        $fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl']);

        //Check if we have any ccidata if so the user has hit the back button
        if ($this->session['order']['ccidata'] == '')
        {
			$orderDetailsArray = $this->initializePayload($fixedUrlPath);
			$orderDetailsJsonString = json_encode($orderDetailsArray);
			$paymentRequestResultArray = json_decode($this->processAPIRequest('POST', 'orders', '', $orderDetailsJsonString), true);

			$paymentURL = $paymentRequestResultArray['data']['payment_url'];
			//As we use a form we need to take the URL parameters
			$parsedPaymentUrl = parse_url($paymentURL);
			parse_str($parsedPaymentUrl['query'], $queryStringArray);

			$smarty->assign('payment_url', $paymentURL);
			//The First request must be a GET for CSRF purposes
            $smarty->assign('method', 'GET');
			$smarty->assign('cancel_url', $orderDetailsArray["payment_options"]['cancel_url']);
			$smarty->assign('parameter', $queryStringArray);

            AuthenticateObj::defineSessionCCICookie();

            //Set the session so it knows we've been to the gateway
            $this->session['order']['ccidata'] = 'start';
            DatabaseObj::updateSession();

            $smarty->cachePage = true;

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
        else
        {
            // the user has clicked the back button
            AuthenticateObj::clearSessionCCICookie();

            $cancelReturnPath = UtilsObj::correctPath($this->session['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];
            $smarty->assign('cancel_url', $cancelReturnPath);

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
    }

    private function initializePayload($pFixedUrlPath)
	{
		$cancelURL = $pFixedUrlPath . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];
		$manualCallBackUrl = $pFixedUrlPath . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'];
		$automaticCallBackUrl = $pFixedUrlPath . '?fsaction=Order.ccAutomaticCallback&ref=' . $this->session['ref'] . '&brandcode=' . $this->session['webbrandcode'];

		$browserLanguageArray = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$browserLanguage = strtolower($browserLanguageArray[0]);

		$orderDescription = $this->config['VENDOR_NAME'];

		if ($this->session['items'][0]['itemproductname'] != '')
		{
			$orderDescription = LocalizationObj::getLocaleString($this->session['items'][0]['itemproductname'], $this->session['browserlanguagecode'], true);
		}

		return array(
			"type" => "redirect",
			"order_id" => $this->session['ref'] . '_'. time(),
			"currency" => $this->session['order']['currencycode'],
			"amount" => ltrim(number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '', ''), "0\t\n\r\0\x0B"),
			"description" => $orderDescription,
			"var1" => $this->session['ref'],
			"payment_options" => array(
				"notification_url" => $automaticCallBackUrl,
				"notification_method" => 'POST',
				"redirect_url" => $manualCallBackUrl,
				"cancel_url" => $cancelURL
			),
			"customer" => array(
				"locale" => $browserLanguage,
				"first_name" => $this->session['order']['billingcontactfirstname'],
				"last_name" => $this->session['order']['billingcontactlastname'],
				"address1" => $this->session['order']['billingcustomeraddress1'],
				"address2" => $this->session['order']['billingcustomeraddress2'],
				"zip_code" => $this->session['order']['billingcustomerpostcode'],
				"city" => $this->session['order']['billingcustomercity'],
				"state" => $this->session['order']['billingcustomerstate'],
				"country" => $this->session['order']['billingcustomercountrycode'],
				"phone" => $this->session['order']['billingcustomertelephonenumber'],
				"email" => $this->session['order']['billingcustomeremailaddress']
			),
			/*
			* Keep the payment link active for 55 minutes (3300 seconds) - just before the Taopix session would expire.
			* This affects the normal workflow as well as the "complete your order" email.
			*/
			"seconds_active" => 3300
		);
	}

    public function confirm($pCallbackType)
	{
		$resultArray = [];

		if ($pCallbackType === 'manual')
		{
			$resultArray = $this->manualConfirm();
		}
		else
		{
			$resultArray = $this->automaticConfirm();
		}

		return $resultArray;
	}

	private function manualConfirm()
	{
		$error = '';
		$authorised = false;
		$authorisedStatus = 0;
		$paymentReceived = 0;
		$transactionID = $this->get['transactionid'];

		$paymentQueryArray = json_decode($this->processAPIRequest('GET', 'orders', $transactionID), true);
		$paymentDataArray = $paymentQueryArray['data'];

		$resultArray = $this->cciEmptyResultArray();
		$resultArray['showerror'] = false;

		set_time_limit(120);
		$retryCount = 30;
		while ($retryCount > 0)
		{
			$cciEntry = $this->getCciLogEntryFromTransactionID($paymentDataArray['transaction_id'], false);

			if (empty($cciEntry))
			{
				$retryCount = 0;
			}
			else if (!empty($cciEntry) && ($cciEntry['orderid'] != -1))
			{
				$retryCount = 0;
			}
			else
			{
				$retryCount--;
				UtilsObj::wait(2);
			}
		}


		// if we have no ccientry then we can go ahead and create the order.
		// if we have a ccientry then the automatic callback could have came first and already created the order.
		if (empty($cciEntry))
		{
			$resultArray['webbrandcode'] = $this->session['webbrandcode'];
			$resultArray['currencycode'] = $this->session['order']['currencycode'];
			$resultArray['amount'] = $this->session['order']['ordertotaltopay'];
			$resultArray['authorisationid'] = $transactionID;
			$resultArray['parentlogid'] = -1;
			$resultArray['orderid'] = -1;
			$resultArray['update'] = false;
			$this->updateStatus = false;
		}
		else
		{
			// We have a record for this order already so use the details from that.
			$resultArray['webbrandcode'] = $cciEntry['webbrandcode'];
			$resultArray['currencycode'] = $cciEntry['currencycode'];
			$resultArray['amount'] = isset($this->session['order']['ordertotaltopay']) ? $this->session['order']['ordertotaltopay'] : $cciEntry['formattedamount'];
			$resultArray['parentlogid'] = $cciEntry['id'];
			$resultArray['orderid'] = $cciEntry['orderid'];
			$resultArray['authorisationid'] = $cciEntry['authorisationid'];

			$update = $resultArray['orderid'] === -1 ? false : true;
			$resultArray['update'] = $update;
			$this->updateStatus = $update;
			$this->loadSession = true;
		}

		// Set authorised, authorisedStatus, and paymentReceived based on the status.
		switch (\strtolower($paymentDataArray['status']))
		{
			case 'completed':
				$authorised = true;
				$authorisedStatus = 1;
				$paymentReceived = 1;
				break;
			case 'uncleared':
				$authorised = true;
				$authorisedStatus = 2;
				break;
			case 'void':
				$authorisedStatus = 3;
				$error = 'cancelled';
				break;
			case 'declined':
				$authorisedStatus = 4;
				$error = 'declined';
				break;
			case 'refunded':
				$authorisedStatus = 5;
				$error = 'refunded';
				break;
			case 'expired':
				$authorisedStatus = 6;
				$error = 'expired';
				break;
			case 'initialized':
				$authorisedStatus = 8;
				break;
			default:
				$authorisedStatus = 7;
				$error = 'unknown status';
		}

		$this->logCCIEntryForSameTransactionID = true;

		$serverTimeStamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime = date('H:i:s');

		if ($this->config['DEBUG'] === 'true')
		{
			// write to log file.
			PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimeStamp, $error, $paymentQueryArray);
			PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimeStamp, $error, [$authorisedStatus, $paymentReceived]);
		}

		// build default array to return
		$resultArray['showerror'] = false;
		$resultArray['ref'] = $this->session['ref'];
		$resultArray['authorised'] = $authorised;
		$resultArray['authorisedstatus'] = $authorisedStatus;
		$resultArray['paymentreceived'] = $paymentReceived;
		$resultArray['responsecode'] = $paymentDataArray['status'];
		$resultArray['formattedamount'] = $resultArray['amount'];
		$resultArray['transactionid'] = $paymentDataArray['transaction_id'];
		$resultArray['formattedtransactionid'] = $paymentDataArray['transaction_id'];
		$resultArray['formattedauthorisationid'] = $transactionID;
		$resultArray['paymentdate'] = $serverDate;
		$resultArray['paymenttime'] = $serverTime;
		$resultArray['formattedpaymentdate'] = $serverTimeStamp;
		$resultArray['paymentmeans'] = $paymentDataArray['payment_details']['type'];

		return $resultArray;
	}

	/**
	 * Process the automatic callback from mutlisafepay.
	 *
	 * @return array CCI result array.
	 */
	private function automaticConfirm()
	{
		// build default array to return
		$this->logCCIEntryForSameTransactionID = true;
		$resultArray = $this->cciEmptyResultArray();
		$resultArray['showerror'] = false;

		/*
		* Set the params for the hash validation, this is the timestamp and the post body, which should be
		* accessable by php://input
		*/
		$bodyContentRaw = \file_get_contents('php://input');

		if ($this->config['DEBUG'] === 'true')
		{
			// write to log file.
			PaymentIntegrationObj::logPaymentGatewayData($this->config, '', '', $bodyContentRaw);
			PaymentIntegrationObj::logPaymentGatewayData($this->config, '', '',  apache_request_headers());
		}

		if ($this->validateAutomaticCallback($bodyContentRaw))
		{
			$this->loadSession = true;
			// Convert the raw data to an array.
			$notificationData = \json_decode($bodyContentRaw, true);

			$authorised = false;
			$authorisedStatus = 0;
			$paymentReceived = 0;

			$transactionID = $this->get['transactionid'];
			$ref = $this->session['ref'] > 0 ? $this->session['ref'] : $this->get['ref'];

			// The automatic and manual may arrive at the same time.
			// We need to have the second automatic callback to wait until the manual is completed.
			// if after a minute the ccilog is empty then it is likely that the manualcallback has not happened.
			// if this is the case let the automatic callback attempt to create the order.
			set_time_limit(120);
			$retryCount = 30;
			while ($retryCount > 0)
			{
				$cciEntry = $this->getCciLogEntryFromTransactionID($notificationData['transaction_id'], true);

				if (empty($cciEntry) && ($notificationData['status'] == 'initialized'))
				{
					$retryCount = 0;
				}
				else if (!empty($cciEntry))
				{
					$retryCount = 0;
				}
				else
				{
					$retryCount--;
					UtilsObj::wait(2);
				}
			}

			// if we have no ccientry then we can go ahead and create the order.
			// if we have a ccientry then either a preious manual or automatic callback could have came first and already created the order.
			if (empty($cciEntry))
			{
				$resultArray['webbrandcode'] = $this->session['webbrandcode'];
				$resultArray['currencycode'] = $this->session['order']['currencycode'];
				$resultArray['amount'] = $this->session['order']['ordertotaltopay'];
				$resultArray['authorisationid'] = $transactionID;
				$resultArray['parentlogid'] = -1;
				$resultArray['orderid'] = -1;
				$resultArray['update'] = false;
				$this->updateStatus = false;
			}
			else
			{
				$resultArray['webbrandcode'] = $cciEntry['webbrandcode'];
				$resultArray['currencycode'] = $cciEntry['currencycode'];
				$resultArray['amount'] = isset($this->session['order']['ordertotaltopay']) ? $this->session['order']['ordertotaltopay'] : $cciEntry['formattedamount'];
				$resultArray['parentlogid'] = $cciEntry['id'];
				$resultArray['orderid'] = $cciEntry['orderid'];
				$resultArray['authorisationid'] = $cciEntry['authorisationid'];

				$update = $resultArray['orderid'] === -1 ? false : true;
				$resultArray['update'] = $update;
				$this->updateStatus = $update;
			}

			$error = '';

			// Set authorised, authorisedStatus, and paymentReceived based on the status.
			switch (\strtolower($notificationData['status']))
			{
				case 'completed':
					$authorised = true;
					$authorisedStatus = 1;
					$paymentReceived = 1;
					break;
				case 'uncleared':
					$authorised = true;
					$authorisedStatus = 2;
					break;
				case 'void':
					$authorisedStatus = 3;
					$error = 'cancelled';
					break;
				case 'declined':
					$authorisedStatus = 4;
					$error = 'declined';
					break;
				case 'refunded':
					$authorisedStatus = 5;
					$error = 'refunded';
					break;
				case 'expired':
					$authorisedStatus = 6;
					$error = 'expired';
					break;
				case 'initialized':
					$authorised = false;
					$authorisedStatus = 0;
					$paymentReceived = 0;

					// If this is the first callback that has the initialized state we do not want to log this.
					$resultArray['statusforinformationonly'] = true;
					$this->statusForInformationOnly = true;
					break;
				default:
					$authorisedStatus = 7;
					$error = 'unknown status';
			}
			$resultArray['acknowledgement'] = 'OK';

			$serverTimeStamp = DatabaseObj::getServerTime();

			if ($this->config['DEBUG'] === 'true')
			{
				// write to log file.
				PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimeStamp, $error, $notificationData);
				PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimeStamp, $error, [$authorisedStatus, $paymentReceived]);
			}

			$serverDate = date('Y-m-d');
			$serverTime = date('H:i:s');

			// Assign the rest of the details sent back.
			$resultArray['ref'] = $ref;
			$resultArray['authorised'] = $authorised;
			$resultArray['authorisedstatus'] = $authorisedStatus;
			$resultArray['responsecode'] = $notificationData['status'];
			$resultArray['formattedamount'] = $resultArray['amount'];
			$resultArray['transactionid'] = $notificationData['transaction_id'];
			$resultArray['formattedtransactionid'] = $resultArray['transactionid'];
			$resultArray['formattedauthorisationid'] = $resultArray['authorisationid'];
			$resultArray['paymentdate'] = $serverDate;
			$resultArray['paymenttime'] = $serverTime;
			$resultArray['paymentreceived'] = $paymentReceived;
			$resultArray['formattedpaymentdate'] = $serverTimeStamp;
			$resultArray['paymentmeans'] = $notificationData['payment_details']['type'];

			$resultArray['resultisarray'] = false;
			$resultArray['resultlist'] = [];
		}
		else
		{
			$resultArray['ref'] = \array_key_exists('ref', $this->session) ? $this->session['ref'] : (\array_key_exists('ref', $this->get) ? $this->get['ref'] : -1);
			$resultArray['update'] = false;
			$resultArray['authorised'] = false;
			$resultArray['paymentreceived'] = false;
			$resultArray['resultisarray'] = false;
			$this->updateStatus = false;

			if ($this->config['DEBUG'] === 'true')
			{
				PaymentIntegrationObj::logPaymentGatewayData($this->config, '', 'Failed validation',  '');
			}
		}

		return $resultArray;
	}

	/**
	 * Validates that the automatic callback has come from multisafepay and is from the past 5 mins.
	 *
	 * @param string $pBodyContent Post body from the automatic request.
	 *
	 * @return bool
	 */
	private function validateAutomaticCallback($pBodyContent)
	{
		$valid = false;

		/*
		 * Set the time offset allowed to be 15 mins, this is due to multisafepay possibly repeating the transaction twice in this period
		 * if they do not receive an 'OK' response.
		 */
		$timeOffsetAllowed = 15 * 60 * 60;
		$headers = apache_request_headers();

		// If we do not have an auth header we can not validate.
		if (isset($headers['Auth']))
		{
			list($timestamp, $signature) = explode(':', base64_decode($headers['Auth']));

			// Try to get the current timestamp from the date time object, fallback to time if there is an error.
			try
			{
				$timeObj = new \DateTime('now', new \DateTimeZone('UTC'));
				$currentTimestamp = $timeObj->getTimestamp();
			}
			catch (\Exception $e)
			{
				$currentTimestamp = \time();
			}

			// Make sure the request is from within the past 15 mins.
			if (($currentTimestamp - $timestamp) < $timeOffsetAllowed)
			{
				/*
				 * Set the params for the hash validation, this is the timestamp and json data from the request
				 */
				$params = [
					'timestamp' => $timestamp,
					'payload' => $pBodyContent,
				];

				$valid = $this->verifyHash($signature, $params, 'server');
			}
			else
			{
				// If we have debugging on log the timestamp mismatch.
				if ($this->config['DEBUG'] === 'true')
				{
					// write to log file.
					PaymentIntegrationObj::logPaymentGatewayData($this->config, '', 'Timestamp mismatch', [$currentTimestamp, $timestamp]);
				}
			}
		}

		return $valid;
	}

    public function cancel()
    {
    	$resultArray = $this->cciEmptyResultArray();

    	$this->cciLogUpdate = true;
    	$this->logCCIEntryForSameTransactionID = true;

		$resultArray['result'] = '';
		$resultArray['ref'] = $this->session['ref'];
		$resultArray['webbrandcode'] = $this->session['webbrandcode'];
		$resultArray['currencycode'] = $this->session['order']['currencycode'];
		$resultArray['amount'] = $this->session['order']['ordertotaltopay'];
		$resultArray['parentlogid'] = -1;
		$resultArray['orderid'] = -1;
		$resultArray['transactionid'] = '';
		$resultArray['authorised'] = false;
		$resultArray['showerror'] = false;

		return $resultArray;
    }

    public function processAPIRequest($http_method, $api_method, $transactionid, $http_body = NULL)
    {
        $url = $this->config['API_URL'] . $api_method . '/';

        if ($transactionid != '')
        {
        	$url .= $transactionid;
        }

        $ch = curl_init($url);

        $request_headers = array(
            "Accept: application/json",
            "api_key:" . $this->config['API_KEY']
        );

        if ($http_body !== NULL)
        {
            $request_headers[] = "Content-Type: application/json";
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $http_body);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $http_method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        $body = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception("Unable to communicatie with the MultiSafepay payment server (" . curl_errno($ch) . "): " . curl_error($ch) . ".");
        }

		curl_close($ch);

        return $body;
    }

    public function generateHash($pString)
    {

        return \hash_hmac('sha512', $pString, $this->config['API_KEY']);
    }

    public function hashString($pParams, $pType)
    {
        return $this->generateHash(\join(':', $pParams));
    }

    public function verifyHash($pSuppliedHash, $pParams, $pType)
    {
    	if ($this->config['DEBUG'] === 'true')
		{
			PaymentIntegrationObj::logPaymentGatewayData($this->config, '', 'Signature mismatch', join(' - ', [$pSuppliedHash, $this->hashString($pParams, $pType)]));
		}
        return ($pSuppliedHash === $this->hashString($pParams, $pType));
    }

    public function getCciLogEntryFromTransactionID($pTransactionID, $pAutomaticCallback)
    {
        $resultArray = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $sql = "SELECT cl.*, oh.ordernumber as ordernumber
                    FROM ccilog cl
                    LEFT JOIN orderheader oh ON (oh.id = cl.orderid)
                    WHERE cl.transactionid = ?";

			if ($pAutomaticCallback)
			{
				// the automatic needs to wait for a manualCallback to create the order first.
				$sql .= " AND cl.orderid > -1";

			}
			else
			{
				// on the first check the manualcallback must ignore any previusly logged inititalized status from the automatic
				$sql .= " AND cl.responsecode <> 'initialized'";
			}

			$sql .= " ORDER BY cl.datecreated DESC";

            if ($stmt = $dbObj->prepare($sql))
            {
                if ($stmt->bind_param('s', $pTransactionID))
                {
                    if ($stmt->execute())
                    {
                        DatabaseObj::stmt_bind_assoc($stmt, $row);
                        if ($stmt->fetch())
                        {
                            foreach ($row as $key=>$value)
                            {
                                $resultArray[$key] = $value;
                            }
                        }

                    }
                }
                $stmt->free_result();
                $stmt->close();
            }
            $dbObj->close();
        }

        return $resultArray;
    }
}
?>
