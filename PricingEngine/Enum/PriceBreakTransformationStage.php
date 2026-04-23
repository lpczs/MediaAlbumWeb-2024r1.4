<?php

namespace PricingEngine\Enum;

require_once(__DIR__ . '/../../Utils/UtilsConstants.php');

class PriceBreakTransformationStage
{
	/**
	 * Transformation is performed prior to performing the
	 * pricing model calculations
	 */
	const PRE_TRANSFORM = TPX_PRICETRANSFORMATIONSTAGE_PRE;

	/**
	 * Transformation is performed after to performing the
	 * pricing model calculations
	 */
	const POST_TRANSFORM = TPX_PRICETRANSFORMATIONSTAGE_POST;
}
