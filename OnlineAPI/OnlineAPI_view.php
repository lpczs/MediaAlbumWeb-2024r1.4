<?php
use Security\ControlCentreCSP;
class OnlineAPI_view
{
    static function returnResult($pErrorCode, $pBrowserLanguageCode)
    {
		if (($pErrorCode == TPX_ONLINE_ERROR_INACTIVELICENSEKEY) || ($pErrorCode == TPX_ONLINE_ERROR_PRODUCTNOTAVAILABLE)
			|| ($pErrorCode == TPX_ONLINE_ERROR_DEVICENOTSUPPOTRTED) || ($pErrorCode == TPX_ONLINE_ERROR_HIGHLEVELAUTH)
            || $pErrorCode == TPX_ONLINE_ERROR_PRODUCT_CONFIGURATION)
		{
			global $gSession;

			$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname'], $pBrowserLanguageCode);
        	$smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));
            $smarty->assign('message', $smarty->get_config_vars(match ($pErrorCode) {
                TPX_ONLINE_ERROR_PRODUCTNOTAVAILABLE, TPX_ONLINE_ERROR_PRODUCT_CONFIGURATION => 'str_ErrorProductNotAvailable',
                TPX_ONLINE_ERROR_DEVICENOTSUPPOTRTED => 'str_ErrorDeviceNotSupported',
                TPX_ONLINE_ERROR_HIGHLEVELAUTH => 'str_MessageAuthMode_AuthKey',
                default => 'str_ErrorLicenseKeyInactive'
            }));

