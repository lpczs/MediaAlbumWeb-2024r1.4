<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// GMO callback in order to retain correct browserlanguagecode
// we must perform the standard initialization as we have bypassed the Fusebox framework

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the constants
$gConstants = DatabaseObj::getConstants();
$gSession = AuthenticateObj::getCurrentSessionData();

$returnUrl = '';

if (empty($_POST))
{
	$returnUrl = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
}

header('location: ' . $returnUrl);