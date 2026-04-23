<?php
// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');

$sessionRefPos = strpos($_POST['oid'], '_');
$sessionID = substr($_POST['oid'], 0,$sessionRefPos);

$_GET['ref'] = $sessionID;

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the constants
$gConstants = DatabaseObj::getConstants();

$gSession = AuthenticateObj::getCurrentSessionData();

$gAuthSession = true;

// perform the payment task
//Order_control::ccManualCallback();

//This is the first attempt for an order.
$smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
	
$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $sessionID;
$smarty->assign('server', $actionURL);
$smarty->assign('parameters', $_POST);
$smarty->displayLocale('order/PaymentIntegration/INILiteReturn.tpl');

?>
