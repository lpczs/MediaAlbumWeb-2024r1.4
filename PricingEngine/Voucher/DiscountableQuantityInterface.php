<?php

namespace PricingEngine\Voucher;

/**
 * Discountable entity with quantity
 *
 * An entity that can be discounted that is based
 * on a quantity of items.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
interface DiscountableQuantityInterface extends DiscountableInterface
{
	/**
	 * Get the net unit price
	 *
	 * Get the unit price of the order line running
	 * price based on net. This price is calculated
	 * using the full order line net price divided
	 * by the order line item product quantity.
	 *
	 * @return string
	 */
	public function getNetUnit();

	/**
	 * Get the unrounded net unit price
	 *
	 * Get the unit price of the order line running
	 * price based on net. This price is calculated
	 * using the full order line net price divided
	 * by the order line item product quantity.
	 *
	 * @return mixed
	 */
	public function getUnRoundedNetUnit();

	/**
	 * Get the gross unit price
	 *
	 * Get the unit price of the order line running
	 * price based on gross. This price is calculated
	 * using the full order line net price divided
	 * by the order line item product quantity.
	 *
	 * @return string
	 */
	public function getGrossUnit();

	/**
	 * Get the unrounded gross unit price
	 *
	 * Get the unit price of the order line running
	 * price based on gross. This price is calculated
	 * using the full order line net price divided
	 * by the order line item product quantity.
	 *
	 * @return string
	 */
	public function getUnRoundedGrossUnit();

	/**
	 * Get the quantity of units that make up the price
	 *
	 * Get the number of units that make up the price.
	 * The quantity of units will be used to make up
	 * the unit price.
	 *
	 * @return int
	 */
	public function getProductQuantity();

	/**
	 * Get the page count for each unit
	 *
	 * Get the number of pages that is unit is made up of.
	 *
	 * @return string
	 */
	public function getPageCount();
}
