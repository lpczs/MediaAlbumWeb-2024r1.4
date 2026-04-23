<?php

// OS Types
define('TPX_OS_TYPE_WINDOWS', 0);
define('TPX_OS_TYPE_UNIX', 1);
define('TPX_OS_TYPE_MAC', 2);


define('__ROOT__', dirname(dirname(__FILE__)));

$isOnline = (file_exists(__ROOT__ . '/config/taopixonline.conf'));

$fixFileName = 'clientgroupdata.txt';

clearScreen();

// read the command line parameters
$scriptOptions = readParameters();

// remove the script timeout
set_time_limit(0);

if ($isOnline)
{
	echo "Online.\n\n";

	// include install flag object
	require_once(dirname(__FILE__) . '/UtilsInstallFlags.php');

	require_once(__ROOT__ . '/libs/internal/Utils.php');
	require_once(__ROOT__ . '/libs/internal/DatabaseObj.php');

	// read the config file for TAOPIX Online
	$gConfig = UtilsObj::readConfigFile(__ROOT__ . '/config/taopixonline.conf');

	$workingDB = UtilsObj::getWorkingDBName();
	$projectsDB = UtilsObj::getWorkingDBName();

	// Stage 1.
	// update from active sessions
	if (($scriptOptions['stage'] & 1) == 1)
	{
		if (($scriptOptions['db'] & 1) == 1)
		{
			$UpdateWorkingResult = updateHeaderFromSessions($workingDB);
		}

		if (($scriptOptions['db'] & 2) == 2)
		{
			$UpdateProjectsResult = updateHeaderFromSessions($projectsDB);
		}
	}

	// Stage 2.
	// Read data from a file exported from control centre and update working.
	if ((file_exists($scriptOptions['file'])) && (($scriptOptions['stage'] & 2) == 2))
	{
		// open the file
		$groupDataFileContent = file($scriptOptions['file'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		if (count($groupDataFileContent) > 0)
		{
			if (($scriptOptions['db'] & 1) == 1)
			{
				$UpdateWorkingResult = updateHeaderFromFile($workingDB, $groupDataFileContent);
			}

			if (($scriptOptions['db'] & 2) == 2)
			{
				$UpdateProjectsResult = updateHeaderFromFile($projectsDB, $groupDataFileContent);
			}
		}
	}

	// Stage 3.
	// Copy any data from projects to working
	if (($scriptOptions['stage'] & 4) == 4)
	{
		$UpdateResult = updateWorkingFromProjects();
	}

		// set the initial value of the installflags field
	$flagArray[TPX_INSTALLFLAG_ONLINECLIENTGROUPDATA] = TPX_INSTALLFLAG_ONLINECLIENTGROUPDATA;

	$dbObj = DatabaseObj::getGlobalDBConnection();

	InstallFlagsObj::updateInstallFlags($dbObj, UtilsObj::getDBName(), $flagArray, false);
}
else
{
	echo "Control Centre.\n\n";

	require_once('../Utils/UtilsDatabase.php');
	require_once('../Utils/Utils.php');

	// read the config file for Control Centre
	$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

	// create a file to pass to the online execution listing clientgroup and user id
	$customerData = getUserList();

	// export group codes and users
	foreach ($customerData['data'] as $code => $userList)
	{
		$outputStr = $code . ":" . implode(',', $userList) . "\n";

		file_put_contents($scriptOptions['file'], $outputStr, FILE_APPEND | LOCK_EX);

		echo ".";
	}

	// display message to outline the next steps for action online
	echo "\nUpdate file created.\n";
	echo "Please refer to the upgrade documentation to continue.\n";
}


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
 * readParameters
 */
function readParameters()
{
	$parametersArray = array("s:" => "stage:", "d:" => "db:", "f:" => "file:");
	$arrayCleanupConfig = array();

	$cliOptionsArray = getopt(implode('', array_keys($parametersArray)), $parametersArray);

	$arrayCleanupConfig['file'] = getOptionsArrayParam($cliOptionsArray, 'file', 'clientgroupdata.txt');
	$arrayCleanupConfig['stage'] = getOptionsArrayParam($cliOptionsArray, 'stage', 3);
	$arrayCleanupConfig['db'] = getOptionsArrayParam($cliOptionsArray, 'db', 3);

	return $arrayCleanupConfig;
}


/**
 * getOptionsArrayParam
 *
 * @param type $paramArray
 * @param type $key
 * @param type $pDefaultValue
 * @return type
 */
function getOptionsArrayParam($paramArray, $key, $pDefaultValue = '')
{
	// return the array's parameter value or the default value if it isn't present
	if (array_key_exists($key, $paramArray))
	{
		return $paramArray[$key];
	}
	else
	{
		return $pDefaultValue;
	}
}


/**
 * updateWorkingFromProjects
 *  - copy any clientgroup information from the projects HEADER to the working HEADER
 *
 * @return array
 */
function updateWorkingFromProjects()
{
	echo "Updating from saved projects...\n";

	$resultArray = array('error' => 0, 'errorParam' =>'');
	$error = 0;
	$errorParam = '';

	$dbObj = DatabaseObj::getGlobalDBConnection();

	if ($dbObj)
	{
		$sql = 'UPDATE `' . UtilsObj::getWorkingDBName() . '`.`HEADER` w';
		$sql .= ' SET `clientgroup` = ';
		$sql .= '	(SELECT `clientgroup` FROM `' . UtilsObj::getProjectDBName() . '`.`HEADER` p';
		$sql .= '		WHERE (p.`projectref` = w.`projectref`))';
		$sql .= ' WHERE ((w.`foreignid` > 0) AND (w.`clientgroup` = ""))';

		if ($stmt = $dbObj->prepare($sql))
		{
			if (! $stmt->execute())
			{
				$errorParam = __FUNCTION__ . ' execute error: ' . $dbObj->error;
			}

			$stmt->free_result();
			$stmt->close();
			$stmt = null;
		}
		else
		{
			$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
		}
	}
	else
	{
		$errorParam = __FUNCTION__ . ' connection error: ' . $dbObj->error;
	}

	$resultArray['error'] = $error;
	$resultArray['errorParam'] = $errorParam;

	echo "...Complete\n\n";

	return $resultArray;
}


/**
 * updateHeaderFromSessions
 *  - get clientgroup information from the sessions and update the HEADER
 *
 * @param string $pDatabase
 * @return array
 */
function updateHeaderFromSessions($pDatabase)
{
	echo "Updating from session data...\n";
	$sessionDataArray = getActiveSessions();

	echo "\t" . $sessionDataArray['count'] . " sessions found.\n";

	$resultArray = array('error' => '', 'errorparam' =>'');
	$error = '';
	$errorParam = '';

	if ($sessionDataArray['count'] > 0)
	{
		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			foreach ($sessionDataArray['sessions'] as $sessionData => $projectRef)
			{
				$projectRefLists = array_chunk($projectRef, 150);
				$projectRefListCount = count($projectRefLists);

				for ($lc = 0; $lc < $projectRefListCount; $lc++)
				{
					echo "\tUpdating projects for " . $sessionData . ".\n";
					$projectList = '"' . implode('","', $projectRefLists[$lc]) . '"';

					$sql = 'UPDATE `' . $pDatabase . '`.`HEADER`';
					$sql .= ' SET `clientgroup` = ? ';
					$sql .= ' WHERE (projectref IN (' . $projectList . '))';

					if ($stmt = $dbObj->prepare($sql))
					{
						if ($stmt->bind_param('s', $sessionData))
						{
							echo "\tUpdating batch " . ($lc + 1) . " of " . $projectRefListCount . "...\n";
							if (! $stmt->execute())
							{
								$error = 'str_DatabaseError';
								$errorParam = __FUNCTION__ . ' execute error: ' . $dbObj->error;
							}

							$stmt->free_result();
						}
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' bind_param error: ' . $dbObj->error;
						}
						$stmt->close();
						$stmt = null;
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
					}
				}
			}
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' connection error: ' . $dbObj->error;
		}
	}

	$resultArray['error'] = $error;
	$resultArray['errorparam'] = $errorParam;

	echo "...Complete\n\n";

	return $resultArray;
}


