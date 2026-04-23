<?php

namespace PricingEngine\PriceBreakSet\Exception;

use Exception;

/**
 * Pricing model unsupported exception
 *
 * An exception thrown by the factory class when the
 * factory method is presented with a price break set
 * database record that is configured with a pricing
 * model that is unsupported by the factory.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class UnsupportedPricingModelException extends Exception
{

}
