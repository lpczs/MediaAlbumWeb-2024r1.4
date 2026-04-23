<?php

namespace PricingEngine\Voucher\Exception;

use Exception;

/**
 * Unsupported voucher type exception
 *
 * Exception thrown when attempting to construct
 * a voucher for a voucher type that is
 * unsupported.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class UnsupportedVoucherTypeException extends Exception
{

}
