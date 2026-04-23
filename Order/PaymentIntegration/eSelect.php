<?php

class eSelectObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = array();
		$currency = $gSession['order']['currencyisonumber'];
		$active = true;

        AuthenticateObj::clearSessionCCICookie();

		// test for eSelect supported currencies, which consist of the Canadian Dollar only
        if ($currency != '124')
        {
            $active = false;
        }

        $resultArray['active'] = ($active && PaymentIntegrationObj::checkSSL()) ? true : false;
        $resultArray['gateways'] = array();
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

    	// First check if we have any ccidata. This is set when the call is made the first time.
        // If the data is set then the user must have hit the back button on their browser.
        if ($gSession['order']['ccidata'] == '')
        {
			$eSelectConfig = PaymentIntegrationObj::readCCIConfigFile('../config/eSelect.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

			// store details
			$server = $eSelectConfig['ESPSERVER'];
			$storeId = $eSelectConfig['ESPSTOREID'];
			$hppKey = $eSelectConfig['ESPHPPKEY'];

			// item details
			$chargeTotal = number_format($gSession['order']['ordertotaltopay'], 2, '.', ''); // must include 2 decimal places and dot
			$productCode = self::validMonerisField($gSession['items'][0]['itemproductcode'], 10);

			$description = LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
			$description = self::validMonerisField($gSession['items'][0]['itemqty'] . ' x ' . $description, 20);

			$quantity = '1';
			$price = $chargeTotal;
			$subTotal = $chargeTotal;

			// transaction details
			$custId	= self::validMonerisField($gSession['userlogin'], 50);
			$orderID = self::validMonerisField($gSession['ref'] . '_' . time(), 50);
			$lang = self::validMonerisField(strtolower($gSession['browserlanguagecode']), 2);
			$lang = ($lang == 'fr') ? 'fr-ca' : 'en-ca'; // only these two available
			$email = self::validMonerisField($gSession['order']['billingcustomeremailaddress'], 50);

			// billing details
			$billFirstName = self::validMonerisField($gSession['order']['billingcontactfirstname'], 30);
			$billLastName = self::validMonerisField($gSession['order']['billingcontactlastname'], 30);
			$billCompanyName = self::validMonerisField($gSession['order']['billingcustomername'], 30);
			$billAddressOne = self::validMonerisField($gSession['order']['billingcustomeraddress1'], 30);
			$billCity = self::validMonerisField($gSession['order']['billingcustomercity'], 30);
			$billStateProvince = self::validMonerisField($gSession['order']['billingcustomerstate'], 30);
			if ($billStateProvince == '') // would be better to get the right field from the DB
			{								// but in this version there is no region
				$billStateProvince = self::validMonerisField($gSession['order']['billingcustomercounty'], 30);
			}
			$billPostalCode = self::validMonerisField($gSession['order']['billingcustomerpostcode'], 30);
			$billCountry = self::validMonerisField($gSession['order']['billingcustomercountryname'], 30);
			$billPhone = self::validMonerisField($gSession['order']['billingcustomertelephonenumber'], 30);

			// shipping details
			$shipFirstName = self::validMonerisField($gSession['shipping'][0]['shippingcontactfirstname'], 30);
			$shipLastName = self::validMonerisField($gSession['shipping'][0]['shippingcontactlastname'], 30);
			$shipCompanyName = self::validMonerisField($gSession['shipping'][0]['shippingcustomername'], 30);
			$shipAddressOne = self::validMonerisField($gSession['shipping'][0]['shippingcustomeraddress1'], 30);
			$shipCity = self::validMonerisField($gSession['shipping'][0]['shippingcustomercity'], 30);
			$shipStateProvince = self::validMonerisField($gSession['shipping'][0]['shippingcustomerstate'], 30);
			if ($shipStateProvince == '') // would be better to get the right field from the DB
			{							  // but in this version there is no region
				$shipStateProvince = self::validMonerisField($gSession['shipping'][0]['shippingcustomercounty'], 30);
			}
			$shipPostalCode = self::validMonerisField($gSession['shipping'][0]['shippingcustomerpostcode'], 30);
			$shipCountry = self::validMonerisField($gSession['shipping'][0]['shippingcustomercountryname'], 30);
			$shipPhone = self::validMonerisField($gSession['shipping'][0]['shippingcustomertelephonenumber'], 30);

			// rvars
			$rvarRef = $gSession['ref'];

			$parameters = array(
				'ps_store_id' => $storeId,
				'hpp_key' => $hppKey,

				// item details
				'charge_total' => $chargeTotal,
				'id1' => $productCode,
				'description1' => $description,
				'quantity1' => $quantity,
				'price1' => $price,
				'subtotal1' => $subTotal,

				// transaction details
				'cust_id' => $custId,
				'order_id' => $orderID,
				'lang' => $lang,
				'email' => $email,

				// billing details
				'bill_first_name' => $billFirstName,
				'bill_last_name' => $billLastName,
				'bill_company_name' => $billCompanyName,
				'bill_address_one' => $billAddressOne,
				'bill_city' => $billCity,
				'bill_state_or_province' => $billStateProvince,
				'bill_postal_code' => $billPostalCode,
				'bill_country' => $billCountry,
				'bill_phone' => $billPhone,

				// shipping details
				'ship_first_name' => $shipFirstName,
				'ship_last_name' => $shipLastName,
				'ship_company_name' => $shipCompanyName,
				'ship_address_one' => $shipAddressOne,
				'ship_city' => $shipCity,
				'ship_state_or_province' => $shipStateProvince,
				'ship_postal_code' => $shipPostalCode,
				'ship_country' => $shipCountry,
				'ship_phone' => $shipPhone,

				// rvars
				'rvar_ref' => $rvarRef
			);

			// define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('parameter', $parameters);
			$smarty->assign('method', 'POST');

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to eSelect
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
        $resultArray = array();
        $resultArray['result'] = '';
        $resultArray['ref'] = $_GET['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function confirm()
    {
        global $gSession;

        $resultArray = Array();
        $verification = Array();
        $result = '';
        $xresult = '';
        $error = '';
        $authorised = true;
        $authorisedStatus = 1;
		$txn_num = '';
		$response_order_id = '';
		$charge_total = '';
		$bank_transaction_id = '';
		$response_code = '';
		$message = '';
		$iso_code = '';
		$card_num = '';
		$formattedCardNumber = '';
		$bank_approval_code = '';
		$status = '';
		$date_stamp = '';
		$card = '';
		$time_stamp = '';
		$trans_name = '';
		$avsResponseCode = '';
		$cvdResponseCode = '';
		$giftcard = $array = array('', ''); // maximum two gift cards
		$gcounter = 0;
		$showError = false;

		$xmlReply = base64_decode(UtilsObj::getPOSTParam('xml_response'));
		$xmlReply = str_replace("\r\n",'',$xmlReply);

		// read XML
		$xml = simplexml_load_string($xmlReply);

		if ($xml)
		{
			// initialise variables
			$response_order_id = (string) $xml->response_order_id;
			$bank_transaction_id = (string) $xml->bank_transaction_id;
			$response_code = (string) $xml->response_code;
			$iso_code = (string) $xml->iso_code;
			$bank_approval_code = (string) $xml->bank_approval_code;
			$time_stamp = (string) $xml->time_stamp;
			$date_stamp = (string) $xml->date_stamp;
			$trans_name = (string) $xml->trans_name;
			$message = (string) $xml->message;
			$charge_total = (string) $xml->charge_total;
			$card_num = (string) $xml->card_num;
			$card = (string) $xml->card;
			$xresult = (string) $xml->result; // to avoid overwriting result
			$txn_num = (string) $xml->txn_num;
			$transactionKey = (string) $xml->transactionKey;
 			$avsResponseCode = (string) $xml->avs_response_code;
 			$cvdResponseCode = (string) $xml->cvd_response_code;

			$formattedCardNumber = substr($card_num,0,4) . '-****-****-' . substr($card_num,-4);

			// get giftcard information when present, only save selected fields, rest can be found in log file
			foreach($xml->children() as $child)
			{
				if ($child->getName() == 'gift_card')
				{
					$giftcard[$gcounter] = $child->order_no;
					$giftcard[$gcounter] .= '::' . $child->txn_num;
					$giftcard[$gcounter] .= '::' . $child->card_num;
					$giftcard[$gcounter] .= '::' . $child->card_desc;
					$gcounter++;
				}
			}
		}
		else
		{
			$error = 'eSelect::Non-XML reply.';
			$authorised = false;
			$authorisedStatus = 0;
		}

        $eSelectConfig = PaymentIntegrationObj::readCCIConfigFile('../config/eSelect.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
		$ESPserver = $eSelectConfig['ESPSERVER'];
		$server = $eSelectConfig['ESPVERIFICATIONURL'];
		$referrer = $eSelectConfig['ESPREFERRER'];
		$vendorName = $eSelectConfig['ESPVENDORNAME'];
		$storeId = $eSelectConfig['ESPSTOREID'];
		$hppKey = $eSelectConfig['ESPHPPKEY'];

		$verification['replyData'] = '';
		$verification['replyXml'] = '';

        if ($error == '')
        {
			// verify response.  only if URL set
			if ($server != '')
			{
				$verification = self::verifyTransaction($server, $storeId, $hppKey, $transactionKey, $referrer);
			}
			else
			{
				// do as if we did the verification
				$verification = array();
				$verification['order_id'] = $response_order_id;
				$verification['response_code'] = $response_code;
				$verification['txn_num'] = $txn_num;
				$verification['error'] = '';
				$verification['status'] = ($xresult == '1') ? 'Valid-Approved' : 'Valid-Declined';
				$verification['replyData'] = 'Simulation';
				$verification['replyXml'] = '<?xml version="1.0" standalone="yes"?' . '><simulation><verification>NO</verification></simulation>';
			}

			if ($verification['error'] == '')
			{
				// do some comparisons

				// status
				if (strtolower(trim($verification['status'])) != 'valid-approved')
				{
					$authorised = false;
					$error = 'Verification failed (' . $verification['status'] . ').';
				}

				// order Id
				if (trim($verification['order_id']) != $response_order_id)
				{
					$authorised = false;
					$error = 'Verification: Order ID mismatch. Status: ' . $verification['status'];
				}

				// Response Code
				if (ltrim(trim($verification['response_code']),'0') != ltrim($response_code,'0'))
				{
					$authorised = false;
					$error = 'Verification: Response Code mismatch. Status: ' . $verification['status'];
				}
			}
			else
			{
					$authorised = false;
					$error = 'Verification: ' . $verification['error'];
			}
		}

		if ($xresult == '0')
		{
			$error = $message;
			$authorised = false;
			$authorisedStatus = 0;
		}

        if ($error == '')
        {
			$status = $verification['status'];
        }
		else
		{
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': Payment Error';
    		$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $error;
   			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $bank_transaction_id;
   			$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $response_order_id;
    		$resultArray['errorform'] = 'error.tpl';
    		$showError = true;
    		$authorisedStatus = 0;
		}

		$serverTime = DatabaseObj::getServerTime();

        $ipAddress = $gSession['order']['useripaddress'];
        $email = $gSession['order']['billingcustomeremailaddress'];

        $logData['callbacktype'] = 'manual';
        $logData['DateTime'] = $serverTime;
        if ($xml)
        {
            $logData['responsexml'] = self::xmlPrettyPrint($xmlReply);
        }
        else
        {
            $logData['responsexml'] = $xmlReply;
        }

        if ($verification['replyXml'] != '')
        {
            // pretty print xml
            $logData['verificationxml'] = self::xmlPrettyPrint($verification['replyXml']);
        }
        else
        {
            $logData['verificationxml'] = $verification['replyData'];
        }

        // write logs
        PaymentIntegrationObj::logPaymentGatewayData($eSelectConfig, $serverTime, $error);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $gSession['ref'];
        $resultArray['amount'] = $charge_total;
        $resultArray['formattedamount'] = $charge_total;
        $resultArray['charges'] = '0.00';
        $resultArray['formattedcharges'] = '0.00';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $response_order_id;
        $resultArray['formattedtransactionid'] = $bank_transaction_id;
        $resultArray['responsecode'] = $response_code;
        $resultArray['responsedescription'] = $message;
        $resultArray['authorisationid'] = $response_order_id;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = UtilsObj::LeftChars($giftcard[0], 100); // information relating to giftcard 1
        $resultArray['bankresponsecode'] = $iso_code;
        $resultArray['cardnumber'] = $card_num;
        $resultArray['formattedcardnumber'] = $formattedCardNumber;
        $resultArray['cvvflag'] = $cvdResponseCode;
        $resultArray['cvvresponsecode'] = $bank_approval_code;
        $resultArray['paymentcertificate'] = $status;
        $resultArray['paymentdate'] = $date_stamp;
        $resultArray['paymentmeans'] = $card;
        $resultArray['paymenttime'] = $time_stamp;
		$resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = '';
        $resultArray['addressstatus'] = $avsResponseCode;
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = $ipAddress;
        $resultArray['payerstatus'] = $ESPserver;
        $resultArray['payeremail'] = $email;
        $resultArray['business'] = $vendorName;
        $resultArray['receiveremail'] = UtilsObj::LeftChars($giftcard[1], 100); // information relating to giftcard 2
        $resultArray['receiverid'] = $txn_num;
        $resultArray['pendingreason'] = $xresult;
        $resultArray['transactiontype'] = $trans_name;
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

    static function verifyTransaction($pServer, $pStoreId, $pHppKey, $pTransactionKey, $pReferrer)
    // perform transaction verification
    {
        $resultArray = Array();
		$error = '';

		// build request string
		$pRequestData  = 'ps_store_id=' . $pStoreId;
		$pRequestData .= '&hpp_key=' . $pHppKey;
		$pRequestData .= '&transactionKey=' . $pTransactionKey;

		$parsedUrl = parse_url($pServer);

		if (empty($parsedUrl['port'])) {
			$parsedUrl['port'] = strtolower($parsedUrl['scheme']) == 'https' ? 443 : 80;
		}

		// generate request
		$header  = 'POST ' . $parsedUrl['path'] ." HTTP/1.1\r\n";
		$header .= 'Host: ' . $parsedUrl['host'] . "\r\n";
		if (trim($pReferrer) != '')
		{
			$header .= 'Referer: ' . $pReferrer . "\r\n";
		}
//		$header .= "Content-Type: text/plain\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= 'Content-Length: ' . strlen($pRequestData) . "\r\n";
		$header .= "Connection: close\r\n";
		$header .= "\r\n";
		$request = $header . $pRequestData;


		$replyData    = '';
		$errno  = 0;
		$errstr = '';

		// open socket to filehandle and retry up to 3 times with a 30 second timeout
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
			// read the response from eSelect
			// while content exists, keep retrieving document in 1K chunks
			fwrite($fp, $request);
			fflush($fp);

			while (!feof($fp))
			{
				$replyData .= fread($fp, 1024);
			}

			fclose($fp);

			if (!$errno)
			{
				$headerSize = strpos($replyData, "\r\n\r\n");
				$headerData = substr($replyData, 0, $headerSize);
				$header = explode("\r\n", $headerData);
				$statusLine = explode(" ", $header[0]);
//				$contentType = "application/octet-stream";
				$chunked = false;

				foreach ($header as $headerLine)
				{
					$headerParts = explode(":", $headerLine);

// actually sends back text/html, not */xml
// 					if (strtolower($headerParts[0]) == "content-type") {
// 						$contentType = trim($headerParts[1]);
// 						break;
// 					}
//
					if (strtolower($headerParts[0]) == "transfer-encoding") {
						$chunked = (strtolower(trim($headerParts[1])) == 'chunked');
						break;
					}
				}

				$replyInfo = array(
					'httpCode'    => (int) $statusLine[1],
//					'contentType' => $contentType,
					'headerSize'  => $headerSize + 4);

				if ($replyInfo['httpCode'] != 200) {
					$error = 'HTTP code is ' . $replyInfo['httpCode'] . ', expected 200';
				}

// 				if (strstr($replyInfo['contentType'], "/xml") === false) {
// 					$error = 'Content type is ' . $replyInfo['contentType'] . ', expected */xml';
// 				}

				// split header and body
				$replyXml    = substr($replyData, $replyInfo['headerSize']);

				// Transfer-Encoding: chunked
				if ($chunked)
				{
					$replyXml = self::transferEncodingChunkedDecode($replyXml);
				}
			}
			else
			{
				$error = $errstr;
			}
		}
		else
		{
			if ($errno)
			{
				$error = $errstr . '(' . $errno . ')';
			}
		}

		if ($error == '')
		{
			$replyXml = str_replace("\r\n",'',$replyXml);
			// read XML
			$xml = simplexml_load_string($replyXml);

			if ($xml)
			{
				$resultArray['order_id'] = trim((string) $xml->order_id);
				$resultArray['response_code'] = ltrim(trim((string) $xml->response_code),'0');
				$resultArray['txn_num'] = trim((string) $xml->txn_num);
				$resultArray['status'] = (string) $xml->status;
				$resultArray['error'] = '';
				$resultArray['replyData'] = $replyData;
				$resultArray['replyXml'] = $replyXml;
			}
			else
			{
				$error = 'eSelect::Non-XML reply.';
			}
		}

		if ($error != '')
		{
			$resultArray['order_id'] = '';
			$resultArray['response_code'] = '';
			$resultArray['txn_num'] = '';
			$resultArray['status'] = '';
			$resultArray['error'] = $error;
			$resultArray['replyData'] = $replyData;
			$resultArray['replyXml'] = '';
		}

		return $resultArray;
	}

	static function transferEncodingChunkedDecode($in)
	{
		$out = '';
		while ($in != '')
		{
			$lf_pos = strpos($in, "\012");
			if ($lf_pos === false)
			{
				$out .= $in;
				break;
			}
			$chunk_hex = trim(substr($in, 0, $lf_pos));
			$sc_pos = strpos($chunk_hex, ';');
			if ($sc_pos !== false)
			{
				$chunk_hex = substr($chunk_hex, 0, $sc_pos);
			}

			if ($chunk_hex == '')
			{
				$out .= substr($in, 0, $lf_pos);
				$in = substr($in, $lf_pos + 1);
				continue;
			}

			$chunk_len = hexdec($chunk_hex);
			if ($chunk_len)
			{
				$out .= substr($in, $lf_pos + 1, $chunk_len);
				$in = substr($in, $lf_pos + 2 + $chunk_len);
			}
			else
			{
				$in = '';
			}
		}
		return $out;
	}

	static function xmlPrettyPrint($text, $html = false)
	// pretty-print XML in $text, optionally escape output for use in HTML
	{
		// line breaks between tags
		$text = str_replace('><', ">\n<", $text);
		// explode text into array
		$lines = explode("\n", $text);
		for ($i = 1, $size = sizeof($lines); $i < $size; ++$i)
		{
			// check if opening tag is followed by closing tag
			if ((strlen($lines[$i-1]) +1 == strlen($lines[$i])) &&
				($lines[$i-1] == str_replace('/', '', $lines[$i])))
			{
				$lines[$i-1] = str_replace('>', '/>', $lines[$i-1]);	// <tag/>
				$lines[$i] = ''; // this empty line needs to be removed later on
			}
		}
		$indent = ''; // the indent corresponding to stepping depth
		for ($i = 1, $size = sizeof($lines); $i < $size; ++$i)
		{
			if ($lines[$i] != '')
			{
				$pos = strpos($lines[$i], '/');
				if (($pos) && ($pos > 0))
				// there is a backslash
				{
					if ($pos == 1)
					{
						// if backslash is in second position, it's a closing tag
						// and we need to shorten the indent
						if ($indent == "\t")
						{
							$indent = '';
						}
						else
						{
							$indent = substr($indent, 1);
						}
					}
					$lines[$i] = $indent . $lines[$i];
				}
				// if there is no backslash at all, it must be an opening tag
				else
				{
					// apply indent
					$lines[$i] = $indent . $lines[$i];
					// only then increase indent
					$indent .= "\t";
				}
			}
		}
		// turn into text again
		$text = implode("\n", $lines);
		// remove empty lines
		$text = str_replace("\n\n", "\n", $text);

		return ($html) ? htmlentities($text) : $text;
	}

	static function validMonerisField($pText, $pLength = 0)
	// format Text so it can be posted to Moneris
	{
		// valid is only a-z A-Z 0-9 _ - : . @ $ = /
		$result = preg_replace("/([^a-zA-Z 0-9_:.@$=\/-])/", '', $pText);

		if ($pLength > 0)
		{
			$result	= UtilsObj::leftChars($result, $pLength);
		}

		return $result;
	}
}

?>