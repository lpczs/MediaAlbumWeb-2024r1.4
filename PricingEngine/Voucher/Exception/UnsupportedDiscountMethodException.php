<?php

namespace PricingEngine\Voucher\Exception;

use Exception;

/**
 * Unsupported discount method exception
 *
 * Exception thrown when attempting to construct
 * a voucher for a discount method which is
 * unsupported.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class UnsupportedDiscountMethodException extends Exception
{

}
