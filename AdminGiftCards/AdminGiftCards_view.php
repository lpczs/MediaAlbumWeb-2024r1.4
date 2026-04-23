<?php

require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');

class AdminGiftCards_view
{
    static function displayListData($pResultArray)
    {
        global $gSession, $gConstants;

        $giftcardItem = array();
        $itemsArray = array();

        LocalizationObj::formatLocaleDateTime('2000-01-01');

        $smarty = SmartyObj::newSmarty('AdminGiftCards');

        foreach($pResultArray['data'] as $item)
        {
            $giftcardItem['giftcardid'] = $item['giftcardid'];
            $giftcardItem['companycode'] = "'" . UtilsObj::ExtJSEscape($item['companycode']) . "'";
            $giftcardItem['code'] = "'" . UtilsObj::ExtJSEscape($item['code']) . "'";
            $giftcardItem['name'] = "'" . UtilsObj::ExtJSEscape(LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $item['name'], '')) . "'";

            // Calculate the difference in days.
            $startDate = $item['startdate'];
            $endDate = $item['enddate'];
            $redeemedDate = $item['redeemeddate'];

            if (strtotime($startDate) < strtotime('2000-01-01'))
            {
                $startDate = '2000-01-01';
            }
            if ($endDate == '1970-01-01 01:00:00')
            {
                $endDate = date('Y-m-d');
            }
            $giftcardItem['startdate'] = "'" . UtilsObj::ExtJSEscape(LocalizationObj::formatLocaleDateTime($startDate)) . "'";
            $giftcardItem['enddate'] = "'" . UtilsObj::ExtJSEscape(LocalizationObj::formatLocaleDateTime($endDate)) . "'";

            if ($item['groupcode'] != '')
            {
                $giftcardItem['groupcode'] = "'" . UtilsObj::ExtJSEscape($item['groupcode']) . "'";
            }
            else
            {
                $giftcardItem['groupcode'] = "'" . UtilsObj::ExtJSEscape('<i>' . $smarty->get_config_vars('str_LabelAll') . '</i>') . "'";
            }

            $giftcardItem['userid'] = "'" . UtilsObj::ExtJSEscape($item['userid']) . "'";

            if ($item['userid'] != 0)
            {
                $giftcardItem['username'] = "'" . UtilsObj::ExtJSEscape($item['contactfirstname'] . ' ' . $item['contactlastname'] . '<br>(' . $item['contactemailaddress'] . ')') . "'";
            }
            else
            {
                $giftcardItem['username'] = "'" . UtilsObj::ExtJSEscape('<i>' . $smarty->get_config_vars('str_LabelAll') . '</i>') . "'";
            }

            $giftcardItem['redeemuserid'] = "'" . UtilsObj::ExtJSEscape($item['redeemuserid']) . "'";

            if ($item['redeemuserid'] != 0)
            {
                $giftcardItem['redeemusername'] = "'" . UtilsObj::ExtJSEscape($item['redeemfirstname'] . ' ' . $item['redeemlastname'] . '<br>(' . $item['redeememailaddress'] . ')') . "'";
            }
            else
            {
                $giftcardItem['redeemusername'] = "''";
            }

            $giftcardItem['giftcardvalue'] = "'" . UtilsObj::ExtJSEscape($item['giftcardvalue']) . "'";

            if (($redeemedDate == '1970-01-01 01:00:00') || ($redeemedDate == '0000-00-00 00:00:00'))
            {
                $redeemedDate = '';
                $giftcardItem['redeemeddate'] = "''";
            }
            else
            {
                $giftcardItem['redeemeddate'] = "'" . UtilsObj::ExtJSEscape(LocalizationObj::formatLocaleDateTime($redeemedDate)) . "'";
            }

            $giftcardItem['isactive'] = "'" . UtilsObj::ExtJSEscape($item['isactive']) . "'";

            array_push($itemsArray, '[' . join(',', $giftcardItem) . ']');

        }

        $summaryArray = join(',', $itemsArray);
        if ($summaryArray != '')
        {
            $summaryArray = ', ' . $summaryArray;
        }

