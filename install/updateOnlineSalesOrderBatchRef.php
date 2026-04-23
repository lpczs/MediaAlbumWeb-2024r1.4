<?php

// OS Types
define('TPX_OS_TYPE_WINDOWS', 0);
define('TPX_OS_TYPE_UNIX', 1);
define('TPX_OS_TYPE_MAC', 2);


define('__ROOT__', dirname(dirname(__FILE__)));

$isOnline = (file_exists(__ROOT__ . '/config/taopixonline.conf'));

$fixFileName = 'salesorderbatchref.txt';

clearScreen();

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

	// Stage 1.
	// SQL to fix batchref not being populated with filename (uploadref) for single line orders
	updateSingleLineSalesOrderRecords();

	// Stage 2.
	// Read data from a file exported from control centre and update salesorder table.
	if (file_exists($fixFileName))
	{
		// open the file
		$batchRefDataFileContent = file($fixFileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		if (count($batchRefDataFileContent) > 0)
		{

			$UpdateSalesOrderResult = updateSalesOrderBatchRefFromFile($batchRefDataFileContent);
						
		}
	}

	// set the initial value of the installflags field
	$flagArray[TPX_INSTALLFLAG_ONLINESALESORDERBATCHREF] = TPX_INSTALLFLAG_ONLINESALESORDERBATCHREF;

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

	// create a file to pass to the online execution listing projectrefs with corresponding uploadbatchrefs
	$batchRefData = getOrderItemUploadBatchRefsForOnlineProjects();
	
	// create a new file, or overwrite an existing file
	file_put_contents($fixFileName, '');
	
	// export group codes and users
	foreach ($batchRefData['data'] as $batchRef => $projectList)
	{
		$outputStr = $batchRef . ":" . "'" . implode("','", $projectList['projectrefs']) . "':" . $projectList['itemcount'] . "\n";

		file_put_contents($fixFileName, $outputStr, FILE_APPEND | LOCK_EX);

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
 * updateSingleLineSalesOrderRecords
 *  - copy the filename (uploadref) to the batchref column as for single line orders these should be the same.
 *
 * @return array
 */
function updateSingleLineSalesOrderRecords()
{
	echo "Updating exisiting sales order records ...\n";

	$resultArray = array('error' => 0, 'errorParam' =>'');
	$error = 0;
	$errorParam = '';

	$dbObj = DatabaseObj::getGlobalDBConnection();

	if ($dbObj)
	{
		$sql = 'UPDATE `' . UtilsObj::getProjectDBName() . '`.`SALESORDER` SET `batchref` = `filename`';

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
 * updateSalesOrderBatchRefFromFile
 *  - get batchref and batchcount from a file exported from Control Centre version and update the working SALESORDER table
 * @param string $pDatabase
 * @param array $pUpdateData
 */
function updateSalesOrderBatchRefFromFile($pUpdateData)
{
	echo "Updating SALESORDER table from Control Centre data...\n";

	$resultArray = array('error' => '', 'errorparam' =>'');
	$error = '';
	$errorParam = '';
		
	$dbObj = DatabaseObj::getGlobalDBConnection();

	if ($dbObj)
	{
		foreach ($pUpdateData as $uploadBatchRefLine)
		{
			$uploadBatchRefData = explode(':', $uploadBatchRefLine);

			echo "\tUpdating records for batchref " . $uploadBatchRefData[0] . ".\n";


			$sql = 'UPDATE `' . UtilsObj::getProjectDBName() . '`.`SALESORDER`';
			$sql .= ' SET `batchref` = ?, `batchcount` = ?';
			$sql .= ' WHERE `projectref` IN (' . $uploadBatchRefData[1] . ')';

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_param('si', $uploadBatchRefData[0], $uploadBatchRefData[2]))
				{
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
 * getOrderItemUploadBatchRefsForOnlineProjects
 *  - get a list of batchrefs for multi line online orders , include a list of projectrefs for each batchref
 *
 * @return array
 */
function getOrderItemUploadBatchRefsForOnlineProjects()
{
	echo "Reading order item data.\n\n";

	$batchrefArray = array('error' => 0, 'errorParam' =>'', 'data' => array());
	$error = 0;
	$errorParam = '';

	$itemCount = 0;
	$uploadBatchRef = '';
	$projectRef ='';

	$dbObj = DatabaseObj::getGlobalDBConnection();

	if ($dbObj)
	{
		$sql = 'SELECT oh.itemcount, oi.uploadbatchref, oi.projectref
 				FROM `ORDERHEADER` oh
				JOIN `ORDERITEMS` oi ON (oh.id = oi.orderid)
 				WHERE oh.itemcount > 1 AND oi.source = 1';

		if ($stmt = $dbObj->prepare($sql))
		{
			if ($stmt->execute())
			{
				if ($stmt->store_result())
				{
					if ($stmt->num_rows > 0)
					{
						if ($stmt->bind_result($itemCount, $uploadBatchRef, $projectRef))
						{
							while ($stmt->fetch())
							{
								if (! array_key_exists($uploadBatchRef, $batchrefArray['data']))
								{
									$batchrefArray['data'][$uploadBatchRef] = array('itemcount' => $itemCount, 'projectrefs' => array($projectRef));
								}
								else
								{
									$batchrefArray['data'][$uploadBatchRef]['projectrefs'][] = $projectRef;
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

	$batchrefArray['error'] = $error;
	$batchrefArray['errorParam'] = $errorParam;

	return $batchrefArray;
}
?>