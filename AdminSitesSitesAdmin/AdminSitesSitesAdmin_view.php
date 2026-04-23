<?php

require_once('../Utils/UtilsAddress.php');

class AdminSitesSitesAdmin_view
{
	static function displayGrid()
	{
		global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminSitesSitesAdmin');

        $smarty->displayLocale('admin/sitessitesadmin/sitesgrid.tpl');
	}
	
	static function getGridData($pResultArray)
	{
		global $gConstants;
		
		echo '[';
		
		$sites = $pResultArray['sites'];
		$itemCount = count($sites);
				
		echo '[' . $pResultArray['total']  . ']';
		
		if ($itemCount > 0)
		{
			echo ',';
			for ($i = 0; $i < $itemCount; $i++)
			{
				$item = $sites[$i];
				echo "['" . $item['recordid'] . "',";
				echo "'" . UtilsObj::encodeString($item['companycode'], true) . "',";
				echo "'" . UtilsObj::encodeString($item['code'], true) . "',";
				echo "'" . UtilsObj::encodeString($item['name'], true) . "',";
				echo "'" . UtilsObj::encodeString($item['address'], true) . "',";
				echo "'" . UtilsObj::encodeString($item['productionsitekey'], true) . "',";
				echo "'" . UtilsObj::encodeString($item['sitetype'], true) . "',";
				echo "'" . UtilsObj::encodeString($item['sitegroup'], true) . "',";
				echo "'" . UtilsObj::encodeString($item['siteonline'], true) . "',";
				echo "'" . UtilsObj::encodeString($item['isactive'], true) . "']";
			
				if ($i != $itemCount - 1)
				{
					echo ",";
				}
			}
		}
		
		echo ']';
	}
	
    static function siteActivate($pSites)
    {
        global $gSession;
        
        $itemCount = count($pSites);
        
        $resultData = '{"success":true, "data":[';
        
        for ($i = 0; $i < $itemCount; $i++)
        {
			$site = $pSites[$i];
			$resultData .= '{"id":' . $site['recordid'] . ',"active":"' . $site['isactive'] . '"}';
        
        	if ($i != $itemCount - 1)
        	{
        		$resultData .= ",";
        	}
        }
		
        $resultData .= ']}';
        
        echo $resultData;

	}
	
    static function siteEdit($pSite)
    {
		if ($pSite['result'] == '')
		{
			echo '{"success":true, "data":[{"id":' . $pSite['item']['recordid'] . 
										',"companycode":"' . UtilsObj::encodeString($pSite['item']['companycode'], true) . 
										'", "name":"' . UtilsObj::encodeString($pSite['item']['name'], true) . 
										'" ,"address":"' . UtilsObj::encodeString($pSite['item']['address'], true) . 
										'" ,"sitetype":"' . UtilsObj::encodeString($pSite['item']['sitetype'], true) . 
										'" ,"sitegroup":"' . UtilsObj::encodeString($pSite['item']['sitegroup'], true) . 
										'" ,"siteonline":"' . UtilsObj::encodeString($pSite['item']['siteonline'], true) . 
										'","active":"' . UtilsObj::encodeString($pSite['item']['isactive'], true) . '"}]}';
		}
		else
		{
	        $error = $pSite['result'];
	        if (substr($error, 0, 4) == 'str_')
	        {
	        	$smarty = SmartyObj::newSmarty('AdminSitesSitesAdmin');
	            SmartyObj::replaceParams($smarty, $error, $pSite['resultparam']);
	            $error = $smarty->get_template_vars($error);
	            
	            $title = $smarty->get_config_vars('str_TitleError');
	        }
			
			echo '{"success":false, "resultparam":"'.$pSite['resultparam'].'", "title":"'.$title.'", "msg":"' . $error . '"}';
		}
	}
	
