<?php

namespace PricingEngine\Voucher;

use PricingEngine\OrderInterface;

/**
 * Abstract voucher implementation
 *
 * An abstract voucher class implementation that provides
 * base logic for handling all types of vouchers.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
abstract class AbstractVoucher implements VoucherInterface
{
	/**
	 * @var string
	 */
	protected $voucherPromotionCode;

	/**
	 * @var string
	 */
	protected $voucherCode;

	/**
	 * @var int
	 */
	protected $minimumOrderQuantity;

	/**
	 * @var int
	 */
	protected $maximumOrderQuantity;

	/**
	 * Constructor
	 *
	 * @param array $options
	 */
	public function __construct(array $options)
	{
		$this->voucherPromotionCode = isset($options['voucherPromotionCode']) ? (string)$options['voucherPromotionCode'] : null;
		$this->voucherCode = isset($options['voucherCode']) ? (string)$options['voucherCode'] : null;
		$this->minimumOrderQuantity = isset($options['minimumOrderQuantity']) ? (int)$options['minimumOrderQuantity'] : 1;
		$this->maximumOrderQuantity = isset($options['maximumOrderQuantity']) ? (int)$options['maximumOrderQuantity'] : 9999;
	}

	/**
	 * Test if the voucher is a script voucher type
	 *
	 * Check if the voucher implementation is a script
	 * voucher type. Returns true if a script voucher
	 * type, false otherwise.
	 *
	 * @return bool
	 */
	public function isScriptVoucher()
	{
		return false;
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
		return new VoucherContext($order, $this);
	}
}
