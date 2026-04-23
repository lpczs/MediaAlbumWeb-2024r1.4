<?php

use PricingEngine\OrderInterface;
use PricingEngine\CurrencyInterface;
use PricingEngine\Component\ComponentProductMapping;
use PricingEngine\Component\SectionComponent;
use PricingEngine\PriceBreakSet\PriceBreakSetInterface;
use PricingEngine\PriceBreakSet\PriceBreakSetFactory;
use PricingEngine\Component\ComponentRegistry;

interface _SessionHelper
{

	public static function validate($pPromoCode, $pVoucherCode, $pCurrentLine);

	public static function calcDiscountedValue($pPromoCode, $pVoucherCode, $pCurrentLine);

	public static function getOrderTotal();

	public static function returnPrice($pPrice);

	public static function returnPriceAndDescription($pPrice, $pDesc);

	public static function returnShipping($pShippingValue);

	public static function returnShippingAndDescription($pShippingValue, $pDesc);

	public static function returnOrderTotal($pOrderValue);

	public static function returnOrderTotalAndDescription($pOrderValue, $pDesc);

	public static function reduceByValue($pValue, $pPrice);

	public static function reduceByPercentage($pPercentage, $pPrice);

	public static function getItemByProductCode($pProductCode);

	public static function getItemByLine($pLine);

	public static function getProductPrice($pItem);

	public static function getComponentPrice($pCheckboxSection);

	public static function getCheckBox($pItem, $pCheckboxName);

	public static function isCheckboxChecked($pCheckbox);

	public static function getCheckBoxes($pItem, $pChecked = true);

	public static function getSections($pItem);

	public static function getSection($pItem, $pSectionName);

	public static function getItemQty($pItem);

	public static function getComponent($pItem, $pComponentName);

	public static function getSubComponent($pItem, $pComponentName);

	public static function getMetaData($pComponent, $pMetaDataName = '');

	public static function getMetaDataValue($pMetaData);

	public static function getMetaDataWithValue($pComponent, $pMetaDataName, $pValue);

	public static function calcComponentPrice($pItem, $pComponent, $pItemQty, $pComponentQty, $pPageCount, $pCurrentLine);

	public static function changeComponentCode($pComponent, $pNewCode);

	public static function calcProductPrice($pProductCode, $pQty, $pCurrentLine);

	public static function getProductCode($pItem);

	public static function getPageCount($pItem);

	public static function getComponentQty($pComponent);

	public static function getPhotoPrintComponent($pItem, $pComponentName, $pSubComponent);

	public static function calcPhotoPrintComponentQty($pItemsArray);

	public static function calcPhotoPrintComponentPrice($pItemsArray);

	public static function getUserID();

	public static function getLineItemPrice($pItem);

	public static function getBrowserLanguagecode();
}

class SessionHelper implements _SessionHelper
{

	public static function validate($pPromoCode, $pVoucherCode, $pCurrentLine)
	{
		return false;
	}

	public static function calcDiscountedValue($pPromoCode, $pVoucherCode, $pCurrentLine)
	{
		$returnArray = array();

		$returnArray['discountvalue'] = 0;
		$returnArray['discountname'] = '';
		$returnArray['ordertotaldiscountvalue'] = 0;
		$returnArray['shippingdiscountvalue'] = 0;
		$returnArray['sellprice'] = 0.00;
		$returnArray['agentfee'] = 0.00;

		return $returnArray;
	}

	public static function getOrderTotal()
	{
		global $gSession;

		$itemProductTotalSell = 0;

		// Determine which values we are going to use based on if prices are being displayed with tax.
		if ($gSession['order']['showpriceswithtax'] == 0)
		{
			$itemProductTotalSell = $gSession['order']['ordertotalitemsellnotaxnodiscount'];
		}
		else
		{
			$itemProductTotalSell = $gSession['order']['ordertotalitemsellwithtaxnodiscount'];
		}

		return $itemProductTotalSell;
	}

	public static function returnPrice($pPrice)
	{
		$returnArray = array();

		$returnArray['discountvalue'] = $pPrice;
		$returnArray['discountname'] = '';
		$returnArray['ordertotaldiscountvalue'] = 0;
		$returnArray['shippingdiscountvalue'] = 0;
		$returnArray['sellprice'] = 0.00;
		$returnArray['agentfee'] = 0.00;

		return $returnArray;
	}

