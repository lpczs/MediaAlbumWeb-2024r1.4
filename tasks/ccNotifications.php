<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

class ccNotifications
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
		$defaultSettings['code'] = 'TAOPIX_CCNOTIFICATION';
		$defaultSettings['name'] = '';

	   /*
		* $defaultSettings('intervalType') defines inteval value
		* 1 - Number of minutes
		* 2 - Exact time of the day
		* 3 - Number of days
		*/
		$defaultSettings['intervalType']  = '1';
		$defaultSettings['intervalValue'] = '5';
		$defaultSettings['maxRunCount']  = '10';
		$defaultSettings['deleteCompletedDays'] = '5';

		return $defaultSettings;
	}

	// function to run this task
	static function run($pEventID)
	{
		global $ac_config;

		$resultArray = Array();
		$resultMessage = '';
		$error = false;

		try
		{
			$systemConfigArray = TaskObj::getSystemConfig();

			$pEventID = (int)$pEventID[0];

			// get list of events for the task
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
                    UtilsObj::resetPHPScriptTimeout(30);

					$postData = array();
					$postArray = array();
					$salesOrderDataArray = array();
					$salesOrderData = '';

					$event = &$eventsList[$i];
					$eventRecordID = $event['id'];
					$notificationData = unserialize($event['param2']);

					TaskObj::writeLogEntry('Task: ' . $taskCode . '. Executing Event ' . ($i + 1) .  ' Of ' . $eventCount . ' (' . $eventRecordID . ').');

					try
       		 		{
						self::includeLowLevelOnlineBasketAPIScript();
			
						$createMethodExists = method_exists('OnlineBasketAPI', 'projectNotifications');
						
						if ($createMethodExists)
						{
							$projectNotificationResult = OnlineBasketAPI::projectNotifications($notificationData);
							
							if ($projectNotificationResult['result'] == '')
							{
								TaskObj::updateEvent($eventRecordID, 2, '');
							}
							else
							{
								TaskObj::writeLogEntry('Task: ' . $taskCode . '. Error In Event ' . ($i + 1) .  ' Of ' . $eventCount .
																						' (' . $eventRecordID . ') - ' . $projectNotificationResult['result']);

								TaskObj::updateEvent($eventRecordID, 1, $projectNotificationResult['result']);
							}
						}
						else
						{
							TaskObj::writeLogEntry('Task: ' . $taskCode . '. Error In Event ' . ($i + 1) .  ' Of ' . $eventCount .
																						' (' . $eventRecordID . ') - projectNotification method does not exist');

							TaskObj::updateEvent($eventRecordID, 1, 'projectNotification method does not exist');
							break;
						}
					

					}
					catch(Exception $e)
					{
						$resultMessage = 'en ' . $e->getMessage();

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
		catch(Exception $e)
		{
			$resultMessage = 'en ' . $e->getMessage();
		}

		return $resultMessage;
	}
	
	static function includeLowLevelOnlineBasketAPIScript()
    {
		// include external shopping cart script.
		if (file_exists('../Customise/scripts/EDL_OnlineBasketAPI.php'))
		{
			require_once('../Customise/scripts/EDL_OnlineBasketAPI.php');
		}
       
    }
}

?>