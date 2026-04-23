<?php
// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the constants
$gConstants = DatabaseObj::getConstants();

// we will have no session at this point
$_GET['ref'] = $_POST['SessionId'];
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

// send the user back to TAOPIX from IPPayments and perform the payment task
$smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

$actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

// Pass data in an array
$parameters = array(
    'ref' => $gSession['ref'],
    'Customernumber' => UtilsObj::getPOSTParam('CustNumber', ''),
    'Customerref' => UtilsObj::getPOSTParam('CustRef', ''),
    'Amount' => UtilsObj::getPOSTParam('Amount', ''),
    'Surcharge' => UtilsObj::getPOSTParam('Surcharge', '')
);

// Assign Smarty variables
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