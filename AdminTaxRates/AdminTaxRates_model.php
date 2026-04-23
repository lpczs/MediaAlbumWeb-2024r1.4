<?php
require_once('../Utils/UtilsDatabase.php');

class AdminTaxRates_model
{

    static function getGridData()
    {
        global $gSession;

        $resultArray = Array();
        $resultArray = DatabaseObj::getTaxRatesList();

        return $resultArray;
    }

    static function taxRatesAdd()
    {
        global $gSession;
        $result = '';
        $resultParam = '';
        $recordID = 0;
        $taxRateCode = strtoupper($_POST['code']);
        $taxRateName = html_entity_decode($_POST['name'], ENT_QUOTES);
        $taxRate = $_POST['rate'];

        if (($taxRateCode != '') && ($taxRateName != '') && ($taxRate != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('INSERT INTO `TAXRATES` VALUES (0, now(), ?, ?, ?)'))
                {
                    if ($stmt->bind_param('ssd', $taxRateCode, $taxRateName, $taxRate))
                    {
                        if ($stmt->execute())
                        {
                            $recordID = $dbObj->insert_id;

                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                    $gSession['username'], 0, 'ADMIN', 'TAXRATE-ADD', $recordID . ' ' . $taxRateCode, 1);
                        }
                        else
                        {
                            // could not execute statement
                            // first check for a duplicate key (tax rate code)
                            if ($stmt->errno == 1062)
                            {
                                $result = 'str_ErrorTaxRateExists';
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'taxratesAdd execute ' . $dbObj->error;
                            }
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'taxratesAdd bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'taxratesAdd prepare ' . $dbObj->error;
                }
                $stmt->close();
                $stmt = null;
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'taxratesAdd connect ' . $dbObj->error;
            }
        }
        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $recordID;
        $resultArray['code'] = $taxRateCode;
        $resultArray['name'] = $taxRateName;
        $resultArray['rate'] = $taxRate;

        return $resultArray;
    }

    static function displayEdit($pID)
    {
        $resultArray = Array();
        $taxRateID = 0;
        $taxRateCode = '';
        $taxRateName = '';
        $taxRate = 0.00;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $stmt = $dbObj->prepare('SELECT `id`, `code`, `name`, `rate` FROM `TAXRATES` WHERE `id` = ?');
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
                                if ($stmt->bind_result($taxRateID, $taxRateCode, $taxRateName, $taxRate))
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
        $resultArray['id'] = $taxRateID;
        $resultArray['code'] = $taxRateCode;
        $resultArray['name'] = $taxRateName;
        $resultArray['rate'] = $taxRate;

        return $resultArray;
    }

