<?php

require_once('../Utils/UtilsSmarty.php');

class AdminTax_view
{
    static function initialize()
    {
        $smarty = SmartyObj::newSmarty('AdminTax');
        $smarty->displayLocale('admin/tax/taxframe.tpl');
    }
    static function initialize2()
    {
        global $gSession;
        
        $smarty = SmartyObj::newSmarty('AdminTax');
        
        $smarty->assign('TPX_LOGIN_SYSTEM_ADMIN',false);
        $smarty->assign('TPX_LOGIN_COMPANY_ADMIN',false);
        $smarty->assign('TPX_LOGIN_SITE_ADMIN',false);
        $smarty->assign('TPX_LOGIN_BRAND_OWNER',false);

        switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				$smarty->assign('TPX_LOGIN_SYSTEM_ADMIN',true);
			break;
			case TPX_LOGIN_COMPANY_ADMIN:
				$smarty->assign('TPX_LOGIN_COMPANY_ADMIN',true);
			break;
			case TPX_LOGIN_SITE_ADMIN:
				$smarty->assign('TPX_LOGIN_SITE_ADMIN',true);
			break;
			case TPX_LOGIN_BRAND_OWNER:
				$smarty->assign('TPX_LOGIN_BRAND_OWNER',true);
			break;
		}
        
        $smarty->displayLocale('admin/tax/tax.tpl');
    }

}

?>
