<?php

class DurangoObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();
		$currency = $gSession['order']['currencycode'];

        AuthenticateObj::clearSessionCCICookie();

		// test for Authorize.Net supported currencies and SSL
        $resultArray['active'] = (($currency == 'USD') && PaymentIntegrationObj::checkSSL()) ? true : false;

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
			$DurangoConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Durango.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
			$server = $DurangoConfig['SERVER'];
			$apiLogin = $DurangoConfig['APILOGIN'];
			$transactionKey = $DurangoConfig['TRANSACTIONKEY'];
			$testMode = $DurangoConfig['TESTMODE'];
			$testMode = ($testMode == 0 ) ? 'FALSE' : 'TRUE';

			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$relayResponsePath = UtilsObj::correctPath($gSession['webbrandwebroot']) . 'PaymentIntegration/Durango/DurangoRelayResponse.php?ref=' . $gSession['ref'];
			$relayResponsePath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');
			$description = $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
	        $description = htmlentities($description, ENT_QUOTES);

			// build transaction id, maximum of 20 characters
			$invoiceNum = $gSession['ref'] . '_'. time();

			// customer name
			$custId = $gSession['userlogin'];

			$sequence = $gSession['ref'];
			$timeStamp	= time ();

			$fingerprint = hash_hmac("md5", $apiLogin . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^", $transactionKey);

			// define Smarty variables
			AuthenticateObj::defineSessionCCICookie();

			// fixed fields
			$parameters['x_version'] = '3.1';
			$parameters['x_show_form'] = 'PAYMENT_FORM';
			$parameters['x_type'] = 'AUTH_CAPTURE';
			$parameters['x_method'] = 'CC';
			$parameters['x_relay_response'] = 'TRUE';

			$parameters['x_relay_url'] = $relayResponsePath;
			$parameters['x_login'] = $apiLogin;
			$parameters['x_cust_id'] = $custId;

			$parameters['x_amount'] = $amount;
			$parameters['x_description'] = $description;
			$parameters['x_invoice_num'] = $invoiceNum;
			$parameters['x_fp_sequence'] = $sequence;
			$parameters['x_fp_timestamp'] = $timeStamp;
			$parameters['x_fp_hash'] = $fingerprint;
			$parameters['x_test_request'] = $testMode;
			$parameters['x_customer_ip'] = $gSession['order']['useripaddress'];

			// billing
			$parameters['x_first_name'] = $gSession['order']['billingcontactfirstname'];
			$parameters['x_last_name'] = $gSession['order']['billingcontactlastname'];
			$parameters['x_company'] = $gSession['order']['billingcustomername'];
			$parameters['x_address'] = $gSession['order']['billingcustomeraddress1'];
			$parameters['x_city'] = $gSession['order']['billingcustomercity'];
			$parameters['x_state'] = $gSession['order']['billingcustomerregioncode'];
			$parameters['x_zip'] = $gSession['order']['billingcustomerpostcode'];
			$parameters['x_country'] = $gSession['order']['billingcustomercountrycode'];
			$parameters['x_phone'] = $gSession['order']['billingcustomertelephonenumber'];
			$parameters['x_email'] = $gSession['order']['billingcustomeremailaddress'];

			// shipping
			$parameters['x_ship_to_first_name'] = $gSession['shipping'][0]['shippingcontactfirstname'];
			$parameters['x_ship_to_last_name'] = $gSession['shipping'][0]['shippingcontactlastname'];
			$parameters['x_ship_to_company'] = $gSession['shipping'][0]['shippingcustomername'];
			$parameters['x_ship_to_address'] = $gSession['shipping'][0]['shippingcustomeraddress1'];
			$parameters['x_ship_to_city'] = $gSession['shipping'][0]['shippingcustomercity'];
			$parameters['x_ship_to_state'] = $gSession['shipping'][0]['shippingcustomerstate'];
			$parameters['x_ship_to_zip'] = $gSession['shipping'][0]['shippingcustomerpostcode'];
			$parameters['x_ship_to_country'] = $gSession['shipping'][0]['shippingcustomercountryname'];

			// session ref and webbrand code
			$parameters['ref'] = $gSession['ref'];
			$parameters['wb'] = $gSession['webbrandcode'];

			// define Smarty variables
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('payment_url', $server);
			$smarty->assign('method', 'POST');
			$smarty->assign('parameter', $parameters);

			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to Authorize.Net
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
        global $ac_config;
        global $gSession;

        $resultArray = Array();
        $result = '';

        $ref = $_GET['ref'];

		$DurangoConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Durango.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

		$vendorName = $DurangoConfig['VENDORNAME'];
		$apiLogin = $DurangoConfig['APILOGIN'];
		$transactionKey = $DurangoConfig['TRANSACTIONKEY'];

		$queryUrl = $DurangoConfig['QUERY_URL'];
		$username = $DurangoConfig['USERNAME'];
		$password = $DurangoConfig['PASSWORD'];

		// request status
		$active = true;
        $authorised = false;
		$error = '';
		$status = '';
		$responseText = '';

		// read result
		$responseCode = UtilsObj::getPOSTParam('x_response_code');
		$callbackSuccess = ($responseCode == '1') ? '1' : '0';
		$responseSubcode = UtilsObj::getPOSTParam('x_response_subcode');
		$responseReasonCode = UtilsObj::getPOSTParam('x_response_reason_code');
		$responseReasonText = UtilsObj::getPOSTParam('x_response_reason_text');
		$authCode = UtilsObj::getPOSTParam('x_auth_code');
		$avsCode = UtilsObj::getPOSTParam('x_avs_code');
		$transId = UtilsObj::getPOSTParam('x_trans_id');
		$invoiceNum = UtilsObj::getPOSTParam('x_invoice_num');
		$amount = UtilsObj::getPOSTParam('x_amount');
		$custId = UtilsObj::getPOSTParam('x_cust_id');
		$cvv2RespCode = UtilsObj::getPOSTParam('x_cvv2_resp_code');
		$paymentTimestamp = UtilsObj::getPOSTParam('x_fp_timestamp');
		$paymentDate = date("Y-m-d", $paymentTimestamp);
		$paymentTime = date("H:i:s", $paymentTimestamp);
		$paymentMethod = UtilsObj::getPOSTParam('x_method');

		$request = "username=$username&password=$password&transaction_id=$transId";

		if ($reply = self::queryPost($queryUrl, $request, $error))
		{
			// read XML
			$xml = simplexml_load_string($reply);

			if ($xml)
			{
				$actionDate = 0;
				$xmlSuccess = '';
				$ccBin = '';

				foreach($xml->children() as $transaction)
				{
					if ($transaction->getName() == 'transaction')
					{
						$ccBin = (string) $transaction->cc_bin;
						foreach($transaction->children() as $action)
						{
							if ($action->getName() == 'action')
							{
								if (($action->date) > $actionDate)
								{
									$actionDate = (string) $action->date;
									$xmlSuccess = (string) $action->success;
								}
							}
						}
					}
				}

				// does query confirm callback?
				if ($callbackSuccess != $xmlSuccess)
				{
					$error = 'Callback result could not be confirmed.';
					$active = false;
				}

			}
			else
			{
				$error = 'Non-XML reply.';
				$active = false;
			}

		}
		else
		{ // error occured, show error page
			$active = false;
		}


		switch ($responseCode)
		{
			case '1': // approved
				$authorised = true;
				$authorisedStatus = 1;
				break;
			case '2': // declined
				$authorisedStatus = 2;
				$error = 'This transaction has been declined.';
				break;
			case '3': // error
				$authorisedStatus = 3;
				$error = 'There has been an error processing this transaction.';
				break;
			case '4': // held for review
				$authorisedStatus = 4;
				$error = 'This transaction is being held for review.';
				break;
		}

		// get html to return to Authorize.Net
		$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname'],$gSession['browserlanguagecode']);

		if ($authorised)
		{
			SmartyObj::replaceParams($smarty, 'str_MessageOrderConfirmation1', '<span class="confirmationNumber"><order_number/></span>');
			$smarty->assign('str_MessageOrderConfirmation1', $smarty->get_template_vars('str_MessageOrderConfirmation1'));
            $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('order/sidebarleft_default.tpl', ''));
            $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('order/sidebarleft_confirmation.tpl', ''));
			$smarty->assign('mainwebsiteurl', $gSession['webbrandwebroot']);

			if ($gSession['ismobile'] == true)
            {
                $smarty->assign('isajaxcall', false);
                $returnHTML = $smarty->fetchLocale('order/externalorderconfirmation_small.tpl', $gSession['browserlanguagecode']);
				$cssFile = $smarty->getLocaleCss('csscustomerexternal_small');
            }
            else
            {
                $returnHTML = $smarty->fetchLocale('order/externalorderconfirmation_large.tpl', $gSession['browserlanguagecode']);
				$cssFile = $smarty->getLocaleCss('csscustomerexternal_large');
            }

			$showError = false;
		}
		else
		{
			$smarty->assign('data1', SmartyObj::getParamValueLocale('Order', 'str_LabelErrorCode', $gSession['browserlanguagecode']) . ': '. $error);
			$smarty->assign('data2', SmartyObj::getParamValueLocale('Order', 'str_LabelErrorMessage', $gSession['browserlanguagecode']) . ': ' . $responseReasonText);
			$smarty->assign('data3', SmartyObj::getParamValueLocale('Order', 'str_LabelTransactionID', $gSession['browserlanguagecode']) . ': ' . $invoiceNum);
			$smarty->assign('data4', '');
			$smarty->assign('homeurl', $gSession['webbrandwebroot']);

			if ($gSession['ismobile'] == true)
			{
				$returnHTML = $smarty->fetchLocale('order/PaymentIntegration/externalerror_small.tpl', $gSession['browserlanguagecode']);
				$cssFile = $smarty->getLocaleCss('csscustomerexternal_small');
			}
			else
			{
				$returnHTML = $smarty->fetchLocale('order/PaymentIntegration/externalerror_large.tpl', $gSession['browserlanguagecode']);
				$cssFile = $smarty->getLocaleCss('csscustomerexternal_large');
			}

    		$showError = true;
    		$authorisedStatus = 0;
		}

		// Add inline styles and replace relative paths (images, css, etc) with absolute paths
		$formattedReturnHTML = str_replace("../", UtilsObj::correctPath($gSession['webbrandweburl']), self::formatReturnHTML($returnHTML, $cssFile));

		// get rid of image tags and convert to unicode code points where necessary
		$gSession['order']['confirmationhtml'] = UtilsObj::cleanHTML($formattedReturnHTML, false, true);

        $currencyCode = $gSession['order']['currencycode'];
        $payerId = $gSession['order']['useripaddress'];

		// write to log file.
		$serverTime = DatabaseObj::getServerTime();
		PaymentIntegrationObj::logPaymentGatewayData($DurangoConfig, $serverTime, $error);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '000';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $transId;
        $resultArray['formattedtransactionid'] = $transId;
        $resultArray['responsecode'] = $responseReasonCode;
        $resultArray['responsedescription'] = $responseReasonText;
        $resultArray['authorisationid'] = $invoiceNum;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $invoiceNum;
        $resultArray['bankresponsecode'] = $responseSubcode;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = $cvv2RespCode;
        $resultArray['paymentcertificate'] = $authCode;
        $resultArray['paymentmeans'] = PaymentIntegrationObj::getCardType($ccBin); // guess tyoe of card
        $resultArray['paymentdate'] = $paymentDate;
        $resultArray['paymenttime'] = $paymentTime;
        $resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $serverTime;
        $resultArray['addressstatus'] = $avsCode;
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = $payerId;
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = '';
        $resultArray['business'] = $vendorName;
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

	static function queryPost($pUrl, $pRequestData, &$pErrorString)
	{
		$parsedUrl = parse_url($pUrl);

		if (empty($parsedUrl['port'])) {
			$parsedUrl['port'] = strtolower($parsedUrl['scheme']) == 'https' ? 443 : 80;
		}

		// generate request
		$header  = 'POST ' . $parsedUrl['path'] ." HTTP/1.1\r\n";
		$header .= 'Host: ' . $parsedUrl['host'] . "\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= 'Content-Length: ' . strlen($pRequestData) . "\r\n";
		$header .= "Connection: close\r\n";
		$header .= "\r\n";
		$request = $header . $pRequestData;

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
			// read the response from MultiSafepay
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
				$header      = explode("\r\n", $headerData);
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

				if ($replyInfo['httpCode'] != 200)
				{
					$pErrorString = 'HTTP code is ' . $replyInfo['httpCode'] . ', expected 200';
					return false;
				}

				// split header and body
				$replyHeader = substr($replyData, 0, $replyInfo['headerSize'] - 4);
				$replyXml    = substr($replyData, $replyInfo['headerSize']);

			}
			else
			{
				$pErrorString = $errstr;
				return false;
			}
		}
		else
		{
			if ($errno)
			{
				$pErrorString = $errstr . '(' . $errno . ')';
				return false;
			}
		}

		return $replyXml; // Xml as plain text
	}

	static function formatReturnHTML($html, $cssFile)
	{
		require_once('../libs/external/EmailContent/css_to_inline_styles.php');

		// Load the css file
		$cssContent = UtilsObj::readTextFile($cssFile);
		$cssContent = str_replace("\r\n", "\n", $cssContent);
		$cssContent = str_replace("\r", "\n", $cssContent);

		// Convert css to inline styles
		$cssToInlineStyles = new CSSToInlineStyles($html, $cssContent);
		$cssToInlineStyles->setUseInlineStylesBlock(false);
		$cssToInlineStyles->setCleanup(false);
		$htmlWithInlineStyles = $cssToInlineStyles->convert(false, false);

		// Restore the <order_number/> tag (convert() causes this to be formatted)
		$htmlWithInlineStyles = str_replace("<order_number></order_number>", '<order_number/>', $htmlWithInlineStyles);

		return $htmlWithInlineStyles;
	}
}

?>