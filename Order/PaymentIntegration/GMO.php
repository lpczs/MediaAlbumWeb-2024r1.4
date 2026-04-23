<?php
use Security\ControlCentreCSP;

class GMOObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();
		$currency = $gSession['order']['currencycode'];
		$active = true;

		$smarty = SmartyObj::newSmarty('CreditCardPayment');

        AuthenticateObj::clearSessionCCICookie();

        // read config file
		$GMOConfig = PaymentIntegrationObj::readCCIConfigFile('../config/GMO.conf',$currency,$gSession['webbrandcode']);

		// make sure merchant details are set
		if (($GMOConfig['SERVER'] == '') || ($GMOConfig['SHOPID'] == '') || ($GMOConfig['SHOPPASSWORD'] == ''))
		{
			$active = false;
		}

		// if customer did enable any payment method in the list, don't display the list box
		$paymentMethodList = self::GMOPaymentMethodList();
		$paymentMethod_count = count($paymentMethodList);
		if($paymentMethod_count == 0)
		{
			$active = false;
		}

		// make sure currency is Japanese Yen
		if ($gSession['order']['currencycode'] != 'JPY')
		{
			$active = false;
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

        $resultArray['gateways'] = '';
        $resultArray['active'] = $active;
        $resultArray['form'] = "
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
											newscript.text = 'GMODropdown();';
											creditCardContainer.appendChild(newscript);
										}
							      	}";

        $resultArray['scripturl'] = '';
        $resultArray['script'] = "
								function GMODropdown()
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
									option.appendChild(document.createTextNode('-- ".$smarty->get_config_vars('str_GMOPrompt')." --'));
									selector.appendChild(option);

									// Assign the array of PaymentMethodList from the config file
									GMOPayTypeArray = new Array();
									";

									for( $i = 0; $i < $paymentMethod_count; $i++)
									{
										 $resultArray['script'] .= "GMOPayTypeArray[".$i."]  = new payType('". $paymentMethodList[$i]['name']."', '".$paymentMethodList[$i]['id']."');" ;
									}

									$resultArray['script'] .="

										if (GMOPayTypeArray)
										{
											for (var i = 0; i < GMOPayTypeArray.length; i++)
											{
												var option = document.createElement('option');

												option.value = GMOPayTypeArray[i].id;
												if (option.value == '".$gSession['order']['paymentgatewaycode']."')
												{
													option.selected = 'selected';
												}
												option.appendChild(document.createTextNode(GMOPayTypeArray[i].name));
												selector.appendChild(option);

											}
										}
								}";

        $resultArray['action'] = "validatePayType('" . $smarty->get_config_vars('str_GMOPrompt') . "')";

        return $resultArray;
    }

    static function initialize()
    {
        global $gSession;

        $parameters = Array();

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$GMOConfig = PaymentIntegrationObj::readCCIConfigFile('../config/GMO.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

			$returnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

			$paymentMethodCode = $gSession['order']['paymentgatewaycode'];

			// Force the language to be "en" or "ja" as GMO only support these 2 languages
			if ($gSession['browserlanguagecode'] != 'ja' && $gSession['browserlanguagecode'] != 'ja_jp')
			{
				$langCode = 'en';
			}
			else
			{
				$langCode = 'ja';
			}

			$gmoTemplate = 1;
			if ($gSession['islargescreen'] == true)
            {
            	$gmoTemplate = 1;
            }
            else
            {
            	$gmoTemplate = 2;
            }

            if($GMOConfig['SETSESSION'] == 'T')
            {
                $sessExp = $gSession['sessionexpiredate'];
                $sessExp = new DateTime($sessExp);
                $sessExp = $sessExp->getTimestamp();

                $now = new DateTime();
                $now = $now->getTimestamp();

                $sessRemaining = $sessExp - $now;
                $sessRemaining = $sessRemaining - 900;

                $parameters['SessionTimeout'] = $sessRemaining;
			}

			$receiptsDisp11 = '';
			$receiptsDisp12 = '';

			if ($paymentMethodCode == "UseCvs")
			{
				$receiptsDisp11 = $GMOConfig['BUSINESSNAME'];
				$receiptsDisp12 = $GMOConfig['BUSINESSPHONE'];
			}
			else
			{
				$receiptsDisp11 = $gSession['order']['billingcontactlastname'] . $gSession['order']['billingcontactfirstname'];
				$receiptsDisp12 = $gSession['order']['billingcustomertelephonenumber'];
			}

			$parameters['ShopID'] = $GMOConfig['SHOPID'];
			$parameters['OrderID'] = $gSession['ref']."-".time();
			$parameters['Amount'] = $gSession['order']['ordertotaltopay'];
			$parameters['Currency'] = $gSession['order']['currencycode'];
			$parameters['DateTime'] = date("YmdHis", time());
			$parameters['TemplateNo'] = $gmoTemplate;
			$parameters['Tax'] = 0.00;
			$parameters['RetURL'] = $returnPath;
			$parameters['CancelURL'] = $cancelReturnPath;
			$parameters['Enc'] = $GMOConfig['ENCODING'];
			$parameters['JobCd'] = $GMOConfig['TRANSACTIONTYPE'];
			$parameters['Lang'] = $langCode;
			$parameters['Confirm'] = 0;
			$parameters[$paymentMethodCode] = 1;
			$parameters['ReceiptsDisp11'] = $receiptsDisp11;
			$parameters['ReceiptsDisp12'] = $receiptsDisp12;
			$parameters['ReceiptsDisp13'] = $GMOConfig['BUSINESSHOURS'];
			$parameters['ClientField1'] = $paymentMethodCode;
			$parameters['ClientField2'] = $gSession['webbrandcode'] . '*' . $gSession['order']['currencycode'] . '*' . $gSession['order']['currencydecimalplaces'];
			$parameters['ClientFieldFlag'] = 1;

			$parameters['ShopPassString'] = md5($parameters['ShopID'].$parameters['OrderID'].$parameters['Amount'].$parameters['Tax'].$GMOConfig['SHOPPASSWORD'].$parameters['DateTime']);

			// define Smarty variables
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('payment_url', $GMOConfig['SERVER']);
			$smarty->assign('method', 'POST');
			$smarty->assign('parameter', $parameters);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to Payone
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
        $resultArray['transactionid'] = '';

    	if (isset($_GET['transid']) && $_GET['transid'] != '')
    	{
	    	$resultArray['transactionid'] = $_GET['transid'];
    	}

       	// payment error
		$resultArray['responsecode'] = 'ABORT';
		$resultArray['responsedescription'] = 'Transaction was cancelled.';

        if (array_key_exists('ref', $_GET))
        {
            $ref = $_GET['ref'];
        }
        else
        {
            $ref = $_GET['amp;ref'];
        }


    	$resultArray['ref'] = $ref;
    	$resultArray['authorised'] = false;
    	$resultArray['authorisedstatus'] = 0;


		$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $resultArray['responsecode'];
		$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $resultArray['responsedescription'];
		if ($resultArray['transactionid'] != '')
		{
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $resultArray['transactionid'];
		}
		else
		{
			$resultArray['data3'] = '';
		}

		$resultArray['data4'] = '';
        $resultArray['errorform'] = 'error.tpl';
		$resultArray['showerror'] = true;

    	return $resultArray;
    }

    static function manualCallback()
    {
        global $gSession;

    	// all we have is the session reference
    	$ref = $gSession['ref'];
        if($ref == 0)
        {
            $ref = $_GET["amp;ref"];
        }

		$resultArray = PaymentIntegrationObj::getCciLogEntry($ref);

		$paymentReceived = 0;
		$authorised = false;
		$authorisedStatus = 0;
		$parentLogId = -1;

		if (!empty($resultArray))
		{
			$responseCode = $resultArray['responsecode'];

			// If payment method is convenience store then the responsecode will be empty.
			if ($resultArray['paymentmeans'] == 3)
			{
				$responseCode = $resultArray['responsedescription'];
			}

			switch ($responseCode)
			{
				case 'CAPTURE':
				case 'PAYSUCCESS':
				case 'AUTH':
				case 'SALES':
					$paymentReceived = 1;
					$authorised = true;
					break;
				case 'REQSUCCESS':
					$authorised = true;
					$paymentReceived = 0;
					break;
				default:
					$paymentReceived = 0;
			}

			$authorisedStatus = $resultArray['authorised'];
			$parentLogId = $resultArray['id'];
		}

		$resultArray['authorised'] = $authorised;
		$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['result'] = '';
        $resultArray['ref'] = $ref;
        $resultArray['showerror'] = false;
        $resultArray['paymentreceived'] = $paymentReceived;
		$resultArray['parentlogid'] = $parentLogId;

        return $resultArray;
    }

    static function automaticCallback()
    {
        global $gSession;

        $resultArray = Array();
        $authorised = false;
		$active = true;
		$transactionStatus = '';
        $ref = UtilsObj::getPOSTParam('ref');
        $cvsConfNo = '';
        $cvsReceiptNo = '';
        $cvsCode = '';
		$webbrandcode = $gSession['webbrandcode'];
		$currencyCode = $gSession['order']['currencycode'];
		
		$cciLogEntry = PaymentIntegrationObj::getCciLogEntry($ref);

		if (empty($cciLogEntry))
		{
			// no entry yet, this must be the first callback
			// we do have a session
			$update = false;
			$parentLogId = -1;
			$orderId = -1;
		}
		else
		{
			// we already have an entry, this must be a status update
			// we won't have a session
			$update = ($cciLogEntry['orderid'] > -1);
			$parentLogId = $cciLogEntry['id'];
			$orderId = $cciLogEntry['orderid'];
		}

		$GMOConfig = PaymentIntegrationObj::readCCIConfigFile('../config/GMO.conf',$currencyCode,$webbrandcode);

        // read POST variables
		$accessID = UtilsObj::getPOSTParam('AccessID');
		$tranid = UtilsObj::getPOSTParam('TranID');
		$amount = UtilsObj::getPOSTParam('Amount');
		$transactionDate = UtilsObj::getPOSTParam('TranDate');
		$GMORef = UtilsObj::getPOSTParam('OrderID');	// this ref provided by Taopix
		$accessPassword = UtilsObj::getPOSTParam('AccessPass');
		$approve = UtilsObj::getPOSTParam('Approve');
		$forwarded = UtilsObj::getPOSTParam('Forwarded');
		$paymentMethod = UtilsObj::getPOSTParam('Method');
		$payTimes = UtilsObj::getPOSTParam('PayTimes');
		$checkString = UtilsObj::getPOSTParam('CheckString');
		$paymentType = UtilsObj::getPOSTParam('PayType');

		$returnedStatus = UtilsObj::getPOSTParam('Status');
		$returnedJobCd = UtilsObj::getPOSTParam('JobCd');

		$transactionStatus = $returnedStatus;

		$amount = number_format($amount, $gSession['order']['currencydecimalplaces'], '.', '');

		//Declare the hash variable before it is assigned as payment type may be something other than 0 or 3
		$hash = '';
		$hashMatches = true;

		// Credit card: This is response when customer finish the order, there is a checkstring so compare it with the hash
		if ($checkString && $paymentType == 0)
		{
			$hash = md5($GMORef.$forwarded.$paymentMethod.$payTimes.$approve.$tranid.$transactionDate.$GMOConfig['SHOPPASSWORD']);
			$hashMatches = ($hash == $checkString);
		}
		elseif ($paymentType == '3')
		{
			$hashMatches = true;

			$cvsCode = UtilsObj::getPOSTParam('CvsCode');
			$cvsConfNo = UtilsObj::getPOSTParam('CvsConfNo');
			$cvsReceiptNo = UtilsObj::getPOSTParam('CvsReceiptNo');
			$paymentTerm = UtilsObj::getPOSTParam('PaymentTerm');

			if (empty($cciLogEntry))
			{
				$transactionStatus = 'REQSUCCESS';
			}
		}

		// If the hash matches and the jobcd status is a specific status we can update the order for credit card payments.
		// Convenience store payments doesn't send the jobcd so always let those through.
		if (($hashMatches) && ($returnedJobCd == 'SALES' || $returnedJobCd == 'PAYSUCCESS' || $returnedJobCd == 'CAPTURE' || $paymentType == '3'))
		{
			switch ($transactionStatus)
			{
				case 'CAPTURE':
					$active = true;
					$authorised = true;
					$authorisedStatus = 1;
					$paymentReceived = 1;
					break;
				case 'PAYSUCCESS':
					$active = true;
					$authorised = true;
					$authorisedStatus = 13;
					$paymentReceived = 1;
					break;
				case 'UNPROCESSED':
					$active = true;
					$authorised = false;
					$authorisedStatus = 2;
					$paymentReceived = 0;
					break;
				case 'CHECK':
					$active = true;
					$authorised = true;
					$authorisedStatus = 3;
					$paymentReceived = 0;
					break;
				case 'AUTHENTICATED':
					// The transation status sent is "AUTHENTICATED" when the end-user is redirected to the 3D secure page
					// but they actually haven't completed the payment yet so don't authorise the payment yet.
					$active = true;
					$authorised = false;
					$authorisedStatus = 4;
					$paymentReceived = 0;
					break;
				case 'SAUTH':
					$active = true;
					$authorised = true;
					$authorisedStatus = 5;
					$paymentReceived = 0;
					break;
				case 'AUTH':
					$active = true;
					$authorised = true;
					$authorisedStatus = 6;
					$paymentReceived = 1;
					break;
				case 'SALES':
					$active = true;
					$authorised = true;
					$authorisedStatus = 7;
					$paymentReceived = 1;
					break;
				case 'VOID':
					$active = false;
					$authorised = false;
					$authorisedStatus = 8;
					$paymentReceived = 0;
					break;
				case 'RETURN':
					$active = false;
					$authorised = false;
					$authorisedStatus = 9;
					$paymentReceived = 0;
					break;
				case 'RETURNX':
					$active = false;
					$authorised = false;
					$authorisedStatus = 10;
					$paymentReceived = 0;
					break;
				case 'REQSUCCESS':
					$active = true;
					$authorised = true;
					$authorisedStatus = 12;
					$paymentReceived = 0;
					break;
				default:
					$active = false;
					$authorised = false;
					$authorisedStatus = 0;
					$paymentReceived = 0;
			}

		}
		else
		{
			$active = false;
			$authorised = false;
			$authorisedStatus = 0;
			$paymentReceived = 0;
		}


		// send status change notification email if
		// 1. this is an update
		// 2. the status has indeed changed
		if ($update && $transactionStatus != $cciLogEntry['responsecode'])
		{
			// include the email creation module
    		require_once('../Utils/UtilsEmail.php');

			$offlineConfirmationName = $GMOConfig['OFFLINECONFIRMATIONNAME'];
			$offlineConfirmationEmailAddress = $GMOConfig['OFFLINECONFIRMATIONEMAILADDRESS'];
			if ($offlineConfirmationEmailAddress != '')
			{
				$smarty = SmartyObj::newSmarty('Order');
				$emailContent = $smarty->get_config_vars('str_LabelOrderNumber') . ': ' . $cciLogEntry['ordernumber'] . "\n" .
								$smarty->get_config_vars('str_LabelTransactionID') . ': ' . $cciLogEntry['transactionid'] . "\n" .
								$smarty->get_config_vars('str_LabelStatus') . ': ' . $transactionStatus . "\n\n";
				$emailObj = new TaopixMailer();

				$emailObj->sendTemplateEmail('admin_offlinepaymentupdate', $webbrandcode, '', '', '',
					$offlineConfirmationName, $offlineConfirmationEmailAddress, '', '',
					0,
					Array('data' => $emailContent));
			}
		}


		// write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
		PaymentIntegrationObj::logPaymentGatewayData($GMOConfig, $serverTimestamp);
		$serverDate = date('Y-m-d');
		$serverTime = date('H:i:s');

        $resultArray['result'] = $returnedJobCd;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '000';
        $resultArray['formattedcharges'] = '000';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $GMORef;
        $resultArray['formattedtransactionid'] = $GMORef;
        $resultArray['responsecode'] = $returnedJobCd;
        $resultArray['responsedescription'] = $transactionStatus;
        $resultArray['authorisationid'] = $accessID;
        $resultArray['formattedauthorisationid'] = $accessID;
        $resultArray['bankresponsecode'] = $checkString;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = $approve;
        $resultArray['cvvresponsecode'] = $forwarded;
        $resultArray['paymentcertificate'] = $accessPassword;
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymentmeans'] = $paymentType;
        $resultArray['paymenttime'] = $serverTime;
        $resultArray['paymentreceived'] = $paymentReceived;
        $resultArray['formattedpaymentdate'] = $serverTimestamp;
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = $cvsConfNo;
        $resultArray['payerstatus'] = $cvsReceiptNo;
        $resultArray['payeremail'] = '';
        $resultArray['business'] = $cvsCode;
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = $returnedJobCd;
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


	static function GMOPaymentMethodList()
	{
		global $gSession;

		$smarty = SmartyObj::newSmarty('CreditCardPayment');

		$GMOConfig = PaymentIntegrationObj::readCCIConfigFile('../config/GMO.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

		$enabledMethodList = explode(",", $GMOConfig['GMOGATEWAYS']);

		$fullPaymentMethodList[] = Array("id" => "UseCredit", "name" => $smarty->get_config_vars('str_GMOCreditCard'));
		$fullPaymentMethodList[] = Array("id" => "UseCvs", "name" => $smarty->get_config_vars('str_GMOConvinienceStore'));
		$fullPaymentMethodList[] = Array("id" => "UseEdy", "name" => $smarty->get_config_vars('str_GMOEdy'));
		$fullPaymentMethodList[] = Array("id" => "UseSuica", "name" => $smarty->get_config_vars('str_GMOSuica'));
		$fullPaymentMethodList[] = Array("id" => "UsePayEasy", "name" => $smarty->get_config_vars('str_GMOPayEasy'));
		$fullPaymentMethodList[] = Array("id" => "UsePayPal", "name" => $smarty->get_config_vars('str_GMOPayPal'));
		$fullPaymentMethodList[] = Array("id" => "UseNetid", "name" => $smarty->get_config_vars('str_GMONetid'));
		$fullPaymentMethodList[] = Array("id" => "UseWebMoney", "name" => $smarty->get_config_vars('str_GMOWebMoney'));

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