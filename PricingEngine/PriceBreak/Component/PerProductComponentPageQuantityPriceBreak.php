<?php

namespace PricingEngine\PriceBreak\Component;

use PricingEngine\PriceBreak\AbstractPriceBreak;

/**
 * Per page, component & product quantity
 *
 * Implements price breaks for per page count, component,
 * product quantity pricing, encompassing product start/end
 * quantity, component start/end quantity and page
 * start/end count.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class PerProductComponentPageQuantityPriceBreak extends AbstractPriceBreak
{
	/**
	 * @var int
	 */
	protected $startPageCount;

	/**
	 * @var int
	 */
	protected $endPageCount;

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
	 * @param int $startQty
	 * @param int $endQty
	 * @param int $startComponentQuantity
	 * @param int $endComponentQuantity
	 * @param int $startPageCount
	 * @param int $endPageCount
	 * @param string $base
	 * @param string $unit
	 * @param string $lineSubtract
	 */
	public function __construct($startQty, $endQty, $startComponentQuantity, $endComponentQuantity, $startPageCount, $endPageCount, $base, $unit, $lineSubtract)
	{
		$this->startComponentQuantity = (int)$startComponentQuantity;
		$this->endComponentQuantity = (int)$endComponentQuantity;
		$this->startPageCount = (int)$startPageCount;
		$this->endPageCount = (int)$endPageCount;

		parent::__construct($startQty, $endQty, $base, $unit, $lineSubtract);
	}

	/**
	 * Get the start page count
	 *
	 * Get the start page count for the price break.
	 *
	 * @return int
	 */
	public function getStartPageCount()
	{
		return $this->startPageCount;
	}

	/**
	 * Get the end page count
	 *
	 * Get the end page count for the price break.
	 *
	 * @return int
	 */
	public function getEndPageCount()
	{
		return $this->endPageCount;
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
