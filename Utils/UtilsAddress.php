<?php

function normalize($string)
{
    // replace accented and otherwise modified Latin characters into 'plain' versions
    // only works on Latin alphabets

    $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåāǎæçèéêëēěìíîïīǐðñòóôõöōǒøùúûūǔüǖǘǚǜýýÿŔŕŠŒŽšœžŸ¥';
    $b = 'aaaaaaaceeeeiiiidnoooooouuuuysaaaaaaaaaceeeeeeiiiiiidnoooooooouuuuuuuuuuyyyRrSOZsozYY';
    $string = mb_convert_encoding($string, 'ISO-8859-1', 'UTF8');
    $string = strtr($string, mb_convert_encoding($a, 'ISO-8859-1', 'UTF8'), $b);
    $string = strtolower($string);

    return mb_convert_encoding($string, 'UTF8', 'ISO-8859-1');
}

function regionListSort($a, $b)
{
    // custom sort function to sort region names into the correct order depending on locale
    // this has to sit outside the object

    $n = normalize($a['sort']);
    $m = normalize($b['sort']);

    return strcmp($n, $m);
}

class UtilsAddressObj
{

    static function builtFullShippingCountryDropDownList()
    {
        global $gSession;

        // return a list of all countries that haven't been used in the current shipping zones
        $result = 'ZONECOUNTRY';
        $resultArray = array();
        $countries = array();

        /*
          $zoneID = $_GET['zoneid']; // current shipping zone code
          $otherCountriesList = $_GET['zonecodes']; // countries/regions used on current page (current zone)
          $country = $_GET['country']; // current country
          $usedCountries = explode(",", $otherCountriesList);
         */

        // we now have an array of either <country> or <country>_<region>
        // some countries have regions, other do not
        // e.g. NO,AL,US_TX,US_AL,GB_DEVON,GB_FIVE,GR,CH
        // this is the list of all countries/regions used in current shipping zones,

        $countries = array(); // collect countrycode / name pairs
        $countryArray = array(); // temporary array to hold code so we can check if already used
        $item = array(); // country item to be added
        // get list of all country/region combinations possible
        // then use only those that haven't been used already
        $combinationList = UtilsAddressObj::getCombinedCountryRegionList(false, $gSession['browserlanguagecode']);

        foreach($combinationList as $zone)
        {
            // only add if zone not in usedCountries
            // or already in countryArray (don't want duplicates)
            //if (!in_array($zone['zonecode'],$usedCountries) && !in_array($zone['countrycode'],$countryArray))
            //{
            $item['isocode2'] = $zone['countrycode'];
            $item['name'] = $zone['countryname'];
            array_push($countries, $item);
            $countryArray[] = $zone['countrycode'];
            //}
        }

        $resultArray['result'] = $result;
        //$resultArray['country'] = $country;
        $resultArray['othercountries'] = $countries;

        return $resultArray;
    }

    static function getRegionCodeFromCode($pCountryCode, $pState, $pCounty)
    // try and find a match for a state or county of that country
    // assume that state or county is actually region code,
    // i.e. the customer has entered 'CA' and not 'California'
    {
        $bestCode = ''; // region code of closest match
        $bestName = ''; // region name of closest match

        $resultArray = Array();

        $countryDate = self::getCountry($pCountryCode);
        $region = $countryDate['region'];
        if ($region == 'STATE')
        {
            $regionValue = $pState;
        }
        else
        {
            $regionValue = $pCounty;
        }
        $regionValue = strtoupper(trim($regionValue));

        $regionList = self::getRegionList($pCountryCode);
        // loop around and find a match
        $regionCount = count($regionList);
        for($i = 0; $i < $regionCount; $i++)
        {
            $regionCode = strtoupper($regionList[$i]['code']);

            if ($regionValue == $regionCode)
            {
                $bestCode = $regionList[$i]['code'];
                $bestName = $regionList[$i]['name'];
                break;
            }
        }

        $resultArray['regioncode'] = $bestCode;
        $resultArray['regionname'] = $bestName;
        $resultArray['region'] = $region;

        return $resultArray;
    }

