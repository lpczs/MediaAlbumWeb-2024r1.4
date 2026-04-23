<?php

require_once(__DIR__ . '/../AdminTaopixOnlineFontLists/AdminTaopixOnlineFontLists_model.php');

class AdminProducts_model
{
    static function getGridData()
	{
		global $gSession;

	    $start = 0;
	    $limit = 100;
	    $resultArray = Array();
	    $params = Array();
	    $sortby = 'code';
	    $dir = 'ASC';
		$i = 0;
		$hideInactive = 0;

	    if (isset($_POST['start']))
		{
			$start = (int)$_POST['start'];
		}

		if (isset($_POST['limit']))
		{
			$limit = (int)$_POST['limit'];
		}

		if (isset($_POST['sort']))
		{
			$sortby = $_POST['sort'];
		}

		if (isset($_POST['hideInactive']))
		{
			$hideInactive = filter_input(INPUT_POST,'hideInactive', FILTER_SANITIZE_NUMBER_INT);
		}

	 	switch ($sortby)
	 	{
			case 'companycode':
				$sort = 'companycode';
				break;
			case 'active':
				$sort = 'active';
				break;
			case 'productcollection':
				$sort = 'productcollection';
				break;
			case 'resourcecode':
				$sort = 'resourcecode';
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


        $stmt = 'SELECT
					`p`.`id`,
                    `p`.`companycode`,
                    `p`.`code`,
                    "" as `categoryname`,
                    `p`.`name`,
                    `p`.`active`,
                    (SELECT
                        GROUP_CONCAT( `pcl`.`collectionname` ORDER BY `pcl`.`collectioncode` SEPARATOR "\n")
                        FROM `PRODUCTCOLLECTIONLINK` pcl
                        LEFT JOIN `APPLICATIONFILES` af ON `af`.`ref` = `pcl`.`collectioncode`
                            WHERE (`pcl`.`productcode` = `p`.`code`) AND (`af`.`type` = 0) AND (`af`.`deleted`=0)
                    ) as `productcollection`,
					`psrl`.`resourcecode`
                FROM
                    `PRODUCTS` as p
				LEFT JOIN
					`PRODUCTONLINESYSTEMRESOURCELINK` psrl ON (`psrl`.`productcode` = `p`.`code`)
                WHERE
					(`psrl`.`type` IS NULL OR `psrl`.`type` = ' . TPX_SYSTEM_RESOURCE_TYPE_3DMODEL . ')
				AND
                    `deleted` = 0 ';

        if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
        {
            // getting products based on companycode of company administrator
            $stmt .= ' AND (`p`.`companycode` = ? OR `p`.`companycode` = "")';
            $params[] = $gSession['userdata']['companycode'];
        }

		$searchFields = UtilsObj::getPOSTParam('fields');

		//  getting search filter fields
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
					$stmt .= $operator.'(`'.$value.'` LIKE ?)';
					$i++;
				}
				$stmt .= ')';
				$bind = 1;
			}
			else
			{
				// if hide inactive button clicked add where to exclude inactive products
				if ($hideInactive)
				{
					$stmt .= ' AND (`p`.`active` = 1)';
				}

				if ($gSession['userdata']['usertype'] == TPX_LOGIN_SYSTEM_ADMIN )
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
			// if hide inactive button clicked add where to exclude inactive products
			if ($hideInactive)
			{
				$stmt .= ' AND (`p`.`active` = 1)';
			}

			$params = Array();

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

        $orderBy = ' ORDER BY `companycode`, `' . $sort . '` ' . $dir . ' LIMIT ' . $limit . ' OFFSET ' . $start . ';';

        $productArray = self::bindParams($stmt, $params, $bind, $orderBy);

        $resultArray['products'] = $productArray['products'];
        $resultArray['total'] = $productArray['total'];

        return $resultArray;
	}

    static function productActivate()
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
            if ($stmt = $dbObj->prepare('UPDATE `PRODUCTS` SET `active` = ? WHERE `id` = ?'))
            {
                for($i=0; $i < $itemCount; $i++)
                {
	                if ($stmt->bind_param('ii', $active, $idList[$i]))
	                {
	                    if ($stmt->execute())
	                    {
	                        $productDataArray = DatabaseObj::getProductFromID($idList[$i]);
	                        if ($productDataArray['isactive'] == 0)
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                    'ADMIN', 'PRODUCT-DEACTIVATE', $idList[$i] . ' ' . $productDataArray['code'], 1);
	                        }
	                        else
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                    'ADMIN', 'PRODUCT-ACTIVATE', $idList[$i] . ' ' . $productDataArray['code'], 1);
	                        }
	                        $resultArray[] = $productDataArray;
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

