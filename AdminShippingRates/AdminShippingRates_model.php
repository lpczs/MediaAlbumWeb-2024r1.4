<?php
require_once('../Utils/UtilsDatabase.php');

class AdminShippingRates_model
{

    static function getItemList($pParentID)
    {
        $result = '';
        $resultParam = '';
        $resultArray = Array();
        $itemList = Array();

        if ($pParentID > 0)
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('SELECT `id`, `companycode`, `shippingmethodcode`, `shippingzonecode`, `productcode`, `groupcode`, `taxcode`, `active` FROM `SHIPPINGRATES` WHERE (`id` = ?) OR (`parentid` = ?) ORDER BY `parentid`'))
                {
                    if ($stmt->bind_param('ii', $pParentID, $pParentID))
                    {
                        if ($stmt->bind_result($id, $companyCode, $shippingMethodCode, $shippingRateCode, $productCode, $groupCode,
                                        $taxCode, $isActive))
                        {
                            if ($stmt->execute())
                            {
                                while ($stmt->fetch())
                                {
                                    $item['recordid'] = $id;
                                    $item['companycode'] = $companyCode;
                                    $item['shippingmethodcode'] = $shippingMethodCode;
                                    $item['shippingratecode'] = $shippingRateCode;
                                    $item['productcode'] = $productCode;
                                    $item['groupcode'] = $groupCode;
                                    $item['taxcode'] = $taxCode;
                                    $item['isactive'] = $isActive;
                                    array_push($itemList, $item);
                                }
                            }
                            else
                            {
                                // could not execute statement
                                $result = 'str_DatabaseError';
                                $resultParam = 'shippingratesgetitemlist execute ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            // could not bind result
                            $result = 'str_DatabaseError';
                            $resultParam = 'shippingratesgetitemlist bind result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'shippingratesgetitemlist bind params ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'shippingratesgetitemlist connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['items'] = $itemList;

        return $resultArray;
    }

    static function getGridData()
    {
        global $gSession;

		$sortby  = 'code';
		$dir = 'ASC';
        $start = 0;
	    $limit = 100;
	    $total = 0;
		$totalRowCount = 50000;
		$params = array();
		$resultArray = array();
		$hideInactive = 0;

		if (UtilsObj::getPOSTParam('sort') != '')
		{
			$sortby = UtilsObj::getPOSTParam('sort');
		}

		// check that hideinactive has been sent before safely retrieving it
		if (isset($_POST['hideInactive']))
		{
			$hideInactive = filter_input(INPUT_POST, 'hideInactive', FILTER_SANITIZE_NUMBER_INT);
		}

		switch ($sortby)
		{
			case 'shippingmethodcode':
				$sort  = '`sr`.`shippingmethodcode`';
				break;
			case 'shippingzonecode':
				$sort = '`sr`.`shippingzonecode`';
				break;
			case 'product':
				$sort = '`pr`.`name`';
				break;
			case 'rates':
				$sort = '`sr`.`rate`';
				break;
			case 'active':
				$sort = '`sr`.`active`';
				break;
			default:
				$sort = '`sr`.`code`';
				break;
		}

		if ((UtilsObj::getPOSTParam('dir') != '') && (UtilsObj::getPOSTParam('dir') != $dir))
		{
			$dir = UtilsObj::getPOSTParam('dir');
		}

		if (isset($_POST['start']))
		{
			$start = (int)$_POST['start'];
		}

		if (isset($_POST['limit']))
		{
			$limit = (int)$_POST['limit'];
		}

		switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				  $stmt = 'SELECT `sr`.`id`, `sr`.`groupcode`,
					(SELECT group_concat(`sr2`.`groupcode`) FROM SHIPPINGRATES sr2 WHERE `sr2`.`parentid` = `sr`.`id`),
					`sr`.`companycode`, `sr`.`code`, `sr`.`shippingmethodcode`, `sr`.`shippingzonecode`, `sm`.`name`, `sz`.`name`, `sr`.`productcode`, `pr`.`name`, `sr`.`info`, `sr`.`rate`, `sr`.`active`
					FROM SHIPPINGRATES sr
					LEFT JOIN `SHIPPINGMETHODS` sm ON `sm`.`code` = `sr`.`shippingmethodcode`
					LEFT JOIN `SHIPPINGZONES` sz ON `sz`.`code` = `sr`.`shippingzonecode`
					LEFT JOIN `PRODUCTS` pr ON `pr`.`code` = `sr`.`productcode`
					WHERE `sr`.`parentid` = 0';
				break;
			case TPX_LOGIN_COMPANY_ADMIN:
				  $stmt = 'SELECT `sr`.`id`, `sr`.`groupcode`,
					(SELECT group_concat(`sr2`.`groupcode`) FROM SHIPPINGRATES sr2 WHERE `sr2`.`parentid` = `sr`.`id`),
					`sr`.`companycode`, `sr`.`code`, `sr`.`shippingmethodcode`, `sr`.`shippingzonecode`, `sm`.`name`, `sz`.`name`, `sr`.`productcode`, `pr`.`name`, `sr`.`info`, `sr`.`rate`, `sr`.`active`
					FROM SHIPPINGRATES sr
					LEFT JOIN `SHIPPINGMETHODS` sm ON `sm`.`code` = `sr`.`shippingmethodcode`
					LEFT JOIN `SHIPPINGZONES` sz ON `sz`.`code` = `sr`.`shippingzonecode`
					LEFT JOIN `PRODUCTS` pr ON `pr`.`code` = `sr`.`productcode`
					WHERE (`sr`.`parentid` = 0) AND (`sr`.`companycode` = "" || `sr`.`companycode` = ?)';

					$params[] = $gSession['userdata']['companycode'];
				break;
		}

        $searchFields = UtilsObj::getPOSTParam('fields');

        if ($searchFields != '')
		{
			$searchQuery = $_POST['query'];
			$selectedfields = str_replace("[", "",$_POST['fields']);
			$selectedfields = str_replace("]", "",$selectedfields);
			$selectedfields = str_replace('"', "",$selectedfields);
			$selectedfields = explode(',', $selectedfields);

			$i = 1;

			if ($searchQuery != '')
			{
				foreach ($selectedfields as $value)
				{
					if ($i == 1)
					{
						$operator = ' AND (';
					}
					else
					{
						$operator = 'OR';
					}
					$params[] = '%'.$searchQuery.'%';
					$stmt .= $operator.'(`sr`.`'.$value.'` LIKE ?)';
					$i++;
				}
				$stmt .= ')';
				$bind = 1;
			}
			else
			{
				if ($hideInactive == 1)
				{
					$stmt .= ' AND (`sr`.`active` = 1)';
				}

				if ($gSession['userdata']['usertype'] == TPX_LOGIN_SYSTEM_ADMIN)
				{
					$bind = 0;
				}
				else
				{
					$bind = 1;
				}
			}
		}
		else
		{
			$params = Array();

			if ($hideInactive == 1)
			{
				$stmt .= ' AND (`sr`.`active` = 1)';
			}

			switch ($gSession['userdata']['usertype'])
			{
				case TPX_LOGIN_SYSTEM_ADMIN:
					$bind = 0;
				break;
				case TPX_LOGIN_COMPANY_ADMIN:
					$bind = 1;
					$params[] = $gSession['userdata']['companycode'];
				break;
			}
		}

		$orderBy = ' ORDER BY `sr`.`companycode`, ' . $sort . ' ' . $dir . ' LIMIT ' . $limit . ' OFFSET ' . $start . ';';

        $resultArray = self::bindParams($stmt, $params, $bind, $orderBy);

        return $resultArray;
    }

    static function bindParams($pStatement, $pParams, $pBind, $pOrderBy)
    {
		$shippingRateItem = array();
		$shippingRateItemArray = array();
		$resultArray = array();
		$sqlBindTypes = '';
        $totalRecords = 0;

    	//for each element, determine type and add
    	foreach($pParams as $param)
    	{
            if(is_int($param))
            {
            	$sqlBindTypes .= 'i';
            }
            else
            {
            	$sqlBindTypes .= 's';
            }
        }

        $bind_names[] = $sqlBindTypes;

        for ($i = 0; $i < count($pParams); $i++)
        {									//go through incoming params and added em to array
            $bind_name = 'bind' . $i;       //give them an arbitrary name
            $$bind_name = $pParams[$i];     //add the parameter to the variable variable
            $bind_names[] = &$$bind_name;   //now associate the variable as an element in an array
        }

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			// Increase the character limit on group_concat to account for shipping rates with many license keys assigned to them.
			// Note: 4096 is 4 times the default character limit of 1024.
			$setSessionOk = false;

			if ($sessionStmt = $dbObj->prepare('SET SESSION group_concat_max_len = 4096'))
			{
				if ($sessionStmt->execute())
				{
					$setSessionOk = true;
				}
			}

			if (($setSessionOk) && ($stmt = $dbObj->prepare($pStatement)))
			{
                if ($pBind == 1)
				{
					$bindOk = call_user_func_array(array($stmt,'bind_param'),$bind_names);
				}
				else
				{
					$bindOk = true;
				}
                if ($bindOk)
				{
                    /* execute query */
                    $stmt->execute();

                    /* store result */
                    $stmt->store_result();

                    // Store the total number of records that the statment returns without limit
                    $totalRecords = $stmt->num_rows;

                    /* close statement */
                    $stmt->close();
                }
			}
		}

		// Concatenate the order by statement to original query so that a limit is set.
		// This is for paging.
		$pStatement .= $pOrderBy;

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare($pStatement))
			{
				$stmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);

				if ($pBind == 1)
				{
					$bindOk = call_user_func_array(array($stmt,'bind_param'),$bind_names);
				}
				else
				{
					$bindOk = true;
				}

				if ($bindOk)
				{
                    if ($stmt->bind_result($shippingRateID, $groupCode, $childrenGroupCodes, $shippingRateCompanyCode, $shippingRateCode, $shippingMethodCode,
                                    $shippingZoneCode, $shippingMethodName, $shippingZoneName, $productCode, $productName, $info,
                                    $shippingRate, $isActive))
	                {
	                    if ($stmt->execute())
	                    {

	                        while ($stmt->fetch())
	                        {
	                            $shippingRateItem['recordid'] = $shippingRateID;

                                if ($childrenGroupCodes == '')
                                {
                                	$shippingRateItem['groupcodes'] = $groupCode;
                                }
                                else
                                {
                                	$shippingRateItem['groupcodes'] = $childrenGroupCodes . ',' . $groupCode;
                                }

                                $shippingRateItem['companycode'] = $shippingRateCompanyCode;
                                $shippingRateItem['shippingratecode'] = $shippingRateCode;
                                $shippingRateItem['shippingmethodcode'] = $shippingMethodCode;
                                $shippingRateItem['shippingzonecode'] = $shippingZoneCode;
                                $shippingRateItem['shippingmethodame'] = $shippingMethodName;
                                $shippingRateItem['shippingzonename'] = $shippingZoneName;
                                $shippingRateItem['productcode'] = $productCode;
                                $shippingRateItem['productname'] = $productName;
                                $shippingRateItem['productinfo'] = $info;
                                $shippingRateItem['shippingrate'] = $shippingRate;
                                $shippingRateItem['isactive'] = $isActive;
                                array_push($shippingRateItemArray, $shippingRateItem);
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

		$resultArray['total'] = $totalRecords;
		$resultArray['shippingrates'] = $shippingRateItemArray;

		return $resultArray;
    }

    static function shippingRateActivate()
    {
        global $gSession;

        $resultArray = Array();

        $ids = $_POST['ids'];
        $idList = explode(',', $ids);
        $active = $_POST['active'];
        if ($active != '0') $active = 1;
        $itemCount = count($idList);

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('UPDATE `SHIPPINGRATES` SET `active` = ? WHERE `id` = ?'))
            {
                for ($i = 0; $i < $itemCount; $i++)
                {
                    $itemArray = self::getItemList($idList[$i]);
                    $itemList = $itemArray['items'];
                    $itemListCount = count($itemList);

                    for ($j = 0; $j < $itemListCount; $j++)
                    {
                        $id = $itemList[$j]['recordid'];
                        $shippingRateCode = $itemList[$j]['shippingratecode'];

                        if ($stmt->bind_param('ii', $active, $id))
                        {
                            if ($stmt->execute())
                            {
                                if ($active == 1)
                                {
                                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                            $gSession['username'], 0, 'ADMIN', 'SHIPPINGRATE-DEACTIVATE', $id . ' ' . $shippingRateCode, 1);
                                }
                                else
                                {
                                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                            $gSession['username'], 0, 'ADMIN', 'SHIPPINGRATE-ACTIVATE', $id . ' ' . $shippingRateCode, 1);
                                }
                                array_push($resultArray, $itemArray);
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

    static function shippingRateUpdate()
    {
        global $gSession;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $recordID = 0;

        if ($gConstants['optionms'])
        {
            if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
            {
                $companyCode = $gSession['userdata']['companycode'];
            }
            else
            {
                $companyCode = $_POST['company'];
            }


            if ($companyCode == 'GLOBAL')
            {
                $companyCode = '';
            }
        }
        else
        {
            $companyCode = '';
        }

        if ($gConstants['optioncfs'])
        {
            $payInStoreOption = $_POST['payinstoreoption'];
            $siteGroups = $_POST['sitegroup'];
        }
        else
        {
            $payInStoreOption = 0;
            $siteGroups = '';
        }

        $shippingRateID = $_GET['id'];

        $shippingRateCode = strtoupper($_POST['code']);
        $shippingMethodCode = $_POST['shippingmethodcode'];
        $shippingZoneCode = $_POST['shippingzonecode'];
        $info = $_POST['info'];
        $info = html_entity_decode($_POST['info'], ENT_QUOTES);
        $orderValueRange = $_POST['ordervaluerange'];
        $orderMinValue = $_POST['orderminvalue'];
        $orderMaxValue = $_POST['ordermaxvalue'];
        $orderValueIncludesDiscount = $_POST['ordervalueincludesdiscount'];

        $shippingRates = $_POST['shippingrates'];

        $productCode = $_POST['productcode'];

        $parentCode = $_POST['parentcode'];
        $groupCodeList = $_POST['groupcode'];

        $taxCode = $_POST['taxcode'];
        $isActive = $_POST['isactive'];

        $firstID = 0;

        if (($shippingRateCode != '') && ($shippingMethodCode != ''))
        {
            $groupArray = explode(',', $groupCodeList);
            $itemCount = count($groupArray);

            $itemArray = self::getItemList($shippingRateID);
            $existingItemList = $itemArray['items'];

            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                $dbObj->query('START TRANSACTION');

                // determine if the parent item will still be present
                $existingItemCount = count($existingItemList);
                if (!in_array($parentCode, $groupArray))
                {
                    // the parent will not exist anymore so we need to delete it and assign a new one
                    self::deleteRatesList(array(0 => array('recordid' => $shippingRateID, 'shippingratecode' => $shippingRateCode)));
                    $shippingRateID = 0;
                }

                // insert / update the rates
                for ($i = 0; $i < $itemCount; $i++)
                {
                    $groupCode = $groupArray[$i];
                    $id = 0;
                    $existingItemCount = count($existingItemList);
                    for ($j = 0; $j < $existingItemCount; $j++)
                    {
                        if ($groupCode == $existingItemList[$j]['groupcode'])
                        {
                            $id = $existingItemList[$j]['recordid'];
                            array_splice($existingItemList, $j, 1);
                            break;
                        }
                    }

                    if ($id == $shippingRateID)
                    {
                        $parentID = 0;
                    }
                    else
                    {

                        $parentID = $shippingRateID;
                    }

                    if ($parentID == 0)
                    {
                        $uniqueCode = $shippingRateCode;
                    }
                    else
                    {
                        $uniqueCode = $shippingRateCode . '_' . $i . '_' . time();
                    }

                    if ($id == 0)
                    {

                        $stmt = $dbObj->prepare('INSERT INTO `SHIPPINGRATES` VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                    }
                    else
                    {
                        $stmt = $dbObj->prepare('UPDATE `SHIPPINGRATES` SET `companycode` = ?, `parentid` = ?, `uniquecode` = ?, `shippingmethodcode` = ?, `shippingzonecode` = ?, `productcode` = ?, `info` = ?,
							`rate` = ?, `ordervaluetype` = ?, `orderminimumvalue` = ?, `ordermaximumvalue` = ?, `ordervalueincludesdiscount` = ?, `payinstoreallowed` = ?, `taxcode` = ?, `active` = ? WHERE `id` = ?');
                    }

                    if ($stmt)
                    {
                        if ($id == 0)
                        {
                            $bindOK = $stmt->bind_param('sisssssssssddiisi', $companyCode, $parentID, $shippingRateCode, $uniqueCode,
                                    $shippingMethodCode, $shippingZoneCode, $productCode, $groupCode, $info, $shippingRates,
                                    $orderValueRange, $orderMinValue, $orderMaxValue, $orderValueIncludesDiscount, $payInStoreOption,
                                    $taxCode, $isActive);
                        }
                        else
                        {
                            $bindOK = $stmt->bind_param('sisssssssddiisii', $companyCode, $parentID, $uniqueCode, $shippingMethodCode,
                                    $shippingZoneCode, $productCode, $info, $shippingRates, $orderValueRange, $orderMinValue,
                                    $orderMaxValue, $orderValueIncludesDiscount, $payInStoreOption, $taxCode, $isActive, $id);
                        }

                        if ($bindOK)
                        {
                            if ($stmt->execute())
                            {
                                if ($id == 0)
                                {
                                    $id = $dbObj->insert_id;

                                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                            $gSession['username'], 0, 'ADMIN', 'SHIPPINGRATE-ADD',
                                            $id . ' ' . $shippingRateCode . ' - ' . ($groupCode == '' ? 'DEFAULT' : $groupCode), 1);
                                }
                                else
                                {
                                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                            $gSession['username'], 0, 'ADMIN', 'SHIPPINGRATE-UPDATE',
                                            $id . ' ' . $shippingRateCode . ' - ' . ($groupCode == '' ? 'DEFAULT' : $groupCode), 1);
                                }

                                if ($gConstants['optioncfs'])
                                {
                                    //update SHIPPINGRATESITES table
                                    self::updateShippingRateSites($shippingRateCode, $siteGroups);
                                }

                                if ($shippingRateID == 0)
                                {
                                    $shippingRateID = $id;
                                }

                                if ($firstID == 0)
                                {
                                    $firstID = $id;
                                    $recordID = $id;
                                }
                            }
                            else
                            {
                                // could not execute statement
                                // first check for a duplicate key (shipping rate code)
                                if ($stmt->errno == 1062)
                                {
                                    $result = 'str_ErrorShippingRateExists';
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'shippingRateUpdate execute ' . $dbObj->error;
                                }

                                break;
                            }
                        }
                        else
                        {
                            // could not bind parameters
                            $result = 'str_DatabaseError';
                            $resultParam = 'shippingRateUpdate bind ' . $dbObj->error;
                        }

                        $stmt->free_result();
                        $stmt->close();
                    }
                    else
                    {
                        // could not prepare statement
                        $result = 'str_DatabaseError';
                        $resultParam = 'shippingRateUpdate prepare ' . $dbObj->error;
                    }

                    $stmt = null;
                }

                // remove any old rates
                self::deleteRatesList($existingItemList);

                $dbObj->query('COMMIT');

                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'shippingRateUpdate connect ' . $dbObj->error;
            }
        }

        // convert the license key codes back into an array
        $itemList = Array();
        $groupArray = explode(',', $groupCodeList);
        $itemCount = count($groupArray);
        for ($i = 0; $i < $itemCount; $i++)
        {
            $item['recordid'] = 0;
            $item['shippingmethodcode'] = '';
            $item['shippingratecode'] = '';
            $item['productcode'] = '';
            $item['groupcode'] = $groupArray[$i];
            $item['isactive'] = true;
            array_push($itemList, $item);
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['recordid'] = $recordID;
        $resultArray['shippingratecode'] = $shippingRateCode;
        $resultArray['shippingmethodcode'] = $shippingMethodCode;
        $resultArray['shippingzonecode'] = $shippingZoneCode;
        $resultArray['productcode'] = $productCode;
        $resultArray['items'] = $itemList;
        $resultArray['groupcode'] = $parentCode;
        $resultArray['shippingrateinfo'] = $info;
        $resultArray['shippingrates'] = $shippingRates;
        $resultArray['ordervaluetype'] = $orderValueRange;
        $resultArray['orderminvalue'] = $orderMinValue;
        $resultArray['ordermaxvalue'] = $orderMaxValue;
        $resultArray['ordervalueincludesdiscount'] = $orderValueIncludesDiscount;
        $resultArray['isactive'] = $isActive;

        return $resultArray;
    }

    static function updateShippingRateSites($pShippingRateCode, $pSiteGroupCodeList)
    {
        $siteGroupArray = explode(',', $pSiteGroupCodeList);
        $itemCount = count($siteGroupArray);

        //get existing codes for that ship site
        $itemArray = self::getSiteGroupList($pShippingRateCode);
        $existingItemList = $itemArray['existingsitegroups'];

        for ($i = $itemCount - 1; $i >= 0; $i--)
        {
            $groupCode = $siteGroupArray[$i];
            $exisitingItemCount = count($existingItemList);

            for ($j = $exisitingItemCount - 1; $j >= 0; $j--)
            {
                if ($groupCode == $existingItemList[$j])
                {
                    array_splice($existingItemList, $j, 1);
                    array_splice($siteGroupArray, $i, 1);
                    break;
                }
            }
        }

        $insertCount = count($siteGroupArray);
        $removeCount = count($existingItemList);

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($removeCount > 0)
            {
                for ($i = 0; $i < $removeCount; $i++)
                {
                    if ($stmt = $dbObj->prepare('DELETE FROM `SHIPPINGRATESITES` WHERE (shippingratecode = ?) AND (sitegroupcode = ?)'))
                    {
                        if ($stmt->bind_param('ss', $pShippingRateCode, $existingItemList[$i]['sitegroupcode']))
                        {
                            $stmt->execute();
                        }
                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                }
            }

            if ($insertCount > 0)
            {
                for ($i = 0; $i < $insertCount; $i++)
                {
                    if ($stmt = $dbObj->prepare('INSERT INTO `SHIPPINGRATESITES` (`id`, `datecreated`, `shippingratecode`, `sitegroupcode`) VALUES (0, now(), ?, ?)'))
                    {
                        if ($stmt->bind_param('ss', $pShippingRateCode, $siteGroupArray[$i]))
                        {
                            $stmt->execute();
                        }
                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                }
            }
            $dbObj->close();
        }
    }

    static function getCodesList($pShippingRateCode, $pCompanyCode = '')
    {
        global $gSession;

        $resultArray = Array();
        $groupCodesArray = Array();
        $productCodesArray = Array();
        $existingCodesArray = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `productcode`, `groupcode` FROM `SHIPPINGRATES` WHERE `code` = ?'))
            {
                if ($stmt->bind_param('s', $pShippingRateCode))
                {
                    if ($stmt->bind_result($productCode, $groupCode))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
                                $codeItem['productcode'] = $productCode;
                                $codeItem['groupcode'] = $groupCode;
                                array_push($existingCodesArray, $codeItem);
                            }
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }

            switch ($gSession['userdata']['usertype'])
            {
                case TPX_LOGIN_SYSTEM_ADMIN:
                    // getting companies for the system administrator
                    $stmt = $dbObj->prepare('SELECT `id`, `groupcode`, `name`, `active` FROM `LICENSEKEYS` WHERE (`companycode` = ? OR `companycode` = "") ORDER BY `groupcode`');
                    $bindOK = $stmt->bind_param('s', $pCompanyCode);
                    break;
                case TPX_LOGIN_COMPANY_ADMIN:
                    // getting companies comboBox based on companycode of company administrator
                    $stmt = $dbObj->prepare('SELECT `id`, `groupcode`, `name`, `active` FROM `LICENSEKEYS` WHERE (`companycode` = ? OR `companycode` = "")  ORDER BY `groupcode`');
                    $bindOK = $stmt->bind_param('s', $gSession['userdata']['companycode']);
                    break;
            }

            if ($stmt)
            {
                if ($bindOK)
                {
                    if ($stmt->bind_result($groupCodeID, $groupCode, $groupName, $active))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
                                $codeItem['id'] = $groupCodeID;
                                $codeItem['code'] = $groupCode;
                                $codeItem['name'] = $groupName;
                                $codeItem['active'] = $active;
                                array_push($groupCodesArray, $codeItem);
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

        // add the default code
        array_unshift($groupCodesArray, Array('id' => '', 'code' => '', 'name' => '', 'active' => ''));

        $resultArray['productslist'] = DatabaseObj::getProductNamesList();
        $resultArray['groupcodes'] = $groupCodesArray;
        $resultArray['existingcodes'] = $existingCodesArray;
        $resultArray['shippingmethodslist'] = DatabaseObj::getShippingMethodsList();
        $resultArray['shippingzoneslist'] = DatabaseObj::getShippingZonesList('');

        return $resultArray;
    }

    static function getSiteGroupList($pShippingRateCode)
    {
        global $gSession;
        $resultArray = Array();
        $siteGroupsArray = Array();
        $existingSiteGroupsCodesArray = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `sitegroupcode` FROM `SHIPPINGRATESITES` WHERE `shippingratecode` = ?'))
            {
                if ($stmt->bind_param('s', $pShippingRateCode))
                {
                    if ($stmt->bind_result($exisitngSiteGroupCode))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
                                $codeItem['sitegroupcode'] = $exisitngSiteGroupCode;
                                array_push($existingSiteGroupsCodesArray, $codeItem);
                            }
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }

            if ($stmt = $dbObj->prepare('SELECT `id`, `code`, `name` FROM `SITEGROUPS` ORDER BY `code`'))
            {
                if ($stmt->bind_result($siteGroupID, $siteGroupCode, $siteGroupName))
                {
                    if ($stmt->execute())
                    {
                        while ($stmt->fetch())
                        {
                            $codeItem['id'] = $siteGroupID;
                            $codeItem['code'] = $siteGroupCode;
                            $codeItem['name'] = LocalizationObj::getLocaleString($siteGroupName, $gSession['browserlanguagecode'], true);
                            array_push($siteGroupsArray, $codeItem);
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }

        $resultArray['allsitegroups'] = $siteGroupsArray;
        $resultArray['existingsitegroups'] = $existingSiteGroupsCodesArray;

        return $resultArray;
    }

    static function displayInitialize($pShippingRateID)
    {
        $resultArray = Array();

        if ($pShippingRateID > -1)
        {
            $resultArray = self::getShippingRateRowFromID($pShippingRateID);
        }

        $itemArray = self::getItemList($pShippingRateID);
        $resultArray['items'] = $itemArray['items'];
        $companyCode = $resultArray['companycode'];

        $codesArray = self::getCodesList($resultArray['shippingratecode'], $companyCode);
        $resultArray['existinggroupcodes'] = $codesArray['existingcodes'];
        $resultArray['allgroupcodes'] = $codesArray['groupcodes'];

        $resultArray['productslist'] = $codesArray['productslist'];

        $siteGroupsArray = self::getSiteGroupList($resultArray['shippingratecode']);
        $resultArray['sitegroups'] = $siteGroupsArray['existingsitegroups'];
        $resultArray['allsitegroups'] = $siteGroupsArray['allsitegroups'];

        $resultArray['shippingmethodslist'] = DatabaseObj::getShippingMethodsList();
        $resultArray['shippingzoneslist'] = DatabaseObj::getShippingZonesList('');

        return $resultArray;
    }

    static function addInitialize()
    {
        return self::displayInitialize(0);
    }

    static function editInitialize()
    {
        $shippingRateID = $_GET['id'];

        return self::displayInitialize($shippingRateID);
    }

    static function getShippingRateRowFromID($pID)
    {
        $resultArray = Array();

        $shippingRateID = 0;
        $parentID = 0;
        $shippingRateCode = '';
        $shippingMethodCode = '';
        $productCode = '';
        $groupCode = '';
        $info = '';
        $shippingRates = '';
        $orderValueType = '';
        $orderMinValue = 0.00;
        $orderMaxValue = 0.00;
        $isActive = 0;
        $taxCode = '';
        $companyCode = '';
        $shippingZoneCode = '';
        $orderValueIncludesDiscount = '';
        $payInStoreAllowed = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $stmt = $dbObj->prepare('SELECT `id`, `parentid`, `companycode`, `code`, `shippingmethodcode`, `shippingzonecode`,
                                        `productcode`, `groupcode`, `info`, `rate`, `ordervaluetype`, `orderminimumvalue`,
                                        `ordermaximumvalue`, `ordervalueincludesdiscount`,`payinstoreallowed`, `taxcode`, `active`
                                    FROM `SHIPPINGRATES`
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
                                if ($stmt->bind_result($shippingRateID, $parentID, $companyCode, $shippingRateCode, $shippingMethodCode,
                                                $shippingZoneCode, $productCode, $groupCode, $info, $shippingRates, $orderValueType,
                                                $orderMinValue, $orderMaxValue, $orderValueIncludesDiscount, $payInStoreAllowed, $taxCode,
                                                $isActive))
                                {
                                    if (!$stmt->fetch())
                                    {
                                        $error = 'getShippingRateRowFromID fetch ' . $dbObj->error;
                                    }
                                }
                                else
                                {
                                    $error = 'getShippingRateRowFromID bind result ' . $dbObj->error;
                                }
                            }
                        }
                        else
                        {
                            $error = 'getShippingRateRowFromID store result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = 'getShippingRateRowFromID execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = 'getShippingRateRowFromID bind params ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $error = 'getShippingRateRowFromID prepare ' . $dbObj->error;
            }

            $dbObj->close();
        }

        $resultArray['recordid'] = $shippingRateID;
        $resultArray['parentid'] = $parentID;
        $resultArray['companycode'] = $companyCode;
        $resultArray['shippingratecode'] = $shippingRateCode;
        $resultArray['shippingmethodcode'] = $shippingMethodCode;
        $resultArray['shippingzonecode'] = $shippingZoneCode;
        $resultArray['productcode'] = $productCode;
        $resultArray['groupcode'] = $groupCode;
        $resultArray['shippingrateinfo'] = $info;
        $resultArray['shippingrates'] = $shippingRates;
        $resultArray['ordervaluetype'] = $orderValueType;
        $resultArray['orderminvalue'] = $orderMinValue;
        $resultArray['ordermaxvalue'] = $orderMaxValue;
        $resultArray['ordervalueincludesdiscount'] = $orderValueIncludesDiscount;
        $resultArray['payinstoreallowed'] = $payInStoreAllowed;
        $resultArray['taxcode'] = $taxCode;
        $resultArray['isactive'] = $isActive;

        return $resultArray;
    }

    static function shippingRateAdd()
    {
        $resultArray = self::shippingRateUpdate();
        if ($resultArray['recordid'] == 0)
        {
            $resultArray['recordid'] = -1;
        }

        return $resultArray;
    }

    static function shippingRateEdit()
    {
        return self::shippingRateUpdate();
    }

    static function deleteRatesList($pItemArray)
    {
        global $gSession;

        $itemCount = count($pItemArray);

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($itemCount > 0)
            {
                if ($stmt = $dbObj->prepare('DELETE FROM `SHIPPINGRATES` WHERE `id` = ?'))
                {
                    for ($i = 0; $i < $itemCount; $i++)
                    {
                        $recordID = $pItemArray[$i]['recordid'];
                        if ($stmt->bind_param('i', $recordID))
                        {
                            if ($stmt->execute())
                            {
                                DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                        $gSession['username'], 0, 'ADMIN', 'SHIPPINGRATE-DELETE',
                                        $pItemArray[$i]['recordid'] . ' ' . $pItemArray[$i]['shippingratecode'], 1);
                            }
                        }
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
            }
            $dbObj->close();
        }
    }

    static function shippingRateDelete()
    {
        global $gSession;

        $resutArray = Array();
        $result = '';
        $resultParam = '';

        $shippingRatesDeleted = Array();
        $shippingRatesNotUsed = Array();
        $allDeleted = 1;

        $shippingRateIDList = explode(',', $_POST['idlist']);
        $shippingRateIDCount = count($shippingRateIDList);
        $shippingRateCodeList = explode(',', $_POST['codelist']);


        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            for ($i = 0; $i < $shippingRateIDCount; $i++)
            {
                // first make sure the shipping rate hasn't been used
                $canDelete = true;
                if ($stmt = $dbObj->prepare('SELECT `id` FROM `ORDERSHIPPING` WHERE `shippingratecode` = ?'))
                {
                    if ($stmt->bind_param('s', $shippingRateCodeList[$i]))
                    {
                        if ($stmt->bind_result($recordID))
                        {
                            if ($stmt->execute())
                            {
                                if ($stmt->fetch())
                                {
                                    $allDeleted = 0;
                                    $result = 'str_ErrorUsedInOrder';
                                    $canDelete = false;
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
                    $dbObj->query('START TRANSACTION');

                    $itemArray = self::getItemList($shippingRateIDList[$i]);
                    self::deleteRatesList($itemArray['items']);

                    $dbObj->query('COMMIT');
                    array_push($shippingRatesDeleted, $shippingRateIDList[$i]);
                }
            }
        }
        $resultArray['result'] = $result;
        $resultArray['alldeleted'] = $allDeleted;
        $resultArray['shippingrateids'] = $shippingRatesDeleted;

        return $resultArray;
    }

    static function getShippingZonesFromCompanyCode()
    {
        global $gSession;

        $shippingZonesList = Array();

        switch ($gSession['userdata']['usertype'])
        {
            case TPX_LOGIN_SYSTEM_ADMIN:
                $companyCode = $_POST['companycode'];
                if ($companyCode == 'GLOBAL')
                {
                    $companyCode = '';
                }
                break;
            case TPX_LOGIN_COMPANY_ADMIN:
                $companyCode = $gSession['userdata']['companycode'];
                break;
        }

        $shippingZonesList = DatabaseObj::getShippingZonesFromCompanyCode($companyCode);

        return $shippingZonesList;
    }

    static function getProductsFromCompany()
    {
        global $gSession;

        $productsList = Array();

        switch ($gSession['userdata']['usertype'])
        {
            case TPX_LOGIN_SYSTEM_ADMIN:
                $companyCode = $_POST['companycode'];
                if ($companyCode == 'GLOBAL')
                {
                    $companyCode = '';
                }
                break;
            case TPX_LOGIN_COMPANY_ADMIN:
                $companyCode = $gSession['userdata']['companycode'];
                break;
        }
        $productsList = DatabaseObj::getProductNamesList($companyCode);

        return $productsList;
    }

    static function getLicenseKeyFromCompany()
    {
        global $gSession;

        $LicenseKeyList = Array();

        switch ($gSession['userdata']['usertype'])
        {
            case TPX_LOGIN_SYSTEM_ADMIN:
                $companyCode = $_POST['companycode'];
                if ($companyCode == 'GLOBAL')
                {
                    $companyCode = '';
                }
                break;
            case TPX_LOGIN_COMPANY_ADMIN:
                $companyCode = $gSession['userdata']['companycode'];
                break;
        }

        $shippingRateCode = $_GET['ratecode'];
        $LicenseKeyList = self::getCodesList($shippingRateCode, $companyCode);

        return $LicenseKeyList;
    }

}
?>
