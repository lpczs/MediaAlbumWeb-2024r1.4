<?php

require __DIR__ . '/../../../libs/external/vendor/autoload.php';

// we must perform the standard initialization as we have bypassed the Fusebox framework
error_reporting(E_ALL);
ini_set('log_errors', true);

// step back to the webroot directory
chdir('../../');

require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/Order_control.php');

// read the config file
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
$automaticCallback = false;

if ((array_key_exists('callback', $_REQUEST)) && ($_REQUEST['callback'] == 'automatic'))
{
	// automatic callback
    $automaticCallback = true;
}
else
{
	// manual callback
	$transactionID = UtilsObj::getGETParam('transactionId');
	$paymentMethodType = UtilsObj::getGETParam('polPaymentMethodType');
	$transactionState = UtilsObj::getGETParam('transactionState');

	// pending transactions will potentially not have a response for several days so we do not want to wait
	if ($transactionState != 7)
	{
		$cciLogExists = false;
		$dbObj = DatabaseObj::getConnection();

		// need to check to see if the automatic callback has been received.
		// wait upto 60 seconds for the automatic callback response
		set_time_limit(120);
		$retryCount = 30;
		while($retryCount > 0)
		{
			// check to see that the order already exists if it does then it is the server to server update.
			if ($dbObj)
			{
				// get the last log entry with this transaction id
				$stmt = $dbObj->prepare('SELECT ccilog.sessionid
											FROM `CCILOG`
												LEFT JOIN orderheader ON (orderheader.id = ccilog.orderid)
											WHERE `transactionid` = ?
											ORDER BY ccilog.datecreated DESC');

				if ($stmt)
				{
					if ($stmt->bind_param('s', $transactionID))
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
}

// get the constants
$gConstants = DatabaseObj::getConstants();

// we will have no session at this point
// ref is already set in GET parameters
$gSession = AuthenticateObj::getCurrentSessionData();

if ($automaticCallback)
{
    if ($gSession['ref'] <= 0)
    {
        global $gDefaultSiteBrandingCode;
        AuthenticateObj::setSessionWebBrand($gDefaultSiteBrandingCode);

        $gSession['order']['ccitype'] = 'PAYU';
        $gSession['ref'] = UtilsObj::getPOSTParam('ref');

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
	$parameters = $_GET;
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