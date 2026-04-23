<?php

require_once('../Utils/UtilsSmarty.php');

class Admin_view
{
    /**
   	* Initialises administrator session
   	*
   	* The administrator session is initialised and the 'Sites' is enabled only for systemn administrators.
   	*
   	* @static
	*
   	*
   	* @author Kevin Gale
	* @since Version 1.0.0
 	*/
    static function initialize()
    {
    	global $gConstants;
    	global $gSession;
		global $ac_config;

		$smarty = SmartyObj::newSmarty('Admin');
        $smarty->assign('buildversionstring', 'Version: '.$gConstants['webversionstring'].' - '. date('l, jS F Y', strtotime($gConstants['webversiondate'])));
        $smarty->assign('adminsitesenabled', ((($gConstants['optionms'] || $gConstants['optioncfs']) && ($gSession['userdata']['usertype'] == TPX_LOGIN_SYSTEM_ADMIN || $gSession['userdata']['usertype'] == TPX_LOGIN_SITE_ADMIN )) ? true : false));

		if ((array_key_exists('DISABLEADMINREAUTHENTICATION', $ac_config)) && ((int) $ac_config['DISABLEADMINREAUTHENTICATION'] == 1))
		{
			$smarty->assign('adminauthentificationenabled', 0);
		}
		else
		{
			$smarty->assign('adminauthentificationenabled', 1);
		}

		$smarty->assign('userid', $gSession['userid']);
		
        $smarty->assign('TPX_LOGIN_SYSTEM_ADMIN',false);
        $smarty->assign('TPX_LOGIN_COMPANY_ADMIN',false);
        $smarty->assign('TPX_LOGIN_SITE_ADMIN',false);
        $smarty->assign('TPX_LOGIN_CREATOR_ADMIN',false);
        $smarty->assign('TPX_LOGIN_BRAND_OWNER',false);
        $smarty->assign('TPX_LOGIN_PRODUCTION_USER',false);
        $smarty->assign('TPX_LOGIN_DISTRIBUTION_CENTRE_USER',false);
        $smarty->assign('TPX_LOGIN_STORE_USER',false);
        $smarty->assign('TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER',false);
        

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
            case TPX_LOGIN_CREATOR_ADMIN:
                $smarty->assign('TPX_LOGIN_CREATOR_ADMIN',true);
            break;
            case TPX_LOGIN_BRAND_OWNER:
                $smarty->assign('TPX_LOGIN_BRAND_OWNER',true);
            break;
            case TPX_LOGIN_PRODUCTION_USER:
                $smarty->assign('TPX_LOGIN_PRODUCTION_USER',true);
            break;
            case TPX_LOGIN_DISTRIBUTION_CENTRE_USER:
                $smarty->assign('TPX_LOGIN_DISTRIBUTION_CENTRE_USER',true);
            break;
            case TPX_LOGIN_STORE_USER:
                $smarty->assign('TPX_LOGIN_STORE_USER',true);
            break;
            case TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER:
                $smarty->assign('TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER',true);
            break;
        }

