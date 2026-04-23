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
 * Percentage off discount
 *
 * Override the running price with a percentage
 * discount for matching order lines
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class PercentageOffDiscountVoucher extends AbstractShippingTotalDiscountVoucher
{
	/**
	 * Applying product discounts
	 *
	 * @param VoucherContext $voucherContext
	 * @param OrderLineInterface $discountable
	 */
	protected function applyProductDiscount(VoucherContext $voucherContext, OrderLineInterface $discountable)
	{
		$places = $discountable->getPlaces();
		$discountQuantity = min($voucherContext->getMaxDiscountQuantity(), $discountable->getProductQuantity());
		$percentage = bcdiv($this->value, 100, FinancialPrecision::PLACES);

		// If the discount quantity is less than the total product quantity, we must use the
		// net unit price to work out how many units are eligible for discount as the net price.
		// If the discount quantity is equal to the total product quantity, we must use the
		// full net price, as using net unit might result in rounding error when multiplying up.
		if ($discountQuantity < $discountable->getProductQuantity()) {
			$discountableNet = bcmul($discountable->getNetUnit(), $discountQuantity, FinancialPrecision::PLACES);
			$discountableGross = bcmul($discountable->getGrossUnit(), $discountQuantity, FinancialPrecision::PLACES);
		} else {
			$net = $discountable->getDiscountedNetPricingNet();
			$gross = $discountable->getDiscountedGrossPricingGross();

			$discountableNet = $net;
			$discountableGross = $gross;
		}

		$discountableNet = BCMath::round(bcmul($discountableNet, $percentage, FinancialPrecision::PLACES), $places);
		$discountable->discountNetPrice($discountableNet);

		// Discount gross
		$discountableGross = BCMath::round(bcmul($discountableGross, $percentage, FinancialPrecision::PLACES), $places);
		$discountable->discountGrossPrice($discountableGross);

		if (in_array($this->discountMethod, [
			DiscountMethod::HIGHEST_PRICED_MATCHING_LINE,
			DiscountMethod::LOWEST_PRICED_MATCHING_LINE])) {
			$discountable->setVoucherName($this->voucherName);
		}
	}

	/**
	 * Applying shipping discounts
	 *
	 * @param VoucherContext $voucherContext
	 * @param ShippingMethodInterface $discountable
	 */
	protected function applyShippingDiscount(VoucherContext $voucherContext, ShippingMethodInterface $discountable)
	{
		$places = $discountable->getPlaces();
		$percentage = bcdiv($this->value, 100, FinancialPrecision::PLACES);

		$discountedNetPrice = BCMath::round(bcmul(
			$discountable->getDiscountedNetPricingNet(),
			$percentage,
			FinancialPrecision::PLACES
		), $places);

		$discountedGrossPrice = BCMath::round(bcmul(
			$discountable->getDiscountedGrossPricingGross(),
			$percentage,
			FinancialPrecision::PLACES
		), $places);

		$discountable->discountNetPrice($discountedNetPrice);
		$discountable->discountGrossPrice($discountedGrossPrice);
	}

	/**
	 * Applying total discounts
	 *
	 * @param VoucherContext $voucherContext
	 * @param OrderInterface $order
	 * @param bool $distribute
	 */
	protected function applyTotalDiscount(VoucherContext $voucherContext, OrderInterface $order, $distribute)
	{
		$places = $order->getPlaces();
		$percentage = bcdiv($this->value, 100, FinancialPrecision::PLACES);

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

				$discountableNet = BCMath::round(bcmul($discountableNet, $percentage, FinancialPrecision::PLACES), $places);
				$line->discountNetPrice($discountableNet);

				$discountableGross = BCMath::round(bcmul($discountableGross, $percentage, FinancialPrecision::PLACES), $places);
				$line->discountGrossPrice($discountableGross);

				if (in_array($this->discountMethod, [
					DiscountMethod::HIGHEST_PRICED_MATCHING_LINE,
					DiscountMethod::LOWEST_PRICED_MATCHING_LINE])) {
					$line->setVoucherName($this->voucherName);
				}
			}

			foreach ($order->getFooterSectionComponentAssociations() as $sectionAssociation) {
				$net = $sectionAssociation->getDiscountedNetPricingNet();
				$discountValue = BCMath::round(bcmul($net, $percentage, FinancialPrecision::PLACES), $places);
				$sectionAssociation->discountNetPrice($discountValue);

				$gross = $sectionAssociation->getDiscountedGrossPricingGross();
				$discountValue = BCMath::round(bcmul($gross, $percentage, FinancialPrecision::PLACES), $places);
				$sectionAssociation->discountGrossPrice($discountValue);

				foreach ($sectionAssociation->getSectionComponentAssociations() as $subSectionAssociation) {
					$net = $subSectionAssociation->getDiscountedNetPricingNet();
					$discountValue = BCMath::round(bcmul($net, $percentage, FinancialPrecision::PLACES), $places);
					$subSectionAssociation->discountNetPrice($discountValue);

					$gross = $subSectionAssociation->getDiscountedGrossPricingGross();
					$discountValue = BCMath::round(bcmul($gross, $percentage, FinancialPrecision::PLACES), $places);
					$subSectionAssociation->discountGrossPrice($discountValue);
				}

				foreach ($sectionAssociation->getCheckboxComponentAssociations() as $checkboxAssociation) {
					if ($checkboxAssociation->isChecked()) {
						$net = $checkboxAssociation->getDiscountedNetPricingNet();
						$discountValue = BCMath::round(bcmul($net, $percentage, FinancialPrecision::PLACES), $places);
						$checkboxAssociation->discountNetPrice($discountValue);

						$gross = $checkboxAssociation->getDiscountedGrossPricingGross();
						$discountValue = BCMath::round(bcmul($gross, $percentage, FinancialPrecision::PLACES), $places);
						$checkboxAssociation->discountGrossPrice($discountValue);
					}
				}
			}

			foreach ($order->getFooterCheckboxComponentAssociations() as $checkboxAssociation) {
				if ($checkboxAssociation->isChecked()) {
					$net = $checkboxAssociation->getDiscountedNetPricingNet();
					$discountValue = BCMath::round(bcmul($net, $percentage, FinancialPrecision::PLACES), $places);
					$checkboxAssociation->discountNetPrice($discountValue);

					$gross = $checkboxAssociation->getDiscountedGrossPricingGross();
					$discountValue = BCMath::round(bcmul($gross, $percentage, FinancialPrecision::PLACES), $places);
					$checkboxAssociation->discountGrossPrice($discountValue);
				}
			}

			$shippingMethod = $order->getShippingMethod();
			$net = $shippingMethod->getDiscountedNetPricingNet();
			$discountValue = BCMath::round(bcmul($net, $percentage, FinancialPrecision::PLACES), $places);
			$shippingMethod->discountNetPrice($discountValue);

			$gross = $shippingMethod->getDiscountedGrossPricingGross();
			$discountValue = BCMath::round(bcmul($gross, $percentage, FinancialPrecision::PLACES), $places);
			$shippingMethod->discountGrossPrice($discountValue);
		} else {
			$discountedNetPrice = BCMath::round(bcmul(
				$order->getDiscountedNetPricingNet(),
				$percentage,
				FinancialPrecision::PLACES
			), $places);

			$discountedGrossPrice = BCMath::round(bcmul(
				$order->getDiscountedGrossPricingGross(),
				$percentage,
				FinancialPrecision::PLACES
			), $places);

			$order->discountNetPrice($discountedNetPrice);
			$order->discountGrossPrice($discountedGrossPrice);
		}
	}
}
