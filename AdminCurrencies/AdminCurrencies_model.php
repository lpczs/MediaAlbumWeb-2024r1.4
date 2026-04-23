<?php

require_once('../Utils/UtilsDatabase.php');

class AdminCurrencies_model
{
    static function getGridData()
	{
        return DatabaseObj::getCurrencyList();
    }

    static function currencyAdd()
    {
        global $gSession;

        $result = '';
        $resultParam = '';
        $recordID = 0;

        $currencyCode = strtoupper($_POST['code']);
        $currencyName = html_entity_decode($_POST['name'], ENT_QUOTES);

        $isoNumber = $_POST['isonumber'];
        $currencySymbol = $_POST['symbol'];

        $currencySymbolAtFront = $_POST['symbolatfront'];
        $decimalPlaces = $_POST['decimalplaces'];
        $exchangerate = $_POST['exchangerate'];

        if (($currencyCode != '') && ($currencyName != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('INSERT INTO `CURRENCIES` VALUES (0, now(), ?, ?, ?, ?, ?, ?, now(), ?)'))
                {
                    if ($stmt->bind_param('ssssiid', $currencyCode, $currencyName, $isoNumber, $currencySymbol, $currencySymbolAtFront, $decimalPlaces, $exchangerate))
                    {
                        if ($stmt->execute())
                        {
                            $recordID = $dbObj->insert_id;

                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                'ADMIN', 'CURRENCY-ADD', $recordID . ' ' . $currencyCode, 1);
                        }
                        else
                        {
                            // could not execute statement
                            // first check for a duplicate key (currency code)
                            if ($stmt->errno == 1062)
                            {
                            	$result = 'str_ErrorCurrencyExists';
                            }
                            else
                            {
                            	$result = 'str_DatabaseError';
                            	$resultParam = 'currencyAdd execute ' . $dbObj->error;
                            }
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'currencyAdd bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
	                $stmt->close();
	                $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'currencyAdd prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'currencyAdd connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $recordID;
        $resultArray['code'] = $currencyCode;
        $resultArray['name'] = $currencyName;
        $resultArray['isonumber'] = $isoNumber;
        $resultArray['symbol'] = $currencySymbol;
        $resultArray['symbolatfront'] = $currencySymbolAtFront;
        $resultArray['decimalplaces'] = $decimalPlaces;
        $resultArray['exchangerate'] = $exchangerate;

        return $resultArray;
    }

    static function displayEdit($pCurrencyCode)
	{
	    $resultArray = Array();

	    $recordID = 0;
    	$currencyCode = '';
    	$currencyName = '';
    	$isoNumber = '';
    	$currencySymbol = '';
    	$currencySymbolAtFront = 0;
    	$decimalPlaces = 0;
    	$exchangeratedateset = '';
    	$exchangerate = 1.0000;

        $dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
	    {
	        if ($stmt = $dbObj->prepare('SELECT `id`, `code`, `name`, `isonumber`, `symbol`, `symbolatfront`, `decimalplaces`, `exchangeratedateset`, `exchangerate`  FROM `CURRENCIES` WHERE `code` = ?'))
	        {
	           if ($stmt->bind_param('s', $pCurrencyCode))
	           {
	                if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($recordID, $currencyCode, $currencyName, $isoNumber, $currencySymbol, $currencySymbolAtFront, $decimalPlaces, $exchangeratedateset, $exchangerate))
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

        $resultArray['id'] = $recordID;
        $resultArray['code'] = $currencyCode;
        $resultArray['name'] = $currencyName;
        $resultArray['isonumber'] = $isoNumber;
        $resultArray['symbol'] = $currencySymbol;
        $resultArray['symbolatfront'] = $currencySymbolAtFront;
        $resultArray['decimalplaces'] = $decimalPlaces;
        $resultArray['exchangeratedateset'] = $exchangeratedateset;
        $resultArray['exchangerate'] = $exchangerate;

        return $resultArray;
    }

    static function currencyEdit()
    {
        global $gSession;

        $result = '';
        $resultParam = '';

        $code = $_GET['id'];
        $currencyCode = strtoupper($_POST['code']);
        $currencyName = html_entity_decode($_POST['name'],ENT_QUOTES);
        $isoNumber = $_POST['isonumber'];
        $currencySymbol = $_POST['symbol'];
        $currencySymbolAtFront = $_POST['symbolatfront'];
        $decimalPlaces = $_POST['decimalplaces'];
        $exchangerate = $_POST['exchangerate'];

		$currencyArray = DatabaseObj::getCurrency($currencyCode);
		$originalRate = $currencyArray['exchangerate'];
		$originalExchangeRateDate = $currencyArray['exchangeratedateset'];

        if (($code != '') && ($currencyName !=''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('UPDATE `CURRENCIES` SET `name` = ?, `isonumber` = ?, `symbol` = ?, `symbolatfront` = ?, `decimalplaces` = ?, `exchangerate` = ? , `exchangeratedateset` = IF (( ? = ?) , ?, now()) WHERE `code` = ?'))
                {
                    if ($stmt->bind_param('sssiidddss', $currencyName, $isoNumber, $currencySymbol, $currencySymbolAtFront, $decimalPlaces, $exchangerate, $exchangerate, $originalRate, $originalExchangeRateDate,  $code))
                    {
                        if ($stmt->execute())
                        {
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                'ADMIN', 'CURRENCY-UPDATE', $code . ' ' . $currencyCode, 1);
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'currencyEdit execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'currencyEdit bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
	                $stmt->close();
	                $stmt = null;
                }
                else
                {
                    // could not prepare statement

                    $result = 'str_DatabaseError';
                    $resultParam = 'currencyEdit prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'currencyEdit connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $code;
        $resultArray['code'] = $currencyCode;
        $resultArray['name'] = $currencyName;
        $resultArray['isonumber'] = $isoNumber;
        $resultArray['symbol'] = $currencySymbol;
        $resultArray['symbolatfront'] = $currencySymbolAtFront;
        $resultArray['decimalplaces'] = $decimalPlaces;
        $resultArray['exchangerate'] = $exchangerate;

        return $resultArray;
    }

    static function currencyDelete()
    {
        global $gSession;

        $resutArray = Array();
        $result = '';
        $resultParam = '';

        $currencyCodesDeleted = Array();
        $currencyCodesNotUsed = Array();
        $allDeleted = 1;

        $currencyCodesIDList = explode(',',$_POST['idlist']);
        $currencyCodesIDCount = count($currencyCodesIDList);
        $currencyCodesCodeList = explode(',',$_POST['codelist']);

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            for ($i = 0; $i < $currencyCodesIDCount; $i++)
            {
	            // first make sure the currency hasn't been used
	            $canDelete = true;
	            if ($stmt = $dbObj->prepare('SELECT `id` FROM `CONSTANTS` WHERE `defaultcurrencycode` = ?'))
	            {
	                if ($stmt->bind_param('s', $currencyCodesIDList[$i]))
	                {
	                    if ($stmt->bind_result($recordID))
	                    {
	                       if ($stmt->execute())
	                       {
	                            if ($stmt->fetch())
	                            {
	                                $result = 'str_ErrorUsedInConstants';
	                                $canDelete = false;
	                                $allDeleted = 0;
	                            }
	                            else
	                            {
	                            	$canDelete = true;
	                            	$item['id'] = $currencyCodesIDList[$i];
	                            	$item['code'] = $currencyCodesCodeList[$i];
									array_push($currencyCodesNotUsed, $item);
	                            }
	                       }
	                    }
	                }
	                $stmt->free_result();
	                $stmt->close();
	                $stmt = null;
	            }

	            if ($canDelete == true)
	            {
	                if ($stmt = $dbObj->prepare('SELECT `id` FROM `LICENSEKEYS` WHERE (`usedefaultcurrency` = 0) AND (`currencycode` = ?)'))
	                {
	                    if ($stmt->bind_param('s', $currencyCodesIDList[$i]))
	                    {
	                        if ($stmt->bind_result($recordID))
	                        {
	                           if ($stmt->execute())
	                           {
	                                if ($stmt->fetch())
	                                {
	                                    $result = 'str_ErrorUsedInLicenseKey';
	                                    $canDelete = false;
	                                    $allDeleted = 0;
	                                }
	                                else
	                            	{
	                            		$canDelete = true;
	                            		$item['id'] = $currencyCodesIDList[$i];
	                            		$item['code'] = $currencyCodesCodeList[$i];
										array_push($currencyCodesNotUsed, $item);
	                            	}
	                           }
	                        }
	                    }
	                    $stmt->free_result();
	                    $stmt->close();
	                    $stmt = null;
	                }
	            }

	            if ($canDelete == true)
	            {
	                if ($stmt = $dbObj->prepare('SELECT `id` FROM `USERS` WHERE (`usedefaultcurrency` = 0) AND (`currencycode` = ?)'))
	                {
	                    if ($stmt->bind_param('s', $currencyCodesIDList[$i]))
	                    {
	                        if ($stmt->bind_result($recordID))
	                        {
	                           if ($stmt->execute())
	                           {
	                                if ($stmt->fetch())
	                                {
	                                    $result = 'str_ErrorCurrencyUsedInCustomer';
	                                    $canDelete = false;
	                                    $allDeleted = 0;
	                                }
	                                else
	                            	{
	                            		$canDelete = true;
	                            		$item['id'] = $currencyCodesIDList[$i];
	                            		$item['code'] = $currencyCodesCodeList[$i];
										array_push($currencyCodesNotUsed, $item);
	                            	}
	                           }
	                        }
	                    }
	                    $stmt->free_result();
	                    $stmt->close();
	                    $stmt = null;
	                }
	            }

	            if ($canDelete == true)
	            {
	                if ($stmt = $dbObj->prepare('DELETE FROM `CURRENCIES` WHERE `code` = ?'))
	                {
	                    if ($stmt->bind_param('s', $currencyCodesIDList[$i]))
	                    {
	                        if ($stmt->execute())
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                'ADMIN', 'CURRENCY-DELETE', $currencyCodesIDList[$i] . ' ' . $currencyCodesCodeList[$i], 1);
	                                array_push($currencyCodesDeleted, $currencyCodesIDList[$i]);
	                        }
	                    }
	                   	$stmt->free_result();
	                    $stmt->close();
	                    $stmt = null;
	                }
	            }
            }
        }

        $dbObj->close();

    	$resultArray['alldeleted'] = $allDeleted;
        $resultArray['currencyids'] = $currencyCodesDeleted;
        $resultArray['result'] = $result;

        return $resultArray;
    }
}
?>
