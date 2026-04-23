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
 * Value voucher
 *
 * Override the running price with the value of
 * the voucher for matching order lines.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class ValueDiscountVoucher extends AbstractDistributedProductDiscountVoucher
{
	/**
	 * Abstract method for implementing distributed discounting
	 *
	 * The method a sub class must implement to handle a distributed
	 * discounting method voucher.
	 *
	 * @param VoucherContext $voucherContext
	 * @param OrderLineInterface $discountable
	 */
	protected function applyProductDistributedDiscount(VoucherContext $voucherContext, OrderLineInterface $discountable)
	{
		$places = $discountable->getPlaces();
		$maxDiscountedQuantity = $voucherContext->getMaxDiscountQuantity();
		$discountQuantity = min($maxDiscountedQuantity, $discountable->getProductQuantity());

		// If the discount quantity is less than the total product quantity, we must use the
		// net unit price to work out how many units are eligible for discount as the net price.
		// If the discount quantity is equal to the total product quantity, we must use the
		// full net price, as using net unit might result in rounding error when multiplying up.
		if ($discountQuantity < $discountable->getProductQuantity()) {
			$netUnit = $discountable->getNetUnit();
			$grossUnit = $discountable->getGrossUnit();

			$discountableNet = BCMath::round(bcmul($netUnit, $discountQuantity, FinancialPrecision::PLACES), $places);
			$discountableGross = BCMath::round(bcmul($grossUnit, $discountQuantity, FinancialPrecision::PLACES), $places);
		} else {
			$net = $discountable->getDiscountedNetPricingNet();
			$gross = $discountable->getDiscountedGrossPricingGross();

			$discountableNet = $net;
			$discountableGross = $gross;
		}

		$voucherNet = $voucherContext->getNetValue();
		$voucherGross = $voucherContext->getGrossValue();

		if (bccomp($voucherNet, $discountableNet, $places) >= 0) {
			$voucherNet = bcsub($voucherNet, $discountableNet, $places);
			$voucherContext->setNetValue($voucherNet);
		} else {
			$discountValue = bcsub($discountableNet, $voucherNet, $places);
			$discountable->discountNetPrice($discountValue);
			$voucherContext->setNetValue('0');
		}

		if (bccomp($voucherGross, $discountableGross, $places) >= 0) {
			$voucherGross = bcsub($voucherGross, $discountableGross, $places);
			$voucherContext->setGrossValue($voucherGross);
		} else {
			$discountValue = bcsub($discountableGross, $voucherGross, $places);
			$discountable->discountGrossPrice($discountValue);
			$voucherContext->setGrossValue('0');
		}

		$voucherContext->setMaxDiscountQuantity($maxDiscountedQuantity - $discountQuantity);
	}

	/**
	 * Override the running price with the voucher value
	 *
	 * Applies the discount to the running price by overriding the price
	 * with the value of the voucher.
	 *
	 * @param VoucherContext $voucherContext
	 * @param OrderLineInterface $discountable
	 */
	protected function applyProductDiscount(VoucherContext $voucherContext, OrderLineInterface $discountable)
	{
		$places = $discountable->getPlaces();
		$discountQuantity = min($voucherContext->getMaxDiscountQuantity(), $discountable->getProductQuantity());

		$net = $discountable->getDiscountedNetPricingNet();
		$gross = $discountable->getDiscountedGrossPricingGross();

		// If the discount quantity is less than the total product quantity, we must use the
		// net unit price to work out how many units are eligible for discount as the net price.
		// If the discount quantity is equal to the total product quantity, we must use the
		// full net price, as using net unit might result in rounding error when multiplying up.
		if ($discountQuantity < $discountable->getProductQuantity()) {
			// Calculate the new discounted net price
			$netUnit = $discountable->getNetUnit();
			$newNet = bcmul($netUnit, $discountQuantity, FinancialPrecision::PLACES);
			$newNet = bcsub($net, $newNet, FinancialPrecision::PLACES);
			$newNet = BCMath::round(bcadd($newNet, $this->value, FinancialPrecision::PLACES), $places);

			// Calculate the new discounted gross price
			$grossUnit = $discountable->getGrossUnit();
			$newGross = bcmul($grossUnit, $discountQuantity, FinancialPrecision::PLACES);
			$newGross = bcsub($gross, $newGross, FinancialPrecision::PLACES);
			$newGross = BCMath::round(bcadd($newGross, $this->value, FinancialPrecision::PLACES), $places);
		} else {
			$newNet = $this->value;
			$newGross = $this->value;
		}

		if (bccomp($newNet, $net, $places) === 1) {
			$newNet = $net;
		}

		if (bccomp($newGross, $gross, $places) === 1) {
			$newGross = $gross;
		}

		$discountValue = bcsub($net, $newNet, $places);
		$discountable->discountNetPrice($discountValue);

		$discountValue = bcsub($gross, $newGross, $places);
		$discountable->discountGrossPrice($discountValue);

		if (in_array($this->discountMethod, [
				DiscountMethod::HIGHEST_PRICED_MATCHING_LINE,
				DiscountMethod::LOWEST_PRICED_MATCHING_LINE])) {
			$discountable->setVoucherName($this->voucherName);
		}
	}

	/**
	 * Abstract method for applying shipping discounts
	 *
	 * @param VoucherContext $voucherContext
	 * @param ShippingMethodInterface $discountable
	 */
	protected function applyShippingDiscount(VoucherContext $voucherContext, ShippingMethodInterface $discountable)
	{
		$places = $discountable->getPlaces();
		$value = BCMath::round($this->value, $places);

		$net = $discountable->getDiscountedNetPricingNet();
		if (bccomp($net, $value, $places) === 1) {
			$discountValue = bcsub($discountable->getDiscountedNetPricingNet(), $value, $places);
			$discountable->discountNetPrice($discountValue);
		}

		$gross = $discountable->getDiscountedGrossPricingGross();
		if (bccomp($gross, $value, $places) === 1) {
			$discountValue = bcsub($discountable->getDiscountedGrossPricingGross(), $value, $places);
			$discountable->discountGrossPrice($discountValue);
		}
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
		$places = $order->getPlaces();

		if ($distribute) {
			$maxDiscountedQuantity = $voucherContext->getMaxDiscountQuantity();
			$voucherNet = $voucherContext->getNetValue();
			$voucherGross = $voucherContext->getGrossValue();
			$productNetSubTotal = '0';
			$productGrossSubTotal = '0';

			$totalQuantity = 0;
			foreach ($voucherContext->getOrder()->getOrderLines() as $orderLine) {
				$totalQuantity += $orderLine->getProductQuantity();
			}

			// Behaviour of the old pricing engine has the value voucher switch to a distributed discount
			// if the quantity of items is less than the max discount quantity. This may have been a bug.
			$discountMethod = $this->discountMethod;
			if ($discountMethod === DiscountMethod::ALL_MATCHING_LINES && $maxDiscountedQuantity >= $totalQuantity) {
				$discountMethod = DiscountMethod::DISTRIBUTED_OVER_MATCHING_LINES;
			}

			$lines = $voucherContext->getMatchingLines($discountMethod);
			foreach ($lines as $lineNumber => $line) {
				$discountQuantity = min($maxDiscountedQuantity, $line->getProductQuantity());

				// If the discount quantity is less than the total product quantity, we must use the
				// net unit price to work out how many units are eligible for discount as the net price.
				// If the discount quantity is equal to the total product quantity, we must use the
				// full net price, as using net unit might result in rounding error when multiplying up.
				if ($discountQuantity < $line->getProductQuantity()) {
					$netUnit = $line->getNetUnit();
					$grossUnit = $line->getGrossUnit();

					$discountableNet = BCMath::round(bcmul($netUnit, $discountQuantity, FinancialPrecision::PLACES),
						$places);
					$discountableGross = BCMath::round(bcmul($grossUnit, $discountQuantity, FinancialPrecision::PLACES),
						$places);
				} else {
					$net = $line->getDiscountedNetPricingNet();
					$gross = $line->getDiscountedGrossPricingGross();

					$discountableNet = $net;
					$discountableGross = $gross;
				}

				if (DiscountMethod::DISTRIBUTED_OVER_MATCHING_LINES === $discountMethod) {
					if (bccomp($voucherNet, $discountableNet, $places) >= 0) {
						$voucherNet = bcsub($voucherNet, $discountableNet, $places);
						$voucherContext->setNetValue($voucherNet);
					} else {
						$discountValue = bcsub($discountableNet, $voucherNet, $places);
						$line->discountNetPrice($discountValue);
						$voucherNet = '0';
					}

					if (bccomp($voucherGross, $discountableGross, $places) >= 0) {
						$voucherGross = bcsub($voucherGross, $discountableGross, $places);
						$voucherContext->setGrossValue($voucherGross);
					} else {
						$discountValue = bcsub($discountableGross, $voucherGross, $places);
						$line->discountGrossPrice($discountValue);
						$voucherGross = '0';
					}

					$maxDiscountedQuantity = $maxDiscountedQuantity - $discountQuantity;
				} else {
					$productNetSubTotal = bcadd($discountableNet, $productNetSubTotal, $places);
					if (bccomp($discountableNet, $this->value, $places) >= 0) {
						$discountValue = bcsub($discountableNet, $this->value, $places);
						$line->discountNetPrice($discountValue);
					}

					$productGrossSubTotal = bcadd($discountableGross, $productGrossSubTotal, $places);
					if (bccomp($discountableGross, $this->value, $places) >= 0) {
						$discountValue = bcsub($discountableGross, $this->value, $places);
						$line->discountGrossPrice($discountValue);
					}
				}

				if (in_array($discountMethod, [
					DiscountMethod::HIGHEST_PRICED_MATCHING_LINE,
					DiscountMethod::LOWEST_PRICED_MATCHING_LINE])) {
					$line->setVoucherName($this->voucherName);
				}
			}

			foreach ($order->getFooterSectionComponentAssociations() as $sectionAssociation) {
				$discountableNet = $sectionAssociation->getDiscountedNetPricingNet();
				$discountableGross = $sectionAssociation->getDiscountedGrossPricingGross();

				if (DiscountMethod::DISTRIBUTED_OVER_MATCHING_LINES === $discountMethod) {
					if (bccomp($voucherNet, $discountableNet, $places) >= 0) {
						$voucherNet = bcsub($voucherNet, $discountableNet, $places);
						$voucherContext->setNetValue($voucherNet);
					} else {
						$discountValue = bcsub($discountableNet, $voucherNet, $places);
						$sectionAssociation->discountNetPrice($discountValue);
						$voucherNet = '0';
					}

					if (bccomp($voucherGross, $discountableGross, $places) >= 0) {
						$voucherGross = bcsub($voucherGross, $discountableGross, $places);
						$voucherContext->setGrossValue($voucherGross);
					} else {
						$discountValue = bcsub($discountableGross, $voucherGross, $places);
						$sectionAssociation->discountGrossPrice($discountValue);
						$voucherGross = '0';
					}
				} else {
					if (bccomp($productNetSubTotal, $this->value, $places) >= 0) {
						$sectionAssociation->discountNetPrice($discountableNet);
					} else {
						$discountValue = bcsub($discountableNet, $voucherNet, $places);
						$sectionAssociation->discountNetPrice($discountValue);
					}

					if (bccomp($productGrossSubTotal, $this->value, $places) >= 0) {
						$sectionAssociation->discountGrossPrice($discountableGross);
					} else {
						$discountValue = bcsub($discountableGross, $voucherGross, $places);
						$sectionAssociation->discountGrossPrice($discountValue);
					}
				}

				foreach ($sectionAssociation->getSectionComponentAssociations() as $subSectionAssociation) {
					$discountableNet = $subSectionAssociation->getDiscountedNetPricingNet();
					$discountableGross = $subSectionAssociation->getDiscountedGrossPricingGross();

					if (DiscountMethod::DISTRIBUTED_OVER_MATCHING_LINES === $discountMethod) {
						if (bccomp($voucherNet, $discountableNet, $places) >= 0) {
							$voucherNet = bcsub($voucherNet, $discountableNet, $places);
							$voucherContext->setNetValue($voucherNet);
						} else {
							$discountValue = bcsub($discountableNet, $voucherNet, $places);
							$subSectionAssociation->discountNetPrice($discountValue);
							$voucherNet = '0';
						}

						if (bccomp($voucherGross, $discountableGross, $places) >= 0) {
							$voucherGross = bcsub($voucherGross, $discountableGross, $places);
							$voucherContext->setGrossValue($voucherGross);
						} else {
							$discountValue = bcsub($discountableGross, $voucherGross, $places);
							$subSectionAssociation->discountGrossPrice($discountValue);
							$voucherGross = '0';
						}
					} else {
						if (bccomp($productNetSubTotal, $this->value, $places) >= 0) {
							$subSectionAssociation->discountNetPrice($discountableNet);
						} else {
							$discountValue = bcsub($discountableNet, $voucherNet, $places);
							$subSectionAssociation->discountNetPrice($discountValue);
						}

						if (bccomp($productGrossSubTotal, $this->value, $places) >= 0) {
							$subSectionAssociation->discountGrossPrice($discountableGross);
						} else {
							$discountValue = bcsub($discountableGross, $voucherGross, $places);
							$subSectionAssociation->discountGrossPrice($discountValue);
						}
					}
				}

				foreach ($sectionAssociation->getCheckboxComponentAssociations() as $checkboxAssociation) {
					if ($checkboxAssociation->isChecked()) {
						$discountableNet = $checkboxAssociation->getDiscountedNetPricingNet();
						$discountableGross = $checkboxAssociation->getDiscountedGrossPricingGross();

						if (DiscountMethod::DISTRIBUTED_OVER_MATCHING_LINES === $discountMethod) {
							if (bccomp($voucherNet, $discountableNet, $places) >= 0) {
								$voucherNet = bcsub($voucherNet, $discountableNet, $places);
								$voucherContext->setNetValue($voucherNet);
							} else {
								$discountValue = bcsub($discountableNet, $voucherNet, $places);
								$checkboxAssociation->discountNetPrice($discountValue);
								$voucherNet = '0';
							}

							if (bccomp($voucherGross, $discountableGross, $places) >= 0) {
								$voucherGross = bcsub($voucherGross, $discountableGross, $places);
								$voucherContext->setGrossValue($voucherGross);
							} else {
								$discountValue = bcsub($discountableGross, $voucherGross, $places);
								$checkboxAssociation->discountGrossPrice($discountValue);
								$voucherGross = '0';
							}
						} else {
							if (bccomp($productNetSubTotal, $this->value, $places) >= 0) {
								$checkboxAssociation->discountNetPrice($discountableNet);
							} else {
								$discountValue = bcsub($discountableNet, $voucherNet, $places);
								$checkboxAssociation->discountNetPrice($discountValue);
							}

							if (bccomp($productGrossSubTotal, $this->value, $places) >= 0) {
								$checkboxAssociation->discountGrossPrice($discountableGross);
							} else {
								$discountValue = bcsub($discountableGross, $voucherGross, $places);
								$checkboxAssociation->discountGrossPrice($discountValue);
							}
						}
					}
				}
			}

			foreach ($order->getFooterCheckboxComponentAssociations() as $checkboxAssociation) {
				if ($checkboxAssociation->isChecked()) {
					$discountableNet = $checkboxAssociation->getDiscountedNetPricingNet();
					$discountableGross = $checkboxAssociation->getDiscountedGrossPricingGross();

					if (DiscountMethod::DISTRIBUTED_OVER_MATCHING_LINES === $discountMethod) {
						if (bccomp($voucherNet, $discountableNet, $places) >= 0) {
							$voucherNet = bcsub($voucherNet, $discountableNet, $places);
							$voucherContext->setNetValue($voucherNet);
						} else {
							$discountValue = bcsub($discountableNet, $voucherNet, $places);
							$checkboxAssociation->discountNetPrice($discountValue);
							$voucherNet = '0';
						}

						if (bccomp($voucherGross, $discountableGross, $places) >= 0) {
							$voucherGross = bcsub($voucherGross, $discountableGross, $places);
							$voucherContext->setGrossValue($voucherGross);
						} else {
							$discountValue = bcsub($discountableGross, $voucherGross, $places);
							$checkboxAssociation->discountGrossPrice($discountValue);
							$voucherGross = '0';
						}
					} else {
						if (bccomp($productNetSubTotal, $this->value, $places) >= 0) {
							$checkboxAssociation->discountNetPrice($discountableNet);
						} else {
							$discountValue = bcsub($discountableNet, $voucherNet, $places);
							$checkboxAssociation->discountNetPrice($discountValue);
						}

						if (bccomp($productGrossSubTotal, $this->value, $places) >= 0) {
							$checkboxAssociation->discountGrossPrice($discountableGross);
						} else {
							$discountValue = bcsub($discountableGross, $voucherGross, $places);
							$checkboxAssociation->discountGrossPrice($discountValue);
						}
					}
				}
			}

			// Shipping
			$shippingMethod = $order->getShippingMethod();
			$discountableNet = $shippingMethod->getDiscountedNetPricingNet();
			$discountableGross = $shippingMethod->getDiscountedGrossPricingGross();

			if (DiscountMethod::DISTRIBUTED_OVER_MATCHING_LINES === $discountMethod) {
				if (bccomp($voucherNet, $discountableNet, $places) >= 0) {
					$voucherNet = bcsub($voucherNet, $discountableNet, $places);
					$voucherContext->setNetValue($voucherNet);
				} else {
					$discountValue = bcsub($discountableNet, $voucherNet, $places);
					$shippingMethod->discountNetPrice($discountValue);
					$voucherNet = '0';
				}

				if (bccomp($voucherGross, $discountableGross, $places) >= 0) {
					$voucherGross = bcsub($voucherGross, $discountableGross, $places);
					$voucherContext->setGrossValue($voucherGross);
				} else {
					$discountValue = bcsub($discountableGross, $voucherGross, $places);
					$shippingMethod->discountGrossPrice($discountValue);
					$voucherGross = '0';
				}
			} else {
				if (bccomp($productNetSubTotal, $this->value, $places) >= 0) {
					$shippingMethod->discountNetPrice($discountableNet);
				} else {
					$discountValue = bcsub($discountableNet, $voucherNet, $places);
					$shippingMethod->discountNetPrice($discountValue);
				}

				if (bccomp($productGrossSubTotal, $this->value, $places) >= 0) {
					$shippingMethod->discountGrossPrice($discountableGross);
				} else {
					$discountValue = bcsub($discountableGross, $voucherGross, $places);
					$shippingMethod->discountGrossPrice($discountValue);
				}
			}
		} else {
			$value = BCMath::round($this->value, $places);;

			$net = $order->getDiscountedNetPricingNet();
			if (bccomp($net, $value, $places) === 1) {
				$discountValue = bcsub($order->getDiscountedNetPricingNet(), $value, $places);
				$order->discountNetPriceReverse($discountValue);
			}

			$gross = $order->getDiscountedGrossPricingGross();
			if (bccomp($gross, $value, $places) === 1) {
				$discountValue = bcsub($order->getDiscountedGrossPricingGross(), $value, $places);
				$order->discountGrossPriceReverse($discountValue);
			}
		}
	}
}
