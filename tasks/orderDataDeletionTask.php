<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('TPX_DATA_CLEAN_UP_STATUS_PRODUCTION_STARTED', 0);
define('TPX_DATA_CLEAN_UP_STATUS_PRODUCTION_FINISHED', 1);
define('TPX_DATA_CLEAN_UP_STATUS_PRODUCTION_ERROR', 2);
define('TPX_DATA_CLEAN_UP_STATUS_FTP_STARTED', 3);
define('TPX_DATA_CLEAN_UP_STATUS_FTP_FINISHED', 4);
define('TPX_DATA_CLEAN_UP_STATUS_FTP_ERROR', 5);
define('TPX_DATA_CLEAN_UP_STATUS_DATA_FINISHED', 6);

class orderDataDeletionTask
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
		$defaultSettings['code'] = 'TAOPIX_ORDERDATADELETION';
		$defaultSettings['name'] = 'en Order Data Deletion<p>it Order Data Deletion<p>fr Order Data Deletion<p>es Order Data Deletion';

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

		$systemConfigArray = TaskObj::getSystemConfig();

		$resultMessage = '';

        logDebug("Starting Order Data Deletion task");

        try
        {
            // get list of events for the task
            $taskCode = self::register();
            $taskCode = $taskCode['code'];

			TaskObj::writeLogEntry('Task: ' . $taskCode . '. Retrieving Events.');

            UtilsObj::resetPHPScriptTimeout(30);

            $dbObj = DatabaseObj::getGlobalDBConnection();

            $brandToRedactArray = array();

            $webBrandCode = "";
            $orderRedactionDays = 0;
            $error = "";

            // get all the brands which have been configured
            if ($dbObj)
            {
                // get a list of all projects linked to the specified user, still in progress
                $sql = "SELECT
                            b.`code`,
                            b.`orderredactiondays`
                        FROM 
                            branding as b
                        WHERE
                            b.orderredactionmode = 1
                        AND 
                            b.active = 1";

                if ($stmt = $dbObj->prepare($sql))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($webBrandCode, $orderRedactionDays))
                                {
                                    while ($stmt->fetch())
                                    {
                                        $brandToRedactArray[] = array('webbrandcode' => $webBrandCode, 'orderredactiondays' => $orderRedactionDays);
                                    }
                                }
                                else
                                {
                                    $error = __FUNCTION__ . ' bind_result ' . $dbObj->error;
                                }
                            }
                        }
                        else
                        {
                            $error = __FUNCTION__ . ' store_result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = __FUNCTION__ . ' execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = __FUNCTION__ . ' prepare ' . $dbObj->error . " " . $sql;
                }
            }
            else
            {
                $error = __FUNCTION__ . ' connect ' . $dbObj->error;
            }

            if ($error == '')
            {
                logDebug("Brands with order deletion switched on: ");
                logDebug("\t" . implode(',', array_map(function($pItem) 
                                                        {
                                                            return ($pItem['webbrandcode'] === '' ? "Default" : $pItem['webbrandcode']) . " after " . $pItem['orderredactiondays'] . " days.";
                                                        }, $brandToRedactArray)));

                // loop around every brand and redact the orders which fall into the criteria
                foreach ($brandToRedactArray as $brandDetails)
                {
                    logDebug("******************************");
                    logDebug("START - Brand: " . ($brandDetails['webbrandcode'] === '' ? "Default" : $brandDetails['webbrandcode']));

                    try
                    {
                        $orderDataDeletion = new OrderDataDeletion($ac_config, new OrderDataDeletionTaskDB($dbObj), $brandDetails['webbrandcode'], $brandDetails['orderredactiondays']);

                        $orderDataDeletion->run();
                    }
                    catch (Exception $e)
                    {
                        $resultMessage = 'en ' . $e->getMessage();
                    }
                }
            }
            else
            {
                $resultMessage = 'en ' . $error;
            }
        }
        catch (Exception $e)
        {
            $resultMessage = 'en ' . $e->getMessage();
        }

        logDebug("Finished: " . (($resultMessage == "") ? "No errors" : $resultMessage));
        logDebug("========================================================");

        return $resultMessage;
    }
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


class OrderDataDeletion
{
    private $webBrandCode;
    private $orderRedactionDays;
    private $orderDataDeletionDB;

    function __construct($pConfig, $pOrderDataDeletionDB, $pWebBrandCode, $pOrderRedactionDays)
    {
        $this->config = $pConfig;
        $this->orderDataDeletionDB = $pOrderDataDeletionDB;
        $this->webBrandCode = $pWebBrandCode;
        $this->orderRedactionDays = $pOrderRedactionDays;
    }

    function run()
    {
        // get all the orders which meeting the deletion criteria and flag them for purge
        $this->flagOrderForDeletion();

        $ordersToBeDeleted = array(); 

        /**
         * There is a hidden config that you are able specfic the mount of orders that will be 
         * flagged for deletion this is to avoid timeouts when running the script. This function displays
         * how many will be flagged for deletion
         */
        $limit = isset($this->config['ORDERFLAGLIMIT']) ? $this->config['ORDERFLAGLIMIT'] : 1000;

        if (!$this->orderDataDeletionDB->hasError())        
        {

            // get all the orders from the database which need to be deleted.
            $ordersToBeDeleted = $this->orderDataDeletionDB->getOrderItemsWhichNeedDeleting($limit, $this->webBrandCode);
        }

        if (!$this->orderDataDeletionDB->hasError())
        {
            $this->createProductionEvent($ordersToBeDeleted);
        }

        if (!$this->orderDataDeletionDB->hasError())
        {
            logDebug("Getting orders which need clean up status updating");
            $this->updateProductionCleanUpStatus();
        }

        /**
         *  Limit the amount of orders that can be deleted on the ftp when testing
         * this seemed to be the biggest bottleneck likely to cause a timeout
         * so we set the limit to be low.
         */
        
         $ftpLimit = isset($this->config['FTPDELETELIMIT']) ? $this->config['FTPDELETELIMIT'] : 250;

        // get the orders which need FTP files cleaning up
        $ftpOrderArray = $this->orderDataDeletionDB->getFTPStartedOrders($ftpLimit, $this->webBrandCode);

        $successfullFTPOrders = array();

        // redact the record for each of the orders which have been clean up from FTP
        if (!$this->orderDataDeletionDB->hasError())
        {
            $successfullFTPOrders = $this->redactFTPData($ftpOrderArray);
        }

        // redact the record for each of the orders which have been clean up from FTP
        if (!$this->orderDataDeletionDB->hasError())
        {
            $this->redactDBAndLocalData($successfullFTPOrders);
        }

        // if there has been an error log it to file
        if ($this->orderDataDeletionDB->hasError())
        {
            throw new Exception($this->orderDataDeletionDB->getError());
        }
    }

