<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('../Order/Order_model.php');

class onlineOrderCreation
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
		$defaultSettings['code'] = 'TAOPIX_ONLINEORDERCREATION';
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

		$resultMessage = '';

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

					$event = &$eventsList[$i];
					$eventRecordID = $event['id'];
					$batchref = 'OrderCreate_'. $eventRecordID;

					TaskObj::writeLogEntry('Task: ' . $taskCode . '. Executing Event ' . ($i + 1) .  ' Of ' . $eventCount . ' (' . $eventRecordID . ').');

					try
       		 		{
						// make sure the order creation event has a valid user id (greater than 0) i.e not a guest user
						if ($event['userid'] > 0)
						{
							$salesOrderDataArray = array();
							$salesOrderDataArray = self::getSalesOrderData($event['param4']);

							if ($salesOrderDataArray['error'] == '')
							{
								$postData = array();

								$postData[] = $event['param1']; // ownercode
								$postData[] = $event['param2']; // projectref
								$postData[] = $event['param3']; // uploadref
								$postData[] = $systemConfigArray['systemkey'];
								$postData[] = ' "' . $ac_config['FTPURL'] .
												'" "' . $ac_config['FTPUSER'] .
												'" "' . $ac_config['FTPPASS'] .
												'" "' . $ac_config['FTPORDERSROOTPATH'] .
												'" "' . $ac_config['FTPGROUPORDERSBYCODE'] . '"';

								$salesOrderData = $salesOrderDataArray['salesorderdata'];

								$postData[] = $salesOrderDataArray['ordernumber'];
								$postData[] = $salesOrderDataArray['groupcode'];
								$postData[] = $salesOrderDataArray['shoppingcarttype'];
								$postData[] = '"' . $salesOrderDataArray['webbrandcode'] . '"';

								$dataToEncrypt = array('batchref' => $batchref,
														'userid' => $event['userid'],
														'orderdata' => implode(chr(9), $postData),
														'salesorderdata' => $salesOrderData);

								$pushOrderUploadArray = TaskObj::sendToTaopixOnline('UPLOADORDER', $dataToEncrypt);
								/**
								 * We need to delete any order cache files that have been created. The cache files have
								 * been created to detect whether or not an order has been successfully placed and confirmed
								 * Currently this is being used for WeChat to detect whether we can redirect to the manual callback
								 */
								Order_model::deleteOnlineOrderStatusCacheFile($salesOrderDataArray['batchref']);
								if ($pushOrderUploadArray['error'] == '')
								{
									if ($pushOrderUploadArray['data']['error'] === 0)
									{
										TaskObj::updateEvent($eventRecordID, 2, '');
									}
									else
									{
										TaskObj::writeLogEntry('Task: ' . $taskCode . '. Error In Event ' . ($i + 1) .  ' Of ' . $eventCount .
																							' (' . $eventRecordID . ') - ' . $pushOrderUploadArray['data']['error']);

										TaskObj::updateEvent($eventRecordID, 1, $pushOrderUploadArray['data']['errorparam']);
									}
								}
								else
								{
									TaskObj::writeLogEntry('Task: ' . $taskCode . '. Error In Event ' . ($i + 1) .  ' Of ' . $eventCount .
																						' (' . $eventRecordID . ') - ' . $pushOrderUploadArray['error']);

									TaskObj::updateEvent($eventRecordID, 1, $pushOrderUploadArray['error']);
								}
							}
							else
							{
								TaskObj::writeLogEntry('Task: ' . $taskCode . '. Error In Event ' . ($i + 1) .  ' Of ' . $eventCount .
																					' (' . $eventRecordID . ') - ' . $salesOrderDataArray['errorparam']);

								TaskObj::updateEvent($eventRecordID, 1, $salesOrderDataArray['errorparam']);
							}
						}
						else
						{
							TaskObj::writeLogEntry('Task: ' . $taskCode . '. Error In Event ' . ($i + 1) .  ' Of ' . $eventCount .
																						' (' . $eventRecordID . ') - INVALID USERID');
							TaskObj::updateEvent($eventRecordID, 1, 'INVALID USERID');
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

	static function getSalesOrderData($pOrderHeaderID)
	{
		$dbObj = DatabaseObj::getGlobalDBConnection();

		$salesOrderDataString = '';
		$orderNumber = '';
		$groupCode = '';
		$shoppingCartType = TPX_SHOPPINGCARTTYPE_INTERNAL;
		$webBrandCode = '';
		$batchRef = '';

		$resultArray['error'] = '';
		$resultArray['errorparam'] = '';
		$resultArray['salesorderdata'] = '';
		$resultArray['batchref'] = '';

		if ($dbObj)
		{

			if ($stmt = $dbObj->prepare('SELECT CONCAT_WS("\t", `uploadbatchref`, `itemcount`, `sessionid`, `ordernumber`, `oh`.`datecreated`) as `salesorderdata`,
										`groupcode`,
										`shoppingcarttype`,
										`ordernumber`,
										`webbrandcode`,
										`uploadbatchref`
										FROM `ORDERHEADER` oh
										JOIN `ORDERITEMS` oi ON (oh.id = oi.orderid)
										JOIN `ORDERSHIPPING` os ON (oi.orderid = os.orderid)
										WHERE oh.id = ?'))
			{
				if ($stmt->bind_param('i', $pOrderHeaderID))
				{
                    if ($stmt->bind_result($salesOrderDataString, $groupCode, $shoppingCartType, $orderNumber, $webBrandCode, $batchRef))
                    {
                        if ($stmt->execute())
                        {
                            if ($stmt->fetch())
                            {
                                $resultArray['salesorderdata'] = $salesOrderDataString;
                                $resultArray['groupcode'] = $groupCode;
                                $resultArray['shoppingcarttype'] = $shoppingCartType;
                                $resultArray['ordernumber'] = $orderNumber;
								$resultArray['webbrandcode'] = $webBrandCode;
								$resultArray['batchref'] = $batchRef;
                            }
                            else
                            {
                                $resultArray['error'] = 'error';
                                $resultArray['errorparam'] = 'Error with getSalesOrderData fetch: ' . $dbObj->error . ' - ' . $dbObj->errno;
                            }
                        }
                        else
                        {
                            $resultArray['error'] = 'error';
                            $resultArray['errorparam'] = 'Error with getSalesOrderData execute: ' . $dbObj->error . ' - ' . $dbObj->errno;
                        }
                    }
                    else
                    {
                        $resultArray['error'] = 'error';
                        $resultArray['errorparam'] = 'Error with getSalesOrderData bind result: ' . $dbObj->error . ' - ' . $dbObj->errno;
                    }

				}
				else
				{
					$resultArray['error'] = 'error';
					$resultArray['errorparam'] = 'Error with  getSalesOrderData bind param: ' . $dbObj->error . ' - ' . $dbObj->errno;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$resultArray['error'] = 'error';
				$resultArray['errorparam'] = 'Error with getSalesOrderData prepare: ' . $dbObj->error . ' - ' . $dbObj->errno;
			}
		}
		else
		{
			$resultArray['error'] = 'error';
			$resultArray['errorparam'] = 'Error with getSalesOrderData prepare: Unable to connect to DB';
		}

        return $resultArray;
	}

}

?>