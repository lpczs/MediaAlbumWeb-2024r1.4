<?php

class AdminHome_view
{

    static function initialize()
    {
        global $gConstants;

        $smarty = SmartyObj::newSmarty('Admin');
        $smarty->assign('webversionstring', $gConstants['webversionstring']);
        $smarty->displayLocale('admin/home/home.tpl');
    }
}

?>
