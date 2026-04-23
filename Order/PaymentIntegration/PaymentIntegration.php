<?php

class PaymentIntegrationObj
{
    protected static $loadedGateway = null;
    protected static $klarnaInstance = null;

    static function loadGateway($pPaymentMethodCode, $pCCIType)
    {
		global $gSession;

        if ($pPaymentMethodCode == 'CARD')
        {
            switch ($pCCIType)
            {
                case 'CYBERPLUS':
                    require_once('../Order/PaymentIntegration/CCICyberPlus.php');
                    break;
                case 'EasyPay2':
                    require_once('../Order/PaymentIntegration/EasyPay2.php');
                    break;
                case 'ePDQ':
                    require_once('../Order/PaymentIntegration/ePDQ.php');
                    break;
                case 'PayEase':
                    require_once('../Order/PaymentIntegration/PayEase.php');
                    break;
                case 'SagePay':
                    $sagePayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/SagePay.conf', '', $gSession['webbrandcode']);

					// default to sagepay form if integration type is not configured
					$sagePayIntegrationType = UtilsObj::getArrayParam($sagePayConfig, 'INTEGRATIONTYPE', 'form');

					if (strtolower($sagePayIntegrationType) == 'server')
					{
						require_once('../Order/PaymentIntegration/SagePayServer.php');
					}
					else
					{
						require_once('../Order/PaymentIntegration/SagePay.php');
					}
                    break;
                case 'Kasikorn':
                    require_once('../Order/PaymentIntegration/Kasikorn.php');
                    break;
                case 'Karandash':
                    require_once('../Order/PaymentIntegration/Karandash.php');
                    break;
                case 'DIBS':
                    require_once('../Order/PaymentIntegration/DIBS.php');
                    break;
                case 'mPP':
                    require_once('../Order/PaymentIntegration/mPP.php');
                    break;
                case 'WorldPay':
                    require_once('../Order/PaymentIntegration/WorldPay.php');
                    break;
                case 'eSelect':
                    require_once('../Order/PaymentIntegration/eSelect.php');
                    break;
                case 'PayDollar':
                    require_once('../Order/PaymentIntegration/PayDollar.php');
                    break;
                case 'NAB':
                    require_once('../Order/PaymentIntegration/NAB.php');
                    break;
                case 'DirecPay':
                   require_once('../Order/PaymentIntegration/DirecPay.php');
                    break;
                case 'IPPayments':
                    require_once('../Order/PaymentIntegration/IPPayments.php');
                    break;
                case 'CIM':
                    require_once('../Order/PaymentIntegration/CIMItalia.php');
                    break;
                case 'Alipay':
                    require_once('../Order/PaymentIntegration/Alipay.php');
                    break;
                case 'CMCIC':
                    require_once('../Order/PaymentIntegration/CMCIC.php');
                    break;
                case 'APCO':
                    require_once('../Order/PaymentIntegration/APCO.php');
                    break;
                case 'PAYONE':
                    require_once('../Order/PaymentIntegration/Payone.php');
                    break;
				case 'PayTrail':
					require_once('../Order/PaymentIntegration/PayTrail.php');
					break;
				case 'PayU':
				case 'PAYU':
                    require_once('../Order/PaymentIntegration/PayU.php');
                    break;
                case 'GMO':
                    require_once('../Order/PaymentIntegration/GMO.php');
                    break;
                case 'PAYGENT':
                    require_once('../Order/PaymentIntegration/Paygent.php');
                    break;
                case 'PAGOSONLINE':
                    require_once('../Order/PaymentIntegration/PagosOnline.php');
                    break;
                case 'INILITE':
                    require_once('../Order/PaymentIntegration/INILite.php');
                    break;
                case 'Intesa':
                    require_once('../Order/PaymentIntegration/Intesa.php');
                    break;
                case 'IPayPayGate':
                    require_once('../Order/PaymentIntegration/IPayPayGate.php');
                    break;
                case 'OGONE':
                    require_once('../Order/PaymentIntegration/Ogone.php');
                    break;
                case 'Durango':
                    require_once('../Order/PaymentIntegration/Durango.php');
                    break;
                case 'TCON':
                    require_once('../Order/PaymentIntegration/TCON.php');
                    break;
                case 'VCS':
                    require_once('../Order/PaymentIntegration/VCS.php');
                    break;
                case 'SIS':
                    require_once('../Order/PaymentIntegration/SISVirtualPOS.php');
                    break;
                case 'REDSYS':
                    require_once('../Order/PaymentIntegration/redsys.php');
                    break;
                case 'BIM':
                    require_once('../Order/PaymentIntegration/MillenniumBIM.php');
                    break;
                case 'DOKU':
                    require_once('../Order/PaymentIntegration/doku.php');
                    break;
                case 'ANZ':
                    require_once('../Order/PaymentIntegration/Anz.php');
                    break;
                case 'SanKyu':
                    require_once('../Order/PaymentIntegration/SanKyu.php');
                    break;
                case 'AudiBank':
                    require_once('../Order/PaymentIntegration/AudiBank.php');
                    break;
                case 'DECIDIR':
                    require_once('../Order/PaymentIntegration/DECIDIR.php');
                    break;
                case 'ACCOSAPG':
                    require_once('../Order/PaymentIntegration/AccosaPG.php');
                    break;
                case 'PAYFORT':
                    require_once('../Order/PaymentIntegration/PayFort.php');
                    break;
                case 'UniCredit':
                    require_once('../Order/PaymentIntegration/UniCredit.php');
                    break;
                case 'Computop':
                    require_once('../Order/PaymentIntegration/Computop.php');
                    break;
                case 'Cielo':
                    require_once('../Order/PaymentIntegration/Cielo.php');
                    break;
                case 'PayFast':
                    require_once('../Order/PaymentIntegration/PayFast.php');
                    break;
				case 'Realex':
                    require_once('../Order/PaymentIntegration/Realex.php');
                    break;
                case 'PayBox':
                    require_once('../Order/PaymentIntegration/PayBox.php');
                    break;
                case 'CNPKZ':
                    require_once('../Order/PaymentIntegration/CNprocessingKZ.php');
					break;
				case 'cPay':
					require_once('../Order/PaymentIntegration/cPay.php');
                    break;
                case 'UNLIMIT':
                    require_once('../Order/PaymentIntegration/Unlimit.php');
                    break;
                case 'WebPay':
                case 'MakeCommerce':
                case 'Tap':
                case 'Stripe':
                    self::$loadedGateway = self::createPaymentGatewayInstanceReferenced($gSession, $pCCIType);
                    break;
				default:
                    self::$loadedGateway = self::createPaymentGatewayInstance($gSession, $pCCIType);
					break;
            }
        }
        elseif ($pPaymentMethodCode == 'PAYPAL')
        {
			$paypalConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayPal.conf', '', $gSession['webbrandcode']);
			// default to PayPal Express if integration type is not configured
			$PayPalIntegrationType = UtilsObj::getArrayParam($paypalConfig, 'PAYPALINTEGRATIONTYPE', 'express');

			if (strtolower($PayPalIntegrationType) == 'plus')
			{
				require_once('../Order/PaymentIntegration/PayPalPlus.php');
			}
			else
			{
				require_once('../Order/PaymentIntegration/PayPal.php');
			}

        }
        elseif ($pPaymentMethodCode == 'KLARNA')
        {
            self::$klarnaInstance = self::createPaymentGatewayInstance($gSession, 'Klarna');
        }
    }

