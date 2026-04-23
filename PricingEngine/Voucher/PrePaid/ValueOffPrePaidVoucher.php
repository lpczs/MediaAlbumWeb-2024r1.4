<?php

namespace PricingEngine\voucher\PrePaid;

use PricingEngine\Enum\Voucher\VoucherType;
use PricingEngine\Voucher\Discount\ValueOffDiscountVoucher;

/**
 * Pre-paid Value off voucher
 *
 * Deduct the voucher value as a monetary amount
 * from the value of matching order lines.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class ValueOffPrePaidVoucher extends ValueOffDiscountVoucher
{
	/**
	 * Get voucher type
	 *
	 * Get the voucher type for this voucher implementation.
	 * Specifically, the pre paid voucher type.
	 *
	 * @return string
	 */
	protected function getVoucherType()
	{
		return VoucherType::PRE_PAID;
	}
}
