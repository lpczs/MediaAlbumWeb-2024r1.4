<?php
class PaygentObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();
        $gateways = Array();

        AuthenticateObj::clearSessionCCICookie();

        $smarty = SmartyObj::newSmarty('CreditCardPayment');

        // Test for PAYGENT supported currencies
        $currencyList = '392';

        if (strpos($currencyList, $gSession['order']['currencyisonumber']) === false)
        {
           $active = false;
        }
        else
        {
        	$active = true;

            $locale = strtolower($gSession['browserlanguagecode']);
			$locale = substr($locale, 0, 2);

            // read in payment methods
			$paygentConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Paygent.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
			$paymentMethodsArray = explode(',', $paygentConfig['PAYMENTMETHODS']);

			foreach ($paymentMethodsArray as $method)
			{
				if ($method == '01')
				{
					if ($locale == 'ja')
					{
						$gateways[$method] = '銀行・郵貯ATM振込';
					}
					else
					{
						$gateways[$method] = $smarty->get_config_vars('str_OrderPaygent_' . $method);
					}
				}

				if ($method == '02')
				{

					if ($locale == 'ja')
					{
						$gateways[$method] = 'クレジットカード';
					}
					else
					{
						$gateways[$method] = $smarty->get_config_vars('str_OrderPaygent_' . $method);
					}
				}

				if ($method == '03')
				{
					if ($locale == 'ja')
					{
						$gateways[$method] = 'コンビニ支払い';
					}
					else
					{
						$gateways[$method] = $smarty->get_config_vars('str_OrderPaygent_' . $method);
					}
				}
			}
        }

		$resultArray['gateways'] = $gateways;
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

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$paygentConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Paygent.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
			$manualReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$automaticReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccAutomaticCallback&ref=' . $gSession['ref'];

			$server = $paygentConfig['SERVER'];
			$hashKey = $paygentConfig['PAYGENTHASH'];
			$merchantID = $paygentConfig['PAYGENTMERCHANTID'];
			$merchantName = mb_substr($paygentConfig['MERCHANTNAME'], 0, 64);
			$paymentDetail = mb_substr($paygentConfig['PAYMENTDETAIL'], 0, 24);
			$paymentDetailKana = mb_substr($paygentConfig['PAYMENTDETAILKANA'], 0, 24);
			$paymentTerm = $paygentConfig['PAYMENTTERM'];
			$bannerUrl = substr($paygentConfig['BANNERURL'], 0, 256);
			$copyrightText = mb_substr($paygentConfig['COPYRIGHT'], 0, 256);

			// selected payment gateway
			$paymentID = $gSession['order']['paymentgatewaycode'];

			$orderID = $gSession['ref'] . '_'. time();

			// amount in smallest unit, e.g. pence or cents
			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
			$fixParams = '';
			$paymentTermMin = '';
			$paymentClass = 0;
			$useCardConfNumber = 0;

			if ($paymentID != "02")
			{
				$paymentClass = "";
				$useCardConfNumber = "";
				$automaticReturnPath = "";
				$manualReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'].'&orderid='.$orderID;

				$telephone = str_replace('-', '', $gSession['order']['billingcustomertelephonenumber']);

				$additionalParams = array(
					'customer_tel'	=> substr($telephone, 0, 11)
				);
			}
			else
			{
				$additionalParams = array(
					'payment_class'	=> $paymentClass,
					'use_card_conf_number'		=> $useCardConfNumber,
					'inform_url'		=> $automaticReturnPath
				);
			}

			// calculate hash key
		 	$org_str = $orderID . $paymentID . $fixParams . $amount . $automaticReturnPath .
          		$merchantID . $paymentTerm . $paymentTermMin . $paymentClass . $useCardConfNumber . $hashKey;

			$hash_str = hash("sha256", $org_str);

			// create random string
			$rand_str = '';
			$rand_char = array('a','b','c','d','e','f','A','B','C','D','E','F','0','1','2','3','4','5','6','7','8','9');
			for ($i=0; ($i<20 && rand(1,10) != 10); $i++)
			{
				$rand_str .= $rand_char[rand(0, count($rand_char)-1)];
			}

			$calculatedHash = $hash_str . $rand_str;

			$standardParameters = array(
				'trading_id' => $orderID,
				'payment_type' => $paymentID,
				'fix_params' => $fixParams,
				'id' => $amount,
				'hc' => $calculatedHash,
				'seq_merchant_id' => $merchantID,
				'merchant_name'	=> $merchantName,
				'payment_detail' => $paymentDetail,
				'payment_detail_kana' => $paymentDetailKana,
				'payment_term_day' => $paymentTerm,
				'payment_term_min' => $paymentTermMin,
				'banner_url' => $bannerUrl,
				'free_memo' => '',
				'return_url' => $manualReturnPath,
				'stop_return_url' => $cancelReturnPath,
				'copy_right' => $copyrightText,
				'customer_family_name' => mb_substr($gSession['order']['billingcontactlastname'], 0 ,12),
				'customer_name' => mb_substr($gSession['order']['billingcontactfirstname'], 0 , 12),
				'customer_family_name_kana' => mb_substr($gSession['order']['billingcontactlastname'], 0, 12),
				'customer_name_kana' => mb_substr($gSession['order']['billingcontactfirstname'], 0 ,12),
				'isbtob' => 0
			);

			$params = array_merge($standardParameters, $additionalParams);

			// define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('parameter', $params);
			$smarty->assign('method', "POST");

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to Paygent
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
        $resultArray['result'] = '';
        $resultArray['ref'] = $gSession['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function confirm($callBack)
    {
    	// include the email creation module
    	require_once('../Utils/UtilsEmail.php');

     	global $gSession;

     	$resultArray = Array();
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $showError = false;
        $update = false;
        $orderExists = false;

	 	$smarty = SmartyObj::newSmarty('Order', '', '');

		$paygentConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Paygent.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
		$hashKey = $paygentConfig['PAYGENTHASH'];
		$tradingID = '';
		$paymentID = '';

		//This is an update from the Paygent Inquiry server executed by Taopix task scheduler
		if ((array_key_exists('inquiry_transaction_id', $_POST) && ($callBack == 'automatic')))
		{
			$ref = $_POST['ref'];

			$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);
			$authorised = true;
			$status = $_POST['inquiry_payment_status'];

			//Payment has been made
			if ($status == '40')
			{
				$authorised = true;
				$authorisedStatus = 1;
			}
			else
			{
				$authorisedStatus = $status;
			}

			// we already have an entry, this is the manual callback
			// we won't have a session
			$webbrandcode = $cciLogEntry['webbrandcode'];
			$currencyCode = $cciLogEntry['currencycode'];
			$amount = $cciLogEntry['formattedamount'];
			$update = true;
			$orderExists = true;
			$parentLogId = $cciLogEntry['id'];
			$orderId = $cciLogEntry['orderid'];
			$paymentID = $cciLogEntry['transactionid'];
			$paymentType = $cciLogEntry['paymentmeans'];
			$ref = $cciLogEntry['sessionid'];
		}

		if (!$orderExists)
		{
			// selected payment gateway
			$selectedPaymentType = $gSession['order']['paymentgatewaycode'];

			// a credit card payment has occured
			if ($selectedPaymentType == '02')
			{
				if ($callBack == 'automatic')
				{
					$sessionRefPos = strpos($_GET['trading_id'], '_');
					$ref = substr($_GET['trading_id'], 0, $sessionRefPos);
				}
				else
				{
					$ref = $_GET['ref'];
				}

				//Check CCILOG to see if this is the Manual Callback
				$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

				if (empty($cciLogEntry))
				{
					// no entry yet, this must be the first callback
					// we do have a session
					$webbrandcode = $gSession['webbrandcode'];
					$currencyCode = $gSession['order']['currencycode'];
					$update = false;
					$parentLogId = -1;
					$orderId = -1;

				}
				else
				{
					// we already have an entry, this is the manual callback
					// we won't have a session
					$webbrandcode = $cciLogEntry['webbrandcode'];
					$currencyCode = $cciLogEntry['currencycode'];
					$amount = $cciLogEntry['formattedamount'];
					$update = true;
					$ManualCallback = true;
					$parentLogId = $cciLogEntry['id'];
					$orderId = $cciLogEntry['orderid'];
					$paymentID = $cciLogEntry['transactionid'];
					$paymentType = $cciLogEntry['paymentmeans'];
					$ref = $cciLogEntry['sessionid'];
				}

				if ($callBack == 'automatic')
				{
					//Calculate Hash KEY
					$paymentID = $_GET['seq_payment_id'];
					$tradingID = $_GET['trading_id'];
					$amount = $_GET['amount'];
					$paymentType = $_GET['payment_type'];

					//only compare with the first 64 bits of the returned hash
					$returnedHash = $_GET['hc'];
					$returnedHash = substr($returnedHash, 0,64);

					$orig_str = $paymentType . $amount . $hashKey . $tradingID . $paymentID;

					$calculatedHash = hash("sha256", $orig_str);

					if ($calculatedHash == $returnedHash)
					{
						echo "result = 0";
						$authorised = true;
						$authorisedStatus = 1;
					}
					else
					{
						echo "result = 0";
						//Email admin to inform them the returned hash does not match
						//And that they should check the payment status within paygent before marking as PAID
						$authorised = true;
						$authorisedStatus = 0;
						$transErrorName  = $paygentConfig['TRANSERRORNAME'];
						$transErrorEmail = $paygentConfig['TRANSERROREMAIL'];

						$emailContent =$smarty->get_config_vars('str_LabelOrderNumber') . ': ' . $tradingID . "\n" . $smarty->get_config_vars('str_LabelTransactionID') .
												': ' . $paymentID . "\n" . $smarty->get_config_vars('str_LabelPaymentMethod') . ': ' . $selectedPaymentType ."\n\n";

						$emailObj = new TaopixMailer();
						$emailObj->sendTemplateEmail('admin_transactionerror', '', '', '', '',
								$transErrorName, $transErrorEmail, '', '',
								0,
								Array('data' => $emailContent));
					}
				}
				else
				{
					if ($ManualCallback)
					{
						$authorised = true;
						$authorisedStatus = 1;
					}
					else
					{
						// The automatic callback did not occur. Therefore the manual callback cannot create the order.
						$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': CALLBACK ERROR';
						$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': SERVER TO SERVER CALLBACK FAILED';
						$resultArray['errorform'] = 'error.tpl';
						$showError = true;
						$authorised = false;
						$authorisedStatus = 0;
					}
				}
			}
			else
			{
				// This is an ATM or Convenience store payment.
				//Nothing is returned from paygent.
				// we do have a session
				$ref = $_GET['ref'];
				$authorised = true;
				$authorisedStatus = 0;
				$webbrandcode = $gSession['webbrandcode'];
				$currencyCode = $gSession['order']['currencycode'];
				$amount = $gSession['order']['ordertotaltopay'];
				$update = false;
				$parentLogId = -1;
				$orderId = -1;
				$paymentID = $_GET['orderid'];

			}
		}

        $serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime =  date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($paygentConfig, $serverTimestamp);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $paymentID;
        $resultArray['formattedtransactionid'] = $paymentID;
        $resultArray['responsecode'] = '';
        $resultArray['responsedescription'] = '';
        $resultArray['authorisationid'] = '';
        $resultArray['formattedauthorisationid'] = '';
        $resultArray['bankresponsecode'] = '';
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = $selectedPaymentType;
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


    static function paymentInquiry()
    {
    	global $ac_config;
    	global $gSession;

        require_once('../Utils/UtilsCoreIncludes.php');
		require_once('../Order/PaymentIntegration/PaymentIntegration.php');
		require_once('../Order/Order_control.php');

        $serverTimestamp = DatabaseObj::getServerTime();

		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
    	$paygentConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Paygent.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

		$paymentRequestServer = $paygentConfig['PAYMENTREQUESTSERVER'];
		$sslCertificate =  UtilsObj::getTaopixWebInstallPath('libs/curl/curl-ca-bundle.crt');
		$sslPassword = $paygentConfig['SSLCERTIFICATEPASSWORD'];
		$PEMCertificate = UtilsObj::getTaopixWebInstallPath('config/'.$paygentConfig['PEMCERTIFICATEFILENAME']);
		$merchantID = $paygentConfig['PAYGENTMERCHANTID'];
		$connectID = $paygentConfig['CONNECTID'];
		$connectPassword = $paygentConfig['CONNECTPASSWORD'];
        $statusRequest = true;

    	$parameters = array(
            'merchant_id'=>$merchantID,
            'connect_id'=>$connectID,
            'connect_password'=>$connectPassword,
            'telegram_kind'=>'091',
            'telegram_version'=>'1.0',
            'trading_id'=>'',
            'payment_id'=>'',
            'payment_notice_id'=>''
        );

    	while ($statusRequest)
    	{
			//POST to paygent to request status updates for each payment.
			$result = self::cURLPost($paymentRequestServer, $parameters, $sslCertificate, $sslPassword, $PEMCertificate);

			if ($result)
			{
				$test = explode('\r\n', $result);

				$itemCount = count($test);

				for ($i = 0; $i < $itemCount; $i++)
				{
					$theItem = $test[$i];

					$itemArray = explode("\r\n", $theItem);
					$testCount = count($itemArray);

					for ($j = 0; $j < $testCount; $j++)
					{
						$myarray2 = explode("=", $itemArray[$j]);

						$StatusArray[$myarray2[0]]=$myarray2[1];
					}

						if ($StatusArray['success_code'] == 1)
						{
							$statusRequest = false;
						}
						else
						{
							$transactionID = $StatusArray['payment_id'];
							$orderID = $StatusArray['trading_id'];
							$inquiryStatus = $StatusArray['payment_status'];

							$_POST['inquiry_transaction_id'] = $transactionID;
							$_POST['inquiry_order_id'] = $orderID;
							$_POST['inquiry_payment_status'] = $inquiryStatus;

							$cciArray = self::getCciLogEntry($transactionID, $orderID);
							$_POST['ref'] = $cciArray['sessionid'];

							$gSession = AuthenticateObj::getCurrentSessionData();

							if ($gSession['ref'] <= 0)
							{
								global $gDefaultSiteBrandingCode;
								AuthenticateObj::setSessionWebBrand($gDefaultSiteBrandingCode);

								$gSession['order']['ccitype'] = 'PAYGENT';
								$gSession['ref'] = $cciArray['sessionid'];

								$browserLocale = UtilsObj::getBrowserLocale();
								if ($browserLocale != '')
								{
									$gSession['browserlanguagecode'] = $browserLocale;
								}
							}

							//Perform status update using Taopix normal workflow;
							Order_control::ccAutomaticCallback();

                            PaymentIntegrationObj::logPaymentGatewayData($paygentConfig, $serverTimestamp, 'STATUS CHANGE: TransactionID = '.$transactionID.' OrderID = '.$orderID.' Status = '.$inquiryStatus);
						}
				}
			}
			else
			{
				$statusRequest = false;
                PaymentIntegrationObj::logPaymentGatewayData($paygentConfig, $serverTimestamp, '***CURL HAS NOT REPLIED ****');
			}

		}
    }

    static function cURLPost($pURL, $pParamArray, $pSSLCertificate, $pSSLPassword, $pPEMCertificate)
    {
    	//url-ify the data for the POST
		$paramterString = http_build_query($pParamArray);

		//open connection
		$ch = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_SSLCERT, $pPEMCertificate);
		curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $pSSLPassword);
		curl_setopt($ch, CURLOPT_CAINFO, $pSSLCertificate);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
		curl_setopt($ch,CURLOPT_URL,$pURL);
		curl_setopt($ch,CURLOPT_POST,TRUE);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$paramterString);

		//execute post
		$result = curl_exec($ch);

    	//close connection
		curl_close($ch);

		return $result;
    }

    static function getCciLogEntry($pTransactionID, $pPaymentID)
	{
		$resultArray = Array();

		$dbObj = DatabaseObj::getConnection();
		if ($dbObj)
		{
			$sql = 'SELECT cl.*, oh.ordernumber as ordernumber
					FROM ccilog cl
					LEFT JOIN orderheader oh ON (oh.id = cl.orderid)
					WHERE cl.transactionid = ? OR cl.transactionid = ? ORDER BY cl.datecreated DESC';

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_param('ss', $pTransactionID, $pPaymentID))
				{
					$stmt->execute();
					DatabaseObj::stmt_bind_assoc($stmt, $row);
					if ($stmt->fetch())
					{
						foreach ($row as $key=>$value)
						{
							$resultArray[$key] = $value;
						}
					}
				}
				$stmt->free_result();
				$stmt->close();
			}
			$dbObj->close();
		}

		return $resultArray;
	}
}

?>