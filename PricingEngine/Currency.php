<?php

namespace PricingEngine;

use PricingEngine\Enum\FinancialPrecision;

/**
 * Represents a world currency
 *
 * Represents a currency of the world, providing
 * functionality for handling the currency and
 * manipulating monetary values for the currency.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class Currency implements CurrencyInterface
{
	/**
	 * @var int
	 */
	private $decimalPlaces;

	/**
	 * @var string
	 */
	private $exchangeRate;

	/**
	 * Constructor
	 *
	 * @param int $decimalPlaces
	 * @param string $exchangeRate
	 */
	public function __construct($decimalPlaces, $exchangeRate)
	{
		$this->decimalPlaces = (int) $decimalPlaces;
		$this->exchangeRate = (string) $exchangeRate;
	}

	/**
	 * Get the number decimal places used for rounding values to this currency
	 *
	 * When rounding a monetary value that will be expressed using
	 * this currency, the value must be rounded. Different currencies
	 * have different levels of decimal precision that are used on display,
	 * typically 2 or 3. This accessor returns the number of places to use.
	 *
	 * @return int
	 */
	public function getDecimalPlaces()
	{
		return $this->decimalPlaces;
	}

	/**
	 * Get the exchange rate for the currency
	 *
	 * Returns the exchange rate for the currency that would be needed
	 * to convert a monetary value from the system default to that of the
	 * currency instance.
	 *
	 * @return string
	 */
	public function getExchangeRate()
	{
		return $this->exchangeRate;
	}

	/**
	 * Use the exchange rate to convert a base system price to the price
	 * represented by this currency
	 *
	 * If the customers currency is different to the base system currency, the
	 * price must be converted. This is done using an exchange rate to convert
	 * the values accordingly.
	 *
	 * @param string $value
	 * @param int|null $places
	 * @return string
	 */
	public function exchange($value, $places = null)
	{
		if (null === $places) {
			$places = $this->decimalPlaces;
		}

		return BCMath::round(bcmul($value, $this->exchangeRate, FinancialPrecision::PLACES), $places);
	}
}
