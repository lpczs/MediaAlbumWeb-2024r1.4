<?php

class AdminVouchersPromotion_view
{
	static function displayList()
	{
	    $smarty = SmartyObj::newSmarty('AdminVouchers');
		$smarty->displayLocale('admin/vouchersPromotion/promotion.tpl');
	}

    static function displayEntry($pTitle, $pResultArray, $pActionButtonName, $pError = '')
    {
    	global $ac_config,$gConstants,$gSession;

        $smarty = SmartyObj::newSmarty('AdminVouchers');
        $smarty->assign('title', UtilsObj::encodeString($smarty->get_config_vars($pTitle), true));
        $smarty->assign('promotionid', $pResultArray['recordid']);
        $smarty->assign('code', $pResultArray['code']);
        $smarty->assign('name', UtilsObj::encodeString($pResultArray['name'], true));

        $selectIndex = 0;
        $date1 = strtotime($pResultArray['startdate']);
	    $date2 = strtotime('2000-01-01');

		 // Which is the latest?
	    if ($date1 < $date2) {
	      $pResultArray['startdate'] = '2000-01-01';
	    }

        if ($pResultArray['enddate'] == '')
        {
        	$pResultArray['enddate'] = date('Y-m-d');
        }

        $smarty->assign('startdate', LocalizationObj::formatLocaleDateTime($pResultArray['startdate']));
        $smarty->assign('enddate', LocalizationObj::formatLocaleDateTime($pResultArray['enddate']));
        $smarty->assign('earliestdate', LocalizationObj::formatLocaleDateTime('1999-01-01 00:00:00'));
        $smarty->assign('latestdate', LocalizationObj::formatLocaleDateTime('2038-01-01 00:00:00'));

		$assignedToProd = 0;
		$companylist = '';
		if (($gConstants['optionms']) && ($gSession['userdata']['usertype'] == TPX_LOGIN_SYSTEM_ADMIN))
		{
         	$itemList = DatabaseObj::getCompanyList();
			$companylist = '<select id="companylist" name="companylist" class="text" onChange="return checkAssignedToProduction(); return false;">';
        	$companylist .= '<option value="">'.$smarty->get_config_vars('str_Global').'</option>';

      	  	$companyCodeSelected = isset($pResultArray['companycode']) ? $pResultArray['companycode'] : '';
            //remove result and resultparam for just the company
            unset($itemList['result']);
            unset($itemList['resultparam']);
            $nbItems = count($itemList);
			for ($i = 0; $i < $nbItems; $i++)
        	{
        		$companyCode = $itemList[$i]['code'];
        		$companyName = $itemList[$i]['companyname'];

        		if ($companyCodeSelected == $companyCode)
        		{
            		$optionSelected = 'selected';
            		$selectIndex = $i + 1;
        		}
        		else
        		{
        			$optionSelected = '';
        		}

        		$displayName = $companyCode . ' - ' . UtilsObj::encodeString($companyName, true);
        		$companylist .= '<option ' . $optionSelected. ' value="' . $companyCode . '">' . $displayName;
        	}
        	$companylist .= '</select>';


        	$smarty->assign('companyCodeSelected', $companyCodeSelected);

        	if ($pResultArray['code'] != '')
        	{
        		$dbObj = DatabaseObj::getGlobalDBConnection();
        		if ($dbObj)
	    		{
        			if ($stmt = $dbObj->prepare('SELECT count(*) as c FROM VOUCHERS WHERE `promotioncode` = ? AND (`owner` IS NOT NULL AND `owner` <> "")'))
        			{
        				if ($stmt->bind_param('s', $pResultArray['code']))
        				{
        					if ($stmt->bind_result($assignedToProd))
	           				{
	           					if ($stmt->execute())
	           					{
                   					$stmt->fetch();
               					}
               				}
        				}
        				$stmt->free_result();
            			$stmt->close();
            			$stmt = null;
            		}
        			$dbObj->close();
	    		}
        	}
       }
		$smarty->assign('selectIndex', $selectIndex);
		$smarty->assign('companylist', $companylist);
		$smarty->assign('vouchersAssignedToProd', $assignedToProd);

        if ($pResultArray['isactive'] == 1)
        {
            $smarty->assign('activechecked', 'checked');
        }
        else
        {
             $smarty->assign('activechecked', '');
        }

        $smarty->assign('dateformat', LocalizationObj::getLocaleFormatValue('str_DateTimeFormat'));

        if (substr($pError, 0, 4) == 'str_')
        {
            $smarty->assign('error', $smarty->get_config_vars($pError));
        }
        else
        {
            $smarty->assign('error', $pError);
        }

        $smarty->assign('actionbutton', $smarty->get_config_vars($pActionButtonName));
        $smarty->displayLocale('admin/vouchersPromotion/promotionedit.tpl');
    }

	static function displayAdd($pResultArray)
	{
        self::displayEntry('str_TitleNewPromotion', $pResultArray, 'str_ButtonAdd');
    }

    static function displayEdit($pResultArray)
	{
		self::displayEntry('str_TitleEditPromotion', $pResultArray, 'str_ButtonUpdate');
    }

}

?>