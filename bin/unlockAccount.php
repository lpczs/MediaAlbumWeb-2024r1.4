<?php
/**
 * Script to unlock a System Administrator account that has been locked due too many failed login attemps.
 */

// OS Types.
define('TPX_OS_TYPE_WINDOWS', 0);
define('TPX_OS_TYPE_UNIX', 1);
define('TPX_OS_TYPE_MAC', 2);

define('__ROOT__', realpath(dirname(dirname(__FILE__))));

// Include required files.
require_once __ROOT__ . '/Utils/UtilsDatabase.php';
require_once __ROOT__ . '/Utils/UtilsConstants.php';
require_once __ROOT__ . '/Utils/Utils.php';
require_once __ROOT__ . '/Utils/UtilsAuthenticate.php';

// Set unlimited script timeout.
set_time_limit(0);

class unLockAccount
{
	private $dbObj;
	private $config;
	private $errorStrings = array
	(
		'str_DatabaseError' => 'Database Error',
		'str_ErrorNoAccount' => 'An account with these details does not exist'
	);
	private $scriptUserLogin = '';

	function __construct($pConfig, $pScriptUser = '')
	{
		// Establish a connection to the database.
		$this->dbObj = DatabaseObj::getGlobalDBConnection();

		// Clear the screen.
		$this->clearScreen();

		if ($pScriptUser != '')
		{
			$this->scriptUserLogin = trim($pScriptUser);
		}

		if ($this->dbObj)
		{
			$this->config = $pConfig;

			$this->printMsg('Taopix Administrator User Unlock Script', false);

			// Start the process.
			$this->init();
		}
		else
		{
			$this->printError('str_DatabaseError', 'Unable to connect to database.');
		}
	}

	/*
	 * Prompts for the script users credentials.
	 */
	function init()
	{
		$scriptUserLogin = '';
		$scriptUserPassword = '';
		$loginToUnlock = '';
		$continue = '';

		// User was valid, prompt for the System Account user to unlock.
		$this->promptForUserToUnlock($loginToUnlock);

		if ($this->scriptUserLogin != '')
		{
			$scriptUserLogin = $this->scriptUserLogin;
		}
		else
		{
			// Prompt for the login name until one if entered.
			while ($scriptUserLogin == '')
			{
				$this->printMsg('Enter script user login: ', true, false);
				$scriptUserLogin = $this->promptForInput();
			}
		}

		// Prompt for the password until one if entered.
		while ($scriptUserPassword == '')
		{
			$this->printMsg('Enter script user password: ', true, false);
			$scriptUserPassword = $this->promptForInput();
		}

		// Try to authenticate the user using the provided details.
		$authenticateScriptUserLoginResult = $this->authenticateScriptUserLogin($scriptUserLogin, $scriptUserPassword);

		// Clear the screen to remove the login & password details from being visible on screen.
		$this->clearScreen();

		if ($authenticateScriptUserLoginResult['error'] == '')
		{
			// Attempt to unlock the System Account.
			$unlockAccountResult = $this->unlockAccount($loginToUnlock);

			// Clear the screen to remove the user login being visible on screen.
			$this->clearScreen();

			if ($unlockAccountResult['error'] == '')
			{
				$this->printMsg($unlockAccountResult['data'], false);

				while ($continue == '')
				{
					$this->printMsg('Do you wish to unlock another account? (Y/N): ', true, false);
					$continue = $this->promptForInput();
				}

				if ($continue === 'Y')
				{
					// Restart the script, including asking to login as the Unlock System Account user again.
					$this->init();
				}
				else
				{
					// Stop the script.
					exit;
				}
			}
			else
			{
				$this->printError($unlockAccountResult['error'], $unlockAccountResult['errorparam'], false);
			}
		}
		else
		{
			$this->printError($authenticateScriptUserLoginResult['error'], $authenticateScriptUserLoginResult['errorparam'], false);
		}
	}

