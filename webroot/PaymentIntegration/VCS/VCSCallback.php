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

// get the constants
$gConstants = DatabaseObj::getConstants();

// we will have no session at this point
$_GET['ref'] = $_POST['m_1'];
$transactionID = $_POST['p3'];
$gSession = AuthenticateObj::getCurrentSessionData();
$logID = 0;

if ($gSession['ref'] > 0)
{
    $browserLocale = UtilsObj::getBrowserLocale();
    if ($browserLocale != '')
    {
        $gSession['browserlanguagecode'] = $browserLocale;
    }
}

$orderExists = false;

$dbObj = DatabaseObj::getGlobalDBConnection();

// Check to see that the order already exists if it does then it is the server to server update.
if ($dbObj)
{
    // get the last log entry with this transaction id
    $stmt = $dbObj->prepare('SELECT ccilog.id
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
                        if ($stmt->bind_result($logID))
                        {
                            if ($stmt->fetch())
                            {
                                $orderExists = true;
                            }
                        }
                    }
                }
            }
        }
        $stmt->free_result();
        $stmt->close();
        $stmt = null;
    }
    $dbObj->close();
}


if ($orderExists)
{
    // This is an update to the PENDING transaction
    Order_control::ccAutomaticCallback();
}
else
{
    //This is the first attempt for an order.
    // send the user back to TAOPIX from VCS and perform the payment task
    $smarty = SmartyObj::newSmartyFromWebRoot('Order', '../../', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    $actionURL = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
    $smarty->assign('server', $actionURL);
    $smarty->assign('parameter', $_POST);
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