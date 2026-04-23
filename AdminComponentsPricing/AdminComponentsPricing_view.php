<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');

class AdminComponentsPricing_view
{
	static function displayGrid($pResultArray)
	{
		global $gConstants;
		global $gSession;

		$smarty = SmartyObj::newSmarty('ComponentsPricing');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));

        $pricingModel = $_GET['pricingmodel'];

        if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
		{
			$smarty->assign('companyLogin', true);
		}
		else
		{
			$smarty->assign('companyLogin', false);
		}

        $name = LocalizationObj::getLocaleString($pResultArray['name'], '', true);
        $title =  UtilsObj::encodeString($pResultArray['code'] . ' - ' . $name, true);

        SmartyObj::replaceParams($smarty, 'str_TitleComponentPricing', $title);
	    $smarty->assign('title', $smarty->get_template_vars('str_TitleComponentPricing'));

        // assigning pricing models contants for use on template
        $smarty->assign('TPX_PRICINGMODEL_NOPRICING', TPX_PRICINGMODEL_NOPRICING);
        $smarty->assign('TPX_PRICINGMODEL_PERORDER', TPX_PRICINGMODEL_PERORDER);
        $smarty->assign('TPX_PRICINGMODEL_PERLINE', TPX_PRICINGMODEL_PERLINE);
        $smarty->assign('TPX_PRICINGMODEL_PERQTY', TPX_PRICINGMODEL_PERQTY);
        $smarty->assign('TPX_PRICINGMODEL_PERPAGEQTY', TPX_PRICINGMODEL_PERPAGEQTY);
        $smarty->assign('TPX_PRICINGMODEL_PERSIDEQTY', TPX_PRICINGMODEL_PERSIDEQTY);
        $smarty->assign('TPX_PRICINGMODEL_PERCHARACTER', TPX_PRICINGMODEL_PERCHARACTER);
        $smarty->assign('pricingModel', $pricingModel);

        $smarty->displayLocale('admin/componentspricing/componentspricinggrid.tpl');
	}

	static function displayPriceListGrid()
	{
		global $gConstants;
		global $gSession;

		$smarty = SmartyObj::newSmarty('ComponentsPricing');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));

        $pricingModel = $_GET['pricingmodel'];

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

        $smarty->displayLocale('admin/componentspricing/componentspricelistgrid.tpl');
	}

	static function getGridData($pResultArray)
	{
		$smarty = SmartyObj::newSmarty('ComponentPricing');

		$pricingModel = $pResultArray['pricingmodel'];
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

            $licenseKeys = '';

            for ($j = 0; $j < $groupCount; $j++)
            {
                $code = $groupList[$j];

                if ($code =='')
                {
                    $licenseKeys .= '<i>' . $smarty->get_config_vars('str_LabelDefault') . '</i>';
                }
                else
                {
                   $licenseKeys .= $code;
                }


                $licenseKeys .= '<br>';
            }

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
            elseif($pricingModel == TPX_PRICINGMODEL_PERPRODCMPQTY)
            {
            	$priceStartQtyHTML = '';
	            $priceEndQtyHTML = '';
	            $priceStartComponentCountHTML = '';
	            $priceEndComponentCountHTML = '';
	            $priceBaseHTML = '';
	            $priceUnitSellHTML = '';
	            $priceTotalDiscountHTML = '';

            }
            elseif($pricingModel == TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY)
            {
            	$priceStartQtyHTML = '';
	            $priceEndQtyHTML = '';
	            $priceStartComponentCountHTML = '';
	            $priceEndComponentCountHTML = '';
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
	            elseif($pricingModel == TPX_PRICINGMODEL_PERPRODCMPQTY)
	            {
	            	$priceStartQtyHTML .= $priceItemArray[$j]['startqty'] . '<br>';
	            	$priceEndQtyHTML .= $priceItemArray[$j]['endqty'] . '<br>';
		            $priceStartComponentCountHTML .= $priceItemArray[$j]['startcmpqty'] . '<br>';
		            $priceEndComponentCountHTML .= $priceItemArray[$j]['endcmpqty'] . '<br>';
		            $priceBaseHTML .= $priceItemArray[$j]['baseprice'] . '<br>';
		            $priceUnitSellHTML .= $priceItemArray[$j]['unitsell'] . '<br>';
		            $priceTotalDiscountHTML .= $priceItemArray[$j]['linesubtract'] . '<br>';
	            }
	            elseif($pricingModel == TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY)
	            {
	            	$priceStartQtyHTML .= $priceItemArray[$j]['startqty'] . '<br>';
	            	$priceEndQtyHTML .= $priceItemArray[$j]['endqty'] . '<br>';
	            	$priceStartComponentCountHTML .= $priceItemArray[$j]['startcmpqty'] . '<br>';
		            $priceEndComponentCountHTML .= $priceItemArray[$j]['endcmpqty'] . '<br>';
		            $priceStartpageCountHTML .= $priceItemArray[$j]['startpagecount'] . '<br>';
		            $priceEndPageCountHTML .= $priceItemArray[$j]['endpagecount'] . '<br>';
		            $priceBaseHTML .= $priceItemArray[$j]['baseprice'] . '<br>';
		            $priceUnitSellHTML .= $priceItemArray[$j]['unitsell'] . '<br>';
		            $priceTotalDiscountHTML .= $priceItemArray[$j]['linesubtract'] . '<br>';
	            }
            }

        	echo "['" . $itemList[$i]['id'] . "',";
			echo "'" .  $itemList[$i]['companycode'] . "',";
			echo "'" .  $licenseKeys . "',";

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
            elseif($pricingModel == TPX_PRICINGMODEL_PERPRODCMPQTY)
            {
               echo "'" . $priceStartQtyHTML . "',";
			   echo "'" . $priceEndQtyHTML . "',";
			   echo "'" . $priceStartComponentCountHTML . "',";
			   echo "'" . $priceEndComponentCountHTML . "',";
            }
            elseif($pricingModel == TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY)
            {
               echo "'" . $priceStartQtyHTML . "',";
			   echo "'" . $priceEndQtyHTML . "',";
			   echo "'" . $priceStartComponentCountHTML . "',";
			   echo "'" . $priceEndComponentCountHTML . "',";
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

			echo "'" .  $itemList[$i]['active'] . "',";
			echo "'" . $itemList[$i]['companycode'] . "']";

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

		$pricingModel = $pResultArray['pricingmodel'];
		$itemList = $pResultArray['pricelists'];
        $itemCount = count($itemList);
        $smarty = SmartyObj::newSmarty('ComponentPricing');

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
            elseif($pricingModel == TPX_PRICINGMODEL_PERPRODCMPQTY)
            {
            	$priceStartQtyHTML = '';
	            $priceEndQtyHTML = '';
	            $priceStartComponentCountHTML = '';
	            $priceEndComponentCountHTML = '';
	            $priceBaseHTML = '';
	            $priceUnitSellHTML = '';
	            $priceTotalDiscountHTML = '';

            }
            elseif($pricingModel == TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY)
            {
            	$priceStartQtyHTML = '';
	            $priceEndQtyHTML = '';
	            $priceStartComponentCountHTML = '';
	            $priceEndComponentCountHTML = '';
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
	            elseif($pricingModel == TPX_PRICINGMODEL_PERPRODCMPQTY)
	            {
	            	$priceStartQtyHTML .= $priceItemArray[$j]['startqty'] . '<br>';
	            	$priceEndQtyHTML .= $priceItemArray[$j]['endqty'] . '<br>';
		            $priceStartComponentCountHTML .= $priceItemArray[$j]['startcmpqty'] . '<br>';
		            $priceEndComponentCountHTML .= $priceItemArray[$j]['endcmpqty'] . '<br>';
		            $priceBaseHTML .= $priceItemArray[$j]['baseprice'] . '<br>';
		            $priceUnitSellHTML .= $priceItemArray[$j]['unitsell'] . '<br>';
		            $priceTotalDiscountHTML .= $priceItemArray[$j]['linesubtract'] . '<br>';
	            }
	            elseif($pricingModel == TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY)
	            {
	            	$priceStartQtyHTML .= $priceItemArray[$j]['startqty'] . '<br>';
	            	$priceEndQtyHTML .= $priceItemArray[$j]['endqty'] . '<br>';
	            	$priceStartComponentCountHTML .= $priceItemArray[$j]['startcmpqty'] . '<br>';
		            $priceEndComponentCountHTML .= $priceItemArray[$j]['endcmpqty'] . '<br>';
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
            elseif($pricingModel == TPX_PRICINGMODEL_PERPRODCMPQTY)
            {
               echo "'" . $priceStartQtyHTML . "',";
			   echo "'" . $priceEndQtyHTML . "',";
			   echo "'" . $priceStartComponentCountHTML . "',";
			   echo "'" . $priceEndComponentCountHTML . "',";
            }
            elseif($pricingModel == TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY)
            {
               echo "'" . $priceStartQtyHTML . "',";
			   echo "'" . $priceEndQtyHTML . "',";
			   echo "'" . $priceStartComponentCountHTML . "',";
			   echo "'" . $priceEndComponentCountHTML . "',";
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

       	$smarty = SmartyObj::newSmarty('ComponentsPricing');
       	$smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
	    $name = LocalizationObj::getLocaleString($pDataArray['componentname'], '', true);
        $title =  UtilsObj::encodeString($pDataArray['categorycode'] . ' - ' . $name, true);
        $companyCode = '';
        $includeGlobal = false;
        $controlDisabled = true;
        $smarty->assign('companyLogin', false);

        SmartyObj::replaceParams($smarty, $pTitle, $title);

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
				if ($pDataArray['componentcompanycode'] != '')
				{
					$companyCode = $pDataArray['componentcompanycode'];
					$includeGlobal = 0;
					$controlDisabled = true;
				}
				else
				{
					$companyCode = '';
					$includeGlobal = 1;
					$controlDisabled = false;
				}
				$smarty->assign('companyLogin', false);
			}
        }

	    $smarty->assign('title', addslashes($smarty->get_template_vars($pTitle)));
	    $smarty->assign('id', $pDataArray['recordid']);
	    $smarty->assign('componentcode', $pDataArray['componentcode']);
	    $smarty->assign('companycode', $companyCode);
	    $smarty->assign('includeglobal', $includeGlobal);
	    $smarty->assign('controldisabled', $controlDisabled);
	    $smarty->assign('categorycode', $pDataArray['categorycode']);
	    $smarty->assign('parentid', $pDataArray['pricelinkparentid']);
	    $smarty->assign('pricingmodel', $pDataArray['pricingmodel']);
	    $smarty->assign('price', $pDataArray['price']);
	    $smarty->assign('ispricelist', $pDataArray['ispricelist']);
	    $smarty->assign('pricelistid', $pDataArray['recordid']);
	    $smarty->assign('pricelinkid', $pDataArray['pricelinkparentid']);
	    $smarty->assign('isactive', $pDataArray['isactive']);
	    $smarty->assign('quantityisdropdown', $pDataArray['quantityisdropdown']);
        $smarty->assign('defaultlanguagecode', $gConstants['defaultlanguagecode']);
        $smarty->assign('taxcode', $pDataArray['taxcode']);
		$smarty->assign('inheritparentqty', $pDataArray['inheritparentqty']);

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

        //Component Info
        LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', $pDataArray['priceinfo']);
        $smarty->assign('priceinfo', UtilsObj::encodeString($pDataArray['priceinfo'], true));

        $smarty->displayLocale('admin/componentspricing/componentspricingedit.tpl');
    }

    static function displayPriceListEntry($pID, $pCategoryCompanyCode, $pCode, $pName, $pPricingModel, $pDecimalPlaces, $pPrice, $pQuantityIsDropDown, $pTaxCode, $pIsActive)
    {
       	global $gSession;
       	global $gConstants;

       	$smarty = SmartyObj::newSmarty('ComponentsPricing');
       	$smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));

       	$companyCode = '';
        $includeGlobal = false;
        $controlDisabled = true;

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
				$smarty->assign('companyLogin', false);
			}
        }

       	$smarty->assign('ID', $pID);
        $smarty->assign('company', $companyCode);
        $smarty->assign('includeglobal', $includeGlobal);
	    $smarty->assign('controldisabled', $controlDisabled);
        $smarty->assign('code', $pCode);
        $smarty->assign('name', UtilsObj::encodeString($pName,true));
        $smarty->assign('pricingModel', $pPricingModel);
        $smarty->assign('price', $pPrice);
        $smarty->assign('quantityIsDropDown', $pQuantityIsDropDown);
        $smarty->assign('isActive', $pIsActive);
        $smarty->assign('decimalplaces', $pDecimalPlaces);
        $smarty->assign('taxcode', $pTaxCode);

        $smarty->displayLocale('admin/componentspricing/componentspricelistedit.tpl');
    }

	static function displayAdd($pDataArray, $pError = '', $pErrorInfo = '')
	{
        self::displayEntry('str_TitleComponentPricing', $pDataArray, 'str_ButtonAdd', $pError, $pErrorInfo);
    }

    static function displayAddPriceList()
	{
        $pricingModel = $_GET['pricingmodel'];
        $categoryCompanyCode = $_GET['companycode'];
        $decimalPlaces = $_GET['decimalplaces'];

        self::displayPriceListEntry(0, $categoryCompanyCode, '', '', $pricingModel, $decimalPlaces,  '', 0, '', 0);
    }

    static function displayEdit($pDataArray, $pError = '', $pErrorInfo = '')
	{
	   self::displayEntry('str_TitleEditPricing', $pDataArray, 'str_ButtonUpdate', $pError, $pErrorInfo);
    }

    static function displayPriceListEdit($pResultArray)
	{
        self::displayPriceListEntry($pResultArray['id'], $pResultArray['companycode'], $pResultArray['pricelistlocalcode'], $pResultArray['pricelistname'], $pResultArray['pricingmodel'],  $pResultArray['decimalplaces'], $pResultArray['price'], $pResultArray['quantityisdropdown'], $pResultArray['taxcode'], $pResultArray['active']);
    }

    static function getLicenseKeyFromCompany($pResultArray)
    {
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

    static function componentPriceSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('ComponentPricing');

    	if ($pResultArray['result'] != '')
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
            $msg = str_replace('^0', $pResultArray['resultparam'], $msg);
			$title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			echo '{"success":true}';
        }
    }

    static function componentPriceListSave($pResultArray)
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

    static function priceListDelete($pResultArray)
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

	static function defaultPriceDelete($pResultArray)
	{
		$deleteList = implode(',',$pResultArray['pricelinkids']);

        $smarty = SmartyObj::newSmarty('ComponentDefaultPrices');

        if ($pResultArray['alldeleted'] == 0)
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');
			$alldeleted = '0';
        }
        else
        {
			$msg = $smarty->get_config_vars('str_MessageDefaultPriceDeleted');
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$alldeleted = '1';
        }

		echo '{"success":true, "title":"' . $title . '", "msg":"' . $msg . '", "alldeleted":"' . $alldeleted . '", "idlist":"' . $deleteList . '"}';

	}

    static function componentPricingActivate($pComponetPrices)
    {
        $itemCount = count($pComponetPrices);

        $resultData = '{"success":true, "data":[';

        for ($i = 0; $i < $itemCount; $i++)
        {
			$type = $pComponetPrices[$i];
			$resultData .= '{"id":' . $type['recordid'] . ',"status":"' . $type['isactive'] . '"}';

        	if ($i != $itemCount - 1)
        	{
        		$resultData .= ",";
        	}
        }

        $resultData .= ']}';
        echo $resultData;
	}

	static function activatePriceList($pPriceLists)
    {
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

}

?>
