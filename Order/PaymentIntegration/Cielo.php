<?php
use Security\ControlCentreCSP;

class CieloObj
{
    static function configure()
    {
        global $gSession;
        $resultArray = Array();

        AuthenticateObj::clearSessionCCICookie();

        $CieloConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Cielo.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);
        $currencyList = $CieloConfig['CURRENCIES'];
        $currency = $gSession['order']['currencyisonumber'];
        $active = true;

        // test for supported currencies
        if (strpos($currencyList, $currency) === false)
        {
            $active = false;
        }

        $form = '';
        $script = '';
        $action = '';

        // If active create the script for the credit card drop down.
        if($active)
        {
			$cspActive = true;
			$nonceValue = '[nonce]';
			$ac_config = UtilsObj::getGlobalValue('ac_config', []);

			if ((array_key_exists('CONTENTSECURITYPOLICY', $ac_config)) && ($ac_config['CONTENTSECURITYPOLICY'] === 'DISABLED'))
			{
				$cspActive = false;
			}

			if (($cspActive) && ($gSession['ismobile'] != true))
			{
				$cspBuilder = ControlCentreCSP::getInstance(UtilsObj::getGlobalValue('ac_config'));
				$nonceValue = $cspBuilder->nonce();
			}

            $form = "
                var paymenthod = document.getElementsByName('paymentmethods');

                for(var i = 0; i < paymenthod.length; i++)
                {
                    if(paymenthod[i].value == 'CARD')
                    {
                        creditCardContainer = paymenthod[i].parentNode;
                        creditCardContainer.appendChild(document.createTextNode('\u00A0\u00A0\u00A0'));
                        newscript = document.createElement('script');
                        newscript.type = 'text/javascript';
						" . ($cspActive ? "newscript.setAttribute('nonce', '" . $nonceValue . "');" : "") ."
                        newscript.text = 'CIELODropdown();';
                        creditCardContainer.appendChild(newscript);
                    }
                }";

            $script = "
            function CIELODropdown()
            {
                var selectorOuterDiv = document.createElement('div');
                selectorOuterDiv.setAttribute('class', 'wizard-dropdown');

                var selector = document.createElement('select');
                selector.id = 'paymentgatewaycode';
                selector.name = 'paymentgatewaycode';
                selector.setAttribute('class', 'wizard-dropdown');
                selector.setAttribute('data-decorator', 'forceSelectCard');
				selector.addEventListener('change', function(event) {
					forceSelectCard();
				});

                selectorOuterDiv.appendChild(selector);
                creditCardContainer.appendChild(selectorOuterDiv);

                var option = document.createElement('option');
                option.value = '';
                option.appendChild(document.createTextNode('-- ". SmartyObj::getParamValue('CreditCardPayment', 'str_DropDownPleaseSelectAPaymentType') ." --'));
                selector.appendChild(option);

                // Assign the array of PaymentMethodList from the config file. Cards are included based upon config file.
                CIELOPayTypeArray = new Array();";
                if((string) $CieloConfig['VISA_DEBIT_ON'] == "true")
				{
					$script .=" CIELOPayTypeArray[CIELOPayTypeArray.length]  = new payType('Visa  -- ". SmartyObj::getParamValue('CreditCardPayment', 'str_CIELODebitCard') ."', 'id0');";
				}

                if((string) $CieloConfig['VISA_CREDIT_ON'] == "true")
				{
					$script .=" CIELOPayTypeArray[CIELOPayTypeArray.length]  = new payType('Visa  -- ". SmartyObj::getParamValue('CreditCardPayment', 'str_CIELOCreditCard') ."', 'id1');";
				}

				if((string) $CieloConfig['DINERS_ON'] == "true")
				{
					$script .=" CIELOPayTypeArray[CIELOPayTypeArray.length]  = new payType('Diners Club', 'id2');";
				}

                if((string) $CieloConfig['DISCO_ON'] == "true")
				{
					$script .=" CIELOPayTypeArray[CIELOPayTypeArray.length]  = new payType('Discover', 'id3');";
				}
                if((string) $CieloConfig['ELO_ON'] == "true")
				{
					$script .=" CIELOPayTypeArray[CIELOPayTypeArray.length]  = new payType('elo', 'id4');";
				}
                if((string) $CieloConfig['AMEX_ON'] == "true")
				{
					$script .=" CIELOPayTypeArray[CIELOPayTypeArray.length]  = new payType('American Express', 'id5');";
				}
                if((string) $CieloConfig['JCB_ON'] == "true")
				{
					$script .=" CIELOPayTypeArray[CIELOPayTypeArray.length]  = new payType('JCB', 'id6');";
				}
                if((string) $CieloConfig['AURA_ON'] == "true")
				{
					$script .=" CIELOPayTypeArray[CIELOPayTypeArray.length]  = new payType('Aura', 'id7');";
				}
                if((string) $CieloConfig['MAST_DEBIT_ON'] == "true")
				{
					$script .=" CIELOPayTypeArray[CIELOPayTypeArray.length]  = new payType('Mastercard  -- ". SmartyObj::getParamValue('CreditCardPayment', 'str_CIELODebitCard') ."', 'id8');";
				}
                if((string) $CieloConfig['MAST_CREDIT_ON'] == "true")
				{
					$script .=" CIELOPayTypeArray[CIELOPayTypeArray.length]  = new payType('Mastercard  -- ". SmartyObj::getParamValue('CreditCardPayment', 'str_CIELOCreditCard') ."', 'id9');";
				}

                $script .="

                if (CIELOPayTypeArray)
                {
                    for (var i = 0; i < CIELOPayTypeArray.length; i++)
                    {
                        var option = document.createElement('option');
                        option.value = CIELOPayTypeArray[i].id;

                        if (option.value == '" . $gSession['order']['paymentgatewaycode'] . "')
                        {
                            option.selected = 'selected';
                        }

                        option.appendChild(document.createTextNode(CIELOPayTypeArray[i].name));
                        selector.appendChild(option);

                    }
                }
            }";

            $action = "validatePayType('Please select a payment method')";
        }
        else
        {
            $active = false;
        }

