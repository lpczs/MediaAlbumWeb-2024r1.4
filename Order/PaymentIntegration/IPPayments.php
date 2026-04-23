<?php

class IPPaymentsObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();
        $active = false;
        AuthenticateObj::clearSessionCCICookie();

        if ($gSession['order']['currencyisonumber'] == '036')
        {
        	$active = true;
        }

        $resultArray['active'] = $active;
        $resultArray['form'] = '';
        $resultArray['script'] = '';
        $resultArray['action'] = '';

        return $resultArray;
    }

    static function initialize()
    {
        global $gConstants;
        global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$IPPaymentsConfig = PaymentIntegrationObj::readCCIConfigFile('../config/IPPayments.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$server = $IPPaymentsConfig['IPPSERVER'];
			$custID = $gSession['userid'];
			$username = $IPPaymentsConfig['IPPUSERNAME'];
			$password = $IPPaymentsConfig['IPPPASSWORD'];

			// get language code and see if it is supported by Dotpay
			$locale = strtolower($gSession['browserlanguagecode']);
			$locale = substr($locale, 0, 2);

			$languageList = 'it,en,de,fr,es';
			if (strpos($languageList, $locale) === false)
			{
				$displayLang = 'it';
			}
			else
			{
				$displayLang = $locale;
			}

			$orderID = $gSession['ref'];
			// amount in smallest unit, e.g. pence or cents
			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
			$currency = $gSession['order']['currencyisonumber'];

                        $parameters = array(
                            'CustNumber' => $custID,
                            'CustRef' => $orderID,
                            'Amount' => $amount,
                            'SessionId' => $gSession['ref'],
                            'Username' => $username,
                            'Password' => $password
                        );

			// define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('cancel_url', $cancelReturnPath);
                        $smarty->assign('method', 'post');
                        $smarty->assign('parameter', $parameters);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to IPPAYMENTS
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

    static function automaticCallback()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $showError = true;
        $responseDescription = '';

        // initialise variables
        $ref = UtilsObj::getPOSTParam('SessionId');
        $custNumber = UtilsObj::getPOSTParam('CustNumber');
        $orderID = UtilsObj::getPOSTParam('CustRef');
        $responseCode = UtilsObj::getPOSTParam('ResponseCode');
        $timeStamp = UtilsObj::getPOSTParam('TimeStamp');
        $receipt = UtilsObj::getPOSTParam('Receipt');
        $settlementDate = UtilsObj::getPOSTParam('SettlementDate');
        $cardType = UtilsObj::getPOSTParam('CreditCardType');
        $cardNumber = UtilsObj::getPOSTParam('CreditCardNumber');
        $cardHolderName = UtilsObj::getPOSTParam('CardHolderName');
        $amount = UtilsObj::getPOSTParam('Amount');
        $surcharge = UtilsObj::getPOSTParam('Surcharge');
        $email = UtilsObj::getPOSTParam('Email');
        $transactionType = UtilsObj::getPOSTParam('TrnType');

        $IPPaymentsConfig = PaymentIntegrationObj::readCCIConfigFile('../config/IPPayments.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

		//Check to see if transaction was successful.
		if($responseCode == 0 && $receipt != '')
		{
			$showError = false;
			$authorised = true;
			$authorisedStatus = 1;
		}
		else
		{
			$responseCode = UtilsObj::getPOSTParam('DeclinedCode');
			$responseDescription = UtilsObj::getPOSTParam('DeclinedMessage');
			$showError = false;
			$authorised = false;
			$authorisedStatus = 0;

			// set a reciept so that an update to the ccilog can happen.
			$receipt = 44444444;
		}

        // write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
		PaymentIntegrationObj::logPaymentGatewayData($IPPaymentsConfig, $serverTimestamp);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = $surcharge;
        $resultArray['formattedcharges'] = $surcharge;
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $receipt;
        $resultArray['formattedtransactionid'] = $receipt;
        $resultArray['responsecode'] = $responseCode;
        $resultArray['responsedescription'] = $responseDescription;
        $resultArray['authorisationid'] = $ref;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $ref;
        $resultArray['bankresponsecode'] = $responseCode;
        $resultArray['cardnumber'] = $cardNumber;
        $resultArray['formattedcardnumber'] = $cardNumber;
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = $receipt;
        $resultArray['paymentdate'] = $settlementDate;
        $resultArray['paymentmeans'] = $cardType;
        $resultArray['paymenttime'] = '';
		$resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $settlementDate;
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = '';
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = $email;
        $resultArray['business'] = '';
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = $transactionType;
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

	static function manualCallback()
    {
    	$resultArray = Array();
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $showError = true;
        $databaseError = 'Database Error';

        // initialise variables
        $ref = UtilsObj::getPOSTParam('ref');
        $custNumber = UtilsObj::getPOSTParam('Customernumber');
        $custRef = UtilsObj::getPOSTParam('Customerref');
        $amount = UtilsObj::getPOSTParam('Amount');
        $surcharge = UtilsObj::getPOSTParam('Surcharge');

        $dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('SELECT `authorised`, `transactionid`, `authorisationid`, `payerstatus`, `responsecode`, `responsedescription` FROM `CCILOG` WHERE `sessionid` = ? ORDER BY `id` DESC'))
			{
				if ($stmt->bind_param('i', $ref))
				{
				   if ($stmt->bind_result($logAuthorised, $logTransactionID, $logControl, $logStatus, $responseCode, $error))
				   {
					   if ($stmt->execute())
					   {
							if ($stmt->fetch())
							{
								if ($responseCode == 0)
								{
									// successfull transaction
									$authorised = true;
									$showError = false;
								}
								elseif ($responseCode == 998)
								{
									//cancel callBack
									$authorised = false;
									$showError = false;
								}
								else
								{
									$authorised = false;
									$showError = true;
									$error = 'Transaction failed.';

									if (! array_key_exists('data1', $resultArray))
									{
										$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': Payment Error';
										$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $error;
										$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $ref;
										$resultArray['data4'] = '';
									}
									$resultArray['errorform'] = 'error.tpl';
								}
							}
					   }
					   else
					   {
							$error = $databaseError;
					   }
					}
					else
					{
						$error = $databaseError;
					}
				}
				else
				{
					$error = $databaseError;
				}

				$stmt->free_result();
				$stmt->close();
			}
			else
			{
				$error = $databaseError;
			}
			$dbObj->close();
		}
		else
		{
			$error = $databaseError;
		}

        $resultArray['ref'] = $ref;
    	$resultArray['authorised'] = $authorised;
    	$resultArray['showerror'] = $showError;

        return $resultArray;

    }//end function
}

?>