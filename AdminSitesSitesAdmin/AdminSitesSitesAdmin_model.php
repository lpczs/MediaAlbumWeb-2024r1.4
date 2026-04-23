<?php
require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsLocalization.php');

class AdminSitesSitesAdmin_model
{

    static function getGridData()
    {
        global $gConstants;

        $resultArray = Array();
        $addressItem = Array();
        $sitesArray = Array();
        $total = 0;
        $extraField = ',id ';

        $searchFields = UtilsObj::getPOSTParam('fields');
        $typesArray = array();
        $paramArray = array();
		$stmtArray = array();
		$searchQuery = '';
		$hideInactive = 0;
		
		// check that hideinactive has been sent before safely retrieving it
		if (isset($_POST['hideInactive']))
		{
			$hideInactive = filter_input(INPUT_POST, 'hideInactive', FILTER_SANITIZE_NUMBER_INT);
		}

        if ($searchFields != '')
        {
            $searchQuery = $_POST['query'];
            $selectedfields = explode(',', str_replace('"', "", str_replace("]", "", str_replace("[", "", $_POST['fields']))));

            if ($searchQuery != '')
            {
                foreach($selectedfields as $value)
                {
                    switch($value)
                    {
                        case 'sitecode':
                            $value = 'code';
                            break;
                        case 'name':
                            $value = 'name';
                            break;
                        case 'address':
                            $value = 'address1';
                            $stmtArray[] = '(`address2` LIKE ?)';
                            $paramArray[] = '%' . $searchQuery . '%';
                            $typesArray[] = 's';
                            $stmtArray[] = '(`address3` LIKE ?)';
                            $paramArray[] = '%' . $searchQuery . '%';
                            $typesArray[] = 's';
                            $stmtArray[] = '(`address4` LIKE ?)';
                            $paramArray[] = '%' . $searchQuery . '%';
                            $typesArray[] = 's';
                            $stmtArray[] = '(`city` LIKE ?)';
                            $paramArray[] = '%' . $searchQuery . '%';
                            $typesArray[] = 's';
                            $stmtArray[] = '(`county` LIKE ?)';
                            $paramArray[] = '%' . $searchQuery . '%';
                            $typesArray[] = 's';
                            $stmtArray[] = '(`state` LIKE ?)';
                            $paramArray[] = '%' . $searchQuery . '%';
                            $typesArray[] = 's';
                            $stmtArray[] = '(`region` LIKE ?)';
                            $paramArray[] = '%' . $searchQuery . '%';
                            $typesArray[] = 's';
                            $stmtArray[] = '(`postcode` LIKE ?)';
                            $paramArray[] = '%' . $searchQuery . '%';
                            $typesArray[] = 's';
                            $stmtArray[] = '(`countryname` LIKE ?)';
                            $paramArray[] = '%' . $searchQuery . '%';
                            $typesArray[] = 's';
                            break;
                        case 'sitegroup':
                            $value = 'sitegroup';
                            break;
                    }

                    $stmtArray[] = '(`' . $value . '` LIKE ?)';
                    $paramArray[] = '%' . $searchQuery . '%';
                    $typesArray[] = 's';
                }
            }
        }

        $start = 0;
        if (isset($_POST['start']))
        {
            $start = (int) $_POST['start'];
        }

        $limit = 100;
        if (isset($_POST['limit']))
        {
            $limit = (int) $_POST['limit'];
        }

        $sortby = 'code';
        if (isset($_POST['sort']))
        {
            $sortby = $_POST['sort'];
        }

        $dir = 'ASC';
        if (isset($_POST['dir']))
        {
            if ($_POST['dir'] != $dir)
            {
                $dir = 'DESC';
            }
        }

        switch($sortby)
        {
            case 'sitecode':
                $sort = '`code`';
                break;
            case 'name':
                $sort = '`name`';
                break;
            case 'sitegroup':
                $sort = '`sitegroup`';
                break;
            case 'productionsite':
                $sort = '`productionsitekey`';
                break;
            case 'sitetype':
                $extraField = ', IF(`productionsitekey` ="", 0, 1) AS test';
                $sort = '`test` ' . $dir . ',`sitetype`';
                break;
            case 'store':
                $sort = '`store`';
                break;
            case 'active':
                $sort = '`active`';
                break;
            default:
                $sort = '`code`';
        }

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            $filter = array();

            $filter[] = '(`isexternalstore` = 0)';

            if (!$gConstants['optionms'])
            {
                $filter[] = '(`sitetype` <> "0" AND (`productionsitekey` IS NULL OR `productionsitekey` = ""))';
            }
            if (!$gConstants['optioncfs'])
            {
                $filter[] = '(`sitetype` <> "2" AND `sitetype` <> "1") ';
            }
            $filter = ' WHERE ' . join(' AND ', $filter);

            if (count($stmtArray) > 0)
            {
                $stmtArray = ' AND (' . join(' OR ', $stmtArray) . ')';
            }
            else
            {
                $stmtArray = '';
			}

			// If No search made and hide inactive is true apply filter for inactive items
			if (($searchQuery === '') && ($hideInactive == 1))
			{
				$filter  .= 'AND (`active` = 1) ';
			}

            $stmtTotal = $dbObj->prepare('SELECT COUNT(*) FROM `SITES` ' . $filter . $stmtArray);
            $stmt = $dbObj->prepare('SELECT `id`, `companycode`, `code`, `name`, `address1`, `address2`, `address3`, `address4`, `city`, `county`, `state`,
                                    `regioncode`, `region`, `postcode`, `countrycode`, `countryname`, `sitetype`, `siteonline`, `sitegroup`, `productionsitekey`, `active`' . $extraField . ' FROM SITES
                                    ' . $filter . $stmtArray . ' ORDER BY ' . $sort . ' ' . $dir . ' LIMIT ' . $limit . ' OFFSET ' . $start . ';');
            if ($stmtTotal)
            {
                DatabaseObj::bindParams($stmtTotal, $typesArray, $paramArray);
                if ($stmtTotal->bind_result($total))
                {
                    if ($stmtTotal->execute())
                    {
                        $stmtTotal->fetch();
                    }
                }
                $stmtTotal->free_result();
                $stmtTotal->close();
                $stmtTotal = null;
            }

            if ($stmt)
            {
                DatabaseObj::bindParams($stmt, $typesArray, $paramArray);
                if ($stmt->bind_result($id, $companycode, $code, $name, $address1, $address2, $address3, $address4, $city, $county, $state,
                                $regioncode, $region, $postcode, $countrycode, $countryname, $siteType, $siteOnline, $siteGroup,
                                $productionsitekey, $active, $test))
                {
                    if ($stmt->execute())
                    {
                        while($stmt->fetch())
                        {
                            $addressItem['contactfirstname'] = '';
                            $addressItem['contactlastname'] = '';
                            $addressItem['customername'] = '';
                            $addressItem['customeraddress1'] = $address1;
                            $addressItem['customeraddress2'] = $address2;
                            $addressItem['customeraddress3'] = $address3;
                            $addressItem['customeraddress4'] = $address4;
                            $addressItem['customercity'] = $city;
                            $addressItem['customercounty'] = $county;
                            $addressItem['customerstate'] = $state;
                            $addressItem['customerregioncode'] = $regioncode;
                            $addressItem['customerregion'] = $region;
                            $addressItem['customerpostcode'] = $postcode;
                            $addressItem['customercountrycode'] = $countrycode;
                            $addressItem['customercountryname'] = $countryname;

                            $address = UtilsAddressObj::formatAddress($addressItem, '', '<br>');

                            $item['recordid'] = $id;
                            $item['code'] = $code;
                            $item['companycode'] = $companycode;
                            $item['name'] = $name;

                            $item['address'] = $address;
                            $item['productionsitekey'] = ($productionsitekey == '') ? 0 : 1;
                            $item['sitetype'] = $siteType;
                            $item['siteonline'] = $siteOnline;
                            $item['sitegroup'] = $siteGroup;
                            $item['isactive'] = $active;

                            array_push($sitesArray, $item);
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }

        $resultArray['sites'] = $sitesArray;
        $resultArray['total'] = $total;

        return $resultArray;
    }

    static function siteActivate()
    {
        global $gSession;

        $resultArray = Array();

        $ids = $_POST['ids'];
        $idList = explode(',', $ids);
        $active = $_POST['active'];
        if ($active != '0') $active = 1;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('UPDATE `SITES` SET `active` = ? WHERE `id` = ?'))
            {
                foreach($idList as $id)
                {
                    if ($stmt->bind_param('ii', $active, $id))
                    {
                        if ($stmt->execute())
                        {
                            $siteDataArray = DatabaseObj::getSiteFromID($id);

                            if ($siteDataArray['isactive'] == 0)
                            {
                                DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                        $gSession['username'], 0, 'ADMIN', 'SITE-DEACTIVATE', $id . ' ' . $siteDataArray['code'], 1);
                            }
                            else
                            {
                                DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                        $gSession['username'], 0, 'ADMIN', 'SITE-ACTIVATE', $id . ' ' . $siteDataArray['code'], 1);
                            }

                            array_push($resultArray, $siteDataArray);
                        }
                    }
                }
            }
            $stmt->free_result();
            $stmt->close();
            $stmt = null;

            $dbObj->close();
        }

        return $resultArray;
    }

