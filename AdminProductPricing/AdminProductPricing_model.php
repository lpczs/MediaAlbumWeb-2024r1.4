<?php

require_once('../Utils/UtilsDatabase.php');

class AdminProductPricing_model
{
   static function getProductPriceRowByCode($pParentID, $pProductCode, $pProductCompanyCode)
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
		$linkedPriceListId = 0;
		$quantityDisplayType = 0;
		$priceDescription = '';
		$taxCode = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			// determine the best price match
			if ($stmt = $dbObj->prepare('SELECT `priceid`, `priceinfo`, `pricedescription`, `shoppingcarttype`, `pl`.`active` FROM `PRICELINK` pl, `PRICES`pr
											WHERE (`pl`.`parentid`  = ?)
											  AND (`pl`.`productcode` = ?)
											  AND (`pl`.`companycode`   = ? OR `pl`.`companycode` = "")
											  AND (`pl`.`priceid` = `pr`.`id`)
											ORDER BY `pl`.`companycode` DESC, `productcode` DESC, `groupcode` DESC;'))
			{
				if ($stmt->bind_param('sss', $pParentID, $pProductCode, $pProductCompanyCode))
				{
					if ($stmt->bind_result($id, $priceInfo, $priceDescription, $shoppingCartType, $priceLinkActive))
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
						$price, $priceListCode, $priceListLocalCode, $priceListName, $quantityDisplayType, $isPriceList, $taxCode, $isActive))
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
        $resultArray['pricedescription'] = $priceDescription;
        $resultArray['quantityisdropdown'] = $quantityDisplayType;

        $resultArray['pricelistcode'] = $priceListCode;
        $resultArray['pricelistlocalcode'] = $priceListLocalCode;
        $resultArray['pricelistname'] = $priceListName;
        $resultArray['ispricelist'] = $isPriceList;
        $resultArray['taxcode'] = $taxCode;
		$resultArray['isactive'] = $priceLinkActive;

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
            	if ($stmt = $dbObj->prepare('SELECT `id`, `companycode`, `productcode`, `groupcode`, `active` FROM `PRICELINK` WHERE `parentid` = ?'))
                {
                    if ($stmt->bind_param('i', $pParentID))
                    {
                        if ($stmt->bind_result($id, $companyCode, $productCode, $groupCode, $isActive))
                        {
                            if ($stmt->execute())
                            {
                                while ($stmt->fetch())
                                {
                                    $item['id'] = $id;
                                    $item['companycode'] = $companyCode;
                                    $item['productcode'] = $productCode;
                                    $item['groupcode'] = $groupCode;
                                    $item['isactive'] = $isActive;
                                    array_push($itemList, $item);
                                }
                            }
                            else
                            {
                                // could not execute statement
                                $result = 'str_DatabaseError';
                                $resultParam = 'productpricinggetitemlist execute ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            // could not bind result
                            $result = 'str_DatabaseError';
                            $resultParam = 'productpricinggetitemlist bind result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'productpricinggetitemlist bind params ' . $dbObj->error;
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
                $resultParam = 'productpricinggetitemlist connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['items'] = $itemList;
        $resultArray['pricelinkactive'] = $isActive;

        return $resultArray;
    }

    static function displayGrid()
    {
    	$productID = $_GET['id'];

    	$productArray = DatabaseObj::getProductFromID($productID);

    	return $productArray;

    }

    static function getGridData()
    {
        global $gSession;

        $result = '';
        $resultParam = '';
        $pricingList = Array();

        $productID = $_GET['id'];

        $productArray = DatabaseObj::getProductFromID($productID);
        $productCode = $productArray['code'];

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
           switch ($gSession['userdata']['usertype'])
			{
				case TPX_LOGIN_SYSTEM_ADMIN:
					// getting products for the system administrator
					$stmt = $dbObj->prepare('SELECT pl.id, pl.parentid, pl.companycode, pl.groupcode, pl.shoppingcarttype, pl.priceinfo, pl.pricedescription,
					pr.price, pl.active, pr.taxcode, tr.code, tr.rate FROM PRICELINK pl, PRICES pr LEFT JOIN `TAXRATES` tr ON `pr`.`taxcode` = `tr`.`code`
					WHERE (pl.productcode = ?) AND (pl.componentcode = "") AND (pl.priceid = pr.id) GROUP BY pl.parentid ORDER BY `companycode`');
					$bindOK = $stmt->bind_param('s', $productCode);
				break;
				case TPX_LOGIN_COMPANY_ADMIN:
					// getting products based on companycode of company administrator
					$stmt = $dbObj->prepare('SELECT pl.id, pl.parentid, pl.companycode, pl.groupcode, pl.shoppingcarttype, pl.priceinfo, pl.pricedescription,
					pr.price, pl.active, pr.taxcode, tr.code, tr.rate FROM PRICELINK pl, PRICES pr LEFT JOIN `TAXRATES` tr ON `pr`.`taxcode` = `tr`.`code`
                	WHERE (pl.productcode = ?) AND (pl.componentcode = "") AND (pl.priceid = pr.id) AND (pl.companycode = ? OR pl.companycode = "")
                	GROUP BY pl.parentid ORDER BY `companycode`');
					$bindOK = $stmt->bind_param('ss', $productCode, $gSession['userdata']['companycode']);
				break;
			}

			$stmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);

            if ($stmt)
            {
                if ($bindOK)
                {
                    if ($stmt->bind_result($id, $parentid, $companyCode, $groupCode, $shoppingCartType, $productInfo, $priceDescription, $price,
                    	$isActive, $priceTaxCode, $taxRateCode, $taxRate))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
                                $itemArray = self::getItemList($parentid);

                                $priceItem['recordid'] = $id;
                                $priceItem['parentid'] = $parentid;
                                $priceItem['companycode'] = $companyCode;
                                $priceItem['groupcode'] = $groupCode;
                                $priceItem['shoppingcarttype'] = $shoppingCartType;
                                $priceItem['productinfo'] = $productInfo;
                                $priceItem['pricedescription'] = $priceDescription;
                                $priceItem['price'] = $price;
                                $priceItem['isactive'] = $isActive;
                                $priceItem['taxcode'] = $priceTaxCode;
								$priceItem['taxratecode'] = $taxRateCode;
								$priceItem['taxrate'] = $taxRate;
                                $priceItem['items'] = $itemArray['items'];

                                array_push($pricingList, $priceItem);
                            }
                        }
                        else
                        {
                            // could not execute statement
							$result = 'str_DatabaseError';
							$resultParam = 'productpricingdisplayList execute ' . $dbObj->error;
						}
                    }
                    else
                    {
                        // could not bind result
                        $result = 'str_DatabaseError';
                        $resultParam = 'productpricingdisplayList bind result ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not bind parameters
                    $result = 'str_DatabaseError';
                    $resultParam = 'productpricingdisplayList bind params ' . $dbObj->error;
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
            $resultParam = 'productpricingdisplayList connect ' . $dbObj->error;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $productID;
        $resultArray['code'] = $productCode;
        $resultArray['name'] = $productArray['name'];
        $resultArray['pricing'] = $pricingList;

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
					$stmt = $dbObj->prepare('SELECT * FROM PRICES pr LEFT JOIN TAXRATES tr ON `pr`.`taxcode` = `tr`.`code` WHERE `categorycode` = ? AND `ispricelist` = 1 ORDER BY `companycode`, `pricelistlocalcode`');
					$bindOK = $stmt->bind_param('s', $categoryCode);
				break;
				case TPX_LOGIN_COMPANY_ADMIN:
					$stmt = $dbObj->prepare('SELECT * FROM PRICES pr LEFT JOIN TAXRATES tr ON `pr`.`taxcode` = `tr`.`code` WHERE `categorycode` = ? AND (`ispricelist` = 1) AND (`companycode` = ? OR `companycode` = "") ORDER BY `companycode`, `pricelistlocalcode`');
					$bindOK = $stmt->bind_param('ss', $categoryCode, $gSession['userdata']['companycode']);
				break;

			}

			if ($stmt)
            {
            	if ($bindOK)
				{
					if ($stmt->bind_result($id, $dateCreated, $companyCode, $categoryCode, $linkedPriceListID, $pricingModel, $price,
						$priceListCode, $priceListLocalCode, $priceListName, $quantityDisplayType, $isPriceList, $taxCode, $active, $taxRateID, $taxRateDateCreated,
						$taxRateCode, $taxRateName, $taxRate))
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


    static function pricingActivate()
    {
        global $gSession;

        $resultArray = Array();
        $ids = $_POST['ids'];
        $idList = explode(',',$ids);
        $active = $_POST['active'];

        if ($active != '0')
		{
			$active = 1;
		}

        $itemCount = count($idList);

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
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
	                                    'ADMIN', 'PRODUCTPRICELINK-DEACTIVATE', 'PARENTID = '.$idList[$i] , 1);
	                        }
	                        else
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                    'ADMIN', 'PRODUCTPRICELINK-ACTIVATE','PARENTID = '.$idList[$i], 1);
	                        }
                        }

	                    $resultArray[$i]['id'] = $idList[$i];
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

    static function activatePriceList()
    {
        global $gSession;

        $resultArray = Array();
        $ids = $_POST['ids'];
        $idList = explode(',',$ids);
        $active = $_POST['active'];

		if ($active != '0')
		{
			$active = 1;
		}

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

    static function getGroupCodeList($pProductCode, $pPricingID, $pComponentCode, $pCompanyCode)
    {
        global $gSession;

        $resultArray = Array();
        $groupCodesArray = Array();
        $existingCodesArray = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($pPricingID > 0)
            {
                if ($stmt = $dbObj->prepare('SELECT `groupcode` FROM LICENSEKEYS where `groupcode` NOT IN (SELECT `groupcode` FROM `PRICELINK` WHERE `productcode` = ? AND `companycode` = ? AND `componentcode` = ? AND (`parentid` = ?)) AND `companycode` = ?'))
                {
                    if ($stmt->bind_param('sssis', $pProductCode, $pCompanyCode, $pComponentCode, $pPricingID,$pCompanyCode))
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
                if ($stmt = $dbObj->prepare('SELECT `groupcode` FROM `LICENSEKEYS` WHERE `groupcode` NOT IN (SELECT `groupcode` FROM `PRICELINK` WHERE `productcode` = ? AND `companycode`= ? AND `componentcode` = ?) AND `companycode` = ?'))
                {
                    if ($stmt->bind_param('ssss', $pProductCode, $pCompanyCode, $pComponentCode, $pCompanyCode))
                    {
                        if ($stmt->bind_result($groupCode))
                        {
                            if ($stmt->execute())
                            {
                                while ($stmt->fetch())
                                {
	                        		$arrayItem['groupcode'] = $groupCode;
									array_push($existingCodesArray, $arrayItem);
                                }
                            }
                        }
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
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

        $resultArray['existinggroupcodes'] = $existingCodesArray;
        $resultArray['groupcodes'] = $groupCodesArray;

        return $resultArray;
    }

    static function displayInitialize($pPricingID, $pPricingCompanyCode)
    {
        global $gSession;

        $collectionType = TPX_PRODUCT_TYPE_PHOTO_BOOK;
        $productID = $_GET['productid'];

        $productArray = DatabaseObj::getProductFromID($productID);

		$dbObj = DatabaseObj::getGlobalDBConnection();

	    if ($dbObj)
	    {
			$stmt = $dbObj->prepare('SELECT `pcl`.`collectiontype` FROM `PRODUCTCOLLECTIONLINK` `pcl`
									  LEFT JOIN `PRODUCTS` `pr` ON `pcl`.`productcode` = `pr`.`code` WHERE `pr`.`id` = ?');
	        if ($stmt)
	        {
                if ($stmt->bind_param('i', $productID))
                {
					if ($stmt->execute())
					{
            			if ($stmt->store_result())
						{
            				if ($stmt->num_rows > 0)
							{
			                    if ($stmt->bind_result($collectionType))
			                    {
		                            if (! $stmt->fetch())
		                            {
		                            	$error = 'priceEditDisplay fetch ' . $dbObj->error;
		                            }
			                    }
			                    else
			                    {
			                    	$error = 'priceEditDisplay bind result ' . $dbObj->error;
			                    }
			                }
		                }
		                else
		                {
		                	$error = 'priceEditDisplay store result ' . $dbObj->error;
		                }
	                }
	                else
	                {
						$error = 'priceEditDisplay execute' . $dbObj->error;
	                }
                }
                else
                {
                	$error = 'priceEditDisplay bind param ' . $dbObj->error;
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
	        }
	        else
	        {
	        	$error = 'priceEditDisplay prepare ' . $dbObj->error;
	        }
            $dbObj->close();
        }

        if ($pPricingID > -1)
        {
            if ($pPricingID > 0)
            {
                $priceArray = self::getProductPriceRowByCode($pPricingID, $productArray['code'],  $pPricingCompanyCode);
            }
            else
            {
                $priceArray = DatabaseObj::getEmptyProductPriceRow();
            }

            $priceArray['productid'] = $productID;
            $priceArray['productcode'] = $productArray['code'];
            $priceArray['productname'] = $productArray['name'];
            $priceArray['productcompanycode'] = $productArray['companycode'];

        }

        $priceArray['pricelinkparentid'] = $pPricingID;

        $itemArray = self::getItemList($pPricingID);
        $priceArray['items'] = $itemArray['items'];

        $priceArray['companycode'] = $pPricingCompanyCode;
        $codesArray = self::getGroupCodeList($productArray['code'], $pPricingID, '', $pPricingCompanyCode);

        if ($pPricingID == 0)
        {
        	$priceArray['pricingmodel'] = TPX_PRICINGMODEL_PERQTY;
        	$priceArray['priceinfo'] = '';
        	$priceArray['pricedescription'] = '';
        }

        $priceArray['producttype'] = $collectionType;
        $priceArray['existinggroupcodes'] = $codesArray['existinggroupcodes'];
        $priceArray['allgroupcodes'] = $codesArray['groupcodes'];
        $priceArray['inheritparentqty'] = 0;
        $priceArray['allowinherit'] = 0;

        return $priceArray;
    }

    static function priceListEditDisplay()
    {
		$resultArray = Array();

		$companyCode = '';
		$pricingModel = 0;
		$price = '';
		$priceListCode = '';
		$priceListLocalCode = '';
		$priceListName = '';
		$quantityIsDropwDown = 0;
		$taxCode = '';
		$active = 0;

    	$priceListID = $_GET['pricelistid'];

    	$dbObj = DatabaseObj::getGlobalDBConnection();

	    if ($dbObj)
	    {
			$stmt = $dbObj->prepare('SELECT `companycode`, `pricingmodel`, `price`, `pricelistcode`, `pricelistlocalcode`,`pricelistname`, `quantityisdropdown`, `taxcode`, `active` FROM `PRICES` WHERE `id` = ?');
	        if ($stmt)
	        {
                if ($stmt->bind_param('i', $priceListID))
                {
					if ($stmt->execute())
					{
            			if ($stmt->store_result())
						{
            				if ($stmt->num_rows > 0)
							{
			                    if ($stmt->bind_result($companyCode, $pricingModel, $price, $priceListCode, $priceListLocalCode, $priceListName, $quantityIsDropwDown, $taxCode, $active))
			                    {
		                            if (! $stmt->fetch())
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
						$error = 'priceListEditDisplay execute' . $dbObj->error;
	                }
                }
                else
                {
                	$error = 'priceListEditDisplay bind param ' . $dbObj->error;
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
	    $resultArray['pricelistcode'] = $priceListCode;
	    $resultArray['pricelistlocalcode'] = $priceListLocalCode;
	    $resultArray['pricelistname'] = $priceListName;
	    $resultArray['quantityisdropdown'] = $quantityIsDropwDown;
	    $resultArray['taxcode'] = $taxCode;
	    $resultArray['active'] = $active;

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

    static function pricingAdd()
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
        $productCode = $_POST['productcode'];
        $parentComponentCode = '';
        $sectionCode = $_POST['categorycode'];
        $sortOrder = '';
        $shoppingCartType = TPX_SHOPPINGCARTTYPE_INTERNAL;
        $isDefault = 0;
        $isVisible = 1;
		$categoryCode = $_POST['categorycode'];
		$componentCode = '';
		$priceAdditionalInfo = html_entity_decode($_POST['priceadditionalinfo'], ENT_QUOTES);
		$priceDescription = html_entity_decode($_POST['pricedescription'], ENT_QUOTES);
		$price = $_POST['price'];
		$pricingModel = $_POST['pricingmodel'];
		$priceLinkActive = $_POST['isactive'];
		$priceListActive = 1;
		$companyCode = '';
		$taxCode = $_POST['taxcode'];

		switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				if ($gConstants['optionms'])
        		{
        			$companyCode = $_POST['company'];

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

		if ($gConstants['optionscbo'])
		{
			 $shoppingCartType = $_POST['useexternalshoppingcart'];
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
	                if ($stmt->bind_param('ssiisssiisi', $companyCode, $categoryCode, $linkedPriceListID, $pricingModel, $price, $priceListLocalCode, $priceListName, $quantityDisplayType, $isPriceList, $taxCode, $priceListActive))
	                {
	                    if ($stmt->execute())
	                    {
	                        $pricesID = $dbObj->insert_id;
							DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
							                            'ADMIN', 'CUSTOMPRICELIST-ADD', $pricesID . ' ' . $componentCode, 1);
	                    }
	                    else
	                    {
							// could not bind parameters
							$result = 'str_DatabaseError';
							$resultParam = 'componentPriceAdd execute ' . $dbObj->error;
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

                        if ($stmt2 = $dbObj->prepare('UPDATE `PRICELINK` SET `parentid` = ?, `companycode` = ?, `productcode` = ?, `groupcode` = ?, `componentcode` = ?, ' .
                  						'`parentpath` = ?, `sectioncode` = ?, `sortorder` = ?, `shoppingcarttype` = ?, `priceid` = ?, `priceinfo` = ?, `pricedescription` = ?, `inheritparentqty` = ?, `isdefault` = ?, `isvisible` = ?, `active` = ? WHERE `id` = ?'))
			            {
		                    if ($stmt2->bind_param('issssssiiissiiiii', $parentID, $companyCode, $productCode, $groupCodesArray[$i], $componentCode, $parentComponentCode, $sectionCode, $sortOrder, $shoppingCartType, $pricesID, $priceAdditionalInfo, $priceDescription, $inheritParentQty, $isDefault, $isVisible, $priceLinkActive, $recordID))
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
        $resultArray['isactive'] = $priceLinkActive;

        return $resultArray;
    }

    static function productPriceListAdd()
    {
		global $gSession;
		global $gConstants;

        $result = '';
        $resultParam = '';
		$linkedPriceListID = 0;
		$quantityDisplayType = $_POST['quantitytypeisdropdown'];
		$categoryCode = $_POST['categorycode'];
		$priceListName = html_entity_decode($_POST['name'], ENT_QUOTES);
		$isPriceList = 1;
		$price = $_POST['price'];
		$pricingModel = $_POST['pricingmodel'];
		$isActive = $_POST['isactive'];
		$priceListLocalCode	= strtoupper($_POST['code']);
		$taxCode = $_POST['taxcode'];
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
                if ($stmt->bind_param('ssiissssiisi', $companyCode, $categoryCode, $linkedPriceListID, $pricingModel, $price, $priceListCode, $priceListLocalCode, $priceListName, $quantityDisplayType, $isPriceList, $taxCode, $isActive))
                {
                    if ($stmt->execute())
                    {
						$recordID = $dbObj->insert_id;
						DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
						                            'ADMIN', 'PRICELIST-ADD', $categoryCode . ' ' . $priceListCode, 1);
                    }else
                    {
						// could not execute statement
						// first check for a duplicate key
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

    static function pricingEdit()
    {
		global $gSession;
		global $gConstants;

        $result = '';
        $resultParam = '';
		$resultArray = Array();
		$pricesID = 0;
		$companyCode = '';
		$priceListSelectionID = $_POST['pricelistid'];
		$componentCode = '';
		$inIsPriceList = $_POST['inispricelist'];
		$inPriceListID = $_POST['inpricelistid'];
		$inPriceLinkID = $_POST['inpricelinkid'];
		$priceAdditionalInfo = html_entity_decode($_POST['priceadditionalinfo'], ENT_QUOTES);
		$priceDescription = html_entity_decode($_POST['pricedescription'], ENT_QUOTES);
		$price = $_POST['price'];
		$priceLinkActive = $_POST['isactive'];
		$groupcodes = $_POST['groupcodes'];
		$productCode = $_POST['productcode'];
		$parentComponentCode = '';
		$sectionPath = '';
		$sectionCode = $_POST['categorycode'];
		$sortOrder = '';
		$shoppingCartType = TPX_SHOPPINGCARTTYPE_INTERNAL;
		$isDefault = 0;
		$isVisible = 0;
		$quantityDisplayType = $_POST['quantitytypeisdropdown'];
		$inheritParentQty = UtilsObj::getPOSTParam('inheritparentqty', 0);
		$linkedPriceListID = 0;
		$categoryCode = $_POST['categorycode'];
		$pricingModel = $_POST['pricingmodel'];
		$priceListLocalCode = 'CUSTOM';
		$priceListName = '';
		$isPriceList = 0;
		$priceListActive = 1;
		$taxCode = $_POST['taxcode'];

		switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				if ($gConstants['optionms'])
        		{
        			$companyCode = $_POST['company'];

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

		if ($gConstants['optionscbo'])
		{
			 $shoppingCartType = $_POST['useexternalshoppingcart'];
		}

        $groupCodesArray = explode(',', $groupcodes);
   		$groupCodesCount = count ($groupCodesArray);

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
	    			if ($stmt = $dbObj->prepare('UPDATE `PRICES` SET `price` = ? , `quantityisdropdown` = ?, `taxcode` = ? WHERE `id` = ?'))
		            {
	                    if ($stmt->bind_param('sisi', $price, $quantityDisplayType,$taxCode, $inPriceListID))
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
		                if ($stmt->bind_param('ssiisssiisi', $companyCode, $categoryCode, $linkedPriceListID, $pricingModel, $price, $priceListLocalCode,$priceListName, $quantityDisplayType, $isPriceList, $taxCode, $priceListActive))
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
        			$pricesID = $priceListSelectionID;
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
					$stmt = $dbObj->prepare('INSERT INTO `PRICELINK` (`id`, `datecreated`, `parentid`, `companycode`, `productcode`, `linkedproductcode`, `groupcode`, `componentcode`, 
						`parentpath`, `sectionpath`, `sectioncode`, `sortorder`, `shoppingcarttype`, `priceid`, `priceinfo`, `pricedescription`, `inheritparentqty`, `isdefault`, `isvisible`, `active`)
						VALUES (0, now(), ?, ?, ?, "", ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
				}
				else
				{
					$stmt = $dbObj->prepare('UPDATE `PRICELINK` SET `companycode` = ?, `groupcode` = ?, `sortorder` = ?, `priceid` = ?, `priceinfo` = ?, `pricedescription` = ?, `shoppingcarttype` = ?, `inheritparentqty` = ?, `active` = ? WHERE `parentid` = ? AND `groupcode` = ?');
				}

				if ($stmt)
				{
					if ($insertNewPriceLink)
					{
						$bindOK = $stmt->bind_param('isssssssiiissiiii', $inPriceLinkID, $companyCode, $productCode, $groupCode, $componentCode, $parentComponentCode, $sectionPath, $sectionCode, $sortOrder, $shoppingCartType,
							$pricesID, $priceAdditionalInfo, $priceDescription, $inheritParentQty, $isDefault, $isVisible, $priceLinkActive);
					}
					else
					{
						$bindOK = $stmt->bind_param('ssiissiiiis', $companyCode, $groupCode, $sortOrder, $pricesID, $priceAdditionalInfo, $priceDescription, $shoppingCartType, $inheritParentQty, $priceLinkActive, $inPriceLinkID, $groupCode);
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
        $resultArray['isactive'] = $priceLinkActive;

        return $resultArray;
    }

    static function productPriceListEdit()
    {
    	global $gSession;
		global $gConstants;

        $result = '';
        $resultParam = '';
        $priceListID = $_GET['id'];
		$linkedPriceListID = 0;
		$priceListCode = strtoupper($_POST['code']);
		$priceListName = html_entity_decode($_POST['name'], ENT_QUOTES);
		$isPriceList = 1;
		$price = $_POST['price'];
		$isActive = $_POST['isactive'];
		$quantityDisplayType = $_POST['quantitytypeisdropdown'];
		$taxCode = $_POST['taxcode'];
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

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('UPDATE `PRICES` SET `quantityisdropdown` = ? , `price` = ?, `pricelistcode` = ?, `pricelistname` = ?, `taxcode` = ?, `active` = ? WHERE `id` = ? '))
            {
                if ($stmt->bind_param('issssii', $quantityDisplayType, $price, $priceListCode, $priceListName, $taxCode, $isActive, $priceListID))
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
        $resultArray['quantitytypeisdropdown'] = $quantityDisplayType;
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
	            if ($stmt = $dbObj->prepare('DELETE FROM `PRICELINK` WHERE `parentid` = ? AND `groupcode` = ?'))
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

    static function deletePricingList($pDBObj, $pItemArray)
    {
        global $gSession;

        $resutArray = Array();
        $allDeleted = 1;

        $itemCount = count($pItemArray);

        if ($itemCount > 0)
        {
            if ($stmt = $pDBObj->prepare('DELETE FROM `PRICELINK` WHERE `id` = ?'))
            {
                for ($i = 0; $i < $itemCount; $i++)
                {
                    if ($stmt->bind_param('i', $pItemArray[$i]['id']))
                    {
                        if ($stmt->execute())
                        {
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                    'ADMIN', 'PRODUCTPRICING-DELETE', $pItemArray[$i]['id'] . ' ' .
                                    ($pItemArray[$i]['groupcode'] == '' ? 'DEFAULT' : $pItemArray[$i]['groupcode']) . ' - ' . ($pItemArray[$i]['productcode'] == '' ? 'DEFAULT' : $pItemArray[$i]['productcode']), 1);

                        }
                        else
                        {
                        	$allDeleted = 0;
	                    	$result = 'str_DatabaseError';
                        }
                    }

                    $stmt->free_result();
                }

                $stmt->close();
            }
        }

        $resultArray['alldeleted'] = $allDeleted;

        return $resultArray;
    }

    static function pricingDelete()
    {
        global $gSession;

        $resultArray = Array();
        $pricingList = explode(',', $_POST['idlist']);
        $itemCount = count($pricingList);

        $licenseKeyListFromPost = explode('<br>', $_POST['lkeylist']);
        $licenseKeyList = array_unique($licenseKeyListFromPost);
        $licenseKeyCount = count($licenseKeyList);

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $dbObj->query('START TRANSACTION');

            for ($i = 0; $i < $itemCount; $i++)
            {
                $itemArray = self::getItemList($pricingList[$i]);
                $allDeleted = self::deletePricingList($dbObj, $itemArray['items']);
            }

            $dbObj->query('COMMIT');

            $dbObj->close();
        }

    	$resultArray['alldeleted'] = $allDeleted;
        $resultArray['pricingids'] = $pricingList;

        return $resultArray;

    }

    static function productPriceListDelete()
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
		                                $result = 'str_ErrorPriceListUsedInProduct';
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

    static function getLicenseKeyFromCompany()
    {
    	global $gSession;

    	$LicenseKeyList = Array();

		switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				$companyCode = $_REQUEST['companycode'];
			break;
			case TPX_LOGIN_COMPANY_ADMIN:
				$companyCode = $gSession['userdata']['companycode'];
			break;
		}

		$productCode = $_GET['productcode'];
		$pricingID = $_GET['id'];
		$componetCode = '';

		$LicenseKeyList = self::getGroupCodeList($productCode, $pricingID, $componetCode, $companyCode);

		return $LicenseKeyList;
    }

}

?>
