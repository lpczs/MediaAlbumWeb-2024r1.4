<?php

class mPPObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();
		$currency = $gSession['order']['currencycode'];

        AuthenticateObj::clearSessionCCICookie();

        // test for SSL module loaded, and if currency is Taiwanese Dollar
        $resultArray['active'] = ($currency == 'TWD' && PaymentIntegrationObj::checkSSL()) ? true : false;

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
			$mPPConfig = PaymentIntegrationObj::readCCIConfigFile('../config/mPP.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

			$server = $mPPConfig['MPPSERVER'];
			$vendorName = $mPPConfig['MPPVENDORNAME'];

			$merchantNumber = $mPPConfig['MPPMERCHANTNUMBER'];
			$merchantCode = $mPPConfig['MPPCODE'];
			$approveFlag = $mPPConfig['MPPAPPROVEFLAG'];
			$depositFlag = $mPPConfig['MPPDEPOSITFLAG'];

			$automaticReturnPath = UtilsObj::correctPath($gSession['webbrandwebroot']) . 'PaymentIntegration/mPP/mPPFeedback.php';
			$manualReturnPath = UtilsObj::correctPath($gSession['webbrandwebroot']) . 'PaymentIntegration/mPP/mPPReceive.php';
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

			// amount with two decimal places
			$amount = number_format($gSession['order']['ordertotaltopay'], 2, '.', '');

			// order number has to be unique, even within a session.
			// If a payment fails we need to send a new number.
			// First 2 digits is minutes plus 10 (because ordernumber mustn't start with a zero)
			// followed by session reference

			$today = getdate();
			$minutes = $today['minutes'] + 10;
			$orderNumber = $minutes . $gSession['ref'];

			// build OrgOrderNumber, make sure it is not longer than 20 characters
			$orgOrderNumber = $vendorName . $gSession['ref'] . time();
			if (strlen($orgOrderNumber) > 20)
			{
				$orgOrderNumber = substr($orgOrderNumber, 0, 20);
			}

            if ($gSession['shipping'][0]['shippingcustomerstate'] != '')
            {
				$state = $gSession['shipping'][0]['shippingcustomerstate'];
            }
            else
            {
				$state = $gSession['shipping'][0]['shippingcustomercounty'];
            }

			// see if we use English
			$englishMode = (substr($gSession['browserlanguagecode'], 0, 2) == 'en') ? 1 : 0;

			// build checksum
			$checksum = md5($merchantNumber.$orderNumber.$merchantCode.$amount);

			// Store parameters in an array
			$parameters = array(
				'MerchantNumber' => $merchantNumber,
				'OrderNumber' => $orderNumber,
				'Amount' => $amount,
				'OrgOrderNumber' => $orgOrderNumber,
				'ApproveFlag' => $approveFlag,
				'DepositFlag' => $depositFlag,
				'Englishmode' => $englishMode,
				'checksum' => $checksum,
				'op' => 'AcceptPayment',
				'OrderURL' => $automaticReturnPath, // feedback
				'ReturnURL' => $manualReturnPath
			);

			// define Smarty variables
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('payment_url', $server);
			$smarty->assign('method', 'POST');
			$smarty->assign('parameter', $parameters);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to mPP
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
        $resultArray = array();
        $resultArray['result'] = '';
        $resultArray['ref'] = $_GET['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

	// one function for both automatic and manual callback
    static function automaticCallback()
    {
    	// decide whether it's feedback or receive
    	if (isset($_POST['final_result']))
    	{
    		// receive
    		$resultArray = self::receiveCallback();
    	}
    	else
    	{
    		// feedback
    		$resultArray = self::feedbackCallback();
    	}

    	return $resultArray;
    }

	// this gets called when feedback.php gets called
    static function feedbackCallback()
    {
        global $gSession;

        // read POST data
		$PRC = UtilsObj::getPOSTParam('PRC');
		$SRC = UtilsObj::getPOSTParam('SRC');
		$approvalCode = UtilsObj::getPOSTParam('ApprovalCode');
		$bankResponseCode = UtilsObj::getPOSTParam('BankResponseCode');
		$orderNumber = UtilsObj::getPOSTParam('OrderNumber');
		$checksum = UtilsObj::getPOSTParam('CheckSum');

        $resultArray = Array();
        $result = '';
        $authorised = false;

        $ref = $_GET['ref'];

		$mPPConfig = PaymentIntegrationObj::readCCIConfigFile('../config/mPP.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

		$vendorName = $mPPConfig['MPPVENDORNAME'];

		$merchantNumber = $mPPConfig['MPPMERCHANTNUMBER'];
		$merchantCode = $mPPConfig['MPPCODE'];


		// amount with two decimal places
		$amount = number_format($gSession['order']['ordertotaltopay'], 2, '.', '');

		if ($gSession['shipping'][0]['shippingcustomerstate'] != '')
		{
			$state = $gSession['shipping'][0]['shippingcustomerstate'];
		}
		else
		{
			$state = $gSession['shipping'][0]['shippingcustomercounty'];
		}

		$description = $gSession['items'][0]['itemqty'] . ' x ' . $gSession['items'][0]['itemproductname'];

		// is it the same as what we have sent?
		// check MerchantNumber, OrderNumber and Amount
		if ($merchantNumber == UtilsObj::getPOSTParam('MerchantNumber') &&
			$amount == UtilsObj::getPOSTParam('Amount'))
		{
			// test checksum
			$checkStr = $merchantNumber . $orderNumber . $PRC . $SRC . $merchantCode . $amount;
			$checkStr = md5($checkStr);

			if ($checkStr == $checksum)
			{
				if ($PRC == 0 && $SRC == 0 )
				{
					$authorised = true; // pending confirmation by manual callback
					$authorisedStatus = 2;
				}
				else
				{
					$authorisedStatus = 3; // failure
				}
			}
			else
			{
				$authorisedStatus = 4; // checksum error
			}
		}
		else
		{
			$authorisedStatus = 5; // data doesn't match
		}
		$showError = false;

        $payerId = $gSession['order']['useripaddress'];

        // write to log file.
		$serverTime = DatabaseObj::getServerTime();
		PaymentIntegrationObj::logPaymentGatewayData($mPPConfig, $serverTime);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '000';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $orderNumber; // minutes + session ID, is displayed on the payment page as 'Order Number'
        $resultArray['formattedtransactionid'] = $orderNumber;
        $resultArray['responsecode'] = "$PRC-$SRC"; // made up, so we can compare values in the manual callback
        $resultArray['responsedescription'] = $description;
        $resultArray['authorisationid'] = $approvalCode;
        $resultArray['formattedauthorisationid'] = '';
        $resultArray['bankresponsecode'] = $bankResponseCode;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $serverTime;
        $resultArray['paymentmeans'] = '';
        $resultArray['paymenttime'] = '';
        $resultArray['paymentreceived'] = 0;
        $resultArray['formattedpaymentdate'] = $serverTime;
        $resultArray['addressstatus'] = '';
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
		$resultArray['nextstage'] = '';
        $resultArray['update'] = false;
        $resultArray['orderid'] = 0;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = $showError;

        return $resultArray;
    }

	// this gets called when receive.php gets called
    static function receiveCallback()
	{
        global $gSession;

        $resultArray = Array();
        $callbackList = Array();
        $resultArray['showerror'] = false;
		$resultArray['resultisarray'] = false;
		$resultArray['resultlist'] = Array();
		$resultArray['update'] = true;
        $result = '';
		$authorised = false;
		$error = '';
		$description = $gSession['items'][0]['itemqty'] . ' x ' . $gSession['items'][0]['itemproductname'];


		$PRC = UtilsObj::getPOSTParam('final_return_PRC');
		$SRC = UtilsObj::getPOSTParam('final_return_SRC');
		$ECI = UtilsObj::getPOSTParam('final_return_ECI');
		$finalResult = UtilsObj::getPOSTParam('final_result');
		$approvalCode = UtilsObj::getPOSTParam('final_return_ApproveCode');
		$bankResponseCode = UtilsObj::getPOSTParam('final_return_BankRC');
		$orderNumber = UtilsObj::getPOSTParam('P_OrderNumber');
		$checksum = UtilsObj::getPOSTParam('P_CheckSum');

        $ref = $_GET['ref'];

		$mPPConfig = PaymentIntegrationObj::readCCIConfigFile('../config/mPP.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

		$vendorName = $mPPConfig['MPPVENDORNAME'];

		$merchantNumber = $mPPConfig['MPPMERCHANTNUMBER'];
		$merchantCode = $mPPConfig['MPPCODE'];

		// amount with two decimal places
		$amount = number_format($gSession['order']['ordertotaltopay'], 2, '.', '');

		if ($PRC == 0 && $SRC == 0 && $bankResponseCode == 0)
		{
			// everything ok so far
		}
		else
		{
			$authorisedStatus = 3; // failure
			$error = "<b>PRC</b>: $PRC, <b>SRC</b>: $SRC, <b>Bank Response Code</b>: $bankResponseCode";
		}

		if ($error == '')
		{
		// retrieve data from ccilog
		// we need this to be able to compare these fields:
		// PRC, SRC, BankResponseCode, ApprovalCode
		// ccilog[responsecode] = "PRC-SRC"
		// ccilog[bankresponsecode] = "bankResponseCode"
		// ccilog[authorisationid] = "approvalCode"
		// the other data we will need later

			$dbObj = DatabaseObj::getGlobalDBConnection();
			if ($dbObj)
			{
				// get the last log entry with this transaction id
				if ($stmt = $dbObj->prepare('SELECT `addressstatus`, `amount`, `authorisationid`, `authorised`,
					`bankresponsecode`, `business`, `cardnumber`, `cavvresponsecode`, `charges`, `charityflag`,
					 ccilog.currencycode, ccilog.webbrandcode, `cvvflag`, `cvvresponsecode`, ccilog.datecreated, `formattedamount`,
					`formattedauthorisationid`, `formattedcardnumber`, `formattedcharges`, `formattedpaymentdate`,
					`formattedtransactionid`, ccilog.id, `mode`, `orderid`, `parentlogid`, `payeremail`, `payerid`,
					`payerstatus`, `paymentcertificate`, `paymentdate`, `paymentmeans`, `paymenttime`,
					`pendingreason`, `postcodestatus`, `receiveremail`, `receiverid`, `responsecode`,
					`responsedescription`, ccilog.sessionid, `settleamount`, `threedsecurestatus`, `transactionid`,
					`transactiontype`, `type`, ccilog.userid, `ordernumber`
					FROM `ccilog`
					LEFT JOIN `orderheader` ON (orderheader.id = ccilog.orderid)
					WHERE `transactionid` = ? ORDER BY datecreated DESC'))
				{
					if ($stmt->bind_param('s', $orderNumber))
					{
						if ($stmt->execute())
						{
							if ($stmt->store_result())
							{
								if ($stmt->num_rows > 0)
								{
									if ($stmt->bind_result($dbAddressStatus, $dbAmount, $dbAuthorisationId, $dbAuthorised,
										$dbBankResponseCode, $dbBusiness, $dbCardNumber, $dbCavvResponseCode,
										$dbCharges, $dbCharityFlag, $dbCurrencyCode, $dbWebBrandCode, $dbCvvFlag, $dbCvvResponseCode,
										$dbDateCreated, $dbFormattedAmount, $dbFormattedAuthorisationId,
										$dbFormattedCardNumber, $dbFormattedCharges, $dbFormattedPaymentDate,
										$dbFormattedTransactionId, $dbId, $dbMode, $dbOrderId, $dbParentLogId,
										$dbPayerEmail, $dbPayerId, $dbPayerStatus, $dbPaymentCertificate,
										$dbPaymentDate, $dbPaymentMeans, $dbPaymentTime, $dbPendingReason,
										$dbPostcodeStatus, $dbReceiverEmail, $dbReceiverId, $dbResponseCode,
										$dbResponseDescription, $dbSessionId, $dbSettleAmount, $dbThreedSecureStatus,
										$dbTransactionId, $dbTransactionType, $dbType, $dbUserId, $dbOrderNumber))
									{
										if ($stmt->fetch())
										{
											$description = $dbResponseDescription;
										}
										else
										{
											// No entry, something is wrong
											$authorisedStatus = 6; // no record of transaction
											$error = 'mPP ReceiveCallback fetch ' . $dbObj->error;
										}
									}
									else
									{
										$error = 'mPP ReceiveCallback bind result ' . $dbObj->error;
									}
								}
								else
								{
									$authorisedStatus = 6; // no record of transaction
									$error = 'mPP ReceiveCallback num row ' . $dbObj->error;
								}
							}
							else
							{
								$error = 'mPP ReceiveCallback store result ' . $dbObj->error;
							}
						}
						else
						{
							$error = 'mPP ReceiveCallback execute ' . $dbObj->error;
						}
					}
					else
					{
						$error = 'mPP ReceiveCallback bind params ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
                    $stmt = null;
				}
				else
				{
					$error = 'mPP ReceiveCallback prepare ' . $dbObj->error;
				}

				$dbObj->close();
			}
		}

        if ($error == '')
		{
			// does the POSTed data match the saved data?
			if ($PRC.'-'.$SRC == $dbResponseCode &&
				$bankResponseCode == $dbBankResponseCode &&
				$approvalCode == $dbAuthorisationId)	// what about amount?
			{
				// see if final_result is 1 - success  or 0 - failure
				if ($finalResult == 0)
				{
					// some error, probably cancelled
					$authorisedStatus = 7; // probably cancelled
					$error = 'Payment not confirmed.';
				}
			}
			else
			{
				$authorisedStatus = 5; // data match error
				$error = 'Data from 2nd response does not match 1st response.';
			}
		}

        if ($error == '')
		{
			// test checksum
			$checkStr = $merchantNumber . $orderNumber . $finalResult . $PRC . $merchantCode . $PRC . $amount;
			$checkStr = md5($checkStr);
			if ($checkStr == $checksum)
			{
				$authorised = true;
				$authorisedStatus = 1; // payment confirmed
			}
			else
			{
				$authorisedStatus = 4; // checksum error
				$error = 'Checksum error.';
			}
		}

		// now that we have the status, print log if that is required
		$serverTime = DatabaseObj::getServerTime();

        $logFilePath = $mPPConfig['LOGFILEPATH'];
        $logOutput = $mPPConfig['LOGOUTPUT'];

        if (($logOutput == 1) && ($logFilePath != ''))
        {
            $errString = "callback type: manual (receive)\n serverTime: $serverTime\n POST data\n";
            foreach ($_POST as $key => $value)
            {
                $errString .= "  $key: $value\n";
            }
            $errString .="order data\n description: $description\n session ref: $ref\n  "
                    . "web brand: {$gSession['webbrandcode']}\n authorisedStatus: $authorisedStatus\n "
                            . "---------------------------------------------------\n";

            PaymentIntegrationObj::logPaymentGatewayData($mPPConfig, $serverTime, $errString);
        }

        if ($error == '')
        {
			$resultArray['result'] = $result;
			$resultArray['ref'] = $ref;
			$resultArray['amount'] = $amount;
			$resultArray['formattedamount'] = $amount;
			$resultArray['charges'] = '000';
			$resultArray['formattedcharges'] = '';
			$resultArray['authorised'] = $authorised;
			$resultArray['authorisedstatus'] = $authorisedStatus;
			$resultArray['transactionid'] = $orderNumber; // minutes + session ID, is displayed on the payment page as 'Order Number'
			$resultArray['formattedtransactionid'] = $orderNumber;
			$resultArray['responsecode'] = "$PRC-$SRC"; // made up, so we can compare values in the manual callback
			$resultArray['responsedescription'] = $dbResponseDescription;
			$resultArray['authorisationid'] = $approvalCode;
			$resultArray['formattedauthorisationid'] = '';
			$resultArray['bankresponsecode'] = $bankResponseCode;
			$resultArray['cardnumber'] = '';
			$resultArray['formattedcardnumber'] = '';
			$resultArray['cvvflag'] = '';
			$resultArray['cvvresponsecode'] = '';
			$resultArray['paymentcertificate'] = '';
			$resultArray['paymentdate'] = $serverTime;
			$resultArray['paymentmeans'] = '';
			$resultArray['paymenttime'] = '';
			$resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
			$resultArray['formattedpaymentdate'] = $serverTime;
			$resultArray['addressstatus'] = '';
			$resultArray['postcodestatus'] = '';
			$resultArray['payerid'] = $dbPayerId;
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
			$resultArray['orderdata'] = $gSession['order'];

			$resultArray['charityflag'] = '';
			$resultArray['threedsecurestatus'] = $ECI;
			$resultArray['cavvresponsecode'] = '';
			$resultArray['update'] = true;
			$resultArray['orderid'] = $dbOrderId;
			$resultArray['parentlogid'] = $dbId;
			$resultArray['resultisarray'] = false;
			$resultArray['resultlist'] = Array();
			$resultArray['nextstage'] = 'complete';
        }
        else
        {
        	// the response was not validated so we just return an empty list of transactions back to the server
    		$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $error;
   			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $orderNumber;
   			$resultArray['data3'] = $description;
   			$resultArray['data4'] = '';
    		$resultArray['errorform'] = 'error.tpl';
			$resultArray['ref'] = $ref;
			$resultArray['authorised'] = false;
			$resultArray['update'] = true;
			$resultArray['nextstage'] = '';
        	$resultArray['resultisarray'] = true;
			$resultArray['resultlist'] = $callbackList;
	        $resultArray['showerror'] = true;
        }

        return $resultArray;
    }

}

?>