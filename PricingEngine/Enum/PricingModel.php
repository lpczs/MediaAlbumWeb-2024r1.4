<?php

namespace PricingEngine\Enum;

require_once __DIR__ . '/../../Utils/UtilsConstants.php';

/**
 * Pricing model enumeration
 *
 * Constants for each pricing model
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class PricingModel
{
	/**
	 * Per product pricing
	 */
	const PER_QUANTITY = TPX_PRICINGMODEL_PERQTY;

	/**
	 * Per product, side pricing
	 */
	const PER_SIDE_QUANTITY = TPX_PRICINGMODEL_PERSIDEQTY;

	/**
	 * Per Product, component pricing
	 */
	const PER_PRODUCT_COMPONENT_QUANTITY = TPX_PRICINGMODEL_PERPRODCMPQTY;

	/**
	 * Per product, side component pricing
	 */
	const PER_SIDE_PER_PRODUCT_PER_COMPONENT_QUANTITY = TPX_PRICINGMODEL_PERSIDEPERPRODPERCMPQTY;
}
