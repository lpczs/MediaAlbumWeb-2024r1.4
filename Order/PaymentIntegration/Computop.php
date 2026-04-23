<?php
use Security\ControlCentreCSP;

class ComputopObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();

        $currency = $gSession['order']['currencycode'];
        $active = false;

        $smarty = SmartyObj::newSmarty('CreditCardPayment');
        AuthenticateObj::clearSessionCCICookie();

        // read config file
        $ComputopConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Computop.conf', $currency, $gSession['webbrandcode']);

		// build a list of all supported countries
		$countriesBank = explode(',', $ComputopConfig['COUNTRIESBANK']);
		$countriesCard = explode(',', $ComputopConfig['COUNTRIESCARD']);
		$countriesPaypal = explode(',', $ComputopConfig['COUNTRIESPAYPAL']);
		$supportedCountries = array_merge($countriesBank, $countriesCard, $countriesPaypal);
		
		// remove empty array values in case there are no countires defined
		$supportedCountries = array_filter($supportedCountries);

		$supportedCountriesLength = count($supportedCountries);
		$defaultPaymentType = $ComputopConfig['DEFAULTPAYMENTTYPE'];

		// build a list of supported currencies
		$currenciesBank = explode(',', $ComputopConfig['CURRENCIESBANK']);
		$currenciesCard = explode(',', $ComputopConfig['CURRENCIESCARD']);
		$currenciesPaypal = explode(',', $ComputopConfig['CURRENCIESPAYPAL']);

		// only merge bank and card payments, PayPal is checked separately
		$supportedCurrencies = array_merge($currenciesBank, $currenciesCard);

		// check currency is supported
        if ((in_array($gSession['order']['currencyisonumber'], $supportedCurrencies)) && ((in_array($gSession['order']['billingcustomercountrycode'], $supportedCountries)) || $supportedCountriesLength == 0))
        {
            $active = true;
        }

		if (((($supportedCountriesLength == 0)) && ($defaultPaymentType == '')))
		{
			$active = false;
		}
		else
		{
			switch($defaultPaymentType)
			{
				case 'bank':
					if (!in_array($gSession['order']['currencyisonumber'], $currenciesBank))
					{
						$active = false;
					}
					break;
				case 'card':
					if (!in_array($gSession['order']['currencyisonumber'], $currenciesCard))
					{
						$active = false;
					}
					break;
				case 'paypal':
					if (!in_array($gSession['order']['currencyisonumber'], $currenciesPaypal))
					{
						$active = false;
					}
					break;
			}
		}

		if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off')
		{
			$active = false;
		}

		if (($ComputopConfig['ENABLEPAYPAL'] == 0) && (in_array($gSession['order']['billingcustomercountrycode'], $countriesPaypal)) || ($defaultPaymentType == 'paypal'))
        {
            $active = false;
        }
		else if (($ComputopConfig['ENABLEPAYPAL'] == 1) && ((!in_array($gSession['order']['currencyisonumber'], $currenciesPaypal))))
		{
			$active = false;
		}
		
		$form = '';
		$script = '';
		$action = '';

		$countryCode = $gSession['order']['billingcustomercountrycode'];
		$transID = $gSession['ref'] . '_' . time();
		$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
		$currency = $gSession['order']['currencycode'];
		$defaultPaymentType = $ComputopConfig['DEFAULTPAYMENTTYPE'];

		// build supported countries lists
		$countriesBank = explode(',', $ComputopConfig['COUNTRIESBANK']);
		$countriesCard = explode(',', $ComputopConfig['COUNTRIESCARD']);
		$countriesPaypal = explode(',', $ComputopConfig['COUNTRIESPAYPAL']);
		$supportedCountries = array_merge($countriesBank, $countriesCard, $countriesPaypal);

		if (((in_array($countryCode, $countriesBank)) && (in_array($countryCode, $countriesCard))) || (in_array($countryCode, $countriesPaypal)))
		{
			$merchantID = $ComputopConfig['MERCHANTID'];
			$password = $ComputopConfig['PASSWORD'];
			$bankListURL = $ComputopConfig['BANKLISTURL'];

			$enc = self::encryptData($password, array('MerchantID' => $merchantID));
			$banks = self::getBanks($merchantID, $bankListURL, $enc['data'], $enc['length'], $password);

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
				if (paymenthod[i].value == 'CARD')
				{
					creditCardContainer = paymenthod[i].parentNode;
					creditCardContainer.appendChild(document.createTextNode('\u00A0\u00A0\u00A0'));
					newscript = document.createElement('script');
					newscript.type = 'text/javascript';
					" . ($cspActive ? "newscript.setAttribute('nonce', '" . $nonceValue ."');" : "") . "
					newscript.text = 'COMPUTOPPaymentDropdown(); COMPUTOPBankDropdown();';
					creditCardContainer.appendChild(newscript);
					paymenthod[i].parentNode.getElementsByTagName('label')[0].innerHTML = '" . SmartyObj::getParamValue('', 'str_LabelPaymentMethod') . "';
				}
			};";

			$script = "
				function forceSelectPaymentMethod()
				{
					var select = document.getElementById('paymentgatewaymethod');
					var selected = select.options[select.selectedIndex].value;

					if (selected == 'bank')
					{
						document.getElementById('paymentgatewaycode').style.display = 'block';
					}
					else
					{
						document.getElementById('paymentgatewaycode').style.display = 'none';
						document.getElementById('paymentgatewaycode').options[0].selected = 'selected';
					}

				}

				function COMPUTOPPaymentDropdown()
				{
					var selectorOuterDiv = document.createElement('div');
					selectorOuterDiv.setAttribute('class', 'wizard-dropdown');

					var selector = document.createElement('select');
					selector.id = 'paymentgatewaymethod';
					selector.name = 'paymentgatewaymethod';
					selector.setAttribute('class', 'wizard-dropdown');
					selector.addEventListener('change', function(event) {
						forceSelectCard();
					});

					selectorOuterDiv.appendChild(selector);
					creditCardContainer.appendChild(selectorOuterDiv);

					var option = document.createElement('option');
					option.value = '';
					option.appendChild(document.createTextNode('-- "  . SmartyObj::getParamValue('Computop', 'str_ComputopSelectPaymentMethod') . " --'));
					selector.appendChild(option);

					// assign the array of paymentmethodlist from the config file
					COMPUTOPBankTypeArray = new Array();";

					if (((in_array($countryCode, $countriesBank)) && (in_array($gSession['order']['currencyisonumber'], $currenciesBank))) || ($supportedCountriesLength == 0 && $defaultPaymentType == 'bank'))
					{
						$script .= "COMPUTOPBankTypeArray.push({id: 'bank', name: '" .  SmartyObj::getParamValue('Computop', 'str_ComputopBankTransfer') . "'});";

					}

					if (((in_array($countryCode, $countriesCard)) && (in_array($gSession['order']['currencyisonumber'], $currenciesCard))) || ($supportedCountriesLength == 0 && $defaultPaymentType == 'card'))
					{

						$script .= "COMPUTOPBankTypeArray.push({id: 'card', name: '" .  SmartyObj::getParamValue('Computop', 'str_ComputopCreditCard') . "'});";
					}

					if (($ComputopConfig['ENABLEPAYPAL'] == 1) && ((in_array($gSession['order']['billingcustomercountrycode'], $countriesPaypal)) && (in_array($gSession['order']['currencyisonumber'], $currenciesPaypal))) || ($defaultPaymentType == 'paypal'))
					{
						$script .= "COMPUTOPBankTypeArray.push({id: 'paypal', name: '" . SmartyObj::getParamValue('Computop', 'str_ComputopPaypal') . "'});";
					}

					$script .="

					if (COMPUTOPBankTypeArray)
					{
						for (var i = 0; i < COMPUTOPBankTypeArray.length; i++)
						{
							var option = document.createElement('option');
							option.value = COMPUTOPBankTypeArray[i].id;

							if (option.value == '" . $gSession['order']['paymentgatewaycode'] . "')
							{
								option.selected = 'selected';
							}

							option.appendChild(document.createTextNode(COMPUTOPBankTypeArray[i].name));
							selector.appendChild(option);

						}
					}
					selector.options[0].selected = 'selected';
				}

				function COMPUTOPBankDropdown()
				{
					var selectorOuterDiv = document.createElement('div');
					selectorOuterDiv.setAttribute('class', 'wizard-dropdown');

					var selector = document.createElement('select');
					selector.id = 'paymentgatewaycode';
					selector.name = 'paymentgatewaycode';
					selector.setAttribute('class', 'wizard-dropdown');
					selector.style.display = 'none';

					selectorOuterDiv.appendChild(selector);
					creditCardContainer.appendChild(selectorOuterDiv);

					var option = document.createElement('option');
					option.value = '';
					option.appendChild(document.createTextNode('-- ". SmartyObj::getParamValue('Computop', 'str_ComputopSelectABank') ." --'));
					selector.appendChild(option);

					// assign the array of paymentmethodlist from the config file
					COMPUTOPBankTypeArray = new Array();
					";

					$i = 0;

					if (count($banks) > 0)
					{
						foreach ($banks as $bank)
						{
							$bank_info = explode(',', $bank);

							if (count($bank_info) > 1)
							{
								$script .= 'COMPUTOPBankTypeArray[' . $i . '] = new ComputopPayType("' . $bank_info[1] . '", "' . $bank_info[0] . '");';

								$i++;
							}
						}
					}

					$script .="
					if (COMPUTOPBankTypeArray)
					{
						for (var i = 0; i < COMPUTOPBankTypeArray.length; i++)
						{
							var option = document.createElement('option');
							option.value = COMPUTOPBankTypeArray[i].id;

							if (option.value == '" . $gSession['order']['paymentgatewaycode'] . "')
							{
								option.selected = 'selected';
							}

							option.appendChild(document.createTextNode(COMPUTOPBankTypeArray[i].name));
							selector.appendChild(option);
						}
					}
				}

				function ComputopPayType(name, id)
				{
					this.name = name;
					this.id = id;
				}

				function validatePaymentMethod()
				{
					var paymentMethodSelect = document.getElementById('paymentgatewaymethod');
					var paytype = paymentMethodSelect.options[paymentMethodSelect.selectedIndex].value;
					var paymethod = document.getElementById('paymentgatewaycode');
					var submit = false;

					if (document.getElementsByName('paymentgatewaymethod').item(0).value == '')
					{
						alert('Please select a payment method');
						submit = false;
					}
					else
					{
						if (paytype == 'bank')
						{
							if(paymethod.style.display != 'none')
							{
								if (document.getElementsByName('paymentgatewaymethod').item(0).value == '')
								{
									alert('Please select a payment method');
									submit = false;
								}
								else
								{
									var paymentSelect = document.getElementById('paymentgatewaymethod');

									if (paymentSelect.options[paymentSelect.selectedIndex].value == 'bank')
									{
										var bankSelect = document.getElementById('paymentgatewaycode');
										var bankOption = bankSelect.options[bankSelect.selectedIndex];

										if(bankOption.value == '')
										{
											alert('" . SmartyObj::getParamValue('Computop', 'str_ComputopPromtPleaseSelectABank') . "');
											submit = false;
										}
										else
										{
											var input = document.createElement('input');
											input.type = 'hidden';
											input.value = paytype;
											input.name = 'paymentgatewaymethod';
											document.submitform.appendChild(input);

											var bankIdInput = document.createElement('input');
											bankIdInput.type = 'hidden';
											bankIdInput.id = 'paymenthgatewaybank';
											bankIdInput.name = 'paymenthgatewaybank';
											bankIdInput.value = paymethod.options[paymethod.selectedIndex].value;
											document.submitform.appendChild(bankIdInput);

											document.submitform.paymentgatewaycode.value = paymethod.options[paymethod.selectedIndex].value;

											submit = true;
										}
									}
								}
							}
						}
						else if (paytype == 'card')
						{
							var paymentSelect = document.getElementById('paymentgatewaymethod');

							var input = document.createElement('input');
							input.type = 'hidden';
							input.value = paymentSelect.options[paymentSelect.selectedIndex].value;
							input.name = 'paymentgatewaymethod';
							document.submitform.appendChild(input);
							submit = true;
						}
						else if (paytype == 'paypal')
						{
							var paymentSelect = document.getElementById('paymentgatewaymethod');

							var input = document.createElement('input');
							input.type = 'hidden';
							input.value = paymentSelect.options[paymentSelect.selectedIndex].value;
							input.name = 'paymentgatewaymethod';
							document.submitform.appendChild(input);
							submit = true;
						}
						else if (paytype.value == '')
						{
							alert('" . SmartyObj::getParamValue('Computop', 'str_ComputopSelectPaymentMethodError') . "');
						}

						if (submit)
						{
							document.submitform.submit();
						}
					}
				}
			";

			$action = "validatePaymentMethod()";
		}
		else if (in_array($countryCode, $countriesBank) || (($supportedCountriesLength == 0) && (strtolower($defaultPaymentType) == 'bank')))
		{
			$merchantID = $ComputopConfig['MERCHANTID'];
			$password = $ComputopConfig['PASSWORD'];
			$bankListURL = $ComputopConfig['BANKLISTURL'];

			$enc = self::encryptData($password, array('MerchantID' => $merchantID));
			$banks = self::getBanks($merchantID, $bankListURL, $enc['data'], $enc['length'], $password);

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
							newscript.text = 'COMPUTOPBankDropdown();';
							creditCardContainer.appendChild(newscript);
							paymenthod[i].parentNode.getElementsByTagName('label')[0].innerHTML = '" . SmartyObj::getParamValue('Computop', 'str_ComputopBankTransfer') . "';
						}
					}";

			$script = "

				function COMPUTOPBankDropdown()
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
					option.appendChild(document.createTextNode('-- ". SmartyObj::getParamValue('Computop', 'str_ComputopSelectABank') ." --'));
					selector.appendChild(option);

					// assign the array of paymentmethodlist from the config file
					COMPUTOPBankTypeArray = new Array();
					";

					$i = 0;

					if (count($banks) > 0)
					{
						foreach ($banks as $bank)
						{
							$bank_info = explode(',', $bank);
							if (count($bank_info) > 1)
							{
								$script .= 'COMPUTOPBankTypeArray[' . $i . '] = new payType("' . $bank_info[1] . '", "' . $bank_info[0] . '");';
								$i++;
							}
						}
					}

					$script .="

					if (COMPUTOPBankTypeArray)
					{
						for (var i = 0; i < COMPUTOPBankTypeArray.length; i++)
						{
							var option = document.createElement('option');
							option.value = COMPUTOPBankTypeArray[i].id;

							if (option.value == '" . $gSession['order']['paymentgatewaycode'] . "')
							{
								option.selected = 'selected';
							}

							option.appendChild(document.createTextNode(COMPUTOPBankTypeArray[i].name));
							selector.appendChild(option);

						}
					}
				}
			";

			$action = "validatePayType('" . SmartyObj::getParamValue('Computop', 'str_ComputopPromtPleaseSelectABank') . "')";
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

        $parameters = array();

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		// urls must be https
		$path = str_replace('http://', 'https://', UtilsObj::correctPath($gSession['webbrandweburl']));
		$successPath =  $path . 'PaymentIntegration/Computop/computop_redirect.php';
        $cancelReturnPath = $path . 'PaymentIntegration/Computop/computop_cancel.php';
        $returnPath = $path . 'PaymentIntegration/Computop/computop_notify.php';
        $payment_url = '';

        // read config file
        $ComputopConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Computop.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

        if ($gSession['order']['ccidata'] == '')
        {
            // read from config filex
            $merchantID = $ComputopConfig['MERCHANTID'];
            $password = $ComputopConfig['PASSWORD'];
			$MACKEY = $ComputopConfig['MACKEY'];
            $countryCode = $gSession['order']['billingcustomercountrycode'];

			$transID = $gSession['ref'] . '_' . time();
			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
			$currency = $gSession['order']['currencycode'];
			$defaultPaymentType = $ComputopConfig['DEFAULTPAYMENTTYPE'];

			// build supported countries lists
			$countriesBank = explode(',', $ComputopConfig['COUNTRIESBANK']);
			$countriesCard = explode(',', $ComputopConfig['COUNTRIESCARD']);
			$countriesPaypal = explode(',', $ComputopConfig['COUNTRIESPAYPAL']);
			$supportedCountries = array_merge($countriesBank, $countriesCard, $countriesPaypal);

			// remove empty array values in case there are no countires defined
			$supportedCountries = array_filter($supportedCountries);
			$supportedCountriesLength = count($supportedCountries);

			$paymentType = isset($_POST['paymentgatewaymethod']) ? $_POST['paymentgatewaymethod'] : '';

			$productName = LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);

            // payment url depends billing country code
            if ((in_array($countryCode, $countriesBank) && in_array($paymentType, array('', 'bank'))) || ($supportedCountriesLength == 0 && $defaultPaymentType == 'bank'))
			{
				// bank transfer
				$payment_url = $ComputopConfig['BANKURL'];
				$oftMethod = $ComputopConfig['BANKOFTMETHOD'];
				
				$parameters = array(
					'MerchantID' => $merchantID,
					'otfMethod' => $oftMethod,
					'TransID' => $transID,
					'RefNr' => $transID,
					'Amount' => $amount,
					'Currency' => $currency, // euro only
					'URLSuccess' => $successPath,
					'URLFailure' => $cancelReturnPath,
					'URLNotify' => $returnPath,
					'UserData' => $gSession['ref'],
					'OrderDesc' => $productName,
					'Response' => 'encrypt',
					'Capture' => 'AUTO',
					'ReqID' => $transID,
					'URLBack' => $cancelReturnPath . '?UserData=' . $gSession['ref']
				);

				if (isset($_POST['paymentgatewaycode']))
				{
					$parameters['IssuerID'] = UtilsObj::getPOSTParam('paymentgatewaycode');
				}
			}
			else if ((in_array($countryCode, $countriesCard) && in_array($paymentType, array('', 'card'))) || ($supportedCountriesLength == 0 && $defaultPaymentType == 'card'))
			{
				// credit or debit card
				$payment_url = $ComputopConfig['CARDURL'];

				$parameters = array(
					'MerchantID' => $merchantID,
					'TransID' => $transID,
					'RefNr' => $transID,
					'Amount' => $amount,
					'Currency' => $currency,
					'URLSuccess' => $successPath,
					'URLFailure' => $cancelReturnPath,
					'URLNotify' => $returnPath,
					'UserData' => $gSession['ref'],
					'OrderDesc' => $productName,
					'Response' => 'encrypt',
					'Capture' => 'AUTO',
					'ReqID' => $transID,
					'URLBack' => $cancelReturnPath . '?UserData=' . $gSession['ref'],
				);

			}
            else if (in_array($countryCode, $countriesPaypal) || ($supportedCountriesLength == 0 && strtolower($defaultPaymentType) == 'paypal'))
			{
                    // paypal
                    $payment_url = $ComputopConfig['PAYPALURL'];

					$paypal_merchantid = $ComputopConfig['PAYPALMERCHANTID'];
					$paypal_brandname = $ComputopConfig['PAYPALBRANDNAME'];

					// supported Languages: AU, DE, FR, IT, GB, ES, US
					// use country code not browser language
					$language = 'GB';
					if (in_array($gSession['order']['billingcustomercountrycode'], array('AU', 'DE', 'FR', 'IT', 'GB', 'ES', 'US')))
					{
						$language = $gSession['order']['billingcustomercountrycode'];
					}

					// paypal parameters
					$parameters = array(
						'MerchantID' => $merchantID,
						'TransID' => $transID,
						'RefNr' => $transID,
						'Amount' => $amount,
						'Currency' => $currency,
						'Capture' => 'Auto',
						'OrderDesc' => $productName,
						'TaxTotal' => '0',
						'ItemTotal' => $amount,
						'URLSuccess' => $successPath,
						'URLFailure' => $cancelReturnPath,
						'URLNotify' => $returnPath,
						'UserData' => $gSession['ref'],
						'Language' => $language,
						'Account' => $paypal_merchantid,
						'Response' => 'encrypt',
                        'FirstName' => $gSession['shipping'][0]['shippingcontactfirstname'],
                        'LastName' => $gSession['shipping'][0]['shippingcontactlastname'],
                        'AddrStreet' => $gSession['shipping'][0]['shippingcustomeraddress1'],
                        'AddrStreet2' => $gSession['shipping'][0]['shippingcustomeraddress2'],
                        'AddrCity' => $gSession['shipping'][0]['shippingcustomercity'],
                        'AddrState' => $gSession['shipping'][0]['shippingcustomercounty'],
                        'AddrZIP' => $gSession['shipping'][0]['shippingcustomerpostcode'],
                        'AddrCountryCode' => $gSession['shipping'][0]['shippingcustomercountrycode']
					);

					if ($paypal_brandname != '')
					{
						$parameters['BrandName'] = $paypal_brandname;
					}
            }
			
            // generate mac code
            $macArray = ComputopObj::encryptData($password, $parameters);

            $data = array(
                'MerchantID' => $merchantID,
                'Len'       =>  $macArray['length'],
                'MAC'       => hash_hmac('sha256', '*'. $transID . '*' . $merchantID . '*' . $amount . '*' . $currency, $MACKEY)
			);

			$data['language'] = $gSession['browserlanguagecode'];
            
			// merge mac and parameters to send
            $data = array_merge($data, $macArray);

            // define smarty parameters
            $smarty->assign('method', 'POST');
            $smarty->assign('parameter', $data);
            $smarty->assign('cancel_url', $cancelReturnPath);
            $smarty->assign('payment_url', $payment_url);

            AuthenticateObj::defineSessionCCICookie();
            $smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
            $smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

            // set the ccidata to remember we have jumped to computop
            $gSession['order']['ccidata'] = 'start';
			
			if ($paymentType != '')
			{
				$paymentGatewayBank = UtilsObj::getPOSTParam('paymenthgatewaybank', '');

				$gSession['order']['paymentgatewaycode'] = trim(ucfirst($paymentType) . ' ' . $paymentGatewayBank);
			}

            DatabaseObj::updateSession();

            $smarty->cachePage = true; // allow the page to be cached so that the browser back button works correctly
        }
		else
        {
            // the user has clicked the back button
            AuthenticateObj::clearSessionCCICookie();

            $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
            $smarty->assign('payment_url', $cancelReturnPath);
            $smarty->assign('cancel_url', $cancelReturnPath);
        }

        // display template
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

	static function getBanks($merchantID, $bankListURL, $data, $length, $password)
	{
		$banks_array = array();

		$post_fields = array(
			"MerchantID" => $merchantID,
			"Data" => $data,
			"Len" => $length,
		);

		$fields_string = http_build_query($post_fields);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_URL, $bankListURL);

		// grab url and pass it to the browser
		$result = curl_exec($curl);
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		// close curl resource, and free up system resources
		curl_close($curl);
		
		if ($http_status == '200')
		{
			// we only want the data value
			$split = explode('&', $result);

			if (count($split) > 1)
			{
				$data = str_replace('Data=', '', $split[2]);
				$decrypted_banks = self::decryptData($password, $data, strlen($data));

				$banks = str_replace(array('IdealIssuerList=', 'BICList='), '',  $decrypted_banks);
				$banks_array = explode('|', $banks);
			}
		}

		return $banks_array;
	}

    static function encryptData($password, $parameters = array())
    {
        $inputMac = '';
        foreach ($parameters as $key => $value)
        {
            $inputMac .= '&' . $key . '=' . $value;
        }

		// encrypt string
        $length = strlen($inputMac);
        $string = mcrypt_encrypt(MCRYPT_BLOWFISH, $password, $inputMac, MCRYPT_MODE_ECB, $length);
        $string = bin2hex($string);

        return array('data' => $string, 'length' => $length);
    }

	static function decryptData($password, $data, $length)
	{
		// convert hex back to binary
		$string = hex2bin($data);
		return trim(mcrypt_decrypt(MCRYPT_BLOWFISH, $password, $string, MCRYPT_MODE_ECB, $length));
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

    static function confirm($callBack = '')
    {
        global $gSession;

        $resultArray = Array();
        $showError = false;
        $update = false;
		$authorised = false;
        $authorisedStatus = 0;
		$paymentReceived = 0;
        $result = '';
		$transID = 0;
        $amount = $gSession['order']['ordertotaltopay'];
        $currency = $gSession['order']['currencycode'];
        $webBrandCode = $gSession['webbrandcode'];
		$paymentMethod = '';

        // read config file
        $ComputopConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Computop.conf', $currency, $gSession['webbrandcode']);

		if (isset($_POST['Data']))
		{
			// encrypted response
			$merchantID = $ComputopConfig['MERCHANTID'];
			$password = $ComputopConfig['PASSWORD'];
			$bankListURL = $ComputopConfig['BANKLISTURL'];
			$parsed = array();

			$enc = self::encryptData($password, array('MerchantID' => $merchantID));
			$data = self::getBanks($merchantID, $bankListURL, $enc['data'], $enc['length'], $password);
			$decrypted_parameters = self::decryptData($password, UtilsObj::getPOSTParam('Data'), UtilsObj::getPOSTParam('Len'));
			parse_str($decrypted_parameters, $parsed);

			$ref = $gSession['ref'];
			$payID = $parsed['PayID']; // paygate paymentid
			$XID = $parsed['XID']; // paygate id for transactions
			$transID = $parsed['TransID']; // our transaction ID
			$mid = $parsed['mid']; // merchant id
			$userData = $parsed['UserData']; // custom data (our session ref)
			$code = $parsed['Code']; // error code. 0 = no error.
			$status = $parsed['Status']; // authorized|ok|failed
			$description = $parsed['Description']; // description of error

			if (isset($parsed['Type']))
			{
				$type = $parsed['Type'];
			}
		}
		else
		{
			$ref = $gSession['ref'];
			$payID = UtilsObj::getGETParam('PayID'); // paygate paymentid
			$XID = UtilsObj::getGETParam('XID'); // paygate id for transactions
			$transID = UtilsObj::getGETParam('TransID'); // our transaction id
			$mid = UtilsObj::getGETParam('mid'); // merchant id
			$userData = UtilsObj::getGETParam('UserData'); // custom data (our session ref)
			$code = UtilsObj::getGETParam('Code'); // error code. 0 = no error.
			$status = UtilsObj::getGETParam('Status'); // authorized|ok|failed
			$description = UtilsObj::getGETParam('Description'); // description of error
			$type= '';
			
			if (isset($_GET['Type']))
			{
				$type = UtilsObj::getGETParam('Type');
			}
		}

        // payment status
		switch ($status)
		{
			case 'OK':
			case 'PENDING':
			case 'AUTHORIZED':
				$authorised = true;
				$authorisedStatus = 1;

				if ($status == 'PENDING')
				{
					$paymentReceived = 0;
				}
				else
				{
					$paymentReceived = 1;
				}
				break;

			case 'FAILED':
				$authorised = false;
				$authorisedStatus = 0;
				break;
		}

		// check ccilog to see if this is an update
		$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

		if (empty($cciLogEntry))
		{
			// first callback
			$update = false;
		}
		else
		{
			// additional callbacks
			$update = true;
		}

		if ($callBack == 'manual')
		{
			// check for error codes
			// 0 means no error
			if ($code > 0)
			{
				$showError = true;
				$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $code;
				$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . urldecode($description);
				$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $transID;
				$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelPayID') . ': ' . $payID;
				$resultArray['errorform'] = 'error.tpl';
			}
		}

        // write result data to log file.
        $serverTimestamp = DatabaseObj::getServerTime();
        PaymentIntegrationObj::logPaymentGatewayData($ComputopConfig, $serverTimestamp);

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
        $resultArray['transactionid'] = $payID;
        $resultArray['formattedtransactionid'] = $payID;
        $resultArray['responsecode'] = $status;
        $resultArray['responsedescription'] = '';
        $resultArray['authorisationid'] = $transID;  // this is our unique id, not the real order id
        $resultArray['formattedauthorisationid'] = $transID;
        $resultArray['bankresponsecode'] = $status;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = UtilsObj::getArrayParam($gSession['order'], 'paymentgatewaycode', '');
        $resultArray['paymenttime'] = $serverTime;
        $resultArray['paymentreceived'] = $paymentReceived;
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
        $resultArray['transactiontype'] = $type;
        $resultArray['settleamount'] = '';
        $resultArray['currencycode'] = $currency;
        $resultArray['webbrandcode'] = $webBrandCode;

        $resultArray['charityflag'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['update'] = $update;
        $resultArray['orderid'] = $transID;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
        $resultArray['showerror'] = $showError;

        return $resultArray;
    }
}