        $smarty->displayLocale('admin/admin.tpl');
    }

    static function saveAsPriceList()
    {
    	global $gConstants;
    	global $gSession;

    	$smarty = SmartyObj::newSmarty('ComponentsPricing');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));

        if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
		{
			$smarty->assign('companyLogin', true);
			$smarty->assign('company', $gSession['userdata']['companycode']);

		}
		else
		{
			$smarty->assign('companyLogin', false);
			$smarty->assign('company','GLOBAL');
		}

        $smarty->displayLocale('admin/adminsaveaspricelist.tpl');
    }

    static function priceListAdd($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('ComponentsPricing');

    	if ($pResultArray['result'] != '')
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			echo '{"success":true,"data":{"id":' . $pResultArray['id'] . ',"price":"' . $pResultArray['price'] . '","pricelistcode":"' . $pResultArray['pricelistcode'] . '", "pricelistname":"' . $pResultArray['pricelistname'] . '"}}';
        }
    }

    static function displayPriceListEntry($pID, $pCompanyCode, $pCode, $pName, $pPricingModel, $pPrice, $pQuantityIsDropDown, $pCategoryCode, $pTaxCode, $pIsActive, $pDecimalPlaces)
    {
       	global $gSession;
		global $gConstants;

       	$smarty = SmartyObj::newSmarty('ComponentsPricing');
       	$smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));

       	$smarty->assign('ID', $pID);
        $smarty->assign('company', $pCompanyCode);
        $smarty->assign('code', $pCode);
        $smarty->assign('name', UtilsObj::encodeString($pName,true));
        $smarty->assign('pricingModel', $pPricingModel);
        $smarty->assign('price', $pPrice);
        $smarty->assign('quantityisdropdown', $pQuantityIsDropDown);
        $smarty->assign('taxcode', $pTaxCode);
        $smarty->assign('isActive', $pIsActive);
        $smarty->assign('categorycodelist', $pCategoryCode);
        $smarty->assign('decimalplaces', $pDecimalPlaces);
        $smarty->assign('companyLogin', false);

       	if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
        {
        	$smarty->assign('companyLogin', true);
        }

        $smarty->displayLocale('admin/adminpricelistedit.tpl');
    }

    static function displayPriceListEdit($pResultArray)
	{
        self::displayPriceListEntry($pResultArray['id'], $pResultArray['companycode'], $pResultArray['pricelistlocalcode'], $pResultArray['pricelistname'], $pResultArray['pricingmodel'], $pResultArray['price'], $pResultArray['quantityisdropdown'], $pResultArray['categorycode'], $pResultArray['taxcode'], $pResultArray['active'], $pResultArray['decimalplaces']);
    }

    static function priceListSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('ComponentsPricing');

    	if ($pResultArray['result'] != '')
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			echo '{"success":true,"data":{"id":' . $pResultArray['id'] . ',"companycode":"' . $pResultArray['company'] . '","code":"' . $pResultArray['pricelistcode'] . '", "active":"' . $pResultArray['isactive'] . '"}}';
        }
    }

    static function adminPriceListDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['pricelistids']);

        $smarty = SmartyObj::newSmarty('ComponentsPricing');

        if ($pResultArray['alldeleted'] == 0)
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');
			$alldeleted = '0';
        }
        else
        {
			$msg = $smarty->get_config_vars('str_MessagePriceListsDeleted');
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$alldeleted = '1';
        }

		echo '{"success":true, "title":"' . $title . '", "msg":"' . $msg . '", "alldeleted":"' . $alldeleted . '", "idlist":"' . $deleteList . '"}';
	}

	static function activatePriceList($pPriceLists)
    {
        global $gSession;

        $itemCount = count($pPriceLists);

        $resultData = '{"success":true, "data":[';

        for ($i = 0; $i < $itemCount; $i++)
        {
			$type = $pPriceLists[$i];
			$resultData .= '{"id":' . $type['recordid'] . ',"status":"' . $type['isactive'] . '"}';

        	if ($i != $itemCount - 1)
        	{
        		$resultData .= ",";
        	}
        }

        $resultData .= ']}';
        echo $resultData;
	}

	static function ExtJsSearchCustomers($pResultArray)
	{
		$userlist = array();

		if ($pResultArray['result']=='')
		{
			$smarty = SmartyObj::newSmarty('');
			$userlist[] = "{'id': '0', 'firstname': '" . $smarty->get_config_vars('str_LabelAll') . "', 'lastname': '', 'emailaddress': ''}";

			foreach($pResultArray['data'] as $dataItem)
			{
				$useritem = array();

				foreach($dataItem as $key=>$value)
				{
					$useritem[] = "'" .$key."': '".UtilsObj::ExtJSEscape($value)."'";
				}

				$userlist[] .= "{" . join(',', $useritem) . "}";
			}

			echo "{'success': 'true', 'totalcount': '". $pResultArray['totalcount'] ."', 'users': [" . join(',', $userlist) . "]}";
		}
		else
		{
			$smarty = SmartyObj::newSmarty('');
			SmartyObj::replaceParams($smarty, $pResultArray['result'], $pResultArray['resultparam'], true);
			echo "{'success': 'false', 'message':  '" . $smarty->get_template_vars($pResultArray['result']) . "'}";
		}
	}

	/**
	 * Outputs the result of the action.
	 *
	 * @param array $pResultArray
	 * @returns JSON string with success status, title and message.
	 */
	static function outputJSON($pResultArray)
	{
		$success = 'true';
		$message = '';
		$title = '';

		if ($pResultArray['error'] != '')
		{
			$smarty = SmartyObj::newSmarty('');
			SmartyObj::replaceParams($smarty, $pResultArray['error'], $pResultArray['errorparam'], true);

			$success = 'false';
			$title = $smarty->get_config_vars('str_TitleError');
			$message = $smarty->get_template_vars($pResultArray['error']);
		}

		echo "{'success': '" . $success . "', 'title': '" . $title . "', 'msg':  '" . $message . "'}";
	}
}

?>
