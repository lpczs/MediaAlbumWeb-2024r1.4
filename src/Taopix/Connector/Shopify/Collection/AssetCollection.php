<?php

namespace Taopix\Connector\Shopify\Collection;

class AssetCollection extends \Taopix\Core\Collection\Collection
{
	function __construct($array = array(), $pType = "\Taopix\Connector\Shopify\Entity\Asset")
	{
		parent::__construct($array, $pType);
	}

	/**
	 * Returns list of assets that contain the provided prefix in the key.
	 *
	 * @param string $pKeyPrefix Key prefix to match on.
	 * @return AssetCollection Filtered asset collection.
	 */
	public function getAssetsByKeyPrefix(string $pKeyPrefix): AssetCollection
	{
		return new AssetCollection(array_values(array_filter($this->getArrayCopy(), function($pAsset) use ($pKeyPrefix)
		{
			return strpos($pAsset->getKey(), $pKeyPrefix) === 0;
		})));
	}
}
