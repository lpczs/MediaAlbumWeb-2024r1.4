<?php
use Security\ControlCentreCSP;
require_once __DIR__ . '/Request/CurlHandler.php';

class Unlimit
{
	private static $authURL = 'auth/token';
	private static $paymentURL = 'payments';
	private static $paymentMethodsURL = 'payment_methods';

	static function cURLRequest($pURL, $parameterArray, $method = 'POST', $headers = [], $json = false)
    {
        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL, $pURL);

		if ($method === 'POST') {
			curl_setopt($ch, CURLOPT_POST, true);
			if ($json) {
				$params = json_encode($parameterArray);
			} else {
				$params = http_build_query($parameterArray);
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		} else {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		}

        //execute post
        $result = curl_exec($ch);

		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$err = curl_error($ch);

		if ($err) {
			error_log("cURL Error:" . $err);
		}

        //close connection
        curl_close($ch);

        return ['code' => $httpcode, 'response' => $result];
    }

    static function configure()
    {
		global $gSession;

		$resultArray = [
            'active' => true, 
            'form' => '',
            'scripturl' => '',
            'script' => '',
            'action' => '',
			'requestpaymentparamsremotely' => true
        ];

		$UnlimitConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Unlimit.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
		$paymentMethodList = $UnlimitConfig['PAYMENTMETHODS'];
		$paymentMethodArray = explode(',', $UnlimitConfig['PAYMENTMETHODS']);
		$supportedCurrencies = explode(',', $UnlimitConfig['CURRENCIES']);

		$cspActive = true;
		$nonceValue = '[nonce]';
		$ac_config = UtilsObj::getGlobalValue('ac_config', []);

		if ((array_key_exists('CONTENTSECURITYPOLICY', $ac_config)) && ($ac_config['CONTENTSECURITYPOLICY'] === 'DISABLED'))
		{
			$cspActive = false;
		}

		if (($cspActive) && ($gSession['ismobile'] != true))
		{
			$cspBuilder = ControlCentreCSP::getInstance(UtilsObj::getGlobalValue('ac_config'));
			$nonceValue = $cspBuilder->nonce();
		}

		$form = "
			var paymentmethod = document.getElementsByName('paymentmethods');

			for(var i = 0; i < paymentmethod.length; i++)
			{
				if(paymentmethod[i].value == 'CARD')
				{
					var creditCardContainer = paymentmethod[i].parentNode;
					creditCardContainer.appendChild(document.createTextNode('\u00A0\u00A0\u00A0'));
				";
					if (count($paymentMethodArray) > 1) 
					{
						$form .= "	
									var newscript = document.createElement('script');
									newscript.type = 'text/javascript';
									" . ($cspActive ? "newscript.setAttribute('nonce', '" . $nonceValue . "');" : "") . "
									newscript.text = 'dropdown();';
									creditCardContainer.appendChild(newscript);
								";
					} 
					else
					{
						$form .= "	var hidden = document.createElement('input');
									hidden.type = 'hidden';
									hidden.id = 'paymentgatewaycode';
									hidden.name = 'paymentgatewaycode';
									hidden.value = '" . $paymentMethodArray[0] . "';
									creditCardContainer.appendChild(hidden); 
								";
					}
		$form .= 	"}
	}";

		$action = "validatePayType('" . SmartyObj::getParamValue('CreditCardPayment', 'str_DropDownPleaseSelectAPaymentType') . "')";

		$smarty = SmartyObj::newSmarty('CreditCardPayment', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
		$smarty->assign('paymentMethodList', $paymentMethodList);
		$smarty->assign('nonceValue', $nonceValue);
		$smarty->assign('paymentgatewaycode', $gSession['order']['paymentgatewaycode']);
		$smarty->assign('str_DropDownPleaseSelectAPaymentType', SmartyObj::getParamValue('CreditCardPayment', 'str_DropDownPleaseSelectAPaymentType'));

		$resultArray = [
            'active' => in_array($gSession['order']['currencycode'], $supportedCurrencies),
            'form' => $form,
            'scripturl' => '',
            'script' =>  (($gSession['ismobile'] == true)) ? $smarty->fetchLocale('order/PaymentIntegration/Unlimit/Unlimit_small.tpl') : $smarty->fetchLocale('order/PaymentIntegration/Unlimit/Unlimit_large.tpl'),
            'action' => $action,
			'requestpaymentparamsremotely' => true
        ];

        AuthenticateObj::clearSessionCCICookie();

        return $resultArray;
    }

    static function initialize()
    {
        global $gSession;

        $requestParameters = array();
        $parameters = array();
		$returnArray = [];

		$refresh = (array_key_exists('refresh', $_POST) && $_POST['refresh'] == 'true') ? true : false;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$UnlimitConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Unlimit.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
			$cancelUrl = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$manualCallbackURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

			//Read settings from the config file.
			$server = $UnlimitConfig['SERVER'];
			$terminalCode = $UnlimitConfig['TERMINAL_CODE'];
			$terminalPass = $UnlimitConfig['TERMINAL_PASS'];

			$paramArray = array(
				'grant_type' => 'password',
				'terminal_code' => $terminalCode,
				'password' => $terminalPass
			);

			$tokenResult = self::cURLRequest($server. self::$authURL, $paramArray, 'POST', ['Accept: application/json']);

			$tokenResultParsed = json_decode($tokenResult['response'], true);

			if ($tokenResult['code'] !== 200) {
				//failed
				$returnArray['error'] = $tokenResultParsed['message'];
			}

			$accessToken = '';

			if (array_key_exists('access_token', $tokenResultParsed)) {
				$accessToken = $tokenResultParsed['access_token'];
			}

			$authHeader = ["Authorization: Bearer " . $accessToken, "Accept: application/json", "Content-Type: application/json"]; 

			//Initialise variables
			$orderID = $gSession['ref'];

			if (array_key_exists('ORDERDESCRIPTION', $UnlimitConfig))
        	{
				if ($UnlimitConfig['ORDERDESCRIPTION'] == '')
				{
					$orderData = $orderID;
				}
				else
				{
					$orderData = str_replace($UnlimitConfig['ORDERDESCRIPTION'], '[ordernumber]', $orderID);
				}
			}
			else
			{
				$orderData = $orderID;
			}

			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');
			$currency = $gSession['order']['currencycode'];

			// specify the language that will be used on the payment page.
			$locale = substr(strtolower($gSession['browserlanguagecode']), 0, 2);

			$firstName = $gSession['order']['billingcontactfirstname'];
			$lastName = $gSession['order']['billingcontactlastname'];
			$telephone = $gSession['order']['billingcustomertelephonenumber'];
			$email = $gSession['order']['billingcustomeremailaddress'];
			
			$date_utc = new \DateTime("now", new \DateTimeZone("UTC"));
			$requestId = $gSession['ref'] . '_' . time();

			$requestParameters = array(
				'request' => [
					'id' => $requestId,
					'time' => $date_utc->format(\DateTime::ATOM)
				],
				'merchant_order' => [
					'id' => $requestId,
					'description' => $orderData
				],
				'customer' => [
					'first_name' => $firstName,
					'last_name' => $lastName,
					'full_name' => $firstName.' '.$lastName,
					'email' => $email,
					'home_phone' => $telephone,
					'locale' => $locale
				],
				'payment_method' => $gSession['order']['paymentgatewaycode'],
				'payment_data' => [
					'amount' => $amount,
					'currency' => $currency,
					'generate_token' => null,
					'note' => $orderID,
				],
				'return_urls' => [
					'decline_url' => $manualCallbackURL,
					'cancel_url' => $cancelUrl,
					'success_url' => $manualCallbackURL,
					'inprocess_url' => $manualCallbackURL,
					'return_url' => $manualCallbackURL
				],
			);

			// Filter out empty values in the array. Empty values maybe due to
			// the parameters in the config file having no values.
			// Passing empty values in the array causes Unlimit to fail.
			foreach($requestParameters as $key => $value)
			{
				if ($value != '')
				{
					$parameters[$key] = $value;
				}
			}

			$redirectRequestResponse = self::cURLRequest($server. self::$paymentURL, $parameters, 'POST', $authHeader, true);
			$redirectRequestResponseArray = (array) json_decode($redirectRequestResponse['response'],true);

			$paymentURL = '';
			$queryStringArray = '';
			$error = '';

			if (!array_key_exists('redirect_url', $redirectRequestResponseArray)) {
				$error = $redirectRequestResponseArray['message'];
			} else {
				$paymentURL = $redirectRequestResponseArray['redirect_url'];
				$parsedPaymentUrl = parse_url($paymentURL);
				parse_str($parsedPaymentUrl['query'], $queryStringArray);
			}

			$returnArray['isMobile'] = $gSession['ismobile'];
			$returnArray['refresh'] = $refresh;
			$returnArray['error'] = $error;
			$returnArray['payment_url'] = $paymentURL;
			$returnArray['parameter'] = $queryStringArray;
			$returnArray['accessToken'] = $accessToken;
			$returnArray['requestId'] = $requestId;
			$returnArray['merchantOrderId'] = $requestId;
			$returnArray['manualCallbackURL'] = $manualCallbackURL;
			$returnArray['cancelURL'] = $cancelUrl;
			$returnArray['paymentMethod'] = $gSession['order']['paymentgatewaycode'];
			$returnArray['statusEndpoint'] = UtilsObj::correctPath($gSession['webbrandweburl']) . "PaymentIntegration/Unlimit/getPaymentStatus.php";
			$returnArray['unlimitServer'] = UtilsObj::correctPath($UnlimitConfig['SERVER']);

			return json_encode($returnArray,true);
    	}
        else
        {
            // the user has clicked the back button
            AuthenticateObj::clearSessionCCICookie();

            $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
            $smarty->assign('server', $cancelReturnPath);

            if ($gSession['ismobile'] == true)
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

    static function cancel()
    {
        global $gSession;

        $resultArray = Array();
        $resultArray['result'] = '';
        $resultArray['ref'] = $gSession['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function confirm($callback)
    {
     	global $gSession;

		$returnParams = [];

		if ($callback === 'automatic') {
			$returnParams = json_decode(@file_get_contents('php://input'),true);
		} else {
			if (isset($_POST['payload'])) {
				$returnParams = (array) json_decode($_POST['payload'],true);
			}
		}

		if (!isset($returnParams['merchant_order'])) {
			return [
				'ref' => (isset($_GET['ref'])) ? $_GET['ref'] : -1,
				'authorised' => true,
				'update' => false,
				'parentlogid' => -1,
				'orderid' => -1,
				'paymentreceived' => false,
				'result' => '',
				'authorisedstatus' => 0,
				'showerror' => false,
				'updateccilog' => false
			];
		}

     	$resultArray = Array(); 
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $showError = false;
        $update = false;
		$paymentReceived = false;

		//Session Reference
		$ref = explode('_', $returnParams['merchant_order']['id'])[0];
		$gSession = DatabaseObj::getSessionData($ref);
		$transactionId = $returnParams['merchant_order']['id'];

		//get Config
		$UnlimitConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Unlimit.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

     	//Check CCILOG to see if this is an update
		$cciLogEntry = PaymentIntegrationObj::getCciLogEntryFromTransactionID($transactionId);

		if (empty($cciLogEntry))
		{
			// no entry yet, this must be the first callback
			// we do have a session
			$webbrandcode = $gSession['webbrandcode'];
			$currencyCode = $gSession['order']['currencycode'];
			$amount = $gSession['order']['ordertotaltopay'];
			$update = false;
			$parentLogId = -1;
			$orderId = -1;
		}
		else
		{
			// we already have an entry, this must be a status update
			// we won't have a session
			$webbrandcode = $cciLogEntry['webbrandcode'];
			$currencyCode = $cciLogEntry['currencycode'];
			$amount = $cciLogEntry['formattedamount'];
			$update = true;
			$parentLogId = $cciLogEntry['id'];
			$orderId = $cciLogEntry['orderid'];
		}

		switch ($returnParams['payment_data']['status'])
		{
			case 'COMPLETED':
					$authorised = true;
					$authorisedStatus = 1;
					$paymentReceived = true;
			break;
			case 'AUTHORIZED':
					$authorised = true;
					$authorisedStatus = 2;
					$paymentReceived = false;
			break;
		}

		$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime =  date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($UnlimitConfig, $serverTimestamp);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] ='';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $transactionId;
        $resultArray['formattedtransactionid'] = $returnParams['payment_data']['id'];
        $resultArray['responsecode'] = $returnParams['payment_data']['status'];
        $resultArray['responsedescription'] = $returnParams['payment_data']['status'];
        $resultArray['authorisationid'] = (array_key_exists('auth_code', $returnParams['payment_data'])) ? $returnParams['payment_data']['auth_code'] : $returnParams['payment_data']['id'];  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = (array_key_exists('auth_code', $returnParams['payment_data'])) ? $returnParams['payment_data']['auth_code'] : $returnParams['payment_data']['id'];
        $resultArray['bankresponsecode'] = '';
        $resultArray['cardnumber'] = (array_key_exists('card_account', $returnParams)) ? $returnParams['card_account']['masked_pan'] : '';
        $resultArray['formattedcardnumber'] = (array_key_exists('card_account', $returnParams)) ? $returnParams['card_account']['masked_pan'] : '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = $returnParams['payment_method'];
        $resultArray['paymenttime'] = $serverTime;
		$resultArray['paymentreceived'] = $paymentReceived;
        $resultArray['formattedpaymentdate'] = $serverTimestamp;
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = '';
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = $returnParams['customer']['email'];
        $resultArray['business'] = '';
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = $returnParams['payment_method'];
        $resultArray['settleamount'] = '';
        $resultArray['currencycode'] = $currencyCode;
        $resultArray['webbrandcode'] = $webbrandcode;
        $resultArray['charityflag'] = '';
        $resultArray['threedsecurestatus'] = (array_key_exists('is_3d', $returnParams['payment_data'])) ? (($returnParams['payment_data']['is_3d'] === true) ? 'Y' : 'N') : 'N';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = $orderId;
        $resultArray['parentlogid'] = $parentLogId;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = $showError;
		$resultArray['updateccilog'] = false;

        return $resultArray;

    }

    static function generateHash($pParams, $pPassPhrase)
    {
        $hash = '';
        ksort($pParams);

        foreach ($pParams as $key => $val)
        {
    		if ($key != 'SHASIGN')
    		{
    			if ($val != "")
    			{
    				$hash .= $key."=" . $val . $pPassPhrase;
    			}
			}
		}

		$generatedHash = sha1($hash);

		$generatedHash = strtoupper($generatedHash);

        return $generatedHash;
    }


    static function getReturnParams($pCallback)
    {
		$resultArray = Array();

		if ($pCallback == 'automatic')
		{
			$requestType = $_POST;
		}
		else
		{
			$requestType = $_GET;
		}

		foreach($requestType as $key => $value)
		{
			if ($key != 'ref' && $key != 'fsaction')
			{
				$key = strtoupper($key);
				$resultArray[$key] = $value;
			}
		}

		ksort($resultArray);

		return $resultArray;
    }

	// Not using currently but may want to in future to get logo's etc 
	static function getPaymentMethodList($server, $ref, $authHeader)
	{
		$smarty = SmartyObj::newSmarty('CreditCardPayment');

		//get payment methods
		$paymentMethodsResponse = self::cURLRequest($server . self::$paymentMethodsURL . '?request_id=' . $ref, [], 'GET', $authHeader);
		if ($paymentMethodsResponse['code'] !== 200) {
			//failed
			//return [];
		}
		$paymentMethodsResponseArray = json_decode($paymentMethodsResponse['response'],true);

		//example response
		// $paymentMethodsResponseArray = json_decode('{
		// 	"payment_methods": [
		// 	  {
		// 		"category": "eWallet",
		// 		"name": "ALIPAYPLUS",
		// 		"supported_payment_methods": [
		// 		  {
		// 			"logo": "https://gw.alipay.com/icon/medium/default/GCASH.svg",
		// 			"name": "GCash"
		// 		  }
		// 		]
		// 	  },
		// 	  {
		// 		"category": "Cards",
		// 		"name": "BANKCARD",
		// 		"brands": [
		// 			"maestro",
		// 			"mastercard",
		// 			"visa"
		// 		]
		// 	  }
		// 	],
		// 	"payout_methods": [
		// 	  {
		// 		"category": "eWallet",
		// 		"name": "ALIPAYPLUS"
		// 	  }
		// 	]
		// }',true);

		$fullPaymentMethodList = [];

		foreach ($paymentMethodsResponseArray["payment_methods"] as $category) {
			if (array_key_exists('supported_payment_methods', $category)) {
				foreach ($category["supported_payment_methods"] as $payment_method) {
					$fullPaymentMethodList[] = [
						"id" => $payment_method["name"], 
						"type" => $category["category"],
						"parent" => $category["name"],
						"name" => $category["category"] . " - " . $payment_method["name"],
						"logo" => $payment_method["logo"]
					];
				}
			} else if (array_key_exists('brands', $category)) {
				foreach ($category["brands"] as $brand) {
					$fullPaymentMethodList[] = [
						"id" => $category["name"], 
						"type" => $category["category"],
						"parent" => $category["name"],
						"name" => $category["name"] . " - " . $brand,
						"logo" => ""
					];
				}
			} else {
				$fullPaymentMethodList[] = [
					"id" => $category["name"], 
					"type" => $category["category"],
					"parent" => $category["name"],
					"name" => $category["name"],
					"logo" => ""
				];
			}
		}

		return $fullPaymentMethodList;
	}
}

?>