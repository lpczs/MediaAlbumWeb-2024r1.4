<?php

class NABObj
{
    static function configure()
    {
        global $gSession;

		$resultArray = Array();
		$active = true;

        AuthenticateObj::clearSessionCCICookie();

		// test for NAB supported currencies
        if ($gSession['order']['currencyisonumber'] != '036')
        {
            $active = false;
        }

		$resultArray['active'] = $active;
		$resultArray['form'] = '';
        $resultArray['scripturl'] = '';
        $resultArray['script'] = '';
        $resultArray['action'] = '';

        return $resultArray;
    }

    static function initialize()
    {
        global $gConstants;
        global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	// first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
			$NABConfig = PaymentIntegrationObj::readCCIConfigFile('../config/NAB.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$manualCallback = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'].'&payment_reference=&bank_reference=&card_type=&payment_amount=&payment_date=&payment_number=&remote_ip=';
			$automaticCallback = UtilsObj::correctPath($gSession['webbrandwebroot']) . '/PaymentIntegration/NAB/NABcallBack.php?fsaction=AutomaticCallback&ref=' . $gSession['ref'].'&payment_reference=&bank_reference=&card_type=&payment_amount=&payment_date=&payment_number=&remote_ip=';

			$server = $NABConfig['NABSERVER'];
			$vendorName = $NABConfig['NABVENDORNAME'];
			$paymentAlert = $NABConfig['NABPAYMENTALERT'];

			$colourPage = $NABConfig['NABCOLOURPAGE'];
			$colourTable = $NABConfig['NABCOLOURTABLE'];
			$colourText = $NABConfig['NABCOLOURTEXT'];
			$font = $NABConfig['NABFONT'];
			$returnLinkText = $NABConfig['NABRETURNLINKTEXT'];

			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');

			// build transaction id
			$paymentReference = $gSession['ref'] . '_' . time();

			$quantity = $gSession['items'][0]['itemqty'];

			if ($quantity > 1)
			{
				$description = $gSession['items'][0]['itemqty'] . ' x ';
			}
			else
			{
				$description = '';
			}

			$description .= LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
			$description = UtilsObj::encodeString($description);

			// $description is passed as an array key, trim any blank lines otherwise it will be rejected by the gateway.
			$description = trim($description);

			$params = array(
				'description' => $description,
				$description => $amount,
				'gst_exempt_fields' => $description,
				'payment_reference' => $paymentReference,
				'vendor_name' => $vendorName,
				'payment_alert' => $paymentAlert,
				'return_link_url' => $manualCallback,
				'reply_link_url' => $automaticCallback
			);

			// Optional params
			if($colourPage != '') {
				$params['colourpage'] = $colourPage;
			}
			if($colourTable != '') {
				$params['colourtable'] = $colourTable;
			}
			if($colourText != '') {
				$params['colourtext'] = $colourText;
			}
			if($font != '') {
				$params['font'] = $font;
			}
			if($returnLinkText != '') {
				$params['return_link_text'] = $returnLinkText;
			}

			// define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('cancel_url', $cancelReturnPath);
                        $smarty->assign('parameter', $params);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to NAB
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
            // the user has clicked the back button
            AuthenticateObj::clearSessionCCICookie();

            $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
            $smarty->assign('payment_url', $cancelReturnPath);
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
		global $gSession;

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
        global $gSession;

        $resultArray = Array();
        $authorised = true;

        $serverTime = DatabaseObj::getServerTime();

        $NABConfig = PaymentIntegrationObj::readCCIConfigFile('../config/NAB.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
		$server = $NABConfig['NABSERVER'];

		$payment_reference 	= $_GET['payment_reference'];
		$bank_reference 	= $_GET['bank_reference'];
		$card_type 			= $_GET['card_type'];
		$payment_amount 	= $_GET['payment_amount'];
		$payment_date 		= $_GET['payment_date'];
		$payment_number 	= $_GET['payment_number'];
		$remote_ip 			= $_GET['remote_ip'];

		$formatted_payment_date = date('Y-m-d H:i:s',$payment_date);

        // write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
		PaymentIntegrationObj::logPaymentGatewayData($NABConfig, $serverTimestamp);

		$authorisedStatus = 1;
		$paymentReceived = 1;

        $resultArray['authorised'] = $authorised;
        $resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['result'] = '';
        $resultArray['ref'] = $_GET['ref'];
        $resultArray['amount'] = $payment_amount;
        $resultArray['formattedamount'] = $payment_amount;
        $resultArray['charges'] = '0.00';
        $resultArray['formattedcharges'] = 0.00;
        $resultArray['paymentdate'] = $serverTime;
        $resultArray['paymenttime'] = '';
        $resultArray['authorisationid'] = '';
        $resultArray['transactionid'] = $payment_reference;
        $resultArray['paymentmeans'] = $formatted_payment_date;
        $resultArray['addressstatus'] = $payment_date;
        $resultArray['payerid'] = $remote_ip;
        $resultArray['payerstatus'] = $server;
        $resultArray['payeremail'] = '';
        $resultArray['business'] = '';
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = $card_type;
        $resultArray['currencycode'] = $gSession['order']['currencycode'];
		$resultArray['webbrandcode'] = $gSession['webbrandcode'];
		$resultArray['settleamount'] = '';
		$resultArray['paymentreceived'] = $paymentReceived;
        $resultArray['formattedpaymentdate'] = $serverTime;
        $resultArray['formattedtransactionid'] = $payment_reference;
        $resultArray['formattedauthorisationid'] = '';
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['responsecode'] = '';
        $resultArray['bankresponsecode'] = $bank_reference;
        $resultArray['paymentcertificate'] = $payment_number;
        $resultArray['update'] = false;
        $resultArray['orderid'] = 0;
        $resultArray['parentlogid'] = 0;
        $resultArray['responsedescription'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['charityflag'] = '';
        $resultArray['showerror'] = false;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();

        return $resultArray;
    }

}

?>