<?php

namespace PricingEngine\OrderLoader;

use DatabaseObj;
use PricingEngine\Component\CheckboxComponent;
use PricingEngine\Component\ComponentProductMapping;
use PricingEngine\Component\ComponentRegistry;
use PricingEngine\Component\SectionComponent;
use PricingEngine\CurrencyInterface;
use PricingEngine\Enum\ProductOption;
use PricingEngine\OrderLine\CheckboxComponentAssociation;
use PricingEngine\OrderLine\ExternalAssetCollection;
use PricingEngine\OrderLine\CalendarCustomisationComponent;
use PricingEngine\OrderLine\AIComponent;
use PricingEngine\OrderLine\PerComponentPhotoPrintsComponent;
use PricingEngine\OrderLine\PerPicturePhotoPrintsComponent;
use PricingEngine\Order;
use PricingEngine\OrderLine;
use PricingEngine\OrderLine\SectionComponentAssociation;
use PricingEngine\PriceBreakSet\Exception\UnsupportedPricingModelException;
use PricingEngine\PriceBreakSet\PriceBreakSetInterface;
use PricingEngine\PriceBreakSet\PriceBreakSetFactory;
use PricingEngine\Tax\TaxRate;
use PricingEngine\Tax\TaxRateInterface;
use UtilsObj;

