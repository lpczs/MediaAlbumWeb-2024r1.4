<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('TPX_INTERNALTASK_PURGEVERBOSELEVEL', 0);
define('TPX_INTERNALTASK_PURGELOGLEVEL', 3);
define('TPX_INTERNALTASK_PURGERECORDLIMIT', 1000);

define('TPX_CLEANUP_OPT_PROJECTS', 4);
define('TPX_CLEANUP_OPT_ASSETS', 5);
define('TPX_CLEANUP_OPT_ARCHIVES', 12);
define('TPX_ARCHIVE_MODE_ARCHIVE', 2);

class onlinePurgeTask
{
	/**
	 * Execute the task
	 *
	 * @param int $pEventID ID of the individual event
	 * @return string
	 */
    static function run($pEventID)
	{
		global $ac_config;

		$systemConfigArray = TaskObj::getSystemConfig();
		$resultMessage = '';

        try
        {
            $taskCode = self::register();
            $taskCode = $taskCode['code'];
			$comCounter = 0;

			TaskObj::writeLogEntry('Task: ' . $taskCode . '. Retrieving Assigned Data Policies.');

			UtilsObj::resetPHPScriptTimeout(30);

			// Read the brand information, check if any data retention policies have been applied.
			$brandPolicyDataResult = self::getActiveDataPolicies();

			if ($brandPolicyDataResult['result'] == '')
			{
				$brandDataPolicies = $brandPolicyDataResult['data'];

				// Make sure at least on policy is active.
				if (!empty($brandDataPolicies))
				{
					$commandList = [];
					$dateTime = new \DateTime('now', new \DateTimeZone('UTC'));
					$defaults = [
						'ownercode' => $systemConfigArray['ownercode'],
						'tenantid' => $systemConfigArray['tenantid'],
						'verbose' => TPX_INTERNALTASK_PURGEVERBOSELEVEL,
						'date' => $dateTime->format('Y-m-d H:i:s'),
						'datekey' => $dateTime->format('YmdHisu'),
						'batchsize' => TPX_INTERNALTASK_PURGERECORDLIMIT,
						'onlineurl' => $ac_config['TAOPIXONLINEURL'],
					];

					foreach ($brandDataPolicies as $key => $policy)
					{
						// Ensure each loop over the brand data/policy get unique keys for processing
						$dateTime = new \DateTime('now', new \DateTimeZone('UTC'));
						$defaults['datekey'] = $dateTime->format('YmdHisu');

						$licenseKeyList = TaskObj::getBrandLicenseKeyCodes($policy['code']);
						$licenseKeys = array_map(function($pValue) { return $pValue['id']; }, $licenseKeyList);

						/*
						 * Set the command configuration for the flagging of projects.
						 * Array will be formatted as follows, with keys related to each section.
						 *
						 * [
						 * 		'ordered' => [
						 * 			'age' => int Projects which have been dormant for this long are flagged for purge.
						 * 			'email' => int (0/1) 1 if we send purge emails to the customer for projects of this type.
						 * 			'days' => int Number of days after flagging that the project will be purged.
						 * 			'emailfrequency' => int Number of days between purge emails sent to the customer.
						 * 		]
						 * ]
						 */
						$commandConfig = [];
						$sections = ['ordered', 'notordered', 'unsaved', 'guest'];
						foreach($sections as $section){
							if ($policy[$section .'projects']) {
								$commandConfig[$section] = [
									'age' => $policy[$section .'age'],
									'email' => $section === 'guest' ? 0 : $policy[$section .'email'],
									'days' =>  $section === 'guest' ? 0 : $policy[$section .'days'],
									'emailfrequency' =>  $section === 'guest' ? 0 : $policy[$section .'emailfrequency'],
								];
							}
						}

						// Add the flag projects command.
						$commandList[] = array_merge($defaults, [
							'operation' => 'flag',
							'target' => 'projects',
							'type' => TPX_CLEANUP_OPT_PROJECTS,
							'licensekeys' => $licenseKeys,
							'config' => $commandConfig,
						]);

						/*
						 * Reset the command configuration for assets, add the default errored/failed uploads.
						 * These are not configurable by the licensee.
						 */
						$commandConfig = [
							'errored' => [
								'age' => 1,
							],
							'faileduploads' => [
								'age' => 1,
							]
						];

						// Only send the unused assets delete for ordered projects if it is active.
						if ($policy['orderedunusedassets'])
						{
							$commandConfig['orderedunusedassets'] = [
								'age' => $policy['orderedunusedassetage'],
							];
						}

						// Only send the unused assets delete for unordered projects if it is active.
						if ($policy['notorderedunusedassets'])
						{
							$commandConfig['notorderedunusedassets'] = [
								'age' => $policy['notorderedunusedassetage'],
							];
						}

						// Add the flag assets command.
						$commandList[] = array_merge($defaults, [
							'operation' => 'flag',
							'target' => 'assets',
							'type' => TPX_CLEANUP_OPT_ASSETS,
							'licensekeys' => $licenseKeys,
							'config' => $commandConfig,
						]);

						// Add the purge projects command.
						$commandList[] = array_merge($defaults, [
							'operation' => 'purge',
							'target' => 'projects',
							'type' => TPX_CLEANUP_OPT_PROJECTS,
							'licensekeys' => $licenseKeys,
						]);

						// Add the purge assets command.
						$commandList[] = array_merge($defaults, [
							'operation' => 'purge',
							'target' => 'assets',
							'type' => TPX_CLEANUP_OPT_ASSETS,
						]);

						$commandErrors = [];

						foreach ($commandList as $commandToQueue)
						{
							// Send the command to the online server, and add to queue.
							TaskObj::writeLogEntry('Task: ' . $taskCode . '. Queuing Task ' . $commandToQueue['operation'] . '-' . $commandToQueue['target'] . '.');

							$dataToEncrypt = array('directive' => $commandToQueue, 'type' => 'purge');
							$queueResult = CurlObj::sendByPost($ac_config['TAOPIXONLINEURL'], 'DataRetentionAPI.queueDataRetentionJob', $dataToEncrypt);

							if ('' !== $queueResult['error'])
							{
								$commandErrors[] = 'Purge Task Queue Error: ' . $queueResult['error'];
							}
						}

						if (! empty($commandErrors))
						{
							$resultMessage = 'en ' . \implode(' - ', $commandErrors);
						}
					}
				}
			}
        }
        catch (Exception $e)
        {
            $resultMessage = 'en ' . $e->getMessage();
        }

        return $resultMessage;
    }

