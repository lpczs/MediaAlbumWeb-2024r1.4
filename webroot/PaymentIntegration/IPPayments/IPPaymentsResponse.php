<?php
// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the constants
$gConstants = DatabaseObj::getConstants();

// get session ID
$_POST['ref'] = UtilsObj::getPOSTParam('SessionId');

$gSession = AuthenticateObj::getCurrentSessionData();

if ($gSession['ref'] <= 0)
{
    global $gDefaultSiteBrandingCode;
    AuthenticateObj::setSessionWebBrand($gDefaultSiteBrandingCode);
}

$gAuthSession = true;

if (array_key_exists('ResponseCode', $_POST)) 
{	
	// silent POST has occurred so continue with payment
	Order_control::ccAutomaticCallback();
}
?>