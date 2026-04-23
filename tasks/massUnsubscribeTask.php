<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('TPX_UNSUBSCRIBECUSTOMERS_BATCH_SIZE', 3000);
define('TPX_UNSUBSCRIBECUSTOMERS_NUMBER_OF_ITERATIONS', 10);

class massUnsubscribeTask
{
    // define default settings for this task
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
        $defaultSettings['code'] = 'TAOPIX_MASSUNSUBSCRIBE';
        $defaultSettings['name'] = 'it italian desciption<p>fr french description<p>es spanish text';

        /*
         * $defaultSettings('intervalType') defines inteval value
         * 1 - Number of minutes
         * 2 - Exact time of the day
         * 3 - Number of days
         */

        $defaultSettings['intervalType'] = '1';
        $defaultSettings['intervalValue'] = '5';
        $defaultSettings['maxRunCount'] = '10';
        $defaultSettings['deleteCompletedDays'] = '5';

        return $defaultSettings;
    }

    // function to run this task
    static function run($pEventID)
    {
        $result = '';
        $resultMessage = '';

        try
        {
            $pEventID = (int) $pEventID[0];

            $taskCode = self::register();
            $taskCode = $taskCode['code'];

			TaskObj::writeLogEntry('Task: ' . $taskCode . '. Retrieving Events.');

			if ($pEventID > 0)
			{
				$eventsList = TaskObj::getEventByID($pEventID);
			}
			else
			{
				$eventsList = TaskObj::getEventsByTaskCode($taskCode, 200);
			}

			if ($eventsList['result'] == '')
			{
				$eventsList = $eventsList['events'];
				$eventCount = count($eventsList);

				TaskObj::writeLogEntry('Task: ' . $taskCode . '. Found ' . $eventCount . ' Events.');

				for ($i = 0; $i < $eventCount; $i++)
				{
					$event = &$eventsList[$i];
					$eventRecordID = $event['id'];

					TaskObj::writeLogEntry('Task: ' . $taskCode . '. Executing Event ' . ($i + 1) .  ' Of ' . $eventCount . ' (' . $event['id'] . ').');

					UtilsObj::resetPHPScriptTimeout(30);

					$brandCode = $event['param1'];
					$startRecordID = $event['param4'];
					$maxUserID = $event['param5'];

					$eventCreationParams = array(
						'brandcode' => $event['param1'],
						'brandapplicationname' => $event['param2'],
						'branddisplayurl' => $event['param3'],
						'maxuserid' => $event['param5'],
						'eventcreationuserid' => $event['userid'],
						'eventcreationuserlogin' => $event['param6'],
						'eventcreationusername' => $event['param7'],
						'eventcreationsessionref' => $event['param8']);

					$iterationCount = 0;

					while ($iterationCount < TPX_UNSUBSCRIBECUSTOMERS_NUMBER_OF_ITERATIONS)
					{
						UtilsObj::resetPHPScriptTimeout(30);

						$getUsersToUnsubscribeResult = self::getUsersToUnsubscribe($brandCode, $startRecordID, $maxUserID);

						if ($getUsersToUnsubscribeResult['result'] == '')
						{
							$unsubscribeUsersResult = self::unsubscribeUsers($brandCode, $getUsersToUnsubscribeResult['data']['users'], $eventCreationParams);

							if ($unsubscribeUsersResult['result'] != '')
							{
								$result = $unsubscribeUsersResult['result'];
								$resultMessage = $unsubscribeUsersResult['resultparam'];
							}
						}
						else
						{
							$result = $getUsersToUnsubscribeResult['result'];
							$resultMessage = $getUsersToUnsubscribeResult['resultparam'];
						}

						$startRecordID = $getUsersToUnsubscribeResult['data']['lastrecordid'];

						if ($startRecordID == 0)
						{
							// if the startrecordid is 0 then there are no more records to process.
							break;
						}
						else
						{
							$iterationCount++;
						}

						if ($iterationCount == TPX_UNSUBSCRIBECUSTOMERS_NUMBER_OF_ITERATIONS)
						{
							// we have hit the max interations this task can do.
							// we must log a new mass unsubscribe task so the remaining records can be processed based off the last startRecordID (lastrecordid returned from the getUsersToUnsubscribe)
							DatabaseObj::createEvent('TAOPIX_MASSUNSUBSCRIBE', '', '', $brandCode, '', 0,	$brandCode,
										$eventCreationParams['brandapplicationname'], $eventCreationParams['branddisplayurl'], $startRecordID, $maxUserID, $eventCreationParams['eventcreationuserlogin'],
											$eventCreationParams['eventcreationusername'], $eventCreationParams['eventcreationsessionref'], 0, 0, $eventCreationParams['eventcreationuserid'], '', '', 0);
						}
					}

					if ($result == '')
					{
						TaskObj::updateEvent($eventRecordID, 2, '');
					}
					else
					{
						TaskObj::writeLogEntry('Task: ' . $taskCode . '. Error In Event ' . ($i + 1) .  ' Of ' . $eventCount .
																				' (' . $eventRecordID . ') - ' . $resultMessage);

						TaskObj::updateEvent($eventRecordID, 1, $resultMessage);
					}
				}
			}
			else
			{
				//return error message to taskManager
				$resultMessage = $eventsList['resultparam'];
			}
        }
        catch (Exception $e)
        {
            $resultMessage = 'en ' . $e->getMessage();
        }

        return $resultMessage;
    }

    static function getUsersToUnsubscribe($pBrandCode, $pStartRecordID, $pMaxUserID)
    {
		$result = '';
		$resultParam = '';
		$returnArray = array('result' => '', 'resultparam' => '', 'users' => array());

		$userDataArray = array('users' => array(), 'lastrecordid' => 0);
		$userID = 0;
		$emailAddress = '';
		$contactFirstName = '';
		$contactLastName = '';
		$login = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
		{
			// get a list of all the users who need the sendmarketinginfo flag setting to 0
			$sql = 'SELECT `id`, `emailaddress`, `contactfirstname`, `contactlastname`, `login`
					FROM `USERS`
					WHERE `webbrandcode` = ?
					AND `id` > ? AND `id` <= ?
					AND `customer` = 1
					AND `sendmarketinginfo` = 1
					ORDER BY `id`
					LIMIT ' . TPX_UNSUBSCRIBECUSTOMERS_BATCH_SIZE;

			if ($stmt = $dbObj->prepare($sql))
        	{
            	if ($stmt->bind_param('sii', $pBrandCode, $pStartRecordID, $pMaxUserID))
            	{
                	if ($stmt->bind_result($userID, $emailAddress, $contactFirstName, $contactLastName, $login))
                	{
                   		if ($stmt->execute())
                   		{
                        	while ($stmt->fetch())
                        	{

								// we should only process the user if the contactfirstname, contactlastname and emailaddress are not empty.
								// this is to prevent errors when trying to send the email.
                        		if (($contactFirstName != '') && ($contactLastName != '') && ($emailAddress != ''))
                        		{
                        			$user = array();
                        			$user['contactname'] = $contactFirstName . ' ' . $contactLastName;
                        			$user['emailaddress'] = $emailAddress;
									$user['login'] = $login;
									
                        			$userDataArray['users'][$userID] = $user;
								}

                        		// record the last recordid processed
                        		// this is so we know where to start on the next batch process from the calling function
                        		$userDataArray['lastrecordid'] = $userID;
                        	}
                   		}
                   		else
                   		{
							$result = 'str_DatabaseError';
							$resultParam = 'unsubscribeAllUsers select execute ' . $dbObj->error;
                   		}
                	}
                	else
                	{
						$result = 'str_DatabaseError';
						$resultParam = 'unsubscribeAllUsers select bind_result ' . $dbObj->error;
                	}
            	}
            	else
            	{
					$result = 'str_DatabaseError';
					$resultParam = 'unsubscribeAllUsers select bind_param ' . $dbObj->error;
            	}

            	$stmt->free_result();
            	$stmt->close();
            	$stmt = null;
        	}
        	else
        	{
				$result = 'str_DatabaseError';
				$resultParam = 'unsubscribeAllUsers select prepare ' . $dbObj->error;
        	}

        	$dbObj->close();
		}
		else
		{
			$result = 'str_DatabaseError';
			$resultParam = 'unsubscribeAllUsers no database connection';
		}

		$returnArray['result'] = $result;
		$returnArray['resultparam'] = $resultParam;
		$returnArray['data'] = $userDataArray;

		return $returnArray;
    }

    static function unsubscribeUsers($pBrandCode, $pUserDataArray, $pEventCreationParams)
    {
		global $gSession;

		$returnArray = array('result' => '', 'resultparam' => '');
		$result = '';
		$resultParam = '';

		$countUserIDs = count($pUserDataArray);

		if ($countUserIDs > 0)
		{
			$dbObj = DatabaseObj::getGlobalDBConnection();

			if ($dbObj)
			{
				// create a CSV for the IN part of the updates where clause
				$userIDString = implode(",", array_keys($pUserDataArray));

				// update all the users for the brand who have sendmarketinginfo on (1) and turn off the sendmarketinginfo setting (0)
				if ($stmt = $dbObj->prepare('UPDATE `USERS` SET `sendmarketinginfo` = 0 WHERE `id` IN (' . $userIDString . ')'))
				{
					if (! $stmt->execute())
					{
						$result = 'str_DatabaseError';
						$resultParam = 'unsubscribeAllUsers update execute ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'unsubscribeAllUsers update prepare ' . $dbObj->error;
				}

				$dbObj->close();
			}
		}

		if ($result == "")
		{
			// send email notification and log an activity log entry for each user
			foreach ($pUserDataArray as $userID => $userInfo)
			{
				TaskObj::sendTemplateBulkEmail('customer_marketingoptoutbrandusers', $pBrandCode, $pEventCreationParams['brandapplicationname'],
						$pEventCreationParams['branddisplayurl'], '', $userInfo['contactname'], $userInfo['emailaddress'],
						'', '', $userID,
						$userInfo, '', ''
						);

				DatabaseObj::updateActivityLog($pEventCreationParams['eventcreationsessionref'], 0, $pEventCreationParams['eventcreationuserid'], $pEventCreationParams['eventcreationuserlogin'],
												$pEventCreationParams['eventcreationusername'], 0, 'ADMIN', 'UPDATEPREFERENCES-ALL', '0 ' . $userID, 1);
			}
		}

		$returnArrray['result'] = $result;
		$returnArrray['resultparam'] = $resultParam;

		return $returnArray;
    }
}

?>