<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// mPP will callback to a pre-determined fixed URL.

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

// get session ID
$orderNumber = UtilsObj::getPOSTParam('P_OrderNumber');
$_GET['ref'] = substr($orderNumber, 2);

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

// We are calling automatic call back here, so the order is updated,
// because this is classed as an update even though it is the manual callback.
// The manual callback of PaymentIntegration.php doesn't handle this.
$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccAutomaticCallback&ref=' . $gSession['ref'];

// Pass data in an array
$parameters = array(
    'ref' => $gSession['ref'],
    'final_result' => UtilsObj::getPOSTParam('final_result'),
    'P_MerchantNumber' => UtilsObj::getPOSTParam('P_MerchantNumber'),
    'P_OrderNumber' => UtilsObj::getPOSTParam('P_OrderNumber'),
    'P_Amount' => UtilsObj::getPOSTParam('P_Amount'),
    'P_CheckSum' => UtilsObj::getPOSTParam('P_CheckSum'),
    'final_return_PRC' => UtilsObj::getPOSTParam('final_return_PRC'),
    'final_return_SRC' => UtilsObj::getPOSTParam('final_return_SRC'),
    'final_return_ApproveCode' => UtilsObj::getPOSTParam('final_return_ApproveCode'),
    'final_return_BankRC' => UtilsObj::getPOSTParam('final_return_BankRC'),
    'final_return_ECI' => UtilsObj::getPOSTParam('final_return_ECI'),
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