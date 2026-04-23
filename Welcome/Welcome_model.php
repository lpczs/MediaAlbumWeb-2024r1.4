<?php
require_once('../Utils/UtilsDeviceDetection.php');

class Welcome_model
{
    static function displayLogin()
    {
        global $gSession;

		$resultArray = Array();
		$result = '';
        $recordID = 0;
		$info = '';
		$info2 = '';
        $canCreateAccounts = 0;
		$ssoResultArray = Array();
        $login = '';

        if (array_key_exists('ref', $_REQUEST))
        {
			// we have a session identifier so make sure we have what appears to be a valid session
            $recordID = (int) $_REQUEST['ref'];
            if (($recordID > 0) && ($gSession['ref'] > 0))
            {
				// perform a single sign-on check
				$ssoResultArray = AuthenticateObj::authenticateLogin(TPX_USER_AUTH_REASON_WEB_INIT, $recordID, true, UtilsObj::getBrowserLocale(),
						$gSession['webbrandcode'], $gSession['licensekeydata']['groupcode'], '', '', TPX_PASSWORDFORMAT_CLEARTEXT, '', true, true,
						true, $gSession['userdata']['ssotoken'], $gSession['userdata']['ssoprivatedata'], array());

				// check the single sign-on result
				if (($ssoResultArray['result'] == '') && ($ssoResultArray['useraccountid'] > 0))
				{
					$result = 'LOGIN';
				}
				elseif ($ssoResultArray['result'] == 'SSOREDIRECT')
				{
					$result = 'SSOREDIRECT';
				}
				else
				{
					$info = $ssoResultArray['result'];
					$info2 = $ssoResultArray['resultparam'];
                    $login = UtilsObj::getPOSTParam('login');
				}

				// use the can create account value retrieved from the single sign-on test
				$canCreateAccounts = $ssoResultArray['cancreateaccounts'];
            }
        }
        else
        {
			// no session provided
            $recordID = -1;

			// perform a single sign-on check
            $ssoResultArray = AuthenticateObj::authenticateLogin(TPX_USER_AUTH_REASON_WEB_INIT, $recordID, false, UtilsObj::getBrowserLocale(),
					$gSession['webbrandcode'], '', '', '', TPX_PASSWORDFORMAT_CLEARTEXT, '', true, true, true, '', array(), array());

            // check the single sign-on result
			if (($ssoResultArray['result'] == '') && ($ssoResultArray['useraccountid'] > 0))
			{
				$result = 'LOGIN';
			}
			elseif ($ssoResultArray['result'] == 'SSOREDIRECT')
			{
				$result = 'SSOREDIRECT';
			}
			else
			{
				$info = $ssoResultArray['result'];
				$info2 = $ssoResultArray['resultparam'];
                $login = UtilsObj::getPOSTParam('login');
			}
        }

		if (substr($info, 0, 4) == 'str_')
		{
			$info = SmartyObj::getParamValue('Login', $info);
		}

		if (substr($info2, 0, 4) == 'str_')
		{
			$info2 = SmartyObj::getParamValue('Login', $info2);
		}

		$resultArray['result'] = $result;
        $resultArray['ref'] = $recordID;
        $resultArray['info'] = $info;
		$resultArray['info2'] = $info2;
        $resultArray['cancreateaccounts'] = $canCreateAccounts;
		$resultArray['ssodataarray'] = $ssoResultArray;
        $resultArray['login'] = UtilsObj::getPOSTParam('login');
		$resultArray['ishighlevel'] = 0;
		$resultArray['prtz'] = 0;
		$resultArray['mawebhluid'] = '';
		$resultArray['mawebhlbr'] = '';

        UtilsObj::setSessionDeviceData();

        DatabaseObj::updateSession();

        return $resultArray;
    }

	/*
	* Performs user login.
	*
	* Authenticates an user account against the database. If authentication was successful
	* then also starts the session.
	*
	* @version 3.5.0
	* @since 1.0.0
	* @author Kevin Gale
	*/
    static function processLogin($pFromOnlineDesigner, $pIsMobile)
    {
        return AuthenticateObj::processLogin($pFromOnlineDesigner, $pIsMobile);
    }

    static function processLogout($pReason)
    {
		global $gConstants;
        global $gSession;

		$redirectURL = '';
		$ssoToken = '';
		$ssoPrivateDataArray = Array();
        $isAdminSession = false;

        if ($gSession['ref'] > 0)
        {
			// extract the single sign-on data
			$ssoToken = $gSession['userdata']['ssotoken'];
			$ssoPrivateDataArray = $gSession['userdata']['ssoprivatedata'];

            if ($gSession['isordersession'] == 1)
            {
                DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ORDER', 'LOGOUT', '', 1);
            }
            else
            {
                if ($gSession['userdata']['usertype'] == TPX_LOGIN_CUSTOMER)
                {
                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'CUSTOMER', 'LOGOUT', '', 1);
                }
                else
                {
                    $isAdminSession = true;
                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'LOGOUT', '', 1);
                }
            }