	/**
	 * Define default settings for this task
	 *
	 * @return array
	 */
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
        $defaultSettings['code'] = 'TAOPIX_ONLINEPURGETASK';
        $defaultSettings['name'] = 'it spurgo italiano compito desciption<p>fr purge français description de la tâche<p>es descripción de la tarea de purga español';

        /*
         * $defaultSettings('intervalType') defines inteval value
         * 1 - Number of minutes
         * 2 - Exact time of the day
         * 3 - Number of days
         */

        $defaultSettings['intervalType'] = '2';
        $defaultSettings['intervalValue'] = '01:00';
        $defaultSettings['maxRunCount'] = '10';
        $defaultSettings['deleteCompletedDays'] = '5';

        return $defaultSettings;
    }


	/**
	 * Get a list of data policies used by the brands
	 *
	 * @return array
	 */
	static function getActiveDataPolicies()
	{
		$dbObj = TaskObj::getGlobalDBConnection();

		$resultArray = array('result' => '', 'resultparam' => '', 'data' => array(), 'policies' => array());
		$id = 0;
		$code = '';
		$applicationname = '';
		$guestprojects = 0;
		$guestage = 0;
		$unsavedprojects = 0;
		$unsavedage = 0;
		$unsaveddays = 0;
		$unsavedemail = 0;
		$unsavedemailfrequency = 0;
		$notorderedprojects = 0;
		$notorderedage = 0;
		$notordereddays = 0;
		$notorderedemail = 0;
		$notorderedemailfrequency = 0;
		$orderedprojects = 0;
		$orderedage = 0;
		$ordereddays = 0;
		$orderedemail = 0;
		$orderedemailfrequency = 0;
		$orderedunusedassets = 0;
		$orderedunusedassetage = 0;
		$notorderedunusedassets = 0;
		$notorderedunusedassetage = 0;

		if ($dbObj)
		{
			$sql = 'SELECT `b`.`id`, `b`.`code`, `b`.`applicationname`, `dp`.`guestprojects`, `dp`.`guestage`,
						`dp`.`unsavedprojects`, `dp`.`unsavedage`, `dp`.`unsaveddays`, `dp`.`unsavedemail`, `dp`.`unsavedemailfrequency`,
						`dp`.`notorderedprojects`, `dp`.`notorderedage`, `dp`.`notordereddays`, `dp`.`notorderedemail`, `dp`.`notorderedemailfrequency`,
						`dp`.`orderedprojects`, `dp`.`orderedage`, `dp`.`ordereddays`, `dp`.`orderedemail`, `dp`.`orderedemailfrequency`,
						`dp`.`orderedunusedassets`, `dp`.`orderedunusedassetsage`, `dp`.`notorderedunusedassets`, `dp`.`notorderedunusedassetsage`
					FROM `BRANDING` `b`
					INNER JOIN `DATAPOLICIES` `dp` ON `b`.`onlinedataretentionpolicy` = `dp`.`id`
 					WHERE (`b`.`onlinedataretentionpolicy` > 0) 
						AND (`b`.`active` = 1)
						AND (`dp`.`active` = 1)';
			$stmt = $dbObj->prepare($sql);

			if ($stmt)
			{
				if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						if ($stmt->num_rows > 0)
						{
							if ($stmt->bind_result($id, $code, $applicationname, $guestprojects, $guestage, $unsavedprojects, $unsavedage,
								$unsaveddays, $unsavedemail, $unsavedemailfrequency, $notorderedprojects, $notorderedage, $notordereddays,
								$notorderedemail, $notorderedemailfrequency, $orderedprojects, $orderedage, $ordereddays, $orderedemail,
								$orderedemailfrequency, $orderedunusedassets, $orderedunusedassetage, $notorderedunusedassets, $notorderedunusedassetage))
							{
								while ($stmt->fetch())
								{
									$tempArray = array();
									$tempArray['id'] = $id;
									$tempArray['code'] = $code;
									$tempArray['applicationname'] = $applicationname;
									$tempArray['guestprojects'] = $guestprojects;
									$tempArray['guestage'] = $guestage;
									$tempArray['unsavedprojects'] = $unsavedprojects;
									$tempArray['unsavedage'] = $unsavedemail ? $unsavedage : $unsavedage - 1;	// If we are not sending emails adjust the age by 1.
									$tempArray['unsaveddays'] = $unsavedemail ? $unsaveddays : 1;	// If we are not sending emails set the days till purge to be 1.
									$tempArray['unsavedemail'] = $unsavedemail;
									$tempArray['unsavedemailfrequency'] = $unsavedemail ? $unsavedemailfrequency : 0; // We are not sending emails so there is no email frequency
									$tempArray['notorderedprojects'] = $notorderedprojects;
									$tempArray['notorderedage'] = $notorderedemail ? $notorderedage : $notorderedage - 1;
									$tempArray['notordereddays'] = $notorderedemail ? $notordereddays : 1;
									$tempArray['notorderedemail'] = $notorderedemail;
									$tempArray['notorderedemailfrequency'] = $notorderedemail ? $notorderedemailfrequency : 0;
									$tempArray['orderedprojects'] = $orderedprojects;
									$tempArray['orderedage'] = $orderedemail ? $orderedage : $orderedage - 1;
									$tempArray['ordereddays'] = $orderedemail ? $ordereddays : 1;
									$tempArray['orderedemail'] = $orderedemail;
									$tempArray['orderedemailfrequency'] = $orderedemail ? $orderedemailfrequency : 0;
									$tempArray['orderedunusedassets'] = $orderedunusedassets;
									$tempArray['orderedunusedassetage'] = $orderedunusedassetage;
									$tempArray['notorderedunusedassets'] = $notorderedunusedassets;
									$tempArray['notorderedunusedassetage'] = $notorderedunusedassetage;
									$resultArray['data'][] = $tempArray;
								}
							}
							else
							{
								$resultArray['result'] = 'str_DatabaseError';
								$resultArray['resultparam'] = __FUNCTION__ . ' - bind_result: error (' . $dbObj->error . ')';
							}
						}
					}
					else
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = __FUNCTION__ . ' - store_result: error (' . $dbObj->error . ')';
					}
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = __FUNCTION__ . ' - execute: error (' . $dbObj->error . ')';
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$resultArray['result'] = 'str_DatabaseError';
				$resultArray['resultparam'] = __FUNCTION__ . ' - prepare: error (' . $dbObj->error . ')';
			}
		}

        return $resultArray;
	}
}

?>