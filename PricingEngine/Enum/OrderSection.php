<?php

namespace PricingEngine\Enum;

/**
 * Order section enumeration
 *
 * Constants for labelling sections of an order.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class OrderSection
{
	/**
	 * The order line section
	 */
	const ORDER_LINE = 'order_line';

	/**
	 * The order footer section
	 */
	const ORDER_FOOTER = 'order_footer';

	/**
	 * The shipping section
	 */
	const SHIPPING = 'shipping';
}
