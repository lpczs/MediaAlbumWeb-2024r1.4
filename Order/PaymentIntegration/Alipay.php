<?php

class AlipayObj
{


/**
* Verifies that Alipay can be used for the current order.
*
* For Alipay to work with the current order two conditions have to be met:
* - currency has to be CNY
* - SSL PHP module has been loaded
*
* @since Version 2.5.3
* @author Steffen Haugk
* @return array
* 'active' field is TRUE when both conditions are met, and FALSE otherwise.
*/
    static function configure()
    {
        global $gSession;

        $resultArray = Array();

        AuthenticateObj::clearSessionCCICookie();

		// test for Alipay supported currencies (RMB only)
		// and SSL module loaded
        $resultArray['active'] = (($gSession['order']['currencyisonumber'] == '156') && PaymentIntegrationObj::checkSSL()) ? TRUE : FALSE;
        $resultArray['form'] = '';
        $resultArray['scripturl'] = '';
        $resultArray['script'] = '';
        $resultArray['action'] = '';

        return $resultArray;
    }


/**
* Initialises Alipay payment request.
*
* Loads payment request form and auto-submits it.
*
* @since Version 2.5.3
* @author Steffen Haugk
*/
    static function initialize()
    {
        global $ac_config;
        global $gConstants;
        global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$AlipayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Alipay.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

			$server = $AlipayConfig['SERVER'];
			$partnerId = $AlipayConfig['PARTNERID'];
			$sellerId = $AlipayConfig['SELLERID'];
			$sellerEmail = $AlipayConfig['SELLEREMAIL'];
			$securityCode = $AlipayConfig['SECURITYCODE'];

			$manualReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$automaticReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccAutomaticCallback&ref=' . $gSession['ref'];

			// amount in smallest unit, e.g. pence or cents
			// decimal separator has to be period '.'
			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');

			$description = $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
			$description = preg_replace('/\s+/', ' ', $description);

			// build transaction id
			$transactionId = $gSession['ref'] . '_' . time();

			// do the payment request
			$active = true;
			$error = '';

			$parameter = array(
				'_input_charset'	=> 'utf-8',
				'notify_url'		=> $automaticReturnPath,
				'out_trade_no'		=> $transactionId,
				'partner'			=> $partnerId,
				'payment_type'		=> '1',
				'return_url'		=> $manualReturnPath,
				'seller_email'		=> $sellerEmail,
				'seller_id'			=> $sellerId,
				'service'			=> 'create_direct_pay_by_user',
				'show_url'			=> $gSession['webbranddisplayurl'],
				'subject'			=> $description,
				'total_fee'			=> $amount
			);
			// remove empty entries, sort, and concatenate
			$arg = '';
			$aliparameter = self::paraFilterSort($parameter, $arg);

			$sign = md5($arg . $securityCode);

			// add sign and sign_type to list of parameters
			$aliparameter['sign'] = $sign;
			$aliparameter['sign_type'] = 'MD5';

			// define Smarty variables
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('payment_url', $server);

			$smarty->assign('parameter', $aliparameter);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to Alipay
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

/**
* Returns buyer to payment page.
*
* @since Version 2.5.3
* @author Steffen Haugk
*/
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

    static function confirm()
    {
    	// include the email creation module
    	require_once('../Utils/UtilsEmail.php');

        global $gSession;

        $resultArray = Array();
        $result = '';
        $authorised = false;
        $verification = '';
		$showError = false;
		$error = '';
		$sessionActive = false;
        $dbOrderNumber = 0;
        $dbUserId = 0;

		$buyer_id = self::getRequestParam('buyer_id');
		$notify_id = self::getRequestParam('notify_id');
		$notify_time = self::getRequestParam('notify_time');
		$seller_id = self::getRequestParam('seller_id');
		$trade_no = self::getRequestParam('trade_no'); // Alipay id number
		$trade_status = self::getRequestParam('trade_status');
		$sign = self::getRequestParam('sign');
		$subject = self::getRequestParam('subject');
		$out_trade_no = self::getRequestParam('out_trade_no'); // Taopix id number
		$total_fee = self::getRequestParam('total_fee');
		$use_coupon = self::getRequestParam('use_coupon');
		$exterface = self::getRequestParam('exterface');

		$callback = ($_REQUEST['fsaction'] == 'Order.ccAutomaticCallback') ? 'automatic' : 'manual';

		// search DB for entry
		// if this is an offline callback we need to read the DB to get the currencyCode and webBrandCode so we can use the correct config file
		$dbId = -1;
		$dbOrderId = -1;
		$dbPaymentCertificate = '';

		// do we have a session, or is this an offline callback
		if ($gSession['ref'] > 0)
		{
			$dbSessionId = $gSession['ref'];
			$dbResponseCode = '';
			$dbCurrencyCode = $gSession['order']['currencycode'];
			$dbWebBrandCode = $gSession['webbrandcode'];
			$sessionActive = true;
		}
		else
		{
			// offline callback, we will get these values from CCILOG
			$dbSessionId = -1;
			$dbResponseCode = '';
			$dbCurrencyCode = '';
			$dbWebBrandCode = '';
			$sessionActive = false;
		}

		//   read DB even if not offline callback, since we need to know
		//   if recorded and received transaction status are the same
		//   the order id if present
		//   the ccilog id so we can use it as parent id in subsequent entries
		//   service used (saved in column paymentcertificate)
		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			// get the last log entry with this transaction id
			if ($stmt = $dbObj->prepare('SELECT cl.id, cl.sessionid, cl.userid, cl.orderid, cl.responsecode, cl.currencycode, cl.webbrandcode,
												cl.paymentcertificate, oh.ordernumber
											FROM ccilog cl
											LEFT JOIN orderheader oh ON (oh.id = cl.orderid)
											WHERE transactionid = ? ORDER BY cl.datecreated DESC'))
			{
				if ($stmt->bind_param('s', $out_trade_no))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0)
							{
								if ($stmt->bind_result($dbId, $dbSessionId, $dbUserId, $dbOrderId, $dbResponseCode, $dbCurrencyCode, $dbWebBrandCode,
														$dbPaymentCertificate, $dbOrderNumber))
								{
									if ($stmt->fetch())
									{
										$gSession['ref'] = $dbSessionId;
										$gSession['userid'] = $dbUserId;
									}
									else
									{
										$error = 'confirm fetch ' . $dbObj->error;
									}
								}
								else
								{
									$error = 'confirm bind_result ' . $dbObj->error;
								}
							}
						}
						else
						{
							$error = 'confirm store result ' . $dbObj->error;
						}
					}
					else
					{
						$error = 'confirm execute ' . $dbObj->error;
					}
				}
				else
				{
					$error = 'confirm bind params ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$error = 'confirm prepare ' . $dbObj->error;
			}

			$dbObj->close();
		}

