<?php
require_once __DIR__ . '/TaopixAbstractGateway.php';
require_once __DIR__ . '/Request/CurlHandler.php';

class Bancard extends TaopixAbstractGateway
{
    //Get the API credentials based on if transaction mode is set to 1 or 0
    private function getCredentials()
    {
        $endpointURL = "";

        // set the endpoint URL bases on the transaction mode setting 1 = live, 0 = test
        if ($this->config['TRANSACTIONMODE'])
        {
            $endpointURL = $this->config['LIVEENDPOINT'];
        }
        else 
        {
            $endpointURL = $this->config['TESTENDPOINT'];
        }

        return array(
            'apiurl' => $endpointURL,
            'scripturl' => $endpointURL . $this->config['SCRIPTURL'],
            'confirmurl' => $endpointURL . $this->config['CONFIRMURL'],
            'paymenturl' => $endpointURL . $this->config['PAYMENTURL']
        );
    }
    
    public function configure()
    {
        $credentials = $this->getCredentials();

        $resultArray = [
            'active' => true,
            'form' => '',
            'scripturl' => $credentials['scripturl'],
            'script' => '',
			'action' => '',
			'requestpaymentparamsremotely' => true
        ];

        AuthenticateObj::clearSessionCCICookie();

        // disable the payment gateway if the currency isn't valid.
        if (strpos($this->config['CURRENCYLIST'], $this->session['order']['currencycode']) === false)
        {
            $resultArray['active'] = false;
        }

        // Load in the required templates.
        $smarty = SmartyObj::newSmarty('CreditCardPayment', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

        // populate the smart variable for styling from the config file
        $smarty->assign('styles',[
                                    'formbackgroundcolor' => $this->config['FORMBACKGROUNDCOLOUR'],
                                    'buttonbackgroundcolor' => $this->config['BUTTONBACKGROUNDCOLOUR'],
                                    'buttontextcolor' => $this->config['BUTTONTEXTCOLOUR'],
                                    'buttonbordercolor' => $this->config['BUTTONBORDEROLOUR'],
                                    'inputbackgroundcolor' => $this->config['INPUTBACKGROUNDCOLOUR'],
                                    'inputtextcolor' => $this->config['INPUTTEXTCOLOUR'],
                                    'inputplaceholdercolor' => $this->config['INPUTPLACEHOLDERCOLOUR']
                                ]);

        // display the correct template for mobile and large screen
        if ($this->session['ismobile'])
        {
            $resultArray['script'] = $smarty->fetchLocale('order/PaymentIntegration/Bancard/Bancard_small.tpl');
        }
        else
        {
            $resultArray['script'] = $smarty->fetchLocale('order/PaymentIntegration/Bancard/Bancard_large.tpl');
        }

		// If CSP is active add the directives specified for the gateway.
		if ($this->cspBuilder !== null)
		{
			$this->addCSPDetails();
		}

        return $resultArray;
    }

    protected function defaultCurlOptions()
    {
        $returnArray = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array('Content-Type: application/json','Accept: application/json'),
			CURLOPT_ENCODING => '',
			CURLOPT_TIMEOUT => 30,
			CURLOPT_MAXREDIRS => 1,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CAINFO => UtilsObj::getCurlPEMFilePath()
        ];
        
        return $returnArray;
    }

    public function initialize()
    {

        // variable to store the data to be sent to bancard
        $orderData = [];

        // variable which returns from the function
        $resultJSON = "";

        // create the shop process id from the session reg and the last 4 charcaters of the time.
        // the max size of the shop process id is 15 and the max ref is 11 so we only use the last 4 characters of the time to make a unqiue number
        $shopprocessid = $this->session['ref'] . substr(time(), -4);

        // create the manual callback url from the web brand url
        $fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl']);
        $manualCallBackUrl = $fixedUrlPath . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'] . "&shopprocessid=" . $shopprocessid;
        $credentials = $this->getCredentials();

        if ($this->session['order']['ccidata'] == '')
        {
            $price = $this->session['order']['ordertotaltopay'];

            // bancard requires the decimals to be added but PYG doesn't have decimal places
            // if the price has decimals already don't append .00 to the price. this will only happen if someone has switched on decimal places.
            if (strpos($price, ".") === false)
            {
                $price .= ".00";
            }

            // construct the token for a sing buy
            $token = md5($this->config['PRIVATEKEY'] . $shopprocessid . $price . 'PYG');

            // construct the order data to send to bancard
            $orderData = 
            [
                'public_key' => $this->config['PUBLICKEY'],
                'operation' =>
                [
                    'token' => $token,
                    'shop_process_id' => $shopprocessid,
                    'additional_data' => "",
                    'amount' => $price,
                    'currency' => 'PYG',
                    'description' => LocalizationObj::getLocaleString($this->session['items'][0]['itemproductcollectionname'], $this->session['browserlanguagecode']),
                    'return_url' => $manualCallBackUrl,
                    'cancel_url' => $manualCallBackUrl
                ]
            ];
        }

        // start the CURL connection
        $this->curlConnection = new CurlHandler('json', $this->defaultCurlOptions());

        //Send the data to Bancard
        $paymentRequest = $this->curlConnection->connectionSend($credentials['paymenturl'], '', 'POST', $orderData, 1);

        $paymentParsed = json_decode($paymentRequest, true);

        if (array_key_exists("status", $paymentParsed))
        {
            // make sure it is a success before parsing
            if ($paymentParsed['status'] == 'success')
            {
                $resultJSON = json_encode([
                    'processid' => $paymentParsed['process_id']
                ]);
            }
            else
            {
                error_log($paymentRequest);
            }
        }
        else
        {
            error_log($paymentRequest);
        }

        return $resultJSON;
    }

