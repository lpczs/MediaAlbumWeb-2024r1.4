<?php

require_once('../libs/internal/curl/Curl.php');

class AdminTaopixOnlineProductURLAdmin_model
{
	static function getGridData()
	{
		global $gConstants;

		$resultArray = array();
        $companyCode = '';
        $groupCode = $_POST['groupcode'];
        $filter = $_POST['filter'];
		$groupDataStatus = $_POST['groupdatastatus'];
		$groupData = $_POST['groupdata'];
		$collectionCode = $_POST['collectioncode'];
		$wizardModeOverride = $_POST['wizstatus'];
		$wizardParams = $_POST['wizparams'];
		$uiOverrideMode = $_POST['uioverridemode'];
		$aiModeOverride = $_POST['aimodeoverride'];

        if ($gConstants['optionms'])
        {
        	$companyCode = $_POST['companycode'];
        }

		// read any custom parameter data
		$cpStatus = $_POST['cpstatus'];
		$cpData = json_decode($_POST['cpdata'], true);

        $resultArray = self::getProductURLData($companyCode, $groupCode, $collectionCode, $filter, $groupDataStatus, $groupData, $cpStatus, $cpData, 
												$wizardModeOverride, $wizardParams, $uiOverrideMode, $aiModeOverride);

        return $resultArray;
	}

	static function productURLExport()
	{
		global $gConstants;

		$resultArray = array();

        $companyCode = '';
        $groupCode = $_GET['groupcode'];
        $filter = $_GET['filter'];
        $groupDataStatus = $_GET['groupdatastatus'];
		$groupData = $_GET['groupdata'];
		$collectionCode = $_GET['collectioncode'];
		$wizardModeOverride = $_GET['wizstatus'];
		$wizardParams = $_GET['wizparams'];
		$uiOverrideMode = $_GET['uioverridemode'];
		$aiModeOverride = $_GET['aimodeoverride'];

        if ($gConstants['optionms'])
        {
        	$companyCode = $_GET['companycode'];
        }

		// read any custom parameter data
		$cpStatus = $_GET['cpstatus'];
		$cpData = json_decode($_GET['cpdata'], true);

        $resultArray = self::getProductURLData($companyCode, $groupCode, $collectionCode, $filter, $groupDataStatus, $groupData, $cpStatus, $cpData,
												$wizardModeOverride, $wizardParams, $uiOverrideMode, $aiModeOverride);
        $resultArray['companycode'] = $companyCode;
        $resultArray['groupcode'] = $groupCode;
		$resultArray['filter'] = $filter;

        return $resultArray;
	}

