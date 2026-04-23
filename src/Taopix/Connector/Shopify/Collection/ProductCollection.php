<?php

namespace Taopix\Connector\Shopify\Collection;

class ProductCollection extends \Taopix\Core\Collection\Collection
{
	function __construct($array = array(), $pType = "\Taopix\Connector\Shopify\Entity\Product")
	{
		parent::__construct($array, $pType);
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
