<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

class productCollectionResourceDeletionTask
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
        $defaultSettings['code'] = 'TAOPIX_PRODUCTCOLLECTIONRESOURCEDELETION';
        $defaultSettings['name'] = 'en Product Collection Resource Deletion<p>it Product Collection Resource Deletion<p>fr Product Collection Resource Deletion<p>es Product Collection Resource Deletion';

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

    static function run()
    {
        logDebug("Starting Product Collection Resource Deletion task");

        try
        {
            // get list of events for the task
            $taskCode = self::register();
            $taskCode = $taskCode['code'];
            $error = '';
            $errorParam = '';

            TaskObj::writeLogEntry('Task: ' . $taskCode . '. Retrieving Events.');

            $resourcesToDeleteDetails = self::getDeprecatedResourcesToPurge();

            if ($resourcesToDeleteDetails['error'] === '')
            {
                $resourceFoldersToDeleteCount = count($resourcesToDeleteDetails['data']);

                if ($resourceFoldersToDeleteCount > 0)
                {
                    $resourcesSuccesfullyDeleted = Array();

                    // iterate around each resource location combination found and attempt to delete them
                    for ($i = 0; $i < $resourceFoldersToDeleteCount; $i++)
                    {
                        $theResourceFolder = $resourcesToDeleteDetails['data'][$i];
                        $folderDeleted = self::deleteCollectionResourceFolder($theResourceFolder['collectioncode'], $theResourceFolder['collectionversiondate']);

                        // if our target folder no longer exists add it to the list of records to delete
                        if ($folderDeleted === true)
                        {
                            $resourcesSuccesfullyDeleted[] = $theResourceFolder;
                        }
                    }

                    // if we have managed to delete any resources delete their database records
                    if (count($resourcesSuccesfullyDeleted) > 0)
                    {
                        $recordDeletionResultArray = self::deleteProductCollectionResourceDatabaseRecords($resourcesSuccesfullyDeleted);

                        if ($recordDeletionResultArray['error'] === '')
                        {
                            logDebug("Deprecated product collection resources succesfully cleaned up");
                        }
                        else
                        {
                            logDebug("Unable to delete database records for deleted resources with error: " . $recordDeletionResultArray['error'] . ' ' . $recordDeletionResultArray['errorparam']);
                            logDebug("The PRODUCTCOLLECTIONRESOURCES table will contain some records pointing to non-existent resources, this will not cause any problems and will be removed on next run.");
                        }
                    }
                    else
                    {
                        logDebug("Unable to delete any resources");    
                    }
                }
                else
                {
                    logDebug("No resources to delete");
                }
            }
            else
            {
                logDebug("Unable to get list of resources to delete with error: " . $resourcesToDeleteDetails['error'] . " " . $resourcesToDeleteDetails['errorparam']);
            }
        }
        catch (Exception $e)
        {
            logDebug('en ' . 'Failed with error ' . $e->getMessage());
            
        }
    }


    /**
     * Gets an array of resources to delete
     * @return array result array containing list of distinct folders to purge
     */
    static function getDeprecatedResourcesToPurge()
    {
        $resultArray = UtilsObj::getReturnArray();
        $collectionCode = '';
        $collectionVersionDate = '';
        $validDeletion = false;
        $error = '';
        $errorParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $sql = 'SELECT DISTINCT `pcr`.`collectioncode`, `pcr`.`collectionversiondate`, IF(`pcr`.`collectionversiondate` = `af`.`versiondate`, 0, 1) AS `validdeletion` FROM `PRODUCTCOLLECTIONRESOURCES` AS `pcr`
                    LEFT JOIN `APPLICATIONFILES` AS `af` 
                    ON `pcr`.`collectioncode` = `af`.`ref` AND `af`.`type` = 0
                    WHERE `pcr`.`islatest` = 0 AND `pcr`.`nextpurgetime` <= curtime()';
            
            $stmt = $dbObj->prepare($sql);

            if ($stmt)
            {
                if ($stmt->execute())
                {
                    if ($stmt->store_result())
                    {
                        if ($stmt->num_rows > 0)
                        {
                            if ($stmt->bind_result($collectionCode, $collectionVersionDate, $validDeletion))
                            {
                                while($stmt->fetch())
                                {
                                    // sanity check to verify that we are not deleting incorrectly flagged resources
                                    // we should never delete a resource that has the same same versiondate as the collection it is assigned to
                                    if ($validDeletion === 1)
                                    {
                                        $resourceFolderToPurge = Array();
                                        $resourceFolderToPurge['collectioncode'] = $collectionCode;
                                        $resourceFolderToPurge['collectionversiondate'] = $collectionVersionDate;

                                        $resultArray['data'][] = $resourceFolderToPurge;
                                    }
                                    else
                                    {
                                        logDebug("Resource folder for collection: " . $collectionCode . " and versiondate: " . $collectionVersionDate . " incorrectly flagged for purge.");
                                    }
                                }
                            }
                            else
                            {
                                // could not bind result
                                $error = 'str_DatabaseError';
                                $errorParam = __FUNCTION__ . ' bind result: ' . $dbObj->error;
                            }
                        }
                    }
                    else
                    {
                        // could not store result
                        $error = 'str_DatabaseError';
                        $errorParam = __FUNCTION__ . ' store result: ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not execute
                    $error = 'str_DatabaseError';
                    $errorParam = __FUNCTION__ . ' execute: ' . $dbObj->error;
                }
            }
            else
            {
                // could not prepare statement
                $error = 'str_DatabaseError';
                $errorParam = __FUNCTION__ . ' prepare statement: ' . $dbObj->error;
            }
        }
        else
        {
            // could not get database connection
            $error = 'str_DatabaseError';
            $errorParam = __FUNCTION__ . ' getconnection: ' . $dbObj->error;
        }

        $resultArray['error'] = $error;
        $resultArray['errorparam'] = $errorParam;

        return $resultArray;
    }

    static function deleteCollectionResourceFolder($pCollectionCode, $pCollectionVersionDate)
    {
        $success = false;
        $resourceFolderPath = UtilsObj::getProductCollectionResourceFolderPath($pCollectionCode, $pCollectionVersionDate);

        UtilsObj::deleteFolder($resourceFolderPath);

        // clear the statcache as the rmdir is not guaranteed to refresh the folder status
        clearstatcache();

        if (! file_exists($resourceFolderPath))
        {
            $success = true;
        }

        return $success;
    }

    /**
     * Deletes database records for the records matching the passed array
     * @param array $pResourcesToDeleteArray in array with subarrays containing the keys collectioncode and collectionversiondate representing records to delete
     * @return array standard result array
     */
    static function deleteProductCollectionResourceDatabaseRecords($pResourcesToDeleteArray)
    {
        $resultArray = UtilsObj::getReturnArray();
        $error = '';
        $errorParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $sql = 'DELETE FROM `PRODUCTCOLLECTIONRESOURCES` WHERE ';
            $resourcesCount = count($pResourcesToDeleteArray);
            $bindDataTypes = Array();
            $bindValues = Array();
            $whereClause = '';
            
            // build the where clauses
            // this has to be done seperately for each record rather than in a like as we do not want to delete every record for for a collection code or date
            for ($i = 0; $i < $resourcesCount; $i++)
            {
                $theResource = $pResourcesToDeleteArray[$i];

                if ($i !== 0)
                {
                    $whereClause .= 'OR';
                }

                $whereClause .= '(`collectioncode` = ? AND `collectionversiondate` = ?)';
                array_push($bindDataTypes, 's', 's');
                array_push($bindValues, $theResource['collectioncode'], $theResource['collectionversiondate']);
            }

            $sql .= $whereClause;
            $stmt = $dbObj->prepare($sql);

            if ($stmt)
            {
                $bindOK = DatabaseObj::bindParams($stmt, $bindDataTypes, $bindValues);

                if ($bindOK)
                {
                    if (! $stmt->execute())
                    {
                        // could not execute statement
                        $error = 'str_DatabaseError';
                        $errorParam = __FUNCTION__ . ' execute ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not bind params
                    $error = 'str_DatabaseError';
                    $errorParam = __FUNCTION__ . ' bindparams ' . $dbObj->error;
                }
            }
            else
            {
                // could not prepare statement
                $error = 'str_DatabaseError';
                $errorParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
            }
        }
        else
        {
            // could not get database connection
            $error = 'str_DatabaseError';
            $errorParam = __FUNCTION__ . ' getconnection ' . $dbObj->error;
        }

        $resultArray['error'] = $error;
        $resultArray['errorparam'] = $errorParam;

        return $resultArray;
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
?>