<?php
class PayTrailObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();
        $active = true;

        AuthenticateObj::clearSessionCCICookie();

        // test for paytrail supported currencies
        if ($gSession['order']['currencycode'] != 'EUR')
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
			$payTrailConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayTrail.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

			$normalReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'];
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$automaticReturnPath = UtilsObj::correctPath($gSession['webbrandwebroot']) . '/PaymentIntegration/PayTrail/PayTrailCallBack.php?fsaction=AutomaticCallback&ref=' . $gSession['ref'];

			$server = $payTrailConfig['SERVER'];
			$merchant = $payTrailConfig['MERCHANTID'];
			$merchantAuthCode = $payTrailConfig['AUTHCODE'];
			$integrationType = ((isset($payTrailConfig['INTEGRATIONTYPE'])) && ! (empty($payTrailConfig['INTEGRATIONTYPE']))) ? $payTrailConfig['INTEGRATIONTYPE'] : 'S1';

			// get language code and see if it is supported by paytrail
			$locale = strtolower($gSession['browserlanguagecode']);
			$locale = substr($locale, 0, 2);

			$languageList = 'fi,se,en,ru';
			if (strpos($languageList, $locale) === false)
			{
				$displayLang = 'fi_FI';
			}
			else
			{
				if($locale == 'fi')
				{
					$locale = 'fi_FI';
				}
				elseif($locale == 'se')
				{
					$locale = 'sv_SE';
				}
				elseif($locale == 'en')
				{
					$locale = 'en_US';
				}
				elseif($locale == 'ru')
				{
					$locale = 'ru_RU';
				}

				$displayLang = $locale;
			}

			$orderText = $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
			$description = UtilsObj::encodeString($orderText, false);
			$orderID = $gSession['ref'];
			$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');
			$currency = $gSession['order']['currencycode'];

			// build parameters to generate secure hash with
			// common parameters
			 $hashParameters = array(
				'AUTHCODE' => $merchantAuthCode,
				'MERCHANT_ID' => $merchant,
				'AMOUNT' => $amount,
				'ORDER_NUMBER' => $orderID,
				'REFERENCE_NUMBER' => '',
				'ORDER_DESCRIPTION' => $description,
				'CURRENCY'  => $currency,
				'RETURN_ADDRESS' => $normalReturnPath,
				'CANCEL_ADDRESS' => $cancelReturnPath,
				'PENDING_ADDRESS' => '', // unused in revision version 4.1
				'NOTIFY_ADDRESS' => $automaticReturnPath,
				'TYPE' => strtoupper($integrationType),
				'CULTURE' => $displayLang,
				'PRESELECTED_METHOD' => '',
				'MODE' => 1,
				'VISIBLE_METHODS' => '',
				'GROUP' => ''
			);

			// e1 integration type only parameters
			if (strtolower($integrationType) == 'e1')
			{
				unset($hashParameters['AMOUNT']);
				$address = trim($gSession['order']['billingcustomeraddress1'] . ' ' . $gSession['order']['billingcustomeraddress2'] . ' ' . $gSession['order']['billingcustomeraddress3'] . ' ' . $gSession['order']['billingcustomeraddress4']);
				$hashParameters['CONTACT_TELNO'] = $gSession['order']['billingcustomertelephonenumber'];
				$hashParameters['CONTACT_CELLNO'] = '';
				$hashParameters['CONTACT_EMAIL'] = $gSession['order']['billingcustomeremailaddress'];
				$hashParameters['CONTACT_FIRSTNAME'] = $gSession['order']['billingcontactfirstname'];
				$hashParameters['CONTACT_LASTNAME'] = $gSession['order']['billingcontactlastname'];
				$hashParameters['CONTACT_COMPANY'] = $gSession['order']['billingcustomername'];
				$hashParameters['CONTACT_ADDR_STREET'] = $address;
				$hashParameters['CONTACT_ADDR_ZIP'] = $gSession['order']['billingcustomerpostcode'];
				$hashParameters['CONTACT_ADDR_CITY'] = $gSession['order']['billingcustomercity'];
				$hashParameters['CONTACT_ADDR_COUNTRY'] = $gSession['order']['billingcustomercountrycode'];
				$hashParameters['INCLUDE_VAT'] = 1;
				$hashParameters['ITEMS'] = 1;
				$hashParameters['ITEM_TITLE[0]'] = LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
				$hashParameters['ITEM_NO[0]'] = $gSession['items'][0]['itemprojectref'];
				$hashParameters['ITEM_AMOUNT[0]'] = 1;
				$hashParameters['ITEM_PRICE[0]'] = $amount;
				$hashParameters['ITEM_TAX[0]'] =  $gSession['items'][0]['itemtaxrate'];
				$hashParameters['ITEM_DISCOUNT[0]'] = 0;
				$hashParameters['ITEM_TYPE[0]'] = 1;
			}

            $md5Key = self::generateHash($hashParameters);

			// build parameters to send to paytrail
			$parameters = array(
				'MERCHANT_ID' => $merchant,
				'AUTHCODE' => $md5Key
			);

			$parameters = array_merge($hashParameters, $parameters);


			// define Smarty variables
			$smarty->assign('payment_url', $server);
            $smarty->assign('cancel_url', $cancelReturnPath);
            $smarty->assign('parameter', $parameters);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to paytrail
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
        $resultArray['ref'] = $gSession['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function confirm()
    {
    	// include the email creation module
    	require_once('../Utils/UtilsEmail.php');

        global $gSession;

        $resultArray = Array();
        $result = '';
        $authorised = false;
        $authorisedStatus = 0;
        $showError = false;
        $emailContent = '';
        $smarty = SmartyObj::newSmarty('Order', '', '');

        // initialise variables
        $orderID = UtilsObj::getGetParam('ORDER_NUMBER');
        $timeStamp = UtilsObj::getGetParam('TIMESTAMP');
        $paymentMethod = UtilsObj::getGetParam('METHOD');
        $transactionID = UtilsObj::getGetParam('PAID');
        $returnHash = UtilsObj::getGetParam('RETURN_AUTHCODE');

       	$payTrailConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayTrail.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
		$merchantAuthCode = $payTrailConfig['AUTHCODE'];

        $md5Check = self::generateHash(array($orderID, $timeStamp, $transactionID, $paymentMethod, $merchantAuthCode));

		$amount = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');
		$formatted_payment_date = DatabaseObj::getServerTime();

		// calculate md5key mac result
		// this has to be the same as $returnHash, otherwise there is something wrong
		if ($md5Check == $returnHash)
		{
			$authorised = true;
		}

		if ($transactionID != '')
		{
			if (! $authorised)
			{
				// md5 check failed
				$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': MD5KEY';
				$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': MD5 check failed';
				$resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $transactionID;
				$resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $orderID;
				$resultArray['errorform'] = 'error.tpl';
				$showError = true;
				$authorisedStatus = 0;
			}
			else
			{
				$showError = false;
				$authorisedStatus = 1;
			}
		}

        // write to log file.
		$serverTimestamp = DatabaseObj::getServerTime();
		PaymentIntegrationObj::logPaymentGatewayData($payTrailConfig, $serverTimestamp);

        // check to see if there was an error during order process
        // resulting in a automaticCallBack delay. Proceed to send email
        // to email address from config file.
        if ($gSession['ref'] == 0)
        {
        	$showError = true;
        	$transErrorName  = $payTrailConfig['TRANSERRORNAME'];
        	$transErrorEmail = $payTrailConfig['TRANSERROREMAIL'];

        	$emailContent = $smarty->get_config_vars('str_LabelOrderNumber') . ': ' . $orderID . "\n" . $smarty->get_config_vars('str_LabelTransactionID') .
											': ' . $transactionID . "\n" . $smarty->get_config_vars('str_LabelPaymentMethod') . ': ' . $paymentMethod ."\n\n";

        	$emailObj = new TaopixMailer();
        	$emailObj->sendTemplateEmail('admin_transactionerror', '', '', '', '',
					$transErrorName, $transErrorEmail, '', '',
					0,
					Array('data' => $emailContent));
        }

        $resultArray['result'] = $result;
        $resultArray['ref'] = $orderID;
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $transactionID;
        $resultArray['formattedtransactionid'] = $transactionID;
        $resultArray['responsecode'] = '';
        $resultArray['responsedescription'] = '';
        $resultArray['authorisationid'] = $orderID;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $orderID;
        $resultArray['bankresponsecode'] = '';
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $formatted_payment_date;
        $resultArray['paymentmeans'] = $paymentMethod;
        $resultArray['paymenttime'] = '';
		$resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $formatted_payment_date;
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
        $resultArray['threedsecurestatus'] = '';
        $resultArray['cavvresponsecode'] = '';
        $resultArray['update'] = false;
        $resultArray['orderid'] = 0;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = $showError;

        return $resultArray;
    }

    static function generateHash($pParamArray)
    {
    	$hashString = implode('|', $pParamArray);
    	$md5String = md5($hashString);

    	return strtoupper($md5String);
    }
}

?>