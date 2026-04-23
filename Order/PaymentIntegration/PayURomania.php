<?php

require_once 'TaopixAbstractGateway.php';

/**
 * Pay U Romania Payment Gateway
 */

class PayURomania extends TaopixAbstractGateway
{
    /**
     * Configure the PayURo Gateway
     * 
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
     
        //Check if we have any ccidata already
        if($this->session['order']['ccidata'] == '')
        {
            $automaticCallBackUrl = $fixedUrlPath . '?fsaction=Order.ccAutomaticCallback&ref=' . $this->session['ref'];
			$confirmPath = UtilsObj::correctPath($this->session['webbrandwebroot']) . 'PaymentIntegration/PayURomania/PayURomaniaCallback.php?ref=' . $this->session['ref'] . '&callback=manual';
            
            if($this->config['ORDERDESCRIPTION'])
            {
                $productName = $this->config['ORDERDESCRIPTION'];
            }
            else
            {
                $productName = LocalizationObj::getLocaleString($this->session['items'][0]['itemproductcollectionname'], $this->session['browserlanguagecode'], true);
            }

            $paymentParameters = [
                'MERCHANT' => $this->config['MERCHANTCODE'],
                'ORDER_REF' => $this->session['ref'],
                'ORDER_DATE' => date('Y-m-d H:i:s'),
                'ORDER_PNAME[]' => $productName,
                'ORDER_PCODE[]' => LocalizationObj::getLocaleString($this->session['items'][0]['itemproductcollectioncode'], $this->session['browserlanguagecode'], true),
                'ORDER_PINFO[]' => $productName,   
                'ORDER_PRICE[]' => number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '.', ''),
                'ORDER_QTY[]' =>  1,
                'ORDER_VAT[]' =>  0,
                'ORDER_PRICE_TYPE[]' => 'GROSS',
                'PRICES_CURRENCY' => $this->session['order']['currencycode'],
                'ORDER_SHIPPING' => '',
                'DISCOUNT' => '',
                'LANGUAGE' => $this->session['browserlanguagecode'],
                'PAY_METHOD' => 'CCVISAMC',
                'TESTORDER' => $this->config['TESTMODE'],
                'BACK_REF' => $confirmPath,
                'BILL_FNAME' => $this->session['order']['billingcontactfirstname'],
                'BILL_LNAME' => $this->session['order']['billingcontactlastname'],
                'BILL_EMAIL' => $this->session['order']['billingcustomeremailaddress'],
                'BILL_PHONE' => $this->session['order']['billingcustomertelephonenumber'],
                'BILL_COUNTRYCODE' => $this->session['order']['billingcustomercountrycode'],

            ];
			
            $paymentParameters['ORDER_HASH'] = $this->hashString($paymentParameters, '');

            $smarty->assign('payment_url', $this->config['SERVER']);
            $smarty->assign('method', 'POST');
            $smarty->assign('parameter', $paymentParameters);
    
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
     * The format for the hash string needs to be the length of the parameter followed by the parameter
     * e.g. 5TAOPIX
     */

    public function formatStringForHash($string)
    {
        return strlen($string) . $string;
    }

