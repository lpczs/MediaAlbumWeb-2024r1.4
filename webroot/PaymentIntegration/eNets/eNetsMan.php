<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// eNets Manual callback.

// We must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

// Step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');
require_once('../Order/PaymentIntegration/eNets/enets2.php');

// Read the config files.
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
$enets_config = UtilsObj::readConfigFile('../config/eNets.conf');

// Get the constants.
$gConstants = DatabaseObj::getConstants();

// Get the message to get the ref.
$enets2 = new Enets2();
$enets2->setSecretKey($enets_config['SECRETKEY']);


$message = urldecode($_POST["message"]);
$payload = json_decode($message, true);

if (! empty($payload['msg']))
{
	$response = $payload['msg'];

	if (isset($response['b2sTxnEndURLParam']))
	{
		// get session ID
		$_GET['ref'] = $response['b2sTxnEndURLParam'];
	}
}


// Try to load the session.
$gSession = AuthenticateObj::getCurrentSessionData();

if ($gSession['ref'] <= 0)
{
    global $gDefaultSiteBrandingCode;
    AuthenticateObj::setSessionWebBrand($gDefaultSiteBrandingCode);

    $browserLocale = UtilsObj::getBrowserLocale();
    if ($browserLocale != '')
    {
        $gSession['browserlanguagecode'] = $browserLocale;
    }
}

$gAuthSession = true;


// This is a manual callback.
$smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
$parameters = $_POST;
$parameters['ref'] = UtilsObj::getGETParam('ref');
$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . UtilsObj::getGETParam('ref');

$smarty->assign('server', $actionURL);
$smarty->assign('parameter', $parameters);

$templateSize = ($gSession['ismobile'] == true) ? 'small' : 'large';
$smarty->displayLocale('order/PaymentIntegration/PaymentReturn_' . $templateSize . '.tpl');

?>