    static function displayAdd()
    {
        global $gConstants;

        $resultArray = Array();

        $siteID = -1;
        $code = '';
        $name = '';
        $address1 = '';
        $address2 = '';
        $address3 = '';
        $address4 = '';
        $city = '';
        $state = '';
        $county = '';
        $region = '';
        $regionCode = '';
        $postCode = '';
        $countryCode = $gConstants['homecountrycode'];
        $firstName = '';
        $lastName = '';
        $telephone = '';
        $email = '';
        $storeUrl = '';
        $acceptAllProducts = 1;
        $acceptedProducts = '';
        $productList = Array();
        $openingTimesList = Array();
        $acceptedProductCodes = Array();
        $acceptedProductIDs = Array();
        $siteType = 1;
        $siteOnline = 1;
        $siteGroup = '';
        $siteGroupDefined = 0;
        $distributionCentreCode = '';
        $smtpProductionName = '';
        $smtpProductionAddress = '';
        $openingTimes = '';
        $isActive = 0;

        $siteGroups = self::getSiteGroupList();
        //if (count($siteGroups) > 0)
        //{
        //	$siteGroup = $siteGroups[0]['code'];
        //	$siteGroupDefined = 1;
        //}

        $productList = self::getProductList();
        $acceptedProductCodes = Array();
        $acceptedProductIndeces = Array();

        // set localised opening times
        $languageList = LocalizationObj::getLanguageList();
        $openingTimesList = $languageList;
        $itemCount = count($openingTimesList);
        for($i = 0; $i < $itemCount; $i++)
        {
            $openingTimesList[$i]['name'] = '';
        }

        if ($countryCode == '')
        {
            $countryCode = $gConstants['homecountrycode'];
        }

        $resultArray['id'] = $siteID;
        $resultArray['code'] = $code;
        $resultArray['companycode'] = '';
        $resultArray['companyName'] = $name;
        $resultArray['siteGroupCode'] = $siteGroup;
        $resultArray['address1'] = $address1;
        $resultArray['address2'] = $address2;
        $resultArray['address3'] = $address3;
        $resultArray['address4'] = $address4;
        $resultArray['city'] = $city;
        $resultArray['state'] = $state;
        $resultArray['county'] = $county;
        $resultArray['region'] = $region;
        $resultArray['regioncode'] = $regionCode;
        $resultArray['postcode'] = $postCode;
        $resultArray['countrycode'] = $countryCode;
        $resultArray['firstname'] = $firstName;
        $resultArray['lastname'] = $lastName;
        $resultArray['telephone'] = $telephone;
        $resultArray['email'] = $email;
        $resultArray['storeurl'] = $storeUrl;

        $resultArray['isproductionsite'] = 0;
        $resultArray['sitetype'] = $siteType;
        $resultArray['siteonline'] = $siteOnline;
        $resultArray['sitegroup'] = $siteGroup;
        $resultArray['distributioncentrecode'] = $distributionCentreCode;

        $resultArray['isactive'] = $isActive;

        $resultArray['productlist'] = $productList;
        $resultArray['acceptedproducts'] = $acceptedProductIndeces;
        $resultArray['acceptallproducts'] = $acceptAllProducts;
        $resultArray['smtpproductionname'] = $smtpProductionName;
        $resultArray['smtpproductionaddress'] = $smtpProductionAddress;

        $resultArray['languagelist'] = $languageList;
        $resultArray['openingtimeslist'] = $openingTimesList;
        $resultArray['defaultlang'] = $gConstants['defaultlanguagecode'];
        $resultArray['countries'] = UtilsAddressObj::getCountryList();
        $resultArray['sitegroups'] = $siteGroups;
        $resultArray['sitegroupdefined'] = $siteGroupDefined;
        $resultArray['companies'] = self::getCompanyList();
        $resultArray['productionsites'] = self::getProductionSiteList();
        $resultArray['distributioncentres'] = self::getDistributionCentreList();


        return $resultArray;
    }

