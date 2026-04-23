<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsRoute.php');

class AdminUsers_view
{
	static function displayGrid()
	{
		global $gConstants;
		global $gSession;

        $smarty = SmartyObj::newSmarty('AdminUsers');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->assign('TPX_LOGIN_SYSTEM_ADMIN', TPX_LOGIN_SYSTEM_ADMIN);
        $smarty->assign('TPX_LOGIN_COMPANY_ADMIN', TPX_LOGIN_COMPANY_ADMIN);
        $smarty->assign('TPX_LOGIN_SITE_ADMIN', TPX_LOGIN_SITE_ADMIN);
        $smarty->assign('TPX_LOGIN_CREATOR_ADMIN', TPX_LOGIN_CREATOR_ADMIN);
        $smarty->assign('TPX_LOGIN_PRODUCTION_USER', TPX_LOGIN_PRODUCTION_USER);
        $smarty->assign('TPX_LOGIN_DISTRIBUTION_CENTRE_USER', TPX_LOGIN_DISTRIBUTION_CENTRE_USER);
        $smarty->assign('TPX_LOGIN_STORE_USER', TPX_LOGIN_STORE_USER);
        $smarty->assign('TPX_LOGIN_BRAND_OWNER', TPX_LOGIN_BRAND_OWNER);
        $smarty->assign('TPX_LOGIN_API', TPX_LOGIN_API);
        $smarty->assign('TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER', TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER);
        $smarty->assign('usertype', $gSession['userdata']['usertype']);
        $smarty->displayLocale('admin/users/usersgrid.tpl');
	}


	static function getGridData($pResultArray)
	{
		global $gConstants;
		echo '[';

		$users = $pResultArray['users'];
		$itemCount = count($users);

		echo '[' . $pResultArray['total'] . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $users[$i];

			echo "['" . $item['id'] . "',";
			if ($gConstants['optionms'])
			{
				echo "'" . $item['companycode'] . "',";
			}
			echo "'" .  UtilsObj::encodeString($item['contactfirstname']) . "',";
			echo "'" . UtilsObj::encodeString($item['contactlastname']) . "',";
			echo "'" . UtilsObj::encodeString($item['login']) . "',";
			echo "'" . UtilsObj::encodeString($item['emailaddress']) . "',";
			echo "'" . UtilsObj::encodeString($item['usertype']) . "',";
			echo "'" . UtilsObj::encodeString($item['isactive']) . "',";
			echo "'" . UtilsObj::encodeString($item['webbrandcode']) . "',";
			echo "'" . UtilsObj::encodeString($item['usertypename']) . "',";

			if ($gConstants['optionms'])
			{
				echo "'" . UtilsObj::encodeString($item['owner']) . "',";
				echo "'" . UtilsObj::encodeString($item['companycode']) . "',";
			}
			else
			{
				echo "'" . UtilsObj::encodeString($item['owner']) . "',";
			}

			echo "'" . UtilsObj::encodeString($item['accountlocked']) . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
	}


