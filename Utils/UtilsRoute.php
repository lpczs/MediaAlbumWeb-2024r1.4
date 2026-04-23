<?php
//Check to see if the external order routing script exists.
if (file_exists("../Customise/scripts/EDL_OrderRouting.php"))
{
    require_once('../Customise/scripts/EDL_OrderRouting.php');
}

class RoutingObj
{

    static function RouteOrder($pWebBrandCode, $pLicenseKeyCode, $pUserID, $pProductCode, $pCoverCode, $pPaperCode, $pCountryCode,
            $pAddress1, $pAddress2, $pAddress3, $pAddress4, $pCity, $pRegionCode, $pPostCode, $pVoucherCode)
    {
        global $gConstants;

        $resultArray = Array('routesitecode' => '', 'productionsitekey' => '', 'productionsitetype' => 0);
        $canProduceProduct = false;

        $voucherOwner = '';
        $voucherProdKey = '';
        $voucherProdType = '';
        $scriptProductionSiteKey = '';
        $scriptProductionSiteType = '';
        $id = -1;
        $rule = '';
        $condition = '';
        $value = '';
        $siteCode = '';
        $productionSiteKey = '';
        $productionSiteType = '';

        if ($gConstants['optionms'])
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            $match = false;

            if ($dbObj)
            {
                // First check to see if a vocuher has been used.
                if ($pVoucherCode != '')
                {
                    $stmt = $dbObj->prepare('SELECT VOUCHERS.owner, IF(ISNULL(productionsitekey) , "", productionsitekey) AS productionsitekey,
                                                    IF(ISNULL(productionsitetype) , 0, productionsitetype ) AS productionsitetype
                                                FROM `VOUCHERS`
                                                    LEFT JOIN `SITES` ON VOUCHERS.owner = SITES.code
                                                WHERE (VOUCHERS.code = ?)
                                                     AND ((VOUCHERS.owner = "")
                                                        OR ((VOUCHERS.owner <> "")
                                                            AND (SITES.active = 1)
                                                            AND (SITES.productionsitekey <> "")
                                                        )
                                                    )');
                    if ($stmt)
                    {
                        if ($stmt->bind_param('s', $pVoucherCode))
                        {
                            if ($stmt->execute())
                            {
                                if ($stmt->store_result())
                                {
                                    if ($stmt->num_rows > 0)
                                    {
                                        if ($stmt->bind_result($voucherOwner, $voucherProdKey, $voucherProdType))
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
                }

                // get data from orderRouting Table
                $stmt = $dbObj->prepare('SELECT ORDERROUTING.id, ORDERROUTING.rule, ORDERROUTING.condition, ORDERROUTING.value,
                                            ORDERROUTING.sitecode, IF(ISNULL(productionsitekey) , "", productionsitekey) AS productionsitekey,
                                            IF(ISNULL(productionsitetype) , 0, productionsitetype ) AS productionsitetype
                                        FROM `ORDERROUTING`
                                        LEFT JOIN `SITES` ON ORDERROUTING.sitecode = SITES.code
                                        WHERE (ORDERROUTING.sitecode = "")
                                            OR ((ORDERROUTING.sitecode <> "") AND (SITES.active = 1) AND (SITES.productionsitekey <> ""))
                                        ORDER BY ORDERROUTING.priority DESC');
                if ($stmt)
                {
                    $stmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);

                    if ($stmt->bind_result($id, $rule, $condition, $value, $siteCode, $productionSiteKey, $productionSiteType))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
                                $match = false;

                                switch ($rule)
                                {
                                    // Route By Brand
                                    case TPX_ROUTE_BY_BRAND_CODE:
                                        $code = $pWebBrandCode;
                                        if (self::orderRuleApplies($code, $value, $condition))
                                        {
                                            $match = true;
                                        }
                                        break;
                                    // Route By License Key Code
                                    case TPX_ROUTE_BY_LICENCE_KEY_CODE:
                                        $code = $pLicenseKeyCode;
                                        if (self::orderRuleApplies($code, $value, $condition))
                                        {
                                            $match = true;
                                        }
                                        break;
                                    // Route By Product Code
                                    case TPX_ROUTE_BY_PRODUCT_CODE:
                                        $code = $pProductCode;
                                        if (self::orderRuleApplies($code, $value, $condition))
                                        {
                                            $match = true;
                                        }
                                        break;
                                    // Route BY Shipping Country Code
                                    case TPX_ROUTE_BY_SHIPPING_COUNTRY_CODE:
                                        $code = $pCountryCode;
                                        if (self::orderRuleApplies($code, $value, $condition))
                                        {
                                            $match = true;
                                        }
                                        break;
                                    // Route By Voucher Owner Code
                                    case TPX_ROUTE_TO_VOUCHER_OWNER_CODE:
                                        if ($pVoucherCode != '')
                                        {
                                            $siteCode = $voucherOwner;
                                            $productionSiteKey = $voucherProdKey;
                                            $productionSiteType = $voucherProdType;
                                            $match = true;
                                        }
                                        break;
                                }

                                // If there is a match see what products can be produced. If it can be produced break out the while loop
                                if ($match)
                                {
                                    if ($siteCode != '')
                                    {
                                        $resultArray = self::productAcceptedBySite($siteCode, $pProductCode);

                                        if ($resultArray['match'] != '')
                                        {
                                            $canProduceProduct = true;
                                        }
                                    }
                                    else // global site accepts all products
                                    {
                                        $canProduceProduct = true;
                                    }

                                    if ($canProduceProduct)
                                    {
                                        break;
                                    }
                                }
                            }

                            $stmt->free_result();
                            $stmt->close();
                            $stmt = null;

                            // Check to see if there is a custom order routing script.
                            // Try and find a better siteCode than the on we aleady have.
                            if (method_exists('EDLRouteObj', 'EDLRouteOrder'))
                            {
                                $scriptSite = EDLRouteObj::EDLRouteOrder($resultArray['routesitecode'], $pWebBrandCode, $pLicenseKeyCode,
                                                $pUserID, $pProductCode, $pCoverCode, $pPaperCode, $pCountryCode, $pAddress1, $pAddress2,
                                                $pAddress3, $pAddress4, $pCity, $pRegionCode, $pPostCode, $pVoucherCode);

                                // Check to see if site code has changed since external data link
                                if ($resultArray['routesitecode'] != $scriptSite)
                                {
                                    // Get new production site key as site code has changed.
                                    $stmt3 = $dbObj->prepare('SELECT `productionsitekey`, `productionsitetype`
                                                                FROM `SITES`
                                                                WHERE `code`= ?
                                                                AND (`active` = 1)
                                                                AND (productionsitekey <> "")');
                                    if ($stmt3)
                                    {
                                        if ($stmt3->bind_param('s', $scriptSite))
                                        {
                                            if ($stmt3->execute())
                                            {
                                                if ($stmt3->store_result())
                                                {
                                                    if ($stmt3->num_rows > 0)
                                                    {
                                                        if ($stmt3->bind_result($scriptProductionSiteKey, $scriptProductionSiteType))
                                                        {
                                                            if ($stmt3->fetch())
                                                            {
                                                                $resultArray['routesitecode'] = $scriptSite;
                                                                $resultArray['productionsitekey'] = $scriptProductionSiteKey;
                                                                $resultArray['productionsitetype'] = $scriptProductionSiteType;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $stmt3->free_result();
                                        $stmt3->close();
                                        $stmt3 = null;
                                    }
                                }
                            }
                        }
                    }
                }
                $dbObj->close();
            }
        }

        return $resultArray;
    }

    static function productAcceptedBySite($pSiteCode, $pProductCode)
	{
		$dbObj = DatabaseObj::getGlobalDBConnection();
		$resultArray = Array('match' => '', 'routesitecode' => '', 'productionsitekey' => '',  'productionsitetype' => '');

		if ($dbObj)
		{
			// Get store data from SITES table.
			if ($stmt = $dbObj->prepare('SELECT `acceptallproducts`, `productionsitekey`, `productionsitetype` FROM `SITES`
										WHERE (`code`= ?) AND (`active` = 1) AND (`productionsitekey` <> "")'))
			{
			   if ($stmt->bind_param('s', $pSiteCode))
			   {
				   if ($stmt->bind_result($acceptAllProducts, $productionSiteKey, $productionSiteType))
				   {
						if ($stmt->execute())
						{
							if ($stmt->fetch())
							{
								$stmt->free_result();
								// check that it can produce all products. If it can use that storecode
								if ($acceptAllProducts == 1)
								{
									$resultArray['match'] = '**MATCH**';
									$resultArray['routesitecode'] = $pSiteCode;
									$resultArray['productionsitekey'] = $productionSiteKey;
									$resultArray['productionsitetype'] = $productionSiteType;
								}
								// if the store is a production site but does not accept all products look the product up in the
								// SITEPRODUCTS table to see if that site can actually produce this product.
								else
								{
									if ($stmt2 = $dbObj->prepare('SELECT id FROM `SITEPRODUCTS`
																WHERE (`ownercode` = ?) AND (`productcode` = ?)'))
									{
										if ($stmt2->bind_param('ss', $pSiteCode, $pProductCode))
										{
											if ($stmt2->bind_result($id))
											{
												if ($stmt2->execute())
												{
													if ($stmt2->fetch())
													{
														$resultArray['match'] = '**MATCH**';
														$resultArray['routesitecode'] = $pSiteCode;
														$resultArray['productionsitekey'] = $productionSiteKey;
														$resultArray['productionsitetype'] = $productionSiteType;
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
		}

		$dbObj->close();
		return $resultArray;
	}

    static function routingAdd($pRule, $pCondition, $pConditionValue, $pSite)
    {
        global $gSession;

        $result = '';
        $resultParam = '';
        $recordID = '';
        $priority = 0;
        $count = 0;
        $routingRuleAdded = false;
        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $getRoutingRule = self::getRoutingRuleFromCondition($pRule, $pCondition, $pConditionValue, $pSite);
            $result = $getRoutingRule['result'];

            if ($result == '')
            {
                $dbObj->query('LOCK TABLES `ORDERROUTING` WRITE');

                $stmt = $dbObj->prepare('SELECT MAX(`priority`) FROM `ORDERROUTING`');
                if ($stmt)
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($count))
                                {
                                    if ($stmt->fetch())
                                    {
                                        if ($count == NULL)
                                        {
                                            $priority = 1;
                                        }
                                        else
                                        {
                                            $priority = $count + 1;
                                        }
                                    }
                                }
                            }
                            else
                            {
                               $priority = 1;
                            }
                        }
                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                }

                if ($stmt = $dbObj->prepare('INSERT INTO `ORDERROUTING` VALUES (0, now(), ?, ?, ?, ?, ?)'))
                {
                    if ($stmt->bind_param('iissi', $pRule, $pCondition, $pConditionValue, $pSite, $priority))
                    {
                        if ($stmt->execute())
                        {
                            $recordID = $dbObj->insert_id;
                            $routingRuleAdded = true;
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'shippingMethodAdd execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'shippingMethodAdd bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'shippingMethodAdd prepare ' . $dbObj->error;
                }

                $stmt = $dbObj->query('UNLOCK TABLES');

                if ($routingRuleAdded) ;
                {
                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'],
                            0, 'ADMIN', 'ROUTINGRULE-ADD', $recordID . ' ' . $recordID, 1);
                }

                $dbObj->close();
            }
        }
        else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'shippingMethodAdd connect ' . $dbObj->error;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $recordID;
        $resultArray['rule'] = $pRule;
        $resultArray['condition'] = $pCondition;
        $resultArray['conditionValue'] = $pConditionValue;
        $resultArray['site'] = $pSite;

        return $resultArray;
    }

    static function routingEdit($pRule, $pCondition, $pConditionValue, $pSite, $pRoutingRuleID)
    {
        global $gSession;

        $result = '';
        $resultParam = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $dbObj->query('LOCK TABLES `ORDERROUTING` WRITE, `ACTIVITYLOG` WRITE');

            if ($stmt = $dbObj->prepare('UPDATE `ORDERROUTING` SET `rule` = ?, `condition` = ?, `value` = ?, `sitecode` = ? WHERE `id` = ?'))
            {
                if ($stmt->bind_param('iissi', $pRule, $pCondition, $pConditionValue, $pSite, $pRoutingRuleID))
                {
                    if ($stmt->execute())
                    {
                        DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                $gSession['username'], 0, 'ADMIN', 'ROUTINGRULE-UPDATE', $pRoutingRuleID . ' ' . $pRoutingRuleID, 1);
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'shippingMethodEdit execute ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not bind parameters
                    $result = 'str_DatabaseError';
                    $resultParam = 'shippingMethodEdit bind ' . $dbObj->error;
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                // could not prepare statement
                $result = 'str_DatabaseError';
                $resultParam = 'shippingMethodEdit prepare ' . $dbObj->error;
            }

            $dbObj->query('UNLOCK TABLES');

            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'shippingMethodEdit connect ' . $dbObj->error;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $pRoutingRuleID;
        $resultArray['rule'] = $pRule;
        $resultArray['condition'] = $pCondition;
        $resultArray['conditionValue'] = $pConditionValue;
        $resultArray['site'] = $pSite;

        return $resultArray;
    }

    static function routingRuleDelete($pRuleList)
    {
        global $gSession;

        $resutArray = Array();
        $updateIdArray = Array();
        $allDeleted = 1;
        $siteCount = count($pRuleList);
        $sitesDeleted = Array();
        $itemsDeleted = false;

        if ($siteCount > 0)
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                // lock tables as when a routing rule is deleted routing priorities are updated.
                // locking tables prevents routing priorities from being changed by another user during this process.
                $dbObj->query('LOCK TABLES `ORDERROUTING` WRITE');

                if ($stmt = $dbObj->prepare('DELETE FROM `ORDERROUTING` WHERE `id` = ?'))
                {
                    foreach ($pRuleList as $ruleId)
                    {
                        if ($stmt->bind_param('i', $ruleId))
                        {
                            if ($stmt->execute())
                            {
                                $itemsDeleted = true;
                                array_push($sitesDeleted, $ruleId);
                            }
                        }
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stsmt = null;
                }

                // get list of routing rules so we can update priorties after a delete.
                if ($stmt = $dbObj->prepare('SELECT `id`, `priority` FROM `ORDERROUTING` ORDER BY `priority` DESC'))
                {
                    if ($stmt->bind_result($id, $priority))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
                                $updateIdArray[] = $id;
                            }
                        }
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }

                // get count of routing rules after delete so we can update priority
                $itemCount = count($updateIdArray);

                if ($stmt = $dbObj->prepare('UPDATE `ORDERROUTING` SET `priority` = ?  WHERE `id` = ?'))
                {
                    for ($i = 0; $i < $itemCount; $i++)
                    {
                        $priority = $itemCount - $i;

                        if ($stmt->bind_param('ii', $priority, $updateIdArray[$i]))
                        {
                            $stmt->execute();
                        }
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }

                // unlock tables after routing priorities have been updated.
                $dbObj->query('UNLOCK TABLES');

                if ($itemsDeleted)
                {
                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'],
                            0, 'ADMIN', 'ROUTINGRULE-DELETE', $ruleId . ' ' . $ruleId, 1);
                }

                $dbObj->close();
            }
        }
        $resultArray['alldeleted'] = $allDeleted;
        $resultArray['sitesids'] = $sitesDeleted;

        return $resultArray;
    }

    static function orderRuleApplies($pCode, $pValue, $pCondition)
    {
        switch ($pCondition)
        {
            case TPX_TEST_FOR_EQUALITY:
                return ($pCode == $pValue);
                break;
            case TPX_TEST_FOR_DIFFERENCE:
                return ($pCode != $pValue);
                break;
            default:
                return false;
        }
    }

    static function getRoutingConstants()
    {
        //get a list of routing constants and put them into an array
        $smarty = SmartyObj::newSmarty('AdminSitesOrderRouting');

        $routingConstants = array
            (
            array('id' => TPX_ROUTE_BY_BRAND_CODE, 'name' => $smarty->get_config_vars('str_LabelBrandCode')),
            array('id' => TPX_ROUTE_BY_LICENCE_KEY_CODE, 'name' => $smarty->get_config_vars('str_LabelLicenseKeyCode')),
            array('id' => TPX_ROUTE_BY_PRODUCT_CODE, 'name' => $smarty->get_config_vars('str_LabelProductCode')),
            array('id' => TPX_ROUTE_BY_SHIPPING_COUNTRY_CODE, 'name' => $smarty->get_config_vars('str_LabelShippingCountryCode')),
            array('id' => TPX_ROUTE_TO_VOUCHER_OWNER_CODE, 'name' => $smarty->get_config_vars('str_LabelVoucherSiteCode'))
        );

        $resultArray = $routingConstants;

        return $resultArray;
    }

    static function getRoutingConditions()
    {
        //get a list of routing conditions and put them into an array
        $smarty = SmartyObj::newSmarty('AdminSitesOrderRouting');

        $routingConditions = array
            (
            array('id' => TPX_TEST_FOR_EQUALITY, 'name' => $smarty->get_config_vars('str_LabelIs')),
            array('id' => TPX_TEST_FOR_DIFFERENCE, 'name' => $smarty->get_config_vars('str_LabelIsNot'))
        );
        $resultArray = $routingConditions;

        return $resultArray;
    }

    static function getLicenseKeyCodes()
    {
        $licenseKeyCodeList = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `id`, `groupcode`, `name` FROM `LICENSEKEYS` ORDER BY `groupcode`'))
            {
                if ($stmt->bind_result($id, $groupCode, $name))
                {
                    if ($stmt->execute())
                    {
                        while ($stmt->fetch())
                        {
                            $codeItem['id'] = $id;
                            $codeItem['code'] = $groupCode;
                            $codeItem['name'] = $name;
                            array_push($licenseKeyCodeList, $codeItem);
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }
        $resultArray = $licenseKeyCodeList;

        return $resultArray;
    }

    static function getProductionSiteNames()
    {
        global $gSession;
        // return an array containing the a list of production sites
        $siteNamesList = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            switch ($gSession['userdata']['usertype'])
            {
                case TPX_LOGIN_SYSTEM_ADMIN:
                    $stmt = $dbObj->prepare('SELECT `id`, `code`, `name`, `companycode` FROM `SITES` WHERE (`productionsitekey` != "") ORDER BY `code`');
                    $bindOK = true;
                    break;
                case TPX_LOGIN_COMPANY_ADMIN:
                    $stmt = $dbObj->prepare('SELECT `id`, `code`, `name`, `companycode` FROM `SITES` WHERE (`productionsitekey` != "") AND (`companycode` = ?) ORDER BY `code`');
                    $bindOK = $stmt->bind_param('s', $gSession['userdata']['companycode']);
                    break;
            }

            if ($stmt)
            {
                if ($bindOK)
                {
                    if ($stmt->bind_result($id, $code, $siteName, $companyCode))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
                                $siteItem['id'] = $id;
                                $siteItem['name'] = $code . ' - ' . $siteName;
                                $siteItem['code'] = $code;
                                $siteItem['companyCode'] = $companyCode;
                                array_push($siteNamesList, $siteItem);
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

        $resultArray = $siteNamesList;

        return $resultArray;
    }

    static function routingRuleAdd($pRule, $pCondition, $pConditionValue, $pSite)
    {
        $resultArray = self::routingAdd($pRule, $pCondition, $pConditionValue, $pSite);

        return $resultArray;
    }

    static function routingRuleEdit($pRule, $pCondition, $pConditionValue, $pSite, $pRoutingRuleID)
    {
        $resultArray = self::routingEdit($pRule, $pCondition, $pConditionValue, $pSite, $pRoutingRuleID);

        return $resultArray;
    }

    static function routingRuleDeleteFromGrid($pRuleList)
    {
        $resultArray = self::routingRuleDelete($pRuleList);

        return $resultArray;
    }

    static function getRoutingRuleFromCondition($pRule, $pCondition, $pConditionValue, $pSite)
    {
        $resultArray = Array();
        $result = '';
        $id = '';
        $rule = '';
        $condition = '';
        $conditionValue = '';
        $siteCode = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $stmt = $dbObj->prepare('SELECT `id`, `rule`, `condition`, `value`, `sitecode`
                                        FROM `ORDERROUTING`
                                        WHERE `rule` = ?
                                            AND `condition` = ?
                                            AND `value` = ?
                                            AND `sitecode` = ?');
            if ($stmt)
            {
                if ($stmt->bind_param('iiss', $pRule, $pCondition, $pConditionValue, $pSite))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($id, $rule, $condition, $conditionValue, $siteCode))
                                {
                                    if ($stmt->fetch())
                                    {
                                        $result = 'str_WarningRuleExists';
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

        $resultArray['result'] = $result;
        $resultArray['id'] = $id;
        $resultArray['rule'] = $rule;
        $resultArray['condition'] = $condition;
        $resultArray['conditionvalue'] = $conditionValue;
        $resultArray['sitecode'] = $siteCode;

        return $resultArray;
    }

}
?>
