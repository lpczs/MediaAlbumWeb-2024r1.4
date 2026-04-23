<?php

require_once('../Utils/UtilsDatabase.php');

class AdminUsers_model
{
	static function getGridData()
	{
	    global $gSession;
	    global $gConstants;
		global $ac_config;

	    $resultArray = Array();
	    $userArray = Array();
	    $params = Array();
		$start = 0;
		$i = 0;
		$hideInactive = 0;
		$rateLimitTime = (isset($ac_config['RATELIMITLOCKOUTTIME'])) ? $ac_config['RATELIMITLOCKOUTTIME'] : TPX_AUTHENTICATION_REPEAT_TRIES;

		if (isset($_POST['start']))
		{
			$start = (int)$_POST['start'];
		}

		$limit = 100;
		if (isset($_POST['limit']))
		{
			$limit = (int)$_POST['limit'];
		}

		$sortby = 'login';
		if (isset($_POST['sort']))
		{
			$sortby = $_POST['sort'];
		}

		if (isset($_POST['hideInactive']))
		{
			$hideInactive = filter_input(INPUT_POST, 'hideInactive', FILTER_SANITIZE_NUMBER_INT);
		}

		switch ($sortby) {
		case 'contactfirstname':
			$sort = 'contactfirstname';
			break;
		case 'contactlastname':
			$sort = 'contactlastname';
			break;
		case 'usertype':
			$sort = 'usertype';
			break;
		case 'emailaddress':
			$sort = 'emailaddress';
			break;
		case 'site':
			$sort = 'owner';
			break;
		case 'administrator':
			$sort = 'administrator';
			break;
		case 'active':
			$sort = 'active';
			break;
		default:
			$sort = 'login';
		}

		$dir = 'ASC';
		if (isset($_POST['dir']))
		{
			if ($_POST['dir'] != $dir)
			{
				$dir = 'DESC';
			}
		}

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			$stmt = 'SELECT users.id, users.companycode, users.webbrandcode, users.owner, users.login, users.usertype, users.emailaddress, users.contactfirstname, users.contactlastname, users.active, ';
			$stmt .= 'CASE usertype WHEN '.TPX_LOGIN_PRODUCTION_USER.' THEN (SELECT sites.name FROM SITES WHERE sites.code = users.owner) ';
			$stmt .= 'WHEN '.TPX_LOGIN_COMPANY_ADMIN.' THEN (SELECT companies.companyname FROM COMPANIES where companies.code = users.companycode) ';
			$stmt .= 'WHEN '.TPX_LOGIN_BRAND_OWNER.' THEN (SELECT branding.applicationname FROM BRANDING where branding.code = users.webbrandcode) ';
			$stmt .= 'WHEN '.TPX_LOGIN_SITE_ADMIN.' THEN (SELECT sites.name FROM SITES WHERE sites.code = users.owner)  ';
			$stmt .= 'WHEN '.TPX_LOGIN_STORE_USER.' THEN (SELECT sites.name FROM SITES WHERE sites.code = users.owner) ';
			$stmt .= 'WHEN '.TPX_LOGIN_DISTRIBUTION_CENTRE_USER.' THEN (SELECT sites.name FROM SITES WHERE sites.code = users.owner) ';

			switch ($gSession['userdata']['usertype'])
			{
				case TPX_LOGIN_SYSTEM_ADMIN:
					if ($gConstants['optioncfs'] && $gConstants['optionms'])
					{
						$ignoreUsers = 'users.usertype <> ' . TPX_LOGIN_LICENCE_SERVER_API;
					}
					else if ($gConstants['optioncfs'])
					{
						$ignoreUsers = '(users.usertype <> ' . TPX_LOGIN_LICENCE_SERVER_API . ' AND users.usertype <> ' . TPX_LOGIN_COMPANY_ADMIN .
							' AND users.usertype <> ' . TPX_LOGIN_SITE_ADMIN . ') AND users.companycode = ""';
					}
					else if ($gConstants['optionms'])
					{
						$ignoreUsers = 'users.usertype <> ' . TPX_LOGIN_LICENCE_SERVER_API . ' AND users.usertype <> ' . TPX_LOGIN_STORE_USER .
							' AND users.usertype <> ' . TPX_LOGIN_DISTRIBUTION_CENTRE_USER;
					}
					else if ($gConstants['optionscbo'] || $gConstants['optionwscrp'])
					{
						//The api user needs to be visible if extneral shopping cart or webscripting is enabled
						
						$ignoreUsers = '(users.usertype = ' . TPX_LOGIN_SYSTEM_ADMIN .
							' OR users.usertype = ' . TPX_LOGIN_PRODUCTION_USER .
							' OR users.usertype = ' . TPX_LOGIN_BRAND_OWNER .
							' OR users.usertype = ' . TPX_LOGIN_API .
							' OR users.usertype = ' . TPX_LOGIN_CREATOR_ADMIN . 
							' OR users.usertype = ' . TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER . ') AND users.companycode = ""';
					}
					else
					{
						$ignoreUsers = '(users.usertype <> ' . TPX_LOGIN_LICENCE_SERVER_API . 
							' AND users.usertype = ' . TPX_LOGIN_SYSTEM_ADMIN .
							' OR users.usertype = ' . TPX_LOGIN_PRODUCTION_USER . 
							' OR users.usertype = ' . TPX_LOGIN_BRAND_OWNER . 
							' OR users.usertype = ' . TPX_LOGIN_CREATOR_ADMIN .
							' OR users.usertype = ' . TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER . ') AND users.companycode = ""';
					}

					$stmt .= 'END AS usertypename ';
					$stmt .= ', IF ( TIMESTAMPDIFF(SECOND, UTC_TIMESTAMP(), `nextvalidlogindate`) > ' . $rateLimitTime . ', 1, 0) as accountlocked ';
					$stmt .= 'FROM `USERS` WHERE (' . $ignoreUsers . ' AND `customer` = 0) ';

				break;
				case TPX_LOGIN_COMPANY_ADMIN:
					$stmt .= 'END AS usertypename ';
					$stmt .= ', IF ( TIMESTAMPDIFF(SECOND, UTC_TIMESTAMP(), `nextvalidlogindate`) > ' . $rateLimitTime . ', 1, 0) as accountlocked ';
					$stmt .= 'FROM `USERS` WHERE (users.companycode = ? AND users.usertype <> ' . TPX_LOGIN_COMPANY_ADMIN .
						' AND users.usertype <> ' . TPX_LOGIN_CREATOR_ADMIN . ' AND users.usertype <> '.TPX_LOGIN_LICENCE_SERVER_API . ' AND users.usertype <> ' . TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER . ' AND `customer` = 0)';
	            	$params[] = $gSession['userdata']['companycode'];
				break;
				case TPX_LOGIN_SITE_ADMIN:
					$stmt .= 'END AS usertypename ';
					$stmt .= ', IF ( TIMESTAMPDIFF(SECOND, UTC_TIMESTAMP(), `nextvalidlogindate`) > ' . $rateLimitTime . ', 1, 0) as accountlocked ';
					$stmt .= 'FROM `USERS` WHERE (users.usertype <> ' . TPX_LOGIN_CREATOR_ADMIN . ' AND users.usertype <> ' . TPX_LOGIN_LICENCE_SERVER_API .
						' AND `usertype` <> ' . TPX_LOGIN_SITE_ADMIN . ' AND users.usertype <> ' . TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER . ' AND users.owner = ? AND `customer` = 0)';
					$params[] = $gSession['userdata']['userowner'];
				break;
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
					// if hide inactive turned on only show active
					if ($hideInactive == 1)
					{
						$stmt .= 'AND (active = 1)';
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
				$params = Array();

				// if hide inactive turned on only show active
				if ($hideInactive == 1)
				{
					$stmt .= 'AND (active = 1)';
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
					case TPX_LOGIN_SITE_ADMIN:
						$bind = 1;
						$params[] = $gSession['userdata']['userowner'];
					break;
				}
			}

			$orderBy = ' ORDER BY `companycode`, `' . $sort . '` ' . $dir . ' LIMIT ' . $limit . ' OFFSET ' . $start . ';';

			$userArray = self::bindParams($stmt, $params, $bind, $orderBy);

            $dbObj->close();
		}

        $resultArray['users'] = $userArray['useritems'];
        $resultArray['total'] = $userArray['total'];

        return $resultArray;
	}