	/**
	 * Get user input from STDIN.
	 *
	 * @return string The user entered text.
	 */
	function promptForInput()
	{
		return trim(fgets(STDIN));
	}

	/*
	 * Prompts the user for the login of the user to unlock.
	 *
	 * @param string $pLoginToUnlock The userlogin of the account to unlock, passed by reference.
	 */
	function promptForUserToUnlock(&$pLoginToUnlock)
	{
		// Prompt for the System User login until one is entered.
		while ($pLoginToUnlock == '')
		{
			$this->printMsg('Enter login of user to unlock: ', true, false);
			$pLoginToUnlock = $this->promptForInput();
		}
	}

	/**
	 * Attempts to unlock the supplied user acount.
	 *
	 * @param string $pLogin
	 * @return array Array containing the result of the user unlock.
	 */
	function unlockAccount($pLogin)
	{
		$returnArray = UtilsObj::getReturnArray();

		// Check the user exists.
		$getUserResult = $this->getUserID($pLogin);

		if ($getUserResult['error'] == '')
		{
			if ($getUserResult['userid'] > 0)
			{
				// Account has been found, attempt to unlock the user.
				$unlockUserAccountResult = $this->unlockUserAccount($getUserResult['userid']);

				if ($unlockUserAccountResult['error'] == '')
				{
					$returnArray['data'] = 'Account unlocked successfully.';
				}
				else
				{
					$returnArray['error'] = $unlockUserAccountResult['error'];
					$returnArray['errorparam'] = $unlockUserAccountResult['errorparam'];
				}
			}
			else
			{
				$returnArray['error'] = 'str_ErrorNoAccount';
				$returnArray['errorparam'] = '';
			}
		}
		else
		{
			$returnArray['error'] = $getUserResult['error'];
			$returnArray['errorparam'] = $getUserResult['errorparam'];
		}

		return $returnArray;
	}

	/**
	 * Authenticate the admin script user using the supplied login and password.
	 *
	 * @param string $pLogin
	 * @param string $pPassword
	 * @return array Array containing if the authenticate was valid or not.
	 */
	function authenticateScriptUserLogin($pLogin, $pPassword)
	{
		$returnArray = UtilsObj::getReturnArray('valid');
		$userID = 0;
		$passwordHash = '';
		$userType = 0;
		$active = 0;

		$sql = "SELECT
					`id`, `password`, `usertype`, `active`
				FROM
					`USERS`
				WHERE
					`login` = ?";

		if (($stmt = $this->dbObj->prepare($sql)))
		{
			if ($stmt->bind_param('s', $pLogin))
			{
				if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						if ($stmt->num_rows == 1)
						{
							if ($stmt->bind_result($userID, $passwordHash, $userType, $active))
							{
								if ($stmt->fetch())
								{
									/*
									 *  Work out the format of the password is stored as.
									 *  The password on the user account can be changed via the Admin interface which may change the format so we need
									 *  work out the format by reading the first character in the password hash.
									 */
									$password = $pPassword;
									$firstCharacter = substr($passwordHash, 0, 1);
									$passwordFormat = TPX_PASSWORDFORMAT_CLEARTEXT;

									switch ($firstCharacter)
									{
										case '$':
										{
											// Password is the PHP format.
											$passwordFormat = TPX_PASSWORDFORMAT_CLEARTEXT;
											break;
										}
										case '+':
										default:
										{
											// Password is the md5 or md5+ format.
											$password = hash('md5', $password);
											$passwordFormat = TPX_PASSWORDFORMAT_MD5;
											break;
										}
									}

									// Check the password matches.
									$verifyPasswordResult = AuthenticateObj::verifyPassword($password, $passwordHash, $passwordFormat);

									if ($verifyPasswordResult['data']['passwordvalid'])
									{
										// Verify the account supplied is the Unlock System Account user.
										if ($userType == TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER)
										{
											// Check if the Unlock System Account user is active.
											if ($active == 1)
											{
												$returnArray['valid'] = true;
											}
											else
											{
												$returnArray['error'] = 'str_ErrorNoAccount';
												$returnArray['errorparam'] = 'This account is not active.';
											}
										}
										else
										{
											$returnArray['error'] = 'str_ErrorNoAccount';
											$returnArray['errorparam'] = 'This is not a valid Unlock System Account User account.';
										}
									}
									else
									{
										$returnArray['error'] = 'str_ErrorNoAccount';
										$returnArray['errorparam'] = 'This is not a valid Unlock System Account User account.';
									}

									// Verify the password hash is up to date, the version of PHP may have changed since the hash was generated last.
									if ($verifyPasswordResult['data']['verifypasswordhash'])
									{
										$passwordNeedsRehashResult = AuthenticateObj::checkPasswordNeedsRehash($passwordHash);

										if ($passwordNeedsRehashResult)
										{
											// Password hash needs updating, rehash it.
											AuthenticateObj::rehashUserPassword($pPassword, $userID, TPX_PASSWORDFORMAT_CLEARTEXT);
										}
									}
								}
								else
								{
									$returnArray['error'] = 'str_DatabaseError';
									$returnArray['errorparam'] = __FUNCTION__ . ' fetch error. ' . $this->dbObj->error;
								}
							}
							else
							{
								$returnArray['error'] = 'str_DatabaseError';
								$returnArray['errorparam'] = __FUNCTION__ . ' bind_result error. ' . $this->dbObj->error;
							}
						}
						else
						{
							$returnArray['error'] = 'str_ErrorNoAccount';
							$returnArray['errorparam'] = 'This is not a valid Unlock System Account User account.';
						}
					}
				}
				else
				{
					$returnArray['error'] = 'str_DatabaseError';
					$returnArray['errorparam'] = __FUNCTION__ . ' execute error. ' . $this->dbObj->error;
				}
			}
			else
			{
				$returnArray['error'] = 'str_DatabaseError';
				$returnArray['errorparam'] = __FUNCTION__ . ' bind_params error. ' . $this->dbObj->error;
			}

