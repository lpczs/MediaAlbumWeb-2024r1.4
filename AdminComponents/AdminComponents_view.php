<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');

class AdminComponents_view
{
	static function displayGrid()
	{
		global $gConstants;
        global $gSession;

		$smarty = SmartyObj::newSmarty('Components');
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
        	$smarty->assign('companycode', '');
        }
        $smarty->displayLocale('admin/components/componentsgrid.tpl');
	}

	static function getGridData($pResultArray)
	{
		global $gConstants;

		$smarty = SmartyObj::newSmarty('Components');

		$itemCount = count($pResultArray['components']);

		echo '[';

		echo '[' . $pResultArray['total'] . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray['components'][$i];
			$name = LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $item['name'], 'black');

			echo "['" . $item['id'] . "',";
			
			if ($gConstants['optionms'])
			{
				echo "'" . $item['companycode'] . "',";
				echo "'" . $item['code'] . "',";
				echo "'" . $item['localcode'] . "',";
				echo "'" . $item['skucode'] . "',";
				echo "'" . $name . "',";
				echo "'" . $item['active'] . "',";
				echo "'" . $item['categorycode'] . "',";
				echo "'" . $item['companycode'] . "']";
			}
			else
			{
				echo "'" . $item['code'] . "',";
				echo "'" . $item['localcode'] . "',";
				echo "'" . $item['skucode'] . "',";
				echo "'" . $name . "',";
				echo "'" . $item['active'] . "',";
				echo "'" . $item['categorycode'] . "']";
			}
			
			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
	}

    static function displayEntry($pWindowTitle, $pCompanyCode, $pCategoryCompanyCode, $pID, $pCategoryCode, $pCode, $pLocalCode, $pSku, $pName, $pInfo, $pMoreInfoLinkURL, $pMoreInfoLinkText,
		$pUnitCost, $pMinPageCount, $pMaxPageCount, $pWeight, $pDefault, $pKeyWordGroupHeaderID, $pOrderFooterUsesProdQty, $pOrderFooterTaxLevel, $pStoreWhenNotSelected, $pActive, $pError = '')
    {
       	global $gSession;
		global $gConstants;

       	$smarty = SmartyObj::newSmarty('Components');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
       	$priceListDataArray = Array();
       	$companyCode = '';
       	$includeGlobal = false;
        $controlDisabled = true;
		$smarty->assign('title', $smarty->get_config_vars($pWindowTitle));
		$smarty->assign('componentcode', $pCode);
       	$smarty->assign('id', $pID);
		$smarty->assign('localcode', $pLocalCode);
		$smarty->assign('sku', $pSku);
       	$smarty->assign('unitCost', $pUnitCost);
       	$smarty->assign('minPageCount', $pMinPageCount);
       	$smarty->assign('maxPageCount', $pMaxPageCount);
       	$smarty->assign('weight', $pWeight);
		$smarty->assign('isActive', $pActive);
		$smarty->assign('checkedByDefault', $pDefault);
		$smarty->assign('keywordsGroupId', $pKeyWordGroupHeaderID);
		$smarty->assign('orderfooterusesprodqty', $pOrderFooterUsesProdQty);
        $smarty->assign('defaultlanguagecode', $gConstants['defaultlanguagecode']);
        $smarty->assign('storewhennotselected', $pStoreWhenNotSelected);
		$smarty->assign('companyLogin', false);

        if ($gConstants['optionms'])
		{
			if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
			{
				$companyCode = $gSession['userdata']['companycode'];
				$includeGlobal = 0;
				$controlDisabled = true;
				$smarty->assign('companyLogin', true);
			}
			else
			{
				if ($pCategoryCompanyCode != '')
				{
					$companyCode = $pCategoryCompanyCode;
					$includeGlobal = 0;
					$controlDisabled = true;
				}
				else
				{
					$companyCode = '';
					$includeGlobal = 1;
					$controlDisabled = false;
				}
			}
        }
        
        $smarty->assign('companycode', $companyCode);
	    $smarty->assign('includeglobal', $includeGlobal);
	    $smarty->assign('controldisabled', $controlDisabled);

        //Component Name
       	LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', $pName);
        //component info
        LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', $pInfo, true, true);

        $smarty->assign('localizedinfomaxchars', 30);
        $smarty->assign('localizedinfowidth', 200);
        $smarty->assign('localizedinfocodesvar', 'gLocalizedCodesArray');
        $smarty->assign('localizednamelabel', $smarty->get_config_vars('str_LabelName'));
		
		// More info URL.
		$moreInfoLinkTextCodesJavaScript = '';
		$moreInfoLinkTextNamesJavaScript = '';

		// Check if the more info link can be added.
		$componentCategoryMoreInfoFilter = ['CALENDARCUSTOMISATION', 'TAOPIXAI', 'SINGLEPRINT', 'SINGLEPRINTOPTION'];
		$canShowMoreInfoLink = ! in_array($pCategoryCode, $componentCategoryMoreInfoFilter);

		if ($canShowMoreInfoLink) {
			// Process the localized string.
			$moreInfoLinkTextCodesJavaScript = 'var gMoreInfoLinkTextCodes = new Array(';
			$moreInfoLinkTextNamesJavaScript = 'var gMoreInfoLinkTextNames = new Array(';

			$localizedMoreInfoLinkTextStringList = explode('<p>', $pMoreInfoLinkText);
			$localizedCount = count($localizedMoreInfoLinkTextStringList);

			if ($localizedMoreInfoLinkTextStringList[$localizedCount - 1] == '')
			{
				$localizedCount--;
			}

			for ($i = 0; $i < $localizedCount; $i++)
			{
				// Split each language item into its code and name.
				$charPos = strpos($localizedMoreInfoLinkTextStringList[$i], ' ');
				$localizedItemCode = substr($localizedMoreInfoLinkTextStringList[$i], 0, $charPos);
				$localizedItemString = substr($localizedMoreInfoLinkTextStringList[$i], $charPos + 1);
				$localizedItemString = UtilsObj::encodeString($localizedItemString, true);
				$moreInfoLinkTextCodesJavaScript .= '"' . $localizedItemCode . '"';

				if ($i < ($localizedCount -1))
				{
					$moreInfoLinkTextCodesJavaScript .= ',';
				}

				$moreInfoLinkTextNamesJavaScript .= '"' . $localizedItemString . '"';
				if ($i < ($localizedCount -1))
				{
					$moreInfoLinkTextNamesJavaScript .= ',';
				}
			}

			// Close the javascript tags.
			$moreInfoLinkTextCodesJavaScript .= ');';
			$moreInfoLinkTextNamesJavaScript .= ');';
		}

		$smarty->assign('canshowmoreinfolink', $canShowMoreInfoLink);
		$smarty->assign('moreinfolinkurl', $pMoreInfoLinkURL);
		$smarty->assign('moreinfolinktextcodes', $moreInfoLinkTextCodesJavaScript);
		$smarty->assign('moreinfolinktextnames', $moreInfoLinkTextNamesJavaScript);

        $taxLevelsArray = Array(
        	Array('id' => 1, 'name' => $smarty->get_config_vars('str_LabelTaxLevel1')),
        	Array('id' => 2, 'name' => $smarty->get_config_vars('str_LabelTaxLevel2')),
        	Array('id' => 3, 'name' => $smarty->get_config_vars('str_LabelTaxLevel3')),
        	Array('id' => 4, 'name' => $smarty->get_config_vars('str_LabelTaxLevel4')),
        	Array('id' => 5, 'name' => $smarty->get_config_vars('str_LabelTaxLevel5'))
        );
        
        $smarty->assign('taxlevellist', $taxLevelsArray);
        $smarty->assign('taxlevel', $pOrderFooterTaxLevel);

		$componentPreview = UtilsObj::getAssetRequest($pCode, 'components');

		if ($componentPreview !== '') 
		{
			$smarty->assign('componentpreview', $componentPreview);
		}
		else
		{
			$smarty->assign('componentpreview', UtilsObj::correctPath($gSession['webbrandwebroot']) . 'images/admin/nopreview.gif');
		}
		
		// recommended image sizes
		$sizes = DatabaseObj::getRecommendedImageSizes('components');
		$previewImageText = str_replace(array('^rw', '^rh'), array($sizes['width'], $sizes['height']), $smarty->get_config_vars('str_LabelSelectPreviewImage'));
		$smarty->assign('previewImageText', $previewImageText);

        $smarty->displayLocale('admin/components/componentsedit.tpl');
    }

    static function componentsDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['componentids']);
        $smarty = SmartyObj::newSmarty('Components');

        if ($pResultArray['alldeleted'] == 0)
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');
			$alldeleted = '0';
        }
        else
        {
			$msg = $smarty->get_config_vars('str_MessageComponentDeleted');
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
        	"displaytype":"' . $pResultArray['displaytype'] . '", "status":"' . $pResultArray['active'] . '"}}';
        }
    }

    static function componentTypesActivate($componentTypes)
    {
        global $gSession;

        $itemCount = count($componentTypes);

        $resultData = '{"success":true, "data":[';

        for ($i = 0; $i < $itemCount; $i++)
        {
			$type = $componentTypes[$i];
			$resultData .= '{"id":' . $type['recordid'] . ',"status":"' . $type['isactive'] . '"}';

        	if ($i != $itemCount - 1)
        	{
        		$resultData .= ",";
        	}
        }

        $resultData .= ']}';
        echo $resultData;
	}

	static function displayAdd($pCategoryCode)
	{
        global $gConstants;
        
        $categoryCompanyCode = '';
        
        if ($gConstants['optionms'])
		{
			$categoryCompanyCode = $_GET['categorycompanycode'];
		}

        self::displayEntry('str_SectionWindowTitleAddComponents', '', $categoryCompanyCode, 0, $pCategoryCode, '', '', '', '', '', '', 0, 0, 0,0,0,0,0,0,1,1,0);
    }

    static function displayEdit($pResult)
	{
        self::displayEntry('str_SectionWindowTitleEditComponents', $pResult['companycode'], $pResult['categorycompanycode'], $pResult['id'], $pResult['categorycode'], $pResult['code'], $pResult['localcode'], $pResult['skucode'], $pResult['name'], $pResult['info'],
				$pResult['moreinfolinkurl'], $pResult['moreinfolinktext'],
        		$pResult['unitcost'], $pResult['minpagecount'], $pResult['maxpagecount'], $pResult['weight'], $pResult['default'], $pResult['keywordgroupheaderid'], $pResult['orderfooterusesproductquantity'], $pResult['orderfootertaxlevel'], $pResult['storewhennotselected'], $pResult['active']);
    }

    static function componentSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('Components');

    	if ($pResultArray['result'] != '')
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			$name = UtilsObj::encodeString($pResultArray['name'],false);

			echo '{"success":true, "data":{"id":' . $pResultArray['id'] . ',"companycode":"' . $pResultArray['company'] . '","code":"' . $pResultArray['code'] . '","skucode":"' . $pResultArray['skucode'] . '",
        		"category":"' . $pResultArray['categorycode'] . '","name":"' . $name . '","status":"' . $pResultArray['isactive'] . '"}}';
        }
    }

    static function uploadPreviewImage($pResultArray)
    {
    	if ($pResultArray['result'] == '')
    	{
			$width = $pResultArray['width'];
			$height = $pResultArray['height'];
			$message = '';
			if (($width > $pResultArray['recommendedwidth']) || ($height > $pResultArray['recommendedheight']))
			{
		       	$smarty = SmartyObj::newSmarty('Components');
				$message = $smarty->get_config_vars('str_MessageLogoDimensions');
				$searchFor = ['^w', '^h', '^rw', '^rh'];
				$replaceWith = [$width, $height, $pResultArray['recommendedwidth'], $pResultArray['recommendedheight']];
				$message = str_replace($searchFor, $replaceWith, $message);
				
				echo '{"success":true, "msg":"' . $message . '"}';
			}
			else
			{
				echo '{"success":true, "msg":""}';
			}
    	}
    	else
    	{
			echo '{success: false, "msg":""}';
    	}
    }
}

?>