    static function userActivate()
    {
        global $gSession;

        $ids = $_POST['ids'];
        $idList = explode(',',$ids);
        $active = $_POST['active'];
        $resultArray = Array();
        if ($active != '0')
		{
			$active = 1;
		}

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('UPDATE `USERS` SET `active` = ? WHERE `id` = ?'))
            {
	         	foreach ($idList as $id)
	         	{
	                if ($stmt->bind_param('ii', $active, $id))
	                {
	                    if($id != 1)
	                    {
		                    if ($stmt->execute())
		                    {
		                        $userAccountArray = DatabaseObj::getUserAccountFromID($id);
		                        if ($userAccountArray['isactive'] == 1)
		                        {
		                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
		                                    'ADMIN', 'USER-DEACTIVATE', $id . ' ' . $userAccountArray['login'], 1);
		                        }
		                        else
		                        {
		                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
		                                    'ADMIN', 'USER-ACTIVATE', $id . ' ' . $userAccountArray['login'], 1);
		                        }
		                        array_push($resultArray, $userAccountArray);
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


    static function userAdd()
    {
        global $gSession;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $recordID = 0;
        $isCustomer = 0;
		$brandCode = '';
		$companyCode = '';
		$owner = '';
		$passwordHash = '';

		$contactFirstName = UtilsObj::getPOSTParam('contactfname');
        $contactLastName = UtilsObj::getPOSTParam('contactlname');
        $login = UtilsObj::getPOSTParam('login_user');
        $password = UtilsObj::getPOSTParam('password_user');
        $emailAddress = UtilsObj::getPOSTParam('email');
        $userType = UtilsObj::getPOSTParam('logintype');
        $canModifyPassword = UtilsObj::getPOSTParam('canmodifypassword');
        $isActive = UtilsObj::getPOSTParam('isactive');
		$passwordFormat = UtilsObj::getPOSTParam('format', TPX_PASSWORDFORMAT_MD5);

    	$ipAccessType = $_POST['ipAccessType'];
        if ($ipAccessType == '0')
        {
        	$ipAccessList = '';
        }
        else
        {
        	$ipAccessList = str_replace(' ', '', $_POST['ipaccesslist']);
        	$ipAccessList = str_replace(array("\r", "\r\n", "\n"), '', $ipAccessList);
        }

		switch ($userType)
		{
			// system admin
			case TPX_LOGIN_SYSTEM_ADMIN:
				$companyCode = '';
				$owner = '';
			break;
			// company admin
			case TPX_LOGIN_COMPANY_ADMIN:
				$companyCode = $_POST['company'];
				$owner = '';
			break;
			// site admin
			case TPX_LOGIN_SITE_ADMIN:
				$siteCode =$_POST['productionsite'];
				$siteArray = DatabaseObj::getSiteFromCode($siteCode);
				$companyCode = $siteArray['companycode'];
				$owner = $_POST['productionsite'];
			break;
			// creator admin
			case TPX_LOGIN_CREATOR_ADMIN:
				$companyCode = '';
				$owner = '';
			break;
			// production user
			case TPX_LOGIN_PRODUCTION_USER:
				if ($gSession['userdata']['usertype'] == TPX_LOGIN_SITE_ADMIN && $gConstants['optionms'])
				{
					$siteCode = $gSession['userdata']['userowner'];
					$siteArray = DatabaseObj::getSiteFromCode($siteCode);
					$companyCode = $siteArray['companycode'];
					$owner = $gSession['userdata']['userowner'];
				}
				else if ($gConstants['optionms'])
				{
					$siteCode = $_POST['productionsite'];
					$siteArray = DatabaseObj::getSiteFromCode($siteCode);
					$companyCode = $siteArray['companycode'];
					$owner = $_POST['productionsite'];
				}
				else if ($gConstants['optioncfs'])
				{
					$companyCode = '';
					$owner = '';
				}
			break;
			case TPX_LOGIN_DISTRIBUTION_CENTRE_USER:
				if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
				{
					$companyCode = $gSession['userdata']['companycode'];
				}
				$owner = $_POST['store'];
			break;
			// store user
			case TPX_LOGIN_STORE_USER:
				if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
				{
					$companyCode = $gSession['userdata']['companycode'];
				}
				$owner = $_POST['store'];
			break;
			// brand owner
			case TPX_LOGIN_BRAND_OWNER:
				$brandCode = $_POST['brand'];

				if ($gConstants['optionms'])
				{
					$brandingArray = DatabaseObj::getBrandingFromCode($brandCode);
					$companyCode = $brandingArray['companycode'];
					$owner = '';
				}
			break;
		}

		if (! class_exists('AuthenticateObj'))
		{
			require_once('../Utils/AuthenticateObj.php');
		}

		// calculate password hash based on if the page is secure or not
		$generategeneratePasswordHashResult = AuthenticateObj::generatePasswordHash($password, $passwordFormat);
		if ($generategeneratePasswordHashResult['result'] == '')
		{
			$passwordHash = $generategeneratePasswordHashResult['data'];
		}
		else
		{
			$result = $generategeneratePasswordHashResult['result'];
			$resultParam = $generategeneratePasswordHashResult['resultparam'];
		}

        if ($canModifyPassword == 'true')
        {
        	$canModifyPassword = 1;
        }
        else
        {
        	$canModifyPassword = 0;
        }

		if ($result == '')
		{
			// check the first name, login and password fields are populated before continuing
			if (($contactFirstName != '') && ($login != '') && ($password !=''))
			{
				// first check to see if the login exists in an external system
				if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
				{
					require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

					if (method_exists('ExternalCustomerAccountObj', 'loginExists'))
					{
						$paramArray = Array();
						$paramArray['id'] = 0;
						$paramArray['login'] = $login;

						$loginExists = ExternalCustomerAccountObj::loginExists($paramArray);
						if ($loginExists)
						{
							// the login exists so we cannot allow the record to be inserted
							$result = 'str_ErrorDuplicateUserName';
						}
					}
				}


				// if we have received no error attempt to insert the user record
				if ($result == '')
				{
					$dbObj = DatabaseObj::getGlobalDBConnection();
					if ($dbObj)
					{
						if ($stmt = $dbObj->prepare('INSERT INTO `USERS` (`id`, `datecreated`, `companycode`, `webbrandcode`, `owner`, `login`, `password`, `customer`, `usertype`, `emailaddress`, `contactfirstname`, `contactlastname`, `modifypassword`, `ipaccesstype`, `ipaccesslist`, `active`)
							VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
						{
							if ($stmt->bind_param('sssssiisssiisi',$companyCode, $brandCode, $owner, $login, $passwordHash, $isCustomer, $userType, $emailAddress, $contactFirstName, $contactLastName, $canModifyPassword, $ipAccessType, $ipAccessList, $isActive))
							{
								if ($stmt->execute())
								{
									$recordID = $dbObj->insert_id;

									DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
										'ADMIN', 'USER-ADD', $recordID . ' ' . $login, 1);
								}
								else
								{
									// could not execute statement
									// first check for a duplicate key (login name)
									if ($stmt->errno == 1062)
									{
										$result = 'str_ErrorDuplicateUserName';
									}
									else
									{
										$result = 'str_DatabaseError';
										$resultParam = 'userAdd execute ' . $dbObj->error;
									}
								}
							}
							else
							{
								// could not bind parameters
								$result = 'str_DatabaseError';
								$resultParam = 'userAdd bind ' . $dbObj->error;
							}

							$stmt->free_result();
							$stmt->close();
							$stmt = null;
						}
						else
						{
							// could not prepare statement
							$result = 'str_DatabaseError';
							$resultParam = 'userAdd prepare ' . $dbObj->error;
						}

						$dbObj->close();
					}
					else
					{
						// could not open database connection
						$result = 'str_DatabaseError';
						$resultParam = 'userAdd connect ' . $dbObj->error;
					}
				}
			}
		}

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['companycode'] = $companyCode;
        $resultArray['owner'] = $owner;
        $resultArray['id'] = $recordID;
        $resultArray['contactfname'] = $contactFirstName;
        $resultArray['contactlname'] = $contactLastName;
        $resultArray['login'] = $login;
        $resultArray['password'] = $passwordHash;
        $resultArray['email'] = $emailAddress;
        $resultArray['usertype'] = $userType;
        $resultArray['canmodifypassword'] = $canModifyPassword;
        $resultArray['isactive'] = $isActive;

        return $resultArray;
    }


    static function userEdit()
    {
        global $gSession;
        global $gConstants;

        $id = $_GET['id'];

        $result = '';
        $resultParam = '';
		$brandCode = '';
		$contactFirstName = UtilsObj::getPOSTParam('contactfname');
        $contactLastName = UtilsObj::getPOSTParam('contactlname');
        $login = UtilsObj::getPOSTParam('login_user');
        $password = UtilsObj::getPOSTParam('password_user');
        $emailAddress = UtilsObj::getPOSTParam('email');
        $userType = UtilsObj::getPOSTParam('logintype');
        $canModifyPassword = UtilsObj::getPOSTParam('canmodifypassword');
        $isActive = UtilsObj::getPOSTParam('isactive');
		$passwordFormat = UtilsObj::getPOSTParam('format', TPX_PASSWORDFORMAT_MD5);

    	$ipAccessType = $_POST['ipAccessType'];
        if ($ipAccessType == '0')
        {
        	$ipAccessList = '';
        }
        else
        {
        	$ipAccessList = str_replace(' ', '', $_POST['ipaccesslist']);
        	$ipAccessList = str_replace(array("\r", "\r\n", "\n"), '', $ipAccessList);
        }

		// the main administrator has been changed.
		// user type is locked to system admin for this user ID.
		// other fields have also been locked down.
		if ($id == 1)
        {
        	$userType = 0;
        	$active = 1;
        }

		switch ($userType)
		{
			// system admin
			case TPX_LOGIN_SYSTEM_ADMIN:
				$companyCode = '';
				$owner = '';
			break;
			// company admin
			case TPX_LOGIN_COMPANY_ADMIN:
				$companyCode = $_POST['company'];
			break;
			// site admin
			case TPX_LOGIN_SITE_ADMIN:
				$siteCode =$_POST['productionsite'];
				$siteArray = DatabaseObj::getSiteFromCode($siteCode);
				$companyCode = $siteArray['companycode'];
				$owner = $_POST['productionsite'];
			break;
			// creator admin
			case TPX_LOGIN_CREATOR_ADMIN:
				$companyCode = '';
				$owner = '';
			break;
			// production user
			case TPX_LOGIN_PRODUCTION_USER:
				if ($gSession['userdata']['usertype'] == TPX_LOGIN_SITE_ADMIN && $gConstants['optionms'])
				{
					$siteCode = $gSession['userdata']['userowner'];
					$siteArray = DatabaseObj::getSiteFromCode($siteCode);
					$companyCode = $siteArray['companycode'];
					$owner = $gSession['userdata']['userowner'];
				}
				else if ($gConstants['optionms'])
				{
					$siteCode = $_POST['productionsite'];
					$siteArray = DatabaseObj::getSiteFromCode($siteCode);
					$companyCode = $siteArray['companycode'];
					$owner = $_POST['productionsite'];
				}
				else if ($gConstants['optioncfs'])
				{
					$companyCode = '';
					$owner = '';
				}
			break;
			case TPX_LOGIN_DISTRIBUTION_CENTRE_USER:
				$siteCode = $_POST['store'];
				$siteArray = DatabaseObj::getSiteFromCode($siteCode);
				$companyCode = $siteArray['companycode'];
				$owner = $_POST['store'];
			break;
			// store user
			case TPX_LOGIN_STORE_USER:
				$siteCode = $_POST['store'];
				$siteArray = DatabaseObj::getSiteFromCode($siteCode);
				$companyCode = $siteArray['companycode'];
				$owner = $_POST['store'];
			break;
			// brand owner
			case TPX_LOGIN_BRAND_OWNER:
				$brandCode = $_POST['brand'];
				$brandingArray = DatabaseObj::getBrandingFromCode($brandCode);
				$companyCode = $brandingArray['companycode'];
				$owner = '';
			break;
		}

        if ($canModifyPassword == 'true')
        {
        	$canModifyPassword = 1;
        }
        else
        {
        	$canModifyPassword = 0;
        }

        if (($id > 0) && ($contactFirstName != '') && ($login != '') && ($password !=''))
        {
            $origUserArray = DatabaseObj::getUserAccountFromID($id);

            if ($password == '**UNCHANGED**')
            {
				// password hasn't changed so use the hash from the database
                $passwordHash = $origUserArray['password'];
            }
			else
			{
				// password has changed so generate the new password hash

				if (! class_exists('AuthenticateObj'))
				{
					require_once('../Utils/AuthenticateObj.php');
				}

				// calculate password hash based on if the page was secure or not
				$generategeneratePasswordHashResult = AuthenticateObj::generatePasswordHash($password, $passwordFormat);
				if ($generategeneratePasswordHashResult['result'] == '')
				{
					$passwordHash = $generategeneratePasswordHashResult['data'];
				}
				else
				{
					$result = $generategeneratePasswordHashResult['result'];
					$resultParam = $generategeneratePasswordHashResult['resultparam'];
				}
			}

			if ($result == '')
			{
				// if the login has changed check to see if the new login exists in an external system
				if ($login != $origUserArray['login'])
				{
					if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
					{
						require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

						if (method_exists('ExternalCustomerAccountObj', 'loginExists'))
						{
							$paramArray = Array();
							$paramArray['id'] = $id;
							$paramArray['login'] = $login;

							$loginExists = ExternalCustomerAccountObj::loginExists($paramArray);
							if ($loginExists)
							{
								// the login exists so we cannot allow the record to be updated
								$result = 'str_ErrorDuplicateUserName';
							}
						}
					}
				}
			}


			// if we have received no error attempt to update the user record
			if ($result == '')
        	{
				$dbObj = DatabaseObj::getGlobalDBConnection();
				if ($dbObj)
				{
					$sql = 'UPDATE `USERS`
							SET `companycode` = ?, `webbrandcode` = ?, `owner` = ?, `login` = ?, `password` = ?, `usertype` = ?,
								`emailaddress` = ?, `contactfirstname` = ?, `contactlastname` = ?, `modifypassword` = ?, ipaccesstype = ?,
								ipaccesslist = ?, `active` = ?
								WHERE `id` = ?';

					$stmt = $dbObj->prepare($sql);

					if ($stmt)
					{
						if ($stmt->bind_param('sssssisssiisii', $companyCode, $brandCode, $owner, $login, $passwordHash, $userType, $emailAddress, $contactFirstName, $contactLastName, $canModifyPassword, $ipAccessType, $ipAccessList, $isActive, $id))
						{
							if ($stmt->execute())
							{
								DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
									'ADMIN', 'USER-UPDATE', $id . ' ' . $login, 1);
							}
							else
							{
								// first check for a duplicate key (login name)
								if ($stmt->errno == 1062)
								{
									$result = 'str_ErrorDuplicateUserName';
								}
								else
								{
									$result = 'str_DatabaseError';
									$resultParam = 'userEdit execute ' . $dbObj->error;
								}
							}
						}
						else
						{
							// could not bind parameters
							$result = 'str_DatabaseError';
							$resultParam = 'userEdit bind ' . $dbObj->error;
						}

						$stmt->free_result();
						$stmt->close();
						$stmt = null;
					}
					else
					{
						// could not prepare statement
						$result = 'str_DatabaseError';
						$resultParam = 'userEdit prepare ' . $dbObj->error;
					}

					$dbObj->close();
				}
				else
				{
					// could not open database connection
					$result = 'str_DatabaseError';
					$resultParam = 'userEdit connect ' . $dbObj->error;
				}
			}
        }

		/*
		 *  If the current user updates their own details then update the session.
		 *  This prevents the reauthentication check failing if they change their userlogin.
		 */
		if (($result == '') && ($gSession['userid'] == $id))
		{
			$gSession['userlogin'] = $login;
			DatabaseObj::updateSession();
		}

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $id;
        $resultArray['companycode'] = $companyCode;
        $resultArray['webbrandcode'] = $brandCode;
        $resultArray['owner'] = $owner;
        $resultArray['contactfname'] = $contactFirstName;
        $resultArray['contactlname'] = $contactLastName;
        $resultArray['login'] = $login;
        $resultArray['password'] = $passwordHash;
        $resultArray['email'] = $emailAddress;
        $resultArray['usertype'] = $userType;
        $resultArray['canmodifypassword'] = $canModifyPassword;
        $resultArray['isactive'] = $isActive;

        return $resultArray;
    }


    static function userDelete()
    {
        global $gSession;

        $resultArray = Array();
        $userIDList = explode (',', $_POST['idlist']);
        $userIDCount = count($userIDList);
        $usersDeleted = Array();

        if ($userIDCount > 0)
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                foreach ($userIDList as $userID )
                {
                	$userAccountArray = DatabaseObj::getUserAccountFromID($userID);

	                if ($stmt = $dbObj->prepare('DELETE FROM `USERS` WHERE `id` = ?'))
	                {
	                    if ($stmt->bind_param('i', $userID))
	                    {
	                        if ($stmt->execute())
	                        {
	                            $allDeleted = 1;
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                'ADMIN', 'USER-DELETE', $userID . ' ' . $userAccountArray['login'], 1);
	                                array_push($usersDeleted, $userID);
	                        }
	                        else
	                        {
	                        	$allDeleted = 0;
	                        }
	                    }
	                    $stmt->free_result();
	                    $stmt->close();
	                    $stmt = null;
	                }
            	}
            }
        }
    	$resultArray['alldeleted'] = $allDeleted;
        $resultArray['userids'] = $usersDeleted;

    	return $resultArray;
    }


