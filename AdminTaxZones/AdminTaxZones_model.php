<?php
require_once('../Utils/UtilsDatabase.php');

class AdminTaxZones_model
{

    static function getGridData()
    {
        global $gSession;

        $resultArray = Array();
        $countryArray = UtilsAddressObj::getCountryList();

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            switch($gSession['userdata']['usertype'])
            {
                case TPX_LOGIN_SYSTEM_ADMIN:
                    $stmt = $dbObj->prepare('SELECT `id`, `companycode`, `code`, `localcode`, `name`, `taxlevel1`, `taxlevel2`, `taxlevel3`, `taxlevel4`, `taxlevel5`, `shippingtaxcode`, `countrycodes` FROM `TAXZONES` ORDER BY `companycode`, `code`');
                    $bindOK = true;
                    break;
                case TPX_LOGIN_COMPANY_ADMIN:
                    $stmt = $dbObj->prepare('SELECT `id`, `companycode`, `code`, `localcode`, `name`, `taxlevel1`, `taxlevel2`, `taxlevel3`, `taxlevel4`, `taxlevel5`, `shippingtaxcode`, `countrycodes` FROM `TAXZONES` WHERE `companycode` = ? OR `companycode` = "" ORDER BY `companycode`, `code`');
                    $bindOK = $stmt->bind_param('s', $gSession['userdata']['companycode']);
                    break;
            }

            if ($stmt)
            {
                if ($bindOK)
                {
                    if ($stmt->bind_result($taxZoneID, $taxZoneCompanyCode, $taxZoneCode, $taxZoneLocalCode, $taxZoneName, $taxlevel1, $taxlevel2, $taxlevel3, $taxlevel4, $taxlevel5, $shippingTaxCode,
                                    $countryCodes))
                    {
                        if ($stmt->execute())
                        {
                            $combinedList = UtilsAddressObj::getCombinedCountryRegionList(true, $gSession['browserlanguagecode']);

                            while($stmt->fetch())
                            {
                                $taxCountryList = UtilsAddressObj::getTaxCountryListFromCodes($countryCodes, $combinedList);

                                $item['id'] = $taxZoneID;
                                $item['companycode'] = $taxZoneCompanyCode;
                                $item['code'] = $taxZoneCode;
                                $item['localcode'] = $taxZoneLocalCode;
                                $item['name'] = $taxZoneName;
                                $item['taxlevel1'] = $taxlevel1;
                                $item['taxlevel2'] = $taxlevel2;
                                $item['taxlevel3'] = $taxlevel3;
                                $item['taxlevel4'] = $taxlevel4;
                                $item['taxlevel5'] = $taxlevel5;
                                $item['shippingtaxcode'] = $shippingTaxCode;
                                $item['countrycodes'] = $taxCountryList;
                                array_push($resultArray, $item);
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
        return $resultArray;
    }

    static function taxZoneAdd()
    {
        global $gSession;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $recordID = 0;
        $taxZoneCompanyCode = '';
        $taxZoneCode = '';

        $taxZoneLocalCode = strtoupper($_POST['code']);

        switch($gSession['userdata']['usertype'])
        {
            case TPX_LOGIN_SYSTEM_ADMIN:
                $taxZoneCode = $taxZoneLocalCode;
                if ($gConstants['optionms'])
                {
                    $taxZoneCompanyCode = $_POST['company'];

                    if ($taxZoneCompanyCode == 'GLOBAL')
                    {
                        $taxZoneCompanyCode = '';
                        $taxZoneCode = $taxZoneLocalCode;
                    }
                    else
                    {
                        $taxZoneCode = $taxZoneCompanyCode . '.' . $taxZoneLocalCode;
                    }
                }
                break;
            case TPX_LOGIN_COMPANY_ADMIN:

                $isRestOfWorld = $_POST['isrestofworld'];
                $taxZoneCompanyCode = $gSession['userdata']['companycode'];

                $taxZoneCode = $taxZoneCompanyCode . '.' . $taxZoneLocalCode;

                if ($isRestOfWorld == 1)
                {
                    $taxZoneCode = $taxZoneCompanyCode . '.';
                    $taxZoneLocalCode = '';
                }

                break;
        }

        $taxZoneName = $_POST['name'];
        $taxlevel1 = $_POST['taxlevel1'];
        $taxlevel2 = $_POST['taxlevel2'];
        $taxlevel3 = $_POST['taxlevel3'];
        $taxlevel4 = $_POST['taxlevel4'];
        $taxlevel5 = $_POST['taxlevel5'];
        $shippingTaxCode = $_POST['shippingtaxcode'];
        $countryCodes = $_POST['countrycodes'];

        if (($taxZoneName != '') && ($taxlevel1 != '') && ($shippingTaxCode != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('INSERT INTO `TAXZONES` VALUES (0, now(),?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
                {
                    if ($stmt->bind_param('sssssssssss', $taxZoneCompanyCode, $taxZoneCode, $taxZoneLocalCode, $taxZoneName,
                                    $taxlevel1, $taxlevel2, $taxlevel3, $taxlevel4, $taxlevel5,
                                    $shippingTaxCode, $countryCodes))
                    {
                        if ($stmt->execute())
                        {
                            $recordID = $dbObj->insert_id;

                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                    $gSession['username'], 0, 'ADMIN', 'TAXZONE-ADD', $recordID . ' ' . $taxZoneLocalCode, 1);
                        }
                        else
                        {
                            // could not execute statement
                            // first check for a duplicate key (tax zone code)
                            if ($stmt->errno == 1062)
                            {
                                $result = 'str_ErrorTaxZoneExists';
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'taxZonesAdd execute ' . $dbObj->error;
                            }
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'taxZonesAdd bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'taxZonesAdd prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'taxZonesAdd connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $recordID;
        $resultArray['companycode'] = $taxZoneCompanyCode;
        $resultArray['code'] = $taxZoneCode;
        $resultArray['name'] = $taxZoneName;
        $resultArray['taxlevel1'] = $taxlevel1;
        $resultArray['taxlevel2'] = $taxlevel2;
        $resultArray['taxlevel3'] = $taxlevel3;
        $resultArray['taxlevel4'] = $taxlevel4;
        $resultArray['taxlevel5'] = $taxlevel5;
        $resultArray['shippingtaxcode'] = $shippingTaxCode;
        $resultArray['countrycodes'] = $countryCodes;

        return $resultArray;
    }

    static function taxZoneEdit()
    {
        global $gSession;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $taxZoneCompanyCode = '';

        $taxZoneID = $_GET['id'];
        //Localcode
        $taxZoneCode = strtoupper($_POST['code']);
        //Actual code for record used to update
        $taxZoneCodeMain = strtoupper($_POST['taxzonecodemain']);
        $taxZoneName = $_POST['name'];

        if ($_POST['isdefault'] == 1)
        {
            $taxZoneCode = '';
        }

        if ($gConstants['optionms'])
        {
            $taxZoneCompanyCode = $_POST['company'];

            if ($taxZoneCompanyCode == 'GLOBAL')
            {
                $taxZoneCompanyCode = '';
            }
        }
        else
        {
            $taxZoneCompanyCode = '';
        }

        $taxlevel1 = $_POST['taxlevel1'];
        $taxlevel2 = $_POST['taxlevel2'];
        $taxlevel3 = $_POST['taxlevel3'];
        $taxlevel4 = $_POST['taxlevel4'];
        $taxlevel5 = $_POST['taxlevel5'];
        $shippingTaxCode = $_POST['shippingtaxcode'];
        $countryCodes = $_POST['countrycodes'];

        if (($taxZoneName != '') && ($taxlevel1 != '') && ($shippingTaxCode != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('UPDATE `TAXZONES` SET `companycode` = ?, `name` = ?, `taxlevel1` = ?, `taxlevel2` = ?, `taxlevel3` = ?, `taxlevel4` = ?, `taxlevel5` = ?, `shippingtaxcode` = ?, `countrycodes` = ? WHERE `code` = ?'))
                {
                    if ($stmt->bind_param('ssssssssss', $taxZoneCompanyCode, $taxZoneName, $taxlevel1, $taxlevel2, $taxlevel3, $taxlevel4,
                                    $taxlevel5, $shippingTaxCode, $countryCodes, $taxZoneCodeMain))
                    {
                        if ($stmt->execute())
                        {
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                    $gSession['username'], 0, 'ADMIN', 'TAXZONE-UPDATE', $taxZoneID . ' ' . $taxZoneCodeMain, 1);
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'taxZonesEdit execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'taxZonesEdit bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'taxZonesEdit prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'taxZonesEdit connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $taxZoneID;
        $resultArray['companycode'] = $taxZoneCompanyCode;
        $resultArray['code'] = $taxZoneCode;
        $resultArray['name'] = $taxZoneName;
        $resultArray['taxlevel1'] = $taxlevel1;
        $resultArray['taxlevel2'] = $taxlevel2;
        $resultArray['taxlevel3'] = $taxlevel3;
        $resultArray['taxlevel4'] = $taxlevel4;
        $resultArray['taxlevel5'] = $taxlevel5;
        $resultArray['shippingtaxcode'] = $shippingTaxCode;
        $resultArray['countrycodes'] = $countryCodes;

        return $resultArray;
    }

    static function taxZoneDelete()
    {
        global $gSession;

        $resutArray = Array();
        $result = '';
        $taxZonesDeleted = Array();
        $allDeleted = 1;

        $taxZoneIDList = explode(',', $_POST['idlist']);
        $taxZoneIDListCount = count($taxZoneIDList);
        $taxZoneCodeList = explode(',', $_POST['codelist']);

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('DELETE FROM `TAXZONES` WHERE `id` = ?'))
            {
                for($i = 0; $i < $taxZoneIDListCount; $i++)
                {
                    if ($stmt->bind_param('i', $taxZoneIDList[$i]))
                    {
                        if ($stmt->execute())
                        {
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                    $gSession['username'], 0, 'ADMIN', 'TAXZONE-DELETE', $taxZoneIDList[$i] . ' ' . $taxZoneCodeList[$i], 1);
                            array_push($taxZonesDeleted, $taxZoneIDList[$i]);
                        }
                        else
                        {
                            $allDeleted = 0;
                            $result = 'str_DatabaseError';
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }
        $resultArray['alldeleted'] = $allDeleted;
        $resultArray['taxzonesids'] = $taxZonesDeleted;
        $resultArray['result'] = $result;

        return $resultArray;
    }

    static function displayEdit($pID)
    {
        $taxZoneID = 0;
        $taxZoneCode = '';
        $taxZoneName = '';
        $shippingTaxCode = '';
        $countryCodes = '';
        $taxZoneCompanyCode = '';
        $taxZoneLocalCode = '';
        $taxlevel1 = '';
        $taxlevel2 = '';
        $taxlevel3 = '';
        $taxlevel4 = '';
        $taxlevel5 = '';
        $companyHasROW = $_GET['companyHasROW'];

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $stmt = $dbObj->prepare('SELECT `id`, `companycode`, `code`, `localcode`, `name`,
                                        `taxlevel1`, `taxlevel2`, `taxlevel3`, `taxlevel4`, `taxlevel5`, `shippingtaxcode`, `countrycodes`
                                        FROM `TAXZONES`
                                        WHERE `id` = ?');
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
                                if ($stmt->bind_result($taxZoneID, $taxZoneCompanyCode, $taxZoneCode, $taxZoneLocalCode, $taxZoneName,
                                                $taxlevel1, $taxlevel2, $taxlevel3, $taxlevel4, $taxlevel5,
                                                $shippingTaxCode, $countryCodes))
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

        $resultArray['id'] = $taxZoneID;
        $resultArray['companycode'] = $taxZoneCompanyCode;
        $resultArray['code'] = $taxZoneCode;
        $resultArray['localcode'] = $taxZoneLocalCode;
        $resultArray['name'] = $taxZoneName;
        $resultArray['level1'] = $taxlevel1;
        $resultArray['level2'] = $taxlevel2;
        $resultArray['level3'] = $taxlevel3;
        $resultArray['level4'] = $taxlevel4;
        $resultArray['level5'] = $taxlevel5;
        $resultArray['shippingcode'] = $shippingTaxCode;
        $resultArray['countrycode'] = $countryCodes;
        $resultArray['companyhasrow'] = $companyHasROW;

        return $resultArray;
    }

}
?>
