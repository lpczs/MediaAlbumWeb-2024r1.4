<?php

class CCICyberPlusObj
{
    static function configure()
    {
        global $gSession;

        $aResult = Array();
        $active = true;

        AuthenticateObj::clearSessionCCICookie();

        // test for Cyberplus supported currencies
        if (strpos('208,752,036,124,840,978,756,826,392,156', $gSession['order']['currencyisonumber']) === false)
        {
            $active = false;
        }

        $aResult['active'] = $active;
        $aResult['form'] = '';
        $aResult['scripturl'] = '';
        $aResult['script'] = '';
        $aResult['action'] = '';

        return $aResult;
    }

    static function initialize()
    {
        global $gConstants;
        global $gSession;

        $orderValue = '';

        $aConfig = PaymentIntegrationObj::readCCIConfigFile('../config/cyberplus.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

        $currencyCode = $gSession['order']['currencyisonumber'];

        $orderValue = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');

		$supportedLanguages = 'fr,de,it,es,en,zh,jp,pt';
		$language = substr($gSession['browserlanguagecode'], 0, 2);
		if (strpos($supportedLanguages, $language) === false)
		{
			$language = $gConstants['defaultlanguagecode'];
			if (strpos($supportedLanguages, $language) === false)
			{
				$language = 'fr';
			}
		}

		$sAmount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
		$iTimeStamp = time();
		$sRefOrder = $gSession['ref'] . 'A' . $iTimeStamp;
		//url
		$sManualReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
		$sCancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

		// generate a unique reference for the transaction
		// we only have 6 digits so the first four are the session number and the last two are a counter for the session
		$iUnique = $gSession['ref'];
		while ($iUnique > 8999)
		{
			$iUnique = $iUnique - 9000;
		}
		$iUnique = sprintf("%04d",$iUnique);

		// if this is the first time the ccidata will be an empty string so use zero
		if(!is_numeric($gSession['order']['ccidata']))
		{
			$gSession['order']['ccidata'] = 0;
		}
		else
		{
			$gSession['order']['ccidata']++;
		}
		$iUnique .= sprintf("%02d",$gSession['order']['ccidata']);

		//parameters for the form. 
        //note that the vendors has issues with regular speech marks so converted to 'curly' equivlents where special chars are allowed. 
		$aParams = array();
		$aParams['vads_site_id'] = $aConfig['MERCHANT_ID'];
		$aParams['vads_amount'] = $sAmount;
		$aParams['vads_currency'] = $currencyCode;
		$aParams['vads_ctx_mode'] = $aConfig['MODE'];
		$aParams['vads_page_action'] = "PAYMENT";
		$aParams['vads_action_mode'] = "INTERACTIVE";
		$aParams['vads_payment_config']= "SINGLE";
		$aParams['vads_version'] = "V2";
		$aParams['vads_trans_date'] = gmdate("YmdHis", $iTimeStamp);
		$aParams['vads_language'] = $language;
		$aParams['vads_user_info'] = $sRefOrder;
		$aParams['vads_url_cancel'] = $sCancelReturnPath;
		$aParams['vads_url_refused'] = $sCancelReturnPath;
		$aParams['vads_url_referral'] = $sCancelReturnPath;
		$aParams['vads_url_error'] = $sCancelReturnPath;
		$aParams['vads_url_return'] = $sCancelReturnPath;
		$aParams['vads_url_success'] = $sManualReturnPath;
		$aParams['vads_return_mode'] = 'POST';
		$aParams['vads_cust_address'] = str_replace(array('"', "'"), array('”', "’"), htmlspecialchars($gSession['order']['billingcustomeraddress1'], ENT_NOQUOTES));
		$aParams['vads_cust_country'] = preg_replace('/[^\p{N}\p{L} ]+/iu', '', $gSession['order']['billingcustomercountrycode']);
		$aParams['vads_cust_email'] = str_replace(array('"', "'"), array('”', "’"), htmlspecialchars($gSession['order']['billingcustomeremailaddress'], ENT_NOQUOTES));
		$aParams['vads_cust_name'] = str_replace(array('"', "'"), array('”', "’"), htmlspecialchars($gSession['order']['billingcontactlastname'] . ' ' . $gSession['order']['billingcontactfirstname'], ENT_NOQUOTES));
		$aParams['vads_cust_phone'] = preg_replace('/[^\p{N}\p{L} ]+/iu', '', $gSession['order']['billingcustomertelephonenumber']);
		$aParams['vads_cust_city'] = preg_replace('/[^\p{N}\p{L} ]+/iu', '', $gSession['order']['billingcustomercity']);
		$aParams['vads_cust_zip'] = preg_replace('/[^\p{N}\p{L} ]+/iu', '', $gSession['order']['billingcustomerpostcode']);

		//create transaction id
		$iTransId = sprintf("%06d",$iUnique);
		$aParams['vads_trans_id'] = $iTransId;

		// Signature creation and assign smarty variable
		ksort($aParams);
		$sSignature = "";
		$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
		foreach ($aParams as $sKey => $sValue)
		{
			$sSignature .= $sValue."+";
		}
		$sSignature .= $aConfig['CERTIFICATE']; // add certificate at the end
		$aParams['signature'] = sha1($sSignature);

        $smarty->assign('method', 'POST');
        $smarty->assign('parameter', $aParams);
        $smarty->assign('cancel_url', $sCancelReturnPath);
		$smarty->assign('payment_url', $aConfig['SERVER_NAME']);

		AuthenticateObj::defineSessionCCICookie();
		$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
		$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

		// set the ccidata to remember we have jumped to anz
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

    static function cancel()
    {
        return self::confirm();
    }

    static function automaticConfirm(){

        return self::confirm();
    }


    static function confirm()
    {
        global $gSession;

        $aResult = Array();

        $aConfig = PaymentIntegrationObj::readCCIConfigFile('../config/cyberplus.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
        //check if the signature is correct
        $sSignature = '';
        $params = $_POST;
        ksort($params);
        foreach ($params as $nom => $valeur)
        {
            if(substr($nom,0,5) == 'vads_')
            {
                    // C'est un champ utilisé pour calculer la signature
                $sSignature .= $valeur."+";
            }
        }
        $sSignature .= $aConfig['CERTIFICATE']; // add the certificate at the end
        $sSignature = sha1($sSignature);

        //config
        $iMerchantId = $aConfig['MERCHANT_ID'];

        //post parameter
        $sAmount = number_format(UtilsObj::getPOSTParam('vads_amount'), $gSession['order']['currencydecimalplaces'], '.', '');
        $iTransId = UtilsObj::getPOSTParam('vads_trans_id');
        $iTransDate = UtilsObj::getPOSTParam('vads_trans_date');
        $sResult = UtilsObj::getPOSTParam('vads_result');
        $iCertificate = UtilsObj::getPOSTParam('vads_payment_certificate');
        $iAuthId = UtilsObj::getPOSTParam('vads_auth_number');
        $iCurrency = UtilsObj::getPOSTParam('vads_currency');
        $sCard = UtilsObj::getPOSTParam('vads_card_number');
        $sAuthResult = UtilsObj::getPOSTParam('vads_auth_result');
        $sDate =  substr($iTransDate, 0, 4) . '-' . substr($iTransDate, 4, 2) . '-' . substr($iTransDate, 6, 2);

        //check signature and result after
        if( UtilsObj::getPOSTParam('signature') != $sSignature){
            $bAuthorised = false;
            $bAuthorisedStatus = 0;
            $bPaymentReceived = 0;
        } else {
            if( $sResult == '00'){
                $bAuthorised = true;
                $bAuthorisedStatus = 1;
                $bPaymentReceived = 1;
            } else {
                $bAuthorised = false;
                $bAuthorisedStatus = 0;
                $bPaymentReceived = 0;
            }
        }

        //write on logs
		$serverTimestamp = DatabaseObj::getServerTime();
        PaymentIntegrationObj::logPaymentGatewayData($aConfig, $serverTimestamp);

        $aResult['result'] = $sResult;
        $aResult['ref'] = $iTransId;
        $aResult['amount'] = $sAmount;
        $aResult['formattedamount'] = number_format($sAmount, $gSession['order']['currencydecimalplaces'], '.', '');
        $aResult['charges'] = '';
        $aResult['formattedcharges'] = 0.00;
        $aResult['authorised'] = $bAuthorised;
        $aResult['authorisedstatus'] = $bAuthorisedStatus;
        $aResult['paymentdate'] = $sDate;
        $aResult['authorisationid'] = $iAuthId;
        $aResult['bankresponsecode'] = '';
        $aResult['transactionid'] = $iTransId;
        $aResult['cvvflag'] = '';
        $aResult['cvvresponsecode'] = '';
        $aResult['business'] = $iMerchantId;
        $aResult['currencycode'] = $iCurrency;
        $aResult['webbrandcode'] = $gSession['webbrandcode'];
        $aResult['paymentcertificate'] = $iCertificate;
        $aResult['paymentmeans'] = '';
        $aResult['paymenttime'] = '';
		$aResult['paymentreceived'] = $bPaymentReceived;
        $aResult['formattedpaymentdate'] = $sDate;
        $aResult['formattedtransactionid'] = '';
        $aResult['formattedauthorisationid'] = '';
        $aResult['addressstatus'] = '';
        $aResult['payerid'] = '';
        $aResult['payerstatus'] = '';
        $aResult['payeremail'] = '';
        $aResult['cardnumber'] = $sCard;
        $aResult['formattedcardnumber'] = '';
        $aResult['receiveremail'] = '';
        $aResult['responsecode'] = $sAuthResult;
        $aResult['receiverid'] = '';
        $aResult['pendingreason'] = '';
        $aResult['transactiontype'] = '';
        $aResult['settleamount'] = '';
        $aResult['update'] = false;
        $aResult['orderid'] = $gSession['ref'];
        $aResult['parentlogid'] = 0;
        $aResult['responsedescription'] = '';
        $aResult['postcodestatus'] = '';
        $aResult['threedsecurestatus'] = '';
        $aResult['cavvresponsecode'] = '';
        $aResult['charityflag'] = '';
        $aResult['showerror'] = false;
        $aResult['resultisarray'] = false;
        $aResult['resultlist'] = Array();
		$aResult['orderdata'] = $gSession['order'];

        return $aResult;
    }
}

?>