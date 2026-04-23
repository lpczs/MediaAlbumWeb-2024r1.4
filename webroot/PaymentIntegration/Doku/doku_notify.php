<?php
// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');
require_once('../Order/PaymentIntegration/PaymentIntegration.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
// get the constants
$gConstants = DatabaseObj::getConstants();

$sFormatedOrder = UtilsObj::getGETParam('OrderNumber');
$sRetrunWord = UtilsObj::getGETParam('WORDS');
$aSession = explode('_', $sFormatedOrder);
$_GET['ref'] = $aSession[0];
$gSession = AuthenticateObj::getCurrentSessionData();

if ($gSession['ref'] == $_GET['OrderNumber'])
{
	if (UtilsObj::getGETParam('RESULT') == 'Fail')
    {
    	echo 'Stop';
    }
    else
    {
        $aDokuConfig = PaymentIntegrationObj::readCCIConfigFile('../config/doku.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
        $amount = number_format($gSession['order']['ordertotal'], $gSession['order']['currencydecimalplaces'], '.', '');
        $sWord = sha1($amount . $aDokuConfig['MID'] . $aDokuConfig['SHARED_KEY'] . $sFormatedOrder);
        //check validity of crypting value
        if ($sRetrunWord != $sWord)
        {
            if ($gSession['ref'] <= 0)
            {
                global $gDefaultSiteBrandingCode;
                AuthenticateObj::setSessionWebBrand($gDefaultSiteBrandingCode);

                $gSession['order']['ccitype'] = 'DOKU';
                $browserLocale = UtilsObj::getBrowserLocale();
                if ($browserLocale != '')
                {
                    $gSession['browserlanguagecode'] = $browserLocale;
                }
            }
            Order_control::ccAutomaticCallback();	
            echo 'Continue';
        } 
        else 
        {
            echo 'Stop';
        }
    }
   
} 
else 
{
    echo 'Stop';
}
?>