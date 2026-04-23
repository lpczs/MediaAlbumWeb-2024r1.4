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

// we will have no session at this point
$_GET['ref'] = $_POST['Remark'];

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

$gAuthSession = true;

// send the user back to TAOPIX from iPay88 and perform the payment task
$smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

// Pass data in an array
$parameters = array(
    'ref' => $gSession['ref'],
    'MerchantCode' => UtilsObj::getPOSTParam('MerchantCode', ''),
    'PaymentId' => UtilsObj::getPOSTParam('PaymentId', ''),
    'RefNo' => UtilsObj::getPOSTParam('RefNo', ''),
    'Amount' => UtilsObj::getPOSTParam('Amount', ''),
    'Currency' => UtilsObj::getPOSTParam('Currency', ''),
    'Remark' => UtilsObj::getPOSTParam('Remark', ''),
    'TransId' => UtilsObj::getPOSTParam('TransId', ''),
    'AuthCode' => UtilsObj::getPOSTParam('AuthCode', ''),
    'Status' => UtilsObj::getPOSTParam('Status', ''),
    'ErrDesc' => UtilsObj::getPOSTParam('ErrDesc', ''),
    'Signature' => UtilsObj::getPOSTParam('Signature', '')
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

?>