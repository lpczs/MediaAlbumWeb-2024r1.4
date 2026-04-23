<?php

use Security\RequestValidationTrait;

require_once(__DIR__.'/../Welcome/Welcome_model.php');
require_once(__DIR__.'/../Welcome/Welcome_view.php');

class Welcome_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::WebSessionActive() == 1)
		{
			$resultArray = Array();
			$resultArray['result'] = '';

			Welcome_view::processLogin($resultArray);
		}
		else
		{
			$resultArray = Welcome_model::displayLogin();

			switch ($resultArray['result'])
			{
				case 'LOGIN':
				{
					// single sign-on was successful
					Welcome_view::processLogin($resultArray['ssodataarray']);
					break;
				}
				case 'SSOREDIRECT':
				{
					// redirect to grab the single sign-on token
					AuthenticateObj::ssoRedirect($resultArray['ssodataarray']);
					break;
				}
				default:
				{
					// single sign-on couldn't be performed
					if ($resultArray['info'] == '')
					{
						//  no error was returned so display the login page
						Welcome_view::displayLogin($resultArray);
					}
					else
					{
						// an error occurred so display it
						Welcome_view::displaySSOError($resultArray['info'], $resultArray['info2']);
					}

					break;
				}
			}
		}
	}

	static function processLogin()
	{
		$isMObile = UtilsObj::getPOSTParam('mobile', false);
        if ($isMObile == 'true')
        {
            $resultArray = Welcome_model::processLogin(0, true);
        }
        else
        {
            $resultArray = Welcome_model::processLogin(0, false);
        }

		Welcome_view::processLogin($resultArray);
	}

	static function processLogout($pReason, $canCreateAccounts = 0, $message = '', $pForceHighLevelBasketUser = 0)
	{
		$sessionID = AuthenticateObj::getSessionRef();

		$redirectURL = Welcome_model::processLogout($pReason);
		Welcome_view::processLogout($redirectURL, $sessionID, $canCreateAccounts, $message, $pForceHighLevelBasketUser);
	}

	static function processLogout2($pReason, $message = '')
	{
		$redirectURL = Welcome_model::processLogout($pReason);

		$_POST['ref'] = 0;
		$_GET['ref'] = 0;

		// Clear down the gSession global object
		global $gSession;
		$gSession['ref'] = 0;

		Welcome_view::processLogout($redirectURL, 0, 0, $message);
	}

	static function processLogoutRedirect($pReason)
	{
		$redirectURL = Welcome_model::processLogout($pReason);

		$_POST['ref'] = 0;
		$_GET['ref'] = 0;

		Welcome_view::processLogoutRedirect($redirectURL);
	}

	static function initNewAccount()
	{
		$resultArray = Welcome_model::initNewAccount();
		Welcome_view::initNewAccount($resultArray);
	}

	static function createNewAccountLarge()
	{
		$resultArray = Welcome_model::createNewAccount();
		$resultArray['registerfsaction'] = 'Welcome.createNewAccountLarge';
		$resultArray['prtz'] = 0;
		$resultArray['mawebhlbr'] = '';
		$resultArray['mawebhluid'] = 0;

        Welcome_view::createNewAccountLarge($resultArray);
	}

    static function createNewAccountSmall()
	{
		$resultArray = Welcome_model::createNewAccount();
		$resultArray['registerfsaction'] = 'Welcome.createNewAccountSmall';
        Welcome_view::createNewAccountSmall($resultArray);
	}

	static function initForgotPassword()
	{
		$resultArray = Welcome_model::initForgotPassword();
		Welcome_view::initForgotPassword($resultArray);
	}

	static function initForgotPasswordFromEmail()
	{
        // Determine the device being used. 
		UtilsObj::setSessionDeviceData();

        DatabaseObj::updateSession();

        $resultArray = Welcome_model::initForgotPasswordFromEmail();

        // Display the forgotten password form again.
		Welcome_view::initForgotPassword($resultArray, '', '', true);
    }

	static function resetPasswordRequest()
	{
		global $gSession;

		$recordID = UtilsObj::getGETParam('ref', -1);
		$webBrandCode = $gSession['webbrandcode'];
		$login = UtilsObj::getPOSTParam('login');
        $isMobile = (UtilsObj::getPOSTParam('mobile', false) == 'true') ? true : false;
		$passwordFormat = UtilsObj::getPOSTParam('format', TPX_PASSWORDFORMAT_MD5);
		$passwordResetRequestToken = UtilsObj::getPOSTParam('passwordresetrequesttoken', '');
		$passwordResetRequestDatabaseToken = UtilsObj::getPOSTParam('passwordresetdatabasetoken', '');
		$returnInformation = '';

		if ($passwordResetRequestToken != '')
		{
			if ($passwordResetRequestDatabaseToken !== '')
			{
				$systemConfigArray = DatabaseObj::getSystemConfig();
				$tokenParamArray = Welcome_model::decryptPasswordResetURL($passwordResetRequestDatabaseToken, $systemConfigArray['systemkey']);

				$tokenString = $tokenParamArray['t'];

				$lookupResult = AuthenticateObj::resetPasswordTokenLookUp($tokenString);
				$returnInformation = $lookupResult['returninformation'];
			}

			$resultArray = Welcome_model::resetPasswordRequest($recordID, $webBrandCode, $login, $passwordFormat, $passwordResetRequestToken, $returnInformation);

			if ($resultArray['validtoken'])
			{
				if ($isMobile)
				{
					Welcome_view::resetPasswordRequestSmall($resultArray);
				}
				else
				{
					$resultArray['ismobile'] = false;
					$resultArray['prtz'] = UtilsObj::getPOSTParam('prtz', 0);
					$resultArray['mawebhluid'] = UtilsObj::getPOSTParam('mawebhluid', '');
					$resultArray['mawebhlbr'] = UtilsObj::getPOSTParam('mawebhlbr', '');
					$resultArray['ishighlevel'] = UtilsObj::getPOSTParam('ishighlevel', 0);
					$resultArray['passwordlinkexpired'] = UtilsObj::getPOSTParam('passwordlinkexpired', false);

					// if there was an error with the passwored reset request we must generate a new form token.
					// this is due to the fact that the page is refreshed when an erorr occurs.
					if ($resultArray['result'] != '')
					{
						$resultArray['passwordresetrequesttoken'] = Welcome_model::generateUniquePasswordResetRequestFormToken();
					}

					Welcome_view::resetPasswordRequestLarge($resultArray);
				}
			}
			else
			{
				self::initialize();
			}
        }
        else
        {
        	self::initialize();
        }
	}

	static function resetPassword()
	{
		UtilsObj::setSessionDeviceData();

        DatabaseObj::updateSession();

		$requestToken = UtilsObj::getGETParam('td', '');

		if ($requestToken != '')
		{
			$linkActive = Welcome_model::resetPassword($requestToken);

			if (($linkActive == TPX_PASSWORDRESETLINKEXPIRY_ACTIVE) || ($linkActive == TPX_PASSWORDRESETLINKEXPIRY_ACTIVEWITHAUTHCODE))
			{
				$resultArray = Array();
				$resultArray['result'] = '';
				$resultArray['resultparam'] = '';

				if ($linkActive == TPX_PASSWORDRESETLINKEXPIRY_ACTIVE)
				{
					Welcome_view::showResetPasswordForm($requestToken, $resultArray);
				}
				else
				{
					Welcome_view::showResetPasswordAuthCodeForm($requestToken, $resultArray);
				}
			}
			else
			{
				Welcome_view::resetPasswordLinkExpired();
			}
		}
		else
		{
			Welcome_view::resetPasswordLinkExpired();
		}
	}

	static function resetPasswordProcess()
	{
		global $gSession;

		UtilsObj::setSessionDeviceData();

        DatabaseObj::updateSession();

		$recordID = UtilsObj::getGETParam('ref', -1);
		$webBrandCode = $gSession['webbrandcode'];

        $newPassword = UtilsObj::getPOSTParam('data2', '');
        $requestToken = UtilsObj::getPOSTParam('data3', '');
        $passwordFormat = (int) UtilsObj::getPOSTParam('format', TPX_PASSWORDFORMAT_MD5);

        if ($requestToken != '')
		{
			$resultArray = Welcome_model::resetPasswordProcess($recordID, $webBrandCode, $requestToken, $newPassword, $passwordFormat);

			// the link might have expired which means a users password has not been updated.
			// redirect the user to the reset link expired page.
			if (($resultArray['linkactive'] == TPX_PASSWORDRESETLINKEXPIRY_ACTIVE) || ($resultArray['linkactive'] == TPX_PASSWORDRESETLINKEXPIRY_ACTIVEWITHAUTHCODE))
			{
				$error = $resultArray['data']['result'];

				if ($error == '')
				{
					Welcome_view::showResetPasswordSuccess($resultArray['data']['returninformation']);
				}
				else
				{
					Welcome_view::showResetPasswordForm($requestToken, $resultArray['data']);
				}
			}
			else
			{
				Welcome_view::resetPasswordLinkExpired(true);
			}
		}
		else
		{
			Welcome_view::resetPasswordLinkExpired(true);
		}
	}

	static function resetPasswordProcessAuthCode()
	{
		global $gSession;

		UtilsObj::setSessionDeviceData();

        DatabaseObj::updateSession();

		$recordID = UtilsObj::getGETParam('ref', -1);
		$webBrandCode = $gSession['webbrandcode'];

		$authCode = UtilsObj::getPOSTParam('data1', '');
        $requestToken = UtilsObj::getPOSTParam('data2', '');

        if ($requestToken != '')
        {
			$resultArray = Welcome_model::validatePasswordResetAuthCode($requestToken, $authCode);

			if ($resultArray['linkactive'] == TPX_PASSWORDRESETLINKEXPIRY_AUTHCODEOK)
			{
				Welcome_view::showResetPasswordForm($requestToken, $resultArray, true);
			}
			else if ($resultArray['linkactive'] == TPX_PASSWORDRESETLINKEXPIRY_AUTHCODEFAILED)
			{
				Welcome_view::showResetPasswordAuthCodeForm($requestToken, $resultArray);
			}
			else if ($resultArray['linkactive'] == TPX_PASSWORDRESETLINKEXPIRY_AUTHCODELIMITREACHED)
			{
				Welcome_view::showResetPasswordAuthCodeLimitReached();
			}
			else
			{
				Welcome_view::resetPasswordLinkExpired(true);
			}
        }
		else
		{
			Welcome_view::resetPasswordLinkExpired(true);
		}
	}

	static function loginExpire()
	{
		self::processLogout(0, 'str_ErrorSessionExpired');
    }

    /**
     * An update email address request has been made, the user has received an email and clicked the link.
     *
     * @global type $gSession
     */
    static function updateEmailRequest()
    {
		UtilsObj::setSessionDeviceData();

        DatabaseObj::updateSession();

        $resetEmailData = UtilsObj::getGETParam('red', '');

        $resetResult = Welcome_model::updateEmailRequest($resetEmailData);
        Welcome_view::updateEmailRequest($resetResult);
    }
}

?>
