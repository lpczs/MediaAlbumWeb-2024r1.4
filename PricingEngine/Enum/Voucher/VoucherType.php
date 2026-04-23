<?php

namespace PricingEngine\Enum\Voucher;

require_once __DIR__ . '/../../../Utils/UtilsConstants.php';

class VoucherType
{
	const DISCOUNT = TPX_VOUCHER_TYPE_DISCOUNT;
	const PRE_PAID = TPX_VOUCHER_TYPE_PREPAID;
	const SCRIPT = TPX_VOUCHER_TYPE_SCRIPT;
}
