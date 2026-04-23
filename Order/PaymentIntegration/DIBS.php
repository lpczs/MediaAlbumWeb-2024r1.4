<?php
use Security\ControlCentreCSP;

class DIBSObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();
        $form = '';
        $script = '';
        $action = '';
        $klarnaInvoiceEnabled = false;

        AuthenticateObj::clearSessionCCICookie();

        $DIBSConfig = PaymentIntegrationObj::readCCIConfigFile('../config/DIBS.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
		$klarnaInvoiceEnabled = ($DIBSConfig['KLARNAINVOICEENABLED'] == 1) ? true : false;

		if ($klarnaInvoiceEnabled > 0)
		{
			$test = $DIBSConfig['DIBSTEST'];
			$paymentMethodList = self::dibsPaymentMethodList($test);
			$paymentMethodCount = count($paymentMethodList);

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
						newscript.text = 'DIBSDropdown();';
						creditCardContainer.appendChild(newscript);
					}
				}";

			$script = "

			function DIBSDropdown()
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
				DIBSPayTypeArray = new Array();
				";

				for($i = 0; $i < $paymentMethodCount; $i++)
				{
					 $script .= "DIBSPayTypeArray[" . $i . "]  = new payType('" . $paymentMethodList[$i]['name'] . "', '" . $paymentMethodList[$i]['id'] . "');";
				}

				$script .="

				if (DIBSPayTypeArray)
				{
					for (var i = 0; i < DIBSPayTypeArray.length; i++)
					{
						var option = document.createElement('option');
						option.value = DIBSPayTypeArray[i].id;

						if (option.value == '" . $gSession['order']['paymentgatewaycode'] . "')
						{
							option.selected = 'selected';
						}

						option.appendChild(document.createTextNode(DIBSPayTypeArray[i].name));
						selector.appendChild(option);

					}
				}
			}";

			$action = "validatePayType('Please select a payment method')";
		}

        $resultArray['active'] = true;
        $resultArray['form'] = $form;
        $resultArray['scripturl'] = '';
        $resultArray['script'] = $script;
        $resultArray['action'] = $action;

        return $resultArray;
    }

    static function initialize2()
    {
    	global $gConstants;
		global $gSession;

		$parameters = Array();

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$DIBSConfig = PaymentIntegrationObj::readCCIConfigFile('../config/DIBS.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

			$automaticReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccAutomaticCallback&ref=' . $gSession['ref'];
			$normalReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

			$server = $DIBSConfig['DIBSSERVER'];
			$vendorName = $DIBSConfig['DIBSVENDORNAME'];
			$merchant = $DIBSConfig['DIBSMERCHANTID'];
			$test = $DIBSConfig['DIBSTEST'];

			$defaultLanguage = $gConstants['defaultlanguagecode'];

			if ($DIBSConfig['CURRENCY_DECIMAL_PLACES_OVERRIDE'] == 1)
			{
				$currencyDecimalPlaces = $DIBSConfig['CURRENCY_DECIMAL_PLACES'];
			}
			else
			{
				$currencyDecimalPlaces = $gSession['order']['currencydecimalplaces'];
			}

			// build DIBS data
			$orderID = $vendorName . $gSession['ref'] . time();
			// amount in smallest unit, e.g. pence or cents
			$amount = number_format($gSession['order']['ordertotaltopay'], $currencyDecimalPlaces, '', '');
			$currency = $gSession['order']['currencyisonumber'];
			$lang = substr($gSession['browserlanguagecode'], 0, 2);

			// define Smarty variables
			$parameters['merchant'] = $merchant;
			$parameters['orderid'] = $orderID;
			$parameters['amount'] = $amount;
			$parameters['currency'] = $currency;
			$parameters['accepturl'] = $normalReturnPath;
			$parameters['callbackurl'] = $automaticReturnPath;
			$parameters['email'] = $gSession['order']['billingcustomeremailaddress'];
			$parameters['distributionType'] = 'email';
			$parameters['billingFirstName'] = $gSession['order']['billingcontactfirstname'];
			$parameters['billingLastName'] = $gSession['order']['billingcontactlastname'];
			$parameters['paytype'] = $gSession['order']['paymentgatewaycode'];

			// test for DIBS supported languages
			$supportedLanguages = array('da', 'sv', 'no', 'en', 'nl', 'de', 'fr', 'fi', 'es', 'it', 'fo', 'pl');

			if (in_array($lang, $supportedLanguages))
			{
				$parameters['lang'] = $lang;
			}
			else
			{
				if (in_array($defaultLanguage, $supportedLanguages))
				{
					$parameters['lang'] = $defaultLanguage;
				}
			}

			// build the structured order information
			$structuredOrderInfo = '<?xml version="1.0" encoding="UTF-8"?><orderInformation>';
			$structuredOrderInfo .= '<orderItem ';
			$structuredOrderInfo .= 'itemDescription="' . $gSession['items'][0]['itemqty'] . ' x ' . UtilsObj::encodeString(LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true)) . '" ';
			$structuredOrderInfo .= 'itemID="1" ';
			$structuredOrderInfo .= 'orderRowNumber="1" ';
			$structuredOrderInfo .= 'price="' . $amount . '" ';
			$structuredOrderInfo .= 'quantity="1" ';
			$structuredOrderInfo .= 'unitCode="pcs" ';
			$structuredOrderInfo .= 'VATPercent="0" />';
        	$structuredOrderInfo .= '</orderInformation>';

        	$parameters['structuredOrderInformation'] = htmlentities($structuredOrderInfo);

        	if ($test == 1)
			{
				$parameters['test'] = 'yes';
			}
			else
			{
				$parameters['createInvoiceNow'] = 'true';
			}

			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('payment_url', $server);
			$smarty->assign('method', 'POST');
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

    static function initialize()
    {
        global $gConstants;
        global $gSession;

        $parameters = Array();

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        if (($gSession['order']['paymentgatewaycode'] != '') && ($gSession['order']['paymentgatewaycode'] != 'creditcard'))
        {
        	self::initialize2();
        }
        else
        {
			// first check if we have any ccidata. this is set when the call is made the first time.
			// if the data is set then the user must have hit the back button on their browser
			if ($gSession['order']['ccidata'] == '')
			{
				$DIBSConfig = PaymentIntegrationObj::readCCIConfigFile('../config/DIBS.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

				$normalReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
				$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
				$automaticReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccAutomaticCallback&ref=' . $gSession['ref'];

				$server = $DIBSConfig['DIBSSERVER'];
				$vendorName = $DIBSConfig['DIBSVENDORNAME'];
				$merchant = $DIBSConfig['DIBSMERCHANTID'];
				$md5Key1 = $DIBSConfig['DIBSMD5KEY1'];
				$md5Key2 = $DIBSConfig['DIBSMD5KEY2'];
				$account = $DIBSConfig['DIBSACCOUNT'];
				$captureNow = $DIBSConfig['DIBSCAPTURENOW'];
				$calcFee = $DIBSConfig['DIBSCALCFEE'];
				$test = $DIBSConfig['DIBSTEST'];
				$colour = $DIBSConfig['DIBSCOLOUR'];
				$decorator = $DIBSConfig['DIBSDECORATOR'];
				$skipLastPage = $DIBSConfig['DIBSSKIPLASTPAGE'];
				$extendedOrderInformation = $DIBSConfig['DIBSEXTINFO'];

				$defaultLanguage = $gConstants['defaultlanguagecode'];
				if ($DIBSConfig['CURRENCY_DECIMAL_PLACES_OVERRIDE'] == 1)
				{
					$currencyDecimalPlaces = $DIBSConfig['CURRENCY_DECIMAL_PLACES'];
				}
				else
				{
					$currencyDecimalPlaces = $gSession['order']['currencydecimalplaces'];
				}

				// build DIBS data
				$orderID = $vendorName . $gSession['ref'] . time();
				$ip = UtilsObj::getClientIPAddress();

				// amount in smallest unit, e.g. pence or cents
				$amount = number_format($gSession['order']['ordertotaltopay'], $currencyDecimalPlaces, '', '');
				$currency = $gSession['order']['currencyisonumber'];
				$lang = substr($gSession['browserlanguagecode'], 0, 2);
				$orderText = $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);

				// define Smarty variables
				$parameters['merchant'] = $merchant;
				$parameters['amount'] = $amount;
				$parameters['currency'] = $currency;
				$parameters['orderid'] = $orderID;
				$parameters['accepturl'] = $normalReturnPath;
				$parameters['cancelurl'] = $cancelReturnPath;
				$parameters['callbackurl'] = $automaticReturnPath;
				$parameters['uniqueoid'] = 'yes';
				$parameters['ip'] = $ip;
				$parameters['wb'] = $gSession['webbrandcode']; // this is not for DIBS, but for us
				$parameters['cci'] = 'DIBS'; // this is not for DIBS, but for us

				// set the paytype to the credit card types you want to display if they are set in the
				$parameters['paytype'] = UtilsObj::getArrayParam($DIBSConfig, 'DIBSPAYTYPE', '');

				// test for DIBS supported languages
				$supportedLanguages = array('da', 'sv', 'no', 'en', 'nl', 'de', 'fr', 'fi', 'es', 'it', 'fo', 'pl');
				if (in_array($lang, $supportedLanguages))
				{
					$parameters['lang'] = $lang;
				}
				else
				{
					if (in_array($defaultLanguage, $supportedLanguages))
					{
						$parameters['lang'] = $defaultLanguage;
					}
				}

				// calculate md5key only when the two keys are present
				if (($md5Key1 != '') && ($md5Key2 != ''))
				{
					$md5Key = "merchant=" . $merchant . "&orderid=" . $orderID . "&currency=" . $currency . "&amount=" . $amount;
					$md5Key = md5($md5Key2 . md5($md5Key1 . $md5Key));

					$parameters['md5key'] = $md5Key;
				}

				if ($account != '')
				{
					$parameters['account'] = $account;
				}
				if ($captureNow == 1)
				{
					$parameters['capturenow'] = 'yes';
				}

				if ($test == 1)
				{
					$parameters['test'] = 'yes';
				}

				if ($colour != '')
				{
					$parameters['colour'] = strtolower($colour); // lower case to be on the safe side
				}

				if ($decorator != '')
				{
					$parameters['decorator'] = strtolower($decorator); // lower case to be on the safe side
				}

				if ($calcFee == 1)
				{
					$parameters['calcfee'] = 'yes';
				}

				if ($skipLastPage == 1)
				{
					$parameters['skiplastpage'] = 'yes';
				}

				if ($extendedOrderInformation == 1)
				{
					$orderText .= '<br>';
					$orderText .= UtilsObj::encodeString($gSession['order']['billingcontactfirstname'] . ' ' .
														$gSession['order']['billingcontactlastname'] . ' <' .
														$gSession['order']['billingcustomeremailaddress'] . '>',false);
				}

				$parameters['ordertext'] = $orderText;

				$smarty->assign('cancel_url', $cancelReturnPath);
				$smarty->assign('payment_url', $server);
				$smarty->assign('method', 'POST');
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

    static function confirm()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';

        // initialise variables
        $agreement = UtilsObj::getPOSTParam('agreement');
        $amount = UtilsObj::getPOSTParam('amount', '000');
        $acquirer = UtilsObj::getPOSTParam('acquirer');
        $authKey = UtilsObj::getPOSTParam('authkey');
        $cardCountry = UtilsObj::getPOSTParam('cardcountry');
        $cardNoMask = UtilsObj::getPOSTParam('cardnomask');
        $cardPrefix = UtilsObj::getPOSTParam('cardprefix');
        $currency = UtilsObj::getPOSTParam('currency');
        $fee = UtilsObj::getPOSTParam('fee', '000');
        $ip = UtilsObj::getPOSTParam('ip');
        $orderID = UtilsObj::getPOSTParam('orderid');
        $orderText = UtilsObj::getPOSTParam('ordertext');
        $payType = UtilsObj::getPOSTParam('paytype');
        $ref = UtilsObj::getPOSTParam('ref');
        $severity = UtilsObj::getPOSTParam('severity', '0');
        $suspect = UtilsObj::getPOSTParam('suspect');
        $transact = UtilsObj::getPOSTParam('transact');
        $captureNow = UtilsObj::getPOSTParam('capturenow');
        $statusCode = UtilsObj::getPOSTParam('statuscode', '0');
        $authorised = true;
        $authorisedStatus = 1;

		// add fee to amount to get total
		$total = (int)$amount + (int)$fee;

        $DIBSConfig = PaymentIntegrationObj::readCCIConfigFile('../config/DIBS.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
		$md5Key1 = $DIBSConfig['DIBSMD5KEY1'];
		$md5Key2 = $DIBSConfig['DIBSMD5KEY2'];
		$vendorName = $DIBSConfig['DIBSVENDORNAME'];

		if ($DIBSConfig['CURRENCY_DECIMAL_PLACES_OVERRIDE'] == 1)
		{
			$currencyDecimalPlaces = $DIBSConfig['CURRENCY_DECIMAL_PLACES'];
		}
		else
		{
			$currencyDecimalPlaces = $gSession['order']['currencydecimalplaces'];
		}

		// calculate md5key only when the two keys are present
		if (($md5Key1 != '') && ($md5Key2 != ''))
		{
			$md5Key = "transact=" . $transact . "&amount=" . $total . "&currency=" . $currency;
			$md5Key = md5($md5Key2 . md5($md5Key1 . $md5Key));

			// this has to be the same as $authkey, otherwise there is something wrong
			$authorised = ($authKey == $md5Key) ? true : false;
		}
		else
		{
			// if there are no md5keys in config file, (or only one)
			// there should be no authkey from the DIBS server either
			$authorised = ($authKey == '') ? true : false;
		}

		if (!$authorised)
		{
			// md5 check failed
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': MD5KEY';
    		$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': MD5 check failed';
   			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $transact;
   			$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $orderID;
    		$resultArray['errorform'] = 'error.tpl';
    		$showError = true;
    		$authorisedStatus = 0;
		}
		else
		{
			$showError = false;

			if (($payType == 'kl_invtest') || ($payType == 'kl_inv'))
			{
				if (($statusCode != '2') && ($statusCode != '5'))
				{
					$authorisedStatus = 0;
				}
			}
			else
			{
				// deal with the case that capturenow is false,
				// i.e. credit card authorisation was successful, but payment hasn't been captured yet
				$authorisedStatus = ($captureNow == '') ? 2 : 1;
			}
		}

		$formatted_payment_date = DatabaseObj::getServerTime();

        $formatted_amount = substr($amount, 0, strlen($amount) - $currencyDecimalPlaces) . '.' .
        	substr($amount, strlen($amount) - $currencyDecimalPlaces);
        $formatted_fee = substr($fee, 0, strlen($fee) - $currencyDecimalPlaces) . '.' .
        	substr($fee, strlen($fee) - $currencyDecimalPlaces);

        // assuming 4 digits are shown, and cardprefix is 6 digits long
        if ($cardNoMask != '')
		{
			if ($cardPrefix != '')
			{
				$formattedCardNumber = substr($cardPrefix, 0, 4) . ' ' . substr($cardPrefix, -2) . '** **** ' . substr($cardNoMask, -4);
			}
			else
			{
				$formattedCardNumber = '**** **** **** ' . substr($cardNoMask, -4);
			}
		}
		else
		{
			$formattedCardNumber = '';
		}

		PaymentIntegrationObj::logPaymentGatewayData($DIBSConfig, $formatted_payment_date);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $formatted_amount;
        $resultArray['charges'] = $fee;
        $resultArray['formattedcharges'] = $formatted_fee;
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $transact;
        $resultArray['formattedtransactionid'] = $transact;
        $resultArray['responsecode'] = $acquirer;
        $resultArray['responsedescription'] = $orderText;
        $resultArray['authorisationid'] = $orderID;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $orderID;
        $resultArray['bankresponsecode'] = $cardCountry;
        $resultArray['cardnumber'] = $cardNoMask;
        $resultArray['formattedcardnumber'] = $formattedCardNumber;
        $resultArray['cvvflag'] = $suspect;
        $resultArray['cvvresponsecode'] = $severity;
        $resultArray['paymentcertificate'] = $agreement;
        $resultArray['paymentdate'] = $formatted_payment_date;
        $resultArray['paymentmeans'] = $payType;
        $resultArray['paymenttime'] = '';
		$resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $formatted_payment_date;
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = $ip;
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
        $resultArray['update'] = false;
        $resultArray['orderid'] = 0;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = $showError;

        return $resultArray;
    }

    static function dibsPaymentMethodList($pTestMode)
	{
		$smarty = SmartyObj::newSmarty('CreditCardPayment');

		$klInvoiceMode = 'kl_invtest';

		if ($pTestMode == 0)
		{
			$klInvoiceMode = 'kl_inv';
		}

		$fullPaymentMethodList[] = Array("id" => "creditcard", "name" => $smarty->get_config_vars('str_OrderDibs_0'));
		$fullPaymentMethodList[] = Array("id" => $klInvoiceMode, "name" => $smarty->get_config_vars('str_OrderDibs_1'));

		return $fullPaymentMethodList;
	}

}

?>