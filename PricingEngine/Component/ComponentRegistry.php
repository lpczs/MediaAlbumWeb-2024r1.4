<?php

namespace PricingEngine\Component;

/**
 * A registry of component instances
 *
 * A registry of component instances, recorded
 * by component code.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class ComponentRegistry
{
	/**
	 * @var AbstractComponent[]
	 */
	private static $instances = [];

	/**
	 * Clear the registry
	 *
	 * Clears the registry of all components.
	 */
	public static function clear()
	{
		self::$instances = [];
	}

	/**
	 * Get a component
	 *
	 * Get a component instance by component code
	 *
	 * @param $componentCode
	 * @return AbstractComponent
	 */
	public static function getInstance($componentCode)
	{
		return @self::$instances[$componentCode];
	}

	/**
	 * Set a component
	 *
	 * Set a component instance in the registry using the given
	 * component code and component instance
	 *
	 * @param string $componentCode
	 * @param AbstractComponent $component
	 */
	public static function setInstance($componentCode, AbstractComponent $component)
	{
		self::$instances[$componentCode] = $component;
	}
}
