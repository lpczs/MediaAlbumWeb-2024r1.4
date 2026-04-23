<?php

class LocalizationObj
{
    static function getConstantName($smarty, $value, $type)
    {
        $returnText = '';

        switch ($type)
        {
            case 'VOUCHERTYPE':
            {
                switch ($value)
                {
                    case TPX_VOUCHER_TYPE_DISCOUNT:
                    {
                        $returnText = $smarty->get_config_vars('str_LabelVoucherTypeDISCOUNT');
                        break;
                    }
                    case TPX_VOUCHER_TYPE_PREPAID:
                    {
                        $returnText = $smarty->get_config_vars('str_LabelVoucherTypePREPAID');
                        break;
                    }
                    case TPX_VOUCHER_TYPE_GIFTCARD:
                    {
                        $returnText = $smarty->get_config_vars('str_LabelVoucherTypeGIFTCARD');
                        break;
                    }
                    case TPX_VOUCHER_TYPE_SCRIPT:
                    {
                        $returnText = $smarty->get_config_vars('str_LabelVoucherTypeSCRIPT');
                        break;
                    }
                    default:
                    {
                        $returnText = 'error - unknown constant';
                        break;
                    }
                }
                break;
            }
            case 'AUTHENTICATIONMODE':
            {
                switch ($value)
                {
                    case TPX_AUTHENTICATIONMODE_LOGINPASS:
                    {
                        $returnText = $smarty->get_config_vars('str_MessageAuthMode_LoginPass');
                        break;
                    }
                    case TPX_AUTHENTICATIONMODE_LOGIN:
                    {
                        $returnText = $smarty->get_config_vars('str_MessageAuthMode_Login');
                        break;
                    }
                    case TPX_AUTHENTICATIONMODE_USERNAME:
                    {
                        $returnText = $smarty->get_config_vars('str_MessageAuthMode_Username');
                        break;
                    }
                    case TPX_AUTHENTICATIONMODE_PASSWORD:
                    {
                        $returnText = $smarty->get_config_vars('str_MessageAuthMode_Password');
                        break;
                    }
                    case TPX_AUTHENTICATIONMODE_AUTHKEY:
                    {
                        $returnText = $smarty->get_config_vars('str_MessageAuthMode_AuthKey');
                        break;
                    }
                    case TPX_AUTHENTICATIONMODE_CODE:
                    {
                        $returnText = $smarty->get_config_vars('str_MessageAuthMode_Code');
                        break;
                    }
                    case TPX_AUTHENTICATIONMODE_SERIALNUMBER:
                    {
                        $returnText = $smarty->get_config_vars('str_MessageAuthMode_SerialNumber');
                        break;
                    }
                    case TPX_AUTHENTICATIONMODE_ACTIVATIONCODE:
                    {
                        $returnText = $smarty->get_config_vars('str_MessageAuthMode_ActivationCode');
                        break;
                    }
                    default:
                    {
                        $returnText = 'error - unknown constant';
                        break;
                    }

                }
                break;
            }
            case 'VOUCHERAPPLYMETHOD':
            {
                switch ($value)
                {
                    case TPX_VOUCHER_APPLY_EACH_MATCHING_PRODUCT:
                    {
                        $returnText = $smarty->get_config_vars('str_LabelDiscountMethodMATCHINGPRODUCT');
                        break;
                    }
                    case TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER:
                    {
                        $returnText = $smarty->get_config_vars('str_LabelDiscountMethodSPREADORDER');
                        break;
                    }
                    case TPX_VOUCHER_APPLY_LOWEST_PRICED:
                    {
                        $returnText = $smarty->get_config_vars('str_LabelDiscountMethodLOWEST');
                        break;
                    }
                    case TPX_VOUCHER_APPLY_HIGHEST_PRICED:
                    {
                        $returnText = $smarty->get_config_vars('str_LabelDiscountMethodHIGHEST');
                        break;
                    }
                    default:
                    {
                        $returnText = 'error - unknown constant';
                        break;
                    }
                }
                break;
            }
            default:
            {
                $returnText ='error - unknown type';
                break;
            }
        }

        return $returnText;
    }

    static function getLanguageNameFromCode($smarty, $pLangCode)
    {

    	$languageName = '';
    	$languages = $smarty->get_config_vars('str_LanguageList');
        $languageList = explode(',', $languages);
        $codeCount = count($languageList);

        for ($i = 0; $i < $codeCount; $i++)
		{
			// split each language item into its code and name
			$langCodePos = explode(' ',$languageList[$i]);

			if ($pLangCode == $langCodePos[0])
			{
				$languageName = $langCodePos[1];
			}
    	}

    	if($languageName == '')
    	{
    		$languageName[0];
    	}

    	return $languageName;
    }

