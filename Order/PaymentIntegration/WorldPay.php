<?php

class WorldPayObj
{
    static function configure()
    {
        global $gSession;

		$gateways 		= Array();
        $resultArray 	= Array();
		$currency 		= $gSession['order']['currencycode'];
		$active 		= true;

		$smarty = SmartyObj::newSmarty('CreditCardPayment');

        AuthenticateObj::clearSessionCCICookie();

		$WorldPayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/WorldPay.conf',$currency,$gSession['webbrandcode']);
		$currencyList = $WorldPayConfig['WPCURRENCIES'];

		// test for WorldPay supported currencies
        if (strpos($currencyList, $currency) === false)
        {
            $active = false;
        }

        $resultArray['active'] = $active;
        $resultArray['gateways'] = Array();
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

    	// First check if we have any ccidata. This is set when the call is made the first time.
        // If the data is set then the user must have hit the back button on their browser.
        if ($gSession['order']['ccidata'] == '')
        {
			$WorldPayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/WorldPay.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

			$server 			= $WorldPayConfig['WPSERVER'];
			$vendorName 		= $WorldPayConfig['WPVENDORNAME'];
			$instID 			= $WorldPayConfig['WPINSTID'];
			$md5Key 			= $WorldPayConfig['WPMD5KEY'];
			$accountID 			= $WorldPayConfig['WPMERCHANTCODE'];
			$hideContactDetails = $WorldPayConfig['WPHIDECONTACTDETAILS'];
			$noLanguageMenu 	= $WorldPayConfig['WPNOLANGUAGEMENU'];
			$languages 			= $WorldPayConfig['WPLANGUAGES'];
			$testMode 			= $WorldPayConfig['WPTESTMODE'];
			$testResult 		= $WorldPayConfig['WPTESTRESULT'];

			// build WorldPay data
			$orderID 		= $gSession['ref'] . time();
			$postCode 		= $gSession['order']['billingcustomerpostcode'];
			$countryCode 	= $gSession['order']['billingcustomercountrycode'];
			$telefon 		= $gSession['order']['billingcustomertelephonenumber'];
			$email 			= $gSession['order']['billingcustomeremailaddress'];
			$name 			= $gSession['order']['billingcontactfirstname'] . ' ' . $gSession['order']['billingcontactlastname'];

			// if testMode and testResult given, provide in the 'name' field
			if ($testMode == '1' && $testResult != '')
			{
				$name = $testResult;
			}

			// decimal separator must be a dot!
			$amount 			= number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', '');
			$currency 			= $gSession['order']['currencycode'];
			$defaultLanguage 	= $gConstants['defaultlanguagecode'];
			$lang 				= substr($gSession['browserlanguagecode'], 0, 2);
			$orderText 			= $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);

			$address = '';

			if ($gSession['order']['billingcustomeraddress1'] != '')
			{
				$address .= $gSession['order']['billingcustomeraddress1'];
			}
			if ($gSession['order']['billingcustomeraddress2'] != '')
			{
				if ($address != '')
				{
					$address .= '&#10;';
				}
				$address .= $gSession['order']['billingcustomeraddress2'];
			}
			if ($gSession['order']['billingcustomeraddress3'] != '')
			{
				if ($address != '')
				{
					$address .= '&#10;';
				}
				$address .= $gSession['order']['billingcustomeraddress3'];
			}
			if ($gSession['order']['billingcustomeraddress4'] != '')
			{
				if ($address != '')
				{
					$address .= '&#10;';
				}
				$address .= $gSession['order']['billingcustomeraddress4'];
			}

			// md5 signature
			$signatureFields = 'amount:currency:instId:cartId';
			$signature = '';

			if ($md5Key != '')
			{
				$signature = md5($md5Key . ':' . $amount . ':' . $currency . ':' . $instID . ':' . $orderID);
			}

			// Store parameters in an array
			$parameters = array(
				'instId' => $instID,
				'cartId' => $orderID,
				'amount' => $amount,
				'currency' => $currency,
				'desc' => $orderText,
				'address' => $address,
				'postcode' => $postCode,
				'country' => $countryCode,
				'tel' => $telefon,
				'email' => $email,
				'name' => $name,
				'authMode' => 'A',
				'fixContact' => '',
				'hideCurrency' => '',
				'ownsignature' => '', // this is not for WorldPay, but for us
				'MC_wb' => $gSession['webbrandcode'], // this is not for WorldPay, but for us
				'MC_ref' => $gSession['ref'] // this is not for WorldPay, but for us
			);

			// test for supported languages
			if (strpos($languages, $lang) === false)
			{
				$parameters['lang'] = $defaultLanguage;
			}
			else
			{
				$parameters['lang'] = $lang;
			}

			// Optional parameters
			if($testMode == 1) {
				$parameters['testMode'] = 100;
			}

			if($accountID != '') {
				$parameters['accId1'] = $accountID;
			}

			if($hideContactDetails == '1') {
				$parameters['hideContact'] = '';
			}

			if($noLanguageMenu == '1') {
				$parameters['noLanguageMenu'] = '';
			}

			if($signature != '') {
				$parameters['signatureFields'] = $signatureFields;
				$parameters['signature'] = $signature;
			}

			// define Smarty variables
			$smarty->assign('payment_url', $server);
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('parameter', $parameters);

			// test for supported languages
			if (strpos($languages, $lang) === false)
			{
				$smarty->assign('lang', $defaultLanguage);
			}
			else
			{
				$smarty->assign('lang', $lang);
			}

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to WorldPay
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

		// read config file, we need the logo
        $WorldPayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/WorldPay.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
		$WPlogo			= $WorldPayConfig['WPLOGO'];
		if ($WPlogo != '')
		{
			$WPlogo = '<img src="/i/<wpdisplay item=instId>/' . $WPlogo . '">';
		}

		// get html to return to WorldPay
		$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname'],$gSession['browserlanguagecode']);
		$smarty->assign('homeurl', $gSession['webbrandweburl']);

		$returnHTML = $smarty->fetchLocale('order/PaymentIntegration/resume.tpl', $gSession['browserlanguagecode']);

		// get rid of javascript and css, alter image tags
		$gSession['order']['confirmationhtml'] = UtilsObj::cleanHTML($returnHTML, false, true, '<img src="/i/<wpdisplay item=instId>/' . $WPlogo . '">');

        $resultArray = Array();
        $resultArray['result'] = '';
        $resultArray['ref'] = $_GET['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = true;

        return $resultArray;
    }

    static function confirm()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
        $error = '';

        // initialise variables
        $amount 		= UtilsObj::getPOSTParam('amount', '0.00');
        $authAmount 	= UtilsObj::getPOSTParam('authAmount', '0.00');
        $authMode 		= UtilsObj::getPOSTParam('authMode');
        $authentication = UtilsObj::getPOSTParam('authentication');
        $AVS 			= UtilsObj::getPOSTParam('AVS');
        $callbackPW 	= UtilsObj::getPOSTParam('callbackPW');
        $cardType 		= UtilsObj::getPOSTParam('cardType');
        $cartId 		= UtilsObj::getPOSTParam('cartId');
        $currency 		= UtilsObj::getPOSTParam('currency');
        $authCurrency 	= UtilsObj::getPOSTParam('authCurrency');
        $instId 		= UtilsObj::getPOSTParam('instId');
        $ipAddress 		= UtilsObj::getPOSTParam('ipAddress');
        $rawAuthMessage = UtilsObj::getPOSTParam('rawAuthMessage');
        $transId 		= UtilsObj::getPOSTParam('transId');
        $transStatus 	= UtilsObj::getPOSTParam('transStatus');
        $transTime 		= UtilsObj::getPOSTParam('transTime');
        $formattedTransTime = date ("Y-m-d H:i:s", $transTime/1000); // transTime in milli seconds
        $wafMerchMessage  = UtilsObj::getPOSTParam('wafMerchMessage ');

        // our custom field
        $webBrandCode 	= UtilsObj::getPOSTParam('MC_wb');

        $authorised = true;
        $authorisedStatus = 1;

        $userID = $gSession['userid'];
        $currencyCode = $gSession['order']['currencycode'];

        $WorldPayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/WorldPay.conf',$currencyCode,$gSession['webbrandcode']);
		$WPinstID 			= $WorldPayConfig['WPINSTID'];
		$WPvendorName 		= $WorldPayConfig['WPVENDORNAME'];
		$WPcallbackPW 		= $WorldPayConfig['WPCALLBACKPW'];
		$WPmd5Key 			= $WorldPayConfig['WPMD5KEY'];
		$WPtestMode			= $WorldPayConfig['WPTESTMODE'];
		$WPlogo				= $WorldPayConfig['WPLOGO'];
        $WPlogFilePath 		= $WorldPayConfig['LOGFILEPATH'];
        $WPlogOutput 		= $WorldPayConfig['LOGOUTPUT'];

        if ($WPlogo != '')
        {
        	$WPlogo = '<img src="/i/<wpdisplay item=instId>/' . $WPlogo . '">';
        }

		// Do a couple of checks to validate response.
		if ($WPcallbackPW != $callbackPW)
		{
			$authorised = false;
			$error = 'Incorrect Callback Password.';
		}

		// Compare installation IDs
		if ($instId != $WPinstID)
		{
			$authorised = false;
			$error = 'Incorrect Installation ID.';
		}

		// Get html to return to WorldPay
		$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname'],$gSession['browserlanguagecode']);

		if ($authorised)
		{
			SmartyObj::replaceParams($smarty, 'str_MessageOrderConfirmation1', '<span class="confirmationNumber"><order_number/></span>');
			$smarty->assign('str_MessageOrderConfirmation1', $smarty->get_template_vars('str_MessageOrderConfirmation1'));
            $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('order/sidebarleft_default.tpl', ''));
            $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('order/sidebarleft_confirmation.tpl', ''));
			$smarty->assign('mainwebsiteurl', $gSession['webbrandwebroot']);

