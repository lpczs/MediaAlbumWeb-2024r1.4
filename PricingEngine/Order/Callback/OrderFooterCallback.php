<?php

namespace PricingEngine\Order\Callback;

use DatabaseObj;
use PricingEngine\OrderLineInterface;

/**
 * Order footer callbacks
 *
 * Order footer callbacks for the order class to
 * handle external system interactions,
 * e.g. database access, without affecting automated
 * tests.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class OrderFooterCallback
{
	/**
	 * Synchronise the order footer component counts with the
	 * order line product counterparts
	 *
	 * For order footer components that use product quantities, and where
	 * the product quantities have a minimum or maximum supported quantity
	 * by the price break, update the footer component counts to match
	 * the order line quantities.
	 *
	 * This should be done after the order line prices have been calculated
	 * to ensure the requested product quantity and the actual priced quantity
	 * correctly reflect the configured quantities for the product, otherwise
	 * the values will simply be equal which may be incorrect.
	 *
	 * @param mixed[] $session
	 * @param OrderLineInterface $orderLine
	 */
	public static function syncCounts(&$session, OrderLineInterface $orderLine)
	{
		// Return quickly if the quantities are the same
		if ($orderLine->getRequestedProductQuantity() == $orderLine->getProductQuantity()) {
			return;
		}

		// Perform synchronisation of updated product quantities with components
		$productCode = $orderLine->getProductCode();
		$quantityDelta = $orderLine->getProductQuantity() - $orderLine->getRequestedProductQuantity();

		$sections = DatabaseObj::getOrderSectionList(
			$productCode,
			$session['licensekeydata']['groupcode'],
			$session['userdata']['companycode'],
			'$ORDERFOOTER\\'
		);

		// For each footer component linked by the product of this order line, and for each footer
		// component in the session, find the matching component by its code
		if ($sections['result'] == '') {
			foreach ($sections['sections'] as $sectionCode) {
				foreach ($session['order']['orderFooterSections'] as &$orderFooterSection) {
					if ($orderFooterSection['sectioncode'] === $sectionCode) {
						// Section component found, update it using the delta but
						// only if the component should use the product quantity
						if ($orderFooterSection['orderfooterusesproductquantity'] == 1) {
							$orderFooterSection['itemqty'] += $quantityDelta;
							$orderFooterSection['itemqtyaccumulative'] += $quantityDelta;
						}

						// Handle section subcomponents
						$subSections = DatabaseObj::getOrderSectionList(
							$productCode,
							$session['licensekeydata']['groupcode'],
							$session['userdata']['companycode'],
							'$ORDERFOOTER\\$' . $sectionCode . '\\'
						);

						if ($subSections['result'] == '') {
							foreach ($subSections['sections'] as $subSectionCode) {
								foreach ($orderFooterSection['subsections'] as &$orderFooterSubSection) {
									if ($orderFooterSubSection['sectioncode'] === $subSectionCode) {
										// Section component found, update it using the delta but
										// only if the component should use the product quantity
										if ($orderFooterSubSection['orderfooterusesproductquantity'] == 1) {
											$orderFooterSubSection['itemqty'] += $quantityDelta;
											$orderFooterSubSection['itemqtyaccumulative'] += $quantityDelta;
										}

										break;
									}
								}
							}
						}

						// Handle checkbox subcomponents
						$checkboxes = DatabaseObj::getOrderCheckboxList(
							$productCode,
							$session['licensekeydata']['groupcode'],
							$session['userdata']['companycode'],
							'$ORDERFOOTER\\$' . $sectionCode . '\\'
						);

						if ($checkboxes['result'] == '') {
							foreach ($checkboxes['checkboxes'] as $subCheckboxCode) {
								foreach ($checkboxes['checkboxes'] as &$orderFooterSubCheckbox) {
									if ($orderFooterSubCheckbox['code'] === $subCheckboxCode) {
										// Section component found, update it using the delta but
										// only if the component should use the product quantity
										if ($orderFooterSubCheckbox['orderfooterusesproductquantity'] == 1) {
											$orderFooterSubCheckbox['itemqty'] += $quantityDelta;
											$orderFooterSubCheckbox['itemqtyaccumulative'] += $quantityDelta;
										}

										break;
									}
								}
							}
						}

						break;
					}
				}
			}
		}

		// Handle checkbox subcomponents
		$checkboxes = DatabaseObj::getOrderCheckboxList(
			$productCode,
			$session['licensekeydata']['groupcode'],
			$session['userdata']['companycode'],
			'$ORDERFOOTER\\'
		);

		if ($checkboxes['result'] == '') {
			foreach ($checkboxes['checkboxes'] as $checkboxCode) {
				foreach ($session['order']['orderFooterCheckboxes'] as &$orderFooterCheckbox) {
					if ($orderFooterCheckbox['code'] === $checkboxCode) {
						// Section component found, update it using the delta but
						// only if the component should use the product quantity
						if ($orderFooterCheckbox['orderfooterusesproductquantity'] == 1) {
							$orderFooterCheckbox['itemqty'] += $quantityDelta;
							$orderFooterCheckbox['itemqtyaccumulative'] += $quantityDelta;
						}

						break;
					}
				}
			}
		}
	}
}
