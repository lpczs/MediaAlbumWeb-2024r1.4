<?php

use PagSeguro\Configuration\Configure;
use PagSeguro\Domains\Requests\Payment;
use PagSeguro\Enum\Shipping\Type;
use PagSeguro\Library;

Library::initialize();

require_once __DIR__ . '/TaopixAbstractGateway.php';
require_once __DIR__ . '/Request/CurlHandler.php';

class Pagseguro extends TaopixAbstractGateway
{
	
	public function __construct($pConfig, &$pSession, &$pGetVars, &$pPostVars)
	{
		parent::__construct($pConfig, $pSession, $pGetVars, $pPostVars);
		$this->curlConnection = new CurlHandler('PAGSEGUROPOST', $this->defaultCurlOptions());
		$this->curlServer = $this->config['POSTSERVER'] .'?email=' . $this->config['ACCOUNTEMAIL'] . '&token=' . $this->config['TOKEN'];
	}
	
    public function configure()
    {
		// Get smarty object.
        $smarty =  SmartyObj::newSmarty('Order', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

        if($this->config['TRANSACTIONMODE'] == 'TEST')
        {
            $fallBackUrl = $this->config['SANDBOXFALLBACKURL'];
			
			$resultArray = [
                'active' => true,
                'form' => '',
                'scripturl' => $this->config['SANDBOXSCRIPTURL'],
                'script' => '',
                'action' => '',
                'gateways' => [],
				'requestpaymentparamsremotely' => true
            ];    
        }
        else
        {
            $fallBackUrl = $this->config['LIVEFALLBAKCURL'];
			
			$resultArray = [
                'active' => true,
                'form' => '',
                'scripturl' => $this->config['LIVESCRIPTURL'],
                'script' => '',
                'action' => '',
                'gateways' => [],
				'requestpaymentparamsremotely' => true
            ];
        }

        $smarty->assign('fallbackurl', $fallBackUrl);

        //Currency is set to what is enabled in the merchant account
        if (strpos($this->config['CURRENCYLIST'], $this->session['order']['currencycode']) === false)
        {
            $resultArray['active'] = false;
        }

        AuthenticateObj::clearSessionCCICookie();

		if($this->session['ismobile'])
        {
            $resultArray['script'] = $smarty->fetchLocale('order/PaymentIntegration/Pagseguro/Pagseguro_small.tpl');
        }
		else
        {
            $resultArray['script'] = $smarty->fetchLocale('order/PaymentIntegration/Pagseguro/Pagseguro_large.tpl');
        }

		// If CSP is active add the directives specified for the gateway.
		if ($this->cspBuilder !== null)
		{
			$this->addCSPDetails();
		}

        return $resultArray;
    }

    protected function formatPriceToSend($pPrice)
	{
		return number_format($pPrice, 2, '.', '');
	}

    public function initialize()
    {
		//See if ssl is enabled
		$sslEnabled = $_SERVER['HTTPS'] ? true : false;
        $fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl']);
        $cancelURL = $fixedUrlPath . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];
		
		$smarty = SmartyObj::newSmarty('Order', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

        if ($this->session['order']['ccidata'] == '')
        {

			//Manual callback
            $manualCallBackUrl = $fixedUrlPath . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'];
            
			//Set the evironment
			Configure::setEnvironment($this->config['ENVIRONMENT']);//production or sandbox
           
            Configure::setAccountCredentials(
                $this->config['ACCOUNTEMAIL'],
                $this->config['TOKEN']
            );
            
			//Initialise the new paymment
            $payment = new Payment();

            //Add the items and the price
			$payment->addItems()->withParameters(
			    1,
                LocalizationObj::getLocaleString($this->session['items'][0]['itemproductcollectionname'], $this->session['browserlanguagecode']),
                1,
                $this->formatPriceToSend($this->session['order']['ordertotaltopay'] - $this->session['order']['ordertotalshippingsellafterdiscount'])
            );
			
			//If the order has been placed on a mobile we still need the redirect URL
			
            if($this->session['ismobile'] || !$sslEnabled)
			{
				$payment->setRedirectUrl($manualCallBackUrl);
			}
			
			/**
			 * Pre populate the CPF number in the light box
			 */
			
			if (($this->session['order']['billingregisteredtaxnumber'] != '') && ($this->session['order']['billingregisteredtaxnumbertype'] != 0))
			{
				$taxNumberSuffix = ($this->session['order']['billingregisteredtaxnumbertype'] == 1) ? 'CPF' : 'CNPJ';
				
				$payment->setSender()->setDocument()->withParameters(
					$taxNumberSuffix,
					$this->session['order']['billingregisteredtaxnumber']
				);
			}

			$payment->setCurrency($this->session['order']['currencycode']);
            $payment->setReference($this->session['ref']);
           
			//The URL which pagseguro will call back to (Automatic) 
			$payment->setNotificationUrl($this->config['NOTIFICATIONURL']);

            $addressArray = UtilsAddressObj::getAdditionalAddressFields($this->session['shipping'][0]['shippingcustomercountrycode'],$this->session['shipping'][0]['shippingcustomeraddress4']);
            
            $payment->setShipping()->setAddress()->withParameters(
                $addressArray['add41'],
                $addressArray['add42'],
                $addressArray['add43'],
                $this->session['shipping'][0]['shippingcustomerpostcode'],
                $this->session['shipping'][0]['shippingcustomercity'],
                $this->session['shipping'][0]['shippingcustomerregioncode'],
                $this->session['shipping'][0]['shippingcustomercountrycode']
            );

            $senderName = $this->session['order']['billingcontactfirstname'] . ' ' . $this->session['order']['billingcontactlastname'];
            // senderName limit in Pageseguro is 50 characters.
            if (strlen($senderName) > 50) {
                $senderName = substr($senderName, 0, 50);
            }

            $payment->setSender()->setName($senderName);
            $payment->setSender()->setEmail($this->session['order']['billingcustomeremailaddress']);
            $payment->setShipping()->setCost()->withParameters($this->formatPriceToSend($this->session['order']['ordertotalshippingsellafterdiscount']));
            $payment->setShipping()->setType()->withParameters(Type::NOT_SPECIFIED);

			try{
				$onlyCheckoutCode = true;
				$result = $payment->register(Configure::getAccountCredentials(), $onlyCheckoutCode);
				
				//We should only display the light box if ssl is enabled
			
				$returnArray = 
				[
					'result' => 1,
					'transactioncode' => $result->getCode(),
					'sslenabled' => $sslEnabled,
					'manualurl' => $manualCallBackUrl,
					'ismobile' => $this->session['ismobile']
				];

				return json_encode($returnArray);
				
			}catch(Exception $e){
				// error redirect to cancel
				AuthenticateObj::clearSessionCCICookie();
				$smarty->assign('server', $cancelURL);

				// Convert Pagseguro error xml into a simplexml object.
				$errorXml = simplexml_load_string($e->getMessage(), 'SimpleXMLElement', LIBXML_NOCDATA);

				// Convert simple xml object to plain php array.
				$errors = json_decode(json_encode((array)$errorXml), true);
				
				// log the issue
				$returnArray = [
					'errors' => $errors['error']
				];

				PaymentIntegrationObj::logPaymentGatewayData($this->config, DatabaseObj::getServerTime(), $e, 'Failed on sending initial curl data');

				return json_encode($returnArray);
		
            }
        }
       
    }

	protected function defaultCurlOptions()
	{
		$return = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8;'),
			CURLOPT_ENCODING => '',
			CURLOPT_TIMEOUT => 30,
			CURLOPT_MAXREDIRS => 1,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CAINFO => UtilsObj::getCurlPEMFilePath()
		];
		if((isset($this->config['LOGOUTPUT'])) && ($this->config['LOGOUTPUT'] === 'true'))
		{
			$logHandle = fopen(__DIR__ . '/Pagseguro-curl.log', 'a+');
			$return += [CURLOPT_VERBOSE => true, CURLOPT_STDERR => $logHandle];
		}
		return $return;
	}