		// only use DB value if service (exterface) is empty
		$exterface = ($exterface == '') ? $dbPaymentCertificate : $exterface;

		// now that we have currency and brand code, read config file
		// needed to verify signature and notification
		$AlipayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Alipay.conf', $dbCurrencyCode, $dbWebBrandCode);
		$server = $AlipayConfig['SERVER'];
		$partnerId = $AlipayConfig['PARTNERID'];
		$securityCode = $AlipayConfig['SECURITYCODE'];

		// check if transaction status is known
		if (!in_array($trade_status, array('WAIT_BUYER_PAY', 'TRADE_FINISHED', 'TRADE_SUCCESS', 'WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS', 'TRADE_CLOSED')))
		{
			$verification = 'status_unknown';
		}

		// check signature
		$testArray = Array();
		$request = $_GET + $_POST;
		foreach ($request as $key => $value)
		{
			// exclude sign and sign_type
			// also exclude all Taopix fields
			if (!in_array($key, array('sign', 'sign_type', 'fsaction', 'ref')))
			{
				if (($callback == 'manual') && ($key != 'notify_id'))
				{
					// fields in manual callback need decoding - apart from notify_id!
					$testArray[$key] = urldecode($value);
				}
				else
				{
					$testArray[$key] = $value;
				}
			}
		}
		$notify_time = $testArray['notify_time'];
		$subject = $testArray['subject'];

		// remove empty entries, sort and concatenate
		$arg = '';
		$aliparameter = self::paraFilterSort($testArray, $arg);