    public function confirm($pCallBackType)
    {
        // init the cci result array
        $resultArray = $this->cciEmptyResultArray();

        // check the page stats get param to make sure the payment was a success
        if ($this->get['status'] !== 'payment_fail') 
        {
            $serverDate = date('Y-m-d');
            $serverTime = date('H:i:s');
            $this->loadSession = true;
            $credentials = $this->getCredentials();

            // read the shop process id from the get params. this will be used to send to bancard to look up the order
            // and get the order status
            $shopprocessid = $this->get['shopprocessid'];

            $resultArray['webbrandcode'] = $this->session['webbrandcode'];
            $resultArray['currencycode'] = $this->session['order']['currencycode'];
            $resultArray['amount'] = $this->session['order']['ordertotaltopay'];
            $resultArray['formattedamount']  = $this->session['order']['ordertotaltopay'];
            $resultArray['parentlogid'] = -1;
            $resultArray['orderid'] = -1;
            $resultArray['update'] = false;
            $this->updateStatus = false;

            // construct the token for the order query
            $token = md5( $this->config['PRIVATEKEY'] . $shopprocessid . "get_confirmation");

            // construct the packet for order querying
            $confirmBody =
            [
                'public_key' => $this->config['PUBLICKEY'],
                'operation' =>
                [
                    'token' => $token,
                    'shop_process_id' => $shopprocessid
                ]
            ];

            $this->curlConnection = new CurlHandler('json', $this->defaultCurlOptions());

            //query the order to get the details
            $orderQuery = $this->curlConnection->connectionSend($credentials['confirmurl'], '', 'POST', $confirmBody, 1);

            $orderQuery = json_decode($orderQuery, true);

            $ref = $this->get['ref'];
            $transactionID = $orderQuery['confirmation']['ticket_number'];
            $responseCode = $orderQuery['confirmation']['response_code'];
            $responseDescription = $orderQuery['confirmation']['response_description'];
            $authorisationCode = $orderQuery['confirmation']['authorization_number'];

            if ( $orderQuery['confirmation'][ 'extended_response_description'] != null)
            {
                $responseDescription .= $orderQuery['confirmation']['extended_response_description'];
            }

            $authorised = ($orderQuery['status'] === 'success');

            $resultArray['result'] = '';
            $resultArray['ref'] = $ref;
            $resultArray['addressstatus'] = '';
            $resultArray['charges'] = '';
            $resultArray['formattedcharges'] = '';
            $resultArray['authorised'] = $authorised;
            $resultArray['authorisedstatus'] = $authorised;
            $resultArray['transactionid'] = $transactionID;
            $resultArray['formattedtransactionid'] = $transactionID;
            $resultArray['responsedescription'] = $responseDescription;
            $resultArray['authorisationid'] = $authorisationCode;
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
            $resultArray['pendingreason'] = '';
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
            $resultArray['resultisarray'] = false;
            $resultArray['resultlist'] = array();
            $resultArray['showerror'] = false;
            $resultArray['responsecode'] = $responseCode;
        }

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
		$urlInfo = parse_url($this->config[$prefix . 'ENDPOINT']);

		return [
			'script-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'] . (isset($urlInfo['port']) ? ':' . $urlInfo['port'] : ''),
			],
			'frame-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'] . (isset($urlInfo['port']) ? ':' . $urlInfo['port'] : ''),
			],
			'connect-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'] . (isset($urlInfo['port']) ? ':' . $urlInfo['port'] : ''),
			],
		];
	}
}
