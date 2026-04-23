<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
define('TPX_DESKTOP_PROJECT_THUMBNAIL_DELETION_UNORDERED_DELETION_DAYS', 7);

class desktopProjectThumbnailDeletionTask
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
        $defaultSettings['code'] = 'TAOPIX_DESKTOPPROJECTTHUMBNAILDELETION';
        $defaultSettings['name'] = 'en Desktop Project Thumbnail Deletion<p>it Desktop Project Thumbnail Deletion<p>fr Desktop Project Thumbnail Deletion<p>es Desktop Project Thumbnail Deletion';

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
        logDebug("Starting Desktop Project Thumbnail Deletion task");

        try
        {
            // get list of events for the task
            $taskCode = self::register();
            $taskCode = $taskCode['code'];
            $error = '';
            $errorParam = '';

            TaskObj::writeLogEntry('Task: ' . $taskCode . '. Retrieving Events.');

            //delete abandoned thumbnails
            $unorderedProjectResultArray = self::getUnorderedAndGuestProjects();

            if ($unorderedProjectResultArray['error'] === '')
            {
                if (count($unorderedProjectResultArray['data']) > 0)
                {
                    $unorderedDeletionResultsArray = UtilsObj::deleteDesktopProjectThumbnails($unorderedProjectResultArray['data']);
                    $error = $unorderedDeletionResultsArray['error'];
                    $errorParam = $unorderedDeletionResultsArray['errorparam'];
                }
            }
            else
            {
                $error = $unorderedProjectResultArray['error'];
                $errorParam = $unorderedProjectResultArray['errorparam'];
            }

            if ($error !== '')
            {
                logDebug('en Error cleaning up abandoned orders with error ' . $error . ': ' . $errorParam);
            }

            //get the deletion policies
            $brandDeletionPolicies = self::getBrandDeletionPolicies();

            if ($brandDeletionPolicies['error'] === '')
            {
                foreach ($brandDeletionPolicies['data'] as $theBrand)
                {
                    $error = '';
                    $errorParam = '';
                    $orderedDeletionResultsArray = Array();
                    $unorderedDeletionResultsArray = Array();

                    //get the license keys for the brand
                    $licenseKeyResultArray = DatabaseObj::getBrandLicenseKeyCodes($theBrand['code']);
                    $licenseKeyArray = array_column($licenseKeyResultArray, 'id');


                    $orderedProjectResultArray = self::getOrderedProjectsElgibleForDeletion($theBrand['desktopthumbnaildeletionordereddays'], $licenseKeyArray);

                    if ($orderedProjectResultArray['error'] === '')
                    {
                        if (count($orderedProjectResultArray['data']) > 0)
                        {
                            $orderedDeletionResultsArray = UtilsObj::deleteDesktopProjectThumbnails($orderedProjectResultArray['data']);
                        }
                    }
                    else
                    {
                        $error = $orderedProjectResultArray['error'];
                        $errorParam = $orderedProjectResultArray['errorparam'];
                    }

                    //check if we got any errors in the deletion
                    if (UtilsObj::getArrayParam($orderedDeletionResultsArray, 'error', '') !== '')
                    {
                        $error = $orderedDeletionResultsArray['error'];
                        $errorParam = $orderedDeletionResultsArray['errorparam'];
                    }

                    if ($error !== '')
                    {
                        logDebug('en Error cleaning up brand: ' . $theBrand['code'] . 'with error ' . $error . ': ' . $errorParam);
                    }
                    else
                    {
                        logDebug('en Succesfully cleaned up brand: ' . $theBrand['code']);
                    }
                }
            }
            else
            {
                logDebug($brandDeletionPolicies['error']);
            }
        }
        catch (Exception $e)
        {
            logDebug('en ' . 'Failed with error ' . $e->getMessage());
            
        }
    }

    static function getBrandDeletionPolicies()
    {
        $resultArray = UtilsObj::getReturnArray();
        $error = '';
        $errorParam = '';
        $code = '';
        $desktopThumbnailDeletionOrderedDays = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $sql = 'SELECT `code`, `desktopthumbnaildeletionordereddays`
                    FROM `BRANDING`
                    WHERE `desktopthumbnaildeletionenabled` = 1';

            if ($stmt = $dbObj->prepare($sql))
            {
                if ($stmt->execute())
                {
                    if ($stmt->store_result())
                    {
                        if ($stmt->num_rows > 0)
                        {
                            if ($stmt->bind_result($code, $desktopThumbnailDeletionOrderedDays))
                            {
                                while($stmt->fetch())
                                {
                                    $theBrandArray = Array();
                                    $theBrandArray['code'] = $code;
                                    $theBrandArray['desktopthumbnaildeletionordereddays'] = $desktopThumbnailDeletionOrderedDays;
                                    $resultArray['data'][] = $theBrandArray;
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
                        $error = 'str_DatabaseError';
                        $errorParam = __FUNCTION__ . ' store_result ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = 'str_DatabaseError';
                    $errorParam = __FUNCTION__ . ' execute ' . $dbObj->error;
                }
            }
            else
            {
                $error = 'str_DatabaseError';
                $errorParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
            }
        }
        else
        {
            $error = 'str_DatabaseError';
            $errorParam = __FUNCTION__ . ' connect ' . $dbObj->error;
        }

        $resultArray['error'] = $error;
        $resultArray['errorparam'] = $errorParam;

        return $resultArray;
    }


    static function getUnorderedAndGuestProjects()
    {
        $resultArray = UtilsObj::getReturnArray();
        $error = '';
        $errorParam = '';
        $projectRef = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $bindDataTypes[] = 'i';
            $bindValues[] = TPX_DESKTOP_PROJECT_THUMBNAIL_DELETION_UNORDERED_DELETION_DAYS;

            $sql = 'SELECT `d`.`projectref`
                    FROM `DESKTOPPROJECTTHUMBNAILS` AS `d`
                    LEFT JOIN `ORDERITEMS` AS `o` ON `d`.`projectref` = `o`.`projectref`
                    WHERE `datemodified` <= curtime() - INTERVAL ? DAY
                    AND `o`.`projectref` IS NULL';

            $stmt = $dbObj->prepare($sql);

            if ($stmt)
            {
                $bindOK = DatabaseObj::bindParams($stmt, $bindDataTypes, $bindValues);

                if ($bindOK)
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($projectRef))
                                {
                                    while($stmt->fetch())
                                    {
                                        $resultArray['data'][] = $projectRef;
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
                            $error = 'str_DatabaseError';
                            $errorParam = __FUNCTION__ . ' store_result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = 'str_DatabaseError';
                        $errorParam = __FUNCTION__ . ' execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = 'str_DatabaseError';
                    $errorParam = __FUNCTION__ . ' bindparam ' . $dbObj->error;
                }
            }
            else
            {
                $error = 'str_DatabaseError';
                $errorParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
            }
        }
        else
        {
            $error = 'str_DatabaseError';
            $errorParam = __FUNCTION__ . ' connect ' . $dbObj->error;
        }

        $resultArray['error'] = $error;
        $resultArray['errorparam'] = $errorParam;

        return $resultArray;
    }

    static function getOrderedProjectsElgibleForDeletion($pDays, $pGroupCodeArray)
    {   
        $resultArray = UtilsObj::getReturnArray();
        $error = '';
        $errorParam = '';
        $projectRef = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $inClause = '';
            $groupCodeCount = count($pGroupCodeArray);
            $bindDataTypes[] = 'i';
            $bindValues[] = $pDays;

            for ($i = 0; $i < $groupCodeCount; $i++)
            {
                if ($i !== 0)
                {
                    $inClause .= ',';
                }

                $inClause .= '?';
                $bindDataTypes[] = 's';
                $bindValues[] = $pGroupCodeArray[$i];
            }

            $bindDataTypes[] = 'i';
            $bindValues[] = $pDays;

            $sql = 'SELECT `projectref` FROM
                    (
                        SELECT `d`.`projectref`, MAX(`o`.`datelastmodified`) AS `mostrecentorderdate`
                        FROM `DESKTOPPROJECTTHUMBNAILS` AS `d`
                        INNER JOIN `ORDERITEMS` AS `o` ON `d`.`projectref` = `o`.`projectref`
                        WHERE `datemodified` <= curtime() - INTERVAL ? DAY
                        AND `groupcode` IN (' . $inClause . ')
                        GROUP BY `d`.`projectref`
                    ) AS `tmp`
                    WHERE `tmp`.`mostrecentorderdate` <= curtime() - INTERVAL ? DAY';

            $stmt = $dbObj->prepare($sql);

            if ($stmt)
            {
                $bindOK = DatabaseObj::bindParams($stmt, $bindDataTypes, $bindValues);

                if ($bindOK)
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($projectRef))
                                {
                                    while($stmt->fetch())
                                    {
                                        $resultArray['data'][] = $projectRef;
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
                            $error = 'str_DatabaseError';
                            $errorParam = __FUNCTION__ . ' store_result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = 'str_DatabaseError';
                        $errorParam = __FUNCTION__ . ' execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = 'str_DatabaseError';
                    $errorParam = __FUNCTION__ . ' bindparam ' . $dbObj->error;
                }
            }
            else
            {
                $error = 'str_DatabaseError';
                $errorParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
            }
        }
        else
        {
            $error = 'str_DatabaseError';
            $errorParam = __FUNCTION__ . ' connect ' . $dbObj->error;
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