    function flagOrderForDeletion()
    {
        $ordersToBePurged = array();
        $ordersWithActiveLinkedItems = array();

        // check the accounts do not have orders in production
        $serverDateTime = new \DateTime(DatabaseObj::getServerTime($this->orderRedactionDays * 24 * 60));

        /**
         * Limit the amount of orders that can be flagged for deletion. This is to avoid any timeouts
         * in the script. flagging the orders for deletion isn't itself slow so the limit doesn't
         * need to be that low
         */
        
        $limit = isset($this->config['ORDERFLAGLIMIT']) ? $this->config['ORDERFLAGLIMIT'] : 1000;

        $getOrderItemsArray = $this->orderDataDeletionDB->getOrderItems($serverDateTime, $this->webBrandCode, $this->orderRedactionDays, $limit);

        if (!$this->orderDataDeletionDB->hasError())
        {
            $ordersToBeDeleted =  $getOrderItemsArray['ordertobedeleted'];

            $ordersWithActiveLinkedItems =  $getOrderItemsArray['orderswithactivelinkeditems'];

            if (count($ordersWithActiveLinkedItems) > 0)
            {
                logDebug("Orders with linked items. Will be deleted later:");
                logDebug(implode(',', $ordersWithActiveLinkedItems));

                $this->orderDataDeletionDB->updateNextCheckDate($ordersWithActiveLinkedItems);
            }
        }

        if (!$this->orderDataDeletionDB->hasError())
        {
            if (count($ordersToBeDeleted) > 0)
            {
                logDebug("Orders to be flagged as needing to be deleted:");
                logDebug(implode(',', $ordersToBeDeleted));

                // update the purge days so that they can be pruged in the future.
                // currently this is turned off since there is no grace period given since we don't know what to adivse the user to do in this time
                $this->orderDataDeletionDB->updatePurgeDays(0, $ordersToBeDeleted);
            }
            else
            {
                logDebug("No Orders to be flagged for deletion");
            }
        }
    }

    function createProductionEvent($pOrdersToBeDeleted)
    {
        if (count($pOrdersToBeDeleted) > 0)
        {
            $orderIDList = array_map(function($pItem) { return $pItem['orderitemid']; }, $pOrdersToBeDeleted);

            logDebug("Orders to be deleted now:");
            logDebug(implode(',', $orderIDList));

            // add an event in the project events table to delete the production data for the order
            $this->orderDataDeletionDB->addProductionEvents($orderIDList);

            if (!$this->orderDataDeletionDB->hasError())
            {
                $this->orderDataDeletionDB->updateOrderItemStatus($orderIDList, TPX_DATA_CLEAN_UP_STATUS_PRODUCTION_STARTED, "");
            }
        }
    }

    function updateProductionCleanUpStatus()
    {
        $productionStatusArray = $this->orderDataDeletionDB->getProductionStatus($this->webBrandCode);

        if (!$this->orderDataDeletionDB->hasError())
        {
            if (count($productionStatusArray['success']) > 0)
            {
                $orderIDList = array_map(function($pItem) { return $pItem['orderitemid']; }, $productionStatusArray['success']);

                logDebug("Orders which have been cleaned up in production:");
                logDebug(implode(',', $orderIDList));

                // add an activity log for each successfull clean up
                foreach ($productionStatusArray['success'] as $orderToBeDeleted)
                {
                    DatabaseObj::updateActivityLog2($this->orderDataDeletionDB->getConnection(), 0, $orderToBeDeleted['orderid'], $orderToBeDeleted['userid'], "", "", 1, 
                                                        'ORDERDATA', 'PRODUCTION-CLEANUP', $orderToBeDeleted['orderitemid'], 1);
                }

                $this->orderDataDeletionDB->updateOrderItemStatus($orderIDList, TPX_DATA_CLEAN_UP_STATUS_PRODUCTION_FINISHED);
            }

            if (count($productionStatusArray['failed']) > 0)
            {
                $orderIDList = array_map(function($pItem) { return $pItem['orderitemid']; }, $productionStatusArray['failed']);

                logDebug("Orders which have failed to be cleaned up in production:");
                logDebug(implode(',', $orderIDList));

                // add an activity log for each unsuccessfull clean up
                foreach ($productionStatusArray['success'] as $orderToBeDeleted)
                {
                    DatabaseObj::updateActivityLog2($this->orderDataDeletionDB->getConnection(), 0, $orderToBeDeleted['orderid'], $orderToBeDeleted['userid'], "", "", 1, 
                                                        'ORDERDATA', 'PRODUCTION-CLEANUP', $orderToBeDeleted['orderitemid'], 0);
                }

                $this->orderDataDeletionDB->updateOrderItemStatus($orderIDList, TPX_DATA_CLEAN_UP_STATUS_PRODUCTION_ERROR, "Error cleaning up production files");
            }
        }
    }

