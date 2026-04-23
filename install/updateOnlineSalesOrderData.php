<?php

// OS Types
define('TPX_OS_TYPE_WINDOWS', 0);
define('TPX_OS_TYPE_UNIX', 1);
define('TPX_OS_TYPE_MAC', 2);

// TAOPIX install flags - values to be assigned for bitwise calculations (1, 2, 4, 8, 16.....)
define('TPX_INSTALLFLAG_ONLINESALESORDERDATA', 1);

$templateArray = array();

$templateArray[0] = "UPDATE %s.`SALESORDER` SET `ordernumber` = ? WHERE (`projectref` = ?) AND (`ordernumber` = '')";
$templateArray[1] = " AND (`userid` = ?)";
$templateArray[2] = " AND (`ownercode` = ?)";

$fixSQLFileName = 'salesorderdata.txt';

clearScreen();

// read the command line parameters
$scriptOptions = readParameters();


if ($scriptOptions['file'] != '')
{
	define('__ROOT__', dirname(dirname(__FILE__)));

	require_once(__ROOT__ . '/libs/internal/Utils.php');
	require_once(__ROOT__ . '/libs/internal/DatabaseObj.php');

	// read the config file for TAOPIX Online
	$gConfig = UtilsObj::readConfigFile(__ROOT__ . '/config/taopixonline.conf');

	// check for ownercode field
	$ownercodeColumnCheck = columnExists(UtilsObj::getProjectDBName(), 'SALESORDER', 'ownercode');
	$scriptOptions['ownercode'] = $ownercodeColumnCheck['exists'];

	// check for userid field
	$useridColumnCheck = columnExists(UtilsObj::getProjectDBName(), 'SALESORDER', 'userid');
	$scriptOptions['userid'] = $useridColumnCheck['exists'];

	executeOrderNumberFixSQL($scriptOptions);
}
else
{
	require_once('../Utils/UtilsDatabase.php');
	require_once('../Utils/Utils.php');

	// read the config file for Control Centre
	$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

	// create a new file, or overwrite an existing file
	file_put_contents($fixSQLFileName, '');

	generateOrderNumberFixSQL();
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
 * readParameters
 */
function readParameters()
{
	global $fixSQLFileName;

	$parametersArray = array("f:" => "file:", "u" => "update");
	$arrayCleanupConfig = array();

	$cliOptionsArray = getopt(implode('', array_keys($parametersArray)), $parametersArray);

	$arrayCleanupConfig['file'] = getOptionsArrayParam($cliOptionsArray, 'file', '');
	$arrayCleanupConfig['update'] = array_key_exists('update', $cliOptionsArray);

	// set sales order update file name
	if ($arrayCleanupConfig['update'])
	{
		if ($arrayCleanupConfig['file'] == '')
		{
			// use the default file name
			$arrayCleanupConfig['file'] = $fixSQLFileName;
		}
	}

	return $arrayCleanupConfig;
}


/**
 * generateOrderNumberFixSQL
 */
function generateOrderNumberFixSQL()
{
	global $fixSQLFileName;

	$orderNumber = '';
	$ownercode = '';
	$userid = 0;
	$projectRef = '';

	$resultArray = array();
	$orderCount = 0;
	$error = '';

	$dbObj = DatabaseObj::getGlobalDBConnection();

	$sql = 'SELECT oh.ordernumber, oh.ownercode, oh.userid, oi.projectref
			FROM ORDERITEMS oi
			LEFT JOIN
				ORDERHEADER oh ON oh.id = oi.orderid
			WHERE
				source = 1
			ORDER BY
				oi.id';

	if ($dbObj)
	{
		if ($stmt = $dbObj->prepare($sql))
		{
			if ($stmt->execute())
			{
				if ($stmt->store_result())
				{
					if ($stmt->num_rows > 0)
					{
						if ($stmt->bind_result($orderNumber, $ownercode, $userid, $projectRef))
						{
							while ($stmt->fetch())
							{
								$resultArray[] = array('ordernumber' => $orderNumber, 'userid' => $userid, 'ownercode' => $ownercode, 'projectref' => $projectRef);
								$orderCount++;
							}
						}
						else
						{
							$error = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
						}
					}
				}
				else
				{
					$error = __FUNCTION__ . ' store_result: ' . $dbObj->error;
				}
			}
			else
			{
				$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
			}

			$stmt->free_result();
			$stmt->close();
			$stmt = null;
		}
		else
		{
			$error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
		}
	}
	else
	{
		$error = __FUNCTION__ . ' connection error: ' . $dbObj->error;
	}


	// output the fix file
	echo "\n\n";
	if ($error == '')
	{
		echo "Creating update file";
		// export ordernumber fix file
		foreach ($resultArray as $orderInfo)
		{
			$outputStr = serialize($orderInfo) . "\n";

			file_put_contents($fixSQLFileName, $outputStr, FILE_APPEND | LOCK_EX);

			echo ".";
		}

		// display message to outline the next steps for action online
		echo "\nUpdate file created.\n";
		echo "Please refer to the upgrade documentation to continue.\n";
	}
	else
	{
		echo " Error:- \n";
		echo $error . "\n";
	}

}

/**
 * executeOrderNumberFixSQL
 */
function executeOrderNumberFixSQL($cliOptsArray)
{
	global $templateArray;

	$errorArray = array();
	$updateCount = 0;
	$successCount = 0;

	// form the sql query string
	$templateStr = $templateArray[0];

	if ($cliOptsArray['userid'])
	{
		$templateStr .= $templateArray[1];
	}

	if ($cliOptsArray['ownercode'])
	{
		$templateStr .= $templateArray[2];
	}

	// add the installflags field to the SYSTEMCONFIG table
	addColumnIfNotExist(UtilsObj::getDBName(), 'SYSTEMCONFIG', 'installflags', 'INT(11)', 0);

	if (file_exists($cliOptsArray['file']))
	{
		// read the specified file and execute the sql statements
		$sql = file($cliOptsArray['file']);

		if (count($sql) > 0)
		{
			$dbObj = DatabaseObj::getGlobalDBConnection();

			if ($dbObj)
			{
				// execute each of the update queries
				$updateSQL = sprintf($templateStr, UtilsObj::getProjectDBName());

				if ($stmt = $dbObj->prepare($updateSQL))
				{
					foreach ($sql as $line)
					{
						$updateCount++;
						$bindValues = unserialize($line);

						$sqlBindParams = array('ss', $bindValues['ordernumber'], $bindValues['projectref']);
						if ($cliOptsArray['userid'])
						{
							$sqlBindParams[0] .= 'i';
							$sqlBindParams[] = $bindValues['userid'];
						}

						if ($cliOptsArray['ownercode'])
						{
							$sqlBindParams[0] .= 's';
							$sqlBindParams[] = $bindValues['ownercode'];
						}

						$bindOK = call_user_func_array(array($stmt, 'bind_param'), UtilsObj::makeValuesReferenced($sqlBindParams));

						if ($bindOK)
						{
							echo "Updating order " . $bindValues['ordernumber'] . "....";
							if (! $stmt->execute())
							{
								echo "Error.\n";
								$errorArray[$updateCount]['sql'] = $line;
								$errorArray[$updateCount]['error'] = __FUNCTION__ . ' execute: ' . $dbObj->error;
							}
							else
							{
								echo "OK.\n";
								$successCount++;
							}
						}
						else
						{
							$errorArray[$updateCount]['sql'] = $line;
							$errorArray[$updateCount]['error'] = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
						}
					}
					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$errorArray[$updateCount]['sql'] = $line;
					$errorArray[$updateCount]['error'] = __FUNCTION__ . ' prepare: ' . $dbObj->error;
				}
			}
			else
			{
				$errorArray[] = __FUNCTION__ . ' connection error: ' . $dbObj->error;
			}
		}
	}

	// set the initial value of the installflags field
	$flagArray[TPX_INSTALLFLAG_ONLINESALESORDERDATA] = 1;

	updateInstallFlags($flagArray);

	echo $successCount . " of " . $updateCount . " updates completed.\n";

	if (count($errorArray) > 0)
	{
		// some of the queries generated errors
		echo " Errors:- \n";
		foreach ($errorArray as $errorKey => $errorData)
		{
			echo $errorKey . ":\n";
			echo "  " . $errorData['sql'] . "\n";
			echo "  " . $errorData['error'] . "\n";
			echo "\n";
		}
	}


}

/**
* addColumnIfNotExist
*/
function addColumnIfNotExist($pSchemaName, $pTableName, $pColumnName, $pColumnType, $pDefaultValue)
{
	// check if the field exists in the specified table, if not, add the column with the default value
	$columnFoundData = columnExists($pSchemaName, $pTableName, $pColumnName);

	if (! $columnFoundData['exists'])
	{
		$fullTableName = '';

		if ($pSchemaName != '')
		{
			$fullTableName = '`' . $pSchemaName . '`.';
		}

		$fullTableName .= '`' . $pTableName . '`';

		$dbObj = DatabaseObj::getGlobalDBConnection();

		$sql = "ALTER TABLE " . $fullTableName . "
				ADD `" . $pColumnName . "` " . $pColumnType . " NOT NULL DEFAULT " . $pDefaultValue;

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare($sql))
			{
				if (! $stmt->execute())
				{
					$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
			}
		}
		else
		{
			$error = __FUNCTION__ . ' connection error: ' . $dbObj->error;
		}
	}
}

