<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/PaymentIntegration/PaymentIntegration.php');
require_once('../Order/PaymentIntegration/Computop.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
// get the constants
$gConstants = DatabaseObj::getConstants();

$ComputopConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Computop.conf', 0, 0);
$merchantID = $ComputopConfig['MERCHANTID'];
$password = $ComputopConfig['PASSWORD'];

if (isset($_GET['Data']))
{
	$decrypted_parameters = ComputopObj::decryptData($password, UtilsObj::getGETParam('Data'), UtilsObj::getGETParam('Len'));
	parse_str($decrypted_parameters, $parsed);

	$parameters = array(
		'Data' => UtilsObj::getGETParam('Data'),
		'Len' => UtilsObj::getGETParam('Len')
	);
	$ref =  $parsed['UserData'];
}
else
{
	$ref = UtilsObj::getGETParam('UserData');
	$code = UtilsObj::getGETParam('Code');
	$parameters = array(
		'ref' => UtilsObj::getGETParam('UserData'),
		'PayID' => UtilsObj::getGETParam('PayID'),
		'XID' => UtilsObj::getGETParam('XID'),
		'TransID' => UtilsObj::getGETParam('TransID'),
		'mid' => UtilsObj::getGETParam('mid'),
		'UserData' => UtilsObj::getGETParam('UserData'),
		'Type' => UtilsObj::getGETParam('Type'),
		'PayID' => UtilsObj::getGETParam('PayID'),
		'Code' => UtilsObj::getGETParam('Code'),
		'Status' => UtilsObj::getGETParam('Status'),
		'Description' => UtilsObj::getGETParam('Description'),
	);
}
$_GET['ref'] = $ref;

$gSession = AuthenticateObj::getCurrentSessionData();

$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $ref;

$smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

// Pass data in an array
$parameters['ref'] = $ref;

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