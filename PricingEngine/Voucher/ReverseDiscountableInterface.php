<?php

namespace PricingEngine\Voucher;

use InvalidArgumentException;
use LogicException;

/**
 * Discountable entity for reversing discounts
 *
 * An entity that can be discounted with the discounts
 * disbursed across the discountable entity in reverse.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
interface ReverseDiscountableInterface extends DiscountableInterface
{
	/**
	 * Discount the net price, in reverse
	 *
	 * Supply new discounted net pricing gross values.
	 *
	 * Current discounted values can be obtained through the
	 * getDiscountedNetPricingNet method.
	 *
	 * @param string $discountedPrice
	 * @throws LogicException
	 * @throws InvalidArgumentException
	 */
	public function discountNetPriceReverse($discountedPrice);

	/**
	 * Discount the gross price, in reverse
	 *
	 * Supply new discounted gross pricing gross values.
	 *
	 * Current discounted values can be obtained through the
	 * getDiscountedGrossPricingGross method.
	 *
	 * @param string $discountedPrice
	 * @throws LogicException
	 * @throws InvalidArgumentException
	 */
	public function discountGrossPriceReverse($discountedPrice);
}
