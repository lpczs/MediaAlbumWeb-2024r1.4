<?php

class CIMObj
{
    static function configure()
    {
        global $gSession;
        $resultArray = Array();
        $active = false;

        AuthenticateObj::clearSessionCCICookie();

        // CIM Italia only supports EUROS (EUR)
        if ($gSession['order']['currencyisonumber'] == 978)
        {
            $active = true;
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
        global $gConstants;
        global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$CIMConfig = PaymentIntegrationObj::readCCIConfigFile('../config/CIMITALIA.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
			$returnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
			// cancel URL has a dummy parameter appended as CIM append their own parameters too.
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref']. '&dummy=dummy';

			$server = $CIMConfig['CIMSERVER'];
			$alias = $CIMConfig['CIMALIAS'];
			$secretKey = $CIMConfig['CIMSECRETKEY'];

			$orderID = $gSession['ref'] . '_' . time();
			$sessionId = $gSession['ref'];

			// Order data
			$orderDataRaw = $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);

			// amount in smallest unit, e.g. pence or cents
			// no decimal separator
			$amount = number_format($gSession['order']['ordertotaltopay'], 2, '', '');
			$currency = $gSession['order']['currencycode'];

			$custEmail = $gSession['order']['billingcustomeremailaddress'];

			// specify the language that will be used on the payment page.
			$locale = strtolower($gSession['browserlanguagecode']);
			$locale = substr($locale, 0, 2);

			$mac = sha1('codTrans='.$orderID.'divisa='.$currency.'importo='.$amount.$secretKey);

			$languageList = 'en,de,it,fr,es,jp';
			if (strpos($languageList, $locale) === false)
			{
				$displayLang = 'ITA-ENG';
			}
			else
			{
				switch ($locale)
				{
					case 'it':
						$displayLang = 'ITA';
					break;
					case 'en':
						$displayLang = 'ENG';
					break;
					case 'es':
						$displayLang = 'SPA';
					break;
					case 'fr':
						$displayLang = 'FRA';
					break;
					case 'de':
						$displayLang = 'GER';
					break;
					case 'jp':
						$displayLang = 'JPN';
					break;
					default:
						$displayLang = 'ITA-ENG';
					break;
				}
			}


                        $parameters = array(
                            'alias' => $alias,
                            'importo' => $amount,
                            'divisa' => $currency,
                            'codTrans' => $orderID,
                            'mail' => $custEmail,
                            'url' => $returnPath,
                            'session_id' => $sessionId,
                            'url_back' => $cancelReturnPath,
                            'languageId' => $displayLang,
                            'mac' => $mac
                        );

			// define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('method', 'post');
			$smarty->assign('parameter', $parameters);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to DIBS
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
        $resultArray['ref'] = $gSession['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function confirm($callBack)
    {
     	global $gSession;

     	$resultArray = Array();
        $result = '';
		$authorisedStatus = 0;
        $authorised = false;
        $update = false;
        $showError = false;
		$databaseError = 'Database Error';

		$ref = UtilsObj::getGETParam('session_id','0');
		if ($ref == '0')
		{
			$ref = UtilsObj::getPOSTParam('session_id','0');
		}
		$_GET['ref'] = $ref;

		$gSession = AuthenticateObj::getCurrentSessionData();


		$serverTimestamp = DatabaseObj::getServerTime();

   		$CIMConfig = PaymentIntegrationObj::readCCIConfigFile('../config/CIMITALIA.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
		$alias = $CIMConfig['CIMALIAS'];
		$secretKey = $CIMConfig['CIMSECRETKEY'];
        $ipAddress = $gSession['order']['useripaddress'];

   		if ($callBack == 'manual')
   		{
			// initialize return variables.
        	$amount = UtilsObj::getGETParam('importo');
        	$date = UtilsObj::getGETParam('data');
        	$currency = UtilsObj::getGETParam('divisa');
        	$ref = UtilsObj::getGETParam('session_id');
        	$orderID = UtilsObj::getGETParam('codTrans');
        	$time = UtilsObj::getGETParam('orario');
        	$success = UtilsObj::getGETParam('esito');
        	$transactionID = UtilsObj::getGETParam('codAut');
        	$cardType = UtilsObj::getGETParam('$BRAND');
        	$cardholderFName = UtilsObj::getGETParam('nome');
        	$cardholderSName = UtilsObj::getGETParam('cognome');
        	$email = UtilsObj::getGETParam('email');
        	$returnHash = UtilsObj::getGETParam('mac');
        	$mac = sha1("codTrans=".$orderID."esito=".$success."importo=".$amount."divisa=".$currency."data=".$date."orario=".$time."codAut=$transactionID".$secretKey);
        }
        else
        {
        	// initialize return variables.
        	$amount = UtilsObj::getPOSTParam('importo');
        	$date = UtilsObj::getPOSTParam('data');
        	$currency = UtilsObj::getPOSTParam('divisa');
        	$ref = UtilsObj::getPOSTParam('session_id');
        	$orderID = UtilsObj::getPOSTParam('codTrans');
        	$time = UtilsObj::getPOSTParam('orario');
        	$success = UtilsObj::getPOSTParam('esito');
        	$transactionID = UtilsObj::getPOSTParam('codAut');
        	$cardType = UtilsObj::getPOSTParam('$BRAND');
        	$cardholderFName = UtilsObj::getPOSTParam('nome');
        	$cardholderSName = UtilsObj::getPOSTParam('cognome');
        	$email = UtilsObj::getPOSTParam('email');
        	$returnHash = UtilsObj::getPOSTParam('mac');
        	$mac = sha1("codTrans=".$orderID."esito=".$success."importo=".$amount."divisa=".$currency."data=".$date."orario=".$time."codAut=$transactionID".$secretKey);
        }

		// this has to be the same as $returnHash, otherwise there is something wrong
		if (urldecode($mac) === urldecode($returnHash))
		{
			// check to see if the payment was susccessful.
			if ($success == 'OK')
			{
				$authorised = true;
				$authorisedStatus = 1;
			}
			else
			{
				$authorised = false;
				$authorisedStatus = 0;
				$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $orderID;
				$resultArray['data2'] = '';
				$resultArray['data3'] = '';
				$resultArray['data4'] = '';
				$resultArray['errorform'] = 'error.tpl';
				$showError = true;
			}
		}
		else
		{
			// md5 check failed
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': MD5KEY';
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': MD5 check failed';
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $transactionID;
			$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $orderID;
			$resultArray['errorform'] = 'error.tpl';
			$showError = true;
		}

		// write to a log file.
		PaymentIntegrationObj::logPaymentGatewayData($CIMConfig, $serverTimestamp);

		$resultArray['result'] = $success;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $gSession['order']['ordertotaltopay'];
        $resultArray['formattedamount'] = $gSession['order']['ordertotaltopay'];
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $transactionID;
        $resultArray['formattedtransactionid'] = $transactionID;
        $resultArray['responsecode'] = $success;
        $resultArray['responsedescription'] = '';
        $resultArray['authorisationid'] = $ref;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $ref;
        $resultArray['bankresponsecode'] = $success;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $serverTimestamp;
        $resultArray['paymentmeans'] = $cardType;
        $resultArray['paymenttime'] = '';
		$resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $serverTimestamp;
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = $ipAddress;
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = '';
        $resultArray['business'] = $alias;
        $resultArray['receiveremail'] = $email;
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

}

?>