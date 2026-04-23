<?php

namespace PricingEngine;

use Extension\Script\Exception\ExtensionScriptMethodNotFoundException;
use Extension\Script\Exception\ExtensionScriptNotLoadedException;
use Extension\Script\Exception\UnhandledExtensionScriptErrorException;
use Extension\Script\TaxExtensionScript;
use InvalidArgumentException;
use LogicException;
use PricingEngine\Enum\ExtensionScript;
use PricingEngine\Enum\OrderSection;
use PricingEngine\Enum\Voucher\DiscountSection;
use PricingEngine\Order\Callback\OrderFooterCallback;
use PricingEngine\Tax\TaxBreakdownInterface;
use PricingEngine\Order\FooterCheckboxComponentAssociation;
use PricingEngine\Order\FooterSectionComponentAssociation;
use PricingEngine\Price\DiscountablePriceComposite;
use PricingEngine\Tax\TaxRateInterface;
use PricingEngine\Voucher\DiscountableInterface;
use PricingEngine\Voucher\VoucherInterface;
use PricingEngine\Voucher\VoucherContext;

/**
 * Order handling
 *
 * Represents an order made by a customer for the purposes
 * of calculating the price, which is persisted to a session
 * reference variable, supplied upon construction.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class Order implements OrderInterface
{
	private $permitOrderDiscounts = false;

	/**
	 * @var string
	 */
	private $orderFooterCallbackClassName;

	/**
	 * @var CurrencyInterface
	 */
	private $currency;

	/**
	 * @var TaxBreakdownInterface
	 */
	private $taxBreakdown;

	/**
	 * @var bool
	 */
	private $showPricesWithTax;

	/**
	 * @var mixed[]
	 */
	private $session;

	/**
	 * @var OrderLine[]
	 */
	private $orderLines = [];

	/**
	 * @var int
	 */
	private $orderLineCount = 0;

	/**
	 * @var FooterSectionComponentAssociation[]
	 */
	private $footerSectionComponentAssociations = [];

	/**
	 * @var FooterCheckboxComponentAssociation[]
	 */
	private $footerCheckboxComponentAssociations = [];

	/**
	 * @var ShippingMethod
	 */
	private $shippingMethod;

	/**
	 * @var VoucherInterface
	 */
	private $voucher;

	/**
	 * @var VoucherContext
	 */
	private $voucherContext;

	/**
	 * @var DiscountablePriceComposite
	 */
	private $orderPrice;

	/**
	 * @var string
	 */
	private $netVoucherDiscountValue = '0';

	/**
	 * @var string
	 */
	private $grossVoucherDiscountValue = '0';

	/**
	 * Constructor
	 *
	 * @param CurrencyInterface $currency
	 * @param TaxBreakdownInterface $taxBreakdown
	 * @param bool $showPricesWithTax
	 * @param mixed[] $session
	 * @param string $orderFooterCallbackClassName
	 */
	public function __construct(CurrencyInterface $currency, TaxBreakdownInterface $taxBreakdown, $showPricesWithTax,
		&$session, $orderFooterCallbackClassName = OrderFooterCallback::class)
	{
		$this->currency = $currency;
		$this->taxBreakdown = $taxBreakdown;
		$this->showPricesWithTax = $showPricesWithTax;
		$this->session = &$session;
		$this->orderFooterCallbackClassName = $orderFooterCallbackClassName;
	}

	/**
	 * Add an order line to the order
	 *
	 * Add an order line to the order for the
	 * purpose of calculating to the order price.
	 * This must be performed before the price is
	 * calculated using the calculateOrder method.
	 *
	 * @param OrderLine $orderLine
	 */
	public function addOrderLine(OrderLine $orderLine)
	{
		$this->orderLines[] = $orderLine;
		$this->orderLineCount++;
		$orderLine->setOrder($this);
	}

	/**
	 * Get the order lines
	 *
	 * Get all the order line object instances
	 * in an array. Returns an empty array if none
	 * added.
	 *
	 * @return OrderLineInterface[]
	 */
	public function getOrderLines()
	{
		return $this->orderLines;
	}

	/**
	 * Get the number of order lines
	 *
	 * Get the number of order lines in the order.
	 * Returns 0 if none added.
	 *
	 * @return int
	 */
	public function getOrderLineCount()
	{
		return $this->orderLineCount;
	}

	/**
	 * Get a specific order line
	 *
	 * Get a specific order line using the line
	 * number given. Returns null not found.
	 *
	 * @param int $orderLineNumber
	 * @return OrderLineInterface
	 */
	public function getOrderLine($orderLineNumber)
	{
		return @$this->orderLines[$orderLineNumber];
	}

	/**
	 * Get a section or checkbox component
	 *
	 * Get the order footer component association for
	 * the given component code that has been added
	 * to the order. Returns null if not found.
	 *
	 * @param string $componentCode
	 * @return FooterCheckboxComponentAssociation|FooterSectionComponentAssociation|null
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
	 * Get all footer checkbox component associations
	 *
	 * Get all the order footer checkbox component
	 * associations added to the order. Returns an
	 * empty array if non have been added.
	 *
	 * @return FooterCheckboxComponentAssociation[]
	 */
	public function getFooterCheckboxComponentAssociations()
	{
		return $this->footerCheckboxComponentAssociations;
	}

	/**
	 * Get order footer checkbox component association by component code
	 *
	 * Get an order footer checkbox component association by the
	 * given component code. Returns null if not found.
	 *
	 * @param string $componentCode
	 * @return FooterCheckboxComponentAssociation
	 */
	public function getFooterCheckboxComponentAssociation($componentCode)
	{
		foreach ($this->footerCheckboxComponentAssociations as $checkBoxComponentAssociation) {
			if ($checkBoxComponentAssociation->getComponentCode() === $componentCode) {
				return $checkBoxComponentAssociation;
			}
		}

		return null;
	}

	/**
	 * Set footer checkbox component associations
	 *
	 * Set the order footer checkbox component associations, overwriting
	 * any that may have previously been passed.
	 *
	 * @param FooterCheckboxComponentAssociation[] $checkBoxComponents
	 * @return $this
	 */
	public function setFooterCheckboxComponentAssociations(array $checkBoxComponents)
	{
		$this->footerCheckboxComponentAssociations = $checkBoxComponents;

		foreach ($checkBoxComponents as $checkBoxComponent) {
			$checkBoxComponent->setOrder($this);
		}

		return $this;
	}

	/**
	 * Get all footer section component associations
	 *
	 * Get all the order footer section component
	 * associations added to the order. Returns an
	 * empty array if non have been added.
	 *
	 * @return FooterSectionComponentAssociation[]
	 */
	public function getFooterSectionComponentAssociations()
	{
		return $this->footerSectionComponentAssociations;
	}

	/**
	 * Get order footer section component association by component code
	 *
	 * Get an order footer section component association by the
	 * given component code. Returns null if not found.
	 *
	 * @param string $componentCode
	 * @return FooterSectionComponentAssociation
	 */
	public function getFooterSectionComponentAssociation($componentCode)
	{
		foreach ($this->footerSectionComponentAssociations as $sectionComponentAssociation) {
			if ($sectionComponentAssociation->getComponentCode() === $componentCode) {
				return $sectionComponentAssociation;
			}
		}

		return null;
	}

	/**
	 * Set footer section component associations
	 *
	 * Set the order footer section component associations, overwriting
	 * any that may have previously been passed.
	 *
	 * @param FooterSectionComponentAssociation[] $sectionComponentAssociations
	 * @return $this
	 */
	public function setFooterSectionComponentAssociations(array $sectionComponentAssociations)
	{
		$this->footerSectionComponentAssociations = $sectionComponentAssociations;

		foreach ($sectionComponentAssociations as $footerSectionComponentAssociation) {
			$footerSectionComponentAssociation->setOrder($this);
		}

		return $this;
	}

	/**
	 * Set shipping method
	 *
	 * Set the shipping method to be used to handle
	 * the shipping price. If a shipping method
	 * is supplied, the shipping price is not included in the
	 * price calculation.
	 *
	 * @param ShippingMethod $shippingMethod
	 */
	public function setShippingMethod(ShippingMethod $shippingMethod)
	{
		$this->shippingMethod = $shippingMethod;
		$shippingMethod->setOrder($this);
	}

	/**
	 * Get the shipping method
	 *
	 * Get the shipping method associated to the order.
	 * Returns null if no shipping method has been added.
	 *
	 * @return ShippingMethodInterface
	 */
	public function getShippingMethod()
	{
		return $this->shippingMethod;
	}

	/**
	 * Set voucher
	 *
	 * Set the voucher instance to be used to apply
	 * any voucher discounts to the order upon calculation.
	 * If no instance is added, no discount will be applied.
	 *
	 * @param VoucherInterface $voucher
	 */
	public function setVoucher(VoucherInterface $voucher)
	{
		$this->voucher = $voucher;
		$this->voucherContext = null;
	}

	/**
	 * Get voucher
	 *
	 * Get the voucher instance used to apply
	 * discounts to the order. Returns null if no
	 * voucher has been added.
	 *
	 * @return VoucherInterface
	 */
	public function getVoucher()
	{
		return $this->voucher;
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
		$this->session['order']['vouchername'] = $voucherName;
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
		return $this->session['order']['vouchername'];
	}

	/**
	 * Get voucher type
	 *
	 * Get the voucher type of the voucher applied.
	 * This is made available for voucher scripting, which
	 * sets the voucher dependent on the configuration and
	 * provided by the voucher script.
	 *
	 * @return string
	 */
	public function getVoucherType()
	{
		return @$this->session['order']['vouchertype'];
	}

	/**
	 * Set voucher type
	 *
	 * Set the voucher type for the voucher applied.
	 * This is made available for voucher scripting, which
	 * sets the voucher dependent on the configuration and
	 * provided by the voucher script.
	 * Returns null if no voucher type has been set.
	 *
	 * @param string $voucherType
	 * @return $this
	 */
	public function setVoucherType($voucherType)
	{
		$this->session['order']['vouchertype'] = $voucherType;
		return $this;
	}

	/**
	 * Set voucher discount type
	 *
	 * Set the voucher discount type for the voucher applied.
	 * This is made available for voucher scripting, which
	 * sets the voucher dependent on the configuration and
	 * provided by the voucher script.
	 * Returns null if no voucher discount type has been set.
	 *
	 * @param string $discountType
	 * @return $this
	 */
	public function setVoucherDiscountType($discountType)
	{
		$this->session['order']['voucherdiscounttype'] = $discountType;
		return $this;
	}

	/**
	 * Get voucher sell price
	 *
	 * Get the voucher sell price for the voucher applied.
	 * This is made available for voucher scripting, which
	 * sets the voucher dependent on the configuration and
	 * provided by the voucher script.
	 * Returns null if no voucher sell price has been set.
	 *
	 * @return string
	 */
	public function getVoucherSellPrice()
	{
		return @$this->session['order']['vouchersellprice'];
	}

	/**
	 * Set voucher sell price
	 *
	 * Get the voucher sell price for the voucher applied.
	 * This is made available for voucher scripting, which
	 * sets the voucher dependent on the configuration and
	 * provided by the voucher script.
	 *
	 * @param string $sellPrice
	 * @return $this
	 */
	public function setVoucherSellPrice($sellPrice)
	{
		$this->session['order']['vouchersellprice'] = $sellPrice;
		return $this;
	}

	/**
	 * Get voucher agent fee
	 *
	 * Get the voucher agent fee for the voucher applied.
	 * This is made available for voucher scripting, which
	 * sets the voucher dependent on the configuration and
	 * provided by the voucher script.
	 * Returns null if no voucher agent fee has been set.
	 *
	 * @return string
	 */
	public function getVoucherAgentFee()
	{
		return @$this->session['order']['voucheragentfee'];
	}

	/**
	 * Set voucher agent fee
	 *
	 * Set the voucher agent fee for the voucher applied.
	 * This is made available for voucher scripting, which
	 * sets the voucher dependent on the configuration and
	 * provided by the voucher script.
	 *
	 * @param string $agentFee
	 * @return $this
	 */
	public function setVoucherAgentFee($agentFee)
	{
		$this->session['order']['voucheragentfee'] = $agentFee;
		return $this;
	}

	/**
	 * Get voucher context
	 *
	 * Get the voucher context for the voucher instance added,
	 * or null if no voucher has been added.
	 * The voucher context is used to track information for
	 * the voucher instance.
	 *
	 * @return VoucherContext|null
	 */
	public function getVoucherContext()
	{
		if (null === $this->voucher) {
			return null;
		}

		if (null === $this->voucherContext) {
			$this->voucherContext = $this->voucher->createContext($this);
		}

		return $this->voucherContext;
	}

	/**
	 * Set gift card balance
	 *
	 * Set the amount of initial gift card balance to deduct
	 * from the order when calculating the amount payable.
	 *
	 * @param string $giftCardBalance
	 */
	public function setGiftCardBalance($giftCardBalance)
	{
		$this->session['usergiftcardbalance'] = $giftCardBalance;
		$this->session['ordergiftcarddeleted'] = false;
	}

	/**
	 * Get the initial gift card balance
	 *
	 * Get the amount of initial gift card balance used to
	 * deduct from the order when calcualting the amount
	 * payable. Returns 0 if no amount has been set.
	 *
	 * @return string
	 */
	public function getGiftCardBalanceInitial()
	{
		return (string) (array_key_exists('usergiftcardbalance', $this->session) ? $this->session['usergiftcardbalance'] : '0');
	}

	/**
	 * Get the amount of gift card balance used
	 *
	 * @return string
	 */
	public function getGiftCardBalanceUsed()
	{
		return $this->canApplyGiftCard() && !empty($this->session['order']['ordergiftcardtotal'])
			? (string) $this->session['order']['ordergiftcardtotal']
			: '0'
		;
	}

	/**
	 * Set the gift card balance used.
	 *
	 * Set the amount of gift card balance used when deducting
	 * the amount payable of the order.
	 *
	 * @param string $giftCardUsed
	 */
	private function setGiftCardBalanceUsed($giftCardUsed)
	{
		$this->session['order']['ordergiftcardtotal'] = $giftCardUsed;
	}

	/**
	 * Test if the gift card balance can be applied to the order
	 *
	 * @return bool
	 */
	private function canApplyGiftCard()
	{
		if (!array_key_exists('ordergiftcarddeleted', $this->session)) {
			return true;
		}

		return $this->session['ordergiftcarddeleted'] != 1;
	}

	/**
	 * Apply the discount, only if the voucher is a script voucher type
	 *
	 * If the voucher added to the order is a script voucher,
	 * apply the discount. Returns true if the voucher discount was attempted,
	 * false otherwise. Note that a discount may still not be applied by the
	 * voucher script.
	 *
	 * @param DiscountableInterface $discountable
	 * @return bool
	 */
	public function scriptDiscount(DiscountableInterface $discountable)
	{
		$voucherContext = $this->getVoucherContext();
		if ($voucherContext && $voucherContext->isScriptVoucher()) {
			$voucherContext->discount($discountable);
			return true;
		}

		return false;
	}

	/**
	 * Apply the discount, only if the voucher is a non-script voucher type
	 *
	 * If the voucher added to the order is not a script voucher,
	 * apply the discount. Returns true if the voucher discount was attempted,
	 * false otherwise. Note that a discount may still not be applied by
	 * the voucher instance if no matching lines are found.
	 *
	 * @param DiscountableInterface $discountable
	 * @return bool
	 */
	public function discount(DiscountableInterface $discountable)
	{
		$voucherContext = $this->getVoucherContext();
		if ($voucherContext && !$voucherContext->isScriptVoucher()) {
			$voucherContext->discount($discountable);
			return true;
		}

		return false;
	}

	/**
	 * Record the net and tax amounts for the given tax rate
	 * in the tax breakdown
	 *
	 * Record the net and tax monetary values within the tax
	 * breakdown for the given tax rate instance supplied.
	 *
	 * @param TaxRateInterface $taxRate
	 * @param string $net
	 * @param string $tax
	 */
	public function recordTaxInBreakdown(TaxRateInterface $taxRate, $net, $tax)
	{
		$this->taxBreakdown->recordTaxInBreakdown($taxRate, $net, $tax);
	}

	/**
	 * Check if a single tax rate exists
	 *
	 * Checks if the tax breakdown contains just a single tax rate
	 * and whether that tax rate code is for a custom tax script.
	 *
	 * If only a single rate exists, and we're not using tax scripting
	 * then true is returned, false otherwise.
	 *
	 * @return bool
	 */
	public function hasSingleTaxRate()
	{
		return $this->taxBreakdown->hasSingleTaxRate();
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
		return $this->showPricesWithTax;
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
		return @$this->session['order']['isreorder'] === 1;
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
		return $this->session['userdata']['companycode'];
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
		return $this->session['licensekeydata']['groupcode'];
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
		return $this->currency->getDecimalPlaces();
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
		if ($this->permitOrderDiscounts) {
			$this->orderPrice->discountNetPrice($discountedPrice);
		} else {
			$this->netVoucherDiscountValue = $discountedPrice;
		}
	}

	/**
	 * Discount the net price, in reverse
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
	public function discountNetPriceReverse($discountedPrice)
	{
		if ($this->permitOrderDiscounts) {
			$this->orderPrice->discountNetPrice($discountedPrice, true);
		} else {
			$this->netVoucherDiscountValue = $discountedPrice;
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
		if ($this->permitOrderDiscounts) {
			$this->orderPrice->discountGrossPrice($discountedPrice);
		} else {
			$this->grossVoucherDiscountValue = $discountedPrice;
		}
	}

	/**
	 * Discount the gross price, in reverse
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
	public function discountGrossPriceReverse($discountedPrice)
	{
		if ($this->permitOrderDiscounts) {
			$this->orderPrice->discountGrossPrice($discountedPrice, true);
		} else {
			$this->grossVoucherDiscountValue = $discountedPrice;
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
		return $this->orderPrice->getFullNet();
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
		return $this->orderPrice->getFullTax();
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
		return $this->orderPrice->getFullGross();
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
		return $this->orderPrice->getDiscountedNetPricingNet();
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
		return $this->orderPrice->getDiscountedNetPricingTax();
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
		return $this->orderPrice->getDiscountedNetPricingGross();
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
		return $this->orderPrice->getDiscountedGrossPricingNet();
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
		return $this->orderPrice->getDiscountedGrossPricingTax();
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
		return $this->orderPrice->getDiscountedGrossPricingGross();
	}

	/**
	 * Get amount due
	 *
	 * Get the amount due (gross price - gift card balance deductions)
	 * for the order.
	 *
	 * @return string
	 */
	public function getAmountDue()
	{
		return (string) @$this->session['order']['ordertotaltopay'];
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
		return $this->currency;
	}

	/**
	 * Calculate the price of the order, synchronising all data to the session
	 *
	 * @throws ExtensionScriptMethodNotFoundException
	 * @throws ExtensionScriptNotLoadedException
	 * @throws UnhandledExtensionScriptErrorException
	 */
	public function calculateOrder()
	{
		// Trigger shipping tax scripting and check if the tax rate returned is included in the tax breakdown
		$taxBreakdownArray = $this->session['order']['ordertaxproductbreakdown'];

		if (($this->session['shipping'][0]['shippingmethodcode'] != '') && ($this->session['shipping'][0]['shippingratecode'] != '')) {
			// if the tax rate code is for custom tax call the script to calculate it
			if (ExtensionScript::TAX_SCRIPT_TAX_CODE == substr($this->session['shipping'][0]['shippingratetaxcode'], 0, 13)) {
				$taxExtensionScriptFacade = new TaxExtensionScript(ExtensionScript::getExtensionPath(), $this->session);
				if ($taxExtensionScriptFacade->load()) {
					$taxRate = $taxExtensionScriptFacade->getShippingTaxRate();

					// make sure that the code returned is a custom tax
					if ($taxRate) {
						$this->session['shipping'][0]['shippingratetaxcode'] = $taxRate->getCode();
						$this->session['shipping'][0]['shippingratetaxname'] = $taxRate->getName();
						$this->session['shipping'][0]['shippingratetaxrate'] = $taxRate->getRate();
					}
				}
			}
		}

		$shippingTaxRateCodeExists = false;
		foreach ($taxBreakdownArray as $taxBreakdown) {
			if ($this->session['shipping'][0]['shippingratetaxcode'] == $taxBreakdown['taxratecode']) {
				$shippingTaxRateCodeExists = true;
				break;
			}
		}

		// update the shipping tax breakdown
		if (!$shippingTaxRateCodeExists) {
			$taxBreakdownItem = Array();
			$taxBreakdownItem['taxratecode'] = $this->session['shipping'][0]['shippingratetaxcode'];
			$taxBreakdownItem['taxratename'] = $this->session['shipping'][0]['shippingratetaxname'];
			$taxBreakdownItem['taxrate'] = $this->session['shipping'][0]['shippingratetaxrate'];
			$taxBreakdownItem['nettotal'] = 0.00;
			$taxBreakdownItem['taxtotal'] = 0.00;

			$taxBreakdownArray[] = $taxBreakdownItem;
		}

		// set the final tax breakdown
		$this->session['order']['ordertaxbreakdown'] = $taxBreakdownArray;

		// determine if all tax rates are equal
		if (count($taxBreakdownArray) == 1) {
			// we only have one rate
			// if it is TAOPIX_CUSTOM it is a generic custom rate so we say they aren't the equal as we don't actually know
			if (ExtensionScript::TAX_SCRIPT_TAX_CODE == $taxBreakdownArray[0]['taxratecode']) {
				$this->session['order']['orderalltaxratesequal'] = 0;
			} else {
				// the tax rates are equal
				$this->session['order']['orderalltaxratesequal'] = 1;
			}
		} else {
			// we have more than one rate so they are not equal
			$this->session['order']['orderalltaxratesequal'] = 0;
		}

		// Create a discountable price composite for tracking the full order price
		$places = $this->getPlaces();
		$this->orderPrice = new DiscountablePriceComposite($this->currency);

		// Order line price calculation
		foreach ($this->orderLines as $orderLine) {
			$orderLineRunningPrice = $orderLine->calculatePrice();
			$this->orderPrice->addDiscountablePrice(OrderSection::ORDER_LINE, $orderLineRunningPrice);
		}

		// Order line price discounts
		foreach ($this->orderLines as $orderLine) {
			$orderLine->discountPrice();
			$orderLineRunningPrice = $orderLine->getPrice();

			$this->taxBreakdown->recordTaxInBreakdown(
				$orderLineRunningPrice->getTaxRate(),
				$orderLineRunningPrice->getDiscountedNet($this->isShowPricesWithTax()),
				$orderLineRunningPrice->getDiscountedTax($this->isShowPricesWithTax())
			);

			// Force a sync of the footer components quantities for
			// footer components using inherit product quantity
			$this->syncFooterComponentCounts($orderLine);
		}

		// Footer section components
		foreach ($this->footerSectionComponentAssociations as &$sectionComponent) {
			$componentPrices = $sectionComponent->calculateDiscountablePrice();
			$this->orderPrice->addDiscountablePrices(OrderSection::ORDER_FOOTER, $componentPrices);

			foreach ($componentPrices as $componentPrice) {
				$this->taxBreakdown->recordTaxInBreakdown(
					$componentPrice->getTaxRate(),
					$componentPrice->getDiscountedNet($this->isShowPricesWithTax()),
					$componentPrice->getDiscountedTax($this->isShowPricesWithTax())
				);
			}
		}

		// Footer checkbox components
		foreach ($this->footerCheckboxComponentAssociations as &$checkboxComponent) {
			$componentPrice = $checkboxComponent->calculateDiscountablePrice();
			$this->orderPrice->addDiscountablePrice(OrderSection::ORDER_FOOTER, $componentPrice);

			if ($componentPrice) {
				$this->taxBreakdown->recordTaxInBreakdown(
					$componentPrice->getTaxRate(),
					$componentPrice->getDiscountedNet($this->isShowPricesWithTax()),
					$componentPrice->getDiscountedTax($this->isShowPricesWithTax())
				);
			}
		}

		// Calculate shipping
		if ($this->shippingMethod) {
			$shippingPrice = $this->shippingMethod->calculateShipping();
			$this->orderPrice->addDiscountablePrice(OrderSection::SHIPPING, $shippingPrice);

			$this->taxBreakdown->recordTaxInBreakdown(
				$shippingPrice->getTaxRate(),
				$shippingPrice->getDiscountedNet($this->isShowPricesWithTax()),
				$shippingPrice->getDiscountedTax($this->isShowPricesWithTax())
			);
		}

		$this->permitOrderDiscounts = true;
		$voucherContext = $this->getVoucherContext();

		// This is a workaround for voucher script, which discounts at the point of discounting each line.
		// When the processing the lines, the total is not yet known and so cannot be applied at that point.
		// To maintain compatibility, we save the discount amount and wait until here to apply it.
		if (bccomp($this->netVoucherDiscountValue, '0', $places) === 1) {
			$this->orderPrice->discountNetPrice($this->netVoucherDiscountValue);

			if (DiscountSection::TOTAL === $voucherContext->getDiscountSection() && false === $this->isShowPricesWithTax()) {
				$this->session['order']['voucherdiscountvalue'] = $this->netVoucherDiscountValue;
			}
		}

		if (bccomp($this->grossVoucherDiscountValue, '0', $places) === 1) {
			$this->orderPrice->discountGrossPrice($this->grossVoucherDiscountValue);

			if (DiscountSection::TOTAL === $voucherContext->getDiscountSection() && true === $this->isShowPricesWithTax()) {
				$this->session['order']['voucherdiscountvalue'] = $this->grossVoucherDiscountValue;
			}
		}

		// Apply discounts to the order total
		$this->discount($this);

		// Export data to the session for product items (order lines and order footer components)
		if ($voucherContext) {
			$this->session['order']['ordertotalitemdiscountable'] = $voucherContext->getEligibleOrderLineCount();
			$this->session['order']['voucherdiscountsection'] = $voucherContext->getDiscountSection();
		} else {
			$this->session['order']['ordertotalitemdiscountable'] = 0;
		}

		$this->session['order']['ordertotalitemcost'] = $this->orderPrice->getTotalCost([OrderSection::ORDER_LINE, OrderSection::ORDER_FOOTER]);
		$this->session['order']['ordertotalitemtax'] = $this->orderPrice->getDiscountedTax($this->isShowPricesWithTax(), [OrderSection::ORDER_LINE, OrderSection::ORDER_FOOTER]);
		$this->session['order']['ordertotalitemsell'] = $this->orderPrice->getDiscountedSell($this->isShowPricesWithTax(), [OrderSection::ORDER_LINE, OrderSection::ORDER_FOOTER]);
		$this->session['order']['ordertotalitemsellnotax'] = $this->orderPrice->getFullNet([OrderSection::ORDER_LINE, OrderSection::ORDER_FOOTER]);
		$this->session['order']['ordertotalitemsellwithtax'] = $this->orderPrice->getDiscountedGross($this->isShowPricesWithTax(), [OrderSection::ORDER_LINE, OrderSection::ORDER_FOOTER]);
		$this->session['order']['ordertotalitemsellnotaxnodiscount'] = $this->orderPrice->getFullNet([OrderSection::ORDER_LINE, OrderSection::ORDER_FOOTER]);;
		$this->session['order']['ordertotalitemsellwithtaxnodiscount'] = $this->orderPrice->getFullGross([OrderSection::ORDER_LINE, OrderSection::ORDER_FOOTER]);;
		$this->session['order']['ordertotalitemsellnotaxalldiscounted'] = $this->orderPrice->getDiscountedNetPricingNet([OrderSection::ORDER_LINE, OrderSection::ORDER_FOOTER]);
		$this->session['order']['ordertotalitemsellwithtaxalldiscounted'] = $this->orderPrice->getDiscountedGrossPricingGross([OrderSection::ORDER_LINE, OrderSection::ORDER_FOOTER]);


		// Export data to the session for the shipping method chosen
		$this->session['order']['ordertotalshippingcost'] = $this->session['shipping'][0]['shippingratecost'];
		$this->session['order']['ordertotalshippingsellbeforediscount'] = $this->session['shipping'][0]['shippingratesell'];
		$this->session['order']['ordertotalshippingsellafterdiscount'] = $this->session['shipping'][0]['shippingratetotalsell'];
		$this->session['order']['ordertotalshippingtax'] = $this->session['shipping'][0]['shippingratetaxtotal'];
		$this->session['order']['ordertotalshippingweight'] = $this->orderPrice->getTotalWeight();


		// Export data to the session for the order footer components
		$this->session['order']['orderfootersubtotal'] = $this->orderPrice->getDiscountedSell($this->isShowPricesWithTax(), [OrderSection::ORDER_FOOTER]);
		$this->session['order']['orderfootertotalnotax'] = $this->orderPrice->getDiscountedNet($this->isShowPricesWithTax(), [OrderSection::ORDER_FOOTER]);
		$this->session['order']['orderfootertotalwithtax'] = $this->orderPrice->getDiscountedGross($this->isShowPricesWithTax(), [OrderSection::ORDER_FOOTER]);
		$this->session['order']['orderfootertotal'] = $this->orderPrice->getDiscountedGross($this->isShowPricesWithTax(), [OrderSection::ORDER_FOOTER]);
		$this->session['order']['orderfootertotaltax'] = $this->orderPrice->getDiscountedTax($this->isShowPricesWithTax(), [OrderSection::ORDER_FOOTER]);
		$this->session['order']['orderfooterdiscountvalue'] = $this->orderPrice->getDiscountedSell($this->isShowPricesWithTax(), [OrderSection::ORDER_FOOTER]);
		$this->session['order']['orderfootertotalnotaxnodiscount'] = $this->orderPrice->getFullNet([OrderSection::ORDER_FOOTER]);
		$this->session['order']['orderfootertotalwithtaxnodiscount'] = $this->orderPrice->getFullGross([OrderSection::ORDER_FOOTER]);

		// Session data for the order total
		// Note that before discount means, before discounts applied to the total. They still *include* discounts applied to other discount sections.
		$this->session['order']['ordertotalcost'] = $this->orderPrice->getTotalCost();
		$this->session['order']['ordertotalsellbeforediscount'] = $this->orderPrice->getDiscountedSell($this->isShowPricesWithTax(), [OrderSection::ORDER_LINE, OrderSection::ORDER_FOOTER, OrderSection::SHIPPING]);
		$this->session['order']['ordertotaltaxbeforediscount'] = $this->orderPrice->getDiscountedTax($this->isShowPricesWithTax(), [OrderSection::ORDER_LINE, OrderSection::ORDER_FOOTER, OrderSection::SHIPPING]);
		$this->session['order']['ordertotalbeforediscount'] = $this->orderPrice->getFullSell($this->isShowPricesWithTax());

		$this->session['order']['ordertotaldiscount'] = $this->orderPrice->getDiscountSellAmount($this->isShowPricesWithTax());
		$this->session['order']['ordertotalsell'] = $this->orderPrice->getDiscountedSell($this->isShowPricesWithTax());
		$this->session['order']['ordertotaltax'] = $this->orderPrice->getDiscountedTax($this->isShowPricesWithTax());
		$this->session['order']['ordertotal'] = $this->orderPrice->getDiscountedGross($this->isShowPricesWithTax());

		// Apply any gift card balance, adjust amount due, and save to the session
		$amountDue = $this->orderPrice->getDiscountedGross($this->isShowPricesWithTax());

		$giftCardBalanceInitial = $this->getGiftCardBalanceInitial();

		if (bccomp($giftCardBalanceInitial, '0', $places)) {
			if (bccomp($giftCardBalanceInitial, $amountDue, $places) === 1) {
				if ($this->canApplyGiftCard()) {
					$this->setGiftCardBalanceUsed($amountDue);
					$amountDue = '0';
				}
			} else {
				if ($this->canApplyGiftCard()) {
					$this->setGiftCardBalanceUsed($giftCardBalanceInitial);
					$amountDue = bcsub($amountDue, $giftCardBalanceInitial, $places);
				}
			}
		} else {
			$this->setGiftCardBalanceUsed('0');
		}

		$this->session['order']['ordertotaltopay'] = $amountDue;
	}

	/**
	 * Synchronise the order footer component counts
	 *
	 * Synchronise the order footer component counts
	 * for components using inherit parent quantity where
	 * the product has a minimum order quantity.
	 *
	 * @param OrderLineInterface $orderLine
	 * @return mixed[]
	 */
	private function syncFooterComponentCounts(OrderLineInterface $orderLine)
	{
		return call_user_func_array([$this->orderFooterCallbackClassName, 'syncCounts'], [&$this->session, $orderLine]);
	}
}
