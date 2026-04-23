<?php
require_once __DIR__ . '/TaopixAbstractGateway.php';
require_once __DIR__ . '/Request/CurlHandler.php';

class DibsEasy extends TaopixAbstractGateway
{
    private $dataPacket = null;
    private $paymentID = '';

    //Get the API credentials based on if transaction mode is set to 1 or 0
    private function getCredentials()
    {
        return array(
            'apiurl' => $this->config['TRANSACTIONMODE'] ? $this->config['LIVEENDPOINT'] : $this->config['TESTENDPOINT'],
            'checkoutjs' => $this->config['TRANSACTIONMODE'] ? $this->config['LIVESCRIPT'] : $this->config['TESTSCRIPT'],
            'secretkey' => $this->config['TRANSACTIONMODE'] ? $this->config['LIVESECRETKEY'] : $this->config['TESTSECRETKEY'],
            'checkoutkey' => $this->config['TRANSACTIONMODE'] ? $this->config['LIVECHECKOUTKEY'] : $this->config['TESTCHECKOUTKEY']
        );
    }

    public function configure()
    {
        $credentials = $this->getCredentials();

        $resultArray = [
            'active' => true,
            'form' => '',
            'scripturl' => '',
            'script' => '',
			'action' => '',
			'requestpaymentparamsremotely' => true
        ];

        AuthenticateObj::clearSessionCCICookie();

        if (strpos($this->config['CURRENCYLIST'], $this->session['order']['currencycode']) === false)
        {
            $resultArray['active'] = false;
        }

        //Load in the required templates.

        $smarty = SmartyObj::newSmarty('CreditCardPayment', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

        if ($this->session['ismobile'])
        {
            $script = $smarty->fetchLocale('order/PaymentIntegration/DibsEasy/DibsEasy_small.tpl');
        }
        else
        {
            $script = $smarty->fetchLocale('order/PaymentIntegration/DibsEasy/DibsEasy_large.tpl');
        }

        $resultArray['script'] = $script;
        $resultArray['scripturl'] = $credentials['checkoutjs'];

		// If CSP is active add the directives specified for the gateway.
		if ($this->cspBuilder !== null)
		{
			$this->addCSPDetails();
		}
		
        return $resultArray;
    }

    protected function defaultCurlOptions()
    {
        $credentials = $this->getCredentials();

        //The token needs to be stripped of the test-key
        $token = substr($credentials['secretkey'], strrpos($credentials['secretkey'], '-') + 1);

        $returnArray = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array('Content-Type: application/json','Accept: application/json','Authorization:' . $token),
			CURLOPT_ENCODING => '',
			CURLOPT_TIMEOUT => 30,
			CURLOPT_MAXREDIRS => 1,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CAINFO => UtilsObj::getCurlPEMFilePath(),
        ];

        return $returnArray;
    }

