<?php

class AdminConstants_view
{

	static function displayEdit($pResultArray)
    {
    	$languageList = Array();
    	$countryList = Array();
    	$currencyList = Array();
        $constantsArray = $pResultArray['constants'];

		// setup the country list
		$itemList = UtilsAddressObj::getCountryList();
        foreach ($itemList as $item)
        {
            $countryList[] = '["' . $item['isocode2'] . '","' . $item['name'] . '"]';
        }
        $countryListText = '[' . implode(',', $countryList) . ']';

        // setup the currency list
        $itemList = $pResultArray['currencylist'];
        foreach ($itemList as $item)
        {
            $currencyCode 	= $item['code'];
            $currencyName 	= LocalizationObj::getLocaleString($item['name'], '', true);
            $currencyList[] = '["' . $currencyCode . '","' . $currencyCode . ' - ' . UtilsObj::encodeString($currencyName, true) . '"]';
        }
        $currencyListText = '[' . implode(',',$currencyList) . ']';

		// setup the language list
        $smarty = SmartyObj::newSmarty('AdminConstants');
		
		$configLocale = DatabaseObj::getSystemConfig();
        $supportedLocalesArray = explode(',', $configLocale['supportedlocales']);
        
        $languages = $smarty->get_config_vars('str_LanguageList');
        $itemList  = explode(',', $languages);
        $itemCount = count($itemList);

        for ($i = 0; $i < $itemCount; $i++)
        {
            // split each language item into its code and name
            $localizedItem 	= trim($itemList[$i]);
            $charPos 		= strpos($localizedItem, ' ');
            $languageCode 	= substr($localizedItem, 0, $charPos);
            $languageName 	= substr($localizedItem, $charPos + 1);
            
            if ($languageCode == 'en')
			{
				$languageList[] = '["' . $languageCode . '","' . UtilsObj::encodeString($languageName, true) . '"]';
			}
			else
			{
				if (in_array($languageCode, $supportedLocalesArray) || in_array('ALL', $supportedLocalesArray))
				{
					$languageList[] = '["' . $languageCode . '","' . UtilsObj::encodeString($languageName, true) . '"]';
				}									
			}            
        }
        $languageListText = '[' . implode(',',$languageList) . ']';

        $smarty->assign('countrylist', $countryListText);
        $smarty->assign('homecountrycode', $constantsArray['homecountrycode']);

        $smarty->assign('languagelist', $languageListText);
        $smarty->assign('defaultlanguagecode', $constantsArray['defaultlanguagecode']);

        $smarty->assign('currencylist', $currencyListText);
        $smarty->assign('defaultcurrencycode', $constantsArray['defaultcurrencycode']);

        $smarty->assign('creditlimit', UtilsObj::formatNumber($constantsArray['defaultcreditlimit'], 2));
        $smarty->assign('taxaddress', $constantsArray['taxaddress']);

        $smarty->assign('defaultipaccesslist', $constantsArray['defaultipaccesslist']);

		$smarty->assign('maxloginattempts', $constantsArray['maxloginattempts']);
		$smarty->assign('accountlockouttime', $constantsArray['accountlockouttime']);

		$smarty->assign('maxiploginattempts', $constantsArray['maxiploginattempts']);
        $smarty->assign('maxiploginattemptsminutes', $constantsArray['maxiploginattemptsminutes']);
        
		$smarty->assign('minpasswordscore', $constantsArray['minpasswordscore']);

		$smarty->assign('customerupdateauthrequired', $constantsArray['customerupdateauthrequired']);

        $smarty->displayLocale('admin/constants/constants.tpl');
    }

    static function constantsEdit($pResultArray)
    {
    	if ($pResultArray['result'] != '')
        {
			$smarty = SmartyObj::newSmarty('AdminConstants');
    		echo '{"success":false, msg: "'.$smarty->get_config_vars('str_MessageError').'"}';
    	}
        else
        {
			echo '{"success":true}';
        }
    }

}

?>
