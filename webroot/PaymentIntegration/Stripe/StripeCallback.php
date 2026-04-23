<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');

// read the TAOPIX config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the session ref from the returned meta data
$payload = @file_get_contents('php://input');
$payloadArray = json_decode($payload, true);

$_GET['ref'] = $payloadArray['data']['object']['metadata']['ref'];

// get the constants
$gConstants = DatabaseObj::getConstants();

$gSession = AuthenticateObj::getCurrentSessionData();

$gAuthSession = true;

// perform the payment task
Order_control::ccAutomaticCallback();
?>