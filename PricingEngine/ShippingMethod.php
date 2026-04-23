<?php

namespace PricingEngine;

use InvalidArgumentException;
use LogicException;
use PricingEngine\Price\DiscountablePrice;
use PricingEngine\Price\Price;
use PricingEngine\Tax\TaxRate;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Shipping method
 *
 * Represents a shipping method used in an order.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class ShippingMethod implements ShippingMethodInterface
{
	/**
	 * @var OrderInterface
	 */
	private $order;

	/**
	 * @var TaxRateInterface
	 */
	private $taxRate;

	/**
	 * @var TaxRateInterface
	 */
	private $grossTaxRate;

	/**
	 * @var DiscountablePrice
	 */
	private $shippingPrice;

	/**
	 * @var mixed[]
	 */
	private $shippingSession;

	/**
	 * Constructor
	 *
	 * @param mixed[] $shippingSession
	 */
	public function __construct(&$shippingSession)
	{
		$this->shippingSession = &$shippingSession;

		if (!empty($this->shippingSession['shippingratepricetaxcode'])) {
			$this->grossTaxRate = new TaxRate(
				$this->shippingSession['shippingratepricetaxcode'],
				$this->shippingSession['shippingratepricetaxrate']
			);
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
	 * Set voucher name
	 *
	 * Set the voucher name applied to the shipping method.
	 *
	 * @param string $voucherName
	 */
	public function setVoucherName($voucherName)
	{
		$this->order->setVoucherName($voucherName);
	}

	/**
	 * Get voucher name
	 *
	 * Get the voucher name applied to the shipping method.
	 *
	 * @return string|null
	 */
	public function getVoucherName()
	{
		return $this->order->getVoucherName();
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
	 * Set tax rate
	 *
	 * Set the tax rate used for the shipping method.
	 *
	 * @param TaxRateInterface $taxRate
	 * @return $this
	 */
	public function setTaxRate(TaxRateInterface $taxRate)
	{
		$this->taxRate = $taxRate;
		$this->shippingSession['shippingratetaxcode'] = $taxRate->getCode();
		$this->shippingSession['shippingratetaxrate'] = $taxRate->getRate();

		return $this;
	}

	/**
	 * Get tax rate
	 *
	 * Get the tax rate used for the shipping method.
	 *
	 * @return TaxRateInterface
	 */
	public function getTaxRate()
	{
		if ($this->taxRate === null ||
			$this->taxRate->getCode() !== $this->shippingSession['shippingratetaxcode'] ||
			$this->taxRate->getRate() !== $this->shippingSession['shippingratetaxrate']) {
			$this->taxRate = new TaxRate(
				$this->shippingSession['shippingratetaxcode'],
				$this->shippingSession['shippingratetaxrate']
			);
		}

		return $this->taxRate;
	}

	/**
	 * Get gross price tax rate
	 *
	 * If the price is recorded as gross, returns the gross price
	 * tax rate.
	 *
	 * @return TaxRateInterface
	 */
	public function getGrossTaxRate()
	{
		if (!empty($this->shippingSession['shippingratepricetaxcode']) && (
			$this->grossTaxRate === null ||
			$this->grossTaxRate->getCode() !== $this->shippingSession['shippingratepricetaxcode'] ||
			$this->grossTaxRate->getRate() !== $this->shippingSession['shippingratepricetaxrate'])
		) {
			$this->grossTaxRate = new TaxRate(
				$this->shippingSession['shippingratepricetaxcode'],
				$this->shippingSession['shippingratepricetaxrate']
			);
		}

		return $this->grossTaxRate;
	}

	/**
	 * Calculate the shipping price
	 *
	 * Calculate the shipping price, applying any vouchers, and return
	 * the discounted price.
	 *
	 * @return DiscountablePrice
	 */
	public function calculateShipping()
	{
		if (null === $this->shippingPrice) {
			$taxRate = $this->getTaxRate();

			$places = $this->getPlaces();
			$net = $this->shippingSession['shippingratesellnotax'];
			$gross = $this->shippingSession['shippingratesellwithtax'];
			$tax = bcsub($this->shippingSession['shippingratesellwithtax'], $this->shippingSession['shippingratesellnotax'], $places);

			$this->shippingPrice = new DiscountablePrice(1, $taxRate, $this->getCurrency());
			$price = new Price($this->getCurrency(), $net, $tax, $gross, '0', '0', '0', '0', $taxRate);
			$this->shippingPrice->addPrice($price);

			$this->shippingSession['shippingratetaxtotal'] = $this->shippingPrice->getDiscountedTax($this->order->isShowPricesWithTax());
			$this->shippingSession['shippingratecalctax'] = $this->order->hasSingleTaxRate() ? 0 : 1;
			$this->shippingSession['shippingratediscountvalue'] = $this->shippingPrice->getDiscountSellAmount($this->order->isShowPricesWithTax());
			$this->shippingSession['shippingratetotalsell'] = $this->shippingPrice->getDiscountedSell($this->order->isShowPricesWithTax());
			$this->shippingSession['shippingratetotalsellnotax'] = $this->shippingPrice->getDiscountedNetPricingNet();
			$this->shippingSession['shippingratetotalsellwithtax'] = $this->shippingPrice->getDiscountedGrossPricingGross();

			// Apply discounts to the order shipping
			$this->order->discount($this);
		}

		return $this->shippingPrice;
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
		$this->calculateShipping();
		$this->shippingPrice->resetNetDiscountToFull();
		$this->shippingPrice->discountNetPrice($discountedPrice);

		$this->shippingSession['shippingratetaxtotal'] = $this->shippingPrice->getDiscountedTax($this->order->isShowPricesWithTax());
		$this->shippingSession['shippingratediscountvalue'] = $this->shippingPrice->getDiscountSellAmount($this->order->isShowPricesWithTax());
		$this->shippingSession['shippingratetotalsell'] = $this->shippingPrice->getDiscountedSell($this->order->isShowPricesWithTax());

		if (false === $this->order->isShowPricesWithTax()) {
			$this->shippingSession['shippingratetotalsellnotax'] = $this->shippingPrice->getDiscountedNetPricingNet();
			$this->shippingSession['shippingratetotalsellwithtax'] = $this->shippingPrice->getDiscountedNetPricingGross();
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
		$this->calculateShipping();
		$this->shippingPrice->resetGrossDiscountToFull();
		$this->shippingPrice->discountGrossPrice($discountedPrice);

		$this->shippingSession['shippingratetaxtotal'] = $this->shippingPrice->getDiscountedTax($this->order->isShowPricesWithTax());
		$this->shippingSession['shippingratediscountvalue'] = $this->shippingPrice->getDiscountSellAmount($this->order->isShowPricesWithTax());
		$this->shippingSession['shippingratetotalsell'] = $this->shippingPrice->getDiscountedSell($this->order->isShowPricesWithTax());

		if (true === $this->order->isShowPricesWithTax()) {
			$this->shippingSession['shippingratetotalsellnotax'] = $this->shippingPrice->getDiscountedGrossPricingNet();
			$this->shippingSession['shippingratetotalsellwithtax'] = $this->shippingPrice->getDiscountedGrossPricingGross();
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
		return $this->shippingPrice->getFullNet();
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
		return $this->shippingPrice->getFullTax();
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
		return $this->shippingPrice->getFullGross();
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
		return $this->shippingPrice->getDiscountedNetPricingNet();
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
		return $this->shippingPrice->getDiscountedNetPricingTax();
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
		return $this->shippingPrice->getDiscountedNetPricingGross();
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
		return $this->shippingPrice->getDiscountedGrossPricingNet();
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
		return $this->shippingPrice->getDiscountedGrossPricingTax();
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
		return $this->shippingPrice->getDiscountedGrossPricingGross();
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
		return $this->order->getCurrency()->getDecimalPlaces();
	}
}
