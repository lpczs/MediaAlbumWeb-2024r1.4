<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsDataExport.php');

class AdminCustomers_model
{
	static function displayList()
	{
		global $gSession;

        $resultArray = array();
        $summaryArray = array();
        $start = (integer)$_POST['start'];
        $limit = (integer)$_POST['limit'];
        $sortBy = (isset($_POST['sort'])) ? $_POST['sort'] : '';
        $sortDir = (isset($_POST['dir'])) ? $_POST['dir'] : '';
		$totalCount = 0;
		$hideInactive = 0;
		$hideInactiveStatementSegment = '';
		$lastLoggedInSQL = '';
		$accountLocked = 0;

		if (isset($_POST['hideInactive']))
		{
			$hideInactive = filter_input(INPUT_POST,'hideInactive', FILTER_SANITIZE_NUMBER_INT);
		}

		// grab the setting to determine the number of days since last login to be used in where clause
		if (isset($_POST['lastloggedinfilterdays']))
		{
			$lastLoggedInFilterDays = filter_input(INPUT_POST,'lastloggedinfilterdays', FILTER_SANITIZE_NUMBER_INT);
		}

		// grab the setting to determine if the filter based off last login date is turned on.
		if (isset($_POST['lastloggedinfilteron']))
		{
			$lastLoggedInFilterOn = filter_input(INPUT_POST,'lastloggedinfilteron', FILTER_SANITIZE_NUMBER_INT);
		}

        $searchFields = UtilsObj::getPOSTParam('fields');

        $typesArray = array();
		$paramArray = array();
		$stmtArray = array();

		$smarty = SmartyObj::newSmarty('AdminCustomers');

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
    					case 'accountcode': $value = '`us`.`accountcode`'; break;
    					case 'contactname': $value = '`us`.`contactfirstname`'; $stmtArray[] = '(`us`.`contactlastname` LIKE ?)'; $paramArray[] = '%'.$searchQuery.'%'; $typesArray[] = 's'; break;
    					case 'postcode': $value = '`us`.`postcode`';	break;
    					case 'login': $value = '`us`.`login`';	break;
    					case 'emailaddress': $value = '`us`.`emailaddress`';	break;
    					case 'headertext': $value = '`us`.`webbrandcode`';	break;
    				}