/**
 * getActiveSessions
 * - read the sessions table and return a list of session
 */
function getActiveSessions()
{
	echo "\tReading online session data.\n";

	$resultArray = array('error' => '', 'errorparam' =>'', 'count' => 0, 'sessions' => array());
	$error = '';
	$errorParam = '';

	$dbObj = DatabaseObj::getGlobalDBConnection();

	$projectref = '';
	$serializedDataLength = 0;
	$sessionData = '';

	if ($dbObj)
	{
		$sql = 'SELECT `projectref`, `serializeddatalength`, `sessionarraydata` FROM `' . UtilsObj::getDBName() . '`.`SESSIONDATA` LIMIT 10000';

		if ($stmt = $dbObj->prepare($sql))
		{
			if ($stmt->execute())
			{
				if ($stmt->store_result())
				{
					if ($stmt->num_rows > 0)
					{
						if ($stmt->bind_result($projectref, $serializedDataLength, $sessionData))
						{
							while ($stmt->fetch())
							{
								// we have the session data now unserialize it back into an array
								if ($serializedDataLength > 0)
								{
									$sessionData = gzuncompress($sessionData, $serializedDataLength);
								}
								$temp = unserialize($sessionData);
								$resultArray['sessions'][$temp['licensekeydata']['groupcode']][] = $projectref;
								$resultArray['count']++;
							}
						}
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' bind_result error: ' . $dbObj->error;
						}
					}
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' store_result error: ' . $dbObj->error;
				}
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' execute error: ' . $dbObj->error;
			}

			$stmt->free_result();
			$stmt->close();
			$stmt = null;
		}
		else
		{
			// could not prepare statement
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
		}
		$dbObj->close();
	}
	else
	{
		// could not open database connection
		$error = 'str_DatabaseError';
		$errorParam = __FUNCTION__ . ' connection error: ' . $dbObj->error;
	}

	$resultArray['error'] = $error;
	$resultArray['errorparam'] = $errorParam;

	return $resultArray;
}


