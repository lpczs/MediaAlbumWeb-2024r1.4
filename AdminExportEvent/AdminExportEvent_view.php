<?php

class AdminExportEvent_view
{

	static function eventEdit($pSite)
    {
		if ($pSite['result'] == '')
		{
			echo '{"success":true, "data":[{"id":' . $pSite['item']['eventId'] . ',"eventCode":"' . $pSite['item']['eventCode'] . '" ,"eventLang":"' . $pSite['item']['eventLang'] . '","exportFormat":"' . $pSite['item']['exportFormat']. '","filePath":"' . UtilsObj::encodeString($pSite['item']['filePath'],true) . '","filenameFormat":"' . UtilsObj::encodeString($pSite['item']['filenameFormat'], true) . '","active":"' . $pSite['item']['active']	. '","paymentdata":"' . $pSite['item']['paymentdata']. '","beautified":"' . $pSite['item']['beautified'] . '","originalLang":"' . $pSite['item']['originalLang']	. '"}]}';
		}
		else
		{
			echo '{"success":false,	"msg":"' . $pSite['result'] . '"}';
		}
	}

	static function eventActivate($pSite)
    {
		if ($pSite['result'] == '')
		{
			echo '{"success":true, "data":[{"id":' . $pSite['recordid'] . ',"active":"' . $pSite['isactive'] . '"},]}';
		}
		else
		{
			echo '{"success":false,	"msg":"' . $pSite['result'] . '"}';
		}
	}

	static function displayList($pResultArray, $pError = '')
	{
        global $gConstants;

	    $summaryArray = array();
        $bufArr = array();
	    $smarty = SmartyObj::newSmarty('AdminExportEvent');
	    $itemCount = count($pResultArray);

	    $tasksList = Array();
	    $tasksList = DatabaseObj::getTasksList();
	    $tasksList = $tasksList['items'];
	    $tasksListArray = array();
	    $tasksListArray[] = array('id'=>'', 'name'=>$smarty->get_config_vars('str_LabelNone'));
	    for ($i = 0; $i < count($tasksList); $i++)
        {
        	$tasksListArray[] = array('id'=>$tasksList[$i]['taskCode'], 'name'=>$tasksList[$i]['taskCode']);
        }

	    for ($i = 0; $i < $itemCount; $i++)
        {
            $id = $pResultArray[$i]['id'];
            $eventcode = $pResultArray[$i]['eventcode'];
            $language = $pResultArray[$i]['language'];
            $exportformat = $pResultArray[$i]['exportformat'];
            $subfolderformat = $pResultArray[$i]['subfolderformat'];
            $filenameformat = $pResultArray[$i]['filenameformat'];
            $isActive = $pResultArray[$i]['active'];
            $paymentdata = $pResultArray[$i]['paymentdata'];
            $beautified = $pResultArray[$i]['beautified'];
            $webhook1url = $pResultArray[$i]['webhook1url'];
            $webhook2url = $pResultArray[$i]['webhook2url'];
           

            $originalLang = $language;
            if ($language == '00')
            {    
                $language = "Order";
            }    
            elseif ($language == 'Default')
            {    
                $language == 'str_LabelDefault';
            }    
            else
            {    
                $language = LocalizationObj::getLanguageNameFromCode($smarty, $pResultArray[$i]['language']);
            }    

        	$bufArr['id'] = '"' . $id . '"';
            $bufArr['eventCode'] = '"' . $eventcode . '"';
            $bufArr['eventLang'] = '"' . $language . '"';
            $bufArr['exportFormat'] = '"' . $exportformat . '"';
            $bufArr['pathFormat'] = '"' . UtilsObj::encodeString($subfolderformat, true) . '"';
            $bufArr['filenameFormat'] = '"' . UtilsObj::encodeString($filenameformat, true) . '"';
            $bufArr['active'] = '"' . $isActive . '"';
            $bufArr['paymentdata'] = '"' . $paymentdata . '"';
            $bufArr['beautified'] = '"' . $beautified . '"';
            $bufArr['originalLang'] = '"' . $originalLang . '"';
            $bufArr['webhook1url'] = '"' . UtilsObj::encodeString($webhook1url, true) . '"';
            $bufArr['webhook2url'] = '"' . UtilsObj::encodeString($webhook2url, true) . '"';

            array_push($summaryArray, '[' . join(',', $bufArr) . ']');
	    }

        // setup the language list
        $configLocale = DatabaseObj::getSystemConfig();
        $supportedLocalesArray = explode(',', $configLocale['supportedlocales']);
        
        $languages = $smarty->get_config_vars('str_LanguageList');
        $itemList  = explode(',', $languages);
        $itemCount = count($itemList);
        $defaultlanguagecode = $gConstants['defaultlanguagecode'];

        for ($i = 0; $i < $itemCount; $i++)
        {
            // split each language item into its code and name
            $localizedItem = trim($itemList[$i]);
            $charPos 	   = strpos($localizedItem, ' ');
            $languageCode  = substr($localizedItem, 0, $charPos);
            $languageName  = substr($localizedItem, $charPos + 1);
            
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
        $smarty->assign('rows', '['.join(',', $summaryArray).']');
        $smarty->assign('languagesList', $languageList);
        $smarty->assign('defaultlanguagename', $defaultlanguagename);
        $smarty->assign('tasksList', $tasksListArray);
		$smarty->displayLocale('admin/exportevent/events.tpl');
	}
}

?>
