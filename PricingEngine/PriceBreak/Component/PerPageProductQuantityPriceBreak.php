<?php

namespace PricingEngine\PriceBreak\Component;

use PricingEngine\PriceBreak\AbstractPriceBreak;

/**
 * Per page & product quantity
 *
 * Implements price breaks for per page and product
 * quantity pricing, encompassing product start/end
 * quantity as well as page count start/end.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class PerPageProductQuantityPriceBreak extends AbstractPriceBreak
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
	 * Constructor
	 *
	 * @param int $startQuantity
	 * @param int $endQuantity
	 * @param int $startPageCount
	 * @param int $endPageCount
	 * @param string $base
	 * @param string $unit
	 * @param string $lineSubtract
	 */
	public function __construct($startQuantity, $endQuantity, $startPageCount, $endPageCount, $base, $unit, $lineSubtract)
	{
		$this->startPageCount = (int)$startPageCount;
		$this->endPageCount = (int)$endPageCount;

		parent::__construct($startQuantity, $endQuantity, $base, $unit, $lineSubtract);
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
}
