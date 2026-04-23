<?php
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsDatabase.php');

class AdminVouchers_model
{

    // is currently in use in session
    static function isUsedInSession($voucherCode)
    {
        $dbObj = DatabaseObj::getGlobalDBConnection();

        $useCount = 0;
        $result = '';
        $resultParam = '';

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT count(id) FROM SESSIONDATA WHERE vouchercode = ? AND sessionactive = 1 AND sessionenabled = 1 AND sessionexpiredate >= now()'))
            {
                if ($stmt->bind_param('s', $voucherCode))
                {
                    if ($stmt->bind_result($useCount))
                    {
                        if ($stmt->execute())
                        {
                            if (!$stmt->fetch())
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'isUsedInSession fetch ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'isUsedInSession execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'isUsedInSession bindresult ' . $dbObj->error;
                    }
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'isUsedInSession bind ' . $dbObj->error;
                }
                $stmt->free_result();
                $stmt->close();
            }
            else
            {
                $result = 'str_DatabaseError';
                $resultParam = 'isUsedInSession prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }
        else
        {
            $result = 'str_DatabaseError';
            $resultParam = 'isUsedInSession connect ' . $dbObj->error;
        }

        $resultArray = Array('result' => '', 'resultparam' => '', 'resultvalue' => '');
        if ($result == '')
        {
            if ($useCount > 0)
            {
                $resultArray['resultvalue'] = 1;
            }
            else
            {
                $resultArray['resultvalue'] = 0;
            }
        }
        else
        {
            $resultArray['result'] = $result;
            $resultArray['resultparam'] = $resultParam;
        }
        return $resultArray;
    }

    /**
     * Echos the list of vouchers for the promotion to be displayed in the grid.
     *
     * @param  $pPromotionID
     *
     * @version 3.0.0
     * @since Version 3.0.0
     * @author Dasha Salo
     * */
    static function listVouchers($pPromotionID)
    {
        global $gConstants, $gSession;
        $itemsArray = Array();
        $promotionID = 0;
        $promotionCode = '';
        $promotionName = '';
        $promotionCompanyCode = '';
        $totalCount = 0;
        $showResultVouchers = UtilsObj::getGETParam('resultvouchers', 0);
        $voucherGroupStatus = 0;
        $smarty = SmartyObj::newSmarty('AdminVouchers');

        //initialise cache for localization to prevent additional database calls to get the brand while looping
        LocalizationObj::formatLocaleDateTime('2000-01-01');

        $usageInfo = 0;

        /* Show cached recent results or get vouchers from database */
        if ($showResultVouchers == '1')
        {
            if (isset($gSession['vouchercreationresult']))
            {
                $voucherIDArray = $gSession['vouchercreationresult'];

                $dbObj = DatabaseObj::getGlobalDBConnection();

                if ($dbObj)
                {
                    $stmt = $dbObj->prepare('SELECT VOUCHERS.id, VOUCHERS.companycode, VOUCHERS.promotioncode, VOUCHERS.code, VOUCHERS.type, VOUCHERS.defaultdiscount,
                        VOUCHERS.name, VOUCHERS.description, VOUCHERS.startdate, VOUCHERS.enddate,
                        VOUCHERS.productcode, VOUCHERS.groupcode, VOUCHERS.userid, VOUCHERS.repeattype, VOUCHERS.discountsection,
                        VOUCHERS.discounttype, VOUCHERS.discountvalue, VOUCHERS.active, PRODUCTS.name,
                	USERS.contactfirstname, USERS.contactlastname, USERS.emailaddress
                	FROM `VOUCHERS`
                	LEFT JOIN `PRODUCTS` ON PRODUCTS.code = VOUCHERS.productcode
                	LEFT JOIN `USERS` ON USERS.id = VOUCHERS.userid
                	WHERE VOUCHERS.id = ?');
                    if($stmt)
                    {
                        $itemCount = count($voucherIDArray);
                        for ($i = 0; $i < $itemCount; $i++)
                        {
                            if ($stmt->bind_param('i', $voucherIDArray[$i]))
                            {
                                if ($stmt->bind_result($id, $voucherCompanyCode, $promotionCode, $code, $type, $defaultDiscount, $name, $description, $startDate, $endDate, $productCode, $groupCode, $userID, $repeatType, $discountSection, $discountType, $discountValue, $isActive, $productName, $contactFirstName, $contactLastName, $emailAddress))
                                {
                                    if ($stmt->execute())
                                    {
                                        if ($stmt->fetch())
                                        {
                                            $voucherItem = array();
                                            $voucherItem['id'] = $id;
                                            $voucherItem['companycode'] = "'" . UtilsObj::ExtJSEscape($voucherCompanyCode) . "'";
                                            $voucherItem['code'] = "'" . UtilsObj::ExtJSEscape($code) . "'";
                                            $voucherItem['type'] = "'" . UtilsObj::ExtJSEscape(LocalizationObj::getConstantName($smarty, $type, 'VOUCHERTYPE')) . "'";
                                            $voucherItem['defaultdiscount'] = $defaultDiscount;
                                            $voucherItem['name'] = "'" . UtilsObj::ExtJSEscape(LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $name, '')) . "'";
                                            $voucherItem['description'] = "'" . UtilsObj::ExtJSEscape($description) . "'";
                                            // Calculate the difference in days.
                                            $date1 = strtotime($startDate);
                                            $date2 = strtotime('2000-01-01');
                                            if ($date1 < $date2)
                                            {
                                                $startDate = '2000-01-01';
                                            }
                                            if ($endDate == '1970-01-01 01:00:00')
                                            {
                                                $endDate = '2000-01-01';
                                            }
                                            $startDate = LocalizationObj::formatLocaleDateTime($startDate);
                                            $endDate = LocalizationObj::formatLocaleDateTime($endDate);
                                            $voucherItem['startdate'] = "'" . UtilsObj::ExtJSEscape($startDate) . "'";
                                            $voucherItem['enddate'] = "'" . UtilsObj::ExtJSEscape($endDate) . "'";
                                            $voucherItem['productcode'] = "'" . UtilsObj::ExtJSEscape($productCode) . "'";

                                            if ($productCode != '')
                                            {
                                                $productDisplayName = $productCode . ' - ' . LocalizationObj::getLocaleString($productName, $gSession['browserlanguagecode'], true);
                                            }
                                            else
                                            {
                                                $productDisplayName = '<i>' . $smarty->get_config_vars('str_LabelAll') . '</i>';
                                            }

                                            $voucherItem['productname'] = "'" . UtilsObj::ExtJSEscape($productDisplayName) . "'";
                                            if ($groupCode != '')
                                            {
                                                $voucherItem['groupcode'] = "'" . UtilsObj::ExtJSEscape($groupCode) . "'";
                                            }
                                            else
                                            {
                                                $voucherItem['groupcode'] = "'" . UtilsObj::ExtJSEscape('<i>' . $smarty->get_config_vars('str_LabelAll') . '</i>') . "'";
                                            }
                                            $voucherItem['userid'] = "'" . UtilsObj::ExtJSEscape($userID) . "'";
                                            if ($userID != 0)
                                            {
                                                $voucherItem['username'] = "'" . UtilsObj::ExtJSEscape($contactFirstName . ' ' . $contactLastName . '<br>(' . $emailAddress . ')') . "'";
                                            }
                                            else
                                            {
                                                $voucherItem['username'] = "'" . UtilsObj::ExtJSEscape('<i>' . $smarty->get_config_vars('str_LabelAll') . '</i>') . "'";
                                            }
                                            $voucherItem['repeattype'] = "'" . UtilsObj::ExtJSEscape($smarty->get_config_vars('str_LabelRepeatType' . $repeatType)) . "'";
                                            $voucherItem['discountsection'] = "'" . UtilsObj::ExtJSEscape($smarty->get_config_vars('str_LabelDiscountSection' . $discountSection)) . "'";
                                            $discountTypeDisplay = UtilsObj::ExtJSEscape($smarty->get_config_vars('str_LabelDiscountType' . $discountType));
                                            if($type == TPX_VOUCHER_TYPE_SCRIPT)
                                            {
                                                $discountTypeDisplay = "'" . UtilsObj::ExtJSEscape(LocalizationObj::getConstantName($smarty, $type, 'VOUCHERTYPE')) . "'";
                                            }
                                            elseif (($discountType == 'VALUE') || ($discountType == 'VALUESET') || ($discountType == 'BOGVOFF'))
                                            {
                                                $discountTypeDisplay = $discountTypeDisplay . '<br>(' . $discountValue . ')';
                                            }
                                            elseif (($discountType == 'PERCENT') || ($discountType == 'BOGPOFF'))
                                            {
                                                $discountTypeDisplay = $discountTypeDisplay . '<br>(' . $discountValue . '%)';
                                            }
                                            $voucherItem['discounttype'] = "'" . UtilsObj::ExtJSEscape($discountTypeDisplay) . "'";
                                            $voucherItem['discountvalue'] = "'" . UtilsObj::ExtJSEscape($discountValue) . "'";
                                            $voucherItem['isactive'] = "'" . UtilsObj::ExtJSEscape($isActive) . "'";

                                            array_push($itemsArray, '[' . join(',', $voucherItem) . ']');
                                        }
                                    }
                                }
                            }
                            $stmt->free_result();
                        }
                        $stmt->close();
                    }
                    $dbObj->close();
                }
            }
        }
        else
        {
			//get search parameters from the control centre front end
            $start = (integer) UtilsObj::getPOSTParam('start', 0);
            $limit = (integer) UtilsObj::getPOSTParam('limit', 0);
            $sortBy = UtilsObj::getPOSTParam('sort', '');
            $sortDir = UtilsObj::getPOSTParam('dir', '');
            $searchFields = UtilsObj::getPOSTParam('fields', '');
			$companyCode = UtilsObj::getPOSTParam('companyCode', '');
			$hideInactive = 0;
			$hideInactiveStatement = '';

			// check that hideinactive has been sent before safely retrieving it
			if (isset($_POST['hideInactive']))
			{
				$hideInactive = filter_input(INPUT_POST, 'hideInactive', FILTER_SANITIZE_NUMBER_INT);
			}

            /* building a statement to bind variable number of parameters */
            $typesArray = array();
            $paramArray = array();
            $stmtArray = array();

            if ($searchFields != '')
            {
				//grab the query that the user is searching for in control centre
                $searchQuery = UtilsObj::getPOSTParam('query');
                $selectedfields = explode(',', str_replace('"', "", str_replace("]", "", str_replace("[", "", $searchFields))));

                if ($searchQuery != '')
                {
                    foreach ($selectedfields as $value)
                    {
                        switch ($value)
                        {
                            case 'voucherCode':
								$value = 'VOUCHERS.code';
                                break;
                            case 'voucherName':
								$value = 'VOUCHERS.name';
                                break;
                            case 'voucherDescription':
                                $value = 'VOUCHERS.description';
                                break;
                            case 'productName':
                                $value = 'VOUCHERS.productcode';
                                $stmtArray[] = '(' . $value . ' LIKE ?)';
                                $paramArray[] = '%' . $searchQuery . '%';
                                $typesArray[] = 's';
                                $value = 'PRODUCTS.name';
                                break;
                            case 'userName':
								$value = 'USERS.contactfirstname';
                                $stmtArray[] = '(' . $value . ' LIKE ?)';
                                $paramArray[] = '%' . $searchQuery . '%';
                                $typesArray[] = 's';
                                $value = 'USERS.contactlastname';
                                break;
							case 'groupCode':
								$value = 'VOUCHERS.groupcode';
								break;
                            default: $value = '';
                        }
                        if ($value != '')
                        {
                            $stmtArray[] = '(' . $value . ' LIKE ?)';
                            $paramArray[] = '%' . $searchQuery . '%';
                            $typesArray[] = 's';
                        }
                    }
				}
				else
				{
					if ($hideInactive == 1)
					{
						$hideInactiveStatement = ' AND (`VOUCHERS`.`active` = 1) ';
					}
				}
			}
			else
			{
				if ($hideInactive == 1)
				{
					$hideInactiveStatement = ' AND (`VOUCHERS`.`active` = 1) ';
				}
			}

            /* sorting */
            $customSort = 'code ASC';
            if ($sortBy != '')
            {
                switch ($sortBy)
                {
                    case 'voucherCode': $sortBy = 'VOUCHERS.code ' . $sortDir;
                        break;
                    case 'voucherName': $sortBy = 'VOUCHERS.name ' . $sortDir;
                        break;
                    case 'voucherDescription': $sortBy = 'VOUCHERS.description ' . $sortDir;
                        break;
                    case 'startDate': $sortBy = 'VOUCHERS.startdate ' . $sortDir;
                        break;
                    case 'endDate': $sortBy = 'VOUCHERS.enddate ' . $sortDir;
                        break;
                    case 'productName': $sortBy = 'VOUCHERS.productcode ' . $sortDir . ', PRODUCTS.name ' . $sortDir;
                        break;
                    case 'groupCode': $sortBy = 'VOUCHERS.groupcode ' . $sortDir;
                        break;
                    case 'userName': $sortBy = 'USERS.contactfirstname ' . $sortDir . ', USERS.contactlastname ' . $sortDir;
                        break;
                    case 'repeatType': $sortBy = 'VOUCHERS.repeattype ' . $sortDir;
                        break;
                    case 'discountSection': $sortBy = 'VOUCHERS.discountsection ' . $sortDir;
                        break;
                    case 'discountType': $sortBy = 'VOUCHERS.discounttype ' . $sortDir;
                        break;
                    case 'isActive': $sortBy = 'VOUCHERS.active ' . $sortDir;
                        break;
                    case 'usageCount': $sortBy = 'usageCount ' . $sortDir;
                        break;
                    case 'voucherType': $sortBy = 'VOUCHERS.type ' . $sortDir;
                        break;
                }
                $customSort = ', ' . $sortBy;
            }

            if ($pPromotionID > 0)
            {
                $promotionID = $pPromotionID;
                $promotionDataArray = DatabaseObj::getVoucherPromotionFromID($pPromotionID);
                $promotionCode = $promotionDataArray['code'];
                $promotionName = $promotionDataArray['name'];
                $promotionCompanyCode = $promotionDataArray['companycode'];
            }

            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if (($gConstants['optionms']) && ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN))
                {
                    $stmtArray = join(' OR ', $stmtArray);

                    $stmt = $dbObj->prepare('SELECT SQL_CALC_FOUND_ROWS VOUCHERS.id, VOUCHERS.companycode, VOUCHERS.code, VOUCHERS.type, VOUCHERS.defaultdiscount, VOUCHERS.name, VOUCHERS.description, VOUCHERS.startdate, VOUCHERS.enddate,
                        VOUCHERS.productcode,
                        VOUCHERS.groupcode, VOUCHERS.userid, VOUCHERS.repeattype, VOUCHERS.discountsection, VOUCHERS.discounttype, VOUCHERS.discountvalue, VOUCHERS.active, PRODUCTS.name,
                        USERS.contactfirstname, USERS.contactlastname, USERS.emailaddress,
                        (SELECT count(Distinct ORDERHEADER.id) FROM ORDERHEADER JOIN ORDERITEMS ON ORDERHEADER.id=ORDERITEMS.orderid WHERE ORDERHEADER.vouchercode = VOUCHERS.code AND ORDERITEMS.currentcompanycode=?),
                        IF (`VOUCHERS`.`hasproductgroup` = 0 OR (`VOUCHERS`.`hasproductgroup` = 1 AND NOT ISNULL(`pgl`.`id`)), 1, 0)
                        FROM `VOUCHERS`
                        LEFT JOIN `PRODUCTS` ON PRODUCTS.code = VOUCHERS.productcode
                        LEFT JOIN `USERS` ON USERS.id = VOUCHERS.userid
                        LEFT JOIN `productgrouplink` AS `pgl` ON `pgl`.`assigneecode` = `VOUCHERS`.`code`
                        WHERE VOUCHERS.type < ' . TPX_VOUCHER_TYPE_GIFTCARD . ' AND VOUCHERS.promotioncode = ? ' . $hideInactiveStatement . ' AND (VOUCHERS.companycode = ? OR VOUCHERS.companycode = "")' . (($stmtArray != '') ? 'AND (' . $stmtArray . ')' : '' ) . ' ORDER BY VOUCHERS.companycode' . $customSort . ' LIMIT ' . $limit . ' OFFSET ' . $start);

                    array_unshift($typesArray, 's');
                    array_unshift($paramArray, $gSession['userdata']['companycode']);
                    array_unshift($typesArray, 's');
                    array_unshift($paramArray, $promotionCode);
                    array_unshift($typesArray, 's');
                    array_unshift($paramArray, $gSession['userdata']['companycode']);
                }
                else
                {
                    if ($companyCode != '')
                    {
                        if ($companyCode == 'GLOBAL')
                        {
                            $paramArray[] = '';
                        }
                        else
                        {
                            $paramArray[] = $companyCode;
                        }
                        $stmtArray[] = '(VOUCHERS.companycode LIKE ?)';
                        $typesArray[] = 's';
                    }
                    $stmtArray = join(' OR ', $stmtArray);
                    $stmt = $dbObj->prepare('SELECT SQL_CALC_FOUND_ROWS VOUCHERS.id, VOUCHERS.companycode, VOUCHERS.code, VOUCHERS.type, VOUCHERS.defaultdiscount, VOUCHERS.name, VOUCHERS.description, VOUCHERS.startdate, VOUCHERS.enddate,
                        VOUCHERS.productcode, VOUCHERS.groupcode, VOUCHERS.userid, VOUCHERS.repeattype, VOUCHERS.discountsection,
                        VOUCHERS.discounttype, VOUCHERS.discountvalue, VOUCHERS.active, PRODUCTS.name,
                        USERS.contactfirstname, USERS.contactlastname, USERS.emailaddress,
                        (SELECT count(Distinct ORDERHEADER.id) FROM ORDERHEADER JOIN ORDERITEMS ON ORDERHEADER.id=ORDERITEMS.orderid WHERE ORDERHEADER.vouchercode = VOUCHERS.code) AS usageCount,
                        IF (`VOUCHERS`.`hasproductgroup` = 0 OR (`VOUCHERS`.`hasproductgroup` = 1 AND NOT ISNULL(`pgl`.`id`)), 1, 0)
                        FROM `VOUCHERS`
                        LEFT JOIN `PRODUCTS` ON PRODUCTS.code = VOUCHERS.productcode
                        LEFT JOIN `USERS` ON USERS.id = VOUCHERS.userid
                        LEFT JOIN `productgrouplink` AS `pgl` ON `pgl`.`assigneecode` = `VOUCHERS`.`code`
                        WHERE VOUCHERS.type < ' . TPX_VOUCHER_TYPE_GIFTCARD . ' AND VOUCHERS.promotioncode = ? ' . $hideInactiveStatement . (($stmtArray != '') ? 'AND (' . $stmtArray . ')' : '' ) . ' ORDER BY VOUCHERS.companycode' . $customSort . ' LIMIT ' . $limit . ' OFFSET ' . $start);
                    array_unshift($typesArray, 's');
                    array_unshift($paramArray, $promotionCode);
                }
                $bindOK = DatabaseObj::bindParams($stmt, $typesArray, $paramArray);

                if ($bindOK)
                {
                    if ($stmt->bind_result($id, $voucherCompanyCode, $code, $type, $defaultDiscount, $name, $description, $startDate, $endDate, $productCode, $groupCode, $userID, $repeatType,
                                            $discountSection, $discountType, $discountValue, $isActive, $productName, $contactFirstName, $contactLastName, $emailAddress, $usageInfo, $voucherGroupStatus))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
                                $voucherItem['id'] = $id;
                                $voucherItem['companycode'] = "'" . UtilsObj::ExtJSEscape($voucherCompanyCode) . "'";
                                $voucherItem['code'] = "'" . UtilsObj::ExtJSEscape($code) . "'";
                                $voucherItem['name'] = "'" . UtilsObj::ExtJSEscape(LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $name, '')) . "'";
                                $voucherItem['description'] = "'" . UtilsObj::ExtJSEscape($description) . "'";
                                $voucherItem['type'] = "'" . UtilsObj::ExtJSEscape(LocalizationObj::getConstantName($smarty, $type, 'VOUCHERTYPE')) . "'";
                                $voucherItem['defaultdiscount'] = $defaultDiscount;

                                // Calculate the difference in days.
                                $date1 = strtotime($startDate);
                                $date2 = strtotime('2000-01-01');
                                if ($date1 < $date2)
                                {
                                    $startDate = '2000-01-01';
                                }
                                if ($endDate == '1970-01-01 01:00:00')
                                {
                                    $endDate = '2000-01-01';
                                }

                                $startDate = LocalizationObj::formatLocaleDateTime($startDate);
                                $endDate = LocalizationObj::formatLocaleDateTime($endDate);

                                $voucherItem['startdate'] = "'" . UtilsObj::ExtJSEscape($startDate) . "'";
                                $voucherItem['enddate'] = "'" . UtilsObj::ExtJSEscape($endDate) . "'";
                                $voucherItem['productcode'] = "'" . UtilsObj::ExtJSEscape($productCode) . "'";


                                if ($productCode != '')
                                {
                                    $productDisplayName = $productCode . ' - ' . LocalizationObj::getLocaleString($productName, $gSession['browserlanguagecode'], true);
                                }
                                else
                                {
                                    $productDisplayName = '<i>' . $smarty->get_config_vars('str_LabelAll') . '</i>';
                                }

                                $voucherItem['productname'] = "'" . UtilsObj::ExtJSEscape($productDisplayName) . "'";


                                if ($groupCode != '')
                                {
                                    $voucherItem['groupcode'] = "'" . UtilsObj::ExtJSEscape($groupCode) . "'";
                                }
                                else
                                {
                                    $voucherItem['groupcode'] = "'" . UtilsObj::ExtJSEscape('<i>' . $smarty->get_config_vars('str_LabelAll') . '</i>') . "'";
                                }

                                $voucherItem['userid'] = "'" . UtilsObj::ExtJSEscape($userID) . "'";

                                if ($userID != 0)
                                {
                                    $voucherItem['username'] = "'" . UtilsObj::ExtJSEscape($contactFirstName . ' ' . $contactLastName . '<br>(' . $emailAddress . ')') . "'";
                                }
                                else
                                {
                                    $voucherItem['username'] = "'" . UtilsObj::ExtJSEscape('<i>' . $smarty->get_config_vars('str_LabelAll') . '</i>') . "'";
                                }

                                $voucherItem['repeattype'] = "'" . UtilsObj::ExtJSEscape($smarty->get_config_vars('str_LabelRepeatType' . $repeatType)) . "'";
                                $voucherItem['discountsection'] = "'" . UtilsObj::ExtJSEscape($smarty->get_config_vars('str_LabelDiscountSection' . $discountSection)) . "'";

                                $discountTypeDisplay = UtilsObj::ExtJSEscape($smarty->get_config_vars('str_LabelDiscountType' . $discountType));


                                if($type == TPX_VOUCHER_TYPE_SCRIPT)
                                {
                                    $discountTypeDisplay = UtilsObj::ExtJSEscape(LocalizationObj::getConstantName($smarty, $type, 'VOUCHERTYPE'));
                                }
                                elseif (($discountType == 'VALUE') || ($discountType == 'VALUESET') || ($discountType == 'BOGVOFF'))
                                {
                                    $discountTypeDisplay = $discountTypeDisplay . '<br>(' . $discountValue . ')';
                                }
                                elseif (($discountType == 'PERCENT') || ($discountType == 'BOGPOFF'))
                                {
                                    $discountTypeDisplay = $discountTypeDisplay . '<br>(' . $discountValue . '%)';
                                }
                                $voucherItem['discounttype'] = "'" . UtilsObj::ExtJSEscape($discountTypeDisplay) . "'";
                                $voucherItem['discountvalue'] = "'" . UtilsObj::ExtJSEscape($discountValue) . "'";
                                $voucherItem['usagecount'] = "'" . UtilsObj::ExtJSEscape($usageInfo) . "'";
                                $voucherItem['status'] = "'" . UtilsObj::ExtJSEscape($voucherGroupStatus) . "'";
                                $voucherItem['isactive'] = "'" . UtilsObj::ExtJSEscape($isActive) . "'";

                                array_push($itemsArray, '[' . join(',', $voucherItem) . ']');
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
                    $stmt = null;
                }
                $dbObj->close();
            }
        }

        $summaryArray = join(',', $itemsArray);
        if ($summaryArray != '')
        {
            $summaryArray = ', ' . $summaryArray;
        }
        echo '[[' . $totalCount . ']' . $summaryArray . ']';
        return;
    }

    /**
     * Returns promotion details for provided promotion id.
     *
     * @param  $pPromotionID
     * @return $resultArray
     *
     * @version 3.0.0
     * @since Version 3.0.0
     * @author Kavin Gale, Dasha Salo
     * */
    static function displayList($pPromotionID)
    {
        $resultArray = Array();
        $promotionID = 0;
        $promotionCode = '';
        $promotionName = '';
        $promotionCompanyCode = '';

        if ($pPromotionID > 0)
        {
            $promotionID = $pPromotionID;
            $promotionDataArray = DatabaseObj::getVoucherPromotionFromID($pPromotionID);
            $promotionCode = $promotionDataArray['code'];
            $promotionName = $promotionDataArray['name'];
            $promotionCompanyCode = $promotionDataArray['companycode'];
        }

        //$resultArray['vouchers'] = array();
        $resultArray['promotionid'] = $promotionID;
        $resultArray['promotioncode'] = $promotionCode;
        $resultArray['promotionname'] = $promotionName;
        $resultArray['promotioncompanycode'] = $promotionCompanyCode;

        return $resultArray;
    }

    /**
     * Makes voucher active or inactive.
     *
     * @param  $pPromotionID
     * @return $resultArray
     *
     * @version 3.0.0
     * @since Version 3.0.0
     * @author Kavin Gale, Dasha Salo
     * */
    static function voucherActivate()
    {
        global $gSession;
        $voucherList = explode(',', $_POST['idlist']);
        $voucehrCount = count($voucherList);
        $result = '';
        $resultParam = '';
        $isActive = $_POST['active'];
        $notDeletedVouchers = array();

        $voucherCode = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            for($i = 0; $i < $voucehrCount; $i++)
            {
                $id = $voucherList[$i];
                $stmt = $dbObj->prepare('SELECT `code` FROM `VOUCHERS` WHERE `id` = ?');
                if ($stmt)
                {
                    if ($stmt->bind_param('i', $id))
                    {
                        if ($stmt->execute())
                        {
                            if ($stmt->store_result())
                            {
                                if ($stmt->num_rows > 0)
                                {
                                    if ($stmt->bind_result($voucherCode))
                                    {
                                        $stmt->fetch();
                                    }
                                    else
                                    {
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'voucherActivate bindresult ' . $dbObj->error;
                                    }
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'voucherActivate store result ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'voucherActivate execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'voucherActivate bind params' . $dbObj->error;
                    }

                    $stmt->free_result();
                    $stmt->close();
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'voucherActivate prepare ' . $dbObj->error;
                }

                /* check if voucher is being used in the session */
                $usedInSession = self::isUsedInSession($voucherCode);
                if (($usedInSession['resultvalue'] == 1) && $isActive == 0)
                {
                    $notDeletedVouchers[] = $voucherCode;
                }
                else
                {
                    if ($result == '')
                    {
                        if ($stmt = $dbObj->prepare('UPDATE `VOUCHERS` SET `active` = ? WHERE `id` = ?'))
                        {
                            if ($stmt->bind_param('ii', $isActive, $id))
                            {
                                if ($stmt->execute())
                                {
                                    if ($isActive == 1)
                                    {
                                        DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                                $gSession['username'], 0, 'ADMIN', 'VOUCHER-DEACTIVATE', $id . ' ' . $voucherCode, 1);
                                    }
                                    else
                                    {
                                        DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                                $gSession['username'], 0, 'ADMIN', 'VOUCHER-ACTIVATE', $id . ' ' . $voucherCode, 1);
                                    }
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'voucherActivate execute ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'voucherActivate bind ' . $dbObj->error;
                            }
                            $stmt->free_result();
                            $stmt->close();
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'voucherActivate prepare ' . $dbObj->error;
                        }
                    }
                }
            }
            $dbObj->close();
        }
        else
        {
            $result = 'str_DatabaseError';
            $resultParam = 'voucherActivate connect ' . $dbObj->error;
        }

        if ($result == '')
        {
            $smarty = SmartyObj::newSmarty('AdminVouchers');

            if (count($notDeletedVouchers) > 0)
            {
                $msg = $smarty->get_config_vars('str_VouchersInUse') . ' ' . join(', ', $notDeletedVouchers) . '.';
                $title = $smarty->get_config_vars('str_TitleWarning');
            }
            else
            {
                $msg = '';
                $title = $smarty->get_config_vars('str_TitleConfirmation');
            }
            echo "{'success':'true', 'title':'" . UtilsObj::ExtJSEscape($title) . "', 'msg':'" . UtilsObj::ExtJSEscape($msg) . "' }";
        }
        else
        {
            echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }
        return;
    }

    static function createRandomVoucherCode()
    {
        $voucherCode = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $voucherValid = false;
            $repeatCount = 0;
            $id = 0;

            while(!$voucherValid)
            {
                $voucherCode = UtilsObj::createRandomString(12, true);

                if ($stmt = $dbObj->prepare('SELECT `id` FROM `VOUCHERS` WHERE `code` = ?'))
                {
                    if ($stmt->bind_param('s', $voucherCode))
                    {
                        if ($stmt->execute())
                        {
                            if ($stmt->store_result())
                            {
                                if ($stmt->num_rows > 0)
                                {
                                    if ($stmt->bind_result($id))
                                    {
                                        if ($stmt->fetch())
                                        {
                                            $repeatCount++;
                                            if ($repeatCount > 20)
                                            {
                                                $voucherCode = '';
                                                $voucherValid = true;
                                            }
                                        }
                                        else
                                        {
                                            $voucherValid = true;
                                        }
                                    }
                                }
                                else
                                {
                                    $voucherValid = true;
                                }
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

        return $voucherCode;
    }

    static function voucherAdd()
    {
        global $gSession;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $recordID = 0;

        if ($gConstants['optionms'])
        {
            $owner = $_POST['productionsitecode'];

            if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
            {
                $companyCode = $gSession['userdata']['companycode'];
            }
            else
            {
                $companyCode = '';
                if ($owner != '')
                {
                    //get company code for production site
                    $siteDatails = DatabaseObj::getSiteFromCode($owner);
                    $companyCode = $siteDatails['companycode'];
                }
            }
        }
        else
        {
            $owner = '';
            $companyCode = '';
        }

        $promotionID = $_POST['promotionid'];
        $promotionCode = '';
        $code = strtoupper($_POST['code']);
        $name = $_POST['name'];
        $description = $_POST['description'];
        $startDate = $_POST['startdatevalue'];
        $endDate = $_POST['enddatevalue'];
        $productCode = $_POST['productcode'];
        $groupCode = $_POST['groupcode'];
        $userID = $_POST['userid'];
        $minQty = $_POST['minqty'];
        $maxQty = $_POST['maxqty'];
        $lockQty = $_POST['lockqtyvalue'];
        $repeatType = $_POST['repeattype'];
        $discountSection = $_POST['discountsection'];
        $discountType = $_POST['discounttype'];
        $defaultDiscount = $_POST['defaultdiscountvalue'];
        $discountValue = $_POST['discountvalue'];
        $isActive = $_POST['isactive'];
        $type = $_POST['type'];
        $sellprice = $_POST['sellprice'];
        $agentfee = $_POST['agentfee'];
        $applicationMethod = $_POST['discountmethod'];
        $applyToQty = $_POST['discountapplytoqty'];
        $productGroupID = UtilsObj::getPOSTParam('groupid', 0);
        $productGroupUsed = ($productGroupID > 0) ? 1 : 0;
        $minimumOrderValue = $_POST['minordervalue'];
        $minimumOrderValueIncludesShipping = $_POST['minordervalueincludesshipping'];
        $minimumOrderValueIncludesTax = $_POST['minordervalueincludestax'];

        if (($code != '') && ($name != ''))
        {
            if ($promotionID > 0)
            {
                $promotionDataArray = DatabaseObj::getVoucherPromotionFromID($promotionID);
                $promotionCode = $promotionDataArray['code'];
            }

            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('INSERT INTO `VOUCHERS` (`id`, `datecreated`, `companycode`, `owner`, `promotioncode`, `code`, `type`, `defaultdiscount`, `name`, `description`, `startdate`, `enddate`, `productcode`,
                    `groupcode`, `userid`, `minimumqty`, `maximumqty`, `lockqty`, `repeattype`, `discountsection`, `discounttype`, `discountvalue`, `applicationmethod`, `maxqtytoapplydiscountto`, `sellprice`, `hasproductgroup`, `agentfee`,
                    `minordervalue`, `minordervalueincshipping`, `minordervalueinctax`, `active`)
                    VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
                {

                    if ($stmt->bind_param('ssssiissssssiiiisssdiididdiii', $companyCode, $owner, $promotionCode, $code, $type, $defaultDiscount,
                                    $name, $description, $startDate, $endDate, $productCode, $groupCode,
                                    $userID, $minQty, $maxQty, $lockQty, $repeatType, $discountSection, $discountType, $discountValue,
                                    $applicationMethod, $applyToQty, $sellprice, $productGroupUsed, $agentfee, $minimumOrderValue, $minimumOrderValueIncludesShipping, $minimumOrderValueIncludesTax, $isActive))
                    {
                        if ($stmt->execute())
                        {
                            $recordID = $dbObj->insert_id;

                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                    $gSession['username'], 0, 'ADMIN', 'VOUCHER-ADD', $recordID . ' ' . $code, 1);
                        }
                        else
                        {
                            // could not execute statement
                            // first check for a duplicate key (voucher code)
                            if ($stmt->errno == 1062)
                            {
                                $result = 'str_ErrorVoucherExists';
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'voucherAdd execute ' . $dbObj->error;
                            }
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'voucherAdd bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'voucherAdd prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'voucherAdd connect ' . $dbObj->error;
            }

            if ($result == '')
            {
                $linkRecordResultArray = DatabaseObj::insertProductGroupLinkRecord($productGroupID, $code, TPX_PRODUCTGROUP_ASSIGNEETYPE_VOUCHER);
                $result = $linkRecordResultArray['error'];
                $resultParam = $linkRecordResultArray['errorparam'];
            }
        }

        if ($result == '')
        {
            echo "{'success':'true', 'msg':'" . '' . "' }";
        }
        else
        {
            $smarty = SmartyObj::newSmarty('AdminVouchers');
            $message = str_replace('^0', $resultParam, $smarty->get_config_vars($result));

            echo '{"success":false,	"msg":"' . $message . '"}';
        }

        return;
    }

    static function buildEditLists(&$pResultArray)
    {
        $existingCategoryArray = Array();
        $productListArray = Array();
        $existingProductListArray = Array();
        $groupListArray = Array();
        $userListArray = Array();
        $productionSitesListArray = Array();
        $productGroupsListArray = Array();

        global $gSession, $gConstants;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if (($gConstants['optionms']) && ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN))
            {
                $stmt = $dbObj->prepare('SELECT `pr`.`code`, `pcl`.`productname` FROM `PRODUCTS` pr
						LEFT JOIN PRODUCTCOLLECTIONLINK pcl on `pcl`.`productcode` = `pr`.`code` WHERE `deleted` = 0 AND (`pr`.`companycode` = ? OR `pr`.`companycode` = "" OR `pr`.`companycode` IS NULL) ORDER BY `code`');
                $bindOK = $stmt->bind_param('s', $gSession['userdata']['companycode']);
            }
            else
            {
                if (isset($pResultArray['promotion']['companycode']))
                {
                    if ($pResultArray['promotion']['companycode'] == '')
                    {
                        $stmt = $dbObj->prepare('SELECT `pr`.`code`, `pcl`.`productname` FROM `PRODUCTS` pr
						LEFT JOIN PRODUCTCOLLECTIONLINK pcl on `pcl`.`productcode` = `pr`.`code` WHERE `deleted` = 0 ORDER BY `code`');
                        $bindOK = true;
                    }
                    else
                    {
                        $stmt = $dbObj->prepare('SELECT `pr`.`code`, `pcl`.`productname` FROM `PRODUCTS` pr
						LEFT JOIN PRODUCTCOLLECTIONLINK pcl on `pcl`.`productcode` = `pr`.`code` WHERE `deleted` = 0 AND (`pr`.`companycode` = ? OR `pr`.`companycode` = "" OR `pr`.`companycode` IS NULL) ORDER BY `code`');
                        $bindOK = $stmt->bind_param('s', $pResultArray['promotion']['companycode']);
                    }
                }
                else
                {
                    $stmt = $dbObj->prepare('SELECT `pr`.`code`, `pcl`.`productname` FROM `PRODUCTS` pr
						LEFT JOIN PRODUCTCOLLECTIONLINK pcl on `pcl`.`productcode` = `pr`.`code` WHERE `deleted` = 0 ORDER BY `pr`.`code`');
                    $bindOK = true;
                }
            }

            if ($bindOK)
            {
                if ($stmt->bind_result($productCode, $productName))
                {
                    if ($stmt->execute())
                    {
                        while($stmt->fetch())
                        {
                            // before adding the productcode make sure it doesn't already exist
							if (!in_array($productCode, $existingProductListArray))
							{
								 $productItem['code'] = $productCode;
                            	 $productItem['name'] = $productName;
                            	 array_push($productListArray, $productItem);
                            	 array_push($existingProductListArray, $productCode);
							}
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }

            if ($gConstants['optionms'])
            {
                if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
                {
                    $stmt = $dbObj->prepare('SELECT `code`, `name` FROM `SITES` WHERE (`productionsitekey` <> "") AND `companycode` = ? ORDER BY `code`');
                    $bindOK = $stmt->bind_param('s', $gSession['userdata']['companycode']);
                }
                else
                {
                    if (isset($pResultArray['promotion']['companycode']))
                    {
                        if ($pResultArray['promotion']['companycode'] == '')
                        {
                            $stmt = $dbObj->prepare('SELECT `code`, `name` FROM `SITES` WHERE (`productionsitekey` <> "") ORDER BY `code`');
                            $bindOK = true;
                        }
                        else
                        {
                            $stmt = $dbObj->prepare('SELECT `code`, `name` FROM `SITES` WHERE (`productionsitekey` <> "") AND (`companycode` = ? OR `companycode` = "") ORDER BY `code`');
                            $bindOK = $stmt->bind_param('s', $pResultArray['promotion']['companycode']);
                        }
                    }
                    else
                    {
                        $stmt = $dbObj->prepare('SELECT `code`, `name` FROM `SITES` WHERE (`productionsitekey` <> "") ORDER BY `code`');
                        $bindOK = true;
                    }
                }

                if ($bindOK)
                {
                    if ($stmt->bind_result($productionSiteCode, $productionSiteName))
                    {
                        if ($stmt->execute())
                        {
                            while($stmt->fetch())
                            {
                                $userItem['code'] = $productionSiteCode;
                                $userItem['name'] = $productionSiteName;
                                array_push($productionSitesListArray, $userItem);
                            }
                        }
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = NULL;
                }
            }


            if (($gConstants['optionms']) && ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN))
            {
                $stmt = $dbObj->prepare('SELECT `groupcode` FROM `LICENSEKEYS` WHERE (`companycode` = ? OR `companycode` = "" OR `companycode` IS NULL) ORDER BY `groupcode`');
                $bindOK = $stmt->bind_param('s', $gSession['userdata']['companycode']);
            }
            else
            {
                if (isset($pResultArray['promotion']['companycode']))
                {
                    if ($pResultArray['promotion']['companycode'] != '')
                    {
                        $stmt = $dbObj->prepare('SELECT `groupcode` FROM `LICENSEKEYS` WHERE (`companycode` = ? OR `companycode` = "" OR `companycode` IS NULL) ORDER BY `groupcode`');
                        $bindOK = $stmt->bind_param('s', $pResultArray['promotion']['companycode']);
                    }
                    else
                    {
                        $stmt = $dbObj->prepare('SELECT `groupcode` FROM `LICENSEKEYS` ORDER BY `groupcode`');
                        $bindOK = true;
                    }
                }
                else
                {
                    $stmt = $dbObj->prepare('SELECT `groupcode` FROM `LICENSEKEYS` ORDER BY `groupcode`');
                    $bindOK = true;
                }
            }

            if ($bindOK)
            {
                if ($stmt->bind_result($groupCode))
                {
                    if ($stmt->execute())
                    {
                        while($stmt->fetch())
                        {
                            array_push($groupListArray, $groupCode);
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }

            $dbObj->close();
        }

        $productGroupResultArray = DatabaseObj::getProductGroupList(0, -1, '');

        if ($productGroupResultArray['error'] === '')
        {
            $productGroupsListArray = $productGroupResultArray['data']['groups'];
        }

        $pResultArray['products'] = $productListArray;
        $pResultArray['productionsites'] = $productionSitesListArray;
        $pResultArray['groups'] = $groupListArray;
        $pResultArray['users'] = $userListArray;
        $pResultArray['productgroups'] = $productGroupsListArray;
    }

    static function displayEdit($pVoucherID)
    {
        $resultArray = Array();

        $id = 0;
        $promotionCode = '';
        $code = '';
        $name = '';
        $description = '';
        $startDate = '';
        $endDate = '';
        $productCode = '';
        $groupCode = '';
        $userID = 0;
        $repeatType = '';
        $discountSection = '';
        $discountType = '';
        $discountValue = 0.00;
        $isActive = 0;
        $owner = '';
        $isUsedInOrder = 0;
        $type = TPX_VOUCHER_TYPE_PREPAID;
        $defaultDiscount = 0;
        $sellprice = 0.00;
        $agentfee = 0.00;
        $minQty = 0;
        $maxQty = 0;
        $lockQty = 0;
        $applicationMethod = TPX_VOUCHER_APPLY_EACH_MATCHING_PRODUCT;
        $applyToQty = 9999;
        $productGroupID = 0;
        $minimumOrderValue = 0.00;
        $minimumOrderValueIncludesShipping = 0;
        $minimumOrderValueIncludesTax = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $stmt = $dbObj->prepare('SELECT `v`.`id`, `v`.`owner`, `v`.`promotioncode`, `v`.`code`, `v`.`type`, `v`.`defaultdiscount`, `v`.`name`, `v`.`description`, `v`.`startdate`,
                                        `v`.`enddate`, `v`.`productcode`, `v`.`groupcode`, `v`.`userid`,
                                        `v`.`minimumqty`, `v`.`maximumqty`, `v`.`lockqty`, `v`.`repeattype`, `v`.`discountsection`, `v`.`discounttype`,
                                        `v`.`discountvalue`, `v`.`sellprice`, `v`.`agentfee`, `v`.`applicationmethod`, `v`.`maxqtytoapplydiscountto`,
                                        `v`.`minordervalue`, `v`.`minordervalueincshipping`,`v`.`minordervalueinctax`,`v`.`active`,
                                        (SELECT count(ORDERHEADER.id) FROM ORDERHEADER WHERE ORDERHEADER.vouchercode = `v`.`code`),  COALESCE(`pgl`.`productgroupid`, 0)
                                        FROM `VOUCHERS` AS `v`
                                        LEFT JOIN `productgrouplink` AS `pgl` ON (`v`.`hasproductgroup` = 1 AND
                                            `pgl`.`assigneetype` = ' . TPX_PRODUCTGROUP_ASSIGNEETYPE_VOUCHER . '
                                            AND `v`.`code` = `pgl`.`assigneecode`)
                                        WHERE `v`.`id` = ?');
            if ($stmt)
            {
                if ($stmt->bind_param('i', $pVoucherID))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($id, $owner, $promotionCode, $code, $type, $defaultDiscount, $name, $description, $startDate,
                                                $endDate, $productCode, $groupCode, $userID,
                                                $minQty, $maxQty, $lockQty, $repeatType, $discountSection, $discountType, $discountValue,
                                                $sellprice, $agentfee, $applicationMethod, $applyToQty, $minimumOrderValue, $minimumOrderValueIncludesShipping,
                                                $minimumOrderValueIncludesTax, $isActive, $isUsedInOrder, $productGroupID))
                                {
                                    $stmt->fetch();
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

        $resultArray['id'] = $id;
        $resultArray['owner'] = $owner;
        $resultArray['promotioncode'] = $promotionCode;
        $resultArray['code'] = $code;
        $resultArray['type'] = $type;
        $resultArray['defaultdiscount'] = $defaultDiscount;
        $resultArray['name'] = $name;
        $resultArray['description'] = $description;
        $resultArray['startdate'] = $startDate;
        $resultArray['enddate'] = $endDate;
        $resultArray['productcode'] = $productCode;
        $resultArray['groupcode'] = $groupCode;
        $resultArray['userid'] = $userID;
        $resultArray['minqty'] = $minQty;
        $resultArray['maxqty'] = $maxQty;
        $resultArray['lockqty'] = $lockQty;
        $resultArray['repeattype'] = $repeatType;
        $resultArray['discountsection'] = $discountSection;
        $resultArray['discounttype'] = $discountType;
        $resultArray['discountvalue'] = $discountValue;
        $resultArray['sellprice'] = $sellprice;
        $resultArray['agentfee'] = $agentfee;
        $resultArray['licenseevalue'] = $sellprice - $agentfee;
        $resultArray['isactive'] = $isActive;
        $resultArray['voucherusedinorder'] = $isUsedInOrder;
        $resultArray['type'] = $type;
        $resultArray['sellprice'] = $sellprice;
        $resultArray['agentfee'] = $agentfee;
        $resultArray['applicationmethod'] = $applicationMethod;
        $resultArray['applytoqty'] = $applyToQty;
        $resultArray['productgroupid'] = $productGroupID;
        $resultArray['minimumordervalue'] = $minimumOrderValue;
        $resultArray['minordervalueincludesshipping'] = $minimumOrderValueIncludesShipping;
        $resultArray['minordervalueincludestax'] = $minimumOrderValueIncludesTax;

        return $resultArray;
    }

    static function voucherEdit()
    {
        global $gSession;
        global $gConstants;

        $result = '';
        $resultParam = '';

        if ($gConstants['optionms'])
        {
            $owner = $_POST['productionsitecode'];
            if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
            {
                $companyCode = $gSession['userdata']['companycode'];
            }
            else
            {
                $companyCode = '';
                if ($owner != '')
                {
                    //get company code for production site
                    $siteDatails = DatabaseObj::getSiteFromCode($owner);
                    $companyCode = $siteDatails['companycode'];
                }
            }
        }
        else
        {
            $owner = '';
            $companyCode = '';
        }

        $id = $_POST['id'];
        $promotionID = $_POST['promotionid'];
        $promotionCode = $_POST['promotioncode'];
        $code = $_POST['code'];
        $name = UtilsObj::decodeString($_POST['name']);
        $description = UtilsObj::decodeString($_POST['description']);
        $startDate = $_POST['startdatevalue'];
        $endDate = $_POST['enddatevalue'];
        $productCode = $_POST['productcode'];
        $groupCode = $_POST['groupcode'];
        $userID = $_POST['userid'];
        $minQty = $_POST['minqty'];
        $maxQty = $_POST['maxqty'];
        $lockQty = $_POST['lockqtyvalue'];
        $repeatType = $_POST['repeattype'];
        $discountSection = $_POST['discountsection'];
        $discountType = $_POST['discounttype'];
        $defaultDiscount = $_POST['defaultdiscountvalue'];
        $discountValue = $_POST['discountvalue'];
        $isActive = $_POST['isactive'];
        $type = $_POST['type'];
        $sellprice = $_POST['sellprice'];
        $agentfee = $_POST['agentfee'];
        $applicationMethod = $_POST['discountmethod'];
        $applyToQty = $_POST['discountapplytoqty'];
        $productGroupID = UtilsObj::getPOSTParam('groupid', 0);
        $productGroupUsed = ($productGroupID > 0) ? 1 : 0;
        $minimumOrderValue = $_POST['minordervalue'];
        $minimumOrderValueIncludesShipping = $_POST['minordervalueincludesshipping'];
        $minimumOrderValueIncludesTax = $_POST['minordervalueincludestax'];

        if (($id > 0) && ($code != '') && ($name != ''))
        {
            // delete any pre-existing product group link records
            $linkDeleteResultArray = DatabaseObj::deleteProductGroupLinkRecordsByAssigneeCode($code, TPX_PRODUCTGROUP_ASSIGNEETYPE_VOUCHER);

            $result = $linkDeleteResultArray['error'];
            $resultParam = $linkDeleteResultArray['errorparam'];

            if ($result == '')
            {
                $dbObj = DatabaseObj::getGlobalDBConnection();
                if ($dbObj)
                {
                    if ($stmt = $dbObj->prepare('UPDATE `VOUCHERS` SET `companyCode` = ?, `owner` = ?, `type` = ?, `defaultdiscount` = ?, `name` = ?, `description` = ?, `startdate` = ?, `enddate` = ?, `productcode` = ?, `groupcode` = ?, `userid` = ?,
                        `minimumqty` = ?, `maximumqty` = ?, `lockqty` = ?, `repeattype` = ?, `discountsection` = ?, `discounttype` = ?, `discountvalue` = ?, `applicationmethod` = ?, `maxqtytoapplydiscountto` = ?,
                        `sellprice` = ?, `agentfee` = ?, `hasproductgroup` = ?, `minordervalue` = ?, `minordervalueincshipping` = ?, `minordervalueinctax` = ?, `active` = ? WHERE `id` = ?'))
                    {
                        if ($stmt->bind_param('ssiissssssiiiisssdiiddidiiii', $companyCode, $owner, $type, $defaultDiscount, $name, $description, $startDate,
                                        $endDate, $productCode, $groupCode, $userID, $minQty,
                                        $maxQty, $lockQty, $repeatType, $discountSection, $discountType, $discountValue, $applicationMethod,
                                        $applyToQty, $sellprice, $agentfee, $productGroupUsed, $minimumOrderValue, $minimumOrderValueIncludesShipping, $minimumOrderValueIncludesTax, $isActive, $id))
                        {
                            if ($stmt->execute())
                            {
                                DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                        $gSession['username'], 0, 'ADMIN', 'VOUCHER-UPDATE', $id . ' ' . $code, 1);
                            }
                            else
                            {
                                // first check for a duplicate key (voucher code)
                                if ($stmt->errno == 1062)
                                {
                                    $result = 'str_ErrorVoucherExists';
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'voucherEdit execute ' . $dbObj->error;
                                }
                            }
                        }
                        else
                        {
                            // could not bind parameters
                            $result = 'str_DatabaseError';
                            $resultParam = 'voucherEdit bind ' . $dbObj->error;
                        }
                        $stmt->free_result();
                    }
                    else
                    {
                        // could not prepare statement
                        $result = 'str_DatabaseError';
                        $resultParam = 'voucherEdit prepare ' . $dbObj->error;
                    }
                    $dbObj->close();
                }
                else
                {
                    // could not open database connection
                    $result = 'str_DatabaseError';
                    $resultParam = 'voucherEdit connect ' . $dbObj->error;
                }
            }
        }

        if (($result == '') && ($productGroupID != 0))
        {
            $linkRecordResultArray = DatabaseObj::insertProductGroupLinkRecord($productGroupID, $code, TPX_PRODUCTGROUP_ASSIGNEETYPE_VOUCHER);
            $result = $linkRecordResultArray['error'];
            $resultParam = $linkRecordResultArray['errorparam'];
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

    /**
     * Deletes the voucher by id
     *
     * @param  $pPromotionID
     *
     * @version 3.0.0
     * @since Version 1.0.0
     * @author Kavin Gale, Dasha Salo
     * */
    static function voucherDelete()
    {
        global $gSession;

        $voucherList = explode(',', $_POST['idlist']);
        $voucherCount = count($voucherList);
        $result = '';
        $resultParam = '';
        $voucherCode = '';
        $notDeletedVouchers = array();

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            for($i = 0; $i < $voucherCount; $i++)
            {
                $voucherID = $voucherList[$i];
                $stmt = $dbObj->prepare('SELECT `code` FROM `VOUCHERS` WHERE `id` = ?');
                if ($stmt)
                {
                    if ($stmt->bind_param('i', $voucherID))
                    {
                        if ($stmt->execute())
                        {
                            if ($stmt->store_result())
                            {
                                if ($stmt->num_rows > 0)
                                {
                                    if ($stmt->bind_result($voucherCode))
                                    {
                                        $stmt->fetch();
                                    }
                                    else
                                    {
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'voucherDelete bindresult ' . $dbObj->error;
                                    }
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'voucherDelete store result ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'voucherDelete execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'voucherDelete bind param' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'voucherDelete prepare ' . $dbObj->error;
                }

                $usedInSession = self::isUsedInSession($voucherCode);
                if ($usedInSession['resultvalue'] == 1)
                {
                    $notDeletedVouchers[] = $voucherCode;
                }
                else
                {
                    if ($result == '')
                    {
                        $stmt = $dbObj->prepare('DELETE FROM `VOUCHERS` WHERE `id` = ?');
                        if ($stmt)
                        {
                            if ($stmt->bind_param('i', $voucherID))
                            {
                                if ($stmt->execute())
                                {
                                    DatabaseObj::updateActivityLog2($dbObj, $gSession['ref'], 0, $gSession['userid'],
                                            $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'VOUCHER-DELETE',
                                            $voucherID . ' ' . $voucherCode, 1);

                                    // delete the product group link record
                                    DatabaseObj::deleteProductGroupLinkRecordsByAssigneeCode($voucherCode, TPX_PRODUCTGROUP_ASSIGNEETYPE_VOUCHER);
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'voucherDelete execute ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'voucherDelete bind ' . $dbObj->error;
                            }
                            $stmt->free_result();
                            $stmt->close();
                            $stmt = null;
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'voucherDelete prepare ' . $dbObj->error;
                        }
                    }
                }
            }
            $dbObj->close();
        }
        else
        {
            $result = 'str_DatabaseError';
            $resultParam = 'voucherDelete connect ' . $dbObj->error;
        }

        if ($result == '')
        {
            $smarty = SmartyObj::newSmarty('AdminVouchers');
            $title = $smarty->get_config_vars('str_TitleConfirmation');
            if (count($notDeletedVouchers) > 0)
            {
                $msg = $smarty->get_config_vars('str_VouchersInUse') . ' ' . join(', ', $notDeletedVouchers) . '.';
            }
            else
            {
                $msg = $smarty->get_config_vars('str_VouchersDeleted');
            }
            echo "{'success':'true', 'title':'" . UtilsObj::ExtJSEscape($title) . "', 'msg':'" . UtilsObj::ExtJSEscape($msg) . "' }";
        }
        else
        {
            echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }
        return;
    }

    static function voucherDelete2($pDbObj, $pVoucherIDList, $pVoucherDataArray)
    // delete a voucher, use provided DB object
    {
        global $gSession;

		$sql = 'DELETE FROM `VOUCHERS` WHERE (`id` IN (' . $pVoucherIDList . '))';

        if (count($pVoucherDataArray) > 0)
        {
            if ($stmt = $pDbObj->prepare($sql));
            {
				if ($stmt->execute())
				{
					$voucherCount = count($pVoucherDataArray);

					for ($i = 0; $i < $voucherCount; $i++)
					{
						DatabaseObj::updateActivityLog2($pDbObj, $gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
							$gSession['username'], 0, 'ADMIN', 'VOUCHER-DELETE', $pVoucherDataArray[$i]['id'] . ' ' . $pVoucherDataArray[$i]['code'], 1);
					}
				}

                $stmt->free_result();
                $stmt->close();
            }
        }
    }

    static function voucherCreate()
    {
        global $gSession;
        global $gConstants;

        $result = '';
        $resultParam = '';
        $recordID = 0;
        $newVouchersIds = array();

        if ($gConstants['optionms'])
        {
            $owner = $_POST['productionsitecode'];
            if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
            {
                $companyCode = $gSession['userdata']['companycode'];
            }
            else
            {
                $companyCode = '';
                if ($owner != '')
                {
                    //get company code for production site
                    $siteDatails = DatabaseObj::getSiteFromCode($owner);
                    $companyCode = $siteDatails['companycode'];
                }
            }
        }
        else
        {
            $owner = '';
            $companyCode = '';
        }

        $promotionID = $_POST['promotionid'];
        $promotionCode = '';
        $isRandom = $_POST['israndom'];
        $startNumber = $_POST['startnumber'];
        $voucherQty = $_POST['qty'];
        $vouchercodelength = $_POST['vouchercodelength'];
        $codePrefix = $_POST['codeprefix'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $type = $_POST['type'];
        $startDate = $_POST['startdatevalue'];
        $endDate = $_POST['enddatevalue'];
        $productCode = $_POST['productcode'];
        $groupCode = $_POST['groupcode'];
        $userID = $_POST['userid'];
        $minQty = $_POST['minqty'];
        $maxQty = $_POST['maxqty'];
        $lockQty = $_POST['lockqtyvalue'];
        $repeatType = $_POST['repeattype'];
        $discountSection = $_POST['discountsection'];
        $discountType = $_POST['discounttype'];
        $defaultDiscount = $_POST['defaultdiscountvalue'];
        $discountValue = $_POST['discountvalue'];
        $sellprice = $_POST['sellprice'];
        $agentfee = $_POST['agentfee'];
        $isActive = $_POST['isactive'];
        $applicationMethod = $_POST['discountmethod'];
        $applyToQty = $_POST['discountapplytoqty'];
        $productGroupID = UtilsObj::getPOSTParam('groupid', 0);
        $productGroupUsed = ($productGroupID > 0) ? 1 : 0;
        $minimumOrderValue = $_POST['minordervalue'];
        $minimumOrderValueIncludesShipping = $_POST['minordervalueincludesshipping'];
        $minimumOrderValueIncludesTax = $_POST['minordervalueincludestax'];

        $vouchersArray = Array();
        $voucherIDArray = Array();

        if ($voucherQty > 3000)
        {
            $voucherQty = 3000; // double-check, just in case
        }
        $voucherEndNumber = $startNumber + $voucherQty - 1;

        if ($name != '')
        {
            if ($promotionID > 0)
            {
                $promotionDataArray = DatabaseObj::getVoucherPromotionFromID($promotionID);
                $promotionCode = $promotionDataArray['code'];
            }

            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('INSERT INTO `VOUCHERS` (`id`, `datecreated`, `companycode`, `owner`, `promotioncode`, `code`, `type`, `defaultdiscount`, `name`, `description`, `startdate`, `enddate`,
                    `productcode`, `groupcode`, `userid`, `minimumqty`, `maximumqty`, `lockqty`, `repeattype`, `discountsection`, `discounttype`, `discountvalue`, `applicationmethod`, `maxqtytoapplydiscountto`, `sellprice`, `hasproductgroup`, `agentfee`,
                    `minordervalue`, `minordervalueincshipping`, `minordervalueinctax`,`active`)
                    VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
                {
                    $voucherCount = 0;
                    while($voucherCount < $voucherQty)
                    {
                        UtilsObj::resetPHPScriptTimeout(30); // just in case

                        if ($isRandom == 1)
                        {
                            if(isset($vouchercodelength))
                            {
                                $voucherCode = $codePrefix . UtilsObj::createRandomString($vouchercodelength, true);
                            }
                            else
                            {
                                $voucherCode = $codePrefix . UtilsObj::createRandomString(12, true);
                            }
                        }
                        else
                        {
                            $voucherNumber = str_pad($startNumber, strlen($voucherEndNumber), '0', STR_PAD_LEFT);

                            if (strpos($codePrefix, '_') === false)
                            {
                                $voucherCode = $codePrefix . $voucherNumber;
                            }
                            else
                            {
                                $voucherCode = str_replace('_', $voucherNumber, $codePrefix);
                            }

                            $startNumber++;

                            // the voucher code is not random so increase the voucher count as it may already exist
                            $voucherCount++;
                        }

                        if ($stmt->bind_param('ssssiissssssiiiisssdiididdiii', $companyCode, $owner, $promotionCode, $voucherCode, $type,
                                        $defaultDiscount, $name, $description, $startDate, $endDate,
                                        $productCode, $groupCode, $userID, $minQty, $maxQty, $lockQty, $repeatType, $discountSection,
                                        $discountType, $discountValue, $applicationMethod, $applyToQty, $sellprice, $productGroupUsed, $agentfee,
                                        $minimumOrderValue, $minimumOrderValueIncludesShipping, $minimumOrderValueIncludesTax, $isActive))
                        {
                            if ($stmt->execute())
                            {
                                $recordID = $dbObj->insert_id;

                                DatabaseObj::updateActivityLog2($dbObj, $gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                        $gSession['username'], 0, 'ADMIN', 'VOUCHER-CREATE', $recordID . ' ' . $voucherCode, 1);

                                // retrieve the voucher and store it so that we can display the result
                                if ($stmt2 = $dbObj->prepare('SELECT PRODUCTS.name, USERS.contactfirstname, USERS.contactlastname, USERS.emailaddress
                                    FROM `VOUCHERS`
                                    LEFT JOIN `PRODUCTS` ON PRODUCTS.code = VOUCHERS.productcode
                                    LEFT JOIN `USERS` ON USERS.id = VOUCHERS.userid
                                    WHERE VOUCHERS.id = ?'))
                                {
                                    if ($stmt2->bind_param('i', $recordID))
                                    {
                                        if ($stmt2->bind_result($productName, $contactFirstName, $contactLastName, $emailAddress))
                                        {
                                            if ($stmt2->execute())
                                            {
                                                while($stmt2->fetch())
                                                {
                                                    $newVouchersIds[] = $recordID;
                                                    $voucherItem['id'] = $recordID;
                                                    $voucherItem['promotioncode'] = $promotionCode;
                                                    $voucherItem['code'] = $voucherCode;
                                                    $voucherItem['type'] = $type;
                                                    $voucherItem['name'] = $name;
                                                    $voucherItem['description'] = $description;
                                                    $voucherItem['startdate'] = $startDate;
                                                    $voucherItem['enddate'] = $endDate;
                                                    $voucherItem['productcode'] = $productCode;
                                                    $voucherItem['productname'] = $productName;
                                                    $voucherItem['groupcode'] = $groupCode;
                                                    $voucherItem['userid'] = $userID;
                                                    $voucherItem['username'] = $contactFirstName . ' ' . $contactLastName . '<br>(' . $emailAddress . ')';
                                                    $voucherItem['repeattype'] = $repeatType;
                                                    $voucherItem['discountsection'] = $discountSection;
                                                    $voucherItem['discounttype'] = $discountType;
                                                    $voucherItem['discountvalue'] = $discountValue;
                                                    $voucherItem['isactive'] = $isActive;
                                                    $voucherItem['applicationmethod'] = $applicationMethod;
                                                    $voucherItem['applytoqty'] = $applyToQty;

                                                    array_push($vouchersArray, $voucherItem);
                                                    array_push($voucherIDArray, $recordID);
                                                }
                                            }
                                        }
                                    }
                                }

                                $stmt2->free_result();
                                $stmt2->close();
                                $stmt2 = null;

                                // for random vouchers only increase the count if the voucher record was created
                                if ($isRandom == 1)
                                {
                                    $voucherCount++;
                                }
                            }
                            else
                            {
                                // could not execute statement
                                // first check for a duplicate key (voucher code)
                                if ($stmt->errno == 1062)
                                {
                                    // this error message should only be returned if the voucher is not auto generated.
                                    if ($isRandom == 0)
                                    {
                                        $result = 'str_ErrorVoucherExists';
                                    }
                                }
                                else
                                {
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'voucherCreate execute ' . $dbObj->error;
                                    break;
                                }
                            }
                        }
                        else
                        {
                            // could not bind parameters
                            $result = 'str_DatabaseError';
                            $resultParam = 'voucherCreate bind ' . $dbObj->error;
                        }
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'voucherCreate prepare ' . $dbObj->error;
                }
                $dbObj->close();

                if ($result === '')
                {
                    foreach ($vouchersArray as $theVoucher)
                    {
                        $linkRecordResultArray = DatabaseObj::insertProductGroupLinkRecord($productGroupID, $theVoucher['code'], TPX_PRODUCTGROUP_ASSIGNEETYPE_VOUCHER);
                        $result = $linkRecordResultArray['error'];
                        $resultParam = $linkRecordResultArray['errorparam'];

                        if ($result !== '')
                        {
                            // if something has gone wrong we want to bail out
                            // we don't need to cleanup as the generated vouchers will have the hasproductgroup flag set
                            break;
                        }
                    }
                }
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'voucherCreate connect ' . $dbObj->error;
            }
        }

        // store the vouchers which have just been created so that the user can save them to disk
        $gSession['vouchercreationresult'] = $voucherIDArray;
        DatabaseObj::updateSession();

        if ($result == '')
        {
            echo "{'success':'true', 'msg':'" . '' . "', data: '" . join(',', $newVouchersIds) . "' }";
        }
        else
        {
            $smarty = SmartyObj::newSmarty('AdminVouchers');
            echo '{"success":false,	"msg":"' . str_replace('^0', $resultParam, $smarty->get_config_vars($result)) . '"}';
        }

        return;
    }

    static function voucherImport()
    {
        global $gSession;
        global $gConstants;

        $textData = '';
        $voucherCodes = array();
        $voucherCodesItem = '';

        $voucherCodeFile = $_FILES['importcodes']['tmp_name'];
        $voucherCodeFileType = $_FILES['importcodes']['type'];
        $voucherCodeFileSize = $_FILES['importcodes']['size'];
        $voucherCodeFileData = '';

        if ($voucherCodeFileSize > 0)
        {
            //Once file is uploaded read the file
            if (is_uploaded_file($voucherCodeFile))
            {
                $textData = UtilsObj::readTextFile($voucherCodeFile);
                $textData = str_replace("\r\n", "\n", $textData);
                $textData = str_replace("\r", "\n", $textData);

                UtilsObj::deleteFile($voucherCodeFile);

                //assign values to array indexes.
                $voucherCodes = explode("\n", $textData);
                $voucherCodeCount = count($voucherCodes);
            }
        }

        $result = '';
        $resultParam = '';
        $recordID = 0;

        if ($gConstants['optionms'])
        {
            $owner = $_POST['hiddenProductionSite'];
            if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
            {
                $companyCode = $gSession['userdata']['companycode'];
            }
            else
            {
                $companyCode = '';
                if ($owner != '')
                {
                    //get company code for production site
                    $siteDatails = DatabaseObj::getSiteFromCode($owner);
                    $companyCode = $siteDatails['companycode'];
                }
            }
        }
        else
        {
            $owner = '';
            $companyCode = '';
        }

        $promotionID = $_POST['hiddenPromoId'];
        $promotionCode = '';
        $name = $_POST['hiddenName'];
        $description = $_POST['hiddenDescription'];
        $type = $_POST['hiddenType'];
        $startDate = $_POST['hiddeStartDate'];
        $endDate = $_POST['hiddenEndDate'];
        $productCode = $_POST['hiddenProductCode'];
        $groupCode = $_POST['hiddenGroupCode'];
        $userID = $_POST['hiddenCustomer'];
        $minQty = $_POST['minqty'];
        $maxQty = $_POST['maxqty'];
        $lockQty = $_POST['hiddenLockOrderQty'];
        $repeatType = $_POST['hiddenRepeatType'];
        $discountSection = $_POST['hiddenDiscountSection'];
        $discountType = $_POST['hiddenDiscountType'];
        $discountValue = $_POST['hiddenDiscountValue'];
        $sellprice = $_POST['hiddenSellPrice'];
        $agentfee = $_POST['hiddenAgentFee'];
        $isActive = $_POST['hiddenActive'];
        $applicationMethod = $_POST['hiddenDiscountMethod'];
        $applyToQty = $_POST['hiddenDiscountApplyToQty'];
        $productGroupID = UtilsObj::getPOSTParam('hiddenProductGroupID', 0);
        $productGroupUsed = ($productGroupID > 0) ? 1 : 0;
        $minimumOrderValue = $_POST['hiddenminordervalue'];
        $minimumOrderValueIncludesShipping = $_POST['hiddenminordervalueincludesshipping'];
        $minimumOrderValueIncludesTax = $_POST['hiddenminordervalueincludestax'];

        $vouchersArray = Array();
        $voucherIDArray = Array();
        $alreadyExist = array();

        if ($name != '')
        {
            if ($promotionID > 0)
            {
                $promotionDataArray = DatabaseObj::getVoucherPromotionFromID($promotionID);
                $promotionCode = $promotionDataArray['code'];
            }

            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('INSERT INTO `VOUCHERS` (`id`, `datecreated`, `companycode` ,`owner`, `promotioncode`, `code`, `type`, `name`, `description`, `startdate`, `enddate`,
                    `productcode`, `groupcode`, `userid`, `minimumqty`, `maximumqty`, `lockqty`, `repeattype`, `discountsection`, `discounttype`, `discountvalue`, `applicationmethod`, `maxqtytoapplydiscountto`,
                    `sellprice`, `hasproductgroup`, `agentfee`, `minordervalue`, `minordervalueincshipping`, `minordervalueinctax`, `active`)
                    VALUES (0, now(), ?, ?, ?, ?, ?,  ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
                {
                    for($i = 0; $i < $voucherCodeCount; $i++)
                    {
                        UtilsObj::resetPHPScriptTimeout(30); // just in case

                        $voucherCodesItem = strtoupper($voucherCodes[$i]);
                        $voucherCodesItem = preg_replace("/([^A-Z0-9_-])/", '', $voucherCodesItem);

                        if ($voucherCodesItem != '')
                        {
                            if ($stmt->bind_param('ssssissssssiiiisssdiididdiii', $companyCode, $owner, $promotionCode, $voucherCodesItem, $type,
                                            $name, $description, $startDate, $endDate, $productCode,
                                            $groupCode, $userID, $minQty, $maxQty, $lockQty, $repeatType, $discountSection, $discountType,
                                            $discountValue, $applicationMethod, $applyToQty, $sellprice, $productGroupUsed, $agentfee, $minimumOrderValue, $minimumOrderValueIncludesShipping, $minimumOrderValueIncludesTax, $isActive))
                            {
                                if ($stmt->execute())
                                {
                                    $recordID = $dbObj->insert_id;

                                    DatabaseObj::updateActivityLog2($dbObj, $gSession['ref'], 0, $gSession['userid'],
                                            $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'VOUCHER-CREATE',
                                            $recordID . ' ' . $voucherCodesItem, 1);

                                    // retrieve the voucher and store it so that we can display the result
                                    if ($stmt2 = $dbObj->prepare('SELECT PRODUCTS.name, USERS.contactfirstname, USERS.contactlastname, USERS.emailaddress
										FROM `VOUCHERS`
										LEFT JOIN `PRODUCTS` ON PRODUCTS.code = VOUCHERS.productcode
										LEFT JOIN `USERS` ON USERS.id = VOUCHERS.userid
										WHERE VOUCHERS.id = ?'))
                                    {
                                        if ($stmt2->bind_param('i', $recordID))
                                        {
                                            if ($stmt2->bind_result($productName, $contactFirstName, $contactLastName, $emailAddress))
                                            {
                                                if ($stmt2->execute())
                                                {
                                                    while($stmt2->fetch())
                                                    {
                                                        $voucherItem['id'] = $recordID;
                                                        $voucherItem['promotioncode'] = $promotionCode;
                                                        $voucherItem['code'] = $voucherCodesItem;
                                                        $voucherItem['name'] = $name;
                                                        $voucherItem['description'] = $description;
                                                        $voucherItem['startdate'] = $startDate;
                                                        $voucherItem['enddate'] = $endDate;
                                                        $voucherItem['productcode'] = $productCode;
                                                        $voucherItem['productname'] = $productName;
                                                        $voucherItem['groupcode'] = $groupCode;
                                                        $voucherItem['userid'] = $userID;
                                                        $voucherItem['username'] = $contactFirstName . ' ' . $contactLastName . '<br>(' . $emailAddress . ')';
                                                        $voucherItem['repeattype'] = $repeatType;
                                                        $voucherItem['discountsection'] = $discountSection;
                                                        $voucherItem['discounttype'] = $discountType;
                                                        $voucherItem['discountvalue'] = $discountValue;
                                                        $voucherItem['isactive'] = $isActive;
                                                        $voucherItem['applicationmethod'] = $applicationMethod;
                                                        $voucherItem['applytoqty'] = $applyToQty;

                                                        array_push($vouchersArray, $voucherItem);
                                                        array_push($voucherIDArray, $recordID);
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $stmt2->free_result();
                                    $stmt2->close();
                                    $stmt2 = null;
                                }
                                else
                                {
                                    // could not execute statement
                                    // first check for a duplicate key (voucher code)
                                    if ($stmt->errno == 1062)
                                    {
                                        $result = 'str_ErrorVoucherExists';
                                        $alreadyExist[] = $voucherCodesItem;
                                    }
                                    else
                                    {
                                        $result = 'str_DatabaseError';
                                        $resultParam = 'voucherCreate execute ' . $dbObj->error;
                                        break;
                                    }
                                }
                            }
                            else
                            {
                                // could not bind parameters
                                $result = 'str_DatabaseError';
                                $resultParam = 'voucherCreate bind ' . $dbObj->error;
                            }
                        }
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'voucherCreate prepare ' . $dbObj->error;
                }
                $dbObj->close();

                if (($result === '') && ($productGroupUsed == 1))
                {
                    foreach ($vouchersArray as $theVoucher)
                    {
                        $linkRecordResultArray = DatabaseObj::insertProductGroupLinkRecord($productGroupID, $theVoucher['code'], TPX_PRODUCTGROUP_ASSIGNEETYPE_VOUCHER);
                        $result = $linkRecordResultArray['error'];
                        $resultParam = $linkRecordResultArray['errorparam'];

                        if ($result !== '')
                        {
                            // if something has gone wrong we want to bail out
                            // we don't need to cleanup as the generated vouchers will have the hasproductgroup flag set
                            break;
                        }
                    }
                }
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'voucherCreate connect ' . $dbObj->error;
            }
        }

        // store the vouchers which have just been created so that the user can save them to disk
        $gSession['vouchercreationresult'] = $voucherIDArray;
        DatabaseObj::updateSession();

        if ($result == '')
        {
            echo "{'success': true, 'msg':''}";
        }
        else
        {
            $smarty = SmartyObj::newSmarty('AdminVouchers');
            if (count($alreadyExist) > 0)
            {
                $resultParam = $smarty->get_config_vars('str_ErrorVouchersExist') . ' ' . join(', ', $alreadyExist);
            }
            else
            {
                $resultParam = str_replace('^0', $resultParam, $smarty->get_config_vars($result));
            }
            echo '{"success": false, "msg":"' . $resultParam . '"}';
        }

        return;
    }

    static function voucherExport($promotionId)
    {
        global $gSession;
        global $gConstants;

        $resultArray = Array();
        $voucherIDArray = array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        $promotionCode = '';
        $id = 0;
        $code = '';
        $type = '';
        $defaultdiscount = '';
        $name = '';
        $description = '';
        $startDate = '';
        $endDate = '';
        $productCode = '';
        $groupCode = '';
        $userID = '';
        $repeatType = '';
        $discountSection = '';
        $discountType = '';
        $discountValue = '';
        $sellprice = '';
        $agentfee = '';
        $isActive = '';
        $productName = '';
        $contactFirstName = '';
        $contactLastName = '';
        $emailAddress = '';
        $applicationMethod = 0;
        $applyToQty = 9999;
        $productGroupID = 0;
        $productGroupName = '';
        $minimumOrderValue = 0.00;
        $minimumOrderValueIncludesShipping = 0;
        $minimumOrderValueIncludesTax =0;

        if ((isset($_GET['useCached'])) && ($_GET['useCached'] == 1) && (isset($gSession['vouchercreationresult'])))
        {
            $voucherIDArray = $gSession['vouchercreationresult'];
        }
        else
        {
            if ($dbObj)
            {
                $promotionInfo = array();
                if ($promotionId > 0)
                {
                    $promotionInfo = DatabaseObj::getVoucherPromotionFromID($promotionId);
                    $promotionCode = $promotionInfo['code'];
                }

                if (($gConstants['optionms']) && ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN))
                {
                    $stmt = $dbObj->prepare('SELECT VOUCHERS.id FROM VOUCHERS WHERE (VOUCHERS.companycode = ? OR VOUCHERS.companycode = "") AND VOUCHERS.promotioncode = ? ORDER BY VOUCHERS.id');
                    $bindOK = $stmt->bind_param('ss', $gSession['userdata']['companycode'], $promotionCode);
                }
                else
                {
                    $stmt = $dbObj->prepare('SELECT id FROM VOUCHERS WHERE VOUCHERS.promotioncode = ? ORDER BY id');
                    $bindOK = $stmt->bind_param('s', $promotionCode);
                }

                if ($stmt->bind_result($id))
                {
                    if ($stmt->execute())
                    {
                        while($stmt->fetch())
                        {
                            $voucherIDArray[] = $id;
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
            }
        }

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT VOUCHERS.id, VOUCHERS.promotioncode, VOUCHERS.code, VOUCHERS.type, VOUCHERS.defaultdiscount, VOUCHERS.name, VOUCHERS.description, VOUCHERS.startdate,
                VOUCHERS.enddate,
                VOUCHERS.productcode, VOUCHERS.groupcode, VOUCHERS.userid, VOUCHERS.repeattype, VOUCHERS.discountsection, VOUCHERS.discounttype, VOUCHERS.discountvalue,
                VOUCHERS.sellprice, VOUCHERS.agentfee,
                VOUCHERS.minordervalue, VOUCHERS.minordervalueincshipping, VOUCHERS.minordervalueinctax, VOUCHERS.active, PRODUCTS.name, USERS.contactfirstname, USERS.contactlastname, USERS.emailaddress, VOUCHERS.applicationmethod, VOUCHERS.maxqtytoapplydiscountto,
                COALESCE(`pgh`.`id`, 0), COALESCE(`pgh`.`name`, "")
                FROM `VOUCHERS`
                LEFT JOIN `PRODUCTS` ON PRODUCTS.code = VOUCHERS.productcode
                LEFT JOIN `USERS` ON (USERS.id = VOUCHERS.userid OR USERS.id = VOUCHERS.redeemeduserid)
                LEFT JOIN `productgrouplink` AS `pgl` ON (`VOUCHERS`.`hasproductgroup` = 1 AND `VOUCHERS`.`code` = `pgl`.`assigneecode`)
                LEFT JOIN `productgroupheader` AS `pgh` ON (`pgl`.`productgroupid` = `pgh`.`id`)
                WHERE VOUCHERS.id = ? AND VOUCHERS.type < ' . TPX_VOUCHER_TYPE_GIFTCARD))
            {
                $itemCount = count($voucherIDArray);
                for ($i = 0; $i < $itemCount; $i++)
                {
                    if ($stmt->bind_param('i', $voucherIDArray[$i]))
                    {
                        if ($stmt->bind_result($id, $promotionCode, $code, $type, $defaultdiscount, $name, $description, $startDate, $endDate, $productCode,
                                                $groupCode, $userID, $repeatType, $discountSection, $discountType, $discountValue, $sellprice, $agentfee,
                                                $minimumOrderValue, $minimumOrderValueIncludesShipping, $minimumOrderValueIncludesTax, $isActive,
                                                $productName, $contactFirstName, $contactLastName, $emailAddress, $applicationMethod, $applyToQty, $productGroupID, $productGroupName))
                        {
                            if ($stmt->execute())
                            {
                                if ($stmt->fetch())
                                {
                                    $voucherItem['id'] = $id;
                                    $voucherItem['promotioncode'] = $promotionCode;
                                    $voucherItem['code'] = $code;
                                    $voucherItem['type'] = $type;
                                    if ($defaultdiscount==1)
                                    {
                                    	$voucherItem['defaultdiscount'] = 'str_LabelYes';
                                    }
                                    else
                                    {
                                    	$voucherItem['defaultdiscount'] = 'str_LabelNo';
                                    }
                                    $voucherItem['name'] = $name;
                                    $voucherItem['description'] = UtilsObj::stripControlCharacters($description);
                                    $voucherItem['startdate'] = $startDate;
                                    $voucherItem['enddate'] = $endDate;
                                    $voucherItem['productcode'] = $productCode;
                                    $voucherItem['productname'] = $productName;
                                    $voucherItem['groupcode'] = $groupCode;
                                    $voucherItem['userid'] = $userID;
                                    $voucherItem['usercontactfirstname'] = $contactFirstName;
                                    $voucherItem['usercontactlastname'] = $contactLastName;
                                    $voucherItem['useremailaddress'] = $emailAddress;
                                    $voucherItem['repeattype'] = $repeatType;
                                    $voucherItem['discountsection'] = $discountSection;
                                    $voucherItem['discounttype'] = $discountType;
                                    $voucherItem['discountvalue'] = $discountValue;
                                    $voucherItem['sellprice'] = $sellprice;
                                    $voucherItem['agentfee'] = $agentfee;
                                    $voucherItem['licenseevalue'] = $sellprice-$agentfee;
                                    $voucherItem['isactive'] = $isActive;
                                    $voucherItem['applicationmethod'] = $applicationMethod;
                                    $voucherItem['applytoqty'] = $applyToQty;
                                    $voucherItem['productgroupid'] = $productGroupID;
                                    $voucherItem['productgroupname'] = $productGroupName;
                                    $voucherItem['minimumordervalue'] = $minimumOrderValue;
                                    $voucherItem['minordervalueincludesshipping'] = $minimumOrderValueIncludesShipping;
                                    $voucherItem['minordervalueincludestax'] = $minimumOrderValueIncludesTax;

                                    array_push($resultArray, $voucherItem);
                                }
                            }
                        }
                    }
                    $stmt->free_result();
                }
                $stmt->close();
            }
            $dbObj->close();
        }

        return $resultArray;
    }

    static function clearSavedVoucherList()
    {
        global $gSession;

        $gSession['vouchercreationresult'] = null;
        DatabaseObj::updateSession();
    }

    static function voucherDeleteNew()
    {
        global $gSession;

        $voucherArray = Array();
        $itemCount = 0;

        $voucherIDArray = $gSession['vouchercreationresult'];
        $voucherCount = count($voucherIDArray);
        $result = '';
        $resultParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `code` FROM VOUCHERS WHERE `id` = ?'))
            {
                for($i = 0; $i < $voucherCount; $i++)
                {
                    $voucherID = $voucherIDArray[$i];
                    if ($voucherID != '')
                    {
                        if ($stmt->bind_param('i', $voucherID))
                        {
                            if ($stmt->bind_result($voucherCode))
                            {
                                if ($stmt->execute())
                                {
                                    while($stmt->fetch())
                                    {
                                        $voucherItem['id'] = $voucherID;
                                        $voucherItem['code'] = $voucherCode;
                                        $voucherArray[] = $voucherItem;
                                    }
                                }
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'voucherDeleteNew bind ' . $dbObj->error;
                        }
                        $stmt->free_result();
                    }
                }

				$counter = 0;
				$voucherIDString = '';
				$processedVoucherArray = array();
				$itemCount = count($voucherArray);
				$lastIndex = $itemCount - 1;

                for($i = 0; $i < $itemCount; $i++)
                {
                    $processedVoucherArray[] = array('id' => $voucherArray[$i]['id'], 'code' => $voucherArray[$i]['code']);
					$voucherIDString .= $voucherArray[$i]['id'] . ',';
					$counter++;

					if (($counter == 500) || ($i == $lastIndex))
					{
						UtilsObj::resetPHPScriptTimeout(60);

						$voucherIDString = substr($voucherIDString, 0, -1);

						$dbObj->query('START TRANSACTION');

						AdminVouchers_model::voucherDelete2($dbObj, $voucherIDString, $processedVoucherArray);

						$dbObj->query('COMMIT');

						$processedVoucherArray = array();
						$counter = 0;
						$voucherIDString = '';
					}
                }

                $stmt->close();
            }
            else
            {
                $result = 'str_DatabaseError';
                $resultParam = 'voucherDeleteNew prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }
        else
        {
            $result = 'str_DatabaseError';
            $resultParam = 'voucherDeleteNew connect ' . $dbObj->error;
        }

        self::clearSavedVoucherList();

        if ($result == '')
        {
            echo "{'success':'true', 'msg':''}";
        }
        else
        {
            echo '{"success":false,	"msg":"' . $resultParam . '"}';
        }

        return;
    }
}
?>
