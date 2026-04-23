<?php

namespace PricingEngine\Enum;

require_once(__DIR__ . '/../../Utils/UtilsConstants.php');

/**
 * Extension script enumeration
 *
 * Constants used for extension scripts.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class ExtensionScript
{
	/**
	 * Tax code used by tax scripts
	 */
	const TAX_SCRIPT_TAX_CODE = TPX_CUSTOMTAX;

	/**
	 * Get the extension path
	 *
	 * @return string
	 */
	public static function getExtensionPath()
	{
		return __DIR__ . '/../../Customise/scripts';
	}
}