    function redactFTPData($pOrders)
    {
        $successfullFTPOrders = array();

        if (count($pOrders) > 0)
        {
            logDebug("Orders which are ready for FTP clean up:");


            // filter out any reorders
            $ftpOrdersToBeDeletedArray = array_filter($pOrders, function($pItem)
                                                    {
                                                        return $pItem['orderitemid'] == $pItem['uploadorderitemid'];
                                                    });

            $reordersToBeDeletedArray = array_filter($pOrders, function($pItem)
                                                    {
                                                        return $pItem['orderitemid'] != $pItem['uploadorderitemid'];
                                                    });

            logDebug(implode(',', array_map(function($pItem) {return $pItem['orderitemid'] . ":" . $pItem['uploadordernumber'] . ":" . $pItem['uploadgroupcode'] . ":" . $pItem['uploadref'];}, $ftpOrdersToBeDeletedArray)));

            $ftpResults = $this->ftpRedaction($ftpOrdersToBeDeletedArray);

            $successfullFTPOrders = array();

            foreach ($ftpResults['success'] as $ftpSuccessOrder)
            {
                $successfullFTPOrders[] = $ftpSuccessOrder;

                foreach ($reordersToBeDeletedArray as $reorderToBeDeleted)
                {
                    if ($ftpSuccessOrder['orderitemid'] == $reorderToBeDeleted['uploadorderitemid'])
                    {
                        $successfullFTPOrders[] = $reorderToBeDeleted;
                    }
                }
            }

            if (count($successfullFTPOrders) > 0)
            {
                $orderItemIDs = array_map(function($order){ return $order['orderitemid'];}, $successfullFTPOrders);

                logDebug("Orders which have had their FTP files cleaned up:");
                logDebug(implode(',', $orderItemIDs));

                $this->orderDataDeletionDB->updateOrderItemStatus($orderItemIDs, TPX_DATA_CLEAN_UP_STATUS_FTP_FINISHED, "");
            }

            $failedFTPOrders = array();

            foreach ($ftpResults['failed'] as $ftpSuccessOrder)
            {
                $failedFTPOrders[] = $ftpSuccessOrder;

                foreach ($reordersToBeDeletedArray as $reorderToBeDeleted)
                {
                    if ($ftpSuccessOrder['orderitemid'] == $reorderToBeDeleted['uploadorderitemid'])
                    {
                        $failedFTPOrders[] = $reorderToBeDeleted;
                    }
                }
            }

            if (count($failedFTPOrders) > 0)
            {
                $orderItemIDs = array_map(function($order){ return $order['orderitemid'];}, $failedFTPOrders);

                logDebug("Orders which failed FTP clean up:");
                logDebug($orderItemIDs);

                $this->orderDataDeletionDB->updateOrderItemStatus($orderItemIDs, TPX_DATA_CLEAN_UP_STATUS_FTP_ERROR, "Unable to delete FTP files");
            }
        }
        else
        {
            logDebug("No Orders ready for FTP Clean up");
        }

        return $successfullFTPOrders;
    }

    function redactDBAndLocalData($pOrders)
    {
        if (count($pOrders) > 0)
        {
            $orderItemIDsWhichNeedUpdating = [];
            $orderIDs = [];
            foreach ($pOrders as $order)
            {
                logDebug("Redacting Order Item Data for: " . $order['orderitemid']);

                // redact the order item data
                $this->orderDataDeletionDB->redactControlCentreOrderItemData($order['orderitemid']);

                $deleteFilesOK = true;

                if (!$this->orderDataDeletionDB->hasError())
                {
                    // delete the thumbnails files
                    $localFileRedactionArray = $this->localFileRedaction($order['uploadref']);

                    if (count($localFileRedactionArray['deleted']) > 0)
                    {
                        logDebug("UploadRef: " . $order['uploadref']);
                        logDebug("Local files deleted: " . implode(",", $localFileRedactionArray['deleted']));
                        $this->orderDataDeletionDB->deleteThumbnailRecords($order['uploadref']);

                        DatabaseObj::updateActivityLog2($this->orderDataDeletionDB->getConnection(), 0, $order['orderid'], $order['userid'], "", "", 1, 
                                                            'ORDERDATA', 'THUMBNAILS-CLEANUP', $order['orderitemid'], 1);

                    }

                    if (count($localFileRedactionArray['failed']) > 0)
                    {
                        $deleteFilesOK = false;
                        logDebug("UploadRef: " . $order['uploadref']);
                        logDebug("Local files not deleted: " . implode(",", $localFileRedactionArray['failed']));

                        DatabaseObj::updateActivityLog2($this->orderDataDeletionDB->getConnection(), 0, $order['orderid'], $order['userid'], "", "", 1, 
                                                            'ORDERDATA', 'THUMBNAILS-CLEANUP', $order['orderitemid'], 0);
                    }

                }

                // only redact the uplaods if the local files have been deleted
                if ($deleteFilesOK)
                {
                    if (!$this->orderDataDeletionDB->hasError())
                    {
                        logDebug("Stopping reorders for: " . $order['orderitemid']);
                        $this->orderDataDeletionDB->updateCanReorder($order['orderitemid']);
                    }

                    if (!$this->orderDataDeletionDB->hasError())
                    {
                        logDebug("Updating order item as finished for: " . $order['orderitemid']);
                        // flag the athe data clean up has finished. this will log the current timestamp in the dbdata column
                        $this->orderDataDeletionDB->updateOrderItemStatus($order['orderitemid'], TPX_DATA_CLEAN_UP_STATUS_DATA_FINISHED, "");
                    }

                    if (!$this->orderDataDeletionDB->hasError())
                    {
                        logDebug("Is " . $order['orderitemid'] . " the last order item record?");
                        // figure out if all the order items records have been redacted
                        $orderHeaderID = $this->orderDataDeletionDB->allItemsRedacted($order['orderitemid']);
                    }

                    if (!$this->orderDataDeletionDB->hasError())
                    {
                        // only redact the header record if the order header id has been returned
                        // this means that the order item was the last one for the complete order
                        if ($orderHeaderID != 0)
                        {
                            logDebug("Last order item. Redacting order header: " . $orderHeaderID);
                            $this->orderDataDeletionDB->redactControlCentreOrderData($orderHeaderID);

                            if (!$this->orderDataDeletionDB->hasError())
                            {
                                logDebug("Updating order header as finished for: " . $orderHeaderID);
                                // update the order header record to flag that the data has been redacted
                                $this->orderDataDeletionDB->updateOrderStatus($orderHeaderID);
                            }

                        }
                    }
                }
            }
        }
    }

    function ftpRedaction($pOrders)
    {
        $ftpResults = array('success'=>array(), 'failed'=>array());

        $ftpRootArray = array_filter(explode('/', $this->config['FTPORDERSROOTPATH']), 'strlen');
        $pathConfig = array('path' => implode('/', $ftpRootArray), 'prefix' => 'Order');

        // use the list of orders to remove all files from the ftp server
        foreach ($pOrders as $order)
        {
            $ordRoot = $pathConfig['path'] . '/';

            if ($this->config['FTPGROUPORDERSBYCODE'] == 1)
            {
                $ordRoot .= $order['uploadgroupcode'] . '/';
            }

            $curlResult = CurlObj::ftpDeleteRecursive($ordRoot, [$order['uploadordernumber'], 'Order_' . $order['uploadref']], 5, 30);
        
            if ($curlResult['error'] != 0)
            {
                DatabaseObj::updateActivityLog2($this->orderDataDeletionDB->getConnection(), 0, $order['orderid'], $order['userid'], "", "", 1, 
                                                            'ORDERDATA', 'FTP-CLEANUP', $order['orderitemid'], 0);

                $ftpResults['failed'][] = $order;
            }
            else
            {
                DatabaseObj::updateActivityLog2($this->orderDataDeletionDB->getConnection(), 0, $order['orderid'], $order['userid'], "", "", 1, 
                                                            'ORDERDATA', 'FTP-CLEANUP', $order['orderitemid'], 1);

                $ftpResults['success'][] = $order;
            }
        }

        return $ftpResults;
    }

