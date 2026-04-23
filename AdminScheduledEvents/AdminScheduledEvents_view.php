<?php

class AdminScheduledEvents_view
{
    static function initialize()
    {
        $smarty = SmartyObj::newSmarty('AdminScheduledEvents');
        $smarty->displayLocale('admin/scheduledevents/scheduledevents.tpl');
    }

    static function detailsDisplay($pResultArray)
    {
    	$smarty = SmartyObj::newSmarty('AdminScheduledEvents');
        $smarty->assign('id', $pResultArray['id']);
        $smarty->assign('title', $smarty->get_config_vars('str_TitleEditEvent'));
        $smarty->assign('datecreated', $pResultArray['datecreated']);
        $smarty->assign('companycode', $pResultArray['companycode']);
        $smarty->assign('groupcode', $pResultArray['groupcode']);
        $smarty->assign('webbrandcode', $pResultArray['webbrandcode']);
        $smarty->assign('taskcode', $pResultArray['taskcode']);
        $smarty->assign('runcount', $pResultArray['runcount']);
        $smarty->assign('maxruncount', $pResultArray['maxruncount']);

        $pResultArray['lastruntime'] = ($pResultArray['lastruntime'] == '0000-00-00 00:00:00') ? '' : UtilsObj::ExtJSEscape(LocalizationObj::formatLocaleDateTime($pResultArray['lastruntime']));
		$pResultArray['nextruntime'] = ($pResultArray['nextruntime'] == '0000-00-00 00:00:00') ? '' : UtilsObj::ExtJSEscape(LocalizationObj::formatLocaleDateTime($pResultArray['nextruntime']));
		$smarty->assign('lastruntime', $pResultArray['lastruntime']);
        $smarty->assign('nextruntime', $pResultArray['nextruntime']);

        $status = '';
        if ($pResultArray['statuscode'] == 0)
        {
        	$status = UtilsObj::ExtJSEscape($smarty->get_config_vars("str_LabelNeverRun"));
        }
        if ($pResultArray['statuscode'] == 1)
        {
        	$status = $pResultArray['statusmessage'];
        }
        $smarty->assign('status', $status);
        $smarty->assign('priority', $pResultArray['priority']);
        $smarty->assign('active', $pResultArray['active']);

        $priorityList = array();
        $priorityList[] = array('id'=>'0', 'name'=>$smarty->get_config_vars('str_LabelNormal'));
        $priorityList[] = array('id'=>'100', 'name'=>$smarty->get_config_vars('str_LabelHigh'));
        $smarty->assign('priorityList', $priorityList);

        $smarty->displayLocale('admin/scheduledevents/scheduledeventsedit.tpl');
    }

}

?>
