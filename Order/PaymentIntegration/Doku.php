<?php

class DokuObj
{
    static function configure()
    {
        global $gSession;

        $aResult = Array();
        $active = true;

        AuthenticateObj::clearSessionCCICookie();

        if ($gSession['order']['currencyisonumber'] != '360')
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
        global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        // first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '') {
            $aDokuConfig = PaymentIntegrationObj::readCCIConfigFile('../config/doku.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
            $sAutoReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccAutomaticCallback&ref=' . $gSession['ref'];
            $sCancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

            // amount in smallest unit, e.g. pence or cents
            $amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');

            // build transaction id
            $orderRef = $gSession['ref'] . '_' . time();
            $currency = $gSession['order']['currencyisonumber'];

            // get language code and see if it is supported by doku
            $sLocale = strtolower($gSession['browserlanguagecode']);
            $sLocale = substr($sLocale, 0, 2);
            switch ($sLocale)
			{
                case 'en':
                    $lang = 'E'; // english
                    break;
                default:
                    $lang = 'E'; // default to english
            }

            $sDescription = $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
            $parameters = array(
                'TYPE' => 'IMMEDIATE',
                'MERCHANTID' => $aDokuConfig['MID'],
                'CHAINNUM' => 'NA',
                'TRANSIDMERCHANT' => $orderRef,
                'AMOUNT' => $amount,
                'CURRENCY' => $currency,
                'PurchaseCurrency' => $currency,
                'acquirerBIN' => $aDokuConfig['ACQUIRER_BIN'],
                'password' => $aDokuConfig['PASSWORD'],
                'URL' => UtilsObj::correctPath($gSession['webbrandweburl']) . 'PaymentIntegration/Doku/',
                'MALLID' => $aDokuConfig['MALL_ID'],
                'SESSIONID' => $orderRef,
                'BASKET' => $sDescription . ',' . $amount . ',1,' . $amount,
                'WORDS' => sha1($amount . $aDokuConfig['MID'] . $aDokuConfig['SHARED_KEY'] . $orderRef),
                'orderRef' => $orderRef
            );

            // define Smarty variables
            $smarty->assign('payment_url', $aDokuConfig['SERVER_NAME']);
            $smarty->assign('cancel_url', $sCancelReturnPath);
            $smarty->assign('method', 'post');
            $smarty->assign('parameters', $parameters);

            AuthenticateObj::defineSessionCCICookie();
            $smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
            $smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

            // set the ccidata to remember we have jumped to doku
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
        } else {
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

    static function cancel()
    {
        $aRef = explode( '_', $_GET['ref']);
        $aResult = Array();
        $aResult['result'] = '';
        $aResult['ref'] = $aRef[0];
        $aResult['transactionid'] = '';
        $aResult['authorised'] = false;
        $aResult['showerror'] = false;

        return $aResult;
    }

    static function confirm()
    {
        global $gSession;

        $aResult = Array();
        $bAuthorised = false;

        $iDateTransaction = DatabaseObj::getServerTime();
        $sReference = UtilsObj::getGETParam('ref');
        $sStatus = UtilsObj::getGETParam('RESULT');
        $sMessage = UtilsObj::getGETParam('RESULTMSG');
        $iResponseCode = UtilsObj::getGETParam('RESPONSECODE');
        $sCardNumber = UtilsObj::getGETParam('CARDNUMBER');
        $aDokuConfig = PaymentIntegrationObj::readCCIConfigFile('../config/doku.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

        if ($sStatus != 'Fail') {
            $bAuthorised = true;
            $bAuthorisedStatus = 1;
            $bPaymentReceived = 1;
        } else {
            $bAuthorised = false;
            $bAuthorisedStatus = 0;
            $bPaymentReceived = 0;
        }

        // write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
        $logsData['authorised'] = $bAuthorised;
        $logsData['authorisedstatus'] = $bAuthorisedStatus;
        $logsData['paymentreceived'] = $bPaymentReceived;
        $logsData['server'] = $aDokuConfig['SERVER_NAME'];
		PaymentIntegrationObj::logPaymentGatewayData($aDokuConfig, $serverTimestamp, '', $logsData);

        $aResult['authorised'] = $bAuthorised;
        $aResult['authorisedstatus'] = $bAuthorisedStatus;
        $aResult['result'] = '';
        $aResult['ref'] = $sReference;
        $aResult['amount'] = '';
        $aResult['formattedamount'] = '';
        $aResult['charges'] = '';
        $aResult['formattedcharges'] = 0.00;
        $aResult['paymentdate'] = $iDateTransaction;
        $aResult['paymenttime'] = '';
        $aResult['authorisationid'] = '';
        $aResult['transactionid'] = $sReference;
        $aResult['paymentmeans'] = '';
        $aResult['addressstatus'] = '';
        $aResult['payerid'] = '';
        $aResult['payerstatus'] = '';
        $aResult['payeremail'] = '';
        $aResult['business'] = $aDokuConfig['MID'];
        $aResult['receiveremail'] = '';
        $aResult['receiverid'] = '';
        $aResult['pendingreason'] = '';
        $aResult['transactiontype'] = '';
        $aResult['currencycode'] = $gSession['order']['currencycode'];
        $aResult['webbrandcode'] = $gSession['webbrandcode'];
        $aResult['settleamount'] = '';
        $aResult['paymentreceived'] = $bPaymentReceived;
        $aResult['formattedpaymentdate'] = '';
        $aResult['formattedtransactionid'] = '';
        $aResult['formattedauthorisationid'] = '';
        $aResult['cardnumber'] = $sCardNumber;
        $aResult['formattedcardnumber'] = '';
        $aResult['cvvflag'] = '';
        $aResult['cvvresponsecode'] = '';
        $aResult['responsecode'] = $sStatus;
        $aResult['bankresponsecode'] = $iResponseCode;
        $aResult['paymentcertificate'] = '';
        $aResult['update'] = false;
        $aResult['orderid'] = 0;
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

}

?>