    static function initAdminDisplayLocalizedNamesList($smarty, $pLocalizedString, $pTextColour)
    {
        // display the localized name from the string

        // get the list of languages from the config file
        $languages = $smarty->get_config_vars('str_LanguageList');
        $languageList = explode(',', $languages);
        $codeCount = count($languageList);

        // process the localized string
        $localizedStringList = explode('<p>', $pLocalizedString);
		$localizedCount = count($localizedStringList);
		if ($localizedStringList[$localizedCount - 1] == '')
		{
		  $localizedCount--;
		}

		if ($localizedCount > 0)
		{
            $languageHTML = '<table class="adminTableEntryBorder text">';

            for ($i = 0; $i < $localizedCount; $i++)
            {
                // split each language item into its code and name
                $charPos = strpos($localizedStringList[$i], ' ');
                $localizedItemCode = substr($localizedStringList[$i], 0, $charPos);
                $localizedItemString = substr($localizedStringList[$i], $charPos + 1);
                $languageName = $localizedItemCode;

                for ($j = 0; $j < $codeCount; $j++)
                {
                    // split each language item into its code and name
                    $localizedItem = trim($languageList[$j]);
                    $charPos = strpos($localizedItem, ' ');
                    $languageCode = substr($localizedItem, 0, $charPos);
                    if ($languageCode == $localizedItemCode)
                    {
                        $languageName = substr($localizedItem, $charPos + 1);
                        break;
                    }
                }

                $languageHTML .= '<tr valign="top" style="color:' . $pTextColour . '">';
                $languageHTML .= '<td align="left" width="110" class="localizedLanguageName">' . $languageName . ':</td>';
                $languageHTML .= '<td align="left">'. UtilsObj::encodeString($localizedItemString, false) .'</td>';
                $languageHTML .= '</tr>';
            }

            $languageHTML .= '</table>';
        }
        else
        {
            $languageHTML = '';
        }

        return $languageHTML;
    }

    static function initAdminEditLocalizedNames($smarty, $pLocalizedTableName, $pLocalizedNamesSuffix, $pLocalizedString, $pInitializeGlobalStrings = true, $pSiteGroupLabel = false, $pEscape = false)
    {
        // build the area which allows localized names to be edited
		$configLocale = DatabaseObj::getSystemConfig();
        $supportedLocalesArray = explode(',', $configLocale['supportedlocales']);

        // get the list of languages from the config file
        $languageCodesJavaScript = 'var gAllLanguageCodesArray = new Array(';
        $languageNamesJavaScript = 'var gAllLanguageNamesArray = new Array(';
        $languages = $smarty->get_config_vars('str_LanguageList');
        $languageList = explode(',', $languages);

        if ($pInitializeGlobalStrings)
        {
            $codeCount = count($languageList);
            for ($i = 0; $i < $codeCount; $i++)
            {
                // split each language item into its code and name
                $localizedItem = trim($languageList[$i]);
                $charPos = strpos($localizedItem, ' ');
                $languageCode = substr($localizedItem, 0, $charPos);
                $languageName = substr($localizedItem, $charPos + 1);

                if ($languageCode == 'en')
				{
					$languageCodesArray[] =  "'" . $languageCode . "'";
                	$languageNamesArray[] =  "'" . $languageName . "'";
				}
				else
				{
					if (in_array($languageCode, $supportedLocalesArray) || in_array('ALL', $supportedLocalesArray))
					{
						$languageCodesArray[] =  "'" . $languageCode . "'";
                		$languageNamesArray[] =  "'" . $languageName . "'";
					}
				}
            }
            $languageCodesJavaScript .= implode(',', $languageCodesArray) . ');';
            $languageNamesJavaScript .= implode(',', $languageNamesArray) . ');';

            $smarty->assign('languagecodesjavascript', $languageCodesJavaScript);
            $smarty->assign('languagenamesjavascript', $languageNamesJavaScript);
		}

		//Check to see if the siteGroups label is present.
		//this is to assign different variables is more than one language component
		if ($pSiteGroupLabel)
		{
			// process the localized string
			$codesJavaScript = 'var gSiteGroupLocalizedCodesArray' . $pLocalizedNamesSuffix . ' = new Array(';
			$namesJavaScript = 'var gSiteGroupLocalizedNamesArray' . $pLocalizedNamesSuffix . ' = new Array(';
		}
		else
		{
			// process the localized string
			$codesJavaScript = 'var gLocalizedCodesArray' . $pLocalizedNamesSuffix . ' = new Array(';
			$namesJavaScript = 'var gLocalizedNamesArray' . $pLocalizedNamesSuffix . ' = new Array(';
		}

		$localizedStringList = explode('<p>', $pLocalizedString);
		$localizedCount = count($localizedStringList);
		if ($localizedStringList[$localizedCount - 1] == '')
		{
		  $localizedCount--;
		}
		for ($i = 0; $i < $localizedCount; $i++)
        {
            // split each language item into its code and name
            $charPos = strpos($localizedStringList[$i], ' ');
            $localizedItemCode = substr($localizedStringList[$i], 0, $charPos);
            $localizedItemString = substr($localizedStringList[$i], $charPos + 1);
            $localizedItemString = UtilsObj::encodeString($localizedItemString, $pEscape);
            $codesJavaScript .= '"' . $localizedItemCode . '"';
            if ($i < ($localizedCount -1))
            {
                $codesJavaScript .= ',';
            }

            $namesJavaScript .= '"' . $localizedItemString . '"';
            if ($i < ($localizedCount -1))
            {
                $namesJavaScript .= ',';
            }
        }
        $codesJavaScript .= ');';
        $namesJavaScript .= ');';

        if ($pSiteGroupLabel)
        {
        	$smarty->assign('sitegrouplocalizedcodesjavascript' . $pLocalizedNamesSuffix, $codesJavaScript);
        	$smarty->assign('sitegrouplocalizednamesjavascript' . $pLocalizedNamesSuffix, $namesJavaScript);
        }
        else
        {
        	$smarty->assign('localizedcodesjavascript' . $pLocalizedNamesSuffix, $codesJavaScript);
        	$smarty->assign('localizednamesjavascript' . $pLocalizedNamesSuffix, $namesJavaScript);
        }

        $smarty->assign('localizednamestablename' . $pLocalizedNamesSuffix, $pLocalizedTableName);
        $smarty->assign('localizednameslistname' . $pLocalizedNamesSuffix, $pLocalizedTableName . '_languagelist');
     }

