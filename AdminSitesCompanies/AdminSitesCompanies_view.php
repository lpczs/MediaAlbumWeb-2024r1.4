<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');

class AdminSitesCompanies_view
{
	static function displayGrid()
	{
		global $gConstants;
		
        $smarty = SmartyObj::newSmarty('AdminSitesCompanies');
        $smarty->displayLocale('admin/sitescompanies/sitescompaniesgrid.tpl');
	}
	
	static function getGridData($pResultArray)
	{
		global $gConstants;
		$smarty = SmartyObj::newSmarty('AdminSitesCompanies');
		
		echo '[';
		
		$itemCount = count($pResultArray);
		
		echo '[' . $itemCount . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];
			echo "['" . $item['recordid'] . "',";
			echo "'" . UtilsObj::encodeString($item['code'], true) . "',";
			echo "'" . UtilsObj::encodeString($item['name'], true) . "',";
			echo "'" . UtilsObj::encodeString($item['address'], true) . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
	}

	static function companyEdit($pResultArray)
    {       	
       	$smarty = SmartyObj::newSmarty('AdminSitesCompanies');
       
    	if ($pResultArray['result'] != '')
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');
			
			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			$name = UtilsObj::encodeString($pResultArray['name'],false);
			echo '{"success":true, "data":{"id":' . $pResultArray['id'] . ',"code":"' . $pResultArray['code'] . '","name":"' . $name . '"}}'; 
        }  	
    }
    
    static function displayEdit($pResult)
	{
        $smarty = SmartyObj::newSmarty('AdminSitesCompanies');
        $smarty->assign('id', $pResult['id']);
        $smarty->assign('companycode', UtilsObj::encodeString($pResult['code'], true));
        $smarty->assign('companyname', UtilsObj::encodeString($pResult['name'], true));
        
        $smarty->assign('contactfirstname', UtilsObj::encodeString($pResult['contactfirstname'], true));
        $smarty->assign('contactlastname', UtilsObj::encodeString($pResult['contactlastname'], true));
        $smarty->assign('companyaddress1', UtilsObj::encodeString($pResult['customeraddress1'], true));
        $smarty->assign('companyaddress2', UtilsObj::encodeString($pResult['customeraddress2'], true));
        $smarty->assign('companyaddress3', UtilsObj::encodeString($pResult['customeraddress3'], true));
        $smarty->assign('companyaddress4', UtilsObj::encodeString($pResult['customeraddress4'], true));
        
		$additionalAddressFields = UtilsAddressObj::getAdditionalAddressFields($pResult['customercountrycode'], $pResult['customeraddress4']);
        $smarty->assign('companyadd41', UtilsObj::encodeString($additionalAddressFields['add41'], true));
        $smarty->assign('companyadd42', UtilsObj::encodeString($additionalAddressFields['add42'], true));
        $smarty->assign('companyadd43', UtilsObj::encodeString($additionalAddressFields['add43'], true));
        
        $smarty->assign('companycity', UtilsObj::encodeString($pResult['customercity'], true));
        $smarty->assign('companycounty', UtilsObj::encodeString($pResult['customercounty'], true));
        $smarty->assign('companystate', UtilsObj::encodeString($pResult['customerstate'], true));
        $smarty->assign('companyregioncode', UtilsObj::encodeString($pResult['customerregioncode'], true));
        $smarty->assign('companyregion', UtilsObj::encodeString($pResult['customerregion'], true));
        $smarty->assign('companypostcode', UtilsObj::encodeString($pResult['customerpostcode'], true));
        $smarty->assign('companycountrycode', UtilsObj::encodeString($pResult['customercountrycode'], true));
        $smarty->assign('companycountryname', UtilsObj::encodeString($pResult['customercountryname'], true));
        $smarty->assign('telephonenumber', UtilsObj::encodeString($pResult['telephonenumber'], true));
        $smarty->assign('emailaddress', UtilsObj::encodeString($pResult['emailaddress'], true));
        $smarty->assign('taxaddress', $pResult['taxaddress']);
        
        $smarty->assign('defaultipaccesslist', $pResult['defaultipaccesslist']);
        $smarty->assign('usedefaultipaccesslist', $pResult['usedefaultipaccesslist']);
        $smarty->assign('ipaccesslist', $pResult['ipaccesslist']);
        
        $smarty->displayLocale('admin/sitescompanies/sitescompaniesedit.tpl');
    }
}

?>