	public static function returnPriceAndDescription($pPrice, $pDesc)
	{
		$returnArray = array();

		$returnArray['discountvalue'] = $pPrice;
		$returnArray['discountname'] = $pDesc;
		$returnArray['ordertotaldiscountvalue'] = 0;
		$returnArray['shippingdiscountvalue'] = 0;
		$returnArray['sellprice'] = 0.00;
		$returnArray['agentfee'] = 0.00;

		return $returnArray;
	}

	public static function returnShipping($pShippingValue)
	{
		$returnArray = array();

		$returnArray['discountvalue'] = 0;
		$returnArray['discountname'] = '';
		$returnArray['ordertotaldiscountvalue'] = 0;
		$returnArray['shippingdiscountvalue'] = $pShippingValue;
		$returnArray['sellprice'] = 0.00;
		$returnArray['agentfee'] = 0.00;

		return $returnArray;

	}

	public static function returnShippingAndDescription($pShippingValue, $pDesc)
	{
		$returnArray = array();

		$returnArray['discountvalue'] = 0;
		$returnArray['discountname'] = $pDesc;
		$returnArray['ordertotaldiscountvalue'] = 0;
		$returnArray['shippingdiscountvalue'] = $pShippingValue;
		$returnArray['sellprice'] = 0.00;
		$returnArray['agentfee'] = 0.00;

		return $returnArray;

	}

	public static function returnOrderTotal($pOrderValue)
	{
		$returnArray = array();

		$returnArray['discountvalue'] = 0;
		$returnArray['discountname'] = '';
		$returnArray['ordertotaldiscountvalue'] = $pOrderValue;
		$returnArray['shippingdiscountvalue'] = 0;
		$returnArray['sellprice'] = 0.00;
		$returnArray['agentfee'] = 0.00;

		return $returnArray;

	}

	public static function returnOrderTotalAndDescription($pOrderValue, $pDesc)
	{
		$returnArray = array();

		$returnArray['discountvalue'] = 0;
		$returnArray['discountname'] = $pDesc;
		$returnArray['ordertotaldiscountvalue'] = $pOrderValue;
		$returnArray['shippingdiscountvalue'] = 0;
		$returnArray['sellprice'] = 0.00;
		$returnArray['agentfee'] = 0.00;

		return $returnArray;

	}

	public static function returnSellPriceAndAgentFee($pSellPrice, $pAgentFee)
	{
		$returnArray = array();

		$returnArray['discountvalue'] = 0;
		$returnArray['discountname'] = '';
		$returnArray['ordertotaldiscountvalue'] = 0;
		$returnArray['shippingdiscountvalue'] = 0;
		$returnArray['sellprice'] = $pSellPrice;
		$returnArray['agentfee'] = $pAgentFee;

		return $returnArray;
	}

	public static function reduceByValue($pValue, $pPrice)
	{
		$finalPrice = $pPrice - $pValue;

		if ($finalPrice < 0)
		{
			$finalPrice = 0;
		}

		return $finalPrice;
	}

	public static function reduceByPercentage($pPercentage, $pPrice)
	{
		return ($pPrice * ($pPercentage / 100));
	}

	public static function getProductCode($pItem)
	{
		return $pItem['itemproductcode'];
	}

	public static function getItemList()
	{
		global $gSession;

		return $gSession['items'];
	}

	public static function getItemByProductCode($pProductCode)
	{
		global $gSession;

		$returnItem = null;

		foreach ($gSession['items'] as $item)
		{
			if ($pProductCode == $item['itemproductcode'])
			{
				$returnItem = $item;
			}
		}

		return $returnItem;
	}

	public static function getItemByLine($pLine)
	{
		global $gSession;

		return $gSession['items'][$pLine];
	}

	public static function getShippingCost()
	{
		global $gSession;

		return $gSession['shipping'][0]['shippingratesell'];
	}

	public static function getVoucherSellPriceAndAgentFee()
	{
		global $gSession;

		return array(
			'sellprice' => $gSession['order']['vouchersellprice'],
			'agentfee' => $gSession['order']['voucheragentfee']
		);
	}

	public static function getProductPrice($pItem)
	{
		return $pItem['itemproducttotalsell'];
	}

