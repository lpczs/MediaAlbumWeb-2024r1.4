<?php
// Kasikorn will only return to a URL which does not contain parameters as it added the oid parameter
// TAOPIX includes the session reference in the URL so Kasikorn must call here and we then jump back

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

// get the session
$session = UtilsObj::getPOSTParam('RETURNINV', '');
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

// send the user back to TAOPIX from Kasikorn
$smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

$actionURL = UtilsObj::correctPath($ac_config['WEBURL']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];

$parameters = array(
    'ref', $gSession['ref'],
    'HOSTRESP' => UtilsObj::getPOSTParam('HOSTRESP', ''),
    'RESERVED1' => UtilsObj::getPOSTParam('RESERVED1', ''),
    'AUTHCODE' => UtilsObj::getPOSTParam('AUTHCODE', ''),
    'RETURNINV' => UtilsObj::getPOSTParam('RETURNINV', ''),
    'RESERVED2' => UtilsObj::getPOSTParam('RESERVED2', ''),
    'CARDNUMBER' => UtilsObj::getPOSTParam('CARDNUMBER', ''),
    'AMOUNT' => UtilsObj::getPOSTParam('AMOUNT', ''),
    'THBAMOUNT' => UtilsObj::getPOSTParam('THBAMOUNT', ''),
    'CURISO' => UtilsObj::getPOSTParam('CURISO', ''),
    'FXRATE' => UtilsObj::getPOSTParam('FXRATE', ''),
    'FILLSPACE' => UtilsObj::getPOSTParam('FILLSPACE', '')
);

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