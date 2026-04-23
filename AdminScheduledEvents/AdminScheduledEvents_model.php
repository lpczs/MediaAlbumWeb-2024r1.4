<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsDatabase.php');

class AdminScheduledEvents_model 
{
	static function displayList()
	{
        
        $resultArray  = array();
        $summaryArray = array();
        $eventsArray = array();
        $start = (isset($_POST['start'])) ? (integer)$_POST['start'] : '0'; 
        $limit = (isset($_POST['limit'])) ? (integer)$_POST['limit'] : '100';    
        $sortBy = (isset($_POST['sort'])) ? $_POST['sort'] : '';
        $sortDir = (isset($_POST['dir'])) ? $_POST['dir'] : '';
        $eventStatus = (isset($_POST['eventStatus'])) ? (integer)$_POST['eventStatus'] : 0; 
        $totalCount = 0;

        //init cache for localization
        LocalizationObj::formatLocaleDateTime('0000-00-00 00:00:00');
        
        $searchFields = UtilsObj::getPOSTParam('fields');
        
        $typesArray = array();
		$paramArray = array();
		$stmtArray = array();
		$statusArray = array();
		
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
    					case 'taskcode':    $value = 'taskcode'; break;	
    					case 'runcount':    $value = 'runcount'; break;	
    					case 'maxruncount':    $value = 'maxruncount'; break;	
    				}
					$stmtArray[] = '(`'.$value.'` LIKE ?)';
					$paramArray[] = '%'.$searchQuery.'%';
					$typesArray[] = 's';
				}
			}	
		}
		
        switch ($sortBy) 
        {
            case 'recordid': 
                $sortBy = 'id ' . $sortDir; 
            break;
            case 'taskcode': 
                $sortBy = 'taskcode ' . $sortDir; 
            break;
            case 'runcount': 
                $sortBy = 'runcount ' . $sortDir; 
            break;	
            case 'maxruncount': 
                $sortBy = 'maxruncount ' . $sortDir; 
            break;	
            case 'lastruntime': 
                $sortBy = 'lastruntime ' . $sortDir;	
            break;
            case 'nextRunTime': 
                $sortBy = 'nextruntime ' . $sortDir;	
            break;
            case 'active': 
                $sortBy = 'active ' . $sortDir;	
            break;
            case 'priority': 
                $sortBy = 'priority ' . $sortDir;	
            break;
        }

    	if ($eventStatus == 0)
    	{
			$statusArray[] = '(`statuscode` = ?)';
			$paramArray[] = 0;
			$typesArray[] = 'i';

			$statusArray[] = '(`statuscode` = ?)';
			$paramArray[] = 1;
			$typesArray[] = 'i';
    	}
    	else
    	{	
			$statusArray[] = '(`statuscode` = ?)';
			$paramArray[] = $eventStatus;
			$typesArray[] = 'i';
    	}
    	
    	$smarty = SmartyObj::newSmarty('AdminScheduledEvents');
		
		// get the current date time format so that we can use it within the loop
		$dateTimeFormat = LocalizationObj::getLocaleDateTimeFormat();
		
		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			if (count($stmtArray) > 0)
            {
                $stmtArray = ' WHERE (' . join(' OR ', $stmtArray) . ') AND (' . join(' OR ', $statusArray) . ')';
            }
            else
            {
                $stmtArray = ' WHERE (' . join(' OR ', $statusArray) . ')';
            }

			if ($stmt = $dbObj->prepare('SELECT SQL_CALC_FOUND_ROWS `id`, `datecreated`, `companycode`, `groupcode`, `webbrandcode`, `taskcode`, `runcount`, `maxruncount`, 
				`lastruntime`, `nextruntime`, `statuscode`, `statusmessage`, `priority`, `active` FROM EVENTS'. $stmtArray . 
    			' ORDER BY '. $sortBy . ' LIMIT ' . $limit . ' OFFSET ' . $start))
			{
				if ($stmt)
				{
					$bindOK = DatabaseObj::bindParams($stmt, $typesArray, $paramArray);
					
					if ($bindOK)
					{
						$stmt->bind_result($id, $dateCreated, $companyCode, $groupCode, $webBrandCode, $taskCode, $runCount, $maxRunCount, 
							$lastRunTime, $nextRunTime, $statusCode, $statusMessage, $priority, $active);
					
						if ($stmt->execute())
						{
							while ($stmt->fetch())
							{
								// remove all not supported characters
								$taskCode = preg_replace('/[^ \w]+/', '', $taskCode);

								$eventsArray['recordid'] = "'" . $id . "'";
								$eventsArray['taskcode'] = "'" . UtilsObj::ExtJSEscape($taskCode) . "'";
								$eventsArray['runcount'] = "'" . UtilsObj::ExtJSEscape($runCount) . "'";
								$eventsArray['maxruncount'] = "'" . UtilsObj::ExtJSEscape($maxRunCount) . "'";
								
								$lastRunTime = ($lastRunTime == '0000-00-00 00:00:00') ? UtilsObj::ExtJSEscape($smarty->get_config_vars("str_LabelNever")) : UtilsObj::ExtJSEscape(LocalizationObj::formatDateTime($lastRunTime, $dateTimeFormat));
								$nextRunTime = ($nextRunTime == '0000-00-00 00:00:00') ? UtilsObj::ExtJSEscape($smarty->get_config_vars("str_LabelNextRun")) : UtilsObj::ExtJSEscape(LocalizationObj::formatDateTime($nextRunTime, $dateTimeFormat));
								
								if ($statusCode == 2) // finished
								{
									$nextRunTime = UtilsObj::ExtJSEscape($smarty->get_config_vars("str_LabelNever"));
								}
								
								$eventsArray['lastRunTime'] = "'" . $lastRunTime . "'";
								$eventsArray['nextRunTime'] = "'" . UtilsObj::ExtJSEscape($nextRunTime) . "'";
								
								$eventsArray['status'] = "''";
								
								if ($statusCode == 2)
								{
									$eventsArray['status'] = "'" . UtilsObj::ExtJSEscape($smarty->get_config_vars("str_EventStatusCompleted")) . "'";
								}
								else
								{
									if ($statusMessage != '')
									{
										if ((substr($statusMessage, 0, 1) == '{')  && (substr($statusMessage, -1) == '}'))
										{
											$eventsArray['status'] = "'" . UtilsObj::ExtJSEscape($smarty->get_config_vars(substr($statusMessage, 1, -1))) . "'";
										}
										else
										{
											$eventsArray['status'] = "'" . UtilsObj::ExtJSEscape(LocalizationObj::getLocaleString($statusMessage, '')) . "'";
										}
									}
									else	
									{
										// error without error message
										if ($statusCode == 1)
										{
											$eventsArray['status'] = "'" . UtilsObj::ExtJSEscape($smarty->get_config_vars("str_Error")) . "'";
										}
									}
								}
								
								
								$eventsArray['statusCode'] = "'" . UtilsObj::ExtJSEscape($statusCode) . "'";
								
								$eventsArray['active'] = "'" . UtilsObj::ExtJSEscape($active) . "'";
								
								$eventsArray['priority'] = "'" . UtilsObj::ExtJSEscape($priority) . "'";
								
								array_push($resultArray, '['.join(',', $eventsArray).']');
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'displayList execute ' . $dbObj->error;
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
						$resultParam = 'displayList bind ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'displayList prepare ' . $dbObj->error;
				}
		    }      
			$dbObj->close();
		}
		else
		{
			$result = 'str_DatabaseError';
			$resultParam = 'displayList connect ' . $dbObj->error;
		}
		
		$summaryArray = join(',', $resultArray);
        if ($summaryArray != '')
        {
        	$summaryArray = ', ' . $summaryArray;
        }
 
 		echo '[['.$totalCount.']'.$summaryArray.']';
        return;
	}
	
	
	static function eventDelete()
	{
		global $gSession;
		
		$eventList  = explode(',',$_POST['idlist']);
        $eventCount = count($eventList);
        $result = '';
		$resultParam = '';
		$recordID = 0;
		
		$dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
		{
        	for ($i = 0; $i < $eventCount; $i++)
        	{
        		$eventID = $eventList[$i];
        		
        		if ($stmt = $dbObj->prepare('DELETE FROM `EVENTS` WHERE `id` = ?'))
				{
					if ($stmt->bind_param('i', $eventID))
					{
						if ($stmt->execute())
						{
							DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'EVENT-DELETE', $eventID, 1);	
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'eventDelete execute ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'eventDelete bind ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'eventDelete prepare ' . $dbObj->error;
				}
        	}
        	$dbObj->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'eventDelete connect ' . $dbObj->error;
		}
		
		$smarty = SmartyObj::newSmarty('AdminScheduledEvents');
		
		if ($result == '')
		{
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$message = $smarty->get_config_vars('str_MessageEventsDeleted');
			echo "{'success':'true', 'title':'" . UtilsObj::ExtJSEscape($title) . "', 'msg':'" . UtilsObj::ExtJSEscape($message) . "' }";
		}
		else
		{
			echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }
		return;
	}
	
	
	
	static function eventActivate()
	{
		global $gSession;
		$eventList  = explode(',',$_POST['idlist']);
        $eventCount = count($eventList);
        $result = '';
		$resultParam = '';
		$recordID = 0;
		$isActive = $_POST['active'];
		
		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			for ($i = 0; $i < $eventCount; $i++)
			{
				$id = $eventList[$i];
				//$taskArray = DatabaseObj::getTask('', $id);  
			
				if ($stmt = $dbObj->prepare('UPDATE `EVENTS` SET `active` = ? WHERE `id` = ?'))
				{
					if ($stmt->bind_param('ii', $isActive, $id))
					{
						if ($stmt->execute())
						{
							if ($isActive == 1)
							{
								DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 
									'ADMIN', 'EVENT-DEACTIVATE', $id, 1);
							}
							else
							{
								DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 
									'ADMIN', 'EVENT-ACTIVATE', $id, 1);
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'eventActivate execute ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'eventActivate bind ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'eventActivate prepare ' . $dbObj->error;
				}
			}
			$dbObj->close();
		}
		else
		{
			$result = 'str_DatabaseError';
			$resultParam = 'eventActivate connect ' . $dbObj->error;
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
	
	
	static function detailsDisplay($pEventId)
	{
		$resultArray = Array();
			    
	    $id = 0;
    	$dateCreated = '';
    	$taskCode = '';
    	$companyCode = '';
    	$groupCode = '';
    	$webBrandCode = '';
    	$runCount = 0;
    	$maxRunCount = 0;
    	$lastRunTime = '';
    	$nextRunTime = '';
    	$status = '';
    	$priority = 0;
    	$active = 0;
    	$statusCode = 0;
    	$statusMessage = '';
    	
        $dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
	    {
	        if ($stmt = $dbObj->prepare('SELECT `id`, `datecreated`, `companycode`, `groupcode`, `webbrandcode`, `taskcode`, `runcount`, `maxruncount`, `lastruntime`, 
	        	`nextruntime`, `statuscode`, `statusmessage`, `priority`, `active` FROM `EVENTS` WHERE `id` = ?'))
	        {
	           if ($stmt->bind_param('i', $pEventId))
	           {
	               	if ($stmt->execute())
					{
           				if ($stmt->store_result())
						{
           					if ($stmt->num_rows > 0)
							{ 
				               	if ($stmt->bind_result($id, $dateCreated, $companyCode, $groupCode, $webBrandCode, $taskCode, $runCount, $maxRunCount, $lastRunTime, $nextRunTime, 
				               		$statusCode, $statusMessage, $priority, $active))
				               	{	
		                            if (!$stmt->fetch())
		                            {
            							$error = 'detailsDisplay fetch ' . $dbObj->error;
		                            }	
			                    }
			                    else
			                    {
			                    	$error = 'detailsDisplay bind result ' . $dbObj->error;
			                    }
			                }
		                }
		                else
		                {
		                	$error = 'detailsDisplay store result ' . $dbObj->error;
		                }
	                }
	                else
	                {
	                	$error = 'detailsDisplay execute ' . $dbObj->error;
	                }	                
                }
                else
                {
                	$error = 'detailsDisplay bind params ' . $dbObj->error;
                }
                
                $stmt->free_result();
	            $stmt->close();
	            $stmt = null;
            }
            else
            {
            	$error = 'detailsDisplay prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }

        $resultArray['id'] = $id;
        $resultArray['datecreated'] = $dateCreated;
        $resultArray['companycode'] = $companyCode;
        $resultArray['groupcode'] = $groupCode;
        $resultArray['webbrandcode'] = $webBrandCode;
        $resultArray['taskcode'] = $taskCode;
        $resultArray['runcount'] = $runCount;
        $resultArray['maxruncount'] = $maxRunCount;
        $resultArray['lastruntime'] = $lastRunTime;
        $resultArray['nextruntime'] = $nextRunTime;
        $resultArray['statuscode'] = $statusCode;
        $resultArray['statusmessage'] = $statusMessage;
        $resultArray['priority'] = $priority;
        $resultArray['active'] = $active;
     
     	return $resultArray;
	}
	
	
	static function eventEdit()
	{
		global $gSession;
		
		$result = '';
		$resultParam = '';
		
		$id = $_POST['id'];
		$active = $_POST['active'];
		$priority = $_POST['priority'];
		$runcount = $_POST['runcount'];
		
		
		$resultArray = array();
		$result = '';
		$resultParam = '';
		$dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
        	if ($stmt = $dbObj->prepare('UPDATE `EVENTS` SET `active` = ?, `priority` = ?, `runcount` = ? WHERE `id` = ?'))
			{
				if ($stmt->bind_param('iiii', $active, $priority, $runcount, $id))
				{
					$stmt->execute();
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'eventEdit bind ' . $dbObj->error;
				}
				
				$stmt->free_result();
				$stmt->close();
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'eventEdit prepare ' . $dbObj->error;
			}
			$dbObj->close();
        }
        
        if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			$smarty = SmartyObj::newSmarty('AdminScheduledEvents');
        	echo '{"success":false,	"msg":"' . $smarty->get_config_vars($result) . ' '. $resultParam . '"}';
        }
		return;
	}
	
	
	static function eventRun()
	{
		global $ac_config;
		
		$eventId = $_POST['id'];

		// we need to replace the backslashes the pathtophp to slashes for windows as the escapheshellcmd strips out the backslashes
		exec(escapeshellcmd(str_replace("\\", '/', $ac_config['PATHTOPHP'])) . ' ' . escapeshellarg(UtilsObj::getTaopixWebInstallPath('/tasks/taskManager.php')) . ' ' . escapeshellarg($eventId));
		echo "{'success':'true', 'msg':'" . '' . "' }";
	}

}


?>