/**
 * updateHeaderFromFile
 *  - get clientgroup information from a file exported from Control Centre version and update the working HEADER
 * @param string $pDatabase
 * @param array $pUpdateData
 */
function updateHeaderFromFile($pDatabase, $pUpdateData)
{
	echo "Updating " . $pDatabase . " from Control Centre data...\n";

	echo "\t" . count($pUpdateData) . " codes found.\n";

	$resultArray = array('error' => '', 'errorparam' =>'');
	$error = '';
	$errorParam = '';

	$dbObj = DatabaseObj::getGlobalDBConnection();

	if ($dbObj)
	{
		foreach ($pUpdateData as $groupLine)
		{
			$groupData = explode(':', $groupLine);

			echo "\tUpdating projects for " . $groupData[0] . ".\n";

			$groupDataArray = explode(':', $groupData[1]);
			$groupDataLists = array_chunk($groupDataArray, 150);
			$groupDataListCount = count($groupDataLists);

			for ($gl = 0; $gl < $groupDataListCount; $gl++)
			{
				$updateList = implode(',', $groupDataLists[$gl]);

				$sql = 'UPDATE `' . $pDatabase . '`.`HEADER`';
				$sql .= ' SET `clientgroup` = ? ';
				$sql .= ' WHERE (`userid` IN (' . $updateList . ')) AND (`clientgroup` = "") ';

				if ($stmt = $dbObj->prepare($sql))
				{
					if ($stmt->bind_param('s', $groupData[0]))
					{
						echo "\tUpdating batch " . ($gl + 1) . " of " . $groupDataListCount . "...\n";
						if (! $stmt->execute())
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' execute error: ' . $dbObj->error;
						}

						$stmt->free_result();
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' bind_param error: ' . $dbObj->error;
					}
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
				}
			}
		}
		$stmt->close();
		$stmt = null;
	}
	else
	{
		$error = 'str_DatabaseError';
		$errorParam = __FUNCTION__ . ' connection error: ' . $dbObj->error;
	}

	$resultArray['error'] = $error;
	$resultArray['errorparam'] = $errorParam;

	echo "...Complete\n\n";

	return $resultArray;
}


