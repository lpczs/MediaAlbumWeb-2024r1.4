<?php

class Order_view
{
    static function initialize()
    {
        global $ac_config;
        global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
        $smarty->assign('url', UtilsObj::getBrandedWebUrl() . '?fsaction=Order.initialize2&ref=' . $_GET['ref']);
        $smarty->displayLocale($ac_config['FRAMEPARENTURL']);
    }

	/**
	* Prepares section data for use in template.
	*
	* The function is calling itself recursively to fetch sub-sections.
	*
	* @static
	*
	* @param array $pSections					the checkbox array from order session
	* @param integer $pOrderlineId 				order line id or -1 if order footer
	* @param string $pStage				 	current stage in ordering process
	* @param string $pStrLabelNotAvailable 	string in case it's needed
	* @param string $pStrLabelChange			string in case it's needed
	* @param string $pStrLabelAdd 				string in case it's needed
	* @param string $pStrLabelRemove 			string in case it's needed
	*
	* @author Steffen Haugk
	* @since Version 3.0.0
	*/
	static function prepareSections($pSections, $pOrderlineId, $pStage, $pStrLabelNotAvailable, $pStrLabelChange, $pStrLabelAdd, $pStrLabelRemove)
	{
		global $gSession;
		global $archivedOrderData;

		if($pStage == 'email')
		{
			$orderDetailsArray = $archivedOrderData;
		}
		else
		{
			$orderDetailsArray = $gSession;
		}

    	$resultArray = array();
		$canProcedeToNextStage = true;

		foreach ($pSections as $section)
		{
			$section['sectionlabel'] = UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($section['sectionlabel'], $orderDetailsArray['browserlanguagecode'], true));
			$section['prompt'] = UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($section['prompt'], $orderDetailsArray['browserlanguagecode'], true));
			$section['discountname'] = UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($orderDetailsArray['order']['vouchername'], $orderDetailsArray['browserlanguagecode'], true));
            $section['discountvalueraw'] = $section['discountvalue'];
            $section['displaytaxraw'] = $section['discountedtax'];
            $section['displaytaxraw'] = UtilsObj::bround($section['displaytaxraw'], $orderDetailsArray['order']['currencydecimalplaces']);

			$componentHasPreview = false;
			$showComponentName = false;
			$showItemTotalRow = false;
			$componentPreviewSrc = '';
			$unitSell = $unitSellTax = $unitSellNoTax = $unitSellWithTax = $pStrLabelNotAvailable;
			$componentPreview = UtilsObj::getAssetRequest($section['code'], 'components');

	        if ($section['defaultcode'] != '')
	        {
	            if ($section['componenthasprice'] == 1)
	            {
					if ($pStage == 'email')
					{
						if (($orderDetailsArray['order']['orderalltaxratesequal'] == 0) && ($orderDetailsArray['order']['showpriceswithtax'] == 0))
						{
							$section['displaytax'] = ($section['subtotal'] / 100) * $section['orderfootertaxrate'];
                            $section['displaytax'] = UtilsObj::bround($section['displaytax'], $orderDetailsArray['order']['currencydecimalplaces']);
							$section['displaytaxraw'] = $section['displaytax'];
							$section['itemtotal'] = $section['subtotal'] + $section['totaltax'];
						}
						else
						{
							$section['displaytaxraw'] = ($section['subtotal'] / (100 + $section['orderfootertaxrate'])) * $section['orderfootertaxrate'];
                            $section['displaytaxraw'] = UtilsObj::bround($section['displaytaxraw'], $orderDetailsArray['order']['currencydecimalplaces']);
							$section['itemtotal'] = $section['subtotal'];
							$section['displaytax'] = $section['displaytaxraw'];
						}

					}
					else
					{
						$section['displaytaxraw'] = ($section['totalsellwithtax'] / (100 + $section['orderfootertaxrate'])) * $section['orderfootertaxrate'];
                        $section['displaytaxraw'] = UtilsObj::bround($section['displaytaxraw'], $orderDetailsArray['order']['currencydecimalplaces']);
						$section['displaytax'] = $section['displaytaxraw'];

						if (($orderDetailsArray['order']['orderalltaxratesequal'] == 0) && ($orderDetailsArray['order']['showpriceswithtax'] == 0))
						{
							$section['displaytax'] = ($section['totalsellnotax'] / 100) * $section['orderfootertaxrate'];
                            $section['displaytax'] = UtilsObj::bround($section['displaytax'], $orderDetailsArray['order']['currencydecimalplaces']);
							$section['itemtotal'] = $section['totalsell'] + $section['totaltax'];
						}
						else
						{
							$section['itemtotal'] = $section['totalsell'];
						}
					}


//					$section['displayprice'] = $section['itemtotal'];
                    $section['displayprice'] = $section['subtotal'] + $section['displaytax'];

					// Added Stage to switch to using archievedDataArray instead of gSession
					$section['unitsell'] = self::formatOrderCurrencyNumber($section['unitsell'], $pStage);
					$section['subtotal'] = self::formatOrderCurrencyNumber($section['subtotal'], $pStage);
					$section['totalsell'] = self::formatOrderCurrencyNumber($section['totalsell'], $pStage);
					$section['totalsellnotax'] = self::formatOrderCurrencyNumber($section['totalsellnotax'], $pStage);
					$section['totalsellwithtax'] = self::formatOrderCurrencyNumber($section['totalsellwithtax'], $pStage);
					$section['discountvalue'] = self::formatOrderCurrencyNumber(($section['discountvalue'] * -1), $pStage);
					$section['taxratename'] = LocalizationObj::getLocaleString($section['orderfootertaxname'], $orderDetailsArray['browserlanguagecode'], true);
					$section['taxrate'] = UtilsObj::formatCurrencyNumber($section['orderfootertaxrate'], 2);

					$section['displaytax'] = self::formatOrderCurrencyNumber($section['displaytax'], $pStage);
					$section['displayprice'] = self::formatOrderCurrencyNumber($section['displayprice'], $pStage);

                    if (isset($section['itemcomponenttotalcost']))
                    {
                        $section['itemcomponenttotalcost'] = self::formatOrderCurrencyNumber($section['itemcomponenttotalcost'], $pStage);
                        $section['itemcomponenttotalsell'] = self::formatOrderCurrencyNumber($section['itemcomponenttotalsell'], $pStage);
                        $section['itemcomponenttotalsellnotax'] = self::formatOrderCurrencyNumber($section['itemcomponenttotalsellnotax'], $pStage);
                        $section['itemcomponenttotalsellwithtax'] = self::formatOrderCurrencyNumber($section['itemcomponenttotalsellwithtax'], $pStage);
                        $section['itemcomponenttotalweight'] = self::formatOrderCurrencyNumber($section['itemcomponenttotalweight'], $pStage);
                    }

					// item tax
                    $smarty = SmartyObj::newSmarty('Order', '', '', '', false, false);
					$includesItemTaxText = $smarty->get_config_vars('str_LabelIncludesTax');
					$includesItemTaxText = str_replace('^0', $section['taxrate'], $includesItemTaxText);
					$includesItemTaxText = str_replace('^1', $section['taxratename'], $includesItemTaxText);
					$includesItemTaxText = str_replace('^2', $section['displaytax'], $includesItemTaxText);
					$section['includesitemtaxtext'] = $includesItemTaxText;


	                // we have more than one component in this section so we will display it
	                if ($section['count'] > 0)
	                {
	                    $showComponentName = true;
	                }

	                // the selected component is different to the default component so we will display it
	                // also show if checkbox component
	                if (($section['defaultcode'] != $section['code']) || ($componentPreview !== '') || ($section['islist'] == 0))
	                {
	                    $showComponentName = true;
	                }

	                if ($showComponentName == true)
	                {

						$buttons = array();
						$button = array();

						if (($section['count'] > 1) && ($pStage == 'qty'))
						{
							$button['label'] = $pStrLabelChange;
                            $button['class'] = '';
                            $button['action'] = 'fnChangeComponent';

							$buttons[] = $button;
							$showItemTotalRow = true;
						}

						$section['itemcomponentbuttons'] = $buttons;
					}
				}
				else
				{
					if ($canProcedeToNextStage)
					{
						$canProcedeToNextStage = false;
					}

					$showComponentName = true;
					$section['itemcompletevaluetextstyle'] = 'itempricenotavailable';
					$section['totalsell'] = $pStrLabelNotAvailable;
					$section['itemtextstyle'] = 'itempricenotavailable';
				}

			}

            $section['itemcomponentname'] = UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($section['name'], $orderDetailsArray['browserlanguagecode'], true));
            $section['itemcomponentinfo'] = UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($section['info'], $orderDetailsArray['browserlanguagecode'], true));
			$section['itemcomponentmoreinfolinkurl'] = UtilsObj::escapeInputForHTML($section['moreinfolinkurl'], $orderDetailsArray['browserlanguagecode'], true);
			$section['itemcomponentmoreinfolinktext'] = UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($section['moreinfolinktext'], $orderDetailsArray['browserlanguagecode'], true));
            $section['itemcomponentpriceinfo'] = LocalizationObj::getLocaleString($section['priceinfo'], $orderDetailsArray['browserlanguagecode'], true);

            if ($componentPreview !== '')
            {
                //Email Content, added stage in to embed thumbnail for sections
                if ($pStage != 'email')
                {
                    $componentPreviewSrc = $componentPreview;
                }
                else
                {
                    if ($orderDetailsArray['emailthumbnailtype'] === TPX_ATTACHED_THUMBNAIL)
                    {
                        $componentPreviewSrc = "cid:" . $section['code'] .':components';
                    }
                    else if ($orderDetailsArray['emailthumbnailtype'] === TPX_REFERENCED_THUMBNAIL)
                    {
                        $componentPreviewSrc = $componentPreview;
                    }
                }
                $componentHasPreview = true;
                $showItemTotalRow = true;
            }

			$section['showcomponentname'] = $showComponentName;
			$section['haspreview'] = $componentHasPreview;
			$section['componentpreviewsrc'] = $componentPreviewSrc;

			$section['canprocedetonextstage'] = $canProcedeToNextStage;

			// prepare sub-section
			$section['subsections'] = self::prepareSections($section['subsections'], $pOrderlineId, $pStage, $pStrLabelNotAvailable, $pStrLabelChange, $pStrLabelAdd, $pStrLabelRemove);

			// Added Stage to switch to using archievedDataArray instead of gSession
			// prepare checkboxes
			$section['checkboxes'] = self::prepareCheckboxes($section['checkboxes'], $pOrderlineId, $pStrLabelNotAvailable, $pStrLabelAdd, $pStrLabelRemove, $pStage);

			$resultArray[] = $section;
		}

		return $resultArray;
	}


	/**
	* Prepares checkbox data for use in template.
	*
	* @static
	*
	* @param array   $pCheckboxes				the checkbox array from order session
	* @param integer $pOrderlineId 				order line id or -1 if order footer
	* @param string  $pStrLabelNotAvailable 	string in case it's needed
	* @param string  $pStrLabelAdd 				string in case it's needed
	* @param string  $pStrLabelRemove 			string in case it's needed
	*
	* @author Steffen Haugk
	* @since Version 3.0.0
	*/
    static function prepareCheckboxes($pCheckboxes, $pOrderlineId, $pStrLabelNotAvailable, $pStrLabelAdd, $pStrLabelRemove, $pStage = '')
    {
        global $gSession;
		global $archivedOrderData;

		if($pStage == 'email')
		{
			$orderDetailsArray = $archivedOrderData;
		}
		else
		{
			$orderDetailsArray = $gSession;
		}

    	$resultArray = array();

		// loop around checkboxes
		foreach ($pCheckboxes as $checkbox)
		{
            $checkbox['sectionlabel'] = LocalizationObj::getLocaleString($checkbox['name'], $orderDetailsArray['browserlanguagecode'], true);
            $checkbox['discountname'] = UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($orderDetailsArray['order']['vouchername'], $orderDetailsArray['browserlanguagecode'], true));
            $checkbox['discountvalueraw'] = $checkbox['discountvalue'];
            $checkbox['displaytaxraw'] = $checkbox['discountedtax'];
            $checkbox['displaytaxraw'] = UtilsObj::bround($checkbox['displaytaxraw'], $orderDetailsArray['order']['currencydecimalplaces']);

			$componentHasPreview = false;
			$showComponentName = true;
			$showItemTotalRow = false;
			$componentPreviewSrc = '';
            $unitSell = $unitSellTax = $unitSellNoTax = $unitSellWithTax = $pStrLabelNotAvailable;
			$componentPreview = UtilsObj::getAssetRequest($checkbox['code'], 'components');

            if ($checkbox['code'] != '' && $checkbox['componenthasprice'] == 1)
            {
				if ($pStage == 'email')
				{
					if (($orderDetailsArray['order']['orderalltaxratesequal'] == 0) && ($orderDetailsArray['order']['showpriceswithtax'] == 0))
					{
						$checkbox['displaytax'] = ($checkbox['subtotal'] / 100) * $checkbox['orderfootertaxrate'];
						$checkbox['displaytax'] = UtilsObj::bround($checkbox['displaytax'], $orderDetailsArray['order']['currencydecimalplaces']);
						$checkbox['displaytaxraw'] = $checkbox['displaytax'];
						$checkbox['itemtotal'] = $checkbox['subtotal'] + $checkbox['totaltax'];
					}
					else
					{
						$checkbox['displaytaxraw'] = ($checkbox['subtotal'] / (100 + $checkbox['orderfootertaxrate'])) * $checkbox['orderfootertaxrate'];
                        $checkbox['displaytaxraw'] = UtilsObj::bround($checkbox['displaytaxraw'], $orderDetailsArray['order']['currencydecimalplaces']);
						$checkbox['itemtotal'] = $checkbox['subtotal'];
						$checkbox['displaytax'] = $checkbox['displaytaxraw'];
					}
				}
				else
				{
					$checkbox['displaytaxraw'] = ($checkbox['totalsellwithtax'] / (100 + $checkbox['orderfootertaxrate'])) * $checkbox['orderfootertaxrate'];
                    $checkbox['displaytaxraw'] = UtilsObj::bround($checkbox['displaytaxraw'], $orderDetailsArray['order']['currencydecimalplaces']);
					$checkbox['displaytax'] = $checkbox['displaytaxraw'];

					if (($orderDetailsArray['order']['orderalltaxratesequal'] == 0) && ($orderDetailsArray['order']['showpriceswithtax'] == 0))
					{
						$checkbox['displaytax'] = ($checkbox['totalsellnotax'] / 100) * $checkbox['orderfootertaxrate'];
                        $checkbox['displaytax'] = UtilsObj::bround($checkbox['displaytax'], $orderDetailsArray['order']['currencydecimalplaces']);
						$checkbox['itemtotal'] = $checkbox['totalsell'] + $checkbox['totaltax'];
					}
					else
					{
						$checkbox['itemtotal'] = $checkbox['totalsell'];
					}
				}


	            //$checkbox['displayprice'] = $checkbox['itemtotal'];
	            $checkbox['displayprice'] = $checkbox['subtotal'] + $checkbox['displaytax'];

				// Added Stage to switch to using archievedDataArray instead of gSession
				$checkbox['unitsell'] = self::formatOrderCurrencyNumber($checkbox['unitsell'], $pStage);
				$checkbox['subtotal'] = self::formatOrderCurrencyNumber($checkbox['subtotal'], $pStage);
				$checkbox['totalsell'] = self::formatOrderCurrencyNumber($checkbox['totalsell'], $pStage);
				$checkbox['totalsellnotax'] = self::formatOrderCurrencyNumber($checkbox['totalsellnotax'], $pStage);
				$checkbox['totalsellwithtax'] = self::formatOrderCurrencyNumber($checkbox['totalsellwithtax'], $pStage);
                $checkbox['discountvalue'] = self::formatOrderCurrencyNumber(($checkbox['discountvalue'] * -1), $pStage);
				$checkbox['taxratename'] = LocalizationObj::getLocaleString($checkbox['orderfootertaxname'], $orderDetailsArray['browserlanguagecode'], true);
				$checkbox['taxrate'] = UtilsObj::formatCurrencyNumber($checkbox['orderfootertaxrate'], 2);

				$checkbox['displaytax'] = self::formatOrderCurrencyNumber($checkbox['displaytax'], $pStage);
				$checkbox['displayprice'] = self::formatOrderCurrencyNumber($checkbox['displayprice'], $pStage);

				// item tax
                $smarty = SmartyObj::newSmarty('Order', '', '', '', false, false);
                $includesItemTaxText = $smarty->get_config_vars('str_LabelIncludesTax');
				$includesItemTaxText = str_replace('^0', $checkbox['taxrate'], $includesItemTaxText);
				$includesItemTaxText = str_replace('^1', $checkbox['taxratename'], $includesItemTaxText);
				$includesItemTaxText = str_replace('^2', $checkbox['displaytax'], $includesItemTaxText);
				$checkbox['includesitemtaxtext'] = $includesItemTaxText;

				$buttons = array(); // list of buttons to show against checkbox
				$button = array();  // individual, temporary button to be added to $buttons

                $button['class'] = '';
                $button['action'] = 'fnUpdateCheckbox';

				if ($checkbox['checked'] == 1)
				{
					$button['label'] = $pStrLabelRemove;
                    $button['class'] = 'btnRemove';
				}
				else
				{
					$button['label'] = $pStrLabelAdd;
                    $button['class'] = 'btnAdd';
				}

				$buttons[] = $button;
				$showItemTotalRow = true;

				$checkbox['itemcomponentbuttons'] = $buttons;
			}
			else
			{
				$checkbox['itemcompletevaluetextstyle'] = 'itempricenotavailable';
				$checkbox['totalsell'] = $pStrLabelNotAvailable;
				$checkbox['itemtextstyle'] = 'itempricenotavailable';
			}

            $checkbox['itemcomponentname'] = UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($checkbox['name'], $orderDetailsArray['browserlanguagecode'], true));
            $checkbox['itemcomponentinfo'] = UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($checkbox['info'], $orderDetailsArray['browserlanguagecode'], true));
			$checkbox['itemcomponentmoreinfolinkurl'] = UtilsObj::escapeInputForHTML($checkbox['moreinfolinkurl'], $orderDetailsArray['browserlanguagecode'], true);
			$checkbox['itemcomponentmoreinfolinktext'] = UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($checkbox['moreinfolinktext'], $orderDetailsArray['browserlanguagecode'], true));
            $checkbox['itemcomponentpriceinfo'] = LocalizationObj::getLocaleString($checkbox['priceinfo'], $orderDetailsArray['browserlanguagecode'], true);
            $checkbox['itemcomponentcategoryname'] = UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($checkbox['categoryname'], $orderDetailsArray['browserlanguagecode'], true));
            $checkbox['itemcomponentprompt'] = UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($checkbox['prompt'], $orderDetailsArray['browserlanguagecode'], true));
            
			if ($componentPreview !== '')
            {
				//Email content, added pStage == email to embed thumbnail different way.
				if ($pStage == 'email')
				{
					if ($orderDetailsArray['emailthumbnailtype'] === TPX_ATTACHED_THUMBNAIL)
					{
						$componentPreviewSrc = "cid:" . $checkbox['code'] .':components';
					}
					else if($orderDetailsArray['emailthumbnailtype'] === TPX_REFERENCED_THUMBNAIL)
					{
						$componentPreviewSrc = $componentPreview;
					}
				}
				else
				{
					$componentPreviewSrc = $componentPreview;
				}
				
                $componentHasPreview = true;
                $showItemTotalRow = true;
            }

			$checkbox['showcomponentname'] = $showComponentName;
			$checkbox['haspreview'] = $componentHasPreview;
			$checkbox['componentpreviewsrc'] = $componentPreviewSrc;

			$resultArray[] = $checkbox;
		}

        return $resultArray;
    }

    static function prepareSinglePrints($pPictures, $pOrderLine, $pStrLabelNotAvailable, $pStage)
	{
		global $gSession;
		global $archivedOrderData;

		if ($pStage == 'email')
		{
			$orderDetailsArray = $archivedOrderData;
			$productOptions = $orderDetailsArray['items'][$pOrderLine]['productoptions'];
		}
		else
		{
			$orderDetailsArray = $gSession;
			$productOptions = $gSession['items'][$pOrderLine]['productoptions'];
		}

    	$resultArray = array();
    	$allPicturesHavePrices = 1;
		$hasPicturesLookup = false;
		$pictures = $pPictures;

    	// sort the pictures into groups of setid, component code and sub-component code
		if (array_key_exists('key', $pPictures))
		{
			$hasPicturesLookup = true;
			$pictures = $pPictures['key'];
		}

		foreach ($pictures as $pictureKey => $picture)
		{
			if ($hasPicturesLookup)
			{
				// Rebuild the picture data from the lookup table.
				$lookUpKey = $picture;
				$uniqueLookup = $lookUpKey . TPX_PICTURES_LOOKUP_SEPERATOR . $pictureKey;
				$picture = $pPictures['data'][$lookUpKey];
				$pictureData = $pPictures['printdata'][$uniqueLookup];
				$pictureName = $pPictures['pname'][$pictureData['fn']];

				$picture['pagename'] = LocalizationObj::getLocaleString($pictureName, $orderDetailsArray['browserlanguagecode'], true);
				$picture['picturename'] = LocalizationObj::getLocaleString($pictureName, $orderDetailsArray['browserlanguagecode'], true);

				$picture = array_merge($picture, $pictureData);
			}
			else
			{
				$picture = UtilsObj::convertPicturesDataToSmallerFormat($picture);
			}

			if ($productOptions == TPX_PRODUCTOPTION_PRICING_PERCOMPONENTSUBCOMPONENT)
			{
				$groupSortKey = $picture['code'] . "\t" . $picture['subcode'];
			}
			else
			{
				$groupSortKey = $picture['code'] . "\t" . $picture['subcode'];
			}

			if ($picture['componenthasprice'] == 0)
			{
				$picture['ts'] = $pStrLabelNotAvailable;
				$allPicturesHavePrices = 0;
			}

			// add the group if it does not exist
			if (! array_key_exists($groupSortKey, $resultArray))
			{
				$componentName = LocalizationObj::getLocaleString($picture['name'], $orderDetailsArray['browserlanguagecode'], true);
				$subComponentName = '';

				if ($picture['subcode'] != '')
				{
					$subComponentName = ' - ' . LocalizationObj::getLocaleString($picture['subname'], $orderDetailsArray['browserlanguagecode'], true);
				}

				$groupDisplayName = $componentName . $subComponentName;

				$resultArray[$groupSortKey]['groupdisplayname'] = $groupDisplayName;
				$resultArray[$groupSortKey]['grouptotalsell'] = 0.00;
				$resultArray[$groupSortKey]['componenthasprice'] = $allPicturesHavePrices;
				$resultArray[$groupSortKey]['picturecount'] = 0;
			}

			// update the group total
			if ($orderDetailsArray['order']['showpriceswithtax'] == 0)
			{
				$resultArray[$groupSortKey]['grouptotalsell'] += ($picture['tsnt'] + $picture['subtsnt']);
			}
			else
			{
				$resultArray[$groupSortKey]['grouptotalsell'] += ($picture['tswt'] + $picture['subtswt']);
			}

			$resultArray[$groupSortKey]['picturecount'] += $picture['qty'];
		}

		// format the total sell price for the groups
		foreach ($resultArray as &$sizeGroup)
		{
			$sizeGroup['formatedgrouptotalsell'] = self::formatOrderCurrencyNumber($sizeGroup['grouptotalsell'], $pStage);
		}

		return $resultArray;
	}


    static function displayOrderLineJobTicket($pResultArray, $pStage, $pOrderLine, $pOrderLineID, $pSmarty)
	{
        global $gSession;

        global $archivedOrderData;

		if($pStage == 'email')
		{
			$orderLineArray = $archivedOrderData;
		}
		else
		{
			$orderLineArray = $gSession;
		}

		$miscOrderItemData = Array();
		$showItemTax = false;
		$showShippingTax = false;
		$showTaxTotal = false;
		$showItemTotalRow = false;
		$orderItem = Array();
		$itemCheckBoxes = Array();
		$orderItemProductCode = '';
		$orderItemQty = 0;
		$hasOrderFooterSections = false;
		$lineFooterSections = Array();
		$orderItemProductName = '';
		$orderItemProjectName = '';
		$orderItemProductInfo = '';
		$orderItemAssetID = 0;
		$orderItemQtyDropDown = 0;
		$itemTotalSell = 0.00;
		$itemTaxTotal = 0.00;
		$ItemProductTotalSell = 0.00;
		$ItemProductTotalTax = 0.00;
		$ItemProductTotalNoTax = 0.00;
		$ItemProductTotalSellWithTax = 0.00;
		$ItemTaxRate = 0.00;
		$itemSubTotal = 0.00;
		$itemHasPoductPrice = 0;
		$itemVoucherApplied = 0;
		$itemDiscountValue = 0;
		$itemTaxName = '';
		$itemTaxCode = '';
		$itemHasProductPrice = 0;
		$ItemExternalAssets = Array();
		$ItemPictures = Array();
		$itemCalendarCustomisations = Array();
		$itemAIComponent = Array();
		$itemProductType = -1;
		$parentOrderItemID = 0;
		$itemHasCompanions = false;
        $itemPageCount = 0;

    	if ($pOrderLine == TPX_ORDERFOOTER_ID)
    	{
    		$orderFooterSectionCount = count($orderLineArray['order']['orderFooterSections']);

			for ($i = 0; $i < $orderFooterSectionCount; $i++)
			{
				if ($orderLineArray['order']['orderFooterSections'][$i]['orderlineid'] == $pOrderLineID)
				{
					$hasOrderFooterSections = true;
					$orderItem = $orderLineArray['order']['orderFooterSections'][$i];
    				$itemCheckBoxes = $orderItem['checkboxes'];
    				$orderItemProductCode = $orderItem['itemproductcode'];
    				$orderItemQty = $orderItem['itemqty'];
    				$itemTotalSell = $orderItem['totalsell'];
    				$itemTaxTotal = $orderItem['totaltax'];
					break;
				}
			}

    		$orderItemLineID = -1;
    		$itemSections = $orderLineArray['order']['orderFooterSections'];
    		$lineFooterCheckboxes = $orderLineArray['order']['orderFooterCheckboxes'];
    	}
    	else
    	{
			$orderItem = $orderLineArray['items'][$pOrderLine];
    		$orderItemLineID = $orderItem['orderlineid'];
    		$itemSections = $orderItem['sections'];
			$itemCheckBoxes = $orderItem['checkboxes'];

    		$lineFooterSections = $orderItem['lineFooterSections'];
    		$lineFooterCheckboxes = $orderItem['lineFooterCheckboxes'];
    		$orderItemProductCode = $orderItem['itemproductcode'];
    		$orderItemProductName = $orderItem['itemproductname'];
    		$orderItemProjectName = $orderItem['itemprojectname'];
    		$orderItemProductInfo = $orderItem['itemproductinfo'];
    		$orderItemAssetID = $orderItem['assetid'];
    		$orderItemQty = $orderItem['itemqty'];
    		$orderItemQtyDropDown = $orderItem['itemqtydropdown'];
    		$ItemProductTotalSell = $orderItem['itemproducttotalsell'];
    		$ItemProductTotalTax = $orderItem['itemproducttotaltax'];
    		$ItemProductTotalNoTax = $orderItem['itemproducttotalsellnotax'];
    		$ItemProductTotalSellWithTax = $orderItem['itemproducttotalsellwithtax'];
    		$ItemExternalAssets = $orderItem['itemexternalassets'];
    		$ItemPictures = $orderItem['pictures'];
    		$ItemTaxRate = $orderItem['itemtaxrate'];
    		$itemTotalSell = $orderItem['itemtotalsell'];
    		$itemTaxTotal = $orderItem['itemtaxtotal'];
    		$itemSubTotal = $orderItem['itemsubtotal'];
    		$itemHasPoductPrice = $orderItem['itemhasproductprice'];
    		$itemVoucherApplied = $orderItem['itemvoucherapplied'];
    		$itemDiscountValue = $orderItem['itemdiscountvalue'];
    		$itemTaxName = $orderItem['itemtaxname'];
    		$itemTaxCode = $orderItem['itemtaxcode'];
    		$itemHasProductPrice = $orderItem['itemhasproductprice'];
    		$itemCalendarCustomisations = $orderItem['calendarcustomisations'];
			$itemProductType = $orderItem['itemproducttype'];
			$itemPageCount = $orderItem['itempagecount'];

			if ((array_key_exists("aicomponent", $orderItem)) && ($orderItem['aicomponent'] !== null) && ($orderItem['aicomponent']['used'] === true))
			{
				$itemAIComponent = $orderItem['aicomponent'];
			}

    		if ($pStage == 'email')
    		{
				// Emails need to know if it's a companion to display the thumbnails correctly.
    			$parentOrderItemID = $orderItem['parentorderitemid'];
				$itemHasCompanions = false;
    		}
    		else
    		{
    			$parentOrderItemID = $orderItem['parentorderitemid'];
    			$itemHasCompanions = $orderItem['itemhascompanions'];
    		}

    	}

        $forceApplyDiscountToLineItems = false;
        $applyDiscountToSingleLineItem = (($orderLineArray['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_LOWEST_PRICED) ||
                                          ($orderLineArray['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_HIGHEST_PRICED));

        // check if the total voucher should be applied to each line (similar to different tax rates)
        if (($gSession['order']['voucherdiscountsection'] == 'TOTAL') &&
            (($gSession['order']['ordertotalitemdiscountable'] > $gSession['order']['voucherapplytoqty']) || ($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER)))
        {
            $forceApplyDiscountToLineItems = true;
        }

        // set the cart and calculations to be applied to lines, not totals
        $applyDiscountToSingleLineItem = ($applyDiscountToSingleLineItem || $forceApplyDiscountToLineItems);


		$formatDP = $orderLineArray['order']['currencydecimalplaces'];
		$strLabelNotAvailable = $pSmarty->get_config_vars('str_LabelNotAvailable');

		$strLabelRemove = $pSmarty->get_config_vars('str_LabelRemove');
		$strLabelAdd = $pSmarty->get_config_vars('str_LabelAdd');
		$strLabelChange = $pSmarty->get_config_vars('str_LabelChange');

		$showZeroTax = ($orderLineArray['order']['showzerotax'] == 1) ? true : false;
		$showTaxBreakdown = ($orderLineArray['order']['showtaxbreakdown'] == 1) ? true : false;
		$showAlwaysTaxTotal = ($orderLineArray['order']['showalwaystaxtotal'] == 1) ? true : false;
		$showPricesWithTax = ($orderLineArray['order']['showpriceswithtax'] == 1) ? true : false;
		$formattedShippingTaxName = LocalizationObj::getLocaleString($orderLineArray['shipping'][0]['shippingratetaxname'], $orderLineArray['browserlanguagecode'], true);
		$formattedShippingTaxRate = UtilsObj::formatCurrencyNumber($orderLineArray['shipping'][0]['shippingratetaxrate'], 2);
		// for replacing amount in web string
        $formattedOrderTotalTax2 = UtilsObj::formatCurrencyNumber($orderLineArray['order']['ordertotaltax'], $formatDP,
						$orderLineArray['browserlanguagecode'], $orderLineArray['order']['currencysymbol'], $orderLineArray['order']['currencysymbolatfront']);
		// for replacing amount in web string
		$formattedShippingTaxTotal2 = self::formatOrderCurrencyNumber($orderLineArray['shipping'][0]['shippingratetaxtotal'], $pStage);

        $miscOrderItemData['orderlineid'] = $orderItemLineID;

        if ($orderLineArray['order']['orderalltaxratesequal'] == 0)
        {
            $differentTaxRates = true;
        }
        else
        {
            $differentTaxRates = false;
        }

        $miscOrderItemData['itemproductcode'] = $orderItemProductCode;
        $miscOrderItemData['itemproductname'] = LocalizationObj::getLocaleString($orderItemProductName, $orderLineArray['browserlanguagecode'], true);
        $miscOrderItemData['itemproductinfo'] = LocalizationObj::getLocaleString($orderItemProductInfo, $orderLineArray['browserlanguagecode'], true);
        $miscOrderItemData['projectname'] = UtilsObj::escapeInputForHTML($orderItemProjectName);
        $miscOrderItemData['parentorderitemid'] = $parentOrderItemID;
        $miscOrderItemData['itemhascompanions'] = $itemHasCompanions;
		$productPreview = UtilsObj::getAssetRequest($orderItemProductCode, 'products');

		if ($productPreview !== '')
		{
			if ($pStage == 'email')
			{
				if($orderLineArray['emailthumbnailtype'] === TPX_ATTACHED_THUMBNAIL)
				{
					$miscOrderItemData['assetrequest'] = "cid:" . $orderItemProductCode . ':products';
				}
				else if($orderLineArray['emailthumbnailtype'] === TPX_REFERENCED_THUMBNAIL)
				{
					$miscOrderItemData['assetrequest'] = $productPreview;
				}
			}
			else
			{
				$miscOrderItemData['assetrequest'] = $productPreview;
			}
		}

		$miscOrderItemData['projectthumbnail'] = '';

		//try to load in the project thumbnail
		//desktop and online have different mechanisms for achieving this
		if ($orderItem['source'] === TPX_SOURCE_ONLINE)
		{
			// If it's a reorder then the project may not have a project thumbnail if it's an older project, so test there is one.
			// Always test for emails due to the way images are embedded into the email to be able to do the fallback if the project does not have a project thumbnail.
			if (($pStage == 'email') || ($gSession['order']['isreorder'] == 1))
			{
				require_once(__DIR__ . '/../libs/internal/curl/Curl.php');

				// Do a request to check if the project has a thumbnail. 
				$projectThumbnailAPIPath = UtilsObj::getProjectThumbnailAPIPath('hasThumbnail', ['projectref' => $orderItem['itemprojectref']]);
				$apiResult = CurlObj::get($projectThumbnailAPIPath, TPX_CURL_RETRY, TPX_CURL_TIMEOUT);

				if ($apiResult['error'] === '')
				{
					$projectHasThumbnailData = json_decode($apiResult['data'], true);
	
					if (($projectHasThumbnailData['error'] === 0) && ($projectHasThumbnailData['hasthumbnail'] === true))
					{
						if ($pStage == 'email')
						{
							if ($orderLineArray['emailthumbnailtype'] == TPX_ATTACHED_THUMBNAIL)
							{
								$miscOrderItemData['projectthumbnail'] = 'cid:' . $orderItem['itemprojectref'] . ":projectthumbnailonline";
							}
							else if ($orderLineArray['emailthumbnailtype'] == TPX_REFERENCED_THUMBNAIL)
							{
								$miscOrderItemData['projectthumbnail'] = UtilsObj::getProjectThumbnailAPIPath('displayThumbnail', ['projectreflist' => [$orderItem['itemprojectref']], 'displaymode' => 2]);
							}
						}
						else
						{
							$miscOrderItemData['projectthumbnail'] = UtilsObj::getProjectThumbnailAPIPath('displayThumbnail', ['projectreflist' => [$orderItem['itemprojectref']], 'displaymode' => 2]);
						}
					}
				}
			}
			else
			{
				$miscOrderItemData['projectthumbnail'] = UtilsObj::getProjectThumbnailAPIPath('displayThumbnail', ['projectreflist' => [$orderItem['itemprojectref']], 'displaymode' => 2]);
			}
		}
		else 
		{
			//check if we have a project thumbnail available
			//in contrast to online thumbnails may not be present in any desktop order
			$thumbnailAvailableArray = DatabaseObj::getDesktopProjectThumbnailAvailabilityFromProjectRef($orderItem['itemprojectref']);

			if (($thumbnailAvailableArray['error'] === '') && ($thumbnailAvailableArray['available'] === true))
			{
				if ($pStage == 'email')
				{
					if ($orderLineArray['emailthumbnailtype'] == TPX_ATTACHED_THUMBNAIL)
					{
						$miscOrderItemData['projectthumbnail'] = 'cid:' . $orderItem['itemprojectref'] . ":projectthumbnaildesktop";
					}
					else if ($orderLineArray['emailthumbnailtype'] == TPX_REFERENCED_THUMBNAIL)
					{
						$miscOrderItemData['projectthumbnail'] = UtilsObj::buildDesktopProjectThumbnailWebURL($orderItem['itemprojectref']);
					}
				}
				else
				{
					$miscOrderItemData['projectthumbnail'] = UtilsObj::buildDesktopProjectThumbnailWebURL($orderItem['itemprojectref']);
				}
			}
		}

        $miscOrderItemData['itemqty'] = $orderItemQty;
        $miscOrderItemData['itemqtydropdown'] = $orderItemQtyDropDown;

        $showProductPrice = 1;

        if ($itemProductType == TPX_PRODUCTCOLLECTIONTYPE_SINGLEPRINTS)
        {
			$showProductPrice = 0;

			if ($ItemProductTotalSell > 0.00)
			{
				$showProductPrice = 1;
			}
        }

        $miscOrderItemData['itemproducttotalsell'] = self::formatOrderCurrencyNumber($ItemProductTotalSell, $pStage);
        $miscOrderItemData['itemshowproductsell'] = $showProductPrice;
        $miscOrderItemData['itemproducttotaltax'] = $ItemProductTotalTax;

		$miscOrderItemData['calendarcustomisations'] = array();
		$miscOrderItemData['aicomponent'] = array();

        foreach ($itemCalendarCustomisations as $calendarCustomisation)
        {
        	if (($calendarCustomisation['componentqty'] > 0) && ($calendarCustomisation['used']))
        	{
        		$name = LocalizationObj::getLocaleString($calendarCustomisation['componentname'], $orderLineArray['browserlanguagecode'], true);
        		$totalSell = self::formatOrderCurrencyNumber($calendarCustomisation['totalsell'], $pStage);

				$miscOrderItemData['calendarcustomisations'][] = array('name' => $name, 'formattedtotalsell' => $totalSell);
			}
		}

		if (!empty($itemAIComponent))
		{
			$name = LocalizationObj::getLocaleString($itemAIComponent['name'], $orderLineArray['browserlanguagecode'], true);
			$totalSell = self::formatOrderCurrencyNumber($itemAIComponent['totalsell'], $pStage);
			$AIComponentPreview = "";
			$AIComponentInfo = "";
			$componentPreview = UtilsObj::getAssetRequest($itemAIComponent['code'], 'components');

			// if an image has not been set for the AI component use a default image
			if ($componentPreview !== '')
			{
				$AIComponentPreview = $componentPreview;
			}
			else
			{
				$AIComponentPreview = UtilsObj::getBrandedWebUrl() . "images/shopping-cart/AI-thumbnail.png";
			}

			if ($itemAIComponent['info'] == '')
			{
				$AIComponentInfo = $pSmarty->get_config_vars('str_LabelDefaultAIComponentDescription');
			}
			else
			{
				$AIComponentInfo = LocalizationObj::getLocaleString($itemAIComponent['info'], $orderLineArray['browserlanguagecode'], true);
			}

			$miscOrderItemData['aicomponent'] = array('name' => $name, 'formattedtotalsell' => $totalSell, 'previewsrc' => $AIComponentPreview, 'componentinfo' => $AIComponentInfo);
		}

		if (array_key_exists('key', $ItemPictures))
		{
			$pictureCount = count($ItemPictures['key']);

			for ($i = 0; $i < $pictureCount; $i++)
			{
				$pictureLookup = $ItemPictures['key'][$i];
				$uniqueLookup = $pictureLookup . TPX_PICTURES_LOOKUP_SEPERATOR . $i;
				$picture = $ItemPictures['data'][$pictureLookup];
				$printData = &$ItemPictures['printdata'][$uniqueLookup];

				// If the totalsell is a float then it hasn't been formatted yet.
				// Need to check as it is modified by reference and we may try to format it multiple times.
				if (is_float($printData['ts']))
				{
					if ($picture['subcode'] != '')
					{
						$printData['ts'] = $printData['ts'] + $printData['subts'];
					}

					$printData['ts'] = self::formatOrderCurrencyNumber($printData['ts'], $pStage);
				}
			}
		}
		else
		{
			$pictureCount = count($ItemPictures);
		}

        if ($pictureCount == 0)
        {
        	$miscOrderItemData['displayassets'] = true;
            $miscOrderItemData['totalprice'] = 0;

        	$assetCount = count($ItemExternalAssets);

			for ($i = 0; $i < $assetCount; $i++)
			{
				$ItemExternalAssets[$i]['name'] = LocalizationObj::getLocaleString($ItemExternalAssets[$i]['name'], $orderLineArray['browserlanguagecode'], true);
				$ItemExternalAssets[$i]['pagename'] = LocalizationObj::getLocaleString($ItemExternalAssets[$i]['pagename'], $orderLineArray['browserlanguagecode'], true);
				$ItemExternalAssets[$i]['charge'] = self::formatOrderCurrencyNumber($ItemExternalAssets[$i]['totalsell'], $pStage);
                $miscOrderItemData['totalprice'] += $ItemExternalAssets[$i]['totalsell'];
			}

            $miscOrderItemData['totalprice'] = self::formatOrderCurrencyNumber($miscOrderItemData['totalprice'], $pStage);

        }
        else
        {
        	$miscOrderItemData['displayassets'] = false;
            $miscOrderItemData['totalprice'] = '';
        }

        $miscOrderItemData['itemexternalassets'] = $ItemExternalAssets;

        // if the voucher qty is locked and the min & max are equal then prevent the user from changing the qty
        if (($orderLineArray['order']['voucherlockqty'] == 1) && ($orderLineArray['order']['voucherminqty'] == $orderLineArray['order']['vouchermaxqty']))
        {
            $miscOrderItemData['lockqty'] = true;
        }
        else
        {
            $miscOrderItemData['lockqty'] = false;
        }

        if ($pStage == 'qty')
        {
            $miscOrderItemData['itemtotal'] = $itemTotalSell;
        }
        else
        {
            if (($differentTaxRates == true) && ($orderLineArray['order']['showpriceswithtax'] == 0))
            {
                $miscOrderItemData['itemtotal'] = $itemTotalSell + $itemTaxTotal;
                $miscOrderItemData['differenttaxrates'] = true;
				$miscOrderItemData['itemtaxrate'] = UtilsObj::formatCurrencyNumber($ItemTaxRate, 2);
				$miscOrderItemData['itemtaxratename'] = LocalizationObj::getLocaleString($itemTaxName, $orderLineArray['browserlanguagecode'], true);
				$miscOrderItemData['itemtaxtotal'] = $itemTaxTotal;
            }
            else
            {
                $miscOrderItemData['itemtotal'] = $itemTotalSell;
                $miscOrderItemData['differenttaxrates'] = false;
                $miscOrderItemData['itemtaxratename'] = LocalizationObj::getLocaleString($itemTaxName, $orderLineArray['browserlanguagecode'], true);
            }
        }

        if ($itemHasProductPrice == 1)
        {
            $miscOrderItemData['itemtextstyle'] = '';
            $miscOrderItemData['hasproductprice'] = true;
            $miscOrderItemData['itemsubtotal'] = self::formatOrderCurrencyNumber($itemSubTotal, $pStage);

			$miscOrderItemData['itemdiscountname'] = '';
            $miscOrderItemData['itemdiscountvalue'] = '';
            $miscOrderItemData['itemdiscountedvalue'] = '';
            $miscOrderItemData['itemdiscountvalueraw'] = 0;

			if (($orderLineArray['order']['vouchertype'] == TPX_VOUCHER_TYPE_SCRIPT) && ($orderLineArray['order']['voucherdiscountsection'] == 'PRODUCT'))
			{
				if ($orderLineArray['order']['itemsdiscounted'][$pOrderLine])
				{
	                $miscOrderItemData['itemvoucherapplied'] = $itemVoucherApplied;

	                $voucherName = $orderLineArray['items'][$pOrderLine]['itemvouchername'];

	                if ($voucherName=='')
	                {
	                	$voucherName = $orderLineArray['order']['vouchername'];
	                }

	                $miscOrderItemData['itemdiscountname'] = UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($voucherName, $orderLineArray['browserlanguagecode'], true));

                    $miscOrderItemData['itemdiscountvalueraw'] = $itemDiscountValue;
	                $miscOrderItemData['itemdiscountvalue'] = self::formatOrderCurrencyNumber(($itemDiscountValue * -1), $pStage);
	                $miscOrderItemData['itemdiscountedvalue'] = self::formatOrderCurrencyNumber($itemTotalSell, $pStage);
				}
			}
			elseif (($orderLineArray['order']['voucherdiscountsection'] == 'PRODUCT') || ($differentTaxRates == true) || ($applyDiscountToSingleLineItem == true))
            {
                $miscOrderItemData['itemdiscountvalueraw'] = $itemDiscountValue;
                $miscOrderItemData['itemvoucherapplied'] = $itemVoucherApplied;
                $miscOrderItemData['itemdiscountname'] = UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($orderLineArray['order']['vouchername'], $orderLineArray['browserlanguagecode'], true));
                $miscOrderItemData['itemdiscountvalue'] = self::formatOrderCurrencyNumber(($itemDiscountValue * -1), $pStage);
                $miscOrderItemData['itemdiscountedvalue'] = self::formatOrderCurrencyNumber($itemTotalSell, $pStage);
            }
        }
        else
        {
            $miscOrderItemData['hasproductprice'] = false;
            $miscOrderItemData['itemsubtotal'] = $strLabelNotAvailable;
            $miscOrderItemData['itemtextstyle'] = 'itempricenotavailable';
            $miscOrderItemData['itemcompletevaluetextstyle'] = 'itempricenotavailable';
            $miscOrderItemData['itemunitsell'] = $strLabelNotAvailable;
            $miscOrderItemData['itemdiscountname'] = '';
            $miscOrderItemData['itemdiscountvalue'] = $strLabelNotAvailable;
            $miscOrderItemData['itemdiscountedvalue'] = $strLabelNotAvailable;
            $miscOrderItemData['itemvoucherapplied'] = 0;
            $miscOrderItemData['itemdiscountvalueraw'] = 0;
        }

		$miscOrderItemData['hasproductprice'] = ($miscOrderItemData['hasproductprice'] ? 1 : 0);

		// prepare single prints, if it is a single prints project
		$miscOrderItemData['itempictures'] = array();

        if (($itemProductType == TPX_PRODUCTCOLLECTIONTYPE_SINGLEPRINTS) && ($pOrderLine != TPX_ORDERFOOTER_ID))
        {
			$miscOrderItemData['itempictures'] = self::prepareSinglePrints($ItemPictures, $pOrderLine, $strLabelNotAvailable, $pStage);
		}

		// prepare checkboxes of order line root
		$miscOrderItemData['checkboxes'] = self::prepareCheckboxes($itemCheckBoxes, $orderItemLineID, $strLabelNotAvailable, $strLabelAdd, $strLabelRemove, $pStage);

		// prepare checkboxes in LINEFOOTER
		$miscOrderItemData['linefootercheckboxes'] = self::prepareCheckboxes($lineFooterCheckboxes, $orderItemLineID, $strLabelNotAvailable, $strLabelAdd, $strLabelRemove, $pStage);

		$miscOrderItemData['sections'] = self::prepareSections($itemSections, $orderItemLineID, $pStage, $strLabelNotAvailable, $strLabelChange, $strLabelAdd, $strLabelRemove);

		$miscOrderItemData['linefootersections'] = self::prepareSections($lineFooterSections, $orderItemLineID, $pStage, $strLabelNotAvailable, $strLabelChange, $strLabelAdd, $strLabelRemove);

		if ($showItemTotalRow == true)
		{
			$miscOrderItemData['showitemtotalrow'] = true;
			$miscOrderItemData['itemcompletetotal'] = $miscOrderItemData['itemsubtotal'];
		}
		else
		{
			$miscOrderItemData['showitemtotalrow'] = false;
			$miscOrderItemData['itemcompletetotal'] = $miscOrderItemData['itemsubtotal'];
		}

		// we assign variable here because we later need these for a label
		$formattedItemTaxName = LocalizationObj::getLocaleString($itemTaxName, $orderLineArray['browserlanguagecode'], true);
		$formattedItemTaxRate = UtilsObj::formatCurrencyNumber($ItemTaxRate, 2);
		$formattedItemTaxTotal = self::formatOrderCurrencyNumber($itemTaxTotal, $pStage);
		// for replacing amount in web string
		$formattedItemTaxTotal2 = self::formatOrderCurrencyNumber($itemTaxTotal, $pStage);

		$miscOrderItemData['itemtaxcode'] = $itemTaxCode;
		$miscOrderItemData['itemtaxname'] = $formattedItemTaxName;
		$miscOrderItemData['itemtaxrate'] = $formattedItemTaxRate;
		$miscOrderItemData['itemtaxtotal'] = $formattedItemTaxTotal;
		$miscOrderItemData['itemtaxtotalraw'] = $itemTaxTotal;
		$miscOrderItemData['itemtotal'] = self::formatOrderCurrencyNumber($miscOrderItemData['itemtotal'], $pStage);
        $miscOrderItemData['itemproducttype'] = $itemProductType;
		$miscOrderItemData['itempagecount'] = $itemPageCount;


		if ($showTaxBreakdown && $differentTaxRates == true)
		{
			if ($orderLineArray['items'][0]['itemtaxtotal'] != 0 || $showZeroTax)
			{
				$showItemTax = true;
			}

			if ($orderLineArray['shipping'][0]['shippingratetaxtotal'] != 0 || $showZeroTax)
			{
				$showShippingTax = true;
			}
		}
		$miscOrderItemData['showitemtax'] = $showItemTax;
		$miscOrderItemData['showshippingtax'] = $showShippingTax;


		if (($showItemTax == true && $showPricesWithTax==false) || ($itemVoucherApplied && ($applyDiscountToSingleLineItem == true)) || ((($orderLineArray['order']['voucherdiscountsection'] == 'PRODUCT')  || (($orderLineArray['order']['voucherdiscountsection'] == 'TOTAL') && ($differentTaxRates == true))) &&
		        ($orderLineArray['order']['voucheractive'] == 1)))
		{
			$itemSubTotalName = 'str_LabelItemSubTotal';
		}
		else
		{
			if ($miscOrderItemData['parentorderitemid'] == 0)
			{
				$itemSubTotalName = 'str_LabelOrderItemListItemTotal';
			}
			else
			{
				$itemSubTotalName = 'str_LabelOrderCompanionItemListItemTotal';
			}
		}

		$miscOrderItemData['itemsubtotalname'] = $pSmarty->get_config_vars($itemSubTotalName);
		$miscOrderItemData['itemtotalsell'] = $miscOrderItemData['itemsubtotal'];

		//Email Content, added $pStage to display the same content as the payment page
		if (($pStage == 'payment') || ($pStage == 'email'))
		{
			// this should not be neccessary, but we do it to be on the safe side
			if ($showPricesWithTax == false)
			{
				$showTaxBreakdown = true;
			}
			// determine if we show the tax total
			// $showTaxTotal is initialised to false
			if ($showTaxBreakdown)
			{
				if ($differentTaxRates)
				{
					if ($showAlwaysTaxTotal)
					{
						$showTaxTotal = true;
					}
				}
				else
				{
					if ($showPricesWithTax)
					{
						$showTaxTotal = true;
					}
				}
			}

			$miscOrderItemData['showtaxtotal'] = $showTaxTotal;

			if ($showPricesWithTax && $showTaxBreakdown)
			{

				if ($differentTaxRates)
				{
					// item tax
					$includesItemTaxText = $pSmarty->get_config_vars('str_LabelIncludesTax');
					$includesItemTaxText = str_replace('^0', $formattedItemTaxRate, $includesItemTaxText);
					$includesItemTaxText = str_replace('^1', $formattedItemTaxName, $includesItemTaxText);
					$includesItemTaxText = str_replace('^2', $formattedItemTaxTotal2, $includesItemTaxText);
					$miscOrderItemData['includesitemtaxtext'] = $includesItemTaxText;

					// shipping tax
					$includesShippingTaxText = $pSmarty->get_config_vars('str_LabelIncludesTax');
					$includesShippingTaxText = str_replace('^0', $formattedShippingTaxRate, $includesShippingTaxText);
					$includesShippingTaxText = str_replace('^1', $formattedShippingTaxName, $includesShippingTaxText);
					$includesShippingTaxText = str_replace('^2', $formattedShippingTaxTotal2, $includesShippingTaxText);
					$miscOrderItemData['includesshippingtaxtext'] = $includesShippingTaxText;
				}
			}

			if ($showTaxTotal)
			{
				// tax total
				if ($formattedItemTaxName == $formattedShippingTaxName)
				{
					if ($showPricesWithTax)
					{
						$includesTaxTotalText = $pSmarty->get_config_vars('str_LabelIncludesTax');
						$includesTaxTotalText = str_replace('^0', $formattedShippingTaxRate, $includesTaxTotalText);
						$includesTaxTotalText = str_replace('^1', $formattedShippingTaxName, $includesTaxTotalText);
						$includesTaxTotalText = str_replace('^2', $formattedOrderTotalTax2, $includesTaxTotalText);
						$miscOrderItemData['includestaxtotaltext'] = $includesTaxTotalText;
					}
				}
				else
				{
					$includesTaxTotalText = $pSmarty->get_config_vars('str_LabelIncludesTaxTotal');
					$includesTaxTotalText = str_replace('^0', $formattedOrderTotalTax2, $includesTaxTotalText);
					$miscOrderItemData['includestaxtotaltext'] = $includesTaxTotalText;
				}
			}
		}

   		return $miscOrderItemData;
	}


    static function displayJobTicket($pResultArray, $pCurrentUserStage, $pCache, $pDisplay, $pRefreshMainContent, $pForceReloadJs, $pTemplateDisplayStage, $pRemoveStore = 'false')
	{
        global $gSession;
		global $gConstants;

		if ($pCurrentUserStage == 'email')
		{
			$orderID = $pResultArray['orderid'];
			$orderDetailsArray = $pResultArray;
		}
		else
		{
			// get the language from the browser
			$browserLocale = UtilsObj::getBrowserLocale();
			if ($browserLocale != '')
			{
				$gSession['browserlanguagecode'] = $browserLocale;
			}

			$orderDetailsArray = $gSession;
		}

        $titlePrefix = '';
		$paymentGatewayJavascriptArray = array();

		$showItemTax = false;
		$showShippingTax = false;
		$showTaxTotal = false;
		$differentTaxRates = false;
		$showPricesWithTax = ($orderDetailsArray['order']['showpriceswithtax'] == 1) ? true : false;
		$showTaxBreakdown = ($orderDetailsArray['order']['showtaxbreakdown'] == 1) ? true : false;
		$showZeroTax = ($orderDetailsArray['order']['showzerotax'] == 1) ? true : false;
		$showAlwaysTaxTotal = ($orderDetailsArray['order']['showalwaystaxtotal'] == 1) ? true : false;
		$formattedShippingTaxRate = '';
		$formattedShippingTaxName = '';
		$paymentMethodLabelForEmail = '';
		$miscOrderItemDataArray = Array();
		$forceApplyDiscountToLineItems = false;
		$includesShippingTaxText = '';

        // override the voucher type to display the discount on each line
        $applyDiscountToSingleLineItem = (($orderDetailsArray['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_LOWEST_PRICED) ||
                                          ($orderDetailsArray['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_HIGHEST_PRICED));

        // check if the total voucher should be applied to each line (similar to different tax rates)
        if (($gSession['order']['voucherdiscountsection'] == 'TOTAL') &&
            (($gSession['order']['ordertotalitemdiscountable'] > $gSession['order']['voucherapplytoqty']) || ($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER)))
        {
            $forceApplyDiscountToLineItems = true;
        }

        // set the cart and calculations to be applied to lines, not totals
        $applyDiscountToSingleLineItem = ($applyDiscountToSingleLineItem || $forceApplyDiscountToLineItems);

        $ignoreVoucherTypes = Array();

        //$ignoreVoucherTypes[] = 'VALUE';
        $ignoreVoucherTypes[] = 'VALUESET';
        //$ignoreVoucherTypes[] = 'PERCENT';

        $formatDP = $orderDetailsArray['order']['currencydecimalplaces'];

        //Case 2847: See this case for the reason why this has been added
		$smartyCust = SmartyObj::newSmarty('Customer', $orderDetailsArray['webbrandcode'], $orderDetailsArray['webbrandapplicationname'], $orderDetailsArray['browserlanguagecode']);

        $smarty = SmartyObj::newSmarty('Order', $orderDetailsArray['webbrandcode'], $orderDetailsArray['webbrandapplicationname'], $orderDetailsArray['browserlanguagecode']);

        $smarty->assign('noselectmetadatamessage', $smartyCust->get_config_vars('str_MessagePleaseSelectAnOption'));
        $smarty->assign('applyVoucherAsLineDiscount', $applyDiscountToSingleLineItem);

        if ($orderDetailsArray['webbrandcode']!='')
        {
        	$smarty->assign('roottoextjs', '../../');
        }
        else
        {
        	$smarty->assign('roottoextjs', '');
        }

        // include the system language selector
		if ($gSession['ismobile'] == true)
        {
            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), true);
            $smarty->assign('systemlanguagelist', $languageHTMLList);
        }
        else
        {
            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), false);
            $smarty->assign('systemlanguagelist', $languageHTMLList);
        }

		$smarty->assign('defaultdiscountactive', $orderDetailsArray['order']['defaultdiscountactive']);

        $smarty->assign('custominit', $pResultArray['custominit']);
        $smarty->assign('currencycode', $orderDetailsArray['order']['currencycode']);
        $smarty->assign('currencyname', LocalizationObj::getLocaleString($orderDetailsArray['order']['currencyname'], $orderDetailsArray['browserlanguagecode'], true));

		$smarty->assign('specialvouchertype', in_array($orderDetailsArray['order']['voucherdiscounttype'],$ignoreVoucherTypes));

        if ($orderDetailsArray['sessionrevived'] == 1)
        {
            $titlePrefix = $smarty->get_config_vars('str_LabelPayLaterRef') . ': ' . $orderDetailsArray['order']['tempordernumber'] . ' - ';
        }

		// loop around order items
		$orderItemsCount = count($orderDetailsArray['items']);

		for ($i = 0; $i < $orderItemsCount; $i++)
		{
			// Added Stage to display the same content as the payment page
	        if (($pCurrentUserStage == 'companionselection') || ($pCurrentUserStage == 'qty') || ($pCurrentUserStage == 'payment') || ($pCurrentUserStage == 'email'))
	        {
				$miscOrderItemDataArray[$i] = self::displayOrderLineJobTicket($pResultArray, $pCurrentUserStage, $i, $i, $smarty);
				$miscOrderItemDataArray[$i]['isfirstline'] = 0;
				if($i == 0)
				{
					$miscOrderItemDataArray[$i]['isfirstline'] = 1;
				}
	        }

			if ($orderDetailsArray['order']['orderalltaxratesequal'] == 0)
			{
				$differentTaxRates = true;
			}
        }


		$orderCanContinue = true;

		if ($pCurrentUserStage == 'qty')
        {
			// we need to process every order line item and its components to check to see if everycomponent has a price available.
			// if not then the order cannot proceed.
			$miscOrderItemDataArrayCount = count($miscOrderItemDataArray);

			for ($i = 0; $i < $orderItemsCount; $i++)
			{
				if ($orderCanContinue)
				{
					self::orderCanContinue($miscOrderItemDataArray[$i]['itempictures'], $orderCanContinue, false);
				}

				if ($orderCanContinue)
				{
					self::orderCanContinue($miscOrderItemDataArray[$i]['checkboxes'], $orderCanContinue, false);
				}

				if ($orderCanContinue)
				{
					self::orderCanContinue($miscOrderItemDataArray[$i]['sections'], $orderCanContinue, true);
				}

				if ($orderCanContinue)
				{
					self::orderCanContinue($miscOrderItemDataArray[$i]['linefootercheckboxes'], $orderCanContinue, false);
				}

				if ($orderCanContinue)
				{
					self::orderCanContinue($miscOrderItemDataArray[$i]['linefootersections'], $orderCanContinue, true);
				}


				// if we have flagged that the order cannot continue do not process any other line items
				if (! $orderCanContinue)
				{
					break;
				}
			}

			if ($orderCanContinue)
			{
				self::orderCanContinue($orderDetailsArray['order']['orderFooterCheckboxes'], $orderCanContinue, false);

				if ($orderCanContinue)
				{
					self::orderCanContinue($orderDetailsArray['order']['orderFooterSections'], $orderCanContinue, true);
				}
			}
        }

        if ($orderCanContinue)
		{
			$smarty->assign('ordercancontinue', 1);
		}
		else
		{
			$smarty->assign('ordercancontinue', 0);
		}

        // pass orderFooterSections and orderFooterCheckboxes to template
		$strLabelNotAvailable = $smarty->get_config_vars('str_LabelNotAvailable');
		$strLabelRemove = $smarty->get_config_vars('str_LabelRemove');
		$strLabelAdd = $smarty->get_config_vars('str_LabelAdd');
		$strLabelChange = $smarty->get_config_vars('str_LabelChange');

		if ($showTaxBreakdown && $differentTaxRates == true)
		{
			if ($orderDetailsArray['items'][0]['itemtaxtotal'] != 0 || $showZeroTax)
			{
				$showItemTax = true;
			}

			if ($orderDetailsArray['shipping'][0]['shippingratetaxtotal'] != 0 || $showZeroTax)
			{
				$showShippingTax = true;
			}
		}

        // calculate order footer totals
        Order_model::updateOrderFooterTaxRate();
        $orderfootertaxratesequal = $orderDetailsArray['order']['orderfootertaxratesequal'];
        $smarty->assign('footertaxratesequal', $orderfootertaxratesequal);

        $orderFooterTaxTotal = ($orderDetailsArray['order']['orderfootertotalwithtax'] - $orderDetailsArray['order']['orderfootertotalnotax']);
        $orderFooterTaxTotal = UtilsObj::bround($orderFooterTaxTotal, $orderDetailsArray['order']['currencydecimalplaces']);

        //if (($showItemTax == true && $showPricesWithTax==false) || ((($orderDetailsArray['order']['voucherdiscountsection'] == 'TOTAL') && ($differentTaxRates == true)) && ($orderDetailsArray['order']['voucheractive'] == 1)))
        if (($showItemTax == true && $showPricesWithTax==false) ||
            ((($orderDetailsArray['order']['voucherdiscountsection'] == 'TOTAL') && (($differentTaxRates == true) || ($applyDiscountToSingleLineItem))) && ($orderDetailsArray['order']['voucheractive'] == 1)) ||
            (($showPricesWithTax == false) && ($differentTaxRates == true) && (($showZeroTax == true) || (($showZeroTax == false) && ($orderFooterTaxTotal > 0)))))
		{
			$orderFooterSubTotalName = 'str_LabelOrderFooterSubTotal';
		}
		else
		{
			$orderFooterSubTotalName = 'str_LabelOrderFooterTotal';
		}

		$smarty->assign('orderfootersubtotalname',$smarty->get_config_vars($orderFooterSubTotalName));
		$smarty->assign('hasorderfooter', $orderDetailsArray['order']['orderfootersubtotal']);

        if (count($orderDetailsArray['order']['orderFooterSections']) > 0)
        {
            $orderFooterTaxRate = $orderDetailsArray['order']['orderFooterSections'][0]['orderfootertaxrate'];
            $orderFooterTaxName = $orderDetailsArray['order']['orderFooterSections'][0]['orderfootertaxname'];
        }
        else
        {
            $orderFooterTaxRate = 0;
            $orderFooterTaxName = '';
        }

        if (count($orderDetailsArray['order']['orderFooterCheckboxes']) > 0)
        {
            $orderFooterTaxRate = $orderDetailsArray['order']['orderFooterCheckboxes'][0]['orderfootertaxrate'];
            $orderFooterTaxName = $orderDetailsArray['order']['orderFooterCheckboxes'][0]['orderfootertaxname'];
        }
        else
        {
            $orderFooterTaxRate = 0;
            $orderFooterTaxName = '';
        }

        $formattedOrderFooterTaxTotal = self::formatOrderCurrencyNumber($orderFooterTaxTotal, $pCurrentUserStage);
        $formattedOrderFooterTaxRate = UtilsObj::formatCurrencyNumber($orderFooterTaxRate, 2);
        $formattedOrderFooterTaxName = LocalizationObj::getLocaleString($orderFooterTaxName, $orderDetailsArray['browserlanguagecode'], true);

        $smarty->assign('orderfootertaxtotalraw', $orderFooterTaxTotal);
        $smarty->assign('orderfootertaxtotal', $formattedOrderFooterTaxTotal);
        $smarty->assign('orderfootertaxrate', $formattedOrderFooterTaxRate);

        if ($orderfootertaxratesequal == 0)
        {
            $formattedOrderFooterTaxName = $smarty->get_config_vars('str_LabelOrderTax');
            $smarty->assign('orderfootertaxname', $formattedOrderFooterTaxName);
        }
        else
        {
            $formattedOrderFooterTaxName = LocalizationObj::getLocaleString($orderFooterTaxName, $orderDetailsArray['browserlanguagecode'], true);
            $smarty->assign('orderfootertaxname', $formattedOrderFooterTaxName);
        }

		$smarty->assign('orderfooterdiscountapplied', self::formatOrderCurrencyNumber($orderDetailsArray['order']['orderfootertotal'] - $orderDetailsArray['order']['orderfootersubtotal'], $pCurrentUserStage));
		$smarty->assign('orderfootersubtotal', self::formatOrderCurrencyNumber($orderDetailsArray['order']['orderfootersubtotal'], $pCurrentUserStage));
		$smarty->assign('orderfootertotalname',$smarty->get_config_vars('str_LabelOrderFooterTotal'));
		$smarty->assign('orderfootertotal', self::formatOrderCurrencyNumber($orderDetailsArray['order']['orderfootertotal'], $pCurrentUserStage));
		$smarty->assign('orderfooterdiscountedvalue', self::formatOrderCurrencyNumber($orderDetailsArray['order']['orderfooterdiscountvalue'], $pCurrentUserStage));
		$smarty->assign('showpricewithtax', $showPricesWithTax);
		$smarty->assign('showitemtax', $showItemTax);


		$orderFooterSections = self::prepareSections($orderDetailsArray['order']['orderFooterSections'], -1, $pCurrentUserStage, $strLabelNotAvailable, $strLabelChange, $strLabelAdd, $strLabelRemove);
        $smarty->assign('orderfootersections', $orderFooterSections);

        $orderFooterCheckboxes = self::prepareCheckboxes($orderDetailsArray['order']['orderFooterCheckboxes'], -1, $strLabelNotAvailable, $strLabelAdd, $strLabelRemove, $pCurrentUserStage);
		$smarty->assign('orderfootercheckboxes', $orderFooterCheckboxes);

		// number of order items
        $smarty->assign('orderitemscount', $orderItemsCount);

		if ($showTaxBreakdown && $differentTaxRates == true)
		{
			if ($orderDetailsArray['shipping'][0]['shippingratetaxtotal'] != 0 || $showZeroTax)
			{
				$showShippingTax = true;
			}
		}

		$formattedShippingTaxRate = UtilsObj::formatCurrencyNumber($orderDetailsArray['shipping'][0]['shippingratetaxrate'], 2);
		$formattedShippingTaxName = LocalizationObj::getLocaleString($orderDetailsArray['shipping'][0]['shippingratetaxname'], $orderDetailsArray['browserlanguagecode'], true);
		$formattedShippingTaxTotal = self::formatOrderCurrencyNumber($orderDetailsArray['shipping'][0]['shippingratetaxtotal'], $pCurrentUserStage);

		// for replacing amount in web string
		$formattedShippingTaxTotal2 = UtilsObj::formatCurrencyNumber($orderDetailsArray['shipping'][0]['shippingratetaxtotal'], $formatDP,
		$orderDetailsArray['browserlanguagecode'], $orderDetailsArray['order']['currencysymbol'], $orderDetailsArray['order']['currencysymbolatfront']);

        $smarty->assign('shippingtaxname', $formattedShippingTaxName);
        $smarty->assign('shippingtaxrate', $formattedShippingTaxRate);
        $smarty->assign('shippingtaxtotal', $formattedShippingTaxTotal);

        // display the correct shipping values
        //if(($showPricesWithTax) && ($showAlwaysTaxTotal == false))
        if($showPricesWithTax)
        {
            $smarty->assign('shippingtotal', self::formatOrderCurrencyNumber($orderDetailsArray['shipping'][0]['shippingratetotalsell'], $pCurrentUserStage));
        }
        else
        {
            $smarty->assign('shippingtotal', self::formatOrderCurrencyNumber($orderDetailsArray['shipping'][0]['shippingratetotalsell'] + $orderDetailsArray['shipping'][0]['shippingratetaxtotal'], $pCurrentUserStage));
	    }

        if ($orderDetailsArray['order']['showpriceswithtax'] == 0)
        {
            $smarty->assign('ordertotalshipping', self::formatOrderCurrencyNumber($orderDetailsArray['order']['ordertotalshippingsellbeforediscount'], $pCurrentUserStage));
        }
        else
        {
            $smarty->assign('ordertotalshipping', self::formatOrderCurrencyNumber($orderDetailsArray['shipping'][0]['shippingratesell'], $pCurrentUserStage));
        }

        if ($orderDetailsArray['order']['voucherdiscountsection'] == 'TOTAL')// && ($applyDiscountToSingleLineItem == false))
        {
            $smarty->assign('orderbeforediscounttotalvalue', self::formatOrderCurrencyNumber($orderDetailsArray['order']['ordertotalbeforediscount'], $pCurrentUserStage));
            $smarty->assign('orderaftertotaldiscountname', UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($orderDetailsArray['order']['vouchername'], $orderDetailsArray['browserlanguagecode'], true)));
            $smarty->assign('ordertotaldiscountvalue', self::formatOrderCurrencyNumber($orderDetailsArray['order']['ordertotaldiscount'] * -1, $pCurrentUserStage));
        }

        // is total tax more than zero
        if ($orderDetailsArray['order']['ordertotaltax'] == 0)
        {
			$smarty->assign('hastotaltax', false);
        }
        else
        {
			$smarty->assign('hastotaltax', true);
        }

        $formattedOrderTotalTax = self::formatOrderCurrencyNumber($orderDetailsArray['order']['ordertotaltax'], $pCurrentUserStage);
		// for replacing amount in web string
        $formattedOrderTotalTax2 = UtilsObj::formatCurrencyNumber($orderDetailsArray['order']['ordertotaltax'], $formatDP,
		$orderDetailsArray['browserlanguagecode'], $orderDetailsArray['order']['currencysymbol'], $orderDetailsArray['order']['currencysymbolatfront']);
        $smarty->assign('ordersubtotal', self::formatOrderCurrencyNumber($orderDetailsArray['order']['ordertotalsell'], $pCurrentUserStage));

        $smarty->assign('ordertotaltax', $formattedOrderTotalTax);
        $smarty->assign('itemtaxname',  LocalizationObj::getLocaleString($orderDetailsArray['items'][0]['itemtaxname'], $orderDetailsArray['browserlanguagecode'], true));
        $smarty->assign('itemtaxrate', UtilsObj::formatCurrencyNumber($orderDetailsArray['items'][0]['itemtaxrate'], 2));

        if (($showPricesWithTax == true) && (($pCurrentUserStage == 'qty') || ($pCurrentUserStage =='shipping') || ($pCurrentUserStage == 'companionselection') || ($pCurrentUserStage == 'payment')))
		{
            $itemTotal = $orderDetailsArray['order']['ordertotalitemsellwithtaxnodiscount'];
			$shippingTotal = $orderDetailsArray['shipping'][0]['shippingratesellwithtax'];
			$orderTotal = $orderDetailsArray['order']['ordertotalbeforediscount'];
            $orderTotal = $itemTotal + $shippingTotal;
		}
		else if (($showPricesWithTax == false) && (($pCurrentUserStage == 'qty') || ($pCurrentUserStage =='shipping') || ($pCurrentUserStage == 'companionselection') || ($pCurrentUserStage == 'payment')))
		{
			$itemTotal = $orderDetailsArray['order']['ordertotalitemsellnotaxnodiscount'];
			$shippingTotal = $orderDetailsArray['shipping'][0]['shippingratesellnotax'];
			$orderTotal = $orderDetailsArray['order']['ordertotalbeforediscount'];
			$orderTotal = $itemTotal + $shippingTotal;
		}
		else
		{
			$itemTotal = $orderDetailsArray['order']['ordertotalitemsellwithtax'];
            $shippingTotal = $orderDetailsArray['shipping'][0]['shippingratetotalsellwithtax'];
            $orderTotal = $orderDetailsArray['order']['ordertotal'];
		}
		
        $smarty->assign('orderitemstotalsell', self::formatOrderCurrencyNumber($itemTotal, $pCurrentUserStage));
        $smarty->assign('orderfooteritemstotalsell', self::formatOrderCurrencyNumber($orderDetailsArray['order']['orderfootertotalnotaxnodiscount'], $pCurrentUserStage));
		$smarty->assign('ordershippingcost', self::formatOrderCurrencyNumber($shippingTotal, $pCurrentUserStage));
        $smarty->assign('ordertotal', self::formatOrderCurrencyNumber($orderTotal, $pCurrentUserStage));

		$smarty->assign('ordergiftcardtotal', $orderDetailsArray['order']['ordergiftcardtotal']);

        if ($orderDetailsArray['ordergiftcarddeleted'])
        {
            $smarty->assign('tooltipGiftcardButton',$smarty->get_config_vars('str_TooltipAddGiftcard'));
            $smarty->assign('add_delete_giftcard', 'add');
            $smarty->assign('disabled_giftcard', 'disabled');
        }
        else
        {
            $smarty->assign('tooltipGiftcardButton',$smarty->get_config_vars('str_TooltipDeleteGiftcard'));
            $smarty->assign('add_delete_giftcard', 'delete');
            $smarty->assign('disabled_giftcard', '');
        }

        $smarty->assign('ordergiftcardtotalvalue', self::formatOrderCurrencyNumber((0 - $orderDetailsArray['order']['ordergiftcardtotal']), $pCurrentUserStage));
        if (($pCurrentUserStage == 'qty') || ($pCurrentUserStage == 'shipping') || ($orderDetailsArray['ordergiftcarddeleted']))
        {
            $smarty->assign('giftcardbalance', self::formatOrderCurrencyNumber($orderDetailsArray['usergiftcardbalance'], $pCurrentUserStage));
        }
        else
        {
            $smarty->assign('giftcardbalance', self::formatOrderCurrencyNumber(($orderDetailsArray['usergiftcardbalance'] - $orderDetailsArray['order']['ordergiftcardtotal']), $pCurrentUserStage));
        }
        $smarty->assign('ordertotaltopayvalue', self::formatOrderCurrencyNumber($orderDetailsArray['order']['ordertotaltopay'], $pCurrentUserStage));

        $shippingRateCode = $orderDetailsArray['shipping'][0]['shippingratecode'];

		$paymentMethodCode = $orderDetailsArray['order']['paymentmethodcode'];

        $canUseAccount = false;

		if ($pCurrentUserStage == 'companionselection')
		{
		    $smarty->assign('title', $titlePrefix . $smarty->get_config_vars('str_TitleOrderStageCompanionSelection'));
		    $smarty->assign('ordertitle', $smarty->get_config_vars('str_HeadingOrderStageCompanionSelection'));
		}
		else if ($pCurrentUserStage == 'qty')
		{
		    $smarty->assign('title', $titlePrefix . $smarty->get_config_vars('str_TitleOrderStageQty'));
		    $smarty->assign('ordertitle', $smarty->get_config_vars('str_HeadingOrderStageQty'));
		}
		else if ($pCurrentUserStage == 'shipping')
		{
		    $smarty->assign('title', $titlePrefix . $smarty->get_config_vars('str_TitleOrderStageShipping'));
		    $smarty->assign('ordertitle', $smarty->get_config_vars('str_HeadingOrderStageShipping'));

		    if($orderDetailsArray['shipping'][0]['shippingmethodcode'] != '')
            {
		        $smarty->assign('storeisfixed', $orderDetailsArray['shipping'][0]['shippingMethods'][$orderDetailsArray['shipping'][0]['shippingmethodcode']]['storeIsFixed']);
		    }
            else
            {
                $smarty->assign('storeisfixed', 0);
            }

			// build the list of shipping methods / rates
			$initialShippingStoreAddressLabel = $smarty->get_config_vars('str_LabelShippingAddress');
			$initialShippingStoreAddress = $pResultArray['formattedshippingaddress'];
			$collectFromStore = 0;
			$shippingRateCode = '';
			$shippingMethodsHTML = '';
			$formattedStoreAddressList = Array();
			$storeFixedList = Array();
			$storeCodeList = Array();
            $shippingMethodSelectedLabel = '';
            $shippingMethodCFSCode = '';
            $shippingSelectedRate = '';
			$shippingRatesList = $pResultArray['shippingrates'];
            $classSelected = '';

            // Check for external store locator script.
            $ajaxCommand = 'STORELOCATOR';

            UtilsObj::includeStoreLocatorScript();

            if (method_exists('EDL_StoreLocatorObj', 'getStoreInformation') && ($gSession['shipping'][0]['shippingMethods'][$orderDetailsArray['shipping'][0]['shippingmethodcode']]['useScript']))
            {
                $ajaxCommand = 'STORELOCATOREXTERNAL';
            }

			$shippingRateItemCount = count($shippingRatesList);
			if ($shippingRateItemCount > 0)
            {
                for ($i = 0; $i < $shippingRateItemCount; $i++)
                {
                    if ($orderDetailsArray['shipping'][0]['shippingratecode'] == $shippingRatesList[$i]['ratecode'])
                    {
                        $checked = ' checked="checked"';
                        $shippingRateCode = $shippingRatesList[$i]['ratecode'];
                        $classSelected = 'optionSelected';
                    }
                    else
                    {
                        $checked = '';
                        $classSelected = '';
                    }

                    $shippingMethodId = 'shippingmethod' . $shippingRatesList[$i]['ratecode'];
                    $shippingMethodClassType = 'shippingMethod';
                    $shippingMethodDecorator = 'fnShippingMethodClick';
                    $shippingMethodDecoratorData = 'data-cfs="false"';
                    $shippingMethodLabel = LocalizationObj::getLocaleString($shippingRatesList[$i]['methodname'], $orderDetailsArray['browserlanguagecode'], true);
                    if (LocalizationObj::getLocaleString($shippingRatesList[$i]['info'], $orderDetailsArray['browserlanguagecode'], true) != '')
                    {
                        $shippingMethodLabel .= ' - ' . LocalizationObj::getLocaleString($shippingRatesList[$i]['info'], $orderDetailsArray['browserlanguagecode'], true);
                    }

                    if ($checked != '')
                    {
                        $shippingMethodCFSCode = $shippingRatesList[$i]['ratecode'];
                        $shippingMethodSelectedLabel = $shippingMethodLabel;
                    }

                    $shippingMethodsItem = $orderDetailsArray['shipping'][0]['shippingMethods'][$shippingRatesList[$i]['methodcode']];
                    $shippingMethodSelect = '';

                    if ($shippingMethodsItem['collectFromStore'])
                    {
						// only show button if store is not fixed
						if (!$shippingMethodsItem['storeIsFixed'])
						{
                            if ($gSession['ismobile'] == true)
                            {
                                $shippingMethodDecorator = 'fnSetHashUrl';
                                $shippingMethodDecoratorData = 'data-hash-url="store|' . $shippingRatesList[$i]['ratecode'] . '"';
                                $shippingMethodSelect = '<div class="btnCollectFromStore">
                                                            <img class="navigationArrow" src="' . UtilsObj::correctPath($gSession['webbrandwebroot']) . '/images/icons/change-arrow.png" alt=">"/>
                                                        </div>';
                            }
                            else
                            {
                                $shippingMethodDecorator = 'fnShippingMethodCfsClick';
                                $shippingMethodDecoratorData = 'data-ratecode="' . $shippingRatesList[$i]['ratecode'] . '" ';
								$shippingMethodDecoratorData .= 'data-script="' . $shippingMethodsItem['useScript'] . '"';
                                $buttonText = $smarty->get_config_vars('str_ButtonSelectStore');
                                $shippingMethodSelect = '<div class="contentBtn" data-decorator="fnSelectStore" data-method="' . $shippingMethodId . '">
                                                            <div class="btn-white-left" ></div>
                                                            <div class="btn-white-middle">'.$buttonText.'</div>
                                                            <div class="btn-white-right"></div>
                                                        </div>';
                            }
						}
						else
						{
							if ($gSession['ismobile'] == true)
                            {
								//if this is a fixed store and this method is already selected then do not allow the change method
								$shippingMethodDecorator = 'fnShippingMethodClick';
                                $shippingMethodDecoratorData = 'data-cfs="true"';
							}
							else
							{
                                $shippingMethodDecorator = 'fnShippingMethodCfsClick';
                                $shippingMethodDecoratorData = 'data-ratecode="' . $shippingRatesList[$i]['ratecode'] . '"';
							}
						}

                        $shippingMethodClassType = 'shippingMethodCfs';

						// get store address
			        	$formattedStoreAddress = UtilsAddressObj::formatAddress($shippingMethodsItem['storeAddress'], 'store', "\r");
			        	$formattedStoreAddress = UtilsObj::encodeString($formattedStoreAddress, false);
                        $formattedStoreAddress = str_replace("\r", "<br>", $formattedStoreAddress);

                        if ($gSession['ismobile'] == true)
                        {
                            $formattedStoreAddressList[$shippingRatesList[$i]['ratecode']] = str_replace("<br>", ",", $formattedStoreAddress);
                        }
                        else
                        {
                            $formattedStoreAddressList[$shippingRatesList[$i]['ratecode']] = $formattedStoreAddress;
                        }
						
						// if selected, display store address
						if ($checked != '')
						{
				            $initialShippingStoreAddressLabel = $smarty->get_config_vars('str_LabelStoreAddress');
				            $initialShippingStoreAddress = UtilsObj::encodeString($formattedStoreAddress, true);
				            $collectFromStore = 1;

							// is the store fixed?
							$storeFixedList[$shippingRatesList[$i]['ratecode']] = ($shippingMethodsItem['storeIsFixed']) ? 0 : 1;

							// current store code
							$storeCodeList[$shippingRatesList[$i]['ratecode']] = $shippingMethodsItem['storeCode'];

							// set the store id
							$orderDetailsArray['shipping'][0]['storeid'] = $shippingMethodsItem['storeCode'];
						}
                    }

                    $inputClass = '';
                    if ($shippingMethodSelect == '')
                    {
                        $classText = 'textShipping';
                    }
                    else
                    {
                        $inputClass = 'inputStore';
                        $classText = 'textShippingStore';
                    }

                    $shippingRate = $shippingRatesList[$i]['sell'];


                    // determine the tax status for the component
					if ($shippingRatesList[$i]['taxcode'] != '')
					{
						// tax is included in the price so determine the price without tax
						$shippingRateSellNoTax = UtilsObj::bround(($shippingRate / ($shippingRatesList[$i]['taxrate'] + 100)) * 100, $orderDetailsArray['order']['currencydecimalplaces']);
						$shippingRateTaxTotal = $shippingRate - $shippingRateSellNoTax;

						if ($shippingRatesList[$i]['taxrate'] != $orderDetailsArray['shipping'][0]['shippingratetaxrate'])
						{
							// if the tax included in the price is different to the line tax then we use the price without tax as we will be adding it later
							$shippingRate = $shippingRateSellNoTax;
							$taxValue = UtilsObj::bround($shippingRate * $orderDetailsArray['shipping'][0]['shippingratetaxrate'] / 100, $orderDetailsArray['order']['currencydecimalplaces']);
							$shippingRateSellWithTax = $shippingRate + $taxValue;
						}
						else
						{
							// tax is already calculated
							$shippingRateSellWithTax = $shippingRate;
						}
					}
					else
					{
						// no tax was included in the price
						$shippingRateTaxTotal = UtilsObj::bround($shippingRate * $orderDetailsArray['shipping'][0]['shippingratetaxrate'] / 100, $orderDetailsArray['order']['currencydecimalplaces']);
						$shippingRateSellWithTax = $shippingRate + $shippingRateTaxTotal;
						$shippingRateSellNoTax = $shippingRate;
					}

					// add the tax at this point if necessary
					if ($orderDetailsArray['order']['showpriceswithtax'] == 1)
					{
						$shippingRate = $shippingRateSellWithTax;
					}
					else
					{
						$shippingRate = $shippingRateSellNoTax;
					}

                    $shippingRateValue = UtilsObj::formatCurrencyNumber($shippingRate, $formatDP, $orderDetailsArray['browserlanguagecode'], $orderDetailsArray['order']['currencysymbol'], $orderDetailsArray['order']['currencysymbolatfront']);
                    if ($checked != '')
                    {
                        $shippingSelectedRate = $shippingRateValue;
                    }

                    if ($gSession['ismobile'] == true)
                    {
                        $shippingMethodsHTML .= '<li class="outerBoxPadding ' . $classSelected . '">';
                        $shippingMethodsHTML .= '<div class="checkboxImage"></div>';
                        $shippingMethodsHTML .= '<input type="radio" name="shippingmethods" id="' . $shippingMethodId . '" value="' . $shippingRatesList[$i]['ratecode'] . '" '
                                                . $checked . ' style="display:none" data-decorator="' . $shippingMethodDecorator . '" ' . $shippingMethodDecoratorData . ' />';
                        $shippingMethodsHTML .= '<div class="shippingContentClick">';
                        $shippingMethodsHTML .= '<label class="listLabel ' . $shippingMethodClassType . '" for="' . $shippingMethodId . '">' . $shippingMethodLabel . '<br /><span class="addressCFS">';
                        if ($classSelected != '')
                        {
                            if (isset($formattedStoreAddressList[$shippingRatesList[$i]['ratecode']]) && $formattedStoreAddressList[$shippingRatesList[$i]['ratecode']] != '')
                            {
                                $shippingMethodsHTML .=  UtilsObj::encodeString($formattedStoreAddressList[$shippingRatesList[$i]['ratecode']], true) . '<br />';
                            }
                        }
                        $shippingMethodsHTML .=  '</span><span class="methodPrice">' . $shippingRateValue . '</span></label>';
                        $shippingMethodsHTML .= $shippingMethodSelect;
                        $shippingMethodsHTML .= '</div>';
                        $shippingMethodsHTML .= '<div class="clear"></div>';
						$shippingMethodsHTML .= '</li>';
                    }
                    else
                    {
                        $shippingMethodsHTML .= '<li>';
                        $shippingMethodsHTML .= '<input type="radio" name="shippingmethods" id="' . $shippingMethodId . '" value="' . $shippingRatesList[$i]['ratecode'] . '" ' .
                            					$checked . '  class="' . $inputClass . '" data-decorator="' . $shippingMethodDecorator . '" data-trigger="change" ' . $shippingMethodDecoratorData . ' />';
                        $shippingMethodsHTML .= '<label class="' . $classText . '" for="' . $shippingMethodId . '">' . $shippingMethodLabel . '</label>' . $shippingMethodSelect;
                        $shippingMethodsHTML .= '<div class="shippingRate">' . $shippingRateValue . '</div>';
                        $shippingMethodsHTML .= '<div class="clear"></div>';
                        $shippingMethodsHTML .= '</li>';
                    }

                }
            }
            else
            {
                $shippingMethodsHTML .= '<li>' . $smarty->get_config_vars('str_ErrorNoShippingOptionsAvailable') . '</li>';
            }

	        if ($gConstants['optioncfs'])
	        {
		        $smarty->assign('storecodelist', $storeCodeList);
		        $smarty->assign('storefixedlist', $storeFixedList);
		        $smarty->assign('storeaddresses', $formattedStoreAddressList);
		        $smarty->assign('storeaddress', UtilsObj::encodeString($initialShippingStoreAddress, true));
	        }

			$smarty->assign('initialShippingStoreAddressLabel', $initialShippingStoreAddressLabel);
			$smarty->assign('initialShippingStoreAddress', $initialShippingStoreAddress);

			$smarty->assign('collectFromStore', $collectFromStore);

			$storecode = '';
			if (($pRemoveStore != 'true') || ($orderDetailsArray['shipping'][0]['shippingMethods'][$orderDetailsArray['shipping'][0]['shippingmethodcode']]['storeIsFixed'] == 1) )
			{
				$storecode = $orderDetailsArray['shipping'][0]['storeid'];
			}

			$smarty->assign('collectFromStoreCode', $storecode);
			$smarty->assign('shippingmethodslist', $shippingMethodsHTML);
            $smarty->assign('shippingmethodselectedlabel', $shippingMethodSelectedLabel);
            $smarty->assign('shippingmethodcfscode', $shippingMethodCFSCode);
            $smarty->assign('shippingselectedrate', $shippingSelectedRate);
			$smarty->assign('shippingratecode', $shippingRateCode);


            $shippingMethodCode = $gSession['shipping'][0]['shippingmethodcode'];

            $smarty->assign('initialfilter', $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeLocator']);
            $smarty->assign('payInStoreAllowed', $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['payInStoreAllowed']);
		    $smarty->assign('ajaxCommand', $ajaxCommand);
        }
        else if ($pCurrentUserStage == 'payment' || $pCurrentUserStage == 'email')
        {

            $smarty->assign('title', $titlePrefix . $smarty->get_config_vars('str_TitleOrderStagePayment'));
            $smarty->assign('ordertitle', $smarty->get_config_vars('str_HeadingOrderStagePayment'));

            // this should not be neccessary, but we do it to be on the safe side
			if ($showPricesWithTax == false)
			{
				$showTaxBreakdown = true;
			}

			// determine if we show the tax total
			if ($showTaxBreakdown)
			{
				if ($differentTaxRates)
				{
					if ($showAlwaysTaxTotal)
					{
						$showTaxTotal = true;
					}
					else
					{
						$showTaxTotal = false;
					}
				}
				else
				{
					if ($showPricesWithTax)
					{
						$showTaxTotal = true;
					}
				}
			}
			else
			{
				$showTaxTotal = false;
			}

			$smarty->assign('showtaxtotal', $showTaxTotal);

            if ($showPricesWithTax && $showTaxBreakdown)
			{
				if ($differentTaxRates)
				{
					// shipping tax
					$includesShippingTaxText = $smarty->get_config_vars('str_LabelIncludesTax');
					$includesShippingTaxText = str_replace('^0', $formattedShippingTaxRate, $includesShippingTaxText);
					$includesShippingTaxText = str_replace('^1', $formattedShippingTaxName, $includesShippingTaxText);
					$includesShippingTaxText = str_replace('^2', $formattedShippingTaxTotal2, $includesShippingTaxText);
					$smarty->assign('includesshippingtaxtext', $includesShippingTaxText);
				}
			}

			// orderFooter tax
            if(($orderFooterTaxTotal == 0) && (!$showZeroTax))
            {
                $smarty->assign('includesorderfootertaxtext', '');
            }
            else
            {
	    		if($orderfootertaxratesequal == 0)
        		{
        			$includesOrderFooterTaxText = $smarty->get_config_vars('str_LabelIncludesTaxTotal');
        			$includesOrderFooterTaxText = str_replace('^0', $formattedOrderFooterTaxTotal, $includesOrderFooterTaxText);
        			$smarty->assign('includesorderfootertaxtext', $includesOrderFooterTaxText);
        		}
        		else
        		{
        			$includesOrderFooterTaxText = $smarty->get_config_vars('str_LabelIncludesTax');
        			$includesOrderFooterTaxText = str_replace('^0', $formattedOrderFooterTaxRate, $includesOrderFooterTaxText);
        			$includesOrderFooterTaxText = str_replace('^1', $formattedOrderFooterTaxName, $includesOrderFooterTaxText);
        			$includesOrderFooterTaxText = str_replace('^2', $formattedOrderFooterTaxTotal, $includesOrderFooterTaxText);
        			$smarty->assign('includesorderfootertaxtext', $includesOrderFooterTaxText);
                }
    		}

			if ($showTaxTotal)
			{
				// tax total
				if ($orderDetailsArray['order']['orderalltaxratesequal'] == 1)
				{
					if ($showPricesWithTax)
					{
						$includesTaxTotalText = $smarty->get_config_vars('str_LabelIncludesTax');
						$includesTaxTotalText = str_replace('^0', $formattedShippingTaxRate, $includesTaxTotalText);
						$includesTaxTotalText = str_replace('^1', $formattedShippingTaxName, $includesTaxTotalText);
						$includesTaxTotalText = str_replace('^2', $formattedOrderTotalTax2, $includesTaxTotalText);
						$smarty->assign('includestaxtotaltext', $includesTaxTotalText);
					}
				}
				else
				{
					$includesTaxTotalText = $smarty->get_config_vars('str_LabelIncludesTaxTotal');
					$includesTaxTotalText = str_replace('^0', $formattedOrderTotalTax2, $includesTaxTotalText);
					$smarty->assign('includestaxtotaltext', $includesTaxTotalText);
				}
			}

            // build the list of payment methods
            $paymentMethodCode = '';
            $paymentMethodsHTML = '';

            if ($orderDetailsArray['order']['paymentmethodcode'] != 'NONE')
            {
				if ($orderDetailsArray['order']['paymentmethodcode'] != 'CARD')
				{
					// if CARD is not the chosen method, no gateway should be selected
					$orderDetailsArray['order']['paymentgatewaycode'] = '';
				}

                $paymentMethodsList = $pResultArray['paymentmethods'];
                $paymentMethodsItemCount = count($paymentMethodsList);
                $hasPaymentMethods = false;
                if ($paymentMethodsItemCount > 0)
                {
                	// include the payment integrations module
					require_once('../Order/PaymentIntegration/PaymentIntegration.php');
					
                    $iCountPayment = 1;
                    for ($i = 0; $i < $paymentMethodsItemCount; $i++)
                    {
                        $paymentAction = '';
                        $theCode = $paymentMethodsList[$i]['code'];
						$gatewaysPresent = false;
						
						if (($theCode == 'PAYPAL') || ($theCode == 'CARD') || ($theCode == 'KLARNA'))
                        {
							$gatewayJavascriptArray = array('scripturl' => '', 'script' => '', 'form' => '', 'requestpaymentparamsremotely' => false);
							$resultArray = PaymentIntegrationObj::configure($theCode);

                            if ($resultArray['active'] == true)
                            {
								// needed for PayPal Plus
								if ($resultArray['scripturl'] != '')
								{
									$gatewayJavascriptArray['scripturl'] = $resultArray['scripturl'];
								}
								
								$gatewayJavascriptArray['script'] = $resultArray['script'];

								$gatewayJavascriptArray['form'] = $resultArray['form'];
								$gatewayJavascriptArray['requestpaymentparamsremotely'] = $resultArray['requestpaymentparamsremotely'];
								$paymentAction = $resultArray['action'];
								

								$includePaymentMethod = true;
								$paymentGatewayJavascriptArray[$theCode] = $gatewayJavascriptArray;
								$algo = 'sha384';
								$paymentGatewayJavascriptArray[$theCode]['hash'] = sprintf('%s-%s', $algo, base64_encode(hash($algo, $gatewayJavascriptArray['script'], true)));
                            }
                            else
                            {
                                $includePaymentMethod = false;
                            }
                        }
                        else
                        {
							$paymentGatewayJavascriptArray[$theCode]['requestpaymentparamsremotely'] = false;
							$includePaymentMethod = true;
						}

						// if the paymentmethod is CARD and we have several gateways,
						// we need to show them separately
                        if ($theCode == 'CARD')
                        {
                        	$gateways = $resultArray['gateways'];
                        	$gatewaysPresent = (count($gateways) > 0);
                        }

						if ($includePaymentMethod == true)
                        {
                        	if ($gatewaysPresent)
                        	{
								foreach ($gateways as $gwCode => $gwName)
								{
									// if payment method is card, and multiple gateways are present, and none of them has been selected
									if (($orderDetailsArray['order']['paymentgatewaycode'] == '') && ($orderDetailsArray['order']['paymentmethodcode'] == 'CARD'))
									{
										$orderDetailsArray['order']['paymentgatewaycode'] = $gwCode;
									}
									if ($orderDetailsArray['order']['paymentgatewaycode'] == $gwCode)
									{
										$checked = ' checked="checked"';
										$paymentMethodCode = 'CARD_' . $gwCode;
                                        $methodClass = 'optionSelected';

										// display the subcode for the payment gateway in the email
										$paymentMethodLabelForEmail = $gwCode;
									}
									else
									{
										$checked = '';
                                        $methodClass = '';
									}

                                    if ($gSession['ismobile'] == true)
                                    {
                                        $paymentMethodsHTML .= '<div class="paymentMethodList outerBoxPadding ' . $methodClass . '">';
                                        $paymentMethodsHTML .= '<div class="checkboxImage"></div>';
										$paymentMethodsHTML .= '<input type="radio" id="paymentmethods_' . $iCountPayment . '" name="paymentmethods" action="' . $paymentAction . '" value="CARD_' . $gwCode . '"' . $checked . ' style="display:none;" 
											data-requestparamsremotley="' . (($paymentGatewayJavascriptArray['CARD']['requestpaymentparamsremotely']) ? 'true' : 'false') . '" data-decorator="paymentMethodClick">';
                                        $paymentMethodsHTML .= '<label data-paymentmethodcode="CARD" class="listLabelPayment paymentMethod" for="paymentmethods_' . $iCountPayment . '"><span>' . $gwName . '</span></label>';
                                        $paymentMethodsHTML .= '<div class="clear"></div>';
                                        $paymentMethodsHTML .= '</div>';
                                    }
                                    else
                                    {
                                        $paymentMethodsHTML .= '<div class="paymentmethodlist">';
                                        $paymentMethodsHTML .= '<input type="radio" id="paymentmethods_' . $iCountPayment . '" name="paymentmethods" action="' . $paymentAction . '" value="CARD_' .
                                                $gwCode . '"' . $checked . ' data-requestparamsremotley="' . (($paymentGatewayJavascriptArray['CARD']['requestpaymentparamsremotely']) ? 'true' : 'false') . '">&nbsp;<label for="paymentmethods_' . $iCountPayment . '">' . $gwName . '</label>';
                                        $paymentMethodsHTML .= '<div class="clear"></div>';
                                        $paymentMethodsHTML .= '</div>';
                                    }
                                    $iCountPayment++;
								}
							}
                        	else
                        	{
								if ($orderDetailsArray['order']['paymentmethodcode'] == $theCode)
								{
									$checked = ' checked="checked"';
									$paymentMethodCode = $theCode;
                                    $methodClass = 'optionSelected';
								}
								else
								{
									$checked = '';
                                    $methodClass = '';
								}

								$name = LocalizationObj::getLocaleString($paymentMethodsList[$i]['name'], $orderDetailsArray['browserlanguagecode'], true);

                                if ($gSession['ismobile'] == true)
                                {
                                    $paymentMethodsHTML .= '<div class="paymentMethodList outerBoxPadding ' . $methodClass . '">';
                                    $paymentMethodsHTML .= '<div class="checkboxImage"></div>';
                                    $paymentMethodsHTML .= '<input type="radio" id="paymentmethods_' . $iCountPayment . '" name="paymentmethods" action="' . $paymentAction . '" value="' .
                                            $paymentMethodsList[$i]['code'] . '"' . $checked . ' style="display:none;" data-requestparamsremotley="' . (($paymentGatewayJavascriptArray[$paymentMethodsList[$i]['code']]['requestpaymentparamsremotely']) ? 'true' : 'false') . '" data-decorator="paymentMethodClick" >';
                                    $paymentMethodsHTML .= '<label class="listLabelPayment paymentMethod" data-paymentmethodcode="'. $paymentMethodsList[$i]['code'] .'" for="paymentmethods_' . $iCountPayment . '"><span>' . $name . '</span></label>';
                                    $paymentMethodsHTML .= '<div class="clear"></div>';
                                    $paymentMethodsHTML .= '</div>';
                                }
                                else
                                {
                                    $paymentMethodsHTML .= '<div class="paymentmethodlist">';
                                    $paymentMethodsHTML .= '<input type="radio" id="paymentmethods_' . $iCountPayment . '" name="paymentmethods" action="' . $paymentAction . '" value="' .
                                            $paymentMethodsList[$i]['code'] . '"' . $checked . ' data-requestparamsremotley="'. (($paymentGatewayJavascriptArray[$paymentMethodsList[$i]['code']]['requestpaymentparamsremotely']) ? 'true' : 'false') . '" />&nbsp;';
                                    $paymentMethodsHTML .= '<label for="paymentmethods_' . $iCountPayment . '">' . $name.'</label>';
                                    $paymentMethodsHTML .= '<div class="clear"></div>';
                                    $paymentMethodsHTML .= '</div>';
                                }
								$paymentMethodLabelForEmail = $name;
                                $iCountPayment++;
							}
                            $hasPaymentMethods = true;
                        }
                    }
                }

                if ($hasPaymentMethods === false)
                {
                    //Email Content, changed li to span to make it compatibe with Outlook & other email clients
                    $paymentMethodsHTML .= '<span>' . $smarty->get_config_vars('str_ErrorNoPaymentMethodsAvailable') . '</span>';
                    $paymentMethodLabelForEmail = $smarty->get_config_vars('str_ErrorNoPaymentMethodsAvailable');
                }
            }
            else
            {
				$paymentMethodCode = 'NONE';
	            $paymentMethodLabelForEmail = 'NONE';
            }

			$showPaymentsOrderTotal = UtilsObj::str2Number(number_format($orderDetailsArray['order']['ordertotal'], $gSession['order']['currencydecimalplaces'], '.', ''), '.', '');
			$showPaymentsToPay = UtilsObj::str2Number(number_format($orderDetailsArray['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces'], '.', ''), '.', '');

            if (($orderDetailsArray['order']['paymentmethodcode'] == 'NONE') || ($showPaymentsOrderTotal > 0 && $showPaymentsToPay == 0))
            {
				$paymentMethodCode = 'NONE';
                $smarty->assign('hidepayments', true);
            }
            else
            {
                $smarty->assign('hidepayments', false);
			}

			//Email Content, display payment method as text rather than radiobox
			if($pCurrentUserStage == 'email')
			{
				$paymentMethodsHTML = '&nbsp;&nbsp;&nbsp;' . $paymentMethodLabelForEmail;
				$paymentMethodCode = '';
    			if ($orderDetailsArray['order']['paymentmethodcode'] == 'ACCOUNT')
                {
					$smarty->assign('labelamounttopay', $smarty->get_config_vars('str_LabelAmountDue'));
                }
                else if (($orderDetailsArray['order']['paymentmethodcode'] == 'PAYINSTORE') || ($orderDetailsArray['order']['paymentmethodcode'] == 'COD')
						|| ($orderDetailsArray['order']['paymentmethodcode'] == 'CHEQUE'))
                {
					$smarty->assign('labelamounttopay', $smarty->get_config_vars('str_LabelAmountToPay'));
                }
                else
                {
                	$smarty->assign('labelamounttopay', $smarty->get_config_vars('str_LabelAmountPaid'));
                }
            }

			$smarty->assign('paymentmethodslist', $paymentMethodsHTML);
			$smarty->assign('paymentmethodcode', $paymentMethodCode);

			if (($orderDetailsArray['useracccountbalance'] + $orderDetailsArray['order']['ordertotaltopay']) <= $orderDetailsArray['usercreditlimit'])
			{
			    $canUseAccount = true;
			}

            //shipping label
            if( $orderDetailsArray['shipping'][0]['shippingMethods'][$orderDetailsArray['shipping'][0]['shippingmethodcode']]['collectFromStore'])
            {
                $shippingStoreAddressLabel = $smarty->get_config_vars('str_LabelStoreAddress');
            }
            else
            {
                $shippingStoreAddressLabel = $smarty->get_config_vars('str_LabelShippingAddress');
            }
            $smarty->assign('shippingStoreAddressLabel', $shippingStoreAddressLabel);

        }

        $smarty->assign('shippingdiscountvalueraw', $orderDetailsArray['shipping'][0]['shippingratediscountvalue']);

		if (($orderDetailsArray['order']['voucherdiscountsection'] == 'SHIPPING') || ($differentTaxRates == true) || ($applyDiscountToSingleLineItem == true))
		{
			$smarty->assign('shippingdiscountname', UtilsObj::escapeInputForHTML(LocalizationObj::getLocaleString($orderDetailsArray['order']['vouchername'], $orderDetailsArray['browserlanguagecode'], true)));
			$smarty->assign('shippingdiscountvalue', self::formatOrderCurrencyNumber(($orderDetailsArray['shipping'][0]['shippingratediscountvalue'] * -1), $pCurrentUserStage));

			if (($showShippingTax == true) || ($orderDetailsArray['order']['showpriceswithtax'] == 1))
			{
				$smarty->assign('shippingsubtotalname', $smarty->get_config_vars('str_LabelSubTotal'));
				$smarty->assign('shippingdiscountedvalue', self::formatOrderCurrencyNumber($orderDetailsArray['shipping'][0]['shippingratetotalsell'], $pCurrentUserStage));
			}
			else
			{
				$smarty->assign('shippingsubtotalname', '');
                $smarty->assign('shippingtotal', self::formatOrderCurrencyNumber($orderDetailsArray['shipping'][0]['shippingratetotalsell'], $pCurrentUserStage));
                $smarty->assign('shippingdiscountedvalue', self::formatOrderCurrencyNumber($orderDetailsArray['shipping'][0]['shippingratetotalsell'], $pCurrentUserStage));
			}
		}
		else
		{
			$smarty->assign('shippingdiscountname', '');
			$smarty->assign('shippingdiscountvalue', '');
			$smarty->assign('shippingsubtotalname', '');
			$smarty->assign('shippingdiscountedvalue', '');
		}

        $smarty->assign('vouchercode', $orderDetailsArray['order']['vouchercode']);

        if (($orderDetailsArray['order']['voucherstatus'] != '') || ($orderDetailsArray['order']['giftcardstatus'] != ''))
        {
            if($orderDetailsArray['order']['giftcardstatus'] != '')
            {
                $smarty->assign('voucherstatusResult', $orderDetailsArray['order']['giftcardstatus']);
                $smarty->assign('voucherstatus', '');
                $smarty->assign('giftcardstatus', $smarty->get_config_vars($orderDetailsArray['order']['giftcardstatus']));
            }
            else
            {
                $smarty->assign('voucherstatusResult', $orderDetailsArray['order']['voucherstatus']);
                $smarty->assign('voucherstatus', $smarty->get_config_vars($orderDetailsArray['order']['voucherstatus']));
                $smarty->assign('giftcardstatus', '');
            }

            $smarty->assign('vouchercustommessage', LocalizationObj::getLocaleString($orderDetailsArray['order']['vouchercustommessage'], $orderDetailsArray['browserlanguagecode'], true));
            $smarty->assign('showgiftcardmessage', $orderDetailsArray['showgiftcardmessage']);
        }
        else
        {
            $smarty->assign('voucherstatusResult', '');
            $smarty->assign('voucherstatus', '');
            $smarty->assign('giftcardstatus', '');
            $smarty->assign('vouchercustommessage', '');
            $smarty->assign('showgiftcardmessage', 0);
        }

        if (($pCurrentUserStage == 'payment') && ($orderDetailsArray['showgiftcardmessage'] == 1))
        {
            // message has been set to be displayed, no need to display it again
            $orderDetailsArray['showgiftcardmessage'] = 0;
            $gSession['showgiftcardmessage'] = 0;
            DatabaseObj::updateSession();
        }

		if ($orderDetailsArray['order']['voucheractive'] == 1)
		{
			$smarty->assign('vouchersection', $orderDetailsArray['order']['voucherdiscountsection']);
		}
		else
		{
			$smarty->assign('vouchersection', '');
		}

		$smarty->assign('showshippingtax', $showShippingTax);
		$smarty->assign('differenttaxrates', $differentTaxRates);
		$smarty->assign('showtaxbreakdown', $showTaxBreakdown);
        $smarty->assign('showpriceswithtax', $showPricesWithTax);
        $smarty->assign('showzerotax', $showZeroTax);
        $smarty->assign('showalwaystaxtotal', $showAlwaysTaxTotal);

        // for debug
		if ($orderDetailsArray['order']['voucherdiscountsection'] == '')
		{
			$smarty->assign('voucherdiscountsection', 'NONE');
		}
		else
		{
			$smarty->assign('voucherdiscountsection', $orderDetailsArray['order']['voucherdiscountsection']);
		}

        $smarty->assign('canuseaccount', ($canUseAccount ? 'true' : 'false'));

        $smarty->assign('previousstage', $pResultArray['previousstage']);

        if ($pCurrentUserStage == 'payment')
        {
        	$showTermsAndConditions = 0;

			// check to see if there is an order terms and conditions template.
			// if no template path is returned then we do not show the terms and conditions section
			if ($smarty->getLocaleTemplate('order/ordertermsandconditions.tpl') != '')
			{
				$showTermsAndConditions = 1;
			}

			$smarty->assign('showtermsandconditions', $showTermsAndConditions);

        }

        /* $pTemplateDisplayStage and $pCurrentUserStage should be the same value, the only exception is when your cancel a payment
         * from a payment gateway on small screen device. The $pTemplateDisplayStage will be qty to load the application and
         * the $pCurrentUserStage will be payment to be able to redirect the user to teh correct panel */
        $smarty->assign('stage', $pTemplateDisplayStage);
        $smarty->assign('currentstage', $pCurrentUserStage);

		if ($pCurrentUserStage == 'email')
		{
			$shippingAddresses = UtilsAddressObj::formatAddress($orderDetailsArray['shipping'][0], 'shipping', ',&nbsp;');
			$billingAddresses = UtilsAddressObj::formatAddress($orderDetailsArray['order'], 'billing', ',&nbsp;');
			$smarty->assign('shippingaddress',$shippingAddresses);
			$smarty->assign('billingaddress', $billingAddresses);
		}
		else
		{
			$smarty->assign('shippingaddress', $pResultArray['formattedshippingaddress']);
			$smarty->assign('encodedshippingaddress', UtilsObj::encodeString($pResultArray['formattedshippingaddress'],true));
			$smarty->assign('billingaddress', $pResultArray['formattedbillingaddress']);
		}

        $smarty->assign('shippingmethodname', LocalizationObj::getLocaleString($orderDetailsArray['shipping'][0]['shippingmethodname'], $orderDetailsArray['browserlanguagecode'], true));

        // determine if the addresses are the same
        $addressDifferenceCount = 0;
        $addressDifference = $addressDifferenceCount + strcmp($orderDetailsArray['shipping'][0]['shippingcontactfirstname'], $orderDetailsArray['order']['billingcontactfirstname']);
        $addressDifferenceCount = $addressDifferenceCount + ($addressDifference == 0 ? 0 : 1);

        $addressDifference = strcmp($orderDetailsArray['shipping'][0]['shippingcontactlastname'], $orderDetailsArray['order']['billingcontactlastname']);
        $addressDifferenceCount = $addressDifferenceCount + ($addressDifference == 0 ? 0 : 1);

        $addressDifference = strcmp($orderDetailsArray['shipping'][0]['shippingcustomername'], $orderDetailsArray['order']['billingcustomername']);
        $addressDifferenceCount = $addressDifferenceCount + ($addressDifference == 0 ? 0 : 1);

        $addressDifference = strcmp($orderDetailsArray['shipping'][0]['shippingcustomeraddress1'], $orderDetailsArray['order']['billingcustomeraddress1']);
        $addressDifferenceCount = $addressDifferenceCount + ($addressDifference == 0 ? 0 : 1);

        $addressDifference = strcmp($orderDetailsArray['shipping'][0]['shippingcustomeraddress2'], $orderDetailsArray['order']['billingcustomeraddress2']);
        $addressDifferenceCount = $addressDifferenceCount + ($addressDifference == 0 ? 0 : 1);

        $addressDifference = strcmp($orderDetailsArray['shipping'][0]['shippingcustomeraddress3'], $orderDetailsArray['order']['billingcustomeraddress3']);
        $addressDifferenceCount = $addressDifferenceCount + ($addressDifference == 0 ? 0 : 1);

        $addressDifference = strcmp($orderDetailsArray['shipping'][0]['shippingcustomeraddress4'], $orderDetailsArray['order']['billingcustomeraddress4']);
        $addressDifferenceCount = $addressDifferenceCount + ($addressDifference == 0 ? 0 : 1);

        $addressDifference = strcmp($orderDetailsArray['shipping'][0]['shippingcustomercity'], $orderDetailsArray['order']['billingcustomercity']);
        $addressDifferenceCount = $addressDifferenceCount + ($addressDifference == 0 ? 0 : 1);

        $addressDifference = strcmp($orderDetailsArray['shipping'][0]['shippingcustomercounty'], $orderDetailsArray['order']['billingcustomercounty']);
        $addressDifferenceCount = $addressDifferenceCount + ($addressDifference == 0 ? 0 : 1);

        $addressDifference = strcmp($orderDetailsArray['shipping'][0]['shippingcustomerstate'], $orderDetailsArray['order']['billingcustomerstate']);
        $addressDifferenceCount = $addressDifferenceCount + ($addressDifference == 0 ? 0 : 1);

        $addressDifference = strcmp($orderDetailsArray['shipping'][0]['shippingcustomerregioncode'], $orderDetailsArray['order']['billingcustomerregioncode']);
        $addressDifferenceCount = $addressDifferenceCount + ($addressDifference == 0 ? 0 : 1);

        $addressDifference = strcmp($orderDetailsArray['shipping'][0]['shippingcustomerregion'], $orderDetailsArray['order']['billingcustomerregion']);
        $addressDifferenceCount = $addressDifferenceCount + ($addressDifference == 0 ? 0 : 1);

        $addressDifference = strcmp($orderDetailsArray['shipping'][0]['shippingcustomerpostcode'], $orderDetailsArray['order']['billingcustomerpostcode']);
        $addressDifferenceCount = $addressDifferenceCount + ($addressDifference == 0 ? 0 : 1);

        $addressDifference = strcmp($orderDetailsArray['shipping'][0]['shippingcustomercountrycode'], $orderDetailsArray['order']['billingcustomercountrycode']);
        $addressDifferenceCount = $addressDifferenceCount + ($addressDifference == 0 ? 0 : 1);

        if ($addressDifferenceCount == 0)
        {
            $smarty->assign('addressesmatch', 'true');
        }
        else
        {
            $smarty->assign('addressesmatch', 'false');
        }

		if ($orderDetailsArray['shipping'][0]['shippingmethodusedefaultshippingaddress'] == 0)
		{
			// set the address modification status

			if ($orderDetailsArray['shipping'][0]['shippingmethodcanmodifycontactdetails'] == 1)
			{
				$smarty->assign('canmodifyshipping', true);
			}
			else
			{
				if (($orderDetailsArray['order']['canmodifyshippingaddress'] == 1) || ($orderDetailsArray['order']['canmodifyshippingcontactdetails'] == 1))
				{
					$smarty->assign('canmodifyshipping', true);
				}
				else
				{
					$smarty->assign('canmodifyshipping', false);
				}
			}
		}
		else
		{
			if ($orderDetailsArray['shipping'][0]['shippingmethodcanmodifycontactdetails'] == 1)
			{
				$smarty->assign('canmodifyshipping', true);
			}
			else
			{
				$smarty->assign('canmodifyshipping', false);
			}
		}

		if ($orderDetailsArray['shipping'][0]['shippingmethodusedefaultbillingaddress'] == 0)
		{
			if ($orderDetailsArray['order']['canmodifybillingaddress'] == 1)
			{
				$smarty->assign('canmodifybilling', true);
			}
			else
			{
				$smarty->assign('canmodifybilling', false);
			}
		}
		else
		{
			if ($orderDetailsArray['shipping'][0]['shippingmethodcanmodifycontactdetails'] == 1)
			{
				$smarty->assign('canmodifybilling', true);
			}
			else
			{
				$smarty->assign('canmodifybilling', false);
			}
		}

        if ($orderDetailsArray['order']['sameshippingandbillingaddress'] == 1)
        {
            $smarty->assign('sameshippingandbillingaddress', true);
        }
        else
        {
            $smarty->assign('sameshippingandbillingaddress', false);
        }

        if ($orderDetailsArray['sessionrevived'] == 1)
        {
            $smarty->assign('sessionrevived', true);
        }
        else
        {
            $smarty->assign('sessionrevived', false);
        }

        $smarty->assign('call_action', 'init'); // first we are ceating the display order
        if ($gSession['ismobile'] == true)
        {
            $smarty->assign('header', $smarty->getLocaleTemplate('header_small.tpl', ''));
            $smarty->assign('footer', $smarty->getLocaleTemplate('order/footer_small.tpl', ''));
        }
        else
        {
            $smarty->assign('header', $smarty->getLocaleTemplate('header_large.tpl', ''));
            $smarty->assign('footer', $smarty->getLocaleTemplate('order/footer_large.tpl', ''));
        }

		if ($pCurrentUserStage == 'email')
		{
			$smarty->assign('orderline', $smarty->getLocaleEmailTemplate($pResultArray['emailtemplate'].'/email_orderline.tpl', ''));
			$smarty->assign('orderfooter', $smarty->getLocaleEmailTemplate($pResultArray['emailtemplate'].'/email_orderfooter.tpl', ''));
		}
		else
		{
            if ($gSession['ismobile'] == true)
            {
                $smarty->assign('orderline', $smarty->getLocaleTemplate('order/orderline_small.tpl', ''));
                $smarty->assign('orderfooter', $smarty->getLocaleTemplate('order/orderfooter_small.tpl', ''));
            }
            else
            {
                $smarty->assign('orderline', $smarty->getLocaleTemplate('order/orderline_large.tpl', ''));
                $smarty->assign('orderfooter', $smarty->getLocaleTemplate('order/orderfooter_large.tpl', ''));
            }
		}

        if ($pCurrentUserStage == 'companionselection')
        {
			if ($pResultArray['companionalbumlist']['companiontitle'] != '')
			{
				$companionTitle = LocalizationObj::getLocaleString($pResultArray['companionalbumlist']['companiontitle'], $gSession['browserlanguagecode'], true);
			}
			else
			{
				$companionTitle = $smarty->get_config_vars('str_LabelCompanionQuestion');
			}

			$smarty->assign('companionheadertitle', $companionTitle);

			foreach ($pResultArray['companionalbumlist']['items'] as $key => &$compOptions)
			{
				// set the section title
				$compOptions['title'] = str_replace("^0", $gSession['items'][$key]['itemprojectname'], $smarty->get_config_vars('str_LabelCompanionAvailableHeader'));
				$compOptions['description'] = LocalizationObj::getLocaleString($compOptions['description'], $gSession['browserlanguagecode'], true);

				foreach ($compOptions['items'] as &$theItems)
				{
					$theItems['productname'] = LocalizationObj::getLocaleString($theItems['productname'], $gSession['browserlanguagecode'], true);
					$theItems['totalsell'] = LocalizationObj::getLocaleString($theItems['totalsell'], $gSession['browserlanguagecode'], true);
					$theItems['qtyincartmessage'] = str_replace("^0", $theItems['qty'], $smarty->get_config_vars('str_LabelCompanionInCart'));
					$theItems['assetrequest'] = UtilsObj::getAssetRequest($theItems['layoutcode'], 'products');
				}
				unset($theItems);
			}
			unset($compOptions);

			$smarty->assign('companionOptions', $pResultArray['companionalbumlist']['items']);
		}

		$smarty->assign('hasCompanions', $gSession['order']['hascompanionalbums']);

        if (($pCurrentUserStage == 'companionselection') || ($pCurrentUserStage == 'qty') || ($pCurrentUserStage == 'payment') || ($pCurrentUserStage == 'email'))
        {
	        $smarty->assign('orderitems', $miscOrderItemDataArray);
        }

        $smarty->assign('sidebarcontactdetails_default', $smarty->getLocaleTemplate('order/sidebarcontactdetails_default.tpl', ''));
        $smarty->assign('sidebarcontactdetails', $smarty->getLocaleTemplate('order/sidebarcontactdetails_' . $pCurrentUserStage . '.tpl', ''));
        $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('order/sidebarleft_default.tpl', ''));
        $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('order/sidebarleft_' . $pCurrentUserStage . '.tpl', ''));
		$smarty->assign('paymentgatewayjavascriptarray', $paymentGatewayJavascriptArray);
        $smarty->assign('metadatalayout', $pResultArray['metadata']['layouthtml']);
        $smarty->assign('metadataform', $pResultArray['metadata']['submitform']);
        $smarty->assign('metadatasubmit', $pResultArray['metadata']['submitjavascript']);
        $smarty->assign('supporttelephonenumber', $orderDetailsArray['webbrandsupporttelephonenumber']);
		$smarty->assign('supportemailaddress', $orderDetailsArray['webbrandsupportemailaddress']);

        //initlanguage on reorder
        $initlanguage = '';

        if ($gConstants['initlang'] != '')
		{
            $initlanguage = 'createCookie("maweblocale", "' . $gConstants['initlang'] . '", 24 * 365);';
        }
        $smarty->assign('initlanguage', $initlanguage);

        $smarty->cachePage = $pCache; // allow the page to be cached so that the browser back button works correctly

       	if ($pCurrentUserStage == 'email')
       	{
			$smarty->assign('showthumbnail', $orderDetailsArray['emailthumbnailtype']);
			$smarty->assign('stage', 'payment');
	       	$htmlContent = $smarty->fetchLocaleEmail($pResultArray['emailtemplate'].'/email_jobticket.tpl', $orderDetailsArray['browserlanguagecode']);
			return $htmlContent;
       	}
       	else
       	{
            $smarty->assign('showgiftcardsbalance', $orderDetailsArray['order']['showgiftcardsbalance']);
            $smarty->assign('showvouchers', $orderDetailsArray['order']['showvouchers']);
	
            if ($gSession['ismobile'] == true)
            {
                if ($pDisplay == true)
                {
                    if ($gSession['ref'] > 0)
                    {
                        $smarty->assign('redirecturl', UtilsObj::getBrandedWebUrl() . '?fsaction=Order.initialize&ref=' . $gSession['ref']);
                    }
                    else
                    {
                        $smarty->assign('redirecturl', UtilsObj::getBrandedWebUrl());
                    }

                    $smarty->displayLocale('order/jobticket_small.tpl'); // temp because the desktop template is loaded when the session is created
                }
                else
                {
                    $smarty->assign('isrefreshcall', $pRefreshMainContent);
					$resultArray['template'] = $smarty->fetchLocale('order/jobticketajax_small.tpl');
					
					//Return the shipping methods template for any updated methods
					$resultArray['template2'] = $smarty->fetchLocale('order/shippingmethodlist.tpl');
                    if (($pRefreshMainContent == false) || ($pForceReloadJs == true))
                    {
                        $resultArray['javascript'] = $smarty->fetchLocale('order/jobticket.tpl');
                    }

					$resultArray['paymentgatewayjavascriptarray'] = $paymentGatewayJavascriptArray;

                    return $resultArray;
                }
            }
            else
            {
                $smarty->displayLocale('order/' . $orderDetailsArray['order']['jobtickettemplate'] . '.tpl');
            }
        }
    }

    static function displayError($pResultArray)
    {
        global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
        SmartyObj::replaceParams($smarty, $pResultArray['result'], $pResultArray['resultparam']);
        $smarty->assign('error1', $smarty->get_template_vars($pResultArray['result']));
        $smarty->assign('error2', '');

		if ($gSession['ismobile'] == true)
		{
			$smarty->assign('homeurl', UtilsObj::correctPath($gSession['webbrandweburl']));
			$smarty->assign('ref', $gSession['ref']);
			$smarty->assign('displayInline', false);
			$smarty->displayLocale('error_small.tpl');
		}
		else
		{
			$smarty->displayLocale('error_large.tpl');
		}
    }

    static function displayCompletion($pResultArray, $pAjaxCall = false)
    {
        global $gSession;

        // clear the cookies assigned to the session
        AuthenticateObj::clearSessionCookies();

        $orderNumber = $gSession['order']['ordernumber'];

        if ($gSession['order']['confirmationhtml'] == '')
        {
			$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
            $smarty->assign('TPX_SOURCE_DESKTOP', TPX_SOURCE_DESKTOP);
            if ($gSession['ref'] > 0)
            {
                $source = $gSession['items'][0]['source'];
                $smarty->assign('source', $source);
                if ($source == TPX_SOURCE_DESKTOP)
                {
                    $smarty->assign('mainwebsiteurl', '');
                }
                else
                {
                	if ($gSession['order']['basketapiworkflowtype'] == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
					{
						$smarty->assign('mainwebsiteurl', $pResultArray['webbrandonlineredirectionurl']);
					}
                }
            }
            else
            {
				$smarty->assign('source', TPX_SOURCE_ONLINE);

				if ($gSession['order']['basketapiworkflowtype'] == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
				{
					$smarty->assign('mainwebsiteurl', $pResultArray['webbrandonlineredirectionurl']);
				}
            }

			$smarty->assign('ordernumber',$gSession['order']['ordernumber']);
			$smarty->assign('ordertotal',$gSession['order']['ordertotal']);
			$smarty->assign('orderdata', $pResultArray['orderdata']);
            $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('order/sidebarleft_default.tpl', ''));
            $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('order/sidebarleft_confirmation.tpl', ''));
			$smarty->assign('hasCompanions', $gSession['order']['hascompanionalbums']);


			if (! class_exists('PaymentIntegrationObj'))
			{
				require_once('../Order/PaymentIntegration/PaymentIntegration.php');
			}

			$additionalPaymentInfoArray = array();

			if ($gSession['order']['paymentmethodcode'] == 'PAYPAL')
			{
				$additionalPaymentInfoArray = array(
					'paymentid' => $pResultArray['transactionid']
				);
			}

			$additionalPaymentInfo = PaymentIntegrationObj::ccAdditionalPaymentInfo($additionalPaymentInfoArray);
			$smarty->assign('additionalPaymentinfo', $additionalPaymentInfo);

			if ($gSession['order']['paymentmethodcode'] == 'PAYLATER')
			{
                SmartyObj::replaceParams($smarty, 'str_MessagePayLaterConfirmation1', '<span class="confirmationNumber">' . $gSession['order']['tempordernumber'] . '</span>');
                $smarty->assign('str_MessagePayLaterConfirmation1', $smarty->get_template_vars('str_MessagePayLaterConfirmation1'));

				if ($gSession['order']['isreorder'] == 1)
				{
                    if ($gSession['ismobile'] == true)
                    {
                        $smarty->assign('isajaxcall', $pAjaxCall);
                        if ($pAjaxCall == true)
                        {
                            $resultArray['template'] = $smarty->fetchLocale('order/orderpaylaterconfirmation_reorder_small.tpl');
                            $resultArray['javascript'] = $smarty->fetchLocale('order/orderconfirmation.tpl');
                            return $resultArray;
                        }
                        else
                        {
                            $smarty->displayLocale('order/orderpaylaterconfirmation_reorder_small.tpl');
                        }
                    }
                    else
                    {
                        $smarty->displayLocale('order/orderpaylaterconfirmation_reorder_large.tpl');
                    }
				}
				else
				{
                    if ($gSession['ismobile'] == true)
                    {
                        $smarty->assign('isajaxcall', $pAjaxCall);
                        if ($pAjaxCall == true)
                        {
                            $resultArray['template'] = $smarty->fetchLocale('order/orderpaylaterconfirmation_small.tpl');
                            $resultArray['javascript'] = '';
                            return $resultArray;
                        }
                        else
                        {
                            $smarty->displayLocale('order/orderpaylaterconfirmationm_small.tpl');
                        }
                    }
                    else
                    {
                        $smarty->displayLocale('order/orderpaylaterconfirmation_large.tpl');
                    }
				}
			}
			else
			{
                SmartyObj::replaceParams($smarty, 'str_MessageOrderConfirmation1', '<span class="confirmationNumber">' . $orderNumber . '</span>');

                $smarty->assign('str_MessageOrderConfirmation1', $smarty->get_template_vars('str_MessageOrderConfirmation1'));

				if (array_key_exists('ccicompletionmessage', $pResultArray))
				{
                    $smarty->assign('cciCompletionMessage', $pResultArray['ccicompletionmessage']);
				}
				else
				{
                    $smarty->assign('cciCompletionMessage', '');
				}

				if ($gSession['order']['isreorder'] == 1)
				{
                    if ($gSession['ismobile'] == true)
                    {
                        $smarty->assign('isajaxcall', $pAjaxCall);
                        if ($pAjaxCall == true)
                        {
                            $resultArray['template'] = $smarty->fetchLocale('order/orderconfirmation_reorder_small.tpl');
                            $resultArray['javascript'] = $smarty->fetchLocale('order/orderconfirmation.tpl');
                            return $resultArray;
                        }
                        else
                        {
                            $smarty->displayLocale('order/orderconfirmation_reorder_small.tpl');
                        }
                    }
                    else
                    {
                        $smarty->displayLocale('order/orderconfirmation_reorder_large.tpl');
                    }
				}
				else
				{
                    if ($gSession['ismobile'] == true)
                    {
                        $smarty->assign('isajaxcall', $pAjaxCall);
                        if ($pAjaxCall == true)
                        {
                            $resultArray['template'] = $smarty->fetchLocale('order/orderconfirmation_small.tpl');
                            $resultArray['javascript'] = $smarty->fetchLocale('order/orderconfirmation.tpl');
                            return $resultArray;
                        }
                        else
                        {
                            $smarty->displayLocale('order/orderconfirmation_small.tpl');
                        }
                    }
                    else
                    {
                        $smarty->displayLocale('order/orderconfirmation_large.tpl');
                    }
                }

		    }
        }
        else
        {
			$returnHTML = $gSession['order']['confirmationhtml'];
            $returnHTML = str_replace('<order_number/>', $orderNumber, $returnHTML);

        	echo $returnHTML;
        }
    }

    static function displayCancellation($pMainWebSiteURL)
    {
        global $gSession;

        // clear the cookies assigned to the session
        AuthenticateObj::clearSessionCookies();

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
        $smarty->assign('sidebarleft_default', $smarty->getLocaleTemplate('order/sidebarleft_default.tpl', ''));
        $smarty->assign('sidebarleft', $smarty->getLocaleTemplate('order/sidebarleft_confirmation.tpl', ''));

        $smarty->assign('TPX_SOURCE_DESKTOP', TPX_SOURCE_DESKTOP);
        if ($gSession['ref'] > 0)
        {
            $source = $gSession['items'][0]['source'];
            $smarty->assign('source', $source);
            if ($source == TPX_SOURCE_DESKTOP)
            {
                $smarty->assign('mainwebsiteurl', '');
            }
            else
			{
				if ($gSession['order']['basketapiworkflowtype'] == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
				{
					$smarty->assign('mainwebsiteurl', $pMainWebSiteURL);
				}
			}
        }
        else
        {
			$smarty->assign('source', TPX_SOURCE_ONLINE);

			if ($gSession['order']['basketapiworkflowtype'] == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
			{
				$smarty->assign('mainwebsiteurl', $pMainWebSiteURL);
			}
        }

        if ($gSession['sessionrevived'] == 1)
        {
            $smarty->assign('ref', $gSession['order']['tempordernumber']);

            $label = $smarty->get_config_vars('str_MessagePayLaterCancellation');

            $label = str_replace('{$formattedexpirydatetime}', LocalizationObj::formatLocaleDateTime($gSession['order']['temporderexpirydate'], $gSession['browserlanguagecode'], true), $label);
            $label = str_replace('{$formattedexpirydate}', LocalizationObj::formatLocaleDate($gSession['order']['temporderexpirydate'], $gSession['browserlanguagecode'], true), $label);
            $label = str_replace('{$formattedexpirytime}', LocalizationObj::formatLocaleTime($gSession['order']['temporderexpirydate'], $gSession['browserlanguagecode'], true), $label);
            $smarty->assign('str_MessagePayLaterCancellation', $label);

            if ($gSession['ismobile'] == true)
            {
                $resultArray['template'] = $smarty->fetchLocale('order/orderpaylatercancellation_small.tpl');
                return $resultArray;
            }
            else
            {
                $smarty->displayLocale('order/orderpaylatercancellation_large.tpl');
            }
        }
        else
        {
            if ($gSession['ismobile'] == true)
            {
                $resultArray['template'] = $smarty->fetchLocale('order/ordercancellation_small.tpl');
                return $resultArray;
            }
            else
            {
                $smarty->assign('reorder', $gSession['order']['isreorder']);
                $smarty->displayLocale('order/ordercancellation_large.tpl');
            }
        }
    }

    static function orderContinue($pResultArray)
    {
        global $ac_config;
        global $gSession;

        switch ($pResultArray['nextstage'])
        {
            case 'companionselection':
				self::displayJobTicket($pResultArray, 'companionselection', true, true, false, false, 'companionselection');
                break;
            case 'qty':
                self::displayJobTicket($pResultArray, 'qty', true, true, false, false, 'qty');
                break;
            case 'shipping':
                self::displayJobTicket($pResultArray, 'shipping', true, true, false, false, 'shipping');
                break;
            case 'payment':
                self::displayJobTicket($pResultArray, 'payment', true, true, false, false, 'payment');
                break;
            case 'promptforcard':
            	// include the payment integrations module
            	require_once('../Order/PaymentIntegration/PaymentIntegration.php');

                PaymentIntegrationObj::initialize();
                break;
            case 'complete':
                self::displayCompletion($pResultArray);

                // finally disable the session so that it cannot be used anymore
                // if we are paying later extend the session expiry time the relevant number of days
                if ($gSession['order']['paymentmethodcode'] == 'PAYLATER')
                {
                    $expiryTime = (int)UtilsObj::getArrayParam($ac_config, 'PAYLATEREXPIRYDAYS', 30);
                    DatabaseObj::disableSession($gSession['ref'], $expiryTime * 60 * 24);
                }
                else
                {
                    // if the session was revived then the timeout will be a long time into the future so set it to the standard timeout
                    if ($gSession['sessionrevived'] == 1)
                    {
                        $sessionDuration = (int)$ac_config['SESSIONDURATION'];
                        DatabaseObj::disableSession($gSession['ref'], $sessionDuration);
                    }
                    else
                    {
                        DatabaseObj::disableSession($gSession['ref'], 0);
                    }
                }
                break;
        }
    }

    static function changeComponentDisplay($pResultArray)
    {
        global $gSession;

        $componentDataArray = &DatabaseObj::getSessionOrderSection(0, $pResultArray['section']);

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        // include the system language selector
		$languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), false);
        $smarty->assign('systemlanguagelist', $languageHTMLList);
        $smarty->assign('time', time());

        $itemCount = count($pResultArray['component']);
        for ($i = 0; $i < $itemCount; $i++)
        {
        	$pResultArray['component'][$i]['assetrequest'] = UtilsObj::getAssetRequest($pResultArray['component'][$i]['code'], 'components');
        }

        $smarty->assign('componentlist', $pResultArray['component']);
        $smarty->assign('componentcount', $itemCount);
        $smarty->assign('componentcode', $componentDataArray['code']);
        $smarty->assign('imageurl', UtilsObj::getBrandedWebUrl());

        $smarty->assign('previousstage', $pResultArray['previousstage']);
        $smarty->assign('stage', $pResultArray['stage']);
        $smarty->assign('section', $pResultArray['section']);
        $smarty->assign('sectionname', LocalizationObj::getLocaleString($pResultArray['sectionname'], $gSession['browserlanguagecode'], true));

        $smarty->displayLocale('order/changecomponent_large.tpl');
    }

	static function selectStoreDisplay($pResultArray)
	{
		global $gSession;

		$logoRow = '';
        $logoHeight = 0;
		$shippingMethodCode = $gSession['shipping'][0]['shippingmethodcode'];

		$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        // include the system language selector
		if ($gSession['ismobile'] == true)
        {
            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), true);
            $smarty->assign('systemlanguagelist', $languageHTMLList);
        }
        else
        {
            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), false);
            $smarty->assign('systemlanguagelist', $languageHTMLList);
        }

		if ($pResultArray['logoUrl'] != '')
		{
			$logoRow = $pResultArray['logoUrl'];
            $logoHeight = $pResultArray['logoHeight'];
        }

		$smarty->assign('logorow', $logoRow);
        $smarty->assign('logoheight', $logoHeight);

		$fieldLabel = $pResultArray['fieldLabel'];
		if ($fieldLabel == '')
		{
			$fieldLabel = $smarty->get_config_vars('str_LabelAddressSearch');
		}

        $smarty->clearConfig('labelAddressSearch');
		$smarty->assign('labelAddressSearch', $fieldLabel);

		$storecode = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeCode'];

		if (isset($pResultArray['removestore']))
		{
			if ($pResultArray['removestore'] == 'true')
			{
				$storecode = '';
			}
		}

		$smarty->assign('ajaxCommand', $pResultArray['ajaxCommand']);
		$smarty->assign('external', $pResultArray['external']);
		$smarty->assign('storecode', $storecode);
		$smarty->assign('payInStoreAllowed', $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['payInStoreAllowed']);
		$smarty->assign('initialfilter', $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeLocator']);

		$smarty->assign('storelocations', $pResultArray['storeLocations']);
		$smarty->assign('countrylist', $pResultArray['countryList']);
		$smarty->assign('showcountrylist', $pResultArray['showCountryList']);
		$smarty->assign('showregionlist', $pResultArray['showRegionList']);
		$smarty->assign('showstoregroups', $pResultArray['showStoreGroups']);
		$smarty->assign('ajaxdivheight', $pResultArray['ajaxDivHeight']);

		$smarty->assign('storegrouplabel', $pResultArray['storeGroupLabel']);
		$smarty->assign('shippingratecode', $pResultArray['shippingRateCode']);

		$smarty->assign('sameshippingandbillingaddress', $gSession['order']['sameshippingandbillingaddress']);

        $smarty->assign('storelocationdata', json_encode($pResultArray['storelocationdata']));

		if ($pResultArray['showstorelistonopen'])
		{
			if ($pResultArray['ajaxCommand'] == 'STORELOCATOR')
			{
				$smarty->assign('storelist', self::storeLocator($pResultArray['storelist'], true));
			}
			else
			{
				$smarty->assign('storelist', self::storeLocatorExternal($pResultArray['storelist'], true));
			}
		}
		else
		{
			$smarty->assign('storelist', '');
		}

		$smarty->assign('previousstage', $pResultArray['previousstage']);
		$smarty->assign('stage', $pResultArray['stage']);
		$smarty->assign('tablewidth', 650);

        if ($gSession['ismobile'] == true)
		{
            $resultArray['templateform'] = $smarty->fetchLocale('order/storelocator_small.tpl');
            $resultArray['javascript'] = $smarty->fetchLocaleWebRoot('order/storelocator.tpl');

            return $resultArray;
        }
        else
        {
            $smarty->displayLocale('order/storelocator_large.tpl');
        }
    }

	static function changeAddressDisplay($pResultArray, $pAddressType, $pFromNewAccount, $pPromptToUpdateAddress)
	{
		global $gSession;
		global $gConstants;

		$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

		$shippingmethodcode = $gSession['shipping'][0]['shippingmethodcode'];

		if (!isset($pResultArray['shippingcfscontact']))
		{
			$pResultArray['shippingcfscontact'] = 0;
		}
		$smarty->assign('shippingcfscontact', $pResultArray['shippingcfscontact']);

		// include the system language selector
		if ($gSession['ismobile'] == true)
        {
            $smarty->assign('issmallscreen', 'true');
            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), true);
            $smarty->assign('systemlanguagelist', $languageHTMLList);
        }
        else
        {
            $smarty->assign('issmallscreen', 'false');
            $languageHTMLList = LocalizationObj::buildSystemLanguageList(UtilsObj::getBrowserLocale(), false);
            $smarty->assign('systemlanguagelist', $languageHTMLList);
        }

		if ($pResultArray['shippingcfscontact'] == '1')
		{
				$smarty->assign('title', $smarty->get_config_vars('str_HeaderCollectionDetails'));
				$smarty->assign('edit', '1');

				$refreshAction = 'Order.changeShippingAddressDisplay';
				$smarty->assign('addresstitle', $smarty->get_config_vars('str_HeaderCollectionDetails'));

				$addressArray = $gSession['shipping'][0]['shippingMethods'][$shippingmethodcode]['storeAddress'];
				$fieldPrefix = 'store';

				if (!isset($addressArray[$fieldPrefix . 'registeredtaxnumbertype']))
				{
					$addressArray[$fieldPrefix . 'registeredtaxnumbertype'] = '';
				}
				if (!isset($addressArray[$fieldPrefix . 'registeredtaxnumber']))
				{
					$addressArray[$fieldPrefix . 'registeredtaxnumber'] = '';
				}
		}
		else 
		{
			if ($pResultArray['updateaddressmode'] == TPX_UPDATEADDRESSMODE_FULL)
			{
				$smarty->assign('title', $smarty->get_config_vars('str_LabelUpdateShippingAddress'));
				$smarty->assign('edit', '0');

				$refreshAction = 'Order.changeShippingAddressDisplay';
				$smarty->assign('addresstitle', $smarty->get_config_vars('str_LabelShippingAddress'));

				$addressArray = $gSession['shipping'][0];
				$fieldPrefix = 'shipping';
			}
			else
			{
				$shippingMethodUseDefaultShippingAddress = $gSession['shipping'][0]['shippingmethodusedefaultshippingaddress'];
				$shippingMethodUseDefaultBillingAddress = $gSession['shipping'][0]['shippingmethodusedefaultbillingaddress'];
				$shippingMethodCanModifyContactDetails = $gSession['shipping'][0]['shippingmethodcanmodifycontactdetails'];

				if ($pAddressType == 'shipping')
				{
					if ($pPromptToUpdateAddress)
					{
						if ($gSession['order']['canmodifyshippingaddress'] == 1)
						{
							$smarty->assign('title', $smarty->get_config_vars('str_LabelUpdateShippingAddress'));
							$smarty->assign('edit', '0');
						}
					}
					else
					{
						if (($shippingMethodUseDefaultShippingAddress == 0) && ($shippingMethodCanModifyContactDetails == 0))
						{
							if (($gSession['order']['canmodifyshippingaddress'] == 0) && ($gSession['order']['canmodifyshippingcontactdetails'] == 1))
							{
								$smarty->assign('title', $smarty->get_config_vars('str_LabelUpdateShippingContactDetails'));
								$smarty->assign('edit', '1');
							}
							else
							{
								$smarty->assign('title', $smarty->get_config_vars('str_LabelUpdateShippingAddress'));
								$smarty->assign('edit', '0');
							}
						}
						elseif (($shippingMethodUseDefaultShippingAddress == 0) && ($shippingMethodCanModifyContactDetails == 1))
						{
							if ($gSession['order']['canmodifyshippingaddress'] == 1)
							{
								$smarty->assign('title', $smarty->get_config_vars('str_LabelUpdateShippingContactDetails'));
								$smarty->assign('edit', '0');
							}
							else
							{
								$smarty->assign('title', $smarty->get_config_vars('str_LabelUpdateShippingAddress'));
								$smarty->assign('edit', '1');
							}
						}
						else
						{
							$smarty->assign('edit', '1');
							$smarty->assign('title', $smarty->get_config_vars('str_LabelUpdateShippingContactDetails'));
						}
					}

					$refreshAction = 'Order.changeShippingAddressDisplay';
					$smarty->assign('addresstitle', $smarty->get_config_vars('str_LabelShippingAddress'));

					$addressArray = $gSession['shipping'][0];
					$fieldPrefix = 'shipping';
				}
				else
				{
					if (($shippingMethodUseDefaultBillingAddress == 0) && ($shippingMethodCanModifyContactDetails == 0))
					{
						if (($gSession['order']['canmodifybillingaddress'] == 0) && ($gSession['order']['canmodifyshippingcontactdetails'] == 1))
						{
							$smarty->assign('title', $smarty->get_config_vars('str_LabelUpdateBillingContactDetails'));
							$smarty->assign('edit', '1');
						}
						else
						{
							$smarty->assign('title', $smarty->get_config_vars('str_LabelUpdateBillingAddress'));
							$smarty->assign('edit', '0');
						}
					}
					elseif (($shippingMethodUseDefaultBillingAddress == 0) && ($shippingMethodCanModifyContactDetails == 1))
					{
						if ($gSession['order']['canmodifybillingaddress'] == 1)
						{
							$smarty->assign('title', $smarty->get_config_vars('str_LabelUpdateBillingContactDetails'));
							$smarty->assign('edit', '0');
						}
						else
						{
							$smarty->assign('title', $smarty->get_config_vars('str_LabelUpdateBillingAddress'));
							$smarty->assign('edit', '1');
						}
					}
					else
					{
						$smarty->assign('edit', '1');
						$smarty->assign('title', $smarty->get_config_vars('str_LabelUpdateBillingContactDetails'));
					}

					$refreshAction = 'Order.changeBillingAddressDisplay';
					$smarty->assign('title', $smarty->get_config_vars('str_LabelUpdateBillingAddress'));
					$smarty->assign('addresstitle', $smarty->get_config_vars('str_LabelBillingAddress'));

					$addressArray = $gSession['order'];
					$fieldPrefix = 'billing';
				}
			}
		}

		$smarty->assign('session', $gSession['ref']);

		if($gSession['useraddressupdated'] == 1)
		{
			$smarty->assign('message', '');
		}
		else
		{
			$smarty->assign('message',  $smarty->get_config_vars('str_MessageUpdateAddressDetails'));
			$smarty->assign('title', $smarty->get_config_vars('str_LabelUpdateAccountDetails'));
		}

		$smarty->assign('addresstype', $pAddressType);
		$smarty->assign('useraddressupdated', $gSession['useraddressupdated']);

		$smarty->assign('contactfname_script', UtilsObj::escapeInputForJavaScript($addressArray[$fieldPrefix . 'contactfirstname']));
		$smarty->assign('contactlname_script', UtilsObj::escapeInputForJavaScript($addressArray[$fieldPrefix . 'contactlastname']));
		$smarty->assign('companyname_script', UtilsObj::escapeInputForJavaScript($addressArray[$fieldPrefix . 'customername']));
		$smarty->assign('address1_script', UtilsObj::escapeInputForJavaScript($addressArray[$fieldPrefix . 'customeraddress1']));
		$smarty->assign('address2_script', UtilsObj::escapeInputForJavaScript($addressArray[$fieldPrefix . 'customeraddress2']));
		$smarty->assign('address3_script', UtilsObj::escapeInputForJavaScript($addressArray[$fieldPrefix . 'customeraddress3']));
		$smarty->assign('address4_script', UtilsObj::escapeInputForJavaScript($addressArray[$fieldPrefix . 'customeraddress4']));

		$additionalAddressFields = UtilsAddressObj::getAdditionalAddressFields($addressArray[$fieldPrefix . 'customercountrycode'], $addressArray[$fieldPrefix . 'customeraddress4']);
		$smarty->assign('add41_script', UtilsObj::escapeInputForJavaScript($additionalAddressFields['add41']));
		$smarty->assign('add42_script', UtilsObj::escapeInputForJavaScript($additionalAddressFields['add42']));
		$smarty->assign('add43_script', UtilsObj::escapeInputForJavaScript($additionalAddressFields['add43']));

		$smarty->assign('city_script', UtilsObj::escapeInputForJavaScript($addressArray[$fieldPrefix . 'customercity']));
		$smarty->assign('county_script', UtilsObj::escapeInputForJavaScript($addressArray[$fieldPrefix . 'customercounty']));
		$smarty->assign('state_script', UtilsObj::escapeInputForJavaScript($addressArray[$fieldPrefix . 'customerstate']));
		$smarty->assign('region', $addressArray[$fieldPrefix . 'customerregion']);
		$smarty->assign('regioncode', $addressArray[$fieldPrefix . 'customerregioncode']);
		$smarty->assign('postcode_script', UtilsObj::escapeInputForJavaScript($addressArray[$fieldPrefix . 'customerpostcode']));
		$smarty->assign('telephonenumber_script', UtilsObj::escapeInputForJavaScript($addressArray[$fieldPrefix . 'customertelephonenumber']));
		$smarty->assign('email', UtilsObj::escapeInputForHTML($addressArray[$fieldPrefix . 'customeremailaddress']));
		$smarty->assign('email_script', UtilsObj::escapeInputForJavaScript($addressArray[$fieldPrefix . 'customeremailaddress']));
		$smarty->assign('countryname', $addressArray[$fieldPrefix . 'customercountryname']);
		$smarty->assign('country', $addressArray[$fieldPrefix . 'customercountrycode']);

		$smarty->assign('registeredtaxnumbertype', $addressArray[$fieldPrefix . 'registeredtaxnumbertype']);
		$smarty->assign('registeredtaxnumber_script', UtilsObj::escapeInputForJavaScript($addressArray[$fieldPrefix . 'registeredtaxnumber']));

		$smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_NA', TPX_REGISTEREDTAXNUMBERTYPE_NA);
		$smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL', TPX_REGISTEREDTAXNUMBERTYPE_PERSONAL);
		$smarty->assign('TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE', TPX_REGISTEREDTAXNUMBERTYPE_CORPORATE);

		$smarty->assign('previousstage', $pResultArray['previousstage']);
		$smarty->assign('stage', $pResultArray['stage']);

		$smarty->assign('tablewidth', 650);

		$smarty->assign('refreshaction', $refreshAction);
        $smarty->assign('strictmode', '1');

		//initlanguage on reorder
        $initlanguage = '';

        if($gConstants['initlang'] != '')
        {
            $initlanguage = 'createCookie("maweblocale", "' . $gConstants['initlang'] . '", 24 * 365);';
        }
        $smarty->assign('initlanguage', $initlanguage);

		$autoSuggestAvailable = 0;

		// Check to see if the tax calculation script exists
		if (file_exists("../Customise/scripts/EDL_TaopixCustomerAccountAPI.php"))
		{
			require_once('../Customise/scripts/EDL_TaopixCustomerAccountAPI.php');

			if (method_exists('CustomerAccountAPI', 'autoSuggest'))
        	{
        		$autoSuggestAvailable = 1;
        	}
		}

		$smarty->assign('autosuggestavailable', $autoSuggestAvailable);

        if ($gSession['ismobile'] == true)
        {
            if ($pFromNewAccount)
            {
                $smarty->displayLocale('order/updateaddress2_small.tpl');
            }
            else
            {
                $resultArray['template'] = $smarty->fetchLocale('order/updateaddress_small.tpl');
                $resultArray['javascript'] = $smarty->fetchLocale('order/updateaddress.tpl');
                return $resultArray;
            }
        }
        else
        {
            $smarty->displayLocale('order/updateaddress_large.tpl');
        }
	}

	static function showCCIError($pResultArray)
	{
		// include the payment integrations module
        require_once('../Order/PaymentIntegration/PaymentIntegration.php');

	    global $gSession;

        if ($gSession['order']['confirmationhtml'] == '')
        {
			$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

			$smarty->assign('data1', $pResultArray['data1']);
			$smarty->assign('data2', $pResultArray['data2']);
			$smarty->assign('data3', $pResultArray['data3']);
			$smarty->assign('data4', $pResultArray['data4']);
			$smarty->assign('homeurl', '');

			if ($gSession['ismobile'] == true)
			{
				$smarty->assign('displayInline', false);
				$smarty->assign('homeurl', UtilsObj::correctPath($gSession['webbrandweburl']));
				$smarty->assign('ref', $gSession['ref']);
			}

			// get the template name
			$templateName = str_replace('.tpl', '', $pResultArray['errorform']);

			if ($gSession['ismobile'] == true)
			{
				$templateName .= '_small.tpl';
			}
			else
			{
				$templateName .= '_large.tpl';
			}

			$smarty->displayLocale('order/PaymentIntegration/' . $templateName);
        }
        else
        {
			$returnHTML = $gSession['order']['confirmationhtml'];

        	echo $returnHTML;
        }
	}

    static function getStoreLocatorLogo($pResultArray)
    {
    	global $gSession;

    	if ($pResultArray['logotype'] != '')
    	{
	    	Header('Content-type:' . $pResultArray['logotype']);
			echo $pResultArray['logo'];
    	}
    	elseif ($_GET['no'] == '1')
    	{
    		Header('Location: ' . UtilsObj::correctPath($gSession['webbrandwebroot']) . 'images/admin/nopreview.gif');
    	}
    }

    /**
	* Returns the currency amount, formatted with the settings currently held in the sesion.
	*
	* E.g. the amount 12.34 is formatted according to currency and language settings:
	* £12.34 en GBP
	* 12,34€ fr EUR
	*
   	* @static
	*
	* @param float $pAmount
	*
   	* @return string
   	*
   	* @author Steffen Haugk
	* @since Version 3.0.0
 	*/
    static function formatOrderCurrencyNumber($pAmount, $pStep = '')
    {
    	global $gSession;
		global $archivedOrderData;

		if($pStep == 'email')
		{
			return UtilsObj::formatCurrencyNumber($pAmount, $archivedOrderData['order']['currencydecimalplaces'], $archivedOrderData['browserlanguagecode'], $archivedOrderData['order']['currencysymbol'], $archivedOrderData['order']['currencysymbolatfront']);
    	}
    	else
    	{
    	    return UtilsObj::formatCurrencyNumber($pAmount, $gSession['order']['currencydecimalplaces'], $gSession['browserlanguagecode'], $gSession['order']['currencysymbol'], $gSession['order']['currencysymbolatfront']);
    	}
    }

    static function orderCanContinue($pComponentsArray, &$pOrderCanContinue, $pIsSection)
    {
    	// check to see if we are passing in a section.
    	// if we are then we need to process the section, its subsections and its checkboxes.
    	// otherwise we just process the checkbox components passed in.
    	if ($pIsSection)
    	{
			foreach ($pComponentsArray as &$component)
			{
				if ($component['componenthasprice'] == 1)
				{
					// loop around sub-sections
					foreach ($component['subsections'] as &$subsection)
					{
						if ($subsection['componenthasprice'] == 0)
						{
							$pOrderCanContinue = false;
							break 2;
						}

						// if the subsection is a drop down and set to inherit and the qty does not match the parent do not let the order through
						if (array_key_exists('inheritparentqty', $subsection))
						{
							if ($subsection['inheritparentqty'] == 1)
							{
								if (count($subsection['itemqtydropdown']) > 0)
								{
									if ($component['quantity'] != $subsection['quantity'])
									{
										$pOrderCanContinue = false;
										break 2;
									}
								}
							}
						}
					}

					if ($pOrderCanContinue)
					{
						foreach ($component['checkboxes'] as &$checkbox)
						{
							if ($checkbox['componenthasprice'] == 0)
							{
								$pOrderCanContinue = false;
								break 2;
							}
						}
					}
					else
					{
						break;
					}
				}
				else
				{
					$pOrderCanContinue = false;
					break;
				}
			}
		}
		else
		{
			foreach ($pComponentsArray as &$checkbox)
			{
				if ($checkbox['componenthasprice'] == 0)
				{
					$pOrderCanContinue = false;
					break;
				}
			}
		}
    }

    static function addGiftCard($pCanUseAccount)
    {
        global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        $resultArray = array(
            'success' => true,
            'tooltip' => $smarty->get_config_vars('str_TooltipDeleteGiftcard'),
            'ordertotaltopay' => self::formatOrderCurrencyNumber($gSession['order']['ordertotaltopay'], 'qty'),
            'giftcardtotalremaining' => self::formatOrderCurrencyNumber(($gSession['usergiftcardbalance'] - $gSession['order']['ordergiftcardtotal']), 'qty'),
            'giftcardamount' => self::formatOrderCurrencyNumber(-$gSession['order']['ordergiftcardtotal'], 'qty'),
            'canuseaccount' => ($pCanUseAccount),
            'hidepayment' => ($gSession['order']['ordertotaltopay'] == 0),
            'giftcardstate' => 'delete'
        );

        echo json_encode($resultArray, true);
    }

    static function deleteGiftCard($pCanUseAccount)
    {
        global $gSession;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        $resultArray = array(
            'success' => true,
            'tooltip' => $smarty->get_config_vars('str_TooltipAddGiftcard'),
            'ordertotaltopay' => self::formatOrderCurrencyNumber($gSession['order']['ordertotaltopay'], 'qty'),
            'giftcardtotalremaining' => self::formatOrderCurrencyNumber($gSession['usergiftcardbalance'], 'qty'),
            'giftcardamount' => self::formatOrderCurrencyNumber(-$gSession['order']['ordergiftcardtotal'], 'qty'),
            'canuseaccount' => ($pCanUseAccount),
            'hidepayment' => ($gSession['order']['ordertotaltopay'] == 0),
            'giftcardstate' => 'add'
        );

        echo json_encode($resultArray, true);
    }

    static function orderContinueAjax($pResultArray)
    {
        global $ac_config;
        global $gSession;
        $resultArray = array();

        switch ($pResultArray['nextstage'])
        {
            case 'qty':
                $resultArray = self::displayJobTicket($pResultArray, 'qty', true, false, false, false, 'qty');
                break;
            case 'shipping':
                $resultArray = self::displayJobTicket($pResultArray, 'shipping', true, false, false, false, 'shipping');
                break;
            case 'payment':
                $resultArray = self::displayJobTicket($pResultArray, 'payment', true, false, false, false, 'payment');
                break;
            case 'promptforcard':
            	// include the payment integrations module
            	require_once('../Order/PaymentIntegration/PaymentIntegration.php');

                $resultArray = PaymentIntegrationObj::initialize();
                break;
            case 'complete':
                $resultArray = self::displayCompletion($pResultArray, true);

                // finally disable the session so that it cannot be used anymore
                // if we are paying later extend the session expiry time the relevant number of days
                if ($gSession['order']['paymentmethodcode'] == 'PAYLATER')
                {
                    $expiryTime = (int)UtilsObj::getArrayParam($ac_config, 'PAYLATEREXPIRYDAYS', 30);
                    DatabaseObj::disableSession($gSession['ref'], $expiryTime * 60 * 24);
                }
                else
                {
                    // if the session was revived then the timeout will be a long time into the future so set it to the standard timeout
                    if ($gSession['sessionrevived'] == 1)
                    {
                        $sessionDuration = (int)$ac_config['SESSIONDURATION'];
                        DatabaseObj::disableSession($gSession['ref'], $sessionDuration);
                    }
                    else
                    {
                        DatabaseObj::disableSession($gSession['ref'], 0);
                    }
                }
                break;
        }

        return $resultArray;
    }

	static function storeLocator($pStoreList, $pReturn)
	{
        global $gSession;

        // return list of stores as a list of radio buttons
		$storeCode = isset($_GET['store']) ? $_GET['store'] : '';

		$html = '';

        if (sizeof($pStoreList) > 0)
        {
            foreach ($pStoreList as $store)
            {
                $classFirst = '';
                $class = '';
                $checked = '';
                $classSmall = '';

				if ($storeCode == $store['code'])
                {
                    $class = ' selected';
                    $checked = ' checked="checked"';
                    $classSmall = 'optionSelected';
                }

                if ($gSession['ismobile'] == true)
                {
                    $html .= '<div class="contentStoreDetail outerBoxPadding ' . $classSmall . '">';
                    $html .= '<div class="checkboxImage"></div>';
                    $html .= '<input type="radio" id="store_' . $store['code'] . '" name="store" data-externalstore="' . $store['externalstore'] . '" value="' . $store['code'] . '" ' . $checked . ' style="display:none" data-decorator="activeStore" />';
                    $html .= '<label class="listLabel" for="store_' . $store['code'] . '" class="labelSelectStore"><b>' . $store['name'] . '</b><br />';
                    $html .=  $store['address'];
                    if (array_key_exists('extrainfo', $store))
                    {
                        $html .= '<br /><label for="store_' . $store['code'] . '">' . $store['extrainfo'] . '</label>';
                    }
                    $html .= '</label>';
                    $html .= '<div class="borderRadioStore"></div>';
                    $html .= '<div class="imgInfo" data-decorator="fnShowStoreInfo" data-storecode="' . $store['code'] . '" data-externalstore="' . $store['externalstore'] . '"></div>';
                    $html .= '<div class="clear"></div>';
                    $html .= '</div>';
                }
                else
                {
                    if ($html == '')
                    {
                        $classFirst = ' first';
                    }

                    $html .= '<div class="contentStoreDetail' . $class . $classFirst . '" data-decorator="fnActivateStore" data-storecode="store_' . $store['code'] . '" data-payinstore="' . $store['payinstoreallowed'] . '">';
                    $html .= '<input type="radio" id="store_' . $store['code'] . '" name="store" data-externalstore="' . $store['externalstore'] . '" value="' . $store['code'] . '" ' . $checked . ' />';
                    $html .= '<div class="textRadioStore">';
                    $html .= '<label for="store_' . $store['code'] . '" class="labelSelectStore"><b>' . $store['name'] . '</b><br />';
                    $html .=  $store['address'] . '</label>';
                    if (array_key_exists('extrainfo', $store))
                    {
                        $html .= '<br /><label for="store_' . $store['code'] . '">' . $store['extrainfo'] . '</label>';
                    }
                    $html .= '</div>';
                    $html .= '<div class="borderRadioStore"></div>';
                    $html .= '<div class="imgInfo ' . $classFirst. '" data-decorator="fnShowStoreInfo" data-storecode="' . $store['code'] . '" data-externalstore="' . $store['externalstore'] . '"><img alt="" src="images/icons/more_info_icon.png" class="imgInfo" /></div>';
                    $html .= '<div class="clear"></div>';
                    $html .= '</div>';
                }
            }
        }
        else
        {
            $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], '', '', false, false);

            $html = '<div class="noStoresFound">';
            $html .= $smarty->get_config_vars('str_MessageNoMatchingStoresFound');
            $html .= '</div>';
        }

		if ($pReturn)
		{
			return $html;
		}
		else
		{
			echo $html;
		}
	}

	static function storeLocatorExternal($pResultArray, $pReturn)
	{
		global $gSession;

		switch ($pResultArray['resulttype'])
		{
			case TPX_EDL_CFS_DATA_TYPE_STORE:
				if ($pReturn)
				{
					return self::storeLocator($pResultArray['resultlist'], true);
				}
				else
				{
					self::storeLocator($pResultArray['resultlist'], false);
				}
				exit();
				break;
			case TPX_EDL_CFS_DATA_TYPE_GROUP:
				$storeList = $pResultArray['resultlist'];

				$html = '<table width="100%" class="scroll">';

				foreach ($storeList as $group)
				{
					$html .= '<tr>';
					$html .= '<td class="scroll"><span class="text">' . $group['description'] . '</span></td>';
					$html .= '<td width="20" class="scroll">';
					$html .= '<a href="#" class="collectfromstoregroupsearch" data-search="' . $group['search'] . '" data-privatesearch="' . $group['privatesearch'] . '" >';
					$html .= '<img src="' . $gSession['webbrandwebroot'] . '/utils/ext/images/silk/magnifier.png" style="border-style: none"></a>';
					$html .= '</td></tr>';
				}

				$html .= '</table>';

				if ($pReturn)
				{
					return $html;
				}
				else
				{
					echo $html;
				}
				break;
			case TPX_EDL_CFS_DATA_TYPE_ERROR:
			default:
				if ($pReturn)
				{
					return UtilsObj::utf8ToHtmlCodepoints($pResultArray['result']);
				}
				else
				{
					echo UtilsObj::utf8ToHtmlCodepoints($pResultArray['result']);
				}
				exit();
		}
	}

	static function initPaymentGateway($pParamArray)
	{
		if (array_key_exists('html', $pParamArray))
		{
			echo $pParamArray['html'];
		}
	}

	static function initPayNowOrder($pParamArray)
	{
		global $gSession;

		$smarty = SmartyObj::newSmarty('', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

    	if ($pParamArray['error'] != '')
        {
			$error = $smarty->get_config_vars($pParamArray['error']);

			echo '{"error":"' . $error . '"}';
        }
        else
        {
			echo json_encode($pParamArray, true);
        }
	}


	/**
	 * Output an acknowledgement message.
	 * iPay88 requires a specific message to acknowledge the data sent to the 'backedURL' from the payment gateway.
	 *
	 * @param string $pAckMessage Message to output
	 */
	static function sendAcknowledgement($pAckMessage)
	{
		if ($pAckMessage != '')
		{
			echo $pAckMessage;
		}
	}
}

?>