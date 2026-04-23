<?php

class DirecPayObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();
        $active = true;

        AuthenticateObj::clearSessionCCICookie();

        // test for DirecPay supported currencies (Indian Rupee only)
        if ($gSession['order']['currencyisonumber'] != '356')
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
        global $gSession;

        $custName = '';
        $custAddress = '';
        $custCity = '';
        $custState = '';
        $custPinCode = '';
        $custCountry = '';
        $custPhoneNo1 = '';
        $custPhoneNo2 = '';
        $custPhoneNo3 = '';
        $custMobileNo = '';
        $custEmailId = '';
        $deliveryName = '';
        $deliveryAddress = '';
        $deliveryCity = '';
        $deliveryState = '';
        $deliveryPinCode = '';
        $deliveryCountry = '';
        $deliveryPhNo1 = '';
        $deliveryPhNo2 = '';
        $deliveryPhNo3 = '';
        $deliveryMobileNo = '';
        $otherNotes = '';

        $operatingMode = 'DOM';
        $country = 'IND';
        $currency = 'INR';
        $amount = '';
        $ordernumber = '';
        $otherdetails = 'NULL';
        $successurl = '';
        $failureurl = '';
        $collaborator = '';

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        // first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
            $DirecPayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/DirecPay.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

            $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
            $successurl = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&amp;ref=' . $gSession['ref'] . '&amp;auth=1';
            $failureurl = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&amp;ref=' . $gSession['ref'] . '&amp;auth=0';

            $server = $DirecPayConfig['DPSERVER'];
            $MID = $DirecPayConfig['DPMID'];
            $testMode = $DirecPayConfig['DPTEST'];
            $AESKey = $DirecPayConfig['DPAESKEY'];

            $amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');

            $ordernumber = $gSession['ref'] . '_' . time();

            // description
            $otherNotes = $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);

            if ($testMode == '0')
            {
                $collaborator = 'DirecPay';
            }
            else
            {
                $collaborator = 'TOML';
            }

            $custName = $gSession['order']['billingcontactfirstname'] . ' ' . $gSession['order']['billingcontactlastname'];
            $address = '';
            if ($gSession['order']['billingcustomeraddress1'] != '')
            {
                $address .= $gSession['order']['billingcustomeraddress1'];
            }
            if ($gSession['order']['billingcustomeraddress2'] != '')
            {
                if ($address != '')
                {
                    $address .= ' ';
                }
                $address .= $gSession['order']['billingcustomeraddress2'];
            }
            if ($gSession['order']['billingcustomeraddress3'] != '')
            {
                if ($address != '')
                {
                    $address .= ' ';
                }
                $address .= $gSession['order']['billingcustomeraddress3'];
            }
            if ($gSession['order']['billingcustomeraddress4'] != '')
            {
                if ($address != '')
                {
                    $address .= ' ';
                }
                $address .= $gSession['order']['billingcustomeraddress4'];
            }
            $custAddress = $address;
            $custCity = $gSession['order']['billingcustomercity'];

            if ($gSession['order']['billingcustomercounty'] != '')
            {
                $custState = $gSession['order']['billingcustomercounty'];
            }

            if ($gSession['order']['billingcustomerstate'] != '')
            {
                $custState = $gSession['order']['billingcustomerstate'];
            }

            $custPinCode = UtilsObj::LeftChars($gSession['order']['billingcustomerpostcode'], 6);
            $custCountry = $gSession['order']['billingcustomercountrycode'];
            $custPhoneNo1 = substr($gSession['order']['billingcustomertelephonenumber'], 0, 3);
            $custPhoneNo2 = substr($gSession['order']['billingcustomertelephonenumber'], 3, 3);
            $custPhoneNo3 = substr($gSession['order']['billingcustomertelephonenumber'], 6);
            $custMobileNo = UtilsObj::LeftChars($gSession['order']['billingcustomertelephonenumber'], 10);
            $custEmailId = $gSession['order']['billingcustomeremailaddress'];

            $deliveryName = $gSession['shipping'][0]['shippingcontactfirstname'] . ' ' . $gSession['shipping'][0]['shippingcontactlastname'];
            $address = '';
            if ($gSession['shipping'][0]['shippingcustomeraddress1'] != '')
            {
                $address .= $gSession['shipping'][0]['shippingcustomeraddress1'];
            }
            if ($gSession['shipping'][0]['shippingcustomeraddress2'] != '')
            {
                if ($address != '')
                {
                    $address .= ' ';
                }
                $address .= $gSession['shipping'][0]['shippingcustomeraddress2'];
            }
            if ($gSession['shipping'][0]['shippingcustomeraddress3'] != '')
            {
                if ($address != '')
                {
                    $address .= ' ';
                }
                $address .= $gSession['shipping'][0]['shippingcustomeraddress3'];
            }
            if ($gSession['shipping'][0]['shippingcustomeraddress4'] != '')
            {
                if ($address != '')
                {
                    $address .= ' ';
                }
                $address .= $gSession['shipping'][0]['shippingcustomeraddress4'];
            }
            $deliveryAddress = $address;
            $deliveryCity = $gSession['shipping'][0]['shippingcustomercity'];
            if ($gSession['shipping'][0]['shippingcustomercounty'] != '')
            {
                $deliveryState = $gSession['shipping'][0]['shippingcustomercounty'];
            }

            if ($gSession['shipping'][0]['shippingcustomerstate'] != '')
            {
                $deliveryState = $gSession['shipping'][0]['shippingcustomerstate'];
            }
            $deliveryPinCode = UtilsObj::LeftChars($gSession['shipping'][0]['shippingcustomerpostcode'], 6);
            $deliveryCountry = $gSession['shipping'][0]['shippingcustomercountrycode'];
            $deliveryPhNo1 = substr($gSession['shipping'][0]['shippingcustomertelephonenumber'], 0, 3);
            $deliveryPhNo2 = substr($gSession['shipping'][0]['shippingcustomertelephonenumber'], 3, 3);
            $deliveryPhNo3 = substr($gSession['shipping'][0]['shippingcustomertelephonenumber'], 6);
            $deliveryMobileNo = UtilsObj::LeftChars($gSession['shipping'][0]['shippingcustomertelephonenumber'], 10);

            // Store billing data
            $billingArray = array(
                $custName, $custAddress, $custCity, $custState, $custPinCode,
                $custCountry, $custPhoneNo1, $custPhoneNo2, $custPhoneNo3,
                $custMobileNo, $custEmailId, $otherNotes
            );

            // Store shipping data
            $shippingArray = array(
                $deliveryName, $deliveryAddress, $deliveryCity, $deliveryState,
                $deliveryPinCode, $deliveryCountry, $deliveryPhNo1, $deliveryPhNo2,
                $deliveryPhNo3, $deliveryMobileNo,
            );

            // Store request params
            $requestArray = array(
                $MID, $operatingMode, $country, $currency, $amount,
                $ordernumber, $otherdetails, $successurl, $failureurl,
                $collaborator
            );

            $DirecPayAES = UtilsObj::getTaopixWebInstallPath('Order/PaymentIntegration/DirecPay/Crypt/AES.php');
            if (file_exists($DirecPayAES))
            {
                require_once $DirecPayAES;
            }

            $aesObj = new Crypt_AES();
            $secret = base64_decode($AESKey);
            $aesObj->setKey($secret);

            $paramsRequest = base64_encode($aesObj->encrypt(implode('|', $requestArray)));
            $paramsBilling = base64_encode($aesObj->encrypt(implode('|', $billingArray)));
            $paramsShipping = base64_encode($aesObj->encrypt(implode('|', $shippingArray)));

            $paramsRequest = preg_replace('/\n/', '', $paramsRequest);
            $paramsBilling = preg_replace('/\n/', '', $paramsBilling);
            $paramsShipping = preg_replace('/\n/', '', $paramsShipping);

            $parameters = array(
                'requestparameter' => $paramsRequest,
                'billingDtls' => $paramsBilling,
                'shippingDtls' => $paramsShipping,
                'merchantId' => $MID
            );
            // define Smarty variables
			$smarty->assign('payment_url', $server);
            $smarty->assign('method', 'POST');
            $smarty->assign('cancel_url', $cancelReturnPath);
            $smarty->assign('parameter', $parameters);

            AuthenticateObj::defineSessionCCICookie();
            $smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
            $smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

            // set the ccidata to remember we have jumped to DirecPay
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
        $resultArray['result'] = '';
        $resultArray['ref'] = $_GET['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function confirm($callback)
    {
        global $gSession;

        $resultArray = Array();
        $error = '';
        $update = false;
        $parentLogId = 0;
        $orderId = 0;
        $authorised = false;
        $showError = false;

		/* pull vars from gSession */
        $ref = $gSession['ref'];

		if ($callback == 'manual')
		{
			$currencyCode = $gSession['order']['currencycode'];
			$webBrandCode = $gSession['webbrandcode'];
			$payeremail = $gSession['order']['billingcustomeremailaddress'];
			$userIpAddr = $gSession['order']['useripaddress'];
			$auth = $_GET['auth'];
		}
		else
		{
			// these will be set
			// on automatic callback
			$currencyCode = '';
			$webBrandCode = '';
			$payeremail = '';
			$userIpAddr = '';
			$auth = null;
		}

        $serverTime = DatabaseObj::getServerTime();

        $DirecPayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/DirecPay.conf', $currencyCode, $webBrandCode);
        $server = $DirecPayConfig['DPSERVER'];

        $responseparams = explode("|", filter_input(INPUT_POST, 'responseparams'));

        $direcPayRefId = $responseparams[0];
        $flag = $responseparams[1];
        $country = $responseparams[2];
        $currency = $responseparams[3];
        $otherDetails = $responseparams[4];
        $merchOrderNo = $responseparams[5];
        $amount = $responseparams[6];

		if ($ref <= 0)
		{
			$ref = $_GET['ref'];
		}

        $logsData['dateTime'] = $serverTime;
        $logsData['sessionref'] = $ref;
        $logsData['auth'] = $auth;
        $logsData['direcpayrefid'] = $direcPayRefId;
        $logsData['flag'] = $flag;
        $logsData['country'] = $country;
        $logsData['currency'] = $currency;
        $logsData['amount'] = $amount;
        $logsData['merchorderno'] = $merchOrderNo;
        $logsData['otherdetails'] = $otherDetails;
        PaymentIntegrationObj::logPaymentGatewayData($DirecPayConfig, $serverTime, $error, $logsData);

        if ($callback == 'manual')
        {
            if ($auth == 1)
            {
                if ($flag == 'SUCCESS')
                {
                    $authorisedStatus = 1;
                    $authorised = true;
                    $paymentReceived = 1;
                }
                else
                {
                    // this will be a deferred
                    // payment
                    $authorisedStatus = 2;
                    $authorised = true;
                    $paymentReceived = 0;
                }
            }
            else
            {
                $error = $flag;
            }
        }
        else // automatic callback from scheduler
        {
            // get ccilog entry for deferred payment
            $cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

            $webBrandCode = $cciLogEntry['webbrandcode'];
            $currencyCode = $cciLogEntry['currencycode'];
            $parentLogId = $cciLogEntry['id'];
            $orderId = $cciLogEntry['orderid'];
			$payeremail = $cciLogEntry['payeremail'];
			$userIpAddr = $cciLogEntry['payerid'];

            if ($flag === 'SUCCESS')
            {
                $authorisedStatus = 1;
                $authorised = true;
                $paymentReceived = 1;
                $update = true;
            }
            elseif ($flag === 'FAIL')
            {
                $authorisedStatus = 0;
                $authorised = false;
                $paymentReceived = 0;
                $update = true;
            }
            else
            {
				// there has been no change in status
				// so we can stop processing
				exit;
            }
        }

        if ($error != '')
        {
            $resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': DirecPay Failure';
            $resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $error;
            $resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $direcPayRefId;
            $resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $merchOrderNo;
            $resultArray['errorform'] = 'error.tpl';
            $showError = true;
            $authorised = false;
            $authorisedStatus = 0;
            $paymentReceived = 0;
        }

        $resultArray['result'] = '';
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '0.00';
        $resultArray['formattedcharges'] = 0.00;
        $resultArray['authorised'] = $authorised;
        $resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['paymentdate'] = $serverTime;
        $resultArray['paymenttime'] = '';
        $resultArray['authorisationid'] = '';
        $resultArray['transactionid'] = $merchOrderNo;
        $resultArray['paymentmeans'] = '';
        $resultArray['addressstatus'] = '';
        $resultArray['payerid'] = $userIpAddr;
        $resultArray['payerstatus'] = $server;
        $resultArray['payeremail'] = $payeremail;
        $resultArray['business'] = '';
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = '';
        $resultArray['settleamount'] = '';
        $resultArray['paymentreceived'] = $paymentReceived;
        $resultArray['formattedpaymentdate'] = $serverTime;
        $resultArray['formattedtransactionid'] = $direcPayRefId;
        $resultArray['formattedauthorisationid'] = '';
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['responsecode'] = $flag;
        $resultArray['bankresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = $orderId;
        $resultArray['parentlogid'] = $parentLogId;
        $resultArray['responsedescription'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['charityflag'] = '';
        $resultArray['currencycode'] = $currencyCode;
        $resultArray['webbrandcode'] = $webBrandCode;
        $resultArray['showerror'] = $showError;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();

        return $resultArray;
    }

    public static function paymentInquiry()
    {
        global $gSession;
		global $ac_config;

		require_once('../Utils/UtilsCoreIncludes.php');
		require_once('../Order/PaymentIntegration/PaymentIntegration.php');
		require_once('../Order/Order_model.php');

		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
        $DirecPayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/DirecPay.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
		$serverTime = DatabaseObj::getServerTime();
        $MID = $DirecPayConfig['DPMID'];

        $toProcessArray = self::getCCILogEntries();
        $toProcessCount = count($toProcessArray);

        if ($toProcessCount > 0)
        {
            PaymentIntegrationObj::logPaymentGatewayData($DirecPayConfig, $serverTime, $serverTime . ' - ' . $toProcessCount . ' records to process');

            foreach ($toProcessArray as $entry)
            {
                if (strlen($entry['transaction']) < 1)
                {
                    continue;
                }

				if(substr(strrev($ac_config['WEBURL']), 0, 1) == '/')
				{
					$webURL = substr($ac_config['WEBURL'], 0, strlen($ac_config['WEBURL']) - 1);
				}
				else
				{
					$webURL = $ac_config['WEBURL'];
				}

                $autoCallbackURL = $webURL . '/PaymentIntegration/DirecPay/DirecPayCallback.php?ref=' . $entry['session'];

                $pParamArray = array(
                    'requestparams' => implode('|', array(
                        $entry['transaction'],
                        $MID,
                        $autoCallbackURL
                    ))
                );

                /*
                * there will be no response as
				* it is posted to the $autoCallbackURL
				*/
                self::cURLPost($DirecPayConfig['DPSERVERAUTO'], $pParamArray);
            }
        }
        else
        {
            PaymentIntegrationObj::logPaymentGatewayData($DirecPayConfig, $serverTime, $serverTime . " - nothing to process");
        }
    }

    static function getCCILogEntries()
    {
        $resultArray = Array();

        $dbObj = DatabaseObj::getConnection();
        if ($dbObj)
        {
            $sql = 'SELECT cl.sessionid, cl.formattedtransactionid
					FROM ccilog cl JOIN orderheader oh ON (oh.ccilogid = cl.id)
					WHERE cl.type = \'DirecPay\' AND cl.authorised = 2
                    ORDER BY cl.datecreated DESC';

            $stmt = $dbObj->prepare($sql);

            if ($stmt)
            {
                $stmt->execute();
                $stmt->bind_result($sess, $trans);
                $i = 0;
                while ($stmt->fetch())
                {
                    $resultArray[$i] = array(
                        'session' => $sess,
                        'transaction' => $trans
                    );
                    $i++;
                }
                $stmt->free_result();
                $stmt->close();
            }
            $dbObj->close();
        }

        return $resultArray;
    }

    static function cURLPost($pURL, $parameterArray)
    {
        //open connection
        $ch = curl_init();

        $parameterString = http_build_query($parameterArray);

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_URL, $pURL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameterString);

        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);

        return $result;
    }
}
?>