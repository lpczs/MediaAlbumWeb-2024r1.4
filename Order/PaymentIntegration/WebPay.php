<?php

require __DIR__ . '/../../libs/external/vendor/autoload.php';
require_once 'TaopixAbstractGateway.php';

use Transbank\Webpay\Configuration;
use Transbank\Webpay\Webpay as WebPaySDK;

/**
 * WebPay Payment Gateway.
 */
class WebPay extends TaopixAbstractGateway
{
	/**
	 * Gets the WebPay config array.
	 *
	 * @return array The WebPay config array.
	 */
	public function getConfig()
	{
		return $this->config;
	}

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

        AuthenticateObj::clearSessionCCICookie();

		$currencyList = $this->config['CURRENCIES'];
		$currency = $this->session['order']['currencycode'];

        // Test for WebPay supported currencies.
        if (strpos($currencyList, $currency) === false)
        {
			$resultArray['active'] = false;
        }

		return $resultArray;
	}

	/**
	 * WebPay initialize.
	 * 
	 * {@inheritDoc}
	 */
	public function initialize()
	{
        $smarty = SmartyObj::newSmarty('Order', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

		/*
		* First check if we have any ccidata. this is set when the call is made the first time.
		* If the data is set then the user must have hit the back button on their browser.
		*/
        if ($this->session['order']['ccidata'] == '')
        {
			// Initialize WebPay and get payment url.
			$initializeWebPayResult = $this->initializeWebPay();
			
			if ($initializeWebPayResult['error'] == '')
			{
				// Define Smarty variables.
				$smarty->assign('payment_url', $initializeWebPayResult['paymenturl']);
				$smarty->assign('method', 'POST');

				AuthenticateObj::defineSessionCCICookie();
				$smarty->assign('ccicookiename', 'mawebcci' . $this->session['ref']);
				$smarty->assign('ccicookievalue', $this->session['order']['ccicookie']);

				// Set the ccidata to remember we have jumped to WebPay.
				$this->session['order']['ccidata'] = 'start';
				DatabaseObj::updateSession();

				// Allow the page to be cached so that the browser back button works correctly.
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
				$smarty->assign('data1', SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $initializeWebPayResult['errorparam']);

				if ($this->session['ismobile'] == true)
				{
					$smarty->assign('displayInline', true);
					$smarty->assign('homeurl', UtilsObj::correctPath($this->session['webbrandweburl']));
					$smarty->assign('ref', $this->session['ref']);

					$resultArray['template'] = $smarty->fetchLocale('order/PaymentIntegration/error_small.tpl', $this->session['browserlanguagecode']);
					$resultArray['javascript'] = '';
					$resultArray['showerror'] = true;
					return $resultArray;
				}
				else
				{
					$smarty->displayLocale('order/PaymentIntegration/error_large.tpl');
				}
			}
		}
		else
		{
            // The user has clicked the back button.
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

	/**
	 * Initialize a normal transaction object instance. The instances varies depending
	 * if it is live (PRODUCCION) or test (INTEGRACION) enviroments.
	 * 
	 * @param $pEnvironment PRODUCCION for live. INTEGRACION for test. Defaults to test enviroment.
	 * @param $pCommerceCode Merchant Commerce code supplied supplied by WebPay.
	 */
	public function initializeTransaction($pEnvironment, $pCommerceCode)
	{
		if ($pEnvironment == "PRODUCCION")
		{
			$configuration = new Configuration();
			$configuration->setEnvironment("PRODUCCION");
			$configuration->setCommerceCode($pCommerceCode);
			$configuration->setPrivateKey(file_get_contents('../config/PaymentIntegration/WebPay/certificates/certificate.key'));
			$configuration->setPublicCert(file_get_contents('../config/PaymentIntegration/WebPay/certificates/certificate.crt'));
		}
		else
		{
			$configuration = Configuration::forTestingWebpayOneClickNormal();
		}
		
		$webPaySDK = new WebpaySDK($configuration);
		
		$transaction = $webPaySDK->getNormalTransaction();
		
		return $transaction;
	}

	/**
	 * Starts a WebPay instance and returns the paymenturl to redirect to the gateway.
	 *
	 * @return array Array with the URL to redirect to the gateway.
	 */
	private function initializeWebPay()
	{
		$returnResult = array('error' => '', 'errorparam' => '', 'paymenturl' => '');
		
		$transaction = $this->initializeTransaction($this->config['ENVIROMENT'], $this->config['COMMERCECODE']);
		
		$returnPath = UtilsObj::correctPath($this->session['webbrandwebroot']) . 'PaymentIntegration/WebPay/WebPayCallback.php?ref=' . $this->session['ref'];
		$finalPath = UtilsObj::correctPath($this->session['webbrandwebroot']) . 'PaymentIntegration/WebPay/WebPayCallback.php?ref=' . $this->session['ref'];

		$decimal = ($this->session['order']['currencycode'] == 'USD') ? '.' : '';
		$amount = number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], $decimal, '');
		$sessionID = $this->session['ref'];
		$buyOrder = $this->session['ref'] . '_' . time();
		
		$initResult = $transaction->initTransaction($amount, $buyOrder, $sessionID, $returnPath, $finalPath);
		
		if ((is_array($initResult)) && (array_key_exists('error', $initResult)))
		{
			$returnResult['error'] = $initResult['error'];
			$returnResult['errorparam'] = $initResult['detail'];
		}
		else
		{
			$token = $initResult->token;
			$urlRedirect = $initResult->url;

			$this->session['webpayUrl'] = $urlRedirect;
			$this->session['webpayToken'] = $token;
			$this->session['webpayStage'] = 'initialisation';

			$returnResult['paymenturl'] = $urlRedirect . '?token_ws=' . $token;
		}

		return $returnResult;
	}

	 /**
     * WebPay confirm. Manual callbacks only.
	 * 
	 * {@inheritDoc}
     */
	public function confirm($pCallbackType)
	{
		$resultArray = array();
        $update = false;
		$ref = $this->session['ref'];
		$parentID = 0;
		$showError = false;

		$webpayResults = unserialize($this->session['webpayParameters']);

		if ($webpayResults->detailOutput->responseCode == 0)
		{
			$authorised = true;
			$paymentReceived = 1;
		}
		else
		{
			// If responseCode is anything other than 0, then that means there was an error.

			$authorised = false;
			$paymentReceived = 0;

			$showError = true;

			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $webpayResults->detailOutput->responseCode;
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $this->getAuthorisationMessage($webpayResults->detailOutput->responseCode);
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $webpayResults->detailOutput->buyOrder;
			$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelPaymentMethod') . ': ' . 
				$this->getPaymentTypeDescription($webpayResults->detailOutput->paymentTypeCode, $webpayResults->detailOutput->sharesNumber);
			$resultArray['errorform'] = 'error.tpl';
			$resultArray['update'] = true;
		}

		$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime = date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimestamp, '', $webpayResults);

		// Check if there is an existing CCI Log entry for this reference. 
		$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

		// CCI Log exists, so get the parent log id and set update to true.
		if (count($cciLogEntry) > 0)
		{
			if ($cciLogEntry['parentlogid'] != 0)
			{
				$parentID = $cciLogEntry['parentlogid'];
			}
			else
			{
				$parentID = $cciLogEntry['id'];
			}

			$update = true;
		}

		// Set results.
		$resultArray['result'] = '';
		$resultArray['ref'] = $ref;
		$resultArray['amount'] = $webpayResults->detailOutput->amount;
		$resultArray['formattedamount'] = $webpayResults->detailOutput->amount;
		$resultArray['charges'] = $webpayResults->detailOutput->sharesNumber;
		$resultArray['formattedcharges'] = '';
		$resultArray['authorised'] = $authorised;
		$resultArray['authorisedstatus'] = ($authorised  == true) ? 1 : 0;
		$resultArray['transactionid'] = $webpayResults->buyOrder;
		$resultArray['formattedtransactionid'] = $webpayResults->buyOrder;
		$resultArray['responsecode'] = $webpayResults->VCI;
		$resultArray['responsedescription'] = $this->getCardAuthenicationMessage($webpayResults->VCI);
		$resultArray['authorisationid'] = $webpayResults->detailOutput->authorizationCode;
		$resultArray['formattedauthorisationid'] = $webpayResults->detailOutput->authorizationCode;
		$resultArray['bankresponsecode'] = $webpayResults->detailOutput->responseCode;
		$resultArray['cardnumber'] = $webpayResults->cardDetail->cardNumber;
		$resultArray['formattedcardnumber'] = $webpayResults->cardDetail->cardNumber;
		$resultArray['cvvflag'] = '';
		$resultArray['cvvresponsecode'] = '';
		$resultArray['paymentcertificate'] = '';
		$resultArray['paymentdate'] = $serverDate;
		$resultArray['paymentmeans'] = ($webpayResults->detailOutput->paymentTypeCode == 'VD') ? 'Venta Debito' : 'Venta Credito';
		$resultArray['paymenttime'] = $serverTime;
		$resultArray['paymentreceived'] = $paymentReceived;
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
		$resultArray['transactiontype'] = $this->getPaymentTypeDescription($webpayResults->detailOutput->paymentTypeCode, $webpayResults->detailOutput->sharesNumber);
		$resultArray['settleamount'] = '';
		$resultArray['currencycode'] = $this->session['order']['currencycode'];
		$resultArray['webbrandcode'] = $this->session['webbrandcode'];
		$resultArray['script'] = '';
		$resultArray['scripturl'] = '';
		$resultArray['charityflag'] = '';
		$resultArray['threedsecurestatus'] = '';
		$resultArray['cavvresponsecode'] = '';
		$resultArray['update'] = $update;
		$resultArray['orderid'] = 0;
		$resultArray['parentlogid'] = $parentID;
		$resultArray['resultisarray'] = false;
		$resultArray['resultlist'] = array();
		$resultArray['showerror'] = $showError;

        return $resultArray;
	}

	/**
	 * WebPay cancel.
	 * 
	 * {@inheritDoc}
	 */
	public function cancel()
	{
		$ref = UtilsObj::getGETParam('ref');
		$tokenAuth = UtilsObj::getPOSTParam('TBK_TOKEN');

		// Acknowledge the cancel transaction.
		if ($tokenAuth != '')
		{
			// Get the transaction results from WebPay.
			$transaction = $this->initializeTransaction($this->config['ENVIROMENT'], $this->config['COMMERCECODE']);

			// getTransactionResult triggers acknowledgeTransaction.
			$transaction->getTransactionResult($tokenAuth);
		}
		
        $resultArray = array();
        $resultArray['result'] = '';
        $resultArray['ref'] = $ref;
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
	}

	/**
	 * Returns a friendly error message string for the response code.
	 * 
	 * @param $pResonseCode The response code from WebPay.
	 * @return string The message string.
	 */
	private function getAuthorisationMessage($pResonseCode)
	{
		$authorisationMessages = Array
		(
			0 => 'Transacción aprobada', // Transaction approved.
			-1 => 'Rechazo de transacción', // Transaction rejected.
			-2 => 'Transacción debe reintentarse', // Transaction must be retried.
			-3 => 'Error en transacción', // Transaction error.
			-4 => 'Rechazo de transacción', // Rejection of transaction.
			-5 => 'Rechazo por error de tasa', // Rejection due to rate error.
			-6 => 'Excede cupo máximo mensual', // Exceeds maximum monthly quota.
			-7 => 'Excede límite diario por transacción', // Exceeds daily limit per transaction.
			-8 => 'Rubro no autorizado' // Unauthorized item.
		);

		if (isset($authorisationMessages[$pResonseCode]))
		{
			return $authorisationMessages[$pResonseCode];
		}
		else
		{
			return 'Unknown WebPay Error';
		}
	}

	/**
	 * Returns a friendly message string for a VCI code.
	 *
	 * @param int $pVCI The VCI value from WebPay.
	 * @return string The message string.
	 */
	private function getCardAuthenicationMessage($pVCI)
	{
		$vciMessages = Array
		(
			'TSY' => 'Autenticación exitosa', // Authentication Successful.
			'TSN' => 'Autenticación fallida', // Authentication Failed.
			'TO5' => 'Tiempo máximo excedido para autenticación', // Maximum timeout for authentication.
			'ABO' => 'Autenticación abortada por tarjetahabiente', // Authentication aborted by the cardholder.
			'U3' => 'Error interno en la autenticación', // Internal error in authentication.
		);

		if (isset($vciMessages[$pVCI]))
		{
			return $vciMessages[$pVCI];
		}
		else
		{
			return 'Unknown WebPay Error';
		}
	}

	/**
	 * Returns a description for the passed paymenttypecode.
	 *
	 * @param string $paymentTypeCode paymentTypeCode from WebPay.
	 * @param int $sharesNumber sharesNumber from WebPay.
	 */
	private function getPaymentTypeDescription($pPaymentTypeCode, $pSharesNumber)
	{
		$paymentTypes = Array
		(
			'VD' => 'Venta Debito', // Debit.
			'VN' => 'Venta Normal', // Without shares.
			'VC' => 'Venta en cuotas', // With shares.
			'SI' => '3 cuotas sin interés', // 3 shares without interest.
			'S2' => '2 cuotas sin interés', // 2 shares without interest.
			'NC' => $pSharesNumber . ' Cuotas sin interés', // N shares without interest.
		);

		if (isset($paymentTypes[$pPaymentTypeCode]))
		{
			return $paymentTypes[$pPaymentTypeCode];
		}
		else
		{
			return 'Unknown WebPay Payment Type';
		}
	}

	/**
	 * Redirects to a supplied URL via the PaymentReurn template.
	 *
	 * @param string $pRedirectUrl The URL to redirect to.
	 * @param array $pParameters Extra data to be sent via POST.
	 */
	public function redirect($pRedirectUrl, $pParameters)
	{
     	global $gSession;

		$smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		$smarty->assign('server', $pRedirectUrl);

		if (is_array($pParameters))
		{
			$smarty->assign('parameter', $pParameters);
		}
		else
		{
			$smarty->assign('parameter', '');
		}

		if ($gSession['ismobile'] == true)
		{
			$smarty->displayLocale('order/PaymentIntegration/PaymentReturn_small.tpl');
		}
		else
		{
			$smarty->displayLocale('order/PaymentIntegration/PaymentReturn_large.tpl');
		}
	}

	/**
	 * Not used by WebPay.
	 *
	 * {@inheritDoc}
	 */
	public function generateHash($pString){}

	/**
	 * Not used by WebPay.
	 *
	 * {@inheritDoc}
	 */
	public function hashString($pParams, $pType){}

	/**
	 * Not used by WebPay.
	 *
	 * {@inheritDoc}
	 */
	public function verifyHash($pSuppliedHash, $pParams, $pType){}
}
?>