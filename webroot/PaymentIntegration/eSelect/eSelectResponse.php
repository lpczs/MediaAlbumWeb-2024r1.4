<?php
// eSelect will callback to a pre-determined fixed URL.

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the constants
$gConstants = DatabaseObj::getConstants();

// get session ID
$reply = UtilsObj::getPOSTParam('xml_response');
$reply = str_replace("\r\n",'',$reply);

// read XML
$xml = simplexml_load_string($reply);

if ($xml)
{
	$xmlRef = (string) $xml->rvar_ref;
	$_GET['ref'] = $xmlRef;
}

$reply = base64_encode($reply);

$gSession = AuthenticateObj::getCurrentSessionData();

if ($gSession['ref'] <= 0)
{
    $browserLocale = UtilsObj::getBrowserLocale();
    if ($browserLocale != '')
    {
        $gSession['browserlanguagecode'] = $browserLocale;
    }
    global $gDefaultSiteBrandingCode;
    AuthenticateObj::setSessionWebBrand($gDefaultSiteBrandingCode);
}

$gAuthSession = true;

// send the user back to TAOPIX from eSelect and perform the payment task
$smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

// Pass data in an array
$parameters = array(
    'ref' => $gSession['ref'],
    'xml_response' => $reply
);

// Assign smarty variables
$smarty->assign('server', $actionURL);
$smarty->assign('parameter', $parameters);

if ($gSession['ismobile'] == true)
{
    $smarty->displayLocale('order/PaymentIntegration/PaymentReturn_small.tpl');
}
else
{
    $smarty->displayLocale('order/PaymentIntegration/PaymentReturn_large.tpl');
}

?>