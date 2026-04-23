<?php

class VCSObj
{
    static function configure()
    {
        global $gSession;
        $resultArray = Array();
        $active = true;

        AuthenticateObj::clearSessionCCICookie();
        // test for VCS currencies
        // South African Rand is the only supported currency
        $currencyList = '710';

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
			$VCSConfig = PaymentIntegrationObj::readCCIConfigFile('../config/VCS.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
			$paymentServer = $VCSConfig['VCSSERVER'];
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$terminalID = $VCSConfig['TERMINALID'];
			$secretKey = $VCSConfig['SECRETKEY'];
			//Initialise payment parameters
			$orderID = $gSession['ref'] . '-'. time();
			$orderData = UtilsObj::encodeString($gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true),false);
			$amount = $gSession['order']['ordertotaltopay'];
			$currency = $gSession['order']['currencycode'];
			$email = $gSession['order']['billingcustomeremailaddress'];

			//All of the payment parameters are passed as an array
			$parameters = array(
				'p1'	=> $terminalID,
				'p2'			  => $orderID,
				'p3'			  => $orderData,
				'p4'			  => $amount,
				'p5'		      => $currency,
				'p10'		      => $cancelReturnPath,
				'p12' 		      => 'N',
				'cardholderEmail' => $email,
				'm_1'			  => $gSession['ref']
			);

			$generatedHash = self::generateHash($parameters, $secretKey);

			$hashArray = array('Hash' => $generatedHash);

			$params = array_merge($parameters, $hashArray);

			// define Smarty variables
			$smarty->assign('payment_url', $paymentServer);
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('parameter', $params);
			$smarty->assign('method', "POST");

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to Pagseguro
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
        $result = '';
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

        $resultArray = Array();
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $showError = false;
        $update = false;

        $ref = $_GET['ref'];

        $cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

		if (empty($cciLogEntry))
		{
			// no entry yet, this must be the first callback
			// we do have a session
			$webbrandcode = $gSession['webbrandcode'];
			$currencyCode = $gSession['order']['currencycode'];
			$amount = $gSession['order']['ordertotaltopay'];
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

       	$VCSConfig = PaymentIntegrationObj::readCCIConfigFile('../config/VCS.conf',$currencyCode,$webbrandcode);
		$secretKey = $VCSConfig['SECRETKEY'];

		$returnParams = self::getReturnParams();
		$generateReturnHash = self::generateHash($returnParams, $secretKey);


		if ($callback == 'automatic')
		{
			if(!empty($cciLogEntry))
			{
				echo "<CallBackResponse>Accepted<CallBackResponse>";
			}
		}

		//check to see if the payment was approved
		if ($returnParams['p12'] == '00' || $returnParams['p12'] = '0')
		{
			$authorised = true;
			$authorisedStatus = 1;
		}

       	$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime = date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($VCSConfig, $serverTimestamp);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $returnParams['p3'];
        $resultArray['formattedtransactionid'] = $returnParams['p3'];
        $resultArray['responsecode'] = $returnParams['p12'];
        $resultArray['responsedescription'] = $returnParams['p12'];
        $resultArray['authorisationid'] = $returnParams['p3'];  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $returnParams['p3'];
        $resultArray['bankresponsecode'] = $returnParams['p3'];
        $resultArray['cardnumber'] = $returnParams['MaskedCardNumber'];
        $resultArray['formattedcardnumber'] = $returnParams['MaskedCardNumber'];
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = $returnParams['p7'];
        $resultArray['paymenttime'] = $serverTime;
		$resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
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
        $resultArray['cavvresponsecode'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = $orderId;
        $resultArray['parentlogid'] = $parentLogId;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = $showError;

        return $resultArray;
    }

    static function getReturnParams()
    {
		$resultArray = Array();

		foreach($_POST as $key => $value)
		{

			if ($key != 'server' && $key != '')
			{
				$resultArray[$key] = $value;
			}

		}
		return $resultArray;
    }


	static function generateHash($pParams, $pSecretKey)
	{
		$hash = '';

		foreach ($pParams as $key => $val)
		{
			if ($key != 'Hash' && $key != '')
			{
				$hash .= $val;
			}
		}

		$generatedHash = md5($hash.$pSecretKey);

		return $generatedHash;
	}
}
?>