            if ($isAdminSession)
            {
                // if we are logging out of an admin session then we can just delete the session.
                DatabaseObj::deleteSession($gSession['ref']);
            }
            else
            {				
				// if there is an order session where the session has expired whilst attempting to pay  via a lightbox gateway 
				// or if the session has been revived then we just disable it rather than remove it
                if ((($gSession['isordersession']) && ($pReason == TPX_USER_LOGOUT_REASON_PAYMENT_SESSION_EXPIRED)) || ($gSession['sessionrevived'] == 1) || (($gSession['isordersession']) && ($gSession['order']['temporder']) &&
					($gSession['order']['paymentmethodcode'] = 'PAYLATER')))
                {
					DatabaseObj::disableSession($gSession['ref'], 0);
					$redirectURL = $gSession['webbrandweburl'];
                }
                else
                {
                    AuthenticateObj::endWebSession();
                }
            }
        }
        else
        {
			// no session

			// attempt to find a single sign-on token that may have been transmitted to the server
			$ssoToken = UtilsObj::getGETParam('ssotoken', UtilsObj::getPOSTParam('ssotoken'));

        	AuthenticateObj::endWebSession();
        }

		// call the single sign-out process if we have a token and the session had been started
		if (($ssoToken != '') && ($pReason != TPX_USER_LOGOUT_REASON_SESSION_NOT_STARTED) &&
			($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
		{
			require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

			if (method_exists('ExternalCustomerAccountObj', 'ssoLogout'))
			{
				$ssoParamArray['reason'] = $pReason;
				$ssoParamArray['ssotoken'] = $ssoToken;
				$ssoParamArray['ssoprivatedata'] = $ssoPrivateDataArray;

				$redirectURL = ExternalCustomerAccountObj::ssoLogout($ssoParamArray);
			}
		}
		else
		{
			// no session found
			if ($gSession['ref'] == 0)
			{
				// if we got here from a share link we show the login form as the customer is not signed in at this point
				// we can also reach here if the user has used the back button after logging out or canceling an order
				$fsaction  = UtilsObj::getGETParam('fsaction', '');
				$ref2 = UtilsObj::getGETParam('ref2', '');

				if (($fsaction != '') && ($fsaction == 'Share.preview') && ($ref2 != ''))
				{
					$redirectURL = '';
				}
				else
				{
					$redirectURL = $gSession['webbrandweburl'];
				}
			}
		}

		return $redirectURL;
    }


    static function initNewAccount()
    {
        global $gConstants;
        global $gSession;

        $resultArray = Array();

        if (array_key_exists('ref', $_GET))
        {
            $resultArray['ref'] = $_GET['ref'];
        }
        else
        {
            $resultArray['ref'] = '-1';
        }

		if (array_key_exists('ishighlevel', $_POST))
        {
			$resultArray['fromregisterlink'] = $_POST['fromregisterlink'];
            $resultArray['ishighlevel'] = $_POST['ishighlevel'];
            $resultArray['prtz'] = $_POST['prtz'];
            $resultArray['mawebhluid'] = $_POST['mawebhluid'];
            $resultArray['mawebhlbr'] = $_POST['mawebhlbr'];
            $resultArray['ref'] = '1';
            $resultArray['groupcode'] = '';

            if ($resultArray['ishighlevel'] == 1)
            {
            	$resultArray['groupcode'] = $_POST['groupcode'];
            	$gSession['licensekeydata']['groupcode'] = $_POST['groupcode'];
            }
        }
        else
        {
			$resultArray['fromregisterlink'] = 0;
            $resultArray['ishighlevel'] = 0;
            $resultArray['groupcode'] = '';
            $resultArray['prtz'] = 0;
            $resultArray['mawebhluid'] = 0;
            $resultArray['mawebhlbr'] = '';
        }

		$resultArray['registerfsaction'] = $_POST['registerfsaction'];

        if (array_key_exists('mobile', $_POST))
        {
            $resultArray['ismobile'] = $_POST['mobile'];
        }
        else
        {
            $resultArray['ismobile'] = false;
        }

        // Get the brand information to check if the user has to register a username, or just use the email address.
        $brandSettings = DatabaseObj::getBrandingFromCode($gSession['webbrandcode']);

        $resultArray['registerusingemail'] = $brandSettings['registerusingemail'];

        // if the order is offline then set the default details when creating a new account to the ones that are in the session
        if (($gSession['isordersession'] == 1) && ($gSession['order']['isofflineorder'] == 1))
        {
            $resultArray['contactfname'] = $gSession['shipping'][0]['shippingcontactfirstname'];
            $resultArray['contactlname'] = $gSession['shipping'][0]['shippingcontactlastname'];
            $resultArray['companyname'] = $gSession['shipping'][0]['shippingcustomername'];
            $resultArray['address1'] = $gSession['shipping'][0]['shippingcustomeraddress1'];
            $resultArray['address2'] = $gSession['shipping'][0]['shippingcustomeraddress2'];
            $resultArray['address3'] = $gSession['shipping'][0]['shippingcustomeraddress3'];
            $resultArray['address4'] = $gSession['shipping'][0]['shippingcustomeraddress4'];
            $resultArray['city'] = $gSession['shipping'][0]['shippingcustomercity'];
            $resultArray['state'] = $gSession['shipping'][0]['shippingcustomerstate'];
            $resultArray['county'] = $gSession['shipping'][0]['shippingcustomercounty'];
            $resultArray['regioncode'] = $gSession['shipping'][0]['shippingcustomerregioncode'];
            $resultArray['region'] = $gSession['shipping'][0]['shippingcustomerregion'];
            $resultArray['postcode'] = $gSession['shipping'][0]['shippingcustomerpostcode'];
            $resultArray['countrycode'] = $gSession['shipping'][0]['shippingcustomercountrycode'];
            $resultArray['countryname'] = $gSession['shipping'][0]['shippingcustomercountryname'];
            $resultArray['telephonenumber'] = $gSession['shipping'][0]['shippingcustomertelephonenumber'];
            $resultArray['emailaddress'] = $gSession['shipping'][0]['shippingcustomeremailaddress'];
        }
        else
        {
            $licenseKeyDataArray = DatabaseObj::getLicenseKeyFromCode($gSession['licensekeydata']['groupcode']);

            $resultArray['contactfname'] = '';
            $resultArray['contactlname'] = '';
            $resultArray['companyname'] = '';
            $resultArray['address1'] = '';
            $resultArray['address2'] = '';
            $resultArray['address3'] = '';
            $resultArray['address4'] = '';
            $resultArray['city'] = '';
            $resultArray['state'] = '';
            $resultArray['county'] = '';
            $resultArray['regioncode'] = '';
            $resultArray['region'] = '';
            $resultArray['postcode'] = '';
            $resultArray['countrycode'] = $licenseKeyDataArray['countrycode']; // set the address form country to the country of the license key.
            $resultArray['countryname'] = '';
            $resultArray['telephonenumber'] = '';
            $resultArray['emailaddress'] = '';
        }

        return $resultArray;
    }

    static function createNewAccount()
    {
        return AuthenticateObj::createNewAccount();
    }

    static function initForgotPassword()
    {
        if (array_key_exists('ref', $_GET))
        {
            $resultArray['ref'] = $_GET['ref'];
        }
        else
        {
            $resultArray['ref'] = '-1';
        }

        if (array_key_exists('mobile', $_POST))
        {
            $resultArray['ismobile'] = $_POST['mobile'];
        }
        else
        {
            $resultArray['ismobile'] = false;
        }

        if (array_key_exists('login', $_POST))
        {
            $resultArray['login'] = $_POST['login'];
        }
        else
        {
            $resultArray['login'] = false;
        }

        if (array_key_exists('passwordlinkexpired', $_POST))
        {
            $resultArray['passwordlinkexpired'] = $_POST['passwordlinkexpired'];
        }
        else
        {
            $resultArray['passwordlinkexpired'] = false;
        }

        // generate unique request token. This is to prevent the reset password request
        // from being called when a user refreshes the page.
		$resultArray['passwordresetrequesttoken'] = self::generateUniquePasswordResetRequestFormToken();

        return $resultArray;
    }

    static function initForgotPasswordFromEmail()
    {
        global $gSession;

        $resultArray = array(
            'ref' => '-1',
            'ismobile' => $gSession['ismobile'],
            'login' => false,
            'passwordlinkexpired' => false,
			'passwordresetrequesttoken' => self::generateUniquePasswordResetRequestFormToken(),
			'passwordresetdatabasetoken' => UtilsObj::getGETParam('td', '')
        );

        return $resultArray;
    }

	static function resetPasswordRequest($pRecordID, $pWebBrandCode, $pLogin, $pPasswordFormat, $pPasswordResetRequestToken, $pReturnInformation)
	{
		$resultArray = array('validtoken' => false);
        $userID = 0;
        $loginToUse = $pLogin;
        $emailAccountArray = array();

		$systemConfigArray = DatabaseObj::getSystemConfig();
		$passwordResetRequestTokenDataArray = unserialize(UtilsObj::decryptData($pPasswordResetRequestToken, $systemConfigArray['systemkey'], true));
		$passwordResetFormTokenValue = $passwordResetRequestTokenDataArray[0];

        // Check for a matching user name first.
        $userArray = DatabaseObj::getUserAccountFromBrandAndLogin($pWebBrandCode, $loginToUse);

        if ('str_ErrorNoAccount' == $userArray['result'])
        {
            // User name does not exist, check for an account using the email address.
            if (UtilsObj::validateEmailAddress($loginToUse))
            {
                // The reset attempt was made with an email address, get a list of customer accounts which use that email address.
                $userEmailAccountArray = DatabaseObj::getValidUserAccountsForEmailAndBrand($pWebBrandCode, $loginToUse, '', $pPasswordFormat);

				// Multiple possible accounts been matched from forgotten password, (matching email address).
                if ($userEmailAccountArray['result'] == '')
                {
                    // Check the number of accounts found for the email address.
                    if ($userEmailAccountArray['count'] == 1)
                    {
                        // A single account matched the details, use this to login.
                        $loginToUse = $userEmailAccountArray['accounts'][0]['login'];

                        // Get the details for the account, using the username.
                        $userArray = DatabaseObj::getUserAccountFromBrandAndLogin($pWebBrandCode, $loginToUse);

                        // Assign the user id for the password reset request.
                        $userID = $userArray['recordid'];
                    }
					else
					{
                        // More than 1 account with the email address was found.
                        // Record the accounts which match the entered email address.
                        // These will be sent to the user via email.
                        $emailAccountArray = $userEmailAccountArray['accounts'];
					}
                }
            }
            else
            {
                // No matching accounts have been found, and the value is not an email address. Remove 
                // the username name, which will still allow a reset code to be generated, but will 
                // not link the code to an account.
                $loginToUse = '';
            }
        }
        else
        {
            // User name found, assign the id for the password reset request.
            $userID = $userArray['recordid'];

            $emailAccountArray[] = $userArray;
        }

        if (count($emailAccountArray) > 1)
        {
            return AuthenticateObj::resetPasswordRequestMultipleAccounts($pRecordID, $pWebBrandCode, $loginToUse, $pPasswordFormat, $passwordResetFormTokenValue, $emailAccountArray, $pReturnInformation);
        }
        else 
        {
            // Check if the token has been used to request a password reset.
            $checkResetFormTokenResult = self::checkIfPasswordResestTokenHasBeenUsed($userID, $passwordResetFormTokenValue);

            if (($checkResetFormTokenResult['result'] == '') && ($checkResetFormTokenResult['validtoken'] == true))
            {
                return AuthenticateObj::resetPasswordRequest($pRecordID, $pWebBrandCode, $loginToUse, $pPasswordFormat, $passwordResetFormTokenValue, $pReturnInformation);
            }
        }
	}

    static function checkIfPasswordResestTokenHasBeenUsed($pUserID, $pPasswordResetFormTokenValue)
	{
		$resultArray = Array();
		$result = '';

		$validToken = true;
		$passwordResetToken = '';

		// see when the user last completed the reset password process
		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$stmt = $dbObj->prepare("SELECT `actionnotes` FROM `ACTIVITYLOG` WHERE (`actioncode` = 'RESETPASSWORDREQUEST') AND (`userid` = ?) AND (`success` = 1) ORDER BY `id` DESC");
			if ($stmt)
			{
				if ($stmt->bind_param('i', $pUserID))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0)
							{
								if ($stmt->bind_result($passwordResetToken))
								{
									while ($stmt->fetch())
									{
										if ($pPasswordResetFormTokenValue == $passwordResetToken)
										{
											$validToken = false;
											break;
										}
									}
								}
								else
								{
									$result = 'getUserLastPasswordResetData lookup bind result ' . $dbObj->error;
								}
							}
						}
						else
						{
							$result = 'getUserLastPasswordResetData lookup store result ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'getUserLastPasswordResetData lookup execute ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'getUserLastPasswordResetData lookup bind param ' . $dbObj->error;
				}
			}
			else
			{
				$result = 'getUserLastPasswordResetData lookup prepare ' . $dbObj->error;
			}
		}
		else
		{
			$result = 'getUserLastPasswordResetData lookup connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
		$resultArray['validtoken'] = $validToken;

		return $resultArray;
	}

	static function decryptPasswordResetURL($pRequestToken, $pSystemKey)
	{
		$tokenParamArray = array();

		$tokenData = explode(chr(10), UtilsObj::decryptData($pRequestToken, $pSystemKey, true));

		// expand tokendata elements into parameter and values, place in associated array
		// uid = userid
		// rt = the time the reset link was requested
		// acs = authentication code status (is the authentication code feature on 0: Off, 1: On)
		// ac = authentication code
		foreach ($tokenData as $theURLParam)
		{
			$temp = explode('=', $theURLParam, 2);

			$tokenParamArray[$temp[0]] = $temp[1];
		}

		return $tokenParamArray;
	}

	static function resetPassword($pRequestToken)
	{
		global $ac_config;

		$systemConfigArray = DatabaseObj::getSystemConfig();
		$tokenParamArray = self::decryptPasswordResetURL($pRequestToken, $systemConfigArray['systemkey']);

		$tokenString = $tokenParamArray['t'];
		$requestHMAC = $tokenParamArray['hmac'];

		$linkActiveResultArray = self::determineIfResetPasswordLinkIsActive($tokenString, $requestHMAC, $systemConfigArray['systemkey'], '', false);
		$linkActive = $linkActiveResultArray['linkactive'];

		return $linkActive;
	}

	static function determineIfResetPasswordLinkIsActive($pTokenLookUpString, $pRequestHMAC, $pSystemKey, $pUserSubmittedAuthCode, $pFromPasswordRestProcess)
	{
		$resultArray = array();
		$linkActive = TPX_PASSWORDRESETLINKEXPIRY_ACTIVE;
		$userID = 0;
		$emailAddress = '';
		$returnInformation = '';
		$userLogin = '';

		// lookup a rest password request based of token
		$resetPasswordTokenLookUpArray = AuthenticateObj::resetPasswordTokenLookUp($pTokenLookUpString);
		$linkActive = $resetPasswordTokenLookUpArray['linkexpirystatus'];

		if (($resetPasswordTokenLookUpArray['result'] == '') && ($linkActive == TPX_PASSWORDRESETLINKEXPIRY_ACTIVE) || ($linkActive == TPX_PASSWORDRESETLINKEXPIRY_ACTIVEWITHAUTHCODE))
		{
			$userID = $resetPasswordTokenLookUpArray['userid'];
			$returnInformation = $resetPasswordTokenLookUpArray['returninformation'];
			$linkRequestTime = $resetPasswordTokenLookUpArray['linkrequesttime'];

			// first check to see if the hmac of the link match.
			// we need to lookup the user account based of the userid from the request token
			// this is so we can get the record datecreated so we can build the hmac,
			// the lastlogin date for the user and last reset completion date.
			$userAccountArray = self::getUserLastPasswordResetData($userID);
			$emailAddress = $userAccountArray['useremailaddress'];
			$userLastLoggedInDate = $userAccountArray['userlastlogindate'];
			$lastPasswordResetDate = $userAccountArray['lastpasswordresetcompletiondate'];
			$userLogin = $userAccountArray['userlogin'];

			if ($userAccountArray['result'] == '')
			{
				// as another layer of security (in the event that the blow fish data has been tampered with)
				// check to make sure that the request hash matches
				$hmac = hash_hmac('sha256', $userID . $userAccountArray['userdatecreated'], $pSystemKey);

				if ($hmac === $pRequestHMAC)
				{
					 // if the user has logged in since the the reset request then we mut also expire the link.
					 if (($linkRequestTime < strtotime($userLastLoggedInDate)) && ($userLastLoggedInDate != '0000-00-00 00:00:00'))
					 {
						$linkActive = TPX_PASSWORDRESETLINKEXPIRY_LOGINAFTERREQUEST;
					 }

					// if the link has not already expired naturally perform the extra checks.
					if (($linkActive == TPX_PASSWORDRESETLINKEXPIRY_ACTIVE) || ($linkActive == TPX_PASSWORDRESETLINKEXPIRY_ACTIVEWITHAUTHCODE))
					{
						 // we need to check to see when the lastime a use reset their password.
						 if ($lastPasswordResetDate != '0000-00-00 00:00:00')
						 {
							// if the user has reset their password after the link was requested then the link has expired.
							if (strtotime($lastPasswordResetDate) >= $linkRequestTime)
							{
								$linkActive = TPX_PASSWORDRESETLINKEXPIRY_PASSWORDRESETAFTERREQUEST;
							}
						 }
					}
				}
				else
				{
					$linkActive = TPX_PASSWORDRESETLINKEXPIRY_HASHMISMATCH;
				}
			}
			else
			{
				$linkActive = TPX_PASSWORDRESETLINKEXPIRY_RESETLOOKUPFAILED;
			}
		}
		else
		{
			$linkActive = TPX_PASSWORDRESETLINKEXPIRY_RESETLOOKUPFAILED;
		}

		// if the link is still active we must check the authentication code status.
		if (($linkActive == TPX_PASSWORDRESETLINKEXPIRY_ACTIVEWITHAUTHCODE) && (! $pFromPasswordRestProcess))
		{
			$autCodeResultArray = self::determineIfPasswordResetAuthCodeCanBeUsed($userID, $linkRequestTime, $pUserSubmittedAuthCode);

			if ($autCodeResultArray['result'] == '')
			{
				$linkActive = $autCodeResultArray['linkactive'];
			}
			else
			{
				// database error occurred so prevent a retry on entering auth code.
				$linkActive = TPX_PASSWORDRESETLINKEXPIRY_AUTHCODELIMITREACHED;
			}
		}

		$resultArray['linkactive'] = $linkActive;
		$resultArray['userid'] = $userID;
		$resultArray['useremailaddress'] = $emailAddress;
		$resultArray['returninformation'] = $returnInformation;
		$resultArray['userlogin'] = $userLogin;

		return $resultArray;
	}

	static function getUserLastPasswordResetData($pUserID)
	{
		$resultArray = Array();
		$result = '';

		$userDateCreated = '0000-00-00 00:00:00';
		$userLastLoginDate = '0000-00-00 00:00:00';
		$lastPasswordResetCompletionDate = '0000-00-00 00:00:00';
		$emailAddress = '';
		$userLogin = '';

		// see when the user last completed the reset password process
		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$stmt = $dbObj->prepare("SELECT `u`.`datecreated`, `u`.`emailaddress`, `u`.`lastlogindate`, `u`.`login`,
   									(SELECT MAX(`datecreated`) FROM `ACTIVITYLOG` WHERE `actioncode` = 'RESETPASSWORD' AND (`userid` = `u`.`id`) AND (`success` = 1)) AS `lastresetdate`
   									FROM `USERS` AS `u`
   									WHERE `u`.`id` = ?");
			if ($stmt)
			{
				if ($stmt->bind_param('i', $pUserID))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0)
							{
								if ($stmt->bind_result($userDateCreated, $emailAddress, $userLastLoginDate, $userLogin, $lastPasswordResetCompletionDate))
								{
									if (! $stmt->fetch())
									{
										$result = 'getUserLastPasswordResetData lookup fetch ' . $dbObj->error;
									}
								}
								else
								{
									$result = 'getUserLastPasswordResetData lookup bind result ' . $dbObj->error;
								}
							}
						}
						else
						{
							$result = 'getUserLastPasswordResetData lookup store result ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'getUserLastPasswordResetData lookup execute ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'getUserLastPasswordResetData lookup bind param ' . $dbObj->error;
				}
			}
			else
			{
				$result = 'getUserLastPasswordResetData lookup prepare ' . $dbObj->error;
			}
		}
		else
		{
			$result = 'getUserLastPasswordResetData lookup connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
		$resultArray['userdatecreated'] = $userDateCreated;
		$resultArray['userlogin'] = $userLogin;
		$resultArray['useremailaddress'] = $emailAddress;
		$resultArray['userlastlogindate'] = $userLastLoginDate;
		$resultArray['lastpasswordresetcompletiondate'] = $lastPasswordResetCompletionDate;

		return $resultArray;
	}

	static function determineIfPasswordResetAuthCodeCanBeUsed($pUserID, $pRequestTime, $pUserSubmittedAuthCode)
	{
		$resultArray = Array();
		$result = '';

		$actionNotesToken = '';
		$failureCount = 0;
		$linkActive = TPX_PASSWORDRESETLINKEXPIRY_EXPIREDNATURALLY;
		$authCodeFromLookUp = 0;

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			// only the last authcode generated for the user can be used so we must get last autchode to compare against.
			$stmt = $dbObj->prepare("SELECT `authenticationcode` FROM `USERPASSWORDREQUESTS` WHERE `userid` = ? AND `expirytime` > NOW() ORDER BY `id` DESC LIMIT 1");

			if ($stmt)
			{
				if ($stmt->bind_param('i', $pUserID))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0)
							{
								if ($stmt->bind_result($authCodeFromLookUp))
								{
									if ($stmt->fetch())
									{
										// if we have a result and the authentication code is not empty
										// then the link is active with the authentication code feature enabled.
										if ($authCodeFromLookUp != 0)
										{
											$linkActive = TPX_PASSWORDRESETLINKEXPIRY_ACTIVEWITHAUTHCODE;
										}
									}
								}
								else
								{
									$result = 'get last issued password reset authcode bind result ' . $dbObj->error;
								}
							}
						}
						else
						{
							$result = 'get last issued password reset authcode store result ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'get last issued password reset authcode execute ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'get last issued password reset authcode bind param ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$result = 'get last issued password reset authcode connect ' . $dbObj->error;
			}

			if (($result == '') && ($linkActive == TPX_PASSWORDRESETLINKEXPIRY_ACTIVEWITHAUTHCODE))
			{
				// lookup to see if the authcode has previously had failed attempts.
				$stmt = $dbObj->prepare("SELECT `actionnotes` FROM `ACTIVITYLOG` WHERE `actioncode` = 'RESETPASSWORDAUTHCODEFAILED' AND `userid` = ? AND UNIX_TIMESTAMP(`datecreated`) > ?");
				if ($stmt)
				{
					if ($stmt->bind_param('ii', $pUserID, $pRequestTime))
					{
						if ($stmt->execute())
						{
							if ($stmt->store_result())
							{
								if ($stmt->num_rows > 0)
								{
									if ($stmt->bind_result($actionNotesToken))
									{
										while ($stmt->fetch())
										{
											if ($actionNotesToken == $authCodeFromLookUp)
											{
												$failureCount++;
											}
										}
									}
									else
									{
										$result = 'determineIfPasswordResetAuthCodeCanBeUsed lookup bind result ' . $dbObj->error;
									}
								}
							}
							else
							{
								$result = 'determineIfPasswordResetAuthCodeCanBeUsed lookup store result ' . $dbObj->error;
							}
						}
						else
						{
							$result = 'determineIfPasswordResetAuthCodeCanBeUsed lookup execute ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'determineIfPasswordResetAuthCodeCanBeUsed lookup bind param ' . $dbObj->error;
					}

					$stmt->free_result();
                    $stmt->close();
                    $stmt = null;
				}
				else
				{
					$result = 'determineIfPasswordResetAuthCodeCanBeUsed lookup prepare ' . $dbObj->error;
				}
			}

			if (($result == '') && ($linkActive == TPX_PASSWORDRESETLINKEXPIRY_ACTIVEWITHAUTHCODE))
			{
				// pUserSubmittedAuthCode is empty then we know we need to initialise the auth code form.
				if (($pUserSubmittedAuthCode == '') && ($failureCount < TPX_PASSWORDRESETAUTHCODE_ATTEMPTS_LIMIT))
				{
					$linkActive = TPX_PASSWORDRESETLINKEXPIRY_ACTIVEWITHAUTHCODE;
				}
				else
				{
					if ($failureCount < TPX_PASSWORDRESETAUTHCODE_ATTEMPTS_LIMIT)
					{
						// we may have previous failures but we stull need to check to see if this attempt matches.
						// if it does match then we need to set failure count back to 0 as this has now passed.
						// if it doesnt then we need to log a failure.
						if ($authCodeFromLookUp == $pUserSubmittedAuthCode)
						{
							$failureCount = 0;
						}
						else
						{
							// log activity failed password reset authcode submission
							DatabaseObj::updateActivityLog(0, 0, $pUserID, '', '', 0, 'CUSTOMER', 'RESETPASSWORDAUTHCODEFAILED', $authCodeFromLookUp, 0);

							$failureCount++;
						}
					}

					if ($failureCount == 0)
					{
						$linkActive = TPX_PASSWORDRESETLINKEXPIRY_AUTHCODEOK;
					}
					else if ($failureCount == TPX_PASSWORDRESETAUTHCODE_ATTEMPTS_LIMIT)
					{
						$linkActive = TPX_PASSWORDRESETLINKEXPIRY_AUTHCODELIMITREACHED;

						// the authcode limit has been reached we must expire all exisiting email links.
						// as a user can only enter the latest authcode this prevents a previously generated email link
						// from becoming active again if the number of attempts on the previous authcode has been reached.
						if ($stmt = $dbObj->prepare('UPDATE `USERPASSWORDREQUESTS` SET `expirytime` = now() WHERE `userid` = ?'))
						{
							if ($stmt->bind_param('i', $pUserID))
							{
								if (! $stmt->execute())
								{
									$result = 'str_DatabaseError';
									$resultParam = 'expire passwordrequest authcode failure execute ' . $dbObj->error;
								}
							}
							else
							{
								// could not bind parameters
								$result = 'str_DatabaseError';
								$resultParam = 'expire passwordrequest authcode failure bind params ' . $dbObj->error;
							}

							$stmt->free_result();
							$stmt->close();
						}
						else
						{
							// could not prepare statement
							$result = 'str_DatabaseError';
							$resultParam = 'expire passwordrequest authcode failure prepare ' . $dbObj->error;
						}
					}
					else
					{
						$linkActive = TPX_PASSWORDRESETLINKEXPIRY_AUTHCODEFAILED;
					}
				}
			}

			$dbObj->close();
		}
		else
		{
			$result = 'getUserLastPasdetermineIfPasswordResetAuthCodeCanBeUsedswordResetData lookup connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
		$resultArray['linkactive'] = $linkActive;

		return $resultArray;
	}

	static function resetPasswordProcess($pRecordID, $pWebBrandCode, $pRequestToken, $pNewPassword, $pPasswordFormat)
	{
		global $ac_config;

		$resultArray = array('linkactive' => -1, 'data' => array());

		$systemConfigArray = DatabaseObj::getSystemConfig();
		$tokenParamArray = self::decryptPasswordResetURL($pRequestToken, $systemConfigArray['systemkey']);

		$tokenString = $tokenParamArray['t'];
		$requestHMAC = $tokenParamArray['hmac'];

		// this is the point where the user has entered a new password.
		// we need to check if the link is still active based off
		// has the link expired naturally
		// has the user already reset their password since this reset link was issued
		// has the user logged in since this reset link was issued.
		$linkActiveResultArray = self::determineIfResetPasswordLinkIsActive($tokenString, $requestHMAC, $systemConfigArray['systemkey'], '', true);
		$linkActive = $linkActiveResultArray['linkactive'];

		// if the link is still active then attempt to update the users password.
		// if the link is inactive then we need to return the reason for the link expiring
		// so we can redirect the user to the reset link expiry page.
		if (($linkActive == TPX_PASSWORDRESETLINKEXPIRY_ACTIVE) || ($linkActive == TPX_PASSWORDRESETLINKEXPIRY_ACTIVEWITHAUTHCODE))
		{
			$userLogin = $linkActiveResultArray['userlogin'];
			$userID = $linkActiveResultArray['userid'];
			$emailAddress = $linkActiveResultArray['useremailaddress'];

			// return information will only be populated if the password reset request was from an ordersession
			$isOrderSession = ($linkActiveResultArray['returninformation'] != '' ? 1 : 0);

			$resultArray['linkactive'] = $linkActive;
			$resultArray['data'] = AuthenticateObj::resetPasswordProcess($pRecordID, $pWebBrandCode, $userID, $userLogin, $pNewPassword, $pPasswordFormat, $isOrderSession);
			$resultArray['data']['returninformation'] = $linkActiveResultArray['returninformation'];
		}
		else
		{
			$resultArray['linkactive'] = $linkActive;
		}

		return $resultArray;
	}

	static function validatePasswordResetAuthCode($pRequestToken, $pAuthCode)
	{
		global $ac_config;

		$resultArray = array('linkactive' => -1, 'result' => '', 'resultparam' => '');

		$systemConfigArray = DatabaseObj::getSystemConfig();
		$tokenParamArray = self::decryptPasswordResetURL($pRequestToken, $systemConfigArray['systemkey']);
		$error = '';

		$tokenString = $tokenParamArray['t'];
		$requestHMAC = $tokenParamArray['hmac'];

		$linkActiveResultArray = self::determineIfResetPasswordLinkIsActive($tokenString, $requestHMAC, $systemConfigArray['systemkey'], $pAuthCode, false);
		$linkActive = $linkActiveResultArray['linkactive'];

		// return error message to display for failed authcode
		if ($linkActive == TPX_PASSWORDRESETLINKEXPIRY_AUTHCODEFAILED)
		{
			$error = 'str_MessageInvalidAuthCode';
		}

		$resultArray['linkactive'] = $linkActive;
		$resultArray['result'] = $error;

		return $resultArray;
	}

	static function generateUniquePasswordResetRequestFormToken()
	{
        $systemConfigArray = DatabaseObj::getSystemConfig();
        $passwordResetRequestTokenArray = array();
        $passwordResetRequestTokenArray[] = UtilsObj::createRandomString(32);
		$passwordResetRequestSerialisedString = serialize($passwordResetRequestTokenArray);

		$passwordResetRequestFormToken = UtilsObj::encryptData($passwordResetRequestSerialisedString, $systemConfigArray['systemkey'], true);

		return $passwordResetRequestFormToken;
    }

    /**
     * Process the update email request.
     *
     * @param string $pResetEmailData encrypted data holding the update email request details.
     */
    static function updateEmailRequest($pResetEmailData)
    {
        global $ac_config;

        $resultArray = array('result' => '', 'resultparam' => '', 'newemail' => '');

        $resetEmailDataArray = array();
        $systemConfigArray = DatabaseObj::getSystemConfig();

        // Decrypt the request email update data, and place each line of the string into an array .
        $resetEmailStrArray = explode(chr(10), UtilsObj::decryptData($pResetEmailData, $systemConfigArray['systemkey'], true));

        // Convert the request data lines into an array.
        foreach ($resetEmailStrArray as $resetData)
        {
            $tempArray = explode('=', $resetData, 2);
            $resetEmailDataArray[$tempArray[0]] = $tempArray[1];
        }

        // Read the AUTHENTICATIONDATASTORE table for the data linked to the update email address request.
        $authenticationRecord = AuthenticateObj::getAuthenticationDataRecord(TPX_AUTHENTICATIONTYPE_EMAILUPDATE, $resetEmailDataArray['authkey'], false);

        // Make sure a record was found in the AUTHENTICATIONDATASTORE.
        if ($authenticationRecord['found'])
        {
            // A record linked to the update of email was found.
            // Use the ID of the user requesting the update to get the account data.
            $userArray = DatabaseObj::getUserAccountFromID($authenticationRecord['data']['userid']);

            // Generate a message auth code based on the user account data, to be used to validate the request.
            $hmacStr = $userArray['recordid'] . $userArray['datecreated'] . $userArray['emailaddress'] . $resetEmailDataArray['tkn'];
            $verifyHMAC = hash_hmac('sha256', $hmacStr, $systemConfigArray['systemkey']);

            // Verify that the hash in the request data matches the generated hash stored in the AUTHENTICATIONDATASTORE.
            if ($verifyHMAC == $resetEmailDataArray['hmac'])
            {
                // Hash matches, update the email address of the users account.
                // If the login and email address are the same, update the login as well.
                $resultArray = DatabaseObj::updateAccountEmailAndLogin($authenticationRecord['data'], ($userArray['login'] == $userArray['emailaddress']));

                if ('' == $resultArray['result'])
                {
                    // The update of the email address was successful, remove the AUTHENTICATIONDATASTORE record.
                    AuthenticateObj::deleteAuthenticationDataRecords($resetEmailDataArray['authkey']);

                    $resultArray['result'] = 'str_MessageUpdateEmailRequestSuccess';
                    $resultArray['newemail'] = $authenticationRecord['data']['new'];

                    // Get the brand information before generating the email.
                    $brandSettings = DatabaseObj::getBrandingFromCode($userArray['webbrandcode']);

                    // Create the link to be used in the email, to allow the email address to be updated.
                    $baseURL = $brandSettings['displayurl'];

                    // Build the brand URL based on default brand if displayurl is empty.
                    $defaults = DatabaseObj::getBrandingFromCode('');
                    if ($baseURL == '')
                    {
                        $baseURL = UtilsObj::correctPath($defaults['displayurl']) . ($ac_config['WEBBRANDFOLDERNAME'] == '' ? 'Branding' : $ac_config['WEBBRANDFOLDERNAME']) . '/' . $brandSettings['name'] . '/';
                    }

					// Determine if this account logs in with their email address.
					$signinWithEmail = ($authenticationRecord['data']['original'] == $userArray['login']);

                    // include the email creation module
                    require_once('../Utils/UtilsEmail.php');

                    // Generate the email notification to send to the user.
                    $emailObj = new TaopixMailer();

                    // Confirmation email to the original email address, BCC to new email address.
                    $emailObj->sendTemplateEmail('customer_emailupdaterequestsuccess', $userArray['webbrandcode'], $brandSettings['applicationname'],
                        $baseURL, '', $userArray['contactfirstname'] . ' ' . $userArray['contactlastname'], $authenticationRecord['data']['original'], 
                        $userArray['contactfirstname'] . ' ' . $userArray['contactlastname'], $authenticationRecord['data']['new'], $userArray['recordid'],
                        array(
                            "userid" => $userArray['recordid'],
                            "originalemail" => $authenticationRecord['data']['original'],
                            "newemail" => $authenticationRecord['data']['new'],
                            "username" => $userArray['login'],
                            "displayurl" => $baseURL,
							"signinwithemail" => $signinWithEmail),
                        '', ''
                    );

                    // Generate a message to insert into the activity log.
                    $activityMessage = 'Email address updated (' . $userArray['emailaddress'] . ' -> ' . $authenticationRecord['data']['new'] . ')';
                    if ($userArray['login'] == $userArray['emailaddress'])
                    {
                        $activityMessage .= ', login updated (' . $userArray['login'] . ' -> ' . $authenticationRecord['data']['new'] . ')';
                    }

                    // Log the update request in the activity log.
                    DatabaseObj::updateActivityLog(0, 0, $userArray['recordid'], $userArray['login'], $userArray['contactfirstname'] . ' ' . $userArray['contactlastname'], 0, 'CUSTOMER', 'UPDATEEMAILREQUESTSUCCESS', $activityMessage, 1);
                }
            }
            else
            {
                // The hash did not match, the message may have been tampered with.
                // The account details will not be updated.
                $resultArray['result'] = 'str_MessageUpdateEmailRequestInvalid';
            }
        }
        else
        {
            // The hash did not match, the message may have been tampered with.
            // The account details will not be updated.
            $resultArray['result'] = 'str_MessageUpdateEmailRequestInvalid';
        }

        return $resultArray;
    }
}
?>
