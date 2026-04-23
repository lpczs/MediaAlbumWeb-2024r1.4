<?php
// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the constants
$gConstants = DatabaseObj::getConstants();

$paymentReturnData = base64_decode(UtilsObj::getPOSTParam('gtpay_echo_data'));

$ref = $paymentReturnData;

$transactionID = UtilsObj::getPOSTParam("gtpay_tranx_id");
$txnStatusCode = UtilsObj::getPOSTParam("gtpay_tranx_status_code");
$amount = UtilsObj::getPOSTParam("gtpay_tranx_amt");
$txnResponseMessage = UtilsObj::getPOSTParam("gtpay_tranx_status_msg");
$txnCurrency = UtilsObj::getPOSTParam("gtpay_tranx_curr");
$txnCustomerID = UtilsObj::getPOSTParam("gtpay_cust_id");
$txnGatewayName = UtilsObj::getPOSTParam("gtpay_gway_name");
$gatewayHash = UtilsObj::getPOSTParam("gtpay_verification_hash");

$_GET['ref'] = $ref;

$gSession = AuthenticateObj::getCurrentSessionData();

if ($txnStatusCode == '-2')
{
	$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
}
else
{
	$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
}

$smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

$parameters = array(
    'ref' => $gSession['ref'],
    'gtpay_echo_data' => $ref,
    'gtpay_tranx_id' => $transactionID,
    'gtpay_tranx_status_code' => $txnStatusCode,
    'gtpay_tranx_amt' => $amount,
    'gtpay_tranx_status_msg' => $txnResponseMessage,
    'gtpay_tranx_curr' => $txnCurrency,
    'gtpay_cust_id' => $txnCustomerID,
    'gtpay_gway_name' => $txnGatewayName,
	'gtpay_verification_hash' => $gatewayHash
);

$smarty->assign('server', $actionURL);
$smarty->assign('parameter', $parameters);

if ($gSession['ismobile'] == true)
{
    $smarty->displayLocale('order/PaymentIntegration/PaymentReturn_small.tpl');
}
else
{
    $smarty->displayLocale('order/PaymentIntegration/PaymentReturn_large.tpl');
}