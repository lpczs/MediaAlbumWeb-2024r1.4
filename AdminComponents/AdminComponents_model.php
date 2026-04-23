<?php

class AdminComponents_model
{

    static function getGridData()
    {
        global $gSession;

        $start = 0;
	    $limit = 100;
	    $total = 0;
		$totalRowCount = 50000;
	    $resultArray = Array();
	    $params = Array();
	    $sortby = 'code';
	    $dir = 'ASC';
	    $i = 0;
	    $componentCategoryCode = $_GET['category'];
	    $categoryCompanyCode = $_GET['categorycompanycode'];
		$bind = 1;
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

		// check that hideinactive has been sent before safely retrieving it
		if (isset($_POST['hideInactive']))
		{
			$hideInactive = filter_input(INPUT_POST, 'hideInactive', FILTER_SANITIZE_NUMBER_INT);
		}

	 	switch ($sortby)
	 	{
			case 'companycode':
				$sort = 'companycode';
				break;
			case 'category':
				$sort = 'category';
				break;
			case 'active':
				$sort = 'active';
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

        switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				// getting products for the system administrator
				$stmt = 'SELECT SQL_CALC_FOUND_ROWS `id`, `companycode`, `code`, `localcode`, `name`, `categorycode`, `skucode`, `active` FROM `COMPONENTS` WHERE `categorycode` = ? ';
				$params[] = $componentCategoryCode;
			break;
			case TPX_LOGIN_COMPANY_ADMIN:
				
				if ($categoryCompanyCode == '')
				{
					// getting products based on companycode of company administrator
					$stmt = 'SELECT SQL_CALC_FOUND_ROWS `id`, `companycode`, `code`, `localcode`, `name`, `categorycode`, `skucode`, `active` FROM `COMPONENTS` WHERE (`categorycode` = ?) AND (`companycode` = ? OR `companycode` = "") ';
					$params[] = $componentCategoryCode;
					$params[] = $gSession['userdata']['companycode'];
				}
				else
				{
					// getting products based on companycode of company administrator
					$stmt = 'SELECT SQL_CALC_FOUND_ROWS `id`, `companycode`, `code`, `localcode`, `name`, `categorycode`, `skucode`, `active` FROM `COMPONENTS` WHERE `categorycode` = ? AND `companycode` = ?';
					$params[] = $componentCategoryCode;
					$params[] = $gSession['userdata']['companycode'];
				}
				
			break;
		}

		$searchFields = UtilsObj::getPOSTParam('fields');

		//  getting search filter fields
		if ($searchFields != '')
		{

			$searchQuery = $_POST['query'];
			$selectedfields = str_replace("[", "",$searchFields);
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
				if ($hideInactive == 1)
				{
					$stmt .= ' AND (`active` = 1) ';
				}
			}
		}
		else
		{
			if ($hideInactive == 1)
			{
				$stmt .= ' AND (`active` = 1) ';
			}
		}

        $orderBy = ' ORDER BY `companycode`, `' . $sort . '` ' . $dir . ' LIMIT ' . $limit . ' OFFSET ' . $start . ';';

		$componentArray = self::bindParams($stmt, $params, $bind, $orderBy);

		$resultArray['components'] = $componentArray['components'];
        $resultArray['total'] = $componentArray['total'];

