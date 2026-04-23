<?php

require_once('../Utils/UtilsSmarty.php');

class AdminSites_view
{
    static function initialize()
    {
        $smarty = SmartyObj::newSmarty('AdminSites');
        $smarty->displayLocale('admin/sites/sitesframe.tpl');
    }
    static function initialize2()
    {
    	global $gConstants;
    	
        $smarty = SmartyObj::newSmarty('AdminSites');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->displayLocale('admin/sites/sites.tpl');
    }

}

?>