			if ($gSession['ismobile'] == true)
            {
                $smarty->assign('isajaxcall', false);
                $returnHTML = $smarty->fetchLocale('order/externalorderconfirmation_small.tpl', $gSession['browserlanguagecode']);
				$cssFile = $smarty->getLocaleCss('csscustomerexternal_small');
            }
            else
            {
                $returnHTML = $smarty->fetchLocale('order/externalorderconfirmation_large.tpl', $gSession['browserlanguagecode']);
				$cssFile = $smarty->getLocaleCss('csscustomerexternal_large');
            }

			$showError = false;

			// deal with the case that payment has not been captured but only pre-authorised,
			// i.e. credit card authorisation was successful, but payment hasn't been captured yet
			$authorisedStatus = ($authMode == 'E') ? 2 : 1;
		}
		else
		{
			$smarty->assign('data1', SmartyObj::getParamValueLocale('Order', 'str_LabelErrorCode', $gSession['browserlanguagecode']) . ': ERROR');
			$smarty->assign('data2', SmartyObj::getParamValueLocale('Order', 'str_LabelErrorMessage', $gSession['browserlanguagecode']) . ': ' . $error);
			$smarty->assign('data3', SmartyObj::getParamValueLocale('Order', 'str_LabelTransactionID', $gSession['browserlanguagecode']) . ': ' . $transId);
			$smarty->assign('data4', SmartyObj::getParamValueLocale('Order', 'str_LabelOrderNumber', $gSession['browserlanguagecode']) . ': ' . $cartId);
			$smarty->assign('homeurl', $gSession['webbrandweburl']);

			if ($gSession['ismobile'] == true)
			{
				$returnHTML = $smarty->fetchLocale('order/PaymentIntegration/externalerror_small.tpl', $gSession['browserlanguagecode']);
				$cssFile = $smarty->getLocaleCss('csscustomerexternal_small');
			}
			else
			{
				$returnHTML = $smarty->fetchLocale('order/PaymentIntegration/externalerror_large.tpl', $gSession['browserlanguagecode']);
				$cssFile = $smarty->getLocaleCss('csscustomerexternal_large');
			}
            
    		$showError = true;
    		$authorisedStatus = 0;
		}

		// Add inline styles and replace relative paths (images, css, etc) with absolute paths
		$formattedReturnHTML = str_replace("../", UtilsObj::correctPath($gSession['webbrandweburl']), self::formatReturnHTML($returnHTML, $cssFile));

		// Get rid of image tags and convert to unicode code points where necessary
		$tempHTML = self::restoreAboutSectionImage(UtilsObj::cleanHTML($formattedReturnHTML, false, true, $WPlogo));

		// Restore the <order_number/> tag, as the previous formatting changes it to have start and end tags
		$gSession['order']['confirmationhtml'] = self::restoreOrderNumberTag($tempHTML);

		// write to log file.
		$serverTime = DatabaseObj::getServerTime();
		PaymentIntegrationObj::logPaymentGatewayData($WorldPayConfig, $serverTime, $error);

        $resultArray['result'] = $result;
        $resultArray['ref'] = $gSession['ref'];
        $resultArray['amount'] = $amount;
        $resultArray['formattedamount'] = $amount;
        $resultArray['charges'] = '0.00';
        $resultArray['formattedcharges'] = '0.00';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $transId;
        $resultArray['formattedtransactionid'] = $transId;
        $resultArray['responsecode'] = $transStatus;
        $resultArray['responsedescription'] = $rawAuthMessage;
        $resultArray['authorisationid'] = $cartId;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $cartId;
        $resultArray['bankresponsecode'] = '';
        $resultArray['cardnumber'] = $authAmount;
        $resultArray['formattedcardnumber'] = $authCurrency;
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = $instId;
        $resultArray['paymentdate'] = $transTime;
        $resultArray['paymentmeans'] = $cardType;
        $resultArray['paymenttime'] = '';
		$resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $formattedTransTime;
        $resultArray['addressstatus'] = $AVS;
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = $ipAddress;
        $resultArray['payerstatus'] = $WPtestMode;
        $resultArray['payeremail'] = '';
        $resultArray['business'] = $WPvendorName;
        $resultArray['receiveremail'] = '';
        $resultArray['receiverid'] = '';
        $resultArray['pendingreason'] = '';
        $resultArray['transactiontype'] = $authMode;
        $resultArray['settleamount'] = '';
        $resultArray['currencycode'] = $currencyCode;
        $resultArray['webbrandcode'] = $gSession['webbrandcode'];

        $resultArray['charityflag'] = '';
        $resultArray['threedsecurestatus'] = $wafMerchMessage;
        $resultArray['cavvresponsecode'] = $authentication;
        $resultArray['update'] = false;
        $resultArray['orderid'] = 0;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = $showError;

        return $resultArray;
    }

	static function formatReturnHTML($html, $cssFile)
	{
		require_once('../libs/external/EmailContent/css_to_inline_styles.php');

		// Load the css file
		$cssContent = UtilsObj::readTextFile($cssFile);
		$cssContent = str_replace("\r\n", "\n", $cssContent);
		$cssContent = str_replace("\r", "\n", $cssContent);

		// Convert css to inline styles
		$cssToInlineStyles = new CSSToInlineStyles($html, $cssContent);
		$cssToInlineStyles->setUseInlineStylesBlock(false);
		$cssToInlineStyles->setCleanup(false);
		$htmlWithInlineStyles = $cssToInlineStyles->convert(false, false);

		return $htmlWithInlineStyles;
	}

	static function restoreAboutSectionImage($confirmationHTML)
	{
		global $gSession;

		$dom = new DOMDocument;
		$dom->loadHTML($confirmationHTML);
		$xpath = new DOMXPath($dom);
		$aboutSection = $xpath->query('//div[@class="aboutSection"]');

		foreach ($aboutSection as $container)
		{
			$images = $container->getElementsByTagName('img');

			foreach ($images as $image)
			{
				$image->setAttribute('src', UtilsObj::correctPath($gSession['webbrandweburl']) . 'images/icons/support-icon_v2.png');

				// DOM save tends to convert the spaces in the <wpdisplay item=instId> to %20, which we don't want 
				$confirmationHTML = str_replace('%20', ' ', $dom->saveHTML());
			}
		}

		return $confirmationHTML;
	}

	static function restoreOrderNumberTag($confirmationHTML)
	{
		return str_replace("<order_number></order_number>", '<order_number/>', $confirmationHTML);
	}
}

?>