     static function getLocaleString($pLocalizedString, $pLanguage, $pUseFirstAvailable = false)
     {
        // return the correct language string
        global $gConstants;

        $result = '';
        $firstAvailable = '';
        $defaultLanguage = '';

        if (is_null($pLocalizedString)) return '';

        if ($pLanguage == '')
        {
            $locale = UtilsObj::getBrowserLocale();
        }
        else
        {
            $locale = $pLanguage;
        }

        $locale2 = substr($locale, 0, 2);

        $localizedStringList = explode('<p>', $pLocalizedString);
		$localizedCount = count($localizedStringList);

		for ($i = 0; $i < $localizedCount; $i++)
        {
            // split each language item into its code and name
            $charPos = strpos($localizedStringList[$i], ' ');
            $localizedItemCode = substr($localizedStringList[$i], 0, $charPos);
            $localizedItemString = substr($localizedStringList[$i], $charPos + 1);

            if (($pUseFirstAvailable == true) && ($firstAvailable == '') && ($localizedItemString != ''))
            {
                $firstAvailable = $localizedItemString;
            }

            if ($localizedItemCode == $gConstants['defaultlanguagecode'])
            {
                $defaultLanguage = $localizedItemString;
            }

            if (($result == '') && ($localizedItemCode == 'en'))
            {
                $result = $localizedItemString;
            }
            else if ($localizedItemCode == $locale)
            {
                $result = $localizedItemString;
                break;
            }
            else if ($localizedItemCode == $locale2)
            {
                $result = $localizedItemString;
                break;
            }
		}

		if ($result == '')
		{
		    if ($defaultLanguage != '')
		    {
		        $result = $defaultLanguage;
		    }
		    else
		    {
		        $result = $firstAvailable;
		    }
		}

		return $result;
     }

	static function getLanguageList()
	{
		$languageList = Array();
		$item = array();

		$configLocale = DatabaseObj::getSystemConfig();
        $supportedLocalesArray = explode(',', $configLocale['supportedlocales']);

	    $smarty = SmartyObj::newSmarty('AdminSitesSitesAdmin');
		$languages = $smarty->get_config_vars('str_LanguageList');
		$itemList = explode(',', $languages);
		$itemCount = count($itemList);

		for ($i = 0; $i < $itemCount; $i++)
		{
		    // split each language item into its code and name
			$localizedItem = trim($itemList[$i]);
			$charPos = strpos($localizedItem, ' ');
			$languageCode = substr($localizedItem, 0, $charPos);
			$languageName = substr($localizedItem, $charPos + 1);

			$item['code'] = $languageCode;
			$item['name'] = UtilsObj::encodeString($languageName, true);

			if ($languageCode == 'en')
			{
				array_push($languageList, $item);
			}
			else
			{
				if (in_array($languageCode, $supportedLocalesArray) || in_array('ALL', $supportedLocalesArray))
				{
					array_push($languageList, $item);
				}
			}
	    }

		return $languageList;
	}