    function localFileRedaction($pOrderUploadRef)
    {
        $returnArray = array('deleted'=> array(), 'failed' => array());
		// If the utils class is not loaded do so.
		if (!class_exists('UtilsObj')) {
			require_once '../Utils/Utils.php';
		}
		// Paths are generated from order upload ref, and are structured in Y/m/d/H
		$paths = UtilsObj::generateOrderThumbnailsPath($pOrderUploadRef, false);
		$pageDir = $paths['actual'] . DIRECTORY_SEPARATOR;
		$xmlString = $pageDir . '%s';

        if (file_exists($pageDir))
        {
            // list all files in the directory and delete
            $pageFiles = array_diff(scandir($pageDir), array('..', '.'));

            foreach ($pageFiles as $fileName)
            {
                if (unlink($pageDir . $fileName))
                {
                    $returnArray['deleted'][] = $fileName;
                }
                else
                {
                    $returnArray['failed'][] = $fileName;
                }
            }

            // delete the directory
            if (rmdir($pageDir))
            {
                $returnArray['deleted'][] = $pageDir;
            }
            else
            {
                $returnArray['failed'][] = $pageDir;
            }
        }
        else
        {
            // log the directory as removed as it does not exist
            $returnArray['deleted'][] = $pageDir;
        }

        // delete the xml file used for page turning
        $xmlFile = sprintf($xmlString, $pOrderUploadRef . '.xml');

        if (file_exists($xmlFile))
        {
            // delete the xml data file
            if (unlink($xmlFile))
            {
                $returnArray['deleted'][] = $xmlFile;
            }
            else
            {
                $returnArray['failed'][] = $xmlFile;
            }
        }
        else
        {
            // file does not exist, mark as deleted
            $returnArray['deleted'][] = $xmlFile;
        }

        return $returnArray;
    }

}

class OrderDataDeletionTaskDB
{
    private $connection;
    private $error = "";
  
    function __construct($pConnection)
    {
        $this->connection = $pConnection;
        $this->error = "";
    }

    public function hasError()
    {
        return $this->error != "";
    }

    public function getError()
    {
        return $this->error;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    private function runSQL($pSQL, $pBindParams = array())
    {
        $insertedID = 0;

        if ($this->connection)
        {
            $stmt = $this->connection->prepare($pSQL);
            if (!$stmt) 
            {
                throw new Exception(mysqli_error($this->connection));
            }

            $bindOK = true;

            if (count($pBindParams) > 0)
            {
                $bindOK = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($pBindParams));
            }

            if (!$bindOK)
            {
                throw new Exception('Bind Error: ' . $this->connection->error . ' - ' . $this->connection->errno);
            }
            else
            {
                if (!$stmt->execute())
                {
                    throw new Exception('Execute Error: ' . $this->connection->error . ' - ' . $this->connection->errno);
                }
                else
                {
                    if (strpos($pSQL, "INSERT") === 0)
                    {
                        $insertedID = $this->connection->insert_id;
                    }
                }
            }

            $stmt->free_result();
            $stmt->close();
            $stmt = null;

        }
        else
        {
            throw new Exception('No DB connection ' . $this->connection->error . ' - ' . $this->connection->errno);
        }

        return $insertedID;
    }

    public function addProductionEvents($pOrderItemsToBeDeleted)
    {
        global $gConstants;

        $this->error = "";

        $actionCode = TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_DELETELOCALDATA;
        $message = '';
        $actionStatus = TPX_REDACTION_STAGE_IN_PROGRESS;

        // get a list of projects based on the user id, and insert them as actions in the production events table
        $sql = 'INSERT INTO `PRODUCTIONEVENTS` (`datecreated`, `userid`, `orderitemid`, `actioncode`, `message`, `status`)
                    SELECT now(), `userid`, `id`, ?, ?, ? from `ORDERITEMS` WHERE `id` IN (' . implode(',', $pOrderItemsToBeDeleted) . ')';

        $bindParams = array('isi', $actionCode, $message, $actionStatus);

        if ($gConstants['optionms'])
        {
            $sql = 'INSERT INTO `PRODUCTIONEVENTS` (`datecreated`, `companycode`, `owner`, `userid`, `orderitemid`, `actioncode`, `message`, `status`)';
            $sql .= ' SELECT now(), IF(`origcompanycode` = `currentcompanycode`, `origcompanycode`, `currentcompanycode`), '
                    . '             IF(`origowner` = `currentowner`, `origowner`, `currentowner`), `userid`, `id`, ?, ?, ? '
                    . 'FROM `ORDERITEMS` WHERE `id` IN (' . implode(',', $pOrderItemsToBeDeleted) . ')';
            $sql .= ' UNION ';
            $sql .= ' SELECT now(), IF(`origcompanycode` = `currentcompanycode`, `currentcompanycode`, `origcompanycode`), '
                    . '             IF(`origowner` = `currentowner`, `currentowner`, `origowner`), `userid`, `id`, ?, ?, ? '
                    . 'FROM `ORDERITEMS` WHERE `id` IN (' . implode(',', $pOrderItemsToBeDeleted) . ') AND (`origcompanycode` != "" AND `origowner` != "")';

            $bindParams[0] .= 'isi';
            $bindParams[] = $actionCode;
            $bindParams[] = $message;
            $bindParams[] = $actionStatus;
        }

        try
        {
            $this->runSQL($sql, $bindParams);
        }
        catch (Exception $e)
        {
            $this->error = "Exception Caught in " . __FUNCTION__ . ": " . $e;
        }
    }

    public function updatePurgeDays($pWaitDays, $pOrdersToBePurged)
    {
        $this->error = "";

        $sql = 'UPDATE
                    `orderitems`
                SET
                    `purgedate` = DATE_ADD(NOW(), INTERVAL ? DAY)
                WHERE
                    `id` IN (' . implode(',', $pOrdersToBePurged) .')';
        try
        {
            $this->runSQL($sql, array('i', $pWaitDays));
        }
        catch (Exception $e)
        {
            $this->error = "Exception Caught in " . __FUNCTION__ . ": " . $e;
        }
    }

    public function updateNextCheckDate($pOrdersWithLinkedItems)
    {
        $this->error = "";

        $nextCheckDays = 5;

        $sql = 'UPDATE
                    `orderitems`
                SET
                    `purgenextcheckdate` = DATE_ADD(NOW(), INTERVAL ? DAY)
                WHERE
                    `id` IN (' . implode(',', $pOrdersWithLinkedItems) .')';
        try
        {
            $this->runSQL($sql, array('i', $nextCheckDays));
        }
        catch (Exception $e)
        {
            $this->error = "Exception Caught in " . __FUNCTION__ . ": " . $e;
        }
    }

