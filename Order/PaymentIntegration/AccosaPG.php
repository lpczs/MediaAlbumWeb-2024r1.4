<?php

class AccosaPGObj
{
    static function configure()
    {
       global $gSession;

        $resultArray = Array();
        $currencyISONumber = $gSession['order']['currencyisonumber'];
        $active = true;

        // test for Accosa supported currencies
        $currencyList = '036,124,144,392,554,702,756,826,840,978,784';
        if (strpos($currencyList, $gSession['order']['currencyisonumber']) === false)
        {
            $active = false;
        }

        AuthenticateObj::clearSessionCCICookie();

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
        	$AccosaConfig = PaymentIntegrationObj::readCCIConfigFile('../config/AccosaPG.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

			$server = $AccosaConfig['ACCOSASERVER'];
			$pgInstanceID = $AccosaConfig['PGINSTANCEID'];
			$merchantID = $AccosaConfig['MERCHANTID'];
			$hashKey = $AccosaConfig['HAHSKEY'];
			$perform = 'initiatePaymentCapture#sale';
			$orderID = $gSession['ref'] . '_' . time();
			$orderData = $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString(substr($gSession['items'][0]['itemproductname'], 0, 125), $gSession['browserlanguagecode'], true);

			//initialise payment parameters
			// amount in smallest unit, e.g. pence or cents
			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');
			$currencyISONumber = $gSession['order']['currencyisonumber'];

			// generate secure hash.
			$messageHash = $pgInstanceID. "|" . $merchantID . "|" . $perform . "|" . $currencyISONumber . "|" . $amount . "|" . $orderID . "|" . $hashKey . "|";
			$message_hash = "CURRENCY:7:" . base64_encode(sha1($messageHash, true));


			// all of the payment parameters are passed as an array
			$parameters = array(
				'pg_instance_id' => $pgInstanceID,
				'merchant_id' => $merchantID,
				'perform' => $perform,
				'device_category' => '0',
				'currency_code' => $currencyISONumber,
				'amount' => $amount,
				'merchant_reference_no' => $orderID,
				'order_desc' => $orderData,
				'message_hash' => $message_hash

			);

			// define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('parameter', $parameters);
			$smarty->assign('method', "POST");

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to AccosaPG
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
            $smarty->assign('server', $cancelReturnPath);

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
        $resultArray['ref'] = $gSession['ref'];
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
        $result = '';
        $authorised = true;
        $authorisedStatus = 1;
        $showError = false;
        $update = false;

		$AccosaConfig = PaymentIntegrationObj::readCCIConfigFile('../config/AccosaPG.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
		$hashKey = $AccosaConfig['HAHSKEY'];

		// return paramters
		$ref = UtilsObj::getPOSTParam('ref');
		$pg_instance_id = UtilsObj::getPOSTParam('pg_instance_id');
		$merchant_id = UtilsObj::getPOSTParam('merchant_id');
		$transaction_type_code = UtilsObj::getPOSTParam('transaction_type_code');
		$installments = UtilsObj::getPOSTParam('installments');
		$transaction_id = UtilsObj::getPOSTParam('transaction_id');
		$amount = UtilsObj::getPOSTParam('amount');
		$exponent = UtilsObj::getPOSTParam('exponent');
		$currency_code = UtilsObj::getPOSTParam('currency_code');
		$merchant_reference_no = UtilsObj::getPOSTParam('merchant_reference_no');
		$status = UtilsObj::getPOSTParam('status');
		$batch_id = UtilsObj::getPOSTParam('batch_id');
		$approval_code = UtilsObj::getPOSTParam('approval_code');
		$eci_3ds = UtilsObj::getPOSTParam('3ds_eci');
		$cavv_aav_3ds = UtilsObj::getPOSTParam('3ds_cavv_aav');
		$status_3ds = UtilsObj::getPOSTParam('3ds_status');
		$pg_error_code = UtilsObj::getPOSTParam('pg_error_code');
		$pg_error_detail = UtilsObj::getPOSTParam('pg_error_detail');
		$pg_error_msg = UtilsObj::getPOSTParam('pg_error_msg');
		$message_hash = UtilsObj::getPOSTParam('message_hash');

		$formattedAmount = $gSession['order']['ordertotaltopay'];

		// if transaction response code is not 50020 (live transaction) or 50097 (test transaction) then an error has occured.
		if ($status == '50020' || $status == '50097')
		{
			// calculate hash and compare it against the return hash.
			$messageHashBuf= $pg_instance_id . "|" . $merchant_id ."|" . $transaction_type_code . "|" . $installments . "|" . $transaction_id . "|" .
								$amount ."|" . $exponent . "|" . $currency_code . "|" . $merchant_reference_no . "|" . $status . "|" . $eci_3ds . "|" .
								$pg_error_code . "|" . $hashKey . "|";
			$calculatedHash = "13:".base64_encode(sha1($messageHashBuf, true));

			if ($message_hash != $calculatedHash)
			{
				$showError = true;
				$authorised = false;
				$authorisedStatus = 0;
				$displayErrorCode = 'DATA VERIFICATION ERROR';
				$displayErrorMessage = 'This transaction did not pass the data verification check.';
			}
		}
		else
		{
			$showError = true;
			$authorised = false;
			$authorisedStatus = 0;
			$displayErrorCode = $pg_error_code;
			$displayErrorMessage = $pg_error_msg;
		}

		if ($showError)
		{
			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $displayErrorCode;
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $displayErrorMessage;
			$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $transaction_id;
			$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $merchant_reference_no;
			$resultArray['errorform'] = 'error.tpl';
		}

		// write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
		$serverDate = date('Y-m-d');
		$serverTime =  date("H:i:s");

		PaymentIntegrationObj::logPaymentGatewayData($AccosaConfig, $serverTime, $pg_error_msg);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $ref;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $formattedAmount;
        $resultArray['charges'] = '000';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $transaction_id;
        $resultArray['formattedtransactionid'] = $transaction_id;
        $resultArray['responsecode'] = $status;
        $resultArray['responsedescription'] = $pg_error_detail;
        $resultArray['authorisationid'] = $transaction_id;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $transaction_id;
        $resultArray['bankresponsecode'] = $status;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = $transaction_id;
        $resultArray['paymentmeans'] = '';
        $resultArray['paymentdate'] = $serverDate;
        $resultArray['paymenttime'] = $serverTime;
        $resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $serverDate;
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = '';
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = '';
        $resultArray['business'] = '';
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = '';
        $resultArray['settleamount'] = '';
        $resultArray['currencycode'] = $gSession['order']['currencycode'];
        $resultArray['webbrandcode'] = $gSession['webbrandcode'];
        $resultArray['charityflag'] = '';
        $resultArray['threedsecurestatus'] = $status_3ds;
        $resultArray['cavvresponsecode'] = $cavv_aav_3ds;
        $resultArray['update'] = false;
        $resultArray['orderid'] = 0;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = $showError;

        return $resultArray;
    }
}

?>