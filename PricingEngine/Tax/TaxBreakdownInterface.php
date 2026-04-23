<?php

namespace PricingEngine\Tax;

/**
 * Tax breakdown interface
 *
 * Tracks total net and tax values for each supplied
 * tax code.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
interface TaxBreakdownInterface
{
	/**
	 * Record the net and tax amounts for the given tax rate
	 * in the tax breakdown
	 *
	 * Record the net and tax monetary values within the tax
	 * breakdown for the given tax rate instance supplied.
	 * The tax rate code must already be recognised in the
	 * the breakdown for the values to be recorded.
	 *
	 * @param TaxRateInterface $taxRate
	 * @param string $net
	 * @param string $tax
	 */
	public function recordTaxInBreakdown(TaxRateInterface $taxRate, $net, $tax);

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
	public function hasSingleTaxRate();
}