		// append security code
		$testSign = md5($arg . $securityCode);

		if ($testSign == $sign)
		{
			// signatures match
			$signatureMatch = true;
		}
		else
		{
			// signatures do not match
			$signatureMatch = false;
			$verification = 'signature_mismatch';
		}

		// only verify notification if siganture is correct and status is known
		if ($verification == '')
		{
			$verification = strtolower(self::verifyNotification($server, $partnerId, $notify_id, $error));
		}

		// for failing manual callbacks, that are not a new order or a status update
		// (for example when pressing the refresh button)
		// verification can only be done whithin a minute of the notification - verification will fail
		// show page directly
		if (($callback == 'manual') 		// manual callback
			&& ($dbId > 0) 					// existing order
			&& ($signatureMatch == true))	// signature must be valid
		{
			// get html to return to Authorize.Net
			$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname'],$gSession['browserlanguagecode']);

			if ($sessionActive)
			{
				// session is still active, show confirmation as though verification succeeded
				SmartyObj::replaceParams($smarty, 'str_MessageOrderConfirmation1', '<span class="confirmationNumber">' .  $dbOrderNumber . '</span>');
				$smarty->assign('str_MessageOrderConfirmation1', $smarty->get_template_vars('str_MessageOrderConfirmation1'));
                $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('order/sidebarleft_default.tpl', ''));
                $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('order/sidebarleft_confirmation.tpl', ''));

                if ($gSession['ismobile'] == true)
                {
                    $smarty->assign('isajaxcall', false);
                    $html = $smarty->fetchLocale('order/orderconfirmation_small.tpl', $gSession['browserlanguagecode']);
                }
                else
                {
                    $html = $smarty->fetchLocale('order/orderconfirmation_large.tpl', $gSession['browserlanguagecode']);
                }