					$stmtArray[] = '('.$value.' LIKE ?)';
					$paramArray[] = '%'.$searchQuery.'%';
					$typesArray[] = 's';
				}
			}
			else
			{
				if ($hideInactive)
				{
					$hideInactiveStatementSegment = 'AND (active = 1)';
				}
			}
		}
		else
		{
			if ($hideInactive)
			{
				$hideInactiveStatementSegment = 'AND (active = 1)';
			}
		}

		$customSort = '';
    	if ($sortBy != '')
    	{
    		switch ($sortBy)
    		{
    			case 'groupcode': $sortBy = 'us.groupcode '.$sortDir; break;
    			case 'accountcode': $sortBy = 'us.accountcode '.$sortDir; break;
    			case 'companyname': $sortBy = 'us.companyname '.$sortDir; break;
    			case 'contactname': $sortBy = 'us.contactfirstname '.$sortDir.', us.contactlastname '.$sortDir; break;
    			case 'postcode': $sortBy = 'us.postcode '.$sortDir;	break;
    			case 'countryname': $sortBy = 'us.countryname '.$sortDir; break;
    			case 'login': $sortBy = 'us.login '.$sortDir;	break;
    			case 'emailaddress': $sortBy = 'us.emailaddress '.$sortDir;	break;
    			case 'isactive': $sortBy = 'us.active '.$sortDir; break;
    			case 'accountlocked': $sortBy = 'us.nextvalidlogindate '.$sortDir; break;
    		}
    		$customSort = ', '. $sortBy;
    	}


		$companyCode = (isset($_POST['companyCode'])) ? $_POST['companyCode'] : '';

		if ($companyCode != '')
	    {
	    	if ($companyCode == 'GLOBAL')
	    	{
	    		$stmtArray[] = '(`'. 'companycode' .'` = ?)';
				$paramArray[] = '';
				$typesArray[] = 's';
	    	}
	    	else
	    	{
	    		$stmtArray[] = '(`'. 'companycode' .'` LIKE ?)';
				$paramArray[] = '%'. $companyCode .'%';
				$typesArray[] = 's';
	    	}
	    }

		// filter the result set based on the redaction state of the account
		$options = array('options' => array('default' => 0, 'min_range' => 0, 'max_range' => 99));
		$redactionState = filter_input(INPUT_POST, "redactionStatus", FILTER_VALIDATE_INT, $options);

		if ($redactionState != 0)
	    {
			if ($redactionState == 3)
			{
	    		$stmtArray[] = '((`redactionprogress` = ?) OR (`redactionprogress` = ?))';
				$paramArray[] = '3';
				$paramArray[] = '4';
				$typesArray[] = 'i';
				$typesArray[] = 'i';
			}
			else if ($redactionState == 6)
			{
	    		$stmtArray[] = '((`redactionprogress` = ?) OR (`redactionprogress` = ?))';
				$paramArray[] = '6';
				$paramArray[] = '7';
				$typesArray[] = 'i';
				$typesArray[] = 'i';
			}
			else
			{
	    		$stmtArray[] = '(`redactionprogress` = ?)';
				$paramArray[] = $redactionState;
				$typesArray[] = 'i';
	    	}
	    }

		if ($lastLoggedInFilterOn == 1)
	    {
	    	// filter users where the lastlogin date is less than X number of days compared to the current date
	    	$lastLoggedInSQL = ' AND (lastlogindate) < (NOW() - INTERVAL ? DAY)';

			// We need to add the bind type and bind params at start of the arrays so
			// they can be used in the SQL where statement. This is due to the stmtArray
			// below being created using a join using OR.
			array_unshift($typesArray, 'i');
			array_unshift($paramArray, $lastLoggedInFilterDays);
	    }

    	$dbObj = DatabaseObj::getGlobalDBConnection();
    	if ($dbObj)
		{
			if (count($stmtArray) > 0)
            {
                $stmtArray = ' AND (' . join(' OR ', $stmtArray) . ')';
            }
            else
            {
                $stmtArray = '';
            }

			switch ($gSession['userdata']['usertype'])
			{
				case TPX_LOGIN_SYSTEM_ADMIN:
					$stmt = $dbObj->prepare('SELECT SQL_CALC_FOUND_ROWS us.id, us.login, us.groupcode, us.accountcode, us.companyname, us.postcode, us.countryname, us.emailaddress, us.contactfirstname,
											us.contactlastname, us.creditlimit, us.accountbalance, us.webbrandcode, us.redactionprogress,
											IF(lastlogindate = "0000-00-00 00:00:00", "",TIMESTAMPDIFF(DAY, lastlogindate, NOW())) as lastlogindate,
											us.active, IF ( TIMESTAMPDIFF(SECOND, UTC_TIMESTAMP(), `nextvalidlogindate`) > 3, 1, 0) as accountlocked
											FROM `USERS` us WHERE us.customer = 1 '. $hideInactiveStatementSegment . $lastLoggedInSQL . $stmtArray . ' ORDER BY us.groupcode'.
											$customSort. ' LIMIT ' . $limit . ' OFFSET ' . $start);
				break;
				case TPX_LOGIN_COMPANY_ADMIN:
					$stmt = $dbObj->prepare('SELECT SQL_CALC_FOUND_ROWS us.id, us.login, us.groupcode, us.accountcode, us.companyname, us.postcode, us.countryname, us.emailaddress, us.contactfirstname,
							us.contactlastname, us.creditlimit, us.accountbalance, us.webbrandcode, us.redactionprogress,
							IF(lastlogindate = "0000-00-00 00:00:00", "",TIMESTAMPDIFF(DAY, lastlogindate, NOW())) as lastlogindate,
							us.active, IF ( TIMESTAMPDIFF(SECOND, UTC_TIMESTAMP(), `nextvalidlogindate`) > 3, 1, 0) as accountlocked
							FROM USERS us JOIN LICENSEKEYS lk ON (us.groupcode = lk.groupcode) WHERE us.customer = 1
							AND lk.companycode = ? '. $lastLoggedInSQL . $stmtArray .' ORDER BY us.groupcode'.$customSort. ' LIMIT ' . $limit . ' OFFSET ' . $start);
					array_unshift($typesArray, 's');
					array_unshift($paramArray, $gSession['userdata']['companycode']);
				break;
			}

			if ($stmt)
			{
				$bindOK = DatabaseObj::bindParams($stmt, $typesArray, $paramArray);

				if ($bindOK)
				{
					if ($stmt->bind_result($id, $login, $groupCode, $accountCode, $companyName, $postCode, $countryName, $emailAddress, $contactFirstName, $contactLastName, $creditLimit, $accountBalance, $brandCode, $redactionProgress, $lastLoginDays, $isActive, $accountLocked))
					{
						if ($stmt->execute())
						{
							while ($stmt->fetch())
							{
								if ($brandCode == '')
								{
									$brandCode = $smarty->get_config_vars('str_LabelDefault');
								}

								$userItem['recordid'] = "'" . UtilsObj::ExtJSEscape($id) . "'";
								$userItem['login'] = "'" . UtilsObj::ExtJSEscape($login) . "'";
								$userItem['headertext'] = "'" . $brandCode . " - " . $smarty->get_config_vars('str_LabelLicenseKey') . " " . UtilsObj::ExtJSEscape($groupCode) . "'";
								$userItem['groupcode'] = "'" . UtilsObj::ExtJSEscape($groupCode) . "'";
								$userItem['accountcode'] = "'" . UtilsObj::ExtJSEscape($accountCode) . "'";
								$userItem['companyname'] = "'" . UtilsObj::ExtJSEscape($companyName) . "'";
								$userItem['postcode'] = "'" . UtilsObj::ExtJSEscape($postCode) . "'";
								$userItem['countryname'] = "'" . UtilsObj::ExtJSEscape($countryName) . "'";
								$userItem['emailaddress'] = "'" . UtilsObj::ExtJSEscape($emailAddress) . "'";
								$userItem['contactname'] = "'" . UtilsObj::ExtJSEscape($contactFirstName .' '.$contactLastName) . "'";
								$userItem['creditlimit'] = "'" . UtilsObj::ExtJSEscape($creditLimit) . "'";
								$userItem['accountbalance'] = "'" . UtilsObj::ExtJSEscape($accountBalance) . "'";
								$userItem['redactionprogress'] = "'" . UtilsObj::ExtJSEscape($redactionProgress) . "'";
								$userItem['lastloggedin'] = "'" . UtilsObj::ExtJSEscape($lastLoginDays) . "'";
								$userItem['isactive'] = "'" . UtilsObj::ExtJSEscape($isActive) . "'";
								$userItem['accountlocked'] = "'" . UtilsObj::ExtJSEscape($accountLocked) . "'";
								array_push($resultArray, '['. join(',', $userItem) . ']');
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

	static function customerActivate()
	{
		global $gSession;
		global $gConstants;

		$customerList  = explode(',',$_POST['idlist']);
        $customerCount = count($customerList);
        $result = '';
		$resultParam = '';
		$isActive = $_POST['active'];

		// determine outside of the loop if we need to call the external script
		$hasExternalLoginScript = false;
		if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
		{
			require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

			if (method_exists('ExternalCustomerAccountObj', 'updateActiveStatus'))
			{
				$hasExternalLoginScript = true;
			}
		}


		// update the records
		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			for ($i = 0; $i < $customerCount; $i++)
			{
				$id = $customerList[$i];

				$userAccountArray = DatabaseObj::getUserAccountFromID($id);

				// start a transaction so that the update can be rolled back if any external scripts fail
				$dbObj->query('START TRANSACTION');

				if ($stmt = $dbObj->prepare('UPDATE `USERS` SET `active` = ? WHERE `id` = ?'))
				{
					if ($stmt->bind_param('ii', $isActive, $id))
					{
						if ($stmt->execute())
						{
							// the sql statement succeeded so update the account status via an external script
							if ($hasExternalLoginScript == true)
							{
								$paramArray = Array();
								$paramArray['languagecode'] = UtilsObj::getBrowserLocale();
								$paramArray['groupcode'] = $userAccountArray['groupcode'];
								$paramArray['brandcode'] = $userAccountArray['webbrandcode'];
								$paramArray['id'] = $id;
								$paramArray['login'] = $userAccountArray['login'];
								$paramArray['accountcode'] = $userAccountArray['accountcode'];
								$paramArray['isactive'] = $isActive;

								$result = ExternalCustomerAccountObj::updateActiveStatus($paramArray);

								if ($result !== '')
								{
									//something has gone wrong in the external script, filled the resultparam for logging like database errors
									$resultParam = 'customerActivate External Script returned error: ' . $result;
								}

							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'customerActivate execute ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'customerActivate bind ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'customerActivate prepare ' . $dbObj->error;
				}


				// if no errors have occurred commit the transaction, log the action and generate a trigger, otherwise roll it back and exit the loop
				if ($result == '')
				{
					$dbObj->query('COMMIT');

					if ($userAccountArray['isactive'] == 1)
					{
						DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
							'ADMIN', 'CUSTOMER-DEACTIVATE', $id . ' ' . $userAccountArray['login'], 1);
					}
					else
					{
						DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
							'ADMIN', 'CUSTOMER-ACTIVATE', $id . ' ' . $userAccountArray['login'], 1);
					}

					DataExportObj::EventTrigger(TPX_TRIGGER_CUSTOMER_ACTIVATE, 'CUSTOMER', $id, 0);
				}
				else
				{
					$dbObj->query('ROLLBACK');

					break;
				}
			}

			$dbObj->close();
		}
		else
		{
			$result = 'str_DatabaseError';
			$resultParam = 'customerActivate connect ' . $dbObj->error;
		}

		if ($result !== '')
		{
			error_log($resultParam);
		}

		return $result;
	}

	static function displayEntry(&$pResultArray)
	{
		global $gSession;
		global $gConstants;

		$groupListArray = Array();
		$groupListItem = Array();
		$paymentMethodListArray = Array();
		$addressDefaultsArray = Array();
		$defaultGiftCardsAndVouchers = Array();
		$licenseKeyCurrencySettings = Array();
		$useDefaultVoucherSettings = 1;
		$lkeyAllowVouchers = 1;
		$lkeyAllowGiftcards = 1;
		$lkeyUseDefaultCurrency = 0;
		$lkeyCurrencyCode = '';

	   	$smarty = SmartyObj::newSmarty('AdminCustomers');

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			switch ($gSession['userdata']['usertype'])
			{
				case TPX_LOGIN_SYSTEM_ADMIN:
					$stmt = $dbObj->prepare('SELECT `groupcode`, `name`, `useaddressforshipping`, `useaddressforbilling`, `modifyshippingaddress`,
					`modifybillingaddress`, `modifyshippingcontactdetails`, `useremaildestination`,
					`usedefaultpaymentmethods`, `paymentmethods`, `webbrandcode`,
					`usedefaultvouchersettings`, `allowvouchers`, `allowgiftcards`, `usedefaultcurrency`, `currencycode`,
					(SELECT `paymentmethods` FROM `BRANDING` WHERE `code` = "") AS `defpaymentmethods`,
					(SELECT `usedefaultpaymentmethods`FROM `BRANDING` WHERE `code` = `LICENSEKEYS`.`webbrandcode`) AS `usedefaultpaymentmethods`,
					(SELECT `paymentmethods` FROM `BRANDING` WHERE `code` = `LICENSEKEYS`.`webbrandcode`) AS `paymentmethods`,
					(SELECT `applicationname` FROM `BRANDING` WHERE `code` = `LICENSEKEYS`.`webbrandcode`) AS `applicationname`,
					(SELECT `allowvouchers` FROM `BRANDING` WHERE `code` = `LICENSEKEYS`.`webbrandcode`) AS `defaultallowvouchers`,
					(SELECT `allowgiftcards` FROM `BRANDING` WHERE `code` = `LICENSEKEYS`.`webbrandcode`) AS `defaultallowgiftcards`
					FROM `LICENSEKEYS` ORDER BY `groupcode`');
					$bindOK = true;
				break;
				case TPX_LOGIN_COMPANY_ADMIN:
				$stmt = $dbObj->prepare('SELECT `groupcode`, `name`, `useaddressforshipping`, `useaddressforbilling`, `modifyshippingaddress`,
					`modifybillingaddress`, `modifyshippingcontactdetails`, `useremaildestination`,
					`usedefaultpaymentmethods`, `paymentmethods`, `webbrandcode`,
					`usedefaultvouchersettings`, `allowvouchers`, `allowgiftcards`, `usedefaultcurrency`, `currencycode`,
					(SELECT `paymentmethods` FROM `BRANDING` WHERE `code` = "") AS `defpaymentmethods`,
					(SELECT `usedefaultpaymentmethods`FROM `BRANDING` WHERE `code` = `LICENSEKEYS`.`webbrandcode`) AS `usedefaultpaymentmethods`,
					(SELECT `paymentmethods` FROM `BRANDING` WHERE `code` = `LICENSEKEYS`.`webbrandcode`) AS `paymentmethods`,
					(SELECT `applicationname` FROM `BRANDING` WHERE `code` = `LICENSEKEYS`.`webbrandcode`) AS `applicationname`,
					(SELECT `allowvouchers` FROM `BRANDING` WHERE `code` = `LICENSEKEYS`.`webbrandcode`) AS `defaultallowvouchers`,
					(SELECT `allowgiftcards` FROM `BRANDING` WHERE `code` = `LICENSEKEYS`.`webbrandcode`) AS `defaultallowgiftcards`
					FROM `LICENSEKEYS` WHERE (`companycode` = ? OR `companycode` = "") ORDER BY `groupcode`');

					$bindOK = $stmt->bind_param('s', $gSession['userdata']['companycode']);
				break;
			}

			if ($stmt)
			{
				if ($bindOK);
				{
					if ($stmt->bind_result($groupCode, $name, $useAddressForShipping, $useAddressForBilling, $canModifyShippingAddress,
							$canModifyBillingAddress, $canmodifyShippingContactDetails, $userEmailDestination,
							$useDefaultPaymentMethods, $paymentMethods, $webBrandCode,
							$useDefaultVoucherSettings, $lkeyAllowVouchers, $lkeyAllowGiftcards, $lkeyUseDefaultCurrency, $lkeyCurrencyCode,
							$defaultPaymentMethods, $brandDefaultPaymentMethods, $brandPaymentMethods, $applicationName, $defaultAllowVouchers, $defaultAllowGiftCards))
					{
						if ($stmt->execute())
						{
							while ($stmt->fetch())
							{

								if ($webBrandCode == '')
								{
									 $webBrandCode = $smarty->get_config_vars('str_LabelDefault');
								}

								$groupListItem['id'] = $groupCode;
								$groupListItem['name'] = $groupCode . ' (' . $webBrandCode . ' - ' . $applicationName .')';

								array_push($groupListArray, $groupListItem);
								$item['groupcode'] = $groupCode;
								$item['useaddressforshipping'] = ($useAddressForShipping == 1) ? 'true' : 'false';
								$item['useaddressforbilling'] = ($useAddressForBilling == 1) ? 'true' : 'false';
								$item['canmodifyshippingaddress'] = ($canModifyShippingAddress == 1) ? 'true' : 'false';
								$item['canmodifybillingaddress'] = ($canModifyBillingAddress == 1) ? 'true' : 'false';
								$item['canmodifyshippingcontactdetails'] = ($canmodifyShippingContactDetails == 1) ? 'true' : 'false';
								$item['useremaildestination'] = $userEmailDestination;
								array_push($addressDefaultsArray, $item);

								if ($useDefaultPaymentMethods == 1)	// get payment methods from either brand or constants
								{
									if ($webBrandCode == '')
									{
										$paymentMethods = $defaultPaymentMethods; // no web brand, use constants
									}
									else
									{
										if ($brandDefaultPaymentMethods == 1)
										{
											$paymentMethods = $defaultPaymentMethods;	// brand uses default, i.e. constants
										}
										else
										{
											$paymentMethods = $brandPaymentMethods;		// use brand settings
										}
									}
								}
								array_push($paymentMethodListArray, $paymentMethods);

								// if license key voucher settings say to use default use the branding settings
								if ($useDefaultVoucherSettings)
								{
									$defaultGiftCardsAndVouchers[$groupCode] = [
										'allowvouchers' => $defaultAllowVouchers,
										'allowgiftcards' => $defaultAllowGiftCards
									];
								}
								// otherwise use the settings from the key
								else
								{
									$defaultGiftCardsAndVouchers[$groupCode] = [
										'allowvouchers' => $lkeyAllowVouchers,
										'allowgiftcards' => $lkeyAllowGiftcards
									];

								}

								$licenseKeyCurrencySettings[$groupCode] = [
									'currencycode' => ($lkeyUseDefaultCurrency == 1) ? $gConstants['defaultcurrencycode'] : $lkeyCurrencyCode,
									'currencylocale' => ''
								];
							}
						}
					}
					$stmt->free_result();
					$stmt->close();
				}
			}
			$dbObj->close();
		}

		$pResultArray['grouplist'] = $groupListArray;
		$pResultArray['paymentmethoddefaults'] = $paymentMethodListArray;
		$pResultArray['defaultgiftcardandvouchersettings'] = $defaultGiftCardsAndVouchers;
		$pResultArray['addressdefaults'] = $addressDefaultsArray;
		$pResultArray['paymentmethodslist'] = DatabaseObj::getPaymentMethodsList();
		$pResultArray['currencylist'] = DatabaseObj::getCurrencyList();
		$pResultArray['licensekeycurrencysettings'] = $licenseKeyCurrencySettings;

	}

	static function displayAdd()
	{
		global $gConstants;

		$resultArray = DatabaseObj::getEmptyUserAccount();
		$resultArray['countrycode'] = $gConstants['homecountrycode'];
		$resultArray['usedefaultpaymentmethods'] = 1;
		$resultArray['creditlimit'] = $gConstants['defaultcreditlimit'];
		$resultArray['isactive'] = 1;
		$resultArray['add41'] = '';
		$resultArray['add42'] = '';
		$resultArray['add43'] = '';

		self::displayEntry($resultArray);

		return $resultArray;
	}

	static function displayEdit($pID)
	{
		$resultArray = DatabaseObj::getUserAccountFromID($pID);
		$additionalAddressFields = UtilsAddressObj::getAdditionalAddressFields($resultArray['countrycode'], $resultArray['address4']);
		$resultArray['add41'] = $additionalAddressFields['add41'];
		$resultArray['add42'] = $additionalAddressFields['add42'];
		$resultArray['add43'] = $additionalAddressFields['add43'];

		self::displayEntry($resultArray);

		return $resultArray;
	}

	static function customerAdd()
	{
		global $gConstants;
		global $gSession;

		$result = '';
		$resultParam = '';
		$recordID = 0;
		$isCustomer = 1;
		$userType = 100;

		$userAccountArray = array();
		$userAccountArray['reason'] = TPX_CUSTOMER_ACCOUNT_OVERRIDE_REASON_ADMINCUSTOMERADD;
		$userAccountArray['countrycode'] = $_POST['countryCode'];

		// see if there are special address fields like
		// add1=add41, add42 - add43
		// meaning address1 = add41 + ", "  + add42 + " - " + add43
		// and     address4 = add41 + "<p>" + add42 + "<p>" + add43
		UtilsAddressObj::specialAddressFields($userAccountArray['countrycode']);

		$groupCode = $_POST['licenseKeyList'];
		$login = $_POST['login_customer'];
		$password = $_POST['password_customer'];
		$passwordFormat = (int) UtilsObj::getPOSTParam('format', TPX_PASSWORDFORMAT_MD5);

		if (! class_exists('AuthenticateObj'))
		{
			require_once('../Utils/AuthenticateObj.php');
		}

		// calculate the password hash depending on if the page was secure or not
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

		if ($result == '')
		{
			$userAccountArray['accountcode'] = $_POST['accountcode'];
			$userAccountArray['contactfirstname'] = $_POST['contactFirstName'];
			$userAccountArray['contactlastname'] = $_POST['contactLastName'];
			$userAccountArray['companyname'] = $_POST['companyName'];
			$userAccountArray['address1'] = $_POST['address1'];
			$userAccountArray['address2'] = $_POST['address2'];
			$userAccountArray['address3'] = $_POST['address3'];
			$userAccountArray['address4'] = $_POST['address4'];

			// we need to check to see if the string contains @@TAOPIXTAG@@. If it does then this means that it is a special address field.
			// we then need to convert @@TAOPIXTAG@@ back to a <p> so that it can be stored correctly in the database.
			$userAccountArray['address4'] = implode('<p>', mb_split('@@TAOPIXTAG@@', $userAccountArray['address4']));

			$userAccountArray['city'] = $_POST['city'];
			$userAccountArray['state'] = $_POST['stateName'];
			$userAccountArray['county'] = $_POST['countyName'];
			$userAccountArray['regioncode'] = $_POST['regionCode'];
			$userAccountArray['region'] = $_POST['region'];
			$userAccountArray['postcode'] = $_POST['postCode'];

			$userAccountArray['registeredtaxnumbertype'] = $_POST['validregisteredtaxnumbertype'];
			$userAccountArray['registeredtaxnumber'] = $_POST['validregisteredtaxnumber'];

			$userAccountArray['countryname'] = $_POST['countryName'];
			$userAccountArray['telephonenumber'] = $_POST['telephonenumber'];
			$userAccountArray['emailaddress'] = $_POST['email'];
			$userAccountArray['usedefaultpaymentmethods'] = $_POST['usedefaultpaymentmethods'];
			$userAccountArray['paymentmethods'] = $_POST['paymentmethods'];
			$userAccountArray['creditlimit'] = $_POST['creditlimit'];
			$userAccountArray['accountbalance'] = $_POST['accountbalance'];
			$userAccountArray['uselicensekeyforshippingaddress'] = $_POST['uselicensekeyforshippingaddress'];
			$userAccountArray['canmodifyshippingaddress'] = $_POST['canmodifyshippingaddress'];
			$userAccountArray['canmodifyshippingcontactdetails'] = $_POST['canmodifyshippingcontactdetails'];
			$userAccountArray['uselicensekeyforbillingaddress'] = $_POST['uselicensekeyforbillingaddress'];
			$userAccountArray['canmodifybillingaddress'] = $_POST['canmodifybillingaddress'];
			$userAccountArray['useremaildestination'] = $_POST['useremaildestination'];
			$userAccountArray['defaultaddresscontrol'] = $_POST['defaultaddresscontrol'];
			$userAccountArray['canmodifypassword'] = $_POST['canmodifypassword'];
			$userAccountArray['creditlimit'] = $_POST['creditlimit'];
			$userAccountArray['accountbalance'] = $_POST['accountbalance'];
			$userAccountArray['sendmarketinginfo'] = $_POST['sendmarketinginfo'];
			$userAccountArray['isactive'] = $_POST['active'];
			$userAccountArray['taxcode'] = $_POST['taxcode'];
			$userAccountArray['shippingtaxcode'] = $_POST['shippingtaxcode'];

			$userAccountArray['protectfromredaction'] = $_POST['protectedfromredaction'];

			// settings for giftcard/voucher use
			$userAccountArray['usedefaultvouchersettings'] = $_POST['usedefaultgiftvouchersettings'];
			$userAccountArray['allowvouchers'] = $_POST['allowvouchers'];
			$userAccountArray['allowgiftcards'] = $_POST['allowgiftcards'];

			$userAccountArray['usedefaultcurrency'] = $_POST['usedefaultcurrency'];
			$userAccountArray['currencycode'] = $_POST['currencycode'];
			$userAccountArray['groupcode'] = $groupCode;

			$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
			$companyCode = $licenseKeyArray['companyCode'];
			$brandCode = $licenseKeyArray['webbrandcode'];

			$userAccountArray['brandcode'] = $brandCode;

			/*  Get COMPULSORY FIELDS for the selected country */
			$addressupdated = 1;
			$compulsoryfields = 'country,firstname,lastname,add1,city,postcode';
			$countryList = UtilsAddressObj::getCountryList();

			for ($i = 0; $i < count($countryList); $i++)
			{
				if ($countryList[$i]['isocode2'] == $userAccountArray['countrycode'])
				{
					$fields = $countryList[$i]['displayfields'];

					if ($fields != '')
					{
						$compulsoryfields = $countryList[$i]['compulsoryfields'];
					}
				}
			}

			/*
				Determine the $addressupdated value by checking COMPULSORY FIELDS are completed
				If any compulsory fields are not completed then the customer must update their address
				on next login.
			*/
			$addressupdated = 1;
			$compulsoryfieldsArray = explode(",", $compulsoryfields . ',telephonenumber');

			$remapKeys = ['add1' => 'address1', 'add2' => 'address2', 'add3' => 'address3', 'add4' => 'address4'];

			foreach ($compulsoryfieldsArray as $compulsoryfield)
			{
				$compulsoryfield = (isset($remapKeys[$compulsoryfield])) ? $remapKeys[$compulsoryfield] : $compulsoryfield;

				if (isset($userAccountArray[$compulsoryfield]))
				{
					if ($userAccountArray[$compulsoryfield] == '')
					{
						$addressupdated = 0;
						break;
					}
				}
			}

			$userAccountArray['addressupdated'] = $addressupdated;

			// check to see if the Taopix Customer Account API script is present.
			if (($gConstants['optionwscrp']) && (file_exists("../Customise/scripts/EDL_TaopixCustomerAccountAPI.php")))
			{
				require_once('../Customise/scripts/EDL_TaopixCustomerAccountAPI.php');

				// If the customer account override function exists pass account details to the external script
				if (method_exists('CustomerAccountAPI', 'customerAccountOverride'))
				{
					$userAccountArray = CustomerAccountAPI::customerAccountOverride($userAccountArray);
				}
			}

			if (($groupCode != '') && ($userAccountArray['contactfirstname'] != '') && ($login != '') && ($passwordHash !=''))
			{
				$dbObj = DatabaseObj::getConnection();
				if ($dbObj)
				{
					// start a transaction so that the insert can be rolled back if any external scripts fail
					$dbObj->query('START TRANSACTION');

					$sql = 'INSERT INTO `USERS` (`id`, `datecreated`, `companycode`, `webbrandcode`, `login`, `password`, `customer`, `usertype`,
							`groupcode`, `accountcode`, `companyname`, `address1`, `address2`, `address3`,`address4`, `city`, `county`, `state`, `regioncode`, `region`,
							`addressupdated`, `postcode`, `countrycode`, `countryname`, `telephonenumber`, `emailaddress`, `contactfirstname`, `contactlastname`,
							`usedefaultcurrency`, `currencycode`, `usedefaultpaymentmethods`, `paymentmethods`, `taxcode`, `shippingtaxcode`, `registeredtaxnumbertype`,
							`registeredtaxnumber`, `uselicensekeyforshippingaddress`, `modifyshippingaddress`, `modifyshippingcontactdetails`,
							`uselicensekeyforbillingaddress`, `modifybillingaddress`, `modifypassword`, `creditlimit`, `accountbalance`, `sendmarketinginfo`, `active`,
							`useremaildestination`, `defaultaddresscontrol`, `protectfromredaction`, `usedefaultvouchersettings`, `allowvouchers`, `allowgiftcards`';

					if ($userAccountArray['sendmarketinginfo'] == 1)
					{
						$sql .= ', `sendmarketinginfooptindate`';
					}

					$sql .= ') VALUES (0, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
						?, ?, ?, ?, ?, ?, ?, ?';

					if ($userAccountArray['sendmarketinginfo'] == 1)
					{
						$sql .= ', NOW()';
					}

					$sql .= ')';

					if ($stmt = $dbObj->prepare($sql))
					{
					if ($stmt->bind_param('ssssiisssss' . 'sssssssssss' . 'ssssisis' . 'ssisii' . 'iiiid' . 'diiiii' . 'iii',
							$companyCode, $brandCode, $login, $passwordHash, $isCustomer, $userType, $groupCode, $userAccountArray['accountcode'], $userAccountArray['companyname'],
							$userAccountArray['address1'], $userAccountArray['address2'], $userAccountArray['address3'], $userAccountArray['address4'], $userAccountArray['city'], $userAccountArray['county'],
							$userAccountArray['state'], $userAccountArray['regioncode'], $userAccountArray['region'], $userAccountArray['addressupdated'], $userAccountArray['postcode'], $userAccountArray['countrycode'], $userAccountArray['countryname'], $userAccountArray['telephonenumber'],
							$userAccountArray['emailaddress'], $userAccountArray['contactfirstname'], $userAccountArray['contactlastname'], $userAccountArray['usedefaultcurrency'], $userAccountArray['currencycode'],
							$userAccountArray['usedefaultpaymentmethods'], $userAccountArray['paymentmethods'], $userAccountArray['taxcode'], $userAccountArray['shippingtaxcode'], $userAccountArray['registeredtaxnumbertype'],
							$userAccountArray['registeredtaxnumber'], $userAccountArray['uselicensekeyforshippingaddress'], $userAccountArray['canmodifyshippingaddress'],
							$userAccountArray['canmodifyshippingcontactdetails'], $userAccountArray['uselicensekeyforbillingaddress'], $userAccountArray['canmodifybillingaddress'],
							$userAccountArray['canmodifypassword'], $userAccountArray['creditlimit'], $userAccountArray['accountbalance'], $userAccountArray['sendmarketinginfo'],
							$userAccountArray['isactive'], $userAccountArray['useremaildestination'], $userAccountArray['defaultaddresscontrol'], $userAccountArray['protectfromredaction'],
							$userAccountArray['usedefaultvouchersettings'], $userAccountArray['allowvouchers'], $userAccountArray['allowgiftcards']))
						{
							if ($stmt->execute())
							{
								$recordID = $dbObj->insert_id;

								DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'UPDATEPREFERENCES', $userAccountArray['sendmarketinginfo'] . " " . $recordID, 1);

								// the sql statement succeeded so pass the account details to the external script
								if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
								{
									require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

									if (method_exists('ExternalCustomerAccountObj', 'createAccount'))
									{
										unset($userAccountArray['reason']);

										// default the giftcardbalance to 0.00 to be passed to external customer account create account function
										$userAccountArray['giftcardbalance'] = 0.00;

										// create the user account via the external script
										$paramArray = Array();
										$paramArray['languagecode'] = UtilsObj::getBrowserLocale();
										$paramArray['isadmin'] = 1;
										$paramArray['groupcode'] = $groupCode;
										$paramArray['brandcode'] = $brandCode;
										$paramArray['login'] = $login;
										$paramArray['accountcode'] = $userAccountArray['accountcode'];
										$paramArray['passwordformat'] = $passwordFormat;
										$paramArray['password'] = $password;
										//as this function is only called from the admin interface we always want to pass the value for all info passed
										$paramArray['status'] = 1;
										$paramArray['useraccount'] = $userAccountArray;

										$externalLoginResultArray = ExternalCustomerAccountObj::createAccount($paramArray);
										$result = $externalLoginResultArray['result'];
										$newAccountCode = $externalLoginResultArray['accountcode'];


										// if the script has returned no error and has supplied a new account code update the record now
										if (($result == '') && ($newAccountCode != $userAccountArray['accountcode']))
										{
											if ($stmt2 = $dbObj->prepare('UPDATE `USERS` SET `accountcode` = ? WHERE `id` = ?'))
											{
												if ($stmt2->bind_param('si', $newAccountCode, $recordID))
												{
													if (! $stmt2->execute())
													{
														$result = 'str_DatabaseError';
														$resultParam = 'customerAdd update account code execute ' . $dbObj->error;
													}
												}
												else
												{
													// could not bind parameters
													$result = 'str_DatabaseError';
													$resultParam = 'customerAdd update account code bind ' . $dbObj->error;
												}

												$stmt2->free_result();
												$stmt2->close();
												$stmt2 = null;
											}
											else
											{
												$result = 'str_DatabaseError';
												$resultParam = 'customerAdd update account code prepare ' . $dbObj->error;
											}
										}
									}
								}
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
									$resultParam = 'customerAdd execute ' . $dbObj->error;
								}
							}
						}
						else
						{
							// could not bind parameters
							$result = 'str_DatabaseError';
							$resultParam = 'customerAdd bind ' . $dbObj->error;
						}

						$stmt->free_result();
						$stmt->close();
						$stmt = null;
					}
					else
					{
						// could not prepare statement
						$result = 'str_DatabaseError';
						$resultParam = 'customerAdd prepare ' . $dbObj->error;
					}

					// if no errors have occurred commit the transaction, log the action and generate a trigger, otherwise roll it back
					if ($result == '')
					{
						$dbObj->query('COMMIT');

						DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
							'ADMIN', 'CUSTOMER-ADD', $recordID . ' ' . $login, 1);

						DataExportObj::EventTrigger(TPX_TRIGGER_CUSTOMER_ADD, 'CUSTOMER', $recordID, 0);
					}
					else
					{
						$dbObj->query('ROLLBACK');
					}

					$dbObj->close();
				}
				else
				{
					// could not open database connection
					$result = 'str_DatabaseError';
					$resultParam = 'customerAdd connect ' . $dbObj->error;
				}
			}
		}

		if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			$smarty = SmartyObj::newSmarty('AdminCustomers');
            $msg = str_replace('^0', $resultParam, $smarty->get_config_vars($result));
            echo '{"success":false,	"msg":"' . $msg . '"}';
        }

		return;
	}

	static function customerEdit()
	{
		global $gSession;
		global $gConstants;

		$result = '';
		$resultParam = '';
		$userAccountArray = array();
		$userAccountArray['reason'] = TPX_CUSTOMER_ACCOUNT_OVERRIDE_REASON_ADMINCUSTOMEREDIT;
		$userAccountArray['countrycode'] = $_POST['countryCode'];

		// see if there are special address fields like
		// add1=add41, add42 - add43
		// meaning address1 = add41 + ", "  + add42 + " - " + add43
		// and     address4 = add41 + "<p>" + add42 + "<p>" + add43
		UtilsAddressObj::specialAddressFields($userAccountArray['countrycode']);

		$id = $_POST['id'];
		$groupCode = $_POST['licenseKeyList'];
		$login = $_POST['login_customer'];
		$password = $_POST['password_customer'];
		$passwordFormat = (int) UtilsObj::getPOSTParam('format', TPX_PASSWORDFORMAT_MD5);
		$userAccountArray['contactfirstname'] = $_POST['contactFirstName'];
		$userAccountArray['contactlastname'] = $_POST['contactLastName'];
		$userAccountArray['accountcode'] = $_POST['accountcode'];
		$userAccountArray['companyname'] = $_POST['companyName'];
		$userAccountArray['address1'] = $_POST['address1'];
		$userAccountArray['address2'] = $_POST['address2'];
		$userAccountArray['address3'] = $_POST['address3'];
		$userAccountArray['address4'] = $_POST['address4'];

		// we need to check to see if the string contains @@TAOPIXTAG@@. If it does then this means that it is a special address field.
		// we then need to convert @@TAOPIXTAG@@ back to a <p> so that it can be stored correctly in the database.
		$userAccountArray['address4'] = implode('<p>', mb_split('@@TAOPIXTAG@@', $userAccountArray['address4']));

		$userAccountArray['city'] = $_POST['city'];
		$userAccountArray['state'] = $_POST['stateName'];
		$userAccountArray['county'] = $_POST['countyName'];
		$userAccountArray['regioncode'] = $_POST['regionCode'];
		$userAccountArray['region'] = $_POST['region'];
		$userAccountArray['postcode'] = $_POST['postCode'];
		$userAccountArray['countryname'] = $_POST['countryName'];
		$userAccountArray['telephonenumber'] = $_POST['telephonenumber'];
		$userAccountArray['emailaddress'] = $_POST['email'];

		$userAccountArray['registeredtaxnumbertype'] = $_POST['validregisteredtaxnumbertype'];
		$userAccountArray['registeredtaxnumber'] = $_POST['validregisteredtaxnumber'];

		$userAccountArray['usedefaultpaymentmethods'] = $_POST['usedefaultpaymentmethods'];
		$userAccountArray['paymentmethods'] = $_POST['paymentmethods'];
		$userAccountArray['creditlimit'] = $_POST['creditlimit'];
		$userAccountArray['uselicensekeyforshippingaddress'] = $_POST['uselicensekeyforshippingaddress'];
		$userAccountArray['canmodifyshippingaddress'] = $_POST['canmodifyshippingaddress'];
		$userAccountArray['canmodifyshippingcontactdetails'] = $_POST['canmodifyshippingcontactdetails'];
		$userAccountArray['uselicensekeyforbillingaddress'] = $_POST['uselicensekeyforbillingaddress'];
		$userAccountArray['canmodifybillingaddress'] = $_POST['canmodifybillingaddress'];
		$userAccountArray['useremaildestination'] = $_POST['useremaildestination'];
		$userAccountArray['defaultaddresscontrol'] = $_POST['defaultaddresscontrol'];
		$userAccountArray['canmodifypassword'] = $_POST['canmodifypassword'];
		$userAccountArray['creditlimit'] = $_POST['creditlimit'];
		$userAccountArray['accountbalancedifference'] = $_POST['accountbalancedifference'];
		$userAccountArray['sendmarketinginfo'] = $_POST['sendmarketinginfo'];
		$userAccountArray['isactive'] = $_POST['active'];
		$userAccountArray['taxcode'] = $_POST['taxcode'];
		$userAccountArray['shippingtaxcode'] = $_POST['shippingtaxcode'];

		$userAccountArray['protectfromredaction'] = $_POST['protectedfromredaction'];

		// settings for giftcard/voucher use
		$userAccountArray['usedefaultvouchersettings'] = $_POST['usedefaultgiftvouchersettings'];
		$userAccountArray['allowvouchers'] = $_POST['allowvouchers'];
		$userAccountArray['allowgiftcards'] = $_POST['allowgiftcards'];

		$userAccountArray['usedefaultcurrency'] = $_POST['usedefaultcurrency'];
		$userAccountArray['currencycode'] = $_POST['currencycode'];
		$userAccountArray['groupcode'] = $groupCode;

		$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($groupCode);
		$companyCode = $licenseKeyArray['companyCode'];
		$brandCode = $licenseKeyArray['webbrandcode'];

		$userAccountArray['brandcode'] = $brandCode;

		$deleteEmailChangeRequests = false;

		// check to see if the Taopix Customer Account API script is present.
		if (($gConstants['optionwscrp']) && (file_exists("../Customise/scripts/EDL_TaopixCustomerAccountAPI.php")))
		{
			require_once('../Customise/scripts/EDL_TaopixCustomerAccountAPI.php');

		   // If the customer account override function exists pass account details to the external script
		   if (method_exists('CustomerAccountAPI', 'customerAccountOverride'))
		   {
			   $userAccountArray = CustomerAccountAPI::customerAccountOverride($userAccountArray);
		   }
		}

		if (($id > 0) && ($userAccountArray['contactfirstname'] != '') && ($login != '') && ($password !=''))
		{
			$origUserArray = DatabaseObj::getUserAccountFromID($id);

			if ($password == '**UNCHANGED**')
			{
				// password has not changed so use the hash from the database

				$password = $origUserArray['password'];
				$passwordHash = $origUserArray['password'];
			}
			else
			{
				// password has changed so generate the new password hash

				if (! class_exists('AuthenticateObj'))
				{
					require_once('../Utils/AuthenticateObj.php');
				}

				// calculate the password hash depending on if the page was secure or not
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

			// Check if the customer login and email address match before update, if so the user logs in with their email address.
			if (($origUserArray['login'] === $origUserArray['emailaddress']) && ($login !== $userAccountArray['emailaddress']))
			{
				/*
				 * Customer logs in with their email address.
				 * If the login and original login are the same we need to update this to be the email address supplied in the edit.
				 * If the email and original email address are the same we need to update the email address to be the login supplied in the edit.
				 */
				if ($login === $origUserArray['login'])
				{
					$login = $userAccountArray['emailaddress'];
				}
				else
				{
					$userAccountArray['emailaddress'] = $login;
				}
			}

			// Users email address has changed, so we remove all requests to change the email address.
			if ($userAccountArray['emailaddress'] !== $origUserArray['emailaddress'])
			{
				// We have made a change to the email address
				$deleteEmailChangeRequests = true;
			}

			/*  Get COMPULSORY FIELDS for the selected country */
			$compulsoryfields = 'country,firstname,lastname,add1,city,postcode';
			$countryList = UtilsAddressObj::getCountryList();

			for ($i = 0; $i < count($countryList); $i++)
			{
				if ($countryList[$i]['isocode2'] == $userAccountArray['countrycode'])
				{
					$fields = $countryList[$i]['displayfields'];

					if ($fields != '')
					{
						$compulsoryfields = $countryList[$i]['compulsoryfields'];
					}
				}
			}

			/*
				Determine the $addressupdated value by checking COMPULSORY FIELDS are completed
				If any compulsory fields are not completed then the customer must update their address
				on next login.
			*/
			$addressupdated = 1;
			$compulsoryfieldsArray = explode(",", $compulsoryfields . ',telephonenumber');

			$remapKeys = ['add1' => 'address1', 'add2' => 'address2', 'add3' => 'address3', 'add4' => 'address4'];

			foreach ($compulsoryfieldsArray as $compulsoryfield)
			{
				$compulsoryfield = (isset($remapKeys[$compulsoryfield])) ? $remapKeys[$compulsoryfield] : $compulsoryfield;

				if (isset($userAccountArray[$compulsoryfield]))
				{
					if ($userAccountArray[$compulsoryfield] == '')
					{
						$addressupdated = 0;
						break;
					}
				}
			}

			$userAccountArray['addressupdated'] = $addressupdated;

			if ($result == '')
			{
				$dbObj = DatabaseObj::getConnection();
				if ($dbObj)
				{
					// start a transaction so that the update can be rolled back if any external scripts fail
					$dbObj->query('START TRANSACTION');

					$sql = 'UPDATE `USERS` SET `companycode` = ?, `webbrandcode` = ?, `groupcode` = ?, `login` = ?, `password` = ?, `accountcode` = ?,
							`companyname` = ?, `address1` = ?, `address2` = ?, `address3` = ?, `address4` = ?, `city` = ?, `county` = ?, `state` = ?, `regioncode` = ?,
							`region` = ?, `postcode` = ?, `countrycode` = ?, `countryname` = ?, `telephonenumber` = ?, `emailaddress` = ?, `contactfirstname` = ?,
							`contactlastname` = ?, `usedefaultpaymentmethods` = ?, `paymentmethods` = ?, `taxcode` = ?, `shippingtaxcode` = ?, `registeredtaxnumbertype` = ?,
							`registeredtaxnumber` = ?, `uselicensekeyforshippingaddress` = ?, `modifyshippingaddress` = ?, `modifyshippingcontactdetails` = ?,
							`uselicensekeyforbillingaddress` = ?, `modifybillingaddress` = ?, `modifypassword` = ?, `creditlimit` = ?,
							`accountbalance` = `accountbalance` + ?, `sendmarketinginfo` = ?, `active` = ?, `useremaildestination` = ?, `defaultaddresscontrol` = ?,
							`addressupdated` = ?, `protectfromredaction` = ?, `usedefaultvouchersettings` = ?, `allowvouchers` = ?, `allowgiftcards` = ?,
							`usedefaultcurrency` = ?, `currencycode` = ?';

					// only update the date the user opt in to marketing if they have opted in
					if ($userAccountArray['sendmarketinginfo'] == 1)
					{
						$sql .= ' , `sendmarketinginfooptindate` = now()';
					}

					$sql .= ' WHERE `id` = ?';

					if ($stmt = $dbObj->prepare($sql))
					{
						if ($stmt->bind_param('sssssssssss' . 'sssssssssss' . 'sisssis' . 'iiii' . 'iiddiii' . 'iiiiiiisi',
								$companyCode, $brandCode, $groupCode, $login, $passwordHash, $userAccountArray['accountcode'], $userAccountArray['companyname'], $userAccountArray['address1'], $userAccountArray['address2'],
								$userAccountArray['address3'], $userAccountArray['address4'], $userAccountArray['city'], $userAccountArray['county'], $userAccountArray['state'],
								$userAccountArray['regioncode'], $userAccountArray['region'], $userAccountArray['postcode'], $userAccountArray['countrycode'], $userAccountArray['countryname'],
								$userAccountArray['telephonenumber'], $userAccountArray['emailaddress'], $userAccountArray['contactfirstname'], $userAccountArray['contactlastname'], $userAccountArray['usedefaultpaymentmethods'],
								$userAccountArray['paymentmethods'], $userAccountArray['taxcode'], $userAccountArray['shippingtaxcode'], $userAccountArray['registeredtaxnumbertype'], $userAccountArray['registeredtaxnumber'],
								$userAccountArray['uselicensekeyforshippingaddress'], $userAccountArray['canmodifyshippingaddress'], $userAccountArray['canmodifyshippingcontactdetails'], $userAccountArray['uselicensekeyforbillingaddress'],
								$userAccountArray['canmodifybillingaddress'], $userAccountArray['canmodifypassword'], $userAccountArray['creditlimit'], $userAccountArray['accountbalancedifference'], $userAccountArray['sendmarketinginfo'],
								$userAccountArray['isactive'], $userAccountArray['useremaildestination'], $userAccountArray['defaultaddresscontrol'], $userAccountArray['addressupdated'], $userAccountArray['protectfromredaction'],
								$userAccountArray['usedefaultvouchersettings'], $userAccountArray['allowvouchers'], $userAccountArray['allowgiftcards'], $userAccountArray['usedefaultcurrency'], $userAccountArray['currencycode'], $id))
						{
							if ($stmt->execute())
							{

								DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'UPDATEPREFERENCES', $userAccountArray['sendmarketinginfo'] . " " . $id, 1);

								// the sql statement succeeded so update the account details via an external script
								if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
								{
									require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

									if (method_exists('ExternalCustomerAccountObj', 'updateAccountDetails'))
									{
										unset($userAccountArray['reason']);
										// default the giftcardbalancedifference to 0.00 to be passed to external customer account update account function
										$userAccountArray['giftcardbalancedifference'] = 0.00;

										// update the user account via the external script
										$paramArray = Array();
										$paramArray['languagecode'] = UtilsObj::getBrowserLocale();
										$paramArray['isadmin'] = 1;
										$paramArray['id'] = $id;
										$paramArray['origgroupcode'] = $origUserArray['groupcode'];
										$paramArray['origbrandcode'] = $origUserArray['webbrandcode'];
										$paramArray['origlogin'] = $origUserArray['login'];
										$paramArray['origaccountcode'] = $origUserArray['accountcode'];
										$paramArray['newbrandcode'] = $brandCode;
										$paramArray['newgroupcode'] = $groupCode;
										$paramArray['newlogin'] = $login;
										$paramArray['newaccountcode'] = $userAccountArray['accountcode'];

										if ($passwordHash == $origUserArray['password'])
										{
											$paramArray['passwordchanged'] = 0;
										}
										else
										{
											$paramArray['passwordchanged'] = 1;
										}

										// pass the plaintext/md5 password depending on if the page is secure or not, it's up to the script to handle validating passwords
										$paramArray['passwordformat'] = $passwordFormat;
										$paramArray['password'] = $password;
										$paramArray['status'] = $userAccountArray['addressupdated'];
										$paramArray['useraccount'] = $userAccountArray;

										$result = ExternalCustomerAccountObj::updateAccountDetails($paramArray);
									}
								}

								if ($deleteEmailChangeRequests)
								{
									// Remove expired email address changes, and any outstanding email address changes for this user.
									AuthenticateObj::deleteAuthenticationDataRecordsForUser($id);
								}
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
									$resultParam = 'customerEdit execute ' . $dbObj->error;
								}
							}
						}
						else
						{
							// could not bind parameters
							$result = 'str_DatabaseError';
							$resultParam = 'customerEdit bind ' . $dbObj->error;
						}

						$stmt->free_result();
					}
					else
					{
						// could not prepare statement
						$result = 'str_DatabaseError';
						$resultParam = 'customerEdit prepare ' . $dbObj->error;
					}


					// if no errors have occurred commit the transaction, log the action and generate a trigger, otherwise roll it back
					if ($result == '')
					{
						$dbObj->query('COMMIT');

						DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
							'ADMIN', 'CUSTOMER-UPDATE', $id . ' ' . $login, 1);

					DataExportObj::EventTrigger(TPX_TRIGGER_CUSTOMER_EDIT, 'CUSTOMER', $id, 0);
					}
					else
					{
						$dbObj->query('ROLLBACK');
					}

					$dbObj->close();
				}
				else
				{
					// could not open database connection
					$result = 'str_DatabaseError';
					$resultParam = 'customerEdit connect ' . $dbObj->error;
				}
			}
		}

		if ($result == '')
		{
			echo "{'success':'true', 'msg':'" . '' . "' }";
		}
		else
		{
			$smarty = SmartyObj::newSmarty('AdminCustomers');
        	echo '{"success":false,	"msg":"' . str_replace('^0', $resultParam, $smarty->get_config_vars($result)) . '"}';
        }
		return;
	}

	static function customerDelete()
    {
        require_once('../libs/internal/curl/Curl.php');
        global $gSession;
        global $gConstants;
        global $ac_config;

        $customerList = explode(',', $_POST['idlist']);
        $result = '';
        $resultParam = '';
        $recordID = 0;
        $notDeletedArray = array(
            'session' => array(),
            'order' => array(),
            'protected' => array(),
            'activeonline' => array()
        );

		// flag the records as redaction authorised by admin
		require_once('../DataRedactionAPI/DataRedactionAPI_model.php');

        //check if the user can be deleted
        $customerListData = DataRedactionAPI_model::canRedactAccounts($customerList, false);
        $result = $customerListData['error'];
        $resultParam = $customerListData['errorparam'];

        //Skip to error message if error detected
        if ($result == '')
        {
            $notDeletedArray['session'] = $customerListData['session'];
            $notDeletedArray['order'] = $customerListData['order'];

            //check if user is protected from redaction
            $customerListData = self::checkRedactionProtection($customerListData['redact']);
            $result = $customerListData['error'];
            $resultParam = $customerListData['errorparam'];

            //skip to error message if DB error detected
            if ($result == '')
            {
                //provides customer name for error message
                $customerProtectDisallowCount = count($customerListData['disallow']);
                if ($customerProtectDisallowCount > 0)
                {
                    for ($i = 0; $i < $customerProtectDisallowCount; $i++)
                    {
                        $userData = DatabaseObj::getUserAccountFromID($customerListData['disallow'][$i]['id']);
                        $notDeletedArray['protected'][] = $userData['contactfirstname'] . ' ' . $userData['contactlastname'];
                    }
                }

                //only check for online sessions if online enabled
                if ($gConstants['optiondesol'])
                {
                    //generate online url for put command
                    $serverURL = $ac_config['TAOPIXONLINEURL'];
                    $dataToEncrypt = array('cmd' => 'CHECKONLINEDATA', 'data' => $customerListData['redact']);
                    $onlineReturn = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt);

                    if ($onlineReturn['error'] == '')
                    {
                    	$customerListData = $onlineReturn['data']['onlinedata'];

                    	//convert the returned array into a more usable format
                        //provide customer name for error message
                        $customerOnlineSessionDisallowCount = count($customerListData['disallow']);
                        if ($customerOnlineSessionDisallowCount > 0)
                        {
                            for ($i = 0; $i < $customerOnlineSessionDisallowCount; $i++)
                            {
                                $userData = DatabaseObj::getUserAccountFromID($customerListData['disallow'][$i]['id']);
                                $notDeletedArray['activeonline'][] = $userData['contactfirstname'] . ' ' . $userData['contactlastname'];
                            }
                        }
                    }
                    else
                    {
						$result = $onlineReturn['error'];
						$resultParam = $onlineReturn['error'];
                    }
                }

				if ($result == '')
                {
                    // determine outside of the loop if we need to call the external script
                    $hasExternalLoginScript = false;
                    if (($gConstants['optionwscrp']) && (file_exists('../Customise/scripts/EDL_ExternalCustomerAccount.php')))
                    {
                        require_once('../Customise/scripts/EDL_ExternalCustomerAccount.php');

                        if (method_exists('ExternalCustomerAccountObj', 'deleteAccount'))
                        {
                            $hasExternalLoginScript = true;
                        }
                    }

                    $customerListCheck = count($customerListData['redact']);

                    //only run deletion if no issues found for at least one account
                    if ($customerListCheck > 0)
                    {
                        // delete the records
                        $dbObj = DatabaseObj::getGlobalDBConnection();
                        if ($dbObj)
                        {
                            for ($i = 0; $i < $customerListCheck; $i++)
                            {
                                $customerID = $customerListData['redact'][$i];

                                $userAccountArray = DatabaseObj::getUserAccountFromID($customerID);

								//check if customer has any completed orders
                                if ($stmt = $dbObj->prepare('SELECT `id` FROM `ORDERHEADER` WHERE `userid` = ?'))
                                {
                                    if ($stmt->bind_param('i', $customerID))
                                    {
                                        if ($stmt->bind_result($recordID))
                                        {
                                            if ($stmt->execute())
                                            {
                                                if ($stmt->fetch())
                                                {
                                                    $notDeletedArray['order'][] = "'" . $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'] . "'";
                                                }
                                                else
                                                {
                                                    // start a transaction so that the delete can be rolled back if any external scripts fail
                                                    $dbObj->query('START TRANSACTION');

                                                    if ($stmt = $dbObj->prepare('DELETE FROM `USERS` WHERE `id` = ?'))
                                                    {
                                                        if ($stmt->bind_param('i', $customerID))
                                                        {
                                                            if ($stmt->execute())
                                                            {
                                                                // the sql statement succeeded so delete the account record via an external script
                                                                if ($hasExternalLoginScript == true)
                                                                {
                                                                    $paramArray = Array();
                                                                    $paramArray['languagecode'] = UtilsObj::getBrowserLocale();
                                                                    $paramArray['groupcode'] = $userAccountArray['groupcode'];
                                                                    $paramArray['brandcode'] = $userAccountArray['webbrandcode'];
                                                                    $paramArray['id'] = $customerID;
                                                                    $paramArray['login'] = $userAccountArray['login'];
                                                                    $paramArray['accountcode'] = $userAccountArray['accountcode'];

                                                                    $result = ExternalCustomerAccountObj::deleteAccount($paramArray);
                                                                }
                                                            }
                                                            else
                                                            {
                                                                $result = 'str_DatabaseError';
                                                                $resultParam = 'customerDelete execute ' . $dbObj->error;
                                                            }
                                                        }
                                                        else
                                                        {
                                                            $result = 'str_DatabaseError';
                                                            $resultParam = 'customerDelete bind ' . $dbObj->error;
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $result = 'str_DatabaseError';
                                                        $resultParam = 'customerDelete prepare ' . $dbObj->error;
                                                    }


                                                    // if no errors have occurred commit the transaction, log the action and generate a trigger, otherwise roll it back
                                                    if ($result == '')
                                                    {
                                                        $dbObj->query('COMMIT');

                                                        DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'CUSTOMER-DELETE', $customerID . ' ' . $userAccountArray['login'], 1);

                                                        DataExportObj::EventTrigger(TPX_TRIGGER_CUSTOMER_DELETE, 'CUSTOMER', $customerID, 0);
                                                    }
                                                    else
                                                    {
                                                        $dbObj->query('ROLLBACK');

                                                        $notDeletedArray[] = "'" . $userAccountArray['contactfirstname'] . ' ' . $userAccountArray['contactlastname'] . "'";
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                $result = 'str_DatabaseError';
                                                $resultParam = 'customerDelete execute ' . $dbObj->error;
                                            }
                                        }
                                        else
                                        {
                                            // could not bind parameters
                                            $result = 'str_DatabaseError';
                                            $resultParam = 'customerDelete bindresult ' . $dbObj->error;
                                        }
                                    }
                                    else
                                    {
                                        // could not bind parameters
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'customerDelete bind ' . $dbObj->error;
                                    }
                                    $stmt->free_result();
                                    $stmt->close();
                                    $stmt = null;
                                }
                                else
                                {
                                    // could not prepare statement
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'customerDelete prepare ' . $dbObj->error;
                                }
                            }
                            $dbObj->close();
                        }
                        else
                        {
                            // could not open database connection
                            $result = 'str_DatabaseError';
                            $resultParam = 'customerDelete connect ' . $dbObj->error;
                        }
                    }
                }
            }
        }

        $notDeletedArray['result'] = $result;
        $notDeletedArray['resultParam'] = $resultParam;

        return $notDeletedArray;
    }

    static function customerRedact()
	{
		global $gSession;

		$customerListRequest = explode(',', $_POST['idlist']);
        $result = '';
		$resultParam = '';
		$notDeletedArray = array(
			'session' => array(),
			'order' => array(),
			'failed' => array()
		);
		$disallowMessage = 'str_ErrorProtectedFromRedaction';

		// flag the records as redaction authorised by admin
		require_once('../DataRedactionAPI/DataRedactionAPI_model.php');

		// confirm accounts can be redacted
		$customerListData = DataRedactionAPI_model::canRedactAccounts($customerListRequest, false);
        $notDeletedArray['session'] = $customerListData['session'];
        $notDeletedArray['order'] = $customerListData['order'];

		$customerList = $customerListData['redact'];

		if (count($customerList) > 0)
		{
			// authorise redaction requests
			$resultArray = DataRedactionAPI_model::authoriseRedaction2($customerList, 1);
			$result = $resultArray['result'];
			$resultParam = $resultArray['resultparam'];

			if ($result == '')
			{
				if (count($resultArray['data']['updated']) > 0)
				{
					foreach ($resultArray['data']['updated'] as $theUser)
					{
						$userData = DatabaseObj::getUserAccountFromID($theUser);
						$brandData = DatabaseObj::getBrandingFromCode($userData['webbrandcode']);
						$redactionDate = date('Y-m-d H:i:s', (time() + (60 * 60 * 24 * $brandData['redactionnotificationdays'])));

						$eventResult = DatabaseObj::createEvent('TAOPIX_DATADELETION', '', '', '', $redactionDate, 0, '', '', $theUser, TPX_REDACTION_AUTHORISED_BY_LICENSEE, '', '', '', '', 0, 0, $gSession['userid'], '', '', $theUser);
					}
				}

				if (count($resultArray['data']['failed']) > 0)
				{
					// redact accounts
					$resultArray2 = DataRedactionAPI_model::authoriseRedaction2($resultArray['data']['failed'], 2);
					$result = $resultArray2['result'];
					$resultParam = $resultArray2['resultparam'];
					if (count($resultArray2['data']['updated']) > 0)
					{
						foreach ($resultArray2['data']['updated'] as $theUser2)
						{
							$userData = DatabaseObj::getUserAccountFromID($theUser2);
							$brandData = DatabaseObj::getBrandingFromCode($userData['webbrandcode']);
							$redactionDate = date('Y-m-d H:i:s', (time() + (60 * 60 * 24 * $brandData['redactionnotificationdays'])));

							$eventResult = DatabaseObj::createEvent('TAOPIX_DATADELETION', '', '', '', $redactionDate, 0, '', '', $theUser2, TPX_REDACTION_AUTHORISED_BY_LICENSEE, '', '', '', '', 0, 0, $gSession['userid'], '', '', $theUser2);
						}
					}

					if (count($resultArray2['data']['failed']) > 0)
					{
						foreach ($resultArray2['data']['failed'] as $failedUserID)
						{
							$userData = DatabaseObj::getUserAccountFromID($failedUserID);

							$notDeletedArray['failed'][] = $userData['contactfirstname'] . ' ' . $userData['contactlastname'];
						}
					}
				}
			}
		}

		$smarty = SmartyObj::newSmarty('AdminCustomers');

		if ($result == '')
		{
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$message = $smarty->get_config_vars('str_CustomersRedacted');
			$messageSession = '';
			$messageOrder = '';
			$messageFailed = '';

			if (count($notDeletedArray['session']) > 0)
			{
				$messageSession = str_replace("'^0'", join(', ', $notDeletedArray['session']), $smarty->get_config_vars('str_ErrorUsedInSession'));
			}

			if (count($notDeletedArray['order']) > 0)
			{
				$messageOrder = str_replace("'^0'", join(', ', $notDeletedArray['order']), $smarty->get_config_vars('str_ErrorUsedInOrder'));
			}

			if (count($notDeletedArray['failed']) > 0)
			{
				$messageFailed = str_replace("'^0'", join(', ', $notDeletedArray['failed']), $smarty->get_config_vars($disallowMessage));
			}

			if (($messageSession != '') || ($messageOrder) || ($messageFailed))
			{
				$title = $smarty->get_config_vars('str_TitleWarning');
				$message = $messageSession . '<br />' . $messageOrder . '<br />' . $messageFailed;
			}

			echo "{'success':'true', 'title':'" . UtilsObj::ExtJSEscape($title) . "', 'msg':'" . UtilsObj::ExtJSEscape($message) . "' }";
		}
		else
		{
			echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }
		return;
	}

	static function customerRedactDecline()
	{
		$customerList  = explode(',',$_POST['idlist']);
		$notDeletedArray = array();

		// flag the records as redaction authorised by admin
		require_once('../DataRedactionAPI/DataRedactionAPI_model.php');

		// authorise redaction requests
		$resultArray = DataRedactionAPI_model::authoriseRedaction2($customerList, 0);
		$result = $resultArray['result'];
		$resultParam = $resultArray['resultparam'];

		$smarty = SmartyObj::newSmarty('AdminCustomers');

		if ($result == '')
		{
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$message = $smarty->get_config_vars('str_CustomersRedactionDeclined');

			if (count($notDeletedArray) > 0)
			{
				$title = $smarty->get_config_vars('str_TitleWarning');
				$message = str_replace("'^0'", join(', ', $notDeletedArray), $smarty->get_config_vars('str_ErrorProtectedFromRedaction'));
			}
			echo "{'success':'true', 'title':'" . UtilsObj::ExtJSEscape($title) . "', 'msg':'" . UtilsObj::ExtJSEscape($message) . "' }";
		}
		else
		{
			echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }
		return;
	}

    static function checkRedactionProtection($pUserIDArray)
    {
        $error = '';
        $resultParam = '';
        $resultArray = array('error' => '', 'errorparam' => '', 'redact' => array(), 'disallow' => array());
        $sql = 'SELECT `id`, `protectfromredaction`
                FROM `USERS`
                WHERE (`protectfromredaction` = 1)
                    AND (`id` = ?)';

        //check each user for redaction protection
        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            //check if any specified users have protectfromredaction set to true
            if ($stmt = $dbObj->prepare($sql))
            {
                foreach ($pUserIDArray as $userToCheck)
                {
                    if ($stmt->bind_param('i', $userToCheck))
                    {
                        if ($stmt->execute())
                        {
                            if (($stmt->store_result()))
                            {
                                if ($stmt->num_rows > 0)
                                {
                                    // can not delete
                                    $resultArray['disallow'][] = array('id' => $userToCheck, 'reason' => 'protected');
                                }
                                else
                                {
                                    $resultArray['redact'][] = $userToCheck;
                                }
                            }
                            else
                            {
                                $error = 'str_DatabaseError';
                                $resultParam = __FUNCTION__ . ' store_result: ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $error = 'str_DatabaseError';
                            $resultParam = __FUNCTION__ . ' execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $error = 'str_DatabaseError';
                        $resultParam = __FUNCTION__ . ' bind_param ' . $dbObj->error;
                    }
                    $stmt->free_result();
                }
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $error = 'str_DatabaseError';
                $resultParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
            }
        }
        else
        {
            // could not open database connection
            $error = 'str_DatabaseError';
            $resultParam = __FUNCTION__ . ' connect ' . $dbObj->error;
        }

        $dbObj->close();


        $resultArray['error'] = $error;
        $resultArray['errorparam'] = $resultParam;

        return $resultArray;
    }

	/**
	 * @param string $companyCode
	 * @param string $groupCode
	 * @param string $brandCode
	 * @param string $format
	 * @param mixed[] $filters
	 */
	static function customerExport($companyCode, $groupCode, $brandCode, $format, $filters)
	{
		global $gSession;

		$encodedFilters = json_encode($filters);
		DatabaseObj::createEvent('TAOPIX_CUSTOMERDATAEXPORT',
			$companyCode,
			$groupCode,
			$brandCode,
			'',
			0,
			'Customer',
			$encodedFilters,
			$format,
			'',
			$gSession['browserlanguagecode'],
			'',
			'',
			'',
			0,
			0,
			$gSession['userid'],
			'',
			'',
			0
		);

		DatabaseObj::updateActivityLog(
			-1,
			0,
			$gSession['userid'],
			$gSession['userlogin'],
			$gSession['username'],
			0,
			'CUSTOMER',
			'CUSTOMEREXPORTREQUESTED',
			$encodedFilters,
			1
		);
	}
}

?>
