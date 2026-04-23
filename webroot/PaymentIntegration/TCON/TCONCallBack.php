<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// TCON callback in order to retain correct browserlanguagecode

// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');

$postCount = count($_POST);
if ($postCount == 6)
{
	echo "0";
}
else
{
	echo "1";
}

$_GET['ref'] = $_POST['BASKETNO'];

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the constants
$gConstants = DatabaseObj::getConstants();

$gSession = AuthenticateObj::getCurrentSessionData();

$gAuthSession = true;

// perform the payment task
Order_control::ccAutomaticCallback();

?>

