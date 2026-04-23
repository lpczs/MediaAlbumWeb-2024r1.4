<?php

require_once('../Utils/UtilsDatabase.php');

class AdminComponentsPricing_model
{
    static function getComponentPriceRowByCode($pPricingID, $pComponentCode,  $pCompanyCode)
	{
	    // return an array containing the component price row which best matches the supplied parameters
        $result = '';
        $resultParam = '';
        $id = 0;
        $recordID = 0;
        $dateCreated = '';
        $companyCode = '';
        $categoryCode = '';
        $shoppingCartType = TPX_SHOPPINGCARTTYPE_INTERNAL;
        $pricingModel = 0;
		$price = '';
		$priceInfo = '';
		$priceListCode = '';
		$priceListName = '';
		$isPriceList = 0;
		$isActive = 0;
		$quantityDropisDropDown = 0;
		$linkedPriceListId = 0;
		$taxCode = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			// determine the best price match
			$sql = 'SELECT `priceid`, `priceinfo` FROM `PRICELINK` pl, `PRICES`pr
											WHERE (`pl`.`parentid`  = ?)
											  AND (`pl`.`componentcode` = ?)
											  AND (`pl`.`productcode` = "")
											  AND (`pl`.`companycode`   = ? OR `pl`.`companycode` = "")
											  AND (`pl`.`priceid` = `pr`.`id`)
											ORDER BY `pl`.`companycode` DESC, `productcode` DESC, `groupcode` DESC;';

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_param('iss', $pPricingID, $pComponentCode, $pCompanyCode))
				{
					if ($stmt->bind_result($id, $priceInfo))
					{
						if ($stmt->execute())
						{
							if (! $stmt->fetch())
							{
								// no matching price
								$id = 0;
							}
						}
						else
						{
							// could not execute statement
							$result = 'str_DatabaseError';
							$resultParam = __FUNCTION__.'1 execute ' . $dbObj->error;
						}
					}
					else
					{
						// could not bind result
						$result = 'str_DatabaseError';
						$resultParam = __FUNCTION__.'1 bind result ' . $dbObj->error;
					}
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = __FUNCTION__.'1 bind params ' . $dbObj->error;
				}
				$stmt->free_result();
				$stmt->close();
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = __FUNCTION__.'1 prepare ' . $dbObj->error;
			}
			$stmt = null;

			// only try and retrieve price row if there is an id
	        if ($id > 0)
	        {
				if ($stmt = $dbObj->prepare('SELECT * FROM `PRICES` WHERE (`id` = ?)'))
				{
					if ($stmt->bind_param('i', $id))
					{
						if ($stmt->bind_result($recordID, $dateCreated, $companyCode, $categoryCode, $linkedPriceListId, $pricingModel,
						$price, $priceListCode, $priceListLocalCode, $priceListName, $quantityDropisDropDown, $isPriceList, $taxCode, $isActive))
						{
							if ($stmt->execute())
							{
								if (! $stmt->fetch())
								{
									// no matching price
									$result = 'str_ErrorInvalidPrice';
								}
							}
							else
							{
								// could not execute statement
								$result = 'str_DatabaseError';
								$resultParam = __FUNCTION__.'2 execute ' . $dbObj->error;
							}
						}
						else
						{
							// could not bind result
							$result = 'str_DatabaseError';
							$resultParam = __FUNCTION__.'2 bind result ' . $dbObj->error;
						}
					}
					else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = __FUNCTION__.'2 bind params ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = __FUNCTION__.'2 prepare ' . $dbObj->error;
				}
	        }
			$dbObj->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'getComponentPriceRowByCode connect ' . $dbObj->error;
		}

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['recordid'] = $recordID;
        $resultArray['datecreated'] = $dateCreated;
        $resultArray['companycode'] = $companyCode;
        $resultArray['categorycode'] = $categoryCode;
        $resultArray['shoppingcarttype'] = $shoppingCartType;
        $resultArray['pricingmodel'] = $pricingModel;
        $resultArray['price'] = $price;
        $resultArray['priceinfo'] = $priceInfo;
        $resultArray['pricelistcode'] = $priceListCode;
		$resultArray['pricelistlocalcode'] = $priceListLocalCode;
        $resultArray['pricelistname'] = $priceListName;
        $resultArray['quantityisdropdown'] = $quantityDropisDropDown;
        $resultArray['ispricelist'] = $isPriceList;
        $resultArray['taxcode'] = $taxCode;
		$resultArray['isactive']    = $isActive;
        $resultArray['inheritparentqty'] = 0;
        $resultArray['allowinherit'] = 0;

        return $resultArray;
    }

    static function getItemList($pParentID)
    {
        $result = '';
        $resultParam = '';
        $resultArray = Array();
        $itemList = Array();
        $isActive = 0;
        if ($pParentID > 0)
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
            	if ($stmt = $dbObj->prepare('SELECT `id`, `companycode`, `groupcode`, `active` FROM `PRICELINK` WHERE `parentid` = ?'))
                {
                    if ($stmt->bind_param('i', $pParentID))
                    {
                        if ($stmt->bind_result($id, $companyCode, $groupCode, $isActive))
                        {
                            if ($stmt->execute())
                            {
                                while ($stmt->fetch())
                                {
                                    $item['id'] = $id;
                                    $item['companycode'] = $companyCode;
                                    $item['groupcode'] = $groupCode;
                                    $item['isactive'] = $isActive;
                                    array_push($itemList, $item);
                                }
                            }
                            else
                            {
                                // could not execute statement
                                $result = 'str_DatabaseError';
                                $resultParam = 'getItemList execute ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            // could not bind result
                            $result = 'str_DatabaseError';
                            $resultParam = 'getItemList bind result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'getItemList bind params ' . $dbObj->error;
                    }

                    $stmt->free_result();
                    $stmt->close();
                }

                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'getItemList connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['items'] = $itemList;
        $resultArray['pricelinkactive'] = $isActive;

        return $resultArray;
    }

    static function initialize()
    {
    	$resultArray = Array();

		$id = $_GET['id'];
	    $categoryCode = '';
	    $code = '';
	    $name = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
	    {
	        if ($stmt = $dbObj->prepare('SELECT `categorycode`, `localcode`, `name` FROM `COMPONENTS` WHERE `id` = ?'))
	        {
                if ($stmt->bind_param('i', $id))
                {
            		if ($stmt->execute())
					{
	                    if ($stmt->store_result())
	                    {
            				if ($stmt->num_rows > 0)
            				{
	            				if ($stmt->bind_result($categoryCode, $code, $name))
			                    {
		                            if (!$stmt->fetch())
		                            {
		                            	$error = 'initialize fetch ' . $dbObj->error;
		                            }
			                    }
			                    else
			                    {
			                    	$error = 'initialize bind result ' . $dbObj->error;
			                    }
            				}
		                }
		                else
		                {
		                	$error = 'initialize store result ' . $dbObj->error;
		                }
	                }
	                else
	                {
						$error = 'initialize execute ' . $dbObj->error;
	                }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
	        }
	        else
	        {
	        	$error = 'initialize prepare ' . $dbObj->error;
	        }
            $dbObj->close();
        }

	    $resultArray['categorycode'] = $categoryCode;
	    $resultArray['code'] = $code;
	    $resultArray['name'] = $name;

        return $resultArray;

    }

    static function getGridData()
    {
        global $gSession;

        $categoryCode = $_GET['category'];
        $componentCode = $_GET['code'];

        $resultArray = array();
		$resultArray['pricing'] = array();
		$resultArray['pricingmodel'] = $_GET['pricingmodel'];
		$result = '';
        $resultParam = '';

		$itemArray = array();

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			switch ($gSession['userdata']['usertype'])
			{
				case TPX_LOGIN_SYSTEM_ADMIN:
					$stmt = $dbObj->prepare('SELECT pl.id, pl.parentid, pl.companycode, pl.groupcode, pl.sortorder, pl.priceinfo, pl.pricedescription,
						pr.price, pr.active, pr.taxcode, tr.code, tr.rate FROM PRICELINK pl, PRICES pr LEFT JOIN `TAXRATES` tr ON
						`pr`.`taxcode` = `tr`.`code` WHERE pl.componentcode = ?  AND (pl.priceid = pr.id) AND (pr.categorycode = ?) AND
						(pl.productcode = "") GROUP BY pl.parentid');
					$bindOK = $stmt->bind_param('ss', $componentCode, $categoryCode);
				break;
				case TPX_LOGIN_COMPANY_ADMIN:
					$stmt = $dbObj->prepare('SELECT pl.id, pl.parentid, pl.companycode, pl.groupcode, pl.sortorder, pl.priceinfo, pl.pricedescription,
						pr.price, pr.active, pr.taxcode, tr.code, tr.rate FROM PRICELINK pl, PRICES pr LEFT JOIN `TAXRATES` tr ON
						`pr`.`taxcode` = `tr`.`code` WHERE pl.componentcode = ?  AND (pl.priceid = pr.id) AND (pr.categorycode = ?) AND
						(pl.productcode = "") AND (pl.companycode = "" OR pl.companycode = ?) ORDER BY pr.companycode');
					$bindOK = $stmt->bind_param('sss', $componentCode, $categoryCode, $gSession['userdata']['companycode']);
				break;

			}

			$stmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);

			if ($stmt)
            {
            	if ($bindOK)
				{
					if ($stmt->bind_result($id, $parentID, $priceCompanyCode, $groupCode, $sortOrder, $priceInfo, $priceDescription, $price,
							$active, $priceTaxCode, $taxRateCode, $taxRate))
					{
						if ($stmt->execute())
						{
							while ($stmt->fetch())
							{
							    $itemArray = self::getItemList($parentID);

							    $itemArray['id'] = $parentID;
								$itemArray['companycode'] = $priceCompanyCode;
								$itemArray['groupcode'] = $groupCode;
								$itemArray['sortorder'] = $sortOrder;
								$itemArray['priceinfo'] = $priceInfo;
								$itemArray['pricedescription'] = $priceDescription;
								$itemArray['price'] = $price;
								$itemArray['items'] = $itemArray['items'];
								$itemArray['active'] = $itemArray['pricelinkactive'];
								$itemArray['taxcode'] = $priceTaxCode;
								$itemArray['taxratecode'] = $taxRateCode;
								$itemArray['taxrate'] = $taxRate;

								array_push($resultArray['pricing'], $itemArray);
							}
						}
						else
						{
							// could not execute statement
							$result = 'str_DatabaseError';
							$resultParam = __FUNCTION__ . ' execute ' . $dbObj->error;
						}
					}
					else
					{
						// could not bind result
						$result = 'str_DatabaseError';
						$resultParam = __FUNCTION__ . ' bind result ' . $dbObj->error;
					}
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = __FUNCTION__ . ' bind params ' . $dbObj->error;
				}
				$stmt->free_result();
                $stmt->close();
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
			}
        	$dbObj->close();

		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = __FUNCTION__ . ' connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
	}


	static function getPriceListGridData()
    {
        global $gSession;

        $categoryCode = $_GET['category'];

        $resultArray = array();
		$resultArray['pricelists'] = array();
		$resultArray['pricingmodel'] = $_GET['pricingmodel'];
		$result = '';
        $resultParam = '';

		$itemArray = array();

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			switch ($gSession['userdata']['usertype'])
			{
				case TPX_LOGIN_SYSTEM_ADMIN:
					$stmt = $dbObj->prepare('SELECT * FROM PRICES pr LEFT JOIN TAXRATES tr ON `pr`.`taxcode` = `tr`.`code` WHERE `categorycode` = ? AND `ispricelist` = 1 ORDER BY `pr`.`companycode`, `pr`.`pricelistlocalcode`');
					$bindOK = $stmt->bind_param('s', $categoryCode);
				break;
				case TPX_LOGIN_COMPANY_ADMIN:
					$companyCode = $gSession['userdata']['companycode'];
					$stmt = $dbObj->prepare('SELECT * FROM PRICES pr LEFT JOIN TAXRATES tr ON `pr`.`taxcode` = `tr`.`code` WHERE `categorycode` = ? AND (`companycode` = ? OR `companycode` = "") AND (`ispricelist` = 1) ORDER BY `pr`.`companycode`, `pr`.`pricelistlocalcode`');
					$bindOK = $stmt->bind_param('ss', $categoryCode, $companyCode);
				break;

			}

			if ($stmt)
            {
            	if ($bindOK)
				{
					if ($stmt->bind_result($id, $dateCreated, $companyCode, $categoryCode, $linkedPriceListID, $pricingModel, $price,
						$priceListCode, $priceListLocalCode, $priceListName, $quantityDisplayType, $isPriceList, $taxCode, $active,
						$taxRateID, $taxRateDateCreated, $taxRateCode, $taxRateName, $taxRate))
					{
						if ($stmt->execute())
						{
							while ($stmt->fetch())
							{

							    $itemArray['id'] = $id;
							    $itemArray['datecreated'] = $dateCreated;
							    $itemArray['companycode'] = $companyCode;
								$itemArray['categorycode'] = $categoryCode;
								$itemArray['linkedpricelistid'] = $linkedPriceListID;
								$itemArray['pricingmodel'] = $pricingModel;
								$itemArray['price'] = $price;
								$itemArray['pricelistcode'] = $priceListCode;
								$itemArray['pricelistlocalcode'] = $priceListLocalCode;
								$itemArray['pricelistname'] = $priceListName;
								$itemArray['taxcode'] = $taxCode;
								$itemArray['active'] = $active;
								$itemArray['taxratecode'] = $taxRateCode;
								$itemArray['taxratename'] = $taxRateName;
								$itemArray['taxrate'] = $taxRate;

								array_push($resultArray['pricelists'], $itemArray);
							}
						}
						else
						{
							// could not execute statement
							$result = 'str_DatabaseError';
							$resultParam = __FUNCTION__ . ' execute ' . $dbObj->error;
						}
					}
					else
					{
						// could not bind result
						$result = 'str_DatabaseError';
						$resultParam = __FUNCTION__ . ' bind result ' . $dbObj->error;
					}
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = __FUNCTION__ . ' bind params ' . $dbObj->error;
				}
				$stmt->free_result();
                $stmt->close();
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
			}
        	$dbObj->close();

		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = __FUNCTION__ . ' connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        return $resultArray;
	}


	static function getCodesList($pComponentCode, $pID, $pCompanyCode)
    {
    	global $gSession;

    	$resultArray = Array();
        $groupCodesArray = Array();
        $existingCodesArray = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
        	if ($pID > 0)
            {
                if ($stmt = $dbObj->prepare('SELECT `groupcode` FROM `PRICELINK` WHERE (`componentcode` = ?) AND (`parentid` = ?)'))
                {
                    if ($stmt->bind_param('si', $pComponentCode, $pID))
                    {
                        if ($stmt->bind_result($groupCode))
                        {
                            if ($stmt->execute())
                            {
                                while ($stmt->fetch())
                                {
									$codeItem['groupcode'] = $groupCode;
									array_push($existingCodesArray, $codeItem);
                                }
                            }
                        }
                    }
                    $stmt->free_result();
                    $stmt->close();
                }
            }
            else
            {
				//if ($stmt = $dbObj->prepare('SELECT `groupcode` FROM `PRICELINK` WHERE `componentcode` = ?'))
				if ($stmt = $dbObj->prepare('SELECT `groupcode` FROM `LICENSEKEYS` WHERE `groupcode` NOT IN (SELECT `groupcode` FROM `PRICELINK` WHERE `productcode` = "" AND `companycode`= ? AND `componentcode` = ?) AND `companycode` = ?'))
				{
					if ($stmt->bind_param('sss', $pCompanyCode,$pComponentCode,$pCompanyCode))
					{
						if ($stmt->bind_result($groupCode))
						{
							if ($stmt->execute())
							{
								while ($stmt->fetch())
								{
									$codeItem['groupcode'] = $groupCode;
									array_push($existingCodesArray, $codeItem);
								}
							}
						}
					}

					$stmt->free_result();
					$stmt->close();
				}

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
	                if ($stmt->bind_result($id, $groupCode, $groupName, $active))
	                {
	                    if ($stmt->execute())
	                    {
	                        while ($stmt->fetch())
	                        {
	                        	$arrayItem['id'] = $id;
	                        	$arrayItem['code'] = $groupCode;
								$arrayItem['name'] = $groupName;
								$arrayItem['active'] = $active;
								array_push($groupCodesArray, $arrayItem);
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

        $resultArray['groupcodes'] = $groupCodesArray;

        $resultArray['existingcodes'] = $existingCodesArray;

        return $resultArray;
    }

    static function displayInitialize($pPricingID, $pPricingCompanyCode)
    {
        global $gSession;

        $componentID = $_GET['componentid'];
        $componentDataArray = DatabaseObj::getComponentByID($componentID);

        if ($pPricingID > -1)
        {
            if ($pPricingID > 0)
            {
                $priceArray = self::getComponentPriceRowByCode($pPricingID, $componentDataArray['code'], $pPricingCompanyCode);
            }
            else
            {
                $priceArray = DatabaseObj::getEmptyPriceRow();
            }

            $priceArray['componentid'] = $componentID;
            $priceArray['componentcode'] = $componentDataArray['code'];
            $priceArray['componentlocalcode'] = $componentDataArray['localcode'];
            $priceArray['componentname'] = $componentDataArray['name'];
            $priceArray['categorycode'] = $componentDataArray['categorycode'];
            $priceArray['componentcompanycode'] = $componentDataArray['companycode'];
        }

        $itemArray = self::getItemList($pPricingID);
        $priceArray['items'] = $itemArray['items'];

        $companyCode = $priceArray['companycode'];
        $codesArray = self::getCodesList($componentDataArray['code'], $pPricingID, $companyCode);

        $priceArray['existinggroupcodes'] = $codesArray['existingcodes'];
        $priceArray['allgroupcodes'] = $codesArray['groupcodes'];

        $priceArray['pricelinkparentid'] = $pPricingID;
		$priceArray['pricingmodel'] = $_GET['pricingmodel'];

        if ($pPricingID == 0)
        {
        	$priceArray['categorycode'] = $_GET['componentcategory'];
        }

        return $priceArray;
    }

    static function componentPricingAdd()
    {
		global $gSession;
		global $gConstants;

        $result = '';
        $resultParam = '';
		$pricesID = 0;
		$parentID = 0;
		$linkedPriceListID = 0;
		$quantityDisplayType = $_POST['quantitytypeisdropdown'];
		$inheritParentQty = UtilsObj::getPOSTParam('inheritparentqty', 0);
		$priceListLocalCode = 'CUSTOM';
		$priceListName = '';
		$isPriceList = 0;
		$priceListID = $_POST['pricelistid'];
        $productCode = '';
        $parentComponentCode = '';
        $sectionCode = $_POST['categorycode'];
        $sectionPath = '';
        $sortOrder = '';
        $shoppingCartType = TPX_SHOPPINGCARTTYPE_INTERNAL;
        $priceDescription = '';
        $isDefault = 0;
        $isVisible = 1;
		$categoryCode = $_POST['categorycode'];
		$componentCode = $_POST['componentcode'];
		$priceAdditionalInfo = html_entity_decode($_POST['priceadditionalinfo'], ENT_QUOTES);
		$price = $_POST['price'];
		$pricingModel = $_POST['pricingmodel'];
		$taxCode = $_POST['taxcode'];
		$isActive = $_POST['isactive'];
		$companyCode = '';

		switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				if ($gConstants['optionms'])
        		{
        			$companyCode = $_POST['companycode'];

					if ($companyCode == 'GLOBAL')
					{
						$companyCode = '';
					}
        		}
			break;
			case TPX_LOGIN_COMPANY_ADMIN:
				$companyCode = $gSession['userdata']['companycode'];
			break;
		}

		$groupcodes = $_POST['groupcodes'];

        $groupCodesArray = explode(',', $groupcodes);
        $groupCodesCount = count ($groupCodesArray);

        if ($companyCode == 'GLOBAL')
        {
        	$companyCode = '';
        }

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($priceListID == '-1')
            {
	            if ($stmt = $dbObj->prepare('INSERT INTO `PRICES` VALUES (0, now(), ?, ?, ?, ?, ?, CONCAT("CUSTOM", UNIX_TIMESTAMP(NOW()), SUBSTRING(MD5(RAND()) FROM 1 FOR 10)), ?, ?, ?, ?, ?, ?)'))
	            {
	                if ($stmt->bind_param('ssiisssiisi', $companyCode, $categoryCode, $linkedPriceListID, $pricingModel, $price, $priceListLocalCode, $priceListName, $quantityDisplayType, $isPriceList, $taxCode, $isActive))
	                {
	                    if ($stmt->execute())
	                    {
	                        $pricesID = $dbObj->insert_id;
							DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
							                            'ADMIN', 'CUSTOMPRICELIST-ADD', $pricesID . ' ' . $componentCode, 1);
	                    }
	                }
	                else
	                {
	                    // could not bind parameters
	                    $result = 'str_DatabaseError';
	                    $resultParam = 'componentPriceAdd bind ' . $dbObj->error;
	                }
					$stmt->free_result();
	                $stmt->close();
	                $stmt = null;
	            }
	            else
	            {
	                // could not prepare statement
	                $result = 'str_DatabaseError';
	                $resultParam = 'componentPriceAdd prepare ' . $dbObj->error;
	            }
            }
            else
            {
            	$pricesID = $priceListID;
            }

            for ($i=0; $i < $groupCodesCount; $i++)
            {
                if ($stmt = $dbObj->prepare('INSERT INTO `PRICELINK` (`datecreated`) VALUES (now())'))
	            {
                    if ($stmt->execute())
                    {
                        $recordID = $dbObj->insert_id;

                        if ($parentID == 0)
                        {
                        	$parentID = $recordID;
                        }

						$sql = 'UPDATE `PRICELINK` SET
									`parentid` = ?, `companycode` = ?, `productcode` = ?, `groupcode` = ?, `componentcode` = ?,
									`parentpath` = ?, `sectioncode` = ?, `sortorder` = ?, `shoppingcarttype` = ?, `priceid` = ?,
									`priceinfo` = ?, `pricedescription` = ?, `inheritparentqty` = ?, `isdefault` = ?, `isvisible` = ?,
									`active` = ?
								WHERE
									`id` = ?';

                        if ($stmt2 = $dbObj->prepare($sql))
			            {
		                    if ($stmt2->bind_param('issss' . 'ssiii' . 'ssiii' . 'i' . 'i',
													$parentID, $companyCode, $productCode, $groupCodesArray[$i], $componentCode,
													$parentComponentCode, $sectionCode, $sortOrder, $shoppingCartType, $pricesID,
													$priceAdditionalInfo, $priceDescription, $inheritParentQty, $isDefault, $isVisible,
													$isActive,
													$recordID))
    						{
			                    if ($stmt2->execute())
			                    {
                                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
			                            'ADMIN', 'PRICE-ADD', $pricesID . ' ' . $componentCode, 1);
			                    }
    						}

							$stmt2->free_result();
			                $stmt2->close();
			                $stmt2 = null;
			            }
                    }

					$stmt->free_result();
	                $stmt->close();
	                $stmt = null;
	            }
            }


            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'componentAdd connect ' . $dbObj->error;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        $resultArray['id'] = $parentID;
        $resultArray['company'] = $companyCode;
        $resultArray['groupcodes'] = $groupCodesArray;
		$resultArray['price'] = $price;
		$resultArray['pricingmodel'] = $pricingModel;
        $resultArray['isactive'] = $isActive;

        return $resultArray;
    }

    static function componentPriceListAdd()
    {
		global $gSession;
		global $gConstants;

        $result = '';
        $resultParam = '';
        $recordID = 0;
		$linkedPriceListID = 0;
		$quantityDisplayType = $_POST['quantitytypeisdropdown'];
		$categoryCode = $_POST['categorycode'];
		$priceListName = html_entity_decode($_POST['name'], ENT_QUOTES);
		$isPriceList = 1;
		$price = $_POST['price'];
		$pricingModel = $_POST['pricingmodel'];
		$taxCode = $_POST['taxcode'];
		$isActive = $_POST['isactive'];
		$priceListLocalCode	= strtoupper($_POST['componentpricelistcode']);
        $companyCode = '';

		switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				if ($gConstants['optionms'])
        		{
        			$companyCode = $_POST['companycode'];

					if ($companyCode == 'GLOBAL')
					{
						$companyCode = '';
					}
        		}
			break;
			case TPX_LOGIN_COMPANY_ADMIN:
				$companyCode = $gSession['userdata']['companycode'];
			break;
		}

		if($companyCode != '')
		{
			$priceListCode = $companyCode.".".$priceListLocalCode;
		}
		else
		{
			$priceListCode = $priceListLocalCode;
		}


        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('INSERT INTO `PRICES` VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
            {
                if ($stmt->bind_param('ssiissssiisi', $companyCode, $categoryCode, $linkedPriceListID, $pricingModel, $price, $priceListCode, $priceListLocalCode,$priceListName, $quantityDisplayType, $isPriceList, $taxCode, $isActive))
                {
                    if ($stmt->execute())
                    {
						$recordID = $dbObj->insert_id;
						DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
						                            'ADMIN', 'PRICELIST-ADD', $categoryCode . ' ' . $priceListCode, 1);
                    }
                    else
                    {
                    // could not execute statement
						// first check for a duplicate key (currency code)
						if ($stmt->errno == 1062)
						{
							$result = 'str_ErrorPricelistExists';
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
                    $resultParam = 'componentPriceAdd bind ' . $dbObj->error;
                }
				$stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $result = 'str_DatabaseError';
                $resultParam = 'componentPriceAdd prepare ' . $dbObj->error;
            }

            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'componentAdd connect ' . $dbObj->error;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        $resultArray['id'] = $recordID;
        $resultArray['company'] = $companyCode;
        $resultArray['categorycode'] = $categoryCode;
		$resultArray['linkedpricelistid'] = $linkedPriceListID;
		$resultArray['pricingmodel'] = $pricingModel;
		$resultArray['price'] = $price;
		$resultArray['pricelistcode'] = $priceListCode;
		$resultArray['pricelistlocalcode'] = $priceListLocalCode;
		$resultArray['pricelistname'] = $priceListName;
		$resultArray['ispricelist'] = $isPriceList;
        $resultArray['isactive'] = $isActive;

        return $resultArray;
    }

    static function priceListEditDisplay()
    {
    	global $gSession;
		global $gConstants;

        $result = '';
        $resultParam = '';
		$resultArray = Array();

		$companyCode = '';
		$pricingModel = 0;
		$price = '';
		$quantityIsDropdown = 0;
		$priceListCode = '';
		$priceListLocalCode = '';
		$priceListName = '';
		$taxCode = '';
		$active = 0;

    	$priceListID = $_GET['pricelistid'];

    	$dbObj = DatabaseObj::getGlobalDBConnection();

	    if ($dbObj)
	    {
	        if ($stmt = $dbObj->prepare('SELECT `companycode`, `pricingmodel`, `price`, `quantityisdropdown`, `pricelistcode`, `pricelistlocalcode`, `pricelistname`, `taxcode`, `active` FROM `PRICES` WHERE `id` = ?'))
	        {
                if ($stmt->bind_param('i', $priceListID))
                {
	               	if ($stmt->execute())
                    {
            			if ($stmt->store_result())
						{
               				if ($stmt->num_rows > 0)
							{
			                    if ($stmt->bind_result($companyCode, $pricingModel, $price, $quantityIsDropdown, $priceListCode, $priceListLocalCode, $priceListName, $taxCode, $active))
			                    {
		                            if (!$stmt->fetch())
		                            {
		                            	$error = 'priceListEditDisplay fetch ' . $dbObj->error;
		                            }
			                    }
			                    else
			                    {
			                    	$error = 'priceListEditDisplay bind result ' . $dbObj->error;
			                    }
			                }
		                }
		                else
		                {
							$error = 'priceListEditDisplay store result ' . $dbObj->error;
		                }
                    }
                    else
                    {
						$error = 'priceListEditDisplay execute ' . $dbObj->error;
                    }
                }
                else
                {
                	$error = 'priceListEditDisplay bind params ' . $dbObj->error;
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
	        }
	        else
	        {
	        	$error = 'priceListEditDisplay prepare ' . $dbObj->error;
	        }
            $dbObj->close();
        }

        $resultArray['id'] = $priceListID;
        $resultArray['companycode'] = $companyCode;
	    $resultArray['pricingmodel'] = $pricingModel;
	    $resultArray['price'] = $price;
	    $resultArray['quantityisdropdown'] = $quantityIsDropdown;
	    $resultArray['pricelistcode'] = $priceListCode;
	    $resultArray['pricelistlocalcode'] = $priceListLocalCode;
	    $resultArray['pricelistname'] = $priceListName;
	    $resultArray['decimalplaces'] = $_GET['decimalplaces'];
	    $resultArray['taxcode'] = $taxCode;
	    $resultArray['active'] = $active;

  		return $resultArray;
    }

    static function componentPricingEdit()
    {
		global $gSession;
		global $gConstants;

        $result = '';
        $resultParam = '';
		$resultArray = Array();
		$pricesID = 0;

		$companyCode = '';

		switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				if ($gConstants['optionms'])
        		{
        			$companyCode = $_POST['companycode'];

					if ($companyCode == 'GLOBAL')
					{
						$companyCode = '';
					}
        		}
			break;
			case TPX_LOGIN_COMPANY_ADMIN:
				$companyCode = $gSession['userdata']['companycode'];
			break;
		}

		$priceListSelectionID = $_POST['pricelistid'];
		$componentCode = $_POST['componentcode'];
		$inIsPriceList = $_POST['inispricelist'];
		$inPriceListID = $_POST['inpricelistid'];
		$inPriceLinkID = $_POST['inpricelinkid'];
		$priceAdditionalInfo = html_entity_decode($_POST['priceadditionalinfo'], ENT_QUOTES);
		$price = $_POST['price'];
		$taxCode = $_POST['taxcode'];
		$isActive = $_POST['isactive'];
		$groupcodes = $_POST['groupcodes'];
		$productCode = '';
		$parentComponentCode = '';
		$sectionCode = $_POST['categorycode'];
		$sectionPath = '';
		$sortOrder = '';
		$shoppingCartType = TPX_SHOPPINGCARTTYPE_INTERNAL;
		$priceDescription = '';
		$isDefault = 0;
		$isVisible = 0;

		$linkedPriceListID = 0;
		$quantityDisplayType = $_POST['quantitytypeisdropdown'];
		$inheritParentQty = UtilsObj::getPOSTParam('inheritparentqty', 0);
		$categoryCode = $_POST['categorycode'];
		$pricingModel = $_POST['pricingmodel'];
		$priceListLocalCode = 'CUSTOM';
		$priceListName = '';
		$isPriceList = 0;

        $groupCodesArray = explode(',', $groupcodes);
   		$groupCodesCount = count($groupCodesArray);

   		$itemArray = self::getItemList($inPriceLinkID);
        $existingItemList = $itemArray['items'];
        $existingGroupCodeCount = count($existingItemList);

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $dbObj->query('START TRANSACTION');

        	if ($priceListSelectionID == '-1')
        	{
        		// check to see if the price is now a custom price and previously was not using a price list
        		if ($inIsPriceList == '0')
        		{
	    			if ($stmt = $dbObj->prepare('UPDATE `PRICES` SET `price` = ? , `active` = ?, `quantityisdropdown` = ?, `taxcode` = ? WHERE `id` = ?'))
		            {
	                    if ($stmt->bind_param('siisi', $price, $isActive, $quantityDisplayType, $taxCode, $inPriceListID))
						{
		                	if ($stmt->execute())
		                	{
		                		$pricesID = $inPriceListID;
		                	}
						}

						$stmt->free_result();
		                $stmt->close();
		                $stmt = null;
		            }
        		}
        		else
        		{
	        		// price is now custom but was previously using a price list.
	        		if ($stmt = $dbObj->prepare('INSERT INTO `PRICES` VALUES (0, now(), ?, ?, ?, ?, ?, CONCAT("CUSTOM", UNIX_TIMESTAMP(NOW()), SUBSTRING(MD5(RAND()) FROM 1 FOR 10)), ?, ?, ?, ?, ?, ?)'))
		            {
		                if ($stmt->bind_param('ssiisssiisi', $companyCode, $categoryCode, $linkedPriceListID, $pricingModel, $price, $priceListLocalCode, $priceListName,$quantityDisplayType, $isPriceList, $taxCode, $isActive))
		                {
		                    if ($stmt->execute())
		                    {
		                        $pricesID = $dbObj->insert_id;
		                    }
		                }

						$stmt->free_result();
		                $stmt->close();
		                $stmt = null;
		            }

        		}
        	}
        	else
        	{
        		// check to see if the price is now going to be using a price list. If the previous price was a custom price delete the custom price record.
        		if ($inIsPriceList == '0')
        		{
        			if ($stmt = $dbObj->prepare('DELETE FROM `PRICES` WHERE `id` = ?'))
		            {
		                if ($stmt->bind_param('i', $inPriceListID))
		                {
		                    if ($stmt->execute())
							{
								$pricesID = $priceListSelectionID;
							}
		                }
						$stmt->free_result();
		                $stmt->close();
		                $stmt = null;
		            }
        		}
        		else
        		{
        			$pricesID = $inPriceListID;
        		}
        	}

            // update pricelink table adding/removing pricelink records with new price records.
	        for ($i = 0; $i < $groupCodesCount; $i++)
		    {
		        $groupCode = $groupCodesArray[$i];
		        $insertNewPriceLink = true;
		        $existingItemCount = count($existingItemList);

		        for ($j = 0; $j < $existingItemCount; $j++)
		        {
		            if ($groupCode == $existingItemList[$j]['groupcode'])
		            {
		                $insertNewPriceLink = false;
		                array_splice($existingItemList, $j, 1);
		                break;
		            }
		        }

		        if ($insertNewPriceLink)
				{
					$stmt = $dbObj->prepare('INSERT INTO `PRICELINK`
						(`id`, `datecreated`, `parentid`, `companycode`, `productcode`, `linkedproductcode`, `groupcode`, `componentcode`, `parentpath`, `sectionpath`, `sectioncode`, `sortorder`,
							`shoppingcarttype`, `priceid`, `priceinfo`, `pricedescription`, `inheritparentqty`, `isdefault`, `isvisible`, `active`)
						VALUES (0, now(), ?, ?, ?, "", ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
				}
				else
				{
					$stmt = $dbObj->prepare('UPDATE `PRICELINK` SET `companycode` = ?, `groupcode` = ?, `sortorder` = ?, `priceid` = ?, `priceinfo` = ?, `inheritparentqty` = ?, `active` = ? WHERE `parentid` = ? AND `groupcode` = ?');
				}

				if ($stmt)
				{
					if ($insertNewPriceLink)
					{
						$bindOK = $stmt->bind_param('isssssssiiissiiii', $inPriceLinkID, $companyCode, $productCode, $groupCode, $componentCode, $parentComponentCode, $sectionPath, $sectionCode, $sortOrder, $shoppingCartType,
							$pricesID, $priceAdditionalInfo, $priceDescription, $inheritParentQty, $isDefault, $isVisible,  $isActive);
					}
					else
					{
						$bindOK = $stmt->bind_param('ssiisiiis', $companyCode, $groupCode, $sortOrder, $pricesID, $priceAdditionalInfo, $inheritParentQty, $isActive, $inPriceLinkID, $groupCode);
					}

					if ($bindOK)
					{
						if ($stmt->execute())
						{
							if ($insertNewPriceLink)
							{
								$id = $dbObj->insert_id;

								DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                        		'ADMIN', 'COMPONENTPRICELINK-ADD', $id . ' ' . 'parentid= '.$inPriceLinkID . ' - ' . ($groupCode == '' ? 'DEFAULT' : $groupCode), 1);
							}
						}
					}
					else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = 'componentPriceLinkUpdate bind ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = 'componentPriceLinkUpdate prepare ' . $dbObj->error;
				}

	        	$stmt = null;
		    }

		    // remove any old rates
        	self::deletePriceLink($inPriceLinkID, $existingItemList);

		    $dbObj->query('COMMIT');

            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'componentPriceLinkUpdate connect ' . $dbObj->error;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        $resultArray['id'] = $inPriceLinkID;
        $resultArray['company'] = $companyCode;
        $resultArray['groupcodes'] = $groupCodesArray;
		$resultArray['price'] = $price;
		$resultArray['pricingmodel'] = $pricingModel;
        $resultArray['isactive'] = $isActive;

        return $resultArray;
    }

    static function componentPriceListEdit()
    {
    	global $gSession;
		global $gConstants;

        $result = '';
        $resultParam = '';
        $priceListID = $_GET['id'];
		$linkedPriceListID = 0;
		$priceListCode = strtoupper($_POST['componentpricelistcode']);
		$priceListName = html_entity_decode($_POST['name'], ENT_QUOTES);
		$isPriceList = 1;
		$price = $_POST['price'];
		$taxCode = $_POST['taxcode'];
		$isActive = $_POST['isactive'];
        $companyCode = '';
		$quantityDisplayType = $_POST['quantitytypeisdropdown'];

		switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				if ($gConstants['optionms'])
        		{
        			$companyCode = $_POST['companycode'];

					if ($companyCode == 'GLOBAL')
					{
						$companyCode = '';
					}
        		}
			break;
			case TPX_LOGIN_COMPANY_ADMIN:
				$companyCode = $gSession['userdata']['companycode'];
			break;
		}

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('UPDATE `PRICES` SET `price` = ?, `quantityisdropdown` = ?, `pricelistcode` = ?, `pricelistname` = ?, `taxcode` = ?, `active` = ? WHERE `id` = ? '))
            {
                if ($stmt->bind_param('sisssii', $price, $quantityDisplayType, $priceListCode, $priceListName, $taxCode, $isActive, $priceListID))
                {
                    if ($stmt->execute())
                    {
						DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
						                            'ADMIN', 'PRICELIST-EDIT', $priceListCode, 1);
                    }
                }
                else
                {
                    // could not bind parameters
                    $result = 'str_DatabaseError';
                    $resultParam = 'componentPriceAdd bind ' . $dbObj->error;
                }
				$stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $result = 'str_DatabaseError';
                $resultParam = 'componentPriceAdd prepare ' . $dbObj->error;
            }

            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'componentAdd connect ' . $dbObj->error;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        $resultArray['id'] = $priceListID;
        $resultArray['company'] = $companyCode;
		$resultArray['linkedpricelistid'] = $linkedPriceListID;
		$resultArray['price'] = $price;
		$resultArray['pricelistcode'] = $priceListCode;
		$resultArray['pricelistname'] = $priceListName;
		$resultArray['ispricelist'] = $isPriceList;
        $resultArray['isactive'] = $isActive;

        return $resultArray;
    }

    static function deletePriceLink($pParentID, $pItemArray)
    {
        global $gSession;

        $itemCount = count($pItemArray);

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
	        if ($itemCount > 0)
	        {
	            if ($stmt = $dbObj->prepare('DELETE FROM `pricelink` WHERE `parentid` = ? AND `groupcode` = ?'))
	            {
	                for ($i = 0; $i < $itemCount; $i++)
	                {
	                   $groupCode = $pItemArray[$i]['groupcode'];
	                    if ($stmt->bind_param('is', $pParentID, $groupCode))
	                    {
	                        if ($stmt->execute())
	                        {
                                DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                    'ADMIN', 'PRICELINK-DELETE', $pItemArray[$i]['groupcode'] . ' ' . $pItemArray[$i]['groupcode'], 1);
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

    static function priceListDelete()
    {
        global $gSession;

		$result = '';
		$priceListsNotUsed = array();
		$priceListsDeleted = array();
		$allDeleted = 1;
        $priceListIDS = $_POST['idlist'];
        $priceListCodes = $_POST['codelist'];

        $priceListArray = explode(',', $priceListIDS);
        $priceListCodesArray = explode(',', $priceListCodes);

        $itemCount = count($priceListArray);

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
	        if ($itemCount > 0)
	        {
	            for ($i = 0; $i < $itemCount; $i++)
	            {
		            // first make sure the tax rate hasn't been used
		            if ($stmt = $dbObj->prepare('SELECT `id` FROM `PRICELINK` WHERE `priceid` = ?'))
		            {
		                if ($stmt->bind_param('i', $priceListArray[$i]))
		                {
		                    if ($stmt->bind_result($recordID))
		                    {
		                       if ($stmt->execute())
		                       {
		                            if ($stmt->fetch())
		                            {
		                                $result = 'str_ErrorPriceListUsedInPricing';
		                                $canDelete = false;
		                                $allDeleted = 0;
		                            }
		                            else
		                            {
		                            	$canDelete = true;
		                            	$item['id'] = $priceListArray[$i];
		                            	$item['code'] = $priceListCodesArray[$i];
										array_push($priceListsNotUsed, $item);
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
			            if ($stmt = $dbObj->prepare('DELETE FROM `PRICES` WHERE `id` = ?'))
			            {
			                if ($stmt->bind_param('i', $priceListArray[$i]))
			                {
			                    if ($stmt->execute())
			                    {
                                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
			                                'ADMIN', 'PRICELINK-DELETE', $priceListArray[$i] . ' ' . $priceListCodesArray[$i], 1);
			                                array_push($priceListsDeleted, $priceListArray[$i]);
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
        }

        $resultArray['alldeleted'] = $allDeleted;
        $resultArray['pricelistids'] = $priceListsDeleted;
        $resultArray['result'] = $result;

        return $resultArray;
    }

    static function defaultPriceDelete()
    {
    	global $gSession;

		$result = '';
		$priceID = 0;
		$priceLinkNotUsed = array();
		$priceListsDeleted = array();
		$allDeleted = 1;
        $priceLinkIDS = $_POST['idlist'];
        $removePriceList = false;

        $priceLinkArray = explode(',', $priceLinkIDS);

        $itemCount = count($priceLinkArray);

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
	        if ($itemCount > 0)
	        {
	            for ($i = 0; $i < $itemCount; $i++)
	            {
		            //$priceID = $priceLinkArray[$i];

		            //First check to see if the default price belongs to a component that is attached to a product
		            if ($stmt = $dbObj->prepare('SELECT `ispricelist`, `priceid` FROM PRICES pr, PRICELINK pl WHERE pl.parentid = ? AND (pl.priceid = pr.id)'))
		            {
		                if ($stmt->bind_param('i', $priceLinkArray[$i]))
		                {
		                    if ($stmt->bind_result($isPriceList, $priceID))
		                    {
		                       if ($stmt->execute())
		                       {
		                       		if ($stmt->fetch())
		                       		{
		                       			if ($isPriceList == 0)
		                       			{
		                       				$removePriceList = true;
		                       			}
		                       		}
		                       }
		                    }
		                }
		            	$stmt->free_result();
		            	$stmt->close();
		            	$stmt = null;
		            }

		            // first make sure the price hasn't been used on a product
		            if ($stmt = $dbObj->prepare('SELECT `id` FROM `PRICELINK` WHERE `priceid` = ? AND `productcode` <> ""'))
		            {
		                if ($stmt->bind_param('i', $priceID))
		                {
		                    if ($stmt->bind_result($recordID))
		                    {
		                       if ($stmt->execute())
		                       {
		                            if ($stmt->fetch())
		                            {
		                                $result = 'str_ErrorPriceListUsedInPricing';
		                                $canDelete = false;
		                                $allDeleted = 0;
		                            }
		                            else
		                            {
		                            	$canDelete = true;
		                            	$item['id'] = $priceLinkArray[$i];
										array_push($priceLinkNotUsed, $item);
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
			            // get all the licensekey in the pricelink records being deleted.
                        $licenseKeyCodeResult = DatabaseObj::getLicenseKeyCodeFromPriceLink($priceID);

                        //First delete the PRICELINK record and then if successful then delete the price record from the PRICES table
			            if ($stmt = $dbObj->prepare('DELETE FROM `PRICELINK` WHERE `parentid` = ?'))
			            {
			                if ($stmt->bind_param('i', $priceLinkArray[$i]))
			                {
			                    if ($stmt->execute())
			                    {
			                        if ($removePriceList)
			                    	{
				                        if ($stmt2 = $dbObj->prepare('DELETE FROM `PRICES` WHERE `id` = ?'))
							            {
							                if ($stmt2->bind_param('i', $priceID))
							                {
							                    $stmt2->execute();

							                }

							                $stmt2->free_result();
								            $stmt2->close();
								            $stmt2 = null;
							            }
			                    	}


			                        DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
			                                'ADMIN', 'COMPONENT-DEFAULT-PRICE-DELETE', $priceLinkArray[$i], 1);
			                                array_push($priceListsDeleted, $priceLinkArray[$i]);

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
	   	 }

        $resultArray['alldeleted'] = $allDeleted;
        $resultArray['pricelinkids'] = $priceListsDeleted;
        $resultArray['result'] = $result;

        return $resultArray;
    }

    static function componentPricingActivate()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
		$resultParam = '';

        $ids = $_POST['ids'];
        $idList = explode(',',$ids);
        $active = $_POST['active'];
        $priceIdArray = Array();

        if ($active != '0') $active = 1;

        $itemCount = count($idList);

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $dbObj->query('START TRANSACTION');

            if ($stmt = $dbObj->prepare('UPDATE `PRICELINK` SET `active` = ? WHERE `parentid` = ?'))
            {
                for($i=0; $i < $itemCount; $i++)
        		{
	                if ($stmt->bind_param('ii', $active, $idList[$i]))
	                {
	                    if ($stmt->execute())
	                    {
	                        if ($active == 1)
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                    'ADMIN', 'COMPONENTPRICELINK-DEACTIVATE', 'PARENTID = '.$idList[$i] , 1);
	                        }
	                        else
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                    'ADMIN', 'COMPONENTPRICELINK-ACTIVATE','PARENTID = '. $idList[$i], 1);
	                        }
	                    }
	                    else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'componentDefaultPriceActivate execute ' . $dbObj->error;
						}

	                    $resultArray[$i]['recordid'] = $idList[$i];
	                    $resultArray[$i]['isactive'] = $active;
	                }
	                else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'componentDefaultPriceActivate bind ' . $dbObj->error;
					}
            	}

                $stmt->free_result();
	            $stmt->close();
	            $stmt = null;
            }
			else
			{
				$result = 'str_DatabaseError';
				$resultParam = 'componentDefaultPriceActivate prepare ' . $dbObj->error;
			}

			if ($result == '')
			{
				if ($stmt = $dbObj->prepare('SELECT `priceid` FROM `PRICELINK` WHERE `id` = ?'))
				{
					for($i=0; $i < $itemCount; $i++)
					{
						if ($stmt->bind_param('i', $idList[$i]))
						{
							if ($stmt->bind_result($priceId))
							{
								if ($stmt->execute())
								{
									$stmt->fetch();
									$priceIdArray[] = $priceId;
								}
							}
						}
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
			}

			$priceIdCount = count($priceIdArray);

			if ($stmt = $dbObj->prepare('UPDATE `PRICES` SET `active` = ? WHERE `id` = ?'))
            {
                for($i=0; $i < $priceIdCount; $i++)
        		{
	                if ($stmt->bind_param('ii', $active, $priceIdArray[$i]))
	                {
	                    if ($stmt->execute())
	                    {

	                    }
	                    else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'componentDefaultPriceActivate prices execute ' . $dbObj->error;
						}
	                }
	                else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'componentDefaultPriceActivate prices bind ' . $dbObj->error;
					}
            	}

                $stmt->free_result();
	            $stmt->close();
	            $stmt = null;
            }
			else
			{
				$result = 'str_DatabaseError';
				$resultParam = 'componentDefaultPriceActivate prices prepare ' . $dbObj->error;
			}

			if ($result == '')
			{
				$dbObj->query('COMMIT');
			}

            $dbObj->close();
        }

        return $resultArray;
    }


    static function activatePriceList()
    {
        global $gSession;

        $resultArray = Array();
        $ids = $_POST['ids'];
        $idList = explode(',',$ids);
        $active = $_POST['active'];

        if ($active != '0') $active = 1;

        $itemCount = count($idList);

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('UPDATE `PRICES` SET `active` = ? WHERE `id` = ?'))
            {
                for($i=0; $i < $itemCount; $i++)
        		{
	                if ($stmt->bind_param('ii', $active, $idList[$i]))
	                {
	                    if ($stmt->execute())
	                    {
	                        if ($active == 1)
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                    'ADMIN', 'PRICELIST-DEACTIVATE', 'PRICEID = '.$idList[$i] , 1);
	                        }
	                        else
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                    'ADMIN', 'PRICELIST-ACTIVATE','PRICEID = '.$idList[$i], 1);
	                        }
	                    }

	                    $resultArray[$i]['recordid'] = $idList[$i];
	                    $resultArray[$i]['isactive'] = $active;
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

    static function addInitialize()
    {
        return self::displayInitialize(0, '');
    }

    static function editInitialize()
    {
        $pricingID = $_GET['pricingid'];
        $priceCompanyCode = $_GET['pricecompanycode'];
        return self::displayInitialize($pricingID, $priceCompanyCode);
    }

    static function getLicenseKeyFromCompany()
    {
    	global $gSession;

    	$licenseKeyList = Array();

		switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				$companyCode = $_REQUEST['companycode'];
			break;
			case TPX_LOGIN_COMPANY_ADMIN:
				$companyCode = $gSession['userdata']['companycode'];
			break;
		}

		$componentcode = $_GET['componentcode'];
		$pricingID = $_GET['id'];

		$licenseKeyList = self::getCodesList($componentcode, $pricingID, $companyCode);

		return $licenseKeyList;
    }


}
?>
