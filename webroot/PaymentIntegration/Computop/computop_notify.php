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
require_once('../Order/Order_control.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
// get the constants
$gConstants = DatabaseObj::getConstants();

$ComputopConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Computop.conf', 0, 0);
$merchantID = $ComputopConfig['MERCHANTID'];
$password = $ComputopConfig['PASSWORD'];

if (isset($_POST['Data']))
{
	$decrypted_parameters = ComputopObj::decryptData($password, UtilsObj::getPOSTParam('Data'), UtilsObj::getPOSTParam('Len'));
	parse_str($decrypted_parameters, $parsed);

	$parameters = array(
		'Data' => UtilsObj::getPOSTParam('Data'),
		'Len' => UtilsObj::getPOSTParam('Len')
	);
	$ref =  $parsed['UserData'];
}
else
{
	$ref = UtilsObj::getPOSTParam('UserData');
	$code = UtilsObj::getPOSTParam('Code');
	$parameters = array(
		'ref' => UtilsObj::getPOSTParam('UserData'),
		'PayID' => UtilsObj::getPOSTParam('PayID'),
		'XID' => UtilsObj::getPOSTParam('XID'),
		'TransID' => UtilsObj::getPOSTParam('TransID'),
		'mid' => UtilsObj::getPOSTParam('mid'),
		'UserData' => UtilsObj::getPOSTParam('UserData'),
		'Type' => UtilsObj::getPOSTParam('Type'),
		'PayID' => UtilsObj::getPOSTParam('PayID'),
		'Code' => UtilsObj::getPOSTParam('Code'),
		'Status' => UtilsObj::getPOSTParam('Status'),
		'Description' => UtilsObj::getPOSTParam('Description'),
	);
}

$_GET = $parameters;
$_GET['ref'] = $ref;

$gSession = AuthenticateObj::getCurrentSessionData();

Order_control::ccAutomaticCallback();