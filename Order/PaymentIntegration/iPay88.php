<?php

require_once __DIR__ . '/TaopixAbstractGateway.php';

/**
 * Integration for iPay88 payment gateway.
 */
class iPay88 extends TaopixAbstractGateway
{
	/**
	 * Configure payment gateway.
	 *
	 * @return array
	 */
	public function configure()
	{
		$resultArray = [
			'active' => false,
			'form' => '',
			'scripturl' => '',
			'script' => '',
			'action' => '',
			'gateways' => []
		];

		AuthenticateObj::clearSessionCCICookie();

		$active = false;
		$gateways = [];
		$currency = $this->session['order']['currencycode'];

		$smarty = SmartyObj::newSmarty('CreditCardPayment');

        // read in payment gateways
		$gatewaysArray = explode(',', UtilsObj::getArrayParam($this->config, 'IPAYGATEWAYS'));
		foreach ($gatewaysArray as $gateway)
		{
			// test for iPay88 supported currencies
			switch ($currency)
        	{
				case 'MYR':
					if (in_array($gateway, array('2','6','8','10','14','15','16','17','20','22','23')))
					{
						$gateways[$gateway] = $smarty->get_config_vars('str_OrderiPay88_' . $gateway);
						$active = true;
					}
					break;

				case 'CNY':
					if ($gateway == '21')
					{
						$gateways[$gateway] = $smarty->get_config_vars('str_OrderiPay88_' . $gateway);
						$active = true;
					}
					break;

				case 'USD':
					if (in_array($gateway, array('25','33')))
					{
						$gateways[$gateway] = $smarty->get_config_vars('str_OrderiPay88_' . $gateway);
						$active = true;
					}
					break;
			}
		}

        $resultArray['active'] = ($active && PaymentIntegrationObj::checkSSL()) ? true : false;
        $resultArray['gateways'] = $gateways;

		return $resultArray;
	}


