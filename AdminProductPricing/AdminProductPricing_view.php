<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');

class AdminProductPricing_view
{
	static function displayGrid($pResultArray)
	{
		global $gConstants;
		global $gSession;

        $smarty = SmartyObj::newSmarty('AdminProductPricing');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));

        $productName = LocalizationObj::getLocaleString($pResultArray['name'], $gSession['browserlanguagecode'], true);
		SmartyObj::replaceParams($smarty, 'str_TitleProductPricing', $pResultArray['code'] . ' - ' . $productName);

		$smarty->assign('title', $pResultArray['code'] . ' - ' . UtilsObj::encodeString($productName,true));
		$smarty->assign('id', $pResultArray['recordid']);
		$smarty->assign('layoutcode', $pResultArray['code']);

		if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
		{
			$smarty->assign('companyLogin', true);
		}
		else
		{
			$smarty->assign('companyLogin', false);
		}

        $smarty->displayLocale('admin/productpricing/productpricing.tpl');
	}

	static function displayPriceListGrid()
	{
		global $gConstants;
		global $gSession;

		$smarty = SmartyObj::newSmarty('AdminProductPricing');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));

        $pricingModel = TPX_PRICINGMODEL_PERQTY;

        if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
		{
			$smarty->assign('companyLogin', true);
		}
		else
		{
			$smarty->assign('companyLogin', false);
		}

        // assigning pricing models contants for use on template
        $smarty->assign('TPX_PRICINGMODEL_NOPRICING', TPX_PRICINGMODEL_NOPRICING);
        $smarty->assign('TPX_PRICINGMODEL_PERORDER', TPX_PRICINGMODEL_PERORDER);
        $smarty->assign('TPX_PRICINGMODEL_PERLINE', TPX_PRICINGMODEL_PERLINE);
        $smarty->assign('TPX_PRICINGMODEL_PERQTY', TPX_PRICINGMODEL_PERQTY);
        $smarty->assign('TPX_PRICINGMODEL_PERPAGEQTY', TPX_PRICINGMODEL_PERPAGEQTY);
        $smarty->assign('TPX_PRICINGMODEL_PERSIDEQTY', TPX_PRICINGMODEL_PERSIDEQTY);
        $smarty->assign('TPX_PRICINGMODEL_PERCHARACTER', TPX_PRICINGMODEL_PERCHARACTER);
        $smarty->assign('pricingModel', $pricingModel);

        $smarty->displayLocale('admin/productpricing/productspricelistgrid.tpl');
	}

	static function getGridData($pResultArray)
	{
		global $gSession;
		global $gConstants;

		$smarty = SmartyObj::newSmarty('AdminProductPricing');

		$itemList = $pResultArray['pricing'];
		$itemCount = count($itemList);

		echo '[';
		echo '[' . $itemCount . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$groupArray = $itemList[$i]['items'];
			$groupCount = count($groupArray);
			$groupList = Array();
			for ($j = 0; $j < $groupCount; $j++)
			{
				array_push($groupList, $groupArray[$j]['groupcode']);
			}
			sort($groupList);

			$deleteCode = '';
			$name = '';
			for ($j = 0; $j < $groupCount; $j++)
			{
				$code = $groupList[$j];

				if ($code =='')
				{
					$deleteCode .= $smarty->get_config_vars('str_LabelDefault');
					$name .= '<i>' . $smarty->get_config_vars('str_LabelDefault') . '</i>';
				}
				else
				{
				   $deleteCode .= $code;
				   $name .= $code;
				}

				$deleteCode .= ', ';
				$name .= '<br>';
			}
			$deleteCode = substr($deleteCode, 0, strlen($deleteCode) - 2);
			$name = substr($name, 0, strlen($name) - 4);

			$priceItemArray = DatabaseObj::priceStringToArray(TPX_PRICINGMODEL_PERQTY,$itemList[$i]['price']);
			$priceItemCount = count($priceItemArray);

			$priceStartHTML = '';
       		$priceEndHTML = '';
        	$priceBaseHTML = '';
        	$priceUnitSellHTML = '';
        	$priceTotalDiscountHTML = '';

			for ($j = 0; $j < $priceItemCount; $j++)
			{
				$priceStartHTML .= $priceItemArray[$j]['startqty'] . '<br>';
				$priceEndHTML .= $priceItemArray[$j]['endqty'] . '<br>';
				$priceBaseHTML .= $priceItemArray[$j]['baseprice'] . '<br>';
				$priceUnitSellHTML .= $priceItemArray[$j]['unitsell'] . '<br>';
				$priceTotalDiscountHTML .= $priceItemArray[$j]['linesubtract'] . '<br>';
			}

			$priceDescription = LocalizationObj::initAdminDisplayLocalizedNamesList($smarty,$itemList[$i]['pricedescription'], 'black');

			$additionalInformation = LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $itemList[$i]['productinfo'], 'black');

			echo "['" . $itemList[$i]['parentid'] . "',";

			if ($gConstants['optionms'])
			{
				echo "'" . $itemList[$i]['companycode'] . "',";
			}

			echo "'" . $name . "',";
			echo "'" . $priceDescription . "',";
			echo "'" . $priceStartHTML . "',";
			echo "'" . $priceEndHTML . "',";
			echo "'" . $priceBaseHTML . "',";
			echo "'" . $priceUnitSellHTML . "',";
			echo "'" . $priceTotalDiscountHTML . "',";

			if ($itemList[$i]['taxcode'] != '')
			{
				$includesTax = $itemList[$i]['taxratecode'] . ' - ' . number_format($itemList[$i]['taxrate'], 2, '.', '') . '%';
			}
			else
			{
				$includesTax = $smarty->get_config_vars('str_LabelNone');
			}

			echo "'" . $includesTax . "',";

			if ($gConstants['optionms'])
			{
				echo "'" . $itemList[$i]['isactive'] . "',";
				echo "'" . $itemList[$i]['companycode'] . "']";
			}
			else
			{
				echo "'" . $itemList[$i]['isactive'] . "']";
			}

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
	}

	static function getPriceListGridData($pResultArray)
	{
		global $gConstants;

		$smarty = SmartyObj::newSmarty('ComponentPricing');

		$pricingModel = $pResultArray['pricingmodel'];
		$itemList = $pResultArray['pricelists'];
        $itemCount = count($itemList);

        echo '[';

		echo '[' . $itemCount . '],';

        for ($i = 0; $i < $itemCount; $i++)
        {
            $priceItemArray = DatabaseObj::priceStringToArray($pricingModel, $itemList[$i]['price']);
        	$priceItemCount = count($priceItemArray);

            if ($pricingModel == TPX_PRICINGMODEL_PERQTY)
            {
	            $priceStartHTML = '';
	            $priceEndHTML = '';
	            $priceBaseHTML = '';
	            $priceUnitSellHTML = '';
	            $priceTotalDiscountHTML = '';
            }
            elseif($pricingModel == TPX_PRICINGMODEL_PERSIDEQTY)
            {
            	$priceStartQtyHTML = '';
	            $priceEndQtyHTML = '';
	            $priceStartpageCountHTML = '';
	            $priceEndPageCountHTML = '';
	            $priceBaseHTML = '';
	            $priceUnitSellHTML = '';
	            $priceTotalDiscountHTML = '';

            }

			for ($j = 0; $j < $priceItemCount; $j++)
            {
	            if ($pricingModel == TPX_PRICINGMODEL_PERQTY)
	            {
		            $priceStartHTML .= $priceItemArray[$j]['startqty'] . '<br>';
        	    	$priceEndHTML .= $priceItemArray[$j]['endqty'] . '<br>';
        	    	$priceBaseHTML .= $priceItemArray[$j]['baseprice'] . '<br>';
        	    	$priceUnitSellHTML .= $priceItemArray[$j]['unitsell'] . '<br>';
        	    	$priceTotalDiscountHTML .= $priceItemArray[$j]['linesubtract'] . '<br>';
	            }
	            elseif($pricingModel == TPX_PRICINGMODEL_PERSIDEQTY)
	            {
	            	$priceStartQtyHTML .= $priceItemArray[$j]['startqty'] . '<br>';
	            	$priceEndQtyHTML .= $priceItemArray[$j]['endqty'] . '<br>';
		            $priceStartpageCountHTML .= $priceItemArray[$j]['startpagecount'] . '<br>';
		            $priceEndPageCountHTML .= $priceItemArray[$j]['endpagecount'] . '<br>';
		            $priceBaseHTML .= $priceItemArray[$j]['baseprice'] . '<br>';
		            $priceUnitSellHTML .= $priceItemArray[$j]['unitsell'] . '<br>';
		            $priceTotalDiscountHTML .= $priceItemArray[$j]['linesubtract'] . '<br>';
	            }
            }

        	echo "['" . $itemList[$i]['id'] . "',";
			echo "'" .  $itemList[$i]['companycode'] . "',";
			echo "'" .  $itemList[$i]['pricelistlocalcode'] . "',";
			echo "'" .  UtilsObj::encodeString($itemList[$i]['pricelistname'],true) . "',";

			if ($pricingModel == TPX_PRICINGMODEL_PERQTY)
            {
	            echo "'" . $priceStartHTML . "',";
				echo "'" . $priceEndHTML . "',";
            }
            elseif($pricingModel == TPX_PRICINGMODEL_PERSIDEQTY)
            {
               echo "'" . $priceStartQtyHTML . "',";
			   echo "'" . $priceEndQtyHTML . "',";
			   echo "'" . $priceStartpageCountHTML . "',";
			   echo "'" . $priceEndPageCountHTML . "',";
            }

			echo "'" . $priceBaseHTML . "',";
			echo "'" . $priceUnitSellHTML . "',";
			echo "'" . $priceTotalDiscountHTML . "',";

			if ($itemList[$i]['taxcode'] != '')
			{
				$includesTax = $itemList[$i]['taxratecode'] . ' - ' . number_format($itemList[$i]['taxrate'], 2, '.', '') . '%';
			}
			else
			{
				$includesTax = $smarty->get_config_vars('str_LabelNone');
			}

			echo "'" . $includesTax . "',";

			if ($gConstants['optionms'])
			{
				echo "'" .  $itemList[$i]['active'] . "',";
				echo "'" . $itemList[$i]['companycode'] . "']";
			}
			else
			{
				echo "'" . $itemList[$i]['active'] . "']";
			}

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
        }
        echo ']';

	}

	static function displayEntry($pTitle, $pDataArray, $pActionButtonName, $pError = '', $pErrorInfo = '')
    {
       	global $gSession;
		global $gConstants;

       	$smarty = SmartyObj::newSmarty('AdminProductPricing');
       	$smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $companyCode = '';
        $includeGlobal = false;
        $controlDisabled = true;
        $smarty->assign('companyLogin', false);

        if ($gConstants['optionscbo'])
        {
        	$smarty->assign('scbo',  1);
        	$smarty->assign('externalcartchecked', $pDataArray['shoppingcarttype']);
        }
        else
        {
        	$smarty->assign('scbo',  0);
        	$smarty->assign('externalcartchecked',  0);
        }

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
				if ($pDataArray['productcompanycode'] != '')
				{
					$companyCode = $pDataArray['productcompanycode'];
					$includeGlobal = 0;
					$controlDisabled = true;
				}
				else
				{
					$companyCode = $pDataArray['companycode'];
					$includeGlobal = 1;
					$controlDisabled = false;
				}
				$smarty->assign('companyLogin', false);
			}
        }


        $name = LocalizationObj::getLocaleString($pDataArray['productname'], '', true);
        $title = $pDataArray['productcode'] . ' - ' . $name;

        SmartyObj::replaceParams($smarty, $pTitle, $title, true);

	    $smarty->assign('title', $smarty->get_template_vars($pTitle));
	    $smarty->assign('id', $pDataArray['recordid']);
	    $smarty->assign('productcode', $pDataArray['productcode']);
	    $smarty->assign('categorycode', 'PRODUCT');
	    $smarty->assign('parentid', $pDataArray['pricelinkparentid']);
	    $smarty->assign('companycode', $companyCode);
	    $smarty->assign('includeglobal', $includeGlobal);
	    $smarty->assign('controldisabled', $controlDisabled);

	    $smarty->assign('pricingmodel', $pDataArray['pricingmodel']);
	    $smarty->assign('quantityisdropdown', $pDataArray['quantityisdropdown']);
	    $smarty->assign('price', $pDataArray['price']);
	    $smarty->assign('title', $smarty->get_template_vars($pTitle));

	    $smarty->assign('ispricelist', $pDataArray['ispricelist']);
	    $smarty->assign('pricelistid', $pDataArray['recordid']);
	    $smarty->assign('pricelinkid', $pDataArray['pricelinkparentid']);
	    $smarty->assign('isactive', $pDataArray['isactive']);

        $smarty->assign('defaultlanguagecode', $gConstants['defaultlanguagecode']);

		$smarty->assign('inheritparentqty', $pDataArray['inheritparentqty']);
		$smarty->assign('allowinherit', $pDataArray['allowinherit']);

	    $itemsList = Array();
        $itemsArray = $pDataArray['items'];
        $itemCount = count($itemsArray);
        $defaultChecked = 0;

        for ($i = 0; $i < $itemCount; $i++)
        {
            if ($itemsArray[$i]['groupcode'] == '')
            {
            	$defaultChecked = '1';
            }

            array_push($itemsList, $itemsArray[$i]['groupcode']);
        }

		// build the license keys list
        $existingCodesList = $pDataArray['existinggroupcodes'];
        $allCodesArray = $pDataArray['allgroupcodes'];
        $itemCount = count($allCodesArray);
        $licensekeyIndeces = Array();

        for ($i = 0; $i < $itemCount; $i++)
        {
            $groupCode = $allCodesArray[$i]['code'];

            if (! in_array($groupCode, $existingCodesList))
            {
			    if ($defaultChecked == '1' || in_array($groupCode, $itemsList))
			    {
		    		array_push($licensekeyIndeces, $i);
		    	}
            }
        }

		$smarty->assign('assignedLicenseKeys', $licensekeyIndeces);
		$smarty->assign('defaultChecked', $defaultChecked);
		$smarty->assign('priceinfo', UtilsObj::encodeString($pDataArray['priceinfo'], true));
		$smarty->assign('taxcode', $pDataArray['taxcode']);
		$smarty->assign('producttype', $pDataArray['producttype']);

        //Product Info
       	LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', $pDataArray['priceinfo']);
        $smarty->assign('localizedinfomaxchars', 30);
        $smarty->assign('localizedinfowidth', 200);
        $smarty->assign('localizedinfocodesvar', 'gLocalizedCodesArray');
        $smarty->assign('localizednamelabel', $smarty->get_config_vars('str_LabelName'));

        //price description
        LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', $pDataArray['pricedescription'], true, true);

        $smarty->displayLocale('admin/productpricing/productpricingedit.tpl');
    }

    static function displayPriceListEntry($pID, $pCompanyCode, $pCode, $pLocalCode, $pName, $quantityIsDropDown, $pPricingModel, $pPrice, $taxCode, $isActive)
    {
       	global $gSession;
       	global $gConstants;

       	$smarty = SmartyObj::newSmarty('AdminProductPricing');
       	$smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));

       	$smarty->assign('ID', $pID);
        $smarty->assign('company', $pCompanyCode);
        $smarty->assign('code', $pLocalCode);
        $smarty->assign('name', UtilsObj::encodeString($pName,true));
        $smarty->assign('pricingModel', $pPricingModel);
        $smarty->assign('price', $pPrice);
        $smarty->assign('isActive', $isActive);
        $smarty->assign('quantityIsDropDown', $quantityIsDropDown);
        $smarty->assign('taxcode', $taxCode);
        $smarty->assign('companyLogin', false);

       	if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
        {
        	$smarty->assign('companyLogin', true);
        }

        $smarty->displayLocale('admin/productpricing/productspricelistedit.tpl');
    }

	static function pricingActivate($pPricing)
    {
        global $gSession;

        $resultData = '{"success":true, "data":[';

        foreach ($pPricing as $price)
        {
			$resultData .= '{"id":' . $price['id'] . ',"active":"' . $price['isactive'] . '"},';
        }

        $resultData .= ']}';

        echo $resultData;
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

    static function ProductPricingSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('ComponentPricing');

    	if ($pResultArray['result'] != '')
        {
            $msg = str_replace('^0', $pResultArray['resultparam'], $smarty->get_config_vars($pResultArray['result']));
			$title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			echo '{"success":true}';
        }
    }

    static function productPriceListSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('ComponentPricing');

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

    static function productPricingDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['pricingids']);

        $smarty = SmartyObj::newSmarty('AdminProductPricing');

        if ($pResultArray['alldeleted'] == 0)
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');
			$alldeleted = '0';
        }
        else
        {
			$msg = $smarty->get_config_vars('str_MessageTaxZonesDeleted');
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$alldeleted = '1';
        }

		echo '{"success":true, "title":"' . $title . '", "msg":"' . $msg . '", "alldeleted":"' . $alldeleted . '", "idlist":"' . $deleteList . '"}';
	}

	static function productPriceListDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['pricelistids']);

        $smarty = SmartyObj::newSmarty('AdminProductPricing');

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

	static function getLicenseKeyFromCompany($pResultArray)
    {
    	global $gConstants;

		$allCodesArray = $pResultArray['groupcodes'];

		echo '[';

		$itemCount = count($allCodesArray);

		for ($i = 0; $i < $itemCount; $i++)
		{
			echo "['" . $allCodesArray[$i]['id'] . "',";
			echo "'" . $allCodesArray[$i]['code'] . "',";
			echo "'" . UtilsObj::encodeString($allCodesArray[$i]['name'],true) . "',";
			echo "'" . $allCodesArray[$i]['active'] . "']";

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
    }

	static function displayAdd($pDataArray, $pError = '', $pErrorInfo = '')
	{
        self::displayEntry('str_TitleNewPricing', $pDataArray, 'str_ButtonAdd', $pError, $pErrorInfo);
    }

    static function displayAddPriceList()
	{
        $pricingModel = $_GET['pricingmodel'];
        self::displayPriceListEntry(0,'', '', '', '', '', $pricingModel, '', '', 0);
    }

    static function displayEdit($pDataArray, $pError = '', $pErrorInfo = '')
	{
	   self::displayEntry('str_TitleEditPricing', $pDataArray, 'str_ButtonUpdate', $pError, $pErrorInfo);
    }

    static function displayPriceListEdit($pResultArray)
	{
        self::displayPriceListEntry($pResultArray['id'], $pResultArray['companycode'], $pResultArray['pricelistcode'], $pResultArray['pricelistlocalcode'], $pResultArray['pricelistname'], $pResultArray['quantityisdropdown'],   $pResultArray['pricingmodel'], $pResultArray['price'], $pResultArray['taxcode'], $pResultArray['active']);
    }
}

?>