    static function bindParams($pStatement,$pParams, $pBind, $pOrderBy)
    {
		$userArray = Array();
		$userItemArray = Array();
		$sqlBindTypes = '';
		$sqlBindVars = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare($pStatement))
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

		if ($dbObj)
		{
			// Concatenate the order by statement to original query so that a limit is set.
			// This is for paging.
			$pStatement .= $pOrderBy;

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
					if ($stmt->bind_result($id, $companyCode, $webBrandCode, $owner, $login, $userType, $emailAddress, $contactFirstName, $contactLastName, $isActive, $usertypeName, $accountLocked))
	                {
	                	if ($stmt->execute())
	                    {
	                        while ($stmt->fetch())
	                        {
	                            $userItem['id'] = $id;
	                            $userItem['companycode'] = $companyCode;
	                            $userItem['webbrandcode'] = $webBrandCode;
	                            $userItem['owner'] = $owner;
	                            $userItem['login'] = $login;
	                            $userItem['usertype'] = $userType;
	                            $userItem['emailaddress'] = $emailAddress;
	                            $userItem['contactfirstname'] = $contactFirstName;
	                            $userItem['contactlastname'] = $contactLastName;
	                            $userItem['isactive'] = $isActive;
	                            $userItem['usertypename'] = $usertypeName;
	                            $userItem['accountlocked'] = $accountLocked;
	                            array_push($userItemArray, $userItem);
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

		$userArray['total'] = $totalRecords;
		$userArray['useritems'] = $userItemArray;

		return $userArray;
    }
}
?>