        $resultArray['active'] = $active;
        $resultArray['form'] = $form;
        $resultArray['scripturl'] = '';
        $resultArray['script'] = $script;
        $resultArray['action'] = $action;

        return $resultArray;
    }


    static function initialize()
    {
        global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        // first check if we have any ccidata. this is set when the call is made the first time.
        // if the data is set then the user must have hit the back button on their browser
        if ($gSession['order']['ccidata'] == '')
        {
            $CieloConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Cielo.conf',$gSession['order']['currencycode'],$gSession['webbrandcode']);
            $cancelReturnPath = UtilsObj::correctPath($gSession['webbrandweburl']) . '?fsaction=Order.ccCancelCallback&ref=' . $gSession['ref'];
            $returnPath = UtilsObj::correctPath($gSession['webbrandwebroot']) . 'PaymentIntegration/Cielo/CieloCallback.php?ref=' . $gSession['ref'];

            //Read settings from the config file.
            $server = $CieloConfig['CIELOSERVER'];
            $ecNumero = $CieloConfig['ECNUMERO'];
            $chave = $CieloConfig['CHAVE'];

            $authorizeVD = $CieloConfig['AUTORIZAR_VISA_DEBIT'];
            $authorizeVD = intval($authorizeVD);
            $authorizeVC = $CieloConfig['AUTORIZAR_VISA_CREDIT'];
            $authorizeVC = intval($authorizeVC);
            $authorizeMD = $CieloConfig['AUTORIZAR_MASTER_DEBIT'];
            $authorizeMD = intval($authorizeMD);
            $authorizeMC = $CieloConfig['AUTORIZAR_MASTER_CREDIT'];
            $authorizeMC = intval($authorizeMC);
            $authorizeDD = $CieloConfig['AUTORIZAR_DDEA'];
            $authorizeDD = intval($authorizeDD);
            $authorizeJCB = $CieloConfig['AUTORIZAR_JCB'];
            $authorizeJCB = intval($authorizeJCB);
            $authorizeAU = $CieloConfig['AUTORIZAR_AURA'];
            $authorizeAU = intval($authorizeAU);

            $totalToPay = number_format($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '', '');

            $cardPayments = 1; // Number of payments !!!!! Do not change
            $myTime = time();
            $orderIDShort = $gSession['ref'] . $myTime; // A unique id from our end
            $dateTime = date("Y-m-d") . "T" . date("H:i:s");

            $cieloCardType = $_POST['paymentgatewaycode'];
            $cardFlag = ""; //Card flag visa, mastercard, amex, elo, diners, discover, jcb, aura

            // Authorization status i.e. 1=authorize only if authenticated, 2=authorize not authenticated. This is set in the config file
            switch($cieloCardType)
            {
                case "id0":
				{
					$cardFlag = "visa";
					$cardType = "A";
					$authorize = $authorizeVD;
					break;
				}
                case "id1":
				{
					$cardFlag = "visa";
					$cardType = 1;
					$authorize = $authorizeVC;
					break;
				}
                case "id2":
				{
					$cardFlag = "diners"; $cardType = 1; $authorize = $authorizeDD;
					break;
				}
                case "id3":
				{
					$cardFlag = "discover";
					$cardType = 1;
					$authorize = $authorizeDD;
					break;
				}
                case "id4":
				{
					$cardFlag = "elo";
					$cardType = 1;
					$authorize = $authorizeDD;
					break;
				}
                case "id5":
				{
					$cardFlag = "amex";
					$cardType = 1;
					$authorize = $authorizeDD;
					break;
				}
                case "id6":
				{
					$cardFlag = "jcb";
					$cardType = 1;
					$authorize = $authorizeJCB;
					break;
				}
                case "id7":
				{
					$cardFlag = "aura";
					$cardType = 1;
					$authorize = $authorizeAU;
					break;
				}
                case "id8":
				{
					$cardFlag = "master";
					$cardType = "A";
					$authorize = $authorizeMD;
					break;
				}
                case "id9":
				{
					$cardFlag = "master";
					$cardType = 1;
					$authorize = $authorizeMC;
					break;
				}
                default: $authorize = "n/a";
					break;
            }

            //Outgoing xml request 1
            $msgXML= '<?xml version="1.0" encoding="ISO-8859-1" ?>'. "\n   ".
                        '<requisicao-transacao id="' . md5(date("YmdHisu")) . '" versao="1.1.0">' . "\n   ".
                            '<dados-ec>' . "\n      " .
                                '<numero>'
                                    . $ecNumero .
                                '</numero>' . "\n      " .
                                '<chave>'
                                    . $chave .
                                '</chave>' . "\n   " .
                            '</dados-ec>'. "\n   " .
                            '<dados-pedido>' . "\n      " .
                                '<numero>'
                                    . $orderIDShort .
                                '</numero>' . "\n      " .
                                '<valor>'
                                    . $totalToPay .
                                '</valor>' . "\n      " .
                                '<moeda>986</moeda>' . "\n      " .
                                '<data-hora>'
                                    . $dateTime  .
                                '</data-hora>' . "\n      ".
                                '<idioma>PT</idioma>' . "\n   " .
                            '</dados-pedido>'. "\n   " .
                            '<forma-pagamento>' . "\n      " .
                                '<bandeira>'
                                    . $cardFlag .
                                '</bandeira>' . "\n      " .
                                '<produto>'
                                    . $cardType .
                                '</produto>' . "\n      " .
                                '<parcelas>'
                                    . $cardPayments .
                                '</parcelas>' . "\n   " .
                            '</forma-pagamento>'. "\n   ".
                            '<url-retorno>'
                                . $returnPath  .  "\n   ".
                            '</url-retorno>' . "\n   " .
                            '<autorizar>'
                            . $authorize . "\n   " .
                            '</autorizar>'."\n   " .
                            '<capturar>false</capturar>' . "\n   " .
                        '</requisicao-transacao>';

            // http request for the transaction
            $xmlReply = self::httprequest($server, "mensagem=" . $msgXML);

            //Error check the response
            self::errorCheck($xmlReply);

            //Get the transaction ID
            $xml = simplexml_load_string($xmlReply);
            $tempTID = (string) $xml->tid;

            //Variables to be passed back into the session
            $gSession['cieloServer'] = $server;
            $gSession['cieloECNumber'] = $ecNumero;
            $gSession['cieloChave'] = $chave;
            $gSession['cieloTID'] = $tempTID;

            //Need to go to Cielo authentication site i.e. follow the authentication url
            $urlAuthTemp = "url-autenticacao";
            $authURL = $xml->$urlAuthTemp;

            $params = array();
            // define Smarty variables
            $smarty->assign('payment_url', $authURL);
            $smarty->assign('method', "POST");
            $smarty->assign('cancel_url', $cancelReturnPath);
            $smarty->assign('parameter', $params);

            AuthenticateObj::defineSessionCCICookie();
            $smarty->assign('ccicookiename', 'mawebcci' . $gSession['ref']);
            $smarty->assign('ccicookievalue', $gSession['order']['ccicookie']);

            // set the ccidata to remember we have jumped to DIBS
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


    static function confirm()
    {
        global $gSession;

        $resultArray = Array();
        $authorised = false;
        $authorisedStatus = 0;
        $showError = false;

        $CieloConfig = PaymentIntegrationObj::readCCIConfigFile('../config/Cielo.conf', $gSession['order']['currencycode'], $gSession['webbrandcode']);

        //Get session variables
        $server = $CieloConfig['CIELOSERVER'];
        $ecNumero = $CieloConfig['ECNUMERO'];
        $chave = $CieloConfig['CHAVE'];

        $tempTID = $gSession['cieloTID'];

        $myIDout = md5(date("YmdHisu"));
        //The second xml request
        $msgXML2= '<?xml version="1.0" encoding="ISO-8859-1" ?>'. "\n   ".
                    '<requisicao-consulta id="' . $myIDout . '" versao="1.1.0">' . "\n   ".
                        '<tid>' . $tempTID . '</tid>' . "\n   ".
                        '<dados-ec>' . "\n      " .
                            '<numero>'
                                . $ecNumero .
                            '</numero>' . "\n      " .
                            '<chave>'
                                . $chave .
                            '</chave>' . "\n   " .
                        '</dados-ec>'. "\n   " .
                    '</requisicao-consulta>';


        // http request for the transaction
        $xmlReply2 = self::httprequest($server, "mensagem=" . $msgXML2);
        $xml2 = simplexml_load_string($xmlReply2);

        //Error check the second xml response.
        $errCheckerArray = self::errorCheck($xmlReply2);
        $errChecker_msg = $errCheckerArray[1];
        $errChecker = $errCheckerArray[0];

        $returnTID = (string) $xml2->tid;

        //Cielo transcation response
        $OKorNot = (string) $xml2->autorizacao->codigo;
        $OKorNot_message = (string) $xml2->autorizacao->mensagem;

        //Check the status of the transaction and the communication with the cielo server.
        if ($errChecker == 0)
        {
            if ($tempTID != $returnTID)
            {
                $resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': TID erro'; // TID error
                $resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . " : " . SmartyObj::getParamValue('CreditCardPayment', 'str_CIELOTransactionIDError'); // O ID da transação de não corresponderem
                $resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $returnTID . ' != '. $tempTID;
                $resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $tempTID;
                $resultArray['errorform'] = 'error.tpl';
                $showError = true;
                $authorised = false;
                $authorisedStatus = 0;
            }

            if ($OKorNot != "4")
            {
                $resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': Autorização de erro'; // Authorisation Error
                $resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') ." : " . SmartyObj::getParamValue('CreditCardPayment', 'str_CIELOTransactionAuthError'); // A transação não foi autorizada.";
                $resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $tempTID;
                $resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $tempTID;
                $resultArray['errorform'] = 'error.tpl';
                $showError = true;
                $authorised = false;
                $authorisedStatus = 0;
            }
        }
		else if ($errChecker == 1)
		{

            //INVALID CERTIFICATE - A certificate of the transaction was not approved
            $resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': CERTIFICADO INVÁLIDO';
            $resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . " : " . SmartyObj::getParamValue('CreditCardPayment', 'str_CIELOCertificateNotApproved');
            $resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $tempTID;
            $resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $tempTID;
            $resultArray['errorform'] = 'error.tpl';
            $showError = true;
            $authorised = false;
            $authorisedStatus = 0;

        }
		else if ($errChecker == 2)
		{

            //HTTP READ TIMEOUT - o Limite de Tempo da transação foi estourado
            $resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': HTTP READ TIMEOUT';
            $resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . " : " . SmartyObj::getParamValue('CreditCardPayment', 'str_CIELOTimeout');
            $resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $tempTID;
            $resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $tempTID;
            $resultArray['errorform'] = 'error.tpl';
            $showError = true;
            $authorised = false;
            $authorisedStatus = 0;

        }
		else if ($errChecker == 3)
		{

            //HTTP READ TIMEOUT - o Limite de Tempo da transação foi estourado
            $resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': Ocorreu um erro em sua transação!';
            $resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . " : " . $errChecker_msg;
            $resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $tempTID;
            $resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $tempTID;
            $resultArray['errorform'] = 'error.tpl';
            $showError = true;
            $authorised = false;
            $authorisedStatus = 0;

        }
        else if ($errChecker == 4)
		{
            //HTTP READ TIMEOUT - o Limite de Tempo da transação foi estourado
            $resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': Ocorreu um erro em sua transação!';
            $resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . " : " . $errChecker_msg;
            $resultArray['data3'] = SmartyObj::getParamValue('Order', 'str_LabelTransactionID') . ': ' . $tempTID;
            $resultArray['data4'] = SmartyObj::getParamValue('Order', 'str_LabelOrderNumber') . ': ' . $tempTID;
            $resultArray['errorform'] = 'error.tpl';
            $showError = true;
            $authorised = false;
            $authorisedStatus = 0;
        }

        if($OKorNot == 4)
        {
            $authorised = true;
            $authorisedStatus = 1;
        }

        // write to log file.
        $serverDate = date('Y-m-d');
        $serverTime = date("H:i:s");

        PaymentIntegrationObj::logPaymentGatewayData($CieloConfig, $serverTime);

        $resultArray['result'] = $OKorNot;
        $resultArray['ref'] = $gSession['ref'];
        $resultArray['amount'] = $gSession['order']['ordertotaltopay'];
        $resultArray['formattedamount'] = $gSession['order']['ordertotaltopay'];
        $resultArray['charges'] = '000';
        $resultArray['formattedcharges'] = '';
        $resultArray['authorised'] = $authorised;
        $resultArray['authorisedstatus'] = $authorisedStatus;
        $resultArray['transactionid'] = $returnTID;
        $resultArray['formattedtransactionid'] = $returnTID;
        $resultArray['responsecode'] = $OKorNot;
        $resultArray['responsedescription'] = $OKorNot_message;
        $resultArray['authorisationid'] = $returnTID;  // this is our unique ID, not the real order ID
        $resultArray['formattedauthorisationid'] = $returnTID;
        $resultArray['bankresponsecode'] = $OKorNot;
        $resultArray['cardnumber'] = '';
        $resultArray['formattedcardnumber'] = '';
        $resultArray['cvvflag'] = '';
        $resultArray['cvvresponsecode'] = '';
        $resultArray['paymentcertificate'] = $returnTID;
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
        $resultArray['threedsecurestatus'] = " ";
        $resultArray['cavvresponsecode'] = " ";
        $resultArray['update'] = false;
        $resultArray['orderid'] = 0;
        $resultArray['parentlogid'] = 0;
        $resultArray['resultisarray'] = false;
        $resultArray['resultlist'] = Array();
        $resultArray['showerror'] = $showError;

        return $resultArray;
    }

    static function httprequest($paEndereco, $xmlPost)
    {
        //Making the http requests to send and get back the xmla data.
        $session_curl = curl_init();
        curl_setopt($session_curl, CURLOPT_URL, $paEndereco);
        curl_setopt($session_curl, CURLOPT_FAILONERROR, true);

        //  CURLOPT_SSL_VERIFYPEER
        curl_setopt($session_curl, CURLOPT_SSL_VERIFYPEER, true);
        //  CURLOPPT_SSL_VERIFYHOST
        curl_setopt($session_curl, CURLOPT_SSL_VERIFYHOST, 2);

        //  CURLOPT_SSL_CAINFO
        curl_setopt($session_curl, CURLOPT_CAINFO, UtilsObj::getCurlPEMFilePath());
        
        curl_setopt($session_curl, CURLOPT_SSLVERSION, 0);

        //  CURLOPT_CONNECTTIMEOUT
        curl_setopt($session_curl, CURLOPT_CONNECTTIMEOUT, 10);

        //  CURLOPT_TIMEOUT
        curl_setopt($session_curl, CURLOPT_TIMEOUT, 40);

        //  CURLOPT_RETURNTRANSFER
        curl_setopt($session_curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($session_curl, CURLOPT_POST, true);
        curl_setopt($session_curl, CURLOPT_POSTFIELDS, $xmlPost );

        $result_curl = curl_exec($session_curl);

        $curlOut="";

        if ($result_curl)
        {
            $curlOut = $result_curl;
        }
        else
        {
            $curlOut = curl_error($session_curl);
        }
        
        return $curlOut;
    }

    //Check the xml response text
    static function errorCheck($responseIn)
    {
        $error_msg = array(0, "");

        try
        {
            if(stripos($responseIn, "SSL certificate problem") !== false)
            {
                //INVALID CERTIFICATE - A certificate of the transaction was not approved
                $error_msg[0] = 1;
            }

            $objReply = simplexml_load_string($responseIn, null, LIBXML_NOERROR);
            if($objReply == null)
            {
                //The transcation has timed out
                $error_msg[0] = 2;
            }
        }
        catch (Exception $ex)
        {
            // Curl errors
            $error_msg[0] = 3;
            $error_msg[1] = $ex->getMessage();
        }

        // Curl errors
        if($objReply->getName() == "erro")
        {
            $error_msg[0] = 4;
            $error_msg[1] = $ex->getMessage();
        }

        return $error_msg;
    }
}

?>