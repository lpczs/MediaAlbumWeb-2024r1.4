<?php

namespace Security;

use SmartyObj;
use UtilsObj;
use Welcome_model;
use Welcome_view;
use AuthenticateObj;

trait RequestValidationTrait
{
	public static function assertRequestMethod(array $methods)
	{
		if (!in_array($_SERVER['REQUEST_METHOD'], $methods)) {
			self::sendSecurityErrorPage(405, false, [
				'Allow' => strtoupper(implode(', ', $methods)),
			]);
			exit;
		}
	}

	public static function assertCsrfToken()
	{
		if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'DELETE'])) {
			global $ac_config;
			global $gSession;

			// Verify the integrity of the CSRF token
			require_once(__DIR__.'/../Utils/Utils.php');
			$signingKey = UtilsObj::getArrayParam($ac_config, 'CSRF_SIGNING_KEY');
			if (empty($signingKey)) {
				error_log('CSRF signing key parameter "CSRF_SIGNING_KEY" not set in configuration file.');
				self::sendSecurityErrorPage(403);
				exit;
			}

			$leeway = UtilsObj::getArrayParam($ac_config, 'CSRF_TOKEN_LEEWAY', 30);

			$validator = new CsrfValidator($signingKey, $leeway, $gSession);
			if (false === $validator->assert()) {
				// If the cookie itself has expired, just expire any authenticated session
				// and forcing the login page to be displayed.
				// This is needed for scenarios whereby an authenticated session expires
				// and the user refreshes the browser, simulating a new login on an expired
				// CSRF token, causing the security page to be displayed.
				// Prior behaviour was to redisplay the login page.
				$isMobile = isset($_REQUEST['mobile']) ? 'true' === $_REQUEST['mobile'] : false;

				if (false === $isMobile && CsrfValidator::ASSERT_CAUSE_EXPIRED_COOKIE === $validator->getFailedAssertionCause()) {
					$sessionId = AuthenticateObj::getSessionRef();
					$redirectURL = Welcome_model::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
					Welcome_view::processLogout($redirectURL, $sessionId, 0, 'str_ErrorSessionExpired');
				} else {
					self::sendSecurityErrorPage(403, $isMobile);
				}

				exit;
			}
		}
	}

	public static function sendSecurityErrorPage($statusCode, $isMobile = false, $headers = [])
	{
		global $gSession;

		http_response_code($statusCode);

		foreach ($headers as $headerName => $headerValue) {
			header($headerName, $headerValue);
		}

		if ($isMobile) {
			$redirectURL = Welcome_model::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
			header('Content-Type', 'application/json');
			echo '{"error": "", "redirecturl": "'.$redirectURL.'"}';
		} else {
			require_once(__DIR__.'/../Utils/UtilsSmarty.php');
			$smarty = SmartyObj::newSmarty('Security', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

			$smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));
			$smarty->displayLocale('security_check_failed.tpl');
		}
	}
}
