<?php

class UniCreditObj
{

    static function configure()
    {
        global $gSession;

        $resultArray = Array();

        $currency = $gSession['order']['currencycode'];
        $active = false;

        $smarty = SmartyObj::newSmarty('CreditCardPayment');
        AuthenticateObj::clearSessionCCICookie();

        // Read config file
        $UniCreditConfig = PaymentIntegrationObj::readCCIConfigFile('../config/UniCredit.conf', $currency, $gSession['webbrandcode']);

        // EURO (978)
        if ($UniCreditConfig['CURRENCY'] == $gSession['order']['currencyisonumber'])
        {
            $active = true;
        }

        $resultArray['active'] = $active;
        $resultArray['form'] = "";
        $resultArray['script'] = "";
        $resultArray['action'] = "";

        return $resultArray;
    }

    static function initialize()
    {
        global $gSession;
        global $gConstants;

        $parameters = Array();

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
        $returnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

        // Read config file
        $UniCreditConfig = PaymentIntegrationObj::readCCIConfigFile('../config/UniCredit.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

        // Optional order reference prefix
        $orderRefPrefix = '';
        if ($UniCreditConfig['ORDERREFPREFIX'] != '')
        {
            $orderRefPrefix = $UniCreditConfig['ORDERREFPREFIX'] . '_';
        }

        if ($gSession['order']['ccidata'] == '')
        {

            // Read from config file
            $payment_url = $UniCreditConfig['SERVER'];
            $merchantID = $UniCreditConfig['MERCHANTID'];
            $userID = $UniCreditConfig['USERID'];
            $password = $UniCreditConfig['PASSWORD'];
            $salespoint = $UniCreditConfig['SALESPOINTID'];
            $stringaSegreta = $UniCreditConfig['SECRETKEY'];
            $description = $UniCreditConfig['DESCRIPTION'];
            $languageCode = $UniCreditConfig['LANGUAGE'];

            // Setup parameters to pass in an array
            $parameters = array(
                "numeroCommerciante" => trim($merchantID),
                "stabilimento" => trim($salespoint),
                "userID" => trim($userID),
                "password" => trim($password),
                "numeroOrdine" => $orderRefPrefix . $gSession['ref'] . '_' . time(),
                "totaleOrdine" => number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', ''),
                "valuta" => trim($gSession['order']['currencyisonumber']),
                "flagRiciclaOrdine" => 'N',
                "flagDeposito" => 'Y',
                "tipoRispostaApv" => 'click',
                "urlOk" => trim($returnPath),
                "urlKo" => trim($cancelReturnPath),
                // Optional parameters
                "emailNotifica" => $gSession['order']['billingcustomeremailaddress'],
                "langCompratore" => $gConstants['defaultlanguagecode']
            );

            // Option decription of the transaction
            if ($description != '')
            {
                $parameters['causalePagamento'] = $description;
            }

            // Overwrite language code if one is set
            if ($languageCode != '')
            {
                $parameters['langCompratore'] = $languageCode;
            }

            // Generate MAC code
            $mac = UniCreditObj::generateMAC($parameters, $stringaSegreta);
            $parameters['mac'] = (trim($mac));

            // Remove password so it isn't passed via POST
            $parameters['password'] = '';

            // Define Smarty parameters
            $smarty->assign('method', 'GET');
            $smarty->assign('parameter', $parameters);
            $smarty->assign('cancel_url', $cancelReturnPath);
            $smarty->assign('payment_url', $payment_url);

            AuthenticateObj::defineSessionCCICookie();
            $smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
            $smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

            // Set the ccidata to remember we have jumped to UniCredit
            $gSession['order']['ccidata'] = 'start';
            DatabaseObj::updateSession();

            $smarty->cachePage = true; // Allow the page to be cached so that the browser back button works correctly
        } else
        {
            // the user has clicked the back button
            AuthenticateObj::clearSessionCCICookie();

            $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
            $smarty->assign('payment_url', $cancelReturnPath);
            $smarty->assign('cancel_url', $cancelReturnPath);
        }

        // Display template
        if ($gSession['ismobile'] == true)
        {
            $resultArray['template'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest_small.tpl');
            $resultArray['javascript'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest.tpl');
            return $resultArray;
        } else
        {
            $smarty->displayLocale('order/PaymentIntegration/PaymentRequest_large.tpl');
        }
    }

    static function generateMAC($parameters, $stringaSegreta)
    {
        $inputMac = '';
        foreach ($parameters as $key => $value)
        {
            $inputMac .= '&' . $key . '=' . $value;
        }

        $inputMac .= '&' . trim($stringaSegreta);

        $inputMac = substr($inputMac, 1);
        $MAC = md5($inputMac);
        $MACtemp = '';

        for ($i = 0; $i < strlen($MAC); $i = $i + 2)
        {
            $MACtemp .= chr(hexdec(substr($MAC, $i, 2)));
        }
        $MAC = $MACtemp;

        $MACcode = base64_encode($MAC);
        return $MACcode;
    }

    static function cancel()
    {
        global $gSession;

        $resultArray = Array();
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
        $error = '';
        $showError = false;
        $update = false;
        $authorisedStatus = 0;
        $result = '';
        $numeroOrdine = 0;

        $amount = $gSession['order']['ordertotaltopay'];
        $currency = $gSession['order']['currencycode'];
        $webBrandCode = $gSession['webbrandcode'];

        // Read config file
        $UniCreditConfig = PaymentIntegrationObj::readCCIConfigFile('../config/UniCredit.conf', $currency, $gSession['webbrandcode']);
        $stringaSegreta = $UniCreditConfig['SECRETKEY'];

        // Get UniCredit params
        if ($callBack == 'automatic')
        {
            // Get MAC
            $mac = UtilsObj::getGETParam('mac');

            // Get status code
            $esito = UtilsObj::getGETParam('statoattuale');

            // Number order
            $numeroOrdine = UtilsObj::getGETParam('numeroOrdine');

            // Ref
            $ref = UtilsObj::getGETParam('ref');

            // Reuse parameters from GET
            $parameters = $_GET;
            // Unset these so they don't cause teh MAC to fail
            unset($parameters['mac']);
            unset($parameters['MAC']);
            unset($parameters['ref']);
        }

        if ($callBack == 'manual')
        {
            $numeroCommerciante = UtilsObj::getPOSTParam('numeroCommerciante'); // Merchant ID
            $stabilimento = UtilsObj::getPOSTParam('stabilimento'); // Sales Point ID
            $esito = UtilsObj::getPOSTParam('esito'); // Status code
            $numeroOrdine = UtilsObj::getPOSTParam('numeroOrdine'); // Order ID
            $dataApprovazione = UtilsObj::getPOSTParam('dataApprovazione'); // Date approved
            $mac = UtilsObj::getPOSTParam('mac'); // MAC code
            // Our parameters
            $ref = UtilsObj::getPOSTParam('ref');

            $parameters = array(
                'numeroOrdine' => $numeroOrdine,
                'numeroCommerciante' => $numeroCommerciante,
                'stabilimento' => $stabilimento,
                'esito' => $esito,
                'dataApprovazione' => $dataApprovazione,
            );
        }

        // Geneate MAC code
        $generatedMac = UniCreditObj::generateMAC($parameters, $stringaSegreta);

        // Check it matches
        if ($mac != $generatedMac)
        {
            $error = 'Data mismatch.';
            if ($callBack == 'manual')
            {
                $showError = true;
                $resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': MAC';
                $resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $error;
                $resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $numeroOrdine;
                $resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $numeroOrdine;
                $resultArray['errorform'] = 'error.tpl';
            }
        }

        // Payment status
        $authorised = false;
        switch ($esito)
        {
            case 'OK':
            case 'IC':
                $authorised = true;
                $authorisedStatus = 1;
                break;

            case 'KO':
                $authorised = false;
                break;
        }

        // Result data
        // Write to log file.
        $serverTimestamp = DatabaseObj::getServerTime();
        PaymentIntegrationObj::logPaymentGatewayData($UniCreditConfig, $serverTimestamp);

        $serverDate = date('Y-m-d');
        $serverTime = date("H:i:s");

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] = '';
        $resultArray['authorised'] = $authorised;
        $resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $numeroOrdine;
        $resultArray['formattedtransactionid'] = $numeroOrdine;
        $resultArray['responsecode'] = $esito;
        $resultArray['responsedescription'] = '';
        $resultArray['authorisationid'] = $numeroOrdine;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $numeroOrdine;
        $resultArray['bankresponsecode'] = $esito;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = '';
        $resultArray['paymenttime'] = $serverTime;
        $resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $serverTimestamp;
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
        $resultArray['currencycode'] = $currency;
        $resultArray['webbrandcode'] = $webBrandCode;

        $resultArray['charityflag'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = $numeroOrdine;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
        $resultArray['showerror'] = $showError;

        return $resultArray;
    }

}
