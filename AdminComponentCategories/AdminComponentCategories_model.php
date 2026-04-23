<?php

require_once('../Utils/UtilsDatabase.php');

class AdminComponentCategories_model
{
    static function getGridData()
    {
        global $gSession;
        global $gConstants;

        $resultArray = Array();
		$companyCode = '';
		$hideInactive = 0;

		// check that hideinactive has been sent before safely retrieving it
		if (isset($_POST['hideInactive']))
		{
			$hideInactive = filter_input(INPUT_POST, 'hideInactive', FILTER_SANITIZE_NUMBER_INT);
		}

		if ($gConstants['optionms'])
        {
			if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
			{
				$companyCode = $gSession['userdata']['companycode'];
			}
		}


		$resultArray = DatabaseObj::componentCategoriesList($companyCode, false, $hideInactive);

        return $resultArray;
	}

    static function componentCategoriesAdd()
    {
        global $gSession;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $sectionLabel = '';
        $recordID = 0;
        $sortOrder = 0;
        $componentCategoryCode = strtoupper($_POST['code']);
        $componentCategoryName = html_entity_decode($_POST['name'], ENT_QUOTES);
        $componentCategoryPrompt = html_entity_decode($_POST['prompt'], ENT_QUOTES);
        $componentCategoryPricingModel = $_POST['pricingModelCombo'];
        $componentCategoryDisplayType = $_POST['displayTypeCombo'];
		$componentCategoryOnlineDisplayStage = $_POST['displaystage'];
        $active = $_POST['isactive'];
        $requiresPageCount = $_POST['requirespagecount'];
        $numberOfDecimalPlaces = $_POST['numberofdeciamalplaces'];
        $companyCode = '';

        switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				if ($gConstants['optionms'])
        		{
        			if ($componentCategoryCode != 'COVER' && $componentCategoryCode != 'PAPER')
        			{
        				$companyCode = $_POST['company'];
        			}
        			else
        			{
        				$companyCode = '';
        			}

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


        if (($componentCategoryCode != '') && ($componentCategoryName != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('INSERT INTO `COMPONENTCATEGORIES` (`id`, `datecreated`, `companycode`, `code`, `name`, `prompt`, `pricingmodel`, `islist`, `requirespagecount`, `componentpricingdecimalplaces`, `displaystage`, `active`) VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
                {
                    if ($stmt->bind_param('ssssiiiiii', $companyCode, $componentCategoryCode, $componentCategoryName, $componentCategoryPrompt,$componentCategoryPricingModel,$componentCategoryDisplayType, $requiresPageCount, $numberOfDecimalPlaces, $componentCategoryOnlineDisplayStage, $active))
                    {
                        if ($stmt->execute())
                        {
                            $recordID = $dbObj->insert_id;
                            $sectionLabel = $componentCategoryName;

                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                'ADMIN', 'COMPONENTCATEGORY-ADD', $recordID . ' ' . $componentCategoryCode, 1);

                            if ($componentCategoryDisplayType == 1)
                            {
                            	if ($stmt2 = $dbObj->prepare('SELECT MAX(`sortorder`) FROM `SECTIONS` WHERE `code` <> "LINEFOOTER" AND `code` <> "ORDERFOOTER"'))
                				{
                					if ($stmt2->bind_result($count))
									{
										if ($stmt2->execute())
										{
											if ($stmt2->fetch())
											{
												$sortOrder = $count + 1;
											}
										}
									}

                					$stmt2->free_result();
                    				$stmt2->close();
                					$stmt2 = null;
                				}

                            	if ($stmt2 = $dbObj->prepare('INSERT INTO `SECTIONS` (`id`, `datecreated`, `companycode`, `code`, `name`, `label`, `categorycode`, `displaytype`, `sortorder`, `active`) VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?)'))
                				{
                					 if ($stmt2->bind_param('sssssiii', $companyCode, $componentCategoryCode, $componentCategoryName, $sectionLabel, $componentCategoryCode, $componentCategoryDisplayType, $sortOrder, $active))
                    				 {
                    				 	if ($stmt2->execute())
                    				 	{
                    				 		$sectionID = $dbObj->insert_id;

                    				 		DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                			'ADMIN', 'SECTION-ADD', $sectionID . ' ' . $componentCategoryCode, 1);
                    				 	}

                    				 }

                    				$stmt2->free_result();
                    				$stmt2->close();
                					$stmt2 = null;
                				}
                            }

                        }
                        else
                        {
                            // could not execute statement
                            // first check for a duplicate key
                            if ($stmt->errno == 1062)
                            {
                            	$result = 'str_ErrorComponentCategoryExists';
                            }
                            else
                            {
                            	$result = 'str_DatabaseError1';
                            	$resultParam = 'componentCategoryAdd execute ' . $dbObj->error;
                            }
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError2';
                        $resultParam = 'componentCategoryAdd bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                	$stmt = null;
                	$dbObj->close();
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError3';
                    $resultParam = 'componentCategoryAdd prepare ' . $dbObj->error;
                }
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
        $resultArray['companycode'] = $companyCode;
        $resultArray['code'] = $componentCategoryCode;
        $resultArray['name'] = $componentCategoryName;
        $resultArray['prompt'] = $componentCategoryPrompt;
        $resultArray['pricingmodel'] = $componentCategoryPricingModel;
        $resultArray['displaytype'] = $componentCategoryDisplayType;
        $resultArray['requirespagecount'] = $requiresPageCount;
        $resultArray['active'] = $active;
		$resultArray['displaystage'] = $componentCategoryOnlineDisplayStage;

        return $resultArray;
    }

    static function componentCategoriesdisplayEdit($pID)
	{
	    $resultArray = Array();
	    $componentCompanyCode = '';
	    $componentCategoryID = 0;
	    $componentCategoryCode = '';
	    $componentCategoryName = '';
	    $componentCategoryPrompt = '';
        $componentCategoryPricingModel = '';
        $componentCategoryIsList = '';
        $active = '';
        $hasComponents = 0;
        $requiresPageCount = 0;
        $componetPricingDecimalPlaces = 2;
        $isPrivate = 0;
		$displayStage = 1;

        $dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
	    {
	        if ($stmt = $dbObj->prepare('SELECT `id`, `companycode`, `code`, `name`, `prompt`, `pricingmodel`, `islist`, `requirespagecount`, `componentpricingdecimalplaces`, `private`, `displaystage`, `active` FROM `COMPONENTCATEGORIES` WHERE `id` = ?'))
	        {
                if ($stmt->bind_param('i', $pID))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($componentCategoryID,$componentCompanyCode, $componentCategoryCode, $componentCategoryName, $componentCategoryPrompt,$componentCategoryPricingModel, $componentCategoryIsList, $requiresPageCount, $componetPricingDecimalPlaces, $isPrivate, $displayStage, $active))
                                {
                                    if (!$stmt->fetch())
                                    {
                                        $error = 'componentCategoriesdisplayEdit COMPONENTCATEGORIES fetch ' . $dbObj->error;
                                    }
                                }
                                else
                                {
                                    $error = 'componentCategoriesdisplayEdit COMPONENTCATEGORIES bind result ' . $dbObj->error;
                                }
                            }
                        }
                        else
                        {
                            $error = 'componentCategoriesdisplayEdit COMPONENTCATEGORIES store result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = 'componentCategoriesdisplayEdit COMPONENTCATEGORIES execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = 'componentCategoriesdisplayEdit COMPONENTCATEGORIES bind params ' . $dbObj->error;
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
	        }
            else
            {
                $error = 'componentCategoriesdisplayEdit COMPONENTCATEGORIES prepare ' . $dbObj->error;
            }

            if ($stmt = $dbObj->prepare('SELECT `id` FROM `COMPONENTS` WHERE `categorycode` = ?'))
	        {
                if ($stmt->bind_param('s', $componentCategoryCode))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                $hasComponents = 1;
                            }
                        }
                        else
                        {
                            $error = 'componentCategoriesdisplayEdit COMPONENTS store result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = 'componentCategoriesdisplayEdit COMPONENTS execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $error = 'componentCategoriesdisplayEdit COMPONENTS bind params ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
	        }
            else
            {
                $error = 'componentCategoriesdisplayEdit COMPONENTS prepare ' . $dbObj->error;
            }

            $dbObj->close();
        }


        $resultArray['id'] = $componentCategoryID;
        $resultArray['companycode'] = $componentCompanyCode;
	    $resultArray['code'] = $componentCategoryCode;
	    $resultArray['name'] = $componentCategoryName;
	    $resultArray['prompt'] = $componentCategoryPrompt;
	    $resultArray['pricingmodel'] = $componentCategoryPricingModel;
	    $resultArray['islist'] = $componentCategoryIsList;
	    $resultArray['requirespagecount'] = $requiresPageCount;
	    $resultArray['componetpricingdecimalplaces'] = $componetPricingDecimalPlaces;
	    $resultArray['isprivate'] = $isPrivate;
	    $resultArray['active'] = $active;
	    $resultArray['hascomponents'] = $hasComponents;
		$resultArray['displaystage'] = $displayStage;

        return $resultArray;
    }

    static function componentCategoriesEdit()
    {
        global $gSession;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $sectionLabel = '';

        $componentCategoryID = $_GET['id'];
        $componentCategoryCode = strtoupper($_POST['code']);
        $componentCategoryName = html_entity_decode($_POST['name'], ENT_QUOTES);
        $componentCategoryPrompt = html_entity_decode($_POST['prompt'], ENT_QUOTES);
        $componentCategoryPricingModel = $_POST['pricingModelCombo'];
        $componentCategoryDisplayType = $_POST['displayTypeCombo'];
		$componentCategoryOnlineDisplayStage = $_POST['displaystage'];
        $active = $_POST['isactive'];
        $requiresPageCount = $_POST['requirespagecount'];
        $originalDisplayType = $_POST['originaldisplaytype'];
        $numberOfDecimalPlaces = $_POST['numberofdeciamalplaces'];
        $companyCode = '';

        switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				if ($gConstants['optionms'])
        		{
        			if ($componentCategoryCode != 'COVER' && $componentCategoryCode != 'PAPER')
        			{
        				$companyCode = $_POST['company'];
        			}
        			else
        			{
        				$companyCode = '';
        			}

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

        if (($componentCategoryCode != '') && ($componentCategoryName != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('UPDATE `COMPONENTCATEGORIES` SET  `companycode` = ?, `name` = ?, `prompt` = ?, `pricingmodel` = ?, `islist` = ?, `requirespagecount` = ?, `componentpricingdecimalplaces` = ?, `displaystage` = ?, `active` = ?  WHERE `code` = ?'))
                {
                    if ($stmt->bind_param('sssiiiiiis', $companyCode, $componentCategoryName, $componentCategoryPrompt, $componentCategoryPricingModel,  $componentCategoryDisplayType, $requiresPageCount, $numberOfDecimalPlaces, $componentCategoryOnlineDisplayStage, $active, $componentCategoryCode))
                    {
                        if ($stmt->execute())
                        {
                            $sectionLabel = $componentCategoryName;

                            if ($stmt2 = $dbObj->prepare('UPDATE `SECTIONS`  SET `companycode` = ?, `label` = ?, `name` = ?, `active` = ? WHERE `code` = ?'))
            				{
            					 if ($stmt2->bind_param('sssis', $companyCode, $componentCategoryName, $sectionLabel, $active, $componentCategoryCode))
                				 {
                				 	$stmt2->execute();

                				 }

                				$stmt2->free_result();
                				$stmt2->close();
            					$stmt2 = null;
            				}

                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                'ADMIN', 'COMPONENTCATEGORY-UPDATE', $componentCategoryID . ' ' . $componentCategoryCode, 1);

                             if ($originalDisplayType <> $componentCategoryDisplayType)
                             {
                             	if ($componentCategoryDisplayType == 1)
                             	{
                             		//create new section as the display type for this category is no longer a checkbox

                             		if ($stmt2 = $dbObj->prepare('SELECT MAX(`sortorder`) FROM `SECTIONS` WHERE `code` <> "LINEFOOTER" AND `code` <> "ORDERFOOTER"'))
	                				{
	                					if ($stmt2->bind_result($count))
										{
											if ($stmt2->execute())
											{
												if ($stmt2->fetch())
												{
													$sortOrder = $count + 1;
												}
											}
										}

	                					$stmt2->free_result();
	                    				$stmt2->close();
	                					$stmt2 = null;
	                				}

	                            	if ($stmt2 = $dbObj->prepare('INSERT INTO `SECTIONS` (`id`, `datecreated`, `companycode`,  `code`, `name`, `label`, `categorycode`, `displaytype`, `sortorder`, `active`) VALUES (0, now(), ?, ?, ?,?, ?, ?, ?, ?)'))
	                				{
	                					 if ($stmt2->bind_param('sssssiii', $companyCode, $componentCategoryCode, $componentCategoryName, $sectionLabel, $componentCategoryCode, $componentCategoryDisplayType, $sortOrder, $active))
	                    				 {
	                    				 	if ($stmt2->execute())
	                    				 	{
	                    				 		$sectionID = $dbObj->insert_id;

	                    				 		DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                			'ADMIN', 'SECTION-ADD', $sectionID . ' ' . $componentCategoryCode, 1);
	                    				 	}
	                    				 }

	                    				$stmt2->free_result();
	                    				$stmt2->close();
	                					$stmt2 = null;
	                				}
                             	}
                             	else
                             	{
                             		//delete section as the displaytype for the category is no longer a list
                             		if ($stmt2 = $dbObj->prepare('DELETE FROM `SECTIONS` WHERE `code` = ?'))
	                				{
	                					 if ($stmt2->bind_param('s', $componentCategoryCode))
	                    				 {
	                    				 	if ($stmt2->execute())
	                    				 	{
	                    				 		$sectionID = $dbObj->insert_id;

	                    				 		DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                			'ADMIN', 'SECTION-DELETE', $sectionID . ' ' . $componentCategoryCode, 1);
	                    				 	}
	                    				 }

	                    				$stmt2->free_result();
	                    				$stmt2->close();
	                					$stmt2 = null;
	                				}
                             	}

                             }

                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'componenetCategoryEdit execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'componenetCategoryEdit bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
	                $stmt->close();
	                $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'componenetCategoryEdit prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'componenetCategoryEdit connect ' . $dbObj->error;
            }
        }
        $resultArray['result'] = $result;
        $resultArray['id'] = $componentCategoryID;
        $resultArray['companycode'] = $companyCode;
        $resultArray['code'] = $componentCategoryCode;
        $resultArray['name'] = $componentCategoryName;
        $resultArray['prompt'] = $componentCategoryPrompt;
        $resultArray['pricingmodel'] = $componentCategoryPricingModel;
        $resultArray['displaytype'] = $componentCategoryDisplayType;
        $resultArray['requirespagecount'] = $requiresPageCount;
        $resultArray['active'] = $active;
		$resultArray['displaystage'] = $componentCategoryOnlineDisplayStage;

        return $resultArray;
    }


    static function componentCategoriesDelete()
    {
        global $gSession;

        $resutArray = Array();
        $result = '';
        $resultParam = '';
        $allDeleted = 1;
        $canDelete = false;

        $componentCategoryIDList = explode(',',$_POST['idlist']);
        $componentCategoryCodeList = explode(',',$_POST['codelist']);
        $componentCategoryDisplayTypes  = explode(',',$_POST['displaytype']);
        $componentCategoryListCount = count($componentCategoryIDList);

        $categoriesDeleted = Array();
        $categoriesNotUsed = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            for ($i = 0; $i < $componentCategoryListCount; $i++)
            {
            	// first make sure the component category hasn't been used
	            if ($stmt = $dbObj->prepare('SELECT `id` FROM `COMPONENTCATEGORIES` WHERE (`code` = ?) AND (`private` = 1)'))
	            {
	                if ($stmt->bind_param('s', $componentCategoryCodeList[$i]))
	                {
	                    if ($stmt->bind_result($recordID))
	                    {
	                       if ($stmt->execute())
	                       {
	                            if ($stmt->fetch())
	                            {
	                                $canDelete = false;
	                                $allDeleted = 0;
	                            }
	                            else
	                            {
	                            	$canDelete = true;
	                            	$item['id'] = $componentCategoryIDList[$i];
	                            	$item['code'] = $componentCategoryCodeList[$i];
									array_push($categoriesNotUsed, $item);
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
	                if ($stmt = $dbObj->prepare('SELECT `id` FROM `COMPONENTS` WHERE (`categorycode` = ?)'))
	                {
	                    if ($stmt->bind_param('s', $componentCategoryCodeList[$i]))
	                    {
	                        if ($stmt->bind_result($recordID))
	                        {
	                           if ($stmt->execute())
	                           {
	                                if ($stmt->fetch())
	                                {
	                                    $result = 'str_ErrorComponentsAttached';
	                                    $canDelete = false;
	                                    $allDeleted = 0;
	                                }
	                                else
	                            	{
	                            		$canDelete = true;
	                            		$item['id'] = $componentCategoryIDList[$i];
	                            		$item['code'] = $componentCategoryCodeList[$i];
										array_push($categoriesNotUsed, $item);
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
	                if ($stmt = $dbObj->prepare('SELECT `id` FROM `ORDERITEMCOMPONENTS` WHERE `componentcategorycode` = ?'))
	                {
	                    if ($stmt->bind_param('s', $componentCategoryCodeList[$i]))
	                    {
	                        if ($stmt->bind_result($recordID))
	                        {
	                           if ($stmt->execute())
	                           {
	                                if ($stmt->fetch())
	                                {
	                                    $result = 'str_ErrorCategoryUsedInOrder';
	                                    $canDelete = false;
	                                    $allDeleted = 0;
	                                }
	                                else
	                            	{
	                            		$canDelete = true;
	                            		$item['id'] = $componentCategoryIDList[$i];
	                            		$item['code'] = $componentCategoryCodeList[$i];
										array_push($categoriesNotUsed, $item);
	                            	}
	                           }
	                        }
	                    }
	                    $stmt->free_result();
	                    $stmt->close();
	                    $stmt = null;
	                }
	            }

	            // check to see if there any price records using this category
	            if ($canDelete)
	            {
	                if ($stmt = $dbObj->prepare('SELECT `id` FROM `PRICES` WHERE `categorycode` = ? AND `ispricelist` = 1'))
	                {
	                    if ($stmt->bind_param('s', $componentCategoryCodeList[$i]))
	                    {
	                        if ($stmt->bind_result($recordID))
	                        {
	                           if ($stmt->execute())
	                           {
	                                if ($stmt->fetch())
	                                {
	                                    $result = 'str_ErrorCategoryHasPriceList';
	                                    $canDelete = false;
	                                    $allDeleted = 0;
	                                }
	                                else
	                            	{
	                            		$canDelete = true;
	                            		$item['id'] = $componentCategoryIDList[$i];
	                            		$item['code'] = $componentCategoryCodeList[$i];
										array_push($categoriesNotUsed, $item);
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
	                if ($stmt = $dbObj->prepare('UPDATE `COMPONENTCATEGORIES` SET `deleted` = 1 WHERE `id` = ?'))
	                {
	                    if ($stmt->bind_param('i', $componentCategoryIDList[$i]))
	                    {
	                        if ($stmt->execute())
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                'ADMIN', 'COMPONENTCATEGORY-DELETE', $componentCategoryIDList[$i] . ' ' .  $componentCategoryCodeList[$i], 1);
	                                array_push($categoriesDeleted, $componentCategoryIDList[$i]);

	                            if ($componentCategoryDisplayTypes[$i] == 1)
	                            {
		                            if ($stmt2 = $dbObj->prepare('UPDATE `SECTIONS` SET `deleted` = 1 WHERE `code` = ?'))
					                {
					                    if ($stmt2->bind_param('s', $componentCategoryCodeList[$i]))
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
	                    $stmt->free_result();
	                    $stmt->close();
	                	$stmt = null;
	                }
	            }
        	}
            $dbObj->close();
        }

        $resultArray['alldeleted'] = $allDeleted;
        $resultArray['categoryids'] = $categoriesDeleted;
        $resultArray['result'] = $result;

        return $resultArray;
    }

    static function componentCategoriesActivate()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
		$resultParam = '';

        $ids = $_POST['ids'];
        $codes = $_POST['codelist'];
        $codeList = explode(',', $codes);

        $idList = explode(',',$ids);
        $active = $_POST['active'];
        if ($active != '0') $active = 1;

        $itemCount = count($idList);

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $dbObj->query('START TRANSACTION');

            if ($stmt = $dbObj->prepare('UPDATE `COMPONENTCATEGORIES` SET `active` = ? WHERE `id` = ?'))
            {
                for($i = 0; $i < $itemCount; $i++)
        		{
	                if ($stmt->bind_param('ii', $active, $idList[$i]))
	                {
	                    if ($stmt->execute())
	                    {
	                        if ($active == 1)
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                    'ADMIN', 'COMPONENTCATEGORIES-DEACTIVATE', $idList[$i] . ' ' . $codeList[$i], 1);
	                        }
	                        else
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                    'ADMIN', 'COMPONENTCATEGORIES-ACTIVATE', $idList[$i] . ' ' . $codeList[$i], 1);
	                        }
	                    }
	                    else
						{
							// could not bind parameters
							$result = 'str_DatabaseError';
							$resultParam = 'componentCategoriesActivate execute ' . $dbObj->error;
						}

	                    $resultArray[$i]['recordid'] = $idList[$i];
	                    $resultArray[$i]['isactive'] = $active;
	                }
	                else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = 'componentCategoriesActivate bind ' . $dbObj->error;
					}
            	}
                $stmt->free_result();
	            $stmt->close();
	            $stmt = null;
            }
            else
			{
				$result = 'str_DatabaseError';
				$resultParam = 'componentCategoriesActivate prepare ' . $dbObj->error;
			}

			if ($result == '')
			{
				if ($stmt = $dbObj->prepare('UPDATE `SECTIONS` SET `active` = ? WHERE `code` = ?'))
				{
					for($i = 0; $i < $itemCount; $i++)
					{
						if ($stmt->bind_param('is', $active, $codeList[$i]))
						{
							if ($stmt->execute())
							{
								if ($active == 1)
								{
									DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
											'ADMIN', 'SECTION-DEACTIVATE', $codeList[$i], 1);
								}
								else
								{
									DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
											'ADMIN', 'SECTION-ACTIVATE', $codeList[$i], 1);
								}
							}
							else
							{
								// could not bind parameters
								$result = 'str_DatabaseError';
								$resultParam = 'componentCategoriesActivate section execute ' . $dbObj->error;
							}
						}
						else
						{
							// could not bind parameters
							$result = 'str_DatabaseError';
							$resultParam = 'componentCategoriesActivate section bind ' . $dbObj->error;
						}
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'componentCategoriesActivate section prepare ' . $dbObj->error;
				}
			}

			if ($result == '')
			{
				$dbObj->query('COMMIT');
			}

            $dbObj->close();
        }

        return $resultArray;
    }
}
?>
