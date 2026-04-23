<?php

// OS Types
define('TPX_OS_TYPE_WINDOWS', 0);
define('TPX_OS_TYPE_UNIX', 1);
define('TPX_OS_TYPE_MAC', 2);

define('__ROOT__', dirname(dirname(__FILE__)));

$isOnline = (file_exists(__ROOT__ . '/config/taopixonline.conf'));

$projectRefFileName = 'onlinebasketprojectrefs.txt';
$onlineBasketProjectDataFileName = 'onlinebasketprojectdata.txt';

clearScreen();

// remove the script timeout
set_time_limit(0);

if ($isOnline)
{
	echo "Online.\n";

	require_once(__ROOT__ . '/libs/internal/Utils.php');
	require_once(__ROOT__ . '/libs/internal/DatabaseObj.php');
	
	// include install flag object
	require_once(dirname(__FILE__) . '/UtilsInstallFlags.php');

	// read the config file for TAOPIX Online
	$gConfig = UtilsObj::readConfigFile(__ROOT__ . '/config/taopixonline.conf');

	// Read data from a file exported from control centre and retrieve proect data
	if (file_exists($projectRefFileName))
	{
		// open the file
		$projectRefDataFileContent = file($projectRefFileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		if (count($projectRefDataFileContent) > 0)
		{
			$projectDataResult = getOnlineProjectData($projectRefDataFileContent[0]);
			
			file_put_contents($onlineBasketProjectDataFileName, $projectDataResult['data'], FILE_APPEND | LOCK_EX);

			echo ".";

			// display message to outline the next steps for action online
			echo "\nUpdate file created.\n";
			echo "Please refer to the upgrade documentation to continue.\n";
			
			// set the initial value of the installflags field
			$flagArray[TPX_INSTALLFLAG_ONLINEBASKETDATA] = TPX_INSTALLFLAG_ONLINEBASKETDATA;
	
			$dbObj = DatabaseObj::getGlobalDBConnection();
	
			InstallFlagsObj::updateInstallFlags($dbObj, UtilsObj::getDBName(), $flagArray, false);				
		}
	}
}
else
{
	echo "Control Centre.\n";
	
	require_once('../Utils/UtilsDatabase.php');
	require_once('../Utils/Utils.php');

	// read the config file for Control Centre
	$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
	
	if (file_exists($onlineBasketProjectDataFileName))
	{
		// open the file
		$projectRefDataFileContent = file($onlineBasketProjectDataFileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		$error = '';
		$errorParam = '';
		
		if (count($projectRefDataFileContent) > 0)
		{
			$projectDataArray = unserialize($projectRefDataFileContent[0]);
			$projectDataResult = updateOnlineBasketData($projectDataArray);
			
			$error	= $projectDataResult['error'];
			$errorParam = $projectDataResult['errorparam'];
		}
		
		if ($error == '')
		{	
			// display message to outline the next steps for action online
			echo "\nUpdate ONLINEBASKET complete.\n";
			echo "Please refer to the upgrade documentation to continue.\n";
		}
		else
		{
			echo $errorParam;
		}
	}
	else
	{
		// create a file to pass to the online execution listing projectrefs with corresponding uploadbatchrefs
		$projectRefArray = getProjectRefsForProjectsInBasket();
		$outputStr = '';
		
		if (count($projectRefArray['data']) > 0)
		{
			// export projectrefs
			$outputStr = "'" . implode("','", $projectRefArray['data']) . "'";
		}
		
		file_put_contents($projectRefFileName, $outputStr, FILE_APPEND | LOCK_EX);

		echo ".";

		// display message to outline the next steps for action online
		echo "\nUpdate file created.\n";
		echo "Please refer to the upgrade documentation to continue.\n";
	}
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
 * getOnlineProjectData
 *  - get project data from Online so that we can fix data in the ONLINEBASKET table in Control Centre
 * @param string $pProjectRefList comma seperated list of projectrefs from the ONLINEBASKET table
 */
function getOnlineProjectData($pProjectRefList)
{
	echo "Retrieving project data for update...\n";

	$resultArray = array('error' => '', 'errorparam' =>'', 'data' => array());
	$error = '';
	$errorParam = '';
	$projectListArray = array();
	
	$dbObj = DatabaseObj::getGlobalDBConnection();

	if ($dbObj)
	{
		$sql = "SELECT `prj`.`projectref`, IF(ISNULL(`pr`.`code`), `cpr`.`code`, `pr`.`code`), 
				IF(ISNULL(`pr`.`name`), `cpr`.`name`, `pr`.`name`), `prj`.`name`, `prj`.`lastsavedcount`
				 FROM `" . UtilsObj::getWorkingDBName() . "`.`PROJECT` `prj` 
				 LEFT JOIN `" . UtilsObj::getWorkingDBName() . "`.`PRODUCT` `pr`
				 ON `prj`.`projectref` = `pr`.`projectref`
				 LEFT JOIN `" . UtilsObj::getCacheDataDBName() . "`.`PRODUCT` `cpr`
				 ON `prj`.`collectionuniqueref` = `cpr`.`collectionuniqueref`
				WHERE `prj`.`projectref` IN (" . $pProjectRefList . ")";

		if ($stmt = $dbObj->prepare($sql))
		{
			if ($stmt->bind_result($projectRef, $layoutCode, $layoutName, $projectName, $lastSavedCount))
			{
				if ($stmt->execute())
				{
					while($stmt->fetch())
					{
						$project = array();
						$project['pr'] = $projectRef;
						$project['lc'] = $layoutCode;
						$project['ln'] = $layoutName;
						$project['pn'] = $projectName;
						$project['ps'] = ($lastSavedCount > 0 ) ? 1 : 0;
						$project['sd'] = '';
						
						$projectListArray[$projectRef] = $project;
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
			$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
		}		
		
		$stmt->free_result();
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
	$resultArray['data'] = serialize($projectListArray);

	echo "...Complete\n\n";

	return $resultArray;
}

/**
 * updateOnlineBasketData
 *  - update project data in the ONLINEBASKET table using data that we have retieved from Online;
 * @param array $pProjectDataArray containg project data that is used to update project data in the ONLINEBASKET table
 */
function updateOnlineBasketData($pProjectDataArray)
{
	global $projectRefFileName;
	
	echo "Updating onlinebasket data.\n";

	$projectRefArray = array('error' => 0, 'errorparam' =>'');
	$error = 0;
	$errorParam = '';
	$projectRefList = '';

	$projectRefDataFileContent = file($projectRefFileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	
	if (count($projectRefDataFileContent) > 0)
	{
		$projectRefList = $projectRefDataFileContent[0];
	}
	
	$dbObj = DatabaseObj::getGlobalDBConnection();

	if ($dbObj)
	{
		$sql = "SELECT `projectref`, `projectdata` FROM ONLINEBASKET WHERE `projectref` IN (" . $projectRefList . ") AND `inbasket` = 1";

		if ($stmt = $dbObj->prepare($sql))
		{
			if ($stmt->execute())
			{
				if ($stmt->store_result())
				{
					if ($stmt->num_rows > 0)
					{
						if ($stmt->bind_result($projectRef, $projectData))
						{
							while ($stmt->fetch())
							{
								$unserialisedProjectData = unserialize($projectData);
								
								if ($unserialisedProjectData != false)
								{
									// update the projectname in the unserialised data.
									$unserialisedProjectData['items'][0]['projectname'] = $pProjectDataArray[$projectRef]['pn'];

									// serialize the project data so it can be updated in the basket table.
									$pProjectDataArray[$projectRef]['sd'] = serialize($unserialisedProjectData);
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
		
		$sql = "UPDATE `ONLINEBASKET` SET `projectname` = ?, `layoutcode` = ?, `layoutname` = ?, `saved` = ?, `projectdata` = ? WHERE `projectref` = ?";
	
		if ($stmt = $dbObj->prepare($sql))
		{
			foreach ($pProjectDataArray as $projectRef => $project)
			{
				if ($stmt->bind_param('sssiss', $project['pn'], $project['lc'], $project['ln'], $project['ps'], $project['sd'], $projectRef))
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
		
	$projectRefArray['error'] = $error;
	$projectRefArray['errorparam'] = $errorParam;

	return $projectRefArray;
}


/**
 * getProjectRefsForProjectsInBasket
 *  - get a list of projectrefs for projects that are in the ONLINEBASKET table
 *
 * @return array
 */
function getProjectRefsForProjectsInBasket()
{
	echo "Reading onlinebasket data.\n";

	$projectRefArray = array('error' => 0, 'errorParam' =>'', 'data' => array());
	$error = 0;
	$errorParam = '';

	$projectRef ='';

	$dbObj = DatabaseObj::getGlobalDBConnection();

	if ($dbObj)
	{
		$sql = 'SELECT `projectref` FROM ONLINEBASKET WHERE `projectref` != ""';

		if ($stmt = $dbObj->prepare($sql))
		{
			if ($stmt->execute())
			{
				if ($stmt->store_result())
				{
					if ($stmt->num_rows > 0)
					{
						if ($stmt->bind_result($projectRef))
						{
							while ($stmt->fetch())
							{
								$projectRefArray['data'][] = $projectRef;
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

	$projectRefArray['error'] = $error;
	$projectRefArray['errorParam'] = $errorParam;

	return $projectRefArray;
}
?>