    public function updateOrderItemStatus($pOrderID, $pStatus, $pMessage = "")
    {
        $this->error = "";

        $set = "";

        $bindParamsArray = array('s', $pMessage);

        switch ($pStatus)
        {
            case TPX_DATA_CLEAN_UP_STATUS_PRODUCTION_STARTED:
            {
                $set = "`productiondata` = 1";
                break;
            }
            case TPX_DATA_CLEAN_UP_STATUS_PRODUCTION_FINISHED:
            {
                $set = "`productiondata` = 2, `ftpdata` = 1";
                break;
            }
            case TPX_DATA_CLEAN_UP_STATUS_PRODUCTION_ERROR:
            {
                $set = "`productiondata` = 3";
                break;
            }
            case TPX_DATA_CLEAN_UP_STATUS_FTP_STARTED:
            {
                $set = "`ftpdata` = 1";
                break;
            }
            case TPX_DATA_CLEAN_UP_STATUS_FTP_FINISHED:
            {
                $set = "`ftpdata` = 2";
                break;
            }
            case TPX_DATA_CLEAN_UP_STATUS_FTP_ERROR:
            {
                $set = "`ftpdata` = 3";
                break;
            }
            case TPX_DATA_CLEAN_UP_STATUS_DATA_FINISHED:
            {
                $set = "`dbdata` = now()";
                break;
            }
        }

        // get a list of all projects which are waiting for the production event to finish
        $sql = "UPDATE `orderitems` SET `message` = ?, " . $set . " WHERE `id` ";

        if (is_array($pOrderID))
        {
            $sql .= "IN (" . implode(',', $pOrderID) .")";
        }
        else
        {
            $sql .= " = ?";
            $bindParamsArray[0] .= 'i';
            $bindParamsArray[] = $pOrderID;
        }

        if ($this->error === "")
        {
            try
            {
                $this->runSQL($sql, $bindParamsArray);
            }
            catch (Exception $e)
            {
                $this->error = "Exception Caught in " . __FUNCTION__ . ": " . $e;
            }
        }
    }

    public function updateCanReorder($pOrderID)
    {
        $this->error = "";

        $bindParamsArray = array();

        $sql = "UPDATE
                    `orderitems`
                SET
                    `canreorder` = 0
                WHERE
                    `id` ";
        
        if (is_array($pOrderID))
        {
            $sql .= " IN (" . implode(',', $pOrderID) .")";
        }
        else
        {
            $sql .= " = ?";
            $bindParamsArray[0] = 'i';
            $bindParamsArray[] = $pOrderID;
        }

        try
        {
            $this->runSQL($sql, $bindParamsArray);
        }
        catch (Exception $e)
        {
            $this->error = "Exception Caught in " . __FUNCTION__ . ": " . $e;
        }
    }

    public function updateOrderStatus($pOrderID)
    {
        $this->error = "";

        $bindParamsArray = array();

        $sql = "UPDATE
                    `orderheader`
                SET
                    `dbdata` = now()
                WHERE
                    `id` ";
        
        if (is_array($pOrderID))
        {
            $sql .= " IN (" . implode(',', $pOrderID) .")";
        }
        else
        {
            $sql .= " = ?";
            $bindParamsArray[0] = 'i';
            $bindParamsArray[] = $pOrderID;
        }

        try
        {
            $this->runSQL($sql, $bindParamsArray);
        }
        catch (Exception $e)
        {
            $this->error = "Exception Caught in " . __FUNCTION__ . ": " . $e;
        }
    }

    public function deleteThumbnailRecords($pUploadRef)
    {
        $this->error = "";

        $bindParamsArray = array();

        $sql = "DELETE FROM `ORDERTHUMBNAILS` WHERE `uploadref` ";
        
        if (is_array($pUploadRef))
        {
            $sql .= " IN (" . implode(',', preg_replace('/^(.*?)$/', "'$1'", preg_replace("#[\\\\\\\\']#", "\\'", $pUploadRef))) .")";
        }
        else
        {
            $sql .= " = ?";
            $bindParamsArray[0] = 's';
            $bindParamsArray[] = $pUploadRef;
        }

        try
        {
            $this->runSQL($sql, $bindParamsArray);
        }
        catch (Exception $e)
        {
            $this->error = "Exception Caught in " . __FUNCTION__ . ": " . $e;
        }
    }

