<?php

require_once('../Utils/UtilsSmarty.php');

class AdminExport_view
{
    static function initialize()
    {
        $smarty = SmartyObj::newSmarty('AdminExport');
        $smarty->displayLocale('admin/export/exportframe.tpl');
    }
    static function initialize2()
    {
        global $gSession;
        
        $smarty = SmartyObj::newSmarty('AdminExport');
        
        if ($gSession['userdata']['usertype'] == TPX_LOGIN_SYSTEM_ADMIN)
		{
			$smarty->assign('showexportevents',true);
		}
		else
		{
			$smarty->assign('showexportevents',false);
		}
        
        $smarty->displayLocale('admin/export/export.tpl');
    }

}

?>
