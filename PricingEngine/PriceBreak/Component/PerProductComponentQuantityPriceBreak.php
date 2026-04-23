<?php

namespace PricingEngine\PriceBreak\Component;

use PricingEngine\PriceBreak\AbstractPriceBreak;

/**
 * Per component & product quantity
 *
 * Implements price breaks for per component & product
 * quantity pricing, encompassing product start/end
 * quantity and component start/end quantity.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class PerProductComponentQuantityPriceBreak extends AbstractPriceBreak
{
	/**
	 * @var int
	 */
	private $startComponentQuantity;

	/**
	 * @var int
	 */
	private $endComponentQuantity;

	/**
	 * Constructor
	 *
	 * @param int $startQuantity
	 * @param int $endQuantity
	 * @param int $startComponentQuantity
	 * @param int $endComponentQuantity
	 * @param string $base
	 * @param string $unit
	 * @param string $lineSubtract
	 */
	public function __construct($startQuantity, $endQuantity, $startComponentQuantity, $endComponentQuantity, $base, $unit, $lineSubtract)
	{
		$this->startComponentQuantity = (int)$startComponentQuantity;
		$this->endComponentQuantity = (int)$endComponentQuantity;

		parent::__construct($startQuantity, $endQuantity, $base, $unit, $lineSubtract);
	}

	/**
	 * Get the start component quantity
	 *
	 * Get the start component quantity for the price break.
	 *
	 * @return int
	 */
	public function getStartComponentQuantity()
	{
		return $this->startComponentQuantity;
	}

	/**
	 * Get the end component quantity.
	 *
	 * Get the end component quantity for the price break.
	 *
	 * @return int
	 */
	public function getEndComponentQuantity()
	{
		return $this->endComponentQuantity;
	}
}
