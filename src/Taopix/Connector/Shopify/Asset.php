<?php

namespace Taopix\Connector\Shopify;

use Taopix\Connector\Shopify\Entity\Asset as AssetEntity;
use Taopix\Connector\Shopify\Entity\Theme;
use Taopix\Connector\Shopify\Collection\AssetCollection;

class Asset extends Theme
{
	/**
	 * @var Theme
	 */
	private $theme = null;

	public function __construct(\PHPShopify\ShopifySDK $pShopifySDK, Theme $pTheme)
	{
		$this->shopifySDK = $pShopifySDK;
		$this->theme = $pTheme;
	}

	/**
	 * Pushes the asset to Shopify.
	 *
	 * @param string $pKey The asset name.
	 * @param string $pValue The content of the asset.
	 */
	public function pushAsset(string $pKey, string $pValue): void
	{
		$asset = [
			"key" => $pKey,
			"theme_id" => $this->theme->getId(),
			"value" => $pValue
		];

		$this->shopifySDK->Theme($this->theme->getId())->Asset->put($asset);
	}

	/**
	 * Requests an asset by the key.
	 *
	 * @param string $pKey Filename of the asset to request.
	 * @return AssetEntity Asset entity instance of the asset.
	 */
	public function requestAsset(string $pKey): AssetEntity
	{
		$asset = [
			'asset' => [
				'key' => $pKey
			],
			'theme_id' => $this->theme->getId()
		];

		return AssetEntity::make($this->shopifySDK->Theme($this->theme->getId())->Asset()->get($asset)['asset']);
	}

	/**
	 * Requests all assets.
	 *
	 * @return AssetCollection Collection of assets for a theme.
	 */
	public function requestAssets(): AssetCollection
	{
		return new AssetCollection($this->shopifySDK->Theme($this->theme->getId())->Asset()->get());
	}
}
