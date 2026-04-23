<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsDatabase.php');

class AdminScheduledTasks_model
{
	static function initialize()
	{
		$resultArray = Array();
	    $schedulerActive = 0;
    	$schedulerLastRunTime = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
	    {
	        if ($stmt = $dbObj->prepare('SELECT `cronlastruntime`, `cronactive` FROM `systemconfig`'))
	        {
	        	if ($stmt->execute())
                {
    				if ($stmt->store_result())
					{
    					if ($stmt->num_rows > 0)
						{
				        	if ($stmt->bind_result($schedulerLastRunTime, $schedulerActive))
				            {
		                    	if (!$stmt->fetch())
		                    	{
		                    		$error = 'initialize fetch ' . $dbObj->error;
		                    	}
			                }
			                else
			                {
	                    		$error = 'initialize bind result ' . $dbObj->error;
			                }
			            }
		           	}
		           	else
		           	{
		           		$error = 'initialize store result ' . $dbObj->error;
		           	}
                }
                else
                {
                	$error = 'initialize execute ' . $dbObj->error;
                }
                $stmt->free_result();
	            $stmt->close();
	            $stmt = null;
            }
            else
            {
            	$error = 'initialize prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }

        $resultArray['schedulerActive'] = $schedulerActive;
        $resultArray['schedulerLastRunTime'] = $schedulerLastRunTime;

        return $resultArray;
	}

	static function displayList()
	{
        global $gConstants;

        $resultArray = array();
        $summaryArray = array();
        $taskArray = array();
        $start = (isset($_POST['start'])) ? (integer)$_POST['start'] : '0';
        $limit = (isset($_POST['limit'])) ? (integer)$_POST['limit'] : '100';
        $sortBy = (isset($_POST['sort'])) ? $_POST['sort'] : '';
        $sortDir = (isset($_POST['dir'])) ? $_POST['dir'] : '';
        $totalCount = 0;
        $searchFields = UtilsObj::getPOSTParam('fields');
        $typesArray = array();
		$paramArray = array();
		$stmtArray = array();

		if ($searchFields != '')
		{
			$searchQuery = $_POST['query'];
			$selectedfields = explode(',', str_replace('"', "", str_replace("]", "", str_replace("[", "",$_POST['fields']))));

			if ($searchQuery != '')
			{
				foreach ($selectedfields as $value)
				{
					switch ($value)
    				{
    					case 'code':
    						$value = 'taskcode';
    					break;
    					case 'name':
    						$value = 'taskname';
    					break;
    				}
					$stmtArray[] = '(`'.$value.'` LIKE ?)';
					$paramArray[] = '%'.$searchQuery.'%';
					$typesArray[] = 's';
				}
			}
		}

		$customSort = '';
    	if ($sortBy != '')
    	{
    		switch ($sortBy)
    		{
    			case 'recordid':
    				$sortBy = 'id ' . $sortDir;
    			break;
    			case 'code':
    				$sortBy = 'taskcode ' . $sortDir;
    			break;
    			case 'name':
    				$sortBy = 'taskname ' . $sortDir;
    			break;
    			case 'lastRunTime':
    				$sortBy = 'lastruntime ' . $sortDir;
    			break;
    			case 'nextRunTime':
    				$sortBy = 'nextruntime ' . $sortDir;
    			break;
    			case 'internal':
    				$sortBy = 'internal ' . $sortDir;
    			break;
    			case 'active':
    				$sortBy = 'active ' . $sortDir;
    			break;
    		}
    		$customSort = ' ' . $sortBy;
    	}

		$smarty = SmartyObj::newSmarty('AdminScheduledTasks');

		// get the current date time format so that we can use it within the loop
		$dateTimeFormat = LocalizationObj::getLocaleDateTimeFormat();

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			if (count($stmtArray) > 0)
            {
                if ($gConstants['optiondesol'])
                {
                	$stmtArray = ' WHERE (' . join(' OR ', $stmtArray) . ')';
                }
                else
                {
                	$stmtArray = ' WHERE (' . join(' OR ', $stmtArray) . ')';
                	$stmtArray .= ' AND `taskcode` <> "TAOPIX_ONLINEASSETPUSH" AND `taskcode` <> "TAOPIX_ONLINEORDERCREATION"';
                }
            }
            else
            {
                if ($gConstants['optiondesol'])
                {
					if ($gConstants['optionholdes'])
					{
						$stmtArray = ' WHERE (`taskcode` <> "TAOPIX_ONLINEPURGETASK") && (`taskcode` <> "TAOPIX_ONLINEARCHIVETASK")';
					}
					else
					{
	                	$stmtArray = '';
					}
                }
                else
                {
                	$stmtArray = ' WHERE `taskcode` <> "TAOPIX_ONLINEASSETPUSH" AND `taskcode` <> "TAOPIX_ONLINEORDERCREATION"';
                }
            }

			//filter connector tasks if not included in license
			if (!$gConstants['optionscntr'])
			{
				$operator = ($stmtArray = '') ? ' AND ' : ' WHERE ';
				$stmtArray .= $operator . '`taskcode` <> "TAOPIX_CONNECTORPRODUCTSYNC" AND `taskcode` <> "TAOPIX_CONNECTORPROCESSSYNCRESULTS" AND `taskcode` <> "TAOPIX_CONNECTORPOPULATEDISCOUNTDATACACHE" AND `taskcode` <> "TAOPIX_CONNECTORSHIPPINGPROFILECACHE"';
			}

			if ($stmt = $dbObj->prepare('SELECT SQL_CALC_FOUND_ROWS `id`, `datecreated`, `taskcode`, `taskname`, `maxruncount`, `intervaltype`, `intervalvalue`, `lastruntime`,
    			`nextruntime`, `statuscode`, `statusmessage`, `runstatus`, `scriptfilename`, `deleteexpiredinterval`, `internal`, `active` FROM TASKS'. $stmtArray .
    			' ORDER BY '.$customSort. ' LIMIT ' . $limit . ' OFFSET ' . $start))
			{
				if ($stmt)
				{
					$bindOK = DatabaseObj::bindParams($stmt, $typesArray, $paramArray);

					if ($bindOK)
					{
						$stmt->bind_result($id, $dateCreated, $taskCode, $taskName, $maxRunCount, $intervalType, $intervalValue, $lastRunTime, $nextRunTime, $statusCode, $statusMessage, $runStatus, $scriptFileName, $deleteExpiredInterval, $internal, $active);

						if ($stmt->execute())
						{
							while ($stmt->fetch())
							{
								// remove all not supported characters
								$taskCode = preg_replace('/[^ \w]+/', '', $taskCode);

								$taskArray['recordid'] = "'" . $id . "'";
								$taskArray['code'] = "'" . UtilsObj::ExtJSEscape($taskCode) . "'";
								$taskArray['name'] = "'" . UtilsObj::ExtJSEscape(LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $taskName, 'black')) . "'";

								$lastRunTime = ($lastRunTime == '0000-00-00 00:00:00') ? UtilsObj::ExtJSEscape($smarty->get_config_vars("str_LabelNever")) : UtilsObj::ExtJSEscape(LocalizationObj::formatDateTime($lastRunTime, $dateTimeFormat));
								$nextRunTime = ($nextRunTime == '0000-00-00 00:00:00') ? UtilsObj::ExtJSEscape($smarty->get_config_vars("str_LabelNextRun")) : UtilsObj::ExtJSEscape(LocalizationObj::formatDateTime($nextRunTime, $dateTimeFormat));

								$taskArray['lastRunTime'] = "'" . $lastRunTime . "'";
								$taskArray['status'] = "''";

								if ($runStatus == 0)
								{
									if ($statusCode == 2)
									{
										$taskArray['status'] = "'" . UtilsObj::ExtJSEscape($smarty->get_config_vars("str_TaskStatusCompleted")) . "'";
									}
									else
									{
										if ($statusCode == 1)
										{
											if ($statusMessage != '')
											{
												if ((substr($statusMessage, 0, 1) == '{')  && (substr($statusMessage, -1) == '}'))
												{
													$taskArray['status'] = "'" . UtilsObj::ExtJSEscape($smarty->get_config_vars(substr($statusMessage, 1, -1))) . "'";
												}
												else
												{
													$taskArray['status'] = "'" . UtilsObj::ExtJSEscape(LocalizationObj::getLocaleString($statusMessage, '')) . "'";
												}
											}
											else
											{
												$taskArray['status'] = "'" . UtilsObj::ExtJSEscape($smarty->get_config_vars("str_Error")) . "'";
											}
										}
									}
								}

								$taskArray['nextRunTime'] = "'" . UtilsObj::ExtJSEscape($nextRunTime) . "'";
								$taskArray['runStatus'] = "'" . UtilsObj::ExtJSEscape($runStatus) . "'";
								$taskArray['internal'] = "'" . UtilsObj::ExtJSEscape($internal) . "'";
								$taskArray['active'] = "'" . UtilsObj::ExtJSEscape($active) . "'";
								$taskArray['statusCode'] = "'" . UtilsObj::ExtJSEscape($statusCode) . "'";

								array_push($resultArray, '['.join(',', $taskArray).']');
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'initialize execute ' . $dbObj->error;
						}

						if (($stmt = $dbObj->prepare("SELECT FOUND_ROWS()")) && ($stmt->bind_result($totalCount)))
						{
							if ($stmt->execute())
							{
								$stmt->fetch();
							}
						}

						$stmt->free_result();
						$stmt->close();
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'initialize bind ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'initialize prepare ' . $dbObj->error;
				}
		    }
			$dbObj->close();
		}
		else
		{
			$result = 'str_DatabaseError';
			$resultParam = 'initialize connect ' . $dbObj->error;
		}

		$summaryArray = join(',', $resultArray);
        if ($summaryArray != '')
        {
        	$summaryArray = ', ' . $summaryArray;
        }

 		echo '[['.$totalCount.']'.$summaryArray.']';
        return;
	}



	static function displayEdit($pTaskId)
	{
		$resultArray = Array();

	    $id = 0;
    	$dateCreated = '';
    	$taskCode = '';
    	$taskName = '';
    	$intervalType = 1;
    	$intervalValue = 1;
    	$lastRunTime = '';
    	$nextRunTime = '';
    	$statusCode = 0;
    	$statusMessage = '';
    	$runStatus = 0;
    	$maxRunCount = 0;
    	$internal = 0;
    	$scriptFileName = '';
    	$deleteExpiredInterval = 0;
    	$active = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
	    {
	        if ($stmt = $dbObj->prepare('SELECT `id`, `dateCreated`, `taskCode`, `taskName`, `intervalType`, `intervalValue`, `lastRunTime`, `nextRunTime`, `statusCode`
	        , `statusMessage`, `runStatus`, `maxRunCount`, `internal`, `scriptFileName`, `deleteExpiredInterval`, `active` FROM `TASKS` WHERE `id` = ?'))
	        {
		        if ($stmt->bind_param('i', $pTaskId))
	            {
	               	if ($stmt->execute())
					{
	        			if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0)
							{
				                if ($stmt->bind_result($id, $dateCreated, $taskCode, $taskName, $intervalType, $intervalValue, $lastRunTime, $nextRunTime, $statusCode, $statusMessage, $runStatus, $maxRunCount, $internal, $scriptFileName, $deleteExpiredInterval, $active))
				                {
		                            if (!$stmt->fetch())
		                            {
		                            	$error = 'displayEdit fetch ' . $dbObj->error;
		                            }
			                    }
			                    else
			                    {
			                    	$error = 'displayEdit bind result ' . $dbObj->error;
			                    }
			                }
		                }
		                else
		                {
		                	$error = 'displayEdit store result ' . $dbObj->error;
		                }
	                }
	                else
	                {
	                	$error = 'displayEdit execute ' . $dbObj->error;
	                }
                }
                else
                {
                	$error = 'displayEdit bind params ' . $dbObj->error;
                }

                $stmt->free_result();
	            $stmt->close();
	            $stmt = null;
            }
            $dbObj->close();
        }

        $resultArray['id'] = $id;
        $resultArray['dateCreated'] = $dateCreated;
        $resultArray['taskCode'] = $taskCode;
        $resultArray['taskName'] = $taskName;
        $resultArray['intervalType'] = $intervalType;
        $resultArray['intervalValue'] = $intervalValue;
        $resultArray['lastRunTime'] = $lastRunTime;
        $resultArray['nextRunTime'] = $nextRunTime;
        $resultArray['statusCode'] = $statusCode;
        $resultArray['statusMessage'] = $statusMessage;
        $resultArray['runStatus'] = $runStatus;
        $resultArray['maxRunCount'] = $maxRunCount;
        $resultArray['internal'] = $internal;
        $resultArray['scriptFileName'] = $scriptFileName;
        $resultArray['deleteExpiredInterval'] = $deleteExpiredInterval;
        $resultArray['active'] = $active;

        return $resultArray;
	}


	static function taskActivate()
	{
		global $gSession;
		$taskList  = explode(',',$_POST['idlist']);
        $taskCount = count($taskList);
        $result = '';
		$resultParam = '';
		$isActive = $_POST['active'];

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			for ($i = 0; $i < $taskCount; $i++)
			{
				$id = $taskList[$i];
				$taskArray = DatabaseObj::getTask('', $id);

				if ($stmt = $dbObj->prepare('UPDATE `TASKS` SET `active` = ? WHERE `id` = ?'))
				{
					if ($stmt->bind_param('ii', $isActive, $id))
					{
						if ($stmt->execute())
						{
							if ($taskArray['active'] == 1)
							{
								DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
									'ADMIN', 'TASK-DEACTIVATE', $id . ' ' . $taskArray['taskCode'], 1);
							}
							else
							{
								DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
									'ADMIN', 'TASK-ACTIVATE', $id . ' ' . $taskArray['taskCode'], 1);
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'taskActivate execute ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'taskActivate bind ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'taskActivate prepare ' . $dbObj->error;
				}
			}
			$dbObj->close();
		}
		else
		{
			$result = 'str_DatabaseError';
			$resultParam = 'taskActivate connect ' . $dbObj->error;
		}

		if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }
		return;
	}



	static function taskDelete()
	{
		global $gSession;

		$taskList  = explode(',',$_POST['idlist']);
        $taskCount = count($taskList);
        $result = '';
		$resultParam = '';
		$notDeletedArray = array();

		$dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
		{
        	for ($i = 0; $i < $taskCount; $i++)
        	{
        		$taskID = $taskList[$i];
        		$taskArray = DatabaseObj::getTask('', $taskID);

        		if ($taskArray['internal'] == 1)
        		{
        			$notDeletedArray[] = "'" . $taskArray['taskCode'] . "'";
        		}
        		else
        		{
        			if ($stmt = $dbObj->prepare('DELETE FROM `TASKS` WHERE `id` = ?'))
					{
						if ($stmt->bind_param('i', $taskID))
						{
							if ($stmt->execute())
							{
								DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
									'ADMIN', 'TASK-DELETE', $taskID . ' ' . $taskArray['taskCode'], 1);
							}
							else
							{
								$result = 'str_DatabaseError';
								$resultParam = 'taskDelete execute ' . $dbObj->error;
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'taskDelete bind ' . $dbObj->error;
						}
						$stmt->free_result();
						$stmt->close();
						$stmt = null;
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'taskDelete prepare ' . $dbObj->error;
					}
        		}
        	}
        	$dbObj->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'taskDelete connect ' . $dbObj->error;
		}

		$smarty = SmartyObj::newSmarty('AdminScheduledTasks');
		$title = $smarty->get_config_vars('str_TitleConfirmation');

		if ($result == '')
		{
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$message = $smarty->get_config_vars('str_MessageTasksDeleted');

			if (count($notDeletedArray) > 0)
			{
				$title = $smarty->get_config_vars('str_TitleWarning');
				$message = str_replace("'^0'", join(', ', $notDeletedArray), $smarty->get_config_vars('str_ErrorDeleteInternalTask'));
			}
			echo "{'success':'true', 'title':'" . UtilsObj::ExtJSEscape($title) . "', 'msg':'" . UtilsObj::ExtJSEscape($message) . "' }";
		}
		else
		{
			echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }
		return;
	}



	static function taskEdit()
	{

		$result = '';
		$resultParam = '';

		$id = $_POST['id'];
		$intervalType = $_POST['intervalType'];
		$name = UtilsObj::decodeString($_POST['name']);
		$active = $_POST['active'];
		$intervalValue = $_POST['intervalValue'];
		$maxRunCount = $_POST['maxRunCount'];
		$deleteExpiredInterval = $_POST['deleteExpiredInterval'];
		$result = '';
		$resultParam = '';
		$dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
        	if ($stmt = $dbObj->prepare('UPDATE `TASKS` SET `taskname` = ?, `intervaltype` = ?, `intervalvalue` = ?, `maxruncount` = ?, `deleteexpiredinterval` = ?, `active` = ?  WHERE `id` = ?'))
			{
				if ($stmt->bind_param('sisiiii', $name, $intervalType, $intervalValue, $maxRunCount, $deleteExpiredInterval, $active, $id))
				{
					$stmt->execute();
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'taskEdit bind ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'taskEdit prepare ' . $dbObj->error;
			}
			$dbObj->close();
        }

        if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			$smarty = SmartyObj::newSmarty('AdminScheduledTasks');
        	echo '{"success":false,	"msg":"' . $smarty->get_config_vars($result) . '"}';
        }
	}


