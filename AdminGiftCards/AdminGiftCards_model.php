<?php

require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsDatabase.php');

class AdminGiftCards_model
{

   private static function getGiftcardCode($pListGiftcardIDs, $pCheckCompany=false, $pDBObj=null)
    {

        global $gConstants, $gSession;

        $dbObj = null;
        if ($pDBObj==null)
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
        }

        $totalCount = 0;
        $giftcardsDataRecord = array();
        $giftcardsDataRecord['data'] = array();
        $giftcardsDataRecord['recordcount'] = 0;
        $giftcardsDataRecord['result'] = '';
        $giftcardsDataRecord['resultparam'] = '';

        $sql = 'SELECT `code` FROM VOUCHERS WHERE `id` = ?';

        if ($dbObj)
        {

            if (($gConstants['optionms'])&&($pCheckCompany)&&($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN))
            {
                $sql .= ' AND `companycode` = ?';

                if ($stmt = $dbObj->prepare($sql))
                {
                    foreach ($pListGiftcardIDs as $gifcardID)
                    {
                        if ($stmt->bind_param('is', $gifcardID, $gSession['userdata']['usertype']))
                        {
                            if ($stmt->bind_result( $code ))
                            {
                                if ($stmt->execute())
                                {
                                    if ($stmt->fetch())
                                    {
                                        $giftcardsDataRecord['data'][$totalCount]['giftcardid']     = $gifcardID;
                                        $giftcardsDataRecord['data'][$totalCount]['code']   = $code;
                                        $totalCount++;
                                    }
                                }
                                else
                                {
                                    $giftcardsDataRecord['result'] = 'str_DatabaseError';
                                    $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcardCode execute ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $giftcardsDataRecord['result'] = 'str_DatabaseError';
                                $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcardCode bind_result ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $giftcardsDataRecord['result'] = 'str_DatabaseError';
                            $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcardCode bind_param ' . $dbObj->error;
                        }

                        $stmt->free_result();
                    }
                    $stmt->close();
                }
                else
                {
                    $giftcardsDataRecord['result'] = 'str_DatabaseError';
                    $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcardCode prepare ' . $dbObj->error;
                }

                $dbObj->close();

            }
            else
            {
                if ($stmt = $dbObj->prepare($sql))
                {
                    foreach ($pListGiftcardIDs as $gifcardID)
                    {
                        if ($stmt->bind_param('i', $gifcardID))
                        {
                            if ($stmt->bind_result( $code ))
                            {
                                if ($stmt->execute())
                                {
                                    if ($stmt->fetch())
                                    {
                                        $giftcardsDataRecord['data'][$totalCount]['giftcardid']     = $gifcardID;
                                        $giftcardsDataRecord['data'][$totalCount]['code']   = $code;
                                        $totalCount++;
                                    }
                                }
                                else
                                {
                                    $giftcardsDataRecord['result'] = 'str_DatabaseError';
                                    $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcardCode execute ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $giftcardsDataRecord['result'] = 'str_DatabaseError';
                                $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcardCode bind_result ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $giftcardsDataRecord['result'] = 'str_DatabaseError';
                            $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcardCode bind_param ' . $dbObj->error;
                        }
                        $stmt->free_result();
                    }
                    $stmt->close();
                }
                else
                {
                    $giftcardsDataRecord['result'] = 'str_DatabaseError';
                    $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcardCode prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
        }
        else
        {
            $giftcardsDataRecord['result'] = 'str_DatabaseError';
            $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcardCode connect ' . $dbObj->error;
        }

        $giftcardsDataRecord['recordcount'] = $totalCount;

        return $giftcardsDataRecord;

    }

    private static function getGiftcards($optionsList=null, $pListGiftcardIDs='', $pDBObj=null)
    {

        global $gConstants, $gSession;

        $dbObj = null;
        if ($pDBObj==null)
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
        }
        $needToBind = false;
        $smarty = SmartyObj::newSmarty('AdminGiftCards');

        $giftcardsDataRecord = array();
        $giftcardsDataRecord['data'] = array();
        $giftcardsDataRecord['recordcount'] = 0;
        $giftcardsDataRecord['result'] = '';
        $giftcardsDataRecord['resultparam'] = '';

        $totalCount = 0;

        $sql = 'SELECT SQL_CALC_FOUND_ROWS
                    VOUCHERS.id,
                    VOUCHERS.companycode,
                    VOUCHERS.code,
                    VOUCHERS.name,
                    VOUCHERS.startdate,
                    VOUCHERS.enddate,
                    VOUCHERS.groupcode,
                    VOUCHERS.userid,
                    VOUCHERS.discountvalue,
                    VOUCHERS.redeemeduserid,
                    VOUCHERS.redeemeddate,
                    VOUCHERS.active,
                    SELECTEDUSERS.contactfirstname,
                    SELECTEDUSERS.contactlastname,
                    SELECTEDUSERS.emailaddress,
                    REDEEMUSERS.contactfirstname,
                    REDEEMUSERS.contactlastname,
                    REDEEMUSERS.emailaddress
                FROM
                    `VOUCHERS`
                    LEFT JOIN `USERS` AS `SELECTEDUSERS` ON SELECTEDUSERS.id = VOUCHERS.userid
                    LEFT JOIN `USERS` AS `REDEEMUSERS` ON REDEEMUSERS.id = VOUCHERS.redeemeduserid
                WHERE
                    VOUCHERS.type = ' . TPX_VOUCHER_TYPE_GIFTCARD;

        if ($dbObj)
        {
            if (is_array($pListGiftcardIDs))
            {
                $sql .= ' AND VOUCHERS.id = ?';

                if ($stmt = $dbObj->prepare($sql))
                {
                    foreach ($pListGiftcardIDs as $gifcardID)
                    {
                        if ($stmt->bind_param('i', $gifcardID))
                        {
                            if ($stmt->bind_result( $id, $companyCode, $code, $name, $startDate, $endDate, $groupCode, $userID, $discountValue, $redeemUserId, $redeemedDate,
                                                    $isActive, $contactFirstName, $contactLastName, $contactEmailAddress, $redeemFirstName, $redeemLastName, $redeemEmailAddress))
                            {
                                if ($stmt->execute())
                                {
                                    if ($stmt->fetch())
                                    {
                                        $giftcardsDataRecord['data'][$totalCount]['giftcardid']             = $id;
                                        $giftcardsDataRecord['data'][$totalCount]['companycode']            = $companyCode;
                                        $giftcardsDataRecord['data'][$totalCount]['code']                   = $code;
                                        $giftcardsDataRecord['data'][$totalCount]['name']                   = $name;
                                        $giftcardsDataRecord['data'][$totalCount]['startdate']              = $startDate;
                                        $giftcardsDataRecord['data'][$totalCount]['enddate']                = $endDate;
                                        $giftcardsDataRecord['data'][$totalCount]['groupcode']              = $groupCode;
                                        $giftcardsDataRecord['data'][$totalCount]['userid']                 = $userID;
                                        $giftcardsDataRecord['data'][$totalCount]['giftcardvalue']          = $discountValue;
                                        $giftcardsDataRecord['data'][$totalCount]['redeemuserid']           = $redeemUserId;
                                        $giftcardsDataRecord['data'][$totalCount]['isactive']               = $isActive;
                                        $giftcardsDataRecord['data'][$totalCount]['contactfirstname']       = $contactFirstName;
                                        $giftcardsDataRecord['data'][$totalCount]['contactlastname']        = $contactLastName;
                                        $giftcardsDataRecord['data'][$totalCount]['contactemailaddress']    = $contactEmailAddress;
                                        $giftcardsDataRecord['data'][$totalCount]['redeemfirstname']        = $redeemFirstName;
                                        $giftcardsDataRecord['data'][$totalCount]['redeemlastname']         = $redeemLastName;
                                        $giftcardsDataRecord['data'][$totalCount]['redeememailaddress']     = $redeemEmailAddress;
                                        $giftcardsDataRecord['data'][$totalCount]['redeemeddate']           = $redeemedDate;
                                        $totalCount++;
                                    }
                                }
                                else
                                {
                                    $giftcardsDataRecord['result'] = 'str_DatabaseError';
                                    $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcards execute ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $giftcardsDataRecord['result'] = 'str_DatabaseError';
                                $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcards bind_result ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $giftcardsDataRecord['result'] = 'str_DatabaseError';
                            $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcards bind_param ' . $dbObj->error;
                        }
                        $stmt->free_result();
                    }
                    $stmt->close();
                }
                else
                {
                    $giftcardsDataRecord['result'] = 'str_DatabaseError';
                    $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcards prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                if ($optionsList!=null)
                {
                    $start = $optionsList['start'];
                    $limit = $optionsList['limit'];
                    $sortBy = $optionsList['sort'];
                    $sortDir = $optionsList['dir'];
                    $searchFields = $optionsList['search']['fields'];
                    $searchQuery = $optionsList['search']['query'];
                    $companyCode = $optionsList['search']['companyCode'];
                }
                else
                {
                    $companyCode = '';
                    $sortBy = '';
                    $sortDir = '';
                    $limit = 0;
                    $start = 0;
                    $searchFields = '';
                    $searchQuery = '';
                }

                /* Searching */
                $typesArray = array();
                $paramArray = array();
                $stmtArray = array();

                if (($searchFields!='')&&($searchQuery!=''))
                {
                    $selectedfields = explode(',', str_replace('"', "", str_replace("]", "", str_replace("[", "", $searchFields))));

                    foreach ($selectedfields as $value)
                    {
                        switch ($value)
                        {
                            case 'giftcardvalue':
                                $value = 'VOUCHERS.discountvalue';
                                break;

                            case 'code':
                                $value = 'VOUCHERS.code';
                                break;

                            case 'name':
                                $value = 'VOUCHERS.name';
                                break;

                            case 'groupcode':
                                $value = 'VOUCHERS.groupcode';
                                break;

                            case 'redeemusername':
                                $value = 'REDEEMUSERS.contactfirstname';
                                $stmtArray[] = '(' . $value . ' LIKE ?)';
                                $paramArray[] = '%' . $searchQuery . '%';
                                $typesArray[] = 's';
                                $value = 'REDEEMUSERS.contactlastname';
                                break;

                            case 'username':
                                $value = 'SELECTEDUSERS.contactfirstname';
                                $stmtArray[] = '(' . $value . ' LIKE ?)';
                                $paramArray[] = '%' . $searchQuery . '%';
                                $typesArray[] = 's';
                                $value = 'SELECTEDUSERS.contactlastname';
                                break;

                            default:
                                $value = '';
                        }

                        if ($value != '')
                        {
                        	$needToBind = true;
                            $stmtArray[] = '(' . $value . ' LIKE ?)';
                            $paramArray[] = '%' . $searchQuery . '%';
                            $typesArray[] = 's';
                        }

                    }

                }


                /* Sorting */
                $customSort = 'code ASC';

                if ($sortBy != '')
                {
                    switch ($sortBy)
                    {
                        case 'redeemusername':
                            $sortBy = 'REDEEMUSERS.contactfirstname ' . $sortDir . ', REDEEMUSERS.contactlastname ' . $sortDir;
                            break;
                        case 'giftcardvalue':
                            $sortBy = 'VOUCHERS.discountvalue ' . $sortDir;
                            break;
                        case 'giftcardcode':
                            $sortBy = 'VOUCHERS.code ' . $sortDir;
                            break;
                        case 'giftcardname':
                            $sortBy = 'VOUCHERS.name ' . $sortDir;
                            break;
                        case 'startdate':
                            $sortBy = 'VOUCHERS.startdate ' . $sortDir;
                            break;
                        case 'enddate':
                            $sortBy = 'VOUCHERS.enddate ' . $sortDir;
                            break;
                        case 'groupcode':
                            $sortBy = 'VOUCHERS.groupcode ' . $sortDir;
                            break;
                        case 'username':
                            $sortBy = 'SELECTEDUSERS.contactfirstname ' . $sortDir . ', SELECTEDUSERS.contactlastname ' . $sortDir;
                            break;
                        case 'redeemeddate':
                            $sortBy = 'VOUCHERS.redeemeddate ' . $sortDir;
                            break;
                        case 'isactive':
                            $sortBy = 'VOUCHERS.active ' . $sortDir;
                            break;
                    }
                    $customSort = ', ' . $sortBy;
                }

                if (($gConstants['optionms']) && ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN))
                {
                    $stmtArray = join(' OR ', $stmtArray);

                    $sql .= ' AND (VOUCHERS.companycode = ? OR VOUCHERS.companycode = "")';

                    $needToBind = true;

                    array_unshift($typesArray, 's');
                    array_unshift($paramArray, $gSession['userdata']['companycode']);
                }
                else
                {
                    if ($companyCode != '')
                    {
                        if ($companyCode == 'GLOBAL')
                        {
                            $paramArray[] = '';
                        }
                        else
                        {
                            $paramArray[] = $companyCode;
                        }

                        $stmtArray[] = '(VOUCHERS.companycode LIKE ?)';
                        $typesArray[] = 's';

                        $needToBind = true;
                    }

                    $stmtArray = join(' OR ', $stmtArray);
                }

                $sql .= ' '. (($stmtArray != '') ? 'AND (' . $stmtArray . ')' : '' ) . ' ORDER BY VOUCHERS.companycode ' . $customSort . ' LIMIT ' . $limit . ' OFFSET ' . $start;

                if ($stmt = $dbObj->prepare($sql))
                {
                    $bindOK = true;

                    if ($needToBind)
                    {
                        $bindOK = DatabaseObj::bindParams($stmt, $typesArray, $paramArray);
                    }

                    if ($bindOK)
                    {
						if ($stmt->bind_result( $id, $companyCode, $code, $name, $startDate, $endDate, $groupCode, $userID, $discountValue, $redeemUserId, $redeemedDate,
                                                    $isActive, $contactFirstName, $contactLastName, $contactEmailAddress, $redeemFirstName, $redeemLastName, $redeemEmailAddress))
                        {
                            if ($stmt->execute())
                            {
                                while ($stmt->fetch())
                                {
                                    $giftcardsDataRecord['data'][$totalCount]['giftcardid']             = $id;
                                    $giftcardsDataRecord['data'][$totalCount]['companycode']            = $companyCode;
                                    $giftcardsDataRecord['data'][$totalCount]['code']                   = $code;
                                    $giftcardsDataRecord['data'][$totalCount]['name']                   = $name;
                                    $giftcardsDataRecord['data'][$totalCount]['startdate']              = $startDate;
                                    $giftcardsDataRecord['data'][$totalCount]['enddate']                = $endDate;
                                    $giftcardsDataRecord['data'][$totalCount]['groupcode']              = $groupCode;
                                    $giftcardsDataRecord['data'][$totalCount]['userid']                 = $userID;
                                    $giftcardsDataRecord['data'][$totalCount]['giftcardvalue']          = $discountValue;
                                    $giftcardsDataRecord['data'][$totalCount]['redeemuserid']           = $redeemUserId;
                                    $giftcardsDataRecord['data'][$totalCount]['isactive']               = $isActive;
                                    $giftcardsDataRecord['data'][$totalCount]['contactfirstname']       = $contactFirstName;
                                    $giftcardsDataRecord['data'][$totalCount]['contactlastname']        = $contactLastName;
                                    $giftcardsDataRecord['data'][$totalCount]['contactemailaddress']    = $contactEmailAddress;
                                    $giftcardsDataRecord['data'][$totalCount]['redeemfirstname']        = $redeemFirstName;
                                    $giftcardsDataRecord['data'][$totalCount]['redeemlastname']         = $redeemLastName;
                                    $giftcardsDataRecord['data'][$totalCount]['redeememailaddress']     = $redeemEmailAddress;
                                    $giftcardsDataRecord['data'][$totalCount]['redeemeddate']           = $redeemedDate;
                                    $totalCount++;
                                }
                            }
                            else
                            {
                                $giftcardsDataRecord['result'] = 'str_DatabaseError';
                                $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcards execute ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $giftcardsDataRecord['result'] = 'str_DatabaseError';
                            $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcards bind_result ' . $dbObj->error;
                        }

                        if (($stmt = $dbObj->prepare("SELECT FOUND_ROWS()")) && ($stmt->bind_result($totalCount)))
                        {
                            if ($stmt->execute())
                            {
                                $stmt->fetch();
                            }
                            else
                            {
                                $giftcardsDataRecord['result'] = 'str_DatabaseError';
                                $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcards execute ' . $dbObj->error;
                            }
                        }

                        $stmt->free_result();
                    }
                    else
                    {
                        $giftcardsDataRecord['result'] = 'str_DatabaseError';
                        $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcards bind_param ' . $dbObj->error;
                    }

                    $stmt->close();
                }
                else
                {
                    $giftcardsDataRecord['result'] = 'str_DatabaseError';
                    $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcards prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
        }
        else
        {
            $giftcardsDataRecord['result'] = 'str_DatabaseError';
            $giftcardsDataRecord['resultparam'] = 'giftcardsGetGiftcards connect ' . $dbObj->error;
        }

        $giftcardsDataRecord['recordcount'] = $totalCount;

        return $giftcardsDataRecord;

    }

    static function listGiftcards()
    {

        global $gSession;

        $itemsArray = array();
        $giftcardData = array();

        $showResultGiftcards = isset($_GET['resultgiftcards']) ? $_GET['resultgiftcards'] : 0;


        if ($showResultGiftcards== '1')
        {
            if (isset($gSession['giftcardcreationresult']))
            {
                $giftcardIDArray = $gSession['giftcardcreationresult'];

                $giftcardData = self::getGiftcards(null, $giftcardIDArray);
            }
        }
        else
        {

            $optionsList['start'] = (integer) $_POST['start'];
            $optionsList['limit'] = (integer) $_POST['limit'];
            $optionsList['sort'] = (isset($_POST['sort'])) ? $_POST['sort'] : '';
            $optionsList['dir'] = (isset($_POST['dir'])) ? $_POST['dir'] : '';
            $optionsList['search']['fields'] = UtilsObj::getPOSTParam('fields');
            $optionsList['search']['query'] = UtilsObj::getPOSTParam('query');
            $optionsList['search']['companyCode']   = (isset($_POST['companycode'])) ? $_POST['companycode'] : '';

            $giftcardData = self::getGiftcards($optionsList);
        }

        return $giftcardData;

    }

    static function giftcardActivate()
    {
        global $gSession;
        $giftcardList = explode(',', $_POST['idlist']);
        $isActive = $_POST['active'];
        $notDeletedGiftcards = array();

        $giftcardData = array();
        $giftcardData['result'] = '';
        $giftcardData['resultparam'] = '';
        $giftcardData['notactive'] = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $giftcardRecords = self::getGiftcardCode($giftcardList,false);

            if ($giftcardRecords['result'] == '')
            {
                foreach($giftcardRecords['data'] as $item)
                {
                    if ($stmt = $dbObj->prepare('UPDATE `VOUCHERS` SET `active` = ? WHERE `id` = ?'))
                    {
                        if ($stmt->bind_param('ii', $isActive, $item['giftcardid']))
                        {
                            if ($stmt->execute())
                            {
                                if ($isActive == 1)
                                {
                                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'GIFTCARD-DEACTIVATE', $item['giftcardid'] . ' ' . $item['code'], 1);
                                }
                                else
                                {
                                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'GIFTCARD-ACTIVATE', $item['giftcardid'] . ' ' . $item['code'], 1);
                                }
                            }
                            else
                            {
                                $giftcardData['result'] = 'str_DatabaseError';
                                $giftcardData['resultparam'] = 'giftcardActivate execute ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $giftcardData['result'] = 'str_DatabaseError';
                            $giftcardData['resultparam'] = 'giftcardActivate bind ' . $dbObj->error;
                        }
                        $stmt->free_result();
                        $stmt->close();
                    }
                    else
                    {
                        $giftcardData['result'] = 'str_DatabaseError';
                        $giftcardData['resultparam'] = 'giftcardActivate prepare ' . $dbObj->error;
                    }
                }
            }
            else
            {
                $giftcardData['result'] = $giftcardRecords['result'];
                $giftcardData['resultparam'] = $giftcardRecords['resultparam'];
            }

            $dbObj->close();
        }
        else
        {
            $giftcardData['result'] = 'str_DatabaseError';
            $giftcardData['resultparam'] = 'giftcardActivate connect ' . $dbObj->error;
        }

        if ($giftcardData['result'] == '')
        {
            $giftcardData['notactive'] = $notDeletedGiftcards;
        }

        return $giftcardData;

    }

    private static function updateGiftCard($pGiftcardData)
    {
        global $gSession;
        global $gConstants;

        $giftcardsDataRecord = array();
        $giftcardsDataRecord['result'] = '';
        $giftcardsDataRecord['resultparam'] = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {

            if ($stmt = $dbObj->prepare('UPDATE `VOUCHERS` SET `companycode` = ?, `type` = ?, `name` = ?, `startdate` = ?, `enddate` = ?, `groupcode` = ?, `userid` = ?,
                `minimumqty` = ?, `maximumqty` = ?, `lockqty` = ?, `repeattype` = ?, `discountsection` = ?, `discounttype` = ?, `discountvalue` = ?, `active` = ? WHERE `id` = ?'))
            {
                if ($stmt->bind_param('sissssiiiisssdii',
                    $pGiftcardData['companycode'],
                    $pGiftcardData['type'],
                    $pGiftcardData['name'],
                    $pGiftcardData['startdate'],
                    $pGiftcardData['enddate'],
                    $pGiftcardData['groupcode'],
                    $pGiftcardData['userid'],
                    $pGiftcardData['minqty'],
                    $pGiftcardData['maxqty'],
                    $pGiftcardData['lockqty'],
                    $pGiftcardData['repeattype'],
                    $pGiftcardData['discountsection'],
                    $pGiftcardData['discounttype'],
                    $pGiftcardData['giftcardvalue'],
                    $pGiftcardData['isactive'],
                    $pGiftcardData['giftcardid']))
                {
                    if ($stmt->execute())
                    {
                        DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'GIFTCARD-UPDATE', $pGiftcardData['giftcardid'] . ' ' . $pGiftcardData['code'], 1);
                    }
                    else
                    {
                        $giftcardsDataRecord['result']  = 'str_DatabaseError';
                        $giftcardsDataRecord['resultparam'] = 'giftcardUpdate execute ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not bind parameters
                    $giftcardsDataRecord['result']  = 'str_DatabaseError';
                    $giftcardsDataRecord['resultparam'] = 'giftcardUpdate bind ' . $dbObj->error;
                }
                $stmt->free_result();
            }
            else
            {
                // could not prepare statement
                $giftcardsDataRecord['result']  = 'str_DatabaseError';
                $giftcardsDataRecord['resultparam'] = 'giftcardUpdate prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $giftcardsDataRecord['result']  = 'str_DatabaseError';
            $giftcardsDataRecord['resultparam']  = 'giftcardUpdate connect ' . $dbObj->error;
        }

        return $giftcardsDataRecord;
    }

    private static function insertGiftCards($pGiftcardDataList)
    {
        $addedRecordsID = array();
        $giftcardData = array();

        $giftcardsDataRecord = array();
        $giftcardsDataRecord['result'] = '';
        $giftcardsDataRecord['resultparam'] = '';
        $giftcardsDataRecord['newrecords'] = array();
        $giftcardsDataRecord['alreadeyadded'] = array();

        foreach($pGiftcardDataList as $giftcardData)
        {
            $giftcardData = self::insertGiftCard($giftcardData);

            if ((!$giftcardData['alreadyadded'])&&($giftcardData['recordid']>0))
            {
                $giftcardsDataRecord['newrecords'][] = $giftcardData['recordid'];
            }
            elseif (($giftcardData['alreadyadded']))
            {
                $giftcardsDataRecord['alreadeyadded'][]= $giftcardData['code'];
            }

            if ($giftcardData['result']!='')
            {
                $giftcardsDataRecord['result'] = $giftcardData['result'];
                $giftcardsDataRecord['resultparam'] = $giftcardData['resultparam'];
            }
        }

        return $giftcardsDataRecord;
    }

    private static function insertGiftCard($pGiftcardData)
    {

        global $gSession;
        global $gConstants;

        $giftcardsDataRecord = array();
        $giftcardsDataRecord['result'] = '';
        $giftcardsDataRecord['resultparam'] = '';
        $giftcardsDataRecord['recordid'] = '';
        $giftcardsDataRecord['code'] = '';
        $giftcardsDataRecord['alreadyadded'] = false;

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('INSERT INTO `VOUCHERS` (`id`, `datecreated`, `companycode`, `code`, `type`, `name`, `startdate`, `enddate`,
                `groupcode`, `userid`, `minimumqty`, `maximumqty`, `lockqty`, `repeattype`, `discountsection`, `discounttype`, `discountvalue`,  `active`)
                VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
            {
                if ($stmt->bind_param('ssissssiiiissssi',
                    $pGiftcardData['companycode'],
                    $pGiftcardData['code'],
                    $pGiftcardData['type'],
                    $pGiftcardData['name'],
                    $pGiftcardData['startdate'],
                    $pGiftcardData['enddate'],
                    $pGiftcardData['groupcode'],
                    $pGiftcardData['userid'],
                    $pGiftcardData['minqty'],
                    $pGiftcardData['maxqty'],
                    $pGiftcardData['lockqty'],
                    $pGiftcardData['repeattype'],
                    $pGiftcardData['discountsection'],
                    $pGiftcardData['discounttype'],
                    $pGiftcardData['giftcardvalue'],
                    $pGiftcardData['isactive']))
                {
                    if ($stmt->execute())
                    {
                        $giftcardsDataRecord['recordid'] = $dbObj->insert_id;

                        DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'GIFTCARD-INSERT', $giftcardsDataRecord['recordid'] . ' ' . $pGiftcardData['code'], 1);
                    }
                    else
                    {
                        // could not execute statement
                        // first check for a duplicate key (voucher code)
                        if ($stmt->errno == 1062)
                        {
                            $giftcardsDataRecord['result'] = 'str_ErrorGiftcardExists';
                            $giftcardsDataRecord['alreadyadded'] = true;
                            $giftcardsDataRecord['code'] = $pGiftcardData['code'];
                        }
                        else
                        {
                            $giftcardsDataRecord['result']  = 'str_DatabaseError';
                            $giftcardsDataRecord['resultparam'] = 'giftcardInsert execute ' . $dbObj->error;
                        }
                    }
                }
                else
                {
                    // could not bind parameters
                    $giftcardsDataRecord['result']  = 'str_DatabaseError';
                    $giftcardsDataRecord['resultparam'] = 'giftcardInsert bind ' . $dbObj->error;
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $giftcardsDataRecord['result']  = 'str_DatabaseError';
                $giftcardsDataRecord['resultparam'] = 'giftcardInsert prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $giftcardsDataRecord['result']  = 'str_DatabaseError';
            $giftcardsDataRecord['resultparam']  = 'giftcardInsert connect ' . $dbObj->error;
        }

        return $giftcardsDataRecord;
    }

    private static function getImportGiftCardData()
    {
        global $gSession;
        global $gConstants;

        $giftcardData = array();

        if ($gConstants['optionms'])
        {
            if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
            {
                $giftcardData['companycode'] = $gSession['userdata']['companycode'];
            }
            elseif ($_POST['hiddenCompanyCode']!='GLOBAL')
                $giftcardData['companycode'] = $_POST['hiddenCompanyCode'];
            else
                $giftcardData['companycode'] = '';
        }
        else
            $giftcardData['companycode'] = '';

        $giftcardData['name'] = $_POST['hiddenName'];
        $giftcardData['startdate'] = $_POST['hiddeStartDate'];
        $giftcardData['enddate'] = $_POST['hiddenEndDate'];

        if ($_POST['hiddenGroupCode']=='ALL')
            $giftcardData['groupcode'] = '';
        else
            $giftcardData['groupcode'] = $_POST['hiddenGroupCode'];

        $giftcardData['userid'] = $_POST['hiddenCustomer'];
        $giftcardData['minqty'] = 1;
        $giftcardData['maxqty'] = 9999;
        $giftcardData['lockqty'] = 0;
        $giftcardData['repeattype'] = 'SINGLE';
        $giftcardData['discountsection'] = 'TOTAL';
        $giftcardData['discounttype'] = 'VALUE';
        $giftcardData['giftcardvalue'] = $_POST['hiddenGiftcardValue'];
        $giftcardData['isactive'] = $_POST['hiddenActive'];
        $giftcardData['type']  = TPX_VOUCHER_TYPE_GIFTCARD;

        return $giftcardData;
    }

    private static function getNewGiftCardData()
    {
        global $gSession;
        global $gConstants;

        $giftcardData = array();

        if ($gConstants['optionms'])
        {

            if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
            {
                $giftcardData['companycode'] = $gSession['userdata']['companycode'];
            }
            elseif ($_POST['companyCombo']!='GLOBAL')
			{
                $giftcardData['companycode'] = $_POST['companyCombo'];
			}
			else
			{
                $giftcardData['companycode'] = '';

			}
		}
        else
		{
            $giftcardData['companycode'] = '';
		}

        $giftcardData['name'] = $_POST['name'];
        $giftcardData['startdate'] = $_POST['startdatevalue'];
        $giftcardData['enddate'] = $_POST['enddatevalue'];

        if (strtoupper($_POST['licenseKeyList']) == 'ALL')
		{
            $giftcardData['groupcode'] = '';
		}
		else
		{
            $giftcardData['groupcode'] = $_POST['licenseKeyList'];
		}

        $giftcardData['userid'] = $_POST['customers'];
        $giftcardData['minqty'] = 1;
        $giftcardData['maxqty'] = 9999;
        $giftcardData['lockqty'] = 0;
        $giftcardData['repeattype'] = 'SINGLE';
        $giftcardData['discountsection'] = 'TOTAL';
        $giftcardData['discounttype'] = 'VALUE';
        $giftcardData['giftcardvalue'] = $_POST['giftcardvalue'];
        $giftcardData['isactive'] = $_POST['isactive'];
        $giftcardData['type']  = TPX_VOUCHER_TYPE_GIFTCARD;

        return $giftcardData;
    }

    static function giftcardAdd()
    {
        $giftcardsDataRecord = array();
        $giftcardsDataRecord['recordid'] = 0;
        $giftcardsDataRecord['result'] = '';
        $giftcardsDataRecord['resultparam'] = '';

        $giftcardInsertRecord = array();

        if ($giftcardsDataRecord['result'] == '')
        {
            $giftcardInsertRecord = self::getNewGiftCardData();

            $giftcardInsertRecord['code'] = strtoupper($_POST['code']);

            if (($giftcardInsertRecord['code']!='') && ($giftcardInsertRecord['name']!=''))
            {
                $giftcardsDataRecord = self::insertGiftCard($giftcardInsertRecord);
            }
        }

        return $giftcardsDataRecord;

    }

    static function buildEditLists(&$pResultArray)
    {
        $userList = array();
        $groupList = array();

        global $gSession, $gConstants;

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			$smartyDefaultObj = SmartyObj::newSmarty('');
            $groupList[] = '["ALL","' . $smartyDefaultObj->get_config_vars('str_LabelAll') . '","ALL"]';

            if (($gConstants['optionms']) && ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN))
            {
                $stmt = $dbObj->prepare('SELECT `groupcode`,`name`, `companycode` FROM `LICENSEKEYS` WHERE (`companycode` = ? OR `companycode` = "" OR `companycode` IS NULL) ORDER BY `groupcode`');
                $bindOK = $stmt->bind_param('s', $gSession['userdata']['companycode']);
            }
            else
            {
                $stmt = $dbObj->prepare('SELECT `groupcode`,`name`,`companycode` FROM `LICENSEKEYS` ORDER BY `groupcode`');
                $bindOK = true;
            }

            if ($bindOK)
            {
                if ($stmt->bind_result($groupCode, $groupName, $companyCode))
                {
                    if ($stmt->execute())
                    {
                        while ($stmt->fetch())
                        {
                            $groupList[] = '["'.$groupCode.'","'.$groupCode . ' - ' . $groupName.'","'.$companyCode.'"]';
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }

            $dbObj->close();
        }

        $pResultArray['groupList'] = $groupList;
    }

    static function displayEdit($pGiftCardID)
    {
        $resultArray = array();

        $giftcardData = self::getGiftcards(null, array($pGiftCardID));

        return $giftcardData['data'][0];
    }

    static function giftcardEdit()
    {
        global $gSession;
        global $gConstants;

        $giftcardsDataRecord = array();
        $giftcardsDataRecord['recordid'] = 0;
        $giftcardsDataRecord['result'] = '';
        $giftcardsDataRecord['resultparam'] = '';

        $giftcardUpdateRecord = array();
        $giftcardUpdateRecord = self::getNewGiftCardData();
        $giftcardUpdateRecord['giftcardid'] = $_POST['giftcardid'];
        $giftcardUpdateRecord['code'] = $_POST['code'];

        if (($giftcardUpdateRecord['code']!='') && ($giftcardUpdateRecord['giftcardid']>0)  && ($giftcardUpdateRecord['name']!=''))
        {
            $giftcardsDataRecord = self::updateGiftCard($giftcardUpdateRecord);
        }

        return $giftcardsDataRecord;

    }

    static function giftcardDelete($pGiftcardList)
    {
        global $gSession;

        $giftcardData = array();
        $giftcardData['result'] = '';
        $giftcardData['resultparam'] = '';
        $giftcardData['notdeleted'] = array();

        $notDeletedGiftcards = array();

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $giftcardRecords = self::getGiftcardCode($pGiftcardList,false);

            if ($giftcardRecords['result']=='')
            {
                foreach($giftcardRecords['data'] as $item)
                {
                    if ($stmt = $dbObj->prepare('DELETE FROM `VOUCHERS` WHERE `id` = ?'))
                    {

                        if ($stmt->bind_param('i', $item['giftcardid']))
                        {
                            if ($stmt->execute())
                            {
                                DatabaseObj::updateActivityLog2($dbObj, $gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'GIFTCARD-DELETE', $item['giftcardid'] . ' ' . $item['code'], 1);
                            }
                            else
                            {
                                $giftcardData['result'] = 'str_DatabaseError';
                                $giftcardData['resultparam'] = 'giftcardDelete execute ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $giftcardData['result'] = 'str_DatabaseError';
                            $giftcardData['resultparam'] = 'giftcardDelete bind ' . $dbObj->error;
                        }
                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                    else
                    {
                        $giftcardData['result'] = 'str_DatabaseError';
                        $giftcardData['resultparam'] = 'giftcardDelete prepare ' . $dbObj->error;
                    }
                }
            }
            else
            {
                $giftcardData['result'] = $giftcardRecords['result'];
                $giftcardData['resultparam'] = $giftcardRecords['resultparam'];
            }

            $dbObj->close();
        }
        else
        {
            $giftcardData['result'] = 'str_DatabaseError';
            $giftcardData['resultparam'] = 'giftcardDelete connect ' . $dbObj->error;
        }

        if ($giftcardData['result'] == '')
        {
            $giftcardData['notdeleted'] = $notDeletedGiftcards;
        }

        return $giftcardData;
    }

    static function giftcardCreate()
    {
        global $gSession;
        global $gConstants;

        $giftcardsDataRecord = array();
        $giftcardsDataRecord['newrecords'] = array();
        $giftcardsDataRecord['alreadeyadded'] = array();
        $giftcardsDataRecord['result'] = '';
        $giftcardsDataRecord['resultparam'] = '';

        $giftcardCreateRecords = array();

        $giftcardCreateRecord = self::getNewGiftCardData();

        $giftcardQty = $_POST['qty'];
        $codePrefix = $_POST['codeprefix'];
        $isRandom = $_POST['israndom'];
        $startNumber = $_POST['startnumber'];

        $giftcardsArray = Array();
        $giftcardIDArray = Array();

        if ($giftcardQty > 3000)
        {
            $giftcardQty = 3000; // double-check, just in case
        }

        $giftcardEndNumber = $startNumber + $giftcardQty - 1;

        if ($giftcardCreateRecord['name'] != '')
        {
            $giftcardCount = 0;

            while ($giftcardCount < $giftcardQty)
            {
                UtilsObj::resetPHPScriptTimeout(30); // just in case

                if ($isRandom == 1)
                {
                    $giftcardCreateRecord['code'] = $codePrefix . strtoupper(UtilsObj::createRandomString(12));
                }
                else
                {
                    $giftcardNumber = str_pad($startNumber, strlen($giftcardEndNumber), '0', STR_PAD_LEFT);

                    if (strpos($codePrefix, '_') === false)
                    {
                         $giftcardCreateRecord['code'] = $codePrefix . $giftcardNumber;
                    }
                    else
                    {
                         $giftcardCreateRecord['code'] = str_replace('_', $giftcardNumber, $codePrefix);
                    }

                    $startNumber++;
                }

                $giftcardCreateRecords[] = $giftcardCreateRecord;

                $giftcardCount++;

            }

            $giftcardsDataRecord = self::insertGiftCards($giftcardCreateRecords);

        }

        $gSession['giftcardcreationresult'] = $giftcardsDataRecord['newrecords'];
        DatabaseObj::updateSession();

        return $giftcardsDataRecord;
    }

    static function giftcardImport()
    {
        global $gSession;
        global $gConstants;

        $textData = '';
        $giftcardCodes = array();
        $giftcardCodeCount = 0;

        $giftcardCodeFile = $_FILES['importcodes']['tmp_name'];
        $giftcardCodeFileType = $_FILES['importcodes']['type'];
        $giftcardCodeFileSize = $_FILES['importcodes']['size'];

        $giftcardsDataRecord = array();
        $giftcardsDataRecord['result'] = '';
        $giftcardsDataRecord['resultparam'] = '';
        $giftcardsDataRecord['alreadyexist'] = array();

        $giftcardImportRecords = array();
        $giftcardImportRecord = array();

        if ($giftcardCodeFileSize > 0)
        {
            //Once file is uploaded read the file
            if (is_uploaded_file($giftcardCodeFile))
            {
                $textData = UtilsObj::readTextFile($giftcardCodeFile);
                $textData = str_replace("\r\n", "\n", $textData);
                $textData = str_replace("\r", "\n", $textData);

                UtilsObj::deleteFile($giftcardCodeFile);

                //assign values to array indexes.
                $giftcardCodes = explode("\n", $textData);
                $giftcardCodeCount = count($giftcardCodes);
            }
        }

        $giftcardImportRecord = self::getImportGiftCardData();

        if ($giftcardImportRecord['name'] != '')
        {
            for ($i = 0; $i < $giftcardCodeCount; $i++)
            {
                UtilsObj::resetPHPScriptTimeout(30); // just in case

                if ($giftcardCodes[$i] != '')
                {
                    $giftcardImportRecord['code'] = preg_replace("/([^A-Z0-9_-])/", '', strtoupper($giftcardCodes[$i]));
                    $giftcardImportRecords[] = $giftcardImportRecord;
                }
            }

            $giftcardsDataRecord = self::insertGiftCards($giftcardImportRecords);
        }

        $gSession['giftcardcreationresult'] = $giftcardsDataRecord['newrecords'];
        DatabaseObj::updateSession();

        return $giftcardsDataRecord;

    }

    static function giftcardExport()
    {
        global $gSession, $gConstants;
        $resultArray = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        $promotionCode = '';
        $giftcardIDArray = array();

        if ((isset($_GET['useCached'])) && ($_GET['useCached'] == 1) && (isset($gSession['giftcardcreationresult'])))
        {
            $giftcardIDArray = $gSession['giftcardcreationresult'];
        }
        else
        {
            if ($dbObj)
            {

                $giftcardType = TPX_VOUCHER_TYPE_GIFTCARD;

                if (($gConstants['optionms']) && ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN))
                {
                    $stmt = $dbObj->prepare('SELECT VOUCHERS.id FROM VOUCHERS WHERE (VOUCHERS.companycode = ? OR VOUCHERS.companycode = "") AND VOUCHERS.type = ? ORDER BY VOUCHERS.id');
                    $bindOK = $stmt->bind_param('si', $gSession['userdata']['companycode'],$giftcardType);
                }
                else
                {
                    $stmt = $dbObj->prepare('SELECT id FROM VOUCHERS WHERE VOUCHERS.type = ? ORDER BY id');
                    $bindOK = $stmt->bind_param('i', $giftcardType);
                }

                if ($stmt->bind_result($id))
                {
                    if ($stmt->execute())
                    {
                        while ($stmt->fetch())
                        {
                            $giftcardIDArray[] = $id;
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
            }
        }

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT VOUCHERS.id, VOUCHERS.code, VOUCHERS.name, VOUCHERS.startdate, VOUCHERS.enddate,
                VOUCHERS.groupcode, VOUCHERS.userid, VOUCHERS.repeattype, VOUCHERS.discountsection, VOUCHERS.discounttype, VOUCHERS.discountvalue,
                VOUCHERS.redeemeduserid, VOUCHERS.redeemeddate, VOUCHERS.active, USERS.contactfirstname, USERS.contactlastname, USERS.emailaddress,
                REDUSERS.contactfirstname, REDUSERS.contactlastname, REDUSERS.emailaddress
                FROM `VOUCHERS`
                LEFT JOIN `USERS` ON (USERS.id = VOUCHERS.userid)
                LEFT JOIN `USERS` AS `REDUSERS` ON (REDUSERS.id = VOUCHERS.redeemeduserid)
                WHERE VOUCHERS.id = ?'))
            {
                $itemCount = count($giftcardIDArray);
                for ($i = 0; $i < $itemCount; $i++)
                {
                    if ($stmt->bind_param('i', $giftcardIDArray[$i]))
                    {
                        if ($stmt->bind_result($id, $code, $name, $startDate, $endDate, $groupCode, $userID, $repeatType, $discountSection, $discountType, $discountValue,
                        $redeemUserID, $redeemedDate, $isActive, $contactFirstName, $contactLastName, $emailAddress, $redeemFirstName, $redeemLastName, $redeemEmailAddress))
                        {
                            if ($stmt->execute())
                            {
                                if ($stmt->fetch())
                                {
                                    $giftcardItem['giftcardid'] = $id;
                                    $giftcardItem['code'] = $code;
                                    $giftcardItem['name'] = $name;
                                    $giftcardItem['startdate'] = $startDate;
                                    $giftcardItem['enddate'] = $endDate;
                                    $giftcardItem['groupcode'] = $groupCode;
                                    $giftcardItem['userid'] = $userID;
                                    $giftcardItem['repeattype'] = $repeatType;
                                    $giftcardItem['discountsection'] = $discountSection;
                                    $giftcardItem['discounttype'] = $discountType;
                                    $giftcardItem['giftcardvalue'] = $discountValue;
                                    $giftcardItem['redeemUserId'] = $redeemUserID;
                                    $giftcardItem['isactive'] = $isActive;
                                    $giftcardItem['usercontactfirstname'] = $contactFirstName;
                                    $giftcardItem['usercontactlastname'] = $contactLastName;
                                    $giftcardItem['useremailaddress'] = $emailAddress;
                                    $giftcardItem['redeemcontactfirstname'] = $redeemFirstName;
                                    $giftcardItem['redeemcontactlastname'] = $redeemLastName;
                                    $giftcardItem['redeememailaddress'] = $redeemEmailAddress;
                                    $giftcardItem['redeemeddate'] = $redeemedDate;

                                    array_push($resultArray, $giftcardItem);
                                }
                            }
                        }
                    }
                    $stmt->free_result();
                }
                $stmt->close();
            }
            $dbObj->close();
        }

        return $resultArray;
    }

    static function clearSavedGiftcardList()
    {
        global $gSession;

        $gSession['giftcardcreationresult'] = null;
        DatabaseObj::updateSession();
    }

}

?>
