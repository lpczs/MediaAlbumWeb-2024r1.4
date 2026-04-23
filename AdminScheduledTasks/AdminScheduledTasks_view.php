<?php

class AdminScheduledTasks_view
{
    static function initialize($pResultArray) 
    {
        global $gConstants;
        
        $smarty = SmartyObj::newSmarty('AdminScheduledTasks');
        
        $pResultArray['schedulerLastRunTime'] = ($pResultArray['schedulerLastRunTime'] == '0000-00-00 00:00:00') ? UtilsObj::ExtJSEscape($smarty->get_config_vars("str_LabelNever")) : UtilsObj::ExtJSEscape(LocalizationObj::formatLocaleDateTime($pResultArray['schedulerLastRunTime']));
		
		$smarty->assign('optionwscrp', ($gConstants['optionwscrp'] ? true : false));	
						
        $smarty->assign('schedulerActive', $pResultArray['schedulerActive']);
        $smarty->assign('schedulerLastRunTime', $pResultArray['schedulerLastRunTime']);
        
        $smarty->displayLocale('admin/scheduledtasks/scheduledtasks.tpl');
    }
    
    static function displayEntry($pID, $pTitle, $pResultArray, $pActionButtonName, $pError = '')
    {
        global $gConstants;
                
    	$sciptsList = array();
		$currentpath = getcwd();				
		$taskPath = str_replace('webroot', '/Customise/scripts/tasks/', $currentpath);

		if (is_dir($taskPath)) 
		{
			if ($dh = opendir($taskPath))
			{
				while (($fileName = readdir($dh)) !== false) 
				{
					if (($fileName != ".") && ($fileName!=".."))
					{
						if (preg_match("/([\.php])$/", $fileName))
						{														
							$sciptsList[] = $fileName;
						}
					}
				}
				closedir($dh);
			}
		}			
		
    	$smarty = SmartyObj::newSmarty('AdminScheduledTasks');
        $smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('id', $pResultArray['id']);
        $smarty->assign('dateCreated', $pResultArray['dateCreated']);
        $smarty->assign('taskCode', $pResultArray['taskCode']);
        $smarty->assign('taskName', $pResultArray['taskName']);
        $smarty->assign('intervalType', $pResultArray['intervalType']);
        $smarty->assign('intervalValue', $pResultArray['intervalValue']);
        $smarty->assign('lastRunTime', $pResultArray['lastRunTime']);
        $smarty->assign('nextRunTime', $pResultArray['nextRunTime']);
        $smarty->assign('statusCode', $pResultArray['statusCode']);
        $smarty->assign('statusMessage', $pResultArray['statusMessage']);
        $smarty->assign('runStatus', $pResultArray['runStatus']);
        $smarty->assign('maxRunCount', $pResultArray['maxRunCount']);
        $smarty->assign('internal', $pResultArray['internal']);
        $smarty->assign('scriptFileName', $pResultArray['scriptFileName']);
        $smarty->assign('deleteExpiredInterval', $pResultArray['deleteExpiredInterval']);
        $smarty->assign('active', $pResultArray['active']);
        $smarty->assign('sciptsList', $sciptsList);
        $smarty->assign('defaultlanguagecode', $gConstants['defaultlanguagecode']); 
         
        $intervalTypes = array();
        $intervalTypes[] = array('id'=>'1', 'name'=>$smarty->get_config_vars('str_LabelNumberOfMinutes'));		
        $intervalTypes[] = array('id'=>'2', 'name'=>$smarty->get_config_vars('str_LabelTimeOfTheDay'));		
        $intervalTypes[] = array('id'=>'3', 'name'=>$smarty->get_config_vars('str_LabelNumberOfDays'));		
        $smarty->assign('intervalTypes', $intervalTypes);
        
        $intervalValueTime = LocalizationObj::formatLocaleTime(date('Y-m-d H:i:s', time()), '');
        if (($pResultArray['intervalType'] == 2))
        {
        	$intervalValueTime = LocalizationObj::formatLocaleTime(date('Y-m-d', time()) . ' ' .$pResultArray['intervalValue'] , '');   
        }
        
        $smarty->assign('intervalValueTime', $intervalValueTime);
        
        LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', $pResultArray['taskName']);
        
        if ($pID == 0)
      	{
      		$smarty->assign('isEdit', 0);	
      	}
      	else
      	{
      		$smarty->assign('isEdit', 1);		
      	}

        $smarty->displayLocale('admin/scheduledtasks/scheduledtasksedit.tpl');
    }
    
    static function displayEdit($pResultArray)
	{
	   self::displayEntry($pResultArray['id'], 'str_TitleEditTask', $pResultArray, 'str_ButtonUpdate', '');
    }
    
     static function displayAdd($pResultArray)
	{ 
        self::displayEntry($pResultArray['id'], 'str_TitleAddTask', $pResultArray, 'str_ButtonAdd', '');
    }
    
}

?>
