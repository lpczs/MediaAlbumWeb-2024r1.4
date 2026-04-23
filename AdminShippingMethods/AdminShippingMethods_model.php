<?php
require_once('../Utils/UtilsDatabase.php');

class AdminShippingMethods_model
{

    static function getGridData()
    {
        return DatabaseObj::getShippingMethodsList();
    }

    static function shippingMethodAdd()
    {
        global $gSession;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $recordID = 0;
        $allowGroupingByCountry = 1;
        $allowGroupingByRegion = 1;
		$showStoreListOnOpen = 1;
        $assetID = 0;

        $shippingMethodCode = strtoupper($_POST['code']);
        $shippingMethodName = html_entity_decode($_POST['name'], ENT_QUOTES);
        $requiresDelivery = $_POST['requiresdelivery'];
        $useDefaultBillingAddress = $_POST['usedefaultbillingaddress'];
        $useDefaultShippingAddress = $_POST['usedefaultshippingaddress'];
        $canModifyContactDetails = $_POST['canmodifycontactdetails'];
        $requiresDelivery = $_POST['requiresdelivery'];
        $isDefault = $_POST['isdefault'];
        $orderValueRange = $_POST['ordervaluerange'];
        $orderMinValue = $_POST['orderminvalue'];
        $orderMaxValue = $_POST['ordermaxvalue'];
        $orderValueIncludesDiscount = $_POST['ordervalueincludesdiscount'];

        if ($gConstants['optioncfs'])
        {
            $collectFromStore = $_POST['collectfromstore'];
            $storeGroupLabel = html_entity_decode($_POST['storegrouplabel'], ENT_QUOTES);
			$showStoreListOnOpen = $_POST['showstorelistonopen'];
            $allowGroupingByCountry = $_POST['allowgroupingbycountry'];
            $allowGroupingByRegion = $_POST['allowgroupingbyregion'];
            $allowGroupingByStoreGroup = $_POST['allowgroupingbystoregroup'];

            if ($allowGroupingByStoreGroup == 0)
            {
                $storeGroupLabel = '';
            }
        }
        else
        {
            $collectFromStore = 0;
            $storeGroupLabel = '';
            $allowGroupingByCountry = 0;
            $allowGroupingByRegion = 0;
            $allowGroupingByStoreGroup = 0;
        }

        if (($shippingMethodCode != '') && ($shippingMethodName != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if (($result == '') && ($gConstants['optioncfs']))
                {
                    $logoUpdate = $_POST['logoupdate'];
                    $logoRemove = $_POST['logoremove'];

                    if ($logoUpdate == '1')
                    {
                        $logoPath = $gSession['previewpath'];
                        $logoType = $gSession['previewtype'];
                    }

                    if ($logoRemove == '1')
                    {
                        $logoPath = '';
                        $logoType = '';
                    }

                    if (($logoUpdate == '1') || ($logoRemove == '1'))
                    {
                        $assetName = 'COLLECT FROM STORE IMAGE FOR SHIPPINGMETHOD ' . $shippingMethodCode;
                        $result1 = DatabaseObj::updatePreviewImage($assetID, $logoPath, $logoType, $assetName);
                        $result = $result1['result'];
                        $resultParam = $result1['resultparam'];
                        $assetID = $result1['assetid'];
                    }
                }

                if ($stmt = $dbObj->prepare('INSERT INTO `SHIPPINGMETHODS` VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
                {
                    if ($stmt->bind_param('ssiiiisddiiisiiiii', $shippingMethodCode, $shippingMethodName, $useDefaultBillingAddress,
                                    $useDefaultShippingAddress, $canModifyContactDetails, $requiresDelivery, $orderValueRange,
                                    $orderMinValue, $orderMaxValue, $orderValueIncludesDiscount, $isDefault, $collectFromStore,
                                    $storeGroupLabel, $allowGroupingByStoreGroup, $allowGroupingByCountry, $allowGroupingByRegion,
                                    $showStoreListOnOpen, $assetID))
                    {
                        if ($stmt->execute())
                        {
                            $recordID = $dbObj->insert_id;

                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                    $gSession['username'], 0, 'ADMIN', 'SHIPPINGMETHOD-ADD', $recordID . ' ' . $shippingMethodCode, 1);

                            // update the default status for other shipping methods
                            if ($isDefault == 1)
                            {
                                $stmt->free_result();
                                $stmt->close();
                                $stmt = null;

                                if ($stmt = $dbObj->prepare('UPDATE `SHIPPINGMETHODS` SET `default` = 0 WHERE `code` <> ?'))
                                {
                                    if ($stmt->bind_param('s', $shippingMethodCode))
                                    {
                                        $stmt->execute();
                                    }
                                }
                            }
                        }
                        else
                        {
                            // could not execute statement
                            // first check for a duplicate key (shipping method code)
                            if ($stmt->errno == 1062)
                            {
                                $result = 'str_ErrorShippingMethodExists';
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'shippingMethodAdd execute ' . $dbObj->error;
                            }
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'shippingMethodAdd bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'shippingMethodAdd prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'shippingMethodAdd connect ' . $dbObj->error;
            }
        }

        $gSession['shippingmethodlogopath'] = '';
        $gSession['shippingmethodlogotype'] = '';

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $recordID;
        $resultArray['code'] = $shippingMethodCode;
        $resultArray['name'] = $shippingMethodName;
        $resultArray['usedefaultbillingaddress'] = $useDefaultBillingAddress;
        $resultArray['usedefaultshippingaddress'] = $useDefaultShippingAddress;
        $resultArray['canmodifycontactdetails'] = $canModifyContactDetails;
        $resultArray['requiresdelivery'] = $requiresDelivery;
        $resultArray['ordervaluerange'] = $orderValueRange;
        $resultArray['orderminvalue'] = $orderMinValue;
        $resultArray['ordermaxvalue'] = $orderMaxValue;
        $resultArray['ordervalueincludesdiscount'] = $orderValueIncludesDiscount;
        $resultArray['isdefault'] = $isDefault;
        $resultArray['collectfromstore'] = $collectFromStore;
        $resultArray['assetid'] = $assetID;

        return $resultArray;
    }

    static function displayEdit($pID)
    {
        $resultArray = Array();

        $shippingMethodID = 0;
        $shippingMethodCode = '';
        $shippingMethodName = '';
        $useDefaultBillingAddress = 0;
        $useDefaultShippingAddress = 0;
        $canModifyContactDetails = 0;
        $requiresDelivery = 0;
        $isDefault = 0;
        $collectFromStore = 0;
        $siteGroupLabel = '';
        $allowGroupingByStoreGroupName = 0;
        $orderValueType = 0;
        $orderMinValue = 0;
        $orderMaxValue = 0;
        $orderValueIncludesDiscount = 0;
        $allowGroupingByCountryName = 0;
        $allowGroupingByRegionName = 0;
		$showStoreListOnOpen = 1;
        $assetid = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $stmt = $dbObj->prepare('SELECT `id`, `code`, `name`, `usedefaultbillingaddress`, `usedefaultshippingaddress`,
                                        `canmodifycontactdetails`, `requiresdelivery`, `ordervaluetype`, `orderminimumvalue`,
                                        `ordermaximumvalue`, `ordervalueincludesdiscount`, `default`, `collectfromstore`,
                                        `sitegrouplabel`, `allowgroupingbycountry`, `allowgroupingbyregion`,
                                        `allowgroupingbystoregroupname`, `showstorelistonopen`, `assetid`
                                    FROM `SHIPPINGMETHODS`
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
                                if ($stmt->bind_result($shippingMethodID, $shippingMethodCode, $shippingMethodName,
                                                $useDefaultBillingAddress, $useDefaultShippingAddress, $canModifyContactDetails,
                                                $requiresDelivery, $orderValueType, $orderMinValue, $orderMaxValue,
                                                $orderValueIncludesDiscount, $isDefault, $collectFromStore, $siteGroupLabel,
                                                $allowGroupingByCountryName, $allowGroupingByRegionName, $allowGroupingByStoreGroupName,
                                                $showStoreListOnOpen, $assetid))
                                {
                                    if (!$stmt->fetch())
                                    {
                                        $error = 'displayEdit fetch ' . $dbObj->error;
                                    }
                                }
                                else
                                {
                                    $error = 'displayEdit bind result ' . $dbObj->error;
                                }
                            }
                        }
                        else
                        {
                            $error = 'displayEdit store result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = 'displayEdit execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = 'displayEdit bind params ' . $dbObj->error;
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $error = 'displayEdit prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }

        $resultArray['id'] = $shippingMethodID;
        $resultArray['code'] = $shippingMethodCode;
        $resultArray['name'] = $shippingMethodName;
        $resultArray['usedefaultbillingaddress'] = $useDefaultBillingAddress;
        $resultArray['usedefaultshippingaddress'] = $useDefaultShippingAddress;
        $resultArray['canmodifycontactdetails'] = $canModifyContactDetails;
        $resultArray['requiresdelivery'] = $requiresDelivery;
        $resultArray['ordervaluetype'] = $orderValueType;
        $resultArray['orderminvalue'] = $orderMinValue;
        $resultArray['ordermaxvalue'] = $orderMaxValue;
        $resultArray['ordervalueincludesdiscount'] = $orderValueIncludesDiscount;
        $resultArray['isdefault'] = $isDefault;
        $resultArray['collectfromstore'] = $collectFromStore;
        $resultArray['sitegrouplabel'] = $siteGroupLabel;

        $resultArray['allowgroupingbycountryname'] = $allowGroupingByCountryName;
        $resultArray['allowgroupingbyregionname'] = $allowGroupingByRegionName;
        $resultArray['allowgroupingbystoregroupname'] = $allowGroupingByStoreGroupName;
		$resultArray['showstorelistonopen'] = $showStoreListOnOpen;
        $resultArray['assetid'] = $assetid;

        return $resultArray;
    }

    static function shippingMethodEdit()
    {
        global $gSession;
        global $gConstants;

        $result = '';
        $resultParam = '';

        $shippingMethodID = $_GET['id'];
        $shippingMethodCode = strtoupper($_POST['code']);
        $shippingMethodName = html_entity_decode($_POST['name'], ENT_QUOTES);
        $useDefaultBillingAddress = $_POST['usedefaultbillingaddress'];
        $useDefaultShippingAddress = $_POST['usedefaultshippingaddress'];
        $canModifyContactDetails = $_POST['canmodifycontactdetails'];
        $requiresDelivery = $_POST['requiresdelivery'];
        $isDefault = $_POST['isdefault'];
        $orderValueRange = $_POST['ordervaluerange'];
        $orderMinValue = $_POST['orderminvalue'];
        $orderMaxValue = $_POST['ordermaxvalue'];
        $orderValueIncludesDiscount = $_POST['ordervalueincludesdiscount'];
        $assetID = $_POST['assetid'];

        if ($gConstants['optioncfs'])
        {
            $collectFromStore = $_POST['collectfromstore'];
            $storeGroupLabel = html_entity_decode($_POST['storegrouplabel'], ENT_QUOTES);
			$showStoreListOnOpen = $_POST['showstorelistonopen'];
            $allowGroupingByCountry = $_POST['allowgroupingbycountry'];
            $allowGroupingByRegion = $_POST['allowgroupingbyregion'];
            $allowGroupingByStoreGroup = $_POST['allowgroupingbystoregroup'];
            $logoUpdate = $_POST['logoupdate'];
            $logoRemove = $_POST['logoremove'];

            if ($allowGroupingByStoreGroup == 0)
            {
                $storeGroupLabel = '';
            }
        }
        else
        {
            $collectFromStore = 0;
            $storeGroupLabel = '';
            $allowGroupingByCountry = 0;
            $allowGroupingByRegion = 0;
            $allowGroupingByStoreGroup = 0;
			$showStoreListOnOpen = 1;
        }

        if ($shippingMethodID > 0)
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if (($result == '') && ($gConstants['optioncfs']))
                {
                    $logoUpdate = $_POST['logoupdate'];
                    $logoRemove = $_POST['logoremove'];

                    if ($logoUpdate == '1')
                    {
                        $logoPath = $gSession['previewpath'];
                        $logoType = $gSession['previewtype'];

                        $assetName = 'COLLECT FROM STORE IMAGE FOR SHIPPINGMETHOD ' . $shippingMethodCode;
                        $result1 = DatabaseObj::updatePreviewImage($assetID, $logoPath, $logoType, $assetName);
                        $result = $result1['result'];
                        $resultParam = $result1['resultparam'];
                        $assetID = $result1['assetid'];
                    }

                    if ($logoRemove == '1')
                    {
                        $logoPath = '';
                        $logoType = '';
                        $result1 = DatabaseObj::deleteAssetRecord($assetID);

                        if ($result1['result'] == '')
                        {
                            $assetID = 0;
                        }
                    }
                }

                if ($stmt = $dbObj->prepare('UPDATE `SHIPPINGMETHODS` SET `name` = ?, `usedefaultbillingaddress` = ?, `usedefaultshippingaddress` = ?,
						`canmodifycontactdetails` = ?, `requiresdelivery` = ?, `default` = ?, `ordervaluetype` = ?,
						`orderminimumvalue` = ?, `ordermaximumvalue` = ?, `ordervalueincludesdiscount` = ?, `collectfromstore` = ?,
						`sitegrouplabel` = ?, `allowgroupingbycountry` = ?, `allowgroupingbyregion` = ?, `allowgroupingbystoregroupname` = ?,
						`showstorelistonopen` = ?,  `assetid` = ? WHERE `id` = ?'))
                {
                    if ($stmt->bind_param('siiiiisddiisiiiiii', $shippingMethodName, $useDefaultBillingAddress, $useDefaultShippingAddress,
                                    $canModifyContactDetails, $requiresDelivery, $isDefault, $orderValueRange, $orderMinValue,
                                    $orderMaxValue, $orderValueIncludesDiscount, $collectFromStore, $storeGroupLabel,
                                    $allowGroupingByCountry, $allowGroupingByRegion, $allowGroupingByStoreGroup, $showStoreListOnOpen,
                                    $assetID, $shippingMethodID))
                    {
                        if ($stmt->execute())
                        {
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                    $gSession['username'], 0, 'ADMIN', 'SHIPPINGMETHOD-UPDATE',
                                    $shippingMethodID . ' ' . $shippingMethodCode, 1);

                            // if we have updated the shipping method update the default status for the shipping methods
                            if ($isDefault == 1)
                            {
                                $stmt->free_result();
                                $stmt->close();
                                $stmt = null;

                                if ($stmt = $dbObj->prepare('UPDATE `SHIPPINGMETHODS` SET `default` = 0 WHERE `id` <> ?'))
                                {
                                    if ($stmt->bind_param('i', $shippingMethodID))
                                    {
                                        $stmt->execute();
                                    }
                                }
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'shippingMethodEdit execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'shippingMethodEdit bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'shippingMethodEdit prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'shippingMethodEdit connect ' . $dbObj->error;
            }
        }

        $gSession['shippingmethodlogopath'] = '';
        $gSession['shippingmethodlogotype'] = '';


        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $shippingMethodID;
        $resultArray['code'] = $shippingMethodCode;
        $resultArray['name'] = $shippingMethodName;
        $resultArray['usedefaultbillingaddress'] = $useDefaultBillingAddress;
        $resultArray['usedefaultshippingaddress'] = $useDefaultShippingAddress;
        $resultArray['canmodifycontactdetails'] = $canModifyContactDetails;
        $resultArray['requiresdelivery'] = $requiresDelivery;
        $resultArray['isdefault'] = $isDefault;
        $resultArray['assetid'] = $assetID;

        return $resultArray;
    }

    static function shippingMethodDelete()
    {
        global $gSession;

        $resutArray = Array();
        $result = '';
        $resultParam = '';
        $allDeleted = 1;
        $canDelete = false;

        $shippingMethodIDList = explode(',', $_POST['idlist']);
        $shippingMethodIDCount = count($shippingMethodIDList);
        $shippingMethodCodeList = explode(',', $_POST['codelist']);

        $shippingMethodNotUsed = Array();
        $shippingMethodsDeleted = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            for ($i = 0; $i < $shippingMethodIDCount; $i++)
            {
                // first make sure the shipping method hasn't been used
                if ($stmt = $dbObj->prepare('SELECT `id` FROM `SHIPPINGRATES` WHERE `shippingmethodcode` = ?'))
                {
                    if ($stmt->bind_param('s', $shippingMethodCodeList[$i]))
                    {
                        if ($stmt->bind_result($recordID))
                        {
                            if ($stmt->execute())
                            {
                                if ($stmt->fetch())
                                {
                                    $result = 'str_ErrorUsedInShippingRates';
                                    $allDeleted = 0;
                                    $canDelete = false;
                                }
                                else
                                {
                                    $canDelete = true;
                                    $item['id'] = $shippingMethodIDList[$i];
                                    $item['code'] = $shippingMethodCodeList[$i];
                                    array_push($shippingMethodNotUsed, $item);
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
                    if ($stmt = $dbObj->prepare('SELECT `id` FROM `ORDERSHIPPING` WHERE `shippingmethodcode` = ?'))
                    {
                        if ($stmt->bind_param('s', $shippingMethodCodeList[$i]))
                        {
                            if ($stmt->bind_result($recordID))
                            {
                                if ($stmt->execute())
                                {
                                    if ($stmt->fetch())
                                    {
                                        $result = 'str_ErrorUsedInOrder';
                                        $allDeleted = 0;
                                        $canDelete = false;
                                    }
                                    else
                                    {
                                        $canDelete = true;
                                        $item['id'] = $shippingMethodIDList[$i];
                                        $item['code'] = $shippingMethodCodeList[$i];
                                        array_push($shippingMethodNotUsed, $item);
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
                    if ($stmt = $dbObj->prepare('DELETE FROM `SHIPPINGMETHODS` WHERE `id` = ?'))
                    {
                        if ($stmt->bind_param('i', $shippingMethodIDList[$i]))
                        {
                            if ($stmt->execute())
                            {
                                DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                        $gSession['username'], 0, 'ADMIN', 'SHIPPINGMETHOD-DELETE',
                                        $shippingMethodIDList[$i] . ' ' . $shippingMethodCodeList[$i], 1);
                                array_push($shippingMethodsDeleted, $shippingMethodIDList[$i]);
                            }
                        }
                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                }
            }
        }

        $resultArray['alldeleted'] = $allDeleted;
        $resultArray['shippingmethodids'] = $shippingMethodsDeleted;
        $resultArray['result'] = $result;

        return $resultArray;
    }

    /**
     * Updates the logo image of a shipping method.
     *
     * If image data is empty or clearLogo flag is set, the image will be cleared.
     *
     * POST parameters
     *
     * $pShippingMethodCode	= shipping method code
     * $pLogoPath			= path to temporary file
     * $pLogoType			= image type of temporary file
     *
     * @since Version 3.0.0
     * @version 3.0.0
     * @author Steffen Haugk
     *
     * @return array
     * result - error
     * resultParam - extended error message
     */
    static function updateLogo($pShippingMethodCode, $pLogoPath, $pLogoType)
    {
        global $gSession;

        $result = '';
        $resultParam = '';
        $width = 0;
        $height = 0;
        $logoData = '';
        $logoSize = 0;

        if ($pShippingMethodCode != '')
        {
            if ($pLogoPath != '')
            {
                list($width, $height) = getimagesize($pLogoPath);
                $logoSize = filesize($pLogoPath);

                // process an image file which has been uploaded via the browser

                $validImageTypes = Array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png');

                if ($logoSize > 0)
                {
                    // make sure that the file is a valid type
                    if (in_array(strtolower($pLogoType), $validImageTypes))
                    {
                        // read the image data
                        $fp = fopen($pLogoPath, 'rb');
                        if ($fp)
                        {
                            $logoData = fread($fp, $logoSize);
                            fclose($fp);
                        }
                        else
                        {
                            $logoSize = 0;
                        }
                    }
                    else
                    {
                        $result = 'str_ErrorUploadInvalidFileType';
                    }

                    // remove the temp upload file
                    UtilsObj::deleteFile($pLogoPath);
                }
            }

            if ($result == '')
            {
                $dbObj = DatabaseObj::getGlobalDBConnection();
                if ($dbObj)
                {
                    if ($stmt = $dbObj->prepare('UPDATE `SHIPPINGMETHODS` SET `storelocatorlogotype` = ?, `storelocatorlogo` = ? , `storelocatorlogowidth` = ? , `storelocatorlogoheight` = ? WHERE `code` = ?'))
                    {
                        if ($stmt->bind_param('ssiis', $pLogoType, $logoData, $width, $height, $pShippingMethodCode))
                        {
                            if ($stmt->execute())
                            {

                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'uploadLogo execute ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            // could not bind parameters
                            $result = 'str_DatabaseError';
                            $resultParam = 'uploadLogo bind ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not prepare statement
                        $result = 'str_DatabaseError';
                        $resultParam = 'uploadLogo prepare ' . $dbObj->error;
                    }

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;

                    if ($result == '')
                    {
                        DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                $gSession['username'], 0, 'ADMIN', 'SHIPPINGMETHOD-UPDATELOGO', $pShippingMethodCode, 1);
                    }
                    $dbObj->close();
                }
                else
                {
                    // could not open database connection
                    $result = 'str_DatabaseError';
                    $resultParam = 'coverAdd connect ' . $dbObj->error;
                }
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
    }

    /**
     * Updates the logo image of a shipping method.
     *
     * If image data is empty or clearLogo flag is set, the image will be cleared.
     *
     * POST parameters
     *
     * code = shipping method code
     *
     * @since Version 3.0.0
     * @version 3.0.0
     * @author Steffen Haugk
     *
	 * @param $pSection string name of section we are uploading logo for, this gives user prompted size information
     * @return array
     * result - error
     * resultParam - extended error message
     */
    static function uploadLogo($pSection)
    {
        $resultArray = DatabaseObj::uploadPreviewImage($pSection);

        return $resultArray;
    }

    static function getPreviewImage()
    {
        $resultArray = DatabaseObj::getPreviewImage();

        return $resultArray;
    }

}
?>
