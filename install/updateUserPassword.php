<?php

// Only PHP version 5.5 or greater are supported
if (! version_compare(PHP_VERSION, '5.5', '>='))
{
	echo "ERROR: The version of PHP is below the minimum recommended version, please update your PHP version and run this script again\n";
	exit;
}

require_once ('../Utils/UtilsConstants.php');
require_once ('../Utils/Utils.php');
require_once ('../Utils/UtilsAuthenticate.php');
require_once ('../Utils/UtilsDatabase.php');

// OS Types
define('TPX_OS_TYPE_WINDOWS', 0);
define('TPX_OS_TYPE_UNIX', 1);
define('TPX_OS_TYPE_MAC', 2);
define('TPX_PASSWORDRECORD_BATCH_SIZE', 5000);

// remove the script timeout
set_time_limit(0);

// read the config file for Control Centre
$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

$resultArray = array();

$error = '';
$errorParam = '';

clearScreen();
$startRecordID = 0;
$counter = 1;

do {

	echo "Processing user data batch " . $counter . " ...\n";

	$getUsersDataResult = getUsers($startRecordID);

	if ($getUsersDataResult['error'] == '')
	{
		$updateUserPasswordResult = updateUserPasswordToNewFormat($getUsersDataResult['data']['users']);

		if ($updateUserPasswordResult['result'] != '')
		{
			$error = $updateUserPasswordResult['result'];
			$errorParam = $updateUserPasswordResult['resultparam'];

			echo "\n" . $updateUserPasswordResult['errorparam'];
		}
	}
	else
	{
		echo "\n" . $getUsersDataResult['errorparam'];
	}

	echo "...Complete\n\n";

	$counter++;
	$startRecordID = $getUsersDataResult['data']['lastrecordid'];
}
while ($startRecordID != 0);

/**
 * clearScreen
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
		system('cls');
	}
	else
	{
		system('clear');
	}
}

/**
 * getUsers
 *  - get user id and password for updating in batches based of the TPX_PASSWORDRECORD_BATCH_SIZE constant
 *	  ignore _taopixlicenseserver and the licence server usertype
 */
function getUsers($pStartID)
{
	$resultArray = array('error' => '', 'errorparam' =>'', 'data' => array());
	$error = '';
	$errorParam = '';
	$userListArray = array('users' => array(), 'lastrecordid' => 0);

	$dbObj = DatabaseObj::getGlobalDBConnection();

	if ($dbObj)
	{
		$sql = "SELECT `id`, `password`
				FROM `USERS`
				WHERE `login` != '_taopixlicenseserver'
				AND `id` > ?
				AND SUBSTR(`password`, 1, 1) != '$'
				AND SUBSTR(`password`, 1, 1)  != '+'
				AND `usertype` != " . TPX_LOGIN_LICENCE_SERVER_API . ' LIMIT ' . TPX_PASSWORDRECORD_BATCH_SIZE;

		if ($stmt = $dbObj->prepare($sql))
		{
			if ($stmt->bind_param('i',  $pStartID))
			{
				if ($stmt->bind_result($userID, $password))
				{
					if ($stmt->execute())
					{
						while($stmt->fetch())
						{
							$user = array();
							$user['id'] = $userID;
							$user['password'] = $password;

							$userListArray['users'][$userID] = $user;
							$userListArray['lastrecordid'] = $userID;
						}
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' execute error: ' . $dbObj->error;
					}
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' bind result error: ' . $dbObj->error;
				}
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' bind error: ' . $dbObj->error;
			}
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
		}

		if ($stmt)
		{
			$stmt->free_result();
			$stmt->close();
			$stmt = null;
		}

		$dbObj->close();
	}
	else
	{
		$error = 'str_DatabaseError';
		$errorParam = __FUNCTION__ . ' connection error: ' . $dbObj->error;
	}

	$resultArray['error'] = $error;
	$resultArray['errorparam'] = $errorParam;
	$resultArray['data'] = $userListArray;

	return $resultArray;
}

/**
 * updateUserPasswordToNewFormat
 *  - update user passwords to new format
 *	  ignore any passwords that might have already been converted
 */
function updateUserPasswordToNewFormat($pUserDataArray)
{
	$result = '';
	$resultParam = '';
	$returnArray = array('result' => '', 'resultparam' => '');
	$started = time();

	$dbObj = DatabaseObj::getGlobalDBConnection();

	if ($dbObj)
	{
		$dbObj->query('START TRANSACTION');

		$sql = "UPDATE `USERS` SET `password` = ?
				WHERE `id` = ?
					AND SUBSTR(`password`, 1, 1) != '$'
					AND SUBSTR(`password`, 1, 1)  != '+'";

		if ($stmt = $dbObj->prepare($sql))
		{
			foreach($pUserDataArray as $userData)
			{
				$result = '';
				$resultParam = '';

				$generatePasswordHashResult = AuthenticateObj::generatePasswordHash($userData['password'], TPX_PASSWORDFORMAT_MD5);

				if ($generatePasswordHashResult['result'] == '')
				{
					if ($generatePasswordHashResult['data'] != '')
					{
						$userData['password'] = $generatePasswordHashResult['data'];
					}
					else
					{
						$result = 'str_Error';
						$resultParam = 'Invalid password';
					}
				}
				else
				{
					$result = $generatePasswordHashResult['result'];
					$resultParam = $generatePasswordHashResult['resultparam'];
				}

				if ($result == '')
				{
					if ($stmt->bind_param('si', $userData['password'], $userData['id']))
					{
						if (! $stmt->execute())
						{
							// could not execute the statement
							$result = 'str_DatabaseError';
							$resultParam = __FUNCTION__ . ' execute failed ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = __FUNCTION__ . ' bind_param failed ' . $dbObj->error;
					}
				}
			}

			$dbObj->query('COMMIT');

			$stmt->free_result();
			$stmt->close();
			$stmt = null;
		}
		else
		{
			// could not open a database connection
			$result = 'str_DatabaseError';
			$resultParam = __FUNCTION__ .  ' unable to get database ' . $dbObj->error;
		}

		$dbObj->close();
	}

	$returnArrray['result'] = $result;
	$returnArrray['resultparam'] = $resultParam;

	return $returnArray;
}

?>