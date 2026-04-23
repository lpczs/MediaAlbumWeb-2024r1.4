<?php
use Security\ControlCentreCSP;

class DecidirObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();
		$active = false;
		$form = '';
		$script = '';
		$action = '';
		$currency = $gSession['order']['currencycode'];

        AuthenticateObj::clearSessionCCICookie();

		// test for DECIDIR supported currencies which is Argentinian Pesos
        $active = ($currency == 'ARS') ? true : false;

		$paymentMethodList = self::decidirPaymentMethodList();
		$paymentMethodCount = count($paymentMethodList);

		if(($paymentMethodCount > 0) && ($active))
		{

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

			$form = "
				var paymenthod = document.getElementsByName('paymentmethods');

				for(var i = 0; i < paymenthod.length; i++)
				{
					if(paymenthod[i].value == 'CARD')
					{
						creditCardContainer = paymenthod[i].parentNode;
						creditCardContainer.appendChild(document.createTextNode('\u00A0\u00A0\u00A0'));
						newscript = document.createElement('script');
						newscript.type = 'text/javascript';
						" . ($cspActive ? "newscript.setAttribute('nonce', '" . $nonceValue . "');" : "") . "
						newscript.text = 'DECIDIRDropdown();';
						creditCardContainer.appendChild(newscript);
					}
				}";

			$script = "

			function DECIDIRDropdown()
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
				option.appendChild(document.createTextNode('-- ". SmartyObj::getParamValue('CreditCardPayment', 'str_DropDownPleaseSelectAPaymentType') ." --'));
				selector.appendChild(option);

				// Assign the array of PaymentMethodList from the config file
				DECIDIRPayTypeArray = new Array();
				";

				for($i = 0; $i < $paymentMethodCount; $i++)
				{
					 $script .= "DECIDIRPayTypeArray[" . $i . "]  = new payType('" . $paymentMethodList[$i]['name'] . "', '" . $paymentMethodList[$i]['id'] . "');";
				}

				$script .="

				if (DECIDIRPayTypeArray)
				{
					for (var i = 0; i < DECIDIRPayTypeArray.length; i++)
					{
						var option = document.createElement('option');
						option.value = DECIDIRPayTypeArray[i].id;

						if (option.value == '" . $gSession['order']['paymentgatewaycode'] . "')
						{
							option.selected = 'selected';
						}

						option.appendChild(document.createTextNode(DECIDIRPayTypeArray[i].name));
						selector.appendChild(option);

					}
				}
			}";

			$action = "validatePayType('Please select a payment method')";
		}
		else
		{
			$active = false;
		}

		$resultArray['active'] = $active;
        $resultArray['form'] = $form;
        $resultArray['scripturl'] = '';
        $resultArray['script'] = $script;
        $resultArray['action'] = $action;

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
			$decidirConfig = PaymentIntegrationObj::readCCIConfigFile('../config/decidir.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$automaticReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccAutomaticCallback&ref=' . $gSession['ref'];

			$server = $decidirConfig['DECIDIRSERVER'];
			$shopNumber = $decidirConfig['SHOPNUMBER'];
			$orderID = $gSession['ref'] . '_'. time();

			//initialise payment parameters
			// amount in smallest unit, e.g. pence or cents
			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');

			// all of the payment parameters are passed as an array
			$parameters = array(
				'NROCOMERCIO' => $shopNumber,
				'NROOPERACION' => $orderID,
				'MEDIODEPAGO' => $gSession['order']['paymentgatewaycode'],
				'MONTO' => $amount,
				'CUOTAS' => 01,
				'URLDINAMICA' => $automaticReturnPath,
				'EMAILCLIENTE' => $gSession['order']['billingcustomeremailaddress'],
				'PARAMSITIO' => $gSession['ref']
			);

			// define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('parameter', $parameters);
			$smarty->assign('method', "POST");

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to DECIDIR
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

        $resultArray = array();
        $resultArray['result'] = '';
        $resultArray['ref'] = $gSession['ref'];
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

        $resultArray = Array();
        $resultArray['result'] = '';
		$paymentReceived = 0;
		$authorised = false;
		$resultArray = PaymentIntegrationObj::getCciLogEntry($ref);

		if ($resultArray['bankresponsecode'] == 'APROBADA')
		{
			$paymentReceived = 1;
			$authorised = true;
		}

        $resultArray['ref'] = $ref;
        $resultArray['showerror'] = false;
		$resultArray['authorised'] = $authorised;
        $resultArray['paymentreceived'] = $paymentReceived;

        return $resultArray;
    }

   	static function automaticCallback()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $showError = false;

		$ref = UtilsObj::getPOSTParam('paramsitio');
		$authcode = UtilsObj::getPOSTParam('codautorizacion');
		$cardType = UtilsObj::getPOSTParam('tarjeta');
		$result = UtilsObj::getPOSTParam('resultado');
		$transactionNo = UtilsObj::getPOSTParam('noperacion');
		$amount = UtilsObj::getPOSTParam('monto');

		if ($result == 'APROBADA')
		{
			$authorised = true;
			$authorisedStatus = 1;
		}

		$serverDate = date('Y-m-d');
		$serverTime =  date("H:i:s");

		$decidirConfig = PaymentIntegrationObj::readCCIConfigFile('../config/decidir.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
		PaymentIntegrationObj::logPaymentGatewayData($decidirConfig, $serverTime);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '000';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $transactionNo;
        $resultArray['formattedtransactionid'] = $transactionNo;
        $resultArray['responsecode'] = '';
        $resultArray['responsedescription'] = '';
        $resultArray['authorisationid'] = $authcode;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $authcode;
        $resultArray['bankresponsecode'] = $result;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = $authcode;
        $resultArray['paymentmeans'] = $cardType;
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymenttime'] = $serverTime;
        $resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $serverDate;
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = '';
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = '';
        $resultArray['business'] = '';
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = $cardType;
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

	static function decidirPaymentMethodList()
	{
		global $gSession;

		$decidirConfig = PaymentIntegrationObj::readCCIConfigFile('../config/decidir.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

		$enabledMethodList = explode(",", $decidirConfig['PAYMENTMETHODS']);

		$fullPaymentMethodList[] = Array("id" => "1", "name" => 'VISA');
		$fullPaymentMethodList[] = Array("id" => "6", "name" => 'AMEX');
		$fullPaymentMethodList[] = Array("id" => "8", "name" => 'DINERS');
		$fullPaymentMethodList[] = Array("id" => "15", "name" => 'MASTERCARD');
		$fullPaymentMethodList[] = Array("id" => "23", "name" => 'SHOPPING');
		$fullPaymentMethodList[] = Array("id" => "24", "name" => 'NARANJA');
		$fullPaymentMethodList[] = Array("id" => "25", "name" => 'PAGO FACIL');
		$fullPaymentMethodList[] = Array("id" => "26", "name" => 'RAPI PAGO');
		$fullPaymentMethodList[] = Array("id" => "27", "name" => 'CABAL');
		$fullPaymentMethodList[] = Array("id" => "29", "name" => 'ITALCRED');
		$fullPaymentMethodList[] = Array("id" => "31", "name" => 'VISA DEBITO');
		$fullPaymentMethodList[] = Array("id" => "34", "name" => 'COOPEPLUS');
		$fullPaymentMethodList[] = Array("id" => "36", "name" => 'ARCASH');
		$fullPaymentMethodList[] = Array("id" => "37", "name" => 'NEXO');
		$fullPaymentMethodList[] = Array("id" => "38", "name" => 'CREDIMAS');
		$fullPaymentMethodList[] = Array("id" => "39", "name" => 'NEVADA');
		$fullPaymentMethodList[] = Array("id" => "41", "name" => 'PAGOMISCUENTAS');
		$fullPaymentMethodList[] = Array("id" => "42", "name" => 'NATIVA');
		$fullPaymentMethodList[] = Array("id" => "43", "name" => 'MAS');
		$fullPaymentMethodList[] = Array("id" => "45", "name" => 'NACION PYMES');

		foreach($fullPaymentMethodList as $key => $value)
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