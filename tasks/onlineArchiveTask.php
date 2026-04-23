<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('TPX_INTERNALTASK_ARCHIVERECORDLIMIT', 1000);

define('TPX_CLEANUP_OPT_PROJECTS', 4);
define('TPX_CLEANUP_OPT_ASSETS', 5);
define('TPX_CLEANUP_OPT_ARCHIVES', 12);
define('TPX_ARCHIVE_MODE_ARCHIVE', 2);

class onlineArchiveTask
{
	/**
	 * Execute the task
	 *
	 * @param int $pEventID ID of the individual event
	 * @return string
	 */
    static function run($pEventID)
	{
		$systemConfigArray = TaskObj::getSystemConfig();
		$resultMessage = '';

        try
        {
            $taskCode = self::register();
            $taskCode = $taskCode['code'];
			$comCounter = 0;

			// Add logs.
			TaskObj::writeLogEntry('Task: ' . $taskCode . '. Retrieving Assigned Data Policies.');

			// Reset excution time.
			UtilsObj::resetPHPScriptTimeout(30);

			// Read the brand information, check if any data retention policies have been applied.
			$brandPolicyDataResult = self::getActiveDataPolicies();

			if ($brandPolicyDataResult['result'] == '')
			{
				$brandDataPolicies = $brandPolicyDataResult['data'];
				$brandPolicyCount = count($brandDataPolicies);

				// Make sure at least on policy is active.
				if ($brandPolicyCount > 0)
				{
					// Set up the common part of the purge command,
					// add no prompt, and suppress on screen feedback, use ownercode for any AWS
					// limit to x number of records
					$directiveArray = array(
						'ownercode' => $systemConfigArray['ownercode'],
						'tenantid' => $systemConfigArray['tenantid'],
						'verbose' => 0,
						'noprompt' => ''
					);

					// Loop around all brands associated to a policy.
					for ($i = 0; $i < $brandPolicyCount; $i++)
					{
						$assignedPolicy = $brandDataPolicies[$i];

						// Make sure archive is active for that policy.
						if (($assignedPolicy['notorderedarchiveactive'] == 1) || ($assignedPolicy['orderedarchiveactive'] == 1))
						{
							// Get the brand information including the license keys for the brand
							$applicationName = $assignedPolicy['applicationname'];
							$brandKeyList = TaskObj::getBrandLicenseKeyCodes($assignedPolicy['code']);
							$keyList = array();

							foreach ($brandKeyList as $keyInfo)
							{
								$keyList[] = $keyInfo['id'];
							}

                            if (empty($keyList)) {
                                continue;
                            }

							// Add to the directives array
							$directiveArray['licensekeys'] = implode(',', $keyList);

							// Archive not ordered projects.
							if ($assignedPolicy['notorderedarchiveactive'] == 1)
							{
								self::createComInstruction($comCounter, $applicationName, $directiveArray, 'old', $assignedPolicy['notorderedarchivedays']);
							}

							// Archive ordered projects.
							if ($assignedPolicy['orderedarchiveactive'] == 1)
							{
								self::createComInstruction($comCounter, $applicationName, $directiveArray, 'ordered', $assignedPolicy['orderedarchivedays']);
							}
						}
					}
				}
			}
        }
        catch (Exception $e)
        {
            $resultMessage = 'en ' . $e->getMessage();
        }

        return $resultMessage;
    }

	/**
	 * Define default settings for this task
	 *
	 * @return array
	 */
    static function register()
    {
        $defaultSettings = array();

        /*
         * $defaultSettings('type') defines type of tasks
         * 0 - scheduled
         * 1 - service
         * 2 - manual
         */
        $defaultSettings['type'] = '0';
        $defaultSettings['code'] = 'TAOPIX_ONLINEARCHIVETASK';
        $defaultSettings['name'] = 'it spurgo italiano compito desciption<p>fr purge français description de la tâche<p>es descripción de la tarea de purga español';

        /*
         * $defaultSettings('intervalType') defines inteval value
         * 1 - Number of minutes
         * 2 - Exact time of the day
         * 3 - Number of days
         */

        $defaultSettings['intervalType'] = '2';
        $defaultSettings['intervalValue'] = '01:00';
        $defaultSettings['maxRunCount'] = '10';
        $defaultSettings['deleteCompletedDays'] = '5';

        return $defaultSettings;
    }


