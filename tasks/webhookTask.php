<?php

use Taopix\Webhook\Webhook;

ini_set('display_errors', 1);
error_reporting(E_ALL);

class webHookTask
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
		$defaultSettings['code'] = 'TAOPIX_WEBHOOK';
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
		$resultMessage = '';
		$successfullWebhooks = [];

		try
		{
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
				
				$webhook = new Webhook('TAOPIX', '', []);
				
				for ($i = 0; $i < $eventCount; $i++)
				{
                    UtilsObj::resetPHPScriptTimeout(30);

					$event = &$eventsList[$i];
					$eventRecordID = $event['id'];
					$webhookID = (int) $event['param3'];
					$webhookURL = $event['param4'];

					TaskObj::writeLogEntry('Task: ' . $taskCode . '. Executing Event ' . ($i + 1) .  ' Of ' . $eventCount . ' (' . $eventRecordID . ').');

					try
       		 		{
						$webhook->loadWebhook($webhookID, $webhookURL);
						$webhookResult = $webhook->post();

						if ($webhookResult[0] == '200')
						{
							$successfullWebhooks[] = $webhookID;
							TaskObj::updateEvent($eventRecordID, 2, '');
						}
						else
						{
							TaskObj::writeLogEntry('Task: ' . $taskCode . '. Error In Event ' . ($i + 1) .  ' Of ' . $eventCount .
																					' (' . $eventRecordID . ') - status ' . $webhookResult[0]);

							TaskObj::updateEvent($eventRecordID, 1, 'status ' . $webhookResult[0]);
						}
					}
					catch(Exception $e)
					{
						$resultMessage = 'en ' . $e->getMessage();

						TaskObj::updateEvent($eventRecordID, 1, $resultMessage);
					}
				}
				
				if (count($successfullWebhooks) > 0)
				{
					$webhook->deleteSuccessfulWebhookRecords($successfullWebhooks);
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
}

?>