/**
 * Order line loader
 *
 * Handles loading the order line from session
 * in to the order objects for price calculation.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class OrderLineLoader
{
	/**
	 * @param Order $order
	 * @param mixed[] $session
	 * @throws UnsupportedPricingModelException
	 */
	public static function loadOrderLines(Order $order, &$session)
	{
		$currency = $order->getCurrency();
		$companyCode = $session['userdata']['companycode'];
		$groupCode = $session['licensekeydata']['groupcode'];

		foreach ($session['items'] as $currentLineNumber => &$currentLine) {
			$productCode = $currentLine['itemproductcode'];

			// Order line and initial product
			$priceBreakSet = self::createProductPriceBreakSet($companyCode, $groupCode, $currency, $currentLine);
			$lineTaxRate = new TaxRate($currentLine['itemtaxcode'], $currentLine['itemtaxrate'], $currentLine['itemtaxname']);
			$orderLine = new OrderLine($currentLine['itemprojectname'], $priceBreakSet, $lineTaxRate, $session, $currentLine, $currentLineNumber);

			// Picture components
			$pictureComponents = self::loadPictureComponent($companyCode, $groupCode, $currency, $orderLine, $currentLine, $currentLine['pictures']);
			$orderLine->setPictureComponents($pictureComponents);

			// External assets, processed only if pictures haven't been declared
			if (empty($pictureComponentSet) && is_array($currentLine['itemexternalassets']) && !empty($currentLine['itemexternalassets'])) {
				$orderLine->setExternalAssetCollection(new ExternalAssetCollection($orderLine, $currentLine['itemexternalassets']));
			}

			// Calendar customisations
			$calendarComponents = self::loadCalendarCustomisationComponent($companyCode, $groupCode, $currency, $orderLine, $currentLine, $currentLine['calendarcustomisations']);
			$orderLine->setCalendarComponents($calendarComponents);

			// AI Component
			$AIComponent = self::loadAIComponent($companyCode, $groupCode, $currency, $orderLine, $currentLine, $currentLine['aicomponent']);
			$orderLine->setAIComponent($AIComponent);

			// Checkbox components
			$checkboxAssociations = self::loadCheckboxComponent($companyCode, $groupCode, $currency, $productCode, $lineTaxRate, $currentLine, $currentLine['checkboxes']);
			$orderLine->setCheckboxComponentAssociations($checkboxAssociations);

			// Section components & all sub-sections and sub-checkboxes
			$sectionAssociations = self::loadSectionComponent($companyCode, $groupCode, $currency, $productCode, $lineTaxRate, $currentLine, $currentLine['sections']);
			$orderLine->setSectionComponentAssociations($sectionAssociations);

			// Footer checkbox components
			$checkboxAssociations = self::loadCheckboxComponent($companyCode, $groupCode, $currency, $productCode, $lineTaxRate, $currentLine, $currentLine['lineFooterCheckboxes']);
			$orderLine->setFooterCheckboxComponentAssociations($checkboxAssociations);

			// Section components & all sub-sections and sub-checkboxes
			$sectionAssociations = self::loadSectionComponent($companyCode, $groupCode, $currency, $productCode, $lineTaxRate, $currentLine, $currentLine['lineFooterSections']);
			$orderLine->setFooterSectionComponentAssociations($sectionAssociations);

			$order->addOrderLine($orderLine);
		}
	}

	/**
	 * Load all pictures
	 *
	 * Load all the pictures in the session in to the order line,
	 * accounting for product option pricing differences.
	 *
	 * @param string $companyCode
	 * @param string $groupCode
	 * @param CurrencyInterface $currency
	 * @param OrderLine $orderLine
	 * @param mixed[] $currentLine
	 * @param mixed[] $pictures
	 * @return PerPicturePhotoPrintsComponent[]
	 * @throws UnsupportedPricingModelException
	 */
	private static function loadPictureComponent($companyCode, $groupCode, CurrencyInterface $currency, OrderLine $orderLine, &$currentLine, &$pictures)
	{
		$pictureComponentSet = [];
		foreach ($pictures['key'] as $componentKey) {
			$componentData = &$pictures['data'][$componentKey];

			$componentCode = $componentData['code'];
			$subComponentCode = !empty($componentData['subcode']) ? $componentData['subcode'] : null;

			// Create a picture component for each component/subcomponent combination found
			$setCode = $componentCode;
			if (null !== $subComponentCode) {
				$setCode .= '.' . $subComponentCode;
			}

			if (!isset($pictureComponentSet[$setCode])) {
				// Create the price break set
				$componentPriceBreakSet = self::createPicturePriceBreakSet($companyCode, $groupCode, $currency,
					$currentLine, $componentCode);

				$subComponentPriceBreakSet = null;
				if (null !== $subComponentCode) {
					$subComponentPriceBreakSet = self::createPicturePriceBreakSet($companyCode, $groupCode, $currency,
						$currentLine, $componentCode, $subComponentCode);
				}

				// Create the picture component
				if (ProductOption::PER_COMPONENT_SUB_COMPONENT === $currentLine['productoptions']) {
					$pictureComponentSet[$setCode] = new PerComponentPhotoPrintsComponent(
						$orderLine,
						$componentCode,
						$subComponentCode,
						$componentPriceBreakSet,
						$subComponentPriceBreakSet,
						$currentLine['pictures']
					);
				} else {
					$pictureComponentSet[$setCode] = new PerPicturePhotoPrintsComponent(
						$orderLine,
						$componentCode,
						$subComponentCode,
						$componentPriceBreakSet,
						$subComponentPriceBreakSet,
						$currentLine['pictures']
					);
				}
			}
		}

		return array_values($pictureComponentSet);
	}

	/**
	 * Load all calendar customisations
	 *
	 * Load all the calendar customisations in the session in to
	 * the order line.
	 *
	 * @param string $companyCode
	 * @param string $groupCode
	 * @param CurrencyInterface $currency
	 * @param OrderLine $orderLine
	 * @param mixed[] $currentLine
	 * @param mixed[] $calendarCustomisations
	 * @return CalendarCustomisationComponent[]
	 * @throws UnsupportedPricingModelException
	 */
	private static function loadCalendarCustomisationComponent($companyCode, $groupCode, CurrencyInterface $currency, OrderLine $orderLine, &$currentLine, &$calendarCustomisations)
	{
		$calendarComponentSet = [];

		foreach ($calendarCustomisations as &$calendarCustomisation) {
			// Create a calendar component for each unique component found
			$setCode = $calendarCustomisation['componentcode'];
			$componentQuantity = $calendarCustomisation['componentqty'];
			$customisationUsed = $calendarCustomisation['used'];

			if (!isset($calendarComponentSet[$setCode]) && $componentQuantity > 0 && $customisationUsed) {
				// Create the component
				$componentCode = $calendarCustomisation['componentcode'];

				$priceBreakSet = self::createCalendarPriceBreakSet($companyCode, $groupCode, $currency, $currentLine, $componentCode);
				$calendarComponentSet[$setCode] = new CalendarCustomisationComponent($orderLine, $componentCode,
					$priceBreakSet, $currentLine['calendarcustomisations']);
			}
		}

		return array_values($calendarComponentSet);
	}

	/**
	 * Load the AI Component
	 *
	 * Load the AI Component in the session in to
	 * the order line.
	 *
	 * @param string $companyCode
	 * @param string $groupCode
	 * @param CurrencyInterface $currency
	 * @param OrderLine $orderLine
	 * @param mixed[] $currentLine
	 * @param mixed[] $calendarCustomisations
	 * @return AIComponent[]
	 * @throws UnsupportedPricingModelException
	 */
	private static function loadAIComponent($companyCode, $groupCode, CurrencyInterface $currency, OrderLine $orderLine, &$currentLine, &$AIComponent)
	{
		$AIComponentSet = [];
		$previouslyOutOfRange = false;
		$componentCode = '';
		$componentQuantity = 0;
		$componentUsed = false;

		if (!is_null($AIComponent))
		{
			$componentCode = $AIComponent['componentcode'];
			$componentQuantity = $AIComponent['componentqty'];
			$componentUsed = $AIComponent['used'];

			$previouslyOutOfRange = UtilsObj::getArrayParam($AIComponent, 'outofrange' , false);
		}

		if ((($componentQuantity > 0) && ($componentUsed)) || ($previouslyOutOfRange))
		{
			try
			{
			// create the component
			$priceBreakSet = self::createAIPriceBreakSet($companyCode, $groupCode, $currency, $currentLine, $componentCode);
			$AIComponentSet[] = new AIComponent($orderLine, $componentCode, $priceBreakSet, $currentLine['aicomponent']);
			}
			catch (UnsupportedPricingModelException $e)
			{
				//the pricing for this component has been disabled or deleted since the order was placed
				//as the AI component does not represent a physical process or resource disabling means that they no longer wish to charge extra for AI
				//therefore we want to set the component to unused  and allow it to proceed as normal
				$currentLine['aicomponent']['used'] = false;
			}
		}

		return $AIComponentSet;
	}

	/**
	 * Load all section and sub-section/sub-checkbox components and return the list
	 *
	 * Load all the section and sub-section/sub-checkbox components and return the list
	 * for inclusion in the order line
	 *
	 * @param string $companyCode
	 * @param string $groupCode
	 * @param CurrencyInterface $currency
	 * @param string $productCode
	 * @param TaxRateInterface $lineTaxRate
	 * @param mixed[] $currentLine
	 * @param mixed[] $sections
	 * @return SectionComponentAssociation[]
	 * @throws UnsupportedPricingModelException
	 */
	private static function loadSectionComponent($companyCode, $groupCode, CurrencyInterface $currency, $productCode, TaxRateInterface $lineTaxRate, &$currentLine, &$sections)
	{
		$sectionAssociations = [];
		foreach ($sections as &$section) {
			$componentCode = $section['code'];
			$componentPath = $section['path'];

			$component = ComponentRegistry::getInstance($componentCode);
			if (!isset($component)) {
				$component = new SectionComponent($componentCode);
				ComponentRegistry::setInstance($componentCode, $component);
			}

			$priceBreakSet = self::createComponentPriceBreakSet($companyCode, $groupCode, $currency, $currentLine, $section, $componentCode, $componentPath);
			$mapping = new ComponentProductMapping($component, $priceBreakSet);
			$association = new SectionComponentAssociation($mapping, $productCode, $lineTaxRate, $section);
			$sectionAssociations[] = $association;

			// Section sub-components
			/** @var SectionComponentAssociation[] $subSections */
			$subSections = [];
			foreach ($section['subsections'] as &$subSection) {
				$subComponentCode = $subSection['code'];
				$subComponentPath = $subSection['path'];

				$component = ComponentRegistry::getInstance($subComponentCode);
				if (!isset($component)) {
					$component = new SectionComponent($subComponentCode);
					ComponentRegistry::setInstance($subComponentCode, $component);
				}

				$priceBreakSet = self::createComponentPriceBreakSet($companyCode, $groupCode, $currency, $currentLine, $subSection, $subComponentCode, $subComponentPath);
				$mapping = new ComponentProductMapping($component, $priceBreakSet);
				$subAssociation = new SectionComponentAssociation($mapping, $productCode, $lineTaxRate, $subSection);
				$subAssociation->setParentAssociation($association);

				$subSections[] = $subAssociation;
			}
			$association->setSubSectionAssociations($subSections);

			// Checkbox sub-components
			$subCheckboxes = self::loadCheckboxComponent($companyCode, $groupCode, $currency, $productCode, $lineTaxRate, $currentLine, $section['checkboxes'], $association);
			$association->setSubCheckboxAssociations($subCheckboxes);
		}

		return $sectionAssociations;
	}

	/**
	 * Load all checkbox components and return the list
	 *
	 * Load all the checkbox components and return the list for inclusion in
	 * the order line
	 *
	 * @param string $companyCode
	 * @param string $groupCode
	 * @param CurrencyInterface $currency
	 * @param string $productCode
	 * @param TaxRateInterface $lineTaxRate
	 * @param mixed[] $currentLine
	 * @param mixed[] $checkboxes
	 * @param SectionComponentAssociation|null $parentAssociation
	 * @return CheckboxComponentAssociation[]
	 * @throws UnsupportedPricingModelException
	 */
	private static function loadCheckboxComponent($companyCode, $groupCode, CurrencyInterface $currency, $productCode,
		TaxRateInterface $lineTaxRate, &$currentLine, &$checkboxes,
		SectionComponentAssociation $parentAssociation = null)
	{
		$subCheckboxes = [];
		foreach ($checkboxes as &$subCheckbox) {
			$subComponentCode = $subCheckbox['code'];
			$subComponentPath = $subCheckbox['path'];

			$component = ComponentRegistry::getInstance($subComponentCode);
			if (!isset($component)) {
				$component = new CheckboxComponent($subComponentCode);
				ComponentRegistry::setInstance($subComponentCode, $component);
			}

			$priceBreakSet = self::createComponentPriceBreakSet($companyCode, $groupCode, $currency, $currentLine, $subCheckbox, $subComponentCode, $subComponentPath);
			$mapping = new ComponentProductMapping($component, $priceBreakSet);
			$subAssociation = new CheckboxComponentAssociation($mapping, $productCode, $lineTaxRate, $subCheckbox);
			$subAssociation->setParentAssociation($parentAssociation);
			$subCheckboxes[] = $subAssociation;
		}

		return $subCheckboxes;
	}

	/**
	 * @param string $companyCode
	 * @param string $groupCode
	 * @param CurrencyInterface $currency
	 * @param mixed[] $currentLine
	 * @return PriceBreakSetInterface
	 * @throws UnsupportedPricingModelException
	 */
	private static function createProductPriceBreakSet($companyCode, $groupCode, CurrencyInterface $currency, array &$currentLine)
	{
		if ($currentLine['itemqty'] <= 0) {
			$currentLine['itemqty'] = 1;
		}

		$priceData = DatabaseObj::getPriceRow(
			'',
			'',
			$currentLine['itemproductcode'],
			$groupCode,
			$companyCode,
			true,
			$currentLine['itemqty'],
			-1,
			0,
			'',
			$currentLine['itempagecount']
		);

		if ('' === $priceData['result']) {
			$currentLine['itemqtydropdown'] = $priceData['itemqtydropdown'];
		} else {
			$currentLine['itemqtydropdown'] = [];
		}
	
		return PriceBreakSetFactory::factory([
			'pricingmodel' => $priceData['pricingmodel'],
			'pricedata' => $priceData['price'],
			'quantityisdropdown' => $priceData['quantityisdropdown'],
			'taxcode' => $priceData['taxcode'],
			'taxrate' => $priceData['taxrate'],
			'unitweight' => $currentLine['itemproductunitweight'],
			'unitcost' => $currentLine['itemunitcost'],
		], $currency);
	}

	/**
	 * @param string $companyCode
	 * @param string $groupCode
	 * @param CurrencyInterface $currency
	 * @param mixed[] $currentLine
	 * @param string $componentCode
	 * @param string $subComponentCode
	 * @return PriceBreakSetInterface
	 * @throws UnsupportedPricingModelException
	 */
	private static function createPicturePriceBreakSet($companyCode, $groupCode, CurrencyInterface $currency, array &$currentLine, $componentCode, $subComponentCode = null)
	{
		if (null === $subComponentCode) {
			$sectionPath = '$SINGLEPRINT\\';
			$componentCode = 'SINGLEPRINT' . '.' . $componentCode;
		} else {
			$sectionPath = '$SINGLEPRINT\\' . $componentCode . '\\$SINGLEPRINTOPTION\\';
			$componentCode = 'SINGLEPRINTOPTION.' . $subComponentCode;
		}

		$priceData = DatabaseObj::getPriceRow(
			$sectionPath,
			$componentCode,
			$currentLine['componenttreeproductcode'],
			$groupCode,
			$companyCode,
			false,
			$currentLine['itemqty'],
			-1,
			0,
			'',
			$currentLine['itempagecount']
		);

		return PriceBreakSetFactory::factory([
			'pricingmodel' => $priceData['pricingmodel'],
			'pricedata' => $priceData['price'],
			'quantityisdropdown' => $priceData['quantityisdropdown'],
			'taxcode' => $priceData['taxcode'],
			'taxrate' => $priceData['taxrate'],
			'unitweight' => $currentLine['itemproductunitweight'],
			'unitcost' => $currentLine['itemproductunitcost'],
		], $currency);
	}

	/**
	 * @param string $companyCode
	 * @param string $groupCode
	 * @param CurrencyInterface $currency
	 * @param mixed[] $currentLine
	 * @param string $componentCode
	 * @return PriceBreakSetInterface
	 * @throws UnsupportedPricingModelException
	 */
	private static function createCalendarPriceBreakSet($companyCode, $groupCode, CurrencyInterface $currency, array &$currentLine, $componentCode)
	{
		$priceData = DatabaseObj::getPriceRow(
			'$CALENDARCUSTOMISATION\\',
			$componentCode,
			$currentLine['componenttreeproductcode'],
			$groupCode,
			$companyCode,
			false,
			$currentLine['itemqty'],
			-1,
			0,
			'',
			$currentLine['itempagecount']
		);

		return PriceBreakSetFactory::factory([
			'pricingmodel' => $priceData['pricingmodel'],
			'pricedata' => $priceData['price'],
			'quantityisdropdown' => $priceData['quantityisdropdown'],
			'taxcode' => $priceData['taxcode'],
			'taxrate' => $priceData['taxrate'],
			'unitweight' => $currentLine['itemproductunitweight'],
			'unitcost' => $currentLine['itemproductunitcost'],
		], $currency);
	}

	/**
	 * @param string $companyCode
	 * @param string $groupCode
	 * @param CurrencyInterface $currency
	 * @param mixed[] $currentLine
	 * @param string $componentCode
	 * @return PriceBreakSetInterface
	 * @throws UnsupportedPricingModelException
	 */
	private static function createAIPriceBreakSet($companyCode, $groupCode, CurrencyInterface $currency, array &$currentLine, $componentCode)
	{
		$priceData = DatabaseObj::getPriceRow(
			'$TAOPIXAI\\',
			$componentCode,
			$currentLine['componenttreeproductcode'],
			$groupCode,
			$companyCode,
			false,
			$currentLine['itemqty'],
			-1,
			0,
			'',
			$currentLine['itempagecount']
		);

		return PriceBreakSetFactory::factory([
			'pricingmodel' => $priceData['pricingmodel'],
			'pricedata' => $priceData['price'],
			'quantityisdropdown' => $priceData['quantityisdropdown'],
			'taxcode' => $priceData['taxcode'],
			'taxrate' => $priceData['taxrate'],
			'unitweight' => $currentLine['itemproductunitweight'],
			'unitcost' => $currentLine['itemproductunitcost'],
		], $currency);
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
			$currentLine['componenttreeproductcode'],
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
			'inheritparentqty' => $priceData['inheritparentqty'],
		], $currency);
	}
}
