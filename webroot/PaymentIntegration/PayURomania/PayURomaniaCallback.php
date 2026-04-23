<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

global $gSession;

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');
require_once('../Order/PaymentIntegration/PaymentIntegration.php');

//Read the MAW config file this is needed for the connection to the database
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

//Load in the global constants an order will not be inserted into the orderheader table without these
$gConstants = DatabaseObj::getConstants();

//Get the current session from the ref in the $_GET
$gSession = AuthenticateObj::getCurrentSessionData();

//Get the payment config as we need to get the secret key
$paymentConfig = PaymentIntegrationObj::readCCIConfigFile('../config/payuromania.conf', '', '');

$automaticCallback = false;

/**
*	Takes in a string and returns it's length concantaned with the string e.g. 5TAOPIX
*/

function formatStringForHash($string)
{
	return strlen($string) . $string;
}

/**
* Creates the HMAC Hash that is needed for the <EPAYMENT>
*/

function generateHash($pString, $key)
{
	$b = 64;

	if (strlen($key) > $b) {
		$key = pack('H*', md5($key));
	}

	$key = str_pad($key, $b, chr(0x00));
	$ipad = str_pad('', $b, chr(0x36));
	$opad = str_pad('', $b, chr(0x5c));
	$k_ipad = $key ^ $ipad;
	$k_opad = $key ^ $opad;

	return md5($k_opad . pack('H*', md5($k_ipad . $pString)));
}

//Check to see which request has come in

if ((array_key_exists('callback', $_REQUEST)) && ($_REQUEST['callback'] == 'automatic'))
{
	/**
	* When the automatic from their IPN we need to get the data and build up the hash to send back
	* We then need to get the data and execute the automatic call back to insert the order into the cci table so the manual can read it out
	*/
	
    $automaticCallback = true;
	$date = date('YmdHis');
	
	$hashString = formatStringForHash($_POST['IPN_PID'][0]);
	$hashString .= formatStringForHash($_POST['IPN_PNAME'][0]);
	$hashString .= formatStringForHash($_POST['IPN_DATE']);
	$hashString .= formatStringForHash($date);
	
	global $token;  
	
	global $orderDetails;
	
	$orderDetails = $_POST;
	
	$token = generateHash($hashString, $paymentConfig['SECRETKEY']);
	
	$string = "<EPAYMENT>" . $date . "|" . $token . "</EPAYMENT>";
	echo $string;
			
}
else
{
	//When the manual comes we need to make sure that we have recieved the automatic as no data is sent across with the manual
	$gSession['ref'] = UtilsObj::getGETParam('ref');
	$cciLogExists = false;
	$dbObj = DatabaseObj::getConnection();
	
	set_time_limit(120);
    $retryCount = 30;
   
	//Try 30 times to find the CCILOG record that was inserted by the automatic...
   
   while($retryCount > 0)
    {
        // check to see that the order already exists if it does then it is the server to server update.
        if ($dbObj)
        {
            // get the last log entry with this transaction id
            $stmt = $dbObj->prepare('SELECT ccilog.id
                                        FROM `CCILOG`
                                        WHERE `sessionid` = ?
                                        ORDER BY ccilog.datecreated DESC');

            if ($stmt)
            {
                if ($stmt->bind_param('s', $gSession['ref']))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
								$cciLogExists = true;
                            }
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
        }
		//We have found the cci log record
        if ($cciLogExists)
        {
            $retryCount = 0;
        }
        else
        {
            $retryCount--;
            UtilsObj::wait(2);
        }
    }
    $dbObj->close();

}

/*
* If the automatic callback is true get the details and execute the callback function
*/
if ($automaticCallback)
{
	if ($gSession['ref'] >= 0)
    { 
	  global $gDefaultSiteBrandingCode;
        AuthenticateObj::setSessionWebBrand($gDefaultSiteBrandingCode);

        $gSession['order']['ccitype'] = 'PayURomania';
        
		$gSession['ref'] = UtilsObj::getPOSTParam('REFNOEXT');
		
		$browserLocale = UtilsObj::getBrowserLocale();
        if ($browserLocale != '')
        {
            $gSession['browserlanguagecode'] = $browserLocale;
        } 	
    }

    Order_control::ccAutomaticCallback();
}
else
{
   // this is the first attempt for an order.
    $smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
	$parameters = $_POST;
	$parameters['ref'] = UtilsObj::getGETParam('ref');
    $actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . UtilsObj::getGETParam('ref');

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
}

?>