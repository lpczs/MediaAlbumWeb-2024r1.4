<?php
/*
 * This task is used to sync products with shopify
 */


ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../libs/external/vendor/autoload.php');
require_once('../Utils/UtilsCoreIncludes.php');

require '../libs/external/vendor/autoload.php';
use Taopix\Connector\Shopify\ShopifyConnector;
use Taopix\Connector\Shopify\Product;

set_time_limit(60);

$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
$gConstants = DatabaseObj::getConstants();

class connectorProcessSyncResults 
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
		$defaultSettings['code'] = 'TAOPIX_CONNECTORPROCESSSYNCRESULTS';
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

	static function createSyncTask($licenseKeyCode, $brandCode, $shopURL) 
	{
		DatabaseObj::createEvent('TAOPIX_CONNECTORPRODUCTSYNC', '', $licenseKeyCode, $brandCode, '', 0, 
		'', '', $shopURL, '', '', '', '', '', 
		0, 0, 0, '', '', 0);
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
			$taskCode = isset($taskCode['code']) ? $taskCode['code'] : 'TAOPIX_CONNECTORPROCESSSYNCRESULTS';

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
					$shopURL = $event['param3'];
					$bulkOpertionID = $event['param4'];
					$mode = $event['param5'];
					$brandCode = $event['webBrandCode'];
					$licenseKeyCode = $event['groupCode'];
					$resultsFile = '';
					$linkResult = array();

					TaskObj::writeLogEntry('Task: ' . $taskCode . '. Executing Event ' . ($i + 1) .  ' Of ' . $eventCount . ' (' . $eventRecordID . ').');

					try
       		 		{
                        $shopifyConnector = new ShopifyConnector($shopURL);
                        $graphQL = $shopifyConnector->initGraphQL();
                        $product = new Product($graphQL);       

						$filePath = $ac_config['CONNECTORRESOURCESPATH'];
						$filePath = UtilsObj::correctPath($filePath, DIRECTORY_SEPARATOR, true);
						if ($brandCode != '')
						{
							$filePath .= $brandCode . DIRECTORY_SEPARATOR;
						}
						$dateNow = date("YmdHis");
						$completedPath = $filePath . 'complete' . DIRECTORY_SEPARATOR;
						$failedPath = $filePath . 'failed' . DIRECTORY_SEPARATOR;
						$extension = '.jsonl';
						$newName = 'BulkUploadCreate' . $extension;
						$updateName = 'BulkUploadUpdate' . $extension;
						$updateFileFullPath = $filePath . $updateName;
						$newFileFullPath = $filePath . $newName;
						$failMessage = '';
                        
						$pollResults = $product->pollBulkOperationStatus();
						$currentOperation = get_object_vars($pollResults->currentBulkOperation);

						//if the operationID is correct and the process complete continue to insert link records
						if ($currentOperation['id'] == $bulkOpertionID && $currentOperation['status'] == 'COMPLETED')
						{
							$resultsFile = $product->storeBulkOperationResults($currentOperation, $brandCode);
						
							//if the results file has been successfully dowloaded then insert link records 
							if (file_exists($resultsFile))
							{
								$linkResult = $shopifyConnector->shopifyProductLink($bulkOpertionID,$mode,$brandCode);
							}
							else
							{
								$linkResult['result'] = 'failed';
								$linkResult['resultParam'] = 'File not found: ' . $resultsFile;
							}

							//if the link records were successfully inserted then cleanup the files and mark task as completed
							if ($linkResult['result'] === 'success')
							{
								$resultCompletedFilename = str_replace($filePath,"",$resultsFile); 
								UtilsObj::moveUploadedFile($resultsFile, $completedPath . $resultCompletedFilename);
								TaskObj::updateEvent($eventRecordID, 2, '');
								$moveFileFromFullPath = $updateFileFullPath;
								$moveFileToFullPath = $completedPath . str_replace($extension, $dateNow . $extension, $updateName);

								//if this was a task to insert new products check if theres an update file and insert a TAOPIX_CONNECTORPRODUCTSYNC task
								if ($mode == 'INSERT') 
								{
									if (file_exists($updateFileFullPath))
									{
										self::createSyncTask($licenseKeyCode, $brandCode, $shopURL);
									}
									$moveFileFromFullPath = $newFileFullPath;
									$moveFileToFullPath = $completedPath . str_replace($extension, $dateNow . $extension, $newName);
								}

								UtilsObj::moveUploadedFile($moveFileFromFullPath, $moveFileToFullPath);
								//delete any old files which are more than 14 days old
								UtilsObj::deleteOldFiles($completedPath, 20160);
							}
							else
							{
								//the link records were NOT successfully inserted so mark event as failed.
								TaskObj::updateEvent($eventRecordID, 1, 'en shopifyProductLink unsuccessful: ' . $linkResult['result'] . ' - ' . $linkResult['resultParam']);
								TaskObj::writeLogEntry('en shopifyProductLink unsuccessful: ' . $linkResult['result'] . ' - ' . $linkResult['resultParam']);
							}
						}
						else
						{
							//if status is RUNNING or CREATED & it has the expected ID then leave the task in queue to try again
							if (($currentOperation['status'] == 'RUNNING' || $currentOperation['status'] == 'CREATED') && ($currentOperation['id'] == $bulkOpertionID))
							{
								TaskObj::updateEvent($eventRecordID, 0, 'en Bulk Operation Status - ' . $currentOperation['status'] . ' - will retry ');
							}
							else 
							{
								//task has failed so set appropriate failiure message and cleanup files so another task can be ran
								if ($currentOperation['id'] != $bulkOpertionID)
								{
									$failMessage = 'en Expected Bulk Operation: ' . $bulkOpertionID . '- Actual Bulk Operation: ' . $currentOperation['id'] . '';
								}
								else
								{
									$failMessage = 'en Bulk Operation Failed in Shopify with status: ' . $currentOperation['status'] . ', Mode: ' . $mode;
								}

								TaskObj::updateEvent($eventRecordID, 1, $failMessage);
								TaskObj::writeLogEntry($failMessage);
								$failFileFullPath = ($mode == 'INSERT') ? $newFileFullPath : $updateFileFullPath;
								$failFileName = ($mode == 'INSERT') ? $newName : $updateName;
								UtilsObj::moveUploadedFile($failFileFullPath, $failedPath . str_replace($extension, $dateNow . $extension, $failFileName));

								//if this was a task to insert new products & theres an update file the create a new TAOPIX_CONNECTORPRODUCTSYNC task
								if (($mode == 'INSERT') && (file_exists($updateFileFullPath)))
								{
									self::createSyncTask($licenseKeyCode, $brandCode, $shopURL);
								}
							}
						}
					}
					catch(Exception $e)
					{
						$resultMessage = 'en ' . $e->getMessage();
						TaskObj::updateEvent($eventRecordID, 1, $resultMessage);
						TaskObj::writeLogEntry($resultMessage);
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