    static function displayEdit($pID)
	{
		global $ac_config;
	    $resultArray = Array();

	    $collectionType = TPX_PRODUCT_TYPE_PHOTO_BOOK;

		$dbObj = DatabaseObj::getGlobalDBConnection();

	    if ($dbObj)
	    {
			$stmt = $dbObj->prepare('SELECT `pcl`.`collectiontype` FROM `PRODUCTCOLLECTIONLINK` `pcl`
									  LEFT JOIN `PRODUCTS` `pr` ON `pcl`.`productcode` = `pr`.`code` WHERE `pr`.`id` = ?');
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
			                    if ($stmt->bind_result($collectionType))
			                    {
		                            if (! $stmt->fetch())
		                            {
		                            	$error = 'product displayEdit fetch ' . $dbObj->error;
		                            }
			                    }
			                    else
			                    {
			                    	$error = 'product displayEdit bind result ' . $dbObj->error;
			                    }
			                }
		                }
		                else
		                {
		                	$error = 'product displayEdit store result ' . $dbObj->error;
		                }
	                }
	                else
	                {
						$error = 'product displayEdit execute' . $dbObj->error;
	                }
                }
                else
                {
                	$error = 'product displayEdit bind param ' . $dbObj->error;
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

		$resultArray['producttype'] = $collectionType;
	    $resultArray['product'] = DatabaseObj::getProductFromID($pID);

		$fontListDetails = AdminTaopixOnlineFontLists_model::getFontListData($ac_config, 'productcode', $resultArray['product']['code']);
		$resultArray['fontlists'] = $fontListDetails['fontlists'];
		$resultArray['fontlistselected'] = $fontListDetails['selected'];

	    return $resultArray;
    }

    static function productConfigDisplay()
    {
        global $gSession;
    	global $gConstants;

        $resultArray = Array();
        $productID = $_GET['id'];
		$canLink = 1;

        $productArray = DatabaseObj::getProductFromID($productID);
        $companyCode = $productArray['companycode'];

        if ($gConstants['optionms'])
        {
			if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
			{
				$companyCode = $gSession['userdata']['companycode'];
			}
		}

		$linkingValidityArray = self::checkProductCanLink($productArray['code']);

		// if the product has been linked to we cannot allow the user to set a link in the product
		// likewise if there is an error retrieving the data we cannot safely allow a link
		if ((! $linkingValidityArray['valid']) || ($linkingValidityArray['error']))
		{
			$canLink = 0;
			// create a dummy record
			$linkedProductRecord = array('error' => false, 'errorparam' => '', 'data' => array('code' => '', 'id' => 0));
		}
		elseif ($companyCode !== '')
		{
			// product linking must be done on a global level and is disabled on site specific products
			$canLink = 0;
			$linkedProductRecord = self::getProductLinkPriceLinkRecord($productArray['code']);
		}
		else
		{
			$linkedProductRecord = self::getProductLinkPriceLinkRecord($productArray['code']);
		}


		if ($linkedProductRecord['data']['id'] == 0)
		{
			$productTree = self::getProductTree($productArray['code'], $companyCode);
		}
		else
		{
			// this is a linked product so we want to display the tree for the product that has been linked to
			$productTree = self::getProductTree($linkedProductRecord['data']['code'], $companyCode);
		}

        $resultArray['product'] = $productArray;
        $resultArray['tree'] = $productTree;
		$resultArray['canlink'] = $canLink;
		$resultArray['linkedproduct']['code'] = $linkedProductRecord['data']['code'];
		$resultArray['linkedproduct']['id'] = $linkedProductRecord['data']['id'];

        return $resultArray;
    }

    static function refreshProductTree($pUseLinkedTree)
    {
    	global $gSession;
    	global $gConstants;

    	$resultArray = Array();
		$productID = filter_input(INPUT_GET, 'productid', FILTER_SANITIZE_NUMBER_INT);
		$companyCode = filter_input(INPUT_GET, 'companycode', FILTER_DEFAULT);

		$companyCode = self::convertProductConfigCompanyCodeForDatabase($companyCode);

      	if ($gConstants['optionms'])
        {
			if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
			{
				$companyCode = $gSession['userdata']['companycode'];
			}
		}

		$productArray = DatabaseObj::getProductFromID($productID);

		if ($pUseLinkedTree == 1)
		{
			$linkedProductArray = self::getProductLinkPriceLinkRecord($productArray['code']);

			if ($linkedProductArray['data']['code'] != '')
			{
				$productTree = self::getProductTree($linkedProductArray['data']['code'], $companyCode);
			}
			else
			{
				$productTree = self::getProductTree($productArray['code'], $companyCode);
			}
		}
		else
		{
			$productTree = self::getProductTree($productArray['code'], $companyCode);
		}

        $resultArray['product'] = $productArray;
        $resultArray['tree'] = $productTree;

        return $resultArray;
    }

	static function getProductTree($pProductCode, $pCompanyCode)
	{
        // return an array containing the components currently attached to the product
        $result = '';
        $resultParam = '';
        $resultArray = Array();
        $id = 0;
		$parentid = 0;
		$companycode = 0;
		$productcode = '';
		$groupcode = '';
		$parentPath = '';
		$sectionCode = '';
		$sortorder = '';
		$isDefault = 0;
		$priceid = 0;
		$inheritParentQty = 0;
		$componentCompanyCode = '';
		$componentCode = '';
		$localcode = '';
		$name = '';
		$categoryCode = '';
		$pricingmodel = 0;
		$price = '';
		$islist = 0;
		$decimalplaces = 0;
		$categoryActive = 0;
		$componentActive = 0;

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			// determine the product and its components
			if ($stmt = $dbObj->prepare('SELECT `pl`.`id`, `pl`.`parentid`, `pl`.`companycode`, `pl`.`productcode`, `pl`.`groupcode`, `pl`.`parentpath`,
											`pl`.`sectioncode`, `pl`.`sortorder`, `pl`.`isdefault`, `pl`.`priceid`, `pl`.`inheritparentqty`, `cmp`.`companycode`, `cmp`.`code`, `cmp`.`localcode`,
											`cmp`.`name`, `cmp`.`categorycode`, `cc`.`pricingmodel`, `pr`.`price`, `cc`.`islist`, `cc`.`componentpricingdecimalplaces`, `cc`.`active`, `cmp`.`active`
										FROM PRICELINK pl
										LEFT JOIN COMPONENTS cmp ON `cmp`.`code` = `pl`.`componentcode`
										LEFT JOIN PRICES pr ON `pr`.`id` = `pl`.`priceid`
										LEFT JOIN COMPONENTCATEGORIES cc ON `cc`.`code` = `cmp`.`categorycode`
										WHERE (`pl`.`productcode` = ?) AND (`pl`.`componentcode` <> "")
										AND ((`pl`.`companycode` = ?) OR (`pl`.`companycode` = ""))
										ORDER BY `sortorder`'))
			{
				if ($stmt->bind_param('ss', $pProductCode, $pCompanyCode))
				{
					if ($stmt->bind_result($id, $parentid, $companycode, $productcode, $groupcode, $parentPath, $sectionCode, $sortorder, $isDefault, $priceid, $inheritParentQty, $componentCompanyCode, $componentCode, $localcode, $name, $categoryCode, $pricingmodel,  $price, $islist, $decimalplaces, $categoryActive, $componentActive))
					{
						if ($stmt->execute())
						{
							while ($stmt->fetch())
							{
								$itemArray['id'] = $id;
								$itemArray['parentid'] = $parentid;
								$itemArray['priceid'] = $priceid;
								$itemArray['companycode'] = $companycode;
								$itemArray['productcode'] = $productcode;
								$itemArray['groupcode'] = $groupcode;
								$itemArray['parentpath'] = $parentPath;
								$itemArray['sectioncode'] = $sectionCode;
								$itemArray['sortorder'] = $sortorder;
								$itemArray['isdefault'] = $isDefault;
								$itemArray['companycode'] = $componentCompanyCode;
								$itemArray['code'] = $componentCode;
								$itemArray['localcode'] = $localcode;
								$itemArray['name'] = $name;
								$itemArray['pricingmodel'] = $pricingmodel;
								$itemArray['price'] = $price;
								$itemArray['categorycode'] = $categoryCode;
								$itemArray['islist'] = $islist;
								$itemArray['decimalplaces'] = $decimalplaces;
								$itemArray['categoryactive'] = $categoryActive;
								$itemArray['componentactive'] = $componentActive;
								$itemArray['inheritparentqty'] = $inheritParentQty;
								$itemArray['pathdepth'] = substr_count($parentPath, '\\');

								$resultArray[] = $itemArray;
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
				$stmt = null;
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = __FUNCTION__.'1 prepare ' . $dbObj->error;
			}
			$stmt = null;

			$dbObj->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'getComponentPriceRowByCode connect ' . $dbObj->error;
		}

        return $resultArray;
	}

    static function productEdit()
    {
        global $gSession;
		global $ac_config;
		global $gConstants;

        $result = '';
        $resultParam = '';

        $id = $_GET['id'];
		$taxLevel = $_POST['taxlevel'];
        $resultArray = DatabaseObj::getProductFromID($id);
        $productCode = $resultArray['code'];
        $resultArray['skucode'] = $_POST['skucode'];
        $resultArray['unitcost'] = $_POST['cost'];
        $resultArray['weight'] = $_POST['weight'];
        $resultArray['jobticketfield1name'] = $_POST['jobticket1name'];
        $resultArray['jobticketfield1value'] = $_POST['jobticket1value'];
        $resultArray['jobticketfield2name'] = $_POST['jobticket2name'];
        $resultArray['jobticketfield2value'] = $_POST['jobticket2value'];
        $resultArray['jobticketfield3name'] = $_POST['jobticket3name'];
        $resultArray['jobticketfield3value'] = $_POST['jobticket3value'];
        $resultArray['jobticketfield4name'] = $_POST['jobticket4name'];
        $resultArray['jobticketfield4value'] = $_POST['jobticket4value'];
        $resultArray['jobticketfield5name'] = $_POST['jobticket5name'];
        $resultArray['jobticketfield5value'] = $_POST['jobticket5value'];
        $resultArray['cancreatenewprojects'] = $_POST['cancreatenewprojects'];
		$resultArray['isactive'] = $_POST['isactive'];
		$resultArray['previewtype'] = $_POST['previewtype'];
		$resultArray['previewcovertype'] = $_POST['previewcovertype'];
		$resultArray['previewautoflip'] = $_POST['previewautoflip'];
		$resultArray['previewthumbnails'] = $_POST['previewthumbnails'];
		$resultArray['previewthumbnailsview'] = $_POST['previewthumbnailsview'];
		$resultArray['productoptions'] = UtilsObj::getPOSTParam('productoptions', TPX_PRODUCTOPTION_PRICING_NON);
		$resultArray['pricetransformationstage'] = UtilsObj::getPOSTParam('pricetransformationstage', TPX_PRICETRANSFORMATIONSTAGE_POST);
		$resultArray['minimumprintsperproject'] = UtilsObj::getPOSTParam('minimumprintsperproject', 1);
		$resultArray['usedefaultimagescalingbefore'] = UtilsObj::getPOSTParam('usedefaultimagescalingbefore', 0);
		$resultArray['imagescalingbeforeenabled'] = UtilsObj::getPOSTParam('imagescalingbeforeenabled', 0);
        $resultArray['imagescalingbefore'] = UtilsObj::getPOSTParam('imagescalingbefore', 0.00);
		$resultArray['retroprints'] = UtilsObj::getPOSTParam('retroprints', 0);

        if ($id > 0)
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();

            if ($dbObj)
            {
                $uniqueID = md5($productCode);
				$destinationFolder = UtilsObj::correctPath($ac_config['CONTROLCENTREPREVIEWSPATH'], DIRECTORY_SEPARATOR, true) . 'products' . DIRECTORY_SEPARATOR . $uniqueID . DIRECTORY_SEPARATOR;

				// Remove old preview.
				if (($_POST['previewupdate'] == '1') || ($_POST['previewremove'] == '1'))
				{
					UtilsObj::deleteFolder($destinationFolder);
				}

	        	if ($_POST['previewupdate'] == '1')
	        	{
					$extension = UtilsObj::getExtensionFromImageType($gSession['previewtype']);
					$resultParam = UtilsObj::moveUploadedFile($gSession['previewpath'], $destinationFolder . $uniqueID . $extension);

					if ($resultParam !== '')
					{
						$result = 'str_UploadError';
					}
	        	}

                if ($result === '')
                {
                    if ($stmt = $dbObj->prepare('UPDATE `PRODUCTS` SET `skucode` = ?, `taxlevel` = ?, `unitcost` = ?, `weight` = ?,
                        `jobticketfield1name` = ?, `jobticketfield1value` = ?, `jobticketfield2name` = ?, `jobticketfield2value` = ?,
                        `jobticketfield3name` = ?, `jobticketfield3value` = ?, `jobticketfield4name` = ?, `jobticketfield4value` = ?,
                        `jobticketfield5name` = ?, `jobticketfield5value` = ?, `createnewprojects` = ?, `previewtype` = ?, `previewcovertype` = ?,
                        `previewautoflip` = ?, `previewthumbnailsview` = ?, `previewthumbnails` = ?, `productoptions` = ?, `pricetransformationstage` = ?,
                        `minimumprintsperproject` = ?, `usedefaultimagescalingbefore` = ?, `imagescalingbeforeenabled` = ?, `imagescalingbefore` = ?,
                        `retroprints` = ?,
                        `active` = ? WHERE `id` = ?'))
                    {
                        if ($stmt->bind_param('ssddssssssssssiiiiiiiiiiidiii',$resultArray['skucode'], $taxLevel, $resultArray['unitcost'], $resultArray['weight'],
                            $resultArray['jobticketfield1name'], $resultArray['jobticketfield1value'], $resultArray['jobticketfield2name'], $resultArray['jobticketfield2value'],
                            $resultArray['jobticketfield3name'], $resultArray['jobticketfield3value'], $resultArray['jobticketfield4name'], $resultArray['jobticketfield4value'],
                            $resultArray['jobticketfield5name'], $resultArray['jobticketfield5value'], $resultArray['cancreatenewprojects'],
                            $resultArray['previewtype'], $resultArray['previewcovertype'], $resultArray['previewautoflip'], $resultArray['previewthumbnailsview'],
                            $resultArray['previewthumbnails'], $resultArray['productoptions'], $resultArray['pricetransformationstage'],
                            $resultArray['minimumprintsperproject'], $resultArray['usedefaultimagescalingbefore'], $resultArray['imagescalingbeforeenabled'],
                            $resultArray['imagescalingbefore'], $resultArray['retroprints'],
                            $resultArray['isactive'], $id))
                        {
                            if ($stmt->execute())
                            {
                                DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                    'ADMIN', 'PRODUCT-UPDATE', $id . ' ' . $resultArray['code'], 1);

								if ($gConstants['optiondesol']) {
									$fontListDetails = [
										'type' => UtilsObj::getPOSTParam('fontlisttype', -1),
										'fontlist' => UtilsObj::getPOSTParam('fontlist', null),
										'codes' => [$productCode],
										'checkfield' => 'productcode',
									];
									AdminTaopixOnlineFontLists_model::updateAssignments($fontListDetails, $ac_config);
								}
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'productEdit execute ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            // could not bind parameters
                            $result = 'str_DatabaseError';
                            $resultParam = 'productEdit bind ' . $dbObj->error;
                        }
                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                    else
                    {
                        // could not prepare statement
                        $result = 'str_DatabaseError';
                        $resultParam = 'productEdit prepare ' . $dbObj->error;
                    }
                }

                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'productEdit connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['isactive'] = $resultArray['isactive'];
        $resultArray['id'] = $id;

        return $resultArray;
    }

    static function productDelete()
    {
        global $ac_config;
        global $gSession;

        $resultArray = Array();
        $result = '';
        $productsDeleted = Array();
        $allDeleted = 1;
		$recordID = 0;

        $productIDList = explode(',',$_POST['idlist']);
        $productIDCount = count($productIDList);

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
           for ($i = 0; $i < $productIDCount; $i++ )
           {
                $productDataArray = DatabaseObj::getProductFromID($productIDList[$i]);

	            if ($productDataArray['result'] == '')
	        	{
	                // first make sure the product hasn't been used
	                $canDelete = true;

	                if ($canDelete == true)
	                {
	                    if ($stmt = $dbObj->prepare('SELECT `id` FROM `SHIPPINGRATES` WHERE `productcode` = ?'))
	                    {
	                        if ($stmt->bind_param('s', $productDataArray['code']))
	                        {
	                            if ($stmt->bind_result($recordID))
	                            {
	                               if ($stmt->execute())
	                               {
	                                    if ($stmt->fetch())
	                                    {
	                                        $result = 'str_ErrorUsedInShippingRate';
	                                        $canDelete = false;
	                                        $allDeleted = 0;
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
	                        if ($stmt = $dbObj->prepare('SELECT `id` FROM `VOUCHERS` WHERE `productcode` = ?'))
	                    	{
	                        	if ($stmt->bind_param('s', $productDataArray['code']))
	                        	{
	                            	if ($stmt->bind_result($recordID))
	                            	{
	                               		if ($stmt->execute())
	                               		{
	                                    	if ($stmt->fetch())
	                                    	{
	                                        	$result = 'str_ErrorUsedInVoucher';
	                                            $canDelete = false;
	                                            $allDeleted = 0;
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
	                    // first attempt to delete the product prices
	                    if ($stmt = $dbObj->prepare('DELETE FROM `PRICELINK` WHERE `productcode` = ?'))
	                    {
	                        if ($stmt->bind_param('s', $productDataArray['code']))
	                        {
	                            if (! $stmt->execute())
	                            {
	                                $productIDList[$i] = 0;
	                            }
	                        }
	                        $stmt->free_result();
	            			$stmt->close();
	           				$stmt = null;
	                    }

						// next delete any tree links pointing to the product
	                    if ($stmt = $dbObj->prepare('DELETE FROM `PRICELINK` WHERE `linkedproductcode` = ?'))
	                    {
	                        if ($stmt->bind_param('s', $productDataArray['code']))
	                        {
	                            if (! $stmt->execute())
	                            {
	                                $productIDList[$i] = 0;
	                            }
	                        }
	                        $stmt->free_result();
	            			$stmt->close();
	           				$stmt = null;
	                    }

	                    // next delete the product itself
	                    if ($productIDList[$i] > 0)
	                    {
	                        if ($stmt = $dbObj->prepare('UPDATE `PRODUCTS` SET `deleted` = 1 WHERE `id` = ?'))
	                            {

	                            if ($stmt->bind_param('i', $productIDList[$i]))
	                            {
	                                if ($stmt->execute())
	                                {
	                                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                        'ADMIN', 'PRODUCT-DELETE', $productIDList[$i] . ' ' . $productDataArray['code'], 1);
	                                        $productsDeleted[] = $productIDList[$i];
	                                    }
	                                    else
	                                    {
	                                        $productIDList[$i] = 0;
	                                    }
	                                }
	                                $stmt->free_result();
						            $stmt->close();
						            $stmt = null;
	                            }
	                            else
	                            {
	                                $productIDList[$i] = 0;
	                            }
	                    }
	                }
	        	}
           }
        }
        $dbObj->close();

        $resultArray['result'] = $result;
        $resultArray['alldeleted'] = $allDeleted;
        $resultArray['productids'] = $productsDeleted;

        return $resultArray;
    }

    static function bindParams($pStatement, $pParams, $pBind, $pOrderBy)
    {
		$productItemArray = Array();
		$resultArray = Array();
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
			if ($stmt = $dbObj->prepare($pStatement))
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

		// Concatenate the order by stament to original query so that a limit is set.
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
                    if ($stmt->bind_result($id, $companyCode, $code, $categoryName, $name, $isActive, $productcollection, $resourceCode))
	                {
	                    if ($stmt->execute())
	                    {

	                        while ($stmt->fetch())
	                        {
	                         	$hasPriceArray = self::doesProductHavePrice($code);
								$collectionTypeArray = self::getCollectionType($code);

	                            $productItem['id'] = $id;
	                            $productItem['companycode'] = $companyCode;
	                            $productItem['code'] = $code;
	                            $productItem['categoryname'] = $categoryName;
	                            $productItem['name'] = $name;
                                $productItem['productcollection'] = $productcollection;
	                            $productItem['active'] = $isActive;
								$productItem['hasprice'] = $hasPriceArray['haspricedefined'];
								$productItem['collectiontype'] = $collectionTypeArray['collectiontype'];
								$productItem['3dmodelcode'] = $resourceCode;
	                            $productItemArray[] = $productItem;
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
		$resultArray['products'] = $productItemArray;
		return $resultArray;
    }

	static function getComponentsFromCategory()
    {
        global $gSession;
		global $gConstants;

        $resultArray = Array();
        $postCategoryCode = $_POST['selection'];
        $companyCode = '';
		$active = 0;
		$id = 0;
		$code = '';
		$localCode = '';
		$name = '';
		$categoryCode = '';
		$pricingModel = 0;
		$islist = 0;
		$decimalPlaces = 0;
		$componentActive = 0;
		$categoryActive = 0;
		$companycode = '';

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

		if ($postCategoryCode != 'SECTIONS')
		{
	        $dbObj = DatabaseObj::getGlobalDBConnection();

	        if ($dbObj)
	        {
				$sql = 'SELECT cmp.id, cmp.companycode, cmp.code, cmp.localcode, cmp.name, cmp.categorycode, cc.pricingmodel, cc.islist, cc.componentpricingdecimalplaces, cmp.active, cc.active FROM COMPONENTS cmp, COMPONENTCATEGORIES cc WHERE (cmp.companycode = ? OR cmp.companycode = "") AND `categorycode` = ? AND (cmp.categorycode = cc.code) ORDER BY `code`';

	        	if ($stmt = $dbObj->prepare($sql))
	            {
	            	$stmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);

	            	if ($stmt->bind_param('ss', $companyCode, $postCategoryCode))
	                {
		            	if ($stmt->bind_result($id, $companycode, $code, $localCode, $name, $categoryCode, $pricingModel, $islist, $decimalPlaces, $componentActive, $categoryActive))
						{
							if ($stmt->execute())
							{
								while ($stmt->fetch())
								{
									$item['id'] = $id;
									$item['companycode'] = $companycode;
									$item['code'] = $code;
									$item['localcode'] = $localCode;
									$item['name'] = $name;
									$item['categorycode'] = $categoryCode;
									$item['pricingmodel'] = $pricingModel;
									$item['islist'] = $islist;
									$item['decimalplaces'] = $decimalPlaces;
									$item['componentactive'] = $componentActive;
									$item['categoryactive'] = $categoryActive;
									$resultArray[] = $item;
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
		}
		else
		{
			$dbObj = DatabaseObj::getGlobalDBConnection();

			if ($dbObj)
	        {
				$baseSql = 'SELECT `id`, `code`, `name`, `categorycode`, `active` FROM `SECTIONS`';
				$whereParams = array();
				$whereParamString = '';
				$whereArray = array(
					'`displaytype` > 0',
					'`deleted` = 0'
				);

				if ($companyCode === '')
				{
					$whereArray[] = '`companycode` = ""';
					$bindOK = true;
				}
				else
				{
					$whereArray[] = '(`companycode` = "" OR `companycode` = ?)';
					$whereParamString .= 's';
					$whereParams[] = $companyCode;
				}

				$baseSql .= ' WHERE ' . join(' AND ', $whereArray) . ' ORDER BY `code`';
				$stmt = $dbObj->prepare($baseSql);

	        	if ($stmt)
	            {
					if ($whereParams !== array())
					{
						$bindOK = DatabaseObj::bindParams($stmt, $whereParamString, $whereParams);
					}
	            	if ($bindOK)
	                {
		            	if ($stmt->bind_result($id, $code, $name, $categoryCode, $active))
						{
							if ($stmt->execute())
							{
								while ($stmt->fetch())
								{
									$item['id'] = $id;
									$item['code'] = $code;
									$item['name'] = $name;
									$item['categorycode'] = $categoryCode;
									$item['categoryactive'] = $active;

									$resultArray[] = $item;
								}
							}
						}
	                }

					$stmt->free_result();
	                $stmt->close();

				}
	        	$dbObj->close();
	        }
		}

        $resultArray['selection'] = $postCategoryCode;

        return $resultArray;
	}

	static function getProductsConfigPricingGridData()
	{
        $pricelinkParentIds = $_POST['pricelinkparentids'];
        $parentIDArray = explode(',', $pricelinkParentIds);
        $itemCount = count($parentIDArray);
        $componentCode = $_POST['componentcode'];
		$companyCode = $_POST['companycode'];

        $resultArray = Array();
		$resultArray['pricing'] = Array();
		$result = '';
        $resultParam = '';
		$recordid = 0;
		$parentid = 0;
		$priceLinkCompanyCode = 0;
		$productCode = 0;
		$componentcode = 0;
		$groupCode = 0;
		$sortOrder = 0;
		$priceid = 0;
		$priceInfo = '';
		$priceDescription = '';
		$inheritParentQty = 0;
		$active = 0;
		$pricelistid = 0;
		$categorycode = '';
		$pricingModel = 0;
		$price = '';
		$quantityIsDropDown = 0;
		$ispricelist = 0;
		$taxCode = '';
		$priceactive = 0;
		$taxRateCode = 0;
		$taxRate = 0;

		$itemArray = Array();

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$stmt = null;
			$bindOK = false;

			if ($pricelinkParentIds != '')
			{
				$bindParamString = str_repeat('?, ', $itemCount);
				$bindParamString = substr($bindParamString, 0, -2);
				$bindTypeString = str_repeat('i', $itemCount);

				// add the additional bind parameters into the array
				$parentIDArray[] = $companyCode;
				$parentIDArray[] = $componentCode;
				$bindTypeString .= 'ss';

				$stmt = $dbObj->prepare('(SELECT `pl`.`id`, `pl`.`parentid`, `pl`.`companycode`, `pl`.`productcode`, `pl`.`componentcode`, `pl`.`groupcode`, `pl`.`sortorder`,
											`pl`.`priceid`, `pl`.`priceinfo`, `pl`.`pricedescription`, `pl`.`inheritparentqty`, `pl`.`active`, `pr`.`id`, `pr`.`categorycode`,
											`pr`.`pricingmodel`, `pr`.`price`, `pr`.`quantityisdropdown`, `pr`.`ispricelist`, `pr`.`taxcode`, `pr`.`active`, `tr`.`code`, `tr`.`rate`
											FROM `PRICELINK` pl JOIN `PRICES` pr ON `pr`.`id` = `pl`.`priceid` LEFT JOIN `TAXRATES` tr ON `pr`.`taxcode` = `tr`.`code`
											WHERE (`pl`.`parentid` IN ('. $bindParamString .'))
											AND (`pl`.`priceid` > 0)
										)
										UNION
										(SELECT `pl`.`id`, `pl`.`parentid`, `pl`.`companycode`, `pl`.`productcode`, `pl`.`componentcode`, `pl`.`groupcode`, `pl`.`sortorder`,
											`pl`.`priceid`, `pl`.`priceinfo`, `pl`.`pricedescription`, `pl`.`inheritparentqty`, `pl`.`active`, `pr`.`id`, `pr`.`categorycode`,
											`pr`.`pricingmodel`, `pr`.`price`, `pr`.`quantityisdropdown`, `pr`.`ispricelist`, `pr`.`taxcode`, `pr`.`active`, `tr`.`code`, `tr`.`rate`
											FROM `PRICELINK` pl JOIN `PRICES` pr ON `pr`.`id` = `pl`.`priceid` LEFT JOIN `TAXRATES` tr ON `pr`.`taxcode` = `tr`.`code`
											WHERE ((`pl`.`companycode` = "") OR (`pl`.`companycode` = ?))
											AND (`pl`.`productcode` = "")
											AND (`pl`.`componentcode` = ?)
											AND (`pl`.`priceid` > 0)
										)');
				if ($stmt)
				{
					$bindOK = DatabaseObj::bindParams($stmt, $bindTypeString, $parentIDArray);
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = __FUNCTION__ . ' prepare1 ' . $dbObj->error;
				}
			}
			else
			{
				$stmt = $dbObj->prepare('SELECT `pl`.`id`, `pl`.`parentid`, `pl`.`companycode`, `pl`.`productcode`, `pl`.`componentcode`, `pl`.`groupcode`, `pl`.`sortorder`,
											`pl`.`priceid`, `pl`.`priceinfo`, `pl`.`pricedescription`, `pl`.`inheritparentqty`, `pl`.`active`, `pr`.`id`, `pr`.`categorycode`,
											`pr`.`pricingmodel`, `pr`.`price`, `pr`.`quantityisdropdown`, `pr`.`ispricelist`, `pr`.`taxcode`, `pr`.`active`, `tr`.`code`, `tr`.`rate`
											FROM `PRICELINK` pl JOIN `PRICES` pr ON `pr`.`id` = `pl`.`priceid` LEFT JOIN `TAXRATES` tr ON `pr`.`taxcode` = `tr`.`code`
											WHERE ((`pl`.`companycode` = "") OR (`pl`.`companycode` = ?))
											AND (`pl`.`productcode` = "")
											AND (`pl`.`componentcode` = ?)
											AND (`pl`.`priceid` > 0)');
				if ($stmt)
				{
					$bindOK = $stmt->bind_param('ss', $companyCode, $componentCode);
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = __FUNCTION__ . ' prepare2 ' . $dbObj->error;
				}

			}

			if ($bindOK)
			{
				if ($stmt->bind_result($recordid, $parentid, $priceLinkCompanyCode, $productCode, $componentcode, $groupCode, $sortOrder,
										$priceid, $priceInfo, $priceDescription, $inheritParentQty, $active, $pricelistid, $categorycode,
										$pricingModel, $price, $quantityIsDropDown, $ispricelist, $taxCode, $priceactive, $taxRateCode, $taxRate))
				{
					if ($stmt->execute())
					{
						while ($stmt->fetch())
						{
					   		$itemArray['id'] = $recordid;
						    $itemArray['parentid'] = $parentid;
						    $itemArray['companycode'] = $priceLinkCompanyCode;
						    $itemArray['productcode'] = $productCode;
							$itemArray['groupcode'] = $groupCode;
							$itemArray['sortorder'] = $sortOrder;
							$itemArray['priceinfo'] = $priceInfo;
							$itemArray['pricedescription'] = $priceDescription;
							$itemArray['inheritparentqty'] = $inheritParentQty;
							$itemArray['pricelistid'] = $pricelistid;
							$itemArray['pricingmodel'] = $pricingModel;
							$itemArray['categorycode'] = $categorycode;
							$itemArray['price'] = $price;
							$itemArray['quantityisdropdown'] = $quantityIsDropDown;
							$itemArray['active'] = $active;
							$itemArray['ispricelist'] = $ispricelist;
							$itemArray['taxcode'] = $taxCode;
							$itemArray['priceactive'] = $priceactive;
							$itemArray['taxratecode'] = $taxRateCode;
							$itemArray['taxrate'] = $taxRate;

							$resultArray['pricing'][] = $itemArray;
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

			if ($stmt)
			{
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
			$resultParam = __FUNCTION__ . ' connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
	}

	static function getPreviewImage()
    {
		$resultArray = DatabaseObj::getPreviewImage();

        return $resultArray;
    }

    static function uploadPreviewImage($pSection)
    {
    	$resultArray = DatabaseObj::uploadPreviewImage($pSection);

	    return $resultArray;
    }

    static function getItemList($pParentID)
    {
        $result = '';
        $resultParam = '';
        $resultArray = Array();
        $itemList = Array();
        $isActive = 0;
		$id = 0;
		$companyCode = '';
		$groupCode = '';

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
                                    $itemList[] = $item;
                                }
                            }
                            else
                            {
                                // could not execute statement
                                $result = 'str_DatabaseError';
                                $resultParam = 'coverpricinggetitemlist execute ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            // could not bind result
                            $result = 'str_DatabaseError';
                            $resultParam = 'coverpricinggetitemlist bind result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'coverpricinggetitemlist bind params ' . $dbObj->error;
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
                $resultParam = 'coverpricinggetitemlist connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['items'] = $itemList;
        $resultArray['pricelinkactive'] = $isActive;

        return $resultArray;
    }

    static function saveProductConfig($pLinkedProductCode)
    {
    	global $gSession;
    	global $gConstants;

		$resultArray = Array();
    	$treeConfigArray = Array();
    	$parentID = 0;
    	$linkedPriceListID = 0;
    	$priceListName = '';
    	$isPriceList = 0;
    	$sectionCode = '';
        $sortOrder = '';
        $shoppingCartType = TPX_SHOPPINGCARTTYPE_INTERNAL;
        $priceDescription = '';
        $isDefault = 0;
        $isVisible = 1;
		$priceAdditionalInfo = '';
		$priceListLocalCode = 'CUSTOM';
		$productCode = $_POST['productcode'];
		$result = '';
		$resultParam = '';
		$quantityDisplayType = 0;
		$sectionPath = '';
		$pricesID = 0;
		$companyCode = '';
    	$treeConfigArray = json_decode($_POST['serializedtreedata'], true);
		$priceLinksToDelete = $_POST['pricelinkidstodelete'];

		if ($priceLinksToDelete != '')
		{
			self::deleteNodesFromProductConfig($priceLinksToDelete);
		}

    	switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				if ($gConstants['optionms'])
        		{
        			$companyCode = self::convertProductConfigCompanyCodeForDatabase(UtilsObj::getPOSTParam('company', ''));
        		}
			break;
			case TPX_LOGIN_COMPANY_ADMIN:
				$companyCode = $gSession['userdata']['companycode'];
			break;
		}

    	$itemCount = count($treeConfigArray);

		// check if we already have a price link record for this link
		$linkCheckResultArray = self::getProductLinkPriceLinkRecord($productCode);

		$result = $linkCheckResultArray['error'];
		$resultParam = $linkCheckResultArray['errorparam'];

		// if we have a linked product code then we want to leave everything else is and insert the record
		if ($pLinkedProductCode !== '')
		{
			// check that we haven't been sent the same product code to link as the current product
			if ($productCode == $pLinkedProductCode)
			{
				$result = "str_ErrorTitleInvalidPricing";
				$resultParam = "saveProductConfig validate link";
			}

			if ($result === '')
			{
				// sanity check that we aren't attempting to link an already linked product
				$linkValidationResultArray = self::getProductLinkPriceLinkRecord($pLinkedProductCode);

				if (($linkValidationResultArray['error'] != '') || ($linkValidationResultArray['data']['code'] != ''))
				{
					$result = "str_ErrorTitleInvalidPricing";
					$resultParam = "saveProductConfig validate link";
				}
			}

			// validate that we are not attempting to assign a product link to a company
			if ($result === '')
			{
				if ($companyCode !== '')
				{
					$result = "str_ErrorTitleInvalidPricing";
					$resultParam = "saveProductConfig company link";
				}
			}

			// do nothing we get an error or find a pre-existing record for this code
			if (($result === '') && ($linkCheckResultArray['data']['code'] !== $pLinkedProductCode))
			{
				// delete the record if it is for a different product code
				if ($linkCheckResultArray['data']['id'] != 0)
				{
					$priceLinkDeleteResultArray = self::deletePriceLinkRecordByID($linkCheckResultArray['data']['linkid']);
					$result = $priceLinkDeleteResultArray['error'];
					$resultParam = $priceLinkDeleteResultArray['errorparam'];
				}

				// don't insert the record if they have managed to send the same link product code as the product code
				if ($result === '')
				{
					// insert the new record
					$productLinkInsertResultArray = self::insertProductLinkingRecord($productCode, $pLinkedProductCode);
					$result = $productLinkInsertResultArray['error'];
					$resultParam = $productLinkInsertResultArray['errorparam'];
				}
			}
		}
    	elseif (($result == '') && ($itemCount > 0))
    	{
			// delete the old removed linking record if there was one
			if ($linkCheckResultArray['data']['code'] !== '')
			{
				$priceLinkDeleteResultArray = self::deletePriceLinkRecordByID($linkCheckResultArray['data']['linkid']);
				$result = $priceLinkDeleteResultArray['error'];
				$resultParam = $priceLinkDeleteResultArray['errorparam'];
			}

			if ($result == '')
			{
				$dbObj = DatabaseObj::getGlobalDBConnection();

				if ($dbObj)
				{
					for ($i = 0; $i < $itemCount; $i++)
					{
						$theItem = $treeConfigArray[$i];
						$sortOrder = $theItem['sortorder'];
						$theAction = $theItem['action'];
						$isDefault = $theItem['isdefault'];

						//if we are not only updating the sortorder get the rest of the properties
						if ($theAction != 2)
						{
							$price = $theItem['price'];
							$quantityDisplayType = $theItem['quantityisdropdown'];
							$isActive = $theItem['active'];
							$taxCode = $theItem['taxcode'];
							$componentCode = $theItem['componentcode'];
							$categoryCode = $theItem['categorycode'];
							$pricingModel = $theItem['pricingmodel'];
							$componentPath = $theItem['path'];
							$sectionCode = $theItem['sectioncode'];
							$priceAdditionalInfo = $theItem['priceadditionalinfo'];
							$recordModified =  $theItem['modified'];
							$inheritParentQty = $theItem['inheritparentqty'];
						}

						// If the action is 0 then we know that this is an insert for new priclink data
						if ($theItem['action'] == 0)
						{
							$groupCodesArray = explode(',', $theItem['groupcodes']);
							$groupCodesCount = count ($groupCodesArray);

							if ($theItem['pricelistid'] == '-1')
							{
								//Check to see if the node has just been dragged onto the tree and had no pricing set at all. Just literally dragged onto the tree
								//If it is a dummy mode use the pricelistid of -1 for insertion in the pricelink table

								if ($theItem['isdummynode'] == 0)
								{
									if ($stmt = $dbObj->prepare('INSERT INTO `PRICES` VALUES (0, now(), ?, ?, ?, ?, ?, CONCAT("CUSTOM", UNIX_TIMESTAMP(NOW()), SUBSTRING(MD5(RAND()) FROM 1 FOR 10)), ?, ?, ?, ?, ?, ?)'))
									{
										if ($stmt->bind_param('ssiisssiisi', $companyCode, $categoryCode, $linkedPriceListID, $pricingModel, $price, $priceListLocalCode, $priceListName, $quantityDisplayType, $isPriceList, $taxCode, $isActive))
										{
											if ($stmt->execute())
											{
												$pricesID = $dbObj->insert_id;
												DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
																			'ADMIN', 'PRODUCTCONFIG-ADD NEW PRICELIST', $pricesID . ' ' . $componentCode, 1);
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
									$pricesID = $theItem['pricelistid'];
								}
							}
							else
							{
								$pricesID = $theItem['pricelistid'];
							}

							for ($j=0; $j < $groupCodesCount; $j++)
							{
								if ($stmt = $dbObj->prepare('INSERT INTO `PRICELINK` (`datecreated`) VALUES (now())'))
								{
									if ($stmt->execute())
									{
										   $recordID = $dbObj->insert_id;

										   if ($j == 0)
										   {
											   $parentID = $recordID;
										   }

										if ($stmt2 = $dbObj->prepare('UPDATE `PRICELINK` SET `parentid` = ?, `companycode` = ?, `productcode` = ?, `groupcode` = ?, `componentcode` = ?, ' .
														  '`parentpath` = ?, `sectioncode` = ?, `sortorder` = ?, `shoppingcarttype` = ?, `priceid` = ?, `priceinfo` = ?, `pricedescription` = ?, `inheritparentqty` = ?, `isdefault` = ?, `isvisible` = ?, `active` = ? WHERE `id` = ?'))
										{
											if ($stmt2->bind_param('isssssssiissiiiii', $parentID, $companyCode, $productCode, $groupCodesArray[$j], $componentCode, $componentPath, $sectionCode, $sortOrder, $shoppingCartType, $pricesID, $priceAdditionalInfo, $priceDescription, $inheritParentQty, $isDefault, $isVisible, $isActive, $recordID))
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
						}
						else if ($theItem['action'] == 1)
						{
							//determine whether or not the pricing was originally a price list or not
							$inIsPriceList = $theItem['inispricelist'];
							$inPriceListID = $theItem['inpricelistid'];

							// This is now an update to an exisitng pricelink entry
							$groupCodesArray = explode(',', $theItem['groupcodes']);
							$groupCodesCount = count ($groupCodesArray);

							$itemArray = self::getItemList($theItem['id']);
							$existingItemList = $itemArray['items'];

							$dbObj->query('START TRANSACTION');

							if ($theItem['pricelistid'] == '-1')
							{
								// check to see if the price is now a custom price and previously was not using a price list
								if ($inIsPriceList == '0')
								{
									if ($stmt = $dbObj->prepare('UPDATE `PRICES` SET `price` = ? , `quantityisdropdown` = ?, `taxcode` = ?, `active` = ? WHERE `id` = ?'))
									{
										if ($stmt->bind_param('sisii', $price, $quantityDisplayType, $taxCode, $isActive, $inPriceListID))
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
									if ($stmt = $dbObj->prepare('INSERT INTO `PRICES` VALUES (0, now(), ?, ?, ?, ?, ? ,CONCAT("CUSTOM", UNIX_TIMESTAMP(NOW()), SUBSTRING(MD5(RAND()) FROM 1 FOR 10)) , ?, ?, ?, ?, ?, ?)'))
									{
										if ($stmt->bind_param('ssiissssisi', $companyCode, $categoryCode, $linkedPriceListID, $pricingModel, $price, $priceListLocalCode, $priceListName, $quantityDisplayType, $isPriceList, $taxCode, $isActive))
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
								// Has the actual price data been modified. If it has then we need to execute the following logic.
								if ($recordModified == '1')
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
													$pricesID = $theItem['pricelistid'];
												}
											}
											$stmt->free_result();
											$stmt->close();
											$stmt = null;
										}
									}
									else
									{
										$pricesID = $theItem['pricelistid'];
									}
								}
								else
								{
									$pricesID = $inPriceListID;
								}
							}

							// update pricelink table adding/removing pricelink records with new price records.
							for ($j = 0; $j < $groupCodesCount; $j++)
							{
								$groupCode = $groupCodesArray[$j];
								$insertNewPriceLink = true;
								$existingItemCount = count($existingItemList);

								for ($k = 0; $k < $existingItemCount; $k++)
								{
									if ($groupCode == $existingItemList[$k]['groupcode'])
									{
										$insertNewPriceLink = false;
										array_splice($existingItemList, $k, 1);
										break;
									}
								}

								if ($insertNewPriceLink)
								{
									$stmt = $dbObj->prepare('INSERT INTO `PRICELINK`
										(`id`, `datecreated`, `parentid`, `companycode`, `productcode`, `linkedproductcode`, `groupcode`, `componentcode`, `parentpath`,
											`sectionpath`, `sectioncode`, `sortorder`, `shoppingcarttype`, `priceid`, `priceinfo`, `pricedescription`, `inheritparentqty`, `isdefault`, `isvisible`, `active`)
										VALUES (0, now(), ?, ?, ?, "", ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
								}
								else
								{
									$stmt = $dbObj->prepare('UPDATE `PRICELINK` SET `groupcode` = ?, `sortorder` = ?, `priceid` = ?, `priceinfo` = ?, `inheritparentqty` = ?, `isdefault` = ?, `active` = ? WHERE `parentid` = ? AND `groupcode` = ?');
								}

								if ($stmt)
								{
									if ($insertNewPriceLink)
									{
										$bindOK = $stmt->bind_param('issssssssiissiiii', $theItem['id'], $companyCode, $productCode, $groupCode, $componentCode, $componentPath, $sectionPath, $sectionCode, $sortOrder, $shoppingCartType,
											$pricesID, $priceAdditionalInfo, $priceDescription, $isDefault, $inheritParentQty, $isVisible,  $isActive);
									}
									else
									{
										$bindOK = $stmt->bind_param('ssisiiiis', $groupCode, $sortOrder, $pricesID, $priceAdditionalInfo, $inheritParentQty, $isDefault, $isActive, $theItem['id'], $groupCode);
									}

									if ($bindOK)
									{
										if ($stmt->execute())
										{
											if ($insertNewPriceLink)
											{
												$id = $dbObj->insert_id;

												DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
													'ADMIN', 'PRODUCTCOMPONENTPRICELINK-ADD', $id . ' ' . 'parentid= '.$theItem['id'] . ' - ' . ($groupCode == '' ? 'DEFAULT' : $groupCode), 1);
											}
											else
											{
												// we have edited a price record via the price grid i.e making the price active/inactive
												// if the price record we are dealing with is not a pricelist we must update the active flag for that price record
												// if we did not do this it means that if a price was initially added as inactive there is no way to mark the price as active from the grid.
												if ($inIsPriceList == 0)
												{
													if ($stmt2 = $dbObj->prepare('UPDATE `PRICES` SET `active` = ? WHERE `id` = ?'))
													{
														if ($stmt2->bind_param('ii', $isActive, $inPriceListID))
														{
															$stmt2->execute();
														}

														$stmt2->free_result();
														$stmt2->close();
														$stmt2 = null;
													}
												}
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
							self::deletePriceLink($theItem['id'], $existingItemList);

							$dbObj->query('COMMIT');
						}
						else
						{
							if ($stmt = $dbObj->prepare('UPDATE `PRICELINK` SET `sortorder` = ?, `isdefault` = ? WHERE `parentid` = ?'))
							{
								if ($stmt->bind_param('sii', $sortOrder, $isDefault, $theItem['id']))
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
				else
				{
					// could not open database connection
					$result = 'str_DatabaseError';
					$resultParam = 'productConfig connect ' . $dbObj->error;
				}
			}
    	}
		elseif ($linkCheckResultArray['data']['code'] !== '')
		{
			// in this scenario someone has unlinked a product to an empty tree and we need to delete the record
			$priceLinkDeleteResultArray = self::deletePriceLinkRecordByID($linkCheckResultArray['data']['linkid']);
			$result = $priceLinkDeleteResultArray['error'];
			$resultParam = $priceLinkDeleteResultArray['errorparam'];
		}

    	$resultArray['result'] = $result;
    	$resultArray['resultparam'] = $resultParam;

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
	            if ($stmt = $dbObj->prepare('DELETE FROM `pricelink` WHERE `parentid` = ? AND `groupcode` = ? AND `productcode` <> ""'))
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

    static function deleteNodesFromProductConfig($pPriceLinkIdsString)
    {
        global $gSession;

		$priceLinkArray = array();
		$priceLinkArray = explode(',', $pPriceLinkIdsString);
		$itemCount = count($priceLinkArray);
		$priceLinkIdsNotUsingPricelist = array();
        $isPriceList = 0;
		$priceID = 0;
		$id = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
	        if ($itemCount > 0)
	        {
	            if ($stmt = $dbObj->prepare('SELECT `priceid` FROM PRICELINK WHERE `parentid` = ? AND `productcode` != ""'))
	            {
	                $stmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);

	                for ($i = 0; $i < $itemCount; $i++)
	                {
	                   $parentID = $priceLinkArray[$i];

	                    if ($stmt->bind_param('i', $parentID))
	                    {
	                        if ($stmt->bind_result($priceID))
                    		{
		                        if ($stmt->execute())
		                        {
		                       		if ($stmt->fetch())
									{
			                       		if ($stmt2 = $dbObj->prepare('SELECT `id`, `ispricelist` FROM PRICES WHERE `id` = ?'))
							            {
						                    if ($stmt2->bind_param('i', $priceID))
						                    {
						                        if ($stmt2->bind_result($id, $isPriceList))
					                    		{
							                        if ($stmt2->execute())
							                        {
							                            if($stmt2->fetch())
							                            {
							                            	if ($isPriceList == 0)
							                            	{
							                            		$priceLinkIdsNotUsingPricelist[] = 	$id;
							                            	}
							                            }
							                        }

					                    		}
						                    }

							                $stmt2->free_result();
								            $stmt2->close();
								            $stmt2 = null;
							            }
									}
		                        }
                    		}
	                    }
	                }
	                $stmt->free_result();
		            $stmt->close();
		            $stmt = null;
	            }

	            if ($stmt = $dbObj->prepare('DELETE FROM `pricelink` WHERE `productcode` <> "" AND `parentid` = ?'))
	            {
	                for ($i = 0; $i < $itemCount; $i++)
	                {
	                   $parentID = $priceLinkArray[$i];

	                    if ($stmt->bind_param('i', $parentID))
	                    {
	                        if ($stmt->execute())
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                    'ADMIN', 'PRICELINK-DELETE', $priceLinkArray[$i] . ' ' . $priceLinkArray[$i], 1);
	                        }
	                    }
	                }
	                $stmt->free_result();
		            $stmt->close();
		            $stmt = null;
	            }

	            $customPriceListsToDeleteCount = count($priceLinkIdsNotUsingPricelist);

	            if ($customPriceListsToDeleteCount > 0)
	            {
	            	if ($stmt = $dbObj->prepare('DELETE FROM `PRICES` WHERE `id` = ?'))
		            {
		                for ($i = 0; $i < $customPriceListsToDeleteCount; $i++)
		                {
		                   $priceID = $priceLinkIdsNotUsingPricelist[$i];

		                    if ($stmt->bind_param('i', $priceID))
		                    {
		                        if ($stmt->execute())
		                        {
		                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
		                                    'ADMIN', 'CUSTOMPRICELINKRECORD-DELETE', $priceLinkArray[$i] . ' ' . $priceLinkArray[$i], 1);
		                        }
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
    }

    static function doesProductHavePrice($pProductCode)
    {
        $result = '';
        $resultParam = '';
  		$hasPriceDefined = 0;
  		$id = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
        	if ($stmt = $dbObj->prepare('SELECT `id` FROM `PRICELINK` WHERE `productcode` = ? AND `componentcode` = ""'))
            {
                if ($stmt->bind_param('s', $pProductCode))
                {
                    if ($stmt->bind_result($id))
                    {
                        if ($stmt->execute())
                        {
                            if ($stmt->fetch())
                            {
                            	$hasPriceDefined = 1;
                            }
                        }
                        else
                        {
                            // could not execute statement
                            $result = 'str_DatabaseError';
                            $resultParam = 'doesproducthaveprice execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind result
                        $result = 'str_DatabaseError';
                        $resultParam = 'doesproducthaveprice bind result ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not bind parameters
                    $result = 'str_DatabaseError';
                    $resultParam = 'doesproducthaveprice bind params ' . $dbObj->error;
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
            $resultParam = 'doesproducthaveprice connect ' . $dbObj->error;
        }

        $resultArray = array();
        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['haspricedefined'] = $hasPriceDefined;

        return $resultArray;
    }

	static function doesProductHaveSystemResource($pProductCode, $pSystemResourceType)
    {
		$returnData = array();
        $result = '';
        $resultParam = '';
  		$resourceCode = '';
		$productCollectionCode = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
        	if ($stmt = $dbObj->prepare('SELECT `resourcecode`, `productcollectioncode` FROM `PRODUCTONLINESYSTEMRESOURCELINK` WHERE `productcollectioncode` = ? AND `type` = ?'))
            {
                if ($stmt->bind_param('si', $pProductCode, $pSystemResourceType))
                {
                    if ($stmt->bind_result($resourceCode, $productCollectionCode))
                    {
                        if ($stmt->execute())
                        {
                            if ($stmt->fetch())
                            {
								$returnData['resourcecode'] = $resourceCode;
								$returnData['productcollectioncode'] = $productCollectionCode;
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
                $stmt = null;
            }

            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'doesproducthaveprice connect ' . $dbObj->error;
        }

        $resultArray = array();
        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['data'] = $returnData;

        return $resultArray;
    }

	static function getCollectionType($pProductCode)
    {
        $result = '';
        $resultParam = '';
		$collectionType = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			$sql = 'SELECT
						`pcl`.`collectiontype`
                    FROM
						`PRODUCTCOLLECTIONLINK` pcl
                    LEFT JOIN
						`APPLICATIONFILES` af
							ON (`af`.`ref` = `pcl`.`collectioncode`)
                    WHERE
						(`pcl`.`productcode` = ?)
					AND
						(`af`.`type` = 0)
					AND
						(`af`.`deleted`= 0)';

        	if ($stmt = $dbObj->prepare($sql))
            {
                if ($stmt->bind_param('s', $pProductCode))
                {
                    if ($stmt->bind_result($collectionType))
                    {
                        if ($stmt->execute())
                        {
                            if (! $stmt->fetch())
							{
								// could not execute statement
								$result = 'str_DatabaseError';
								$resultParam = __FUNCTION__ . ' fetch ' . $dbObj->error;
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
                $stmt = null;
            }
			else
			{
				// could not bind parameters
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

        $resultArray = array();
        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['collectiontype'] = $collectionType;
        return $resultArray;
    }

	static function productLinkingList($pProductCode)
	{
		$resultArray = self::getProductsForProductLinking($pProductCode);

		return $resultArray;
	}

	static function convertProductConfigCompanyCodeForDatabase($pCompanyCode)
	{
		$companyCode  = $pCompanyCode;

		if (($companyCode === '**ALL**') || ($companyCode === 'GLOBAL'))
		{
			// if the company code is the all code then we went to empty it out to match what will be in the database
			$companyCode = '';
		}

		return $companyCode;
	}

	/**
     * gets a list of products and whether they can be linked for display in the product linking drop down
     * @param string $pCompanyCode the company code to display results for
     * @return array Standard taopix error array with products in data key with subkeys id, code and valid
     */
    static function getProductsForProductLinking($pProductCode)
    {
        $resultArray = UtilsObj::getReturnArray();
        $error  = '';
        $errorParam = '';
        $productID = 0;
        $productCode = '';
        $productValidForLinking = 0;
        $productArray = Array();
        $productsArray = Array();
        $bindResult = true;
		$smarty = SmartyObj::newSmarty('AdminProducts');

		// add the non-link entry to the array
		$productsArray[] = array('id' => 0, 'code' => $smarty->get_config_vars("str_LabelNoProductLink"), 'valid' => 1);

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $sql = "SELECT `p`.`id`, `p`.`code`, ISNULL(`pl`.`id`) AS `validforlinking`
                FROM `products` AS `p`
                LEFT JOIN `pricelink` AS `pl` ON (`p`.`code` = `pl`.`productcode` AND `pl`.`linkedproductcode` != '')
				WHERE `p`.`deleted` = 0 AND `p`.`companycode` = '' AND `p`.`code` != ?
				ORDER BY `p`.`code`";

            $stmt = $dbObj->prepare($sql);

            if ($stmt)
            {
				$bindResult = $stmt->bind_param('s', $pProductCode);

                if ($bindResult)
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->bind_result($productID, $productCode, $productValidForLinking))
                            {
                                while ($stmt->fetch())
                                {
                                    $productArray = Array();

                                    $productArray['id'] = $productID;
                                    $productArray['code'] = $productCode;
                                    $productArray['valid'] = $productValidForLinking;

                                    $productsArray[] = $productArray;
                                }
                            }
                            else
                            {
                                $error = 'str_DatabaseError';
                                $errorParam = __FUNCTION__ . ' bindresult ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $error = 'str_DatabaseError';
                            $errorParam = __FUNCTION__ . ' storeresult ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = 'str_DatabaseError';
                        $errorParam = __FUNCTION__ . ' execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = 'str_DatabaseError';
                    $errorParam = __FUNCTION__ . ' bindparam ' . $dbObj->error;
                }
            }
            else
            {
                $error = 'str_DatabaseError';
                $errorParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
            }
        }
        else
        {
            $error = 'str_DatabaseError';
            $errorParam = __FUNCTION__ . ' connect ' . $dbObj->error;
        }

        $resultArray['error'] = $error;
        $resultArray['errorparam'] = $errorParam;
        $resultArray['data'] = $productsArray;

        return $resultArray;
    }

	static function getProductLinkPriceLinkRecord($pProductCode)
    {
        $resultArray = UtilsObj::getReturnArray();
        $error  = '';
        $errorParam = '';
        $recordID = 0;
		$linkRecordID = 0;
        $linkedProductCode = '';
        $bindResult = true;

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $sql = "SELECT `p`.`id`, `pl`.`id`, `pl`.`linkedproductcode`
				FROM `pricelink` AS `pl`
				LEFT JOIN `products` AS `p` ON (`p`.`code` = `pl`.`linkedproductcode`)
				WHERE (`pl`.`productcode` = ?) AND (`pl`.`linkedproductcode` != '')";


            $stmt = $dbObj->prepare($sql);

            if ($stmt)
            {
				$bindResult = $stmt->bind_param('s', $pProductCode);

                if ($bindResult)
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->bind_result($recordID,$linkRecordID, $linkedProductCode))
                            {
                                while ($stmt->fetch())
                                {
                                }
                            }
                            else
                            {
                                $error = 'str_DatabaseError';
                                $errorParam = __FUNCTION__ . ' bindresult ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $error = 'str_DatabaseError';
                            $errorParam = __FUNCTION__ . ' storeresult ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = 'str_DatabaseError';
                        $errorParam = __FUNCTION__ . ' execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = 'str_DatabaseError';
                    $errorParam = __FUNCTION__ . ' bindparam ' . $dbObj->error;
                }
            }
            else
            {
                $error = 'str_DatabaseError';
                $errorParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
            }
        }
        else
        {
            $error = 'str_DatabaseError';
            $errorParam = __FUNCTION__ . ' connect ' . $dbObj->error;
        }

        $resultArray['error'] = $error;
        $resultArray['errorparam'] = $errorParam;
        $resultArray['data']['id'] = $recordID;
		$resultArray['data']['code'] = $linkedProductCode;
		$resultArray['data']['linkid'] = $linkRecordID;

        return $resultArray;
    }

	/**
	 * Checks if a product has any products linked to it thus preventing allowing it to be linked
	 * Note that this deliberately ignores multisite due to inheritance
	 * @param string $pProductCode the product code
	 * @return array standard taopix result array with boolean in valid key
	 */
	static function checkProductCanLink($pProductCode)
	{
		$resultArray = UtilsObj::getReturnArray('valid');
        $error  = '';
        $errorParam = '';
		$rowCount = 0;
        $bindResult = true;

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $sql = "SELECT count(*)
				FROM `pricelink`
				WHERE `linkedproductcode` = ?";

            $stmt = $dbObj->prepare($sql);

            if ($stmt)
            {
				$bindResult = $stmt->bind_param('s', $pProductCode);

                if ($bindResult)
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->bind_result($rowCount))
                            {
                                while ($stmt->fetch())
                                {
                                }
                            }
                            else
                            {
                                $error = 'str_DatabaseError';
                                $errorParam = __FUNCTION__ . ' bindresult ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $error = 'str_DatabaseError';
                            $errorParam = __FUNCTION__ . ' storeresult ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = 'str_DatabaseError';
                        $errorParam = __FUNCTION__ . ' execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = 'str_DatabaseError';
                    $errorParam = __FUNCTION__ . ' bindparam ' . $dbObj->error;
                }
            }
            else
            {
                $error = 'str_DatabaseError';
                $errorParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
            }
        }
        else
        {
            $error = 'str_DatabaseError';
            $errorParam = __FUNCTION__ . ' connect ' . $dbObj->error;
        }

        $resultArray['error'] = $error;
        $resultArray['errorparam'] = $errorParam;
		// only valid for linking if no products link to it
        $resultArray['valid'] = ($rowCount == 0);

        return $resultArray;
	}

	static function insertProductLinkingRecord($pProductCode, $pLinkedProductCode)
	{
		global $gSession;

		$resultArray = UtilsObj::getReturnArray();
        $error  = '';
        $errorParam = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
        {
			$stmt = $dbObj->prepare('INSERT INTO `PRICELINK` (`datecreated`) VALUES (now())');

			if ($stmt)
			{
				if ($stmt->execute())
				{
					$recordID = $dbObj->insert_id;

					if ($stmt2 = $dbObj->prepare('UPDATE `PRICELINK` SET `parentid` = ?, `productcode` = ?, `linkedproductcode` = ?, `active` = 1 WHERE `id` = ?'))
					{
						if ($stmt2->bind_param('issi', $recordID, $pProductCode, $pLinkedProductCode, $recordID))
						{
							if ($stmt2->execute())
							{
								DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
									'ADMIN', 'PRODUCTTREE-LINK', $pProductCode . ' ' . $pLinkedProductCode, 1);
							}
							else
							{
								$error = 'str_DatabaseError';
          						$errorParam = __FUNCTION__ . ' update execute ' . $dbObj->error;
							}
						}
						else
						{
							$error = 'str_DatabaseError';
          					$errorParam = __FUNCTION__ . ' update bind param ' . $dbObj->error;
						}

						$stmt2->free_result();
						$stmt2->close();
						$stmt2 = null;
					}
					else
					{
						$error = 'str_DatabaseError';
         				$errorParam = __FUNCTION__ . ' update prepare ' . $dbObj->error;
					}
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
        }
        else
        {
            $error = 'str_DatabaseError';
            $errorParam = __FUNCTION__ . ' connect ' . $dbObj->error;
        }

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;

		return $resultArray;
	}

	/**
	 * Deletes a pricelink table record by ID
	 * @param int $pRecordIDToDelete The record ID to delete
	 * @return array standard taopix error array
	 */
	static function deletePriceLinkRecordByID($pRecordIDToDelete)
	{
		$resultArray = UtilsObj::getReturnArray();
        $error  = '';
        $errorParam = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$stmt = $dbObj->prepare('DELETE FROM `pricelink` WHERE `id` = ?');

			if ($stmt)
			{
				$bindOK = $stmt->bind_param('i', $pRecordIDToDelete);

				if ($bindOK)
				{
					if (! $stmt->execute())
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' execute ' . $dbObj->error;
					}
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' bindparam ' . $dbObj->error;
				}
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
			}
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' connect ' . $dbObj->error;
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;

		return $resultArray;
	}

	static function checkProductDeletionWarnings($pProductCodes)
	{
		$resultArray = UtilsObj::getReturnArray('data');
        $error  = '';
        $errorParam = '';
        $bindResult = true;
		$productCodesArray = explode(',', $pProductCodes);
		$productCount = count($productCodesArray);
		$linkedProductCode = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $sql = "SELECT DISTINCT `linkedproductcode`
				FROM `pricelink`
				WHERE `linkedproductcode` IN (" . join(',', array_fill(0, $productCount, '?')) . ')';

            $stmt = $dbObj->prepare($sql);

            if ($stmt)
            {
				$dataTypes = str_repeat('s', $productCount);
				$bindResult = DatabaseObj::bindParams($stmt, $dataTypes, $productCodesArray);

                if ($bindResult)
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->bind_result($linkedProductCode))
                            {
                                while ($stmt->fetch())
                                {
									$resultArray['data'][] = $linkedProductCode;
                                }
                            }
                            else
                            {
                                $error = 'str_DatabaseError';
                                $errorParam = __FUNCTION__ . ' bindresult ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $error = 'str_DatabaseError';
                            $errorParam = __FUNCTION__ . ' storeresult ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = 'str_DatabaseError';
                        $errorParam = __FUNCTION__ . ' execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = 'str_DatabaseError';
                    $errorParam = __FUNCTION__ . ' bindparam ' . $dbObj->error;
                }
            }
            else
            {
                $error = 'str_DatabaseError';
                $errorParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
            }
        }
        else
        {
            $error = 'str_DatabaseError';
            $errorParam = __FUNCTION__ . ' connect ' . $dbObj->error;
        }

        $resultArray['error'] = $error;
        $resultArray['errorparam'] = $errorParam;

        return $resultArray;
	}

	/**
     * gets a list of products and whether they can be linked for display in the product linking drop down
     * @param string $pProductCode the product code to display results for
     * @return array Standard taopix error array with products in data key with subkeys id, code and valid
     */
    static function getLinkingPreviewGridData($pProductCode)
    {
		global $gSession;

        $resultArray = UtilsObj::getReturnArray();
        $error  = '';
        $errorParam = '';
        $productID = 0;
        $productCode = '';
		$productName = '';
        $productCompanyCode = '';
        $productArray = Array();
        $productsArray = Array();
        $bindResult = true;
		$companyCode = self::convertProductConfigCompanyCodeForDatabase($gSession['userdata']['companycode']);

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $sql = "SELECT DISTINCT `p`.`id`, `p`.`code`, `p`.`name`, `p`.`companycode`
                FROM `products` AS `p`
                LEFT JOIN `pricelink` AS `pl` ON (`p`.`code` = `pl`.`productcode` AND `pl`.`linkedproductcode` != '')
				WHERE `pl`.`linkedproductcode` = ?";

            if ($companyCode !== '')
            {
                $sql .= " AND `p`.`companycode` IN ('', ?)";
            }

            $sql .= " ORDER BY `p`.`code`";

            $stmt = $dbObj->prepare($sql);

            if ($stmt)
            {
                if ($companyCode !== '')
                {
                    $bindResult = $stmt->bind_param('ss', $pProductCode, $companyCode);
                }
				else
				{
					$bindResult = $stmt->bind_param('s', $pProductCode);
				}

                if ($bindResult)
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->bind_result($productID, $productCode, $productName, $productCompanyCode))
                            {
                                while ($stmt->fetch())
                                {
                                    $productArray = Array();

                                    $productArray['id'] = $productID;
                                    $productArray['code'] = $productCode;
                                    $productArray['name'] = $productName;
									$productArray['companycode'] = $productCompanyCode;

                                    $productsArray[] = $productArray;
                                }
                            }
                            else
                            {
                                $error = 'str_DatabaseError';
                                $errorParam = __FUNCTION__ . ' bindresult ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $error = 'str_DatabaseError';
                            $errorParam = __FUNCTION__ . ' storeresult ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = 'str_DatabaseError';
                        $errorParam = __FUNCTION__ . ' execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = 'str_DatabaseError';
                    $errorParam = __FUNCTION__ . ' bindparam ' . $dbObj->error;
                }
            }
            else
            {
                $error = 'str_DatabaseError';
                $errorParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
            }
        }
        else
        {
            $error = 'str_DatabaseError';
            $errorParam = __FUNCTION__ . ' connect ' . $dbObj->error;
        }

        $resultArray['error'] = $error;
        $resultArray['errorparam'] = $errorParam;
        $resultArray['data'] = $productsArray;

        return $resultArray;
    }

	/**
	 * gets the linked product code for a product
	 * @param string $pProductCode the product code to check
	 * @return array standard taopix error array with the link information in the data=> id, code keys
	 */
	static function getLinkedProductCode($pProductCode)
	{
		$resultArray = self::getProductLinkPriceLinkRecord($pProductCode);

		return $resultArray;
	}
}
?>
