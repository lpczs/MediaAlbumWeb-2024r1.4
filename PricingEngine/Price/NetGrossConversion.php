<?php

namespace PricingEngine\Price;

use PricingEngine\BCMath;
use PricingEngine\Enum\FinancialPrecision;

/**
 * Net/gross conversion
 *
 * Converts a net to gross and gross to net,
 * using the supplied tax rate and rounding
 * precision.
 *
 * Includes functions to simply convert a net to
 * gross or gross to net, or to provide the full
 * net, tax and gross for the given net or gross
 * values.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class NetGrossConversion
{
	/**
	 * Net, tax and gross price breakdown from net.
	 *
	 * Break a net price down to a gross price using the supplied tax rate
	 * and rounding to the required number of places, returning all three
	 * values.
	 *
	 * @param string $price
	 * @param string $taxRate
	 * @param int $places
	 * @return string[]
	 */
	public static function breakdownNetToGross($price, $taxRate, $places)
	{
		$net = BCMath::round($price, $places);
		$tax = BCMath::round(bcmul($net, bcdiv($taxRate, 100, FinancialPrecision::PLACES),
			FinancialPrecision::PLACES), $places);
		$gross = bcadd($net, $tax, $places);

		if (bccomp($net, '0', $places) === -1) {
			$net = '0';
		}

		if (bccomp($tax, '0', $places) === -1) {
			$tax = '0';
		}

		if (bccomp($gross, '0', $places) === -1) {
			$gross = '0';
		}

		return [$net, $tax, $gross];
	}

	/**
	 * Conversion of a net price to a gross price
	 *
	 * Convert a net price to a gross price using the supplied tax rate, returning
	 * the new gross price.
	 *
	 * @param string $price
	 * @param string $taxRate
	 * @param int $places
	 * @return string
	 */
	public static function convertNetToGross($price, $taxRate, $places)
	{
		return self::breakdownNetToGross($price, $taxRate, $places)[2];
	}

	/**
	 * Net, tax and gross price breakdown from gross.
	 *
	 * Breakdown a gross price down to a net price using the supplied rate
	 * and rounding to the required number of places returning all three
	 * values.
	 *
	 * @param string $price
	 * @param string $taxRate
	 * @param int $places
	 * @return string[]
	 */
	public static function breakdownGrossToNet($price, $taxRate, $places)
	{
		$gross = BCMath::round($price, $places);
		$net = BCMath::round(bcdiv($gross, bcadd(1, bcdiv($taxRate, 100, FinancialPrecision::PLACES),
			FinancialPrecision::PLACES), FinancialPrecision::PLACES), $places);
		$tax = bcsub($gross, $net, $places);

		if (bccomp($net, '0', $places) === -1) {
			$net = '0';
		}

		if (bccomp($tax, '0', $places) === -1) {
			$tax = '0';
		}

		if (bccomp($gross, '0', $places) === -1) {
			$gross = '0';
		}

		return [$net, $tax, $gross];
	}

	/**
	 * Conversion of a gross price to a net price
	 *
	 * Convert a gross price to a net price using the supplied tax rate, returning
	 * the new net price.
	 *
	 * @param string $price
	 * @param string $taxRate
	 * @param int $places
	 * @return string
	 */
	public static function convertGrossToNet($price, $taxRate, $places)
	{
		return self::breakdownGrossToNet($price, $taxRate, $places)[0];
	}
}
