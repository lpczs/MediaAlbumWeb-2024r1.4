<?php

namespace PricingEngine\Voucher\Discount;

use PricingEngine\BCMath;
use PricingEngine\Enum\FinancialPrecision;
use PricingEngine\Enum\Voucher\DiscountMethod;
use PricingEngine\Voucher\VoucherContext;
use PricingEngine\OrderLineInterface;

/**
 * Buy quantity, get one free of charge voucher
 *
 * When a quantity is purchased, deduct the value of an item
 * from the items so that item is free of charge for all matching
 * order lines
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class BuyQuantityGetOneFreeDiscountVoucher extends AbstractProductDiscountVoucher
{
	/**
	 * Override the running price by calculating
	 * the discount value to take off one item
	 * if the minimum quantity is met
	 *
	 * Applies the discount by calculating the unit price of each item
	 * against the number of of discountable items and deducting this
	 * from the running price.
	 *
	 * @param VoucherContext $voucherContext
	 * @param OrderLineInterface $discountable
	 */
	protected function applyProductDiscount(VoucherContext $voucherContext, OrderLineInterface $discountable)
	{
		// Calculate the number of discountable items
		$discountQuantity = floor($discountable->getProductQuantity() / ($this->minimumOrderQuantity + 1));
		$discountQuantity = min($discountQuantity, $voucherContext->getMaxDiscountQuantity());

		// Calculate and apply the deductions necessary for the free quantity calculated
		if ($discountQuantity > 0) {
			$places = $discountable->getPlaces();

			$discountValue = BCMath::round(bcmul($discountable->getUnRoundedNetUnit(), $discountQuantity, FinancialPrecision::PLACES), $places);
			$discountable->discountNetPrice($discountValue);

			$discountValue = BCMath::round(bcmul($discountable->getUnRoundedGrossUnit(), $discountQuantity, FinancialPrecision::PLACES), $places);
			$discountable->discountGrossPrice($discountValue);

			if (in_array($this->discountMethod, [
				DiscountMethod::HIGHEST_PRICED_MATCHING_LINE,
				DiscountMethod::LOWEST_PRICED_MATCHING_LINE])) {
				$discountable->setVoucherName($this->voucherName);
			}
		}
	}
}