	public static function getComponentPrice($pCheckboxSection)
	{
		$returnValue = null;

		if (isset($pCheckboxSection['totalsell']))
		{
			$returnValue = $pCheckboxSection['totalsell'];
		}
		else
		{
			// Photoprint session data has a different structure.
			$returnValue = $pCheckboxSection['pricedata']['ts'] + $pCheckboxSection['pricedata']['subts'];
		}

		return $returnValue;
	}

	public static function getUnCheckedCheckBox($pItem, $pCheckboxName)
	{
		$returnCheckbox = self::getCheckBox($pItem, $pCheckboxName);

		if (self::isCheckboxChecked($returnCheckbox))
		{
			$returnCheckbox = null;
		}

		return $returnCheckbox;
	}

	public static function getCheckedCheckBox($pItem, $pCheckboxName)
	{
		$returnCheckbox = self::getCheckBox($pItem, $pCheckboxName);

		if (!self::isCheckboxChecked($returnCheckbox))
		{
			$returnCheckbox = null;
		}

		return $returnCheckbox;
	}

	public static function getCheckBox($pItem, $pCheckboxName)
	{
		$returnCheckbox = null;

		if (!empty($pItem['checkboxes'])) {
			foreach ($pItem['checkboxes'] as $checkbox)
			{
				if ($checkbox['code'] == $pCheckboxName)
				{
					$returnCheckbox = $checkbox;
				}
			}
		}

		return $returnCheckbox;
	}

	public static function isCheckboxChecked($pCheckbox)
	{
		return ($pCheckbox['checked'] == 1);
	}

	public static function getCheckBoxes($pItem, $pChecked = true)
	{
		$checkboxes = $pItem['checkboxes'];

		if ($pChecked)
		{
			foreach ($pItem['checkboxes'] as $checkbox)
			{
				if (self::isCheckboxChecked($checkbox))
				{
					$checkboxes[] = $checkbox;
				}
			}
		}

		return $checkboxes;
	}

	public static function getSections($pItem)
	{
		return $pItem['sections'];
	}

	public static function getSection($pItem, $pSectionName)
	{
		if (empty($pItem)) {
			return null;
		}

		$returnSection = null;

		if (isset($pItem['subsections']))
		{
			$lookAt = 'subsections';
		}
		else if (isset($pItem['sections']))
		{
			$lookAt = 'sections';
		}
		else
		{
			return null;
		}

		foreach ($pItem[$lookAt] as $section)
		{
			if ($section['code'] == $pSectionName)
			{
				$returnSection = $section;
			}
		}

		return $returnSection;
	}

	public static function getItemQty($pItem)
	{
		return $pItem['itemqty'];
	}

	public static function getComponent($pItem, $pComponentName)
	{
		$returnComponent = null;
		$productType = null;

		if (isset($pItem['itemproducttype'])) {
			$productType = $pItem['itemproducttype'];
		}

		switch ($productType)
		{
			case TPX_PRODUCT_TYPE_PHOTO_PRINTS:
			{
				// Photoprints session has a different structure.
				foreach ($pItem['pictures']['key'] as $printIndex => $printKey)
				{
					$priceKey = $printKey . '*' . $printIndex;

					if ($pItem['pictures']['data'][$printKey]['code'] == $pComponentName)
					{
						$returnComponent = $pItem['pictures']['data'][$printKey];
						$returnComponent['pricedata'] = $pItem['pictures']['printdata'][$priceKey];

						// Photo print uses the component, break from loop.
						break;
					}
				}

				break;
			}

			default:
			{
				// Photobooks, etc.
				$component = self::getSection($pItem, $pComponentName);

				if ($component == null)
				{
					$component = self::getCheckBox($pItem, $pComponentName);

					if ($component != null)
					{
						if (!self::isCheckboxChecked($component))
						{
							$component = null;
						}
					}
				}

				if ($component != null)
				{
					$returnComponent = $component;
				}

				break;
			}
		}

		return $returnComponent;
	}