	static function taskAdd()
	{
		$result = '';
		$resultParam = '';

		$code = $_POST['code'];
		$scriptName = $_POST['scriptName'];
		$intervalType = $_POST['intervalType'];
		$name = $_POST['name'];
		$active = $_POST['active'];
		$intervalValue = $_POST['intervalValue'];
		$maxRunCount = $_POST['maxRunCount'];
		$deleteExpiredInterval = $_POST['deleteExpiredInterval'];
		$result = '';
		$resultParam = '';
		$dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
        	if ($stmt = $dbObj->prepare('SELECT `id` FROM `TASKS` WHERE `taskCode` = ?'))
            {
            	if ($stmt->bind_param('s', $code))
                {
                	if ($stmt->bind_result($id))
                    {
                    	if ($stmt->execute())
                        {
                        	if ($stmt->fetch())
                            {
                            	$result = 'str_ErrorDuplicateTaskCode';
								$resultParam = '';
                            }
                        }
                        else
                        {
                        	$result = 'str_DatabaseError';
                            $resultParam = 'taskAdd execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'taskAdd bind result ' . $dbObj->error;
                    }
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'taskAdd bind ' . $dbObj->error;
                }
                $stmt->free_result();
                $stmt->close();
            }
            else
            {
                $result = 'str_DatabaseError';
                $resultParam = 'taskAdd prepare ' . $dbObj->error;
            }

        	if ($result == '')
        	{
        		if ($stmt = $dbObj->prepare('INSERT INTO `TASKS` (`datecreated`, `taskcode`, `taskname`, `intervaltype`, `intervalvalue`, `maxruncount`, `scriptfilename`, `deleteexpiredinterval`, `active`) VALUES (now(), ?, ?, ?, ?, ?, ?, ?, ?)'))
				{
					if ($stmt->bind_param('ssisisii', $code, $name, $intervalType, $intervalValue, $maxRunCount, $scriptName, $deleteExpiredInterval, $active))
					{
						$stmt->execute();
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'taskAdd bind ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'taskAdd prepare ' . $dbObj->error;
				}
			}
			$dbObj->close();
        }

        if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			$smarty = SmartyObj::newSmarty('AdminScheduledTasks');
        	echo '{"success":false,	"msg":"' . $smarty->get_config_vars($result). (($resultParam != '') ? ': '. $resultParam : '') . '"}';
        }
		return;
	}


