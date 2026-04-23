<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the constants
$gConstants = DatabaseObj::getConstants();

// include required payment gatway files
require_once('../Order/PaymentIntegration/PaymentIntegration.php');
require_once('../Order/PaymentIntegration/sagePayServer.php');
require_once('../Order/Order_control.php');

// get session data
// ref is passed in the get parameter
$gSession = AuthenticateObj::getCurrentSessionData();

$sagePayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/SagePay.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

$status = 'ERROR';
$statusDetail = '';

$ref = UtilsObj::getGETParam('ref', 0);
$postData = $_POST;

$generateHashResult = SagePayObj::generateHash($postData, strtolower($sagePayConfig['VENDORNAME']), $gSession['sagepayserversecuritykey']);

if ($generateHashResult !== UtilsObj::getPOSTParam('VPSSignature'))
{
	// test signature matches

	$status = 'INVALIDSIG';
	$statusDetail = SmartyObj::getParamValue('CreditCardPayment', 'str_OrderSagePayServerSignaturesDoNotMatch');
}
else if ($ref <= 0)
{
	// test ref is valid

	$status = 'INVALID';
	$statusDetail = 'Invalid session ref';
}
else
{
	// handle errors returned by sagepay
	switch(strtolower(UtilsObj::getPOSTParam('Status')))
	{
		case 'ok':
		case 'ok repeated':
		case 'pending':
		{
			// transaction ok

			$status = 'OK';
			$statusDetail = '';

			break;
		}
		default:
		{
			// there was a problem with the transaction, we need to diplay an error on the manual callback

			$status = UtilsObj::getPOSTParam('Status');
			$statusDetail = UtilsObj::getPOSTParam('StatusDetail');

			break;
		}
	}
}

if ($status != 'INVALIDSIG')
{
	// call the automatic callback to complete the order
	Order_control::ccAutomaticCallback();
}

if ($status == 'OK')
{
	$redirectURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $ref . '&status=OK';
}
else
{
	$redirectURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $ref . '&status=' . $status . '&statusdetail=' . urlencode($statusDetail);
}

// sagepay expects a status and redirecturl to be echo'd back (statusdetail is optional)
echo 'Status=' . $status . "\n\r";
echo 'StatusDetail=' . $statusDetail . "\n\r";
echo 'RedirectURL=' . $redirectURL . "\n\r";