	/**
	 * Get a list of data policies used by the brands
	 *
	 * @return array
	 */
	static function getActiveDataPolicies()
	{
		$dbObj = TaskObj::getGlobalDBConnection();

		$resultArray = array('result' => '', 'resultparam' => '', 'data' => array());
		$id = 0;
		$code = '';
		$applicationname = '';
		$notorderedarchiveactive = 0;
		$notorderedarchivedays = 0;
		$orderedarchiveactive = 0;
		$orderedarchivedays = 0;

		if ($dbObj)
		{
			$sql = 'SELECT `b`.`id`, `b`.`code`, `b`.`applicationname`, `dp`.`notorderedarchiveactive`, `dp`.`notorderedarchivedays`,
						`dp`.`orderedarchiveactive`, `dp`.`orderedarchivedays`
					FROM `BRANDING` `b`
						INNER JOIN `DATAPOLICIES` `dp` ON `b`.`onlinedataretentionpolicy` = `dp`.`id`
 					WHERE (`b`.`onlinedataretentionpolicy` > 0)
						AND (`b`.`active` = 1)
						AND (`dp`.`active` = 1)';

			$stmt = $dbObj->prepare($sql);

			if ($stmt)
			{
				if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						if ($stmt->num_rows > 0)
						{
							if ($stmt->bind_result($id, $code, $applicationname, $notorderedarchiveactive, $notorderedarchivedays,
								$orderedarchiveactive, $orderedarchivedays))
							{
								while ($stmt->fetch())
								{
									$tempArray = array();
									$tempArray['id'] = $id;
									$tempArray['code'] = $code;
									$tempArray['applicationname'] = $applicationname;
									$tempArray['notorderedarchiveactive'] = $notorderedarchiveactive;
									$tempArray['notorderedarchivedays'] = $notorderedarchivedays;
									$tempArray['orderedarchiveactive'] = $orderedarchiveactive;
									$tempArray['orderedarchivedays'] = $orderedarchivedays;
									$resultArray['data'][] = $tempArray;
								}
							}
							else
							{
								$resultArray['result'] = 'str_DatabaseError';
								$resultArray['resultparam'] = __FUNCTION__ . ' - bind_result: error (' . $dbObj->error . ')';
							}
						}
					}
					else
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' - store_result: error (' . $dbObj->error . ')';
					}
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' - execute: error (' . $dbObj->error . ')';
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$resultArray['result'] = 'str_DatabaseError';
				$resultArray['resultparam'] = __FUNCTION__ . ' - prepare: error (' . $dbObj->error . ')';
			}
		}

        return $resultArray;
	}

	/**
	 * Generate the command and send it to the queue.
	 *
	 * @param int $pComCounter Current execution count.
	 * @param string $pApplicationName Application name.
	 * @param array $pDirectiveArray All command instruction.
	 * @param string $pLevel directive level.
	 * @param int $pArchiveDays Number of archiving days.
	 */
	static function createComInstruction(&$pComCounter, $pApplicationName, $pDirectiveArray, $pLevel, $pArchiveDays)
	{
		global $ac_config;

		$comData = array();
		$pComCounter++;

		if ($pLevel == 'old')
		{
			$comData['event'] = $pComCounter . ' - Archive Not Ordered Projects (' . $pApplicationName . ')';
		}
		else
		{
			$comData['event'] = $pComCounter . ' - Archive Ordered Projects (' . $pApplicationName . ')';
		}

		$comData['key'] = $pComCounter . '-' . date('Ymdhism');
		$comData['type'] = TPX_CLEANUP_OPT_ARCHIVES;
		$comData['directives'] = $pDirectiveArray;
		$comData['directives']['level'] = $pLevel;
		$comData['directives']['age'] = $pArchiveDays;
		$comData['directives']['mode'] = 'archive';
		$comData['directives']['limit'] = TPX_INTERNALTASK_ARCHIVERECORDLIMIT;

		// Log the task.
		TaskObj::writeLogEntry('Task: TAOPIX_ONLINEARCHIVETASK. Queuing Task ' . $comData['event'] . '.');

		// Send the command to the online server, and add to queue.
		$dataToEncrypt = array('directive' => $comData, 'type' => 'archive');
		CurlObj::sendByPost($ac_config['TAOPIXONLINEURL'], 'DataRetentionAPI.queueDataRetentionJob', $dataToEncrypt);
	}
}

?>
