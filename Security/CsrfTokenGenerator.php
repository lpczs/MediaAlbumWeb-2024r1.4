<?php

namespace Security;

use Exception;
use Firebase\JWT\JWT;

class CsrfTokenGenerator
{
	/**
	 * Default token expiry seconds leeway of the JWT expiry claim
	 * (3 seconds, configurable through the CSRF_TOKEN_LEEWAY parameter)
	 */
	const DEFAULT_TOKEN_LEEWAY = 3;

	/**
	 * Default expiry of the cookie token using the JWT expiry claim
	 * (60 minutes, configurable through the SESSIONDURATION parameter)
	 */
	const DEFAULT_EXPIRY_TIME = 60;

	/**
	 * @var CsrfTokenGenerator
	 */
	private static $instance;

	/**
	 * @var bool
	 */
	private static $resumeCookie = true;

	/**
	 * @var string
	 */
	private $signingKey;

	/**
	 * @var int
	 */
	private $leeway;

	/**
	 * @var int
	 */
	private $expiry;

	/**
	 * @var bool
	 */
	private $requireSecureCookies;

	/**
	 * @var mixed[]
	 */
	private $session;

	/**
	 * @var callable
	 */
	private $sessionCallback;

	/**
	 * @var callable
	 */
	private $setCookieCallback;

	/**
	 * @var string
	 */
	private $token;

	/**
	 * Get an existing singleton instance of CsrfTokenGenerator
	 *
	 * @return CsrfTokenGenerator
	 */
	public static function getInstance()
	{
		return self::$instance;
	}

	/**
	 * Create a singleton instance of CsrfTokenGenerator
	 *
	 * @param string $signingKey
	 * @param int $leeway
	 * @param int $expiry
	 * @param bool $requireSecureCookies
	 * @param mixed[] $session
	 * @param callable $sessionCallback
	 * @param callable|null $setCookieCallback
	 * @return self
	 */
	public static function createInstance($signingKey, $leeway, $expiry, $requireSecureCookies, &$session, callable $sessionCallback, callable $setCookieCallback = null)
	{
		if (null === self::$instance) {
			self::$instance = new self($signingKey, $leeway, $expiry, $requireSecureCookies, $session, $sessionCallback, $setCookieCallback);
		}

		return self::$instance;
	}

	/**
	 * Under normal behaviour, an existing cookie is reloaded with the token within it
	 * reused when generating the token. It may be necessary for the cookie to be ignored
	 * and to force a new token to be generated.
	 *
	 * @param bool $resumeCookie
	 */
	public static function allowResumeCookie($resumeCookie)
	{
		self::$resumeCookie = $resumeCookie;
	}

	/**
	 * Constructor
	 *
	 * @param string $signingKey
	 * @param int $leeway
	 * @param int $expiry
	 * @param bool $requireSecureCookies
	 * @param mixed[] $session
	 * @param callable $sessionCallback
	 * @param callable|null $setCookieCallback
	 */
	private function __construct($signingKey, $leeway, $expiry, $requireSecureCookies, &$session, callable $sessionCallback, callable $setCookieCallback = null)
	{
		$this->signingKey = $signingKey;
		$this->leeway = $leeway;
		$this->expiry = $expiry;
		$this->requireSecureCookies = $requireSecureCookies;
		$this->session = &$session;
		$this->sessionCallback = $sessionCallback;

		if (null === $setCookieCallback) {
			$this->setCookieCallback = function($jwt, $secure) {
				setcookie('csrf_token', $jwt, 0, '/', '', $secure, true);
			};
		} else {
			$this->setCookieCallback = $setCookieCallback;
		}
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function generateToken()
	{
		$token = null;
		$hasSessionStarted = isset($this->session['ref']) && $this->session['ref'] > 0;

		// Reload the token from session or the cookie if not previously loaded
		if (null === $this->token && $hasSessionStarted && isset($this->session['csrftoken']) && !empty($this->session['csrftoken'])) {
			$this->token = $this->session['csrftoken'];
		} elseif (null === $this->token && self::$resumeCookie && isset($_COOKIE['csrf_token'])) {
			$token = $this->decodeJWT($_COOKIE['csrf_token'], $this->signingKey, $this->leeway);
		}

		if (null === $this->token && null === $token) {
			// No token previously generated and could not reload from a cookie. Generate a new one.
			$token = bin2hex(random_bytes(32));
			$jwt = null;
		}

		if (null === $this->token) {
			// Encode the token if its not been previously set
			$this->token = $token;

			// Link the token to the session for authenticated sessions
			// Send back a cookie for unauthenticated sessions
			if ($hasSessionStarted) {
				$this->session['csrftoken'] = $token;
				call_user_func($this->sessionCallback);
			} else {
				$jwt = $this->encodeJWT($token, $this->signingKey, $this->leeway, $this->expiry);
				call_user_func($this->setCookieCallback, $jwt, $this->requireSecureCookies);
			}
		}

		return $this->token;
	}

	/**
	 * Decode a JSON web token (JWT) and validate.
	 * Returns the validated decoded CSRF token inside, or null.
	 *
	 * @param string $token
	 * @param string $signingKey
	 * @param int $leewayAllowance
	 * @return string|null
	 */
	private function decodeJWT($token, $signingKey, $leewayAllowance)
	{
		$jwt = [];

		try {
			// Verify the integrity of the cookie token using JWT
			$leeway = JWT::$leeway;
			JWT::$leeway = $leewayAllowance;
			$jwt = (array) JWT::decode($token, $signingKey);
		} catch (Exception $ex) {
			return null;
		} finally {
			JWT::$leeway = $leeway;
		}

		return isset($jwt['jti']) ? $jwt['jti'] : null;
	}

	/**
	 * Encode a JSON web token. Returns the encoded JWT.
	 *
	 * @param string $token
	 * @param string $signingKey
	 * @param int $leewayAllowance
	 * @param int $tokenExpiryTime
	 * @return string|null
	 */
	private function encodeJWT($token, $signingKey, $leewayAllowance, $tokenExpiryTime)
	{
		try {
			$leeway = JWT::$leeway;
			JWT::$leeway = $leewayAllowance;

			return JWT::encode([
				'jti' => $token,
				'exp' => time() + (int) $tokenExpiryTime,
			], $signingKey, 'HS256');
		} catch (Exception $ex) {
			return null;
		} finally {
			JWT::$leeway = $leeway;
		}
	}
}