	public static function getSubComponent($pItem, $pComponentName)
	{
		$returnComponent = null;
		switch ($pItem['itemproducttype'])
		{
			case TPX_PRODUCT_TYPE_PHOTO_PRINTS:
			{
				// Photoprints session has a different structure.
				foreach ($pItem['pictures']['key'] as $printIndex => $printKey)
				{
					$priceKey = $printKey . '*' . $printIndex;

					if ($pItem['pictures']['data'][$printKey]['code'] == $pComponentName)
					{
						$returnComponent = $pItem['pictures']['data'][$printKey];
						$returnComponent['pricedata'] = $pItem['pictures']['printdata'][$priceKey];

						// Photo print uses the component, break from loop.
						break;
					}
				}

				break;
			}

			default:
			{
				// Photobooks, etc.

				// check section sub components
				foreach ($pItem['sections'] as $section)
				{
					if (($returnComponent = self::getComponent($section, $pComponentName)) != null)
					{
						break;
					}
				}

				// if not found in sections check checkbox sub components
				if ($returnComponent == null)
				{
					foreach ($pItem['checkboxes'] as $checkbox)
					{
						if (($returnComponent = self::getComponent($checkbox, $pComponentName)) != null)
						{
							break;
						}
					}
				}

				break;
			}
		}

		return $returnComponent;
	}

	public static function getMetaDataWithValue($pComponent, $pMetaDataName, $pValue)
	{
		$metadata = null;

		if (($metatdata = self::getMetaData($pComponent, $pMetaDataName))!=null)
		{
			if (self::getMetaDataValue($metatdata)!=$pValue)
			{
				$metatdata = null;
			}
		}

		return $metatdata;
	}

	public static function getMetaData($pComponent, $pMetaDataName = '')
	{
		$returnValue = null;

		if ($pMetaDataName != '')
		{
			foreach ($pComponent['metadata'] as $metadata)
			{
				if ($metadata['code'] == $pMetaDataName)
				{
					$returnValue = $metadata;
				}
			}
		}
		else
		{
			$returnValue = $pComponent['metadata'];
		}

		return $returnValue;
	}

	public static function getMetaDataValue($pMetaData)
	{
		return $pMetaData['defaultvalue'];
	}

	public static function getPageCount($pItem)
	{
		return $pItem['itempagecount'];
	}

	public static function getComponentQty($pComponent)
	{
		if (isset($pComponent['quantity']))
		{
			$lookAt = 'quantity';
		}
		else
		{
			$lookAt = 'qty';
		}

		return $pComponent[$lookAt];
	}