	static function getScriptInfo()
	{
		$scriptName = isset($_GET['scriptName']) ? $_GET['scriptName'] : '';
		$smarty = SmartyObj::newSmarty('AdminScheduledTasks');

		if ($scriptName != '')
		{
			$scriptPath = rtrim(rtrim(str_replace('webroot', 'tasks', getcwd()), '/'), '\\');
			$scriptPath = $scriptPath . DIRECTORY_SEPARATOR . $scriptName;

			if (file_exists($scriptPath))
			{
				include_once($scriptPath);
			}
			else
			{
				$taskResult = $smarty->get_config_vars('str_ErrorScriptNotFound');
			}

			$className = str_replace('.php', '', $scriptName);
			if(method_exists($className, 'register'))
			{
				$taskResult = call_user_func(array($className, 'register'));

				$names = explode('<p>', $taskResult['name']);
				$localizedCodes = array();
				$localizedValues = array();
				foreach ($names as $name)
				{
					$values = explode(' ', $name);
					$lang = array_shift($values);
					$value = implode(' ', $values);
					$localizedCodes[] = '"' . UtilsObj::ExtJSEscape($lang) . '"';
					$localizedValues[] = '"' . UtilsObj::ExtJSEscape($value) . '"';
				}
				$localizedCodes = '[' . implode(',', $localizedCodes) . ']';
				$localizedValues = '[' . implode(',', $localizedValues). ']';

				echo '['.
					"'" . UtilsObj::ExtJSEscape($taskResult['code']) . "'".','.
					"'" . UtilsObj::ExtJSEscape($taskResult['intervalType']) . "'".','.
					"'" . UtilsObj::ExtJSEscape($taskResult['intervalValue'])."','".
					$localizedCodes."','".
					$localizedValues."','".
					UtilsObj::ExtJSEscape($taskResult['maxRunCount']) . "'".','.
					"'" . UtilsObj::ExtJSEscape($taskResult['deleteCompletedDays']) . "'". ']';
        		return;
			}
			else
			{
				$taskResult = $smarty->get_config_vars('str_ErrorNoRunMethod');
			}
		}
		else
		{
			$taskResult = $smarty->get_config_vars('str_DatabaseError') . ' getScriptInfo no script name provided';
		}
		echo $taskResult;
	}


