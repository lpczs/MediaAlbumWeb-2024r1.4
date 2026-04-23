<?php

namespace PricingEngine;

use InvalidArgumentException;
use LogicException;
use PricingEngine\OrderLine\CheckboxComponentAssociation;
use PricingEngine\OrderLine\ExternalAssetCollection;
use PricingEngine\OrderLine\CalendarCustomisationComponent;
use PricingEngine\OrderLine\AIComponent;
use PricingEngine\OrderLine\PerComponentPhotoPrintsComponent;
use PricingEngine\OrderLine\PerPicturePhotoPrintsComponent;
use PricingEngine\OrderLine\SectionComponentAssociation;
use PricingEngine\Price\DiscountablePrice;
use PricingEngine\Price\Price;
use PricingEngine\PriceBreakSet\Exception\PriceBreakNotFoundException;
use PricingEngine\PriceBreakSet\PriceBreakSetInterface;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Order line handling
 *
 * Represents an order line attached to an order.
 * An order line is a project being ordered by the customer
 * as well as associated assets/components.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class OrderLine implements OrderLineInterface
{
	/**
	 * @var string
	 */
	private $projectName;

	/**
	 * @var OrderInterface
	 */
	private $order;

	/**
	 * @var DiscountablePrice
	 */
	private $orderLinePrice;

	/**
	 * @var PriceBreakSetInterface
	 */
	private $priceBreakSet;

	/**
	 * @var TaxRateInterface
	 */
	private $lineTaxRate;

	/**
	 * @var PerPicturePhotoPrintsComponent[]|PerComponentPhotoPrintsComponent[]
	 */
	private $pictureComponents = [];

	/**
	 * @var CalendarCustomisationComponent[]
	 */
	private $calendarComponents = [];

	/**
	 * @var AIComponent[]
	 */
	private $AIComponents = [];

	/**
	 * @var ExternalAssetCollection
	 */
	private $externalAssetCollection;

	/**
	 * @var CheckboxComponentAssociation[]
	 */
	private $checkBoxComponentAssociations = [];

	/**
	 * @var SectionComponentAssociation[]
	 */
	private $sectionComponentAssociations = [];

	/**
	 * @var CheckboxComponentAssociation[]
	 */
	private $lineFooterCheckboxComponentAssociations = [];

	/**
	 * @var SectionComponentAssociation[]
	 */
	private $lineFooterSectionComponentAssociations = [];

	/**
	 * @var mixed[]
	 */
	private $session;

	/**
	 * @var mixed[]
	 */
	private $lineSession;

	/**
	 * @var int
	 */
	private $currentLine;

	/**
	 * @var Price
	 */
	private $productPrice;

	/**
	 * @var int
	 */
	private $requestedProductQuantity = null;

	/**
	 * Constructor
	 *
	 * @param string $projectName
	 * @param PriceBreakSetInterface $priceBreakSet
	 * @param TaxRateInterface $lineTaxRate
	 * @param mixed[] $session
	 * @param mixed[] $lineSession
	 * @param int $currentLine
	 */
	public function __construct($projectName, PriceBreakSetInterface $priceBreakSet,
		TaxRateInterface $lineTaxRate, &$session, &$lineSession, $currentLine)
	{
		$this->projectName = $projectName;
		$this->priceBreakSet = $priceBreakSet;
		$this->lineTaxRate = $lineTaxRate;
		$this->session = &$session;
		$this->lineSession = &$lineSession;
		$this->currentLine = $currentLine;

		$this->setVoucherName('');
	}

	/**
	 * Set order
	 *
	 * Set the associated order of the order line.
	 * This should be called by the order when adding
	 * the order line to the order itself.
	 *
	 * @param OrderInterface $order
	 * @return $this
	 */
	public function setOrder(OrderInterface $order)
	{
		$this->order = $order;
		return $this;
	}

	/**
	 * Get the project name
	 *
	 * Get the project name of the order line.
	 *
	 * @return string
	 */
	public function getProjectName()
	{
		return $this->projectName;
	}

	/**
	 * Get line number
	 *
	 * Get the line number of the order line.
	 *
	 * @return int
	 */
	public function getLineNumber()
	{
		return $this->currentLine;
	}

	/**
	 * Check if show prices with tax is enabled
	 *
	 * Check if the license key option, "show prices with tax"
	 * is enabled. The option affects the way the order price
	 * and the amounts discounted from the order are applied.
	 *
	 * @return bool
	 */
	public function isShowPricesWithTax()
	{
		return $this->order->isShowPricesWithTax();
	}

	/**
	 * Check if the order is a reorder
	 *
	 * Check if the order is a reorder of an existing order.
	 *
	 * @return bool
	 */
	public function isReorder()
	{
		return $this->order->isReorder();
	}

	/**
	 * Get the associated product option for the project
	 *
	 * Each project is for a specific product, which
	 * can have a production option added to change
	 * product behaviour. Returns the product option
	 * flag for interpretation.
	 *
	 * @return int
	 */
	public function getProductOption()
	{
		return $this->lineSession['productoptions'];
	}

	/**
	 * Get the associated product price transformation stage for the project
	 *
	 * Each project is for a specific product, which
	 * can have a pre or post price transformation stage
	 * set to change pricing behaviour. Returns the
	 * price transformation stage flag for interpretation.
	 *
	 * @return int
	 */
	public function getPriceTransformationStage()
	{
		return $this->lineSession['pricetransformationstage'];
	}

	/**
	 * Get the currency decimal places
	 *
	 * Get the currency rounding decimal places to be used
	 * for the order when rounding all monetary values.
	 *
	 * @return int
	 */
	public function getPlaces()
	{
		return $this->order->getPlaces();
	}

	/**
	 * Get currency
	 *
	 * Get the currency used by the order
	 *
	 * @return CurrencyInterface
	 */
	public function getCurrency()
	{
		return $this->order->getCurrency();
	}

	/**
	 * Get tax rate
	 *
	 * Get the tax rate of the order line. This is
	 * the tax rate that will be used to calculate
	 * tax for all assets/components that are
	 * associated with the order line that make up
	 * its price.
	 *
	 * @return TaxRateInterface
	 */
	public function getTaxRate()
	{
		return $this->lineTaxRate;
	}

	/**
	 * Get company code
	 *
	 * Get the associated company code of the license key
	 * used in the order.
	 *
	 * @return string
	 */
	public function getCompanyCode()
	{
		return $this->order->getCompanyCode();
	}

	/**
	 * Get license key code
	 *
	 * Get the associated license key code of the license key
	 * used in the order.
	 *
	 * @return string
	 */
	public function getLicenseKeyCode()
	{
		return $this->order->getLicenseKeyCode();
	}

	/**
	 * Get product code
	 *
	 * Get the associated product code of the project
	 * that this order line is for.
	 *
	 * @return string
	 */
	public function getProductCode()
	{
		return $this->lineSession['itemproductcode'];
	}

	/**
	 * Set the quantity of units that make up the order line
	 *
	 * @param int $quantity
	 * @return $this
	 */
	public function setProductQuantity($quantity)
	{
		$this->lineSession['itemqty'] = $quantity;
		return $this;
	}

	/**
	 * Get the quantity of units that make up the order line
	 *
	 * Get the number of units that make up the price.
	 * The quantity of units will be used to make up
	 * the unit price.
	 *
	 * @return int
	 */
	public function getProductQuantity()
	{
		return @$this->lineSession['itemqty'];
	}

	/**
	 * Get the original quantity of units that make up the order,
	 * before it may have updated by price break business rules.
	 *
	 * If the calculate price has not yet been called, then this value
	 * will match the current session item quantity.
	 *
	 * @return int
	 */
	public function getRequestedProductQuantity()
	{
		if (null === $this->requestedProductQuantity) {
			return @$this->lineSession['itemqty'];
		}

		return $this->requestedProductQuantity;
	}

	/**
	 * Set the page count for each unit
	 *
	 * Set the number of pages that is unit is made up of.
	 *
	 * @param $pageCount
	 * @return $this
	 */
	public function setPageCount($pageCount)
	{
		$this->lineSession['itempagecount'] = $pageCount;
		return $this;
	}

	/**
	 * Get the page count for each unit
	 *
	 * Get the number of pages that is unit is made up of.
	 *
	 * @return string
	 */
	public function getPageCount()
	{
		return @$this->lineSession['itempagecount'];
	}

	/**
	 * Set picture components
	 *
	 * Set the picture components that make up the order line.
	 * Overwrites any existing picture components that may
	 * have been set.
	 *
	 * @param PerPicturePhotoPrintsComponent[] $pictureComponents
	 * @return $this
	 */
	public function setPictureComponents(array $pictureComponents)
	{
		$this->pictureComponents = $pictureComponents;
		return $this;
	}

	/**
	 * Set calendar components
	 *
	 * Set the calendar components that make up the order line.
	 *
	 * @param CalendarCustomisationComponent[] $calendarComponents
	 * @return $this
	 */
	public function setCalendarComponents(array $calendarComponents)
	{
		$this->calendarComponents = $calendarComponents;
		return $this;
	}

	/**
	 * Set AI Component
	 * 
	 * Set the AI Components that set up the order line
	 * 
	 * @param pAIComponent[] $AIComponent
	 * @return this
	 */
	public function setAIComponent(array $pAIComponents)
	{
		$this->AIComponents = $pAIComponents;
		return $this;
	}

	/**
	 * Get a section or checkbox component
	 *
	 * Get a component for the component code given. Returns
	 * null if the component is not found.
	 *
	 * @param string $componentCode
	 * @return CheckboxComponentAssociation|SectionComponentAssociation|null
	 */
	public function getComponentAssociation($componentCode)
	{
		$componentAssociation = $this->getSectionComponentAssociation($componentCode);
		if ($componentAssociation) {
			return $componentAssociation;
		}

		$componentAssociation = $this->getCheckboxComponentAssociation($componentCode);
		if ($componentAssociation) {
			return $componentAssociation;
		}

		return null;
	}

	/**
	 * Set section components
	 *
	 * Set the section components that make up the order line.
	 * Overwrites any existing section components that may
	 * have been set.
	 *
	 * @param SectionComponentAssociation[] $sectionComponentAssociations
	 * @return $this
	 */
	public function setSectionComponentAssociations(array $sectionComponentAssociations)
	{
		$this->sectionComponentAssociations = $sectionComponentAssociations;

		foreach ($sectionComponentAssociations as $sectionComponentsAssociation) {
			$sectionComponentsAssociation->setOrderLine($this);
		}

		return $this;
	}

	/**
	 * Get section component associations
	 *
	 * Get all section component associations added to the
	 * order line.
	 *
	 * @return SectionComponentAssociation[]
	 */
	public function getSectionComponentAssociations()
	{
		return $this->sectionComponentAssociations;
	}

	/**
	 * Get a section component association
	 *
	 * Get a section component association by its component code
	 *
	 * @param string $componentCode
	 * @return SectionComponentAssociation
	 */
	public function getSectionComponentAssociation($componentCode)
	{
		foreach ($this->sectionComponentAssociations as $sectionComponentAssociation) {
			if ($sectionComponentAssociation->getComponentCode() === $componentCode) {
				return $sectionComponentAssociation;
			}
		}

		return null;
	}

	/**
	 * Set checkbox components
	 *
	 * Set the checkbox components that make up the order line.
	 * Overwrites any existing checkbox components that may
	 * have been set.
	 *
	 * @param CheckboxComponentAssociation[] $checkBoxComponentAssociations
	 * @return $this
	 */
	public function setCheckboxComponentAssociations(array $checkBoxComponentAssociations)
	{
		$this->checkBoxComponentAssociations = $checkBoxComponentAssociations;

		foreach ($checkBoxComponentAssociations as $checkBoxComponentAssociation) {
			$checkBoxComponentAssociation->setOrderLine($this);
		}

		return $this;
	}

	/**
	 * Get checkbox component associations
	 *
	 * Get all checkbox component associations added to the
	 * order line.
	 *
	 * @return CheckboxComponentAssociation[]
	 */
	public function getCheckboxComponentAssociations()
	{
		return $this->checkBoxComponentAssociations;
	}

	/**
	 * Get a checkbox component association
	 *
	 * Get a checkbox component association by its component code
	 *
	 * @param string $componentCode
	 * @return CheckboxComponentAssociation
	 */
	public function getCheckboxComponentAssociation($componentCode)
	{
		foreach ($this->checkBoxComponentAssociations as $checkBoxComponentAssociation) {
			if ($checkBoxComponentAssociation->getComponentCode() === $componentCode) {
				return $checkBoxComponentAssociation;
			}
		}

		return null;
	}

	/**
	 * Get a section or checkbox footer component
	 *
	 * Get a footer component for the component code given.
	 * Returns null if the component is not found.
	 *
	 * @param string $componentCode
	 * @return CheckboxComponentAssociation|SectionComponentAssociation|null
	 */
	public function getFooterComponentAssociation($componentCode)
	{
		$componentAssociation = $this->getFooterSectionComponentAssociation($componentCode);
		if ($componentAssociation) {
			return $componentAssociation;
		}

		$componentAssociation = $this->getFooterCheckboxComponentAssociation($componentCode);
		if ($componentAssociation) {
			return $componentAssociation;
		}

		return null;
	}

	/**
	 * Set section footer components
	 *
	 * Set the section footer components that make up the order line.
	 * Overwrites any existing section components that may
	 * have been set.
	 *
	 * @param SectionComponentAssociation[] $sectionComponentAssociations
	 * @return $this
	 */
	public function setFooterSectionComponentAssociations(array $sectionComponentAssociations)
	{
		$this->lineFooterSectionComponentAssociations = $sectionComponentAssociations;

		foreach ($sectionComponentAssociations as $sectionComponentAssociation) {
			$sectionComponentAssociation->setOrderLine($this);
		}

		return $this;
	}

	/**
	 * Get section footer component associations
	 *
	 * Get all section component footer associations added to the
	 * order line.
	 *
	 * @return SectionComponentAssociation[]
	 */
	public function getFooterSectionComponentAssociations()
	{
		return $this->lineFooterSectionComponentAssociations;
	}

	/**
	 * Get a section footer component association
	 *
	 * Get a section footer component association by its component code
	 *
	 * @param string $componentCode
	 * @return SectionComponentAssociation
	 */
	public function getFooterSectionComponentAssociation($componentCode)
	{
		foreach ($this->lineFooterSectionComponentAssociations as $sectionComponentAssociation) {
			if ($sectionComponentAssociation->getComponentCode() === $componentCode) {
				return $sectionComponentAssociation;
			}
		}

		return null;
	}

	/**
	 * @param CheckboxComponentAssociation[] $checkBoxComponentAssociations
	 * @return $this
	 */
	public function setFooterCheckboxComponentAssociations(array $checkBoxComponentAssociations)
	{
		$this->lineFooterCheckboxComponentAssociations = $checkBoxComponentAssociations;

		foreach ($checkBoxComponentAssociations as $checkBoxComponentAssociation) {
			$checkBoxComponentAssociation->setOrderLine($this);
		}

		return $this;
	}

	/**
	 * Get all footer checkbox component associations
	 *
	 * @return CheckboxComponentAssociation[]
	 */
	public function getFooterCheckboxComponentAssociations()
	{
		return $this->lineFooterCheckboxComponentAssociations;
	}

	/**
	 * Get a footer checkbox component association by its component code
	 *
	 * @param string $componentCode
	 * @return CheckboxComponentAssociation
	 */
	public function getFooterCheckboxComponentAssociation($componentCode)
	{
		foreach ($this->lineFooterCheckboxComponentAssociations as $checkBoxComponentAssociation) {
			if ($checkBoxComponentAssociation->getComponentCode() === $componentCode) {
				return $checkBoxComponentAssociation;
			}
		}

		return null;
	}

	/**
	 * Set the external asset collection
	 *
	 * Set the external asset collection instance which handles pricing of
	 * all external assets in an efficient way.
	 *
	 * @param ExternalAssetCollection $externalAssetCollection
	 * @return $this
	 */
	public function setExternalAssetCollection(ExternalAssetCollection $externalAssetCollection)
	{
		$this->externalAssetCollection = $externalAssetCollection;
		return $this;
	}

	/**
	 * Set voucher name
	 *
	 * Set the voucher name applied to the order line.
	 *
	 * @param string $voucherName
	 * @return $this
	 */
	public function setVoucherName($voucherName)
	{
		$this->lineSession['itemvouchername'] = $voucherName;
		return $this;
	}

	/**
	 * Get voucher name
	 *
	 * Get the voucher name applied to the order line.
	 *
	 * @return string|null
	 */
	public function getVoucherName()
	{
		return $this->lineSession['itemvouchername'];
	}

	/**
	 * Set whether a voucher can be applied.
	 *
	 * Set whether a voucher can be applied to the order line.
	 * This is used by the voucher later to decide if the voucher
	 * should be applied. The actual discount is not applied by
	 * this method.
	 *
	 * @param bool $canApplyVoucher
	 * @return $this
	 */
	public function setCanApplyVoucher($canApplyVoucher)
	{
		$this->lineSession['itemvoucherapplied'] = $canApplyVoucher ? 1 : 0;
		return $this;
	}

	/**
	 * Check if a voucher can be applied to this order line
	 *
	 * Check if a voucher can be applied to this order line. Note
	 * that this does not mean the voucher is applied, and a
	 * voucher may choose not to apply a discount based on other
	 * criteria.
	 *
	 * @return bool
	 */
	public function canApplyVoucher()
	{
		return @$this->lineSession['itemvoucherapplied'] === 1;
	}

	/**
	 * Check if the order line has been discounted
	 *
	 * Check if the order line has been discounted by a voucher.
	 *
	 * @return bool
	 */
	public function isDiscounted()
	{
		return @$this->session['order']['itemsdiscounted'][$this->currentLine] === true;
	}

	/**
	 * Calculate the price of the order line and apply any scripted discounts
	 *
	 * Calculate the price of the order line and apply any scripted discounts.
	 * Any non-scripted discounts are not applied here, but should be done in
	 * a separate step by calling the method discountPrice().
	 *
	 * @return DiscountablePrice
	 */
	public function calculatePrice()
	{
		$this->requestedProductQuantity = $this->getProductQuantity();
		$productQuantity = $this->requestedProductQuantity;

		// Initial product price
		try {
			// Add the product price to the running price
			// If a price break cannot be found, catch the exception and mark the product price as unavailable
			$this->productPrice = $this->priceBreakSet->createPrice($this->getTaxRate(), $this->isShowPricesWithTax(), $productQuantity);
			$this->setProductQuantity($productQuantity);

			// Running price for tracking all the order line pricing values
			// All prices added to the running price must use the order lines tax rate
			$this->orderLinePrice = new DiscountablePrice($productQuantity, $this->getTaxRate(), $this->getCurrency());
			$this->orderLinePrice->addPrice($this->productPrice);

			$this->lineSession['itemhasproductprice'] = 1;

			// Note: Assignment of the shipping quantity here is for backwards compatibility reasons,
			// probably isn't required, and is definitely wrong for multiline orders
			$this->session['shipping'][0]['shippingqty'] = $this->lineSession['itemqty'];

			// If the product price is recorded gross, track the gross tax rate used
			$grossTaxRate = $this->priceBreakSet->getGrossTaxRate();
			if ($grossTaxRate) {
				$this->lineSession['pricetaxcode'] = $grossTaxRate->getCode();
				$this->lineSession['pricetaxrate'] = $grossTaxRate->getRate();
			}
		} catch (PriceBreakNotFoundException $ex) {
			// An appropriate price break could not be found
			// Mark the product price as not found but continue with pricing the various order line
			// addons and components
			$this->lineSession['itemhasproductprice'] = 0;

			$this->orderLinePrice = new DiscountablePrice($productQuantity, $this->getTaxRate(), $this->getCurrency());
		}

		// Picture pricing
		foreach ($this->pictureComponents as $pictureComponent) {
			$price = $pictureComponent->calculatePrice();
			$this->orderLinePrice->addPrice($price);
		}

		// External asset pricing
		if (null !== $this->externalAssetCollection) {
			$price = $this->externalAssetCollection->calculatePrice();
			$this->orderLinePrice->addPrice($price);
		}

		// Calendar customisations
		foreach ($this->calendarComponents as $calendarComponent) {
			$price = $calendarComponent->calculatePrice();
			$this->orderLinePrice->addPrice($price);
		}

		// AI Component
		foreach ($this->AIComponents as $AIComponent) {
			$price = $AIComponent->calculatePrice();
			$this->orderLinePrice->addPrice($price);
		}

		// Checkbox components
		foreach ($this->checkBoxComponentAssociations as $checkboxComponentAssociation) {
			$price = $checkboxComponentAssociation->calculatePrice();
			$this->orderLinePrice->addPrice($price);
		}

		// Section components
		foreach ($this->sectionComponentAssociations as $sectionComponentAssociation) {
			$price = $sectionComponentAssociation->calculatePrice();
			$this->orderLinePrice->addPrice($price);
		}

		// Footer checkbox components
		foreach ($this->lineFooterCheckboxComponentAssociations as $checkboxComponentAssociation) {
			$price = $checkboxComponentAssociation->calculatePrice();
			$this->orderLinePrice->addPrice($price);
		}

		// Footer section components
		foreach ($this->lineFooterSectionComponentAssociations as $sectionComponentAssociation) {
			$price = $sectionComponentAssociation->calculatePrice();
			$this->orderLinePrice->addPrice($price);
		}

		// Reset the order lines discount status
		$this->resetDiscountStatus();

		// Apply product level scripted discounts
		if ($this->order->scriptDiscount($this)) {
			// If the script didn't discount the line, explicitly mark the line as not discounted
			if (!isset($this->session['order']['itemsdiscounted'][$this->currentLine])) {
				$this->session['order']['itemsdiscounted'][$this->currentLine] = false;
			}
		}

		return $this->orderLinePrice;
	}

	/**
	 * Apply any non-scripted voucher discounts to the order line
	 *
	 * Apply any non-scripted voucher discounts to the order line, which is done
	 * separately to scripted voucher discounts in order to ensure all order line
	 * prices are calculated so that vouchers applied to the highest or lowest
	 * priced matching lines are done so based on correct order line price.
	 */
	public function discountPrice()
	{
		// Request non-script vouchers be applied
		$this->order->discount($this);

		// Save out the product price data
		// If a product price was not found, override to 0
		if ($this->productPrice === null) {
			$this->lineSession['itemproductunitsell'] = '0';
			$this->lineSession['itemproducttotalweight'] = '0';
			$this->lineSession['itemproducttotalcost'] = '0';
			$this->lineSession['itemproducttotalsell'] = '0';
			$this->lineSession['itemproducttotalsellnotax'] = '0';
			$this->lineSession['itemproducttotaltax'] = '0';
			$this->lineSession['itemproducttotalsellwithtax'] = '0';
		} else {
			$this->lineSession['itemproducttotalcost'] = $this->productPrice->getFullCost();
			$this->lineSession['itemproducttotalweight'] = $this->productPrice->getFullWeight();
			$this->lineSession['itemproductunitsell'] = $this->productPrice->getUnit();
			$this->lineSession['itemproducttotalsellnotax'] = $this->productPrice->getFullNet();
			$this->lineSession['itemproducttotaltax'] = $this->order->isShowPricesWithTax() ? $this->productPrice->getFullTax() : '0';
			$this->lineSession['itemproducttotalsellwithtax'] = $this->productPrice->getFullGross();
			$this->lineSession['itemproducttotalsell'] = $this->productPrice->getFullSell($this->isShowPricesWithTax());
		}

		$this->lineSession['itemtotalcost'] = $this->orderLinePrice->getTotalCost();
		$this->lineSession['itemsubtotal'] = $this->orderLinePrice->getFullSell($this->isShowPricesWithTax());
		$this->lineSession['itemtotalsell'] = $this->orderLinePrice->getDiscountedSell($this->order->isShowPricesWithTax());
		$this->lineSession['itemtotalsellnotax'] = $this->orderLinePrice->getDiscountedNetPricingNet();
		$this->lineSession['itemtotalsellnotaxnodiscount'] =$this->orderLinePrice->getFullNet();
		$this->lineSession['itemtotalsellwithtax'] = $this->orderLinePrice->getDiscountedGrossPricingGross();
		$this->lineSession['itemtotalsellwithtaxnodiscount'] = $this->orderLinePrice->getFullGross();
		$this->lineSession['itemtotalweight'] = $this->orderLinePrice->getTotalWeight();
		$this->lineSession['itemtaxtotal'] = $this->orderLinePrice->getDiscountedTax($this->order->isShowPricesWithTax());
	}

	/**
	 * Get the order line price
	 *
	 * Get the order line price, as calculated by calculatePrice and discounted
	 * by discountPrice. If calculatePrice is not called, null is returned.
	 *
	 * @return DiscountablePrice
	 */
	public function getPrice()
	{
		return $this->orderLinePrice;
	}

	/**
	 * Discount the net price
	 *
	 * Supply new discounted net pricing gross values.
	 *
	 * Current discounted values can be obtained through the
	 * getDiscountedNetPricingNet method.
	 *
	 * @param string $discountedPrice
	 * @throws LogicException
	 * @throws InvalidArgumentException
	 */
	public function discountNetPrice($discountedPrice)
	{
		$this->orderLinePrice->discountNetPrice($discountedPrice);

		$this->session['order']['itemsdiscounted'][$this->currentLine] = true;
		$this->lineSession['itemdiscountvalue'] = $this->orderLinePrice->getDiscountSellAmount($this->order->isShowPricesWithTax());
		$this->lineSession['itemdiscountvaluenotax'] = $this->orderLinePrice->getDiscountNetAmount();
		$this->lineSession['itemdiscountvaluenwithtax'] = $this->orderLinePrice->getDiscountGrossAmount();
		$this->lineSession['itemtotalsellnotaxalldiscounted'] = $this->orderLinePrice->getDiscountedNetPricingNet();
		$this->lineSession['itemtotalsell'] = $this->orderLinePrice->getDiscountedSell($this->order->isShowPricesWithTax());
		$this->lineSession['itemtaxtotal'] = $this->orderLinePrice->getDiscountedTax($this->order->isShowPricesWithTax());
	}

	/**
	 * Discount the gross price
	 *
	 * Supply new discounted gross pricing gross values.
	 *
	 * Current discounted values can be obtained through the
	 * getDiscountedGrossPricingGross method.
	 *
	 * @param string $discountedPrice
	 * @throws LogicException
	 * @throws InvalidArgumentException
	 */
	public function discountGrossPrice($discountedPrice)
	{
		$this->orderLinePrice->discountGrossPrice($discountedPrice);

		$this->session['order']['itemsdiscounted'][$this->currentLine] = true;
		$this->lineSession['itemdiscountvalue'] = $this->orderLinePrice->getDiscountSellAmount($this->order->isShowPricesWithTax());
		$this->lineSession['itemdiscountvaluenotax'] = $this->orderLinePrice->getDiscountNetAmount();
		$this->lineSession['itemdiscountvaluenwithtax'] = $this->orderLinePrice->getDiscountGrossAmount();
		$this->lineSession['itemtotalsellwithtaxalldiscounted'] = $this->orderLinePrice->getDiscountedGrossPricingGross();
		$this->lineSession['itemtotalsell'] = $this->orderLinePrice->getDiscountedSell($this->order->isShowPricesWithTax());
		$this->lineSession['itemtaxtotal'] = $this->orderLinePrice->getDiscountedTax($this->order->isShowPricesWithTax());
	}

	/**
	 * Get the full net price of the discountable entity
	 *
	 * Get the full net price of the discountable entity. This
	 * value should not included any discounts previously made.
	 *
	 * @return string
	 */
	public function getFullNet()
	{
		return $this->orderLinePrice->getFullNet();
	}

	/**
	 * Get the full tax price of the discountable entity
	 *
	 * Get the full tax price of the discountable entity. This
	 * value should not included any discounts previously made.
	 *
	 * @return string
	 */
	public function getFullTax()
	{
		return $this->orderLinePrice->getFullTax();
	}

	/**
	 * Get the full gross price of the discountable entity
	 *
	 * Get the full gross price of the discountable entity. This
	 * value should not included any discounts previously made.
	 *
	 * @return string
	 */
	public function getFullGross()
	{
		return $this->orderLinePrice->getFullGross();
	}

	/**
	 * Get the discounted net pricing net value
	 *
	 * Get the discounted net value for net pricing.
	 * This value is the net price with discounts applied,
	 * resulting in recalculation of the tax and gross.
	 *
	 * @return string
	 */
	public function getDiscountedNetPricingNet()
	{
		return $this->orderLinePrice->getDiscountedNetPricingNet();
	}

	/**
	 * Get the discounted net pricing tax value
	 *
	 * Get the discounted tax value for net pricing.
	 * This value is the tax recalculated as a result of a discounted
	 * net.
	 *
	 * @return string
	 */
	public function getDiscountedNetPricingTax()
	{
		return $this->orderLinePrice->getDiscountedNetPricingTax();
	}

	/**
	 * Get the discounted net pricing gross value
	 *
	 * Get the discounted gross value for net pricing.
	 * This value is the gross recalculated as a result of a discounted
	 * net.
	 *
	 * @return string
	 */
	public function getDiscountedNetPricingGross()
	{
		return $this->orderLinePrice->getDiscountedNetPricingGross();
	}

	/**
	 * Get the discounted gross pricing net value
	 *
	 * Get the discounted net value for gross pricing.
	 * This value is the net recalculated as a result of a discounted
	 * gross.
	 *
	 * @return string
	 */
	public function getDiscountedGrossPricingNet()
	{
		return $this->orderLinePrice->getDiscountedGrossPricingNet();
	}

	/**
	 * Get the discounted gross pricing tax value
	 *
	 * Get the discounted tax value for gross pricing.
	 * This value is the tax recalculated as a result of a discounted
	 * gross.
	 *
	 * @return string
	 */
	public function getDiscountedGrossPricingTax()
	{
		return $this->orderLinePrice->getDiscountedGrossPricingTax();
	}

	/**
	 * Get the discounted gross pricing gross value
	 *
	 * Get the discounted gross value for gross pricing.
	 * This value is the gross price with discounts applied,
	 * resulting in recalculation of the tax and net.
	 *
	 * @return string
	 */
	public function getDiscountedGrossPricingGross()
	{
		return $this->orderLinePrice->getDiscountedGrossPricingGross();
	}

	/**
	 * Get the net unit price
	 *
	 * Get the unit price of the order line running
	 * price based on net. This price is calculated
	 * using the full order line net price divided
	 * by the order line item product quantity.
	 *
	 * @return string
	 */
	public function getNetUnit()
	{
		return $this->orderLinePrice->getUnitNet();
	}

	/**
	 * Get the unrounded net unit price
	 *
	 * Get the unit price of the order line running
	 * price based on net. This price is calculated
	 * using the full order line net price divided
	 * by the order line item product quantity.
	 *
	 * @return mixed
	 */
	public function getUnRoundedNetUnit()
	{
		return $this->orderLinePrice->getUnRoundedNetUnit();
	}

	/**
	 * Get the gross unit price
	 *
	 * Get the unit price of the order line running
	 * price based on gross. This price is calculated
	 * using the full order line net price divided
	 * by the order line item product quantity.
	 *
	 * @return string
	 */
	public function getGrossUnit()
	{
		return $this->orderLinePrice->getUnitGross();
	}

	/**
	 * Get the unrounded gross unit price
	 *
	 * Get the unit price of the order line running
	 * price based on gross. This price is calculated
	 * using the full order line net price divided
	 * by the order line item product quantity.
	 *
	 * @return string
	 */
	public function getUnRoundedGrossUnit()
	{
		return $this->orderLinePrice->getUnRoundedGrossUnit();
	}

	/**
	 * Reset the discount status of the order line
	 *
	 * Resets the order line session data to a state whereby
	 * the voucher has not been applied. A voucher discount
	 * can then be re-applied on the reset values.
	 */
	private function resetDiscountStatus()
	{
		unset($this->session['order']['itemsdiscounted'][$this->currentLine]);
		$this->lineSession['itemdiscountvalue'] = '0';
		$this->lineSession['itemdiscountvaluenotax'] = '0';
		$this->lineSession['itemdiscountvaluenwithtax'] = '0';

		$this->lineSession['itemtotalsellnotaxalldiscounted'] = $this->orderLinePrice->getDiscountedNetPricingNet();
		$this->lineSession['itemtotalsellwithtaxalldiscounted'] = $this->orderLinePrice->getDiscountedGrossPricingGross();
	}
}
