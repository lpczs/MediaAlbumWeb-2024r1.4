<?php

require_once('../Welcome/Welcome_model.php');

class Admin_model
{
    static function logout()
	{
		Welcome_model::processLogout(TPX_USER_LOGOUT_REASON_USER_LOGOUT);

		$_POST['ref'] = 0;
	    $_GET['ref'] = 0;
	}

	static function priceListAdd()
	{
		global $gSession;
		global $gConstants;

        $result = '';
        $resultParam = '';
		$resultArray = Array();

		$companyCode = '';

		if ($gConstants['optionms'])
        {
			switch ($gSession['userdata']['usertype'])
			{
				case TPX_LOGIN_SYSTEM_ADMIN:
					$companyCode = $_POST['pricelistcompanycode'];

					if ($companyCode == 'GLOBAL')
					{
						$companyCode = '';
					}
				break;
				case TPX_LOGIN_COMPANY_ADMIN:
					$companyCode = $gSession['userdata']['companycode'];
				break;
			}
		}

		$pricesListID = 0;
		$categoryCode = $_POST['categorycode'];
		$linkedPriceListID = 0;
		$pricingModel = $_POST['pricingmodel'];
		$price = $_POST['price'];
		$priceListLocalCode = strtoupper($_POST['pricelistcode']);
		$taxCode = $_POST['taxcode'];

		if($companyCode != '')
		{
			$priceListCode = $companyCode.".".$priceListLocalCode;
		}
		else
		{
			$priceListCode = $priceListLocalCode;
		}

		$priceListName = $_POST['pricelistname'];
		$isPriceList = 1;
		$isActive = 1;

		if (($categoryCode == 'PRODUCT') || ($pricingModel == TPX_PRICINGMODEL_PERPRODCMPQTY) || ($pricingModel == TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY))
		{
			$quantityDisplayType = $_POST['quantitytypeisdropdown'];
		}
		else
		{
			$quantityDisplayType = 0;
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
	                    $pricesListID = $dbObj->insert_id;
						DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
						                            'ADMIN', 'PRICELIST-ADD', $pricesListID . ' ' . $categoryCode, 1);
	                }
	                else
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
	                $resultParam = 'priceListAdd bind ' . $dbObj->error;
	            }
				$stmt->free_result();
	            $stmt->close();
	            $stmt = null;
	        }
	        else
	        {
	            // could not prepare statement
	            $result = 'str_DatabaseError';
	            $resultParam = 'priceLisstAdd prepare ' . $dbObj->error;
	        }
	    }

	    $resultArray['result'] = $result;
	    $resultArray['id'] = $pricesListID;
	    $resultArray['price'] = $price;
	    $resultArray['pricelistcode'] = $priceListCode;
	    $resultArray['pricelistname'] = $priceListName;

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
		$quantityisdropdown = 0;
		$categorycode = '';
		$priceListCode = '';
		$priceListLocalCode = '';
		$priceListName = '';
		$taxCode = '';
		$active = 0;

    	$priceListID = $_GET['pricelistid'];

    	$dbObj = DatabaseObj::getGlobalDBConnection();

	    if ($dbObj)
	    {
	        if ($stmt = $dbObj->prepare('SELECT `companycode`,`pricingmodel`, `price`, `quantityisdropdown`, `categorycode`, `pricelistcode`, `pricelistlocalcode`, `pricelistname`, `taxcode`, `active` FROM `PRICES` WHERE `id` = ?'))
	        {
                if ($stmt->bind_param('i', $priceListID))
                {
                    if ($stmt->execute())
                    {
						if ($stmt->store_result())
						{
                            if ($stmt->num_rows > 0)
                            {
								if ($stmt->bind_result($companyCode, $pricingModel, $price, $quantityisdropdown, $categorycode, $priceListCode, $priceListLocalCode, $priceListName, $taxCode, $active))
								{
									if(!$stmt->fetch())
									{
										$error = __FUNCTION__ . ' fetch ' . $dbObj->error;
									}
								}
								else
								{
									$error = __FUNCTION__ . ' bind_result ' . $dbObj->error;
								}
							}
						}
						else
						{
							$error = __FUNCTION__ . ' store_result ' . $dbObj->error;
						}
                    }
                    else
                    {
                		$error = __FUNCTION__ . ' execute ' . $dbObj->error;
                    }
                }
                else
                {
                	$error = __FUNCTION__ . ' bind params ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
	        }
	        else
	        {
	        	$error = __FUNCTION__ . ' prepare ' . $dbObj->error;
	        }

            $dbObj->close();
        }

        $resultArray['id'] = $priceListID;
        $resultArray['companycode'] = $companyCode;
	    $resultArray['pricingmodel'] = $pricingModel;
	    $resultArray['price'] = $price;
	    $resultArray['quantityisdropdown'] = $quantityisdropdown;
	    $resultArray['categorycode'] = $categorycode;
	    $resultArray['pricelistcode'] = $priceListCode;
	    $resultArray['pricelistlocalcode'] = $priceListLocalCode;
	    $resultArray['pricelistname'] = $priceListName;
	    $resultArray['taxcode'] = $taxCode;
	    $resultArray['active'] = $active;
	    $resultArray['decimalplaces'] = $_GET['decimalplaces'];

  		return $resultArray;
    }

    static function priceListEdit()
    {

    	global $gSession;
		global $gConstants;

        $result = '';
        $resultParam = '';
        $priceListID = $_GET['id'];
		$linkedPriceListID = 0;
		$priceListCode = strtoupper($_POST['adminpricelistcode']);
		$priceListName = html_entity_decode($_POST['adminpricelistname'], ENT_QUOTES);
		$categoryCode = $_POST['categorycodelist'];
		$isPriceList = 1;
		$price = $_POST['pricelisteditprice'];
		$isActive = $_POST['isactive'];
		$pricingModel = $_POST['pricingmodel'];
		$taxCode = $_POST['taxcodepricelist'];

		if (($categoryCode == 'PRODUCT') || ($pricingModel == TPX_PRICINGMODEL_PERPRODCMPQTY) || ($pricingModel == TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY))
		{
			$quantityDisplayType = $_POST['quantitytypeisdropdown'];
		}
		else
		{
			$quantityDisplayType = 0;
		}

        $companyCode = '';

		if ($gConstants['optionms'])
        {
			switch ($gSession['userdata']['usertype'])
			{
				case TPX_LOGIN_SYSTEM_ADMIN:
					$companyCode = $_POST['pricelistcompany'];

					if ($companyCode == 'GLOBAL')
					{
						$companyCode = '';
					}
				break;
				case TPX_LOGIN_COMPANY_ADMIN:
					$companyCode = $gSession['userdata']['companycode'];
				break;
			}
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

	static function adminPriceListDelete()
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

	static function ExtJsSearchCustomers()
	{

		$total = 0;
		$bindOK = true;

		$resultArray = array();
		$resultArray['data'] = array();
		$resultArray['totalcount'] = 0;
		$resultArray['result'] = '';
		$resultArray['resultparam'] = '';
		$query = '';
		$sqlstring = '';
		$sqlstringcount = 'SELECT SQL_NO_CACHE COUNT(*) FROM USERS WHERE `customer`=1';

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$typeList = "";

			if (isset($_POST['query']))
			{
				$query = $_POST['query'];
			}

			if ($query != '')
			{
				$query = '%' . $query . '%';
			}

			$sqlstring = "SELECT `id`,`login`, `contactfirstname`,`contactlastname`,`emailaddress`, `address1`, `postcode`, `telephonenumber` FROM `users` WHERE `customer`=1";

			if (isset($_POST['id']))
			{
				$typeList .= "i";
				$sqlstring .= " AND `id`=? ";
				$sqlstringcount .= " AND `id`=? ";
			}
			elseif ($query != '')
			{
				$typeList .= "sss";
				$sqlstring .= " AND (`contactfirstname` LIKE ? OR `contactlastname` LIKE ? OR `emailaddress` LIKE ?)";
				$sqlstringcount .=" AND (`contactfirstname` LIKE ? OR `contactlastname` LIKE ? OR `emailaddress` LIKE ?)";
			}

			$typeList .= "s";

			if (isset($_POST['group']) && strlen($_POST['group']) > 0)
			{
				$sqlstring .= " AND `groupcode` = ? ";
				$sqlstringcount .=" AND `groupcode` = ? ";
			}
			else
			{
				$sqlstring .=" AND `groupcode` IN (SELECT `groupcode` FROM LICENSEKEYS WHERE (`companycode`=? OR `companycode`='' OR `companycode` IS NULL))";
				$sqlstringcount .=" AND `groupcode` IN (SELECT `groupcode` FROM LICENSEKEYS WHERE (`companycode`=? OR `companycode`='' OR `companycode` IS NULL))";
			}

			// Sort results by firstname, lastname
			$sqlstring .= ' ORDER BY `contactfirstname`, `contactlastname`';

			if (isset($_POST['limit']) && isset($_POST['start']))
			{
				$sqlstring .= " LIMIT " . $_POST['limit'] . " OFFSET " . $_POST['start'];
			}

			$paramList = array();

			if ($stmt = $dbObj->prepare($sqlstring))
			{
				$bindOK = true;

				if (isset($_POST['group']) && strlen($_POST['group']) > 0)
				{

					if (isset($_POST['id']))
					{
						$bindOK = $stmt->bind_param($typeList, $_POST['id'], $_POST['group']);
					}
					elseif ($query != '')
					{
						$bindOK = $stmt->bind_param($typeList, $query, $query, $query, $_POST['group']);
					}
					else
					{
						$bindOK = $stmt->bind_param($typeList, $_POST['group']);
					}
				}
				else
				{
					if (isset($_POST['id']))
					{
						$bindOK = $stmt->bind_param($typeList, $_POST['id'], $gSession['userdata']['companycode']);
					}
					elseif ($query != '')
					{
						$bindOK = $stmt->bind_param($typeList, $query, $query, $query, $gSession['userdata']['companycode']);
					}
					else
					{
						$bindOK = $stmt->bind_param($typeList, $gSession['userdata']['companycode']);
					}
				}

				if ($bindOK)
				{
					if ($stmt->execute())
					{
						if ($stmt->bind_result($id, $login, $firstname, $lastname, $emailaddress, $address1, $postcode, $telephonenumber))
						{
							$i = 0;
							while ($stmt->fetch())
							{
								$resultArray['data'][$i]['id'] = $id;
								$resultArray['data'][$i]['login'] = $login;
								$resultArray['data'][$i]['firstname'] = $firstname;
								$resultArray['data'][$i]['lastname'] = $lastname;
								$resultArray['data'][$i]['emailaddress'] = $emailaddress;
								$resultArray['data'][$i]['address1'] = $address1;
								$resultArray['data'][$i]['postcode'] = $postcode;
								$resultArray['data'][$i]['telephonenumber'] = $telephonenumber;

								$i++;
							}
						}
						else
						{
							$resultArray['result'] = 'str_DatabaseError';
							$resultArray['resultparam'] = 'ExtJsSearchCustomers bind_result ' . $dbObj->error;
						}

						if ($stmt = $dbObj->prepare($sqlstringcount))
						{
							$bindOK = true;

							if (isset($_POST['group']) && strlen($_POST['group']) > 0)
							{

								if (isset($_POST['id']))
								{
									$bindOK = $stmt->bind_param($typeList, $_POST['id'], $_POST['group']);
								}
								elseif ($query != '')
								{
									$bindOK = $stmt->bind_param($typeList, $query, $query, $query, $_POST['group']);
								}
								else
								{
									$bindOK = $stmt->bind_param($typeList, $_POST['group']);
								}
							}
							else
							{
								if (isset($_POST['id']))
								{
									$bindOK = $stmt->bind_param($typeList, $_POST['id'], $gSession['userdata']['companycode']);
								}
								elseif ($query != '')
								{
									$bindOK = $stmt->bind_param($typeList, $query, $query, $query, $gSession['userdata']['companycode']);
								}
								else
								{
									$bindOK = $stmt->bind_param($typeList, $gSession['userdata']['companycode']);
								}
							}

							if ($bindOK)
							{
								if (($stmt->bind_result($total)))
								{
									if ($stmt->execute())
									{
										$stmt->fetch();
									}
									else
									{
										$resultArray['result'] = 'str_DatabaseError';
										$resultArray['resultparam'] = 'ExtJsSearchCustomers count execute ' . $dbObj->error;
									}
								}
								else
								{
									$resultArray['result'] = 'str_DatabaseError';
									$resultArray['resultparam'] = 'ExtJsSearchCustomers count bind_result ' . $dbObj->error;
								}
							}
							else
							{
								$resultArray['result'] = 'str_DatabaseError';
								$resultArray['resultparam'] = 'ExtJsSearchCustomers count bind_param ' . $dbObj->error;
							}
						}
					}
					else
					{
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = 'ExtJsSearchCustomers execute ' . $dbObj->error;
					}
				}
				else
				{
					$resultArray['result'] = 'str_DatabaseError';
					$resultArray['resultparam'] = 'ExtJsSearchCustomers bind_param ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
			}
			else
			{
				$resultArray['result'] = 'str_DatabaseError';
				$resultArray['resultparam'] = 'ExtJsSearchCustomers prepare ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			$resultArray['result'] = 'str_DatabaseError';
			$resultArray['resultparam'] = 'ExtJsSearchCustomers connect ' . $dbObj->error;
		}

		$resultArray['totalcount'] = $total;

		return $resultArray;
	}

	/**
	 * Reauthenticate the currently signed in user.
	 *
	 * @global array $gSession
	 * @param string $pReason What the authentication is for.
	 * @param int $pSessionRef Ref of the current main session.
	 * @param string $pPassword Password of the user trying to authenticate in md5 format on HTTP or plaintext on HTTPS.
	 * @param int $pPasswordFormat Format of the password passed via parameter.
	 * @return array
	 */
	static function reauthenticate($pReason, $pSessionRef, $pPassword, $pPasswordFormat)
	{
		global $gSession;

		$returnArray = UtilsObj::getReturnArray();
		$success = false;
		$userLogin = UtilsObj::getArrayParam($gSession, 'userlogin', '');
		$brandCode = UtilsObj::getArrayParam($gSession, 'webbrandcode', '');
		$groupCode = UtilsObj::getArrayParam($gSession, 'groupcode', '');

		// (Re)authenticate the user without creating a new session.
		$authenticateLoginResult = AuthenticateObj::authenticateLogin(TPX_USER_AUTH_REASON_SYSTEMUSER_REAUTHENTICATE, 0, false, UtilsObj::getBrowserLocale(),
				$brandCode, $groupCode, '', $userLogin, $pPasswordFormat, $pPassword, false, false, false, '', [], [], 0);

		if ($authenticateLoginResult['result'] != '')
		{
			$returnArray['error'] = 'str_ErrorUnableToAuthenticate';
		}
		else
		{
			$success = true;
			$returnArray['data'] = $authenticateLoginResult;
		}

		// Record the attempt in the activity log.
		DatabaseObj::updateActivityLog($pSessionRef, 0, $gSession['userid'], $userLogin, $gSession['username'], 0, 'ADMIN', 'REAUTHENTICATE', $pReason, $success);

		return $returnArray;
	}

	/**
	 * Unlocks a user account reseting the nextvalidlogindate to the current datetime and loginattemptcount back to 0.
	 *
	 * @param int $pUserID ID of the user to unlock.
	 * @return array Returns errors if any, else it was a successful unlock.
	 */
	static function unlockAccount($pUserID)
	{
		$returnArray = UtilsObj::getReturnArray();
		$blockReason = TPX_BLOCK_REASON_NONE;

		$dbObj = DatabaseObj::getConnection();

		if ($dbObj)
		{
			$sql = 'UPDATE `USERS`
				SET
					`nextvalidlogindate` = UTC_TIMESTAMP(), `loginattemptcount` = 0, `blockreason` = ?
				WHERE
					`id` = ?';

			if (($stmt = $dbObj->prepare($sql)))
			{
				if ($stmt->bind_param('ii', $blockReason, $pUserID))
				{
					if (! $stmt->execute())
					{
						$returnArray['error'] = 'str_DatabaseError';
						$returnArray['errorparam'] =  __FUNCTION__ . ' execute error: ' . $dbObj->error;
					}
				}
				else
				{
					$returnArray['error'] = 'str_DatabaseError';
					$returnArray['errorparam'] =  __FUNCTION__ . ' bind_param error: ' . $dbObj->error;
				}
			}
			else
			{
				$returnArray['error'] = 'str_DatabaseError';
				$returnArray['errorparam'] =  __FUNCTION__ . ' execute error: ' . $dbObj->error;
			}

			$dbObj->close();
		}

		return $returnArray;
	}
}
?>