	public static function calcProductPrice($pProductCode, $pQty, $pCurrentLine)
	{
		global $gSession;

		$itemProductTotalSell = 0.00;
		$itemProductTotalSellNoTax = 0.00;
		$itemProductTotalTax = 0.00;
		$itemProductTotalSellWithTax = 0.00;

		$productPriceArray = DatabaseObj::getProductPrice(	$pProductCode,
															$gSession['licensekeydata']['groupcode'],
															$gSession['userdata']['companycode'],
															$gSession['order']['currencyexchangerate'],
															$gSession['order']['currencydecimalplaces'],
															$pQty);

		if ($productPriceArray['result']=='')
		{

			$itemProductTotalSell = $productPriceArray['totalsell'];


            if ($productPriceArray['taxcode']!='')
			{
				// tax is included in the price so determine the price without tax
				$itemProductTotalSellNoTax = UtilsObj::bround(($itemProductTotalSell / ($productPriceArray['taxrate'] + 100)) * 100, $gSession['order']['currencydecimalplaces']);
				$itemProductTotalTax = $itemProductTotalSell - $itemProductTotalSellNoTax;

				if ($productPriceArray['taxrate'] != $gSession['items'][$pCurrentLine]['itemtaxrate'])
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


            // if no tax is included in the price or if the tax included in the price is different to the line tax then we need to calculate the values with tax now
        	if (($productPriceArray['taxcode'] == '') || ($productPriceArray['taxrate'] != $gSession['items'][$pCurrentLine]['itemtaxrate']))
        	{
        		$itemProductTotalTax = UtilsObj::bround($itemProductTotalSell * $gSession['items'][$pCurrentLine]['itemtaxrate'] / 100, $gSession['order']['currencydecimalplaces']);
            	$itemProductTotalSellWithTax = $itemProductTotalSell + $itemProductTotalTax;
        	}


			// determine which values we are going to total based on if prices are being displayed with tax
            if ($gSession['order']['showpriceswithtax'] == 1)
            {
            	$itemProductTotalSell = $itemProductTotalSellWithTax;
            }
            else
            {
            	$itemProductTotalSell = $itemProductTotalSellNoTax;
            }

		}

		return $itemProductTotalSell;

	}

	public static function changeComponentCode($pComponent, $pNewCode)
	{
		$pComponent['code'] = $pNewCode;
		$pComponent['defaultcode'] = $pNewCode;

		return $pComponent;
	}

	/**
	 * @param string $companyCode
	 * @param string $groupCode
	 * @param CurrencyInterface $currency
	 * @param mixed[] $currentLine
	 * @param mixed[] $component
	 * @param string $componentCode
	 * @param string $componentPath
	 * @return PriceBreakSetInterface
	 * @throws UnsupportedPricingModelException
	 */
	private static function createComponentPriceBreakSet($companyCode, $groupCode, CurrencyInterface $currency, array &$currentLine, array &$component, $componentCode, $componentPath)
	{
		$priceData = DatabaseObj::getPriceRow(
			$componentPath,
			$componentCode,
			$currentLine['itemproductcode'],
			$groupCode,
			$companyCode,
			false,
			$currentLine['itemqty'],
			-1,
			0,
			'',
			$currentLine['itempagecount']
		);

		if ('' === $priceData['result']) {
			$component['itemqtydropdown'] = $priceData['itemqtydropdown'];
		} else {
			$component['itemqtydropdown'] = [];
		}

		return PriceBreakSetFactory::factory([
			'pricingmodel' => $priceData['pricingmodel'],
			'pricedata' => $priceData['price'],
			'quantityisdropdown' => $priceData['quantityisdropdown'],
			'taxcode' => $priceData['taxcode'],
			'taxrate' => $priceData['taxrate'],
			'unitweight' => $component['unitweight'],
			'unitcost' => $component['unitcost'],
		], $currency);
	}

	public static function calcComponentPrice($pItem, $pComponent, $pItemQty, $pComponentQty, $pPageCount, $pCurrentLine)
	{
		global $gSession;

		if (!$gSession['order']['uselegacypricingsystem']) {
			/** @var OrderInterface $gOrder */
			global $gOrder;
			global $gSession;
			$price = '0';

			if (null !== $gOrder && !empty($pComponent)) {
				$orderLine = $gOrder->getOrderLine($pCurrentLine);
				if ((null === $orderLine) || (0 === $pPageCount)) {
					return '0';
				}

				$componentCode = $pComponent['code'];
				$component = ComponentRegistry::getInstance($componentCode);

				if (!isset($component)) {
					$component = new SectionComponent($componentCode);
					ComponentRegistry::setInstance($componentCode, $component);
				}

				$companyCode = $gSession['userdata']['companycode'];
				$groupCode = $gSession['licensekeydata']['groupcode'];
				$currency = $gOrder->getCurrency();

				$priceBreakSet = self::createComponentPriceBreakSet($companyCode, $groupCode, $currency, $pItem,
					$pComponent, $componentCode, $pComponent['path']);
				$mapping = new ComponentProductMapping($component, $priceBreakSet);

				$itemPageCount = self::getPageCount($pItem);
				if ($pPageCount > $itemPageCount) {
					$pPageCount = $itemPageCount;
				}

				$price = $mapping->calculatePrice($orderLine->getTaxRate(), $gOrder->isShowPricesWithTax(),
					$pItemQty, $pPageCount, $pComponentQty);
			}

			return $price ? $price->getFullSell($gOrder->isShowPricesWithTax()) : '0';
		} else {
			global $gSession;

			$price = 0.00;

			$itemPageCount = self::getPageCount($pItem);

			if ($pPageCount>$itemPageCount)
			{
				$pPageCount = $itemPageCount;
			}

			Order_model::updateSectionTotal(	$pComponent,
				$pItem['itemproductcode'],
				$gSession['order']['currencyexchangerate'],
				$gSession['order']['currencydecimalplaces'],
				$pItemQty,
				$pPageCount,
				$pComponentQty,
				$pCurrentLine);

			$price = $pComponent['totalsell'];

			return $price;
		}
	}


	/**
	 * getPhotoPrintComponent
	 *	Search the list of items and return an array of those with matching component and sub component names
	 *
	 * @param array $pItem - the line item
	 * @param string $pComponentName - the component code to match, can be an empty string
	 * @param string $pSubComponent - the sub component code to match, can be an empty string
	 *
	 * @return array - matching elements of the line item
	 */
	public static function getPhotoPrintComponent($pItem, $pComponentName, $pSubComponent)
	{
		$returnComponent = array();

		// Photoprints session has a different structure.
		if ($pItem['itemproducttype'] == TPX_PRODUCT_TYPE_PHOTO_PRINTS)
		{
			// Loop arround the key array to search for matching components.
			foreach ($pItem['pictures']['key'] as $printIndex => $printKey)
			{
				// Generate the key for the prints price.
				$priceKey = $printKey . '*' . $printIndex;

				// Check if the component code has been supplied.
				if ($pComponentName != '')
				{
					// Check if the component code matches the prints component.
					if ($pItem['pictures']['data'][$printKey]['code'] == $pComponentName)
					{
						// Has the sub component code be supplied.
						if ($pSubComponent != '')
						{
							// Check if the sub component code match the print.
							if ($pItem['pictures']['data'][$printKey]['subcode'] == $pSubComponent)
							{
								// Add the print data to the return array.
								$thePrintData = $pItem['pictures']['data'][$printKey];
								$thePrintData['pricedata'] = $pItem['pictures']['printdata'][$priceKey];

								$returnComponent[] = $thePrintData;
							}
						}
						else
						{
							// Add the print data to the return array.
							$thePrintData = $pItem['pictures']['data'][$printKey];
							$thePrintData['pricedata'] = $pItem['pictures']['printdata'][$priceKey];

							$returnComponent[] = $thePrintData;
						}
					}
				}
				else
				{
					// No component code supplied, check for sub component code.
					if ($pSubComponent != '')
					{
						// Check the sub component code matches that of the print data.
						if ($pItem['pictures']['data'][$printKey]['subcode'] == $pSubComponent)
						{
							// Add the print data to the return array.
							$thePrintData = $pItem['pictures']['data'][$printKey];
							$thePrintData['pricedata'] = $pItem['pictures']['printdata'][$priceKey];

							$returnComponent[] = $thePrintData;
						}
					}
				}
			}
		}

		return $returnComponent;
	}

	/**
	 * calcPhotoPrintComponentQty
	 *	Calculate the total number of prints matching the criteria supplied to getPhotoPrintComponent function
	 *
	 * @param array $pItemsArray - results of getPhotoPrintComponent function
	 *
	 * @return integer
	 */
	public static function calcPhotoPrintComponentQty($pItemsArray)
	{
		$totalQty = 0;

		// Loop around each of the matching print items and calculate a total quantity.
		foreach ($pItemsArray as $thePrint)
		{
			$totalQty += $thePrint['qty'];
		}

		return $totalQty;
	}


	/**
	 * calcPhotoPrintComponentPrice
	 *	Calculate the total value of prints matching the criteria supplied to getPhotoPrintComponent function
	 *
	 * @param array $pItemsArray - results of getPhotoPrintComponent function
	 *
	 * @return float
	 */
	public static function calcPhotoPrintComponentPrice($pItemsArray)
	{
		$totalValue = 0.00;

		// Loop around each of the matching print items and calculate a total value.
		foreach ($pItemsArray as $thePrint)
		{
			$totalValue += ($thePrint['pricedata']['ts'] + $thePrint['pricedata']['subts']);
		}

		return $totalValue;
	}

	/**
	 * Return the active user ID
	 *
	 * @return int
	 */
	public static function getUserID()
	{
		global $gSession;
		
		return $gSession['userid'];
	}

	/**
	 * Returns the item price with tax if tax included in the cart.
	 *
	 * @param array $pLineItem Cart item data.
	 *
	 * @return int
	 */
	public static function getLineItemPrice($pLineItem)
	{
		global $gSession;

		$valueApply = 0;

		if ($gSession['order']['showpriceswithtax'] == 1)
		{
			$valueApply = $pLineItem['itemproducttotalsellwithtax'];
		}
		else
		{
			$valueApply = $pLineItem['itemproducttotalsellnotax'];
		}

		return $valueApply;
	}

	/**
	 * Return the active browser language code.
	 *
	 * @return string
	 */
	public static function getBrowserLanguagecode()
	{
		global $gSession;
		
		return $gSession['browserlanguagecode'];
	}
}

abstract class _Voucher extends SessionHelper {}
