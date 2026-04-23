<?php

class Welcome_view
{
    static function showLoginTemplate($ref = 0, $canCreateAccounts = 0, $error = '', $info = '', $loginValue = '', $pForceHighLevelBasketUser = 0, $pIsHighLevel = 0, $pPrtz = 0, $pOnlineBasketUniqueID = '', $pOnlineBasketRef = '')
    {
        global $ac_config;
        global $gSession;

        $smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        $smarty->assign('resetpasswordenabled', $ac_config['RESETPASSWORDENABLED'] == 1);
        $smarty->assign('error', $error);
        $smarty->assign('info', $info);
        $smarty->assign('loginVal', UtilsObj::escapeInputForHTML($loginValue));

		// Generate the class for the message area.
        if ('' == $info)
		{
			$smarty->assign('messageareaclass', 'message warning');
		}
		else
		{
			$smarty->assign('messageareaclass', 'message confirmation warning');
		}

        // always show the standard header at the login page
        $smarty->assign('footer', $smarty->getLocaleTemplate('footer.tpl', ''));

        $smarty->assign('login2Template', '');
        $smarty->assign('fromregisterlink', '0');

        // if we have a reference number then we must be ordering
        if (($ref > 0) && ($gSession['isordersession'] == 1))
        {
            if (($canCreateAccounts == 1) && ($pForceHighLevelBasketUser == 0))
            {
                if ($gSession['ismobile'] == true)
                {
                    $smarty->assign('login2Template', $smarty->getLocaleTemplate('login2_small.tpl', ''));
                }
                else
                {
                    $smarty->assign('login2Template', $smarty->getLocaleTemplate('login2_large.tpl', ''));
                }
            }
        }

		if ($pIsHighLevel)
		{
			$smarty->assign('groupcode', UtilsObj::escapeInputForHTML(UtilsObj::getGETParam('groupcode', '')));
			$smarty->assign('loginfsaction', 'OnlineAPI.processLogin');
		}
		else
		{
			$smarty->assign('groupcode', '');
			$smarty->assign('loginfsaction', 'Welcome.processLogin');
		}

        $smarty->assign('canCreateAccounts', $canCreateAccounts);
        $smarty->assign('changelanguageinitfsaction', 'Welcome.initialize');

		$smarty->cachePage = false; // do not allow the page to be cached so that the browser grabs the correct language

        if ($gSession['ismobile'] == true)
        {
			if ($pIsHighLevel)
			{
				$smarty->assign('groupcode_script', UtilsObj::escapeInputForJavaScript(UtilsObj::getGETParam('groupcode', '')));
			}
			else
			{
				$smarty->assign('groupcode_script', '');
			}

			// Generate the small screen error dialog box content to be displayed.
			$smarty->assign('dialogMessage', "<p>" . $error . "</p>");

			$smarty->assign('ishighlevel', UtilsObj::escapeInputForJavaScript($pIsHighLevel));
			$smarty->assign('prtz', UtilsObj::escapeInputForJavaScript($pPrtz));
			$smarty->assign('mawebhluid', UtilsObj::escapeInputForJavaScript($pOnlineBasketUniqueID));
			$smarty->assign('mawebhlbr', UtilsObj::escapeInputForJavaScript($pOnlineBasketRef));
			$smarty->assign('fhlbu', UtilsObj::escapeInputForJavaScript($pForceHighLevelBasketUser));

            $smarty->assign('registerfsaction', 'Welcome.createNewAccountSmall');

            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), true);
            $smarty->assign('systemlanguagelist', $languageHTMLList);

