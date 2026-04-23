<?php

use PricingEngine\OrderLoader;

require_once(__DIR__ . '/../Utils/UtilsAddress.php');
require_once(__DIR__ . '/../Utils/UtilsMetaData.php');

if (file_exists("../Customise/scripts/EDL_Voucher.php"))
{
    require_once('../Customise/scripts/EDL_Voucher.php');
}

class Order_model
{
    static function initialize()
    {
        global $gSession;
        global $gConstants;
		global $gAuthSession;

        $result = '';
        $resultParam = '';
        $sourceOnline = false;
        $canCreateAccounts = 0;
        $productCode = '';
        $currencyCode = '';
        $currencyName = '';
        $currencyISONumber = '';
        $currencyDecimalPlaces = 2;
        $currencySymbol = '';
        $currencySymbolAtFront = 1;
        $exchangeRate = 1.0000;
        $tempSectionArray = Array();
        $resultArray = array();

        if ($gSession['ref'] > 0)
        {
			// store the ip address and browser information (we must do it here as this could be first place the browser hits)
			$gSession['order']['useripaddress'] = UtilsObj::getClientIPAddress();
			$gSession['order']['userbrowser'] = UtilsObj::cleanseInput(UtilsObj::getArrayParam($_SERVER, 'HTTP_USER_AGENT', ''), false);

            // if the online client time is not equal to zero then override the maweb timezone cookie with the new value
            if ($gSession['onlineclienttime'] != 0)
            {
				// we know if we came from online if we fall in here so turn off authentication checks
				$gAuthSession = false;
                $sourceOnline = true;

                $_COOKIE['mawebtz'] = $gSession['onlineclienttime'];

                // set the session value to zero so that it isn't used any more
                // the update session later on in this function will update to database session so that it isn't used any more
                $gSession['onlineclienttime'] = 0;
            }

            //Reset the items which are discounted
            $gSession['order']['itemsdiscounted'] = array();

            $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($gSession['licensekeydata']['groupcode']);
			$brandingDefaults = DatabaseObj::getBrandingFromCode('');

			// default brand settings for giftcards and vouchers
            $gSession['order']['showgiftcardsbalance'] = $brandingDefaults['allowgiftcards'];
            $gSession['order']['showvouchers'] = $brandingDefaults['allowvouchers'];

            // Set correct companycode for the customer. Check to see if the license key belongs to a brand if it does
            // use the brand company code. If not use the license key companycode
            $gSession['userdata']['companycode'] = $licenseKeyArray['companyCode'];

            if ($licenseKeyArray['webbrandcode'] != '')
            {
                $brandingArray = DatabaseObj::getBrandingFromCode($licenseKeyArray['webbrandcode']);
                $gSession['userdata']['companycode'] = $brandingArray['companycode'];
            }

            $canCreateAccounts = $licenseKeyArray['cancreateaccounts'];

            if (AuthenticateObj::WebSessionActive() == 1)
            {
                if ($gSession['order']['initialized'] == 0)
                {
                    // not yet initialized
                    $userAccountArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
                    $gSession['useraddressupdated'] = $userAccountArray['addressupdated'];

                    // get payment methods from: user -> lkey -> [brand] -> constants
                    // try and use customer settings first
                    if ($userAccountArray['usedefaultpaymentmethods'] == 1)
                    {
                        // then try licence key
                        if ($licenseKeyArray['usedefaultpaymentmethods'] == 1)
                        {
                            // is there a brand?
                            if ($licenseKeyArray['webbrandcode'] == '')
                            {
                                $gSession['userpaymentmethods'] = $brandingDefaults['paymentmethods'];
                            }
                            else
                            {
                                $brandingArray = DatabaseObj::getBrandingFromCode($licenseKeyArray['webbrandcode']);

                                // try brand
                                if ($brandingArray['usedefaultpaymentmethods'] == 1)
                                {
                                    $gSession['userpaymentmethods'] = $brandingDefaults['paymentmethods'];
                                }
                                else
                                {
                                    $gSession['userpaymentmethods'] = $brandingArray['paymentmethods'];
                                }
                            }
                        }
                        else
                        {
                            $gSession['userpaymentmethods'] = $licenseKeyArray['paymentmethods'];
                        }
                    }
                    else
                    {
                        $gSession['userpaymentmethods'] = $userAccountArray['paymentmethods'];
                    }

                    //assign the currency for the session
                    if ($userAccountArray['usedefaultcurrency'] == 1)
                    {
                        // account uses default, that means we go by licence key
                        $currencyCode = $licenseKeyArray['currencycode'];
                    }
                    else
                    {
                        $currencyCode = $userAccountArray['currencycode'];
                    }

					// if the customer account is set to use its own voucher details use them
					if($userAccountArray['usedefaultvouchersettings'] !== 1)
					{
						$gSession['order']['showgiftcardsbalance'] = $userAccountArray['allowgiftcards'];
						$gSession['order']['showvouchers'] = $userAccountArray['allowvouchers'];
					}
					// otherwise if its set in the license key to use the branding default use the branding default
					else if ($licenseKeyArray['usedefaultvouchersettings'] === 1)
					{
						//if not default brand use the settings for that brand
						if ($licenseKeyArray['webbrandcode'] != '')
						{
							$gSession['order']['showgiftcardsbalance'] = $brandingArray['allowgiftcards'];
							$gSession['order']['showvouchers'] = $brandingArray['allowvouchers'];
						}
						//otherwise use the branding defaults
						else
						{
							$gSession['order']['showgiftcardsbalance'] = $brandingDefaults['allowgiftcards'];
							$gSession['order']['showvouchers'] = $brandingDefaults['allowvouchers'];
						}
					}
					// otherwise it is set to use the license key defaults in which case use the license key defaults
					else
					{
						$gSession['order']['showgiftcardsbalance'] = $licenseKeyArray['allowgiftcards'];
						$gSession['order']['showvouchers'] = $licenseKeyArray['allowvouchers'];
					}

                    $currencyArray = DatabaseObj::getCurrency($currencyCode);
                    if ($currencyArray['recordid'] > 0)
                    {
                        $currencyName = $currencyArray['name'];
                        $currencyISONumber = $currencyArray['isonumber'];
                        $currencyDecimalPlaces = $currencyArray['decimalplaces'];
                        $currencySymbol = $currencyArray['symbol'];
                        $currencySymbolAtFront = $currencyArray['symbolatfront'];
                        $exchangeRate = $currencyArray['exchangerate'];
                    }
                    else
                    {
                        $currencyCode = '';
                    }

                    $gSession['order']['currencycode'] = $currencyCode;
                    $gSession['order']['currencyname'] = $currencyName;
                    $gSession['order']['currencyisonumber'] = $currencyISONumber;
                    $gSession['order']['currencydecimalplaces'] = $currencyDecimalPlaces;
                    $gSession['order']['currencysymbol'] = $currencySymbol;
                    $gSession['order']['currencysymbolatfront'] = $currencySymbolAtFront;
                    $gSession['order']['basecurrencycode'] = $gConstants['defaultcurrencycode'];
                    $gSession['order']['currencyexchangerate'] = $exchangeRate;

                    // assign the ccitype for the session
                    $gSession['order']['ccitype'] = DatabaseObj::getPaymentIntegrationFromBrand($licenseKeyArray['webbrandcode']);

					// initialise the default addresses for the cart
					self::initializeCartShippingAndBillingAddress($licenseKeyArray, $userAccountArray);

					// set the session brand based on the license key brand
                    AuthenticateObj::setSessionWebBrand($licenseKeyArray['webbrandcode']);

                    // loop around order items - begin
                    $productCodes = array();

                    foreach ($gSession['items'] as $currentLine => $orderLine)
					{
                    	// build order line component structures
						self::buildOrderLineComponentStructure($productCodes, $exchangeRate, $currencyDecimalPlaces, $currentLine);
					}

                    // process ORDERFOOTER section
                    // if reorder then get the list of components in the original footer
                    $componentsFromOriginalFooter = Array();
                    if ($gSession['order']['isreorder'] == 1)
                    {
                        $componentsFromOriginalFooter = DatabaseObj::getComponentsFromFooter($gSession['order']['origorderid']);
                        if ($componentsFromOriginalFooter['result'] == '')
                        {
                            $componentsFromOriginalFooter = $componentsFromOriginalFooter['items'];
                        }
                        else
                        {
                            $result = $componentsFromOriginalFooter['result'];
                            $resultParam = $componentsFromOriginalFooter['resultParam'];
                        }
                    }

                    $tempSectionArray = self::getOrderFooterSectionData($componentsFromOriginalFooter, $exchangeRate, $currencyDecimalPlaces);

                    // calculate the order totals
                    self::updateAllOrderSections();
                    self::updateOrderTaxRate();
                    self::updateOrderTotal();
                    self::updateOrderShippingRate();
                    self::updateOrderTotal();

                    $gSession['order']['metadata'] = MetaDataObj::getKeywordList('ORDER', $gSession['licensekeydata']['groupcode'],
                                    $productCodes, 0);
                    $gSession['order']['starttime'] = strtotime(DatabaseObj::getServerTime());

                    //assign the project thumbnails to the user if it is an order from desktop
                    if ($sourceOnline === false)
                    {
                        self::assignUserToDesktopProjectThumbnails($gSession['userid']);
                    }

                    $gSession['order']['initialized'] = 1;
                    $gSession['order']['currentstage'] = '';

                    if (($gSession['order']['canmodifyshippingaddress'] == 1) && ($gSession['useraddressupdated'] == 0))
                    {
                        $gSession['promptforaddress'] = true;
                    }
                    else
                    {
                        $gSession['promptforaddress'] = false;
                    }

                    // we must ignore the licensekey settings and force update address
                    if ($gSession['useraddressupdated'] == 2)
                    {
                        $gSession['promptforaddress'] = true;
                    }

                    DatabaseObj::updateSession();
                }

				$resultArray['companionalbumlist'] = array('companiontitle' => '', 'items' => array());

				// dont show companion albums if running Internet Explorer 7 or earlier
				if ((stripos($gSession['order']['userbrowser'], 'msie') !== false) && (stripos($gSession['order']['userbrowser'], 'opera') === false))
				{
					// Internet Explorer versions before 11
					$versionString = explode(' ', stristr(str_replace(';' ,'; ' , $gSession['order']['userbrowser']), 'msie'));
					$version = str_replace(array('(' , ')' ,';'), '', $versionString[1]);
					$pos = strpos($version, '.');
    				$versionNumber = (int) substr($version, 0, $pos);

					if ($versionNumber <= 7)
					{
						$gSession['order']['hascompanionalbums'] = TPX_ORDERHASCOMPANIONALBUMS_NO;
					}
				}

				if (($gSession['ismobile'] == false) && ($gSession['order']['hascompanionalbums'] != TPX_ORDERHASCOMPANIONALBUMS_NO))
				{
					$resultArray['companionalbumlist'] = self::buildOrderCompanionSelection($exchangeRate, $currencyDecimalPlaces);
				}

                $resultArray['custominit'] = '';

				if (count($resultArray['companionalbumlist']['items']) > 0)
				{
					$resultArray['previousstage'] = 'companionselection';
               		$resultArray['stage'] = 'companionselection';
               		$gSession['order']['hascompanionalbums'] = TPX_ORDERHASCOMPANIONALBUMS_YES;
				}
				else
				{
                	$resultArray['previousstage'] = 'qty';
                	$resultArray['stage'] = 'qty';
                	$gSession['order']['hascompanionalbums'] = TPX_ORDERHASCOMPANIONALBUMS_NO;
                }

                $resultArray['metadata'] = self::buildOrderMetaData($resultArray['stage']);

                if ($gSession['order']['currentstage'] == '')
                {
                	$gSession['order']['currentstage'] = $resultArray['stage'];
                }

                self::formatOrderAddresses($resultArray);
            }
            else
            {
                AuthenticateObj::setSessionWebBrand($licenseKeyArray['webbrandcode']);
                DatabaseObj::updateSession();
            }
        }
        else
        {
            $result = 'str_ErrorNoSessionRef';
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['cancreateaccounts'] = $canCreateAccounts;

        return $resultArray;
    }

    static function getComponentsFromDesignerSelections(&$pComponentsFromOriginalOrder, $pSelectedComponentsArray)
    {
        foreach($pSelectedComponentsArray as $component)
        {
            // if the componentcode is empty then we know we are dealing with a section and dont want to record it
            if ($component['componentcode'] != '')
            {
                $orderComponentItem = array();
                $codeArray = explode('.', $component['componentcode']);

                $orderComponentItem['name'] = $component['name'];
                $orderComponentItem['componentcode'] = $component['componentcode'];
                $orderComponentItem['componentpath'] = ($component['path'] == '' || $component['path'] == '$LINEFOOTER\\') ? $component['path'] : str_replace('\\\\', '\\', $component['path']) . '\\';
                $orderComponentItem['islist'] = $component['islist'];
                $orderComponentItem['checkboxselected'] = ($component['islist'] == 1) ? 0 : $component['selected'];
                $orderComponentItem['quantity'] = $component['quantity'];
                $orderComponentItem['componentlocalcode'] = $codeArray[1];
                $orderComponentItem['keywords'] = $component['keywords'];
                $pComponentsFromOriginalOrder[] = $orderComponentItem;
            }

            if (!empty($component['children']))
            {
                self::getComponentsFromDesignerSelections($pComponentsFromOriginalOrder, $component['children']);
            }
        }

        return $pComponentsFromOriginalOrder;
    }

	static function buildOrderLineComponentStructure(&$pProductCodesArray, $pCurrencyExchangeRate, $pCurrencyDecimalPlaces, $pLineItemIndex)
	{
		global $gSession;

		$result = '';
		$resultParam = '';

		$currentLine = $pLineItemIndex;

		$orderLine = &$gSession['items'][$pLineItemIndex];

		$productCode = $orderLine['itemproductcode'];
        $componentTreeProductCode = $orderLine['componenttreeproductcode'];

		$pProductCodesArray[] = $productCode;
        $componentsFromOriginalOrder = array();
        $iCountComp = 0;
        $hasComponentsFromDesigner = false;

        // get the product price
		$itemQty = -1;

        if ($orderLine['source'] == TPX_SOURCE_ONLINE && $gSession['order']['isreorder'] == 0 && $orderLine['components'] != '')
        {
            $selectedComponentsArray = json_decode($orderLine['components'], true);
            $itemQty = $selectedComponentsArray[0]['productquantity'];
            array_shift($selectedComponentsArray);

            foreach($selectedComponentsArray as $key => $section)
            {
                $componentsFromOriginalOrder = self::getComponentsFromDesignerSelections($componentsFromOriginalOrder, $section['children']);
            }

            $iCountComp = count($componentsFromOriginalOrder);
            $hasComponentsFromDesigner = true;
        }

		$productPriceArray = DatabaseObj::getProductPrice($productCode, $gSession['licensekeydata']['groupcode'],
						$gSession['userdata']['companycode'], $pCurrencyExchangeRate, $pCurrencyDecimalPlaces, $itemQty);

		// if the qty is still -1, force the qty to 1 and recalculate price
		if ($productPriceArray['newqty'] == -1)
		{
			$itemQty = 1;
			$productPriceArray = DatabaseObj::getProductPrice($productCode, $gSession['licensekeydata']['groupcode'],
						$gSession['userdata']['companycode'], $pCurrencyExchangeRate, $pCurrencyDecimalPlaces, $itemQty);
		}

		$itemQty = $productPriceArray['newqty'];

		$gSession['shipping'][0]['shippingqty'] = $itemQty;
		$orderLine['itemproductinfo'] = $productPriceArray['productinfo'];
		$orderLine['itemqty'] = $itemQty;
		$orderLine['pricetaxcode'] = $productPriceArray['taxcode'];
		$orderLine['pricetaxrate'] = $productPriceArray['taxrate'];

		// if reorder then get the list of components in the original order
		if ($gSession['order']['isreorder'] == 1)
		{
			$componentsFromOriginalOrder = DatabaseObj::getComponentsFromOrder($orderLine['origorderitemid']);

            if ($componentsFromOriginalOrder['result'] == '')
			{
				$componentsFromOriginalOrder = $componentsFromOriginalOrder['items'];
				$iCountComp = count($componentsFromOriginalOrder);
			}
			else
			{
				$result = $componentsFromOriginalOrder['result'];
				$resultParam = $componentsFromOriginalOrder['resultParam'];
			}
		}

		// get list of checkboxes in order item root
		$checkboxList = DatabaseObj::getOrderCheckboxes($componentTreeProductCode, $gSession['licensekeydata']['groupcode'],
						$gSession['userdata']['companycode'], '', $pCurrencyExchangeRate, $pCurrencyDecimalPlaces, $itemQty,
						$orderLine['itempagecount'], $orderLine['orderlineid']);

		if ($checkboxList['result'] == '')
		{
			// on reorder restore state of the checkboxes in order item root
			if ((($gSession['order']['isreorder'] == 1) || ($hasComponentsFromDesigner)) && ($result == ''))
			{
				$orderCheckboxes = &$checkboxList['checkboxes'];
				for ($i = 0; $i < count($orderCheckboxes); $i++)
				{
					$itemComponent = $orderCheckboxes[$i];

					for ($j = 0; $j < $iCountComp; $j++)
					{
						$itemOriginalComponent = $componentsFromOriginalOrder[$j];
						if (($itemComponent['code'] == $itemOriginalComponent['componentcode']) && ($itemComponent['path'] == $itemOriginalComponent['componentpath']))
						{
                            if (array_key_exists('checked', $itemComponent))
							{
								$checkboxList['checkboxes'][$i]['checked'] = $itemOriginalComponent['checkboxselected'];
                                $checkboxList['checkboxes'][$i]['quantity'] = $itemOriginalComponent['quantity'];
								break;
							}
						}
					}
				}

                self::getOrderCheckboxes($componentsFromOriginalOrder, $orderCheckboxes);
			}

			$orderLine['checkboxes'] = $checkboxList['checkboxes'];
		}

        // get list of required sections
		$sectionList = DatabaseObj::getOrderSectionList($componentTreeProductCode, $gSession['licensekeydata']['groupcode'],
						$gSession['userdata']['companycode'], '');

		foreach ($sectionList['sections'] as $sectionCode)
		{
			$section = &DatabaseObj::getSessionOrderSection($currentLine, $componentTreeProductCode, '$' . $sectionCode . '\\',
							$pCurrencyExchangeRate, $pCurrencyDecimalPlaces, $itemQty, false, '', 0);

            // on reorder select components selected in the original order
			for ($j = 0; $j < $iCountComp; $j++)
			{
				$itemOriginalComponent = $componentsFromOriginalOrder[$j];

                if ($section['path'] == $itemOriginalComponent['componentpath'])
				{
                    //get subsection
					self::updateComponentReorder($orderLine['orderlineid'], $section['orderlineid'],
							$itemOriginalComponent['componentcode'], $itemOriginalComponent['componentlocalcode'],
							$componentsFromOriginalOrder);
					$section['code'] = $itemOriginalComponent['componentcode'];
                    $section['defaultcode'] = $itemOriginalComponent['componentcode'];
					self::updateOneOrderSection($currentLine, $componentsFromOriginalOrder);
					break;
				}
			}
		}

		// get list of required sections in line footer
		$sectionList = DatabaseObj::getOrderSectionList($componentTreeProductCode, $gSession['licensekeydata']['groupcode'],
						$gSession['userdata']['companycode'], '$LINEFOOTER\\');

		foreach ($sectionList['sections'] as $sectionCode)
		{
			$section = &DatabaseObj::getSessionOrderSection($currentLine, $componentTreeProductCode,
							'$LINEFOOTER\$' . $sectionCode . '\\', $pCurrencyExchangeRate, $pCurrencyDecimalPlaces, $itemQty);

                            // on reorder select components selected in the original order
			for ($j = 0; $j < $iCountComp; $j++)
			{
				$itemOriginalComponent = $componentsFromOriginalOrder[$j];

				if ($section['path'] == $itemOriginalComponent['componentpath'])
				{
					//get subsection
					self::updateComponentReorder($orderLine['orderlineid'], $section['orderlineid'],
							$itemOriginalComponent['componentcode'], $itemOriginalComponent['componentlocalcode'],
							$componentsFromOriginalOrder);
					$section['code'] = $itemOriginalComponent['componentcode'];
                    $section['defaultcode'] = $itemOriginalComponent['componentcode'];
					self::updateOneOrderSection($currentLine, $componentsFromOriginalOrder);
					break;
				}
			}
		}

		// get list of checkboxes in line footer
		$checkboxList = DatabaseObj::getOrderCheckboxes($componentTreeProductCode, $gSession['licensekeydata']['groupcode'],
						$gSession['userdata']['companycode'], '$LINEFOOTER\\', $pCurrencyExchangeRate, $pCurrencyDecimalPlaces,
						$itemQty, $orderLine['itempagecount'],
						$orderLine['orderlineid']);

		// on reorder restore state of the checkboxes in  line footer
		if ((($gSession['order']['isreorder'] == 1) || ($hasComponentsFromDesigner)) && ($result == ''))
		{
			$orderCheckboxes = &$checkboxList['checkboxes'];
			for ($i = 0; $i < count($orderCheckboxes); $i++)
			{
				$itemComponent = $orderCheckboxes[$i];

				for ($j = 0; $j < $iCountComp; $j++)
				{
					$itemOriginalComponent = $componentsFromOriginalOrder[$j];

                    if (($itemComponent['code'] == $itemOriginalComponent['componentcode']) && ($itemComponent['path'] == $itemOriginalComponent['componentpath']))
					{
						if (array_key_exists('checked', $itemComponent))
						{
							$checkboxList['checkboxes'][$i]['checked'] = $itemOriginalComponent['checkboxselected'];
                            $checkboxList['checkboxes'][$i]['quantity'] = $itemOriginalComponent['quantity'];
							break;
						}
					}
				}
			}

            self::getOrderCheckboxes($componentsFromOriginalOrder, $orderCheckboxes);

		}

		if ($checkboxList['result'] == '')
		{
			$orderLine['lineFooterCheckboxes'] = $checkboxList['checkboxes'];
		}
	}

	static function buildOrderCompanionSelection($pCurrencyExchangeRate, $pCurrencyDecimalPlaces)
	{
		global $gSession;

		$resultArray = array('companiontitle' => '', 'items' => array());

		$companionConfig = UtilsObj::readCompanionConfigFile($gSession['licensekeydata']['groupcode']);

		$companionConfigCount = count($companionConfig['companions']);

		if ($companionConfigCount > 0)
		{
			$resultArray['companiontitle'] = $companionConfig['headerdescription'];

			$companionsInOrderArray = array();

			foreach ($gSession['items'] as $currentLine => $orderLine)
			{
				if ($orderLine['parentorderitemid'] > 0)
				{
					$companionKey = $orderLine['itemproductcode'] . '_' . $orderLine['parentorderitemid'];
					$companionsInOrderArray[$companionKey] = array('qty' => $orderLine['itemqty'], 'orderlineid' => $orderLine['orderlineid']);
				}
			}

			// for each line item check to see if any companion albums have bene configured.
			foreach ($gSession['items'] as $currentLine => &$orderLine)
			{
				$orderLineHasCompanions = false;
				$parentCollectionCode = $gSession['items'][$currentLine]['itemproductcollectioncode'];
				$parentProductCode = $gSession['items'][$currentLine]['itemproductcode'];

				$companionsProductKey = $parentCollectionCode . '.' . $parentProductCode;

				if (array_key_exists($companionsProductKey, $companionConfig['companions']))
				{
					$companionItemArray = array('parentorderlineid' => $orderLine['orderlineid'], 'description' => '', 'items' => array());

					$companionItemArray['description'] = $companionConfig['companions'][$companionsProductKey]['description'];

					$companionsForParentArray = $companionConfig['companions'][$companionsProductKey]['children'];

					foreach ($companionsForParentArray as $productCompanion)
					{
						$companionItem = array();

						$companionCollectionAndLayoutCodeArray = explode('.', $productCompanion['code']);

						$companionCollectionCode = $companionCollectionAndLayoutCodeArray[0];
						$companionLayoutCode = $companionCollectionAndLayoutCodeArray[1];

						$companionLayoutCode = trim($companionLayoutCode);

						$productArray = DatabaseObj::getProductFromCollectionCodeAndLayoutCode($companionCollectionCode, $companionLayoutCode);

						if ($productArray['result'] == '')
						{
							// get the product price
							$itemQty = -1;
							$productPriceArray = DatabaseObj::getProductPrice($companionLayoutCode, $gSession['licensekeydata']['groupcode'],
											$gSession['userdata']['companycode'], $pCurrencyExchangeRate, $pCurrencyDecimalPlaces, $itemQty);

							if ($productPriceArray['result'] == '')
							{
								// if the qty is still -1, force the qty to 1 and recalculate price
								if ($productPriceArray['newqty'] == -1)
								{
									$itemQty = 1;
									$productPriceArray = DatabaseObj::getProductPrice($companionLayoutCode, $gSession['licensekeydata']['groupcode'],
												$gSession['userdata']['companycode'], $pCurrencyExchangeRate, $pCurrencyDecimalPlaces, $itemQty);
								}

								// if there is a valid price which is active and the product is active add the companion album
								if (($productPriceArray['result'] == '') && ($productPriceArray['isactive'] == 1) && ($productArray['isactive'] == 1))
								{
									$companionItem['productcode'] = $productCompanion['code'];
									$companionItem['productname'] = $productArray['name'];
                                    $companionItem['layoutcode'] = $companionLayoutCode;
									$companionItem['totalsell'] = $productCompanion['price'];
									$companionItem['quantityisdropdown'] = $productPriceArray['quantityisdropdown'];
									$companionItem['itemqtydropdown'] = implode(',', $productPriceArray['itemqtydropdown']);
									$companionItem['lowestqtyvalue'] = $productPriceArray['newqty'];
									$companionItem['companionorderlineid'] = 0;
									$companionItem['qty'] = 0;

									$companionLookupKey = $companionLayoutCode . '_' . $orderLine['orderlineid'];

									if (array_key_exists($companionLookupKey, $companionsInOrderArray))
									{
										$companionItem['qty'] = $companionsInOrderArray[$companionLookupKey]['qty'];
										$companionItem['companionorderlineid'] = $companionsInOrderArray[$companionLookupKey]['orderlineid'];
									}

									$companionItemArray['items'][] = $companionItem;
									$orderLineHasCompanions = true;
								}
							}
						}
					}

					if ($orderLineHasCompanions)
					{
						$resultArray['items'][$currentLine] = $companionItemArray;
						$orderLine['itemhascompanions'] = true;
					}
				}
			}

			DatabaseObj::updateSession();
		}

		return $resultArray;
	}

    static function copyArrayAddressFields($pSourceAddressArray, $pSourceKeyPrefix, &$pDestinationArray, $pDestinationKeyPrefix, $pCopyingDefaultAddress, $pAddressFieldsOnly)
	{
		if (! $pAddressFieldsOnly)
		{
			// if the source is empty and destination is 'defaultbilling'
			// or if both the source and destination are the billing address copy the tax number settings
			if (($pSourceKeyPrefix == '' && $pDestinationKeyPrefix == 'defaultbilling') ||
					(strpos($pSourceKeyPrefix, 'billing') !== false) && (strpos($pDestinationKeyPrefix, 'billing') !== false))
			{
				$pDestinationArray[$pDestinationKeyPrefix . 'registeredtaxnumbertype'] = $pSourceAddressArray[$pSourceKeyPrefix . 'registeredtaxnumbertype'];
				$pDestinationArray[$pDestinationKeyPrefix . 'registeredtaxnumber'] = $pSourceAddressArray[$pSourceKeyPrefix . 'registeredtaxnumber'];
			}

			$pDestinationArray[$pDestinationKeyPrefix . 'contactfirstname'] = $pSourceAddressArray[$pSourceKeyPrefix . 'contactfirstname'];
			$pDestinationArray[$pDestinationKeyPrefix . 'contactlastname'] = $pSourceAddressArray[$pSourceKeyPrefix . 'contactlastname'];

			if ($pCopyingDefaultAddress)
			{
				$pSourceKeyPrefix .= 'customer';
				$pDestinationArray[$pDestinationKeyPrefix . 'customername'] = $pSourceAddressArray[$pSourceKeyPrefix . 'name'];
			}
			else
			{
				$pDestinationArray[$pDestinationKeyPrefix . 'customername'] = $pSourceAddressArray[$pSourceKeyPrefix . 'companyname'];
			}
		}

		$pDestinationArray[$pDestinationKeyPrefix . 'customeraddress1'] = $pSourceAddressArray[$pSourceKeyPrefix . 'address1'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customeraddress2'] = $pSourceAddressArray[$pSourceKeyPrefix . 'address2'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customeraddress3'] = $pSourceAddressArray[$pSourceKeyPrefix . 'address3'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customeraddress4'] = $pSourceAddressArray[$pSourceKeyPrefix . 'address4'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customercity'] = $pSourceAddressArray[$pSourceKeyPrefix . 'city'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customercounty'] = $pSourceAddressArray[$pSourceKeyPrefix . 'county'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customerstate'] = $pSourceAddressArray[$pSourceKeyPrefix . 'state'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customerregioncode'] = $pSourceAddressArray[$pSourceKeyPrefix . 'regioncode'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customerregion'] = $pSourceAddressArray[$pSourceKeyPrefix . 'region'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customerpostcode'] = $pSourceAddressArray[$pSourceKeyPrefix . 'postcode'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customercountrycode'] = $pSourceAddressArray[$pSourceKeyPrefix . 'countrycode'];
		$pDestinationArray[$pDestinationKeyPrefix . 'customercountryname'] = $pSourceAddressArray[$pSourceKeyPrefix . 'countryname'];

		if (! $pAddressFieldsOnly)
		{
			$pDestinationArray[$pDestinationKeyPrefix . 'customertelephonenumber'] = $pSourceAddressArray[$pSourceKeyPrefix . 'telephonenumber'];
			$pDestinationArray[$pDestinationKeyPrefix . 'customeremailaddress'] = $pSourceAddressArray[$pSourceKeyPrefix . 'emailaddress'];
		}
	}

    static function copyDefaultAddressToCurrentAddress($pAddressToCopy)
    {
        global $gSession;

        if ($pAddressToCopy == 'billing')
        {
            $ref = &$gSession['order'];
        }
        else
        {
            $ref = &$gSession['shipping'][0];
        }

        self::copyArrayAddressFields($ref, 'default' . $pAddressToCopy, $ref, $pAddressToCopy, true, false);
    }


    static function initializeCartShippingAndBillingAddress($pLicenseKeyArray, $pUserAccountArray)
    {
    	global $gSession;

    	// decide which settings to use, user or licence key
		if ($pUserAccountArray['defaultaddresscontrol'] == 1)
		{
			// the user account uses the default, that means we go by licence key
			$useLicenseKeyForBillingAddress = $pLicenseKeyArray['useaddressforbilling'];
			$useLicenseKeyForShippingAddress = $pLicenseKeyArray['useaddressforshipping'];
			$gSession['order']['canmodifyshippingaddress'] = $pLicenseKeyArray['canmodifyshippingaddress'];
			$gSession['order']['canmodifyshippingcontactdetails'] = $pLicenseKeyArray['canmodifyshippingcontactdetails'];
			$gSession['order']['canmodifybillingaddress'] = $pLicenseKeyArray['canmodifybillingaddress'];
			$gSession['order']['useremaildestination'] = $pLicenseKeyArray['useremaildestination'];
		}
		else
		{
			// use the settings from the user account
			$useLicenseKeyForBillingAddress = $pUserAccountArray['uselicensekeyforbillingaddress'];
			$useLicenseKeyForShippingAddress = $pUserAccountArray['uselicensekeyforshippingaddress'];
			$gSession['order']['canmodifyshippingaddress'] = $pUserAccountArray['canmodifyshippingaddress'];
			$gSession['order']['canmodifyshippingcontactdetails'] = $pUserAccountArray['canmodifyshippingcontactdetails'];
			$gSession['order']['canmodifybillingaddress'] = $pUserAccountArray['canmodifybillingaddress'];
			$gSession['order']['useremaildestination'] = $pUserAccountArray['useremaildestination'];
		}

		if (($pUserAccountArray['canmodifyshippingaddress'] == $pUserAccountArray['canmodifybillingaddress']) &&
			($pUserAccountArray['canmodifyshippingaddress'] == 1))
		{
			$gSession['order']['sameshippingandbillingaddress'] = 1;
		}
		else
		{
			$gSession['order']['sameshippingandbillingaddress'] = 0;
		}

		$gSession['order']['billingcustomeraccountcode'] = $pUserAccountArray['accountcode'];

		if ($useLicenseKeyForBillingAddress == 1)
		{
			self::copyArrayAddressFields($pLicenseKeyArray, '', $gSession['order'], 'defaultbilling', false, false);
		}
		else
		{
			self::copyArrayAddressFields($pUserAccountArray, '', $gSession['order'], 'defaultbilling', false, false);
		}

		if ($useLicenseKeyForShippingAddress == 1)
		{
			self::copyArrayAddressFields($pLicenseKeyArray, '', $gSession['shipping'][0], 'defaultshipping', false, false);
		}
		else
		{
			self::copyArrayAddressFields($pUserAccountArray, '', $gSession['shipping'][0], 'defaultshipping', false, false);
		}

		self::copyDefaultAddressToCurrentAddress('billing');
		self::copyDefaultAddressToCurrentAddress('shipping');
    }

	/**
	 * Returns the path to the orderstatuscache folder.
	 * This will include the filename if a batchref is provided.
	 *
	 * @param string $pBatchRef Batchref to retreive the file path for.
	 * @return string The full path to the orderstatuscache folder.
	 */
	static function calcOrderStatusCachePath($pBatchRef)
	{
		$result = UtilsObj::getOrderStatusCachePath();

		if (($result !== '') && ($pBatchRef !== ''))
		{
			$result .= $pBatchRef . '.inf';
		}

		return $result;
	}

    static function cleanUpOrderStatusCachePath()
    {
        // delete any cache files older than 4 hours
        $cacheFolderPath = self::calcOrderStatusCachePath('');

        if ($cacheFolderPath != '')
        {
            UtilsObj::deleteOldFiles($cacheFolderPath, 240);
        }
    }

    static function writeOrderStatusCacheFile($pValue = '', $pWriteOnlyIfExists = false)
    {
        global $gSession;

        $result = '';

        //ccicachefileneeded is needed to create the cache file for normal orders and reorders
        //primarily for payment gateways
        if (($gSession['order']['origorderid'] == 0) || ($gSession['order']['ccicachefileneeded']))
        {

            $filePath = self::calcOrderStatusCachePath($gSession['items'][0]['itemuploadbatchref']);

            // if we have a file path we write the file based on if one already exists and the parameters provided
            if (($filePath != '') && ((file_exists($filePath)) || (! $pWriteOnlyIfExists)))
            {
                // first delete any existing file
                UtilsObj::deleteFile($filePath);

                // attempt upto 10 times to write the cache file
                $retryCount = 10;
                do
                {
                    if (UtilsObj::writeTextFile($filePath, $pValue))
                    {
                        $result = $filePath;

                        $retryCount = 0;
                    }
                    else
                    {
                        // the file could not be written so retry
                        $retryCount--;
                        usleep(10000);
                    }
                } while ($retryCount > 0);
            }
        }

        return $result;
    }

    /**
     * As we now create a cache file for all orders, when an online order is placed
     * we need to make sure that they are cleaned up. This function will clean up
     * the cache with the batch ref and also clean up any old cache files older
     * than 4 hours.
     * {@param} $pBatchRef is the batch ref of the current order or an empty string
     *  if an empty string is passed it will use the session. This is needed when
     * cancelling an order
     */

    static function deleteOnlineOrderStatusCacheFile($pBatchRef)
    {
        if($pBatchRef != '')
        {
            $filePath = self::calcOrderStatusCachePath($pBatchRef);

            if ($filePath != '')
            {
                UtilsObj::deleteFile($filePath);
            }
        }

        $cachePath = self::calcOrderStatusCachePath('');

        if ($cachePath != '')
        {
            UtilsObj::deleteOldFiles($cachePath, 240);
        }
    }

    static function deleteOrderStatusCacheFile($pBatchRef = '')
    {
        global $gSession;

        $batchRef = '';

        if ($pBatchRef == '')
        {
            if ($gSession['ref'] > 0)
            {
                $batchRef = $gSession['items'][0]['itemuploadbatchref'];
            }
        }
        else
        {
            $batchRef = $pBatchRef;
        }

        if ($batchRef != '')
        {
            $filePath = self::calcOrderStatusCachePath($batchRef);

            if ($filePath != '')
            {
                UtilsObj::deleteFile($filePath);
            }
        }

        // clean-up any orphaned status files
        self::cleanUpOrderStatusCachePath();
    }

    static function getOrderFooterSectionData($pComponentsForOriginalOrder, $pExchangeRate, $pCurrencyDecimalPlaces, $pRebuild = false)
    {
        global $gSession;

        $existingSectionListArray = Array();
        $exisitngCheckBoxListArray = Array();
        $orderFooterCheckboxes = Array();

        foreach ($gSession['items'] as $currentLine => $orderLine)
        {
            $productCode = $orderLine['componenttreeproductcode'];
            $itemPageCount = $orderLine['itempagecount'];
            $itemQty = $orderLine['itemqty'];

            // get list of required sections in order footer
            $sectionList = DatabaseObj::getOrderSectionList($productCode, $gSession['licensekeydata']['groupcode'],
                            $gSession['userdata']['companycode'], '$ORDERFOOTER\\');

            // we need to loop round the sections filtering out duplicates.
            // if there are duplicates then we know this section has been used on more than one product so we need to add to the item qty
            foreach ($sectionList['sections'] as $sectionCode)
            {
                if (array_key_exists($sectionCode, $existingSectionListArray))
                {
                    $existingSectionListArray[$sectionCode]['qty'] += $itemQty;
                    $existingSectionListArray[$sectionCode]['pagecount'] += $itemPageCount;
                }
                else
                {
                    $existingSectionListArray[$sectionCode] = Array('qty' => $itemQty, 'productcode' => $productCode, 'pagecount' => $itemPageCount);
                }
            }

            // get list of checkboxes in order footer
            // we need to loop round the sections filtering out duplicates.
            // if there are duplicates then we know this section has been used on more than one product so we need to add to the item qty
            $checkboxList = DatabaseObj::getOrderCheckboxes($productCode, $gSession['licensekeydata']['groupcode'],
                            $gSession['userdata']['companycode'], '$ORDERFOOTER\\', $pExchangeRate, $pCurrencyDecimalPlaces, -1,
                            $itemPageCount, -1, $pRebuild);

            $i = 0;

            foreach ($checkboxList['checkboxes'] as $checkbox)
            {
                if (array_key_exists($checkbox['code'], $exisitngCheckBoxListArray))
                {
                    $exisitngCheckBoxListArray[$checkbox['code']]['qty'] += $itemQty;
                    $exisitngCheckBoxListArray[$checkbox['code']]['pagecount'] += $itemPageCount;
                }
                else
                {
                    $exisitngCheckBoxListArray[$checkbox['code']] = Array('productcode' => $productCode, 'qty' => $itemQty, 'pagecount' => $itemPageCount);
                    $i++;
                }
            }
        }

        // we have filtered out the duplicates for the sections so we pass the accumlative itemqty so we can calculate the price correctly
        foreach ($existingSectionListArray as $sectionCode => $listItem)
        {
            $itemQty = $listItem['qty'];
            $itemPageCount = $listItem['pagecount'];
            $productCode = $listItem['productcode'];

			// if this is not a reorder then we must find the default component for the particular section given
			if (count($pComponentsForOriginalOrder) == 0)
			{
				 $section = &DatabaseObj::getSessionOrderSection(-1, $productCode, '$ORDERFOOTER\\$' . $sectionCode . '\\', $pExchangeRate,
                            $pCurrencyDecimalPlaces, $itemQty, true, '', $itemPageCount);
			}

            // on reorder select components selected in the original order
            for ($j = 0; $j < count($pComponentsForOriginalOrder); $j++)
            {
                $itemOriginalComponent = $pComponentsForOriginalOrder[$j];

                $section = &DatabaseObj::getSessionOrderSection(-1, $productCode, '$ORDERFOOTER\\$' . $sectionCode . '\\', $pExchangeRate,
                            $pCurrencyDecimalPlaces, $itemQty, true, $itemOriginalComponent['componentcode'], $itemPageCount);

                if ($section['path'] == $itemOriginalComponent['componentpath'])
                {
                    $section['code'] = $itemOriginalComponent['componentcode'];
                    self::updateOneOrderSection($currentLine);
                }
            }
        }

        // we have filtered out the duplicates for the checkboxes so we pass the accumlative itemqty & pagecount so we can calculate the price correctly
        // here we build the $orderFooterCheckboxes array that contains the checkboxes for each product.
        foreach ($exisitngCheckBoxListArray as $checkbox)
        {
            $checkBoxProductCode = $checkbox['productcode'];
            $checkBoxQty = $checkbox['qty'];
            $checkBoxPageCount = $checkbox['pagecount'];

            $checkboxList = DatabaseObj::getOrderCheckboxes($checkBoxProductCode, $gSession['licensekeydata']['groupcode'],
                            $gSession['userdata']['companycode'], '$ORDERFOOTER\\', $pExchangeRate, $pCurrencyDecimalPlaces, $checkBoxQty,
                            $checkBoxPageCount, -1, $pRebuild);
            $orderFooterCheckboxes[] = $checkboxList['checkboxes'];
        }

        // we now need to restructure the array correctly so it can be assigned to the $gSession['order']['orderFooterCheckboxes'] correctly.
        $checkboxList = Array();
        $exisitngCheckBoxListArray = Array();

        foreach ($orderFooterCheckboxes as $checkboxArrayItem)
        {
            foreach ($checkboxArrayItem as $checkbox)
            {
                if (!array_key_exists($checkbox['code'], $exisitngCheckBoxListArray))
                {
                    $checkboxList[] = $checkbox;
                    $exisitngCheckBoxListArray[$checkbox['code']] = $checkbox['code'];
                }
            }
        }

        // on reorder restore state of the checkboxes in  order footer
        if (isset($gSession['order']) && $gSession['order']['isreorder'] == 1)
        {
            $orderCheckboxes = $checkboxList;
            for ($i = 0; $i < count($orderCheckboxes); $i++)
            {
                $itemComponent = $orderCheckboxes[$i];

                for ($j = 0; $j < count($pComponentsForOriginalOrder); $j++)
                {
                    $itemOriginalComponent = $pComponentsForOriginalOrder[$j];
                    if (($itemComponent['code'] == $itemOriginalComponent['componentcode']) && ($itemComponent['path'] == $itemOriginalComponent['componentpath']))
                    {
                        if (array_key_exists('checked', $itemComponent))
                        {
                            $checkboxList[$i]['checked'] = $itemOriginalComponent['checkboxselected'];
                            break;
                        }
                    }
                }
            }
        }

        //(Added "&& !empty($checkboxList['checkboxes']" to assign the checkbox to orderFoooterCheckboxes only if the current product has a checkbox )
        // otherwise it will asign an empty array to orderFooterCheckboxes if the current product doesn't have a checkbox on orderfooter.
        if (!empty($checkboxList))
        {
            $gSession['order']['orderFooterCheckboxes'] = $checkboxList;
        }
    }

    static function orderSessionInitialize($pLanguageCode, $pAppVersion, $pWebBrandCode, $pShoppingCartType, $pUUID, $pJobTicketTemplate,
            $pShowPricesWithTax, $pShowTaxBreakDown, $pShowZeroTax, $pShowAlwaysTaxTotal, $pOwnerCode, $pGroupCode, $pGroupData,
            $pGroupName, $pGroupAddress1, $pGroupAddress2, $pGroupAddress3, $pGroupAddress4, $pGroupAddressCity, $pGroupAddressCounty,
            $pGroupAddressState, $pGroupPostCode, $pGroupCountryCode, $pGroupTelephoneNumber, $pGroupEmailAddress, $pGroupContactFirstName,
            $pGroupContactLastName, $pUploadBatchRef, $pCartItemsArray, $pBasketAPIWorkFlowType, $pHighLevelBasketRef, $pCreateSession)
    {
		global $gSession;
		global $ac_config;


        $resultArray = array();

        $gSession['applanguagecode'] = $pLanguageCode;
        $gSession['appversion'] = $pAppVersion;
        AuthenticateObj::setSessionWebBrand($pWebBrandCode);

        $gSession['order'] = AuthenticateObj::createSessionOrderData();
        $gSession['shipping'] = Array(AuthenticateObj::createSessionShippingLine());

        $gSession['order']['uuid'] = $pUUID;
        $gSession['order']['isreorder'] = 0;
        $gSession['order']['origorderid'] = 0;
        $gSession['order']['origordernumber'] = 0;
        $gSession['order']['basketapiworkflowtype'] = $pBasketAPIWorkFlowType;
        $gSession['order']['shoppingcarttype'] = $pShoppingCartType;
        $gSession['order']['jobtickettemplate'] = $pJobTicketTemplate;
        $gSession['order']['showpriceswithtax'] = $pShowPricesWithTax;
        $gSession['order']['showtaxbreakdown'] = $pShowTaxBreakDown;
        $gSession['order']['showzerotax'] = $pShowZeroTax;
		$gSession['order']['showalwaystaxtotal'] = $pShowAlwaysTaxTotal;
		$gSession['order']['uselegacypricingsystem'] = (UtilsObj::getArrayParam($ac_config,'USELEGACYPRICINGSYSTEM', 0) == 1);

        $gSession['licensekeydata']['ownercode'] = $pOwnerCode;
        $gSession['licensekeydata']['groupcode'] = $pGroupCode;
        $gSession['licensekeydata']['groupdata'] = $pGroupData;
        $gSession['licensekeydata']['groupname'] = $pGroupName;
        $gSession['licensekeydata']['groupaddress1'] = $pGroupAddress1;
        $gSession['licensekeydata']['groupaddress2'] = $pGroupAddress2;
        $gSession['licensekeydata']['groupaddress3'] = $pGroupAddress3;
        $gSession['licensekeydata']['groupaddress4'] = $pGroupAddress4;
        $gSession['licensekeydata']['groupcity'] = $pGroupAddressCity;
        $gSession['licensekeydata']['groupcounty'] = $pGroupAddressCounty;
        $gSession['licensekeydata']['groupstate'] = $pGroupAddressState;
        $gSession['licensekeydata']['grouppostcode'] = $pGroupPostCode;
        $gSession['licensekeydata']['groupcountrycode'] = $pGroupCountryCode;
        $gSession['licensekeydata']['grouptelephonenumber'] = $pGroupTelephoneNumber;
        $gSession['licensekeydata']['groupemailaddress'] = $pGroupEmailAddress;
        $gSession['licensekeydata']['groupcontactfirstname'] = $pGroupContactFirstName;
        $gSession['licensekeydata']['groupcontactlastname'] = $pGroupContactLastName;

        $gSession['items'] = Array();
        $itemCount = count($pCartItemsArray);
        for ($i = 0; $i < $itemCount; $i++)
        {
            $cartItemArray = $pCartItemsArray[$i];

            $orderItemArray = AuthenticateObj::createSessionOrderLine();

            $orderItemArray['orderlineid'] = Order_model::getNextOrderLineId();
            $orderItemArray['itemshareid'] = $cartItemArray['shareid'];
            $orderItemArray['source'] = $cartItemArray['source'];
            $orderItemArray['productoptions'] = $cartItemArray['productoptions'];
			$orderItemArray['pricetransformationstage'] = $cartItemArray['pricetransformationstage'];
            $orderItemArray['itemuploadgroupcode'] = $cartItemArray['uploadgroupcode'];
            $orderItemArray['itemuploadorderid'] = $cartItemArray['uploadorderid'];
            $orderItemArray['itemuploadordernumber'] = $cartItemArray['uploadordernumber'];
            $orderItemArray['itemuploadorderitemid'] = $cartItemArray['uploadorderitemid'];
            $orderItemArray['itemuploadbatchref'] = $pUploadBatchRef;
            $orderItemArray['itemuploadref'] = $cartItemArray['uploadref'];
            $orderItemArray['itemproductcollectioncode'] = $cartItemArray['collectioncode'];
            $orderItemArray['itemproductcollectionname'] = $cartItemArray['collectionname'];
            $orderItemArray['itemproductcode'] = $cartItemArray['productcode'];
            $orderItemArray['itemproductskucode'] = $cartItemArray['productskucode'];
            $orderItemArray['itemproductname'] = $cartItemArray['productname'];
            $orderItemArray['itemproducttype'] = $cartItemArray['producttype'];
            $orderItemArray['itemproductpageformat'] = $cartItemArray['productpageformat'];
            $orderItemArray['itemproductspreadpageformat'] = $cartItemArray['productspreadformat'];
            $orderItemArray['itemproductcover1format'] = $cartItemArray['productcover1format'];
            $orderItemArray['itemproductcover2format'] = $cartItemArray['productcover2format'];
            $orderItemArray['itemproductoutputformat'] = $cartItemArray['productoutputformat'];
            $orderItemArray['itemproductheight'] = $cartItemArray['productheight'];
            $orderItemArray['itemproductwidth'] = $cartItemArray['productwidth'];
            $orderItemArray['itemproductdefaultpagecount'] = $cartItemArray['productdefaultpagecount'];
            $orderItemArray['itemproductinfo'] = '';
            $orderItemArray['itemprojectref'] = $cartItemArray['projectref'];
            $orderItemArray['itemprojectreforig'] = $cartItemArray['projectreforig'];
            $orderItemArray['itemprojectname'] = $cartItemArray['projectname'];
            $orderItemArray['itemprojectstarttime'] = $cartItemArray['projectstarttime'];
            $orderItemArray['itemprojectduration'] = $cartItemArray['projectduration'];
            $orderItemArray['itempagecount'] = $cartItemArray['pagecount'];
            $orderItemArray['itemproducttaxlevel'] = $cartItemArray['producttaxlevel'];
            $orderItemArray['itemunitcost'] = $cartItemArray['productunitcost'];
            $orderItemArray['itemunitsell'] = 0.00;
            $orderItemArray['itemproductunitweight'] = $cartItemArray['productunitweight'];
            $orderItemArray['itemproducttotalweight'] = 0.0000;
            $orderItemArray['itemtaxcode'] = '';
            $orderItemArray['itemtaxname'] = '';
            $orderItemArray['itemtaxrate'] = 0.00;
            $orderItemArray['itemtotalweight'] = 0.0000;
            $orderItemArray['itemqty'] = 0;
            $orderItemArray['itemtotalcost'] = 0.00;
            $orderItemArray['itemtotalsell'] = 0.00;
            $orderItemArray['itemtaxtotal'] = 0.00;
            $orderItemArray['itemuploadappversion'] = $cartItemArray['uploadappversion'];
            $orderItemArray['itemuploadappplatform'] = $cartItemArray['uploadappplatform'];
            $orderItemArray['itemuploadappcputype'] = $cartItemArray['uploadappcputype'];
            $orderItemArray['itemuploadapposversion'] = $cartItemArray['uploadapposversion'];
            $orderItemArray['itemexternalassets'] = $cartItemArray['externalassets'];
            $orderItemArray['pictures'] = $cartItemArray['pictures'];
			$orderItemArray['calendarcustomisations'] = $cartItemArray['calendarcustomisations'];
            $orderItemArray['componenttreeproductcode'] = $cartItemArray['componenttreeproductcode'];

			if (array_key_exists('aicomponent', $cartItemArray))
			{
				$orderItemArray['aicomponent'] = $cartItemArray['aicomponent'];
			}

            if (array_key_exists('components', $cartItemArray))
			{
				$orderItemArray['components'] = $cartItemArray['components'];
			}

            $orderItemArray['previewsonline'] = $cartItemArray['previewsonline'];
            $orderItemArray['canupload'] = $cartItemArray['canupload'];

            $orderItemArray['itemuploaddatasize'] = $cartItemArray['uploaddatasize'];
            $orderItemArray['itemuploadduration'] = $cartItemArray['uploadduration'];
            $orderItemArray['metadata'] = Array();

            if (empty($orderItemArray['pricetransformationstage'])) {
				$orderItemArray['pricetransformationstage'] = TPX_PRICETRANSFORMATIONSTAGE_POST;
			}

            // save default cover and paper codes in item so they are available in order.initialize
            // we don't know yet what sections will be needed, this will be determined in order.initialize
            $orderItemArray['covercode'] = $cartItemArray['covercode'];
            $orderItemArray['papercode'] = $cartItemArray['papercode'];

            $orderItemArray['itemproductcollectionorigownercode'] = $cartItemArray['productcollectionorigownercode'];

            // Only assign the itemaimode if the projectaimode is in the cartItemArray.
            if (array_key_exists('projectaimode', $cartItemArray))
            {
                $orderItemArray['itemaimode'] = $cartItemArray['projectaimode'];
            }

            $gSession['items'][] = $orderItemArray;
        }

        if ($pCreateSession)
        {
            $resultArray = DatabaseObj::insertOrderSessionDataRecord($gSession['items'][0]['itemprojectref'], $pHighLevelBasketRef);

            if ($resultArray['result'] == '')
            {
                $gSession['ref'] = $resultArray['ref'];
            }
        }

        return $resultArray;
    }

    static function duplicate($pOrderLineId)
    {
        global $gSession;

        $result = '';
        $resultParam = '';
		$resultArray = array();

        if ($gSession['ref'] > 0)
        {
            // duplicate order specified order item
            // $pOrderLineId is the orderlineid
            $itemIndex = 0;

            // get orderline index based on id
            foreach ($gSession['items'] as $index => $item)
            {
                if ($item['orderlineid'] == $pOrderLineId)
                {
                    $itemIndex = $index;
                }
            }

            // copy order line
            $lastOrderItem = $gSession['items'][$itemIndex];

            // update ordelineid
            $newOrderLineId = self::getNextOrderLineId();
            $lastOrderItem['orderlineid'] = $newOrderLineId;

            // set ordelineid in sections
            foreach ($lastOrderItem['sections'] as &$item)
            {
                $item['orderlineid'] = $newOrderLineId . '_' . self::getNextOrderLineId();
            }

            // insert into order
            array_splice($gSession['items'], $itemIndex + 1, 0, array($lastOrderItem));

            // re-check the voucher to make sure its usage status hasn't changed
            self::checkVoucher();

            // update the totals
            self::updateOrderTotal();
            DatabaseObj::updateSession();
        }
        else
        {
            $result = 'str_ErrorNoSessionRef';
        }

        self::formatOrderAddresses($resultArray);
        $resultArray['metadata'] = self::buildOrderMetaData('qty');
        $resultArray['custominit'] = '';
        $resultArray['previousstage'] = 'qty';
        $resultArray['stage'] = 'qty';

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
    }

    static function remove($pOrderLineId)
    {
        global $gSession;

        $result = '';
        $resultParam = '';
		$resultArray = array();

        if ($gSession['ref'] > 0)
        {
            // duplicate order specified order item
            // $pOrderLineId is the orderlineid
            $itemIndex = 0;

            // get orderline index based on id
            foreach ($gSession['items'] as $index => $item)
            {
                if ($item['orderlineid'] == $pOrderLineId)
                {
                    $itemIndex = $index;
                }
            }

            // remove from order
            array_splice($gSession['items'], $itemIndex, 1, array());

            // re-check the voucher to make sure its usage status hasn't changed
            self::checkVoucher();

            // update the totals
            self::updateOrderTotal();
            DatabaseObj::updateSession();
        }
        else
        {
            $result = 'str_ErrorNoSessionRef';
        }

        self::formatOrderAddresses($resultArray);
        $resultArray['metadata'] = self::buildOrderMetaData('qty');
        $resultArray['custominit'] = '';
        $resultArray['previousstage'] = 'qty';
        $resultArray['stage'] = 'qty';

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
    }

    static function revive($pOfflineOrder)
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
        $resultParam = '';

        $sessionRef = AuthenticateObj::getSessionRef();

        if ($sessionRef > 0)
        {
            $sessionRevived = DatabaseObj::reviveSession($sessionRef, $pOfflineOrder);

            if ($sessionRevived == true)
            {
                // we have revived the session
                // now load the session as the global session
                $gSession = DatabaseObj::getSessionData($sessionRef);

                $gSession['order']['defaultdiscount'] = Array();
				$gSession['order']['defaultdiscountactive'] = true;

                UtilsObj::setSessionDeviceData();

                // determine what we need to do based on the state of the session
                if ($pOfflineOrder == true)
                {
                    // determine if the order is being completed internally or by the customer
                    $gSession['order']['isofflineordercompletedbycustomer'] = UtilsObj::getGETParam('internal', 1);

                    if ($gSession['userid'] == 0)
                    {
                        // if this is an offline order and no user account has been connected then we need to reset the initialization status
                        // this causes the order to be re-initialized when the user logs in for the first time
                        $gSession['order']['initialized'] = 0;
                    }
                    else
                    {
                        // this is an offline order that has been connected to a user so we can call the order initialization here
                        $resultArray = self::initialize();

                        $result = $resultArray['result'];
                        $resultParam = $resultArray['resultparam'];
                    }

                    DatabaseObj::updateSession();
                }
                else
                {
                    // this is a pay later order so we can call the order initialization here
                    $resultArray = self::initialize();

                    $result = $resultArray['result'];
                    $resultParam = $resultArray['resultparam'];
                }
            }
            else
            {
                $result = 'str_ErrorNoSessionRef';
            }
        }
        else
        {
            $result = 'str_ErrorNoSessionRef';
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['userid'] = $resultParam;

        return $resultArray;
    }

    static function formatOrderAddresses(&$pResultArray)
    {
        global $gSession;

        if ($gSession['shipping'][0]['shippingmethodcode'] != '')
        {
            if ($gSession['shipping'][0]['shippingMethods'][$gSession['shipping'][0]['shippingmethodcode']]['collectFromStore'])
            {
                $pResultArray['formattedshippingaddress'] = UtilsAddressObj::formatAddress($gSession['shipping'][0]['shippingMethods'][$gSession['shipping'][0]['shippingmethodcode']]['storeAddress'],
                                'store', '<br />');
            }
            else
            {
                $pResultArray['formattedshippingaddress'] = UtilsAddressObj::formatAddress($gSession['shipping'][0], 'shipping', '<br />');
            }
        }
        else
        {
            $pResultArray['formattedshippingaddress'] = UtilsAddressObj::formatAddress($gSession['shipping'][0], 'shipping', '<br />');
        }
        $pResultArray['formattedbillingaddress'] = UtilsAddressObj::formatAddress($gSession['order'], 'billing', '<br />');
    }

    static function calcDiscountedValue($pItemQty, $pMinQty, $pItemValue, $pDiscountSection, $pDiscountType, $pDiscountValue)
    {
        global $gSession;

        switch ($pDiscountType)
        {
            case 'FOC': // free of charge
                $discountValue = $pItemValue;
                break;
            case 'VALUE':
                if ($pDiscountValue <= $pItemValue)
                {
                    $discountValue = UtilsObj::bround($pDiscountValue, $gSession['order']['currencydecimalplaces']);
                }
                else
                {
                    $discountValue = $pItemValue;
                }
                break;
            case 'PERCENT':
                $discountValue = UtilsObj::bround($pItemValue * $pDiscountValue / 100, $gSession['order']['currencydecimalplaces']);
                break;
            case 'BOGOF':
                $unitValue = $pItemValue / $pItemQty;
                $freeQty = floor($pItemQty / ($pMinQty + 1));

                $discountValue = UtilsObj::bround($unitValue * $freeQty, $gSession['order']['currencydecimalplaces']);
                break;
            case 'BOGPOFF':
                $unitValue = $pItemValue / $pItemQty;
                $discountOff = floor($pItemQty / ($pMinQty + 1)) * $unitValue;

                $discountValue = UtilsObj::bround($discountOff * $pDiscountValue / 100, $gSession['order']['currencydecimalplaces']);
                break;
            case 'BOGVOFF':
                $unitValue = $pItemValue / $pItemQty;

                if ($pDiscountValue > $unitValue)
                {
                    $pDiscountValue = $unitValue;
                }
                $discountOff = floor($pItemQty / ($pMinQty + 1)) * $pDiscountValue;

                $discountValue = UtilsObj::bround($discountOff, $gSession['order']['currencydecimalplaces']);
                break;
            case 'VALUESET':
                if ($pDiscountSection == 'PRODUCT')
                {
                    $newValue = $pDiscountValue * $pItemQty;
                }
                else
                {
                    $newValue = $pDiscountValue;
                }

                if ($newValue > $pItemValue)
                {
                    $newValue = $pItemValue;
                }

                $discountValue = UtilsObj::bround($pItemValue - $newValue, $gSession['order']['currencydecimalplaces']);
                break;
            default:
                $discountValue = 0.00;
        }

        return $discountValue;
    }

    /**
     * calcDiscountedValue2
     *
     * calculate the discount to apply based on a miximum qty to apply the discount to
     * ie, 7 items in the order line apply discount to a maximum of 3, not all 7.
     *
     * @return: discount value
     * $pQtyToDiscount passed by ref and modified within the function
     *
     */
    static function calcDiscountedValue2($pItemQty, $pMinQty, $pItemValue, $pDiscountSection, $pDiscountType, $pDiscountValue, &$pQtyToDiscount)
    {
        global $gSession;

        // calculate the unit price based on the Qty
        $unitValue = $pItemValue;
        if ($pItemQty != 0)
        {
            $unitValue = $pItemValue / $pItemQty;
        }

        switch ($pDiscountType)
        {
            case 'FOC': // free of charge
                $freeQty = min($pItemQty, $pQtyToDiscount);
                $pQtyToDiscount -= $freeQty;

                $discountValue = ($unitValue * $freeQty);
                break;
            case 'VALUE': // discount order line by $pDiscountValue (does not use the qty)
                $discQty = min($pItemQty, $pQtyToDiscount);
                $pQtyToDiscount -= $discQty;

                $discountValue = min($pItemValue, $pDiscountValue);
                break;
            case 'PERCENT': // discount order line by $pDiscountValue %
                $discQty = min($pItemQty, $pQtyToDiscount);
                $pQtyToDiscount -= $discQty;

                $discountOff = $discQty * $unitValue;

                $discountValue = ($discountOff * $pDiscountValue / 100);
                break;
            case 'BOGOF':
                $freeQty = floor($pItemQty / ($pMinQty + 1));

                $freeQty = min($freeQty, $pQtyToDiscount);
                $pQtyToDiscount -= $freeQty;

                $discountValue = ($unitValue * $freeQty);
                break;
            case 'BOGPOFF':
                $discQty = floor($pItemQty / ($pMinQty + 1));
                $discQty = min($discQty, $pQtyToDiscount);
                $pQtyToDiscount -= $discQty;

                $discountOff = $discQty * $unitValue;

                $discountValue = ($discountOff * $pDiscountValue / 100);
                break;
            case 'BOGVOFF':
                if ($pDiscountValue > $unitValue)
                {
                    $pDiscountValue = $unitValue;
                }

                $discQty = floor($pItemQty / ($pMinQty + 1));
                $discQty = min($discQty, $pQtyToDiscount);
                $pQtyToDiscount -= $discQty;

                $discountValue = ($discQty * $pDiscountValue);
                break;
            case 'VALUESET':
                if (($pDiscountSection == 'PRODUCT') || (($pDiscountSection == 'TOTAL') && ($pItemQty > $pQtyToDiscount)))
                {
                    $discQty = min($pItemQty, $pQtyToDiscount);
                    $pQtyToDiscount -= $discQty;

                    // $newValue = ($pItemValue - ($unitValue * $discQty)) + ($pDiscountValue * $discQty);
                    $newValue = ($pItemValue - ($unitValue * $discQty)) + $pDiscountValue;
                }
                else
                {

                    $newValue = $pDiscountValue;
                }

                if ($newValue > $pItemValue)
                {
                    $newValue = $pItemValue;
                }

                $discountValue = ($pItemValue - $newValue);

                break;
            default:
                $discountValue = 0.00;
        }

        return UtilsObj::bround($discountValue, $gSession['order']['currencydecimalplaces']);
    }

    /**
     * Updates a section of an order line on reorder.
     *
     * @param string $pOrderLineId -> orderline id of order item
     * @param string $pSectionOrderLineId -> section code
     * @param string $pComponentCode -> component code
     * @param string $pComponentLocalCode -> local code of component
     * @param array $pComponentsFromOriginalOrder -> array with all selected section
     */
    static function updateComponentReorder($pOrderLineId, $pSectionOrderLineId, $pComponentCode, $pComponentLocalCode,
            $pComponentsFromOriginalOrder)
    {
        global $gSession;

        if ($pOrderLineId == TPX_ORDERFOOTER_ID)
        {
            $section = &DatabaseObj::getSectionByOrderLineId($pSectionOrderLineId);
            $section['code'] = $pComponentCode;
            $section['localcode'] = $pComponentLocalCode;

            self::getOrderFooterSectionData(Array(), $gSession['order']['currencyexchangerate'], $gSession['order']['currencydecimalplaces']);
            self::updateOneOrderSection(TPX_ORDERFOOTER_ID);

            self::updateOrderTotal();
            DatabaseObj::updateSession();
        }
        else
        {

            // get orderline index based on id
            foreach ($gSession['items'] as $index => $item)
            {
                if ($item['orderlineid'] == $pOrderLineId)
                {
                    $section = &DatabaseObj::getSectionByOrderLineId($pSectionOrderLineId);
                    $section['code'] = $pComponentCode;
                    $section['localcode'] = $pComponentLocalCode;

                    // we need to check if we are changing a component that belongs to a section in the LINEFOOTER section
                    $parentPathElements = explode('\\', $section['path']);

                    if ($parentPathElements[0] == '$LINEFOOTER')
                    {
                        $parentSection = '$LINEFOOTER\\';
                    }
                    else
                    {
                        $parentSection = '';
                    }

                    // since the component has changed we need to re-build everything that belongs to it
                    $sectionList = DatabaseObj::getOrderSectionList($item['componenttreeproductcode'], $gSession['licensekeydata']['groupcode'],
                                    $gSession['userdata']['companycode'], $parentSection);
                    foreach ($sectionList['sections'] as $sectionCode)
                    {
                        $section = &DatabaseObj::getSessionOrderSection($index, $item['componenttreeproductcode'],
                                        $parentSection . '$' . $sectionCode . '\\', $gSession['order']['currencyexchangerate'],
                                        $gSession['order']['currencydecimalplaces'], $item['itemqty'], true);
                    }

                    // change the default value for a selected one on subsection
                    $iCountComp = count($pComponentsFromOriginalOrder);
                    foreach ($gSession['items'][$index]['sections'] as $sKey => $aSection)
                    {
                        foreach ($aSection['subsections'] as $sSubKey => $aSubsection)
                        {
                            for ($j = 0; $j < $iCountComp; $j++)
                            {
                                $itemOriginalComponent = $pComponentsFromOriginalOrder[$j];
                                if ($aSubsection['path'] == $itemOriginalComponent['componentpath'])
                                {
                                    $gSession['items'][$index]['sections'][$sKey]['subsections'][$sSubKey]['code'] = $itemOriginalComponent['componentcode'];
                                }
                            }
                        }
                    }

                    self::updateOneOrderSection($index);
                    self::updateOrderTotal();
                    DatabaseObj::updateSession();
                }
            }
        }
    }

    /**
     * Calculates total of a checkbox array.
     *
     * @static
     *
     * @param array     $pCheckboxes
     * @param string        $pProductCode
     * @param float     $pCurrencyExchangeRate
     * @param integer   $pCurrencyDecimalPlaces
     * @param integer   $pItemQty
     * @param integer   $pPageCount
     *
     * @author Steffen Haugk
     * @since Version 3.0.0
     */
    static function updateCheckboxTotal(&$pCheckbox, $pProductCode, $pCurrencyExchangeRate, $pCurrencyDecimalPlaces, $pItemQty, $pPageCount,
           $pOrderItemLineId)
    {
        global $gSession;

        $checkboxUnitSell = 0;
        $checkboxTotalCost = 0;
        $checkboxTotalSell = 0;
        $checkboxTotalTax = 0;
        $checkboxTotalSellNoTax = 0;
        $checkboxTotalSellWithTax = 0;
        $checkboxTotalWeight = 0;
        $taxRate = 0.00;
        $taxCode = '';
        $taxName = '';

        $exisitingCheckbox = DatabaseObj::getSectionByOrderLineId($pCheckbox['orderlineid']);

        $checkboxPriceArray = DatabaseObj::getPrice($pCheckbox['path'], $pCheckbox['code'], false, $pProductCode,
                        $gSession['licensekeydata']['groupcode'], $gSession['userdata']['companycode'], $pCurrencyExchangeRate,
                        $pCurrencyDecimalPlaces, $pItemQty, $pPageCount, $pCheckbox['quantity'], $pCheckbox['quantity'], true, true, -1, 0, '', true);

        if ($checkboxPriceArray['result'] == '')
        {
            $pCheckbox['itemqtydropdown'] = $checkboxPriceArray['itemqtydropdown'];
            $pCheckbox['quantity'] = $checkboxPriceArray['newqty'];
            $pCheckbox['pricetaxcode'] = $checkboxPriceArray['taxcode'];
            $pCheckbox['pricetaxrate'] = $checkboxPriceArray['taxrate'];

            switch ($checkboxPriceArray['pricingmodel'])
            {
                case TPX_PRICINGMODEL_PERQTY:
                    $checkboxTotalWeight = $pItemQty * $pCheckbox['unitweight'];
                    $checkboxTotalCost = $pItemQty * $pCheckbox['unitcost'];
                    break;
                case TPX_PRICINGMODEL_PERSIDEQTY:
                    $checkboxTotalWeight = ($pItemQty * $pPageCount) * $pCheckbox['unitweight'];
                    $checkboxTotalCost = ($pItemQty * $pPageCount) * $pCheckbox['unitcost'];
                    break;
                case TPX_PRICINGMODEL_PERPRODCMPQTY:
                    $checkboxTotalWeight = $pItemQty * ($pCheckbox['quantity'] * $pCheckbox['unitweight']);
                    $checkboxTotalCost = $pItemQty * ($pCheckbox['quantity'] * $pCheckbox['unitcost']);
                    break;
                case TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY:
                    $checkboxTotalWeight = ($pCheckbox['quantity'] * $pPageCount) * $pCheckbox['unitweight'];
                    $checkboxTotalWeight = $pItemQty * $checkboxTotalWeight;

                    $checkboxTotalCost = ($pCheckbox['quantity'] * $pPageCount) * $pCheckbox['unitcost'];
                    $checkboxTotalCost = $pItemQty * $checkboxTotalCost;
                    break;
            }

            $checkboxTotalSell = $checkboxPriceArray['totalsell'];
            $checkboxUnitSell = $checkboxPriceArray['unitsell'];

            $pCheckbox['hasprice'] = 1;
            $pCheckbox['componenthasprice'] = 1;
        }
        else
        {
            $pCheckbox['componenthasprice'] = 0;
            $pCheckbox['hasprice'] = 0;
        }


        $componentArray = DatabaseObj::getComponentByCode($pCheckbox['code']);

        if ($componentArray['keywordgroupheaderid'] > 0 && $pCheckbox['componenthasprice'] == 1)
        {
            $pCheckbox['metadata'] = MetaDataObj::getKeywordList('COMPONENT', '', '', $componentArray['keywordgroupheaderid']);
        }
        else
        {
            $pCheckbox['metadata'] = Array();
        }

        foreach ($pCheckbox['metadata'] as &$metadataItem)
        {
            $componentOldMetadata = $exisitingCheckbox['metadata'];
            foreach ($componentOldMetadata as &$oldMetadataItem)
            {
                if ($metadataItem['ref'] == $oldMetadataItem['ref'])
                {
                    $metadataItem['defaultvalue'] = $oldMetadataItem['defaultvalue'];
                    break;
                }
            }
        }

        // check to see if the component belongs to the order footer.
        // If so then we need to grab the taxrate from the session based on the orderfooterlevel set on the component
        if ($pOrderItemLineId == TPX_ORDERFOOTER_ID)
        {
            if ($gSession['order']['orderalltaxratesequal'] == 1)
            {
                $taxRateArray = DatabaseObj::getTaxRate($gSession['order']['fixedtaxrate']);
            }
            else
            {
                $taxRateArray = self::getTaxRateArrayFromTaxLevel($pCheckbox['orderfootertaxlevel']);
            }

            $taxRate = $taxRateArray['rate'];
            $taxCode = $taxRateArray['code'];
            $taxName = $taxRateArray['name'];
        }
        else
        {
            $taxRate = $gSession['items'][$pOrderItemLineId]['itemtaxrate'];
            $taxCode = $gSession['items'][$pOrderItemLineId]['itemtaxcode'];
            $taxName = $gSession['items'][$pOrderItemLineId]['itemtaxname'];
        }

        // determine the tax status for the component
        if ($pCheckbox['pricetaxcode'] != '')
        {
            // tax is included in the price so determine the price without tax
            $checkboxTotalSellNoTax = UtilsObj::bround(($checkboxTotalSell / ($pCheckbox['pricetaxrate'] + 100)) * 100,
                            $gSession['order']['currencydecimalplaces']);
            $checkboxTotalTax = $checkboxTotalSell - $checkboxTotalSellNoTax;

            if ($pCheckbox['pricetaxrate'] != $taxRate)
            {
                // if the tax included in the price is different to the line tax then we use the price without tax as we will be adding it later
                $checkboxTotalSell = $checkboxTotalSellNoTax;
                $totalTax = UtilsObj::bround($checkboxTotalSell * $taxRate / 100, $pCurrencyDecimalPlaces);
                $checkboxTotalSellWithTax = $checkboxTotalSell + $totalTax;
            }
            else
            {
                // tax is already calculated
                $checkboxTotalSellWithTax = $checkboxTotalSell;
            }
        }
        else
        {
            // no tax was included in the price
            $checkboxTotalTax = UtilsObj::bround($checkboxTotalSell * $taxRate / 100, $pCurrencyDecimalPlaces);
            $checkboxTotalSellWithTax = $checkboxTotalSell + $checkboxTotalTax;
            $checkboxTotalSellNoTax = $checkboxTotalSell;
        }

        // add the tax at this point if necessary
        if ($gSession['order']['showpriceswithtax'] == 1)
        {
            $checkboxTotalSell = $checkboxTotalSellWithTax;
        }
        else
        {
            $checkboxTotalSell = $checkboxTotalSellNoTax;
        }


        $pCheckbox['orderfootertaxrate'] = $taxRate;
        $pCheckbox['orderfootertaxcode'] = $taxCode;
        $pCheckbox['orderfootertaxname'] = $taxName;
        $pCheckbox['unitsell'] = $checkboxUnitSell;
        $pCheckbox['totalcost'] = $checkboxTotalCost;
        $pCheckbox['totalsell'] = $checkboxTotalSell;
        $pCheckbox['totaltax'] = $checkboxTotalTax;
        $pCheckbox['totalsellnotax'] = $checkboxTotalSellNoTax;
        $pCheckbox['totalsellwithtax'] = $checkboxTotalSellWithTax;
        $pCheckbox['totalweight'] = $checkboxTotalWeight;
        $pCheckbox['discountvalue'] = 0.0;
        $pCheckbox['discountvaluenotax'] = 0.0;
        $pCheckbox['discountvaluewithtax'] = 0.0;
        $pCheckbox['subtotal'] = $checkboxTotalSell;

        // nothing to return
    }

    /**
     * Calculates total of a section.
     *
     * This function does not go into sub-sections
     *
     * @static
     *
     * @param array     $pSection
     * @param string    $pProductCode
     * @param float     $pCurrencyExchangeRate
     * @param integer   $pCurrencyDecimalPlaces
     * @param integer   $pItemQty
     * @param integer   $pPageCount
     *
     * @author Steffen Haugk
     * @since Version 3.0.0
     */
    static function updateSectionTotal(&$pSection, $pProductCode, $pCurrencyExchangeRate, $pCurrencyDecimalPlaces, $pItemQty, $pPageCount,
            $pComponentQty, $pOrderItemLineId)
    {
        global $gSession;

        $sectionTotalSell = 0;
        $sectionUnitSell = 0;
        $sectionTotalCost = 0;
        $sectionTotalWeight = 0;
        $taxRate = 0.00;
        $taxCode = '';
        $taxName = '';

        if ($pSection['defaultcode'] != '')
        {
            $sectionPriceArray = DatabaseObj::getPrice($pSection['path'], $pSection['code'], false, $pProductCode,
                            $gSession['licensekeydata']['groupcode'], $gSession['userdata']['companycode'], $pCurrencyExchangeRate,
                            $pCurrencyDecimalPlaces, $pItemQty, $pPageCount, $pComponentQty, $pComponentQty, true, true, -1, 0, '', true);

            if ($sectionPriceArray['result'] == '')
            {
                $pSection['itemqtydropdown'] = $sectionPriceArray['itemqtydropdown'];
                $pSection['quantity'] = $sectionPriceArray['newqty'];
                $pSection['pricetaxcode'] = $sectionPriceArray['taxcode'];
                $pSection['pricetaxrate'] = $sectionPriceArray['taxrate'];

				if (count($pSection['subsections']) > 0)
				{
					// check the sub components for inheritparentqty setting
					foreach ($pSection['subsections'] as &$theSubComponent)
					{
						// get the details of the sub component price
						// Is the sub component set to inherit the parent qty?
						$subSectionPriceArray = DatabaseObj::getPrice($theSubComponent['path'], $theSubComponent['code'], false, $pProductCode,
										$gSession['licensekeydata']['groupcode'], $gSession['userdata']['companycode'], $pCurrencyExchangeRate,
										$pCurrencyDecimalPlaces, $pItemQty, $pPageCount, $pComponentQty, $pComponentQty, true, true, -1, 0, '', true);

						$theSubComponent['inheritparentqty'] = $subSectionPriceArray['inheritparentqty'];

						// is the sub-component is set to inherit the parent qty
						if ($subSectionPriceArray['inheritparentqty'] == 1)
						{
							// does the sub-component uses fixed quantity ranges
							if ($subSectionPriceArray['quantityisdropdown'] == 1)
							{
								// is the new quantity included in the sub-components fixed quantity ranges
								if (in_array($pSection['quantity'], $subSectionPriceArray['itemqtydropdown']))
								{
									$theSubComponent['quantity'] = $pSection['quantity'];
								}
							}
							else
							{
								$theSubComponent['quantity'] = $pSection['quantity'];
							}
						}
					}
					unset($theSubComponent);
				}

				// update the quantities of any checkbox sub components
				if (count($pSection['checkboxes']) > 0)
				{
					// check the sub components for inheritparentqty setting
					foreach ($pSection['checkboxes'] as &$theSubComponent)
					{
						// get the details of the sub component price
						// Is the sub component set to inherit the parent qty?
						$subSectionPriceArray = DatabaseObj::getPrice($theSubComponent['path'], $theSubComponent['code'], false, $pProductCode,
										$gSession['licensekeydata']['groupcode'], $gSession['userdata']['companycode'], $pCurrencyExchangeRate,
										$pCurrencyDecimalPlaces, $pItemQty, $pPageCount, $pComponentQty, $pComponentQty, true, true, -1, 0, '', true);

						$theSubComponent['inheritparentqty'] = $subSectionPriceArray['inheritparentqty'];

						// is the sub-component is set to inherit the parent qty
						if ($subSectionPriceArray['inheritparentqty'] == 1)
						{
							$theSubComponent['quantity'] = $pSection['quantity'];
						}
					}
					unset($theSubComponent);
				}

                switch ($sectionPriceArray['pricingmodel'])
                {
                    case TPX_PRICINGMODEL_PERQTY:
                        $sectionTotalWeight = $pItemQty * $pSection['unitweight'];
                        $sectionTotalCost = $pItemQty * $pSection['unitcost'];
                        break;
                    case TPX_PRICINGMODEL_PERSIDEQTY:
                        $sectionTotalWeight = ($pItemQty * $pPageCount) * $pSection['unitweight'];
                        $sectionTotalCost = ($pItemQty * $pPageCount) * $pSection['unitcost'];
                        break;
                    case TPX_PRICINGMODEL_PERPRODCMPQTY:
                        $sectionTotalWeight = $pItemQty * ($pSection['quantity'] * $pSection['unitweight']);
                        $sectionTotalCost = $pItemQty * ($pSection['quantity'] * $pSection['unitcost']);
                        break;
                    case TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY:
                        $sectionTotalWeight = ($pSection['quantity'] * $pPageCount) * $pSection['unitweight'];
                        $sectionTotalWeight = $pItemQty * $sectionTotalWeight;

                        $sectionTotalCost = ($pSection['quantity'] * $pPageCount) * $pSection['unitcost'];
                        $sectionTotalCost = $pItemQty * $sectionTotalCost;
                        break;
                }

                $sectionTotalSell = $sectionPriceArray['totalsell'];
                $sectionUnitSell = $sectionPriceArray['unitsell'];
            }
        }

        // check to see if the component belongs to the order footer.
        // If so then we need to grab the taxrate from the session based on the orderfooterlevel set on the component
        if ($pOrderItemLineId == TPX_ORDERFOOTER_ID)
        {
            if ($gSession['order']['orderalltaxratesequal'] == 1)
            {
                $taxRateArray = DatabaseObj::getTaxRate($gSession['order']['fixedtaxrate']);
            }
            else
            {
                $taxRateArray = self::getTaxRateArrayFromTaxLevel($pSection['orderfootertaxlevel']);
            }

            $taxRate = $taxRateArray['rate'];
            $taxCode = $taxRateArray['code'];
            $taxName = $taxRateArray['name'];
        }
        else
        {
            $taxRate = $gSession['items'][$pOrderItemLineId]['itemtaxrate'];
            $taxCode = $gSession['items'][$pOrderItemLineId]['itemtaxcode'];
            $taxName = $gSession['items'][$pOrderItemLineId]['itemtaxname'];
        }

        $sectionTotalTax = UtilsObj::bround($sectionTotalSell * $taxRate / 100, $pCurrencyDecimalPlaces);
        $sectionTotalSellWithTax = $sectionTotalSell + $sectionTotalTax;
        $sectionTotalSellNoTax = $sectionTotalSellWithTax - $sectionTotalTax;

        // determine the tax status for the component
        if ($pSection['pricetaxcode'] != '')
        {
            // tax is included in the price so determine the price without tax
            $sectionTotalSellNoTax = UtilsObj::bround(($sectionTotalSell / ($pSection['pricetaxrate'] + 100)) * 100,
                            $gSession['order']['currencydecimalplaces']);
            $sectionTotalTax = $sectionTotalSell - $sectionTotalSellNoTax;

            if ($pSection['pricetaxrate'] != $taxRate)
            {
                // if the tax included in the price is different to the line tax then we use the price without tax
                $sectionTotalSell = $sectionTotalSellNoTax;
                $totalTax = UtilsObj::bround($sectionTotalSell * $taxRate / 100, $pCurrencyDecimalPlaces);
                $sectionTotalSellWithTax = $sectionTotalSell + $totalTax;
            }
            else
            {
                // tax is already calculated
                $sectionTotalSellWithTax = $sectionTotalSell;
            }
        }
        else
        {
            // no tax was included in the price
            $sectionTotalTax = UtilsObj::bround($sectionTotalSell * $taxRate / 100, $pCurrencyDecimalPlaces);
            $sectionTotalSellWithTax = $sectionTotalSell + $sectionTotalTax;
            $sectionTotalSellNoTax = $sectionTotalSell;
        }

        // add the tax at this point if necessary
        if ($gSession['order']['showpriceswithtax'] == 1)
        {
            $sectionTotalSell = $sectionTotalSellWithTax;
        }
        else
        {
            $sectionTotalSell = $sectionTotalSellNoTax;
        }

        $pSection['orderfootertaxrate'] = $taxRate;
        $pSection['orderfootertaxcode'] = $taxCode;
        $pSection['orderfootertaxname'] = $taxName;
        $pSection['unitsell'] = $sectionUnitSell;
        $pSection['totalcost'] = $sectionTotalCost;
        $pSection['totalsell'] = $sectionTotalSell;
        $pSection['totaltax'] = $sectionTotalTax;
        $pSection['totalsellnotax'] = $sectionTotalSellNoTax;
        $pSection['totalsellwithtax'] = $sectionTotalSellWithTax;
        $pSection['totalweight'] = $sectionTotalWeight;
        $pSection['discountvalue'] = 0.0;
        $pSection['discountvaluenotax'] = 0.0;
        $pSection['discountvaluewithtax'] = 0.0;
        $pSection['subtotal'] = $sectionTotalSell;

        // nothing to return
    }

    static function updatePicturesTotal(&$pPicture, $pProductCode, $pCurrencyExchangeRate, $pCurrencyDecimalPlaces, $pItemQty, $pPageCount,
            $pLineBreakQty, $pComponentQty, $pOrderItemLineId, $pIsSubComponent, $pApplyBasePriceLineSubtract)
    {
        global $gSession;

        $pictureTotalSell = 0;
        $pictureUnitSell = 0;
        $pictureTotalCost = 0;
        $pictureTotalWeight = 0;
        $assetSell = 0;
        $pictureArrayCacheKey = '';

        $taxRate = $gSession['items'][$pOrderItemLineId]['itemtaxrate'];
        $taxCode = $gSession['items'][$pOrderItemLineId]['itemtaxcode'];
        $taxName = $gSession['items'][$pOrderItemLineId]['itemtaxname'];

        $parentPath = '$SINGLEPRINT\\';
        $componentCode = 'SINGLEPRINT' . '.' . $pPicture['code'];
        $subComponentArrayKeyPrefix = '';

        if ($pIsSubComponent)
        {
        	$parentPath = '$SINGLEPRINT\\' . $pPicture['code'] . '\\$SINGLEPRINTOPTION\\';
			$componentCode = 'SINGLEPRINTOPTION' . '.' . $pPicture['subcode'];
			$subComponentArrayKeyPrefix = 'sub';

        }

        if ($pApplyBasePriceLineSubtract)
        {
        	$applyBasePrice = 1;
        }
        else
        {
        	$applyBasePrice = 0;
        }

		$pictureArrayCacheKey = $gSession['userdata']['companycode'] . '.' . $gSession['licensekeydata']['groupcode'] . '.' . $pProductCode;
		$pictureArrayCacheKey .= '.' . $componentCode . '.' . $pItemQty . '.' . $pPageCount . '.' . $pComponentQty . '.' . $applyBasePrice;

		$picturePriceArray = DatabaseObj::getPriceCacheData($pictureArrayCacheKey);

		if (count($picturePriceArray) == 0)
		{
			$picturePriceArray = DatabaseObj::getPrice($parentPath, $componentCode, false, $pProductCode,
                        $gSession['licensekeydata']['groupcode'], $gSession['userdata']['companycode'], $pCurrencyExchangeRate,
                        $pCurrencyDecimalPlaces, $pItemQty, $pPageCount, $pLineBreakQty, $pComponentQty, false, false, -1, 0, '', $pApplyBasePriceLineSubtract);

            DatabaseObj::setPriceCacheData($pictureArrayCacheKey, $picturePriceArray);
		}

        if ($picturePriceArray['result'] == '')
        {
            $pPicture[$subComponentArrayKeyPrefix . 'pricetaxcode'] = $picturePriceArray['taxcode'];
            $pPicture[$subComponentArrayKeyPrefix . 'pricetaxrate'] = $picturePriceArray['taxrate'];
            $pictureTotalSell = $picturePriceArray['totalsell'];

            $pictureTotalWeight = $pItemQty * ($pPicture['qty'] * $pPicture[$subComponentArrayKeyPrefix . 'unitweight']);

            if ($pIsSubComponent)
            {
            	$pictureUnitSell = $picturePriceArray['unitsell'];
            	$pictureTotalCost = $pItemQty * ($pPicture['qty'] * $pPicture[$subComponentArrayKeyPrefix . 'unitcost']);
            }
            else
            {
            	$pictureUnitSell = $picturePriceArray['unitsell'] + $pPicture['as'];
            	$pictureTotalCost = $pItemQty * (($pPicture['qty'] * $pPicture['unitcost']) + ($pPicture['qty'] * $pPicture['ac']));
            }

            $pPicture['componenthasprice'] = 1;
        }
        else
        {
            $pPicture['componenthasprice'] = 0;
        }

        // determine the tax status for the component
        if ($pPicture[$subComponentArrayKeyPrefix . 'pricetaxcode'] != '')
        {
            // tax is included in the price so determine the price without tax
            $pictureTotalSellNoTax = UtilsObj::bround(($pictureTotalSell / ($pPicture[$subComponentArrayKeyPrefix . 'pricetaxrate'] + 100)) * 100,
                            $gSession['order']['currencydecimalplaces']);
            $pictureTotalTax = $pictureTotalSell - $pictureTotalSellNoTax;

            if ($pPicture[$subComponentArrayKeyPrefix . 'pricetaxrate'] != $taxRate)
            {
                // if the tax included in the price is different to the line tax then we use the price without tax
                $pictureTotalSell = $pictureTotalSellNoTax;
                $totalTax = UtilsObj::bround($pictureTotalSell * $taxRate / 100, $pCurrencyDecimalPlaces);
                $pictureTotalSellWithTax = $pictureTotalSell + $totalTax;
            }
            else
            {
                // tax is already calculated
                $pictureTotalSellWithTax = $pictureTotalSell;
            }
        }
        else
        {
            // no tax was included in the price
            $pictureTotalTax = UtilsObj::bround($pictureTotalSell * $taxRate / 100, $pCurrencyDecimalPlaces);
            $pictureTotalSellWithTax = $pictureTotalSell + $pictureTotalTax;
            $pictureTotalSellNoTax = $pictureTotalSell;
        }

		// external asset prices should only be added to the main size component and not the sub component.
		if (!$pIsSubComponent)
		{
			// calculate the asset price
			switch ($pPicture['apt'])
			{
				case TPX_EXTERNALASSETPRICETYPE_ONCE:
					$assetSell = $pItemQty * $pPicture['as'];
					break;
				case TPX_EXTERNALASSETPRICETYPE_EACHTIME:
					$assetSell = $pItemQty * $pPicture['qty'] * $pPicture['as'];
					break;
			}

			$assetSell = UtilsObj::bround($assetSell, $pCurrencyDecimalPlaces);
			$pictureTotalSellWithTax += $assetSell + UtilsObj::bround($assetSell * $taxRate / 100, $pCurrencyDecimalPlaces);
			$pictureTotalSellNoTax += $assetSell;
        }

        // add the tax at this point if necessary
        if ($gSession['order']['showpriceswithtax'] == 1)
        {
            $pictureTotalSell = $pictureTotalSellWithTax;
        }
        else
        {
            $pictureTotalSell = $pictureTotalSellNoTax;
        }

		return array(
			'unitsell' => $pictureUnitSell,
			'totalcost' => $pictureTotalCost,
			'totalsell' => $pictureTotalSell,
			'totaltax' => $pictureTotalTax,
			'totalsellnotax' => $pictureTotalSellNoTax,
			'totalsellwithtax' => $pictureTotalSellWithTax,
			'totalweight' => $pictureTotalWeight
		);
    }

    static function updateCalendarCustomisationsTotal(&$pCalendarCustomisation, $pProductCode, $pCurrencyExchangeRate,
                                                            $pCurrencyDecimalPlaces, $pItemQty, $pPageCount, $pOrderItemLineId)
    {
        global $gSession;

        $taxRate = $gSession['items'][$pOrderItemLineId]['itemtaxrate'];
        $taxCode = $gSession['items'][$pOrderItemLineId]['itemtaxcode'];
        $taxName = $gSession['items'][$pOrderItemLineId]['itemtaxname'];

        $calcustomPriceArray = DatabaseObj::getPrice('$CALENDARCUSTOMISATION\\', $pCalendarCustomisation['componentcode'], false, $pProductCode,
                                                    $gSession['licensekeydata']['groupcode'], $gSession['userdata']['companycode'], $pCurrencyExchangeRate,
                                                    $pCurrencyDecimalPlaces, $pItemQty, $pPageCount, $pCalendarCustomisation['componentqty'], $pCalendarCustomisation['componentqty'], true, true, -1, 0, '', true);

        if ($calcustomPriceArray['result'] == '')
        {

            $unitSell = $calcustomPriceArray['unitsell'];

            $totalTax = UtilsObj::bround($calcustomPriceArray['totalsell'] * $gSession['items'][$pOrderItemLineId]['itemtaxrate'] / 100,
                                            $pCurrencyDecimalPlaces);


            $totalSell = $calcustomPriceArray['totalsell'];


            $totalWeight = $pItemQty * ($pCalendarCustomisation['componentqty'] * $pCalendarCustomisation['unitweight']);
            $totalCost = $pItemQty * ($pCalendarCustomisation['componentqty'] * $pCalendarCustomisation['unitcost']);

            $totalSellWithTax = $totalSell + $totalTax;
            $totalSellNoTax = $totalSellWithTax - $totalTax;

            // determine the tax status for the component
            if ($pCalendarCustomisation['pricetaxcode'] != '')
            {
                // tax is included in the price so determine the price without tax
                $totalSellNoTax = UtilsObj::bround(($totalSell / ($pCalendarCustomisation['pricetaxrate'] + 100)) * 100,
                                $gSession['order']['currencydecimalplaces']);
                $totalTax = $totalSell - $totalSellNoTax;

                if ($pCalendarCustomisation['pricetaxrate'] != $taxRate)
                {
                    // if the tax included in the price is different to the line tax then we use the price without tax
                    $totalSell = $totalSellNoTax;
                    $totalTax = UtilsObj::bround($totalSell * $taxRate / 100, $pCurrencyDecimalPlaces);
                    $totalSellWithTax = $totalSell + $totalTax;
                }
                else
                {
                    // tax is already calculated
                    $totalSellWithTax = $totalSell;
                }
            }
            else
            {
                // no tax was included in the price
                $totalTax = UtilsObj::bround($totalSell * $taxRate / 100, $pCurrencyDecimalPlaces);
                $totalSellWithTax = $totalSell + $totalTax;
                $totalSellNoTax = $totalSell;
            }

            // add the tax at this point if necessary
            if ($gSession['order']['showpriceswithtax'] == 1)
            {
                $totalSell = $totalSellWithTax;
            }
            else
            {
                $totalSell = $totalSellNoTax;
            }

            $pCalendarCustomisation['unitsell'] = $unitSell;
            $pCalendarCustomisation['totalcost'] = $totalCost;
            $pCalendarCustomisation['totalsell'] = $totalSell;
            $pCalendarCustomisation['totaltax'] = $totalTax;
            $pCalendarCustomisation['totalsellnotax'] = $totalSellNoTax;
            $pCalendarCustomisation['totalsellwithtax'] = $totalSellWithTax;
            $pCalendarCustomisation['totalweight'] = $totalWeight;
        }
    }

    static function updateExternalAssetTotal(&$pExternalAsset, $pCurrencyExchangeRate, $pCurrencyDecimalPlaces, $pItemQty, $pOrderItemLineId)
    {
        global $gSession;

        $externalAssetTotalCost = 0;
        $externalAssetTotalSell = 0;
        $externalAssetTotalTax = 0;
        $externalAssetTotalSellNoTax = 0;
        $externalAssetTotalSellWithTax = 0;
		$isReorder = $gSession['order']['isreorder'];
		$unitSell = $pExternalAsset['unitsell'];

		// in a reorder the unit sell will include tax from the previous order so it must be taken from the raw asset cost
		if ($isReorder == true)
		{
			// pricing type is handled by online or desktop for non-single print products so the non-first image of a pay once image will have the
			// unit sell set to zero with the full price in the asset unit so we need to use the unit sell when this is the case to avoid charging
			// multiple times for pay once images.
			if ($unitSell == 0.00)
			{
				$externalAssetTotalCost = $pExternalAsset['assetunitcost'];
				$externalAssetTotalSell = $unitSell;
			}
			else
			{
				$externalAssetTotalCost = $pExternalAsset['assetunitcost'];
				$externalAssetTotalSell = $pExternalAsset['assetunitsell'];
			}
		}
		else
		{
			$externalAssetTotalCost = $pExternalAsset['unitcost'] * $pItemQty;
			$externalAssetTotalSell = $unitSell * $pItemQty;
		}

        $externalAssetTotalSellNoTax = $externalAssetTotalSell;

        // taxrate should be per item
        $externalAssetTotalTax = UtilsObj::bround($externalAssetTotalSell * $gSession['items'][$pOrderItemLineId]['itemtaxrate'] / 100,
                        $pCurrencyDecimalPlaces);
        $externalAssetTotalSellWithTax = $externalAssetTotalSell + $externalAssetTotalTax;

        // add the tax at this point if necessary
        if ($gSession['order']['showpriceswithtax'] == 1)
        {
            $externalAssetTotalSell = $externalAssetTotalSellWithTax;
        }
        else
        {
            // these values are meaningless
            $externalAssetTotalTax = 0.00;
        }

        $pExternalAsset['totalcost'] = $externalAssetTotalCost;
        $pExternalAsset['totalsell'] = $externalAssetTotalSell;
        $pExternalAsset['totaltax'] = $externalAssetTotalTax;
        $pExternalAsset['totalsellnotax'] = $externalAssetTotalSellNoTax;
        $pExternalAsset['totalsellwithtax'] = $externalAssetTotalSellWithTax;
        // nothing to return
    }

	static function updateOrderTotal()
	{
		global $gSession;

		// read pricing system from session rather than config file to avoid issues caused by mid-order config changes
		if (!$gSession['order']['uselegacypricingsystem'])
		{
			global $gOrder;

			// Refresh the credit status
			Order_model::updateCreditStatus();

			$gOrder = OrderLoader::load($gSession);
			$gOrder->calculateOrder();

			DatabaseObj::setSessionGiftCardTotal($gSession['ref'], $gSession['order']['ordergiftcardtotal']);
		}
		else
		{
			self::updateOrderTotalLegacy();
		}
	}

    static function updateOrderTotalLegacy()
    {
        global $gSession;
        global $gConstants;

        $orderDiscountValue = 0.00;

        $orderTotal = 0.00;
        $orderTotalCost = 0.00;
        $orderTotalSell = 0.00;
        $orderTotalTax = 0.00;
        $orderTotalWithTax = 0.00;
        $orderTotalWithoutTax = 0.00;
        $orderTotalForDiscount = 0.00;
        $orderTotalBeforeDiscount = 0.00;
        $orderGiftCardTotal = 0.00;
        $orderTotalToPay = 0.00;

        $orderTotalItemCost = 0.00;
        $orderTotalItemSell = 0.00;

        $orderTotalItemSellNoTax = 0.00;
        $orderTotalItemSellNoTaxNoDiscount = 0.00;
        $orderTotalItemSellNoTaxAllDiscounted = 0.00;
        $orderTotalItemSellWithTax = 0.00;
        $orderTotalItemSellWithTaxNoDiscount = 0.00;
        $orderTotalItemSellWithTaxAllDiscounted = 0.00;
        $orderTotalItemTax = 0.00;
        $orderTotalWeight = 0.0000;

        $currencyExchangeRate = $gSession['order']['currencyexchangerate'];
        $currencyDecimalPlaces = $gSession['order']['currencydecimalplaces'];

		// apply the exchange rate to the voucher value depending on the voucher type
		// we don't want to recalculate the exchangerate every time the voucher value is passed into calcdiscountedval2 as this results in a invalid value when using multiline
		//we need to apply the exchangerate before passing it into the function
		$voucherDiscountValue = self::applyExchangeRateToVoucher($gSession['order']['voucherdiscounttype'], $gSession['order']['voucherdiscountvalue'], $gSession['order']['currencyexchangerate']);
        $voucherDiscountValue2 = self::applyExchangeRateToVoucher($gSession['order']['voucherdiscounttype'], $gSession['order']['voucherdiscountvalue'], $gSession['order']['currencyexchangerate']);

        $canApplyvoucherDiscount = true;
        $canApplyvoucherDiscount2 = true;

        $orderTotalNoTaxWithDiscount = 0.00;
        $orderEligableForDiscount = 0.00;

        $ignoreVoucherTypes = Array();
        $ignoreVoucherTypes[] = 'VALUESET';

		$taxBreakdownArray = $gSession['order']['ordertaxproductbreakdown'];

		if (($gSession['shipping'][0]['shippingmethodcode'] != '') && ($gSession['shipping'][0]['shippingratecode'] != ''))
		{
			// if the tax rate code is for custom tax call the script to calculate it
			if (substr($gSession['shipping'][0]['shippingratetaxcode'], 0, 13) == 'TPX_CUSTOMTAX')
			{
				if (file_exists("../Customise/scripts/EDL_TaxCalculation.php"))
				{
					require_once('../Customise/scripts/EDL_TaxCalculation.php');

					$paramArray = array();
					$paramArray['brandcode'] = $gSession['webbrandcode'];
					$paramArray['groupcode'] = $gSession['licensekeydata']['groupcode'];
					$paramArray['groupdata'] = $gSession['licensekeydata']['groupdata'];
					$paramArray['browserlanguagecode'] = $gSession['browserlanguagecode'];
					$paramArray['currencycode'] = $gSession['order']['currencycode'];
					$paramArray['currencyexchange'] = $gSession['order']['currencyexchangerate'];
					$paramArray['currencydecimalplaces'] = $gSession['order']['currencydecimalplaces'];
					$paramArray['taxcalculationaddress'] = array();
					$paramArray['customershippingaddress'] = array();
					$paramArray['customerbillingaddress'] = array();
					$paramArray['cartitems']['lineitems'] = $gSession['items'];
					$paramArray['cartitems']['orderfooteritems']['orderfootersections'] = $gSession['order']['orderFooterSections'];
					$paramArray['cartitems']['orderfooteritems']['orderfootercheckboxes'] = $gSession['order']['orderFooterCheckboxes'];

					$paramArray['shipping'] = array('shippingmethodcode' => $gSession['shipping'][0]['shippingmethodcode'],
													'shippingratecode' => $gSession['shipping'][0]['shippingratecode'],
													'shippingratecost' => $gSession['shipping'][0]['shippingratecost'],
													'shippingratesell' => $gSession['shipping'][0]['shippingratesell'],
													'shippingratesellnotax' => $gSession['shipping'][0]['shippingratesellnotax'],
													'shippingratesellwithtax' => $gSession['shipping'][0]['shippingratesellwithtax'],
													'shippingratepricetaxcode' => $gSession['shipping'][0]['shippingratepricetaxcode'],
													'shippingratepricetaxrate' => $gSession['shipping'][0]['shippingratepricetaxrate'],
													'shippingratediscountvalue' => $gSession['shipping'][0]['shippingratediscountvalue']);

					self::copyArrayAddressFields($gSession['shipping'][0], 'shippingcustomer', $paramArray['customershippingaddress'], 'shipping', false, true);
					self::copyArrayAddressFields($gSession['order'], 'billingcustomer', $paramArray['customerbillingaddress'], 'billing', false, true);

					if ($gConstants['taxaddress'] == TPX_TAX_CALCULATION_BY_BILLING_ADDRESS)
					{
						self::copyArrayAddressFields($gSession['order'], 'billingcustomer', $paramArray['taxcalculationaddress'], 'billing', false, true);
					}
					else
					{
						self::copyArrayAddressFields($gSession['shipping'][0], 'shippingcustomer', $paramArray['taxcalculationaddress'], 'shipping', false, true);
					}

					$scriptTaxRateResultArray = TaxCalculationAPI::getShippingTaxRate($paramArray);

					// make sure that the code returned is a custom tax
					if (substr($scriptTaxRateResultArray['customtaxdetails']['code'], 0, 13) == TPX_CUSTOMTAX)
					{
						$gSession['shipping'][0]['shippingratetaxcode'] = $scriptTaxRateResultArray['customtaxdetails']['code'];
						$gSession['shipping'][0]['shippingratetaxname'] = $scriptTaxRateResultArray['customtaxdetails']['description'];
						$gSession['shipping'][0]['shippingratetaxrate'] = UtilsObj::bround($scriptTaxRateResultArray['customtaxdetails']['rate'], 4);
					}
				}
			}
        }

        $shippingTaxRateCodeExists = false;

        foreach ($taxBreakdownArray as $taxBreakdown)
        {
        	if ($gSession['shipping'][0]['shippingratetaxcode'] == $taxBreakdown['taxratecode'])
        	{
        		$shippingTaxRateCodeExists = true;
        		break;
        	}
        }

        // update the shipping tax breakdown
        if (! $shippingTaxRateCodeExists)
        {
            $taxBreakdownItem = Array();
            $taxBreakdownItem['taxratecode'] = $gSession['shipping'][0]['shippingratetaxcode'];
            $taxBreakdownItem['taxratename'] = $gSession['shipping'][0]['shippingratetaxname'];
            $taxBreakdownItem['taxrate'] = $gSession['shipping'][0]['shippingratetaxrate'];
            $taxBreakdownItem['nettotal'] = 0.00;
            $taxBreakdownItem['taxtotal'] = 0.00;

            $taxBreakdownArray[] = $taxBreakdownItem;
        }

        // set the final tax breakdown
        $gSession['order']['ordertaxbreakdown'] = $taxBreakdownArray;

        // determine if all tax rates are equal
        if (count($taxBreakdownArray) == 1)
        {
            // we only have one rate
            // if it is TAOPIX_CUSTOM it is a generic custom rate so we say they aren't the equal as we don't actually know
            if ($taxBreakdownArray[0]['taxratecode'] == TPX_CUSTOMTAX)
            {
                $gSession['order']['orderalltaxratesequal'] = 0;
            }
            else
            {
                // the tax rates are equal
                $gSession['order']['orderalltaxratesequal'] = 1;
            }
        }
        else
        {
            // we have more than one rate so they are not equal
            $gSession['order']['orderalltaxratesequal'] = 0;
        }

        // reset the ordertaxbreakdown nettotal & taxtotal values.
        foreach ($gSession['order']['ordertaxbreakdown'] as $item => &$value)
        {
            $value['nettotal'] = 0.00;
            $value['taxtotal'] = 0.00;
        }

        $previousItemTaxRateCode = '';

        // count the number of items for each loop around order items
        $orderItemsCount = count($gSession['items']);

        // loop around order lines to determine which have the highest and lowest prices (before discount)
        $highest = 0;
        $lowest = 1000000000;

        $highestID = -1;
        $lowestID = -1;
        $orderQtyCount = 0;
        $forceApplyDiscountToLineItems = false;


        // determine which order lines have the highest and lowest prices before discount
        // count how many items are eligable for discount to comapre with the voucherapplytoqty if a voucher is applied
        for ($i = 0; $i < $orderItemsCount; $i++)
        {
            if ($gSession['items'][$i]['itemvoucherapplied'] == 1)
            {
                $orderQtyCount += $gSession['items'][$i]['itemqty'];

                if ($gSession['items'][$i]['itemtotalsellnotaxnodiscount'] > $highest)
                {
                    $highest = $gSession['items'][$i]['itemtotalsellnotaxnodiscount'];
                    $highestID = $i;
                }
                if ($gSession['items'][$i]['itemtotalsellnotaxnodiscount'] < $lowest)
                {
                    $lowest = $gSession['items'][$i]['itemtotalsellnotaxnodiscount'];
                    $lowestID = $i;
                }
            }
        }

        // check to see if the voucher should to be applied to each line of the order (forcing a total voucher to act as a product voucher)
        // this would occur if:
        //  - the voucher is to be applied to the lowest or highest lines only
        //  - the voucher has an voucherapplytoqty set lower than the total number of items in the cart.
        //
        // set a variable to tell the cart items a voucher is being applied to either the line with the lowest or highest values
        $applyDiscountToSingleLineItem = (($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_LOWEST_PRICED) ||
                                          ($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_HIGHEST_PRICED));

        // check if the total voucher should be applied to each line (similar to different tax rates)
        if (($gSession['order']['voucherdiscountsection'] == 'TOTAL') &&
            (($orderQtyCount > $gSession['order']['voucherapplytoqty']) || ($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER)))
        {
            $forceApplyDiscountToLineItems = true;
        }

        // disable the itemvoucherapplied setting from the items the voucher should skip
        if ($applyDiscountToSingleLineItem)
        {
            for ($i = 0; $i < $orderItemsCount; $i++)
            {
                if ($gSession['items'][$i]['itemvoucherapplied'] == 1)
                {
                    if ((($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_LOWEST_PRICED) && ($i == $lowestID)) ||
                        (($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_HIGHEST_PRICED) && ($i == $highestID)))
                    {
                        $gSession['items'][$i]['itemvoucherapplied'] = 1;
                        $gSession['items'][$i]['itemvouchername'] = $gSession['order']['vouchername'];
                    }
                    else
                    {
                        $gSession['items'][$i]['itemvoucherapplied'] = 0;
                        $gSession['items'][$i]['itemvouchername'] = '';
                    }
                }
            }
        }

        // set the cart and calculations to be applied to lines, not totals
        $applyDiscountToSingleLineItem = ($applyDiscountToSingleLineItem || $forceApplyDiscountToLineItems);


        // set the number of products a voucher can be applied to
        $voucherQty = $gSession['order']['voucherapplytoqty'];
        $voucherQtyForTaxCalc = $voucherQty;

        // loop around order items
        for ($i = 0; $i < $orderItemsCount; $i++)
        {
            $currentLine = $i;
            $itemQty = $gSession['items'][$currentLine]['itemqty'];
            $itemPageCount = $gSession['items'][$currentLine]['itempagecount'];
            $itemTaxRateCode = $gSession['items'][$currentLine]['itemtaxcode'];

            // initialise the line item variables
            $itemProductUnitCost = 0.00;
            $itemProductUnitSell = 0.00;
            $itemProductTotalCost = 0.00;
            $itemProductTotalSell = 0.00;
            $itemProductTotalTax = 0.00;
            $itemProductTotalSellNoTax = 0.00;
            $itemProductTotalSellWithTax = 0.00;

            $itemComponentUnitCost = 0.00;
            $itemComponentUnitSell = 0.00;
            $itemComponentTotalCost = 0.00;
            $itemComponentTotalSell = 0.00;
            $itemComponentTotalTax = 0.00;
            $itemComponentTotalSellNoTax = 0.00;
            $itemComponentTotalSellWithTax = 0.00;
            $itemComponentTotalWeight = 0.0000;

            $itemSubTotal = 0.00;
            $itemDiscountValue = 0.00;
            $itemDiscountValueNoTax = 0.00;
            $itemDiscountValueWithTax = 0.00;

            $itemTotalCost = 0.00;
            $itemTotalSell = 0.00;
            $itemTotalSellNoTax = 0.00;
            $itemTotalSellNoTaxNoDiscount = 0.00;
            $itemTotalSellNoTaxAllDiscounted = 0.00;
            $itemTotalSellWithTax = 0.00;
            $itemTotalSellWithTaxNoDiscount = 0.00;
            $itemTotalSellWithTaxAllDiscounted = 0.00;
            $itemTaxTotal = 0.00;
            $calcShippingTax = 0;
            $shippingTaxTotal = 0.00;

            $shippingfromscript = false;
            $ordertotaldiscountfromscript = false;
            $orderfooterdiscountfromscript = false;


            // calculate the product price
            $productPriceArray = DatabaseObj::getProductPrice($gSession['items'][$currentLine]['itemproductcode'],
                            $gSession['licensekeydata']['groupcode'], $gSession['userdata']['companycode'], $currencyExchangeRate,
                            $currencyDecimalPlaces, $itemQty);

            if ($productPriceArray['result'] == '')
            {
                $gSession['items'][$currentLine]['itemhasproductprice'] = 1;
                $gSession['items'][$currentLine]['itemqtydropdown'] = $productPriceArray['itemqtydropdown'];

                // update the qty in the session as the price may have a minimum qty attached to it
            	$itemQty = $productPriceArray['newqty'];
            	$gSession['items'][$currentLine]['itemqty'] = $itemQty;
            	$gSession['shipping'][0]['shippingqty'] = $itemQty;

            	// initialise the basic sell prices
            	$itemProductUnitSell = $productPriceArray['unitsell'];
            	$itemProductTotalSell = $productPriceArray['totalsell'];
            }
            else
            {
                $gSession['items'][$currentLine]['itemhasproductprice'] = 0;
                $gSession['items'][$currentLine]['itemqtydropdown'] = Array();
            }

            $gSession['items'][$currentLine]['pricetaxcode'] = $productPriceArray['taxcode'];
            $gSession['items'][$currentLine]['pricetaxrate'] = $productPriceArray['taxrate'];


            // initialise the basic cost prices
            $itemProductUnitCost = $gSession['items'][$currentLine]['itemunitcost'];
            $itemProductTotalCost = $itemQty * $itemProductUnitCost;

            // determine the tax status for the product
            if ($gSession['items'][$currentLine]['pricetaxcode'] != '')
            {
                // tax is included in the price so determine the price without tax
                $itemProductTotalSellNoTax = UtilsObj::bround(($itemProductTotalSell / ($gSession['items'][$currentLine]['pricetaxrate'] + 100)) * 100,
                                $gSession['order']['currencydecimalplaces']);
                $itemProductTotalTax = $itemProductTotalSell - $itemProductTotalSellNoTax;

                if ($gSession['items'][$currentLine]['pricetaxrate'] != $gSession['items'][$currentLine]['itemtaxrate'])
                {
                    // if the tax included in the price is different to the line tax then we use the price without tax as we will be adding it later
                    $itemProductTotalSell = $itemProductTotalSellNoTax;
                }
                else
                {
                    // tax is already calculated
                    $itemProductTotalSellWithTax = $itemProductTotalSell;
                }
            }
            else
            {
                // no tax was included to the price
                $itemProductTotalSellNoTax = $itemProductTotalSell;
            }

            // initialize the weights
            $itemProductTotalWeight = $itemQty * $gSession['items'][$currentLine]['itemproductunitweight'];
            $itemTotalWeight = $itemProductTotalWeight;

            // initialise the sub-totals
            $itemTotalCost = $itemProductTotalCost;
            $itemSubTotal = $itemProductTotalSell;
            $itemTotalSellNoTax = $itemProductTotalSellNoTax;
            $pictureCount = count($gSession['items'][$currentLine]['pictures']['key']);

			$applyBasePriceLineSubtract = true;

			if ($gSession['items'][$currentLine]['productoptions'] == TPX_PRODUCTOPTION_PRICING_PERCOMPONENTSUBCOMPONENT)
			{
				$consolidatedPicturesSizeStockArray = array();

				// consolidate the singleprint prices
				foreach ($gSession['items'][$currentLine]['pictures']['key'] as $pictureLookup)
				{
					$picture = $gSession['items'][$currentLine]['pictures']['data'][$pictureLookup];

					$componentSubComponentKey = $picture['code'];

					if ($picture['subcode'] != '')
					{
						$componentSubComponentKey .= '.' . $picture['subcode'];
					}

					if (!array_key_exists($componentSubComponentKey, $consolidatedPicturesSizeStockArray))
					{
						$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['qty'] = $picture['qty'];
						$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['basepricelinesubtractapplied'] = false;
					}
					else
					{
						$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['qty'] += $picture['qty'];
					}
				}
			}

			$picturesCount = count($gSession['items'][$currentLine]['pictures']['key']);

            // handle single prints
			for ($picturesIndex = 0; $picturesIndex < $picturesCount; $picturesIndex++)
            {
				$asset = array(
					'aid' => '', // asset id
					'asc' => '', // asset service code
					'asn' => '', // asset service name
					'apt' => 0, // asset price type
					'ac' => 0.00, // asset cost
					'as' => 0.00 // asset sell
				);

				$pictureLookup = $gSession['items'][$currentLine]['pictures']['key'][$picturesIndex];
				$uniqueLookup = $pictureLookup . TPX_PICTURES_LOOKUP_SEPERATOR . $picturesIndex;
				$picture = &$gSession['items'][$currentLine]['pictures']['data'][$pictureLookup];
				$printData = &$gSession['items'][$currentLine]['pictures']['printdata'][$uniqueLookup];

				if (array_key_exists($uniqueLookup, $gSession['items'][$currentLine]['pictures']['asset']))
				{
					$asset = $gSession['items'][$currentLine]['pictures']['asset'][$uniqueLookup];
				}

				$picture['asc'] = $asset['asc'];
				$picture['asn'] = $asset['asn'];
				$picture['apt'] = $asset['apt'];
				$picture['ac'] = $asset['ac'];
				$picture['as'] = $asset['as'];

                $lineBreakQTY = $picture['qty'];

                $componentSubComponentKey = $picture['code'];

				if ($picture['subcode'] != '')
				{
					$componentSubComponentKey .= '.' . $picture['subcode'];
				}

                if ($gSession['items'][$currentLine]['productoptions'] == TPX_PRODUCTOPTION_PRICING_PERCOMPONENTSUBCOMPONENT)
                {
                	$lineBreakQTY = $consolidatedPicturesSizeStockArray[$componentSubComponentKey]['qty'];

                	$applyBasePriceLineSubtract = false;

					if (! $consolidatedPicturesSizeStockArray[$componentSubComponentKey]['basepricelinesubtractapplied'])
					{
						$applyBasePriceLineSubtract = true;
					}
                }

                $updatesPictureTotal = self::updatePicturesTotal($picture, $gSession['items'][$currentLine]['itemproductcode'], $currencyExchangeRate,
                        $currencyDecimalPlaces, $itemQty, $itemPageCount, $lineBreakQTY, $picture['qty'], $currentLine, false, $applyBasePriceLineSubtract);

				$printData['us'] = $updatesPictureTotal['unitsell'];
				$printData['tc'] = $updatesPictureTotal['totalcost'];
				$printData['ts'] = $updatesPictureTotal['totalsell'];
				$printData['tt'] = $updatesPictureTotal['totaltax'];
				$printData['tsnt'] = $updatesPictureTotal['totalsellnotax'];
				$printData['tswt'] = $updatesPictureTotal['totalsellwithtax'];
				$printData['tw'] = $updatesPictureTotal['totalweight'];
				$printData['subtotal'] = $updatesPictureTotal['totalsell'];

                $itemTotalCost += $updatesPictureTotal['totalcost'];
                $itemSubTotal += $updatesPictureTotal['totalsell'];
                $itemTotalSellNoTax += $updatesPictureTotal['totalsellnotax'];
                $itemTotalSellWithTax += $updatesPictureTotal['totalsellwithtax'];

                if ($picture['subcode'] != '')
                {
                	 $updatesPictureTotal = self::updatePicturesTotal($picture, $gSession['items'][$currentLine]['itemproductcode'], $currencyExchangeRate,
                        $currencyDecimalPlaces, $itemQty, $itemPageCount, $lineBreakQTY, $picture['qty'], $currentLine, true, $applyBasePriceLineSubtract);

					$printData['subus'] = $updatesPictureTotal['unitsell'];
					$printData['subtc'] = $updatesPictureTotal['totalcost'];
					$printData['subts'] = $updatesPictureTotal['totalsell'];
					$printData['subtt'] = $updatesPictureTotal['totaltax'];
					$printData['subtsnt'] = $updatesPictureTotal['totalsellnotax'];
					$printData['subtswt'] = $updatesPictureTotal['totalsellwithtax'];
					$printData['subtw'] = $updatesPictureTotal['totalweight'];
					$printData['subtotal'] = $updatesPictureTotal['totalsell'];

					$itemTotalCost += $updatesPictureTotal['totalcost'];
					$itemSubTotal += $updatesPictureTotal['totalsell'];
					$itemTotalSellNoTax += $updatesPictureTotal['totalsellnotax'];
					$itemTotalSellWithTax += $updatesPictureTotal['totalsellwithtax'];
                }

                if ($gSession['items'][$currentLine]['productoptions'] == TPX_PRODUCTOPTION_PRICING_PERCOMPONENTSUBCOMPONENT)
                {
                	$consolidatedPicturesSizeStockArray[$componentSubComponentKey]['basepricelinesubtractapplied'] = true;
            	}
            }

			unset($picture);

            // handle external asset charges
            foreach ($gSession['items'][$currentLine]['itemexternalassets'] as &$externalAsset)
            {
                self::updateExternalAssetTotal($externalAsset, $currencyExchangeRate, $currencyDecimalPlaces, $itemQty, $currentLine);

                if ($pictureCount == 0)
                {
                    $itemTotalCost += $externalAsset['totalcost'];
                    $itemSubTotal += $externalAsset['totalsell'];
                    $itemTotalSellNoTax += $externalAsset['totalsellnotax'];
                    $itemTotalSellWithTax += $externalAsset['totalsellwithtax'];
                }
            }

            // handle calendar customisations
            foreach ($gSession['items'][$currentLine]['calendarcustomisations'] as &$calendarCustomisation)
            {
                if (($calendarCustomisation['componentqty'] > 0) && ($calendarCustomisation['used']))
                {
                    self::updateCalendarCustomisationsTotal($calendarCustomisation, $gSession['items'][$currentLine]['itemproductcode'],
                                                            $currencyExchangeRate, $currencyDecimalPlaces, $itemQty, $itemPageCount, $currentLine);

                    $itemTotalCost += $calendarCustomisation['totalcost'];
                    $itemSubTotal += $calendarCustomisation['totalsell'];
                    $itemTotalSellNoTax += $calendarCustomisation['totalsellnotax'];
                    $itemTotalSellWithTax += $calendarCustomisation['totalsellwithtax'];
                }
            }

            // loop around checkboxes
            foreach ($gSession['items'][$currentLine]['checkboxes'] as &$checkbox)
            {
                self::updateCheckboxTotal($checkbox, $gSession['items'][$currentLine]['itemproductcode'], $currencyExchangeRate,
                        $currencyDecimalPlaces, $itemQty, $itemPageCount, $currentLine);

                if ($checkbox['checked'])
                {
                    // add to the sub-totals
                    $itemTotalCost += $checkbox['totalcost'];
                    $itemSubTotal += $checkbox['totalsell'];
                    $itemTotalSellNoTax += $checkbox['totalsellnotax'];
                    $itemTotalSellWithTax += $checkbox['totalsellwithtax'];
                    $itemTotalWeight += $checkbox['totalweight'];
                }

            }

            // loop around sections
            foreach ($gSession['items'][$currentLine]['sections'] as &$component)
            {
                $itemComponentUnitCost = 0.00;
                $itemComponentUnitSell = 0.00;
                $itemComponentTotalCost = 0.00;
                $itemComponentTotalSell = 0.00;
                $itemComponentTotalTax = 0.00;
                $itemComponentTotalSellNoTax = 0.00;
                $itemComponentTotalSellWithTax = 0.00;
                $itemComponentTotalWeight = 0.0000;

                self::updateSectionTotal($component, $gSession['items'][$currentLine]['itemproductcode'], $currencyExchangeRate,
                        $currencyDecimalPlaces, $itemQty, $itemPageCount, $component['quantity'], $currentLine);
                // add to the sub-totals
                $itemTotalCost += $component['totalcost'];
                $itemSubTotal += $component['totalsell'];
                $itemTotalSellNoTax += $component['totalsellnotax'];
                $itemTotalSellWithTax += $component['totalsellwithtax'];
                $itemTotalWeight += $component['totalweight'];

                // add component prices
                $itemComponentTotalCost = $component['totalcost'];
                $itemComponentTotalSell = $component['totalsell'];
                $itemComponentTotalSellNoTax = $component['totalsellnotax'];
                $itemComponentTotalSellWithTax = $component['totalsellwithtax'];
                $itemComponentTotalWeight = $component['totalweight'];

                // loop around sub-sections
                foreach ($component['subsections'] as &$subsection)
                {
                    self::updateSectionTotal($subsection, $gSession['items'][$currentLine]['itemproductcode'], $currencyExchangeRate,
                            $currencyDecimalPlaces, $itemQty, $itemPageCount, $subsection['quantity'], $currentLine);

                    // add to the sub-totals
                    $itemTotalCost += $subsection['totalcost'];
                    $itemSubTotal += $subsection['totalsell'];
                    $itemTotalSellNoTax += $subsection['totalsellnotax'];
                    $itemTotalSellWithTax += $subsection['totalsellwithtax'];
                    $itemTotalWeight += $subsection['totalweight'];

                    // add to component total
                    $itemComponentTotalCost += $subsection['totalcost'];
                    $itemComponentTotalSell += $subsection['totalsell'];
                    $itemComponentTotalSellNoTax += $subsection['totalsellnotax'];
                    $itemComponentTotalSellWithTax += $subsection['totalsellwithtax'];
                    $itemComponentTotalWeight += $subsection['totalweight'];

                } // loop around sub-sections

                // loop around checkboxes
                foreach ($component['checkboxes'] as &$checkbox)
                {
                    self::updateCheckboxTotal($checkbox, $gSession['items'][$currentLine]['itemproductcode'], $currencyExchangeRate,
                            $currencyDecimalPlaces, $itemQty, $itemPageCount, $currentLine);

                    if ($checkbox['checked'])
                    {
                        // add to the sub-totals
                        $itemTotalCost += $checkbox['totalcost'];
                        $itemSubTotal += $checkbox['totalsell'];
                        $itemTotalSellNoTax += $checkbox['totalsellnotax'];
                        $itemTotalSellWithTax += $checkbox['totalsellwithtax'];
                        $itemTotalWeight += $checkbox['totalweight'];

                        // add to component total
                        $itemComponentTotalCost += $checkbox['totalcost'];
                        $itemComponentTotalSell += $checkbox['totalsell'];
                        $itemComponentTotalSellNoTax += $checkbox['totalsellnotax'];
                        $itemComponentTotalSellWithTax += $checkbox['totalsellwithtax'];
                        $itemComponentTotalWeight += $checkbox['totalweight'];
                    }
                }

                $component['itemcomponenttotalcost'] = $itemComponentTotalCost;
                $component['itemcomponenttotalsell'] = $itemComponentTotalSell;
                $component['itemcomponenttotalsellnotax'] = $itemComponentTotalSellNoTax;
                $component['itemcomponenttotalsellwithtax'] = $itemComponentTotalSellWithTax;
                $component['itemcomponenttotalweight'] = $itemComponentTotalWeight;

            } // loop around sections

            // loop around line footer sections
            foreach ($gSession['items'][$currentLine]['lineFooterSections'] as &$component)
            {
                self::updateSectionTotal($component, $gSession['items'][$currentLine]['itemproductcode'], $currencyExchangeRate,
                        $currencyDecimalPlaces, $itemQty, $itemPageCount, $component['quantity'], $currentLine);

                // add to the sub-totals
                $itemTotalCost += $component['totalcost'];
                $itemSubTotal += $component['totalsell'];
                $itemTotalSellNoTax += $component['totalsellnotax'];
                $itemTotalSellWithTax += $component['totalsellwithtax'];
                $itemTotalWeight += $component['totalweight'];

                // add component prices
                $itemComponentTotalCost = $component['totalcost'];
                $itemComponentTotalSell = $component['totalsell'];
                $itemComponentTotalSellNoTax = $component['totalsellnotax'];
                $itemComponentTotalSellWithTax = $component['totalsellwithtax'];
                $itemComponentTotalWeight = $component['totalweight'];


                // loop around sub-sections
                foreach ($component['subsections'] as &$subsection)
                {
                    self::updateSectionTotal($subsection, $gSession['items'][$currentLine]['itemproductcode'], $currencyExchangeRate,
                            $currencyDecimalPlaces, $itemQty, $itemPageCount, $subsection['quantity'], $currentLine);

                    // add to the sub-totals
                    $itemTotalCost += $subsection['totalcost'];
                    $itemSubTotal += $subsection['totalsell'];
                    $itemTotalSellNoTax += $subsection['totalsellnotax'];
                    $itemTotalSellWithTax += $subsection['totalsellwithtax'];
                    $itemTotalWeight += $subsection['totalweight'];

                    // add to component total
                    $itemComponentTotalCost += $subsection['totalcost'];
                    $itemComponentTotalSell += $subsection['totalsell'];
                    $itemComponentTotalSellNoTax += $subsection['totalsellnotax'];
                    $itemComponentTotalSellWithTax += $subsection['totalsellwithtax'];
                    $itemComponentTotalWeight += $subsection['totalweight'];

                } // loop around sub-sections
                // loop around checkboxes
                foreach ($component['checkboxes'] as &$checkbox)
                {
                    self::updateCheckboxTotal($checkbox, $gSession['items'][$currentLine]['itemproductcode'], $currencyExchangeRate,
                            $currencyDecimalPlaces, $itemQty, $itemPageCount, $currentLine);

                    if ($checkbox['checked'])
                    {
                        // add to the sub-totals
                        $itemTotalCost += $checkbox['totalcost'];
                        $itemSubTotal += $checkbox['totalsell'];
                        $itemTotalSellNoTax += $checkbox['totalsellnotax'];
                        $itemTotalSellWithTax += $checkbox['totalsellwithtax'];
                        $itemTotalWeight += $checkbox['totalweight'];

                        // add to component total
                        $itemComponentTotalCost += $checkbox['totalcost'];
                        $itemComponentTotalSell += $checkbox['totalsell'];
                        $itemComponentTotalSellNoTax += $checkbox['totalsellnotax'];
                        $itemComponentTotalSellWithTax += $checkbox['totalsellwithtax'];
                        $itemComponentTotalWeight += $checkbox['totalweight'];
                    }
                }

                // save the total price for a component
                $component['itemcomponenttotalcost'] = $itemComponentTotalCost;
                $component['itemcomponenttotalsell'] = $itemComponentTotalSell;
                $component['itemcomponenttotalsellnotax'] = $itemComponentTotalSellNoTax;
                $component['itemcomponenttotalsellwithtax'] = $itemComponentTotalSellWithTax;
                $component['itemcomponenttotalweight'] = $itemComponentTotalWeight;

            } // loop around line footer sections

            // loop around line footer checkboxes
            foreach ($gSession['items'][$currentLine]['lineFooterCheckboxes'] as &$checkbox)
            {
                self::updateCheckboxTotal($checkbox, $gSession['items'][$currentLine]['itemproductcode'], $currencyExchangeRate,
                        $currencyDecimalPlaces, $itemQty, $itemPageCount, $currentLine);

                if ($checkbox['checked'])
                {
                    // add to the sub-totals
                    $itemTotalCost += $checkbox['totalcost'];
                    $itemSubTotal += $checkbox['totalsell'];
                    $itemTotalSellNoTax += $checkbox['totalsellnotax'];
                    $itemTotalSellWithTax += $checkbox['totalsellwithtax'];
                    $itemTotalWeight += $checkbox['totalweight'];
                }

            }


            // if no tax is included in the price or if the tax included in the price is different to the line tax then we need to calculate the values with tax now
            if (($gSession['items'][$currentLine]['pricetaxcode'] == '') || ($gSession['items'][$currentLine]['pricetaxrate'] != $gSession['items'][$currentLine]['itemtaxrate']))
            {
                $itemProductTotalTax = UtilsObj::bround($itemProductTotalSell * $gSession['items'][$currentLine]['itemtaxrate'] / 100,
                                $currencyDecimalPlaces);
                $itemProductTotalSellWithTax = $itemProductTotalSell + $itemProductTotalTax;
            }

            $itemTotalSellWithTax += $itemProductTotalSellWithTax;


            // determine which values we are going to total based on if prices are being displayed with tax
            if ($gSession['order']['showpriceswithtax'] == 1)
            {
                $itemProductTotalSell = $itemProductTotalSellWithTax;
                $itemSubTotal = $itemTotalSellWithTax;
                $orderTotalBeforeDiscount += $itemTotalSellWithTax;
            }
            else
            {
                $itemProductTotalSell = $itemProductTotalSellNoTax;
                $itemSubTotal = $itemTotalSellNoTax;
                $orderTotalBeforeDiscount += $itemTotalSellNoTax;
                $itemProductTotalTax = 0.00;
            }

            // calculate the sub-totals
            $itemTotalSellNoTaxNoDiscount = $itemTotalSellNoTax;
            $itemTotalSellNoTaxAllDiscounted = $itemTotalSellNoTax;
            $itemTotalSellWithTaxNoDiscount = $itemTotalSellWithTax;
            $itemTotalSellWithTaxAllDiscounted = $itemTotalSellWithTax;

            $applyDifferentTaxRates = ($gSession['order']['orderalltaxratesequal'] == 0);


            if ($gSession['order']['vouchertype'] == TPX_VOUCHER_TYPE_SCRIPT)
            {
                $gSession['order']['itemsdiscounted'][$currentLine] = false;

                $itemDiscountValue = 0.00;
                $itemDiscountValueNoTax = 0.00;
                $itemDiscountValueWithTax = 0.00;

                if (($gSession['order']['voucheractive'] == 1) && ($gSession['items'][$currentLine]['itemvoucherapplied'] == 1))
                {
                    if (file_exists("../Customise/scripts/EDL_Voucher.php"))
                    {
                        if (is_subclass_of('EDL_VoucherScriptObj', '_Voucher'))
                        {
                            // use the script to calculate the discount to be applied
                            $resultArray = EDL_VoucherScriptObj::calcDiscountedValue($gSession['order']['voucherpromotioncode'],
                                            $gSession['order']['vouchercode'], $currentLine);

                            if (count($resultArray) > 0)
                            {
                                $gSession['order']['voucherdiscounttype'] = 'VALUE';

                                if ($resultArray['discountvalue'] > 0)
                                {
                                    // script has a discount to be applied to a product
                                    $gSession['order']['voucherdiscountsection'] = 'PRODUCT';

                                    $itemDiscountValue = UtilsObj::bround($resultArray['discountvalue'], $gSession['order']['currencydecimalplaces']);

                                    // make sure the discount does not result in a negative item price
                                    if ($itemSubTotal - $itemDiscountValue < 0)
                                    {
                                        $itemDiscountValue = $itemSubTotal;
                                    }

                                    // calculate prices with and without tax
                                    if ($gSession['order']['showpriceswithtax'] == 1)
                                    {
                                        $itemDiscountValueNoTax = UtilsObj::bround(($itemDiscountValue / ($gSession['items'][$currentLine]['itemtaxrate'] + 100)) * 100,
                                                        $gSession['order']['currencydecimalplaces']);
                                        $itemDiscountValueWithTax = $itemDiscountValue;
                                    }
                                    else
                                    {
                                        $itemDiscountValueNoTax = $itemDiscountValue;
                                        $itemDiscountValueWithTax = UtilsObj::bround($itemDiscountValue * (1 + ($gSession['items'][$currentLine]['itemtaxrate'] / 100)),
                                                        $gSession['order']['currencydecimalplaces']);
                                    }

                                    if ($resultArray['discountname'] != '')
                                    {
                                        // the scripted voucher has a name / description
                                        $gSession['items'][$currentLine]['itemvouchername'] = $resultArray['discountname'];
                                        $gSession['order']['vouchername'] = '';
                                    }
                                    else
                                    {
                                        $gSession['items'][$currentLine]['itemvouchername'] = '';
                                    }

                                    $gSession['order']['itemsdiscounted'][$currentLine] = true;
                                }

                                if ($resultArray['shippingdiscountvalue'] > 0)
                                {
                                    // script has a discount to be applied to the shipping
                                    $gSession['order']['voucherdiscountsection'] = 'SHIPPING';
                                    $shippingfromscript = true;

                                    $shippingDiscountValue = UtilsObj::bround($resultArray['shippingdiscountvalue'],
                                                    $gSession['order']['currencydecimalplaces']);

                                    $gSession['shipping'][0]['shippingratediscountvalue'] = $shippingDiscountValue;
                                    $gSession['shipping'][0]['shippingratetotalsell'] = $gSession['shipping'][0]['shippingratesell'] - $shippingDiscountValue;

                                    // make sure the discount value is not negative
                                    if ($gSession['shipping'][0]['shippingratetotalsell'] < 0)
                                    {
                                        $gSession['shipping'][0]['shippingratediscountvalue'] = $gSession['shipping'][0]['shippingratesell'];
                                        $gSession['shipping'][0]['shippingratetotalsell'] = 0;
                                    }

                                    // calculate shipping with and without tax
                                    if ($gSession['order']['showpriceswithtax'] == 1)
                                    {
                                        $shippingDiscountValueNoTax = UtilsObj::bround(($shippingDiscountValue / ($gSession['shipping'][0]['shippingratetaxrate'] + 100)) * 100,
                                                        $gSession['order']['currencydecimalplaces']);
                                        $shippingDiscountValueWithTax = $shippingDiscountValue;
                                    }
                                    else
                                    {
                                        $shippingDiscountValueNoTax = $shippingDiscountValue;
                                        $shippingDiscountValueWithTax = UtilsObj::bround($shippingDiscountValue * (1 + ($gSession['shipping'][0]['shippingratetaxrate'] / 100)),
                                                        $gSession['order']['currencydecimalplaces']);
                                    }

                                    $gSession['shipping'][0]['shippingratetotalsellnotax'] = $gSession['shipping'][0]['shippingratesellnotax'] - $shippingDiscountValueNoTax;
                                    $gSession['shipping'][0]['shippingratetotalsellwithtax'] = $gSession['shipping'][0]['shippingratesellwithtax'] - $shippingDiscountValueWithTax;

                                    if ($resultArray['discountname'] != '')
                                    {
                                        $gSession['order']['vouchername'] = $resultArray['discountname'];
                                    }
                                }

                                if ($resultArray['ordertotaldiscountvalue'] > 0)
                                {
                                    $gSession['order']['voucherdiscountsection'] = 'TOTAL';
                                    $ordertotaldiscountfromscript = true;
                                    $orderDiscountValue = $resultArray['ordertotaldiscountvalue'];

									$gSession['order']['voucherdiscountvalue'] = $orderDiscountValue;

                                    if ($resultArray['discountname'] != '')
                                    {
                                        $gSession['order']['vouchername'] = $resultArray['discountname'];
                                    }
                                }

								if ($resultArray['sellprice'] > 0)
								{
									$gSession['order']['vouchertype'] = TPX_VOUCHER_TYPE_PREPAID;

									$gSession['order']['vouchersellprice'] = (is_numeric($resultArray['sellprice'])) ? $resultArray['sellprice'] : 0.00;
									$gSession['order']['voucheragentfee'] =  (is_numeric($resultArray['agentfee'])) ? $resultArray['agentfee'] : 0.00;
								}
                            }
                        }
                    }
                }

                // re-calculate the totals for the current item
                $itemTotalSell = $itemSubTotal - $itemDiscountValue;
                $itemTotalSellNoTax = $itemTotalSellNoTax - $itemDiscountValueNoTax;
                $itemTotalSellWithTax = $itemTotalSellWithTax - $itemDiscountValueWithTax;

                $itemTotalSellNoTaxAllDiscounted = $itemTotalSellNoTaxAllDiscounted - $itemDiscountValueNoTax;
                $itemTotalSellWithTaxAllDiscounted = $itemTotalSellWithTaxAllDiscounted - $itemDiscountValueWithTax;

                if ($gSession['order']['showpriceswithtax'] == 1)
                {
                    $orderTotalNoTaxWithDiscount += $itemTotalSellWithTax;
                    if ($gSession['items'][$currentLine]['itemvoucherapplied'] == 1)
                    {
                        $orderEligableForDiscount += $itemTotalSellWithTax;
                    }
                }
                else
                {
                    $orderTotalNoTaxWithDiscount += $itemTotalSellNoTax;
                    if ($gSession['items'][$currentLine]['itemvoucherapplied'] == 1)
                    {
                        $orderEligableForDiscount += $itemTotalSellNoTax;
                    }
                }
            }
            else
            {
                // apply discount
                // first check to make sure that the voucher is active and it has been applied against this line
                // if the voucher section is the product then we calculate the discount here
                // if the voucher section is total and the shipping & item tax rates are different we also calculate it here
                // if the voucher is applied to the highest or lowest value items, calculate it here
                // if the voucher section is total and the tax rates are the same we calculate the discount when we have the final order total
                if ((($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_LOWEST_PRICED) && ($currentLine == $lowestID)) ||
                    (($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_HIGHEST_PRICED) && ($currentLine == $highestID)) ||
                    (($gSession['order']['voucherapplicationmethod'] != TPX_VOUCHER_APPLY_LOWEST_PRICED) && ($gSession['order']['voucherapplicationmethod'] != TPX_VOUCHER_APPLY_HIGHEST_PRICED)))
                {
                    $voucherActiveAndApplied = (($gSession['order']['voucheractive'] == 1) && ($gSession['items'][$currentLine]['itemvoucherapplied'] == 1));
                    $discountForProduct = ($gSession['order']['voucherdiscountsection'] == 'PRODUCT');
                    $discountForTotal = (($gSession['order']['voucherdiscountsection'] == 'TOTAL') && (!in_array($gSession['order']['voucherdiscounttype'], $ignoreVoucherTypes)));

                    if ($voucherActiveAndApplied && (($discountForProduct || $applyDiscountToSingleLineItem) || ($discountForTotal && $applyDifferentTaxRates)))
                    {
                        $itemDiscountValue = 0;
                        $itemDiscountValueNoTax = 0;
                        $itemDiscountValueWithTax = 0;

                        $voucherQtyForTaxCalc = $voucherQty;
                        $voucherSectionForTaxCalc = $gSession['order']['voucherdiscountsection'];

                        if ($canApplyvoucherDiscount)
                        {
                            if (($forceApplyDiscountToLineItems) && (($gSession['order']['voucherdiscountsection'] == 'TOTAL') && (in_array($gSession['order']['voucherdiscounttype'], $ignoreVoucherTypes))))
                            {
                                $voucherSectionForTaxCalc = 'PRODUCT';
                            }

                            // retain the qty that the voucher can be applied to for use in each call to the discount calculation
                            // the function will change the value based on the qty the voucher will be applied to during the calculation, but
                            // must be the same value passed in each time.
                            $voucherQtyToUseInTaxCalc = $voucherQtyForTaxCalc;
                            $itemDiscountValue = self::calcDiscountedValue2($itemQty, $gSession['order']['voucherminqty'], $itemSubTotal,
                                            $voucherSectionForTaxCalc, $gSession['order']['voucherdiscounttype'],
                                            $voucherDiscountValue, $voucherQtyToUseInTaxCalc);

                            $voucherQtyToUseInTaxCalc = $voucherQtyForTaxCalc;
                            $itemDiscountValueNoTax = self::calcDiscountedValue2($itemQty, $gSession['order']['voucherminqty'], $itemTotalSellNoTax,
                                            $voucherSectionForTaxCalc, $gSession['order']['voucherdiscounttype'],
                                            $voucherDiscountValue, $voucherQtyToUseInTaxCalc);

                            $voucherQtyToUseInTaxCalc = $voucherQtyForTaxCalc;
                            $itemDiscountValueWithTax = self::calcDiscountedValue2($itemQty, $gSession['order']['voucherminqty'], $itemTotalSellWithTax,
                                            $voucherSectionForTaxCalc, $gSession['order']['voucherdiscounttype'],
                                            $voucherDiscountValue, $voucherQtyToUseInTaxCalc);

                            $voucherQtyForTaxCalc = $voucherQtyToUseInTaxCalc;
                        }

                        // re-calculate the discount value of the voucher if required
                        if ((($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($gSession['order']['voucherdiscounttype'] == 'VALUE')) ||
                             (($gSession['order']['voucherdiscountsection'] == 'PRODUCT') && ($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER) && ($gSession['order']['voucherdiscounttype'] != 'VALUESET')))
                        {
                            if ($gSession['order']['showpriceswithtax'] == 1)
                            {
                                $voucherDiscountValue = $voucherDiscountValue - $itemDiscountValueWithTax;
                            }
                            else
                            {
                                $voucherDiscountValue = $voucherDiscountValue - $itemDiscountValueNoTax;
                            }

                            //stop voucher discount value falling below 0 (and adding to price)
                            $voucherDiscountValue = Max($voucherDiscountValue, 0);

                            if ($voucherDiscountValue == 0)
                            {
                                $canApplyvoucherDiscount = false;
                            }
                        }
                        elseif ((($forceApplyDiscountToLineItems) && (($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER) && (in_array($gSession['order']['voucherdiscounttype'], $ignoreVoucherTypes)))) ||
                                 (($gSession['order']['voucherdiscountsection'] == 'PRODUCT') && ($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER) && (in_array($gSession['order']['voucherdiscounttype'], $ignoreVoucherTypes))))
                        {
                            // if the value set is distributed over order, apply the discount to the first line and reduce the remaining
                            // allowed items to free on following lines
                            $voucherDiscountValue = 0;
                        }


                        // check that the voucher meets the criteria and can be applied to the order
                        if ((($gSession['order']['voucherdiscountsection'] == 'PRODUCT') && ($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER) &&
                            (($gSession['order']['voucherdiscounttype'] != 'VALUESET') || ($gSession['order']['voucherdiscounttype'] == 'VALUE'))) &&
                            ($voucherQtyForTaxCalc > 0))
                        {
                            $canApplyvoucherDiscount = true;
                        }

                        // check that the voucher meets the criteria and can be applied to the order
                        if (($gSession['order']['voucherdiscountsection'] == 'PRODUCT') && ($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER) &&
                            (($gSession['order']['voucherdiscounttype'] == 'VALUESET') || ($gSession['order']['voucherdiscounttype'] == 'PERCENT')))
                        {
                            $canApplyvoucherDiscount = true;
                        }

                    }
                    elseif (($gSession['order']['voucherdiscounttype'] == 'VALUESET') && ($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($applyDifferentTaxRates))
                    {
                        // calculate value set discount running total
                        //1. In a temp variable add order total with discount without tax and the line total without tax together.
                        if ($gSession['order']['showpriceswithtax'] == 1)
                        {
                            //$orderCalcTemp = $orderTotalNoTaxWithDiscount + $itemTotalSellWithTax;
                            $orderCalcTemp = $orderEligableForDiscount + $itemTotalSellWithTax;
                        }
                        else
                        {
                            //$orderCalcTemp = $orderTotalNoTaxWithDiscount + $itemTotalSellNoTax;
                            $orderCalcTemp = $orderEligableForDiscount + $itemTotalSellNoTax;
                        }

                        if ($orderCalcTemp > $gSession['order']['voucherdiscountvalue'])
                        {
                            //2. If the temp variable > voucher value then we will need to apply the difference as discount for the line in the same way we are doing already.
                            //3. order total with discount without tax would then have the line total without tax with discount added to it.
                            $tempDiscountVal = ($orderCalcTemp - $gSession['order']['voucherdiscountvalue']);

                            $itemDiscountValue = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'], $itemSubTotal,
                                                                            $gSession['order']['voucherdiscountsection'], 'VALUE', $tempDiscountVal);
                            $itemDiscountValueNoTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'], $itemTotalSellNoTax,
                                                                            $gSession['order']['voucherdiscountsection'], 'VALUE', $tempDiscountVal);
                            $itemDiscountValueWithTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'], $itemTotalSellWithTax,
                                                                            $gSession['order']['voucherdiscountsection'], 'VALUE', $tempDiscountVal);
                        }


                    }
                }

                $itemTotalSell = $itemSubTotal - $itemDiscountValue;
                $itemTotalSellNoTax = $itemTotalSellNoTax - $itemDiscountValueNoTax;
                $itemTotalSellWithTax = $itemTotalSellWithTax - $itemDiscountValueWithTax;

                if ($gSession['order']['showpriceswithtax'] == 1)
                {
                    $orderTotalNoTaxWithDiscount += $itemTotalSellWithTax;
                    if ($gSession['items'][$currentLine]['itemvoucherapplied'] == 1)
                    {
                        $orderEligableForDiscount += $itemTotalSellWithTax;
                    }
                }
                else
                {
                    $orderTotalNoTaxWithDiscount += $itemTotalSellNoTax;
                    if ($gSession['items'][$currentLine]['itemvoucherapplied'] == 1)
                    {
                        $orderEligableForDiscount += $itemTotalSellNoTax;
                    }
                }

                if ((($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_LOWEST_PRICED) && ($i == $lowestID)) ||
                        (($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_HIGHEST_PRICED) && ($i == $highestID)) ||
                        (($gSession['order']['voucherapplicationmethod'] != TPX_VOUCHER_APPLY_LOWEST_PRICED) && ($gSession['order']['voucherapplicationmethod'] != TPX_VOUCHER_APPLY_HIGHEST_PRICED)))
                {
                    $voucherActiveAndApplied = (($gSession['order']['voucheractive'] == 1) && ($gSession['items'][$currentLine]['itemvoucherapplied'] == 1));
                    $discountForProduct = ($gSession['order']['voucherdiscountsection'] == 'PRODUCT');
                    $discountForTotal = (($gSession['order']['voucherdiscountsection'] == 'TOTAL') && (!in_array($gSession['order']['voucherdiscounttype'], $ignoreVoucherTypes)));

                    // calculate the item total with all relevant discount to calculate the value used in the shipping rate comparisons
                    if ($voucherActiveAndApplied && ($discountForProduct || $applyDiscountToSingleLineItem || $discountForTotal))
                    {
                        $itemDiscountValueNoTax = 0;
                        $itemDiscountValueWithTax = 0;

                        $voucherQtyForTaxCalc = $voucherQty;
                        $voucherSectionForTaxCalc = $gSession['order']['voucherdiscountsection'];

                        if (($forceApplyDiscountToLineItems) && (($gSession['order']['voucherdiscountsection'] == 'TOTAL') && (in_array($gSession['order']['voucherdiscounttype'], $ignoreVoucherTypes))))
                        {
                            $voucherSectionForTaxCalc = 'PRODUCT';
                        }

                        if ($canApplyvoucherDiscount2)
                        {
                            // retain the qty that the voucher can be applied to for use in each call to the discount calculation
                            // the function will change the value based on the qty the voucher will be applied to during the calculation, but
                            // must be the same value passed in each time.
                            $voucherQtyToUseInTaxCalc = $voucherQtyForTaxCalc;
                            $itemDiscountValueNoTax = self::calcDiscountedValue2($itemQty, $gSession['order']['voucherminqty'],
                                            $itemTotalSellNoTaxAllDiscounted, $voucherSectionForTaxCalc,
                                            $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2, $voucherQtyToUseInTaxCalc);

                            $voucherQtyToUseInTaxCalc = $voucherQtyForTaxCalc;
                            $itemDiscountValueWithTax = self::calcDiscountedValue2($itemQty, $gSession['order']['voucherminqty'],
                                            $itemTotalSellWithTaxAllDiscounted, $voucherSectionForTaxCalc,
                                            $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2, $voucherQtyToUseInTaxCalc);

                            $voucherQtyForTaxCalc = $voucherQtyToUseInTaxCalc;
                        }
                        $itemTotalSellNoTaxAllDiscounted = $itemTotalSellNoTaxAllDiscounted - $itemDiscountValueNoTax;
                        $itemTotalSellWithTaxAllDiscounted = $itemTotalSellWithTaxAllDiscounted - $itemDiscountValueWithTax;

                        if ((($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($gSession['order']['voucherdiscounttype'] == 'VALUE')) ||
                            (($gSession['order']['voucherdiscountsection'] == 'PRODUCT') && ($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER) && ($gSession['order']['voucherdiscounttype'] != 'VALUESET')))
                        {
                            if ($gSession['order']['showpriceswithtax'] == 1)
                            {
                                $voucherDiscountValue2 = $voucherDiscountValue2 - $itemDiscountValueWithTax;
                            }
                            else
                            {
                                $voucherDiscountValue2 = $voucherDiscountValue2 - $itemDiscountValueNoTax;
                            }
                            //stop voucher discount value falling below 0 (and adding to price)
                            $voucherDiscountValue2 = Max($voucherDiscountValue2, 0);

                            if ($voucherDiscountValue2 == 0)
                            {
                                $canApplyvoucherDiscount2 = false;
                            }
                        }
                        elseif ((($forceApplyDiscountToLineItems) && (($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER) && (in_array($gSession['order']['voucherdiscounttype'], $ignoreVoucherTypes)))) ||
                                (($gSession['order']['voucherdiscountsection'] == 'PRODUCT') && ($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER) && (in_array($gSession['order']['voucherdiscounttype'], $ignoreVoucherTypes))))
                        {
                            // if the value set is distributed over order, apply the discount to the first line and reduce the remaining
                            // allowed items to free on following lines
                            if ($gSession['order']['showpriceswithtax'] == 1)
                            {
                                $voucherDiscountValue2 = $voucherDiscountValue2 - $itemDiscountValueWithTax;
                            }
                            else
                            {
                                $voucherDiscountValue2 = $voucherDiscountValue2 - $itemDiscountValueNoTax;
                            }

                            //stop voucher discount value falling below 0 (and adding to price)
                            $voucherDiscountValue2 = Max($voucherDiscountValue2, 0);
                        }

                        if ((($gSession['order']['voucherdiscountsection'] == 'PRODUCT') && ($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER) &&
                            (($gSession['order']['voucherdiscounttype'] != 'VALUESET') || ($gSession['order']['voucherdiscounttype'] == 'VALUE'))) &&
                            ($voucherQtyForTaxCalc > 0))
                        {
                            $canApplyvoucherDiscount2 = true;
                        }

                        if (($gSession['order']['voucherdiscountsection'] == 'PRODUCT') && ($gSession['order']['voucherapplicationmethod'] == TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER) &&
                            (($gSession['order']['voucherdiscounttype'] == 'VALUESET') || ($gSession['order']['voucherdiscounttype'] == 'PERCENT')))
                        {
                            $canApplyvoucherDiscount2 = true;
                        }
                    }
                }

            }

            if ($gSession['order']['showpriceswithtax'] == 1)
            {
                $itemDiscountValue = $itemDiscountValueWithTax;
            }


            // calculate the total tax for the line
            if ($gSession['order']['showpriceswithtax'] == 0)
            {
                $itemTaxTotal = UtilsObj::bround($itemTotalSell * $gSession['items'][$currentLine]['itemtaxrate'] / 100,
                                $currencyDecimalPlaces);
            }
            else
            {
                $taxCalc = 1 + ($gSession['items'][$currentLine]['itemtaxrate'] / 100);
                $itemTotalWithoutTax = UtilsObj::bround($itemTotalSell / $taxCalc, $currencyDecimalPlaces);
                $itemTaxTotal = $itemTotalSell - $itemTotalWithoutTax;
            }

            self::updateOrderTaxBreakDownSummary($itemTaxRateCode, $itemTotalSellNoTax, $itemTaxTotal);

            if ($gSession['order']['orderalltaxratesequal'] == 0)
            {
                $calcShippingTax = 1;
            }

            $gSession['items'][$currentLine]['itemproductunitsell'] = $itemProductUnitSell;
            $gSession['items'][$currentLine]['itemproducttotalweight'] = $itemProductTotalWeight;
            $gSession['items'][$currentLine]['itemproducttotalcost'] = $itemProductTotalCost;
            $gSession['items'][$currentLine]['itemproducttotalsell'] = $itemProductTotalSell;
            $gSession['items'][$currentLine]['itemproducttotalsellnotax'] = $itemProductTotalSellNoTax;
            $gSession['items'][$currentLine]['itemproducttotaltax'] = $itemProductTotalTax;
            $gSession['items'][$currentLine]['itemproducttotalsellwithtax'] = $itemProductTotalSellWithTax;

            $gSession['items'][$currentLine]['itemtotalcost'] = $itemTotalCost;
            $gSession['items'][$currentLine]['itemsubtotal'] = $itemSubTotal;
            $gSession['items'][$currentLine]['itemdiscountvalue'] = $itemDiscountValue;
            $gSession['items'][$currentLine]['itemdiscountvaluenotax'] = $itemDiscountValueNoTax;
            $gSession['items'][$currentLine]['itemdiscountvaluenwithtax'] = $itemDiscountValueWithTax;
            $gSession['items'][$currentLine]['itemtotalsell'] = $itemTotalSell;
            $gSession['items'][$currentLine]['itemtotalsellnotax'] = $itemTotalSellNoTax;
            $gSession['items'][$currentLine]['itemtotalsellnotaxnodiscount'] = $itemTotalSellNoTaxNoDiscount;
            $gSession['items'][$currentLine]['itemtotalsellnotaxalldiscounted'] = $itemTotalSellNoTaxAllDiscounted;
            $gSession['items'][$currentLine]['itemtotalsellwithtax'] = $itemTotalSellWithTax;
            $gSession['items'][$currentLine]['itemtotalsellwithtaxnodiscount'] = $itemTotalSellWithTaxNoDiscount;
            $gSession['items'][$currentLine]['itemtotalsellwithtaxalldiscounted'] = $itemTotalSellWithTaxAllDiscounted;

            $gSession['items'][$currentLine]['itemtotalweight'] = $itemTotalWeight;
            $gSession['items'][$currentLine]['itemtaxtotal'] = $itemTaxTotal;


            // update the order totals
            $orderTotal += $itemTotalSell;
            $orderTotalCost += $itemTotalCost;
            $orderTotalSell += $itemTotalSell;
            $orderTotalTax += $itemTaxTotal;

            // if the voucher has been applied add the item total to the amount we can apply discount on
            if ($gSession['items'][$currentLine]['itemvoucherapplied'] == 1)
            {
                $orderTotalForDiscount += $itemTotalSell;
            }

            $orderTotalItemCost += $itemTotalCost;
            $orderTotalItemSell += $itemTotalSell;
            $orderTotalItemSellNoTax += $itemTotalSellNoTax;
            $orderTotalItemSellNoTaxNoDiscount += $itemTotalSellNoTaxNoDiscount;
            $orderTotalItemSellNoTaxAllDiscounted += $itemTotalSellNoTaxAllDiscounted;
            $orderTotalItemSellWithTax += $itemTotalSellWithTax;
            $orderTotalItemSellWithTaxNoDiscount += $itemTotalSellWithTaxNoDiscount;
            $orderTotalItemSellWithTaxAllDiscounted += $itemTotalSellWithTaxAllDiscounted;
            $orderTotalItemTax += $itemTaxTotal;

            // set the order total weight to the accumulative total of the cart items weight
            $orderTotalWeight += $itemTotalWeight;

            // reset the number of products left to apply the discount to
            if ($gSession['order']['voucherapplicationmethod'] != TPX_VOUCHER_APPLY_EACH_MATCHING_PRODUCT)
            {
                $voucherQty = $voucherQtyForTaxCalc;
            }
        } // loop around order items

        $orderFooterTotalCost = 0.00;
        $orderFooterSubTotal = 0.00;
        $orderFooterTotalSellNoTax = 0.00;
        $orderFooterTotalSellWithTax = 0.00;
        $orderFooterTotalWeight = 0.00;
        $orderFooterTaxTotal = 0.00;
        $orderFooterTotalSell = 0.00;
        $orderFooterTotalNoTaxNoDiscount = 0.00;
        $orderFooterTotalWithTaxNoDiscount = 0.00;

        // order footer sections and order footer checkboxes
        // loop around sub-sections
        foreach ($gSession['order']['orderFooterSections'] as &$component)
        {
            $itemPageCount = $component['itempagecount'];
            $productCode = $component['itemproductcode'];

            $component['discountvalue'] = 0.0;
            $component['discountedvalue'] = 0.0;
            $component['discountedtax'] = 0.0;
            $component['discountvaluenotax'] = 0.0;
            $component['discountvaluewithtax'] = 0.0;

            self::updateSectionTotal($component, $productCode, $currencyExchangeRate, $currencyDecimalPlaces, $component['itemqty'],
                    $itemPageCount, $component['quantity'], -1);

            $componentDiscountValue = 0;
            $componentDiscountValueNoTax = 0;
            $componentDiscountValueWithTax = 0;

            $component['subtotal'] = $component['totalsell'];

            $orderFooterTotalNoTaxNoDiscount += $component['totalsellnotax'];
            $orderFooterTotalWithTaxNoDiscount += $component['totalsellwithtax'];

            if (($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($gSession['order']['voucheractive'] == 1) &&
                (!in_array($gSession['order']['voucherdiscounttype'], $ignoreVoucherTypes)))
            {
                if (($gSession['order']['orderalltaxratesequal'] == 0) || ($applyDiscountToSingleLineItem))
                {
                    $componentDiscountValue = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                    $component['totalsell'], $gSession['order']['voucherdiscountsection'],
                                    $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2);
                    $componentDiscountValueNoTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                    $component['totalsellnotax'], $gSession['order']['voucherdiscountsection'],
                                    $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2);
                    $componentDiscountValueWithTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                    $component['totalsellwithtax'], $gSession['order']['voucherdiscountsection'],
                                    $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2);

                    $component['subtotal'] = $component['totalsell'] - $componentDiscountValue;
                    $component['totalsellnotax'] = $component['totalsellnotax'] - $componentDiscountValueNoTax;
                    $component['totalsellwithtax'] = $component['totalsellwithtax'] - $componentDiscountValueWithTax;

                    if ($gSession['order']['voucherdiscounttype'] == 'VALUE' || $gSession['order']['voucherdiscounttype'] == 'BOGVOFF')
                    {
                        if ($gSession['order']['showpriceswithtax'] == 1)
                        {
                            $voucherDiscountValue2 = $voucherDiscountValue2 - $componentDiscountValueWithTax;
                        }
                        else
                        {
                            $voucherDiscountValue2 = $voucherDiscountValue2 - $componentDiscountValue;
                        }
                    }

                    if ($gSession['order']['showpriceswithtax'] == 1)
                    {
                        $componentDiscountValue = $componentDiscountValueWithTax;
                    }

                    $component['discountvalue'] = $componentDiscountValue;
                    $component['discountvaluenotax'] = $componentDiscountValueNoTax;
                    $component['discountvaluewithtax'] = $componentDiscountValueWithTax;
                }
            }
            elseif (($gSession['order']['voucherdiscounttype'] == 'VALUESET') && ($gSession['order']['voucherdiscountsection'] == 'TOTAL') && (($gSession['order']['orderalltaxratesequal'] == 0) || ($applyDiscountToSingleLineItem)))
            {
                // calculate value set discount running total
                //1. In a temp variable add order total with discount without tax and the line total without tax together.
                if ($gSession['order']['showpriceswithtax'] == 1)
                {
                    //$orderCalcTemp = $orderTotalNoTaxWithDiscount + $component['totalsellwithtax'];
                    $orderCalcTemp = $orderEligableForDiscount + $component['totalsellwithtax'];
                }
                else
                {
                    //$orderCalcTemp = $orderTotalNoTaxWithDiscount + $component['totalsellnotax'];
                    $orderCalcTemp = $orderEligableForDiscount + $component['totalsellnotax'];
                }

                if ($orderCalcTemp > $gSession['order']['voucherdiscountvalue'])
                {
                    //2. If the temp variable > voucher value then we will need to apply the difference as discount for the line in the same way we are doing already.
                    //3. order total with discount without tax would then have the line total without tax with discount added to it.
                    $tempDiscountVal = ($orderCalcTemp - $gSession['order']['voucherdiscountvalue']);

                    $componentDiscountValue = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                    $component['totalsell'], $gSession['order']['voucherdiscountsection'], 'VALUE', $tempDiscountVal);
                    $componentDiscountValueNoTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                    $component['totalsellnotax'], $gSession['order']['voucherdiscountsection'], 'VALUE', $tempDiscountVal);
                    $componentDiscountValueWithTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                    $component['totalsellwithtax'], $gSession['order']['voucherdiscountsection'], 'VALUE', $tempDiscountVal);

                    $component['subtotal'] = $component['totalsell'] - $componentDiscountValue;
                    $component['totalsellnotax'] = $component['totalsellnotax'] - $componentDiscountValueNoTax;
                    $component['totalsellwithtax'] = $component['totalsellwithtax'] - $componentDiscountValueWithTax;

                    $component['discountvalue'] = $componentDiscountValue;
                    $component['discountvaluenotax'] = $componentDiscountValueNoTax;
                    $component['discountvaluewithtax'] = $componentDiscountValueWithTax;

                    if ($gSession['order']['showpriceswithtax'] == 1)
                    {
                        $orderTotalNoTaxWithDiscount += $component['totalsellwithtax']; //$componentDiscountValueWithTax;
                        $orderEligableForDiscount += $component['totalsellwithtax'];
                    }
                    else
                    {
                        $orderTotalNoTaxWithDiscount += $component['totalsellnotax']; //$componentDiscountValueNoTax;
                        $orderEligableForDiscount += $component['totalsellnotax'];
                    }
                }
                else
                {
                    if ($gSession['order']['showpriceswithtax'] == 1)
                    {
                        $orderTotalNoTaxWithDiscount += $component['totalsellwithtax'];
                        $orderEligableForDiscount += $component['totalsellwithtax'];
                    }
                    else
                    {
                        $orderTotalNoTaxWithDiscount += $component['totalsellnotax'];
                        $orderEligableForDiscount += $component['totalsellnotax'];
                    }
                }
            }

            // add to the sub-totals
            $itemTaxRateArray = self::getTaxRateArrayFromTaxLevel($component['orderfootertaxlevel']);

            self::updateOrderTaxBreakDownSummary($itemTaxRateArray['code'], $component['totalsellnotax'], $component['totaltax']);

            //calculate orderFooterTaxTotal based on the components tax rate
            if ($gSession['order']['showpriceswithtax'] == 0)
            {
                $taxVal = UtilsObj::bround($component['subtotal'] * $component['orderfootertaxrate'] / 100, $currencyDecimalPlaces);
                $component['totalsellwithtax'] = $component['subtotal'] + $taxVal;
                $orderFooterTaxTotal += $taxVal;
            }
            else
            {
                $taxCalc = 1 + ($component['orderfootertaxrate'] / 100);
                $component['totalsellnotax'] = UtilsObj::bround($component['subtotal'] / $taxCalc, $currencyDecimalPlaces);
                $orderFooterTaxTotal += ($component['subtotal'] - $component['totalsellnotax']);
            }

            $orderFooterTotalCost += $component['totalcost'];
            $orderFooterTotalSell += $component['subtotal'];
            $orderFooterTotalSellNoTax += $component['totalsellnotax'];
            $orderFooterTotalSellWithTax += $component['totalsellwithtax'];
            $orderFooterTotalWeight += $component['totalweight'];

            // add component prices
            $itemComponentTotalCost = $component['totalcost'];
            $itemComponentTotalSell = $component['totalsell'];
            $itemComponentTotalSellNoTax = $component['totalsellnotax'];
            $itemComponentTotalSellWithTax = $component['totalsellwithtax'];
            $itemComponentTotalWeight = $component['totalweight'];

            // loop around sub-sections
            foreach ($component['subsections'] as &$subsection)
            {
                $subsection['discountvalue'] = 0.0;
                $subsection['discountedvalue'] = 0.0;
                $subsection['discountedtax'] = 0.0;
                $subsection['discountvaluenotax'] = 0.0;
                $subsection['discountvaluewithtax'] = 0.0;

                self::updateSectionTotal($subsection, $productCode, $currencyExchangeRate, $currencyDecimalPlaces, $subsection['itemqty'],
                        $itemPageCount, $subsection['quantity'], -1);

                $subsectionDiscountValue = 0;
                $subsectionDiscountValueNoTax = 0;
                $subsectionDiscountValueWithTax = 0;

                $subsection['subtotal'] = $subsection['totalsell'];

                $orderFooterTotalNoTaxNoDiscount += $subsection['totalsellnotax'];
                $orderFooterTotalWithTaxNoDiscount += $subsection['totalsellwithtax'];

                if (($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($gSession['order']['voucheractive'] == 1)
                        && (!in_array($gSession['order']['voucherdiscounttype'], $ignoreVoucherTypes)))
                {
                    if ($gSession['order']['orderalltaxratesequal'] == 0)
                    {
                        $subsectionDiscountValue = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                        $subsection['totalsell'], $gSession['order']['voucherdiscountsection'],
                                        $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2);
                        $subsectionDiscountValueNoTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                        $subsection['totalsellnotax'], $gSession['order']['voucherdiscountsection'],
                                        $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2);
                        $subsectionDiscountValueWithTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                        $subsection['totalsellwithtax'], $gSession['order']['voucherdiscountsection'],
                                        $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2);

                        $subsection['subtotal'] = $subsection['totalsell'] - $subsectionDiscountValue;
                        $subsection['totalsellnotax'] = $subsection['totalsellnotax'] - $subsectionDiscountValueNoTax;
                        $subsection['totalsellwithtax'] = $subsection['totalsellwithtax'] - $subsectionDiscountValueWithTax;

                        if ($gSession['order']['voucherdiscounttype'] == 'VALUE' || $gSession['order']['voucherdiscounttype'] == 'BOGVOFF')
                        {
                            if ($gSession['order']['showpriceswithtax'] == 1)
                            {
                                $voucherDiscountValue2 = $voucherDiscountValue2 - $subsectionDiscountValueWithTax;
                            }
                            else
                            {
                                $voucherDiscountValue2 = $voucherDiscountValue2 - $subsectionDiscountValue;
                            }
                        }

                        if ($gSession['order']['showpriceswithtax'] == 1)
                        {
                            $subsectionDiscountValue = $subsectionDiscountValueWithTax;
                        }

                        $subsection['discountvalue'] = $subsectionDiscountValue;
                        $subsection['discountvaluenotax'] = $subsectionDiscountValueNoTax;
                        $subsection['discountvaluewithtax'] = $subsectionDiscountValueWithTax;
                    }
                }
                elseif (($gSession['order']['voucherdiscounttype'] == 'VALUESET') && ($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($gSession['order']['orderalltaxratesequal'] == 0))
                {
                    // calculate value set discount running total
                    //1. In a temp variable add order total with discount without tax and the line total without tax together.
                    if ($gSession['order']['showpriceswithtax'] == 1)
                    {
                        $orderCalcTemp = $orderTotalNoTaxWithDiscount + $subsection['totalsellwithtax'];
                    }
                    else
                    {
                        $orderCalcTemp = $orderTotalNoTaxWithDiscount + $subsection['totalsellnotax'];
                    }

                    if ($orderCalcTemp > $gSession['order']['voucherdiscountvalue'])
                    {
                        //2. If the temp variable > voucher value then we will need to apply the difference as discount for the line in the same way we are doing already.
                        //3. order total with discount without tax would then have the line total without tax with discount added to it.
                        $tempDiscountVal = ($orderCalcTemp - $gSession['order']['voucherdiscountvalue']);

                        $subsectionDiscountValue = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                        $subsection['totalsell'], $gSession['order']['voucherdiscountsection'], 'VALUE', $tempDiscountVal);
                        $subsectionDiscountValueNoTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                        $subsection['totalsellnotax'], $gSession['order']['voucherdiscountsection'], 'VALUE',
                                        $tempDiscountVal);
                        $subsectionDiscountValueWithTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                        $subsection['totalsellwithtax'], $gSession['order']['voucherdiscountsection'], 'VALUE',
                                        $tempDiscountVal);

                        $subsection['subtotal'] = $subsection['totalsell'] - $subsectionDiscountValue;
                        $subsection['totalsellnotax'] = $subsection['totalsellnotax'] - $subsectionDiscountValueNoTax;
                        $subsection['totalsellwithtax'] = $subsection['totalsellwithtax'] - $subsectionDiscountValueWithTax;

                        $subsection['discountvalue'] = $subsectionDiscountValue;
                        $subsection['discountvaluenotax'] = $subsectionDiscountValueNoTax;
                        $subsection['discountvaluewithtax'] = $subsectionDiscountValueWithTax;

                        if ($gSession['order']['showpriceswithtax'] == 1)
                        {
                            $orderTotalNoTaxWithDiscount += $subsection['totalsellwithtax']; //$subsectionDiscountValueWithTax;
                        }
                        else
                        {
                            $orderTotalNoTaxWithDiscount += $subsection['totalsellnotax']; //$subsectionDiscountValueNoTax;
                        }
                    }
                    else
                    {
                        if ($gSession['order']['showpriceswithtax'] == 1)
                        {
                            $orderTotalNoTaxWithDiscount += $subsection['totalsellwithtax'];
                        }
                        else
                        {
                            $orderTotalNoTaxWithDiscount += $subsection['totalsellnotax'];
                        }
                    }
                }

                // add to the sub-totals
                $itemTaxRateArray = self::getTaxRateArrayFromTaxLevel($subsection['orderfootertaxlevel']);

                self::updateOrderTaxBreakDownSummary($itemTaxRateArray['code'], $subsection['totalsellnotax'], $subsection['totaltax']);

                //calculate orderFooterTaxTotal based on the components tax rate
                if ($gSession['order']['showpriceswithtax'] == 0)
                {
                    $taxVal = UtilsObj::bround($subsection['subtotal'] * $subsection['orderfootertaxrate'] / 100, $currencyDecimalPlaces);
                    $subsection['totalsellwithtax'] = $subsection['subtotal'] + $taxVal;
                    $orderFooterTaxTotal += $taxVal;
                }
                else
                {
                    $taxCalc = 1 + ($subsection['orderfootertaxrate'] / 100);
                    $subsection['totalsellnotax'] = UtilsObj::bround($subsection['subtotal'] / $taxCalc, $currencyDecimalPlaces);
                    $orderFooterTaxTotal += ($subsection['subtotal'] - $subsection['totalsellnotax']);
                }

                $orderFooterTotalCost += $subsection['totalcost'];
                $orderFooterTotalSell += $subsection['subtotal'];
                //$orderFooterTotalSell += $subsection['discountedvalue'];
                $orderFooterTotalSellNoTax += $subsection['totalsellnotax'];
                $orderFooterTotalSellWithTax += $subsection['totalsellwithtax'];
                $orderFooterTotalWeight += $subsection['totalweight'];

                // add component prices
                $itemComponentTotalCost += $subsection['totalcost'];
                $itemComponentTotalSell += $subsection['subtotal'];
                $itemComponentTotalSellNoTax += $subsection['totalsellnotax'];
                $itemComponentTotalSellWithTax += $subsection['totalsellwithtax'];
                $itemComponentTotalWeight += $subsection['totalweight'];

            } // loop around sub-sections

            // loop around checkboxes
            foreach ($component['checkboxes'] as &$checkbox)
            {
                $checkbox['discountvalue'] = 0.0;
                $checkbox['discountedvalue'] = 0.0;
                $checkbox['discountedtax'] = 0.0;
                $checkbox['discountvaluenotax'] = 0.0;
                $checkbox['discountvaluewithtax'] = 0.0;

                self::updateCheckboxTotal($checkbox, $checkbox['productcode'], $currencyExchangeRate, $currencyDecimalPlaces,
                        $checkbox['itemqty'], $itemPageCount, -1);

                if ($checkbox['checked'])
                {
                    $checkboxDiscountValue = 0;
                    $checkboxDiscountValueNoTax = 0;
                    $checkboxDiscountValueWithTax = 0;

                    $checkbox['subtotal'] = $checkbox['totalsell'];

                    $orderFooterTotalNoTaxNoDiscount += $checkbox['totalsellnotax'];
                    $orderFooterTotalWithTaxNoDiscount += $checkbox['totalsellwithtax'];

                    if (($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($gSession['order']['voucheractive'] == 1)
                            && (!in_array($gSession['order']['voucherdiscounttype'], $ignoreVoucherTypes)))
                    {
                        if ($gSession['order']['orderalltaxratesequal'] == 0)
                        {
                            $checkboxDiscountValue = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                            $checkbox['totalsell'], $gSession['order']['voucherdiscountsection'],
                                            $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2);
                            $checkboxDiscountValueNoTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                            $checkbox['totalsellnotax'], $gSession['order']['voucherdiscountsection'],
                                            $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2);
                            $checkboxDiscountValueWithTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                            $checkbox['totalsellwithtax'], $gSession['order']['voucherdiscountsection'],
                                            $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2);

                            $checkbox['subtotal'] = $checkbox['totalsell'] - $checkboxDiscountValue;
                            $checkbox['totalsellnotax'] = $checkbox['totalsellnotax'] - $checkboxDiscountValueNoTax;
                            $checkbox['totalsellwithtax'] = $checkbox['totalsellwithtax'] - $checkboxDiscountValueWithTax;

                            if ($gSession['order']['voucherdiscounttype'] == 'VALUE' || $gSession['order']['voucherdiscounttype'] == 'BOGVOFF')
                            {
                                if ($gSession['order']['showpriceswithtax'] == 1)
                                {
                                    $voucherDiscountValue2 = $voucherDiscountValue2 - $checkboxDiscountValueWithTax;
                                }
                                else
                                {
                                    $voucherDiscountValue2 = $voucherDiscountValue2 - $checkboxDiscountValue;
                                }
                            }

                            if ($gSession['order']['showpriceswithtax'] == 1)
                            {
                                $checkboxDiscountValue = $checkboxDiscountValueWithTax;
                            }

                            $checkbox['discountvalue'] = $checkboxDiscountValue;
                            $checkbox['discountvaluenotax'] = $checkboxDiscountValueNoTax;
                            $checkbox['discountvaluewithtax'] = $checkboxDiscountValueWithTax;
                        }
                    }
                    elseif (($gSession['order']['voucherdiscounttype'] == 'VALUESET') && ($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($gSession['order']['orderalltaxratesequal'] == 0))
                    {
                        // calculate value set discount running total
                        //1. In a temp variable add order total with discount without tax and the line total without tax together.
                        if ($gSession['order']['showpriceswithtax'] == 1)
                        {
                            $orderCalcTemp = $orderTotalNoTaxWithDiscount + $checkbox['totalsellwithtax'];
                        }
                        else
                        {
                            $orderCalcTemp = $orderTotalNoTaxWithDiscount + $checkbox['totalsellnotax'];
                        }

                        if ($orderCalcTemp > $gSession['order']['voucherdiscountvalue'])
                        {
                            //2. If the temp variable > voucher value then we will need to apply the difference as discount for the line in the same way we are doing already.
                            //3. order total with discount without tax would then have the line total without tax with discount added to it.
                            $tempDiscountVal = ($orderCalcTemp - $gSession['order']['voucherdiscountvalue']);

                            $checkboxDiscountValue = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                            $checkbox['totalsell'], $gSession['order']['voucherdiscountsection'], 'VALUE', $tempDiscountVal);
                            $checkboxDiscountValueNoTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                            $checkbox['totalsellnotax'], $gSession['order']['voucherdiscountsection'], 'VALUE',
                                            $tempDiscountVal);
                            $checkboxDiscountValueWithTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                            $checkbox['totalsellwithtax'], $gSession['order']['voucherdiscountsection'], 'VALUE',
                                            $tempDiscountVal);

                            $checkbox['subtotal'] = $checkbox['totalsell'] - $checkboxDiscountValue;
                            $checkbox['totalsellnotax'] = $checkbox['totalsellnotax'] - $checkboxDiscountValueNoTax;
                            $checkbox['totalsellwithtax'] = $checkbox['totalsellwithtax'] - $checkboxDiscountValueWithTax;

                            $checkbox['discountvalue'] = $checkboxDiscountValue;
                            $checkbox['discountvaluenotax'] = $checkboxDiscountValueNoTax;
                            $checkbox['discountvaluewithtax'] = $checkboxDiscountValueWithTax;

                            if ($gSession['order']['showpriceswithtax'] == 1)
                            {
                                $orderTotalNoTaxWithDiscount += $checkbox['totalsellwithtax']; //$checkboxDiscountValueWithTax;
                            }
                            else
                            {
                                $orderTotalNoTaxWithDiscount += $checkbox['totalsellnotax']; //$checkboxDiscountValueNoTax;
                            }
                        }
                        else
                        {
                            if ($gSession['order']['showpriceswithtax'] == 1)
                            {
                                $orderTotalNoTaxWithDiscount += $checkbox['totalsellwithtax'];
                            }
                            else
                            {
                                $orderTotalNoTaxWithDiscount += $checkbox['totalsellnotax'];
                            }
                        }
                    }

                    // add to the sub-totals
                    $itemTaxRateArray = self::getTaxRateArrayFromTaxLevel($checkbox['orderfootertaxlevel']);

                    self::updateOrderTaxBreakDownSummary($itemTaxRateArray['code'], $checkbox['totalsellnotax'], $checkbox['totaltax']);

                    //calculate orderFooterTaxTotal based on the components tax rate
                    if ($gSession['order']['showpriceswithtax'] == 0)
                    {
                        $taxVal = UtilsObj::bround($checkbox['subtotal'] * $checkbox['orderfootertaxrate'] / 100, $currencyDecimalPlaces);
                        $checkbox['totalsellwithtax'] = $checkbox['subtotal'] + $taxVal;
                        $orderFooterTaxTotal += $taxVal;
                    }
                    else
                    {
                        $taxCalc = 1 + ($checkbox['orderfootertaxrate'] / 100);
                        $checkbox['totalsellnotax'] = UtilsObj::bround($checkbox['subtotal'] / $taxCalc, $currencyDecimalPlaces);
                        $orderFooterTaxTotal += ($checkbox['subtotal'] - $checkbox['totalsellnotax']);
                    }

                    $orderFooterTotalCost += $checkbox['totalcost'];
                    $orderFooterTotalSell += $checkbox['subtotal'];
                    //$orderFooterTotalSell += $checkbox['discountedvalue'];
                    $orderFooterTotalSellNoTax += $checkbox['totalsellnotax'];
                    $orderFooterTotalSellWithTax += $checkbox['totalsellwithtax'];
                    $orderFooterTotalWeight += $checkbox['totalweight'];

                    // add component prices
                    $itemComponentTotalCost += $checkbox['totalcost'];
                    $itemComponentTotalSell += $checkbox['subtotal'];
                    $itemComponentTotalSellNoTax += $checkbox['totalsellnotax'];
                    $itemComponentTotalSellWithTax += $checkbox['totalsellwithtax'];
                    $itemComponentTotalWeight += $checkbox['totalweight'];
                }
            }

            $component['itemcomponenttotalcost'] = $itemComponentTotalCost;
            $component['itemcomponenttotalsell'] = $itemComponentTotalSell;
            $component['itemcomponenttotalsellnotax'] = $itemComponentTotalSellNoTax;
            $component['itemcomponenttotalsellwithtax'] = $itemComponentTotalSellWithTax;
            $component['itemcomponenttotalweight'] = $itemComponentTotalWeight;


        } // loop around sections

        // loop around order footer checkboxes
        foreach ($gSession['order']['orderFooterCheckboxes'] as &$checkbox)
        {
            $checkbox['discountvalue'] = 0.0;
            $checkbox['discountedvalue'] = 0.0;
            $checkbox['discountedtax'] = 0.0;
            $checkbox['discountvaluenotax'] = 0.0;
            $checkbox['discountvaluewithtax'] = 0.0;

            self::updateCheckboxTotal($checkbox, $checkbox['productcode'], $currencyExchangeRate, $currencyDecimalPlaces,
                    $checkbox['itemqty'], $checkbox['itempagecount'], -1);

            if ($checkbox['checked'])
            {
                $checkboxDiscountValue = 0;
                $checkboxDiscountValueNoTax = 0;
                $checkboxDiscountValueWithTax = 0;

                $checkbox['subtotal'] = $checkbox['totalsell'];

                $orderFooterTotalNoTaxNoDiscount += $checkbox['totalsellnotax'];
                $orderFooterTotalWithTaxNoDiscount += $checkbox['totalsellwithtax'];

                if (($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($gSession['order']['voucheractive'] == 1)
                        && (!in_array($gSession['order']['voucherdiscounttype'], $ignoreVoucherTypes)))
                {
                    if (($gSession['order']['orderalltaxratesequal'] == 0) || ($applyDiscountToSingleLineItem))
                    {
                        $checkboxDiscountValue = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                        $checkbox['totalsell'], $gSession['order']['voucherdiscountsection'],
                                        $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2);
                        $checkboxDiscountValueNoTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                        $checkbox['totalsellnotax'], $gSession['order']['voucherdiscountsection'],
                                        $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2);
                        $checkboxDiscountValueWithTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                        $checkbox['totalsellwithtax'], $gSession['order']['voucherdiscountsection'],
                                        $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2);

                        $checkbox['subtotal'] = $checkbox['totalsell'] - $checkboxDiscountValue;
                        $checkbox['totalsellnotax'] = $checkbox['totalsellnotax'] - $checkboxDiscountValueNoTax;
                        $checkbox['totalsellwithtax'] = $checkbox['totalsellwithtax'] - $checkboxDiscountValueWithTax;

                        if ($gSession['order']['voucherdiscounttype'] == 'VALUE' || $gSession['order']['voucherdiscounttype'] == 'BOGVOFF')
                        {
                            if ($gSession['order']['showpriceswithtax'] == 1)
                            {
                                $voucherDiscountValue2 = $voucherDiscountValue2 - $checkboxDiscountValueWithTax;
                            }
                            else
                            {
                                $voucherDiscountValue2 = $voucherDiscountValue2 - $checkboxDiscountValue;
                            }
                        }

                        if ($gSession['order']['showpriceswithtax'] == 1)
                        {
                            $checkboxDiscountValue = $checkboxDiscountValueWithTax;
                        }

                        $checkbox['discountvalue'] = $checkboxDiscountValue;
                        $checkbox['discountvaluenotax'] = $checkboxDiscountValueNoTax;
                        $checkbox['discountvaluewithtax'] = $checkboxDiscountValueWithTax;
                    }
                }
                elseif (($gSession['order']['voucherdiscounttype'] == 'VALUESET') && ($gSession['order']['voucherdiscountsection'] == 'TOTAL') && (($gSession['order']['orderalltaxratesequal'] == 0) || ($applyDiscountToSingleLineItem)))
                {
                    // calculate value set discount running total
                    //1. In a temp variable add order total with discount without tax and the line total without tax together.
                    if ($gSession['order']['showpriceswithtax'] == 1)
                    {

                        //$orderCalcTemp = $orderTotalNoTaxWithDiscount + $checkbox['totalsellwithtax'];
                        $orderCalcTemp = $orderEligableForDiscount + $checkbox['totalsellwithtax'];
                        $tempItemValue = $checkbox['totalsellwithtax'];
                    }
                    else
                    {
                        //$orderCalcTemp = $orderTotalNoTaxWithDiscount + $checkbox['totalsellnotax'];
                        $orderCalcTemp = $orderEligableForDiscount + $checkbox['totalsellnotax'];
                        $tempItemValue = $checkbox['totalsellnotax'];
                    }

                    if ($orderCalcTemp > $gSession['order']['voucherdiscountvalue'])
                    {

                        //2. If the temp variable > voucher value then we will need to apply the difference as discount for the line in the same way we are doing already.
                        //3. order total with discount without tax would then have the line total without tax with discount added to it.
                        $tempDiscountVal = ($orderCalcTemp - $gSession['order']['voucherdiscountvalue']);

                        $checkboxDiscountValue = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                        $checkbox['totalsell'], $gSession['order']['voucherdiscountsection'], 'VALUE', $tempDiscountVal);
                        $checkboxDiscountValueNoTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                        $checkbox['totalsellnotax'], $gSession['order']['voucherdiscountsection'], 'VALUE', $tempDiscountVal);
                        $checkboxDiscountValueWithTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                        $checkbox['totalsellwithtax'], $gSession['order']['voucherdiscountsection'], 'VALUE',
                                        $tempDiscountVal);

                        $checkbox['subtotal'] = $checkbox['totalsell'] - $checkboxDiscountValue;
                        $checkbox['totalsellnotax'] = $checkbox['totalsellnotax'] - $checkboxDiscountValueNoTax;
                        $checkbox['totalsellwithtax'] = $checkbox['totalsellwithtax'] - $checkboxDiscountValueWithTax;

                        $checkbox['discountvalue'] = $checkboxDiscountValue;
                        $checkbox['discountvaluenotax'] = $checkboxDiscountValueNoTax;
                        $checkbox['discountvaluewithtax'] = $checkboxDiscountValueWithTax;

                        if ($gSession['order']['showpriceswithtax'] == 1)
                        {
                            $orderTotalNoTaxWithDiscount += $checkbox['totalsellwithtax']; //$checkboxDiscountValueWithTax
                            $orderEligableForDiscount += $checkbox['totalsellwithtax'];
                        }
                        else
                        {
                            $orderTotalNoTaxWithDiscount += $checkbox['totalsellnotax']; //$checkboxDiscountValueNoTax
                            $orderEligableForDiscount += $checkbox['totalsellnotax'];
                        }
                    }
                    else
                    {
                        if ($gSession['order']['showpriceswithtax'] == 1)
                        {
                            $orderTotalNoTaxWithDiscount += $checkbox['totalsellwithtax'];
                            $orderEligableForDiscount += $checkbox['totalsellwithtax'];
                        }
                        else
                        {
                            $orderTotalNoTaxWithDiscount += $checkbox['totalsellnotax'];
                            $orderEligableForDiscount += $checkbox['totalsellnotax'];
                        }
                    }
                }

                // add to the sub-totals
                $itemTaxRateArray = self::getTaxRateArrayFromTaxLevel($checkbox['orderfootertaxlevel']);

                self::updateOrderTaxBreakDownSummary($itemTaxRateArray['code'], $checkbox['totalsellnotax'], $checkbox['totaltax']);

                //calculate orderFooterTaxTotal based on the components tax rate
                if ($gSession['order']['showpriceswithtax'] == 0)
                {
                    $taxVal = UtilsObj::bround($checkbox['subtotal'] * $checkbox['orderfootertaxrate'] / 100, $currencyDecimalPlaces);
                    $checkbox['totalsellwithtax'] = $checkbox['subtotal'] + $taxVal;
                    $orderFooterTaxTotal += $taxVal;
                }
                else
                {
                    $taxCalc = 1 + ($checkbox['orderfootertaxrate'] / 100);
                    $checkbox['totalsellnotax'] = UtilsObj::bround($checkbox['subtotal'] / $taxCalc, $currencyDecimalPlaces);
                    $orderFooterTaxTotal += ($checkbox['subtotal'] - $checkbox['totalsellnotax']);
                }

                $orderFooterTotalCost += $checkbox['totalcost'];
                $orderFooterTotalSell += $checkbox['subtotal'];
                //$orderFooterTotalSell += $checkbox['discountedvalue'];
                $orderFooterTotalSellNoTax += $checkbox['totalsellnotax'];
                $orderFooterTotalSellWithTax += $checkbox['totalsellwithtax'];
                $orderFooterTotalWeight += $checkbox['totalweight'];
            }
        }


        // add the order footer components to the total order weight
        $orderTotalWeight += $orderFooterTotalWeight;
        $orderFooterSubTotal = $orderFooterTotalSell;
        $orderFooterTotalSellNoTaxNoDiscount = $orderFooterTotalSellNoTax;
        $orderFooterTotalSellWithTaxNoDiscount = $orderFooterTotalSellWithTax;
        $orderTotalForDiscount += $orderFooterTotalSell;

        // determine if we need to apply a voucher to the order footer items
        if (($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($gSession['order']['voucheractive'] == 1)
                && (!in_array($gSession['order']['voucherdiscounttype'], $ignoreVoucherTypes)))
        {
            if (($gSession['order']['voucherdiscounttype'] != 'PERCENT'))
            {
                if (($gSession['order']['orderalltaxratesequal'] == 0) || ($applyDiscountToSingleLineItem))
                {
                    $orderFooterDiscountValue = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                    $orderFooterTotalSell, $gSession['order']['voucherdiscountsection'],
                                    $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2);
                    $orderFooterDiscountValueNoTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                    $orderFooterTotalSellNoTax, $gSession['order']['voucherdiscountsection'],
                                    $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2);
                    $orderFooterDiscountValueWithTax = self::calcDiscountedValue($itemQty, $gSession['order']['voucherminqty'],
                                    $orderFooterTotalSellWithTax, $gSession['order']['voucherdiscountsection'],
                                    $gSession['order']['voucherdiscounttype'], $voucherDiscountValue2);

                    $orderFooterTotalSell = $orderFooterTotalSell - $orderFooterDiscountValue;
                    $orderFooterTotalSellNoTax = $orderFooterTotalSellNoTax - $orderFooterDiscountValueNoTax;
                    $orderFooterTotalSellWithTax = $orderFooterTotalSellWithTax - $orderFooterDiscountValueWithTax;
                    $orderFooterTaxTotal = $orderFooterTotalSellWithTax - $orderFooterTotalSellNoTax;

                    if ($gSession['order']['voucherdiscounttype'] == 'VALUE' || $gSession['order']['voucherdiscounttype'] == 'BOGVOFF')
                    {
                        if ($gSession['order']['showpriceswithtax'] == 1)
                        {
                            $voucherDiscountValue2 = $voucherDiscountValue2 - $orderFooterDiscountValueWithTax;
                        }
                        else
                        {
                            $voucherDiscountValue2 = $voucherDiscountValue2 - $orderFooterDiscountValue;
                        }
                    }
                }
            }
        }

        if ($gSession['order']['showpriceswithtax'] == 1)
        {
            $orderTotalBeforeDiscount += $orderFooterTotalSellWithTaxNoDiscount;
        }
        else
        {
            $orderTotalBeforeDiscount += $orderFooterTotalSellNoTaxNoDiscount;
        }
        $orderTotal += $orderFooterTotalSell;
        $orderTotalCost += $orderFooterTotalCost;
        $orderTotalSell += $orderFooterTotalSell;
        $orderTotalTax += $orderFooterTaxTotal;
        $orderTotalItemCost += $orderFooterTotalCost;
        $orderTotalItemSell += $orderFooterTotalSell;
        $orderTotalItemSellNoTax += $orderFooterTotalSellNoTax;
        $orderTotalItemSellWithTax += $orderFooterTotalSellWithTax;
        $orderTotalItemTax += $orderFooterTaxTotal;

        // order item totals need to be updated to include the orderFooter totals
        $orderTotalItemSellNoTaxNoDiscount += $orderFooterTotalSellNoTax;
        $orderTotalItemSellWithTaxNoDiscount += $orderFooterTotalSellWithTax;

        if (! $shippingfromscript)
        {
            // apply shipping discount
            // if the voucher section is shipping or total and the shipping & item tax rates are different we also calculate it here
            if ($gSession['shipping'][0]['shippingmethodcode'] != '')
            {
                $differentTaxRates = ($gSession['order']['orderalltaxratesequal'] == 0);
                $voucherActiveAndApplied = (($gSession['order']['voucheractive'] == 1) && ($gSession['items'][$currentLine]['itemvoucherapplied'] == 1));
                $discountForShipping = ($gSession['order']['voucherdiscountsection'] == 'SHIPPING');
                $discountForTotal = (($gSession['order']['voucherdiscountsection'] == 'TOTAL') && (!in_array($gSession['order']['voucherdiscounttype'], $ignoreVoucherTypes)));

                if (($discountForShipping || ($discountForTotal && ($differentTaxRates || $applyDiscountToSingleLineItem))) && ($gSession['order']['voucheractive'] == 1))
                {
                    // calculate the discount for the shipping if the discount is to be displayed
                    $shippingDiscountValue = self::calcDiscountedValue($gSession['items'][0]['itemqty'],
                                    $gSession['order']['voucherminqty'], $gSession['shipping'][0]['shippingratesell'],
                                    $gSession['order']['voucherdiscountsection'], $gSession['order']['voucherdiscounttype'],
                                    $voucherDiscountValue2);
                    $shippingDiscountValueNoTax = self::calcDiscountedValue($gSession['items'][0]['itemqty'],
                                    $gSession['order']['voucherminqty'], $gSession['shipping'][0]['shippingratesellnotax'],
                                    $gSession['order']['voucherdiscountsection'], $gSession['order']['voucherdiscounttype'],
                                    $voucherDiscountValue2);
                    $shippingDiscountValueWithTax = self::calcDiscountedValue($gSession['items'][0]['itemqty'],
                                    $gSession['order']['voucherminqty'], $gSession['shipping'][0]['shippingratesellwithtax'],
                                    $gSession['order']['voucherdiscountsection'], $gSession['order']['voucherdiscounttype'],
                                    $voucherDiscountValue2);

                    $gSession['shipping'][0]['shippingratediscountvalue'] = $shippingDiscountValue;
                    $gSession['shipping'][0]['shippingratetotalsell'] = $gSession['shipping'][0]['shippingratesell'] - $shippingDiscountValue;
                    $gSession['shipping'][0]['shippingratetotalsellnotax'] = $gSession['shipping'][0]['shippingratesellnotax'] - $shippingDiscountValueNoTax;
                    $gSession['shipping'][0]['shippingratetotalsellwithtax'] = $gSession['shipping'][0]['shippingratesellwithtax'] - $shippingDiscountValueWithTax;
                }
                elseif (($gSession['order']['voucherdiscounttype'] == 'VALUESET') && ($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($differentTaxRates || $applyDiscountToSingleLineItem))
                {
                    // calculate value set discount running total
                    if ($gSession['order']['showpriceswithtax'] == 1)
                    {
                        $orderCalcTemp = $orderEligableForDiscount + $gSession['shipping'][0]['shippingratesellwithtax'];
                        //$orderCalcTemp = $orderTotalNoTaxWithDiscount + $gSession['shipping'][0]['shippingratesellwithtax'];
                    }
                    else
                    {
                        $orderCalcTemp = $orderEligableForDiscount + $gSession['shipping'][0]['shippingratesellnotax'];
                        //$orderCalcTemp = $orderTotalNoTaxWithDiscount + $gSession['shipping'][0]['shippingratesellnotax'];
                    }

                    if ($orderCalcTemp > $gSession['order']['voucherdiscountvalue'])
                    {
                        $tempDiscountVal = ($orderCalcTemp - $gSession['order']['voucherdiscountvalue']);

                        $shippingDiscountValue = self::calcDiscountedValue($gSession['items'][0]['itemqty'],
                                        $gSession['order']['voucherminqty'], $gSession['shipping'][0]['shippingratesell'],
                                        $gSession['order']['voucherdiscountsection'], 'VALUE', $tempDiscountVal);
                        $shippingDiscountValueNoTax = self::calcDiscountedValue($gSession['items'][0]['itemqty'],
                                        $gSession['order']['voucherminqty'], $gSession['shipping'][0]['shippingratesellnotax'],
                                        $gSession['order']['voucherdiscountsection'], 'VALUE', $tempDiscountVal);
                        $shippingDiscountValueWithTax = self::calcDiscountedValue($gSession['items'][0]['itemqty'],
                                        $gSession['order']['voucherminqty'], $gSession['shipping'][0]['shippingratesellwithtax'],
                                        $gSession['order']['voucherdiscountsection'], 'VALUE', $tempDiscountVal);

                        $gSession['shipping'][0]['shippingratediscountvalue'] = $shippingDiscountValue;
                        $gSession['shipping'][0]['shippingratetotalsell'] = $gSession['shipping'][0]['shippingratesell'] - $shippingDiscountValue;
                        $gSession['shipping'][0]['shippingratetotalsellnotax'] = $gSession['shipping'][0]['shippingratesellnotax'] - $shippingDiscountValueNoTax;
                        $gSession['shipping'][0]['shippingratetotalsellwithtax'] = $gSession['shipping'][0]['shippingratesellwithtax'] - $shippingDiscountValueWithTax;

                        if ($gSession['order']['showpriceswithtax'] == 1)
                        {
                            $orderTotalNoTaxWithDiscount += $shippingDiscountValueWithTax;
                        }
                        else
                        {
                            $orderTotalNoTaxWithDiscount += $shippingDiscountValueNoTax;
                        }
                    }
                    else
                    {
                        if ($gSession['order']['showpriceswithtax'] == 1)
                        {
                            $orderTotalNoTaxWithDiscount += $gSession['shipping'][0]['shippingratesellwithtax'];
                        }
                        else
                        {
                            $orderTotalNoTaxWithDiscount += $gSession['shipping'][0]['shippingratesellnotax'];
                        }
                    }
                }
                else
                {
                    $gSession['shipping'][0]['shippingratediscountvalue'] = 0.00;
                    $gSession['shipping'][0]['shippingratetotalsell'] = $gSession['shipping'][0]['shippingratesell'];
                    $gSession['shipping'][0]['shippingratetotalsellnotax'] = $gSession['shipping'][0]['shippingratesellnotax'];
                    $gSession['shipping'][0]['shippingratetotalsellwithtax'] = $gSession['shipping'][0]['shippingratesellwithtax'];
                }
            }
            else
            {
                $gSession['shipping'][0]['shippingratediscountvalue'] = 0.00;
                $gSession['shipping'][0]['shippingratetotalsell'] = $gSession['shipping'][0]['shippingratesell'];
                $gSession['shipping'][0]['shippingratetotalsellnotax'] = $gSession['shipping'][0]['shippingratesellnotax'];
                $gSession['shipping'][0]['shippingratetotalsellwithtax'] = $gSession['shipping'][0]['shippingratesellwithtax'];
            }
        }

        // calculate the shipping tax
        if ($gSession['order']['orderalltaxratesequal'] == 1)
        {
            $calcShippingTax = 0;
            if ($gSession['order']['showpriceswithtax'] == 0)
            {
                // the tax rates are the same and we are not showing the prices with tax
                // in this situation we sum the items & shipping rate and re-calculate the total tax as well as the shipping tax
                $shippingWithoutTax = $gSession['shipping'][0]['shippingratetotalsell'];
                $shippingTaxTotal = UtilsObj::bround($shippingWithoutTax * $gSession['items'][0]['itemtaxrate'] / 100,
                                $currencyDecimalPlaces);
            }
            else
            {
                // the tax rates are the same but we are showing prices with tax
                // in this situation we need to calculate the amount of shipping tax and add it to the order total tax
                $taxCalc = 1 + ($gSession['items'][0]['itemtaxrate'] / 100);
                $shippingWithoutTax = UtilsObj::bround($gSession['shipping'][0]['shippingratetotalsell'] / $taxCalc, $currencyDecimalPlaces);
                $shippingTaxTotal = UtilsObj::bround($gSession['shipping'][0]['shippingratetotalsell'] - $shippingWithoutTax,
                                $currencyDecimalPlaces);
            }

            $orderTotalTax += $shippingTaxTotal;
            $gSession['shipping'][0]['shippingratetotalsellwithtax'] = $shippingWithoutTax + $shippingTaxTotal;
        }
        else
        {
            $calcShippingTax = 1;
            if ($gSession['order']['showpriceswithtax'] == 0)
            {
                // the tax rates are different and we are not showing the prices with tax
                // in this situation we calculate the shipping tax and add it to the total tax
                $shippingWithoutTax = $gSession['shipping'][0]['shippingratetotalsell'];
                $shippingTaxTotal = UtilsObj::bround($shippingWithoutTax * $gSession['shipping'][0]['shippingratetaxrate'] / 100,
                                $currencyDecimalPlaces);
            }
            else
            {
                // the tax rates are different but we are showing prices with tax
                // in this situation we need to calculate the amount of shipping tax and add it to the order total tax
                $taxCalc = 1 + ($gSession['shipping'][0]['shippingratetaxrate'] / 100);
                $shippingWithoutTax = UtilsObj::bround($gSession['shipping'][0]['shippingratetotalsell'] / $taxCalc, $currencyDecimalPlaces);
                $shippingTaxTotal = UtilsObj::bround($gSession['shipping'][0]['shippingratetotalsell'] - $shippingWithoutTax,
                                $currencyDecimalPlaces);
            }

            $orderTotalTax += $shippingTaxTotal;
            $gSession['shipping'][0]['shippingratetotalsellwithtax'] = $shippingWithoutTax + $shippingTaxTotal;
        }

        self::updateOrderTaxBreakDownSummary($gSession['shipping'][0]['shippingratetaxcode'], $shippingWithoutTax, $shippingTaxTotal);

        $orderTotal += $gSession['shipping'][0]['shippingratetotalsell'];
        $orderTotalForDiscount += $gSession['shipping'][0]['shippingratetotalsell'];

        $orderTotalBeforeDiscount += $gSession['shipping'][0]['shippingratesell'];
        $gSession['shipping'][0]['shippingratecalctax'] = $calcShippingTax;
        $gSession['shipping'][0]['shippingratetaxtotal'] = $shippingTaxTotal;

        // add the tax at this point if necessary
        if ($gSession['order']['showpriceswithtax'] == 1)
        {
            $orderTotalItemSell = $orderTotalItemSellWithTax;
        }
        else
        {
            $orderTotalItemSellWithTax = $orderTotalItemSellNoTax + $orderTotalItemTax;
        }

        // round to 4 dp to remove any minor floating point errors during the calculation
        $orderTotalTax = UtilsObj::bround($orderTotalTax, 4);

        $gSession['order']['ordertotalitemdiscountable'] = $orderQtyCount;
        $gSession['order']['ordertotalitemcost'] = $orderTotalItemCost;
        $gSession['order']['ordertotalitemsell'] = $orderTotalItemSell;
        $gSession['order']['ordertotalitemsellnotax'] = $orderTotalItemSellNoTax;
        $gSession['order']['ordertotalitemsellwithtax'] = $orderTotalItemSellWithTax;

        $gSession['order']['ordertotalitemsellnotaxnodiscount'] = $orderTotalItemSellNoTaxNoDiscount;
        $gSession['order']['ordertotalitemsellwithtaxnodiscount'] = $orderTotalItemSellWithTaxNoDiscount;

        $gSession['order']['ordertotalitemsellnotaxalldiscounted'] = $orderTotalItemSellNoTaxAllDiscounted + $orderFooterTotalSellNoTax;
        $gSession['order']['ordertotalitemsellwithtaxalldiscounted'] = $orderTotalItemSellWithTaxAllDiscounted + $orderFooterTotalSellWithTax;

        $gSession['order']['ordertotalitemtax'] = $orderTotalItemTax;
        $gSession['order']['ordertotalshippingcost'] = $gSession['shipping'][0]['shippingratecost'];
        $gSession['order']['ordertotalshippingsellbeforediscount'] = $gSession['shipping'][0]['shippingratesell'];
        $gSession['order']['ordertotalshippingsellafterdiscount'] = $gSession['shipping'][0]['shippingratetotalsell'];
        $gSession['order']['ordertotalshippingtax'] = $shippingTaxTotal;
        $gSession['order']['ordertotalshippingweight'] = $orderTotalWeight;
        $gSession['order']['ordertotalcost'] = $itemTotalCost + $gSession['shipping'][0]['shippingratecost'];
        $orderTotalSell += $gSession['shipping'][0]['shippingratetotalsell'];

        $gSession['order']['ordertotalsellbeforediscount'] = $orderTotalSell;
        $gSession['order']['ordertotaltaxbeforediscount'] = $orderTotalTax;
        $gSession['order']['ordertotalbeforediscount'] = $orderTotalBeforeDiscount;

        $gSession['order']['orderfootersubtotal'] = $orderFooterSubTotal;
        $gSession['order']['orderfootertotalnotax'] = $orderFooterTotalSellNoTax;
        $gSession['order']['orderfootertotalwithtax'] = $orderFooterTotalSellWithTax;
        $gSession['order']['orderfootertotal'] = $orderFooterTotalSellWithTax;
        $gSession['order']['orderfootertotaltax'] = $orderFooterTaxTotal;
        $gSession['order']['orderfooterdiscountvalue'] = $orderFooterTotalSell;

        $gSession['order']['orderfootertotalnotaxnodiscount'] = $orderFooterTotalNoTaxNoDiscount;
        $gSession['order']['orderfootertotalwithtaxnodiscount'] = $orderFooterTotalWithTaxNoDiscount;

        // if the voucher is applied to the total
        if (!$applyDiscountToSingleLineItem && (($gSession['order']['voucherdiscountsection'] == 'TOTAL') && ($gSession['order']['voucheractive'] == 1)))
        {
            // If the tax rates are the same
            if ($gSession['order']['orderalltaxratesequal'] == 1)
            {
                if (!$ordertotaldiscountfromscript)
                {
                    $orderDiscountValue = self::calcDiscountedValue(0, 0, $orderTotalForDiscount,
                                    $gSession['order']['voucherdiscountsection'], $gSession['order']['voucherdiscounttype'],
                                    self::applyExchangeRateToVoucher($gSession['order']['voucherdiscounttype'], $gSession['order']['voucherdiscountvalue'], $gSession['order']['currencyexchangerate']));
                }
                else
                {
                    if ($orderTotal - $orderDiscountValue < 0)
                    {
                        $orderDiscountValue = $orderTotal;
                    }
                }

                // round to 4 dp to remove any minor floating point errors during the calculation
                $orderTotal = UtilsObj::bround($orderTotal - $orderDiscountValue, 4);
                $orderTotalSell = $orderTotal;

                // we can use the tax rate from $gSession['items'][0] as the tax rates are the same for all line items and shipping
                if ($gSession['order']['showpriceswithtax'] == 0)
                {
                    $orderTaxCalc = 1 + ($gSession['items'][0]['itemtaxrate'] / 100);
                    $orderTotalWithTax = UtilsObj::bround($orderTotal * $orderTaxCalc, $currencyDecimalPlaces);
                    $orderTotalTax = $orderTotalWithTax - $orderTotal;
                }
                else
                {
                    $orderTaxCalc = 1.0 / (1 + ($gSession['items'][0]['itemtaxrate'] / 100));
                    $orderTotalWithoutTax = UtilsObj::bround($orderTotal * $orderTaxCalc, $currencyDecimalPlaces);
                    $orderTotalTax = $orderTotal - $orderTotalWithoutTax;
                }
            }
            else
            {
                switch ($gSession['order']['voucherdiscounttype'])
                {
                    case 'VALUESET':
					{
						$orderDiscountValue = $orderTotalForDiscount - $orderDiscountValue;

						break;
					}
                    default:
					{
						break;
					}
                }
            }
        }

        // if we are not showing the prices inclusive of tax add it to the final total
        if ($gSession['order']['showpriceswithtax'] == 0)
        {
            $orderTotal = $orderTotal + $orderTotalTax;
        }

        // check for a gift card balance
        self::updateCreditStatus();

        // round to 4 dp to remove any minor floating point errors during the calculation
        $orderTotalTax = UtilsObj::bround($orderTotalTax, 4);

        $gSession['order']['ordertotaldiscount'] = $orderDiscountValue;
        $gSession['order']['ordertotalsell'] = $orderTotalSell;
        $gSession['order']['ordertotaltax'] = $orderTotalTax;
        $gSession['order']['ordertotal'] = $orderTotal;
        $gSession['order']['ordergiftcardtotal'] = $gSession['usergiftcardbalance'];

        if ($gSession['order']['ordergiftcardtotal'] > $gSession['order']['ordertotal'])
        {
            $gSession['order']['ordergiftcardtotal'] = $gSession['order']['ordertotal'];
        }

        if ($gSession['ordergiftcarddeleted'])
		{
			$gSession['order']['ordertotaltopay'] = $gSession['order']['ordertotal'];
		}
        else
		{
			$gSession['order']['ordertotaltopay'] = $gSession['order']['ordertotal'] - $gSession['order']['ordergiftcardtotal'];
		}

        if ($gSession['order']['ordergiftcardtotal'] >= 0)
        {
            DatabaseObj::setSessionGiftCardTotal($gSession['ref'], $gSession['order']['ordergiftcardtotal']);
        }
    }

    static function updateOrderTaxBreakDownSummary($pTaxRateCode, $pNetTotal, $pTaxTotal)
    {
        global $gSession;

        foreach ($gSession['order']['ordertaxbreakdown'] as $orderSummaryIndex => &$value)
        {
            if ($pTaxRateCode == $value['taxratecode'])
            {
                $value['nettotal'] += $pNetTotal;
                $value['taxtotal'] += $pTaxTotal;
                break;
            }
        }
    }

    static function cancel()
    {
        global $gSession;

        $redirectionURL = '';

        // write the order status cache file to state that the order process has been cancelled
        self::writeOrderStatusCacheFile('CANCEL', true);

        // if the session has been revived then we just disable it rather than remove it
        if ($gSession['sessionrevived'] == 1)
        {
            if ($gSession['order']['temporder'] == 1)
            {
                $gSession['order']['paymentmethodcode'] = 'PAYLATER';
                DatabaseObj::updateSession();
            }

            DatabaseObj::disableSession($gSession['ref'], 0);
        }
        else
        {
            AuthenticateObj::endWebSession();
            DatabaseObj::deleteSession($gSession['ref']);
        }

        if ($gSession['order']['basketapiworkflowtype'] == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
		{
			// read the config file
			$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $gSession['webbrandcode']);
			$brandDataArray = DatabaseObj::getBrandingFromCode($gSession['webbrandcode']);

			if ($brandDataArray['onlinedesignerlogouturl'] != '')
			{
				$redirectionURL = $brandDataArray['onlinedesignerlogouturl'];
			}
			else
			{
				$redirectionURL =$hl_config['REDIRECTIONURL'];
			}

			$redirectionURL = UtilsObj::correctPath($redirectionURL, '/', true);
		}

        //Delete the cache file
        self::deleteOnlineOrderStatusCacheFile('');

        return $redirectionURL;
    }

    static function complete()
    {
        // include the order routing, data export and email creation modules
        require_once('../Utils/UtilsRoute.php');
        require_once('../Utils/UtilsDataExport.php');
        require_once('../Utils/UtilsEmail.php');

        global $ac_config;
        global $gConstants;
        global $gSession;

        $result = '';
        $resultParam = '';
        $status = TPX_ITEM_STATUS_AWAITING_FILES;
        $paymentGatewayCode = '';
        $orderDate = DatabaseObj::getServerTime();
        $wasTempOrder = 0;
        $isTempOrder = 0;
        $tempOrderExpiryDate = '';
        $requiresUpload = 0;
        $siteResultArray = Array();
        $storeArray = Array();
        $siteCode = '';
        $siteCompanyCode = '';
        $calcOrderRouting = true;
        $ownerOrderKey = '';
        $ownerOrderKeyInitialize = '';
        $productionSiteType = 0;
        $storeCode = '';
        $storeOpeningTimes = '';
        $storeURL = '';
        $storeEmailAddress = '';
        $storeTelephoneNumber = '';
        $storeContactName = '';
        $storeOnline = 0;
        $origItemStatus = TPX_ITEM_STATUS_AWAITING_FILES;
        $origPreviewsOnline = false;
        $origProjectBuildStartDate = '';
        $origProjectBuildDuration = 0;
        $origUploadDataType = TPX_UPLOAD_DATA_TYPE_RENDERED;
        $origUploadMethod = 0;
        $origUploadAppVersion = '';
        $origUploadAppPlatform = '';
        $origUploadAppCPUType = '';
        $origUploadAppOSVersion = '';
        $origUploadDataSize = '';
		$origUploadDuration = 0;
		$pricingEngineVersion = TPX_PRICINGENGINE_2018;

        $resultArray = array();

        if ($gSession['ref'] > 0)
        {
			// Acquire a lock and deal with processing.
			$mutexName = 'taopix_order_confirm_' . $gSession['ref'];
			$confirmMutex = DatabaseObj::acquireDBMutex($mutexName);

			/*
			 * Only continue to process if we have acquired a mutex, if the operation
			 * doesn't get the mutex wait and retrigger the call.
			 */
			if ($confirmMutex['result'] == true)
			{
				$sessionData = DatabaseObj::getSessionData($gSession['ref']);
				$gSession['order']['processed'] = $sessionData['order']['processed'];
				if ($gSession['order']['processed'] == 0)
				{
					$dbObj = DatabaseObj::getConnection();
					if ($dbObj)
					{
						$gSession['order']['processed'] = 1;
						$gSession['order']['currentstage'] = 'complete';

						// if the order total uses the gift card to cover full amount, set payment method to 'NONE'
						if (($gSession['order']['ordertotaltopay'] == 0) && ($gSession['order']['ordergiftcardtotal'] > 0))
						{
							$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
							$gSession['order']['paymentmethodcode'] = 'NONE';
							$gSession['order']['paymentmethodname'] = $smarty->get_config_vars('str_LanguageCode') . ' ' . $smarty->get_config_vars('str_LabelNone');
						}

						// update the session now as we want to record the fact that the order session has been processed
						DatabaseObj::updateSession();

						// handle temporary (paylater) orders
						if ($gSession['order']['paymentmethodcode'] == 'PAYLATER')
						{
							// remove the voucher code as we do not want it in the saved session
							$gSession['order']['voucheractive'] = 0;
							$gSession['order']['voucherpromotioncode'] = '';
							$gSession['order']['voucherpromotionname'] = '';
							$gSession['order']['vouchercode'] = '';
							$gSession['order']['vouchertype'] = 0;
							$gSession['order']['vouchername'] = '';
							$gSession['order']['vouchersellprice'] = 0.00;
							$gSession['order']['voucheragentfee'] = 0.00;
							$gSession['order']['defaultdiscountactive'] = true;
							$gSession['order']['voucherapplicationmethod'] = TPX_VOUCHER_APPLY_EACH_MATCHING_PRODUCT;
							$gSession['order']['voucherapplytoqty'] = 9999;
							$gSession['order']['voucherstatus'] = '';
							$gSession['order']['voucherdiscounttype'] = '';
							$gSession['order']['voucherdiscountvalue'] = 0.00;


							// retrieve the expiry date based on the pay later expiry number of days
							$expiryTime = (int) UtilsObj::getArrayParam($ac_config, 'PAYLATEREXPIRYDAYS', 30);
							$tempOrderExpiryDate = DatabaseObj::getServerTime($expiryTime * 60 * 24);
							$isTempOrder = 1;
						}

						// remember if this was originally a temp order
						$wasTempOrder = $gSession['order']['temporder'];

						// pre-configure the email notification for later
						$emailObj = new TaopixMailer();

						$shippingAddress = UtilsAddressObj::formatAddress($gSession['shipping'][0], 'shipping', "\n");
						$billingAddress = UtilsAddressObj::formatAddress($gSession['order'], 'billing', "\n");

						$webBrandEmailSettingsArray = DatabaseObj::getBrandingFromCode($gSession['webbrandcode']);
						$brandingDefaults = DatabaseObj::getBrandingFromCode('');

						// check if confirmation email should be sent
						if (($gSession['webbrandcode'] != '') && ($webBrandEmailSettingsArray['usedefaultemailsettings'] == 0))
						{
							$sendNotification = $webBrandEmailSettingsArray['smtporderconfirmationactive'];
						}
						else
						{
							$sendNotification = $brandingDefaults['smtporderconfirmationactive'];
						}

						// if this is a temp order that has not been assigned to a user (offline order) then we do not send an email notification to the user
						if (($isTempOrder == 1) && ($gSession['userid'] == 0))
						{
							$sendNotification = false;
						}

						// if this is an offline order that was not completed by the customer then we do not send an email notification to the user
						if (($gSession['order']['isofflineorder'] == 1) && ($gSession['order']['isofflineordercompletedbycustomer'] == 0))
						{
							$sendNotification = false;
						}

						// if this is an offline order set the upload method to mail otherwise set the upload method to internet as this will be set when the files are received
						if ($gSession['order']['isofflineorder'] == 1)
						{
							$uploadMethod = TPX_UPLOAD_DELIVERY_METHOD_MAIL;
						}
						else
						{
							$uploadMethod = TPX_UPLOAD_DELIVERY_METHOD_INTERNET;
						}

						// set the payment received information
						switch ($gSession['order']['paymentmethodcode'])
						{
							case 'CARD': // go by ccilog
                            case 'PAYPAL': // ditto
                            case 'KLARNA': // ditto
								if ($gSession['order']['ccipaymentreceived'] == 1)
								{ // payment received
									$paymentReceived = 1;
									$paymentReceivedTimestamp = $gSession['order']['ccipaymentreceiveddatetime'];
									$paymentReceivedDate = $orderDate;
									$paymentReceivedUserId = -1;
								}
								else
								{ // no payment
									$paymentReceived = 0;
									$paymentReceivedTimestamp = '';
									$paymentReceivedDate = '';
									$paymentReceivedUserId = 0;
								}
								break;
							case 'ACCOUNT': // counts as paid
							case 'NONE':    // ditto
								$paymentReceived = 1;
								$paymentReceivedTimestamp = $orderDate;
								$paymentReceivedDate = $orderDate;
								$paymentReceivedUserId = -1;
								break;
							default:
								$paymentReceived = 0;
								$paymentReceivedTimestamp = '';
								$paymentReceivedDate = '';
								$paymentReceivedUserId = 0;
						}

						$voucherPromotionCode = '';
						$voucherPromotionName = '';
						$voucherCode = '';
						$voucherType = '';
						$voucherName = '';
						$voucherDiscountSection = '';
						$voucherDiscountType = '';
						$voucherDiscountValue = 0.00;
						$voucherSellPrice = 0.00;
						$voucherAgentFee = 0.00;
						$voucherApplicationMethod = TPX_VOUCHER_APPLY_EACH_MATCHING_PRODUCT;
						$voucherApplyToQty = 9999;

						if ($gSession['order']['voucheractive'] == 1)
						{
							$voucherPromotionCode = $gSession['order']['voucherpromotioncode'];
							$voucherPromotionName = $gSession['order']['voucherpromotionname'];
							$voucherCode = $gSession['order']['vouchercode'];
							$voucherType = $gSession['order']['vouchertype'];
							$voucherName = $gSession['order']['vouchername'];
							$voucherDiscountSection = $gSession['order']['voucherdiscountsection'];
							$voucherDiscountType = $gSession['order']['voucherdiscounttype'];
							$voucherDiscountValue = $gSession['order']['voucherdiscountvalue'];
							$voucherSellPrice = $gSession['order']['vouchersellprice'];
							$voucherAgentFee = $gSession['order']['voucheragentfee'];
							$voucherApplicationMethod = $gSession['order']['voucherapplicationmethod'];
							$voucherApplyToQty = $gSession['order']['voucherapplytoqty'];
						}

						$shippingTotalSell = $gSession['order']['ordertotalshippingsellafterdiscount'];
						$totalSellBeforeDiscount = $gSession['order']['ordertotalsellbeforediscount'];
						$totalBeforeDiscount = $gSession['order']['ordertotalbeforediscount'];
						$totalSell = $gSession['order']['ordertotalsell'];

						// if multi-site is enabled first determine if the order should be routed to a single production site
						$theShippingItem = $gSession['shipping'][0];

						if ($gConstants['optionms'])
						{
							// if this is an offline order or an order where the upload method was mail we route to the site creating the order
							if ($gSession['order']['offlineordersitecode'] != '')
							{
								$siteCode = $gSession['order']['offlineordersitecode'];
								$siteArray = DatabaseObj::getSiteFromCode($siteCode);

								if ($siteArray['isactive'] == 0)
								{
									$siteCode = '';
									$siteArray = DatabaseObj::getSiteFromCode($siteCode);
								}

								$siteCompanyCode = DatabaseObj::getCompanyCodeFromSiteCode($siteCode);
								$ownerOrderKeyInitialize = 'UNALLOCATED';
								$ownerOrderKey = $siteArray['productionsitekey'];
								$productionSiteType = $siteArray['productionsitetype'];

								$calcOrderRouting = false;
							}
						}
						else
						{
							// do not attempt to route the order
							$calcOrderRouting = false;
						}


						// perform collect from store
						if ($gSession['shipping'][0]['collectfromstore'])
						{
							// swap shipping address for store address if collect from store
							$shippingMethodCode = $gSession['shipping'][0]['shippingmethodcode'];
							$storeAddress = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress'];

							$gSession['shipping'][0]['shippingcustomername'] = $storeAddress['storecustomername'];
							$gSession['shipping'][0]['shippingcustomeraddress1'] = $storeAddress['storecustomeraddress1'];
							$gSession['shipping'][0]['shippingcustomeraddress2'] = $storeAddress['storecustomeraddress2'];
							$gSession['shipping'][0]['shippingcustomeraddress3'] = $storeAddress['storecustomeraddress3'];
							$gSession['shipping'][0]['shippingcustomeraddress4'] = $storeAddress['storecustomeraddress4'];
							$gSession['shipping'][0]['shippingcustomercity'] = $storeAddress['storecustomercity'];
							$gSession['shipping'][0]['shippingcustomercounty'] = $storeAddress['storecustomercounty'];
							$gSession['shipping'][0]['shippingcustomerstate'] = $storeAddress['storecustomerstate'];
							$gSession['shipping'][0]['shippingcustomerregioncode'] = $storeAddress['storecustomerregioncode'];
							$gSession['shipping'][0]['shippingcustomerregion'] = $storeAddress['storecustomerregion'];
							$gSession['shipping'][0]['shippingcustomerpostcode'] = $storeAddress['storecustomerpostcode'];
							$gSession['shipping'][0]['shippingcustomercountrycode'] = $storeAddress['storecustomercountrycode'];
							$gSession['shipping'][0]['shippingcustomercountryname'] = $storeAddress['storecustomercountryname'];
							$gSession['shipping'][0]['shippingcustomertelephonenumber'] = $storeAddress['storecustomertelephonenumber'];
							$gSession['shipping'][0]['shippingcustomeremailaddress'] = $storeAddress['storecustomeremailaddress'];
							$gSession['shipping'][0]['shippingcontactfirstname'] = $storeAddress['storecontactfirstname'];
							$gSession['shipping'][0]['shippingcontactlastname'] = $storeAddress['storecontactlastname'];

							$shippingAddress = UtilsAddressObj::formatAddress($gSession['shipping'][0], 'shipping', "\n");

							if (($gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['useScript'] == 0) ||
									($gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['externalstore'] == 0))
							{
								$storeArray = DatabaseObj::getSite(-1, $gSession['shipping'][0]['storeid']);
								$storeCode = $storeArray['code'];
								$storeOpeningTimes = $storeArray['openingtimes'];
								$storeURL = $storeArray['storeurl'];
								$storeEmailAddress = $storeArray['emailaddress'];
								$storeTelephoneNumber = $storeArray['telephonenumber'];
								$storeContactName = $storeArray['contactfirstname'] . ' ' . $storeArray['contactlastname'];
								$storeOnline = $storeArray['siteonline'];
							}
							else
							{
								// if external store, add it to the DB or update details
								$siteType = TPX_SITE_TYPE_STORE;
								$dbCode = $gSession['shipping'][0]['storeid'];
								$storeUrl = '';
								$storeOpeningTimes = '';

                                //we don't want to include any user contact details
                                $shippingcustomertelephonenumber = '';
                                $shippingcustomeremailaddress = '';
                                $shippingcontactfirstname = '';
                                $shippingcontactlastname = '';

								// might already be in the DB
								$siteArray = DatabaseObj::getSiteFromCode($dbCode);

								if ($siteArray['recordid'] == 0)
								{
									// new store
									$shippingcustomername = $gSession['shipping'][0]['shippingcustomername'];
									$shippingcustomeraddress1 = $gSession['shipping'][0]['shippingcustomeraddress1'];
									$shippingcustomeraddress2 = $gSession['shipping'][0]['shippingcustomeraddress2'];
									$shippingcustomeraddress3 = $gSession['shipping'][0]['shippingcustomeraddress3'];
									$shippingcustomeraddress4 = $gSession['shipping'][0]['shippingcustomeraddress4'];
									$shippingcontactlastname = $gSession['shipping'][0]['shippingcontactlastname'];
									$shippingcustomercity = $gSession['shipping'][0]['shippingcustomercity'];
									$shippingcustomercounty = $gSession['shipping'][0]['shippingcustomercounty'];
									$shippingcustomerstate = $gSession['shipping'][0]['shippingcustomerstate'];
									$shippingcustomerregioncode = $gSession['shipping'][0]['shippingcustomerregioncode'];

									if ($stmt = $dbObj->prepare('INSERT INTO `SITES` (`id`, `datecreated`, `code`, `name`, `address1`, `address2`, `address3`, `address4`, `city`, `county`, `state`,
																					`regioncode`, `region`, `postcode`, `countrycode`, `countryname`, `telephonenumber`, `emailaddress`, `contactfirstname`, `contactlastname`,
																					`sitetype`, `sitegroup`, `distributioncentrecode`, `openingtimes`, `storeurl`, `smtpproductionname`, `smtpproductionaddress`,
																					`siteonline`, `isexternalstore`, `active`)
																					VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "", "", ?, ?, "", "", 0, 1, 1)'))
									{
										if ($stmt->bind_param('ssssssssssssssssssiss', $dbCode, $shippingcustomername,
														$shippingcustomeraddress1, $shippingcustomeraddress2, $shippingcustomeraddress3,
														$shippingcustomeraddress4, $shippingcustomercity, $shippingcustomercounty,
														$shippingcustomerstate, $shippingcustomerregioncode,
														$gSession['shipping'][0]['shippingcustomerregion'],
														$gSession['shipping'][0]['shippingcustomerpostcode'],
														$gSession['shipping'][0]['shippingcustomercountrycode'],
														$gSession['shipping'][0]['shippingcustomercountryname'],
														$shippingcustomertelephonenumber, $shippingcustomeremailaddress,
														$shippingcontactfirstname, $shippingcontactlastname, $siteType, $storeOpeningTimes,
														$storeUrl))
										{
											if ($stmt->execute())
											{
												$siteId = $dbObj->insert_id;
												DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
														$gSession['username'], 0, 'ADMIN', 'EXTERNAL-STORE-INSERT', $dbCode, 1);
											}
											else
											{
												$result = 'str_DatabaseError';
												$resultParam = 'externalStoreAdd execute ' . $dbObj->error;
											}
										}
										else
										{
											// could not bind parameters
											$result = 'str_DatabaseError';
											$resultParam = 'externalStoreAdd bind ' . $dbObj->error;
										}
										$stmt->free_result();
										$stmt->close();
										$stmt = null;
									}
									else
									{
										// could not prepare statement
										$result = 'str_DatabaseError';
										$resultParam = 'externalStoreAdd prepare ' . $dbObj->error;
									}
								}
								else
								{
									// update details of existing external store
									if ($stmt = $dbObj->prepare('UPDATE `SITES` SET `name` = ?, `address1` = ?, `address2` = ?, `address3` = ?, `address4` = ?, `city` = ?, `county` = ?, `state` = ?,
																					`regioncode` = ?, `region` = ?, `postcode` = ?, `countrycode` = ?, `countryname` = ?, `telephonenumber` = ?,
																					`emailaddress` = ?, `contactfirstname` = ?, `contactlastname`  = ?, `storeurl` = ?, `openingtimes`  = ?
																					WHERE `code` = ?'))
									{
										if ($stmt->bind_param('ssssssssssssssssssss', $gSession['shipping'][0]['shippingcustomername'],
														$gSession['shipping'][0]['shippingcustomeraddress1'],
														$gSession['shipping'][0]['shippingcustomeraddress2'],
														$gSession['shipping'][0]['shippingcustomeraddress3'],
														$gSession['shipping'][0]['shippingcustomeraddress4'],
														$gSession['shipping'][0]['shippingcustomercity'],
														$gSession['shipping'][0]['shippingcustomercounty'],
														$gSession['shipping'][0]['shippingcustomerstate'],
														$gSession['shipping'][0]['shippingcustomerregioncode'],
														$gSession['shipping'][0]['shippingcustomerregion'],
														$gSession['shipping'][0]['shippingcustomerpostcode'],
														$gSession['shipping'][0]['shippingcustomercountrycode'],
														$gSession['shipping'][0]['shippingcustomercountryname'],
														$shippingcustomertelephonenumber,
                                                        $shippingcustomeremailaddress,
														$shippingcontactfirstname,
                                                        $shippingcontactlastname, $storeUrl, $storeOpeningTimes,
														$dbCode))
										{
											if ($stmt->execute())
											{
												$siteId = $dbObj->insert_id;
												DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'],
														$gSession['username'], 0, 'ADMIN', 'EXTERNAL-STORE-UPDATE', $dbCode, 1);
											}
											else
											{
												$result = 'str_DatabaseError';
												$resultParam = 'externalStoreUpdate execute ' . $dbObj->error;
											}
										}
										else
										{
											// could not bind parameters
											$result = 'str_DatabaseError';
											$resultParam = 'externalStoreUpdate bind ' . $dbObj->error;
										}
										$stmt->free_result();
										$stmt->close();
										$stmt = null;
									}
									else
									{
										// could not prepare statement
										$result = 'str_DatabaseError';
										$resultParam = 'externalStoreUpdate prepare ' . $dbObj->error;
									}
								}
							}
						}
						else
						{
							$gSession['shipping'][0]['collectfromstore'] = false;
							$gSession['shipping'][0]['payinstoreallowed'] = TPX_PAY_IN_STORE_DO_NOT_ALLOW;
							$gSession['shipping'][0]['storeid'] = '';
							$gSession['shipping'][0]['distributioncentrecode'] = '';
							$gSession['shipping'][0]['storecustomername'] = '';
							$gSession['shipping'][0]['storecustomeraddress1'] = '';
							$gSession['shipping'][0]['storecustomeraddress2'] = '';
							$gSession['shipping'][0]['storecustomeraddress3'] = '';
							$gSession['shipping'][0]['storecustomeraddress4'] = '';
							$gSession['shipping'][0]['storecustomercity'] = '';
							$gSession['shipping'][0]['storecustomercounty'] = '';
							$gSession['shipping'][0]['storecustomerstate'] = '';
							$gSession['shipping'][0]['storecustomerregioncode'] = '';
							$gSession['shipping'][0]['storecustomerregion'] = '';
							$gSession['shipping'][0]['storecustomerpostcode'] = '';
							$gSession['shipping'][0]['storecustomercountrycode'] = '';
							$gSession['shipping'][0]['storecustomercountryname'] = '';
							$gSession['shipping'][0]['storecustomertelephonenumber'] = '';
							$gSession['shipping'][0]['storecustomeremailaddress'] = '';
							$gSession['shipping'][0]['storecontactfirstname'] = '';
							$gSession['shipping'][0]['storecontactlastname'] = '';
							$gSession['shipping'][0]['storecontacturl'] = '';
						}

						$orderItemsCount = count($gSession['items']);
						$origOrderId = 0;
						$origOrderNumber = 0;
						if ($gSession['order']['isreorder'] == 1)
						{
							$origOrderId = $gSession['order']['origorderid'];
							$origOrderNumber = $gSession['order']['origordernumber'];
						}

						$giftcardamount = $gSession['order']['ordergiftcardtotal'];

						if ($gSession['ordergiftcarddeleted'])
						{
							$giftcardamount = 0.00;
						}

						// if a credit card integration was used then we need to record which one it was
						if ($gSession['order']['paymentmethodcode'] == 'CARD')
						{
							$paymentGatewayCode = $gSession['order']['ccitype'];
						}

						// change the pricing engine type for recording if set to be legacy
						if ($gSession['order']['uselegacypricingsystem'])
						{
							$pricingEngineVersion = TPX_PRICINGENGINE_LEGACY;
						}

						if ($stmt = $dbObj->prepare('INSERT INTO `ORDERHEADER` (`id`, `datecreated`, `ownercode`, `groupcode`, `groupdata`, `shoppingcarttype`,
								`designeruuid`, `userid`, `useripaddress`, `userbrowser`, `sessionid`, `origorderid`, `origordernumber`,`temporder`,
								`temporderid`, `temporderexpirydate`, `offlineorder`, `offlineordercompletedbycustomer`, `webbrandcode`, `languagecode`, `billingcustomeraccountcode`,
								`billingcustomername`, `billingcustomeraddress1`, `billingcustomeraddress2`, `billingcustomeraddress3`,
								`billingcustomeraddress4`, `billingcustomercity`, `billingcustomercounty`, `billingcustomerstate`,
								`billingcustomerregioncode`, `billingcustomerregion`, `billingcustomerpostcode`, `billingcustomercountrycode`,
								`billingcustomercountryname`, `billingcustomertelephonenumber`, `billingcustomeremailaddress`,
								`billingcontactfirstname`, `billingcontactlastname`, `billingcustomerregisteredtaxnumbertype`, `billingcustomerregisteredtaxnumber`, `currencycode`, `currencyname`,
								`currencyisonumber`, `currencysymbol`, `currencysymbolatfront`, `currencydecimalplaces`,
								`currencyexchangerate`, `basecurrencycode`, `voucherpromotioncode`, `voucherpromotionname`, `vouchercode`, `vouchertype`, `vouchername`, `voucherdiscountsection`,
								`voucherdiscounttype`, `voucherdiscountvalue`, `voucherapplicationmethod`, `vouchermaxqtytoapplydiscountto`, `vouchersellprice`, `voucheragentfee`, `itemcount`, `itemtotalcost`, `itemtotalsell`, `itemtotaltax`,
								`shippingtotalcost`, `shippingtotalsellbeforediscount`, `shippingtotalsell`, `shippingtotaltax`,
								`shippingtotalweight`, `totalcost`, `showzerotax`, `showtaxbreakdown`, `orderalltaxratesequal`,
								`showalwaystaxtotal`, `totalsellbeforediscount`, `totaltaxbeforediscount`, `totalbeforediscount`, `totaldiscount`,
								`totalsell`, `ordertotalitemsellwithtax`, `totaltax`, `total`, `giftcardamount`, `usergiftcardbalance`, `totaltopay`,
								`orderfootertotalwithtax`, `orderfootertotalnotax`, `orderfootertotalnotaxnodiscount`, `orderfootertaxratesequal`, `orderfootersubtotal`, `orderfootertotal`, `orderfootertotaltax`, `paymentmethodcode`, `paymentmethodname`,
								`requiresdelivery`, `ccilogid`, `useremaildestination`, `paymentgatewaycode`,  `paymentgatewaysubcode`,
								`paymentreceived`, `paymentreceivedtimestamp`, `paymentreceiveddate`, `paymentreceiveduserid`, `pricesincludetax`, `pricingengineversion`)
								VALUES (0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
						{
							if ($stmt->bind_param('ssssi' . 'sissiisi' . 'isiisss' . 'ssss' . 'ssss' . 'ssss' . 'sss' . 'ssisss' . 'ssii' . 'dssssiss' . 'sdiiddiddd' . 'dddd' . 'ddiii' . 'idddd' . 'ddddddd' . 'dddidddss' . 'iiiss' . 'issiii',
											$orderDate, $gSession['licensekeydata']['ownercode'], $gSession['licensekeydata']['groupcode'],
											$gSession['licensekeydata']['groupdata'], $gSession['order']['shoppingcarttype'],
											$gSession['order']['uuid'], $gSession['userid'], $gSession['order']['useripaddress'],
											$gSession['order']['userbrowser'], $gSession['ref'], $origOrderId, $origOrderNumber, $isTempOrder,
											$gSession['order']['temporderid'], $tempOrderExpiryDate, $gSession['order']['isofflineorder'],
											$gSession['order']['isofflineordercompletedbycustomer'], $gSession['webbrandcode'],
											$gSession['browserlanguagecode'], $gSession['order']['billingcustomeraccountcode'],
											$gSession['order']['billingcustomername'], $gSession['order']['billingcustomeraddress1'],
											$gSession['order']['billingcustomeraddress2'], $gSession['order']['billingcustomeraddress3'],
											$gSession['order']['billingcustomeraddress4'], $gSession['order']['billingcustomercity'],
											$gSession['order']['billingcustomercounty'], $gSession['order']['billingcustomerstate'],
											$gSession['order']['billingcustomerregioncode'], $gSession['order']['billingcustomerregion'],
											$gSession['order']['billingcustomerpostcode'], $gSession['order']['billingcustomercountrycode'],
											$gSession['order']['billingcustomercountryname'], $gSession['order']['billingcustomertelephonenumber'],
											$gSession['order']['billingcustomeremailaddress'], $gSession['order']['billingcontactfirstname'],
											$gSession['order']['billingcontactlastname'], $gSession['order']['billingregisteredtaxnumbertype'],
											$gSession['order']['billingregisteredtaxnumber'], $gSession['order']['currencycode'],
											$gSession['order']['currencyname'], $gSession['order']['currencyisonumber'],
											$gSession['order']['currencysymbol'], $gSession['order']['currencysymbolatfront'],
											$gSession['order']['currencydecimalplaces'], $gSession['order']['currencyexchangerate'],
											$gSession['order']['basecurrencycode'], $voucherPromotionCode, $voucherPromotionName, $voucherCode,
											$voucherType, $voucherName, $voucherDiscountSection, $voucherDiscountType, $voucherDiscountValue,
											$voucherApplicationMethod, $voucherApplyToQty, $voucherSellPrice, $voucherAgentFee, $orderItemsCount,
											$gSession['order']['ordertotalitemcost'], $gSession['order']['ordertotalitemsell'],
											$gSession['order']['ordertotalitemtax'], $gSession['order']['ordertotalshippingcost'],
											$gSession['order']['ordertotalshippingsellbeforediscount'], $shippingTotalSell,
											$gSession['order']['ordertotalshippingtax'], $gSession['order']['ordertotalshippingweight'],
											$gSession['order']['ordertotalcost'], $gSession['order']['showzerotax'], $gSession['order']['showtaxbreakdown'],
											$gSession['order']['orderalltaxratesequal'], $gSession['order']['showalwaystaxtotal'], $totalSellBeforeDiscount,
											$gSession['order']['ordertotaltaxbeforediscount'], $totalBeforeDiscount, $gSession['order']['ordertotaldiscount'],
											$totalSell, $gSession['order']['ordertotalitemsellwithtax'], $gSession['order']['ordertotaltax'],
											$gSession['order']['ordertotal'], $giftcardamount, $gSession['usergiftcardbalance'],
											$gSession['order']['ordertotaltopay'], $gSession['order']['orderfootertotalwithtax'],
											$gSession['order']['orderfootertotalnotax'], $gSession['order']['orderfootertotalnotaxnodiscount'],
											$gSession['order']['orderfootertaxratesequal'], $gSession['order']['orderfootersubtotal'],
											$gSession['order']['orderfootertotal'], $gSession['order']['orderfootertotaltax'],
											$gSession['order']['paymentmethodcode'], $gSession['order']['paymentmethodname'],
											$gSession['order']['shippingrequiresdelivery'], $gSession['order']['ccilogid'],
											$gSession['order']['useremaildestination'], $paymentGatewayCode, $gSession['order']['paymentgatewaycode'],
											$paymentReceived, $paymentReceivedTimestamp, $paymentReceivedDate, $paymentReceivedUserId,
											$gSession['order']['showpriceswithtax'], $pricingEngineVersion))
							{
								if ($stmt->execute())
								{
									$orderHeaderRecordID = $dbObj->insert_id;
								}
								else
								{
									// could not execute statement
									$result = 'str_DatabaseError';
									$resultParam = 'orderconfirm insert orderheader execute ' . $dbObj->error;
								}
							}
							else
							{
								// could not bind parameters
								$result = 'str_DatabaseError';
								$resultParam = 'orderconfirm insert orderheader bind ' . $dbObj->error;
							}
							$stmt->free_result();
							$stmt->close();
							$stmt = null;
						}
						else
						{
							// could not prepare statement
							$result = 'str_DatabaseError';
							$resultParam = 'orderconfirm insert orderheader prepare ' . $dbObj->error;
						}

						// if no error has occurred we have inserted the order header so now insert the order meta-data
						$metaDataRefList = '';
						if ($result == '')
						{
							$metaDataResult = MetaDataObj::storeMetaData($orderHeaderRecordID, 0, 0, $gSession['userid'], 'ORDER',
											$gSession['order']['metadata']);
							$result = $metaDataResult['result'];
							$resultParam = $metaDataResult['resultparam'];

							// if no error has occurred we have inserted the order meta-data so now update the order header with the meta-data
							if ($result == '')
							{
								$metaDataRefList = $metaDataResult['reflist'];
							}
						}


						// if no error has occurred we have inserted the order meta-data so now update the order date, order number and order meta-data reference list
						if ($result == '')
						{
							// configure the order number
							// if the order was originally a temp order then we want to use the same order number
							if (($wasTempOrder == 1) && ($gSession['order']['tempordernumber'] != ''))
							{
								$orderNumber = $gSession['order']['tempordernumber'];
								$generateOrderNumber = false;
							}
							else
							{
								$generateOrderNumber = true;
							}


							$retryCount = 100;
							$createOrderNumber = true;
							while ($createOrderNumber)
							{
								// increase the standard php timeout just incase it takes multiple attempts to find an order number
								UtilsObj::resetPHPScriptTimeout(10);

								if ($generateOrderNumber)
								{
									$orderNumber = $orderHeaderRecordID; // same as the order header identity column
									if ($ac_config['ORDERNUMBERFORMAT'] == 'TICKS')
									{
										$orderNumber = time();
										$orderNumber = abs($orderNumber); // make sure the order number is never negative
									}

									// if necessary pad the number (default to 7 if the parameter is not present)
									$numberPadding = $ac_config['ORDERNUMBERPADDING'];
									if ($numberPadding == '')
									{
										$numberPadding = '7';
									}

									if (is_numeric($numberPadding))
									{
										$numberPaddingInt = (int) $numberPadding;
										if ($numberPaddingInt > 0)
										{
											$orderNumber = str_pad($orderNumber, $numberPaddingInt, '0', STR_PAD_LEFT);
										}
									}

									// if necessary add the prefix
									$numberPrefix = $ac_config['ORDERNUMBERPREFIX'];
									if ($numberPrefix != '')
									{
										$orderNumber = $numberPrefix . $orderNumber;
									}
								}

								if ($stmt = $dbObj->prepare('UPDATE `ORDERHEADER` SET `orderdate` = `datecreated`, `ordernumber` = ?, `metadatacodelist` = ? WHERE `id` = ?'))
								{
									if ($stmt->bind_param('ssi', $orderNumber, $metaDataRefList, $orderHeaderRecordID))
									{
										// if the order number has been updated check to make sure it is unique
										if ($stmt->execute())
										{
											// if we have generated the order number then we must make sure it is unique
											if ($generateOrderNumber)
											{
												$stmt->free_result();
												$stmt->close();
												$stmt = null;

												if ($stmt = $dbObj->prepare('SELECT `id` FROM `ORDERHEADER` WHERE (`ordernumber` = ?) AND (`id` <> ?)'))
												{
													if ($stmt->bind_param('si', $orderNumber, $orderHeaderRecordID))
													{
														if ($stmt->execute())
														{
															if ($stmt->store_result())
															{
																if ($stmt->num_rows > 0)
																{
																	// there is another order with that order number so we must try again
																	$result = 'str_DatabaseError';
																	$resultParam = 'orderconfirm confirm duplicate order number error';
																	$retryCount = $retryCount - 1;
																	if ($retryCount > 0)
																	{
																		UtilsObj::wait(0.75);
																	}
																	else
																	{
																		$createOrderNumber = false;
																	}
																}
																else
																{
																	// the order number is unique
																	$result = '';
																	$resultParam = '';
																	$createOrderNumber = false;
																}
															}
															else
															{
																// could not store result
																$result = 'str_DatabaseError';
																$resultParam = 'orderconfirm confirm order number store result ' . $dbObj->error;
																$createOrderNumber = false;
															}
														}
														else
														{
															// could not execute statement
															$result = 'str_DatabaseError';
															$resultParam = 'orderconfirm confirm order number execute ' . $dbObj->error;
															$createOrderNumber = false;
														}
													}
													else
													{
														// could not bind parameters
														$result = 'str_DatabaseError';
														$resultParam = 'orderconfirm confirm order number bind params ' . $dbObj->error;
														$createOrderNumber = false;
													}
												}
												else
												{
													// could not prepare statement
													$result = 'str_DatabaseError';
													$resultParam = 'orderconfirm confirm order number prepare ' . $dbObj->error;
													$createOrderNumber = false;
												}
											}
											else
											{
												// we supplied the order number so just report that it is unique
												$result = '';
												$resultParam = '';
												$createOrderNumber = false;
											}
										}
										else
										{
											// could not execute statement
											$result = 'str_DatabaseError';
											$resultParam = 'orderconfirm update order number execute ' . $dbObj->error;
											$createOrderNumber = false;
										}
									}
									else
									{
										// could not bind parameters
										$result = 'str_DatabaseError';
										$resultParam = 'orderconfirm update order number params ' . $dbObj->error;
										$createOrderNumber = false;
									}

									$stmt->free_result();
									$stmt->close();
									$stmt = null;
								}
								else
								{
									// could not prepare statement
									$result = 'str_DatabaseError';
									$resultParam = 'orderconfirm update order number prepare ' . $dbObj->error;
									$createOrderNumber = false;
								}
							}
						}

						$sortOrder = 0;
						$currentLine = 0;
						$tempOrderParentStatusArray = array();
						$companionParentOrderItemRecordIDArray = array();
						$companionParentOrderItemRoutingInfoArray = array();

						// if no error has occurred we have inserted the order number so now insert the order lines
						foreach ($gSession['items'] as $theItem)
						{
							$parentOrderItemID = 0;

							// if the parentorderitemid is not 0 then we know we are dealing with a companion album.
							// we need to set the parentorderitemid for the companion to be the order item record id of the parent.
							if ($theItem['parentorderitemid'] != 0)
							{
								$parentOrderItemID = $companionParentOrderItemRecordIDArray[$theItem['parentorderitemid']];
							}

							if ($result == '')
							{
								$origOrderItemId = 0;

								// if this is a temporary order being converted to a real order we need to copy the status for the uploadref
								if ($wasTempOrder == 1)
								{
									if ($ac_config['SERVERLOCATION'] == 'REMOTE')
									{
										$status = TPX_ITEM_STATUS_FILES_ON_REMOTE_FTP_SERVER;
									}
									else
									{
										$status = TPX_ITEM_STATUS_FILES_RECEIVED;
									}

									// we only want to do a database lookup for parent order items
									// as companion albums have the same uploadref of their parents then we must record the original status of the original parent item.
									// when processing companions we can simpy pull the status info from the tempOrderParentStatusArray using the parent uploadref
									if (! array_key_exists($theItem['itemuploadref'], $tempOrderParentStatusArray))
									{
										// determine the status of the original item
										if ($stmt = $dbObj->prepare('SELECT `status`, `previewsonline`, `projectbuildstartdate`, `projectbuildduration`, `uploaddatatype`,`uploadmethod`,
																	`uploadappversion`, `uploadappplatform`, `uploadappcputype`, `uploadapposversion`, `uploaddatasize`, `uploadduration`
																	FROM `ORDERITEMS` WHERE (`orderid` = ?) AND (`uploadref` = ?) AND (`parentorderitemid` = 0)'))
										{
											if ($stmt->bind_param('is', $gSession['order']['temporderid'], $theItem['itemuploadref']))
											{
												if ($stmt->bind_result($origItemStatus, $origPreviewsOnline, $origProjectBuildStartDate, $origProjectBuildDuration, $origUploadDataType, $origUploadMethod,
																		$origUploadAppVersion, $origUploadAppPlatform, $origUploadAppCPUType, $origUploadAppOSVersion, $origUploadDataSize, $origUploadDuration))
												{
													if ($stmt->execute())
													{
														if ($stmt->fetch())
														{
															$tempOrderParentStatusArray[$theItem['itemuploadref']]['status'] = $origItemStatus;
															$tempOrderParentStatusArray[$theItem['itemuploadref']]['previewsonline'] = $origPreviewsOnline;
															$tempOrderParentStatusArray[$theItem['itemuploadref']]['itemprojectstarttime'] = $origProjectBuildStartDate;
															$tempOrderParentStatusArray[$theItem['itemuploadref']]['itemprojectduration'] = $origProjectBuildDuration;
															$tempOrderParentStatusArray[$theItem['itemuploadref']]['itemuploadappversion'] = $origUploadAppVersion;
															$tempOrderParentStatusArray[$theItem['itemuploadref']]['uploadmethod'] = $origUploadMethod;
															$tempOrderParentStatusArray[$theItem['itemuploadref']]['itemuploaddatatype'] = $origUploadDataType;
															$tempOrderParentStatusArray[$theItem['itemuploadref']]['itemuploadappplatform'] = $origUploadAppPlatform;
															$tempOrderParentStatusArray[$theItem['itemuploadref']]['itemuploadappcputype'] = $origUploadAppCPUType;
															$tempOrderParentStatusArray[$theItem['itemuploadref']]['itemuploadapposversion'] = $origUploadAppOSVersion;
															$tempOrderParentStatusArray[$theItem['itemuploadref']]['itemuploaddatasize'] = $origUploadDataSize;
															$tempOrderParentStatusArray[$theItem['itemuploadref']]['itemuploadduration'] = $origUploadDuration;
														}
														else
														{
															// we should always find a match but just incase set the status to waiting for files
															$status = TPX_ITEM_STATUS_AWAITING_FILES;
														}
													}
													else
													{
														$result = 'str_DatabaseError';
														$resultParam = 'orderconfirm select orderitems execute ' . $dbObj->error;
													}
												}
												else
												{
													$result = 'str_DatabaseError';
													$resultParam = 'orderconfirm select orderitems bind result ' . $dbObj->error;
												}
											}
											$stmt->free_result();
											$stmt->close();
											$stmt = null;
										}
									}

									if ($result == '')
									{
										// set the correct status, uplaodmethod and item info from the tempOrderParentStatusArray
										// this means that both parents and any subsequent companion albums with the same upload ref will all have the correct data.
										$tempOrderStatusArray = $tempOrderParentStatusArray[$theItem['itemuploadref']];

										foreach ($tempOrderStatusArray as $key => $value)
										{
											if ($key == 'status')
											{
												$status = $value;
											}
											elseif ($key == 'uploadmethod')
											{
												$uploadMethod = $value;
											}
											else
											{
												$theItem[$key] = $value;
											}
										}
									}
								}
								else
								{
									// the order was not a temporary order
									// if the order is a re-order find the original item to copy the status from
									if ($gSession['order']['isreorder'] == 1)
									{
										$origOrderItemId = $theItem['origorderitemid'];

										if ($ac_config['SERVERLOCATION'] == 'REMOTE')
										{
											$status = TPX_ITEM_STATUS_FILES_ON_REMOTE_FTP_SERVER;
										}
										else
										{
											$status = TPX_ITEM_STATUS_FILES_RECEIVED;
										}

										// determine the status of the original item
										if ($stmt = $dbObj->prepare('SELECT `status`, `previewsonline` FROM `ORDERITEMS` WHERE `id` = ?'))
										{
											if ($stmt->bind_param('i', $theItem['itemuploadorderitemid']))
											{
												if ($stmt->bind_result($origItemStatus, $origPreviewsOnline))
												{
													if ($stmt->execute())
													{
														if ($stmt->fetch())
														{
															// if the original status is greater or equal to files received then set the status to files received
															if ($origItemStatus >= TPX_ITEM_STATUS_FILES_RECEIVED)
															{
																$status = TPX_ITEM_STATUS_FILES_RECEIVED;
															}

															$theItem['previewsonline'] = $origPreviewsOnline;
														}
													}
													else
													{
														$result = 'str_DatabaseError';
														$resultParam = 'orderconfirm select 2 orderitems execute ' . $dbObj->error;
													}
												}
												else
												{
													$result = 'str_DatabaseError';
													$resultParam = 'orderconfirm select 2 orderitems bind result ' . $dbObj->error;
												}
											}
											else
											{
												$result = 'str_DatabaseError';
												$resultParam = 'orderconfirm select 2 orderitems bindn params ' . $dbObj->error;
											}
											$stmt->free_result();
											$stmt->close();
											$stmt = null;
										}
										else
										{
											$result = 'str_DatabaseError';
											$resultParam = 'orderconfirm select 2 orderitems prepare ' . $dbObj->error;
										}
									}
									else
									{
										$status = TPX_ITEM_STATUS_AWAITING_FILES;
									}
								}

								if ($status == TPX_ITEM_STATUS_AWAITING_FILES)
								{
									$requiresUpload = 1;
								}

								// if the order routing has not already been determined route this order line
								if ($calcOrderRouting)
								{
									// if the items upload ref does not exisit in the array then we must calculate the routing info for the order line.
									// this will be either for an order line that has no companions attached or for a line that is a parent and has companion albums
									// all companions should be routed to the same site as the parent as their will only be one set of files for print
									if (! array_key_exists($theItem['itemuploadref'], $companionParentOrderItemRoutingInfoArray))
									{
										$calcOrderLineRouting = true;

										// collect from store is enabled determine if this effects the routing
										if (($gConstants['optioncfs']) && ($theShippingItem['storeid'] != ''))
										{
											$storeArray = RoutingObj::productAcceptedBySite($theShippingItem['storeid'], $theItem['itemproductcode']);
											$siteCode = $storeArray['routesitecode'];
											$siteCompanyCode = DatabaseObj::getCompanyCodeFromSiteCode($siteCode);

											$ownerOrderKeyInitialize = 'UNALLOCATED';
											$ownerOrderKey = $storeArray['productionsitekey'];
											$productionSiteType = $storeArray['productionsitetype'];

											if ($siteCode != '')
											{
												$calcOrderLineRouting = false;
											}
										}

										// if we can still route the line then process the routing rules
										if ($calcOrderLineRouting)
										{
											$coverCode = '';
											$paperCode = '';

											$sectionArray = $theItem['sections'];

											foreach ($sectionArray as $sectionItem)
											{
												if ($sectionItem['sectioncode'] == 'COVER')
												{
													$coverCode = $sectionItem['code'];
												}

												if ($sectionItem['sectioncode'] == 'PAPER')
												{
													$paperCode = $sectionItem['code'];
												}
											}

											// attempt to route the order line
											$siteResultArray = RoutingObj::RouteOrder($gSession['webbrandcode'],
															$gSession['licensekeydata']['groupcode'], $gSession['userid'],
															$theItem['itemproductcode'], $coverCode, $paperCode,
															$theShippingItem['shippingcustomercountrycode'],
															$theShippingItem['shippingcustomeraddress1'],
															$theShippingItem['shippingcustomeraddress2'],
															$theShippingItem['shippingcustomeraddress3'],
															$theShippingItem['shippingcustomeraddress4'], $theShippingItem['shippingcustomercity'],
															$theShippingItem['shippingcustomerregioncode'],
															$theShippingItem['shippingcustomerpostcode'], $voucherCode);

											$siteCode = $siteResultArray['routesitecode'];
											$siteCompanyCode = DatabaseObj::getCompanyCodeFromSiteCode($siteCode);

											$ownerOrderKeyInitialize = 'UNALLOCATED';
											$ownerOrderKey = $siteResultArray['productionsitekey'];
											$productionSiteType = $siteResultArray['productionsitetype'];
										}

										$companionParentOrderItemRoutingInfoArray[$theItem['itemuploadref']]['sitecode'] = $siteCode;
										$companionParentOrderItemRoutingInfoArray[$theItem['itemuploadref']]['sitecompanycode'] = $siteCompanyCode;
										$companionParentOrderItemRoutingInfoArray[$theItem['itemuploadref']]['ownerorderkeyinitialize'] = $ownerOrderKeyInitialize;
										$companionParentOrderItemRoutingInfoArray[$theItem['itemuploadref']]['ownerorderkey'] = $ownerOrderKey;
										$companionParentOrderItemRoutingInfoArray[$theItem['itemuploadref']]['productionsitetype'] = $productionSiteType;
									}
									else
									{
										// we must set all companion items to be routed the same as the parent
										$parentRoutingInfo = $companionParentOrderItemRoutingInfoArray[$theItem['itemuploadref']];

										$siteCode = $parentRoutingInfo['sitecode'];
										$siteCompanyCode = $parentRoutingInfo['sitecompanycode'];
										$ownerOrderKeyInitialize = $parentRoutingInfo['ownerorderkeyinitialize'];
										$ownerOrderKey = $parentRoutingInfo['ownerorderkey'];
										$productionSiteType = $parentRoutingInfo['productionsitetype'];
									}
								}

								// insert the order line
								if ($stmt = $dbObj->prepare('INSERT INTO `ORDERITEMS` (`id`, `datecreated`, `origcompanycode`, `origowner`, `origownertype`,
										`currentcompanycode`, `currentowner`, `currentownertype`, `ownerorderkey`, `orderid`,
										`itemnumber`, `userid`, `source`, `uploadbatchref`, `uploadref`, `origorderid`,`origorderitemid`, `shareid`, `parentorderitemid`,
										`projectref`, `projectreforig`, `projectname`, `projectbuildstartdate`, `projectbuildduration`,
										`productcollectioncode`, `productcollectionorigownercode`, `productcollectionname`, `productcode`, `productcodepurchased`, `skucode`,
										`productname`, `productinfo`, `producttype`, `productoptions`, `productpageformat`, `productspreadpageformat`,
										`productcover1format`, `productcover2format`, `productoutputformat`, `productheight`, `productwidth`,
										`assetid`, `productdefaultpagecount`, `pagecount`, `pagecountpurchased`, `productunitcost`, `productunitsell`,
										`taxcode`, `taxname`, `taxrate`, `productunitweight`, `producttotalweight`, `qty`,
										`producttotalcost`, `producttotalsell`, `producttotaltax`, `subtotal`, `voucherapplied`, `discountvalue`, `discountname`,
										`totalcost`, `totalsell`, `totaltax`, `totalshippingweight`, `uploadmethod`, `status`, `orderwebversion`,
										`uploadappversion`, `uploadappplatform`, `uploadappcputype`, `uploadapposversion`, `uploaddatasize`, `uploadduration`,
										`uploaddatatype`, `previewsonline`, `canupload`, `activetimestamp`, `projectaimode`)
										VALUES (0, now(), ?, ?, ?, ?, ? , ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
										?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
										?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)'))
								{
									if ($stmt->bind_param('ssissisi' . 'iiissiiii' . 'ssssi' . 'ssssss' . 'ssiiii' . 'iiidd' . 'iiiidd' . 'ssdddi' . 'ddddids' . 'ddddiis' . 'ssssi' . 'iiiii',
													$siteCompanyCode, $siteCode, $productionSiteType, $siteCompanyCode, $siteCode,
													$productionSiteType, $ownerOrderKeyInitialize, $orderHeaderRecordID,
													$theItem['orderlineid'], $gSession['userid'], $theItem['source'],
													$theItem['itemuploadbatchref'], $theItem['itemuploadref'], $origOrderId, $origOrderItemId,
													$theItem['itemshareid'], $parentOrderItemID, $theItem['itemprojectref'], $theItem['itemprojectreforig'],
													$theItem['itemprojectname'], $theItem['itemprojectstarttime'],
													$theItem['itemprojectduration'], $theItem['itemproductcollectioncode'], $theItem['itemproductcollectionorigownercode'],
													$theItem['itemproductcollectionname'], $theItem['itemproductcode'],
													$theItem['itemproductcode'], $theItem['itemproductskucode'], $theItem['itemproductname'],
													$theItem['itemproductinfo'], $theItem['itemproducttype'], $theItem['productoptions'], $theItem['itemproductpageformat'],
													$theItem['itemproductspreadpageformat'], $theItem['itemproductcover1format'],
													$theItem['itemproductcover2format'], $theItem['itemproductoutputformat'],
													$theItem['itemproductheight'], $theItem['itemproductwidth'], $theItem['assetid'],
													$theItem['itemproductdefaultpagecount'], $theItem['itempagecount'],
													$theItem['itempagecount'], $theItem['itemproductunitcost'], $theItem['itemproductunitsell'],
													$theItem['itemtaxcode'], $theItem['itemtaxname'], $theItem['itemtaxrate'],
													$theItem['itemproductunitweight'], $theItem['itemproducttotalweight'], $theItem['itemqty'],
													$theItem['itemproducttotalcost'], $theItem['itemproducttotalsell'],
													$theItem['itemproducttotaltax'], $theItem['itemsubtotal'], $theItem['itemvoucherapplied'],
													$theItem['itemdiscountvalue'], $theItem['itemvouchername'], $theItem['itemtotalcost'],
													$theItem['itemtotalsell'], $theItem['itemtaxtotal'], $theItem['itemtotalweight'],
													$uploadMethod, $status, $gConstants['webversionnumber'], $theItem['itemuploadappversion'],
													$theItem['itemuploadappplatform'], $theItem['itemuploadappcputype'],
													$theItem['itemuploadapposversion'], $theItem['itemuploaddatasize'],
													$theItem['itemuploadduration'], $theItem['itemuploaddatatype'], $theItem['previewsonline'],
													$theItem['canupload'], $theItem['itemaimode']))

									{
										if ($stmt->execute())
										{
											$orderItemRecordID = $dbObj->insert_id;

											// if the parentorderitemid is 0 then we are potentially dealing with an item that has companions.
											// we need to record the orderitem id so that any companions can use it for their parentorderitemid.
											if ($theItem['parentorderitemid'] == 0)
											{
												$companionParentOrderItemRecordIDArray[$theItem['orderlineid']] = $orderItemRecordID;
											}

											// if we have not been provided with upload order details set it to the current order
											if ($theItem['itemuploadgroupcode'] == '')
											{
												$theItem['itemuploadgroupcode'] = $gSession['licensekeydata']['groupcode'];
											}

											if ($theItem['itemuploadorderid'] == 0)
											{
												$theItem['itemuploadorderid'] = $orderHeaderRecordID;
											}

											if ($theItem['itemuploadordernumber'] == '')
											{
												$theItem['itemuploadordernumber'] = $orderNumber;
											}

											if ($theItem['itemuploadorderitemid'] == 0)
											{
												$theItem['itemuploadorderitemid'] = $orderItemRecordID;
											}

											// check to see if the item is using a custom tax code.
											// we need to make sure only the first 13 characters are inserted so that it matches TPX_CUSTOMTAX
											if (substr($theItem['itemtaxcode'], 0, 13) == TPX_CUSTOMTAX)
											{
												$theItem['itemtaxcode'] = TPX_CUSTOMTAX;
											}

											// calculate the order order key
											if ($siteCode == '')
											{
												$itemOwnerOrderKey = '';
											}
											else
											{
												$itemOwnerOrderKey = $siteCode . $orderItemRecordID . $ownerOrderKey;
												$itemOwnerOrderKey = md5($itemOwnerOrderKey);
											}

											if ($stmt2 = $dbObj->prepare('UPDATE `ORDERITEMS` SET `ownerorderkey` = ?, `uploadgroupcode` = ?, `uploadorderid` = ?, `uploadordernumber` = ?, `uploadorderitemid` = ?, `taxcode` = ? WHERE `id` = ?'))
											{
												if ($stmt2->bind_param('ssisisi', $itemOwnerOrderKey, $theItem['itemuploadgroupcode'],
																$theItem['itemuploadorderid'], $theItem['itemuploadordernumber'],
																$theItem['itemuploadorderitemid'], $theItem['itemtaxcode'], $orderItemRecordID))
												{
													$stmt2->execute();
												}
												$stmt2->free_result();
												$stmt2->close();
											}
										}
										else
										{
											// could not execute statement
											$result = 'str_DatabaseError';
											$resultParam = 'orderconfirm insert orderitems execute ' . $dbObj->error;
										}
									}
									else
									{
										// could not bind parameters
										$result = 'str_DatabaseError';
										$resultParam = 'orderconfirm insert orderitems bind ' . $dbObj->error;
									}
									$stmt->free_result();
									$stmt->close();
									$stmt = null;
								}
								else
								{
									// could not prepare statement
									$result = 'str_DatabaseError';
									$resultParam = 'orderconfirm insert orderitems prepare ' . $dbObj->error;
								}
							}

							// if no error has occurred we have inserted the order line so now insert the components
							if ($result == '')
							{
								// when inserting order item components we first try to perfrom the insert in one transaction.
								// we try the transaction for up to 3 times when receiveing deadlocks.
								// if we receive any other database errors whilst attempting the insert in a transaction we then try to insert all item components individually outside of a transaction.
								$abandonOnError = true;
								$transactionCommited = false;
								$retryCount = 3;

								while (! $transactionCommited)
								{
									// if the retryCount is 0 then we know have exceed the number of allowed retries.
									// we set the abandonOnError flag to false so that we can attempt the insert one by one ignoring any database errors.
									if ($retryCount == 0)
									{
										 $abandonOnError = false;
									}
									else
									{
										$dbObj->query('START TRANSACTION');
									}

									$resultArray = self::insertOrderItemComponents(0, $sortOrder, $theItem['itemexternalassets'], $orderHeaderRecordID,
											$orderItemRecordID, $gSession['userid'], $theItem['itemqty'], $dbObj,
											TPX_ORDERITEMCOMPONENTARRAYTYPE_EXTERNALASSET, false, $abandonOnError);

									if (($resultArray['result'] == '') || (! $abandonOnError))
									{
										$resultArray = self::insertOrderItemComponents(0, $sortOrder, $theItem['pictures'], $orderHeaderRecordID, $orderItemRecordID,
												$gSession['userid'], $theItem['itemqty'], $dbObj, TPX_ORDERITEMCOMPONENTARRAYTYPE_SINGLEPRINT, false, $abandonOnError);

										if (($resultArray['result'] == '') || (! $abandonOnError))
										{
											// only insert calendar order item components for calendars
											if ($theItem['itemproducttype'] == TPX_PRODUCTCOLLECTIONTYPE_CALENDAR)
											{
												$resultArray = self::insertOrderItemComponents(0, $sortOrder, $theItem['calendarcustomisations'], $orderHeaderRecordID,
													$orderItemRecordID, $gSession['userid'], $theItem['itemqty'], $dbObj,
													TPX_ORDERITEMCOMPONENTARRAYTYPE_CALENDAR, false, $abandonOnError);
											}

											if (($resultArray['result'] == '') || (! $abandonOnError))
											{
												// insert the AI component if applicable
												$resultArray = self::insertAIOrderItemComponent($theItem, $sortOrder, $orderHeaderRecordID, $orderItemRecordID, $dbObj, $abandonOnError);
											}

											if (($resultArray['result'] == '') || (! $abandonOnError))
											{
												$resultArray = self::insertOrderItemComponents(0, $sortOrder, $theItem['sections'], $orderHeaderRecordID, $orderItemRecordID,
													$gSession['userid'], $theItem['itemqty'], $dbObj, TPX_ORDERITEMCOMPONENTARRAYTYPE_COMPONENT, false, $abandonOnError);

												if (($resultArray['result'] == '') || (! $abandonOnError))
												{
													$resultArray = self::insertOrderItemComponents(0, $sortOrder, $theItem['checkboxes'], $orderHeaderRecordID, $orderItemRecordID,
														$gSession['userid'], $theItem['itemqty'], $dbObj, TPX_ORDERITEMCOMPONENTARRAYTYPE_COMPONENT, false, $abandonOnError);

													if (($resultArray['result'] == '') || (! $abandonOnError))
													{
														$resultArray = self::insertOrderItemComponents(0, $sortOrder, $theItem['lineFooterSections'], $orderHeaderRecordID,
															$orderItemRecordID, $gSession['userid'], $theItem['itemqty'], $dbObj,
															TPX_ORDERITEMCOMPONENTARRAYTYPE_COMPONENT, false, $abandonOnError);

														if (($resultArray['result'] == '') || (! $abandonOnError))
														{
															$resultArray = self::insertOrderItemComponents(0, $sortOrder, $theItem['lineFooterCheckboxes'], $orderHeaderRecordID,
																$orderItemRecordID, $gSession['userid'], $theItem['itemqty'], $dbObj,
																TPX_ORDERITEMCOMPONENTARRAYTYPE_COMPONENT, false, $abandonOnError);


															// if we have no error and abandonOnError is true then we have a transaction which we need to commit
															if (($resultArray['result'] == '') && ($abandonOnError == true))
															{
																$dbObj->query('COMMIT');
																$transactionCommited = true;

																// set the retrycount to 0 here as we know we have at least inserted something.
																// the next retrycount decrement will then set this to -1 and break out of the while loop.
																$retryCount = 0;
															}
														}
													}
												}
											}
										}
									}

									// if the error was not a deadlock then we need to retry inserting each order item component individually.
									// setting retry count to 1 at this point allows us to conitnue the while loop one more time.
									if (($resultArray['dberrorcode'] != 1213) && ($resultArray['dberrorcode'] != 0))
									{
										$retryCount = 1;
									}

									// we only attempt the rollback if we know we got a dberror and we are trying to insert using a transaction (abandonOnError)
									if ((! $transactionCommited) && ($abandonOnError))
									{
										$dbObj->query('ROLLBACK');
									}

									$retryCount--;

									if ($retryCount < 0)
									{
										break;
									}
								}
							}

							// increment the current line variable which is used in multi line order routing.
							$currentLine++;
						} // loop around order items
						// if no error has occurred we have inserted the order lines so now insert the orderfooter components
						if ($result == '')
						{
							self::insertOrderItemComponents(0, $sortOrder, $gSession['order']['orderFooterSections'], $orderHeaderRecordID, -1,
									$gSession['userid'], 1, $dbObj, TPX_ORDERITEMCOMPONENTARRAYTYPE_COMPONENT, false, false);
							self::insertOrderItemComponents(0, $sortOrder, $gSession['order']['orderFooterCheckboxes'], $orderHeaderRecordID,
									-1, $gSession['userid'], 1, $dbObj, TPX_ORDERITEMCOMPONENTARRAYTYPE_COMPONENT, false, false);
						}

						// if no error has occurred we have inserted the order line including components  so now insert the shipping line
						if ($result == '')
						{
							$theShippingItem = $gSession['shipping'][0];

							// check to see if the item is using a custom tax code.
							// we need to make sure only the first 13 characters are inserted so that it matches TPX_CUSTOMTAX
							if (substr($theShippingItem['shippingratetaxcode'], 0, 13) == TPX_CUSTOMTAX)
							{
								$theShippingItem['shippingratetaxcode'] = TPX_CUSTOMTAX;
							}

							if ($stmt = $dbObj->prepare('INSERT INTO `ORDERSHIPPING` (`id`, `datecreated`, `orderid`, `userid`, `shippingcustomername`, `shippingcustomeraddress1`,
								`shippingcustomeraddress2`, `shippingcustomeraddress3`, `shippingcustomeraddress4`, `shippingcustomercity`, `shippingcustomercounty`, `shippingcustomerstate`,
								`shippingcustomerregioncode`, `shippingcustomerregion`, `shippingcustomerpostcode`,
								`shippingcustomercountrycode`, `shippingcustomercountryname`, `shippingcustomertelephonenumber`, `shippingcustomeremailaddress`,
								`shippingcontactfirstname`, `shippingcontactlastname`, `shippingmethodcode`, `shippingmethodname`, `shippingratecode`, `shippingrateinfo`, `shippingratecost`, `shippingratesell`,
								`shippingdiscountvalue`, `shippingtotalsell`, `shippingratetaxcode`, `shippingratetaxname`, `shippingratetaxrate`, `shippingratecalctax`, `shippingratetaxtotal`, `qty`,
								`storecode`, `storename`, `distributioncentrecode`)
								VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
							{
								if ($stmt->bind_param('iisssssssssssssssssssssddddssdidisss', $orderHeaderRecordID, $gSession['userid'],
												$theShippingItem['shippingcustomername'], $theShippingItem['shippingcustomeraddress1'],
												$theShippingItem['shippingcustomeraddress2'], $theShippingItem['shippingcustomeraddress3'],
												$theShippingItem['shippingcustomeraddress4'], $theShippingItem['shippingcustomercity'],
												$theShippingItem['shippingcustomercounty'], $theShippingItem['shippingcustomerstate'],
												$theShippingItem['shippingcustomerregioncode'], $theShippingItem['shippingcustomerregion'],
												$theShippingItem['shippingcustomerpostcode'], $theShippingItem['shippingcustomercountrycode'],
												$theShippingItem['shippingcustomercountryname'],
												$theShippingItem['shippingcustomertelephonenumber'],
												$theShippingItem['shippingcustomeremailaddress'], $theShippingItem['shippingcontactfirstname'],
												$theShippingItem['shippingcontactlastname'], $theShippingItem['shippingmethodcode'],
												$theShippingItem['shippingmethodname'], $theShippingItem['shippingratecode'],
												$theShippingItem['shippingrateinfo'], $theShippingItem['shippingratecost'],
												$theShippingItem['shippingratesell'], $theShippingItem['shippingratediscountvalue'],
												$theShippingItem['shippingratetotalsell'], $theShippingItem['shippingratetaxcode'],
												$theShippingItem['shippingratetaxname'], $theShippingItem['shippingratetaxrate'],
												$theShippingItem['shippingratecalctax'], $theShippingItem['shippingratetaxtotal'],
												$gSession['items'][0]['itemqty'], $theShippingItem['storeid'],
												$theShippingItem['storecustomername'], $theShippingItem['distributioncentrecode']))
								{
									if ($stmt->execute())
									{
										$orderShippingItemRecordID = $dbObj->insert_id;
									}
									else
									{
										// could not execute statement
										$result = 'str_DatabaseError';
										$resultParam = 'orderconfirm insert ordershipping execute ' . $dbObj->error;
									}
								}
								else
								{
									// could not bind parameters
									$result = 'str_DatabaseError';
									$resultParam = 'orderconfirm insert ordershipping bind ' . $dbObj->error;
								}
								$stmt->free_result();
								$stmt->close();
								$stmt = null;
							}
							else
							{
								// could not prepare statement
								$result = 'str_DatabaseError';
								$resultParam = 'orderconfirm insert ordershipping prepare ' . $dbObj->error;
							}
						}


						// if no error has occurred update the ccilog if the payment was by credit card
						if ($result == '')
						{
							if (($gSession['order']['paymentmethodcode'] == 'CARD') || ($gSession['order']['paymentmethodcode'] == 'PAYPAL') || ($gSession['order']['paymentmethodcode'] == 'KLARNA'))
							{
								if ($gSession['order']['ccilogid'] > 0)
								{
									if ($stmt = $dbObj->prepare('UPDATE `CCILOG` SET `orderid` = ? WHERE `id` = ?'))
									{
										if ($stmt->bind_param('ii', $orderHeaderRecordID, $gSession['order']['ccilogid']))
										{
											if (! $stmt->execute())
											{
												// could not execute statement
												$result = 'str_DatabaseError';
												$resultParam = 'orderconfirm update ccilog execute ' . $dbObj->error;
											}
										}
										else
										{
											// could not bind parameters
											$result = 'str_DatabaseError';
											$resultParam = 'orderconfirm update ccilog bind params ' . $dbObj->error;
										}

										$stmt->free_result();
										$stmt->close();
										$stmt = null;
									}
									else
									{
										// could not prepare statement
										$result = 'str_DatabaseError';
										$resultParam = 'orderconfirm update ccilog prepare ' . $dbObj->error;
									}
								}
							}
							else if ($gSession['order']['paymentmethodcode'] == 'ACCOUNT')
							{
								DatabaseObj::updateCustomerAccountBalance($gSession['userid'], $gSession['order']['ordertotaltopay']);
							}

							if ($gSession['order']['paymentmethodcode'] != 'PAYLATER')
							{
								if (($gSession['ordergiftcarddeleted'] == false) && ($gSession['order']['ordergiftcardtotal'] > 0))
								{
									// if the payment method was a non-immediate payment option or covered by gift card balance update the customer's gift card balance
									DatabaseObj::updateCustomerGiftCardBalance($gSession['userid'],
											(0 - $gSession['order']['ordergiftcardtotal']));
								}
							}
							else
							{
								$gSession['order']['ordergiftcardtotal'] = 0.00;
								DatabaseObj::setSessionGiftCardTotal($gSession['ref'], $gSession['order']['ordergiftcardtotal']);
							}
						}

						// if we have received no error and we originally had a temp order we need to mark the temp order as converted
						if ($result == '')
						{
							if ($wasTempOrder == 1)
							{
								if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `active` = ' . TPX_ORDER_STATUS_CONVERTED . ' WHERE `orderid` = ?'))
								{
									if ($stmt->bind_param('i', $gSession['order']['temporderid']))
									{
										if (!$stmt->execute())
										{
											// could not execute statement
											$result = 'str_DatabaseError';
											$resultParam = 'orderconfirm update ordertempitems execute ' . $dbObj->error;
										}
									}
									else
									{
										// could not bind parameters
										$result = 'str_DatabaseError';
										$resultParam = 'orderconfirm update ordertempitems bind params ' . $dbObj->error;
									}
									$stmt->free_result();
									$stmt->close();
									$stmt = null;
								}
								else
								{
									// could not prepare statement
									$result = 'str_DatabaseError';
									$resultParam = 'orderconfirm update ordertempitems prepare ' . $dbObj->error;
								}

								// if we have received no error prefix the order number and reset the order ready flag
								if ($result == '')
								{
									if ($stmt = $dbObj->prepare('UPDATE `ORDERHEADER` SET `ordernumber` = CONCAT("TMP_", `ordernumber`), `orderready` = 0 WHERE `id` = ?'))
									{
										if ($stmt->bind_param('i', $gSession['order']['temporderid']))
										{
											if (!$stmt->execute())
											{
												// could not execute statement
												$result = 'str_DatabaseError';
												$resultParam = 'orderconfirm update ordertempheader execute ' . $dbObj->error;
											}
										}
										else
										{
											// could not bind parameters
											$result = 'str_DatabaseError';
											$resultParam = 'orderconfirm update ordertempheader bind params ' . $dbObj->error;
										}
										$stmt->free_result();
										$stmt->close();
										$stmt = null;
									}
									else
									{
										// could not prepare statement
										$result = 'str_DatabaseError';
										$resultParam = 'orderconfirm update ordertempheader prepare ' . $dbObj->error;
									}
								}
							}
						}


						// if we have received no error then the order has been created so mark it as ready
						if ($result == '')
						{
							if ($stmt = $dbObj->prepare('UPDATE `ORDERHEADER` SET `orderready` = 1 WHERE `id` = ?'))
							{
								if ($stmt->bind_param('i', $orderHeaderRecordID))
								{
									if ($stmt->execute())
									{
										// write the order status cache file to state that the order process has been completed
										self::writeOrderStatusCacheFile('UPLOAD', true);
									}
									else
									{
										// could not execute statement
										$result = 'str_DatabaseError';
										$resultParam = 'orderconfirm set orderready execute ' . $dbObj->error;
									}
								}
								else
								{
									// could not bind parameters
									$result = 'str_DatabaseError';
									$resultParam = 'orderconfirm set orderready bind params ' . $dbObj->error;
								}
								$stmt->free_result();
								$stmt->close();
								$stmt = null;
							}
							else
							{
								// could not prepare statement
								$result = 'str_DatabaseError';
								$resultParam = 'orderconfirm set orderready prepare ' . $dbObj->error;
							}
						}

						// Release mutex after we have set the processing value.
						DatabaseObj::releaseDBMutex($mutexName);

						$dbObj->close();
					}
					else
					{
						// could not open database connection
						$result = 'str_DatabaseError';
						$resultParam = 'orderconfirm connect ' . $dbObj->error;
					}


					// retrieve the order source for the first line as we will use this to determine which emails and tasks to queue
					// note. this is based on all order lines having the same source which for now will be true
					$orderSource = $gSession['items'][0]['source'];


					// if we have received no error then the order has been created so update the voucher status, update any temporary order variables and notify the user
					if ($result == '')
					{
						$gSession['order']['id'] = $orderHeaderRecordID;
						$gSession['order']['ordernumber'] = $orderNumber;
						$gSession['order']['temporder'] = $isTempOrder;

						if ($isTempOrder == 1)
						{
							$gSession['order']['temporderid'] = $orderHeaderRecordID;
							$gSession['order']['tempordernumber'] = $orderNumber;
							$gSession['order']['temporderexpirydate'] = $tempOrderExpiryDate;
						}

						DatabaseObj::updateSession();

						// update the activity log
						DatabaseObj::updateActivityLog($gSession['ref'], $orderHeaderRecordID, $gSession['userid'], $gSession['userlogin'],
								$gSession['username'], 0, 'ORDER', 'ORDER-CREATION', $orderNumber, 1);

						// if this is an online order we need to queue an event to generate the order data
						if (($gConstants['optiondesol']) && ($gSession['order']['isreorder'] == 0) && ($orderSource == TPX_SOURCE_ONLINE))
						{
							$taskInfo = DatabaseObj::getTask('TAOPIX_ONLINEORDERCREATION');

							if ($taskInfo['result'] == '')
							{
								$projectRefList = '';

								foreach ($gSession['items'] as $theItem)
								{
									// only create an event for main line items and ignore any companion items
									if ($theItem['parentorderitemid'] == 0)
									{
										$eventResultArray = DatabaseObj::createEvent('TAOPIX_ONLINEORDERCREATION', $siteCompanyCode,
														$gSession['licensekeydata']['groupcode'], $gSession['webbrandcode'], $taskInfo['nextRunTime'],
														0, $gSession['licensekeydata']['ownercode'], $theItem['itemprojectref'],
														$theItem['itemuploadref'], $orderHeaderRecordID, '', '', '', '', $orderHeaderRecordID,
														$theItem['itemuploadorderitemid'], $gSession['userid'], '', '', $gSession['userid']);

										$projectRefList .= "'" . $theItem['itemprojectref'] . "',";
									}
								}

								$projectRefList = substr($projectRefList, 0, -1);
							}

							// if the order is via the high level api then we must now empty the users basket as the order has now been placed.
							if ($gSession['order']['basketapiworkflowtype'] == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
							{
								DatabaseObj::removeItemsFromBasket($gSession['basketref'], $projectRefList);
							}
						}

						// trigger the data event
						if ($isTempOrder == 1)
						{
							DataExportObj::EventTrigger(TPX_TRIGGER_TEMP_ORDER_CREATED, 'ORDER', $orderHeaderRecordID, $orderHeaderRecordID);
						}
						else
						{
							DataExportObj::EventTrigger(TPX_TRIGGER_ORDER_CREATED, 'ORDER', $orderHeaderRecordID, $orderHeaderRecordID);
						}

						// Trigger order paid when payment received.
						if ($paymentReceived)
						{
							// Generate the trigger.
							DataExportObj::EventTrigger(TPX_TRIGGER_ORDER_PAID, 'ORDER', $orderHeaderRecordID, $orderHeaderRecordID);
						}

						// we only send a confirmation email if the shopping cart is taopix web
						if ($gSession['order']['shoppingcarttype'] == TPX_SHOPPINGCARTTYPE_INTERNAL)
						{
							if ($gSession['order']['voucheractive'] == 1)
							{
								DatabaseObj::setVoucherOrderID($gSession['order']['vouchercode'], $orderHeaderRecordID, $gSession['userid']);
							}

							$formattedOrderTotal = UtilsObj::formatCurrencyNumber($gSession['order']['ordertotal'],
											$gSession['order']['currencydecimalplaces'], $gSession['browserlanguagecode'],
											$gSession['order']['currencysymbol'], $gSession['order']['currencysymbolatfront']);
							$formattedOrderGiftCardTotal = UtilsObj::formatCurrencyNumber($gSession['order']['ordergiftcardtotal'],
											$gSession['order']['currencydecimalplaces'], $gSession['browserlanguagecode'],
											$gSession['order']['currencysymbol'], $gSession['order']['currencysymbolatfront']);
							$formattedOrderTotalToPay = UtilsObj::formatCurrencyNumber($gSession['order']['ordertotaltopay'],
											$gSession['order']['currencydecimalplaces'], $gSession['browserlanguagecode'],
											$gSession['order']['currencysymbol'], $gSession['order']['currencysymbolatfront']);


							// if the order was originally a temp order use a different email template
							if ($wasTempOrder == 1)
							{
								$emailTemplate = 'customer_orderconfirmation2';
							}
							else
							{
								// if the order is a temp order use a different email template
								if ($isTempOrder == 1)
								{
									if ($orderSource == TPX_SOURCE_DESKTOP)
									{
										$emailTemplate = 'customer_paylater';
									}
									else
									{
										$emailTemplate = 'customer_paylateronline';
									}
								}
								else
								{
									if ($gSession['shipping'][0]['collectfromstore'])
									{
										if ($gSession['order']['isreorder'] == 1)
										{
											$emailTemplate = 'customer_orderconfirmation3_reorder';
										}
										else
										{
											$emailTemplate = 'customer_orderconfirmation3';
										}
									}
									else
									{
										if ($gSession['order']['isreorder'] == 1)
										{
											$emailTemplate = 'customer_orderconfirmation_reorder';
										}
										else
										{
											$emailTemplate = 'customer_orderconfirmation';
										}
									}
								}
							}

							if ($sendNotification == true)
							{
								self::generateOrderEmailContent($orderHeaderRecordID, $emailTemplate);
							}

							// check if production email should be sent
							if (($gSession['webbrandcode'] != '') && ($webBrandEmailSettingsArray['usedefaultemailsettings'] == 0))
							{
								if (($webBrandEmailSettingsArray['smtpproductionactive'] == 0) || ($webBrandEmailSettingsArray['smtpproductionname'] == ''))
								{
									$sendNotification = false;
								}
							}
							else
							{
								if (($brandingDefaults['smtpproductionactive'] == 0) || ($brandingDefaults['smtpproductionname'] == ''))
								{
									$sendNotification = false;
								}
							}

							// for production emails
							if ($sendNotification == true)
							{
								// if the order was a temporary order that has been uploaded we must notify production
								if (($wasTempOrder == 1) && ($requiresUpload == 0))
								{
									$jobTicketArray = DatabaseObj::getJobTicket($orderItemRecordID, $gConstants['defaultlanguagecode']);
									$webBrandArray = AuthenticateObj::getWebBrandData($jobTicketArray['webbrandcode']);

									$shippingAddress = UtilsAddressObj::formatAddress($jobTicketArray, 'shipping', "\n");
									$billingAddress = UtilsAddressObj::formatAddress($jobTicketArray, 'billing', "\n");

									$userAccount = DatabaseObj::getUserAccountFromID($jobTicketArray['userid']);
									$loginName = $userAccount['login'];

									// Check to see if ms is enabled.
									if ($gConstants['optionms'])
									{
										$siteResultArray = DatabaseObj::getSiteFromCode($siteCode);

										// if there is a brand check to see if the brand is using its own email settings.
										if (($webBrandEmailSettingsArray['usedefaultemailsettings'] == 0) && ($webBrandEmailSettingsArray['isactive'] == 1))
										{
											$smtpProductionName = '';
											$smtpProductionAddress = '';

											// check to see if the brand email settings have a production email name and address to use.
											if (($webBrandEmailSettingsArray['smtpproductionname'] != '') && ($webBrandEmailSettingsArray['smtpproductionaddress'] != ''))
											{
												$smtpProductionName = $webBrandEmailSettingsArray['smtpproductionname'];
												$smtpProductionAddress = $webBrandEmailSettingsArray['smtpproductionaddress'];
											}

											// check to see if the site email settings have a production email name and address to use.
											if (($siteResultArray['smtpproductionname'] != '') && ($siteResultArray['smtpproductionaddress'] != ''))
											{
												// also use the sites production name and address.
												if ($smtpProductionName != '')
												{
													$smtpProductionName .= ";";
													$smtpProductionAddress .= ";";
												}

												$smtpProductionName .= $siteResultArray['smtpproductionname'];
												$smtpProductionAddress .= $siteResultArray['smtpproductionaddress'];
											}
										}
										else
										{
											// if no brand just use the sites production name and email address
											$smtpProductionName = $siteResultArray['smtpproductionname'];
											$smtpProductionAddress = $siteResultArray['smtpproductionaddress'];
										}

										// check to see if the email settings have a production email name and address to use.
										if (($smtpProductionName == '') || ($smtpProductionAddress == ''))
										{
											// if no production name and email address use the constants production name and email address
											$smtpProductionName = $brandingDefaults['smtpproductionname'];
											$smtpProductionAddress = $brandingDefaults['smtpproductionaddress'];
										}
									}
									else
									{
										// if no ms
										// check to see if the brand is using its own email settings
										if (($webBrandEmailSettingsArray['usedefaultemailsettings'] == 0) && ($webBrandEmailSettingsArray['isactive'] == 1))
										{
											// check to see if the production name and email address is actaully available.
											if (($webBrandEmailSettingsArray['smtpproductionname'] != '') && ($webBrandEmailSettingsArray['smtpproductionaddress'] != ''))
											{
												$smtpProductionName = $webBrandEmailSettingsArray['smtpproductionname'];
												$smtpProductionAddress = $webBrandEmailSettingsArray['smtpproductionaddress'];
											}
											else
											{
												// if no production name and email address use the constants production name and email address
												$smtpProductionName = $brandingDefaults['smtpproductionname'];
												$smtpProductionAddress = $brandingDefaults['smtpproductionaddress'];
											}
										}
										else
										{
											// if no brand set just use the contants production name and email address
											$smtpProductionName = $brandingDefaults['smtpproductionname'];
											$smtpProductionAddress = $brandingDefaults['smtpproductionaddress'];
										}
									}

									$paymentMethodTextForEmail = $jobTicketArray['paymentmethodname'];
									if (($jobTicketArray['paymentmethodcode'] == 'CARD') &&
									   ($jobTicketArray['paymentgatewaycode'] != ''))
									{
										$paymentMethodTextForEmail = $jobTicketArray['paymentgatewaycode'];
									}

									$emailObj = new TaopixMailer();
									$emailObj->sendTemplateEmail('admin_orderuploaded', $webBrandArray['webbrandcode'],
											$webBrandArray['webbrandapplicationname'], $webBrandArray['webbranddisplayurl'],
											$gConstants['defaultlanguagecode'], $smtpProductionName, $smtpProductionAddress, '', '', 0,
											Array(
										'itemnumber' => $jobTicketArray['itemnumber'],
										'orderid' => $jobTicketArray['orderid'],
										'orderitemid' => $jobTicketArray['recordid'],
										'userid' => $jobTicketArray['userid'],
										'loginname' => $loginName,
										'currencycode' => $jobTicketArray['currencycode'],
										'currencyname' => $jobTicketArray['currencyname'],
										'ordernumber' => $jobTicketArray['ordernumber'],
										'qty' => $jobTicketArray['qty'],
										'pagecount' => $jobTicketArray['pagecount'],
										'productcode' => $jobTicketArray['productcode'],
										'productname' => LocalizationObj::getLocaleString($jobTicketArray['productname'],
												$gSession['browserlanguagecode'], true),
										'defaultcovercode' => $jobTicketArray['defaultcovercode'],
										'defaultpapercode' => $jobTicketArray['defaultpapercode'],
										'defaultpagecount' => $jobTicketArray['defaultpagecount'],
										'covercode' => $jobTicketArray['covercode'],
										'covername' => $jobTicketArray['covername'],
										'papercode' => $jobTicketArray['papercode'],
										'papername' => $jobTicketArray['papername'],
										'vouchercode' => $jobTicketArray['vouchercode'],
										'vouchername' => $jobTicketArray['vouchername'],
										'ordertotal' => $jobTicketArray['ordertotal'],
										'ordergiftcardtotal' => $jobTicketArray['ordergiftcardtotal'],
										'ordertotaltopay' => $jobTicketArray['ordertotaltopay'],
										'formattedordertotal' => $jobTicketArray['formattedordertotal'],
										'formattedordergiftcardtotal' => $jobTicketArray['formattedordergiftcardtotal'],
										'formattedordertotaltopay' => $jobTicketArray['formattedordertotaltopay'],
										'shippingcontactname' => $jobTicketArray['shippingcontactfirstname'] . ' ' . $jobTicketArray['shippingcontactlastname'],
										'shippingcontactfirstname' => $jobTicketArray['shippingcontactfirstname'],
										'shippingcontactlastname' => $jobTicketArray['shippingcontactlastname'],
										'shippingaddress' => $shippingAddress,
										'shippingmethodname' => $jobTicketArray['shippingmethodname'],
										// leave 'shippingmethod' in in order not to break existing templates,
										// but really it should be 'shippingmethodname'
										'shippingmethod' => $jobTicketArray['shippingmethodname'],
										'shippingqty' => $jobTicketArray['shippingqty'],
										'shippingcustomername' => $jobTicketArray['shippingcustomername'],
										'shippingcustomeraddress1' => $jobTicketArray['shippingcustomeraddress1'],
										'shippingcustomeraddress2' => $jobTicketArray['shippingcustomeraddress2'],
										'shippingcustomeraddress3' => $jobTicketArray['shippingcustomeraddress3'],
										'shippingcustomeraddress4' => $jobTicketArray['shippingcustomeraddress4'],
										'shippingcustomercity' => $jobTicketArray['shippingcustomercity'],
										'shippingcustomercounty' => $jobTicketArray['shippingcustomercounty'],
										'shippingcustomerstate' => $jobTicketArray['shippingcustomerstate'],
										'shippingcustomerregioncode' => $jobTicketArray['shippingcustomerregioncode'],
										'shippingcustomerregion' => $jobTicketArray['shippingcustomerregion'],
										'shippingcustomerpostcode' => $jobTicketArray['shippingcustomerpostcode'],
										'shippingcustomercountrycode' => $jobTicketArray['shippingcustomercountrycode'],
										'shippingcustomercountryname' => $jobTicketArray['shippingcustomercountryname'],
										'shippingcustomertelephonenumber' => $jobTicketArray['shippingcustomertelephonenumber'],
										'shippingcustomeremailaddress' => $jobTicketArray['shippingcustomeremailaddress'],
										'shippingmethodcode' => $jobTicketArray['shippingmethodcode'],
										'shippingratecode' => $jobTicketArray['shippingratecode'],
										'shippingrateinfo' => $jobTicketArray['shippingrateinfo'],
										'shippingratecost' => $jobTicketArray['shippingratecost'],
										'shippingratesell' => $jobTicketArray['shippingratesell'],
										'shippingratetaxcode' => $jobTicketArray['shippingratetaxcode'],
										'shippingratetaxname' => $jobTicketArray['shippingratetaxname'],
										'shippingratetaxrate' => $jobTicketArray['shippingratetaxrate'],
										'shippingratecalctax' => $jobTicketArray['shippingratecalctax'],
										'shippingratetaxtotal' => $jobTicketArray['shippingratetaxtotal'],
										'billingcontactname' => $jobTicketArray['billingcontactfirstname'] . ' ' . $jobTicketArray['billingcontactlastname'],
										'billingcontactfirstname' => $jobTicketArray['billingcontactfirstname'],
										'billingcontactlastname' => $jobTicketArray['billingcontactlastname'],
										'billingaddress' => $billingAddress,
										'billingcustomeraccountcode' => $jobTicketArray['billingcustomeraccountcode'],
										'billingcustomername' => $jobTicketArray['billingcustomername'],
										'billingcustomeraddress1' => $jobTicketArray['billingcustomeraddress1'],
										'billingcustomeraddress2' => $jobTicketArray['billingcustomeraddress2'],
										'billingcustomeraddress3' => $jobTicketArray['billingcustomeraddress3'],
										'billingcustomeraddress4' => $jobTicketArray['billingcustomeraddress4'],
										'billingcustomercity' => $jobTicketArray['billingcustomercity'],
										'billingcustomercounty' => $jobTicketArray['billingcustomercounty'],
										'billingcustomerstate' => $jobTicketArray['billingcustomerstate'],
										'billingcustomerregioncode' => $jobTicketArray['billingcustomerregioncode'],
										'billingcustomerregion' => $jobTicketArray['billingcustomerregion'],
										'billingcustomerpostcode' => $jobTicketArray['billingcustomerpostcode'],
										'billingcustomercountrycode' => $jobTicketArray['billingcustomercountrycode'],
										'billingcustomercountryname' => $jobTicketArray['billingcustomercountryname'],
										'billingcustomertelephonenumber' => $jobTicketArray['billingcustomertelephonenumber'],
										'billingcustomeremailaddress' => $jobTicketArray['billingcustomeremailaddress'],
										'paymentmethodname' => $paymentMethodTextForEmail,
										'targetuserid' => $jobTicketArray['userid']), '', ''
									);
								} // end of notify
							}
						}
					}
				}
				else
				{
					// Release mutex no processing needed.
					DatabaseObj::releaseDBMutex($mutexName);
				}
			}
        }
        else
        {
            $result = 'str_ErrorNoSessionRef';
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['ref'] = $gSession['ref'];

        return $resultArray;
    }

    /**
     * Inserts component data into database.
     *
     * @static
     *
     * @param array  $pOrderItemComponents      the data array
     *
     * @author Steffen Haugk
     * @since Version 3.0.0
     */
    static function insertOrderItemComponents($pParentID, &$pSortOrder, $pComponentList, $pOrderHeaderRecordID, $pOrderItemRecordID,
            $pUserid, $pItemQty, $pDbObj, $pOrderItemComponentType, $pComponentIsSinglePrintSubComponent, $pAbandonOnError)
    {
        $resultArray = array('result' => '', 'resultparam' => '', 'dberrorcode' => 0);
        $item = array();
        $itemSelected = 0;

       	// prepare the statement outside the loop so we are not doing this multiple times
		if ($stmt = $pDbObj->prepare('INSERT INTO `ORDERITEMCOMPONENTS` (`id`, `datecreated`, `orderid`, `orderitemid`, `userid`, `parentcomponentid`, `externalassetid`, `externalassetservicecode`, `externalassetservicename`,
                        `externalassetpricetype`, `componentcode`, `componentlocalcode`, `skucode`, `componentdefaultcode`, `componentname`, `componentpath`,
                        `componentcategorycode`, `componentcategoryname`, `componentdescription`, `componentinfo`, `componentpriceinfo` ,`sortorder`, `pricingmodel`, `islist`, `checkboxselected`, `componentselectioncount`,
                        `quantity`, `componentunitcost`, `componentunitsell`, `externalassetunitcost`, `externalassetunitsell`, `componentunitweight`,
                        `componenttotalcost`, `subtotal`, `componenttotalsell`, `componenttotalweight`, `componenttotaltax`, `discountvalue`, `componentdiscountedtax`,
                        `branchunitcost`, `branchunitsell`, `branchunitweight`, `branchtotalcost`, `branchtotalsell`, `branchtotalweight`, `branchtotaltax`, `externalassetexpirydate`,
                        `externalassetpageref`, `externalassetpagenumber`, `externalassetpagename`, `externalassetboxref`,`componenttaxname`,`componenttaxrate`, `setid`, `setname`)
                    VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
		{
			$hasPicturesLookup = false;

			if (array_key_exists('key', $pComponentList))
			{
				$componentList = $pComponentList['key'];
				$hasPicturesLookup = true;
			}
			else
			{
				$componentList = $pComponentList;
			}

			foreach ($componentList as $theComponentKey => $theItem)
			{
				// Need to maintain compatablility with new session structure and the old format.
				if ($hasPicturesLookup)
				{
					// Build the component data from the lookup table
					$lookUpKey = $theItem;
					$uniqueLookup = $lookUpKey . TPX_PICTURES_LOOKUP_SEPERATOR . $theComponentKey;
					$printData = $pComponentList['printdata'][$uniqueLookup];
					$pictureName = $pComponentList['pname'][$printData['fn']];

					$componentData = $pComponentList['data'][$lookUpKey];
					$componentData['pagename'] = $pictureName;
					$componentData['picturename'] = $pictureName;

					// Rebuild the asset data the full key name to reinsert.
					$asset = array(
						'aid' => '', // asset id
						'asc' => '', // asset service code
						'asn' => '', // asset service name
						'apt' => 0, // asset price type
						'ac' => 0.00, // asset cost
						'as' => 0.00 // asset sell
					);

					// Add the external asset data from the lookup.
					if (array_key_exists($uniqueLookup, $pComponentList['asset']))
					{
						$asset = $pComponentList['asset'][$uniqueLookup];
					}

					// Asset data with full keynames.
					$assetFullFormat = array(
						'assetid' => $asset['aid'],
						'assetservicecode' => $asset['asc'],
						'assetservicename' => $asset['asn'],
						'assetpricetype' => $asset['apt'],
						'assetcost' => $asset['ac'],
						'assetsell' => $asset['as']
					);

					$theComponent = array_merge($componentData, $printData, $assetFullFormat);
				}
				else
				{
					$theComponent = $theItem;
				}

				$insertComponent = true;
				$pSortOrder++;

				switch ($pOrderItemComponentType)
				{
					case TPX_ORDERITEMCOMPONENTARRAYTYPE_AI:
						$item = self::createAIComponentDataForInsertion($theComponent);
					break;
                    case TPX_ORDERITEMCOMPONENTARRAYTYPE_CALENDAR:
                        $item['componentdefaultcode'] = '';
                        $item['componentdescription'] = '';
                        $item['componentselectioncount'] = 1;
                        $item['servicecode'] = '';
                        $item['servicename'] = '';
                        $item['skucode'] = $theComponent['skucode'];
                        $item['externalassetid'] = '';
                        $item['assetpricetype'] = 0;
                        $item['assetunitcost'] = 0.00;
                        $item['assetunitsell'] = 0.00;
                        $item['assetexpirydate'] = '';
                        $item['assetpageref'] = 0;
                        $item['assetpagenumber'] = 0;
                        $item['assetpagename'] = '';
                        $item['assetboxref'] = '';
                        $item['componentcategoryname'] = '';
                        $item['componentcode'] = $theComponent['componentcode'];
                        $explodeArray = explode(".", $theComponent['componentcode']);
                        $item['componentlocalcode'] = end($explodeArray);
                        $item['componentname'] = $theComponent['componentname'];
                        $item['itemcomponentinfo'] = $theComponent['info'];
                        $item['itemcomponentpriceinfo'] = $theComponent['priceinfo'];
                        $item['componentpath'] = $theComponent['path'];
                        $item['componentcategorycode'] = $theComponent['componentcategory'];
                        $item['pricingmodel'] = $theComponent['pricingmodel'];
                        $item['islist'] = $theComponent['islist'];
                        $item['componentunitcost'] = $theComponent['unitcost'];
                        $item['componentunitsell'] = $theComponent['unitsell'];
                        $item['componentunitweight'] = $theComponent['unitweight'];
                        $item['componenttotalcost'] = $theComponent['totalcost'];
                        $item['componenttotalsell'] = $theComponent['totalsell'];
                        $item['subtotal'] = $theComponent['subtotal'];
                        $item['componenttotalweight'] = $theComponent['totalweight'];
                        $item['componenttotaltax'] = $theComponent['totaltax'];
                        $item['orderfootertaxname'] = $theComponent['orderfootertaxname'];
                        $item['orderfootertaxrate'] = $theComponent['orderfootertaxrate'];
                        $item['discountvalue'] = $theComponent['discountvalue'];
                        $item['discountedtax'] = $theComponent['discountedtax'];
                        $item['metadata'] = $theComponent['metadata'];
                        $item['itemqty'] = $theComponent['componentqty'];
                        $item['setid'] = 0;
                        $item['setname'] = '';

                        break;
					case TPX_ORDERITEMCOMPONENTARRAYTYPE_COMPONENT:
						if (array_key_exists('defaultcode', $theComponent))
						{
							$item['componentdefaultcode'] = $theComponent['defaultcode'];
							$item['componentdescription'] = $theComponent['sectionlabel'];
							$item['componentselectioncount'] = $theComponent['count'];
						}
						else
						{
							$item['componentdefaultcode'] = '';
							$item['componentdescription'] = '';
							$item['componentselectioncount'] = 1;
						}

						$item['servicecode'] = '';
						$item['servicename'] = '';
						$item['skucode'] = $theComponent['skucode'];
						$item['externalassetid'] = '';
						$item['assetpricetype'] = 0;
						$item['assetunitcost'] = 0.00;
						$item['assetunitsell'] = 0.00;
						$item['assetexpirydate'] = '';
						$item['assetpageref'] = 0;
						$item['assetpagenumber'] = 0;
						$item['assetpagename'] = '';
						$item['assetboxref'] = '';
						$item['componentcategoryname'] = $theComponent['categoryname'];
						$item['componentcode'] = $theComponent['code'];
						$explodeArray = explode(".", $theComponent['code']);
						$item['componentlocalcode'] = end($explodeArray);
						$item['componentname'] = $theComponent['name'];
						$item['itemcomponentinfo'] = $theComponent['info'];
						$item['itemcomponentpriceinfo'] = $theComponent['priceinfo'];
						$item['componentpath'] = $theComponent['path'];
						$item['componentcategorycode'] = $theComponent['categorycode'];
						$item['pricingmodel'] = $theComponent['pricingmodel'];
						$item['islist'] = $theComponent['islist'];
						$item['componentunitcost'] = $theComponent['unitcost'];
						$item['componentunitsell'] = $theComponent['unitsell'];
						$item['componentunitweight'] = $theComponent['unitweight'];
						$item['componenttotalcost'] = $theComponent['totalcost'];
						$item['componenttotalsell'] = $theComponent['totalsell'];
						$item['subtotal'] = $theComponent['subtotal'];
						$item['componenttotalweight'] = $theComponent['totalweight'];
						$item['componenttotaltax'] = $theComponent['totaltax'];
						$item['orderfootertaxname'] = $theComponent['orderfootertaxname'];
						$item['orderfootertaxrate'] = $theComponent['orderfootertaxrate'];
						$item['discountvalue'] = $theComponent['discountvalue'];
						$item['discountedtax'] = $theComponent['discountedtax'];
						$item['itemqty'] = $theComponent['quantity'];
						$item['metadata'] = $theComponent['metadata'];
						$item['setid'] = 0;
						$item['setname'] = '';

						if (array_key_exists('checked', $theComponent))
						{
							$itemSelected = $theComponent['checked'];

							// we need to check to see if the compoment is a checkbox and that it has been selected.
							// if it has not been selected then we do not want to insert it into the database.
							if (($itemSelected == 0) && ($theComponent['storewhennotselected'] == 0))
							{
								$insertComponent = false;
							}
						}
						break;
					case TPX_ORDERITEMCOMPONENTARRAYTYPE_EXTERNALASSET:
						$item['servicecode'] = $theComponent['servicecode'];
						$item['servicename'] = $theComponent['servicename'];
						$item['skucode'] = '';
						$item['externalassetid'] = $theComponent['id'];
						$item['assetpricetype'] = $theComponent['pricetype'];
						$item['assetunitcost'] = $theComponent['assetunitcost'];
						$item['assetunitsell'] = $theComponent['assetunitsell'];
						$item['assetexpirydate'] = $theComponent['expirationdate'];
						$item['assetpageref'] = $theComponent['pageref'];
						$item['assetpagenumber'] = $theComponent['pagenumber'];
						$item['assetpagename'] = $theComponent['pagename'];
						$item['assetboxref'] = $theComponent['boxref'];
						$item['componentcode'] = TPX_ASSETTYPE_COMPONENTCODE;
						$item['componentlocalcode'] = TPX_ASSETTYPE_COMPONENTCODE;
						$item['componentdefaultcode'] = '';
						$item['componentname'] = $theComponent['name'];
						$item['itemcomponentinfo'] = '';
						$item['itemcomponentpriceinfo'] = '';
						$item['componentpath'] = '';
						$item['componentcategorycode'] = TPX_ASSETTYPE_COMPONENTCATEGORY;
						$item['componentcategoryname'] = '';
						$item['componentdescription'] = '';
						$item['pricingmodel'] = TPX_PRICINGMODEL_PERQTY;
						$item['islist'] = 0;
						$item['componentselectioncount'] = 0;
						$item['itemqty'] = 1;
						$item['componentunitcost'] = $theComponent['assetunitcost'];
						$item['componentunitsell'] = $theComponent['totalsell'];
						$item['subtotal'] = 0.00;
						$item['componentunitweight'] = 0.00;
						$item['componenttotalcost'] = $theComponent['totalcost'];
						$item['componenttotalsell'] = $theComponent['totalsell'];
						$item['componenttotalweight'] = 0.00;
						$item['componenttotaltax'] = $theComponent['totaltax'];
						$item['orderfootertaxname'] = '';
						$item['orderfootertaxrate'] = 0.00;
						$item['discountvalue'] = 0.00;
						$item['discountedtax'] = 0.00;
						$item['metadata'] = Array();
						$item['setid'] = 0;
						$item['setname'] = '';
						break;
					case TPX_ORDERITEMCOMPONENTARRAYTYPE_SINGLEPRINT:

						if ($pComponentIsSinglePrintSubComponent)
						{
							$componentCode = $theComponent['subcode'];
							$componentCategory = $theComponent['subcategory'];
							$componentName = $theComponent['subname'];
							$componentPath = '$SINGLEPRINT\\' . $theComponent['code'] . '\\$SINGLEPRINTOPTION\\';
							$assetServiceCode = '';
							$assetServiceName = '';
							$skuCode = $theComponent['subskucode'];
							$externalAssetID = '';
							$assetPriceType = 0;
							$assetUnitCost = 0.00;
							$assetUnitSell = 0.00;
							$subComponentArrayKeyPrefix = 'sub';
						}
						else
						{
							$componentCode = $theComponent['code'];
							$componentCategory = $theComponent['category'];
							$componentName = $theComponent['name'];
							$componentPath = '$SINGLEPRINT\\';
							$assetServiceCode = $theComponent['assetservicecode'];
							$assetServiceName = $theComponent['assetservicename'];
							$skuCode = $theComponent['skucode'];
							$externalAssetID = $theComponent['assetid'];
							$assetPriceType = $theComponent['assetpricetype'];
							$assetUnitCost = $theComponent['assetcost'];
							$assetUnitSell = $theComponent['assetsell'];
							$subComponentArrayKeyPrefix = '';
						}

						$item['servicecode'] = $assetServiceCode;
						$item['servicename'] = $assetServiceName;
						$item['skucode'] = $skuCode;
						$item['externalassetid'] = $externalAssetID;
						$item['assetpricetype'] = $assetPriceType;
						$item['assetunitcost'] = $assetUnitCost;
						$item['assetunitsell'] = $assetUnitSell;
						$item['assetexpirydate'] = '';
						$item['assetpageref'] = $theComponent['pageref'];
						$item['assetpagenumber'] = $theComponent['pagenumber'];
						$item['assetpagename'] = $theComponent['picturename'];
						$item['assetboxref'] = $theComponent['boxref'];
						$item['componentcode'] = $componentCategory . '.' . $componentCode;
						$item['componentlocalcode'] = $componentCode;
						$item['componentdefaultcode'] = '';
						$item['componentname'] = $componentName;
						$item['itemcomponentinfo'] = '';
						$item['itemcomponentpriceinfo'] = '';
						$item['componentpath'] = $componentPath;
						$item['componentcategorycode'] = $componentCategory;
						$item['componentcategoryname'] = '';
						$item['componentdescription'] = '';
						$item['pricingmodel'] = TPX_PRICINGMODEL_PERPRODCMPQTY;
						$item['islist'] = 1;
						$item['componentselectioncount'] = 0;
						$item['itemqty'] = $theComponent['qty'];
						$item['componentunitcost'] = $theComponent[$subComponentArrayKeyPrefix . 'unitcost'];
						$item['componentunitsell'] = $theComponent[$subComponentArrayKeyPrefix . 'us'];
						$item['componentunitweight'] = $theComponent[$subComponentArrayKeyPrefix . 'unitweight'];
						$item['componenttotalcost'] = $theComponent[$subComponentArrayKeyPrefix . 'tc'];
						$item['componenttotalsell'] = $theComponent[$subComponentArrayKeyPrefix . 'ts'];
						$item['subtotal'] = 0.00;
						$item['componenttotalweight'] = $theComponent[$subComponentArrayKeyPrefix . 'tw'];
						$item['componenttotaltax'] = $theComponent[$subComponentArrayKeyPrefix . 'tt'];
						$item['orderfootertaxname'] = '';
						$item['orderfootertaxrate'] = 0.00;
						$item['discountvalue'] = 0.00;
						$item['discountedtax'] = 0.00;
						$item['metadata'] = Array();
						$item['setid'] = $theComponent['setid'];
						$item['setname'] = $theComponent['setname'];
						break;
				}

				if ($insertComponent)
				{
					if ($stmt->bind_param('iiiisss' . 'issssssss' . 'sssiiiiiid' . 'ddddddddddd' . 'dddddd' . 'dssssssdis', $pOrderHeaderRecordID,
									$pOrderItemRecordID, $pUserid, $pParentID, $item['externalassetid'], $item['servicecode'], $item['servicename'],
									$item['assetpricetype'], $item['componentcode'], $item['componentlocalcode'], $item['skucode'],
									$item['componentdefaultcode'], $item['componentname'], $item['componentpath'],
									$item['componentcategorycode'], $item['componentcategoryname'], $item['componentdescription'],
									$item['itemcomponentinfo'], $item['itemcomponentpriceinfo'], $pSortOrder, $item['pricingmodel'],
									$item['islist'], $itemSelected, $item['componentselectioncount'], $item['itemqty'],
									$item['componentunitcost'], $item['componentunitsell'], $item['assetunitcost'], $item['assetunitsell'],
									$item['componentunitweight'], $item['componenttotalcost'], $item['subtotal'], $item['componenttotalsell'],
									$item['componenttotalweight'], $item['componenttotaltax'], $item['discountvalue'], $item['discountedtax'],
									$item['componentunitcost'], $item['componentunitsell'], $item['componentunitweight'],
									$item['componenttotalcost'], $item['componenttotalsell'], $item['componenttotalweight'],
									$item['componenttotaltax'], $item['assetexpirydate'], $item['assetpageref'], $item['assetpagenumber'],
									$item['assetpagename'], $item['assetboxref'], $item['orderfootertaxname'], $item['orderfootertaxrate'], $item['setid'], $item['setname']))
					{
						if ($stmt->execute())
						{
							$recordID = $pDbObj->insert_id;
							$metaDataRefList = '';

							if (($item['islist']) || ((!$item['islist']) && $itemSelected))
							{
								$metaDataResult = MetaDataObj::storeMetaData($pOrderHeaderRecordID, $pOrderItemRecordID, $recordID, $pUserid,
												'COMPONENT', $item['metadata']);
								$resultArray['result'] = $metaDataResult['result'];
								$resultArray['resultparam'] = $metaDataResult['resultparam'];

								if ($resultArray['result'] == '')
								{
									$metaDataRefList = $metaDataResult['reflist'];
								}

								// if there is metadata update metadata
								if ($metaDataRefList != '')
								{
									if ($stmt2 = $pDbObj->prepare('UPDATE `ORDERITEMCOMPONENTS` SET `metadatacodelist` = ? WHERE `id` = ?'))
									{
										if ($stmt2->bind_param('si', $metaDataRefList, $recordID))
										{
											if (! $stmt2->execute())
											{
												$resultArray['result'] = 'str_DatabaseError';
												$resultArray['resultparam'] = 'insertOrderItemComponents metadata execute ' . $pDbObj->error;
												$resultArray['deberrorcode'] = $pDbObj->error;
											}
										}
										else
										{
											$resultArray['result'] = 'str_DatabaseError';
											$resultArray['resultparam'] = 'insertOrderItemComponents metadata bindparam ' . $pDbObj->error;
											$resultArray['deberrorcode'] = $pDbObj->error;
										}

										$stmt2->free_result();
										$stmt2->close();
									}
									else
									{
										$resultArray['result'] = 'str_DatabaseError';
										$resultArray['resultparam'] = 'insertOrderItemComponents metadata prepare ' . $pDbObj->error;
										$resultArray['deberrorcode'] = $pDbObj->error;
									}
								}
							}
						}
						else
						{
							// could not execute statement
							$resultArray['result'] = 'str_DatabaseError';
							$resultArray['resultparam'] = 'insertOrderItemComponents execute ' . $pDbObj->error;
							$resultArray['deberrorcode'] = $pDbObj->error;
						}
					}
					else
					{
						// could not bind parameters
						$resultArray['result'] = 'str_DatabaseError';
						$resultArray['resultparam'] = 'insertOrderItemComponents bind ' . $pDbObj->error;
						$resultArray['deberrorcode'] = $pDbObj->error;
					}

					$stmt->free_result();
				}

				if ($resultArray['result'] == '')
				{
					// add sub-components if present
					if ((array_key_exists('subsections', $theComponent)) && (!empty($theComponent['subsections'])))
					{
						$resultArray = self::insertOrderItemComponents($recordID, $pSortOrder, $theComponent['subsections'], $pOrderHeaderRecordID,
								$pOrderItemRecordID, $pUserid, $pItemQty, $pDbObj, $pOrderItemComponentType, false, $pAbandonOnError);
					}

					if (($resultArray['result'] == '') || (! $pAbandonOnError))
					{
						// add checkboxes if present
						if ((array_key_exists('checkboxes', $theComponent)) && (!empty($theComponent['checkboxes'])))
						{
							$resultArray = self::insertOrderItemComponents($recordID, $pSortOrder, $theComponent['checkboxes'], $pOrderHeaderRecordID,
									$pOrderItemRecordID, $pUserid, $pItemQty, $pDbObj, $pOrderItemComponentType, false, $pAbandonOnError);
						}

						if (($resultArray['result'] == '') || (! $pAbandonOnError))
						{
							// add singleprint subcomponents
							if (($pOrderItemComponentType == TPX_ORDERITEMCOMPONENTARRAYTYPE_SINGLEPRINT) && ($theComponent['subcode'] != '') && (!$pComponentIsSinglePrintSubComponent))
							{
								$subComponentArray = array();
								$subComponentArray[] = $theComponent;

								$resultArray = self::insertOrderItemComponents($recordID, $pSortOrder, $subComponentArray, $pOrderHeaderRecordID,
										$pOrderItemRecordID, $pUserid, $pItemQty, $pDbObj, $pOrderItemComponentType, true, $pAbandonOnError);
							}
						}
					}
				}
			}

            $stmt->close();
            $stmt = null;
        }
        else
		{
			// could not prepare statement
			$resultArray['result'] = 'str_DatabaseError';
			$resultArray['resultparam'] = 'insertOrderItemComponents prepare ' . $pDbObj->error;
			$resultArray['deberrorcode'] = $pDbObj->error;
		}

        return $resultArray;
    }

    /**
     * Updates fields of a section.
     *
     * Function is used for both sections and sub-sections.
     *
     * @static
     *
     * @param integer   $pItemProductCode       product code    unless ORDERFOOTER
     * @param integer   $pItemQty               quantity        unless ORDERFOOTER
     * @param integer   $pItemPageCount         page count      unless ORDERFOOTER
     * @param array     $pSection               the actual section, passed by reference
     * @param array     $pComponentsFromOriginalOrder
     * @param integer   $pOrderItemId           id in session for the items
     * @param integer   $pSectionCount          id of the component in session
     * @param boolean   $pIsSubsection          indicate if is a subsection
     *
     * @author Steffen Haugk
     * @since Version 3.0.0
     */
    static function updateSection($pItemProductCode, $pItemQty, $pItemPageCount, &$pSection, $pComponentsFromOriginalOrder, $pOrderItemId,
            $pSectionIndex, $pSubSectionIndex, $pIsSubsection)
    {
        global $gSession;
        $itemCount = 0;
        $sectionPath = $pSection['path'];
        $categoryCode = $pSection['categorycode'];

        $parsedPath = DatabaseObj::parseComponentOrSectionPath($sectionPath);
        $origSectionCode = $parsedPath[0]['code'];
        $sectionCode = $origSectionCode;
        $hit = false;
        $index = 0;

        switch ($origSectionCode)
        {
            case 'ORDERFOOTER':
                array_shift($parsedPath);
                if ($pIsSubsection == false)
                {
                    $origSectionArray = $gSession['order']['orderFooterSections'][$pSectionIndex];
                    $sectionCode = $parsedPath[0]['code'];
                }
                else
                {
                    $origSectionArray = $gSession['order']['orderFooterSections'][$pSectionIndex]['subsections'][$pSubSectionIndex];
                    $sectionCode = $parsedPath[2]['code'];
                }
                break;
            case 'LINEFOOTER':
                array_shift($parsedPath);
                if ($pIsSubsection == false)
                {
                    $origSectionArray = $gSession['items'][$pOrderItemId]['lineFooterSections'][$pSectionIndex];
                    $sectionCode = $parsedPath[0]['code'];
                }
                else
                {
                    $origSectionArray = $gSession['items'][$pOrderItemId]['lineFooterSections'][$pSectionIndex]['subsections'][$pSubSectionIndex];
                    $sectionCode = $parsedPath[2]['code'];
                }
                break;
            default:
                if ($pIsSubsection == false)
                {
                    $origSectionArray = $gSession['items'][$pOrderItemId]['sections'][$pSectionIndex];
                }
                else
                {
                    $origSectionArray = $gSession['items'][$pOrderItemId]['sections'][$pSectionIndex]['subsections'][$pSubSectionIndex];
                    $sectionCode = $parsedPath[2]['code'];
                }
        }

        if ($pSection['defaultcode'] != '')
        {
            $componentExists = false;
            $sectionArray = DatabaseObj::getComponentsInOrderSectionByCategory($sectionPath, $categoryCode,
                            $gSession['userdata']['companycode'], $pItemProductCode, $gSession['licensekeydata']['groupcode'],
                            $gSession['order']['currencyexchangerate'], $gSession['order']['currencydecimalplaces'], $pItemQty,
                            $pItemPageCount, -1, '', false, true);
            $componentList = $sectionArray['component'];
            $itemCount = count($componentList);

            // we need to check to see if the component still has a price.
            if ($sectionArray['result'] != '')
            {
                $pSection['componenthasprice'] = 0;
            }
            else
            {
                $pSection['componenthasprice'] = 1;
            }

            // check if we have a component code assigned
            if ($pSection['code'] != '')
            {
                // we have a component but have to make sure it is still available
                for ($k = 0; $k < $itemCount; $k++)
                {
                    if ($componentList[$k]['code'] == $pSection['code'])
                    {
                        $pSection['id'] = $componentList[$k]['id'];
                        $pSection['code'] = $componentList[$k]['code'];
                        $pSection['localcode'] = $componentList[$k]['localcode'];
                        $pSection['skucode'] = $componentList[$k]['skucode'];
                        $pSection['name'] = $componentList[$k]['name'];
                        $pSection['info'] = $componentList[$k]['info'];
                        $pSection['moreinfolinkurl'] = $componentList[$k]['moreinfolinkurl'];
                        $pSection['moreinfolinktext'] = $componentList[$k]['moreinfolinktext'];
                        $pSection['priceinfo'] = $componentList[$k]['priceinfo'];
                        $pSection['unitcost'] = $componentList[$k]['unitcost'];
                        $pSection['unitweight'] = $componentList[$k]['unitweight'];
                        $pSection['orderfooterusesproductquantity'] = $componentList[$k]['orderfooterusesproductquantity'];
						$pSection['orderfootertaxlevel'] = $componentList[$k]['orderfootertaxlevel'];
						$pSection['pricedata'] = $componentList[$k]['pricedata'];
                        $componentExists = true;
                        break;
                    }
                }
            }

            // if we don't have a component attempt to grab the first one
            if ($componentExists == false)
            {
                if ($itemCount > 0)
                {
                    $defaultComponentFound = false;

                    for ($j = 0; $j < $itemCount; $j++)
                    {
                        if ($componentList[$j]['default'] == 1)
                        {
                            $pSection['id'] = $componentList[$j]['id'];
                            $pSection['code'] = $componentList[$j]['code'];
                            $pSection['localcode'] = $componentList[$j]['localcode'];
                            $pSection['skucode'] = $componentList[$j]['skucode'];
                            $pSection['name'] = $componentList[$j]['name'];
                            $pSection['info'] = $componentList[$j]['info'];
							$pSection['moreinfolinkurl'] = $componentList[$j]['moreinfolinkurl'];
                        	$pSection['moreinfolinktext'] = $componentList[$j]['moreinfolinktext'];
                            $pSection['priceinfo'] = $componentList[$j]['priceinfo'];
                            $pSection['unitcost'] = $componentList[$j]['unitcost'];
                            $pSection['unitweight'] = $componentList[$j]['unitweight'];
                            $pSection['orderfooterusesproductquantity'] = $componentList[$j]['orderfooterusesproductquantity'];
                            $pSection['orderfootertaxlevel'] = $componentList[$j]['orderfootertaxlevel'];
                            $defaultComponentFound = true;
                            break;
                        }
                    }

                    if (!$defaultComponentFound)
                    {
                        $pSection['id'] = $componentList[0]['id'];
                        $pSection['code'] = $componentList[0]['code'];
                        $pSection['localcode'] = $componentList[0]['localcode'];
                        $pSection['skucode'] = $componentList[0]['skucode'];
                        $pSection['name'] = $componentList[0]['name'];
                        $pSection['info'] = $componentList[0]['info'];
                        $pSection['moreinfolinkurl'] = $componentList[0]['moreinfolinkurl'];
                        $pSection['moreinfolinktext'] = $componentList[0]['moreinfolinktext'];
                        $pSection['priceinfo'] = $componentList[0]['priceinfo'];
                        $pSection['unitcost'] = $componentList[0]['unitcost'];
                        $pSection['unitweight'] = $componentList[0]['unitweight'];
                        $pSection['orderfooterusesproductquantity'] = $componentList[0]['orderfooterusesproductquantity'];
                        $pSection['orderfootertaxlevel'] = $componentList[0]['orderfootertaxlevel'];

                        if ($pOrderItemId == TPX_ORDERFOOTER_ID)
                        {
                            $parentSection = '$ORDERFOOTER\\';
                        }
                        else
                        {
                            // we need to check if we are changing a component that belongs to a section in the LINEFOOTER section
                            $parentPathElements = explode('\\', $pSection['path']);

                            if ($parentPathElements[0] == '$LINEFOOTER')
                            {
                                $parentSection = '$LINEFOOTER\\';
                            }
                            else
                            {
                                $parentSection = '';
                            }
                        }

                        // since the component has changed we need to re-build everything that belongs to it
                        $sectionList = DatabaseObj::getOrderSectionList($pItemProductCode, $gSession['licensekeydata']['groupcode'],
                                        $gSession['userdata']['companycode'], $parentSection);

                        foreach ($sectionList['sections'] as $sectionCode)
                        {
                            $pSection = &DatabaseObj::getSessionOrderSection($pOrderItemId, $pItemProductCode,
                                            $parentSection . '$' . $sectionCode . '\\', $gSession['order']['currencyexchangerate'],
                                            $gSession['order']['currencydecimalplaces'], $pItemQty, true, $componentList[0]['code']);
                        }

                        self::updateOneOrderSection($pOrderItemId);
                    }
                }
                else
                {
                    $pSection['id'] = 0;
                    $pSection['localcode'] = '';
                    $pSection['skucode'] = '';
                    $pSection['name'] = '';
                    $pSection['info'] = '';
                    $pSection['moreinfolinkurl'] = '';
                    $pSection['moreinfolinktext'] = '';
                    $pSection['priceinfo'] = '';
                    $pSection['unitcost'] = 0.00;
                    $pSection['unitweight'] = 0.0000;
                    $pSection['orderfooterusesproductquantity'] = 0;
                    $pSection['orderfootertaxlevel'] = 1;
                }
            }
        }
        else
        {
            $pSection['code'] = '';
        }

        // get metadata for the selected component
        $componentArray = DatabaseObj::getComponentByCode($pSection['code']);

        // we need to check to see if the component does have metadata assigned. We also need to check to see if the component has a price.
        // if it does not have a price then we do not want to present the metadata to the user.
        $section['metadata'] = Array();

        if ($componentArray['keywordgroupheaderid'] > 0 && $pSection['componenthasprice'] == 1)
        {
            $section['metadata'] = MetaDataObj::getKeywordList('COMPONENT', '', '', $componentArray['keywordgroupheaderid']);
        }


        // copy the existing metadata back
        foreach ($section['metadata'] as &$metadataItem)
        {
            $componentOldMetadata = $origSectionArray['metadata'];

            foreach ($componentOldMetadata as &$oldMetadataItem)
            {
                if ($metadataItem['ref'] == $oldMetadataItem['ref'])
                {
                    $metadataItem['defaultvalue'] = $oldMetadataItem['defaultvalue'];
                    break;
                }
            }
        }

        foreach ($pSection['checkboxes'] as &$sectionCheckboxItem)
        {
            foreach ($origSectionArray['checkboxes'] as &$existingCheckboxItem)
            {
                if ($sectionCheckboxItem['code'] == $existingCheckboxItem['code'])
                {
                    $sectionCheckboxItem['checked'] = $existingCheckboxItem['checked'];

                    foreach ($sectionCheckboxItem['metadata'] as &$metadataItem)
                    {
                        foreach ($existingCheckboxItem['metadata'] as &$oldMetadataItem)
                        {
                            if ($metadataItem['ref'] == $oldMetadataItem['ref'])
                            {
                                $metadataItem['defaultvalue'] = $oldMetadataItem['defaultvalue'];
                                break;
                            }
                        }
                    }

                    break;
                }
            }
        }

        $pSection['metadata'] = $section['metadata'];
        //collect the meta on reorder
        if ($pSection['code'] != '' && !empty($pComponentsFromOriginalOrder))
        {
            //reorder product wich is not the default
            if ($pSection['code'] != $pSection['defaultcode'])
            {
                //metadata
                $componentArray = DatabaseObj::getComponentByCode($pSection['code']);
                if ($componentArray['keywordgroupheaderid'] > 0)
                {
                    $pSection['metadata'] = MetaDataObj::getKeywordList('COMPONENT', '', '', $componentArray['keywordgroupheaderid']);
                }
                else
                {
                    $pSection['metadata'] = Array();
                }
            }
            $SectionMetaData = &$pSection['metadata'];
            MetaDataObj::getMetaDataComponentValue($pComponentsFromOriginalOrder, $SectionMetaData, $pSection['path']);
            // checkboxes
            if (isset($pSection['checkboxes']))
            {
                $aCheckbox = &$pSection['checkboxes'];
                self::getOrderCheckboxes($pComponentsFromOriginalOrder, $aCheckbox);
            }
        }

        $pSection['itemqty'] = $pItemQty;
        $pSection['count'] = $itemCount;

        // if we are not a reorder we want to attempt to match the quantities slected against a component from the designer
        if ($gSession['order']['isreorder'] == 0)
        {
            foreach ($pComponentsFromOriginalOrder as $orignalItem)
            {
                if ($pSection['path'] == $orignalItem['componentpath'])
                {
                    $pSection['quantity'] = $orignalItem['quantity'];
                    break;
                }
            }
        }

        // loop around sub-sections
        foreach ($pSection['subsections'] as $subSectionIndex => &$subSection)
        {
            self::updateSection($pItemProductCode, $pItemQty, $pItemPageCount, $subSection, $pComponentsFromOriginalOrder, $pOrderItemId,
                    $pSectionIndex, $subSectionIndex, true);
        }

        // nothing to return
    }

    static function resetCheckboxAtRootOfSectionsMetaData($pItemQty, &$pCheckbox, $pOrderItemId)
    {
        global $gSession;

        $itemCount = 0;
        $checkboxPath = $pCheckbox['path'];
        $parsedPath = DatabaseObj::parseComponentOrSectionPath($checkboxPath);
        $origCheckboxCode = $parsedPath[0]['code'];
        $hit = false;
        $index = 0;

        switch ($origCheckboxCode)
        {
            case 'ORDERFOOTER':
                $origCheckboxArray = $gSession['order']['orderFooterCheckboxes'];
                break;
            case 'LINEFOOTER':
                $origCheckboxArray = $gSession['items'][$pOrderItemId]['lineFooterCheckboxes'];
                break;
            default:
                $origCheckboxArray = $gSession['items'][$pOrderItemId]['checkboxes'];
        }

        // find
        $itemCount = count($origCheckboxArray);

        for ($i = 0; $i < $itemCount; $i++)
        {
            if ($origCheckboxArray[$i]['path'] == $pCheckbox['path'])
            {
                $hit = true;
                $index = $i;
                break;
            }
        }

        if ($hit)
        {
            foreach ($origCheckboxArray as &$existingCheckboxItem)
            {
                if ($pCheckbox['code'] == $existingCheckboxItem['code'])
                {
                    $pCheckbox['checked'] = $existingCheckboxItem['checked'];

                    foreach ($pCheckbox['metadata'] as &$metadataItem)
                    {
                        foreach ($existingCheckboxItem['metadata'] as &$oldMetadataItem)
                        {
                            if ($metadataItem['ref'] == $oldMetadataItem['ref'])
                            {
                                $metadataItem['defaultvalue'] = $oldMetadataItem['defaultvalue'];
                                break;
                            }
                        }
                    }

                    break;
                }
            }
        }

        $pCheckbox['itemqty'] = $pItemQty;

        // nothing to return
    }

    /**
     * Check the checkbox for a subcomponent
     *
     * @param array $pcomponentsFromOriginalOrder
     * @param array $pSectionCheckBoxes
     */
    static function getOrderCheckboxes($pcomponentsFromOriginalOrder, &$pSectionCheckBoxes)
    {
        global $gSession;

        if (!empty($pcomponentsFromOriginalOrder) && !empty($pSectionCheckBoxes))
        {
            $iCountDefault = count($pSectionCheckBoxes);
            foreach ($pcomponentsFromOriginalOrder as $aData)
            {
                if ($aData['islist'] == 0)
                {
                    $aData['componentpath'] .= $aData['componentcode'];
                }
                for ($iIncDefault = 0; $iIncDefault < $iCountDefault; $iIncDefault++)
                {
                    $sectionPathToCheck = $pSectionCheckBoxes[$iIncDefault]['path'];

                    if ($pSectionCheckBoxes[$iIncDefault]['islist'] == 0)
                    {
                        $sectionPathToCheck .= $pSectionCheckBoxes[$iIncDefault]['code'];
                    }

                    if ($aData['componentpath'] == $sectionPathToCheck && $pSectionCheckBoxes[$iIncDefault]['code'] == $aData['componentcode'])
                    {
                        if (array_key_exists('checkboxselected', $aData))
                        {
                            $pSectionCheckBoxes[$iIncDefault]['checked'] = $aData['checkboxselected'];
                            $pSectionCheckBoxes[$iIncDefault]['quantity'] = $aData['quantity'];

                             //if we are not a reorder we want to attempt to match the quantities slected against a component from the designer
                            if ($gSession['order']['isreorder'] == 0)
                            {
                                $pSectionCheckBoxes[$iIncDefault]['quantity'] = $aData['quantity'];
                            }

                            $SectionMetaData = &$pSectionCheckBoxes[$iIncDefault]['metadata'];
                            MetaDataObj::getMetaDataComponentValue($pcomponentsFromOriginalOrder, $SectionMetaData,
                            $sectionPathToCheck);
                        }
                    }
                }
            }
        }
    }

    /**
     * Updates sections of the entire order.
     *
     * @static
     *
     * @author Steffen Haugk
     * @since Version 3.0.0
     */
    // update order sections of all order items
    static function updateAllOrderSections()
    {
        global $gSession;

        // loop around order items
        $itemsCount = count($gSession['items']);
        for ($i = 0; $i < $itemsCount; $i++)
        {
            self::updateOneOrderSection($i);
        }

        // loop around order footer sections
        foreach ($gSession['order']['orderFooterSections'] as $sectionIndex => &$orderFooterSection)
        {
            self::updateSection($orderFooterSection['itemproductcode'], $orderFooterSection['itemqty'],
                    $orderFooterSection['itempagecount'], $orderFooterSection, array(), -1, $sectionIndex, -1, false);
        }

        return true;
    }

    // update order sections of specified order item
    static function updateOneOrderSection($pOrderLine, $pComponentsFromOriginalOrder = array())
    {
        global $gSession;
        if ($pOrderLine == TPX_ORDERFOOTER_ID)
        {
            $orderItem = &$gSession['order']['orderFooterSections'];

            foreach ($orderItem as $sectionIndex => &$section)
            {
                self::updateSection($section['itemproductcode'], $section['itemqty'], $section['itempagecount'], $section,
                        $pComponentsFromOriginalOrder, -1, $sectionIndex, -1, false);
                foreach ($section['checkboxes'] as &$checkbox)
                {
                    self::resetCheckboxAtRootOfSectionsMetaData($checkbox['itemqty'], $checkbox, $pOrderLine);
                }
            }

            foreach ($gSession['order']['orderFooterCheckboxes'] as &$checkbox)
            {
                self::resetCheckboxAtRootOfSectionsMetaData($checkbox['itemqty'], $checkbox, $pOrderLine);
            }
        }
        else
        {
            $orderItem = &$gSession['items'][$pOrderLine];
            $orderItemQty = $orderItem['itemqty'];

            // loop round each top level section
            foreach ($orderItem['sections'] as $sectionIndex => &$section)
            {
                self::updateSection($section['itemproductcode'], $orderItemQty, $section['itempagecount'], $section,
                        $pComponentsFromOriginalOrder, $pOrderLine, $sectionIndex, -1, false);

                foreach ($section['checkboxes'] as &$checkbox)
                {
                    self::resetCheckboxAtRootOfSectionsMetaData($orderItemQty, $checkbox, $pOrderLine);
                }
            }

            foreach ($orderItem['checkboxes'] as &$checkbox)
            {
                self::resetCheckboxAtRootOfSectionsMetaData($orderItemQty, $checkbox, $pOrderLine);
            }
        }

        if ($pOrderLine != TPX_ORDERFOOTER_ID)
        {
            foreach ($orderItem['lineFooterSections'] as $sectionIndex => &$section)
            {
                self::updateSection($orderItem['componenttreeproductcode'], $orderItem['itemqty'], $orderItem['itempagecount'], $section,
                        $pComponentsFromOriginalOrder, $pOrderLine, $sectionIndex, -1, false);

                foreach ($section['checkboxes'] as &$checkbox)
                {
                    self::resetCheckboxAtRootOfSectionsMetaData($orderItemQty, $checkbox, $pOrderLine);
                }
            }

            foreach ($orderItem['lineFooterCheckboxes'] as &$checkbox)
            {
                self::resetCheckboxAtRootOfSectionsMetaData($orderItem['itemqty'], $checkbox, $pOrderLine);
            }
        }
    }

    static function updateOrderShippingRate()
    {
        global $gSession;

        $processedProductsArray = array();
        $productList = '';
		$shippingMethodList = array();
		$hasSetDefaultShippingRate = false;

		// first check to see if the edl script exists
		$shippingAPIFilePath = '../Customise/scripts/EDL_ShippingRateAPI.php';

		if (file_exists($shippingAPIFilePath))
		{
			require_once($shippingAPIFilePath);
		}

        // we need to loop round each line item checking to see if there are duplicate productcodes
        // if there is then we need to add all the item totals together for the produc so we can caluclate the shipping methods correctly
        foreach ($gSession['items'] as $orderLine)
        {
            $itemProductCode = $orderLine['itemproductcode'];

            if (array_key_exists($itemProductCode, $processedProductsArray))
            {
                $processedProductsArray[$itemProductCode]['itemtotalweight'] += $orderLine['itemtotalweight'];
                $processedProductsArray[$itemProductCode]['itemtotalsellnotaxnodiscount'] += $orderLine['itemtotalsellnotaxnodiscount'];
                $processedProductsArray[$itemProductCode]['itemtotalsellwithtaxnodiscount'] += $orderLine['itemtotalsellwithtaxnodiscount'];
                $processedProductsArray[$itemProductCode]['itemtotalsellnotaxalldiscounted'] += $orderLine['itemtotalsellnotaxalldiscounted'];
                $processedProductsArray[$itemProductCode]['itemtotalsellwithtaxalldiscounted'] += $orderLine['itemtotalsellwithtaxalldiscounted'];
            }
            else
            {
                $processedProductsArray[$itemProductCode] = array();
                $processedProductsArray[$itemProductCode]['itemtotalweight'] = $orderLine['itemtotalweight'];
                $processedProductsArray[$itemProductCode]['itemtotalsellnotaxnodiscount'] = $orderLine['itemtotalsellnotaxnodiscount'];
                $processedProductsArray[$itemProductCode]['itemtotalsellwithtaxnodiscount'] = $orderLine['itemtotalsellwithtaxnodiscount'];
                $processedProductsArray[$itemProductCode]['itemtotalsellnotaxalldiscounted'] = $orderLine['itemtotalsellnotaxalldiscounted'];
                $processedProductsArray[$itemProductCode]['itemtotalsellwithtaxalldiscounted'] = $orderLine['itemtotalsellwithtaxalldiscounted'];

                $productList .= '"' . $itemProductCode . '",';
            }
        }

        // strip the trailing comma of the product list
        $productList = substr($productList, 0, -1);

		$retryCount = 3;
		do
		{
			$rebuildShippingMethods = false;

			$shippingRatesList = DatabaseObj::getOrderShippingMethods($productList, $processedProductsArray,
							$gSession['licensekeydata']['groupcode'], $gSession['order']['currencyexchangerate'],
							$gSession['order']['currencydecimalplaces'], $gSession['order']['ordertotalshippingweight'],
							$gSession['order']['ordertotalitemsellnotaxnodiscount'], $gSession['order']['ordertotalitemsellwithtaxnodiscount'],
							$gSession['order']['ordertotalitemsellnotaxalldiscounted'],
							$gSession['order']['ordertotalitemsellwithtaxalldiscounted'],
							$gSession['shipping'][0]['shippingcustomercountrycode'], $gSession['shipping'][0]['shippingcustomerregioncode'],
							$gSession['order']['billingcustomercountrycode'], $gSession['order']['billingcustomerregioncode']);

			$itemCount = count($shippingRatesList);

			if (method_exists('ShippingRateAPI', 'buildShippingMethodsList'))
			{
				$shippingMethodList = array();
				$existingShippingMethodCodeArray = array();
				$newShippingRateMethodArray = array();
				$shippingMethodTemp = array();
				$newShippingMethodsList = array();
				$taxRateCache = array();

				for ($i = 0; $i < $itemCount; $i++)
				{
					// build a list of existing shipping methods
					// this is used to calucalte which shipping methods have been added by the script later on
					if (! in_array($shippingRatesList[$i]['methodcode'], $existingShippingMethodCodeArray))
					{
						$existingShippingMethodCodeArray[] = $shippingRatesList[$i]['methodcode'];
					}

					$shippingMethodList[] = array(
						'shippingratecode' => $shippingRatesList[$i]['ratecode'],
						'shippingrateinfo' => $shippingRatesList[$i]['info'],
						'shippingratecost' => $shippingRatesList[$i]['cost'],
						'shippingratesell' => $shippingRatesList[$i]['sell'],
						'shippingmethodcode' => $shippingRatesList[$i]['methodcode'],
						'shippingratepricetaxcode' => $shippingRatesList[$i]['taxcode'],
						'orderminvalue' => $shippingRatesList[$i]['orderminvalue'],
						'ordermaxvalue' => $shippingRatesList[$i]['ordermaxvalue']
					);
				}

				$paramArray = self::buildShippingAPIParams($shippingMethodList);
				$modifiedShippingMethodsList = ShippingRateAPI::buildShippingMethodsList($paramArray);

				$gSession['shipping'][0]['shippingprivatedata'] = $modifiedShippingMethodsList['privatedata'];

				// record customershippingcountrycode so we can determine if the customer has changed country
				// this can be used by the script to set resetselection if needed
				// if $gSession['shipping'][0]['shippingprivatedata']['customershippingcountrycode'] is empty then we know it's the first time
				$gSession['shipping'][0]['shippingprivatedata']['customershippingcountrycode'] = $paramArray['shippingaddress']['shippingcustomercountrycode'];

				// build a list of shipping methods codes
				// this is used to calulcate new methods added by the script
				foreach ($modifiedShippingMethodsList['shippingmethodslist'] as $newShippingRate)
				{
					$newShippingRateMethodArray[] = $newShippingRate['shippingmethodcode'];
				}

				// check for new shipping methods from the edl script and look it up to make sure it is a valid shipping method
				$shippingMethodCodeDiffArray = array_diff($newShippingRateMethodArray, $existingShippingMethodCodeArray);

				// store processed shipping methods so we can filter out duplicates
				$processedShippingMethods = array();

				// loop through all the shipping methods returned by the script and process them
				foreach ($modifiedShippingMethodsList['shippingmethodslist'] as $theShippingMethod)
				{
					$shippingMethodTemp = array();

					// filter out duplicate shipping methods
					if (! in_array($theShippingMethod['shippingmethodcode'], $processedShippingMethods))
					{
						// check if the rate has been added from the script and not originally from taopix
						if (in_array($theShippingMethod['shippingmethodcode'], $shippingMethodCodeDiffArray))
						{
							// lookup shipping rate info from database as we don't have the details at this point
							$getShippingRateFromCodeResult = self::getShippingRateFromMethodAndRateCode($theShippingMethod['shippingmethodcode'], $theShippingMethod['shippingratecode'], $gSession['licensekeydata']['groupcode'], $gSession['userdata']['companycode']);

							// if the rate has been found then set the shipping method to the values from the database
							// these will be overwritten with what was returned from the script later on
							if ($getShippingRateFromCodeResult['error'] == '')
							{
								$shippingMethodTemp = $getShippingRateFromCodeResult['data'];

								// check if this shipping method already exists in the session, if not then create the method with default/empty settings
								// this prevents collect from store details being overwritten if a collect from store method is added via the script
								if (! array_key_exists($theShippingMethod['shippingmethodcode'], $gSession['shipping'][0]['shippingMethods']))
								{
									$shippingMethodEntry = array();
									Order_model::setShippingMethodDefaults($shippingMethodEntry);

									if ($shippingMethodTemp['collectfromstore'])
									{
										Order_model::setCollectFromStoreValues($shippingMethodTemp['methodcode'],
											$shippingMethodTemp['methodname'], $shippingMethodEntry, $gSession['licensekeydata']['groupcode']);
									}

									$gSession['shipping'][0]['shippingMethods'][$theShippingMethod['shippingmethodcode']] = $shippingMethodEntry;
									$gSession['shipping'][0]['shippingMethods'][$theShippingMethod['shippingmethodcode']]['payInStoreAllowed'] = $shippingMethodTemp['payinstoreallowed'];
								}
							}
						}
						else
						{
							// shipping method was returned by taopix so use the existing details
							// these will be overwritten with what was returned from the script later on
							$methodCode = $theShippingMethod['shippingmethodcode'];
							$origShippingMethodArray = array_values(array_filter($shippingRatesList, function($pValue) use($methodCode)
							{
								return $pValue['methodcode'] == $methodCode;
							}));

							if (isset($origShippingMethodArray[0]))
							{
								$shippingMethodTemp = $origShippingMethodArray[0];
							}
						}

						$processedShippingMethods[] = $theShippingMethod['shippingmethodcode'];
					}

					// if we have some shipping methods then apply the changes from the script
					// we may not have any shipping methods if they are not set for the correct zone, etc. and none have been added by the script
					if (count($shippingMethodTemp) > 0)
					{
						// apply changes from the script
						$shippingMethodTemp['ratecode'] = (isset($theShippingMethod['shippingratecode'])) ? $theShippingMethod['shippingratecode'] : $shippingMethodTemp['ratecode'];
						$shippingMethodTemp['info'] = (isset($theShippingMethod['shippingrateinfo'])) ? $theShippingMethod['shippingrateinfo'] : $shippingMethodTemp['info'];
						$shippingMethodTemp['cost'] = (isset($theShippingMethod['shippingratecost'])) ? $theShippingMethod['shippingratecost'] : $shippingMethodTemp['cost'];
						$shippingMethodTemp['sell'] = (isset($theShippingMethod['shippingratesell'])) ? $theShippingMethod['shippingratesell'] : $shippingMethodTemp['sell'];

						// look up the taxrate value from the shippingratepricetaxcode value either returned from taopix or the script
						// only look up the tax rate if the code has been changed
						if ($theShippingMethod['shippingratepricetaxcode'] != '' && $shippingMethodTemp['taxcode'] != $theShippingMethod['shippingratepricetaxcode'])
						{
							// cache the gettaxrate() results so we don't need to call the database everytime the same taxcode is used
							if (! array_key_exists($theShippingMethod['shippingratepricetaxcode'], $taxRateCache))
							{
								// taxrate does not exist in the cache so we need to look it up from the database
								$taxDataArray = DatabaseObj::getTaxRate($theShippingMethod['shippingratepricetaxcode']);

								if ($taxDataArray['result'] == '')
								{
									$shippingMethodTemp['taxcode'] = $theShippingMethod['shippingratepricetaxcode'];
									$shippingMethodTemp['taxrate'] = $taxDataArray['rate'];

									$taxRateCache[$theShippingMethod['shippingratepricetaxcode']] = $taxDataArray;
								}
								else
								{
									$shippingMethodTemp['taxcode'] = '';
									$shippingMethodTemp['taxrate'] = 0.00;
								}
							}
							else
							{
								// read from cache
								$shippingMethodTemp['taxcode'] = $theShippingMethod['shippingratepricetaxcode'];
								$shippingMethodTemp['taxrate'] =$taxRateCache[$theShippingMethod['shippingratepricetaxcode']]['rate'];
							}
						}
						else
						{
							$shippingMethodTemp['taxcode'] = '';
							$shippingMethodTemp['taxrate'] = 0.00;
						}

						$newShippingMethodsList[] = $shippingMethodTemp;
					}
				}

				// if no shipping methods are returned from the script, then allow no shipping methods to be displayed in the cart
				$shippingRatesList = $newShippingMethodsList;

				$itemCount = count($shippingRatesList);

				// control centre checks resetselection and if true sets $gSession['shipping'][0]['shippingratecode'] = ''

				if ($modifiedShippingMethodsList['resetselection'])
				{
					$gSession['shipping'][0]['shippingmethodcode'] = '';
					$gSession['shipping'][0]['shippingmethodname'] = '';
					$gSession['shipping'][0]['shippingmethodusedefaultshippingaddress'] = (($gSession['order']['canmodifyshippingaddress'] + 1) % 2);
					$gSession['shipping'][0]['shippingmethodusedefaultbillingaddress'] = (($gSession['order']['canmodifybillingaddress'] + 1) % 2);
					$gSession['shipping'][0]['shippingmethodcanmodifycontactdetails'] = $gSession['order']['canmodifyshippingcontactdetails'];
					$gSession['shipping'][0]['shippingratecode'] = '';
					$gSession['shipping'][0]['shippingrateinfo'] = '';
					$gSession['shipping'][0]['shippingratecost'] = 0.00;
					$gSession['shipping'][0]['shippingratesell'] = 0.00;
					$gSession['shipping'][0]['shippingratepricetaxcode'] = '';
					$gSession['shipping'][0]['shippingratepricetaxrate'] = 0.00;
					$gSession['shipping'][0]['payinstoreallowed'] = 0;
					$gSession['order']['shippingrequiresdelivery'] = 0;
				}
			}

			// check if we have a shipping rate assigned
			if ($gSession['shipping'][0]['shippingratecode'] == '')
			{
				// if we don't have a rate and one is available then assign the first one (default?)
				if ($itemCount > 0)
				{
					$gSession['shipping'][0]['shippingmethodcode'] = $shippingRatesList[0]['methodcode'];
					$gSession['shipping'][0]['shippingmethodname'] = $shippingRatesList[0]['methodname'];
					$gSession['shipping'][0]['shippingmethodusedefaultshippingaddress'] = $shippingRatesList[0]['usedefaultshippingaddress'];
					$gSession['shipping'][0]['shippingmethodusedefaultbillingaddress'] = $shippingRatesList[0]['usedefaultbillingaddress'];
					$gSession['shipping'][0]['shippingmethodcanmodifycontactdetails'] = $shippingRatesList[0]['canmodifycontactdetails'];
					$gSession['shipping'][0]['shippingratecode'] = $shippingRatesList[0]['ratecode'];
					$gSession['shipping'][0]['shippingrateinfo'] = $shippingRatesList[0]['info'];
					$gSession['shipping'][0]['shippingratecost'] = $shippingRatesList[0]['cost'];
					$gSession['shipping'][0]['shippingratesell'] = $shippingRatesList[0]['sell'];
					$gSession['shipping'][0]['shippingratepricetaxcode'] = $shippingRatesList[0]['taxcode'];
					$gSession['shipping'][0]['shippingratepricetaxrate'] = $shippingRatesList[0]['taxrate'];

					$gSession['shipping'][0]['payinstoreallowed'] = $gSession['shipping'][0]['shippingMethods'][$shippingRatesList[0]['methodcode']]['payInStoreAllowed'];
					$gSession['order']['shippingrequiresdelivery'] = $shippingRatesList[0]['requiresdelivery'];

					if ($gSession['shipping'][0]['shippingmethodusedefaultshippingaddress'] == 1)
					{
						self::copyDefaultAddressToCurrentAddress('shipping');

						// we have changed the shipping address so we need to re-build the shipping methods
						$rebuildShippingMethods = true;
					}

					if ($gSession['shipping'][0]['shippingmethodusedefaultbillingaddress'] == 1)
					{
						self::copyDefaultAddressToCurrentAddress('billing');
					}
				}
				else
				{
					// don't have a rate and none are available to use, so set can modify shipping address to order default.
					$gSession['shipping'][0]['shippingmethodcanmodifycontactdetails'] = $gSession['order']['canmodifyshippingcontactdetails'];
				}

				$hasSetDefaultShippingRate = true;
			}
			else
			{
				// we have a rate but make sure it is still available
				$shippingRateExists = false;
				$shippingMethodExists = false;
				$shippingMethodList = Array();

				for ($i = 0; $i < $itemCount; $i++)
				{
					if ($shippingRatesList[$i]['ratecode'] == $gSession['shipping'][0]['shippingratecode'])
					{
						$gSession['shipping'][0]['shippingmethodcode'] = $shippingRatesList[$i]['methodcode'];
						$gSession['shipping'][0]['shippingmethodname'] = $shippingRatesList[$i]['methodname'];
						$gSession['shipping'][0]['shippingmethodusedefaultshippingaddress'] = $shippingRatesList[$i]['usedefaultshippingaddress'];
						$gSession['shipping'][0]['shippingmethodusedefaultbillingaddress'] = $shippingRatesList[$i]['usedefaultbillingaddress'];
						$gSession['shipping'][0]['shippingmethodcanmodifycontactdetails'] = $shippingRatesList[$i]['canmodifycontactdetails'];
						$gSession['shipping'][0]['shippingrateinfo'] = $shippingRatesList[$i]['info'];
						$gSession['shipping'][0]['shippingratecost'] = $shippingRatesList[$i]['cost'];
						$gSession['shipping'][0]['shippingratesell'] = $shippingRatesList[$i]['sell'];
						$gSession['shipping'][0]['shippingratepricetaxcode'] = $shippingRatesList[$i]['taxcode'];
						$gSession['shipping'][0]['shippingratepricetaxrate'] = $shippingRatesList[$i]['taxrate'];

						$gSession['order']['shippingrequiresdelivery'] = $shippingRatesList[$i]['requiresdelivery'];

						// if the store is not external then we need to use the pay in store flag from the rate
						// (choosing an external store sets the flag in the shipping method)
						if ($gSession['shipping'][0]['shippingMethods'][$shippingRatesList[$i]['methodcode']]['externalstore'] == 0)
						{
							$gSession['shipping'][0]['payinstoreallowed'] = $shippingRatesList[$i]['payinstoreallowed'];
							$gSession['shipping'][0]['shippingMethods'][$shippingRatesList[$i]['methodcode']]['payInStoreAllowed'] = $shippingRatesList[$i]['payinstoreallowed'];
						}

						$shippingRateExists = true;
						break;
					}

					if ($shippingRatesList[$i]['methodcode'] == $gSession['shipping'][0]['shippingmethodcode'])
					{
						$shippingMethodExists = true;
						array_push($shippingMethodList, $shippingRatesList[$i]);
					}
				}

				// if the shipping rate doesn't exist use the same shipping method or just default to the first rate if one exists
				if ($shippingRateExists == false)
				{
					if ($itemCount > 0)
					{
						array_push($shippingMethodList, $shippingRatesList[0]);
					}

					if (count($shippingMethodList) > 0)
					{
						if ($shippingMethodList[0]['usedefaultshippingaddress'] == 1)
						{
							self::copyDefaultAddressToCurrentAddress('shipping');

							// we have changed the shipping address so we need to re-build the shipping methods
							$rebuildShippingMethods = true;
						}
						else
						{
							$gSession['shipping'][0]['shippingmethodcode'] = $shippingMethodList[0]['methodcode'];
							$gSession['shipping'][0]['shippingmethodname'] = $shippingMethodList[0]['methodname'];
							$gSession['shipping'][0]['shippingmethodusedefaultshippingaddress'] = $shippingMethodList[0]['usedefaultshippingaddress'];
							$gSession['shipping'][0]['shippingmethodusedefaultbillingaddress'] = $shippingMethodList[0]['usedefaultbillingaddress'];
							$gSession['shipping'][0]['shippingmethodcanmodifycontactdetails'] = $shippingRatesList[0]['canmodifycontactdetails'];
							$gSession['shipping'][0]['shippingratecode'] = $shippingMethodList[0]['ratecode'];
							$gSession['shipping'][0]['shippingrateinfo'] = $shippingMethodList[0]['info'];
							$gSession['shipping'][0]['shippingratecost'] = $shippingMethodList[0]['cost'];
							$gSession['shipping'][0]['shippingratesell'] = $shippingMethodList[0]['sell'];
							$gSession['shipping'][0]['shippingratepricetaxcode'] = $shippingMethodList[0]['taxcode'];
							$gSession['shipping'][0]['shippingratepricetaxrate'] = $shippingMethodList[0]['taxrate'];

							if (isset($shippingMethodList[0]['payInStoreAllowed']))
							{
								$gSession['shipping'][0]['payinstoreallowed'] = $shippingMethodList[0]['payInStoreAllowed'];
							}
							else
							{
								$gSession['shipping'][0]['payinstoreallowed'] = false;
							}

							$gSession['order']['shippingrequiresdelivery'] = $shippingMethodList[0]['requiresdelivery'];
						}
					}
					else
					{
						$gSession['shipping'][0]['shippingmethodcode'] = '';
						$gSession['shipping'][0]['shippingmethodname'] = '';
						$gSession['shipping'][0]['shippingmethodusedefaultshippingaddress'] = (($gSession['order']['canmodifyshippingaddress'] + 1) % 2);
						$gSession['shipping'][0]['shippingmethodusedefaultbillingaddress'] = (($gSession['order']['canmodifybillingaddress'] + 1) % 2);
						$gSession['shipping'][0]['shippingmethodcanmodifycontactdetails'] = $gSession['order']['canmodifyshippingcontactdetails'];
						$gSession['shipping'][0]['shippingratecode'] = '';
						$gSession['shipping'][0]['shippingrateinfo'] = '';
						$gSession['shipping'][0]['shippingratecost'] = 0.00;
						$gSession['shipping'][0]['shippingratesell'] = 0.00;
						$gSession['shipping'][0]['shippingratepricetaxcode'] = '';
						$gSession['shipping'][0]['shippingratepricetaxrate'] = 0.00;
						$gSession['shipping'][0]['payinstoreallowed'] = 0;
						$gSession['order']['shippingrequiresdelivery'] = 0;
					}

					$hasSetDefaultShippingRate = true;
				}
			}

			// decrement the retry count and if it becomes zero assume we have no shipping methods
			// this is to prevent any type of infinite loop caused by the customer address / shipping methods
			$retryCount--;
			if ($retryCount == 0)
			{
				$gSession['shipping'][0]['shippingmethodcode'] = '';
				$gSession['shipping'][0]['shippingmethodname'] = '';
				$gSession['shipping'][0]['shippingmethodusedefaultshippingaddress'] = (($gSession['order']['canmodifyshippingaddress'] + 1) % 2);
				$gSession['shipping'][0]['shippingmethodusedefaultbillingaddress'] = (($gSession['order']['canmodifybillingaddress'] + 1) % 2);
				$gSession['shipping'][0]['shippingmethodcanmodifycontactdetails'] = $gSession['order']['canmodifyshippingcontactdetails'];
				$gSession['shipping'][0]['shippingratecode'] = '';
				$gSession['shipping'][0]['shippingrateinfo'] = '';
				$gSession['shipping'][0]['shippingratecost'] = 0.00;
				$gSession['shipping'][0]['shippingratesell'] = 0.00;
				$gSession['shipping'][0]['shippingratepricetaxcode'] = '';
				$gSession['shipping'][0]['shippingratepricetaxrate'] = 0.00;
				$gSession['shipping'][0]['payinstoreallowed'] = 0;
				$gSession['order']['shippingrequiresdelivery'] = 0;

				$hasSetDefaultShippingRate = true;

				break;
			}

        } while ($rebuildShippingMethods);

        // determine the tax status for the component
        if ($gSession['shipping'][0]['shippingratepricetaxcode'] != '')
        {
            // tax is included in the price so determine the price without tax
            $gSession['shipping'][0]['shippingratesellnotax'] = UtilsObj::bround(($gSession['shipping'][0]['shippingratesell'] / ($gSession['shipping'][0]['shippingratepricetaxrate'] + 100)) * 100,
                            $gSession['order']['currencydecimalplaces']);
            $gSession['shipping'][0]['shippingratetaxtotal'] = $gSession['shipping'][0]['shippingratesell'] - $gSession['shipping'][0]['shippingratesellnotax'];

            if ($gSession['shipping'][0]['shippingratepricetaxrate'] != $gSession['shipping'][0]['shippingratetaxrate'])
            {
                // if the tax included in the price is different to the line tax then we use the price without tax as we will be adding it later
                $gSession['shipping'][0]['shippingratesell'] = $gSession['shipping'][0]['shippingratesellnotax'];
                $taxValue = UtilsObj::bround($gSession['shipping'][0]['shippingratesell'] * $gSession['shipping'][0]['shippingratetaxrate'] / 100,
                                $gSession['order']['currencydecimalplaces']);
                $gSession['shipping'][0]['shippingratesellwithtax'] = $gSession['shipping'][0]['shippingratesell'] + $taxValue;
            }
            else
            {
                // tax is already calculated
                $gSession['shipping'][0]['shippingratesellwithtax'] = $gSession['shipping'][0]['shippingratesell'];
            }
        }
        else
        {
            // no tax was included in the price
            $gSession['shipping'][0]['shippingratetaxtotal'] = UtilsObj::bround($gSession['shipping'][0]['shippingratesell'] * $gSession['shipping'][0]['shippingratetaxrate'] / 100,
                            $gSession['order']['currencydecimalplaces']);
            $gSession['shipping'][0]['shippingratesellwithtax'] = $gSession['shipping'][0]['shippingratesell'] + $gSession['shipping'][0]['shippingratetaxtotal'];
            $gSession['shipping'][0]['shippingratesellnotax'] = $gSession['shipping'][0]['shippingratesell'];
        }

        // add the tax at this point if necessary
        if ($gSession['order']['showpriceswithtax'] == 1)
        {
            $gSession['shipping'][0]['shippingratesell'] = $gSession['shipping'][0]['shippingratesellwithtax'];
        }
        else
        {
            $gSession['shipping'][0]['shippingratesell'] = $gSession['shipping'][0]['shippingratesellnotax'];
        }

		if (($hasSetDefaultShippingRate) && ($gSession['shipping'][0]['shippingmethodcode'] != ''))
		{
			if (method_exists('ShippingRateAPI', 'setDefaultShippingMethod'))
			{
				$shippingMethodList = array();
				$existingShippingMethodCodeArray = array();
				$newShippingRateMethodArray = array();
				$shippingMethodTemp = array();
				$newShippingMethodsList = array();

				for ($i = 0; $i < $itemCount; $i++)
				{
					if (! in_array($shippingRatesList[$i]['methodcode'], $existingShippingMethodCodeArray))
					{
						$existingShippingMethodCodeArray[] = $shippingRatesList[$i]['methodcode'];
					}

					$shippingMethodList[] = array(
						'shippingratecode' => $shippingRatesList[$i]['ratecode'],
						'shippingrateinfo' => $shippingRatesList[$i]['info'],
						'shippingratecost' => $shippingRatesList[$i]['cost'],
						'shippingratesell' => $shippingRatesList[$i]['sell'],
						'shippingmethodcode' => $shippingRatesList[$i]['methodcode'],
						'taxcode' => $shippingRatesList[$i]['taxcode'],
						'taxrate' => $shippingRatesList[$i]['taxrate'],
						'orderminvalue' => $shippingRatesList[$i]['orderminvalue'],
						'ordermaxvalue' => $shippingRatesList[$i]['ordermaxvalue']
					);
				}

				$paramArray = self::buildShippingAPIParams($shippingMethodList);
				$defaultShippingMethod = ShippingRateAPI::setDefaultShippingMethod($paramArray);

				// check a shipping method with this method code exists
				if (($defaultShippingMethod == '') || (! in_array($defaultShippingMethod, $existingShippingMethodCodeArray)))
				{
					$gSession['shipping'][0]['shippingmethodcode'] = $shippingRatesList[0]['methodcode'];
					$gSession['shipping'][0]['shippingmethodname'] = $shippingRatesList[0]['methodname'];
					$gSession['shipping'][0]['shippingmethodusedefaultshippingaddress'] = $shippingRatesList[0]['usedefaultshippingaddress'];
					$gSession['shipping'][0]['shippingmethodusedefaultbillingaddress'] = $shippingRatesList[0]['usedefaultbillingaddress'];
					$gSession['shipping'][0]['shippingmethodcanmodifycontactdetails'] = $shippingRatesList[0]['canmodifycontactdetails'];
					$gSession['shipping'][0]['shippingratecode'] = $shippingRatesList[0]['ratecode'];
					$gSession['shipping'][0]['shippingrateinfo'] = $shippingRatesList[0]['info'];
					$gSession['shipping'][0]['shippingratecost'] = UtilsObj::bround($shippingRatesList[0]['cost'], $gSession['order']['currencydecimalplaces']);
					$gSession['shipping'][0]['shippingratesell'] = UtilsObj::bround($shippingRatesList[0]['sell'], $gSession['order']['currencydecimalplaces']);
					$gSession['shipping'][0]['shippingratepricetaxcode'] = $shippingRatesList[0]['taxcode'];
					$gSession['shipping'][0]['shippingratepricetaxrate'] =  UtilsObj::bround($shippingRatesList[0]['taxrate'], $gSession['order']['currencydecimalplaces']);

					if (isset($shippingRatesList[0]['payInStoreAllowed']))
					{
						$gSession['shipping'][0]['payinstoreallowed'] = $shippingRatesList[0]['payInStoreAllowed'];
					}
					else
					{
						$gSession['shipping'][0]['payinstoreallowed'] = false;
					}

					$gSession['order']['shippingrequiresdelivery'] = $shippingRatesList[0]['requiresdelivery'];
				}
				else
				{
					$defaultShippingMethodArray = array_values(array_filter($shippingRatesList, function($pValue) use($defaultShippingMethod)
					{
						return $pValue['methodcode'] == $defaultShippingMethod;
					}));

					$gSession['shipping'][0]['shippingmethodcode'] = $defaultShippingMethodArray[0]['methodcode'];
					$gSession['shipping'][0]['shippingmethodname'] = $defaultShippingMethodArray[0]['methodname'];
					$gSession['shipping'][0]['shippingmethodusedefaultshippingaddress'] = $defaultShippingMethodArray[0]['usedefaultshippingaddress'];
					$gSession['shipping'][0]['shippingmethodusedefaultbillingaddress'] = $defaultShippingMethodArray[0]['usedefaultbillingaddress'];
					$gSession['shipping'][0]['shippingmethodcanmodifycontactdetails'] = $defaultShippingMethodArray[0]['canmodifycontactdetails'];
					$gSession['shipping'][0]['shippingratecode'] = $defaultShippingMethodArray[0]['ratecode'];
					$gSession['shipping'][0]['shippingrateinfo'] = $defaultShippingMethodArray[0]['info'];
					$gSession['shipping'][0]['shippingratecost'] = UtilsObj::bround($defaultShippingMethodArray[0]['cost'], $gSession['order']['currencydecimalplaces']);
					$gSession['shipping'][0]['shippingratesell'] = UtilsObj::bround($defaultShippingMethodArray[0]['sell'], $gSession['order']['currencydecimalplaces']);
					$gSession['shipping'][0]['shippingratepricetaxcode'] = $defaultShippingMethodArray[0]['taxcode'];
					$gSession['shipping'][0]['shippingratepricetaxrate'] = UtilsObj::bround($defaultShippingMethodArray[0]['taxrate'], 4);


					if (isset($defaultShippingMethodArray[0]['payInStoreAllowed']))
					{
						$gSession['shipping'][0]['payinstoreallowed'] = $defaultShippingMethodArray[0]['payInStoreAllowed'];
					}
					else
					{
						$gSession['shipping'][0]['payinstoreallowed'] = false;
					}

					$gSession['order']['shippingrequiresdelivery'] = $defaultShippingMethodArray[0]['requiresdelivery'];
				}
			}
		}

        self::updateOrderTotal();

        return $shippingRatesList;
    }

    static function updateOrderPaymentMethod()
    {
        // include the payment integrations module
        require_once('../Order/PaymentIntegration/PaymentIntegration.php');

        global $gSession;

        $resultArray = Array();
        $payInStoreMethod = 0;
        $payInStoreOnly = false;

        if ($gSession['order']['ordertotal'] > 0.00)
        {
            $hasPaymentMethod = false;
            $userPaymentMethodsList = explode(',', $gSession['userpaymentmethods']);
            $userPaymentMethodsItemCount = count($userPaymentMethodsList);

            $paymentMethodsList = DatabaseObj::getPaymentMethodsList($gSession['order']['shippingrequiresdelivery']);

            $itemCount = count($paymentMethodsList);
            for ($i = 0; $i < $itemCount; $i++)
            {
                $paymentMethodCode = $paymentMethodsList[$i]['code'];

                // if the payment method is paylater then check if we should include it
                if ($paymentMethodCode == 'PAYLATER')
                {
                    $compareAppVersionResult = UtilsObj::compareApplicationVersions($gSession['appversion'], '2.1.0.11');
                    if (($compareAppVersionResult == '=') || ($compareAppVersionResult == '>'))
                    {
                        $validAppVersion = true;
                    }
                    else
                    {
                        $validAppVersion = false;
                    }

                    // if the session has been revived, is a re-order or the app version is less than 2.1.0.11 (2.1.0b4) we do not want to include it
                    if (($gSession['sessionrevived'] == 1) || ($gSession['order']['isreorder'] == 1) || ($validAppVersion == false))
                    {
                        $paymentMethodsList[$i]['isactive'] = 0;
                    }
                }

                if ($paymentMethodCode == 'PAYINSTORE')
                {
                    if ($gSession['shipping'][0]['collectfromstore'])
                    {
                        if ($gSession['shipping'][0]['payinstoreallowed'] == TPX_PAY_IN_STORE_DO_NOT_ALLOW)
                        {
                            $paymentMethodsList[$i]['isactive'] = 0;
                        }
                        if ($gSession['shipping'][0]['payinstoreallowed'] == TPX_PAY_IN_STORE_ONLY)
                        {
                            $payInStoreOnly = true;
                        }
                    }
                    else
                    {
                        $paymentMethodsList[$i]['isactive'] = 0;
                    }
                }

                for ($j = 0; $j < $userPaymentMethodsItemCount; $j++)
                {
                    if (($paymentMethodCode == $gSession['order']['paymentmethodcode']) && ($paymentMethodsList[$i]['isactive'] == 1))
                    {
                        $gSession['order']['paymentmethodname'] = $paymentMethodsList[$i]['name'];
                        $hasPaymentMethod = true;
                    }
                    if (($paymentMethodCode == $userPaymentMethodsList[$j]) && ($paymentMethodsList[$i]['isactive'] == 1))
                    {
                        $active = true;

                        if ($paymentMethodCode == 'CARD')
                        {
                            $methodArray = PaymentIntegrationObj::configure($paymentMethodCode);
                            $active = $methodArray['active'];
                        }

                        if ($active)
                        {
                            array_push($resultArray, $paymentMethodsList[$i]);
                            if ($paymentMethodCode == 'PAYINSTORE')
                            {
                                $payInStoreMethod = $i;
                            }
                            break;
                        }
                    }
                }
            }

            if (($hasPaymentMethod == false) || ($payInStoreOnly))
            {
                if (count($resultArray) > 0)
                {
                    if ($payInStoreOnly)
                    {
                        $gSession['order']['paymentmethodcode'] = $paymentMethodsList[$payInStoreMethod]['code'];
                        $gSession['order']['paymentmethodname'] = $paymentMethodsList[$payInStoreMethod]['name'];
                    }
                    else
                    {
                        $gSession['order']['paymentmethodcode'] = $resultArray[0]['code'];
                        $gSession['order']['paymentmethodname'] = $resultArray[0]['name'];
                    }
                }
                else
                {
                    $gSession['order']['paymentmethodcode'] = '';
                    $gSession['order']['paymentmethodname'] = '';
                }
            }
        }
        else
        {
            $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
            $gSession['order']['paymentmethodcode'] = 'NONE';
            $gSession['order']['paymentmethodname'] = $smarty->get_config_vars('str_LanguageCode') . ' ' . $smarty->get_config_vars('str_LabelNone');
        }

        if ($payInStoreOnly)
        {
            return Array(0 => $paymentMethodsList[$payInStoreMethod]);
        }

        return $resultArray;
    }

    static function orderBack()
    {
        global $gSession;

        $resultArray = Array();

        $previousStage = '';
        $nextStage = $_POST['previousstage'];

        if ($nextStage == 'companionselection')
        {
            $previousStage = 'companionselection';

			$resultArray['companionalbumlist'] = self::buildOrderCompanionSelection($gSession['order']['currencyexchangerate'], $gSession['order']['currencydecimalplaces']);
        }
        else if ($nextStage == 'qty')
        {
			if ($gSession['order']['hascompanionalbums'] != 'NO')
			{
	            $previousStage = 'companionselection';
			}
			else
			{
	            $previousStage = 'qty';
			}
        }
        else if ($nextStage == 'shipping')
        {
            $gSession['order']['paymentmethodcode'] = $_POST['paymentmethodcode'];
            $gSession['order']['paymentgatewaycode'] = $_POST['paymentgatewaycode'];
            $resultArray['shippingrates'] = self::updateOrderShippingRate();

            self::updateCreditStatus();
            $previousStage = 'qty';
        }
        else if ($nextStage == 'payment')
        {
            $previousStage = 'shipping';
        }

        self::formatOrderAddresses($resultArray);

        $resultArray['custominit'] = '';
        $resultArray['metadata'] = self::buildOrderMetaData($previousStage);
        $resultArray['previousstage'] = $previousStage;
        $resultArray['nextstage'] = $nextStage;

        $gSession['order']['currentstage'] = $nextStage;

        DatabaseObj::updateSession();

        return $resultArray;
    }

    static function orderContinue()
    {
        global $gSession;

        $resultArray = Array();

        $currentStage = $_POST['stage'];

        if ($currentStage == 'companionselection')
        {
            $nextStage = 'qty';

            // re-check the voucher to make sure its usage status hasn't changed
            self::checkVoucher();
        }
        else if ($currentStage == 'qty')
        {
            $resultArray['shippingrates'] = self::updateOrderShippingRate();

            // re-check the voucher to make sure its usage status hasn't changed
            self::checkVoucher();

            $nextStage = 'shipping';
        }
        else if ($currentStage == 'shipping')
        {
            $gSession['shipping'][0]['shippingratecode'] = $_POST['shippingratecode'];

            $resultArray['shippingrates'] = self::updateOrderShippingRate();

            // update collect from store information
            $shippingMethodCode = $gSession['shipping'][0]['shippingmethodcode'];
            $gSession['shipping'][0]['storeid'] = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeCode'];
            $gSession['shipping'][0]['collectfromstore'] = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['collectFromStore'];
            $gSession['shipping'][0]['payinstoreallowed'] = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['payInStoreAllowed'];
            $gSession['shipping'][0]['distributioncentrecode'] = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['distributionCentreCode'];
            $gSession['shipping'][0]['storecustomername'] = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomername'];

            $resultArray['paymentmethods'] = self::updateOrderPaymentMethod();
            self::updateCreditStatus();

            // re-check the voucher to make sure its usage status hasn't changed
            self::checkVoucher();

            $nextStage = 'payment';
        }
        else if ($currentStage == 'payment')
        {
            $gSession['order']['paymentmethodcode'] = $_POST['paymentmethodcode'];
            $gSession['order']['paymentgatewaycode'] = $_POST['paymentgatewaycode'];

            $resultArray['paymentmethods'] = self::updateOrderPaymentMethod();

            if ((($gSession['order']['paymentmethodcode'] == 'CARD') || ($gSession['order']['paymentmethodcode'] == 'PAYPAL') || ($gSession['order']['paymentmethodcode'] == 'KLARNA')) && ($gSession['order']['ordertotaltopay'] > 0.00))
            {
                $nextStage = 'promptforcard';
            }
            else
            {
                // we haven't paid by credit card so reset the cci data just in case it was previously set by a failed transaction
                $gSession['order']['ccilogid'] = 0;
                $gSession['order']['ccitransactionid'] = '';
                $gSession['order']['cciauthorised'] = 0;
                $gSession['order']['ccipaymentreceived'] = 0;
                $gSession['order']['ccipaymentreceiveddatetime'] = '';

                $resultArray = self::complete();
                $nextStage = 'complete';
                $resultArray['orderdata'] = self::prepareOrderDataForTrackingCodes();
                $resultArray['webbrandonlineredirectionurl'] = '';

                if ($gSession['order']['basketapiworkflowtype'] == TPX_BASKETWORKFLOWTYPE_HIGHLEVELCHECKOUT)
                {
					$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $gSession['webbrandcode']);
					$brandDataArray = DatabaseObj::getBrandingFromCode($gSession['webbrandcode']);

					if ($brandDataArray['onlinedesignerlogouturl'] != '')
					{
						$redirectionURL = $brandDataArray['onlinedesignerlogouturl'];
					}
					else
					{
						$redirectionURL = $hl_config['REDIRECTIONURL'];
					}

					$resultArray['webbrandonlineredirectionurl'] = UtilsObj::correctPath($redirectionURL, '/', true);
                }
            }
        }

        self::formatOrderAddresses($resultArray);

        $resultArray['custominit'] = '';
        $resultArray['metadata'] = self::buildOrderMetaData($nextStage);
        $resultArray['previousstage'] = $currentStage;
        $resultArray['nextstage'] = $nextStage;

        $gSession['order']['currentstage'] = $nextStage;

        DatabaseObj::updateSession();

        return $resultArray;
    }

    static function orderRefresh()
    {
        global $gSession;

        $resultArray = Array();
        $previousStage = $_REQUEST['previousstage'];
        $currentStage = $_REQUEST['stage'];
        $forceChangeAddressDisplay = false;

        if ($currentStage == 'companionselection')
        {
			$resultArray['companionalbumlist'] = self::buildOrderCompanionSelection($gSession['order']['currencyexchangerate'], $gSession['order']['currencydecimalplaces']);
		}
        else if ($currentStage == 'qty')
        {
            // no additional processing for qty
        }
        else if ($currentStage == 'shipping')
        {
            $resultArray['shippingrates'] = self::updateOrderShippingRate();

            if (isset($_REQUEST['fsactionorig']))
            {
                $command = explode('.', $_REQUEST['fsactionorig']);
            }
            else
            {
                $command = explode('.', $_REQUEST['fsaction']);
			}

            if ($command[1] == 'changeShippingMethod')
            {
                $shippingRatesCount = count($resultArray['shippingrates']);
                $shippingRateCodeSelected = $_REQUEST['shippingratecode'];

                for ($i = 0; $i < $shippingRatesCount; $i++)
                {
                    if ($resultArray['shippingrates'][$i]['ratecode'] == $shippingRateCodeSelected)
                    {
                        if ($resultArray['shippingrates'][$i]['usedefaultshippingaddress'] == 0 && $gSession['shipping'][0]['shippingMethods'][$shippingRateCodeSelected]['collectFromStore'] == 0)
                        {
                            if ($gSession['order']['shippingaddressmodified'] == false)
                            {
                                $forceChangeAddressDisplay = true;
                                $gSession['order']['shippingaddressmodified'] = true;
                            }
                        }

                        break;
                    }
                }
            }
        }
        else if ($currentStage == 'payment')
        {
            $resultArray['paymentmethods'] = self::updateOrderPaymentMethod();
            self::updateCreditStatus();
        }

        self::formatOrderAddresses($resultArray);

        $resultArray['custominit'] = '';
        $resultArray['metadata'] = self::buildOrderMetaData($currentStage);
        $resultArray['previousstage'] = $previousStage;
        $resultArray['nextstage'] = $currentStage;
        $resultArray['forcechangeaddressdisplay'] = $forceChangeAddressDisplay;

        $gSession['order']['currentstage'] = $currentStage;

        DatabaseObj::updateSession();

        return $resultArray;
    }

    static function buildAllMetadataSections($pNextStage)
    {
        global $gSession;

        $isReadOnly = false;

        if ($pNextStage == 'companionselection')
        {
            $isReadOnly = false;
        }
        else if ($pNextStage == 'qty')
        {
            $isReadOnly = false;
        }
        else if ($pNextStage == 'shipping')
        {
            return;
        }
        else if ($pNextStage == 'payment')
        {
            $isReadOnly = true;
        }

        // ORDERFOOTER
        $sectionArray = &$gSession['order']['orderFooterSections'];

        foreach ($sectionArray as & $section)
        {
            $resultMetadata = MetaDataObj::buildKeywordHTML('COMPONENT',
                    strtoupper($pNextStage), $section['metadata'], $gSession['browserlanguagecode'], $section['orderlineid'], $isReadOnly);
            $section['metadatahtml'] = $resultMetadata['metadatahtml'];
            $section['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];

            foreach ($section['checkboxes'] as &$checkbox)
            {
                $resultMetadata = MetaDataObj::buildKeywordHTML('COMPONENT', strtoupper($pNextStage), $checkbox['metadata'],
                                                                $gSession['browserlanguagecode'], $checkbox['orderlineid'], $isReadOnly);
                $checkbox['metadatahtml'] = $resultMetadata['metadatahtml'];
                $checkbox['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
                if (($resultMetadata['isonekeywordmandatory']) && ($checkbox['checked'] == 1))
                {
                    $section['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
                }
            }

            foreach ($section['subsections'] as &$subSection)
            {
                $resultMetadata = MetaDataObj::buildKeywordHTML('COMPONENT', strtoupper($pNextStage), $subSection['metadata'],
                                                                $gSession['browserlanguagecode'], $subSection['orderlineid'], $isReadOnly);
                $subSection['metadatahtml'] = $resultMetadata['metadatahtml'];
                $subSection['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
                if ($resultMetadata['isonekeywordmandatory'])
                {
                    $section['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
                }

                foreach ($subSection['checkboxes'] as &$checkbox)
                {
                    $resultMetadata = MetaDataObj::buildKeywordHTML('COMPONENT', strtoupper($pNextStage), $checkbox['metadata'],
                                                                    $gSession['browserlanguagecode'], $checkbox['orderlineid'], $isReadOnly);
                    $checkbox['metadatahtml'] = $resultMetadata['metadatahtml'];
                    $checkbox['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
                    if (($resultMetadata['isonekeywordmandatory']) && ($checkbox['checked'] == 1))
                    {
                        $section['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
                    }
                }
            }
        }

        $checkboxArray = & $gSession['order']['orderFooterCheckboxes'];
        foreach ($checkboxArray as &$checkbox)
        {
            $resultMetadata = MetaDataObj::buildKeywordHTML('COMPONENT', strtoupper($pNextStage), $checkbox['metadata'],
                                                            $gSession['browserlanguagecode'], $checkbox['orderlineid'], $isReadOnly);
            $checkbox['metadatahtml'] = $resultMetadata['metadatahtml'];
            $checkbox['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
        }

        // loop around order items - begin
        foreach ($gSession['items'] as $currentLine => $orderLine)
        {
            // LINEFOOTER
            $sectionArray = &$gSession['items'][$currentLine]['lineFooterSections'];
            foreach ($sectionArray as &$section)
            {
                $resultMetadata = MetaDataObj::buildKeywordHTML('COMPONENT', strtoupper($pNextStage), $section['metadata'],
                                                                $gSession['browserlanguagecode'], $section['orderlineid'], $isReadOnly);
                $section['metadatahtml'] = $resultMetadata['metadatahtml'];
                $section['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];

                foreach ($section['checkboxes'] as &$checkbox)
                {
                    $resultMetadata = MetaDataObj::buildKeywordHTML('COMPONENT', strtoupper($pNextStage), $checkbox['metadata'],
                                    $gSession['browserlanguagecode'], $checkbox['orderlineid'], $isReadOnly);
                    $checkbox['metadatahtml'] = $resultMetadata['metadatahtml'];
                    $checkbox['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
                    if (($resultMetadata['isonekeywordmandatory']) && ($checkbox['checked'] == 1))
                    {
                        $section['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
                    }
                }

                foreach ($section['subsections'] as &$subSection)
                {
                    $resultMetadata = MetaDataObj::buildKeywordHTML('COMPONENT', strtoupper($pNextStage), $subSection['metadata'],
                                                                    $gSession['browserlanguagecode'], $subSection['orderlineid'], $isReadOnly);
                    $subSection['metadatahtml'] = $resultMetadata['metadatahtml'];
                    $subSection['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
                    if ($resultMetadata['isonekeywordmandatory'])
                    {
                        $section['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
                    }

                    foreach ($subSection['checkboxes'] as &$checkbox)
                    {
                        $resultMetadata = MetaDataObj::buildKeywordHTML('COMPONENT', strtoupper($pNextStage), $checkbox['metadata'],
                                                                        $gSession['browserlanguagecode'], $checkbox['orderlineid'], $isReadOnly);
                        $checkbox['metadatahtml'] = $resultMetadata['metadatahtml'];
                        $checkbox['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
                        if (($resultMetadata['isonekeywordmandatory']) && ($checkbox['checked'] == 1))
                        {
                            $section['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
                        }
                    }
                }
            }

            $checkboxArray = &$gSession['items'][$currentLine]['lineFooterCheckboxes'];
            foreach ($checkboxArray as &$checkbox)
            {
                $resultMetadata = MetaDataObj::buildKeywordHTML('COMPONENT', strtoupper($pNextStage), $checkbox['metadata'],
                                                                $gSession['browserlanguagecode'], $checkbox['orderlineid'], $isReadOnly);
                $checkbox['metadatahtml'] = $resultMetadata['metadatahtml'];
                $checkbox['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
            }


            // ROOT
            $sectionArray = &$gSession['items'][$currentLine]['sections'];
            foreach ($sectionArray as &$section)
            {
                $resultMetadata = MetaDataObj::buildKeywordHTML('COMPONENT', strtoupper($pNextStage), $section['metadata'],
                                                                $gSession['browserlanguagecode'], $section['orderlineid'], $isReadOnly);
                $section['metadatahtml'] = $resultMetadata['metadatahtml'];
                $section['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];

                foreach ($section['checkboxes'] as &$checkbox)
                {
                    $resultMetadata = MetaDataObj::buildKeywordHTML('COMPONENT', strtoupper($pNextStage), $checkbox['metadata'],
                                                                    $gSession['browserlanguagecode'], $checkbox['orderlineid'], $isReadOnly);
                    $checkbox['metadatahtml'] = $resultMetadata['metadatahtml'];
                    $checkbox['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];

                    if (($resultMetadata['isonekeywordmandatory']) && ($checkbox['checked'] == 1))
                    {
                        $section['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
                    }
                }

                foreach ($section['subsections'] as &$subSection)
                {
                    $resultMetadata = MetaDataObj::buildKeywordHTML('COMPONENT', strtoupper($pNextStage), $subSection['metadata'],
                                                                    $gSession['browserlanguagecode'], $subSection['orderlineid'], $isReadOnly);
                    $subSection['metadatahtml'] = $resultMetadata['metadatahtml'];
                    $subSection['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];

                    if ($resultMetadata['isonekeywordmandatory'])
                    {
                        $section['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
                    }

                    foreach ($subSection['checkboxes'] as &$checkbox)
                    {
                        $resultMetadata = MetaDataObj::buildKeywordHTML('COMPONENT', strtoupper($pNextStage), $checkbox['metadata'],
                                                                        $gSession['browserlanguagecode'], $checkbox['orderlineid'], $isReadOnly);
                        $checkbox['metadatahtml'] = $resultMetadata['metadatahtml'];
                        $checkbox['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];

                        if (($resultMetadata['isonekeywordmandatory']) && ($checkbox['checked'] == 1))
                        {
                            $section['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
                        }
                    }
                }
            }

            $checkboxArray = & $gSession['items'][$currentLine]['checkboxes'];
            foreach ($checkboxArray as &$checkbox)
            {
                $resultMetadata = MetaDataObj::buildKeywordHTML('COMPONENT', strtoupper($pNextStage), $checkbox['metadata'],
                                                                $gSession['browserlanguagecode'], $checkbox['orderlineid'], $isReadOnly);
                $checkbox['metadatahtml'] = $resultMetadata['metadatahtml'];
                $checkbox['isonekeywordmandatory'] = $resultMetadata['isonekeywordmandatory'];
            }
        }

        DatabaseObj::updateSession();
    }

    static function buildOrderMetaData($pNextStage)
    {
        global $gSession;
        $resultArray = Array();

        if ($pNextStage == 'companionselection')
        {
            $resultArray = MetaDataObj::buildKeywordHTML('ORDER', 'COMPANIONSELECTION', $gSession['order']['metadata'], $gSession['browserlanguagecode'],
                            '', 0);
        }
        else if ($pNextStage == 'qty')
        {
            $resultArray = MetaDataObj::buildKeywordHTML('ORDER', 'QTY', $gSession['order']['metadata'], $gSession['browserlanguagecode'],
                            '', 0);
        }
        else if ($pNextStage == 'shipping')
        {
            $resultArray = MetaDataObj::buildKeywordHTML('ORDER', 'SHIPPING', $gSession['order']['metadata'],
                            $gSession['browserlanguagecode'], '', 0);
        }
        else if ($pNextStage == 'payment')
        {
            $resultArray = MetaDataObj::buildKeywordHTML('ORDER', 'PAYMENT', $gSession['order']['metadata'],
                            $gSession['browserlanguagecode'], '', 0);
        }

        self::buildAllMetadataSections($pNextStage);

        return $resultArray;
    }

    static function storeOrderMetaData($pCurrentStage)
    {
        global $gSession;

		if ($pCurrentStage == 'qty')
        {
            // save component metadata
            MetaDataObj::storeHTMLKeywords(array(), 'COMPONENT');
        }
        else if ($pCurrentStage == 'shipping')
        {

        }
        else if ($pCurrentStage == 'payment')
        {
            // save order metadata
            $gSession['order']['metadata'] = MetaDataObj::storeHTMLKeywords($gSession['order']['metadata'], 'ORDER');
        }

        DatabaseObj::updateSession();
    }

    static function selectStoreDisplay()
    {
        global $gSession;

        $resultArray = array();
        $storeLocations = array();
        $location = array();
        $countryArray = array();
        $countryList = array();
        $country = array();
        $showRegionList = 0;
        $storeGroup = '';
        $showStoreGroups = 0;
        $storeGroupLabel = '';
        $shippingRateCode = $_GET['shippingratecode'];
		$countryCode = '';
		$countryName = '';
		$region = '';
		$regionCode = '';
		$regionName = '';
		$siteGroupCode = '';
		$siteGroupName = '';
        $gSession['shipping'][0]['shippingratecode'] = $shippingRateCode;
        $gSession['order']['sameshippingandbillingaddress'] = $_GET['sameshippingandbillingaddress'];

        self::updateOrderShippingRate();
        DatabaseObj::updateSession();

        $resultArray['previousstage'] = $_GET['previousstage'];
        $resultArray['stage'] = $_GET['stage'];

        $shippingMethodCode = $gSession['shipping'][0]['shippingmethodcode'];
        $shippingMethod = DatabaseObj::getShippingMethodFromCode($shippingMethodCode);

        $shippingMethodAssetID = $shippingMethod['assetid'];
        $shippingMethodAssetDataArray = DatabaseObj::getStoreLocatorLogo($shippingMethodAssetID, 0);

        if ($shippingMethodAssetID != 0)
        {
            $logoSource = './?fsaction=Order.getStoreLocatorLogo&id=' . $shippingMethodAssetID . '&ref=' . $gSession['ref'] . '&tmp=0&no=0';
            $logoWidth = $shippingMethodAssetDataArray['logowidth'];
            $logoHeight = $shippingMethodAssetDataArray['logoheight'];
        }
        else
        {
            $logoSource = '';
            $logoWidth = 0;
            $logoHeight = 0;
		}

		$smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);
        $searchFieldLabel = $smarty->get_config_vars('str_LabelAddressSearch');

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
			$sql = 'SELECT DISTINCT s.countrycode, s.countryname, s.regioncode, IF (s.region="STATE", s.state, s.county), sg.code, sg.name
					FROM `SITES` s, `SITEGROUPS` sg, `SHIPPINGRATESITES` sr
					WHERE (s.sitegroup = sg.code)
						AND (sg.code = sr.sitegroupcode)
						AND (s.active = 1)
						AND (sr.shippingratecode = ?);';
			$stmt = $dbObj->prepare($sql);

            if ($stmt)
            {
                if ($stmt->bind_param('s', $shippingRateCode))
                {
                    if ($stmt->bind_result($countryCode, $countryName, $regionCode, $regionName, $siteGroupCode, $siteGroupName))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
                                $location['countryCode'] = $countryCode;
                                $location['countryName'] = htmlentities($countryName);
                                $location['regionCode'] = $regionCode;
                                $location['regionName'] = htmlentities($regionName);
                                $location['siteGroupCode'] = $siteGroupCode;
                                $location['siteGroupName'] = LocalizationObj::getLocaleString($siteGroupName, UtilsObj::getBrowserLocale(),
                                                true);
                                array_push($storeLocations, $location);

                                // country list without duplicates
                                if (!isset($countryArray[$countryCode]))
                                {
                                    $countryArray[$countryCode] = ($regionCode == '') ? '' : $regionCode;
                                    $country['code'] = $countryCode;
                                    $country['name'] = $countryName;
                                    array_push($countryList, $country);
                                }
                                else
                                {
                                    if ($countryArray[$countryCode] != $regionCode)
                                    {
                                        // more than one region for this country
                                        $showRegionList = 1;
                                    }
                                }

                                // see if we have more than one store group
                                if (($storeGroup != '') && ($storeGroup != $siteGroupCode))
                                {
                                    $showStoreGroups = 1;
                                }
                                else
                                {
                                    $storeGroup = $siteGroupCode;
                                }
                            }
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }

            $dbObj->close();
        }

        if ($shippingMethod['allowgroupingbycountry'] == 0)
        {
            $showCountryList = 0;
        }
        else
        {
            $showCountryList = (count($countryList) > 1) ? 1 : 0;
        }

        if ($shippingMethod['allowgroupingbyregion'] == 0)
        {
            $showRegionList = 0;
        }

        if ($shippingMethod['allowgroupingbystoregroupname'] == 0)
        {
            $showStoreGroups = 0;
        }
        else
        {
            $storeGroupLabel = LocalizationObj::getLocaleString($shippingMethod['sitegrouplabel'], UtilsObj::getBrowserLocale(), true);
        }

		$showStoreListOnOpen = $shippingMethod['showstorelistonopen'];
		$ajaxCommand = 'STORELOCATOR';
        $external = 0;

		UtilsObj::includeStoreLocatorScript();

		if (method_exists('EDL_StoreLocatorObj', 'configure') && ($gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['useScript']))
		{
			$paramArray = array();
			$paramArray['allowgroupingbycountry'] = $showCountryList;
			$paramArray['allowgroupingbyregion'] = $showRegionList;
			$paramArray['allowgroupingbystoregroup'] = $showStoreGroups;
			$paramArray['storegrouplabel'] = $storeGroupLabel;
			$paramArray['searchfieldlabel'] = $searchFieldLabel;
			$paramArray['billingcustomercountrycode'] = $gSession['order']['billingcustomercountrycode'];
			$paramArray['billingcustomeraddress1'] = $gSession['order']['billingcustomeraddress1'];
			$paramArray['billingcustomeraddress2'] = $gSession['order']['billingcustomeraddress2'];
			$paramArray['billingcustomeraddress3'] = $gSession['order']['billingcustomeraddress3'];
			$paramArray['billingcustomeraddress4'] = $gSession['order']['billingcustomeraddress4'];
			$paramArray['billingcustomercity'] = $gSession['order']['billingcustomercity'];
			$paramArray['billingcustomerregioncode'] = $gSession['order']['billingcustomerregioncode'];
			$paramArray['billingcustomerpostcode'] = $gSession['order']['billingcustomerpostcode'];
			$paramArray['groupcode'] = $gSession['licensekeydata']['groupcode'];
			$paramArray['groupdata'] = $gSession['licensekeydata']['groupdata'];
			$paramArray['webbrandcode'] = $gSession['webbrandcode'];
			$paramArray['shippingmethodcode'] = $shippingMethodCode;
			$paramArray['browserlanguagecode'] = $gSession['browserlanguagecode'];
			$paramArray['privatedata'] = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['privateData'];

			$settings = EDL_StoreLocatorObj::configure($paramArray);

			$showCountryList = $settings['allowgroupingbycountry'];
			$showRegionList = $settings['allowgroupingbyregion'];
			$showStoreGroups = $settings['allowgroupingbystoregroup'];
			$storeGroupLabel = $settings['storegrouplabel'];
			$searchFieldLabel = $settings['searchfieldlabel'];
			$useExternalData = $settings['useexternaldata'];
			$defaultSearch = $settings['defaultsearch'];
			$privateData = $settings['privatedata'];
			$showStoreListOnOpen = $settings['showstorelistonopen'];
			$ajaxCommand = 'STORELOCATOREXTERNAL';
			$external = 1;

			$gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['useExternalData'] = $useExternalData;
			$gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['privateData'] = $privateData;

			if ($gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeLocator']['filter'] == '')
			{
				$gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeLocator']['filter'] = $defaultSearch;
			}
			DatabaseObj::updateSession();
		}

        $ajaxDivHeight = 350;
        if ($showCountryList + $showRegionList > 0)
        {
            $ajaxDivHeight -= 50;
        }
        $ajaxDivHeight -= $showStoreGroups * 50;

        $resultArray['ajaxCommand'] = $ajaxCommand;
        $resultArray['external'] = $external;
        $resultArray['fieldLabel'] = $searchFieldLabel;
        $resultArray['countryList'] = $countryList;
        $resultArray['showCountryList'] = $showCountryList;
        $resultArray['storeLocations'] = $storeLocations;
        $resultArray['showRegionList'] = $showRegionList;
        $resultArray['showStoreGroups'] = $showStoreGroups;
        $resultArray['ajaxDivHeight'] = $ajaxDivHeight;
        $resultArray['storeGroupLabel'] = $storeGroupLabel;
        $resultArray['shippingRateCode'] = $shippingRateCode;
        $resultArray['logoUrl'] = $logoSource;
        $resultArray['logoWidth'] = $logoWidth;
        $resultArray['logoHeight'] = $logoHeight;
		$resultArray['showstorelistonopen'] = $showStoreListOnOpen;

		$initialfilter = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeLocator'];
		$filter = $initialfilter['filter'];
		$privateFilter = $initialfilter['privateFilter'];

		// load the store list by default
		$country = '';
		if ($showCountryList == 0)
		{
			$country = '';
		}
		else
		{
			$country = $initialfilter['country'];
		}

		if ($showRegionList == 0)
		{
			$region = '';
		}
		else
		{
			$region = $initialfilter['region'];
		}

		if ($showStoreGroups == 0)
		{
			$storeGroup = '';
		}
		else
		{
			$storeGroup = $initialfilter['storeGroup'];
		}

		if ($ajaxCommand == 'STORELOCATOR')
		{
			if ($shippingMethod['showstorelistonopen'])
			{
				$resultArray['storelist'] = self::storeLocator($country, $region, $storeGroup, $filter, $privateFilter, true);
			}
		}
		else
		{
			if ($showStoreListOnOpen)
			{
				$resultArray['storelist'] = self::storeLocatorExternal($country, $region, $storeGroup, $filter, $privateFilter);
			}
		}

        $storeLocationsDataArray = array(
            'countrycode' => array(),
            'countryname' => array(),
            'regioncode' => array(),
            'regionname' => array(),
            'sitegroupcode' => array(),
            'sitegroupname' => array()
        );

        foreach ($storeLocations as $aStore)
        {
            $storeLocationsDataArray['countrycode'][] = $aStore['countryCode'];
            $storeLocationsDataArray['countryname'][] = $aStore['countryName'];
            $storeLocationsDataArray['regioncode'][] = $aStore['regionCode'];
            $storeLocationsDataArray['regionname'][] = $aStore['regionName'];
            $storeLocationsDataArray['sitegroupcode'][] = $aStore['siteGroupCode'];
            $storeLocationsDataArray['sitegroupname'][] = $aStore['siteGroupName'];
        }

        $resultArray['storelocationdata'] = $storeLocationsDataArray;

        return $resultArray;
    }

    static function selectStore()
    {
        global $gSession;

        $resultArray = Array();

        $resultArray['previousstage'] = $_GET['previousstage'];
        $resultArray['stage'] = $_GET['stage'];

        $storeCode = $_GET['storecode'];
        $isExternalStore = $_GET['externalstore'];

        $shippingMethodCode = $gSession['shipping'][0]['shippingmethodcode'];
        $previousStoreCode = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeCode'];

        $gSession['shipping'][0]['storeid'] = $storeCode;
        $gSession['shipping'][0]['collectfromstore'] = true;
        $gSession['shipping'][0]['payinstoreallowed'] = $_GET['payinstoreallowed'];
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeCode'] = $storeCode;
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['externalstore'] = $isExternalStore;
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['collectFromStore'] = true;

        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeLocator']['country'] = $_GET['country'];
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeLocator']['region'] = $_GET['region'];
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeLocator']['storeGroup'] = $_GET['storegroup'];
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeLocator']['filter'] = $_GET['filter'];
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeLocator']['privateFilter'] = $_GET['privatefilter'];

		UtilsObj::includeStoreLocatorScript();

		if (method_exists('EDL_StoreLocatorObj', 'getStoreInformation') && ($gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['useScript']))
		{
			$paramArray = array();
			$paramArray['storecode'] = $storeCode;
			$paramArray['groupcode'] = $gSession['licensekeydata']['groupcode'];
			$paramArray['groupdata'] = $gSession['licensekeydata']['groupdata'];
			$paramArray['webbrandcode'] = $gSession['webbrandcode'];
			$paramArray['shippingmethodcode'] = $shippingMethodCode;
			$paramArray['browserlanguagecode'] = $gSession['browserlanguagecode'];
			$paramArray['search'] = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeLocator']['filter'];
			$paramArray['privatesearch'] = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeLocator']['privateFilter'];
			$paramArray['privatedata'] = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['privateData'];

			$address = EDL_StoreLocatorObj::getStoreInformation($paramArray);

			$companyName = $address['name'];
			$address1 = $address['address1'];
			$address2 = $address['address2'];
			$address3 = $address['address3'];
			$address4 = $address['address4'];
			$city = $address['city'];
			$county = $address['county'];
			$state = $address['state'];
			$regionCode = $address['regioncode'];
			$postCode = $address['postcode'];
			$countryCode = $address['countrycode'];
			$countryName = $address['countryname'];
			$distributionCentreCode = $address['distributioncentrecode'];
			$telephoneNumber = $address['telephonenumber'];
			$emailAddress = $address['emailaddress'];
			$storeURL = $address['storeurl'];

			$countryRecord = UtilsAddressObj::getCountry($countryCode);
			$region = $countryRecord['region'];

			// replace the pay in store flag for the collect from store shipping method for the one defined by the store locator
			$gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['payInStoreAllowed'] = $address['payinstoreallowed'];

			DatabaseObj::updateSession();
		}
		else
		{
			//  get data from Taopix store
			$dbObj = DatabaseObj::getGlobalDBConnection();
			if ($dbObj)
			{
				if ($stmt = $dbObj->prepare('SELECT `name`, `address1`, `address2`, `address3`, `address4`, `city`, `county`, `state`, `regioncode`, `region`,
											`postcode`, `countrycode`, `countryname`, `distributioncentrecode`, `telephonenumber`, `emailaddress` FROM `SITES` WHERE `code` = ?;'))
				{
					if ($stmt->bind_param('s', $storeCode))
					{
						if ($stmt->bind_result($companyName, $address1, $address2, $address3, $address4, $city, $county, $state,
										$regionCode, $region, $postCode, $countryCode, $countryName, $distributionCentreCode, $telephoneNumber, $emailAddress))
						{
							if ($stmt->execute())
							{
								if ($stmt->fetch())
								{

								}
							}
						}
					}
					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}

				$dbObj->close();
			}
		}

        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['distributionCentreCode'] = $distributionCentreCode;
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomername'] = $companyName;
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomeraddress1'] = $address1;
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomeraddress2'] = $address2;
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomeraddress3'] = $address3;
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomeraddress4'] = $address4;
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomercity'] = $city;
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomercounty'] = $county;
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomerstate'] = $state;
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomerregioncode'] = $regionCode;
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomerregion'] = $region;
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomerpostcode'] = $postCode;
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomercountrycode'] = $countryCode;
        $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomercountryname'] = $countryName;

        //if a store has not been chosen prior to this then use billing contact information
        if ($previousStoreCode == '')
        {
            $storecontactfirstname = $gSession['order']['billingcontactfirstname'];
            $storecontactlastname = $gSession['order']['billingcontactlastname'];
            $storecontacttelephone = $gSession['order']['billingcustomertelephonenumber'];
            $storecontactemail = $gSession['order']['billingcustomeremailaddress'];

            $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomeremailaddress'] = $storecontactemail;
            $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomertelephonenumber'] = $storecontacttelephone;
            $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecontactfirstname'] = $storecontactfirstname;
            $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecontactlastname'] = $storecontactlastname;
        }

        DatabaseObj::updateSession();

        $resultArray['countrylist'] = UtilsAddressObj::getCountryList();

        return $resultArray;
    }

	static function storeLocator($pCountry, $pRegion, $pStoregroup, $pFilter, $pPrivateFilter, $pFormatAddress)
    {
        // get all stores for supplied parameters
        global $gSession;

        $shippingRateCode = $gSession['shipping'][0]['shippingratecode'];
        $shippingMethodCode = $gSession['shipping'][0]['shippingmethodcode'];

        $store = array();
        $storeList = array();
        $filteredList = array();
		$siteCode = '';
		$companyName = '';
		$address1 = '';
		$address2 = '';
		$address3 = '';
		$address4 = '';
		$city = '';
		$county = '';
		$state = '';
		$regionCode = '';
		$region = '';
		$postCode = '';
		$countryCode = '';
		$countryName = '';
		$longitude = '';
		$latitude = '';
		$isExternalStore = 0;
		$currentCountry = '';
		$addressFormat = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
			$sql = 'SELECT DISTINCT s.code, s.name, s.address1, s.address2, s.address3, s.address4, s.city, s.county, s.state, s.regioncode, s.region,
						s.postcode, s.countrycode, s.countryname, s.locationlon, s.locationlat, s.isexternalstore
					FROM `SITES` s
						INNER JOIN `SHIPPINGRATESITES` sr ON sr.sitegroupcode = s.sitegroup
					WHERE (s.active = 1)
						AND (s.sitetype = ' . TPX_SITE_TYPE_STORE . ')
						AND ((s.countrycode = ?) OR (? = ""))
						AND ((s.regioncode = ?) OR (? = ""))
						AND ((sr.sitegroupcode = ?) OR (? = ""))
						AND (sr.shippingratecode = ?);';

			$stmt = $dbObj->prepare($sql);

            if ($stmt)
            {
                if ($stmt->bind_param('sssssss', $pCountry, $pCountry, $pRegion, $pRegion, $pStoregroup, $pStoregroup, $shippingRateCode))
                {
                    if ($stmt->bind_result($siteCode, $companyName, $address1, $address2, $address3, $address4, $city, $county, $state,
                                    $regionCode, $region, $postCode, $countryCode, $countryName, $longitude, $latitude, $isExternalStore))
                    {
                        if ($stmt->execute())
                        {
                            while ($stmt->fetch())
                            {
								// Reset the formatted address.
								$address = '';

								// Make sure the address will not be formated later.
								if ($pFormatAddress)
								{
									// Get the address fromat.
									if ($currentCountry != $countryCode)
									{
										// Keep the current active country.
										$currentCountry = $countryCode;

										// format either the billing or shipping order address
										// the delimiter can be used to format it for HTML or text emails
										$addressFormat = UtilsAddressObj::getAddressDisplayFormat($countryCode);
									}

									$addArray['[firstname]'] = '';
									$addArray['[lastname]'] = '';
									$addArray['[company]'] = '';
									$addArray['[add1]'] = $address1;
									$addArray['[add2]'] = $address2;
									$addArray['[add3]'] = $address3;
									$addArray['[add4]'] = $address4;
									$addArray['[city]'] = $city;
									$addArray['[county]'] = $county;
									$addArray['[state]'] = $state;
									$addArray['[regioncode]'] = $regionCode;
									$addArray['[region]'] = $region;
									$addArray['[postcode]'] = $postCode;
									$addArray['[country]'] = $countryName;

									$address = UtilsAddressObj::formatAddressData($addArray, $addressFormat, ',');

									// remove trailing coma
									while (substr($address, -1) == ',')
									{
										$address = substr($address, 0, -1);
									}

									// space after comma
									$address = str_replace(',', ', ', $address);
								}

                                $store['address'] = $address;
                                $store['name'] = $companyName;
                                $store['code'] = $siteCode;
                                $store['payinstoreallowed'] = TPX_PAY_IN_STORE_DO_NOT_ALLOW;
                                $store['address1'] = $address1;
                                $store['address2'] = $address2;
								$store['address3'] = $address3;
								$store['address4'] = $address4;
								$store['city'] = $city;
								$store['county'] = $county;
								$store['state'] = $state;
								$store['regioncode'] = $regionCode;
								$store['postcode'] = $postCode;
								$store['countrycode'] = $countryCode;
								$store['countryname'] = $countryName;
								$store['locationlon'] = $longitude;
								$store['locationlat'] = $latitude;
								$store['locationlat'] = $latitude;
								$store['externalstore'] = $isExternalStore;

                                // filter unless external filter is to be used
                                if ($gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['useExternalData'] == TPX_EDL_CFS_SEARCH_FILTERED)
                                {
                                    // only add if search string present
                                    if (($pFilter == '') || (stripos($address, $pFilter) !== false) || (stripos($companyName, $pFilter) !== false))
                                    {
                                        $storeList[] = $store;
                                    }
                                }
                                else
                                {
                                    $storeList[] = $store;
                                }
                            }
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            $dbObj->close();
        }

		$filteredList = Array();
		$shippingMethodCode = $gSession['shipping'][0]['shippingmethodcode'];

		UtilsObj::includeStoreLocatorScript();

		if (method_exists('EDL_StoreLocatorObj', 'getExternalStoreList') && ($gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['useScript']))
		{
			$paramArray = array();
			$paramArray['billingcustomercountrycode'] = $gSession['order']['billingcustomercountrycode'];
			$paramArray['billingcustomeraddress1'] = $gSession['order']['billingcustomeraddress1'];
			$paramArray['billingcustomeraddress2'] = $gSession['order']['billingcustomeraddress2'];
			$paramArray['billingcustomeraddress3'] = $gSession['order']['billingcustomeraddress3'];
			$paramArray['billingcustomeraddress4'] = $gSession['order']['billingcustomeraddress4'];
			$paramArray['billingcustomercity'] = $gSession['order']['billingcustomercity'];
			$paramArray['billingcustomerregioncode'] = $gSession['order']['billingcustomerregioncode'];
			$paramArray['billingcustomerpostcode'] = $gSession['order']['billingcustomerpostcode'];
			$paramArray['groupcode'] = $gSession['licensekeydata']['groupcode'];
			$paramArray['groupdata'] = $gSession['licensekeydata']['groupdata'];
			$paramArray['webbrandcode'] = $gSession['webbrandcode'];
			$paramArray['shippingmethodcode'] = $shippingMethodCode;
			$paramArray['browserlanguagecode'] = $gSession['browserlanguagecode'];
			$paramArray['search'] = $pFilter;
			$paramArray['privatesearch'] = $pPrivateFilter;
			$paramArray['groupingcountrycode'] = $pCountry;
			$paramArray['groupingregioncode'] = $pRegion;
			$paramArray['groupingstoregroupcode'] = $pStoregroup;
			$paramArray['storelist'] = $storeList;
			$paramArray['privatedata'] = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['privateData'];

			$results = EDL_StoreLocatorObj::getExternalStoreList($paramArray);

			$gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['privateData'] = $results['privatedata'];
			DatabaseObj::updateSession();

			if ($results['resulttype'] == TPX_EDL_CFS_DATA_TYPE_STORE)
			{
				$filteredList = array();
				$filteredList['resulttype'] = $results['resulttype'];
				$filteredList['result'] = $results['result'];
				$filteredList['resultlist'] = array();
				$filteredList['privatedata'] = $results['privatedata'];

				foreach ($results['resultlist'] as $scriptStore)
				{
					$storeFound = false;

					foreach ($storeList as $taopixStore)
					{
						if ($scriptStore['code'] == $taopixStore['code'])
						{
							$storeFound = true;
							if ($taopixStore['externalstore'] == 0)
							{
								// force store details from taopix store as this is an internal
								// store and should not be modified.
								$filteredList['resultlist'][] = $taopixStore;
							}
							else
							{
								// update store info returned with details from script
								$filteredList['resultlist'][] = $scriptStore;
							}

							break;
						}
					}

					if ($storeFound == false)
					{
						$scriptStore['externalstore'] = 1;
						$filteredList['resultlist'][] = $scriptStore;
					}
				}

				return $filteredList;
			}
			else
			{
				return $results;
			}
		}
		else
		{
			return $storeList;
		}

    }

	static function storeLocatorExternal($pCountry, $pRegion, $pStoregroup, $pFilter, $pPrivateFilter)
    {
        $resultArray = array();
        $resultArray['result'] = '';
        $resultArray['resultParam'] = '';
        $storeList = array();

		$resultArray = self::storeLocator($pCountry, $pRegion, $pStoregroup, $pFilter, $pPrivateFilter, false);

		// is it a list of stores, or a list of groups
		switch ($resultArray['resulttype'])
		{
			case TPX_EDL_CFS_DATA_TYPE_STORE:
				// format address
				$storeList = $resultArray['resultlist'];
				$itemCount = count($storeList);
				$currentCountry = '';
				$addressFormat = '';

				for ($i = 0; $i < $itemCount; $i++)
				{
					$store = $storeList[$i];

					// Get the format for the current used country.
					if ($store['countrycode'] != $currentCountry)
					{
						// Keep the current active country.
						$currentCountry = $store['countrycode'];

						// format either the billing or shipping order address
						// the delimiter can be used to format it for HTML or text emails
						$addressFormat = UtilsAddressObj::getAddressDisplayFormat($currentCountry);
					}

					$addArray['[firstname]'] = '';
					$addArray['[lastname]'] = '';
					$addArray['[company]'] = '';
					$addArray['[add1]'] = $store['address1'];
					$addArray['[add2]'] = $store['address2'];
					$addArray['[add3]'] = $store['address3'];
					$addArray['[add4]'] = $store['address4'];
					$addArray['[city]'] = $store['city'];
					$addArray['[county]'] = $store['county'];
					$addArray['[state]'] = $store['state'];
					$addArray['[regioncode]'] = $store['regioncode'];
					$addArray['[region]'] = 'STATE';
					$addArray['[postcode]'] = $store['postcode'];
					$addArray['[country]'] = $store['countryname'];
					$address = UtilsAddressObj::formatAddressData($addArray, $addressFormat, ',');

					// remove trailing spaces
					while (substr($address, -1) == ',')
					{
						$address = substr($address, 0, -1);
					}

					// space after comma
					$address = str_replace(',', ', ', $address);

					$storeList[$i]['address'] = $address;
				}
				$resultArray['resultlist'] = $storeList;
				break;
			case TPX_EDL_CFS_DATA_TYPE_GROUP:
				break;
			case TPX_EDL_CFS_DATA_TYPE_ERROR:
			default:
				$resultArray['result'] = UtilsObj::utf8ToHtmlCodepoints($resultArray['result']);
		}
        return $resultArray;
    }

    static function changeAddressDisplay()
    {
        global $gSession;

        $resultArray = Array();

        $gSession['shipping'][0]['shippingratecode'] = UtilsObj::getPOSTParam('shippingratecode', $gSession['shipping'][0]['shippingratecode']);
        $gSession['order']['sameshippingandbillingaddress'] = UtilsObj::getPOSTParam('sameshippingandbillingaddress', $gSession['order']['sameshippingandbillingaddress']);

        $resultArray['previousstage'] = UtilsObj::getPOSTParam('previousstage');
        $resultArray['stage'] = UtilsObj::getPOSTParam('stage');

        $resultArray['countrylist'] = UtilsAddressObj::getCountryList();

        DatabaseObj::updateSession();

        return $resultArray;
    }


    static function changeShippingAddressDisplay()
    {
    	global $gSession;

    	self::storeOrderMetaData($_POST['stage']);

		// re-check the voucher to make sure its usage status hasn't changed
		self::checkVoucher();
		$resultArray = self::changeAddressDisplay();

		$updateAddressMode = TPX_UPDATEADDRESSMODE_NA;

		if ($gSession['useraddressupdated'] == 0)
		{
			$updateAddressMode = TPX_UPDATEADDRESSMODE_RESPECT;
		}
		elseif ($gSession['useraddressupdated'] == 2)
		{
			$updateAddressMode = TPX_UPDATEADDRESSMODE_FULL;
		}

		$resultArray['updateaddressmode'] = $updateAddressMode;

		return $resultArray;
    }


    static function changeShippingAddress()
    {
    	global $gSession;
        global $gConstants;

        // re-check the voucher to make sure its usage status hasn't changed
		self::checkVoucher();

		$previousAddressUpdatedSetting = $gSession['useraddressupdated'];
		self::changeAddress('shipping');

		$resultArray = self::orderRefresh();

		if ((($gSession['order']['sameshippingandbillingaddress'] == 1) && ($gSession['order']['canmodifybillingaddress'] == 1) &&
				($gSession['shipping'][0]['shippingmethodusedefaultbillingaddress'] == 0)) || ($previousAddressUpdatedSetting == 2))
		{
			self::copyShippingAddressToBillingAddress();
			self::formatOrderAddresses($resultArray);
		}
		elseif ($gConstants['taxaddress'] == TPX_TAX_CALCULATION_BY_SHIPPING_ADDRESS)
		{
			self::updateOrderTaxRate();
		}

		return $resultArray;
    }


    static function changeBillingAddressDisplay()
    {
    	self::storeOrderMetaData($_POST['stage']);

		// re-check the voucher to make sure its usage status hasn't changed
		self::checkVoucher();

		$resultArray = Order_model::changeAddressDisplay();
		$resultArray['updateaddressmode'] = TPX_UPDATEADDRESSMODE_NA;

		return $resultArray;
    }


    static function changeBillingAddress()
    {
		global $gConstants;

		// re-check the voucher to make sure its usage status hasn't changed
		self::checkVoucher();

		self::changeAddress('billing');

		if ($gConstants['taxaddress'] == TPX_TAX_CALCULATION_BY_BILLING_ADDRESS)
		{
			self::updateOrderTaxRate();
		}

		return self::orderRefresh();
    }


	static function changeAddress($pAddressType)
	{
		global $gSession;

		$updateAddress = false;
        $shippingcfscontact = (int) UtilsObj::getPOSTParam('shippingcfscontact',0);

		// determine if we are updating the shipping or billing details and if we can update the address
		if ($pAddressType == 'shipping')
		{
			$addressArray = &$gSession['shipping'][0];

			if (($gSession['shipping'][0]['shippingmethodusedefaultshippingaddress'] == 0) &&
				($gSession['order']['canmodifyshippingaddress'] == 1) && $shippingcfscontact == 0)
			{
				$updateAddress = true;
			}
		}
		else
		{
			$addressArray = &$gSession['order'];

			if (($gSession['shipping'][0]['shippingmethodusedefaultbillingaddress'] == 0) &&
				($gSession['order']['canmodifybillingaddress'] == 1))
			{
				$updateAddress = true;
			}
		}

        if ($shippingcfscontact == 0)
        {
            // set the values that can always be changed
            // update the session contact details
            $addressArray[$pAddressType . 'contactfirstname'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('contactfname'));
            $addressArray[$pAddressType . 'contactlastname'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('contactlname'));
            $addressArray[$pAddressType . 'customertelephonenumber'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('telephonenumber'));
            $addressArray[$pAddressType . 'customeremailaddress'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('email'));
            $addressArray[$pAddressType . 'registeredtaxnumbertype'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('registeredtaxnumbertype', 0));
            $addressArray[$pAddressType . 'registeredtaxnumber'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('registeredtaxnumber'));
        }
        else
        {
            $shippingMethodCode = $gSession['shipping'][0]['shippingmethodcode'];
            $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomeremailaddress'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('email'));
            $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomertelephonenumber'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('telephonenumber'));
            $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecontactfirstname'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('contactfname'));
            $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecontactlastname'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('contactlname'));
        }

		if ($updateAddress)
		{
			$countryCode = UtilsObj::getPOSTParam('countrycode');
			$countryCode = UtilsObj::cleanseInput($countryCode);

			// see if there are special address fields like
			// add1=[add41], [add42] - [add43]
			// meaning address1 = add41 + ", "  + add42 + " - " + add43
			// and     address4 = add41 + "<p>" + add42 + "<p>" + add43
			$specialAddressFields = UtilsAddressObj::specialAddressFields($countryCode);

			// update the session address
			$addressArray[$pAddressType . 'customername'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('companyname'));
			$addressArray[$pAddressType . 'customeraddress1'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('address1'));
			$addressArray[$pAddressType . 'customeraddress2'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('address2'));
			$addressArray[$pAddressType . 'customeraddress3'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('address3'));

			// we need to check to see if the string contains @@TAOPIXTAG@@. If it does then this means that it is a special address field.
			// we then need to convert @@TAOPIXTAG@@ back to a <p> so that it can be stored correctly in the database.
			$addressArray[$pAddressType . 'customeraddress4'] = implode('<p>', mb_split('@@TAOPIXTAG@@', UtilsObj::cleanseInput(UtilsObj::getPOSTParam('address4'))));

			$addressArray[$pAddressType . 'customercity'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('city'));
			$addressArray[$pAddressType . 'customercounty'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('county'));
			$addressArray[$pAddressType . 'customerstate'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('state'));
			$addressArray[$pAddressType . 'customerregioncode'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('regioncode'));
			$addressArray[$pAddressType . 'customerregion'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('region'));
			$addressArray[$pAddressType . 'customerpostcode'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('postcode'));
			$addressArray[$pAddressType . 'customercountrycode'] = $countryCode;
			$addressArray[$pAddressType . 'customercountryname'] = UtilsObj::cleanseInput(UtilsObj::getPOSTParam('countryname'));
		}
	}


	static function copyShippingAddress()
	{
		self::storeOrderMetaData($_POST['stage']);

		// re-check the voucher to make sure its usage status hasn't changed
		self::checkVoucher();

		self::copyShippingAddressToBillingAddress();

		return self::orderRefresh();
	}


	static function updateAccountDetails()
	{
		global $gSession;

		// include the customer model and use it's account update feature
		require_once('../Customer/Customer_model.php');

		$resultArray = Customer_model::updateAccountDetails(TPX_CUSTOMER_ACCOUNT_OVERRIDE_REASON_CUSTOMERUPDATEDETAILSORDERPROMPT);

		if (($resultArray['result'] == '') || ($resultArray['result'] == 'str_LabelAccountDetailsUpdated'))
		{
			// retrieve the license key and user account so that we can re-initialise the cart addresses
			$userAccountArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
			$licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($gSession['licensekeydata']['groupcode']);
            self::initializeCartShippingAndBillingAddress($licenseKeyArray, $userAccountArray);

            if ($userAccountArray['usedefaultcurrency'] == 1)
            {
                // account uses default, that means we go by licence key
                $currencyCode = $licenseKeyArray['currencycode'];
            }
            else
            {
                $currencyCode = $userAccountArray['currencycode'];
            }

            // we only want to update the currency if it is not the same as the order session currecny.
            // the customer account API may have changed the currecny assigned to a user after they have updated their details.
            if ($currencyCode != $gSession['order']['currencycode'])
            {
                self::updateOrderSessionCurrency($currencyCode);
            }

			// update the session to say that the address has been updated and we don't need to prompt
			$gSession['useraddressupdated'] = 1;
			$gSession['promptforaddress'] = false;
		}

		return self::orderRefresh();
    }

    static function updateOrderSessionCurrency($pCurrencyCode)
    {
        global $gSession;

        $currencyArray = DatabaseObj::getCurrency($pCurrencyCode);

        if ($currencyArray['recordid'] > 0)
        {
            $gSession['order']['currencycode'] = $pCurrencyCode;
            $gSession['order']['currencyname'] = $currencyArray['name'];;
            $gSession['order']['currencyisonumber'] = $currencyArray['isonumber'];
            $gSession['order']['currencydecimalplaces'] = $currencyArray['decimalplaces'];
            $gSession['order']['currencysymbol'] = $currencyArray['symbol'];
            $gSession['order']['currencysymbolatfront'] = $currencyArray['symbolatfront'];
            $gSession['order']['currencyexchangerate'] = $currencyArray['exchangerate'];

            DatabaseObj::updateSession();
        }
    }


    static function copyShippingAddressToBillingAddress()
    {
        global $gSession;

        $gSession['order']['sameshippingandbillingaddress'] = 1;

        self::copyArrayAddressFields($gSession['shipping'][0], 'shipping', $gSession['order'], 'billing', true, false);

        self::updateOrderTaxRate();
    }


    static function updateOrderTaxRate()
    {
        global $gSession;
        global $gConstants;

        $useTaxAPIScript = false;
        $taxBreakdownArray = Array();
        $existingTaxRatesArray = Array();
        $useProductTaxFromZone = true;
        $useShippingTaxFromZone = true;
        $itemTaxRateArray = Array();

        $licenseKeyArray = DatabaseObj::getLicenseKeyFromCode($gSession['licensekeydata']['groupcode']);
        $userAccountArray = DatabaseObj::getUserAccountFromID($gSession['userid']);

        // if the user or license key have tax rates set find them now
        if ($userAccountArray['taxcode'] != '')
        {
            $itemTaxRateArray = DatabaseObj::getTaxRate($userAccountArray['taxcode']);
            if ($itemTaxRateArray['result'] == '')
            {
                $useProductTaxFromZone = false;
            }
        }
        else
        {
            if ($licenseKeyArray['taxcode'] != '')
            {
                $itemTaxRateArray = DatabaseObj::getTaxRate($licenseKeyArray['taxcode']);
                if ($itemTaxRateArray['result'] == '')
                {
                    $useProductTaxFromZone = false;
                }
            }
        }

        if ($userAccountArray['shippingtaxcode'] != '')
        {
            $shippingTaxRateArray = DatabaseObj::getTaxRate($userAccountArray['shippingtaxcode']);
            if ($shippingTaxRateArray['result'] == '')
            {
                $useShippingTaxFromZone = false;
            }
        }
        else
        {
            if ($licenseKeyArray['shippingtaxcode'] != '')
            {
                $shippingTaxRateArray = DatabaseObj::getTaxRate($licenseKeyArray['shippingtaxcode']);
                if ($shippingTaxRateArray['result'] == '')
                {
                    $useShippingTaxFromZone = false;
                }
            }
        }


        // now find the rates for the tax zone
        $taxZoneData = DatabaseObj::getTaxZoneDataFromSession();
        $taxZoneItemLevel1TaxRateArray = DatabaseObj::getTaxRate($taxZoneData['producttaxlevel1']);
        if ($taxZoneData['producttaxlevel2'] != '')
        {
            $taxZoneItemLevel2TaxRateArray = DatabaseObj::getTaxRate($taxZoneData['producttaxlevel2']);
        }
        else
        {
            $taxZoneItemLevel2TaxRateArray = $taxZoneItemLevel1TaxRateArray;
        }

        if ($taxZoneData['producttaxlevel3'] != '')
        {
            $taxZoneItemLevel3TaxRateArray = DatabaseObj::getTaxRate($taxZoneData['producttaxlevel3']);
        }
        else
        {
            $taxZoneItemLevel3TaxRateArray = $taxZoneItemLevel1TaxRateArray;
        }

        if ($taxZoneData['producttaxlevel4'] != '')
        {
            $taxZoneItemLevel4TaxRateArray = DatabaseObj::getTaxRate($taxZoneData['producttaxlevel4']);
        }
        else
        {
            $taxZoneItemLevel4TaxRateArray = $taxZoneItemLevel1TaxRateArray;
        }

        if ($taxZoneData['producttaxlevel5'] != '')
        {
            $taxZoneItemLevel5TaxRateArray = DatabaseObj::getTaxRate($taxZoneData['producttaxlevel5']);
        }
        else
        {
            $taxZoneItemLevel5TaxRateArray = $taxZoneItemLevel1TaxRateArray;
        }

        // we need to store these zone tax levels in the session so we can calculate orderfooter tax correctly.
        $gSession['order']['producttaxlevel1'] = $taxZoneItemLevel1TaxRateArray;
        $gSession['order']['producttaxlevel2'] = $taxZoneItemLevel2TaxRateArray;
        $gSession['order']['producttaxlevel3'] = $taxZoneItemLevel3TaxRateArray;
        $gSession['order']['producttaxlevel4'] = $taxZoneItemLevel4TaxRateArray;
        $gSession['order']['producttaxlevel5'] = $taxZoneItemLevel5TaxRateArray;

        if ($useShippingTaxFromZone)
        {
            $taxZoneShippingRateArray = DatabaseObj::getTaxRate($taxZoneData['shippingtaxcode']);
            $shippingTaxRateArray = $taxZoneShippingRateArray;
        }

		if (file_exists("../Customise/scripts/EDL_TaxCalculation.php"))
		{
			require_once('../Customise/scripts/EDL_TaxCalculation.php');

			if (method_exists('TaxCalculationAPI', 'getProductTaxRateInit'))
			{
				$useTaxAPIScript = true;

				$paramArray = array();
				$paramArray['brandcode'] = $gSession['webbrandcode'];
				$paramArray['groupcode'] = $gSession['licensekeydata']['groupcode'];
				$paramArray['groupdata'] = $gSession['licensekeydata']['groupdata'];
				$paramArray['browserlanguagecode'] = $gSession['browserlanguagecode'];
				$paramArray['currencycode'] = $gSession['order']['currencycode'];
				$paramArray['currencyexchange'] = $gSession['order']['currencyexchangerate'];
				$paramArray['currencydecimalplaces'] = $gSession['order']['currencydecimalplaces'];
				$paramArray['taxcalculationaddress'] = array();
				$paramArray['customershippingaddress'] = array();
				$paramArray['customerbillingaddress'] = array();

				self::copyArrayAddressFields($gSession['shipping'][0], 'shippingcustomer', $paramArray['customershippingaddress'], 'shipping', false, true);
				self::copyArrayAddressFields($gSession['order'], 'billingcustomer', $paramArray['customerbillingaddress'], 'billing', false, true);

				if ($gConstants['taxaddress'] == TPX_TAX_CALCULATION_BY_BILLING_ADDRESS)
				{
					self::copyArrayAddressFields($gSession['order'], 'billingcustomer', $paramArray['taxcalculationaddress'], 'billing', false, true);
				}
				else
				{
					self::copyArrayAddressFields($gSession['shipping'][0], 'shippingcustomer', $paramArray['taxcalculationaddress'], 'shipping', false, true);
				}

				$shippingMethodCode = $gSession['shipping'][0]['shippingmethodcode'];
				if ($gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['collectFromStore'] == true) {
					self::copyArrayAddressFields(
						$gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress'],
						'storecustomer', $paramArray['storeaddress'], 'store', false, true);
				} else {
					$paramArray['storeaddress'] = [];
				}
			}
		}

        // determine the tax that we will apply to each line
        $orderItemsCount = count($gSession['items']);
        for ($i = 0; $i < $orderItemsCount; $i++)
        {
            $orderline = &$gSession['items'][$i];

            $lineUseProductTaxFromZone = $useProductTaxFromZone;
            $lineTaxRateArray = $itemTaxRateArray;

            if ($lineUseProductTaxFromZone)
            {
            	$lineTaxRateArray = self::getTaxRateArrayFromTaxLevel($orderline['itemproducttaxlevel']);
            }

            // if we are getting the product or shipping tax from the zone run the tax script
            if ($lineUseProductTaxFromZone)
            {
				if ($useTaxAPIScript)
				{
					$paramArray['taxratecode'] = $lineTaxRateArray['code'];
					$paramArray['productcollectioncode'] = $orderline['itemproductcollectioncode'];
					$paramArray['productcode'] = $orderline['itemproductcode'];

					$taxCodeResultArray = TaxCalculationAPI::getProductTaxRateInit($paramArray);

					if (($lineUseProductTaxFromZone) && (substr($taxCodeResultArray['producttaxrate'], 0, 13) == TPX_CUSTOMTAX))
					{
						$customTaxRateArray = TaxCalculationAPI::getProductTaxRate($paramArray);

						// we have been provided a custom tax rate so we do not get it from the zone
						$lineUseProductTaxFromZone = false;

						$lineTaxRateArray['code'] = $customTaxRateArray['customtaxdetails']['code'];
						$lineTaxRateArray['name'] = $customTaxRateArray['customtaxdetails']['description'];
						$lineTaxRateArray['rate'] = UtilsObj::bround($customTaxRateArray['customtaxdetails']['rate'], 4);
					}
					else if (($lineUseProductTaxFromZone) && ($taxCodeResultArray['producttaxrate'] != ''))
					{
						// if we are getting the line tax from the zone and the script has provided one then get the tax data for the code provided
						$lineTaxRateArray = DatabaseObj::getTaxRate($taxCodeResultArray['producttaxrate']);
						if ($lineTaxRateArray['result'] == '')
						{
							// we have a product tax rate so we do not get it from the zone
							$lineUseProductTaxFromZone = false;
						}
					}
				}
            }

            $orderline['itemtaxcode'] = $lineTaxRateArray['code'];
            $orderline['itemtaxname'] = $lineTaxRateArray['name'];
            $orderline['itemtaxrate'] = $lineTaxRateArray['rate'];

            // update the tax summary
            if (!array_key_exists($lineTaxRateArray['code'], $existingTaxRatesArray))
            {
                $taxBreakdownItem = Array();
                $taxBreakdownItem['taxratecode'] = $lineTaxRateArray['code'];
                $taxBreakdownItem['taxratename'] = $lineTaxRateArray['name'];
                $taxBreakdownItem['taxrate'] = $lineTaxRateArray['rate'];
                $taxBreakdownItem['nettotal'] = 0.00;
                $taxBreakdownItem['taxtotal'] = 0.00;
                $taxBreakdownArray[] = $taxBreakdownItem;

                $existingTaxRatesArray[$lineTaxRateArray['code']] = true;
            }
        }

        if ($useTaxAPIScript)
		{
			unset($paramArray['taxratecode']);
			unset($paramArray['productcollectioncode']);
			unset($paramArray['productcode']);

			$paramArray['shippingtaxratecode'] = $shippingTaxRateArray['code'];
			$paramArray['cartitems']['lineitems'] = $gSession['items'];
			$paramArray['cartitems']['orderfooteritems']['orderfootersections'] = $gSession['order']['orderFooterSections'];
			$paramArray['cartitems']['orderfooteritems']['orderfootercheckboxes'] = $gSession['order']['orderFooterCheckboxes'];
			$paramArray['shipping'] = array();

			$taxCodeResultArray = TaxCalculationAPI::getShippingTaxRateInit($paramArray);

			if (($useShippingTaxFromZone) && (substr($taxCodeResultArray['shippingtaxrate'], 0, 13) == TPX_CUSTOMTAX))
			{
				$shippingTaxRateArray['code'] = $taxCodeResultArray['shippingtaxrate'];
				$shippingTaxRateArray['name'] = '';
				$shippingTaxRateArray['rate'] = 0.0000;
			}
			else if (($useShippingTaxFromZone) && ($taxCodeResultArray['shippingtaxrate'] != ''))
			{
				$shippingTaxRateArray = DatabaseObj::getTaxRate($taxCodeResultArray['shippingtaxrate']);
				if ($shippingTaxRateArray['result'] == '')
				{
					// we have a shipping tax rate so we do not get it from the zone
					$useShippingTaxFromZone = false;
				}
			}
		}

        $gSession['shipping'][0]['shippingratetaxcode'] = $shippingTaxRateArray['code'];
        $gSession['shipping'][0]['shippingratetaxname'] = $shippingTaxRateArray['name'];
        $gSession['shipping'][0]['shippingratetaxrate'] = $shippingTaxRateArray['rate'];

        // we need to loop round the order footer sections and order footer checkboxes
        // checking to see if the tax rates are different
        foreach ($gSession['order']['orderFooterSections'] as &$component)
        {
            if ($useProductTaxFromZone)
            {
                $lineTaxRateArray = self::getTaxRateArrayFromTaxLevel($component['orderfootertaxlevel']);
            }

            if (!array_key_exists($lineTaxRateArray['code'], $existingTaxRatesArray))
            {
                $taxBreakdownItem = Array();
                $taxBreakdownItem['taxratecode'] = $lineTaxRateArray['code'];
                $taxBreakdownItem['taxratename'] = $lineTaxRateArray['name'];
                $taxBreakdownItem['taxrate'] = $lineTaxRateArray['rate'];
                $taxBreakdownItem['nettotal'] = 0.00;
                $taxBreakdownItem['taxtotal'] = 0.00;

                $taxBreakdownArray[] = $taxBreakdownItem;
            }

            // loop around sub-sections
            foreach ($component['subsections'] as &$subsection)
            {
                if ($useProductTaxFromZone)
                {
                    $lineTaxRateArray = self::getTaxRateArrayFromTaxLevel($subsection['orderfootertaxlevel']);
                }

                if (!array_key_exists($lineTaxRateArray['code'], $existingTaxRatesArray))
                {
                    $taxBreakdownItem = Array();
                    $taxBreakdownItem['taxratecode'] = $lineTaxRateArray['code'];
                    $taxBreakdownItem['taxratename'] = $lineTaxRateArray['name'];
                    $taxBreakdownItem['taxrate'] = $lineTaxRateArray['rate'];
                    $taxBreakdownItem['nettotal'] = 0.00;
                    $taxBreakdownItem['taxtotal'] = 0.00;

                    $taxBreakdownArray[] = $taxBreakdownItem;
                }
            }

            // loop around checkboxes
            foreach ($component['checkboxes'] as &$checkbox)
            {
                if ($checkbox['checked'])
                {
                    if ($useProductTaxFromZone)
                    {
                        $lineTaxRateArray = self::getTaxRateArrayFromTaxLevel($checkbox['orderfootertaxlevel']);
                    }

                    if (!array_key_exists($lineTaxRateArray['code'], $existingTaxRatesArray))
                    {
                        $taxBreakdownItem = Array();
                        $taxBreakdownItem['taxratecode'] = $lineTaxRateArray['code'];
                        $taxBreakdownItem['taxratename'] = $lineTaxRateArray['name'];
                        $taxBreakdownItem['taxrate'] = $lineTaxRateArray['rate'];
                        $taxBreakdownItem['nettotal'] = 0.00;
                        $taxBreakdownItem['taxtotal'] = 0.00;

                        $taxBreakdownArray[] = $taxBreakdownItem;
                    }
                }
            }
        }

        // loop around order footer checkboxes
        foreach ($gSession['order']['orderFooterCheckboxes'] as &$checkbox)
        {
            if ($checkbox['checked'])
            {
                if ($useProductTaxFromZone)
                {
                    $lineTaxRateArray = self::getTaxRateArrayFromTaxLevel($checkbox['orderfootertaxlevel']);
                }

                if (!array_key_exists($lineTaxRateArray['code'], $existingTaxRatesArray))
                {
                    $taxBreakdownItem = Array();
                    $taxBreakdownItem['taxratecode'] = $lineTaxRateArray['code'];
                    $taxBreakdownItem['taxratename'] = $lineTaxRateArray['name'];
                    $taxBreakdownItem['taxrate'] = $lineTaxRateArray['rate'];
                    $taxBreakdownItem['nettotal'] = 0.00;
                    $taxBreakdownItem['taxtotal'] = 0.00;

                    $taxBreakdownArray[] = $taxBreakdownItem;
                }
            }
        }

        $gSession['order']['ordertaxbreakdown'] = $taxBreakdownArray;
        $gSession['order']['ordertaxproductbreakdown'] = $taxBreakdownArray;
        $gSession['order']['fixedtaxrate'] = $lineTaxRateArray['code'];

        // update the order total
        self::updateOrderTotal();
    }

    static function updateOrderFooterTaxRate()
    {
        global $gSession;
        global $gConstants;

        $taxBreakdownArray = Array();
        $existingTaxRatesArray = Array();
        $useProductTaxFromZone = true;
        $useShippingTaxFromZone = true;
        $itemTaxRateArray = Array();
        {
            // we need to loop round the order footer sections and order footer checkboxes
            // checking to see if the tax rates are different
            foreach ($gSession['order']['orderFooterSections'] as &$component)
            {
                $lineTaxRateArray = self::getTaxRateArrayFromTaxLevel($component['orderfootertaxlevel']);

                if (!in_array($lineTaxRateArray['code'], $taxBreakdownArray))
                {
                    $taxBreakdownArray[] = $lineTaxRateArray['code'];
                }

                // loop around sub-sections
                foreach ($component['subsections'] as &$subsection)
                {
                    $lineTaxRateArray = self::getTaxRateArrayFromTaxLevel($subsection['orderfootertaxlevel']);

                    if (!in_array($lineTaxRateArray['code'], $taxBreakdownArray))
                    {
                        $taxBreakdownArray[] = $lineTaxRateArray['code'];
                    }
                }

                // loop around checkboxes
                foreach ($component['checkboxes'] as &$checkbox)
                {
                    if ($checkbox['checked'])
                    {
                        $lineTaxRateArray = self::getTaxRateArrayFromTaxLevel($checkbox['orderfootertaxlevel']);

                        if (!in_array($lineTaxRateArray['code'], $taxBreakdownArray))
                        {
                            $taxBreakdownArray[] = $lineTaxRateArray['code'];
                        }
                    }
                }
            }

            // loop around order footer checkboxes
            foreach ($gSession['order']['orderFooterCheckboxes'] as &$checkbox)
            {
                if ($checkbox['checked'])
                {
                    $lineTaxRateArray = self::getTaxRateArrayFromTaxLevel($checkbox['orderfootertaxlevel']);

                    if (!in_array($lineTaxRateArray['code'], $taxBreakdownArray))
                    {
                        $taxBreakdownArray[] = $lineTaxRateArray['code'];
                    }
                }
            }
        }

        if (count($taxBreakdownArray) == 1)
        {
            $gSession['order']['orderfootertaxratesequal'] = 1;
            //return 1;
        }
        else
        {
            $gSession['order']['orderfootertaxratesequal'] = 0;
            //return 0;
        }

        DatabaseObj::updateSession();
    }

    static function updateCreditStatus()
    {
        global $gSession;

        $userAccountArray = DatabaseObj::getUserAccountFromID($gSession['userid']);
        $sessionRef = AuthenticateObj::getSessionRef();

        $gSession['useracccountbalance'] = $userAccountArray['accountbalance'];
        $gSession['usercreditlimit'] = $userAccountArray['creditlimit'];

        $availableGiftCardBalance = $userAccountArray['giftcardbalance'];
        $assignedGiftCardBalance = DatabaseObj::getSessionGiftCardTotal($sessionRef, $gSession['userid'], true);

        $gSession['usergiftcardbalance'] = $availableGiftCardBalance - $assignedGiftCardBalance;
    }

    static function updateVoucher($pVoucherCode, $pCheckProductGroup, $pVoucherArray = Array())
    {
        global $gSession;

        $resultArray = Array();
        $voucherAppliedToOrder = false;
        $wasActive = false;
        $checkProductGroup = $pCheckProductGroup;

        if ($gSession['order']['vouchercode'] != '')
        {
            DatabaseObj::setVoucherSessionRef($gSession['order']['vouchercode'], 0);
        }

        $orderItemCount = count($gSession['items']);

        if ($pVoucherCode != '')
        {
            $licensekeyArray = DatabaseObj::getLicenseKeyFromCode($gSession['licensekeydata']['groupcode']);
            $licenseKeyCompanyCode = $licensekeyArray['companyCode'];

            if (count($pVoucherArray) == 0)
            {
                $resultArray = DatabaseObj::getVoucher($pVoucherCode, $gSession['items'][0]['itemqty'],
                                $gSession['items'][0]['itemproductcode'], $gSession['licensekeydata']['groupcode'], $gSession['userid'],
                                $gSession['order']['starttime'], $licenseKeyCompanyCode, TPX_VOUCHER_TYPE_DISCOUNT);
                if ($resultArray['defaultdiscount'] == 1)
                {
                    $resultArray['result'] = 'error_default';
                }

                if ($resultArray['result'] === '')
                {
                    if ($resultArray['hasproductgroups'] == 1)
                    {
                        $productGroupsResultArray = self::buildProductGroupsValidityArray($pVoucherCode);
                        $resultArray['result'] = $productGroupsResultArray['error'];
                        $resultArray['resultparam'] = $productGroupsResultArray['errorparam'];
                        $resultArray['productgroup'] = $productGroupsResultArray['data'];
                    }
                    else
                    {
                        $checkProductGroup = false;
                    }
                }
            }
            else
            {
                $resultArray = $pVoucherArray;
            }

            if ($resultArray['result'] == '')
            {
                $gSession['order']['voucherpromotioncode'] = $resultArray['promotioncode'];
                $gSession['order']['voucherpromotionname'] = $resultArray['promotionname'];
                $gSession['order']['vouchercode'] = $resultArray['code'];
                $gSession['order']['vouchertype'] = $resultArray['type'];
                $gSession['order']['vouchername'] = $resultArray['name'];
                $gSession['order']['voucherminqty'] = $resultArray['minqty'];
                $gSession['order']['vouchermaxqty'] = $resultArray['maxqty'];
                $gSession['order']['voucherapplicationmethod'] = $resultArray['applicationmethod'];
                $gSession['order']['voucherapplytoqty'] = $resultArray['applytoqty'];

                if (count($pVoucherArray) > 0)
                {
                    $gSession['order']['voucherlockqty'] = 0;
                }
                else
                {
                    $gSession['order']['voucherlockqty'] = $resultArray['lockqty'];
                }

                // dereference the productgroups key
                if ($checkProductGroup == true)
                {
                    $voucherProductGroup = $resultArray['productgroup'];
                }

                // loop through all order items
                $voucherActive = false;
                for ($i = 0; $i < $orderItemCount; $i++)
                {
                    $theItem = &$gSession['items'][$i];
                    $itemCollectionCode = $theItem['itemproductcollectioncode'];
                    $itemLayoutCode = $theItem['itemproductcode'];

                    $voucherAppliedToLine = 0;
                    $configuredProductGroupApplicable = true;

                    // check if a product group configuration will prevent this voucher from being applied
                    if ($checkProductGroup === true)
                    {
                        $configuredProductGroupApplicable = self::checkProductGroupEligibilityForCartItem($voucherProductGroup, $itemCollectionCode, $itemLayoutCode);
                    }

                    // check if the voucher can be applied :-
                    // first check to see if the voucher can be applied to the line
                    if ((($itemLayoutCode == $resultArray['productcode']) || ($resultArray['productcode'] == '')) && ($configuredProductGroupApplicable === true))
                    {
                        $voucherAppliedToOrder = true;

						// Store the current quantity.
                        $prevItemQty = $theItem['itemqty'];

                        $factor = (substr($resultArray['discounttype'], 0, 3) == 'BOG') ? 1 : 0;

                        // now process this line
                        // first update the item qty if it has to be locked
                        if ($resultArray['lockqty'] == 1)
                        {
                            if ($theItem['itemqty'] < ($resultArray['minqty'] + $factor))
                            {
                                $theItem['itemqty'] = ($resultArray['minqty'] + $factor);
                            }

                            if ($theItem['itemqty'] > $resultArray['maxqty'])
                            {
                                $theItem['itemqty'] = $resultArray['maxqty'];
                            }
                        }

                        // next, determine if the qty is in range
                        if (($theItem['itemqty'] >= $resultArray['minqty'] + $factor) && ($theItem['itemqty'] <= $resultArray['maxqty']))
                        {
                            if ($resultArray['type'] == TPX_VOUCHER_TYPE_SCRIPT)
                            {
								// Set the accepted state of the voucher to false as default.
								$scriptedVoucherValid = false;

                                if (file_exists("../Customise/scripts/EDL_Voucher.php"))
                                {
                                    if (is_subclass_of('EDL_VoucherScriptObj', '_Voucher'))
                                    {
                                        $voucherActiveForLine = EDL_VoucherScriptObj::validate($gSession['order']['voucherpromotioncode'], $pVoucherCode, $i);

										// Original voucher script returns a boolean or a string.
										// This needs to be kept for any legacy scripts.
										if ($voucherActiveForLine === true)
										{
											$scriptedVoucherValid = true;
											$resultArray['discountsection'] = 'PRODUCT';
										}
										else
										{
											// Array has been returned from the updated voucher script,
											// copy the result in to the required variables.
											if (is_array($voucherActiveForLine))
											{
												$scriptedVoucherValid = $voucherActiveForLine['valid'];

												if ($scriptedVoucherValid)
												{
													// If the voucher can be applied, set the default discount section.
													$resultArray['discountsection'] = 'PRODUCT';
												}
												else
												{
													// Revert back to the previous quantity.
													$theItem['itemqty'] = $prevItemQty;

													// If the voucher cannot be applied, set the reason.
													$resultArray['custommessage'] = $voucherActiveForLine['message'];
												}
											}
											else
											{
												// Revert back to the previous quantity.
												$theItem['itemqty'] = $prevItemQty;

												// Original voucher script returns a string for an error.
												// This needs to be kept for any legacy scripts.
												if ($voucherActiveForLine !== false)
												{
													// If the voucher cannot be applied, set the reason.
													$resultArray['custommessage'] = $voucherActiveForLine;
												}
											}
										}
                                    }
								}

								// Make sure the voucher can still be used.
                                $voucherActive = ($scriptedVoucherValid || $voucherActive);
								$voucherAppliedToLine = ($voucherActive) ? 1 : 0;
                            }
                            else
                            {
                                $voucherAppliedToLine = 1;
                                $voucherActive = true;
                            }
                        }
                    }
                    $theItem['itemvoucherapplied'] = $voucherAppliedToLine;
                }

                if ($resultArray['minordervalue'] > 0)
                {
                    // ordeitemtotallsell no tax no shipping
                    $totalToCompare = $gSession['order']['ordertotalitemsellnotax'];

                    if ($resultArray['minordervalueinctax'] == 1 && $resultArray['minordervalueincshipping'] == 1)
                    {
                        // ordertotal includes tax and shipping
                        $totalToCompare = $gSession['order']['ordertotal'];
                    }
                    else if ($resultArray['minordervalueinctax'] == 1 && $resultArray['minordervalueincshipping'] == 0)
                    {
                        // ordertotal includes tax and no shipping
                        $totalToCompare = $gSession['order']['ordertotalitemsellwithtax'];
                    }
                    else if ($resultArray['minordervalueinctax'] == 0 && $resultArray['minordervalueincshipping'] == 1)
                    {
                        // ordertotal no tax with shipping
                        $totalToCompare = ($gSession['order']['showpriceswithtax'] == 0) ? $gSession['order']['ordertotalsell'] : $gSession['order']['ordertotalsell'] - $gSession['order']['ordertotaltaxbeforediscount'];
                    }

                    if ($totalToCompare < $resultArray['minordervalue'])
                    {
                        $voucherActive = false;
                    }
                }

                if ($voucherActive)
                {
                    $gSession['order']['voucheractive'] = 1;
                    if (count($pVoucherArray) == 0)
                    {
                        $gSession['order']['voucherstatus'] = 'str_LabelOrderVoucherAccepted';
                    }
                    else
                    {
                        $gSession['order']['voucherstatus'] = '';
                    }

                    $gSession['order']['vouchercustommessage'] = '';

                    $gSession['order']['defaultdiscountactive'] = (count($pVoucherArray) > 0);
                }
                else
                {
                    $wasActive = $gSession['order']['voucheractive'] == 1;

                    $gSession['order']['voucheractive'] = 0;
                    if (count($pVoucherArray) == 0)
                    {
                        $gSession['order']['voucherstatus'] = 'str_LabelInvalidVoucher';
                    }
                    else
                    {
                        $gSession['order']['voucherstatus'] = '';
                    }
                    $gSession['order']['vouchercustommessage'] = $resultArray['custommessage'];
                    $gSession['showgiftcardmessage'] = 1;
                    $voucherAppliedToOrder = false;
                }

                $gSession['order']['voucherdiscountsection'] = $resultArray['discountsection'];
                $gSession['order']['voucherdiscounttype'] = $resultArray['discounttype'];
                $gSession['order']['voucherdiscountvalue'] = $resultArray['discountvalue'];
                $gSession['order']['vouchersellprice'] = $resultArray['sellprice'];
                $gSession['order']['voucheragentfee'] = $resultArray['agentfee'];
                $gSession['order']['voucherapplicationmethod'] = $resultArray['applicationmethod'];
                $gSession['order']['voucherapplytoqty'] = $resultArray['applytoqty'];
                $gSession['order']['itemsdiscounted'] = array();

                DatabaseObj::setVoucherSessionRef($pVoucherCode, $gSession['ref']);
            }
            elseif ($resultArray['type'] == TPX_VOUCHER_TYPE_GIFTCARD)
            {
                // gift card, applied to account so reset voucher variable
                $voucherAppliedToOrder = true;
                DatabaseObj::setVoucherSessionRef($pVoucherCode, $gSession['ref']);

                if (! $gSession['order']['defaultdiscountactive'])
                {
                    $gSession['order']['voucherpromotioncode'] = '';
                    $gSession['order']['voucherpromotionname'] = '';
                    $gSession['order']['vouchercode'] = '';
                    $gSession['order']['vouchertype'] = 0;
                    $gSession['order']['vouchername'] = '';
                    $gSession['order']['voucherminqty'] = 1;
                    $gSession['order']['vouchermaxqty'] = 9999;
                    $gSession['order']['voucherlockqty'] = 0;
                    $gSession['order']['voucheractive'] = 0;
                    $gSession['order']['voucherdiscountsection'] = '';
                    $gSession['order']['voucherdiscounttype'] = '';
                    $gSession['order']['voucherdiscountvalue'] = 0.00;
                    $gSession['order']['vouchersellprice'] = 0.00;
                    $gSession['order']['voucheragentfee'] = 0.00;
                    $gSession['order']['voucherstatus'] = $resultArray['result'];
                    $gSession['order']['voucherapplicationmethod'] = TPX_VOUCHER_APPLY_EACH_MATCHING_PRODUCT;
                    $gSession['order']['voucherapplytoqty'] = 9999;
                }
                else
                {
                    $defaultVoucher = self::getDefaultVoucher();
                    $gSession['order']['voucherpromotioncode'] = $defaultVoucher['voucherpromotioncode'];
                    $gSession['order']['voucherpromotionname'] = $defaultVoucher['voucherpromotionname'];
                    $gSession['order']['vouchercode'] = $defaultVoucher['vouchercode'];
                    $gSession['order']['vouchertype'] = $defaultVoucher['vouchertype'];
                    $gSession['order']['vouchername'] = $defaultVoucher['vouchername'];
                    $gSession['order']['voucherminqty'] = $defaultVoucher['voucherminqty'];
                    $gSession['order']['vouchermaxqty'] = $defaultVoucher['vouchermaxqty'];
                    $gSession['order']['voucherlockqty'] = $defaultVoucher['voucherlockqty'];
                    $gSession['order']['voucheractive'] = $defaultVoucher['voucheractive'];
                    $gSession['order']['voucherdiscountsection'] = $defaultVoucher['voucherdiscountsection'];
                    $gSession['order']['voucherdiscounttype'] = $defaultVoucher['voucherdiscounttype'];
                    $gSession['order']['voucherdiscountvalue'] = $defaultVoucher['voucherdiscountvalue'];
                    $gSession['order']['vouchersellprice'] = $defaultVoucher['vouchersellprice'];
                    $gSession['order']['voucheragentfee'] = $defaultVoucher['voucheragentfee'];
                    $gSession['order']['voucherstatus'] = $defaultVoucher['voucherstatus'];
                    $gSession['order']['voucherapplicationmethod'] = $defaultVoucher['voucherapplicationmethod'];
                    $gSession['order']['voucherapplytoqty'] = $defaultVoucher['voucherapplytoqty'];

                }
            }
        }

        $defaultVoucher = Array();
        $defaultWasOn = false;

        if (! $voucherAppliedToOrder)
        {
            $defaultVoucher = self::getDefaultVoucher();

            if (count($defaultVoucher) > 0)
            {
                /* If the codes match then the default voucher is not valid.
                 * If the codes don't match then the default is valid if there is one.
                 */

                if ($gSession['order']['vouchercode'] == $defaultVoucher['vouchercode'])
                {
                    $defaultWasOn = true;
                    $gSession['order']['voucheractive'] = false;
                }
                else
                {
                    $gSession['order']['voucheractive'] = $defaultVoucher['voucheractive'];
                }

                $gSession['order']['voucherpromotioncode'] = $defaultVoucher['voucherpromotioncode'];
                $gSession['order']['voucherpromotionname'] = $defaultVoucher['voucherpromotionname'];
                $gSession['order']['vouchercode'] = $defaultVoucher['vouchercode'];
                $gSession['order']['vouchertype'] = $defaultVoucher['vouchertype'];
                $gSession['order']['vouchername'] = $defaultVoucher['vouchername'];
                $gSession['order']['voucherminqty'] = $defaultVoucher['voucherminqty'];
                $gSession['order']['vouchermaxqty'] = $defaultVoucher['vouchermaxqty'];
                $gSession['order']['voucherlockqty'] = $defaultVoucher['voucherlockqty'];
                $gSession['order']['voucherdiscountsection'] = $defaultVoucher['voucherdiscountsection'];
                $gSession['order']['voucherdiscounttype'] = $defaultVoucher['voucherdiscounttype'];
                $gSession['order']['voucherdiscountvalue'] = $defaultVoucher['voucherdiscountvalue'];
                $gSession['order']['vouchersellprice'] = $defaultVoucher['vouchersellprice'];
                $gSession['order']['voucheragentfee'] = $defaultVoucher['voucheragentfee'];
                $gSession['order']['voucherstatus'] = $defaultVoucher['voucherstatus'];
                $gSession['order']['voucherapplicationmethod'] = $defaultVoucher['voucherapplicationmethod'];
                $gSession['order']['voucherapplytoqty'] = $defaultVoucher['voucherapplytoqty'];

                /*
                 * Still set that there is a default discount active on the order even though it doesn't apply.
                 */
                $gSession['order']['defaultdiscountactive'] = true;
            }
            else
            {
                $gSession['order']['voucherpromotioncode'] = '';
                $gSession['order']['voucherpromotionname'] = '';
                $gSession['order']['vouchercode'] = '';
                $gSession['order']['vouchertype'] = 0;
                $gSession['order']['vouchername'] = '';
                $gSession['order']['voucherminqty'] = 1;
                $gSession['order']['vouchermaxqty'] = 9999;
                $gSession['order']['voucherlockqty'] = 0;
                $gSession['order']['voucheractive'] = 0;
                $gSession['order']['voucherdiscountsection'] = '';
                $gSession['order']['voucherdiscounttype'] = '';
                $gSession['order']['voucherdiscountvalue'] = 0.00;
                $gSession['order']['vouchersellprice'] = 0.00;
                $gSession['order']['voucheragentfee'] = 0.00;
                $gSession['order']['voucherstatus'] = 'str_LabelInvalidVoucher';
                $gSession['order']['voucherapplicationmethod'] = TPX_VOUCHER_APPLY_EACH_MATCHING_PRODUCT;
                $gSession['order']['voucherapplytoqty'] = 9999;
            }

            $gSession['order']['voucherstatus'] = '';

            if (isset($_POST['vouchercode']))
            {
                if ($_POST['vouchercode'] != '')
                {
                    $gSession['order']['voucherstatus'] = 'str_LabelInvalidVoucher';
                }
            }
            else
            {
                if ($wasActive)
                {
                    if (count($defaultVoucher) == 0)
                    {
                        $gSession['order']['voucherstatus'] = 'str_LabelVoucherNoLonger';
                    }
                    else
                    {
                        if ($defaultWasOn)
                        {
                            $gSession['order']['voucherstatus'] = '';
                        }
                        else
                        {
                            $gSession['order']['voucherstatus'] = 'str_LabelVoucherNoLonger';
                        }
                    }
                }
            }

            for ($i = 0; $i < $orderItemCount; $i++)
            {
                $gSession['items'][$i]['itemvoucherapplied'] = 0;
            }
        }

        // clear any remaining gift card status
        $gSession['order']['giftcardstatus'] = '';

        self::updateAllOrderSections();
        self::updateOrderTotal();
        self::updateOrderShippingRate();
        self::updateOrderTotal();
        DatabaseObj::updateSession();
    }

    static function buildProductGroupsValidityArray($pVoucherCode)
    {
        global $gSession;

        $productArray = Array();
        $orderItemCount = count($gSession['items']);

        for ($i = 0; $i < $orderItemCount; $i++)
        {
            $theItem = $gSession['items'][$i];

            $productArray[] = array("collectioncode" => $theItem['itemproductcollectioncode'], "layoutcode" => $theItem['itemproductcode']);
        }

        return DatabaseObj::retrieveVoucherProductGroupValidity($pVoucherCode, $productArray);
    }


    static function checkProductGroupEligibilityForCartItem($pProductGroupArray, $pCollectionCode, $pLayoutCode)
    {
        $configuredProductGroupApplicable = false;
        $continueSearch = true;

        // check if the passed cart item is valid for the product group
        if ($pProductGroupArray['groupfound'] === true)
        {
            // check if the database query did not find this lines collection or layout code
            if ($pProductGroupArray['groupapplicable'] === true)
            {
                if (array_key_exists($pCollectionCode, $pProductGroupArray['groupconfigurations']) === true)
                {
                    if ((UtilsObj::getArrayParam($pProductGroupArray['groupconfigurations'][$pCollectionCode], $pLayoutCode, false) == true)
                        || (UtilsObj::getArrayParam($pProductGroupArray['groupconfigurations'][$pCollectionCode], "*", false) == true))
                    {
                        // the item does not match any of the configurations for the assigned group
                        $configuredProductGroupApplicable = true;
                        // we have found a valid configuration, we do not want to continue searching
                        $continueSearch = false;
                    }
                }

                if (($continueSearch === true) && ((array_key_exists('*', $pProductGroupArray['groupconfigurations']) == true)
                    && (UtilsObj::getArrayParam($pProductGroupArray['groupconfigurations']['*'], $pLayoutCode, false) == true)))
                {
                    $configuredProductGroupApplicable = true;
                }
            }
        }
        else
        {
            $configuredProductGroupApplicable = true;
        }

        return $configuredProductGroupApplicable;
    }

    static function updateGiftCard($pGiftCardCode)
    {
        global $gSession;

        $resultArray = Array();

        if ($pGiftCardCode != '')
        {
            $licensekeyArray = DatabaseObj::getLicenseKeyFromCode($gSession['licensekeydata']['groupcode']);
            $licenseKeyCompanyCode = $licensekeyArray['companyCode'];

            $resultArray = DatabaseObj::getVoucher($pGiftCardCode, $gSession['items'][0]['itemqty'],
                            $gSession['items'][0]['itemproductcode'], $gSession['licensekeydata']['groupcode'], $gSession['userid'],
                            $gSession['order']['starttime'], $licenseKeyCompanyCode, TPX_VOUCHER_TYPE_GIFTCARD);

            $gSession['order']['giftcardstatus'] = $resultArray['result'];
        }

        self::updateAllOrderSections();
        self::updateOrderTotal();
        self::updateOrderShippingRate();
        self::updateOrderTotal();
        DatabaseObj::updateSession();
    }

    private static function getDefaultVoucher()
    {
        global $gSession;
        $resultArray = Array();

        $defaultDiscount = $gSession['order']['defaultdiscount'];

        if (count($defaultDiscount) > 0)
        {
            $resultArray['voucherpromotioncode'] = $defaultDiscount['promotioncode'];
            $resultArray['voucherpromotionname'] = $defaultDiscount['promotionname'];
            $resultArray['vouchercode'] = $defaultDiscount['code'];
            $resultArray['vouchertype'] = $defaultDiscount['type'];
            $resultArray['vouchername'] = $defaultDiscount['name'];
            $resultArray['voucherminqty'] = $defaultDiscount['minqty'];
            $resultArray['vouchermaxqty'] = $defaultDiscount['maxqty'];
            $resultArray['voucherlockqty'] = 0;
            $resultArray['voucheractive'] = 1;
            $resultArray['voucherdiscountsection'] = $defaultDiscount['discountsection'];
            $resultArray['voucherdiscounttype'] = $defaultDiscount['discounttype'];
            $resultArray['voucherdiscountvalue'] = $defaultDiscount['discountvalue'];
            $resultArray['vouchersellprice'] = 0.00;
            $resultArray['voucheragentfee'] = 0.00;
            $resultArray['voucherstatus'] = '';
            $resultArray['voucherapplicationmethod'] = $defaultDiscount['applicationmethod'];
            $resultArray['voucherapplytoqty'] = $defaultDiscount['applytoqty'];
        }

        return $resultArray;
    }

    static function setVoucher()
    {
        global $gSession;

        $gSession['order']['paymentmethodcode'] = $_POST['paymentmethodcode'];
        $gSession['order']['paymentgatewaycode'] = $_POST['paymentgatewaycode'];
        $gSession['showgiftcardmessage'] = $_POST['showgiftcardmessage'];

        $voucherStatus = '';

        self::updateVoucher($_POST['vouchercode'], true);

        if (($gSession['order']['defaultdiscountactive']) && ($gSession['order']['vouchercustommessage'] == ''))
        {
            $voucherStatus = $gSession['order']['voucherstatus'];

            self::checkVoucher();

            if ($voucherStatus != '')
            {
                $gSession['order']['voucherstatus'] = $voucherStatus;
            }
        }
    }

    static function setGiftCard()
    {
        global $gSession;

        $gSession['order']['paymentmethodcode'] = $_POST['paymentmethodcode'];
        $gSession['order']['paymentgatewaycode'] = $_POST['paymentgatewaycode'];
        $gSession['showgiftcardmessage'] = $_POST['showgiftcardmessage'];

        self::updateGiftCard($_POST['giftcardcode']);
    }

    static function checkVoucher()
    {
        global $gSession;

        $defaultVoucherIndex = 0;

        $voucherCode = $gSession['order']['vouchercode'];
        if (! $gSession['order']['defaultdiscountactive'])
        {
            if (strlen($voucherCode) > 0)
            {
                self::updateVoucher($voucherCode, true);
            }
        }
        else
        {
			// get list of product codes in order
			$prodsList = array();
            $catsList = array();

			foreach ($gSession['items'] as $testProd)
			{
				$prodsList[] = $testProd['itemproductcode'];
                $catsList[] = $testProd['itemproductcollectioncode'];
			}
            /* Grab the default discounts */
            $defaultDiscounts = DatabaseObj::getDefaultDiscounts($gSession['licensekeydata']['groupcode'], $gSession['userid'],
                            $gSession['order']['starttime'], $gSession['userdata']['companycode'], $prodsList, $catsList);

			$defaultDiscountCount = count($defaultDiscounts);
			$discountCanBeAppliedToOrder = false;
            $productGroupCache = array();

			// find the first voucher which can be applied
            for ($defaultVoucherIndex = 0; $defaultVoucherIndex < $defaultDiscountCount; $defaultVoucherIndex++)
            {
                $defaultDiscount = &$defaultDiscounts[$defaultVoucherIndex];

                // the lookup is expensive so only look it up if we have found a product group in the earlier query
                if (($defaultDiscount['hasproductgroup'] == 1))
                {
                    // check if we have previously examined this product group
                    if (! array_key_exists($defaultDiscount['productgroupid'], $productGroupCache))
                    {
                        $productGroupValidityArray = self::buildProductGroupsValidityArray($defaultDiscount['code']);

                        if ($productGroupValidityArray['error'] === '')
                        {
                            $defaultDiscount['productgroup'] = $productGroupValidityArray['data'];
                            $productGroupCache[$defaultDiscount['productgroupid']] = $productGroupValidityArray['data'];
                        }
                        else
                        {
                            // if we have an error we cannot be sure that the voucher is valid thus we must skip it
                            continue;
                        }
                    }
                    else
                    {
                        $defaultDiscount['productgroup'] = $productGroupCache[$defaultDiscount['productgroupid']];
                    }

                }
                else
                {
                    // generate a dummy product group array
                    $defaultDiscount['productgroup'] = array('groupfound' => false, 'groupapplicable' => false, 'groupconfigurations' => array());
                }

				foreach ($gSession['items'] as $testLineItem)
				{
                    $validVoucher = false;

                    if ($defaultDiscount['productcode'] == '')
                    {
                        if ($testLineItem['itemproductcode'] != $defaultDiscount['productcode'])
                        {
                            $validVoucher = true;
                        }
                    }
                    elseif ($defaultDiscount['hasproductgroup'] == 1)
                    {
                        $validVoucher = self::checkProductGroupEligibilityForCartItem($defaultDiscount['productgroup'],
                            $testLineItem['itemproductcollectioncode'], $testLineItem['itemproductcode']);
                    }
                    else
                    {
                        $validVoucher = true;
                    }

                    if ($validVoucher)
                    {
                        self::updateVoucher($defaultDiscount['code'], true, $defaultDiscount);

                        if ($gSession['order']['defaultdiscountactive'])
                        {
                            $gSession['order']['defaultdiscount'] = $defaultDiscount;
                            $discountCanBeAppliedToOrder = true;

                            // a voucher has been found, stop searching for any other valid default discounts
                            break;
                        }
                    }
				}

				if ($discountCanBeAppliedToOrder)
				{
					// a voucher has been found, stop searching for any other valid default discounts
					break;
				}
            }

            if (count($defaultDiscounts) == 0)
            {
                $gSession['order']['defaultdiscountactive'] = false;
            }
        }
    }

    static function ccCancelCallback()
    {
        $resultArray = Array();

        $resultArray['paymentmethods'] = self::updateOrderPaymentMethod();
        self::updateCreditStatus();

        self::formatOrderAddresses($resultArray);

        $resultArray['custominit'] = '';
        $resultArray['previousstage'] = 'shipping';
        $resultArray['nextstage'] = 'payment';
        $resultArray['metadata'] = self::buildOrderMetaData('payment');

        DatabaseObj::updateSession();

        return $resultArray;
    }

    static function getStoreLocatorLogo()
    {
        return DatabaseObj::getStoreLocatorLogo($_GET['id'], $_GET['tmp']);
    }

    /**
     * Returns list of available components in that section.
     *
     * Availability is taking settings of order line into account, like product, quantity, pagecount.
     * Gets components that have been placed in $pSectionPath.
     * Also gets components with default prices, that is why we need $pCategoryCode.
     *
     * If $pCalculatePriceDifference is false, the price differences to the current component is not computed. This
     * is so that a list of components can be retrieved before a component is there is a current component, for example
     * to determine the default component.
     *
     * @static
     *
     * @param integer $pOrderLine
     * @param string  $pCategoryCode
     * @param string  $pSectionPath
     * @param boolean  $pCalculatePriceDifference
     *
     * @return array
     *
     * @author Steffen Haugk
     * @since Version 3.0.0
     */
    static function getOrderComponentList($pOrderLine, $pProductCode, $pCategoryCode, $pSectionPath, $pCalculatePriceDifference = true,
            $pSelectedComponentCode = '', $pComponentChanged = false, $pOrderFooterAccumaltiveItemQty = 0,
            $pOrderFooterAccumaltivePageCount = 0, $pComponentQty = 0)
    {
        global $gSession;

        $resultArray = Array();

        $currencyExchangeRate = $gSession['order']['currencyexchangerate'];
        $currencyDecimalPlaces = $gSession['order']['currencydecimalplaces'];
        $itemProductCode = $pProductCode;
		$componentQty = $pComponentQty == 0 ? -1 : $pComponentQty;

        // check if orderfooter section
        if ($pOrderLine < 0)
        {
            $itemQty = $pOrderFooterAccumaltiveItemQty;
            $itemPageCount = $pOrderFooterAccumaltivePageCount;
        }
        else
        {
            $itemQty = $gSession['items'][$pOrderLine]['itemqty'];
            $itemPageCount = $gSession['items'][$pOrderLine]['itempagecount'];
        }
        $resultArray = DatabaseObj::getComponentsInOrderSectionByCategory($pSectionPath, $pCategoryCode,
                        $gSession['userdata']['companycode'], $itemProductCode, $gSession['licensekeydata']['groupcode'],
                        $currencyExchangeRate, $currencyDecimalPlaces, $itemQty, $itemPageCount, $componentQty, $pSelectedComponentCode,
                        $pComponentChanged, true);

        $componentList = $resultArray['component'];
        $itemCount = count($componentList);
        $selectedComponentTotalSell = 0.00;

        for ($i = 0; $i < $itemCount; $i++)
        {
            $componentSell = $componentList[$i]['sell'];

            if ($pOrderLine < 0)
            {
                // if the component is in the ORDERFOOTER and the component does not use the product qty to calculate its price then we have to recalulate the price based on a itemQty of 1
                // we then reset the component sell value to the new price.
                if ($componentList[$i]['orderfooterusesproductquantity'] == 0)
                {
                    $sectionPriceArray = DatabaseObj::getPrice($pSectionPath, $componentList[$i]['code'], false, $itemProductCode,
                                    $gSession['licensekeydata']['groupcode'], $gSession['userdata']['companycode'], $currencyExchangeRate,
                                    $currencyDecimalPlaces, 1, $itemPageCount, $pComponentQty, $pComponentQty, true, true, -1, 0, '', true);
                    $componentSell = $sectionPriceArray['totalsell'];
                }

                if ($gSession['order']['orderalltaxratesequal'] == 1)
                {
                    $taxRateArray = DatabaseObj::getTaxRate($gSession['order']['fixedtaxrate']);
                }
                else
                {
                    $taxRateArray = self::getTaxRateArrayFromTaxLevel($componentList[$i]['orderfootertaxlevel']);
                }
                $taxRate = $taxRateArray['rate'];
            }
            else
            {
                $taxRate = $gSession['items'][$pOrderLine]['itemtaxrate'];
            }

            // determine the tax status for the component
            if ($componentList[$i]['pricetaxcode'] != '')
            {
                // tax is included in the price so determine the price without tax
                $componentTotalSellNoTax = UtilsObj::bround(($componentSell / ($componentList[$i]['pricetaxrate'] + 100)) * 100,
                                $gSession['order']['currencydecimalplaces']);
                $componentTotalTax = $componentSell - $componentTotalSellNoTax;

                if ($componentList[$i]['pricetaxrate'] != $taxRate)
                {
                    // if the tax included in the price is different to the line tax then we use the price without tax
                    $componentSell = $componentTotalSellNoTax;
                    $totalTax = UtilsObj::bround($componentSell * $taxRate / 100, $gSession['order']['currencydecimalplaces']);
                    $componentTotalSellWithTax = $componentSell + $totalTax;
                }
                else
                {
                    // tax is already calculated
                    $componentTotalSellWithTax = $componentSell;
                }
            }
            else
            {
                // no tax was included in the price
                $componentTotalTax = UtilsObj::bround($componentSell * $taxRate / 100, $gSession['order']['currencydecimalplaces']);
                $componentTotalSellWithTax = $componentSell + $componentTotalTax;
                $componentTotalSellNoTax = $componentSell;
            }

            // add the tax at this point if necessary
            if ($gSession['order']['showpriceswithtax'] == 1)
            {
                $componentTotalSell = $componentTotalSellWithTax;
            }
            else
            {
                $componentTotalSell = $componentTotalSellNoTax;
            }

            $componentList[$i]['sell'] = $componentTotalSell;

            $componentList[$i]['name'] = LocalizationObj::getLocaleString($componentList[$i]['name'], $gSession['browserlanguagecode'], true);
            $componentList[$i]['info'] = LocalizationObj::getLocaleString($componentList[$i]['info'], $gSession['browserlanguagecode'], true);

            if ($componentList[$i]['code'] == $pSelectedComponentCode)
            {
                $selectedComponentTotalSell = $componentList[$i]['sell'];
            }
        }

        if ($pCalculatePriceDifference)
        {
            foreach ($componentList as &$componentItem)
            {
                $componentItem['pricedifference'] = UtilsObj::formatCurrencyNumber($componentItem['sell'] - $selectedComponentTotalSell,
                                $currencyDecimalPlaces, $gSession['browserlanguagecode'], $gSession['order']['currencysymbol'],
                                $gSession['order']['currencysymbolatfront']);
            }
        }

        $resultArray['component'] = $componentList;

        return $resultArray;
    }

    /**
     * Returns the next order line id and increments it.
     *
     * Since this will only be used when new orderlines or sections are added,
     * it is not necessary to save the session, as the calling function will do that.
     *
     * @static
     *
     * @return integer
     *
     * @author Steffen Haugk
     * @since Version 3.0.0
     */
    static function getNextOrderLineId()
    {
        global $gSession;

        $orderLineId = $gSession['order']['nextorderlineid'];
        $gSession['order']['nextorderlineid'] = $orderLineId + 1;

        return $orderLineId;
    }

    static function changeShippingMethod()
    {
        global $gSession;

        $gSession['shipping'][0]['shippingratecode'] = $_REQUEST['shippingratecode'];
        $origShippingMethodCode = '';
        $existingShippingRatesArray = Array();
        $origShippingMethodArray = Array();
        $checkShippingMethod = true;
        $resultArray = array();
        $shippingMethodCode = '';
        $useDefaultBillingAddress = 0;
        $useDefaultShippingAddress = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
            while ($checkShippingMethod)
            {
                if ($origShippingMethodCode == '')
                {
                    // find shipping method record for method code that links to the shipping rate code in $_POST
                    if ($stmt = $dbObj->prepare('SELECT `sm`.`code`, `sm`.`usedefaultbillingaddress`, `sm`.`usedefaultshippingaddress` FROM SHIPPINGMETHODS sm JOIN SHIPPINGRATES sr ON (sr.shippingmethodcode = sm.code) WHERE sr.code = ?'))
                    {
                        if ($stmt->bind_param('s', $_REQUEST['shippingratecode']))
                        {
                            if ($stmt->execute())
                            {
                                if ($stmt->store_result())
                                {
                                    if ($stmt->num_rows > 0)
                                    {
                                        if ($stmt->bind_result($shippingMethodCode, $useDefaultBillingAddress, $useDefaultShippingAddress))
                                        {
                                            if ($stmt->fetch())
                                            {
                                                $origShippingMethodCode = $shippingMethodCode;
                                            }
                                            else
                                            {
                                                $error = 'changeShippingMethod fetch ' . $dbObj->error;
                                            }
                                        }
                                        else
                                        {
                                            $error = 'changeShippingMethod bind result ' . $dbObj->error;
                                        }
                                    }
                                }
                                else
                                {
                                    $error = 'changeShippingMethod store result ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                $error = 'changeShippingMethod execute ' . $dbObj->error;
                            }
                            $stmt->free_result();
                            $stmt->close();
                        }
                        else
                        {
                            $error = 'changeShippingMethod bind param ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error = 'changeShippingMethod prepare ' . $dbObj->error;
                    }
                }
                else
                {
                    // find shipping method record for orig shipping method code
                    $origShippingMethodArray = DatabaseObj::getShippingMethodFromCode($origShippingMethodCode);
                    $useDefaultBillingAddress = $origShippingMethodArray['usedefaultbillingaddress'];
                    $useDefaultShippingAddress = $origShippingMethodArray['usedefaultshippingaddress'];
                }

                // change addresses if use default
                if ($useDefaultBillingAddress == 1)
                {
                    self::copyDefaultAddressToCurrentAddress('billing');
                }

                if ($useDefaultShippingAddress == 1)
                {
                    self::copyDefaultAddressToCurrentAddress('shipping');
                }

                Order_model::updateOrderTaxRate();

                $resultArray['shippingrates'] = self::updateOrderShippingRate();

                // re-check the voucher to make sure its usage status hasn't changed
                self::checkVoucher();

                // check to see if the shipping method is still the same as if it has changed we need to repeat the text
                if ($gSession['shipping'][0]['shippingmethodcode'] == $origShippingMethodCode)
                {
                    $checkShippingMethod = false;
                }
                else
                {
                    $gSession['shipping'][0]['storeid'] = '';
                    $origShippingMethodCode = $gSession['shipping'][0]['shippingmethodcode'];

                    // make sure we have not tested this shipping rate code before
                    // if we have this probably indicates that we are in an infinite loop so we cannot continue
                    if (in_array($gSession['shipping'][0]['shippingratecode'], $existingShippingRatesArray))
                    {
                        $checkShippingMethod = false;
                    }
                    else
                    {
                        $existingShippingRatesArray[] = $gSession['shipping'][0]['shippingratecode'];
                    }

                    // if we do not have a shipping method then we cannot continue
                    if ($origShippingMethodCode == '')
                    {
                        $checkShippingMethod = false;
                    }
                }
            }

            $dbObj->close();
        }

        Order_model::updateOrderTotal();

        DatabaseObj::updateSession();
    }


    static function getTaxRateArrayFromTaxLevel($pOrderFooterComponentTaxLevel)
    {
        global $gSession;

        $orderFooterComponentTaxRate = Array();

        switch ($pOrderFooterComponentTaxLevel)
        {
            case 1:
                $orderFooterComponentTaxRate = $gSession['order']['producttaxlevel1'];
                break;
            case 2:
                $orderFooterComponentTaxRate = $gSession['order']['producttaxlevel2'];
                break;
            case 3:
                $orderFooterComponentTaxRate = $gSession['order']['producttaxlevel3'];
                break;
            case 4:
                $orderFooterComponentTaxRate = $gSession['order']['producttaxlevel4'];
                break;
            case 5:
                $orderFooterComponentTaxRate = $gSession['order']['producttaxlevel5'];
                break;
        }

        return $orderFooterComponentTaxRate;
    }

    static function addGiftCard()
    {
        global $gSession;

        self::updateCreditStatus();

        $gSession['order']['ordergiftcardtotal'] = $gSession['usergiftcardbalance'];

        if ($gSession['order']['ordergiftcardtotal'] > $gSession['order']['ordertotal'])
        {
            $gSession['order']['ordergiftcardtotal'] = $gSession['order']['ordertotal'];
        }

        $gSession['order']['ordertotaltopay'] = $gSession['order']['ordertotal'] - $gSession['order']['ordergiftcardtotal'];
        $gSession['ordergiftcarddeleted'] = false;
        DatabaseObj::updateSession();
        DatabaseObj::setSessionGiftCardTotal($gSession['ref'], $gSession['order']['ordergiftcardtotal']);

        self::updateCreditStatus();

        $canUseAccount = false;

        if (($gSession['useracccountbalance'] + $gSession['order']['ordertotaltopay']) <= $gSession['usercreditlimit'])
        {
            $canUseAccount = true;
        }

        return $canUseAccount;
    }

    static function deleteGiftCard()
    {
        global $gSession;

        self::updateCreditStatus();

        $gSession['order']['ordertotaltopay'] = $gSession['order']['ordertotal'];
        $gSession['ordergiftcarddeleted'] = true;
        $gSession['order']['ordergiftcardtotal'] = 0;

        DatabaseObj::updateSession();
        DatabaseObj::setSessionGiftCardTotal($gSession['ref'], 0.00);

        self::updateCreditStatus();

        $canUseAccount = false;

        if (($gSession['useracccountbalance'] + $gSession['order']['ordertotaltopay']) <= $gSession['usercreditlimit'])
        {
            $canUseAccount = true;
        }

        return $canUseAccount;
    }

    static function generateOrderEmailContent($pOrderHeaderID, $pEmailTemplate)
    {
        // include the email creation module
        require_once('../Utils/UtilsEmail.php');

        $archivedOrderData = DatabaseObj::getJobTicketData($pOrderHeaderID, $pEmailTemplate);

        $brandEmailCssFile = $archivedOrderData['brandroot'] . "/email/resources/emailcontent.css";
        if (!file_exists($brandEmailCssFile))
        {
            $brandEmailCssFile = "../Customise/email/resources/emailcontent.css";
        }
        $cssContent = UtilsObj::readTextFile($brandEmailCssFile);

        $cssContent = str_replace("\r\n", "\n", $cssContent);
        $cssContent = str_replace("\r", "\n", $cssContent);

        $htmlContent = '';

        if ($archivedOrderData['order']['paymentmethodcode'] != 'PAYLATER')
        {
            /* Get the whole jobticket html content from the view
             *  Not really MVC compliant as data returned from the view however we don't want to make new fuseaction call to get the data
             *  because it will create more php & database overhead.
             */

            // include the order view as we need to display the job ticket
            require_once('../libs/external/EmailContent/css_to_inline_styles.php');
            require_once('../Order/Order_view.php');

            $htmlContent = Order_view::displayJobTicket($archivedOrderData, 'email', true, true, false, false, 'email');

            // Convert external & embeded CSS to inline CSS for email clients.
            $cssToInlineStyles = new CSSToInlineStyles($htmlContent, $cssContent);
            $cssToInlineStyles->setUseInlineStylesBlock(false);
            $cssToInlineStyles->setCleanup(false);
            $htmlContent = $cssToInlineStyles->convert();
        }

        $shippingAddress = UtilsAddressObj::formatAddress($archivedOrderData['shipping'][0], 'shipping', "\n");
        $billingAddress = UtilsAddressObj::formatAddress($archivedOrderData['order'], 'billing', "\n");
        $orderDate = $archivedOrderData['order']['orderdate'];

        $formattedOrderTotal = UtilsObj::formatCurrencyNumber($archivedOrderData['order']['ordertotal'],
                        $archivedOrderData['order']['currencydecimalplaces'], $archivedOrderData['browserlanguagecode'],
                        $archivedOrderData['order']['currencysymbol'], $archivedOrderData['order']['currencysymbolatfront']);
        $formattedOrderGiftCardTotal = UtilsObj::formatCurrencyNumber($archivedOrderData['order']['ordergiftcardtotal'],
                        $archivedOrderData['order']['currencydecimalplaces'], $archivedOrderData['browserlanguagecode'],
                        $archivedOrderData['order']['currencysymbol'], $archivedOrderData['order']['currencysymbolatfront']);
        $formattedOrderTotalToPay = UtilsObj::formatCurrencyNumber($archivedOrderData['order']['ordertotaltopay'],
                        $archivedOrderData['order']['currencydecimalplaces'], $archivedOrderData['browserlanguagecode'],
                        $archivedOrderData['order']['currencysymbol'], $archivedOrderData['order']['currencysymbolatfront']);

        // Store Data
        $storeCode = $archivedOrderData['shipping'][0]['storecode'];
        $storeData = $archivedOrderData['shipping'][0]['storedata'];
        $storeOpeningTimes = LocalizationObj::getLocaleString($storeData['openingtimes'], $archivedOrderData['browserlanguagecode'], true);
        $storeOpeningTimes = str_replace("\\n", '<br>', $storeOpeningTimes);
        $storeURL = $storeData['storeurl'];
        $storeEmailAddress = $storeData['emailaddress'];
        $storeTelephoneNumber = $storeData['telephonenumber'];
        $storeContactName = $storeData['contactfirstname'] . " " . $storeData['contactlastname'];
        $storeOnline = $storeData['siteonline'];
        $isTempOrder = 0;

        // Brading Data
        $brandData = $archivedOrderData['webbrandemailsettings'];

        // check to see if the brand assigned is set to use default email settings.
        // check to see if the brand assigned is active. If it is not then we must use the default brand settings.
        if ((($archivedOrderData['webbrandcode'] != '') && ($brandData['usedefaultemailsettings'] == 1)) || (($archivedOrderData['webbrandcode'] != '') && ($brandData['isactive'] == 0)))
        {
        	$brandData = DatabaseObj::getBrandingFromCode('');
        }

        $sendNotification = $brandData['smtporderconfirmationactive'];
        $smtpOrderConfirmationName = $brandData['smtporderconfirmationname'];
        $smtpOrderConfirmationAddress = $brandData['smtporderconfirmationaddress'];

        if ($archivedOrderData['order']['paymentmethodcode'] == 'PAYLATER')
        {
            $isTempOrder = 1;
        }
        // if this is a temp order that has not been assigned to a user (offline order) then we do not send an email notification to the user
        if (($isTempOrder == 1) && ($archivedOrderData['userid'] == 0))
        {
            $sendNotification = false;
        }

        // if this is an offline order that was not completed by the customer then we do not send an email notification to the user
        if (($archivedOrderData['order']['isofflineorder'] == 1) && ($archivedOrderData['order']['isofflineordercompletedbycustomer'] == 0))
        {
            $sendNotification = false;
        }

        $emailName = '';
        $emailAddress = '';
        $emailNameBCC = '';
        $emailAddressBCC = '';

        if ($sendNotification)
        {
            // decide where to send email to
            switch ($archivedOrderData['order']['useremaildestination'])
            {
                case 0: // billing address;
                    $emailName = $archivedOrderData['order']['billingcontactfirstname'] . ' ' . $archivedOrderData['order']['billingcontactlastname'];
                    $emailAddress = $archivedOrderData['order']['billingcustomeremailaddress'];
                    $emailNameBCC = '';
                    $emailAddressBCC = '';
                    break;
                case 1: // shipping address
                    $emailName = $archivedOrderData['shipping'][0]['shippingcontactfirstname'] . ' ' . $archivedOrderData['shipping'][0]['shippingcontactlastname'];
                    $emailAddress = $archivedOrderData['shipping'][0]['shippingcustomeremailaddress'];
                    $emailNameBCC = '';
                    $emailAddressBCC = '';
                    break;
                case 2: // shipping address and bcc to billing address
                    $emailName = $archivedOrderData['shipping'][0]['shippingcontactfirstname'] . ' ' . $archivedOrderData['shipping'][0]['shippingcontactlastname'];
                    $emailAddress = $archivedOrderData['shipping'][0]['shippingcustomeremailaddress'];
                    $emailNameBCC = $archivedOrderData['order']['billingcontactfirstname'] . ' ' . $archivedOrderData['order']['billingcontactlastname'];
                    $emailAddressBCC = $archivedOrderData['order']['billingcustomeremailaddress'];
                    break;
                case 3: // billing address and bcc to shipping address
                    $emailName = $archivedOrderData['order']['billingcontactfirstname'] . ' ' . $archivedOrderData['order']['billingcontactlastname'];
                    $emailAddress = $archivedOrderData['order']['billingcustomeremailaddress'];
                    $emailNameBCC = $archivedOrderData['shipping'][0]['shippingcontactfirstname'] . ' ' . $archivedOrderData['shipping'][0]['shippingcontactlastname'];
                    $emailAddressBCC = $archivedOrderData['shipping'][0]['shippingcustomeremailaddress'];
                    break;
            }

            // could be blank
            if ($smtpOrderConfirmationAddress != '')
            {
                if ($emailAddressBCC != '')
                {
                    $emailNameBCC .= ';';
                    $emailAddressBCC .= ';';
                }

                $emailNameBCC .= $smtpOrderConfirmationName;
                $emailAddressBCC .= $smtpOrderConfirmationAddress;
            }
        }

        $formattedOrderTotal = UtilsObj::formatCurrencyNumber($archivedOrderData['order']['ordertotal'],
                        $archivedOrderData['order']['currencydecimalplaces'], $archivedOrderData['browserlanguagecode'],
                        $archivedOrderData['order']['currencysymbol'], $archivedOrderData['order']['currencysymbolatfront']);
        $tempOrderExpiryDate = $archivedOrderData['order']['temporderexpirydate'];

		$additionalPaymentInfo = '';

		// get additional payment information from the payment gateway
		if ($archivedOrderData['order']['paymentmethodcode'] == 'CARD' || $archivedOrderData['order']['paymentmethodcode'] == 'PAYPAL' || $archivedOrderData['order']['paymentmethodcode'] == 'KLARNA')
		{
			if (! class_exists('PaymentIntegrationObj'))
			{
				require_once('../Order/PaymentIntegration/PaymentIntegration.php');
			}

			$additionalPaymentInfo = PaymentIntegrationObj::ccAdditionalPaymentInfo(array('paymentid' => $archivedOrderData['order']['ccitransactionid']));

			if ($additionalPaymentInfo != '')
			{
				$additionalPaymentInfo = strip_tags($additionalPaymentInfo);
			}
		}

        $emailObj = new TaopixMailer();
        $emailObj->sendTemplateEmail($pEmailTemplate, $archivedOrderData['webbrandcode'], $archivedOrderData['webbrandapplicationname'],
                $archivedOrderData['webbranddisplayurl'], $archivedOrderData['browserlanguagecode'], $emailName, $emailAddress,
                $emailNameBCC, $emailAddressBCC, $archivedOrderData['userid'],
                Array(
            'orderid' => $pOrderHeaderID,
            'userid' => $archivedOrderData['userid'],
            'loginname' => $archivedOrderData['userlogin'],
            'currencycode' => $archivedOrderData['order']['currencycode'],
            'currencyname' => LocalizationObj::getLocaleString($archivedOrderData['order']['currencyname'],
                    $archivedOrderData['browserlanguagecode'], true),
            'ordernumber' => $archivedOrderData['order']['ordernumber'],
            'qty' => $archivedOrderData['items'][0]['itemqty'],
            'pagecount' => $archivedOrderData['items'][0]['itempagecount'],
            'productcode' => $archivedOrderData['items'][0]['itemproductcode'],
            'productname' => LocalizationObj::getLocaleString($archivedOrderData['items'][0]['itemproductname'],
                    $archivedOrderData['browserlanguagecode'], true),
            // dont care about the following because they are not used in the new email content any more
            'defaultcovercode' => '',
            'defaultpapercode' => '',
            'defaultpagecount' => 0,
            'covercount' => 0,
            'covercode' => '',
            'covername' => '',
            'papercount' => 0,
            'papercode' => '',
            'papername' => '',
            'vouchercode' => $archivedOrderData['order']['vouchercode'],
            'vouchername' => LocalizationObj::getLocaleString($archivedOrderData['order']['vouchername'],
                    $archivedOrderData['browserlanguagecode'], true),
            'ordertotal' => $archivedOrderData['order']['ordertotal'],
            'ordergiftcardtotal' => $archivedOrderData['order']['ordergiftcardtotal'],
            'ordertotaltopay' => $archivedOrderData['order']['ordertotaltopay'],
            'formattedordertotal' => $formattedOrderTotal,
            'formattedordergiftcardtotal' => $formattedOrderGiftCardTotal,
            'formattedordertotaltopay' => $formattedOrderTotalToPay,
            'orderdate' => $orderDate,
            'formattedorderdatetime' => LocalizationObj::formatLocaleDateTime($orderDate, $archivedOrderData['browserlanguagecode'], true),
            'formattedorderdate' => LocalizationObj::formatLocaleDate($orderDate, $archivedOrderData['browserlanguagecode'], true),
            'formattedordertime' => LocalizationObj::formatLocaleTime($orderDate, $archivedOrderData['browserlanguagecode'], true),
            'expirydate' => $tempOrderExpiryDate,
            'formattedexpirydatetime' => LocalizationObj::formatLocaleDateTime($tempOrderExpiryDate,
                    $archivedOrderData['browserlanguagecode'], true),
            'formattedexpirydate' => LocalizationObj::formatLocaleDate($tempOrderExpiryDate, $archivedOrderData['browserlanguagecode'], true),
            'formattedexpirytime' => LocalizationObj::formatLocaleTime($tempOrderExpiryDate, $archivedOrderData['browserlanguagecode'], true),
            'shippingaddress' => $shippingAddress,
            'shippingcontactname' => $archivedOrderData['shipping'][0]['shippingcontactfirstname'] . ' ' . $archivedOrderData['shipping'][0]['shippingcontactlastname'],
            'shippingqty' => $archivedOrderData['shipping'][0]['shippingqty'],
            'shippingcustomername' => $archivedOrderData['shipping'][0]['shippingcustomername'],
            'shippingcustomeraddress1' => $archivedOrderData['shipping'][0]['shippingcustomeraddress1'],
            'shippingcustomeraddress2' => $archivedOrderData['shipping'][0]['shippingcustomeraddress2'],
            'shippingcustomeraddress3' => $archivedOrderData['shipping'][0]['shippingcustomeraddress3'],
            'shippingcustomeraddress4' => $archivedOrderData['shipping'][0]['shippingcustomeraddress4'],
            'shippingcustomercity' => $archivedOrderData['shipping'][0]['shippingcustomercity'],
            'shippingcustomercounty' => $archivedOrderData['shipping'][0]['shippingcustomercounty'],
            'shippingcustomerstate' => $archivedOrderData['shipping'][0]['shippingcustomerstate'],
            'shippingcustomerregioncode' => $archivedOrderData['shipping'][0]['shippingcustomerregioncode'],
            'shippingcustomerregion' => $archivedOrderData['shipping'][0]['shippingcustomerregion'],
            'shippingcustomerpostcode' => $archivedOrderData['shipping'][0]['shippingcustomerpostcode'],
            'shippingcustomercountrycode' => $archivedOrderData['shipping'][0]['shippingcustomercountrycode'],
            'shippingcustomercountryname' => $archivedOrderData['shipping'][0]['shippingcustomercountryname'],
            'shippingcustomertelephonenumber' => $archivedOrderData['shipping'][0]['shippingcustomertelephonenumber'],
            'shippingcustomeremailaddress' => $archivedOrderData['shipping'][0]['shippingcustomeremailaddress'],
            'shippingcontactfirstname' => $archivedOrderData['shipping'][0]['shippingcontactfirstname'],
            'shippingcontactlastname' => $archivedOrderData['shipping'][0]['shippingcontactlastname'],
            'shippingmethodcode' => $archivedOrderData['shipping'][0]['shippingmethodcode'],
            'shippingmethodname' => LocalizationObj::getLocaleString($archivedOrderData['shipping'][0]['shippingmethodname'],
                    $archivedOrderData['browserlanguagecode'], true),
            'shippingmethod' => LocalizationObj::getLocaleString($archivedOrderData['shipping'][0]['shippingmethodname'],
                    $archivedOrderData['browserlanguagecode'], true), // leave 'shippingmethod' in in order not to break existing templates, but really it should be 'shippingmethodname'
            'shippingratecode' => $archivedOrderData['shipping'][0]['shippingratecode'],
            'shippingrateinfo' => LocalizationObj::getLocaleString($archivedOrderData['shipping'][0]['shippingrateinfo'],
                    $archivedOrderData['browserlanguagecode'], true),
            'shippingratecost' => $archivedOrderData['shipping'][0]['shippingratecost'],
            'shippingratesell' => $archivedOrderData['shipping'][0]['shippingratesell'],
            'shippingratesellnotax' => $archivedOrderData['shipping'][0]['shippingratesellnotax'],
            'shippingratesellwithtax' => $archivedOrderData['shipping'][0]['shippingratesellwithtax'],
            'shippingratediscountvalue' => $archivedOrderData['shipping'][0]['shippingratediscountvalue'],
            'shippingratetotalsell' => $archivedOrderData['shipping'][0]['shippingratetotalsell'],
            'shippingratetotalsellnotax' => $archivedOrderData['shipping'][0]['shippingratetotalsellnotax'],
            'shippingratetotalsellwithtax' => $archivedOrderData['shipping'][0]['shippingratetotalsellwithtax'],
            'shippingratetaxcode' => $archivedOrderData['shipping'][0]['shippingratetaxcode'],
            'shippingratetaxname' => LocalizationObj::getLocaleString($archivedOrderData['shipping'][0]['shippingratetaxname'],
                    $archivedOrderData['browserlanguagecode'], true),
            'shippingratetaxrate' => $archivedOrderData['shipping'][0]['shippingratetaxrate'],
            'shippingratetaxexempt' => $archivedOrderData['shipping'][0]['shippingratetaxexempt'],
            'shippingratecalctax' => $archivedOrderData['shipping'][0]['shippingratecalctax'],
            'shippingratetaxtotal' => $archivedOrderData['shipping'][0]['shippingratetaxtotal'],
            'storecode' => $storeCode,
            'storeopeningtimes' => LocalizationObj::getLocaleString($storeOpeningTimes, $archivedOrderData['browserlanguagecode'], true),
            'storeurl' => $storeURL,
            'storeemailaddress' => $storeEmailAddress,
            'storetelephonenumber' => $storeTelephoneNumber,
            'storecontactname' => $storeContactName,
            'storeonline' => $storeOnline,
            'billingcontactname' => $archivedOrderData['order']['billingcontactfirstname'] . ' ' . $archivedOrderData['order']['billingcontactlastname'],
            'billingcustomeraccountcode' => $archivedOrderData['order']['billingcustomeraccountcode'],
            'billingcustomername' => $archivedOrderData['order']['billingcustomername'],
            'billingcustomeraddress1' => $archivedOrderData['order']['billingcustomeraddress1'],
            'billingcustomeraddress2' => $archivedOrderData['order']['billingcustomeraddress2'],
            'billingcustomeraddress3' => $archivedOrderData['order']['billingcustomeraddress3'],
            'billingcustomeraddress4' => $archivedOrderData['order']['billingcustomeraddress4'],
            'billingcustomercity' => $archivedOrderData['order']['billingcustomercity'],
            'billingcustomercounty' => $archivedOrderData['order']['billingcustomercounty'],
            'billingcustomerstate' => $archivedOrderData['order']['billingcustomerstate'],
            'billingcustomerregioncode' => $archivedOrderData['order']['billingcustomerregioncode'],
            'billingcustomerregion' => $archivedOrderData['order']['billingcustomerregion'],
            'billingcustomerpostcode' => $archivedOrderData['order']['billingcustomerpostcode'],
            'billingcustomercountrycode' => $archivedOrderData['order']['billingcustomercountrycode'],
            'billingcustomercountryname' => $archivedOrderData['order']['billingcustomercountryname'],
            'billingcustomertelephonenumber' => $archivedOrderData['order']['billingcustomertelephonenumber'],
            'billingcustomeremailaddress' => $archivedOrderData['order']['billingcustomeremailaddress'],
            'billingcontactfirstname' => $archivedOrderData['order']['billingcontactfirstname'],
            'billingcontactlastname' => $archivedOrderData['order']['billingcontactlastname'],
            'billingcustomerregisteredtaxnumbertype' => $archivedOrderData['order']['billingcustomerregisteredtaxnumbertype'],
            'billingcustomerregisteredtaxnumber' => $archivedOrderData['order']['billingcustomerregisteredtaxnumber'],
            'billingaddress' => $billingAddress,
            'paymentmethodcode' => $archivedOrderData['order']['paymentmethodcode'],
            'paymentmethodname' => LocalizationObj::getLocaleString($archivedOrderData['order']['paymentmethodname'],
                    $archivedOrderData['browserlanguagecode'], true),
            'ccicardnumber' => $archivedOrderData['order']['ccicardnumber'],
            'ccitransactionid' => $archivedOrderData['order']['ccitransactionid'],
            'cciauthorisationid' => $archivedOrderData['order']['cciauthorisationid'],
			'ccipaymentmeans' => $archivedOrderData['order']['ccipaymentmeans'],
			'ccitransactiontype' => $archivedOrderData['order']['ccitransactiontype'],
			'ccicharges' => $archivedOrderData['order']['ccicharges'],
            'requiresupload' => 0, // set this to 0 because it is not needed in Email
            'source' => $archivedOrderData['items'][0]['source'],
            'emailContent' => $htmlContent,
			'additionalpaymentinfo' => $additionalPaymentInfo,
			'targetuserid' => $archivedOrderData['userid']), '', ''
        );

    }

    static function prepareOrderDataForTrackingCodes()
    {
        global $gSession;
        global $gConstants;

        $resultArray = Array();

        $defaultBrandData = DatabaseObj::getBrandingFromCode('');

        $brandDataArray = DatabaseObj::getBrandingFromCode($gSession['webbrandcode']);
        $gooogleAnalyticsCode = $brandDataArray['googleanalyticscode'];

        if ($brandDataArray['weburl'] == '')
        {
            $webURL = $defaultBrandData['weburl'];
        }
        else
        {
            $webURL = $brandDataArray['weburl'];
        }

        $resultArray['ordernumber'] = $gSession['order']['ordernumber'];
        $resultArray['datecreatedunixtimestamp'] = $gSession['order']['starttime'];
        $resultArray['formatteddatecreated'] = date('y-m-d H:i:s', $gSession['order']['starttime']);
        $resultArray['brandcode'] = $gSession['webbrandcode'];
		$resultArray['total'] = UtilsObj::formatNumber($gSession['order']['ordertotaltopay'], $gSession['order']['currencydecimalplaces']);
		$resultArray['shippingtotal'] = UtilsObj::formatNumber($gSession['shipping'][0]['shippingratetotalsellnotax'], $gSession['order']['currencydecimalplaces']);
        $resultArray['vouchercode'] = $gSession['order']['vouchercode'];
        $resultArray['ordertotaldiscount'] = UtilsObj::formatNumber($gSession['order']['ordertotaldiscount'], $gSession['order']['currencydecimalplaces']);
        $resultArray['ordercurrency'] = $gSession['order']['currencycode'];
        $resultArray['billingcustomeremailaddress'] = $gSession['order']['billingcustomeremailaddress'];
        $resultArray['billingcontactfirstname'] = $gSession['order']['billingcontactfirstname'];
        $resultArray['billingcontactlastname'] = $gSession['order']['billingcontactlastname'];
        $resultArray['city'] = $gSession['shipping'][0]['shippingcustomercity'];
        $resultArray['state'] = $gSession['shipping'][0]['shippingcustomerstate'];
        $resultArray['county'] = $gSession['shipping'][0]['shippingcustomercounty'];
        $resultArray['country'] = $gSession['shipping'][0]['shippingcustomercountrycode'];
        $resultArray['googleanalyticscode'] = $gooogleAnalyticsCode;
        $resultArray['googleanalyticsdomainname'] = UtilsObj::getWebURLBaseDomain($webURL);
		$resultArray['googleanalyticsuseridtracking'] = $brandDataArray['googleanalyticsuseridtracking'];
		$resultArray['userid'] = $gSession['userid'];

        $orderLineQty = 0;
        $orderTaxTotal = 0;

        foreach ($gSession['items'] as $currentLine => $orderLine)
        {
            $orderLineQty += $orderLine['itemqty'];
            $orderTaxTotal += $orderLine['itemtaxtotal'];
			$itemUnitPrice = UtilsObj::formatNumber($orderLine['itemtotalsellnotax'] / $orderLine['itemqty'], $gSession['order']['currencydecimalplaces']);

            $resultArray['orderlines'][] = Array('productcode' => $orderLine['itemproductcode'], 'productname' => LocalizationObj::getLocaleString($orderLine['itemproductname'],
                        $gConstants['defaultlanguagecode'], true), 'qty' => $orderLine['itemqty'], 'price' => $itemUnitPrice);
        }

        $orderTaxTotal += $gSession['shipping'][0]['shippingratetaxtotal'] + $gSession['order']['orderfootertotaltax'];

        $resultArray['orderlineqty'] = $orderLineQty;
		$resultArray['ordertaxtotal'] = UtilsObj::formatNumber($orderTaxTotal, $gSession['order']['currencydecimalplaces']);

        return $resultArray;
    }

	static function initPaymentGatewayPaymentOptions($pPaymentMethod)
	{
		require_once('PaymentIntegration/PaymentIntegration.php');

		$resultArray = PaymentIntegrationObj::ccInitPaymentGatewayPaymentOptions($pPaymentMethod);

		return $resultArray;
	}

	static function initPayNowOrder($pSessionRef)
	{
		$returnArray = UtilsObj::getReturnArray();

		if ($pSessionRef > 0)
		{
			$sessionArray = DatabaseObj::getSessionData($pSessionRef);

            if ($sessionArray['ref'] > 0)
            {
				// we came from your orders which means the user has already been authenticated so switch off cookie validation
				$sessionArray['authenticatecookie'] = 0;
				DatabaseObj::updateSession2($sessionArray);

				$returnArray['data']['shoppingcarturl'] = UtilsObj::getBrandedWebUrl() . '?fsaction=Order.revive&ref=' . $pSessionRef;
			}
		}
		else
		{
			$returnArray['error'] = 'str_ErrorNoSessionRef';
		}

		return $returnArray;
	}

	static function setShippingMethodDefaults(&$pShippingMethodEntry)
	{
        global $gSession;
        $shippingMethodCode = $gSession['shipping'][0]['shippingmethodcode'];
        $previousStoreCode = '';
        if (isset($gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeCode']))
        {
            $previousStoreCode = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeCode'];
        }
        $storecontactfirstname = '';
        $storecontactlastname = '';
        $storecontacttelephone = '';
        $storecontactemail = '';

        //if we previously selected a store keep the previous contact details
        if ($previousStoreCode != '')
        {
            $storecontactfirstname = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecontactfirstname'];
            $storecontactlastname = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecontactlastname'];
            $storecontacttelephone = $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomertelephonenumber'];
            $storecontactemail =  $gSession['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress']['storecustomeremailaddress'];
        }

		$pShippingMethodEntry['collectFromStore'] = false;
		$pShippingMethodEntry['storeCode'] = $previousStoreCode;
		$pShippingMethodEntry['externalstore'] = '';
		$pShippingMethodEntry['distributionCentreCode'] = '';

		$pShippingMethodEntry['storeIsFixed'] = false;
		$pShippingMethodEntry['useScript'] = false;
		$pShippingMethodEntry['useExternalData'] = TPX_EDL_CFS_SEARCH_FILTERED;
		$pShippingMethodEntry['privateData'] = '';

		$pShippingMethodEntry['storeLocator'] = array(); // only needed if collect from store
		$pShippingMethodEntry['storeAddress'] = array(); // only needed if collect from store
		$pShippingMethodEntry['storeAddress']['storecustomername'] = '';
		$pShippingMethodEntry['storeAddress']['storecustomeraddress1'] = '';
		$pShippingMethodEntry['storeAddress']['storecustomeraddress2'] = '';
		$pShippingMethodEntry['storeAddress']['storecustomeraddress3'] = '';
		$pShippingMethodEntry['storeAddress']['storecustomeraddress4'] = '';
		$pShippingMethodEntry['storeAddress']['storecustomercity'] = '';
		$pShippingMethodEntry['storeAddress']['storecustomercounty'] = '';
		$pShippingMethodEntry['storeAddress']['storecustomerstate'] = '';
		$pShippingMethodEntry['storeAddress']['storecustomerregioncode'] = '';
		$pShippingMethodEntry['storeAddress']['storecustomerregion'] = '';
		$pShippingMethodEntry['storeAddress']['storecustomerpostcode'] = '';
		$pShippingMethodEntry['storeAddress']['storecustomercountrycode'] = '';
		$pShippingMethodEntry['storeAddress']['storecustomercountryname'] = '';
		$pShippingMethodEntry['storeAddress']['storecustomertelephonenumber'] = $storecontacttelephone;
		$pShippingMethodEntry['storeAddress']['storecustomeremailaddress'] = $storecontactemail;
		$pShippingMethodEntry['storeAddress']['storecontactfirstname'] = $storecontactfirstname;
		$pShippingMethodEntry['storeAddress']['storecontactlastname'] = $storecontactlastname;
		$pShippingMethodEntry['storeAddress']['storecontacturl'] = '';
	}

	static function setCollectFromStoreValues($pShippingMethodCode, $pShippingMethodName, &$shippingMethodEntry, $pGroupCode)
	{
		global $gSession;

		$shippingMethodEntry['collectFromStore'] = true;

		$shippingMethodEntry['storeLocator']['country'] = '';
		$shippingMethodEntry['storeLocator']['region'] = '';
		$shippingMethodEntry['storeLocator']['storeGroup'] = '';
		$shippingMethodEntry['storeLocator']['filter'] = '';
		$shippingMethodEntry['storeLocator']['privateFilter'] = '';

		UtilsObj::includeStoreLocatorScript();

		if (method_exists('EDL_StoreLocatorObj', 'initialize'))
		{
			$paramArray = array();
			$paramArray['billingcustomercountrycode'] = $gSession['order']['billingcustomercountrycode'];
			$paramArray['billingcustomeraddress1'] = $gSession['order']['billingcustomeraddress1'];
			$paramArray['billingcustomeraddress2'] = $gSession['order']['billingcustomeraddress2'];
			$paramArray['billingcustomeraddress3'] = $gSession['order']['billingcustomeraddress3'];
			$paramArray['billingcustomeraddress4'] = $gSession['order']['billingcustomeraddress4'];
			$paramArray['billingcustomercity'] = $gSession['order']['billingcustomercity'];
			$paramArray['billingcustomerregioncode'] = $gSession['order']['billingcustomerregioncode'];
			$paramArray['billingcustomerpostcode'] = $gSession['order']['billingcustomerpostcode'];
			$paramArray['groupcode'] = $pGroupCode;
			$paramArray['groupdata'] = $gSession['licensekeydata']['groupdata'];
			$paramArray['webbrandcode'] = $gSession['webbrandcode'];
			$paramArray['shippingmethodcode'] = $pShippingMethodCode;
			$paramArray['shippingmethodname'] = LocalizationObj::getLocaleString($pShippingMethodName, $gSession['browserlanguagecode'], true);
			$paramArray['browserlanguagecode'] = $gSession['browserlanguagecode'];
			$paramArray['privatedata'] = $shippingMethodEntry['privateData'];

			$settings = EDL_StoreLocatorObj::initialize($paramArray);

			if ($settings['result'] == '')
			{
				$shippingMethodEntry['useScript'] = true;

				// only set store code if it is not empty
				if ($settings['storecode'] != '')
				{
					// check if the store exists
					$siteArray = DatabaseObj::getSiteFromCode($settings['storecode']);

					// only use this code if it is the code of an active store
					if (($siteArray['sitetype'] == TPX_SITE_TYPE_STORE) && ($siteArray['isactive'] == 1))
					{
						$shippingMethodEntry['storeCode'] = $settings['storecode'];
						$shippingMethodEntry['externalstore'] = $siteArray['isexternalstore'];
						$shippingMethodEntry['storeIsFixed'] = true;
						$shippingMethodEntry['distributionCentreCode'] = $siteArray['distributioncentrecode'];
						$shippingMethodEntry['storeAddress']['storecustomername'] = $siteArray['companyname'];
						$shippingMethodEntry['storeAddress']['storecustomeraddress1'] = $siteArray['address1'];
						$shippingMethodEntry['storeAddress']['storecustomeraddress2'] = $siteArray['address2'];
						$shippingMethodEntry['storeAddress']['storecustomeraddress3'] = $siteArray['address3'];
						$shippingMethodEntry['storeAddress']['storecustomeraddress4'] = $siteArray['address4'];
						$shippingMethodEntry['storeAddress']['storecustomercity'] = $siteArray['city'];
						$shippingMethodEntry['storeAddress']['storecustomercounty'] = $siteArray['county'];
						$shippingMethodEntry['storeAddress']['storecustomerstate'] = $siteArray['state'];
						$shippingMethodEntry['storeAddress']['storecustomerregioncode'] = $siteArray['regioncode'];
						$shippingMethodEntry['storeAddress']['storecustomerregion'] = $siteArray['region'];
						$shippingMethodEntry['storeAddress']['storecustomerpostcode'] = $siteArray['postcode'];
						$shippingMethodEntry['storeAddress']['storecustomercountrycode'] = $siteArray['countrycode'];
						$shippingMethodEntry['storeAddress']['storecustomercountryname'] = $siteArray['countryname'];
						$shippingMethodEntry['storeAddress']['storecustomertelephonenumber'] = '';
						$shippingMethodEntry['storeAddress']['storecustomeremailaddress'] = '';
						$shippingMethodEntry['storeAddress']['storecontactfirstname'] = '';
						$shippingMethodEntry['storeAddress']['storecontactlastname'] = '';
					}
				}
				// set the script's private data
				$shippingMethodEntry['privateData'] = $settings['privatedata'];
			}
		}
	}

	static function buildShippingAPIParams($pShippingMethodList)
	{
		global $gSession;

		$paramArray = array();
		$paramArray['brandcode'] = $gSession['webbrandcode'];
		$paramArray['groupcode'] = $gSession['licensekeydata']['groupcode'];
		$paramArray['groupdata'] = $gSession['licensekeydata']['groupdata'];
		$paramArray['browserlanguagecode'] = $gSession['browserlanguagecode'];
		$paramArray['currencycode'] = $gSession['order']['currencycode'];
		$paramArray['currencydecimalplaces'] = $gSession['order']['currencydecimalplaces'];
		$paramArray['currencyexchangerate'] = $gSession['order']['currencyexchangerate'];
		$paramArray['shippingweight'] = $gSession['order']['ordertotalshippingweight'];
		$paramArray['shippingaddress'] = array();
		$paramArray['billingaddress'] = array();
		$paramArray['cartitems']['lineitems'] = $gSession['items'];
		$paramArray['cartitems']['orderfooteritems']['orderfootersections'] = $gSession['order']['orderFooterSections'];
		$paramArray['cartitems']['orderfooteritems']['orderfootercheckboxes'] = $gSession['order']['orderFooterCheckboxes'];
		$paramArray['shippingmethodslist'] = $pShippingMethodList;
		$paramArray['privatedata'] = $gSession['shipping'][0]['shippingprivatedata'];

		self::copyArrayAddressFields($gSession['shipping'][0], 'shippingcustomer', $paramArray['shippingaddress'], 'shipping', false, true);
		self::copyArrayAddressFields($gSession['order'], 'billingcustomer', $paramArray['billingaddress'], 'billing', false, true);

		return $paramArray;
	}

	static function getShippingRateFromMethodAndRateCode($pMethodCode, $pRateCode, $pGroupCode, $pCompanyCode)
    {
		$resultArray = UtilsObj::getReturnArray();

        $shippingRateID = 0;
        $parentID = 0;
        $shippingRateCode = '';
        $shippingMethodCode = '';
		$shippingMethodName = '';
        $productCode = '';
        $groupCode = '';
        $info = '';
        $shippingRates = '';
        $orderValueType = '';
        $orderMinValue = 0.00;
        $orderMaxValue = 0.00;
        $isActive = 0;
        $taxCode = '';
        $companyCode = '';
        $shippingZoneCode = '';
        $orderValueIncludesDiscount = '';
		$taxRate = 0.00;
		$useDefaultBillingAddress = 0;
		$useDefaultShippingAddress = 0;
		$isDefault = 0;
		$collectFromStore = 0;
		$payInStoreAllowed = 0;
		$canModifyContactDetails = 0;
		$requiresDelivery = 0;

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            $stmt = $dbObj->prepare('SELECT `sr`.`id`, `sr`.`parentid`, `sr`.`companycode`, `sr`.`code`, `sr`.`shippingmethodcode`, `sr`.`shippingzonecode`,
					`sr`.`productcode`, `sr`.`groupcode`, `sr`.`info`, `sr`.`rate`, `sr`.`ordervaluetype`, `sr`.`orderminimumvalue`,
					`sr`.`ordermaximumvalue`, `sr`.`ordervalueincludesdiscount`, `sr`.`payinstoreallowed`, `sr`.`taxcode`, `sr`.`active`,
					IF (tr.rate IS NULL, "0.00", tr.rate), `sm`.`usedefaultbillingaddress`, `sm`.`usedefaultshippingaddress`,
					`sm`.`default`, `sm`.`collectfromstore`, `sm`.`canmodifycontactdetails`, `sm`.`requiresdelivery`, `sm`.`name`
				FROM `SHIPPINGRATES` sr
				LEFT JOIN `SHIPPINGMETHODS` sm ON `sm`.`code` = `sr`.`shippingmethodcode`
				LEFT JOIN `TAXRATES` tr ON `sr`.`taxcode` = `tr`.`code`
				WHERE
					`sr`.`shippingmethodcode` = ?
				AND
					`sr`.`code` = ?
				AND
					`sr`.`groupcode` = ?
				AND
					`sr`.`active` = 1
				AND
					(`sr`.`companycode` = ? OR `sr`.`companycode` = "")
				ORDER BY
					`sr`.`companycode` DESC
				LIMIT 1');

            if ($stmt)
            {
                if ($stmt->bind_param('ssss', $pMethodCode, $pRateCode, $pGroupCode, $pCompanyCode))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($shippingRateID, $parentID, $companyCode, $shippingRateCode, $shippingMethodCode,
                                                $shippingZoneCode, $productCode, $groupCode, $info, $shippingRates, $orderValueType,
                                                $orderMinValue, $orderMaxValue, $orderValueIncludesDiscount, $payInStoreAllowed, $taxCode,
                                                $isActive, $taxRate, $useDefaultBillingAddress, $useDefaultShippingAddress, $isDefault, $collectFromStore,
												$canModifyContactDetails, $requiresDelivery, $shippingMethodName))
                                {
                                    if ($stmt->fetch())
                                    {
                                        $row = array();
										$row['ratecode'] = $shippingRateCode;
										$row['zonecode'] = $shippingZoneCode;
										$row['methodcode'] = $shippingMethodCode;
										$row['methodname'] = $shippingMethodName;
										$row['info'] = $info;
										$row['usedefaultbillingaddress'] = $useDefaultBillingAddress;
										$row['usedefaultshippingaddress'] = $useDefaultShippingAddress;
										$row['canmodifycontactdetails'] = $canModifyContactDetails;
										$row['requiresdelivery'] = $requiresDelivery;
										$row['isdefault'] = $isDefault;
										$row['cost'] = 0.00;
										$row['payinstoreallowed'] = $payInStoreAllowed;
										$row['sell'] = 0.00;
										$row['taxcode'] = $taxCode;
										$row['taxrate'] = $taxRate;
										$row['collectfromstore'] = $collectFromStore;
										$row['orderminvalue'] = $orderMinValue;
										$row['ordermaxvalue'] = $orderMaxValue;

										$resultArray['data'] = $row;
                                    }
									else
									{
										$resultArray['error'] = __FUNCTION__ . ' fetch ' . $dbObj->error;
									}
                                }
                                else
                                {
                                    $resultArray['error'] = __FUNCTION__ . ' bind result ' . $dbObj->error;
                                }
                            }
							else
							{
								$resultArray['error'] = __FUNCTION__ . ' num rows ';
							}
                        }
                        else
                        {
                            $resultArray['error'] = __FUNCTION__ . ' store result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $resultArray['error'] = __FUNCTION__ . ' execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $resultArray['error'] = __FUNCTION__ . ' bind params ' . $dbObj->error;
                }

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
                $resultArray['error'] = __FUNCTION__ . ' prepare ' . $dbObj->error;
            }

            $dbObj->close();
        }

        return $resultArray;
    }

	static function applyExchangeRateToVoucher($pVoucherType, $pVoucherDiscountValue, $pExchangeRate)
	{
		$voucherDiscountValue = $pVoucherDiscountValue;

		switch ($pVoucherType)
		{
			// apply exchange rate to these voucher types
			case 'BOGVOFF':
            case 'VALUE':
            case 'VALUESET':
			{
				$voucherDiscountValue *= $pExchangeRate;
				break;
			}
		}

		return $voucherDiscountValue;
    }

    /**
     * This function has been created if you need to redirect to the manual but
     * unaware if the order has been created. Currenly this is being used as a
     * fallback for the WeChat gateway.
     * {@param} $pSessionRef - the sessionid column in the ccilog
     */

    static function checkCCIRecordExists($pSessionRef)
    {
        $dbObj = DatabaseObj::getGlobalDBConnection();

        $resultArray = [];
        $resultArray['orderfound'] = false;
        $resultArray['error'] = '';

        if($dbObj)
        {
            if ($stmt = $dbObj->prepare('SELECT `id` FROM CCILOG WHERE `sessionid` = ?'))
            {
                if ($stmt->bind_param('i', $pSessionRef));
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                $resultArray['orderfound'] = true;
                            }
                        }
                        else
                        {
                            $resultArray['error'] = $dbObj->error;
                        }
                    }
                    else
                    {
                        $resultArray['error'] = $dbObj->error;
                    }
                }
            }
        }

        if($resultArray['error'] != '')
        {
            error_log('Unable to retrieve order ' . $resultArray['error']);
        }

        return $resultArray;
	}

	static function insertAIOrderItemComponent($pItem, $pSortOrder, $pOrderHeaderRecordID, $pOrderItemRecordID, $pDbObj, $pAbandonOnError)
	{
		global $gSession;
		$resultArray = array('result' => '', 'resultparam' => '', 'dberrorcode' => 0);
		$aiComponentArray = array();

		// only insert if one is set against this project
		if (array_key_exists("aicomponent", $pItem) && (null !== $pItem['aicomponent']))
		{
			// the insert function expects the component to be an entry in an array
			$aiComponentArray[] = $pItem['aicomponent'];

			$resultArray = self::insertOrderItemComponents(0, $pSortOrder, $aiComponentArray, $pOrderHeaderRecordID, $pOrderItemRecordID,
				$gSession['userid'], $pItem['itemqty'], $pDbObj, TPX_ORDERITEMCOMPONENTARRAYTYPE_AI, false, $pAbandonOnError);
		}

		return $resultArray;
	}

	static function createAIComponentDataForInsertion($pComponent)
	{
		$resultArray = array();

		if ($pComponent['used'] === true)
		{
			$resultArray['componentunitsell'] = $pComponent['unitsell'];
			$resultArray['componenttotalcost'] = $pComponent['totalcost'];
			$resultArray['componenttotalsell'] = $pComponent['totalsell'];
			$resultArray['subtotal'] = $pComponent['subtotal'];
			$resultArray['componenttotalweight'] = $pComponent['totalweight'];
			$resultArray['componenttotaltax'] = $pComponent['totaltax'];
		}
		else
		{
			// the component hasn't actually been used but we want to create a dummy entry so that re-orders can use it if it is turned on in the future
			$resultArray['componentunitsell'] = 0;
			$resultArray['componenttotalcost'] = 0;
			$resultArray['componenttotalsell'] = 0;
			$resultArray['subtotal'] = 0;
			$resultArray['componenttotalweight'] = 0;
			$resultArray['componenttotaltax'] = 0;
		}

		$resultArray['componentdefaultcode'] = '';
		$resultArray['componentdescription'] = '';
		$resultArray['componentselectioncount'] = 1;
		$resultArray['servicecode'] = '';
		$resultArray['servicename'] = '';
		$resultArray['skucode'] = $pComponent['skucode'];
		$resultArray['externalassetid'] = 0;
		$resultArray['assetpricetype'] = 0;
		$resultArray['assetunitcost'] = 0.00;
		$resultArray['assetunitsell'] = 0.00;
		$resultArray['assetexpirydate'] = '';
		$resultArray['assetpagename'] = '';
		$resultArray['assetpagenumber'] = 0;
		$resultArray['assetpageref'] = '';
		$resultArray['componentcategoryname'] = '';
		$resultArray['assetboxref'] = '';
		$resultArray['componentcode'] = $pComponent['code'];
		$resultArray['componentlocalcode'] = $pComponent['localcode'];
		$resultArray['componentname'] = $pComponent['name'];
		$resultArray['itemcomponentinfo'] = $pComponent['info'];
		$resultArray['itemcomponentpriceinfo'] = '';
		$resultArray['componentpath'] = $pComponent['path'];
		$resultArray['componentcategorycode'] = $pComponent['categorycode'];
		$resultArray['pricingmodel'] = TPX_PRICINGMODEL_PERSIDEQTY;
		$resultArray['islist'] = true;
		$resultArray['componentunitcost'] = 0;
		$resultArray['componentunitweight'] = $pComponent['weight'];
		$resultArray['orderfootertaxname'] = '';
		$resultArray['orderfootertaxrate'] = 0;
		$resultArray['discountvalue'] = 0;
		$resultArray['discountedtax'] = 0;
		$resultArray['metadata'] = Array();
		$resultArray['itemqty'] = $pComponent['componentqty'];
		$resultArray['setid'] = 0;
		$resultArray['setname'] = '';

		return $resultArray;
	}

    /**
     * Assigns a userid to the desktop project thumbnail records
     *
     * @param int the userid to assign the projects
     * @return array error array containing success status
     */

    static function assignUserToDesktopProjectThumbnails($pUserID)
    {
        global $gSession;

        $resultArray = UtilsObj::getReturnArray();
        $projectRefArray = Array();

        //pull out all of the projectrefs to pass to the database
        foreach ($gSession['items'] as $lineItem)
        {
            $projectRefArray[] = $lineItem['itemprojectref'];
        }

        $resultArray = DatabaseObj::assignUserToDesktopProjectThumbnails($projectRefArray, $pUserID);

        return $resultArray;
    }
}
?>
