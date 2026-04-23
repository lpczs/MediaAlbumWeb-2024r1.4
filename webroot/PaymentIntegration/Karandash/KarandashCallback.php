<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the constants
$gConstants = DatabaseObj::getConstants();

$_POST['ref'] = $_POST['secretkey'];
$_GET['ref'] = $_POST['secretkey'];

$gSession = AuthenticateObj::getCurrentSessionData();

$smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

// Pass data in an array
$parameters = array(
    'ref' => $gSession['ref'],
    'SecretKey' => UtilsObj::getPOSTParam('secretkey'),
    'Status' => UtilsObj::getPOSTParam('status')
);

// AssignSmarty variables
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