            $smarty->assign('header', $smarty->getLocaleTemplate('header_small.tpl', ''));
            $smarty->displayLocale('login_small.tpl');
        }
        else
        {
			$smarty->assign('ishighlevel', UtilsObj::escapeInputForHTML($pIsHighLevel));
			$smarty->assign('prtz', UtilsObj::escapeInputForHTML($pPrtz));
			$smarty->assign('mawebhluid', UtilsObj::escapeInputForHTML($pOnlineBasketUniqueID));
			$smarty->assign('mawebhlbr', UtilsObj::escapeInputForHTML($pOnlineBasketRef));
			$smarty->assign('fhlbu', UtilsObj::escapeInputForHTML($pForceHighLevelBasketUser));

            $smarty->assign('registerfsaction', 'Welcome.createNewAccountLarge');

            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), false);
            $smarty->assign('systemlanguagelist', $languageHTMLList);

            $smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));

            $smarty->assign('sidebaradditionalinfo', $smarty->getLocaleTemplate('sidebaradditionalinfo_login.tpl', ''));
            $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('sidebarleft_login.tpl', ''));
            $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('sidebarleft_default.tpl', ''));

            $smarty->displayLocale('login_large.tpl');
        }
    }

    static function displayLogin($resultArray)
    {
        self::showLoginTemplate($resultArray['ref'], $resultArray['cancreateaccounts'], '', $resultArray['info'], $resultArray['login'], 0, $resultArray['ishighlevel'], $resultArray['prtz'], $resultArray['mawebhluid'], $resultArray['mawebhlbr']);
    }

    static function processLogin($resultArray)
    {
        global $gSession;
        global $gConstants;

        if ($resultArray['result'] == '')
        {
            if ($gSession['isordersession'] == 1)
            {
            	// include the shopping cart module
            	require_once('../Order/Order_control.php');

                Order_control::initialize2();
            }
            else if (($resultArray['usertype'] != TPX_LOGIN_CREATOR_ADMIN) &&
            	($resultArray['usertype'] != TPX_LOGIN_CUSTOMER) && ($resultArray['usertype'] != TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER))
            {
                if ($resultArray['usertype'] == TPX_LOGIN_COMPANY_ADMIN && !$gConstants['optionms'])
                {
                    echo 'Not Implemented.';
                    Welcome_model::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
                }
                else
                {
                    // include the admin module
					require_once('../Admin/Admin_control.php');

                    $_GET['ref'] = $gSession['ref'];
                    Admin_control::initialize();
                }
            }
            else if ($resultArray['iscustomer'] == 1)
            {
                $_POST['ref'] = $gSession['ref'];

                // determine the login type
                if ((isset($_GET['fsaction'])) && ($_GET['fsaction'] == 'Share.preview') && (isset($_GET['ref2'])))
                {
                	// preview login

                	// include the social sharing module
                	require_once('../Share/Share_control.php');

                    // get info about originally shared item and check if it's the same user who shared
                    $sharedItem = Share_model::getOriginalSharedItem($_GET['ref2']);
                    if (($sharedItem['result'] == '') && ($gSession['userid'] == $sharedItem['userid']))
                    {
                        $shareResult = Share_model::preview($_GET['ref2']);

                        if(!empty($shareResult['pages']) || ($shareResult['result'] != '') || ($shareResult['ordersource'] == TPX_SOURCE_ONLINE))
                        {
                            if (($shareResult['productionstatus'] == TPX_ITEM_STATUS_AWAITING_FILES) && ($shareResult['canupload'] == 1) &&
                                ($shareResult['ordersource'] == TPX_SOURCE_ONLINE))
                            {
                                Share_view::previewNotAvailable(true);
                            }
                            else
                            {
                                Share_view::preview($shareResult, TPX_PREVIEW_CUSTOMER, true);
                            }
                        }
                        else
                        {
                            Share_view::previewNotFound(true);
                        }
                    }
                    else
                    {
                        // if the user is trying to view somebody's elses preview then just log them into their account

                        // include the customer module
                        require_once('../Customer/Customer_control.php');

                        Customer_control::initialize2();
                    }
                }
                else
                {
                	// customer login

                	// include the customer module
                    require_once('../Customer/Customer_control.php');

                    Customer_control::initialize2();
                }
            }
            else
            {
                Welcome_model::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
                $resultArray['result'] = 'str_LoginNotForControlCentre';
            }
        }
        else
        {
            $foreceHighLevelBasketUser = 0;
			$isHighLevel = UtilsObj::getArrayParam($resultArray, 'ishighlevel', 0);
			$prtz = UtilsObj::getArrayParam($resultArray, 'prtz', 0);
			$highLevelUniqueID = UtilsObj::getArrayParam($resultArray, 'mawebhluid', '');
			$highLevelBasketRef = UtilsObj::getArrayParam($resultArray, 'mawebhlbr', '');

            if (array_key_exists('fhlbu', $_POST))
            {
            	$foreceHighLevelBasketUser = $_POST['fhlbu'];
            }

            $error = $resultArray['result'];
            if (substr($error, 0, 4) == 'str_')
            {
				$smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
                SmartyObj::replaceParams($smarty, $error, $resultArray['resultparam']);
                $error = $smarty->get_template_vars($resultArray['result']);
            }

            self::showLoginTemplate($resultArray['ref'], $resultArray['cancreateaccounts'], $error, '', $resultArray['login'], $foreceHighLevelBasketUser, $isHighLevel, $prtz, $highLevelUniqueID, $highLevelBasketRef);
        }
    }

    static function processLogout($pRedirectURL, $ref = 0, $canCreateAccounts = 0, $message = '', $pForeceHighLevelBasketUser = 0)
    {
        if ($pRedirectURL != '')
        {
            self::processLogoutRedirect($pRedirectURL);
        }
        else
        {
            self::showLoginTemplate($ref, $canCreateAccounts, SmartyObj::getParamValue('Login', $message), '', '', $pForeceHighLevelBasketUser);
        }
    }

    static function processLogoutRedirect($pRedirectURL)
    {
        if ($pRedirectURL == '')
        {
            $pRedirectURL = UtilsObj::getBrandedWebUrl();
        }

        header('Location: ' . $pRedirectURL);
    }

    static function initNewAccount($resultArray)
    {
        global $gSession;
        global $gConstants;

        $smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        // include the system language selector
        $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale());
        $smarty->assign('systemlanguagelist', $languageHTMLList);

        // always show the standard header at the login page
        $smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));
        $smarty->assign('footer', $smarty->getLocaleTemplate('footer_large.tpl', ''));
        $smarty->assign('sidebaradditionalinfo', $smarty->getLocaleTemplate('sidebaradditionalinfo_newaccount.tpl', ''));
        $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('sidebarleft_newaccount.tpl', ''));
        $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('sidebarleft_default.tpl', ''));
        $smarty->assign('sidebarcontactdetails', $smarty->getLocaleTemplate('sidebarcontactdetails_newaccount.tpl', ''));
        $smarty->assign('sidebarcontactdetails_default', $smarty->getLocaleTemplate('sidebarcontactdetails_default.tpl', ''));
        $smarty->assign('addresstitle', $smarty->get_config_vars('str_LabelContactInformation'));

        $smarty->assign('contactfname', UtilsObj::escapeInputForJavaScript($resultArray['contactfname']));
        $smarty->assign('contactlname', UtilsObj::escapeInputForJavaScript($resultArray['contactlname']));
        $smarty->assign('companyname', UtilsObj::escapeInputForJavaScript($resultArray['companyname']));
        $smarty->assign('address1', UtilsObj::escapeInputForJavaScript($resultArray['address1']));
        $smarty->assign('address2', UtilsObj::escapeInputForJavaScript($resultArray['address2']));
        $smarty->assign('address3', UtilsObj::escapeInputForJavaScript($resultArray['address3']));
        $smarty->assign('address4', UtilsObj::escapeInputForJavaScript($resultArray['address4']));
        $smarty->assign('city', UtilsObj::escapeInputForJavaScript($resultArray['city']));
        $smarty->assign('state', UtilsObj::escapeInputForJavaScript($resultArray['state']));
        $smarty->assign('county', UtilsObj::escapeInputForJavaScript($resultArray['county']));
        $smarty->assign('regioncode', UtilsObj::escapeInputForJavaScript($resultArray['regioncode']));
        $smarty->assign('postcode', UtilsObj::escapeInputForJavaScript($resultArray['postcode']));
        $smarty->assign('country', UtilsObj::escapeInputForJavaScript($resultArray['countrycode']));

        $smarty->assign('telephonenumber', UtilsObj::escapeInputForHTML($resultArray['telephonenumber']));
        $smarty->assign('email', UtilsObj::escapeInputForHTML($resultArray['emailaddress']));

        // Show the username input if the brand setting allows users to register with a username
        if ($resultArray['registerusingemail'] == TPX_REGISTER_USING_USERNAME)
        {
            $smarty->assign('showusernameinput', 1);
            $smarty->assign('usernamefieldid', 'login');
        }
        else
        {
            $smarty->assign('showusernameinput', 0);
            $smarty->assign('usernamefieldid', 'email');
        }

        $smarty->assign('session', $gSession['ref']);
        $smarty->assign('sendmarketinginfo', $gSession['webbranddefaultcommunicationpreference']);
        $smarty->assign('tablewidth', 650);
        $smarty->assign('strictmode', '1');

        $smarty->assign('ishighlevel', UtilsObj::escapeInputForHTML($resultArray['ishighlevel']));
        $smarty->assign('registerfsaction', UtilsObj::escapeInputForHTML($resultArray['registerfsaction']));
		$smarty->assign('groupcode', UtilsObj::escapeInputForHTML($resultArray['groupcode']));
		$smarty->assign('prtz', UtilsObj::escapeInputForHTML($resultArray['prtz']));
		$smarty->assign('mawebhluid', UtilsObj::escapeInputForHTML($resultArray['mawebhluid']));
		$smarty->assign('mawebhlbr', UtilsObj::escapeInputForHTML($resultArray['mawebhlbr']));

        $smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_NA', TPX_REGISTEREDTAXNUMBERTYPE_NA);
		$smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL', TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL);
		$smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE', TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE);

		$showTermsAndConditions = 0;

		// check to see if there is a new acctount terms and conditions template.
		// if no template path is returned then we do not show the terms and conditions section
		if ($smarty->getLocaleTemplate('newaccounttermsandconditions.tpl') != '')
		{
			$showTermsAndConditions = 1;
		}

		$smarty->assign('showtermsandconditions', $showTermsAndConditions);

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

		$smarty->assign('passwordstrengthmin', $gConstants['minpasswordscore']);

        if ($resultArray['ishighlevel'] == 1)
        {
            $fromRegisterLink = $resultArray['fromregisterlink'];

            if ($fromRegisterLink == 1)
            {
                $smarty->assign('cancelfsaction', 'OnlineAPI.hlRegisterDisplay');
            }
            else
            {
                $smarty->assign('cancelfsaction', 'OnlineAPI.hlSignInDisplay');
            }

            $smarty->assign('fromregisterlink', UtilsObj::escapeInputForHTML($fromRegisterLink));
        }
        else
        {   
            $smarty->assign('fromregisterlink', '');
            $smarty->assign('cancelfsaction', 'Welcome.initialize');
        }

        if ($resultArray['ismobile'] == 'true')
        {
            $smarty->assign('issmallscreen', 'true');
            $resultArray['template'] = $smarty->fetchLocale('newaccount_small.tpl');
            $resultArray['javascript'] = $smarty->fetchLocale('newaccount.tpl');
            echo json_encode($resultArray);
        }
        else
        {
            $smarty->assign('issmallscreen', 'false');
            $smarty->displayLocale('newaccount_large.tpl');
        }
    }

    static function createNewAccountLarge($resultArray)
    {
        global $gSession;
		global $gConstants;

        if ($resultArray['result'] == '')
        {
            // the account was created so log the user in
            self::processLogin($resultArray);
        }
        else
        {
            // the account wasn't created for some reason so refresh the page showing the error
            $smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

            // always show the standard header at the login page
            $smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));

            // load sidebar
            $smarty->assign('sidebarcontactdetails', $smarty->getLocaleTemplate('sidebarcontactdetails.tpl', ''));

            $smarty->assign('addresstitle', $smarty->get_config_vars('str_LabelContactInformation'));

            // Show the username input if the brand setting allows users to register with a username
            if ($resultArray['registerusingemail'] == TPX_REGISTER_USING_USERNAME)
            {
                $smarty->assign('showusernameinput', 1);
                $smarty->assign('usernamefieldid', 'login');
            }
            else
            {
                $smarty->assign('showusernameinput', 0);
                $smarty->assign('usernamefieldid', 'email');
            }

            $error = $resultArray['result'];

            if (substr($error, 0, 4) == 'str_')
            {
                SmartyObj::replaceParams($smarty, $error, $resultArray['resultparam']);
                $smarty->assign('error', $smarty->get_template_vars($error));
            }
            else
            {
                $smarty->assign('error', $error);
            }

            $smarty->assign('login', UtilsObj::escapeInputForJavaScript($resultArray['login']));
            $smarty->assign('contactfname', UtilsObj::escapeInputForJavaScript($resultArray['contactfname']));
            $smarty->assign('contactlname', UtilsObj::escapeInputForJavaScript($resultArray['contactlname']));
            $smarty->assign('companyname', UtilsObj::escapeInputForJavaScript($resultArray['companyname']));
            $smarty->assign('address1', UtilsObj::escapeInputForJavaScript($resultArray['address1']));
            $smarty->assign('address2', UtilsObj::escapeInputForJavaScript($resultArray['address2']));
            $smarty->assign('address3', UtilsObj::escapeInputForJavaScript($resultArray['address3']));
            $smarty->assign('address4', UtilsObj::escapeInputForJavaScript($resultArray['address4']));
            $smarty->assign('city', UtilsObj::escapeInputForJavaScript($resultArray['city']));
            $smarty->assign('state', UtilsObj::escapeInputForJavaScript($resultArray['state']));
            $smarty->assign('county', UtilsObj::escapeInputForJavaScript($resultArray['county']));
            $smarty->assign('regioncode', UtilsObj::escapeInputForJavaScript($resultArray['regioncode']));
            $smarty->assign('postcode', UtilsObj::escapeInputForJavaScript($resultArray['postcode']));
            $smarty->assign('country', UtilsObj::escapeInputForJavaScript($resultArray['countrycode']));
            $smarty->assign('sendmarketinginfo', UtilsObj::escapeInputForJavaScript((int) UtilsObj::getPOSTParam('sendmarketinginfo', 0)));

            $smarty->assign('ishighlevel', UtilsObj::escapeInputForHTML($resultArray['ishighlevel']));
			$smarty->assign('email', UtilsObj::escapeInputForHTML($resultArray['emailaddress']));
			$smarty->assign('telephonenumber', UtilsObj::escapeInputForHTML($resultArray['telephonenumber']));
            $smarty->assign('groupcode', UtilsObj::escapeInputForHTML($resultArray['groupcode']));
            $smarty->assign('registerfsaction', UtilsObj::escapeInputForHTML($resultArray['registerfsaction']));
            $smarty->assign('prtz', UtilsObj::escapeInputForHTML($resultArray['prtz']));
            $smarty->assign('mawebhluid', UtilsObj::escapeInputForHTML($resultArray['mawebhluid']));
            $smarty->assign('mawebhlbr', UtilsObj::escapeInputForHTML($resultArray['mawebhlbr']));

            $smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_NA', TPX_REGISTEREDTAXNUMBERTYPE_NA);
			$smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL', TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL);
			$smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE', TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE);

            if ($resultArray['sendmarketinginfo'] == 1)
            {
                $smarty->assign('sendmarketinginfochecked1', 'checked');
                $smarty->assign('sendmarketinginfochecked2', '');
            }
            else
            {
                $smarty->assign('sendmarketinginfochecked1', '');
                $smarty->assign('sendmarketinginfochecked2', 'checked');
            }
            $smarty->assign('session', $gSession['ref']);
            $smarty->assign('tablewidth', 650);
            $smarty->assign('strictmode', '1');

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
			$smarty->assign('passwordstrengthmin', $gConstants['minpasswordscore']);

			$smarty->assign('passwordstrengthmin', $gConstants['minpasswordscore']);

            $smarty->assign('issmallscreen', 'false');
            $smarty->displayLocale('newaccount_large.tpl');
        }
    }

    static function createNewAccountSmall($resultArray)
    {
        global $gSession;

        $return['error'] = '';

        if ($resultArray['result'] != '')
        {
            // the account wasn't created for some reason so refresh the page showing the error
            $smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

            $error = $resultArray['result'];
            if (substr($error, 0, 4) == 'str_')
            {
                SmartyObj::replaceParams($smarty, $error, $resultArray['resultparam']);
                $return['error'] = $smarty->get_template_vars($error);
            }
            else
            {
                $return['error'] = $error;
            }
        }

        echo json_encode($return);
    }

    static function initForgotPassword($resultArray, $error = '', $pInfoMessage = '', $pFromEmail = false)
    {
        global $gSession;
		global $gConstants;

        $smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        $smarty->assign('error', $error);
        $smarty->assign('info', $pInfoMessage);

		// Generate the class for the message area.
        if ('' == $pInfoMessage)
		{
			$smarty->assign('messageareaclass', 'message error');
		}
		else
		{
			$smarty->assign('messageareaclass', 'message confirmation warning');
		}

        // always show the standard page components
        $smarty->assign('footer', $smarty->getLocaleTemplate('footer.tpl', ''));
        $smarty->assign('sidebaradditionalinfo', $smarty->getLocaleTemplate('sidebaradditionalinfo_forgotpassword.tpl', ''));
        $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('sidebarleft_forgotpassword.tpl', ''));
        $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('sidebarleft_default.tpl', ''));
        $smarty->assign('sidebarcontactdetails', $smarty->getLocaleTemplate('sidebarcontactdetails_forgotpassword.tpl', ''));
        $smarty->assign('sidebarcontactdetails_default', $smarty->getLocaleTemplate('sidebarcontactdetails_default.tpl', ''));

		$smarty->assign('prtz', UtilsObj::escapeInputForHTML(UtilsObj::getPOSTParam('prtz', 0)));
        $smarty->assign('mawebhluid', UtilsObj::escapeInputForHTML(UtilsObj::getPOSTParam('mawebhluid', '')));
        $smarty->assign('mawebhlbr', UtilsObj::escapeInputForHTML(UtilsObj::getPOSTParam('mawebhlbr', '')));
        $smarty->assign('ishighlevel', UtilsObj::escapeInputForHTML(UtilsObj::getPOSTParam('ishighlevel', 0)));
        $smarty->assign('groupcode', UtilsObj::escapeInputForHTML(UtilsObj::getPOSTParam('groupcode', '')));

		$showBackButton = true;

		// if we are coming from the expired password resset link
		// we want to hide the back button as their nothing to go back to.
		if ($resultArray['passwordlinkexpired'] == true)
		{
			$showBackButton = false;
		}

		$smarty->assign('showbackbutton', $showBackButton);
		$smarty->assign('passwordlinkexpired', $resultArray['passwordlinkexpired']);
		$smarty->assign('passwordresetrequesttoken', $resultArray['passwordresetrequesttoken']);
        $smarty->assign('fromregisterlink', '');

		if (UtilsObj::getPOSTParam('ishighlevel') == 1)
		{
			$fromRegisterLink = UtilsObj::getPOSTParam('fromregisterlink', 0);

			if ($fromRegisterLink == 1)
			{
				$smarty->assign('cancelfsaction', 'OnlineAPI.hlRegisterDisplay');
			}
			else
			{
				$smarty->assign('cancelfsaction', 'OnlineAPI.hlSignInDisplay');
			}

			$smarty->assign('fromregisterlink', UtilsObj::escapeInputForHTML($fromRegisterLink));
		}
		else
		{
			$smarty->assign('cancelfsaction', 'Welcome.initialize');
		}

        $smarty->assign('loginval', UtilsObj::escapeInputForHTML($resultArray['login']));

        if ($resultArray['ismobile'] == 'true')
        {
            // include the system language selector
            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), true);
            $smarty->assign('systemlanguagelist', $languageHTMLList);
        
            // Include the correct header.
            $smarty->assign('header', $smarty->getLocaleTemplate('header_small.tpl', ''));

            if ($pFromEmail)
            {
				$smarty->assign('passwordresetdatabasetoken', $resultArray['passwordresetdatabasetoken']);

                $smarty->displayLocale('forgotpasswordfromemail_small.tpl');
            }
            else
            {
                $resultArray['template'] = $smarty->fetchLocale('forgotpassword_small.tpl');
                echo json_encode($resultArray);
            }
        }
        else
        {
            // include the system language selector
            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), false);
			$smarty->assign('systemlanguagelist', $languageHTMLList);

			if ($pFromEmail)
			{
				$smarty->assign('passwordresetdatabasetoken', $resultArray['passwordresetdatabasetoken']);
			}
			else
			{
				$smarty->assign('passwordresetdatabasetoken','');
			}
            
            // Include the correct header.
            $smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));

            $smarty->displayLocale('forgotpassword_large.tpl');
        }
    }

    static function resetPasswordRequestLarge($resultArray)
    {
        global $gSession;
        $errorResult = '';
        $infoResult = '';

		$smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		if ($resultArray['redirecturl'] != '')
		{
			// we have been provided with a redirect url from the external script
			header('Location: ' . $resultArray['redirecturl']);
        }
        else if ($resultArray['result'] == '')
        {
        	$smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));
			$smarty->assign('footer', $smarty->getLocaleTemplate('footer.tpl', ''));
			$smarty->assign('sidebaradditionalinfo', $smarty->getLocaleTemplate('sidebaradditionalinfo_forgotpassword.tpl', ''));
			$smarty->assign('sidebarleft', $smarty->getLocaleTemplate('sidebarleft_forgotpassword.tpl', ''));
			$smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('sidebarleft_default.tpl', ''));
			$smarty->assign('sidebarcontactdetails', $smarty->getLocaleTemplate('sidebarcontactdetails_forgotpassword.tpl', ''));
			$smarty->assign('sidebarcontactdetails_default', $smarty->getLocaleTemplate('sidebarcontactdetails_default.tpl', ''));
			$smarty->assign('resetpasswordauthcode', $resultArray['resetpasswordauthcode']);
            $smarty->displayLocale('forgotpasswordconfirmation_large.tpl');
        }
        else
        {
            if (substr($resultArray['result'], 0, 4) == 'str_')
            {
                SmartyObj::replaceParams($smarty, $resultArray['result'], $resultArray['resultparam']);
                $resultStr = $smarty->get_template_vars($resultArray['result']);
            }
            else
            {
                $resultStr = $resultArray['result'];
            }

            if ('str_ErrorResetPasswordMultipleAccounts' == $resultArray['result'])
            {
                $infoResult = $resultStr;
            }
            else
            {
                $errorResult = $resultStr;
            }

            self::initForgotPassword($resultArray, $errorResult, $infoResult);
        }
    }

    static function resetPasswordLinkExpired($pUseAjaxTemplate = false)
    {
    	global $gSession;

		$smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		// include the system language selector
        $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), $gSession['ismobile']);
        $smarty->assign('systemlanguagelist', $languageHTMLList);

		if ($gSession['ismobile'] == true)
        {
            $smarty->assign('header', $smarty->getLocaleTemplate('header_small.tpl', ''));

            if ($pUseAjaxTemplate)
			{
				 $return = array('error' => '', 'template' => '');
				 $return['template'] = $smarty->fetchLocale('forgotpasswordlinkexpiredajax_small.tpl');
				 echo json_encode($return);
			}
			else
			{
				$smarty->displayLocale('forgotpasswordlinkexpired_small.tpl');
			}
        }
        else
        {
            $smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));
            $smarty->assign('footer', $smarty->getLocaleTemplate('footer.tpl', ''));

            $smarty->assign('sidebaradditionalinfo', $smarty->getLocaleTemplate('sidebaradditionalinfo_login.tpl', ''));
            $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('sidebarleft_login.tpl', ''));
            $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('sidebarleft_default.tpl', ''));
            $smarty->assign('sidebarcontactdetails', $smarty->getLocaleTemplate('sidebarcontactdetails_forgotpassword.tpl', ''));
			$smarty->assign('sidebarcontactdetails_default', $smarty->getLocaleTemplate('sidebarcontactdetails_default.tpl', ''));

            $smarty->displayLocale('forgotpasswordlinkexpired_large.tpl');
        }

    }

    static function showResetPasswordForm($pRequestToken, $pResultArray, $pUseAjaxTemplate = false)
    {
		global $gSession;
        global $gConstants;

		$smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		// include the system language selector
        $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), $gSession['ismobile']);
        $smarty->assign('systemlanguagelist', $languageHTMLList);

		$smarty->assign('requesttoken', $pRequestToken);

		$error = $pResultArray['result'];
		if (substr($error, 0, 4) == 'str_')
		{
			SmartyObj::replaceParams($smarty, $error, $pResultArray['resultparam']);
			$result = $smarty->get_template_vars($error);
		}
		else
		{
			$result = $error;
		}

		$smarty->assign('footer', $smarty->getLocaleTemplate('footer.tpl', ''));
		$smarty->assign('error', $result);
		$smarty->assign('sidebaradditionalinfo', $smarty->getLocaleTemplate('sidebaradditionalinfo_login.tpl', ''));
		$smarty->assign('sidebarleft', $smarty->getLocaleTemplate('sidebarleft_login.tpl', ''));
		$smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('sidebarleft_default.tpl', ''));
		$smarty->assign('sidebarcontactdetails', $smarty->getLocaleTemplate('sidebarcontactdetails_forgotpassword.tpl', ''));
		$smarty->assign('sidebarcontactdetails_default', $smarty->getLocaleTemplate('sidebarcontactdetails_default.tpl', ''));

        $smarty->assign('passwordstrengthmin', $gConstants['minpasswordscore']);

		if ($gSession['ismobile'])
        {
            $smarty->assign('header', $smarty->getLocaleTemplate('header_small.tpl', ''));

            // if the result is empty then this is the first time we load the template on mobile
            // if the resukt is not empty then we must echo the error back to the mobile form
            if ($result == '')
            {
            	if ($pUseAjaxTemplate)
            	{
					 $return = array('error' => '', 'template' => '');
					 $return['template'] = $smarty->fetchLocale('resetpasswordformajax_small.tpl');
					 echo json_encode($return);
            	}
            	else
            	{
            		$smarty->displayLocale('resetpasswordform_small.tpl');
            	}
            }
            else
            {
            	$return = array('error' => $result, 'template' => '');
            	echo json_encode($return);
            }
        }
        else
        {
            $smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));
            $smarty->displayLocale('resetpasswordform_large.tpl');
        }
    }

    static function showResetPasswordAuthCodeForm($pRequestToken, $pResultArray)
    {
		global $gSession;
        global $gConstants;

		$smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		// include the system language selector
        $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), $gSession['ismobile']);
        $smarty->assign('systemlanguagelist', $languageHTMLList);

		$smarty->assign('requesttoken', $pRequestToken);

		$error = $pResultArray['result'];
		if (substr($error, 0, 4) == 'str_')
		{
			SmartyObj::replaceParams($smarty, $error, $pResultArray['resultparam']);
			$result = $smarty->get_template_vars($error);
		}
		else
		{
			$result = $error;
		}

		$smarty->assign('footer', $smarty->getLocaleTemplate('footer.tpl', ''));
		$smarty->assign('error', $result);
		$smarty->assign('sidebaradditionalinfo', $smarty->getLocaleTemplate('sidebaradditionalinfo_login.tpl', ''));
		$smarty->assign('sidebarleft', $smarty->getLocaleTemplate('sidebarleft_login.tpl', ''));
		$smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('sidebarleft_default.tpl', ''));
		$smarty->assign('sidebarcontactdetails', $smarty->getLocaleTemplate('sidebarcontactdetails_forgotpassword.tpl', ''));
		$smarty->assign('sidebarcontactdetails_default', $smarty->getLocaleTemplate('sidebarcontactdetails_default.tpl', ''));

        $smarty->assign('passwordstrengthmin', $gConstants['minpasswordscore']);

		if ($gSession['ismobile'])
        {
            $smarty->assign('header', $smarty->getLocaleTemplate('header_small.tpl', ''));

            // if the result is empty then this is the first time we load the template on mobile
            // if the resukt is not empty then we must echo the error back to the mobile form
            if ($result == '')
            {
            	$smarty->displayLocale('resetpasswordauthcodeform_small.tpl');
            }
            else
            {
            	$return = array('error' => $result, 'template' => '');
            	echo json_encode($return);
            }
        }
        else
        {
            $smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));
            $smarty->displayLocale('resetpasswordauthcodeform_large.tpl');
        }
    }

    static function resetPasswordRequestSmall($resultArray)
    {
        global $gSession;

        $return = array('error' => '', 'info' => '');

		$smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        if ($resultArray['result'] == '')
        {
            $smarty->assign('resetpasswordauthcode', $resultArray['resetpasswordauthcode']);
           	$return['redirecturl'] = $resultArray['redirecturl'];
            $return['template'] = $smarty->fetchLocale('forgotpasswordconfirmation_small.tpl');
        }
        else
        {
            $error = $resultArray['result'];
            if (substr($error, 0, 4) == 'str_')
            {
                SmartyObj::replaceParams($smarty, $error, $resultArray['resultparam']);
                $return['error'] = $smarty->get_template_vars($error);
            }
            else
            {
                $return['error'] = $error;
            }
        }

        echo json_encode($return);
    }

    static function showResetPasswordSuccess($pReturnInformation)
    {
		global $gSession;

		$smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		if ($gSession['ismobile'] == true)
        {
            $smarty->assign('header', $smarty->getLocaleTemplate('header_small.tpl', ''));
			$resultArray = array('error' => '', 'template' => '');
            $resultArray['template'] = $smarty->fetchLocale('resetpasswordconfirmation_small.tpl');
            echo json_encode($resultArray);
        }
        else
        {
            $smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));
            $smarty->assign('footer', $smarty->getLocaleTemplate('footer.tpl', ''));

            $smarty->assign('sidebaradditionalinfo', $smarty->getLocaleTemplate('sidebaradditionalinfo_login.tpl', ''));
            $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('sidebarleft_login.tpl', ''));
            $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('sidebarleft_default.tpl', ''));
            $smarty->assign('sidebarcontactdetails', $smarty->getLocaleTemplate('sidebarcontactdetails_forgotpassword.tpl', ''));
			$smarty->assign('sidebarcontactdetails_default', $smarty->getLocaleTemplate('sidebarcontactdetails_default.tpl', ''));
			$smarty->assign('returninformation', $pReturnInformation);

            $smarty->displayLocale('resetpasswordconfirmation_large.tpl');
        }
    }

    static function showResetPasswordAuthCodeLimitReached()
    {
		global $gSession;

		$smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		if ($gSession['ismobile'] == true)
        {
            $smarty->assign('header', $smarty->getLocaleTemplate('header_small.tpl', ''));
			$resultArray = array('error' => '', 'template' => '');
            $resultArray['template'] = $smarty->fetchLocale('resetpasswordauthcodefailureajax_small.tpl');
            echo json_encode($resultArray);
        }
        else
        {
            $smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));
            $smarty->assign('footer', $smarty->getLocaleTemplate('footer.tpl', ''));

            $smarty->assign('sidebaradditionalinfo', $smarty->getLocaleTemplate('sidebaradditionalinfo_login.tpl', ''));
            $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('sidebarleft_login.tpl', ''));
            $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('sidebarleft_default.tpl', ''));
            $smarty->assign('sidebarcontactdetails', $smarty->getLocaleTemplate('sidebarcontactdetails_forgotpassword.tpl', ''));
			$smarty->assign('sidebarcontactdetails_default', $smarty->getLocaleTemplate('sidebarcontactdetails_default.tpl', ''));

            $smarty->displayLocale('resetpasswordauthcodefailure_large.tpl');
        }
    }

    static function displaySSOError($pTitle, $pMessage, $pIsMobile = false)
    {
        global $gSession;

        $smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        $templatePostFix = '_large';

        $isMobile = $gSession['ismobile'];

        if (!$isMobile)
        {
            $isMobile = $pIsMobile;
        }

        if ($isMobile == true)
        {
            $templatePostFix = '_small';
        }

        $header = $smarty->getLocaleTemplate('header' . $templatePostFix . '.tpl', '');
        $customerinclude = $smarty->getLocaleTemplate('includes/customerinclude' . $templatePostFix . '.tpl', '');

        $smarty->assign('header', $header);
        $smarty->assign('customerinclude', $customerinclude);

        $smarty->assign('title', $smarty->get_config_vars('str_TitleError'));

        if ($pTitle == 'str_DatabaseError')
        {
            if ($pMessage != '')
            {
                $pMessage = str_replace('^0', $pTitle, $pMessage);
            }

            $smarty->assign('message', $pMessage);

        }
        else
        {
            if ($pTitle != '')
            {
                if (substr($pTitle, 0, 4) == 'str_')
                {
                    $smarty->assign('title', $smarty->get_config_vars($pTitle));
                }
                else
                {
                    $smarty->assign('title', $pTitle);
                }
            }

            if ($pMessage != '')
            {
                if (substr($pMessage, 0, 4) == 'str_')
                {
                    $smarty->assign('message', $smarty->get_config_vars($pMessage));
                }
                else
                {
                    $smarty->assign('message', $pMessage);
                }
            }
        }

        $smarty->displayLocale('ssoerror' . $templatePostFix  . '.tpl');
    }

    static function ajaxSSORedirect($pURL, $pReason)
    {
        global $gSession;

        $smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        $smarty->assign('url', $pURL);

        if ($pReason == TPX_USER_AUTH_REASON_HIGHLEVEL_ONLINE_PROJECT_CONTINUE_EDIT)
        {
            $smarty->assign('edittype', 0);
        }
        else
        {
            $smarty->assign('edittype', 1);
        }

        $smarty->displayLocale('ssoajaxredirect.tpl');
    }

    /**
     * Display the result of the update email action.
     * 
     * @param array $pUpdateResult result data of the update action.
     */
    static function updateEmailRequest($pUpdateResult)
    {
        global $gSession;

        $smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        // Include the system language selector.
        $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), $gSession['ismobile']);
        $smarty->assign('systemlanguagelist', $languageHTMLList);

        $emailUpdatedMessage = SmartyObj::getParamValueLocale('Login', $pUpdateResult['result'], $gSession['browserlanguagecode']);
        $emailUpdatedMessage = str_replace('^0', $pUpdateResult['newemail'], $emailUpdatedMessage);

        $smarty->assign('emailUpdatedMessage', $emailUpdatedMessage);
        $smarty->assign('action', 'Welcome.initialize');

        if ($gSession['ismobile'] == true)
        {
            // Display the small screen result page.
            $smarty->assign('header', $smarty->getLocaleTemplate('header_small.tpl', ''));

            $smarty->displayLocale('emailupdated_small.tpl');
        }
        else
        {
            // Display the large screen result page.
            $smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));
            $smarty->assign('footer', $smarty->getLocaleTemplate('footer.tpl', ''));

            $smarty->assign('sidebaradditionalinfo', $smarty->getLocaleTemplate('sidebaradditionalinfo_login.tpl', ''));
            $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('sidebarleft_login.tpl', ''));
            $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('sidebarleft_default.tpl', ''));
            $smarty->assign('sidebarcontactdetails', $smarty->getLocaleTemplate('sidebarcontactdetails_forgotpassword.tpl', ''));
            $smarty->assign('sidebarcontactdetails_default', $smarty->getLocaleTemplate('sidebarcontactdetails_default.tpl', ''));

            $smarty->displayLocale('emailupdated_large.tpl');
        }
    }
}
?>
