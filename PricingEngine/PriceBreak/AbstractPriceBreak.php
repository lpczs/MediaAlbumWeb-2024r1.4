<?php

namespace PricingEngine\PriceBreak;

/**
 * Abstract price break
 *
 * Abstract price break functionality.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
abstract class AbstractPriceBreak
{
	/**
	 * @var int
	 */
	protected $startQuantity;

	/**
	 * @var int
	 */
	protected $endQuantity;

	/**
	 * @var string
	 */
	protected $base;

	/**
	 * @var string
	 */
	protected $unit;

	/**
	 * @var string
	 */
	protected $lineSubtract;

	/**
	 * Constructor
	 *
	 * @param int $startQuantity
	 * @param int $endQuantity
	 * @param string $base
	 * @param string $unit
	 * @param string $lineSubtract
	 */
	public function __construct($startQuantity, $endQuantity, $base, $unit, $lineSubtract)
	{
		$this->startQuantity = (int)$startQuantity;
		$this->endQuantity = (int)$endQuantity;
		$this->base = (string)$base;
		$this->unit = (string)$unit;
		$this->lineSubtract = (string)$lineSubtract;
	}

	/**
	 * Get the start product quantity
	 *
	 * Get the start product quantity of the price break.
	 *
	 * @return int
	 */
	public function getStartQuantity()
	{
		return $this->startQuantity;
	}

	/**
	 * Get the end product quantity
	 *
	 * Get the end product quantity of the price break.
	 *
	 * @return int
	 */
	public function getEndQuantity()
	{
		return $this->endQuantity;
	}

	/**
	 * Get base price
	 *
	 * Get the base price of the price break.
	 *
	 * @return string
	 */
	public function getBase()
	{
		return $this->base;
	}

	/**
	 * Get unit price
	 *
	 * Get the unit price of the price break.
	 *
	 * @return string
	 */
	public function getUnit()
	{
		return $this->unit;
	}

	/**
	 * Get line subtract
	 *
	 * Get the line subtract price of the price break.
	 *
	 * @return string
	 */
	public function getLineSubtract()
	{
		return $this->lineSubtract;
	}
}
