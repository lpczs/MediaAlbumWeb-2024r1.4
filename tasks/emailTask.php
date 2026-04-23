<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

class emailTask
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
        $defaultSettings['code'] = 'TAOPIX_EMAIL';
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
    static function run($pEventID, $pTaskCode = '')
    {
        global $ac_config;

        $resultMessage = '';
        $sendingResult = array();

        try
        {
            $pEventID = (int) $pEventID[0];
			$eventLimit = 200;
			$sleep = 0;

            // if $pTaskCode is not empty then we need to use the taskcode from the calling email task
            // rather than the one registered in this email task. This is so we can have other low priority emails
            // running using the same email task code. We also introduce a a config setting for how many events are retrived as well as a sleep.
            // This is to stop the bulk email from hitting smtp server send message limits.
            if ($pTaskCode != '')
            {
            	$taskCode = $pTaskCode;
            	$eventLimit = (int) $ac_config['BULKEMAILTASKBATCHSIZE'];
            	$sleep = (int) $ac_config['BULKEMAILTASKDELAY'];
			}
			else
			{
				// get list of events for the task
            	$taskCode = self::register();
            	$taskCode = $taskCode['code'];
			}

			TaskObj::writeLogEntry('Task: ' . $taskCode . '. Retrieving Events.');

            if ($pEventID > 0)
            {
                $eventsList = TaskObj::getEventByID($pEventID);
            }
            else
            {
                $eventsList = TaskObj::getEventsByTaskCode($taskCode, $eventLimit);
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

                        $emailObj = new TaopixMailer();

                        // brand email settings
                        $brandSettings = TaskObj::getBrandingFromCode($event['webBrandCode']);

                        if ($brandSettings['usedefaultemailsettings'] == 1)
                        {
                            // default email settings
                            $brandSettings = TaskObj::getBrandingFromCode('');
                        }

                        $serverDetails = array();
                        $serverDetails['smtpsystemfromname'] = $brandSettings['smtpsystemfromname'];
                        $serverDetails['smtpsystemreplytoaddress'] = $brandSettings['smtpsystemreplytoaddress'];
                        $serverDetails['smtpsystemreplytoname'] = $brandSettings['smtpsystemreplytoname'];
                        $serverDetails['smtpaddress'] = $brandSettings['smtpaddress'];
                        $serverDetails['smtpport'] = $brandSettings['smtpport'];
                        $serverDetails['smtpsystemfromaddress'] = $brandSettings['smtpsystemfromaddress'];
                        $serverDetails['smtpauth'] = $brandSettings['smtpauth'];
                        $serverDetails['smtpauthusername'] = $brandSettings['smtpauthusername'];
                        $serverDetails['smtpauthpassword'] = $brandSettings['smtpauthpassword'];
                        $serverDetails['smtptype'] = $brandSettings['smtptype'];
						$serverDetails['oauthprovider'] = $brandSettings['oauthprovider'];
						$serverDetails['oauthtoken'] = $brandSettings['oauthtoken'];

                        $sendingResult = $emailObj->taopixSendEmailContents($event['param1'], $event['param2'], $event['param3'], $event['param4'], $event['param6'],
                        												$event['param5'], $event['param8'], $event['param7'], $event['webBrandCode'], $serverDetails);

                        if ($sendingResult['result'] != 2)
                        {
                            $resultMessage = $sendingResult['resultParam'];
                        }
                    }
                    catch (Exception $e)
                    {
                        $resultMessage = 'en ' . $e->getMessage();
                        $sendingResult['result'] = 1;
                        $sendingResult['resultParam'] = $resultMessage;
                    }

                    TaskObj::updateEvent($event['id'], $sendingResult['result'], $sendingResult['resultParam']);

					// sleep command set for bulk emails.
					// this is to prevent bulk emails hitting smtp server send limits
                    if ($sleep != 0)
                    {
                    	sleep($sleep);
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
}

?>