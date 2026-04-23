<?php

require_once 'TaopixAbstractGateway.php';
use \net\authorize\api\contract\v1 as AnetAPI;
use \net\authorize\api\controller as AnetController;

/**
 * {@inheritDoc}
 */
class AuthorizeNet extends TaopixAbstractGateway
{
    /**
     * Configure the payment gateway to be able to use it part of the light box.
     */
    public function configure()
    {
		$resultArray = $this->getConfigureDefaultResultArray();
		
        AuthenticateObj::clearSessionCCICookie();

        // Make sure the currency is supported and the srever is running HTTPS.
        if ((strpos($this->config['SUPPORTEDCURRENCIES'], $this->session['order']['currencycode']) === false) || (!PaymentIntegrationObj::checkSSL()))
        {
            $resultArray['active'] = false;
        }
        else
        {
            // Build the form data.
            $smarty = SmartyObj::newSmarty('CreditCardPayment', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);
            $smarty->assign('apiloginid', $this->config['APILOGIN']);
            $smarty->assign('apipublickey', $this->config['PUBLICKEY']);
            $smarty->assign('formattedPrice', UtilsObj::formatCurrencyNumber($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], $this->session['browserlanguagecode'], $this->session['order']['currencysymbol'], $this->session['order']['currencysymbolatfront']));
            $scriptName = ($this->session['ismobile']) ? 'AuthorizeNet_small.tpl' : 'AuthorizeNet_large.tpl';
            $resultArray['script'] = $smarty->fetchLocale('order/PaymentIntegration/AuthorizeNet/' . $scriptName);
            $resultArray['scripturl'] = $this->config['SCRIPTURL'];
            $resultArray['requestpaymentparamsremotely'] = true;

            // If CSP is active add the directives specified for the gateway.
            $this->addCSPDetails();
        }

