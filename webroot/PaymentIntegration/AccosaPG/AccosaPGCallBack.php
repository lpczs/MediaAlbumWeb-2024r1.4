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

// get the session ref from the merchant reference number that was orignally sent up
$sessionRefPos = strpos(UtilsObj::getPOSTParam("merchant_reference_no"), '_');
$ref = substr(UtilsObj::getPOSTParam("merchant_reference_no"), 0, $sessionRefPos);

$pgInstanceID = UtilsObj::getPOSTParam("pg_instance_id");
$merchantID = UtilsObj::getPOSTParam("merchant_id");
$transactionTypeCode = UtilsObj::getPOSTParam("transaction_type_code");
$installments = UtilsObj::getPOSTParam("installments");
$transactionID = UtilsObj::getPOSTParam("transaction_id");
$amount = UtilsObj::getPOSTParam("amount");
$exponent = UtilsObj::getPOSTParam("exponent");
$currencyCode = UtilsObj::getPOSTParam("currency_code");
$merchantRefNo = UtilsObj::getPOSTParam("merchant_reference_no");
$status = UtilsObj::getPOSTParam("status");
$batchID = UtilsObj::getPOSTParam("batch_id");
$approval_code = UtilsObj::getPOSTParam("approval_code");
$eci_3ds = UtilsObj::getPOSTParam("3ds_eci");
$cavv_aav_3ds = UtilsObj::getPOSTParam("3ds_cavv_aav");
$status_3ds = UtilsObj::getPOSTParam("3ds_status");
$pgErrorCode = UtilsObj::getPOSTParam("pg_error_code");
$pgErrorDetail = UtilsObj::getPOSTParam("pg_error_detail");
$pgErrorMessage = UtilsObj::getPOSTParam("pg_error_msg");
$messageHash = UtilsObj::getPOSTParam("message_hash");

$_GET['ref'] = $ref;

$gSession = AuthenticateObj::getCurrentSessionData();

if ($status == '50011')
{
	$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
}
else
{
	$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
}

$smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

// Pass data in an array
$parameters = array(
    'ref' => $gSession['ref'],
    'pg_instance_id' => $pgInstanceID,
    'merchant_id' => $merchantID,
    'transaction_type_code' => $transactionTypeCode,
    'installments' => $installments,
    'transaction_id' => $transactionID,
    'amount' => $amount,
    'exponent' => $exponent,
    'currency_code' => $currencyCode,
    'merchant_reference_no' => $merchantRefNo,
    'status' => $status,
    'batch_id' => $batchID,
    'approval_code' => $approval_code,
    '3ds_eci' => $eci_3ds,
    '3ds_cavv_aav' => $cavv_aav_3ds,
    '3ds_status' => $status_3ds,
    'pg_error_code' => $pgErrorCode,
    'pg_error_detail' => $pgErrorDetail,
    'pg_error_msg' => $pgErrorMessage,
    'message_hash' => $messageHash
);

// Assign Smarty variables
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

?>