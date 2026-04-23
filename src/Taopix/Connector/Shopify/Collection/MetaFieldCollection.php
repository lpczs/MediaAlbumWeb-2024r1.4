<?php

namespace Taopix\Connector\Shopify\Collection;

class MetaFieldCollection extends \Taopix\Core\Collection\Collection
{
	function __construct($array = array(), $pType = "\Taopix\Connector\Shopify\Entity\MetaField")
	{
		parent::__construct($array, $pType);
	}

	/**
	 * Returns a filtered MedtaFieldCollection of metafielfs that match the provided namespace and key.
	 *
	 * @param string $pNameSpace Namespace to filter on.
	 * @param string $pKey Key to filter on.
	 * @return MetaFieldCollection Filtered MetaFieldCollection.
	 */
	public function getByNameSpaceAndKey(string $pNameSpace, string $pKey): MetaFieldCollection
	{
		$result = [];

		if ($this->count() > 0)
		{
			$result = array_values(array_filter($this->getArrayCopy(), function($pMetaField) use ($pNameSpace, $pKey)
			{
				return (($pMetaField->getNamespace() === $pNameSpace) && ($pMetaField->getKey() === $pKey));
			}));
		}

		return new MetaFieldCollection($result);
	}

	/**
	 * Builds on array of properties for every Metafield in the collection.
	 *
	 * @return array Array of Metafield properties list.
	 */
	public function getProperties(): array
	{
		$propertyList = [];

		foreach($this as $metafield)
		{
			$propertyList[] = $metafield->getProperties();
		}

		return $propertyList;
	}
}