        return $resultArray;
	}

    static function componentAdd()
    {
		global $gSession;
		global $gConstants;
		global $ac_config;

        $result = '';
        $resultParam = '';
        $recordID = 0;

        $companyCode = '';
        $componentCategoryCode = $_POST['categorycode'];
        $localCode = strtoupper($_POST['localcode']);
        $keyWordGroupHeaderID = $_POST['metadatagroupheader'];
        $orderFooterUsesProductQuantity = $_POST['orderfooterusesprodqty'];
        $orderFooterTaxLevel = $_POST['orderfootertaxlevel'];
        $storeWhenNotSelected = 1;
        
        $componentCode = $componentCategoryCode.'.'.$localCode;

        switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				if ($gConstants['optionms'])
        		{
        			$companyCode = $_POST['company'];

					if($companyCode != 'GLOBAL')
					{
						$componentCode = $companyCode.'.'.$componentCategoryCode.'.'.$localCode;
					}
					else
					{
						$companyCode = '';
					}
        		}
			break;
			case TPX_LOGIN_COMPANY_ADMIN:
				$companyCode = $gSession['userdata']['companycode'];
        		$componentCode = $companyCode.'.'.$componentCategoryCode.'.'.$localCode;
			break;
		}

        $skuCode = $_POST['skucode'];
        $componentName = html_entity_decode($_POST['componentname'], ENT_QUOTES);

        $componentInfo = html_entity_decode($_POST['componentinfo'], ENT_QUOTES);
		$moreInfoLinkURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('componentmoreinfolinkurl'));
		$moreInfoLinkText = html_entity_decode(UtilsObj::getPOSTParam('componentmoreinfolinktext'), ENT_QUOTES);
		$unitCost = $_POST['unitcost'];
		$minimumPageCount = $_POST['minpagecount'];
		$maximumPageCount = $_POST['maxpagecount'];
		$shippingWeight = $_POST['weight'];
		$isList = $_POST['isList'];
		$default = -1;

		if ($isList == 0)
		{
			$default = $_POST['default'];
			$storeWhenNotSelected =  $_POST['storewhennotselected'];
		}

        $isActive = $_POST['isactive'];

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			if ($_POST['previewupdate'] == '1')
			{
				$uniqueID = md5($componentCode);
				$destinationFolder = UtilsObj::correctPath($ac_config['CONTROLCENTREPREVIEWSPATH'], DIRECTORY_SEPARATOR, true) . 'components' . DIRECTORY_SEPARATOR . $uniqueID . DIRECTORY_SEPARATOR;
				$extension = UtilsObj::getExtensionFromImageType($gSession['previewtype']);
				$resultParam = UtilsObj::moveUploadedFile($gSession['previewpath'], $destinationFolder . $uniqueID . $extension);

				if ($resultParam !== '')
				{
					$result = 'str_UploadError';
				}
			}

        	if ($result === '')
        	{
				if ($stmt = $dbObj->prepare('INSERT INTO `COMPONENTS` VALUES (0, now(), now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
				{
					if ($stmt->bind_param('sssssssssdiidiiiiii', $companyCode, $componentCategoryCode, $componentCode, $localCode, $skuCode, $componentName, $componentInfo, $moreInfoLinkURL, $moreInfoLinkText, $unitCost, $minimumPageCount, $maximumPageCount, $shippingWeight, $default, $keyWordGroupHeaderID, $orderFooterUsesProductQuantity, $orderFooterTaxLevel, $storeWhenNotSelected, $isActive))
					{
						if ($stmt->execute())
						{
							$recordID = $dbObj->insert_id;

							DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
								'ADMIN', 'COMPONENT-ADD', $recordID . ' ' . $localCode, 1);
						}
						else
						{
							// could not execute statement
							// first check for a duplicate key (tax rate code)
							if ($stmt->errno == 1062)
							{
								$result = 'str_ErrorComponentExists';
							}
							else
							{
								$result = 'str_DatabaseError';
								$resultParam = 'componentAdd execute ' . $dbObj->error;
							}
						}
					}
					else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = 'componentAdd bind ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = 'componentAdd prepare ' . $dbObj->error;
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
        $resultArray['id'] = $recordID;
        $resultArray['company'] = $companyCode;
        $resultArray['categorycode'] = $componentCategoryCode;
        $resultArray['code'] = $componentCode;
        $resultArray['localcode'] = $localCode;
        $resultArray['skucode'] = $skuCode;
        $resultArray['name'] = $componentName;
        $resultArray['info'] = $componentInfo;
        $resultArray['unitcost'] = $unitCost;
        $resultArray['minimumpagecount'] = $minimumPageCount;
        $resultArray['maximumpagecount'] = $maximumPageCount;
        $resultArray['weight'] = $shippingWeight;
        $resultArray['default'] = $default;
        $resultArray['keywordgroupheaderid'] = $keyWordGroupHeaderID;
        $resultArray['isactive'] = $isActive;

        return $resultArray;
    }

    static function componentEdit()
    {
		global $gSession;
		global $gConstants;
		global $ac_config;

        $result = '';
        $resultParam = '';
        $recordID = 0;
		$companyCode = '';
        $componentCategoryCode = $_POST['categorycode'];
        $localCode = strtoupper($_POST['localcode']);
        $componentCode = strtoupper($_POST['componentcode']);

        switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				if ($gConstants['optionms'])
        		{
        			$companyCode = $_POST['company'];

					if($companyCode == 'GLOBAL')
					{
						$companyCode = '';
					}
        		}
			break;
			case TPX_LOGIN_COMPANY_ADMIN:
				$companyCode = $gSession['userdata']['companycode'];
			break;
		}

        $componentID = $_GET['id'];
        $skuCode = $_POST['skucode'];
        $componentName = html_entity_decode($_POST['componentname'], ENT_QUOTES);
        $componentInfo = html_entity_decode($_POST['componentinfo'], ENT_QUOTES);
		$moreInfoLinkURL = UtilsObj::getValidUrl(UtilsObj::getPOSTParam('componentmoreinfolinkurl'));
		$moreInfoLinkText = html_entity_decode(UtilsObj::getPOSTParam('componentmoreinfolinktext'), ENT_QUOTES);
		$unitCost = $_POST['unitcost'];
		$minimumPageCount = $_POST['minpagecount'];
		$maximumPageCount = $_POST['maxpagecount'];
		$shippingWeight = $_POST['weight'];
		$isList = $_POST['isList'];
		$default = -1;
		$storeWhenNotSelected = 1;

		if ($isList == 0)
		{
			$default = $_POST['default'];
			$storeWhenNotSelected = $_POST['storewhennotselected'];
		}

        $keyWordGroupHeaderID = $_POST['metadatagroupheader'];
        $orderFooterUsesProductQuantity = $_POST['orderfooterusesprodqty'];
        $orderFooterTaxLevel = $_POST['orderfootertaxlevel'];
        $isActive = $_POST['isactive'];
        $deleted = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $uniqueID = md5($componentCode);
			$destinationFolder = UtilsObj::correctPath($ac_config['CONTROLCENTREPREVIEWSPATH'], DIRECTORY_SEPARATOR, true) . 'components' . DIRECTORY_SEPARATOR . $uniqueID . DIRECTORY_SEPARATOR;

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
				if ($stmt = $dbObj->prepare('UPDATE `COMPONENTS` SET `datelastmodified` = now(), `companycode` = ?, `skucode` = ?, `name` = ?, `info` = ?, `moreinfolinkurl` = ?, `moreinfolinktext` = ?,
					`unitcost` = ?, `minimumpagecount` = ?, `maximumpagecount` = ?, `weight` = ?, `default` = ?, `keywordgroupheaderid` = ?, `orderfooterusesproductquantity` = ?,
					`orderfootertaxlevel` = ?, `storewhennotselected` = ?, `active` = ? WHERE `code` = ?'))
				{
					if ($stmt->bind_param('ssssssdiidiiiiiis', $companyCode, $skuCode, $componentName, $componentInfo, $moreInfoLinkURL, $moreInfoLinkText, $unitCost, $minimumPageCount, $maximumPageCount, $shippingWeight, $default, $keyWordGroupHeaderID, $orderFooterUsesProductQuantity, $orderFooterTaxLevel, $storeWhenNotSelected, $isActive, $componentCode))
					{
						if ($stmt->execute())
						{
							DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
								'ADMIN', 'COMPONENT-UPDATE', $componentID . ' ' . $componentCode, 1);
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'componentsEdit execute ' . $dbObj->error;
						}
					}
					else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = 'componentsEdit bind ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = 'componentsEdit prepare ' . $dbObj->error;
				}
			}
            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'componentsEdit connect ' . $dbObj->error;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $recordID;

        $resultArray['company'] = $companyCode;
        $resultArray['categorycode'] = $componentCategoryCode;
        $resultArray['code'] = $componentCode;
        $resultArray['localcode'] = $localCode;
        $resultArray['skucode'] = $skuCode;
        $resultArray['name'] = $componentName;
        $resultArray['info'] = $componentInfo;
        $resultArray['unitcost'] = $unitCost;
        $resultArray['minimumpagecount'] = $minimumPageCount;
        $resultArray['maximumpagecount'] = $maximumPageCount;
        $resultArray['weight'] = $shippingWeight;
        $resultArray['default'] = $default;
        $resultArray['keywordgroupheaderid'] = $keyWordGroupHeaderID;
        $resultArray['isactive'] = $isActive;

        return $resultArray;
    }


    static function editDisplay($pID)
	{
	    global $gConstants;
	    
	    $resultArray = Array();
	    $id = 0;
	    $companyCode = '';
	    $categoryCode = '';
	    $categoryCompanyCode = '';
	    $code = '';
	    $localcode = '';
	    $skucode = '';
	    $name = '';
	    $info = '';
		$moreInfoLinkURL = '';
		$moreInfoLinkText = '';
	    $unitCost = 0;
	    $minPageCount = 0;
	    $maxPageCount = 0;
	    $weight = 0;
	    $default = 0;
	    $keyWordGroupHeaderID = '';
	    $active = 0;
	    $categoryCompanyCode = '';
	    $orderFooterUsesProductQuantity = 0;
	    $orderFooterTaxLevel = 1;
	    $storeWhenNotSelected = 1;
		
		if ($gConstants['optionms'])
		{
			$categoryCompanyCode = $_GET['categorycompanycode'];
		}
		
        $dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
	    {
	        if ($stmt = $dbObj->prepare('SELECT `id`, `companycode`, `categorycode`, `code`, `localcode`, `skucode`, `name`, `info`,
				`moreinfolinkurl`, `moreinfolinktext`,
				`unitcost`, `minimumpagecount`, `maximumpagecount`, `weight`, `default`, `keywordgroupheaderid`, `orderfooterusesproductquantity`, `orderFooterTaxLevel`,
				`storewhennotselected`, `active`
				FROM `COMPONENTS` WHERE `id` = ?'))
	        {                
                if ($stmt->bind_param('i', $pID))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            { 
                                if ($stmt->bind_result($id, $companyCode, $categoryCode, $code, $localcode, $skucode, $name, $info,
									$moreInfoLinkURL, $moreInfoLinkText,
									$unitCost, $minPageCount, $maxPageCount, $weight, $default, $keyWordGroupHeaderID, $orderFooterUsesProductQuantity, $orderFooterTaxLevel,
									$storeWhenNotSelected, $active))
                                {                                
                                    if(!$stmt->fetch())
                                    {
                                        $error = 'functionName editDisplay ' . $dbObj->error;
                                    }  
                                }
                                else
                                {
                                    $error = 'functionName bind result ' . $dbObj->error;
                                }
                            }
                        }
                        else
                        {
                            $error = 'functionName store result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = 'functionName execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = 'functionName bind params ' . $dbObj->error;
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
	        }
            else
            {
                $error = 'functionName prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }

        $resultArray['id'] = $id;
	    $resultArray['companycode'] = $companyCode;
	    $resultArray['categorycompanycode'] = $categoryCompanyCode;
	    $resultArray['categorycode'] = $categoryCode;
	    $resultArray['code'] = $code;
	    $resultArray['localcode'] = $localcode;
	    $resultArray['skucode'] = $skucode;
	    $resultArray['name'] = $name;
	    $resultArray['info'] = $info;
		$resultArray['moreinfolinkurl'] = $moreInfoLinkURL;
		$resultArray['moreinfolinktext'] = $moreInfoLinkText;
	    $resultArray['unitcost'] = $unitCost;
	    $resultArray['minpagecount'] = $minPageCount;
	    $resultArray['maxpagecount'] = $maxPageCount;
	    $resultArray['weight'] = $weight;
	    $resultArray['default'] = $default;
	    $resultArray['keywordgroupheaderid'] = $keyWordGroupHeaderID;
	    $resultArray['orderfooterusesproductquantity'] = $orderFooterUsesProductQuantity;
	    $resultArray['orderfootertaxlevel'] = $orderFooterTaxLevel;
	    $resultArray['storewhennotselected'] = $storeWhenNotSelected;
	    $resultArray['active'] = $active;

        return $resultArray;
    }

    static function componentsDelete()
    {
        global $gSession;

        $resutArray = Array();
        $result = '';
        $resultParam = '';
        $allDeleted = 1;

        $componentIDList = explode(',',$_POST['idlist']);
        $componentCodeList = explode(',',$_POST['codelist']);
        $componentListCount = count($componentIDList);

        $componentsDeleted = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            for ($i = 0; $i < $componentListCount; $i++)
            {
            	$canDelete = true;
            	
            	if ($stmt = $dbObj->prepare('SELECT `id` FROM `PRICELINK` WHERE `componentcode` = ? AND `productcode` <> ""'))
            	{
                	if ($stmt->bind_param('s', $componentCodeList[$i]))
                	{
                    	if ($stmt->execute())
                    	{
                        	if ($stmt->fetch())
                        	{
                        		$result = 'str_ErrorComponentAttachedToAProduct';
                        		$canDelete = false;
                        		$allDeleted = 0;
                        	}
                    	}
                	}
                	$stmt->free_result();
                	$stmt->close();
                	$stmt = null;
            	}
            	
            	if ($canDelete)
            	{
            		if ($stmt = $dbObj->prepare('DELETE FROM `COMPONENTS` WHERE `id` = ?'))
	            	{
                    	if ($stmt->bind_param('i', $componentIDList[$i]))
                    	{
                        	if ($stmt->execute())
                        	{
                            	DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                	'ADMIN', 'COMPONENT-DELETE',$componentIDList[$i] . ' ' . $componentCodeList[$i], 1);
                                array_push($componentsDeleted, $componentIDList[$i]);
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
        $resultArray['componentids'] = $componentsDeleted;
        $resultArray['result'] = $result;

        return $resultArray;
    }


    static function componentTypesActivate()
    {
        global $gSession;

        $resultArray = Array();
        $ids = $_POST['ids'];
        $codes = $_POST['codelist'];
        $codeList = explode(',',$codes);

        $idList = explode(',',$ids);
        $active = $_POST['active'];
        if ($active != '0') $active = 1;

        $itemCount = count($idList);

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('UPDATE `COMPONENTS` SET `datelastmodified` = now(), `active` = ? WHERE `id` = ?'))
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
	                                    'ADMIN', 'COMPONENT-DEACTIVATE', $idList[$i] . ' ' . $codeList[$i], 1);
	                        }
	                        else
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                    'ADMIN', 'COMPONENT-ACTIVATE', $idList[$i] . ' ' . $codeList[$i], 1);
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

    static function bindParams($pStatement,$pParams, $pBind, $pOrderBy)
    {
		$componentItemArray = Array();
		$resultArray = Array();
		$sqlBindTypes = '';
		$sqlBindVars = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();

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

		// Concatenate the order by stament to original query so that a limit is set.
		// This is for paging.
		$pStatement .= $pOrderBy;

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
					if ($stmt->bind_result($componentID, $componentCompanyCode, $componentCode, $componentLocalCode,  $componentName, $categoryCode, $componentSkuCode, $componentActive))
	                {
	                    if ($stmt->execute())
	                    {
	                        while ($stmt->fetch())
	                        {
	                            $componentItem['id'] = $componentID;
								$componentItem['companycode'] = $componentCompanyCode;
								$componentItem['code'] = $componentCode;
								$componentItem['localcode'] = $componentLocalCode;
								$componentItem['name'] = $componentName;
								$componentItem['categorycode'] = $categoryCode;
								$componentItem['skucode'] = $componentSkuCode;
								$componentItem['active'] = $componentActive;
								array_push($componentItemArray, $componentItem);
	                        }
	                    }
	                }
	                
	                if (($stmt = $dbObj->prepare("SELECT FOUND_ROWS()")) && ($stmt->bind_result($totalRecords)))
					{
						if ($stmt->execute()) 
						{
							$stmt->fetch();
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
		$resultArray['components'] = $componentItemArray;

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
}
?>
