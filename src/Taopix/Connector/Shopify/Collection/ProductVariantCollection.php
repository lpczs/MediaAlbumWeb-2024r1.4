<?php

namespace Taopix\Connector\Shopify\Collection;

class ProductVariantCollection extends \Taopix\Core\Collection\Collection
{
	function __construct($array = array(), $pType = "\Taopix\Connector\Shopify\Entity\ProductVariant")
	{
		parent::__construct($array, $pType);
	}

	/**
	 * Builds on array of properties for every ProductVariant in the collection.
	 *
	 * @return array Array of ProductVariant properties list.
	 */
	public function getProperties(): array
	{
		$propertyList = [];

		foreach($this as $productVariant)
		{
			$propertyList[] = $productVariant->getProperties();
		}

		return $propertyList;
	}
}
