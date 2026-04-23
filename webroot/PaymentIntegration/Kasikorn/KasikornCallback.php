<?php
// Kasikorn will only callback to a pre-determined fixed URL
// TAOPIX includes the session reference in the URL so Kasikorn must call here 

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

// get the session
// if using the new format grab it from that otherwise grab it from the old format for compatiability purposes
if (array_key_exists('PMGWRESP2', $_POST))
{
	$session = $_POST['PMGWRESP2'];
	$session = substr($session, 32, 12);
}
else
{
	$session = $_POST['PMGWRESP'];
	$session = substr($session, 56, 12);
}

$_GET['ref'] = substr($session, 4);
$gSession = AuthenticateObj::getCurrentSessionData();
if ($gSession['ref'] > 0)
{
    $browserLocale = UtilsObj::getBrowserLocale();
    if ($browserLocale != '')
    {
        $gSession['browserlanguagecode'] = $browserLocale;
    }
}
else
{
    global $gDefaultSiteBrandingCode;
    AuthenticateObj::setSessionWebBrand($gDefaultSiteBrandingCode);
}

$gAuthSession = true;

// perform the payment task
Order_control::ccAutomaticCallback();

?>