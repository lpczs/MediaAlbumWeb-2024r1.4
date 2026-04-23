<?php

namespace Extension\Script\Callback;

use UtilsAddressObj;

class TaxExtensionScriptCallback
{
	/**
	 * @param mixed[] $session
	 * @param array $arguments
	 */
	public static function copyAddressFields(&$session, &$arguments)
	{
		UtilsAddressObj::copyArrayAddressFields($session['shipping'][0], 'shippingcustomer', $arguments['customershippingaddress'], 'shipping', false, true);
		UtilsAddressObj::copyArrayAddressFields($session['order'], 'billingcustomer', $arguments['customerbillingaddress'], 'billing', false, true);

		if (self::getTaxCalculationAddressPreference() == TPX_TAX_CALCULATION_BY_BILLING_ADDRESS) {
			UtilsAddressObj::copyArrayAddressFields($session['order'], 'billingcustomer', $arguments['taxcalculationaddress'], 'billing', false, true);
		} else {
			UtilsAddressObj::copyArrayAddressFields($session['shipping'][0], 'shippingcustomer', $arguments['taxcalculationaddress'], 'shipping', false, true);
		}

		$shippingMethodCode = $session['shipping'][0]['shippingmethodcode'];
		if ($session['shipping'][0]['shippingMethods'][$shippingMethodCode]['collectFromStore'] == true) {
			UtilsAddressObj::copyArrayAddressFields(
				$session['shipping'][0]['shippingMethods'][$shippingMethodCode]['storeAddress'],
				'storecustomer', $arguments['storeaddress'], 'store', false, true);
		} else {
			$arguments['storeaddress'] = [];
		}
	}

	/**
	 * @return int
	 */
	private static function getTaxCalculationAddressPreference()
	{
		global $gConstants;
		return $gConstants['taxaddress'];
	}
}
