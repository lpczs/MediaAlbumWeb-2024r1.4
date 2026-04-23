<?php
use Security\ControlCentreCSP;

class TCONObj
{
    static function configure()
    {
        global $gSession;

		$gateways = '';
        $resultArray = Array();
		$currency = $gSession['order']['currencycode'];
		$active = false;

		$smarty = SmartyObj::newSmarty('CreditCardPayment');

        AuthenticateObj::clearSessionCCICookie();

        // read config file
		$TconConfig = PaymentIntegrationObj::readCCIConfigFile('../config/TCON.conf',$currency,$gSession['webbrandcode']);
		$smarty = SmartyObj::newSmarty('CreditCardPayment');

		// if customer did enable any payment method in the list, don't display the list box
		$paymentMethodList = self::TCONPaymentMethodList();
		$paymentMethod_count = count($paymentMethodList);
		if($paymentMethod_count > 0)
		{
			$active = true;
		}

		$gatewaysArray = explode(',', $TconConfig['TCONGATEWAYS']);
		foreach ($gatewaysArray as $gateway)
		{
			$gateways += $gateway;
		}

		// make sure merchant details are set
		$currencyList = '392';
		if (($TconConfig['SHOPID'] == '') or ($TconConfig['HASHKEY'] == ''))
		{
			$active = false;
		}
		elseif (strpos($currencyList, $gSession['order']['currencyisonumber']) === false)
        {
			$active = false;
        }
		else
		{
	        $locale = strtolower($gSession['browserlanguagecode']);
			$locale = substr($locale, 0, 2);
        	$active = true;
        }

		$cspActive = true;
		$nonceValue = '[nonce]';
		$ac_config = UtilsObj::getGlobalValue('ac_config', []);

		if ((array_key_exists('CONTENTSECURITYPOLICY', $ac_config)) && ($ac_config['CONTENTSECURITYPOLICY'] === 'DISABLED'))
		{
			$cspActive = false;
		}

		if (($cspActive) && ($gSession['ismobile'] != true))
		{
			$cspBuilder = ControlCentreCSP::getInstance(UtilsObj::getGlobalValue('ac_config'));
			$nonceValue = $cspBuilder->nonce();
		}

        $resultArray['gateways'] = $gateways;
        $resultArray['active'] = $active;

        $resultArray['form'] =  "
			var paymenthod = document.getElementsByName('paymentmethods');
			for(var i=0;i < paymenthod.length; i++)
			{
				if(paymenthod[i].value=='CARD')
				{
					creditCardContainer = paymenthod[i].parentNode;
					creditCardContainer.appendChild(document.createTextNode('\u00A0\u00A0\u00A0'));
					newscript = document.createElement('script');
					newscript.type = 'text/javascript';
					" . ($cspActive ? "newscript.setAttribute('nonce', '" . $nonceValue . "');" : "") . "
					newscript.text = 'TCONDropdown();';
					creditCardContainer.appendChild(newscript);
				}
	      	}";
        $resultArray['scripturl'] = '';
        $resultArray['script'] = "

		function TCONDropdown()
		{
			var selectorOuterDiv = document.createElement('div');
            selectorOuterDiv.setAttribute('class', 'wizard-dropdown');

            var selector = document.createElement('select');
			selector.id = 'paymentgatewaycode';
			selector.name = 'paymentgatewaycode';
            selector.setAttribute('class', 'wizard-dropdown');
			selector.setAttribute('data-decorator', 'forceSelectCard');
			selector.addEventListener('change', function(event) {
				forceSelectCard();
			});

            selectorOuterDiv.appendChild(selector);
            creditCardContainer.appendChild(selectorOuterDiv);

			var option = document.createElement('option');
			option.value = '';
			option.appendChild(document.createTextNode('-- ".$smarty->get_config_vars('str_TCONPrompt')." --'));
			selector.appendChild(option);

			// Assign the array of PaymentMethodList from the config file
			TCONPayTypeArray = new Array();
			";

			for( $i = 0; $i < $paymentMethod_count; $i++)
			{
				 $resultArray['script'] .= "TCONPayTypeArray[".$i."]  = new payType('". $paymentMethodList[$i]['name']."', '".$paymentMethodList[$i]['id']."');" ;
			}

			$resultArray['script'] .="

				if (TCONPayTypeArray)
				{
					for (var i = 0; i < TCONPayTypeArray.length; i++)
					{
						var option = document.createElement('option');

						option.value = TCONPayTypeArray[i].id;
						if (option.value == '".$gSession['order']['paymentgatewaycode']."')
						{
							option.selected = 'selected';
						}
						option.appendChild(document.createTextNode(TCONPayTypeArray[i].name));
						selector.appendChild(option);

					}
				}
		}";

        $resultArray['action'] = "validatePayType('" . $smarty->get_config_vars('str_TCONPrompt') . "')";

        return $resultArray;
    }

