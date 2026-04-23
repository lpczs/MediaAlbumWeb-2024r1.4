<?php

namespace PricingEngine\Enum;

require_once __DIR__ . '/../../Utils/UtilsConstants.php';

/**
 * Product option enumeration
 *
 * Constants for each product option
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class ProductOption
{
	/**
	 * Per picture print pricing
	 */
	const PER_PICTURE = TPX_PRODUCTOPTION_PRICING_PERPICTURE;

	/**
	 * Per component/subcomponent print pricing
	 */
	const PER_COMPONENT_SUB_COMPONENT = TPX_PRODUCTOPTION_PRICING_PERCOMPONENTSUBCOMPONENT;
}
