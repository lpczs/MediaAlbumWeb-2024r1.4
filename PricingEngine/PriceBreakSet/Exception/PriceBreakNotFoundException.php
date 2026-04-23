<?php

namespace PricingEngine\PriceBreakSet\Exception;

use Exception;

/**
 * Price break not found exception
 *
 * Exception thrown when attempting to create a price
 * using a price break set, but a price break for the
 * given quantities meant a price break could not be
 * found within the set in order to calculate the
 * price.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class PriceBreakNotFoundException extends Exception
{

}
