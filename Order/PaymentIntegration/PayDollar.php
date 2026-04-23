<?php

class PayDollarObj
{
    static function configure()
    {
        global $gSession;

		$resultArray = Array();
		$active = true;

        AuthenticateObj::clearSessionCCICookie();

		// test for PayDollar supported currencies
        $currencyList = '344,840,702,156,036,124,446,901,826,360,554,704,392,978,608,764,410,784,356,458,682,096';
        if (strpos($currencyList, $gSession['order']['currencyisonumber']) === false)
        {
            $active = false;
        }

        $resultArray['active'] = $active;
        $resultArray['form'] = '';
        $resultArray['scripturl'] = '';
        $resultArray['script'] = '';
        $resultArray['action'] = '';

        return $resultArray;
    }

    static function initialize()
    {
        global $gConstants;
        global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$payDollarConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayDollar.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

			$successReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&sc=0&ref=' . $gSession['ref'];
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$failReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&sc=1&ref=' . $gSession['ref'];
			
			$server = $payDollarConfig['PDSERVER'];
			$vendorName = $payDollarConfig['PDVENDORNAME'];
			$merchantId = $payDollarConfig['PDMERCHANTID'];
			$secureSecret = $payDollarConfig['SECURESECRET'];
            $paymethod= $payDollarConfig['PDGATEWAY'];

			// amount in smallest unit, e.g. pence or cents
			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');
			$gateway = $payDollarConfig['PDGATEWAY'];

			// build transaction id
			$orderRef = $gSession['ref'] . '_' . time();

			$currency = $gSession['order']['currencyisonumber'];

			// get language code and see if it is supported by PayDollar
			$locale = strtolower($gSession['browserlanguagecode']);
			$locale = substr($locale, 0, 2);
			switch ($locale)
			{
			  case 'en':
					$lang = 'E'; // english
				break;
			  case 'ko':
					$lang = 'K'; // korean
				break;
			  case 'ja':
					$lang = 'J'; // japanese
				break;
			  case 'th':
					$lang = 'T'; // thai
				break;
			  case 'zh':
					if (strtolower($gSession['browserlanguagecode']) == 'zh_cn')
					{
						$lang = 'X'; // simplified Chinese
					}
					else
					{
						$lang = 'C'; // traditional Chinese
					}
				break;
			  default:
					$lang = 'E'; // default to english
			}

			// define Smarty variables
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$smarty->assign('payment_url', $server);
			$smarty->assign('cancel_url', $cancelReturnPath);
			
			$paymentType = 'N';
            
            $dataString = $merchantId ."|". $orderRef ."|". $currency ."|". $amount ."|". $paymentType ."|". $secureSecret ;
            $myHash = sha1($dataString);

			$parameters = array(
                            'orderRef' => $orderRef,
                            'amount' => $amount,
                            'currCode' => $currency,
                            'lang' => $lang,
                            'merchantId' => $merchantId,
                            'remark' => $gSession['ref'],
                            'cancelUrl' => $cancelReturnPath,
                            'failUrl' => $failReturnPath,
                            'successUrl' => $successReturnPath,
                            'payType' => $paymentType,
                            'payMethod' => $paymethod,
							'secureHash' => $myHash
                        );

            $smarty->assign('parameter', $parameters);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to PayDollar
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
            // the user has clicked the back button
            AuthenticateObj::clearSessionCCICookie();

            $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
            $smarty->assign('payment_url', $cancelReturnPath);
            $smarty->assign('cancel_url', $cancelReturnPath);

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
        $result = '';

        $resultArray['result'] = '';
        $resultArray['ref'] = $_GET['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function confirm($pCallBack)
    {
		global $gSession;

		$payDollarConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayDollar.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);		
		$secureSecret = $payDollarConfig['SECURESECRET'];
		
		$resultArray = Array();
		$result = '';
		$authorised = false;

		$formatted_payment_date = DatabaseObj::getServerTime();
		
		//Check if we come from automatic or manual callback 
		if($pCallBack == 'manual')
		{
			//Check CCILOG to see if this is an update
			$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($gSession['ref']);
			$authorisedStatus = $cciLogEntry['authorised'];
			
			if ($authorisedStatus == 1)
			{
				$authorised = true;
				$paymentReceived = 1;
				
				$amount = $cciLogEntry['authorised'];
				$formatted_payment_date = $cciLogEntry['paymentdate'];
				$ApprovalCode = $cciLogEntry['authorisationid'];
				$payDollarReferenceNumber = $cciLogEntry['transactionid'];
				$sourceIP = $cciLogEntry['payerid'];
				$server = $cciLogEntry['payerstatus'];
				$accountName = $cciLogEntry['business'];
				$merchantOrderRefenceNumber = $cciLogEntry['transactiontype'];
				$currency = $cciLogEntry['currencycode'];
				$formatted_payment_date = $cciLogEntry['formattedpaymentdate'];
				$payDollarReferenceNumber = $cciLogEntry['formattedtransactionid'];
				$ApprovalCode = $cciLogEntry['formattedauthorisationid'];
				$statusCode = $cciLogEntry['responsecode'];
				$bankResponse = $cciLogEntry['bankresponsecode'];
				$bankReference = $cciLogEntry['paymentcertificate'];
				$eci = $cciLogEntry['threedsecurestatus'];
				$payerAuthStatus = $cciLogEntry['cavvresponsecode'];
			}
			else
			{
				$authorised = false;
				$paymentReceived = 0;
			}	
		}
		else
		{
			$secondarybankHostStatusCode 	= UtilsObj::getPOSTParam('src', '');
			$primarybankHostStatusCode 		= UtilsObj::getPOSTParam('prc', '');
			$bankReference 					= UtilsObj::getPOSTParam('Ord', '');
			$accountName 					= UtilsObj::getPOSTParam('Holder', '');
			$statusCode 					= UtilsObj::getPOSTParam('successcode', '');
			$merchantOrderRefenceNumber 	= UtilsObj::getPOSTParam('Ref', '');
			$payDollarReferenceNumber 		= UtilsObj::getPOSTParam('PayRef', '');
			$amount 						= UtilsObj::getPOSTParam('Amt', '0.00');
			$currency 						= UtilsObj::getPOSTParam('Cur', '');
			$ApprovalCode 					= UtilsObj::getPOSTParam('AuthId', '');
			$eci 							= UtilsObj::getPOSTParam('eci', '');
			$payerAuthStatus 				= UtilsObj::getPOSTParam('payerAuth', '');
			$sourceIP 						= UtilsObj::getPOSTParam('sourceIp', '');
			$ipCountry 						= UtilsObj::getPOSTParam('ipCountry', '');
			$remark 						= UtilsObj::getPOSTParam('remark', '');
			$hashBack 						= UtilsObj::getPOSTParam('secureHash', '');

			$dataString = $secondarybankHostStatusCode ."|". $primarybankHostStatusCode ."|". 
                    $statusCode ."|". $merchantOrderRefenceNumber ."|". 
					$payDollarReferenceNumber ."|". $currency ."|".$amount ."|".
                    $payerAuthStatus ."|".$secureSecret;
							
			$myHashBack = sha1($dataString);
			
			$hashMatch = 0;
			if($myHashBack == $hashBack)
			{
				$hashMatch = 1;
			}

			$payDollarConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayDollar.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
			$server = $payDollarConfig['PDSERVER'];

			$bankResponse = $primarybankHostStatusCode . ' ' . $secondarybankHostStatusCode;
			
			// write to log file.
			$serverTimestamp = DatabaseObj::getServerTime();
			PaymentIntegrationObj::logPaymentGatewayData($payDollarConfig, $serverTimestamp);

			if ($statusCode == 0 && $hashMatch == 1)
			{
				$authorised = true;
				$authorisedStatus = 1;
				$paymentReceived = 1;
			}
			else
			{
				$authorised = false;
				$authorisedStatus = 0;
				$paymentReceived = 0;
			}	
		}
		
		$resultArray['authorised'] = $authorised;
		$resultArray['authorisedstatus'] = $authorisedStatus;
		$resultArray['result'] = $result;
		$resultArray['ref'] = $_GET['ref'];
		$resultArray['amount'] = $amount;
		$resultArray['formattedamount'] = $amount;
		$resultArray['charges'] = '';
		$resultArray['formattedcharges'] = 0.00;
		$resultArray['paymentdate'] = $formatted_payment_date;
		$resultArray['paymenttime'] = '';
		$resultArray['authorisationid'] = $ApprovalCode;
		$resultArray['transactionid'] = $payDollarReferenceNumber;
		$resultArray['paymentmeans'] = '';
		$resultArray['addressstatus'] = $ipCountry;
		$resultArray['payerid'] = $sourceIP;
		$resultArray['payerstatus'] = $server;
		$resultArray['payeremail'] = '';
		$resultArray['business'] = $accountName;
		$resultArray['receiveremail'] = '';
		$resultArray['receiverid'] = '';
		$resultArray['pendingreason'] = '';
		$resultArray['transactiontype'] = $merchantOrderRefenceNumber;
		$resultArray['currencycode'] = $currency;
		$resultArray['webbrandcode'] = $gSession['webbrandcode'];
		$resultArray['settleamount'] = '';
		$resultArray['paymentreceived'] = $paymentReceived;
		$resultArray['formattedpaymentdate'] = $formatted_payment_date;
		$resultArray['formattedtransactionid'] = $payDollarReferenceNumber;
		$resultArray['formattedauthorisationid'] = $ApprovalCode;
		$resultArray['cardnumber'] = '';
		$resultArray['formattedcardnumber'] = '';
		$resultArray['cvvflag'] = '';
		$resultArray['cvvresponsecode'] = '';
		$resultArray['responsecode'] = $statusCode;
		$resultArray['bankresponsecode'] = $bankResponse;
		$resultArray['paymentcertificate'] = $bankReference;
		$resultArray['update'] = false;
		$resultArray['orderid'] = 0;
		$resultArray['parentlogid'] = 0;
		$resultArray['responsedescription'] = '';
		$resultArray['postcodestatus'] = '';
		$resultArray['threedsecurestatus'] = $eci;
		$resultArray['cavvresponsecode'] = $payerAuthStatus;
		$resultArray['charityflag'] = '';
		$resultArray['showerror'] = false;
		$resultArray['resultisarray'] = false;
		$resultArray['resultlist'] = Array();

		return $resultArray;
    }
}

?>