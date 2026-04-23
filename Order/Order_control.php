<?php

use Security\RequestValidationTrait;

require_once(__DIR__.'/../Welcome/Welcome_control.php');
require_once(__DIR__.'/../Order/Order_model.php');
require_once(__DIR__.'/../Order/Order_view.php');

class Order_control
{
	use RequestValidationTrait;

    static function initialize()
    {
        global $ac_config;
        global $gAuthSession;
        global $gConstants;
        global $gSession;

        $info = '';
        $info2 = '';
        $ssoResultArray = array();

        if (isset($gSession['browserlanguagecode']))
        {
			$_COOKIE['maweblocale'] = $gSession['browserlanguagecode'];
			$gConstants['initlang'] = $gSession['browserlanguagecode'];
        }

		// this is the first time arriving at order.initialise so we don't have a cookie but have come from online
		// all other times we authenticate using the cookie
		if ($gSession['authenticatecookie'] == 0)
		{
			$gAuthSession = false;
			$gSession['authenticatecookie'] = 1;
		}

		self::orderCSP();

        if ((isset($ac_config['FRAMEPARENTURL'])) && ($ac_config['FRAMEPARENTURL'] != ''))
        {
            Order_view::initialize();
        }
        else
        {
            // perform a single sign-on check
            if ($gSession['ref'] > 0)
            {
                // if this is high level this check has already been done so there is no need to do it again
                if ($gSession['order']['basketapiworkflowtype'] != TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
                {
                    $reason = TPX_USER_AUTH_REASON_WEB_INIT;

                    if ($gSession['userid'] <= 0)
                    {
                        $reason = TPX_USER_AUTH_REASON_WEB_CART_INIT;
                    }

                    $ssoResultArray = AuthenticateObj::authenticateLogin($reason, $gSession['ref'], true, UtilsObj::getBrowserLocale(),
                        $gSession['webbrandcode'], $gSession['licensekeydata']['groupcode'], '', '', TPX_PASSWORDFORMAT_CLEARTEXT, '', true, true,
                        true, $gSession['userdata']['ssotoken'], $gSession['userdata']['ssoprivatedata'], array());

                    $ssoResult = $ssoResultArray['result'];
                }
                else
                {
                    $ssoResult = '';
                }
            }
            else
            {
                $ssoResult = '';
            }

            // process the single sign-on result
            switch ($ssoResult)
            {
                case '':
                {
                    // no error occurred so just initialize which will log the user in if single sign-on was successful
                    self::initialize2();

                    break;
                }
                case 'SSOREDIRECT':
                {
                    // redirect to grab the single sign-on token
                    AuthenticateObj::ssoRedirect($ssoResultArray);

                    break;
                }
                default:
                {
                    // a single sign-on error occurred
                    $info = $ssoResultArray['result'];
                    $info2 = $ssoResultArray['resultparam'];

                    if (substr($info, 0, 4) == 'str_')
                    {
                        $info = SmartyObj::getParamValue('Login', $info);
                    }

                    if (substr($info2, 0, 4) == 'str_')
                    {
                        $info2 = SmartyObj::getParamValue('Login', $info2);
                    }

                    Welcome_view::displaySSOError($info, $info2);

                    break;
                }
            }
        }
    }

    static function initialize2()
    {
        global $gSession;

		UtilsObj::setSessionDeviceData();

        $resultArray = Order_model::initialize();

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            if ($resultArray['result'] == '')
            {
				//force address update depending on if customer can modify accounts
                if (($gSession['useraddressupdated'] == 0 || $gSession['useraddressupdated'] == 2) && ($gSession['promptforaddress'] == true))
                {
                    if ($gSession['useraddressupdated'] == 0)
                    {
                        $resultArray['updateaddressmode'] = TPX_UPDATEADDRESSMODE_RESPECT;
                    }
                    elseif ($gSession['useraddressupdated'] == 2)
                    {
                        $resultArray['updateaddressmode'] = TPX_UPDATEADDRESSMODE_FULL;
                    }

                    Order_view::changeAddressDisplay($resultArray, 'shipping', true, true);
                }
                else
                {
                    Order_view::displayJobTicket($resultArray, $resultArray['stage'], true, true, false, false, $resultArray['stage']);
                }
            }
            else
            {
                Order_view::displayError($resultArray);
            }
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, $resultArray['cancreateaccounts'], 'str_ErrorSessionExpired');
        }
        elseif (AuthenticateObj::WebSessionActive() == -1)
        {
             $canCreateAccounts = $resultArray['cancreateaccounts'];
             $foreceHighLevelBasketUser = 0;

             // if a user checks out via high level api and there is no valid user session we must force the user to log back in at the shopping cart using the same
			 // user id that belongs to the basket. In order for us to capture this when at the shopping cart side we must tag the hlfbu paramter
			 // onto the shopping cart URL. We must force a user to signin and not let them register for an account.
			 // hlfbu - High Level Force Basket User.
             if (isset($_GET['hlfbu']))
             {
             	$canCreateAccounts = 0;
             	$foreceHighLevelBasketUser = 1;
             }

            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_NOT_STARTED, $canCreateAccounts, '', $foreceHighLevelBasketUser);
        }
        else
        {
            Welcome_control::processLogoutRedirect(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
        }
    }

    static function duplicate()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            Order_model::storeOrderMetaData($_POST['stage']);

            // re-check the voucher to make sure its usage status hasn't changed
            Order_model::checkVoucher();

            $resultArray = Order_model::duplicate($_POST['orderlineid']);
            if ($resultArray['result'] == '')
            {
                Order_view::displayJobTicket($resultArray, 'qty', true, true, false, false, 'qty');
            }
            else
            {
                Order_view::displayError($resultArray);
            }
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function remove()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            Order_model::storeOrderMetaData($_POST['stage']);

            // re-check the voucher to make sure its usage status hasn't changed
            Order_model::checkVoucher();

            $resultArray = Order_model::remove($_POST['orderlineid']);
            if ($resultArray['result'] == '')
            {
                Order_view::displayJobTicket($resultArray, 'qty', true, true, false, false, 'qty');
            }
            else
            {
                Order_view::displayError($resultArray);
            }
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function revive()
    {
        $resultArray = Order_model::revive(false);

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            if ($resultArray['result'] == '')
            {
                Order_view::displayJobTicket($resultArray, $resultArray['stage'], true, true, false, false, $resultArray['stage']);
            }
            else
            {
                Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
            }
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function offlineOrderRevive()
    {
        global $gSession;
        global $gAuthSession;

        // switch off the session authentication as there may be no cookie for the session
        $gAuthSession = false;

        $resultArray = Order_model::revive(true);

        $isOfflineOrder = $gSession['order']['isofflineorder'];

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            if ($gSession['userid'] > 0)
            {
                if ($resultArray['result'] == '')
                {
                    Order_view::displayJobTicket($resultArray, 'qty', false, true, false, false, 'qty');
                }
                else
                {
                    Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, $isOfflineOrder, 'str_ErrorSessionExpired');
                }
            }
            else
            {
                Welcome_view::processLogout('', $gSession['ref'], $isOfflineOrder, '');
            }
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, $isOfflineOrder, '');
        }
    }

    static function orderContinue()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            Order_model::storeOrderMetaData($_POST['stage']);

            $resultArray = Order_model::orderContinue();
            Order_view::orderContinue($resultArray);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
			// empty the ref is the session is a reorder but it has been cancelled
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function orderBack()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            Order_model::storeOrderMetaData($_POST['stage']);

			// re-check the voucher to make sure its usage status hasn't changed
            Order_model::checkVoucher();

			$resultArray = Order_model::orderBack();
			Order_view::orderContinue($resultArray);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function cancel()
    {
        $mainWebSiteURL = Order_model::cancel();
        Order_view::displayCancellation($mainWebSiteURL);
    }

    static function changeComponentCancel()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            // re-check the voucher to make sure its usage status hasn't changed
            Order_model::checkVoucher();

            $resultArray = Order_model::orderRefresh();
            Order_view::orderContinue($resultArray);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function changeComponent()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            // re-check the voucher to make sure its usage status hasn't changed
            Order_model::checkVoucher();

            Order_model::changeComponent();

            $resultArray = Order_model::orderRefresh();
            Order_view::orderContinue($resultArray);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function changeComponentDisplay()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            Order_model::storeOrderMetaData($_POST['stage']);

            $resultArray = Order_model::changeComponentDisplay($_POST['section']);
            Order_view::changeComponentDisplay($resultArray);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function selectStoreDisplay()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            Order_model::storeOrderMetaData($_GET['stage']);

            // re-check the voucher to make sure its usage status hasn't changed
            Order_model::checkVoucher();
            $resultArray = Order_model::selectStoreDisplay();

            Order_view::selectStoreDisplay($resultArray);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function selectStore()
    {
        global $gSession;
        global $gConstants;

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            // re-check the voucher to make sure its usage status hasn't changed
            Order_model::checkVoucher();

            Order_model::selectStore();

            if ($gConstants['taxaddress'] == TPX_TAX_CALCULATION_BY_SHIPPING_ADDRESS)
            {
                Order_model::updateOrderTaxRate();
            }

            Order_model::updateOrderShippingRate();

            // get store address
            $shippingMethodCode = $gSession['shipping'][0]['shippingmethodcode'];
            $formattedStoreAddress = UtilsAddressObj::formatAddress($gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress'],
                            'store', "\r");
            $formattedStoreAddress = UtilsObj::encodeString($formattedStoreAddress, false);
            $formattedStoreAddress = str_replace("\r", "<br>", $formattedStoreAddress);

            echo json_encode(array('storeid' => $gSession['shipping'][0]['storeid'], 'storeaddress' => $formattedStoreAddress));

            return;
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function changeShippingAddressDisplay()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
        	$resultArray = Order_model::changeShippingAddressDisplay();
            $resultArray['shippingcfscontact'] =  UtilsObj::getPOSTParam('shippingcfscontact',0);

            Order_view::changeAddressDisplay($resultArray, 'shipping', true, true);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function changeShippingAddress()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            $resultArray = Order_model::changeShippingAddress();
            Order_view::orderContinue($resultArray);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function changeShippingMethod()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            Order_model::changeShippingMethod();

            $resultArray = Order_model::orderRefresh();

            if ($resultArray['forcechangeaddressdisplay'])
            {
                self::changeShippingAddressDisplay();
            }
            else
            {
                Order_view::orderContinue($resultArray);
            }
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function changeBillingAddressDisplay()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            $resultArray = Order_model::changeBillingAddressDisplay();
            Order_view::changeAddressDisplay($resultArray, 'billing', false, false);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function changeBillingAddress()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            $resultArray = Order_model::changeBillingAddress();
            Order_view::orderContinue($resultArray);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function changeAddressCancel()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            // re-check the voucher to make sure its usage status hasn't changed
            Order_model::checkVoucher();

            $resultArray = Order_model::orderRefresh();
            Order_view::orderContinue($resultArray);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function copyShippingAddress()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            $resultArray = Order_model::copyShippingAddress();
            Order_view::orderContinue($resultArray);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

	static function updateAccountDetails()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            $resultArray = Order_model::updateAccountDetails();
            Order_model::updateOrderTaxRate();
            Order_view::orderContinue($resultArray);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function setVoucher()
    {
        global $gSession;

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            Order_model::storeOrderMetaData($_POST['stage']);

            // we must remember the original shipping method before applying the voucher
            //this is because setVoucher workflow calls updateOrderShippingRate.
            $origShippingMethod = $gSession['shipping'][0]['shippingmethodcode'];

            Order_model::setVoucher();

            $resultArray = Order_model::orderRefresh();

            if ($_POST['stage'] != 'payment')
            {
                if ((($gSession['order']['voucherdiscountsection'] == 'SHIPPING') || ($gSession['order']['voucherdiscountsection'] == 'TOTAL')) &&
                        ($gSession['order']['voucherstatus'] == 'str_LabelOrderVoucherAccepted'))
                {
                    $resultArray['custominit'] = 'setTimeout("alert(\"' . SmartyObj::getParamValue('Order', 'str_OrderDiscountConfirmation') . '\")", 1000);';
                }
            }

            // update the shipping methods as the order total may have changed causing the shipping method not to be available

            Order_model::updateOrderShippingRate();

            if ($origShippingMethod != $gSession['shipping'][0]['shippingmethodcode'])
            {
                // the original shipping method is not available so we must go back to the shipping methods list
                $_POST['stage'] = 'qty';
                $resultArray = Order_model::orderContinue();
            }

            Order_view::orderContinue($resultArray);

        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function setGiftCard()
    {
        global $gSession;

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            Order_model::storeOrderMetaData($_POST['stage']);
            Order_model::setGiftCard();
            $resultArray = Order_model::orderRefresh();
            Order_view::orderContinue($resultArray);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function addGiftCard()
    {

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            $canUseAccount = Order_model::addGiftCard();

            Order_view::addGiftCard($canUseAccount);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function deleteGiftCard()
    {

        if (AuthenticateObj::WebSessionActive() == 1)
        {

            $canUseAccount = Order_model::deleteGiftCard();

            Order_view::deleteGiftCard($canUseAccount);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function refresh()
    {

        if (AuthenticateObj::WebSessionActive() == 1)
        {
        	self::orderCSP();
            $resultArray = Order_model::orderRefresh();
            Order_view::orderContinue($resultArray);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function ccAutomaticCallback()
    {
        // process an automatic callback from the payment service
        // there is no need to return any output as it will not go anywhere
        // include the payment integrations module
        require_once('../Order/PaymentIntegration/PaymentIntegration.php');

        $resultArray = PaymentIntegrationObj::ccAutomaticCallback();

		if (array_key_exists('acknowledgement', $resultArray))
		{
			Order_view::sendAcknowledgement($resultArray['acknowledgement']);
		}

        if ($resultArray['showerror'] == true)
        {
            Order_view::showCCIError($resultArray);
        }
        else
        {
            if ($resultArray['update'] == false)
            {
                // if the payment transaction wasn't an update to an existing payment transaction determine what to do next

                if ($resultArray['authorised'] == true)
                {
                    Order_model::complete();
                }
                else
                {
                    if ($resultArray['statusforinformationonly'] == false)
                    {
                    	$resultArray = Order_model::ccCancelCallback();
                    }
                }
            }
            else
            {
                if ($resultArray['nextstage'] == 'complete')
                {
                    // mPP's manual callback performs an update and is therefore classed as an automatic callback.
                    // It needs to show the confirmation page.
                    AuthenticateObj::clearSessionCCICookie();
                    Order_view::orderContinue($resultArray);
                }
            }
        }
    }

    static function ccManualCallback()
    {
		global $gSession;

		// process a manual callback from the payment service
        // include the payment integrations module
        require_once('../Order/PaymentIntegration/PaymentIntegration.php');

        $resultArray = PaymentIntegrationObj::ccManualCallback();

        if ($resultArray['showerror'] == true)
        {
            Order_view::showCCIError($resultArray);
        }
        else
        {
            if ($resultArray['authorised'] == true)
            {
                $resultArray['orderdata'] = Order_model::prepareOrderDataForTrackingCodes();
                $resultArray['webbrandonlineredirectionurl'] = '';

                if ($gSession['order']['basketapiworkflowtype'] == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
                {
					$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $gSession['webbrandcode']);
					$brandDataArray = DatabaseObj::getBrandingFromCode($gSession['webbrandcode']);

					if ($brandDataArray['onlinedesignerlogouturl'] != '')
					{
						$redirectionURL = $brandDataArray['onlinedesignerlogouturl'];
					}
					else
					{
						$redirectionURL = $hl_config['REDIRECTIONURL'];
					}

					$resultArray['webbrandonlineredirectionurl'] = UtilsObj::correctPath($redirectionURL, '/', true);
                }

                // if the payment transaction wasn't an update then we must complete the ordering process now (no ipn was received)
                if ($resultArray['update'] == false)
                {
                    Order_model::complete();
                }
                AuthenticateObj::clearSessionCCICookie();

                Order_view::orderContinue($resultArray);
            }
            else
            {
                $resultArray = Order_model::ccCancelCallback();
                AuthenticateObj::clearSessionCCICookie();

				if ($gSession['ismobile'] == true)
				{
					Order_view::displayJobTicket($resultArray, 'payment', true, true, false, false, 'qty');
				}
				else
				{
					// re-check the voucher to make sure its usage status hasn't changed
					Order_model::checkVoucher();
					Order_view::orderContinue($resultArray);
				}
            }
        }
    }

    static function ccCancelCallback()
    {
        global $gSession;

        // process a cancel callback from the payment service
        // include the payment integrations module
        require_once('../Order/PaymentIntegration/PaymentIntegration.php');

        $resultArray = PaymentIntegrationObj::ccCancelCallback();
        if ($resultArray['showerror'] == true)
        {
            Order_view::showCCIError($resultArray);
        }
        else
        {
            $resultArray = Order_model::ccCancelCallback();
            if ($gSession['ismobile'] == true)
            {
                Order_view::displayJobTicket($resultArray, 'payment', true, true, false, false, 'qty');
            }
            else
            {
                // re-check the voucher to make sure its usage status hasn't changed
                Order_model::checkVoucher();
                Order_view::orderContinue($resultArray);
            }
        }
    }

    static function ccResume()
    {
		global $gSession;

		// resume the ordering process after manually displaying an error from the payment service
        $resultArray = Order_model::ccCancelCallback();

		if ($gSession['ismobile'] == true)
		{
			Order_view::displayJobTicket($resultArray, 'payment', true, true, false, false, 'qty');
		}
		else
		{
			// re-check the voucher to make sure its usage status hasn't changed
			Order_model::checkVoucher();
			Order_view::orderContinue($resultArray);
		}

    }

    static function getStoreLocatorLogo()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            $resultArray = Order_model::getStoreLocatorLogo();
            Order_view::getStoreLocatorLogo($resultArray);
        }
        elseif (AuthenticateObj::WebSessionActive() == 0)
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, 'str_ErrorSessionExpired');
        }
        else
        {
            Welcome_control::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED, 0, '');
        }
    }

    static function reorder()
    {
        global $gAuthSession;
        global $gConstants;
		global $gSession;

		// do not reauthenticate if this is the first time triggering the reorder we won't have a cookie at this point
		// all other times we authenticate based on the cookie
		if ($gSession['authenticatecookie'] == 0)
		{
			$gAuthSession = false;
			$gSession['authenticatecookie'] = 1;

			//language for the current page
			$_COOKIE['maweblocale'] = $gSession['browserlanguagecode'];
			$gConstants['initlang'] = $gSession['browserlanguagecode'];
		}

        self::initialize();
    }

	static function initPaymentGatewayPaymentOptions()
	{
		$pm = UtilsObj::getGETParam('pm', 'CARD');

		$resultArray = Order_model::initPaymentGatewayPaymentOptions($pm);

		Order_view::initPaymentGateway($resultArray);
	}

	static function initPayNowOrder()
	{
		$ref = UtilsObj::getPOSTParam('origref', 0);

		$resultArray = Order_model::initPayNowOrder($ref);

		Order_view::initPayNowOrder($resultArray);
    }
    
    /**
     * This function is used to access the initialise function of the gateway
     * at current this is only being used for pagseguro but in future could be used 
     * for any other gateways that require a lightbox or initialising on the payment page
     */

    static function requestPaymentParams()
    {
        global $gSession;

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            $gSession['order']['paymentmethodcode'] = $_POST['paymentmethodcode'];
            $gSession['order']['paymentgatewaycode'] = $_POST['paymentgatewaycode'];
        
            /**
             * Here we need to update the payment method as we have bypassed the 
             * Order continue function which usually handles the update of payment methods
             */
            Order_model::updateOrderPaymentMethod();
            
            DatabaseObj::updateSession();
            
            require_once('../Order/PaymentIntegration/PaymentIntegration.php');
    
            $result = PaymentIntegrationObj::initialize();
    
            echo $result;
        }
        else
        {
            // If we do not have an active session then we must redirect the user.
            // This is to prevent them from trying to pay via a lightbox gateway when an order session has expired or the order has already been paid for.
            // This could be a result of them clicking back in the browser and attempting to pay again.
            $returnArray['result'] = TPX_USER_LOGOUT_REASON_PAYMENT_SESSION_EXPIRED;
            $returnArray['redirecturl'] = Welcome_model::processLogout(TPX_USER_LOGOUT_REASON_PAYMENT_SESSION_EXPIRED);
  
           echo json_encode($returnArray);
        }

    }

	/**
	 * Adds CSP settings for requests made on mobile, we do not reload the page on mobile,
	 * so we need to supply the csp details for the gateway at initialization.
	 *
	 * @param string $pWebBrandCode Web brand code for this request.
	 * @returns void.
	 */
	static function attachSmallScreenCSP($pWebBrandCode)
	{	
		global $gSession;

		$defaultBranding = null;
		$paymentMethodString = '';

		// Get the non-default brand.
		$branding = DatabaseObj::getBrandingFromCode($pWebBrandCode);
		$paymentIntegration = $branding['paymentintegration'];

		// Get payment methods from: user -> lkey -> [brand] -> constants.
		$userAccountArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
		
        // Use customer account settings first.
		if ($userAccountArray['usedefaultpaymentmethods'] === 1)
		{
			$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($gSession['licensekeydata']['groupcode']);

			// Then try licence key.
			if ($licenseKeyArray['usedefaultpaymentmethods'] === 1)
			{
				// Get the default brand settings.
				$defaultBranding = DatabaseObj::getBrandingFromCode('');

				// Check if there is a brand attached to the license key.
				if ($licenseKeyArray['webbrandcode'] === '')
				{
					// Use the default brand settings.
					$paymentMethodString = $defaultBranding['paymentmethods'];
					$paymentIntegration = $defaultBranding['paymentintegration'];
				}
				else
				{
					// Get settings from the brand.
					if (($branding['usedefaultpaymentmethods'] === 1)  || ($branding['paymentintegration'] === 'DEFAULT'))
					{
						// Only update the payment methods if we are using the defaults.
						if ($branding['usedefaultpaymentmethods'] === 1)
						{
							$paymentMethodString = $defaultBranding['paymentmethods'];
						}
                        else
                        {
                            $paymentMethodString = $branding['paymentmethods'];
                        }

						// Only set the payment integration if the brand uses the default integration.
						if ($branding['paymentintegration'] == 'DEFAULT')
						{
							$paymentIntegration = $defaultBranding['paymentintegration'];
						}
					}
					else
					{
						// Use the brand settings.
						$paymentMethodString = $branding['paymentmethods'];
					}
				}
			}
			else
			{
				$paymentMethodString = $licenseKeyArray['paymentmethods'];
			}
		}
		else
		{
			$paymentMethodString = $userAccountArray['paymentmethods'];
		}

		$addCardCSP = true;
		$paymentMethods = explode(',', $paymentMethodString);

		// Check if we have a payment method that requires CSP.
		// Check for paypal first as we should be disabling csp if paypal is active.
		if (in_array('PAYPAL', $paymentMethods))
		{
			require_once '../Order/PaymentIntegration/PaymentIntegration.php';
			// Paypal is a top level payment option so is configured differently
			$paypalConfig = PaymentIntegrationObj::readCCIConfigFile('../config/PayPal.conf', '', $pWebBrandCode);
			$integrationType = UtilsObj::getArrayParam($paypalConfig, 'PAYPALINTEGRATIONTYPE', 'express');

			// Paypal Plus currently has an issue with the payment wall so we disable CSP.
			if (strtolower($integrationType) == 'plus')
			{
				// Set csp to disabled on the global ac_config
				$GLOBALS['ac_config']['CONTENTSECURITYPOLICY'] = 'DISABLED';
				$addCardCSP = false;
			}
        }
        
        if (in_array('KLARNA', $paymentMethods))
        {
            // We are a card payment with a gateway integration.
			require_once '../Order/PaymentIntegration/PaymentIntegration.php';

			// Add any small screen gateway csp details.
			PaymentIntegrationObj::addSmallScreenGatewayCSP('Klarna');
        }

		// Check if we need to add card csp rules as paypal express may be used which does not disable csp.
		if (($addCardCSP) && (in_array('CARD', $paymentMethods)) && ($paymentIntegration != ''))
		{
			// We are a card payment with a gateway integration.
			require_once '../Order/PaymentIntegration/PaymentIntegration.php';

			// Add any small screen gateway csp details.
			PaymentIntegrationObj::addSmallScreenGatewayCSP($paymentIntegration);
		}
	}

	static function orderCSP()
	{
		global $ac_config;
		global $gSession;

		$cspActive = true;

		if (array_key_exists('CONTENTSECURITYPOLICY', $ac_config))
		{
			if ($ac_config['CONTENTSECURITYPOLICY'] === 'DISABLED')
			{
				$cspActive = false;
			}
		}

		/*
		 * If csp is active and we are on mobile we need to pre populate the csp with all details for the gateways
		 * when we call initialize, as this is the only csp policy that the browser will get in full, other pages are injected
		 * via ajax calls.
		 */
		if ($cspActive)
		{
			// Get the device info as the session has not been updated with the mobile status yet.
			$deviceInfo = UtilsDeviceDetection::determineDevice(false);

			// If the device is mobile attach the small screen csp.
			if ($deviceInfo['ismobiledevice'])
			{
				self::attachSmallScreenCSP($gSession['webbrandcode']);
			}
		}
	}
}
?>