/**
* columnExists
*/
function columnExists($pSchemaName, $pTableName, $pColumnName)
{
	$resultArray = array('exists' => false, 'value' => 0);
	$fullTableName = '';
	$exists = 0;
	$columnExists = false;
	$tableExists = false;
	$sqlConditionArray = array();

	if ($pSchemaName != '')
	{
		$fullTableName = '`' . $pSchemaName . '`.';
		$sqlConditionArray[] = "(`TABLE_SCHEMA` = '" . $pSchemaName . "')";
	}

	$fullTableName .= '`' . $pTableName . '`';
	$sqlConditionArray[] = "(`TABLE_NAME` = '" . $pTableName . "')";


	$dbObj = DatabaseObj::getGlobalDBConnection();

	// does the table exist
	$sqlTable = "SELECT count(*) AS exist
				 FROM `information_schema`.`tables`
				 WHERE " . implode(' AND ', $sqlConditionArray);

	if ($dbObj)
	{
		if ($stmt = $dbObj->prepare($sqlTable))
		{
			if ($stmt->execute())
			{
				if ($stmt->store_result())
				{
					if ($stmt->num_rows > 0)
					{
						if ($stmt->bind_result($exists))
						{
							if ($stmt->fetch())
							{
								if ($exists != 0)
								{
									$tableExists = true;
								}
							}
						}
						else
						{
							$error = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
						}
					}
				}
				else
				{
					$error = __FUNCTION__ . ' store_result: ' . $dbObj->error;
				}
			}
			else
			{
				$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
			}

			$stmt->free_result();
			$stmt->close();
			$stmt = null;
		}
		else
		{
			$error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
		}


		if ($tableExists)
		{
			// does the table exist
			$sqlColumns = "SHOW columns FROM " . $fullTableName;

			if ($stmt = $dbObj->prepare($sqlColumns))
			{
				if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						if ($stmt->num_rows > 0)
						{
					        $data = array();
							$variables = array();
							$meta = $stmt->result_metadata();

							while ($field = $meta->fetch_field())
							{
								$variables[] = &$data[$field->name];
							}

							call_user_func_array(array($stmt, 'bind_result'), $variables);

							while (($stmt->fetch()) && (! $columnExists))
							{
								foreach ($data as $k => $v)
								{
									if (($k == 'Field') && ($v == $pColumnName))
									{
										$columnExists = true;
										break;
									}
								}
							}
						}
					}
					else
					{
						$error = __FUNCTION__ . ' store_result: ' . $dbObj->error;
					}
				}
				else
				{
					$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
			}
		}
	}
	else
	{
		$error = __FUNCTION__ . ' connection error: ' . $dbObj->error;
	}

	$resultArray['exists'] = $columnExists;

	return $resultArray;
}

