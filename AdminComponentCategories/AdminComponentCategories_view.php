<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');

class AdminComponentCategories_view
{
	static function displayGrid()
	{
		global $gConstants;
        global $gSession;
		
        $smarty = SmartyObj::newSmarty('AdminComponentCategories');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
        {
        	$smarty->assign('companyLogin', true);
        	$smarty->assign('companycode', $gSession['userdata']['companycode']);
        }
        else
        {
        	$smarty->assign('companyLogin', false);
        }
        $smarty->displayLocale('admin/componentcategories/componentcategoriesgrid.tpl');
	}
	
	static function getGridData($pResultArray)
	{
		global $gConstants;
		$smarty = SmartyObj::newSmarty('AdminComponentCategories');
		
		echo '[';
		
		$itemCount = count($pResultArray);
		
		echo '[' . $itemCount . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray[$i];
			$name = LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $item['name'], 'black');
			$prompt = LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $item['prompt'], 'black');
			echo "['" . $item['id'] . "',";
			echo "'" . $item['companycode'] . "',";
			echo "'" . $item['code'] . "',";
			echo "'" . $name . "',";
			echo "'" . $prompt . "',";
			echo "'" . $item['pricingmodel'] . "',";
			echo "'" . $item['islist'] . "',";
			echo "'" . $item['active'] . "',";
			echo "'" . $item['requirespagecount'] . "',";
			echo "'" . $item['decimalplaces'] . "']";
			
			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
	}

    static function displayEntry($pTitle, $pID, $pCompanyCode, $pCode, $pName, $pPrompt, $pPricingModel, $pIsList, $requiresPageCount, $componentPricingDecimalPlaces, $pisPrivate, $pActive, $pHasComponents, $pOnlineDisplayStage, $pError = '')
    {
        global $gSession;
        global $gConstants;
        
        $showCompany = true;
        
        $smarty = SmartyObj::newSmarty('AdminComponentCategories');
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('categoryid', $pID);
        $smarty->assign('categorycode', $pCode);
        $smarty->assign('categorypricingmodel', $pPricingModel);
        $smarty->assign('categorydisplaytype', $pIsList);
        $smarty->assign('isActive', $pActive);
       	$smarty->assign('hascomponents', $pHasComponents);   
       	$smarty->assign('isprivate', $pisPrivate);   
       	$smarty->assign('requirespagecount', $requiresPageCount); 
       	$smarty->assign('decimalplaces', $componentPricingDecimalPlaces); 
        $smarty->assign('defaultlanguagecode', $gConstants['defaultlanguagecode']);
		$smarty->assign('onlinedisplaystage', $pOnlineDisplayStage);

		$smarty->assign('TPX_COMPONENT_DISPLAY_STAGE_NONE', TPX_COMPONENT_DISPLAY_STAGE_NONE);
		$smarty->assign('TPX_COMPONENT_DISPLAY_STAGE_START', TPX_COMPONENT_DISPLAY_STAGE_START);
		$smarty->assign('TPX_COMPONENT_DISPLAY_STAGE_ORDER', TPX_COMPONENT_DISPLAY_STAGE_ORDER);
		$smarty->assign('TPX_COMPONENT_DISPLAY_STAGE_ALL', TPX_COMPONENT_DISPLAY_STAGE_ALL);
       	   	
       	$pricingModelData = array
					(
						array('id' => TPX_PRICINGMODEL_PERQTY, 'name' => $smarty->get_config_vars('str_LabelPerQty')),	
						array('id' => TPX_PRICINGMODEL_PERSIDEQTY, 'name' => $smarty->get_config_vars('str_LabelPerPageQty')),
						array('id' => TPX_PRICINGMODEL_PERPRODCMPQTY, 'name' => $smarty->get_config_vars('str_LabelPerProductCmpQty')),
						array('id' => TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY, 'name' => $smarty->get_config_vars('str_LabelPerPageProductCmpQty'))	
					);
		
		$displayTypeData = array
					(
						array('id' => TPX_COMPONENTCATEGORY_DISPLAYTYPE_CHECKBOX,      		'name' => $smarty->get_config_vars('str_LabelCheckBox')),
						array('id' => TPX_COMPONENTCATEGORY_DISPLAYTYPE_LIST,	'name' => $smarty->get_config_vars('str_LabelList')),
					);
		
		 $smarty->assign('displayTypeData', $displayTypeData);
       	 $smarty->assign('pricingModelData', $pricingModelData);
       	
       	if ($pID == 0)
       	{
       		$smarty->assign('isEdit', 0);	
       		
       	}
       	else
       	{
       		$smarty->assign('isEdit', 1);	
       	}
       	
       	if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
        {
        	$showCompany = false;
        	$smarty->assign('companyLogin', true);
        	$smarty->assign('companycode', $gSession['userdata']['companycode']);
        }
        else
        {
        	$smarty->assign('companyLogin', false);
        	$smarty->assign('companycode', $pCompanyCode);
        }
        
        if ($pCode == 'PAPER' || $pCode == 'COVER')
        {
        	$showCompany = false;
        }
        
        $smarty->assign('showcompany', $showCompany);
               	              
        LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', $pName);
        
        //site group label 
        LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', $pPrompt, true, true);
        
        $smarty->assign('localizedinfomaxchars', 30);
        $smarty->assign('localizedinfowidth', 200);
        $smarty->assign('localizedinfocodesvar', 'gLocalizedCodesArray');
        $smarty->assign('localizednamelabel', $smarty->get_config_vars('str_LabelName'));
        
        $smarty->displayLocale('admin/componentcategories/componentcategoriesedit.tpl');
    }
    
    static function componentCategoriesDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['categoryids']);
        $smarty = SmartyObj::newSmarty('AdminComponentCategories');

        if ($pResultArray['alldeleted'] == 0)
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');
			$alldeleted = '0';
        }
        else
        {
			$msg = $smarty->get_config_vars('str_MessageComponentCategoriesDeleted');
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$alldeleted = '1';
        }
    	
		echo '{"success":true, "title":"' . $title . '", "msg":"' . $msg . '", "alldeleted":"' . $alldeleted . '", "idlist":"' . $deleteList . '"}';
	}
	
	static function componentCategoriesSave($pResultArray)
    {       	
       	$smarty = SmartyObj::newSmarty('AdminComponentCategories');
       
    	if ($pResultArray['result'] != '')
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');
			
			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			$name = UtilsObj::encodeString($pResultArray['name'],false);
			$prompt = UtilsObj::encodeString($pResultArray['prompt'],false);
			
			echo '{"success":true, "data":{"id":' . $pResultArray['id'] . ',"code":"' . $pResultArray['code'] . '","name":"' . $name . '","prompt":"' . $prompt . '","pricingmodel":"' . $pResultArray['pricingmodel'] . '",
        	"displaytype":"' . $pResultArray['displaytype'] . '", "displaystage":"' . $pResultArray['displaystage'] . '", "status":"' . $pResultArray['active'] . '"}}'; 
        }
    }
    
    static function componentCategoriesActivate($componentCategories)
    {
        global $gSession;
        
        $itemCount = count($componentCategories);
        
        $resultData = '{"success":true, "data":[';
        
        for ($i = 0; $i < $itemCount; $i++)
        {
			$category = $componentCategories[$i];
			$resultData .= '{"id":' . $category['recordid'] . ',"status":"' . $category['isactive'] . '"}';
        
        	if ($i != $itemCount - 1)
        	{
        		$resultData .= ",";
        	}
        }
		
        $resultData .= ']}';
        echo $resultData;
	}
    
	static function displayAdd()
	{ 
        self::displayEntry('str_TitleNewComponentCategory', 0, '', '', '', '', 0, 0, 0, 2, 0, 0, 0, TPX_COMPONENT_DISPLAY_STAGE_ORDER);
    }
    
    static function displayEdit($pResult)
	{
        self::displayEntry('str_TitleEditComponenetCategory', $pResult['id'], $pResult['companycode'], $pResult['code'], $pResult['name'], $pResult['prompt'],$pResult['pricingmodel'], $pResult['islist'], $pResult['requirespagecount'], $pResult['componetpricingdecimalplaces'], $pResult['isprivate'], $pResult['active'], $pResult['hascomponents'], $pResult['displaystage']);
    }
}

?>
