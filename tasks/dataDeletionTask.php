<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

class dataDeletionTask
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
		$defaultSettings['code'] = 'TAOPIX_DATADELETION';
		$defaultSettings['name'] = 'en Personal Data Deletion<p>it Personal Data Deletion<p>fr Personal Data Deletion<p>es Personal Data Deletion';

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
		$systemConfigArray = TaskObj::getSystemConfig();

		$resultMessage = '';

        try
        {
            $pEventID = (int) $pEventID[0];

            // get list of events for the task
            $taskCode = self::register();
            $taskCode = $taskCode['code'];

			// include data redaction
			require_once('../DataRedactionAPI/DataRedactionAPI_control.php');

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

                    TaskObj::writeLogEntry('Task: ' . $taskCode . '. Executing Event ' . ($i + 1) .  ' Of ' . $eventCount . ' (' . $event['id'] . ').');

                    try
                    {
                        if ($event['parentId'] > 0)
                        {
                            $parentEventList = TaskObj::getEventByID($event['parentId']);
                            $parentEventList = $parentEventList['events'];
                            $parentEvent = $parentEventList[0];
                            $event['param1'] = $parentEvent['param1'];
                            $event['param2'] = $parentEvent['param2'];
                            $event['param3'] = $parentEvent['param3'];
                            $event['param4'] = $parentEvent['param4'];
                            $event['param6'] = $parentEvent['param6'];
                            $event['param5'] = $parentEvent['param5'];
                            $event['param8'] = $parentEvent['param8'];
                            $event['param7'] = $parentEvent['param7'];
                            $event['webBrandCode'] = $parentEvent['webBrandCode'];
                        }

						// send the task command to the DataDeletionAPI
						$eventResult = DataRedactionAPI_control::startRedactionTask($event['param3'], $event['param4'], $event['param5'], $event['webBrandCode'], $systemConfigArray);

						if (($eventResult['result'] == '') || ($eventResult['result'] == 2))
						{
							// no error, set as complete
							$eventResult['result'] = 2;
						}
						else
						{
							if ($eventResult['result'] == '0')
							{
								$resultMessage = '';
								$eventResult['result'] = 0;
								$eventResult['resultparam'] = '';
							}
							else
							{
								$eventResult['result'] = 1;
							}
						}
                    }
                    catch (Exception $e)
                    {
                        $eventResult['result'] = 1;
                        $eventResult['resultparam'] = 'en ' . $e->getMessage();
                    }

					global $serverTimeOffset;

					$runTime = time() + $serverTimeOffset;

					DatabaseObj::updateEventLastRunTime($event['id'], date('Y-m-d H:i:s', $runTime), $eventResult['result'], $eventResult['resultparam']);

                }
            }
            else
            {
                //return error message to taskManager
                $resultMessage = $eventsList['resultparam'];
            }

			// check the status of the in progress stage of redaction if all passed, move to next stage
			$checkProgressResult = DataRedactionAPI_model::checkProgress();

			// check the status of the production stage of redaction if all passed
			DataRedactionAPI_model::checkProductionEventStatus();

        }
        catch (Exception $e)
        {
            $resultMessage = 'en ' . $e->getMessage();
        }

        return $resultMessage;
    }
}

?>