        	$smarty->displayLocale('productnotavailable.tpl');
		}
		else
		{
			self::communicationError();
		}
    }

    static function redirect($pURL)
    {
        header("Location: " . $pURL);
    }

	static function returnResultAPI($pResultArray, $pBrowserLanguageCode, $pBasketWorkFlowType, $pWebBrandCode)
	{
		global $gSession;

        if (array_key_exists('result', $pResultArray))
        {
            switch ($pResultArray['result'])
            {
                case TPX_ONLINE_ERROR_DATABASE:
                    $smarty = SmartyObj::newSmarty('', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = str_replace("'^0'", "", $smarty->get_config_vars('str_DatabaseError'));
                break;
                case TPX_ONLINE_ERROR_PRODUCTNOTAVAILABLE:
                    $smarty = SmartyObj::newSmarty('Order', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorProductNotAvailable');
                break;
                case TPX_ONLINE_ERROR_DEVICENOTSUPPOTRTED:
                    $smarty = SmartyObj::newSmarty('Order', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorDeviceNotSupported');
                break;
                case TPX_ONLINE_ERROR_BROWSERNOTSUPPORTED:
                    $smarty = SmartyObj::newSmarty('BrowserSupport', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_titleBrowserNotSupported');
                break;
                case TPX_ONLINE_ERROR_INACTIVELICENSEKEY:
                    $smarty = SmartyObj::newSmarty('Order', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorLicenseKeyInactive');
                break;
                case TPX_ONLINE_ERROR_INVALIDDEVICEDETECTIONDATA:
                    $smarty = SmartyObj::newSmarty('Order', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorDeviceDetectionData');
                break;
                case TPX_ONLINE_ERROR_PROJECTDOESNOTEXIST:
                    $smarty = SmartyObj::newSmarty('Customer', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorProjectHasBeenDeleted');
                break;
                case TPX_ONLINE_ERROR_PROJECTALREADYOPEN:
                    $smarty = SmartyObj::newSmarty('Order', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_WarningTerminateOtherSession');
                break;
                case TPX_ONLINE_ERROR_PROJECTINPRODUCTION:
                    $smarty = SmartyObj::newSmarty('Customer', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorOrderInProduction');
                break;
                case TPX_ONLINE_ERROR_PROJECTLOCKED:
                    $smarty = SmartyObj::newSmarty('Customer', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorProjectLocked');
                break;
                case TPX_ONLINE_ERROR_PROJECTNAMEALREADYEXISTS:
                    $smarty = SmartyObj::newSmarty('Customer', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_MessageProjectNameAlreadyExists');
                break;
                case TPX_ONLINE_ERROR_PROJECTNAMECANNOTBEEMPTY:
                    $smarty = SmartyObj::newSmarty('Customer', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorNoProjectName');
                break;
                case TPX_ONLINE_ERROR_PROJECTREFLIMIT:
                    $smarty = SmartyObj::newSmarty('Customer', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorProjectRefLimitReached');
                break;
                case TPX_ONLINE_ERROR_INVALIDPARAMETER:
                    $smarty = SmartyObj::newSmarty('Customer', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorInvalidParameter');
                break;
                case TPX_ONLINE_ERROR_DEVICEINCOMPATIBILITY:
                    $smarty = SmartyObj::newSmarty('Customer', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorDeviceCompatibilityIssue');
                break;
                case TPX_ONLINE_ERROR_HIGHLEVELNOTENABLED:
                    $smarty = SmartyObj::newSmarty('Customer', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorMultiLineBasketAPINotEnabled');
                break;
                case TPX_ONLINE_ERROR_INVALIDUSERID:
                    $smarty = SmartyObj::newSmarty('Customer', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorInvalidUser');
                break;
                case TPX_ONLINE_ERROR_EMPTYGROUPCODE:
                    $smarty = SmartyObj::newSmarty('Login', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorEmptyGroupCode');
                break;
                case TPX_ONLINE_ERROR_ACCOUNT_MISTMATCH:
                    $smarty = SmartyObj::newSmarty('Login', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorAccountMisMatch');
                break;
                case TPX_ONLINE_ERROR_ACCOUNTTASKNOTALLOWED:
                    $smarty = SmartyObj::newSmarty('Customer', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorAccountTaskNotAllowed');
                break;
                case TPX_ONLINE_ERROR_COMMUNICATION_FAILED:
                    $smarty = SmartyObj::newSmarty('CommunicationFailed', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_MessageCommunicationFailed');
                break;
                case TPX_ONLINE_ERROR_RESTORE_FAILED:
                    $smarty = SmartyObj::newSmarty('Customer', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_MessageUnableToRestoreProject');
                break;
                case TPX_ONLINE_ERROR_HIGHLEVELPROJECTACTIVECHECKOUTSESSION:
                    $smarty = SmartyObj::newSmarty('', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_WarningProjectOpenInShoppingCart');
                break;
                case TPX_ONLINE_ERROR_HIGHLEVELINTERNALERROR:
                    $smarty = SmartyObj::newSmarty('CommunicationFailed', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_MessageCommunicationFailed');
                break;
                case TPX_ONLINE_ERROR_HIGHLEVELBASKETEXPIRED:
                    $smarty = SmartyObj::newSmarty('Login', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorLoginHasExpired');
                break;
                case TPX_ONLINE_ERROR_HIGHLEVELSESSIONEXPIRED:
                    $smarty = SmartyObj::newSmarty('Login', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorLoginHasExpired');
                break;
                case TPX_ONLINE_ERROR_HIGHLEVELBASKETEMPTY:
                    $smarty = SmartyObj::newSmarty('Order', '', '', $pBrowserLanguageCode);
                    $pResultArray['resultmessage'] = $smarty->get_config_vars('str_ErrorEmptyBasket');
                break;
            }

            if (!empty($pResultArray['html']))
            {
                switch ($pResultArray['html'])
                {
                    case 'projectrow':
                    {
						// Check is CSP is active.
						$cspActive = UtilsObj::getCSPActive();

						if (($pResultArray['projectpreviewthumbnail'] !== '') && ($cspActive))
						{
							// Add the project preview thumbnail domain to the CSP rules.
							$cspBuilder = ControlCentreCSP::getInstance(UtilsObj::getGlobalValue('ac_config'));

							// Add the unique domain to the list for CSP.
							$parsedUrl = parse_url($pResultArray['projectpreviewthumbnail']);
							$domain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
							$cspBuilder->getBuilder()->addSource('image-src', $domain);
						}

                        $projectName = $pResultArray['projectname'];
                        $pResultArray['name'] = $projectName;
                        unset($pResultArray['projectname']);

                        $smarty = SmartyObj::newSmarty('Customer', '', '', $pBrowserLanguageCode);
                        $smarty->assign('onlinedesignerurl', $pResultArray['onlinedesignerurl']);
                        $smarty->assign('project', $pResultArray);
                        $smarty->assign('panelclass', 'hidden');

                        $pResultArray['html'] = $smarty->fetchLocale("customer/projectlist_row.tpl");

                        break;
                    }
                }
            }
        }

        self::returnJSON($pResultArray, $pBasketWorkFlowType, $pWebBrandCode);
	}

	static function displayLogin($pResultArray)
	{
		global $ac_config;
        global $gSession;

        $smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        $smarty->assign('resetpasswordenabled', $ac_config['RESETPASSWORDENABLED'] == 1);
        $smarty->assign('error', $pResultArray['error']);
        $smarty->assign('info', '');
        $smarty->assign('loginVal', UtilsObj::escapeInputForHTML($pResultArray['login']));

		// Generate the class to use by the message area.
        $smarty->assign('messageareaclass', 'message warning');

		// Generate the error dialog box content to be displayed.
		$smarty->assign('dialogMessage', "<p>" . $pResultArray['error'] . "</p>");

		// if we have a reference number equal to 0 then the user has selected the register for high level api
        if ($pResultArray['ref'] == 0)
        {
            if ($pResultArray['cancreateaccounts'] == 1)
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

        $smarty->assign('canCreateAccounts', $pResultArray['cancreateaccounts']);
        $smarty->assign('loginfsaction', 'OnlineAPI.processLogin');

        if ($pResultArray['fromregisterlink'])
        {
        	 $smarty->assign('fromregisterlink', '1');
        	 $smarty->assign('changelanguageinitfsaction', 'OnlineAPI.hlRegisterDisplay');
        }
        else
        {
			$smarty->assign('fromregisterlink', '0');
			$smarty->assign('changelanguageinitfsaction', 'OnlineAPI.hlSignInDisplay');
        }

        // always show the standard header at the login page
        $smarty->assign('ishighlevel', 1);
        $smarty->assign('groupcode', UtilsObj::escapeInputForHTML($pResultArray['groupcode']));

		$languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), $gSession['ismobile']);
		$smarty->assign('systemlanguagelist', $languageHTMLList);

		// Prevent the browser caching the page, so the correct language is displayed.
        $smarty->cachePage = false;

        if ($gSession['ismobile'])
        {
			// Configure the small screen version of the template.
			$templateSuffix = 'small';

			$smarty->assign('prtz', UtilsObj::escapeInputForJavaScript($pResultArray['prtz']));
			$smarty->assign('mawebhluid', UtilsObj::escapeInputForJavaScript($pResultArray['mawebhluid']));
			$smarty->assign('mawebhlbr', UtilsObj::escapeInputForJavaScript($pResultArray['mawebhlbr']));

            $smarty->assign('registerfsaction', 'OnlineAPI.createNewAccountSmall');

			$smarty->assign('groupcode_script', UtilsObj::escapeInputForJavaScript($pResultArray['groupcode']));
        }
        else
        {
			// Configure the large screen version of the template.
			$templateSuffix = 'large';

			$smarty->assign('prtz', UtilsObj::escapeInputForHTML($pResultArray['prtz']));
			$smarty->assign('mawebhluid', UtilsObj::escapeInputForHTML($pResultArray['mawebhluid']));
			$smarty->assign('mawebhlbr', UtilsObj::escapeInputForHTML($pResultArray['mawebhlbr']));

            $smarty->assign('registerfsaction', 'OnlineAPI.createNewAccountLarge');

            $smarty->assign('sidebaradditionalinfo', $smarty->getLocaleTemplate('sidebaradditionalinfo_login.tpl', ''));
            $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('sidebarleft_login.tpl', ''));
            $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('sidebarleft_default.tpl', ''));
        }

		// Display the template.
		$smarty->assign('header', $smarty->getLocaleTemplate('header_' . $templateSuffix . '.tpl', ''));
        $smarty->assign('footer', $smarty->getLocaleTemplate('footer.tpl', ''));

		$smarty->displayLocale('login_' . $templateSuffix . '.tpl');
	}

	static function processLogin($pResultArray)
	{
		global $gSession;

		$error = $pResultArray['result'];
		if (substr($error, 0, 4) == 'str_')
		{
			$smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
			SmartyObj::replaceParams($smarty, $error, $pResultArray['resultparam']);
			$error = $smarty->get_template_vars($pResultArray['result']);
		}

		$pResultArray['error'] = $error;

		self::displayLogin($pResultArray);
	}

	static function onlineDesignerLogout($pResultArray)
	{
        self::returnEncryptedData($pResultArray);
	}

	static function redirectToURL($pURL)
	{
		header('Location: ' . $pURL);
	}

	static function broswerNotSupported($pReturnArray)
	{
		global $gSession;

		$smarty = SmartyObj::newSmarty('BrowserSupport', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
        $smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));

        $smarty->assign('hidesafari', $pReturnArray['hidesafari']);
        $smarty->assign('hidechrome', $pReturnArray['hidechrome']);
        $smarty->assign('hidefirefox', $pReturnArray['hidefirefox']);
        $smarty->assign('hideedge', $pReturnArray['hideedge']);

        $smarty->assign('hidesafaridownload', $pReturnArray['hidesafaridownload']);
        $smarty->assign('hideedgedownload', $pReturnArray['hideedgedownload']);
        $smarty->assign('hidechromedownload', $pReturnArray['hidechromedownload']);
        $smarty->assign('hidefirefoxdownload', $pReturnArray['hidefirefoxdownload']);
        $smarty->assign('hideedgedownload', $pReturnArray['hideedgedownload']);

        $minVersionMessage = $smarty->get_config_vars('str_ErrorMinBrowserVersion');

		$safariMinVersion = TPX_ONLINESUPPORTED_BROWSER_VERSION_SAFARI;

		// If on small screen, display the iOS version.
		if ($gSession['ismobile'])
		{
			$safariMinVersion = TPX_ONLINESUPPORTED_IOS_VERSION;
		}

        $safariMinVersionMessage = str_replace("^0", $safariMinVersion, $minVersionMessage);
        $ieMinVersionMessage = str_replace("^0", TPX_ONLINESUPPORTED_BROWSER_VERSION_INTERNETEXPLORER, $minVersionMessage);
        $chromeMinVersionMessage = str_replace("^0", TPX_ONLINESUPPORTED_BROWSER_VERSION_CHROME, $minVersionMessage);
        $ffMinVersionMessage = str_replace("^0", TPX_ONLINESUPPORTED_BROWSER_VERSION_FIREFOX, $minVersionMessage);

        $smarty->assign('minsafari', $safariMinVersionMessage);
        $smarty->assign('minie', $ieMinVersionMessage);
        $smarty->assign('minchrome', $chromeMinVersionMessage);
        $smarty->assign('minff', $ffMinVersionMessage);

        $smarty->displayLocale('browsernotsupported.tpl');
	}


    static function communicationError()
    {
        global $gSession;

        $smarty = SmartyObj::newSmarty('CommunicationFailed', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
        $smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));

        $smarty->displayLocale('communicationerror.tpl');
    }

    static function returnJSON($pReturnArray, $pBasketWorkFlowType, $pWebBrandCode)
    {
        ob_start();

        if ($pBasketWorkFlowType == TPX_BASKETWORKFLOWTYPE_HIGHLEVELAPI)
        {
        	header('Access-Control-Allow-Origin: ' . UtilsObj::getAllowedOriginForHighLevelBasketAPI($pWebBrandCode));
        	header('Access-Control-Allow-Credentials: true');
        }

        header('Content-Type: application/json');
        echo json_encode($pReturnArray, JSON_UNESCAPED_SLASHES);
        ob_end_flush();
    }

	static function createNewAccountFromOnline($pResultArray)
    {
        global $gSession;

       	if ($pResultArray['result'] != '')
        {
            $error = $pResultArray['result'];
            if (substr($error, 0, 4) == 'str_')
            {
                $smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
                SmartyObj::replaceParams($smarty, $error, $pResultArray['resultparam']);
                $pResultArray['result'] = $smarty->get_template_vars($error);
            }
            else
            {
                $pResultArray['result'] = $error;
            }
        }

		self::returnEncryptedData($pResultArray);
    }

	static function resetPasswordFromOnline($pResultArray)
    {
        global $gSession;

        if ($pResultArray['result'] == '')
        {
            if ($pResultArray['redirecturl'] != '')
            {
            	$pResultArray['info'] = SmartyObj::getParamValue('Login', 'str_MessageExternalResetPassword');
        		$pResultArray['note'] = '';
        		$pResultArray['authcodemessage'] = '';
            }
            else
            {
            	$pResultArray['info'] = SmartyObj::getParamValue('Login', 'str_ConfirmationResetPassword');
        		$pResultArray['note'] = SmartyObj::getParamValue('Login', 'str_ConfirmationResetMessage');
        		$pResultArray['authcodemessage'] = SmartyObj::getParamValue('Login', 'str_ResetAuthCodeMessage');
            }

            $pResultArray['redirecturl'] = $pResultArray['redirecturl'];
        }
        else
        {
            $error = $pResultArray['result'];
            if (substr($error, 0, 4) == 'str_')
            {
                $smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
                SmartyObj::replaceParams($smarty, $error, $pResultArray['resultparam']);
                $pResultArray['result'] = $smarty->get_template_vars($error);
            }
            else
            {
                $pResultArray['result'] = $error;
            }
        }

		self::returnEncryptedData($pResultArray);
    }

	static function processOnlineLogin($pResultArray)
    {
        global $gSession;

		$error = $pResultArray['result'];
		$hasErrorString = (substr($error, 0, 4) == 'str_');

		if (substr($error, 0, 4) == 'str_')
		{
			$smarty = SmartyObj::newSmarty('Login', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
            SmartyObj::replaceParams($smarty, $error, $pResultArray['resultparam']);
            $pResultArray['result'] = $smarty->get_template_vars($error);
        }

        self::returnEncryptedData($pResultArray);
    }

    static function returnShareURL($pResultArray)
    {
        self::returnEncryptedData($pResultArray);
    }

    static function processOnlineOrder($pResultArray)
    {
		global $ac_config;

        $apiVersion = $pResultArray['apiversion'];
        $languageCode = $pResultArray['languageCode'];

        $outputString = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';

        switch ($pResultArray['result'])
        {
            case 'ORDER':
                $outputString .= 'ORDER<br>';
                $outputString .= '1<br>' . $pResultArray['shoppingcarturl'];
                $outputString .= '<br>' . $pResultArray['ref'];

                $uploadRefs = '';
                $items = $pResultArray['items'];
                $itemCount = count($items);
                for ($i = 0; $i < $itemCount; $i++)
                {
                    $uploadRefs .= $items[$i]['uploadref'] . ',';
                }
                $uploadRefs = substr($uploadRefs, 0, strlen($uploadRefs) - 1);

                $outputString .= '<br>' . $uploadRefs;
                $outputString .= '<br>' . $pResultArray['statusurl'];
                $outputString .= '<br>' . json_encode($pResultArray['shoppingcartdata']);
				$outputString .= '<br><br><br><br>';
                break;
            case 'ORDERCANCELCONFIRM':
            case 'ORDERCANCELLED':
            case 'ORDERCOMPLETED':
            case 'CANCEL':
                $outputString .= $pResultArray['result'] . '<br>';
                $outputString .= '1<br><br><br><br><br><br><br><br><br><br>';
                break;
            case 'UPLOAD':
                $outputString .= 'UPLOAD<br>';
                $orderedItemsArray = $pResultArray['items'];
                $itemCount = count($orderedItemsArray);

                // if the apiversion is 4 or higher then this is taopix designer version 3.1 and later
                // in this situation we return information on all items within the order
                if ($apiVersion >= 4)
                {
                    $outputString .= '2<br>';
                }
                else
                {
                    $outputString .= '1<br>';
                }

                $outputString .= $orderedItemsArray[0]['ordernumber'] . '<br>';
                $outputString .= $ac_config['FTPURL'] . '<br>';
                $outputString .= $ac_config['FTPUSER'] . '<br>';
                $outputString .= $ac_config['FTPPASS'] . '<br>';
                $outputString .= UtilsObj::correctPath($ac_config['FTPORDERSROOTPATH']) . '<br>';
                if ($ac_config['FTPGROUPORDERSBYCODE'] == '1')
                {
                    $outputString .= 'TRUE<br>';
                }
                else
                {
                    $outputString .= 'FALSE<br>';
                }

                if ($apiVersion < 4)
                {
                    $outputString .= $orderedItemsArray[0]['outputdeliverymethods'] . '<br><br><br>';
                }
                else
                {
                    $uploadRefs = '';
                    $outputDeliveryMethods = '';
                    $canUpload = '';
                    $saveOverride = '';
                    for ($i = 0; $i < $itemCount; $i++)
                    {
                        $itemArray = $orderedItemsArray[$i];

                        $uploadRefs .= $itemArray['uploadref'] . ',';
                        $outputDeliveryMethods .= $itemArray['outputdeliverymethods'] . ',';

                        if ($itemArray['canupload'] == '1')
                        {
                            $canUpload .= 'TRUE,';
                        }
                        else
                        {
                            $canUpload .= 'FALSE,';
                        }

                        if ($itemArray['canuploadenablesaveoverride'] == '1')
                        {
                            $saveOverride .= 'TRUE,';
                        }
                        else
                        {
                            $saveOverride .= 'FALSE,';
                        }
                    }

                    // append the save override onto the end of the upload status
                    $canUpload .= '<br>' . $saveOverride;

                    $uploadRefs = substr($uploadRefs, 0, strlen($uploadRefs) - 1);
                    $outputDeliveryMethods = substr($outputDeliveryMethods, 0, strlen($outputDeliveryMethods) - 1);
                    $canUpload = substr($canUpload, 0, strlen($canUpload) - 1);

                    $outputString .= $uploadRefs . '<br>';
                    $outputString .= $outputDeliveryMethods . '<br>';
                    $outputString .= $canUpload . '<br>';
                }

                break;
            case 'PRODUCTION':
            case 'PRODUCTCODEMISMATCH':
            case 'COVERPAGECOUNTMISMATCH':
            case 'PAPERPAGECOUNTMISMATCH':
            case 'INACTIVEPRODUCT':
            case 'WAITINGFORPAYMENT':
			case 'CUSTOMERROR':
                $smarty = SmartyObj::newSmarty('Order', '', '', $languageCode, false, false);

                $outputString .= 'MESSAGE<br>1<br>';
                $outputString .= $smarty->get_config_vars('str_Error') . '...<br>';
                $outputString .= $smarty->get_config_vars('str_Error') . '.<br>';

                switch ($pResultArray['result'])
                {
                    case 'PRODUCTION':
                    {
                        $outputString .= $smarty->get_config_vars('str_ErrorCannotUploadOrder') . '<br>';
                        break;
                    }
                    case 'PRODUCTCODEMISMATCH':
                    {
                        $outputString .= $smarty->get_config_vars('str_ErrorProductCodeMismatch') . '<br>';
                        break;
                    }
                    case 'COVERPAGECOUNTMISMATCH':
                    {
                        $outputString .= self::replaceParams($smarty->get_config_vars('str_ErrorCoverPageCountMismatch'), $pResultArray['str_ErrorCoverPageCountMismatch']) . '<br>';
                        break;
                    }
                    case 'PAPERPAGECOUNTMISMATCH':
                    {
                        if ($pResultArray['papermaxpagecount'] == 1)
                        {
                            $outputString .= $smarty->get_config_vars('str_ErrorPaperPageCountMismatch') . '<br>';
                        }
                        else
                        {
                            $outputString .= self::replaceParams($smarty->get_config_vars('str_ErrorPaperPageCountMismatch2'), $pResultArray['papermaxpagecount']) . '<br>';
                        }
                        break;
                    }
                    case 'INACTIVEPRODUCT':
                    {
                        $msg = $smarty->getConfigVars('str_ErrorProductNotAvailable2');

                        $msg = str_replace(['^0', '^1'], [$pResultArray['inactiveproductcollectioncode'], $pResultArray['inactiveproductcollectioncode']], $msg);


                        $outputString .= $msg . '<br>';

                        break;
                    }
                    case 'WAITINGFORPAYMENT':
                    {
                        $outputString .= $smarty->get_config_vars('str_ErrorCannotUploadProject') . '<br>';
                        break;
                    }
                    case 'CUSTOMERROR':
                    {
                        $outputString .= $pResultArray['resultparam'] . '<br>';
                        break;
                    }
                }

                $outputString .= '<br><br><br><br><br><br>';

                break;
            default:
				// an error occurred

                // calendar components could return this error as well as high level when exceeding the cart size
                // trying to checkout via the Taopix cart from the e-commerce api as a guest is also not supported.
                if (($pResultArray['result'] == 'str_ErrorNoComponent') || ($pResultArray['result'] == 'str_MessageShoppingCartFull') || ($pResultArray['result'] == 'str_ErrorGuestCheckoutNotSupported'))
                {
					$webBrandCode = UtilsObj::getArrayParam($pResultArray, 'webbrandcode', '');

                    $smarty = SmartyObj::newSmarty('Order', $webBrandCode, '', $languageCode, false, false);
                }
                else
                {
                    $smarty = SmartyObj::newSmarty('AppAPI', '', '', $languageCode, false, false);
                }

                $resultParam1 = self::replaceParams($smarty->get_config_vars($pResultArray['result']), $pResultArray['resultparam']);

                if (($pResultArray['result'] == 'str_ErrorNoComponent') || ($pResultArray['result'] == 'str_MessageShoppingCartFull'))
                {
                    // no need for the \n when it is being returned to either desktop or online
                    $resultParam1 = str_replace('\n', " ", $resultParam1);
                }

                $outputString .= 'ERROR<br>1<br>' . $resultParam1 . '<br><br><br><br><br><br><br><br><br>';


                break;
        }

        $outputString .= '</body></html>';

        self::returnEncryptedData($outputString);
    }

    static function replaceParams($pString, $pParam1, $pEncode = false)
    {
        $text = str_replace('^0', $pParam1, $pString);

        if ($pEncode)
        {
            $text = UtilsObj::encodeString($text, true);
        }

        return $text;
    }

    static function ccNotificationDispatcher($pResult)
    {
        self::returnEncryptedData($pResult);
    }

    static function returnEncryptedData($pDataArray)
    {
        $systemConfigArray = DatabaseObj::getSystemConfig();

        echo UtilsObj::encryptData(serialize($pDataArray), $systemConfigArray['secret'], false);
    }

    static function usersProjectList($pDataArray, $pMode, $pWebBrandCode)
    {
		// Determine if the Content Security Policy is active.
		$cspActive = UtilsObj::getCSPActive();

		if ($cspActive)
		{
			$cspPreviewThumbnailCache = [];

			foreach ($pDataArray['projects'] as $pProject)
			{
				if ($pProject['projectpreviewthumbnail'] !== '')
				{
					// Add the unique domain to the list for CSP.
					$parsedUrl = parse_url($pProject['projectpreviewthumbnail']);
					$cspPreviewThumbnailCache[$parsedUrl['scheme'] . '://' . $parsedUrl['host']] = true;
				}
			}

			// Add the project preview thumbnail domain to the CSP rules.
			$cspBuilder = ControlCentreCSP::getInstance(UtilsObj::getGlobalValue('ac_config'));

			if (count($cspPreviewThumbnailCache) > 0)
			{

				array_map(function($pThumbnailDomain) use ($cspBuilder)
				{
					$cspBuilder->getBuilder()->addSource('image-src', $pThumbnailDomain);
				}, array_keys($cspPreviewThumbnailCache));
			}

			// Add unsafe-eval, this allows handlebars to work without precompiling all the templates.
			$cspBuilder->getBuilder()->setUnsafeEvalAllowed('script-src', true);
			$cspBuilder->getBuilder()->setStrictDynamic('script-src', false);
		}

        $smarty = SmartyObj::newSmarty('Customer', $pWebBrandCode, '', $pDataArray['languagecode']);

        $smarty->assign('projects', $pDataArray['projects']);
        $smarty->assign('onlinedesignerurl', $pDataArray['onlinedesignerurl']);
        $smarty->assign('returntext', LocalizationObj::getLocaleString($pDataArray['returntext'], $pDataArray['languagecode'],true));
        $smarty->assign('languagecode', $pDataArray['languagecode']);
        $smarty->assign('systemerror', $pDataArray['result'] != TPX_ONLINE_ERROR_NONE);
        $smarty->assign('invaliduser', $pDataArray['result'] == TPX_ONLINE_ERROR_INVALIDUSERID);
        $smarty->assign('panelclass', '');

        $smarty->auto_literal = true;
        // need to modify the braces due to conflicts with handlebars.js
        $smarty->setLiterals(array('{{{','}}}','{{','}}'));

        // Find out the path of the projectlist_row template. This could be in Customise or the web brand root.
        $templateInfo = $smarty->getLocaleTemplateInfo('customer/projectlist_row.tpl', $pDataArray['languagecode']);

        // Set the template to the projectrowtemplate
        $smarty->assign('projectrowtemplate', $templateInfo['template']);

		$templateExtra = [
			'template' => '',
		];

		if ($pDataArray['template'] !== '')
		{
			// Find out the path of the projectlist_row template. This could be in Customise or the web brand root.
        	$templateExtra = $smarty->getLocaleTemplateInfo($pDataArray['template'], $pDataArray['languagecode']);
		}

        // Set the extra template params.
        $smarty->assign('extratemplate', $templateExtra['template']);
		$smarty->assign('templateparams', $pDataArray['templateparams']);

        $smarty->displayLocale('customer/projectlist.tpl');

    }
}

?>
