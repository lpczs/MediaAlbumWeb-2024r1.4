<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

class bulkEmailTask
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
        $defaultSettings['code'] = 'TAOPIX_BULKEMAIL';
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
        require_once('../tasks/emailTask.php');

        $resultMessage = '';
        $sendingResult = array();

        // get list of events for the task
        $taskCode = self::register();
        $taskCode = $taskCode['code'];

		$bulkEmailResult = emailTask::run($pEventID, $taskCode);

        return $bulkEmailResult;
    }
}

?>