<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

global $gSession;

error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');

//PagosOnline uses the same URL for cancel & manual callback, so we have to redirect customer to relevent page according to the data being sent back.

$_GET['ref'] = $_GET['ref_venta'];

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

// get the constants
$gConstants = DatabaseObj::getConstants();

$gSession = AuthenticateObj::getCurrentSessionData();

$gAuthSession = true;

$responseCode = $_GET['estado_pol'];
switch($responseCode)
{
	// redirect to cancel callback URL
	case "1":
	case "2":
	case "5":
	case "6":
	case "8":
	case "9":
		$cancelReturnURL  = UtilsObj::correctPath($gSession['webbrandweburl'])."?fsaction=Order.ccCancelCallback&ref=".$_GET['ref_venta'];
		header("Location: $cancelReturnURL");
	break;

	// redirect to manual callback URL
	case "4":
	case "7":
	case "10":
	case "11":
	case "12":
	case "13":
	case "14":
	case "15":
	case "16":
	  	$successReturnURL = UtilsObj::correctPath($gSession['webbrandweburl'])."?fsaction=Order.ccManualCallback&ref=".$_GET['ref_venta'];
		header("Location: $successReturnURL");
	break;
}

?>