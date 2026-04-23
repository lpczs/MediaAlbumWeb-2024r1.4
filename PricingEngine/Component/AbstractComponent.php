<?php

namespace PricingEngine\Component;

/**
 * Abstract component
 *
 * Abstract class of component functionality.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
abstract class AbstractComponent
{
	/**
	 * @var string
	 */
	protected $componentCode;

	/**
	 * Constructor
	 *
	 * @param string $componentCode
	 */
	public function __construct($componentCode)
	{
		$this->componentCode = $componentCode;
	}

	/**
	 * Get component code
	 *
	 * Get the component code of the component.
	 *
	 * @return string
	 */
	public function getComponentCode()
	{
		return $this->componentCode;
	}
}
