<?php

namespace PricingEngine;

use PricingEngine\OrderLoader\OrderFooterLoader;
use PricingEngine\OrderLoader\OrderLineLoader;
use PricingEngine\OrderLoader\ShippingLoader;
use PricingEngine\OrderLoader\VoucherLoader;
use PricingEngine\PriceBreakSet\Exception\UnsupportedPricingModelException;
use PricingEngine\Tax\TaxBreakdown;
use PricingEngine\Voucher\Exception\UnsupportedVoucherTypeException;

/**
 * Order loader
 *
 * Handles loading an order from the session
 * in to an Order object instance.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class OrderLoader
{
	/**
	 * Load the order from the given session
	 *
	 * Using the given session, load the order in to a new
	 * Order object instance and return the instance.
	 *
	 * @param mixed[] $session
	 * @return Order
	 * @throws UnsupportedPricingModelException
	 * @throws UnsupportedVoucherTypeException
	 */
	public static function load(&$session)
	{
		// Order
		$isShowPricesWithTax = $session['order']['showpriceswithtax'] === 1;
		$currency = new Currency($session['order']['currencydecimalplaces'], $session['order']['currencyexchangerate']);
		$taxBreakdown = new TaxBreakdown($currency, $session['order']['ordertaxbreakdown']);
		$order = new Order($currency, $taxBreakdown, $isShowPricesWithTax, $session);

		VoucherLoader::loadVouchers($order, $session, $currency);
		OrderLineLoader::loadOrderLines($order, $session);
		OrderFooterLoader::loadOrderFooterComponents($order, $session);
		ShippingLoader::loadShippingMethod($order, $session);

		return $order;
	}
}
