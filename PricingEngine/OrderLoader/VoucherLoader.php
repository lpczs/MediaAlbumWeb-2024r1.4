<?php

namespace PricingEngine\OrderLoader;

use PricingEngine\Order;
use PricingEngine\Voucher\Exception\UnsupportedVoucherTypeException;
use PricingEngine\Voucher\VoucherFactory;
use PricingEngine\CurrencyInterface;

/**
 * Order voucher loader
 *
 * Handles loading the order voucher from session
 * in to the order objects for price calculation.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class VoucherLoader
{
	/**
	 * @param Order $order
	 * @param mixed[] $session
	 * @throws UnsupportedVoucherTypeException
	 */
	public static function loadVouchers(Order $order, &$session, CurrencyInterface $currency)
	{
		if (!empty(@$session['order']['vouchercode'])) {
			$voucher = VoucherFactory::factory($session['order']['vouchertype'], $session['order']['voucherdiscounttype'], [
				'voucherPromotionCode' => $session['order']['voucherpromotioncode'],
				'voucherCode' => $session['order']['vouchercode'],

				'minimumOrderQuantity' => $session['order']['voucherminqty'],
				'maximumOrderQuantity' => $session['order']['vouchermaxqty'],

				'name' => $session['order']['vouchername'],
				'value' => $session['order']['voucherdiscountvalue'],
				'discountSection' => $session['order']['voucherdiscountsection'],
				'discountMethod' => $session['order']['voucherapplicationmethod'],

				'maximumDiscountQuantity' => $session['order']['voucherapplytoqty'],
			],
			$currency);
			$order->setVoucher($voucher);
		}
	}
}
