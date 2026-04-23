<?php

namespace Security;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use UtilsObj;

class CsrfValidator
{
	const ASSERT_CAUSE_EXPIRED_COOKIE = 'cookie_expired';
	const ASSERT_CAUSE_CROSS_ORIGIN_REQUEST = 'cross_origin_request';
	const ASSERT_CAUSE_MISSING_TOKEN = 'missing_token';
	const ASSERT_CAUSE_TOKEN_MISMATCH = 'token_mismatch';

	/**
	 * @var string
	 */
	private $signingKey;

	/**
	 * @var string
	 */
	private $leeway;

	/**
	 * @var mixed[]
	 */
	private $session;

	/**
	 * @var string
	 */
	private $failed_assertion_cause;

	/**
	 * Constructor
	 *
	 * @param string $signingKey
	 * @param int $leeway
	 * @param mixed[] $session
	 */
	public function __construct($signingKey, $leeway, &$session)
	{
		$this->signingKey = $signingKey;
		$this->leeway = $leeway;
		$this->session = &$session;
	}

	/**
	 * Assert the request against CSRF attacks
	 *
	 * @return bool
	 */
	public function assert()
	{
		$hasSessionStarted = isset($this->session['ref']) && $this->session['ref'] > 0;

		// Attempt validation of Origin/Referer headers if present
		$origin = null;
		$matches = null;

		if (isset($_SERVER['HTTP_ORIGIN'])) {
			$origin = $_SERVER['HTTP_ORIGIN'];
		} elseif (isset($_SERVER['HTTP_REFERER']) && preg_match('/(^http(s)?:\/\/.*?)\//', $_SERVER['HTTP_REFERER'], $matches)) {
			$origin = $matches[1];
		}

		if (isset($origin)) {

			// determine the http scheme from the _SERVER var
			$scheme = UtilsObj::getHTTPScheme($_SERVER);

			$target = $scheme . $_SERVER['HTTP_HOST'];

			if ($origin !== $target) {
				$this->failed_assertion_cause = self::ASSERT_CAUSE_CROSS_ORIGIN_REQUEST;
				return false;
			}
		}

		if (!isset($_POST['csrf_token'])) {
			$this->failed_assertion_cause = self::ASSERT_CAUSE_MISSING_TOKEN;
			return false;
		}

		if (!$hasSessionStarted && !isset($_COOKIE['csrf_token'])) {
			$this->failed_assertion_cause = self::ASSERT_CAUSE_EXPIRED_COOKIE;
			return false;
		}

		$formToken = $_POST['csrf_token'];
		if (empty($formToken)) {
			$this->failed_assertion_cause = self::ASSERT_CAUSE_MISSING_TOKEN;
			return false;
		}

		if (!$hasSessionStarted) {
			$cookieTokenData = $_COOKIE['csrf_token'];
			$jwt = [];

			try {
				$leeway = JWT::$leeway;
				JWT::$leeway = $this->leeway;
				$jwt = (array)JWT::decode($cookieTokenData, new Key($this->signingKey, 'HS256'));
			} catch (Exception $ex) {
				$this->failed_assertion_cause = self::ASSERT_CAUSE_EXPIRED_COOKIE;
				return false;
			} finally {
				JWT::$leeway = $leeway;
			}

			// Check matching cookie
			if (!isset($jwt['jti']) || $jwt['jti'] !== $formToken) {
				$this->failed_assertion_cause = self::ASSERT_CAUSE_TOKEN_MISMATCH;
				return false;
			}
		}

		// Check matching session (if a session exists)
		if ($hasSessionStarted &&
			isset($this->session['csrftoken']) && $this->session['csrftoken'] !== $formToken) {
			$this->failed_assertion_cause = self::ASSERT_CAUSE_TOKEN_MISMATCH;
			return false;
		}

		$this->failed_assertion_cause = null;
		return true;
	}

	/**
	 * Get the cause of a failed assetion
	 *
	 * @return string
	 */
	public function getFailedAssertionCause()
	{
		return $this->failed_assertion_cause;
	}
}