    static function siteEdit()
    {
        global $gSession;

        $resultArray = Array();
        $addressItem = Array();
        $item = Array();
        $result = '';
        $resultParam = '';
        $canUpdate = true;

        $siteId = $_POST['siteid'];
        $code = strtoupper($_POST['sitecode']);
        $name = UtilsObj::getPOSTParam('sitename');
        $companyCode = UtilsObj::getPOSTParam('company');
        $siteType = UtilsObj::getPOSTParam('sitetype');
        if (($siteType == TPX_SITE_TYPE_PRODUCTION) && ($siteId < 0))
        {
            $siteType = TPX_SITE_TYPE_STORE; // don't allow production sites to be added accidentally
        }
        $isActive = (int) UtilsObj::getPOSTParam('active', 0);

        // Site Contact Tab
        $firstName = UtilsObj::getPOSTParam('firstname');
        $lastName = UtilsObj::getPOSTParam('lastname');
        $telephone = UtilsObj::getPOSTParam('telephone');
        $email = UtilsObj::getPOSTParam('email');
        $storeUrl = UtilsObj::getPOSTParam('storeurl');
        $siteOnline = (UtilsObj::getPOSTParam('siteonline') == 'false') ? 0 : 1;

        // Site Address
        // see if there are special address fields like
        // add1=add41, add42 - add43
        // meaning address1 = add41 + ", "  + add42 + " - " + add43
        // and     address4 = add41 + "<p>" + add42 + "<p>" + add43
        UtilsAddressObj::specialAddressFields($_POST['countryCode']);

        $countryCode = UtilsObj::getPOSTParam('countryCode');
        $countryName = UtilsObj::getPOSTParam('countryName');
        $address1 = UtilsObj::getPOSTParam('address1');
        $address2 = UtilsObj::getPOSTParam('address2');
        $address3 = UtilsObj::getPOSTParam('address3');
        $address4 = UtilsObj::getPOSTParam('address4');

        // we need to check to see if the string contains @@TAOPIXTAG@@. If it does then this means that it is a special address field.
        // we then need to convert @@TAOPIXTAG@@ back to a <p> so that it can be stored correctly in the database.
        $address4 = implode('<p>', mb_split('@@TAOPIXTAG@@', $address4));

        $city = UtilsObj::getPOSTParam('city');
        $postCode = UtilsObj::getPOSTParam('postCode');
        $region = UtilsObj::getPOSTParam('region');
        if ($region == 'STATE')
        {
            $regioncode = UtilsObj::getPOSTParam('statelist');
        }
        else
        {
            $regioncode = UtilsObj::getPOSTParam('countylist');
        }
        $county = UtilsObj::getPOSTParam('countyName');
        $state = UtilsObj::getPOSTParam('stateName');

        // Production Settings
        $smtpProductionName = UtilsObj::getPOSTParam('smtpname');
        $smtpProductionAddress = UtilsObj::getPOSTParam('smtpemail');
        $acceptAllProducts = (int) UtilsObj::getPOSTParam('acceptallproducts', 1);
        $acceptedProductCodes = UtilsObj::getPOSTParam('acceptedproductcodes');

        // Store Settings
        $distributionCentreCode = UtilsObj::getPOSTParam('distributioncentre');
        $siteGroupCode = UtilsObj::getPOSTParam('sitegroup');
        $openingTimes = UtilsObj::getPOSTParam('openingtimes');
        $openingTimes = str_replace("\n", '\\n', $openingTimes);
        $openingTimes = str_replace("\r", '\\n', $openingTimes);
        $openingTimes = str_replace("\\n\\n", '\\n', $openingTimes);

        if ($siteType != TPX_SITE_TYPE_STORE)
        {
            $distributionCentreCode = '';
            $siteGroupCode = '';
            $openingTimes = '';
        }

        if ($siteId > 0)
        {
            if ($siteType == TPX_SITE_TYPE_STORE)
            {
                $dbObj = DatabaseObj::getGlobalDBConnection();

                if ($stmt = $dbObj->prepare('SELECT `id` FROM `SITES` WHERE `distributioncentrecode` = ?'))
                {
                    if ($stmt->bind_param('s', $code))
                    {
                        if ($stmt->bind_result($id))
                        {
                            if ($stmt->execute())
                            {
                                if ($stmt->fetch())
                                {
                                    $result = 'str_ErrorDistCentreAssignedToStore';
                                    $resultParam = -1;
                                    $canUpdate = false;
                                }
                            }
                        }
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
            }

            if ($canUpdate)
            {
                // don't accept these values for production site
                $siteArray = DatabaseObj::getSiteFromCode($code);

                $dbObj = DatabaseObj::getGlobalDBConnection();
                if ($dbObj)
                {
                    if ($stmt = $dbObj->prepare('UPDATE `SITES` SET `acceptallproducts` = ?, `name` = ?, `address1` = ?, `address2` = ?, `address3` = ?, `address4` = ?,
					`city` = ?, `county` = ?, `state` = ?, `region` = ?, `regioncode` = ?, `postcode` = ?, `countrycode` = ?, `countryname` = ?, `telephonenumber` = ?, `emailaddress` = ?,
					`contactfirstname` = ?, `contactlastname` = ?, `sitetype` = ?, `distributioncentrecode` = ?, `sitegroup` = ?, `storeurl` = ?,
					`smtpproductionname` = ?, `smtpproductionaddress` = ?, `openingtimes` = ?, `siteonline` = ?, `active` = ?
					WHERE `id` = ?'))
                    {
                        if ($stmt->bind_param('isssssssssssssssssissssssiii', $acceptAllProducts, $name, $address1, $address2, $address3,
                                        $address4, $city, $county, $state, $region, $regioncode, $postCode, $countryCode, $countryName,
                                        $telephone, $email, $firstName, $lastName, $siteType, $distributionCentreCode, $siteGroupCode,
                                        $storeUrl, $smtpProductionName, $smtpProductionAddress, $openingTimes, $siteOnline, $isActive,
                                        $siteId))
                        {
                            if ($stmt->execute())
                            {
                                $update = self::updateSiteProducts($code, $acceptedProductCodes);

                                if ($update['result'] == '')
                                {
                                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                            $gSession['username'], 0, 'ADMIN', 'SITE-UPDATE', $siteId . ' ' . $code, 1);
                                }
                                else
                                {
                                    $result = $update['result'];
                                    $resultParam = $update['resultparam'];
                                }
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'siteEdit execute ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            // could not bind parameters
                            $result = 'str_DatabaseError';
                            $resultParam = 'siteEdit bind ' . $dbObj->error;
                        }
                        $stmt->free_result();
                        $stmt->close();
                        $stmt = null;
                    }
                    else
                    {
                        // could not prepare statement
                        $result = 'str_DatabaseError';
                        $resultParam = 'siteEdit prepare ' . $dbObj->error;
                    }
                    $dbObj->close();
                }
                else
                {
                    // could not open database connection
                    $result = 'str_DatabaseError';
                    $resultParam = 'siteEdit connect ' . $dbObj->error;
                }
            }
        }
        else
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('INSERT INTO `SITES` (`id`, `datecreated`, `code`, `name`, `address1`, `address2`, `address3`, `address4`, `city`, `county`, `state`,
																`regioncode`, `region`, `postcode`, `countrycode`, `countryname`, `telephonenumber`, `emailaddress`, `contactfirstname`, `contactlastname`,
																`sitetype`, `siteonline`, `sitegroup`, `distributioncentrecode`, `openingtimes`, `storeurl`, `smtpproductionname`, `smtpproductionaddress`, `active`)
                                    VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
                {
                    if ($stmt->bind_param('ssssssssssssssssssiissssssi', $code, $name, $address1, $address2, $address3, $address4, $city,
                                    $county, $state, $regioncode, $region, $postCode, $countryCode, $countryName, $telephone, $email,
                                    $firstName, $lastName, $siteType, $siteOnline, $siteGroupCode, $distributionCentreCode, $openingTimes,
                                    $storeUrl, $smtpProductionName, $smtpProductionAddress, $isActive))
                    {
                        if ($stmt->execute())
                        {
                            $siteId = $dbObj->insert_id;
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
                                    $gSession['username'], 0, 'ADMIN', 'SITE-INSERT', $siteId . ' ' . $code, 1);
                        }
                        else
                        {
                            // check for a duplicate key (site code)
                            if ($stmt->errno == 1062)
                            {
                                $result = 'str_ErrorSiteExists';
                            }
                            else
                            {
                                $result = 'str_DatabaseError';
                                $resultParam = 'siteAdd execute ' . $dbObj->error;
                            }
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'siteAdd bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'siteAdd prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'siteAdd connect ' . $dbObj->error;
            }
        }

        if ($result == '')
        {
            $addressItem['contactfirstname'] = '';
            $addressItem['contactlastname'] = '';
            $addressItem['customername'] = '';
            $addressItem['customeraddress1'] = $address1;
            $addressItem['customeraddress2'] = $address2;
            $addressItem['customeraddress3'] = $address3;
            $addressItem['customeraddress4'] = $address4;
            $addressItem['customercity'] = $city;
            $addressItem['customercounty'] = $county;
            $addressItem['customerstate'] = $state;
            $addressItem['customerregioncode'] = $regioncode;
            $addressItem['customerregion'] = $region;
            $addressItem['customerpostcode'] = $postCode;
            $addressItem['customercountrycode'] = $countryCode;
            $addressItem['customercountryname'] = $countryName;

            $address = UtilsAddressObj::formatAddress($addressItem, '', '<br>');

            $item['recordid'] = $siteId;
            $item['companycode'] = $companyCode;
            $item['code'] = $code;
            $item['name'] = $name;
            $item['companycode'] = $companyCode;
            $item['address'] = $address;
            $item['sitetype'] = $siteType;
            $item['siteonline'] = $siteOnline;
            $item['sitegroup'] = $siteGroupCode;
            $item['isactive'] = $isActive;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['item'] = $item;

        return $resultArray;
    }

    static function displayEdit()
    {
        global $gConstants;

        $resultArray = Array();

        $siteID = $_GET['id'];
        $code = '';
        $name = '';
        $address1 = '';
        $address2 = '';
        $address3 = '';
        $address4 = '';
        $city = '';
        $state = '';
        $county = '';
        $region = '';
        $regionCode = '';
        $postCode = '';
        $countryCode = '';
        $firstName = '';
        $lastName = '';
        $telephone = '';
        $email = '';
        $storeUrl = '';
        $acceptAllProducts = 1;
        $siteType = 0;
        $siteOnline = 1;
        $siteGroup = '';
        $siteGroupDefined = 0;
        $distributionCentreCode = '';
        $smtpProductionName = '';
        $smtpProductionAddress = '';
        $openingTimes = '';
        $isActive = 0;
        $companyCode = '';
        $productionSiteKey = '';

        $siteGroups = self::getSiteGroupList();
        if (count($siteGroups) > 0)
        {
            $siteGroup = $siteGroups[0]['code'];
            $siteGroupDefined = 1;
        }

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $stmt = $dbObj->prepare('SELECT `companycode`, `code`, `productionsitekey`, `name`, `address1`, `address2`, `address3`, `address4`,
                                        `city`, `state`, `county`, `region`, `regioncode`, `postcode`, `countrycode`, `contactfirstname`,
                                        `contactlastname`, `telephonenumber`, `emailaddress`, `storeurl`, `acceptallproducts`, `sitetype`,
                                        `siteonline`, `sitegroup`, `distributioncentrecode`, `smtpproductionname`, `smtpproductionaddress`,
                                        `openingtimes`, `active`
                                    FROM `SITES`
                                    WHERE `id` = ?');
            if ($stmt)
            {
                if ($stmt->bind_param('i', $siteID))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($companyCode, $code, $productionSiteKey, $name, $address1, $address2, $address3,
                                                $address4, $city, $state, $county, $region, $regionCode, $postCode, $countryCode,
                                                $firstName, $lastName, $telephone, $email, $storeUrl, $acceptAllProducts, $siteType,
                                                $siteOnline, $siteGroup, $distributionCentreCode, $smtpProductionName,
                                                $smtpProductionAddress, $openingTimes, $isActive))
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

            $userAssigned = 0;
            if ($dbObj)
            {
                $bindOk = false;
                if (($productionSiteKey != '') && ($siteType == 2))
                {
                    $stmt = $dbObj->prepare('SELECT count(*) FROM USERS WHERE `owner` = ? AND `usertype` = ? ');
                    $storeUser = TPX_LOGIN_STORE_USER;
                    $bindOk = $stmt->bind_param('si', $code, $storeUser);
                }
                else
                {
                    $stmt = $dbObj->prepare('SELECT count(*) FROM USERS WHERE `owner` = ?');
                    $bindOk = $stmt->bind_param('s', $code);
                }

                if ($bindOk)
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($userAssigned))
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

                $dbObj->close();
            }
        }

        $productList = self::getProductList($companyCode);
        $acceptedProductCodes = self::getAcceptedProductCodes($code);
        $acceptedProductIndeces = self::getAcceptedProductIndeces($productList, $acceptedProductCodes, $acceptAllProducts);

        // lookup list for localised opening times
        $tempList = explode('<p>', $openingTimes);
        $lookupList = Array();
        $tempCount = count($tempList);
        for($i = 0; $i < $tempCount; $i++)
        {
            // split each language item into its code and name
            $charPos = strpos($tempList[$i], ' ');
            $tempCode = substr($tempList[$i], 0, $charPos);
            $tempString = substr($tempList[$i], $charPos + 1);
            $lookupList[$tempCode] = $tempString;
        }

        // set localised opening times
        $languageList = LocalizationObj::getLanguageList();
        $openingTimesList = $languageList;
        $itemCount = count($openingTimesList);
        for($i = 0; $i < $itemCount; $i++)
        {
            $tempCode = $openingTimesList[$i]['code'];
            $openingTimesList[$i]['name'] = (array_key_exists($tempCode, $lookupList)) ? $lookupList[$tempCode] : '';
        }

        if ($countryCode == '')
        {
            $countryCode = $gConstants['homecountrycode'];
        }

        $resultArray['id'] = $siteID;
        $resultArray['code'] = $code;
        $resultArray['companycode'] = $companyCode;
        $resultArray['companyName'] = $name;
        $resultArray['siteGroupCode'] = $siteGroup;
        $resultArray['address1'] = $address1;
        $resultArray['address2'] = $address2;
        $resultArray['address3'] = $address3;
        $resultArray['address4'] = $address4;
        $resultArray['city'] = $city;
        $resultArray['state'] = $state;
        $resultArray['county'] = $county;
        $resultArray['region'] = $region;
        $resultArray['regioncode'] = $regionCode;
        $resultArray['postcode'] = $postCode;
        $resultArray['countrycode'] = $countryCode;
        $resultArray['firstname'] = $firstName;
        $resultArray['lastname'] = $lastName;
        $resultArray['telephone'] = $telephone;
        $resultArray['email'] = $email;
        $resultArray['storeurl'] = $storeUrl;

        $resultArray['isproductionsite'] = ($productionSiteKey == '') ? 0 : 1;
        $resultArray['sitetype'] = $siteType;
        $resultArray['siteonline'] = $siteOnline;
        $resultArray['sitegroup'] = $siteGroup;
        $resultArray['distributioncentrecode'] = $distributionCentreCode;

        $resultArray['isactive'] = $isActive;

        $resultArray['productlist'] = $productList;
        $resultArray['acceptedproducts'] = $acceptedProductIndeces;
        $resultArray['acceptallproducts'] = $acceptAllProducts;
        $resultArray['smtpproductionname'] = $smtpProductionName;
        $resultArray['smtpproductionaddress'] = $smtpProductionAddress;

        $resultArray['languagelist'] = $languageList;
        $resultArray['openingtimeslist'] = $openingTimesList;
        $resultArray['defaultlang'] = $gConstants['defaultlanguagecode'];
        $resultArray['countries'] = UtilsAddressObj::getCountryList();
        $resultArray['sitegroups'] = $siteGroups;
        $resultArray['sitegroupdefined'] = $siteGroupDefined;
        $resultArray['companies'] = self::getCompanyList();
        $resultArray['productionsites'] = self::getProductionSiteList();
        $resultArray['distributioncentres'] = self::getDistributionCentreList();
        $resultArray['usersassigned'] = $userAssigned;

        return $resultArray;
    }

    static function siteDelete()
    {
        global $gSession;

        $allDeleted = 1;
        $siteIdList = explode(',',$_POST['idlist']);
        $siteCount = count($siteIdList);
        $siteList = Array();
        $sitesNotUsedInOrder = Array();
        $sitesNotUsedInUser = Array();
        $sitesNotProductionSite = Array();
        $sitesNotUsedAsDistributionCentre = Array();
        $sitesDeleted = Array();
		$site = Array();
		$resultList = Array();
        $siteCode = '';
        $siteType = '';

        if ($siteCount > 0)
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
            	// get site codes
                if ($stmt = $dbObj->prepare('SELECT `code`,`sitetype` FROM `SITES` WHERE `id` = ?'))
                {
                	foreach ($siteIdList as $siteId)
                	{
						if ($stmt->bind_param('s', $siteId))
						{
							if ($stmt->bind_result($siteCode, $siteType))
							{
							   if ($stmt->execute())
							   {
									if ($stmt->fetch())
									{
										$site['id'] = $siteId;
										$site['code'] = $siteCode;
										$site['type'] = $siteType;

										$resultList[$siteCode] = '';

										array_push($siteList, $site);
									}
							   }
							}
						}
                    }

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }

                for ($i = 0; $i < count($siteList); $i++)
                {
                	if ($siteList[$i]['type'] == '0')
                	{
                		$allDeleted = 0;
						$resultList[$siteList[$i]['code']] = 'str_ErrorDeleteProductionSites';
                	}
                	else
                	{
                		array_push($sitesNotProductionSite, $siteList[$i]);
                	}
                }

                // fill array with sites that are not being used as distribution centre of a store
            	// only consider those sites not used in an order
                if ($stmt = $dbObj->prepare('SELECT `code` FROM `SITES` WHERE (`distributioncentrecode` = ?)'))
                {
                	foreach ($sitesNotProductionSite as $site)
                	{
						if ($stmt->bind_param('s', $site['code']))
						{
							if ($stmt->bind_result($siteCode))
							{
							   if ($stmt->execute())
							   {
									if ($stmt->fetch())
									{
										$allDeleted = 0;
										$resultList[$site['code']] = 'str_ErrorUsedInSites';
									}
									else
									{
										array_push($sitesNotUsedAsDistributionCentre, $site);
									}
							   }
							}
						}
                    }

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }

             	// get site code if this site has ever been used in an order, either as store or as distribution centre
                if ($stmt = $dbObj->prepare('SELECT DISTINCT(s.code) FROM `SITES` s, `ORDERSHIPPING` os WHERE ((s.code=os.storecode) OR (s.code=os.distributioncentrecode)) AND (s.id = ?)'))
                {
                	foreach ($sitesNotUsedAsDistributionCentre as $site)
                	{
						if ($stmt->bind_param('s', $site['id']))
						{
							if ($stmt->bind_result($siteCode))
							{
							   if ($stmt->execute())
							   {
									if ($stmt->fetch())
									{
										$allDeleted = 0;
									 	$resultList[$siteCode] = 'str_ErrorUsedInOrders';
									}
									else
									{
										array_push($sitesNotUsedInOrder, $site);
									}
							   }
							}
						}
                    }

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }

                if ($stmt = $dbObj->prepare('SELECT DISTINCT(s.code) FROM `SITES` s, `USERS` u WHERE (s.code=u.owner) AND (s.id = ?)'))
                {
                	foreach ($sitesNotUsedInOrder as $site)
                	{
						if ($stmt->bind_param('s', $site['id']))
						{
							if ($stmt->bind_result($siteCode))
							{
							   if ($stmt->execute())
							   {
									if ($stmt->fetch())
									{
										$allDeleted = 0;
									 	$resultList[$siteCode] = 'str_ErrorUsedInUsers';
									}
									else
									{
										array_push($sitesNotUsedInUser, $site);
									}
							   }
							}
						}
                    }

                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }



                // make sure we never delete a production site
				if ($stmt = $dbObj->prepare('DELETE FROM `SITES` WHERE (`id` = ?) AND (`productionsitekey` = "") AND (`sitetype` <> 0)'))
				{
                	foreach ($sitesNotUsedInUser as $site)
                	{
						if ($stmt->bind_param('i', $site['id']))
						{
							if ($stmt->execute())
							{
		                        if ($dbObj->affected_rows == 1)
		                        {
									DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
										'ADMIN', 'SITE-DELETE', $site['id'] . ' ' . $site['code'], 1);
									array_push($sitesDeleted, $site['id']);
		                        }
		                        else
		                        {
		                        	$allDeleted = 0;
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
        }

        $resultArray['alldeleted'] = $allDeleted;
        $resultArray['sitesids'] = $sitesDeleted;
        $resultArray['sitemes'] = $resultList;

        return $resultArray;
    }

    static function getSiteGroupList()
    {
        global $gSession;

        $siteGroups = array();
        $item = array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `code`, `name` FROM `SITEGROUPS`'))
            {
                if ($stmt->bind_result($siteGroupCode, $siteGroupName))
                {
                    if ($stmt->execute())
                    {
                        while($stmt->fetch())
                        {
                            $item['code'] = $siteGroupCode;
                            $item['name'] = LocalizationObj::getLocaleString($siteGroupName, $gSession['browserlanguagecode'], true);
                            array_push($siteGroups, $item);
                        }
                    }
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }

        return $siteGroups;
    }

    static function getCompanyList()
    {
        global $gSession;

        $companies = array();
        $item = array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `code`, `companyname` FROM `COMPANIES` WHERE `code` <> "";'))
            {
                if ($stmt->bind_result($companyCode, $companyName))
                {
                    if ($stmt->execute())
                    {
                        while($stmt->fetch())
                        {
                            $item['code'] = $companyCode;
                            $item['name'] = $companyName;
                            array_push($companies, $item);
                        }
                    }
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }

        return $companies;
    }

    static function getProductionSiteList()
    {
        global $gSession;

        $productionSites = array();
        $item = array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `code`, `name` FROM `SITES` WHERE `productionsitekey` <> "";'))
            {
                if ($stmt->bind_result($siteCode, $siteName))
                {
                    if ($stmt->execute())
                    {
                        while($stmt->fetch())
                        {
                            $item['code'] = $siteCode;
                            $item['name'] = $siteName;
                            array_push($productionSites, $item);
                        }
                    }
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }

        return $productionSites;
    }

    static function getDistributionCentreList()
    {
        global $gSession;

        $distributionCentres = array();
        $item = array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `code`, `name` FROM `SITES` WHERE `sitetype` = 1;'))
            {
                if ($stmt->bind_result($siteCode, $siteName))
                {
                    if ($stmt->execute())
                    {
                        while($stmt->fetch())
                        {
                            $item['code'] = $siteCode;
                            $item['name'] = $siteName;
                            array_push($distributionCentres, $item);
                        }
                    }
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }

        return $distributionCentres;
    }

    static function getProductList($companyCode = '**ALL**')
    {
        global $gSession;

        $productsArray = array();
        $productItem = array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if (($companyCode == '**ALL**') || ($companyCode == ''))
            {
                $stmt = $dbObj->prepare('SELECT `id`, `code`, `name`, `active` FROM `PRODUCTS` WHERE `deleted` = 0 ORDER BY `code`');
                $bindOk = true;
            }
            else
            {
                $stmt = $dbObj->prepare('SELECT `id`, `code`, `name`, `active` FROM `PRODUCTS` WHERE `deleted` = 0 AND (`companycode` = ? OR `companycode` = "") ORDER BY `code`');
                $bindOk = $stmt->bind_param('s', $companyCode);
            }
            if (($stmt) && ($bindOk))
            {
                if ($stmt->bind_result($productId, $productCode, $productName, $productActive))
                {
                    if ($stmt->execute())
                    {
                        while($stmt->fetch())
                        {
                            $productItem['id'] = $productId;
                            $productItem['code'] = $productCode;
                            $productItem['name'] = UtilsObj::encodeString(LocalizationObj::getLocaleString($productName,
                                                    $gSession['browserlanguagecode'], true), false);
                            $productItem['active'] = $productActive;
                            array_push($productsArray, $productItem);
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }

        return $productsArray;
    }

    static function getAcceptedProductCodes($pSiteCode)
    {
        $productCodesArray = Array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `productcode` FROM `SITEPRODUCTS` WHERE `ownercode` = ?'))
            {
                if ($stmt->bind_param('s', $pSiteCode))
                {
                    if ($stmt->bind_result($productCode))
                    {
                        if ($stmt->execute())
                        {
                            while($stmt->fetch())
                            {
                                array_push($productCodesArray, $productCode);
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

        return $productCodesArray;
    }

    static function getAcceptedProductIndeces($pProductList, $pAcceptedProductCodes, $pAcceptAllProducts)
    {
        // return list of all Product ID accepted by the site

        $productIndeces = Array();

        $itemCount = count($pProductList);
        for($i = 0; $i < $itemCount; $i++)
        {
            // if all accepted or code is in $pAcceptedProductCodes
            if (($pAcceptAllProducts == 1) || (in_array($pProductList[$i]['code'], $pAcceptedProductCodes)))
            {
                array_push($productIndeces, $pProductList[$i]['code']);
            }
        }

        return $productIndeces;
    }

    static function updateSiteProducts($pSiteCode, $pAcceptedProductIDs)
    {
        // update SITEPRODUCTS table
        $resultArray = Array();
        $result = '';
        $resultParam = '';

        $productList = self::getProductList();
        $acceptedProductIDs = explode(',', $pAcceptedProductIDs);

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('DELETE FROM `SITEPRODUCTS` WHERE `ownercode` = ?'))
            {
                if ($stmt->bind_param('s', $pSiteCode))
                {
                    if (!$stmt->execute())
                    {
                    	$result = 'str_DatabaseError';
						$resultParam = 'siteproduct delete execute ' . $dbObj->error;
                    }
                }
                else
                {
                	$result = 'str_DatabaseError';
					$resultParam = 'siteproduct delete bind ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
            	$result = 'str_DatabaseError';
				$resultParam = 'siteproduct delete prepare ' . $dbObj->error;
            }


            if ($result == '')
            {
            	$itemCount = count($productList);

				if ($stmt = $dbObj->prepare('INSERT INTO `SITEPRODUCTS` VALUES (0, now(), ?, ?)'))
				{
					for ($i = 0; $i < $itemCount; $i++)
					{
						// if code is in $acceptedProductIDs
						if (in_array($productList[$i]['code'], $acceptedProductIDs))
						{
							if ($stmt->bind_param('ss', $pSiteCode, $productList[$i]['code']))
							{
								if (! $stmt->execute())
								{
									$result = 'str_DatabaseError';
									$resultParam = 'siteproduct update execute ' . $dbObj->error;
								}
							}
							else
							{
								// could not bind parameters
								$result = 'str_DatabaseError';
								$resultParam = 'siteproduct update bind ' . $dbObj->error;
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
					$resultParam = 'siteproduct update prepare ' . $dbObj->error;
				}
            }

            $dbObj->close();
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
    }

}
?>
