<?php

require_once __DIR__ . '/TaopixAbstractGateway.php';

class Stripe extends TaopixAbstractGateway
{
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
			$templateDataArray = array();
			$templateDataArray['ref'] =  $this->session['ref'];
			$templateDataArray['stripepublishablekey'] =  $this->config['STRIPEPK'];
			$templateDataArray['name'] = htmlspecialchars($this->session['order']['billingcontactfirstname'] . ' ' . $this->session['order']['billingcontactlastname'], ENT_QUOTES);
			$templateDataArray['addressline1'] = htmlspecialchars($this->session['order']['billingcustomeraddress1'], ENT_QUOTES);
			$templateDataArray['addressline2'] = htmlspecialchars($this->session['order']['billingcustomeraddress2'], ENT_QUOTES);
			$templateDataArray['addresscity'] = htmlspecialchars($this->session['order']['billingcustomercity'], ENT_QUOTES);
			$templateDataArray['addressstate'] = htmlspecialchars($this->session['order']['billingcustomerstate'],ENT_QUOTES);
			$templateDataArray['addresszip'] = htmlspecialchars($this->session['order']['billingcustomerpostcode'], ENT_QUOTES);
			$templateDataArray['addresscountrycode'] = $this->session['order']['billingcustomercountrycode'];
			$templateDataArray['amount'] = number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '', '');
			$templateDataArray['currencycode'] = strtolower($this->session['order']['currencycode']);
			$templateDataArray['orderdescription'] = htmlspecialchars(LocalizationObj::getLocaleString($this->session['items'][0]['itemproductname'], $this->session['browserlanguagecode'], true), ENT_QUOTES);

			$smarty->assign('stripeparams', $templateDataArray);

			if ($this->session['ismobile'])
			{
				$script = $smarty->fetchLocale('order/PaymentIntegration/Stripe/Stripe_small.tpl');
			}
			else
			{
				$script = $smarty->fetchLocale('order/PaymentIntegration/Stripe/Stripe_large.tpl');
			}

			$resultArray['active'] = true;
			$resultArray['script'] = $script;
			$resultArray['scripturl'] = $this->config['STRIPEJSURL'];

			// If CSP is active add the directives specified for the gateway.
			if ($this->cspBuilder !== null)
			{
				$this->addCSPDetails();
			}
		}

        return $resultArray;
    }

    public function initialize()
    {
		global $gSession;
	
		// the paymentRequestAPI needs the amount to charge sent in the browser.
		// we must return the amount from the server so that it is the correct amount at the time of payment.
		// this is due to the fact that applying a gift card is an ajax call rather than a complete page refresh.

		$amount = number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '', '');

		$returnArray = array();
		$returnArray['result'] = 1;
		$returnArray['amount'] = $amount;

		\Stripe\Stripe::setApiKey($this->config['STRIPESK']);
		\Stripe\Stripe::setApiVersion('2022-11-15');

		$intent = \Stripe\PaymentIntent::create([
		'amount' => $amount,
		'currency' => $this->session['order']['currencycode'],
		'metadata' => ['ref' => $this->session['ref'], 'integration_check' => 'accept_a_payment'],
		'description' => htmlspecialchars(LocalizationObj::getLocaleString($this->session['items'][0]['itemproductname'], $this->session['browserlanguagecode'], true), ENT_QUOTES),
		'automatic_payment_methods' => ['enabled' => true],
		'shipping' => ['name' => $gSession['shipping'][0]['shippingcontactfirstname'] . ' ' . $gSession['shipping'][0]['shippingcontactlastname'], 'address' => 
			['city' => $gSession['shipping'][0]['shippingcustomercity'], 
			'line1' => $gSession['shipping'][0]['shippingcustomeraddress1'], 
			'line2' => $gSession['shipping'][0]['shippingcustomeraddress2'], 
			'state' => $gSession['shipping'][0]['shippingcustomerstate'], 
			'postal_code' => $gSession['shipping'][0]['shippingcustomerpostcode'],
			'country' => $gSession['shipping'][0]['shippingcustomercountrycode']
			]
			]
		]);

		$returnArray['clientsecret'] = $intent['client_secret'];

		$fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl']);
        $returnArray['redirecturl'] = $fixedUrlPath . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'];

		return json_encode($returnArray);
	}

    public function confirm($pCallbackType)
    {
		// build required array
		$resultArray = $this->cciEmptyResultArray();
		$resultArray['showerror'] = false;
		$resultArray['resultisarray'] = false;

		$update = false;
		$authorised = false;
		$authorisedStatus = 0;
		$paymentReceived = 0;
		$status = '';
		$paymentMeans = '';
		$ref = 0;
		$transactionID = '';
		$authorisationID = '';
		$paymentCertificate = '';
		$transactionType = '';
		$parentLogID = -1;
		$orderID = -1;
		$this->cciLogUpdate = true;

		// the automatic callback is called by the Stripe webhook after it has successfully performed a charge to the card.
		if ($pCallbackType == 'automatic')
		{
			$ref = $this->get['ref'];
			$payload = @file_get_contents('php://input');
			$endpoint_secret = $this->config['STRIPEENDPOINTSECRET'];
			$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
			$event = null;

			try {
				$event = \Stripe\Webhook::constructEvent(
					$payload, $sig_header, $endpoint_secret
				);
			} catch(\UnexpectedValueException $e) {
				// Invalid payload
				http_response_code(400);
				exit();
			} catch(\Stripe\Exception\SignatureVerificationException $e) {
				// Invalid signature
				http_response_code(400);
				exit();
			}

			$intent = $event->data->object;
			$transactionID = $intent->id;
			$status = $intent->status;
			$paymentCertificate = $intent->id;
			$transactionType = $event->type;

			if ($event->type == "payment_intent.succeeded") {
				$authorisationID = $intent->payment_method;

				$authorised = true;
				$authorisedStatus = 1;
				$paymentReceived = 1;
				$this->cciLogUpdate = true;
				$this->logCCIEntryForSameTransactionID = true;
				http_response_code(200);
			} elseif ($event->type == "payment_intent.payment_failed") {
				$authorisationID = $intent->last_payment_error->payment_method->id;
				$paymentMeans = $intent->last_payment_error->payment_method->type;
				
				// cciLogUpdate is set based on wheter or not it is a failed card payment. The browser handles card payment errors so we can ignore the failed webhook. 
				// All other payment methods must log a cci record as they redirect to the manual callback when their has been a payment error. 
				$this->cciLogUpdate = isset($intent->last_payment_error->payment_method->card) ? false : true;

				if (!$this->cciLogUpdate)
				{
					$this->session['order']['ccitransactionid'] = $transactionID;
					$this->loadSession = false;
				}

				http_response_code(200);
			}

			$this->updateStatus = false;
			$update = false;
		}
		else
		{
			// as this is a manual callback we need to first process the Stripe webhook to confirm the status of the payment
			// we must redirect to the order confirmation page or return the user back to the shopping cart if the payment intent failed.
			$ref = $this->get['ref'];

			// need to check to see if the automatic callback has been received.
			// when processing an manual callback for a succesfull order we must wait for the automatic callback to create the order so we get the correct
			// orderid in order to update the payment recieved flag correctly
			// wait upto 60 seconds for the automatic callback response
			set_time_limit(120);
			$retryCount = 30;
			while($retryCount > 0)
			{
				$cciEntry = PaymentIntegrationObj::getCciLogEntry($ref);

				if ($cciEntry === [])
				{
					$retryCount--;
					UtilsObj::wait(2);
				}
				else
				{
					if ($cciEntry['mode'] == 'AUTOMATIC')
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

			$parentLogID = $cciEntry['id'];
			$orderID = $cciEntry['orderid'];

			if ($cciEntry['transactiontype'] == "payment_intent.succeeded")
			{
				$authorised = true;
				$authorisedStatus = 1;
				$paymentReceived = 1;
				$parentLogID = $cciEntry['id'];
				$orderID = $cciEntry['orderid'];

				$update = true;
				$this->updateStatus = true;
			}
			
			$this->cciLogUpdate = true;
			$this->logCCIEntryForSameTransactionID = true;
			$this->loadSession = true;
		}

		$serverTimeStamp = DatabaseObj::getServerTime();
        $serverDate = date('Y-m-d');
        $serverTime = date('H:i:s');

		//Assign the rest of the details sent back
		$resultArray['ref'] = $ref;
		$resultArray['transactionid'] = $transactionID;
		$resultArray['responsecode'] = $status;
		$resultArray['responsedescription'] = $status;
		$resultArray['authorisationid'] = $authorisationID;
		$resultArray['paymentcertificate'] =  $paymentCertificate;
		$resultArray['transactiontype'] = $transactionType;
		$resultArray['authorised'] = $authorised;
		$resultArray['authorisedstatus'] = $authorisedStatus;
		$resultArray['paymentreceived'] = $paymentReceived;
		$resultArray['webbrandcode'] = $this->session['webbrandcode'];
		$resultArray['currencycode'] = $this->session['order']['currencycode'];
		$resultArray['amount'] = $this->session['order']['ordertotaltopay'];
		$resultArray['formattedamount'] = $this->session['order']['ordertotaltopay'];
		$resultArray['parentlogid'] = $parentLogID;
		$resultArray['orderid'] = $orderID;
		$resultArray['update'] = $update;
        $resultArray['formattedamount'] = $this->session['order']['ordertotaltopay'];
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymenttime'] = $serverTime;
		$resultArray['paymentmeans'] = $paymentMeans;
        $resultArray['formattedpaymentdate'] = $serverTimeStamp;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = [];

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
		$urlInfo = parse_url($this->config['STRIPEJSURL']);

		return [
			'script-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'],
			],
			'frame-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'],
			],
		];
	}
}

?>