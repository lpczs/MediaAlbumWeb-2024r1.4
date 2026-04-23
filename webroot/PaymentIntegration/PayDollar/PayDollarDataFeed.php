<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// PayDollar will only callback to a pre-determined fixed URL
// TAOPIX includes the session reference in the URL so PayDollar must call here 

// echo back to paydollar to say that we have received the notification okay
echo 'OK';

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

// get the session
$_GET['ref'] = $_POST['remark'];
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