    static function siteDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['sitesids']);
    	$deleteMessages = $pResultArray['sitemes'];
    	
        $smarty = SmartyObj::newSmarty('AdminSitesSitesAdmin');
        
        $messageList = Array();
        foreach ($deleteMessages as $key => $value)
        {
        	$messageList[] = '"'.$key.'":"'.str_replace('^0', $key, $smarty->get_config_vars($deleteMessages[$key])).'"';
        }
        $messageList = join(',', $messageList);
        
        if ($pResultArray['alldeleted'] == 0)
        {
			$msg = $smarty->get_config_vars('str_WarningNotAllSitesDeleted');
			$title = $smarty->get_config_vars('str_TitleWarning');
			$alldeleted = '0';
        }
        else
        {
			$msg = $smarty->get_config_vars('str_MessageSiteDeleted');
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$alldeleted = '1';
        }
    	
		echo '{"success":true, "title":"' . $title . '", "msg":"' . $msg . '", "alldeleted":"' . $alldeleted . '", "idlist":"' . $deleteList . '", "messagelist": {'.$messageList.'} }';
	}

	static function displayEntry($pResultArray)
	{ 
		global $gConstants;
		global $gSession;

        $smarty = SmartyObj::newSmarty('AdminSitesSitesAdmin');

        $smarty->assign('id', 					  UtilsObj::encodeString($pResultArray['id'], true));
        $smarty->assign('sitecode', 			  UtilsObj::encodeString($pResultArray['code'], true)); 
        $smarty->assign('companycode', 			  UtilsObj::encodeString($pResultArray['companycode'],true));
        $smarty->assign('sitename', 			  UtilsObj::encodeString($pResultArray['companyName'], true));
        $smarty->assign('sitegroupcode', 		  UtilsObj::encodeString($pResultArray['siteGroupCode'], true));
        $smarty->assign('address1', 			  UtilsObj::encodeString($pResultArray['address1'], true));
        $smarty->assign('address2', 			  UtilsObj::encodeString($pResultArray['address2'], true));
        $smarty->assign('address3', 			  UtilsObj::encodeString($pResultArray['address3'], true));
        $smarty->assign('address4', 			  UtilsObj::encodeString($pResultArray['address4'], true));
        
		$additionalAddressFields = UtilsAddressObj::getAdditionalAddressFields($pResultArray['countrycode'], $pResultArray['address4']);
        $smarty->assign('add41', 				  UtilsObj::encodeString($additionalAddressFields['add41'], true));
        $smarty->assign('add42', 				  UtilsObj::encodeString($additionalAddressFields['add42'], true));
        $smarty->assign('add43', 				  UtilsObj::encodeString($additionalAddressFields['add43'], true));

        $smarty->assign('city', 				  UtilsObj::encodeString($pResultArray['city'], true));
        $smarty->assign('state', 				  UtilsObj::encodeString($pResultArray['state'], true));
        $smarty->assign('county', 				  UtilsObj::encodeString($pResultArray['county'], true));
        $smarty->assign('regioncode', 			  UtilsObj::encodeString($pResultArray['regioncode'], true));
        $smarty->assign('region', 				  UtilsObj::encodeString($pResultArray['region'], true));
        $smarty->assign('postcode', 			  UtilsObj::encodeString($pResultArray['postcode'], true));
        $smarty->assign('countrycode', 			  UtilsObj::encodeString($pResultArray['countrycode'], true));
        $smarty->assign('firstname', 			  UtilsObj::encodeString($pResultArray['firstname'], true));
        $smarty->assign('lastname', 			  UtilsObj::encodeString($pResultArray['lastname'], true));
        $smarty->assign('telephone', 			  UtilsObj::encodeString($pResultArray['telephone'], true));
        $smarty->assign('email', 			 	  UtilsObj::encodeString($pResultArray['email'], true));
        $smarty->assign('storeurl', 		 	  UtilsObj::encodeString($pResultArray['storeurl'], true));
        $smarty->assign('acceptedproducts',  	  UtilsObj::encodeString($pResultArray['acceptedproducts'], true));
        $smarty->assign('acceptallproducts', 	  UtilsObj::encodeString($pResultArray['acceptallproducts'], true));
        $smarty->assign('smtpname', 			  UtilsObj::encodeString($pResultArray['smtpproductionname'], true));
        $smarty->assign('smtpemail', 			  UtilsObj::encodeString($pResultArray['smtpproductionaddress'], true));
        $smarty->assign('isproductionsite', 	  UtilsObj::encodeString($pResultArray['isproductionsite'], true));
        $smarty->assign('sitetype', 			  UtilsObj::encodeString($pResultArray['sitetype'], true));
        $smarty->assign('siteonline', 			  UtilsObj::encodeString($pResultArray['siteonline'], true));
        $smarty->assign('sitegroup', 			  UtilsObj::encodeString($pResultArray['sitegroup'], true));
        $smarty->assign('distributioncentrecode', UtilsObj::encodeString($pResultArray['distributioncentrecode'], true));
        $smarty->assign('isactive', 			  UtilsObj::encodeString($pResultArray['isactive'], true));
        $smarty->assign('ref', 					  UtilsObj::encodeString($gSession['ref'], true));
        $smarty->assign('productlist',  		  UtilsObj::encodeString($pResultArray['productlist'], true));
        $smarty->assign('languagelist', 		  UtilsObj::encodeString($pResultArray['languagelist'], true));
        
        $smarty->assign('usersassigned', 		  isset($pResultArray['usersassigned']) ? $pResultArray['usersassigned'] : 0);
        
      
        $itemCount = count($pResultArray['openingtimeslist']);
		for ($i = 0; $i < $itemCount; $i++)
        {
        	$pResultArray['openingtimeslist'][$i]['code'] = UtilsObj::encodeString($pResultArray['openingtimeslist'][$i]['code'],true);
        	$pResultArray['openingtimeslist'][$i]['name'] = UtilsObj::encodeString($pResultArray['openingtimeslist'][$i]['name'],true);
		}
        $smarty->assign('openingtimeslist', 	  $pResultArray['openingtimeslist']);
        $smarty->assign('defaultlang', 			  UtilsObj::encodeString($pResultArray['defaultlang'], true));
        $smarty->assign('countries', 			  UtilsObj::encodeString($pResultArray['countries'], true));
        
        $itemCount = count($pResultArray['sitegroups']);
		for ($i = 0; $i < $itemCount; $i++) 
		{  	
			$pResultArray['sitegroups'][$i]['code'] = UtilsObj::encodeString($pResultArray['sitegroups'][$i]['code']);	
			$pResultArray['sitegroups'][$i]['name'] = $pResultArray['sitegroups'][$i]['code'] .' - '. UtilsObj::encodeString($pResultArray['sitegroups'][$i]['name']);	
		}
        $smarty->assign('sitegroups', 			  $pResultArray['sitegroups']);
        $smarty->assign('sitegroupdefined', 	  UtilsObj::encodeString($pResultArray['sitegroupdefined'], true));
        
        $itemCount = count($pResultArray['companies']);
		for ($i = 0; $i < $itemCount; $i++) 
		{  	
			$pResultArray['companies'][$i]['code'] = UtilsObj::encodeString($pResultArray['companies'][$i]['code']);
			$pResultArray['companies'][$i]['name'] = UtilsObj::encodeString($pResultArray['companies'][$i]['name']);
		}
        $smarty->assign('companies', 			  $pResultArray['companies']);
        
        $itemCount = count($pResultArray['productionsites']);		
        for ($i = 0; $i < $itemCount; $i++)    
        {  	
        	$pResultArray['productionsites'][$i]['code'] = UtilsObj::encodeString($pResultArray['productionsites'][$i]['code']);
        	$pResultArray['productionsites'][$i]['name'] = UtilsObj::encodeString($pResultArray['productionsites'][$i]['name']);
        }
        $smarty->assign('productionsites', 		  $pResultArray['productionsites'], true);
        
        $itemCount = count($pResultArray['distributioncentres']);
        for ($i = 0; $i < $itemCount; $i++) 
        {   	
        	$pResultArray['distributioncentres'][$i]['code'] = UtilsObj::encodeString($pResultArray['distributioncentres'][$i]['code']);
        	$pResultArray['distributioncentres'][$i]['name'] = $pResultArray['distributioncentres'][$i]['code'] .' - '. UtilsObj::encodeString($pResultArray['distributioncentres'][$i]['name']);
        }
        $smarty->assign('distributioncentres', 	  $pResultArray['distributioncentres']);
        
        $smarty->displayLocale('admin/sitessitesadmin/sitesedit.tpl');
    }
    
}

?>