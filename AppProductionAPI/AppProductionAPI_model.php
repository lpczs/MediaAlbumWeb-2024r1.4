<?php
require_once('../Utils/UtilsDataExport.php');

class AppProductionAPI_model
{

    /**
     * Decrypts the production api command and authenticates the production session
     *
     * @param string $pCommand
     *   this contains the base64 encoded encrypted command string
     *
     * @param string $pCommand2
     *   this contains the initialization vector for the encryption
     *
     * @Param string $pKey
     *   this contains the encryption key which is used for the login command only
     *
     * @return array
     *   the result array will contain information on the session status, the system key, the plain text command and the encryption key provided for the login process
     *
     * @author Kevin Gale
     * @version 3.0.0
     * @since Version 3.0.0
     */
    static function decryptCommand($pCommand, $pCommand2, $pKey)
    {
        global $gSession;

        $resultArray = Array();
        $decryptCommand = true;
        $cmd = $pCommand;
        $key = $pKey;
        $systemKey = '';
        $sessionActive = false;

        UtilsObj::decodeTransmissionString($cmd, $cmdKeyLen);

        if ($key != '')
        {
        	$iv = substr($key, 0, 8);
        	$key = substr($key, 8);

            UtilsObj::decodeTransmissionString($key, $keyLen);

            $systemConfigArray = DatabaseObj::getSystemConfig();
            $systemKey = $systemConfigArray['systemkey'];

            $key = mcrypt_decrypt(MCRYPT_BLOWFISH, $systemKey, $key, MCRYPT_MODE_CBC, $iv);
            $key = substr($key, 0, $keyLen);

            $cmd = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $cmd, MCRYPT_MODE_CBC, $pCommand2);
            $cmd = substr($cmd, 0, $cmdKeyLen);

            // login is the only command where we accept the key being transferred across
            if ($cmd == 'LOGIN')
            {
                $decryptCommand = false;
                $sessionActive = true;
            }
        }

        // if we still need to decrypt the command grab the key from the session and use it
        if ($decryptCommand == true)
        {
            if (AuthenticateObj::productionSessionActive() == true)
            {
                $cmd = mcrypt_decrypt(MCRYPT_BLOWFISH, $gSession['sessionkey'], $cmd, MCRYPT_MODE_CBC, $pCommand2);
                $cmd = substr($cmd, 0, $cmdKeyLen);

                $sessionActive = true;
            }
        }

        $resultArray['sessionactive'] = $sessionActive;
        $resultArray['systemkey'] = $systemKey;
        $resultArray['cmd'] = $cmd;
        $resultArray['key'] = $key;

