<?php

namespace Taopix\Connector\Shopify\Collection;

class ProductCollectionCollection extends \Taopix\Core\Collection\Collection
{
	function __construct($array = array(), $pType = "\Taopix\Connector\Shopify\Entity\ProductCollection")
	{
		parent::__construct($array, $pType);
	}

	/**
	 * Filter the items in the title by the value that is passed.
	 *
	 * @param string $pTitle Title to filter on.
	 * @return ProductCollectionCollection Updated collection.
	 */
	public function getByTitle(string $pTitle): ProductCollectionCollection
	{
		return new ProductCollectionCollection(array_values(array_filter($this->getArrayCopy(), function($pCollection) use ($pTitle)
		{
			return $pCollection->getTitle() === $pTitle;
		})));
	}

	/**
	 * Builds on array of properties for every Product in the collection.
	 *
	 * @return array Array of Product properties list.
	 */
	public function getProperties(): array
	{
		$propertyList = [];

		foreach($this as $product)
		{
			$propertyList[] = $product->getProperties();
		}

		return $propertyList;
	}
}
