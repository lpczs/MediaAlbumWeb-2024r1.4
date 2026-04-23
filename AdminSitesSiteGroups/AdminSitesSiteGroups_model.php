<?php
require_once('../Utils/UtilsDatabase.php');

class AdminSitesSiteGroups_model
{

    static function getGridData()
    {
        global $gSession;

        $resultArray = Array();
        $resultArray = DatabaseObj::getSiteGroupList();

        return $resultArray;
    }

    static function siteGroupAdd()
    {
        global $gSession;
        $result = '';
        $resultParam = '';
        $recordID = 0;
        $siteGroupCode = strtoupper($_POST['code']);
        $siteGroupName = html_entity_decode($_POST['name'], ENT_QUOTES);

        if (($siteGroupCode != '') && ($siteGroupName != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('INSERT INTO `SITEGROUPS` VALUES (0, now(), ?, ?)'))
                {
                    if ($stmt->bind_param('ss', $siteGroupCode, $siteGroupName))
                    {
                        if ($stmt->execute())
                        {
                            $recordID = $dbObj->insert_id;

                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                    $gSession['username'], 0, 'ADMIN', 'SITEGROUP-ADD', $recordID . ' ' . $siteGroupCode, 1);
                        }
                        else
                        {
                            // could not execute statement
                            // first check for a duplicate key (tax rate code)
                            if ($stmt->errno == 1062)
                            {
                                $result = 'str_ErrorStoreGroupExists';
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'sitegroupAdd execute ' . $dbObj->error;
                            }
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'sitegroupAdd bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'sitegroupAdd prepare ' . $dbObj->error;
                }
                $stmt->close();
                $stmt = null;
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'sitegroupAdd connect ' . $dbObj->error;
            }
        }
        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $recordID;
        $resultArray['code'] = $siteGroupCode;
        $resultArray['name'] = $siteGroupName;

        return $resultArray;
    }

    static function displayEdit($pID)
    {
        $resultArray = array();
        $siteGroupID = 0;
        $siteGroupCode = '';
        $siteGroupName = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $stmt = $dbObj->prepare('SELECT `id`, `code`, `name` FROM `SITEGROUPS` WHERE `id` = ?');
            if ($stmt)
            {
                if ($stmt->bind_param('i', $pID))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($siteGroupID, $siteGroupCode, $siteGroupName))
                                {
                                    $stmt->fetch();
                                }
                            }
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }

        $resultArray['id'] = $siteGroupID;
        $resultArray['code'] = $siteGroupCode;
        $resultArray['name'] = $siteGroupName;

        return $resultArray;
    }

    static function siteGroupEdit()
    {
        global $gSession;

        $result = '';
        $resultParam = '';

        $siteGroupID = $_GET['id'];
        $siteGroupCode = strtoupper($_POST['code']);
        $siteGroupName = html_entity_decode($_POST['name'], ENT_QUOTES);

        if (($siteGroupCode != '') && ($siteGroupName != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                $stmt = $dbObj->prepare('UPDATE `SITEGROUPS` SET `name` = ? WHERE `code` = ?');
                if ($stmt)
                {
                    if ($stmt->bind_param('ss', $siteGroupName, $siteGroupCode))
                    {
                        if ($stmt->execute())
                        {
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                    $gSession['username'], 0, 'ADMIN', 'SITEGROUP-UPDATE', $siteGroupID . ' ' . $siteGroupCode, 1);
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'sitegroupEdit execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'sitegroupEdit bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'sitegroupEdit prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'sitegroupEdit connect ' . $dbObj->error;
            }
        }
        $resultArray['result'] = $result;
        $resultArray['id'] = $siteGroupID;
        $resultArray['code'] = $siteGroupCode;
        $resultArray['name'] = $siteGroupName;

        return $resultArray;
    }

    static function siteGroupsDelete()
    {
        global $gSession;

        $result = '';
        $allDeleted = 1;
        $canDelete = false;

        $siteGroupIDList = explode(',', $_POST['idlist']);
        $siteGroupCodeList = explode(',', $_POST['codelist']);
        $siteGroupIDListCount = count($siteGroupIDList);

        $siteGroupCodesDeleted = Array();
        $siteGroupCodesNotUsed = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            for($i = 0; $i < $siteGroupIDListCount; $i++)
            {
                $stmt = $dbObj->prepare('SELECT `id` FROM `SITES` WHERE `sitegroup` = ?');
                if ($stmt)
                {
                    if ($stmt->bind_param('s', $siteGroupCodeList[$i]))
                    {
                        if ($stmt->execute())
                        {
                            if ($stmt->store_result())
                            {
                                if ($stmt->num_rows > 0)
                                {
                                    $result = 'str_ErrorUsedInSite';
                                    $canDelete = false;
                                    $allDeleted = 0;
                                }
                                else
                                {
                                    $canDelete = true;
                                    $item['id'] = $siteGroupIDList[$i];
                                    $item['code'] = $siteGroupCodeList[$i];
                                    array_push($siteGroupCodesNotUsed, $item);
                                }
                            }
                        }
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }

                if ($canDelete)
                {
                    $stmt = $dbObj->prepare('SELECT `id` FROM `SHIPPINGRATESITES` WHERE `sitegroupcode` = ?');
                    if ($stmt)
                    {
                        if ($stmt->bind_param('s', $siteGroupCodeList[$i]))
                        {
                            if ($stmt->execute())
                            {
                                if ($stmt->store_result())
                                {
                                    if ($stmt->num_rows > 0)
                                    {
                                        $result = 'str_ErrorUsedInShippingRate';
                                        $canDelete = false;
                                        $allDeleted = 0;
                                    }
                                    else
                                    {
                                        $canDelete = true;
                                        $item['id'] = $siteGroupIDList[$i];
                                        $item['code'] = $siteGroupCodeList[$i];
                                        array_push($siteGroupCodesNotUsed, $item);
                                    }
                                }
                            }
                        }
                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                }

                if ($canDelete)
                {
                    $stmt = $dbObj->prepare('DELETE FROM `SITEGROUPS` WHERE `id` = ?');
                    if ($stmt)
                    {
                        if ($stmt->bind_param('i', $siteGroupIDList[$i]))
                        {
                            if ($stmt->execute())
                            {
                                DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                        $gSession['username'], 0, 'ADMIN', 'SITEGROUP-DELETE',
                                        $siteGroupIDList[$i] . ' ' . $siteGroupCodeList[$i], 1);
                                array_push($siteGroupCodesDeleted, $siteGroupIDList[$i]);
                            }
                        }
                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                }
            }
            $dbObj->close();
        }

        $resultArray['alldeleted'] = $allDeleted;
        $resultArray['sitegroupids'] = $siteGroupCodesDeleted;
        $resultArray['result'] = $result;

        return $resultArray;
    }

}
?>
