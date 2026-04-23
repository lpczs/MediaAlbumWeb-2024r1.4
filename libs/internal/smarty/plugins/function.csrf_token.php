<?php

use Security\CsrfTokenGenerator;

require_once(__DIR__.'/../../../../Utils/UtilsDatabase.php');

/**
 * @param array $params
 * @param Smarty_Internal_Template $template
 * @return string
 * @throws Exception
 */

function smarty_function_csrf_token(array $params, Smarty_Internal_Template $template)
{
	if (null === $instance = CsrfTokenGenerator::getInstance()) {
		global $gSession;
		global $ac_config;

		$signingKey = UtilsObj::getArrayParam($ac_config, 'CSRF_SIGNING_KEY');
		if (empty($signingKey)) {
			error_log('CSRF signing key parameter "CSRF_SIGNING_KEY" not set in configuration file.');
			return null;
		}

		$leeway = UtilsObj::getArrayParam($ac_config, 'CSRF_TOKEN_LEEWAY', CsrfTokenGenerator::DEFAULT_TOKEN_LEEWAY);
		$expiry = UtilsObj::getArrayParam($ac_config, 'SESSIONDURATION', CsrfTokenGenerator::DEFAULT_EXPIRY_TIME) * 60;
		$requireSecureCookies = UtilsObj::needSecureCookies();

		$instance = CsrfTokenGenerator::createInstance($signingKey, $leeway, $expiry, $requireSecureCookies, $gSession, function() {
			DatabaseObj::updateSession();
		});
	}

	return $instance->generateToken();
}