			$stmt->free_result();
			$stmt->close();
		}
		else
		{
			$returnArray['error'] = 'str_DatabaseError';
			$returnArray['errorparam'] = __FUNCTION__ . ' prepare error. ' . $this->dbObj->error;
		}

		return $returnArray;
	}

	/**
	 * Returns the user id if the user exists and is a System Administrator.
	 *
	 * @param string $pLogin The userlogin of the account to unlock.
	 * @return array Array containing the user id.
	 */
	function getUserID($pLogin)
	{
		$returnArray = UtilsObj::getReturnArray('userid');
		$returnArray['userid'] = 0;
		$userID = 0;

		$sql = "SELECT
					`id`
				FROM
					`USERS`
				WHERE
					`login` = ?
				AND
					`usertype` = " . TPX_LOGIN_SYSTEM_ADMIN . "
				AND
					`customer` = 0";

		if (($stmt = $this->dbObj->prepare($sql)))
		{
			if ($stmt->bind_param('s', $pLogin))
			{
				if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						if ($stmt->num_rows == 1)
						{
							if ($stmt->bind_result($userID))
							{
								if ($stmt->fetch())
								{
									$returnArray['userid'] = $userID;
								}
								else
								{
									$returnArray['error'] = 'str_ErrorNoAccount';
									$returnArray['errorparam'] = '';
								}
							}
							else
							{
								$returnArray['error'] = 'str_DatabaseError';
								$returnArray['errorparam'] = __FUNCTION__ . ' bind_result error. ' . $this->dbObj->error;
							}
						}
						else
						{
							$returnArray['error'] = 'str_ErrorNoAccount';
							$returnArray['errorparam'] = '';
						}
					}
				}
				else
				{
					$returnArray['error'] = 'str_DatabaseError';
					$returnArray['errorparam'] = __FUNCTION__ . ' execute error. ' . $this->dbObj->error;
				}
			}
			else
			{
				$returnArray['error'] = 'str_DatabaseError';
				$returnArray['errorparam'] = __FUNCTION__ . ' bind_params error. ' . $this->dbObj->error;
			}

			$stmt->free_result();
			$stmt->close();
		}
		else
		{
			$returnArray['error'] = 'str_DatabaseError';
			$returnArray['errorparam'] = __FUNCTION__ . ' prepare error. ' . $this->dbObj->error;
		}

		return $returnArray;
	}

	/**
	 * Resets fields on the account to unlock them.
	 *
	 * @param int $pUserID ID of the user to reset.
	 * @return array Array containing the result of the update.
	 */
	function unlockUserAccount($pUserID)
	{
		$returnArray = UtilsObj::getReturnArray('affectedrows');
		$blockReason = TPX_BLOCK_REASON_NONE;

		// Unlock the account by resetting the nextvalidlogindate and loginattemptcount values.
		$sql = "UPDATE `USERS` SET `nextvalidlogindate` = '0000-00-00 00:00:00', `loginattemptcount` = 0, `blockreason` = ? WHERE `id` = ?";

		if (($stmt = $this->dbObj->prepare($sql)))
		{
			if ($stmt->bind_param('ii', $blockReason, $pUserID))
			{
				if ($stmt->execute())
				{
					$returnArray['affectedrows'] = $stmt->affected_rows;
				}
				else
				{
					$returnArray['error'] = 'str_DatabaseError';
					$returnArray['errorparam'] = __FUNCTION__ . ' execute error. ' . $this->dbObj->error;
				}
			}
			else
			{
				$returnArray['error'] = 'str_DatabaseError';
				$returnArray['errorparam'] = __FUNCTION__ . ' bind_params error. ' . $this->dbObj->error;
			}
		}
		else
		{
			$returnArray['error'] = 'str_DatabaseError';
			$returnArray['errorparam'] = __FUNCTION__ . ' prepare error. ' . $this->dbObj->error;
		}

		return $returnArray;
	}

	/**
	 * Prints a string to the screen.
	 *
	 * @param string $pString Message to display.
	 * @param string $pInsertEOL False to not prepend a new line to the string.
	 */
	function printMsg($pString, $pPrependEOL = true, $pAppendEOL = true)
	{
		if ($pPrependEOL)
		{
			echo PHP_EOL;
		}

		echo $pString;

		if ($pAppendEOL)
		{
			echo PHP_EOL;
		}
	}

	/**
	 * Prints error messages to the screen.
	 *
	 * @param string $pError Error message string, this can be a language string.
	 * @param string $pErrorParam Additional information about the error.
	 */
	function printError($pError, $pErrorParam, $pPrependEOL = true)
	{
		$error = $this->errorStrings[$pError];

		$this->printMsg('Error: ' . $error);

		if ($pErrorParam != '')
		{
			$this->printMsg($pErrorParam, $pPrependEOL);
		}
	}

	/**
	 * Clears the terminal screen.
	 */
	function clearScreen()
	{
		$osType = TPX_OS_TYPE_UNIX;

		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
		{
			$osType = TPX_OS_TYPE_WINDOWS;
		}
		else if (strtoupper(substr(PHP_OS, 0, 3)) === 'DAR')
		{
			$osType = TPX_OS_TYPE_MAC;
		}

		if ($osType == TPX_OS_TYPE_WINDOWS)
		{
			popen('cls', 'w');
		}
		else
		{
			system('clear');
		}
	}

	function __destruct()
	{
		// Make sure the database connection is closed when the script ends.
		$this->dbObj->close();
	}
}

// Read the config file.
$ac_config = UtilsObj::readConfigFile(__ROOT__ . '/config/mediaalbumweb.conf');

// Check if the script user login is passed in via a parameter.
$scriptUserLogin = '';

if (isset($argv[1]))
{
	$scriptUserLogin = $argv[1];
}

$unLockAccount = new unLockAccount($ac_config, $scriptUserLogin);