    static function initialize()
    {
        global $gSession;

        $parameters = array();

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$TconConfig = PaymentIntegrationObj::readCCIConfigFile('../config/TCON.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

			// FOR TESTING ONLY (REMOVE AFTER COMPLETE)
  		    $successReturnPath = UtilsObj::correctPath($gSession['webbrandweburl'])."?fsaction=Order.ccManualCallback&amp;amp;ref=".$gSession['ref'];
		    $cancelReturnPath  = UtilsObj::correctPath($gSession['webbrandweburl'])."?fsaction=Order.ccCancelCallback&amp;amp;ref=".$gSession['ref'];

			$successReturnPathForKey = UtilsObj::correctPath($gSession['webbrandweburl'])."?fsaction=Order.ccManualCallback&ref=".$gSession['ref'];
			$cancelReturnPathForKey  = UtilsObj::correctPath($gSession['webbrandweburl'])."?fsaction=Order.ccCancelCallback&ref=".$gSession['ref'];

			// amount in smallest unit
			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');

			$cusid  = $gSession['order']['billingcustomeremailaddress'];
			$custmail = $gSession['order']['billingcustomeremailaddress'];
			$zipCode = $gSession['order']['billingcustomerpostcode'];

			$city = $gSession['order']['billingcustomercity'];
			$street = $gSession['order']['billingcustomeraddress1'];
			$billingName = $gSession['order']['billingcontactfirstname'].$gSession['order']['billingcontactlastname'];

			$telephone = $gSession['order']['billingcustomertelephonenumber'];
			$d_zipCode =	$gSession['shipping'][0]['shippingcustomerpostcode'];
			$d_city = $gSession['shipping'][0]['shippingcustomercity'];

			$shippingName = $gSession['shipping'][0]['shippingcontactfirstname'].$gSession['shipping'][0]['shippingcontactlastname'];
			$d_treet = $gSession['shipping'][0]['shippingcustomeraddress1'];

			$basketNumber = $gSession['ref'];
			$payCode = $gSession['order']['paymentgatewaycode'];

			$productName = LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);

			// build hash
			$key = md5($TconConfig['SHOPID'].$cusid.$custmail.$zipCode.$city.$street.$billingName.$telephone.$d_zipCode.$d_city.$d_treet.$shippingName
					.$successReturnPathForKey.$cancelReturnPathForKey.$basketNumber.$payCode.$amount.$TconConfig['HASHKEY']);

			$paydata =
			"<PAYMENT>
			<SHOPID>".$TconConfig['SHOPID']."</SHOPID>
			<CUSTID>".$cusid."</CUSTID>
			<CUSTMAIL>".$custmail."</CUSTMAIL>

			<ZIPCODE>".$zipCode."</ZIPCODE>
			<CITY>".$city."</CITY>
			<STREET>".$street."</STREET>
			<CUSTNAME>".$billingName."</CUSTNAME>
			<CUSTTEL>".$telephone."</CUSTTEL>

			<D_ZIPCODE>".$d_zipCode."</D_ZIPCODE>
			<D_CITY>".$d_city."</D_CITY>
			<D_STREET>".$d_treet."</D_STREET>
			<D_CUSTNAME>".$shippingName."</D_CUSTNAME>

			<PURCHASE>
			<GNAME>".$productName."</GNAME>
			<GPRICE>".$amount."</GPRICE>
			<GPTAX>OFF</GPTAX>
			<GCOUNT>1</GCOUNT>
			<GCTAX>OFF</GCTAX>
			</PURCHASE>

			<BACKURL>".$successReturnPath."</BACKURL>
			<CANCELURL>".$cancelReturnPath."</CANCELURL>
			<BASCKETNO>".$basketNumber."</BASCKETNO>
			<PAYCODE>".$payCode."</PAYCODE>
			<LANG>JA</LANG>
			<KEY>".$key."</KEY>
			</PAYMENT>";

			$parameters['PayData'] = $paydata;

			// define Smarty variables
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('payment_url', $TconConfig['SERVER']);
			$smarty->assign('method', 'POST');
			$smarty->assign('parameter', $parameters);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to TCON
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


    static function cancel() // is this ever being called?
    {
        $resultArray = array();
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
			if ($resultArray['bankresponsecode'] == 'PP')
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

        global $gSession;

        $resultArray = array();
        $result = '';
        $authorised = false;
		$active = true;
		$error = '';

        $ref = $_GET['ref'];

		$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

		if (empty($cciLogEntry))
		{
			// no entry yet, this must be the first callback
			// we do have a session
			$webbrandcode = $gSession['webbrandcode'];
			$currencyCode = $gSession['order']['currencycode'];
			$amount = $gSession['order']['ordertotaltopay'];
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
			$amount = $cciLogEntry['formattedamount'];
			$update = true;
			$parentLogId = $cciLogEntry['id'];
			$orderId = $cciLogEntry['orderid'];
		}

		$TconConfig = PaymentIntegrationObj::readCCIConfigFile('../config/TCON.conf',$currencyCode,$gSession['webbrandcode']);

        // read POST variables
		$custid = UtilsObj::getPOSTParam('CUSTID');
		$salesno = UtilsObj::getPOSTParam('SALESNO');
		$basketno = UtilsObj::getPOSTParam('BASKETNO');
		$payment = UtilsObj::getPOSTParam('PAYMENT');
		$paymentStatus = UtilsObj::getPOSTParam('STATUS');
		$key = UtilsObj::getPOSTParam('KEY');

		$localKey = md5($custid.$salesno.$basketno.$payment.$paymentStatus.$TconConfig['HASHKEY']);

		if ($localKey != $key)
		{
			// we have discrepancies
			$error = 'Incorrect Key.';
			$active = false;
			$authorised = false;
			$authorisedStatus = 10;
			$paymentReceived = 0;
		}

		// no errors? proceed!
		if ($error == '')
		{
			// we continue because there was no error so far
			// and we have a successful payment
			switch ($paymentStatus)
			{
				case 'PP':
					$active = true;
					$responsedesc = 'Request Accepted.';
					$authorised = true;
					$authorisedStatus = 1;
					$paymentReceived = 0;
					break;
				case 'PM':
					$active = true;
					$responsedesc = 'Authorised.';
					$authorised = true;
					$authorisedStatus = 2;
					$paymentReceived = 1;
					break;
				case 'XP':
					$active = true;
					$responsedesc = "Request Cancelled.";
					$authorised = true;
					$authorisedStatus = 3;
					$paymentReceived = 0;
					break;
				case 'XM':
					$active = true;
					$responsedesc = "Authorisation cancelled.";
					$authorised = true;
					$authorisedStatus = 4;
					$paymentReceived = 0;
					break;
				default:
					$error = 'Unexpected result.';
					$active = false;
					$responsedesc = 'Unexpected result.';
					$authorised = false;
					$authorisedStatus = 9;
					$paymentReceived = 0;
			}

			// send status tion email if
			// 1. this is an update
			// 2. the status has indeed changed
			// 3. new status is not 'paid' (otherwise we get an email on every payment)
			if ($update && ($paymentStatus != $cciLogEntry['responsecode']))
			{
				$offlineConfirmationName = $TconConfig['OFFLINECONFIRMATIONNAME'];
				$offlineConfirmationEmailAddress = $TconConfig['OFFLINECONFIRMATIONEMAILADDRESS'];
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

		// write to log file.
		$serverDate = date('Y-m-d');
		$serverTime = date('H:i:s');
		$serverTimestamp = DatabaseObj::getServerTime();
        PaymentIntegrationObj::logPaymentGatewayData($TconConfig, $serverTimestamp, $error);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '000';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $salesno;  // this is our unique ID, not the real order ID
        $resultArray['formattedtransactionid'] = $salesno;
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
        $resultArray['payerid'] = $custid;
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = '';
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
        $resultArray['resultlist'] = array();
    	$resultArray['showerror'] = false;

        return $resultArray;
    }


    static function TCONPaymentMethodList()
	{
		global $gSession;

		$smarty = SmartyObj::newSmarty('CreditCardPayment');

		$TconConfig = PaymentIntegrationObj::readCCIConfigFile('../config/TCON.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

		$enabledMethodList = explode(",", $TconConfig['TCONGATEWAYS']);

		$fullPaymentMethodList[] = array("id" => "1", "name" => $smarty->get_config_vars('str_TCONCreditCard'));
		$fullPaymentMethodList[] = array("id" => "2", "name" => $smarty->get_config_vars('str_TCONBank'));
		$fullPaymentMethodList[] = array("id" => "4", "name" => $smarty->get_config_vars('str_TCONPostalBank'));
		$fullPaymentMethodList[] = array("id" => "16", "name" => $smarty->get_config_vars('str_TCONInternetBank'));
		$fullPaymentMethodList[] = array("id" => "64", "name" => $smarty->get_config_vars('str_TCONCOD'));
		$fullPaymentMethodList[] = array("id" => "2048", "name" => $smarty->get_config_vars('str_TCONCorporateSettlement'));
		$fullPaymentMethodList[] = array("id" => "4096", "name" => $smarty->get_config_vars('str_TCONPaymentInvoice'));

		if ($gSession['order']['ordertotaltopay'] >= 400)
		{
			$fullPaymentMethodList[] = array("id" => "8192", "name" => $smarty->get_config_vars('str_TCONOnlinePaymentInvoice'));
		}

		$fullPaymentMethodList[] = array("id" => "16384", "name" => $smarty->get_config_vars('str_TCONPayEasy'));
		$fullPaymentMethodList[] = array("id" => "32768", "name" => $smarty->get_config_vars('str_TCONShoppingCredit'));
		$fullPaymentMethodList[] = array("id" => "262144", "name" => $smarty->get_config_vars('str_TCONSuicaInternetService'));

		foreach ($fullPaymentMethodList as $key => $value)
		{
			if(!in_array($fullPaymentMethodList[$key]['id'],$enabledMethodList))
			{
				unset($fullPaymentMethodList[$key]);
			}
		}

		$fullPaymentMethodList = array_values($fullPaymentMethodList);

		return $fullPaymentMethodList;
	}
}

?>