    public function initialize()
    {
        $fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl']);
        $manualCallBackUrl = $fixedUrlPath . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'];
        // Developers have the ability to use an ngrok url here by setting the DEVAUTOMATICURL in the config file.
        $automaticCallBackUrl = UtilsObj::getArrayParam($this->config, "DEVAUTOMATICURL", $fixedUrlPath) . '?fsaction=Order.ccAutomaticCallback&ref=' . $this->session['ref'];
		$credentials = $this->getCredentials();
		$orderName = '';

		if ((UtilsObj::getArrayParam($this->config, "ORDERREFERENCEMODE", 0) == 1) && (UtilsObj::getArrayParam($this->config, "ORDERREFERENCE", '') != ''))
		{
			$orderName = $this->config['ORDERREFERENCE'];
		}
		else 
		{
			// dibs does not like the following characters, remove them
			$charactersToRemove = array('"', "'", '&', "<", ">");
			$translatedOrderName = LocalizationObj::getLocaleString($this->session['items'][0]['itemproductcollectionname'], $this->session['browserlanguagecode']);

			$orderName = str_replace($charactersToRemove, "", $translatedOrderName);
		}

        if ($this->session['order']['ccidata'] == '')
        {
            $price = bcmul($this->session['order']['ordertotaltopay'], 100);
			$country  = UtilsAddressobj::getCountry($this->session['order']['billingcustomercountrycode']);

            $orderData = [
                'order' => [
                    'items' => [],
                    'amount' => (int) $price,
                    'currency' => $this->session['order']['currencycode'],
                    'reference' => $orderName
                ],
                'checkout' => [
                    'url' => $manualCallBackUrl,
                    'termsurl' => $fixedUrlPath,
					'merchantHandlesConsumerData' => true,
					'consumer' => [
						'reference' => $this->session['userid'],
						'email' => $this->session['order']['billingcustomeremailaddress'],
						'shippingAddress' => [
							'addressLine1' => $this->session['order']['billingcustomeraddress1'],
							'addressLine2' => $this->session['order']['billingcustomeraddress2'],
							'postalCode' => trim(str_replace(' ', '', $this->session['order']['billingcustomerpostcode'])),
							'city' => $this->session['order']['defaultbillingcustomercity'],
							'country' => $country['isocode3']
						],
						'privatePerson' => [
							'firstName' => $this->session['order']['billingcontactfirstname'],
							'lastName' => $this->session['order']['billingcontactlastname']
						]
					]
                ]
            ];

            $orderInfo = [
                'reference' => $this->session['ref'],
                'name' => $orderName,
                'quantity' => 1,
                'unit' => 'pcs',
                'unitPrice' => (int) $price,
                'taxRate' => 0,
                'taxAmount' => 0,
                'grossTotalAmount' => (int) $price,
                'netTotalAmount' => (int) $price
            ];

            $orderData['order']['items'][] = $orderInfo;
            
            $authorisationKey = str_pad($this->session['ref'], 50, "0", STR_PAD_LEFT);

            $orderData['notifications'] = [
                'webhooks' => [
                    [
                        'eventName' => 'payment.reservation.created',
                        'url' => $automaticCallBackUrl,
                        'authorization' => $authorisationKey
                    ]
                ]
            ];
        }

        $this->curlConnection = new CurlHandler('json', $this->defaultCurlOptions());

        //Send the data to DIBS
        $dibsRequest = $this->curlConnection->connectionSend($credentials['apiurl'], '', 'POST', $orderData, 1);

        $dibsParsed = json_decode($dibsRequest, true);

        $resultArray = [
            'paymentid' => -1,
            'checkoutkey' => -1,
            'manualurl' => "",
            'language' => $this->config['LANGUAGE']
        ];

        // If there has been an error then log it.
        if (array_key_exists("errors", $dibsParsed))
        {
            PaymentIntegrationObj::logPaymentGatewayData($this->config, DatabaseObj::getServerTime(), '', ["Error with call to dibs", $dibsParsed, $orderData]);
        }
        else
        {
            $resultArray = [
                'paymentid' => $dibsParsed['paymentId'],
                'checkoutkey' => $credentials['checkoutkey'],
                'manualurl' => $manualCallBackUrl,
                'language' => $this->config['LANGUAGE']
            ];
        }

        return json_encode($resultArray);
    }

    /**
     * Gets the payment data packet from the either the post body or via a curl call
     *
     * @return []
     */
    private function getDataPacket($pCallbackType)
    {
        if ($pCallbackType === 'automatic')
        {
            return $this->getDataPacketFromBody();
        }
        else
        {
            return $this->getDataPacketFromCurl();
        }
    }

    /**
     * Gets the payment data packet from the post body
     *
     * @return []
     */
    private function getDataPacketFromBody()
    {
        if (!$this->dataPacket)
        {
            $authorisationKey = str_pad($this->session['ref'], 50, "0", STR_PAD_LEFT);
            $headers = apache_request_headers();
    
            // Make sure the packet has the correct authorization header and value.
            if (array_key_exists('Authorization', $headers))
            {
                if ($authorisationKey === $headers['Authorization'])
                {
                    // Read the post body
                    $body = file_get_contents('php://input');

                    $this->dataPacket = json_decode($body, true);

                    if (!$this->dataPacket)
                    {
                        throw new \Exception("Problem parsing data from post body: " . $body);
                    }

                }
                else
                {
                    throw new \Exception("Incorrect Authorization header. Key: " . $authorisationKey . " !== Header:" . $headers['Authorization']);
                }
            }
            else
            {
                throw new \Exception("Authorization header missing");
            }
        }
        
        return $this->dataPacket;
    }

    /**
     * Gets the data packet from a CURL call.
     *
     * @param [string] $pPaymentID
     * @return []
     */
    private function getDataPacketFromCurl()
    {
        $credentials = $this->getCredentials();

        $this->curlConnection = new CurlHandler('', $this->defaultCurlOptions());
        // Query the order to get the details
        $orderQuery = $this->curlConnection->connectionSend($credentials['apiurl'] . '/' . $this->paymentID, '', 'GET', '', 1);
        $this->dataPacket = json_decode($orderQuery, true);

        if (!$this->dataPacket)
        {
            throw new \Exception("Problem parsing data from curl request: " . $orderQuery);
        }
    }