    public function getOrderItemsWhichNeedDeleting($pLimit, $pWebBrandCode)
    {
        $this->error = "";

        $ordersToBeDeleted = array();

        $orderitemid = 0;
        $orderid = 0;
        $uploadorderid = 0;
        $userid = 0;

        if ($this->connection)
        {
            // get a list of all projects linked to the specified user, still in progress
            $sql = "SELECT
                        `oi`.`id`,
                        `oi`.`orderid`,
                        `oi`.`uploadorderid`,
                        `oi`.`userid`
                     FROM 
                        `orderitems` as `oi`
                    JOIN 
                        `orderheader` as `oh`
                    ON
                        `oh`.`id` = `oi`.`orderid` 
                    WHERE
                        `oh`.`webbrandcode` = ?
					AND
                        DATEDIFF(CURRENT_TIMESTAMP(), `oi`.`purgedate`) >= 0
                    AND
                        `oi`.`productiondata` = 0";
                 
            if($pLimit > 0)
            {
                $sql .= " LIMIT " . $pLimit;
            }

            if ($stmt = $this->connection->prepare($sql))
            {
				if ($stmt->bind_param('s', $pWebBrandCode))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($orderitemid, $orderid, $uploadorderid, $userid))
                                {
                                    while ($stmt->fetch())
                                    {
                                        $ordersToBeDeleted[] = array(   'orderid' => $orderid,
                                                                        'orderitemid' => $orderitemid,
                                                                        'uploadorderid' => $uploadorderid,
                                                                        'userid' => $userid);
                                    }
                                }
                                else
                                {
                                    $this->error = __FUNCTION__ . ' bind_result ' . $this->connection->error;
                                }
                            }
                        }
                        else
                        {
                            $this->error = __FUNCTION__ . ' store_result ' . $this->connection->error;
                        }
                    }
                    else
                    {
                        $this->error = __FUNCTION__ . ' execute ' . $this->connection->error;
                    }
                }
                else
                {
                    // could not bind parameters
                    $this->error = __FUNCTION__ . ' bind_param ' . $this->connection->error;  
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $this->error = __FUNCTION__ . ' prepare ' . $this->connection->error . " " . $sql;
            }
        }
        else
        {
            // could not open database connection
            $this->error = __FUNCTION__ . ' connect ' . $this->connection->error;
        }
        
        return $ordersToBeDeleted;
    }

    public function getOrderItems($pServerDateTime, $pWebBrandCode, $pOrderRedactionDays, $pLimit)
    {
        $this->error = "";

        $returnArray = array('ordertobedeleted' => array(), 'orderswithactivelinkeditems' => array());
   
        if ($this->connection)
        {
            // get a list of all projects linked to the specified user, still in progress
            $sql = "SELECT
                        oi.`id`,
                        oi.`orderid`,
                        oi.`userid`,
                        oi.`itemnumber`,
                        oi.`uploadorderid`,
                        oi.`uploadordernumber`,
                        oi.`uploadorderitemid`,
                        oi.`origorderid`,
                        oi.`origorderitemid`,
                        oi.`projectname`,
                        oi.`activetimestamp`,
                        oi.`active`,
                        (SELECT GROUP_CONCAT(`id`, '\t', `activetimestamp`, '\t', `active` SEPARATOR '\n') FROM ORDERITEMS oil WHERE `oil`.`uploadorderitemid` = `oi`.`id` 
                            AND `oil`.`id` <> `oi`.`id`) as `linkeditems`
                    FROM 
                        orderitems as oi
                    JOIN 
                        orderheader as oh 
                    ON
                        oh.id = oi.orderid 
                    WHERE
                        oh.webbrandcode = ?
                    AND
                        oi.id = oi.uploadorderitemid
                    AND 
                        oi.active IN (1, 2)
                    AND
                        oi.productiondata = 0
                    AND
                        DATEDIFF(CURRENT_TIMESTAMP(), oi.activetimestamp) >= ?
                    AND
                    (
                            oi.purgenextcheckdate = '0000-00-00 00:00:00'
                        OR
                            DATEDIFF(CURRENT_TIMESTAMP(), oi.purgenextcheckdate) > 0
                    )";

            if($pLimit > 0)
            {
                $sql .= " LIMIT " . $pLimit;
            }
                   
        
            if ($stmt = $this->connection->prepare($sql))
            {
                if ($stmt->bind_param('si', $pWebBrandCode, $pOrderRedactionDays))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($id, $orderid,$userid,$itemnumber,$uploadorderid,$uploadordernumber,$uploadorderitemid,$origorderid,$origorderitemid,
                                                        $projectname,$activetimestamp,$active,$linkeditems))
                                {
                                    while ($stmt->fetch())
                                    {
                                        $tempArray = array();
                                        $tempArray['id'] = $id;
                                        $tempArray['orderid'] = $orderid;
                                        $tempArray['userid'] = $userid;
                                        $tempArray['itemnumber'] = $itemnumber;
                                        $tempArray['uploadorderid'] = $uploadorderid;
                                        $tempArray['uploadordernumber'] = $uploadordernumber;
                                        $tempArray['uploadorderitemid'] = $uploadorderitemid;
                                        $tempArray['origorderid'] = $origorderid;
                                        $tempArray['origorderitemid'] = $origorderitemid;
                                        $tempArray['projectname'] = $projectname;
                                        $tempArray['activetimestamp'] = $activetimestamp;
                                        $tempArray['active'] = $active;
                                        $tempArray['linkeditems'] = $linkeditems;

                                        $canBePurged = false;

                                        if ($tempArray['linkeditems'] != "")
                                        {
                                            $linkedItemArray = explode("\n", $tempArray['linkeditems']);

                                            foreach ($linkedItemArray as $linkedItem)
                                            {
                                                list($linkedItemID, $linkedItemActiveDate, $linkedItemActiveStatus) = explode("\t", $linkedItem);

                                                $linkedItemActiveDateTime = new \DateTime($linkedItemActiveDate);

                                                // work out if the linked item is whithin the time
                                                $interval = $pServerDateTime->diff($linkedItemActiveDateTime);

                                                // active linked order items have a status which is not 1 or 2 (cancelled or completed)
                                                // or their active time stamp is within the purge period
                                                if (($linkedItemActiveStatus != 1) && ($linkedItemActiveStatus != 2))
                                                {
                                                    $returnArray['orderswithactivelinkeditems'][] = $id;
                                                }
                                                else if ($interval->invert)
                                                {
                                                    $returnArray['orderswithactivelinkeditems'][] = $id;
                                                }
                                                else
                                                {
                                                    $returnArray['ordertobedeleted'][] = $linkedItemID;
                                                    $canBePurged = true;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $canBePurged = true;
                                        }

                                        if ($canBePurged)
                                        {
                                            $returnArray['ordertobedeleted'][] = $tempArray['id'];
                                        }
                                    }
                                }
                                else
                                {
                                    $this->error = __FUNCTION__ . ' bind_result ' . $this->connection->error;
                                }
                            }
                        }
                        else
                        {
                            $this->error = __FUNCTION__ . ' store_result ' . $this->connection->error;
                        }
                    }
                    else
                    {
                        $this->error = __FUNCTION__ . ' execute ' . $this->connection->error;
                    }
                }
                else
                {
                    // could not bind parameters
                    $this->error = __FUNCTION__ . ' bind_param ' . $this->connection->error;
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $this->error = __FUNCTION__ . ' prepare ' . $this->connection->error . " " . $sql;
            }
        }
        else
        {
            // could not open database connection
            $this->error = __FUNCTION__ . ' connect ' . $this->connection->error;
        }

        return $returnArray;
    }

    public function getFTPStartedOrders($pLimit, $pWebBrandCode)
    {
        $this->error = "";

        $returnArray = array();

        $orderitemid = 0;
        $uploadordernumber = '';
        $uploadgroupcode = '';
        $uploadref = '';
        $orderid = 0;
        $uploadorderid = 0;
        $userid = 0;
        $uploadref = '';

        if ($this->connection)
        {
            // get a list of all projects which are waiting for the production event to finish
            $sql = "SELECT
                        `oi`.`id`,
                        `oi`.`uploadordernumber`,
                        `oi`.`uploadgroupcode`,
                        `oi`.`uploadref`,
                        `oi`.`orderid`,
                        `oi`.`uploadorderid`,
                        `oi`.`uploadorderitemid`,
                        `oi`.`userid`,
                        `oi`.`uploadref`
                    FROM 
                        `orderitems` `oi`
                    JOIN 
                        `orderheader` as `oh` 
                    ON
                        `oh`.`id` = `oi`.`orderid` 
                    WHERE
                        `oh`.`webbrandcode` = ?
                    AND
                        `oi`.`productiondata` = 2
                    AND 
                        `oi`.`ftpdata` = 1
                    AND 
                        `oi`.`dbdata` = '0000-00-00 00:00:00'";
    
            if($pLimit > 0)
            {
                $sql .= " LIMIT " . $pLimit;
            }

            if ($stmt = $this->connection->prepare($sql))
            {
                if ($stmt->bind_param('s', $pWebBrandCode))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($orderitemid, $uploadordernumber, $uploadgroupcode, $uploadref, 
                                                            $orderid, $uploadorderid, $uploadorderitemid, $userid, $uploadref))
                                {
                                    while ($stmt->fetch())
                                    {
                                        $returnArray[] = array( 'orderitemid' => $orderitemid, 
                                                                'uploadordernumber' => $uploadordernumber,
                                                                'uploadgroupcode' => $uploadgroupcode,
                                                                'uploadref' => $uploadref,
                                                                'orderid' => $orderid,
                                                                'uploadorderid' => $uploadorderid,
                                                                'uploadorderitemid' => $uploadorderitemid,
                                                                'userid' => $userid,
                                                                'uploadref' => $uploadref
                                                            );
                                    }
                                }
                                else
                                {
                                    $this->error = __FUNCTION__ . ' bind_result ' . $this->connection->error;
                                }
                            }
                        }
                        else
                        {
                            $this->error = __FUNCTION__ . ' store_result ' . $this->connection->error;
                        }
                    }
                    else
                    {
                        $this->error = __FUNCTION__ . ' execute ' . $this->connection->error;
                    }
                }
                else
                {
                    $this->error = __FUNCTION__ . ' bind_param ' . $this->connection->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $this->error = __FUNCTION__ . ' prepare ' . $this->connection->error . " " . $sql;
            }

            $this->connection->close();
        }
        else
        {
            // could not open database connection
            $this->error = __FUNCTION__ . ' connect ' . $this->connection->error;
        }

        return $returnArray;
    }

    public function getProductionStatus($pWebBrandCode)
    {
        $this->error = "";

        $returnArray = array('success' => array(), 'failed' => array());

        $orderitemid = 0;
        $orderid = 0;
        $uploadorderid = 0;
        $userid = 0;

        if ($this->connection)
        {
            // get a list of all projects which are waiting for the production event to finish
            $sql = "SELECT
                        `pe`.`orderitemid`,
                        `oi`.`orderid`,
                        `oi`.`uploadorderid`,
                        `oi`.`userid`,
                        `pe`.`status`
                    FROM 
                        `orderitems` `oi`
                    JOIN 
                        `productionevents` `pe` 
                    ON 
                        `pe`.`orderitemid` = `oi`.`id`
                    JOIN
                        `orderheader` as `oh` 
                    ON
                        `oh`.`id` = `oi`.`orderid` 
                    WHERE
                        `oh`.`webbrandcode` = ?
                    AND
                        `oi`.`productiondata` = 1
                    AND 
                        `oi`.`ftpdata` = 0
                    AND 
                        `oi`.`dbdata` = '0000-00-00 00:00:00'
                    AND
                        `pe`.`status` <> 3";

            if ($stmt = $this->connection->prepare($sql))
            {
                if ($stmt->bind_param('s', $pWebBrandCode))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($orderitemid, $orderid,  $uploadorderid, $userid, $status))
                                {
                                    while ($stmt->fetch())
                                    {
                                        $orderItem = array( 'orderid' => $orderid,
                                                            'orderitemid' => $orderitemid,
                                                            'uploadorderid' => $uploadorderid,
                                                            'userid' => $userid);

                                        if ($status == TPX_PRODUCTION_EVENT_STATUS_SUCCESSFUL)
                                        {
                                            $returnArray['success'][] = $orderItem;
                                        }
                                        else if ($status == TPX_PRODUCTION_EVENT_STATUS_FAILED)
                                        {
                                            $returnArray['failed'][] = $orderItem;
                                        }
                                    }
                                }
                                else
                                {
                                    $this->error = __FUNCTION__ . ' bind_result: ' . $this->connection->error;
                                }
                            }
                        }
                        else
                        {
                            $this->error = __FUNCTION__ . ' store_result: ' . $this->connection->error;
                        }
                    }
                    else
                    {
                        $this->error = __FUNCTION__ . ' execute: ' . $this->connection->error;
                    }
                }
                else
                {
                    $this->error = __FUNCTION__ . ' bind_param: ' . $this->connection->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $this->error = __FUNCTION__ . ' prepare ' . $this->connection->error;
            }

            $this->connection->close();
        }
        else
        {
            // could not open database connection
            $this->error = __FUNCTION__ . ' connect ' . $this->connection->error;
        }

        return $returnArray;
    }

    public function redactControlCentreOrderItemData($pOrderItemID)
    {
        $this->error = "";

        if ($this->connection)
        {
            // Order Item Redaction

            $queryArray['METADATA']['sql'] = 'UPDATE `METADATAVALUES` JOIN `METADATA` ON `METADATAVALUES`.`metadataid` = `METADATA`.`id` '
                                                . 'SET `METADATAVALUES`.`value`="" WHERE `METADATA`.`orderitemid`= ?';
            $queryArray['METADATA']['id'] = $pOrderItemID;

            $queryArray['ORDERITEMCOMPONENTS']['sql'] = 'UPDATE `ORDERITEMCOMPONENTS` SET `setname` = CONCAT("Set ", setid)'
                    . ' WHERE `orderitemid` = ? AND `setid` > 0';
            $queryArray['ORDERITEMCOMPONENTS']['id'] = $pOrderItemID;

            $queryArray['SHAREDITEMS']['sql'] = 'DELETE FROM `SHAREDITEMS` WHERE `orderitemid` = ?';
            $queryArray['SHAREDITEMS']['id'] = $pOrderItemID;

            $queryArray['ORDERITEMS']['sql'] = 'UPDATE `ORDERITEMS` SET `shareid` = 0, `projectname` = "", `jobticketoutputsubfoldername` = "", '
                    . ' `pagesoutputsubfoldername` = "", `cover1outputsubfoldername` = "", `cover2outputsubfoldername` = "", `xmloutputsubfoldername` = "", '
                    . ' `jobticketoutputfilename` = "", `pagesoutputfilename` = "", `cover1outputfilename` = "", `cover2outputfilename` = "", '
                    . ' `xmloutputfilename` = "", `previewsonline` = 0'
                    . ' WHERE `id` = ?';
            $queryArray['ORDERITEMS']['id'] = $pOrderItemID;

            // execute the queries in a transaction, any errors, rollback
            $this->connection->query('START TRANSACTION');

            // loop around the query array, break on failure
            foreach ($queryArray as $tableName => $tableData)
            {
                $sql = $tableData['sql'];
                $id = $tableData['id'];

                if ($stmt = $this->connection->prepare($sql))
                {
                    if ($stmt->bind_param('i', $id))
                    {
                        if (!$stmt->execute())
                        {
                            $this->error = __FUNCTION__ . ' execute (' . $tableName . '): ' . $this->connection->error;
                        }
                    }
                    else
                    {
                        $this->error = __FUNCTION__ . ' bind_param (' . $tableName . '): ' . $this->connection->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    $this->error = __FUNCTION__ . ' prepare (' . $tableName . '): ' . $this->connection->error;
                }

                if ($this->hasError())
                {
                    $this->connection->query('ROLLBACK');
                    break;
                }
            }


            // if no errors, commit the transaction
            if (!$this->hasError())
            {
                $this->connection->query('COMMIT');
            }
            else
            {
                $this->connection->query('ROLLBACK');
            }

            $this->connection->close();
        }
    }

    public function redactControlCentreOrderData($pOrderID)
    {
        $this->error = "";

        $fieldName = '';

        $metadataFields = array();

        if ($this->connection)
        {
            $queryArray['ORDERHEADER']['sql'] = 'UPDATE `ORDERHEADER` SET `designeruuid` = "", `useripaddress` = "", `billingcustomeraccountcode` = "", '
                    . ' `billingcustomername` = "", `billingcustomeraddress1` = "", `billingcustomeraddress2` = "", `billingcustomeraddress3` = "", '
                    . ' `billingcustomeraddress4` = "", `billingcustomerpostcode` = "", `billingcustomertelephonenumber` = "", '
                    . ' `billingcustomeremailaddress` = "", `billingcontactfirstname` = "", `billingcontactlastname` = "", '
                    . ' `billingcustomerregisteredtaxnumbertype` = "", `billingcustomerregisteredtaxnumber` = "" '
                    . ' WHERE `id` = ?';
            $queryArray['ORDERHEADER']['id'] = $pOrderID;

            $queryArray['CCILOG']['sql'] = 'UPDATE `CCILOG` SET `transactionid` = "", `authorisationid` = "", `cardnumber` = "", `cvvflag` = "", '
                    . ' `payeremail` = "", `payerid` = "", `formattedtransactionid` = "", `formattedauthorisationid` = "", `formattedcardnumber` = "" '
                    . ' WHERE `orderid` = ?';
            $queryArray['CCILOG']['id'] = $pOrderID;

            $queryArray['ORDERSHIPPING']['sql'] = 'UPDATE `ORDERSHIPPING` SET `shippingcustomername` = "", `shippingcustomeraddress1` = "", '
                    . '`shippingcustomeraddress2` = "", `shippingcustomeraddress3` = "", `shippingcustomeraddress4` = "", `shippingcustomerpostcode` = "", '
                    . ' `shippingcustomertelephonenumber` = "", `shippingcustomeremailaddress` = "", `shippingcontactfirstname` = "", '
                    . '`shippingcontactlastname` = "" '
                    . ' WHERE `orderid` = ?';
            $queryArray['ORDERSHIPPING']['id'] = $pOrderID;

            // execute the queries in a transaction, any errors, rollback
            $this->connection->query('START TRANSACTION');

            // loop around the query array, break on failure
            foreach ($queryArray as $tableName => $tableData)
            {
                $sql = $tableData['sql'];
                $id = $tableData['id'];

                if ($stmt = $this->connection->prepare($sql))
                {
                    if ($stmt->bind_param('i', $id))
                    {
                        if (!$stmt->execute())
                        {
                            $this->error = __FUNCTION__ . ' execute (' . $tableName . '): ' . $this->connection->error;
                        }
                    }
                    else
                    {
                        $this->error = __FUNCTION__ . ' bind_param (' . $tableName . '): ' . $this->connection->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    $this->error = __FUNCTION__ . ' prepare (' . $tableName . '): ' . $this->connection->error;
                }

                if ($this->hasError())
                {
                    $this->connection->query('ROLLBACK');
                    break;
                }
            }


            // if no errors, commit the transaction
            if (!$this->hasError())
            {
                $this->connection->query('COMMIT');
            }
            else
            {
                $this->connection->query('ROLLBACK');
            }

            $this->connection->close();
        }
    }

    public function allItemsRedacted($pOrderItemID)
    {
        $this->error = "";

        $returnOrderID = 0;
        $orderID = 0;
        $productionData = 0;
        $ftpData = 0;
        $dbData = "0000-00-00 00:00:00";

        $sql = "SELECT 
                    ORDERID,
                    PRODUCTIONDATA,
                    FTPDATA,
                    DBDATA
                FROM 
                    ORDERITEMS
                WHERE 
                    ORDERID = (SELECT ORDERID FROM ORDERITEMS WHERE id = ?)";

        if ($stmt = $this->connection->prepare($sql))
        {
            if ($stmt->bind_param('i', $pOrderItemID))
            {
                if ($stmt->execute())
                {
                    if ($stmt->store_result())
                    {
                        if ($stmt->num_rows > 0)
                        {
                            if ($stmt->bind_result($orderID, $productionData, $ftpData, $dbData))
                            {
                                $finished = true;

                                while ($stmt->fetch())
                                {
                                    if ($finished)
                                    {
                                        if (($productionData == 2) && ($ftpData == 2) && ($dbData != "0000-00-00 00:00:00"))
                                        {
                                            $finished = true;
                                        }
                                        else
                                        {
                                            $finished = false;
                                        }
                                    }
                                }

                                if ($finished)
                                {
                                    $returnOrderID = $orderID;
                                }

                            }
                            else
                            {
                                $this->error = __FUNCTION__ . ' bind_result: ' . $this->connection->error;
                            }
                        }
                    }
                    else
                    {
                        $this->error = __FUNCTION__ . ' store_result: ' . $this->connection->error;
                    }
                }
                else
                {
                    $this->error = __FUNCTION__ . ' execute: ' . $this->connection->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $this->error = __FUNCTION__ . ' bind_param ' . $this->connection->error;
            }
        }
        else
        {
            $this->error = __FUNCTION__ . ' prepare ' . $this->connection->error;
        }

        return $returnOrderID;
    }
}

?>