    static function configure($pPaymentMethodCode)
    {
        global $gSession;

        $resultArray = Array();

        $resultArray['active'] = false;
        $resultArray['form'] = '';
        $resultArray['link'] = '';
        $resultArray['requestpaymentparamsremotely'] = false;
        self::loadGateway($pPaymentMethodCode, $gSession['order']['ccitype']);

        if ($pPaymentMethodCode == 'CARD')
        {
            switch ($gSession['order']['ccitype'])
            {
                case 'CYBERPLUS':
                    $resultArray = CCICyberPlusObj::configure();
                    break;
                case 'ePDQ':
                    $resultArray = ePDQObj::configure();
                    break;
                case 'EasyPay2':
                    $resultArray = EasyPay2Obj::configure();
                    break;
                case 'PayEase':
                    $resultArray = PayEaseObj::configure();
                    break;
                case 'SagePay':
                    $resultArray = SagePayObj::configure();
                    break;
                case 'Kasikorn':
                    $resultArray = KasikornObj::configure();
                    break;
                case 'Karandash':
                    $resultArray = KarandashObj::configure();
                    break;
                case 'DIBS':
                    $resultArray = DIBSObj::configure();
                    break;
                case 'mPP':
                    $resultArray = mPPObj::configure();
                    break;
                case 'WorldPay':
                    $resultArray = WorldPayObj::configure();
                    break;
                case 'eSelect':
                    $resultArray = eSelectObj::configure();
                    break;
                case 'PayDollar':
                    $resultArray = PayDollarObj::configure();
                    break;
                case 'NAB':
                    $resultArray = NABObj::configure();
                    break;
                case 'DirecPay':
                    $resultArray = DirecPayObj::configure();
                    break;
                case 'IPPayments':
                    $resultArray = IPPaymentsObj::configure();
                    break;
                case 'CIM':
                    $resultArray = CIMObj::configure();
                    break;
                case 'Alipay':
                    $resultArray = AlipayObj::configure();
                    break;
                case 'CMCIC':
                    $resultArray = CMCICObj::configure();
                    break;
                case 'APCO':
                    $resultArray = ApcoObj::configure();
                    break;
                case 'PAYONE':
                    $resultArray = PayoneObj::configure();
                    break;
				case 'PayTrail':
                    $resultArray = PayTrailObj::configure();
                    break;
				case 'PayU':
				case 'PAYU':
                    $resultArray = PayUObj::configure();
                    break;
                case 'GMO':
                    $resultArray = GMOObj::configure();
                    break;
                case 'PAYGENT':
                    $resultArray = PaygentObj::configure();
                    break;
                case 'PAGOSONLINE':
                    $resultArray = PagosOnlineObj::configure();
                    break;
                case 'INILITE':
                    $resultArray = INILiteObj::configure();
                    break;
                case 'Intesa':
                    $resultArray = IntesaObj::configure();
                    break;
                case 'IPayPayGate':
                    $resultArray = IPayPayGateObj::configure();
                    break;
                case 'OGONE':
                    $resultArray = OgoneObj::configure();
                    break;
                case 'Durango':
                    $resultArray = DurangoObj::configure();
                    break;
                case 'TCON':
                    $resultArray = TconObj::configure();
                    break;
                case 'VCS':
                    $resultArray = VCSObj::configure();
                    break;
                case 'SIS':
                    $resultArray = SISObj::configure();
					break;
                case 'REDSYS':
                    $resultArray = REDSYSObj::configure();
                    break;
                case 'BIM':
                    $resultArray = MillenniumBIMObj::configure();
                    break;
                case 'DOKU':
                    $resultArray = DokuObj::configure();
                    break;
                case 'ANZ':
                    $resultArray = AnzObj::configure();
                    break;
                case 'SanKyu':
                    $resultArray = SanKyuObj::configure();
                    break;
                case 'AudiBank':
                    $resultArray = AudiBankObj::configure();
                    break;
                case 'DECIDIR':
                    $resultArray = DecidirObj::configure();
                    break;
                case 'ACCOSAPG':
                    $resultArray = AccosaPGObj::configure();
                    break;
                case 'PAYFORT':
                    $resultArray = PayFortObj::configure();
                    break;
                case 'UniCredit':
                    $resultArray = UniCreditObj::configure();
                    break;
                case 'Computop':
                    $resultArray = ComputopObj::configure();
                    break;
                case 'Cielo':
                    $resultArray = CieloObj::configure();
                    break;
                case 'PayFast':
                    $resultArray = PayFastObj::configure();
                    break;
				case 'Realex':
                    $resultArray = RealexObj::configure();
                    break;
                case 'PayBox':
                    $resultArray = PayBoxObj::configure();
                    break;
                case 'CNPKZ':
                    $resultArray = CNPKZObj::configure();
					break;
				case 'cPay':
					$resultArray = CPayObj::configure();
					break;
                case 'UNLIMIT':
                    $resultArray = Unlimit::configure();
                    break;
                default:
                    //Sanity check that we have an instance
					if(self::$loadedGateway !== null)
					{
						$gateway = self::$loadedGateway;
						//This returns the array returned by PaymentGateway::configure
						$resultArray = $gateway->configure();
					}
					break;
            }

            // next line to be taken into the configure() functions
            if (! in_array($gSession['order']['ccitype'], array("iPay88", "PAYONE", "PAYGENT", "INILITE", "INICIS")))
            {
                $resultArray['gateways'] = Array();
			}

			// Existing gateways won't return this value.
			if (! array_key_exists('requestpaymentparamsremotely', $resultArray))
			{
				$resultArray['requestpaymentparamsremotely'] = false;
			}
        }
        elseif ($pPaymentMethodCode == 'PAYPAL')
        {
            $resultArray = PayPalObj::configure();
            // next line to be taken into the configure() function
            $resultArray['gateways'] = Array();
            $resultArray['requestpaymentparamsremotely'] = false;
        }
        elseif ($pPaymentMethodCode == 'KLARNA')
        {
            //Sanity check that we have an instance
            if(self::$klarnaInstance !== null)
            {
                $gateway = self::$klarnaInstance;
                //This returns the array returned by PaymentGateway::configure
                $resultArray = $gateway->configure();
            }
        }

        return $resultArray;
    }

    static function initialize()
    {
        global $gSession;

        // increase the standard php timeout just incase we need to interact with the payment service at this stage
        UtilsObj::resetPHPScriptTimeout(90);

        self::loadGateway($gSession['order']['paymentmethodcode'], $gSession['order']['ccitype']);

        if ($gSession['order']['paymentmethodcode'] == 'CARD')
        {
            switch ($gSession['order']['ccitype'])
            {
                case 'CYBERPLUS':
                    return CCICyberPlusObj::initialize();
                    break;
                case 'ePDQ':
                    return ePDQObj::initialize();
                    break;
                case 'EasyPay2':
                    return EasyPay2Obj::initialize();
                    break;
                case 'PayEase':
                    return PayEaseObj::initialize();
                    break;
                case 'SagePay':
                    return SagePayObj::initialize();
                    break;
                case 'Kasikorn':
                    return KasikornObj::initialize();
                    break;
                case 'DIBS':
                    return DIBSObj::initialize();
                    break;
                case 'mPP':
                    return mPPObj::initialize();
                    break;
                case 'WorldPay':
                    return WorldPayObj::initialize();
                    break;
                case 'eSelect':
                    return eSelectObj::initialize();
                    break;
                case 'GMO':
                    return GMOObj::initialize();
                    break;
                case 'PayDollar':
                    return PayDollarObj::initialize();
                    break;
                case 'NAB':
                    return NABObj::initialize();
                    break;
                case 'DirecPay':
                    return DirecPayObj::initialize();
                    break;
                case 'IPPayments':
                    return IPPaymentsObj::initialize();
                    break;
                case 'IPayPayGate':
                    return IPayPayGateObj::initialize();
                    break;
                case 'CIM':
                    return CIMObj::initialize();
                    break;
                case 'Alipay':
                    return AlipayObj::initialize();
                    break;
                case 'CMCIC':
                    return CMCICObj::initialize();
                    break;
                case 'APCO':
                    return ApcoObj::initialize();
                    break;
                case 'PAYONE':
                    return PayoneObj::initialize();
                    break;
				case 'PayTrail':
                    return PayTrailObj::initialize();
                    break;
				case 'PayU':
				case 'PAYU':
                    return PayUObj::initialize();
                    break;
                case 'PAYGENT':
                    return PaygentObj::initialize();
                    break;
                case 'PAGOSONLINE':
                    return PagosOnlineObj::initialize();
                    break;
                case 'INILITE':
                    return INILiteObj::initialize();
                    break;
                case 'Intesa':
                    return IntesaObj::initialize();
                    break;
                case 'OGONE':
                    return OgoneObj::initialize();
                    break;
                case 'Durango':
                    return DurangoObj::initialize();
                    break;
                case 'VCS':
                    return VCSObj::initialize();
                    break;
                case 'SIS':
                    return SISObj::initialize();
                    break;
                case 'REDSYS':
                    return REDSYSObj::initialize();
                    break;
                case 'TCON':
                    return TconObj::initialize();
                    break;
                case 'BIM':
                    return MillenniumBIMObj::initialize();
                    break;
                case 'DOKU':
                    return DokuObj::initialize();
                    break;
                 case 'ANZ':
                    return AnzObj::initialize();
                    break;
                case 'SanKyu':
                    return SanKyuObj::initialize();
                    break;
                case 'Karandash':
                    return KarandashObj::initialize();
                    break;
                case 'AudiBank':
                    return AudiBankObj::initialize();
                    break;
                case 'DECIDIR':
                    return DecidirObj::initialize();
                    break;
                case 'ACCOSAPG':
                    return AccosaPGObj::initialize();
                    break;
                case 'PAYFORT':
                    return PayFortObj::initialize();
                    break;
                case 'UniCredit':
                    return UniCreditObj::initialize();
                    break;
                case 'Computop':
                    return ComputopObj::initialize();
                    break;
                case 'Cielo':
                    return CieloObj::initialize();
                    break;
                case 'PayFast':
                    return PayFastObj::initialize();
                    break;
				case 'Realex':
                     return RealexObj::initialize();
                    break;
                case 'PayBox':
                     return PayBoxObj::initialize();
                    break;
                case 'CNPKZ':
                    return CNPKZObj::initialize();
                    break;
				case 'cPay':
					return CPayObj::initialize();
                    break;
                case 'UNLIMIT':
                    return Unlimit::initialize();
                    break;
				default:
					//Sanity check that we have an instance
					if(self::$loadedGateway !== null)
					{
						$gateway = self::$loadedGateway;
						//This returns the array returned by PaymentGateway::initialize
						return $gateway->initialize();
					}
					else
					{
						$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
						$smarty->assign('error1', 'Unknown Credit Card Integration');
						$smarty->assign('error2', '');
						$smarty->displayLocale('error.tpl');
					}
					break;
            }
        }
        elseif ($gSession['order']['paymentmethodcode'] == 'PAYPAL')
        {
            return PayPalObj::initialize();
        }
        elseif ($gSession['order']['paymentmethodcode'] == 'KLARNA')
        {
            //Sanity check that we have an instance
            if(self::$klarnaInstance !== null)
            {
                $gateway = self::$klarnaInstance;

                //This returns the array returned by PaymentGateway::configure
                return $gateway->initialize();
            }
        }
    }

