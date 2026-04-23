<?php

namespace PricingEngine\Tax;

use InvalidArgumentException;

/**
 * Tax rate
 *
 * Representation of a tax rate, including
 * its code and rate, and optionally its
 * name/description.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class TaxRate implements TaxRateInterface
{
	/**
	 * @var string
	 */
	private $code;

	/**
	 * @var string|null
	 */
	private $name;

	/**
	 * @var string
	 */
	private $rate;

	/**
	 * Constructor
	 *
	 * @param string $code
	 * @param string $rate
	 * @param string|null $name
	 */
	public function __construct($code, $rate, $name = null)
	{
		if (!is_numeric($rate)) {
			throw new InvalidArgumentException('Tax rate is not numeric.');
		}

		$this->code = $code;
		$this->name = $name;
		$this->rate = (string) $rate;
	}

	/**
	 * Get code
	 *
	 * Get the tax code.
	 *
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Get name
	 *
	 * Get the tax name.
	 *
	 * @return string|null
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get rate
	 *
	 * Get the tax rate, as a decimal
	 * represented string.
	 *
	 * @return string
	 */
	public function getRate()
	{
		return $this->rate;
	}
}
