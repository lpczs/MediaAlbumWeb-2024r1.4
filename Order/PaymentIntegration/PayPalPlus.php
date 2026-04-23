<?php
use Security\ControlCentreCSP;

class PayPalObj
{
	static function configure()
	{
		global $gSession;

		$resultArray = array();
		$active = true;
		$script = '';
		$ref = UtilsObj::getGETParam('ref');

		AuthenticateObj::clearSessionCCICookie();

		$paypalConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayPal.conf', $gSession['order']['currencycode'], $gSession['webbrandcode'], 'PAYPAL');

		// check the config exists and has been configured correctly
		if (count($paypalConfig) > 0)
		{
			// Check the currency conversion
			$orderCurrencyArray = self::getOrderCurrency();

			if ($orderCurrencyArray['ordercurrency'] !== '')
			{
				// Test for supported currencies.
				$active = in_array($orderCurrencyArray['ordercurrency'], explode(',', $paypalConfig['PLUS_CURRENCIES']));
			}
			else
			{
				$active = false;
			}

			// check https is enabled for live servers
			if (($active) && ((! isset($_SERVER['HTTPS'])) || ($_SERVER['HTTPS'] == 'off')) && (strtolower($paypalConfig['PLUS_MODE']) == 'live'))
			{
				$active = false;
			}

			if ($active)
			{
				// Disable CSP for paypal untill paypal address the issue of an inline script.
				$GLOBALS['ac_config']['CONTENTSECURITYPOLICY'] = 'DISABLED';
				$smarty = SmartyObj::newSmarty('CreditCardPayment', '', '', $gSession['browserlanguagecode']);
				$loadingOptionsMessage = $smarty->get_config_vars('str_OrderPayPalPlusLoadingOptions');

				$script = '
					var isSmallScreen = "'. (($gSession['ismobile']) ? 'true' : 'false')  .'";
					var initPayPalPlus = "' . (($gSession['order']['ordertotaltopay'] == 0.00) ? 'false' : 'true') . '";

					function initPaymentWall()
					{
						// create a placeholder
						var payPalPaymentOptionElement;

						if (document.querySelector)
						{
							payPalPaymentOptionElement = document.querySelector("input[type=radio][value=PAYPAL]");
						}
						else
						{
							var childNodes = document.getElementById("paymentMethodsList").getElementsByTagName("input");
							var childNodesLength = childNodes.length;

							for (var i = 0; i < childNodesLength; i++)
							{
								if (childNodes[i].value == "PAYPAL")
								{
									payPalPaymentOptionElement = childNodes[i];
									break;
								}
							}
						}

						// IE8 and lower are not supported by paypals script so we need to hide the PayPal option
						if (window.addEventListener)
						{
							// there should always be at least 1
							var paymentMethods = document.getElementById("paymentMethodsList").getElementsByTagName("div");

							// hide PayPal payment option
							payPalPaymentOptionElement.parentNode.style.display = "none";

							// create the placeholder where the paypal plus payment options go
							var pppPlaceholder = document.createElement("div");
							pppPlaceholder.id = "ppplus";
							pppPlaceholder.className = "paymentmethodlist";
							pppPlaceholder.style.padding = "0";

							var pppLoadingText = document.createElement("p");
							pppLoadingText.className = "payPalLoading";
							var textNode = document.createTextNode("' . $loadingOptionsMessage . '");

							pppLoadingText.appendChild(textNode);
							pppPlaceholder.appendChild(pppLoadingText);

							// place it above the other payment method options
							document.getElementById("paymentMethodsList").insertBefore(pppPlaceholder, paymentMethods[0]);

							if (initPayPalPlus === "true")
							{
								payPalPlusLoadPaymentOptions();
							}
							else
							{
								pppPlaceholder.style.display = "none";
							}
						}
						else
						{
							// unsupported browser
							payPalPaymentOptionElement.parentNode.style.display = "none";
						}
					};

					function reinitializePayPal()
					{
						var placeholderElement = document.getElementById("ppplus");

						if ((placeholderElement != null) && (placeholderElement.style.display !== "block"))
						{
							placeholderElement.innerHTML = "";
							placeholderElement.style.height = "";
							var pppLoadingText = document.createElement("p");
							pppLoadingText.className = "payPalLoading";
							var textNode = document.createTextNode("' . $loadingOptionsMessage . '");
							pppLoadingText.appendChild(textNode);
							placeholderElement.appendChild(pppLoadingText);
							placeholderElement.style.display = "block";
							
						}

						payPalPlusLoadPaymentOptions();
					}
					
					function payPalPlusLoadPaymentOptions()
					{
						var xmlhttp = getxmlhttp();

						xmlhttp.open("GET", "?fsaction=Order.initPaymentGatewayPaymentOptions&ref=' . $ref . '&pm=PAYPAL&dummy=" + new Date().getTime(), true);
						xmlhttp.send(null);

						xmlhttp.onreadystatechange = function()
						{
							if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
							{
								var script = document.createElement("script");
								script.type = "text/javascript";
								var scriptTextNode = document.createTextNode(xmlhttp.responseText);
								script.appendChild(scriptTextNode);
								document.getElementsByTagName("head")[0].appendChild(script);
								document.getElementById("ppplus").style.height = "";
							}
						}
					};

					// trigger the initPaymentWall() in different ways depending on the screen size
					if (isSmallScreen == "true")
					{
						// the dom is already loaded on small screen so we need to use a settimeout instead

						setTimeout(function()
						{
							initPaymentWall();
						}, 300);
					}
					else
					{
						if (window.addEventListener)
						{
							window.addEventListener("DOMContentLoaded", function()
							{
								initPaymentWall();
							}, false);
						}
						else if (window.attachEvent)
						{
							window.attachEvent("onload", function()
							{
								initPaymentWall();
							}, false);
						}
					}';
			}
		}
		else
		{
			$active = false;
		}

		$resultArray['active'] = $active;
		$resultArray['form'] = '';
		$resultArray['scripturl'] = '/PaymentIntegration/PayPalPlus/ppplus.min.js';
		$resultArray['script'] = $script;
		$resultArray['action'] = '';

		return $resultArray;
	}

