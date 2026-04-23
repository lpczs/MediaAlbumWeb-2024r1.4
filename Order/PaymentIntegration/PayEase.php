<?php

class PayEaseObj
{

    static function configure()
    {
        global $gSession;

        $resultArray = Array();

        $resultArray['active'] = true;
        $resultArray['form'] = '';
        $resultArray['scripturl'] = '';
        $resultArray['script'] = '';
        $resultArray['action'] = '';

        // test for PayEase supported currencies
        $supportedCurrencies = array('USD', 'CNY', 'RMB');
        $currency = $gSession['order']['currencycode'];
        $resultArray['active'] = (in_array($currency, $supportedCurrencies)) ? true : false;

        AuthenticateObj::clearSessionCCICookie();

        return $resultArray;
    }

    static function initialize()
    {
        global $ac_config;
        global $gConstants;
        global $gSession;

        $result = '';

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        // first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
            $payEaseConfig = PaymentIntegrationObj::readCCIConfigFile('../config/payease.conf', '', '');

            $md5Key = $payEaseConfig['MD5KEY'];
            $md5ExePath = $payEaseConfig['MD5EXEPATH'];
            $v_mid = $payEaseConfig['MERCHANTID'];
            $v_oid = date('Ymd', time()) . '-' . $v_mid . '-' . date('His', time()) . '-' . $gSession['ref'];
            $v_ymd = date('Ymd', time());
            $v_rcvname = $v_mid;
            $v_amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');

            if (($gSession['order']['currencycode'] == 'RMB') || ($gSession['order']['currencycode'] == 'CNY'))
            {
                $v_moneytype = 0;
            }
            else if ($gSession['order']['currencycode'] == 'USD')
            {
                $v_moneytype = 1;
            }
            else
            {
                $result = 'Currency Not Supported';
            }

            if ($result == '')
            {
                $v_url = UtilsObj::correctPath($ac_config['WEBURL']) . '?fsaction=Order.ccManualCallback';

                $sourceData = $v_moneytype . $v_ymd . $v_amount . $v_rcvname . $v_oid . $v_mid . $v_url;
                $v_md5info = self::md5hash($md5ExePath, $sourceData, $md5Key);

                $serverURL = $payEaseConfig['SERVER'];
                $cancelReturnPath = UtilsObj::correctPath($ac_config['WEBURL']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

                $v_orderstatus = $payEaseConfig['ORDERSTATUS'];
            }

            if ($result == '')
            {
                $parameters = array(
                    'v_md5info' => $v_md5info,
                    'v_mid' => $v_mid,
                    'v_oid' => $v_oid,
                    'v_ymd' => $v_ymd,
                    'v_amount' => $v_amount,
                    'v_moneytype' => $v_moneytype,
                    'v_url' => $v_url,
                    'v_orderstatus' => $v_orderstatus
                );
                $smarty->assign('payment_url', $serverURL);
                $smarty->assign('cancel_url', $cancelReturnPath);
                $smarty->assign('method', 'post');
                $smarty->assign('parameter', $parameters);

                AuthenticateObj::defineSessionCCICookie();
                $smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
                $smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

                // set the ccidata to remember we have jumped to payease
                $gSession['order']['ccidata'] = 'start';
                DatabaseObj::updateSession();

                $smarty->cachePage = true; // allow the page to be cached so that the browser back button works correctly
                if ($gSession['ismobile'] == true)
                {
                    $resultArray['template'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest_small.tpl');
                    $resultArray['javascript'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest.tpl');
                    return $resultArray;
                }
                else
                {
                    $smarty->displayLocale('order/PaymentIntegration/PaymentRequest_large.tpl');
                }
            }
            else
            {
                $smarty->assign('error1', 'PayEase Error');
                $smarty->assign('error2', $result);
                $smarty->displayLocale('error.tpl');
            }
        }
        else
        {
            // the user has clicked the back button
            AuthenticateObj::clearSessionCCICookie();

            $cancelReturnPath = UtilsObj::correctPath($ac_config['WEBURL']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
            $smarty->assign('cancel_url', $cancelReturnPath);

            if ($gSession['ismobile'] == true)
            {
                $resultArray['template'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest_small.tpl');
                $resultArray['javascript'] = $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest.tpl');
                return $resultArray;
            }
            else
            {
                $smarty->displayLocale('order/PaymentIntegration/PaymentRequest_large.tpl');
            }
        }
    }

    static function cancel()
    {
        $resultArray = Array();
        $result = '';

        $resultArray['result'] = '';
        $resultArray['ref'] = $_GET['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function confirm()
    {
        global $ac_config;
        global $gSession;

        $resultArray = Array();
        $result = '';

        $payEaseConfig = PaymentIntegrationObj::readCCIConfigFile('../config/payease.conf', '', '');

        $md5Key = $payEaseConfig['MD5KEY'];
        $md5ExePath = $payEaseConfig['MD5EXEPATH'];

        $authorised = false;
        $authorisedStatus = 0;

        $v_oid = $_REQUEST['v_oid'];
        $v_pstatus = $_REQUEST['v_pstatus'];
        $v_pstring = $_REQUEST['v_pstring'];
        $v_pmode = $_REQUEST['v_pmode'];
        $v_md5info = $_REQUEST['v_md5info'];
        $v_amount = $_REQUEST['v_amount'];
        $v_moneytype = $_REQUEST['v_moneytype'];
        $v_md5money = $_REQUEST['v_md5money'];
        $v_sign = $_REQUEST['v_sign'];

        $key = $v_oid . $v_pstatus . $v_pstring . $v_pmode;
        $v_localmd5info = self::md5hash($md5ExePath, $key, $md5Key);

        $key = $v_amount . $v_moneytype;
        $v_localmd5money = self::md5hash($md5ExePath, $key, $md5Key);

        if ($v_localmd5info != $v_md5info)
        {
            $result = "Invalid md5info data";
        }

        if ($result == '')
        {
            if ($v_localmd5money != $v_md5money)
            {
                $result = "Invalid md5money data";
            }
        }

        if ($result == '')
        {
            if ($v_pstatus == 1)
            {
                $authorised = true;
                $authorisedStatus = 2;
            }
            else if ($v_pstatus == 20)
            {
                $authorised = true;
                $authorisedStatus = 1;
            }
            else
            {
                $authorised = false;
                $authorisedStatus = 0;
            }

            $resultArray['showerror'] = false;
        }
        else
        {
            $resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $result;
            if ($v_oid != '')
            {
                $resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $v_oid;
            }
            else
            {
                $resultArray['data2'] = '';
            }
            $resultArray['data3'] = '';
            $resultArray['data4'] = '';
            $resultArray['errorform'] = 'error.tpl';
            $resultArray['showerror'] = true;
        }

        // the session reference is embedded in the v_oid value so retrieve it
        $refArray = explode('-', $v_oid);
        $ref = $refArray[3];

        // load the session
        $gSession = DatabaseObj::getSessionData($ref);

        $formattedPaymentDate = DatabaseObj::getServerTime();

        //write on logs
        PaymentIntegrationObj::logPaymentGatewayData($payEaseConfig, $formattedPaymentDate);

        $resultArray['authorised'] = $authorised;
        $resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $v_amount;
        $resultArray['formattedamount'] = $v_amount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] = 0.00;
        $resultArray['paymentdate'] = $formattedPaymentDate;
        $resultArray['paymenttime'] = '';
        $resultArray['authorisationid'] = '';
        $resultArray['transactionid'] = $v_oid;
        $resultArray['paymentmeans'] = $v_pmode;
        $resultArray['addressstatus'] = '';
        $resultArray['payerid'] = '';
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = '';
        $resultArray['business'] = $payEaseConfig['MERCHANTID'];
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = '';
        $resultArray['currencycode'] = $gSession['order']['currencycode'];
        $resultArray['webbrandcode'] = '';
        $resultArray['settleamount'] = '';
        $resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $formattedPaymentDate;
        $resultArray['formattedtransactionid'] = '';
        $resultArray['formattedauthorisationid'] = '';
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['responsecode'] = $v_pstatus;
        $resultArray['responsedescription'] = $v_pstring;
        $resultArray['bankresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['update'] = false;
        $resultArray['orderid'] = 0;
        $resultArray['parentlogid'] = 0;
        $resultArray['postcodestatus'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['charityflag'] = '';
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();

        return $resultArray;
    }

    static function offlineCallback()
    {
        // include the email creation module
        require_once('../Utils/UtilsEmail.php');

        $resultArray = Array();
        $resultArray['showerror'] = false;
        $result = '';
        $emailContent = '';
        $logID = 0;
        $orderID = 0;
        $sessionID = 0;
        $userID = 0;
        $v_amount = 0.00;
        $business = '';
        $currencyCode = '';
        $orderNumber = 0;

        $callbackList = Array();

        $smarty = SmartyObj::newSmarty('Order', '', '');

        $formattedPaymentDate = DatabaseObj::getServerTime();

        $payEaseConfig = PaymentIntegrationObj::readCCIConfigFile('../config/payease.conf', '', '');

        $md5Key = $payEaseConfig['MD5KEY'];
        $md5ExePath = $payEaseConfig['MD5EXEPATH'];

        $offlineConfirmationName = $payEaseConfig['OFFLINECONFIRMATIONNAME'];
        $offlineConfirmationEmailAddress = $payEaseConfig['OFFLINECONFIRMATIONEMAILADDRESS'];

        $authorised = false;
        $authorisedStatus = 0;

        $v_count = $_REQUEST['v_count'];
        $v_oid = $_REQUEST['v_oid'];
        $v_pmode = $_REQUEST['v_pmode'];
        $v_pstatus = $_REQUEST['v_pstatus'];
        $v_pstring = $_REQUEST['v_pstring'];
        $v_mac = $_REQUEST['v_mac'];
        $v_sign = $_REQUEST['v_sign'];

        $oidArray = explode('|_|', $v_oid);
        $pmodeArray = explode('|_|', $v_pmode);
        $pstatusArray = explode('|_|', $v_pstatus);
        $pstringArray = explode('|_|', $v_pstring);

        $key = $v_oid . $v_pmode . $v_pstatus . $v_pstring . $v_count;
        $v_localvmac = self::md5hash($md5ExePath, $key, $md5Key);

        if ($v_localvmac != $v_mac)
        {
            $result = "Invalid md5info data";
        }

        //write on logs
        PaymentIntegrationObj::logPaymentGatewayData($payEaseConfig, $formattedPaymentDate, $result);

        if ($result == '')
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                // get the last log entry with this transaction id
                if ($stmt = $dbObj->prepare('SELECT ccilog.id, ccilog.sessionid, ccilog.userid, `orderid`, `ordernumber`, `amount`, `business`, ccilog.currencycode
					FROM `CCILOG`
					LEFT JOIN orderheader ON (orderheader.id = ccilog.orderid)
					WHERE `transactionid` = ? ORDER BY ccilog.datecreated DESC'))
                {
                    $itemCount = count($oidArray);
                    for ($i = 0; $i < $itemCount; $i++)
                    {
                        $v_oid = $oidArray[$i];
                        $v_pmode = $pmodeArray[$i];
                        $v_pstatus = $pstatusArray[$i];
                        $v_pstring = $pstringArray[$i];
                        if ($v_pstatus == 0)
                        {
                            $authorised = true;
                            $authorisedStatus = 2;
                        }
                        else if ($v_pstatus == 1)
                        {
                            $authorised = true;
                            $authorisedStatus = 1;
                        }
                        else
                        {
                            $authorised = false;
                            $authorisedStatus = 0;
                        }

                        // the session reference is embedded in the v_oid value so retrieve it
                        $refArray = explode('-', $v_oid);
                        $ref = $refArray[3];

                        $logID = 0;
                        $orderID = 0;

                        if ($stmt->bind_param('s', $v_oid))
                        {
                            if ($stmt->execute())
                            {
                                if ($stmt->bind_result($logID, $sessionID, $userID, $orderID, $orderNumber, $v_amount, $business,
                                                $currencyCode))
                                {
                                    if ($stmt->num_rows > 0)
                                    {
                                        if ($stmt->fetch())
                                        {
                                            $emailContent .= $smarty->get_config_vars('str_LabelOrderNumber') . ': ' . $orderNumber . "\n" . $smarty->get_config_vars('str_LabelTransactionID') .
                                                    ': ' . $v_oid . "\n" . $smarty->get_config_vars('str_LabelStatus') . ': ' . $v_pstatus . ' - ' . $v_pstring . "\n\n";
                                        }
                                        else
                                        {
                                            $error = 'PayEase offlineCallback fetch ' . $dbObj->error;
                                        }
                                    }
                                    else
                                    {
                                        $error = 'PayEase offlineCallback num_rows ' . $dbObj->error;
                                    }
                                }
                                else
                                {
                                    $error = 'PayEase offlineCallback bind result ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $error = 'PayEase offlineCallback execute ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $error = 'PayEase offlineCallback bind params ' . $dbObj->error;
                        }

                        $callbackItem['authorised'] = $authorised;
                        $callbackItem['authorisedstatus'] = $authorisedStatus;
                        $callbackItem['result'] = $result;
                        $callbackItem['ref'] = $ref;
                        $callbackItem['amount'] = $v_amount;
                        $callbackItem['formattedamount'] = $v_amount;
                        $callbackItem['charges'] = '';
                        $callbackItem['formattedcharges'] = 0.00;
                        $callbackItem['paymentdate'] = $formattedPaymentDate;
                        $callbackItem['paymenttime'] = '';
                        $callbackItem['authorisationid'] = '';
                        $callbackItem['transactionid'] = $v_oid;
                        $callbackItem['paymentmeans'] = $v_pmode;
                        $callbackItem['addressstatus'] = '';
                        $callbackItem['payerid'] = '';
                        $callbackItem['payerstatus'] = '';
                        $callbackItem['payeremail'] = '';
                        $callbackItem['business'] = $business;
                        $callbackItem['receiveremail'] = '';
                        $callbackItem['receiverid'] = '';
                        $callbackItem['pendingreason'] = '';
                        $callbackItem['transactiontype'] = '';
                        $callbackItem['currencycode'] = $currencyCode;
                        $callbackItem['webbrandcode'] = '';
                        $callbackItem['settleamount'] = '';
                        $callbackItem['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
                        $callbackItem['formattedpaymentdate'] = $formattedPaymentDate;
                        $callbackItem['formattedtransactionid'] = '';
                        $callbackItem['formattedauthorisationid'] = '';
                        $callbackItem['cardnumber'] = '';
                        $callbackItem['formattedcardnumber'] = '';
                        $callbackItem['cvvflag'] = '';
                        $callbackItem['cvvresponsecode'] = '';
                        $callbackItem['responsecode'] = $v_pstatus;
                        $callbackItem['responsedescription'] = $v_pstring;
                        $callbackItem['bankresponsecode'] = '';
                        $callbackItem['paymentcertificate'] = '';
                        $callbackItem['orderid'] = $orderID;
                        $callbackItem['parentlogid'] = $logID;
                        $callbackItem['postcodestatus'] = '';
                        $callbackItem['threedsecurestatus'] = '';
                        $callbackItem['cavvresponsecode'] = '';
                        $callbackItem['charityflag'] = '';
                        $callbackItem['userid'] = $userID;
                        $callbackItem['update'] = true;

                        array_push($callbackList, $callbackItem);

                        //write on logs
                        PaymentIntegrationObj::logPaymentGatewayData($payEaseConfig, $formattedPaymentDate, $result);

                        $stmt->free_result();
                    }

                    $stmt->close();
                }
                else
                {
                    $error = 'PayEase offlineCallback prepare ' . $dbObj->error;
                }

                $dbObj->close();
            }

            // send an email containing the offline transaction results
            if (($offlineConfirmationEmailAddress != '') && ($emailContent != ''))
            {
                $emailObj = new TaopixMailer();

                $emailObj->sendTemplateEmail('admin_offlinepaymentupdate', '', '', '', '', $offlineConfirmationName,
                        $offlineConfirmationEmailAddress, '', '', 0, Array('data' => $emailContent));
            }

            $resultArray['resultisarray'] = true;
            $resultArray['resultlist'] = $callbackList;
            $resultArray['update'] = true;

            // echo the result back to the payment service
            echo 'sent';
        }
        else
        {
            // the response was not validated so we just return an empty list of transactions back to the server
            $resultArray['resultisarray'] = true;
            $resultArray['resultlist'] = $callbackList;
            $resultArray['update'] = true;

            // echo the result back to the payment service
            echo 'error';
        }

        return $resultArray;
    }

    static function md5hash($pExePath, $pSourceData, $pKey)
    {
        $theResult = Array();

        // escape the | character
        $pSourceData = str_replace('|', '^|', $pSourceData);

        exec('"' . $pExePath . '"' . " $pSourceData $pKey", $theResult, $theResult2);

        return $theResult[0];
    }

}
?>