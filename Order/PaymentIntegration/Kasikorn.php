<?php

class KasikornObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();
        $active = true;

        AuthenticateObj::clearSessionCCICookie();

        // test for Kasikorn supported currencies
        if ($gSession['order']['currencyisonumber'] != '764')
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
        global $ac_config;
        global $gSession;

		$smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
		// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
        	$kasikornConfig = PaymentIntegrationObj::readCCIConfigFile('../config/kasikorn.conf',$gSession['order']['currencycode'],'');

        	if ($kasikornConfig['REALTIMEURL'] != '')
			{
				$automaticReturnPath = UtilsObj::correctPath($kasikornConfig['REALTIMEURL']) . 'PaymentIntegration/Kasikorn/KasikornCallback.php';
			}
			else
			{
				$automaticReturnPath = '';
			}

        	$normalReturnPath = UtilsObj::correctPath($ac_config['WEBURL']) . 'PaymentIntegration/Kasikorn/KasikornComplete.php';
        	$cancelReturnPath = UtilsObj::correctPath($ac_config['WEBURL']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

			$merchant2 = $kasikornConfig['MERCHANT2'];
			$term2 = $kasikornConfig['TERM2'];
			$fillSpace = $kasikornConfig['FILLSPACE'];
			$md5Key = $kasikornConfig['MD5KEY'];
			
			if($gSession['ismobile'] == 1)
			{
				$kasikornURL = $kasikornConfig['MOBILESERVER'];
			}
			else
			{
				$kasikornURL = $kasikornConfig['DESKTOPSERVER'];
			}

			$orderValue = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
			$orderValue = str_pad($orderValue,12,'0',STR_PAD_LEFT);
			$description = substr($gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true), 0, 30);

			// we don't have an invoice number at this point in time so invent one using the session reference and date
			$invoiceNumber = substr(time(), -4, 4) . str_pad($gSession['ref'], 8, '0', STR_PAD_LEFT);
			
			$description = str_replace(' ', '+', $description);
			
			$md5ToHash = $merchant2 . $term2 . $orderValue . $normalReturnPath . $automaticReturnPath . $_SERVER['SERVER_ADDR'] . $description;
			$md5ToHash .= $invoiceNumber . $fillSpace . $md5Key;
			$md5CheckSum = md5($md5ToHash);

            $parameters = array(
				'MERCHANT2' => $merchant2,
				'TERM2' => $term2,
				'AMOUNT2' => $orderValue,
                'URL2' => $normalReturnPath,
				'RESPURL' => $automaticReturnPath,
				'IPCUST2' => $_SERVER['SERVER_ADDR'],
				'DETAIL2' => $description,
	            'INVMERCHANT' => $invoiceNumber,
				'FILLSPACE' => $fillSpace,
				'CHECKSUM' => $md5CheckSum
			);

			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('payment_url', $kasikornURL);
			$smarty->assign('method', 'post');
			$smarty->assign('parameter', $parameters);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to kasikorn
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

            $cancelReturnPath = UtilsObj::correctPath($ac_config['WEBURL']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
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
        $resultArray['authorisedstatus'] = 0;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function confirm()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';

        $kasikornConfig = PaymentIntegrationObj::readCCIConfigFile('../config/kasikorn.conf',$gSession['order']['currencycode'],'');

        $authorised = false;
        $authorisedStatus = 0;
        $logID = 0;
        $update = false;

        $responseCode = UtilsObj::getPOSTParam('HOSTRESP', '');
        $reserved1 = UtilsObj::getPOSTParam('RESERVED1', '');
        $authCode = UtilsObj::getPOSTParam('AUTHCODE', '');
        $returnInv = UtilsObj::getPOSTParam('RETURNINV', '');
        $reserved2 = UtilsObj::getPOSTParam('RESERVED2', '');
        $cardNumber = UtilsObj::getPOSTParam('CARDNUMBER', '');
        $amount = UtilsObj::getPOSTParam('AMOUNT', '');
        $thbAmount = UtilsObj::getPOSTParam('THBAMOUNT', '');
        $currency = UtilsObj::getPOSTParam('CURISO', '');
        $exRate = UtilsObj::getPOSTParam('FXRATE', '');
        $cardType = UtilsObj::getPOSTParam('FILLSPACE', '');

        $formattedPaymentDate = DatabaseObj::getServerTime();

        if ($responseCode == '00')
		{
			$authorised = true;
			$authorisedStatus = 1;
		}

        //write on logs
        PaymentIntegrationObj::logPaymentGatewayData($kasikornConfig, $formattedPaymentDate, $result);

        $formattedAmount = substr($amount, 0, strlen($amount) - $gSession['order']['currencydecimalplaces']) . '.' .
        	substr($amount, strlen($amount) - $gSession['order']['currencydecimalplaces']);

		if ($cardNumber != '')
		{
			$formattedCardNumber = substr($cardNumber, 0, 4) . ' **** **** ' . substr($cardNumber, -4);
		}
		else
		{
			$formattedCardNumber = '';
		}

		// attempt to convert the currency number to a currency code
		$currencyArray = DatabaseObj::getCurrencyFromNumber($currency);
		if ($currencyArray['result'] == '')
		{
			$currency = $currencyArray['code'];
		}

		// if the transaction has been authorised determine if the automatic callback has already been received
		if (($authorised == true) && ($kasikornConfig['REALTIMEURL'] != ''))
		{
			$dbObj = DatabaseObj::getGlobalDBConnection();
			if ($dbObj)
			{
				if ($stmt = $dbObj->prepare('SELECT ccilog.id
					FROM `CCILOG`
					JOIN `ORDERHEADER` ON (orderheader.id = ccilog.orderid)
					WHERE `transactionid` = ? ORDER BY ccilog.datecreated DESC'))
				{
					if ($stmt->bind_param('s', $returnInv))
					{
						if ($stmt->execute())
                        {
                            if ($stmt->store_result())
                            {
                                if ($stmt->num_rows > 0)
                                {
                                    if ($stmt->bind_result($logID))
            						{
        								if ($stmt->fetch())
        								{
        									$update = true;
        								}
                                        else
                                        {
                                            $error = 'Kasikorn Confirm fetch ' . $dbObj->error;
                                        }
            						}
                                    else
                                    {
                                        $error = 'Kasikorn Confirm bind result ' . $dbObj->error;
                                    }
                                }
                            }
                            else
                            {
                                $error = 'Kasikorn Confirm store result ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $error = 'Kasikorn Confirm execute ' . $dbObj->error;
                        }
					}
                    else
                    {
                        $error = 'Kasikorn Confirm bind params ' . $dbObj->error;
                    }

					$stmt->free_result();
					$stmt->close();
				}
                else
                {
                    $error = 'Kasikorn Confirm prepare ' . $dbObj->error;
                }
				$dbObj->close();
			}
		}

        $resultArray['result'] = $result;
        $resultArray['ref'] = $_GET['ref'];
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $formattedAmount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] = 0.00;
        $resultArray['transactionid'] = $returnInv;
        $resultArray['authorised'] = $authorised;
        $resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['responsecode'] = $responseCode;
        $resultArray['authorisationid'] = $authCode;
        $resultArray['bankresponsecode'] = $reserved1;
        $resultArray['cardnumber'] = $cardNumber;
        $resultArray['formattedcardnumber'] = $formattedCardNumber;
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = $reserved2;
        $resultArray['paymentdate'] = $formattedPaymentDate;
        $resultArray['paymentmeans'] = $cardType;
        $resultArray['paymenttime'] = '';
		$resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $formattedPaymentDate;
        $resultArray['formattedtransactionid'] = $returnInv;
        $resultArray['formattedauthorisationid'] = $authCode;
        $resultArray['addressstatus'] = '';
        $resultArray['payerid'] = '';
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = '';
        $resultArray['business'] = $kasikornConfig['MERCHANT2'];
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = $kasikornConfig['TERM2'];
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = $exRate;
        $resultArray['settleamount'] = $thbAmount;
        $resultArray['currencycode'] = $currency;
        $resultArray['webbrandcode'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = 0;
        $resultArray['parentlogid'] = $logID;
        $resultArray['responsedescription'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['charityflag'] = '';
        $resultArray['showerror'] = false;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();

        return $resultArray;
    }

    static function automaticCallback()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
        $update = false;

        $kasikornConfig = PaymentIntegrationObj::readCCIConfigFile('../config/kasikorn.conf',$gSession['order']['currencycode'],'');

        $authorised = false;
        $authorisedStatus = 0;
        $cardNumber = '';
        $formattedCardNumber = '';
        $logID = 0;
        $orderID = 0;
		$cavvResponseCode = '';
		$legacy = false;
		$payerEmail = '';
		$payerInternetProtocolAddress = '';
		$warningLight = '';
		$issuerBank = '';

		if (array_key_exists('PMGWRESP', $_POST))
		{
			$data = UtilsObj::getPOSTParam('PMGWRESP');
			// mark this as the legacy return system for later use
			$legacy = true;

			$responseCode = substr($data, 0, 2);
			$reserved1 = substr($data, 2, 12);
			$authCode = substr($data, 14, 6);
			$reserved2 = substr($data, 20, 36);
			$returnInv = substr($data, 56, 12);
			$timeStamp = substr($data, 68, 14);
			$amount = substr($data, 82, 12);
			$reserved3 = substr($data, 94, 40);
			$cardType = substr($data, 134, 20);
			$reserved4 = substr($data, 154, 40);
			$thbAmount = substr($data, 194, 12);
			$currency = substr($data, 206, 3);
			$exRate = substr($data, 209, 12);
		}
		else
		{
			$data = UtilsObj::getPOSTParam('PMGWRESP2');
			
			$responseCode = substr($data, 97, 2);
			$authCode = substr($data, 99, 6);
			$returnInv = substr($data, 32, 12);
			// time stamp is grabbed from 2 adjacent return values that happen to form the same order as the TIMESTAMP value from the legacy option
			$timeStamp = substr($data, 44, 14);
			$amount = substr($data, 85, 12);
			$cardType = substr($data, 105, 3);
			$thbAmount = substr($data, 188, 20);
			$currency = substr($data, 29, 3);
			$exRate = substr($data, 168, 20);
			$cavvResponseCode = substr($data, 776);
			$cvvFlag = substr($data, 81, 4);
			$cardNumber = substr($data, 58, 19);
			$payerEmail = substr($data, 208, 100);
			$payerInternetProtocolAddress = substr($data, 458, 18);
			$warningLight = substr($data, 476, 1);
			$issuerBank =substr($data, 537, 60);
			
			// convert the Kasikorn card type return code into an actual card name
			$cardType = self::cardTypeCodeConverter($cardType);
		}

        $formattedPaymentDate = substr($timeStamp, 4, 4) . '-' . substr($timeStamp, 2, 2) . '-' . substr($timeStamp, 0, 2) . ' ' . substr($timeStamp, 8, 2) .
        	':' . substr($timeStamp, 10, 2) . ':' . substr($timeStamp, 12, 2);

        if ($responseCode == '00')
		{
			$authorised = true;
			$authorisedStatus = 1;
		}

        //write on logs
        $serverDate = DatabaseObj::getServerTime();
        PaymentIntegrationObj::logPaymentGatewayData($kasikornConfig, $serverDate);

        $formattedAmount = substr($amount, 0, strlen($amount) - $gSession['order']['currencydecimalplaces']) . '.' .
        	substr($amount, strlen($amount) - $gSession['order']['currencydecimalplaces']);

		// attempt to convert the currency number to a currency code
		$currencyArray = DatabaseObj::getCurrencyFromNumber($currency);
		if ($currencyArray['result'] == '')
		{
			$currency = $currencyArray['code'];
		}

		// if the transaction has been authorised determine if the manual callback has already been received
		if ($authorised == true)
		{
			$dbObj = DatabaseObj::getGlobalDBConnection();
			if ($dbObj)
			{
				if ($stmt = $dbObj->prepare('SELECT ccilog.id, ccilog.orderid, `cardnumber`, `formattedcardnumber`
                                                FROM `CCILOG`
                                                JOIN `ORDERHEADER` ON (orderheader.id = ccilog.orderid)
                                                WHERE `transactionid` = ? ORDER BY ccilog.datecreated DESC'))
				{
					if ($stmt->bind_param('s', $returnInv))
					{
                        if ($stmt->execute())
                        {
                            if ($stmt->store_result())
                            {
                                if ($stmt->num_rows > 0)
                                {
                                    if ($stmt->bind_result($logID, $orderID, $cardNumber, $formattedCardNumber))
                                    {
                                        if ($stmt->fetch())
                                        {
                                            $update = true;
                                        }
                                    }
                                }
							}
						}
					}

					$stmt->free_result();
					$stmt->close();
				}
				$dbObj->close();
			}
		}

        $resultArray['result'] = $result;
        $resultArray['ref'] = $_GET['ref'];
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $formattedAmount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] = 0.00;
        $resultArray['transactionid'] = $returnInv;
        $resultArray['authorised'] = $authorised;
        $resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['responsecode'] = $responseCode;
        $resultArray['authorisationid'] = $authCode;
        $resultArray['cardnumber'] = $cardNumber;
        $resultArray['formattedcardnumber'] = $formattedCardNumber;
        $resultArray['paymentdate'] = $formattedPaymentDate;
        $resultArray['paymentmeans'] = $cardType;
        $resultArray['paymenttime'] = '';
		$resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $formattedPaymentDate;
        $resultArray['formattedtransactionid'] = $returnInv;
        $resultArray['formattedauthorisationid'] = $authCode;
        $resultArray['addressstatus'] = '';
        $resultArray['payerid'] = $payerInternetProtocolAddress;
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = $payerEmail;
        $resultArray['business'] = $kasikornConfig['MERCHANT2'];
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = $kasikornConfig['TERM2'];
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = $exRate;
        $resultArray['settleamount'] = $thbAmount;
        $resultArray['currencycode'] = $currency;
        $resultArray['webbrandcode'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = $orderID;
        $resultArray['parentlogid'] = $logID;
        $resultArray['responsedescription'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = $cavvResponseCode;
        $resultArray['charityflag'] = '';
        $resultArray['showerror'] = false;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
		$resultArray['bankresponsecode'] = '';
		$resultArray['paymentcertificate'] = '';
		$resultArray['cvvresponsecode'] = '';
		
		// assign the values returned from the legacy system to their places as described in our old documentation
		if ($legacy)
		{
			$resultArray['bankresponsecode'] = $reserved1;
			$resultArray['paymentcertificate'] = $reserved2;
			$resultArray['cvvflag'] = $reserved3;
		    $resultArray['cvvresponsecode'] = $reserved4;
		}
		else
		{
			$resultArray['cvvflag'] = $cvvFlag;
		}
		
        return $resultArray;
    }
	
	/**
	 * Converts the numeric card types returned from Kasikorn into their corresponding name in string form as per the K-payment documentation
	 * 
	 * @param string $pCardTypeCode The numeric code returned by Kasikorn
	 * @return string The name of the card type
	 */
	static function cardTypeCodeConverter($pCardTypeCode)
	{
		$returnCardName = '';
		
		switch ($pCardTypeCode)
		{
			case '001':
				$returnCardName = 'VISA';
				break;	
			case '002':
				$returnCardName = 'MasterCard';
				break;
			case '003':
				$returnCardName = 'KBank';
				break;
			case '004':
				$returnCardName = 'JCB';
				break;
			case '005':
				$returnCardName = 'CUP';
				break;
			case '007':
				$returnCardName = 'AMEX';
				break;
			default:
				$returnCardName = 'Unknown Card Type';
				break;
		}
		
		return $returnCardName;
	}
}

?>