	/**
	 * Build paypal payment option list and create a paypal payment at server side.
	 * Callesd by ajax when the Taopix payment page gets loaded.
	 * 
	 * @return array An empty array or an array with an HTML content.
	 */
	static function paymentOptionsCallback()
	{
		global $gSession;

		$resultArray = [];
		$script = '';
		$paypalConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayPal.conf', $gSession['order']['currencycode'], $gSession['webbrandcode'], 'PAYPAL');

		// Initialise the payment on paypal server.
		$createPaymentResult = self::createPayment($paypalConfig);

		// Make sure the payment has been created succesfully.
		if ($createPaymentResult['url'] != '')
		{
			$script = '
				var isSmallScreen = "'. (($gSession['ismobile']) ? 'true' : 'false')  .'";

				function initPayPalPlus()
				{
					var ppp;
					var payPalPaymentOptionElement;

					if (document.querySelector)
					{
						payPalPaymentOptionElement = document.querySelector("input[type=radio][value=PAYPAL]");
					}
					else
					{
						var childNodes = document.getElementById("paymentMethodsList").getElementsByTagName("input");
						var childNodesLength = childNodes.length;

						for (var i = 0; i < childNodesLength; i++)
						{
							if (childNodes[i].value == "PAYPAL")
							{
								payPalPaymentOptionElement = childNodes[i];
								break;
							}
						}
					}

					ppp = PAYPAL.apps.PPP(
					{
						approvalUrl: "' . $createPaymentResult['url'] . '",
						placeholder: "ppplus",
						mode: "' . strtolower($paypalConfig['PLUS_MODE']) . '",
						country: "' . $gSession['order']['billingcustomercountrycode'] . '",
						language: "' . self::getPayPalLocale() . '",
						showPuiOnSandbox: true, // applies to sandbox mode only, ignore when mode is set to live
						buttonLocation: "outside",
						showLoadingIndicator: true,
						preselection: "paypal",
						onLoad: function() {},
						onContinue: "ordercontinuebutton",
						enableContinue: function(pParam)
						{
							resetSelectedPaymentOption();

							// select the PayPal option
							payPalPaymentOptionElement.checked = true;

							// Set that we are not remotley requesting the payment params (No longer a lightbox payment method).
							gRequestPaymentParamsRemotely = false;
							
							// fire the click event on the hidden paymentlist radio button that paypal is attached to.
							// this is due to lightbox payment gateways changing the on click event for the radio buttons 
							// in order to ovveride the onclick event of the order confirm button.
							payPalPaymentOptionElement.click();
						},
						disableContinue: function(pParam)
						{
							// disable the PayPal option
							payPalPaymentOptionElement.checked = false;
						}
					});

					// Deselect options when selecting other payment methods.
					document.body.addEventListener("change", function (e) {
						var target = e.target;
						if ((target.name === "paymentmethods") && (target.value !== "PAYPAL")) {
							ppp.deselectPaymentMethod();
						}
					});
				};

				function resetSelectedPaymentOption()
				{
					var childNodes = document.getElementById("paymentMethodsList").getElementsByTagName("input");
					var childNodesLength = childNodes.length;

					for (var i = 0; i < childNodesLength; i++)
					{
						var node = childNodes[i];
						if (node.checked == true)
						{
							node.checked = false;

							var parentNode = node.parentNode;
							if (parentNode.className.indexOf("optionSelected") != -1)
							{
								parentNode.className = parentNode.className.replace("optionSelected", "");
							}
						}
					}
				}

				(function()
				{
					initPayPalPlus();
				})();';
		}
		else 
		{
			$script = '
				function resetSelectedPaymentOption()
				{
					var childNodes = document.getElementById("paymentMethodsList").getElementsByTagName("input");
					var childNodesLength = childNodes.length;
					var paypalTicked = false;

					// Untick paypal if it is ticked.
					for (var i = 0; i < childNodesLength; i++)
					{
						var node = childNodes[i];
						if ((node.value == "PAYPAL") && (node.checked == true))
						{
							node.checked = false;

							var parentNode = node.parentNode;
							if (parentNode.className.indexOf("optionSelected") != -1)
							{
								parentNode.className = parentNode.className.replace("optionSelected", "");
							}
							paypalTicked = true;
						}
					}

					// If paypal was tick try to to tick the first option excluding paypal option.
					if (paypalTicked)
					{
						for (var i = 0; i < childNodesLength; i++)
						{
							var node = childNodes[i];
							if (node.value !== "PAYPAL")
							{
								node.checked = true;

								var parentNode = node.parentNode;
								parentNode.className += " optionSelected";
								break;
							}
						}
					}
				}
				
				resetSelectedPaymentOption();';
		
			if ($createPaymentResult['addressinvalid'])
			{
				$smarty = SmartyObj::newSmarty('CreditCardPayment', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

				$script .= '
					// Display an error message.
					document.getElementById("ppplus").innerHTML = "<div class=\"paypalplus-address-error\"><h3>PayPal</h3>' . $smarty->get_config_vars('str_OrderPayPalPlusAddressInvalid') . '</div>";';
			}
			else
			{
				// Remove paypal option.
				$script .= ' 
					// Hide Paypal option.
					document.getElementById("ppplus").style.display = "none";';
			}
		}

		$resultArray['html'] = $script;
		return $resultArray;
	}

	/**
	 * Map the selected language to a paypal locale.
	 * 
	 * @return string The paypal locale.
	 */
	static function getPayPalLocale()
	{
		global $gSession;

		$acceptedLanguages = array
		(
			'de' => 'de_DE',
			'en' => 'en_US',
			'es' => 'es_ES',
			'fr' => 'fr_FR',
			'it' => 'it_IT',
			'pl' => 'pl_PL',
			'pt' => 'pt_PT',
			'nl' => 'nl_NL',
			'no' => 'no_NO',
			'ru' => 'ru_RU',
			'th' => 'th_TH',
			'cs' => 'sv_SE',
			'da' => 'da_DK',
			'ja' => 'ja_JP',
			'zh_cn' => 'zh_CN',
			'zh_tw' => 'zh_TW',
		);

		return UtilsObj::getArrayParam($acceptedLanguages, $gSession['browserlanguagecode'], 'en_US');
	}

	static function initialize()
	{
		global $gSession;

		$resultArray['showerror'] = false;
		$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
		$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			// define smarty variables
			$smarty->assign('payment_url', '');
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('parameter', array());
			$smarty->assign('method', 'POST');
			$smarty->assign('ispaypalplus', 'true');

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to paypal
			$gSession['order']['ccidata'] = 'start';
			DatabaseObj::updateSession();

			$smarty->cachePage = true; // allow the page to be cached so that the browser back button works correctly
		}
		else
		{
	        // The user has clicked the back button
            AuthenticateObj::clearSessionCCICookie();
            $smarty->assign('payment_url', $cancelReturnPath);
		}

		// Load the corresponding template.
		if ($gSession['ismobile'])
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

	static function confirm()
	{
		$resultArray = [];
		$ref = UtilsObj::getGETParam('ref');
		$parentID = 0;
        $update = false;

		// check if there is an existing cci log entry for this reference
		$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

		// cci log exists, so get the parent log id and set update to true
		if (count($cciLogEntry) > 0)
		{
			$parentID = ($cciLogEntry['parentlogid'] !== 0) ? $cciLogEntry['parentlogid'] : $cciLogEntry['id'];
			$update = true;
		}

		// Automatic callback triggered by a PayPal webhook
		if (isset($_POST['paypalpluswebhook']))
		{
			$resultArray = $cciLogEntry;
			$resultArray['resultisarray'] = false;
			$resultArray['showerror'] = false;
			$resultArray['parentlogid'] = $parentID;
			$resultArray['authorised'] = 1; // already authorised
			$resultArray['authorisedstatus'] = 1;
			$resultArray['paymentreceived'] = 0;
			$resultArray['update'] = $update;

			$webhookArray = UtilsObj::getArrayParam($_POST, 'paypalpluswebhook', array('event_type' => ''));

			if (strtolower($webhookArray['event_type']) === 'payment.sale.completed')
			{
				$resultArray['paymentreceived'] = 1;
				$resultArray['responsecode'] = $webhookArray['resource']['state'];
				$resultArray['responsedescription'] = self::getResponseDescription($webhookArray['resource']['state']);
			}
		}
		else
		{
			// Manual callback
			$resultArray = self::executeManualCallBack($update, $parentID, $ref);
		}

        return $resultArray;
	}

	/**
	 * Manual callback.
	 * 
	 * @param boolean $pUpdate Boolean to detect if CCI logs needs to be updated.
	 * @param int $pParentID CCIlog parent ID
	 * @param string $pRef Taopix Ref.
	 * @return array An array with all payment details.
	 */
	static function executeManualCallBack($pUpdate, $pParentID, $pRef)
	{
		global $gSession;

		$update = $pUpdate;
		$showError = false;
		$authorised = false;
		$token = UtilsObj::getGETParam('token', 'NO_TOKEN');
		$resultArray = [];
		$paymentDetails = [];
		$bankName = '';
		$accountHolderName = '';
		$internationalBankAccountNumber = '';
		$bankIdentifierCode = '';
		$paymentDueDate = '';
		$reference = '';
		$responseCode = '';
		$paymentReceived = 0;

		if ($token !== 'NO_TOKEN')
		{
			// Execute the payment.
			$paymentDetails = self::processPayment();

			if ($paymentDetails['error'])
			{
				$showError = true;
				$responseCode = $paymentDetails['errorparam'];
			}

			if (! $showError)
			{
				$responseCode = $paymentDetails['state'];

				switch ($paymentDetails['paymentstatus'])
				{
					case 'completed':
					{
						$authorised = true;
						$paymentReceived = (strtolower($paymentDetails['paymentmethod']) == 'pay_upon_invoice') ? 0 : 1;
						break;
					}
					case 'pending':
					{
						$authorised = true;
						$paymentReceived = 0;
						break;
					}
					default:{
						$showError = true;
					}
				}
			}

			if (! $showError)
			{
				$paymentType = strtolower($paymentDetails['paymentmethod']);

				if ($paymentType === 'pay_upon_invoice')
				{
					$recipientBankingInstruction = $paymentDetails['bankinginstruction'];

					$bankName = $recipientBankingInstruction['bankname'];
					$accountHolderName =  $recipientBankingInstruction['accountholdername'];
					$internationalBankAccountNumber = $recipientBankingInstruction['internationalbankaccountnumber'];
					$bankIdentifierCode = $recipientBankingInstruction['bankidentifiercode'];
					$paymentDueDate = $recipientBankingInstruction['paymentduedate'];
					$reference = $recipientBankingInstruction['reference'];
				}
			}
		}
		else
		{
			$showError = true;
			$responseCode = 'failed';
		}

		$responseCodeDescription = self::getResponseDescription($responseCode);

		// set results
		$resultArray['result'] = '';
		$resultArray['ref'] = $pRef;
		$resultArray['authorised'] = $authorised;
		$resultArray['showerror'] = $showError;
		$resultArray['update'] = $update;

		if ($showError)
		{
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . strtoupper($responseCode);
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $responseCodeDescription;
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . UtilsObj::getPOSTParam('Details2');
			$resultArray['data4'] = '';
			$resultArray['errorform'] = 'error.tpl';
			$update = true;
		}
		else
		{
			// Log the success callback
			self::writeLogs('PayPalPlusObj.executeManualCallBack', $paymentDetails);

			$serverTimestamp = DatabaseObj::getServerTime();
			$serverDate = date('Y-m-d');
			$serverTime = date("H:i:s");

			$resultArray['amount'] = $paymentDetails['amount'];
			$resultArray['formattedamount'] = $paymentDetails['amount'];
			$resultArray['charges'] = '';
			$resultArray['formattedcharges'] = '';
			$resultArray['authorisedstatus'] = ($authorised  == true) ? 1 : 0;
			$resultArray['transactionid'] = $paymentDetails['transactionid'];
			$resultArray['formattedtransactionid'] = $paymentDetails['transactionid'];
			$resultArray['responsecode'] = $responseCode;
			$resultArray['responsedescription'] = $responseCodeDescription;
			$resultArray['authorisationid'] = $token;
			$resultArray['formattedauthorisationid'] = $token;
			$resultArray['bankresponsecode'] = $paymentDetails['paymentstatus'];
			$resultArray['cardnumber'] = '';
			$resultArray['formattedcardnumber'] = '';
			$resultArray['cvvflag'] = '';
			$resultArray['cvvresponsecode'] = '';
			$resultArray['paymentcertificate'] = $bankName;
			$resultArray['paymentdate'] = $serverDate;
			$resultArray['paymentmeans'] = $paymentDetails['paymentmethod'];
			$resultArray['paymenttime'] = $serverTime;
			$resultArray['paymentreceived'] = $paymentReceived;
			$resultArray['formattedpaymentdate'] = $serverTimestamp;
			$resultArray['addressstatus'] = $reference;
			$resultArray['postcodestatus'] = $bankIdentifierCode;
			$resultArray['payerid'] = $paymentDetails['payerid'];
			$resultArray['payerstatus'] = $internationalBankAccountNumber;
			$resultArray['payeremail'] = $paymentDetails['payeremail'];
			$resultArray['business'] = $accountHolderName;
			$resultArray['receiveremail'] = '';
			$resultArray['receiverid'] = '';
			$resultArray['pendingreason'] = $paymentDueDate;
			$resultArray['transactiontype'] = $paymentDetails['intent'];
			$resultArray['settleamount'] = '';
			$resultArray['currencycode'] = $paymentDetails['currency'];
			$resultArray['webbrandcode'] = $gSession['webbrandcode'];
			$resultArray['script'] = '';
			$resultArray['scripturl'] = '';
			$resultArray['charityflag'] = '';
			$resultArray['threedsecurestatus'] = '';
			$resultArray['cavvresponsecode'] = '';
			$resultArray['orderid'] = 0;
			$resultArray['parentlogid'] = $pParentID;
			$resultArray['resultisarray'] = false;
			$resultArray['resultlist'] = array();
		}

		return $resultArray;
	}

	static function cancel()
	{
		$resultArray = [];

		// set results
		$resultArray['result'] = '';
        $resultArray['ref'] = UtilsObj::getGETParam('ref');
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['authorisedstatus'] = 0;
        $resultArray['showerror'] = false;
        return $resultArray;
	}

	/**
	 * Return additionnal payment data if payment is compatible. 
	 * 
	 * @param array $pParamArray Contains payment ID.
	 * @return string An HTML string with the payment info.
	 */
	static function getAdditionalPaymentInfo($pParamArray)
	{
		global $gSession;

		$returnData = '';
		$payment = null;
		$paypalConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayPal.conf', $gSession['order']['currencycode'], $gSession['webbrandcode'], 'PAYPAL');
		$getTokenResult = self::getToken($paypalConfig);

		// Get the payment object using the paymentID.
		if (! $getTokenResult['error'])
		{
			$getPaymentResult = self::getPayment($paypalConfig, $pParamArray['paymentid'], $getTokenResult['data']);
			$payment = $getPaymentResult['payment'];
		}

		// Make sure a payment has been found.
		if (($payment) && (isset($payment->payment_instruction)))
		{
			// Get payment instructions.
			$paymentInstruction = $payment->payment_instruction;

			// Get instruction type.
			$paymentType = strtolower($paymentInstruction->instruction_type);

			// Add extra payment detais if it's a upon invoice payment.
			if ($paymentType == 'pay_upon_invoice')
			{
				$recipientBankingInstruction = $paymentInstruction->recipient_banking_instruction;

				$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

				$smarty->assign('bankname', $recipientBankingInstruction->bank_name);
				$smarty->assign('accountholdername', $recipientBankingInstruction->account_holder_name);
				$smarty->assign('internationalbankaccountnumber', $recipientBankingInstruction->international_bank_account_number);
				$smarty->assign('bankidentifiercode', $recipientBankingInstruction->bank_identifier_code);
				$smarty->assign('paymentduedate', $paymentInstruction->payment_due_date);
				$smarty->assign('reference', $paymentInstruction->reference_number);
				$smarty->assign('webbrandapplicationname', $gSession['webbrandapplicationname']);

				$returnData = $smarty->fetchLocale('order/PaymentIntegration/PaymentAdditionalInfo.tpl', $gSession['browserlanguagecode']);
			}
		}

		return $returnData;
	}

	/**
	 * Create a payment on paypal server.
	 * 
	 * @param array $pPaypalConfig Taopix Paypal config.
	 * @return string The approval URL.
	 */
	static function createPayment($pPaypalConfig)
	{
		$returnUrl = '';
		$addressIsInvalid = false;
		$error = false;
		$token = [];

		// Get the auth token.
		$getTokenResult = self::getToken($pPaypalConfig);
		$error = $getTokenResult['error'];
		$token = $getTokenResult['data'];

		if (! $error)
		{
			// Create payment.sale.completed webhook if it deosn't exists.
			$error = self::getWebHook('PAYMENT.SALE.COMPLETED', $token, $pPaypalConfig);
		}

		if (! $error)
		{
			// Get the webprofile ID to be used for the payment.
			$webProfileId = self::getWebProfile($token, $pPaypalConfig);
		}

		// Make sure a profile has been found.
		if ((! $error) && ($webProfileId !== ''))
		{
			try
			{
				// Create the payment at server side. 
				$pamentDetails = self::getPaymentRequestJSON($webProfileId);
				$returnData = self::processAPIRequest($pPaypalConfig, '/payments/payment', $token, $pamentDetails, 'POST');

				// Make sure the link is returned.
				if (isset($returnData->links))
				{
					// Get the approval Link. 
					foreach($returnData->links as $link)
					{
						if ($link->rel === 'approval_url')
						{
							$returnUrl = $link->href;
							break;
						}
					}
				}
				else if ((isset($returnData->name)) && ($returnData->name === 'VALIDATION_ERROR'))
				{
					$addressIsInvalid =  true;
				}
			} 
			catch (Exception $pError)
			{
				self::writeLogs('PayPalPlusObj.createWebprofile', $pError);
			}
		}

		return ['url' => $returnUrl, 'addressinvalid' => $addressIsInvalid];
	}

	/**
	 * Return a JSON string to create a payment.
	 * 
	 * @param string $pProfileID Profile to be attached to the payment.
	 * @return string A JSON string to create a payment.  
	 */
	static function getPaymentRequestJSON($pProfileID)
	{
		global $gSession;

		// Get the order currency.
		$orderCurrencyArray = self::getOrderCurrency();
		
		// Do the currency conversion if needed.
		if ($orderCurrencyArray['currencyexchanged'])
		{
			$orderTotal = UtilsObj::bround($gSession['order']['ordertotaltopay'] * $orderCurrencyArray['exchangerate'], $orderCurrencyArray['decimalplaces']);
			$orderAmount = number_format($orderTotal, $orderCurrencyArray['decimalplaces'], '.', '');
		}
		else
		{
			$orderAmount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');
		}

		$itemDescription = LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);

		return '{
			"intent": "sale",
			"experience_profile_id": "'. $pProfileID . '",
			"payer": {
				"payment_method": "paypal"
			},
			"transactions": [{
				"amount": {
					"currency": "' . $orderCurrencyArray['ordercurrency']. '",
					"total": "' . $orderAmount . '"
				},
				"description": "' . $itemDescription . '",
				"item_list": {
					"items": [{
						"name": "' . $itemDescription . '",
						"quantity": "1",
						"price": "'. $orderAmount . '",
						"sku": "' .  $gSession['ref'] . '_' . time() . '",
						"currency": "' . $orderCurrencyArray['ordercurrency'] . '"
					}],
					"shipping_address": {
						"line1": "' . $gSession['order']['billingcustomeraddress1'] . '",
						"line2": "' . $gSession['order']['billingcustomeraddress2'] . '",
						"city": "' . $gSession['order']['billingcustomercity'] . '",
						"country_code": "' . $gSession['order']['billingcustomercountrycode'] . '",
						"postal_code": "123' .  $gSession['order']['billingcustomerpostcode'] . '",
						"state": "' . str_replace(' ', '', self::getState()) . '"
					}
				}
			}],
			"redirect_urls": {
				"return_url": "' . UtilsObj::correctPath($gSession['webbrandwebroot']) . '?fsaction=Order.ccManualCallback&pm=PAYPAL&ref=' . $gSession['ref'] . '",
				"cancel_url": "' . UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&pm=PAYPAL&ref=' . $gSession['ref'] . '"
			}
		}';
	}

	/**
	 * Return the state for an address.
	 * 
	 * @return string State value.
	 */
	static function getState() 
	{
		global $gSession;

		$state = '';
		$order = $gSession['order'];
		if (strtolower($order['billingcustomerregion']) == 'state')
		{
			if ($order['billingcustomerstate'] != '')
			{
				$state = (strtolower($order['billingcustomercountrycode']) == 'us') ? $order['billingcustomerregioncode'] : $order['billingcustomerstate'];
			}
			else
			{
				$state = (strtolower($order['defaultbillingcustomercountrycode']) == 'us') ? $order['defaultbillingcustomerregioncode'] : $order['defaultbillingcustomerstate'];
			}
		}
		else
		{
			$state = ($order['billingcustomercounty'] !== '') ? $order['billingcustomercounty'] : $order['defaultbillingcustomercounty'];
		}

		return $state;
	}

	/**
	 * Return a profile ID.
	 * A profile is unique per brand.
	 * 
	 * @param array $pToken Auth token for server communication.
	 * @param array $pPaypalConfig Taopix Paypal config.
	 * @return string Profile ID.
	 */
	static function getWebProfile($pToken, $pPaypalConfig)
	{
		global $gSession;

		$profileID = '';
		$webprofileListResult = self::getWebProfileList($pToken, $pPaypalConfig);

		if (! $webprofileListResult['error'])
		{
			$profileName = 'tpx_' . (($gSession['webbrandcode']) ? $gSession['webbrandcode'] : 'DEFAULT');

			// Check if one of the existing profile match the current brand.
			foreach ($webprofileListResult['data'] as $profileObj)
			{
				if ($profileObj->name === $profileName)
				{
					//Update profile incase config settings have changed.
					if (self::updateWebProfile($pToken, $profileObj, $pPaypalConfig))
					{
						$profileID = $profileObj->id;
					}
					break;
				}
			}

			// Profile doesn't exist, create it.
			if (! $profileID)
			{
				$profileID = self::createWebProfile($pToken, $profileName, $pPaypalConfig);
			}
		}

		return $profileID;
	}

	/**
	 * Get a list of existing profile for the paypal app.
	 * 
	 * @param array $pToken Auth token for server communication.
	 * @param array $pPaypalConfig Taopix Paypal config.
	 * @return array A list of existing profile.
	 */
	static function getWebProfileList($pToken, $pPaypalConfig)
	{
		$error = false;
		$returnData = [];

		try
		{
			$returnData = self::processAPIRequest($pPaypalConfig, '/payment-experience/web-profiles', $pToken, null, 'GET');
		} 
		catch (Exception $pError)
		{
			self::writeLogs('PayPalPlusObj.getWebProfileList', $pError);
			$error = true;
		}

		return ['error' => $error, 'data' => $returnData];
	}

	/**
	 * Create a profile and return its ID.
	 * 
	 * @param array $pToken Auth token for server communication.
	 * @param string $pProfileName New profile name.
	 * @param array $pPaypalConfig Taopix Paypal config.
	 * @return string Profile ID.
	 */
	static function createWebprofile($pToken, $pProfileName, $pPaypalConfig)
	{
		$returnID = '';

		try
		{
			$content = self::getProfileJSON($pProfileName, $pPaypalConfig['PLUS_LOGO_IMG'], $pPaypalConfig['PLUS_BRAND_NAME']);
			$queryResult = self::processAPIRequest($pPaypalConfig, '/payment-experience/web-profiles', $pToken, $content, 'POST');

			if (isset($queryResult->id))
			{
				$returnID = $queryResult->id;
			}
		} 
		catch (Exception $pError)
		{
			self::writeLogs('PayPalPlusObj.createWebprofile', $pError);
		}

		return $returnID;
	}

	/**
	 * Update a profile if the brand name or the logo have changed.
	 * 
	 * @param array $pToken Auth token for server communication.
	 * @param object $pProfile Existing profile to be updated.
	 * @param array $pPaypalConfig Taopix Paypal config.
	 * @return boolean True if the process has ran successfully.
	 */
	static function updateWebProfile($pToken, $pProfile, $pPaypalConfig)
	{
		$hasUpdated = true;

		// Make sure the profile needs to be updated.
		if (((! isset($pProfile->presentation)) || (! isset($pProfile->presentation->brand_name)) || (! isset($pProfile->presentation->logo_image))) ||
			(($pProfile->presentation->brand_name !== $pPaypalConfig['PLUS_BRAND_NAME']) || ($pProfile->presentation->logo_image !== $pPaypalConfig['PLUS_LOGO_IMG'])))
		{
			// Update profile
			try
			{
				$content = self::getProfileJSON($pProfile->name, $pPaypalConfig['PLUS_LOGO_IMG'], $pPaypalConfig['PLUS_BRAND_NAME']);
				self::processAPIRequest($pPaypalConfig, '/payment-experience/web-profiles/'. $pProfile->id, $pToken, $content, 'PUT');
			} 
			catch (Exception $pError)
			{
				self::writeLogs('PayPalPlusObj.updateWebProfile', $pError);
				$hasUpdated = false;
			}
		}

		return $hasUpdated;
	}

	/**
	 * Build a profile JSON string.
	 * 
	 * @param string $pName Profile name.
	 * @param string $pLogoPath Path to customer logo.
	 * @param string $pBrandName Brand name.
	 * @return string Profile JSON string.
	 */
	static function getProfileJSON($pName, $pLogoPath, $pBrandName)
	{
		return '{
			"name": "' . $pName . '",
			"presentation": {
				"logo_image": "' . $pLogoPath . '",
				"brand_name": "' . $pBrandName . '"
			},
			"input_fields": {
				"no_shipping": 1,
				"address_override": 0
			}
		}';
	}


	/**
	 * Check if a webhook exits at Paypal server side, if not create it.
	 * 
	 * @param string $pHookName Webhook name.
	 * @param array $pToken Auth token for server communication.
	 * @param array $pPaypalConfig Taopix Paypal config.
	 * @return boolean Execution result.
	 */
	static function getWebHook($pHookName, $pToken, $pPaypalConfig)
	{
		global $gSession;

		// search existing webhooks
		$error = false;
		$webHookExists = false;
		$webHookPath = str_replace('http://', 'https://', UtilsObj::correctPath($gSession['webbrandwebroot'])) . 'PaymentIntegration/PayPalPlus/PayPalPlusCallback.php';
		$existingWebHooksResult = self::getWebHookList($pToken, $pPaypalConfig);
		$error = $existingWebHooksResult['error'];

		if (! $error)
		{
			foreach ($existingWebHooksResult['data']->webhooks as $webHookData)
			{
				if (($webHookData->url === $webHookPath))
				{
					foreach ($webHookData->event_types as $event)
					{
						if ($event->name == $pHookName)
						{
							$webHookExists = true;
							break;
						}
					}
				}
			}
		}

		// Event doesn't exist, so create it.
		if ((! $error) && (! $webHookExists))
		{
			$error = self::createWebHook($pHookName, $webHookPath, $pToken, $pPaypalConfig);
		}

		return $error;
	}

	/**
	 * Get a list of existing webhook for the paypal app.
	 * 
	 * @param array $pToken Auth token for server communication.
	 * @param array $pPaypalConfig Taopix Paypal config.
	 * @return array A list of existing webHook and error.
	 */
	static function getWebHookList($pToken, $pPaypalConfig)
	{
		$returnData = [];
		$error = false;

		try
		{
			$returnData = self::processAPIRequest($pPaypalConfig, '/notifications/webhooks', $pToken, null, 'GET');
		} 
		catch (Exception $pError)
		{
			self::writeLogs('PayPalPlusObj.getWebHookList', $pError);
			$error = true;
		}

		return ['error' => $error, 'data' => $returnData];
	}

	/**
	 * Create a webhook at Paypal server side.
	 * 
	 * @param string $pHookName WebHook name.
	 * @param string $pWebHookPath WebHook URL.
	 * @param array $pToken Auth token for server communication.
	 * @param array $pPaypalConfig Taopix Paypal config.
	 * @return boolean Execution result.
	 */
	static function createWebHook($pHookName, $pWebHookPath, $pToken, $pPaypalConfig)
	{
		$error = false;

		try
		{
			// webhook URL must be HTTPS
			$content = '{"url": "'. $pWebHookPath . '","event_types": [{"name": "' . $pHookName . '"}]}';
			self::processAPIRequest($pPaypalConfig, '/notifications/webhooks', $pToken, $content, 'POST');
		} 
		catch (Exception $pError)
		{
			self::writeLogs('PayPalPlusObj.createWebhook', $pError);
			$error = true;
		}

		return $error;
	}

	/**
	 * Process a payment and return payment details.
	 * 
	 * @return array An array containing payment data.
	 */
	static function processPayment()
	{
		global $gSession;

		$error = false;
		$resultArray = array(
			'error' => false,
			'errorparam' => '',
			'requestid' => '',
			'transactionid' => '',
			'intent' => '',
			'state' => '',
			'cartid' => '',
			'paymentmethod' => '',
			'paymentstatus' => '',
			'status' => '',
			'payerid' => '',
			'payeremail' => '',
			'amount' => '',
			'currency' => '',
			'description' => '',
			'invoicenumber' => ''
		);

		$paypalConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayPal.conf', $gSession['order']['currencycode'], $gSession['webbrandcode'], 'PAYPAL');

		// Get auth token.
		$getTokenResult = self::getToken($paypalConfig);
		$authToken = $getTokenResult['data'];

		if ($getTokenResult['error'])
		{
			$error = true;
			$errorParam = 'Unbale to get auth token';
		}

		if (! $error)
		{
			$paymentID = UtilsObj::getGETParam('paymentId');

			// Get the payment.
			$getPaymentResult = self::getPayment($paypalConfig, $paymentID, $authToken);
			$error = $getPaymentResult['error'];
			$errorParam = $getPaymentResult['errorparam'];
			$paymentResult = $getPaymentResult['payment'];
		}

		// if state == created then this is the first time we have returned to the shopping cart
		// only execute payment the first time or we will catch an error about the payment already being completed
		if ((! $error) && ($paymentResult->state === 'created'))
		{
			// Execute the payment.
			$paymentExecResult = self::executePayment($paypalConfig, $authToken);
			$error = $paymentExecResult['error'];
			$errorParam = $paymentExecResult['errorparam'];

			if (! $error)
			{
				// Get the updated payment.
				$getPaymentResult = self::getPayment($paypalConfig, $paymentID, $authToken);
				$error = $getPaymentResult['error'];
				$errorParam = $getPaymentResult['errorparam'];
				$paymentResult = $getPaymentResult['payment'];
			}
		}

		if (! $error)
		{
			// Build return payment data.
			$payer = $paymentResult->payer;
			$transactions = $paymentResult->transactions;
			$transaction = $transactions[0];

			$resultArray['transactionid'] = $paymentResult->id;
			$resultArray['intent'] = $paymentResult->intent;
			$resultArray['state'] = $paymentResult->state;
			$resultArray['cartid'] = $paymentResult->cart;
			$resultArray['paymentmethod'] = $payer->payment_method;
			$resultArray['paymentstatus'] = $transaction->related_resources[0]->sale->state;
			$resultArray['status'] = $payer->status;
			$resultArray['payerid'] = $payer->payer_info->payer_id;
			$resultArray['payeremail'] = $payer->payer_info->email;
			$resultArray['amount'] = $transaction->amount->total;
			$resultArray['currency'] = $transaction->amount->currency;
			$resultArray['description'] = $transaction->description;
			$resultArray['bankinginstruction'] = array(
				'bankname' => '',
				'accountholdername' => '',
				'internationalbankaccountnumber' => '',
				'bankidentifiercode' => '',
				'paymentduedate' => '',
				'reference' => ''
			);

			// Add extra details for upon invoice.
			if (strtolower($resultArray['paymentmethod']) == 'pay_upon_invoice')
			{
				$paymentInstruction = $paymentResult->payment_instruction;
				$recipientBankingInstruction = $paymentInstruction->recipient_banking_instruction;
				$resultArray['bankinginstruction']['bankname'] = $recipientBankingInstruction->bank_name;
				$resultArray['bankinginstruction']['accountholdername'] =  $recipientBankingInstruction->account_holder_name;
				$resultArray['bankinginstruction']['internationalbankaccountnumber'] = $recipientBankingInstruction->international_bank_account_number;
				$resultArray['bankinginstruction']['bankidentifiercode'] = $recipientBankingInstruction->bank_identifier_code;
				$resultArray['bankinginstruction']['paymentduedate'] = $paymentInstruction->payment_due_date;
				$resultArray['bankinginstruction']['reference'] = $paymentInstruction->reference_number;
			}
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;

		return $resultArray;
	}

	/**
	 * Return payment data.
	 * 
	 * @param array $pPaypalConfig Taopix Paypal config.
	 * @param string $pPaymentID Payment ID.
	 * @param array $pToken Auth token for communictaion.
	 * @return array An array with errors and payment object.
	 */
	static function getPayment($pPaypalConfig, $pPaymentID, $pToken)
	{
		$error = false;
		$errorParam = '';
		$payment = null;

		try
		{
			// get the payment object using the paymentID
			$payment = self::processAPIRequest($pPaypalConfig, '/payments/payment/'. $pPaymentID, $pToken, null, 'GET');
		}
		catch (Exception $ex)
		{
			self::writeLogs('PayPalPlusObj.getPayment', $ex);

			$error = true;
			$errorParam = $ex->getMessage();
		}

		return ['error' => $error, 'errorparam' => $errorParam, 'payment' => $payment];
	}

	/**
	 * Execute a payment from a manual callback.
	 * 
	 * @param array $pPaypalConfig Taopix Paypal config.
	 * @param array $pToken Auth token for communictaion.
	 * @return array An array with errors.
	 */
	static function executePayment($pPaypalConfig, $pToken)
	{
		$error = false;
		$errorParam = '';

		try
		{
			// get the payment object using the paymentID
			$paymentID = UtilsObj::getGETParam('paymentId');

			$content = '{
				"payer_id": "' .  UtilsObj::getGETParam('PayerID') . '"
			  }';

			self::processAPIRequest($pPaypalConfig, '/payments/payment/'. $paymentID .'/execute', $pToken, $content, 'POST');
		}
		catch (Exception $ex)
		{
			self::writeLogs('PayPalPlusObj.executePayment', $ex);

			$error = true;
			$errorParam = $ex->getMessage();
		}

		return ['error' => $error, 'errorparam' => $errorParam];
	}

	/**
	 * Return a string from a response code.
	 * 
	 * @param string $pResponseCode Code to get the string for.
	 * @return string Response code description.
	 */
	static function getResponseDescription($pResponseCode)
	{
		global $gSession;

		$smarty = SmartyObj::newSmarty('CreditCardPayment', '', '', $gSession['browserlanguagecode']);
		$response = '';

		switch ($pResponseCode)
		{
			case 'created':
			{
				$response = $smarty->get_config_vars('str_OrderPayPalPlusCreated');
				break;
			}
			case 'approved':
			{
				$response = $smarty->get_config_vars('str_OrderPayPalPlusApproved');
				break;
			}
			case 'pending':
			{
				$response = $smarty->get_config_vars('str_OrderPayPalPlusPending');
				break;
			}
			case 'failed':
			{
				$response = $smarty->get_config_vars('str_OrderPayPalPlusFailed');
				break;
			}
			case 'partially_completed':
			{
				$response = $smarty->get_config_vars('str_OrderPayPalPlusPartial');
				break;
			}
			case 'in_progress':
			{
				$response = $smarty->get_config_vars('str_OrderPayPalPlusInProgress');
				break;
			}
			case 'cancelled':
			{
				$response = $smarty->get_config_vars('str_OrderPayPalPlusCancelled');
				break;
			}
			case 'noresponse':
			{
				$response = $smarty->get_config_vars('str_OrderPayPalPlusNoResponse');
				break;
			}
			case 'completed':
			{
				$response = $smarty->get_config_vars('str_OrderPayPalPlusCompleted');
				break;
			}
		}

		return $response;
	}

	/**
	 * Return order currency details.
	 * 
	 * @return array An array of currency details.
	 * 		'ordercurrency' => New order currency
	 * 		'currencyexchanged' => True if the curreny has changed
	 *  	'exchangerate' => Currecny exchange rate
	 *  	'decimalplaces' => Currency decimal places
	 */
	static function getOrderCurrency()
    {
        global $gSession;

        $resultArray = [];
        $orderCurrency = $gSession['order']['currencycode'];
        $decimalPlaces = $gSession['order']['currencydecimalplaces'];
        $currencyExchanged = false;
        $exchangeRate = $gSession['order']['currencyexchangerate'];
		$currencyTransform = '';

		$paypalConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayPal.conf', $gSession['order']['currencycode'], $gSession['webbrandcode'], 'PAYPAL');

        if (isset($paypalConfig['CURRENCYTRANSFORM']))
        {
        	$currencyTransform = $paypalConfig['CURRENCYTRANSFORM'];
        }

        if (($currencyTransform !== '') && ($currencyTransform !== $orderCurrency))
        {
            $orderCurrency = $currencyTransform;
            $currencyExchanged = true;

            $currencyItem = DatabaseObj::getCurrency($orderCurrency);

            if ($currencyItem['result'] == '')
            {
                $decimalPlaces = $currencyItem['decimalplaces'];

                if ($exchangeRate !== 0)
                {
					$exchangeRate = $currencyItem['exchangerate'] / $exchangeRate;
                }
				
				// Don't want to divide by zero
                if ($exchangeRate == 0)
                {
                    $orderCurrency = '';
                }
            }
            else
            {
                $orderCurrency = '';
            }
        }

        $resultArray['ordercurrency'] = $orderCurrency;
        $resultArray['currencyexchanged'] = $currencyExchanged;
        $resultArray['exchangerate'] = $exchangeRate;
        $resultArray['decimalplaces'] = $decimalPlaces;

        return $resultArray;
    }

	/**
	 * Return the auth token for server communication.
	 * 
	 * @param array $pPaypalConfig Taopix Paypal config.
	 * @return array An array of token details.
	 * 	'access_token' => The token
	 * 	'token_type' => The token type.
	 */
	static function getToken($pPaypalConfig)
	{
		$error = false;
		$token = ['access_token' => '', 'token_type' => ''];
        $url = $pPaypalConfig['PLUS_API_URL'] . '/oauth2/token';
		$curlSettings = self::getCurlDefaultSettings($pPaypalConfig['PLUS_MODE']);

		$ch = curl_init();
		$curlSettings[CURLOPT_URL] = $url;
		$curlSettings[CURLOPT_HEADER] = false;
		$curlSettings[CURLOPT_POST] = true;
		$curlSettings[CURLOPT_USERPWD] = $pPaypalConfig['PLUS_CLIENTID'] . ":" . $pPaypalConfig['PLUS_CLIENTSECRET'];
		$curlSettings[CURLOPT_POSTFIELDS] = "grant_type=client_credentials";
		curl_setopt_array($ch, $curlSettings);
		$queryResult = curl_exec($ch);
		
		// Detect call error.
		if (curl_errno($ch)) 
		{
			$error = true;
		}

		curl_close($ch);

		if (! empty($queryResult))
		{
			$jsonDecoded = json_decode($queryResult);

			// Make sure there is no error.
			if (isset($jsonDecoded->scope))
			{
				$token = [
					'access_token' => $jsonDecoded->access_token, 
					'token_type' => $jsonDecoded->token_type
				];
			}
		} 
		else 
		{
			$error = true;
		}

		return ['error' => $error, 'data' => $token];
	}
	
	/**
	 * Process a CURL call to the paypal server.
	 * An exception is thrown if the communication has failed. 
	 * 
	 * @param array $pPaypalConfig Taopix Paypal config.
	 * @param string $pApiURLCall URL to call.
	 * @param array $pToken Auth token for communication.
	 * @param mixed $pBody Dat to be sent.
	 * @param string $pMethod Server call method.
	 * @return mixed JSONdecode server response.
	 */
	static function processAPIRequest($pPaypalConfig, $pApiURLCall, $pToken, $pBody, $pMethod)
    {
		$curlSettings = self::getCurlDefaultSettings($pPaypalConfig['PLUS_MODE']);

		$ch = curl_init();
		$curlSettings[CURLOPT_URL] = $pPaypalConfig['PLUS_API_URL'] . $pApiURLCall;

		// Handle call header.
		$request_headers = ["Content-Type: application/json"];

		if ($pToken !== null)
		{
			$request_headers[] = "Authorization: " . $pToken['token_type'] . " " . $pToken['access_token'];
		}
		$curlSettings[CURLOPT_HTTPHEADER] = $request_headers;
		$curlSettings[CURLOPT_CUSTOMREQUEST] = $pMethod;

		if ($pBody !== null)
        {
			$curlSettings[CURLOPT_POSTFIELDS] = $pBody;
        }

		if ($pMethod === 'POST')
		{
			$curlSettings[CURLOPT_POST] = true;
		}

		curl_setopt_array($ch, $curlSettings);
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception("Unable to communicate with the PayPal Plus payment server (" . curl_errno($ch) . "): " . curl_error($ch) . ".");
        }

		curl_close($ch);

		return json_decode($result);
    }

	/**
	 * Return default CURL settings.
	 * 
	 * @param string $pPlusMode SANDBOX or LIVE.
	 * @return array Default CURL settings.
	 */
	static function getCurlDefaultSettings($pPlusMode)
	{
		$curlSettings = [CURLOPT_RETURNTRANSFER => true];

		// Sandbox specific settings.
		if ($pPlusMode === 'SANDBOX')
		{
			$curlSettings[CURLOPT_SSL_VERIFYHOST] = 0;
			$curlSettings[CURLOPT_SSL_VERIFYPEER] = 0;
		}
		else
		{
			$curlSettings[CURLOPT_SSL_VERIFYPEER] = 1;
			$curlSettings[CURLOPT_SSL_VERIFYHOST] = 2;
			$curlSettings[CURLOPT_CAINFO] = UtilsObj::getCurlPEMFilePath();
		}

		return $curlSettings;
	}

	/**
	 * Write data into the log file.
	 * 
	 * @param string $pProcessName Process execution name.
	 * @param mixed $pData Data to be logged.
	 */
	static function writeLogs($pProcessName, $pData)
	{
		global $gSession;

		$paypalConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayPal.conf', $gSession['order']['currencycode'], $gSession['webbrandcode'], 'PAYPAL');
		$serverTimestamp = DatabaseObj::getServerTime();

		PaymentIntegrationObj::logPaymentGatewayData($paypalConfig, $serverTimestamp, $pProcessName, $pData);
	}
}