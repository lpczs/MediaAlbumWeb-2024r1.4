<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');

class AdminExportManual_view
{

	static function displayForm($pResultArray)
	{
		global $gSession;
		$countrylist 		 = $pResultArray['countrylist'];
		$defaultlanguagecode = $pResultArray['defaultlanguagecode'];
		$message 			 = $pResultArray['message'];
		$defaultlanguagename = '';
		$brandList			 = Array();
		$licenseKeyList 	 = array();
	    $smarty 			 = SmartyObj::newSmarty('AdminExport');
		$brands 			 = DatabaseObj::getBrandingList();
		$itemCount 			 = count($brands);

		if ($itemCount > 0)
		{
			for ($i = 0; $i < $itemCount; $i++)
			{
				if ($gSession['userdata']['usertype'] == TPX_LOGIN_BRAND_OWNER)
				{
					if ($gSession['userdata']['webbrandcode'] == $brands[$i]['code'])
					{
						$brandList[] = '["'.$brands[$i]['code'].'","'.UtilsObj::encodeString($brands[$i]['applicationname'], true).'"]';
					}
				}
				else
				{
					$brandList[] = '["'.$brands[$i]['code'].'","'.UtilsObj::encodeString($brands[$i]['applicationname'], true).'"]';
				}
			}
		}
		$brandList = '['.join(',',$brandList) .']';

		// if we are a brand owner, we need to get the brand code from the users record
		// rather than the session (as it may not be set depending on which url the logged in on)
		$webBrandCode = $gSession['userdata']['webbrandcode'];
		if ($gSession['userdata']['usertype'] === TPX_LOGIN_BRAND_OWNER)
		{
			$userAccountArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
			if ($userAccountArray['result'] == '')
			{
				$webBrandCode = $userAccountArray['webbrandcode'];
			} 
		}

		// establishing a database connection to obtain a list of license keys
		$licenses = DatabaseObj::getLicenseKeysList('', $webBrandCode);
		$licenseKeyCount = count($licenses);
		if ($licenseKeyCount == 0)
		{
			if ($gSession['userdata']['usertype'] != TPX_LOGIN_BRAND_OWNER)
			{
				//$licenseKeyList[] = '["N","'.$smarty->get_config_vars('str_LabelNone').'"]';
			}
		}
		else
		{
			for ($i = 0; $i < $licenseKeyCount; $i++)
			{
				$licenseKeyList[] = '["'.$licenses[$i]['id'].'","'.UtilsObj::encodeString($licenses[$i]['id'], true) .' - '.UtilsObj::encodeString($licenses[$i]['name'], true). '"]';
			}
		}
		$licenseKeyList = '['.join(',',$licenseKeyList) .']';

		// setup the language list
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

            if ($defaultlanguagecode == $languageCode)
            {
            	$defaultlanguagename = UtilsObj::encodeString($languageName, true);
            }
            
            if ($languageCode == 'en')
			{
				$languageList[] = '["'.$languageCode.'","'.UtilsObj::encodeString($languageName, true).'"]';
			}
			else
			{
				if (in_array($languageCode, $supportedLocalesArray) || in_array('ALL', $supportedLocalesArray))
				{
					$languageList[] = '["'.$languageCode.'","'.UtilsObj::encodeString($languageName, true).'"]';
				}									
			}       
        }
        
        $languageList = '['.join(',',$languageList) .']';
        $companyList = '[]';

		$smarty->assign('companycode', '');

        if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN || $gSession['userdata']['usertype'] == TPX_LOGIN_BRAND_OWNER)
		{
			$smarty->assign('companycode', $gSession['userdata']['companycode']);
		}

        $smarty->assign('brandlist', $brandList);
        $smarty->assign('companyList', $companyList);
        $smarty->assign('licensekeylist', $licenseKeyList);
        $smarty->assign('languagelist', $languageList);
	    $smarty->assign('defaultlanguagecode', $defaultlanguagecode);
        $smarty->assign('defaultlanguagename', $defaultlanguagename);
        $smarty->assign('userType', $gSession['userdata']['usertype']);
        $smarty->assign('dateformat', LocalizationObj::getLocaleFormatValue('str_DateFormat'));
        $smarty->assign('startdate', date(LocalizationObj::getLocaleFormatValue('str_DateFormat'), time()-7*86400)); // today minus 7 days
        $smarty->assign('enddate', date(LocalizationObj::getLocaleFormatValue('str_DateFormat'), time())); // today
        $smarty->assign('message', $message); // optional message to pop up
		$smarty->displayLocale('admin/exportmanual/exportmanual.tpl');
	}


	static function reportExport($pResultArray)
	{
        global $gSession;

        $smarty = SmartyObj::newSmarty('AdminExport');
        $exportformat = $_POST['exportformat'];

        // output only if not empty
        if (empty($pResultArray))
        {
        	// would be nice to only have a popup here
        	self::displayForm('noresult');
        }
        else
        {
			$fileName = 'Report_' . date('d_M_Y_His');
			// xml or tab separated
			switch ($exportformat) {
			case 'XML':
				// turn reportArray into xml text
				header('Content-Type: text/xml');
				header('Content-Disposition: Attachment; filename=' . $fileName . '.xml');
				break;
			case 'TXT':
				// turn reportArray into tab separated text
				header('Content-Type: text/tab-separated-values');
				header('Content-Disposition: Attachment; filename=' . $fileName . '.txt');
				break;
			}
			header('Pragma: no-cache');
			header('Expires: 0');
			echo $pResultArray;
		}
    }
}

?>
