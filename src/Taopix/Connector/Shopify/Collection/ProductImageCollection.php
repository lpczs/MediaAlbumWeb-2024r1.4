<?php

namespace Taopix\Connector\Shopify\Collection;

class ProductImageCollection extends \Taopix\Core\Collection\Collection
{
	function __construct($array = array(), $pType = "\Taopix\Connector\Shopify\Entity\ProductImage")
	{
		parent::__construct($array, $pType);
	}

	/**
	 * Builds on array of properties for every ProductImage in the collection.
	 *
	 * @return array Array of ProductImages properties list.
	 */
	public function getProperties(): array
	{
		$propertyList = [];

		foreach($this as $productImage)
		{
			$propertyList[] = $productImage->getProperties();
		}

		return $propertyList;
	}
}
