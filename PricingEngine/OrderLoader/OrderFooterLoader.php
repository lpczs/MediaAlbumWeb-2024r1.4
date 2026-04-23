<?php

namespace PricingEngine\OrderLoader;

use DatabaseObj;
use PricingEngine\Component\CheckboxComponent;
use PricingEngine\Component\ComponentProductMapping;
use PricingEngine\Component\ComponentRegistry;
use PricingEngine\Component\SectionComponent;
use PricingEngine\CurrencyInterface;
use PricingEngine\Order\FooterCheckboxComponentAssociation;
use PricingEngine\Order\FooterSectionComponentAssociation;
use PricingEngine\Order;
use PricingEngine\PriceBreakSet\Exception\UnsupportedPricingModelException;
use PricingEngine\PriceBreakSet\PriceBreakSetInterface;
use PricingEngine\PriceBreakSet\PriceBreakSetFactory;
use PricingEngine\Tax\TaxRate;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Order footer loader
 *
 * Handles loading the order footer from session
 * in to the order objects for price calculation.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class OrderFooterLoader
{
	/**
	 * @param Order $order
	 * @param mixed[] $session
	 * @throws UnsupportedPricingModelException
	 */
	public static function loadOrderFooterComponents(Order $order, &$session)
	{
		$currency = $order->getCurrency();
		$companyCode = $session['userdata']['companycode'];
		$groupCode = $session['licensekeydata']['groupcode'];

		// Footer checkbox components
		/** @var FooterCheckboxComponentAssociation[] $checkboxAssociations */
		$checkboxAssociations = [];
		foreach ($session['order']['orderFooterCheckboxes'] as &$checkbox) {
			$componentCode = $checkbox['code'];
			$componentPath = $checkbox['path'];
			$productCode = $checkbox['productcode'];
			$quantity = $checkbox['itemqty'];
			$pageCount = $checkbox['itempagecount'];
			$weight = $checkbox['unitweight'];
			$cost = $checkbox['unitcost'];
			$taxRate = self::createTaxRate($session, $checkbox);

			$component = ComponentRegistry::getInstance($componentCode);
			if (!isset($component)) {
				$component = new CheckboxComponent($componentCode);
				ComponentRegistry::setInstance($componentCode, $component);
			}

			$priceBreakSet = self::createComponentPriceBreakSet($companyCode, $groupCode, $currency, $checkbox,
				$componentCode, $componentPath, $productCode, $quantity, $pageCount, $weight, $cost);
			$mapping = new ComponentProductMapping($component, $priceBreakSet);

			$checkboxAssociation = new FooterCheckboxComponentAssociation($mapping, $taxRate, $checkbox);
			$checkboxAssociation->setOrder($order);
			$checkboxAssociations[] = $checkboxAssociation;
		}
		$order->setFooterCheckboxComponentAssociations($checkboxAssociations);

		// Section components
		/** @var FooterSectionComponentAssociation[] $sectionAssociations */
		$sectionAssociations = [];
		foreach ($session['order']['orderFooterSections'] as &$section) {
			$componentCode = $section['code'];
			$componentPath = $section['path'];
			$productCode = $section['itemproductcode'];
			$quantity = $section['itemqty'];
			$pageCount = $section['itempagecount'];
			$weight = $section['unitweight'];
			$cost = $section['unitcost'];
			$taxRate = self::createTaxRate($session, $section);

			$component = ComponentRegistry::getInstance($componentCode);
			if (!isset($component)) {
				$component = new SectionComponent($componentCode);
				ComponentRegistry::setInstance($componentCode, $component);
			}

			$priceBreakSet = self::createComponentPriceBreakSet($companyCode, $groupCode, $currency, $section,
				$componentCode, $componentPath, $productCode, $quantity, $pageCount, $weight, $cost);
			$mapping = new ComponentProductMapping($component, $priceBreakSet);

			$association = new FooterSectionComponentAssociation($mapping, $taxRate, $section);
			$association->setOrder($order);
			$sectionAssociations[] = $association;

			// Section sub-components
			/** @var FooterSectionComponentAssociation[] $subSections */
			$subSections = [];
			foreach ($section['subsections'] as &$subSection) {
				$subComponentCode = $subSection['code'];
				$subComponentPath = $subSection['path'];
				$quantity = $subSection['itemqty'];
				$weight = $subSection['unitweight'];
				$cost = $subSection['unitcost'];
				$taxRate = self::createTaxRate($session, $subSection);

				$component = ComponentRegistry::getInstance($subComponentCode);
				if (!isset($component)) {
					$component = new SectionComponent($subComponentCode);
					ComponentRegistry::setInstance($subComponentCode, $component);
				}

				$priceBreakSet = self::createComponentPriceBreakSet($subComponentCode, $groupCode, $currency, $subSection,
					$subComponentCode, $subComponentPath, $productCode, $quantity, $pageCount, $weight, $cost);
				$mapping = new ComponentProductMapping($component, $priceBreakSet);

				$subSectionAssociation = new FooterSectionComponentAssociation($mapping, $taxRate, $subSection);
				$subSectionAssociation->setOrder($order);
				$subSectionAssociation->setParentAssociation($association);
				$subSections[] = $subSectionAssociation;
			}
			$association->setSubSections($subSections);

			// Checkbox sub-components
			/** @var FooterCheckboxComponentAssociation[] $subCheckboxes */
			$subCheckboxes = [];
			foreach ($section['checkboxes'] as &$subCheckbox) {
				$subComponentCode = $subCheckbox['code'];
				$subComponentPath = $subCheckbox['path'];
				$productCode = $subCheckbox['productcode'];
				$quantity = $subCheckbox['itemqty'];
				$weight = $subCheckbox['unitweight'];
				$cost = $subCheckbox['unitcost'];
				$taxRate = self::createTaxRate($session, $subCheckbox);

				$component = ComponentRegistry::getInstance($subComponentCode);
				if (!isset($component)) {
					$component = new CheckboxComponent($subComponentCode);
					ComponentRegistry::setInstance($subComponentCode, $component);
				}

				$priceBreakSet = self::createComponentPriceBreakSet($subComponentCode, $groupCode, $currency, $subCheckbox,
					$subComponentCode, $subComponentPath, $productCode, $quantity, $pageCount, $weight, $cost);
				$mapping = new ComponentProductMapping($component, $priceBreakSet);

				$subCheckboxAssociation = new FooterCheckboxComponentAssociation($mapping, $taxRate, $subCheckbox);
				$subCheckboxAssociation->setOrder($order);
				$subCheckboxAssociation->setParentAssociation($association);
				$subCheckboxes[] = $subCheckboxAssociation;
			}
			$association->setSubCheckboxes($subCheckboxes);
		}
		$order->setFooterSectionComponentAssociations($sectionAssociations);
	}

	/**
	 * @param string $companyCode
	 * @param string $groupCode
	 * @param CurrencyInterface $currency
	 * @param mixed[] $component
	 * @param string $componentCode
	 * @param string $componentPath
	 * @param string $productCode
	 * @param int $quantity
	 * @param int $pageCount
	 * @param string $weight
	 * @param string $cost
	 * @return PriceBreakSetInterface
	 * @throws UnsupportedPricingModelException
	 */
	public static function createComponentPriceBreakSet($companyCode, $groupCode, CurrencyInterface $currency,
		array &$component, $componentCode, $componentPath, $productCode, $quantity, $pageCount, $weight, $cost)
	{
		$priceData = DatabaseObj::getPriceRow(
			$componentPath,
			$componentCode,
			$productCode,
			$groupCode,
			$companyCode,
			false,
			$quantity,
			-1,
			0,
			'',
			$pageCount
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
			'unitweight' => $weight,
			'unitcost' => $cost,
			'inheritparentqty' => $priceData['inheritparentqty'],
		], $currency);
	}

	/**
	 * @param mixed[] $session
	 * @param mixed[] $component
	 * @return TaxRateInterface
	 */
	private static function createTaxRate(&$session, &$component)
	{
		if ($session['order']['orderalltaxratesequal'] == 1) {
			$taxRateData = DatabaseObj::getTaxRate($session['order']['fixedtaxrate']);
		} else {
			$taxRateData = self::getTaxRateArrayFromTaxLevel($session, $component['orderfootertaxlevel']);
		}

		return new TaxRate($taxRateData['code'], $taxRateData['rate'], $taxRateData['name']);
	}

	/**
	 * @param mixed[] $session
	 * @param int $taxLevel
	 * @return array|null
	 */
	private static function getTaxRateArrayFromTaxLevel(&$session, $taxLevel)
	{
		switch ($taxLevel) {
			case 1: return $session['order']['producttaxlevel1'];
			case 2: return $session['order']['producttaxlevel2'];
			case 3: return $session['order']['producttaxlevel3'];
			case 4: return $session['order']['producttaxlevel4'];
			case 5: return $session['order']['producttaxlevel5'];
		}

		return null;
	}
}
