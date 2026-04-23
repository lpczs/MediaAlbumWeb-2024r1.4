<?php

namespace PricingEngine\Component;

use PricingEngine\Component\Callback\MetadataCallback;
use PricingEngine\Price\Price;
use PricingEngine\PriceBreakSet\Exception\PriceBreakNotFoundException;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Abstract checkbox component
 *
 * An abstract class for a checkbox component with basic
 * checkbox component handling functionality that is
 * non-specific to either order line or order footer
 * components.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class CheckboxComponent extends AbstractComponent
{
	/**
	 * @var string
	 */
	protected $metadataLoaderCallbackClassName;

	/**
	 * Constructor
	 *
	 * @param string $componentCode
	 * @param string $metadataLoaderCallbackClassName
	 */
	public function __construct($componentCode, $metadataLoaderCallbackClassName = MetadataCallback::class)
	{
		parent::__construct($componentCode);
		$this->metadataLoaderCallbackClassName = $metadataLoaderCallbackClassName;
	}

	/**
	 * Synchronise the database metadata with the existing metadata
	 *
	 * Synchronise the database metadata with the existing metadata
	 * passed as an associate array, and return the updated array.
	 *
	 * @param mixed[] $existingMetadata
	 * @return mixed[]
	 */
	public function syncMetadata($existingMetadata)
	{
		return call_user_func([$this->metadataLoaderCallbackClassName, 'sync'], $this->componentCode, $existingMetadata);
	}
}
