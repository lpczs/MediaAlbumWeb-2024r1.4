<?php

class SagePayObj
{
	static function configure()
	{
		$resultArray = array();

		AuthenticateObj::clearSessionCCICookie();

		$resultArray['active'] = true;
		$resultArray['form'] = '';
		$resultArray['scripturl'] = '';
		$resultArray['script'] = '';
		$resultArray['action'] = '';

		return $resultArray;
	}

	static function initialize()
	{
		global $gSession;

		$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		// first check if we have any ccidata. this is set when the call is made the first time.
		// if the data is set then the user must have hit the back button on their browser
		if ($gSession['order']['ccidata'] == '') {
			$SagePayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/SagePay.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

			$normalReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

			$basket = '';

			$basket = '2:' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true) . ':' . $gSession['items'][0]['itemqty'] . ':::::';
			$basket .= LocalizationObj::getLocaleString($gSession['shipping'][0]['shippingmethodname'], $gSession['browserlanguagecode'], true) . ':::::';

			$vendorName = $SagePayConfig['VENDORNAME'];
			$vendorEmail = $SagePayConfig['VENDOREMAIL'];
			$transactionType = $SagePayConfig['TRANSACTIONTYPE'];

			$intRandNum = rand(0, 32000) * rand(0, 32000);

			if (array_key_exists('TRANSPREFIX', $SagePayConfig)) {
				if ($SagePayConfig['TRANSPREFIX'] == '') {
					$vendorTxCode = $vendorName . $gSession['ref'] . $intRandNum;
				} else {
					$transactionPrefix = UtilsObj::LeftChars($SagePayConfig['TRANSPREFIX'], 20);

					$vendorTxCode = $transactionPrefix . $gSession['ref'] . $intRandNum;
				}
			} else {
				$vendorTxCode = $vendorName . $gSession['ref'] . $intRandNum;
			}

			// build the Sage Pay data

			$shippingAddress1 = '';
			$shippingAddress2 = '';
			$billingAddress1 = '';
			$billingAddress2 = '';

			// build shipping address

			if ($gSession['shipping'][0]['shippingcustomeraddress3'] != '') {
				$shippingAddress1 = $gSession['shipping'][0]['shippingcustomeraddress1'];
				self::addAddressLine($shippingAddress1, $gSession['shipping'][0]['shippingcustomeraddress2'], 100);

				$shippingAddress2 = $gSession['shipping'][0]['shippingcustomeraddress3'];
				self::addAddressLine($shippingAddress2, $gSession['shipping'][0]['shippingcustomeraddress4'], 100);
			} else {
				$shippingAddress1 = $gSession['shipping'][0]['shippingcustomeraddress1'];
				$shippingAddress2 = $gSession['shipping'][0]['shippingcustomeraddress2'];
			}

			// build billing address

			$billingAddress1 = $gSession['order']['billingcustomeraddress1'];
			self::addAddressLine($billingAddress1, $gSession['order']['billingcustomeraddress2'], 100);

			if ($gSession['order']['billingcustomeraddress3'] != '') {
				$billingAddress1 = $gSession['order']['billingcustomeraddress1'];
				self::addAddressLine($billingAddress1, $gSession['order']['billingcustomeraddress2'], 100);

				$billingAddress2 = $gSession['order']['billingcustomeraddress3'];
				self::addAddressLine($billingAddress2, $gSession['order']['billingcustomeraddress4'], 100);
			} else {
				$billingAddress1 = $gSession['order']['billingcustomeraddress1'];
				$billingAddress2 = $gSession['order']['billingcustomeraddress2'];
			}

			$sagePayData = 'VendorTxCode=' . $vendorTxCode;
			$sagePayData .= '&Description=' . $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
			// Sagepay requires amount to be in UK price foramt
			$sagePayData .= '&Amount=' . number_format($gSession['order']['ordertotaltopay'], 2, '.', '');
			$sagePayData .= '&Currency=' . $gSession['order']['currencycode'];
			$sagePayData .= '&SuccessURL=' . $normalReturnPath;
			$sagePayData .= '&FailureURL=' . $cancelReturnPath;
			$sagePayData .= '&CustomerName=' . $gSession['order']['billingcontactfirstname'] . ' ' . $gSession['order']['billingcontactlastname'];
			$sagePayData .= '&BillingSurname=' . $gSession['order']['billingcontactlastname'];
			$sagePayData .= '&BillingFirstnames=' . $gSession['order']['billingcontactfirstname'];
			$sagePayData .= '&BillingAddress1=' . $billingAddress1;
			$sagePayData .= '&BillingAddress2=' . $billingAddress2;
			$sagePayData .= '&BillingPostCode=' . $gSession['order']['billingcustomerpostcode'];
			$sagePayData .= '&BillingCity=' . $gSession['order']['billingcustomercity'];
			$sagePayData .= '&BillingCountry=' . $gSession['order']['billingcustomercountrycode'];

			// billingstate is required if country is set to US
			if ($gSession['order']['billingcustomercountrycode'] == 'US') {
				$sagePayData .= '&BillingState=' . $gSession['order']['billingcustomerregioncode'];
			}

			$sagePayData .= '&DeliverySurname=' . $gSession['shipping'][0]['shippingcontactlastname'];
			$sagePayData .= '&DeliveryFirstnames=' . $gSession['shipping'][0]['shippingcontactfirstname'];
			$sagePayData .= '&DeliveryAddress1=' . $shippingAddress1;
			$sagePayData .= '&DeliveryAddress2=' . $shippingAddress2;
			$sagePayData .= '&DeliveryPostCode=' . $gSession['shipping'][0]['shippingcustomerpostcode'];
			$sagePayData .= '&DeliveryCity=' . $gSession['shipping'][0]['shippingcustomercity'];
			$sagePayData .= '&DeliveryCountry=' . $gSession['shipping'][0]['shippingcustomercountrycode'];

			// deliverystate is required if country is set to US
			if ($gSession['shipping'][0]['shippingcustomercountrycode'] == 'US') {
				$sagePayData .= '&DeliveryState=' . $gSession['shipping'][0]['shippingcustomerregioncode'];
			}

			$sagePayData .= '&ContactNumber=' . $gSession['order']['billingcustomertelephonenumber'];
			$sagePayData .= '&Basket=' . $basket;
			$sagePayData .= '&AllowGiftAid=' . $SagePayConfig['ALLOWGIFTAID'];
			$sagePayData .= '&Apply3DSecure=' . $SagePayConfig['APPLY3DSECURE'];

			if ($vendorEmail !== '') {
				$sagePayData .= '&VendorEMail=' . $vendorEmail;
			}

			if ($SagePayConfig['SENDCUSTOMERCONFIRMATION'] == '1') {
				$sagePayData .= '&CustomerEmail=' . $gSession['order']['billingcustomeremailaddress'];
			}

			if ($transactionType !== 'AUTHENTICATE') {
				$sagePayData .= '&ApplyAVSCV2=' . $SagePayConfig['APPLYAVSCV2'];
			}

			$cryptData = bin2hex(self::encryptCrypt($sagePayData, $SagePayConfig['ENCRYPTIONPASSWORD']));

			// Pass data in an array
			$parameters = array(
				'VPSProtocol' => '3.00',
				'TxType' => $transactionType,
				'Vendor' => $vendorName,
				'Crypt' => '@' . $cryptData
			);

			// Assign Smarty variables
			$smarty->assign('parameter', $parameters);
			$smarty->assign('method', 'post');
			$smarty->assign('cancel_url', $cancelReturnPath);

			if ($SagePayConfig['SERVERMODE'] == 'LIVE') {
				$smarty->assign('payment_url', $SagePayConfig['SERVERLIVE']);
			} else {
				$smarty->assign('payment_url', $SagePayConfig['SERVERTEST']);
			}

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to SagePay
			$gSession['order']['ccidata'] = 'start';
			DatabaseObj::updateSession();

			$smarty->cachePage = true; // allow the page to be cached so that the browser back button works correctly
			if ($gSession['ismobile'] == true) {
				$resultArray['template'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest_small.tpl');
				$resultArray['javascript'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest.tpl');
				return $resultArray;
			} else {
				$smarty->displayLocale('order/PaymentIntegration/PaymentRequest_large.tpl');
			}
		} else {
			// the user has clicked the back button
			AuthenticateObj::clearSessionCCICookie();

			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$smarty->assign('payment_url', $cancelReturnPath);

			if ($gSession['ismobile'] == true) {
				$resultArray['template'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest_small.tpl');
				$resultArray['javascript'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest.tpl');
				return $resultArray;
			} else {
				$smarty->displayLocale('order/PaymentIntegration/PaymentRequest_large.tpl');
			}
		}
	}

	static function cancel()
	{
		$resultArray = array();
		$error = '';

		// check to see if the cancel has come from the payment service or the user clicking the browser back button
		// (empty POST array means cancel button)
		if (empty($_POST)) {
			$resultArray = self::processResult();
		} else {
			// if back button pressed we need to do some extra work
			$_GET['crypt'] = $_POST['Crypt'];
			$resultArray = self::processResult();

			// 'fix' some of the fields before they get logged in ccilog
			$pieces = explode('&', $resultArray['amount']);
			$resultArray['amount'] = $pieces[0];
			$pieces = explode('&', $resultArray['formattedamount']);
			$resultArray['formattedamount'] = $pieces[0];
			$pieces = explode('&', $resultArray['transactionid']);
			$resultArray['transactionid'] = $pieces[0];
			$pieces = explode('&', $resultArray['charityflag']);
			$resultArray['charityflag'] = $pieces[0];

			$resultArray['responsecode'] = 'ABORT';
			$resultArray['responsedescription'] = 'TAOPIX: User pressed back button.';
		}

		$resultArray['ref'] = $_GET['ref'];
		$resultArray['authorised'] = false;
		$resultArray['authorisedstatus'] = 0;
		if ($resultArray['responsecode'] == 'ABORT') {
			$resultArray['showerror'] = false;
		} else {
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $resultArray['responsecode'];
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $resultArray['responsedescription'];
			if ($resultArray['transactionid'] != '') {
				$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $resultArray['transactionid'];
			} else {
				$resultArray['data3'] = '';
			}

			$resultArray['data4'] = '';
			$resultArray['errorform'] = 'error.tpl';
			$resultArray['showerror'] = true;
		}

		return $resultArray;
	}

	static function confirm()
	{
		$resultArray = self::processResult();

		return $resultArray;
	}

	static function processResult()
	{
		global $gSession;

		$error = false;
		$errorArray = array();
		$resultArray = array();
		$result = '';
		$showError = false;
		$ref = UtilsObj::getGetParam('ref');
		$authorised = false;
		$authorisedStatus = 0;
		$paymentReceived = 0;
		$sagePayStatus = '';
		$sagePayStatusDetail = '';
		$sagePayVendorTxCode = '';
		$sagePayVPSTxId = '';
		$sagePayTxAuthNo = '';
		$sagePayAmount = '';
		$sagePayAddressResult = '';
		$sagePayPostCodeResult = '';
		$sagePayCV2Result = '';
		$sagePayGiftAid = '';
		$sagePay3DSecureStatus = '';
		$sagePayCardType = '';
		$sagePayCardNo = '';
		$sagePayBankAuthCode = '';

		$SagePayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/SagePay.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

		$cryptData = UtilsObj::getGetParam('crypt');

		$sagePayValues = array();
		$sagePay = self::decodeCrypt($cryptData, $SagePayConfig['ENCRYPTIONPASSWORD']);

		if ($sagePay != '__DECRYPT_ERROR__') {
			parse_str($sagePay, $sagePayValues);

			$sagePayStatus = UtilsObj::getArrayParam($sagePayValues, 'Status');
			$sagePayStatusDetail = UtilsObj::getArrayParam($sagePayValues, 'StatusDetail');
			$sagePayVendorTxCode = UtilsObj::getArrayParam($sagePayValues, 'VendorTxCode');
			$sagePayVPSTxId = UtilsObj::getArrayParam($sagePayValues, 'VPSTxId');
			$sagePayTxAuthNo = UtilsObj::getArrayParam($sagePayValues, 'TxAuthNo');
			$sagePayAmount = UtilsObj::getArrayParam($sagePayValues, 'Amount');
			$sagePayAddressResult = UtilsObj::getArrayParam($sagePayValues, 'AddressResult');
			$sagePayPostCodeResult = UtilsObj::getArrayParam($sagePayValues, 'PostCodeResult');
			$sagePayCV2Result = UtilsObj::getArrayParam($sagePayValues, 'CV2Result');
			$sagePayGiftAid = UtilsObj::getArrayParam($sagePayValues, 'GiftAid');
			$sagePay3DSecureStatus = UtilsObj::getArrayParam($sagePayValues, '3DSecureStatus');
			$sagePayCardType = UtilsObj::getArrayParam($sagePayValues, 'CardType');
			$sagePayCardNo = UtilsObj::getArrayParam($sagePayValues, 'Last4Digits');
			$sagePayBankAuthCode = trim(UtilsObj::getArrayParam($sagePayValues, 'BankAuthCode'));

			if ($sagePayStatus == 'OK') {
				$authorised = true;
				$authorisedStatus = 1;
				$paymentReceived = 1;
			} else {
				$error = true;
				$errorArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': STATUS';
				$errorArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $sagePayStatusDetail;
				$errorArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $ref;
				$errorArray['data4'] = '';
				$authorised = false;
				$authorisedStatus = 0;
				$paymentreceived = 0;
			}
		} else {
			// decrypting the returned values failed
			$error = true;
			$errorArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': CRYPTDECODE';
			$errorArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': Crypt Decrypt failed';
			$errorArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $ref;
			$errorArray['data4'] = '';
			$authorised = false;
			$authorisedStatus = 0;
			// we don't know if the payment went through
			$paymentreceived = 1;
		}

		if ($error) {
			// signature check failed
			$resultArray['data1'] = $errorArray['data1'];
			$resultArray['data2'] = $errorArray['data2'];
			$resultArray['data3'] = $errorArray['data3'];
			$resultArray['data4'] = $errorArray['data4'];
			$resultArray['errorform'] = 'error.tpl';
			$showError = true;
			$authorised = false;
			$authorisedStatus = 0;
		}

		$formatted_payment_date = DatabaseObj::getServerTime();

		//write on logs
		PaymentIntegrationObj::logPaymentGatewayData($SagePayConfig, $formatted_payment_date);

		$formatted_amount = substr($sagePayAmount, 0, strlen($sagePayAmount) - $gSession['order']['currencydecimalplaces']) . '.' .
			substr($sagePayAmount, strlen($sagePayAmount) - $gSession['order']['currencydecimalplaces']);

		$resultArray['authorised'] = $authorised;
		$resultArray['authorisedstatus'] = $authorisedStatus;
		$resultArray['result'] = $result;
		$resultArray['ref'] = $ref;
		$resultArray['amount'] = $sagePayAmount;
		$resultArray['formattedamount'] = $formatted_amount;
		$resultArray['charges'] = '';
		$resultArray['formattedcharges'] = 0.00;
		$resultArray['transactionid'] = $sagePayVendorTxCode;
		$resultArray['responsecode'] = $sagePayStatus;
		$resultArray['responsedescription'] = $sagePayStatusDetail;
		$resultArray['authorisationid'] = $sagePayTxAuthNo;
		$resultArray['bankresponsecode'] = $sagePayBankAuthCode;
		$resultArray['cardnumber'] = $sagePayCardNo;
		$resultArray['formattedcardnumber'] = '';
		$resultArray['cvvflag'] = '';
		$resultArray['cvvresponsecode'] = $sagePayCV2Result;
		$resultArray['paymentcertificate'] = $sagePayVPSTxId;
		$resultArray['paymentdate'] = $formatted_payment_date;
		$resultArray['paymentmeans'] = $sagePayCardType;
		$resultArray['paymenttime'] = '';
		$resultArray['paymentreceived'] = $paymentReceived;
		$resultArray['formattedpaymentdate'] = $formatted_payment_date;
		$resultArray['formattedtransactionid'] = $sagePayVPSTxId;
		$resultArray['formattedauthorisationid'] = $sagePayTxAuthNo;
		$resultArray['addressstatus'] = $sagePayAddressResult;
		$resultArray['postcodestatus'] = $sagePayPostCodeResult;
		$resultArray['payerid'] = '';
		$resultArray['payerstatus'] = '';
		$resultArray['payeremail'] = '';
		$resultArray['business'] = $SagePayConfig['VENDORNAME'];
		$resultArray['receiveremail'] = '';
		$resultArray['receiverid'] = '';
		$resultArray['pendingreason'] = '';
		$resultArray['transactiontype'] = '';
		$resultArray['settleamount'] = '';
		$resultArray['currencycode'] = $gSession['order']['currencycode'];
		$resultArray['webbrandcode'] = $gSession['webbrandcode'];
		$resultArray['charityflag'] = $sagePayGiftAid;
		$resultArray['threedsecurestatus'] = $sagePay3DSecureStatus;
		$resultArray['cavvresponsecode'] = '';
		$resultArray['update'] = false;
		$resultArray['orderid'] = 0;
		$resultArray['parentlogid'] = 0;
		$resultArray['resultisarray'] = false;
		$resultArray['showerror'] = $showError;
		$resultArray['resultlist'] = array();

		return $resultArray;
	}

	/*
	Sage Pay functions
	*/
	static function encryptCrypt($pInString, $pKey)
	{
		$padding = (16 - (strlen($pInString) % 16));
		$InString = $pInString . str_repeat(chr($padding), $padding);

		$output = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $pKey, $InString, MCRYPT_MODE_CBC, $pKey);

		// Return the result
		return $output;
	}

	static function decodeCrypt($pCrypt, $pKey)
	{
		$error = false;
		$returnValue = '__DECRYPT_ERROR__';

		// remove leading '@' symbol
		$crypt = substr($pCrypt, 1);
		$packedCrypt = pack('H*', $crypt);

		$decodedCrypt = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $pKey, $packedCrypt, MCRYPT_MODE_CBC, $pKey);

		if ($decodedCrypt === false) {
			$error = true;
		}

		if (!$error) {
			// remove any padding from the decrypted string
			$blockSize = 16;
			$padChar = ord($decodedCrypt[strlen($decodedCrypt) - 1]);

			if ($padChar > $blockSize) {
				$error = true;
			}

			if (!$error) {
				// check the padding on the cyrpt is what we expect
				if (strspn($decodedCrypt, chr($padChar), strlen($decodedCrypt) - $padChar) != $padChar) {
					$error = true;
				}
			}

			if (!$error) {
				$returnValue = substr($decodedCrypt, 0, (-1 * $padChar));
			}
		}

		return $returnValue;
	}

	static function addAddressLine(&$pSource, $pLine, $pMaxLength)
	{
		$result = false;

		if ($pLine != '') {
			$tempValue = $pSource . " " . $pLine;

			if (strlen($tempValue) <= $pMaxLength) {
				$pSource = $tempValue;
				$result = true;
			}
		} else {
			$result = true;
		}

		return $result;
	}
}
