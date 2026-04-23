<?php

class PayFastObj
{
    static function configure()
    {
		global $gSession;
        $resultArray = Array();
		$active = false;
		$form = '';
		$script = '';
		$action = '';
		$currency = $gSession['order']['currencycode'];

        AuthenticateObj::clearSessionCCICookie();

        $PayFastConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayFast.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
        $currencyList = $PayFastConfig['CURRENCIES'];
        $currency = $gSession['order']['currencyisonumber'];
        $active = true;

        // test for supported currencies
        if (strpos($currencyList, $currency) === false)
        {
            $active = false;
        }

        $resultArray['active'] = $active;
        $resultArray['form'] = $form;
        $resultArray['scripturl'] = '';
        $resultArray['script'] = $script;
        $resultArray['action'] = $action;

        return $resultArray;
    }

    static function initialize()
    {
        global $gConstants;
        global $gSession;

        $requestParameters = Array();
        $parameters = Array();

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        // first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
            $PayFastConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayFast.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
            $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
            $returnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
            $notifyPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccAutomaticCallback&ref=' . $gSession['ref'];

            //Read settings from the config file.
            $paymentURL = $PayFastConfig['PAYMENTURL'];
            $merchantUserName = $PayFastConfig['MERCHANTUSERNAME'];
            $merchantID = $PayFastConfig['MERCHANTID'];
            $merchantKey = $PayFastConfig['MERCHANTKEY'];
            $passPhrase = $PayFastConfig['PASSPHRASE'];
            $totalToPay = $gSession['order']['ordertotaltopay'];
            $myTime = time();
            $orderRef = $gSession['ref'] . '_'. $myTime;
            $dateTime = date("Y-m-d") . "T" . date("H:i:s");
            $productName =LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
            $orderDataRaw = $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);

            // billing
			$customerFirstName = $gSession['order']['billingcontactfirstname'];
			$customerLastName = $gSession['order']['billingcontactlastname'];
			$customerEmail = $gSession['order']['billingcustomeremailaddress'];

            $params = array(
                'merchant_id' => $merchantID,
                'merchant_key' => $merchantKey,
                'return_url' => $returnPath,
                'cancel_url' => $cancelReturnPath,
                'notify_url' => $notifyPath,
                'name_first' => $customerFirstName,
                'name_last'  => $customerLastName,
                'email_address'=> $customerEmail,
                'm_payment_id' => $orderRef, //Unique payment ID to pass through to notify_url
                'amount' => $totalToPay,
                'item_name' => str_replace("\"", " ", $productName),
                'item_description' => str_replace("\"", " ", $orderDataRaw),
                'custom_int1' => $gSession['ref'], //custom integer to be passed through
				'custom_str1' => date("H:i:s"),
				'custom_str2' => date("Y-m-d")
                );

                // Create GET string
				$pfOutput = '';
                foreach( $params as $key => $val )
                {
                  if(!empty($val))
                  {
                    $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
                  }
                }

            // Remove last ampersand
            $getString = substr( $pfOutput, 0, -1 );
			if( $passPhrase != 'test' )
			{
				$getString .= '&passphrase='.$passPhrase;
			}
            $params['signature'] = md5( $getString );

            // define Smarty variables
            $smarty->assign('payment_url', $paymentURL); //Test
            $smarty->assign('method', "POST");
            $smarty->assign('cancel_url', $cancelReturnPath);
            $smarty->assign('parameter', $params);

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
                //Use this for v5.5
				$smarty->displayLocale('order/PaymentIntegration/PaymentRequest_large.tpl');

				//Use this for dev.taopix.com
                //$smarty->displayLocale('order/PaymentIntegration/desktopPaymentRequest.tpl');
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

    static function manualCallback()
    {
        global $gSession;

    	// all we have is the session reference
    	$ref = $gSession['ref'];
		$tempArray = PaymentIntegrationObj::getCciLogEntry($ref);
        $resultArray['ref'] = $ref;
		$resultArray['authorised'] = $tempArray['authorised'];
		$resultArray['showerror'] = false;

        return $resultArray;
    }

