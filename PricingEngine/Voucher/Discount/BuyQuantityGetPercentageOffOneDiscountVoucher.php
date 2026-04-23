<?php

namespace PricingEngine\Voucher\Discount;

use PricingEngine\BCMath;
use PricingEngine\Enum\FinancialPrecision;
use PricingEngine\Enum\Voucher\DiscountMethod;
use PricingEngine\Voucher\VoucherContext;
use PricingEngine\OrderLineInterface;

/**
 * Buy Quantity, get the percentage off one voucher
 *
 * When a quantity is purchased, takes the percentage off
 * value of the voucher from the items in the specific
 * order line.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class BuyQuantityGetPercentageOffOneDiscountVoucher extends AbstractProductDiscountVoucher
{
	/**
	 * Override the running price by calculating
	 * the discount value to take off one item
	 * if the minimum quantity is met
	 *
	 * Applies the discount by calculating the
	 * number of discountable items to allow and
	 * deducting the percentage off to each of those
	 * discountable items. The discount is deduced
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

		// Calculate and apply the deductions to make for the percentage off quantity
		if ($discountQuantity > 0) {
			$places = $discountable->getPlaces();

			$discountValue = bcmul($discountQuantity, $discountable->getNetUnit(), FinancialPrecision::PLACES);
			$discountValue = bcmul($discountValue, bcdiv($this->value, 100, FinancialPrecision::PLACES), FinancialPrecision::PLACES);
			$discountValue = BCMath::round($discountValue, $places);
			$discountable->discountNetPrice($discountValue);

			$discountValue = bcmul($discountQuantity, $discountable->getGrossUnit(), FinancialPrecision::PLACES);
			$discountValue = bcmul($discountValue, bcdiv($this->value, 100, FinancialPrecision::PLACES), FinancialPrecision::PLACES);
			$discountValue = BCMath::round($discountValue, $places);
			$discountable->discountGrossPrice($discountValue);

			if (in_array($this->discountMethod, [
				DiscountMethod::HIGHEST_PRICED_MATCHING_LINE,
				DiscountMethod::LOWEST_PRICED_MATCHING_LINE])) {
				$discountable->setVoucherName($this->voucherName);
			}
		}
	}
}
