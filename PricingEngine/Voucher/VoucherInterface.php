<?php

namespace PricingEngine\Voucher;

use PricingEngine\OrderInterface;

/**
 * Voucher
 *
 * A voucher for discounting a running price
 * as part of the order. The value and approach to
 * the application of the discount is dependent
 * on the underlying class implementation.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
interface VoucherInterface
{
	/**
	 * Test if the voucher is a script voucher type
	 *
	 * Check if the voucher implementation is a script
	 * voucher type. Returns true if a script voucher
	 * type, false otherwise.
	 *
	 * @return bool
	 */
	public function isScriptVoucher();

	/**
	 * Create a voucher context for the given order
	 *
	 * Create a voucher context for the given order that
	 * will handle the storage and access to voucher state
	 * information on behalf of the voucher and the order
	 * object instances.
	 *
	 * @param OrderInterface $order
	 * @return VoucherContext
	 */
	public function createContext(OrderInterface $order);

	/**
	 * Apply the voucher discount to the discountable using the context
	 *
	 * Apply the discount logic of the voucher type to the discountable instance passed.
	 *
	 * @param VoucherContext $voucherContext
	 * @param DiscountableInterface $discountable
	 */
	public function discount(VoucherContext $voucherContext, DiscountableInterface $discountable);
}