    static function taxRatesEdit()
    {
        global $gSession;

        $result = '';
        $resultParam = '';

        $taxRateID = $_GET['id'];
        $taxRateCode = strtoupper($_POST['code']);
        $taxRateName = html_entity_decode($_POST['name'], ENT_QUOTES);
        $taxRate = $_POST['rate'];

        if (($taxRateCode != '') && ($taxRateName != '') && ($taxRate != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('UPDATE `TAXRATES` SET `name` = ?, `rate` = ? WHERE `code` = ?'))
                {
                    if ($stmt->bind_param('sds', $taxRateName, $taxRate, $taxRateCode))
                    {
                        if ($stmt->execute())
                        {
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                    $gSession['username'], 0, 'ADMIN', 'TAXRATE-UPDATE', $taxRateID . ' ' . $taxRateCode, 1);
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'taxratesEdit execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'taxratesEdit bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'taxratesEdit prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'taxratesEdit connect ' . $dbObj->error;
            }
        }
        $resultArray['result'] = $result;
        $resultArray['id'] = $taxRateID;
        $resultArray['code'] = $taxRateCode;
        $resultArray['name'] = $taxRateName;
        $resultArray['rate'] = $taxRate;

        return $resultArray;
    }

    static function taxRatesDelete()
    {
        global $gSession;

        $result = '';
        $allDeleted = 1;
        $canDelete = false;
        $recordID = 0;

        $taxRateIDList = explode(',',$_POST['idlist']);
        $taxRateCodeList = explode(',',$_POST['codelist']);
        $taxRateIDListCount = count($taxRateIDList);

        $taxCodesDeleted = Array();
        $taxCodesNotUsed = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            for ($i = 0; $i < $taxRateIDListCount; $i++)
            {
            	// first make sure the tax rate hasn't been used
	            if ($stmt = $dbObj->prepare('SELECT `id` FROM `TAXZONES` WHERE (`taxlevel1` = ?) OR (`shippingtaxcode` = ?)'))
	            {
	                if ($stmt->bind_param('ss', $taxRateCodeList[$i], $taxRateCodeList[$i]))
	                {
	                    if ($stmt->bind_result($recordID))
	                    {
	                       if ($stmt->execute())
	                       {
	                            if ($stmt->fetch())
	                            {
	                                $result = 'str_ErrorUsedInTaxZone';
	                                $canDelete = false;
	                                $allDeleted = 0;
	                            }
	                            else
	                            {
	                            	$canDelete = true;
	                            	$item['id'] = $taxRateIDList[$i];
	                            	$item['code'] = $taxRateCodeList[$i];
									array_push($taxCodesNotUsed, $item);
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
	                if ($stmt = $dbObj->prepare('SELECT `id` FROM `USERS` WHERE `customer` = 1 AND (`taxcode` = ?) OR (`shippingtaxcode` = ?)'))
	                {
	                    if ($stmt->bind_param('ss', $taxRateCodeList[$i], $taxRateCodeList[$i]))
	                    {
	                        if ($stmt->bind_result($recordID))
	                        {
	                           if ($stmt->execute())
	                           {
	                                if ($stmt->fetch())
	                                {
	                                    $result = 'str_ErrorAssignedToCustomer';
	                                    $canDelete = false;
	                                    $allDeleted = 0;
	                                }
	                                else
	                            	{
	                            		$canDelete = true;
	                            		$item['id'] = $taxRateIDList[$i];
	                            		$item['code'] = $taxRateCodeList[$i];
										array_push($taxCodesNotUsed, $item);
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
	                if ($stmt = $dbObj->prepare('SELECT `id` FROM `LICENSEKEYS` WHERE (`taxcode` = ?) OR (`shippingtaxcode` = ?)'))
	                {
	                    if ($stmt->bind_param('ss', $taxRateCodeList[$i], $taxRateCodeList[$i]))
	                    {
	                        if ($stmt->bind_result($recordID))
	                        {
	                           if ($stmt->execute())
	                           {
	                                if ($stmt->fetch())
	                                {
	                                    $result = 'str_ErrorAssignedToLicenseKey';
	                                    $canDelete = false;
	                                    $allDeleted = 0;
	                                }
	                                else
	                            	{
	                            		$canDelete = true;
	                            		$item['id'] = $taxRateIDList[$i];
	                            		$item['code'] = $taxRateCodeList[$i];
										array_push($taxCodesNotUsed, $item);
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
	                if ($stmt = $dbObj->prepare('SELECT `id` FROM `ORDERITEMS` WHERE `taxcode` = ?'))
	                {
	                    if ($stmt->bind_param('s', $taxRateCodeList[$i]))
	                    {
	                        if ($stmt->bind_result($recordID))
	                        {
	                           if ($stmt->execute())
	                           {
	                                if ($stmt->fetch())
	                                {
	                                    $result = 'str_ErrorUsedInOrder';
	                                    $canDelete = false;
	                                    $allDeleted = 0;
	                                }
	                                else
	                            	{
	                            		$canDelete = true;
	                            		$item['id'] = $taxRateIDList[$i];
	                            		$item['code'] = $taxRateCodeList[$i];
										array_push($taxCodesNotUsed, $item);
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
	                if ($stmt = $dbObj->prepare('DELETE FROM `TAXRATES` WHERE `id` = ?'))
	                {
	                    if ($stmt->bind_param('i', $taxRateIDList[$i]))
	                    {
	                        if ($stmt->execute())
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                'ADMIN', 'TAXRATE-DELETE', $taxRateIDList[$i] . ' ' .  $taxRateCodeList[$i], 1);
	                                array_push($taxCodesDeleted, $taxRateIDList[$i]);
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
        $resultArray['taxrateids'] = $taxCodesDeleted;
        $resultArray['result'] = $result;

        return $resultArray;
    }

}
?>
