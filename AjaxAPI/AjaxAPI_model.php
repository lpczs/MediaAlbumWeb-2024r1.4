<?php
require_once('../Order/Order_model.php');
require_once('../Utils/UtilsAddress.php');


class AjaxAPI_model
{
    static function extJsAddressForm($adminMode = false)
    {
        global $gConstants;

        $resultArray = Array();
        $fieldDefinitionArray = Array();
        $displayFields = Array();
        $fieldLabels = Array();
        $fieldLabelsLocalized = Array();
        $compulsoryList = Array();
        $allowBlanckFields = Array();
        $region = '';
        $countryName = '';

        $smarty = SmartyObj::newSmarty('AjaxAPI', '', '', '', false, false);

        $excludeFields = $_GET['excludeFields'];
        $strictMode = $_GET['strict']; // enforce compulsory fields? 1 - yes, 0 - no
        $editmode = ($_GET['editMode'] == '1') ? true : false;  // edit contact details only? 1 - yes, 0 - no

        $countryCode = $_GET['countryCode'];

        // use default country if none specified
        if ($countryCode == '')
        {
            $countryCode = $gConstants['homecountrycode'];
        }

        //****************** PREPOPULATE LISTS **************
        $countryList = UtilsAddressObj::getCountryList();

        if ($editmode == '0')
        {
            $regionList = UtilsAddressObj::getRegionList($countryCode);
        }
        else
        {
            $regionList = Array();
        }

        for ($i = 0; $i < count($countryList); $i++)
        {
            if ($countryList[$i]['isocode2'] == $countryCode)
            {
                $region = $countryList[$i]['region'];
                $countryName = $countryList[$i]['name'];
                $fields = $countryList[$i]['displayfields'];

                if ($fields != '')
                {
                    $tmpDisplayFields = explode('<p>', $fields);
                    $displayFields = Array();
                    foreach ($tmpDisplayFields as $field)
                    {

                        $displayFieldConfig = substr($field, 0, 2);

                        if ($displayFieldConfig == '*b')
                        {
                            $field = substr($field, 2);
                        }

                        if (strpos($field, '=') !== false)
                        {
                            // this is for lines like "add1=[add4.1], [add4.2] - [add4.3]"
                            list($head, $tail) = explode('=', $field, 2);

                            // here we are only interested in the tail
                            // extract tags by removing everything between tags
                            $tmpField = '';
                            $tag = false;
                            for ($j = 0; $j < strlen($tail); $j++)
                            {
                                if ($tail[$j] == '[')
                                {
                                    $tag = true;
                                }
                                if ($tag)
                                {
                                    $tmpField .= $tail[$j];
                                }
                                if ($tail[$j] == ']')
                                {
                                    $tag = false;
                                }
                            }
                            // in the above example, we are now left with "[add41][add42][add43]"
                            $tmpField = str_replace('][', ',', $tmpField);
                            $tmpField = str_replace('[', '', $tmpField);
                            $tmpField = str_replace(']', '', $tmpField);
                            // in the above example, we are now left with "add41,add42,add43"
                            $tmpFields = explode(',', $tmpField);
                            foreach ($tmpFields as $item)
                            {
                                $displayFields[] = $item;
                            }
                        }
                        else
                        {
                            $displayFields[] = $field;
                        }
                    }

                    $fieldLabels = explode(',', $countryList[$i]['fieldlabels']);

                    if ($strictMode == 1 && $adminMode == 0)
                    {
                        $compulsoryList = explode(',', $countryList[$i]['compulsoryfields'] . ',country,firstname,lastname'); // last three are always compulsory
                    }
                }
                else
                {
                    $displayFields = explode(',', 'country,firstname,lastname,company,add1,add2,add3,add4,city,county,state,postcode');
                    $fieldLabels = explode(',',
                            'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelAddressLine3,str_LabelAddressLine4,str_LabelTownCity,str_LabelCounty,str_LabelState,str_LabelPostCode');
                    if ($strictMode == 1 && $adminMode == 0)
                    {
                        $compulsoryList = explode(',', 'country,firstname,lastname,add1,city,postcode');
                    }
                }
                break;
            }
        }

        //****************** LABELS **************
        $smarty = SmartyObj::newSmarty('AjaxAPI', '', '', '', false, false);

        for ($i = 0; $i < count($fieldLabels); $i++)
        {
            $fieldLabelsLocalized[$displayFields[$i]] = $smarty->get_config_vars($fieldLabels[$i]);
        }

        //If we're an admin only 'country,firstname,lastname' are compulsory
        if ($adminMode)
        {
            $compulsoryList = explode(',', 'country,firstname,lastname');
        }

        //****************** COMPALSORY FIELDS **************
        for ($i = 0; $i < count($displayFields); $i++)
        {
            $allowBlanckFields[$displayFields[$i]] = (in_array($displayFields[$i], $compulsoryList)) ? 'false' : 'true';
        }

        //****************** FORM FIELDS TEMPLATES **************
        $fieldDefinitionArray['firstname'] = array('name' => 'maincontactfname', 'value' => '', 'type' => 'txt', 'readonly' => false, 'fieldlabel' => $fieldLabelsLocalized['firstname'], 'allowblank' => $allowBlanckFields['firstname']);
        $fieldDefinitionArray['lastname'] = array('name' => 'maincontactlname', 'value' => '', 'type' => 'txt', 'readonly' => false, 'fieldlabel' => $fieldLabelsLocalized['lastname'], 'allowblank' => $allowBlanckFields['lastname']);
        $fieldDefinitionArray['company'] = array('name' => 'maincompanyname', 'value' => '', 'type' => 'txt', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['company'], 'allowblank' => $allowBlanckFields['company']);
        if (isset($fieldLabelsLocalized['add1']) && isset($allowBlanckFields['add1']))
        {
            $fieldDefinitionArray['add1'] = array('name' => 'mainaddress1', 'value' => '', 'type' => 'txt', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['add1'], 'allowblank' => $allowBlanckFields['add1']);
        }
        if (isset($fieldLabelsLocalized['add2']) && isset($allowBlanckFields['add2']))
        {
            $fieldDefinitionArray['add2'] = array('name' => 'mainaddress2', 'value' => '', 'type' => 'txt', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['add2'], 'allowblank' => $allowBlanckFields['add2']);
        }
        if (isset($fieldLabelsLocalized['add3']) && isset($allowBlanckFields['add3']))
        {
            $fieldDefinitionArray['add3'] = array('name' => 'mainaddress3', 'value' => '', 'type' => 'txt', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['add3'], 'allowblank' => $allowBlanckFields['add3']);
        }
        if (isset($fieldLabelsLocalized['add4']) && isset($allowBlanckFields['add4']))
        {
            $fieldDefinitionArray['add4'] = array('name' => 'mainaddress4', 'value' => '', 'type' => 'txt', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['add4'], 'allowblank' => $allowBlanckFields['add4']);
        }
        if (isset($fieldLabelsLocalized['add41']) && isset($allowBlanckFields['add41']))
        {
            $fieldDefinitionArray['add41'] = array('name' => 'mainadd41', 'value' => '', 'type' => 'txt', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['add41'], 'allowblank' => $allowBlanckFields['add41']);
        }
        if (isset($fieldLabelsLocalized['add42']) && isset($allowBlanckFields['add42']))
        {
            $fieldDefinitionArray['add42'] = array('name' => 'mainadd42', 'value' => '', 'type' => 'txt', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['add42'], 'allowblank' => $allowBlanckFields['add42']);
        }
        if (isset($fieldLabelsLocalized['add43']) && isset($allowBlanckFields['add43']))
        {
            $fieldDefinitionArray['add43'] = array('name' => 'mainadd43', 'value' => '', 'type' => 'txt', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['add43'], 'allowblank' => $allowBlanckFields['add43']);
        }
        if (isset($fieldLabelsLocalized['city']) && isset($allowBlanckFields['city']))
        {
            $fieldDefinitionArray['city'] = array('name' => 'maincity', 'value' => '', 'type' => 'txt', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['city'], 'allowblank' => $allowBlanckFields['city']);
        }
        if (isset($fieldLabelsLocalized['postcode']) && isset($allowBlanckFields['postcode']))
        {
            $fieldDefinitionArray['postcode'] = array('name' => 'mainpostcode', 'value' => '', 'type' => 'txt', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['postcode'], 'allowblank' => $allowBlanckFields['postcode']);
        }
        if (isset($fieldLabelsLocalized['county']) && isset($allowBlanckFields['county']))
        {
            if (($region != 'COUNTY') || (count($regionList) <= 0))
            {
                $fieldDefinitionArray['county'] = array('name' => 'maincounty', 'value' => '', 'type' => 'txt', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['county'], 'allowblank' => $allowBlanckFields['county']);
            }
            else
            {
                $fieldDefinitionArray['county'] = array('name' => 'countylist', 'value' => $regionList, 'type' => 'select', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['county'], 'keyCode' => 'code', 'valueCode' => 'name', 'allowblank' => $allowBlanckFields['county']);
            }
        }
        if (isset($fieldLabelsLocalized['state']) && isset($allowBlanckFields['state']))
        {
            if (($region != 'STATE') || (count($regionList) <= 0))
            {
                $fieldDefinitionArray['state'] = array('name' => 'mainstate', 'value' => '', 'type' => 'txt', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['state'], 'allowblank' => $allowBlanckFields['state']);
            }
            else
            {
                $fieldDefinitionArray['state'] = array('name' => 'statelist', 'value' => $regionList, 'type' => 'select', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['state'], 'keyCode' => 'code', 'valueCode' => 'name', 'allowblank' => $allowBlanckFields['state']);
            }
        }
        if (isset($fieldLabelsLocalized['country']) && isset($allowBlanckFields['country']))
        {
            if ($editmode == '0')
            {
                $fieldDefinitionArray['country'] = array('name' => 'countrylist', 'value' => $countryList, 'type' => 'select', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['country'], 'keyCode' => 'isocode2', 'valueCode' => 'name', 'allowblank' => $allowBlanckFields['country']);
            }
            else
            {
                $fieldDefinitionArray['country'] = array('name' => 'country', 'value' => '', 'type' => 'txt', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['country'], 'allowblank' => $allowBlanckFields['country']);
            }
        }

        if (isset($fieldLabelsLocalized['regtaxnum']) && isset($allowBlanckFields['regtaxnum']))
        {
            $fieldDefinitionArray['regtaxnum'] = array('name' => 'regtaxnum', 'value' => '', 'type' => 'txt', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['regtaxnum'], 'allowblank' => $allowBlanckFields['regtaxnum']);
        }

        if (isset($fieldLabelsLocalized['regtaxnumtype']) && isset($allowBlanckFields['regtaxnumtype']))
        {
            $registeredTaxNumberTypesArray = array
                (
                array('id' => TPX_REGISTEREDTAXNUMBERTYPE_NA, 'name' => $smarty->get_config_vars('str_LabelMakeSelection')),
                array('id' => TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL, 'name' => $smarty->get_config_vars('str_LabelCustomerTaxNumberTypePersonal')),
                array('id' => TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE, 'name' => $smarty->get_config_vars('str_LabelCustomerTaxNumberTypeCorporate'))
            );

            $fieldDefinitionArray['regtaxnumtype'] = array('name' => 'regtaxnumtype', 'value' => $registeredTaxNumberTypesArray, 'type' => 'select', 'readonly' => $editmode, 'fieldlabel' => $fieldLabelsLocalized['regtaxnumtype'], 'keyCode' => 'id', 'valueCode' => 'name', 'allowblank' => $allowBlanckFields['regtaxnumtype']);
        }

        for ($i = 0; $i < count($displayFields); $i++)
        {
            $resultArray[$displayFields[$i]] = $fieldDefinitionArray[$displayFields[$i]];
        }

        //****************** DELETE FIRST AND LAST NAME FIELDS **************
        $fieldArray = explode(',', $excludeFields);
        foreach ($fieldArray as $value)
        {
            unset($resultArray[$value]);
        }

        return $resultArray;
    }

    static function extJsAddressVerification()
    {
        global $gSession;

        $addCountryCode = $_GET['country'];
        $addStateCode = $_GET['statecode'];
        $addCounty = $_GET['county'];
        $addCity = $_GET['city'];
        $addPostCode = $_GET['postcode'];
        $result = '';
        $companyCode = $gSession['userdata']['companycode'];

        if ($_GET['region'] == 'STATE')
        {
            $taxZoneData = DatabaseObj::getTaxZoneDataFromRegion($companyCode, $_GET['country'], $_GET['statecode']);
        }
        else
        {
            $taxZoneData = DatabaseObj::getTaxZoneDataFromRegion($companyCode, $_GET['country'], $_GET['county']);
        }

        // only load Tax calculation script is present.
        if (file_exists("../Customise/scripts/EDL_TaopixCustomerAccountAPI.php"))
        {
            require_once('../Customise/scripts/EDL_TaopixCustomerAccountAPI.php');
        }

        if (method_exists('CustomerAccountAPI', 'verifyAddress'))
        {
            $licenseKeyCode = isset($gSession['licensekeydata']['groupcode']) ? $gSession['licensekeydata']['groupcode'] : '';
            $brandCode = isset($gSession['webbrandcode']) ? $gSession['webbrandcode'] : '';

			$paramArray = array();
			$paramArray['brandcode'] = $brandCode;
			$paramArray['groupcode'] = $licenseKeyCode;
			$paramArray['groupdata'] = $gSession['licensekeydata']['groupdata'];
			$paramArray['browserlanguagecode'] = $gSession['browserlanguagecode'];
			$paramArray['addresstype'] = 'billing';
			$paramArray['address']['countrycode'] = $addCountryCode;
			$paramArray['address']['statecode'] = $addStateCode;
			$paramArray['address']['county'] = $addCounty;
			$paramArray['address']['city'] = $addCity;
			$paramArray['address']['postcode'] = $addPostCode;

            $verifyAddressResult = CustomerAccountAPI::verifyAddress($pParamArray);

            $resultArray = $verifyAddressResult['invalidaddressfields'];
        }

		if (count($resultArray) > 0)
		{
			$result = '{';

			foreach ($resultArray as $key => $value)
			{
				$result .= '"' . $key . '":"' . $value . '",';
			}

			$result = substr($result, 0, -1);
			$result .= '}';
		}

        if ($result == '')
        {
            echo '0';
        }
		else
		{
			echo '1';
		}

        return;
    }

