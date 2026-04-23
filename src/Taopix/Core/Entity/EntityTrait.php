<?php

namespace Taopix\Core\Entity;

trait EntityTrait
{
	/**
	 * Makes a concrete class from the properties passed in.
	 *
	 * @param array $pEntityProperties
	 * @return self
	 */
	public static function make(array $pEntityProperties = []): self
	{
		$class = get_called_class();
		$instance = new $class();

		if (! empty($pEntityProperties))
		{
			$instance->populateInstance($pEntityProperties);
		}

		if (method_exists($instance, 'postHydrate'))
		{
			$instance->postHydrate();
		}

		return $instance;
	}

	/**
	 * Populate an object after formatting it's properties.
	 *
	 * @param array $pEntityProperties Array of properties to build the asset.
	 */
	public function populateInstance(array $pEntityProperties): void
	{
		$this->hydrate($this->preHydrate($pEntityProperties));
	}

	/**
	 * Before the data is hydrated check that the properties passed in a valid i.e. There is a class member for it.
	 *
	 * @param array $pEntityProperties
	 * @return array
	 */
	protected function preHydrate(array $pEntityProperties): array
	{
		return $pEntityProperties;
	}

	/**
	 * Add the data to the the relevant class member from the properties array.
	 *
	 * @param array $pEntityProperties
	 * @return void
	 */
	protected function hydrate(array $pEntityProperties)
	{
		$setItems = 0;

		foreach($pEntityProperties as $keyProperty => $property)
		{
			$methodName = 'set' . $keyProperty;

			try
			{
				$this->{$methodName}($property);
				$setItems++;
			}
			catch(\Error $e){}
		}

		if ($setItems === 0)
		{
			$keys = \array_keys($this->toArray());
			throw new \Exception("Invalid object array. Object must have one of the following: " . get_class($this) . '  '. \implode(', ', $keys));
		}
	}
}