    static function displayEntry($pTitle, $pID, $pContactFirstName, $ContactLastName, $pLogin, $pEmailAddress, $pUserType, $pModifyPassword, $pIsActive,
		$pOwner, $pActionButtonName, $pProductionSite, $pBrandCode, $pCompanyCode, $pIpAccessType, $pIpAccessList, $pDefaultIpAccessList, $pNextValidLoginDate)
    {
       	global $gConstants;
       	global $gSession;

        $smarty = SmartyObj::newSmarty('AdminUsers');
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));

        $smarty->assign('TPX_LOGIN_SYSTEM_ADMIN', TPX_LOGIN_SYSTEM_ADMIN);
        $smarty->assign('TPX_LOGIN_COMPANY_ADMIN', TPX_LOGIN_COMPANY_ADMIN);
        $smarty->assign('TPX_LOGIN_SITE_ADMIN', TPX_LOGIN_SITE_ADMIN);
        $smarty->assign('TPX_LOGIN_CREATOR_ADMIN', TPX_LOGIN_CREATOR_ADMIN);
        $smarty->assign('TPX_LOGIN_PRODUCTION_USER', TPX_LOGIN_PRODUCTION_USER);
        $smarty->assign('TPX_LOGIN_DISTRIBUTION_CENTRE_USER', TPX_LOGIN_DISTRIBUTION_CENTRE_USER);
        $smarty->assign('TPX_LOGIN_STORE_USER', TPX_LOGIN_STORE_USER);
        $smarty->assign('TPX_LOGIN_BRAND_OWNER', TPX_LOGIN_BRAND_OWNER);
        $smarty->assign('TPX_LOGIN_API', TPX_LOGIN_API);
        $smarty->assign('TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER', TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER);

        $smarty->assign('usertype', $pUserType);
        $smarty->assign('owner', $pProductionSite);
        $smarty->assign('companycode', $pCompanyCode);
        $smarty->assign('brandcode', $pBrandCode);
        $smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('userid', $pID);
        $smarty->assign('contactfname', UtilsObj::ExtJSEscape($pContactFirstName));
        $smarty->assign('contactlname', UtilsObj::ExtJSEscape($ContactLastName));
        $smarty->assign('login', UtilsObj::ExtJSEscape($pLogin));
        $smarty->assign('password', '**UNCHANGED**');
        $smarty->assign('email', $pEmailAddress);
        $smarty->assign('userprodsite', $pProductionSite);
       	$smarty->assign('ipaccesstype', $pIpAccessType);
       	$smarty->assign('ipaccesslist', $pIpAccessList);
       	$smarty->assign('defaultipaccesslist', $pDefaultIpAccessList);


        $loginTypes = UtilsObj::getUserLoginConstants();
       	$smarty->assign('userlogintypes', $loginTypes);

       	switch ($gSession['userdata']['usertype'])
		{
			case TPX_LOGIN_SYSTEM_ADMIN:
				$defaultLoginValue = 0;
				$smarty->assign('loggedInAs', TPX_LOGIN_SYSTEM_ADMIN);
			break;
			case TPX_LOGIN_COMPANY_ADMIN:
				$defaultLoginValue = TPX_LOGIN_SITE_ADMIN;
				$smarty->assign('loggedInAs', TPX_LOGIN_COMPANY_ADMIN);
			break;
			case TPX_LOGIN_SITE_ADMIN:
				$defaultLoginValue = TPX_LOGIN_PRODUCTION_USER;
				$smarty->assign('loggedInAs', TPX_LOGIN_SITE_ADMIN);
			break;
		}

       	$smarty->assign('defaultLoginTypeValue', $defaultLoginValue);

        if ($pID == 0)
        {
        	$smarty->assign('isEdit', 0);
        }
        else
        {
        	$smarty->assign('isEdit', 1);
        	$smarty->assign('adminID', $pID);
        }

        if ($pModifyPassword == 1)
        {
            $smarty->assign('canmodifypasswordchecked', 1);
        }
        else
        {
             $smarty->assign('canmodifypasswordchecked', 0);
        }

        if ($pIsActive == 1)
        {
            $smarty->assign('activechecked', 1);
        }
        else
        {
             $smarty->assign('activechecked', 0);
        }

		// Check if the account is locked.
		if (strtotime($pNextValidLoginDate) > strtotime(DatabaseObj::getServerTimeUTC()))
		{
			$smarty->assign('accountnotlocked', 0);
		}
		else
		{
			$smarty->assign('accountnotlocked', 1);
		}

        $smarty->assign('defaultDisplay',0);
        $smarty->assign('actionbutton', $smarty->get_config_vars($pActionButtonName));
        $smarty->displayLocale('admin/users/useredit.tpl');
    }


	static function displayAdd()
	{
        global $gConstants;
		self::displayEntry('str_TitleNewUser', 0, '', '', '', '', 0, 0, 1, '', 'str_ButtonAdd', '', '', '', '', '0', '',
			$gConstants['defaultipaccesslist'], '0000-00-00 00:00:00');
    }


    static function displayEdit($pID)
	{
		global $gConstants;
		$resultArray = DatabaseObj::getUserAccountFromID($pID);
		self::displayEntry('str_TitleEditUser', $resultArray['recordid'], $resultArray['contactfirstname'], $resultArray['contactlastname'],
			$resultArray['login'], $resultArray['emailaddress'],  $resultArray['usertype'], $resultArray['canmodifypassword'],
			$resultArray['isactive'], $resultArray['owner'], 'str_ButtonUpdate', $resultArray['owner'], $resultArray['webbrandcode'],
			$resultArray['companycode'],$resultArray['ipaccesstype'], $resultArray['ipaccesslist'], $gConstants['defaultipaccesslist'],
			$resultArray['nextvalidlogindate']);
	}


    static function userDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['userids']);
        $smarty = SmartyObj::newSmarty('AdminUsers');

        if ($pResultArray['alldeleted'] == 0)
        {
			$msg = $smarty->get_config_vars('str_WarningUsedInOrder');
			$title = $smarty->get_config_vars('str_TitleWarning');
			$alldeleted = '0';
        }
        else
        {
			$msg = $smarty->get_config_vars('str_MessageUserDeleted');
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$alldeleted = '1';
        }

		echo '{"success":true, "title":"' . $title . '", "msg":"' . $msg . '", "alldeleted":"' . $alldeleted . '", "idlist":"' . $deleteList . '"}';
	}


	static function userSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('AdminUsers');

    	if ($pResultArray['result'] != '')
        {
            $msg = str_replace('^0', $pResultArray['resultparam'], $smarty->get_config_vars($pResultArray['result']));
			$title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			$fname = UtilsObj::encodeString($pResultArray['contactfname'],false);
			$lname = UtilsObj::encodeString($pResultArray['contactlname'],false);
			$login = UtilsObj::encodeString($pResultArray['login'],false);

			echo '{"success":true,"data":{"id":' . $pResultArray['id'] . ',"companycode":"' . $pResultArray['companycode'] . '","fname":"' . $fname . '","lname":"' . $lname . '","login":"' . $login . '","email":"' . $pResultArray['email'] . '",
        		"site":"' . $pResultArray['owner'] . '","usertype":"' . $pResultArray['usertype'] . '","active":"' . $pResultArray['isactive'] . '"}}';
        }
    }


    static function userActivate($pUsers)
    {
        global $gSession;

        $itemCount = count($pUsers);
        $resultData = '{"success":true, "data":[';

        for ($i=0; $i < $itemCount; $i++)
        {
			$user = $pUsers[$i];
			$resultData .= '{"id":' . $user['recordid'] . ',"active":"' . $user['isactive'] . '"}';

        	if ($i != $itemCount - 1)
			{
				$resultData .= ",";
			}
        }

        $resultData .= ']}';

        echo $resultData;

	}

}

?>
