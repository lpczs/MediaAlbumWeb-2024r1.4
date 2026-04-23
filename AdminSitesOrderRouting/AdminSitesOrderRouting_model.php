<?php

require_once('../Utils/UtilsAddress.php');

class AdminSitesOrderRouting_model
{
    static function getGridData()
    {
        global $gSession;
        $resultArray = Array();

		$dbObj = DatabaseObj::getGlobalDBConnection();

		$statement = 'SELECT orderrouting.id, orderrouting.rule, orderrouting.condition, orderrouting.value, orderrouting.sitecode, orderrouting.priority, sites.name,'.'
					CASE rule WHEN '.TPX_ROUTE_BY_BRAND_CODE.' THEN (SELECT branding.applicationname FROM BRANDING WHERE branding.code = orderrouting.value)'.'
							  WHEN '.TPX_ROUTE_BY_LICENCE_KEY_CODE.' THEN (SELECT licensekeys.name FROM LICENSEKEYS where licensekeys.groupcode = orderrouting.value)'.'
							  WHEN '.TPX_ROUTE_BY_PRODUCT_CODE.' THEN (SELECT products.name FROM PRODUCTS where products.code = orderrouting.value)'.'
							  END AS routingname FROM `ORDERROUTING` LEFT JOIN SITES ON orderrouting.sitecode = sites.code ORDER BY priority DESC';

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare($statement))
			{
				if ($stmt->bind_result($id, $rule, $condition, $value, $sitecode, $priority, $sitename, $routingName))
				{
					if ($stmt->execute())
					{
						while ($stmt->fetch())
						{
							if ($rule == TPX_ROUTE_BY_SHIPPING_COUNTRY_CODE)
							{
								$value = UtilsAddressObj::getCountryNameFromCode($value);
							}

							$item['recordid'] = $id;
							$item['rule'] = $rule;
							$item['condition'] = $condition;
							$item['value'] = $value;
							$item['sitecode'] = $sitecode;
							$item['sitename'] = $sitename;
							$item['priority'] = $priority;
							$item['routingname'] = $routingName;
							array_push($resultArray, $item);
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
	    $resultArray = Array();

        $routingRuleID = 0;
        $rule = TPX_ROUTE_BY_BRAND_CODE;
        $condition = TPX_TEST_FOR_EQUALITY;
        $value = '';
        $siteCode = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
	    {
	        if ($stmt = $dbObj->prepare('SELECT `id`, `rule`, `condition`, `value`, `sitecode` FROM `ORDERROUTING` WHERE `id` = ?'))
	        {
	            if ($stmt->bind_param('i', $pID))
	            {
		       		if ($stmt->execute())
                    {
            			if ($stmt->store_result())
						{
            				if ($stmt->num_rows > 0)
							{
				                if ($stmt->bind_result($routingRuleID, $rule, $condition, $value, $siteCode))
				                {
		                            if (!$stmt->fetch())
		                            {
		                            	$error = 'displayEdit fetch ' . $dbObj->error;
		                            }
			                    }
			                    else
			                    {
			                    	$error = 'displayEdit bind result ' . $dbObj->error;
			                    }
			                }
	                   	}
	                   	else
	                   	{
	                   		$error = 'displayEdit store result ' . $dbObj->error;
	                   	}
                    }
                    else
                    {
                    	$error = 'displayEdit execute ' . $dbObj->error;
                    }
                }
                else
                {
					$error = 'displayEdit bind params ' . $dbObj->error;
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
            	$error = 'displayEdit prepare ' . $dbObj->error;
            }
            $dbObj->close();
        }

        $resultArray['id'] = $routingRuleID;
        $resultArray['rule'] = $rule;
        $resultArray['condition'] = $condition;
        $resultArray['value'] = $value;
        $resultArray['sitecode'] = $siteCode;

        return $resultArray;
    }

    static function getConditionValueStore()
    {
  	    global $gSession;

  	    $resultArray = Array();
    	$conditionId = $_POST['conditionId'];
        $itemList = [];

		if ($conditionId != TPX_ROUTE_TO_VOUCHER_OWNER_CODE)
		{
			switch ($conditionId) {
				case TPX_ROUTE_BY_BRAND_CODE:
					// get list of brands
					$itemList  = DatabaseObj::getBrandingList();
					break;
				case TPX_ROUTE_BY_LICENCE_KEY_CODE:
					// get list of licensekeys
					$itemList  = RoutingObj::getLicenseKeyCodes();
					break;
				case TPX_ROUTE_BY_PRODUCT_CODE:
					// get list of products
					$itemList  = DatabaseObj::getProductNamesList('',true);
					break;
				case TPX_ROUTE_BY_SHIPPING_COUNTRY_CODE:
					//get list of countries
					$itemList  = UtilsAddressObj::getCountryList();
					break;
			}

			$itemCount = count($itemList);

			for ($i = 0; $i < $itemCount; $i++)
			{
				if ($conditionId != TPX_ROUTE_BY_SHIPPING_COUNTRY_CODE)
				{
					if ($conditionId == TPX_ROUTE_BY_BRAND_CODE)
					{
						$value = $itemList[$i]['applicationname'];
					}
					else if ($conditionId != TPX_ROUTE_BY_PRODUCT_CODE)
					{
						$value = $itemList[$i]['code'].' - '.$itemList[$i]['name'];
					}
					else
					{
						$value = $itemList[$i]['code'].' - '.LocalizationObj::getLocaleString($itemList[$i]['name'], $gSession['browserlanguagecode'], true);
					}

					$item['id'] = $itemList[$i]['code'];
				}
				else
				{
					$value = $itemList[$i]['name'];
					$item['id'] = $itemList[$i]['isocode2'];
				}

				$item['name'] = UtilsObj::ExtJSEscape($value);
				array_push($resultArray, $item);
			}
		}

    	return $resultArray;
    }

    static function routingRuleAdd()
    {
		$rule = $_POST['rule'];
    	$conditionValue = $_POST['conditionvalue'];
        $condition = $_POST['condition'];
        $site = $_POST['site'];

        $resultArray = RoutingObj::routingRuleAdd($rule,$condition,$conditionValue,$site);

        return $resultArray;
    }

    static function routingRuleEdit()
    {
    	$routingRuleID = $_GET['id'];
		$rule = $_POST['rule'];
    	$conditionValue = $_POST['conditionvalue'];
        $condition = $_POST['condition'];
        $site = $_POST['site'];

        $resultArray = RoutingObj::routingRuleEdit($rule, $condition, $conditionValue, $site, $routingRuleID);

    	return $resultArray;
    }

    static function routingRuleDelete()
    {
    	$ruleList = explode(',',$_POST['idlist']);

    	$resultArray = RoutingObj::routingRuleDeleteFromGrid($ruleList);

    	return $resultArray;
    }

    static function routingRulesTogglePriority()
    {
    	global $gSession;

    	$resultArray = Array();
    	$result = '';
        $resultParam = '';

    	$dataStoreIdList = $_POST['storeIdList'];
    	$moveIndex = $_POST['toggleId'];
    	$direction = $_POST['direction'];
    	$dataStoreIdArray = explode(',', $dataStoreIdList);
    	$itemCount = count($dataStoreIdArray);

   		switch ($direction) {
			case 'down':
				$temp = $dataStoreIdArray[$moveIndex];
				$dataStoreIdArray[$moveIndex] = $dataStoreIdArray[$moveIndex+1];
				$dataStoreIdArray[$moveIndex+1] = $temp;
				break;
			case 'up':
				$temp = $dataStoreIdArray[$moveIndex];
				$dataStoreIdArray[$moveIndex] = $dataStoreIdArray[$moveIndex-1];
				$dataStoreIdArray[$moveIndex-1] = $temp;
				break;
			case 'last':
				$temp = $dataStoreIdArray[$moveIndex];
				for($i = 0; $i < $itemCount - 1; $i++)
				{
					$dataStoreIdArray[$i] = $dataStoreIdArray[$i + 1];
				}
				$dataStoreIdArray[$itemCount-1] = $temp;
				break;
			case 'first':
				$temp = $dataStoreIdArray[$moveIndex];
				for($i = $itemCount - 1; $i > 0; $i--)
				{
					$dataStoreIdArray[$i] = $dataStoreIdArray[$i - 1];
				}
				$dataStoreIdArray[0] = $temp;
				break;
		}

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
            $dbObj->query('LOCK TABLES `ORDERROUTING` WRITE, `ACTIVITYLOG` WRITE');

	    	if ($stmt = $dbObj->prepare('UPDATE `ORDERROUTING` SET `priority` = ?  WHERE `id` = ?'))
			{
				for ($i=0; $i < $itemCount; $i++)
		   		{
					$priority = $itemCount - $i;

					if ($stmt->bind_param('ii',$priority,$dataStoreIdArray[$i]))
					{
						 $stmt->execute();
					}
					else
                	{
                    	// could not bind parameters
                   		$result = 'str_DatabaseError';
                    	$resultParam = 'ChangePriority bind ' . $dbObj->error;
               		}
		   		}
		   		DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
								'ADMIN', 'ROUTINGPRIORITY-UPDATE', $moveIndex . ' ' . $direction, 1);
				$stmt->free_result();
                $stmt->close();
                $stmt = null;
	         }
	         else
             {
                // could not prepare statement
                $result = 'str_DatabaseError';
                $resultParam = 'ChangePriority prepare ' . $dbObj->error;
             }
		}
		else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'ChangePriority connect ' . $dbObj->error;
        }

    	$dbObj->query('UNLOCK TABLES');
    	$dbObj->close();

    	$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;

    	return $resultArray;
    }
}
?>
