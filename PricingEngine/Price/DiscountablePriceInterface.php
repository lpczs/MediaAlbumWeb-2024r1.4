<?php

namespace PricingEngine\Price;

use InvalidArgumentException;
use LogicException;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Discountable price interface
 *
 * Represents totals of cost, weight, net, tax and
 * gross values, and allows discounting of the net,
 * tax and gross by net or gross pricing.
 *
 * Once a discount has been applied, the full price
 * cannot be changed as the full price is marked
 * immutable.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
interface DiscountablePriceInterface
{
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
	public function isFullPriceImmutable();

	/**
	 * Mark the full price as immutable to prevent further changes
	 *
	 * Once the full price is marked as immutable, the full price can no
	 * longer be added to. This is used in conjunction with
	 * discounting. If the full price is discounted by a relative
	 * amount (e.g. a percentage) then the price should not be changed
	 * in order to protected it from modification.
	 */
	public function markFullPriceAsImmutable();

	/**
	 * Get the tax rate of the discountable price
	 *
	 * Returns an implementation of the TaxRateInterface interface,
	 * that makes up the underlying tax rate of the prices added to
	 * the discountable price.
	 *
	 * @return TaxRateInterface
	 */
	public function getTaxRate();

	/**
	 * Compare the given TaxRateInterface implementation against
	 * discountable's own tax rate
	 *
	 * Compares the given TaxRateInterface implementation against
	 * the discountable's own tax rate and returns true or false,
	 * depending on whether the rates match.
	 *
	 * @param TaxRateInterface $taxRate
	 * @return bool
	 */
	public function compareTaxRate(TaxRateInterface $taxRate);

	/**
	 * Check if the pricing for the discountable is made
	 * up of a single tax rate
	 *
	 * Return whether the pricing for the discountable
	 * is made up of a single tax rate, or if the price is made
	 * up of multiple tax rates. Returns true or false, depending
	 * whether a single tax rate is used.
	 *
	 * @return bool
	 */
	public function hasSingleTaxRate();

	/**
	 * Discount the net price
	 *
	 * Discount the net price. Current discounted values
	 * can be obtained through the getDiscountedNetPricingNet
	 * method.
	 *
	 * @param string $discountAmount
	 * @return string
	 * @throws LogicException
	 * @throws InvalidArgumentException
	 */
	public function discountNetPrice($discountAmount);

	/**
	 * Discount the gross price
	 *
	 * Discount the gross price. Current discounted values
	 * can be obtained through the getDiscountedGrossPricingGross
	 * method.
	 *
	 * @param string $discountAmount
	 * @return string
	 * @throws LogicException
	 * @throws InvalidArgumentException
	 */
	public function discountGrossPrice($discountAmount);

	/**
	 * Get the total cost
	 *
	 * A discountable tracks internal cost of the associated prices
	 * added to the discountable price. This method will return
	 * all the individual costs as one total.
	 *
	 * @return string
	 */
	public function getTotalCost();

	/**
	 * Get the total weight
	 *
	 * A discountable tracks the weight of the associated prices
	 * added to the discountable price. This method will return
	 * all the individual weights as one total.
	 *
	 * @return string
	 */
	public function getTotalWeight();

	/**
	 * Get the full net value
	 *
	 * Returns the full net value of all prices added to the
	 * discountable, regardless of any discounts that may have
	 * been applied.
	 *
	 * @return string
	 */
	public function getFullNet();

	/**
	 * Get the full tax value
	 *
	 * Returns the full tax value of all prices added to the
	 * discountable, regardless of any discounts that may have
	 * been applied.
	 *
	 * @return string
	 */
	public function getFullTax();

	/**
	 * Get the full gross value
	 *
	 * Returns the full gross value of all prices added to the
	 * discountable, regardless of any discounts that may have
	 * been applied.
	 *
	 * @return string
	 */
	public function getFullGross();

	/**
	 * Get the full net or gross value
	 *
	 * Returns the full net or gross value of all prices added
	 * to the discountable, regardless of any discounts that
	 * may have been applied, based on the $isShowPricesWithTax
	 * parameter passed. If the parameter is false, the net is
	 * returned. If the parameter is true, the gross is returned.
	 *
	 * @param string $isShowPricesWithTax
	 * @return string
	 */
	public function getFullSell($isShowPricesWithTax);

	/**
	 * Get the amount discounted from the net
	 *
	 * Return the amount discounted from the net when using
	 * net pricing. If no discounts have yet to be made, the
	 * value returned will be 0.
	 *
	 * @return string
	 */
	public function getDiscountNetAmount();

