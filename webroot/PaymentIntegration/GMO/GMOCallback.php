<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// GMO callback in order to retain correct browserlanguagecode
// we must perform the standard initialization as we have bypassed the Fusebox framework

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');
require_once('../Order/PaymentIntegration/PaymentIntegration.php');

echo 0;

	$refSentback = explode("-", $_POST['OrderID']);

	$_GET['ref'] = $refSentback[0];
	$_POST['ref'] = $refSentback[0];

	// read the config file
	$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

	// get the constants
	$gConstants = DatabaseObj::getConstants();

	$gSession = AuthenticateObj::getCurrentSessionData();

	if ($gSession['ref'] <= 0)
	{
		$brandDetailsArray = $refSentback = explode("*", $_POST['ClientField2']); 
		$brandCode = $brandDetailsArray[0];
		$currencyCode = $brandDetailsArray[1];
		$currencyDecimalPlaces = $brandDetailsArray[2];

		AuthenticateObj::setSessionWebBrand($brandCode);

		$gSession['order']['ccitype'] = 'GMO';
		$gSession['ref'] = $refSentback[0];
		$gSession['order']['currencycode'] = $currencyCode;
		$gSession['order']['currencydecimalplaces'] = $currencyDecimalPlaces;

		$browserLocale = UtilsObj::getBrowserLocale();
		
		if ($browserLocale != '')
		{
			$gSession['browserlanguagecode'] = $browserLocale;
		}
	}

	$gAuthSession = true;

	if ($_POST['ErrCode'] == '')
	{
		if (($_POST['PayType'] == '0') || ($_POST['PayType'] == '3'))
		{
			// perform the payment task
			Order_control::ccAutomaticCallback();
		}
	}
	else
	{
		$paymentId = $_POST['ref'];
		$errorCode = $_POST['ErrCode'];
		$errorMessage = $_POST['ErrInfo'];

		$errorMessage = urlencode($errorCode . "-" . $errorMessage);
		$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'] . "&error=" . $errorMessage . "&transid=".$paymentId;
		header("Location: ".$cancelReturnPath);
	}


?>