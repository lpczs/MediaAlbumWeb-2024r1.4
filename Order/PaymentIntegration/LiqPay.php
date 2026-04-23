<?php

require_once 'TaopixAbstractGateway.php';

/**
 * LiqPay Payment
 */
class LiqPay extends TaopixAbstractGateway
{
    /**
     * Configure the liqPay gateway
     *
     * {@inheritDoc}
     */
    public function configure()
    {
        $resultArray = [
            'active' => false,
            'form' => '',
            'scripturl' => '',
            'script' => '',
            'action' => ''
        ];

        // Supported currencies: USD, EUR, RUB, UAH, GEL
        if (in_array(strtoupper($this->session['order']['currencycode']), explode(',', $this->config['CURRENCY'])))
        {
            $resultArray['active'] = true;
        }

        AuthenticateObj::clearSessionCCICookie();

        return $resultArray;
    }

    public function initialize()
    {
        global $gSession;

        $parameters = Array();
		$resultArray = Array();
		$resultArray['script'] = "";
		$resultArray['scripturl'] = "";

        $smarty = SmartyObj::newSmarty('Order', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

        $cancelReturnPath = UtilsObj::correctPath($this->session['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];
        $returnPath = UtilsObj::correctPath($this->session['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'];
        $notificationPath = UtilsObj::correctPath($this->session['webbrandweburl']) . '?fsaction=Order.ccAutomaticCallback&ref=' . $this->session['ref'];
        
        if ($gSession['order']['ccidata'] == '')
        {
            // Read from config file
            $server_url = $this->config['SERVER'];
			$browserLanguage = $this->session['browserlanguagecode'];
			$languageList = $this->config['LANGUAGES'];
			$defaultLanguage = $this->config['DEFAULTLANGUAGE'];
			$productName = LocalizationObj::getLocaleString($this->session['items'][0]['itemproductname'], $this->session['browserlanguagecode'], true);
			$productNameEncoded = htmlentities($productName, ENT_QUOTES);

			// Check language is supported and fall back to default if not.
			if (!in_array($browserLanguage, explode(',', $languageList)))
			{
				$browserLanguage = $defaultLanguage;
            }
            
            $parameters = array(
				'version' => 3,
				'public_key' => $this->config['PUBLICKEY'],
				'action' => 'pay',
				'amount' => number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '.', ''),
				'currency' => $this->session['order']['currencycode'],
				'description' => $productName,
				'order_id' => $this->session['ref'] . '_' . time(),
                'result_url' => $returnPath,
                'server_url' => $notificationPath,
                'language' => $browserLanguage
            );
            
            $formParams = array(
                'data' => $this->encode_params($parameters),
                'signature' => $this->cnb_signature($parameters)
            );

            // Define Smarty parameters
            $smarty->assign('method', 'POST');
            $smarty->assign('parameter', $formParams);
            $smarty->assign('cancel_url', $cancelReturnPath);
            $smarty->assign('payment_url', $server_url);

            AuthenticateObj::defineSessionCCICookie();
            $smarty->assign('ccicookiename', 'mawebcci' . $this->session['ref']);
            $smarty->assign('ccicookievalue', $this->session['order']['ccicookie']);

            // Set the ccidata to remember we have jumped to LiqPay
            $this->session['order']['ccidata'] = 'start';
            DatabaseObj::updateSession();

            $smarty->cachePage = true; // Allow the page to be cached so that the browser back button works correctly
        }
		else
        {
            // the user has clicked the back button
            AuthenticateObj::clearSessionCCICookie();

            $cancelReturnPath = UtilsObj::correctPath($this->session['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];
            $smarty->assign('payment_url', $cancelReturnPath);
            $smarty->assign('cancel_url', $cancelReturnPath);
        }

        // Display template
        if ($this->session['ismobile'] == true)
        {
            $resultArray['template'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest_small.tpl');
            $resultArray['javascript'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest.tpl');
        }
		else
        {
            $smarty->displayLocale('order/PaymentIntegration/PaymentRequest_large.tpl');
        }
        
        return $resultArray;
    }


    /**
     * Valitor confirm
     *
     * {@inheritDoc}
     */
    public function confirm($pCallbackType)
    {
        $validatedData = null;

        //Build a default array to return
        $resultArray = $this->cciEmptyResultArray();
        $resultArray['showerror'] = false;

        $signature = $this->str_to_sign($this->config['PRIVATEKEY'] . $this->post['data'] . $this->config['PRIVATEKEY']);
        $liqPaySignature = $this->post['signature'];

        $liqPayReturnParams = $this->decode_params($this->post['data']);

        // get the session ref from the orderid that was orignally sent up
        $sessionRefPos = strpos($liqPayReturnParams['order_id'], '_');
        $ref = substr($liqPayReturnParams['order_id'], 0, $sessionRefPos);

        if ($signature == $liqPaySignature)
        {   
            $this->logCCIEntryForSameTransactionID = true;

            // Check for a cciEntry for this transactionid.
            $cciEntry = PaymentIntegrationObj::getCciLogEntry($ref);
            
            if ($cciEntry === [])
            {
                // empty first callback from automatic callback
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
                $resultArray['update'] = ($cciEntry['orderid'] !== -1);
                $this->updateStatus = false;
                $this->loadSession = true;
            }

            switch ($liqPayReturnParams['status'])
            {
                case 'failure':
                case 'error':
                case 'reversed':
                case 'try_again':
                    $resultArray['authorised'] = false;
                    $resultArray['authorisedstatus'] = 0;
                    $resultArray['paymentreceived'] = 0;
                break;
                case 'success':
                case 'wait_secure':
                    $validatedData['transactionid'] = $liqPayReturnParams['transaction_id'];
                    $validatedData['paymentid'] = $liqPayReturnParams['payment_id'];
                    $validatedData['sendercardmask2'] = $liqPayReturnParams['sender_card_mask2'];
                    $validatedData['paytype'] = $liqPayReturnParams['paytype'];

                    $resultArray['authorised'] = true;
                    $resultArray['authorisedstatus'] = 1;
                    $resultArray['paymentreceived'] = 1;
                break;
                default:
                $resultArray['authorised'] = true;
                $resultArray['authorisedstatus'] = 2;
                $resultArray['paymentreceived'] = 0;
            }
        }
        
        $serverTimeStamp = DatabaseObj::getServerTime();
        $serverDate = date('Y-m-d');
        $serverTime = date('H:i:s');

        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $liqPayReturnParams['amount'];
		$resultArray['formattedamount'] = $liqPayReturnParams['amount'];
        $resultArray['transactionid'] = (($validatedData !== null) ? $validatedData['transactionid'] : '');
        $resultArray['authorisationid'] = (($validatedData !== null) ? $validatedData['paymentid'] : '');
		$resultArray['formattedtransactionid'] = $resultArray['transactionid'];
		$resultArray['responsecode'] = $liqPayReturnParams['status'];
		$resultArray['authorisationid'] = $resultArray['transactionid'];
		$resultArray['formattedauthorisationid'] = $resultArray['transactionid'];
		$resultArray['bankresponsecode'] = $resultArray['transactionid'];
		$resultArray['cardnumber'] = (($validatedData !== null) ? $validatedData['sendercardmask2'] : '');
		$resultArray['paymentmeans'] = (($validatedData !== null) ? $validatedData['paytype'] : '');
		$resultArray['formattedcardnumber'] = $resultArray['cardnumber'];
		$resultArray['paymentdate'] = $serverDate;
		$resultArray['paymenttime'] = $serverTime;
		$resultArray['formattedpaymentdate'] = $serverTimeStamp;
		$resultArray['resultisarray'] = false;
		$resultArray['resultlist'] = [];

        // Get the server timestamp.
		$serverTimestamp = DatabaseObj::getServerTime();

		// Log the confirm result.
		PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimestamp, '', $resultArray);

        return $resultArray;
    }

    /**
     * cnb_signature
     *
     * @param array $params
     *
     * @return string
     */
    private function cnb_signature($params)
    {
        $private_key = $this->config['PRIVATEKEY'];

        $json = $this->encode_params($params);
        $signature = $this->str_to_sign($private_key . $json . $private_key);

        return $signature;
    }

    /**
     * encode_params
     *
     * @param array $params
     * @return string
     */
    private function encode_params($params)
    {
        return base64_encode(json_encode($params));
    }

    /**
     * decode_params
     *
     * @param string $params
     * @return array
     */
    private function decode_params($params)
    {
        return json_decode(base64_decode($params), true);
    }

    /**
     * str_to_sign
     *
     * @param string $str
     *
     * @return string
     */
    private function str_to_sign($str)
    {
        $signature = base64_encode(sha1($str, 1));

        return $signature;
    }

    /**
	 * UNUSED
	 */
	public function generateHash($pString)
	{
		return null;
    }
    
    /**
	 * UNUSED
	 */
	public function hashString($pParams, $pType)
	{
		return null;
    }
    
    /**
	 * UNUSED
	 */
	public function verifyHash($pSuppliedHash, $pParams, $pType)
	{
		return null;
	}
}
?>