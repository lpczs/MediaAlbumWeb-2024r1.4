<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// Authorize.Net will callback to a pre-determined fixed URL.

// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the constants
$gConstants = DatabaseObj::getConstants();

// get session ID
$_GET['ref'] = UtilsObj::getPOSTParam('MC_ref');

$gSession = AuthenticateObj::getCurrentSessionData();

if ($gSession['ref'] <= 0)
{
    global $gDefaultSiteBrandingCode;
    AuthenticateObj::setSessionWebBrand($gDefaultSiteBrandingCode);
}

$gAuthSession = true;

// see if payment was canceled or successful
$transStatus = UtilsObj::getPOSTParam('transStatus');
switch ($transStatus) 
{
    case 'Y':
		// perform the payment task
		Order_control::ccManualCallback();
        break;
    case 'C':
    default:
		// payment cancelled, continue
		Order_control::ccCancelCallback();
}

?>