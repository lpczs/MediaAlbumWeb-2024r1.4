<?php

class CollectFromStore_view
{
    static function initialize()
    {
    	global $gSession;
        $smarty = SmartyObj::newSmarty('CollectFromStore');
        $smarty->assign('dateformat', LocalizationObj::getLocaleFormatValue('str_DateFormat'));
        $smarty->assign('timeFormat', LocalizationObj::getLocaleFormatValue('str_TimeFormat'));
        $smarty->assign('todayDate', date(LocalizationObj::getLocaleFormatValue('str_DateFormat'), time()));
        $smarty->assign('todayTime', date(LocalizationObj::getLocaleFormatValue('str_TimeFormat'), time()));
        $smarty->assign('minTime', LocalizationObj::formatLocaleTime('07:00'));
        $smarty->assign('maxTime', LocalizationObj::formatLocaleTime('22:00'));
        $smarty->assign('siteType', ($gSession['userdata']['usertype'] == TPX_LOGIN_DISTRIBUTION_CENTRE_USER) ? '0' : '1');
        $smarty->assign('companyCode', $gSession['userdata']['companycode']);

        $smarty->displayLocale('collectfromstore/collectfromstore.tpl');
    }

    static function listOrders($resultArray)
	{
		for ($i = 0; $i < count($resultArray); $i++)
        {
        	$resultArray[$i] = '['.join(',', $resultArray[$i]).']';
        }

		if (count($resultArray) > 0)
		{
			echo '[['.count($resultArray).'],'.join(',', $resultArray).']';
		}
		else
		{
			echo '[]';
		}
	}
}

?>
