<?php

class AnzObj
{

    static function configure()
    {
    	global $gSession;

        $aResult = Array();
        $active = true;

        AuthenticateObj::clearSessionCCICookie();

        // test for ANZ supported currencies
        if ($gSession['order']['currencyisonumber'] != '036')
        {
            $active = false;
        }

        $aResult['active'] = $active;
        $aResult['form'] = '';
        $aResult['script'] = '';
        $aResult['scripturl'] = '';
        $aResult['action'] = '';

        return $aResult;
    }

    static function initialize()
    {
        global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        // first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if($gSession['order']['ccidata'] == '')
        {
            $aAnzConfig = PaymentIntegrationObj::readCCIConfigFile('../config/anz.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
            //url
            $sManualReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
            $sCancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
            //config localisation
            $sLocale = strtolower($gSession['browserlanguagecode']);

            // amount in smallest unit, e.g. pence or cents
            $sAmount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
            $sRefOrder = $gSession['ref'] . '_' . time();

            //createHash
            $aParam = array();
            $aParam['vpc_Version'] = 1;
            $aParam['vpc_Command'] = 'pay';
            $aParam['vpc_AccessCode'] = $aAnzConfig['ACCESS_CODE'];
            $aParam['vpc_MerchTxnRef'] = $sRefOrder;
            $aParam['vpc_Merchant'] = $aAnzConfig['MERCHANT_ID'];
            $aParam['vpc_OrderInfo'] = $sRefOrder;
            $aParam['vpc_Amount'] = $sAmount;
            $aParam['vpc_Locale'] = $sLocale;
            $aParam['vpc_ReturnURL'] = $sManualReturnPath;

            ksort($aParam);
			
            $sSecretHash = $aAnzConfig['SECRET_HASH'];

			$aParam['vpc_SecureHash'] = self::generateSecureHash($aParam, $sSecretHash);
			$aParam['vpc_SecureHashType'] = 'SHA256';

            $smarty->assign('parameter', $aParam);

            // define Smarty variables
            $smarty->assign('payment_url', $aAnzConfig['SERVER_NAME']);
            $smarty->assign('sSecretHash', strtoupper($sSecretHash));
            $smarty->assign('method', 'post');

            AuthenticateObj::defineSessionCCICookie();
            $smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
            $smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

            // set the ccidata to remember we have jumped to anz
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

            $sCancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
            $smarty->assign('payment_url', $sCancelReturnPath);
            $smarty->assign('cancel_url', $sCancelReturnPath);

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

    static function confirm()
    {
        global $gSession;

        $aResult = Array();
        $bAuthorised = false;

        $iDateTransaction = DatabaseObj::getServerTime();
        $aAnzConfig = PaymentIntegrationObj::readCCIConfigFile('../config/anz.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
        $sSecretHash = $aAnzConfig['SECRET_HASH'];
        $vpc_Txn_Secure_Hash = UtilsObj::getGETParam('vpc_SecureHash');
        $vpc_TxnResponseCode = UtilsObj::getGETParam('vpc_TxnResponseCode');
        unset($_GET['vpc_SecureHash']);
        unset($_GET['vpc_SecureHashType']);
        unset($_GET['ref']);
        unset($_GET['fsaction']);

        if (strlen($sSecretHash) > 0 &&  $vpc_TxnResponseCode != "7" && $vpc_TxnResponseCode != "No Value Returned") {
			$parametersArray = $_GET;
			ksort($parametersArray);

			$fieldsToEncrypt = array();

            foreach($parametersArray as $sKey => $sValue)
            {
				// only use anz/user parameters as part of the secure hash
				if (((substr($sKey, 0, 4) == "vpc_") || (substr($sKey, 0, 5) == "user_")))
				{
					$fieldsToEncrypt[$sKey] = $sValue;
				}
            }

			$secureHash = self::generateSecureHash($fieldsToEncrypt, $sSecretHash);

            if(($vpc_TxnResponseCode == '0') && (strtoupper($vpc_Txn_Secure_Hash) == $secureHash))
            {
                $bAuthorised = true;
                $bAuthorisedStatus = 1;
                $bPaymentReceived = 1;
            } else {
                $bAuthorised = false;
                $bAuthorisedStatus = 0;
                $bPaymentReceived = 0;
            }
        } else {
            $bAuthorised = false;
            $bAuthorisedStatus = 0;
            $bPaymentReceived = 0;
        }

        // Standard Receipt Data
        $sAmount = UtilsObj::getGETParam('vpc_Amount');
        $iBatchNo = UtilsObj::getGETParam('vpc_BatchNo');
        $sCommand = UtilsObj::getGETParam('vpc_Command');
        $sMessage = UtilsObj::getGETParam('vpc_Message');
        $sCardType = UtilsObj::getGETParam('vpc_Card');
        $orderInfo = UtilsObj::getGETParam('vpc_OrderInfo');
        $iReceiptNo = UtilsObj::getGETParam('vpc_ReceiptNo');
        $iMerchantId = UtilsObj::getGETParam('vpc_Merchant');
        $iAuthorizeId = UtilsObj::getGETParam('vpc_AuthorizeId');
        $sTransactionNo = UtilsObj::getGETParam('vpc_TransactionNo');
        $sAcqResponseCode = UtilsObj::getGETParam('vpc_AcqResponseCode');
        $sReference = UtilsObj::getGETParam('vpc_MerchTxnRef');
        $aReference = explode('_', $sReference);

        // write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
		PaymentIntegrationObj::logPaymentGatewayData($aAnzConfig, $serverTimestamp);

        //store data
        $aResult['authorised'] = $bAuthorised;
        $aResult['authorisedstatus'] = $bAuthorisedStatus;
        $aResult['result'] = $vpc_TxnResponseCode;
        $aResult['ref'] = $aReference[0];
        $aResult['amount'] = $sAmount;
        $aResult['formattedamount'] = number_format($sAmount, $gSession['order']['currencydecimalplaces'], '.', '');
        $aResult['charges'] = '';
        $aResult['formattedcharges'] = 0.00;
        $aResult['paymentdate'] = $iDateTransaction;
        $aResult['paymenttime'] = '';
        $aResult['authorisationid'] = $iAuthorizeId;
        $aResult['transactionid'] = $sTransactionNo;
        $aResult['paymentmeans'] = '';
        $aResult['addressstatus'] = '';
        $aResult['payerid'] = '';
        $aResult['payerstatus'] = '';
        $aResult['payeremail'] = '';
        $aResult['business'] = $iMerchantId;
        $aResult['receiveremail'] = '';
        $aResult['receiverid'] = $iReceiptNo;
        $aResult['pendingreason'] = '';
        $aResult['transactiontype'] = $sCommand;
        $aResult['currencycode'] = $gSession['order']['currencycode'];
        $aResult['webbrandcode'] = $gSession['webbrandcode'];
        $aResult['settleamount'] = '';
        $aResult['paymentreceived'] = $bPaymentReceived;
        $aResult['formattedpaymentdate'] = $iBatchNo;
        $aResult['formattedtransactionid'] = '';
        $aResult['formattedauthorisationid'] = '';
        $aResult['cardnumber'] = $sCardType;
        $aResult['formattedcardnumber'] = '';
        $aResult['cvvflag'] = '';
        $aResult['cvvresponsecode'] = $sAcqResponseCode;
        $aResult['responsecode'] = $vpc_TxnResponseCode;
        $aResult['bankresponsecode'] = $vpc_TxnResponseCode;
        $aResult['paymentcertificate'] = '';
        $aResult['update'] = false;
        $aResult['orderid'] = $orderInfo;
        $aResult['parentlogid'] = 0;
        $aResult['responsedescription'] = $sMessage;
        $aResult['postcodestatus'] = '';
        $aResult['threedsecurestatus'] = '';
        $aResult['cavvresponsecode'] = '';
        $aResult['charityflag'] = '';
        $aResult['showerror'] = false;
        $aResult['resultisarray'] = false;
        $aResult['resultlist'] = Array();

        return $aResult;
    }

	static function generateSecureHash($pParamArray, $pSecretHash)
	{
		$stringToHash = '';
		foreach($pParamArray as $sKey => $sValue)
		{
			// leave out any fields that have no value
			if (strlen($sValue) > 0)
			{
				$stringToHash .= $sKey . '=' . $sValue . '&';
			}
		}
		$stringToHash = rtrim($stringToHash, '&');

		return strtoupper(hash_hmac('SHA256', $stringToHash, pack("H*", $pSecretHash)));
	}
}

?>