/**
 * updateInstallFlags
 */
function updateInstallFlags($pFlagArray)
{
	// read the existing install flags from the database
	$currentFlags = getInstallFlags();

	// calculate the new value to insert
	// for each of the flags passed, overwrite that value in the install flags array
	foreach ($pFlagArray as $installKey => $installFlag)
	{
		$currentFlags[$installKey] = $installFlag;
	}

	// write values back to database
	setInstallFlags($currentFlags);
}

/**
 * getInstallFlags
 */
function getInstallFlags()
{
	// read the install flags from the SYSTEMCONFIG table
	$resultArray = array();
	$flagValue = 0;

	$dbObj = DatabaseObj::getGlobalDBConnection();

	$sql = 'SELECT installflags FROM `' . UtilsObj::getDBName() . '`.`SYSTEMCONFIG`';

	if ($dbObj)
	{
		if ($stmt = $dbObj->prepare($sql))
		{
			if ($stmt->execute())
			{
				if ($stmt->store_result())
				{
					if ($stmt->num_rows > 0)
					{
						if ($stmt->bind_result($flagValue))
						{
							if (! $stmt->fetch())
							{
								$error = __FUNCTION__ . ' fetch: ' . $dbObj->error;
							}
						}
						else
						{
							$error = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
						}
					}
				}
				else
				{
					$error = __FUNCTION__ . ' store_result: ' . $dbObj->error;
				}
			}
			else
			{
				$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
			}

			$stmt->free_result();
			$stmt->close();
			$stmt = null;
		}
		else
		{
			$error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
		}

	}
	else
	{
		$error = __FUNCTION__ . ' connection error: ' . $dbObj->error;
	}

	// set each of the flag values in the result array to 1 or 0
	$resultArray[TPX_INSTALLFLAG_ONLINESALESORDERDATA] = $flagValue & TPX_INSTALLFLAG_ONLINESALESORDERDATA;

	return $resultArray;
}

/**
 * getInstallFlags
 */
function setInstallFlags($pInstallFlags)
{
	// write the install flags to the SYSTEMCONFIG table
	$flagValue = 0;
	$error = '';

	foreach ($pInstallFlags as $flagKey => $flagSet)
	{
		if ($flagSet == 1)
		{
			$flagValue = $flagValue | $flagKey;
		}
	}

	$dbObj = DatabaseObj::getGlobalDBConnection();

	$sql = 'UPDATE `' . UtilsObj::getDBName() . '`.`SYSTEMCONFIG` SET installflags = ?';

	if ($dbObj)
	{
		if ($stmt = $dbObj->prepare($sql))
		{
			if ($stmt->bind_param('i', $flagValue))
			{
				if (! $stmt->execute())
				{
					$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
				}
			}
			else
			{
				$error = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
			}

			$stmt->free_result();
			$stmt->close();
			$stmt = null;
		}
		else
		{
			$error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
		}

	}
	else
	{
		$error = __FUNCTION__ . ' connection error: ' . $dbObj->error;
	}

	return $error;
}

?>
