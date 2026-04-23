<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');

class AdminProducts_view
{
	static function displayGrid()
	{
		global $gConstants;
        global $gSession;

        $smarty = SmartyObj::newSmarty('AdminProducts');

		$smarty->assign('optiondesol', ($gConstants['optiondesol'] ? true : false));
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
		$smarty->assign('optiontdpv', ($gConstants['optiontdpv'] ? true : false));

		$threeDPreviewAvailable = false;

		// show 3d preview options only if 3d preview is enabled on the licensekey, online is available and for global administrators
		if (($gConstants['optiontdpv']) && ($gConstants['optiondesol']) && ($gSession['userdata']['usertype'] == TPX_LOGIN_SYSTEM_ADMIN))
		{
			$threeDPreviewAvailable = true;
		}

		$smarty->assign('threedpreviewavailable', $threeDPreviewAvailable);

        if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
        {
        	$smarty->assign('companyLogin', true);
        	$smarty->assign('companycode', $gSession['userdata']['companycode']);
        }
        else
        {
        	$smarty->assign('companyLogin', false);
        }
        $smarty->displayLocale('admin/products/productsgrid.tpl');
	}

	static function getGridData($pResultArray)
	{
		global $gConstants;
		global $gSession;

		$smarty = SmartyObj::newSmarty('AdminProducts');

		echo '[';

		$itemCount = count($pResultArray['products']);

		echo '[' . $pResultArray['total'] . '],';

		for ($i = 0; $i < $itemCount; $i++)
		{
			$item = $pResultArray['products'][$i];

			$categoryName = '';

            $arrProductionCollections = explode(chr(10), $item['productcollection'] ?? '');

            $productcollection = '';

            if (count($arrProductionCollections)>0)
            {
                $productcollection = '<table class=\"adminTableEntryBorder text\">';

                foreach ($arrProductionCollections as $prodCollection)
                {

                    $productcollection .= '<tr valign=\"top\" style=\"color:black\"><td align=\"left\">';
                    $productcollection .= LocalizationObj::getLocaleString($prodCollection, $gSession['browserlanguagecode'], true);
                    $productcollection .= '</td></tr>';
                }

                $productcollection .= '</table>';
            }

			$name = LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $item['name'], 'black');
			echo "['" . $item['id'] . "',";

			if ($gConstants['optionms'])
			{
				echo "'" . $item['companycode'] . "',";
			}

			echo "'" . UtilsObj::encodeString($categoryName, true) . "',";
			echo "'" . $item['code'] . "',";
			echo "'" . UtilsObj::encodeString($name, true) . "',";
			echo "'" . UtilsObj::encodeString($productcollection, true) . "',";

			if ($gConstants['optionms'])
			{
				echo "'" . $item['active'] . "',";
				echo "'" . $item['companycode'] . "',";
				echo "'" . $item['hasprice'] . "',";
			}
			else
			{
				echo "'" . $item['active'] . "',";
				echo "'" . $item['hasprice'] . "',";
			}

			if ($gConstants['optiontdpv'])
			{
				echo  "'" . $item['collectiontype'] . "',";
				echo  "'" . $item['3dmodelcode'] . "'";
			}
			echo ']';

			if ($i != $itemCount - 1)
			{
				echo ",";
			}
		}
		echo ']';
	}

    static function displayEntry($pTitle, $pResultArray, $pActionButtonName, $pError = '')
    {
        global $gSession;
		global $gConstants;
		global $ac_config;

        $productArray = $pResultArray['product'];

        $categoryName = "";
        $name = LocalizationObj::getLocaleString($productArray['name'], $gSession['browserlanguagecode'], true);

       	$smarty = SmartyObj::newSmarty('AdminProducts');
        $smarty->assign('title', $smarty->get_config_vars($pTitle));
        $smarty->assign('productid', $productArray['recordid']);
        $smarty->assign('code', $productArray['code']);

        if ($productArray['companycode'] == '')
        {
        	$productArray['companycode'] = 'GLOBAL';

        }
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->assign('companycode', $productArray['companycode']);
        $smarty->assign('category', UtilsObj::encodeString($categoryName, false));
        $smarty->assign('name', UtilsObj::encodeString($name, false));
        $smarty->assign('cost', $productArray['unitcost']);
        $smarty->assign('skucode', $productArray['skucode']);
        $smarty->assign('weight', $productArray['weight']);
        $smarty->assign('jobticket1name', UtilsObj::encodeString($productArray['jobticketfield1name'], true));
        $smarty->assign('jobticket1value',UtilsObj::encodeString($productArray['jobticketfield1value'],true));
        $smarty->assign('jobticket2name', UtilsObj::encodeString($productArray['jobticketfield2name'], true));
        $smarty->assign('jobticket2value', UtilsObj::encodeString($productArray['jobticketfield2value'], true));
        $smarty->assign('jobticket3name', UtilsObj::encodeString($productArray['jobticketfield3name'], true));
        $smarty->assign('jobticket3value', UtilsObj::encodeString($productArray['jobticketfield3value'], true));
        $smarty->assign('jobticket4name', UtilsObj::encodeString($productArray['jobticketfield4name'], true));
        $smarty->assign('jobticket4value', UtilsObj::encodeString($productArray['jobticketfield4value'], true));
        $smarty->assign('jobticket5name', UtilsObj::encodeString($productArray['jobticketfield5name'], true));
        $smarty->assign('jobticket5value', UtilsObj::encodeString($productArray['jobticketfield5value'], true));

        $smarty->assign('previewtype', $productArray['previewtype']);
        $smarty->assign('previewcovertype', $productArray['previewcovertype']);
        $smarty->assign('previewautoflip', $productArray['previewautoflip']);
        $smarty->assign('previewthumbnails', $productArray['previewthumbnails']);
        $smarty->assign('previewthumbnailsview', $productArray['previewthumbnailsview']);

        // image scaling settings
        $smarty->assign('usedefaultimagescalingbefore', $productArray['usedefaultimagescalingbefore']);
        $smarty->assign('imagescalingbeforeenabled', $productArray['imagescalingbeforeenabled']);
        $smarty->assign('imagescalingbefore', $productArray['imagescalingbefore']);

        $smarty->assign('createnewprojectschecked', $productArray['cancreatenewprojects']);
        $smarty->assign('activechecked', $productArray['isactive']);

        $taxLevelsArray = Array(
        	Array('id' => 1, 'name' => $smarty->get_config_vars('str_LabelTaxLevel1')),
        	Array('id' => 2, 'name' => $smarty->get_config_vars('str_LabelTaxLevel2')),
        	Array('id' => 3, 'name' => $smarty->get_config_vars('str_LabelTaxLevel3')),
        	Array('id' => 4, 'name' => $smarty->get_config_vars('str_LabelTaxLevel4')),
        	Array('id' => 5, 'name' => $smarty->get_config_vars('str_LabelTaxLevel5'))

        );

        $smarty->assign('taxlevellist', $taxLevelsArray);
        $smarty->assign('taxlevel', $productArray['taxlevel']);
        $smarty->assign('producttype', $pResultArray['producttype']);
        $smarty->assign('productoptions', $productArray['productoptions']);
		$smarty->assign('pricetransformationstage', $productArray['pricetransformationstage']);
		$smarty->assign('minimumprintsperproject', $productArray['minimumprintsperproject']);

		$productPreview = UtilsObj::getAssetRequest($productArray['code'], 'products');

		if ($productPreview !== '')
		{
			$smarty->assign('productpreview', $productPreview);
		}
		else
		{
			$smarty->assign('productpreview', UtilsObj::correctPath($gSession['webbrandwebroot']) . 'images/admin/nopreview.gif');
		}

		// recommended image sizes
		$sizes = DatabaseObj::getRecommendedImageSizes('products');
		$previewImageText = str_replace(array('^rw', '^rh'), array($sizes['width'], $sizes['height']), $smarty->get_config_vars('str_LabelSelectPreviewImage'));
		$smarty->assign('previewImageText', $previewImageText);

		// Used for deciding whether to show designer settings.
		if ($gConstants['optiondesol'])
		{
			$smarty->assign('hasonlinedesigner' , 1);
		}
		else
		{
			$smarty->assign('hasonlinedesigner' , 0);
		}

        // Average pictures per page.
        $picturesPerPageValues = array(
            array(-1, $smarty->get_config_vars('str_LabelDefault')),
			array(0, $smarty->get_config_vars('str_LabelOff')),
			array(1, 1),
            array(2, 2),
            array(3, 3),
            array(4, 4),
            array(5, 5),
            array(6, 6),
            array(7, 7),
            array(8, 8),
            array(9, 9),
            array(10, 10),
        );
        $smarty->assign('usedefaultaveragepicturesperpage', $productArray['usedefaultaveragepicturesperpage']);
        $smarty->assign('averagepicturesperpage', $productArray['averagepicturesperpage']);
        $smarty->assign('picturesperpagevalues', json_encode($picturesPerPageValues));
		$smarty->assign('allowretroprints', UtilsObj::getArrayParam($ac_config, "ALLOWRETROPRINTS", 0) == 1);
		$smarty->assign('retroprints', $productArray['retroprints']);

		$smarty->assign('fontlists', json_encode($pResultArray['fontlists'] ?? []));
		$smarty->assign('selectedfontlist', $pResultArray['fontlistselected']);

		// used to decide whether or not to show image scaling options at product level
		$smarty->assign('allowimagescalingbefore',  UtilsObj::getArrayParam($ac_config, "ALLOWIMAGESCALINGBEFORE", 0) == 1);

        $smarty->displayLocale('admin/products/productedit.tpl');
    }

    static function productSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('AdminProducts');

    	if ($pResultArray['result'] != '')
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			echo '{"success":true,"data":{"id":' . $pResultArray['id'] . ',"active":"' . $pResultArray['isactive'] . '"}}';
        }
    }

    static function productConfigSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('AdminProducts');

    	if ($pResultArray['result'] != '')
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			echo '{"success":true}';
        }
    }

    static function productActivate($pProducts)
    {
        $resultData = '{"success":true, "data":[';

        foreach ($pProducts as $product)
        {
			$resultData .= '{"id":' . $product['recordid'] . ',"active":"' . $product['isactive'] . '"},';
        }

        $resultData .= ']}';

        echo $resultData;
	}

	static function productDelete($pResultArray)
    {
    	$deleteList = implode(',',$pResultArray['productids']);
        $smarty = SmartyObj::newSmarty('AdminProducts');

        if ($pResultArray['alldeleted'] == 0)
        {
			$msg = $smarty->get_config_vars($pResultArray['result']);
			$title = $smarty->get_config_vars('str_TitleWarning');
			$alldeleted = '0';
        }
        else
        {
			$msg = $smarty->get_config_vars('str_MessageProductDeleted');
			$title = $smarty->get_config_vars('str_TitleConfirmation');
			$alldeleted = '1';
        }

		echo '{"success":true, "title":"' . $title . '", "msg":"' . $msg . '", "alldeleted":"' . $alldeleted . '", "idlist":"' . $deleteList . '"}';
	}

    static function displayEdit($pResultArray)
	{
	   self::displayEntry('str_TitleEditProduct', $pResultArray, 'str_ButtonUpdate');
    }

    static function productConfigDisplay($pDataArray)
	{
	 	global $gSession;
	 	global $gConstants;

	   	$smarty = SmartyObj::newSmarty('AdminProducts');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->assign('companyLogin', false);

		if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
        {
        	$smarty->assign('companyLogin', true);
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
        	$smarty->assign('companycode', $pDataArray['product']['companycode']);
        }

		LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', '');
        $smarty->assign('localizedinfomaxchars', 30);
        $smarty->assign('localizedinfowidth', 200);
        $smarty->assign('localizedinfocodesvar', 'gLocalizedCodesArray');
        $smarty->assign('localizednamelabel', $smarty->get_config_vars('str_LabelName'));

		$productName = LocalizationObj::getLocaleString($pDataArray['product']['name'], $gSession['browserlanguagecode'], true);

		SmartyObj::replaceParams($smarty, 'str_TitleProductPricing', $pDataArray['product']['code'] . ' - ' . UtilsObj::encodeString($productName,true));
		$smarty->assign('title', $pDataArray['product']['code'] . ' - ' .  UtilsObj::encodeString($productName, true));
		$smarty->assign('productcode', $pDataArray['product']['code']);
		$smarty->assign('canlink', $pDataArray['canlink']);

		if ($pDataArray['linkedproduct']['code'] == '')
		{
			$smarty->assign('linkedproductcode', $smarty->get_config_vars('str_LabelNoProductLink'));
		}
		else
		{
			$smarty->assign('linkedproductcode', $pDataArray['linkedproduct']['code']);
		}

		$smarty->assign('linkedproductid', $pDataArray['linkedproduct']['id']);

		$treeData = self::getProductTree($pDataArray['tree']);

		$smarty->assign('productcode', $pDataArray['product']['code']);
		$smarty->assign('productid', $pDataArray['product']['recordid']);
	    $smarty->assign('tree', $treeData['tree']);
        $smarty->assign('defaultlanguagecode', $gConstants['defaultlanguagecode']);

	    $smarty->displayLocale('admin/products/productsconfig.tpl');
    }

    static function refreshProductTree($pDataArray)
    {
		global $gSession;

	   	$smarty = SmartyObj::newSmarty('AdminProducts');

	   	$productName = LocalizationObj::getLocaleString($pDataArray['product']['name'], $gSession['browserlanguagecode'], true);
		SmartyObj::replaceParams($smarty, 'str_TitleProductPricing', $pDataArray['product']['code'] . ' - ' . UtilsObj::encodeString($productName,true));

		$title = $pDataArray['product']['code'] . ' - ' . $productName;
	   	$treeData = self::getProductTree($pDataArray['tree']);

	   	$returnString = '';
	   	$treeLength = strlen($treeData['tree']);
	   	$returnString = $treeLength.' '.$treeData['tree'];

	   	echo $returnString;
    }

    static function buildProductTree(&$pDataArray, $pStartIndex, $pDepth, $pParentPath, $pTreeString, &$pNextNodeID, &$pProcessedItemsArray, $pSectionCode)
    {
    	$treeString = $pTreeString;
    	$hasSection = false;
    	$componentChildrenOpen = false;
    	$componentHadChildrenOpen = false;
    	$itemCount = count($pDataArray);
		$previousParentPath = '';
		$previousSectionCode = $pSectionCode;

    	for ($i = $pStartIndex; $i < $itemCount; $i ++)
    	{
    		$includeDefaultClass = false;
    		$theItem = &$pDataArray[$i];

    		if (! in_array($theItem['parentid'], $pProcessedItemsArray))
    		{
	    		$parentPathDepth = substr_count($theItem['parentpath'], '\\');
	    		if ($parentPathDepth == $pDepth)
		    	{
		    		$theItem['depth'] = $pDepth; // set the depth incase we need it later

		    		if ($previousParentPath != $theItem['parentpath'])
		    		{
		    			// new section
			 			if ($previousSectionCode != $theItem['sectioncode'])
			 			{
							$pNextNodeID++;

							if ($hasSection)
							{
								$treeString = substr($treeString, 0, -1);
								$treeString .= ']},';
							}

							$treeString .= '{';
							$treeString .= '"id": '. $pNextNodeID . ',';
							$treeString .= '"text": "'.$theItem['sectioncode'].'" ,';
							$treeString .= '"sectioncode": "'.$theItem['sectioncode'].'" ,';
							$treeString .= '"componentcode": "" ,';
							$treeString .= '"parentpath": "'.UtilsObj::encodeString($theItem['parentpath'],true).'",';
							$treeString .= '"pathdepth": '. $theItem['depth'].',';
							$treeString .= '"sectionname": "'. UtilsObj::encodeString($theItem['name'], true) . '" ,';
							$treeString .= '"hasprice": true ,';
							$treeString .= '"issection": true ,';
							$treeString .= '"islist": '.$theItem['islist'] .' , ';
							$treeString .= '"leaf": false ,';
							$treeString .= '"expanded": true,';
							$treeString .= '"iconCls": "silk-chart-organisation" ,';
							$treeString .= '"allowinherit": 0,';

							if ($theItem['categoryactive'] == 0)
							{
								$treeString .= '"cls": "list-component-inactive",';
							}

							$treeString .= '"children": [';
							$hasSection = true;
							$componentHadChildrenOpen = false;
			 			}

			 			$pProcessedItemsArray[] = $theItem['parentid'];
		    		}

		    		// process component
		    		if ($theItem['parentid'] > 0)
		    		{
		    			$pNextNodeID++;

		    			//check to see if the item is a list or checkbox component
		    			if ($theItem['islist'] == '1')
			    		{
			    			$iconCls = 'silk-list';

			    			if ($theItem['isdefault'] == '1')
			    			{
			    				$includeDefaultClass = true;
			    				$text = UtilsObj::encodeString($theItem['localcode'] . ' - ' . LocalizationObj::getLocaleString($theItem['name'], '', true), true);
			    			}
			    			else
			    			{
			    				$text = UtilsObj::encodeString($theItem['localcode'] . ' - ' . LocalizationObj::getLocaleString($theItem['name'], '', true), true);
			    			}
			    		}
			    		else
			    		{
			    			if ($theItem['isdefault'] == '1')
			    			{
			    				$iconCls = 'checkboxComponentChecked';
			    			}
			    			else
			    			{
			    				$iconCls = 'checkboxComponentUnchecked';
			    			}

			    			$text = UtilsObj::encodeString($theItem['localcode'] . ' - ' . LocalizationObj::getLocaleString($theItem['name'], '', true), true);
			    		}

			 			$treeString .= '{';
			 			$treeString .= '"id": ' . $pNextNodeID . ',';
			 			$treeString .= '"parentid": ' . $theItem['parentid'] . ',';
			 			$treeString .= '"text": "' . $text . '",';
			 			$treeString .= '"sectioncode": "' . $theItem['sectioncode'] . '", ';
			 			$treeString .= '"companycode": "' . $theItem['companycode'] . '", ';
			 			$treeString .= '"componentcode": "' . $theItem['localcode'] . '", ';
			 			$treeString .= '"code": "' . $theItem['code'] . '", ';
			 			$treeString .= '"hasprice": true,';
			 			$treeString .= '"issection": false,';
			 			$treeString .= '"islist": ' . $theItem['islist'] . ', ';
			 			$treeString .= '"decimalplaces": ' . $theItem['decimalplaces'] . ', ';
			 			$treeString .= '"isdefault": ' . $theItem['isdefault'] . ', ';
			 			$treeString .= '"leaf": false,';
						$treeString .= '"pathdepth": '. $theItem['depth'].',';
			 			$treeString .= '"expanded": true,';
			 			$treeString .= '"iconCls": "' . $iconCls . '",';
			 			$treeString .= '"pricingmodel": ' . $theItem['pricingmodel'] . ',';
			 			$treeString .= '"categorycode": "' . $theItem['categorycode'] . '",';
			 			$treeString .= '"pricelinkparentids": "' . $theItem['pricelinkids'] . '",';
			 			$treeString .= '"initialized": 0,';
			 			$treeString .= '"removed": false,';
						$treeString .= '"parentpath": "' . UtilsObj::encodeString($theItem['parentpath'], true) . '",';
						$treeString .= '"pathdepth": ' . $theItem['depth'] . ',';
						$treeString .= '"inheritparentqty": ' . $theItem['inheritparentqty'] . ',';
						$treeString .= '"allowinherit": ' . $theItem['allowinherit'] . ',';

			 			if ($includeDefaultClass)
			 			{
			 				$treeString .= '"cls": "default-list-component-bold",';
			 			}

			 			if ($theItem['categoryactive'] == 0)
						{
							$treeString .= '"cls": "list-component-inactive",';
						}
						else if ($theItem['componentactive'] == 0)
						{
							$treeString .= '"cls": "list-component-inactive",';
						}

						// if we have detected a missing dummy record for a component then
						// attach the class to highlight it to the user
						if (strrpos($theItem['pricelinkids'], '-1') !== false)
						{
							$treeString .= '"cls": "corrupted-price-link-record",';
						}

			 			$treeString .= '"children": [';

			 			// is the component a list
			 			if ($theItem['islist'] == '1')
			 			{
			 				// it is a list so we can have sub-components
			 				$componentChildrenOpen = true;

			 				// this is a new open node so we did not previously have an open node
			 				$componentHadChildrenOpen = false;
			 			}
			 			else
			 			{
			 				// it is a checkbox so we cannot have sub-components
			 				$treeString .= ']},';
			 				$componentChildrenOpen = false;

			 				// do not allow the node we have closed to be re-opened
			 				$componentHadChildrenOpen = false;
			 			}
				    }

		    		$pProcessedItemsArray[] = $theItem['parentid'];

		    		$previousParentPath = $theItem['parentpath'];
		    		$previousSectionCode = $theItem['sectioncode'];
		    	}
		    	else
		    	{
		    		$theItem['depth'] = $parentPathDepth; // set the depth incase we need it later

		    		// are we now deeper in the tree
		    		if ($parentPathDepth == $pDepth + 1)
		    		{
		    			$previousParentPathLen = strlen($previousParentPath);
		    			$startOfNewItemPath = substr($theItem['parentpath'], 0, $previousParentPathLen);

		    			//check to see if the start of the new path matches the previous path. if it does then this is a sub component
		    			if ($startOfNewItemPath == $previousParentPath)
						{
							// do we have an open child node
							if (! $componentChildrenOpen)
							{
								// we don't have an open child node but did we previously have one open that has been closed
								if ($componentHadChildrenOpen)
								{
									// we had one open that was closed so re-open it
									$treeString = substr($treeString, 0, -3);
									$componentHadChildrenOpen = false;
									$componentChildrenOpen = true;
								}
							}

							// its a sub component
							$treeString = self::buildProductTree($pDataArray, $i, $parentPathDepth, $theItem['parentpath'], $treeString, $pNextNodeID, $pProcessedItemsArray, $previousSectionCode);

							$pProcessedItemsArray[] = $theItem['parentid'];

							if ($componentChildrenOpen)
							{
								// remove any trailing commas
								if (substr($treeString, -1) == ',')
								{
									$treeString = substr($treeString, 0, -1);
								}

								$treeString .= ']},';
								$componentChildrenOpen = false;
								$componentHadChildrenOpen = true;
							}
						}
		    		}
		    		elseif ($parentPathDepth < $pDepth)
		    		{
		    			// we are not deeper so just exit the loop
		    			break;
		    		}
		    	}
    		}
     	}

    	if ($componentChildrenOpen)
		{
			$treeString .= ']},';
			$componentChildrenOpen = false;
			$componentHadChildrenOpen = true;
		}

    	if ($hasSection)
    	{
    		$treeString = substr($treeString, 0, -1);
    		$treeString .= ']},';
    	}

		// if we are leaving for the last time remove any trailing commas
		if (($pDepth == 0) && (substr($treeString, -1) == ','))
		{
			$treeString = substr($treeString, 0, -1);
		}

		return $treeString;
    }

    static function getProductTree($pDataArray)
	{
		$resultArray = Array();
	 	$priceLinkParentIdList = Array();
	 	$recordArray = Array();
	 	$tree = '';
	 	$lastParentID = -1;
	 	$javaScriptArray = '';

		 if (count($pDataArray) > 0)
	 	{
		 	// pre processing to sort the pricelinks
		 	$sortedComponentList = Array();

		 	for ($i = 0; $i < count($pDataArray); $i++)
		 	{
		 		$priceLinkParentIdList = Array();

		 		$item = $pDataArray[$i];
		 		$sortedComponentList[] = $item;

		 		$lastParentID = $item['parentid'];

				if (! in_array($item['parentid'], $priceLinkParentIdList))
		 		{
		 			$priceLinkParentIdList[] = $item['parentid'];
		 		}

				// get path depth, to check if the item is a sub component
				$itemDepth = substr_count($item['parentpath'], '\\');

				$recordArray[$i]['parentid'] = $item['parentid'];
				$recordArray[$i]['companycode'] = $item['companycode'];
				$recordArray[$i]['productcode'] = $item['productcode'];
			    $recordArray[$i]['sectioncode'] = $item['sectioncode'];
			    $recordArray[$i]['sortorder'] = $item['sortorder'];
				$recordArray[$i]['parentpath'] = $item['parentpath'];
				$recordArray[$i]['code'] = $item['code'];
				$recordArray[$i]['localcode'] = $item['localcode'];
				$recordArray[$i]['name'] = $item['name'];
				$recordArray[$i]['pricingmodel'] = $item['pricingmodel'];
				$recordArray[$i]['categorycode'] = $item['categorycode'];
				$recordArray[$i]['islist'] = $item['islist'];
				$recordArray[$i]['isdefault'] = $item['isdefault'];
				$recordArray[$i]['decimalplaces'] = $item['decimalplaces'];
				$recordArray[$i]['categoryactive'] = $item['categoryactive'];
				$recordArray[$i]['componentactive'] = $item['componentactive'];
				$recordArray[$i]['depth'] = $itemDepth;
				$recordArray[$i]['inheritparentqty'] = $item['inheritparentqty'];
				$recordArray[$i]['allowinherit'] = 0;

				/*
				 * Only allow inheritparentqty if the item is
				 * 1, not a single print option or calendar customisation option,
				 * 2, sub component list item with depth >= 3
				 * 3, a check box item with depth >= 2
				 */
				if (($item['sectioncode'] != 'SINGLEPRINTOPTION') && ($item['sectioncode'] != 'CALENDARCUSTOMISATION'))
				{
					if ((($item['islist'] === 1) && ($itemDepth >= 3)) ||
						(($item['islist'] === 0) && ($itemDepth >= 2)))
					{
						// only inherit if the pricing model includes component qty
						if ((TPX_PRICINGMODEL_PERPRODCMPQTY === $item['pricingmodel']) || (TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY === $item['pricingmodel']))
						{
							$recordArray[$i]['allowinherit'] = 1;
						}
					}
				}

		 		$i2 = $i;

				$dummyPriceLinkIDRequired = true;

		 		// find all entries that match and append them in order to the sorted list
		 		while ($i2 < count($pDataArray))
		 		{
		 			$item2 = $pDataArray[$i2];

		 			// compare the two entries
		 			if (($item['parentpath'] == $item2['parentpath']) && ($item['localcode'] == $item2['localcode']))
		 			{
						// if we know the item has a pricelink record with a priceid of -1
						// then we know a dummy record has been created for this componet
						if ($item2['priceid'] == -1)
						{
							$dummyPriceLinkIDRequired = false;
						}

						// the entries match so append this one to the sorted list
		 				$sortedComponentList[] = $item2;

		 				if (! in_array($item2['parentid'], $priceLinkParentIdList))
				 		{
				 			$priceLinkParentIdList[] = $item2['parentid'];
				 		}

						$lastParentID = $item2['parentid'];

						// remove this item from the original list so that it is not processed again
		 				if ($i2 != $i)
		 				{
		 					array_splice($pDataArray, $i2, 1);
		 				}
		 				else
		 				{
		 					$i2++;
		 				}
		 			}
		 			else
		 			{
		 				// the item is different so increase the counter
		 				$i2++;
		 			}
		 		}

				 // we have detected that the componenet does not have a dummy record
				 // append a -1 pricelink id to the node so the javascript knows that we need to
				 // create a dummy priclink record to be sent to the server on the tree save
				 if ($dummyPriceLinkIDRequired)
				 {
					$priceLinkParentIdList[] = -1;
				 }

		 		$recordArray[$i]['pricelinkids'] = implode(',', $priceLinkParentIdList);
		 	}

		 	$existingItemsArray = Array();
		 	$dummyParentID = 0;

		 	$productCode = $recordArray[0]['productcode'];

		 	// loop round each item in the data array so that we can insert dummy items into the right position in the array so that we have the full structure of the tree
		 	for ($i = 0; $i < count($recordArray); $i++)
		 	{
		 		if (! in_array($recordArray[$i]['parentid'], $existingItemsArray))
		 		{
			 		$existingItemsArray[] = $recordArray[$i]['parentid'];
			 		$searchForPath = $recordArray[$i]['parentpath'] . $recordArray[$i]['localcode'] . '\\';

			 		for ($j = $i; $j < count($recordArray); $j++)
			 		{
			 			if ($searchForPath != $recordArray[$j]['parentpath'])
			 			{
			 				$dummyItem  = array('parentid' => --$dummyParentID, 'companycode' => $recordArray[$i]['companycode'], 'productcode' => $recordArray[$i]['productcode'],
			 									'sectioncode' => $recordArray[$i]['sectioncode'], 'sortorder' => $recordArray[$i]['sortorder'],
			 									'parentpath' => $searchForPath, 'code' => $recordArray[$i]['code'], 'localcode' => $recordArray[$i]['localcode'] ,
			 									'name' => $recordArray[$i]['name'], 'pricingmodel' => $recordArray[$i]['pricingmodel'],
			 									'categorycode' => $recordArray[$i]['categorycode'], 'islist' => $recordArray[$i]['islist'],
			 									'isdefault' => $recordArray[$i]['isdefault'], 'pricelinkids' => $recordArray[$i]['pricelinkids'],
												'categoryactive' => $recordArray[$i]['categoryactive'], 'componentactive' => $recordArray[$i]['componentactive'],
												'inheritparentqty' => $recordArray[$i]['inheritparentqty'], 'allowinherit' => $recordArray[$i]['allowinherit']);

			 				$existingItemsArray[] = $dummyParentID;
			 				array_splice($recordArray, $j + 1, 0, array($dummyItem));
			 				break;
			 			}
			 		}
		 		}
		 	}

		 	$existingItemsArray = Array();
		 	$sortedRecordArray = Array();
		 	$itemCount = count($recordArray);

		 	// loop round the new record array so that we can sort the array into the correct order
		 	for ($i = 0; $i < count($recordArray); $i++)
		 	{
		 		if (! in_array($recordArray[$i]['parentid'], $existingItemsArray))
		 		{
		 			$sortedRecordArray[] = $recordArray[$i];
			 		$existingItemsArray[] = $recordArray[$i]['parentid'];

			 		$searchForPath = $recordArray[$i]['parentpath'] . $recordArray[$i]['localcode'] . '\\';

			 		for ($j = 0; $j < count($recordArray); $j++)
			 		{
			 			if (substr($recordArray[$j]['parentpath'], 0, strlen($searchForPath)) == $searchForPath)
			 			{
			 				if (! in_array($recordArray[$j]['parentid'], $existingItemsArray))
			 				{
			 					$sortedRecordArray[] = $recordArray[$j];
			 					$existingItemsArray[] = $recordArray[$j]['parentid'];
			 				}
			 			}
			 		}
		 		}
			}
			$recordArray = $sortedRecordArray;

			// start building the tree list
		 	$nextNodeID = 1;
		 	$processedItemsArray = Array();

			// add LINEFOOTER and ORDERFOOTER sections to the array as default if there are any sections starting with $LINEFOOTER or $ORDERFOOTER
			$dummmyLineFooterArray = array('id' => '998', 'parentid'=> --$dummyParentID, 'companycode' => '', 'productcode'  => $productCode, 'groupcode' => '', 'sortorder' => '998', 'sectioncode'  => 'LINEFOOTER', 'parentpath' => '$LINEFOOTER\\', 'code' => 'LINEFOOTER', 'localcode' => 'LINEFOOTER', 'name'=> '',
						'pricingmodel' => 0, 'categorycode' => '', 'islist' => 1, 'isdefault' => 0, 'priclinkids' => '' , 'categoryactive' => 1);
			$dummmyOrderFooterArray = array('id' => '999','parentid'=> --$dummyParentID, 'companycode' => '', 'productcode'  => $productCode, 'groupcode' => '', 'sortorder' => '999', 'sectioncode'  => 'ORDERFOOTER', 'parentpath' => '$ORDERFOOTER\\', 'code' => 'ORDERFOOTER', 'localcode' => 'ORDERFOOTER', 'name'=> '',
						'pricingmodel' => 0, 'categorycode' => '', 'islist' => 1, 'isdefault' => 0, 'priclinkids' => '', 'categoryactive' => 1);

			$existingItemsArray = Array();

			// add LINEFOOTER and ORDERFOOTER in the correct order in the recordArray
			for ($i = 0; $i < count($recordArray); $i++)
		 	{
		 		$theItem = $recordArray[$i];

		 		if (substr($theItem['parentpath'], 0, 12) == '$LINEFOOTER\\')
		 		{
		 			if (! in_array($dummmyLineFooterArray['id'], $existingItemsArray))
		 			{
		 				array_splice($recordArray, $i, 0, array($dummmyLineFooterArray));
		 				$existingItemsArray[] = $dummmyLineFooterArray['id'];
		 			}
		 		}
		 		elseif (substr($theItem['parentpath'], 0, 13) == '$ORDERFOOTER\\')
		 		{
		 			if (! in_array($dummmyOrderFooterArray['id'], $existingItemsArray))
		 			{
	 					array_splice($recordArray, $i, 0, array($dummmyOrderFooterArray));
		 				$existingItemsArray[] = $dummmyOrderFooterArray['id'];
		 			}
		 		}
		 	}

		 	$tree = self::buildProductTree($recordArray, 0, 0, '', $tree, $nextNodeID, $processedItemsArray, '');
		 	$tree = '[' . $tree . ']';

	 	}
	 	else
	 	{
	 		$tree = '{}';
	 	}

	 	$resultArray['tree'] = $tree;
		$resultArray['javascriptarray'] = $javaScriptArray;

		return $resultArray;
    }

    static function getComponentsFromCategory($pDataArray)
    {
    	global $gSession;

    	$itemCount = count($pDataArray);

    	$tree = '[';

    	for ($i = 0; $i < $itemCount - 1; $i++)
    	{

    		$theItem = $pDataArray[$i];

    		if ($pDataArray['selection'] == 'SECTIONS')
    		{

    			if ($theItem['categoryactive'] == 1)
				{
					$cls = 'hide-expand-collapse';
					$active = 1;
				}
				else
				{
					$cls = 'hide-expand-collapse-inactive';
					$active = 0;
				}

    			$tree .= '{';
	    		$tree .= 'id:'.$theItem['id'].',';
	    		$tree .= 'text: "'.$theItem['code'].' - '. LocalizationObj::getLocaleString(UtilsObj::encodeString($theItem['name'], true), '', true).'",';
	    		$tree .= "cls: '". $cls . "',";
	    		$tree .= 'sectioncode: "'.$theItem['code'].'",';
	    		$tree .= 'sectionname: "'. UtilsObj::encodeString($theItem['name'], true) . '",';
	    		$tree .= 'issection: true,';
	    		$tree .= 'componentcode: "", ';
	    		$tree .= 'leaf: false,';
	    		$tree .= 'expandable: false,';
	    		$tree .= 'children: [],';
	    		$tree .= 'active: "'. $active .'",';
	    		$tree .= 'iconCls: "silk-chart-organisation"';
	    		$tree .= '}';
    		}
    		else
    		{
    			if ($theItem['categoryactive'] == 1)
				{
					if ($theItem['componentactive'] == 1)
					{
						$cls = 'hide-expand-collapse';
						$active = 1;
					}
					else
					{
						$cls = 'hide-expand-collapse-inactive';
						$active = 0;
					}
				}
				else
				{
					$cls = 'hide-expand-collapse-inactive';
					$active = 0;
				}


    			if ($theItem['islist'] == '1')
	    		{
	    			$iconCls = 'iconCls: "silk-list"';

	    		}
	    		else
	    		{
	    			$iconCls = 'iconCls: "checkboxComponentUnchecked"';
	    		}

    			$tree .= '{';
	    		$tree .= 'id:'.$theItem['id'].',';
	    		$tree .= 'text: "'.$theItem['localcode'].' - '. LocalizationObj::getLocaleString(UtilsObj::encodeString($theItem['name'], true), '', true).'",';
	    		$tree .= "cls: '". $cls . "',";
	    		$tree .= 'sectioncode: "'.$theItem['categorycode'].'",';
	    		$tree .= 'issection: false,';
	    		$tree .= 'islist: '.$theItem['islist'].', ';
	    		$tree .= 'decimalplaces: '.$theItem['decimalplaces'].', ';
	    		$tree .= 'categorycode: "'.$theItem['categorycode'].'", ';
	    		$tree .= 'componentcode: "'.$theItem['localcode'].'", ';
	    		$tree .= 'code: "'.$theItem['code'].'", ';
	    		$tree .= 'companycode: "'.$theItem['companycode'].'", ';
	    		$tree .= 'pricingmodel: '.$theItem['pricingmodel'].', ';
	    		$tree .= 'leaf: true,';
	    		$tree .= 'active: "'.$active.'",';
	    		$tree .= $iconCls;
	    		$tree .= '}';
    		}

    		if ($i != $itemCount - 1)
			{
				$tree .= ',';
			}

    	}
		$tree = substr($tree, 0, -2);
		$tree .= '}]';

	   	echo $tree;

    }

    static function getProductsConfigPricingGridData($pDataArray)
    {
		$smarty = SmartyObj::newSmarty('AdminProductPricing');

    	$lastParentID = -1;
    	$jsonString = '({"rows": [';
    	$itemCount = count($pDataArray['pricing']);
		$pricelinkString = '';
		$groupCodeString = '';
		$jsonPriceLinkString = '';
		$jsonGroupCodeString = '';
		$itemCounter = 0;
		$priceIDArray = Array();
		$groupCodeArray = Array();

		if ($itemCount > 0)
		{
			//loop through the pricelink records so that we can build a jsonstring to update the javascript array with pricing data.
			for ($i = 0; $i < $itemCount; $i++)
			{
				$theItem = $pDataArray['pricing'][$i];

				if ($theItem['parentid'] != $lastParentID)
	 			{
					$itemCounter++;

					if ($i > 0)
					{
						$jsonString .= $jsonPriceLinkString.'",'.$jsonGroupCodeString.'"},';
					}

					if ($theItem['taxcode'] != '')
					{
						$includesTax = $theItem['taxratecode'] . ' - ' . number_format($theItem['taxrate'], 2, '.', '') . '%';
					}
					else
					{
						$includesTax = $smarty->get_config_vars('str_LabelNone');
					}

					$jsonPriceLinkString = '{';
					$jsonPriceLinkString .= '"id":' . $theItem['id'] . ', ';
					$jsonPriceLinkString .= '"parentid":' . $theItem['parentid'] . ', ';
					$jsonPriceLinkString .= '"companycode":"' . $theItem['companycode'] . '", ';
					$jsonPriceLinkString .= '"productcode":"' . $theItem['productcode'] . '", ';
					$jsonPriceLinkString .= '"ispricelist": "' . $theItem['ispricelist'] . '", ';
					$jsonPriceLinkString .= '"pricelistid": "' . $theItem['pricelistid'] . '", ';
					$jsonPriceLinkString .= '"inpricelistid": "' . $theItem['pricelistid'] . '", ';
					$jsonPriceLinkString .= '"inispricelist": "' . $theItem['ispricelist'] . '", ';
					$jsonPriceLinkString .= '"categorycode": "' . $theItem['categorycode'] . '", ';
					$jsonPriceLinkString .= '"pricingmodel": "' . $theItem['pricingmodel'] . '", ';
					$jsonPriceLinkString .= '"price": "' . $theItem['price'] . '", ';
					$jsonPriceLinkString .= '"quantityisdropdown": "' . $theItem['quantityisdropdown'] . '", ';
					$jsonPriceLinkString .= '"inheritparentqty": ' . $theItem['inheritparentqty'] . ', ';
					$jsonPriceLinkString .= '"pricedescription": "' . $theItem['pricedescription'] . '", ';
					$jsonPriceLinkString .= '"priceladditionalinfo": "' . $theItem['priceinfo'] . '", ';
					$jsonPriceLinkString .= '"active": "' . $theItem['active'] . '", ';
					$jsonPriceLinkString .= '"taxcode": "' . $theItem['taxcode'] . '", ';
					$jsonPriceLinkString .= '"includestax": "' . $includesTax . '", ';
					$jsonPriceLinkString .= '"pricelinkids": "' . $theItem['id'];

	 				$jsonGroupCodeString = '"groupcodes": "'.$theItem['groupcode'];

	 				$priceIDArray[] = $theItem['id'];
	 				$groupCodeArray[] = $theItem['groupcode'];
	 			}
	 			else
	 			{
					if (!in_array($theItem['id'], $priceIDArray))
					{
						$jsonPriceLinkString .= ',' . $theItem['id'];
					}

					if (!in_array($theItem['groupcode'], $groupCodeArray))
					{
						$jsonGroupCodeString .= ',' . $theItem['groupcode'];
					}

					$priceIDArray[] = $theItem['id'];
					$groupCodeArray[] = $theItem['groupcode'];
	 			}

				$lastParentID = $theItem['parentid'];
			}

			$jsonString .= $jsonPriceLinkString.'",'.$jsonGroupCodeString.'"}]})';
		}
		else
		{
			$jsonString = '({"rows": []})';
		}

	 	echo $jsonString;
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

	static function productLinkingList($pResultArray)
	{
		if ($pResultArray['error'] === '')
		{
			$string = '[';

			$itemArray = $pResultArray['data'];
			$itemCount = count($itemArray);
			$string .= '[' . $itemCount . ']';

			for ($i = 0; $i < $itemCount; $i++)
			{
				$item = $itemArray[$i];

				$string .= ",[" . $item['id'] . ",";
				$string .= "'" .  $item['code'] . "',";
				$string .= "'" .  $item['valid'] . "']";

			}
			$string .= ']';

			echo $string;
		}
		else
		{
			echo '{success: false, "msg":""}';
		}
	}

	static function getLinkedProductCode($pResultArray)
	{
		$smarty = SmartyObj::newSmarty('AdminProducts');

		if ($pResultArray['error'] === '')
		{
			if ($pResultArray['data']['code'] != '')
			{
				echo '{"success":true, "msg":"' . $pResultArray['data']['code'] . '"}';
			}
			else
			{
				echo '{"success":true, "msg":"' . $smarty->get_config_vars('str_LabelNoProductLink') . '"}';
			}
		}
		else
		{
			echo '{success: false, "msg":""}';
		}
	}

	static function checkProductDeletionWarnings($pResultArray, $pSentProductCodes)
	{
		$smarty = SmartyObj::newSmarty('AdminProducts');

		if ($pResultArray['error'] === '')
		{
			$linkedProductCount = count($pResultArray['data']);

			if ($linkedProductCount === 0)
			{
				echo '{"success":true, "msg":"' . str_replace('^0', $pSentProductCodes, $smarty->get_config_vars('str_DeleteProductConfirmation')) . '"}';
			}
			elseif ($linkedProductCount === 1)
			{
				echo '{"success":true, "msg":"' . str_replace('^0', $pResultArray['data'][0], $smarty->get_config_vars('str_ConfirmLinkedProductDelete')) . '"}';
			}
			else
			{
				$productCodeString = join(', ', $pResultArray['data']);
				echo '{"success":true, "msg":"' . str_replace('^0', $productCodeString, $smarty->get_config_vars('str_ConfirmMultipleLinkedProductDelete')) . '"}';
			}
		}
		else
		{
			echo '{"success": false, "msg":"' . $pResultArray['error'] . '}';
		}
	}

	static function getLinkingPreviewGridData($pResultArray)
	{
		$smarty = SmartyObj::newSmarty('AdminProducts');

        $dataArray = array();
        $data = $pResultArray['data'];
        $companyCode = '';
		$globalString = UtilsObj::ExtJSEscape($smarty->get_config_vars('str_Global'));
        $rowID = 0;

        echo '[';

        foreach ($data as $product)
        {
			if ($product['companycode'] == '')
			{
				$companyCode = $globalString;
			}
			else
			{
				$companyCode = $product['companycode'];
			}

            $dataArray[] = "[" . $rowID . ",'" . UtilsObj::ExtJSEscape($product['code']) . "','" . UtilsObj::ExtJSEscape(LocalizationObj::getLocaleString($product['name'], '')) . "','" . $companyCode . "']";
            $rowID++;
        }

        echo '[' . ($rowID + 1) . '],';
		echo implode(',', $dataArray);

		echo ']';
	}

	static function linkingPreviewDisplay()
	{
		global $gConstants;

		$smarty = SmartyObj::newSmarty('AdminProducts');
		$smarty->assign('optionms', ($gConstants['optionms'] ? true : false));

        $smarty->displayLocale('admin/products/productlinkpreview.tpl');
	}
}

?>
