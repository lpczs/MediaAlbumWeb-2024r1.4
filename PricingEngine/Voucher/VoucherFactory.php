<?php

namespace PricingEngine\Voucher;

use Extension\Script\VoucherExtensionScript;
use PricingEngine\Enum\Voucher\DiscountType;
use PricingEngine\Enum\Voucher\VoucherType;
use PricingEngine\Enum\ExtensionScript;
use PricingEngine\Voucher\Discount\BuyQuantityGetOneFreeDiscountVoucher;
use PricingEngine\Voucher\Discount\BuyQuantityGetPercentageOffOneDiscountVoucher;
use PricingEngine\Voucher\Discount\BuyQuantityGetValueOffOneDiscountVoucher;
use PricingEngine\Voucher\Discount\FreeOfChargeDiscountVoucher;
use PricingEngine\Voucher\Discount\PercentageOffDiscountVoucher;
use PricingEngine\Voucher\Discount\ValueOffDiscountVoucher;
use PricingEngine\Voucher\Discount\ValueDiscountVoucher;
use PricingEngine\Voucher\Exception\UnsupportedVoucherTypeException;
use PricingEngine\Voucher\PrePaid\ValueOffPrePaidVoucher;
use PricingEngine\Voucher\Script\ScriptVoucher;
use PricingEngine\CurrencyInterface;

/**
 * Factory class for constructing a voucher
 *
 * Constructs a voucher instance based on the
 * type parameters passed, forwarding the options
 * to the voucher.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class VoucherFactory
{
	/**
	 * Factory method for constructing a new voucher instance
	 *
	 * Create a voucher instance for $voucherType and $discountType, passing the $order
	 * and $options through to the voucher instance constructor.
	 *
	 * @param string $voucherType
	 * @param string $discountType
	 * @param array $options
	 * @return VoucherInterface
	 * @throws UnsupportedVoucherTypeException
	 */
	public static function factory($voucherType, $discountType, array $options, CurrencyInterface $currency)
	{
		if (VoucherType::DISCOUNT === $voucherType) {
			switch ($discountType) {
				case DiscountType::VALUE:
					return new ValueDiscountVoucher($options, $currency);

				case DiscountType::VALUE_OFF:
					return new ValueOffDiscountVoucher($options, $currency);

				case DiscountType::PERCENTAGE_OFF:
					return new PercentageOffDiscountVoucher($options);

				case DiscountType::FREE_OF_CHARGE:
					return new FreeOfChargeDiscountVoucher($options);

				case DiscountType::BUY_QTY_GET_ONE_FREE:
					return new BuyQuantityGetOneFreeDiscountVoucher($options);

				case DiscountType::BUY_QTY_GET_PERCENTAGE_OFF_ONE:
					return new BuyQuantityGetPercentageOffOneDiscountVoucher($options);

				case DiscountType::BUY_QTY_GET_VALUE_OFF_ONE:
					return new BuyQuantityGetValueOffOneDiscountVoucher($options, $currency);
			}
		} elseif (VoucherType::PRE_PAID === $voucherType) {
			switch ($discountType) {
				case DiscountType::VALUE_OFF:
					return new ValueOffPrePaidVoucher($options, $currency);
			}
		} elseif (VoucherType::SCRIPT === $voucherType) {
			$extensionPath = isset($options['extensionPath']) ? $options['extensionPath'] : ExtensionScript::getExtensionPath();
			unset($options['extensionPath']);

			return new ScriptVoucher($options, new VoucherExtensionScript($extensionPath));
		}

		throw new UnsupportedVoucherTypeException();
	}
}