	/**
	 * Initialize the payment gateway.
	 *
	 * @return array
	 */
	public function initialize()
	{
        $smarty = SmartyObj::newSmarty('Order', UtilsObj::getArrayParam($this->session, 'webbrandcode'), UtilsObj::getArrayParam($this->session, 'webbrandapplicationname'));

    	// First check if we have any ccidata. This is set when the call is made the first time.
        // If the data is set then the user must have hit the back button on their browser
        if (UtilsObj::getArrayParam($this->session['order'], 'ccidata') == '')
        {
			$cancelReturnPath = UtilsObj::correctPath(UtilsObj::getArrayParam($this->session, 'webbrandweburl')) . '?fsaction=Order.ccCancelCallback&ref=' . UtilsObj::getArrayParam($this->session, 'ref');
			$backendUrl = UtilsObj::correctPath(UtilsObj::getArrayParam($this->session, 'webbrandweburl')) . '?fsaction=Order.ccAutomaticCallback&ref=' . UtilsObj::getArrayParam($this->session, 'ref');

			$server = UtilsObj::getArrayParam($this->config, 'IPAYREQUEST');
			$merchantCode = UtilsObj::getArrayParam($this->config, 'IPAYMERCHANTCODE');
			$merchantKey = UtilsObj::getArrayParam($this->config, 'IPAYMERCHANTKEY');

			$responseUrl = UtilsObj::getArrayParam($this->config, 'IPAYRESPONSEURL');

			// amount with period as decimal symbol
			$amount = number_format(UtilsObj::getArrayParam($this->session['order'], 'ordertotaltopay'), UtilsObj::getArrayParam($this->session['order'], 'currencydecimalplaces'), '.', '');
			$description = UtilsObj::getArrayParam($this->session['items'][0], 'itemqty') . ' x ' . LocalizationObj::getLocaleString(UtilsObj::getArrayParam($this->session['items'][0], 'itemproductname'), UtilsObj::getArrayParam($this->session, 'browserlanguagecode'), true);
			$description = UtilsObj::encodeString($description);

			// selected payment gateway
			$paymentID = UtilsObj::getArrayParam($this->session['order'], 'paymentgatewaycode');

			// get the session ref, this is not for iPay88, but for us
			$remark = UtilsObj::getArrayParam($this->session, 'ref');

			// build transaction id
			// make it no more than 20 charcters long
			$transactionId = $remark . time();

			$currency = UtilsObj::getArrayParam($this->session['order'], 'currencycode');

			$userName = UtilsObj::getArrayParam($this->session['shipping'][0], 'shippingcontactfirstname') . ' ' . UtilsObj::getArrayParam($this->session['shipping'][0], 'shippingcontactlastname');
			$userEmail = UtilsObj::getArrayParam($this->session['shipping'][0], 'shippingcustomeremailaddress');
			$userContact = UtilsObj::getArrayParam($this->session['shipping'][0], 'shippingcustomertelephonenumber');

			// build signature
			$signatureParams = [$merchantKey, $merchantCode, $transactionId, str_replace('.', '', $amount), $currency];
			$signatureStr = $this->hashString($signatureParams, '');
			$signature = $this->generateHash($signatureStr);

			// Store parameters in an array
			$parameters = array(
				'MerchantCode' => $merchantCode,
				'PaymentId' => $paymentID,
				'RefNo' => $transactionId,
				'Amount' => $amount,
				'Currency' => $currency,
				'ProdDesc' => $description,
				'UserName' => $userName,
				'UserEmail' => $userEmail,
				'UserContact' => $userContact,
				'Remark' => $remark,
				'Lang' => 'UTF-8',
				'Signature' => $signature,
				'ResponseURL' => $responseUrl,
				'BackendURL' => $backendUrl
			);

			// define Smarty variables
			$smarty->assign('method', 'POST');
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('payment_url', $server);
			$smarty->assign('parameter', $parameters);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $remark);
			$smarty->assign('ccicookievalue', UtilsObj::getArrayParam($this->session['order'], 'ccicookie'));

			// set the ccidata to remember we have jumped to iPay88
			$this->session['order']['ccidata'] = 'start';
			DatabaseObj::updateSession();

			$smarty->cachePage = true; // allow the page to be cached so that the browser back button works correctly
            if (UtilsObj::getArrayParam($this->session, 'ismobile') == true)
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

			$cancelReturnPath = UtilsObj::correctPath(UtilsObj::getArrayParam($this->session, 'webbrandweburl')) . '?fsaction=Order.ccCancelCallback&ref=' . UtilsObj::getArrayParam($this->session, 'ref');
            $smarty->assign('payment_url', $cancelReturnPath);
            $smarty->assign('cancel_url', $cancelReturnPath);

            if (UtilsObj::getArrayParam($this->session, 'ismobile') == true)
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
	 * The payment request has been cancelled.
	 * iPay88 has no Cancel button, we only get here by pressing Backspace.
	 *
	 * @return boolean
	 */
    public function cancel()
    {
		$this->cciLogUpdate = false;

		$resultArray = [
			'result' => '',
			'ref' => UtilsObj::getGETParam('ref', ''),
			'transactionid' => '',
			'authorised' => false,
			'showerror' => false
		];

        return $resultArray;
    }


	public function confirm($pCallbackType)
	{
		$resultArray = $this->cciEmptyResultArray();
        $result = '';
        $authorised = false;
		$active = true;
		$error = '';
		$description = '';

		$ref = UtilsObj::getGETParam('ref', '');

        // read POST variables
		$postMerchantCode = UtilsObj::getPOSTParam('MerchantCode', '');
		$paymentId = UtilsObj::getPOSTParam('PaymentId', '');
		$refNo = UtilsObj::getPOSTParam('RefNo', '');
		$postAmount = UtilsObj::getPOSTParam('Amount', '');
		$currency = UtilsObj::getPOSTParam('Currency', '');
		$postRemark = UtilsObj::getPOSTParam('Remark', '');
		$transId = UtilsObj::getPOSTParam('TransId', '');
		$authCode = UtilsObj::getPOSTParam('AuthCode', '');
		$tStatus = UtilsObj::getPOSTParam('Status', '');
		$errDesc = UtilsObj::getPOSTParam('ErrDesc', '');
		$signature = UtilsObj::getPOSTParam('Signature', '');

		$requery = $this->config['IPAYREQUERY'];
		$merchantCode = $this->config['IPAYMERCHANTCODE'];
		$merchantKey = $this->config['IPAYMERCHANTKEY'];
		$vendorName = $this->config['IPAYVENDORNAME'];

		// before we do anything else, let's check if the data coming back matches what we have sent
		$orderAmount = number_format($this->session['order']['ordertotaltopay'], $this->session['order']['currencydecimalplaces'], '.', '');

		// if there is a signature, let's check that
		// there will only be a signature if the transaction was successful or has been declined
		if ($signature != '')
		{
			$signatureParams = [$merchantKey, $merchantCode, $paymentId, $refNo, str_replace('.', '', $orderAmount), $currency, $tStatus];
			$verifySignatureString = $this->hashString($signatureParams, $pCallbackType);
			if (! $this->verifyHash($signature, $verifySignatureString, $pCallbackType))
			{
				// we have discrepancies
				$error = 'Incorrect Signature.';
				$active = false;
			}
		}
		else
		{
			// we check merchantCode and amount
			if (($postMerchantCode != $merchantCode) || ($orderAmount != $postAmount))
			{
				// we have discrepancies
				$error = 'Data in response does not match data in request.';
				$active = false;
			}
			else
			{
				// there is a corruption in the data we have sent
				$error = 'Data sent: '.$errDesc;
				$active = false;
			}
		}

		// no errors? proceed!
		if ($error == '')
		{
			// we double check by requesting the transaction status
            $params = "MerchantCode=" . $this->config['IPAYMERCHANTCODE'];
            $params .= "&RefNo=" . $refNo;
            $params .= "&Amount=" . $orderAmount;

			if ($reply = $this->Post($requery, $params, $error))
			{
				// all we ever get back is a line of text
				if ($reply != '00')
				{
					$error = $reply;
					$active = false;
					$tStatus = $reply;
				}
				else
				{
					$tStatus = 'Successful payment';
				}
			}
			else
			{
				// error occured, show error page
				$error = 'No reply from iPay88';
				$active = false;
			}

			if ($active)
			{
				// we continue because there was no error so far
				// and we have a successful payment
				$authorised = true;
				$authorisedStatus = 1;

				if ($this->session['shipping'][0]['shippingcustomerstate'] != '')
				{
					$state = $this->session['shipping'][0]['shippingcustomerstate'];
				}
				else
				{
					$state = $this->session['shipping'][0]['shippingcustomercounty'];
				}

				$description = $this->session['items'][0]['itemqty'] . ' x ' . $this->session['items'][0]['itemproductname'];
				$showError = false;
			}
		}

		if ($error != '')
		{
			// display error
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': Invalid reply';
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $error;
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $refNo;
			$resultArray['data4'] = '';
			$resultArray['errorform'] = 'error.tpl';
			$tStatus = $error; // for logging
			$showError = true;
			$authorisedStatus = 0;
		}

        $payerId = $this->session['order']['useripaddress'];

		$serverTime = DatabaseObj::getServerTime();
        PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTime, $error);

		// check ccilog to see if this is an update
		$cciLogEntry = $this->getCciLogEntry($ref, $refNo);

		if (empty($cciLogEntry))
		{
			// first callback
			$update = false;
			$resultArray['orderid'] = 0;
		}
		else
		{
			// additional callbacks
			$update = true;
			$this->updateStatus = true;
			$resultArray['orderid'] = UtilsObj::getArrayParam($cciLogEntry, 'orderid', 0);
		}

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $orderAmount;
        $resultArray['formattedamount'] = $orderAmount;
        $resultArray['charges'] = '000';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $refNo;  // this is our unique ID, not the real order ID
        $resultArray['formattedtransactionid'] = $refNo;
        $resultArray['responsecode'] = $tStatus;
        $resultArray['responsedescription'] = $description;
        $resultArray['authorisationid'] = $transId;
        $resultArray['formattedauthorisationid'] = $transId;
        $resultArray['bankresponsecode'] = $authCode;
        $resultArray['paymentdate'] = $serverTime;
        $resultArray['paymentreceived'] = $authorisedStatus;
        $resultArray['formattedpaymentdate'] = $serverTime;
        $resultArray['payerid'] = $payerId;
        $resultArray['business'] = $vendorName;
        $resultArray['currencycode'] = $this->session['order']['currencycode'];
        $resultArray['webbrandcode'] = $this->session['webbrandcode'];
        $resultArray['update'] = $update;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = array();
    	$resultArray['showerror'] = $showError;

		if ($pCallbackType == 'automatic')
		{
			$resultArray['acknowledgement'] = 'RECEIVEOK';
		}

		PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTime, "TAOPIX TESTING - RESULT STRING:\n\n" . var_export($resultArray, true));

