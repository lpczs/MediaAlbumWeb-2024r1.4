<?php

require_once('../Utils/UtilsDatabase.php');

class AdminShippingZones_model
{
    static function getGridData()
    {
        global $gSession;
	
        $resultArray = Array();
	    $sortby = 'code';
	    $dir = 'ASC';

		if (isset($_POST['sort']))
		{
			$sortby = $_POST['sort'];
		}
		
	 	switch ($sortby)
	 	{
			case 'name':
				$sort = 'name';
				break;
			case 'countrycodes':
				$sort = 'countrycodes';
				break;
			default:
				$sort = 'code';
		}		
		
		if (isset($_POST['dir']))
		{
			if ($_POST['dir'] != $dir)
			{
				$dir = 'DESC';
			}
		}		
              		
		$orderBy = ' ORDER BY `companycode`, `' . $sort . '` ' . $dir . ';';		
		
		$shippingZonesList = DatabaseObj::getShippingZonesList($orderBy);
        $shippingZoneCount = count($shippingZonesList);
        $combinedList = UtilsAddressObj::getCombinedCountryRegionList(true, $gSession['browserlanguagecode']);

        for ($i = 0; $i < $shippingZoneCount; $i++)
        {
       		$countryCodes = UtilsAddressObj::getTaxCountryListFromCodes($shippingZonesList[$i]['countrycodes'],
       						$combinedList);

       		$item['id'] = $shippingZonesList[$i]['id'];
			$item['localcode'] = $shippingZonesList[$i]['localcode'];
			$item['name'] = $shippingZonesList[$i]['name'];
			$item['countrycodes'] = $countryCodes;
			$item['companycode'] = $shippingZonesList[$i]['companycode'];
       	 	array_push($resultArray, $item);
        }
		
        return $resultArray;
	}
    
    static function shippingZoneAdd()
    {
        global $gSession;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $recordID = 0;
        $companyCode = '';
        
        $shippingZoneCode = strtoupper($_POST['code']);
        $shippingZoneLocalCode = strtoupper($_POST['code']);
        $shippingZoneName = $_POST['name'];
        $countryCodes = $_POST['countrycodes'];
                
    	switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:								
				if ($gConstants['optionms'])
        		{
        			$companyCode = $_POST['company'];
					
					if($companyCode == 'GLOBAL')
					{
						$companyCode = '';
						$shippingZoneCode = $shippingZoneLocalCode;
					}
					else
					{
						$shippingZoneCode = $companyCode.'.'.$shippingZoneLocalCode;
					}
        		}		
			break;
			case TPX_LOGIN_COMPANY_ADMIN:
				
				$isRestOfWorld = $_POST['isrestofworld'];
				
				$companyCode = $_POST['company'];
				
				if($companyCode == 'GLOBAL')
				{
					$companyCode = '';
				}
				
        		$shippingZoneCode = $companyCode.'.'.$shippingZoneLocalCode;
        		$shippingZoneLocalCode = $shippingZoneLocalCode;
				
				if($isRestOfWorld == 1)
				{
					$shippingZoneCode = $companyCode.'.';
					$shippingZoneLocalCode = '';
				}
								
			break;
		}