	/**
	 * Get the discounted net pricing net value
	 *
	 * Returns the discounted net value for net pricing.
	 * This value is the net price with discounts applied,
	 * resulting in recalculation of the tax and gross.
	 *
	 * @return string
	 */
	public function getDiscountedNetPricingNet();

	/**
	 * Get the discounted net pricing tax value
	 *
	 * Returns the discounted tax value for net pricing.
	 * This value is the tax value with discounts applied
	 * to the net, resulting in the recalculating of the tax
	 * price.
	 *
	 * @return string
	 */
	public function getDiscountedNetPricingTax();

	/**
	 * Get the discounted net pricing gross value
	 *
	 * Returns the discounted gross value for net pricing.
	 * This value is the gross value with discounts applied
	 * to the net, resulting in the recalculation of the gross
	 * price.
	 *
	 * @return string
	 */
	public function getDiscountedNetPricingGross();

	/**
	 * Get the discounted net pricing net or gross value
	 *
	 * Returns the discounted net or gross value for net pricing.
	 * This value is the net or gross value with discounts applied,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the net is returned. If the
	 * parameter is true, the gross is returned.
	 *
	 * @param string $isShowPricesWithTax
	 * @return string
	 */
	public function getDiscountedNetPricingSell($isShowPricesWithTax);

	/**
	 * Get the amount discounted from the gross
	 *
	 * Return the amount discounted from the gross when using
	 * gross pricing. If no discounts have yet to be made, the
	 * value returned will be 0.
	 *
	 * @return string
	 */
	public function getDiscountGrossAmount();

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
	public function getDiscountSellAmount($isShowPricesWithTax);

	/**
	 * Get the discounted gross pricing net value
	 *
	 * Returns the discounted net value for gross pricing.
	 * This value is the net value with discounts applied
	 * to the gross, resulting in the recalculation of the net
	 * price.
	 *
	 * @return string
	 */
	public function getDiscountedGrossPricingNet();

	/**
	 * Get the discounted gross pricing tax value
	 *
	 * Returns the discounted tax value for gross pricing.
	 * This value is the tax value with discounts applied
	 * to the gross, resulting in the recalculating of the tax
	 * price.
	 *
	 * @return string
	 */
	public function getDiscountedGrossPricingTax();

	/**
	 * Get the discounted gross pricing gross value
	 *
	 * Get the discounted gross value for gross pricing.
	 * This value is the gross price with discounts applied,
	 * resulting in recalculation of the tax and net.
	 *
	 * @return string
	 */
	public function getDiscountedGrossPricingGross();

	/**
	 * Get the discounted gross pricing net or gross value
	 *
	 * Returns the discounted net or gross value for gross pricing.
	 * This value is the net or gross value with discounts applied,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the net is returned. If the
	 * parameter is true, the gross is returned.
	 *
	 * @param string $isShowPricesWithTax
	 * @return string
	 */
	public function getDiscountedGrossPricingSell($isShowPricesWithTax);

	/**
	 * Get the discounted net or gross pricing tax value
	 *
	 * Returns the tax value for net or gross pricing, based on the
	 * $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the tax for net pricing is returned.
	 * If the parameter is true, the tax for gross pricing is returned.
	 *
	 * @param string $isShowPricesWithTax
	 * @return string
	 */
	public function getDiscountedTax($isShowPricesWithTax);

	/**
	 * Get the discounted net or gross value for net or gross pricing
	 *
	 * Returns the discounted net or gross value for net or gross pricing,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the net is returned. If the
	 * parameter is true, the gross is returned.
	 *
	 * @param string $isShowPricesWithTax
	 * @return string
	 */
	public function getDiscountedSell($isShowPricesWithTax);

	/**
	 * Get the discounted net value for net or gross pricing
	 *
	 * Returns the discounted net value for net or gross pricing,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, net pricing net is returned. If the
	 * parameter is true, gross pricing net is returned.
	 *
	 * @param bool $isShowPricesWithTax
	 * @return string
	 */
	public function getDiscountedNet($isShowPricesWithTax);

	/**
	 * Get the discounted gross value for net or gross pricing
	 *
	 * Returns the discounted net value for net or gross pricing,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, net pricing net is returned. If the
	 * parameter is true, gross pricing net is returned.
	 *
	 * @param bool $isShowPricesWithTax
	 * @return string
	 */
	public function getDiscountedGross($isShowPricesWithTax);

	/**
	 * Reset the net discount to full
	 */
	public function resetNetDiscountToFull();

	/**
	 * Reset the gross discount to full
	 */
	public function resetGrossDiscountToFull();
}