     static function buildSystemLanguageList($pCurrentLocale, $pIsSmallVersion = false)
     {
        // return the html / required to display the system language icon + list
		global $gSession;

        if ($pIsSmallVersion == true)
        {
            $languageHTMLList = '<ul>';
            $languageList = SmartyObj::getLanguageList();
            $itemCount = count($languageList);
            for ($i = 0; $i < $itemCount; $i++)
            {
                $code = $languageList[$i]['code'];

                if (($code == $pCurrentLocale) || ($code == substr($pCurrentLocale, 0, 2)))
                {
                    $optionSelected = ' list-item-selected';
                }
                else
                {
                    $optionSelected = ' list-item-unselected';
                }

                $languageHTMLList .= '
                   <li class="list-item' . $optionSelected . '" data-decorator="fnChangeSystemLanguageSmallScreen" data-code="'.$code.'"><div class="language-item">' . $languageList[$i]['name'] . '</div></li>';
            }
            $languageHTMLList .= '</ul>';
        }
        else
        {
            $languageHTMLList = '<div>
                <img src="' . $gSession['webbrandwebroot'] . '/images/icons/language_icon_v2.png" alt="" class="imgLanguage" />
                <select id="systemlanguagelist" name="systemlanguagelist" class="text">
                    ';
            $languageList = SmartyObj::getLanguageList();
            $itemCount = count($languageList);
            for ($i = 0; $i < $itemCount; $i++)
            {
                $code = $languageList[$i]['code'];

                if (($code == $pCurrentLocale) || ($code == substr($pCurrentLocale, 0, 2)))
                {
                    $optionSelected = 'selected="selected" ';
                }
                else
                {
                    $optionSelected = '';
                }

                $languageHTMLList .= '
                    <option ' . $optionSelected . 'value="' . $code . '">' . $languageList[$i]['name'] . '</option>';
            }
            $languageHTMLList .= '
                </select>
            </div>';
        }


        return $languageHTMLList;
     }

     static function getLocaleFormatValue($pParameter, $pLocale = '')
     {
        // return the parameter from the localized formats config file

        global $gConstants;

        $cacheKey = $pParameter . '_' . $pLocale;
        $theResult = '';

        if (array_key_exists($cacheKey, $gConstants['localeFormatCache']))
        {
        	// we have previously retrieved this value, so use it from the locale cache
        	$theResult = $gConstants['localeFormatCache'][$cacheKey];
        }
        else
        {
			// use smarty as a way to read and obtain the config values
			$smarty = SmartyObj::newSmarty('', '', '', $pLocale, false, false, 'format.conf');
			$theResult = $smarty->get_config_vars($pParameter);

			// store the result in our locale cache for later
			$gConstants['localeFormatCache'][$cacheKey] = $theResult;
		}

        return $theResult;
     }

	static function getLocaleDateTimeFormat($pLocale = '')
     {
        // return the date and time format

        return self::getLocaleFormatValue('str_DateTimeFormat', $pLocale);
     }

     static function formatLocaleDateTime($pDateTime, $pLocale = '')
     {
        // return the supplied date and time localized

        $dateFormat = self::getLocaleFormatValue('str_DateTimeFormat', $pLocale);

        return date($dateFormat, strtotime($pDateTime));
     }

     static function formatDateTime($pDateTime, $pDateTimeFormat)
     {
     	return date($pDateTimeFormat, strtotime($pDateTime));
     }

     static function formatLocaleDate($pDate, $pLocale = '')
     {
        // return the supplied date localized

        $dateFormat = self::getLocaleFormatValue('str_DateFormat', $pLocale);

        return date($dateFormat, strtotime($pDate));
     }

     static function formatLocaleTime($pTime, $pLocale = '')
     {
        // return the supplied time localized

        $dateFormat = self::getLocaleFormatValue('str_TimeFormat', $pLocale);

        return date($dateFormat, strtotime($pTime));
     }

     static function getLocaleDecimalPoint($pLocale = '')
     {
        // return the localized decimal point character

        $dpChar = self::getLocaleFormatValue('str_DecimalPoint', $pLocale);
        if ($dpChar == '')
        {
            $dpChar = '.';
        }

        return $dpChar;
     }

     static function getLocaleThousandsSeparator($pLocale = '')
     {
        // return the localized decimal point character

        $dpChar = self::getLocaleFormatValue('str_ThousandsSeparator', $pLocale);
        if ($dpChar == '')
        {
            $dpChar = ',';
        }

        return $dpChar;
     }

    static function getBrowserHourOffset()
    {
        $hourOffset = 0;

		// access value via REQUEST as the mobile version passes the tzoffset value via GET and the desktop version passes it via POST
        if (isset($_REQUEST['tzoffset']) && ($_REQUEST['tzoffset'] != 0))
        {
            $hourOffset = (float) round(($_REQUEST['tzoffset'] - time()) / (60 * 60), 1);
        }

        return $hourOffset;
    }
}

?>