        return $resultArray;
    }

    /**
     * Performs the login process for the user based in the POST parameters
     *
     * @static
     *
     * @param array $pCommandArray
     *   the array will contain:
     *      'key' - the encryption key 'key' used to decrypt the login parameters
     *      'systemkey' - the system key used to encrypt the encryption key that is sent back and used for all future communications
     *
     * @return array
     *   the result array will contain the login response to be echo'd back to the calling application
     *
     * @author Kevin Gale
     * @since Version 3.0.0
     */
    static function login($pCommandArray)
    {
        global $gConstants;
        global $gSession;

        $resultArray = Array();
        $result = '';
        $sessionRef = 0;
        $sessionKey = '';
        $loginResult = 0;
        $systemCertificate = '';
        $userID = 0;
        $userName = '';
        $siteCode = '';
        $siteKey = '';
        $companyCode = '';
        $licenseData1 = '';
        $licenseData2 = '';
		$passwordFormat = TPX_PASSWORDFORMAT_CLEARTEXT;

		$authKey = UtilsObj::getPOSTParam('auth1', '');
		$authData = UtilsObj::getPOSTParam('auth2', '');

		if (($authKey != '') && ($authData != ''))
		{
			// decode the authentication key
			$iv = substr($authKey, -8);
			$authKey = substr($authKey, 0, -8);

			UtilsObj::decodeTransmissionString($authKey, $authKeyLength);
			$authKey = mcrypt_decrypt(MCRYPT_BLOWFISH, $pCommandArray['systemkey'], $authKey, MCRYPT_MODE_CBC, $iv);
			$authKey = substr($authKey, 0, $authKeyLength);


			// decode the authentication data
			if (strlen($authKey) == 48)
			{
				UtilsObj::decodeTransmissionString($authData, $authDataLength);
				$authData = mcrypt_decrypt(MCRYPT_BLOWFISH, substr($authKey, 0, 32), $authData, MCRYPT_MODE_CBC, '*5318008');
				$authData = substr($authData, 0, $authDataLength);

				$authDataArray = explode("\t", $authData);
				if (count($authDataArray) == 2)
				{
					// decode the data elements
					$login = $authDataArray[0];
					UtilsObj::decodeTransmissionString($login, $loginLength);
					$login = mcrypt_decrypt(MCRYPT_BLOWFISH, $pCommandArray['key'], $login, MCRYPT_MODE_CBC, substr($authKey, 32, 8));
					$login = substr($login, 0, $loginLength);

					$password = $authDataArray[1];
					UtilsObj::decodeTransmissionString($password, $passwordLength);
					$password = mcrypt_decrypt(MCRYPT_BLOWFISH, $pCommandArray['key'], $password, MCRYPT_MODE_CBC, substr($authKey, 40, 8));
					$password = substr($password, 0, $passwordLength);

					// look for a matching user account
					if (($login != '') && ($password != ''))
					{
						// password for the _taopixlicenseserver user is passed as md5
						// all other users have the password passed as plaintext
						if ($login == '_taopixlicenseserver')
						{
							$passwordFormat = TPX_PASSWORDFORMAT_MD5;
						}

						$userAccountArray = DatabaseObj::getUserAccountFromLoginAndPassword($login, $password, $passwordFormat);
						$result = $userAccountArray['result'];

						if ($result != '')
						{
							// the login failed due to an error
							$loginResult = 1;
						}
					}
					else
					{
						// the authentication data does not appear to be complete
						$loginResult = 1;
					}
				}
				else
				{
					// the authentication data does not appear to be in the correct format
					$loginResult = 1;
				}
			}
			else
			{
				// the authentication key is not the correct length
				$loginResult = 1;
			}
		}
		else
		{
			// the authentication parameters are not present
			$loginResult = 1;
		}


		// if we have got here without an error then we have found a matching user account
		// we now need to perform some more indepth validation
		if (($result == '') && ($loginResult == 0))
        {
        	// we found a matching user account

			// make sure the user is logging in from a valid ip address
			if ($userAccountArray['usertype'] == TPX_LOGIN_PRODUCTION_USER)
			{
				$isIPAllowed = DatabaseObj::isUserIPAllowed($_SERVER['REMOTE_ADDR'], $userAccountArray['ipaccesslist'],
								$userAccountArray['ipaccesstype'], $userAccountArray['companycode']);
				$result = $isIPAllowed['result'];

				if ($result != '')
				{
					// ip address not allowed
					$loginResult = 4;
				}
			}

			// make sure the account is active
			if (($result == '') && ($loginResult == 0))
			{
				if ($userAccountArray['isactive'] == 0)
				{
					// account not active
					$loginResult = 1;
				}
			}

        	// make sure the user account is valid
        	if (($result == '') && ($loginResult == 0))
        	{
				if (($userAccountArray['iscustomer'] == 0) && (($userAccountArray['usertype'] == TPX_LOGIN_PRODUCTION_USER) || ($userAccountArray['usertype'] == TPX_LOGIN_LICENCE_SERVER_API)))
				{
					$userID = $userAccountArray['recordid'];
					$userName = $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'];

					$systemConfigArray = DatabaseObj::getSystemConfig();
					$systemCertificate = $systemConfigArray['systemcertificate'];

					// perform additional validation for multisite
					if ($gConstants['optionms'])
					{
						$siteCode = $userAccountArray['owner'];
						if ($siteCode != '')
						{
							$siteArray = DatabaseObj::getSiteFromCode($siteCode);
							$companyCode = $siteArray['companycode'];
							$siteKey = $siteArray['productionsitekey'];

							if ($siteArray['isactive'] == 1)
							{
								// if the site is active make sure that the production user and site user companies match
								if ($userAccountArray['companycode'] != $siteArray['companycode'])
								{
									// the companies do not match
									$loginResult = 3;
								}
							}
							else
							{
								// the production site is not active
								$loginResult = 2;
							}
						}
					}

					if ($loginResult == 0)
					{
						$companyArray = DatabaseObj::getCompanyFromCode($companyCode);
						$licenseData1 = $companyArray['licensedata1'];
						$licenseData2 = $companyArray['licensedata2'];
					}
				}
				else
				{
					// the account we matched is not valid (ie: not a production login)
					$loginResult = 1;
				}
			}
        }
        else
        {
        	// the login has definitely failed

        	// we should have already set an error code but if not, set one now
        	if ($loginResult == 0)
        	{
        		$loginResult = 1;
        	}
        }


        // if the login is successful create a new session for the production user
        if ($loginResult == 0)
        {
            $sessionRef = DatabaseObj::startSession($userAccountArray['recordid'], $userAccountArray['login'],
                            $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'], TPX_LOGIN_PRODUCTION_USER,
                            $userAccountArray['companycode'], $userAccountArray['owner'], $userAccountArray['webbrandcode'], '', '', Array());

            if ($userAccountArray['usertype'] == TPX_LOGIN_PRODUCTION_USER)
            {
                DatabaseObj::updateActivityLog($sessionRef, 0, $userAccountArray['recordid'], $userAccountArray['login'],
                        $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'], 0, 'PRODUCTION', 'LOGIN', '', 1);
            }

            // create a new key for all further communications
            $keyString = UtilsObj::createRandomString(32);
            $gSession['sessionkey'] = $keyString;

            $sessionKey = strlen($keyString) . '=' . base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, $pCommandArray['systemkey'], $keyString,
                                    MCRYPT_MODE_CBC, '10101010'));

            DatabaseObj::updateSession();
        }

        $resultArray['result'] = $loginResult;
        $resultArray['ref'] = $sessionRef;
        $resultArray['sessionkey'] = $sessionKey;
        $resultArray['id'] = $userID;
        $resultArray['systemcertificate'] = $systemCertificate;
        $resultArray['username'] = $userName;
        $resultArray['sitecode'] = $siteCode;
        $resultArray['sitekey'] = $siteKey;
        $resultArray['licensedata1'] = $licenseData1;
        $resultArray['licensedata2'] = $licenseData2;

        return $resultArray;
    }

    /**
     * Performs the log out process for the production user session
     *
     * @static
     *
     * @author Kevin Gale
     * @since Version 3.0.0
     */
    static function logout()
    {
        global $gSession;

        DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                'PRODUCTION', 'LOGOUT', '', 1);

        // we can just remove the session as it is not required anymore
        DatabaseObj::deleteSession($gSession['ref']);
    }

    static function getProductionSites()
    {
        $productionSiteList = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `id`, `code`, `productionsitekey`, `name`, `active` FROM `SITES` WHERE `productionsitekey` <> "" ORDER BY `code`'))
            {
                if ($stmt->bind_result($siteID, $siteCode, $siteKey, $siteName, $isActive))
                {
                    if ($stmt->execute())
                    {
                        while($stmt->fetch())
                        {
                            $siteItem['id'] = $siteID;
                            $siteItem['code'] = $siteCode;
                            $siteItem['key'] = $siteKey;
                            $siteItem['name'] = $siteName;
                            $siteItem['isactive'] = $isActive;
                            array_push($productionSiteList, $siteItem);
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }

        return $productionSiteList;
    }


    /**
     * Retrieves the main production queue based on the parameters
     *
     * @static
     *
     * @return array
     *   the result array will contain the production queue data
     *
     * @author Kevin Gale
     * @since Version 1.0.0
     */
    static function getOrderList($pOwner, $pClusterNodeCount, $pClusterNodeIndex, $pOrderNumber, $pOrderItemActive,
    		$pOrderStatusWaitingForPayment, $pItemStatus, $pItemStatusOnHold, $pFreeTextSearch, $pDateLastModified, $pLocale, $pLimit = '')
    {
        // return an array containing the orders that meet the specified criteria

        global $gConstants;

        $resultArray = Array();
        $itemsArray = Array();
        $newItems = false;
        $retrieveQueue = true;
        $tempID = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            // if we have been provided with a last modified date perform a basic check to see if the queue has changed
            if ($pDateLastModified != 0)
            {
                $dateLastModifiedMySQL = date('Y-m-d H:i:s', $pDateLastModified);

                if ($stmt = $dbObj->prepare('(SELECT `id` FROM `ORDERHEADER` WHERE `datelastmodified` >= ? LIMIT 1)
											UNION
											(SELECT `id` FROM `ORDERITEMS` WHERE `datelastmodified` >= ? LIMIT 1)'))
                {
                    if ($stmt->bind_param('ss', $dateLastModifiedMySQL, $dateLastModifiedMySQL))
                    {
                        if ($stmt->bind_result($tempID))
                        {
                            if ($stmt->execute())
                            {
                                if (! $stmt->fetch())
                                {
                                    $retrieveQueue = false;
                                }
                            }
                        }
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
            }

            // if we think the queue has changed then we retrieve it and determine the items that have changed
            if ($retrieveQueue)
            {
                $sqlStatement = 'SELECT `oi`.`id`, `oi`.`datelastmodified`, `oi`.`currentowner`, `oi`.`ownerorderkey`,
                	`oi`.`orderid`, `oi`.`userid`, `oi`.`projectname`, `oi`.`productcode`, `oi`.`productname`,
                	`oi`.`productheight`, `oi`.`productwidth`, IF (`oic`.`componentlocalcode` IS NULL, "", `oic`.`componentlocalcode`) as covercode,
					IF (`oic`.`componentname` IS NULL, "", `oic`.`componentname`) as covername,
					IF (`oip`.`componentlocalcode` IS NULL, "", `oip`.`componentlocalcode`) as papercode,
					IF (`oip`.`componentname` IS NULL, "", `oip`.`componentname`) as papername,
					IF (`ofl2`.`outputformatcode` IS NULL, `ofl`.`outputformatcode`, `ofl2`.`outputformatcode`),
					IF (`ofl2`.`outputformatcode` IS NULL, `oflof`.`name`, `oflof2`.`name`), oi.convertoutputformatcode,
					`of`.`name`, `oi`.`qty`, `oi`.`uploaddatatype`, `oi`.`uploadmethod`, `oi`.`uploadref`,
					`oi`.`jobticketoutputfilename`, `oi`.`pagesoutputfilename`, `oi`.`cover1outputfilename`,
					`oi`.`cover2outputfilename`, `oi`.`jobticketoutputdevicecode`, `oi`.`pagesoutputdevicecode`,
					`oi`.`cover1outputdevicecode`, `oi`.`cover2outputdevicecode`, `oi`.`onhold`, `oi`.`onholdreason`,
					`oi`.`status`, `oi`.`statusdescription`, `oh`.`datelastmodified`, `oh`.`shoppingcarttype`,
					`oh`.`orderdate`, `oh`.`ordernumber`, `oh`.`paymentreceived`, `oi`.`active`, `oh`.`groupcode`,
					`oh`.`webbrandcode`, `oh`.`billingcustomeraccountcode`, `oh`.`billingcustomername`,
					`oh`.`billingcontactfirstname`, `oh`.`billingcontactlastname`, `os`.`storecode`, `oi`.`uploadgroupcode`,
					`oi`.`uploadordernumber`, `oi`.`filesreceivedtimestamp`, `oi`.`decryptfilesreceivedtimestamp`,
					`oh`.`temporder`, `oh`.`temporderexpirydate`, `oh`.`sessionid`, `oh`.`itemcount`, `oi`.`itemnumber`,
					`oi`.`outputcount`, `oh`.`offlineorder`, `oi`.`xmloutputfilename`, `oi`.`xmloutputdevicecode`, `oi`.`source`
					FROM `ORDERITEMS` `oi` JOIN `ORDERHEADER` oh ON (`oh`.`id` = `oi`.`orderid`)
					LEFT JOIN `ORDERSHIPPING` `os` ON (`os`.`orderid` = `oh`.`id`)
					LEFT JOIN `ORDERITEMCOMPONENTS` `oic` ON (`oic`.`orderitemid` = `oi`.`id`) AND (`oic`.`componentcategorycode` = "COVER")
					LEFT JOIN `ORDERITEMCOMPONENTS` `oip` ON (`oip`.`orderitemid` = `oi`.`id`) AND (`oip`.`componentcategorycode` = "PAPER")
					LEFT JOIN `OUTPUTFORMATSPRODUCTLINK` `ofl` ON ((`ofl`.`productcode` = `oi`.`productcode`) AND (`ofl`.`componentcode` = "") AND (`ofl`.`owner` = `oi`.`currentowner`))
					LEFT JOIN `OUTPUTFORMATS` `oflof` ON (`oflof`.`code` = `ofl`.`outputformatcode`)
					LEFT JOIN `OUTPUTFORMATSPRODUCTLINK` `ofl2` ON ((`ofl2`.`productcode` = `oi`.`productcode`) AND (`ofl2`.`componentcode` = `oip`.`componentcode`) AND (`ofl2`.`owner` = `oi`.`currentowner`))
					LEFT JOIN `OUTPUTFORMATS` `oflof2` ON (`oflof2`.`code` = `ofl2`.`outputformatcode`)
					LEFT JOIN `OUTPUTFORMATS` `of` ON (`of`.`code` = `oi`.`convertoutputformatcode`)';

                if ($pFreeTextSearch != '')
                {
                    $orderItemID = 0;

                    // check to see if the string starts and ends with the barcode start / stop characters
                    if ((substr($pFreeTextSearch, 0, 1) == '*') && (substr($pFreeTextSearch, -1) == '*'))
                    {
                        $barcodeString = substr($pFreeTextSearch, 1, strlen($pFreeTextSearch) - 2);

                        // check to see if this is a job ticket barcode or a page / cover barcode
                        $barcodeArray = explode('-', $barcodeString);
                        if (count($barcodeArray) == 3)
                        {
                            // this appears to be a job ticket barcode so take the middle element
                            $barcodeString = $barcodeArray[1];
                        }

                        if (is_numeric($barcodeString))
                        {
                            $orderItemID = (int) $barcodeString;
                        }
                    }

                    if ($orderItemID > 0)
                    {
                        $sqlStatement .= 'WHERE (oi.id = ' . $orderItemID . ')';
                    }
                    else
                    {
                        $sqlStatement .= 'WHERE (';
                        $sqlStatement .= '(oh.ordernumber LIKE "%' . $dbObj->real_escape_string($pFreeTextSearch) . '%")';
                        $sqlStatement .= ' OR (CONCAT(oh.billingcontactfirstname , " ", oh.billingcontactlastname) LIKE "%' . $dbObj->real_escape_string($pFreeTextSearch) . '%")';
                        $sqlStatement .= ' OR (oh.billingcustomername LIKE "%' . $dbObj->real_escape_string($pFreeTextSearch) . '%")';
                        $sqlStatement .= ' OR (oh.billingcustomerpostcode LIKE "%' . $dbObj->real_escape_string($pFreeTextSearch) . '%")';
                        $sqlStatement .= ' OR (oh.billingcustomertelephonenumber LIKE "%' . $dbObj->real_escape_string($pFreeTextSearch) . '%")';
                        $sqlStatement .= ' OR (oh.billingcustomeremailaddress LIKE "%' . $dbObj->real_escape_string($pFreeTextSearch) . '%")';
                        //voucher codes will be known thus wildcard is only suffixed for performance reasons 
                        $sqlStatement .= ' OR (oh.vouchercode LIKE "' . $dbObj->real_escape_string($pFreeTextSearch) . '%")';

                        // if the string is numeric also search for an orderitem id
                    	if (is_numeric($pFreeTextSearch))
                        {
                            $sqlStatement .= ' OR (oi.id = ' . $dbObj->real_escape_string($pFreeTextSearch) . ')';
                        }

                        $sqlStatement .= ')';
                    }
                }
                else
                {
                    if ($pOrderNumber != '')
                    {
                        $sqlStatement .= 'WHERE (oh.ordernumber = "' . $dbObj->real_escape_string($pOrderNumber) . '")';
                    }
                    else
                    {
                        if ($pOrderItemActive < 4)
                        {
                            $sqlStatement .= 'WHERE (oi.active = ' . $dbObj->real_escape_string($pOrderItemActive) . ')';
                        }
                        else
                        {
                            if ($pOrderItemActive == 4)
                            {
                                $sqlStatement .= 'WHERE ((oi.active = 1) OR (oi.active = 2))';
                            }
                            else
                            {
                                $sqlStatement .= 'WHERE ((oi.active = 1) OR (oi.active = 2) OR (oi.active = ' . $dbObj->real_escape_string(TPX_ORDER_STATUS_IN_PROGRESS) . '))';
                            }
                        }
                    }

                    if ($pOrderStatusWaitingForPayment == '1')
                    {
                        $sqlStatement .= ' AND (oh.paymentreceived = 0)';
                    }

                    if ($pItemStatusOnHold == '1')
                    {
                        $sqlStatement .= ' AND (oi.onhold = 1)';
                    }

                    if ($pItemStatus != '')
                    {
                        $listSQL = DatabaseObj::convertIDList2SQL($dbObj->real_escape_string($pItemStatus), 'oi.status');
                        if ($listSQL != '')
                        {
                            $sqlStatement .= ' AND ' . $listSQL;
                        }
                    }
                }

                if (($pOwner != '**ALL**') && ($gConstants['optionms']))
                {
                    $sqlStatement .= ' AND (oi.currentowner = "' . $dbObj->real_escape_string($pOwner) . '")';
                }

                if ($pClusterNodeCount > 1)
				{
					$pClusterNodeIndex--;

					$sqlStatement .= ' AND (MINUTE(`oh`.`orderdate`) MOD ' . $dbObj->real_escape_string($pClusterNodeCount) . ' = ' .
						$dbObj->real_escape_string($pClusterNodeIndex) . ')';
				}

				// we want to ignore any companion projects so only pull items back which have a parentorderitemid of 0
				$sqlStatement .= ' AND (`oi`.`parentorderitemid` = 0)';

                // if calling from control centre limit rows
                if ($pLimit != '')
                {
                    $sqlStatement .= ' ORDER BY oi.id DESC LIMIT ' . $pLimit;
                }

                if ($resultSet = $dbObj->query($sqlStatement))
                {
                    // process each item
                    // from version 3.0.0a4 the function now uses fetch_row() to retrieve the records from the database
                    // This proves to be much faster than bind result when returning large amounts of rows. ie 12,000
                    // This means that all results are no longer returned to a variable as there is no bind result function.
                    // Instead each row index is now assigned to the $row array variable.
                    $existingIDArray = array();
                    while ($row = $resultSet->fetch_row())
                    {
                        $id = $row[0];

                        // the sql statement may return multiple copies of the same row
                        // the quickest way is to filter the list via a hash table
                        if (! isset($existingIDArray[$id]))
                        {
                            $queueItem['id'] = $id;

                            // now determine if we need to include the entire row based on the date modified passed
                            $addEntireItem = true;
                            if ($pDateLastModified != 0)
                            {
                                $row[1] = strtotime($row[1]);
                                $row[35] = strtotime($row[35]);
                                if (($row[1] <= $pDateLastModified) && ($row[35] <= $pDateLastModified))
                                {
                                    $addEntireItem = false;
                                }
                            }

                            if ($addEntireItem)
                            {
                                $queueItem['currentowner'] = $row[2];
                                $queueItem['ownerorderkey'] = $row[3];
                                $queueItem['orderid'] = $row[4];
                                $queueItem['userid'] = $row[5];
                                $queueItem['projectname'] = $row[6];
                                $queueItem['productcode'] = $row[7];
                                $queueItem['productname'] = LocalizationObj::getLocaleString($row[8], $pLocale, true);
                                $queueItem['productheight'] = $row[9];
                                $queueItem['productwidth'] = $row[10];
                                $queueItem['covercode'] = $row[11];
                                $queueItem['covername'] = LocalizationObj::getLocaleString($row[12], $pLocale, true);
                                $queueItem['papercode'] = $row[13];
                                $queueItem['papername'] = LocalizationObj::getLocaleString($row[14], $pLocale, true);
                                $queueItem['productoutputformatcode'] = $row[15];
                                $queueItem['productoutputformatname'] = $row[16];
                                $queueItem['convertoutputformatcode'] = $row[17];
                                $queueItem['convertoutputformatname'] = $row[18];
                                $queueItem['qty'] = $row[19];
                                $queueItem['uploaddatatype'] = $row[20];
                                $queueItem['uploadmethod'] = $row[21];
                                $queueItem['uploadref'] = $row[22];
                                $queueItem['jobticketoutputfilename'] = $row[23];
                                $queueItem['pagesoutputfilename'] = $row[24];
                                $queueItem['cover1outputfilename'] = $row[25];
                                $queueItem['cover2outputfilename'] = $row[26];
                                $queueItem['jobticketoutputdevicecode'] = $row[27];
                                $queueItem['pagesoutputdevicecode'] = $row[28];
                                $queueItem['cover1outputdevicecode'] = $row[29];
                                $queueItem['cover2outputdevicecode'] = $row[30];
                                $queueItem['onhold'] = $row[31];
                                $queueItem['onholdreason'] = $row[32];
                                $queueItem['status'] = $row[33];
                                $queueItem['statusdescription'] = $row[34];
                                $queueItem['shoppingcarttype'] = $row[36];
                                $queueItem['orderdate'] = $row[37];
                                $queueItem['ordernumber'] = $row[38];
                                $queueItem['paymentreceived'] = $row[39];
                                $queueItem['orderstatus'] = $row[40];
                                $queueItem['groupcode'] = $row[41];
                                $queueItem['brandcode'] = $row[42];
                                $queueItem['accountcode'] = $row[43];
                                $queueItem['companyname'] = $row[44];
                                $queueItem['contactfirstname'] = $row[45];
                                $queueItem['contactlastname'] = $row[46];
                                $queueItem['storecode'] = $row[47];
                                $queueItem['uploadgroupcode'] = $row[48];
                                $queueItem['uploadordernumber'] = $row[49];
                                $queueItem['filesreceivedtimestamp'] = $row[50];
                                $queueItem['decryptfilesreceivedtimestamp'] = $row[51];
                                $queueItem['temporder'] = $row[52];
                                $queueItem['temporderexpirydate'] = $row[53];
                                $queueItem['sessionid'] = $row[54];
                                $queueItem['orderlinecount'] = $row[55];
                                $queueItem['orderlinenumber'] = $row[56];
                                $queueItem['outputcount'] = $row[57];
                                $queueItem['offlineorder'] = $row[58];
                                $queueItem['xmloutputfilename'] = $row[59];
                                $queueItem['xmloutputdevicecode'] = $row[60];
                                $queueItem['source'] = $row[61];

                                $newItems = true;
                            }
                            else
                            {
                                $queueItem['orderid'] = '<eol>';
                            }

                            $itemsArray[] = $queueItem;

                            $existingIDArray[$id] = true;
                        }
                    }

                    $resultSet->close();
                    $resultSet = null;
                }
                else
                {
                    // could not run the query
                    $result = 'str_DatabaseError';
                    $resultParam = 'getOrderList query ' . $dbObj->error;
                }
            }

            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'getOrderList connect ' . $dbObj->error;
        }

        $resultArray['queueretrieved'] = $retrieveQueue;
        $resultArray['items'] = $itemsArray;
        $resultArray['newitems'] = $newItems;

        return $resultArray;
    }


    /**
     * Retrieves the main production queue, the temp production queue, the output device status and output format status based on the POST parameters
     *
     * @static
     *
     * @return array
     *   the result array will contain the production queue data, the temp production queue data, the output device status data and output format status data
     *   to be echo'd back to the calling application
     *
     * @author Kevin Gale
     * @since Version 1.0.0
     */
    static function getProductionQueue()
	{
	    global $gSession;

		$resultArray = Array();
		$outputDeviceCount = 0;
		$outputDeviceChangeCount = 0;
		$outputFormatCount = 0;
		$outputFormatChangeCount = 0;
		$paperCount = 0;
		$paperCountChanged = 0;
		$brandCount = 0;
		$brandCountChanged = 0;

		$owner = $_POST['owner'];
		$clusterNodeCount = (int) $_POST['clusternodecount'];
		$clusterNodeIndex = (int) $_POST['clusternodeindex'];
		$orderNumber = $_POST['ordernumber'];
		$orderItemActive = $_POST['itemactivestatus'];
		$orderStatusWaitingForPayment = $_POST['orderstatuswaitingforpayment'];
		$itemStatus = $_POST['itemstatus'];
		$itemStatusOnHold = $_POST['itemstatusonhold'];
		$searchString = $_POST['searchstring'];
		$dateLastModified = $_POST['datelastmodified'];
		$queueCount = (int) $_POST['queuecount'];
		$languageCode = $_POST['langcode'];
        $limit = UtilsObj::getPOSTParam('limit');
        
		// get the server time and adjust to prevent any overlap
		$serverTime = strtotime(DatabaseObj::getServerTime()) - 5;

		// get the default brand information
		$webBrandArray = DatabaseObj::getBrandingFromCode('');

		// get the license information
		$companyArray = DatabaseObj::getCompanyFromCode($gSession['userdata']['companycode']);

		$ordersArray = self::getOrderList($owner, $clusterNodeCount, $clusterNodeIndex, $orderNumber, $orderItemActive, $orderStatusWaitingForPayment,
				$itemStatus, $itemStatusOnHold, $searchString, $dateLastModified, $languageCode, $limit);
		$resultArray['queueretrieved'] = $ordersArray['queueretrieved'];
		$resultArray['queuelist'] = $ordersArray['items'];
	    $resultArray['queuenewitems'] = &$ordersArray['newitems'];

		if ($orderNumber == '')
		{
			// obtain the current status of output devices and output formats as this will determine if the client data is valid
			$mysqlDateTime = date('Y-m-d H:i:s', $dateLastModified);

			$dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
            	if ($owner != '**ALL**')
                {
                    $deviceOwner = $owner;
                }
                else
                {
                    $deviceOwner = '';
                }

                if ($stmt = $dbObj->prepare('SELECT COUNT(*), (SELECT COUNT(*) FROM `OUTPUTDEVICES` WHERE (`owner` = ?) AND (`datelastmodified` >= ?)),
                        (SELECT COUNT(*) FROM `OUTPUTFORMATS` WHERE `owner` = ?),
                        (SELECT COUNT(*) FROM `OUTPUTFORMATS` WHERE (`owner` = ?) AND (`datelastmodified` >= ?)),
                        (SELECT COUNT(*) FROM `COMPONENTS` WHERE ((`companycode` = ?) OR (`companycode` = "")) AND (`categorycode` = "PAPER")),
                        (SELECT COUNT(*) FROM `COMPONENTS` WHERE ((`companycode` = ?) OR (`companycode` = "")) AND (`categorycode` = "PAPER") AND (`datelastmodified` >= ?)),
                        (SELECT COUNT(*) FROM `BRANDING` WHERE (`companycode` = ?) OR (`companycode` = "")),
                        (SELECT COUNT(*) FROM `BRANDING` WHERE ((`companycode` = ?) OR (`companycode` = "")) AND (`datelastmodified` >= ?))
                        FROM `OUTPUTDEVICES` WHERE `owner` = ?'))
	            {
	                if ($stmt->bind_param('ssssssssssss', $deviceOwner, $mysqlDateTime, $deviceOwner, $deviceOwner, $mysqlDateTime, $gSession['userdata']['companycode'],
	                        $gSession['userdata']['companycode'], $mysqlDateTime, $gSession['userdata']['companycode'], $gSession['userdata']['companycode'], $mysqlDateTime, $deviceOwner))
                    {
                        if ($stmt->bind_result($outputDeviceCount, $outputDeviceChangeCount, $outputFormatCount, $outputFormatChangeCount, $paperCount, $paperCountChanged, $brandCount, $brandCountChanged))
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
		}
		else
		{
			$resultArray['tempqueuelist'] = Array();
		}

		$resultArray['serverurl'] = $webBrandArray['weburl'];
		$resultArray['servertime'] = $serverTime;
		$resultArray['queuecount'] = $queueCount;
		$resultArray['outputdevicecount'] = $outputDeviceCount;
		$resultArray['outputdevicechangecount'] = $outputDeviceChangeCount;
		$resultArray['outputformatcount'] = $outputFormatCount;
		$resultArray['outputformatchangecount'] = $outputFormatChangeCount;
		$resultArray['papercount'] = $paperCount;
		$resultArray['paperchangecount'] = $paperCountChanged;
		$resultArray['brandcount'] = $brandCount;
		$resultArray['brandchangecount'] = $brandCountChanged;
        $resultArray['licensedata1'] = $companyArray['licensedata1'];
        $resultArray['licensedata2'] = $companyArray['licensedata2'];

		return $resultArray;
	}

    /**
     * Retrieves the data either for all output formats or the output format provided by the POST parameters
     *
     * @static
     *
     * @return array
     *   the result array will contain a list of output formats and the products linked to the output formats to be echo'd back to the calling application
     *
     * @author Kevin Gale
     * @since Version 3.0.0
     */
    static function getOutputFormats()
    {
        // return an array containing the TAOPIX output formats

        global $gSession;

        $result = Array();
        $outputFormatsList = Array();
        $productLinkList = Array();

        $outputFormatCode = $_POST['code'];
        $languageCode = $_POST['langcode'];

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $sqlStatement = 'SELECT `id`, `code`, `localcode`, `name`, `pagestype`, `cover1type`, `cover2type`, `jobticketoptions`, `leftpageoptions`, `rightpageoptions`, `frontcoveroptions`,
	            `backcoveroptions`, `steppagenumbers`, `leftpagefilenameformat`, `rightpagefilenameformat`, `cover1separatefile`, `cover1atfront`, `cover1filenameformat`, `cover2separatefile`,
	            `cover2outputwithcover1`, `cover2filenameformat`, `jobticketseparatefile`, `jobticketfilenameformat`, `xmloutputfile`, `xmlfilenameformat`, `jobticketdefaultoutputdevicecode`,
	            `pagesdefaultoutputdevicecode`, `cover1defaultoutputdevicecode`, `cover2defaultoutputdevicecode`, `xmldefaultoutputdevicecode`, `jobticketsubfoldernameformat`, `pagessubfoldernameformat`,
	            `cover1subfoldernameformat`, `cover2subfoldernameformat`, `xmlsubfoldernameformat`, `xmllanguage`, `xmlincludepaymentdata`, `xmlbeautified`, `printersmarkscolourspace`, `sluginfocolour`,
	            `cropmarkoffset`, `cropmarklength`, `cropmarkwidth`, `cropmarkborderwidth`, `cropmarkcolour`, `bleedoverlapwidth`,
                `foldmarkoffset`, `foldmarklength`, `foldmarkwidth`, `foldmarkborder`, `foldmarkcolour`, `foldmarkcentreline`, `foldmarkoutsidelines`, `foldmarkshowspinewidth`,
                `jobticketcolourspace`, `jobticketcolour`, `leftpageslugbarcodeheight`, `rightpageslugbarcodeheight`, `cover1slugbarcodeheight`, `cover2slugbarcodeheight`
	            FROM `OUTPUTFORMATS`
	            WHERE';

            if ($outputFormatCode == '')
            {
                $sqlStatement .= ' `owner` = ?';
            }
            else
            {
                $sqlStatement .= ' (`owner` = ?) AND (`code` = ?)';
            }

            if ($stmt = $dbObj->prepare($sqlStatement))
            {
                if ($outputFormatCode == '')
                {
                    $bindResult = $stmt->bind_param('s', $gSession['userdata']['userowner']);
                }
                else
                {
                    $bindResult = $stmt->bind_param('ss', $gSession['userdata']['userowner'], $outputFormatCode);
                }

                if ($bindResult == true)
                {
                    if ($stmt->bind_result($recordID, $outputFormatCode, $outputFormatLocalCode, $outputFormatName, $pagesOutputType,
                                    $cover1OutputType, $cover2OutputType, $outputFormatJobTicketOptions, $outputFormatLeftPageOptions,
                                    $outputFormatRightPageOptions, $outputFormatFrontCoverOptions, $outputFormatBackCoverOptions,
                                    $outputFormatStepPageNumbers, $outputFormatLeftPageFilenameFormat, $outputFormatRightPageFilenameFormat,
                                    $outputFormatIsCover1SeparateFile, $outputFormatIsCover1AtFront, $outputFormatCover1FilenameFormat,
                                    $outputFormatIsCover2SeparateFile, $outputFormatCover2OutputWithCover1,
                                    $outputFormatCover2FilenameFormat, $outputFormatIsJobTicketSeparateFile,
                                    $outputFormatJobTicketFilenameFormat, $outputFormatXMLOutputFile, $outputFormatXMLOutputFilenameFormat,
                                    $outputFormatJobTicketDefaultOutputDeviceCode, $outputFormatPagesDefaultOutputDeviceCode,
                                    $outputFormatCover1DefaultOutputDeviceCode, $outputFormatCover2DefaultOutputDeviceCode,
                                    $outputFormatXMLDefaultOutputDeviceCode, $jobTicketSubFolderNameFormat, $pagesSubFolderNameFormat,
                                    $cover1SubFolderNameFormat, $cover2SubFolderNameFormat, $xmlSubFolderNameFormat, $xmlLanguage,
                                    $xmlIncludePaymentData, $xmlBeautified, $printersMarksColourspace, $slugInfoColour, $cropMarkOffset,
                                    $cropMarkLength, $cropMarkWidth, $cropMarkBorderWidth, $cropMarkColour, $bleedOverlapWidth,
                                    $foldMarkOffset, $foldMarkLength, $foldMarkWidth, $foldMarkBorderWidth, $foldMarkColour, $foldMarkCentreLine, $foldMarkOutsideLines, $foldMarkShowSpineWidth,
                                    $jobTicketColourSpace, $jobTicketColour, $leftPageSlugBarcodeHeight, $rightPageSlugBarcodeHeight, $cover1SlugBarcodeHeight, $cover2SlugBarcodeHeight))
                    {
                        if ($stmt->execute())
                        {
                            while($stmt->fetch())
                            {
                                $outputFormatItem['id'] = $recordID;
                                $outputFormatItem['code'] = $outputFormatCode;
                                $outputFormatItem['localcode'] = $outputFormatLocalCode;
                                $outputFormatItem['name'] = $outputFormatName;
                                $outputFormatItem['pagestype'] = $pagesOutputType;
                                $outputFormatItem['cover1type'] = $cover1OutputType;
                                $outputFormatItem['cover2type'] = $cover2OutputType;
                                $outputFormatItem['jobticketoptions'] = $outputFormatJobTicketOptions;
                                $outputFormatItem['leftpageoptions'] = $outputFormatLeftPageOptions;
                                $outputFormatItem['rightpageoptions'] = $outputFormatRightPageOptions;
                                $outputFormatItem['frontcoveroptions'] = $outputFormatFrontCoverOptions;
                                $outputFormatItem['backcoveroptions'] = $outputFormatBackCoverOptions;
                                $outputFormatItem['steppagenumbers'] = $outputFormatStepPageNumbers;
                                $outputFormatItem['leftpagefilenameformat'] = $outputFormatLeftPageFilenameFormat;
                                $outputFormatItem['rightpagefilenameformat'] = $outputFormatRightPageFilenameFormat;
                                $outputFormatItem['iscover1separatefile'] = $outputFormatIsCover1SeparateFile;
                                $outputFormatItem['iscover1atfront'] = $outputFormatIsCover1AtFront;
                                $outputFormatItem['cover1filenameformat'] = $outputFormatCover1FilenameFormat;
                                $outputFormatItem['iscover2separatefile'] = $outputFormatIsCover2SeparateFile;
                                $outputFormatItem['cover2outputwithcover1'] = $outputFormatCover2OutputWithCover1;
                                $outputFormatItem['cover2filenameformat'] = $outputFormatCover2FilenameFormat;
                                $outputFormatItem['isjobticketseparatefile'] = $outputFormatIsJobTicketSeparateFile;
                                $outputFormatItem['jobticketfilenameformat'] = $outputFormatJobTicketFilenameFormat;
                                $outputFormatItem['xmloutputfile'] = $outputFormatXMLOutputFile;
                                $outputFormatItem['xmlfilenameformat'] = $outputFormatXMLOutputFilenameFormat;
                                $outputFormatItem['jobticketdefaultoutputdevicecode'] = $outputFormatJobTicketDefaultOutputDeviceCode;
                                $outputFormatItem['pagesdefaultoutputdevicecode'] = $outputFormatPagesDefaultOutputDeviceCode;
                                $outputFormatItem['cover1defaultoutputdevicecode'] = $outputFormatCover1DefaultOutputDeviceCode;
                                $outputFormatItem['cover2defaultoutputdevicecode'] = $outputFormatCover2DefaultOutputDeviceCode;
                                $outputFormatItem['xmldefaultoutputdevicecode'] = $outputFormatXMLDefaultOutputDeviceCode;
                                $outputFormatItem['jobticketsubfoldernameformat'] = $jobTicketSubFolderNameFormat;
                                $outputFormatItem['pagessubfoldernameformat'] = $pagesSubFolderNameFormat;
                                $outputFormatItem['cover1subfoldernameformat'] = $cover1SubFolderNameFormat;
                                $outputFormatItem['cover2subfoldernameformat'] = $cover2SubFolderNameFormat;
                                $outputFormatItem['xmlsubfoldernameformat'] = $xmlSubFolderNameFormat;
                                $outputFormatItem['xmllanguage'] = $xmlLanguage;
                                $outputFormatItem['xmlincludepaymentdata'] = $xmlIncludePaymentData;
                                $outputFormatItem['xmlbeautified'] = $xmlBeautified;
                                $outputFormatItem['printersmarkscolourspace'] = $printersMarksColourspace;
                                $outputFormatItem['sluginfocolour'] = $slugInfoColour;
                                $outputFormatItem['cropmarkoffset'] = $cropMarkOffset;
                                $outputFormatItem['cropmarklength'] = $cropMarkLength;
                                $outputFormatItem['cropmarkwidth'] = $cropMarkWidth;
                                $outputFormatItem['cropmarkborderwidth'] = $cropMarkBorderWidth;
                                $outputFormatItem['cropmarkcolour'] = $cropMarkColour;
                                $outputFormatItem['foldmarkoffset'] = $foldMarkOffset;
                                $outputFormatItem['foldmarklength'] = $foldMarkLength;
                                $outputFormatItem['foldmarkwidth'] = $foldMarkWidth;
                                $outputFormatItem['foldmarkborderwidth'] = $foldMarkBorderWidth;
                                $outputFormatItem['foldmarkcolour'] = $foldMarkColour;
                                $outputFormatItem['foldmarkcentreline'] = $foldMarkCentreLine;
                                $outputFormatItem['foldmarkoutsidelines'] = $foldMarkOutsideLines;
                                $outputFormatItem['foldmarkshowspinewidth'] = $foldMarkShowSpineWidth;
                                $outputFormatItem['bleedoverlapwidth'] = $bleedOverlapWidth;
                                $outputFormatItem['jobticketcolourspace'] = $jobTicketColourSpace;
                                $outputFormatItem['jobticketcolour'] = $jobTicketColour;
                                $outputFormatItem['leftpageslugbarcodeheight'] = $leftPageSlugBarcodeHeight;
                                $outputFormatItem['rightpageslugbarcodeheight'] = $rightPageSlugBarcodeHeight;
                                $outputFormatItem['cover1slugbarcodeheight'] = $cover1SlugBarcodeHeight;
                                $outputFormatItem['cover2slugbarcodeheight'] = $cover2SlugBarcodeHeight;
                                $outputFormatsList[] = $outputFormatItem;
                            }
                        }
                    }
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;

                $bindResult = false;
                if ($outputFormatCode == '')
                {
                    if ($stmt = $dbObj->prepare('SELECT `outputformatcode`, `productcode`, `componentcode` FROM `OUTPUTFORMATSPRODUCTLINK` WHERE `owner` = ?'))
                    {
                        $bindResult = $stmt->bind_param('s', $gSession['userdata']['userowner']);
                    }
                }
                else
                {
                    if ($stmt = $dbObj->prepare('SELECT `outputformatcode`, `productcode`,`componentcode` FROM `OUTPUTFORMATSPRODUCTLINK` WHERE (`owner` = ?) AND (`outputformatcode` = ?)'))
                    {
                        $bindResult = $stmt->bind_param('ss', $gSession['userdata']['userowner'], $outputFormatCode);
                    }
                }

                if ($bindResult == true)
                {
                    if ($stmt->bind_result($outputFormatCode, $productCode, $componentCode))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
                                $productLinkItem['outputformatcode'] = $outputFormatCode;
                                $productLinkItem['productcode'] = $productCode;
                                $productLinkItem['componentcode'] = $componentCode;
                                $productLinkList[] = $productLinkItem;
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

        $result['outputformatlist'] = $outputFormatsList;
        $result['productlinklist'] = $productLinkList;

        return $result;
    }

    /**
     * Updates the product and output format matrix database table
     *
     * @static
     *
     * @param string $pOutputFormatCode
     * @param string $pProducts
     * @return array
     *
     * @author Kevin Gale
     * @since Version 3.0.0
     */
    static function updateOutputFormatProducts($pOutputFormatCode, $pProducts, $pComponents)
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
        $resultParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($pProducts != '')
            {
                $productList = explode(',', $pProducts);
                $componentList = explode(',', $pComponents);
                $itemCount = count($productList);
            }
            else
            {
                $itemCount = -1;
            }

            // first delete the products that were linked to this output format
            if ($stmt = $dbObj->prepare('DELETE FROM `OUTPUTFORMATSPRODUCTLINK` WHERE (`owner` = ?) AND (`outputformatcode` = ?)'))
            {
                if ($stmt->bind_param('ss', $gSession['userdata']['userowner'], $pOutputFormatCode))
                {
                    if ($stmt->execute())
                    {
                        // now delete the products that are linked to any other output format for this production site
                        if ($stmt2 = $dbObj->prepare('DELETE FROM `OUTPUTFORMATSPRODUCTLINK` WHERE (`owner` = ?) AND (`productcode` = ?) AND (`componentcode` = ?)'))
                        {
                            for($i = 0; $i < $itemCount; $i++)
                            {
                                $productCode = $productList[$i];
                                $componentCode = $componentList[$i];

                                if ($stmt2->bind_param('sss', $gSession['userdata']['userowner'], $productCode, $componentCode))
                                {
                                    if (!$stmt2->execute())
                                    {
                                        // could not execute statement
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'updateOutputFormatProducts execute2 ' . $dbObj->error;
                                        break;
                                    }
                                }
                                else
                                {
                                    // could not bind parameters
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'updateOutputFormatProducts bind2 ' . $dbObj->error;
                                    break;
                                }
                                $stmt2->free_result();
                            }

                            $stmt2->close();
                            $stmt2 = null;
                        }
                        else
                        {
                            // could not prepare statement
                            $result = 'str_DatabaseError';
                            $resultParam = 'updateOutputFormatProducts prepare2 ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not execute statement
                        $result = 'str_DatabaseError';
                        $resultParam = 'updateOutputFormatProducts execute ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not bind parameters
                    $result = 'str_DatabaseError';
                    $resultParam = 'updateOutputFormatProducts bind ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;

                if ($result == '')
                {
                    // now create the link between the output formats, the products and the production site
                    if ($stmt = $dbObj->prepare('INSERT INTO `OUTPUTFORMATSPRODUCTLINK` VALUES (0, now(), ?, ?, ?, ?)'))
                    {
                        for($i = 0; $i < $itemCount; $i++)
                        {
                            $productCode = $productList[$i];
                            $componentCode = $componentList[$i];

                            if ($stmt->bind_param('ssss', $gSession['userdata']['userowner'], $pOutputFormatCode, $productCode,
                                            $componentCode))
                            {
                                if (!$stmt->execute())
                                {
                                    // could not execute statement
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'updateOutputFormatProducts execute3 ' . $dbObj->error;
                                    break;
                                }
                            }
                            else
                            {
                                // could not bind parameters
                                $result = 'str_DatabaseError';
                                $resultParam = 'updateOutputFormatProducts bind3 ' . $dbObj->error;
                                break;
                            }

                            $stmt->free_result();
                        }

                        $stmt->close();
                        $stmt = null;
                    }
                    else
                    {
                        // could not prepare statement
                        $result = 'str_DatabaseError';
                        $resultParam = 'updateOutputFormatProducts prepare3 ' . $dbObj->error;
                    }
                }
            }
            else
            {
                // could not prepare statement
                $result = 'str_DatabaseError';
                $resultParam = 'updateOutputFormatProducts prepare ' . $dbObj->error;
            }

            $dbObj->close();
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
    }

    /**
     * Inserts a new output format into the database based on the POST parameters
     *
     * @static
     *
     * @return array
     *   the result array will contain the new output format data to be echo'd back to the calling application
     *
     * @author Kevin Gale
     * @since Version 3.0.0
     */
    static function outputFormatAdd()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
        $resultParam = '';
        $recordID = 0;

        $languageCode = $_POST['langcode'];

        $outputFormatCode = $_POST['code'];
        $outputFormatLocalCode = $_POST['localcode'];
        $outputFormatName = $_POST['name'];
        $pagesOutputType = $_POST['pagesoutputtype'];
        $cover1OutputType = $_POST['cover1outputtype'];
        $cover2OutputType = $_POST['cover2outputtype'];
        $outputFormatJobTicketOptions = $_POST['jobticketoptions'];
        $outputFormatLeftPageOptions = $_POST['leftpageoptions'];
        $outputFormatRightPageOptions = $_POST['rightpageoptions'];
        $outputFormatFrontCoverOptions = $_POST['frontcoveroptions'];
        $outputFormatBackCoverOptions = $_POST['backcoveroptions'];
        $outputFormatStepPageNumbers = $_POST['steppagenumbers'];
        $outputFormatLeftPageFilenameFormat = $_POST['leftpagefilenameformat'];
        $outputFormatRightPageFilenameFormat = $_POST['rightpagefilenameformat'];
        $outputFormatIsCover1SeparateFile = $_POST['iscover1separatefile'];
        $outputFormatIsCover1AtFront = $_POST['iscover1atfront'];
        $outputFormatCover1FilenameFormat = $_POST['cover1filenameformat'];
        $outputFormatIsCover2SeparateFile = $_POST['iscover2separatefile'];
        $outputFormatCover2OutputWithCover1 = $_POST['cover2outputwithcover1'];
        $outputFormatCover2FilenameFormat = $_POST['cover2filenameformat'];
        $outputFormatIsJobTicketSeparateFile = $_POST['isjobticketseparatefile'];
        $outputFormatJobTicketFilenameFormat = $_POST['jobticketfilenameformat'];
        $outputFormatXMLOutputFile = $_POST['xmloutputfile'];
        $outputFormatXMLFilenameFormat = $_POST['xmlfilenameformat'];
        $outputFormatJobTicketOutputDeviceCode = $_POST['jobticketdefaultoutputdevicecode'];
        $outputFormatPagesOutputDeviceCode = $_POST['pagesdefaultoutputdevicecode'];
        $outputFormatCover1OutputDeviceCode = $_POST['cover1defaultoutputdevicecode'];
        $outputFormatCover2OutputDeviceCode = $_POST['cover2defaultoutputdevicecode'];
        $outputFormatXMLOutputDeviceCode = $_POST['xmldefaultoutputdevicecode'];
        $outputFormatJobTicketSubFolderNameFormat = $_POST['jobticketsubfoldernameformat'];
        $outputFormatPagesSubFolderNameFormat = $_POST['pagessubfoldernameformat'];
        $outputFormatCover1SubFolderNameFormat = $_POST['cover1subfoldernameformat'];
        $outputFormatCover2SubFolderNameFormat = $_POST['cover2subfoldernameformat'];
        $outputFormatXMLSubFolderNameFormat = $_POST['xmlsubfoldernameformat'];
        $outputFormatXMLLanguage = $_POST['xmllanguage'];
        $outputFormatXMLIncludePaymentData = $_POST['xmlincludepaymentdata'];
        $outputFormatXMLBeautified = $_POST['xmlbeautified'];
        $printersMarksColourspace = $_POST['printersmarkscolourspace'];
        $slugInfoColour = $_POST['sluginfocolour'];
        $cropMarkOffset = $_POST['cropmarkoffset'];
        $cropMarkLength = $_POST['cropmarklength'];
        $cropMarkWidth = $_POST['cropmarkwidth'];
        $cropMarkBorderWidth = $_POST['cropmarkborderwidth'];
        $cropMarkColour = $_POST['cropmarkcolour'];
        $bleedOverlapWidth = $_POST['bleedoverlapwidth'];
        $outputFormatProducts = $_POST['products'];
        $outputFormatComponents = $_POST['components'];
        $foldMarkOffset = UtilsObj::getPOSTParam('foldmarkoffset', '');
        $foldMarkLength = UtilsObj::getPOSTParam('foldmarklength', '');
        $foldMarkWidth = UtilsObj::getPOSTParam('foldmarkwidth', '');
        $foldMarkBorderWidth = UtilsObj::getPOSTParam('foldmarkborderwidth', '');
        $foldMarkColour = UtilsObj::getPOSTParam('foldmarkcolour', '');
        $foldMarkCentreLine = UtilsObj::getPOSTParam('foldmarkcentreline', 0);
        $foldMarkOutsideLines = UtilsObj::getPOSTParam('foldmarkoutsidelines', 0);
        $foldMarkShowSpineWidth = UtilsObj::getPOSTParam('foldmarkshowspinewidth', 0);
        $jobTicketColourSpace = UtilsObj::getPOSTParam('jobticketcolourspace', 0);
        $jobTicketColour = UtilsObj::getPOSTParam('jobticketcolour', '');
        $leftPageSlugBarcodeHeight = UtilsObj::getPOSTParam('leftpagebarcodeheight', '0');
        $rightPageSlugBarcodeHeight = UtilsObj::getPOSTParam('rightpagebarcodeheight', '0');
        $cover1SlugBarcodeHeight = UtilsObj::getPOSTParam('cover1barcodeheight', '0');
        $cover2SlugBarcodeHeight = UtilsObj::getPOSTParam('cover2barcodeheight', '0');

        if (($outputFormatCode == 'JPEG') || ($outputFormatCode == 'TIFF') || ($outputFormatCode == 'PDFMULTIPAGE') || ($outputFormatCode == 'PDFSINGLEPAGE'))
        {
            $result = 'str_ErrorOutputFormatExists';
        }
        else
        {
            if (($outputFormatCode != '') && ($outputFormatName != ''))
            {
                $dbObj = DatabaseObj::getGlobalDBConnection();
                if ($dbObj)
                {
                    if ($stmt = $dbObj->prepare('INSERT INTO `OUTPUTFORMATS` (`id`, `datecreated`, `owner`, `code`, `localcode`, `name`, `pagestype`, `cover1type`, `cover2type`,
                        `jobticketoptions`, `leftpageoptions`, `rightpageoptions`, `frontcoveroptions`, `backcoveroptions`, `steppagenumbers`, `leftpagefilenameformat`,
                        `rightpagefilenameformat`, `cover1separatefile`, `cover1atfront`, `cover1filenameformat`, `cover2separatefile`, `cover2outputwithcover1`, `cover2filenameformat`,
                        `jobticketseparatefile`, `jobticketfilenameformat`, `xmloutputfile`, `xmlfilenameformat`, `jobticketdefaultoutputdevicecode`, `pagesdefaultoutputdevicecode`,
                        `cover1defaultoutputdevicecode`, `cover2defaultoutputdevicecode`, `xmldefaultoutputdevicecode`, `jobticketsubfoldernameformat`, `pagessubfoldernameformat`,
                        `cover1subfoldernameformat`, `cover2subfoldernameformat`, `xmlsubfoldernameformat`, `xmllanguage`, `xmlincludepaymentdata`, `xmlbeautified`, `printersmarkscolourspace`,
                        `sluginfocolour`, `cropmarkoffset`, `cropmarklength`, `cropmarkwidth`, `cropmarkborderwidth`, `cropmarkcolour`, `bleedoverlapwidth`,
                        `foldmarkoffset`, `foldmarklength`, `foldmarkwidth`, `foldmarkborder`, `foldmarkcolour`, `foldmarkcentreline`, `foldmarkoutsidelines`, `foldmarkshowspinewidth`,
                        `jobticketcolourspace`, `jobticketcolour`, `leftpageslugbarcodeheight`, `rightpageslugbarcodeheight`, `cover1slugbarcodeheight`, `cover2slugbarcodeheight`)
                        VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
                    {
                        if ($stmt->bind_param('sssssss' . 'ssss' . 'siss' . 'iisi' . 'isis' . 'issss' . 'ssss' . 'ssssi' . 'iisssssss' . 'sssssiii' . 'isssss',
                                        $gSession['userdata']['userowner'], $outputFormatCode, $outputFormatLocalCode, $outputFormatName,
                                        $pagesOutputType, $cover1OutputType, $cover2OutputType, $outputFormatJobTicketOptions,
                                        $outputFormatLeftPageOptions, $outputFormatRightPageOptions, $outputFormatFrontCoverOptions,
                                        $outputFormatBackCoverOptions, $outputFormatStepPageNumbers, $outputFormatLeftPageFilenameFormat,
                                        $outputFormatRightPageFilenameFormat, $outputFormatIsCover1SeparateFile,
                                        $outputFormatIsCover1AtFront, $outputFormatCover1FilenameFormat, $outputFormatIsCover2SeparateFile,
                                        $outputFormatCover2OutputWithCover1, $outputFormatCover2FilenameFormat,
                                        $outputFormatIsJobTicketSeparateFile, $outputFormatJobTicketFilenameFormat,
                                        $outputFormatXMLOutputFile, $outputFormatXMLFilenameFormat, $outputFormatJobTicketOutputDeviceCode,
                                        $outputFormatPagesOutputDeviceCode, $outputFormatCover1OutputDeviceCode,
                                        $outputFormatCover2OutputDeviceCode, $outputFormatXMLOutputDeviceCode,
                                        $outputFormatJobTicketSubFolderNameFormat, $outputFormatPagesSubFolderNameFormat,
                                        $outputFormatCover1SubFolderNameFormat, $outputFormatCover2SubFolderNameFormat,
                                        $outputFormatXMLSubFolderNameFormat, $outputFormatXMLLanguage, $outputFormatXMLIncludePaymentData,
                                        $outputFormatXMLBeautified, $printersMarksColourspace, $slugInfoColour, $cropMarkOffset,
                                        $cropMarkLength, $cropMarkWidth, $cropMarkBorderWidth, $cropMarkColour, $bleedOverlapWidth,
                                        $foldMarkOffset, $foldMarkLength, $foldMarkWidth, $foldMarkBorderWidth, $foldMarkColour, $foldMarkCentreLine, $foldMarkOutsideLines, $foldMarkShowSpineWidth,
                                        $jobTicketColourSpace, $jobTicketColour, $leftPageSlugBarcodeHeight, $rightPageSlugBarcodeHeight, $cover1SlugBarcodeHeight, $cover2SlugBarcodeHeight))
                        {
                            if ($stmt->execute())
                            {
                                $recordID = $dbObj->insert_id;

                                $updateProductListResult = self::updateOutputFormatProducts($outputFormatCode, $outputFormatProducts,
                                                $outputFormatComponents);
                                $result = $updateProductListResult['result'];
                                $resultParam = $updateProductListResult['resultparam'];

                                if ($result == '')
                                {
                                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                            $gSession['username'], 0, 'ADMIN', 'OUTPUTFORMAT-ADD', $recordID . ' ' . $outputFormatCode, 1);
                                }
                            }
                            else
                            {
                                // could not execute statement
                                // first check for a duplicate key (output format code)
                                if ($stmt->errno == 1062)
                                {
                                    $result = 'str_ErrorOutputFormatExists';
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'outputFormatAdd execute ' . $dbObj->error;
                                }
                            }
                        }
                        else
                        {
                            // could not bind parameters
                            $result = 'str_DatabaseError';
                            $resultParam = 'outputFormatAdd bind ' . $dbObj->error;
                        }
                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                    else
                    {
                        // could not prepare statement
                        $result = 'str_DatabaseError';
                        $resultParam = 'outputFormatAdd prepare ' . $dbObj->error;
                    }
                    $dbObj->close();
                }
                else
                {
                    // could not open database connection
                    $result = 'str_DatabaseError';
                    $resultParam = 'outputFormatAdd connect ' . $dbObj->error;
                }
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $recordID;
        $resultArray['code'] = $outputFormatCode;
        $resultArray['localcode'] = $outputFormatLocalCode;
        $resultArray['name'] = $outputFormatName;
        $resultArray['langcode'] = $languageCode;

        return $resultArray;
    }

    /**
     * Updates the output format database record based on the POST parameters
     *
     * @static
     *
     * @return array
     *   the result array will contain the updated output format data to be echo'd back to the calling application
     *
     * @author Kevin Gale
     * @since Version 3.0.0
     */
    static function outputFormatEdit()
    {
        global $gSession;

        $result = '';
        $resultParam = '';

        $languageCode = $_POST['langcode'];

        $id = $_POST['id'];
        $outputFormatCode = $_POST['code'];
        $outputFormatLocalCode = $_POST['localcode'];
        $outputFormatName = $_POST['name'];
        $pagesOutputType = $_POST['pagesoutputtype'];
        $cover1OutputType = $_POST['cover1outputtype'];
        $cover2OutputType = $_POST['cover2outputtype'];
        $outputFormatJobTicketOptions = $_POST['jobticketoptions'];
        $outputFormatLeftPageOptions = $_POST['leftpageoptions'];
        $outputFormatRightPageOptions = $_POST['rightpageoptions'];
        $outputFormatFrontCoverOptions = $_POST['frontcoveroptions'];
        $outputFormatBackCoverOptions = $_POST['backcoveroptions'];
        $outputFormatStepPageNumbers = $_POST['steppagenumbers'];
        $outputFormatLeftPageFilenameFormat = $_POST['leftpagefilenameformat'];
        $outputFormatRightPageFilenameFormat = $_POST['rightpagefilenameformat'];
        $outputFormatIsCover1SeparateFile = $_POST['iscover1separatefile'];
        $outputFormatIsCover1AtFront = $_POST['iscover1atfront'];
        $outputFormatCover1FilenameFormat = $_POST['cover1filenameformat'];
        $outputFormatIsCover2SeparateFile = $_POST['iscover2separatefile'];
        $outputFormatCover2OutputWithCover1 = $_POST['cover2outputwithcover1'];
        $outputFormatCover2FilenameFormat = $_POST['cover2filenameformat'];
        $outputFormatIsJobTicketSeparateFile = $_POST['isjobticketseparatefile'];
        $outputFormatJobTicketFilenameFormat = $_POST['jobticketfilenameformat'];
        $outputFormatXMLOutputFile = $_POST['xmloutputfile'];
        $outputFormatXMLFilenameFormat = $_POST['xmlfilenameformat'];
        $outputFormatJobTicketOutputDeviceCode = $_POST['jobticketdefaultoutputdevicecode'];
        $outputFormatPagesOutputDeviceCode = $_POST['pagesdefaultoutputdevicecode'];
        $outputFormatCover1OutputDeviceCode = $_POST['cover1defaultoutputdevicecode'];
        $outputFormatCover2OutputDeviceCode = $_POST['cover2defaultoutputdevicecode'];
        $outputFormatXMLOutputDeviceCode = $_POST['xmldefaultoutputdevicecode'];
        $outputFormatJobTicketSubFolderNameFormat = $_POST['jobticketsubfoldernameformat'];
        $outputFormatPagesSubFolderNameFormat = $_POST['pagessubfoldernameformat'];
        $outputFormatCover1SubFolderNameFormat = $_POST['cover1subfoldernameformat'];
        $outputFormatCover2SubFolderNameFormat = $_POST['cover2subfoldernameformat'];
        $outputFormatXMLSubFolderNameFormat = $_POST['xmlsubfoldernameformat'];
        $outputFormatXMLLanguage = $_POST['xmllanguage'];
        $outputFormatXMLIncludePaymentData = $_POST['xmlincludepaymentdata'];
        $outputFormatXMLBeautified = $_POST['xmlbeautified'];
        $printersMarksColourspace = $_POST['printersmarkscolourspace'];
        $slugInfoColour = $_POST['sluginfocolour'];
        $cropMarkOffset = $_POST['cropmarkoffset'];
        $cropMarkLength = $_POST['cropmarklength'];
        $cropMarkWidth = $_POST['cropmarkwidth'];
        $cropMarkBorderWidth = $_POST['cropmarkborderwidth'];
        $cropMarkColour = $_POST['cropmarkcolour'];
        $bleedOverlapWidth = $_POST['bleedoverlapwidth'];
        $outputFormatProducts = $_POST['products'];
        $outputFormatComponents = $_POST['components'];
        $foldMarkOffset = UtilsObj::getPOSTParam('foldmarkoffset', '');
        $foldMarkLength = UtilsObj::getPOSTParam('foldmarklength', '');
        $foldMarkWidth = UtilsObj::getPOSTParam('foldmarkwidth', '');
        $foldMarkBorderWidth = UtilsObj::getPOSTParam('foldmarkborderwidth', '');
        $foldMarkColour = UtilsObj::getPOSTParam('foldmarkcolour', '');
        $foldMarkCentreLine = UtilsObj::getPOSTParam('foldmarkcentreline', 0);
        $foldMarkOutsideLines = UtilsObj::getPOSTParam('foldmarkoutsidelines', 0);
        $foldMarkShowSpineWidth = UtilsObj::getPOSTParam('foldmarkshowspinewidth', 0);
        $jobTicketColourSpace = UtilsObj::getPOSTParam('jobticketcolourspace', 0);
        $jobTicketColour = UtilsObj::getPOSTParam('jobticketcolour', '');
        $leftPageSlugBarcodeHeight = UtilsObj::getPOSTParam('leftpagebarcodeheight', '0');
        $rightPageSlugBarcodeHeight = UtilsObj::getPOSTParam('rightpagebarcodeheight', '0');
        $cover1SlugBarcodeHeight = UtilsObj::getPOSTParam('cover1barcodeheight', '0');
        $cover2SlugBarcodeHeight = UtilsObj::getPOSTParam('cover2barcodeheight', '0');

        if (($id > 0) && ($outputFormatName != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                // set 'datelastmodified' so it is updated even when only the matrix, but not OUTPUTFORMATS table has changed
                if ($stmt = $dbObj->prepare('UPDATE `OUTPUTFORMATS` SET `datelastmodified` = now(), `name` = ?, `pagestype` = ?, `cover1type` = ?, `cover2type` = ?, `jobticketoptions` = ?,
                        `leftpageoptions` = ?, `rightpageoptions` = ?, `frontcoveroptions` = ?, `backcoveroptions` = ?, `steppagenumbers` = ?, `leftpagefilenameformat` = ?, `rightpagefilenameformat` = ?,
                        `cover1separatefile` = ?, `cover1atfront` = ?, `cover1filenameformat` = ?, `cover2separatefile` = ?, `cover2outputwithcover1` = ?, `cover2filenameformat` = ?,
                        `jobticketseparatefile` = ?, `jobticketfilenameformat` = ?, `xmloutputfile` = ?, `xmlfilenameformat` = ?,
                        `jobticketdefaultoutputdevicecode` = ?, `pagesdefaultoutputdevicecode` = ?, `cover1defaultoutputdevicecode` = ?, `cover2defaultoutputdevicecode` = ?, `xmldefaultoutputdevicecode` = ?,
                        `jobticketsubfoldernameformat` = ?, `pagessubfoldernameformat` = ?, `cover1subfoldernameformat` = ?, `cover2subfoldernameformat` = ?,
                        `xmlsubfoldernameformat` = ?, `xmllanguage` = ?, `xmlincludepaymentdata` = ?, `xmlbeautified` = ?,
                        `printersmarkscolourspace` = ?, `sluginfocolour` = ?, `cropmarkoffset` = ?, `cropmarklength` = ?, `cropmarkwidth` = ?, `cropmarkborderwidth` = ?, `cropmarkcolour` = ?, `bleedoverlapwidth` = ?,
                        `foldmarkoffset` = ?, `foldmarklength` = ?, `foldmarkwidth` = ?, `foldmarkborder` = ?, `foldmarkcolour` = ?, `foldmarkcentreline` = ?, `foldmarkoutsidelines` = ?,
                        `foldmarkshowspinewidth` = ?, `jobticketcolourspace` = ?, `jobticketcolour` = ?, `leftpageslugbarcodeheight` = ?, `rightpageslugbarcodeheight` = ?,
                        `cover1slugbarcodeheight` = ?, `cover2slugbarcodeheight` = ?
                        WHERE `id` = ?'))
                {
                    if ($stmt->bind_param('ssssss' . 'sssi' . 'ssii' . 'siis' . 'isis' . 'ssss' . 'ssss' . 'sssii' . 'isssssssi' . 'sssssi' . 'iisss' . 'ss' . 'i',
                                    $outputFormatName, $pagesOutputType, $cover1OutputType, $cover2OutputType,
                                    $outputFormatJobTicketOptions, $outputFormatLeftPageOptions, $outputFormatRightPageOptions,
                                    $outputFormatFrontCoverOptions, $outputFormatBackCoverOptions, $outputFormatStepPageNumbers,
                                    $outputFormatLeftPageFilenameFormat, $outputFormatRightPageFilenameFormat,
                                    $outputFormatIsCover1SeparateFile, $outputFormatIsCover1AtFront, $outputFormatCover1FilenameFormat,
                                    $outputFormatIsCover2SeparateFile, $outputFormatCover2OutputWithCover1,
                                    $outputFormatCover2FilenameFormat, $outputFormatIsJobTicketSeparateFile,
                                    $outputFormatJobTicketFilenameFormat, $outputFormatXMLOutputFile, $outputFormatXMLFilenameFormat,
                                    $outputFormatJobTicketOutputDeviceCode, $outputFormatPagesOutputDeviceCode,
                                    $outputFormatCover1OutputDeviceCode, $outputFormatCover2OutputDeviceCode,
                                    $outputFormatXMLOutputDeviceCode, $outputFormatJobTicketSubFolderNameFormat,
                                    $outputFormatPagesSubFolderNameFormat, $outputFormatCover1SubFolderNameFormat,
                                    $outputFormatCover2SubFolderNameFormat, $outputFormatXMLSubFolderNameFormat, $outputFormatXMLLanguage,
                                    $outputFormatXMLIncludePaymentData, $outputFormatXMLBeautified, $printersMarksColourspace,
                                    $slugInfoColour, $cropMarkOffset, $cropMarkLength, $cropMarkWidth, $cropMarkBorderWidth,
                                    $cropMarkColour, $bleedOverlapWidth,
                                    $foldMarkOffset, $foldMarkLength, $foldMarkWidth, $foldMarkBorderWidth, $foldMarkColour, $foldMarkCentreLine, $foldMarkOutsideLines,
                                    $foldMarkShowSpineWidth, $jobTicketColourSpace, $jobTicketColour, $leftPageSlugBarcodeHeight, $rightPageSlugBarcodeHeight,
                                    $cover1SlugBarcodeHeight, $cover2SlugBarcodeHeight,
                                    $id))
                    {
                        if ($stmt->execute())
                        {
                            $updateProductListResult = self::updateOutputFormatProducts($outputFormatCode, $outputFormatProducts,
                                            $outputFormatComponents);
                            $result = $updateProductListResult['result'];
                            $resultParam = $updateProductListResult['resultparam'];

                            if ($result == '')
                            {
                                DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                        $gSession['username'], 0, 'ADMIN', 'OUTPUTFORMAT-UPDATE', $id . ' ' . $outputFormatCode, 1);
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'outputFormatEdit execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'outputFormatEdit bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'outputFormatEdit prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'outputFormatEdit connect ' . $dbObj->error;
            }
        }
        else if ($id == -1)
        {
            // update the products linked to one of the built-in output formats
            $updateProductListResult = self::updateOutputFormatProducts($outputFormatCode, $outputFormatProducts, $outputFormatComponents);
            $result = $updateProductListResult['result'];
            $resultParam = $updateProductListResult['resultparam'];

            if ($result == '')
            {
                DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                        'ADMIN', 'OUTPUTFORMAT-UPDATE', $id . ' ' . $outputFormatCode, 1);
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $id;
        $resultArray['code'] = $outputFormatCode;
        $resultArray['localcode'] = $outputFormatLocalCode;
        $resultArray['name'] = $outputFormatName;
        $resultArray['langcode'] = $languageCode;

        return $resultArray;
    }

    /**
     * Deletes the output format database record based on the POST parameters
     *
     * @static

     * @return array
     *   the result array will contain the result of the deletion to be echo'd back to the calling application
     *
     * @author Kevin Gale
     * @since Version 3.0.0
     */
    static function outputFormatDelete()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
        $resultParam = '';
        $deleted = false;

        $languageCode = $_POST['langcode'];
        $outputFormatID = $_POST['id'];
        $outputFormatCode = $_POST['code'];
        if ($outputFormatID)
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('DELETE FROM `OUTPUTFORMATS` WHERE `id` = ?'))
                {
                    if ($stmt->bind_param('i', $outputFormatID))
                    {
                        $deleted = $stmt->execute();
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }

                if ($deleted == true)
                {
                    if ($stmt = $dbObj->prepare('DELETE FROM `OUTPUTFORMATSPRODUCTLINK` WHERE `outputformatcode` = ?'))
                    {
                        if ($stmt->bind_param('s', $outputFormatCode))
                        {
                            if ($stmt->execute())
                            {
                                DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                        $gSession['username'], 0, 'ADMIN', 'OUTPUTFORMAT-DELETE', $outputFormatID . ' ' . $outputFormatCode,
                                        1);
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

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['langcode'] = $languageCode;

        return $resultArray;
    }

    /**
     * Retrieves a list of products from the database that are available to the company within the session
     *
     * @static
     *
     * @return array
     *   the result array will contain the list of product codes and names to be echo'd back to the calling application
     *
     * @author Kevin Gale
     * @since Version 3.0.0
     */
    static function getProductsList()
    {
        global $gSession;

        $resultArray = Array();

        $languageCode = $_POST['langcode'];

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `code`, `name` FROM `PRODUCTS` WHERE ((`companycode` = "") OR (`companycode` = ?)) AND (`deleted` = 0) ORDER BY `code`'))
            {
                if ($stmt->bind_param('s', $gSession['userdata']['companycode']))
                {
                    if ($stmt->bind_result($productCode, $productName))
                    {
                        if ($stmt->execute())
                        {
                            while($stmt->fetch())
                            {
                                $productItem['productcode'] = $productCode;
                                $productItem['productname'] = LocalizationObj::getLocaleString($productName, $languageCode, true);
                                array_push($resultArray, $productItem);
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

    /**
     * Retrieves a list of components from the database that are available to the company within the session
     *
     * @static
     *
     * @return array
     *   the result array will contain the list of components to be echo'd back to the calling application
     *
     * @author Kevin Gale
     * @since Version 3.2.0
     */
    static function getComponentsList()
    {
        global $gSession;

        $resultArray = Array();

        $categoryCode = $_POST['type'];
        $languageCode = $_POST['langcode'];

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `id`, `code`, `localcode`, `name`, `active` FROM `COMPONENTS`
                WHERE ((`companycode` = "") OR (`companycode` = ?)) AND (`categorycode` = ?)'))
            {
                if ($stmt->bind_param('ss', $gSession['userdata']['companycode'], $categoryCode))
                {
                    if ($stmt->bind_result($id, $code, $localCode, $name, $active))
                    {
                        if ($stmt->execute())
                        {
                            while($stmt->fetch())
                            {
                                $itemArray['id'] = $id;
                                $itemArray['code'] = $code;
                                $itemArray['localcode'] = $localCode;
                                $itemArray['name'] = LocalizationObj::getLocaleString($name, $languageCode, true);
                                $itemArray['active'] = $active;
                                $resultArray[] = $itemArray;
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

    static function getOrderStatusList()
    {
        $resultArray = Array();
        $orderArray = Array();
        $brandArray = Array();

        // increase the timeout as we could be retrieving a lot of data here
        UtilsObj::resetPHPScriptTimeout(120);

        $companyCode = UtilsObj::getPOSTParam('companycode', '');
        $installs = (int) UtilsObj::getPOSTParam('installs', '0');
        $info1 = UtilsObj::getPOSTParam('info1', '');
        $info2 = UtilsObj::getPOSTParam('info2', '');
		$firstRecord = (int) UtilsObj::getPOSTParam('firstrecord', '0');
        $maxRecordCount = (int) UtilsObj::getPOSTParam('maxrecords', '0');
		$lastStatusDate = (string) UtilsObj::getPOSTParam('laststatusdate', '');

        $systemConfigArray = DatabaseObj::getSystemConfig();
        $resultArray['webversionnumber'] = $systemConfigArray['webversionnumber'];

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($installs = 0)
            {
                if ($stmt = $dbObj->prepare('UPDATE `COMPANIES` SET `licensedatadate` = NOW(), `licensedata1` = ?, `licensedata2` = ?'))
                {
                    if ($stmt->bind_param('ss', $info1, $info2))
                    {
                        $stmt->execute();
                    }

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
            }
            else
            {
                if ($stmt = $dbObj->prepare('UPDATE `COMPANIES` SET `licensedatadate` = NOW(), `licensedata1` = ?, `licensedata2` = ? WHERE `code` = ?'))
                {
                    if ($stmt->bind_param('sss', $info1, $info2, $companyCode))
                    {
                        $stmt->execute();
                    }

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
            }

            $sqlStatement = 'SELECT `id`, `status`, `statustimestamp` FROM `ORDERITEMS` WHERE (`status` >= 13) AND ((`active` = 0) OR (`active` = 2))';

			if ($lastStatusDate != '')
			{
				$sqlStatement .= ' AND ((`activetimestamp` >= ?) OR (`statustimestamp` >= ?))';
			}

            $sqlStatement .= ' ORDER BY `id` ASC';

			if ($maxRecordCount > 0)
			{
				$sqlStatement .= ' LIMIT ' . $firstRecord . ', ' . $maxRecordCount;
			}

            if ($stmt = $dbObj->prepare($sqlStatement))
            {
                if ($lastStatusDate != '')
                {
                    $bindResult = $stmt->bind_param('ss', $lastStatusDate, $lastStatusDate);
                }
                else
                {
                    $bindResult = true;
                }

                if ($bindResult)
                {
                    if ($stmt->bind_result($orderItemID, $orderItemStatus, $orderItemStatusTimeStamp))
                    {
                        if ($stmt->execute())
                        {
                            while($stmt->fetch())
                            {
                                $orderStatusItem = Array();
                                $orderStatusItem['id'] = $orderItemID;
                                $orderStatusItem['status'] = $orderItemStatus;
                                $orderStatusItem['statustimestamp'] = $orderItemStatusTimeStamp;
                                $orderArray[] = $orderStatusItem;
                            }
                        }
                    }
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }

            if ($stmt = $dbObj->prepare('SELECT `code`, `name`, `applicationname`, `displayurl`, `weburl`, `mainwebsiteurl`, `macdownloadurl`, `win32downloadurl` FROM `BRANDING` WHERE `active` = 1'))
            {
                if ($stmt->bind_result($brandCode, $brandName, $brandApplicationName, $brandDisplayURL, $brandWebURL, $brandMainWebsiteURL,
                                $brandMacDownloadURL, $brandWin32DownloadURL))
                {
                    if ($stmt->execute())
                    {
                        while($stmt->fetch())
                        {
                            $brandItem = Array();
                            $brandItem['code'] = $brandCode;
                            $brandItem['name'] = $brandName;
                            $brandItem['applicationname'] = $brandApplicationName;
                            $brandItem['displayurl'] = $brandDisplayURL;
                            $brandItem['weburl'] = $brandWebURL;
                            $brandItem['mainwebsiteurl'] = $brandMainWebsiteURL;
                            $brandItem['macdownloadurl'] = $brandMacDownloadURL;
                            $brandItem['win32downloadurl'] = $brandWin32DownloadURL;
                            $brandArray[] = $brandItem;
                        }
                    }
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }


            $dbObj->close();
        }

        $resultArray['orders'] = &$orderArray;
        $resultArray['brands'] = &$brandArray;

        return $resultArray;
    }

    /**
     * Retrieves a list of order item status data based on the list of id's in the POST parameter
     *
     * @static
     *
     * @return array
     *   the result array will contain the order status data to be echo'd back to the calling application
     *
     * @author Kevin Gale
     * @since Version 1.0.0
     */
    static function getOrderStatusData()
    {
        $resultArray = Array();

        $orderItemList = $_POST['orderitemlist'];

        $sqlStatement = 'SELECT oh.ownercode, oh.orderdate, oh.id, oh.ordernumber, oh.groupcode, oh.userid, oh.offlineorder, oi.id, oi.uploadref,
			oh.currencycode, oh.currencyname, oh.currencyisonumber, oh.currencysymbol, oh.currencysymbolatfront, oh.currencydecimalplaces,
			oh.billingcustomercountrycode, os.shippingcustomercountrycode, os.shippingmethodcode, os.shippingmethodname, oi.currentcompanycode, oi.currentowner, oi.currentownertype,
			oi.projectname, oi.projectbuildstartdate, oi.projectbuildduration, oi.productcode, oi.productname, oi.productheight, oi.productwidth,
			IF (oic.componentlocalcode IS NULL, "", oic.componentlocalcode),
			IF (oic.componentname IS NULL, "", oic.componentname),
			IF (oip.componentcode IS NULL, "", oip.componentcode),
			IF (oip.componentname IS NULL, "", oip.componentname),
			oi.pagecount, oi.productunitcost, oi.productunitsell, oic.componentunitcost, oic.componentunitsell, oip.componentunitcost, oip.componentunitsell,
			oi.taxcode, oi.taxname, oi.taxrate, oi.qty, oi.producttotalcost, oi.producttotalsell,
			oic.componenttotalcost, oic.componenttotalsell, oip.componenttotalcost, oip.componenttotalsell, oi.subtotal, oi.discountvalue, oi.totalcost,
			oi.totalsell, oi.totaltax, oi.uploadappversion, oi.uploadappplatform, oi.uploadappcputype, oi.uploadapposversion,
			oi.uploaddatasize, oi.uploadduration, oi.uploaddatatype, oi.uploadmethod,
			oi.outputtimestamp, oi.shippedtimestamp, oi.shippeddate, oi.statustimestamp, oi.status, oi.active,
			oh.groupdata, oh.shoppingcarttype, oh.designeruuid, oh.userbrowser, oh.webbrandcode, oh.voucherpromotioncode, oh.voucherpromotionname, oh.vouchercode, oh.vouchertype,
			oh.vouchername, oh.voucherdiscountsection, oh.voucherdiscounttype, oh.voucherdiscountvalue, oh.voucherapplicationmethod, oh.vouchermaxqtytoapplydiscountto, oh.vouchersellprice,
			oh.voucheragentfee, oh.itemcount, oh.shippingtotalcost, oh.shippingtotalsellbeforediscount, oh.shippingtotalsell, oh.shippingtotaltax, oi.itemnumber, oi.shareid,
			oi.origorderitemid, oi.projectref, oi.productcollectioncode, oi.productcollectionname, oi.producttype, oi.productpageformat, oi.productspreadpageformat,
			oi.productcover1format, oi.productcover2format, oi.productoutputformat, oi.orderwebversion, oh.total, oh.totaltax, oh.totaldiscount, oh.giftcardamount, oh.totaltopay,
			oh.paymentmethodcode, oh.paymentgatewaycode, oh.paymentgatewaysubcode, oh.pricesincludetax, os.shippingdiscountvalue, oi.discountname, oi.source, oh.totalsellbeforediscount,
			oh.ordertotalitemsellwithtax, oh.orderfootertotalwithtax, oh.orderfootertotalnotax, oh.orderfootertotalnotaxnodiscount, oh.orderfootertaxratesequal, oh.orderfootersubtotal,
			oh.orderfootertotal, oh.orderfootertotaltax, oh.orderfooterdiscountvalue, oi.productcollectionorigownercode, oi.parentorderitemid, oh.pricingengineversion, oi.projectlsdata
			FROM `ORDERITEMS` oi
			LEFT JOIN `ORDERHEADER` oh ON oh.id = oi.orderid
			LEFT JOIN `ORDERSHIPPING` os ON os.orderid = oi.orderid
			LEFT JOIN ORDERITEMCOMPONENTS oic ON (oi.id = oic.orderitemid) AND (oic.componentcategorycode="COVER")
			LEFT JOIN ORDERITEMCOMPONENTS oip ON (oi.id = oip.orderitemid) AND (oip.componentcategorycode="PAPER")';

        $itemIDArray = explode(',', $orderItemList);
        $itemCount = count($itemIDArray);
        if ($itemCount > 0)
        {
            $sqlStatement = $sqlStatement . ' WHERE (';
            for($i = 0; $i < $itemCount; $i++)
            {
                $sqlStatement = $sqlStatement . '(oi.id = ' . $itemIDArray[$i] . ')';
                if ($i < ($itemCount - 1))
                {
                    $sqlStatement = $sqlStatement . ' OR ';
                }
            }
            $sqlStatement = $sqlStatement . ') ORDER BY oi.id ASC';
        }

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare($sqlStatement))
            {
                if ($stmt->bind_result($ownerCode, $orderDate, $orderID, $orderNumber, $groupCode, $userID, $offlineOrder, $orderItemID,
                                $uploadRef, $currencyCode, $currencyName, $currencyISONumber, $currencySymbol, $currencySymbolAtFront,
                                $currencyDecimalPlaces, $billingCountryCode, $shippingCountryCode, $shippingMethodCode, $shippingMethodName,
                                $currentCompanyCode, $currentOwner, $currentOwnerType, $projectName, $projectBuildStartDate,
                                $projectBuildDuration, $productCode, $productName, $productHeight, $productWidth, $coverCode, $coverName,
                                $paperCode, $paperName, $pageCount, $productUnitCost, $productUnitSell, $coverUnitCost, $coverUnitSell,
                                $paperUnitCost, $paperUnitSell, $taxCode, $taxName, $taxRate, $qty, $productTotalCost, $productTotalSell,
                                $coverTotalCost, $coverTotalSell, $paperTotalCost, $paperTotalSell, $subTotal, $discountValue, $totalCost,
                                $totalSell, $totalTax, $uploadAppVersion, $uploadAppPlatform, $uploadAppCPUType, $uploadAppOSVersion,
                                $uploadDataSize, $uploadDuration, $uploadDataType, $uploadMethod, $outputTimeStamp, $shippedTimeStamp,
                                $shippedDate, $statusTimeStamp, $itemStatus, $orderItemActive, $groupData, $shoppingCartType, $designerUUID,
                                $orderBrowserType, $webBrandCode, $voucherPromotionCode, $voucherPromotionName, $voucherCode, $voucherType,
                                $voucherName, $voucherDiscountSection, $voucherDiscountType, $voucherDiscountValue, $voucherApplicationMethod,
                                $voucherApplyToQty, $voucherSellPrice, $voucherAgentFee, $itemCount, $shippingTotalCost, $shippingTotalSellBeforeDiscount,
                                $shippingTotalSell, $shippingTotalTax, $itemNumber, $shareID, $origOrderItemID, $projectRef, $productCollectionCode,
                                $productCollectionName, $productType, $productPageFormat, $productSpreadPageFormat, $productCover1Format,
                                $productCover2Format, $productOutputFormat, $orderWebVersion, $orderTotal, $orderTotalTax,
                                $orderTotalDiscount, $orderGiftCardAmount, $orderTotalToPay, $paymentMethodCode, $paymentGatewayCode,
                                $paymentGatewaySubCode, $pricesIncludeTax, $shippingTotalDiscount, $discountName, $source, $orderTotalSellBeforeDiscount,
                                $orderTotalItemSellWithTax, $orderFooterTotalWithTax, $orderFooterTotalNoTax, $orderFooterTotalNoTaxNoDiscount, $orderFooterTaxRatesEqual,
                                $orderFooterSubtotal, $orderFooterTotal, $orderFooterTotalTax, $orderFooterDiscountValue, $productCollectionOrigOwnerCode, $parentOrderItemID,
                                $orderPricingEngineVersion, $projectLSData))
                {
                    if ($stmt->execute())
                    {
                        // process each item
                        while($stmt->fetch())
                        {
                            $statusItem['ownercode'] = $ownerCode;
                            $statusItem['currentcompanycode'] = $currentCompanyCode;
                            $statusItem['currentowner'] = $currentOwner;
                            $statusItem['currentownertype'] = $currentOwnerType;
                            $statusItem['orderdate'] = $orderDate;
                            $statusItem['orderid'] = $orderID;
                            $statusItem['ordernumber'] = $orderNumber;
                            $statusItem['orderstatus'] = $orderItemActive;
                            $statusItem['groupcode'] = $groupCode;
                            $statusItem['userid'] = $userID;
                            $statusItem['orderoffline'] = $offlineOrder;
                            $statusItem['orderitemid'] = $orderItemID;
                            $statusItem['uploadref'] = $uploadRef;
                            $statusItem['currencycode'] = $currencyCode;
                            $statusItem['currencyname'] = $currencyName;
                            $statusItem['currencyisonumber'] = $currencyISONumber;
                            $statusItem['currencysymbol'] = $currencySymbol;
                            $statusItem['currencysymbolatfront'] = $currencySymbolAtFront;
                            $statusItem['currencydecimalplaces'] = $currencyDecimalPlaces;
                            $statusItem['billingcountrycode'] = $billingCountryCode;
                            $statusItem['shippingcountrycode'] = $shippingCountryCode;
                            $statusItem['shippingmethodcode'] = $shippingMethodCode;
                            $statusItem['shippingmethodname'] = $shippingMethodName;
                            $statusItem['projectname'] = $projectName;
                            $statusItem['projectbuildstartdate'] = $projectBuildStartDate;
                            $statusItem['projectbuildduration'] = $projectBuildDuration;
                            $statusItem['productcode'] = $productCode;
                            $statusItem['productname'] = $productName;
                            $statusItem['productheight'] = $productHeight;
                            $statusItem['productwidth'] = $productWidth;
                            $statusItem['covercode'] = $coverCode;
                            $statusItem['covername'] = $coverName;
                            $statusItem['papercode'] = $paperCode;
                            $statusItem['papername'] = $paperName;
                            $statusItem['pagecount'] = $pageCount;
                            $statusItem['productunitcost'] = $productUnitCost;
                            $statusItem['productunitsell'] = $productUnitSell;
                            $statusItem['coverunitcost'] = $coverUnitCost;
                            $statusItem['coverunitsell'] = $coverUnitSell;
                            $statusItem['paperunitcost'] = $paperUnitCost;
                            $statusItem['paperunitsell'] = $paperUnitSell;
                            $statusItem['taxcode'] = $taxCode;
                            $statusItem['taxname'] = $taxName;
                            $statusItem['taxrate'] = $taxRate;
                            $statusItem['qty'] = $qty;
                            $statusItem['producttotalcost'] = $productTotalCost;
                            $statusItem['producttotalsell'] = $productTotalSell;
                            $statusItem['covertotalcost'] = $coverTotalCost;
                            $statusItem['covertotalsell'] = $coverTotalSell;
                            $statusItem['papertotalcost'] = $paperTotalCost;
                            $statusItem['papertotalsell'] = $paperTotalSell;
                            $statusItem['subtotal'] = $subTotal;
                            $statusItem['discountvalue'] = $discountValue;
                            $statusItem['totalcost'] = $totalCost;
                            $statusItem['totalsell'] = $totalSell;
                            $statusItem['totaltax'] = $totalTax;
                            $statusItem['uploadappversion'] = $uploadAppVersion;
                            $statusItem['uploadappplatform'] = $uploadAppPlatform;
                            $statusItem['uploadappcputype'] = $uploadAppCPUType;
                            $statusItem['uploadapposversion'] = $uploadAppOSVersion;
                            $statusItem['uploaddatasize'] = $uploadDataSize;
                            $statusItem['uploadduration'] = $uploadDuration;
                            $statusItem['uploaddatatype'] = $uploadDataType;
                            $statusItem['uploadmethod'] = $uploadMethod;
                            $statusItem['outputtimestamp'] = $outputTimeStamp;
                            $statusItem['shippedtimestamp'] = $shippedTimeStamp;
                            $statusItem['shippeddate'] = $shippedDate;
                            $statusItem['statustimestamp'] = $statusTimeStamp;
                            $statusItem['itemstatus'] = $itemStatus;
                            $statusItem['groupdata'] = $groupData;
                            $statusItem['shoppingcarttype'] = $shoppingCartType;
                            $statusItem['designeruuid'] = $designerUUID;
                            $statusItem['browsertype'] = $orderBrowserType;
                            $statusItem['webbrandcode'] = $webBrandCode;
                            $statusItem['voucherpromotioncode'] = $voucherPromotionCode;
                            $statusItem['voucherpromotionname'] = $voucherPromotionName;
                            $statusItem['vouchercode'] = $voucherCode;
                            $statusItem['vouchertype'] = $voucherType;
                            $statusItem['vouchername'] = $voucherName;
                            $statusItem['voucherdiscountsection'] = $voucherDiscountSection;
                            $statusItem['voucherdiscountype'] = $voucherDiscountType;
                            $statusItem['voucherdiscountvalue'] = $voucherDiscountValue;
                            $statusItem['voucherapplicationmethod'] = $voucherApplicationMethod;
                            $statusItem['voucherapplytoqty'] = $voucherApplyToQty;
                            $statusItem['vouchersellprice'] = $voucherSellPrice;
                            $statusItem['voucheragentfee'] = $voucherAgentFee;
                            $statusItem['itemcount'] = $itemCount;
                            $statusItem['shippingtotalcost'] = $shippingTotalCost;
                            $statusItem['shippingtotalsellbeforediscount'] = $shippingTotalSellBeforeDiscount;
                            $statusItem['shippingtotalsell'] = $shippingTotalSell;
                            $statusItem['shippingtotaltax'] = $shippingTotalTax;
                            $statusItem['itemnumber'] = $itemNumber;
                            $statusItem['shareid'] = $shareID;
                            $statusItem['origorderitemid'] = $origOrderItemID;
                            $statusItem['projectref'] = $projectRef;
                            $statusItem['collectioncode'] = $productCollectionCode;
                            $statusItem['collectionname'] = $productCollectionName;
                            $statusItem['producttype'] = $productType;
                            $statusItem['productpageformat'] = $productPageFormat;
                            $statusItem['productspreadformat'] = $productSpreadPageFormat;
                            $statusItem['productcover1format'] = $productCover1Format;
                            $statusItem['productcover2format'] = $productCover2Format;
                            $statusItem['productoutputformat'] = $productOutputFormat;
                            $statusItem['orderwebversion'] = $orderWebVersion;
                            $statusItem['ordertotal'] = $orderTotal;
                            $statusItem['ordertotaltax'] = $orderTotalTax;
                            $statusItem['ordertotaldiscount'] = $orderTotalDiscount;
                            $statusItem['ordergiftcardamount'] = $orderGiftCardAmount;
                            $statusItem['ordertotaltopay'] = $orderTotalToPay;
                            $statusItem['paymentmethodcode'] = $paymentMethodCode;
                            $statusItem['paymentgatewaycode'] = $paymentGatewayCode;
                            $statusItem['paymentgatewaysubcode'] = $paymentGatewaySubCode;
                            $statusItem['pricesincludetax'] = $pricesIncludeTax;
                            $statusItem['shippingdiscountvalue'] = $shippingTotalDiscount;
                            $statusItem['discountname'] = $discountName;
                            $statusItem['source'] = $source;
							$statusItem['ordertotalsellbeforediscount'] = $orderTotalSellBeforeDiscount;
							$statusItem['ordertotalitemsellwithtax'] = $orderTotalItemSellWithTax;
							$statusItem['orderfootertotalwithtax'] = $orderFooterTotalWithTax;
							$statusItem['orderfootertotalnotax'] = $orderFooterTotalNoTax;
							$statusItem['orderfootertotalnotaxnodiscount'] = $orderFooterTotalNoTaxNoDiscount;
							$statusItem['orderfootertaxratesequal'] = $orderFooterTaxRatesEqual;
							$statusItem['orderfootersubtotal'] = $orderFooterSubtotal;
							$statusItem['orderfootertotal'] = $orderFooterTotal;
							$statusItem['orderfootertotaltax'] = $orderFooterTotalTax;
							$statusItem['orderfooterdiscountvalue'] = $orderFooterDiscountValue;
							$statusItem['productcollectionorigownercode'] = $productCollectionOrigOwnerCode;
							$statusItem['parentorderitemid'] = $parentOrderItemID;
							$statusItem['orderpricingengineversion'] = $orderPricingEngineVersion;
							$statusItem['projectlsdata'] = $projectLSData;

                            $resultArray[] = $statusItem;
                        }
                    }
                    else
                    {
                        // could not execute statement
                        $result = 'str_DatabaseError';
                        $resultParam = 'getOrderStatus execute ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not bind result
                    $result = 'str_DatabaseError';
                    $resultParam = 'getOrderStatus bind result ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $result = 'str_DatabaseError';
                $resultParam = 'getOrderStatus prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'getOrderStatus connect ' . $dbObj->error;
        }

        return $resultArray;
    }


    /**
     * Retrieves a list of output devices from the database that are available to the production site identified in the session
     *
     * @static
     *
     * @return array
     *   the result array will contain the list of output devices to be echo'd back to the calling application
     *
     * @author Kevin Gale
     * @since Version 1.0.0
     */
    static function getOutputDevices()
    {
        global $gSession;

        $resultArray = Array();

        $sqlStatement = 'SELECT `id`, `owner`, `code`, `localcode`, `name`, `type`, `epwaccountdetails`, `epwurl`, `epwurlversion`, `epwworkflowcode`,
        				`epwworkflowname`, `epwworkflowcompletionstatus`, `pathmac`, `pathwin`, `pathserver`, `copyfiles`, `additionalsettings`, `active`
                        FROM `OUTPUTDEVICES` WHERE `owner` = ?';

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare($sqlStatement))
            {
                if ($stmt->bind_param('s', $gSession['userdata']['userowner']))
                {
                    if ($stmt->bind_result($recordID, $outputDeviceOwner, $outputDeviceCode, $outputDeviceLocalCode, $outputDeviceName,
                                    $outputDeviceType, $outputDeviceAccountDetails, $outputDeviceEPWURL, $outputDeviceEPWURLVersion,
                                    $outputDeviceWorkflowCode, $outputDeviceWorkflowName, $outputDeviceWorkflowCompletionStatus,
                                    $outputDevicePathMac, $outputDevicePathWin, $outputDevicePathServer, $outputDeviceCopyFiles, $additionalSettings, $isActive))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
                                $outputDeviceItem['id'] = $recordID;
                                $outputDeviceItem['owner'] = $outputDeviceOwner;
                                $outputDeviceItem['code'] = $outputDeviceCode;
                                $outputDeviceItem['localcode'] = $outputDeviceLocalCode;
                                $outputDeviceItem['name'] = $outputDeviceName;
                                $outputDeviceItem['type'] = $outputDeviceType;
                                $outputDeviceItem['epwaccountdetails'] = $outputDeviceAccountDetails;
                                $outputDeviceItem['epwurl'] = $outputDeviceEPWURL;
                                $outputDeviceItem['epwurlversion'] = $outputDeviceEPWURLVersion;
                                $outputDeviceItem['epwworkflowcode'] = $outputDeviceWorkflowCode;
                                $outputDeviceItem['epwworkflowname'] = $outputDeviceWorkflowName;
                                $outputDeviceItem['epwworkflowcompletionstatus'] = $outputDeviceWorkflowCompletionStatus;
                                $outputDeviceItem['pathmac'] = $outputDevicePathMac;
                                $outputDeviceItem['pathwin'] = $outputDevicePathWin;
                                $outputDeviceItem['pathserver'] = $outputDevicePathServer;
                                $outputDeviceItem['copyfiles'] = $outputDeviceCopyFiles;
                                $outputDeviceItem['additionalsettings'] = $additionalSettings;
                                $outputDeviceItem['isactive'] = $isActive;

                                $resultArray[] = $outputDeviceItem;
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

    /**
     * Inserts a new output device into the database based on the POST parameters
     *
     * @static
     *
     * @return array
     *   the result array will contain the new output device data to be echo'd back to the calling application
     *
     * @author Kevin Gale
     * @since Version 1.0.0
     */
    static function outputDeviceAdd()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
        $resultParam = '';
        $recordID = 0;

        $outputDeviceCode = $_POST['code'];
        $outputDeviceLocalCode = $_POST['localcode'];
        $outputDeviceName = $_POST['name'];
        $outputDeviceType = $_POST['type'];
        $outputDeviceAccountDetails = $_POST['epwaccountdetails'];
        $outputDeviceEPWURL = $_POST['epwurl'];
        $outputDeviceEPWURLVersion = $_POST['epwurlversion'];
        $outputDeviceWorkflowCode = $_POST['epwworkflowcode'];
        $outputDeviceWorkflowName = $_POST['epwworkflowname'];
        $outputDeviceWorkflowCompletionStatus = $_POST['epwworkflowcompletionstatus'];
        $outputDevicePathMac = $_POST['pathmac'];
        $outputDevicePathWin = $_POST['pathwin'];
        $outputDevicePathServer = $_POST['pathserver'];
        $outputDeviceCopyFiles = $_POST['copyfiles'];
        $outputDevicePathActive = $_POST['active'];
        $languageCode = $_POST['langcode'];
        $additionalSettings = UtilsObj::getPOSTParam('additionalsettings', '');

        if (($outputDeviceCode != '') && ($outputDeviceName != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('INSERT INTO `OUTPUTDEVICES` (`id`, `datecreated`, `owner`, `code`, `localcode`, `name`, `type`, `epwaccountdetails`,
                							`epwurl`, `epwurlversion`, `epwworkflowcode`, `epwworkflowname`, `epwworkflowcompletionstatus`, `pathmac`, `pathwin`,
                							`pathserver`, `copyfiles`, `additionalsettings`, `active`)
				                            VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
                {
                    if ($stmt->bind_param('ssssisssssisssisi', $gSession['userdata']['userowner'], $outputDeviceCode, $outputDeviceLocalCode,
                                    $outputDeviceName, $outputDeviceType, $outputDeviceAccountDetails, $outputDeviceEPWURL, $outputDeviceEPWURLVersion,
                                    $outputDeviceWorkflowCode, $outputDeviceWorkflowName, $outputDeviceWorkflowCompletionStatus,
                                    $outputDevicePathMac, $outputDevicePathWin, $outputDevicePathServer, $outputDeviceCopyFiles, $additionalSettings, $outputDevicePathActive))
                    {
                        if ($stmt->execute())
                        {
                            $recordID = $dbObj->insert_id;

                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                    $gSession['username'], 0, 'ADMIN', 'OUTPUTDEVICE-ADD', $recordID . ' ' . $outputDeviceCode, 1);
                        }
                        else
                        {
                            // could not execute statement
                            // first check for a duplicate key (output device code)
                            if ($stmt->errno == 1062)
                            {
                                $result = 'str_ErrorOutputDeviceExists';
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'outputDeviceAdd execute ' . $dbObj->error;
                            }
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'outputDeviceAdd bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'outputDeviceAdd prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'outputDeviceAdd connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $recordID;
        $resultArray['langcode'] = $languageCode;

        return $resultArray;
    }

    /**
     * Updated an output device database record based on the POST parameters
     *
     * @static
     *
     * @return array
     *   the result array will contain the updated output device data to be echo'd back to the calling application
     *
     * @author Kevin Gale
     * @since Version 1.0.0
     */
    static function outputDeviceEdit()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
        $resultParam = '';

        $outputDeviceID = $_POST['id'];
        $outputDeviceCode = $_POST['code'];
        $outputDeviceLocalCode = $_POST['localcode'];
        $outputDeviceName = $_POST['name'];
        $outputDeviceType = $_POST['type'];
        $outputDeviceAccountDetails = $_POST['epwaccountdetails'];
        $outputDeviceEPWURL = $_POST['epwurl'];
        $outputDeviceEPWURLVersion = $_POST['epwurlversion'];
        $outputDeviceWorkflowCode = $_POST['epwworkflowcode'];
        $outputDeviceWorkflowName = $_POST['epwworkflowname'];
        $outputDeviceWorkflowCompletionStatus = $_POST['epwworkflowcompletionstatus'];
        $outputDevicePathMac = $_POST['pathmac'];
        $outputDevicePathWin = $_POST['pathwin'];
        $outputDevicePathServer = $_POST['pathserver'];
        $outputDeviceCopyFiles = $_POST['copyfiles'];
        $outputDevicePathActive = $_POST['active'];
        $languageCode = $_POST['langcode'];
        $additionalSettings = UtilsObj::getPOSTParam('additionalsettings', '');

        if (($outputDeviceID != '') && ($outputDeviceCode != '') && ($outputDeviceName != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('UPDATE `OUTPUTDEVICES` SET `name` = ?, `type` = ?, `epwaccountdetails` = ?, `epwurl` = ?, `epwurlversion` = ?,
                							`epwworkflowcode` = ?, `epwworkflowname` = ?, `epwworkflowcompletionstatus` = ?, `pathmac` = ?, `pathwin` = ?,
                							`pathserver` = ?, `copyfiles` = ?, `additionalsettings` = ?, `active` = ?
				                            WHERE `code` = ?'))
                {
                    if ($stmt->bind_param('sisssssisssisis', $outputDeviceName, $outputDeviceType, $outputDeviceAccountDetails, $outputDeviceEPWURL,
                    				$outputDeviceEPWURLVersion, $outputDeviceWorkflowCode, $outputDeviceWorkflowName, $outputDeviceWorkflowCompletionStatus,
                    				$outputDevicePathMac, $outputDevicePathWin, $outputDevicePathServer, $outputDeviceCopyFiles, $additionalSettings, $outputDevicePathActive,
                    				$outputDeviceCode))
                    {
                        if ($stmt->execute())
                        {
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                    $gSession['username'], 0, 'ADMIN', 'OUTPUTDEVICE-UPDATE', $outputDeviceID . ' ' . $outputDeviceCode, 1);
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'outputDeviceEdit execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'outputDeviceEdit bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'outputDeviceEdit prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'outputDeviceEdit connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $outputDeviceID;
        $resultArray['langcode'] = $languageCode;

        return $resultArray;
    }

    /**
     * Deletes the output format database record identified by the POST parameters
     *
     * @static
     *
     * @return array
     *   the result array will contain the result of the deletion to be echo'd back to the calling application
     *
     * @author Kevin Gale
     * @since Version 1.0.0
     */
    static function outputDeviceDelete()
	{
	    global $gSession;

		$resultArray = Array();
		$result = '';
		$resultParam = '';

		$languageCode = $_POST['langcode'];
		$outputDeviceID = $_POST['id'];
		$outputDeviceCode = $_POST['code'];
		if ($outputDeviceID)
		{
			$dbObj = DatabaseObj::getGlobalDBConnection();
			if ($dbObj)
			{
				// first make sure the output device hasn't been used
				$canDelete = true;
				if ($stmt = $dbObj->prepare('SELECT `id` FROM `OUTPUTFORMATS` WHERE (`jobticketdefaultoutputdevicecode` = ?) OR (`pagesdefaultoutputdevicecode` = ?)
					OR (`cover1defaultoutputdevicecode` = ?) OR (`cover2defaultoutputdevicecode` = ?) OR (`xmldefaultoutputdevicecode` = ?)'))
				{
					if ($stmt->bind_param('sssss', $outputDeviceCode, $outputDeviceCode, $outputDeviceCode, $outputDeviceCode, $outputDeviceCode))
					{
						if ($stmt->bind_result($recordID))
						{
						   if ($stmt->execute())
						   {
								if ($stmt->fetch())
								{
									$result = 'str_ErrorUsedInOutputFormat';
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
					if ($stmt = $dbObj->prepare('DELETE FROM `OUTPUTDEVICES` WHERE `id` = ?'))
					{
						if ($stmt->bind_param('i', $outputDeviceID))
						{
							if ($stmt->execute())
							{
							    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                        'ADMIN', 'OUTPUTDEVICE-DELETE', $outputDeviceID . ' ' . $outputDeviceCode, 1);
							}

						}
						$stmt->free_result();
						$stmt->close();
					}
				}
			}
		}

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;
		$resultArray['langcode'] = $languageCode;

		return $resultArray;
	}

    /**
     * Helper function to update the order item database record active status based on the order id provided in the POST parameters
     *
     * @param  $_POST['orderid'] Order ID
     * @param  $_POST['itemactivestatus'] Order item active status
     * @param  $_POST['userid'] User performing the action
     * @global $gSession Global session object
     *
     * @author Kevin Gale
     * @version 3.0.0
     * @since Version 3.0.0
     */
    static function updateOrderActiveStatusPOST()
    {
        global $gSession;

        $orderID = $_POST['orderid'];
        $activeStatus = (int) $_POST['orderactivestatus'];
        $userID = $gSession['userid'];

        self::updateOrderActiveStatus($orderID, $userID, $activeStatus);
    }

    /**
     * Updates the order header database record status based on the order item id provided in the POST parameters
     *
     * @param  $pOrderID int Order ID
     * @param  $pUserID int User ID
     * @param  $pActiveStatus int Order item active status
     *
     * @author Kevin Gale
     * @version 3.0.0
     * @since Version 1.0.0
     */
    static function updateOrderActiveStatus($pOrderID, $pUserID, $pActiveStatus)
    {
        $result = '';
        $resultParam = '';

        $orderItemArray = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            // retrieve a list of items we are going to update
            if ($result == '')
            {
                if ($stmt = $dbObj->prepare('SELECT `oi`.`id`, `oh`.`temporder` FROM `ORDERITEMS` oi JOIN `ORDERHEADER` oh ON `oh`.`id` = `oi`.`orderid` WHERE `orderid` = ?'))
                {
                    if ($stmt->bind_param('i', $pOrderID))
                    {
                        if ($stmt->bind_result($id, $tempOrder))
                        {
                            if ($stmt->execute())
                            {
                                while($stmt->fetch())
                                {
                                    $itemArray = Array();
                                    $itemArray['id'] = $id;
                                    $itemArray['temporder'] = $tempOrder;
                                    $orderItemArray[] = $itemArray;
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'updateorderactivestatus execute ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            // could not bind result
                            $result = 'str_DatabaseError';
                            $resultParam = 'updateorderactivestatus bindresult ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind params
                        $result = 'str_DatabaseError';
                        $resultParam = 'updateorderactivestatus bindparams ' . $dbObj->error;
                    }

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
            }

            // if we have received no error update the order items
            if ($result == '')
            {
                if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `active` = ?, `activetimestamp` = now(), `activeuserid` = ? WHERE `orderid` = ?'))
                {
                    if ($stmt->bind_param('iii', $pActiveStatus, $pUserID, $pOrderID))
                    {
                        if (!$stmt->execute())
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'updateorderactivestatus execute2 ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind params
                        $result = 'str_DatabaseError';
                        $resultParam = 'updateorderactivestatus bindparams2 ' . $dbObj->error;
                    }


                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
            }

            $dbObj->close();


            // if we have received no error determine the events we need to trigger and then trigger them
            if ($result == '')
            {
                $itemCount = count($orderItemArray);
                for($i = 0; $i < $itemCount; $i++)
                {
                    $itemArray = $orderItemArray[$i];

                    $triggerAction = '';
                    if ($pActiveStatus == TPX_ORDER_STATUS_IN_PROGRESS)
                    {
                        $triggerAction = TPX_TRIGGER_ORDER_ACTIVATE;
                    }
                    elseif ($pActiveStatus == TPX_ORDER_STATUS_CANCELLED)
                    {
                        if ($itemArray['temporder'] == 0)
                        {
                            $triggerAction = TPX_TRIGGER_ORDER_CANCELLED;
                        }
                        else
                        {
                            $triggerAction = TPX_TRIGGER_TEMP_ORDER_CANCELLED;
                        }
                    }
                    elseif ($pActiveStatus == TPX_ORDER_STATUS_COMPLETED)
                    {
                        $triggerAction = TPX_TRIGGER_ORDER_COMPLETE;
                    }

                    if ($triggerAction != '')
                    {
                        DataExportObj::EventTrigger($triggerAction, 'ORDERITEM', $itemArray['id'], $pOrderID);
                    }
                }
            }
        }
    }

    /**
     * Helper function to update the order item database record active status based on the order item id provided in the POST parameters
     *
     * @param  $_POST['orderitemidlist'] Comma separated list of order item id's
     * @param  $_POST['itemactivestatus'] Order item active status
     * @param  $_POST['userid'] User performing the action
     * @global $gSession Global session object
     *
     * @author Kevin Gale
     * @version 3.0.0
     * @since Version 3.0.0
     */
    static function updateItemActiveStatusPOST()
    {
        global $gSession;

        $orderItemIDList = (string) $_POST['orderitemidlist'];
        $itemActiveStatus = (int) $_POST['itemactivestatus'];
        $userID = $gSession['userid'];

        self::updateItemActiveStatus($orderItemIDList, $userID, $itemActiveStatus);
    }

    /**
     * Updates the order header database record status based on the order item id provided in the parameters
     *
     * @param  $pOrderItemIDList string Comma separated list of order item id's
     * @param  $pUserID int User ID
     * @param  $pItemActiveStatus int Order item active status
     *
     * @author Kevin Gale
     * @version 3.0.0
     * @since Version 1.0.0
     */
    static function updateItemActiveStatus($pOrderItemIDList, $pUserID, $pItemActiveStatus)
    {
        $result = '';
        $resultParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            //get a list of companion order item records ids based of the parent order item record ids
            $companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemIDList);
			$parentOrderItemIDList = explode(',', $pOrderItemIDList);

			// append the companion order item record id's to the parent array so that they can all be updated correctly.
			$orderItemIDArray = array_merge($parentOrderItemIDList, $companionOrderItemRecordIDArray);

            $itemCount = count($orderItemIDArray);
            $orderID = 0;

            for ($i = 0; $i < $itemCount; $i++)
            {
                UtilsObj::resetPHPScriptTimeout(10);

                $orderItemID = $orderItemIDArray[$i];

                $tempOrder = 0;

                if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `active` = ?, `activetimestamp` = now(), `activeuserid` = ? WHERE `id` = ?'))
                {
                    if ($stmt->bind_param('iii', $pItemActiveStatus, $pUserID, $orderItemID))
                    {
                        if (!$stmt->execute())
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'updateitemactivestatus execute1 ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind params
                        $result = 'str_DatabaseError';
                        $resultParam = 'updateitemactivestatus bindparams1 ' . $dbObj->error;
                    }


                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }

                // if we have received no error determine if the item was temporary
                if ($result == '')
                {
                    $stmt = $dbObj->prepare('SELECT `oh`.`id`, `oh`.`temporder`
                                                FROM `ORDERITEMS` oi
                                                    JOIN `ORDERHEADER` oh ON `oh`.`id` = `oi`.`orderid`
                                                WHERE `oi`.`id` = ?');
                    if ($stmt)
                    {
                        if ($stmt->bind_param('i', $orderItemID))
                        {
                            if ($stmt->execute())
                            {
                                if ($stmt->store_result())
                                {
                                    if ($stmt->num_rows > 0)
                                    {
                                        if ($stmt->bind_result($orderID, $tempOrder))
                                        {
                                            $stmt->fetch();
                                        }
                                        else
                                        {
                                            // could not bind result
                                            $result = 'str_DatabaseError';
                                            $resultParam = 'updateitemactivestatus bindresult2 ' . $dbObj->error;
                                        }
                                    }
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'updateitemactivestatus store result 2' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'updateitemactivestatus execute2 ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            // could not bind params
                            $result = 'str_DatabaseError';
                            $resultParam = 'updateitemactivestatus bindparams2 ' . $dbObj->error;
                        }

                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                }
                else
                {
                    break;
                }

                // if we have received no error determine the event we need to trigger and then trigger it
                if ($result == '')
                {
                    $triggerAction = '';
                    if ($pItemActiveStatus == TPX_ORDER_STATUS_IN_PROGRESS)
                    {
                        $triggerAction = TPX_TRIGGER_ORDER_ACTIVATE;
                    }
                    elseif ($pItemActiveStatus == TPX_ORDER_STATUS_CANCELLED)
                    {
                        if ($tempOrder == 0)
                        {
                            $triggerAction = TPX_TRIGGER_ORDER_CANCELLED;
                        }
                        else
                        {
                            $triggerAction = TPX_TRIGGER_TEMP_ORDER_CANCELLED;
                        }
                    }
                    elseif ($pItemActiveStatus == TPX_ORDER_STATUS_COMPLETED)
                    {
                        $triggerAction = TPX_TRIGGER_ORDER_COMPLETE;
                    }

                    if ($triggerAction != '')
                    {
                        DataExportObj::EventTrigger($triggerAction, 'ORDERITEM', $orderItemID, $orderID);
                    }
                }
                else
                {
                    break;
                }
            }

            $dbObj->close();
        }
    }

    /**
     * Helper function to update the order payment status based on the POST parameters
     *
     * @param  $_POST['orderidlist'] Comma separated list of order header id's
     * @param  $_POST['paymentreceived'] Payment received status
     * @param  $_POST['paymentreceiveddate'] Payment received date
     * @global $gSession Global session object

     * @author Loc Dinh
     * @since Version 3.0.0
     */
    static function updateOrderPaymentStatusPOST()
    {
        global $gSession;

        $orderIDList = (string) $_POST['orderidlist'];
        $orderPaymentReceived = (int) $_POST['paymentreceived'];
        $orderPaymentReceivedDate = $_POST['paymentreceiveddate'];
        $userID = $gSession['userid'];

        self::updateOrderPaymentStatus($orderIDList, $userID, $orderPaymentReceived, $orderPaymentReceivedDate);
    }

    /**
     * Updates the order payment received status based on the specified parameters
     *
     * @param  $pOrderIDList string Comma separated list of order item id's
     * @param  $pUserID int User ID
     * @param  $pOrderPaymentReceived int Payment received status
     * @param  $pOrderPaymentReceivedDate string Payment received date
     *
     * @author Kevin Gale
     * @since Version 1.0.0
     */
    static function updateOrderPaymentStatus($pOrderIDList, $pUserID, $pOrderPaymentReceived, $pOrderPaymentReceivedDate)
    {
        $rowAffected = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('UPDATE `ORDERHEADER` SET `paymentreceived` = ?, `paymentreceivedtimestamp` = now(), `paymentreceiveddate` = ?, `paymentreceiveduserid` = ? WHERE `id` = ?'))
            {
                $orderIDArray = explode(',', $pOrderIDList);
                $itemCount = count($orderIDArray);

                for ($i = 0; $i < $itemCount; $i++)
                {
                    UtilsObj::resetPHPScriptTimeout(10);

                    $orderID = $orderIDArray[$i];

                    if ($stmt->bind_param('isii', $pOrderPaymentReceived, $pOrderPaymentReceivedDate, $pUserID, $orderID))
                    {
                        $stmt->execute();
                        $queryResult = self::getQueryResultInfo($dbObj->info);
                        $rowAffected += $queryResult['matched'];

						// Trigger order paid when payment received.
						if ($pOrderPaymentReceived)
						{
							// Generate the trigger.
							DataExportObj::EventTrigger(TPX_TRIGGER_ORDER_PAID, 'ORDER', $orderID, $orderID);
						}
                    }
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }

            $dbObj->close();
        }

        return $rowAffected;
    }

    /**
     * Re-routes the orders based on the POST parameters
     *
     * @static
     *
     * @return array
     *   the result array will contain the list of re-routed items to be echo'd back to the calling application
     *
     * @author Kevin Gale
     * @since Version 3.0.0
     */
    static function reRouteItems()
    {
        // include the email creation module
        require_once('../Utils/UtilsEmail.php');

        global $gConstants;
        global $gSession;

        $resultArray = Array();
        $result = '';
        $resultParam = '';
        $reRoutedItems = '';
        $reRoutedOrderNumbers = '';
        $newCompanyCode = '';
        $siteKey = '';
        $productionSiteType = 0;

        $orderItemList = $_POST['orderitemidlist'];
        $newSiteCode = $_POST['newowner'];
        $reRouteReason = $_POST['reroutereason'];

        $userAccountArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
        $result = $userAccountArray['result'];
        if ($result == '')
        {
            $reRouteUserName = $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'];

            if ($newSiteCode != '')
            {
                $siteArray = DatabaseObj::getSiteFromCode($newSiteCode);
                $result = $siteArray['result'];
                $newCompanyCode = $siteArray['companycode'];
                $siteKey = $siteArray['productionsitekey'];
                $productionSiteType = $siteArray['productionsitetype'];
            }
        }

        if ($result == '')
        {
            //get a list of companion order item records ids based of the parent order item record ids
			$companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($orderItemList);
			$parentOrderItemIDList = explode(',', $orderItemList);

			// append the companion order item record id's to the parent array so that they can all be updated correctly.
			$itemIDArray = array_merge($parentOrderItemIDList, $companionOrderItemRecordIDArray);
            $itemCount = count($itemIDArray);

            if ($itemCount > 0)
            {
                $dbObj = DatabaseObj::getGlobalDBConnection();
                if ($dbObj)
                {
                    // prepare a statement to get the order header details
                    if ($stmt = $dbObj->prepare('SELECT oh.id, oh.ordernumber
                        FROM `ORDERHEADER` oh
        		        LEFT JOIN `ORDERITEMS` oi ON oh.id = oi.orderid WHERE oi.id = ?'))
                    {
                        // prepare the statement to change the owner site
                        if ($stmt2 = $dbObj->prepare('UPDATE `ORDERITEMS` SET `currentcompanycode` = ?, `currentowner` = ?, `ownerorderkey` = ?, `currentownertype` = ? WHERE `id` = ?'))
                        {
                            // prepare the statement to reset the file status back to ready to download
                            // this is down as a separate statement as we only update if the files have been received
                            if ($stmt3 = $dbObj->prepare('UPDATE `ORDERITEMS` SET `decryptuserid` = 0, `converttimestamp` = \'\', `convertuserid` = 0, `convertoutputformatcode` = \'\',
                                            `jobticketoutputfilename` = \'\', `pagesoutputfilename` = \'\', `cover1outputfilename` = \'\', `cover2outputfilename` = \'\', `xmloutputfilename` = \'\',
                                            `jobticketoutputdevicecode` = \'\', `pagesoutputdevicecode` = \'\', `cover1outputdevicecode` = \'\', `cover2outputdevicecode` = \'\', `xmloutputdevicecode` = \'\',
                                            `jobticketoutputsubfoldername` = \'\', `pagesoutputsubfoldername` = \'\', `cover1outputsubfoldername` = \'\', `cover2outputsubfoldername` = \'\', `xmloutputsubfoldername` = \'\',
                                            `outputtimestamp` = \'\', `outputuserid` = 0, `jobticketepwpartid` = \'\', `pagesepwpartid` = \'\', `cover1epwpartid` = \'\', `cover2epwpartid` = \'\',
				                            `jobticketepwsubmissionid` = \'\', `pagesepwsubmissionid` = \'\', `cover1epwsubmissionid` = \'\', `cover2epwsubmissionid` = \'\',
                                            `jobticketepwcompletionstatus` = 0, `pagesepwcompletionstatus` = 0, `cover1epwcompletionstatus` = 0, `cover2epwcompletionstatus` = 0,
                                            `jobticketepwstatus` = 0, `pagesepwstatus` = 0, `cover1epwstatus` = 0, `cover2epwstatus` = 0, `status` = 1, `statusdescription` = "",
                                            `statustimestamp` = NOW() WHERE (`id` = ?) AND (`status` > 0)'))
                            {
                                for($i = 0; $i < $itemCount; $i++)
                                {
                                    $orderItemID = $itemIDArray[$i];

                                    if ($newSiteCode == '')
                                    {
                                        $ownerOrderKey = '';
                                    }
                                    else
                                    {
                                        $ownerOrderKey = $newSiteCode . $orderItemID . $siteKey;
                                        $ownerOrderKey = md5($ownerOrderKey);
                                    }

                                    // get the order info for the order item
                                    if ($stmt->bind_param('i', $orderItemID))
                                    {
                                        if ($stmt->bind_result($orderID, $orderNumber))
                                        {
                                            if ($stmt->execute())
                                            {
                                                if ($stmt->fetch())
                                                {
                                                    $stmt->free_result();

                                                    // update the owner
                                                    if ($stmt2->bind_param('sssii', $newCompanyCode, $newSiteCode, $ownerOrderKey,
                                                                    $productionSiteType, $orderItemID))
                                                    {
                                                        if ($stmt2->execute())
                                                        {
                                                            // update the file status
                                                            if ($stmt3->bind_param('i', $orderItemID))
                                                            {
                                                                if ($stmt3->execute())
                                                                {
                                                                    // add the order item to the list that we have successfully re-routed
                                                                    $reRoutedOrderNumbers .= $orderNumber . ', ';

                                                                    // we do not want to send back the order item id back to production if the item is a companion
                                                                    if (! in_array($orderItemID, $companionOrderItemRecordIDArray))
                                                                    {
                                                                    	$reRoutedItems .= $orderItemID . ',';
																	}

                                                                    // update the activity log
                                                                    DatabaseObj::updateActivityLog2($dbObj, 0, $orderID,
                                                                            $gSession['userid'], $userAccountArray['login'],
                                                                            $reRouteUserName, 0, 'ORDER', 'ORDER-REROUTE',
                                                                            $newSiteCode . "\n" . $reRouteReason, 1);

                                                                    DataExportObj::EventTrigger(TPX_TRIGGER_ORDER_REROUTE, 'ORDERITEM', $orderItemID,
                                                                            $orderID, $newCompanyCode);
                                                                }
                                                                else
                                                                {
                                                                    // could not execute statement
                                                                    $result = 'str_DatabaseError';
                                                                    $resultParam = 'reRouteItems update execute3 ' . $dbObj->error;
                                                                    break;
                                                                }

                                                                $stmt3->free_result();
                                                            }
                                                            else
                                                            {
                                                                // could not bind parameters
                                                                $result = 'str_DatabaseError';
                                                                $resultParam = 'reRouteItems bind params3 ' . $dbObj->error;
                                                                break;
                                                            }
                                                        }
                                                        else
                                                        {
                                                            // could not execute statement
                                                            $result = 'str_DatabaseError';
                                                            $resultParam = 'reRouteItems update execute2 ' . $dbObj->error;
                                                            break;
                                                        }

                                                        $stmt2->free_result();
                                                    }
                                                }
                                                else
                                                {
                                                    // could not fetch the result
                                                    $result = 'str_DatabaseError';
                                                    $resultParam = 'reRouteItems update fetch ' . $dbObj->error;
                                                    break;
                                                }
                                            }
                                            else
                                            {
                                                // could not execute statement
                                                $result = 'str_DatabaseError';
                                                $resultParam = 'reRouteItems update execute ' . $dbObj->error;
                                                break;
                                            }
                                        }
                                        else
                                        {
                                            // could not bind result
                                            $result = 'str_DatabaseError';
                                            $resultParam = 'reRouteItems bind result ' . $dbObj->error;
                                            break;
                                        }
                                    }
                                    else
                                    {
                                        // could not bind parameters
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'reRouteItems bind params ' . $dbObj->error;
                                        break;
                                    }
                                }

                                $stmt3->free_result();
                                $stmt3->close();
                                $stmt3 = null;
                            }

                            $stmt2->free_result();
                            $stmt2->close();
                            $stmt2 = null;
                        }

                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }

                    $dbObj->close();
                }
            }
        }

        // trim off any trailing separators
        $reRoutedOrderNumbers = substr($reRoutedOrderNumbers, 0, strlen($reRoutedOrderNumbers) - 2);
        $reRoutedItems = substr($reRoutedItems, 0, strlen($reRoutedItems) - 1);

        // finally send the re-routing confirmation if any orders were re-routed
        $brandSettings = DatabaseObj::getBrandingFromCode('');
        if (($reRoutedOrderNumbers != '') && ($brandSettings['smtpproductionactive'] == 1))
        {
            $emailName = $siteArray['smtpproductionname'];
            $emailAddress = $siteArray['smtpproductionaddress'];

			// only send the email if we have an email address (name is optional)
            if ($emailAddress != '')
            {
                $emailObj = new TaopixMailer();
                $emailObj->sendTemplateEmail('admin_orderrerouted', '', '', '', $gConstants['defaultlanguagecode'], $emailName,
                        $emailAddress, '', '', 0,
                        Array(
                    'user' => $reRouteUserName,
                    'reason' => $reRouteReason,
                    'orders' => $reRoutedOrderNumbers), '', ''
                );
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['items'] = $reRoutedItems;

        return $resultArray;
    }

	/**
	* Helper function to update the order item database record active status based on the order item id provided in the POST parameters
	*
	* @param  $_POST['orderitemidlist'] Comma separated list of order item id's
	* @param  $_POST['itemstatus'] New item status
	* @param  $_POST['itemstatusdescription'] New item status description
	* @param  $_POST['itemuploaddatatype'] New item upload data type or -1 if not relevant
	* @param  $_POST['itemuploadmethod'] New item upload method or -1 if not relevant (must be supplied if itemuploaddatatype supplied)
	*
	* @author Kevin Gale
	* @version 3.0.0
	* @since Version 3.0.0
	*/
    static function updateItemStatusPOST()
    {
        $orderItemIDList = (string) $_POST['orderitemidlist'];
        $itemStatus = (int) $_POST['itemstatus'];
        $itemDescription = $_POST['itemstatusdescription'];

        self::updateItemStatus($orderItemIDList, $itemStatus, $itemDescription);
    }

    /**
	* Updates the order item database record status based on the specified parameters
	*
	* @param  $pOrderItemIDList string Comma separated list of order item id's
	* @param  $pItemStatus int New item status
	* @param  $pItemDescription string New item status description
	* @param  $pUploadDataType int New item upload data type or -1 if it should not be changed
	* @param  $pUploadMethod int New item upload method or -1 if it should not be changed (must be supplied if pUploadDataType supplied)
	*
	* @author Kevin Gale
	* @since Version 1.0.0
	*/
    static function updateItemStatus($pOrderItemIDList, $pItemStatus, $pItemDescription)
    {
        $rowAffected = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
			$bindResult = $stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `status` = ?, `statusdescription` = ?, `statustimestamp` = NOW() WHERE `id` = ?');

            if ($bindResult)
            {
                //get a list of companion order item records ids based of the parent order item record ids
            	$companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemIDList);
				$parentOrderItemIDList = explode(',', $pOrderItemIDList);

				// append the companion order item record id's to the parent array so that they can all be updated correctly.
				$orderItemIDArray = array_merge($parentOrderItemIDList, $companionOrderItemRecordIDArray);
                $itemCount = count($orderItemIDArray);

                for ($i = 0; $i < $itemCount; $i++)
                {
                    UtilsObj::resetPHPScriptTimeout(10);

                    $orderItemID = $orderItemIDArray[$i];
    				$bindResult = $stmt->bind_param('isi', $pItemStatus, $pItemDescription, $orderItemID);

                    if ($bindResult)
                    {
                        $stmt->execute();
                        $queryResult = self::getQueryResultInfo($dbObj->info);
                        $rowAffected += $queryResult['matched'];
                    }
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }

            $dbObj->close();
        }

        return $rowAffected;
    }

    /**
     * Helper function to update the order item files received status based on the POST parameters
     *
     * @param  $_POST['orderitemid'] Order item ID
     * @param  $_POST['itemstatus'] New order status
     * @param  $_POST['itemstatusdescription'] Status description
     * @param  $_POST['filesreceiveddate'] Update date
     * @global $gSession Global session object
     *
     * @author Dasha Salo
     * @version 3.0.0
     * @since Version 3.0.0
     */
    static function updateItemFilesReceivedStatusPOST()
    {
        global $gSession;

        $orderItemID = $_POST['orderitemid'];
        $itemStatus = (int) $_POST['itemstatus'];
        $itemDescription = $_POST['itemstatusdescription'];
        $filesReceivedDate = $_POST['filesreceiveddate'];
        $userId = $gSession['userid'];

        self::updateItemFilesReceivedStatus($orderItemID, $itemStatus, $itemDescription, $filesReceivedDate, $userId);
    }

    /**
     * Updates the order item files received status based on parameters specified
     *
     * @param  $orderItemID int Order item ID
     * @param  $itemStatus int New order status
     * @param  $itemDescription string Status description
     * @param  $filesReceivedDate string Update date
     * @param  $userId int User ID
     *
     * @author Kevin Gale
     * @since Version 1.0.0
     * @version 3.0.0
     */
    static function updateItemFilesReceivedStatus($pOrderItemID, $pItemStatus, $pItemDescription, $pFilesReceivedDate, $pUserId)
    {
        $rowAffected = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
			$orderItemIDList = $pOrderItemID;

            //get a list of companion order item records ids based of the parent order item record ids
            $companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemID);

			// append the companion orderitem id's to the parent order item id
			if (count($companionOrderItemRecordIDArray) > 0)
			{
				$orderItemIDList .= ',' . implode(',', $companionOrderItemRecordIDArray);
			}

            if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `status` = ?, `statusdescription` = ?, `statustimestamp` = now(),
			    `canmodify` = 0, `canupload` = 0, `canuploadproductcodeoverride` = 0, `canuploadpagecountoverride` = 0 WHERE `id` IN (' . $orderItemIDList .')'))
            {
                if ($stmt->bind_param('is', $pItemStatus, $pItemDescription))
                {
                    if ($stmt->execute())
                    {
                        // we have updated the status now set the timestamp if it hasn't already been set

                        $stmt->free_result();
                        $stmt->close();

                        if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `filesreceivedtimestamp` = ?, `filesreceiveduserid` = ?
							WHERE `id` IN (' . $orderItemIDList .') AND (`filesreceivedtimestamp` IS NULL)'))
                        {
                            if ($stmt->bind_param('si', $pFilesReceivedDate, $pUserId))
                            {
                                $stmt->execute();
                                $queryResult = self::getQueryResultInfo($dbObj->info);
                                $rowAffected += $queryResult['matched'];
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

        return $rowAffected;
    }

    /**
     * Updates Item Can Modify Project Status based on the POST parameters
     *
     * @static
     *
     * @author Kevin Gale
     * @since Version 3.0.0
     */
    static function updateItemCanModifyStatusPOST()
    {
        $orderItemID = $_POST['orderitemid'];
        $canModify = $_POST['canmodify'];

        self::updateItemCanModifyStatus($orderItemID, $canModify);
    }

    static function updateItemCanModifyStatus($pOrderItemID, $pCanModify)
    {
        $rowAffected = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $orderItemIDList = $pOrderItemID;

            //get a list of companion order item records ids based of the parent order item record ids
            $companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemID);

			// append the companion orderitem id's to the parent order item id
			if (count($companionOrderItemRecordIDArray) > 0)
			{
				$orderItemIDList .= ',' . implode(',', $companionOrderItemRecordIDArray);
			}

            // update the canmodify status for projects
            // if this is set to true we also set canupload to true
            if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `canmodify` = ?, `canupload` = IF (? = 1, 1, `canupload`) WHERE `id` IN (' . $orderItemIDList .')'))
            {
                if ($stmt->bind_param('ii', $pCanModify, $pCanModify))
                {
                    $stmt->execute();
                    $queryResult = self::getQueryResultInfo($dbObj->info);
                    $rowAffected += $queryResult['matched'];
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }

        return $rowAffected;
    }

    static function updateItemCanUploadFilesStatusPOST()
    {
        $orderItemID = $_POST['orderitemid'];
        $canUploadFiles = $_POST['canuploadfiles'];

        self::updateItemCanUploadFilesStatus($orderItemID, $canUploadFiles);
    }

    static function updateItemCanUploadFilesStatus($pOrderItemID, $pCanUploadFiles)
    {
        $rowAffected = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $orderItemIDList = $pOrderItemID;

            //get a list of companion order item records ids based of the parent order item record ids
            $companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemID);

			// append the companion orderitem id's to the parent order item id
			if (count($companionOrderItemRecordIDArray) > 0)
			{
				$orderItemIDList .= ',' . implode(',', $companionOrderItemRecordIDArray);
			}

            if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `canupload` = ? WHERE `id` IN (' . $orderItemIDList .')'))
            {
                if ($stmt->bind_param('i', $pCanUploadFiles))
                {
                    $stmt->execute();
                    $queryResult = self::getQueryResultInfo($dbObj->info);
                    $rowAffected += $queryResult['matched'];
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }

        return $rowAffected;
    }

    static function updateItemCanUploadFilesOverrideProductCodeStatusPOST()
    {
        $orderItemID = $_POST['orderitemid'];
        $overrideProductCode = $_POST['overrideproductcode'];

        self::updateItemCanUploadFilesOverrideProductCodeStatus($orderItemID, $overrideProductCode);
    }

    /**
     * Updates Item Can Upload Files Override Product Code Status based on the POST parameters
     *
     * @static
     *
     * @author Kevin Gale
     * @since Version 3.0.0
     */
    static function updateItemCanUploadFilesOverrideProductCodeStatus($pOrderItemID, $pOverrideProductCode)
    {
        $rowAffected = 0;
        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $orderItemIDList = $pOrderItemID;

            //get a list of companion order item records ids based of the parent order item record ids
            $companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemID);

			// append the companion orderitem id's to the parent order item id
			if (count($companionOrderItemRecordIDArray) > 0)
			{
				$orderItemIDList .= ',' . implode(',', $companionOrderItemRecordIDArray);
			}

            if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `canuploadproductcodeoverride` = ? WHERE `id` IN (' . $orderItemIDList .')'))
            {
                if ($stmt->bind_param('i', $pOverrideProductCode))
                {
                    $stmt->execute();
                    $queryResult = self::getQueryResultInfo($dbObj->info);
                    $rowAffected += $queryResult['matched'];
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }

        return $rowAffected;
    }

    static function updateItemCanUploadFilesOverridePageCountStatusPOST()
    {
        $orderItemID = $_POST['orderitemid'];
        $overridePageCount = $_POST['overridepagecount'];

        self::updateItemCanUploadFilesOverridePageCountStatus($orderItemID, $overridePageCount);
    }

    /**
     * Updates Item Can Upload Files Override Page Count Status based on the POST parameters
     *
     * @static
     *
     * @author Loc Dinh
     * @since Version 3.0.0
     */
    static function updateItemCanUploadFilesOverridePageCountStatus($pOrderItemID, $pOverridePageCount)
    {
        $rowAffected = 0;
        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $orderItemIDList = $pOrderItemID;

            //get a list of companion order item records ids based of the parent order item record ids
            $companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemID);

			// append the companion orderitem id's to the parent order item id
			if (count($companionOrderItemRecordIDArray) > 0)
			{
				$orderItemIDList .= ',' . implode(',', $companionOrderItemRecordIDArray);
			}

            if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `canuploadpagecountoverride` = ? WHERE `id` IN (' . $orderItemIDList .')'))
            {
                if ($stmt->bind_param('i', $pOverridePageCount))
                {
                    $stmt->execute();
                    $queryResult = self::getQueryResultInfo($dbObj->info);
                    $rowAffected += $queryResult['matched'];
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }

        return $rowAffected;
    }

    /**
     * Updates Item Can Upload Files Override Save Status based on the POST parameters
     *
     * @static
     *
     * @author Kevin Gale
     * @since Version 3.0.0
     */
    static function updateItemCanUploadFilesOverrideSaveStatusPOST()
    {
        $orderItemID = $_POST['orderitemid'];
        $overrideSave = $_POST['overridesave'];

        self::updateItemCanUploadFilesOverrideSaveStatus($orderItemID, $overrideSave);
    }

    static function updateItemCanUploadFilesOverrideSaveStatus($pOrderItemID, $pOverrideSave)
    {
        $rowAffected = 0;
        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $orderItemIDList = $pOrderItemID;

            //get a list of companion order item records ids based of the parent order item record ids
            $companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemID);

			// append the companion orderitem id's to the parent order item id
			if (count($companionOrderItemRecordIDArray) > 0)
			{
				$orderItemIDList .= ',' . implode(',', $companionOrderItemRecordIDArray);
			}

            if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `canuploadenablesaveoverride` = ? WHERE `id` IN (' . $orderItemIDList .')'))
            {
                if ($stmt->bind_param('i', $pOverrideSave))
                {
                    $stmt->execute();
                    $queryResult = self::getQueryResultInfo($dbObj->info);
                    $rowAffected += $queryResult['matched'];
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }

        return $rowAffected;
    }

    /**
     * Updates the order item after it has been imported via TAOPIX Production
     *
     * @static
     *
     * @author Kevin Gale
     * @since Version 3.0.0
     */
    static function updateItemImportStatus()
    {
        global $gSession;
		global $ac_config;

        $rowAffected = 0;
        $imported = false;

        $orderItemID = $_POST['orderitemid'];
        $projectStartTime = $_POST['projectstarttime'];
        $projectDuration = $_POST['projectduration'];
        $uploadRef = $_POST['uploadref'];
        $filesReceivedDate = $_POST['filesreceiveddate'];
        $designerAPIVersion = $_POST['designerapiversion'];
        $projectDataType = $_POST['projectdatatype'];
		$cartType = TPX_SHOPPINGCARTTYPE_INTERNAL;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $orderItemIDList = $orderItemID;

            //get a list of companion order item records ids based of the parent order item record ids
            $companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($orderItemID);

			// append the companion orderitem id's to the parent order item id
			if (count($companionOrderItemRecordIDArray) > 0)
			{
				$orderItemIDList .= ',' . implode(',', $companionOrderItemRecordIDArray);
			}

            if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `projectbuildstartdate` = ?, `projectbuildduration` = ?, `status` = ' . TPX_ITEM_STATUS_FILES_RECEIVED . ',
			                            `statusdescription` = "", `statustimestamp` = now(), `canmodify` = 0, `canupload` = 0, `canuploadproductcodeoverride` = 0,
			                            `canuploadpagecountoverride` = 0 WHERE `id` IN (' . $orderItemIDList .')'))
            {
                if ($stmt->bind_param('si', $projectStartTime, $projectDuration))
                {
                    if ($stmt->execute())
                    {
                        $queryResult = self::getQueryResultInfo($dbObj->info);
                        $rowAffected += $queryResult['matched'];

                        // we have updated the status now set the timestamp if it hasn't already been set
                        $stmt->free_result();
                        $stmt->close();

                        if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `filesreceivedtimestamp` = ?, `filesreceiveduserid` = ?
							WHERE `id` IN (' . $orderItemIDList .') AND (`filesreceivedtimestamp` IS NULL)'))
                        {
                            if ($stmt->bind_param('si', $filesReceivedDate, $gSession['userid']))
                            {
                                if ($stmt->execute())
                                {
                                    $imported = true;
                                }
                            }
                        }
                    }
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }

			// Get the shopping cart type for the item we have imported.
			if ($stmt = $dbObj->prepare('SELECT `oh`.`shoppingcarttype` FROM `ORDERHEADER` `oh` LEFT JOIN `ORDERITEMS` `oi` ON `oh`.`id` = `oi`.`orderid` WHERE `oi`.`id` = ?')) {
				if ($stmt->bind_param('i', $orderItemID)) {
					if ($stmt->execute()) {
						if ($stmt->store_result()) {
							if ($stmt->num_rows > 0) {
								$rowCartType = -1;
								if ($stmt->bind_result($rowCartType)) {
									while ($stmt->fetch()) {
										if (-1 !== $rowCartType) {
											$cartType = $rowCartType;
										}
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
            $dbObj->close();
        }

        // if the order has been imported we need to update the order thumbnails
        if ($imported)
        {
			// Validate that the shopping cart is internal or preview thumbnails are desired.
			if (TPX_SHOPPINGCARTTYPE_INTERNAL === $cartType || 1 === (int) $ac_config['ENABLE_PREVIEW_THUMBNAILS'] ?? 0) {
				UtilsObj::processOrderThumbnails($designerAPIVersion, $uploadRef);
			}

			$orderItemIDArray = explode(',', $orderItemIDList);

			// we need to loop around each orderitem. This array will contain the orderitemid sent from
			// production as well as any companion items.
			foreach ($orderItemIDArray as $orderItemID)
            {
				if ($projectDataType == TPX_UPLOAD_DATA_TYPE_RENDERED)
				{
					DataExportObj::EventTrigger(TPX_TRIGGER_IMPORTED_RENDERED, 'ORDERITEM', $orderItemID, 0);
					DataExportObj::EventTrigger(TPX_TRIGGER_IMPORTED_FILES, 'ORDERITEM', $orderItemID, 0);
				}
				elseif ($projectDataType == TPX_UPLOAD_DATA_TYPE_RAW)
				{
					DataExportObj::EventTrigger(TPX_TRIGGER_IMPORTED_PROJECT_ELEMENTS, 'ORDERITEM', $orderItemID, 0);
					DataExportObj::EventTrigger(TPX_TRIGGER_IMPORTED_FILES, 'ORDERITEM', $orderItemID, 0);
				}
        	}
        }

        return $rowAffected;
	}
	

	static function updateItemDecryptQueueStatusPOST()
	{
		$orderItemID = (int) $_POST['orderitemid'];
		$uploadDataType = (int) $_POST['itemuploaddatatype'];
		$uploadMethod = (int) $_POST['itemuploadmethod'];
		$projectLSData = (string) $_POST['projectlsdata'];

		return self::updateItemDecryptQueueStatus($orderItemID, $uploadDataType, $uploadMethod, $projectLSData);
	}


	/**
	 * Updates item queued for decryption status based on post parameters
	 */
	static function updateItemDecryptQueueStatus($pOrderItemID, $pUploadDataType, $pUploadMethod, $pProjectLSData)
	{
		$rowAffected = 0;
		$error = '';

		$dbObj= DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			$paramsToBind = array($pUploadDataType, $pUploadMethod, TPX_ITEM_STATUS_DECRYPTING_FILES, $pProjectLSData, $pOrderItemID);
			$queryIn = '?';
			$bindParamChars = 'iiisi';

			//get a list of companion order item records ids based of the parent order item record ids
			$companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemID);
			$companionOrderItemsCount = count($companionOrderItemRecordIDArray);

			// build the in statement to match the amount of companion IDs
			if ($companionOrderItemsCount > 0)
			{
				// push the companion album array to maintain the order.
				array_push($paramsToBind, ...$companionOrderItemRecordIDArray);
 
				for ($i = 0; $i < $companionOrderItemsCount; $i++) 
				{
					$queryIn .= ',?';
					$bindParamChars .= 'i';
				}
			}

			$stmt = 'UPDATE `ORDERITEMS` SET `uploaddatatype` = ?, `uploadmethod` = ?, `status` = ?, `projectlsdata` = ?, `statustimestamp` = NOW()
				WHERE `id`  IN (' . $queryIn . ')';

			if ($stmt = $dbObj->prepare($stmt))
			{
				// use the splat operator to bind a potentially unknown amount of params
				if ($stmt->bind_param($bindParamChars, ...$paramsToBind))
				{
					$stmt->execute();

					$queryResult = self::getQueryResultInfo($dbObj->info);
					$rowAffected += $queryResult['matched'];
				}	
			}
		}

		if ($rowAffected == 0)
		{
			$error = "Database Update Error";
		}

		return $error;
	}

    static function updateItemDecryptStatusPOST()
    {
        $orderItemID = $_POST['orderitemid'];
        $itemStatus = (int) $_POST['itemstatus'];
        $itemDescription = $_POST['itemstatusdescription'];

        self::updateItemDecryptStatus($orderItemID, $itemStatus, $itemDescription);
    }

    /**
     * Updates the order item's decryption status based on the POST parameters
     *
     * @static
     *
     * @author Kevin Gale
     * @since Version 1.0.0
     */
    static function updateItemDecryptStatus($pOrderItemID, $pItemStatus, $pItemDescription)
    {
        global $gSession;

        $rowAffected = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $orderItemIDList = $pOrderItemID;

            //get a list of companion order item records ids based of the parent order item record ids
            $companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemID);

			// append the companion orderitem id's to the parent order item id
			if (count($companionOrderItemRecordIDArray) > 0)
			{
				$orderItemIDList .= ',' . implode(',', $companionOrderItemRecordIDArray);
			}

            $sqlStatement = 'UPDATE `ORDERITEMS` SET `decrypttimestamp` = now(), ';

            // if the files have been decrypted we also want to update the decryptfilesreceivedtimestamp column with the timestamp of the upload decrypted
            if ($pItemStatus == TPX_ITEM_STATUS_DECRYPTED_FILES)
            {
                $sqlStatement .= '`decryptfilesreceivedtimestamp` = `filesreceivedtimestamp`, ';
            }

            $sqlStatement .= '`decryptuserid` = ?, `canupload` = 0, `canuploadproductcodeoverride` = 0,
			    `canuploadpagecountoverride` = 0, `converttimestamp` = \'\', `convertuserid` = 0, `convertoutputformatcode` = \'\', `jobticketoutputfilename` = \'\',
			    `xmloutputfilename` = \'\', `pagesoutputfilename` = \'\', `cover1outputfilename` = \'\', `cover2outputfilename` = \'\', `jobticketoutputdevicecode` = \'\',
			    `pagesoutputdevicecode` = \'\', `cover1outputdevicecode` = \'\', `cover2outputdevicecode` = \'\', `xmloutputdevicecode` = \'\',
			    `jobticketoutputsubfoldername` = \'\', `pagesoutputsubfoldername` = \'\', `cover1outputsubfoldername` = \'\', `cover2outputsubfoldername` = \'\', `xmloutputsubfoldername` = \'\',
			    `outputtimestamp` = \'\', `outputuserid` = 0, `jobticketepwpartid` = \'\', `pagesepwpartid` = \'\', `cover1epwpartid` = \'\', `cover2epwpartid` = \'\',
				`jobticketepwsubmissionid` = \'\', `pagesepwsubmissionid` = \'\', `cover1epwsubmissionid` = \'\', `cover2epwsubmissionid` = \'\',
				`jobticketepwcompletionstatus` = 0, `pagesepwcompletionstatus` = 0, `cover1epwcompletionstatus` = 0, `cover2epwcompletionstatus` = 0,
				`jobticketepwstatus` = 0, `pagesepwstatus` = 0, `cover1epwstatus` = 0, `cover2epwstatus` = 0,
				`status` = ?, `statusdescription` = ?, `statustimestamp` = now() WHERE `id` IN (' . $orderItemIDList .')';

            if ($stmt = $dbObj->prepare($sqlStatement))
            {
                if ($stmt->bind_param('iis', $gSession['userid'], $pItemStatus, $pItemDescription))
                {
                    $stmt->execute();
                    $queryResult = self::getQueryResultInfo($dbObj->info);
                    $rowAffected += $queryResult['matched'];
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();

			$orderItemIDArray = explode(',', $orderItemIDList);

			// we need to loop around each orderitem. This array will contain the orderitemid sent from
			// production as well as any companion items.
			foreach ($orderItemIDArray as $orderItemID)
            {
				if ($pItemStatus == TPX_ITEM_STATUS_DECRYPTED_FILES)
				{
					DataExportObj::EventTrigger(TPX_TRIGGER_DECRYPTED_RENDERED, 'ORDERITEM', $orderItemID, 0);
					DataExportObj::EventTrigger(TPX_TRIGGER_DECRYPTED_FILES, 'ORDERITEM', $orderItemID, 0);
				}
				elseif (($pItemStatus == TPX_ITEM_STATUS_RAW_FILES_READY_TO_PROCESS) || ($pItemStatus == TPX_ITEM_STATUS_RAW_FILES_QUEUED_FOR_RENDER_SUBMISSION) ||
						($pItemStatus == TPX_ITEM_STATUS_RAW_FILES_QUEUED_FOR_RENDERING))
				{
					DataExportObj::EventTrigger(TPX_TRIGGER_DECRYPTED_PROJECT_ELEMENTS, 'ORDERITEM', $orderItemID, 0);
					DataExportObj::EventTrigger(TPX_TRIGGER_DECRYPTED_FILES, 'ORDERITEM', $orderItemID, 0);
				}
            }
        }

        return $rowAffected;
    }

    static function updateItemConvertStatusPOST()
    {
        $orderItemID = $_POST['orderitemid'];
        $itemStatus = (int) $_POST['itemstatus'];
        $itemDescription = $_POST['itemstatusdescription'];
        $outputFormatCode = $_POST['outputformatcode'];
        $jobTicketOutputDeviceCode = $_POST['jobticketoutputdevicecode'];
        $jobTicketFilename = $_POST['jobticketfilename'];
        $pagesOutputDeviceCode = $_POST['pagesoutputdevicecode'];
        $pagesFilename = $_POST['pagesfilename'];
        $cover1OutputDeviceCode = $_POST['cover1outputdevicecode'];
        $cover1Filename = $_POST['cover1filename'];
        $cover2OutputDeviceCode = $_POST['cover2outputdevicecode'];
        $cover2Filename = $_POST['cover2filename'];
        $xmlOutputDeviceCode = $_POST['xmloutputdevicecode'];
        $xmlFilename = $_POST['xmlfilename'];

        self::updateItemConvertStatus($orderItemID, $outputFormatCode, $jobTicketOutputDeviceCode, $jobTicketFilename,
                $pagesOutputDeviceCode, $pagesFilename, $cover1OutputDeviceCode, $cover1Filename, $cover2OutputDeviceCode, $cover2Filename,
                $xmlOutputDeviceCode, $xmlFilename, $itemStatus, $itemDescription);
    }

    /**
     * Updates the order item's conversion status based on the POST parameters
     *
     * @static
     *
     * @author Kevin Gale
     * @since Version 1.0.0
     */
    static function updateItemConvertStatus($pOrderItemID, $pOutputFormatCode, $pJobTicketOutputDeviceCode, $pJobTicketFilename,
            $pPagesOutputDeviceCode, $pPagesFilename, $pCover1OutputDeviceCode, $pCover1Filename, $pCover2OutputDeviceCode,
            $pCover2Filename, $pXMLOutputDeviceCode, $pXMLFilename, $pItemStatus, $pItemStatusDescription)
    {
        global $gSession;

        $rowAffected = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $orderItemIDList = $pOrderItemID;

            //get a list of companion order item records ids based of the parent order item record ids
            $companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemID);

			// append the companion orderitem id's to the parent order item id
			if (count($companionOrderItemRecordIDArray) > 0)
			{
				$orderItemIDList .= ',' . implode(',', $companionOrderItemRecordIDArray);
			}

            if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `converttimestamp` = NOW(), `convertuserid` = ?, `convertoutputformatcode` = ?, `jobticketoutputfilename` = ?,
				`pagesoutputfilename` = ?, `cover1outputfilename` = ?, `cover2outputfilename` = ?, `xmloutputfilename` = ?, `jobticketoutputdevicecode` = ?, `pagesoutputdevicecode` = ?,
				`cover1outputdevicecode` = ?, `cover2outputdevicecode` = ?, `xmloutputdevicecode` = ?, `outputtimestamp` = \'\', `jobticketoutputsubfoldername` = \'\',
				`pagesoutputsubfoldername` = \'\', `cover1outputsubfoldername` = \'\', `cover2outputsubfoldername` = \'\', `xmloutputsubfoldername` = \'\', `outputuserid` = 0,
				`jobticketepwpartid` = \'\', `pagesepwpartid` = \'\', `cover1epwpartid` = \'\', `cover2epwpartid` = \'\', `jobticketepwsubmissionid` = \'\',
				`pagesepwsubmissionid` = \'\', `cover1epwsubmissionid` = \'\', `cover2epwsubmissionid` = \'\', `jobticketepwcompletionstatus` = 0, `pagesepwcompletionstatus` = 0,
				`cover1epwcompletionstatus` = 0, `cover2epwcompletionstatus` = 0, `jobticketepwstatus` = 0, `pagesepwstatus` = 0, `cover1epwstatus` = 0,
				`cover2epwstatus` = 0, `status` = ?, `statusdescription` = ?, `statustimestamp` = NOW()
				WHERE `id` IN (' . $orderItemIDList .')'))
            {
                if ($stmt->bind_param('isssssssssssis', $gSession['userid'], $pOutputFormatCode, $pJobTicketFilename, $pPagesFilename,
                                $pCover1Filename, $pCover2Filename, $pXMLFilename, $pJobTicketOutputDeviceCode, $pPagesOutputDeviceCode,
                                $pCover1OutputDeviceCode, $pCover2OutputDeviceCode, $pXMLOutputDeviceCode, $pItemStatus,
                                $pItemStatusDescription))
                {
                    $stmt->execute();
                    $queryResult = self::getQueryResultInfo($dbObj->info);
                    $rowAffected += $queryResult['matched'];
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();

			$orderItemIDArray = explode(',', $orderItemIDList);

			// we need to loop around each orderitem. This array will contain the orderitemid sent from
			// production as well as any companion items.
			foreach ($orderItemIDArray as $orderItemID)
            {
				if (($pItemStatus == TPX_ITEM_STATUS_READY_TO_PRINT) || ($pItemStatus == TPX_ITEM_STATUS_PRINT_FILES_QUEUED))
				{
					DataExportObj::EventTrigger(TPX_TRIGGER_CONVERTED_FILES, 'ORDERITEM', $orderItemID, 0);
				}
            }
        }

        return $rowAffected;
    }

    /**
     * Helper function to update the order item database record active status based on the order item id provided in the POST parameters
     *
     * @param  $_POST['orderitemidlist'] Comma separated list of order item id's
     * @param  $_POST['onholdstatus'] New on-hold status
     * @param  $_POST['onholdreason'] On-hold reason
     * @global $gSession Global session object
     *
     * @author Kevin Gale
     * @version 3.0.0
     * @since Version 3.0.0
     */
    static function updateItemOnHoldStatusPOST()
    {
        global $gSession;

        $orderItemIDList = (string) $_POST['orderitemidlist'];
        $itemOnHoldStatus = (int) $_POST['onholdstatus'];
        $itemOnHoldReason = $_POST['onholdreason'];
        $userID = $gSession['userid'];

        return self::updateItemOnHoldStatus($orderItemIDList, $userID, $itemOnHoldStatus, $itemOnHoldReason);
    }

    /**
     * Updates the order item's on-hold status based on specified parameters
     *
     * @param  $pOrderItemIDList string Comma separated list of order item id's
     * @param  $pUserID int User ID
     * @param  $pItemOnHoldStatus int New on-hold status
     * @param  $pItemOnHoldReason string On-hold reason
     *
     * @author Kevin Gale
     * @since Version 1.0.0
     */
    static function updateItemOnHoldStatus($pOrderItemIDList, $pUserID, $pItemOnHoldStatus, $pItemOnHoldReason)
    {
		$returnArray = UtilsObj::getReturnArray();
		$error = "";
		$errorParam = "";

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `onhold` = ?, `onholdtimestamp` = now(), `onholduserid` = ?, `onholdreason` = ? WHERE `id` = ?'))
            {
            	//get a list of companion order item records ids based of the parent order item record ids
            	$companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemIDList);
				$parentOrderItemIDList = explode(',', $pOrderItemIDList);

				// append the companion order item record id's to the parent array so that they can all be updated correctly.
				$orderItemIDArray = array_merge($parentOrderItemIDList, $companionOrderItemRecordIDArray);
                $itemCount = count($orderItemIDArray);

                for ($i = 0; $i < $itemCount; $i++)
                {
                    UtilsObj::resetPHPScriptTimeout(10);

                    $orderItemID = $orderItemIDArray[$i];

                    if ($stmt->bind_param('iisi', $pItemOnHoldStatus, $pUserID, $pItemOnHoldReason, $orderItemID))
                    {
						if ($stmt->execute())
						{
							DataExportObj::EventTrigger(TPX_TRIGGER_ON_HOLD_UPDATED, 'ORDERITEM', $orderItemID, 0);
						}                
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = 'updateItemOnHoldStatus Execute ' . $dbObj->error;
						}
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = 'updateItemOnHoldStatus BindParam ' . $dbObj->error;
					}
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = 'updateItemOnHoldStatus prepare ' . $dbObj->error;
			}

            $dbObj->close();
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = 'updateItemOnHoldStatus getConnection ' . $dbObj->error;
		}
		
		$returnArray['error'] = $error;
		$returnArray['errorparam'] = $errorParam;

        return $returnArray;
    }

    /**
     * Update's the order item's output (printed) status based on the POST parameters
     *
     * @param  $_POST['orderitemidlist'] Comma separated list of order item id's
     * @param  $_POST['itemstatus'] New order status
     * @param  $_POST['itemstatusdescription'] Status description
     * @param  $_POST['jobticketoutputdevicecode'] Job ticket output device code
     * @param  $_POST['pagesoutputdevicecode'] Pages output device code
     * @param  $_POST['cover1outputdevicecode'] Cover 1 output device code
     * @param  $_POST['cover2outputdevicecode'] Cover 2 output device code
     * @global $gSession Global session object
     *
     * @author Kevin Gale
     * @version 3.0.0
     * @since Version 3.0.0
     */
    static function updateItemOutputStatusPOST()
    {
        global $gSession;

        $orderItemIDList = (string) $_POST['orderitemidlist'];
        $itemStatus = (int) $_POST['itemstatus'];
        $itemStatusDescription = $_POST['itemstatusdescription'];

        $generateDataExport = $_POST['dataexportgenerate'];
        $dataExportLanguage = $_POST['dataexportlanguage'];
        $dataExportIncludePaymentData = $_POST['dataexportincludepaymentdata'];
        $dataExportBeautify = $_POST['dataexportbeautify'];
        $updateItemSettings = $_POST['updatesettings'];
        $updateOutputCount = $_POST['updateoutputcount'];

        $jobTicketOutputDeviceCode = $_POST['jobticketoutputdevicecode'];
        $pagesOutputDeviceCode = $_POST['pagesoutputdevicecode'];
        $cover1OutputDeviceCode = $_POST['cover1outputdevicecode'];
        $cover2OutputDeviceCode = $_POST['cover2outputdevicecode'];
        $xmlOutputDeviceCode = $_POST['xmloutputdevicecode'];

        $jobTicketOutputSubfolderName = $_POST['jobticketoutputsubfoldername'];
        $pagesOutputSubfolderName = $_POST['pagesoutputsubfoldername'];
        $cover1OutputSubfolderName = $_POST['cover1outputsubfoldername'];
        $cover2OutputSubfolderName = $_POST['cover2outputsubfoldername'];
        $xmlOutputSubfolderName = $_POST['xmloutputsubfoldername'];

        $jobTicketEPWPartID = $_POST['jobticketepwpartid'];
        $pagesEPWPartID = $_POST['pagesepwpartid'];
        $cover1EPWPartID = $_POST['cover1epwpartid'];
        $cover2EPWPartID = $_POST['cover2epwpartid'];

        $jobTicketEPWSubmissionID = $_POST['jobticketepwsubmissionid'];
        $pagesEPWSubmissionID = $_POST['pagesepwsubmissionid'];
        $cover1EPWSubmissionID = $_POST['cover1epwsubmissionid'];
        $cover2EPWSubmissionID = $_POST['cover2epwsubmissionid'];

        $jobTicketEPWCompletionStatus = $_POST['jobticketepwcompletionstatus'];
        $pagesEPWCompletionStatus = $_POST['pagesepwcompletionstatus'];
        $cover1EPWCompletionStatus = $_POST['cover1epwcompletionstatus'];
        $cover2EPWCompletionStatus = $_POST['cover2epwcompletionstatus'];

        $jobTicketEPWStatus = $_POST['jobticketepwstatus'];
        $pagesEPWStatus = $_POST['pagesepwstatus'];
        $cover1EPWStatus = $_POST['cover1epwstatus'];
        $cover2EPWStatus = $_POST['cover2epwstatus'];

        $userID = $gSession['userid'];

        return self::updateItemOutputStatus($orderItemIDList, $userID, $itemStatus, $itemStatusDescription, $generateDataExport,
                        $dataExportLanguage, $dataExportIncludePaymentData, $dataExportBeautify, $updateItemSettings, $updateOutputCount,
                        $jobTicketOutputDeviceCode, $pagesOutputDeviceCode, $cover1OutputDeviceCode, $cover2OutputDeviceCode,
                        $xmlOutputDeviceCode, $jobTicketOutputSubfolderName, $pagesOutputSubfolderName, $cover1OutputSubfolderName,
                        $cover2OutputSubfolderName, $xmlOutputSubfolderName, $jobTicketEPWPartID, $pagesEPWPartID,
                        $cover1EPWPartID, $cover2EPWPartID, $jobTicketEPWSubmissionID, $pagesEPWSubmissionID, $cover1EPWSubmissionID,
                        $cover2EPWSubmissionID, $jobTicketEPWCompletionStatus, $pagesEPWCompletionStatus, $cover1EPWCompletionStatus,
                        $cover2EPWCompletionStatus, $jobTicketEPWStatus, $pagesEPWStatus, $cover1EPWStatus, $cover2EPWStatus);
    }

    /**
     * Update's the order item's output (printed) status based on the specified parameters
     *
     * @author Kevin Gale
     * @version 3.0.0
     * @since Version 1.0.0
     */
    static function updateItemOutputStatus($pOrderItemIDList, $pUserID, $pItemStatus, $pItemStatusDescription, $pGenerateDataExport,
            $pDataExportLanguage, $pDataExportIncludePaymentData, $pDataExportBeautify, $pUpdateOutputSettings, $pUpdateOutputCount,
            $pItemJobTicketOutputDeviceCode = '', $pItemPagesOutputDeviceCode = '', $pItemCover1OutputDeviceCode = '',
            $pItemCover2OutputDeviceCode = '', $pItemXMLOutputDeviceCode = '', $pJobTicketOutputSubfolderName = '',
            $pPagesOutputSubfolderName = '', $pCover1OutputSubfolderName = '', $pCover2OutputSubfolderName = '',
            $pXMLOutputSubfolderName = '', $pJobTicketEPWPartID = '', $pPagesEPWPartID = '',
            $pCover1EPWPartID = '', $pCover2EPWPartID = '', $pJobTicketEPWSubmissionID = '', $pPagesEPWSubmissionID = '',
            $pCover1EPWSubmissionID = '', $pCover2EPWSubmissionID = '', $pJobTicketEPWCompletionStatus = 0, $pPagesEPWCompletionStatus = 0,
            $pCover1EPWCompletionStatus = 0, $pCover2EPWCompletionStatus = 0, $pJobTicketEPWStatus = 0, $pPagesEPWStatus = 0,
            $pCover1EPWStatus = 0, $pCover2EPWStatus = 0)
    {
        $resultArray = Array();
        $rowAffected = 0;
        $dataExport = '';

        // if we are updating the output count then we compare the status with the sent to external workflow status
        // if we are not updating the output count then we compare against a high invalid status that an item could never be set to
        if ($pUpdateOutputCount == 1)
        {
            $outputStatusToCompare = TPX_ITEM_STATUS_PRINTING_SENT_TO_EXTERNAL_WORKFLOW;
        }
        else
        {
            $outputStatusToCompare = 9999;
        }

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            //get a list of companion order item records ids based of the parent order item record ids
			$companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemIDList);
			$parentOrderItemIDList = explode(',', $pOrderItemIDList);

			// append the companion order item record id's to the parent array so that they can all be updated correctly.
			$orderItemIDArray = array_merge($parentOrderItemIDList, $companionOrderItemRecordIDArray);
            $itemCount = count($orderItemIDArray);

            for ($i = 0; $i < $itemCount; $i++)
            {
                UtilsObj::resetPHPScriptTimeout(10);

                $orderItemID = $orderItemIDArray[$i];

                if ($pUpdateOutputSettings == 1)
                {
                    if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `outputtimestamp` = NOW(), `outputcount` = IF (? >= ' . $outputStatusToCompare . ', `outputcount` + 1, `outputcount`),
                            `outputuserid` = ?, `jobticketoutputdevicecode` = ?, `pagesoutputdevicecode` = ?, `cover1outputdevicecode` = ?, `cover2outputdevicecode` = ?, `xmloutputdevicecode` = ?,
                            `jobticketoutputsubfoldername` = ?, `pagesoutputsubfoldername` = ?, `cover1outputsubfoldername` = ?, `cover2outputsubfoldername` = ?, `xmloutputsubfoldername` = ?,
                            `jobticketepwpartid` = ?, `pagesepwpartid` = ?, `cover1epwpartid` = ?, `cover2epwpartid` = ?,
                            `jobticketepwsubmissionid` = ?, `pagesepwsubmissionid` = ?, `cover1epwsubmissionid` = ?, `cover2epwsubmissionid` = ?,
                            `jobticketepwcompletionstatus` = ?, `pagesepwcompletionstatus` = ?, `cover1epwcompletionstatus` = ?, `cover2epwcompletionstatus` = ?,
                            `jobticketepwstatus` = ?, `pagesepwstatus` = ?, `cover1epwstatus` = ?, `cover2epwstatus` = ?, `status` = ?, `statusdescription` = ?, `statustimestamp` = NOW()
                            WHERE `id` = ?'))
                    {
                        $bindOK = $stmt->bind_param('i' . 'isssss' . 'sssss' . 'ssss' . 'ssss' . 'iiii' . 'iiiiis' . 'i', $pItemStatus,
                        		$pUserID, $pItemJobTicketOutputDeviceCode, $pItemPagesOutputDeviceCode, $pItemCover1OutputDeviceCode, $pItemCover2OutputDeviceCode, $pItemXMLOutputDeviceCode,
                                $pJobTicketOutputSubfolderName, $pPagesOutputSubfolderName, $pCover1OutputSubfolderName, $pCover2OutputSubfolderName, $pXMLOutputSubfolderName,
                                $pJobTicketEPWPartID, $pPagesEPWPartID, $pCover1EPWPartID, $pCover2EPWPartID,
                                $pJobTicketEPWSubmissionID, $pPagesEPWSubmissionID, $pCover1EPWSubmissionID, $pCover2EPWSubmissionID,
                                $pJobTicketEPWCompletionStatus, $pPagesEPWCompletionStatus, $pCover1EPWCompletionStatus, $pCover2EPWCompletionStatus,
                                $pJobTicketEPWStatus, $pPagesEPWStatus, $pCover1EPWStatus, $pCover2EPWStatus, $pItemStatus, $pItemStatusDescription,
                                $orderItemID);
                    }
                }
                else
                {
                    if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `outputtimestamp` = NOW(), `outputcount` = IF (? >= ' . $outputStatusToCompare . ', `outputcount` + 1, `outputcount`),
                    		`outputuserid` = ?, `status` = ?, `statusdescription` = ?, `statustimestamp` = NOW()
                            WHERE `id` = ?'))
                    {
                        $bindOK = $stmt->bind_param('iiisi', $pItemStatus, $pUserID, $pItemStatus, $pItemStatusDescription, $orderItemID);
                    }
                }

                if ($bindOK)
                {
                    $stmt->execute();
                    $queryResult = self::getQueryResultInfo($dbObj->info);
                    $rowAffected += $queryResult['matched'];

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }

                if ($pItemStatus == TPX_ITEM_STATUS_PRINTING_SENT_TO_EXTERNAL_WORKFLOW)
                {
                    DataExportObj::EventTrigger(TPX_TRIGGER_FILES_SENT_TO_EXTERNAL_WORKFLOW, 'ORDERITEM', $orderItemID, 0);
                }
                elseif ($pItemStatus == TPX_ITEM_STATUS_PRINTED)
                {
                    DataExportObj::EventTrigger(TPX_TRIGGER_FILES_PRINTED, 'ORDERITEM', $orderItemID, 0);
                }
            }

            $dbObj->close();
        }

		if ($pGenerateDataExport == 1)
		{
			$dataExportArray = DataExportObj::generateOrderExportData($orderItemIDArray, false, $pDataExportLanguage,
							$pDataExportIncludePaymentData, '*NONE*');
			$dataExport = DataExportObj::exportDataGenerate($dataExportArray, 'XML', $pDataExportBeautify, 'order');
		}

        $resultArray['rowsaffected'] = $rowAffected;
        $resultArray['dataexport'] = $dataExport;

        return $resultArray;
    }

    /**
     * Helper function to update the order item's finishing status based on the POST parameters
     *
     * @param  $_POST['orderitemidlist'] Comma separated list of order item id's
     * @param  $_POST['itemfinishingdate'] Update date
     * @param  $_POST['itemstatus'] New item status
     * @param  $_POST['itemstatusdescription'] Status description
     * @global $gSession Global session object
     *
     * @author Kevin Gale
     * @version 3.0.0
     * @since Version 3.0.0
     */
    static function updateItemFinishingStatusPOST()
    {
        global $gSession;

        $orderItemIDList = (string) $_POST['orderitemidlist'];
        $itemFinishingDate = $_POST['itemfinishingdate'];
        $itemStatus = (int) $_POST['itemstatus'];
        $itemStatusDescription = $_POST['itemstatusdescription'];
        $userID = $gSession['userid'];

        self::updateItemFinishingStatus($orderItemIDList, $userID, $itemFinishingDate, $itemStatus, $itemStatusDescription);
    }

    /**
     * Updates the order item's finishing status based on specified parameters
     *
     * @param  $pOrderItemIDList string Comma separated list of order item id's
     * @param  $pUserID int User ID
     * @param  $pItemFinishingDate string Update date
     * @param  $pItemStatus int New item status
     * @param  $pItemStatusDescription string Status description
     *
     * @author Kevin Gale
     * @version 3.0.0
     * @since Version 1.0.0
     */
    static function updateItemFinishingStatus($pOrderItemIDList, $pUserID, $pItemFinishingDate, $pItemStatus, $pItemStatusDescription)
    {
        $rowAffected = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            //get a list of companion order item records ids based of the parent order item record ids
			$companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemIDList);
			$parentOrderItemIDList = explode(',', $pOrderItemIDList);

			// append the companion order item record id's to the parent array so that they can all be updated correctly.
			$orderItemIDArray = array_merge($parentOrderItemIDList, $companionOrderItemRecordIDArray);
            $itemCount = count($orderItemIDArray);

            for ($i = 0; $i < $itemCount; $i++)
            {
                UtilsObj::resetPHPScriptTimeout(10);

                $orderItemID = $orderItemIDArray[$i];

                if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `finishtimestamp` = now(), `finishdate` = ?, `finishuserid` = ?, `status` = ?, `statusdescription` = ?, `statustimestamp` = now()
                                            WHERE `id` = ?'))
                {
                    if ($stmt->bind_param('siisi', $pItemFinishingDate, $pUserID, $pItemStatus, $pItemStatusDescription, $orderItemID))
                    {
                        $stmt->execute();
                        $queryResult = self::getQueryResultInfo($dbObj->info);
                        $rowAffected += $queryResult['matched'];
                    }

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }

                if ($pItemStatus == TPX_ITEM_STATUS_FINISHING_COMPLETE)
                {
                    DataExportObj::EventTrigger(TPX_TRIGGER_FINISHING_COMPLETE, 'ORDERITEM', $orderItemID, 0);
                }
            }

            $dbObj->close();
        }

        return $rowAffected;
    }

    /**
     * Helper function to update the order item's shipping status based on the POST parameters
     *
     * @param  $_POST['orderid'] Order ID
     * @param  $_POST['orderitemid'] Order item ID
     * @param  $_POST['itemshippeddate'] Update date
     * @param  $_POST['shippingtrackingreference'] Shipping tracking reference
     * @global $gSession Global session object
     *
     * @author Dasha Salo
     * @version 3.0.0
     * @since Version 3.0.0
     */
    static function updateItemShippingStatusPOST()
    {
        global $gSession;

        $orderID = $_POST['orderid'];
        $orderItemID = $_POST['orderitemid'];
        $itemShippingDate = $_POST['itemshippeddate'];
        $itemTrackingReference = $_POST['shippingtrackingreference'];
        $userID = $gSession['userid'];

        self::updateItemShippingStatus($orderID, $orderItemID, $userID, $itemShippingDate, $itemTrackingReference, TPX_PRODUCTIONAUTOMATION_NOTIFY_YES);
    }

    /**
     * Updates the order item's shipping status based on specified parameters
     *
     * @param $userId int User ID
     * @param $orderID string Order ID
     * @param $orderItemID int Order item ID
     * @param $itemShippingDate string Update date
     * @param $itemTrackingReference string Shipping tracking reference
     *
     * @author Kevin Gale
     * @version 3.0.0
     * @since Version 1.0.0
     */
    static function updateItemShippingStatus($pOrderID, $pOrderItemID, $pUserID, $pItemShippingDate, $pItemTrackingReference, $pSendEmail)
    {
        // include the mailing address and email creation modules
        require_once('../Utils/UtilsAddress.php');
        require_once('../Utils/UtilsEmail.php');

        $rowAffected = 0;

        $shippedStatus = TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER;
        $emailShippedStatus = TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER;
        $canShip = true;
        $shipped = false;
        $sendNotification = true;
        $distributionCentreCode = '';
        $distributionCentreName = '';
        $storeCode = '';
        $storeOpeningTimes = '';
        $storeURL = '';
        $storeEmailAddress = '';
        $storeTelephoneNumber = '';
        $storeContactName = '';
        $storeOnline = 0;
        $currentOwnerSite = '';
        $distributionCentreOnline = '';
        $distributionCentreNotifyEmailName = '';
        $distributionCentreNotifyEmailAddress = '';
        $storeContactFirstName = '';
        $storeContactLastName = '';
        $storeNotifyEmailName = '';
        $storeNotifyEmailAddress = '';
        $userEmailDestination = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            // first determine where we are shipping to
            $stmt = $dbObj->prepare('SELECT oi.currentowner, os.distributioncentrecode, `storecode`, ds.name, ds.siteonline,
                                        ds.smtpproductionname, ds.smtpproductionaddress, ss.telephonenumber, ss.emailaddress,
                                        ss.contactfirstname, ss.contactlastname, ss.siteonline, ss.openingtimes, ss.storeurl,
                                        ss.smtpproductionname, ss.smtpproductionaddress
                                    FROM `ORDERITEMS` oi
                                        LEFT JOIN `ORDERSHIPPING` os ON os.orderid = oi.orderid
                                        LEFT JOIN `SITES` ds ON ds.code = os.distributioncentrecode
                                        LEFT JOIN `SITES` ss ON ss.code = `storecode`
                                    WHERE oi.id = ?');
            if ($stmt)
            {
                if ($stmt->bind_param('i', $pOrderItemID))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($currentOwnerSite, $distributionCentreCode, $storeCode, $distributionCentreName,
                                                $distributionCentreOnline, $distributionCentreNotifyEmailName,
                                                $distributionCentreNotifyEmailAddress, $storeTelephoneNumber, $storeEmailAddress,
                                                $storeContactFirstName, $storeContactLastName, $storeOnline, $storeOpeningTimes, $storeURL,
                                                $storeNotifyEmailName, $storeNotifyEmailAddress))
                                {
                                    if ($stmt->fetch())
                                    {
                                        if ($storeCode != '')
                                        {
                                            // we are either shipping to a store or a distribution centre
                                            $storeContactName = $storeContactFirstName . ' ' . $storeContactLastName;

                                            if ($distributionCentreCode != '')
                                            {
                                                $shippedStatus = TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE;
                                                $emailShippedStatus = TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE;
                                            }
                                            else
                                            {
                                                $shippedStatus = TPX_ITEM_STATUS_SHIPPED_TO_STORE_DIRECTLY;

                                                // if the current production site is the store and the store is not online we notify the customer that their order is ready to collect
                                                if (($storeCode == $currentOwnerSite) && ($storeOnline == 0))
                                                {
                                                    $emailShippedStatus = TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE;
                                                }
                                                else
                                                {
                                                    $emailShippedStatus = TPX_ITEM_STATUS_SHIPPED_TO_STORE_DIRECTLY;
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $canShip = false;
                                    }
                                }
                            }
                            else
                            {
                                $canShip = false;
                            }
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }

            if ($canShip == true)
            {
                 $orderItemIDList = $pOrderItemID;

				//get a list of companion order item records ids based of the parent order item record ids
				$companionOrderItemRecordIDArray = self::getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemID);

				// append the companion orderitem id's to the parent order item id
				if (count($companionOrderItemRecordIDArray) > 0)
				{
					$orderItemIDList .= ',' . implode(',', $companionOrderItemRecordIDArray);
				}

                if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `shippedtimestamp` = now(), `shippeddate` = ?, `shippeduserid` = ?, `shippingtrackingreference` = ?,
                    `invoicedtimestamp` = now(), `invoiceddate` = ?, `invoiceduserid` = ?, `status` = ?, `statusdescription` = "", `statustimestamp` = now() WHERE `id`IN (' . $orderItemIDList .')'))
                {
                    if ($stmt->bind_param('sissii', $pItemShippingDate, $pUserID, $pItemTrackingReference, $pItemShippingDate, $pUserID,
                                    $shippedStatus))
                    {
                        if ($stmt->execute())
                        {
                            $shipped = true;
                            $queryResult = self::getQueryResultInfo($dbObj->info);
                            $rowAffected += $queryResult['matched'];
                        }
                    }

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }

                // if we are shipping directly to the customer we need to determine which address to send the email to
                if ($shippedStatus == TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER)
                {
                    $shipped = false;
                    $stmt = $dbObj->prepare('SELECT `useremaildestination` FROM `ORDERHEADER` WHERE `id` = ?');
                    if ($stmt)
                    {
                        if ($stmt->bind_param('i', $pOrderID))
                        {
                            if ($stmt->execute())
                            {
                                if ($stmt->store_result())
                                {
                                    if ($stmt->num_rows > 0)
                                    {
                                        if ($stmt->bind_result($userEmailDestination))
                                        {
                                            if ($stmt->fetch())
                                            {
                                                $shipped = true;
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
                }
            }

            $dbObj->close();
        }

        if ($shipped == true)
        {
			$orderItemIDArray = explode(',', $orderItemIDList);

			// we need to loop around each orderitem. This array will contain the orderitemid sent from
			// production as well as any companion items.
			foreach ($orderItemIDArray as $orderItemID)
            {
				// check if the confirmation email should be sent
				if (($pSendEmail == TPX_PRODUCTIONAUTOMATION_NOTIFY_YES) ||
						(($pSendEmail == TPX_PRODUCTIONAUTOMATION_NOTIFY_NOTCUSTOMER) && ($emailShippedStatus != TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER)))
				{
					// if the orderitem id we are processing matches the orderitemid passed from prodution
					// then this is the parent item. This means some of the data we need for companions can
					// just be taken from the parent order item.
					if ($orderItemID == $pOrderItemID)
					{
						$orderLanguage = DatabaseObj::getOrderLanguage($pOrderID);
					}

					$jobTicketArray = DatabaseObj::getJobTicket($orderItemID, $orderLanguage);

					// if we are processing the parent only pull the branding information once
					// as it will be the same for each companion
					if ($orderItemID == $pOrderItemID)
					{
						$webBrandArray = AuthenticateObj::getWebBrandData($jobTicketArray['webbrandcode']);
						$webBrandEmailSettingsArray = DatabaseObj::getBrandingFromCode($jobTicketArray['webbrandcode']);
						$brandingDefaults = DatabaseObj::getBrandingFromCode('');

						if (($jobTicketArray['webbrandcode'] != '') && ($webBrandEmailSettingsArray['usedefaultemailsettings'] == 0))
						{
							if ($webBrandEmailSettingsArray['smtpshippingactive'] == 0)
							{
								$sendNotification = false;
							}
						}
						else
						{
							if ($brandingDefaults['smtpshippingactive'] == 0)
							{
								$sendNotification = false;
							}
						}
					}
				}
				else
				{
					$sendNotification = false;
				}

				if ($sendNotification)
				{
					// if we are processing the parent only pull the customer information once
					// as it will be the same for each companion
					if ($orderItemID == $pOrderItemID)
					{
						$useraccount = DatabaseObj::getUserAccountFromID($jobTicketArray['userid']);
						$loginname = $useraccount['login'];

						$billingAddress = UtilsAddressObj::formatAddress($jobTicketArray, 'billing', "\n");
						$shippingAddress = UtilsAddressObj::formatAddress($jobTicketArray, 'shipping', "\n");
					}

					if ($emailShippedStatus == TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER)
					{
						$emailTemplate = 'customer_ordershipped';

						// determine, where to send the confirmation email
						switch($userEmailDestination)
						{
							case 0: // billing address;
								$emailName = $jobTicketArray['billingcontactfirstname'] . ' ' . $jobTicketArray['billingcontactlastname'];
								$emailAddress = $jobTicketArray['billingcustomeremailaddress'];
								$emailNameBCC = $webBrandEmailSettingsArray['smtpshippingname'];
								$emailAddressBCC = $webBrandEmailSettingsArray['smtpshippingaddress'];
								break;
							case 1: // shipping address
								$emailName = $jobTicketArray['shippingcontactfirstname'] . ' ' . $jobTicketArray['shippingcontactlastname'];
								$emailAddress = $jobTicketArray['shippingcustomeremailaddress'];
								$emailNameBCC = $webBrandEmailSettingsArray['smtpshippingname'];
								$emailAddressBCC = $webBrandEmailSettingsArray['smtpshippingaddress'];
								break;
							case 2: // shipping address and bcc to billing address
								$emailName = $jobTicketArray['shippingcontactfirstname'] . ' ' . $jobTicketArray['shippingcontactlastname'];
								$emailAddress = $jobTicketArray['shippingcustomeremailaddress'];
								$emailNameBCC = $jobTicketArray['billingcontactfirstname'] . ' ' . $jobTicketArray['billingcontactlastname'];
								$emailAddressBCC = $jobTicketArray['billingcustomeremailaddress'];
								break;
							case 3: // billing address and bcc to shipping address
								$emailName = $jobTicketArray['billingcontactfirstname'] . ' ' . $jobTicketArray['billingcontactlastname'];
								$emailAddress = $jobTicketArray['billingcustomeremailaddress'];
								$emailNameBCC = $jobTicketArray['shippingcontactfirstname'] . ' ' . $jobTicketArray['shippingcontactlastname'];
								$emailAddressBCC = $jobTicketArray['shippingcustomeremailaddress'];
								break;
						}
					}
					elseif ($emailShippedStatus == TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE)
					{
						$emailTemplate = 'distributioncentre_ordershipped';
						$emailName = $distributionCentreNotifyEmailName;
						$emailAddress = $distributionCentreNotifyEmailAddress;
						$emailNameBCC = $webBrandEmailSettingsArray['smtpshippingname'];
						$emailAddressBCC = $webBrandEmailSettingsArray['smtpshippingaddress'];
					}
					elseif ($emailShippedStatus == TPX_ITEM_STATUS_SHIPPED_TO_STORE_DIRECTLY)
					{
						$emailTemplate = 'store_ordershipped';
						$emailName = $storeNotifyEmailName;
						$emailAddress = $storeNotifyEmailAddress;
						$emailNameBCC = $webBrandEmailSettingsArray['smtpshippingname'];
						$emailAddressBCC = $webBrandEmailSettingsArray['smtpshippingaddress'];
					}
					elseif ($emailShippedStatus == TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE)
					{
						$emailTemplate = 'customer_orderreadytocollect';
						$emailName = $jobTicketArray['billingcontactfirstname'] . ' ' . $jobTicketArray['billingcontactlastname'];
						$emailAddress = $jobTicketArray['billingcustomeremailaddress'];
						$emailNameBCC = '';
						$emailAddressBCC = '';

						$storeOpeningTimes = LocalizationObj::getLocaleString($storeOpeningTimes, $orderLanguage, true);
						$storeOpeningTimes = str_replace('\n', "\n", $storeOpeningTimes);
					}

					// only send the email if we have an email address
					if ($emailAddress != '')
					{
						$emailObj = new TaopixMailer();
						$emailObj->sendTemplateEmail($emailTemplate, $webBrandArray['webbrandcode'], $webBrandArray['webbrandapplicationname'],
								$webBrandArray['webbranddisplayurl'], $orderLanguage, $emailName, $emailAddress, $emailNameBCC,
								$emailAddressBCC, 0,
								Array(
							'orderid' => $jobTicketArray['orderid'],
							'orderitemid' => $jobTicketArray['recordid'],
							'userid' => $jobTicketArray['userid'],
							'loginname' => $loginname,
							'currencycode' => $jobTicketArray['currencycode'],
							'currencyname' => $jobTicketArray['currencyname'],
							'ordernumber' => $jobTicketArray['ordernumber'],
							'qty' => $jobTicketArray['qty'],
							'pagecount' => $jobTicketArray['pagecount'],
							'projectname' => $jobTicketArray['projectname'],
							'productcode' => $jobTicketArray['productcode'],
							'productname' => $jobTicketArray['productname'],
							'defaultcovercode' => $jobTicketArray['defaultcovercode'],
							'defaultpapercode' => $jobTicketArray['defaultpapercode'],
							'defaultpagecount' => $jobTicketArray['defaultpagecount'],
							'covercount' => $jobTicketArray['covercount'],
							'covercode' => $jobTicketArray['covercode'],
							'covername' => $jobTicketArray['covername'],
							'papercount' => $jobTicketArray['papercount'],
							'papercode' => $jobTicketArray['papercode'],
							'papername' => $jobTicketArray['papername'],
							'vouchercode' => $jobTicketArray['vouchercode'],
							'vouchername' => $jobTicketArray['vouchername'],
							'ordertotal' => $jobTicketArray['ordertotal'],
							'formattedordertotal' => $jobTicketArray['formattedordertotal'],
							'shippingcontactname' => $jobTicketArray['shippingcontactfirstname'] . ' ' . $jobTicketArray['shippingcontactlastname'],
							'shippingcontactfirstname' => $jobTicketArray['shippingcontactfirstname'],
							'shippingcontactlastname' => $jobTicketArray['shippingcontactlastname'],
							'shippingaddress' => $shippingAddress,
							'shippingmethodname' => $jobTicketArray['shippingmethodname'],
							'shippingmethod' => $jobTicketArray['shippingmethodname'], // leave 'shippingmethod' in in order not to break existing templates, but really it should be 'shippingmethodname'
							'shippingqty' => $jobTicketArray['shippingqty'],
							'shippingcustomername' => $jobTicketArray['shippingcustomername'],
							'shippingcustomeraddress1' => $jobTicketArray['shippingcustomeraddress1'],
							'shippingcustomeraddress2' => $jobTicketArray['shippingcustomeraddress2'],
							'shippingcustomeraddress3' => $jobTicketArray['shippingcustomeraddress3'],
							'shippingcustomeraddress4' => $jobTicketArray['shippingcustomeraddress4'],
							'shippingcustomercity' => $jobTicketArray['shippingcustomercity'],
							'shippingcustomercounty' => $jobTicketArray['shippingcustomercounty'],
							'shippingcustomerstate' => $jobTicketArray['shippingcustomerstate'],
							'shippingcustomerregioncode' => $jobTicketArray['shippingcustomerregioncode'],
							'shippingcustomerregion' => $jobTicketArray['shippingcustomerregion'],
							'shippingcustomerpostcode' => $jobTicketArray['shippingcustomerpostcode'],
							'shippingcustomercountrycode' => $jobTicketArray['shippingcustomercountrycode'],
							'shippingcustomercountryname' => $jobTicketArray['shippingcustomercountryname'],
							'shippingcustomertelephonenumber' => $jobTicketArray['shippingcustomertelephonenumber'],
							'shippingcustomeremailaddress' => $jobTicketArray['shippingcustomeremailaddress'],
							'shippingmethodcode' => $jobTicketArray['shippingmethodcode'],
							'shippingratecode' => $jobTicketArray['shippingratecode'],
							'shippingrateinfo' => $jobTicketArray['shippingrateinfo'],
							'shippingratecost' => $jobTicketArray['shippingratecost'],
							'shippingratesell' => $jobTicketArray['shippingratesell'],
							'shippingratetaxcode' => $jobTicketArray['shippingratetaxcode'],
							'shippingratetaxname' => $jobTicketArray['shippingratetaxname'],
							'shippingratetaxrate' => $jobTicketArray['shippingratetaxrate'],
							'shippingratecalctax' => $jobTicketArray['shippingratecalctax'],
							'shippingratetaxtotal' => $jobTicketArray['shippingratetaxtotal'],
							'shippeddate' => $jobTicketArray['shippeddate'],
							'formattedshippeddatetime' => $jobTicketArray['formattedshippeddatetime'],
							'formattedshippeddate' => $jobTicketArray['formattedshippeddate'],
							'formattedshippedtime' => $jobTicketArray['formattedshippedtime'],
							'shippingtrackingreference' => $jobTicketArray['shippingtrackingreference'],
							'orderdate' => $jobTicketArray['orderdate'],
							'formattedorderdatetime' => $jobTicketArray['formattedorderdatetime'],
							'formattedorderdate' => $jobTicketArray['formattedorderdate'],
							'formattedordertime' => $jobTicketArray['formattedordertime'],
							'distributioncentrecode' => $distributionCentreCode,
							'distributioncentrename' => $distributionCentreName,
							'storecode' => $storeCode,
							'storeopeningtimes' => $storeOpeningTimes,
							'storeurl' => $storeURL,
							'storeemailaddress' => $storeEmailAddress,
							'storetelephonenumber' => $storeTelephoneNumber,
							'storecontactname' => $storeContactName,
							'storeonline' => $storeOnline,
							'billingcontactname' => $jobTicketArray['billingcontactfirstname'] . ' ' . $jobTicketArray['billingcontactlastname'],
							'billingcontactfirstname' => $jobTicketArray['billingcontactfirstname'],
							'billingcontactlastname' => $jobTicketArray['billingcontactlastname'],
							'billingcustomerregisteredtaxnumbertype' => $jobTicketArray['billingcustomerregisteredtaxnumbertype'],
							'billingcustomerregisteredtaxnumber' => $jobTicketArray['billingcustomerregisteredtaxnumber'],
							'billingaddress' => $billingAddress,
							'billingcustomeraccountcode' => $jobTicketArray['billingcustomeraccountcode'],
							'billingcustomername' => $jobTicketArray['billingcustomername'],
							'billingcustomeraddress1' => $jobTicketArray['billingcustomeraddress1'],
							'billingcustomeraddress2' => $jobTicketArray['billingcustomeraddress2'],
							'billingcustomeraddress3' => $jobTicketArray['billingcustomeraddress3'],
							'billingcustomeraddress4' => $jobTicketArray['billingcustomeraddress4'],
							'billingcustomercity' => $jobTicketArray['billingcustomercity'],
							'billingcustomercounty' => $jobTicketArray['billingcustomercounty'],
							'billingcustomerstate' => $jobTicketArray['billingcustomerstate'],
							'billingcustomerregioncode' => $jobTicketArray['billingcustomerregioncode'],
							'billingcustomerregion' => $jobTicketArray['billingcustomerregion'],
							'billingcustomerpostcode' => $jobTicketArray['billingcustomerpostcode'],
							'billingcustomercountrycode' => $jobTicketArray['billingcustomercountrycode'],
							'billingcustomercountryname' => $jobTicketArray['billingcustomercountryname'],
							'billingcustomertelephonenumber' => $jobTicketArray['billingcustomertelephonenumber'],
							'billingcustomeremailaddress' => $jobTicketArray['billingcustomeremailaddress'],
							'paymentmethodname' => $jobTicketArray['paymentmethodname'],
							'targetuserid' => $jobTicketArray['userid']), '', ''
						);
					}
				}

				if (($shippedStatus == TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER) || ($shippedStatus == TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE) || ($shippedStatus == TPX_ITEM_STATUS_SHIPPED_TO_STORE_DIRECTLY))
				{
					DataExportObj::EventTrigger(TPX_TRIGGER_SHIPPED, 'ORDERITEM', $orderItemID, $pOrderID);
				}
            }
        }

        return $rowAffected;
    }

    /**
     * Return info about the latest executed statement & put them in array.
     *
     * @author Loc Dinh
     * @since Version 3.0.0
     */
    static function getQueryResultInfo($pResultString)
    {
        $returnArray = array();

        list($matched, $changed, $warnings) = sscanf($pResultString, "Rows matched: %d Changed: %d Warnings: %d");
        $returnArray['matched'] = $matched;
        $returnArray['changed'] = $changed;
        $returnArray['warning'] = $warnings;

        return $returnArray;
    }

    /**
     * Return the job ticket data array.
     *
     * @author Kevin Gale
     * @since Version 1.0.0
     */
    static function getJobInfo()
    {
        $resultArray = array();
        $componentsArray = array();
        $componentMetaDataArray = array();
        $jobTicketArray = array();
        $jobInfoArray = array();

        $orderID = (int) $_POST['orderid'];
        $orderItemID = (int) $_POST['orderitemid'];
        $languageCode = $_POST['langcode'];

        // string to show for paymentreceiveduserid=SERVER
		$smarty = SmartyObj::newSmarty('AppAPI');
		$paymentReceivedByServer = $smarty->get_config_vars('str_LabelServer');


		// retrieve the order item components
        $orderItemComponentsArray = DatabaseObj::getOrderItemComponents($orderID, $orderItemID, '', $languageCode);
        $componentsArray = $orderItemComponentsArray['components'];


        // retrieve the order footer items
        $orderItemComponentsArray = DatabaseObj::getOrderItemComponents($orderID, -1, '', $languageCode);
        $orderFooterComponentsArray = &$orderItemComponentsArray['components'];
        $itemCount = count($orderFooterComponentsArray);
        for($i = 0; $i < $itemCount; $i++)
        {
            $componentsArray[] = &$orderFooterComponentsArray[$i];
        }


        // get the meta-data for all of the components
        $itemCount = count($componentsArray);
        for($i = 0; $i < $itemCount; $i++)
        {
            $componentItem = &$componentsArray[$i];

            if ($componentItem['metadatacodelist'] != '')
            {
                $metaDataArray = MetaDataObj::getMetaData($orderID, $componentItem['orderitemid'], $componentItem['id'], 'COMPONENT',
                                $componentItem['metadatacodelist'], $languageCode);
                $itemCount2 = count($metaDataArray);
                for($i2 = 0; $i2 < $itemCount2; $i2++)
                {
                    $componentMetaDataArray[] = &$metaDataArray[$i2];
                }
            }
        }


		// get the job ticket data
        $jobTicketArray = DatabaseObj::getJobTicket($orderItemID, $languageCode);


        // get the additional job info data
        $dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('SELECT oh.paymentreceivedtimestamp,
					IF (oh.paymentreceiveduserid = -1, "' . $paymentReceivedByServer . '",  CONCAT(pru.contactfirstname, " ", pru.contactlastname)),
					oi.filesreceivedtimestamp, CONCAT(fru.contactfirstname, " ", fru.contactlastname),
					oi.decrypttimestamp, CONCAT(dcu.contactfirstname, " ", dcu.contactlastname),
					oi.converttimestamp, CONCAT(cnu.contactfirstname, " ", cnu.contactlastname), oi.convertoutputformatcode, `of`.name,
					oi.productcover1format, oi.productcover2format,
					oi.outputtimestamp, CONCAT(opu.contactfirstname, " ", opu.contactlastname),
					jtod.name, pagesod.name, cov1od.name, cov2od.name, xmlod.name,
					oi.jobticketoutputsubfoldername, oi.pagesoutputsubfoldername, oi.cover1outputsubfoldername, oi.cover2outputsubfoldername, oi.xmloutputsubfoldername,
					oi.jobticketoutputfilename, oi.pagesoutputfilename, oi.cover1outputfilename, oi.cover2outputfilename, oi.xmloutputfilename,
					oi.finishtimestamp, CONCAT(fnu.contactfirstname, " ", fnu.contactlastname),
					oi.shippedtimestamp, CONCAT(shu.contactfirstname, " ", shu.contactlastname),
					oi.shippeddistributioncentrereceivedtimestamp, oi.shippeddistributioncentrereceiveddate, CONCAT(sdcru.contactfirstname, " ", sdcru.contactlastname),
					oi.shippeddistributioncentreshippedtimestamp, oi.shippeddistributioncentreshippeddate, CONCAT(sdcsu.contactfirstname, " ", sdcsu.contactlastname),
					oi.shippedstorereceivedtimestamp, oi.shippedstorereceiveddate, CONCAT(sstru.contactfirstname, " ", sstru.contactlastname),
					oi.shippedcustomercollectedtimestamp, oi.shippedcustomercollecteddate, CONCAT(sstcu.contactfirstname, " ", sstcu.contactlastname),
					oi.canmodify, oi.canupload, oi.canuploadproductcodeoverride, oi.canuploadpagecountoverride, oi.canuploadenablesaveoverride,
					oi.jobticketepwpartid, oi.pagesepwpartid, oi.cover1epwpartid, oi.cover2epwpartid,
                    oi.jobticketepwsubmissionid, oi.pagesepwsubmissionid, oi.cover1epwsubmissionid, oi.cover2epwsubmissionid,
                    oi.jobticketepwcompletionstatus, oi.pagesepwcompletionstatus, oi.cover1epwcompletionstatus, oi.cover2epwcompletionstatus,
                    oi.jobticketepwstatus, oi.pagesepwstatus, oi.cover1epwstatus, oi.cover2epwstatus, oi.projectaimode
					FROM ORDERHEADER oh LEFT JOIN ORDERITEMS oi ON oh.id = oi.orderid
					LEFT JOIN USERS pru ON pru.id = oh.paymentreceiveduserid
					LEFT JOIN USERS fru ON fru.id = oi.filesreceiveduserid
					LEFT JOIN USERS dcu ON dcu.id = oi.decryptuserid
					LEFT JOIN USERS cnu ON cnu.id = oi.convertuserid
					LEFT JOIN OUTPUTFORMATS `of` ON `of`.code = oi.convertoutputformatcode
					LEFT JOIN USERS opu ON opu.id = oi.outputuserid
					LEFT JOIN OUTPUTDEVICES jtod ON jtod.code = oi.jobticketoutputdevicecode
					LEFT JOIN OUTPUTDEVICES pagesod ON pagesod.code = oi.pagesoutputdevicecode
					LEFT JOIN OUTPUTDEVICES cov1od ON cov1od.code = oi.cover1outputdevicecode
					LEFT JOIN OUTPUTDEVICES cov2od ON cov2od.code = oi.cover2outputdevicecode
					LEFT JOIN OUTPUTDEVICES xmlod ON xmlod.code = oi.xmloutputdevicecode
					LEFT JOIN USERS fnu ON fnu.id = oi.finishuserid
					LEFT JOIN USERS shu ON shu.id = oi.shippeduserid
					LEFT JOIN USERS sdcru ON sdcru.id = oi.shippeddistributioncentrereceiveduserid
                    LEFT JOIN USERS sdcsu ON sdcsu.id = oi.shippeddistributioncentreshippeduserid
                    LEFT JOIN USERS sstru ON sstru.id = oi.shippedstorereceiveduserid
                    LEFT JOIN USERS sstcu ON sstcu.id = oi.shippedcustomercollecteduserid
					WHERE oi.id = ?'))
			{
				if ($stmt->bind_param('i', $orderItemID))
				{
					if ($stmt->bind_result($paymentReceivedTimeStamp, $paymentReceivedUserName, $filesReceivedTimeStamp, $filesReceivedUserName,
						$decryptTimeStamp, $decryptUserName, $convertTimeStamp, $convertUserName, $convertOutputFormatCode, $convertOutputFormatName,
						$productCover1Format, $productCover2Format, $outputTimeStamp, $outputUserName,
						$jobTicketOutputDeviceName, $pagesOutputDeviceName, $cover1OutputDeviceName, $cover2OutputDeviceName, $xmlOutputDeviceName,
						$jobTicketSubfolderName, $pagesSubfolderName, $cover1SubfolderName, $cover2SubfolderName, $xmlSubfolderName,
						$jobTicketOutputFilename, $pagesOutputFilename, $cover1OutputFilename, $cover2OutputFilename, $xmlOutputFilename,
						$finishTimeStamp, $finishUserName, $shippedTimeStamp, $shippedUserName, $shippedDistributionCentreReceivedTimeStamp,
						$shippedDistributionCentreReceivedDate, $shippedDistributionCentreReceivedUserName, $shippedDistributionCentreShippedTimeStamp,
						$shippedDistributionCentreShippedDate, $shippedDistributionCentreShippedUserName, $shippedStoreReceivedTimeStamp, $shippedStoreReceivedDate,
						$shippedStoreReceivedUserName, $shippedStoreCustomerCollectedTimeStamp, $shippedStoreCustomerCollectedDate, $shippedStoreCustomerCollectedUserName,
						$canModify, $canUploadFiles, $canUploadProductCodeOverride, $canUploadPageCountOverride, $canUploadEnableSaveOverride,
						$jobTicketEPWPartID, $pagesEPWPartID, $cover1EPWPartID, $cover2EPWPartID,
						$jobTicketEPWSubmissionID, $pagesEPWSubmissionID, $cover1EPWSubmissionID, $cover2EPWSubmissionID,
						$jobTicketEPWCompletionSatus, $pagesEPWCompletionStatus, $cover1EPWCompletionSatus, $cover2EPWCompletionStatus,
						$jobTicketEPWStatus, $pagesEPWStatus, $cover1EPWStatus, $cover2EPWStatus, $projectAIMode))
					{
						if ($stmt->execute())
						{
							if ($stmt->fetch())
							{
								$jobInfoArray['paymentreceivedtimestamp'] = $paymentReceivedTimeStamp;
								$jobInfoArray['paymentreceivedusername'] = $paymentReceivedUserName;
								$jobInfoArray['filesreceivedtimestamp'] = $filesReceivedTimeStamp;
								$jobInfoArray['filesreceivedusername'] = $filesReceivedUserName;
								$jobInfoArray['decrypttimestamp'] = $decryptTimeStamp;
								$jobInfoArray['decryptusername'] = $decryptUserName;
								$jobInfoArray['converttimestamp'] = $convertTimeStamp;
								$jobInfoArray['convertusername'] = $convertUserName;
								$jobInfoArray['convertoutputformatcode'] = $convertOutputFormatCode;
								$jobInfoArray['convertoutputformatname'] = $convertOutputFormatName;
								$jobInfoArray['productcover1format'] = $productCover1Format;
                            	$jobInfoArray['productcover2format'] = $productCover2Format;
								$jobInfoArray['outputtimestamp'] = $outputTimeStamp;
								$jobInfoArray['outputusername'] = $outputUserName;
								$jobInfoArray['jobticketoutputdevicename'] = $jobTicketOutputDeviceName;
								$jobInfoArray['pagesoutputdevicename'] = $pagesOutputDeviceName;
								$jobInfoArray['cover1outputdevicename'] = $cover1OutputDeviceName;
								$jobInfoArray['cover2outputdevicename'] = $cover2OutputDeviceName;
								$jobInfoArray['xmloutputdevicename'] = $xmlOutputDeviceName;
								$jobInfoArray['jobticketsubfoldername'] = $jobTicketSubfolderName;
								$jobInfoArray['pagessubfoldername'] = $pagesSubfolderName;
								$jobInfoArray['cover1subfoldername'] = $cover1SubfolderName;
								$jobInfoArray['cover2subfoldername'] = $cover2SubfolderName;
								$jobInfoArray['xmlsubfoldername'] = $xmlSubfolderName;
								$jobInfoArray['jobticketoutputfilename'] = $jobTicketOutputFilename;
								$jobInfoArray['pagesoutputfilename'] = $pagesOutputFilename;
								$jobInfoArray['cover1outputfilename'] = $cover1OutputFilename;
								$jobInfoArray['cover2outputfilename'] = $cover2OutputFilename;
								$jobInfoArray['xmloutputfilename'] = $xmlOutputFilename;
								$jobInfoArray['finishtimestamp'] = $finishTimeStamp;
								$jobInfoArray['finishusername'] = $finishUserName;
								$jobInfoArray['shippedtimestamp'] = $shippedTimeStamp;
								$jobInfoArray['shippedusername'] = $shippedUserName;
								$jobInfoArray['shippeddistributioncentrereceivedtimestamp'] = $shippedDistributionCentreReceivedTimeStamp;
								$jobInfoArray['shippeddistributioncentrereceiveddate'] = $shippedDistributionCentreReceivedDate;
								$jobInfoArray['shippeddistributioncentrereceivedusername'] = $shippedDistributionCentreReceivedUserName;
								$jobInfoArray['shippeddistributioncentreshippedtimestamp'] = $shippedDistributionCentreShippedTimeStamp;
								$jobInfoArray['shippeddistributioncentreshippeddate'] = $shippedDistributionCentreShippedDate;
								$jobInfoArray['shippeddistributioncentreshippedusername'] = $shippedDistributionCentreShippedUserName;
								$jobInfoArray['shippedstorereceivedtimestamp'] = $shippedStoreReceivedTimeStamp;
								$jobInfoArray['shippedstorereceiveddate'] = $shippedStoreReceivedDate;
								$jobInfoArray['shippedstorereceivedusername'] = $shippedStoreReceivedUserName;
								$jobInfoArray['shippedstorecustomercollectedtimestamp'] = $shippedStoreCustomerCollectedTimeStamp;
								$jobInfoArray['shippedstorecustomercollecteddate'] = $shippedStoreCustomerCollectedDate;
								$jobInfoArray['shippedstorecustomercollectedusername'] = $shippedStoreCustomerCollectedUserName;
								$jobInfoArray['canmodify'] = $canModify;
								$jobInfoArray['canuploadfiles'] = $canUploadFiles;
								$jobInfoArray['canuploadproductcodeoverride'] = $canUploadProductCodeOverride;
								$jobInfoArray['canuploadpagecountoverride'] = $canUploadPageCountOverride;
								$jobInfoArray['canuploadenablesaveoverride'] = $canUploadEnableSaveOverride;
								$jobInfoArray['jobticketepwpartid'] = $jobTicketEPWPartID;
								$jobInfoArray['pagesepwpartid'] = $pagesEPWPartID;
								$jobInfoArray['cover1epwpartid'] = $cover1EPWPartID;
								$jobInfoArray['cover2epwpartid'] = $cover2EPWPartID;
								$jobInfoArray['jobticketepwsubmissionid'] = $jobTicketEPWSubmissionID;
								$jobInfoArray['pagesepwsubmissionid'] = $pagesEPWSubmissionID;
								$jobInfoArray['cover1epwsubmissionid'] = $cover1EPWSubmissionID;
								$jobInfoArray['cover2epwsubmissionid'] = $cover2EPWSubmissionID;
								$jobInfoArray['jobticketepwcompletionstatus'] = $jobTicketEPWCompletionSatus;
								$jobInfoArray['pagesepwcompletionstatus'] = $pagesEPWCompletionStatus;
								$jobInfoArray['cover1epwcompletionstatus'] = $cover1EPWCompletionSatus;
								$jobInfoArray['cover2epwcompletionstatus'] = $cover2EPWCompletionStatus;
								$jobInfoArray['jobticketepwstatus'] = $jobTicketEPWStatus;
								$jobInfoArray['pagesepwstatus'] = $pagesEPWStatus;
								$jobInfoArray['cover1epwstatus'] = $cover1EPWStatus;
                                $jobInfoArray['cover2epwstatus'] = $cover2EPWStatus;
                                $jobInfoArray['projectaimode'] = $projectAIMode;
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

        $resultArray['jobticket'] = $jobTicketArray;
        $resultArray['jobinfo'] = $jobInfoArray;
        $resultArray['components'] = $componentsArray;
        $resultArray['componentmetadata'] = $componentMetaDataArray;

        return $resultArray;
    }

    static function getRow()
    {
        $tableName = $_POST['table'];
        $idColumnName = $_POST['columnname'];
        $idColumnValue = $_POST['columnvalue'];

        $resultArray = DatabaseObj::getRow($tableName, $idColumnName, $idColumnValue);

        return $resultArray;
    }

    /**
     * Attempt to find an offline order based on the batch reference and designer uuid
     *
     * @author Kevin Gale
     * @since Version 3.2.0
     */
    static function findOfflineOrder()
    {
        $resultArray = Array();
        $result = '';
        $resultParam = '';
        $isTempOrder = 0;
        $orderURL = '';
        $orderNumber = '';
        $sessionID = 0;
        $brandCode = '';
        $userID = 0;
        $contactFirstName = '';
        $contactLastName = '';

        $uploadBatchRef = $_POST['batchref'];
        $designerUUID = $_POST['uuid'];

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $stmt = $dbObj->prepare('SELECT `oh`.`ordernumber`, `oh`.`sessionid`, `oh`.`webbrandcode`, `oh`.`temporder`
                                        FROM `ORDERITEMS` oi
                                        LEFT JOIN `ORDERHEADER` oh ON `oh`.`id` = `oi`.`orderid`
                                        WHERE (`oi`.`uploadbatchref` = ?) AND (`oh`.`designeruuid` = ?)
                                        ORDER BY `oh`.`temporder` ASC, `oi`.`active` ASC');
            if ($stmt)
            {
                if ($stmt->bind_param('ss', $uploadBatchRef, $designerUUID))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($orderNumber, $sessionID, $brandCode, $isTempOrder))
                                {
                                    $stmt->fetch();
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'str_DatabaseError bind result1 ' . $dbObj->error;
                                }
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'findOfflineOrder store result1 ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not execute statement
                        $result = 'str_DatabaseError';
                        $resultParam = 'findOfflineOrder execute1 ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not bind parameters
                    $result = 'str_DatabaseError';
                    $resultParam = 'findOfflineOrder bind1 ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $result = 'str_DatabaseError';
                $resultParam = 'findOfflineOrder prepare1 ' . $dbObj->error;
            }

            // if we have found an order number format the order url
            if ($orderNumber != '')
            {
                // if this is still a temporary order build the url that we will use in production
                if ($isTempOrder == 1)
                {
                    $orderURL = UtilsObj::getBrandedWebUrl($brandCode) . '?fsaction=Order.offline&ref=' . $sessionID;
                }
            }
            else
            {
                // if we have not found an order number and we have a uuid attempt to find the last order that used the uuid
                if ($designerUUID != '')
                {
                    $stmt = $dbObj->prepare('SELECT `u`.`id`, `u`.`contactfirstname`, `u`.`contactlastname`
                                            FROM `USERS` u
                                            LEFT JOIN `ORDERHEADER` oh ON `u`.`id` = `oh`.`userid`
                                            WHERE `oh`.`designeruuid` = ?
                                            ORDER BY `oh`.`id` DESC LIMIT 1');
                    if ($stmt)
                    {
                        if ($stmt->bind_param('s', $designerUUID))
                        {
                            if ($stmt->execute())
                            {
                                if ($stmt->store_result())
                                {
                                    if ($stmt->num_rows > 0)
                                    {
                                        if ($stmt->bind_result($userID, $contactFirstName, $contactLastName))
                                        {
                                            $stmt->fetch();
                                        }
                                        else
                                        {
                                            $result = 'str_DatabaseError';
                                            $resultParam = 'str_DatabaseError bind result2 ' . $dbObj->error;
                                        }
                                    }
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'findOfflineOrder store result2 ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                // could not execute statement
                                $result = 'str_DatabaseError';
                                $resultParam = 'findOfflineOrder execute2 ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            // could not bind parameters
                            $result = 'str_DatabaseError';
                            $resultParam = 'findOfflineOrder bind2 ' . $dbObj->error;
                        }

                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                    else
                    {
                        // could not prepare statement
                        $result = 'str_DatabaseError';
                        $resultParam = 'findOfflineOrder prepare2 ' . $dbObj->error;
                    }
                }
            }

            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'findOfflineOrder connect ' . $dbObj->error;
        }

        $resultArray['ordernumber'] = $orderNumber;
        $resultArray['offlinesessionref'] = $sessionID;
        $resultArray['offlineurl'] = $orderURL;
        $resultArray['userid'] = $userID;
        $resultArray['contactfirstname'] = $contactFirstName;
        $resultArray['contactlastname'] = $contactLastName;

        return $resultArray;
    }

    /**
     * Attempt to create an offline order based on the data POSTed
     * The offline order is really a stored with a payment method of PAYLATER so that it can be completed afterwards
     *
     * @author Kevin Gale
     * @since Version 3.2.0
     */
    static function createOfflineOrder()
    {
        // include the application api module
        require_once('../AppAPI/AppAPI_model.php');

        global $ac_config;
        global $gSession;
        global $gAuthSession;

        $offlineSessionRef = 0;
        $orderNumber = '';
        $orderURL = '';

        $languageCode = $_POST['langcode'];

        // offline orders can only use the taopix cart
        $_POST['enableexternalcart'] = 0;

        // create an empty session
        // this will overwrite the current production session (the production session shouldn't be needed anymore for this call so this shouldn't cause any problems)
        $gSession = AuthenticateObj::createSessionDataArray();

        // perform the order API call that would be initiated by the designer
        $orderDataResult = AppAPI_model::prepareOrderData();
        $resultArray = AppAPI_model::order($orderDataResult);

        if ($resultArray['result'] == 'ORDER')
        {
            // if the result is to create an order then we continue
            // first overwrite the batch reference with the one from the offline order as we will use that to find the order number
            $count = count($gSession['items']);
            for($i = 0; $i < $count; $i++)
            {
                $gSession['items'][$i]['itemuploadbatchref'] = $_POST['batchref'];
            }

            // store the production site that is creating this offline order
            $gSession['order']['offlineordersitecode'] = $_POST['system4'];

            // grab the license key data for the supplied group code
            $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($gSession['licensekeydata']['groupcode']);

            // if we have been provided with a user account retreive it's details otherwise we use an empty user account
            if ($_POST['userid'] > 0)
            {
                $userAccountArray = DatabaseObj::getUserAccountFromID($_POST['userid']);
            }
            else
            {
                $userAccountArray = DatabaseObj::getEmptyUserAccount();
            }


            // start the web session
            $recordID = DatabaseObj::startSession($userAccountArray['recordid'], $userAccountArray['login'],
                            $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'],
                            $userAccountArray['usertype'], $userAccountArray['companycode'], $userAccountArray['owner'],
                            $userAccountArray['webbrandcode'], '', '', Array());

            $_GET['ref'] = $recordID;

            AuthenticateObj::setSessionWebBrand($licenseKeyArray['webbrandcode']);


            // perform the order initialization that would occur after logging in
            $gAuthSession = false;
            $resultArray = Order_model::initialize();

            if ($resultArray['result'] == '')
            {
                // if we don't have a user account assigned yet copy the address details POSTed into the session
                if ($_POST['userid'] == 0)
                {
                    $gSession['shipping'][0]['shippingcustomername'] = $_POST['name'];
                    $gSession['shipping'][0]['shippingcustomeraddress1'] = $_POST['address1'];
                    $gSession['shipping'][0]['shippingcustomeraddress2'] = $_POST['address2'];
                    $gSession['shipping'][0]['shippingcustomeraddress3'] = $_POST['address3'];
                    $gSession['shipping'][0]['shippingcustomeraddress4'] = $_POST['address4'];
                    $gSession['shipping'][0]['shippingcustomercity'] = $_POST['city'];
                    $gSession['shipping'][0]['shippingcustomercounty'] = $_POST['county'];
                    $gSession['shipping'][0]['shippingcustomerstate'] = $_POST['state'];
                    $gSession['shipping'][0]['shippingcustomerregioncode'] = $_POST['regioncode'];
                    $gSession['shipping'][0]['shippingcustomerregion'] = $_POST['regionname'];
                    $gSession['shipping'][0]['shippingcustomerpostcode'] = $_POST['postcode'];
                    $gSession['shipping'][0]['shippingcustomercountrycode'] = $_POST['countrycode'];
                    $gSession['shipping'][0]['shippingcustomercountryname'] = $_POST['countryname'];
                    $gSession['shipping'][0]['shippingcustomeremailaddress'] = $_POST['email'];
                    $gSession['shipping'][0]['shippingcustomertelephonenumber'] = $_POST['telephone'];
                    $gSession['shipping'][0]['shippingcontactfirstname'] = $_POST['contactfname'];
                    $gSession['shipping'][0]['shippingcontactlastname'] = $_POST['contactlname'];

                    Order_model::copyShippingAddressToBillingAddress();
                }

                // set the order as an offline, paylater order and insert it
                $gSession['order']['isofflineorder'] = 1;
                $gSession['order']['paymentmethodcode'] = 'PAYLATER';

                $resultArray = Order_model::complete();
                DatabaseObj::updateSession();

                if ($resultArray['result'] == '')
                {
                    $expiryTime = (int) UtilsObj::getArrayParam($ac_config, 'PAYLATEREXPIRYDAYS', 30);
                    DatabaseObj::disableSession($gSession['ref'], $expiryTime * 60 * 24);

                    $offlineSessionRef = $gSession['ref'];
                    $orderURL = UtilsObj::getBrandedWebUrl() . '?fsaction=Order.offline&ref=' . $offlineSessionRef;
                    $orderNumber = $gSession['order']['ordernumber'];
                }
            }
        }

        $resultArray['languagecode'] = $languageCode;
        $resultArray['offlinesessionref'] = $offlineSessionRef;
        $resultArray['offlineurl'] = $orderURL;
        $resultArray['ordernumber'] = $orderNumber;

        return $resultArray;
    }

    /**
     * Retrieves a list of brands from the database
     *
     * @static
     *
     * @return array
     *   the result array will contain the list of brands to be echo'd back to the calling application
     *
     * @author Kevin Gale
     * @since Version 3.2.0
     */
    static function getBrands()
    {
        return DatabaseObj::getBrandingList();
    }

	/**
	* Helper function to process a list of item actions provided in the POST parameters
	*
	* @author Kevin Gale
	* @since Version 5.0.0
	*/
	static function performItemActionListPOST()
    {
		$actionList = Array();

		$languageCode = $_POST['langcode'];

        $count = (int) $_POST['count'];
        for ($i = 1; $i <= $count; $i++)
        {
        	$actionItem = Array();
        	$actionItem['orderitemidlist'] = (string) $_POST['orderitemidlist' . $i];
        	$actionItem['action'] = (int) $_POST['action' . $i];
        	$actionItem['actionname'] = (string) $_POST['actionname' . $i];
        	$actionItem['date'] = (string) $_POST['date' . $i];
        	$actionItem['info'] = (string) $_POST['info' . $i];
        	$actionItem['value1'] = (string) $_POST['value1' . $i];
        	$actionItem['notify'] = (int) $_POST['notify' . $i];

        	$actionList[] = $actionItem;
        }

		$resultArray = self::performItemActionList($actionList);
		$resultArray['langcode'] = $_POST['langcode'];

        return $resultArray;
    }


	/**
	* Process a list of item actions
	*
	* @static
	*
	* @author Kevin Gale
	* @since Version 5.0.0
	*/
	static function performItemActionList($pActionList)
	{
		global $ac_config;
        global $gSession;

        $resultArray = Array();
		$result = '';
		$resultParam = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
        	$canChangeOrderStatus = true;
        	$previousAction = -1;
        	$previousOrderItemIDList = '';
        	$lowestItemStatus = 99999;
        	$hasImportedItems = false;
        	$hasRenderedFiles = false;
        	$hasRAWFiles = false;
        	$canPerformActions = true;
        	$orderItemIDArray = Array();
        	$idArrayCount = 0;
        	$orderItemDataArray = Array();

			$itemCount = count($pActionList);
			for ($i = 0; $i < $itemCount; $i++)
			{
				$actionItemArray = &$pActionList[$i];

				$orderItemIDList = $actionItemArray['orderitemidlist'];
				$action = $actionItemArray['action'];
				$actionName = $actionItemArray['actionname'];

				if ($orderItemIDList != $previousOrderItemIDList)
				{
					$previousOrderItemIDList = $orderItemIDList;

					$canChangeOrderStatus = true;
					$previousAction = -1;
					$lowestItemStatus = 99999;
					$hasImportedItems = false;
					$hasRenderedFiles = false;
					$hasRAWFiles = false;
					$canPerformActions = true;
					$orderID = 0;
					$tempOrder = 0;

					// make sure we have a list of values
					if ($orderItemIDList != '')
					{
						// make sure the list of values are unique
						$orderItemIDArray = explode(',', $orderItemIDList);
						$idArrayCount = count($orderItemIDArray);

						$orderItemUniqueIDArray = array_unique($orderItemIDArray);
						if ($idArrayCount == count($orderItemUniqueIDArray))
						{
							// make sure the list of values are numeric
							foreach($orderItemIDArray as $id)
							{
								if (! is_numeric($id))
								{
									$result = 'str_ErrorIDListInvalid';
									$resultParam = $orderItemIDList;
									break;
								}
							}

							// retrieve the status information from the database and set some flags we will use to validate the actions
							$matchedCount = 0;
							$lastOrderID = -1;
							if ($stmt = $dbObj->prepare('SELECT `oi`.`id`, `oi`.`uploaddatatype`, `oi`.`uploadmethod`, `oi`.`status`, `oi`.`active`, `oh`.`id`, `oh`.`temporder`
														FROM `ORDERITEMS` oi JOIN `ORDERHEADER` oh ON `oh`.`id` = `oi`.`orderid` WHERE `oi`.`id` IN (' . $orderItemIDList. ')'))
							{
								if ($stmt->bind_result($id, $uploadDataType, $uploadMethod, $status, $isActive, $orderID, $tempOrder))
								{
									if ($stmt->execute())
									{
										while ($stmt->fetch())
										{
											// make sure the items are from the same order
											if ($lastOrderID > -1)
											{
												if ($lastOrderID != $orderID)
												{
													$result = 'str_ErrorNotTheSameOrder';
													break;
												}

											}
											else
											{
												$lastOrderID = $orderID;
											}


											// if this active status is not 'in progress' then we cannot change the status for any in the batch
											if ($isActive != TPX_ORDER_STATUS_IN_PROGRESS)
											{
												$canChangeOrderStatus = false;
											}

											if ($uploadMethod != TPX_UPLOAD_DELIVERY_METHOD_INTERNET)
											{
												$hasImportedItems = true;
											}

											if (($uploadDataType == TPX_UPLOAD_DATA_TYPE_RAW) ||
												(($status >= TPX_ITEM_STATUS_RAW_FILES_READY_TO_PROCESS) && ($status < TPX_ITEM_STATUS_CONVERT_FILES_QUEUED)))
											{
												$hasRAWFiles = true;
											}

											if ($uploadDataType == TPX_UPLOAD_DATA_TYPE_RENDERED)
											{
												$hasRenderedFiles = true;
											}

											$lowestItemStatus = min($lowestItemStatus, $status);

											$matchedCount++;
										}
									}
								}
							}

							$stmt->free_result();
							$stmt->close();
							$stmt = null;

							if ($matchedCount != $idArrayCount)
							{
								// we did not retrieve the correct number of records so we should not continue
								$result = 'str_ErrorItemsMissing';
								$resultParam = $orderItemIDList;
								break;
							}
						}
						else
						{
							// the same id appeared more than once so we should not continue
							$result = 'str_ErrorDuplicateItem';
							$resultParam = $orderItemIDList;
							break;
						}
					}
					else
					{
						// the id list is empty so we should not continue
						$result = 'str_ErrorIDListInvalid';
						$resultParam = $orderItemIDList;
						break;
					}
				}

				$actionItemArray['orderid'] = $orderID;
				$actionItemArray['temporder'] = $tempOrder;


				// only continue if we have not received an error
				if ($result != '')
				{
					break;
				}


				// if this is a temporary order then all we can do is cancel
				if (($tempOrder == 1) && ($action != TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_CANCEL))
				{
					$result = 'str_ErrorActionNotAllowedTempOrder';
					$resultParam = $actionName;
					break;
				}


				// make sure we are allowed to perform actions which involve changing the status
				if (! $canPerformActions)
				{
					if (($action != TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SETONHOLD) && ($action != TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SETPAYMENTRECEIVED))
					{
						$result = 'str_ErrorActionBlocked';
						$resultParam = $actionName;
						break;
					}
				}


				// validate the action
				switch ($action)
				{
					case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_COMPLETE:
						// if this action is completing an order item then we are not allowed to change the status for items in the batch
						// we also do not allow items to be completed if they have never been decrypted

						if ($lowestItemStatus < TPX_ITEM_STATUS_DECRYPTED_FILES)
						{
							$result = 'str_ErrorActionNotAllowedTooLow';
							$resultParam = $actionName;
						}

						$canChangeOrderStatus = false;
						break;
					case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_CANCEL:
						// if this action is cancelling an order item then we are not allowed to change the status for items in the batch
						$canChangeOrderStatus = false;
						break;
					case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_ACTIVATE:
						// if this action is activating an order item then we are allowed to change the status for items in the batch
						$canChangeOrderStatus = true;
						$previousAction = -1;
						break;
					case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SETONHOLD:
						// this action doesn't affect the status but we only allow it if the order is active
						if (! $canChangeOrderStatus)
						{
							$result = 'str_ErrorActionNotAllowedInactiveOrder';
							$resultParam = $actionName;
						}
						break;
					case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SETPAYMENTRECEIVED:
						// this action doesn't affect the status but we only allow it if the order is active
						if (! $canChangeOrderStatus)
						{
							$result = 'str_ErrorActionNotAllowedInactiveOrder';
							$resultParam = $actionName;
						}
						break;
					default:
						// if we are not allowed to change the status fail with an error
						// TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SETONHOLD & TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SETPAYMENTRECEIVED are also prevented as these can only be performed on active orders

						if (! $canChangeOrderStatus)
						{
							$result = 'str_ErrorActionNotAllowedInactiveOrder';
							$resultParam = $actionName;
							break;
						}

						// only allow downloads if the location is remote and none of the items in the batch were imported (mailed)
						if (($action == TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_DOWNLOAD) &&
							(($ac_config['SERVERLOCATION'] != 'REMOTE') || ($lowestItemStatus < TPX_ITEM_STATUS_FILES_ON_REMOTE_FTP_SERVER) ||
							($hasImportedItems == true)))
						{
							$result = 'str_ErrorActionNotAllowed';
							$resultParam = $actionName;
							break;
						}

						// allow decryptions as long as none of the items in the batch have a status lower than files received
						if (($action == TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_DECRYPT) && ($lowestItemStatus < TPX_ITEM_STATUS_FILES_RECEIVED))
						{
							$result = 'str_ErrorActionNotAllowedTooLow';
							$resultParam = $actionName;
							break;
						}

						// allow conversions as long as none of the items in the batch have a status lower than decrypted
						if (($action == TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_CONVERT) && ($lowestItemStatus < TPX_ITEM_STATUS_DECRYPTED_FILES))
						{
							$result = 'str_ErrorActionNotAllowedTooLow';
							$resultParam = $actionName;
							break;
						}

						// allow conversions as long as none of the items in the batch have a project elements status
						if (($action == TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_CONVERT) && ($hasRAWFiles == true))
						{
							$result = 'str_ErrorActionNotAllowedRAW';
							$resultParam = $actionName;
							break;
						}

						// allow renderer submissions as long as none of the items in the batch have a rendered status
						if (($action == TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SUBMITTORENDERER) && ($hasRenderedFiles == true))
						{
							$result = 'str_ErrorActionNotAllowedRendered';
							$resultParam = $actionName;
							break;
						}

						// allow print as long as none of the items in the batch have a status lower than ready to print
						if (($action == TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_PRINT) && ($lowestItemStatus < TPX_ITEM_STATUS_READY_TO_PRINT))
						{
							$result = 'str_ErrorActionNotAllowedTooLow';
							$resultParam = $actionName;
							break;
						}

						// allow set as printed as long as none of the items in the batch have a status lower than decrypted
						if (($action == TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SETASPRINTED) && ($lowestItemStatus < TPX_ITEM_STATUS_DECRYPTED_FILES))
						{
							$result = 'str_ErrorActionNotAllowedTooLow';
							$resultParam = $actionName;
							break;
						}

						// allow set as finished as long as none of the items in the batch have a status lower than decrypted
						if (($action == TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SETASFINISHED) && ($lowestItemStatus < TPX_ITEM_STATUS_DECRYPTED_FILES))
						{
							$result = 'str_ErrorActionNotAllowedTooLow';
							$resultParam = $actionName;
							break;
						}

						// allow ship as long as none of the items in the batch have a status lower than decrypted
						if (($action == TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SHIP) && ($lowestItemStatus < TPX_ITEM_STATUS_DECRYPTED_FILES))
						{
							$result = 'str_ErrorActionNotAllowedTooLow';
							$resultParam = $actionName;
							break;
						}

						// make sure that the action is not lower than the previous action in this batch
						if ($action < $previousAction)
						{
							$result = 'str_ErrorActionNotAllowedOnHigher';
							$resultParam = $actionName;
							break;
						}

						// if this is one of the actions that is performed in the background we cannot allow any further actions in this batch
						if (($action == TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_DOWNLOAD) || ($action == TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_DECRYPT) ||
							($action == TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_CONVERT) || ($action == TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SUBMITTORENDERER) ||
							($action == TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_PRINT))
						{
							$canPerformActions = false;
						}

						$previousAction = $action;
				}


				// if we have an error we should not continue
				if ($result != '')
				{
					break;
				}
			}


			// if we have validated all of the actions perform them now
			if ($result == '')
			{
				// unset the variable as it is currently a reference to the last entry within the array
				// this is needed to prevent the array from being corrupted when we re-use the variable further down
				unset($actionItemArray);

				// grab the current date/time incase we need one and it has not been provided in the action
				$serverTime = DatabaseObj::getServerTime();

				// perform the actions
				for ($i = 0; $i < $itemCount; $i++)
				{
					$actionItemArray = $pActionList[$i];

					$actionDate = $actionItemArray['date'];
					if ($actionDate == '')
					{
						$actionDate = $serverTime;
					}

					$orderID = $actionItemArray['orderid'];
					$orderItemIDList = $actionItemArray['orderitemidlist'];

					switch ($actionItemArray['action'])
					{
						case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_DOWNLOAD:
							self::updateItemStatus($orderItemIDList, TPX_ITEM_STATUS_FILES_ON_REMOTE_FTP_SERVER, '');
							break;
						case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_DECRYPT:
							self::updateItemStatus($orderItemIDList, TPX_ITEM_STATUS_FILES_RECEIVED, '');
							break;
						case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_CONVERT:
							self::updateItemStatus($orderItemIDList, TPX_ITEM_STATUS_DECRYPTED_FILES, '');
							break;
						case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SUBMITTORENDERER:
							self::updateItemStatus($orderItemIDList, TPX_ITEM_STATUS_RAW_FILES_QUEUED_FOR_RENDER_SUBMISSION, '');
							break;
						case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_PRINT:
							self::updateItemStatus($orderItemIDList, TPX_ITEM_STATUS_READY_TO_PRINT, '');
							break;
						case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SETASPRINTED:
							self::updateItemOutputStatus($orderItemIDList, $gSession['userid'], TPX_ITEM_STATUS_PRINTED, '', 0, '', 0, 0, 0, 0);
							break;
						case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SETASFINISHED:
							self::updateItemFinishingStatus($orderItemIDList, $gSession['userid'], $actionDate, TPX_ITEM_STATUS_FINISHING_COMPLETE, '');
							break;
						case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SHIP:
							$orderItemIDArray = explode(',', $orderItemIDList);
							$idArrayCount = count($orderItemIDArray);
							for ($i2 = 0; $i2 < $idArrayCount; $i2++)
							{
								self::updateItemShippingStatus($orderID, $orderItemIDArray[$i2], $gSession['userid'], $actionDate, $actionItemArray['info'], $actionItemArray['notify']);
							}
							break;
						case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_COMPLETE:
							self::updateItemActiveStatus($orderItemIDList, $gSession['userid'], TPX_ORDER_STATUS_COMPLETED);
							break;
						case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_CANCEL:
							// if this is a temporary order then we cancel the entire order
							if ($actionItemArray['temporder'] == 1)
							{
								self::updateOrderActiveStatus($orderID, $gSession['userid'], TPX_ORDER_STATUS_CANCELLED);
							}
							else
							{
								self::updateItemActiveStatus($orderItemIDList, $gSession['userid'], TPX_ORDER_STATUS_CANCELLED);
							}
							break;
						case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_ACTIVATE:
							self::updateItemActiveStatus($orderItemIDList, $gSession['userid'], TPX_ORDER_STATUS_IN_PROGRESS);
							break;
						case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SETONHOLD:
							$onHoldArray = self::updateItemOnHoldStatus($orderItemIDList, $gSession['userid'], (int) $actionItemArray['value1'], $actionItemArray['info']);
							$result = $onHoldArray['error'];
							$resultParam = $onHoldArray['errorparam'];
							break;
						case TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_SETPAYMENTRECEIVED:
							self::updateOrderPaymentStatus($orderID, $gSession['userid'], (int) $actionItemArray['value1'], $actionDate);
							break;
					}
				}
			}

			$dbObj->close();
		}
		else
		{
			// could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'itemActionList connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;

		return $resultArray;
	}


	/**
	* Process status updates from an external print workflow
	*
	* @static
	*
	* @author Kevin Gale
	* @since Version 2016.4.0
	*/
    static function epwStatusUpdate($pSubmissionID, $pPartID, $pNewPartID, $pNewStatus, $pStatusData, $pCleanupProductionData)
	{
		$resultArray = array();
		$result = '';
		$resultParam = '';

		$id = 0;
		$epwStatus = TPX_EPW_STATUS_NOT_APPLICABLE;
		$cleanupProductionData = false;

       // if we have a new status, a submission ID and a part ID ID attempt to update the record within the database
       // as well as updating the status we could be replacing the part ID so we need to perform that after the status change
        if (($pNewStatus > TPX_EPW_STATUS_SUBMITTED) && ($pSubmissionID != '') && ($pPartID != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `jobticketepwstatus` = IF((`jobticketepwsubmissionid` = ?) AND (`jobticketepwpartid` = ?), ?, `jobticketepwstatus`),
                		`jobticketepwpartid` = IF((`jobticketepwsubmissionid` = ?) AND (`jobticketepwpartid` = ?), ?, `jobticketepwpartid`),
                        `pagesepwstatus` = IF((`pagesepwsubmissionid` = ?) AND (`pagesepwpartid` = ?), ?, `pagesepwstatus`),
                        `pagesepwpartid` = IF((`pagesepwsubmissionid` = ?) AND (`pagesepwpartid` = ?), ?, `pagesepwpartid`),
                        `cover1epwstatus` = IF((`cover1epwsubmissionid` = ?) AND (`cover1epwpartid` = ?), ?, `cover1epwstatus`),
                        `cover1epwpartid` = IF((`cover1epwsubmissionid` = ?) AND (`cover1epwpartid` = ?), ?, `cover1epwpartid`),
                        `cover2epwstatus` = IF((`cover2epwsubmissionid` = ?) AND (`cover2epwpartid` = ?), ?, `cover2epwstatus`),
                        `cover2epwpartid` = IF((`cover2epwsubmissionid` = ?) AND (`cover2epwpartid` = ?), ?, `cover2epwpartid`)
                        WHERE ((`jobticketepwsubmissionid` = ?) AND (`jobticketepwpartid` = ?)) OR ((`pagesepwsubmissionid` = ?) AND (`pagesepwpartid` = ?)) OR
                        ((`cover1epwsubmissionid` = ?) AND (`cover1epwpartid` = ?)) OR ((`cover2epwsubmissionid` = ?) AND (`cover2epwpartid` = ?))'))
                {
                    if ($stmt->bind_param('ssi' . 'sss' . 'ssi' . 'sss' . 'ssi' . 'sss' . 'ssi' . 'sss' . 'ssss' . 'ssss',
                    		$pSubmissionID, $pPartID, $pNewStatus,
                    		$pSubmissionID, $pPartID, $pNewPartID,
                    		$pSubmissionID, $pPartID, $pNewStatus,
                    		$pSubmissionID, $pPartID, $pNewPartID,
                    		$pSubmissionID, $pPartID, $pNewStatus,
                    		$pSubmissionID, $pPartID, $pNewPartID,
                            $pSubmissionID, $pPartID, $pNewStatus,
                            $pSubmissionID, $pPartID, $pNewPartID,
                            $pSubmissionID, $pPartID, $pSubmissionID, $pPartID,
                            $pSubmissionID, $pPartID, $pSubmissionID, $pPartID))
                    {
                        if ($stmt->execute())
                        {
                            // now find the orderitem that we should have updated
                            if ($stmt2 = $dbObj->prepare('SELECT `oi`.`id`, `orderid`, `jobticketepwstatus`, `pagesepwstatus`, `cover1epwstatus`, `cover2epwstatus`,
                                `jobticketepwcompletionstatus`, `pagesepwcompletionstatus`, `cover1epwcompletionstatus`, `cover2epwcompletionstatus`, `status`, `active`
                                FROM `ORDERITEMS` oi JOIN `ORDERHEADER` oh ON `oh`.`id` = `oi`.`orderid`
                                WHERE ((`jobticketepwsubmissionid` = ?) AND (`jobticketepwpartid` = ?)) OR ((`pagesepwsubmissionid` = ?) AND (`pagesepwpartid` = ?)) OR
                                ((`cover1epwsubmissionid` = ?) AND (`cover1epwpartid` = ?)) OR ((`cover2epwsubmissionid` = ?) AND (`cover2epwpartid` = ?)) AND (`oi`.`parentorderitemid` = 0)'))
                            {
                                if ($stmt2->bind_param('ssssssss', $pSubmissionID, $pNewPartID, $pSubmissionID, $pNewPartID, $pSubmissionID, $pNewPartID, $pSubmissionID, $pNewPartID))
                                {
                                    if ($stmt2->bind_result($id, $orderID, $jobTicketEPWStatus, $pagesEPWStatus, $cover1EPWStatus, $cover2EPWStatus, $jobTicketEPWCompletionStatus,
                                                            $pagesEPWCompletionStatus, $cover1EPWCompletionStatus, $cover2EPWCompletionStatus, $status, $activeStatus))
                                    {
                                        if ($stmt2->execute())
                                        {
                                            if ($stmt2->fetch())
                                            {
                                                // get rid of the statement now as we may be performing additional database commands based on the result
                                                $stmt2->free_result();
                                                $stmt2->close();
                                                $stmt2 = null;

                                                // determine the overall epw status for the orderitem
                                                if (($activeStatus == TPX_ORDER_STATUS_IN_PROGRESS) && ($status == TPX_ITEM_STATUS_PRINTING_SENT_TO_EXTERNAL_WORKFLOW))
                                                {
                                                    if ($pNewStatus == TPX_EPW_STATUS_FAILED)
                                                    {
                                                        $epwStatus = TPX_EPW_STATUS_FAILED;
                                                    }
                                                    elseif ($pNewStatus == TPX_EPW_STATUS_ABORTED)
                                                    {
                                                        $epwStatus = TPX_EPW_STATUS_ABORTED;
                                                    }
                                                    else
                                                    {
                                                        $epwStatus = TPX_EPW_STATUS_COMPLETED;
                                                        $workflowCompletionStatus = TPX_EPW_COMPLETION_STATUS_COMPLETED;

                                                        if ($jobTicketEPWStatus >= TPX_EPW_STATUS_SUBMITTED)
                                                        {
                                                            $epwStatus = min($jobTicketEPWStatus, $epwStatus);
                                                            $workflowCompletionStatus = min($jobTicketEPWCompletionStatus, $workflowCompletionStatus);
                                                        }

                                                        if ($pagesEPWStatus >= TPX_EPW_STATUS_SUBMITTED)
                                                        {
                                                            $epwStatus = min($pagesEPWStatus, $epwStatus);
                                                            $workflowCompletionStatus = min($pagesEPWCompletionStatus, $workflowCompletionStatus);
                                                        }

                                                        if ($cover1EPWStatus >= TPX_EPW_STATUS_SUBMITTED)
                                                        {
                                                            $epwStatus = min($cover1EPWStatus, $epwStatus);
                                                            $workflowCompletionStatus = min($cover1EPWCompletionStatus, $workflowCompletionStatus);
                                                        }

                                                        if ($cover2EPWStatus >= TPX_EPW_STATUS_SUBMITTED)
                                                        {
                                                            $epwStatus = min($cover2EPWStatus, $epwStatus);
                                                            $workflowCompletionStatus = min($cover2EPWCompletionStatus, $workflowCompletionStatus);
                                                        }
                                                    }

                                                    // if the epw status indicates the job has failed, has been aborted or has been completed update the orderitem status
                                                    if ($epwStatus == TPX_EPW_STATUS_FAILED)
                                                    {
                                                    	// update the order item status
                                                        $smarty = SmartyObj::newSmarty('AppProductionAPI');
                                                        SmartyObj::replaceParams($smarty, 'str_MessageExternalWorkflowJobFailed', $pSubmissionID);

                                                        self::updateItemOutputStatus($id, -1, TPX_ITEM_STATUS_PRINTING_FILES_ERROR,
                                                        		$smarty->get_template_vars('str_MessageExternalWorkflowJobFailed'), 0, '', 0, 0, 0, 0);

                                                    	$cleanupProductionData = $pCleanupProductionData;
                                                    }
                                                    elseif ($epwStatus == TPX_EPW_STATUS_ABORTED)
                                                    {
                                                    	// update the order item status
                                                        $smarty = SmartyObj::newSmarty('AppProductionAPI');
                                                        SmartyObj::replaceParams($smarty, 'str_MessageExternalWorkflowJobAborted', $pSubmissionID);

                                                        self::updateItemOutputStatus($id, -1, TPX_ITEM_STATUS_PRINTING_FILES_ERROR,
                                                        		$smarty->get_template_vars('str_MessageExternalWorkflowJobAborted'), 0, '', 0, 0, 0, 0);

                                                    	$cleanupProductionData = $pCleanupProductionData;
                                                    }
                                                    elseif ($epwStatus == TPX_EPW_STATUS_COMPLETED)
                                                    {
                                                    	// update the order item status
                                                        switch ($workflowCompletionStatus)
                                                        {
                                                            case TPX_EPW_COMPLETION_STATUS_PRINTED:
                                                            {
                                                                self::updateItemOutputStatus($id, -1, TPX_ITEM_STATUS_PRINTED, '', 0, '', 0, 0, 0, 0);
                                                                break;
                                                            }
                                                            case TPX_EPW_COMPLETION_STATUS_FINISHED:
                                                            {
                                                                self::updateItemFinishingStatus($id, -1, DatabaseObj::getServerTime(), TPX_ITEM_STATUS_FINISHING_COMPLETE, '');
                                                                break;
                                                            }
                                                            case TPX_EPW_COMPLETION_STATUS_SHIPPED:
                                                            {
                                                                self::updateItemShippingStatus($orderID, $id, -1, DatabaseObj::getServerTime(), $pStatusData, TPX_PRODUCTIONAUTOMATION_NOTIFY_NO);
                                                                break;
                                                            }
                                                            case TPX_EPW_COMPLETION_STATUS_COMPLETED:
                                                            {
                                                                self::updateItemActiveStatus($id, -1, TPX_ORDER_STATUS_COMPLETED);
                                                                break;
                                                            }
                                                        }

                                                        $cleanupProductionData = $pCleanupProductionData;
                                                    }


                                                    // post a production event to cleanup the production data
                                                    if ($cleanupProductionData)
                                                    {
                                                    	 self::createProductionEvent($id, TPX_PRODUCTIONAUTOMATION_ACTION_ORDER_DELETEOUTPUTDATA);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                // if we still have the statement get rid of it now
                                if ($stmt2)
                                {
                                    $stmt2->free_result();
                                    $stmt2->close();
                                    $stmt2 = null;
                                }
                            }
                        }
                    }
                    else
					{
						// could not bind parameters
						$result = 'str_DatabaseError';
						$resultParam = 'epwStatusUpdate bind ' . $dbObj->error;
					}

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = 'epwStatusUpdate prepare ' . $dbObj->error;
				}

                $dbObj->close();
            }
            else
			{
				// could not open database connection
				$result = 'str_DatabaseError';
				$resultParam = 'epwStatusUpdate connect ' . $dbObj->error;
			}
        }

        $resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;
		$resultArray['recordid'] = $id;
		$resultArray['epwstatus'] = $epwStatus;

    	return $resultArray;
	}


    /**
     * Process the JDF status updates
     *
     * @static
     *
     * @author Kevin Gale
     * @since Version 3.3.0
     */
	static function epwHPJDFCallback()
    {
        $newStatus = TPX_EPW_STATUS_RECEIVED;
        $jobTicketID = '';
        $queueEntryID = '';

        $xmlData = file_get_contents('php://input');

        // find the start of the ReturnQueueEntryParams element
        $charPos = strpos($xmlData, '<ReturnQueueEntryParams ');
        if ($charPos !== false)
        {
            $xmlData = substr($xmlData, $charPos + 24);

            // remove any double quotes
            $xmlData = str_replace('"', '', $xmlData);


            // look for the status and the queue entry ID
            $xmlDataArray = explode(' ', $xmlData);
            for ($i = 0; $i < count($xmlDataArray); $i++)
            {
                $xmlLineArray = explode('=', $xmlDataArray[$i]);

                if (count($xmlLineArray) == 2)
                {
                    if ($xmlLineArray[0] == 'Aborted')
                    {
                        $jobTicketID = (string)$xmlLineArray[1];
                        $newStatus = TPX_EPW_STATUS_ABORTED;
                    }
                    elseif ($xmlLineArray[0] == 'Completed')
                    {
                        $jobTicketID = (string)$xmlLineArray[1];
                        $newStatus = TPX_EPW_STATUS_COMPLETED;
                    }
                    elseif ($xmlLineArray[0] == 'QueueEntryID')
                    {
                        $queueEntryID = (string)$xmlLineArray[1];
                    }
                }
            }

            self::epwStatusUpdate($queueEntryID, $jobTicketID, $jobTicketID, $newStatus, '', true);
        }
    }


    static function epwHPPrintOSCallback()
    {
    	// retrieve the input data and attempt to parse it as json
		$inputData = file_get_contents('php://input');
		if ($inputData != '')
		{
			$callbackArray = json_decode($inputData, true);

			// grab some of the parameters that should always be present and only continue if they are not empty
			$id = UtilsObj::getArrayParam($callbackArray, 'id', '');
			$submissionID = UtilsObj::getArrayParam($callbackArray, 'orderid', '');
			$newStatusString = UtilsObj::getArrayParam($callbackArray, 'status', '');

			if (($id != '') && ($submissionID != '') && ($newStatusString != ''))
			{
				// convert the status string into a recognised status
				$newStatus = TPX_EPW_STATUS_NOT_APPLICABLE;
				$statusData = '';
				$criticalCallback = false;

				switch ($newStatusString)
				{
					case 'orderreceived':
					{
						$newStatus = TPX_EPW_STATUS_RECEIVED;

						break;
					}
					case 'orderfailed':
					{
						$newStatus = TPX_EPW_STATUS_FAILED;

						break;
					}
					case 'printready':
					{
						$newStatus = TPX_EPW_STATUS_PRINTREADY;

						break;
					}
					case 'componentprinted':
					{
						$newStatus = TPX_EPW_STATUS_PRINTED;

						break;
					}
					case 'ordercancelled':
					{
						$newStatus = TPX_EPW_STATUS_ABORTED;

						break;
					}
					case 'ordershipped':
					{
						$newStatus = TPX_EPW_STATUS_COMPLETED;
						$statusData = UtilsObj::getArrayParam($callbackArray, 'trackingnumber', '');

						break;
					}
				}


				// if the status has been recognised we can proceed with the processing
				if ($newStatus > TPX_EPW_STATUS_NOT_APPLICABLE)
				{
					if (array_key_exists('items', $callbackArray))
					{
						// process each item within the callback (we should only have one)
						$itemArray = $callbackArray['items'];
						$itemCount = count($itemArray);
						for ($i = 0; $i < $itemCount; $i++)
						{
							$theItem = $itemArray[$i];

							// process the components
							// depending on the callback, the item might contain every component assigned to the job or specific components for the job
							$componentArray = $theItem['components'];
							$componentCount = count($componentArray);
							for ($i2 = 0; $i2 < $componentCount; $i2++)
							{
								$theComponent = $componentArray[$i2];

								$componentID = $theComponent['id'];
								$componentCode = UtilsObj::getArrayParam($theComponent, 'code', '');

								// configure the update based on the status we are processing
								$partID = $componentID;
								$newPartID = $componentID;

								if ($newStatus == TPX_EPW_STATUS_RECEIVED)
								{
									// order received is the first callback we ever receive from printos
									// it is also one of the most important callbacks as it contains the taopix part identifier (component code) and the printos part identifier (component id)
									// some of the other printos callbacks only contain the component id so we switch them them in the database
									$partID = $componentCode;
									$newPartID = $componentID;

									// flag this callback as critical
									$criticalCallback = true;
								}
								elseif ($newStatus == TPX_EPW_STATUS_FAILED)
								{
									// order failed can be the second callback we receive from printos
									// it can occur very quickly after order received so we flag it as critical so we wait until the part identifiers have been switched
									$criticalCallback = true;
								}


								// set the new status if we have been provided with all of the information
								if (($partID != '') && ($newPartID != ''))
								{
									// determine how we will handle the status update not succeeding
									if ($criticalCallback)
									{
										// this is a critical callback so we give it special treatment
										if ($i2 == 0)
										{
											// this is the first component so allow for multiple retries and start with an initial delay to increase the chance that the data will be there first time
											$retryCount = 20;
											UtilsObj::wait(5);

										}
										else
										{
											// this is not the first component so set the retry count to 1 as it should be possible to find a matching record first time
											$retryCount = 1;
										}
									}
									else
									{
										// this is not a critical callback so set the retry count to 1 as it should be possible to find a matching record first time
										$retryCount = 1;
									}


									// perform the update
									while ($retryCount > 0)
									{
										UtilsObj::resetPHPScriptTimeout(10);

										// update the status (+ potentially remap the data)
										$updateResultArray = self::epwStatusUpdate($submissionID, $partID, $newPartID, $newStatus, $statusData, true);

										// if we have not updated the database it could be because the data was never remapped (maybe the order received callback was never received or processed)
										// in this situation we try to fulfill aborted & completed status updates by using the component code we extracted which will match an unmapped column value
										if ($updateResultArray['recordid'] == 0)
										{
											if (($componentCode != '') && (($newStatus == TPX_EPW_STATUS_ABORTED) || ($newStatus == TPX_EPW_STATUS_COMPLETED)))
											{
												$updateResultArray = self::epwStatusUpdate($submissionID, $componentCode, $newPartID, $newStatus, $statusData, true);
											}
										}

										// if we have updated the database there is no need to retry again for this component
										if ($updateResultArray['recordid'] > 0)
										{
											break;
										}

										// we did not update so retry this component
										$retryCount--;
										if ($retryCount > 0)
										{
											UtilsObj::wait(5);
										}
									}
								}
							}
						}
					}
				}
			}
		}
    }


	/**
     * Create a production event
	 *
     * @static
     *
     * @since Version 2016.4.0
	 */
	static function createProductionEvent($pOrderItemID, $pActionCode)
    {
    	$resultArray = array();
    	$result = '';
    	$resultParam = '';
    	$recordID = 0;

    	$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('INSERT INTO `PRODUCTIONEVENTS` (`id`, `datecreated`, `companycode`, `owner`, `userid`, `orderitemid`, `actioncode`, `status`)' .
										' SELECT 0, NOW(), `currentcompanycode`, `currentowner`, `userid`, `id`, ?, ' . TPX_PRODUCTION_EVENT_STATUS_IDLE .
										' FROM `ORDERITEMS` WHERE `id` = ?'))
			{
				if ($stmt->bind_param('ii', $pActionCode, $pOrderItemID))
				{
					if ($stmt->execute())
					{
						$recordID = $dbObj->insert_id;
					}
					else
					{
						// could not execute statement
						$result = 'str_DatabaseError';
						$resultParam = 'createProductionEvent execute ' . $dbObj->error;
					}
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'createProductionEvent bind ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'createProductionEvent prepare ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'createProductionEvent connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;
		$resultArray['recordid'] = $recordID;

    	return $resultArray;
    }


	/**
     * Retrieve a list of production events for the production site
	 *
     * @static
     *
     * @since Version 2016.1.0
	 */
	static function getProductionEvents()
    {
    	global $gConstants;

        $resultArray = array();
        $productionEventList = array();

		$owner = $_POST['owner'];
		$lastEventID = (int) $_POST['lasteventid'];

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
        	$typesArray = array();
        	$paramArray = array();

			$sqlStatement = 'SELECT `pe`.`id`, `pe`.`actioncode`, `oi`.`orderid`, `oh`.`groupcode`, `oh`.`ordernumber`, `oh`.`itemcount`,
					`oi`.`id`, `oi`.`itemnumber`, `oi`.`uploadref`, `oi`.`jobticketoutputdevicecode`, `oi`.`pagesoutputdevicecode`,
					`oi`.`cover1outputdevicecode`, `oi`.`cover2outputdevicecode`, `oi`.`xmloutputdevicecode`, `oi`.`jobticketoutputsubfoldername`,
					`oi`.`pagesoutputsubfoldername`, `oi`.`cover1outputsubfoldername`, `oi`.`cover2outputsubfoldername`, `oi`.`xmloutputsubfoldername`,
					`oi`.`jobticketoutputfilename`, `oi`.`pagesoutputfilename`, `oi`.`cover1outputfilename`, `oi`.`cover2outputfilename`, `oi`.`xmloutputfilename`
					FROM `PRODUCTIONEVENTS` pe LEFT JOIN `ORDERITEMS` oi ON (`oi`.`id` = `pe`.`orderitemid`)
					LEFT JOIN `ORDERHEADER` oh ON (`oh`.`id` = `oi`.`orderid`)
					WHERE (`pe`.`id` > ?) AND (`pe`.`status` <= ' . TPX_PRODUCTION_EVENT_STATUS_RUNNING . ')';
			$typesArray[] = 'i';
            $paramArray[] = $lastEventID;

			if ($gConstants['optionms'])
			{
				$sqlStatement .= ' AND (`pe`.`owner` = ?)';
				$typesArray[] = 's';
            	$paramArray[] = $owner;
			}

            if ($stmt = $dbObj->prepare($sqlStatement))
			{
				if (DatabaseObj::bindParams($stmt, $typesArray, $paramArray))
				{
					if ($stmt->bind_result($eventID, $actionCode, $orderID, $groupCode, $orderNumber, $itemCount, $orderItemID, $itemNumber, $uploadRef,
							$jobTicketOutputDeviceCode, $pagesOutputDeviceCode, $cover1OutputDeviceCode, $cover2OutputDeviceCode, $xmlOutputDeviceCode,
							$jobTicketSubfolderName, $pagesSubfolderName, $cover1SubfolderName, $cover2SubfolderName, $xmlSubfolderName,
							$jobTicketOutputFilename, $pagesOutputFilename, $cover1OutputFilename, $cover2OutputFilename, $xmlOutputFilename))
					{
						if ($stmt->execute())
						{
							while ($stmt->fetch())
							{
								$theEvent = array();
								$theEvent['eventid'] = $eventID;
								$theEvent['actioncode'] = $actionCode;
								$theEvent['orderid'] = $orderID;
								$theEvent['groupcode'] = $groupCode;
								$theEvent['ordernumber'] = $orderNumber;
								$theEvent['itemcount'] = $itemCount;
								$theEvent['orderitemid'] = $orderItemID;
								$theEvent['itemnumber'] = $itemNumber;
								$theEvent['uploadref'] = $uploadRef;
								$theEvent['jobticketoutputdevicecode'] = $jobTicketOutputDeviceCode;
								$theEvent['pagesoutputdevicecode'] = $pagesOutputDeviceCode;
								$theEvent['cover1outputdevicecode'] = $cover1OutputDeviceCode;
								$theEvent['cover2outputdevicecode'] = $cover2OutputDeviceCode;
								$theEvent['xmloutputdevicecode'] = $xmlOutputDeviceCode;
								$theEvent['jobticketsubfoldername'] = $jobTicketSubfolderName;
								$theEvent['pagessubfoldername'] = $pagesSubfolderName;
								$theEvent['cover1subfoldername'] = $cover1SubfolderName;
								$theEvent['cover2subfoldername'] = $cover2SubfolderName;
								$theEvent['xmlsubfoldername'] = $xmlSubfolderName;
								$theEvent['jobticketoutputfilename'] = $jobTicketOutputFilename;
								$theEvent['pagesoutputfilename'] = $pagesOutputFilename;
								$theEvent['cover1outputfilename'] = $cover1OutputFilename;
								$theEvent['cover2outputfilename'] = $cover2OutputFilename;
								$theEvent['xmloutputfilename'] = $xmlOutputFilename;

								$productionEventList[] = $theEvent;
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

		$resultArray['eventlist'] = $productionEventList;

        return $resultArray;
    }


	/**
     * Update the status of the production event
	 *
     * @static
     *
     * @since Version 2016.1.0
	 */
	static function updateProductionEventStatus()
    {
    	$result = '';
        $resultParam = '';

		$eventID = (int) $_POST['eventid'];
		$eventMessage = $_POST['eventmessage'];
		$eventStatus = (int) $_POST['eventstatus'];

		$dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
        	if ($stmt = $dbObj->prepare('UPDATE `PRODUCTIONEVENTS` SET `message` = ?, `status` = ? WHERE `id` = ?'))
			{
				if ($stmt->bind_param('ssi', $eventMessage, $eventStatus, $eventID))
				{
					if (! $stmt->execute())
					{
						$result = 'str_DatabaseError';
						$resultParam = 'updateProductionEventStatus execute ' . $dbObj->error;
					}
				}
				else
				{
					// could not bind params
					$result = 'str_DatabaseError';
					$resultParam = 'updateProductionEventStatus bindparams ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'updateProductionEventStatus prepare ' . $dbObj->error;
			}

        	$dbObj->close();
        }

		$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

		return $resultArray;
    }

    static function getCompanionAlbumOrderItemRecordIDsFromParentRecordID($pOrderItemIDList)
    {
		$companionOrderItemRecordIDArray = array();
		$orderItemRecordID = 0;

		$dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			$stmt = $dbObj->prepare('SELECT `id` FROM `ORDERITEMS` WHERE `parentorderitemid` IN (' . $pOrderItemIDList. ')');

			if ($stmt)
			{
				if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						if ($stmt->num_rows > 0)
						{
							if ($stmt->bind_result($orderItemRecordID))
							{
								while($stmt->fetch())
								{
									$companionOrderItemRecordIDArray[] = $orderItemRecordID;
								}
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

		return $companionOrderItemRecordIDArray;
    }
}

?>
