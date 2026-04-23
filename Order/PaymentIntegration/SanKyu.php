<?php

class SanKyuObj
{
    static function configure()
    {
        global $gSession;

        $gateways = Array();
        $resultArray = Array();
        $currency = $gSession['order']['currencycode'];
        $active = true;

        $smarty = SmartyObj::newSmarty('CreditCardPayment');

        AuthenticateObj::clearSessionCCICookie();

        // read config file
        $SanKyuConfig = PaymentIntegrationObj::readCCIConfigFile('../config/SanKyu.conf', $currency, $gSession['webbrandcode']);

        // make sure merchant details are set
        if (($SanKyuConfig['SERVER'] == '') || ($SanKyuConfig['APILOGIN'] == '') || ($SanKyuConfig['APIPWD'] == ''))
        {
            $active = false;
        }

        $resultArray['gateways'] = '';
        $resultArray['active'] = $active;
        $resultArray['form'] = '';
        $resultArray['scripturl'] = '';
        $resultArray['script'] = '';
        $resultArray['action'] = '';

        return $resultArray;
    }

    static function initialize()
    {
        global $ac_config;
        global $gConstants;
        global $gSession;

        $parameters = Array();

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        // first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
            $SanKyuConfig = PaymentIntegrationObj::readCCIConfigFile('../config/SanKyu.conf',
            $gSession['order']['currencycode'], $gSession['webbrandcode']);

            $successReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) .
            '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

