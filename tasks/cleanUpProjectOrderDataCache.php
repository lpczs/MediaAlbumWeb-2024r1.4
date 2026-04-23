<?php
/*
 * This task is used to cleanup projectorderdatacachetable
 */


ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../libs/external/vendor/autoload.php');
require_once('../Utils/UtilsCoreIncludes.php');

set_time_limit(60);

$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
$gConstants = DatabaseObj::getConstants();

class cleanUpProjectOrderDataCache 
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
		$defaultSettings['code'] = 'TAOPIX_CLEANUPPROJECTORDERDATACACHE';
		$defaultSettings['name'] = 'it italian desciption<p>fr french description<p>es spanish text';

		/*
		* $defaultSettings('intervalType') defines inteval value
		* 1 - Number of minutes
		* 2 - Exact time of the day
		* 3 - Number of days
		*/

		$defaultSettings['intervalType'] = '1';
		$defaultSettings['intervalValue'] = '1';
		$defaultSettings['maxRunCount'] = '10';
		$defaultSettings['deleteCompletedDays'] = '5';

		return $defaultSettings;
	}

    static function run($pEventID, $pTaskCode = '')
    {
        global $ac_config;

		$resultMessage = '';

		try
		{
			$pEventID = (int)$pEventID[0];

			// get list of events for the task
			$taskCode = self::register();
			$taskCode = isset($taskCode['code']) ? $taskCode['code'] : 'TAOPIX_CLEANUPPROJECTORDERDATACACHE';

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

					$event = &$eventsList[$i];
					$eventRecordID = $event['id'];
					$projectRefArray = $event['param1'];

					TaskObj::writeLogEntry('Task: ' . $taskCode . '. Executing Event ' . ($i + 1) .  ' Of ' . $eventCount . ' (' . $eventRecordID . ').');

					try
       		 		{
						$projectRefArray = unserialize($projectRefArray);
						DatabaseObj::cleanUpProjectOrderDataCache($projectRefArray);

						TaskObj::updateEvent($eventRecordID, 2, '');
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

    function logDebug($pMessage)
    {
        $message = $pMessage;

        if (is_array($message))
        {
            $message = var_export($message, true);
        }
        TaskObj::writeLogEntry($message);
    }

}

?>