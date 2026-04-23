<?php

namespace PricingEngine\OrderLine;

use PricingEngine\Component\ComponentProductMapping;
use PricingEngine\CurrencyInterface;
use PricingEngine\OrderLineInterface;
use PricingEngine\Price\Price;
use PricingEngine\Tax\TaxRateInterface;

class SectionComponentAssociation
{
	/**
	 * @var OrderLineInterface
	 */
	private $orderLine;

	/**
	 * @var ComponentProductMapping
	 */
	private $componentMapping;

	/**
	 * @var string
	 */
	private $productCode;

	/**
	 * @var TaxRateInterface
	 */
	private $taxRate;

	/**
	 * @var SectionComponentAssociation
	 */
	private $parentAssociation;

	/**
	 * @var CheckboxComponentAssociation[]
	 */
	private $subCheckboxAssociations = [];

	/**
	 * @var SectionComponentAssociation[]
	 */
	private $subSectionAssociations = [];

	/**
	 * @var mixed[]
	 */
	private $session;

	/**
	 * Constructor
	 *
	 * @param ComponentProductMapping $componentMapping
	 * @param string $productCode
	 * @param TaxRateInterface $taxRate
	 * @param mixed[] $session
	 */
	public function __construct(ComponentProductMapping $componentMapping, $productCode, TaxRateInterface $taxRate,
		&$session)
	{
		$this->componentMapping = $componentMapping;
		$this->productCode = $productCode;
		$this->taxRate = $taxRate;
		$this->session = &$session;
	}

	/**
	 * Set the order line
	 *
	 * Set the associated order line of the checkbox component.
	 * This should be called by the order line when adding
	 * the component to the order itself.
	 *
	 * @param OrderLineInterface $orderLine
	 * @return $this
	 */
	public function setOrderLine(OrderLineInterface $orderLine)
	{
		$this->orderLine = $orderLine;

		foreach ($this->subSectionAssociations as $subSectionAssociation) {
			$subSectionAssociation->setOrderLine($orderLine);
		}

		foreach ($this->subCheckboxAssociations as $subCheckboxAssociation) {
			$subCheckboxAssociation->setOrderLine($orderLine);
		}

		return $this;
	}

	public function getMapping()
	{
		return $this->componentMapping;
	}

	/**
	 * Get component code
	 *
	 * Get the component code of the associated checkbox component.
	 *
	 * @return string
	 */
	public function getComponentCode()
	{
		return $this->componentMapping->getComponentCode();
	}

	/**
	 * Check if the component is a subcomponent
	 *
	 * Check if the component is a subcomponent of another component.
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
	 * Set the parent component association, creating a subcomponent.
	 *
	 * @param SectionComponentAssociation $parentAssociation
	 */
	public function setParentAssociation(SectionComponentAssociation $parentAssociation)
	{
		$this->parentAssociation = $parentAssociation;
	}

	/**
	 * Get a section or checkbox component
	 *
	 * Get a section of checkbox subcomponent association.
	 *
	 * @param string $componentCode
	 * @return CheckboxComponentAssociation|SectionComponentAssociation|null
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
	 * @return SectionComponentAssociation[]
	 */
	public function getSectionComponentAssociations()
	{
		return $this->subSectionAssociations;
	}

	/**
	 * Get all checkbox component associations
	 *
	 * @return CheckboxComponentAssociation[]
	 */
	public function getCheckboxComponentAssociations()
	{
		return $this->subCheckboxAssociations;
	}

	/**
	 * Set sub-checkbox associations
	 *
	 * @param CheckboxComponentAssociation[] $checkboxAssociations
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
	 * @param SectionComponentAssociation[] $sectionAssociations
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
	 * Set the quantity of components for pricing the component.
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
	 * Get the quantity of components for pricing the component.
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
	 * Get currency
	 *
	 * Get the currency used by the order
	 *
	 * @return CurrencyInterface
	 */
	public function getCurrency()
	{
		return $this->orderLine->getCurrency();
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
		return $this->orderLine->isShowPricesWithTax();
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
	 * Calculate the price of the component for the mapped product
	 *
	 * Forwards calls to the underling component for the mapped product
	 * code and the session reference, returning the price.
	 *
	 * @return Price|null
	 */
	public function calculatePrice()
	{
		$runningPrice = Price::createZeroPrice($this->getCurrency(), $this->taxRate);
		$componentQuantity = $this->getComponentQuantity();
		$productQuantity = $this->orderLine->getProductQuantity();
		$pageCount = $this->orderLine->getPageCount();
		$price = null;

		// If the default code is not set, the component is not valid for the order and
		// we do not price it. Instead, the price defaults to 0 so nothing gets added to
		// the order.
		if (array_key_exists('defaultcode', $this->session) && '' !== $this->session['defaultcode']) {
			$price = $this->componentMapping->calculatePrice($this->taxRate, $this->isShowPricesWithTax(),
				$productQuantity, $pageCount, $componentQuantity);
			$this->setComponentQuantity($componentQuantity);
		}

		// Export product gross price tax data to session
		if ($price) {
			$grossTaxRate = $price->getGrossTaxRate();
			$this->session['pricetaxcode'] = $grossTaxRate ? $grossTaxRate->getCode() : '';
			$this->session['pricetaxrate'] = $grossTaxRate ? $grossTaxRate->getRate() : '';
		}

		$this->session['orderfootertaxcode'] = $this->taxRate->getCode();
		$this->session['orderfootertaxrate'] = $this->taxRate->getRate();
		$this->session['orderfootertaxname'] = $this->taxRate->getName() !== null ? $this->taxRate->getName() : '';

		$this->session['unitsell'] = null !== $price ? $price->getUnit() : '0';
		$this->session['totalcost'] = null !== $price ? $price->getFullCost() : '0';
		$this->session['totalsell'] = null !== $price ? $price->getFullSell($this->isShowPricesWithTax()) : '0';
		$this->session['totaltax'] = null !== $price ? $price->getFullTax() : '0';
		$this->session['totalsellnotax'] = null !== $price ? $price->getFullNet() : '0';
		$this->session['totalsellwithtax'] = null !== $price ? $price->getFullGross() : '0';
		$this->session['totalweight'] = null !== $price ? $price->getFullWeight() : '0';
		$this->session['subtotal'] = null !== $price ? $price->getFullSell($this->isShowPricesWithTax()) : '0';

		$runningPrice->addPrice($price);

		// Sub checkboxes
		foreach ($this->subCheckboxAssociations as $subCheckbox) {
			$price = $subCheckbox->calculatePrice();
			$runningPrice->addPrice($price);
		}

		// Sub sections
		foreach ($this->subSectionAssociations as $subSection) {
			$price = $subSection->calculatePrice();
			$runningPrice->addPrice($price);
		}

		if (!$this->isSubComponent()) {
			$this->session['itemcomponenttotalcost'] = $runningPrice->getFullCost();
			$this->session['itemcomponenttotalsell'] = $runningPrice->getFullSell($this->isShowPricesWithTax());
			$this->session['itemcomponenttotalsellnotax'] = $runningPrice->getFullNet();
			$this->session['itemcomponenttotalsellwithtax'] = $runningPrice->getFullGross();
			$this->session['itemcomponenttotalweight'] = $runningPrice->getFullWeight();
		}

		return $runningPrice;
	}
}