        return $resultArray;
    }

    /**
     * Not used
     */
    public function initialize() {}
    
    /**
     * Set the payment in database.
     * 
     * @param string $pCallbackType Automatic or Manual.
     */
    public function confirm($pCallbackType)
    {
		// build required array
		$resultArray = $this->cciEmptyResultArray();
        $authorised = true;
        $authorisedStatus = 1;
        $paymentReceived = 1;
		$status = '';
        $ref = 0;
        $transactionID = 0;
        $parentLogID = -1;
        $orderID = -1;
        $update = false;
        $responseDescription = '';
        $cvvResponseCode = '';
        $cavvResponseCode = '';
        $last4CardNumber = '';
        $paymentMeans = '';
        $addressStatus = '';
        $paymentCertificate = '';

		// the automatic callback is called by the TAOPIX server after it has successfully performed a payment.
		// in order to replicate a server to server call we must pull the payment params from the POST.
		if ($pCallbackType == 'automatic')
		{
			$ref = $this->post['ref'];
            $transactionID = $this->post['transactionid'];
            $status = $this->post['responsecode'];
            $responseDescription = $this->post['responsedescription'];          
			$last4CardNumber = $this->post['cardnumber'];
            $cvvResponseCode = $this->post['cvvresultcode'];
            $cavvResponseCode = $this->post['cavvresultcode'];
            $paymentMeans = $this->post['paymentmeans'];
            $addressStatus = $this->post['addressstatus'];
            $paymentCertificate = $this->post['paymentcertificate'];
            $update = true;
            $this->updateStatus = true;
			$this->cciLogUpdate = true;
		}
		else
		{
			// as this is a manual callback then we can rely on the data in the ccilog to continue
			// so the customer is redirected to the order confirmation page. This is due to the fact that an entry 
			// logged by the automatic callback is only logged when the card has successfully been charged.
			$ref = $this->get['ref'];
			$cciEntry = PaymentIntegrationObj::getCciLogEntry($ref);

			$transactionID = $cciEntry['transactionid'];
            $status = $cciEntry['responsecode'];
            $responseDescription = $cciEntry['responsedescription'];          
			$last4CardNumber = $cciEntry['cardnumber'];
            $cvvResponseCode = $cciEntry['cvvresponsecode'];
            $cavvResponseCode = $cciEntry['cavvresponsecode'];
            $paymentMeans = $cciEntry['paymentmeans'];
            $addressStatus = $cciEntry['addressstatus'];
            $paymentCertificate = $cciEntry['paymentcertificate'];
            $parentLogID = $cciEntry['id'];
            $this->updateStatus = false;
            $this->cciLogUpdate = true;
            $this->logCCIEntryForSameTransactionID = true;
        }
        
		$serverTimeStamp = DatabaseObj::getServerTime();
        $serverDate = date('Y-m-d');
        $serverTime = date('H:i:s');

		//Assign the rest of the details sent back
		$resultArray['ref'] = $ref;
		$resultArray['transactionid'] = $transactionID;
		$resultArray['responsecode'] = $status;
		$resultArray['responsedescription'] = $responseDescription;          
		$resultArray['cardnumber'] = $last4CardNumber;
		$resultArray['formattedcardnumber'] = $last4CardNumber;
        $resultArray['cvvresponsecode'] = $cvvResponseCode;
        $resultArray['paymentcertificate'] = $paymentCertificate;
        $resultArray['cavvresponsecode'] = $cavvResponseCode;
        $resultArray['paymentmeans'] = $paymentMeans;
        $resultArray['addressstatus'] = $addressStatus;
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
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymenttime'] = $serverTime;
        $resultArray['formattedpaymentdate'] = $serverTimeStamp;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = [];
        $resultArray['showerror'] = false;

		return $resultArray;
    }

    /**
     * not used
     */
    public function hashString($pParams, $pType)
	{
		return null;
	}

     /**
     * not used
     */
	public function verifyHash($pSuppliedHash, $pParams, $pType)
	{
		return null;
	}

     /**
     * not used
     */
	public function generateHash($pString)
	{
		return null;
	}

    /**
     * Register the payment on AuthorizeNet server. 
     * 
     * @param string $pPaymentToken Not use for AutorizeNet.
     */
	public function processPaymentToken($pPaymentToken)
	{
        $resultArray = array();
        $resultArray['error'] = '';
		$resultArray['errormessage'] = '';
        $resultArray['redirecturl'] = '';
        $resultArray['success'] = false;
        $resultArray['data'] = array();

        // Force the type to be a card.
        $this->session['order']['paymentmethodcode'] = 'CARD';
        $this->session['order']['paymentgatewaycode'] = '';
        $paymentMethodDataResult = DatabaseObj::getPaymentMethodFromCode('CARD');

        if ($paymentMethodDataResult['result'] === '')
        {
            $this->session['order']['paymentmethodname'] = $paymentMethodDataResult['name'];
            DatabaseObj::updateSession();

            // Create a merchantAuthenticationType object with authentication details retrieved from the config file.
            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($this->config['APILOGIN']);
            $merchantAuthentication->setTransactionKey($this->config['TRANSACTIONKEY']);
            
            // Set the transaction's refId
            $refId = $this->session['ref'] . time();

            // Create the payment object for a payment nonce
            $opaqueData = new AnetAPI\OpaqueDataType();
            $opaqueData->setDataDescriptor(UtilsObj::getPOSTParam('datadescriptor'));
            $opaqueData->setDataValue(UtilsObj::getPOSTParam('datavalue'));

            // Add the payment data to a paymentType object
            $paymentOne = new AnetAPI\PaymentType();
            $paymentOne->setOpaqueData($opaqueData);

            // Create order information
            $order = new AnetAPI\OrderType();
            $order->setInvoiceNumber($this->session['ref']);
            $order->setDescription(htmlspecialchars(LocalizationObj::getLocaleString($this->session['items'][0]['itemproductname'], $this->session['browserlanguagecode'], true), ENT_QUOTES));

            // Set the customer's Bill To address
            $customerAddress = new AnetAPI\CustomerAddressType();
            $customerAddress->setFirstName(htmlspecialchars($this->session['order']['billingcontactfirstname'], ENT_QUOTES));
            $customerAddress->setLastName(htmlspecialchars($this->session['order']['billingcontactlastname'], ENT_QUOTES));
            $customerAddress->setAddress(htmlspecialchars($this->session['order']['billingcustomeraddress1'] . ' ' . $this->session['order']['billingcustomeraddress2'], ENT_QUOTES));
            $customerAddress->setCity(htmlspecialchars($this->session['order']['billingcustomercity'], ENT_QUOTES));
            $customerAddress->setState(htmlspecialchars($this->session['order']['billingcustomerstate'],ENT_QUOTES));
            $customerAddress->setZip(htmlspecialchars($this->session['order']['billingcustomerpostcode'], ENT_QUOTES));
            $customerAddress->setCountry($this->session['order']['billingcustomercountrycode']);

            // Create a TransactionRequestType object and add the previous objects to it
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction"); 
            $transactionRequestType->setAmount(number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '.', ''));
            $transactionRequestType->setOrder($order);
            $transactionRequestType->setPayment($paymentOne);
            $transactionRequestType->setCurrencyCode($this->session['order']['currencycode']);
            $transactionRequestType->setBillTo($customerAddress);

            // Assemble the complete transaction request
            $request = new AnetAPI\CreateTransactionRequest();
            $request->setMerchantAuthentication($merchantAuthentication);
            $request->setRefId($refId);
            $request->setTransactionRequest($transactionRequestType);

            // Create the controller and get the response
            $controller = new AnetController\CreateTransactionController($request);
            $url = ($this->config['ENVMODE'] === '1') ? \net\authorize\api\constants\ANetEnvironment::PRODUCTION : \net\authorize\api\constants\ANetEnvironment::SANDBOX;
            $response = $controller->executeWithApiResponse($url);

            if ($response != null) {
                // Check to see if the API request was successfully received and acted upon
                if ($response->getMessages()->getResultCode() == "Ok") {
                    // Since the API request was successful, look for a transaction response
                    // and parse it to display the results of authorizing the card
                    $tresponse = $response->getTransactionResponse();

                    if (($tresponse != null) && ($tresponse->getMessages() != null)) {
                        $resultArray['success'] = true;
                        $resultArray['data']['ref'] = $this->session['ref'];
                        $resultArray['data']['transactionid'] = $tresponse->getTransId();
                        $resultArray['data']['responsecode'] = $tresponse->getResponseCode();
                        $resultArray['data']['responsedescription'] = $tresponse->getMessages()[0]->getDescription();          
                        $resultArray['data']['cardnumber'] = $tresponse->getAccountNumber();
                        $resultArray['data']['addressstatus'] = $tresponse->getAVSResultCode();
                        $resultArray['data']['cvvresultcode'] = $tresponse->getCVVResultCode();
                        $resultArray['data']['cavvresultcode'] = $tresponse->getCAVVResultCode();
                        $resultArray['data']['paymentmeans'] = $tresponse->getAccountType();
                        $resultArray['data']['paymentcertificate'] = $tresponse->getAuthCode();
                        
                        $fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl']);
                        $resultArray['redirecturl'] = $fixedUrlPath . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'];
                    } else {
                        if ($tresponse->getErrors() != null) {
                            $resultArray['error'] = $tresponse->getErrors()[0]->getErrorCode();
                            $resultArray['errormessage'] = $tresponse->getErrors()[0]->getErrorText();
                        }
                    }
                } else {
                    $tresponse = $response->getTransactionResponse();
                
                    if ($tresponse != null && $tresponse->getErrors() != null) {
                        $resultArray['error'] = $tresponse->getErrors()[0]->getErrorCode();
                        $resultArray['errormessage'] = $tresponse->getErrors()[0]->getErrorText();
                    } else {
                        $resultArray['error'] = $response->getMessages()->getMessage()[0]->getCode();
                        $resultArray['errormessage'] = $response->getMessages()->getMessage()[0]->getText();
                    }
                }      
            } else {
                $resultArray['errormessage'] = SmartyObj::getParamValue('CreditCardPayment', 'str_OrderGenericError');
            }
        }
        else
        {
            $resultArray['error'] = $paymentMethodDataResult['result'];
            $resultArray['errormessage'] = $paymentMethodDataResult['resultparam'];
        }

        return $resultArray;
	}

	public function getCSPDetails()
	{
		$urlInfo = parse_url($this->config['SCRIPTURL']);

		return [
			'script-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'],
			],
			'frame-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'],
            ],
            'connect-src'=> [
                $urlInfo['scheme'] . '://' . $urlInfo['host'],
                'https://apitest.authorize.net/',
                'https://api.authorize.net/'
			]
		];
	}
}

?>