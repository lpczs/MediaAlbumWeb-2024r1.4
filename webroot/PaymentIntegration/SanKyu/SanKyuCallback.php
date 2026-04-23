<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');
require_once('../Order/PaymentIntegration/PaymentIntegration.php');

// read the TAOPIX config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the constants
$gConstants = DatabaseObj::getConstants();

// get Taopix ref passed back in order to revive gSession.
$refArr = explode('-', $_POST['wpf_transaction_id']);

$_GET['ref'] = $refArr[0];
$_POST['ref'] = $refArr[0];

$gSession = AuthenticateObj::getCurrentSessionData();
if ($gSession['ref'] > 0)
{
	$browserLocale = UtilsObj::getBrowserLocale();
	if ($browserLocale != '')
	{
		$gSession['browserlanguagecode'] = $browserLocale;
	}
}
else
{
	global $gDefaultSiteBrandingCode;
	AuthenticateObj::setSessionWebBrand($gDefaultSiteBrandingCode);
}

// read SanKyu config file
$SanKyuConfig = PaymentIntegrationObj::readCCIConfigFile('../config/SanKyu.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

// if SanKyu sent back no error then continue & validate data
if($_POST['wpf_status'] != 'error')
{
	$sanKyu['signature'] = UtilsObj::getPOSTParam('signature');
	$sanKyu['payment_transaction_channel_token'] = UtilsObj::getPOSTParam('payment_transaction_channel_token');
	$sanKyu['payment_transaction_unique_id'] = UtilsObj::getPOSTParam('payment_transaction_unique_id');
	$sanKyu['wpf_transaction_id'] = UtilsObj::getPOSTParam('wpf_transaction_id');
	$sanKyu['payment_transaction_transaction_type'] = UtilsObj::getPOSTParam('payment_transaction_transaction_type');
	$sanKyu['wpf_status'] = UtilsObj::getPOSTParam('wpf_status');
	$sanKyu['wpf_unique_id'] = UtilsObj::getPOSTParam('wpf_unique_id');
	$sanKyu['notification_type'] = UtilsObj::getPOSTParam('notification_type');

	if ($sanKyu['wpf_status'] == 'approved')
	{
		$hashString = hash('sha512', $sanKyu['wpf_unique_id'] . $SanKyuConfig['APIPWD']);

		if($hashString == $sanKyu['signature'])
		{ 
			// hash and signature match, payment approved
			Order_control::ccAutomaticCallback();
		}
		else
		{ 
			// hash and signature do not match, reject payment
			$errorMessage = urlencode("ABORT-Mismatched Signature.");
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref']."&error=".$errorMessage;
			header('Location:' . $cancelReturnPath);
		}
	}
	else
	{
		// card declined / rejected
		$paymentId = $_POST['wpf_transaction_id'];
		$errorCode = $_POST['wpf_status'];
		$errorMessage = 'Error processing card.';

		$errorMessage = urlencode($errorCode . "-" . $errorMessage);
		$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'] . "&error=" . $errorMessage . "&transid=".$paymentId;
		header('Location:' . $cancelReturnPath);
	}
}
else 
{
	// SanKyu sent back error, redirect customer to cancel page & display error.
	$paymentId = $_POST['wpf_transaction_id'];
	$errorCode = $_POST['code'];
	$errorMessage = $_POST['technical_message'];

	$errorMessage = urlencode($errorCode . "-" . $errorMessage);
	$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'] . "&error=" . $errorMessage . "&transid=".$paymentId;
	header('Location:' . $cancelReturnPath);
}
?>