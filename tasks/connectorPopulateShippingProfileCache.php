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

set_time_limit(60);

$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
$gConstants = DatabaseObj::getConstants();
class connectorPopulateShippingProfileCache 
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
		$defaultSettings['code'] = 'TAOPIX_CONNECTORSHIPPINGPROFILECACHE';
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
			$taskCode = isset($taskCode['code']) ? $taskCode['code'] : 'TAOPIX_CONNECTORSHIPPINGPROFILECACHE';

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
					$brandCode = $event['webBrandCode'];
					$shopURL = $event['param3'];
                    $error = false;
                    $errorDetail = ''; 

					TaskObj::writeLogEntry('Task: ' . $taskCode . '. Executing Event ' . ($i + 1) .  ' Of ' . $eventCount . ' (' . $eventRecordID . ').');

					try
       		 		{
                        $shopifyConnector = new ShopifyConnector($shopURL);
                        $bulkResult = $shopifyConnector->submitBulkQuery($shopifyConnector->shippingProfilesQuery);

						if (isset($bulkResult->bulkOperationRunQuery)) 
                        {
                            if (isset($bulkResult->bulkOperationRunQuery->userErrors))
                            {
                                if (count($bulkResult->bulkOperationRunQuery->userErrors) > 0)
                                {
                                    $error = true;
                                    $errorDetail = 'en ' . $bulkResult->bulkOperationRunQuery->userErrors[0]->message;
                                }
                            }
                        }

						$filePath = $ac_config['CONNECTORRESOURCESPATH'];
						$filePath = UtilsObj::correctPath($filePath, DIRECTORY_SEPARATOR, true);

						if ($brandCode != '')
						{
							$filePath .= $brandCode . DIRECTORY_SEPARATOR; 
						}

						$filePath .= 'deliveryProfiles' . DIRECTORY_SEPARATOR . 'complete' . DIRECTORY_SEPARATOR;

						//delete any old files which are more than 14 days old
						UtilsObj::deleteOldFiles($filePath, 20160);

						if (!$error)
						{
							TaskObj::writeLogEntry('Bulk Operation ID: ' . $bulkResult->bulkOperationRunQuery->bulkOperation->id);							
							TaskObj::updateEvent($eventRecordID, 2, '');
						} 
						else
						{
							TaskObj::updateEvent($eventRecordID, 1, $errorDetail);
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