    /**
     * Parses the paymentid from the http data based on the packet type.
     * For a manual callback it is sent in the get parameters.
     * For an automatic callback it is sent in the POST body.
     *
     * @param [string] $pPacketType 'manual' or 'automatic'
     * @return string The payment ID assigned to the transaction.
     */
    public function getPaymentIDFromPacket($pPacketType)
    {
        if ($pPacketType === 'manual')
        {
            $this->paymentID = $this->get['paymentId'];
        }
        else
        {
            try
            {
                $dataBodyArray = $this->getDataPacketFromBody();

                $this->paymentID = $dataBodyArray['data']['paymentId'];
            }
            catch (\Exception $e)
            {
                PaymentIntegrationObj::logPaymentGatewayData($this->config, DatabaseObj::getServerTime(), '', [$e->getMessage()]);
            }
        }

        return $this->paymentID;
    }

    /**
     * Extract the order payment details from the data packet
     *
     * @param [type] $pCallBackType manual/automatic
     * @return []
     */
    private function getOrderPaymentDetails($pCallBackType)
    {
        $status = 'AWAITING';

        $returnArray = 
        [
            'ordertotal' => 0.00,
            'paymentid' => '',
            'currencycode' => '',
            'authorised' => true,
            'pendingstatus' => $status,
            'responsecode' => 'SUCCESS',
        ];

        $rootElement = 'data';
        $paymentDetailsElement = '';
        $priceElement = 'amount';

        if ($pCallBackType === 'manual')
        {
            $rootElement = 'payment';
            $priceElement = 'orderDetails';
            $paymentDetailsElement = 'paymentDetails';
        }               

        $rootArray = UtilsObj::getArrayParam($this->dataPacket, $rootElement, []);
        $priceArray = ($rootArray !== [])?UtilsObj::getArrayParam($rootArray, $priceElement, []):[];

        $paymentDetailsArray = ($paymentDetailsElement === '')?$rootArray:UtilsObj::getArrayParam($rootArray, $paymentDetailsElement, []);
        
        if ($priceArray !== [])
        {
            $returnArray['paymentid'] = UtilsObj::getArrayParam($rootArray, 'paymentId', '');
            $returnArray['ordertotal'] = UtilsObj::getArrayParam($priceArray, 'amount', 0);
            $returnArray['currencycode'] = UtilsObj::getArrayParam($priceArray, 'currency', '');
        }

        if (UtilsObj::getArrayParam($this->get, 'paymentFailed', '') !== '')
        {
            $status = 'FAILED';
        }
        else if (UtilsObj::getArrayParam($paymentDetailsArray, 'paymentType', '') === '')
        {
            $status = 'ABANDONED';
        }

        $returnArray['pendingstatus'] = $status;

        if (($status === 'ABANDONED') || ($status === 'FAILED'))
        {
            $returnArray['responsecode'] = $status;
            $returnArray['authorised'] = false;
        }

        // Default the order total to 2 decimal places.
        $returnArray['ordertotal'] = number_format(str_replace(",","", ($returnArray['ordertotal']/ 100)), 2, ".", "");

        return $returnArray;
    }

    private function waitForAutomaticCallback($pRef)
    {
        $cciEntry = [];

        // Make sure the call doesn't time out
        set_time_limit(120);
        $retryCount = 30;
        PaymentIntegrationObj::logPaymentGatewayData($this->config, DatabaseObj::getServerTime(), '',
        [
            'Manual call with no order id means that the automatic callback hasnt come yet. Waiting for max ' . ($retryCount*2) . ' seconds'
        ]);

        // Since the automatic call might have reached us first we need to wait and see if it has by checking the cci log table for an entry.
        // Wait a max of 60 seconds.
        while ($retryCount > 0)
        {
            $cciEntry = PaymentIntegrationObj::getCciLogEntry($pRef);
            
            if ($cciEntry === [])
            {
                $retryCount--;
                UtilsObj::wait(2);
            }
            else
            {
                if ($cciEntry['orderid'] !== -1)
                {
                    $retryCount = 0;
                }
                else
                {
                    $retryCount--;
                    UtilsObj::wait(2);
                }
            }
        }

        if ($cciEntry !== [])
        {
            PaymentIntegrationObj::logPaymentGatewayData($this->config, DatabaseObj::getServerTime(), '',
            ['We have a CCILog entry now so the automatic callback has come in.']);
        }
        else
        {
            PaymentIntegrationObj::logPaymentGatewayData($this->config, DatabaseObj::getServerTime(), '',
            ['Automatic callback still not occured so will have to query for the data.']);
        }

        return $cciEntry;
    }


