<?php
use Security\ControlCentreCSP;

class PayBoxObj
{
    static function configure()
    {
        global $gSession;
        $resultArray = Array();

        AuthenticateObj::clearSessionCCICookie();

        $PayBoxConfig = PaymentIntegrationObj::readCCIConfigFile('../config/paybox.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
        $currencyList = $PayBoxConfig['CURRENCIES'];
        $currency = $gSession['order']['currencyisonumber'];
        $active = true;
        $form = "";
        $script = "";
        $action = "";

        // test for PayBox supported currencies
        if (strpos($currencyList, $currency) === false)
        {
            $active = false;
        }
        else
        {
			if ((array_key_exists('PAYMENTOPTIONS', $PayBoxConfig)) && ($PayBoxConfig['PAYMENTOPTIONS'] != ''))
	        {
	        	// parse the PAYMENTSOPTIONS config setting into an array
				$paymentMethodList = self::paymentMethodList($PayBoxConfig['PAYMENTOPTIONS']);
				$paymentMethodCount = count($paymentMethodList);

				// if there is only one payment method configured then don't show the dropdown box.
				if ($paymentMethodCount == 1)
				{
					$gSession['order']['paymentgatewaycode'] = $paymentMethodList[0]['id'];
				}
				else
				{
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

					// build the form and script needed for selecting multiple payment types

					$form = "
						var paymenthod = document.getElementsByName('paymentmethods');

						for (var i = 0; i < paymenthod.length; i++)
						{
							if (paymenthod[i].value == 'CARD')
							{
								creditCardContainer = paymenthod[i].parentNode;
								creditCardContainer.appendChild(document.createTextNode('\u00A0\u00A0\u00A0'));
								newscript = document.createElement('script');
								newscript.type = 'text/javascript';
								" . ($cspActive ? "newscript.setAttribute('nonce', '" . $nonceValue . "');" : "") . "
								newscript.text = 'PayBoxDropdown();';
								creditCardContainer.appendChild(newscript);
							}
						}";


					$script = "

					function PayBoxDropdown()
					{
						var selectorOuterDiv = document.createElement('div');
		                selectorOuterDiv.setAttribute('class', 'wizard-dropdown');

		                var selector = document.createElement('select');
						selector.id = 'paymentgatewaycode';
						selector.name = 'paymentgatewaycode';
		                selector.setAttribute('class', 'wizard-dropdown');
						selector.setAttribute('data-decorator', 'forceSelectCard');
						selector.addEventListener('change', function(event) {
							forceSelectCard();
						});

		                selectorOuterDiv.appendChild(selector);
		                creditCardContainer.appendChild(selectorOuterDiv);

						var option = document.createElement('option');
						option.value = '';
						option.appendChild(document.createTextNode('-- ". SmartyObj::getParamValue('CreditCardPayment', 'str_DropDownPleaseSelectAPaymentType') ." --'));
						selector.appendChild(option);

						// Assign the array of PaymentMethodList from the config file
						PayBoxPayTypeArray = new Array();
						";

						for($i = 0; $i < $paymentMethodCount; $i++)
						{
							 $script .= "PayBoxPayTypeArray[" . $i . "]  = new payType('" . $paymentMethodList[$i]['name'] . "', '" . $paymentMethodList[$i]['id'] . "');";
						}

						$script .="

						if (PayBoxPayTypeArray)
						{
							for (var i = 0; i < PayBoxPayTypeArray.length; i++)
							{
								var option = document.createElement('option');
								option.value = PayBoxPayTypeArray[i].id;

								if (option.value == '" . $gSession['order']['paymentgatewaycode'] . "')
								{
									option.selected = 'selected';
								}

								option.appendChild(document.createTextNode(PayBoxPayTypeArray[i].name));
								selector.appendChild(option);

							}
						}
					}";

					$action = "validatePayType('Please select a payment method')";
				}
	        }
	    }

        $resultArray['active'] = $active;
        $resultArray['form'] = $form;
        $resultArray['scripturl'] = '';
        $resultArray['script'] = $script;
        $resultArray['action'] = $action;

        return $resultArray;
    }

    static function paymentMethodList($pPaymentOptions)
	{
		$smarty = SmartyObj::newSmarty('CreditCardPayment');

		$paymentOptionsArray = explode(";", $pPaymentOptions);
		$countOptions = count($paymentOptionsArray);
		$i = 0;
		for ($i = 0; $i < $countOptions; $i++)
		{
			list($paymentType, $paymentTypeArray) = explode(":", $paymentOptionsArray[$i]);
			$cardTypeArray = explode(",", $paymentTypeArray);
			$countCardTypes = count($cardTypeArray);
			$n = 0;
			for ($n = 0; $n < $countCardTypes; $n++)
			{
				$fullPaymentMethodList[] = Array("id" => $paymentType . ":" . $cardTypeArray[$n], "name" => $smarty->get_config_vars('str_OrderPayBox_' . $cardTypeArray[$n]));
			}
		}

		return $fullPaymentMethodList;
	}

    static function initialize()
    {
        global $gSession;

        $requestParameters = array();
        $parameters = array();

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$PayBoxConfig = PaymentIntegrationObj::readCCIConfigFile('../config/paybox.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$returnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
			$autoReturnPath = UtilsObj::correctPath($gSession['webbrandwebroot']) . 'PaymentIntegration/PayBox/PayBoxCallback.php?ref=' . $gSession['ref'];

			//Read settings from the config file.
			$server = self::checkServer(explode(',', $PayBoxConfig['SERVERS']), $PayBoxConfig['SCRAPEENDPOINT']);

			if ($server != "")
			{
				//Initialise variables
				$orderID = $gSession['ref'] . '_'. time();

				$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
				// $currency = $gSession['order']['currencycode'];
				$currency = $gSession['order']['currencyisonumber'];
				$email = $gSession['order']['billingcustomeremailaddress'];

				// specify the language that will be used on the payment page.
				$locale = strtolower($gSession['browserlanguagecode']);
				$locale = substr($locale, 0, 2);
				$languageList = 'en,nl,fr,de,es,it,sv,pt';

				if (strpos($languageList, $locale) === false)
				{
					$displayLang = 'FRA';
				}
				else
				{
					switch ($locale)
					{
						case 'en':
							$displayLang = 'GBR';
						break;
						case 'nl':
							$displayLang = 'NLD';
						break;
						case 'fr':
							$displayLang = 'FRA';
						break;
						case 'de':
							$displayLang = 'DEU';
						break;
						case 'es':
							$displayLang = 'ESP';
						break;
						case 'it':
							$displayLang = 'ITA';
						break;
						case 'sv':
							$displayLang = 'SWE';
						break;
						case 'pt':
							$displayLang = 'PRT';
						break;
					}
				}

				$dateTime = date("c");

				$source = "HTML";

				if ($gSession['ismobile'] == true)
				{
					$source = 'XHTML';
				}

				$requestParameters = array(
					'PBX_SITE'	=> $PayBoxConfig['SITE'],
					'PBX_RANG' => $PayBoxConfig['RANG'],
					'PBX_IDENTIFIANT' => $PayBoxConfig['IDENTIFIANT'],
					'PBX_TOTAL' => $amount,
					'PBX_DEVISE' => $currency,
					'PBX_CMD' => $orderID,
					'PBX_PORTEUR' => $email,
					'PBX_RETOUR' => "cardtype:C;cost:M;ordernumber:R;auth:A;status:E;ts:Q;transnumber:S;cardhash:H;paytype:P;tdstatus:F;tdenrolled:O;sig:K",
					'PBX_HASH' => 'SHA512',	
					'PBX_TIME' => $dateTime,
					'PBX_LANGUE' => $displayLang,
					'PBX_EFFECTUE' => $returnPath,
					'PBX_ANNULE' => $cancelReturnPath,
					'PBX_REPONDRE_A' => $autoReturnPath,
					'PBX_ATTENTE' => $returnPath,
					'PBX_REFUSE' => $returnPath,
					'PBX_SOURCE' => $source,
					'PBX_RUF1' => 'POST'
				);

				if ($gSession['order']['paymentgatewaycode'] != '')
				{
					list($paymentType, $cardType) = explode(":", $gSession['order']['paymentgatewaycode']);
					$requestParameters['PBX_TYPEPAIEMENT'] = $paymentType;
					$requestParameters['PBX_TYPECARTE'] = $cardType;

					switch ($cardType)
					{
						case "SOFINCO":
						case "COFINOGA":
						case "CDGP":
						{
							if ((array_key_exists('CODEFAMILLE', $PayBoxConfig)) && ($PayBoxConfig['CODEFAMILLE'] != ''))
							{
								$requestParameters['PBX_CODEFAMILLE'] = $PayBoxConfig['CODEFAMILLE'];
							}

							break;
						}
						case "NETCDGP":
						{
							$requestParameters['PBX_NETRESERVE_DATA'] = self::trimValue($gSession['shipping'][0]['shippingcontactfirstname'], 25) ."#";
							$requestParameters['PBX_NETRESERVE_DATA'] .= self::trimValue($gSession['shipping'][0]['shippingcontactlastname'], 25) ."#";
            				$requestParameters['PBX_NETRESERVE_DATA'] .= self::trimValue($gSession['shipping'][0]['shippingcustomeraddress1'], 25) ."#";
            				$requestParameters['PBX_NETRESERVE_DATA'] .= self::trimValue($gSession['shipping'][0]['shippingcustomeraddress2'], 25) ."#";
							$requestParameters['PBX_NETRESERVE_DATA'] .= self::trimValue($gSession['shipping'][0]['shippingcustomerpostcode'], 10) . "#";
            				$requestParameters['PBX_NETRESERVE_DATA'] .= self::trimValue($gSession['shipping'][0]['shippingcustomercity'], 25) ."#";
            				$requestParameters['PBX_NETRESERVE_DATA'] .= self::trimValue($gSession['shipping'][0]['shippingcustomercountrycode'], 2) . "#";
							$requestParameters['PBX_NETRESERVE_DATA'] .= self::trimValue($email, 50) . "#";
            				$requestParameters['PBX_NETRESERVE_DATA'] .= self::trimValue($gSession['shipping'][0]['shippingcustomertelephonenumber'], 25);

							break;
						}
						case "PAYPAL":
						{

							$customerName = $gSession['shipping'][0]['shippingcontactfirstname'] . " " . $gSession['shipping'][0]['shippingcontactlastname'];

							if (strlen($customerName) > 32)
							{
								$customerName = substr($gSession['shipping'][0]['shippingcontactfirstname'], 0, 1) . " ";

								if (strlen($gSession['shipping'][0]['shippingcontactlastname']) > 30)
								{
									$customerName .= substr($gSession['shipping'][0]['shippingcontactlastname'], 0, 30);
								}
								else
								{
									$customerName .= $gSession['shipping'][0]['shippingcontactlastname'];
								}
							}

							$requestParameters['PBX_PAYPAL_DATA'] = $customerName . "#";

            				$requestParameters['PBX_PAYPAL_DATA'] .= self::trimValue($gSession['shipping'][0]['shippingcustomeraddress1'], 100) ."#";
            				$requestParameters['PBX_PAYPAL_DATA'] .= self::trimValue($gSession['shipping'][0]['shippingcustomeraddress2'], 100) ."#";
            				$requestParameters['PBX_PAYPAL_DATA'] .= self::trimValue($gSession['shipping'][0]['shippingcustomercity'], 40) ."#";
            
				            if ($gSession['shipping'][0]['shippingcustomerstate'] != '')
				            {
								if ($gSession['shipping'][0]['shippingcustomercountrycode'] == 'US')
								{
									$requestParameters['PBX_PAYPAL_DATA'] .= $gSession['shipping'][0]['shippingcustomerregioncode'] . "#";
								}
								else
								{
									$requestParameters['PBX_PAYPAL_DATA'] .= self::trimValue($gSession['shipping'][0]['shippingcustomerstate'], 40) . "#";
								}
				            }
				            else
				            {
				                $requestParameters['PBX_PAYPAL_DATA'] .= self::trimValue($gSession['shipping'][0]['shippingcustomercounty'], 40) . "#";
				            }

							$requestParameters['PBX_PAYPAL_DATA'] .= self::trimValue($gSession['shipping'][0]['shippingcustomerpostcode'], 20) . "#";
							$requestParameters['PBX_PAYPAL_DATA'] .= self::trimValue($gSession['shipping'][0]['shippingcustomercountrycode'], 2) . "#";
							$requestParameters['PBX_PAYPAL_DATA'] .= self::trimValue($gSession['shipping'][0]['shippingcustomertelephonenumber'], 20) . "#";

            				$productname = UtilsObj::encodeString(LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true),false);

				            if ($gSession['items'][0]['itemqty'] == 1)
				            {
				                $requestParameters['PBX_PAYPAL_DATA'] .= self::trimValue($productname, 127);
				            }
				            else
				            {
				                $requestParameters['PBX_PAYPAL_DATA'] .= self::trimValue($gSession['items'][0]['itemqty'] . ' x ' . $productname, 127);
				            }

							break;
						}
					}

				}

				$parameters = "";
				foreach($requestParameters as $key => $value)
				{
					$parameters .= $key . '=' . $value . '&';
				}

				// strip off the last ampersand
				$parameters = substr($parameters, 0, -1);

				// generate the hash using the key provided from PayBox
				$requestParameters['PBX_HMAC'] = self::generateHash($parameters, $PayBoxConfig['HMACKEY']);

				// define Smarty variables

				if ($gSession['ismobile'] == true)
				{
					$smarty->assign('payment_url', $server . $PayBoxConfig['SMALLENDPOINT']);
				}
				else
				{
					$smarty->assign('payment_url', $server . $PayBoxConfig['LARGEENDPOINT']);
				}

				$smarty->assign('method', "POST");
				$smarty->assign('cancel_url', $cancelReturnPath);
				$smarty->assign('parameter', $requestParameters);

				AuthenticateObj::defineSessionCCICookie();
				$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
				$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

				// set the ccidata to remember we have jumped to DIBS
				$gSession['order']['ccidata'] = 'start';
				DatabaseObj::updateSession();

				$smarty->cachePage = true; // allow the page to be cached so that the browser back button works correctly
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
	        else
	        {
	        	error_log('all servers down');
	        }
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

    static function trimValue($pValue, $pSize)
    {
    	$returnValue = $pValue;

    	if (strlen($pValue) > $pSize)
    	{
    		$returnValue = substr(0, $pSize);
    	}

    	return $returnValue;
    }

    static function checkServer($pServersArray, $pEndPoint)
    {

    	$serverOK = "";

		foreach ($pServersArray as $server)
		{
			$doc = new DOMDocument(); 
			$doc->loadHTMLFile($server . $pEndPoint);
			$server_status = "";
			$element = $doc->getElementById('server_status'); 

			if ($element)
			{
				$server_status = $element->textContent;
			}

			if ($server_status == "OK")
			{
				// Server is up and services are available 
				$serverOK = $server;
				break;
			}
		}

		return $serverOK;

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

	static function extractSignature($pQueryString, &$pNewQueryString, &$pSignature, $pIsURL ) 
	{
	    $pos = strrpos($pQueryString, '&');
	    $pNewQueryString = substr($pQueryString, 0, $pos);
	    $pos= strpos($pQueryString, '=', $pos) + 1;
	    $pSignature = substr($pQueryString, $pos);
	    
	    if ($pIsURL)
	    {
	    	$pSignature = urldecode($pSignature);
	    }

	    $pSignature = base64_decode($pSignature);
	}

	static function loadKey($pKeyFile) 
	{
	    $fp = false;
	    $filedata = false;
	    $key = '';

	    $fsize = filesize($pKeyFile);

	    if (!$fsize)
	    {
	    	$key = '';
	    }
	    else
	    {
	    	$fp = fopen($pKeyFile, 'r');

	    	if (!$fp)
	    	{
	    		$key = '';
	    	}
	    	else
	    	{
		    	$filedata = fread($fp, $fsize);
		    	fclose($fp);

		    	if (!$filedata)
		    	{
		    		$key = '';
		    	}
		    	else
		    	{
		    		$key = openssl_pkey_get_public($filedata);
		    	}
		    }
	    }

	    return $key;
	}

    static function confirm($callback)
    {
     	global $gSession;

     	$resultArray = Array();
        $result = '';
        $authorised = false;
        $paymentreceived = 0;
        $update = false;
        $queryString = '';
        $payboxSignature = '';
        $signatureString = '';
        $showError = false;
        $extraData = array('SIGNATURESTATUS' => 'FAILED');

     	$PayBoxConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayBox.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

		// if the callback is manual then the whole query string can be used
		// if the callback is automatic then we need to remove fsaction parameter since paybox would not have included this in the 
		// signature
     	if ($callback == 'manual')
     	{
     		$queryString = $_SERVER['QUERY_STRING'];
     	}
     	else
     	{
     		$queryString = http_build_query($_POST);
     	}

     	// check the signature provided on the query string matches the one we make based on the paybox publickey
		self::extractSignature($queryString, $signatureString, $payboxSignature, true);

		if ($signatureString != '')
		{
			// load the paybox public key using the config setting as the file name
			$payBoxKey = self::loadKey(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PayBox' . DIRECTORY_SEPARATOR . $PayBoxConfig['PUBLICKEY']);

			if ($payBoxKey)
			{
				$keyReturn = openssl_verify($signatureString, $payboxSignature, $payBoxKey);

				if ($keyReturn)
				{
					$extraData['SIGNATURESTATUS'] = 'OK';
				}

				openssl_free_key($payBoxKey);
			}
		}

     	//Put return parameters into an array.
     	$returnParams = self::getReturnParams($callback);

     	$orderID = $returnParams['ORDERNUMBER'];

     	// parse the order id to ge the session ref
     	list($ref, $time) = explode("_", $orderID);

     	//Check CCILOG to see if this is an update
     	$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

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

		if ($extraData['SIGNATURESTATUS'] == 'OK')
		{
			if (($returnParams['STATUS'] == '9999') || ($returnParams['STATUS'] == '0000'))
			{
				if ($returnParams['STATUS'] == '0000')
				{
					$paymentreceived = 1;
				}

				$authorised = true;
			}
		}
     	else
     	{
     		// SHA check failed
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': SHAKEY';
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': SHA check failed';
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $returnParams['TRANSNUMBER'];
			$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $returnParams['ORDERNUMBER'];
			$resultArray['errorform'] = 'error.tpl';
			$showError = true;
			$paymentreceived = 0;
     	}

		$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime =  date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($PayBoxConfig, $serverTimestamp, '', $extraData);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] ='';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $paymentreceived;
        $resultArray['transactionid'] = $returnParams['TRANSNUMBER'];
        $resultArray['formattedtransactionid'] = $returnParams['TRANSNUMBER'];
        $resultArray['responsecode'] = $returnParams['STATUS'];
        $resultArray['responsedescription'] = '';

        if (array_key_exists('AUTH', $returnParams))
        {
        	$resultArray['authorisationid'] = urldecode($returnParams['AUTH']);  // this is our unique ID, not the real order ID
        	$resultArray['formattedauthorisationid'] = urldecode($returnParams['AUTH']);
        }
        else
        {
			$resultArray['authorisationid'] = '';
			$resultArray['formattedauthorisationid'] = '';
        }

        $resultArray['bankresponsecode'] = $returnParams['STATUS'];
        $resultArray['cardnumber'] = $returnParams['CARDHASH'];
        $resultArray['formattedcardnumber'] = $returnParams['CARDHASH'];
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = $returnParams['PAYTYPE'] . ":" . $returnParams['CARDTYPE'];
        $resultArray['paymenttime'] = $serverTime;
		$resultArray['paymentreceived'] = $paymentreceived;
		$resultArray['formattedpaymentdate'] = $serverTimestamp;
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
        $resultArray['currencycode'] = $currencyCode;
        $resultArray['webbrandcode'] = $webbrandcode;
        $resultArray['charityflag'] = '';
        $resultArray['threedsecurestatus'] = '';

        if (array_key_exists('TDENROLLED', $returnParams))
        {
	        if ($returnParams['TDENROLLED'] == 'Y')
	        {
	        	$resultArray['threedsecurestatus'] = $returnParams['TDSTATUS'];
	        }
	    }

        $resultArray['cavvresponsecode'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = $orderId;
        $resultArray['parentlogid'] = $parentLogId;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = $showError;

        return $resultArray;

    }

    static function generateHash($pParams, $pKey)
    {
       	// If the key is in ASCII format, convert it to binary
       	$binKey = pack("H*", $pKey);

       	$generatedHash = strtoupper(hash_hmac('sha512', $pParams, $binKey));

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
			$key = strtoupper($key);
			$resultArray[$key] = $value;
		}

		ksort($resultArray);

		return $resultArray;
    }
}

?>