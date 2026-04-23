<?php
use Security\ControlCentreCSP;

require_once('../Share/Share_view.php');

class Customer_view
{
    static function initialize()
    {
        global $ac_config;
        global $gSession;

        $smarty = SmartyObj::newSmarty('Customer', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
        $smarty->assign('url', UtilsObj::getBrandedWebUrl() . '?fsaction=Customer.initialize2&ref=' . $_GET['ref']);
        $smarty->displayLocale($ac_config['FRAMEPARENTURL']);
    }

    static function display($pResultArray, $pAjaxCall = false)
    {
        global $gConstants;
        global $gSession;
        global $ac_config;
        
        $isAccountCustomer = false;
        $onlineDesignerURL = '';
        $registerWithEmail = TPX_REGISTER_USING_EMAIL;

        /* generic part */
        $smarty = SmartyObj::newSmarty('Customer', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
        $smarty->assign('session', $gSession['ref']);


        if ($gSession['ismobile'] == true)
        {
            $smarty->assign('issmallscreen', 'true');
            $smarty->assign('header', $smarty->getLocaleTemplate('customer/header_customer_small.tpl', ''));
            $smarty->assign('footer', $smarty->getLocaleTemplate('customer/footer_small.tpl', ''));
        }
        else
        {
			$smarty->assign('issmallscreen', 'false');
            $smarty->assign('header', $smarty->getLocaleTemplate('customer/header_customer_large.tpl', ''));
            $smarty->assign('footer', $smarty->getLocaleTemplate('customer/footer_large.tpl', ''));
            $smarty->assign('information', $smarty->getLocaleTemplate('customer/information.tpl', ''));

            $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('customer/sidebarleft_'.$pResultArray['section'].'.tpl', ''));
            $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('customer/sidebarleft_default.tpl', ''));

			$smarty->assign('userlogin', $gSession['userlogin']);
        }

        // display the user's login if signed in normally
        // if signed in via single sign-on then the login may not mean anything so use the name instead
        if ($gSession['userdata']['ssotoken'] == '')
        {
            $smarty->assign('userdisplayname', $gSession['userlogin']);
        }
        else
        {
            $smarty->assign('userdisplayname', $gSession['username']);
        }


        $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($pResultArray['user']['groupcode']);
        $brandingDefaults = DatabaseObj::getBrandingFromCode('');

        if ($licenseKeyArray['webbrandcode'] == '')
        {
            $smarty->assign('googletagmanagercccode', $brandingDefaults['googletagmanagercccode']);
            
            $onlineDesignerURL = $brandingDefaults['onlinedesignerurl'];
			// if customer set to use default voucher settings and license key is to use default voucher settings use the branding settings
			if (($pResultArray['user']['usedefaultvouchersettings'] === 1) && ($licenseKeyArray['usedefaultvouchersettings'] === 1))
			{
				$smarty->assign('showgiftcardsbalance', $brandingDefaults['allowgiftcards']);
			}
			// if customer is set to just use default voucher settings in customer use the license key settings
			else if ($pResultArray['user']['usedefaultvouchersettings'] === 1)
			{
				$smarty->assign('showgiftcardsbalance', $licenseKeyArray['allowgiftcards']);
			}
			// otherwise use customer voucher settings
			else
			{
				$smarty->assign('showgiftcardsbalance',$pResultArray['user']['allowgiftcards']);
			}

			$smarty->assign('sharebyemailmethod', $brandingDefaults['sharebyemailmethod']);

			$redactionMode = $brandingDefaults['redactionmode'];
			$smarty->assign('redactionmode', $redactionMode);
			if ($redactionMode == TPX_REDACTION_MODE_IMMEDIATE)
			{
				$smarty->assign('redactiondays', 0);
			}
			else
			{
				$smarty->assign('redactiondays', $brandingDefaults['redactionnotificationdays']);
			}
        }
        else
        {
            $brandingArray = DatabaseObj::getBrandingFromCode($licenseKeyArray['webbrandcode']);
            $onlineDesignerURL = $brandingArray['onlinedesignerurl'];

            // Set the register with email to that of the current brand.
            $registerWithEmail = $brandingArray['registerusingemail'];

            $smarty->assign('googletagmanagercccode', $brandingArray['googletagmanagercccode']);
            
            // if customer set to use default voucher settings and license key is to use default voucher settings use the branding settings
			if (($pResultArray['user']['usedefaultvouchersettings'] === 1) && ($licenseKeyArray['usedefaultvouchersettings'] === 1))
			{
				$smarty->assign('showgiftcardsbalance', $brandingArray['allowgiftcards']);
			}
			// if customer is set to just use default voucher settings in customer use the license key settings
			else if ($pResultArray['user']['usedefaultvouchersettings'] === 1)
			{
				$smarty->assign('showgiftcardsbalance', $licenseKeyArray['allowgiftcards']);
			}
			// otherwise use customer voucher settings
			else
			{
				$smarty->assign('showgiftcardsbalance',$pResultArray['user']['allowgiftcards']);
			}
            $smarty->assign('sharebyemailmethod', $brandingArray['sharebyemailmethod']);

			$redactionMode = $brandingArray['redactionmode'];
			$smarty->assign('redactionmode', $redactionMode);
			if ($redactionMode == TPX_REDACTION_MODE_IMMEDIATE)
			{
				$smarty->assign('redactiondays', 0);
			}
			else
			{
				$smarty->assign('redactiondays', $brandingArray['redactionnotificationdays']);
			}
        }

        // Assign the text for the redaction link.
        switch ($redactionMode)
        {
            case TPX_REDACTION_MODE_IMMEDIATE:
            {
                $smarty->assign('redactionmodeoptiontext', $smarty->get_config_vars('str_LabelImmediateDataDeletion'));
                break;
            }

            case TPX_REDACTION_MODE_ALLOW:
            {
                $smarty->assign('redactionmodeoptiontext', $smarty->get_config_vars('str_LabelActivateDataDeletion'));
                break;
            }

            case TPX_REDACTION_MODE_REQUEST:
            {
                $smarty->assign('redactionmodeoptiontext', $smarty->get_config_vars('str_LabelRequestDataDeletion'));
                break;
            }
        }

        if ($pResultArray['user']['usedefaultpaymentmethods'] == 1)
        {
            // then try licence key
            if ($licenseKeyArray['usedefaultpaymentmethods'] == 1)
            {
                // is there a brand?
                if ($licenseKeyArray['webbrandcode'] == '')
                {
                    $paymentmethods = $brandingDefaults['paymentmethods'];
                }
                else
                {
                    // try brand
                    if ($brandingArray['usedefaultpaymentmethods'] == 1)
                    {
                        $paymentmethods = $brandingDefaults['paymentmethods'];
                    }
                    else
                    {
                        $paymentmethods = $brandingArray['paymentmethods'];
                    }
                }
            }
            else
            {
                $paymentmethods = $licenseKeyArray['paymentmethods'];
            }
        }
        else
        {
            $paymentmethods = $pResultArray['user']['paymentmethods'];
        }

        $smarty->assign('sidebaraccount', $smarty->getLocaleTemplate('customer/sidebaraccount_'.$pResultArray['section'].'.tpl', ''));
        $smarty->assign('sidebaraccount_default', $smarty->getLocaleTemplate('customer/sidebaraccount_default.tpl', ''));

        $smarty->assign('sidebarcontactdetails', $smarty->getLocaleTemplate('customer/sidebarcontactdetails_'.$pResultArray['section'].'.tpl', ''));
        $smarty->assign('sidebarcontactdetails_default', $smarty->getLocaleTemplate('customer/sidebarcontactdetails_default.tpl', ''));

        $smarty->assign('sidebaradditionalinfo', $smarty->getLocaleTemplate('customer/sidebaradditionalinfo_'.$pResultArray['section'].'.tpl', ''));
        $smarty->assign('sidebaradditionalinfo_default', $smarty->getLocaleTemplate('customer/sidebaradditionalinfo_default.tpl', ''));

        $smarty->assign('webbrandapplicationname', UtilsObj::escapeInputForJavaScript($gSession['webbrandapplicationname']));

        $smarty->assign('sidebarredactioninfo', $smarty->getLocaleTemplate('customer/sidebarredactioninfo_'.$pResultArray['section'].'.tpl', ''));
        $smarty->assign('sidebarredaction_default', $smarty->getLocaleTemplate('customer/sidebarredaction_default.tpl', ''));


    	// include the system language selector
        if ($gSession['ismobile'] == true)
        {
            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), true);
            $smarty->assign('systemlanguagelist', $languageHTMLList);
        }
        else
        {
            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), false);
            $smarty->assign('systemlanguagelist', $languageHTMLList);
        }

        $userAccountArray = $pResultArray['user'];

        $userGiftCardResult = $pResultArray['result'];
        $smarty->assign('giftcardresult', $userGiftCardResult);
        $smarty->assign('showgiftcardmessage', $gSession['showgiftcardmessage']);

        if($gSession['showgiftcardmessage'] == 1)
        {
            $gSession['showgiftcardmessage'] = 0;
            DatabaseObj::updateSession();
        }

        $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($pResultArray['user']['groupcode']);

        if ($licenseKeyArray['usedefaultcurrency'] == 0)
        {
        	$currencyCode = $licenseKeyArray['currencycode'];
        }
        else
        {
        	$currencyCode = $gConstants['defaultcurrencycode'];
        }

		$smarty->assign('protectedfromredaction', $userAccountArray['protectedfromredaction']);

		$displayRedaction = 0;

		if ($userAccountArray['protectedfromredaction'] == 0)
		{
			if (($redactionMode == TPX_REDACTION_MODE_REQUEST) ||
				($redactionMode == TPX_REDACTION_MODE_ALLOW) ||
				($redactionMode == TPX_REDACTION_MODE_IMMEDIATE))
			{
				$displayRedaction = 1;
			}
		}
		$smarty->assign('displayredaction', $displayRedaction);

        $currencyArray = DatabaseObj::getCurrency($currencyCode);
        if (strpos($paymentmethods, 'ACCOUNT') !== false)
		{
            $isAccountCustomer = true;
            $formattedAccountBalance = UtilsObj::formatCurrencyNumber($userAccountArray['accountbalance'], $currencyArray['decimalplaces'], $gSession['browserlanguagecode'],
                    $currencyArray['symbol'], $currencyArray['symbolatfront']);

            $formattedCreditLimit = UtilsObj::formatCurrencyNumber($userAccountArray['creditlimit'], $currencyArray['decimalplaces'], $gSession['browserlanguagecode'],
                    $currencyArray['symbol'], $currencyArray['symbolatfront']);
        }
        else
        {
            $formattedAccountBalance = '';
            $formattedCreditLimit = '';
        }

        $availableGiftCardBalance = $userAccountArray['giftcardbalance'] - DatabaseObj::getSessionGiftCardTotal($gSession['ref'], $userAccountArray['recordid'], true);
        $formattedGiftCardBalance = UtilsObj::formatCurrencyNumber($availableGiftCardBalance, $currencyArray['decimalplaces'], $gSession['browserlanguagecode'],
                    $currencyArray['symbol'], $currencyArray['symbolatfront']);

        // we need to check if the user was created online and has still not updated their address.
        // If they have not then we must allow them to update their full address and contact details
        if ($userAccountArray['addressupdated'] == 2)
        {
        	$smarty->assign('edit', '0');
			$smarty->assign('canmodifyaccountdetails', 1);
        }
        else
        {
        	if ($userAccountArray['defaultaddresscontrol'] == 1)
			{
				if ($licenseKeyArray['canmodifyshippingaddress'] == 1)
				{
					$smarty->assign('edit', '0');
					$smarty->assign('canmodifyaccountdetails', 1);
				}
				else
				{
					if ($licenseKeyArray['canmodifyshippingcontactdetails'] == 1)
					{
						$smarty->assign('canmodifyaccountdetails', 1);
						$smarty->assign('edit', '1');
					}
					else
					{
						$smarty->assign('canmodifyaccountdetails', 0);
						$smarty->assign('edit', '0');
					}
				}
			}
			else
			{
				if ($userAccountArray['canmodifyshippingaddress'] == 1)
				{
					$smarty->assign('edit', '0');
					$smarty->assign('canmodifyaccountdetails', 1);
				}
				else
				{
					if ($userAccountArray['canmodifyshippingcontactdetails'] == 1)
					{
						$smarty->assign('canmodifyaccountdetails', 1);
						$smarty->assign('edit', '1');
					}
					else
					{
						$smarty->assign('canmodifyaccountdetails', 0);
						$smarty->assign('edit', '0');
					}
				}
			}
        }

        $smarty->assign('canmodifypassword', $userAccountArray['canmodifypassword']);

        $smarty->assign('showaccountbalance', $isAccountCustomer);
        $smarty->assign('accountbalance', $formattedAccountBalance);
        $smarty->assign('creditlimit', $formattedCreditLimit);

        $smarty->assign('giftcardbalance', $formattedGiftCardBalance);

        $smarty->assign('isConfirmation', $pResultArray['isConfirmation']);

    	if (substr($pResultArray['message'], 0, 4) == 'str_')
        {
            $smarty->assign('message', $smarty->get_config_vars($pResultArray['message']));
        }
        elseif ((($userAccountArray['addressupdated'] == 0) && ($userAccountArray['canmodifyshippingaddress'] == 1)) ||
        	    (($userAccountArray['addressupdated'] == 0) && ($userAccountArray['canmodifybillingaddress'] == 1)))
        {
            $smarty->assign('message',  $smarty->get_config_vars('str_MessageUpdateAddressDetails'));
            $smarty->assign('addressupdated',  1);
        }
        else
        {
        	$smarty->assign('message', $pResultArray['message']);
        }

    	if ((($userAccountArray['addressupdated'] == 0) && ($userAccountArray['canmodifyshippingaddress'] == 1)) ||
        	(($userAccountArray['addressupdated'] == 0) && ($userAccountArray['canmodifybillingaddress'] == 1)))
        {
        	$pResultArray['section'] = 'accountdetails';

            if (($onlineDesignerURL != '') && ($gConstants['optiondesol']))
            {
                $smarty->assign('hasonlinedesignerurl', 1);
            }
            else
            {
                $smarty->assign('hasonlinedesignerurl', 0);
            }
        }

        $smarty->assign('section', $pResultArray['section']);
        /* end of genereic part */

        // force to not prompt the update address form on mobile version
        $addressUpdated = 1;
        $refreshAction = '';

        $logOutFuseAction = 'Customer.logout';
        $brandCode = $gSession['webbrandcode'];
        $basketRef = '';

		$homeButtonFuseAction = 'Customer.initialize';

		if (($pResultArray['ishighlevel'] == 1) && (array_key_exists('mawebhlbr', $_GET)))
		{
			$basketRef = $_GET['mawebhlbr'];
			$logOutFuseAction = 'OnlineAPI.hlAccountPageLogout';
			$homeButtonFuseAction = 'OnlineAPI.hlMyAccountDisplay';
			$smarty->assign('userlogin', $gSession['userlogin']);
		}

		$smarty->assign('languagecode', UtilsObj::getBrowserLocale());
		$smarty->assign('ishighlevel', $pResultArray['ishighlevel']);
		$smarty->assign('logoutfsaction', $logOutFuseAction);
		$smarty->assign('homebuttonfuseaction', $homeButtonFuseAction);
		$smarty->assign('basketref', $basketRef);
		$smarty->assign('webbrandcode', $brandCode);
		$smarty->assign('registeredtaxnumbertype', 0);
        $smarty->assign('registeredtaxnumber', '');

        if (array_key_exists('hasflaggedprojects', $pResultArray))
		{
			$checkUrl = 1 == $pResultArray['showprojectsbutton'] ? 'Customer.displayOnlineProjectList' : ('' === $licenseKeyArray['webbrandcode'] ? $brandingDefaults['mainwebsiteurl'] : $brandingArray['mainwebsiteurl']);
			$smarty->assign('hasflaggedonlineprojects', $pResultArray['hasflaggedprojects']);
			$smarty->assign('checkprojectslink', $checkUrl);
		}
        else
		{
			$smarty->assign('hasflaggedonlineprojects', false);
		}


        switch ($pResultArray['section'])
        {
        	case 'menu':
            {
                $refreshAction = 'Customer.initialize';
                if ($onlineDesignerURL != '')
                {
                    $smarty->assign('hasonlinedesignerurl', 1);
                }
                else
                {
                    $smarty->assign('hasonlinedesignerurl', 0);
                }

				if ($pResultArray['showprojectsbutton'] == 0)
				{
					$smarty->assign('hasonlinedesignerurl', 0);
                }

                if ($gSession['ismobile'] == true)
                {
                    // Required for small screen to determine if the pageflip preview could be used.
                    $brandingArray = DatabaseObj::getBrandingFromCode($gSession['webbrandcode']);

                    // Determine if the Content Security Policy is active.
                    $cspActive = true;

                    if ((array_key_exists('CONTENTSECURITYPOLICY', $ac_config)) && ($ac_config['CONTENTSECURITYPOLICY'] === 'DISABLED'))
                    {
                        $cspActive = false;
                    }

                    // Add the unsafe-eval to allow the pageflip script to execute.
                    // This must be done here, due to the way the small screen account pages work (side scrolling).
                    if (($brandingArray['previewlicensekey'] != '') && $cspActive)
                    {
                        $cspBuilder = ControlCentreCSP::getInstance(UtilsObj::getGlobalValue('ac_config'));

                        $cspBuilder->getBuilder()->setAllowUnsafeEval('script-src', true);
                    }
                }

                break;
            }
            case 'yourorders':
            {
				$orderCount = count($pResultArray['orders']['orders']);
				
				// Determine if the Content Security Policy is active.
				$cspActive = UtilsObj::getCSPActive();
				$cspPreviewThumbnailCache = [];

				if ($cspActive)
				{
					foreach ($pResultArray['orders']['orders'] as $pOrder)
					{
						foreach ($pOrder['product'] as $OrderProduct)
						{
							if ($OrderProduct['projectpreviewthumbnail'] !== '')
							{
								// Add the unique domain to the list for CSP.
								$parsedUrl = parse_url($OrderProduct['projectpreviewthumbnail']);
								$cspPreviewThumbnailCache[$parsedUrl['scheme'] . '://' . $parsedUrl['host']] = true;
							}
						}
					}
				}

				if (count($cspPreviewThumbnailCache) > 0)
				{
					// Add the project preview thumbnail domain to the CSP rules.
					$cspBuilder = ControlCentreCSP::getInstance(UtilsObj::getGlobalValue('ac_config'));

					array_map(function($pThumbnailDomain) use ($cspBuilder)
					{
						$cspBuilder->getBuilder()->addSource('image-src', $pThumbnailDomain);
					}, array_keys($cspPreviewThumbnailCache));
				}

            	$smarty->assign('title', $smarty->get_config_vars('str_MenuTitleYourOrders'));
            	$refreshAction = 'Customer.yourOrders';

                $tempOrderCount = count($pResultArray['orders']['temporders']);
                if ($tempOrderCount > 0)
                {
                    $smarty->assign('sectiontitle', $smarty->get_config_vars('str_MenuTitlePayLaterOrders'));
                    $smarty->assign('sectiontitle2', $smarty->get_config_vars('str_MenuTitleYourOrders'));
                }
                else
                {
                    $smarty->assign('sectiontitle', $smarty->get_config_vars('str_MenuTitleYourOrders'));
                    $smarty->assign('sectiontitle2', '');
                }

                $smarty->assign('temporderlist', $pResultArray['orders']['temporders']);
                $smarty->assign('tempordercount', $tempOrderCount);

                $smarty->assign('orderlist', $pResultArray['orders']['orders']);
                $smarty->assign('ordercount', $orderCount);
                break;
            }
            case 'accountdetails':
            {
				$refreshAction = 'Customer.accountDetails';
            	$additionalAddressFields = UtilsAddressObj::getAdditionalAddressFields($userAccountArray['countrycode'], $userAccountArray['address4']);
				$userAccountArray['add41'] = $additionalAddressFields['add41'];
				$userAccountArray['add42'] = $additionalAddressFields['add42'];
				$userAccountArray['add43'] = $additionalAddressFields['add43'];
        		$smarty->assign('strictmode', '1');
                $smarty->assign('contactfname', UtilsObj::escapeInputForJavaScript($userAccountArray['contactfirstname']));
                $smarty->assign('contactlname', UtilsObj::escapeInputForJavaScript($userAccountArray['contactlastname']));
                $smarty->assign('companyname', UtilsObj::escapeInputForJavaScript($userAccountArray['companyname']));
                $smarty->assign('address1', UtilsObj::escapeInputForJavaScript($userAccountArray['address1']));
                $smarty->assign('address2', UtilsObj::escapeInputForJavaScript($userAccountArray['address2']));
                $smarty->assign('address3', UtilsObj::escapeInputForJavaScript($userAccountArray['address3']));
                $smarty->assign('address4', UtilsObj::escapeInputForJavaScript($userAccountArray['address4']));
				$smarty->assign('add41', UtilsObj::escapeInputForJavaScript($userAccountArray['add41']));
				$smarty->assign('add42', UtilsObj::escapeInputForJavaScript($userAccountArray['add42']));
				$smarty->assign('add43', UtilsObj::escapeInputForJavaScript($userAccountArray['add43']));
                $smarty->assign('city', UtilsObj::escapeInputForJavaScript($userAccountArray['city']));
                $smarty->assign('county', UtilsObj::escapeInputForJavaScript($userAccountArray['county']));
                $smarty->assign('state', UtilsObj::escapeInputForJavaScript($userAccountArray['state']));
                $smarty->assign('regioncode', UtilsObj::escapeInputForJavaScript($userAccountArray['regioncode']));
                $smarty->assign('postcode', UtilsObj::escapeInputForJavaScript($userAccountArray['postcode']));
                $smarty->assign('telephonenumber', UtilsObj::escapeInputForHTML($userAccountArray['telephonenumber']));
                $smarty->assign('email', UtilsObj::escapeInputForHTML($userAccountArray['emailaddress']));
                $smarty->assign('country', UtilsObj::escapeInputForJavaScript($userAccountArray['countrycode']));
                $smarty->assign('countryname', UtilsObj::escapeInputForJavaScript($userAccountArray['countryname']));
                $addressUpdated = $userAccountArray['addressupdated'];

                $smarty->assign('registeredtaxnumbertype', UtilsObj::escapeInputForJavaScript($userAccountArray['registeredtaxnumbertype']));
                $smarty->assign('registeredtaxnumber', UtilsObj::escapeInputForJavaScript($userAccountArray['registeredtaxnumber']));

                $smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_NA', TPX_REGISTEREDTAXNUMBERTYPE_NA);
				$smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL', TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL);
				$smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE', TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE);

                $smarty->assign('simpleDialog', $smarty->getLocaleTemplate('customer/simple_dialog.tpl', ''));

				$smarty->assign('showPendingMessage', ($pResultArray['user']['pendingEmailChange'] ? 1 : 0));

                break;
            }
            case 'changepassword':
            {
                $refreshAction = 'Customer.changePassword';
                break;
            }
            case 'existingonlineprojects':
            {
                $refreshAction = 'Customer.displayOnlineProjectList';

				$cspPreviewThumbnailCache = [];

				// Determine if the Content Security Policy is active.
				$cspActive = UtilsObj::getCSPActive();

				$projectCount = count($pResultArray['projects']);
				for ($i = 0; $i < $projectCount; $i++)
				{
					$statusDescription = $pResultArray['projects'][$i]['statusdescription'];
					if ($statusDescription != '')
					{
						$pResultArray['projects'][$i]['statusdescription'] = $smarty->get_config_vars($statusDescription);
					}

					$pResultArray['projects'][$i]['name'] = UtilsObj::escapeInputForHTML($pResultArray['projects'][$i]['name']);

					if (($pResultArray['projects'][$i]['projectpreviewthumbnail'] !== '') && ($cspActive))
					{
						// Add the unique domain to the list for CSP.
						$parsedUrl = parse_url($pResultArray['projects'][$i]['projectpreviewthumbnail']);
						$cspPreviewThumbnailCache[$parsedUrl['scheme'] . '://' . $parsedUrl['host']] = true;
					}
				}

				if (count($cspPreviewThumbnailCache) > 0)
				{
					// Add the project preview thumbnail domain to the CSP rules.
					$cspBuilder = ControlCentreCSP::getInstance(UtilsObj::getGlobalValue('ac_config'));

					array_map(function($pThumbnailDomain) use ($cspBuilder)
					{
						$cspBuilder->getBuilder()->addSource('image-src', $pThumbnailDomain);
					}, array_keys($cspPreviewThumbnailCache));
				}

                $smarty->assign('projects', $pResultArray['projects']);

				// added check to ensure maintenancemode is set, if not set this to false
                $smarty->assign('maintenancemode', (isset($pResultArray['maintenancemode']) ? $pResultArray['maintenancemode'] : false));

				$browserArray = OnlineAPI_model::checkBrowsers();

				$smarty->assign('browsersupported', $browserArray['browsersupported']);

                $brandingArray = DatabaseObj::getBrandingFromCode($gSession['webbrandcode']);

                $smarty->assign('onlinedesignerurl', UtilsObj::correctPath($brandingArray['onlinedesignerurl'], "/", false));

                $smarty->assign('simpleDialog', $smarty->getLocaleTemplate('customer/simple_dialog.tpl', ''));
                $smarty->assign('showpurgeall', $pResultArray['purgableprojects']);
                break;
            }
            case 'changepreferences':
            {
                $refreshAction = 'Customer.changePreferences';
                $smarty->assign('sendmarketinginfo', $userAccountArray['sendmarketinginfo']);
            }
        }

        $smarty->assign('addressupdated', $addressUpdated);

        $smarty->assign('refreshaction', $refreshAction);
        $smarty->assign('showaccountbalance', $isAccountCustomer);
        $smarty->assign('supporttelephonenumber', $gSession['webbrandsupporttelephonenumber']);
		$smarty->assign('supportemailaddress', $gSession['webbrandsupportemailaddress']);
        $smarty->assign('webbranddisplayurl', $gSession['webbranddisplayurl']);
        $smarty->assign('webbrandweburl', UtilsObj::correctPath($gSession['webbrandweburl'], "/", true));

        $autoSuggestAvailable = 0;

        // Check to see if the tax calculation script exists
		if (file_exists("../Customise/scripts/EDL_TaopixCustomerAccountAPI.php"))
		{
			require_once('../Customise/scripts/EDL_TaopixCustomerAccountAPI.php');

			if (method_exists('CustomerAccountAPI', 'autoSuggest'))
        	{
        		$autoSuggestAvailable = 1;
        	}
		}

		$smarty->assign('autosuggestavailable', $autoSuggestAvailable);
        $smarty->assign('kProducTypePhotobook', TPX_PRODUCT_TYPE_PHOTO_BOOK);
        $smarty->assign('kProducTypeProofbook', TPX_PRODUCT_TYPE_PROOF_BOOK);
        $smarty->assign('kProducTypeSinglePrints', TPX_PRODUCT_TYPE_SINGLE_PRINTS);
		$smarty->assign('kProducTypePhotoPrints', TPX_PRODUCT_TYPE_PHOTO_PRINTS);
        $smarty->assign('kProducTypeCalendar', TPX_PRODUCT_TYPE_CALENDAR);
        $smarty->assign('kProducTypeYearBook', TPX_PRODUCT_TYPE_YEAR_BOOK);
        $smarty->assign('kProducTypeCanvas', TPX_PRODUCT_TYPE_CANVAS);
        $smarty->assign('kProducTypeCard', TPX_PRODUCT_TYPE_CARD);

		$smarty->assign('passwordstrengthmin', $gConstants['minpasswordscore']);

		// If on small screen, display the iOS version.
        $smarty->assign('minSafariVersion', (($gSession['ismobile']) ? TPX_ONLINESUPPORTED_IOS_VERSION : TPX_ONLINESUPPORTED_BROWSER_VERSION_SAFARI));
        $smarty->assign('minIEVersion', TPX_ONLINESUPPORTED_BROWSER_VERSION_INTERNETEXPLORER);

        // Does the customer need to re-authenticate when updating their details
        if($gConstants['customerupdateauthrequired'] == 1)
        {
            $smarty->assign('customerupdateauthrequired', true);
        }
        else
        {
            $smarty->assign('customerupdateauthrequired', false);
        }

		// Detect if easy editor mode is active for the device detection.
		if ((! array_key_exists('USELEGACYEDITOR', $ac_config)) || ($ac_config['USELEGACYEDITOR'] == 0))
		{
			$smarty->assign('easyEditorModeActive', 1);
		}
		else
		{
			$smarty->assign('easyEditorModeActive', 0);
		}

		$smarty->assign('tzoffset', (int) UtilsObj::getPOSTParam('tzoffset', '0'));
		$smarty->assign('kCanReorder', TPX_ITEM_CAN_REORDER);

        $smarty->cachePage = true; // allow the page to be cached so that the browser back button works correctly
        if ($gSession['ismobile'] == true)
        {
            $smarty->assign('isajaxcall', $pAjaxCall);
            if ($pAjaxCall == true)
            {
                $resultArray['template'] = $smarty->fetchLocale('customer/mainajax_small.tpl');
                $resultArray['javascript'] = $smarty->fetchLocale('customer/main.tpl');
                echo json_encode($resultArray);
            }
            else
            {
                // Display the customer account pages.
                $smarty->displayLocale('customer/main_small.tpl');
            }
        }
        else
        {
            // Display the customer account pages.
            $smarty->displayLocale('customer/main_large.tpl');
        }
    }

    static function showPreview($pResultArray)
    {
        // if we have an order created using Taopix online and we still have not recieved the files we cant order.
		if (($pResultArray['ordersource'] == TPX_SOURCE_ONLINE) && ($pResultArray['productionstatus'] == TPX_ITEM_STATUS_AWAITING_FILES))
		{
			$previewOwner = -1;
		}
		else
		{
			$previewOwner = TPX_PREVIEW_CUSTOMER;
		}

        Share_view::preview($pResultArray, $previewOwner, false);

    }

    static function displayError($pResultArray)
    {
        global $gSession;

		$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
        SmartyObj::replaceParams($smarty, $pResultArray['result'], $pResultArray['resultparam']);
        $smarty->assign('error1', $smarty->get_template_vars($pResultArray['result']));
        $smarty->assign('error2', '');

        if ($gSession['ismobile'] == true)
        {
			$smarty->assign('displayInline', false);
            $smarty->displayLocale('error_small.tpl');
        }
        else
        {
            $smarty->displayLocale('error_large.tpl');
        }

    }

    static function updateGiftCard($pResultArray)
    {
        global $gSession;
        global $gConstants;

        $userAccountArray = $pResultArray['user'];

        $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($pResultArray['user']['groupcode']);

        if ($licenseKeyArray['usedefaultcurrency'] == 0)
        {
        	$currencyCode = $licenseKeyArray['currencycode'];
        }
        else
        {
        	$currencyCode = $gConstants['defaultcurrencycode'];
        }

        $currencyArray = DatabaseObj::getCurrency($currencyCode);

        $availableGiftCardBalance = $userAccountArray['giftcardbalance'] - DatabaseObj::getSessionGiftCardTotal($gSession['ref'], $userAccountArray['recordid'], true);
        $formattedGiftCardBalance = UtilsObj::formatCurrencyNumber($availableGiftCardBalance, $currencyArray['decimalplaces'], $gSession['browserlanguagecode'],
                    $currencyArray['symbol'], $currencyArray['symbolatfront']);
        echo json_encode(array('giftcardresult' => $pResultArray['result'],
                                'showgiftcardmessage' => $gSession['showgiftcardmessage'],
                                'giftcardbalance' => $formattedGiftCardBalance));
    }


    static function updateAjaxAction($pResultArray)
    {
        global $gSession;

        $message = '';
        $userAccountArray = $pResultArray['user'];

        $smarty = SmartyObj::newSmarty('Customer', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        if (substr($pResultArray['message'], 0, 4) == 'str_')
        {
            $message = $smarty->get_config_vars($pResultArray['message']);
        }
        elseif ((($userAccountArray['addressupdated'] == 0) && ($userAccountArray['canmodifyshippingaddress'] == 1)) ||
        	    (($userAccountArray['addressupdated'] == 0) && ($userAccountArray['canmodifybillingaddress'] == 1)))
        {
            $message = $smarty->get_config_vars('str_MessageUpdateAddressDetails');
        }
        else
        {
        	$message = $pResultArray['message'];
        }

         echo json_encode(array('message' => $message, 'section' => $pResultArray['section']));
    }

    static function verifyPassword($pResultArray)
    {
        global $gSession;

        if($pResultArray['result'] != '')
        {
            $smarty = SmartyObj::newSmarty('', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

            $pResultArray['result'] = $smarty->get_config_vars($pResultArray['result']);
            $pResultArray['message'] = $pResultArray['result'];
        }

        echo json_encode($pResultArray);
    }
}

?>