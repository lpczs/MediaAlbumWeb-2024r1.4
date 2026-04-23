<?php

class AdminVouchersPromotion_model
{
	/**
	 * Echos the list of voucher promotions depending on the company of logged-in user.
	 *
	 * @param  Description of method
	 * @global $gConstants, $gSession
	 * @return Echos values back in javascript array format
	 *
	 * @since Version 3.0.0
	 * @author Dasha Salo
	 * @version 3.0.0
	 */
	static function listPromotions()
	{
		$resultArray = Array();

	    global $gConstants, $gSession;

	    $companyCode = '';
	    $totalCount = 0;

	    $dbObj = DatabaseObj::getGlobalDBConnection();

        $start = (integer)$_POST['start'];
        $limit = (integer)$_POST['limit'];
        $sortBy = (isset($_POST['sort'])) ? $_POST['sort'] : '';
        $sortDir = (isset($_POST['dir'])) ? $_POST['dir'] : '';
        $searchFields = UtilsObj::getPOSTParam('fields');

        //init cache for localization
        LocalizationObj::formatLocaleDateTime('0000-00-00 00:00:00');

        /* building a statement to bind variable number of parameters */
        $typesArray = array();
		$paramArray = array();
		$stmtArray = array();

		if ($searchFields != '')
		{
			$searchQuery = $_POST['query'];
			$selectedfields = explode(',', str_replace('"', "", str_replace("]", "", str_replace("[", "",$_POST['fields']))));

			if ($searchQuery != '')
			{
				foreach ($selectedfields as $value)
				{
					switch ($value)
    				{
    					case 'promoCode':    $value = 'code'; break;
    					case 'promoName':    $value = 'name'; break;
    				}
					$stmtArray[] = '(`'.$value.'` LIKE ?)';
					$paramArray[] = '%'.$searchQuery.'%';
					$typesArray[] = 's';
				}
			}
		}

		/* sorting */
		$customSort = 'code ASC';
    	if ($sortBy != '')
    	{
    		switch ($sortBy)
    		{
    			case 'promoCode': $sortBy = 'code '.$sortDir; break;
    			case 'promoName': $sortBy = 'name '.$sortDir; break;
    			case 'startDate': $sortBy = 'startdate '.$sortDir; break;
    			case 'endDate':   $sortBy = 'enddate '.$sortDir; break;
    			case 'promoVoucherCount': $sortBy = 'vouchercount '.$sortDir;	break;
    			case 'isActive': $sortBy = 'active '.$sortDir; break;
    		}
    		$customSort = ', '. $sortBy;
    	}

		if ($dbObj)
        {
	    	$stmtArray = join(' OR ', $stmtArray);

	    	if (($gConstants['optionms']) && ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN))
			{
           		$companyCode = $gSession['userdata']['companycode'];
           		$stmt = $dbObj->prepare('SELECT SQL_CALC_FOUND_ROWS `id`, `companycode`, `code`, `name`, `startdate`, `enddate`, `active`, (SELECT count(*) FROM `VOUCHERS` WHERE `promotioncode` = VOUCHERPROMOTIONS.code) as vouchercount FROM `VOUCHERPROMOTIONS` WHERE `companycode` = ? OR `companycode` = "" '.(($stmtArray != '') ? 'AND ('.$stmtArray.')' : '' ).' ORDER BY companycode'.$customSort. ' LIMIT ' . $limit . ' OFFSET ' . $start);

				array_unshift($typesArray, 's');
				array_unshift($paramArray, $gSession['userdata']['companycode']);
			}
			else
			{
				$stmt = $dbObj->prepare('SELECT SQL_CALC_FOUND_ROWS `id`, `companycode`, `code`, `name`, `startdate`, `enddate`, `active`, (SELECT count(*) FROM `VOUCHERS` WHERE `promotioncode` = VOUCHERPROMOTIONS.code) as vouchercount FROM `VOUCHERPROMOTIONS` '.(($stmtArray != '') ? 'WHERE '.$stmtArray : '' ).' ORDER BY companycode'.$customSort. ' LIMIT ' . $limit . ' OFFSET ' . $start);
				$bindOK = true;
			}

	    	if ($stmt)
			{
				// otherwise gives and error when using formatLocaleDateTime function
				$stmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);
				$bindOK = DatabaseObj::bindParams($stmt, $typesArray, $paramArray);

            	if ($bindOK)
            	{
	                if ($stmt->bind_result($id, $promotionCompanyCode, $code, $name, $startDate, $endDate, $isActive, $voucherCount))
	                {
	                    if ($stmt->execute())
	                    {
	                        while ($stmt->fetch())
	                        {
	                            $date1 = strtotime($startDate);
    							$date2 = strtotime('2000-01-01');
  		    					if ($date1 < $date2) {
		      						$startDate = '2000-01-01';
		    					}
	        					if ($endDate == '1970-01-01 01:00:00')
						        {
						        	$endDate = '2000-01-01';
						        }
	        			        $startDate = LocalizationObj::formatLocaleDateTime($startDate);
					            $endDate = LocalizationObj::formatLocaleDateTime($endDate);

	                            $userItem['id'] = "'" . UtilsObj::ExtJSEscape($id) . "'";
	                            $userItem['companycode'] = "'" . UtilsObj::ExtJSEscape($promotionCompanyCode) . "'";
	                            $userItem['code'] = "'" . UtilsObj::ExtJSEscape($code) . "'";
	                            $userItem['name'] = "'" . UtilsObj::ExtJSEscape($name) . "'";
	                            $userItem['startdate'] = "'" . UtilsObj::ExtJSEscape($startDate) . "'";
	                            $userItem['enddate'] = "'" . UtilsObj::ExtJSEscape($endDate) . "'";
	                            $userItem['vouchercount'] = "'" . UtilsObj::ExtJSEscape($voucherCount) . "'";
	                            $userItem['active'] = "'" . UtilsObj::ExtJSEscape($isActive) . "'";
								array_push($resultArray, '['.join(',', $userItem).']');
	                        }
	                    }
	                }
	                if (($stmt = $dbObj->prepare("SELECT FOUND_ROWS()")) && ($stmt->bind_result($totalCount)))
					{
						if ($stmt->execute())
						{
							$stmt->fetch();
						}
					}
					$stmt->free_result();
					$stmt->close();
				}
			}
            $dbObj->close();
        }

        $summaryArray = join(',', $resultArray);
        if ($summaryArray != '')
        {
        	$summaryArray = ', ' . $summaryArray;
        }

        echo '[['.$totalCount.']'.$summaryArray.']';
        return;
	}

	/**
	 * Make voucher promotion active/inactive
	 *
	 * @param $_POST['idlist'] List of promotion ids
	 * @param $_POST['active'] Active or inactive (1/0)
	 * @return JSON object with success or failure details
	 *
	 * @since Version 1.0.0
	 * @author Kevin Gale
	 * @author Dasha Salo, 14 Jan 2011
	 * @version 3.0.0
	 */
    static function promotionActivate()
    {
        global $gSession;

        $promoList  = explode(',',$_POST['idlist']);
        $promoCount = count($promoList);
        $result = '';
		$resultParam = '';
		$recordID = 0;
		$isActive = $_POST['active'];

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
        	for ($i = 0; $i < $promoCount; $i++)
			{
				$id = $promoList[$i];
				$promotionDataArray = DatabaseObj::getVoucherPromotionFromID($id);

				if ($stmt = $dbObj->prepare('UPDATE `VOUCHERPROMOTIONS` SET `active` = ? WHERE `id` = ?'))
	            {
	                if ($stmt->bind_param('ii', $isActive, $id))
	                {
	                    if ($stmt->execute())
	                    {
	                        if ($promotionDataArray['isactive'] == 1)
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                    'ADMIN', 'VOUCHERPROMOTION-DEACTIVATE', $id . ' ' . $promotionDataArray['code'], 1);
	                        }
	                        else
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                    'ADMIN', 'VOUCHERPROMOTION-ACTIVATE', $id . ' ' . $promotionDataArray['code'], 1);
	                        }
	                    }
	                    else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'voucherPromotionActivate execute ' . $dbObj->error;
						}
	                }
	                else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'voucherPromotionActivate bind ' . $dbObj->error;
					}
	                $stmt->free_result();
	                $stmt->close();
	            }
	            else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'voucherPromotionActivate prepare ' . $dbObj->error;
				}
			}
        	$dbObj->close();
        }
        else
		{
			$result = 'str_DatabaseError';
			$resultParam = 'voucherPromotionActivate connect ' . $dbObj->error;
		}

		if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }
		return;
	}


    static function displayAdd()
	{
	    return DatabaseObj::getEmptyVoucherPromotion();
    }

    static function displayEdit($pID)
	{
		return DatabaseObj::getVoucherPromotionFromID($pID);
    }


	static function promotionAdd()
	{
        global $gSession;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $recordID = 0;

        $code = strtoupper($_POST['code']);
        $name = $_POST['name'];
        $startDate = $_POST['startdateformat'];
        $endDate = $_POST['enddateformat'];
        $isActive = $_POST['isactive'];
        $companyCode = isset($_POST['companylist']) ? (($_POST['companylist'] == 'GLOBAL') ? '' : $_POST['companylist']) : '';

        if (($gConstants['optionms']) && ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN))
		{
        	$companyCode = $gSession['userdata']['companycode'];
		}

        if (($code != '') && ($name != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('INSERT INTO `VOUCHERPROMOTIONS` (`id`, `datecreated`, `companycode`, `code`, `name`, `startdate`, `enddate`, `active`)
                    VALUES (0, now(), ?, ?, ?, ?, ?, ?)'))
                {
                    if ($stmt->bind_param('sssssi', $companyCode, $code, $name, $startDate, $endDate, $isActive))
                    {
                        if ($stmt->execute())
                        {
                            $recordID = $dbObj->insert_id;

                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                    'ADMIN', 'VOUCHERPROMOTION-ADD', $recordID . ' ' . $code, 1);
                        }
                        else
                        {
                            // could not execute statement

                            // first check for a duplicate key (voucher code)
                            if ($stmt->errno == 1062)
                            {
                            	$result = 'str_ErrorPromotionExists';
                            }
                            else
                            {
                            	$result = 'str_DatabaseError';
                            	$resultParam = 'promotionAdd execute ' . $dbObj->error;
                            }
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'promotionAdd bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'promotionAdd prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'promotionAdd connect ' . $dbObj->error;
            }
        }

        /*$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['recordid'] = $recordID;
        $resultArray['code'] = $code;
        $resultArray['name'] = $name;
        $resultArray['startdate'] = $startDate;
        $resultArray['enddate'] = $endDate;
        $resultArray['isactive'] = $isActive;
        return $resultArray;*/

        if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			$smarty = SmartyObj::newSmarty('AdminVouchers');
            echo '{"success":false,	"msg":"' . str_replace('^0', $resultParam, $smarty->get_config_vars($result)). '"}';
		}
		return;
    }


    static function promotionEdit()
    {
        global $gSession, $gConstants;

        $result = '';
        $resultParam = '';

        $id = $_POST['id'];
        $code = $_POST['code'];
        $name = $_POST['name'];
        $startDate = $_POST['startdateformat'];
        $endDate = $_POST['enddateformat'];
        $isActive = $_POST['isactive'];
        $companyCode = '';

        if (($id > 0) && ($code != '') && ($name != ''))
        {
        	$promotionDataArray = DatabaseObj::getVoucherPromotionFromID($id);

            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if (($gConstants['optionms']) && ($gSession['userdata']['usertype'] == TPX_LOGIN_SYSTEM_ADMIN))
				{
           			$companyCode = isset($_POST['companylist']) ? (($_POST['companylist'] == 'GLOBAL') ? '' : $_POST['companylist']) : '';
           			$stmt = $dbObj->prepare('UPDATE `VOUCHERPROMOTIONS` SET `companycode` = ?, `name` = ?, `startdate` = ?, `enddate` = ?, `active` = ? WHERE `id` = ?');
					$bindOk = $stmt->bind_param('ssssii', $companyCode, $name, $startDate, $endDate, $isActive, $id);
				}
				else
				{
					$stmt = $dbObj->prepare('UPDATE `VOUCHERPROMOTIONS` SET `name` = ?, `startdate` = ?, `enddate` = ?, `active` = ? WHERE `id` = ?');
					$bindOk = $stmt->bind_param('sssii', $name, $startDate, $endDate, $isActive, $id);
				}

                if ($bindOk)
                {
                	if ($stmt->execute())
                    {
                    	// we have updated the promotion now update its vouchers
						$stmt->free_result();
						$stmt->close();

						if ($stmt = $dbObj->prepare('UPDATE `VOUCHERS` SET `startdate` = ?, `enddate` = ? WHERE `promotioncode` = ?'))
						{
							if ($stmt->bind_param('sss', $startDate, $endDate, $promotionDataArray['code']))
							{
								if ($stmt->execute())
								{
									DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                   		'ADMIN', 'VOUCHERPROMOTION-UPDATE', $id . ' ' . $promotionDataArray['code'], 1);
								}
							}
						}
            		}
                    else
                    {
                    	// first check for a duplicate key (promotion code)
                        if ($stmt->errno == 1062)
                        {
                        	$result = 'str_ErrorPromotionExists';
                        }
                        else
                        {
                        	$result = 'str_DatabaseError';
                            $resultParam = 'promotionEdit execute ' . $dbObj->error;
                        }
                    }
            	}
                else
                {
                	// could not bind parameters
                    $result = 'str_DatabaseError';
                    $resultParam = 'promotionEdit bind ' . $dbObj->error;
                }
                $stmt->free_result();
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'promotionEdit connect ' . $dbObj->error;
            }
        }

        if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }
		return;

        /*$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['recordid'] = $id;
        $resultArray['code'] = $code;
        $resultArray['name'] = $name;
        $resultArray['startdate'] = $startDate;
        $resultArray['enddate'] = $endDate;
        $resultArray['isactive'] = $isActive;


        return $resultArray;*/
	}


    /**
	 * Deletes specified promotion and its vouchers.
	 *
	 * @param  Description of method
	 * @global bool Used to control the weather
	 * @return Description of the return value
	 *
	 * @since Version 3.0.0
	 * @author Dasha Salo
	 * @version 3.0.0
	 */
    static function promotionDelete()
    {
        global $ac_config;
        global $gSession;

        $promoList  = explode(',',$_POST['idlist']);
        $promoCount = count($promoList);
        $result = '';
		$resultParam = '';

        $filename = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
        	for ($i = 0; $i < $promoCount; $i++)
        	{
        		$promotionID = $promoList[$i];
        		$promotionDataArray = DatabaseObj::getVoucherPromotionFromID($promotionID);
	        	if ($promotionDataArray['recordid'] > 0)
	        	{
					if ($stmt = $dbObj->prepare('DELETE FROM `VOUCHERPROMOTIONS` WHERE `id` = ?'))
					{
						if ($stmt->bind_param('i', $promotionID))
						{
							if ($stmt->execute())
							{
								// we have deleted the promotion now delete its vouchers
								$stmt->free_result();
								$stmt->close();

								if ($stmt = $dbObj->prepare('DELETE `VOUCHERS`, `pgl`
									FROM `VOUCHERS`
									LEFT JOIN `productgrouplink` AS `pgl` ON (`pgl`.`assigneetype` = ' . TPX_PRODUCTGROUP_ASSIGNEETYPE_VOUCHER . ' AND `pgl`.`assigneecode` = `VOUCHERs`.`code`)
									WHERE `promotioncode` = ?'))
								{
									if ($stmt->bind_param('s', $promotionDataArray['code']))
									{
										if ($stmt->execute())
										{
											DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
												'ADMIN', 'VOUCHERPROMOTION-DELETE', $promotionID . ' ' . $promotionDataArray['code'], 1);
										}
										else
									   	{
									   		$result = 'str_DatabaseError';
											$resultParam = 'voucherPromotionDelete execute2 ' . $dbObj->error;
									   	}
									}
									else
									{
										$result = 'str_DatabaseError';
										$resultParam = 'voucherPromotionDelete bind2 ' . $dbObj->error;
									}
								}
								else
								{
									$result = 'str_DatabaseError';
									$resultParam = 'voucherPromotionDelete prepare2 ' . $dbObj->error;
								}
							}
							else
						   	{
						   		$result = 'str_DatabaseError';
								$resultParam = 'voucherPromotionDelete execute ' . $dbObj->error;
						   	}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'voucherPromotionDelete bind ' . $dbObj->error;
						}
						$stmt->free_result();
						$stmt->close();
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'voucherPromotionDelete prepare ' . $dbObj->error;
					}
				}
	    	}
        	$dbObj->close();
		}
        else
		{
			$result = 'str_DatabaseError';
			$resultParam = 'voucherPromotionDelete connect ' . $dbObj->error;
		}

        if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }
		return;
    }
}
?>
