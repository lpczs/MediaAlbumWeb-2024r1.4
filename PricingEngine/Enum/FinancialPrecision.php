<?php

namespace PricingEngine\Enum;

/**
 * Financial precision enumeration
 *
 * Constants relevant to financial calculation
 * precision.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class FinancialPrecision
{
	/**
	 * The decimal precision to apply for any monetary
	 * calculations before rounding to the customers currency
	 * decimal places.
	 */
	const PLACES = 10;

	/**
	 * The decimal precision to round to for any cost calculations.
	 */
	const COST_PLACES = 4;

	/**
	 * The decimal precision to round to for any weight calculations.
	 */
	const WEIGHT_PLACES = 4;
}
