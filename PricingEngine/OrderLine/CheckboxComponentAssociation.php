<?php

namespace PricingEngine\OrderLine;

use PricingEngine\Component\CheckboxComponent;
use PricingEngine\Component\ComponentProductMapping;
use PricingEngine\OrderLineInterface;
use PricingEngine\Price\Price;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Order line checkbox component
 *
 * Order line checkbox component class with
 * functionality for handling checkboxes added to the order line
 * or order line footer.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class CheckboxComponentAssociation
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

		if (!isset($this->session['metadata'])) {
			$this->session['metadata'] = [];
		}
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
		return $this;
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
	public function setParentAssociation(SectionComponentAssociation $parentAssociation = null)
	{
		$this->parentAssociation = $parentAssociation;
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
	 * Set the component checked status
	 *
	 * Set whether the checkbox component is checked or not. A checked
	 * component will be priced and included in the price of final order.
	 * An unchecked component will be priced but the price will not be
	 * added to the order total.
	 *
	 * @param bool $checked
	 * @return $this
	 */
	public function setChecked($checked)
	{
		$this->session['checked'] = (bool) $checked;
		return $this;
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
		$componentQuantity = $this->getComponentQuantity();
		$productQuantity = $this->orderLine->getProductQuantity();
		$pageCount = $this->orderLine->getPageCount();

		$price = $this->componentMapping->calculatePrice($this->taxRate, $this->isShowPricesWithTax(), $productQuantity, $pageCount, $componentQuantity);

		if ($price) {
			$this->session['hasprice'] = 1;
			$this->session['componenthasprice'] = 1;
			$this->setComponentQuantity($componentQuantity);

			$grossTaxRate = $price->getGrossTaxRate();
			$this->session['pricetaxcode'] = $grossTaxRate !== null ? $grossTaxRate->getCode() : '';
			$this->session['pricetaxrate'] = $grossTaxRate !== null ? $grossTaxRate->getRate() : '';

			$this->session['orderfootertaxcode'] = $this->taxRate->getCode();
			$this->session['orderfootertaxrate'] = $this->taxRate->getRate();
			$this->session['orderfootertaxname'] = $this->taxRate->getName() !== null ? $this->taxRate->getName() : '';

			$this->session['unitsell'] = $price->getUnit();
			$this->session['totalcost'] = $price->getFullCost();
			$this->session['totalsell'] = $price->getFullSell($this->isShowPricesWithTax());
			$this->session['totaltax'] = $price->getFullTax();
			$this->session['totalsellnotax'] = $price->getFullNet();
			$this->session['totalsellwithtax'] = $price->getFullGross();
			$this->session['totalweight'] = $price->getFullWeight();
			$this->session['subtotal'] = $price->getFullSell($this->isShowPricesWithTax());
		} else {
			$this->session['componenthasprice'] = 0;
			$this->session['hasprice'] = 0;
		}

		// Sync metadata
		/** @var CheckboxComponent $component */
		$component = $this->componentMapping->getComponent();
		$this->session['metadata'] = $component->syncMetadata($this->session['metadata']);

		return @$this->session['checked'] ? $price : null;
	}
}
