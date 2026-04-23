<?php

namespace PricingEngine\Order;

use InvalidArgumentException;
use LogicException;
use PricingEngine\Component\CheckboxComponent;
use PricingEngine\Component\ComponentProductMapping;
use PricingEngine\CurrencyInterface;
use PricingEngine\OrderInterface;
use PricingEngine\OrderLineInterface;
use PricingEngine\Price\DiscountablePrice;
use PricingEngine\Price\DiscountablePriceInterface;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Order footer specific checkbox component
 *
 * Order footer checkbox component specific class with
 * functionality for handling checkboxes added to the order
 * footer.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class FooterCheckboxComponentAssociation implements OrderFooterComponentInterface
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
	 * @var DiscountablePriceInterface
	 */
	protected $componentPrice;

	/**
	 * @var TaxRateInterface
	 */
	private $taxRate;

	/**
	 * @var FooterSectionComponentAssociation
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
	 * @param TaxRateInterface $taxRate
	 * @param mixed[] $session
	 */
	public function __construct(ComponentProductMapping $componentMapping, TaxRateInterface $taxRate, &$session)
	{
		$this->componentMapping = $componentMapping;
		$this->taxRate = $taxRate;
		$this->session = &$session;

		if (!isset($this->session['metadata'])) {
			$this->session['metadata'] = [];
		}
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
	 * Discount the net price
	 *
	 * Supply new discounted net pricing net values.
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
	 * Test the component checked status
	 *
	 * TEst if the checkbox component is checked or not. A checked
	 * component will be priced and included in the price of final order.
	 * An unchecked component will be priced but the price will not be
	 * added to the order total.
	 *
	 * @return bool
	 */
	public function isChecked()
	{
		return array_key_exists('checked', $this->session) && $this->session['checked'] == 1;
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
	 * If the component is not checked, then the price is calculated,
	 * saved to the session but the price is not returned for
	 * inclusion in the order total.
	 *
	 * @return DiscountablePriceInterface
	 */
	public function calculateDiscountablePrice()
	{
		// Reset pricing values
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

		// Calculate price
		$productQuantity = $this->getProductQuantity();
		$pageCount = $this->getPageCount();
		$componentQuantity = $this->getComponentQuantity();

		$price = $this->componentMapping->calculatePrice($this->taxRate, $this->isShowPricesWithTax(), $productQuantity, $pageCount, $componentQuantity);
		$this->setComponentQuantity($componentQuantity);

		if ($price) {
			$this->session['hasprice'] = 1;
			$this->session['componenthasprice'] = 1;

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
			$this->session['orderfootertaxcode'] = $this->taxRate->getCode();
			$this->session['orderfootertaxrate'] = $this->taxRate->getRate();
			$this->session['orderfootertaxname'] = $this->taxRate->getName();

			// Set product tax rate
			$grossTaxRate = $price->getGrossTaxRate();
			if ($grossTaxRate) {
				$this->session['pricetaxcode'] = $grossTaxRate->getCode();
				$this->session['pricetaxrate'] = $grossTaxRate->getRate();
			} else {
				$this->session['pricetaxcode'] = null;
				$this->session['pricetaxrate'] = 0;
			}

			// If the component is checked (included with the order), setup
			// the components price and discount if required
			if ($this->isChecked()) {
				$this->componentPrice = new DiscountablePrice($productQuantity, $this->taxRate, $this->getCurrency());
				$this->componentPrice->addPrice($price);
			}
		} else {
			$this->session['hasprice'] = 0;
			$this->session['componenthasprice'] = 0;
		}

		// Sync metadata
		/** @var CheckboxComponent $component */
		$component = $this->componentMapping->getComponent();
		$this->session['metadata'] = $component->syncMetadata($this->session['metadata']);

		return $this->componentPrice;
	}
}