    public function confirm($pCallBackType)
    {
        $this->logCCIEntryForSameTransactionID = true;

        $errorString = "";
        $resultArray = $this->cciEmptyResultArray();
		$serverDate = date('Y-m-d');
		$serverTime = date('H:i:s');
        $this->loadSession = true;
        $authorised = false;
        $pendingstatus = '';
        $responseCode = '';
        $update = true;
        $orderTotal = 0.00;
        $transactionID = '';
        $currencyCode = '';
        $orderId = -1;
        $parentLogID = -1;

        // read the ref from the get params
        $ref = $this->get['ref'];

        // Get the payment id from data packet
        $this->getPaymentIDFromPacket($pCallBackType);

        // Make sure that a payment id has been sent
        if ($this->paymentID !== "")
        {
            // Check to see if we have a cci entry already
            $cciEntry = PaymentIntegrationObj::getCciLogEntryFromTransactionID($this->paymentID);

            // If this is a manual callback and there is no order id then wait for the automatic callback to come in.
            if (($cciEntry === []) && ($pCallBackType === 'manual'))
            {
                // If this is not a card then don't wait for the automatic since it will never come.
                $cciEntry = $this->waitForAutomaticCallback($ref);
            }

            // If the cciEntry is empty then it is an automatic callback or the manual callback has waiting 10 seconds for the automatic to come in and
            // update the ccilog
            if ($cciEntry !== [])
            {
                $orderId = $cciEntry['orderid'];
                $parentLogID = $cciEntry['id'];

                PaymentIntegrationObj::logPaymentGatewayData($this->config, DatabaseObj::getServerTime(), '', 
                [
                    'Already got a CCI log entry for payment ID: ' . $this->paymentID . ' calling Dibs again to get the latest payment status.', 
                    'Order ID: ' . $orderId,
                    'Parent Log ID: ' . $parentLogID,
                    'Transaction ID: ' . $cciEntry['transactionid'],
                    'Session ID: ' . $cciEntry['sessionid'],
                    'Mode: ' . $cciEntry['mode']
                ]);
            }

            try
            {
                $this->getDataPacket($pCallBackType);

                PaymentIntegrationObj::logPaymentGatewayData($this->config, DatabaseObj::getServerTime(), '', $this->dataPacket);

                $orderPaymentDetails = $this->getOrderPaymentDetails($pCallBackType);

                $orderTotal = $orderPaymentDetails['ordertotal'];
                $transactionID = $orderPaymentDetails['paymentid'];
                $currencyCode = $orderPaymentDetails['currencycode'];
                $authorised = $orderPaymentDetails['authorised'];
                $pendingstatus = $orderPaymentDetails['pendingstatus'];
                $responseCode = $orderPaymentDetails['responsecode'];
                $update = false;
            }
            catch (\Exception $e)
            {
                $errorString = $e->getMessage();
            }
        }
        else
        {
            $errorString = 'Unable to get payment ID from ' . $pCallBackType . ' callback';
        }

        if ($errorString !== "")
        {
            error_log($errorString);
            PaymentIntegrationObj::logPaymentGatewayData($this->config, DatabaseObj::getServerTime(), '', [$errorString]);
        }

        $this->updateStatus = false;
        $resultArray['result'] = '';
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $orderTotal;
        $resultArray['formattedamount'] = $orderTotal;
        $resultArray['addressstatus'] = '';
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] = '';
        $resultArray['authorised'] = $authorised;
        $resultArray['authorisedstatus'] = $authorised;
        $resultArray['transactionid'] = $transactionID;
        $resultArray['formattedtransactionid'] = $transactionID;
        $resultArray['responsedescription'] = '';
        $resultArray['authorisationid'] = $transactionID;
        $resultArray['formattedauthorisationid'] = $transactionID;
        $resultArray['bankresponsecode'] = '';
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = '';
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = '';
        $resultArray['business'] = '';
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = $pendingstatus;
        $resultArray['transactiontype'] = '';
        $resultArray['settleamount'] = '';
        $resultArray['script'] = '';
        $resultArray['scripturl'] = '';
        $resultArray['charityflag'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = '';
        $resultArray['paymenttime'] = $serverTime;
        $resultArray['paymentreceived'] = 1;
        $resultArray['formattedpaymentdate'] = $serverDate;
        $resultArray['settleamount'] = '';
        $resultArray['currencycode'] = $currencyCode;
        $resultArray['webbrandcode'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = $orderId;
        $resultArray['parentlogid'] = $parentLogID;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = array();
        $resultArray['showerror'] = false;
        $resultArray['responsecode'] = $responseCode;

        return $resultArray;
    }

    public function hashString($pParams, $pType)
	{
        return null;
	}

	public function verifyHash($pSuppliedHash, $pParams, $pType)
	{
		return null;
	}

	public function generateHash($pString)
	{
        return null;
	}

	public function getCSPDetails()
	{
		$prefix = $this->config['TRANSACTIONMODE'] ? 'LIVE' : 'TEST';
		$urlInfo = parse_url($this->config[$prefix . 'SCRIPT']);

		return [
			'script-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'],
				"unsafe-eval",
			],
			'frame-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'],
			],
			'child-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'],
			],
			'style-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'],
			],
			'connect-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'],
			],
		];
	}
}
?>