    static function automaticCallback()
    {
		global $ac_config;
        global $gSession;

        $PayFastConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayFast.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
		$hostSelector = $PayFastConfig['HOSTSELECTOR'];

        $resultArray = Array();
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $showError = false;
        $update = false;

		//Begin the ITN Process Here !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        //Return a 200 header so payfast know we are where we say we are and all is ok.
        header( 'HTTP/1.0 200 OK' );
        flush();

        // Posted variables from ITN
        $pfData = $_POST;

        // Strip any slashes in data
        foreach( $pfData as $key => $val )
        {
            $pfData[$key] = stripslashes( $val );
        }

		$signCheck = 1;
        $ipCheck = 1;
        $amountCheck = 1;
        $dataCheck = 1;

        //Security checks - Check 1
        // $pfData includes of ALL the fields posted through from PayFast, this includes the empty strings
		$pfParamString = '';
        foreach( $pfData as $key => $val )
        {
            if( $key != 'signature' )
            {
                $pfParamString .= $key .'='. urlencode( $val ) .'&';
            }
        }

         // Remove the last '&' from the parameter string
        $pfParamString = substr( $pfParamString, 0, -1 );
        $pfTempParamString = $pfParamString;

        // If a passphrase has been set in the PayFast Settings, then it needs to be included in the signature string.
        $passPhrase = $PayFastConfig['PASSPHRASE'];
        if( $passPhrase != 'test' )
        {
            $pfTempParamString .= '&passphrase='.urlencode( $passPhrase );
        }

        $signature = md5( $pfTempParamString );

        if($signature!=$pfData['signature'])
        {
            //The order will not be completed
             $signCheck = 0;
        }

        //Security checks - Check 2
        // Variable initialization
        $validHosts = array(
            'www.payfast.co.za',
            'sandbox.payfast.co.za',
            'w1w.payfast.co.za',
            'w2w.payfast.co.za',
        );

        $validIps = array();

        foreach( $validHosts as $pfHostname )
        {
            $ips = gethostbynamel( $pfHostname );

            if( $ips !== false )
            {
                $validIps = array_merge( $validIps, $ips );
            }
        }

        // Remove duplicates
        $validIps = array_unique( $validIps );

        if( !in_array( $_SERVER['REMOTE_ADDR'], $validIps ) )
        {
            $ipCheck = 0;
        }

        //Security Checks - Check 3
        $cartTotal = $gSession['order']['ordertotaltopay']; //This amount needs to be sourced from your application
        if( abs( floatval( $cartTotal ) - floatval( $pfData['amount_gross'] ) ) > 0.01 )
        {
            $amountCheck = 0;
        }

        //Security Checks - Check 4
        if( in_array( 'curl', get_loaded_extensions() ) )
        {
            // Variable initializationleft: (($('#headerBar').width()*0.5)-75) , position:'absolute'}

			$pfHost = $validHosts[$hostSelector];

            $url = 'https://'. $pfHost .'/eng/query/validate';

            // Create default cURL object
            $ch = curl_init();

            // Set cURL options - Use curl_setopt for freater PHP compatibility
            // Base settings
			$uaString = 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)';
            curl_setopt( $ch, CURLOPT_USERAGENT, $uaString );  // Set user agent
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );      // Return output as string rather than outputting it
            curl_setopt( $ch, CURLOPT_HEADER, false );             // Don't include header in output
            curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

