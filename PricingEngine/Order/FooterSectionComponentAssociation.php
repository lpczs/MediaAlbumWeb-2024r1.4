<?php

namespace PricingEngine\Order;

use InvalidArgumentException;
use LogicException;
use PricingEngine\Component\ComponentProductMapping;
use PricingEngine\Enum\OrderSection;
use PricingEngine\OrderInterface;
use PricingEngine\OrderLineInterface;
use PricingEngine\Price\DiscountablePrice;
use PricingEngine\Price\DiscountablePriceComposite;
use PricingEngine\Price\DiscountablePriceInterface;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Order footer specific section component
 *
 * Order footer section component specific class with
 * functionality for handling sections added to the order
 * footer
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class FooterSectionComponentAssociation implements OrderFooterComponentInterface
{
	/**
	 * @var OrderInterface
	 */
	private $order;

	/**
	 * @var OrderLineInterface[]
	 */
	private $orderLines = [];

	/**
	 * @var ComponentProductMapping
	 */
	private $componentMapping;

	/**
	 * @var FooterSectionComponentAssociation
	 */
	protected $parentAssociation;

	/**
	 * @var FooterSectionComponentAssociation[]
	 */
	protected $subSectionAssociations = [];

	/**
	 * @var FooterCheckboxComponentAssociation[]
	 */
	protected $subCheckboxAssociations = [];

	/**
	 * @var DiscountablePriceInterface
	 */
	protected $componentPrice;

	/**
	 * @var DiscountablePriceComposite
	 */
	protected $componentPriceHierarchyPrice;

	/**
	 * @var TaxRateInterface
	 */
	private $taxRate;

	/**
	 * @var mixed[]
	 */
	private $session;

	/**
	 * Constructor
	 *
	 * @param ComponentProductMapping $componentMapping
	 * @param TaxRateInterface $taxRate
	 * @param mixed[] $session
	 */
	public function __construct(ComponentProductMapping $componentMapping, TaxRateInterface $taxRate, &$session)
	{
		$this->componentMapping = $componentMapping;
		$this->taxRate = $taxRate;
		$this->session = &$session;
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

		foreach ($this->subCheckboxAssociations as $subCheckboxAssociation) {
			$subCheckboxAssociation->setOrder($order);
		}

		foreach ($this->subSectionAssociations as $subSectionAssociation) {
			$subSectionAssociation->setOrder($order);
		}

		return $this;
	}

	/**
	 * An an order line to the footer association
	 *
	 * Footer component associations are linked via
	 * order lines, that is, order lines define
	 * which associations must exist. Multiple lines
	 * can have the same order footer component
	 * association, so link them all here.
	 *
	 * @param OrderLineInterface $orderLine
	 * @return $this
	 */
	public function addOrderLine(OrderLineInterface $orderLine)
	{
		$this->orderLines[] = $orderLine;
		return $this;
	}

	/**
	 * Check if an order line associated with the order
	 * footer component has been discounted
	 *
	 * Check if an order line associated with the order
	 * footer component has been discounted, so that
	 * the footer component may be discounted at the appropriate
	 * phase in price calculation.
	 *
	 * @return bool
	 */
	public function hasDiscountedOrderLine()
	{
		foreach ($this->orderLines as $orderLine) {
			if ($orderLine->isDiscounted()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get component code
	 *
	 * Get the associated order footer component
	 * code for this association.
	 *
	 * @return string
	 */
	public function getComponentCode()
	{
		return $this->componentMapping->getComponentCode();
	}

	/**
	 * Check if the association is for a sub component
	 *
	 * Returns true or false for whether the association
	 * is for a sub component.
	 *
	 * @return bool
	 */
	public function isSubComponent()
	{
		return $this->parentAssociation !== null;
	}

	/**
	 * Set parent association
	 *
	 * Set a parent association for this association, creating
	 * a component/subcomponent relationship.
	 *
	 * @param FooterSectionComponentAssociation $parentAssociation
	 * @return $this
	 */
	public function setParentAssociation(FooterSectionComponentAssociation $parentAssociation)
	{
		$this->parentAssociation = $parentAssociation;
		return $this;
	}

	/**
	 * Set the subsection associations of this association
	 *
	 * Set an array of subsection associations of this
	 * association, creating a component/subcomponent
	 * relationship.
	 *
	 * @param FooterSectionComponentAssociation[] $subSections
	 * @return $this
	 */
	public function setSubSections(array $subSections)
	{
		$this->subSectionAssociations = $subSections;

		foreach ($this->subSectionAssociations as $subSectionAssociation) {
			$subSectionAssociation->setOrder($this->order);
		}

		return $this;
	}

	/**
	 * Set the subcheckbox associations of this association
	 *
	 * Set an array of subcheckbox associations of this
	 * association, creating a component/subcomponent
	 * relationship.
	 *
	 * @param FooterCheckboxComponentAssociation[] $subCheckboxes
	 * @return $this
	 */
	public function setSubCheckboxes(array $subCheckboxes)
	{
		$this->subCheckboxAssociations = $subCheckboxes;

		foreach ($this->subCheckboxAssociations as $subCheckboxAssociation) {
			$subCheckboxAssociation->setOrder($this->order);
		}

		return $this;
	}

	/**
	 * Set product quantity
	 *
	 * Set the quantity of products used for price calculation.
	 *
	 * @param $productQuantity
	 * @return $this
	 */
	public function setProductQuantity($productQuantity)
	{
		$this->session['itemqty'] = $productQuantity;
		return $this;
	}

	/**
	 * Get product quantity
	 *
	 * Get the quantity of products used for price calculation.
	 *
	 * @return int
	 */
	public function getProductQuantity()
	{
		return @$this->session['itemqty'];
	}

	/**
	 * Set page count
	 *
	 * Set the product page count used for price calculation.
	 *
	 * @param $pageCount
	 * @return $this
	 */
	public function setPageCount($pageCount)
	{
		$this->session['itempagecount'] = $pageCount;
		return $this;
	}

	/**
	 * Get page count
	 *
	 * Get the product page count used for price calculation.
	 *
	 * @return int
	 */
	public function getPageCount()
	{
		return @$this->session['itempagecount'];
	}

	/**
	 * Set the component default code
	 *
	 * Set the code of the component that will be used as the default
	 *
	 * @param $defaultCode
	 * @return $this
	 */
	public function setDefaultCode($defaultCode)
	{
		$this->session['defaultcode'] = $defaultCode;
		return $this;
	}

	/**
	 * Set the component quantity
	 *
	 * Set the quantity of components used for price calculation.
	 *
	 * @param int $componentQuantity
	 * @return $this
	 */
	public function setComponentQuantity($componentQuantity)
	{
		$this->session['quantity'] = $componentQuantity;
		return $this;
	}

	/**
	 * Get the component quantity
	 *
	 * Get the quantity of components used for price calculation.
	 *
	 * @return int
	 */
	public function getComponentQuantity()
	{
		if ($this->isSubComponent() && $this->isInheritParentQuantity()) {
			$this->session['quantity'] = $this->parentAssociation->getComponentQuantity();
		}

		return @$this->session['quantity'];
	}

	/**
	 * Set voucher name
	 *
	 * Set the voucher name used to apply discounts
	 * to the discountable. Returns null if no voucher name
	 * has been set.
	 *
	 * @param string $voucherName
	 */
	public function setVoucherName($voucherName)
	{
	}

	/**
	 * Get voucher name
	 *
	 * Get the voucher name used to apply discounts
	 * to the discountable. Returns null if no voucher name has been
	 * set.
	 *
	 * @return null|string
	 */
	public function getVoucherName()
	{
		return null;
	}

	/**
	 * Get a section or checkbox component
	 *
	 * Get a subcomponent association of this section component.
	 *
	 * @param string $componentCode
	 * @return FooterCheckboxComponentAssociation|FooterSectionComponentAssociation|null
	 */
	public function getComponentAssociation($componentCode)
	{
		foreach ($this->subSectionAssociations as $subSectionAssociation) {
			if ($subSectionAssociation->getComponentCode() === $componentCode) {
				return $subSectionAssociation;
			}
		}

		foreach ($this->subCheckboxAssociations as $subCheckboxAssociation) {
			if ($subCheckboxAssociation->getComponentCode() === $componentCode) {
				return $subCheckboxAssociation;
			}
		}

		return null;
	}

	/**
	 * Get all section component associations
	 *
	 * Get all the section subcomponent associations of this association.
	 *
	 * @return FooterSectionComponentAssociation[]
	 */
	public function getSectionComponentAssociations()
	{
		return $this->subSectionAssociations;
	}

	/**
	 * Get all checkbox component associations
	 *
	 * Get all the checkbo subcomponent associations of this association.
	 *
	 * @return FooterCheckboxComponentAssociation[]
	 */
	public function getCheckboxComponentAssociations()
	{
		return $this->subCheckboxAssociations;
	}

	/**
	 * Set sub-checkbox associations
	 *
	 * Set subcheckbox associations of this association.
	 *
	 * @param FooterCheckboxComponentAssociation[] $checkboxAssociations
	 * @return $this
	 */
	public function setSubCheckboxAssociations(array $checkboxAssociations)
	{
		foreach ($checkboxAssociations as $checkboxAssociation) {
			$checkboxAssociation->setParentAssociation($this);
		}

		$this->subCheckboxAssociations = $checkboxAssociations;
		return $this;
	}

	/**
	 * Set sub-section associations
	 *
	 * @param FooterSectionComponentAssociation[] $sectionAssociations
	 * @return $this
	 */
	public function setSubSectionAssociations(array $sectionAssociations)
	{
		foreach ($sectionAssociations as $sectionAssociation) {
			$sectionAssociation->setParentAssociation($this);
		}

		$this->subSectionAssociations = $sectionAssociations;
		return $this;
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
		$this->componentPrice->discountNetPrice($discountedPrice);

		$this->session['totalsellnotax'] = $this->componentPrice->getDiscountedNet($this->isShowPricesWithTax());
		$this->session['totalsellwithtax'] = $this->componentPrice->getDiscountedGross($this->isShowPricesWithTax());
		$this->session['subtotal'] = $this->componentPrice->getDiscountedSell($this->isShowPricesWithTax());

		$this->session['discountvalue'] = $this->componentPrice->getDiscountSellAmount($this->isShowPricesWithTax());

		if (false === $this->isShowPricesWithTax()) {
			$this->session['discountvaluenotax'] = $this->componentPrice->getDiscountNetAmount();
			$this->session['discountvaluewithtax'] = $this->componentPrice->getDiscountNetAmount();
		}

		$this->session['itemcomponenttotalsellnotax'] = $this->componentPriceHierarchyPrice->getDiscountedNet($this->isShowPricesWithTax());
		$this->session['itemcomponenttotalsellwithtax'] = $this->componentPriceHierarchyPrice->getDiscountedGross($this->isShowPricesWithTax());
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
		$this->componentPrice->discountGrossPrice($discountedPrice);

		$this->session['totalsellnotax'] = $this->componentPrice->getDiscountedNet($this->isShowPricesWithTax());
		$this->session['totalsellwithtax'] = $this->componentPrice->getDiscountedGross($this->isShowPricesWithTax());
		$this->session['subtotal'] = $this->componentPrice->getDiscountedSell($this->isShowPricesWithTax());

		$this->session['discountvalue'] = $this->componentPrice->getDiscountSellAmount($this->isShowPricesWithTax());

		if (true === $this->isShowPricesWithTax()) {
			$this->session['discountvaluenotax'] = $this->componentPrice->getDiscountGrossAmount();
			$this->session['discountvaluewithtax'] = $this->componentPrice->getDiscountGrossAmount();
		}

		$this->session['itemcomponenttotalsellnotax'] = $this->componentPriceHierarchyPrice->getDiscountedNet($this->isShowPricesWithTax());
		$this->session['itemcomponenttotalsellwithtax'] = $this->componentPriceHierarchyPrice->getDiscountedGross($this->isShowPricesWithTax());
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
		return $this->componentPrice->getFullNet();
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
		return $this->componentPrice->getFullTax();
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
		return $this->componentPrice->getFullGross();
	}

	/**
	 * Get the discounted net pricing net value
	 *
	 * Get the discounted net value for net pricing.
	 * This value is the gross price with discounts applied,
	 * resulting in recalculation of the tax and gross.
	 *
	 * @return string
	 */
	public function getDiscountedNetPricingNet()
	{
		return $this->componentPrice->getDiscountedNetPricingNet();
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
		return $this->componentPrice->getDiscountedNetPricingTax();
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
		return $this->componentPrice->getDiscountedNetPricingGross();
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
		return $this->componentPrice->getDiscountedGrossPricingNet();
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
		return $this->componentPrice->getDiscountedGrossPricingTax();
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
		return $this->componentPrice->getDiscountedGrossPricingGross();
	}

	/**
	 * Get the number decimal places used for rounding values
	 *
	 * When rounding a monetary value that will be expressed using
	 * this currency, the value must be rounded. Different currencies
	 * have different levels of decimal precision that are used on display,
	 * typically 2 or 3. This accessor returns the number of places to use.
	 *
	 * @return int
	 */
	public function getPlaces()
	{
		return $this->order->getPlaces();
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
	 * Check if the inherit parent quantity option is set
	 *
	 * Check if the inherit parent quantity option is set in
	 * the components configuration for the purposes of pricing
	 * the component.
	 *
	 * @return bool
	 */
	public function isInheritParentQuantity()
	{
		return $this->componentMapping->isInheritParentQuantity();
	}

	/**
	 * Calculate price
	 *
	 * Calculate the price of the footer component and return
	 * the price of the component for inclusion in the order price.
	 *
	 * @return DiscountablePriceInterface[]
	 */
	public function calculateDiscountablePrice()
	{
		$this->componentPrice = null;

		$this->session['unitsell'] = '0';
		$this->session['totalcost'] = '0';
		$this->session['totalsell'] = '0';
		$this->session['totaltax'] = '0';
		$this->session['totalsellnotax'] = '0';
		$this->session['totalsellwithtax'] = '0';
		$this->session['totalweight'] = '0';
		$this->session['subtotal'] = '0';

		$this->session['discountvalue'] = '0';
		$this->session['discountedvalue'] = '0';
		$this->session['discountedtax'] = '0';
		$this->session['discountvaluenotax'] = '0';
		$this->session['discountvaluewithtax'] = '0';

		$currency = $this->order->getCurrency();
		$productQuantity = $this->getProductQuantity();
		$pageCount = $this->getPageCount();
		$componentQuantity = $this->getComponentQuantity();
		$price = null;

		// If the default code is not set, the component is not valid for the order and
		// we do not price it. Instead, the price defaults to 0 so nothing gets added to
		// the order.
		if (array_key_exists('defaultcode', $this->session) && '' !== $this->session['defaultcode']) {
			// Calculate component price
			$price = $this->componentMapping->calculatePrice($this->taxRate, $this->isShowPricesWithTax(),
				$productQuantity, $pageCount, $componentQuantity);
			$this->setComponentQuantity($componentQuantity);
		}

		$this->componentPrice = new DiscountablePrice($productQuantity, $this->taxRate, $currency);

		if ($price) {
			// Update component price session data
			$this->session['unitsell'] = $price->getUnit();
			$this->session['totalcost'] = $price->getFullCost();
			$this->session['totalsell'] = $price->getFullSell($this->isShowPricesWithTax());
			$this->session['totaltax'] = $price->getFullTax();
			$this->session['totalsellnotax'] = $price->getFullNet();
			$this->session['totalsellwithtax'] = $price->getFullGross();
			$this->session['totalweight'] = $price->getFullWeight();
			$this->session['subtotal'] = $price->getFullSell($this->isShowPricesWithTax());

			// Set gross price tax rate
			$taxRate = $price->getTaxRate();
			$this->session['orderfootertaxcode'] = $taxRate->getCode();
			$this->session['orderfootertaxrate'] = $taxRate->getRate();
			$this->session['orderfootertaxname'] = $taxRate->getName();

			// Set product tax rate
			$grossTaxRate = $price->getGrossTaxRate();
			if ($grossTaxRate) {
				$this->session['pricetaxcode'] = $grossTaxRate->getCode();
				$this->session['pricetaxrate'] = $grossTaxRate->getRate();
			} else {
				$this->session['pricetaxcode'] = null;
				$this->session['pricetaxrate'] = 0;
			}

			// Apply any voucher discounts
			$this->componentPrice->addPrice($price);
		}

		// Sub components don't proceed any further
		if ($this->isSubComponent()) {
			return [$this->componentPrice];
		}

		// Use the composite class to calculate the component hierarchy totals
		$discountablePrices = [$this->componentPrice];
		$this->componentPriceHierarchyPrice = new DiscountablePriceComposite($currency);
		$this->componentPriceHierarchyPrice->addDiscountablePrice(OrderSection::ORDER_FOOTER, $this->componentPrice);

		// Sub sections
		foreach ($this->subSectionAssociations as &$subSection) {
			$subComponentPrices = $subSection->calculateDiscountablePrice();
			$subSection->componentPriceHierarchyPrice = new DiscountablePriceComposite($currency);
			$subSection->componentPriceHierarchyPrice->addDiscountablePrices(OrderSection::ORDER_FOOTER, $subComponentPrices);
			$discountablePrices = array_merge($discountablePrices, $subComponentPrices);
		}

		// Sub checkboxes
		foreach ($this->subCheckboxAssociations as $subCheckbox) {
			$subComponentPrice = $subCheckbox->calculateDiscountablePrice();

			if ($subComponentPrice !== null) {
				$this->componentPriceHierarchyPrice->addDiscountablePrice(OrderSection::ORDER_FOOTER, $subComponentPrice);
				$discountablePrices[] = $subComponentPrice;
			}
		}

		// Set component hierarchy totals
		$this->session['itemcomponenttotalcost'] = $this->componentPriceHierarchyPrice->getTotalCost();
		$this->session['itemcomponenttotalsell'] = $this->componentPriceHierarchyPrice->getDiscountedSell($this->isShowPricesWithTax());
		$this->session['itemcomponenttotalsellnotax'] = $this->componentPriceHierarchyPrice->getDiscountedNetPricingNet();
		$this->session['itemcomponenttotalsellwithtax'] = $this->componentPriceHierarchyPrice->getDiscountedGrossPricingGross();
		$this->session['itemcomponenttotalweight'] = $this->componentPriceHierarchyPrice->getTotalWeight();

		return $discountablePrices;
	}
}
