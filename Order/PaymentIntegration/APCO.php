<?php

class ApcoObj
{
    static function configure()
    {
       global $gSession;

        $resultArray = Array();
        $APCOConfig = PaymentIntegrationObj::readCCIConfigFile('../config/APCO.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
        $currencyList = $APCOConfig['APCOCURRENCIES'];
        $currency = $gSession['order']['currencyisonumber'];
        $active = true;

        // test for APCO supported currencies
        if (strpos($currencyList, $currency) === false)
        {
            $active = false;
        }

        AuthenticateObj::clearSessionCCICookie();
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

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$APCOConfig = PaymentIntegrationObj::readCCIConfigFile('../config/APCO.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

			$manualReturnPath = UtilsObj::correctPath($gSession['webbrandwebroot']) . '/PaymentIntegration/APCO/APCOCallBack.php';
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$automaticReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccAutomaticCallback&ref=' . $gSession['ref'];

			$server = $APCOConfig['APCOSERVER'];
			$merchant = $APCOConfig['PROFILEID'];
			$secretWord = $APCOConfig['SECRETWORD'];
			$allowRetry = $APCOConfig['ALLOWRETRY'];
			$newCardOnFail = $APCOConfig['NEWCARDONFAIL'];
			$topBannerURL = $APCOConfig['TOPBANNERURL'];
			$bottomBannerURL = $APCOConfig['BOTTOMBANNERURL'];

			$actionType = 1;

			$amount = $gSession['order']['ordertotaltopay'];
			$currency = $gSession['order']['currencyisonumber'];
			$orderRef = $gSession['ref'] . '_'. time();
			$description = $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
			$customerEmail = $gSession['shipping'][0]['shippingcustomeremailaddress'];

			// test for APCO supported languages
			$defaultLanguage = strtoupper($gConstants['defaultlanguagecode']);
			$lang = substr($gSession['browserlanguagecode'], 0, 2);

			$supportedLanguages = array('en', 'mt', 'it', 'fr', 'de', 'es', 'hr', 'se', 'ro', 'hu', 'cz', 'sk',
			'pt', 'rs', 'si', 'nl', 'zh', 'no', 'ru', 'us', 'gr', 'pl', 'bg', 'jp');

			if (! in_array($lang, $supportedLanguages))
			{
				$lang = (in_array($defaultLanguage, $supportedLanguages)) ? $defaultLanguage : 'en';
			}

			$transaction = Array();
			$transaction['ProfileID'] = $merchant;
			$transaction['ActionType'] = $actionType;
			$transaction['Value'] = $amount;
			$transaction['Curr'] = $currency;
			$transaction['Lang'] = $lang;
			$transaction['ORef'] = $orderRef;
			$transaction['UDF1'] = htmlspecialchars($description);
			$transaction['UDF2'] = $gSession['ref'];
			$transaction['UDF3'] = '';
			$transaction['Email'] = $customerEmail;
			$transaction['RedirectionURL'] = $manualReturnPath;
			$transaction['status_url'] = $automaticReturnPath;
			$transaction['PostDeclined'] = '';
			$transaction['return_pspid'] = '';
			$transaction['ExtendedErr'] = '';
			$transaction['ExtendedData2'] = '';

			if ($allowRetry == 0)
			{
				$transaction['noRetry'] = '';
			}

			if ($newCardOnFail == 1)
			{
				$transaction['NewCardOnFail'] = '';
			}

			$popUp = Array();
			if ($topBannerURL != '')
			{
				$popUp['topBannerURL'] = $topBannerURL;
			}

			if ($bottomBannerURL != '')
			{
				$popUp['bottomBannerURL'] = $bottomBannerURL;
			}

			// generate request XML
			$request = '<Transaction hash="'.$secretWord.'">';
			$request .= self::arrayToXml($transaction);
			$request .= '<ShowInPopup>';
			$request .= self::arrayToXml($popUp);
			$request .= '</ShowInPopup>';
			$request .= '</Transaction>';

			//hash the file
			$xmlMD5Hash = md5($request);

			//generate new file that includes the hash
			$request = '<Transaction hash="'.$xmlMD5Hash.'">';
			$request .= self::arrayToXml($transaction);
			$request .= '<ShowInPopup>';
			$request .= self::arrayToXml($popUp);
			$request .= '</ShowInPopup>';
			$request .= '</Transaction>';

            $parameters = array(
              'params' => urlencode($request)
            );

			// define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('cancelurl', $cancelReturnPath);
			$smarty->assign('method', 'post');
			$smarty->assign('parameter', $parameters);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to APCO
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
        $resultArray['ref'] = $_GET['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

	static function AutomaticCancel()
	{
		exit();
	}

    static function confirm($callBack)
    {
     	global $gSession;

        $resultArray = Array();
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $showError = false;
        $automatic = false;
        $orderExists = false;
        $update = false;

       	$APCOConfig = PaymentIntegrationObj::readCCIConfigFile('../config/APCO.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

		$secretWord = $APCOConfig['SECRETWORD'];

       	$amount = $gSession['order']['ordertotaltopay'];
		$formatted_payment_date = DatabaseObj::getServerTime();

       	if ($callBack == 'automatic')
       	{
       		$automatic = true;
       	}

		$params = $_POST['params'];
       	$xml = simplexml_load_string($params);

		if ($xml)
		{
			$returnedHash = $xml['hash'];
			$orderNumber = (string) $xml->ORef;
			$result =  (string) $xml->Result;
			$authcode = (string) $xml->AuthCode;
			$CardInput = (string) $xml->CardInput;
			$transactionRef = (string) $xml->pspid;
			$status3DS = (string) $xml->Status3DS;
			$cardNumber = (string) $xml->ExtendedData->CardNum;
			$cardExpiry = (string) $xml->ExtendedData->CardExpiry;
			$cardHolderName = (string) $xml->ExtendedData->CardHName;
			$acq = (string) $xml->ExtendedData->Acq;
			$source = (string) $xml->ExtendedData->Source;
			$cardCountry = (string) $xml->ExtendedData->CardCountry;
			$cardType = (string) $xml->ExtendedData->CardType;
			$description = (string) $xml->UDF1;
			$sessionRef = (string) $xml->UDF2;
			$UDF3 = (string) $xml->UDF3;

			//calculate hash
			$transaction = Array();
			$transaction['ORef'] = $orderNumber;
			$transaction['Result'] = $result;
			$transaction['AuthCode'] = $authcode;
			$transaction['CardInput'] = $CardInput;
			$transaction['pspid'] = $transactionRef;
			$transaction['Status3DS'] = $status3DS;

			$extended = Array();
			$extended['CardNum'] = $cardNumber;
			$extended['CardExpiry'] = $cardExpiry;
			$extended['CardHName'] = $cardHolderName;
			$extended['Acq'] = $acq;
			$extended['Source'] = $source;

			if ($cardCountry != '')
			{
				$extended['CardCountry'] = $cardCountry;
			}

			if ($cardType != '')
			{
				$extended['CardType'] = $cardType;
			}

			$transaction2 = Array();
			$transaction2['UDF1'] = $description;
			$transaction2['UDF2'] = $sessionRef;
			$transaction2['UDF3'] = $UDF3;

			// generate request XML
			$request = '<Transaction hash="'.$secretWord.'">';
			$request .= self::arrayToXml($transaction);
			$request .= '<ExtendedData>';
			$request .= self::arrayToXml($extended);
			$request .= '</ExtendedData>';
			$request .= self::arrayToXml($transaction2);
			$request .= '</Transaction>';

			$generatedHash = md5($request);

			if ($automatic)
			{
				$dbObj = DatabaseObj::getGlobalDBConnection();
				// Check to see that the order already exists if it does then it is the server to server update.
				if ($dbObj)
				{
					// get the last log entry with this transaction id
                    $stmt = $dbObj->prepare('SELECT `ordernumber`, `amount`
                                                FROM `CCILOG`
                                                    LEFT JOIN orderheader ON (orderheader.id = ccilog.orderid)
                                                WHERE `transactionid` = ?
                                                ORDER BY ccilog.datecreated DESC');
					if ($stmt)
					{
						if ($stmt->bind_param('s', $transactionRef))
						{
							if ($stmt->execute())
							{
								if ($stmt->store_result())
								{
									if ($stmt->num_rows > 0)
									{
										if ($stmt->bind_result($orderNumber, $amount))
										{
											if($stmt->fetch())
											{
												$orderExists = true;
											}
											else
											{
												$error = 'APCO Confirm fetch ' . $dbObj->error;
											}
										}
										else
										{
											$error = 'APCO Confirm bind result ' . $dbObj->error;
										}
									}
								}
								else
								{
									$error = 'APCO Confirm store result ' . $dbObj->error;
								}
							}
							else
							{
								$error = 'APCO Confirm execute ' . $dbObj->error;
							}
						}
						else
						{
							$error = 'APCO Confirm bind params ' . $dbObj->error;
						}
					}
					else
					{
						$error = 'APCO Confirm prepare ' . $dbObj->error;
					}
				}
			}

			if ($result == 'OK')
			{
				if($orderExists)
				{
					$update = true;
				}

				if ($returnedHash == $generatedHash)
				{
					$authorised = true;
					$authorisedStatus = 1;
				}
				else
				{
					// md5 check failed
					$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': MD5KEY';
					$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': MD5 check failed';
					$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $transactionRef;
					$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $orderNumber;
					$resultArray['errorform'] = 'error.tpl';
					$showError = true;
					$authorised = false;
					$authorisedStatus = 0;
				}
		   }
		   else if($result == 'PENDING')
		   {
				$authorised = true;
				$authorisedStatus = 0;
		   }
		   else if($result == 'DECLINED')
		   {
				if($orderExists)
				{
					$update = true;
				}

				if (!$automatic)
				{
					if (! array_key_exists('data1', $resultArray))
					{
						$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': Payment Error';
						$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $result;
						$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $orderNumber;
						$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $orderNumber;
					}
					$showError = true;
					$authorised = false;
					$authorisedStatus = 0;
					$resultArray['errorform'] = 'error.tpl';
				}
		   }

		}
		else
		{
			$error = 'Non-XML reply.';
		}

        if ($automatic)
		{

            // write logs
            $logData['dateTime'] = $formatted_payment_date;
            foreach($xml->children() as $elementName => $child)
            {
                $logData[$elementName] = $child;
            }

            PaymentIntegrationObj::logPaymentGatewayData($APCOConfig, $formatted_payment_date, $error);

		}

        $resultArray['result'] = $result;
        $resultArray['ref'] = $sessionRef;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $transactionRef;
        $resultArray['formattedtransactionid'] = $transactionRef;
        $resultArray['responsecode'] = '';
        $resultArray['responsedescription'] = '';
        $resultArray['authorisationid'] = $authcode;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $authcode;
        $resultArray['bankresponsecode'] = '';
        $resultArray['cardnumber'] = $cardNumber;
        $resultArray['formattedcardnumber'] = $cardNumber;
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = $transactionRef;
        $resultArray['paymentdate'] = $formatted_payment_date;
        $resultArray['paymentmeans'] = '';
        $resultArray['paymenttime'] = '';
		$resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $formatted_payment_date;
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
        $resultArray['update'] = $update;
        $resultArray['orderid'] = 0;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = $showError;

        return $resultArray;

    }

	static function xmlEscape($str)
	{
		return htmlspecialchars($str, ENT_COMPAT, 'UTF-8');
	}

	static function arrayToXml($arr)
	{
		$data = "";

		foreach ($arr as $key => $value)
		{
			$value = self::xmlEscape($value);
			if ($key == 'status_url')
			{
				$data.= "<{$key} urlEncode=\"true\">{$value}</{$key}>";
			}
			else
			{
				$data .= "<{$key}>{$value}</{$key}>";
			}
		}

		return $data;
	}

}

?>