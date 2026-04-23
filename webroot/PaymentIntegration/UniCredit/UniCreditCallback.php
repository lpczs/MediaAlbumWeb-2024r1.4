<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');

if (strpos($_GET['numeroOrdine'], 'taopix') !== false)
{
    // read the config file
    $ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

    // Get ref
    $refArray = explode('_', $_GET['ORDERNUMBER']);

    // Check option prefix has been set
    if (count($refArray) == 3)
    {
        // Has a prefix
        $ref = $refArray[1];
    } else
    {
        // No prefix
        $ref = $refArray[0];
    }

    // Update GET values
    $_GET['ref'] = $ref;

    // get the constants
    $gConstants = DatabaseObj::getConstants();

    $gSession = AuthenticateObj::getCurrentSessionData();

    $gAuthSession = true;

    // perform the payment task
    Order_control::ccAutomaticCallback();
}