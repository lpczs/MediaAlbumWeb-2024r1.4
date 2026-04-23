<?php

namespace PricingEngine\OrderLoader;

use PricingEngine\Order;
use PricingEngine\ShippingMethod;

/**
 * Order shipping loader
 *
 * Handles loading the order shipping from session
 * in to the order objects for price calculation.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class ShippingLoader
{
	/**
	 * @param Order $order
	 * @param mixed[] $session
	 */
	public static function loadShippingMethod(Order $order, &$session)
	{
		$shippingMethod = new ShippingMethod($session['shipping'][0]);
		$order->setShippingMethod($shippingMethod);
	}
}