    static function ccAutomaticCallback()
    {
        global $gSession;
        $resultArray = Array();

        $authorised = false;
        $update = false;
        $loadSession = true;
        $updateSession = false;
        $serverDate = DatabaseObj::getServerTime();
        $nextStage = '';
        $statusForInformationOnly = false;
        $logCCIEntryForSameTransactionID = false;

        if (array_key_exists('pm', $_GET))
        {
            $paymentMethod = $_GET['pm'];
        }
        else
        {
            $paymentMethod = 'CARD';
        }

        if (array_key_exists('ref', $_GET))
        {
            $sessionRef = $_GET['ref'];
        }
        else
        {
            $sessionRef = 0;
        }

        if ($paymentMethod == 'CARD')
        {
            // make sure we have an order array (could be undefined if the session cannot be loaded yet & integration has no dedicated callback)
            if (! array_key_exists('order', $gSession))
            {
                $gSession['order'] = AuthenticateObj::createSessionOrderData();
                $gSession['items'] = Array(AuthenticateObj::createSessionOrderLine());
                $gSession['shipping'] = Array(AuthenticateObj::createSessionShippingLine());
                // add COVER and PAPER component
                $component = DatabaseObj::addSessionOrderItemComponent(0, 'COVER');
                $component = DatabaseObj::addSessionOrderItemComponent(0, 'PAPER');
            }

            // make sure we have a ccitype (could be blank if the session cannot be loaded yet & integratoin has no dedicated callback)
            if ($gSession['order']['ccitype'] == '')
            {
                $brandingDefaults = DatabaseObj::getBrandingFromCode('');
                $gSession['order']['ccitype'] = $brandingDefaults['paymentintegration'];
            }

			// If ccitype is still blank at this point (Added 27/10/2015)
			if (($gSession['order']['ccitype'] == '') && ($sessionRef != 0))
			{
				$ccilog = self::getCciLogEntry($sessionRef);
				$gSession['order']['ccitype'] = $ccilog['type'];
			}

            self::loadGateway($paymentMethod, $gSession['order']['ccitype']);

            switch ($gSession['order']['ccitype'])
            {
                case 'CYBERPLUS':
                    $cciResultArray = CCICyberPlusObj::automaticConfirm();
                    // cyberplus returns the session in the url which is automatically re-loaded by the main entry point code
                    $loadSession = false;
                    break;
                case 'ePDQ':
                    $cciResultArray = ePDQObj::confirm('automatic');
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'EasyPay2':
                    $cciResultArray = EasyPay2Obj::automaticCallback();
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'PayEase':
                    $cciResultArray = PayEaseObj::offlineCallback();

                    // there is no need to load the session as this will be called off-line
                    $loadSession = false;
                    $update = $cciResultArray['update'];
                    break;
                case 'SagePay':
					$sagePayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/SagePay.conf', '', $gSession['webbrandcode']);

					// default to sagepay form if integration type is not configured
					$sagePayIntegrationType = UtilsObj::getArrayParam($sagePayConfig, 'INTEGRATIONTYPE', 'form');

					if (strtolower($sagePayIntegrationType) == 'server')
					{
						$cciResultArray = SagePayObj::automaticCallback();
						$update = $cciResultArray['update'];
					}
					else
					{
						$cciResultArray = SagePayObj::confirm();
					}

                    // sagepay returns the session in the url which is automatically re-loaded by the main entry point code
                    $loadSession = false;
                    break;
                case 'Kasikorn':
                    $cciResultArray = KasikornObj::automaticCallback();

                    // the Kasikorn module re-loads the session so no need to re-load it
                    $loadSession = false;
                    break;
                case 'DIBS':
                    $cciResultArray = DIBSObj::confirm();

                    // DIBS returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;
                    break;
                case 'mPP':
                    $cciResultArray = mPPObj::automaticCallback();

                    $nextStage = $cciResultArray['nextstage'];

                    // there is no need to load the session
                    $loadSession = false;
                    break;
                case 'PayDollar':
                    $cciResultArray = PayDollarObj::confirm('automatic');

                    // the paydollar module re-loads the session so no need to re-load it
                    $loadSession = false;
                    break;
                case 'NAB':
                    $cciResultArray = NABObj::confirm();

                    // the NAB module re-loads the session so no need to re-load it
                    $loadSession = false;
                    break;
                case 'IPPayments':
                    $cciResultArray = IPPaymentsObj::automaticCallback();
                    $update = $cciResultArray['update'];
                    // the IPPayments module re-loads the session so no need to re-load it
                    $loadSession = false;
                    break;
                case 'IPayPayGate':
                    $cciResultArray = IPayPayGateObj::automaticCallback();
                    $update = $cciResultArray['update'];
                    // the IPPayments module re-loads the session so no need to re-load it
                    $loadSession = false;
                    break;
                case 'CIM':
                    $cciResultArray = CIMObj::confirm('automatic');
                    $update = $cciResultArray['update'];

                    $loadSession = false;
                    break;
                case 'Alipay':
                    $cciResultArray = AlipayObj::confirm();
                    $update = $cciResultArray['update'];

                    $loadSession = false;
                    break;
                case 'CMCIC':
                    $cciResultArray = CMCICObj::confirm('automatic');
                    $update = $cciResultArray['update'];

                    $loadSession = false;
                    break;
                case 'APCO':
                    if (array_key_exists('params', $_POST))
                    {
                        $cciResultArray = ApcoObj::confirm('automatic');
                        $update = $cciResultArray['update'];
                    }
                    else
                    {
                        ApcoObj::AutomaticCancel();
                    }
                    $loadSession = false;
                    break;
                case 'PAYONE':
                    $cciResultArray = PayoneObj::automaticCallback();
                    $update = $cciResultArray['update'];

                    $loadSession = false;
                    break;
				case 'PayTrail':
                    $cciResultArray = PayTrailObj::confirm();
                    $update = $cciResultArray['update'];
                    // the paytrail module re-loads the session so no need to re-load it
                    $loadSession = false;
                    break;
				case 'PayU':
				case 'PAYU':
                    $cciResultArray = PayUObj::automaticCallback();
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'PAYGENT':
                    $cciResultArray = PaygentObj::confirm('automatic');
                    $update = $cciResultArray['update'];

                    $loadSession = false;
                    break;
                case 'PAGOSONLINE':
                    $cciResultArray = PagosOnlineObj::automaticCallback();
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'OGONE':
                    $cciResultArray = OgoneObj::confirm('automatic');
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'VCS':
                    $cciResultArray = VCSObj::confirm('automatic');
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'SIS':
                    $cciResultArray = SISObj::confirm('automatic');
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'REDSYS':
                    $cciResultArray = REDSYSObj::confirm('automatic');
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'TCON':
                    $cciResultArray = TconObj::automaticCallback();
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'DOKU':
                    $cciResultArray = DokuObj::confirm();
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'GMO':
                    $cciResultArray = GMOObj::automaticCallback();
                    $update = $cciResultArray['update'];
					$loadSession = false;
					$logCCIEntryForSameTransactionID = true;
                    break;
                case 'Intesa':
                    $cciResultArray = IntesaObj::automaticCallback();
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'SanKyu':
                    $cciResultArray = SanKyuObj::automaticCallback();
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'DECIDIR':
                    $cciResultArray = DecidirObj::automaticCallback();
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'PAYFORT':
                    $cciResultArray = PayFortObj::confirm('automatic');
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'UniCredit':
                    $cciResultArray = UniCreditObj::confirm('automatic');
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'Computop':
                    $cciResultArray = ComputopObj::confirm('automatic');
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                 case 'DirecPay':
                    $cciResultArray = DirecPayObj::confirm('automatic');
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
               case 'PayFast':
                    $cciResultArray = PayFastObj::automaticCallback();
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
				case 'Realex':
                    $cciResultArray = RealexObj::automaticCallback();
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'PayBox':
                    $cciResultArray = PayBoxObj::confirm('automatic');
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'CNPKZ':
                    $cciResultArray = CNPKZObj::confirm('automatic');
                    $update = $cciResultArray['update'];
                    $loadSession = false;
                    break;
                case 'UNLIMIT':
                    $cciResultArray = Unlimit::confirm('automatic');
                    $update = $cciResultArray['update'];
                    $loadSession = true;
                    break;

                // there will never be an automatic callback for these
                case 'eSelect':
                case 'WorldPay':
                case 'INILITE':
                case 'Durango':
                case 'Karandash':
				case 'cPay':
                    $loadSession = false; // there is no need to load the session
                    break;
                case 'BIM':
                case 'AudiBank':
                case 'ACCOSAPG':
                case 'Cielo':
					// these performed no action
					break;

				default:
					// sanity check that we have an instance
					if(self::$loadedGateway !== null)
					{
						$gateway = self::$loadedGateway;
						// this returns the array returned by paymentgateway::confirm('automatic')
						$cciResultArray = $gateway->confirm('automatic');
						$update = $gateway->getUpdateStatus();
						$loadSession = $gateway->getLoadSession();
						$statusForInformationOnly = $gateway->getStatusForInformationOnly();
						$logCCIEntryForSameTransactionID = $gateway->getStatusForLoggingCCIEntryForSameTransactionID();

					}
					break;

            }

            $cciResultArray['type'] = $gSession['order']['ccitype'];
            $update = $cciResultArray['update'];
        }
        elseif ($paymentMethod == 'PAYPAL')
        {
            self::loadGateway($paymentMethod, '');

            $cciResultArray = PayPalObj::confirm();
            $cciResultArray['type'] = 'PAYPAL';
            $update = $cciResultArray['update'];

            // paypal returns the session in the url which is automatically re-loaded by the main entry point code
            $loadSession = false;
        }
        elseif ($paymentMethod == 'KLARNA')
        {
            self::loadGateway($paymentMethod, '');

            //Sanity check that we have an instance
            if(self::$klarnaInstance !== null)
            {
                $gateway = self::$klarnaInstance;
                $cciResultArray = $gateway->confirm('automatic');
                $cciResultArray['type'] = 'KLARNA';
                $update = $gateway->getUpdateStatus();
			    $loadSession = $gateway->getLoadSession();
				$statusForInformationOnly = $gateway->getStatusForInformationOnly();
			    $logCCIEntryForSameTransactionID = $gateway->getStatusForLoggingCCIEntryForSameTransactionID();
            }
        }

        if ($update == true)
        {
            // this is a status update to an existing payment transaction sent by the payment system
            if ($cciResultArray['resultisarray'] == true)
            {
                $dbObj = DatabaseObj::getGlobalDBConnection();
                if ($dbObj)
                {
                    if ($stmt = $dbObj->prepare('UPDATE `ORDERHEADER` SET `ccilogid` = ?, `paymentreceived` = ?, `paymentreceivedtimestamp` = ?, `paymentreceiveddate` = ?, `paymentreceiveduserid` = ?  WHERE `id` = ?'))
                    {
                        $resultArray = $cciResultArray['resultlist'];
                        $itemCount = count($resultArray);

                        for ($i = 0; $i < $itemCount; $i++)
                        {
                            $cciResultArray = $resultArray[$i];
                            $cciResultArray['type'] = $gSession['order']['ccitype'];

                            // first create the log entry
                            $logResultArray = self::logCCIResult($cciResultArray['ref'], $cciResultArray['userid'], 'AUTOMATIC', $cciResultArray);

                            // set paymentReceived information
                            if ((($paymentMethod == 'CARD') || ($paymentMethod == 'PAYPAL') || ($paymentMethod == 'KLARNA')) && ($cciResultArray['paymentreceived'] == 1))
                            {   // payment received
                                $paymentReceived = 1;
                                $paymentReceivedTimestamp = $cciResultArray['formattedpaymentdate'];
                                $paymentReceivedDate = $serverDate;
                                $paymentReceivedUserId = -1;
                            }
                            else
                            {   // no payment
                                $paymentReceived = 0;
                                $paymentReceivedTimestamp = '';
                                $paymentReceivedDate = '';
                                $paymentReceivedUserId = 0;
                            }

                            // we must now update the order header to point to the new log entry
                            if ($stmt->bind_param('iissii', $logResultArray['id'], $paymentReceived, $paymentReceivedTimestamp, $paymentReceivedDate, $paymentReceivedUserId, $cciResultArray['orderid']))
                            {
                                if ($stmt->execute())
								{
									// Trigger order paid when payment received.
									if ($paymentReceived)
									{

									    if (!class_exists('DataExportObj'))
                                        {
                                            require_once('../Utils/UtilsDataExport.php');
                                        }
										// Generate the trigger.
										DataExportObj::EventTrigger(TPX_TRIGGER_ORDER_PAID, 'ORDER', $cciResultArray['orderid'], $cciResultArray['orderid']);
									}
								}
                            }
                        }

                        $stmt->free_result();
                        $stmt->close();
                    }

                    $dbObj->close();
                }
            }
            else
            {
                if (($gSession['ref'] != '') && ($gSession['ref'] > 0))
                {
                    $sessionID = $gSession['ref'];
                    $userID = $gSession['userid'];
                }
                else
                {
                    $sessionID = 0;
                    $userID = 0;
                }

                $logResultArray = self::logCCIResult($sessionID, $userID, 'AUTOMATIC', $cciResultArray);

                // set paymentReceived information
                if ((($paymentMethod == 'CARD') || ($paymentMethod == 'PAYPAL') || ($paymentMethod == 'KLARNA')) && ($cciResultArray['paymentreceived'] == 1))
                {   // payment received
                    $paymentReceived = 1;
                    $paymentReceivedTimestamp = $cciResultArray['formattedpaymentdate'];
                    $paymentReceivedDate = $serverDate;
                    $paymentReceivedUserId = -1;
                }
                else
                {   // no payment
                    $paymentReceived = 0;
                    $paymentReceivedTimestamp = '';
                    $paymentReceivedDate = '';
                    $paymentReceivedUserId = 0;
                }

                // we must now update the order header to point to the new log entry
                $dbObj = DatabaseObj::getGlobalDBConnection();
                if ($dbObj)
                {
                    if ($stmt = $dbObj->prepare('UPDATE `ORDERHEADER` SET `ccilogid` = ?, `paymentreceived` = ?, `paymentreceivedtimestamp` = ?, `paymentreceiveddate` = ?, `paymentreceiveduserid` = ?  WHERE `id` = ?'))
                    {
                        if ($stmt->bind_param('iissii', $logResultArray['id'], $paymentReceived, $paymentReceivedTimestamp, $paymentReceivedDate, $paymentReceivedUserId, $cciResultArray['orderid']))
                        {
                            if ($stmt->execute())
							{
								// Trigger order paid when payment received.
								if ($paymentReceived)
								{
                                    if (!class_exists('DataExportObj'))
                                    {
                                        require_once('../Utils/UtilsDataExport.php');
                                    }
									// Generate the trigger.
									DataExportObj::EventTrigger(TPX_TRIGGER_ORDER_PAID, 'ORDER', $cciResultArray['orderid'], $cciResultArray['orderid']);
								}
							}
                        }
                        $stmt->free_result();
                        $stmt->close();
                    }
                    $dbObj->close();
                }
            }
        }
        else
        {
            // this is a new payment transaction
            // we need to load the session as there was no reference to load it from when the payment service
            // initiated the callback
            $sessionRef = $cciResultArray['ref'];
            if ($sessionRef > 0)
            {
                $authorised = $cciResultArray['authorised'];

                $_POST['ref'] = $sessionRef;

                if ($loadSession)
                {
                    $gSession = DatabaseObj::getSessionData($sessionRef);
                }

                // make sure we actually have a session
                if ($gSession['ref'] > 0)
                {
                    // if the transaction numbers are different create the log entry
                    // this is to prevent duplicate transactions where the manual and automatic callbacks contain the payment information

                    if (($gSession['order']['ccitransactionid'] != $cciResultArray['transactionid']) || ($logCCIEntryForSameTransactionID))
                    {
                        $logResultArray = self::logCCIResult($sessionRef, $gSession['userid'], 'AUTOMATIC', $cciResultArray);
                        $gSession['order']['ccilogid'] = $logResultArray['id'];
                        $gSession['order']['ccitransactionid'] = $cciResultArray['transactionid'];
                        $gSession['order']['cciauthorised'] = ($authorised == true ? 1 : 0);
                        $gSession['order']['ccipaymentreceived'] = $cciResultArray['paymentreceived'];
                        $gSession['order']['ccipaymentreceiveddatetime'] = $cciResultArray['formattedpaymentdate'];
                        $updateSession = true;
                    }
                }

                if ($updateSession == true)
                {
                    DatabaseObj::updateSession();
                }
            }
            else
            {
                $logResultArray = self::logCCIResult(-1, 0, 'AUTOMATIC', $cciResultArray);
            }
        }

        $cciResultArray['nextstage'] = $nextStage;
        $cciResultArray['authorised'] = $authorised;
        $cciResultArray['update'] = $update;
        $cciResultArray['statusforinformationonly'] = $statusForInformationOnly;

        return $cciResultArray;
    }

