<?php

require_once 'TaopixAbstractGateway.php';

/**
 * Valitor Payment
 */
class Valitor extends TaopixAbstractGateway
{
    /**
     * Configure the Valitor gateway
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

        //Currency is set to what is enabled in the merchant account
        if (strpos($this->config['CURRENCY'], $this->session['order']['currencycode']) === false)
        {
            $resultArray['active'] = false;
        }

        AuthenticateObj::clearSessionCCICookie();

        return $resultArray;
    }

    public function initialize()
    {
        $resultArray = [];

        $smarty = SmartyObj::newSmarty('Order', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

        $fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl']);
        $cancelURL = $fixedUrlPath . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];

        //Check if we have any ccidata if so the user has hit the back button
        if ($this->session['order']['ccidata'] == '')
        {
            $automaticCallBackUrl = $fixedUrlPath . '?fsaction=Order.ccAutomaticCallback&ref=' . $this->session['ref'];
            $manualCallBackUrl = $fixedUrlPath . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'];

            //Setup the data to send to valitor
            $paymentParameters = [
                'MerchantID' => $this->config['MERCHANTID'],
                'Currency' => $this->session['order']['currencycode'],
                'ReferenceNumber' => $this->session['ref'] . '_'. time(),
                'AuthorisationOnly' => 0,
                //This is only required for the digital signature to be created on their end
                'PaymentSuccessfulURL' => $manualCallBackUrl,
                'PaymentSuccessfulURLText' => 'Payment Sucessful',
                'PaymentSuccessfulAutomaticRedirect' => 1,
                'PaymentSuccessfulServerSideURL' => $automaticCallBackUrl,
                'PaymentCancelledURL' => $cancelURL,
                'Language' => $this->config['LANGUAGE']
            ];

            //Add the product parameters
            $paymentParameters = array_merge($paymentParameters, $this->createProductsArray());

            //Create and add the digital signature to the array
            $paymentParameters['DigitalSignature'] = $this->hashString($paymentParameters, '');

            $smarty->assign('payment_url', $this->config['PAYMENTURL']);
            $smarty->assign('method', 'POST');
            $smarty->assign('parameter', $paymentParameters);
            $smarty->assign('cancel_url', $cancelURL);

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

    /**
     *  Functions to add more than one item to the cart
     */
    public function createProductsArray()
    {
       //If the ORDERDESCRIPTION config field is not set revert to the first product name
        if (!$this->config['ORDERDESCRIPTION'])
       {
            $paymentArray = [
                'Product_1_Quantity' => '1',
                'Product_1_Price'=> number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '.', ''),
                'Product_1_Discount' => 0,
                'Product_1_Description' => LocalizationObj::getLocaleString($this->session['items'][0]['itemproductname'], $this->session['browserlanguagecode'], true)
            ];
       }
       else
       {
            $paymentArray = [
                'Product_1_Quantity' => '1',
                'Product_1_Price'=> number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '.', ''),
                'Product_1_Discount' => 0,
                'Product_1_Description' => $this->config['ORDERDESCRIPTION']
            ];
       }

        return $paymentArray;
    }

    /**
     * Valitor confirm
     *
     * {@inheritDoc}
     */
    public function confirm($pCallbackType)
    {
        //Build a default array to return
        $resultArray = $this->cciEmptyResultArray();
        $resultArray['showerror'] = false;

        $checkVariables = $this->get;

        $cciRef = array_key_exists('ReferenceNumber', $checkVariables) ? $checkVariables['ReferenceNumber'] : $this->session['ref'];

        $cciEntry = PaymentIntegrationObj::getCciLogEntry($cciRef);
        if ($cciEntry === [])
        {
            $resultArray['webbrandcode'] = $this->session['webbrandcode'];
            $resultArray['currencycode'] = $this->session['order']['currencycode'];
            $resultArray['amount'] = $this->session['order']['ordertotaltopay'];
            $resultArray['parentlogid'] = -1;
			$resultArray['orderid'] = -1;
			$resultArray['update'] = false;
			$this->updateStatus = false;
        }
        else
        {
            $resultArray['webbrandcode'] = $cciEntry['webbrandcode'];
			$resultArray['currencycode'] = $cciEntry['currencycode'];
			$resultArray['amount'] = $cciEntry['formattedamount'];
			$resultArray['parentlogid'] = $cciEntry['id'];
			$resultArray['orderid'] = $cciEntry['orderid'];
			$resultArray['update'] = true;
			$this->updateStatus = true;
        }

        if ($checkVariables['DigitalSignatureResponse'] !== null && ($this->verifyHash($checkVariables['DigitalSignatureResponse'], $checkVariables, '')))
        {
            $resultArray['authorised'] = true;
            $resultArray['authroisedstatus'] = 1;
        }
        else
        {
            //The hash verification has failed

            $resultArray['showerror'] = true;
			$resultArray['authorised'] = false;
			$resultArray['authorisedstatus'] = 0;
			// error messages for hash fail
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': Payment Error';
			// the language string str_OrderAdyenSignatureFailed gives the correct error message of signature check failed
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . SmartyObj::getParamValue('CreditCardPayment', 'str_OrderAdyenSignatureFailed');
			$resultArray['errorform'] = 'error.tpl';

        }

        $serverTimeStamp = DatabaseObj::getServerTime();
        $serverDate = date('Y-m-d');
        $serverTime = date('H:i:s');

        //Assign the rest of the details sent back
        $resultArray['ref'] = $checkVariables['ReferenceNumber'];
        $resultArray['formattedamount'] = $resultArray['amount'];
        $resultArray['transactionid'] = $checkVariables['TransactionNumber'];
        $resultArray['formattedtransactionid'] = $resultArray['transactionid'];
        $resultArray['formattedauthorisationid'] = $resultArray['transactionid'];
        $resultArray['cardnumber'] = $checkVariables['CardNumberMasked'];
        $resultArray['formattedcardnumber'] = $resultArray['cardnumber'];
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentime'] = $serverTime;
        $resultArray['paymentreceived'] = 1;
        $resultArray['formattedpaymentdate'] = $serverTimeStamp;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = [];

        return $resultArray;
    }

    /**
     * Generate an md5 hash requried for valitor digital signautre
     *
     *  @param string $pString is a concatenated string used to create the digital sinature
     */
    public function generateHash($pString)
    {
        return hash('md5', $pString);
    }

    /**
     * Generate the digital signature required for valitor
     *
     * The digital signature is a string which is md5 hashed which contains the following values in this order:
     * VerificationCode
     * AuthorizationOnly - always set to 0
     * Product_X_Quantity
     * Product_X_Price
     * Product_X_Discount
     * MerchantID
     * ReferenceNumber
     * PaymentSuccessfulURL
     * PaymentSuccessfulServerSideURL
     * Currency
     *
     * @param array $pParameterArray is an existing array consisting of the product options
     */
    public function hashString($pParams, $pType)
    {
        $hashString = $this->config['VERIFICATIONCODE'];
        //Authorisation is not used and it set to 0
        $hashString .= '0';
        //Quantitiy
        $hashString .= '1';
        //Price
        $hashString .= number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '.', '');
        //Discount
        $hashString .= '0';
        $hashString .= $pParams['MerchantID'];
        $hashString .= $this->session['ref'] . '_'. time();
        $hashString .= $pParams['PaymentSuccessfulURL'];
        $hashString .= $pParams['PaymentSuccessfulServerSideURL'];
        $hashString .= $pParams['Currency'];

        //Hash the string
        $hashString = $this->generateHash($hashString);

        //Return the hash string so it can be added to the array

        return $hashString;
    }

    /**
     *
     * @param string $pSuppliedHash md5 hash sent from Valitor
     * @param array $pParams an array containing what we are sent back
     * @param string $pType not used for valitor
     */
    public function verifyHash($pSuppliedHash, $pParams, $pType)
    {
        //Valitor sends back the Verification and RefernceNumber md5 hashed

        $string = $this->config['VERIFICATIONCODE'] . $pParams['ReferenceNumber'];
        return $this->generateHash($string) === $pSuppliedHash ? true : false;
    }
}
?>