<?php

	require __DIR__ . '/../../../libs/external/vendor/autoload.php';

	// EasyPay2 will only callback to a pre-determined fixed URL
	// TAOPIX includes the session reference in the URL so EasyPay2 must call here 
	// For consistency we attempt to get the session number from the URL even though it won't be there
	
	// we must perform the standard initialization as we have bypassed the Fusebox framework
	error_reporting(E_ALL);
	ini_set('log_errors', true);
	
	// step back to the webroot directory
	chdir('../../');
	
	require_once('../Utils/UtilsCoreIncludes.php');
	require_once('../Order/Order_control.php');
	
	
	if(array_key_exists('TM_MCode', $_POST) && array_key_exists('TM_RefNo', $_POST))
	{
		if ($_POST['TM_RefNo'] != '' && $_POST['TM_MCode'] != '')
		{
			echo "mid=".$_POST['TM_MCode']."&ref=".$_POST['TM_RefNo']."&ack=YES";
		}
	}
	
		
	// read the config file
	$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
	
	// get the constants
	$gConstants = DatabaseObj::getConstants();
	
	
	$_POST['ref'] = $_GET['ref'];
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