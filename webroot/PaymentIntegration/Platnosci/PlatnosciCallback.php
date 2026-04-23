<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// Platnosci will callback to a pre-determined fixed URL. (automatic callback)
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

// do we have a session reference?
if (!isset($_POST['session_id']))
{
	exit("ERROR");
}

// acknowledge receipt of notification to Platnosci
echo "OK";


$sessionRefArray = explode('_', $_POST['session_id']);
$_GET['ref'] = $sessionRefArray[0];
$gSession = AuthenticateObj::getCurrentSessionData();

/*
 * This gateway supports payments requiring confirmation and in branch payments
 * As the session will likely have expired by the time the payment is confirmed additional logic is required to create the session 
 * The session ref and brand code are pulled from the session_id sent to the payment gateway and all information needed to update the orders table
 * and CCIlog as well as send the confirmation email can be built from these two
 */
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
	//initialise global defaultbrandingcode
	global $gDefaultSiteBrandingCode;
	//set global default branding code based on what is returned from the payment gateway
	$gDefaultSiteBrandingCode = $sessionRefArray[2];
	
	//if the gateway has made a mistake and not sent the branding code properly correctly attempt to build the brandcode from the url
	if ($gDefaultSiteBrandingCode == '')
	{
		if (isset($_SERVER['HTTP_HOST']))
		{
			$host = $_SERVER['HTTP_HOST'];
			$gDefaultSiteBrandingCode = DatabaseObj::getBrandFromWebUrl($host);
		}
	}
	
    AuthenticateObj::setSessionWebBrand($gDefaultSiteBrandingCode);
	
	//set the session ref and payment gateway in the session using what has been returned from the gateway
	$gSession['order']['ccitype'] = 'Platnosci';
    $gSession['ref'] = UtilsObj::getGETParam('ref');

	//set browser locale if relevant
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