	static function taskSchedulerActivate()
	{
		global $gSession;
		$result = '';
		$resultParam = '';
        $schedulerActive = 0;
		$isActive = $_POST['active'];

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('SELECT `cronactive` FROM `systemconfig`'))
	        {
    			if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						if ($stmt->num_rows > 0)
						{
				        	if ($stmt->bind_result($schedulerActive))
				            {
		                    	if (!$stmt->fetch())
		                    	{
		                    		$error = 'taskSchedulerActivate fetch ' . $dbObj->error;
		                    	}
			                }
			                else
			                {
			                	$error = 'taskSchedulerActivate bind result ' . $dbObj->error;
			                }
			            }
		            }
		            else
		            {
		            	$error = 'taskSchedulerActivate store result ' . $dbObj->error;
		            }
	            }
	            else
	            {
	            	$error = 'taskSchedulerActivate execute ' . $dbObj->error;
	            }
                $stmt->free_result();
	            $stmt->close();
	            $stmt = null;
            }
            else
            {
            	$error = 'taskSchedulerActivate prepare ' . $dbObj->error;
            }

			if ($stmt = $dbObj->prepare('UPDATE `systemconfig` SET `cronactive` = ?'))
			{
				if ($stmt->bind_param('i', $isActive))
				{
					if ($stmt->execute())
					{
						if ($schedulerActive == 1)
						{
							DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
								'ADMIN', 'TASKS-DEACTIVATE', '', 1);
						}
						else
						{
							DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
								'ADMIN', 'TASKS-ACTIVATE', '', 1);
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'taskSchedulerActivate execute ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'taskSchedulerActivate bind ' . $dbObj->error;
				}
				$stmt->free_result();
				$stmt->close();
                $stmt = null;
			}
			else
			{
				$result = 'str_DatabaseError';
				$resultParam = 'taskSchedulerActivate prepare ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			$result = 'str_DatabaseError';
			$resultParam = 'taskSchedulerActivate connect ' . $dbObj->error;
		}

		if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }
		return;
	}


	static function taskRunNow()
	{
		global $gSession;
		$taskList  = explode(',',$_POST['idlist']);
        $taskCount = count($taskList);
        $result = '';
		$resultParam = '';
		$nextRunTime = date('Y-m-d H:i:s', time());

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			for ($i = 0; $i < $taskCount; $i++)
			{
				$id = $taskList[$i];

				if ($stmt = $dbObj->prepare('UPDATE `TASKS` SET `nextruntime` = ? WHERE `id` = ?'))
				{
					if ($stmt->bind_param('si', $nextRunTime, $id))
					{
						if ($stmt->execute())
						{
							DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
								'ADMIN', 'TASK-RESETNEXTRUN', $id . ' ' . $nextRunTime, 1);
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'taskActivate execute ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'taskRunNow bind ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'taskRunNow prepare ' . $dbObj->error;
				}
			}
			$dbObj->close();
		}
		else
		{
			$result = 'str_DatabaseError';
			$resultParam = 'taskRunNow connect ' . $dbObj->error;
		}

		if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }
		return;
	}

}
?>
