<?php

class AdminAbout_view
{
   
    static function initialize() 
    {
        $smarty = SmartyObj::newSmarty('Admin');
        $smarty->displayLocale('admin/about/about.tpl');
    }
}

?>
