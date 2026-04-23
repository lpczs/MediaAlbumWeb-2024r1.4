<?php

namespace PricingEngine\Enum\Voucher;

class DiscountType
{
	const VALUE = 'VALUESET';
	const VALUE_OFF = 'VALUE';
	const PERCENTAGE_OFF = 'PERCENT';
	const FREE_OF_CHARGE = 'FOC';
	const BUY_QTY_GET_ONE_FREE = 'BOGOF';
	const BUY_QTY_GET_PERCENTAGE_OFF_ONE = 'BOGPOFF';
	const BUY_QTY_GET_VALUE_OFF_ONE = 'BOGVOFF';
}