    static function ccManualCallback()
    {
        global $gSession;
        $loadSession = true;
        $updateSession = false;
        $updateCCILog = true;
        $authorised = false;
        $update = true;
        $logCCIEntryForSameTransactionID = false;

        if (array_key_exists('pm', $_GET))
        {
            $paymentMethod = $_GET['pm'];
        }
        else
        {
            $paymentMethod = 'CARD';
        }

        if (array_key_exists('ref', $_GET))
        {
            $sessionRef = $_GET['ref'];
        }
        else
        {
            $sessionRef = 0;
        }

        if ($paymentMethod == 'CARD')
        {
            // make sure we have an order array (could be undefined if the session cannot be loaded yet & integration has no dedicated callback)
            if (! array_key_exists('order', $gSession))
            {
                $gSession['order'] = AuthenticateObj::createSessionOrderData();
                $gSession['items'] = Array(AuthenticateObj::createSessionOrderLine());
                $gSession['shipping'] = Array(AuthenticateObj::createSessionShippingLine());
                // add COVER and PAPER component
                $component = DatabaseObj::addSessionOrderItemComponent(0, 'COVER');
                $component = DatabaseObj::addSessionOrderItemComponent(0, 'PAPER');
            }

            // make sure we have a ccitype (could be blank if the session cannot be loaded yet & integration has no dedicated callback)
            if ($gSession['order']['ccitype'] == '')
            {
                $brandingDefaults = DatabaseObj::getBrandingFromCode('');
                $gSession['order']['ccitype'] = $brandingDefaults['paymentintegration'];
            }

            self::loadGateway($paymentMethod, $gSession['order']['ccitype']);

            switch ($gSession['order']['ccitype'])
            {
                case 'CYBERPLUS':
                    $cciResultArray = CCICyberPlusObj::confirm();
                    $loadSession = false;
                    $update = false;
                    break;
                case 'ePDQ':
                    $cciResultArray = ePDQObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'EasyPay2':
                    $cciResultArray = EasyPay2Obj::manualCallback();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'PayEase':
                    $cciResultArray = PayEaseObj::confirm();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    break;
                case 'SagePay':
					$sagePayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/SagePay.conf', '', $gSession['webbrandcode']);

					// default to sagepay form if integration type is not configured
					$sagePayIntegrationType = UtilsObj::getArrayParam($sagePayConfig, 'INTEGRATIONTYPE', 'form');

					if (strtolower($sagePayIntegrationType) == 'server')
					{
						$cciResultArray = SagePayObj::manualCallback();
						$updateCCILog = true;
						$update = $cciResultArray['update'];
					}
					else
					{
						$cciResultArray = SagePayObj::confirm();
						$update = false;
					}

                    // sagepay returns the session in the url which is automatically re-loaded by the main entry point code
                    $loadSession = false;
                    break;
                case 'Kasikorn':
                    // the Kasikorn module re-loads the session so no need to re-load it
                    $loadSession = false;
                    $cciResultArray = KasikornObj::confirm();
                    $update = $cciResultArray['update'];

                    // if we have received the realtime response before the automatic response we don't need to update the log
                    if ($cciResultArray['authorised'] == true)
                    {
                        if ($update == true)
                        {
                            $updateCCILog = false;
                        }
                    }

                    // reset the ccidata for the session
                    $gSession['order']['ccidata'] = '';
                    $updateSession = true;
                    break;
                case 'Karandash':
                    // session will be loaded at this point
                    $loadSession = false;
                    $cciResultArray = KarandashObj::manualCallback();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    break;
                case 'DIBS':
                    // DIBS returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;
                    $cciResultArray = DIBSObj::confirm();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    break;
                case 'WorldPay':
                    // session will be loaded at this point
                    $loadSession = false;
                    $cciResultArray = WorldPayObj::confirm();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;

                    // only update the log if transaction was successful
                    $updateCCILog = $cciResultArray['authorised'];

                    break;
                case 'eSelect':
                    // session will be loaded at this point
                    $loadSession = false;
                    $cciResultArray = eSelectObj::confirm();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;

                    // only update the log if transaction was successful
                    $updateCCILog = $cciResultArray['authorised'];
                    break;
                case 'mPP':
                    // no manual callback for mPP
                    break;
                case 'PayDollar':
                    $cciResultArray = PayDollarObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = false;
                    break;
                case 'NAB':
                    $cciResultArray = NABObj::confirm();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'DirecPay':
                    $cciResultArray = DirecPayObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = $cciResultArray['authorised'];
                    break;
                case 'IPPayments':
                    $cciResultArray = IPPaymentsObj::manualCallback();
                    $update = false;
                    $updateCCILog = false;
                    break;
                case 'IPayPayGate':
                    $cciResultArray = IPayPayGateObj::manualCallback();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = false;
                    break;
                case 'CIM':
                    $cciResultArray = CIMObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'Alipay':
                    $cciResultArray = AlipayObj::confirm();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'CMCIC':
                    $cciResultArray = CMCICObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = false;
                    break;
                case 'APCO':
                    $cciResultArray = ApcoObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'PAYONE':
                    $cciResultArray = PayoneObj::manualCallback();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
				case 'PayTrail':
                    $cciResultArray = PayTrailObj::confirm();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
				case 'PayU':
				case 'PAYU':
                    $cciResultArray = PayUObj::manualCallback();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'PAYGENT':
                    $loadSession = false;
                    $cciResultArray = PaygentObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'PAGOSONLINE':
                    $cciResultArray = PagosOnlineObj::manualCallback();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'OGONE':
                    $cciResultArray = OgoneObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'Durango':
                    // session will be loaded at this point
                    $loadSession = false;
                    $cciResultArray = DurangoObj::confirm();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    break;
                case 'VCS':
                    // session will be loaded at this point
                    $loadSession = false;
                    $cciResultArray = VCSObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    // only update the log if transaction was successful
                    $updateCCILog = $cciResultArray['authorised'];
                    break;
                case 'SIS':
                    $cciResultArray = SISObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'REDSYS':
                    $cciResultArray = REDSYSObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'TCON':
                    $cciResultArray = TconObj::manualCallback();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'Intesa':
                    $cciResultArray = IntesaObj::manualCallback();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'INILITE':
                    // session will be loaded at this point
                    $loadSession = false;
                    $cciResultArray = INILiteObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = $cciResultArray['update'];
                    break;
                case 'BIM':
                    // session will be loaded at this point
                    $loadSession = false;
                    $cciResultArray = MillenniumBIMObj::confirm();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'DOKU':
                    $loadSession = false;
                    $cciResultArray = DokuObj::confirm();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    break;
                 case 'ANZ':
                    $loadSession = false;
                    $cciResultArray = AnzObj::confirm();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    break;
                case 'SanKyu':
                    $cciResultArray = SanKyuObj::manualCallback();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                 case 'GMO':
                    $cciResultArray = GMOObj::manualCallback();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
					$updateCCILog = true;
					$logCCIEntryForSameTransactionID = true;
                    break;
                case 'AudiBank':
                    // session will be loaded at this point
                    $loadSession = false;
                    $cciResultArray = AudiBankObj::confirm();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                 case 'DECIDIR':
                    // session will be loaded at this point
                    $loadSession = false;
                    $cciResultArray = DecidirObj::manualCallback();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'ACCOSAPG':
                    // session will be loaded at this point
                    $loadSession = false;
                    $cciResultArray = AccosaPGObj::confirm();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'PAYFORT':
                    $cciResultArray = PayFortObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'UniCredit':
                    $cciResultArray = UniCreditObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'Computop':
                    $cciResultArray = ComputopObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'Cielo':
                    // session will be loaded at this point
                    $loadSession = false;
                    $cciResultArray = CieloObj::confirm();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'PayFast':
                    // session will be loaded at this point
                     $loadSession = false;
                    $cciResultArray = PayFastObj::manualCallback();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = false;
                    break;
				case 'Realex':
                    $cciResultArray = RealexObj::manualCallback();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'PayBox':
                    $cciResultArray = PayBoxObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
                case 'CNPKZ':
                    $cciResultArray = CNPKZObj::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
                    break;
				case 'cPay':
                    $cciResultArray = CPayObj::manualCallback();
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = true;
					break;
                case 'UNLIMIT':
                    $cciResultArray = Unlimit::confirm('manual');
                    $sessionRef = $cciResultArray['ref'];
                    $update = false;
                    $updateCCILog = $cciResultArray['updateccilog'];
                    break;

				default:
					// sanity check that we have an instance
					if(self::$loadedGateway !== null)
					{
						$gateway = self::$loadedGateway;
						// this returns the array returned by paymentgateway::configure
						$cciResultArray = $gateway->confirm('manual');
						$sessionRef = $cciResultArray['ref'];
						// this is only configured in some existing gateways
						$loadSession = $gateway->getLoadSession();
						$update = $gateway->getUpdateStatus();
						$updateCCILog = $gateway->getCCIUpdate();
						$logCCIEntryForSameTransactionID = $gateway->getStatusForLoggingCCIEntryForSameTransactionID();
					}
					break;
            }

            $cciResultArray['type'] = $gSession['order']['ccitype'];
        }
        elseif ($paymentMethod == 'PAYPAL')
        {
            // paypal returns the session in the url which is automatically re-loaded by the main entry point code
            $loadSession = false;
            $updateCCILog = false;

            self::loadGateway($paymentMethod, '');
            $cciResultArray = PayPalObj::confirm();

            if ($cciResultArray['authorised'] == true)
            {
                if ($cciResultArray['update'] == false)
                {
                    if ($gSession['order']['cciauthorised'] == 0)
                    {
                        $updateCCILog = true;
                        $update = false;
                        $cciResultArray['type'] = 'PAYPAL';
                        $cciResultArray['authorised'] = true;
                    }
                }
            }
        }
        elseif ($paymentMethod == 'KLARNA')
        {
            self::loadGateway($paymentMethod, '');

            //Sanity check that we have an instance
            if(self::$klarnaInstance !== null)
            {
                $gateway = self::$klarnaInstance;
                $cciResultArray = $gateway->confirm('manual');
                $sessionRef = $cciResultArray['ref'];
                $cciResultArray['type'] = 'KLARNA';
                $update = $gateway->getUpdateStatus();
			    $loadSession = $gateway->getLoadSession();
			    $logCCIEntryForSameTransactionID = $gateway->getStatusForLoggingCCIEntryForSameTransactionID();
            }
        }

        $authorised = $cciResultArray['authorised'];

        // we may need to load the session as there was no reference to load it from when the payment service initiated the callback
        if ($sessionRef > 0)
        {
            $_POST['ref'] = $sessionRef;

            if ($loadSession)
            {
                 $gSession = DatabaseObj::getSessionData($sessionRef);
            }

            if ($updateCCILog)
            {
                if (($gSession['order']['ccitransactionid'] != $cciResultArray['transactionid']) || ($logCCIEntryForSameTransactionID))
                {
                    $logResultArray = self::logCCIResult($sessionRef, $gSession['userid'], 'MANUAL', $cciResultArray);

                    if ($gSession['ref'] > 0)
                    {
                        $gSession['order']['ccilogid'] = $logResultArray['id'];
                        $gSession['order']['ccitransactionid'] = $cciResultArray['transactionid'];
                        $gSession['order']['cciauthorised'] = ($authorised == true ? 1 : 0);
                        $gSession['order']['ccipaymentreceived'] = $cciResultArray['paymentreceived'];
                        $gSession['order']['ccipaymentreceiveddatetime'] = $cciResultArray['formattedpaymentdate'];
                        $updateSession = true;
                    }
                }
            }

            if ($updateSession == true)
            {
                DatabaseObj::updateSession();
            }
        }
        else
        {
            $logResultArray = self::logCCIResult(-1, 0, 'MANUAL', $cciResultArray);
        }

        $cciResultArray['previousstage'] = 'payment';
        $cciResultArray['nextstage'] = 'complete';

        $cciResultArray['authorised'] = $authorised;
        $cciResultArray['update'] = $update;

        return $cciResultArray;
    }

