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

//MANUAL
if (array_key_exists('params', $_GET));
{
	$params = $_GET['params'];
}

$xml = simplexml_load_string($params);

if ($xml)
{
	$result = (string) $xml->Result;

	$sessionRefPos = strpos($xml->ORef, '_');
	$_GET['ref'] = substr($xml->ORef, 0,$sessionRefPos);

}

$gSession = AuthenticateObj::getCurrentSessionData();

if ($result == 'CANCEL')
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
    'params' => htmlspecialchars($params)
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