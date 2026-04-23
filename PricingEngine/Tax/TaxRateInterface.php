<?php

namespace PricingEngine\Tax;

/**
 * Tax rate interface
 *
 * Representation of a tax rate, including
 * its code and rate, and optionally its
 * name/description.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
interface TaxRateInterface
{
	/**
	 * Get code
	 *
	 * Get the tax code.
	 *
	 * @return string
	 */
	public function getCode();

	/**
	 * Get name
	 *
	 * Get the tax name.
	 *
	 * @return string|null
	 */
	public function getName();

	/**
	 * Get rate
	 *
	 * Get the tax rate, as a decimal
	 * represented string.
	 *
	 * @return string
	 */
	public function getRate();
}