    static function countryPanel()
    {
        $isGlobal = isset($_GET['isGlobal']) ? $_GET['isGlobal'] : false;
        $requestType = isset($_GET['requestType']) ? $_GET['requestType'] : '';
        $requestParam = isset($_GET['requestParam']) ? $_GET['requestParam'] : '';
        $requestMode = isset($_GET['requestMode']) ? $_GET['requestMode'] : ''; // add or edit
        $companyCode = isset($_GET['companyCode']) ? $_GET['companyCode'] : '';

        $combinedList = UtilsAddressObj::getCombinedCountryRegionList(true);

        $countryCodes = '';

        if (($requestType != '') && ($requestMode != ''))
        {
            switch ($requestType)
            {
                case 'SHIPPINGZONES':
                    if ($requestMode == 'EDIT')
                    {
                        $dbObj = DatabaseObj::getGlobalDBConnection();
                        if ($dbObj)
                        {
                            $stmt = $dbObj->prepare('SELECT `countrycodes`
                                                        FROM `SHIPPINGZONES`
                                                        WHERE `id` = ?
                                                            ' . (($companyCode != '') ? '
                                                            AND `companycode` = ?' : '
                                                            AND (`companycode` = ? OR `companycode` IS NULL)'));
                            if ($stmt)
                            {
                                if ($stmt->bind_param('is', $requestParam, $companyCode))
                                {
                                    if ($stmt->execute())
                                    {
                                        if ($stmt->store_result())
                                        {
                                            if ($stmt->num_rows > 0)
                                            {
                                                if ($stmt->bind_result($countryCodes))
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
                    }
                    $countryCodesUsed = $countryCodes;
                    break;

                case 'TAXZONES':
                    if ($requestMode == 'EDIT')
                    {
                        $dbObj = DatabaseObj::getGlobalDBConnection();
                        if ($dbObj)
                        {
                            $stmt = $dbObj->prepare('SELECT `countrycodes`
                                                        FROM `TAXZONES`
                                                        WHERE `id` = ?
                                                            ' . (($companyCode != '') ? '
                                                            AND `companycode` = ?' : '
                                                            AND (`companycode` = ? OR `companycode` IS NULL)'));
                            if ($stmt)
                            {
                                if ($stmt->bind_param('is', $requestParam, $companyCode))
                                {
                                    if ($stmt->execute())
                                    {
                                        if ($stmt->store_result())
                                        {
                                            if ($stmt->num_rows > 0)
                                            {
                                                if ($stmt->bind_result($countryCodes))
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

                            if ($isGlobal)
                            {
                                $stmt = $dbObj->prepare('SELECT GROUP_CONCAT(`countrycodes`) as codes
                                                            FROM `TAXZONES`
                                                            WHERE' . (($companyCode != '') ? ' `companycode` = ?' : ' (`companycode` = ? OR `companycode` IS NULL)'));
                                if ($stmt)
                                {
                                    if ($stmt->bind_param('s', $companyCode))
                                    {
                                        if ($stmt->execute())
                                        {
                                            if ($stmt->store_result())
                                            {
                                                if ($stmt->num_rows > 0)
                                                {
                                                    if ($stmt->bind_result($countryCodesUsed))
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
                            else
                            {
                                $countryCodesUsed = $countryCodes;
                            }
                            $dbObj->close();
                        }
                    }
                    else
                    {
                        $dbObj = DatabaseObj::getGlobalDBConnection();
                        if ($dbObj)
                        {
                            $stmt = $dbObj->prepare('SELECT GROUP_CONCAT(`countrycodes`) as codes
                                                        FROM `TAXZONES`
                                                        WHERE' . (($companyCode != '') ? ' `companycode` = ?' : ' (`companycode` = ? OR `companycode` IS NULL)'));
                            if ($stmt)
                            {
                                if ($stmt->bind_param('s', $companyCode))
                                {
                                    if ($stmt->execute())
                                    {
                                        if ($stmt->store_result())
                                        {
                                            if ($stmt->num_rows > 0)
                                            {
                                                if ($stmt->bind_result($countryCodesUsed))
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
                    }
                    break;
            }
        }

        $usedInRecord = $countryCodes;
        $countryList = array();
        $theCodeList = explode(',', $countryCodes);
        sort($theCodeList);

        $countryCodes = '';
        $countryNames = '';

        $codeCount = count($theCodeList);
        for ($i = 0; $i < $codeCount; $i++)
        {
            $theCode = $theCodeList[$i];
            $itemCount = count($combinedList);
            for ($j = 0; $j < $itemCount; $j++)
            {
                if ($combinedList[$j]['zonecode'] == $theCode)
                {
                    if ($combinedList[$j]['countrycode'] == $combinedList[$j]['zonecode'])
                    {
                        $countryCodes = $combinedList[$j]['zonecode'];
                        $countryNames = $combinedList[$j]['countryname'];
                    }
                    else
                    {
                        $countryCodes = $combinedList[$j]['zonecode'];
                        $countryNames = $combinedList[$j]['countryname'] . '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $combinedList[$j]['regionname'];
                    }
                    $countryList[] = '["' . $countryCodes . '","' . $countryNames . '"]';
                    break;
                }
            }
        }

        $removedCountries = UtilsAddressObj::getUsedCountryList($combinedList, $countryCodesUsed, false);
        $notIncludeAll = array();

        $countryCodesUsedArray = explode(',', $countryCodesUsed);
        for ($i = 0; $i < count($countryCodesUsedArray); $i++)
        {
            if ((strrpos($countryCodesUsedArray[$i], "_") === false) && ($countryCodesUsedArray[$i] != ''))
            {
                $notIncludeAll[] = $countryCodesUsedArray[$i];
            }
        }

        echo '[[' . join(',', $countryList) . '],"' . join(',', $removedCountries) . '","' . join(',', $notIncludeAll) . '", "' . $usedInRecord . '"]';
    }

    static function addressForm()
    {
        // return address form in country format
        // use GET
        // see if session ref is valid, reject otherwise
        // if no country-specific format can be found, use default settings
        // turn county OR state into drop-down if regions are defined in DB table COUNTRYREGION
        // usage example: ?fsaction=AjaxAPI.callback&cmd=ADDRESSFORM&ref=112&country=GB&strict=1&edit=0
        // ref  - session reference
        // country - country code
        // strict   - enforce compulsory fields
        // edit     - edit contact details only

        global $gSession;

        $result = '';
        $countryName = '';
        $region = '';
        $tableWidth = '';
        $addressForm = Array();
        $displayFields = Array();
        $excludeFieldsArray = Array();
        $hideConfigFields = 0;
        $FromHighLevelRegister = 0;
        $addressType = 'billing';

        if (array_key_exists('hideconfigfields', $_GET))
        {
            $hideConfigFields = (int) $_GET['hideconfigfields'];
        }

        if (array_key_exists('ishighlevel', $_GET))
        {
            $FromHighLevelRegister = $_GET['ishighlevel'];
        }

        if (array_key_exists('addresstype', $_GET))
        {
            $addressType = $_GET['addresstype'];
        }

        if (($gSession['ref'] > 0) || ($FromHighLevelRegister == 1))
        {
            if (array_key_exists('country', $_GET))
            {
                $countryCode = $_GET['country'];

                if (array_key_exists('strict', $_GET)) // enforce compulsory fields? 1 - yes, 0 - no
                {
                    $strictMode = $_GET['strict'];
                }
                else
                {
                    $strictMode = '1'; // default
                }

                if (array_key_exists('edit', $_GET)) // edit contact details only? 1 - yes, 0 - no
                {
                    $editMode = $_GET['edit'];
                }
                else
                {
                    $editMode = '0'; // default
                }

                if (array_key_exists('tablewidth', $_GET))
                {
                    $tableWidth = $_GET['tablewidth'];
                }
                else
                {
                    $tableWidth = '650'; // default
                }

                $countryList = UtilsAddressObj::getCountryList();
                if ($editMode == '0')
                {
                    $regionList = UtilsAddressObj::getRegionList($countryCode);
                }
                else
                {
                    $regionList = Array();
                }

                // loop to find OUR country
                $itemCount = count($countryList);
                for ($itemIndex = 0; $itemIndex < $itemCount; $itemIndex++)
                {
                    if ($countryList[$itemIndex]['isocode2'] == $countryCode)
                    {
                        $region = $countryList[$itemIndex]['region'];
                        $countryName = $countryList[$itemIndex]['name'];
                        $fields = $countryList[$itemIndex]['displayfields'];
                        if ($fields <> '')
                        {
                            $tmpDisplayFields = explode('<p>', $fields);
                            $displayFields = Array();
                            foreach ($tmpDisplayFields as $field)
                            {
                                if (strpos($field, '=') !== false)
                                {
                                    // this is for lines like "add1=[add4.1], [add4.2] - [add4.3]"
                                    list($head, $tail) = explode('=', $field, 2);

                                    // here we are only interested in the tail
                                    // extract tags by removing everything between tags
                                    $tmpField = '';
                                    $tag = false;
                                    for ($i = 0; $i < strlen($tail); $i++)
                                    {
                                        if ($tail[$i] == '[')
                                        {
                                            $tag = true;
                                        }
                                        if ($tag)
                                        {
                                            $tmpField .= $tail[$i];
                                        }
                                        if ($tail[$i] == ']')
                                        {
                                            $tag = false;
                                        }
                                    }
                                    // in the above example, we are now left with "[add41][add42][add43]"
                                    $tmpField = str_replace('][', ',', $tmpField);
                                    $tmpField = str_replace('[', '', $tmpField);
                                    $tmpField = str_replace(']', '', $tmpField);
                                    // in the above example, we are now left with "add41,add42,add43"
                                    $tmpFields = explode(',', $tmpField);
                                    foreach ($tmpFields as $item)
                                    {
                                        $displayFields[] = $item;
                                    }
                                }
                                else
                                {
                                    $displayFields[] = $field;
                                }
                            }

                            $fieldLabels = explode(',', $countryList[$itemIndex]['fieldlabels']);
                            $compulsoryFields = $countryList[$itemIndex]['compulsoryfields'] . ',country,firstname,lastname'; // last three are always compulsory
                        }
                    }
                }

                // prepare address fields in the way they are shown
                $itemCount = count($displayFields);

                // take default settings if not specified
                if ($itemCount == 0)
                {
                    $displayFields = explode(',', 'country,firstname,lastname,company,add1,add2,add3,add4,city,county,state,postcode');
                    $compulsoryFields = 'country,firstname,lastname,add1,city,postcode';
                    $fieldLabels = explode(',',
                            'str_LabelCountry,str_LabelFirstName,str_LabelLastName,str_LabelCompanyName,str_LabelAddressLine1,str_LabelAddressLine2,str_LabelAddressLine3,str_LabelAddressLine4,str_LabelTownCity,str_LabelCounty,str_LabelState,str_LabelPostCode');
                    $itemCount = count($displayFields);
                }

                // only load Tax calculation script is present.
                if (file_exists("../Customise/scripts/EDL_TaopixCustomerAccountAPI.php"))
                {
                    require_once('../Customise/scripts/EDL_TaopixCustomerAccountAPI.php');
                }

                if (method_exists('CustomerAccountAPI', 'configureAddressForm'))
                {
                    $paramArray = array();

                    foreach ($displayFields as $key => $addressField)
                    {
                    	$isCompulsory = true;

                    	if (strpos($compulsoryFields, $addressField) === false)
                    	{
                    		$isCompulsory = false;
                    	}

                    	$addressFields[$addressField]['compulsory'] = $isCompulsory;
                    }

                    $paramArray['brandcode'] = $gSession['webbrandcode'];
            		$paramArray['groupcode'] = $gSession['licensekeydata']['groupcode'];
            		$paramArray['groupdata'] = $gSession['licensekeydata']['groupdata'];
            		$paramArray['browserlanguagecode'] = $gSession['browserlanguagecode'];
           			$paramArray['addresstype'] = $addressType;
            		$paramArray['countrycode'] = $countryCode;
            		$paramArray['addressfields'] = $addressFields;

                    $configureAddressFields = CustomerAccountAPI::configureAddressForm($paramArray);

					foreach ($configureAddressFields as $addressField => $addressFieldConfigArray)
					{
						if ($addressFieldConfigArray['compulsory'])
						{
							if (strpos($compulsoryFields, $addressField) === false)
                            {
                                $compulsoryFields .= $addressField . ',';
                            }
						}
					}

					$compulsoryFields = substr($compulsoryFields, 0, -1);
                }

                for ($itemIndex = 0; $itemIndex < $itemCount; $itemIndex++)
                {
                    $includeAddressField = true;

                    $displayFieldConfig = substr($displayFields[$itemIndex], 0, 2);

                    if (($displayFieldConfig == '*b') && ($hideConfigFields == 1))
                    {
                        $displayFields[$itemIndex] = substr($displayFields[$itemIndex], 2);
                        $excludeFieldsArray[] = $displayFields[$itemIndex];
                    }
                    elseif ($displayFieldConfig == '*b')
                    {
                        $displayFields[$itemIndex] = substr($displayFields[$itemIndex], 2);
                    }

                    // exclude fields from the address form
                    if (count($excludeFieldsArray) > 0)
                    {
                        // if the address field name appears in the exclude fields array then do not include it in the address form
                        if (in_array($displayFields[$itemIndex], $excludeFieldsArray))
                        {
                            $includeAddressField = false;
                        }
                    }

                    if ($includeAddressField)
                    {
                        $addressItem['name'] = $displayFields[$itemIndex];
                        $addressItem['label'] = $fieldLabels[$itemIndex];

                        if ($strictMode == '1')
                        {
                            // check if field is compulsory
                            if (strpos($compulsoryFields, $addressItem['name']) !== false)
                            {
                                $addressItem['compulsory'] = '1';
                            }
                            else
                            {
                                $addressItem['compulsory'] = '0';
                            }
                        }
                        else
                        {
                            $addressItem['compulsory'] = '0';
                        }

                        array_push($addressForm, $addressItem);
                    }
                }

                $result = 'ADDRESSFORM';
                $resultArray['addressform'] = $addressForm;
                $resultArray['countrycode'] = $countryCode;
                $resultArray['countryname'] = $countryName;
                $resultArray['countrylist'] = $countryList;
                $resultArray['regionlist'] = $regionList;
                $resultArray['region'] = $region;
                $resultArray['editmode'] = $editMode;
                $resultArray['tablewidth'] = $tableWidth;
            }
            else
            {
                $result = 'ERROR';
                $resultArray['resultparam'] = 'Country code missing.';
            }
        }
        else
        {
            $result = 'ERROR';
            $resultArray['resultparam'] = 'Invalid session.';
        }

        $resultArray['result'] = $result;

        return $resultArray;
    }

    /* NEW shippingRegion function */

    static function ExtJsShippingRegion()
    {
        $resultArray = array();
        $countryCode = $_GET['country']; // current country

        $completeRegionList = UtilsAddressObj::getRegionList($countryCode);

        // get label for region
        $countryDetails = UtilsAddressObj::getCountry($countryCode);
        $region = strtolower(trim($countryDetails['region']));

        $resultArray['countryCode'] = $countryCode;
        $resultArray['label'] = $region;
        $resultArray['regions'] = $completeRegionList;

        return $resultArray;
    }

    static function emailTest()
    {
        // include the email creation module
        require_once('../Utils/UtilsEmail.php');

        global $gSession;

        $serverDetails = Array();

        $smtpAddress = $_POST['ssa'];
        $smtpPort = $_POST['ssp'];
        $smtpAuth = $_POST['saut'];
        $smtpUsername = $_POST['sau'];
        $smtpPassword = $_POST['sap'];
        $smtpType = $_POST['sst'];
		$smtpProvider = $_POST['ssop'];
		$smtpProviderToken = $_POST['sspt'];
        $smtpFromName = UtilsObj::FormatEmailNameSettings($_POST['sfn']);
        $smtpFromAddress = UtilsObj::FormatEmailSettings($_POST['sfa']);
        $smtpReplyName = UtilsObj::FormatEmailNameSettings($_POST['srn']);
        $smtpReplyAddress = UtilsObj::FormatEmailSettings($_POST['sra']);
        $sectionName = UtilsObj::FormatEmailNameSettings($_POST['sn']);
        $sectionAddress = UtilsObj::FormatEmailSettings($_POST['sa']);

        // overwriting emails settings from constants or brand
        $serverDetails['smtpaddress'] = $smtpAddress;
        $serverDetails['smtpport'] = $smtpPort;
        $serverDetails['smtpsystemfromname'] = $smtpFromName;
        $serverDetails['smtpsystemfromaddress'] = $smtpFromAddress;
        $serverDetails['smtpsystemreplytoname'] = $smtpReplyName;
        $serverDetails['smtpsystemreplytoaddress'] = $smtpReplyAddress;
        $serverDetails['smtpauth'] = $smtpAuth;
        $serverDetails['smtpauthusername'] = $smtpUsername;
        $serverDetails['smtpauthpassword'] = $smtpPassword;
        $serverDetails['smtptype'] = $smtpType;
		$serverDetails['oauthprovider'] = $smtpProvider;
		$serverDetails['oauthtoken'] = $smtpProviderToken;

        $language = $gSession['browserlanguagecode'];
		$brandCode = $_POST['bc'];
        $brandAppName = $_POST['applicationname'];
        $brandDisplayUrl = $_POST['displayurl'];

        $emailNameBCC = "";
        $emailAddressBCC = "";

        $emailObj = new TaopixMailer();
        $result = $emailObj->sendTemplateTestEmail('admin_emailtest', $brandCode, $brandAppName, $brandDisplayUrl, $language, $sectionName,
                $sectionAddress, $emailNameBCC, $emailAddressBCC, 0, Array('section' => $sectionName), $serverDetails);

        return $result;
    }

    static function addressVerification()
    {
        global $gSession;

        $addCountryCode = $_GET['country'];
        $addStateCode = $_GET['statecode'];
        $addCounty = $_GET['county'];
        $addCity = $_GET['city'];
        $addPostCode = $_GET['postcode'];
        $addressType = $_GET['addresstype'];
        $result = '';
        $companyCode = $gSession['userdata']['companycode'];

        if ($_GET['region'] == 'STATE')
        {
            $taxZoneData = DatabaseObj::getTaxZoneDataFromRegion($companyCode, $_GET['country'], $_GET['statecode']);
        }
        else
        {
            $taxZoneData = DatabaseObj::getTaxZoneDataFromRegion($companyCode, $_GET['country'], $_GET['county']);
        }

        // only load Tax calculation script is present.
        if (file_exists("../Customise/scripts/EDL_TaopixCustomerAccountAPI.php"))
        {
            require_once('../Customise/scripts/EDL_TaopixCustomerAccountAPI.php');
        }

        if (method_exists('CustomerAccountAPI', 'verifyAddress'))
        {
            $licenseKeyCode = isset($gSession['licensekeydata']['groupcode']) ? $gSession['licensekeydata']['groupcode'] : '';
            $brandCode = isset($gSession['webbrandcode']) ? $gSession['webbrandcode'] : '';

			$paramArray = array();
			$paramArray['brandcode'] = $brandCode;
			$paramArray['groupcode'] = $licenseKeyCode;
			$paramArray['groupdata'] = $gSession['licensekeydata']['groupdata'];;
			$paramArray['browserlanguagecode'] = $gSession['browserlanguagecode'];
			$paramArray['addresstype'] = $addressType;
			$paramArray['address']['countrycode'] = $addCountryCode;
			$paramArray['address']['statecode'] = $addStateCode;
			$paramArray['address']['county'] = $addCounty;
			$paramArray['address']['city'] = $addCity;
			$paramArray['address']['postcode'] = $addPostCode;

            $verifyAddressResult = CustomerAccountAPI::verifyAddress($paramArray);

            $resultArray = $verifyAddressResult['invalidaddressfields'];
        }

		if (count($resultArray) > 0)
		{
			$result = '{';

			foreach ($resultArray as $key => $value)
			{
				$result .= '"' . $key . '":"' . $value . '",';
			}

			$result = substr($result, 0, -1);
			$result .= '}';
		}

        if ($result == '')
        {
            $result = 'match'; // never return empty string in Ajax, it breaks Safari 1
        }

        return $result;
    }

// end function

    static function autoSuggest()
    {
        global $gSession;

        $addressField = $_GET['field'];
        $countryCode = $_GET['country'];
        $stateCode = $_GET['statecode'];
        $input = $_GET['input'];
        $addressType = $_GET['addresstype'];
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 0;

        $result = Array();

        // only load Tax calculation script is present.
        if (file_exists("../Customise/scripts/EDL_TaopixCustomerAccountAPI.php"))
        {
            require_once('../Customise/scripts/EDL_TaopixCustomerAccountAPI.php');
        }

        if (method_exists('CustomerAccountAPI', 'autoSuggest'))
        {
            $paramArray = array();
            $paramArray['brandcode'] = $gSession['webbrandcode'];
            $paramArray['groupcode'] = $gSession['licensekeydata']['groupcode'];
            $paramArray['groupdata'] = $gSession['licensekeydata']['groupdata'];
            $paramArray['browserlanguagecode'] = $gSession['browserlanguagecode'];
            $paramArray['addresstype'] = $addressType;
            $paramArray['address']['countrycode'] = $countryCode;
            $paramArray['address']['statecode'] = $stateCode;
            $paramArray['addressfield'] = $addressField;
            $paramArray['addressfielduserinput'] = $input;
            $paramArray['limit'] = $limit;

            $result = CustomerAccountAPI::autoSuggest($paramArray);
        }

        return $result;
    }

    static function productionSitesComboStore()
    {
        global $gSession;

        // return an array containing the a list of production sites
        $siteNamesList = Array();
        $dbObj = DatabaseObj::getGlobalDBConnection();
        $smarty = SmartyObj::newSmarty('', '', '');
        $siteAdminHandle = UtilsObj::getPOSTParam('siteAdmin');

        if (($siteAdminHandle == 1) || ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN))
        {
            $siteAdminUser = true;
        }
        else
        {
            $siteAdminUser = false;
        }

        if ($dbObj)
        {
            switch ($gSession['userdata']['usertype'])
            {
                case TPX_LOGIN_SYSTEM_ADMIN:
                    // getting production sites for the system administrator
                    $stmt = $dbObj->prepare('SELECT `id`, `code`, `name` FROM `SITES` WHERE (`productionsitekey` <> "") ORDER BY `code`');
                    $bindOK = true;
                    break;
                case TPX_LOGIN_COMPANY_ADMIN:
                    // getting production sites comboBox based on companycode of company administrator
                    $stmt = $dbObj->prepare('SELECT `id`, `code`, `name` FROM `SITES` WHERE `companycode` = ? ORDER BY `code`');
                    $bindOK = $stmt->bind_param('s', $gSession['userdata']['companycode']);
                    break;
                case TPX_LOGIN_SITE_ADMIN:
                    // getting production sites comboBox based on companycode of company administrator
                    $stmt = $dbObj->prepare('SELECT `id`, `code`, `name` FROM `SITES` WHERE `code` = ? ORDER BY `code`');
                    $bindOK = $stmt->bind_param('s', $gSession['userdata']['userowner']);
                    break;
            }

            if ($stmt)
            {
                if ($bindOK)
                {
                    if ($stmt->bind_result($id, $code, $companyName))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
                                $siteItem['id'] = $code;
                                $siteItem['name'] = $code . ' - ' . $companyName;
                                $siteItem['code'] = $code;
                                array_push($siteNamesList, $siteItem);
                            }
                        }
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = NULL;
                }
            }
            $dbObj->close();
        }

        if ($siteAdminUser == false)
        {
            array_unshift($siteNamesList, Array('id' => '', 'name' => $smarty->get_config_vars('str_LabelNone'), 'code' => ''));
        }

        $resultArray = $siteNamesList;

        return $resultArray;
    }

    static function companiesComboStore()
    {
        global $gSession;
        $smarty = SmartyObj::newSmarty('');

        // return an array containing the a list of production sites
        $companyList = Array();

        $includeGlobal = $_GET['includeGlobal'];
        $includeShowAll = $_GET['includeShowAll'];
        $includeGlobalAndSpecificCompany = $_GET['includeGlobalAndSpecificCompany'];
		$allLabel = !empty($_GET['allLabel']) ? $_GET['allLabel'] : null;

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            switch ($gSession['userdata']['usertype'])
            {
                case TPX_LOGIN_SYSTEM_ADMIN:

                    // this is a check to see if we are trying to create a price as a system administrator for a product
                    // if the product belongs to a company and we are trying to save a price list for a component that is global then the system admin should
                    // be able to create a pricelist for either global or the products company.

                    if ($includeGlobalAndSpecificCompany != '')
                    {
                        // we only want to display a specific company in the list
                        $stmt = $dbObj->prepare("SELECT `id`, `code`, `companyname` FROM `COMPANIES` WHERE `code` IS NOT NULL AND `code` = ?");
                        $bindOK = $stmt->bind_param('s', $includeGlobalAndSpecificCompany);
                    }
                    else
                    {
                        // getting companies for the system administrator
                        $stmt = $dbObj->prepare("SELECT `id`, `code`, `companyname` FROM `COMPANIES` WHERE `code` IS NOT NULL AND `code` != '' ORDER BY `code` ASC");
                        $bindOK = true;
                    }
                    break;
                case TPX_LOGIN_COMPANY_ADMIN:
                    // getting companies comboBox based on companycode of company administrator
                    $stmt = $dbObj->prepare("SELECT `id`, `code`, `companyname` FROM `COMPANIES` WHERE `code` = ? AND `code` IS NOT NULL AND `code` <> ''");
                    $bindOK = $stmt->bind_param('s', $gSession['userdata']['companycode']);
                    break;
            }

            if ($stmt)
            {
                if ($bindOK)
                {
                    if ($stmt->bind_result($id, $code, $companyName))
                    {
                        if ($stmt->execute())
                        {
                            if ($gSession['userdata']['usertype'] == TPX_LOGIN_SYSTEM_ADMIN && $includeShowAll == '1')
                            {
                                $companyItem['id'] = -1;
                                $companyItem['name'] = $allLabel ? $allLabel : $smarty->get_config_vars('str_ShowAll');
                                $companyItem['code'] = '';
                                array_push($companyList, $companyItem);
                            }

                            if ($includeGlobal == '1')
                            {
                                $companyItem['id'] = 0;
                                $companyItem['name'] = $smarty->get_config_vars('str_Global');
                                $companyItem['code'] = 'GLOBAL';
                                array_push($companyList, $companyItem);
                            }

                            while ($stmt->fetch())
                            {
                                $companyItem['id'] = $id;
                                $companyItem['name'] = $code . ' - ' . $companyName;
                                $companyItem['code'] = $code;
                                array_push($companyList, $companyItem);
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
        $resultArray = $companyList;

        return $resultArray;
    }

    static function storesComboStore()
    {
        global $gSession;

        // return an array containing the a list of production sites
        $storesList = Array();
        $distributionHandle = UtilsObj::getGETParam('distributionCentre');

        if ($distributionHandle == 1)
        {
            $distributionUser = true;
        }
        else
        {
            $distributionUser = false;
        }

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            switch ($gSession['userdata']['usertype'])
            {
                case TPX_LOGIN_SYSTEM_ADMIN:
                    // getting stores for the system administrator
                    if ($distributionUser)
                    {
                        $stmt = $dbObj->prepare('SELECT `id`, `code`, `name` FROM `SITES` WHERE (`productionsitekey` = "") AND (`sitetype` = 1) AND (`isexternalstore` = 0) ORDER BY `code`');
                    }
                    else
                    {
                        $stmt = $dbObj->prepare('SELECT `id`, `code`, `name` FROM `SITES` WHERE (`sitetype` = 2) AND (`isexternalstore` = 0) ORDER BY `code`');
                    }
                    $bindOK = true;
                    break;
                case TPX_LOGIN_COMPANY_ADMIN:
                    // getting stores comboBox based on companycode of company administrator

                    if ($distributionUser)
                    {
                        $stmt = $dbObj->prepare('SELECT `id`, `code`, `name` FROM `SITES` WHERE (`productionsitekey` = "") AND (`sitetype` = 1) AND (`isexternalstore` = 0) AND (`companycode` = ?) ORDER BY `code`');
                    }
                    else
                    {
                        $stmt = $dbObj->prepare('SELECT `id`, `code`, `name` FROM `SITES` WHERE (`sitetype` = 2) AND (`isexternalstore` = 0) AND (`companycode` = ?) ORDER BY `code`');
                    }

                    $bindOK = $stmt->bind_param('s', $gSession['userdata']['companycode']);
                    break;
                case TPX_LOGIN_SITE_ADMIN:
                    // getting stores comboBox based on companycode of company administrator
                    $stmt = $dbObj->prepare('SELECT `id`, `code`, `name` FROM `SITES` WHERE (`sitetype` = 2) AND (`isexternalstore` = 0) AND (`code` = ?) ORDER BY `code`');
                    $bindOK = $stmt->bind_param('s', $gSession['userdata']['userowner']);
                    break;
            }

            if ($stmt)
            {
                if ($bindOK)
                {
                    if ($stmt->bind_result($id, $code, $companyName))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
                                $storesItem['id'] = $id;
                                $storesItem['name'] = $code . ' - ' . $companyName;
                                $storesItem['code'] = $code;
                                array_push($storesList, $storesItem);
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

        $resultArray = $storesList;

        return $resultArray;
    }

    static function brandComboStore()
    {
        $companyCode = isset($_GET['companyCode']) ? $_GET['companyCode'] : '';
        $userPage = isset($_GET['userpage']) ? $_GET['userpage'] : '';

        $resultArray = DatabaseObj::getBrandingList($companyCode, $userPage);
        $itemCount = count($resultArray);

        for ($i = 0; $i < $itemCount; $i++)
        {
            $id = $resultArray[$i]['code'];
            $resultArray[$i]['id'] = $id;
        }

        return $resultArray;
    }

    static function licenseComboStore()
    {
        $companyCode = isset($_GET['companyCode']) ? $_GET['companyCode'] : '';
		$userPage = isset($_GET['userpage']) ? $_GET['userpage'] : '';

        $resultArray = DatabaseObj::getLicenseKeysList($companyCode, '', $userPage);

        return $resultArray;
    }

    static function countryComboStore()
	{
		$countryList = UtilsAddressObj::getCountryList();
		$resultArray = [];

		foreach ($countryList as $country) {
			$resultArray[] = [
				'id' => $country['isocode2'],
				'name' => $country['name'],
			];
		}

		return $resultArray;
	}

    static function productCollectionComboStore()
    {
        global $gSession;

        $ref = '';
        $name = '';
        $productCollectionArray = array();

        $companyCode = isset($_GET['companyCode']) ? $_GET['companyCode'] : '';

		$dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			$filters = array();
			// multi dimensional array of filter values in format [type, value]
			$paramString = '';
			$filterParams = array();

			if ($companyCode !== '')
			{
				$filters[] = '(`pl`.`companycode` = ? OR `pl`.`companycode` = "" OR `af`.`companycode` = ? OR `af`.`companycode` = "")';
				$filterParams[] = $companyCode;
				$filterParams[] = $companyCode;
				$paramString .= 'ss';
			}

			// define sql that is used when companyCode is blank do not need to add any additional where params
			// inclusion of pricelink table as companyCode in application files is sometimes blank
			$baseSql = 'SELECT DISTINCT `af`.`ref`, `af`.`name` FROM `PRODUCTCOLLECTIONLINK` `pcl` LEFT JOIN `APPLICATIONFILES` `af` ON `af`.`ref` = `pcl`.`collectioncode` '
					. 'LEFT JOIN `PRICELINK` `pl` ON `pl`.`productcode` = `pcl`.`productcode` WHERE (`pcl`.`availableonline` = 1) AND (`af`.`type` = 0) AND (`af`.`deleted` = 0)';
			if ($filters !== array())
			{
				$baseSql .= ' AND ' . join(' AND ', $filters);
			}
			$baseSql .= ' ORDER BY `af`.`ref`';

			$stmt = $dbObj->prepare($baseSql);
			$bindOk = false;

			if ($filterParams !== array())
			{
				$bindOk = DatabaseObj::bindParams($stmt, $paramString, $filterParams);
			}
			else
			{
				$bindOk = true;
			}

            if ($stmt)
            {
                if ($bindOk)
                {
                    if ($stmt->bind_result($ref, $name))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
                                $arrayItem['code'] = $ref;
                                $arrayItem['name'] = LocalizationObj::getLocaleString($name, $gSession['browserlanguagecode'], true);
                               	$productCollectionArray[] = $arrayItem;
                            }
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
        }

        return $productCollectionArray;
    }

    static function storeLocator()
    {
        // if a parameter is not set then this field will always match
        // i.e. country='' means fetch stores from all countries
        $country = $_GET['country'];
        $region = $_GET['region'];
        $storegroup = $_GET['storegroup'];
        $filter = $_GET['filter'];
        $privateFilter = $_GET['privatefilter'];

		return Order_model::storeLocator($country, $region, $storegroup, $filter, $privateFilter, true);
    }

    static function storeLocatorExternal()
    {
        $country = $_GET['country'];
        $region = $_GET['region'];
        $storegroup = $_GET['storegroup'];
        $filter = $_GET['filter'];
        $privateFilter = $_GET['privatefilter'];

        return Order_model::storeLocatorExternal($country, $region, $storegroup, $filter, $privateFilter);
    }

    static function storeInformationExternal()
    {
        // get additional information for store

        global $gSession;

        $storeCode = $_GET['store'];
        $shippingMethodCode = $gSession['shipping'][0]['shippingmethodcode'];
        $resultArray = Array();

        // only load store locator script if present.
        UtilsObj::includeStoreLocatorScript();

        if (method_exists('EDL_StoreLocatorObj', 'getStoreInformation') && ($gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['useScript']))
        {
            $paramArray = array();
			$paramArray['storecode'] = $storeCode;
			$paramArray['groupcode'] = $gSession['licensekeydata']['groupcode'];
			$paramArray['groupdata'] = $gSession['licensekeydata']['groupdata'];
			$paramArray['webbrandcode'] = $gSession['webbrandcode'];
			$paramArray['shippingmethodcode'] = $shippingMethodCode;
			$paramArray['browserlanguagecode'] = $gSession['browserlanguagecode'];
			$paramArray['search'] = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeLocator']['filter'];
			$paramArray['privatesearch'] = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeLocator']['privateFilter'];
			$paramArray['privatedata'] = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['privateData'];

            $address = EDL_StoreLocatorObj::getStoreInformation($paramArray);

            $addressItem['contactfirstname'] = '';
            $addressItem['contactlastname'] = '';
            $addressItem['customername'] = '';
            $addressItem['customeraddress1'] = $address['address1'];
            $addressItem['customeraddress2'] = $address['address2'];
            $addressItem['customeraddress3'] = $address['address3'];
            $addressItem['customeraddress4'] = $address['address4'];
            $addressItem['customercity'] = $address['city'];
            $addressItem['customercounty'] = $address['county'];
            $addressItem['customerstate'] = $address['state'];
            $addressItem['customerregioncode'] = $address['regioncode'];

            $addressItem['customerpostcode'] = $address['postcode'];
            $addressItem['customercountrycode'] = $address['countrycode'];
            $addressItem['customercountryname'] = $address['countryname'];

            // determine region by country
            $countryRecord = UtilsAddressObj::getCountry($address['countrycode']);
            $addressItem['customerregion'] = $countryRecord['region'];

            $storeDetails = UtilsAddressObj::formatAddress($addressItem, '', '<br>');

            $storeOpeningTimes = LocalizationObj::getLocaleString($address['storeopeningtimes'], $gSession['browserlanguagecode'], true);
            $storeOpeningTimes = str_replace("\\n", '<br>', $storeOpeningTimes);

            $resultArray['telephonenumber'] = $address['telephonenumber'];
            $resultArray['emailaddress'] = $address['emailaddress'];
            $resultArray['storeurl'] = $address['storeurl'];
            $resultArray['storedetails'] = $storeDetails;
            $resultArray['storename'] = $address['companyname'];
            $resultArray['information'] = $address['information']; // additional information
            $resultArray['storeopeningtimes'] = $storeOpeningTimes;
        }
        else
        {
            // no store data available
            $resultArray['telephonenumber'] = '';
            $resultArray['emailaddress'] = '';
            $resultArray['storeurl'] = '';
            $resultArray['storedetails'] = '';
            $resultArray['storename'] = '';
            $resultArray['information'] = '';
            $resultArray['storeopeningtimes'] = '';
        }

        return $resultArray;
    }

    static function storeInformation()
    {
        // get additional information for external store

        global $gSession;

        $storeCode = $_GET['store'];

        $resultArray = Array();

        $storeData = DatabaseObj::getSiteFromCode($storeCode);

        $addressItem['contactfirstname'] = '';
        $addressItem['contactlastname'] = '';
        $addressItem['customername'] = '';
        $addressItem['customeraddress1'] = $storeData['address1'];
        $addressItem['customeraddress2'] = $storeData['address2'];
        $addressItem['customeraddress3'] = $storeData['address3'];
        $addressItem['customeraddress4'] = $storeData['address4'];
        $addressItem['customercity'] = $storeData['city'];
        $addressItem['customercounty'] = $storeData['county'];
        $addressItem['customerstate'] = $storeData['state'];
        $addressItem['customerregioncode'] = $storeData['regioncode'];
        $addressItem['customerregion'] = $storeData['region'];
        $addressItem['customerpostcode'] = $storeData['postcode'];
        $addressItem['customercountrycode'] = $storeData['countrycode'];
        $addressItem['customercountryname'] = $storeData['countryname'];

        $storeDetails = UtilsAddressObj::formatAddress($addressItem, '', '<br>');

        $storeOpeningTimes = LocalizationObj::getLocaleString($storeData['openingtimes'], $gSession['browserlanguagecode'], true);
        $storeOpeningTimes = str_replace("\\n", '<br>', $storeOpeningTimes);

        $resultArray['telephonenumber'] = $storeData['telephonenumber'];
        $resultArray['emailaddress'] = $storeData['emailaddress'];
        $resultArray['storeurl'] = $storeData['storeurl'];
        $resultArray['storedetails'] = $storeDetails;
        $resultArray['storename'] = $storeData['companyname'];
        $resultArray['information'] = '';
        $resultArray['storeopeningtimes'] = $storeOpeningTimes;

        return $resultArray;
    }

    static function getCompaniesLicensekeys()
    {
        $companyCode = $_GET['companycode'];
        $productCode = $_GET['productCode'];
        $pricingID = $_GET['pricingID'];
        $tableID = $_GET['tableid'];
        $paperOrCoverCode = isset($_GET['paperOrCoverCode']) ? $_GET['paperOrCoverCode'] : '';

        return self::getLicensekeysTableData($companyCode, $productCode, $pricingID, $tableID, $paperOrCoverCode);
    }

    static function getLicensekeysTableData($pCompanyCode, $pProductCode, $pPricingID, $pTableID, $paperOrCoverCode = '')
    {
        $resultArray = Array();
        $codeArray = Array();
        $itemList = Array();
        $dbObj = DatabaseObj::getGlobalDBConnection();

        global $gConstants;
        global $gSession;

        if (($gConstants['optionms']) && ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN))
        {
            $pCompanyCode = $gSession['userdata']['companycode'];
        }

        if ($dbObj)
        {
            switch ($pTableID)
            {
                case TPX_TABLE_PRODUCTPRICES:
                    $tableName = 'PRODUCTPRICES';
                    $fieldName = 'productcode';
                    break;
                case TPX_TABLE_PAPERPRICES:
                    $tableName = 'PAPERPRICES';
                    $fieldName = 'papercode';
                    break;
                case TPX_TABLE_COVERPRICES:
                    $tableName = 'COVERPRICES';
                    $fieldName = 'covercode';
                    break;
            }

            if (($pTableID == TPX_TABLE_PAPERPRICES) || ($pTableID == TPX_TABLE_COVERPRICES))
            {
                // Paper and Cover pricing
                if ($pPricingID == 0)
                {
                    $operator = '=';

                    if ($pCompanyCode == '')
                    {
                        $stmt = $dbObj->prepare('SELECT groupcode, name FROM LICENSEKEYS WHERE companycode = ""');
                        $bindOk = true;
                    }
                    else
                    {
                        $stmt = $dbObj->prepare('SELECT groupcode, name FROM LICENSEKEYS WHERE companycode = ? OR companycode = ""');
                        $bindOk = $stmt->bind_param('s', $pCompanyCode);
                    }
                }
                else
                {
                    $operator = '<>';

                    if ($pCompanyCode == '')
                    {
                        $stmt = $dbObj->prepare('SELECT groupcode, name FROM LICENSEKEYS WHERE companycode = ""');
                        $bindOk = true;

                        $stmt2 = $dbObj->prepare('SELECT `id`, `productcode`, `groupcode`, `active` FROM `' . $tableName . '` WHERE (`' . $fieldName . '` = ?) AND (`productcode` = ?) AND (`id` = ?) OR (`parentid` = ?) AND (`companycode` = "") ORDER BY `parentid`');
                        $bindOK2 = $stmt2->bind_param('ssii', $paperOrCoverCode, $pProductCode, $pPricingID, $pPricingID);
                    }
                    else
                    {
                        $stmt = $dbObj->prepare('SELECT groupcode, name FROM LICENSEKEYS WHERE companycode = ? OR companycode = ""');
                        $bindOk = $stmt->bind_param('s', $pCompanyCode);

                        $stmt2 = $dbObj->prepare('SELECT `id`, `productcode`, `groupcode`, `active` FROM `' . $tableName . '` WHERE (`' . $fieldName . '` = ?) AND (`productcode` = ?) AND (`id` = ?) OR (`parentid` = ?) AND (`companycode` = ?) ORDER BY `parentid`');
                        $bindOK2 = $stmt2->bind_param('ssiis', $paperOrCoverCode, $pProductCode, $pPricingID, $pPricingID, $pCompanyCode);
                    }

                    if ($stmt2)
                    {
                        if ($bindOK2)
                        {
                            if ($stmt2->bind_result($id, $productCode, $groupCode, $isActive))
                            {
                                if ($stmt2->execute())
                                {
                                    while ($stmt2->fetch())
                                    {
                                        $item['recordid'] = $id;
                                        $item['productcode'] = $productCode;
                                        $item['groupcode'] = $groupCode;
                                        $item['isactive'] = $isActive;
                                        array_push($itemList, $item);
                                    }
                                }
                            }
                        }
                        $stmt2->free_result();
                        $stmt2->close();
                    }
                }

                if ($stmt)
                {
                    if ($bindOk)
                    {
                        if ($stmt->bind_result($groupCode, $groupName))
                        {
                            if ($stmt->execute())
                            {
                                while ($stmt->fetch())
                                {
                                    $arrayItem['code'] = $groupCode;
                                    $arrayItem['name'] = $groupName;
                                    array_push($codeArray, $arrayItem);
                                }
                            }
                        }
                    }
                    $stmt->free_result();
                    $stmt->close();
                }
            }
            else
            {
                // Product pricing
                if ($pPricingID == 0)
                {
                    if ($pCompanyCode == '')
                    {
                        $stmt = $dbObj->prepare('SELECT groupcode, name FROM LICENSEKEYS where groupcode NOT IN (SELECT groupcode FROM PRODUCTPRICES  WHERE productcode = ? AND `companycode` = "")');
                        $bindOk = $stmt->bind_param('s', $pProductCode);

                        $stmt2 = $dbObj->prepare('SELECT `id`, `productcode`, `groupcode`, `active` FROM `PRODUCTPRICES` WHERE (`productcode` = ?) AND (`companycode` = "") ORDER BY `parentid`');
                        $bindOK2 = $stmt2->bind_param('s', $pProductCode);
                    }
                    else
                    {
                        $stmt = $dbObj->prepare('SELECT groupcode, name FROM LICENSEKEYS where (companycode = ? OR companycode = "") AND groupcode NOT IN (SELECT groupcode FROM PRODUCTPRICES  WHERE productcode = ? AND companycode = ?)');
                        $bindOk = $stmt->bind_param('sss', $pCompanyCode, $pProductCode, $pCompanyCode);

                        $stmt2 = $dbObj->prepare('SELECT `id`, `productcode`, `groupcode`, `active` FROM `PRODUCTPRICES` WHERE ((`productcode` = ?) AND (`companycode` = ?)) ORDER BY `parentid`');
                        $bindOK2 = $stmt2->bind_param('ss', $pProductCode, $pCompanyCode);
                    }
                }
                else
                {
                    if ($pCompanyCode == '')
                    {
                        $stmt = $dbObj->prepare('SELECT groupcode, name FROM LICENSEKEYS where  groupcode NOT IN (SELECT groupcode FROM PRODUCTPRICES WHERE productcode = ? AND companycode = "" AND (id <> ? AND parentid <> ?))');
                        $bindOk = $stmt->bind_param('sii', $pProductCode, $pPricingID, $pPricingID);

                        $stmt2 = $dbObj->prepare('SELECT `id`, `productcode`, `groupcode`, `active` FROM `PRODUCTPRICES` WHERE (`productcode` = ?) AND (`id` = ?) OR (`parentid` = ?) AND (`companycode` = "") ORDER BY `parentid`');
                        $bindOK2 = $stmt2->bind_param('sii', $pProductCode, $pPricingID, $pPricingID);
                    }
                    else
                    {
                        $stmt = $dbObj->prepare('SELECT groupcode, name FROM LICENSEKEYS where (companycode = ? OR companycode = "") AND groupcode NOT IN (SELECT groupcode FROM PRODUCTPRICES WHERE productcode = ? AND companycode = ? AND (id <> ? AND parentid <> ?))');
                        $bindOk = $stmt->bind_param('sssii', $pCompanyCode, $pProductCode, $pCompanyCode, $pPricingID, $pPricingID);

                        $stmt2 = $dbObj->prepare('SELECT `id`, `productcode`, `groupcode`, `active` FROM `PRODUCTPRICES` WHERE (`productcode` = ?) AND (`id` = ?) OR (`parentid` = ?) AND (`companycode` = ?) ORDER BY `parentid`');
                        $bindOK2 = $stmt2->bind_param('siis', $pProductCode, $pPricingID, $pPricingID, $pCompanyCode);
                    }
                }

                if ($stmt)
                {
                    if ($bindOk)
                    {
                        if ($stmt->bind_result($groupCode, $groupName))
                        {
                            if ($stmt->execute())
                            {
                                while ($stmt->fetch())
                                {
                                    $arrayItem['code'] = $groupCode;
                                    $arrayItem['name'] = $groupName;
                                    array_push($codeArray, $arrayItem);
                                }
                            }
                        }
                    }
                    $stmt->free_result();
                    $stmt->close();
                }

                if ($stmt2)
                {
                    if ($bindOK2)
                    {
                        if ($stmt2->bind_result($id, $productCode, $groupCode, $isActive))
                        {
                            if ($stmt2->execute())
                            {
                                while ($stmt2->fetch())
                                {
                                    $item['recordid'] = $id;
                                    $item['productcode'] = $productCode;
                                    $item['groupcode'] = $groupCode;
                                    $item['isactive'] = $isActive;
                                    array_push($itemList, $item);
                                }
                            }
                        }
                    }
                    $stmt2->free_result();
                    $stmt2->close();
                }
            }
            array_unshift($codeArray, Array('code' => '', 'name' => ''));

            $dbObj->close();
        }

        $resultArray['items'] = $itemList;
        $resultArray['groupcodes'] = $codeArray;

        return $resultArray;
    }

    /**
     * Gets product list filtered by company code.
     *
     * Modified: -
     *
     * @param string $pCompanyCode
     * Passed either as a parameter to the function or as a $_GET variable
     *
     * @return array
     *
     * @since Version 2.5.2
     * @author Dasha Salo
     */
    static function getCompaniesProducts($pCompanyCode = 'NONE')
    {
        global $gConstants;
        global $gSession;

        if ($pCompanyCode == 'NONE')
        {
            $pCompanyCode = isset($_GET['companycode']) ? $_GET['companycode'] : '';
        }
        if (($gConstants['optionms']) && ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN))
        {
            $pCompanyCode = $gSession['userdata']['companycode'];
        }

        $productCode = '';
        $productName = '';
        $productArray = array();
        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            if ($pCompanyCode == "")
            {
                $stmt = $dbObj->prepare('SELECT `code`, `name` FROM `PRODUCTS` WHERE `deleted` = 0  ORDER BY `code`');
                $bindOk = true;
            }
            else
            {
                $stmt = $dbObj->prepare('SELECT `code`, `name` FROM `PRODUCTS` WHERE `deleted` = 0  AND (`companycode` = ? OR `companycode` = "") ORDER BY `code`');
                $bindOk = $stmt->bind_param('s', $pCompanyCode);
            }

            if ($stmt)
            {
                if ($bindOk)
                {
                    if ($stmt->bind_result($productCode, $productName))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
                                $arrayItem['code'] = $productCode;
                                $arrayItem['name'] = LocalizationObj::getLocaleString($productName, $gSession['browserlanguagecode'], true);
                                array_push($productArray, $arrayItem);
                            }
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
            }
        }
        return $productArray;
    }

    static function getPriceLists()
    {
        global $gSession;
        global $gConstants;

        $resultArray = Array();
        $companyCode = $_GET['company'];

        if ($gConstants['optionms'])
        {
            if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
            {
                $companyCode = $gSession['userdata']['companycode'];
            }
        }

        $componentCategroyCode = $_GET['category'];
        $displayCustom = $_GET['displayCustom'];

        $priceLists = DatabaseObj::getPriceListFromComponentCategoryCode($componentCategroyCode, $companyCode);

        $resultArray['displaycustom'] = $displayCustom;
        $resultArray['pricelists'] = $priceLists;

        return $resultArray;
    }

    /**
     * Updates order quantity for one order line.
     *
     * @param string orderline id
     * Passed as a $_GET variable
     *
     * @param string quantity
     * Passed as a $_GET variable
     *
     * @return array
     *
     * @since Version 3.0.0
     * @author Steffen Haugk
     */
    static function updateQty($pQty = '', $pOrderLineId = '', $pComponentorderlineid = '')
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
        $resultParam = '';
        $qty = !empty($pQty) ? $pQty : UtilsObj::getGETParam('itemqty', 1);
        $orderLineId = !empty($pOrderLineId) ? $pOrderLineId : UtilsObj::getGETParam('orderlineid');
        $componentorderlineid = !empty($pComponentorderlineid) ? $pComponentorderlineid : UtilsObj::getGETParam('componentorderlineid');
        $itemIndex = 0;
        $componentPath = '';

        if ($orderLineId != TPX_ORDERFOOTER_ID)
        {
            // get orderline index based on id
            foreach ($gSession['items'] as $index => $item)
            {
                if ($item['orderlineid'] == $orderLineId)
                {
                    $itemIndex = $index;
                    break;
                }
            }
        }

        Order_model::storeOrderMetaData('qty');

        // re-check the voucher to make sure its usage status hasn't changed
        Order_model::checkVoucher();

        if (!(($gSession['order']['voucherlockqty'] == 1) && ($gSession['order']['voucherminqty'] == $gSession['order']['vouchermaxqty'])))
        {
            if (is_numeric($qty))
            {
                if (($qty < 1) || ($qty > 99999999))
                {
                    $qty = 1;
                }
            }
            else
            {
                $qty = 1;
            }

            if ($orderLineId == TPX_ORDERFOOTER_ID)
            {
                $itemIndex = TPX_ORDERFOOTER_ID;
                $orderLineId = $componentorderlineid;

                if ($gSession['ismobile'] == true)
                {
                    $section = &DatabaseObj::getSectionByOrderLineId($componentorderlineid);

                    if ($section['orderlineid'] == $componentorderlineid)
                    {
                        $componentPath = $section['path'] . $section['code'];
                    }
                    else
                    {
                        foreach ($section['subsections'] as $subsections)
                        {
                            if ($subsections['orderlineid'] == $componentorderlineid)
                            {
                                $componentPath = $subsections['path'] . $subsections['code'];
                            }
                        }

                        if ($componentPath == '')
                        {
                            foreach ($section['checkboxes'] as $checkboxes)
                            {
                                if ($checkboxes['orderlineid'] == $componentorderlineid)
                                {
                                    $componentPath = $checkboxes['path'] . $checkboxes['code'];
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                //Need to validate the product price to make sure the correct qty is being used and it is in a valid range
                $productPriceArray = DatabaseObj::getProductPrice($gSession['items'][$itemIndex]['itemproductcode'], $gSession['licensekeydata']['groupcode'],
                        $gSession['userdata']['companycode'], $gSession['order']['currencyexchangerate'], $gSession['order']['currencydecimalplaces'], $qty);

                //Update the qty to be the correct qty
                $qty = $productPriceArray['newqty'];

                $gSession['items'][$itemIndex]['itemqty'] = $qty;
                $gSession['shipping'][0]['shippingqty'] = $qty;
            }

            Order_model::updateOneOrderSection($itemIndex);

            // as we are updating the quantities of a product then we need to update each orderfooter section.
            // this is so they have the new product quantities in case the component in the order footer uses the product qty to calcualte price.
            // update the components itemqty to the new qty
            Order_model::getOrderFooterSectionData(Array(), $gSession['order']['currencyexchangerate'],
                   $gSession['order']['currencydecimalplaces'], true);

            // re-check the voucher to make sure its usage status hasn't changed
            Order_model::checkVoucher();

            // update components
            Order_model::updateOneOrderSection(-1);

            if ($gSession['ismobile'] == true)
            {
                foreach ($gSession['order']['orderFooterSections'] as $section)
                {
                    if ($section['path'] . $section['code'] == $componentPath)
                    {
                        $componentorderlineid = $section['orderlineid'];
                    }
                    else
                    {
                        foreach ($section['subsections'] as $subsection)
                        {

                            if ($subsection['path'] . $subsection['code'] == $componentPath)
                            {
                                $componentorderlineid = $subsection['orderlineid'];
                            }
                        }

                        foreach ($section['checkboxes'] as $checkbox)
                        {
                            if ($checkbox['path'] . $checkbox['code'] == $componentPath)
                            {
                                $componentorderlineid = $checkbox['orderlineid'];
                            }
                        }
                    }
                }

                foreach ($gSession['order']['orderFooterCheckboxes'] as $checkbox)
                {
                    if ($checkbox['path'] . $checkbox['code'] == $componentPath)
                    {
                        $componentorderlineid = $checkbox['orderlineid'];
                    }
                }
            }

            // update the totals
			Order_model::updateOrderTotal();
            Order_model::updateOrderShippingRate();
            Order_model::updateOrderTotal();
            DatabaseObj::updateSession();

        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        Order_model::formatOrderAddresses($resultArray);
        $resultArray['custominit'] = '';
        $resultArray['metadata'] = Order_model::buildOrderMetaData('qty');
        $resultArray['previousstage'] = 'qty';
        $resultArray['nextstage'] = 'shipping';
        $resultArray['itemindex'] = $itemIndex;
        $resultArray['orderlineid'] = $orderLineId;
        $resultArray['vouchermessage'] = '';
        $resultArray['sectionorderlineid'] = $componentorderlineid;

        if ($gSession['order']['voucherstatus'] == 'str_LabelVoucherNoLonger')
        {
            $resultArray['vouchermessage'] = $gSession['order']['voucherstatus'];
        }

        return $resultArray;
    }

    static function updateComponentQty($pQty = '', $pOrderLineId = '', $pComponentorderlineid = '')
    {
        global $gSession;

        $qty = !empty($pQty) ? $pQty : UtilsObj::getGETParam('componentitemqty', 1);
        $orderLineId = !empty($pOrderLineId) ? $pOrderLineId : UtilsObj::getGETParam('orderlineid');
        $componentOrderLineID = !empty($pComponentorderlineid) ? $pComponentorderlineid : UtilsObj::getGETParam('componentorderlineid');
        $itemIndex = 0;

        // if the qty entered is not numeric then we set the quantity to 1.
        // this will be corrected when we calcualte the price
        if (!is_numeric($qty))
        {
            $qty = 1;
        }

        if ($orderLineId == TPX_ORDERFOOTER_ID)
        {
            $itemIndex = TPX_ORDERFOOTER_ID;

            // we need to pass both sections that belong to an orderfooter section and the orderfooter root
            $tempArray = Array();
            $tempArray['sections'] = &$gSession['order']['orderFooterSections'];
            $tempArray['footer'] = &$gSession['order']['orderFooterCheckboxes'];

            // update the components itemqty to the new qty
            self::updateComponentsItemQty($tempArray, $componentOrderLineID, $qty);
        }
        else
        {

            // get orderline index based on id
            foreach ($gSession['items'] as $index => &$item)
            {
                if ($item['orderlineid'] == $orderLineId)
                {
                    $itemIndex = $index;
                    break;
                }
            }

            // update the components itemqty to the new qty
            self::updateComponentsItemQty($item, $componentOrderLineID, $qty);
        }
    }

    static function updateComponentsItemQty(&$pHaystack, $pComponentOrderlineId, $pNewQty)
    {
        if (is_array($pHaystack))
        {
            foreach ($pHaystack as &$item)
            {
                if (is_array($item))
                {
                    if (array_key_exists('orderlineid', $item) && ($item['orderlineid'] == $pComponentOrderlineId))
                    {
                        $item['quantity'] = $pNewQty;
                    }
                    else
                    {
                        self::updateComponentsItemQty($item, $pComponentOrderlineId, $pNewQty);
                    }
                }
            }
        }
    }

    static function updateQtyAll()
    {
        global $gSession;

        $arrayResult = array();
        $arrayResultComponent = array();
        //update component first
        $componentArrayToUpdate = json_decode($_POST['component']);
        foreach ($componentArrayToUpdate as $objectComponent)
        {
            $result = self::updateComponentQty($objectComponent->qty, $objectComponent->orderline, $objectComponent->componentorderlineid);
            $arrayResultComponent[$objectComponent->content] = array('orderline' => $objectComponent->orderline, 'productqty' => $objectComponent->prodqty);
        }
        //update product
        $productArrayToUpdate = json_decode($_POST['product']);
        foreach ($productArrayToUpdate as $objectProduct)
        {
            $arrayResultComponent[$objectProduct->content] = array('orderline' => $objectProduct->orderline, 'productqty' => $objectProduct->qty);
        }

        //refresh html and update product
        foreach ($arrayResultComponent as $key => $arrayProduct)
        {
            $result = self::updateQty($arrayProduct['productqty'], $arrayProduct['orderline']);
            if ($gSession['ismobile'] == true)
            {
                $arrayResult[$key] = AjaxAPI_view::updateOrderLineSmall($result, false);
            }
            else
            {
                $arrayResult[$key] = AjaxAPI_view::updateOrderLineLarge($result, false);
            }
        }
        return $arrayResult;
    }

    /**
     * Returns list of available components in that section so they can be presented as a choice.
     *
     * @param string orderline id of order item
     * Passed as a $_GET variable
     * @param string orderline id of section
     * Passed as a $_GET variable
     *
     * @return array
     *
     * @since Version 3.0.0
     * @author Steffen Haugk
     */
    static function changeComponent()
    {
        global $gSession;

        $resultArray = Array();
        $itemIndex = 0;
        $sectionCode = '';
        $sectionName = '';
        $componentPath = '';
        $defaultCode = '';

        $orderLineId = UtilsObj::getGETParam('item'); // orderline id of order line
        if ($orderLineId == '')
        {
            $orderLineId = UtilsObj::getGETParam('orderlineid'); // orderline id of order line
        }

        $sectionOrderLineId = UtilsObj::getGETParam('section'); // orderline id of section
        if ($sectionOrderLineId == '')
        {
            $sectionOrderLineId = UtilsObj::getGETParam('componentorderlineid');
        }

        if ($orderLineId == TPX_ORDERFOOTER_ID)
        {
            $itemIndex = TPX_ORDERFOOTER_ID;

            $section = DatabaseObj::getSectionByOrderLineId($sectionOrderLineId);
            $sectionCode = $section['sectioncode'];
            $sectionName = $section['sectionname'];
            $path = $section['path'];
            $defaultCode = $section['code'];
            $productCode = $section['itemproductcode'];
            $itemPageCount = $section['itempagecount'];
            $itemQty = $section['itemqty'];
            $componentQty = $section['quantity'];

            $resultArray = Order_model::getOrderComponentList($itemIndex, $productCode, $sectionCode, $path, true, $defaultCode, false,
                            $itemQty, $itemPageCount, $componentQty);
        }
        else
        {
            // Find order line
            foreach ($gSession['items'] as $index => $item)
            {
                if ($item['orderlineid'] == $orderLineId)
                {
                    $itemIndex = $index;

                    $section = DatabaseObj::getSectionByOrderLineId($sectionOrderLineId);
                    $sectionCode = $section['sectioncode'];
                    $sectionName = $section['sectionname'];
                    $path = $section['path'];
                    $defaultCode = $section['code'];
                    $productTreeCode = $item['componenttreeproductcode'];
					$componentQty = $section['quantity'];

					$resultArray = Order_model::getOrderComponentList($itemIndex, $productTreeCode, $sectionCode, $path, true, $defaultCode, false, 0, 0, $componentQty);
                    break;
                }
            }
        }

        $resultArray['custominit'] = '';
        $resultArray['metadata'] = Order_model::buildOrderMetaData('qty');
        $resultArray['previousstage'] = 'qty';
        $resultArray['nextstage'] = 'shipping';
        $resultArray['itemindex'] = $itemIndex;

        $resultArray['section'] = $sectionCode;
        $resultArray['sectionname'] = $sectionName;
        $resultArray['sectionorderlineid'] = $sectionOrderLineId;
        $resultArray['orderlineid'] = $orderLineId;
        $resultArray['defaultcode'] = $defaultCode;

        return $resultArray;
    }

    /**
     * Updates a section of an order line with the selected component.
     *
     * @param string orderline id of order item
     * Passed as a $_GET variable
     * @param string section code
     * Passed as a $_GET variable
     * @param string component code
     * Passed as a $_GET variable
     *
     * @return array
     *
     * @since Version 3.0.0
     * @author Steffen Haugk
     */
    static function updateComponent()
    {
        global $gSession;

        $resultArray = Array();
        $itemIndex = 0;
        $componentPath = '';

        $orderLineId = UtilsObj::getGETParam('orderlineid'); // orderline id of order line
        $sectionOrderLineId = UtilsObj::getGETParam('section'); // section code
        $componentCode = UtilsObj::getGETParam('code'); // component code
        $componentLocalCode = UtilsObj::getGETParam('localcode'); // component local code

        if ($orderLineId == TPX_ORDERFOOTER_ID)
        {
            $itemIndex = TPX_ORDERFOOTER_ID;

            $section = &DatabaseObj::getSectionByOrderLineId($sectionOrderLineId);

            if ($gSession['ismobile'] == true)
            {
                $sectionCode = $section['sectioncode'];

                if ($section['orderlineid'] == $sectionOrderLineId)
                {
                    $componentPath = $section['path'] . $componentCode;
                }
                else
                {
                    foreach ($section['subsections'] as $subsection)
                    {
                        if ($subsection['orderlineid'] == $sectionOrderLineId)
                        {
                            $componentPath = $subsection['path'] . $componentCode;
                        }
                    }

                    if ($componentPath == '')
                    {
                        foreach ($section['checkboxes'] as $checkbox)
                        {
                            if ($checkbox['orderlineid'] == $sectionOrderLineId)
                            {
                                $componentPath = $checkbox['path'] . $componentCode;
                            }
                        }
                    }
                }
            }

            $componentArray = DatabaseObj::getComponentByCode($componentCode);

            $section['id'] = $componentArray['id'];
            $section['code'] = $componentCode;
            $section['localcode'] = $componentLocalCode;
            $section['name'] = $componentArray['name'];
            $section['info'] = $componentArray['info'];
            $section['unitcost'] = $componentArray['unitcost'];
            $section['unitweight'] = $componentArray['weight'];
            $section['orderfooterusesproductquantity'] = $componentArray['orderfooterusesproductquantity'];
            $section['orderfootertaxlevel'] = $componentArray['orderfootertaxlevel'];
            $section['skucode'] = $componentArray['skucode'];

            $sectionList = DatabaseObj::getOrderSectionList($section['itemproductcode'], $gSession['licensekeydata']['groupcode'],
                            $gSession['userdata']['companycode'], '$ORDERFOOTER\\');

            // update the order footer sections without changing the item qty
            foreach ($sectionList['sections'] as $sectionCode)
            {
                $section = &DatabaseObj::getSessionOrderSection($itemIndex, $section['itemproductcode'],
                                '$ORDERFOOTER\\$' . $sectionCode . '\\', $gSession['order']['currencyexchangerate'],
                                $gSession['order']['currencydecimalplaces'], -1, true, $componentCode, $section['itempagecount']);
            }

            Order_model::getOrderFooterSectionData(Array(), $gSession['order']['currencyexchangerate'],
                    $gSession['order']['currencydecimalplaces'], true);

            if ($gSession['ismobile'] == true)
            {
                foreach ($gSession['order']['orderFooterSections'] as $section)
                {
                    if ($section['path'] . $section['code'] == $componentPath)
                    {
                        $sectionOrderLineId = $section['orderlineid'];
                    }
                    else
                    {
                        foreach ($section['subsections'] as $subsection)
                        {
                            if ($subsection['path'] . $subsection['code'] == $componentPath)
                            {
                                $sectionOrderLineId = $subsection['orderlineid'];
                            }
                        }

                        foreach ($section['checkboxes'] as $checkbox)
                        {
                            if ($checkbox['path'] . $checkbox['code'] == $componentPath)
                            {
                                $sectionOrderLineId = $checkbox['orderlineid'];
                            }
                        }
                    }
                }

                foreach ($gSession['order']['orderFooterCheckboxes'] as $checkbox)
                {
                    if ($checkbox['path'] . $checkbox['code'] == $componentPath)
                    {
                        $sectionOrderLineId = $checkbox['orderlineid'];
                    }
                }
            }

            Order_model::updateOneOrderSection($itemIndex);
            Order_model::updateOrderShippingRate();
            Order_model::updateOrderTotal();
            DatabaseObj::updateSession();

        }
        else
        {
            // get orderline index based on id
            foreach ($gSession['items'] as $index => $item)
            {
                if ($item['orderlineid'] == $orderLineId)
                {
                    $itemIndex = $index;

                    $section = &DatabaseObj::getSectionByOrderLineId($sectionOrderLineId);

                    if ($gSession['ismobile'] == true)
                    {
                        if ($section['orderlineid'] == $sectionOrderLineId)
                        {
                            $componentPath = $section['path'];
                        }
                        else
                        {
                            foreach ($section['subsections'] as $subsections)
                            {
                                if ($subsections['orderlineid'] == $sectionOrderLineId)
                                {
                                    $componentPath = $subsections['path'];
                                }
                            }

                            if ($componentPath == '')
                            {
                                foreach ($section['checkboxes'] as $checkboxes)
                                {
                                    if ($checkboxes['orderlineid'] == $sectionOrderLineId)
                                    {
                                        $componentPath = $checkboxes['path'];
                                    }
                                }
                            }
                        }
                    }

                    $section['code'] = $componentCode;
                    $section['localcode'] = $componentLocalCode;

                    // we need to check if we are changing a component that belongs to a section in the LINEFOOTER section
                    $parentPathElements = explode('\\', $section['path']);

                    if ($parentPathElements[0] == '$LINEFOOTER')
                    {
                        $parentSection = '$LINEFOOTER\\';
                    }
                    else
                    {
                        $parentSection = '';
                    }

                    // since the component has changed we need to re-build everything that belongs to it
                    $sectionList = DatabaseObj::getOrderSectionList($item['componenttreeproductcode'], $gSession['licensekeydata']['groupcode'],
                                    $gSession['userdata']['companycode'], $parentSection);

                    foreach ($sectionList['sections'] as $sectionCode)
                    {
                        $section = &DatabaseObj::getSessionOrderSection($itemIndex, $item['componenttreeproductcode'],
                                        $parentSection . '$' . $sectionCode . '\\', $gSession['order']['currencyexchangerate'],
                                        $gSession['order']['currencydecimalplaces'], $item['itemqty'], true, $componentCode);

                        if ($gSession['ismobile'] == true)
                        {
                            if ($section['path'] == $componentPath)
                            {
                                $sectionOrderLineId = $section['orderlineid'];
                            }
                            else
                            {
                                foreach ($section['subsections'] as $subsections)
                                {
                                    if ($subsections['path'] == $componentPath)
                                    {
                                        $sectionOrderLineId = $subsections['orderlineid'];
                                    }
                                }

                                foreach ($section['checkboxes'] as $checkboxes)
                                {
                                    if ($checkboxes['path'] == $componentPath)
                                    {
                                        $sectionOrderLineId = $checkboxes['orderlineid'];
                                    }
                                }
                            }
                        }
                    }

                    Order_model::updateOneOrderSection($itemIndex);
					Order_model::updateOrderShippingRate();
					Order_model::updateOrderTotal();
					DatabaseObj::updateSession();
                }
            }
        }

        $resultArray['custominit'] = '';
        $resultArray['metadata'] = Order_model::buildOrderMetaData('qty');
        $resultArray['previousstage'] = 'qty';
        $resultArray['nextstage'] = 'shipping';
        $resultArray['itemindex'] = $itemIndex;
        $resultArray['orderlineid'] = $sectionOrderLineId;
        $resultArray['sectionorderlineid'] = $sectionOrderLineId;
        $resultArray['vouchermessage'] = '';

        return $resultArray;
    }

    static function saveTempMetadata()
    {
		Order_model::storeOrderMetaData($_POST['stage']);
    }

    /**
     * Toggles a checkbox of an order line with the selected component.
     *
     * @param array $pHaystack          the array
     * @param string $pOrderlineId      order line id of checkbox
     *
     * @return array
     *
     * @since Version 3.0.0
     * @author Steffen Haugk
     */
    static function toggleCheckboxRecursive(&$pHaystack, $pOrderlineId)
    {
        if (is_array($pHaystack))
        {
            foreach ($pHaystack as &$item)
            {
                if (is_array($item))
                {
                    if (array_key_exists('orderlineid', $item) && ($item['orderlineid'] == $pOrderlineId))
                    {
                        $item['checked'] = 1 - $item['checked'];
                    }
                    else
                    {
                        self::toggleCheckboxRecursive($item, $pOrderlineId);
                    }
                }
            }
        }
    }

    /**
     * Toggles a checkbox of an order line with the selected component.
     *
     * @param string orderline id of order item
     * Passed as a $_GET variable
     * @param string checkbox id
     * Passed as a $_GET variable
     *
     * @return array
     *
     * @since Version 3.0.0
     * @author Steffen Haugk
     */
    static function updateCheckbox()
    {
        global $gSession;

        $itemIndex = -1;
        $resultArray = Array();

        $orderLineId = UtilsObj::getGETParam('orderlineid'); // orderline id of order line
        $checkboxId = UtilsObj::getGETParam('componentid'); // checkbox id
        // order line id -1 means order footer
        if ($orderLineId == TPX_ORDERFOOTER_ID)
        {
            $itemIndex = TPX_ORDERFOOTER_ID;

            // we need to pass both sections that belong to an orderfooter section and the orderfooter root
            // as we are unable to determine what the clicked checkbox belongs to.
            $tempArray = Array();
            $tempArray['sections'] = &$gSession['order']['orderFooterSections'];
            $tempArray['footer'] = &$gSession['order']['orderFooterCheckboxes'];

            self::toggleCheckboxRecursive($tempArray, $checkboxId);
        }
        else
        {
            // 'regular' order line
            // get orderline index based on id
            foreach ($gSession['items'] as $index => &$item)
            {
                if ($item['orderlineid'] == $orderLineId)
                {
                    $itemIndex = $index;
                    break; // we found what we wanted, now break out
                }
            }
            self::toggleCheckboxRecursive($item, $checkboxId);
        }

        Order_model::updateOneOrderSection($itemIndex);
        Order_model::updateOrderShippingRate();
        Order_model::updateOrderTotal();

        DatabaseObj::updateSession();

        $resultArray['custominit'] = '';
        $resultArray['metadata'] = Order_model::buildOrderMetaData('qty');
        $resultArray['previousstage'] = 'qty';
        $resultArray['nextstage'] = 'shipping';
        $resultArray['itemindex'] = $itemIndex;
        $resultArray['orderlineid'] = $checkboxId;
        $resultArray['vouchermessage'] = '';
        $resultArray['sectionorderlineid'] = -1;


        return $resultArray;
    }

    static function getComponentCategories()
    {
        $smarty = SmartyObj::newSmarty('', '', '');

        $companyCode = UtilsObj::getGETParam('companycode', '');

		if ($companyCode == 'GLOBAL')
        {
            $companyCode = '';
        }

        $componentCategories = DatabaseObj::componentCategoriesList($companyCode, true);

        array_unshift($componentCategories,
                Array('id' => '', 'companycode' => '', 'code' => 'SECTIONS', 'name' => $smarty->get_config_vars('str_LabelSections'), 'prompt' => '', 'pricingmodel' => '', 'islist' => '', 'active' => '', 'requirespagecount' => '',));

        return $componentCategories;
    }

    static function updateOrderSummaryLarge()
    {
        global $gSession;

        DatabaseObj::updateSession();

        $stage = $gSession['order']['currentstage'];

        $showPricesWithTax = ($gSession['order']['showpriceswithtax'] == 1) ? true : false;

        if (($showPricesWithTax == true) && (($stage == 'qty') || ($stage == 'shipping') || ($stage == 'companionselection')))
        {
            $itemTotal = $gSession['order']['ordertotalitemsellwithtaxnodiscount'];
            $shippingCost = $gSession['order']['ordertotalshippingsellbeforediscount'];
            $orderTotal = $gSession['order']['ordertotalbeforediscount'];
            $orderTotal = $itemTotal + $shippingCost;
        }
        else if (($showPricesWithTax == false) && (($stage == 'qty') || ($stage == 'shipping') || ($stage == 'companionselection')))
        {
            $itemTotal = $gSession['order']['ordertotalitemsellnotaxnodiscount'];
            $shippingCost = $gSession['order']['ordertotalshippingsellbeforediscount'];
            $orderTotal = $gSession['order']['ordertotalbeforediscount'];
            $orderTotal = $itemTotal + $shippingCost;
        }
        else
        {
            $itemTotal = $gSession['order']['ordertotalitemsellwithtax'];
            $shippingCost = $gSession['order']['shippingratetotalsellwithtax'];
            $orderTotal = $gSession['order']['ordertotaltopay'];
        }

        $totalsArray = array('itemtotal' => $itemTotal, 'shippingcost' => $shippingCost, 'ordertotal' => $orderTotal);

        return $totalsArray;
    }

    static function updateOrderSummarySmall()
    {
        global $gSession;

        DatabaseObj::updateSession();

        $stage = $gSession['order']['currentstage'];

        $orderTotal = -1;

        $showPricesWithTax = ($gSession['order']['showpriceswithtax'] == 1) ? true : false;

        if (($showPricesWithTax == true) && (($stage == 'qty') || ($stage == 'shipping')))
        {
            $orderTotal = $gSession['order']['ordertotalitemsellwithtaxnodiscount'] + $gSession['order']['ordertotalshippingsellbeforediscount'];
        }
        else if (($showPricesWithTax == false) && (($stage == 'qty') || ($stage == 'shipping')))
        {
            $orderTotal = $gSession['order']['ordertotalitemsellnotaxnodiscount'] + $gSession['order']['ordertotalshippingsellbeforediscount'];
        }
        else
        {
            $orderTotal = $gSession['order']['ordertotaltopay'];
        }

        return $orderTotal;
    }

    static function cfsChangeShippingMethod()
    {
        global $gSession;

        if (isset($_REQUEST['removestore']))
        {
            if ($_REQUEST['removestore'] == 'true')
            {
                $gSession['shipping'][0]['storeid'] = '';
            }
        }

        Order_model::changeShippingMethod();

        $showPricesWithTax = ($gSession['order']['showpriceswithtax'] == 1) ? true : false;

        if ($showPricesWithTax == true)
        {
        	$orderTotal = $gSession['order']['ordertotalitemsellwithtaxnodiscount'] + $gSession['order']['ordertotalshippingsellbeforediscount'];
        }
        else
        {
        	$orderTotal = $gSession['order']['ordertotalitemsellnotaxnodiscount'] + $gSession['order']['ordertotalshippingsellbeforediscount'];
        }


        return $orderTotal;
    }

    static function getTaxCodeList()
    {
        $resultArray = DatabaseObj::getTaxRatesList();

        return $resultArray;
    }

    static function getTermsAndConditions()
    {
        global $gSession;

        $result['template'] = $_GET['template'];

        if (array_key_exists('mobile', $_GET))
        {
            $result['ismobile'] = $_GET['mobile'];
        }
        else
        {
            $result['ismobile'] = $gSession['ismobile'];
        }

        return $result;
    }

    static function changeBillingAddressDisplay()
    {
        return Order_model::changeBillingAddressDisplay();
    }

    static function changeBillingAddress()
    {
        return Order_model::changeBillingAddress();
    }

    static function changeShippingMethod()
    {
        Order_model::changeShippingMethod();

        return Order_model::orderRefresh();
    }

    static function changeShippingAddressDisplay()
    {
		return Order_model::changeShippingAddressDisplay();
    }

    static function changeShippingAddress()
    {
    	return Order_model::changeShippingAddress();
    }

	static function updateAccountDetails()
	{
		return Order_model::updateAccountDetails();
	}

    static function copyShippingAddress()
    {
        return Order_model::copyShippingAddress();
    }

    static function changeAddressCancel()
    {
        // re-check the voucher to make sure its usage status hasn't changed
        Order_model::checkVoucher();

        return Order_model::orderRefresh();
    }

    static function selectStoreDisplay()
    {
        global $gSession;

        Order_model::storeOrderMetaData($_GET['stage']);

        $removestore = UtilsObj::getGETParam('removestore','false');

        if ($removestore == 'true')
        {
            $gSession['shipping'][0]['storeid'] = '';
        }

        // re-check the voucher to make sure its usage status hasn't changed
        Order_model::checkVoucher();
        $resultArray['store'] = Order_model::selectStoreDisplay();

        if ($_GET['refreshshipping'] == true)
        {
            Order_model::checkVoucher();

            $resultArray['shipping'] = Order_model::orderRefresh();
        }

        $resultArray['store']['removestore'] = $removestore;

        return $resultArray;
    }

    static function selectStore()
    {
        global $gConstants;

        // re-check the voucher to make sure its usage status hasn't changed
        Order_model::checkVoucher();

        Order_model::selectStore();

        if ($gConstants['taxaddress'] == TPX_TAX_CALCULATION_BY_SHIPPING_ADDRESS)
        {
            Order_model::updateOrderTaxRate();
        }

        Order_model::updateOrderShippingRate();

        Order_model::checkVoucher();

        return Order_model::orderRefresh();
    }

    static function orderContinue()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            Order_model::storeOrderMetaData($_POST['stage']);

            $result = Order_model::orderContinue();
            $result['result'] = true;
        }
        else
        {
            require_once('../Welcome/Welcome_model.php');

            $message = '';
            if (AuthenticateObj::WebSessionActive() == 0)
            {
                $message = 'str_ErrorSessionExpired';
            }

            Welcome_model::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
            $result = array('result' => false, 'message' => $message);
        }

        return $result;
    }

    static function orderBack()
    {
        require_once('../Welcome/Welcome_model.php');

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            Order_model::storeOrderMetaData($_POST['stage']);

            // re-check the voucher to make sure its usage status hasn't changed
            Order_model::checkVoucher();

            $result = Order_model::orderBack();
            $result['result'] = true;
        }
		else
		{
			require_once('../Welcome/Welcome_model.php');

            $message = '';
            if (AuthenticateObj::WebSessionActive() == 0)
            {
                $message = 'str_ErrorSessionExpired';
            }

            Welcome_model::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
            $result = array('result' => false, 'message' => $message);
        }

        return $result;
    }

    static function orderCancel()
    {
        $mainWebSiteURL = Order_model::cancel();

        return $mainWebSiteURL;
    }

    static function setGiftCard()
    {
        if (AuthenticateObj::WebSessionActive() == 1)
        {
            Order_model::setGiftCard();
            $result =  Order_model::orderRefresh();
            $result['result'] = true;
        }
        else
		{
			require_once('../Welcome/Welcome_model.php');

            $message = '';
            if (AuthenticateObj::WebSessionActive() == 0)
            {
                $message = 'str_ErrorSessionExpired';
            }

            Welcome_model::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
            $result = array('result' => false, 'message' => $message);
        }

        return $result;
    }

    static function changeGiftCard()
    {
        $resultArray['action'] = $_GET['action'];

        if ($resultArray['action'] == 'add')
        {
            $resultArray['canuseaccount'] = Order_model::addGiftCard();
        }
        else
        {
            $resultArray['canuseaccount'] = Order_model::deleteGiftCard();
        }

        return $resultArray;
    }

    static function setVoucher()
    {
        global $gSession;

        if (AuthenticateObj::WebSessionActive() == 1)
        {
            Order_model::setVoucher();
            $resultArray = Order_model::orderRefresh();

            if ($_POST['stage'] != 'payment')
            {
                if ((($gSession['order']['voucherdiscountsection'] == 'SHIPPING') || ($gSession['order']['voucherdiscountsection'] == 'TOTAL')) &&
                        ($gSession['order']['voucherstatus'] == 'str_LabelOrderVoucherAccepted'))
                {
                    $resultArray['custominit'] = 'setTimeout("alert(\"' . SmartyObj::getParamValue('Order', 'str_OrderDiscountConfirmation') . '\")", 1000);';
                }
            }

            // update the shipping methods as the order total may have changed causing the shipping method not to be available
            $origShippingMethod = $gSession['shipping'][0]['shippingmethodcode'];
            Order_model::updateOrderShippingRate();
            if ($origShippingMethod != $gSession['shipping'][0]['shippingmethodcode'])
            {
                // the original shipping method is not available so we must go back to the shipping methods list
                $_POST['stage'] = 'qty';
                $resultArray = Order_model::orderContinue();
            }

            $resultArray['result'] = true;
        }
        else
		{
			require_once('../Welcome/Welcome_model.php');

            $message = '';
            if (AuthenticateObj::WebSessionActive() == 0)
            {
                $message = 'str_ErrorSessionExpired';
            }

            Welcome_model::processLogout(TPX_USER_LOGOUT_REASON_SESSION_EXPIRED);
            $resultArray = array('result' => false, 'message' => $message);
        }

        return $resultArray;
    }


	static function updateCompanionQty()
	{
        global $gSession;

		$resultArray = array(
							'result' => true,
							'qty' => 0,
							'companioncode' => '');

		$companionCollectionAndLayoutCode = filter_input(INPUT_POST, 'companioncode');
		$parentOrderLineID = filter_input(INPUT_POST, 'parentorderlineid', FILTER_VALIDATE_INT);
		$companionOrderLineID = filter_input(INPUT_POST, 'companionorderlineid', FILTER_VALIDATE_INT);

		$companionCollectionAndLayoutCodeArray = explode('.', $companionCollectionAndLayoutCode);
		$companionCollectionCode = $companionCollectionAndLayoutCodeArray[0];
		$companionCode = $companionCollectionAndLayoutCodeArray[1];

		$resultArray['companioncode'] = $companionCode;
		$resultArray['targetuniquecompanionid'] = filter_input(INPUT_POST, 'targetuniquecompanionid');

		$qtyToAdd = UtilsObj::getPOSTParam('qtytoadd', 1);

		if ($companionOrderLineID == 0)
		{
			$productCodesArray = array();

			// we need to first pass -1 to get the price to see if it has a minimum quantity.
			// then call the same function again to get the correct qty
			$productPriceArray = DatabaseObj::getProductPrice($companionCode, $gSession['licensekeydata']['groupcode'], $gSession['userdata']['companycode'],
			$gSession['order']['currencyexchangerate'], $gSession['order']['currencydecimalplaces'], -1);

			// if the qty is still -1, force the qty to 1 and recalculate price
			if ($productPriceArray['newqty'] == -1)
			{
				$qtyToAdd = 1;
				$productPriceArray = DatabaseObj::getProductPrice($companionCode, $gSession['licensekeydata']['groupcode'], $gSession['userdata']['companycode'],
							$gSession['order']['currencyexchangerate'], $gSession['order']['currencydecimalplaces'], $qtyToAdd);
			}

			$qtyToAdd = $productPriceArray['newqty'];


			// get parenet line item index based on orderlineid
            foreach ($gSession['items'] as $index => $item)
            {
                if ($item['orderlineid'] == $parentOrderLineID)
                {
                    $parentLineItemIndex = $index;
                    break;
                }
            }

			$parentItem = $gSession['items'][$parentLineItemIndex];

			$productArray = DatabaseObj::getProductFromCollectionCodeAndLayoutCode($companionCollectionCode, $companionCode);
			$companionItemArray = AuthenticateObj::createSessionOrderLine();
			$orderLineID = Order_model::getNextOrderLineId();

			// Update the page sizes of the companion album, fixing to 6 decimal places.
			$companionPageWidth = UtilsObj::scaleNumberDown($productArray['productpagewidth'], 6);
			$companionPageHeight = UtilsObj::scaleNumberDown($productArray['productpageheight'], 6);

			$companionItemArray['orderlineid'] = $orderLineID;
			$companionItemArray['source'] = $parentItem['source'];
			$companionItemArray['productoptions'] = $parentItem['productoptions'];
			$companionItemArray['itemuploadgroupcode'] = $parentItem['itemuploadgroupcode'];
			$companionItemArray['itemuploadorderid'] = $parentItem['itemuploadorderid'];
			$companionItemArray['itemuploadordernumber'] = $parentItem['itemuploadordernumber'];
			$companionItemArray['itemuploadorderitemid'] = $parentItem['itemuploadorderitemid'];
			$companionItemArray['itemuploadbatchref'] =  $parentItem['itemuploadbatchref'];
			$companionItemArray['itemuploadref'] =  $parentItem['itemuploadref'];
			$companionItemArray['itemproductcollectioncode'] = $companionCollectionCode;
			$companionItemArray['itemproductcollectionname'] = $productArray['collectionname'];
			$companionItemArray['itemproductcode'] = $companionCode;
			$companionItemArray['itemproductskucode'] = $productArray['skucode'];
			$companionItemArray['itemproductname'] = $productArray['name'];
			$companionItemArray['itemproducttype'] = $parentItem['itemproducttype'];
			$companionItemArray['itemproductpageformat'] = $parentItem['itemproductpageformat'];
			$companionItemArray['itemproductspreadpageformat'] = $parentItem['itemproductspreadpageformat'];
			$companionItemArray['itemproductcover1format'] = $parentItem['itemproductcover1format'];
			$companionItemArray['itemproductcover2format'] = $parentItem['itemproductcover2format'];
			$companionItemArray['itemproductoutputformat'] = $parentItem['itemproductoutputformat'];
			$companionItemArray['itemproductheight'] = $companionPageHeight;
			$companionItemArray['itemproductwidth'] = $companionPageWidth;
			$companionItemArray['itemproductdefaultpagecount'] = $productArray['defaultpagecount'];
			$companionItemArray['itemprojectref'] = $parentItem['itemprojectref'];
			$companionItemArray['itemprojectreforig'] = $parentItem['itemprojectreforig'];
			$companionItemArray['itemprojectname'] = $parentItem['itemprojectname'];
			$companionItemArray['itemprojectstarttime'] = $parentItem['itemprojectstarttime'];
			$companionItemArray['itemprojectduration'] = $parentItem['itemprojectduration'];
			$companionItemArray['itempagecount'] = $parentItem['itempagecount'];
			$companionItemArray['itemproducttaxlevel'] = $productArray['taxlevel'];
			$companionItemArray['itemunitcost'] = $productArray['unitcost'];
			$companionItemArray['itemproductunitweight'] = $productArray['weight'];
			$companionItemArray['itemqty'] = $qtyToAdd;
			$companionItemArray['itemuploadappversion'] = $parentItem['itemuploadappversion'];
			$companionItemArray['itemuploadappplatform'] = $parentItem['itemuploadappplatform'];
			$companionItemArray['itemuploadappcputype'] = $parentItem['itemuploadappcputype'];
			$companionItemArray['itemuploadapposversion'] = $parentItem['itemuploadapposversion'];
			$companionItemArray['itemexternalassets'] = $parentItem['itemexternalassets'];
			$companionItemArray['pictures'] = $parentItem['pictures'];
			$companionItemArray['calendarcustomisations'] = $parentItem['calendarcustomisations'];
			$companionItemArray['previewsonline'] = $parentItem['previewsonline'];
			$companionItemArray['canupload'] = $parentItem['canupload'];
			$companionItemArray['itemuploaddatasize'] = $parentItem['itemuploaddatasize'];
			$companionItemArray['itemuploadduration'] = $parentItem['itemuploadduration'];
			$companionItemArray['metadata'] = Array();
			$companionItemArray['covercode'] = '';
            $companionItemArray['papercode'] = '';
            $companionItemArray['itemproductcollectionorigownercode'] = $parentItem['itemproductcollectionorigownercode'];
            $companionItemArray['itemaimode'] = $parentItem['itemaimode'];
			$companionItemArray['assetid'] = 0;
			$companionItemArray['origorderitemid'] = 0;
			$companionItemArray['parentorderitemid'] = $parentItem['orderlineid'];

			// depending on the order of actions the array can be created by the pricing engine so check if it exists and isn't empty
			if ((array_key_exists("aicomponent", $parentItem)) && (!empty($parentItem['aicomponent'])))
			{
				$companionItemArray['aicomponent'] = $parentItem['aicomponent'];
				// force the used flag to be true so that it is evaluated by the pricing engine.
				$companionItemArray['aicomponent']['used'] = true;
			}

            // Check if the product is linked
            $productLinkingArray = DatabaseObj::getApplicableProductLinkCode($companionCode);

            if ($productLinkingArray['linkedcode'] != '')
            {
                $companionItemArray['componenttreeproductcode'] = $productLinkingArray['linkedcode'];
            }
            else
            {
                $companionItemArray['componenttreeproductcode'] = $companionCode;
            }

			$newCompanionArray = array($companionItemArray);
			$newCompanionOrderLineID = $newCompanionArray[0]['orderlineid'];

			// insert the companion line item after the parent in the order items array.
			array_splice($gSession['items'], $parentLineItemIndex + 1, 0, $newCompanionArray);

			// we now need to build the companion items component structure so we can recalculate the item and order totals correctly
			Order_model::buildOrderLineComponentStructure($productCodesArray, $gSession['order']['currencyexchangerate'], $gSession['order']['currencydecimalplaces'], $parentLineItemIndex + 1);

			// After adding the companion line item we need to rebuild the order foot sections as the companion might habve order footer components attached.
			$tempSectionArray = Order_model::getOrderFooterSectionData(array(), $gSession['order']['currencyexchangerate'], $gSession['order']['currencydecimalplaces']);
		}
		else
		{
			// get orderline index based on id
            foreach ($gSession['items'] as $index => $item)
            {
                if ($item['orderlineid'] == $companionOrderLineID)
                {
                    $companionItemIndex = $index;
                    $orderLineID = $item['orderlineid'];
                    break;
                }
            }

            $companionOrderItem = &$gSession['items'][$companionItemIndex];

			if ($qtyToAdd < 1)
			{
				array_splice($gSession['items'], $companionItemIndex, 1);
				$orderLineID = 0;
				$qtyToAdd = 0;
			}
			else
			{
				$productPriceArray = DatabaseObj::getProductPrice($companionCode, $gSession['licensekeydata']['groupcode'],
					$gSession['userdata']['companycode'], $gSession['order']['currencyexchangerate'], $gSession['order']['currencydecimalplaces'], $qtyToAdd);

				$qtyToAdd = $productPriceArray['newqty'];
			}

            $companionOrderItem['itemqty'] = $qtyToAdd;

            //  need to empty and then rebuild the order footer sections as the quantities have changed.
			$gSession['order']['orderFooterSections'] = array();

			$tempSectionArray = Order_model::getOrderFooterSectionData(array(), $gSession['order']['currencyexchangerate'], $gSession['order']['currencydecimalplaces']);
		}

		Order_model::updateAllOrderSections();
		Order_model::updateOrderTaxRate();
		Order_model::updateOrderShippingRate();
		Order_model::updateOrderTotal();

		DatabaseObj::updateSession();

		// we need to build the data for the order summary panel on the companion selection screen
		// this is to prevent another ajax call from occurring like other places in the Taopix cart.
		$stage = $gSession['order']['currentstage'];

        $showPricesWithTax = ($gSession['order']['showpriceswithtax'] == 1) ? true : false;

        if ($showPricesWithTax == true)
        {
            $itemTotal = $gSession['order']['ordertotalitemsellwithtaxnodiscount'];
            $shippingCost = $gSession['order']['ordertotalshippingsellbeforediscount'];
            $orderTotal = $gSession['order']['ordertotalbeforediscount'];
            $orderTotal = $itemTotal + $shippingCost;
        }
        else if ($showPricesWithTax == false)
        {
            $itemTotal = $gSession['order']['ordertotalitemsellnotaxnodiscount'];
            $shippingCost = $gSession['order']['ordertotalshippingsellbeforediscount'];
            $orderTotal = $gSession['order']['ordertotalbeforediscount'];
            $orderTotal = $itemTotal + $shippingCost;
        }


		$resultArray['qty'] = $qtyToAdd;
		$resultArray['companionorderlineid'] = $orderLineID;
		$resultArray['itemtotal'] = $itemTotal;
		$resultArray['shippingcost'] = $shippingCost;
		$resultArray['ordertotal'] = $orderTotal;

		return $resultArray;
    }
    static function getCCIRecord($pSessionRef)
    {
        $result = Order_model::checkCCIRecordExists($pSessionRef);

        return $result;
    }
    static function processPaymentToken($pCCIType, $pPaymentToken)
    {
        global $gSession;

        require_once('../Order/PaymentIntegration/PaymentIntegration.php');

        $gateway = PaymentIntegrationObj::createPaymentGatewayInstanceReferenced($gSession, $pCCIType);
        $processPaymentTokenResultArray = $gateway->processPaymentToken($pPaymentToken);

        return $processPaymentTokenResultArray;
    }

    /**
     * Check that the potential user login in not already in use on another account.
     */
    static function processUserLoginUniqueCheck($pLoginToCheck)
    {
        global $gSession;

        $resultArray = array('result' => '', 'resultparam' => '');

        if ($pLoginToCheck != '')
        {
			// Get the brand code and user id from the session.
			$userid = $gSession['userid'];
			$webBrandCode = $gSession['webbrandcode'];

            // If the new login is not empty, perform the check. checkLoginUnique() returns 
            // an empty string if no error occurs, or no account is found to be using the login.
            $resultArray = DatabaseObj::checkLoginUnique($webBrandCode, $pLoginToCheck);
        }

        // Check the result of the unique login check, or that the email was not passed.
		// Other errors, such as DB errors are not replaced.
        if (('str_ErrorAccountExists' == $resultArray['result']) ||
            ('str_MessageCompulsoryEmaiInvalid' == $resultArray['result']))
        {
            // If the result was that the login is already in use or that the email was missing, send back the message in the correct language.
            $smarty = SmartyObj::newSmarty('', $gSession['webbrandcode'], '', $gSession['browserlanguagecode']);

            $resultArray['resultparam'] = $smarty->get_config_vars($resultArray['result']);
        }

        return $resultArray;
    }

    static function keepOnlineProject(string $projectRef, array $ac_config): array
	{
		require_once "../libs/internal/curl/Curl.php";
		$result = [
			'status' => false,
			'projectref' => $projectRef,
		];

		$serverURL = $ac_config['TAOPIXONLINEURL'];

		$dataToEncrypt = array('cmd' => 'KEEPONLINEPROJECT', 'data' => array('projectref' => $projectRef));
		$processResult = CurlObj::sendByPut($serverURL, 'ProjectAPI.callback', $dataToEncrypt);

		// Error code sent back from online will be in 0 on success, int > 0 on error
		if ('' === $processResult['data']['error']) {
			$result['status'] = $processResult['data']['status'];
		}

		return $result;
	}

	static function purgeFlaggedProjects(int $userId, array $ac_config): array
	{
		require_once "../libs/internal/curl/Curl.php";
		$resultArray = [
			'status' => false,
		];

		$serverURL = $ac_config['TAOPIXONLINEURL'];

		$dataToEncrypt = [
			'cmd' => 'PURGEPROJECTSNOW',
			'data' => [
				'userid' => $userId,
			],
		];

		$processResult = CurlObj::sendByPut($serverURL, 'ProjectAPI.callback', $dataToEncrypt);

		// Error code sent back from online will be in 0 on success, int > 0 on error
		if ('' === $processResult['data']['error']) {
			$resultArray['success'] = $processResult['data']['success'];
		}

		return $resultArray;
	}
}
?>