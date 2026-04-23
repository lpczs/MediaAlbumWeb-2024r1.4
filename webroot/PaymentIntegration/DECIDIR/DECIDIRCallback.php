<?php

// Include vendor autoload.
require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
$sessionRefPos = strpos($_GET['idop'], '_');
$sessionID = substr($_GET['idop'], 0, $sessionRefPos);

// get the constants
$gConstants = DatabaseObj::getConstants();
$_GET['ref'] = $sessionID;
$gSession = AuthenticateObj::getCurrentSessionData();

//This is the first attempt for an order.
$smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $sessionID;

$parameters = array(
    'ref' => $sessionID
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