    static function ccCancelCallback()
    {
        global $gConstants;
        global $gSession;

        $cciResultArray = Array();

        $loadSession = true;
        $updateCCILog = true;
        $logCCIEntryForSameTransactionID = false;

        if (array_key_exists('pm', $_GET))
        {
            $paymentMethod = $_GET['pm'];
        }
        else
        {
            $paymentMethod = 'CARD';
        }

        if (array_key_exists('ref', $_GET))
        {
            $sessionRef = $_GET['ref'];
        }
        else
        {
            $sessionRef = 0;
        }

        if ($paymentMethod == 'CARD')
        {
            // make sure we have an order array (could be undefined if the session cannot be loaded yet & integration has no dedicated callback)
            if (! array_key_exists('order', $gSession))
            {
                $gSession['order'] = AuthenticateObj::createSessionOrderData();
                $gSession['items'] = Array(AuthenticateObj::createSessionOrderLine());
                $gSession['shipping'] = Array(AuthenticateObj::createSessionShippingLine());
                // add COVER and PAPER component
                $component = DatabaseObj::addSessionOrderItemComponent(0, 'COVER');
                $component = DatabaseObj::addSessionOrderItemComponent(0, 'PAPER');
            }

            // make sure we have a ccitype (could be blank if the session cannot be loaded yet & integration has no dedicated callback)
            if ($gSession['order']['ccitype'] == '')
            {
                $brandingDefaults = DatabaseObj::getBrandingFromCode('');
                $gSession['order']['ccitype'] = $brandingDefaults['paymentintegration'];
            }

            self::loadGateway($paymentMethod, $gSession['order']['ccitype']);

            switch ($gSession['order']['ccitype'])
            {
                case 'CYBERPLUS':
                    // cyberplus returns the session in the url which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = CCICyberPlusObj::cancel();
                    break;
                case 'ePDQ':
                     // ePDQ returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = ePDQObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'EasyPay2':
                     // EasyPay2 returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = EasyPay2Obj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;

                case 'PayEase':
                    // payease returns the session in the url which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = PayEaseObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'SagePay':
                    // Sage Pay returns the session in the url which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = SagePayObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    break;
                case 'Kasikorn':
                    // kasikorn returns the session in the url which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = KasikornObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    break;
                case 'Karandash':
                    // Karandash returns the session in the url which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = KarandashObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    break;
                case 'DIBS':
                    // DIBS returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = DIBSObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'mPP':
                    // mPP has no Cancel button, we only get here by pressing Backspace
                    $loadSession = false;

                    $cciResultArray = mPPObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'WorldPay':
                    // WorldPay returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = WorldPayObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'eSelect':
                    // eSelect returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = eSelectObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'PayDollar':
                    // paydollar returns the session in the url which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = PayDollarObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'NAB':
                    // NAB returns the session in the url which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = NABObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'DirecPay':
                    // DirecPay returns the session in the url which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = DirecPayObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'IPPayments':
                    // IPPayments returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = IPPaymentsObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = true;
                    break;
                case 'IPayPayGate':
                    // IPayPayGate returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = IPayPayGateObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = true;
                    break;
                case 'CIM':
                    // CIM returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = CIMObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'Alipay':
                    // Alipay returns the session in the GET which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = AlipayObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'CMCIC':
                    // CMCIC returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = CMCICObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'APCO':
                    // APCO returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = ApcoObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'PAYONE':
                    // PAYONE returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = PayoneObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
				case 'PayTrail':
                    // paytrail returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = PayTrailObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
				case 'PayU':
                    $loadSession = false;

                    $cciResultArray = PayUObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'PAYGENT':
                    // PAYGENT returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = PaygentObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'PAGOSONLINE':
                    // PAYGENT returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = PagosOnlineObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;

                case 'OGONE':
                    // Ogone returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = OgoneObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'Durango':
                    // Durango has no Cancel button, we only get here by pressing Backspace
                    $loadSession = false;

                    $cciResultArray = DurangoObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'VCS':
                    $loadSession = false;

                    $cciResultArray = VCSObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'TCON':
                    $loadSession = false;

                    $cciResultArray = TconObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'Intesa':
                    $loadSession = false;

                    $cciResultArray = IntesaObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;

                case 'SIS':
                    $loadSession = false;
                    $cciResultArray = SISObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'REDSYS':
                    $loadSession = false;
                    $cciResultArray = REDSYSObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'BIM':
                    // BIM has a Cancel button, but we only get here by pressing Backspace
                    $loadSession = false;

                    $cciResultArray = MillenniumBIMObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'DOKU':
                    // session will be loaded at this point
                    $loadSession = false;
                    $cciResultArray = DokuObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'SanKyu':
                    $loadSession = false;
                    $cciResultArray = SanKyuObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                 case 'GMO':
                    $loadSession = false;
                    $cciResultArray = GMOObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
					$updateCCILog = false;
					$logCCIEntryForSameTransactionID = true;
                    break;
                case 'AudiBank':
                    // AudiBank has a Cancel button, but we only get here by pressing Backspace
                    $loadSession = false;
                    $cciResultArray = AudiBankObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'DECIDIR':
                    // DECIDIR has a Cancel button, but we only get here by pressing Backspace
                    $loadSession = false;
                    $cciResultArray = DecidirObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'ACCOSAPG':
                    // AccosaPG has a Cancel button, but we only get here by pressing Backspace
                    $loadSession = false;
                    $cciResultArray = AccosaPGObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'PAYFORT':
                     // PayFort returns the session in the POST which is automatically re-loaded by the main entry point code
                    $loadSession = false;

                    $cciResultArray = PayFortObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'UniCredit':
                    $loadSession = false;

                    $cciResultArray = UniCreditObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'Computop':
                    $loadSession = false;

                    $cciResultArray = ComputopObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
                case 'PayFast':
                    $loadSession = false;
                    $cciResultArray = PayFastObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;

				case 'Realex':
                    $loadSession = false;

                    $cciResultArray = RealexObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;

                case 'PayBox':
                    $loadSession = false;

                    $cciResultArray = PayBoxObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;

                case 'CNPKZ':
                    $loadSession = false;
                    $cciResultArray = CNPKZObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;
				case 'cPay':
                    $loadSession = false;
                    $cciResultArray = CPayObj::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = true;
					break;
                case 'UNLIMIT':
                    $loadSession = false;
                    $cciResultArray = Unlimit::cancel();
                    $sessionRef = $cciResultArray['ref'];
                    $updateCCILog = false;
                    break;

 				default:
					// sanity check that we have an instance
					if(self::$loadedGateway !== null)
					{
						$gateway = self::$loadedGateway;
						// this returns the array returned by paymentgateway::cancel
						$cciResultArray = $gateway->cancel();
						$sessionRef = $cciResultArray['ref'];
						$loadSession = $gateway->getLoadSession();
						$updateCCILog = $gateway->getCCiUpdate();
						$logCCIEntryForSameTransactionID = $gateway->getStatusForLoggingCCIEntryForSameTransactionID();
					}
					break;
            }

            $cciResultArray['type'] = $gSession['order']['ccitype'];
        }
        elseif ($paymentMethod == 'PAYPAL')
        {
            self::loadGateway($paymentMethod, '');
            $cciResultArray = PayPalObj::cancel();
            $cciResultArray['type'] = 'PAYPAL';

            // paypal returns the session in the url which is automatically re-loaded by the main entry point code
            $loadSession = false;
            $updateCCILog = false;

            // reset the logid / transactionid values
            $gSession['order']['ccilogid'] = '';
            $gSession['order']['ccitransactionid'] = '';
            DatabaseObj::updateSession();
        }

        // we may need to load the session as there was no reference to load it from when the payment service
        // initiated the callback
        if ($sessionRef > 0)
        {
            $_POST['ref'] = $sessionRef;

            if ($loadSession)
            {
                $gSession = DatabaseObj::getSessionData($sessionRef);
            }

            if ($updateCCILog)
            {
                if (($gSession['order']['ccitransactionid'] != $cciResultArray['transactionid']) || ($logCCIEntryForSameTransactionID))
                {
                    $logResultArray = self::logCCIResult($sessionRef, $gSession['userid'], 'CANCEL', $cciResultArray);
                    $gSession['order']['ccilogid'] = $logResultArray['id'];
                    $gSession['order']['ccitransactionid'] = $cciResultArray['transactionid'];
                }
            }

            DatabaseObj::updateSession();
        }
        else
        {
            $logResultArray = self::logCCIResult(-1, 0, 'CANCEL', $cciResultArray);
        }

        return $cciResultArray;
    }

