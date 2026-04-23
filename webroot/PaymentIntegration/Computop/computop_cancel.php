<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');
require_once('../Order/PaymentIntegration/PaymentIntegration.php');
require_once('../Order/PaymentIntegration/Computop.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
// get the constants
$gConstants = DatabaseObj::getConstants();

$ref = 0;
$status;

$ComputopConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Computop.conf', 0, 0);
$merchantID = $ComputopConfig['MERCHANTID'];
$password = $ComputopConfig['PASSWORD'];

// Check if data is encrypted
if ((isset($_GET['Data'])) || (isset($_POST['data'])) || (isset($_POST['data'])))
{
	$data;
	$length;

	if (isset($_GET['Data']))
	{
		$data = UtilsObj::getGETParam('Data');
		$length = UtilsObj::getGETParam('Len');
	}
	else if(isset($_GET['data']))
	{
		$data = UtilsObj::getGETParam('data');
		$length = UtilsObj::getGETParam('Len');
	}
	else if (isset($_POST['data']))
	{
		$data = UtilsObj::getPOSTParam('data');
		$length = UtilsObj::getPOSTParam('Len');
	}

	$decrypted_parameters = ComputopObj::decryptData($password, $data, $length);
	parse_str($decrypted_parameters, $parsed);

	$parameters = array(
		'Data' => UtilsObj::getGETParam('Data'),
		'Len' => UtilsObj::getGETParam('Len')
	);
	
	if (isset($parsed['UserData']))
	{
		$ref = $parsed['UserData'];
	}
	else
	{
		$ref = '';
	}
	$status = $parsed['Status'];
}
else
{
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

	$ref = UtilsObj::getGETParam('UserData');
	$status = UtilsObj::getGETParam('Status');
}

$gSession = AuthenticateObj::getCurrentSessionData();
$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $ref;
$smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
$_GET['ref'] = $ref;

$gSession = AuthenticateObj::getCurrentSessionData();

$smarty->assign('server', $actionURL);
$smarty->assign('parameter', $parameters);

// Display template
if ($gSession['ismobile'] == true)
{
    $smarty->displayLocale('order/PaymentIntegration/PaymentReturn_small.tpl');
}
else
{
    $smarty->displayLocale('order/PaymentIntegration/PaymentReturn_large.tpl');
}

?>