<?php
class PagosOnlineObj
{
    static function configure()
    {
        global $gSession;

		$gateways = [];
        $resultArray = Array();
		$currency = $gSession['order']['currencycode'];
		$active = false;

		$smarty = SmartyObj::newSmarty('CreditCardPayment');
        AuthenticateObj::clearSessionCCICookie();

        // read config file
		$PagosOnlineConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PagosOnline.conf',$currency,$gSession['webbrandcode']);
		$smarty = SmartyObj::newSmarty('CreditCardPayment');


		$gatewaysArray = explode(',', $PagosOnlineConfig['PAYMETHOD']);
		foreach ($gatewaysArray as $gateway)
		{
			$gateways[] = $gateway;
		}

		// currencies supported: COP, EUR, GBP, MXN, USD, VEB
		$currencyList = '170,978,826,484,840,862';

		// make sure merchant details are set
		if ($PagosOnlineConfig['USERID'] == '' || $PagosOnlineConfig['KEY'] == '')
		{
			$active = false;
		}elseif (strpos($currencyList, $gSession['order']['currencyisonumber']) === false)
        {

			$active = false;
        }else{
	        $locale = strtolower($gSession['browserlanguagecode']);
			$locale = substr($locale, 0, 2);
        	$active = true;
        }



       $resultArray['gateways'] = $gateways;
       $resultArray['active'] = $active;
       $resultArray['form'] =  "";
       $resultArray['scripturl'] = '';
	   $resultArray['script'] ="";
       $resultArray['action'] = "";

        return $resultArray;
    }

