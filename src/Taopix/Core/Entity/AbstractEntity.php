<?php

namespace Taopix\Core\Entity;

/**
 * Abstract class for entities.
 *
 * @package Taopix\Core\Entity
 */
abstract class AbstractEntity
{
	use EntityTrait;

	/**
	 * Convert the item back to an array.
	 *
	 * @return array Array representation of the object.
	 */
	public function toArray(): array
	{
		return $this->executeToArray(function($pAttributeName, $pAttributeValue){
			return $pAttributeValue;
		});
	}

	/**
	 * Convert the item back to an array.
	 *
	 * @param function $pConversionFunction Function to perform extra data conversions etc.
	 * @return array Array representation of the object.
	 */
	public function executeToArray($pConversionFunction): array
	{
		$return = [];

		$reflected = new \ReflectionClass($this);

		$props = $reflected->getProperties();

		foreach ($props as $propDetails)
		{
			$attributeName = $propDetails->name;
			$getterName = 'get' . ucfirst($attributeName);
			$attributeValue = $this->{$getterName}();

			// If an object is returned, we need to get the array version of the object.
			if (is_object($attributeValue))
			{
				$return[strtolower($propDetails->name)] = $attributeValue->executeToArray($pConversionFunction);
			}
			else
			{
				$attributeValue = $pConversionFunction($attributeName, $attributeValue);
				$return[strtolower($propDetails->name)] = $attributeValue;
			}
		}

		return $return;
	}

	/**
	 * Get the properties of the entity as an array.
	 *
	 * @return array Entity properties.
	 */
	public function getProperties(): array
	{
		return [];
	}
}
