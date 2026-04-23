<?php

namespace PricingEngine\Voucher\Discount;

use PricingEngine\Enum\Voucher\DiscountMethod;
use PricingEngine\Enum\Voucher\DiscountSection;
use PricingEngine\ShippingMethodInterface;
use PricingEngine\Voucher\DiscountableInterface;
use PricingEngine\Voucher\VoucherContext;
use PricingEngine\OrderInterface;
use PricingEngine\CurrencyInterface;

/**
 * Shipping and total abstract class
 *
 * Abstract class that includes abstract method declaraitons
 * of shipping and total discount sections.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
abstract class AbstractShippingTotalDiscountVoucher extends AbstractProductDiscountVoucher
{
	public function __construct(array $options)
	{
		parent::__construct($options);
	}

	/**
	 * Apply the voucher discount to the discountable using the context
	 *
	 * Apply the discount logic of the voucher type to the discountable instance passed.
	 *
	 * @param VoucherContext $voucherContext
	 * @param DiscountableInterface $discountable
	 */
	public function discount(VoucherContext $voucherContext, DiscountableInterface $discountable)
	{
		$discountSection = $voucherContext->getDiscountSection();
		if ($discountable instanceof ShippingMethodInterface && $discountSection === DiscountSection::SHIPPING) {
			$this->applyShippingDiscount($voucherContext, $discountable);
		} elseif ($discountable instanceof OrderInterface && $discountSection === DiscountSection::TOTAL) {
			$order = $voucherContext->getOrder();
			$this->applyTotalDiscount($voucherContext, $order, $this->isTotalDistributed($voucherContext));
		} else {
			parent::discount($voucherContext, $discountable);
		}
	}

	/**
	 * Check if the total discount should be distributed across the order
	 *
	 * Check if the total discount has to be distributed across the order,
	 * such a if multiple tax rates are used, not all order lines are
	 * eligible for discount and if the maximum quantity for discount
	 * is less than the maximum number of items in the order.
	 *
	 * @param VoucherContext $voucherContext
	 * @return bool
	 */
	protected function isTotalDistributed(VoucherContext $voucherContext)
	{
		$maxDiscountQuantity = $voucherContext->getMaxDiscountQuantity();
		$order = $voucherContext->getOrder();
		$allLinesEligible = true;
		$orderLines = $order->getOrderLines();
		$totalQuantity = 0;

		$isDistributedDiscountMethod = in_array($this->discountMethod, [
			DiscountMethod::LOWEST_PRICED_MATCHING_LINE,
			DiscountMethod::HIGHEST_PRICED_MATCHING_LINE,
			DiscountMethod::DISTRIBUTED_OVER_MATCHING_LINES,
		]);

		// If we have a discount method that forces a distributed total discount,
		// return quickly
		if ($isDistributedDiscountMethod) {
			return true;
		}

		foreach ($orderLines as $orderLine) {
			$allLinesEligible = $allLinesEligible && $orderLine->canApplyVoucher();
			$totalQuantity += $orderLine->getProductQuantity();
		}

		// If definitely don't need to distribute (so we must not) and flip the results
		return !($allLinesEligible && $order->hasSingleTaxRate() && $totalQuantity <= $maxDiscountQuantity);
	}

	/**
	 * Abstract method for applying shipping discounts
	 *
	 * @param VoucherContext $voucherContext
	 * @param ShippingMethodInterface $discountable
	 */
	abstract protected function applyShippingDiscount(VoucherContext $voucherContext, ShippingMethodInterface $discountable);

	/**
	 * Abstract method for applying total discounts
	 *
	 * @param VoucherContext $voucherContext
	 * @param OrderInterface $order
	 * @param bool $distribute
	 */
	abstract protected function applyTotalDiscount(VoucherContext $voucherContext, OrderInterface $order, $distribute);
}
