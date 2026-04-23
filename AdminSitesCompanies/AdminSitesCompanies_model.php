<?php

require_once('../Utils/UtilsDatabase.php');

class AdminSitesCompanies_model
{
    static function getGridData()
    {
	    $resultArray = Array();
	    
        $dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
	    {
			if ($stmt = $dbObj->prepare('SELECT `id`, `code`, `companyname`, `address1`, `address2`, `address3`, `address4`, `city`, `county`, `state`, 
										`regioncode`, `region`, `postcode`, `countrycode`, `countryname` FROM COMPANIES WHERE `code` <> "";'))
			{
				if ($stmt->bind_result($id, $code, $name, $address1, $address2, $address3, $address4, $city, $county, $state, 
										$regioncode, $region, $postcode, $countrycode, $countryname))
				{
					if ($stmt->execute())
					{
						while ($stmt->fetch())
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
							$item['name'] = $name;
							$item['address'] = $address;
							
							array_push($resultArray, $item);
						}
					}
				}	
			}
            $dbObj->close();
        }
        
        return $resultArray;
    }
    
    static function displayEdit($pID)
	{
	    $resultArray = Array();
	    
	    global $gConstants;
	    
        $dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
	    {
			if ($stmt = $dbObj->prepare('SELECT `id`, `code`, `companyname`, `address1`, `address2`, `address3`, `address4`, `city`, `county`, `state`, 
										`regioncode`, `region`, `postcode`, `countrycode`, `countryname`, `telephonenumber`, `emailaddress`, `contactfirstname`, `contactlastname`, 
										`taxaddress`, `usedefaultipaccesslist`, `ipaccesslist` FROM COMPANIES WHERE `id` = ?;'))
			{
				if ($stmt->bind_result($id, $code, $name, $address1, $address2, $address3, $address4, $city, $county, $state, 
										$regioncode, $region, $postcode, $countrycode, $countryname, $telephonenumber, $emailaddress, $contactfirstname, $contactlastname, $taxaddress,
										$useDefaultIpAccessList, $ipAccessList))
				{
					if ($stmt->bind_param('i', $pID))
					{
						if ($stmt->execute())
						{
							while ($stmt->fetch())
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
							}
						}
					}
				}	
			}
            $dbObj->close();
        }
        
        $resultArray['id'] = $id;
		$resultArray['code'] = $code;
		$resultArray['name'] = $name;
		$resultArray['address'] = $address;
		
		$resultArray['contactfirstname'] = $contactfirstname;
		$resultArray['contactlastname'] = $contactlastname;
		$resultArray['customername'] = '';
		$resultArray['customeraddress1'] = $address1;
		$resultArray['customeraddress2'] = $address2;
		$resultArray['customeraddress3'] = $address3;
		$resultArray['customeraddress4'] = $address4;
		$resultArray['customercity'] = $city;
		$resultArray['customercounty'] = $county;
		$resultArray['customerstate'] = $state;
		$resultArray['customerregioncode'] = $regioncode;
		$resultArray['customerregion'] = $region;
		$resultArray['customerpostcode'] = $postcode;
		$resultArray['customercountrycode'] = $countrycode;
		$resultArray['customercountryname'] = $countryname;
		$resultArray['telephonenumber'] = $telephonenumber;
		$resultArray['emailaddress'] = $emailaddress;
		$resultArray['taxaddress'] = $taxaddress;
		
		$resultArray['usedefaultipaccesslist'] = $useDefaultIpAccessList;
		$resultArray['ipaccesslist'] = $ipAccessList;
		$resultArray['defaultipaccesslist'] = $gConstants['defaultipaccesslist'];
		
		return $resultArray;
    }

    static function companyEdit()
    {
        global $gSession;
        
        $result = '';
        $resultParam = '';

        $companyID = $_GET['id'];
        $companyCode = strtoupper($_POST['code']);
        $companyName = html_entity_decode($_POST['name'], ENT_QUOTES);
        
        $firstName = html_entity_decode($_POST['companyContactFirstName'], ENT_QUOTES);
        $lastName = html_entity_decode($_POST['companyContactLastName'], ENT_QUOTES);
        $email = html_entity_decode($_POST['emailAddress'], ENT_QUOTES);
        $telephone = html_entity_decode($_POST['phoneNumber'], ENT_QUOTES);
        $taxAddress = html_entity_decode(UtilsObj::getPOSTParam('taxaddress'), ENT_QUOTES);
        
        $useDefaultIpAddressList = $_POST['useDefaultIpAddressList'];
        if ($useDefaultIpAddressList == '1')
        {
        	$ipAccessList = '';
        }
        else 
        {
        	$ipAccessList = str_replace(' ', '', $_POST['ipaccesslist']);
        	$ipAccessList = str_replace(array("\r", "\r\n", "\n"), '', $ipAccessList);
        }
        
        // Site Address

		// see if there are special address fields like 
		// add1=add41, add42 - add43
		// meaning address1 = add41 + ", "  + add42 + " - " + add43
		// and     address4 = add41 + "<p>" + add42 + "<p>" + add43
        $countryCode = UtilsObj::getPOSTParam('countryCode');
		UtilsAddressObj::specialAddressFields($countryCode);

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
        
        
        if (($companyCode != '') && ($companyName != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('UPDATE `COMPANIES` SET `companyname` = ?, `address1` = ?, `address2` = ?, `address3` = ?, `address4` = ?, `city` = ?, 
                		`county` = ?, `state` = ?, `regioncode` = ?, `region` = ?, `postcode` = ?, `countrycode` = ?, `countryname` = ?, `telephonenumber` = ?,
                		`emailaddress` = ?, `contactfirstname` = ?,`contactlastname` = ?, `taxaddress` = ?, `usedefaultipaccesslist` = ?, `ipaccesslist` = ?  WHERE `code` = ?'))
                {
                    if ($stmt->bind_param('ssssssssssssssssssiss', $companyName, $address1, $address2, $address3, $address4,$city, $county, $state, $regioncode,$region, 
                    	$postCode, $countryCode, $countryName, $telephone, $email, $firstName, $lastName, $taxAddress, $useDefaultIpAddressList, $ipAccessList, $companyCode))
                    {
                        if ($stmt->execute())
                        {
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 
                                'ADMIN', 'COMPANY-UPDATE', $companyID . ' ' . $companyCode, 1);
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'companyEdit execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'companyEdit bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
	                $stmt->close();
	                $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'companyEdit prepare ' . $dbObj->error;
                }
                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'companyEdit connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['id'] = $companyID;
        $resultArray['code'] = $companyCode;
        $resultArray['name'] = $companyName;

        return $resultArray;
    }
}
?>
