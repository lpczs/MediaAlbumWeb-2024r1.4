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
class connectorProductSync 
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
		$defaultSettings['code'] = 'TAOPIX_CONNECTORPRODUCTSYNC';
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
			$taskCode = isset($taskCode['code']) ? $taskCode['code'] : 'TAOPIX_CONNECTORPRODUCTSYNC';

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
					$brandCode = $event['webBrandCode'];
					$licenseKeyCode = $event['groupCode'];

					TaskObj::writeLogEntry('Task: ' . $taskCode . '. Executing Event ' . ($i + 1) .  ' Of ' . $eventCount . ' (' . $eventRecordID . ').');

					try
       		 		{

						$bulkOperationId = '';
                        $shopifyConnector = new ShopifyConnector($shopURL);
                        $graphQL = $shopifyConnector->initGraphQL('2022-10');
                        $product = new Product($graphQL);       

						// Read JSONL file 
						$filePath = $ac_config['CONNECTORRESOURCESPATH'];
						$filePath = UtilsObj::correctPath($filePath, DIRECTORY_SEPARATOR, true);
						if ($brandCode != '')
						{
							$filePath .= $brandCode . DIRECTORY_SEPARATOR;
						}
						$completedPath = $filePath . 'complete' . DIRECTORY_SEPARATOR;
						$extension = '.jsonl';

						$newName = 'BulkUploadCreate' . $extension;
						$updateName = 'BulkUploadUpdate' . $extension;

					    $newFilename = $filePath . $newName;
						$updateFilename = $filePath . $updateName;
						
						$dateNow = date("YmdHis");

						//always process new first
						if (file_exists($newFilename))
						{
							$bulkFilename = $newFilename;
							$mode = 'INSERT';
							$completedPath .= str_replace($extension, $dateNow . $extension, $newName);
						} 
						elseif (file_exists($updateFilename))
						{
							$bulkFilename = $updateFilename;
							$mode = 'UPDATE';
							$completedPath .= str_replace($extension, $dateNow . $extension, $updateName);
						}

						TaskObj::writeLogEntry('Mode: ' . $mode . '. - Found ' . $bulkFilename);

						$bulkOperationId = $product->processBulkFile($bulkFilename,$mode);

						if ($bulkOperationId != '')
						{
							TaskObj::writeLogEntry('Bulk Operation ID: ' . $bulkOperationId);

							//create event for the next task to pick up
							$param1 = '';
							$param2 = '';
							$param3 = $shopURL;
							$param4 = $bulkOperationId;
							$param5 = $mode;

							DatabaseObj::createEvent('TAOPIX_CONNECTORPROCESSSYNCRESULTS', '', $licenseKeyCode, $brandCode, '', 0, 
								$param1, $param2, $param3, $param4, $param5, '', '', '', 
								0, 0, 0, '', '', 0);
							
							TaskObj::updateEvent($eventRecordID, 2, '');
						} 
						else
						{
							TaskObj::updateEvent($eventRecordID, 1, 'No bulk operation ID found');
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