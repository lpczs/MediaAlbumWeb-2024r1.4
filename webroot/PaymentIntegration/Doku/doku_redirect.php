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

$aSession = explode('_', UtilsObj::getGETParam('TRANSIDMERCHANT'));
$_GET['ref'] = $aSession[0];
$gSession = AuthenticateObj::getCurrentSessionData();

if ( UtilsObj::getGETParam('STATUSCODE') != '00')
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
    'status' => UtilsObj::getGETParam('STATUSCODE'),
    'amount' => UtilsObj::getGETParam('AMOUNT')
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