            $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) .
            '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

            $errorCode = 'Error';
            $errorMessage = 'Error while processing card.';
            $errorMessage = urlencode($errorCode . "-" . $errorMessage);

            $failureReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) .
            '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'] . '&error=' . $errorMessage;

            $responseURL = UtilsObj::correctPath($gSession['webbrandweburl']) .
            "PaymentIntegration/SanKyu/SanKyuCallback.php";

            $description = LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'],
            $gSession['browserlanguagecode'], true);


            // convert to 'cents'
            $orderTotalToPayNonDecimal = $gSession['order']['ordertotaltopay'];
            for($dp = 0; $dp < $gSession['order']['currencydecimalplaces']; $dp++)
            {
                $orderTotalToPayNonDecimal *= 10;
            }


            $parameters['transaction_id'] = $gSession['ref'] . '-' . date('YmdHis');
            $parameters['amount'] = $orderTotalToPayNonDecimal;
            $parameters['currency'] = $gSession['order']['currencycode'];
            $parameters['usage'] = $gSession['webbrandapplicationname'];
            $parameters['description'] = $description;
            $parameters['customer_email'] = $gSession['order']['billingcustomeremailaddress'];
            $parameters['customer_phone'] = $gSession['order']['billingcustomertelephonenumber'];
            $parameters['notification_url'] = $responseURL;
            $parameters['return_success_url'] = $successReturnPath;
            $parameters['return_failure_url'] = $failureReturnPath;
            $parameters['return_cancel_url'] = $cancelReturnPath;

            $parameters['billing_address']['first_name'] = $gSession['order']['billingcontactfirstname'];
            $parameters['billing_address']['last_name'] = $gSession['order']['billingcontactlastname'];
            $parameters['billing_address']['address1'] = $gSession['order']['billingcustomeraddress1'];
            $parameters['billing_address']['address2'] = $gSession['order']['billingcustomeraddress2'];
            $parameters['billing_address']['city'] = $gSession['order']['billingcustomercity'];
            $parameters['billing_address']['zip_code'] = $gSession['order']['billingcustomerpostcode'];
            $parameters['billing_address']['country'] = $gSession['order']['billingcustomercountrycode'];

            $parameters['transaction_types'][0] = 'sale';
            $parameters['transaction_types'][1] = 'sale3d';


            // create the xml formatted data
            $postString = self::arrayToXml($parameters);

            $result = self::cURLPost($SanKyuConfig, $postString);

            $xml = simplexml_load_string($result);

            // if SanKyu returns an error, send customer to cancel and display error
            if($xml->status == 'error')
            {
                header('Location:' . $cancelReturnPath . "&error=" . urlencode($xml->code) . "-" . urlencode($xml->message));

                exit;
            }
            else // If no error, redirect customer to payment URL
            {
                $paymentID = $xml->unique_id;
                $paymentURL = $xml->redirect_url;
            }

            // do the payment request
            $active = true;
            $error = '';

            // define Smarty variables
            $smarty->assign('cancel_url', $cancelReturnPath);
            $smarty->assign('payment_url', $paymentURL);
            $smarty->assign('method', 'POST');
            $smarty->assign('parameter', '');

            AuthenticateObj::defineSessionCCICookie();
            $smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
            $smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

            // set the ccidata to remember we have jumped to SanKyu
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
        $resultArray = Array();
        $error = '';

        // payment error
        if (array_key_exists('error', $_GET))
        {
            $error = urldecode($_GET['error']);
            $pieces = explode("-", $error, 2);
            $resultArray['responsecode'] = $pieces[0];
            $resultArray['responsedescription'] = $pieces[1];
        }
        else
        {
            $resultArray['responsecode'] = 'ABORT';
            $resultArray['responsedescription'] = 'TAOPIX: User pressed back button.';
        }

        $resultArray['ref'] = $_GET['ref'];
        $resultArray['authorised'] = false;
        $resultArray['authorisedstatus'] = 0;


        $resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $resultArray['responsecode'];
        $resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $resultArray['responsedescription'];
        $resultArray['data3'] = '';
        $resultArray['data4'] = '';

        $resultArray['errorform'] = 'error.tpl';
        $resultArray['showerror'] = true;

        return $resultArray;
    }

    static function manualCallback()
    {
        global $gSession;

        // all we have is the session reference
        $ref = $gSession['ref'];
        $paymentReceived = 0;

        $retryCount = 15;

        $dbObj = DatabaseObj::getConnection();

        // wait for 30 seconds for automatic callback
        while($retryCount > 0)
        {
            $resultArray = PaymentIntegrationObj::getCciLogEntry($ref);
            if(!array_key_exists('responsecode', $resultArray))
            {
                $resultArray['responsecode'] = '';
            }

            if($resultArray['responsecode'] == '')
            {
                $retryCount--;
                UtilsObj::wait(2);
            }
            else
            {
                $retryCount = 0;
            }
        }

        switch ($resultArray['responsecode'])
        {
            case 'approved':
                $paymentReceived = 1;
                break;
            default:
                $paymentReceived = 0;
        }

        $resultArray['ref'] = $ref;
        $resultArray['showerror'] = false;
        $resultArray['paymentreceived'] = $paymentReceived;

        return $resultArray;
    }

    static function automaticCallback()
    {
        global $ac_config;
        global $gSession;

        $resultArray = Array();
        $result = '';
        $authorised = false;
        $active = true;
        $error = '';
        $status = '';
        $description = '';
        $ref = $_GET['ref'];

        $cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

        if (empty($cciLogEntry))
        {
            // no entry yet, this must be the first callback
            // we do have a session
            $gSession = DatabaseObj::getSessionData($ref);

            $webbrandcode = $gSession['webbrandcode'];
            $currencyCode = $gSession['order']['currencycode'];
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
            $update = true;
            $parentLogId = $cciLogEntry['id'];
            $orderId = $cciLogEntry['orderid'];
        }

        $SanKyuConfig = PaymentIntegrationObj::readCCIConfigFile('../config/SanKyu.conf', $currencyCode, $webbrandcode);

        // read POST variables
        $sanKyu['signature'] = UtilsObj::getPOSTParam('signature');
        $sanKyu['payment_transaction_channel_token'] = UtilsObj::getPOSTParam('payment_transaction_channel_token');
        $sanKyu['payment_transaction_unique_id'] = UtilsObj::getPOSTParam('payment_transaction_unique_id');
        $sanKyu['wpf_transaction_id'] = UtilsObj::getPOSTParam('wpf_transaction_id');
        $sanKyu['payment_transaction_transaction_type'] = UtilsObj::getPOSTParam('payment_transaction_transaction_type');
        $sanKyu['wpf_status'] = UtilsObj::getPOSTParam('wpf_status');
        $sanKyu['wpf_unique_id'] = UtilsObj::getPOSTParam('wpf_unique_id');
        $sanKyu['notification_type'] = UtilsObj::getPOSTParam('notification_type');

        $amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');
        $result = $sanKyu['wpf_status'];
        $tranid = $sanKyu['wpf_unique_id'];
        $auth = $sanKyu['payment_transaction_unique_id'];
        $returnedHash = $sanKyu['signature'];
        $ref = $sanKyu['wpf_transaction_id'];
        $taopixRef = $sanKyu['wpf_transaction_id'];
        $trackid = $sanKyu['wpf_status'];
        $paymentid = $sanKyu['payment_transaction_unique_id'];

        $postdate = '';
        $cardtype = '';
        $payinst = '';
        $liability = '';
        $transactionType = '';

        switch($result)
        {
            case 'approved':
                $active = true;
                $authorised = true;
                $authorisedStatus = 1;
                $paymentReceived = 1;
                break;
            case 'declined':
                $active = true;
                $authorised = false;
                $authorisedStatus = 2;
                $paymentReceived = 0;
                break;
            case 'error':
                $active = true;
                $authorised = false;
                $authorisedStatus = 2;
                $paymentReceived = 0;
                break;
            case 'rejected':
                $active = true;
                $authorised = false;
                $authorisedStatus = 2;
                $paymentReceived = 0;
                break;
        }

        // write to log file.
        $serverTimestamp = DatabaseObj::getServerTime();
        $serverDate = date('Y-m-d');
        $serverTime = date('H:i:s');

        PaymentIntegrationObj::logPaymentGatewayData($SanKyuConfig, $serverTimestamp);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $taopixRef;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '000';
        $resultArray['formattedcharges'] = '000';
        $resultArray['authorised'] = $authorised;
        $resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $tranid;
        $resultArray['formattedtransactionid'] = $tranid;
        $resultArray['responsecode'] = $trackid;
        $resultArray['responsedescription'] = $result;
        $resultArray['authorisationid'] = $auth;
        $resultArray['formattedauthorisationid'] = $auth;
        $resultArray['bankresponsecode'] = $result;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = $cardtype;
        $resultArray['cvvresponsecode'] = $auth;
        $resultArray['paymentcertificate'] = $paymentid;
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = '';
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
        $resultArray['transactiontype'] = $transactionType;
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
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function cURLPost($pSanKyuArray, $pPostString, $pReconcile=false)
    {
        $sslCertificate =  UtilsObj::getTaopixWebInstallPath('libs/internal/curl/curl-ca-bundle.pem');
        $apiLogin = $pSanKyuArray['APILOGIN'];
        $apiPassword = $pSanKyuArray['APIPWD'];

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        if($pReconcile)
        {
          curl_setopt($ch, CURLOPT_URL, $pSanKyuArray['SERVER'] . '/reconcile');
        }
        else
        {
          curl_setopt($ch, CURLOPT_URL, $pSanKyuArray['SERVER']);
        }
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $pPostString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "Content-length: ".strlen($pPostString)));
        curl_setopt($ch, CURLOPT_USERPWD, $apiLogin . ":" . $apiPassword);

        // configure curl for ssl
        curl_setopt($ch, CURLOPT_PORT , 443);

		// configure the TLS library to attempt to figure out the remote SSL protocol version and use its highest supported TLS version
		// 0 = CURLOPT_SSLVERSION_DEFAULT
        curl_setopt($ch, CURLOPT_SSLVERSION, 0);

        curl_setopt($ch, CURLOPT_CAINFO, $sslCertificate);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, "PEM");
        curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $apiPassword);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $result = curl_exec($ch);

        //close connection
        curl_close($ch);

        if ($result)
        {
            return $result;
        }
        else
        {
            return curl_error($ch);
        }
    }


    static function xmlEscape($str)
    {
        return htmlspecialchars($str, ENT_COMPAT, 'UTF-8');
    }


    static function arrayToXml($arr)
    {
        $data = '<?xml version="1.0" encoding="UTF-8"?>';
        $data .= '<wpf_payment>';

        foreach ($arr as $key => $value)
        {
            $data .= '<' . $key . '>';
            if(is_array($value))
            {
                foreach ($value as $subkey => $subvalue)
                {
                    if($key == 'transaction_types')
                    {
                        $data .= '<transaction_type>' . self::xmlEscape($subvalue) . '</transaction_type>';
                    }
                    else
                    {
                        $data .= '<' . $subkey . '>' . self::xmlEscape($subvalue) . '</' . $subkey . '>';
                    }
                }
            }
            else
            {
                $data .= self::xmlEscape($value);
            }
            $data .= '</' . $key . '>';
        }
        $data .= '</wpf_payment>';

        return $data;
    }
}

?>