/**
 * getUserList
 *  - get a list of groupcodes, include a list of user ID's for each groupcode
 *
 * @return array
 */
function getUserList()
{
	echo "Reading users data.\n\n";

	$groupCodeArray = array('error' => 0, 'errorParam' =>'', 'data' => array());
	$error = 0;
	$errorParam = '';

	$userID = 0;
	$groupCode = '';

	$dbObj = DatabaseObj::getGlobalDBConnection();

	if ($dbObj)
	{
		$sql = 'SELECT `id`, `groupcode` FROM `USERS` WHERE `groupcode` != ""';

		if ($stmt = $dbObj->prepare($sql))
		{
			if ($stmt->execute())
			{
				if ($stmt->store_result())
				{
					if ($stmt->num_rows > 0)
					{
						if ($stmt->bind_result($userID, $groupCode))
						{
							while ($stmt->fetch())
							{
//								if ($groupCode != '')
								{
//									if (! array_key_exists($groupCode, $groupCodeArray['data']))
//									{
//										$groupCodeArray['data'][$groupCode] = array();
//									}

									$groupCodeArray['data'][$groupCode][] = $userID;
								}
							}
						}
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' bind_result error: ' . $dbObj->error;
						}
					}
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' store_result error: ' . $dbObj->error;
				}
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' execute error: ' . $dbObj->error;
			}

			$stmt->free_result();
			$stmt->close();
			$stmt = null;
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
		}
	}
	else
	{
		$error = 'str_DatabaseError';
		$errorParam = __FUNCTION__ . ' connection error: ' . $dbObj->error;
	}

	$groupCodeArray['error'] = $error;
	$groupCodeArray['errorParam'] = $errorParam;

	return $groupCodeArray;
}


/**
 * getClientGroupFromHeader
 *  - read the clientgroup data from the HEADER table and list of userid's for each clientgroup code
 *
 * @param string $pDataBase
 * @return array
 */
function getClientGroupFromHeader($pDataBase)
{
	$clientGroupArray = array('error' => 0, 'errorParam' =>'', 'data' => array());
	$error = 0;
	$errorParam = '';

	$userID = 0;
	$clientGroup = '';


	$dbObj = DatabaseObj::getGlobalDBConnection();

	if ($dbObj)
	{
		$sql = 'SELECT DISTINCT `userid`, `clientgroup` FROM `' . $pDataBase . '`.`HEADER`';

		if ($stmt = $dbObj->prepare($sql))
		{
			if ($stmt->execute())
			{
				if ($stmt->store_result())
				{
					if ($stmt->num_rows > 0)
					{
						if ($stmt->bind_result($userID, $clientGroup))
						{
							while ($stmt->fetch())
							{
								if ($clientGroup != '')
								{
									if (! array_key_exists($clientGroup, $clientGroupArray['data']))
									{
										$clientGroupArray['data'][$clientGroup] = array();
									}

									$clientGroupArray['data'][$clientGroup][] = $userID;
								}
							}
						}
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' bind_result error: ' . $dbObj->error;
						}
					}
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' store_result error: ' . $dbObj->error;
				}

			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' execute error: ' . $dbObj->error;
			}

			$stmt->free_result();
			$stmt->close();
			$stmt = null;
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
		}
	}
	else
	{
		$error = 'str_DatabaseError';
		$errorParam = __FUNCTION__ . ' connection error: ' . $dbObj->error;
	}

	$clientGroupArray['error'] = $error;
	$clientGroupArray['errorParam'] = $errorParam;

	return $clientGroupArray;
}

?>