    static function getRegionCodeFromName($pCountryCode, $pState, $pCounty)
    // try and find a match for a state or county of that country
    {
        $bestDistance = 1024; // smallest Levenshtein distance
        $bestCode = ''; // region code of closest match
        $bestName = ''; // region name of closest match

        $resultArray = Array();

        $countryDate = self::getCountry($pCountryCode);
        $region = $countryDate['region'];
        if ($region == 'STATE')
        {
            $regionValue = $pState;
        }
        else
        {
            $regionValue = $pCounty;
        }

        // if have have a region value attempt to match or perform a best match
        if ($regionValue != '')
        {
            $regionValue = strtolower(trim($regionValue));

            $regionList = self::getRegionList($pCountryCode);
            // loop around and find a match
            $regionCount = count($regionList);
            for($i = 0; $i < $regionCount; $i++)
            {
                $regionName = strtolower($regionList[$i]['name']);

                if ($regionValue == $regionName)
                {
                    $bestCode = $regionList[$i]['code'];
                    $bestName = $regionList[$i]['name'];
                    break;
                }

                $distance = levenshtein($regionValue, $regionName);

                if (($distance != -1) && ($distance < $bestDistance))
                {
                    $bestDistance = $distance;
                    $bestCode = $regionList[$i]['code'];
                    $bestName = $regionList[$i]['name'];
                    if ($distance == 0)
                    {
                        break;
                    }
                }

                if (strlen($regionName) > strlen($regionValue))
                {
                    $regionName = substr($regionName, 1, strlen($regionValue));
                    $distance = levenshtein($regionValue, $regionName);

                    if (($distance != -1) && ($distance < $bestDistance))
                    {
                        $bestDistance = $distance;
                        $bestCode = $regionList[$i]['code'];
                        $bestName = $regionList[$i]['name'];
                        if ($distance == 0)
                        {
                            break;
                        }
                    }
                }
            }
        }

        $resultArray['regioncode'] = $bestCode;
        $resultArray['regionname'] = $bestName;
        $resultArray['region'] = $region;

        return $resultArray;
    }