        if ($shippingZoneName != '')
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('INSERT INTO `SHIPPINGZONES` VALUES (0, now(), ?, ?, ?, ?, ?)'))
                {
                    if ($stmt->bind_param('sssss', $companyCode, $shippingZoneCode,$shippingZoneLocalCode, $shippingZoneName, $countryCodes))
                    {
                        if ($stmt->execute())
                        {
                            $recordID = $dbObj->insert_id;
                            
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 
                                'ADMIN', 'SHIPPINGZONE-ADD', $recordID . ' ' . $shippingZoneLocalCode, 1);
                        }
                        else
                        {
                            // could not execute statement
                            
                            // first check for a duplicate key (shipping zone code)
                            if ($stmt->errno == 1062)
                            {
                            	$result = 'str_ErrorShippingZoneExists';
                            }
                            else
                            {
                            	$result = 'str_DatabaseError';
                            	$resultParam = 'shippingZoneAdd execute ' . $dbObj->error;
                            }
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'shippingZoneAdd bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
	                $stmt->close();
	                $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'shippingZoneAdd prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'shippingZoneAdd connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $recordID;
        $resultArray['companycode'] = $companyCode;
        $resultArray['code'] = $shippingZoneCode;
        $resultArray['name'] = $shippingZoneName;
        $resultArray['countries'] = $countryCodes;
        
        return $resultArray;
    }

    static function displayEdit($pID)
	{
	    $resultArray = Array();
	    
        $shippingZoneID = 0;
        $shippingZoneCode = '';
        $shippingZoneLocalCode = '';
        $shippingZoneName = '';
        $countryCodes = '';
        $shippingCompanyCode = '';
        
        $dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
	    {
	        if ($stmt = $dbObj->prepare('SELECT `id`, `companycode`, `code`, `localcode`, `name`, `countrycodes` FROM `SHIPPINGZONES` WHERE `id` = ?'))
	        {
	            if ($stmt->bind_param('i', $pID))
	            {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($shippingZoneID, $shippingCompanyCode,$shippingZoneCode, $shippingZoneLocalCode, $shippingZoneName, $countryCodes))
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
        
        $resultArray['id'] = $shippingZoneID;
        $resultArray['companycode'] = $shippingCompanyCode;
         $resultArray['code'] = $shippingZoneCode;
        $resultArray['localcode'] = $shippingZoneLocalCode;
        $resultArray['name'] = $shippingZoneName;
        $resultArray['countries'] = $countryCodes;
        
        return $resultArray;
    }

    static function shippingZoneEdit()
    {
        global $gSession;
        global $gConstants;
        
        $result = '';
        $resultParam = '';
        $shippingZoneID = $_GET['id'];
        // shipping zone code used for display purposes localcode in DB
        $shippingZoneCode = strtoupper($_POST['code']);
        // Shipping zone code used to update - code in DB
        $shippingZoneCodeMain = strtoupper($_POST['shippingzonemain']);
        $shippingZoneName = $_POST['name'];
        $countryCodes = $_POST['countrycodes'];
        $isDefault = ($_POST['isdefault']);
        
        if ($isDefault == 1)
        {
        	$shippingZoneCode = '';
        }
        
        if ($gConstants['optionms'])
        {
        	$companyCode = $_POST['company'];
        	if($companyCode == 'GLOBAL')
			{
				$companyCode = '';
			}
        }
        else
        {
        	$companyCode = '';
        }
		
        if ($shippingZoneName != '')
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('UPDATE `SHIPPINGZONES` SET `companycode` = ?, `name` = ?, `countrycodes` = ? WHERE `code` = ?'))
                {
                    if ($stmt->bind_param('ssss', $companyCode, $shippingZoneName, $countryCodes, $shippingZoneCodeMain))
                    {
                        if ($stmt->execute())
                        {
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 
                                'ADMIN', 'SHIPPINGZONE-UPDATE', $shippingZoneID . ' ' . $shippingZoneCodeMain, 1);
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'shippingZoneEdit execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'shippingZoneEdit bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
	                $stmt->close();
	                $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'shippingZoneEdit prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'shippingZoneEdit connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $shippingZoneID;
        $resultArray['companycode'] = $companyCode;
        $resultArray['code'] = $shippingZoneCode;
        $resultArray['name'] = $shippingZoneName;
        $resultArray['countries'] = $countryCodes;
        
        return $resultArray;
    }

    
    static function shippingZoneDelete()
    {
        global $gSession;
        global $gConstants;
        
        $resutArray = Array();
        $result = '';
        $resultParam = '';
        $shippingZonesDeleted = Array();
        $shippingZonesNotUsed = Array();        
        $allDeleted = 1;
        $canDelete = false;
        
        $shippingZoneIDList = explode(',',$_POST['idlist']);
        $shippingZoneIDCount = count($shippingZoneIDList);
        $shippingZoneCodeList = explode(',',$_POST['codelist']);
        $shippingZoneCompanyCodeList = explode(',',$_POST['companycodelist']);
		
        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            for ($i = 0; $i < $shippingZoneIDCount; $i++)
            {
	             if ($gConstants['optionms'])
        		 {
        		 	$companyCode = $shippingZoneCompanyCodeList[$i];
        		 }
        		 else
        		 {
        		 	$companyCode = '';
        		 }
	            
	            // first make sure the shipping zone hasn't been used
	            if ($stmt = $dbObj->prepare('SELECT `id` FROM `SHIPPINGRATES` WHERE `shippingzonecode` = ? AND `companycode` = ?'))
	            {
	                if ($stmt->bind_param('ss', $shippingZoneCodeList[$i], $companyCode))
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
	                            	$item['id'] = $shippingZoneIDList[$i];
									$item['code'] = $shippingZoneCodeList[$i];
									array_push($shippingZonesNotUsed, $item);
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
		            if ($stmt = $dbObj->prepare('DELETE FROM `SHIPPINGZONES` WHERE `id` = ?'))
		            {
	                	if ($stmt->bind_param('i', $shippingZoneIDList[$i]))
	                	{
	                    	if ($stmt->execute())
	                    	{
	                        	DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 
	                        	'ADMIN', 'SHIPPINGZONE-DELETE', $shippingZoneIDList[$i] . ' ' . $shippingZoneCodeList[$i], 1);
	                        	array_push($shippingZonesDeleted, $shippingZoneIDList[$i]);
	                    	}
	                	}
	                	$stmt->free_result();
	                	$stmt->close();
	                	$stmt = null;
		            }
	            }
	        }		    
        }
        $resultArray['result'] = $result;
        $resultArray['alldeleted'] = $allDeleted;
        $resultArray['shippingzoneids'] = $shippingZonesDeleted;
        
        return $resultArray;
    }
}
?>