    static function initialize()
    {
        global $ac_config;
        global $gConstants;
        global $gSession;

        $parameters = Array();

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$PagosOnlineConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PagosOnline.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

  		    $responseURL = UtilsObj::correctPath($gSession['webbrandweburl'])."PaymentIntegration/Pagosonline/PagosOnlineRedirect.php";
			$automaticCallbackURL = UtilsObj::correctPath($gSession['webbrandweburl'])."PaymentIntegration/Pagosonline/PagosOnlineCallback.php";
			$cancelReturnURL  = UtilsObj::correctPath($gSession['webbrandweburl'])."?fsaction=Order.ccCancelCallback&ref=".$gSession['ref'];

			$timestamp = time();
			$key = $PagosOnlineConfig["KEY"];
			$userID = $PagosOnlineConfig["USERID"];
			$refVenta = $gSession['ref'];
			$valor = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');
			$currency = $gSession["order"]["currencycode"];
			$rawKey = "$key~$userID~$refVenta~$valor~$currency";

			$parameters['firma'] = md5($rawKey); // MD5 hash key
			$parameters['usuarioId'] = $PagosOnlineConfig['USERID']; // userid provided by PagosOnline
			$parameters['refVenta'] = $gSession['ref']; // orderID
			$parameters['descripcion'] = $gSession['ref'].'_'.$timestamp; // transaction description
			$parameters['valor'] = $valor ; // Amount
			$parameters['iva'] = 0; // VAT has been calculated on Taopix // VAT
			$parameters['baseDevolucionIva'] = 0; // VAT has been calculated on Taopix  // VAT
			$parameters['emailComprador'] = $gSession['order']['billingcustomeremailaddress']; // customer's email
			$parameters['lng'] = substr($gSession['browserlanguagecode'], 0, 2);   // language code
			$parameters['moneda'] = $gSession['order']['currencycode']; // currency code
			$parameters['nombreComprador'] = $gSession['order']['billingcontactfirstname']." ".$gSession['order']['billingcontactlastname'];
			$parameters['telefonoMovil'] = $gSession['order']['billingcustomertelephonenumber']; // billing telephone number
			$parameters['medio_pago'] = ""; // Payment method
			$parameters['url_respuesta'] = $responseURL; // manual callback
			$parameters['url_confirmacion'] = $automaticCallbackURL; // automatic callback

			if (array_key_exists('TESTMODE', $PagosOnlineConfig))
			{
				if ($PagosOnlineConfig['TESTMODE'] == 1)
				{
					$parameters['prueba'] = "1"; // 1 means test transaction
				}
			}

			// do the payment request
			$active = true;
			$error = '';

			// define Smarty variables
			$smarty->assign('cancel_url', $cancelReturnURL);
			$smarty->assign('payment_url', $PagosOnlineConfig['SERVER']);
			$smarty->assign('method', 'POST');
			$smarty->assign('parameter', $parameters);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to PagosOnline
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
        $resultArray = Array();
        $result = '';
        $resultArray['result'] = '';

        $resultArray['ref'] = $_GET['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }


    static function manualCallback()
    {
        global $gSession;

    	// all we have is the session reference
    	$ref = $gSession['ref'];

		$resultArray = PaymentIntegrationObj::getCciLogEntry($ref);

        $resultArray['result'] = '';
		$paymentReceived = 0;
		$authorised = false;

		if (!empty($resultArray))
		{
			if ($resultArray['bankresponsecode'] == '4')
			{
				$paymentReceived = 1;
				$authorised = true;
			}
		}
		$resultArray['authorised'] = $authorised;
		$resultArray['update'] = false;
        $resultArray['ref'] = $ref;
        $resultArray['showerror'] = false;
        $resultArray['paymentreceived'] = $paymentReceived;
        return $resultArray;
    }

    static function automaticCallback()
    {
		// include the email creation module
    	require_once('../Utils/UtilsEmail.php');

        global $ac_config;
        global $gSession;

        $resultArray = Array();
        $result = '';
        $authorised = false;
		$active = true;
		$error = '';
		$status = '';
		$description = '';

        $ref = $_GET['ref'];

		$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

		if (empty($cciLogEntry))
		{
			// no entry yet, this must be the first callback
			// we do have a session
			$webbrandcode = $gSession['webbrandcode'];
			$currencyCode = $gSession['order']['currencycode'];
			$amount 	  = $gSession['order']['ordertotaltopay'];
			$update = false;
			$parentLogId = -1;
			$orderId = -1;
		}
		else
		{
			// we already have an entry, this must be a status update
			// we won't have a session
			$webbrandcode = $cciLogEntry['webbrandcode'];
			$currencyCode = $cciLogEntry['currencycode'];
			$amount 	  = $cciLogEntry['formattedamount'];
			$update = true;
			$parentLogId = $cciLogEntry['id'];
			$orderId = $cciLogEntry['orderid'];
		}

		$PagosOnlineConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PagosOnline.conf',$currencyCode,$gSession['webbrandcode']);

        // read POST variables
		$llaveEncripcion = $PagosOnlineConfig['KEY'];
		$usuarioId = UtilsObj::getPOSTParam('usuario_id');
		$refVenta = UtilsObj::getPOSTParam('ref_venta');
		$valor = UtilsObj::getPOSTParam('valor');
		$moneda = UtilsObj::getPOSTParam('moneda');
		$estado_pol = UtilsObj::getPOSTParam('estado_pol');
		$paymentStatus = UtilsObj::getPOSTParam('estado_pol');
		$transaccion_id = UtilsObj::getPOSTParam('transaccion_id');
		$email_comprador = UtilsObj::getPOSTParam('email_comprador');

		// Keys to compare
		$localKey =	md5("$llaveEncripcion~$usuarioId~$refVenta~$valor~$moneda~$estado_pol");

		$remoteKey = UtilsObj::getPOSTParam('firma');
		$responsedesc = UtilsObj::getPOSTParam('mensaje_respuesta_pol');


		// no errors? proceed!
		if (strtolower($localKey) == strtolower($remoteKey))
		{
			switch ($paymentStatus)
			{
				// These code means transaction has successed or currently under validation.
				case "4":
					$active = true;
					$authorised = true;
					$paymentReceived = 1;
					break;
				case "7":
					$active = true;
					$authorised = true;
					$paymentReceived = 0;
					break;
				case "10":
					$active = true;
					$authorised = true;
					$paymentReceived = 0;
					break;
				case "11":
					$active = true;
					$authorised = false;
					$paymentReceived = 0;
					break;
				case "12":
					$active = true;
					$authorised = true;
					$paymentReceived = 0;
					break;
				case "13":
					$active = true;
					$authorised = true;
					$paymentReceived = 0;
					break;
				case "14":
					$active = true;
					$authorised = false;
					$paymentReceived = 0;
					break;
				case "15":
					$active = true;
					$authorised = false;
					$paymentReceived = 0;
					break;
				case "16":
					$active = true;
					$authorised = true;
					$paymentReceived = 0;
					break;
			}


			// send status tion email if
			// 1. this is an update
			// 2. the status has indeed changed
			// 3. new status is not 'paid' (otherwise we get an email on every payment)
			if ($update && ($paymentStatus != $cciLogEntry['responsecode']))
			{
				$offlineConfirmationName = $PagosOnlineConfig['OFFLINECONFIRMATIONNAME'];
				$offlineConfirmationEmailAddress = $PagosOnlineConfig['OFFLINECONFIRMATIONEMAILADDRESS'];
				if ($offlineConfirmationEmailAddress != '')
				{
					$smarty = SmartyObj::newSmarty('Order');
					$emailContent = $smarty->get_config_vars('str_LabelOrderNumber') . ': ' . $cciLogEntry['ordernumber'] . "\n" .
									$smarty->get_config_vars('str_LabelTransactionID') . ': ' . $cciLogEntry['transactionid'] . "\n" .
									$smarty->get_config_vars('str_LabelStatus') . ': ' . $responsedesc . "\n\n";
					$emailObj = new TaopixMailer();

					$emailObj->sendTemplateEmail('admin_offlinepaymentupdate', $webbrandcode, '', '', '',
						$offlineConfirmationName, $offlineConfirmationEmailAddress, '', '',
						0,
						Array('data' => $emailContent),
						false);
				}
			}
		}
		else
		{
     		// sig check failed
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . '';
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': Mismatched Signature';
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $ref;
			$resultArray['errorform'] = 'error.tpl';
     	}

		// write to log file.
		$serverDate = date('Y-m-d');
		$serverTime = date('H:i:s');

        // write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
		PaymentIntegrationObj::logPaymentGatewayData($PagosOnlineConfig, $serverTimestamp, $error);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '000';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $responsedesc;
        $resultArray['transactionid'] = $transaccion_id;  // this is our unique ID, not the real order ID
        $resultArray['formattedtransactionid'] = $transaccion_id;
        $resultArray['responsecode'] = $paymentStatus;
        $resultArray['responsedescription'] = $responsedesc;
        $resultArray['authorisationid'] = $ref;
        $resultArray['formattedauthorisationid'] = $ref;
        $resultArray['bankresponsecode'] = '';
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = '';
        $resultArray['paymenttime'] = $serverTime;
        $resultArray['paymentreceived'] = $paymentReceived;
        $resultArray['formattedpaymentdate'] = $serverTimestamp;
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = '';
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = $email_comprador;
        $resultArray['business'] = '';
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = '';
        $resultArray['settleamount'] = '';
        $resultArray['currencycode'] = $currencyCode;
        $resultArray['webbrandcode'] = $webbrandcode;

        $resultArray['charityflag'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = $orderId;
        $resultArray['parentlogid'] = $parentLogId;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = false;

        return $resultArray;
    }

}

?>