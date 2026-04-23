<?php

namespace PricingEngine\Price;

use LogicException;
use InvalidArgumentException;
use PricingEngine\CurrencyInterface;
use PricingEngine\Enum\FinancialPrecision;
use PricingEngine\Enum\OrderSection;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Discountable price composite
 *
 * Represents other discountable price object
 * instances and provides the facility to handle
 * the calculation of totals across all the discountable
 * instances and distributing net or gross discounts
 * over each discountable instance within it.
 *
 * Once a discount has been applied, further discountable
 * instances cannot be added as this may affect how
 * distributing discounts applied.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class DiscountablePriceComposite
{
	private static $tagOrder = [
		OrderSection::ORDER_LINE,
		OrderSection::ORDER_FOOTER,
		OrderSection::SHIPPING,
	];

	/**
	 * @var CurrencyInterface
	 */
	private $currency;

	/**
	 * @var array
	 */
	private $discountablePrices = [];

	/**
	 * @var bool
	 */
	private $hasSingleTaxRate = true;

	/**
	 * @var TaxRateInterface
	 */
	private $initialTaxRate;

	/**
	 * @var string
	 */
	private $discountNetAmount = '0';

	/**
	 * @var string
	 */
	private $discountedNetPricingNet = null;

	/**
	 * @var string
	 */
	private $discountedNetPricingTax = null;

	/**
	 * @var string
	 */
	private $discountedNetPricingGross = null;

	/**
	 * @var string
	 */
	private $discountGrossAmount = '0';

	/**
	 * @var string
	 */
	private $discountedGrossPricingNet = null;

	/**
	 * @var string
	 */
	private $discountedGrossPricingTax = null;

	/**
	 * @var string
	 */
	private $discountedGrossPricingGross = null;

	/**
	 * @var bool
	 */
	private $isFullPriceImmutable = false;

	/**
	 * Constructor
	 *
	 * @param CurrencyInterface $currency
	 */
	public function __construct(CurrencyInterface $currency)
	{
		$this->currency = $currency;
	}

	/**
	 * Get initial tax rate
	 *
	 * Get the tax rate of the price first added to the composite,
	 * or null if a price has not yet been added.
	 *
	 * @return TaxRateInterface
	 */
	public function getInitialTaxRate()
	{
		return $this->initialTaxRate;
	}

	/**
	 * Test if the full price has been made immutable
	 *
	 * Test if the full price has been made immutable
	 * and return the status. If the full price is
	 * immutable, the full price may no longer be
	 * modified by adding new pricing.
	 *
	 * @return bool
	 */
	public function isFullPriceImmutable()
	{
		return $this->isFullPriceImmutable;
	}

	/**
	 * Mark the full price as immutable to prevent further changes
	 *
	 * Once the full price is marked as immutable, the full price can no
	 * longer be added to. This is used in conjunction with
	 * discounting. If the full price is discounted by a relative
	 * amount (e.g. a percentage) then the price should not be changed
	 * in order to protected it from modification.
	 */
	public function markFullPriceAsImmutable()
	{
		$this->isFullPriceImmutable = true;
	}

	/**
	 * Add multiple discountable price instances
	 *
	 * Add multiple discountable price instances to the
	 * composite. All the instances will be added to the total
	 * cost, weight, net, tax and gross values, and will be used
	 * in any discounts that are applied to the composite instance
	 * by distributing the discount across all the composed
	 * discountable instances
	 *
	 * @param string $tag
	 * @param DiscountablePriceInterface[] $prices
	 */
	public function addDiscountablePrices($tag, array $prices)
	{
		foreach ($prices as $price) {
			$this->addDiscountablePrice($tag, $price);
		}
	}

	/**
	 * Add a single discountable price
	 *
	 * Add a single discountable price instance to the
	 * composite. The new instance will be added to the total
	 * cost, weight, net, tax and gross values, and will be used
	 * in any discounts that are applied to the composite instance
	 * by distributing the discount across the all composed
	 * discountable instances.
	 *
	 * @param string $tag
	 * @param DiscountablePriceInterface $price
	 */
	public function addDiscountablePrice($tag, DiscountablePriceInterface $price = null)
	{
		if (!in_array($tag, self::$tagOrder)) {
			throw new LogicException(sprintf('Tag "%s" is not supported.', $tag));
		}

		// Check that the price can still be added to
		if ($this->isFullPriceImmutable()) {
			throw new LogicException(
				'Price is now immutable. Cannot add additional pricing to the price.'
			);
		}

		if (null === $price) {
			return;
		}

		// Ensure the full price cannot be changed
		$price->markFullPriceAsImmutable();

		if ($this->initialTaxRate === null) {
			$this->initialTaxRate = $price->getTaxRate();
		} elseif ($this->hasSingleTaxRate) {
			$this->hasSingleTaxRate = $price->compareTaxRate($this->initialTaxRate);
		}

		if (!isset($this->discountablePrices[$tag])) {
			$this->discountablePrices[$tag] = [];
		}

		$this->discountablePrices[$tag][] = $price;
	}

	/**
	 * Get the tax rate of the discountable price
	 *
	 * Returns an implementation of the TaxRateInterface interface,
	 * that makes up the initial tax rate of the discountable price
	 * added to the composite.
	 *
	 * @return TaxRateInterface
	 */
	public function getTaxRate()
	{
		return $this->initialTaxRate;
	}

	/**
	 * Compare the given TaxRateInterface implementation against
	 * discountable composites initial discountable price tax rate
	 *
	 * Compares the given TaxRateInterface implementation against
	 * the discountable composites initial discountable price tax rate
	 * and returns true or false, depending on whether the rates match.
	 *
	 * @param TaxRateInterface $taxRate
	 * @return bool
	 */
	public function compareTaxRate(TaxRateInterface $taxRate)
	{
		return $this->initialTaxRate === null || $taxRate->getRate() === $this->initialTaxRate->getRate();
	}

	/**
	 * Check if the pricing for the composite is made
	 * up of a single tax rate
	 *
	 * Return whether the pricing for the composite
	 * is made up of a single tax rate, or if the price is made
	 * up of multiple tax rates. Returns true or false, depending
	 * whether a single tax rate is used.
	 *
	 * @return bool
	 */
	public function hasSingleTaxRate()
	{
		return $this->hasSingleTaxRate;
	}

	/**
	 * Discount the net price
	 *
	 * Discount the net price. Current discounted values
	 * can be obtained through the getDiscountedNetPricingNet
	 * method.
	 *
	 * @param string $discountAmount
	 * @param bool $reverse
	 * @throws LogicException
	 * @throws InvalidArgumentException
	 */
	public function discountNetPrice($discountAmount, $reverse = false)
	{
		$discountAmount = (string) $discountAmount;
		$places = $this->currency->getDecimalPlaces();

		// Return quickly if the amount to be discounted is 0
		if (bccomp(0, $discountAmount, $places) >= 0) {
			return;
		}

		// Mark as immutable to prevent further changes
		$this->markFullPriceAsImmutable();

		if ($this->hasSingleTaxRate) {
			// Only a single tax rate exists, start/keep a total of the discount amounts and
			// update this as a summary
			$discountedNetPricingNet = $this->getDiscountedNetPricingNet();
			$discountNetAmount = $this->getDiscountNetAmount();

			if (bccomp($discountAmount, $discountedNetPricingNet, $places) === 1) {
				$discountAmount = $discountedNetPricingNet;
			}

			$this->discountNetAmount = bcadd($discountNetAmount, $discountAmount, $places);
			$discountedNetPricingNet = bcsub($discountedNetPricingNet, $discountAmount, $places);

			list ($this->discountedNetPricingNet, $this->discountedNetPricingTax, $this->discountedNetPricingGross)
				= NetGrossConversion::breakdownNetToGross($discountedNetPricingNet, $this->initialTaxRate->getRate(), $places);
		} else {
			if ($reverse === false) {
				// Apply discount across each discountable price
				foreach (self::$tagOrder as $discountablePriceTag) {
					if (!isset($this->discountablePrices[$discountablePriceTag])) {
						continue;
					}

					/** @var DiscountablePrice $discountablePrice */
					foreach ($this->discountablePrices[$discountablePriceTag] as $discountablePrice) {
						$net = $discountablePrice->getDiscountedNetPricingNet();

						if (bccomp($net, $discountAmount, $places) === 1) {
							$discountablePrice->discountNetPrice($discountAmount);
							$discountAmount = '0';
						} else {
							$discountablePrice->discountNetPrice($net);
							$discountAmount = bcsub($discountAmount, $net, $places);
						}

						// Break out of loop if there's nothing left to discount
						if (bccomp('0', $discountAmount, $places) >= 0) {
							return;
						}
					}
				}
			} else {
				// Apply discount across each discountable price
				foreach (array_reverse(self::$tagOrder) as $discountablePriceTag) {
					if (!isset($this->discountablePrices[$discountablePriceTag])) {
						continue;
					}

					/** @var DiscountablePrice $discountablePrice */
					foreach (array_reverse($this->discountablePrices[$discountablePriceTag]) as $discountablePrice) {
						$net = $discountablePrice->getDiscountedNetPricingNet();

						if (bccomp($net, $discountAmount, $places) === 1) {
							$discountablePrice->discountNetPrice($discountAmount);
							$discountAmount = '0';
						} else {
							$discountablePrice->discountNetPrice($net);
							$discountAmount = bcsub($discountAmount, $net, $places);
						}

						// Break out of loop if there's nothing left to discount
						if (bccomp('0', $discountAmount, $places) >= 0) {
							return;
						}
					}
				}
			}
		}
	}

	/**
	 * Discount the gross price
	 *
	 * Discount the gross price. Current discounted values
	 * can be obtained through the getDiscountedGrossPricingGross
	 * method.
	 *
	 * @param string $discountAmount
	 * @param bool $reverse
	 * @throws LogicException
	 * @throws InvalidArgumentException
	 */
	public function discountGrossPrice($discountAmount, $reverse = false)
	{
		$discountAmount = (string) $discountAmount;
		$places = $this->currency->getDecimalPlaces();

		// Return quickly if the amount to be discounted is 0
		if (bccomp(0, $discountAmount, $places) >= 0) {
			return;
		}

		// Mark as immutable to prevent further changes
		$this->markFullPriceAsImmutable();

		if ($this->hasSingleTaxRate) {
			// Only a single tax rate exists, start/keep a total of the discount amounts and
			// update this as a summary
			$discountedGrossPricingGross = $this->getDiscountedGrossPricingGross();
			$discountGrossAmount = $this->getDiscountGrossAmount();

			if (bccomp($discountAmount, $discountedGrossPricingGross, $places) === 1) {
				$discountAmount = $discountedGrossPricingGross;
			}

			$this->discountGrossAmount = bcadd($discountGrossAmount, $discountAmount, $places);
			$discountedGrossPricingGross = bcsub($discountedGrossPricingGross, $discountAmount, $places);

			list ($this->discountedGrossPricingNet, $this->discountedGrossPricingTax, $this->discountedGrossPricingGross)
				= NetGrossConversion::breakdownGrossToNet($discountedGrossPricingGross, $this->initialTaxRate->getRate(), $places);
		} else {
			if ($reverse === false) {
				// Apply discount across each discountable price
				foreach (self::$tagOrder as $discountablePriceTag) {
					if (!isset($this->discountablePrices[$discountablePriceTag])) {
						continue;
					}

					/** @var DiscountablePrice $discountablePrice */
					foreach ($this->discountablePrices[$discountablePriceTag] as $discountablePrice) {
						$gross = $discountablePrice->getDiscountedGrossPricingGross();

						if (bccomp($gross, $discountAmount, $places) === 1) {
							$discountablePrice->discountGrossPrice($discountAmount);
							$discountAmount = '0';
						} else {
							$discountablePrice->discountGrossPrice($gross);
							$discountAmount = bcsub($discountAmount, $gross, $places);
						}

						// Break out of loop if there's nothing left to discount
						if (bccomp('0', $discountAmount, $places) >= 0) {
							return;
						}
					}
				}
			} else {
				// Apply discount across each discountable price
				foreach (array_reverse(self::$tagOrder) as $discountablePriceTag) {
					if (!isset($this->discountablePrices[$discountablePriceTag])) {
						continue;
					}

					/** @var DiscountablePrice $discountablePrice */
					foreach (array_reverse($this->discountablePrices[$discountablePriceTag]) as $discountablePrice) {
						$gross = $discountablePrice->getDiscountedGrossPricingGross();

						if (bccomp($gross, $discountAmount, $places) === 1) {
							$discountablePrice->discountGrossPrice($discountAmount);
							$discountAmount = '0';
						} else {
							$discountablePrice->discountGrossPrice($gross);
							$discountAmount = bcsub($discountAmount, $gross, $places);
						}

						// Break out of loop if there's nothing left to discount
						if (bccomp('0', $discountAmount, $places) >= 0) {
							return;
						}
					}
				}
			}
		}
	}

	/**
	 * Get the total cost
	 *
	 * A discountable tracks internal cost of the associated prices
	 * added to the discountable price. This method will return
	 * all the individual costs as one total.
	 *
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getTotalCost(array $tag = null)
	{
		return $this->calculateTotal('getTotalCost', FinancialPrecision::COST_PLACES, $tag);
	}

	/**
	 * Get the total weight
	 *
	 * A discountable tracks the weight of the associated prices
	 * added to the discountable price. This method will return
	 * all the individual weights as one total.
	 *
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getTotalWeight(array $tag = null)
	{
		return $this->calculateTotal('getTotalWeight', FinancialPrecision::WEIGHT_PLACES, $tag);
	}

	/**
	 * Get the full net value
	 *
	 * Returns the full net value of all prices added to the
	 * discountable, regardless of any discounts that may have
	 * been applied.
	 *
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getFullNet(array $tag = null)
	{
		$places = $this->currency->getDecimalPlaces();
		return $this->calculateTotal('getFullNet', $places, $tag);
	}

	/**
	 * Get the full tax value
	 *
	 * Returns the full tax value of all prices added to the
	 * discountable, regardless of any discounts that may have
	 * been applied.
	 *
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getFullTax(array $tag = null)
	{
		$places = $this->currency->getDecimalPlaces();
		return $this->calculateTotal('getFullTax', $places, $tag);
	}

	/**
	 * Get the full gross value
	 *
	 * Returns the full gross value of all prices added to the
	 * discountable, regardless of any discounts that may have
	 * been applied.
	 *
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getFullGross(array $tag = null)
	{
		$places = $this->currency->getDecimalPlaces();
		return $this->calculateTotal('getFullGross', $places, $tag);
	}

	/**
	 * Get the full net or gross value
	 *
	 * Returns the full net or gross value of all prices added
	 * to the discountable, regardless of any discounts that
	 * may have been applied, based on the $isShowPricesWithTax
	 * parameter passed. If the parameter is false, the net is
	 * returned. If the parameter is true, the gross is returned.
	 *
	 * @param bool $isShowPricesWithTax
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getFullSell($isShowPricesWithTax, array $tag = null)
	{
		return $isShowPricesWithTax
			? $this->getFullGross($tag)
			: $this->getFullNet($tag)
		;
	}

	/**
	 * Get the amount discounted from the net
	 *
	 * Return the amount discounted from the net when using
	 * net pricing. If no discounts have yet to be made, the
	 * value returned will be 0.
	 *
	 * @return string
	 */
	public function getDiscountNetAmount()
	{
		return $this->discountNetAmount;
	}

	/**
	 * Get the discounted net pricing net value
	 *
	 * Returns the discounted net value for net pricing.
	 * This value is the net price with discounts applied,
	 * resulting in recalculation of the tax and gross.
	 *
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getDiscountedNetPricingNet(array $tag = null)
	{
		if (null !== $this->discountedNetPricingNet && null === $tag) {
			return $this->discountedNetPricingNet;
		} else {
			$places = $this->currency->getDecimalPlaces();
			return $this->calculateTotal('getDiscountedNetPricingNet', $places, $tag);
		}
	}

	/**
	 * Get the discounted net pricing tax value
	 *
	 * Returns the discounted tax value for net pricing.
	 * This value is the tax value with discounts applied
	 * to the net, resulting in the recalculating of the tax
	 * price.
	 *
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getDiscountedNetPricingTax(array $tag = null)
	{
		if (null !== $this->discountedNetPricingTax && null === $tag) {
			return $this->discountedNetPricingTax;
		} else {
			$places = $this->currency->getDecimalPlaces();
			return $this->calculateTotal('getDiscountedNetPricingTax', $places, $tag);
		}
	}

	/**
	 * Get the discounted net pricing gross value
	 *
	 * Returns the discounted gross value for net pricing.
	 * This value is the gross value with discounts applied
	 * to the net, resulting in the recalculation of the gross
	 * price.
	 *
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getDiscountedNetPricingGross(array $tag = null)
	{
		if (null !== $this->discountedNetPricingGross && null === $tag) {
			return $this->discountedNetPricingGross;
		} else {
			$places = $this->currency->getDecimalPlaces();
			return $this->calculateTotal('getDiscountedNetPricingGross', $places, $tag);
		}
	}

	/**
	 * Get the discounted net pricing net or gross value
	 *
	 * Returns the discounted net or gross value for net pricing.
	 * This value is the net or gross value with discounts applied,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the net is returned. If the
	 * parameter is true, the gross is returned.
	 *
	 * @param bool $isShowPricesWithTax
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getDiscountedNetPricingSell($isShowPricesWithTax, array $tag = null)
	{
		return $isShowPricesWithTax
			? $this->getDiscountedNetPricingGross($tag)
			: $this->getDiscountedNetPricingNet($tag)
		;
	}

	/**
	 * Get the amount discounted from the gross
	 *
	 * Return the amount discounted from the gross when using
	 * gross pricing. If no discounts have yet to be made, the
	 * value returned will be 0.
	 *
	 * @return string
	 */
	public function getDiscountGrossAmount()
	{
		return $this->discountGrossAmount;
	}

	/**
	 * Get the discounted gross pricing net value
	 *
	 * Returns the discounted net value for gross pricing.
	 * This value is the net value with discounts applied
	 * to the gross, resulting in the recalculation of the net
	 * price.
	 *
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getDiscountedGrossPricingNet(array $tag = null)
	{
		if (null !== $this->discountedGrossPricingNet && null === $tag) {
			return $this->discountedGrossPricingNet;
		} else {
			$places = $this->currency->getDecimalPlaces();
			return $this->calculateTotal('getDiscountedGrossPricingNet', $places, $tag);
		}
	}

	/**
	 * Get the discounted gross pricing tax value
	 *
	 * Returns the discounted tax value for gross pricing.
	 * This value is the tax value with discounts applied
	 * to the gross, resulting in the recalculating of the tax
	 * price.
	 *
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getDiscountedGrossPricingTax(array $tag = null)
	{
		if (null !== $this->discountedGrossPricingTax && null === $tag) {
			return $this->discountedGrossPricingTax;
		} else {
			$places = $this->currency->getDecimalPlaces();
			return $this->calculateTotal('getDiscountedGrossPricingTax', $places, $tag);
		}
	}

	/**
	 * Get the discounted gross pricing gross value
	 *
	 * Get the discounted gross value for gross pricing.
	 * This value is the gross price with discounts applied,
	 * resulting in recalculation of the tax and net.
	 *
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getDiscountedGrossPricingGross(array $tag = null)
	{
		if (null !== $this->discountedGrossPricingGross && null === $tag) {
			return $this->discountedGrossPricingGross;
		} else {
			$places = $this->currency->getDecimalPlaces();
			return $this->calculateTotal('getDiscountedGrossPricingGross', $places, $tag);
		}
	}

	/**
	 * Get the discounted gross pricing net or gross value
	 *
	 * Returns the discounted net or gross value for gross pricing.
	 * This value is the net or gross value with discounts applied,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the net is returned. If the
	 * parameter is true, the gross is returned.
	 *
	 * @param bool $isShowPricesWithTax
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getDiscountedGrossPricingSell($isShowPricesWithTax, array $tag = null)
	{
		return $isShowPricesWithTax
			? $this->getDiscountedGrossPricingGross($tag)
			: $this->getDiscountedGrossPricingNet($tag)
		;
	}

	/**
	 * Get the discounted net or gross pricing tax value
	 *
	 * Returns the tax value for net or gross pricing, based on the
	 * $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the tax for net pricing is returned.
	 * If the parameter is true, the tax for gross pricing is returned.
	 *
	 * @param bool $isShowPricesWithTax
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getDiscountedTax($isShowPricesWithTax, array $tag = null)
	{
		return $isShowPricesWithTax
			? $this->getDiscountedGrossPricingTax($tag)
			: $this->getDiscountedNetPricingTax($tag)
		;
	}

	/**
	 * Get the discount amount for net or gross pricing.
	 *
	 * Returns the discount amount for net or gross pricing,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the net pricing discount
	 * amount is returned. If the parameter is true, the gross
	 * pricing discount amount is returned.
	 *
	 * @param bool $isShowPricesWithTax
	 * @return string
	 */
	public function getDiscountSellAmount($isShowPricesWithTax)
	{
		return $isShowPricesWithTax
			? $this->getDiscountGrossAmount()
			: $this->getDiscountNetAmount()
			;
	}

	/**
	 * Get the discounted net or gross value for net or gross pricing
	 *
	 * Returns the discounted net or gross value for net or gross pricing,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the net is returned. If the
	 * parameter is true, the gross is returned.
	 *
	 * @param bool $isShowPricesWithTax
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getDiscountedSell($isShowPricesWithTax, array $tag = null)
	{
		return $isShowPricesWithTax
			? $this->getDiscountedGrossPricingGross($tag)
			: $this->getDiscountedNetPricingNet($tag)
		;
	}

	/**
	 * Get the discounted net value for net or gross pricing
	 *
	 * Returns the discounted net value for net or gross pricing,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the net pricing net is returned.
	 * If the parameter is true, the gross pricing net is returned.
	 *
	 * @param bool $isShowPricesWithTax
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getDiscountedNet($isShowPricesWithTax, array $tag = null)
	{
		return $isShowPricesWithTax
			? $this->getDiscountedGrossPricingNet($tag)
			: $this->getDiscountedNetPricingNet($tag)
			;
	}

	/**
	 * Get the discounted gross value for net or gross pricing
	 *
	 * Returns the discounted gross value for net or gross pricing,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the net pricing gross is returned.
	 * If the parameter is true, the gross pricing gross is returned.
	 *
	 * @param bool $isShowPricesWithTax
	 * @param string[]|null $tag
	 * @return string
	 */
	public function getDiscountedGross($isShowPricesWithTax, array $tag = null)
	{
		return $isShowPricesWithTax
			? $this->getDiscountedGrossPricingGross($tag)
			: $this->getDiscountedNetPricingGross($tag)
		;
	}

	/**
	 * Calculate the total of something in the composed discountable
	 * instances using the supplied method name
	 *
	 * Iterates over each of the discountable prices by tag name within
	 * the composite and calls the method name given in the $methodName
	 * parameter, taking the result and adding it to a total before
	 * returning that total.
	 *
	 * @param string $methodName
	 * @param int $places
	 * @param string[]|null $tags
	 * @return string
	 */
	private function calculateTotal($methodName, $places, array $tags = null)
	{
		$compositeTotal = '0';
		$tags = $tags === null ? array_keys($this->discountablePrices) : $tags;

		foreach ($tags as $tag) {
			if (!isset($this->discountablePrices[$tag])) {
				continue;
			}

			foreach ($this->discountablePrices[$tag] as $price) {
				$compositeTotal = bcadd($compositeTotal, call_user_func([$price, $methodName]), $places);
			}
		}

		return $compositeTotal;
	}
}
