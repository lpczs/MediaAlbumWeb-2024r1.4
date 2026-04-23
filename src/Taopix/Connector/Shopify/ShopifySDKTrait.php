<?php

namespace Taopix\Connector\Shopify;

require_once __DIR__ . '/../../../../libs/external/vendor/autoload.php';

trait ShopifySDKTrait
{
	/**
	 * Shopify API version to use.
	 * 
	 * @var string
	 */
	static $APIVERSION = '2024-01';

	public function configureShopifySDK(): void
	{
		$configParams = array(
			'ShopUrl' => $this->getShopURL(),
			'ApiKey' => $this->getApiKey(),
			'SharedSecret' => $this->getApiSecret(),
			'ApiVersion' => self::$APIVERSION
		);

		\PHPShopify\ShopifySDK::config($configParams);
	}

	public function initShopifySDK(): void
	{
		$config = array(
			'ShopUrl' => $this->getShopURL(),
			'AccessToken' => $this->getAccessToken(),
			'ApiVersion' => self::$APIVERSION
		);

		$this->setShopifySDK(new \PHPShopify\ShopifySDK($config));
	}
}