        echo '[[' . $pResultArray['recordcount'] . ']' . $summaryArray . ']';

    }

    static function giftcardImport($pResultArray)
    {
        if ($pResultArray['result'] == '')
        {
            echo "{'success':'true', 'msg':'" . '' . "' }";
        }
        else
        {
            $smarty = SmartyObj::newSmarty('AdminGiftCards');

            if (count($pResultArray['alreadeyadded']) > 0)
            {
                $resultParam = $smarty->get_config_vars('str_ErrorGiftcardsExist') . ' ' . join(', ', $pResultArray['alreadeyadded']);
            }
            else
            {
                $resultParam = str_replace('^0', $pResultArray['resultparam'], $smarty->get_config_vars($pResultArray['result']));
            }
            echo '{"success": false, "msg":"' . $resultParam  . '"}';
        }
    }

    static function giftcardCreate($pResultArray)
    {
        if ($pResultArray['result'] == '')
        {
            echo "{'success':'true', 'msg':'" . '' . "', data: '" . join(',', $pResultArray['newrecords']) . "' }";
        }
        else
        {
            $smarty = SmartyObj::newSmarty('AdminGiftCards');
            $resultParam = $smarty->get_config_vars($pResultArray['result'] ) . ' ' . $pResultArray['resultparam'];
            echo '{"success":false, "msg":"' . $resultParam . '"}';
        }

        return;
    }

    static function giftcardEdit($pResultArray)
    {
        if ($pResultArray['result'] == '')
        {
            echo "{'success':'true', 'msg':'" . '' . "' }";
        }
        else
        {
            $smarty = SmartyObj::newSmarty('AdminGiftCards');
            $resultParam = $smarty->get_config_vars($pResultArray['result'] ) . ' ' . $pResultArray['resultparam'];
            echo '{"success":false, "msg":"' . $resultParam . '"}';
        }
    }

    static function giftcardAdd($pResultArray)
    {

        if ($pResultArray['result'] == '')
        {
            echo "{'success':'true', 'msg':'" . '' . "' }";
        }
        else
        {
            $smarty = SmartyObj::newSmarty('AdminGiftCards');
            $message = str_replace('^0', $pResultArray['resultparam'], $smarty->get_config_vars($pResultArray['result']));

            echo '{"success":false, "msg":"' . $message . '"}';

        }
    }

    static function giftcardDelete($pResultArray)
    {

        $smarty = SmartyObj::newSmarty('AdminGiftCards');

        if ($pResultArray['result']=='')
        {
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$msg = $smarty->get_config_vars('str_GiftcardsDeleted');
            echo "{'success':'true', 'title':'" . UtilsObj::ExtJSEscape($title) . "', 'msg':'" .UtilsObj::ExtJSEscape($msg) . "' }";
        }
        else
        {
            echo '{"success":false, "msg":"' . $pResultArray['resultparam'] . '"}';
        }
    }

    static function giftcardActivate($pResultArray)
    {

        $smarty = SmartyObj::newSmarty('AdminGiftCards');

        if ($pResultArray['result']=='')
        {
            echo "{'success':'true', 'title':'" . UtilsObj::ExtJSEscape($smarty->get_config_vars('str_TitleConfirmation')) . "', 'msg':'' }";
        }
        else
        {
            echo '{"success":false, "msg":"' . $pResultArray['resultparam'] . '"}';
        }
    }

	static function displayList()
	{
	    global $gSession;
	    global $gConstants;

	    $tableData = '';
	    $rowCount = 1;

	    $smarty = SmartyObj::newSmarty('AdminGiftCards');

        $smarty->assign('title', $smarty->get_config_vars('str_VoucherTitleGiftCards'));

		$smarty->displayLocale('admin/giftcards/giftcardlistwindow.tpl');
	}

    static function displayEntry($pTitle, $pResultArray, $pActionButtonName, $pError = '', $pTemplate = '', $pDestAction = '')
    {
        global $gSession, $gConstants;

		$smartyDefaultObj = SmartyObj::newSmarty('');
        $smarty = SmartyObj::newSmarty('AdminGiftCards');

        $smarty->assign('optionms', $gConstants['optionms']);
        $smarty->assign('systemadmin', ($gSession['userdata']['usertype'] == TPX_LOGIN_SYSTEM_ADMIN));
        $smarty->assign('companyadmin', ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN));

        if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
            $smarty->assign('companycode' ,$gSession['userdata']['companycode']);
        else
        {
            if (($pResultArray['displayMode'] == TPX_ADD_EDIT_FORM_TYPE) && (strlen($pResultArray['companycode'])>0))
                $smarty->assign('companycode' ,$pResultArray['companycode']);
            else
                $smarty->assign('companycode' ,'GLOBAL');
        }

        $smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('giftcardid', $pResultArray['giftcardid']);
        $smarty->assign('code', $pResultArray['code']);
        $smarty->assign('defaultlanguagecode', $gConstants['defaultlanguagecode']);

        // What template fields to display
        $smarty->assign('displayMode', $pResultArray['displayMode']);

        // Calculate the difference in days.
        $date1 = strtotime($pResultArray['startdate']);
        $date2 = strtotime('2000-01-01');

            // Which is the latest?
        if ($date1 < $date2)
        {
            $pResultArray['startdate'] = '2000-01-01';
        }

        if ($pResultArray['enddate'] == '1970-01-01 01:00:00' || $pResultArray['enddate'] == '')
        {
            $pResultArray['enddate'] = date('Y-m-d');
        }

        $smarty->assign('startdate', LocalizationObj::formatLocaleDateTime($pResultArray['startdate']));
        $smarty->assign('enddate', LocalizationObj::formatLocaleDateTime($pResultArray['enddate']));
		$smarty->assign('all', $smartyDefaultObj->get_config_vars('str_LabelAll'));

        if ($pResultArray['groupcode']=='')
		{
            $smarty->assign('groupcode', 'ALL');
		}
        else
		{
            $smarty->assign('groupcode', $pResultArray['groupcode']);
		}

        $smarty->assign('userid', $pResultArray['userid']);
        $smarty->assign('earliestdate', LocalizationObj::formatLocaleDateTime('1999-01-01 00:00:00'));
        $smarty->assign('latestdate', LocalizationObj::formatLocaleDateTime('2038-01-01 00:00:00'));
        $smarty->assign('giftcardvalue', UtilsObj::formatNumber($pResultArray['giftcardvalue'], 2));

        $dataList = array();
        $langList = array();

        // Set up the language arrays

        $langs = array();

        $languageList = explode(',', $smarty->get_config_vars('str_LanguageList'));

        foreach($languageList as $lang)
        {
            $lang = trim($lang);
            $charPos = strpos($lang, ' ');
            $langs[substr($lang, 0, $charPos)] = substr($lang, $charPos + 1);
        }

        if (strlen($pResultArray['name'])>0)
        {
            $localizedStringList = explode('<p>', $pResultArray['name']);

            foreach ($localizedStringList as $name)
            {
                $charPos = strpos($name, ' ');
                $code = substr($name, 0, $charPos);
                $name = UtilsObj::encodeString(substr($name, $charPos + 1),false);
                $langname = $langs[$code];

                $dataList[] = '["'.$code.'","'.$langname.'","'.$name.'"]';

                unset($langs[$code]);
            }
        }

        foreach($langs as $langCode=>$langName)
            $langList[] = '["'.$langCode.'","'.$langName.'"]';

        $smarty->assign('dataList', '['.join(',', $dataList).']');
        $smarty->assign('langList', '['.join(',', $langList).']');
        $smarty->assign('groupList', '['.join(',', $pResultArray['groupList']).']');

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
            $message = $smarty->get_config_vars($pError);
            if (strpos($message, '^0') !== false)
            {
                $message = str_replace('^0', $pResultArray['resultparam'], $message);
            }
            $smarty->assign('error', $message);
        }
        else
        {
            $smarty->assign('error', $pError);
        }

        $smarty->assign('actionbutton', $smarty->get_config_vars($pActionButtonName));
        $smarty->assign('destaction', $pDestAction);

        if ($pTemplate == '')
        {
            $smarty->displayLocale('admin/giftcards/giftcardedit.tpl');
        }
        else
        {
            $smarty->displayLocale($pTemplate);
        }
    }

    private static function defaultValues(&$pResultArray)
    {
		$smarty = SmartyObj::newSmarty('');

        $pResultArray['giftcardid'] = 0;
        $pResultArray['companycode'] = '';
        $pResultArray['name'] = '';
        $pResultArray['userid'] = 0;
        $pResultArray['startdate'] = '2000-01-01';
        $pResultArray['enddate'] = date('Y-m-d');
        $pResultArray['groupcode'] = 'ALL';
        $pResultArray['giftcardvalue'] = 0.00;
        $pResultArray['isactive'] = 1;
    }

	static function displayAdd($pResultArray)
	{
	    $pResultArray['displayMode'] = TPX_ADD_EDIT_FORM_TYPE; // add or edit window

        self::defaultValues($pResultArray);

        self::displayEntry('str_TitleNewGiftCard', $pResultArray, 'str_ButtonAdd', '', '', 'AdminGiftCards');
    }

    static function displayCreate($pResultArray)
	{
	    $pResultArray['displayMode'] = TPX_CREATE_FORM_TYPE; // create window

	    self::defaultValues($pResultArray);

        $pResultArray['code'] = '';

        self::displayEntry('str_TitleCreateVouchers', $pResultArray, 'str_ButtonCreate', '', '', 'AdminGiftCards');
    }

    static function displayImport($pResultArray)
	{

        $pResultArray['displayMode'] = TPX_IMPORT_FORM_TYPE; // import window

        self::defaultValues($pResultArray);

        $pResultArray['code'] = '';

        self::displayEntry('str_TitleCreateVouchers', $pResultArray, 'str_ButtonCreate', '', '', 'AdminGiftCards');
    }

    static function giftcardExport($pResultArray)
    {
        global $gSession;

        $smarty = SmartyObj::newSmarty('AdminGiftCards');

		header('Content-Type: text/plain; charset=utf-8');
		header('Content-Disposition: Attachment; filename=Giftcards_'. date('d_M_Y_His') . '.txt');
		header('Pragma: no-cache');
        header('Expires: 0');

        $separator = "\t";

        // write the file header

        echo $smarty->get_config_vars('str_LabelCode') . $separator;
        echo $smarty->get_config_vars('str_LabelName') . $separator;
        echo $smarty->get_config_vars('str_LabelStartDate') . $separator;
        echo $smarty->get_config_vars('str_LabelEndDate') . $separator;
        echo $smarty->get_config_vars('str_LabelLicenseKey') . $separator;
        echo $smarty->get_config_vars('str_LabelUserID') . $separator;
        echo $smarty->get_config_vars('str_LabelFirstName') . $separator;
        echo $smarty->get_config_vars('str_LabelLastName') . $separator;
        echo $smarty->get_config_vars('str_LabelEmailAddress') . $separator;
        echo $smarty->get_config_vars('str_LabelRedeemUserID') . $separator;
        echo $smarty->get_config_vars('str_LabelRedeemFirstName') . $separator;
        echo $smarty->get_config_vars('str_LabelRedeemLastName') . $separator;
        echo $smarty->get_config_vars('str_LabelRedeemEmailAddress') . $separator;
        echo $smarty->get_config_vars('str_LabelRedeemedDate') . $separator;
        echo $smarty->get_config_vars('str_LabelGiftcardValue'). $separator;
        echo "\n";

        for ($i = 0; $i < count($pResultArray); $i++)
        {
            $groupCode = $pResultArray[$i]['groupcode'];
            $userID = $pResultArray[$i]['userid'];
            $redeemUserId = $pResultArray[$i]['redeemUserId'];

            $repeatType = 'str_LabelRepeatType' . $pResultArray[$i]['repeattype'];
	        $discountSection = 'str_LabelDiscountSection' . $pResultArray[$i]['discountsection'];
	        $discountType = 'str_LabelDiscountType' . $pResultArray[$i]['discounttype'];

            if ($groupCode == '')
            {
                $groupCode = $smarty->get_config_vars('str_LabelAll');
            }

            if ($userID == 0)
            {
                $userID = '';
            }

            if ($redeemUserId==0)
            {
                $redeemUserId = '';
            }

            echo $pResultArray[$i]['code'] . $separator;
            echo LocalizationObj::getLocaleString($pResultArray[$i]['name'], $gSession['browserlanguagecode'], true) . $separator;
            echo $pResultArray[$i]['startdate'] . $separator;
            echo $pResultArray[$i]['enddate'] . $separator;
            echo $groupCode . $separator;
            echo $userID . $separator;
            echo $pResultArray[$i]['usercontactfirstname'] . $separator;
            echo $pResultArray[$i]['usercontactlastname'] . $separator;
            echo $pResultArray[$i]['useremailaddress'] . $separator;
            echo $redeemUserId . $separator;
            echo $pResultArray[$i]['redeemcontactfirstname'] . $separator;
            echo $pResultArray[$i]['redeemcontactlastname'] . $separator;
            echo $pResultArray[$i]['redeememailaddress'] . $separator;
            echo $pResultArray[$i]['redeemeddate'] . $separator;
            echo $pResultArray[$i]['giftcardvalue'];
            echo "\n";
        }
    }

}

?>
