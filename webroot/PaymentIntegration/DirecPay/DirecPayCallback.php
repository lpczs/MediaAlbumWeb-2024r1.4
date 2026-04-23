<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

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

// get the session (if available)
$gSession = AuthenticateObj::getCurrentSessionData();

if ($gSession['ref'] <= 0)
{
	global $gDefaultSiteBrandingCode;
	AuthenticateObj::setSessionWebBrand($gDefaultSiteBrandingCode);

	$gSession['order']['ccitype'] = 'DirecPay';
	$gSession['ref'] = $_GET['ref'];

	$browserLocale = UtilsObj::getBrowserLocale();
	if ($browserLocale != '')
	{
		$gSession['browserlanguagecode'] = $browserLocale;
	}
}

$gAuthSession = true;

// perform the payment task
Order_control::ccAutomaticCallback();
?>
