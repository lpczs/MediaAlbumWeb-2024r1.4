<?php

namespace PricingEngine\Voucher\Discount;

use PricingEngine\Enum\Voucher\DiscountSection;
use PricingEngine\Enum\Voucher\DiscountMethod;
use PricingEngine\Voucher\DiscountableInterface;
use PricingEngine\Voucher\VoucherContext;
use PricingEngine\OrderLineInterface;
use PricingEngine\CurrencyInterface;

/**
 * Abstract distributed discounting voucher
 *
 * Abstract class for distributed discount vouchers
 * that will distribute their voucher value over
 * matching order lines, rather than an individual
 * order line
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
abstract class AbstractDistributedProductDiscountVoucher extends AbstractShippingTotalDiscountVoucher
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
		if ($discountSection === DiscountSection::PRODUCT &&
			$this->discountMethod === DiscountMethod::DISTRIBUTED_OVER_MATCHING_LINES &&
			$discountable instanceof OrderLineInterface &&
			$discountable->canApplyVoucher()) {
			$this->applyProductDistributedDiscount($voucherContext, $discountable);
		} else {
			parent::discount($voucherContext, $discountable);
		}
	}

	/**
	 * Abstract method for implementing distributed discounting
	 *
	 * The method a sub class must implement to handle a distributed
	 * discounting method voucher.
	 *
	 * @param VoucherContext $voucherContext
	 * @param OrderLineInterface $discountable
	 */
	abstract protected function applyProductDistributedDiscount(VoucherContext $voucherContext, OrderLineInterface $discountable);
}