	public function queryPagseguroTransaction($pTransactionCode, $pCallbackType)
	{
		$pagesuguroAuthenticationParameters = '?email=' . $this->config['ACCOUNTEMAIL'] . '&token=' . $this->config['TOKEN'];
		$pagseguroURLStructure = '';

		if ($pCallbackType == 'automatic')
		{
			$pagseguroURLStructure = $this->config['QUERYSERVER'] . '/' . 'notifications/';
		}
		else
		{
			$pagseguroURLStructure = $this->config['QUERYSERVER'] . '/';
		}

		$serverResponse = $this->curlConnection->connectionSend($pagseguroURLStructure, $pTransactionCode  . $pagesuguroAuthenticationParameters, 'GET', '', 1);

		return $serverResponse;
	}
	
	
    public function confirm($pCallbackType)
    {
		// build required array
		$resultArray = $this->cciEmptyResultArray();
		$resultArray['showerror'] = false;
		$resultArray['resultisarray'] = false;

		try
		{
			if ($pCallbackType == 'manual')
			{
				// on a manual callback we are still yet to query the server and attempt to parse the xml
				$pagseguroTransactionID =  (string) $this->get['transaction_id'];
				$serverResponse = $this->queryPagseguroTransaction($pagseguroTransactionID, $pCallbackType);
				$queryResultXML = new SimpleXMLElement($serverResponse);
			}
			else
			{
				// on an automatic callback we've already queried the server and succesfully parsed the xml so simply retrieve this from the global
				global $callbackQueryResultXML;
				$queryResultXML = $callbackQueryResultXML;
				unset($callbackQueryResultXML);
				// the pagseguro transaction ID is returned differently in an autoamtic callback
				$pagseguroTransactionID = $this->post['notificationCode'];
			}

			if (! isset($queryResultXML['errors']))
			{
				$ref = (string) $queryResultXML->reference;
				$cciEntry = PaymentIntegrationObj::getCciLogEntry($ref);
				$resultArray['ref'] = $ref;

				if ($cciEntry === [])
				{
					// empty first callback
					$resultArray['webbrandcode'] = $this->session['webbrandcode'];
					$resultArray['currencycode'] = $this->session['order']['currencycode'];
					$resultArray['parentlogid'] = -1;
					$resultArray['orderid'] = -1;
					$resultArray['update'] = false;
					$this->updateStatus = false;
				}
				else
				{
					// additional callback
					$resultArray['webbrandcode'] = $cciEntry['webbrandcode'];
					$resultArray['currencycode'] = $cciEntry['currencycode'];
					$resultArray['parentlogid'] = $cciEntry['id'];
					$resultArray['orderid'] = $cciEntry['orderid'];
					$resultArray['update'] = true;
					$this->updateStatus = true;
				}

				switch ($queryResultXML->status)
				{
					case 3:
					case 4:
					{
						$resultArray['authorised'] = 1;
						$resultArray['authorisedstatus'] = true;
						$resultArray['paymentreceived'] = 1;
						$resultArray['transactionid'] = $pagseguroTransactionID;
						$resultArray['formattedtransactionid'] = $pagseguroTransactionID;
						$resultArray['amount'] = (string) $queryResultXML->grossAmount;
						$resultArray['formattedamount'] = $resultArray['amount'];
						$resultArray['responsecode'] = (string) $queryResultXML->status;

						$returnedDate = $queryResultXML->date;
						$timeStampDate = strtotime($returnedDate);

						$resultArray['paymentdate'] = $returnedDate;
						$resultArray['formattedpaymentdate'] = date('Y-m-d H:i:s', $timeStampDate);
						$resultArray['payeremail'] = (isset($queryResultXML->sender->email)) ? (string) $queryResultXML->sender->email : '';
						$resultArray['payerid'] = (isset($queryResultXML->sender->name)) ? (string) $queryResultXML->sender->name : '';

						// we cannot trust that gateway system will exist but the data inside it is of use to us
						if (isset($queryResultXML->gatewaySystem))
						{
							$gatewaySystem = $queryResultXML->gatewaySystem;

							$resultArray['authorisationid'] = (isset($gatewaySystem->authorizationCode)) ? (string) $gatewaySystem->authorizationCode : '';
							$resultArray['formattedauthorisationid'] = $resultArray['authorisationid'];
							$resultArray['bankresponsecode'] = (isset($gatewaySystem->establishmentCode)) ? (string) $gatewaySystem->establishmentCode : '';
							$resultArray['paymentmeans'] = (isset($gatewaySystem->type)) ? (string) $gatewaySystem->type : '';

						}
						break;
					}
					case 1:
					case 2:
					case 5:
					{
						$resultArray['authorised'] = true;
						$resultArray['authorisedstatus'] = 2;
						$resultArray['paymentreceived'] = 0;
						$resultArray['pendingreason'] = (string) $queryResultXML->status;
						$resultArray['responsecode'] = (string) $queryResultXML->status;
						$resultArray['transactionid'] = $pagseguroTransactionID;

						break;
					}
					default:
					{
						$resultArray['authorised'] = false;
						$resultArray['authorisedstatus'] = 0;
						$resultArray['paymentreceived'] = 0;
						$resultArray['pendingreason'] = (string) $queryResultXML->status;
						$resultArray['responsecode'] = (string) $queryResultXML->status;
						$resultArray['transactionid'] = $pagseguroTransactionID;

					}
				}
			}
			else
			{
				$resultArray['showerror'] = true;
				$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_ErrorPaymentFailed1');
				$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_ErrorPaymentFailed2') . ': ' . $queryResultXML['errors']['error'][0]['message'];
				$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . $queryResultXML['errors']['error'][0]['code'];
				$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $ref;
				$resultArray['errorform'] = 'error.tpl';
			}
		}
		catch (Exception $e)
		{
			$resultArray['showerror'] = true;
			$parsedErrorQueryString = [];
			parse_str($queryResultXML, $parsedErrorQueryString);

			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_ErrorPaymentFailed1');
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_ErrorPaymentFailed2') . ': ' . SmartyObj::getParamValue('Order', 'str_ErrorConnectFailure');
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . $parsedErrorQueryString['errornumber'] . ': ' . $parsedErrorQueryString['errorname'];
			$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $ref;
			$resultArray['errorform'] = 'error.tpl';
		}

		$this->curlConnection->connectionClose();

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
		$prefix = $this->config['TRANSACTIONMODE'] !== 'TEST' ? 'LIVE' : 'SANDBOX';
		$urlInfo = parse_url($this->config[$prefix . 'SCRIPTURL']);
		$fallbackUrl = $prefix === 'LIVE' ? $this->config['LIVEFALLBAKCURL'] : $this->config['SANDBOXFALLBACKURL'];

		$fallbackInfo = parse_url($fallbackUrl);

		return [
			'script-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'],
				$fallbackInfo['scheme'] . '://' . $fallbackInfo['host'],
			],
			'frame-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'],
				$fallbackInfo['scheme'] . '://' . $fallbackInfo['host'],
				$urlInfo['host'],
				$fallbackInfo['host'],
			],
			'connect-src' => [
				$urlInfo['scheme'] . '://' . $urlInfo['host'],
				$fallbackInfo['scheme'] . '://' . $fallbackInfo['host'],
				$urlInfo['host'],
				$fallbackInfo['host'],
			],
		];
	}
}

?>