				// no further action required
				exit($html);
			}
			else
			{
				// session no longer active, show error
				$error = SmartyObj::getParamValue('Login', 'str_ErrorSessionExpired');

				$smarty->assign('data1', SmartyObj::getParamValueLocale('Order', 'str_LabelErrorCode', $gSession['browserlanguagecode']) . ': Invalid reply');
				$smarty->assign('data2', SmartyObj::getParamValueLocale('Order', 'str_LabelErrorMessage', $gSession['browserlanguagecode']) . ': ' . $error);
				$smarty->assign('data3', SmartyObj::getParamValueLocale('Order', 'str_LabelTransactionID', $gSession['browserlanguagecode']) . ': ' . $trade_no);
				$smarty->assign('data4', '');
				$smarty->assign('ref', '-1');

				if ($gSession['ismobile'] == true)
				{
					$smarty->assign('displayInline', false);
					$returnHTML = $smarty->fetchLocale('order/PaymentIntegration/error_small.tpl', $gSession['browserlanguagecode']);
				}
				else
				{
					$returnHTML = $smarty->fetchLocale('order/PaymentIntegration/error_large.tpl', $gSession['browserlanguagecode']);
				}

				// no further action required
				exit($returnHTML);
			}
		}

		// verification should be true, false, invalid
		// 'error_response' - error code in $error
		// '' - connection error
		$response = 'fail';
		$active = false;
		$update = false;

		switch ($verification)
		{
			case 'true':
				$active = true;
				$response = 'success';
				break;
			case 'false':
				$error = 'Verification failed (invalid parameter).';
				break;
			case 'invalid':
				$error = 'Verification failed.';
				break;
			case 'signature_mismatch':
				$error = 'Signature invalid.';
				break;
			case 'status_unknown':
				$error = 'Unsupported transaction status: ' . $trade_status;
				break;
			case 'error_response':
				// error is already in $error
				break;
			default:
				// error is already in $error
		}

		// write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
		PaymentIntegrationObj::logPaymentGatewayData($AlipayConfig, $serverTimestamp, $error);

		// respond to Alipay server if automatic callback
		if ($callback == 'automatic')
		{
			if ($response == 'fail')
			{
				exit ($response);
			}
			{
				echo $response;
			}
		}

		if ($active)
		{
			// only continue if there was no error so far
			switch ($trade_status)
			{
				case 'TRADE_FINISHED':
					$authorised = true;
					$authorisedStatus = 1;
					break;
				case 'WAIT_BUYER_PAY':
					$authorised = true;
					$authorisedStatus = 2;
					break;
				case 'WAIT_SELLER_SEND_GOODS':
					$authorised = true;
					$authorisedStatus = 3;
					break;
				case 'WAIT_BUYER_CONFIRM_GOODS':
					$authorised = true;
					$authorisedStatus = 4;
					break;
				case 'TRADE_SUCCESS':
					$authorised = true;
					$authorisedStatus = 1;
					break;
				case 'TRADE_CLOSED':
					$authorised = false;
					$authorisedStatus = 6;
					break;
			}

			// is this an update
			if (($trade_status != $dbResponseCode) && ($dbResponseCode != ''))
			{
				// update
				$update = true;

				// send an email containing the offline transaction results
				$offlineConfirmationName = $AlipayConfig['OFFLINECONFIRMATIONNAME'];
				$offlineConfirmationEmailAddress = $AlipayConfig['OFFLINECONFIRMATIONEMAILADDRESS'];
				$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname'],$gSession['browserlanguagecode']);
				$emailContent = $smarty->get_config_vars('str_LabelOrderNumber') . ': ' . $dbOrderNumber . "\n" .
								$smarty->get_config_vars('str_LabelTransactionID') . ': ' . $trade_no . "\n" .
								$smarty->get_config_vars('str_LabelStatus') . ': ' . $trade_status . "\n\n";
				if (($offlineConfirmationEmailAddress != '') && ($emailContent != ''))
				{
					$emailObj = new TaopixMailer();

					$emailObj->sendTemplateEmail('admin_offlinepaymentupdate', $dbWebBrandCode, '', '', '',
						$offlineConfirmationName, $offlineConfirmationEmailAddress, '', '',
						0,
						Array('data' => $emailContent));
				}
			}
			else
			{
				// has this transaction already been recorded?
				if ($dbId > 0)
				{
					// not a new transaction, i.e. not a new order
					// since it's not an update either, we don't need to do anything
					if ($callback == 'automatic')
					{
						// no update, data already in DB, no action required
						exit();
					}

				}
			}
		}
		else
		{
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': Invalid reply';
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $error;
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $trade_no;
			$resultArray['data4'] = '';
			$resultArray['errorform'] = 'error.tpl';

    		$showError = true;
    		$authorisedStatus = 0;
		}

        $timestampParts = explode(" ", $notify_time);
        $serverDate = $timestampParts[0];
        if (count($timestampParts) > 1)
        {
	        $serverTime = $timestampParts[1];
        }
        else
        {
	        $serverTime = '';
        }

        $resultArray['result'] = $result;
        $resultArray['ref'] = $dbSessionId;
        $resultArray['amount'] = $total_fee;
        $resultArray['formattedamount'] = $total_fee;
        $resultArray['charges'] = '000';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $out_trade_no; // this is our unique ID, not the real order ID
        $resultArray['formattedtransactionid'] = $out_trade_no;
        $resultArray['responsecode'] = $trade_status;
        $resultArray['responsedescription'] = $subject;
        $resultArray['authorisationid'] = $trade_no;  // this is Alipay ID
        $resultArray['formattedauthorisationid'] = $trade_no;
        $resultArray['bankresponsecode'] = '';
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = $exterface;
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymenttime'] = $serverTime;
        $resultArray['paymentmeans'] = '';
        $resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $notify_time;
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = $buyer_id;
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = '';
        $resultArray['business'] = $seller_id;
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = '';
        $resultArray['settleamount'] = '';
        $resultArray['currencycode'] = $dbCurrencyCode;
        $resultArray['webbrandcode'] = $dbWebBrandCode;

        $resultArray['charityflag'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = $dbOrderId;
        $resultArray['parentlogid'] = $dbId;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = $showError;
		$resultArray['nextstage'] = '';
		$resultArray['stage'] = '';


        return $resultArray;
    }

	static function verifyNotification($pUrl, $pPartnerId, $pNotifyId, &$pErrorString)
	{
		$reply = '';
		$requestData = 'service=notify_verify&partner=' . $pPartnerId . '&notify_id=' . $pNotifyId;

		$parsedUrl = parse_url($pUrl);

		if (empty($parsedUrl['port'])) {
			$parsedUrl['port'] = strtolower($parsedUrl['scheme']) == 'https' ? 443 : 80;
		}

		// generate request
		$header  = 'POST ' . $parsedUrl['path'] ." HTTP/1.1\r\n";
		$header .= 'Host: ' . $parsedUrl['host'] . "\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= 'Content-Length: ' . strlen($requestData) . "\r\n";
		$header .= "Connection: close\r\n";
		$header .= "\r\n";
		$request = $header . $requestData;

		$replyData    = '';
		$errno  = 0;
		$errstr = '';

		// open socket to filehandle and retry up to 3 times with a 20 second timeout
		$retryCount = 3;

		while ($retryCount > 0)
		{
			// increase the standard php timeout
			UtilsObj::resetPHPScriptTimeout(60);

			$fp = fsockopen(($parsedUrl['scheme'] == 'https' ? 'ssl://' : '') . $parsedUrl['host'], $parsedUrl['port'], $errno, $errstr, 30);
			if ($fp)
			{
				$retryCount = 0;
			}
			else
			{
				$retryCount--;
			}
		}

		if ($fp)
		{
			// read the response from Alipay
			// while content exists, keep retrieving document in 1K chunks
			fwrite($fp, $request);
			fflush($fp);

			while (!feof($fp)) {
				$replyData .= fread($fp, 1024);
			}

			fclose($fp);

			if (!$errno)
			{

				$headerSize  = strpos($replyData, "\r\n\r\n");
				$headerData  = substr($replyData, 0, $headerSize);
				$header       = explode("\r\n", $headerData);
				$statusLine  = explode(" ", $header[0]);
				$contentType = "application/octet-stream";

				foreach ($header as $headerLine) {
					$headerParts = explode(":", $headerLine);

					if (strtolower($headerParts[0]) == "content-type") {
						$contentType = trim($headerParts[1]);
						break;
					}
				}

				$replyInfo = array(
					'httpCode'    => (int) $statusLine[1],
					'contentType' => $contentType,
					'headerSize'  => $headerSize + 4);

				if ($replyInfo['httpCode'] != 200) {
					$pErrorString = 'HTTP code is ' . $replyInfo['httpCode'] . ', expected 200';
					return '';
				}

				// split header and body
				$replyHeader = substr($replyData, 0, $replyInfo['headerSize'] - 4);
				$reply       = substr($replyData, $replyInfo['headerSize']);

			}
			else
			{
				$pErrorString = $errstr;
				return '';
			}
		}
		else
		{
			if ($errno)
			{
				$pErrorString = $errstr . '(' . $errno . ')';
				return '';
			}
		}

		// errors start with ILLEGAL_ or HASH_
		$pos = strpos($reply, 'ILLEGAL_');
		if ($pos === false)
		{
			$pos = strpos($reply, 'HASH_');
		}
		if ($pos !== false)
		{
			// get error code
			$error_code = '';
			$end = $pos;

			while (preg_match("/[A-Z_]/", substr($reply, $end, 1)))
			{
				$error_code .= substr($reply, $end, 1);
				$end++;
			}

			$pErrorString = $error_code;
			$reply = 'error_response';
		}

		return $reply; // Xml as plain text
	}

    static function getRequestParam($pParam, $pDefaultValue = '')
    {
        // return the REQUEST parameter's value or the default value if it isn't present
        return (array_key_exists($pParam, $_REQUEST)) ? $_REQUEST[$pParam] : $pDefaultValue;
    }


	/**
	* Sorts an array by array key and removes empty elements.
	*
	* Alipay expects the form fields to come in sorted order.
	* Also concatenates the array elements to one string.
	*
	* @since Version 2.5.3
	* @author Steffen Haugk
	*/
    static function paraFilterSort($pParameters, &$pSignatureString)
    {
		$result = Array();
		$signatureArray = Array();

		ksort($pParameters);

		foreach ($pParameters as $key => $value)
		{
			if ($value != '')
			{
				$result[$key] = $value;
				$signatureArray[] = $key . '=' . $value;
			}
		}

		$pSignatureString = implode("&", $signatureArray);

		return $result;
    }

}

?>