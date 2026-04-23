<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the constants
$gConstants = DatabaseObj::getConstants();

// Load the session data
$_POST['ref'] = UtilsObj::getPOSTParam('merchantReference');
$gSession = AuthenticateObj::getCurrentSessionData();

if ($gSession['ref'] <= 0)
{
	global $gDefaultSiteBrandingCode;
	
	AuthenticateObj::setSessionWebBrand($gDefaultSiteBrandingCode);

	$gSession['order']['ccitype'] = 'Adyen';
	$gSession['ref'] = UtilsObj::getPOSTParam('merchantReference');

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