    static function logCCIResult($pSessionRef, $pUserID, $pMode, $pCCIResultArray)
    {
        $result = '';
        $resultParam = '';
        $recordID = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('INSERT INTO `CCILOG` (`id`, `datecreated`, `sessionid`, `orderid`, `userid`, `parentlogid`,
                `type`, `mode`, `amount`, `charges`, `authorised`, `transactionid`, `responsecode`,
                `responsedescription`, `authorisationid`, `bankresponsecode`, `cardnumber`, `cvvflag`,
                `cvvresponsecode`, `paymentcertificate`, `paymentdate`, `paymenttime`, `paymentmeans`,
                `addressstatus`, `postcodestatus`, `payeremail`, `payerid`, `payerstatus`, `business`,
                `receiveremail`, `receiverid`, `pendingreason`, `transactiontype`, `settleamount`,
                `threedsecurestatus`, `cavvresponsecode`, `charityflag`, `currencycode`, `webbrandcode`, `formattedpaymentdate`,
                `formattedtransactionid`, `formattedauthorisationid`, `formattedcardnumber`, `formattedamount`, `formattedcharges`)
                VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
            {
                if ($stmt->bind_param('iiii' . 'ssssiss' . 'sssss' . 'sssss' . 'ssssss' . 'ssssss' . 'sssss' . 'sssdd', $pSessionRef, $pCCIResultArray['orderid'], $pUserID, $pCCIResultArray['parentlogid'],
                    $pCCIResultArray['type'], $pMode, $pCCIResultArray['amount'], $pCCIResultArray['charges'], $pCCIResultArray['authorisedstatus'], $pCCIResultArray['transactionid'], $pCCIResultArray['responsecode'],
                    $pCCIResultArray['responsedescription'], $pCCIResultArray['authorisationid'], $pCCIResultArray['bankresponsecode'], $pCCIResultArray['cardnumber'], $pCCIResultArray['cvvflag'],
                    $pCCIResultArray['cvvresponsecode'], $pCCIResultArray['paymentcertificate'], $pCCIResultArray['paymentdate'], $pCCIResultArray['paymenttime'], $pCCIResultArray['paymentmeans'],
                    $pCCIResultArray['addressstatus'], $pCCIResultArray['postcodestatus'], $pCCIResultArray['payeremail'], $pCCIResultArray['payerid'], $pCCIResultArray['payerstatus'], $pCCIResultArray['business'],
                    $pCCIResultArray['receiveremail'], $pCCIResultArray['receiverid'], $pCCIResultArray['pendingreason'], $pCCIResultArray['transactiontype'], $pCCIResultArray['settleamount'],
                    $pCCIResultArray['threedsecurestatus'], $pCCIResultArray['cavvresponsecode'], $pCCIResultArray['charityflag'], $pCCIResultArray['currencycode'], $pCCIResultArray['webbrandcode'],
                    $pCCIResultArray['formattedpaymentdate'],
                    $pCCIResultArray['formattedtransactionid'], $pCCIResultArray['formattedauthorisationid'], $pCCIResultArray['formattedcardnumber'], $pCCIResultArray['formattedamount'], $pCCIResultArray['formattedcharges']))
                {
                    if ($stmt->execute())
                    {
                        $recordID = $dbObj->insert_id;
                    }
                    else
                    {
                        // could not execute statement
                        $result = 'str_DatabaseError';
                        $resultParam = 'logResult execute ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not bind parameters
                    $result = 'str_DatabaseError';
                    $resultParam = 'logResult bind ' . $dbObj->error;
                }
                $stmt->free_result();
            }
            else
            {
                // could not prepare statement
                $result = 'str_DatabaseError';
                $resultParam = 'logResult prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'logResult connect ' . $dbObj->error;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $recordID;

        return $resultArray;
    }

    static function readCCIConfigFile($pConfigFilePath, $pCurrencyCode, $pWebBrandCode, $pPaymentMethodCode = '')
    {
        // read a CCI config file,
        // select file based on currency code and web brand code

        // file name can be <integration>_<webbrand>_<currency>.conf
        // E.g. kasikorn_MyBrand.conf
        //      kasikorn_MyBrand_USD.conf
        //      kasikorn_USD.conf

        // only attach <webbrand> if it has been configured in that brand
        // (unless PAYPAL, where we always use the brand if the file exists)
        // E.g. Constants[integration]:kasikorn
        //      Branding[integration]: DEFAULT
        //      kasikorn.conf
        // E.g. Constants[integration]:DIBS
        //      Branding[integration]: Kasikorn
        //      kasikorn_<webbrand>.conf

        if ($pPaymentMethodCode == 'PAYPAL')
        {
                $branding = '_' . $pWebBrandCode;   // use brand code if possible
        }
        else
        {
            // determine if we are to insert branding code
            $brandingArray = DatabaseObj::getBrandingFromCode($pWebBrandCode);

            switch ($brandingArray['paymentintegration'])
            {
                case 'DEFAULT':
                    $branding = ''; // brand not included in filename
                    break;
                case 'NONE':
                    $branding = ''; // blank
                    break;
                default:
                    $branding = '_' . $pWebBrandCode;   // use brand code
            }
        }

        // determine filename to use

        // extract path and extension
        $pos = strrpos($pConfigFilePath, '.');
        $ext = substr($pConfigFilePath, $pos);
        $path = substr($pConfigFilePath, 0, $pos);
        $currency = '_' . $pCurrencyCode;

        // try in this order
        //      <integration>_<webbrand>_<currency>.conf
        //      <integration>_<webbrand>.conf
        //      <integration>_<currency>.conf
        //      <integration>.conf

        if (file_exists($path . $branding . $currency . $ext))
        {
            $path .= $branding . $currency . $ext;
        }
        else if (file_exists($path . $branding . $ext))
        {
            $path .= $branding . $ext;
        }
        else if (file_exists($path . $currency . $ext))
        {
            $path .= $currency . $ext;
        }
        else
        {
            $path = $pConfigFilePath; // if all else fails
        }

        $resultArray = UtilsObj::readConfigFile($path);

        return $resultArray;
    }

    static function checkSSL()
    {
        return ((isset($_SERVER['HTTPS']) && (! empty($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] !== 'off')) || (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == 443)));
    }

    static function logPaymentGatewayData($pPaymentConfig, $pServerTime, $pError = '', $pExtraData = array())
    {
        $logFilePath = $pPaymentConfig['LOGFILEPATH'];
        $logOutput = $pPaymentConfig['LOGOUTPUT'];

        if (($logOutput == 1) && ($logFilePath != ''))
        {
            // Check if $logFilePath contains any slashes to distinguish if $logFilePath is a path to a file or just a file name
            if (! preg_match('/[\\\\\/]/', $logFilePath) && ! is_file($logFilePath))
            {
                $path = __FILE__;
                $dir = dirname($path);
                $separator = substr($path, strlen($dir), 1);

                $path = $dir . $separator . '..' . $separator . '..' . $separator . 'logs';
                $path = realpath($path);
                $path .= $separator . $logFilePath;
            }
            else
            {
                $path = $logFilePath;
            }

            $fp = fopen($path, 'a');

            if ($fp)
            {
                fwrite($fp, 'DateTime: ' . $pServerTime . "\n");

                if(is_array($pError))
                {
                        fwrite($fp, 'Error: ' . print_r($pError, TRUE));
                }
                else
                {
                    if ($pError != '')
                    {
                        fwrite($fp, 'Error: ' . $pError . "\n");
                    }
                }
                fwrite($fp, 'GET::' . print_r($_GET,TRUE));
                fwrite($fp, 'POST::' . print_r($_POST,TRUE));
                fwrite($fp, 'Extra datas::' . print_r($pExtraData,TRUE));
                fwrite($fp, "---------------------------------------------------\n");
                fclose($fp);
            }
        }
    }


/**
* Gets the last entry in the CCI log table for a given session reference.
*
* Also returns the order number if present.
* If there is no entry, an empty array will be returned.
*
* @since Version 2.5.3
* @author Steffen Haugk
* @return array
* 'id' field is -1 if there was no entry.
*/
    static function getCciLogEntry($pSessionRef)
    {
        $resultArray = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $sql = 'SELECT cl.*, oh.ordernumber as ordernumber
                    FROM ccilog cl
                    LEFT JOIN orderheader oh ON (oh.id = cl.orderid)
                    WHERE cl.sessionid = ? ORDER BY cl.datecreated DESC';

            if ($stmt = $dbObj->prepare($sql))
            {
                if ($stmt->bind_param('s', $pSessionRef))
                {
                    if ($stmt->execute())
                    {
                        DatabaseObj::stmt_bind_assoc($stmt, $row);
                        if ($stmt->fetch())
                        {
                            foreach ($row as $key=>$value)
                            {
                                $resultArray[$key] = $value;
                            }
                        }

                    }
                }
                $stmt->free_result();
                $stmt->close();
            }
            $dbObj->close();
        }

        return $resultArray;
    }

