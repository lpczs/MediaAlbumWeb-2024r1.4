<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsRoute.php');
require_once('../Utils/TaopixOAuthProvider.php');
require_once('../Utils/TaopixOAuthRefreshToken.php');
require_once('../AdminBranding/AdminBranding_model.php');
require_once('../AdminBranding/AdminBranding_view.php');

use Security\RequestValidationTrait;

class AdminBranding_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$brandCount = AdminBranding_model::getBrandCount();
			AdminBranding_view::initialize($brandCount);
		}
	}

	static function brandList()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    AdminBranding_model::brandList();
		}
	}

    static function brandingActivate()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
        {
            AdminBranding_model::brandingActivate();
        }
	}

    static function brandingAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminBranding_model::displayEdit(-1);
			AdminBranding_view::displayAdd($resultArray);
		}
	}

	static function brandingAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminBranding_model::brandingAdd();
		}
	}

	static function brandingEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$brandingID = $_GET['id'];
			$resultArray = AdminBranding_model::displayEdit($brandingID);
			$resultArray['productionsites'] = RoutingObj::getProductionSiteNames();
			AdminBranding_view::displayEdit($resultArray);
		}
	}

	static function brandingEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminBranding_model::brandingEdit();
		}
	}

	static function brandingDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$deleteResultArray = AdminBranding_model::brandingDelete();
		}
	}

	static function unsubscribeAllUsers()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$brandingCode = UtilsObj::getArrayParam($_POST, 'code', '');
			$resultArray = AdminBranding_model::unsubscribeAllUsers($brandingCode);
			AdminBranding_view::unsubscribeAllUsers($resultArray);
		}
	}

	static function getBrandFilePreview()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$brandCode = UtilsObj::getGETParam('code', '');
			$brandID = UtilsObj::getGETParam('bid', '');
			$typeRef = UtilsObj::getGETParam('typeref', '');
			$useTempFile = UtilsObj::getGETParam('tmp', '');

			$resultArray = AdminBranding_model::getBrandFilePreview($brandID, $typeRef, $useTempFile);
		}
	}

	static function uploadBrandFile()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$typeRef = UtilsObj::getPOSTParam('typeref', '');
			$fileData = UtilsObj::getArrayParam($_FILES, 'preview', array());

			$resultArray = AdminBranding_model::uploadBrandFile($typeRef, $fileData);
			AdminBranding_view::uploadBrandFile($resultArray);
		}
	}

	public static function getAuthentication()
	{
		// Exit out of processing no admin session was active.
		if (1 !== AuthenticateObj::adminSessionActive()) {
			return;
		}

		global $gSession;
		global $ac_config;

		$providerId = (int) $_GET['provider'];
		$redirectUrl = $ac_config['WEBURL'] . ('/' !== substr($ac_config['WEBURL'], -1) ? '/' : '') . 'AdminBranding.authenticate';
		$provider = (new TaopixOAuthProvider($providerId, DatabaseObj::getGlobalDBConnection(), [
			'redirectUri' => $redirectUrl
		]))->getProvider();
		$stateString = strtr(base64_encode(json_encode([
			'ref' => $gSession['ref'],
			'provider' => $providerId,
		])), '+/=', '-_,');

		$authUrl = $provider->getAuthorizationUrl(['state' => $stateString]);
		$gSession['oauth2state'] = $provider->getState();
		DatabaseObj::updateSession();
		header('Location: ' . $authUrl);
		exit;
	}

	public static function authenticate()
	{
		if (empty($_GET['state'])) {
			return;
		}
		global $ac_config;

		// Decode the details of the state to get the session ref and provider.
		$details = json_decode(base64_decode(strtr($_GET['state'], '-_,', '+/=')), true);
		$_GET['ref'] = (int) $details['ref'];
		$providerId = (int) $details['provider'];

		// Configure the providerContainer
		$redirectUrl = $ac_config['WEBURL'] . ('/' !== substr($ac_config['WEBURL'], -1) ? '/' : '') . 'AdminBranding.authenticate';
		$providerContainer = new TaopixOAuthProvider($providerId, DatabaseObj::getGlobalDBConnection(), [
			'redirectUri' => $redirectUrl
		]);
		$provider = $providerContainer->getProvider();

		// We need to load the session as the initial request does not contain a ref param.
		$gSession = AuthenticateObj::getCurrentSessionData();

		// Exit out of processing no admin session was active.
		if (1 !== AuthenticateObj::adminSessionActive()) {
			return;
		}

		// States do not match stop processing
		if ($_GET['state'] !== $gSession['oauth2state']) {
			return;
		}

		// Generate an accessToken this is used to generate the refresh token and get the owner details.
		$token = $provider->getAccessToken(
			'authorization_code',
			[
				'code' => $_GET['code'],
			]
		);
		$refreshToken = $token->getRefreshToken();
		$details = $provider->getResourceOwner($token)->toArray();

		$tokenManager = new TaopixOAuthRefreshToken(DatabaseObj::getGlobalDBConnection());

		/**
		 * Refresh token can be null in instances where a provider will only allow one refresh token per app per login.
		 * We really only want the latest refresh token anyway, so if we get a new token remove all existing refresh tokens for this email address/provider combination.
		 */
		if (null === $refreshToken) {
			$tokenDetails = $tokenManager->findByParams(['providerid' => $providerId, 'authemail' => $details['email']]);
		} else {
			$existingTokens = $tokenManager->getCount(['providerid' => $providerId, 'authemail' => $details['email']]);
			if (0 < $existingTokens) {
				$tokenManager->clearExistingTokens(['providerid' => $providerId, 'authemail' => $details['email']]);
			}

			$tokenDetails = $tokenManager->insert(['providerid' => $providerId, 'authemail' => $details['email'], 'refreshtoken' => $refreshToken]);

		}

		// Send these values to the view, they will update in the window and propergate to the parent window.
		AdminBranding_view::authenticate([
			'authEmailAddress' => $details['email'],
			'authName' => $details['name'],
			'authToken' => $tokenDetails['refreshtoken'],
			'tokenId' => $tokenDetails['id'],
		]);
	}

	public static function downloadKey()
	{
		if (isset($_GET['IV'])) {
			AdminBranding_model::downloadKey(false, $_GET['IV']);
		} else {
			$regenerate = 1 == $_GET['generate'] ? true : false;
			AdminBranding_model::downloadKey($regenerate);
		}
	}
}
?>
