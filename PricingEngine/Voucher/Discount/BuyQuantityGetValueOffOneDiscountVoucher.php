<?php

namespace PricingEngine\Voucher\Discount;

use PricingEngine\BCMath;
use PricingEngine\Enum\FinancialPrecision;
use PricingEngine\Enum\Voucher\DiscountMethod;
use PricingEngine\Voucher\VoucherContext;
use PricingEngine\OrderLineInterface;
use PricingEngine\CurrencyInterface;

/**
 * Buy Quantity, get the value off one voucher
 *
 * When a quantity is purchased, takes the monetary value
 * of the voucher off one of the items in the specific
 * order line.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class BuyQuantityGetValueOffOneDiscountVoucher extends AbstractProductDiscountVoucher
{
	public function __construct(array $options, CurrencyInterface $currency)
	{
		parent::__construct($options);
		
		if (!is_null($this->value))
		{
			$this->value = $currency->exchange($this->value);
		}
	}


	/**
	 * Override the running price by calculating
	 * the discount value to take off one item
	 * if the minimum quantity is met
	 *
	 * Applies the discount by calculating the
	 * number of discountable items to allow and
	 * apply the value off to each of those discountable
	 * items. The discount is deduced from the running
	 * price.
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

			$netUnit = $discountable->getNetUnit();
			$discountValue = bccomp($netUnit, $this->value) === -1 ? $netUnit : $this->value;
			$discountValue = BCMath::round(bcmul($discountValue, $discountQuantity, FinancialPrecision::PLACES), $places);
			$discountable->discountNetPrice($discountValue);

			$grossUnit = $discountable->getGrossUnit();
			$discountValue = bccomp($grossUnit, $this->value) === -1 ? $grossUnit : $this->value;
			$discountValue = BCMath::round(bcmul($discountValue, $discountQuantity, FinancialPrecision::PLACES), $places);
			$discountable->discountGrossPrice($discountValue);

			if (in_array($this->discountMethod, [
				DiscountMethod::HIGHEST_PRICED_MATCHING_LINE,
				DiscountMethod::LOWEST_PRICED_MATCHING_LINE])) {
				$discountable->setVoucherName($this->voucherName);
			}
		}
	}
}