	static function getCciLogEntryFromTransactionID($pTransactionID)
    {
        $resultArray = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $sql = 'SELECT cl.*, oh.ordernumber as ordernumber
                    FROM ccilog cl
                    LEFT JOIN orderheader oh ON (oh.id = cl.orderid)
                    WHERE cl.transactionid = ? ORDER BY cl.datecreated DESC';

            if ($stmt = $dbObj->prepare($sql))
            {
                if ($stmt->bind_param('s', $pTransactionID))
                {
                    if ($stmt->execute())
                    {
                        DatabaseObj::stmt_bind_assoc($stmt, $row);
                        if ($stmt->fetch())
                        {
                            foreach ($row as $key=>$value)
                            {
                                $resultArray[$key] = $value;
                            }
                        }

                    }
                }
                $stmt->free_result();
                $stmt->close();
            }
            $dbObj->close();
        }

        return $resultArray;
    }


/**
* Gets the type of card based on partial card number.
*
* @since Version 2.5.3
* @author Steffen Haugk
* @return string
*/
    static function getCardType($pCardNumber)
    {
        $card4 = substr($pCardNumber, 0, 4);
        $card3 = substr($pCardNumber, 0, 3);
        $card2 = substr($pCardNumber, 0, 2);
        $card1 = substr($pCardNumber, 0, 1);
        $type ='';

        switch ($card4)
        {
            case '6334':
            case '6767':
                $type = 'Solo';
                break;
            case '2014':
            case '2149':
                $type = 'Diners Club';
                break;
            case '6304':
            case '6706':
            case '6771':
            case '6709':
                $type = 'Laser';
                break;
            case '5018':
            case '5020':
            case '5038':
            case '6304':
            case '6759':
            case '6761':
            case '6763':
                $type = 'Maestro';
                break;
            case '4903':
            case '4905':
            case '4911':
            case '4936':
            case '6331':
            case '6333':
            case '6759':
                $type = 'Switch';
                break;
            case '4026':
            case '4508':
            case '4844':
            case '4913':
            case '4917':
                $type = 'Visa Electron';
                break;
            case '2131':
            case '1800':
                $type = 'JCB';
                break;
            case '5610':
                $type = 'Bankcard';
                break;
            default:
                switch ($card3)
                {
                    case '560':
                        $type = 'Bankcard';
                        break;
                    case '417':
                        $type = 'Visa Electron';
                        break;
                    case '564':
                        $type = 'Switch';
                        break;
                    case '601':
                    case '622':
                        $type = 'Discover Card';
                        break;
                    case '637':
                    case '638':
                    case '639':
                        $type = 'InstaPayment';
                        break;
                    default:
                        switch ($card2)
                        {
                            case '64':
                            case '65':
                                $type = 'Discover Card';
                                break;
                            case '62':
                                $type = 'China UnionPay';
                                break;
                            case '35':
                                $type = 'JCB';
                                break;
                            case '34':
                            case '37':
                                $type = 'AMEX';
                                break;
                            case '30':
                            case '36':
                            case '38':
                                $type = 'Diners Club';
                                break;
                            case '51':
                            case '52':
                            case '53':
                            case '54':
                            case '55':
                                $type = 'MASTERCARD';
                                break;
                            default:
                                switch ($card1)
                                {
                                    case '4':
                                        $type = 'VISA';
                                        break;
                                    default:
                                        $type = 'CC';
                                }
                        }
                }
        }

        return $type;
    }

	/*
	 * get additional payment information, such as bank details from the payment gateway
	 * for example, this can be returned as a string to display in a template
	 *
	 * @param $pParamArray array an array of values to pass to the payment gateway class
	 *
	 * @return string
	 */
	static function ccAdditionalPaymentInfo($pParamArray)
	{
		global $gSession;

		$returnData = '';

		if (array_key_exists('pm', $_GET))
        {
            $paymentMethod = $_GET['pm'];
        }
        else
        {
            $paymentMethod = 'CARD';
        }

		// currently only used for PayPal Plus, CARD is for posible future use
		if ($paymentMethod == 'CARD')
		{
			self::loadGateway($paymentMethod, $gSession['order']['ccitype']);

			// return an empty string
            switch ($gSession['order']['ccitype'])
            {
				default:
				{
					$returnData = '';
				}
			}
		}
		elseif ($paymentMethod == 'PAYPAL')
        {
			self::loadGateway($paymentMethod, '');

			$returnData = PayPalObj::getAdditionalPaymentInfo($pParamArray);
        }
        elseif ($paymentMethod == 'KLARNA')
        {
			$returnData = '';
		}

		return $returnData;
	}

	static function ccInitPaymentGatewayPaymentOptions($pPaymentMethod)
	{
		global $gSession;

		$returnData = array();

		// currently only used for PayPal Plus, CARD is for posible future use
		if ($pPaymentMethod == 'CARD')
		{
			self::loadGateway($pPaymentMethod, $gSession['order']['ccitype']);

			switch($gSession['order']['ccitype'])
			{
				default:
				{
					$returnData = array();
					break;
				}
			};
		}
		else if ($pPaymentMethod == 'PAYPAL')
		{
			$paypalConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayPal.conf', '', $gSession['webbrandcode']);
			// default to PayPal Express if integration type is not configured
			$PayPalIntegrationType = UtilsObj::getArrayParam($paypalConfig, 'PAYPALINTEGRATIONTYPE', 'express');

			if (strtolower($PayPalIntegrationType) == 'plus')
			{
				require_once('../Order/PaymentIntegration/PayPalPlus.php');
			}
			else
			{
				require_once('../Order/PaymentIntegration/PayPal.php');
			}

			self::loadGateway($pPaymentMethod, '');

			$returnData = PayPalObj::paymentOptionsCallback($pPaymentMethod);
        }
        else if ($pPaymentMethod == 'KLARNA')
		{
			$returnData = array();
		}

		return $returnData;
    }
    
    static function createPaymentGatewayInstance($pGSession, $pCCIType)
    {
        $gateway = null;
        $gatewayMap = include(__DIR__ . '/PaymentGatewayMap.php');
        
        //Sanity check to make sure we dont try to load a gateway that doesnt exist
        if(array_key_exists($pCCIType, $gatewayMap))
        {
            require_once($gatewayMap[$pCCIType]);
            $config = PaymentIntegrationObj::readCCIConfigFile('../config/' . $pCCIType . '.conf', '', $pGSession['webbrandcode']);
            $gateway = new $pCCIType($config, $pGSession, $_GET, $_POST);
        }

        return $gateway;
	}

    /**
     * Temporary function.
     * Modified version of createPaymentGatewayInstance to pass $pGSession by reference. 
     * WebPay and MakeCommerce need to be able to update the session.
     */
    static function createPaymentGatewayInstanceReferenced(&$pGSession, $pCCIType)
    {
        $gateway = null;
        $gatewayMap = include(__DIR__ . '/PaymentGatewayMap.php');
        
        //Sanity check to make sure we dont try to load a gateway that doesnt exist
        if(array_key_exists($pCCIType, $gatewayMap))
        {
            require_once($gatewayMap[$pCCIType]);
            $config = PaymentIntegrationObj::readCCIConfigFile('../config/' . $pCCIType . '.conf', '', $pGSession['webbrandcode']);
            $gateway = new $pCCIType($config, $pGSession, $_GET, $_POST);
        }

        return $gateway;
	}

	/**
	 * Adds CSP details for small screen so the gateway specific csp rules are added up front.
	 * 
	 * @param string $pGatewayCode Gateway code we will be using.
	 * @returns void
	 */
	public static function addSmallScreenGatewayCSP($pGatewayCode = '')
	{
		$gSession = UtilsObj::getGlobalValue('gSession', []);

		$objectName = $pGatewayCode;
		$isStatic = true;

		switch ($pGatewayCode)
		{
			case 'CYBERPLUS':
				require_once('../Order/PaymentIntegration/CCICyberPlus.php');
				$objectName = 'CCICyberPlus';
				break;
			case 'EasyPay2':
				require_once('../Order/PaymentIntegration/EasyPay2.php');
				break;
			case 'ePDQ':
				require_once('../Order/PaymentIntegration/ePDQ.php');
				break;
			case 'PayEase':
				require_once('../Order/PaymentIntegration/PayEase.php');
				break;
			case 'SagePay':
				$sagePayConfig = PaymentIntegrationObj::readCCIConfigFile('../config/SagePay.conf', '', $gSession['webbrandcode']);

				// default to sagepay form if integration type is not configured
				$sagePayIntegrationType = UtilsObj::getArrayParam($sagePayConfig, 'INTEGRATIONTYPE', 'form');

				if (strtolower($sagePayIntegrationType) == 'server')
				{
					require_once('../Order/PaymentIntegration/SagePayServer.php');
					$objectName = 'SagePayServer';
				}
				else
				{
					require_once('../Order/PaymentIntegration/SagePay.php');
				}
				break;
			case 'Kasikorn':
				require_once('../Order/PaymentIntegration/Kasikorn.php');
				break;
			case 'Karandash':
				require_once('../Order/PaymentIntegration/Karandash.php');
				break;
			case 'DIBS':
				require_once('../Order/PaymentIntegration/DIBS.php');
				break;
			case 'mPP':
				require_once('../Order/PaymentIntegration/mPP.php');
				break;
			case 'WorldPay':
				require_once('../Order/PaymentIntegration/WorldPay.php');
				break;
			case 'eSelect':
				require_once('../Order/PaymentIntegration/eSelect.php');
				break;
			case 'PayDollar':
				require_once('../Order/PaymentIntegration/PayDollar.php');
				break;
			case 'NAB':
				require_once('../Order/PaymentIntegration/NAB.php');
				break;
			case 'DirecPay':
			   require_once('../Order/PaymentIntegration/DirecPay.php');
				break;
			case 'IPPayments':
				require_once('../Order/PaymentIntegration/IPPayments.php');
				break;
			case 'CIM':
				require_once('../Order/PaymentIntegration/CIMItalia.php');
				$objectName = 'CIMItalia';
				break;
			case 'Alipay':
				require_once('../Order/PaymentIntegration/Alipay.php');
				break;
			case 'CMCIC':
				require_once('../Order/PaymentIntegration/CMCIC.php');
				break;
			case 'APCO':
				require_once('../Order/PaymentIntegration/APCO.php');
				break;
			case 'PAYONE':
				require_once('../Order/PaymentIntegration/Payone.php');
				break;
			case 'PayTrail':
				require_once('../Order/PaymentIntegration/PayTrail.php');
				break;
			case 'PayU':
			case 'PAYU':
				require_once('../Order/PaymentIntegration/PayU.php');
				break;
			case 'GMO':
				require_once('../Order/PaymentIntegration/GMO.php');
				break;
			case 'PAYGENT':
				require_once('../Order/PaymentIntegration/Paygent.php');
				break;
			case 'PAGOSONLINE':
				require_once('../Order/PaymentIntegration/PagosOnline.php');
				break;
			case 'INILITE':
				require_once('../Order/PaymentIntegration/INILite.php');
				break;
			case 'Intesa':
				require_once('../Order/PaymentIntegration/Intesa.php');
				break;
			case 'IPayPayGate':
				require_once('../Order/PaymentIntegration/IPayPayGate.php');
				break;
			case 'OGONE':
				require_once('../Order/PaymentIntegration/Ogone.php');
				break;
			case 'Durango':
				require_once('../Order/PaymentIntegration/Durango.php');
				break;
			case 'TCON':
				require_once('../Order/PaymentIntegration/TCON.php');
				break;
			case 'VCS':
				require_once('../Order/PaymentIntegration/VCS.php');
				break;
			case 'SIS':
				require_once('../Order/PaymentIntegration/SISVirtualPOS.php');
				$objectName = 'SISVirtualPOS';
				break;
			case 'REDSYS':
				require_once('../Order/PaymentIntegration/redsys.php');
				break;
			case 'BIM':
				require_once('../Order/PaymentIntegration/MillenniumBIM.php');
				$objectName = 'MillenniumBIM';
				break;
			case 'DOKU':
				require_once('../Order/PaymentIntegration/doku.php');
				break;
			case 'ANZ':
				require_once('../Order/PaymentIntegration/Anz.php');
				break;
			case 'SanKyu':
				require_once('../Order/PaymentIntegration/SanKyu.php');
				break;
			case 'AudiBank':
				require_once('../Order/PaymentIntegration/AudiBank.php');
				break;
			case 'DECIDIR':
				require_once('../Order/PaymentIntegration/DECIDIR.php');
				break;
			case 'ACCOSAPG':
				require_once('../Order/PaymentIntegration/AccosaPG.php');
				break;
			case 'PAYFORT':
				require_once('../Order/PaymentIntegration/PayFort.php');
				break;
			case 'UniCredit':
				require_once('../Order/PaymentIntegration/UniCredit.php');
				break;
			case 'Computop':
				require_once('../Order/PaymentIntegration/Computop.php');
				break;
			case 'Cielo':
				require_once('../Order/PaymentIntegration/Cielo.php');
				break;
			case 'PayFast':
				require_once('../Order/PaymentIntegration/PayFast.php');
				break;
			case 'Realex':
				require_once('../Order/PaymentIntegration/Realex.php');
				break;
			case 'PayBox':
				require_once('../Order/PaymentIntegration/PayBox.php');
				break;
			case 'CNPKZ':
				require_once('../Order/PaymentIntegration/CNprocessingKZ.php');
				$objectName = 'CNprocessingKZ';
				break;
			case 'cPay':
				require_once('../Order/PaymentIntegration/cPay.php');
				break;
			default:
				$gatewayMap = include(__DIR__ . '/PaymentGatewayMap.php');

				//Sanity check to make sure we dont try to load a gateway that doesnt exist
				if(array_key_exists($pGatewayCode, $gatewayMap))
				{
					require_once($gatewayMap[$pGatewayCode]);
					$isStatic = false;
				}
				break;
		}

		if ($isStatic)
		{
			$objectName .= 'Obj';
		}

		if (method_exists($objectName, 'addCSPDetails'))
		{
			if ($isStatic)
			{
				$cspBuilder = ControlCentreCSP::getInstance(UtilsObj::getGlobalValue('ac_config'));
				// Static call to addCSPDetails, old gateways are static.
				call_user_func_array([$objectName, 'addCSPDetails'], [$cspBuilder]);
			}
			else
			{
				// Temp array to pass to the constructor for the new gateways, this object is not kept
				$tmpArray = [];
				// Get the config for the gateway.
				$config = PaymentIntegrationObj::readCCIConfigFile('../config/' . $pGatewayCode . '.conf', '', $gSession['webbrandcode']);
				// Instance the gateway object and call addCSPDetails.
				$instance = new $objectName($config, $tmpArray, $tmpArray, $tmpArray);
				$instance->addCSPDetails();
				unset ($instance);
				unset ($tmpArray);
			}
		}
	}
}

?>
