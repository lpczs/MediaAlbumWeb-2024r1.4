<?php

namespace PricingEngine\Voucher\Discount;

use PricingEngine\BCMath;
use PricingEngine\Enum\Voucher\DiscountMethod;
use PricingEngine\Enum\Voucher\DiscountSection;
use PricingEngine\Voucher\DiscountableInterface;
use PricingEngine\Voucher\VoucherContext;
use PricingEngine\OrderInterface;
use PricingEngine\OrderLineInterface;
use PricingEngine\ShippingMethodInterface;

/**
 * Value off voucher
 *
 * Deduct the voucher value as a monetary amount
 * from the value of matching order lines.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class ValueOffDiscountVoucher extends AbstractDistributedProductDiscountVoucher
{
	/**
	 * Calculate and apply the distributed discounting method
	 * for the voucher at the product level
	 *
	 * Applies the discount value of the voucher across all matching
	 * order lines, one line at a time, on demand. As the discount value
	 * is applied the remaining discount value is updated within the
	 * voucher context.
	 *
	 * @param VoucherContext $voucherContext
	 * @param OrderLineInterface $discountable
	 */
	protected function applyProductDistributedDiscount(VoucherContext $voucherContext, OrderLineInterface $discountable)
	{
		$places = $discountable->getPlaces();

		// Deduct the remaining net value from the discounted net price
		$netValue = $voucherContext->getNetValue();
		if (bccomp($netValue, 0, $places) === 1) {
			$discountedNetPrice = $discountable->getDiscountedNetPricingNet();

			if (bccomp($netValue, $discountedNetPrice, $places) === -1) {
				$discountAmount = BCMath::round($netValue, $places);
				$netValue = '0';
			} else {
				$discountAmount = BCMath::round($discountedNetPrice, $places);
				$netValue = bcsub($netValue, $discountedNetPrice, $places);
			}

			// Update the voucher context remaining values and discount the price
			$voucherContext->setNetValue($netValue);
			$discountable->discountNetPrice($discountAmount);
		}

		// Deduct the remaining gross value from the discounted gross price
		$grossValue = $voucherContext->getGrossValue();
		if (bccomp($grossValue, 0, $places) === 1) {
			$discountedGrossPrice = $discountable->getDiscountedGrossPricingGross();

			if (bccomp($grossValue, $discountedGrossPrice, $places) === -1) {
				$discountAmount = BCMath::round($grossValue, $places);
				$grossValue = '0';
			} else {
				$discountAmount = BCMath::round($discountedGrossPrice, $places);
				$grossValue = bcsub($grossValue, $discountedGrossPrice, $places);
			}

			// Update the voucher context remaining values and discount the price
			$voucherContext->setGrossValue($grossValue);
			$discountable->discountGrossPrice($discountAmount);
		}

		$discountable->setVoucherName($this->voucherName);
	}

	/**
	 * Calculate and apply the discount for the voucher to the running price
	 * at the product level
	 *
	 * Applies the discount in its entirety to the running price at a product level
	 * (order line).
	 *
	 * @param VoucherContext $voucherContext
	 * @param OrderLineInterface $discountable
	 */
	protected function applyProductDiscount(VoucherContext $voucherContext, OrderLineInterface $discountable)
	{
		$this->applyDiscount($discountable);
	}

	/**
	 * Abstract method for applying shipping discounts
	 *
	 * @param VoucherContext $voucherContext
	 * @param ShippingMethodInterface $discountable
	 */
	protected function applyShippingDiscount(VoucherContext $voucherContext, ShippingMethodInterface $discountable)
	{
		$this->applyDiscount($discountable);
	}

	/**
	 * Abstract method for applying total discounts
	 *
	 * @param VoucherContext $voucherContext
	 * @param OrderInterface $order
	 * @param bool $distribute
	 */
	protected function applyTotalDiscount(VoucherContext $voucherContext, OrderInterface $order, $distribute)
	{
		if ($distribute) {
			$places = $order->getPlaces();
			$netValue = $grossValue = $this->value;

			$lines = $voucherContext->getMatchingLines($this->discountMethod);
			foreach ($lines as $line) {
				$net = $line->getDiscountedNetPricingNet();
				if (bccomp($netValue, $net, $places) === -1) {
					$discountAmount = $netValue;
					$netValue = '0';
				} else {
					$discountAmount = $net;
					$netValue = bcsub($netValue, $net, $places);
				}
				$line->discountNetPrice($discountAmount);

				$gross = $line->getDiscountedGrossPricingGross();
				if (bccomp($grossValue, $gross, $places) === -1) {
					$discountAmount = $grossValue;
					$grossValue = '0';
				} else {
					$discountAmount = $gross;
					$grossValue = bcsub($grossValue, $gross, $places);
				}
				$line->discountGrossPrice($discountAmount);

				if (in_array($this->discountMethod, [
					DiscountMethod::HIGHEST_PRICED_MATCHING_LINE,
					DiscountMethod::LOWEST_PRICED_MATCHING_LINE])) {
					$line->setVoucherName($this->voucherName);
				}
			}

			foreach ($order->getFooterSectionComponentAssociations() as $sectionAssociation) {
				$net = $sectionAssociation->getDiscountedNetPricingNet();
				if (bccomp($netValue, $net, $places) === -1) {
					$discountAmount = $netValue;
					$netValue = '0';
				} else {
					$discountAmount = $net;
					$netValue = bcsub($netValue, $net, $places);
				}
				$sectionAssociation->discountNetPrice($discountAmount);

				$gross = $sectionAssociation->getDiscountedGrossPricingGross();
				if (bccomp($grossValue, $gross, $places) === -1) {
					$discountAmount = $grossValue;
					$grossValue = '0';
				} else {
					$discountAmount = $gross;
					$grossValue = bcsub($grossValue, $gross, $places);
				}
				$sectionAssociation->discountGrossPrice($discountAmount);

				foreach ($sectionAssociation->getSectionComponentAssociations() as $subSectionAssociation) {
					$net = $subSectionAssociation->getDiscountedNetPricingNet();
					if (bccomp($netValue, $net, $places) === -1) {
						$discountAmount = $netValue;
						$netValue = '0';
					} else {
						$discountAmount = $net;
						$netValue = bcsub($netValue, $net, $places);
					}
					$subSectionAssociation->discountNetPrice($discountAmount);

					$gross = $subSectionAssociation->getDiscountedGrossPricingGross();
					if (bccomp($grossValue, $gross, $places) === -1) {
						$discountAmount = $grossValue;
						$grossValue = '0';
					} else {
						$discountAmount = $gross;
						$grossValue = bcsub($grossValue, $gross, $places);
					}
					$subSectionAssociation->discountGrossPrice($discountAmount);
				}

				foreach ($sectionAssociation->getCheckboxComponentAssociations() as $checkboxAssociation) {
					if ($checkboxAssociation->isChecked()) {
						$net = $checkboxAssociation->getDiscountedNetPricingNet();
						if (bccomp($netValue, $net, $places) === -1) {
							$discountAmount = $netValue;
							$netValue = '0';
						} else {
							$discountAmount = $net;
							$netValue = bcsub($netValue, $net, $places);
						}
						$checkboxAssociation->discountNetPrice($discountAmount);

						$gross = $checkboxAssociation->getDiscountedGrossPricingGross();
						if (bccomp($grossValue, $gross, $places) === -1) {
							$discountAmount = $grossValue;
							$grossValue = '0';
						} else {
							$discountAmount = $gross;
							$grossValue = bcsub($grossValue, $gross, $places);
						}
						$checkboxAssociation->discountGrossPrice($discountAmount);
					}
				}
			}

			foreach ($order->getFooterCheckboxComponentAssociations() as $checkboxAssociation) {
				if ($checkboxAssociation->isChecked()) {
					$net = $checkboxAssociation->getDiscountedNetPricingNet();
					if (bccomp($netValue, $net, $places) === -1) {
						$discountAmount = $netValue;
						$netValue = '0';
					} else {
						$discountAmount = $net;
						$netValue = bcsub($netValue, $net, $places);
					}
					$checkboxAssociation->discountNetPrice($discountAmount);

					$gross = $checkboxAssociation->getDiscountedGrossPricingGross();
					if (bccomp($grossValue, $gross, $places) === -1) {
						$discountAmount = $grossValue;
						$grossValue = '0';
					} else {
						$discountAmount = $gross;
						$grossValue = bcsub($grossValue, $gross, $places);
					}
					$checkboxAssociation->discountGrossPrice($discountAmount);
				}
			}

			$shippingMethod = $order->getShippingMethod();
			$net = $shippingMethod->getDiscountedNetPricingNet();
			if (bccomp($netValue, $net, $places) === -1) {
				$discountAmount = $netValue;
				$netValue = '0';
			} else {
				$discountAmount = $net;
				$netValue = bcsub($netValue, $net, $places);
			}
			$shippingMethod->discountNetPrice($discountAmount);

			$gross = $shippingMethod->getDiscountedGrossPricingGross();
			if (bccomp($grossValue, $gross, $places) === -1) {
				$discountAmount = $grossValue;
				$grossValue = '0';
			} else {
				$discountAmount = $gross;
				$grossValue = bcsub($grossValue, $gross, $places);
			}
			$shippingMethod->discountGrossPrice($discountAmount);
		} else {
			$this->applyDiscount($order);
		}
	}

	/**
	 * Generic function to apply voucher discount for all discount sections
	 *
	 * @param DiscountableInterface $discountable
	 */
	private function applyDiscount(DiscountableInterface $discountable)
	{
		$places = $discountable->getPlaces();
		$value = BCMath::round($this->value, $places);

		$discountable->discountNetPrice($value);
		$discountable->discountGrossPrice($value);

		if (in_array($this->discountMethod, [
			DiscountMethod::HIGHEST_PRICED_MATCHING_LINE,
			DiscountMethod::LOWEST_PRICED_MATCHING_LINE])) {
			$discountable->setVoucherName($this->voucherName);
		}
	}
}