    public function confirm($pCallbackType)
    {
	   $resultArray = $this->cciEmptyResultArray();
	   $serverTimeStamp = DatabaseObj::getServerTime();
       $serverDate = date('Y-m-d');
       $serverTime = date('H:i:s');
	   $this->loadSession = true;
	   
		if($pCallbackType === 'automatic')
		{
		
			$orderDetails = $this->post;
			$this->updateStatus = false;
		
			$orderCurrency = $orderDetails['CURRENCY'];
            $orderTotal = $orderDetails['IPN_TOTALGENERAL'];
            $orderId = $orderDetails['REFNO'];
            $ref = $orderDetails['REFNOEXT'];
            $formattedAmount = $orderDetails['IPN_TOTALGENERAL'];
            $transactionId = $orderDetails['REFNO'];
            $formattedtransactionid = $orderDetails['REFNO'];
            $formattedauthorisationid = $orderDetails['REFNO'];
            $paymentDate = $orderDetails['SALEDATE'];
            $update = false;
			$responseCode = 1;
	
            if($orderDetails['ORDERSTATUS'] === 'TEST' || 'PAYMENT_AUTHORIZED')
			{
                $authorised = 1;
                $paymentReceived = 1;
			}
			else
			{
				$authorised = 0;
                $paymentReceived = 0;
			}
		}
		else if($pCallbackType === 'manual')
		{
			//We need to get the details from the ccilog table
			$cciRef = $this->session['ref'];
			$cciEntry = PaymentIntegrationObj::getCciLogEntry($cciRef);
						
			$orderCurrency = $cciEntry['currencycode'];
            $orderTotal = $cciEntry['formattedamount'];
            $orderId = $cciEntry['orderid'];
            $ref = $cciEntry['sessionid'];
            $formattedAmount = $cciEntry['formattedamount'];
            $transactionId = $cciEntry['formattedtransactionid'];
            $formattedtransactionid = $cciEntry['formattedtransactionid'];
            $formattedauthorisationid = $cciEntry['formattedtransactionid'];
            $paymentDate = $cciEntry['paymentdate'];
			$authorised = $cciEntry['authorised'];
			$responseCode = $cciEntry['responsecode'];
            $update = true;
		}
		
			$resultArray = [];
			
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
			$resultArray['transactionid'] = $formattedtransactionid;
			$resultArray['formattedtransactionid'] = $formattedtransactionid;
			$resultArray['responsedescription'] = '';
			$resultArray['authorisationid'] = $formattedtransactionid;
			$resultArray['formattedauthorisationid'] = $formattedtransactionid;
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
			$resultArray['paymentdate'] = $paymentDate;
			$resultArray['paymentmeans'] = '';
			$resultArray['paymenttime'] = $paymentDate;
			$resultArray['paymentreceived'] = 1;
			$resultArray['formattedpaymentdate'] = $paymentDate;
			$resultArray['settleamount'] = '';
			$resultArray['currencycode'] = $orderCurrency;
			$resultArray['webbrandcode'] = '';
			$resultArray['update'] = $update;
			$resultArray['orderid'] = $orderId;
			$resultArray['parentlogid'] = 0;
			$resultArray['resultisarray'] = false;
			$resultArray['resultlist'] = array();
			$resultArray['showerror'] = false;
			$resultArray['responsecode'] = $responseCode;
		
		    return $resultArray;
    }

    public function generateHash($pString)
    {
        $key = $this->config['SECRETKEY'];
        
        $b = 64;

        if (strlen($key) > $b) {
            $key = pack('H*', md5($key));
        }

        $key = str_pad($key, $b, chr(0x00));
        $ipad = str_pad('', $b, chr(0x36));
        $opad = str_pad('', $b, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack('H*', md5($k_ipad . $pString)));
    
    }

	
	
    public function hashString($pParams, $pType)
    {
        //Merchant Code
        $hashString = $this->formatStringForHash($this->config['MERCHANTCODE']);
        //Order Ref
        $hashString .= $this->formatStringForHash($pParams['ORDER_REF']);
        //Order Date
        $hashString .= $this->formatStringForHash($pParams['ORDER_DATE']);
        //Product Name
        $hashString .= $this->formatStringForHash($pParams['ORDER_PNAME[]']);
        //Product Code
        $hashString .= $this->formatStringForHash($pParams['ORDER_PCODE[]']);
        //Product Info
        $hashString .= $this->formatStringForHash($pParams['ORDER_PINFO[]']);
        //Product Price 
        $hashString .= $this->formatStringForHash($pParams['ORDER_PRICE[]']);
        //Order Qty
        $hashString .= $this->formatStringForHash($pParams['ORDER_QTY[]']);
        // Order Vat Rate
        $hashString .= $this->formatStringForHash($pParams['ORDER_VAT[]']);
        //We do not send the shipping but hash needs it
        $hashString .= $this->formatStringForHash('');
        //Currency code
        $hashString .= $this->formatStringForHash($pParams['PRICES_CURRENCY']);
        //We do not send the discount but hash needs it
        $hashString .= $this->formatStringForHash('');
        //Payment Method
        $hashString .= $this->formatStringForHash($pParams['PAY_METHOD']);
        //Price Type
        $hashString .= $this->formatStringForHash($pParams['ORDER_PRICE_TYPE[]']);
        //Test mode
        $hashString .= $this->formatStringForHash($pParams['TESTORDER']);

        //Hash the string
        $hashString = $this->generateHash($hashString);

        return $hashString;

    }

    public function verifyHash($pSuppliedHash, $pParams, $pType)
    {
        return null;
    }
}

?>