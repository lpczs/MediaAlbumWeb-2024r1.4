<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../libs/external/vendor/autoload.php');

// get the tasks path from the caller
$filePath = dirname($argv[0]);
chdir($filePath);

require_once('../Utils/UtilsCoreIncludes.php');

UtilsObj::writeLogEntry('Task Manager Started');

$maxTaskCount = 5;
$tasksList = array();
$launchedTaskCount = 0;
$runningArray = array();
$taskArray = array();
$cronActive = 0;

$taskCode = '';
$intervalType = 0;
$intervalValue = 0;
$runCount = 0;

$taskResult = '';


// get the eventID from the caller
$eventID = isset($argv[1]) ? (int)$argv[1] : 0;


$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
$pathToPHP = isset($ac_config['PATHTOPHP']) ? $ac_config['PATHTOPHP'] : '';
$taskRunnerPath = $filePath . '/taskRunner.php';


// get the offset between the server time and PHP time
// we do this so that we can just use the PHP time() function and apply the offset
$serverTimeOffset = strtotime(DatabaseObj::getServerTime()) - time();

$constantsArray = DatabaseObj::getConstants();


// determine if the task manager is enabled
$dbObj = DatabaseObj::getGlobalDBConnection();
if ($dbObj)
{
	if ($stmt = $dbObj->prepare('SELECT `cronactive` FROM `SYSTEMCONFIG`'))
	{
		if ($stmt->execute())
		{
			if ($stmt->store_result())
			{
				if ($stmt->num_rows > 0)
				{
					if($stmt->bind_result($cronActive))
					{
						if (! $stmt->fetch())
						{
							UtilsObj::writeLogEntry("str_DatabaseError - 'cronactive fetch '" . $dbObj->error);
						}
					}
					else
					{
						UtilsObj::writeLogEntry("str_DatabaseError - 'cronactive bind result '" . $dbObj->error);
					}
				}
				else
				{
					UtilsObj::writeLogEntry("str_DatabaseError - 'cronactive num rows '" . $dbObj->error);
				}
			}
			else
			{
				UtilsObj::writeLogEntry("str_DatabaseError - 'cronactive store result '" . $dbObj->error);
			}
		}
		else
		{
			UtilsObj::writeLogEntry("str_DatabaseError - 'cronactive bind execute '" . $dbObj->error);
		}
	}
	else
	{
		UtilsObj::writeLogEntry("str_DatabaseError - 'cronactive prepare '" . $dbObj->error);
	}

	$stmt->free_result();
	$stmt->close();
	$stmt = null;


    if ($cronActive == 1)
    {
    	/*
		determine how we are going to read data from the task runner pipes
		win32 does not support non-blocking pipes so we must use fstat and read the number of bytes available
		linux supports non-blocking pipes but does not support fstat so we just read from the stream
		osx seems to support both but for how we treat it the same as linux
		*/
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
		{
			$fstatRequired = true;
		}
		else
		{
			$fstatRequired = false;
		}

		// build the pipe descriptor spec so that we can read data from the processes we launch
    	$pipeDescriptorSpec = array(
			0 => array('pipe', 'r'),
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w'));


    	// if we have an eventID then we have been asked to run a specific event only
    	if ($eventID > 0)
    	{
    		if (! file_exists($pathToPHP))
			{
    			$taskResult = '{str_ErrorWrongPhpPath}';
			}
			else
			{
				if (! file_exists($taskRunnerPath))
				{
    				$taskResult = '{str_ErrorWrongTaskRunnerPath}';
				}
				else
				{
					$eventArray = DatabaseObj::getEventById($eventID, false);

					if ($eventArray['result'] == '')
					{
						$eventArray = $eventArray['events'][0];
						$taskArray = DatabaseObj::getTask($eventArray['taskCode']);
						$runTask = true;

						// if we do not have web scripting enabled and the task is not internal then we should not run the task.
						if ((! $constantsArray['optionwscrp']) && ($taskArray['internal'] == 0))
						{
							$runTask = false;
						}

						if ($runTask)
						{
							if ($eventArray['taskCode'] != '')
							{
								$procPipes = array();

								$handle = proc_open($pathToPHP . ' "' . $taskRunnerPath . '" "' . $eventArray['taskCode'] . '" ' . $eventID, $pipeDescriptorSpec, $procPipes);

								if (is_resource($handle))
								{
									stream_set_blocking($procPipes[1], 0);
									stream_set_blocking($procPipes[2], 0);

									$launchedTaskCount++;

									UtilsObj::writeLogEntry('Launched Event Task: ' . $eventArray['taskCode']);

									$runningArray[] = array('code' => $eventArray['taskCode'],
															'status' => TPX_TASKMANAGER_STATUS_NEVERRUN,
															'handle' => $handle,
															'pipes' => $procPipes,
															'data' => '',
															'intervaltype' => $taskArray['intervalType'],
															'intervalvalue' => $taskArray['intervalValue']);
								}
								else
								{
									UtilsObj::writeLogEntry('Fatal Error Launching Event Task: ' . $eventArray['taskCode']);

									$taskResult = '{str_ErrorTaskRunnerFailedAtStartup}';
								}
							}
							else
							{
								$taskResult = 'en Event with such ID does not exist';
							}
						}
					}
					else
					{
						$taskResult = 'en ' . $eventArray['resultparam'];
					}
				}
			}

			UtilsObj::writeLogEntry($taskResult);
    	}
    	else
    	{
    		// find the tasks that we need to run
    		// if we do not have web scripting enabled then we should only retrieve internal tasks only
    		if (! $constantsArray['optionwscrp'])
    		{
    			$retrieveInternalTasksOnlySQL = ' AND (`internal` = 1)';
    		}
    		else
    		{
    			$retrieveInternalTasksOnlySQL = '';
    		}

    		if ($stmt = $dbObj->prepare('SELECT `taskcode`, `intervaltype`, `intervalvalue` FROM `TASKS`
    									WHERE ((`nextruntime` <= NOW()) OR (`nextruntime` = 0)) AND (`active` = 1)'
    									. $retrieveInternalTasksOnlySQL . ' ORDER BY `lastruntime` LIMIT 0, ' . $maxTaskCount))
			{
				$stmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);
				if (($stmt) && ($stmt->bind_result($taskCode, $intervalType, $intervalValue)))
				{
					if ($stmt->execute())
					{
						while ($stmt->fetch())
						{
                            UtilsObj::resetPHPScriptTimeout(30);

							// remove all not supported characters
							$taskCode = preg_replace('/[^ \w]+/', '', $taskCode);

							// make sure this task is not still running before we attempt to launch it
							if (DatabaseObj::dbMutexFree('taopixtask_' . $taskCode) == true)
							{
								$taskStatus = TPX_TASKMANAGER_STATUS_COMPLETED;

								// check that path to script and php is correct
								if (! file_exists($pathToPHP))
								{
									$taskStatus = TPX_TASKMANAGER_STATUS_FAILED;
									$taskResult = '{str_ErrorWrongPhpPath}';
								}

								if (! file_exists($taskRunnerPath))
								{
									$taskStatus = TPX_TASKMANAGER_STATUS_FAILED;
									$taskResult = '{str_ErrorWrongTaskRunnerPath}';
								}

								if ($taskStatus != TPX_TASKMANAGER_STATUS_FAILED)
								{
									$procPipes = array();

									$handle = proc_open($pathToPHP . ' "' . $taskRunnerPath . '" "'. $taskCode . '" ', $pipeDescriptorSpec, $procPipes);

									if (is_resource($handle))
									{
										stream_set_blocking($procPipes[1], 0);
										stream_set_blocking($procPipes[2], 0);

										$launchedTaskCount++;

										UtilsObj::writeLogEntry('Launched Task: ' . $taskCode);
									}
									else
									{
										UtilsObj::writeLogEntry('Fatal Error Launching Task: ' . $taskCode);

										$taskStatus = TPX_TASKMANAGER_STATUS_FAILED;
										$taskResult = '{str_ErrorTaskRunnerFailedAtStartup}';
									}
								}

								if ($taskStatus == TPX_TASKMANAGER_STATUS_FAILED)
								{
									$params = array('runstatus' => array('value' => TPX_TASKMANAGER_STATUS_IDLE, 'type' => 'i'),
													'lastruntime' => array('value' => date('Y-m-d H:i:s', getServerTime()), 'type' => 's'),
													'nextruntime' => array('value' => getNextRunTime($intervalType, $intervalValue), 'type' => 's'),
													'statuscode' => array('value' => TPX_TASKMANAGER_STATUS_FAILED, 'type' => 'i'),
													'statusmessage' => array('value' => $taskResult, 'type' => 's'));

									DatabaseObj::updateTaskStatus($taskCode, $params, $dbObj);
								}
								else
								{
									$runningArray[] = array('code' => $taskCode,
															'status' => TPX_TASKMANAGER_STATUS_NEVERRUN,
															'handle' => $handle,
															'pipes' => $procPipes,
															'data' => '',
															'intervaltype' => $intervalType,
															'intervalvalue' => $intervalValue);
								}
							}
						}

						UtilsObj::writeLogEntry('Tasks Launched: ' . $launchedTaskCount);
					}
					else
					{
						UtilsObj::writeLogEntry("str_DatabaseError - 'taskManager execute '" . $dbObj->error);
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					UtilsObj::writeLogEntry("str_DatabaseError - 'taskManager bind '" . $dbObj->error);
				}
			}
			else
			{
				UtilsObj::writeLogEntry("str_DatabaseError - 'taskManager prepare '" . $dbObj->error);
			}
		}


		// check the status of each process we have launched
		if ($launchedTaskCount > 0)
		{
			$tasksRunning = true;
			while ($tasksRunning == true)
			{
				$tasksRunning = false;

				for ($i = 0; $i < count($runningArray); $i++)
				{
					UtilsObj::resetPHPScriptTimeout(30);

					$taskArray = &$runningArray[$i];

					// if the task is running attempt to read data from it
					if ($taskArray['status'] == TPX_TASKMANAGER_STATUS_NEVERRUN)
					{
						if ($fstatRequired)
						{
							$pipeStatus = fstat($taskArray['pipes'][1]);
							if ($pipeStatus['size'] > 0)
							{
								$data = fread($taskArray['pipes'][1], $pipeStatus['size']);
							}
							else
							{
								$data = '';
							}
						}
						else
						{
							$data = stream_get_contents($taskArray['pipes'][1]);
						}

						if ($data != '')
						{
							$taskArray['data'] .= $data;

							// echo the data received from the task process for debugging purposes
							echo $data;

							if (substr($data, -1) != "\n")
							{
								echo "\n";
							}
						}


						// is the task still running?
						$processStatus = proc_get_status($taskArray['handle']);
						if ($processStatus['running'])
						{
							$tasksRunning = true;
						}
						else
						{
							// the task process has finished
							if (is_resource($taskArray['handle']))
							{
								// first grab any data from the exceptions pipe
								if ($fstatRequired)
								{
									$pipeStatus = fstat($taskArray['pipes'][2]);
									if ($pipeStatus['size'] > 0)
									{
										$taskOutput = fread($taskArray['pipes'][2], $pipeStatus['size']);
									}
									else
									{
										$taskOutput = '';
									}
								}
								else
								{
									$taskOutput = stream_get_contents($taskArray['pipes'][2]);
								}

								if ($taskOutput != '')
								{
									// write the exception error
									UtilsObj::writeLogEntry($taskOutput);
								}
								else
								{
									$taskOutput = $taskArray['data'];
								}

								// close the pipes and the process handle
								fclose($taskArray['pipes'][0]);
								fclose($taskArray['pipes'][1]);
								fclose($taskArray['pipes'][2]);
								proc_close($taskArray['handle']);

								UtilsObj::writeLogEntry('Completed Task: ' . $taskArray['code']);

								// update the running task status so that we do not check it again
								$taskArray['status'] = TPX_TASKMANAGER_STATUS_COMPLETED;

								// process the result of the task
								$startChar = substr($taskOutput, 0, 1);
								$endCharPos = strrpos($taskOutput, 'TAOPIX_TASK_COMPLETION');
								$taskResult = substr($taskOutput, $endCharPos + strlen('TAOPIX_TASK_COMPLETION'));

								// assume the task completed successfully
								$taskStatus = TPX_TASKMANAGER_STATUS_COMPLETED;

								if ($startChar != '*')
								{
									// there was no start character so the task has failed
									$taskStatus = TPX_TASKMANAGER_STATUS_FAILED;
									$taskResult = '{str_ErrorNoStartCharacter}';
								}
								else
								{
									if ($endCharPos === false)
									{
										// the characters expected at the end are not correct so the task has failed
										$taskStatus = TPX_TASKMANAGER_STATUS_FAILED;
										$taskResult = '{str_ErrorNoEndCharacter}';
									}
								}

								// update the task status
								// if the result is ALREADYRUNNING then we didn't launch the task as we detected that one of its type was already running
								// in this situation we do not update the status as the one which is running will update it
								if ($taskResult != 'ALREADYRUNNING')
								{
									// if the task result is not empty the task failed
									if ($taskResult != '')
									{
										$taskStatus = TPX_TASKMANAGER_STATUS_FAILED;
									}

									// if we aren't running the task for a specific event update the task status
									if ($eventID <= 0)
									{
										$params = array('runstatus' => array('value' => TPX_TASKMANAGER_STATUS_IDLE, 'type' => 'i'),
											'lastruntime' => array('value' => date('Y-m-d H:i:s', getServerTime()), 'type' => 's'),
											'nextruntime' => array('value' => getNextRunTime($taskArray['intervaltype'], $taskArray['intervalvalue']), 'type' => 's'),
											'statuscode' => array('value' => $taskStatus, 'type' => 'i'),
											'statusmessage' => array('value' => $taskResult, 'type' => 's'));

										DatabaseObj::updateTaskStatus($taskArray['code'], $params, $dbObj);
									}
								}
							}
						}
					}
				}

				usleep(100000);
			}
		}

		// if we aren't running the task manager for a specific event update the time the task manager was last executed
		if ($eventID <= 0)
    	{
			updateCronLastRunTime();
		}

		UtilsObj::writeLogEntry('Task Manager Finished');
	}
	else
	{
		UtilsObj::writeLogEntry('CRON Not Active');
	}

	$dbObj->close();
}
else
{
	UtilsObj::writeLogEntry("str_DatabaseError - 'taskManager connect '" . $dbObj->error);
}



// functions
function updateCronLastRunTime()
{
	$resultArray = array();
	$result = '';
	$resultParam = '';

	$dbObj = DatabaseObj::getGlobalDBConnection();
    if ($dbObj)
    {
       	if ($stmt = $dbObj->prepare('UPDATE `SYSTEMCONFIG` SET `cronlastruntime` = NOW()'))
		{
			$stmt->execute();

			$stmt->free_result();
			$stmt->close();
		}
		else
		{
			// could not prepare statement
			$result = 'str_DatabaseError';
			$resultParam = 'updateCronLastRunTime prepare ' . $dbObj->error;
		}

		$dbObj->close();
    }

    $resultArray['result'] = $result;
    $resultArray['resultParam'] = $resultParam;

	return $resultArray;
}

function getServerTime()
{
	global $serverTimeOffset;

	return (time() + $serverTimeOffset);
}

function getNextRunTime($pIntervalType, $pIntervalValue)
{
    $timeStamp = getServerTime();

    switch ($pIntervalType)
    {
        case 1: // minutes
            $timeStamp = strtotime('+' . $pIntervalValue . ' minutes', $timeStamp);
            $nextRunDate = date('Y-m-d H:i:s', $timeStamp);

            break;
        case 2: // exact time of the day - has to be a valid format

            // create the next run date using the current date and supplied time
            $currentDate = date('Y-m-d', $timeStamp);
            $nextRunDate = $currentDate . ' ' . $pIntervalValue;

            // if the next run date is before the current time add one day
            $nextRunDateTimeStamp = strtotime($nextRunDate);

            if ($nextRunDateTimeStamp < $timeStamp)
            {
                $nextRunDateTimeStamp = strtotime('+1 days', $nextRunDateTimeStamp);
                $nextRunDate = date('Y-m-d H:i:s', $nextRunDateTimeStamp);
            }

            break;
        case 3: // days
            $timeStamp = strtotime('+' . $pIntervalValue . ' days', $timeStamp);
            $nextRunDate = date('Y-m-d H:i:s', $timeStamp);

            break;
    }

    return $nextRunDate;
}

?>