            // Standard settings
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $pfParamString );
            //curl_setopt( $ch, CURLOPT_TIMEOUT, PF_TIMEOUT );
            if( !empty( $pfProxy ) )
            {
                curl_setopt( $ch, CURLOPT_PROXY, $proxy );
            }
            // Execute CURL
            $response = curl_exec( $ch );
            curl_close( $ch );

        }
        else
        {
            $header = '';
            $res = '';
            $headerDone = false;

            // Construct Header
            $header = "POST /eng/query/validate HTTP/1.0\r\n";
            $header .= "Host: ". $pfHost ."\r\n";
            $header .= "User-Agent: ". PF_USER_AGENT ."\r\n";
            $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $header .= "Content-Length: " . strlen( $pfParamString ) . "\r\n\r\n";

            // Connect to server
            $socket = fsockopen( 'ssl://'. $pfHost, 443, $errno, $errstr, PF_TIMEOUT );

            // Send command to server
            fputs( $socket, $header . $pfParamString );

            // Read the response from the server
            while( !feof( $socket ) )
            {
                $line = fgets( $socket, 1024 );

                // Check if we are finished reading the header yet
                if( strcmp( $line, "\r\n" ) == 0 )
                {
                    // read the header
                    $headerDone = true;

                }
                // If header has been processed
                else if( $headerDone )
                {
                    // Read the main response
                    $response .= $line;
                }
            }
        }

        $lines = explode( "\r\n", $response );
        $verifyResult = trim( $lines[0] );

        if( strcasecmp( $verifyResult, 'VALID' ) != 0 )
        {
           $dataCheck = 0;
        }

        //Check the order has not already been processed
        $pfPaymentId = $pfData['pf_payment_id'];
        //query your database and compare in order to ensure you have not processed this payment allready

        switch( $pfData['payment_status'] )
        {
            case 'COMPLETE':
				// If complete, update your application, email the buyer and process the transaction as paid
                $authorised = true;
				$authorisedStatus = 1;
				$result = 'COMPLETE';
				break;
            case 'FAILED':
				// There was an error, update your application and contact a member of PayFast's support team for further assistance
				$authorised = false;
				$authorisedStatus = 0;
				$result = 'FAILED';
				break;
            case 'PENDING':
				// The transaction is pending, please contact a member of PayFast's support team for further assistance
				$authorised = false;
				$authorisedStatus = 0;
				$result = 'PENDING';
				break;
            default:
				// If unknown status, do nothing (safest course of action)
				$authorised = false;
				$authorisedStatus = 0;
				$result = 'DEFAULT';
				break;
        }
        //End of ITN Process !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

		$resultTemp='';
		$checkFlag = 0;
        //Analyse the results of the security checks
        if($signCheck == 0)
        {
            $authorised = false;
            $authorisedStatus = 0;
            $resultTemp = $resultTemp.' SIGNATURECHECKFAIL';
			$checkFlag = 1;
        }

        if($ipCheck == 0)
        {
            $authorised = false;
            $authorisedStatus = 0;
            $resultTemp = $resultTemp.' IPCHECKFAIL';
			$checkFlag = 1;
        }

        if($amountCheck == 0)
        {
            $authorised = false;
            $authorisedStatus = 0;
            $resultTemp = $resultTemp.' AMOUNTCHECKFAIL';
			$checkFlag = 1;
        }

        if($dataCheck == 0)
        {
            $authorised = false;
            $authorisedStatus = 0;
            $resultTemp = $resultTemp.' DATACHECKFAIL';
			$checkFlag = 1;
        }

		$paymentRecieved = 0;
		if($authorisedStatus == 1)
		{
			$paymentRecieved = 1;
		}

		$checkFlag = 1;
		if(array_key_exists('CHECKOVERIDE', $PayFastConfig) && $checkFlag == 1)
        {
			// include the email creation module
			require_once('../Utils/UtilsEmail.php');

            $authorised = true;
            $authorisedStatus = 1;
			$paymentRecieved = 0;

            $result = $resultTemp;
            $transErrorName  = $result;
			$transErrorEmail = $PayFastConfig['CHECKOVERIDE'];

			$emailContent ="SECURITY CHECK ERROR : ".$transErrorName." , Ref : ".$pfData['m_payment_id']."Transcation ID : ".$pfData['m_payment_id']." , authorised : ".$authorised.", authorisedStatus : ".$authorisedStatus." , PAYFAST PARAMETERS = ".$pfParamString;

			$emailObj = new TaopixMailer();
			$emailObj->sendTemplateEmail('admin_transactionerror', '', '', '', '', $transErrorName, $transErrorEmail, '', '',0,Array('data' => $emailContent));
        }

		// write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime =  date("H:i:s");

		$PayFastConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayFast.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
		PaymentIntegrationObj::logPaymentGatewayData($PayFastConfig, $serverTime);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $pfData['m_payment_id'];
        $resultArray['amount'] = $cartTotal;
        $resultArray['formattedamount'] = floatval( $pfData['amount_gross'] );
        $resultArray['charges'] = str_replace("-","",$pfData['amount_fee']);
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $pfData['m_payment_id'];
        $resultArray['formattedtransactionid'] = $pfData['pf_payment_id'];
        $resultArray['responsecode'] = '';
        $resultArray['responsedescription'] = '';
        $resultArray['authorisationid'] = '';  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = '';
        $resultArray['bankresponsecode'] = $result;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentmeans'] = '';
        $resultArray['paymentdate'] = $pfData['custom_str2'];
        $resultArray['paymenttime'] = $pfData['custom_str1'];
        $resultArray['paymentreceived'] = $paymentRecieved;
        $resultArray['formattedpaymentdate'] = '';
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
        $resultArray['currencycode'] = $gSession['order']['currencycode'];
        $resultArray['webbrandcode'] = $gSession['webbrandcode'];

        $resultArray['charityflag'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['update'] = false;
        $resultArray['orderid'] = 0;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = $showError;

        return $resultArray;
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
}

?>