	static function getProductURLData($pCompanyCode, $pGroupCode, $pCollectionCode, $pFilter, $pGroupDataStatus, $pGroupData, $pCPStatus, $pCPData,  
										$pWizardModeOverride, $pWizardParams, $pUIOverrideMode, $pAiModeOverride)
	{
		
		$resultArray = array();
		$resultArray['urldata'] = array();
		$result = '';
        $resultParam = '';
        $systemConfigArray = DatabaseObj::getSystemConfig();

        $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($pGroupCode);

		$webBrandCode = $licenseKeyArray['webbrandcode'];
		$webBrandArray = DatabaseObj::getBrandingFromCode($webBrandCode);
        $onlineDesignerURL = $webBrandArray['weburl'];

		if ($onlineDesignerURL == '')
		{
			$webBrandArray = DatabaseObj::getBrandingFromCode('');
			$onlineDesignerURL = $webBrandArray['weburl'];
		}
        $onlineDesignerURL = UtilsObj::correctPath($onlineDesignerURL);

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$paramString = '';
			$whereParams = array();

			// build the sub query
			$subQuery = 'SELECT `pl`.`groupcode`, `pl`.`productcode`, `pl`.`componentcode`, `pl`.`active` FROM `PRICELINK` `pl` WHERE (`pl`.`componentcode` = "")';

			// when a license key is passed add this to the sub query
			if($pGroupCode !== 'NOTPRICED')
			{
				$subQuery .=  ' AND ((`pl`.`groupcode` = ?) OR (`pl`.`groupcode` = ""))';
				$paramString .= 's';
				$whereParams[] = $pGroupCode;
			}
			$subQuery .= ' AND ((`pl`.`companycode` = ?) OR (`pl`.`companycode` = ""))';
			$paramString .= 's';
			$whereParams[] = $pCompanyCode;
			$subQuery .= ' GROUP BY `pl`.`productcode`';

			$baseSql = 'SELECT `pcl`.`id`,  `pcl`.`collectioncode`, `pcl`.`collectionname`, `pcl`.`productcode`, `pcl`.`productname` FROM `PRODUCTCOLLECTIONLINK` `pcl`
                            INNER JOIN `PRODUCTS` `pr`
                                ON (`pr`.`code` = `pcl`.`productcode`)
                                    AND (`pr`.`deleted` = 0)';

			// when a filter is either active or inactive filter the join for products and join the applicationfiles table
            $baseSql .= (($pFilter === 'ACTIVE') ? ' AND (`pr`.`active` = 1)' : '');
            
            $baseSql .=  ' INNER JOIN `APPLICATIONFILES` `af` 
                            ON (`af`.`ref` = `pcl`.`collectioncode`) 
                                AND (`af`.`type` = 0)
                                AND (`af`.`deleted` = 0)';

            // when a filter is either active or inactive filter the join for products and join the applicationfiles table
            $baseSql .= (($pFilter === 'ACTIVE') ? ' AND (`af`.`active` = 1)' : '');

			$baseSql .= ' LEFT JOIN (' . $subQuery . ') as `pl2` ON `pl2`.`productcode` = `pcl`.`productcode`'
					. ' WHERE ((`pcl`.`companycode` = ?) OR (`pcl`.`companycode` = "")) AND (`pcl`.`availableonline` = 1)';
			// attach company code to whereParams as a string
			$paramString .= 's';
			$whereParams[] = $pCompanyCode;

			// attach collection code if not -1 (show all)
			if ($pCollectionCode !== '-1')
			{
				$baseSql .= ' AND (`pcl`.`collectioncode` = ?)';
				$paramString .= 's';
				$whereParams[] = $pCollectionCode;
			}

			// additional filter options
			if ($pGroupCode === 'NOTPRICED')
			{
				$baseSql .= ' AND (ISNULL(`pl2`.`productcode`))';
			}
			else
			{
				// determine if key is active as this changes what we filter the result set with
				$licenseActive = (($licenseKeyArray['isactive'] == 0) || ($licenseKeyArray['availableonline'] == 0)) ? false : true;
				if ($licenseActive === true)
				{
					// active license
					if (($pFilter === 'ACTIVE') || ($pFilter === 'INACTIVE'))
					{
						$baseSql .= ' AND (NOT ISNULL(`af`.`active`)) AND (NOT ISNULL(`pr`.`active`)) AND (NOT ISNULL(`pl2`.`productcode`))';
						if ($pFilter === 'INACTIVE')
						{
							$baseSql .= ' AND ((`af`.`active` = 0) OR (`pr`.`active` = 0) OR (`pl2`.`active` = 0))';
						}
					}
					else
					{
						$baseSql .= ' AND (NOT ISNULL(`pl2`.`productcode`))';
					}
				}
				else
				{
					// additional param for inactive license or items that are not priced for the current view
					$baseSql .= ' AND (NOT ISNULL(`pl2`.`productcode`))';
				}
			}

			$baseSql .= ' ORDER BY `pcl`.`collectioncode`, `pcl`.`productcode`';

			$stmt = $dbObj->prepare($baseSql);
			
			if($stmt)
			{
				$bindOK = DatabaseObj::bindParams($stmt, $paramString, $whereParams);

				if ($bindOK)
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->bind_result($id, $collectionCode, $collectionName, $productCode, $productName))
							{
								while ($stmt->fetch())
								{

									if ($pGroupCode == 'NOTPRICED')
									{
										$theItem['url'] = '';
									}
									else
									{
										$paramData = $collectionCode . chr(9) . $productCode . chr(9) . $pGroupCode . chr(9) . $pGroupDataStatus . chr(9) . $pGroupData;

										// the custom parameters
										$paramData .= chr(9) . $pCPStatus;
										if ($pCPStatus == 2)
										{
											// add the number of parameters passed
											$paramData .= chr(9) . count($pCPData);
											foreach ($pCPData as $customParam)
											{
												$paramData .= chr(9) . 'cp' . implode('=', $customParam);
											}
										}
										else
										{
											$paramData .= chr(9) . '0';
										}

										// the new URL data format has new params seperated by new lines.
										// each param should be key=value pair.
										$paramData .= chr(10) . 'wms=' . $pWizardModeOverride . chr(10) . 'wmp=' . $pWizardParams;
										$paramData .= chr(10) . 'uio=' . $pUIOverrideMode;
										$paramData .= chr(10) . 'aimo=' . $pAiModeOverride;

										$IVArray = str_split(md5($pCompanyCode . $pGroupCode . $collectionCode . $productCode));
										$iv = $IVArray[3] . $IVArray[7] . $IVArray[11] . $IVArray[15]. $IVArray[19] . $IVArray[23] . $IVArray[27] . $IVArray[31];
 
										$productIdent = UtilsObj::encryptData($paramData, $systemConfigArray['systemkey'], true, $iv);

										$url = $onlineDesignerURL . '?fsaction=OnlineAPI.create&id=' . $productIdent;
										$theItem['url'] = $url;
									
									}

									$theItem['id'] = $id;
									$theItem['collectioncode'] = $collectionCode;
									$theItem['collectionname'] = $collectionName;
									$theItem['productcode'] = $productCode;
									$theItem['productname'] = $productName;

									$resultArray['urldata'][] = $theItem;
								}
							}
							else
							{
								// could not bind result
								$result = 'str_DatabaseError';
								$resultParam = __FUNCTION__ . ' bind result ' . $dbObj->error;
							}
						}
						else
						{
							// could not execute statement
							$result = 'str_DatabaseError';
							$resultParam = __FUNCTION__ . ' store result ' . $dbObj->error;
						}
					}
					else
					{
						// could not execute statement
						$result = 'str_DatabaseError';
						$resultParam = __FUNCTION__ . ' execute ' . $dbObj->error;
					}
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = __FUNCTION__ . ' bind params ' . $dbObj->error;
				}
				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = __FUNCTION__ . ' prepare ' . $dbObj->error;
			}
			$dbObj->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = __FUNCTION__ . ' connect ' . $dbObj->error;
		}

		$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
	}

	static function productURLDecrypt()
	{
		$resultArray = array();
		$result = '';

		// take the url to decrypt, FULL url starting with http...
		$url = $_POST['decrypturl'];

		// split the URL to obtain the parameters
		$urlParts = explode('&', $url);

		// remove the first part, as this will be the domain + action
		array_shift($urlParts);

		// expand reaining elements into parameter and values, place in associated array
		$urlParamArray = array();

		foreach ($urlParts as $theURLParam)
		{
			$temp = explode('=', $theURLParam, 2);

			$urlParamArray[$temp[0]] = $temp[1];
		}

		// decrypt the encrypted component of the url
		$systemConfigArray = DatabaseObj::getSystemConfig();
		$productIdentData = explode(chr(10), UtilsObj::decryptData($urlParamArray['id'], $systemConfigArray['systemkey'], true), 2);

		$legacyParamArray = explode(chr(9), $productIdentData[0]);

		if (count($legacyParamArray) < 3)
		{
			$result = 'str_ErrorDecryptURL';
		}
		else
		{
			$parsedParamArray = UtilsObj::parseProductURLIdentData($productIdentData, $urlParamArray);
		}

		$resultArray['result'] = $result;
        $resultArray['urldata'] = $parsedParamArray;

		return $resultArray;
	}
}
?>