    static function getCountry($pCountryCode)
    // return row from COUNTRIES for given country
    // pCountryCode is two-character country code
    {
        $id = -1;
        $name = '';
        $isocode2 = '';
        $isocode3 = '';
        $region = '';
        $displayfields = '';
        $compulsoryfields = '';
        $displayformat = '';
        $fieldlabels = '';
        $addressformatid = 0;

        $result = '';
        $resultParam = '';
        $resultArray = Array();

        $dbObj = DatabaseObj::getConnection();
        if ($dbObj)
        {
            $stmt = $dbObj->prepare('SELECT `id`, `name`, `isocode2`, `isocode3`, `region`, `displayfields`, `compulsoryfields`,
                                        `displayformat`, `fieldlabels`, `addressformatid`
                                        FROM `COUNTRIES`
                                        WHERE `isocode2` = ?');
            if ($stmt)
            {
                if ($stmt->bind_param('s', $pCountryCode))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($id, $name, $isocode2, $isocode3, $region, $displayfields, $compulsoryfields,
                                                $displayformat, $fieldlabels, $addressformatid))
                                {
                                    $stmt->fetch();
                                }
                                else
                                {
                                    // could not bind result
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'getCountry bind result ' . $dbObj->error;
                                }
                            }
                        }
                        else
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'getCountry store result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'getCountry execute ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not bind parameters
                    $result = 'str_DatabaseError';
                    $resultParam = 'getCountry bind params ' . $dbObj->error;
                }
            }
            else
            {
                // could not prepare statement
                $result = 'str_DatabaseError';
                $resultParam = 'getCountry prepare ' . $dbObj->error;
            }

            if ($stmt)
            {
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }

            $dbObj->close();
        }

        $resultArray['id'] = $id;
        $resultArray['name'] = $name;
        $resultArray['isocode2'] = $isocode2;
        $resultArray['isocode3'] = $isocode3;
        $resultArray['region'] = $region;
        $resultArray['displayfields'] = $displayfields;
        $resultArray['compulsoryfields'] = $compulsoryfields;
        $resultArray['displayformat'] = $displayformat;
        $resultArray['fieldlabels'] = $fieldlabels;
        $resultArray['addressformatid'] = $addressformatid;

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
    }

    static function getRegionList($pCountryCode)
    {
        // returns an array containing the TAOPIX regions for a given country
        // sorted by region name within region group, e.g. counties within England, Wales, Scotland, &c.
        // includes 'region group', that, when present, is used for <optgroup> in the <select> element
        global $gSession;

        $regionList = Array();
        $result = '';

        $dbObj = DatabaseObj::getConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `regioncode`, `regionname` , COALESCE(`regiongroupname`,""), COALESCE(`sortorder`,"0000")
										 FROM COUNTRYREGION cr LEFT JOIN COUNTRYREGIONGROUP crg
										 ON cr.countrycode=crg.countrycode AND cr.regiongroupcode=crg.regiongroupcode
										 WHERE (cr.countrycode = ?)'))
            {
                if ($stmt->bind_param('s', $pCountryCode))
                {
                    if ($stmt->bind_result($regionCode, $regionName, $regionGroupName, $sortOrder))
                    {

                        if (!$stmt->execute())
                        {
                            // could not execute statement
                            $result = 'str_DatabaseError';
                            $resultParam = 'getRegionList execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not bind result
                        $result = 'str_DatabaseError';
                        $resultParam = 'getRegionList bind result ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not bind parameters
                    $result = 'str_DatabaseError';
                    $resultParam = 'getRegionList bind params ' . $dbObj->error;
                }
            }
            else
            {
                // could not prepare statement
                $result = 'str_DatabaseError';
                $resultParam = 'getRegionList prepare ' . $dbObj->error;
            }

            if ($result == '')
            {
                // process each region, build array
                while($stmt->fetch())
                {
                    // localise strings
                    $regionName = LocalizationObj::getLocaleString($regionName, UtilsObj::getBrowserLocale(), true);
                    $regionGroupName = LocalizationObj::getLocaleString($regionGroupName, UtilsObj::getBrowserLocale(), true);

                    $regionItem['code'] = $regionCode;
                    $regionItem['name'] = $regionName;
                    $regionItem['group'] = $regionGroupName;
                    $regionItem['sort'] = $sortOrder . '_' . $regionName;

                    array_push($regionList, $regionItem);
                }

                usort($regionList, 'regionListSort');
            }

            if ($stmt)
            {
                $stmt->free_result();
                $stmt->close();
            }

            $dbObj->close();
        }

        return $regionList;
    }

    static function getCountryList()
    {
        // return an array containing the TAOPIX countries
        $countryList = Array();

        $dbObj = DatabaseObj::getConnection();
        if ($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `id`, `name`, `isocode2`, `isocode3`,
												`region`, `displayfields`, `compulsoryfields`, `displayformat`, `fieldlabels`
										 FROM `COUNTRIES` ORDER BY `name`'))
            {
                if ($stmt->bind_result($id, $name, $isocode2, $isocode3, $region, $displayFields, $compulsoryFields, $displayFormat,
                                $fieldLabels))
                {
                    if ($stmt->execute())
                    {
                        while($stmt->fetch())
                        {
                            $countryItem['id'] = $id;
                            $countryItem['name'] = $name;
                            $countryItem['isocode2'] = $isocode2;
                            $countryItem['isocode3'] = $isocode3;
                            $countryItem['region'] = $region;
                            $countryItem['displayfields'] = $displayFields;
                            $countryItem['compulsoryfields'] = $compulsoryFields;
                            $countryItem['displayformat'] = $displayFormat;
                            $countryItem['fieldlabels'] = $fieldLabels;
                            array_push($countryList, $countryItem);
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
            }
            $dbObj->close();
        }

        return $countryList;
    }

    static function getCountryNameFromCode($pCountryCode, $pCountryList = Array())
    {
        // return the country name based on to iso country code

        $countryName = '';

        if (count($pCountryList) == 0)
        {
            $pCountryList = self::getCountryList();
        }

        $itemCount = count($pCountryList);
        for($i = 0; $i < $itemCount; $i++)
        {
            if ($pCountryList[$i]['isocode2'] == $pCountryCode)
            {
                $countryName = $pCountryList[$i]['name'];
                break;
            }
        }

        return $countryName;
    }

    static function getCountryListFromCodes($pCountryArray, $pCountryCodes, $pBreakCount = 0)
    {
        // return a html formatted string of country names from the supplied comma separated list of country codes

        $result = '';
        $theCodeList = explode(',', $pCountryCodes);
        $breakCount = 0;

        $codeCount = count($theCodeList);
        $breakCount = 0;
        for($i = 0; $i < $codeCount; $i++)
        {
            $theCode = $theCodeList[$i];
            $itemCount = count($pCountryArray);
            for($j = 0; $j < $itemCount; $j++)
            {
                if ($pCountryArray[$j]['isocode2'] == $theCode)
                {
                    $result .= $pCountryArray[$j]['name'];
                    if ($i < ($codeCount - 1))
                    {
                        $result .= ', ';
                    }
                    $breakCount++;
                    if ($breakCount == $pBreakCount)
                    {
                        $result .= '<br>';
                        $breakCount = 0;
                    }
                    break;
                }
            }
        }

        $result = trim($result, ', ');
        $result = ($result == '' ? '&nbsp;' : $result); // fix empty cell problem in ms-ie

        return $result;
    }

    static function getTaxCountryListFromCodes($pCountryCodes, $pCombinedList)
    {
        // return a html formatted string of country names from the supplied comma separated list of country codes

        global $gSession;

        $result = '';
        $lastCountry = '';

        $combinedList = $pCombinedList;

        $theCodeList = explode(',', $pCountryCodes);
        sort($theCodeList);

        $codeCount = count($theCodeList);
        for($i = 0; $i < $codeCount; $i++)
        {
            $theCode = $theCodeList[$i];
            $itemCount = count($combinedList);
            for($j = 0; $j < $itemCount; $j++)
            {
                if ($combinedList[$j]['zonecode'] == $theCode)
                {
                    if ($combinedList[$j]['countrycode'] == $combinedList[$j]['zonecode'])
                    {
                        $result .= $combinedList[$j]['countryname'];
                    }
                    else
                    {
                        if ($combinedList[$j]['countrycode'] != $lastCountry)
                        {
                            $result .= $combinedList[$j]['countryname'] . '<br>';
                        }
                        $result .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $combinedList[$j]['regionname'];
                        $lastCountry = $combinedList[$j]['countrycode'];
                    }

                    if ($i < ($codeCount - 1))
                    {
                        $result .= '<br>';
                    }
                    break;
                }
            }
        }

        $result = trim($result, ', ');
        $result = ($result == '' ? '&nbsp;' : $result); // fix empty cell problem in ms-ie

        return $result;
    }

    static function getAddressDisplayFormat($pCountryCode)
    // return display format of given country
    {
        // default address format
        $displayFormat = '[firstname] [lastname]<br>[company]<br>[add1]<br>[add2]<br>[add3]<br>[add4]<br>[city]<br>[county]<br>[state]<br>[postcode]<br>[country]';

        $dbObj = DatabaseObj::getConnection();
        if ($dbObj)
        {
            $stmt = $dbObj->prepare('SELECT `displayformat`
                                        FROM `COUNTRIES`
                                        WHERE `isocode2` = ?
                                        AND `displayformat` <> ""');
            if ($stmt)
            {
                if ($stmt->bind_param('s', $pCountryCode))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($displayFormat))
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

        return $displayFormat;
    }

    /**
     * Modifies POST array in such a way that additional address fields are stored.
     * in the correct columns
     *
     * A line like add1=[add41], [add42] - [add43]
     * will result in the values of fields add41, add42 and add43 to be stored
     * in address1 as $add41 . ', ' . $add42 . ' - ' . $add43
     * and also
     * in address4 as $add41 . '<p>' . $add42 . '<p>' . $add43
     * The address fields address1, address2, address3, address4 can then be read from
     * the POST array as usual.
     * Only add41 add42 and add43 are recognised as additional address fields.
     * The field name in front of the equal sign can be any og the usual
     * address fields, except address4.
     *
     * @since Version 2.5.3
     * @author Steffen Haugk
     * @return array
     * 'active' field is TRUE when both conditions are met, and FALSE otherwise.
     */
    static function specialAddressFields($countryCode)
    {
        // non-ExtJS and ExtJS forms use different address field names
        $addArray['[firstname]'] = UtilsObj::getPOSTParam('contactfname', UtilsObj::getPOSTParam('contactFirstName'));
        $addArray['[lastname]'] = UtilsObj::getPOSTParam('contactlname', UtilsObj::getPOSTParam('contactLastName'));
        $addArray['[company]'] = UtilsObj::getPOSTParam('companyname', UtilsObj::getPOSTParam('companyName'));
        $addArray['[add1]'] = UtilsObj::getPOSTParam('address1');
        $addArray['[add2]'] = UtilsObj::getPOSTParam('address2');
        $addArray['[add3]'] = UtilsObj::getPOSTParam('address3');
        $addArray['[add4]'] = UtilsObj::getPOSTParam('address4');
        $addArray['[add41]'] = UtilsObj::getPOSTParam('add41');
        $addArray['[add42]'] = UtilsObj::getPOSTParam('add42');
        $addArray['[add43]'] = UtilsObj::getPOSTParam('add43');
        $addArray['[city]'] = UtilsObj::getPOSTParam('city');
        $addArray['[county]'] = UtilsObj::getPOSTParam('county', UtilsObj::getPOSTParam('countyName'));
        $addArray['[state]'] = UtilsObj::getPOSTParam('state', UtilsObj::getPOSTParam('stateName'));
        $addArray['[regioncode]'] = UtilsObj::getPOSTParam('regioncode', UtilsObj::getPOSTParam('regionCode'));
        $addArray['[region]'] = UtilsObj::getPOSTParam('region');
        $addArray['[postcode]'] = UtilsObj::getPOSTParam('postcode', UtilsObj::getPOSTParam('postCode'));
        $addArray['[country]'] = UtilsObj::getPOSTParam('countryname', UtilsObj::getPOSTParam('countryName'));

        $country = self::getCountry($countryCode);
        $fields = $country['displayfields'];

        if ($fields <> '')
        {
            $displayFields = explode('<p>', $fields);
            foreach($displayFields as $field)
            {
                if (strpos($field, '=') !== false)
                {
                    // this is for lines like "add1=[add41], [add42] - [add43]"
                    list($head, $tail) = explode('=', $field, 2);
                    $tail = self::formatAddressData($addArray, $tail, '<p>');
                    switch($head)
                    {
                        case 'firstname':
                            $_POST['contactfname'] = $tail;
                            break;
                        case 'lastname':
                            $_POST['contactlname'] = $tail;
                            break;
                        case 'company':
                            $_POST['companyname'] = $tail;
                            break;
                        case 'add1':
                            $_POST['address1'] = $tail;
                            break;
                        case 'add2':
                            $_POST['address2'] = $tail;
                            break;
                        case 'add3':
                            $_POST['address3'] = $tail;
                            break;
                        case 'country':
                            $_POST['countryname'] = $tail;
                            break;
                        default:
                            $_POST[$head] = $tail;
                            break;
                    }
                    $_POST['address4'] = $_POST['add41'] . '@@TAOPIXTAG@@' . $_POST['add42'] . '@@TAOPIXTAG@@' . $_POST['add43'];
                }
            }
        }
    }

    static function getAdditionalAddressFields($pCountryCode, $pAddress4)
    {
        $resultArray = Array();
        $resultArray['add41'] = '';
        $resultArray['add42'] = '';
        $resultArray['add43'] = '';

        // we need to see if column address4 holds additional parts
        // we can only do this by examining the address format
        $country = UtilsAddressObj::getCountry($pCountryCode);
        $fields = $country['displayfields'];
        if ($fields <> '')
        {
            $displayFields = explode('<p>', $fields);
            foreach($displayFields as $field)
            {
                if (strpos($field, '=') !== false)
                {
                    // this is for lines like "add1=[add41], [add42] - [add43]"
                    list($head, $tail) = explode('=', $field, 2);
                    $additionalFields = explode('<p>', $pAddress4);
                    switch(count($additionalFields))
                    {
                        case 3:
                            $resultArray['add43'] = $additionalFields[2];
                        case 2:
                            $resultArray['add42'] = $additionalFields[1];
                        case 1:
                            $resultArray['add41'] = $additionalFields[0];
                    }
                }
            }
        }

        return $resultArray;
    }

    static function formatAddressData($pAddressArray, $pAddressFormat, $pDelimiter)
    {
        $address = $pAddressFormat;

        // temporarily replace delimiter so that any tag is preceeded by a ']'
        $address = str_replace('<br>', '[br]', $address);

        foreach($pAddressArray as $field => $value)
        {
            $pos = strpos($address, $field);
            if (($value == '') && ($pos > -1))
            {
                // if there is no value for the tag, remove preceeding characters
                // e.g. "[add1], [add2]" becomes "[add1]" if add2 is empty (", " removed)
                while(substr($address, $pos - 1, 1) != ']') // either tag or line break
                {
                    $address = substr($address, 0, $pos - 1) . substr($address, $pos);
                    $pos = strpos($address, $field);
                }
            }
        }

        foreach($pAddressArray as $field => $value)
        {
            // replace tags with values
            $address = str_replace($field, UtilsObj::escapeInputForHTML($value), $address);
        }

        // replace delimiter
        $address = str_replace('[br]', $pDelimiter, $address);

        // remove trailing spaces
        while(strpos($address, ' ' . $pDelimiter) !== false) $address = str_replace(' ' . $pDelimiter, $pDelimiter, $address);

        // remove empty lines
        while(strpos($address, $pDelimiter . $pDelimiter) !== false)
                $address = str_replace($pDelimiter . $pDelimiter, $pDelimiter, $address);

        // remove leading line breaks
        if (substr($address, 0, strlen($pDelimiter)) == $pDelimiter) $address = substr($address, strlen($pDelimiter));

        // if address consists of line break only, clear the address
        if ($address == $pDelimiter) $address = '';

        return $address;
    }

    static function formatAddress($pAddressArray, $pFieldPrefix, $pDelimiter)
    {
        $addArray = Array();

        // format either the billing or shipping order address
        // the delimiter can be used to format it for HTML or text emails
        $address = self::getAddressDisplayFormat($pAddressArray[$pFieldPrefix . 'customercountrycode']);

        $addArray['[firstname]'] = $pAddressArray[$pFieldPrefix . 'contactfirstname'];
        $addArray['[lastname]'] = $pAddressArray[$pFieldPrefix . 'contactlastname'];
        $addArray['[company]'] = $pAddressArray[$pFieldPrefix . 'customername'];
        $addArray['[add1]'] = $pAddressArray[$pFieldPrefix . 'customeraddress1'];
        $addArray['[add2]'] = $pAddressArray[$pFieldPrefix . 'customeraddress2'];
        $addArray['[add3]'] = $pAddressArray[$pFieldPrefix . 'customeraddress3'];
        $addArray['[add4]'] = $pAddressArray[$pFieldPrefix . 'customeraddress4'];
        $addArray['[city]'] = $pAddressArray[$pFieldPrefix . 'customercity'];
        $addArray['[county]'] = $pAddressArray[$pFieldPrefix . 'customercounty'];
        $addArray['[state]'] = $pAddressArray[$pFieldPrefix . 'customerstate'];
        $addArray['[regioncode]'] = $pAddressArray[$pFieldPrefix . 'customerregioncode'];
        $addArray['[region]'] = $pAddressArray[$pFieldPrefix . 'customerregion'];
        $addArray['[postcode]'] = $pAddressArray[$pFieldPrefix . 'customerpostcode'];
        $addArray['[country]'] = $pAddressArray[$pFieldPrefix . 'customercountryname'];

        return self::formatAddressData($addArray, $address, $pDelimiter);
    }

    static function getPanelCountryList($combinedList)
    {
        global $gConstants, $gSession;

        $list = array();
        $item = array();
        for($i = 0; $i < count($combinedList); $i++)
        {
            if (strrpos($combinedList[$i]['zonecode'], '_') === false)
            {
                $item['countryCode'] = $combinedList[$i]['countrycode'];
                $item['countryName'] = $combinedList[$i]['countryname'];
                $item['regionLabel'] = $combinedList[$i]['regionlabel'];
                $item['hasRegions'] = ($combinedList[$i]['hasregions'] > 0) ? true : false;
                array_push($list, $item);
            }
        }

        return $list;
    }

    static function getUsedCountryList($pAllCountriesAndRegions, $pUsedCodes, $pOnlyReturnCountries)
    {
        $removeCodes = Array();
        $usedlist = Array();
        $fulllist = Array();

        $usedCodesArray = explode(',', $pUsedCodes);
        $usedCodesCount = count($usedCodesArray);

        $allCountriesAndRegionsCount = count($pAllCountriesAndRegions);

        for($i = 0; $i < $usedCodesCount; $i++)
        {
            $usedCodeItem = &$usedCodesArray[$i];

            if (strpos($usedCodeItem, '_') === false)
            {
                if ($pOnlyReturnCountries == true)
                {
                    // we only want countries so append it's code
                    $removeCodes[] = $usedCodeItem;
                }
                else
                {
                    // append the country and all of it's regions
                    for($j = 0; $j < $allCountriesAndRegionsCount; $j++)
                    {
                        if ($pAllCountriesAndRegions[$j]['countrycode'] == $usedCodeItem)
                        {
                            $removeCodes[] = $pAllCountriesAndRegions[$j]['zonecode'];
                        }
                    }
                }
            }
            else
            {
                if ($pOnlyReturnCountries == true)
                {
                    // we only want countries so append it's code
                    $buf = explode('_', $usedCodeItem);
                    $removeCodes[] = $buf[0];
                }
                else
                {
                    // append the country region
                    $removeCodes[] = $usedCodeItem;
                }
            }
        }

        return $removeCodes;
    }

    static function getCombinedCountryRegionList($pIncludeAll, $pLocale = '')
    // return country and region combinations, country and region names
    // pIncludeAll=true:  for countries with regions, return country code on its own as well
    // pIncludeAll=false: for countries with regions, only return country code / region code combinations
    {
        global $gConstants;

        if ($pLocale == '')
        {
            $locale = $gConstants['defaultlanguagecode'];
        }
        else
        {
            $locale = $pLocale;
        }

        $list = Array();
        // list of country and region codes / names used in this zone
        // e.g. NO,AL,US_TX,US_AL,GB_DEVON,GB_FIVE,GR,CH

        $dbObj = DatabaseObj::getConnection();
        if ($dbObj)
        {
            $lastCountry = '';
            $item = Array();
            // get list of all country/region combinations possible
            if ($stmt = $dbObj->prepare('SELECT isocode2, IF(ISNULL(`regioncode`),`isocode2`, CONCAT(`isocode2`,"_",`regioncode`)) AS zones, name, IF(ISNULL(`regioncode`),"",`regionname`) AS regionname, region, displayfields, fieldlabels, (SELECT count(*) FROM COUNTRYREGION WHERE countrycode=isocode2) as hasregions FROM `COUNTRIES` LEFT JOIN `countryregion` ON `isocode2`=`countrycode` ORDER BY `name`'))
            {
                if ($stmt->bind_result($countrycode, $zonecode, $countryname, $regionname, $regionLabel, $displayFields, $fieldLabels,
                                $hasRegions))
                {
                    if ($stmt->execute())
                    {
                        while($stmt->fetch())
                        {
                            $regionTitle = '';
                            $regionLower = strtolower($regionLabel);
                            $displayFieldsArr = explode(',', $displayFields);
                            $fieldLabelsArr = explode(',', $fieldLabels);
                            if (($regionLabel) && (count($displayFieldsArr) > 1))
                            {
                                $position = array_search($regionLower, $displayFieldsArr);

                                if ($position !== false)
                                {
                                    $regionTitle = $fieldLabelsArr[$position];
                                }
                                else
                                {
                                    $regionTitle = '';
                                }
                            }

                            if (($countrycode != $zonecode) && ($countrycode != $lastCountry) && $pIncludeAll)
                            // if this is a composite like <country>_<region>
                            // we still need to include <country>
                            // but only once!
                            {
                                $item['countrycode'] = $countrycode;
                                $item['countryname'] = $countryname;
                                $item['zonecode'] = $countrycode;
                                $item['regionname'] = '';
                                $item['rawregionname'] = '';
                                $item['regionlabel'] = $regionTitle;
                                $item['hasregions'] = $hasRegions;

                                array_push($list, $item);
                            }

                            $item['countrycode'] = $countrycode;
                            $item['countryname'] = $countryname;
                            $item['zonecode'] = $zonecode;
                            $item['regionname'] = LocalizationObj::getLocaleString($regionname, $locale, true);
                            $item['rawregionname'] = $regionname;
                            $item['regionlabel'] = $regionTitle;
                            $item['hasregions'] = $hasRegions;

                            array_push($list, $item);
                            $lastCountry = $countrycode;
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
            }
            $dbObj->close();
        }

        return $list;
    }

	public static function copyArrayAddressFields($pSourceAddressArray, $pSourceKeyPrefix, &$pDestinationArray,
		$pDestinationKeyPrefix, $pCopyingDefaultAddress, $pAddressFieldsOnly)
	{
		if (! $pAddressFieldsOnly)
		{
			// if the source is empty and destination is 'defaultbilling'
			// or if both the source and destination are the billing address copy the tax number settings
			if (($pSourceKeyPrefix == '' && $pDestinationKeyPrefix == 'defaultbilling') ||
				(strpos($pSourceKeyPrefix, 'billing') !== false) && (strpos($pDestinationKeyPrefix, 'billing') !== false))
			{
				$pDestinationArray[$pDestinationKeyPrefix . 'registeredtaxnumbertype'] = $pSourceAddressArray[$pSourceKeyPrefix . 'registeredtaxnumbertype'];
				$pDestinationArray[$pDestinationKeyPrefix . 'registeredtaxnumber'] = $pSourceAddressArray[$pSourceKeyPrefix . 'registeredtaxnumber'];
			}

			$pDestinationArray[$pDestinationKeyPrefix . 'contactfirstname'] = $pSourceAddressArray[$pSourceKeyPrefix . 'contactfirstname'];
			$pDestinationArray[$pDestinationKeyPrefix . 'contactlastname'] = $pSourceAddressArray[$pSourceKeyPrefix . 'contactlastname'];

			if ($pCopyingDefaultAddress)
			{
				$pSourceKeyPrefix .= 'customer';
				$pDestinationArray[$pDestinationKeyPrefix . 'customername'] = $pSourceAddressArray[$pSourceKeyPrefix . 'name'];
			}
			else
			{
				$pDestinationArray[$pDestinationKeyPrefix . 'customername'] = $pSourceAddressArray[$pSourceKeyPrefix . 'companyname'];
			}
		}

		$pDestinationArray[$pDestinationKeyPrefix . 'customeraddress1'] = $pSourceAddressArray[$pSourceKeyPrefix . 'address1'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customeraddress2'] = $pSourceAddressArray[$pSourceKeyPrefix . 'address2'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customeraddress3'] = $pSourceAddressArray[$pSourceKeyPrefix . 'address3'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customeraddress4'] = $pSourceAddressArray[$pSourceKeyPrefix . 'address4'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customercity'] = $pSourceAddressArray[$pSourceKeyPrefix . 'city'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customercounty'] = $pSourceAddressArray[$pSourceKeyPrefix . 'county'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customerstate'] = $pSourceAddressArray[$pSourceKeyPrefix . 'state'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customerregioncode'] = $pSourceAddressArray[$pSourceKeyPrefix . 'regioncode'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customerregion'] = $pSourceAddressArray[$pSourceKeyPrefix . 'region'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customerpostcode'] = $pSourceAddressArray[$pSourceKeyPrefix . 'postcode'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customercountrycode'] = $pSourceAddressArray[$pSourceKeyPrefix . 'countrycode'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customercountryname'] = $pSourceAddressArray[$pSourceKeyPrefix . 'countryname'];

		if (! $pAddressFieldsOnly)
		{
			$pDestinationArray[$pDestinationKeyPrefix . 'customertelephonenumber'] = $pSourceAddressArray[$pSourceKeyPrefix . 'telephonenumber'];
			$pDestinationArray[$pDestinationKeyPrefix . 'customeremailaddress'] = $pSourceAddressArray[$pSourceKeyPrefix . 'emailaddress'];
		}
	}
}
