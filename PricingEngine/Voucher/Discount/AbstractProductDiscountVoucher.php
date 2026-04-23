<?php

namespace PricingEngine\Voucher\Discount;

use PricingEngine\Enum\Voucher\DiscountSection;
use PricingEngine\Enum\Voucher\DiscountMethod;
use PricingEngine\Enum\Voucher\VoucherType;
use PricingEngine\OrderInterface;
use PricingEngine\OrderLineInterface;
use PricingEngine\Voucher\AbstractVoucher;
use PricingEngine\Voucher\DiscountableInterface;
use PricingEngine\Voucher\VoucherContext;
use PricingEngine\CurrencyInterface;

/**
 * Abstract discounting voucher
 *
 * Abstract class for providing the generic functionality
 * for discounting specific matching order lines using a
 * voucher sub class.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
abstract class AbstractProductDiscountVoucher extends AbstractVoucher
{
	/**
	 * @var string
	 */
	protected $voucherName;

	/**
	 * @var string
	 */
	protected $value;

	/**
	 * @var mixed
	 */
	protected $discountSection;

	/**
	 * @var mixed
	 */
	protected $discountMethod;

	/**
	 * @var int
	 */
	protected $maxDiscountQuantity;

	/**
	 * Constructor
	 *
	 * @param array $options
	 */
	public function __construct(array $options)
	{
		parent::__construct($options);

		$this->voucherName = isset($options['name']) ? (string) $options['name'] : '';
		$this->value = isset($options['value']) ? (string) $options['value'] : null;
		$this->discountSection = isset($options['discountSection']) ? $options['discountSection'] : null;
		$this->discountMethod = isset($options['discountMethod']) ? $options['discountMethod'] : null;
		$this->maxDiscountQuantity = isset($options['maximumDiscountQuantity']) ? $options['maximumDiscountQuantity'] : null;
	}

	/**
	 * Create a voucher context for the given order
	 *
	 * Create a voucher context for the given order that
	 * will handle the storage and access to voucher state
	 * information on behalf of the voucher and the order
	 * object instances.
	 *
	 * @param OrderInterface $order
	 * @return VoucherContext
	 */
	public function createContext(OrderInterface $order)
	{
		return parent::createContext($order)
			->setDiscountSection($this->discountSection)
			->setVoucherType($this->getVoucherType())
			->setNetValue($this->value)
			->setGrossValue($this->value)
			->setMaxOrderQuantity($this->maximumOrderQuantity)
			->setMaxDiscountQuantity($this->maxDiscountQuantity)
		;
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
		if ($discountable instanceof OrderLineInterface && $discountable->canApplyVoucher() && $discountSection === DiscountSection::PRODUCT) {
			if ($this->discountMethod === DiscountMethod::ALL_MATCHING_LINES) {
				$this->applyProductDiscount($voucherContext, $discountable);
			} elseif ($this->discountMethod === DiscountMethod::LOWEST_PRICED_MATCHING_LINE &&
				$voucherContext->isLowestPricedLine($discountable)) {
				$this->applyProductDiscount($voucherContext, $discountable);
			} elseif ($this->discountMethod === DiscountMethod::HIGHEST_PRICED_MATCHING_LINE &&
				$voucherContext->isHighestPricedLine($discountable)) {
				$this->applyProductDiscount($voucherContext, $discountable);
			}
		}
	}

	/**
	 * Get voucher type
	 *
	 * Get the voucher type for this voucher implementation.
	 * Specifically, the discount voucher type.
	 *
	 * @return string
	 */
	protected function getVoucherType()
	{
		return VoucherType::DISCOUNT;
	}

	/**
	 * Abstract method for applying non distributed product discounts
	 *
	 * The method a sub class must implement to handle discounting
	 * an individually matched order line.
	 *
	 * @param VoucherContext $voucherContext
	 * @param OrderLineInterface $discountable
	 */
	abstract protected function applyProductDiscount(VoucherContext $voucherContext, OrderLineInterface $discountable);
}
