<?php

namespace PricingEngine;

use PricingEngine\Enum\FinancialPrecision;

/**
 * Additional BCMath functionality not provided in the
 * standard PHP extension.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class BCMath
{
	/**
	 * Round $value to $places using bankers rounding (round half even)
	 *
	 * Round $value to $places using bankers rounding (round half even)
	 * and using the PHP bcmath extension functions to ensure high precision
	 * calculations are used and that the result is a string.
	 *
	 * @param string $value
	 * @param int $places
	 * @return string
	 */
	public static function round($value, $places)
	{
		// Ensure correct types have been passed
		$value = (string) $value;
		$places = (int) $places;
		
		if (! is_numeric($value))
		{
			// if a malformed string is passed to bcmath the pricing engine expects a 0 to be returned
			// from php 7.4 onwards this causes an error so handle this properly
			error_log("Malformed Value: " . $value . " passed to BCMath Round");
			return 0;
		}

		// Cleanup formatting
		if (isset($value[0])) {
			if ($value[0] === '.') {
				$value = '0' . $value;
			} elseif ($value[0] === '-' && isset($value[1]) && $value[1] === '.') {
				$value = '-0.' . substr($value, 2);
			}
		}

		// Return early if there's no fraction, making sure to pad to $places
		if (($decimalPosition = strpos($value, '.')) === false) {
			return bcadd($value, 0, $places);
		}

		// Test value passed for conditions
		$isNegative = '-' === $value[0];
		$leftDigit = ($places === 0) ? $decimalPosition - 1 : $decimalPosition + $places;
		$roundingDigit = $decimalPosition + $places + 1;
		$roundingDigits = isset($value[$roundingDigit]) ? substr($value, $roundingDigit, strlen($value) - $roundingDigit) : '0';

		$cmp = bccomp('0.' . $roundingDigits, '0.5', FinancialPrecision::PLACES);
		$isLeftDigitOdd = isset($value[$leftDigit]) ? (int)$value[$leftDigit] % 2 !== 0 : true;

		// Select approach to apply rounding
		if ($cmp === 1 || ($cmp === 0 && $isLeftDigitOdd)) {
			return $isNegative
				? bcsub($value, '0.' . str_repeat('0', $places) . '5', $places)
				: bcadd($value, '0.' . str_repeat('0', $places) . '5', $places);
		} else {
			return bcsub($value, 0, $places);
		}
	}

	/**
	 * Round $value down to $places
	 *
	 * Round $value down to $places using PHP bcmath extension functions to ensure
	 * high precision calculations are used and that the result is a string.
	 *
	 * @param $value
	 * @param int $places
	 * @return string
	 */
	public static function floor($value, $places = 0)
	{
		// Ensure correct types have been passed
		$value = (string) $value;
		$places = (int) $places;

		if (! is_numeric($value))
		{
			// if a malformed string is passed to bcmath the pricing engine expects a 0 to be returned
			// from php 7.4 onwards this causes an error so handle this properly
			error_log("Malformed Value: " . $value . " passed to BCMath Floor");
			return 0;
		}
		
		// Cleanup formatting
		if (isset($value[0])) {
			if ($value[0] === '.') {
				$value = '0' . $value;
			} elseif ($value[0] === '-' && isset($value[1]) && $value[1] === '.') {
				$value = '-0.' . substr($value, 2);
			}
		}

		// Return early if there's no fraction, making sure to pad to $places
		if (($decimalPosition = strpos($value, '.')) === false) {
			return bcadd($value, 0, $places);
		}

		$isNegative = '-' === $value[0];
		$roundingDigit = $decimalPosition + $places + 1;
		$roundingDigit = isset($value[$roundingDigit]) ? $value[$roundingDigit] : '';
		$roundingDigit = $isNegative && '' !== $roundingDigit ? (10 - $roundingDigit) : $roundingDigit;

		return bcsub($value, '0.' . str_repeat('0', $places) . $roundingDigit, $places);			
	}
}
