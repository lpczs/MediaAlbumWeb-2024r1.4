<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/PaymentIntegration/PaymentIntegration.php');
require_once('../Order/PaymentIntegration/Realex.php');
require_once('../Order/Order_control.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
// get the constants
$gConstants = DatabaseObj::getConstants();

// read the Realex config file
$RealexConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Realex.conf', 0, 0);

$action = '';
$result = UtilsObj::getPOSTParam('RESULT');
$message = UtilsObj::getPOSTParam('MESSAGE');
$ref = UtilsObj::getGETParam('ref');
$gSession = AuthenticateObj::getCurrentSessionData();

Order_control::ccAutomaticCallback();

// Redirect user to confirmation page via manual callback
$action = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $ref;
echo '<p>' . SmartyObj::getParamValue('Order', 'str_MessageTransferring') . '</p>';
echo '<form id="tpx-form" action="' . $action . '" method="post"></form>';
echo '<script type="text/javascript">document.getElementById("tpx-form").submit();</script>';