<?php

namespace PricingEngine\Voucher\Discount;

use PricingEngine\BCMath;
use PricingEngine\Enum\FinancialPrecision;
use PricingEngine\Enum\Voucher\DiscountMethod;
use PricingEngine\OrderInterface;
use PricingEngine\OrderLineInterface;
use PricingEngine\ShippingMethodInterface;
use PricingEngine\Voucher\VoucherContext;

/**
 * Free of charge voucher
 *
 * Override the running price with a
 * value of 0 (no charge).
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class FreeOfChargeDiscountVoucher extends AbstractShippingTotalDiscountVoucher
{
	/**
	 * Override the running price by forcing
	 * the value to 0, resulting in no charge
	 *
	 * Applies the discount by forcing the running
	 * price to the literal, 0, making the order
	 * line free of charge.
	 *
	 * @param VoucherContext $voucherContext
	 * @param OrderLineInterface $discountable
	 */
	protected function applyProductDiscount(VoucherContext $voucherContext, OrderLineInterface $discountable)
	{
		$places = $discountable->getPlaces();
		$discountQuantity = min($voucherContext->getMaxDiscountQuantity(), $discountable->getProductQuantity());

		// If the discount quantity is less than the total product quantity, we must use the
		// net unit price to work out how many units are eligible for discount as the net price.
		// If the discount quantity is equal to the total product quantity, we must use the
		// full net price, as using net unit might result in rounding error when multiplying up.
		if ($discountQuantity < $discountable->getProductQuantity()) {
			$discountableNet = BCMath::round(bcmul($discountable->getNetUnit(), $discountQuantity, FinancialPrecision::PLACES),
				$places);
			$discountableGross = BCMath::round(bcmul($discountable->getGrossUnit(), $discountQuantity, FinancialPrecision::PLACES), $places);
		} else {
			$net = $discountable->getDiscountedNetPricingNet();
			$gross = $discountable->getDiscountedGrossPricingGross();

			$discountableNet = $net;
			$discountableGross = $gross;
		}

		$discountable->discountNetPrice($discountableNet);
		$discountable->discountGrossPrice($discountableGross);

		if (in_array($this->discountMethod, [
			DiscountMethod::HIGHEST_PRICED_MATCHING_LINE,
			DiscountMethod::LOWEST_PRICED_MATCHING_LINE])) {
			$discountable->setVoucherName($this->voucherName);
		}
	}

	/**
	 * Override the running price by forcing
	 * the value to 0, resulting in no charge
	 *
	 * Applies the discount by forcing the running
	 * price to the literal, 0, making the shipping
	 * free of charge.
	 *
	 * @param VoucherContext $voucherContext
	 * @param ShippingMethodInterface $discountable
	 */
	protected function applyShippingDiscount(VoucherContext $voucherContext, ShippingMethodInterface $discountable)
	{
		$discountable->discountNetPrice($discountable->getDiscountedNetPricingNet());
		$discountable->discountGrossPrice($discountable->getDiscountedGrossPricingGross());
	}

	/**
	 * Override the running price by forcing
	 * the value to 0, resulting in no charge
	 *
	 * Applies the discount by forcing the running
	 * price to the literal, 0, making the total
	 * free of charge.
	 *
	 * @param VoucherContext $voucherContext
	 * @param OrderInterface $order
	 * @param bool $distribute
	 */
	protected function applyTotalDiscount(VoucherContext $voucherContext, OrderInterface $order, $distribute)
	{
		$places = $order->getPlaces();

		if ($distribute) {
			$lines = $voucherContext->getMatchingLines($this->discountMethod);
			foreach ($lines as $line) {
				$discountQuantity = min($voucherContext->getMaxDiscountQuantity(), $line->getProductQuantity());

				// If the discount quantity is less than the total product quantity, we must use the
				// net unit price to work out how many units are eligible for discount as the net price.
				// If the discount quantity is equal to the total product quantity, we must use the
				// full net price, as using net unit might result in rounding error when multiplying up.
				if ($discountQuantity < $line->getProductQuantity()) {
					$discountableNet = BCMath::round(bcmul($line->getNetUnit(), $discountQuantity, FinancialPrecision::PLACES), $places);
					$discountableGross = BCMath::round(bcmul($line->getGrossUnit(), $discountQuantity, FinancialPrecision::PLACES), $places);
				} else {
					$net = $line->getDiscountedNetPricingNet();
					$gross = $line->getDiscountedGrossPricingGross();

					$discountableNet = $net;
					$discountableGross = $gross;
				}

				$line->discountNetPrice($discountableNet);
				$line->discountGrossPrice($discountableGross);

				if (in_array($this->discountMethod, [
					DiscountMethod::HIGHEST_PRICED_MATCHING_LINE,
					DiscountMethod::LOWEST_PRICED_MATCHING_LINE])) {
					$line->setVoucherName($this->voucherName);
				}
			}

			foreach ($order->getFooterSectionComponentAssociations() as $sectionAssociation) {
				$sectionAssociation->discountNetPrice($sectionAssociation->getDiscountedNetPricingNet());
				$sectionAssociation->discountGrossPrice($sectionAssociation->getDiscountedGrossPricingGross());

				foreach ($sectionAssociation->getSectionComponentAssociations() as $subSectionAssociation) {
					$subSectionAssociation->discountNetPrice($subSectionAssociation->getDiscountedNetPricingNet());
					$subSectionAssociation->discountGrossPrice($subSectionAssociation->getDiscountedGrossPricingGross());
				}

				foreach ($sectionAssociation->getCheckboxComponentAssociations() as $checkboxAssociation) {
					if ($checkboxAssociation->isChecked()) {
						$checkboxAssociation->discountNetPrice($checkboxAssociation->getDiscountedNetPricingNet());
						$checkboxAssociation->discountGrossPrice($checkboxAssociation->getDiscountedGrossPricingGross());
					}
				}
			}

			foreach ($order->getFooterCheckboxComponentAssociations() as $checkboxAssociation) {
				if ($checkboxAssociation->isChecked()) {
					$checkboxAssociation->discountNetPrice($checkboxAssociation->getDiscountedNetPricingNet());
					$checkboxAssociation->discountGrossPrice($checkboxAssociation->getDiscountedGrossPricingGross());
				}
			}

			$shippingMethod = $order->getShippingMethod();
			$shippingMethod->discountNetPrice($shippingMethod->getDiscountedNetPricingNet());
			$shippingMethod->discountGrossPrice($shippingMethod->getDiscountedGrossPricingGross());
		} else {
			$order->discountNetPrice($order->getDiscountedNetPricingNet());
			$order->discountGrossPrice($order->getDiscountedGrossPricingGross());
		}
	}
}