        return $resultArray;
    }


	protected function transferEncodingChunkedDecode($in)
	{
		$out = '';
		while($in != '')
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
			if($chunk_len)
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


	protected function Post($pUrl, $pRequestData, &$pErrorString)
	{
		$parsedUrl = parse_url($pUrl);

		if (empty($parsedUrl['port']))
		{
			$parsedUrl['port'] = strtolower($parsedUrl['scheme']) == 'https' ? 443 : 80;
		}

		// generate request
		$header  = 'POST ' . $parsedUrl['path'] . " HTTP/1.1\r\n";
		$header .= 'Host: ' . $parsedUrl['host'] . "\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= 'Content-Length: ' . strlen($pRequestData) . "\r\n";
		$header .= "Connection: close\r\n";
		$header .= "\r\n";
		$request = $header . $pRequestData;

		$replyData = '';
		$errno  = 0;
		$errstr = '';

		// open socket to filehandle and retry upto 3 times with a 20 second timeout
		$retryCount = 3;
		while ($retryCount > 0)
		{
			UtilsObj::resetPHPScriptTimeout(30); //increase the standard php timeout each retry
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
			// read the response from iPay88
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
				$headerSize  = strpos($replyData, "\r\n\r\n");
				$headerData  = substr($replyData, 0, $headerSize);
				$header       = explode("\r\n", $headerData);
				$statusLine  = explode(" ", $header[0]);
				$chunked = false;

				foreach ($header as $headerLine)
				{
					$headerParts = explode(":", $headerLine);

					if (strtolower($headerParts[0]) == "transfer-encoding") {
						$chunked = (strtolower(trim($headerParts[1])) == 'chunked');
						break;
					}
				}

				$replyInfo = array(
					'httpCode'    => (int) $statusLine[1],
					'headerSize'  => $headerSize + 4);

				if ($replyInfo['httpCode'] != 200) {
					$pErrorString = 'HTTP code is ' . $replyInfo['httpCode'] . ', expected 200';
					return false;
				}

				// split header and body
				$replyText   = substr($replyData, $replyInfo['headerSize']);

				// Transfer-Encoding: chunked
				if ($chunked)
				{
					$replyText = $this->transferEncodingChunkedDecode($replyText);
				}
			}
			else
			{
				$pErrorString = $errstr;
				return false;
			}
		}

		return $replyText; // Xml as plain text
	}


	/**
	 * Create the string used to generate the signature
	 *
	 * @param array $pParams The collection of values used to generate the string to be used to sign the transaction
	 * @param string $pType The signature being generated, either sending of data or verifying received data
	 *
	 * @return string
	 */
	public function hashString($pParams, $pType)
	{
		// join each element of the array to create the signature string
		$hashString = join('', $pParams);

		return $hashString;
	}


	/**
	 * Generate the signature required for the iPay88 payment gateway
	 *
	 * @param string $pSource The string used to generate the signature
	 *
	 * @return string Signature used to sign the transaction
	 */
	public function generateHash($pSource)
	{
		$pSource = sha1($pSource);
		$len = strlen($pSource);
		$bin = '';
		for ($i = 0; $i < $len; $i += 2)
		{
			$bin .= chr(hexdec(substr($pSource, $i, 2)));
		}
		return base64_encode($bin);
	}


	/**
	 * Make sure the signature sent from the payment gateway is correct.
	 *
	 * @param string $pSuppliedHash The signature from the gateway.
	 * @param string $pVerifyString The string which was used to create the signature.
	 * @param string $pType The signature being generated, either sending of data or verifying received data
	 *
	 * @return boolean Does the signature sent match that generate using the data
	 */
	public function verifyHash($pSuppliedHash, $pVerifyString, $pType)
	{
		$signature = $this->generateHash($pVerifyString);
		return ($signature == $pSuppliedHash);
	}


	/**
	* Gets the last entry in the CCI log table for a given session reference and transaction id.
	* This is a modified version of PaymentIntegrationObj::getCciLogEntry specifically for iPay88
	* allowing a transaction ID to be passed. iPay88 records failed transactions which are returned
	* in the original function, preventing orders being created.
	*
	* Also returns the order number if present.
	* If there is no entry, an empty array will be returned.
	*
	* 'id' field is -1 if there was no entry.
	*/
    protected function getCciLogEntry($pSessionRef, $pTransactionID)
    {
        $resultArray = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $sql = 'SELECT cl.*, oh.ordernumber as ordernumber
                    FROM ccilog cl
                    LEFT JOIN orderheader oh ON (oh.id = cl.orderid)
                    WHERE (cl.sessionid = ?) AND (cl.transactionid = ?)
					ORDER BY cl.datecreated DESC';

            if ($stmt = $dbObj->prepare($sql))
            {
                if ($stmt->bind_param('ss', $pSessionRef, $pTransactionID))
                {
                    if ($stmt->execute())
                    {
                        DatabaseObj::stmt_bind_assoc($stmt, $row);
                        if ($stmt->fetch())
                        {
                            foreach ($row as $key=>$value)
                            {
                                $resultArray[$key] = $value;
                            }
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