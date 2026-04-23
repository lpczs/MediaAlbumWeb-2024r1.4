<?php

class CMCICObj
{
    static function configure()
    {
        global $gSession;

        $resultArray = Array();
        $CMCICConfig = PaymentIntegrationObj::readCCIConfigFile('../config/CMCIC.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
        $currencyList = $CMCICConfig['CMCICCURRENCIES'];
        $currency = $gSession['order']['currencycode'];
        $active = true;

        // test for CMCIC supported currencies
        if (strpos($currencyList, $currency) === false)
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
			$CMCICConfig = PaymentIntegrationObj::readCCIConfigFile('../config/CMCIC.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);

			$successReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccManualCallback&ref=' . $gSession['ref'] . '&success=1';
			$errorReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
			$cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];

			$server = $CMCICConfig['CMCICSERVER'];
			$EPTNumber = $CMCICConfig['EPTNUMBER'];
			$CMCIC_CLE = $CMCICConfig['CMCIC_CLE'];
			$siteCode = $CMCICConfig['SITECODE'];
            $sVersion = $CMCICConfig['VERSION'];
            $challenge = $CMCICConfig['CHALLENGE'];
            $defaultLanguage = strtoupper($gConstants['defaultlanguagecode']);

            $contexteCommandeArray = [
                'billing' => [
                    'firstName' => $gSession['order']['billingcontactfirstname'],
                    'lastName' => $gSession['order']['billingcontactlastname'],
                    'addressLine1' => $gSession['order']['billingcustomeraddress1'],
                    'city' => $gSession['order']['billingcustomercity'],
                    'postalCode' => $gSession['order']['billingcustomerpostcode'],
                    'country' => $gSession['order']['billingcustomercountrycode'],
                    'email' => $gSession['order']['billingcustomeremailaddress']
                ]
            ];

            $sContexteCommande = base64_encode(json_encode($contexteCommandeArray));

			// build CMCIC data
			$orderID = $gSession['ref'];
			$sDate = date('d/m/Y:H:i:s');
			// amount has the currency code appended to the amount
			$amount = $gSession['order']['ordertotaltopay'] . $gSession['order']['currencycode'];
			$sLang = strtoupper(substr($gSession['browserlanguagecode'], 0, 2));
			$orderText = $gSession['items'][0]['itemqty'] . ' x ' . LocalizationObj::getLocaleString($gSession['items'][0]['itemproductname'], $gSession['browserlanguagecode'], true);
			$custEmail = $gSession['order']['billingcustomeremailaddress'];

			// test for CMCIC supported languages
			$supportedLanguages = array('FR', 'EN', 'DE', 'ES', 'NL', 'IT');
			if (! in_array($sLang, $supportedLanguages))
			{
				$sLang = (in_array($defaultLanguage, $supportedLanguages)) ? $defaultLanguage : 'FR';
			}

            // Check for supported 3D secure challenge option.
            $supportedChallenges = [
                'challenge_preferred', 
                'no_preference', 
                'challenge_mandated', 
                'no_challenge_requested', 
                'no_challenge_requested_strong_authentication',
                'no_challenge_requested_trusted_third_party',
                'no_challenge_requested_risk_analysis',
            ];
			if (! in_array($challenge, $supportedChallenges))
			{
				$sLang = 'challenge_preferred';
            }
            
        
            // Store the data in an array. used to create the form to submit and the hmac.
            $parameters = [
                'TPE' => $EPTNumber,
                'ThreeDSecureChallenge' => $challenge,
                'contexte_commande' => $sContexteCommande,
                'date' => $sDate,
                'lgue' => $sLang,
                'mail' => $custEmail,
                'montant' => $amount,
                'reference' => $orderID,
                'societe' => $siteCode,
                'texte-libre' => $orderText,
                'url_retour_err' => $errorReturnPath,
                'url_retour_ok' => $successReturnPath,
                'version' => $sVersion,
            ];

            // Make sure the parameters are ordered alphabetically for the mac calculation.
            ksort($parameters);

            // Create elements of the hmac string, leaving the original parameters array intact.
            $macElements = array_map(function ($pKey) use ($parameters)
            {
                return $pKey . '=' . $parameters[$pKey];
            }, array_keys($parameters));

            // Generate the string for the hmac.
            $mac = implode('*', $macElements);

            // Generate the hmac.
            $parameters['MAC'] = self::computeHmac($mac, $CMCIC_CLE);

			$smarty->assign('payment_url', $server);
			$smarty->assign('cancel_url', $cancelReturnPath);
			$smarty->assign('method', 'POST');
			$smarty->assign('parameter', $parameters);

			AuthenticateObj::defineSessionCCICookie();
			$smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
			$smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

			// set the ccidata to remember we have jumped to CMCIC
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
        $resultArray['result'] = '';
        $resultArray['ref'] = $gSession['ref'];
        $resultArray['transactionid'] = '';
        $resultArray['authorised'] = false;
        $resultArray['showerror'] = false;

        return $resultArray;
    }

    static function confirm($pCallBack)
    {
        global $gSession;

        $resultArray = Array();
        $authorisedStatus = 0;
        $update = false;
		$showError = false;

        $formattedAmount = $gSession['order']['ordertotaltopay'];
        // initialise variables
		$TPE = '';
		$reference = '';
		$orderText = '';
		$paymentResult = '';
		$cv2Entered = '';
		$cardType = '';
		$statusof3dSecure = '';
		$bankAuthNumber = '';
		$customersIp = '';
		$hashedCreditCardNumber = '';
		$response = '';

        $serverTimestamp = DatabaseObj::getServerTime();

		$CMCICConfig = PaymentIntegrationObj::readCCIConfigFile('../config/CMCIC.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
		$sVersion = $CMCICConfig['VERSION'];
		$CMCIC_CLE = $CMCICConfig['CMCIC_CLE'];

		// write to a log file.
		PaymentIntegrationObj::logPaymentGatewayData($CMCICConfig, $serverTimestamp);

        if ($pCallBack == 'manual')
        {
        	$authorised = true;
        	$reference = $gSession['ref'];
        }
        else
        {
			// define constants
			define("CMCIC_RECEIPT","version=2\ncdr=%s");
			define("CMCIC_MAC_OK","0");
			define("CMCIC_MAC_NOTOK","1\n");

			// initialise variables
			$returnMac = UtilsObj::getPOSTParam('MAC');
			$TPE = UtilsObj::getPOSTParam('TPE');
			$sDate = UtilsObj::getPOSTParam('date');
			$amount = UtilsObj::getPOSTParam('montant');
			$reference = UtilsObj::getPOSTParam('reference');
			$orderText = UtilsObj::getPOSTParam('texte-libre');
			$paymentResult = UtilsObj::getPOSTParam('code-retour');
			$cv2Entered = UtilsObj::getPOSTParam('cvx');
			$cardDateOfValidity = UtilsObj::getPOSTParam('vld');
			$cardType = UtilsObj::getPOSTParam('brand');
			$statusof3dSecure = UtilsObj::getPOSTParam('status3ds');
			$bankAuthNumber = UtilsObj::getPOSTParam('numauto');
			$reasonForRefusal = UtilsObj::getPOSTParam('motifrefus');

			$issuingBankCountry = UtilsObj::getPOSTParam('originecb');
			$binCodeOfBank = UtilsObj::getPOSTParam('bincb');
			$hashedCreditCardNumber = UtilsObj::getPOSTParam('hpancb');
			$customersIp = UtilsObj::getPOSTParam('ipclient');
			$transactionCountryCode = UtilsObj::getPOSTParam('originetr');
			$Veres3dSecure = UtilsObj::getPOSTParam('veres');
			$Pares3dSecure = UtilsObj::getPOSTParam('pares');

            // Prepare the array of parameters to calculate the mac.
            $macElementsArray = $_POST;

            // Remove the MAC as this is what is being calculated, already stored in $returnMac.
            unset($macElementsArray['MAC']);

            // Order the reurn data alphabetically by key.
            ksort($macElementsArray);

            // Restructure entries to "key=value".
            array_walk($macElementsArray, function(&$a, $b)
            {
                $a = "$b=$a"; 
            });

            // Join all entries using asterisk as separator to create the MAC string.
            $macString = implode('*', $macElementsArray);

            // Generate the MAC.
            $mac = self::computeHmac($macString, $CMCIC_CLE);

			// check to see if the return mac matches against the mac calculated using the return fields
			if ($mac == strtolower($returnMac))
			{
				switch ($paymentResult)
				{
					case "Annulation":
						exit(sprintf(CMCIC_RECEIPT, CMCIC_MAC_OK));
					break;
					case "payetest":
						// Payment has been accepeted on the test server
						$authorisedStatus = 1;
						$authorised = true;
						break;

					case "paiement":
						// Payment has been accepted on the productive server
						$authorisedStatus = 1;
						$authorised = true;
						break;
				}

				// seal is valid
				$receipt = CMCIC_MAC_OK;
			}
			else
			{
				// seal is not valid
				$receipt = CMCIC_MAC_NOTOK . $macString;
			}

			// response to server to say we have a valid seal or not
			$response = printf(CMCIC_RECEIPT, $receipt);
		}

        $resultArray['result'] = $paymentResult;
        $resultArray['ref'] = $reference;
        $resultArray['amount'] = $formattedAmount;
        $resultArray['formattedamount'] = $formattedAmount;
        $resultArray['charges'] = '';
        $resultArray['formattedcharges'] = '';
    	$resultArray['authorised'] = $authorised;
    	$resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $reference;
        $resultArray['formattedtransactionid'] = $reference;
        $resultArray['responsecode'] = $paymentResult;
        $resultArray['responsedescription'] = $orderText;
        $resultArray['authorisationid'] = $reference;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $reference;
        $resultArray['bankresponsecode'] = $bankAuthNumber;
        $resultArray['cardnumber'] = $hashedCreditCardNumber;
        $resultArray['formattedcardnumber'] = $hashedCreditCardNumber;
        $resultArray['cvvflag'] = $cv2Entered;
        $resultArray['cvvresponsecode'] = $statusof3dSecure;
        $resultArray['paymentcertificate'] = '';
        $resultArray['paymentdate'] = $serverTimestamp;
        $resultArray['paymentmeans'] = $cardType;
        $resultArray['paymenttime'] = '';
		$resultArray['paymentreceived'] = ($authorisedStatus == 1) ? 1 : 0;
        $resultArray['formattedpaymentdate'] = $serverTimestamp;
        $resultArray['addressstatus'] = '';
        $resultArray['postcodestatus'] = '';
        $resultArray['payerid'] = $customersIp;
        $resultArray['payerstatus'] = '';
        $resultArray['payeremail'] = '';
        $resultArray['business'] = $TPE;
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
        $resultArray['update'] = $update;
        $resultArray['orderid'] = 0;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
    	$resultArray['showerror'] = $showError;

        return $resultArray;
    }

    static function computeHmac($pData, $pCMCIC_CLE)
    {
		$hexStrKey  = substr($pCMCIC_CLE, 0, 38);
		$hexFinal   = "" . substr($pCMCIC_CLE, 38, 2) . "00";

		$cca0 = ord($hexFinal);

		if ($cca0 > 70 && $cca0 < 97)
		{
			$hexStrKey .= chr($cca0-23) . substr($hexFinal, 1, 1);
		}
		else
		{
			if (substr($hexFinal, 1, 1) == "M")
			{
				$hexStrKey .= substr($hexFinal, 0, 1) . "0";
			}
			else
			{
				$hexStrKey .= substr($hexFinal, 0, 2);
			}
		}

		$final = pack("H*", $hexStrKey);

		return strtolower(hash_hmac("sha1", $pData, $final));
	}
}
?>