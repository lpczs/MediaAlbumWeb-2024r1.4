<?php

class createUnlockSystemAccountUser extends ExternalScript
{
	/**
	 * Prompts the user for a login and password for the  Unlock System Account script user.
	 */
	public function run()
	{
		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$unlockScriptUsername = '';
			$unlockScriptPassword = '';

			echo "\n Enter details for the Unlock System Account User. \n";
			echo "\n This user is used to run the unlockAccount script in the event the System Administrator account becomes locked out due to too many failed login attempts. \n\n";

			// Create the Unlock Script user account.
			$this->checkUnlockScriptAccountDetailsForNewInstallation($unlockScriptUsername, $unlockScriptPassword);

			// Hash the password using the PHP password format.
			$generatePasswordHash = AuthenticateObj::generatePasswordHash($unlockScriptPassword, TPX_PASSWORDFORMAT_CLEARTEXT);

			if ($generatePasswordHash['result'] == '')
			{
				$unlockScriptPasswordHash = $generatePasswordHash['data'];
			}
			else
			{
				// If the hash fails, fall back to md5. This will be upgraded if the unlock script is ever used.
				$unlockScriptPasswordHash = "MD5('" . $unlockScriptPassword . "')";
			}

			$userType = TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER;

			$sql = "INSERT INTO
				`USERS`
					(`datecreated`, `owner`, `login`, `password`, `customer`, `usertype`, `groupcode`, `companyname`, `address1`, `address2`, `address3`, `address4`, `city`, `county`, `state`,
						`regioncode`, `region`, `postcode`, `countrycode`, `countryname`, `telephonenumber`, `emailaddress`, `contactfirstname`, `contactlastname`, `currencycode`, `paymentmethods`,
						`taxcode`, `shippingtaxcode`, `registeredtaxnumber`, `sendmarketinginfo`, `protectfromredaction`, `active`)
				VALUES
					(NOW(), '', ?, ?, 0, ?, '', '', '', '', '', '', '', '', '',
						'', '', '', '', '', '', '', ?, '', '', '',
						'', '', '', 0, 1, 1)";

			if (($stmt = $dbObj->prepare($sql)))
			{
				if ($stmt->bind_param('ssis', $unlockScriptUsername, $unlockScriptPasswordHash, $userType, $unlockScriptUsername))
				{
					if (! $stmt->execute())
					{
						echo "\n" . __FUNCTION__ . ' execute failed. Error: ' . $dbObj->error . "\n\n";
					}
				}
				else
				{
					echo "\n" . __FUNCTION__ . ' bind_param failed. Error: ' . $dbObj->error . "\n\n";
				}
			}
			else
			{
				echo "\n" . __FUNCTION__ . ' prepare failed. Error: ' . $dbObj->error . "\n\n";
			}
		}
		else
		{
			echo "\n" . __FUNCTION__ . ' unable to establish connection with the database' . "\n\n";
		}
	}

	/**
	 * Prompts for a userlogin and password for the Unlock System Account user and makes sure they are populated.
	 * Since this runs at the end of the script, it is not possible to return to the action select menu at this point.
	 *
	 * @param string $pUnlockScriptUserName Unlock System Account user login, passed by reference.
	 * @param string $pUnlockScriptPassword Unlock System Account user password, passed by reference.
	 */
	private function checkUnlockScriptAccountDetailsForNewInstallation(&$pUnlockScriptUserName = '', &$pUnlockScriptPassword = '')
	{
		if ($pUnlockScriptUserName == '')
		{
			// Prompt for username if none set.
			echo "\n Enter Unlock System Account User username: ";
			$pUnlockScriptUserName = trim(fgets(STDIN));

			// Make sure a username was entered.
			while (trim($pUnlockScriptUserName) == '')
			{
				echo "\n Enter Unlock System Account User username: ";
				$pUnlockScriptUserName = trim(fgets(STDIN));
			}
		}

		if ($pUnlockScriptPassword == '')
		{
			// Prompt for password if none set.
			echo "\n Enter Unlock System Account User password: ";
			$pUnlockScriptPassword = trim(fgets(STDIN));

			// Make sure a password was entered.
			while ($pUnlockScriptPassword == '')
			{
				echo "\n Enter Unlock System Account User password: ";
				$pUnlockScriptPassword = trim(fgets(STDIN));
			}
		}

		// Warn users if their password is in the black list of weak password.
		$fileName = 'passwordBlackList.txt';
		$handle = fopen($fileName, "r");
		$passwordBlackListStr = fread($handle, filesize($fileName));
		fclose($handle);
		$passwordBlackListArr = explode(",", $passwordBlackListStr);

		while (in_array(md5($pUnlockScriptPassword), $passwordBlackListArr) || strlen($pUnlockScriptPassword) < 8)
		{
			if (strlen($pUnlockScriptPassword) < 8)
			{
				echo "\n The password should be at least 8 characters long, please choose a different one: ";
				$pUnlockScriptPassword = trim(fgets(STDIN));
			}
			else
			{
				echo "\n The password you entered appears to be unsecure, please choose a different one: ";
				$pUnlockScriptPassword = trim(fgets(STDIN));
			}
		}

		echo "\n Your Unlock System Account User is going to be created with the following details: \n";
		echo "\n Username: " . $pUnlockScriptUserName . " \n";
		echo " Password: " . $pUnlockScriptPassword . " \n";
		echo "\n Is this correct? (Y/N): ";

		$answer = strtoupper(trim(fgets(STDIN)));

		// Reset and prompt for a new login and password if user does not enter "Y".
		if ($answer != 'Y')
		{
			$pUnlockScriptUserName = '';
			$pUnlockScriptPassword = '';
			$this->checkUnlockScriptAccountDetailsForNewInstallation($pUnlockScriptUserName, $pUnlockScriptPassword);
		}
	}
}