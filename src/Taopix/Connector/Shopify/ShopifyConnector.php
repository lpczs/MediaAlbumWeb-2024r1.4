<?php

namespace Taopix\Connector\Shopify;

use Taopix\Connector\Shopify\Product;
use Taopix\Connector\Shopify\Webhooks;
use Taopix\Connector\Shopify\GraphQLTrait;
use Taopix\Connector\Shopify\Theme;
use Taopix\Connector\Shopify\ShopifySDKTrait;
use Taopix\Connector\Shopify\Entity\Product as ProductEntity;
use Taopix\API\AppData\API as AppDataAPI;
use Taopix\Connector\Shopify\Collection\MetaFieldCollection;
use Taopix\Connector\Shopify\EDLTrait;
use PricingEngine\Enum\FinancialPrecision;
use PricingEngine\BCMath;
use stdClass;
use Taopix\Connector\Shopify\ProductCollection;
use Taopix\Connector\Shopify\Collection\ProductCollectionCollection;
use Throwable;

class ShopifyConnector extends \Taopix\Connector\Connector
{
	use ShopifySDKTrait;
	use GraphQLTrait;
	use CurlTrait;
	use EDLTrait;

	/**
	 * @var int
	 */
	private $connectorType = 1;

	/**
	 * @var \PHPShopify\ShopifySDK
	 */
	private $shopifySDK;

	/**
	 * @var string
	 */
	private $shopURL = '';

	/**
	 * @var array
	 */
	private $scopes = [
		// Admin API
		'read_products',
		'read_script_tags',
		'write_script_tags',
		'write_products',
		'read_products',
		'read_themes',
		'write_themes',
		'read_locales',
		'write_locales',
		'read_orders',
		'write_product_listings',
		'read_customers',
		'write_customers',
		'read_discounts',
		'write_discounts',
		'read_price_rules',
		'write_price_rules',
		'write_files',
		'read_files',

		'read_shipping',
		'write_shipping',

		// StoreFront API
		'unauthenticated_read_product_listings',
		'unauthenticated_write_checkouts',
		'unauthenticated_write_customers'
	];

	private $themeErrors = [];

	/**
	 * @var string
	 */
	private $automaticBasicDiscountQuery = "query {
		automaticDiscountNodes {
		  edges {
			node {
			  id
			  automaticDiscount {
				... on DiscountAutomaticBasic {
				 title
				  __typename
				  customerGets {
					items {
					  __typename

					  ... on DiscountCollections {
						__typename
						collections {
						  edges {
							node {
							  id
							  products {
								edges {
								  node {
									id
									title
									product_id: metafield(
									  namespace: \"taopix\"
									  key: \"taopix_product_id\"
									) {
									  id
									  value
									}
								  }
								}
							  }
							  taopix_discount: metafield(namespace:\"taopix\", key:\"taopix_discount\") {
								id,
								value
							  }
							}
						  }
						}
					  }

					  ... on DiscountProducts {
						__typename
						products {
						  edges {
							node {
							  id
							  title
							  product_id: metafield(
								namespace: \"taopix\"
								key: \"taopix_product_id\"
							  ) {
								id
								value
							  }
							}
						  }
						}
						productVariants {
						  edges {
							node {
							  id
							  title
							  variant_id: metafield(
								namespace: \"taopix\"
								key: \"taopix_product_id\"
							  ) {
								id
								value
							  }
							  parent_product_id: product {
								id
							  }
							}
						  }
						}
					  }
					}
				  }
				  combinesWith {
					orderDiscounts
					shippingDiscounts
					productDiscounts
				  }
				}
			  }
			}
		  }
		}
	  }
	";

	/**
	 * @var string
	 */
	private $automaticBxgyDiscountQuery = "query {
		automaticDiscountNodes {
		  edges {
			node {
			  id
			  automaticDiscount {
				... on DiscountAutomaticBxgy {
				  title
				  __typename
				  customerBuys {
					items {
					  __typename
					  ... on DiscountCollections {
						__typename
						collections {
						  edges {
							node {
							  id
							  products {
								edges {
								  node {
									id
									title
									product_id: metafield(
									  namespace: \"taopix\"
									  key: \"taopix_product_id\"
									) {
									  id
									  value
									}
								  }
								}
							  }
							  taopix_discount: metafield(namespace:\"taopix\", key:\"taopix_discount\") {
								id,
								value
							  }
							}
						  }
						}
					  }
					}
					items {
					  __typename
					  ... on DiscountProducts {
						__typename
						productVariants {
						  edges {
							node {
							  id
							  title
							  variant_id: metafield(
								namespace: \"taopix\"
								key: \"taopix_product_id\"
							  ) {
								id
								value
							  }
							  parent_product_id: product {
								id
							  }
							}
						  }
						}
					  }
					}
					items {
					  __typename
					  ... on DiscountProducts {
						__typename
						products {
						  edges {
							node {
							  id
							  title
							  product_id: metafield(
								namespace: \"taopix\"
								key: \"taopix_product_id\"
							  ) {
								id
								value
							  }
							}
						  }
						}
					  }
					}
				  }
				  combinesWith {
					orderDiscounts
					shippingDiscounts
					productDiscounts
				  }
				}
			  }
			}
		  }
		}
	  }
	";

	 /**
	 * @var string
	 */
	private $priceRulesQuery = "query {
		priceRules {
		  __typename
		  edges {
			node {
			  id
			  title
			  target
			  combinesWith {
				orderDiscounts
				shippingDiscounts
				productDiscounts
			  }
			  itemEntitlements {
				collections{
				  edges {
					node {
					  id
					  products{
						edges {
						  node {
							id
							product_id: metafield(namespace:\"taopix\", key:\"taopix_product_id\") {
									id,
									value
							}
						  }
						}
					  }
					  taopix_discount: metafield(namespace:\"taopix\", key:\"taopix_discount\") {
							  id,
							  value
					  }
					}
				  }
				}
			  }
			  itemEntitlements {
				products{
				  edges {
					node {
					  id
					  product_id: metafield(namespace:\"taopix\", key:\"taopix_product_id\") {
							  id,
							  value
					  }
					}
				  }
				}
			  }
			  itemEntitlements {
				productVariants{
				  edges {
					node {
					  id
					  variant_id: metafield(namespace:\"taopix\", key:\"taopix_product_id\") {
						id,
						value
					  }
					  parent_product_id: product {
						id
					 }
					}
				  }
				}
			  }
			}
		  }
		}
	  }
	";

	/**
	 * Sets the Shopify SDK instance.
	 *
	 * @param \PHPShopify\ShopifySDK $pShopifySDK The Shopify SDK instance.
	 * @return ShopifyConnector ShopifyConnector instance.
	 */
	public function setShopifySDK(\PHPShopify\ShopifySDK $pShopifySDK): ShopifyConnector
	{
		$this->shopifySDK = $pShopifySDK;
		return $this;
	}

	/**
	 * Returns the Shopify SDk instance.
	 */
	public function getShopifySDK(): \PHPShopify\ShopifySDK
	{
		return $this->shopifySDK;
	}

	/**
	 * Sets the shop URL.
	 *
	 * @param string $pShopURL The shop URL to set.
	 * @return ShopifyConnector ShopifyConnector instance.
	 */
	public function setShopURL(string $pShopURL): ShopifyConnector
	{
		$this->shopURL = $pShopURL;
		return $this;
	}

	/**
	 * Returns the shop URL value.
	 *
	 * @return string The shop URL.
	 */
	public function getShopURL(): string
	{
		return $this->shopURL;
	}

	/**
	 * Sets the theme error array.
	 *
	 * @param array $pThemeErrors Theme error array to set.
	 * @return ShopifyConnector ShopifyConnector instance.
	 */
	public function setThemeErrors(array $pThemeErrors): ShopifyConnector
	{
		$this->themeErrors = $pThemeErrors;
		return $this;
	}

	/**
	 * Returns theme error array.
	 *
	 * @return array Theme error array.
	 */
	public function getThemeErrors(): array
	{
		return $this->themeErrors;
	}

	/**
	 * Retrieves the vendorname from the shop URL.
	 *
	 * @return string The vendor name.
	 */
	public function getVendorNameFromShopURL(): string
	{
		$matches = [];
		preg_match('/https:\/\/(.*).myshopify.com/', $this->getShopURL(), $matches);
		return $matches[1];
	}

	public function __construct($pShopURL)
	{
		$this->setShopURL('https://' . $pShopURL . '/');
		$vendorName = $this->getVendorNameFromShopURL();

		$queryArray = [
			'fields' => [
				'id',
				'connectorkey',
				'connectorurl',
				'connectorprimarydomain',
				'connectorsecret',
				'connectoraccesstoken1',
				'connectoraccesstoken2',
				'brandcode',
				'licensekeycode',
				'pricesincludetax'
			],
			'ref' => ['connectorurl'],
			'refvalue' => [$vendorName],
			'reftype' => ['s']
		];

		$connectorDetails = parent::__construct('shopify', $queryArray);
		$connectorPrimaryDomain = $connectorDetails['connectorprimarydomain'];
		$connectorPrimaryDomain = ($connectorPrimaryDomain != '') ? $connectorPrimaryDomain : $pShopURL;

		$this->setApiKey($connectorDetails['connectorkey'])
			->setApiSecret($connectorDetails['connectorsecret'])
			->setAccessToken($connectorDetails['connectoraccesstoken1'])
			->setOnlineURL($this->getUtils()->correctPath($connectorDetails['onlinedesignerurl']))
			->setOnlineUiURL($this->getUtils()->correctPath($connectorDetails['onlineuiurl']))
			->setControlCentreURL($this->getUtils()->correctPath($connectorDetails['weburl']))
			->setBrandControlCentreURL($this->getUtils()->correctPath($connectorDetails['brandurl']))
			->setLicenseKeyCode($connectorDetails['licensekeycode'])
			->setBrandCode($connectorDetails['brandcode'])
			->setPrimaryDomain($this->getUtils()->correctPath($connectorPrimaryDomain))
			->setPricesIncludeTax($connectorDetails['pricesincludetax'])
			->setACConfig($this->getUtils()->getACConfig())
			->setConnectorID($connectorDetails['id'])
			->setApplicationName($connectorDetails['applicationname']);
		$componentUpsellSettings = $this->getComponentUpSellConfig($this->getLicenseKeyCode());
		$this->setLineItemQtyProtected(($componentUpsellSettings & TPX_COMPONENT_UPSELL_ALLOW_PRODUCT_QTY));

		$this->configureShopifySDK();

		return $connectorDetails;
	}

	/**
	 * Verify's hash from Shopify.
	 *
	 * @return bool True if the hash is valid.
	 */
	public function verifyHash(): bool
	{
		return \PHPShopify\AuthHelper::verifyShopifyRequest();
	}

	/**
	 * Verifies the signature for a proxy call is valid.
	 *
	 * @param array $pData Array contains items used to build hmac.
	 * @return bool True if the signature matches.
	 */
	public function verifyProxyHash(array $pData): bool
	{
		$hmac_header = $pData['signature'];

		if (isset($pData['signature'])) {
			unset($pData['signature']);
		}

		//Create data string for the remaining url parameters
		$dataString = $this->buildHashString($pData);
		$hmac = hash_hmac('sha256', $dataString, $this->getApiSecret());

		return hash_equals($hmac, $hmac_header);
	}

	/**
	 * Verifies the signature for a webhook call is valid.
	 *
	 * @return bool True if the signature matches.
	 */
	public function verifyWebhookHash(string $pPayload): bool
	{
		// perform HMAC vaidation for webhooks
		$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
		$hmac = base64_encode(hash_hmac('sha256', $pPayload, $this->getApiSecret(), true));
		return hash_equals($hmac, $hmac_header);
	}


	/**
	 * Builds the hash string to authenicate requests are from Shopify.
	 *
	 * @param array $pData Data to use to build the hash string.
	 * @return string The generated hash string.
	 */
	public static function buildHashString(array $pData): string
	{
		// Sort alphabetically.
		ksort($pData);

		$paramStrings = [];
		foreach ($pData as $key => $value) {
			$paramStrings[] = "$key=$value";
		}

		return implode('', $paramStrings);
	}

	/**
	 * Functions to execute on install.
	 */
	public function install()
	{
		$this->requestAuth();
	}

	/**
	 * Subscribes to the required webhooks in Shopify.
	 */
	public function subscribeToWebhooks()
	{
		$graphQL = $this->initGraphQL();

		$webhooks = new Webhooks($graphQL, $this->getControlCentreURL());
		$webhooks->subscribe();
	}

	/**
	 * Functions to execute when the callback file is called.
	 */
	public function installCallBack(): void
	{
		//Check if connector already installed
		$installed = false;
		if ($this->getAccessToken() != '')
		{
			$installed = true;
		}

		$accessToken = $this->requestAccessToken();
		// Get the access token.

		$this->setAccessToken($accessToken);

		$brandCode = $this->getBrandCode();

		$this->updateConnector($brandCode, ['connectoraccesstoken1' => $accessToken]);

		// Initialise the Shopify SDK so we can make requests.
		$this->initShopifySDK();

		if (!$installed)
		{
			// Apply theme changes, e.g. push snippet, inject code.
			$this->applyThemeChanges();
		}

		// Subscribe to webhooks
		$this->subscribeToWebhooks();

		// Add a secret to store metafields.
		$this->pushStoreSecret();

		// Create product collections for tax levels.
		$this->createTaxCollections();

		// Update Delivery Profiles
		$this->deliveryProfileUpdateTask();
	}

	/**
	 * Requests all metafields for the shop.
	 *
	 * @return MetaFieldCollection Collection of shop metafields.
	 */
	public function requestShopMetaFields(): MetaFieldCollection
	{
		return new MetaFieldCollection($this->getShopifySDK()->Metafield()->get());
	}

	/**
	 * Add's a metafield to the store containing a UUID. This will be used to genertae a customer ID hash.
	 */
	private function pushStoreSecret(): void
	{
		// Check if he secret has already been set on the shop.
		$shopMetaFields = $this->requestShopMetaFields();
		$secretMetaField = $shopMetaFields->getByNameSpaceAndKey('taopix', 'secret');

		if ($secretMetaField->count() === 0) {
			// Shop does not have the secret metafield, so add it.

			$metaField = [
				'namespace' => 'taopix',
				'key' => 'secret',
				'value' => uniqid('tpx'),
				'type' => 'single_line_text_field',
				'owner_resource' => 'shop'
			];

			$this->getShopifySDK()->Metafield()->post($metaField);
		}
	}

	/**
	 * Get tax level collections from Shopify.
	 *
	 * @return array
	 * 			0 => Product collection instance.
	 * 			1 => ProductCollectionCollection containing the tax level collections.
	 */
	public function getTaxLevelCollections(): array
	{
		$graphQL = $this->initGraphQL();

		$productCollection = new ProductCollection($graphQL);
		return [$productCollection, $productCollection->getTaxLevelCollections()];
	}

	/**
	 * Creates the tax level collections in Shopify.
	 */
	private function createTaxCollections(): void
	{
		list($productCollection, $productCollectionCollection) = $this->getTaxLevelCollections();

		if ($productCollectionCollection->count() === 0) {
			$collectionsToCreate = new ProductCollectionCollection(
				[
					['title' => 'Tax Level 1'],
					['title' => 'Tax Level 2'],
					['title' => 'Tax Level 3'],
					['title' => 'Tax Level 4'],
					['title' => 'Tax Level 5']
				]
			);

			foreach ($collectionsToCreate as $collectionToCreate) {
				$productCollection->createCollection($collectionToCreate->getProperties());
			}
		}
	}

	/**
	 * Builds the callback URL.
	 *
	 * @return string The callback URL.
	 */
	public function buildRedirectURL(): string
	{
		return $this->getControlCentreURL() . 'Connectors/Shopify/callback.php';
	}

	/**
	 * Creates an authentication request for Shopify.
	 */
	private function requestAuth(): void
	{
		\PHPShopify\AuthHelper::createAuthRequest($this->scopes, $this->buildRedirectURL(), null, null, false);
	}

	/**
	 * Requests an access token from Shopify.
	 *
	 * @return string The access token.
	 */
	private function requestAccessToken(): string
	{
		return \PHPShopify\AuthHelper::getAccessToken();
	}

	/**
	 * Applies any required changes to the installed theme on Shopify.
	 *
	 * @param bool pPushTPXTheme - should the taopix theme be pushed
	 */
	public function applyThemeChanges(bool $pPushTPXTheme = true): void
	{
		$theme = new Theme($this->getShopifySDK(), $this->getUtils(), $this->getControlCentreURL());
		$theme->install($pPushTPXTheme);
		$this->setThemeErrors($theme->getThemeErrors());
	}

	/**
	 * Push Taopix Theme to shopify
	 */
	public function pushTaopixTheme(): void
	{
		$theme = new Theme($this->getShopifySDK(), $this->getUtils(), $this->getControlCentreURL());
		$theme->pushTaopixTheme();
		$this->setThemeErrors($theme->getThemeErrors());
	}

	/**
	 * Creates the temporary product in Shopify.
	 *
	 * @param array $pProjectData Project data to create the product from.
	 * @return array Result array.
	 */
	public function createTempProduct(array $pProjectData): array
	{
		$graphQL = $this->initGraphQL();
		$product = new Product($graphQL);
		$locale = $product->getPrimaryLocale()->getLocale();

		$productEntity = ProductEntity::make(
			[
				'title' => $product->getUtils()->getLocaleString($pProjectData['collectionname'],$locale),
				'descriptionHTML' => $pProjectData['description'],
				'vendor' => $this->getApplicationName(),
				'productType' => 'taopix',
				'handle' => $product->getUtils()->getLocaleString($pProjectData['collectionname'],$locale),
				'tags' => "taopix_hidden_product,cc_" . $pProjectData['collectioncode'] . ",pc_" . $pProjectData['productcode'],
				'published' => true,
				'images' => $pProjectData['images'],
				'variants' => $pProjectData['variants'],
				'metafields' => $pProjectData['metafields']
			]
		);

		$productToInsert = $productEntity->getProperties();

		unset($productToInsert['variants']['metafields']);
		unset($productToInsert['variants']['options']);
		unset($productToInsert['options']);

		$productdata = $product->insertProduct($productToInsert);

		return array('productdata' => $productdata, 'shoplocale' => $locale);
	}

	/**
	 * Uploads an image to Shopify's AWS bucket and assigns it to the supplied product.
	 *
	 * @param string $pProductID Product ID to assign the image to.
	 * @param array $pProductImageMutation GraphQL product image mutation data array.
	 *
	 * @return \stdClass The query result object from assignProductImage
	 */
	public function createTempProductImage(string $pProductID, array $pProductImageMutation): \stdClass
	{
		$graphQL = $this->initGraphQL();

		$srcList = [];
		$productImageMutation = array_map(function ($pMutation) use (&$srcList) {
			// Remove the src from the array, as it isn't part of the createStagedUploads mutation, but we will need it later.
			$srcList[] = ['src' => $pMutation['src'], 'filesize' => $pMutation['fileSize'], 'mimetype' => $pMutation['mimeType'], 'resource' => $pMutation['resource']];
			unset($pMutation['src']);
			return $pMutation;
		}, $pProductImageMutation);

		$generateStagedUploadsResult = $this->generateStagedUploads($graphQL, $productImageMutation);

		$result = [];
		$success = [];
		if (!array_key_exists('message', $generateStagedUploadsResult->stagedUploadsCreate->userErrors)) {
			foreach ($generateStagedUploadsResult->stagedUploadsCreate->stagedTargets as $stagedTarget) {
				$currentSrc = current($srcList);
				$putProductImageResult = $this->putProductImage($currentSrc, $stagedTarget);

				if ($putProductImageResult) {
					$success[] = [
						'originalSource' => $stagedTarget->resourceUrl,
						'alt' => '',
						'mediaContentType' => $currentSrc['resource']
					];
				}

				next($srcList);
			}
		}

		if (count($success) > 0) {
		 	//if no product id then we are doing a file input so just return the data
			if ($pProductID === '')
			{
				$result = (object) $success;
			}
			else
			{
		 		$result = $this->assignProductImage($graphQL, $pProductID, $success);
			}
		}

		return $result;
	}

	/**
	 * Stages an upload in Shopify.
	 *
	 * @param \GraphQL\Client $pGraphQL GraphQL client instance.
	 * @param array $pUploadDataMutation GraphQL mutation data array.
	 * @return \stdClass The query result object.
	 */
	private function generateStagedUploads(\GraphQL\Client $pGraphQL, array $pUploadDataMutation): \stdClass
	{
		$mutation = (new \GraphQL\Mutation('stagedUploadsCreate'))
			->setVariables([new \GraphQL\Variable('input', '[StagedUploadInput!]', true)])
			->setArguments(['input' => '$input'])
			->setSelectionSet([
				(new \GraphQL\Query('stagedTargets'))->setSelectionSet([
					'url',
					'resourceUrl',
					(new \GraphQL\Query('parameters'))->setSelectionSet([
						'name',
						'value'
					])
				]),
				(new \GraphQL\Query('userErrors'))->setSelectionSet([
					'field',
					'message'
				])
			]);

		return $this->runGraphQLQuery($pGraphQL, $mutation, false, ['input' => $pUploadDataMutation]);
	}

	/**
	 * Puts the product image onto Shopify's AWS bucket.
	 *
	 * @param array $pSource The source image data.
	 * @param object $pPutProductImage Result object for when the image was staged to be uploaded.
	 * @return bool True on successfull upload (HTTP code 200).
	 */
	private function putProductImage(array $pSource, object $pPutProductImage): bool
	{
		$headers = [
			'content_type' => $pSource['mimetype'],
			'x-aws-acl' => array_values(array_filter($pPutProductImage->parameters, function ($pParameter) {
				return $pParameter->name === 'acl';
			}))[0]->value,
			'Content-Type' => $pSource['mimetype']
		];

		list($httpCode) = $this->initCURL($pPutProductImage->url, [
			// Using a PUT method i.e. -XPUT
			CURLOPT_PUT => true,
			CURLOPT_INFILE => $pSource['src'],
			CURLOPT_INFILESIZE => $pSource['filesize'],
			CURLOPT_TIMEOUT => 0,
			CURLOPT_HTTPHEADER => $headers,
		]);

		return $httpCode === 200;
	}

	/**
	 * Assign an uploaded image to a product.
	 *
	 * @param \GraphQL\Client $pGraphQL GraphQL client instance.
	 * @param string $pProductID Product ID to assign the image to.
	 * @param array $pProductMutation GraphQL Mutation query array.
	 * @return \stdClass The query result object.
	 */
	private function assignProductImage(\GraphQL\Client $pGraphQL, string $pProductID, array $pProductMutation): \stdClass
	{
		$mutation = (new \GraphQL\Mutation('productCreateMedia'))
			->setVariables([
				new \GraphQL\Variable('productId', 'ID', true),
				new \GraphQL\Variable('media', '[CreateMediaInput!]', true)
			])
			->setArguments(['productId' => '$productId', 'media' => '$media'])
			->setSelectionSet(
				[
					(new \GraphQL\Query('media'))->setSelectionSet([
						(new \GraphQL\Query('preview'))->setSelectionSet([
							(new \GraphQL\Query('image'))->setSelectionSet([
								'originalSrc',
								'transformedSrc'
							]),
							'status'
						]),
						'status',
						(new \GraphQL\Query('... on MediaImage'))->setSelectionSet([
							'id'
						])
					]),
					(new \GraphQL\Query('product'))->setSelectionSet([
						'id',
						(new \GraphQL\Query('featuredImage'))->setSelectionSet([
							'id'
						])
					]),
					(new \GraphQL\Query('mediaUserErrors'))->setSelectionSet([
						'code',
						'field',
						'message'
					])
				]
			);

		$result = $this->runGraphQLQuery($pGraphQL, $mutation, false, ['productId' => $pProductID, 'media' => $pProductMutation]);

		return $result;
	}

	/**
	 * Calls the createProject2 to create a Taopix project.
	 *
	 * @throws \Exception If an error is returned from createProject2.
	 * @inheritDoc
	 */
	public function createProject(string $pProductID, string $pDeviceDetection, array $pCustomer, string $pLanguageCode, array $pCustomParams): array
	{
		$languageCode = str_replace('-', '_', $pLanguageCode);
		$endpoint = sprintf('%s?fsaction=OnlineAPI.createProject2&l=%s', $this->getControlCentreURL(), $languageCode);

		$data = [CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query(['id' => $pProductID, 'dd' => $pDeviceDetection,
																'customer' => $pCustomer, 'customparams' => $pCustomParams])];

		list($httpCode, $createProjectResponse) = $this->initCURL($endpoint, $data);

		$redirect = '';
		$onlineapiurl = '';

		if ($httpCode === 200) {
			if ($createProjectResponse->result === 0) {
				$redirect = $createProjectResponse->designurl;
				$onlineapiurl = $createProjectResponse->onlineapiurl;
			} else {
				throw new \Exception($createProjectResponse->resultmessage, $createProjectResponse->result);
			}
		} else {
			throw new \Exception('Unexpected response when creating project', $httpCode);
		}

		return ['redirecturl' => $redirect, 'onlineapiurl' => $onlineapiurl];
	}

	/**
	 * Inserts the order into Taopix and deleted the temporary product.
	 * Routes the project if needed.
	 *
	 * @param array $pPayloadArray Data from the Shopify webhook.
	 */
	public function ordersPaid(array $pPayloadArray): void
	{
		$orderNumber = $pPayloadArray['order_number'];
		$orderedProjectRefList = [];
		$priceMap = [];
		$shopifyLineItems = $pPayloadArray['line_items'];
		$customer = $pPayloadArray['customer'];
		$customerID = $customer['id'];
		$userAccount = [];
		$userID = 0;
		$topic = 'orders/paid';
		$lineQtyMismatch = false;

		$this->recordWebhookData('SHOPIFY', $topic, $pPayloadArray, $orderNumber);

		$orderExistsArray = $this->checkOrderExists($orderNumber);

		if (!$orderExistsArray['orderfound']) {
			if ($customerID) {
				try {
					// Initialise the Shopify SDK so we can make requests.
					$this->initShopifySDK();

					// Get the shop secret.
					$shopMetaFields = $this->requestShopMetaFields();
					$secretMetaField = $shopMetaFields->getByNameSpaceAndKey('taopix', 'secret')[0];

					// Set the customer ID as the hashed version.
					$customerAccount = [];
					$customerAccount['id'] = $this->generateUserIDHash($pPayloadArray['customer']['id'], $secretMetaField->getValue());
					$customerAccount['firstname'] = (!is_null($pPayloadArray['customer']['first_name'])) ? $pPayloadArray['customer']['first_name'] : '';
					$customerAccount['lastname'] = (!is_null($pPayloadArray['customer']['last_name'])) ? $pPayloadArray['customer']['last_name'] : '';

					$userAccount = $this->createUserAccount($customerAccount, true);
					$userID = $userAccount['recordid'];
				} catch (\Exception $pError) {
					throw new \Exception($pError->getMessage(), $pError->getCode());
				}
			}

			$totalDiscount = 0.00;
			$orderTotalItemSell = 0.00;
			$orderTotalItemSellWithTax = 0.00;
			$orderTotalWithoutDiscount = 0.00;
			$orderTotalWithoutTax = 0.00;
			$taxBeforeDiscount = 0.00;
			$totalTax = 0.00;
			$totalToPay = 0.00;
			$totalSell = 0.00;
			$includesTax = ($pPayloadArray['taxes_included'] == 1);
			$discountNames = [];
			$voucherDiscountValue = 0.00;
			$totalSellBeforeDiscount = 0.00;

			foreach ($shopifyLineItems as $lineItem) {
				$productid = $lineItem['product_id'];
				$productExists = $lineItem['product_exists'];

				if ( (!is_null($productid)) && ($productExists) )
				{
					foreach ($lineItem['properties'] as $property) {
						if ($property['name'] == '__taopix_project_thumbnail') {
							if ($property['value'] !== '')
							{
								$imageResult = EDLTrait::createProductImage($this, 'gid://shopify/Product/' . $productid, $property['value']);
							}
						}
					}
				}

				foreach ($lineItem['properties'] as $property) {
					if ($property['name'] == '__taopix_project_id') {
						$orderedProjectRefList[] = $property['value'];
						$taxMultiplier = isset($lineItem['tax_lines'][0]) ? 1.00 + $lineItem['tax_lines'][0]['rate'] : 1.00;

						$this->initShopifySDK();
						$shopify = $this->getShopifySDK();

						if ( (!is_null($productid)) && ($productExists) )
						{
							try {
								$updateResult = $shopify->Product($productid)->put(["published"=>false]);
							} catch(Throwable $e) {
								error_log(print_r($e, true));
							}
						}

						$discount = array_reduce($lineItem['discount_allocations'], function($pDiscountTotal, $pDiscountItem) use ($taxMultiplier, $includesTax) {
							return bcadd($pDiscountTotal, $pDiscountItem['amount'], FinancialPrecision::PLACES);
						}, 0.00);

						$tax = array_reduce($lineItem['tax_lines'], function ($pTaxTotal, $pTaxItem) {
							return bcadd($pTaxTotal, $pTaxItem['price'], FinancialPrecision::PLACES);
						}, 0.00);

						$priceIncludingTax = bcsub(bcmul($lineItem['price'], $lineItem['quantity'], FinancialPrecision::PLACES), $discount, FinancialPrecision::PLACES);
						$priceIncludingTaxBeforeDiscount = bcmul($lineItem['price'], $lineItem['quantity'], FinancialPrecision::PLACES);

						if (! $includesTax)
						{
							// Add the tax back on if tax is not included. The tax is not included in $lineItem['price'].
							$priceIncludingTax = bcmul($priceIncludingTax, $taxMultiplier, FinancialPrecision::PLACES);
							$priceIncludingTaxBeforeDiscount = bcmul($priceIncludingTaxBeforeDiscount, $taxMultiplier, FinancialPrecision::PLACES);
						}

						$priceExcludingTax = bcdiv($priceIncludingTax, $taxMultiplier, FinancialPrecision::PLACES);
						$priceExcludingTaxBeforeDiscount = bcdiv($priceIncludingTaxBeforeDiscount, $taxMultiplier, FinancialPrecision::PLACES);

						$priceMap[$property['value']] = [
							'tax' => $tax,
							'unitsell' => $lineItem['price'],
							'priceincludingtax' => $priceIncludingTax,
							'priceexcludingtax' => $priceExcludingTax,
							'priceincludingtaxbeforediscount' => $priceIncludingTaxBeforeDiscount,
							'priceexcludingtaxbeforediscount' => $priceExcludingTaxBeforeDiscount,
							'discount' => $discount,
							'taxrate' => ($lineItem['tax_lines'][0]['rate'] * 100),
							'taxname' => 'en ' . $lineItem['tax_lines'][0]['title'],
							'qty' => $lineItem['quantity']
						];

						// Multiple discounts could be applied so we build a list.
						array_map(function ($pDiscount) use ($pPayloadArray, &$discountNames) {
							$discountApplication = $pPayloadArray['discount_applications'][$pDiscount['discount_application_index']];

							if (count($discountApplication) > 0) {
								$discountNames[] = isset($discountApplication['code']) ? $discountApplication['code'] : $discountApplication['type'];
							}
						}, $lineItem['discount_allocations']);

						// Prices with discount.
						$priceTaxKey = ($includesTax) ? 'priceincludingtax' : 'priceexcludingtax';
						$price = $priceMap[$property['value']][$priceTaxKey];
						$priceWithoutTax = $priceMap[$property['value']]['priceexcludingtax'];
						$priceWithTax = $priceMap[$property['value']]['priceincludingtax'];
						$orderTotalItemSell = bcadd($orderTotalItemSell, $price, FinancialPrecision::PLACES);
						$orderTotalWithoutTax = bcadd($orderTotalWithoutTax, $priceWithoutTax, FinancialPrecision::PLACES);
						$orderTotalItemSellWithTax = bcadd($orderTotalItemSellWithTax, $priceWithTax, FinancialPrecision::PLACES);
						$taxBeforeDiscount = bcadd($taxBeforeDiscount, bcsub($priceWithTax, $priceWithoutTax, FinancialPrecision::PLACES), FinancialPrecision::PLACES);
						$totalSell = bcadd($totalSell, $price, FinancialPrecision::PLACES);
						$totalToPay = bcadd($totalToPay, $price, FinancialPrecision::PLACES);

						// Prices before discount.
						$priceTaxKeyBeforeDiscount = $priceTaxKey . 'beforediscount';
						$priceBeforeDiscount = $priceMap[$property['value']][$priceTaxKeyBeforeDiscount];
						$totalSellBeforeDiscount = bcadd($totalSellBeforeDiscount, $priceBeforeDiscount, FinancialPrecision::PLACES);
						$orderTotalWithoutDiscount = bcadd($orderTotalWithoutDiscount, $priceBeforeDiscount, FinancialPrecision::PLACES);

						$totalTax = bcadd($totalTax, $priceMap[$property['value']]['tax'], FinancialPrecision::PLACES);
						$voucherDiscountValue = bcadd($totalDiscount, $priceMap[$property['value']]['discount'], FinancialPrecision::PLACES);
					}
				}

				if (isset($imageResult[0]))
				{
					if (isset($imageResult[0]->productCreateMedia->product) && isset($imageResult[0]->productCreateMedia->product->featuredImage->id))
					{
						$moveMutation = ['id' => $imageResult[0]->productCreateMedia->product->featuredImage->id, 'newPosition' => '1'];

						if ( (!is_null($productid)) && ($productExists) )
						{
							$this->reorderProductImages($this->initGraphQL(), $productid, $moveMutation);
						}
					}
				}
			}

			if (count($orderedProjectRefList) > 0) {
				$dataAPI = new AppDataAPI();

				$authenticationResult = $dataAPI->authenticate();
				$result = $authenticationResult['error'];

				if ($result == '') {
					$projectOrderDataResult = $dataAPI->getProjectOrderData($orderNumber, $orderedProjectRefList);
					$result = $projectOrderDataResult['error'];
					$orderData = $projectOrderDataResult['orderdata'];
					$shoppingCartSessionRef = $projectOrderDataResult['orderdata']['sessionref'];

					// Get the first discount's name.
					// We only store 1 discount voucher code in the database.
					$discountName = '';
					$hasDiscount = false;

					if (count($discountNames) > 0) {
						reset($discountNames);
						$discountName = current($discountNames);
						$hasDiscount = true;
					}

					$orderData['headerarray']['userid'] = $userID;

					foreach ($orderData['cartarray'] as &$lineItem) {

						$priceData = $priceMap[$lineItem['projectref']];

						// if quantity has been selected in the designer we must check to make sure what has been sent back matches.
						// quantities cannot be set for photoprints so we can skip
						if (($this->lineItemQtyProtected) && ($lineItem['qty'] != $priceData['qty']) &&
							($lineItem['producttype']!= TPX_PRODUCTCOLLECTIONTYPE_PHOTOPRINTS) && ($lineItem['source'] == 1))
						{
							$lineQtyMismatch = true;
							break;
						}

						$lineItem['userid'] = $userID;
						$voucherApplied = ((bccomp($priceData['discount'], 0, $orderData['headerarray']['currencydecimalplaces']) === 1) ? 1 : 0);
						$lineItemTotalSell = bcadd($priceData[$priceTaxKey], 0, FinancialPrecision::PLACES);
						$lineItemTax = bcsub($priceData['priceincludingtax'], $priceData['priceexcludingtax'], FinancialPrecision::PLACES);
						$lineItemTaxBeforeDiscount = bcsub($priceData['priceincludingtaxbeforediscount'], $priceData['priceexcludingtaxbeforediscount'], FinancialPrecision::PLACES);

						// Copy price data scaled to use the currency decimal number.
						$lineItem['producttotalsell'] = BCMath::round($priceData[$priceTaxKeyBeforeDiscount], $orderData['headerarray']['currencydecimalplaces']);
						$lineItem['subtotal'] = BCMath::round($priceData[$priceTaxKeyBeforeDiscount], $orderData['headerarray']['currencydecimalplaces']);
						$lineItem['productunitsell'] = BCMath::round($priceData['unitsell'], $orderData['headerarray']['currencydecimalplaces']);
						$lineItem['totalsell'] = BCMath::round($lineItemTotalSell, $orderData['headerarray']['currencydecimalplaces']);
						$lineItem['taxname'] = $priceData['taxname'];
						$lineItem['taxrate'] = $priceData['taxrate'];
						$lineItem['discountvalue'] = BCMath::round($priceData['discount'], $orderData['headerarray']['currencydecimalplaces']);
						$lineItem['voucherapplied'] = $voucherApplied;
						$lineItem['qty'] = $priceData['qty'];
						$lineItem['producttotaltax'] = ($includesTax) ? $lineItemTaxBeforeDiscount : 0.00;
						$lineItem['taxtotal'] = $lineItemTax;
					}

					if (!$lineQtyMismatch)
					{
						$orderData = $this->routeOrderItems($orderData);

						if (! $includesTax)
						{
							$totalToPay = bcadd($totalToPay, $totalTax, FinancialPrecision::PLACES);
						}

						// Convert prices to use currency decimal places.
						$orderTotalItemSell = BCMath::round($orderTotalItemSell, $orderData['headerarray']['currencydecimalplaces']);
						$totalTax = BCMath::round($totalTax, $orderData['headerarray']['currencydecimalplaces']);
						$taxBeforeDiscount = BCMath::round($taxBeforeDiscount, $orderData['headerarray']['currencydecimalplaces']);
						$orderTotalWithoutDiscount = BCMath::round($orderTotalWithoutDiscount, $orderData['headerarray']['currencydecimalplaces']);
						$totalDiscount = BCMath::round($totalDiscount, $orderData['headerarray']['currencydecimalplaces']);
						$orderTotalItemSellWithTax = BCMath::round($orderTotalItemSellWithTax, $orderData['headerarray']['currencydecimalplaces']);
						$totalToPay = BCMath::round($totalToPay, $orderData['headerarray']['currencydecimalplaces']);
						$orderTotalWithoutTax = BCMath::round($orderTotalWithoutTax, $orderData['headerarray']['currencydecimalplaces']);
						$totalSell = BCMath::round($totalSell, $orderData['headerarray']['currencydecimalplaces']);
						$voucherDiscountValue = BCMath::round($voucherDiscountValue, $orderData['headerarray']['currencydecimalplaces']);
						$totalSellBeforeDiscount = BCMath::round($totalSellBeforeDiscount, $orderData['headerarray']['currencydecimalplaces']);

						// update order totals.
						$orderData['headerarray']['ordertotalitemsell'] = $orderTotalItemSell;
						$orderData['headerarray']['ordertotalitemtax'] = $taxBeforeDiscount;
						$orderData['headerarray']['totalsellbeforediscount'] = $totalSellBeforeDiscount;
						$orderData['headerarray']['ordertotaltaxbeforediscount'] = $taxBeforeDiscount;
						$orderData['headerarray']['totalbeforediscount'] = $orderTotalWithoutDiscount;
						$orderData['headerarray']['ordertotaldiscount'] = $voucherDiscountValue;
						$orderData['headerarray']['ordertotalitemsellwithtax'] = $orderTotalItemSellWithTax;
						$orderData['headerarray']['ordertotalitemsell'] = $orderTotalItemSell;
						$orderData['headerarray']['ordertotaltax'] = $totalTax;
						$orderData['headerarray']['ordertotal'] = $totalToPay;
						$orderData['headerarray']['totalsell'] = $totalSell;
						$orderData['headerarray']['showpriceswithtax'] = $pPayloadArray['taxes_included'];

						// update voucher data.
						$orderData['headerarray']['vouchercode'] = $discountName;
						$orderData['headerarray']['vouchername'] = (($hasDiscount) ? 'en ' . $discountName : '');

						if ($hasDiscount) {
							$orderData['headerarray']['voucherdiscountsection'] = 'TOTAL';
							$orderData['headerarray']['voucherdiscounttype'] = 'VALUE';
						}
						$orderData['headerarray']['voucherdiscountvalue'] = $voucherDiscountValue;

						// update billing data
						$orderData['headerarray']['billingcustomeraddress1'] = (is_null($pPayloadArray['billing_address']['address1']) ? '' : $pPayloadArray['billing_address']['address1']);
						$orderData['headerarray']['billingcustomeraddress2'] =  (is_null($pPayloadArray['billing_address']['address2']) ? '' : $pPayloadArray['billing_address']['address2']);
						$orderData['headerarray']['billingcustomercity'] = (is_null($pPayloadArray['billing_address']['city']) ? '' : $pPayloadArray['billing_address']['city']);
						$orderData['headerarray']['billingcustomerstate'] = (is_null($pPayloadArray['billing_address']['province']) ? '' : $pPayloadArray['billing_address']['province']);
						$orderData['headerarray']['billingcustomerregioncode'] = (is_null($pPayloadArray['billing_address']['province_code']) ? '' : $pPayloadArray['billing_address']['province_code']);
						$orderData['headerarray']['billingcustomerregion'] = 'PROVINCE';
						$orderData['headerarray']['billingcustomerpostcode'] = (is_null($pPayloadArray['billing_address']['zip']) ? '' : $pPayloadArray['billing_address']['zip']);
						$orderData['headerarray']['billingcustomercountrycode'] = (is_null($pPayloadArray['billing_address']['country_code']) ? '' : $pPayloadArray['billing_address']['country_code']);
						$orderData['headerarray']['billingcustomercountryname'] = (is_null($pPayloadArray['billing_address']['country']) ? '' : $pPayloadArray['billing_address']['country']);
						$orderData['headerarray']['billingcustomeremailaddress'] = (is_null($pPayloadArray['customer']['email']) ? '' : $pPayloadArray['customer']['email']);
						$orderData['headerarray']['billingcontactfirstname'] = (is_null($pPayloadArray['billing_address']['first_name']) ? '' : $pPayloadArray['billing_address']['first_name']);
						$orderData['headerarray']['billingcontactlastname'] = (is_null($pPayloadArray['billing_address']['last_name']) ? '' : $pPayloadArray['billing_address']['last_name']);

						// update shipping data
						$orderData['shippingdata']['shippingmethodcode'] = $pPayloadArray['shipping_lines'][0]['code'];
						$orderData['shippingdata']['shippingmethodname'] = 'en ' . $pPayloadArray['shipping_lines'][0]['title'];
						$orderData['shippingdata']['shippingcustomername'] = (is_null($pPayloadArray['shipping_address']['name']) ? '' : $pPayloadArray['shipping_address']['name']);
						$orderData['shippingdata']['shippingcustomeraddress1'] = (is_null($pPayloadArray['shipping_address']['address1']) ? '' : $pPayloadArray['shipping_address']['address1']);
						$orderData['shippingdata']['shippingcustomeraddress2'] = (is_null($pPayloadArray['shipping_address']['address2']) ? '' : $pPayloadArray['shipping_address']['address2']);
						$orderData['shippingdata']['shippingcustomercity'] = (is_null($pPayloadArray['shipping_address']['city']) ? '' : $pPayloadArray['shipping_address']['city']);
						$orderData['shippingdata']['shippingcustomerstate'] = (is_null($pPayloadArray['shipping_address']['province']) ? '' : $pPayloadArray['shipping_address']['province']);
						$orderData['shippingdata']['shippingcustomerregioncode'] = (is_null($pPayloadArray['shipping_address']['province_code']) ? '' : $pPayloadArray['shipping_address']['province_code']);
						$orderData['shippingdata']['shippingcustomerregion'] = 'PROVINCE';
						$orderData['shippingdata']['shippingcustomerpostcode'] = (is_null($pPayloadArray['shipping_address']['zip']) ? '' : $pPayloadArray['shipping_address']['zip']);
						$orderData['shippingdata']['shippingcustomercountrycode'] = (is_null($pPayloadArray['shipping_address']['country_code']) ? '' : $pPayloadArray['shipping_address']['country_code']);
						$orderData['shippingdata']['shippingcustomercountryname'] = (is_null($pPayloadArray['shipping_address']['country']) ? '' : $pPayloadArray['shipping_address']['country']);
						$orderData['shippingdata']['shippingcustomeremailaddress'] = (is_null($pPayloadArray['customer']['email']) ? '' : $pPayloadArray['customer']['email']);
						$orderData['shippingdata']['shippingcontactfirstname'] = (is_null($pPayloadArray['shipping_address']['first_name']) ? '' : $pPayloadArray['shipping_address']['first_name']);
						$orderData['shippingdata']['shippingcontactlastname'] = (is_null($pPayloadArray['shipping_address']['last_name']) ? '' : $pPayloadArray['shipping_address']['last_name']);

						// check to make sure we have either no error or a partial order
						if ($result == 0 || $result == 4) {
							$insertOrderDataResult = $dataAPI->insertOrder($orderData);
							$result = $insertOrderDataResult['error'];

							if ($result == '') {
								$ordertimestamp = strtotime($pPayloadArray['created_at']);
								$orderDate = date('Y-m-d H:i:s', $ordertimestamp);

								$this->getUtils()->updateProjectOrderDataCache($orderedProjectRefList, $orderDate, $orderNumber);

								// Delete the cache file for Desktop orders.
								self::deleteCheckoutFile($shoppingCartSessionRef);
							} else {
								/*
								an error occurred while inserting the order
								*/

								/*
								call cancelOrder action to kill the shopping cart session
								*/
								$dataAPI->cancelOrder($shoppingCartSessionRef);
							}
						} else {
							/*
							an error occurred while requesting the order data
							*/

							/*
							call cancelOrder action to kill the shopping cart session
							*/
							$dataAPI->cancelOrder($shoppingCartSessionRef);
						}
					}
					else
					{
						/*
						one or more line item quantities do not match
						*/

						/*
						call cancelOrder action to kill the shopping cart session
						*/
						$dataAPI->cancelOrder($shoppingCartSessionRef);
					}

					/*
					call endSession action to end the api session
					*/
					$dataAPI->endSession();
				} else {
					//authentication result error occured;
				}
			} else {
				// order does not contain any Taopix projects so we can ignore the webhook
			}
		} else {
			// we have already processed the order previously.
		}
	}

	/**
	 * Calls the editProject to be able to edit a Taopix project.
	 *
	 * @throws \Exception If an error is returned from editProject.
	 * @inheritDoc
	 */
	public function editProject(string $pProjectRef, string $pCustomerID, string $pDeviceDetection, string $pLanguageCode): string
	{
		$endpoint = sprintf('%s?fsaction=OnlineAPI.editProject&projectref=%s&customerid=%s&dd=%s&l=%s', $this->getControlCentreURL(), $pProjectRef, $pCustomerID, $pDeviceDetection, $pLanguageCode);

		list($httpCode, $editProjectResponse) = $this->initCURL($endpoint, []);

		$redirect = '';
		if ($httpCode === 200) {
			if ($editProjectResponse->result === 0) {
				$redirect = $editProjectResponse->designurl;
			} else {
				throw new \Exception($editProjectResponse->resultmessage, $editProjectResponse->result);
			}
		} else {
			throw new \Exception('Unexpected response when editing project', $httpCode);
		}

		return $redirect;
	}

	/**
	 * Calls the duplicateProject to be able to duplicate a Taopix project, and then
	 * calls editProject to open it for editing.
	 *
	 * @throws \Exception If an error is returned from duplicateProject.
	 * @inheritDoc
	 */
	public function duplicateProject(string $pProjectRef, string $pProjectName, string $pCustomerID, string $pDeviceDetection, string $pLanguageCode): string
	{
		$endpoint = sprintf('%s?fsaction=OnlineAPI.duplicateProject&projectref=%s&projectname=%s&fromcart=1&l=%s', $this->getControlCentreURL(), $pProjectRef, urlencode($pProjectName), $pLanguageCode);

		list($httpCode, $duplicateProjectResponse) = $this->initCURL($endpoint, []);

		$redirect = '';
		if ($httpCode === 200) {
			if ($duplicateProjectResponse->result === 0) {
				$redirect = $this->editProject($duplicateProjectResponse->projectref, $pCustomerID, $pDeviceDetection, $pLanguageCode);
			} else {
				throw new \Exception($duplicateProjectResponse->resultmessage, $duplicateProjectResponse->result);
			}
		} else {
			throw new \Exception('Unexpected response when duplicating project', $httpCode);
		}

		return $redirect;
	}

	/**
	 * Calls the previewProject to be able to show a preview of the project.
	 *
	 * @throws \Exception If an error is returned from previewProject.
	 * @inheritDoc
	 */
	public function previewProject(string $pProjectRef, string $pDeviceDetection, $pLanguageCode): string
	{
		$endpoint = sprintf('%s?fsaction=OnlineAPI.previewProject&projectref=%s&dd=%s&l=%s', $this->getControlCentreURL(), $pProjectRef, $pDeviceDetection, $pLanguageCode);

		list($httpCode, $previewProjectResponse) = $this->initCURL($endpoint, []);

		$redirect = '';
		if ($httpCode === 200) {
			if ($previewProjectResponse->result === 0) {
				$redirect = $previewProjectResponse->designurl;
			} else {
				throw new \Exception($previewProjectResponse->resultmessage, $previewProjectResponse->result);
			}
		} else {
			throw new \Exception('Unexpected response when previewing project', $httpCode);
		}

		return $redirect;
	}

	/**
	 * Read Results of bulk operation and dependant on mode choose whether to insert or
	 * update record in CONNECTORSPRODUCTCOLLECTIONLINK table
	 *
	 * @param string $pBulkOperationID ID of the bulk operation
	 * @param string $pMode INSERT OR UPDATE
	 * @return string result of the insert / update operation
	 */
	public function shopifyProductLink(string $pBulkOperationID, string $pMode, string $pBrandCode): array
	{
		$ac_config = $this->getACConfig();
		$result = [];
		$return = [];
		$utils = $this->getUtils();

		$vendor = $this->getVendorNameFromShopURL();

		$bulkOperationID = str_replace("gid://shopify/BulkOperation/", "", $pBulkOperationID);

		$filePath = $ac_config['CONNECTORRESOURCESPATH'];
		$filePath = $utils->correctPath($filePath, DIRECTORY_SEPARATOR, true);

		if ($pBrandCode != '')
		{
			$filePath .= $pBrandCode . DIRECTORY_SEPARATOR;
		}

		$resultsFileName = $filePath . 'bulk-' . $bulkOperationID . '.jsonl';

		$bulkResultsJSONL = $utils->readTextFile($resultsFileName);
		$bulkResultsArray = preg_split('/\n|\r\n?/', $bulkResultsJSONL);

		$arrayCollectionData = [];
		$currentProductLinkData = $this->getShopifyProductLinkData();

		foreach ($bulkResultsArray as $result) {
			if ($result != '') {
				$thisProduct = '';

				if (isset(json_decode($result)->errors)) {
					error_log(print_r(json_decode($result)->errors[0]->message, true));
				}

				if ($pMode === 'INSERT') {
					if (isset(json_decode($result)->data->productCreate->product)) {
						$thisProduct = json_decode($result)->data->productCreate->product;
					}
				} else {
					if (isset(json_decode($result)->data->productUpdate->product)) {
						$thisProduct = json_decode($result)->data->productUpdate->product;
					}

					if (isset($currentProductLinkData[$thisProduct->metafield->value])) {
						$currentMetaData = json_decode($currentProductLinkData[$thisProduct->metafield->value]['metadata']);

						if (isset($currentMetaData->images)) {
							$thisProduct->images = $currentMetaData->images;
						}
					}
				}

				if (is_object($thisProduct)) {
					$arrayCollectionData[] = [
						$thisProduct->metafield->value,
						$thisProduct->id,
						json_encode($thisProduct),
						$thisProduct->updatedAt,
						$thisProduct->createdAt
					];
				}
			}
		}

		if (count($arrayCollectionData) > 0) {
			if ($pMode === 'INSERT') {
				$return = $this->insertShopifyProductLink($arrayCollectionData);
			} else {
				$return = $this->updateShopifyProductLink($arrayCollectionData);
			}

			if ($return['result'] === 'success') {
				$this->cleanUpConnectorResources($arrayCollectionData);
			}
		} else {
			$return['resultParam'] = 'failiure';
			$return['result'] = 'failiure';
		}

		return $return;
	}

	/**
	 * Insert results of productCreate bulk operation to CONNECTORSPRODUCTCOLLECTIONLINK table
	 *
	 * @param array pArrayCollectionData array of data to insert into CONNECTORSPRODUCTCOLLECTIONLINK
	 * @return bool success
	 */
	public function insertShopifyProductLink(array $pArrayCollectionData): array
	{
		$vendor = $this->getVendorNameFromShopURL();
		$db = $this->getUtils()->getGlobalDBConnection();

		$result = '';
		$resultParam = '';
		$resultArray = array();

		$collectioncode = '';
		$connectorproduct_id  = '';
		$connectorproduct_datcreated  = '';
		$connectorproduct_dateupdated  = '';
		$metadata  = '';
		$metadataDataLength = 0;

		$sql = 'INSERT INTO `CONNECTORSPRODUCTCOLLECTIONLINK`
		(	`connectorurl`
			,`collectioncode`
			,`connectorproduct_id`
			,`connectorproduct_datecreated`
			,`connectorproduct_dateupdated`
			,`metadata`
			,`metadatalength`
		)
		SELECT ? AS `connectorurl`, ? AS `collectioncode`, ? AS `connectorproduct_id`, ? AS `connectorproduct_datecreated`,
			? AS `connectorproduct_dateupdated`, ? AS `metadata`, ? AS `metadatalength`
			FROM DUAL
		WHERE NOT EXISTS (
			SELECT `collectioncode` FROM `CONNECTORSPRODUCTCOLLECTIONLINK` WHERE `collectioncode` = ? AND `connectorurl` = ?
		)';

		if ($db) {
			$stmt = $db->prepare($sql);
			if ($stmt) {
				if ($stmt->bind_param(
					'sssssssss',
					$vendor,
					$collectioncode,
					$connectorproduct_id,
					$connectorproduct_datcreated,
					$connectorproduct_dateupdated,
					$metadata,
					$metadataDataLength,
					$collectioncode,
					$vendor
				)) {
					foreach ($pArrayCollectionData as $collection) {
						$collectioncode = $collection[0];
						$connectorproduct_id = $collection[1];
						$metadata = $collection[2];
						$connectorproduct_dateupdated = $collection[3];
						$connectorproduct_datcreated = $collection[4];

						$metadataDataLength = strlen($metadata);

						if ($metadataDataLength > 15728640) {
							$metadata = gzcompress($metadata, 9);
						} else {
							$metadataDataLength = 0;
						}

						if (!$stmt->execute()) {
							$result = 'str_DatabaseError';
							$resultParam = 'insertShopifyProductLink execute ' . $db->error;
						} else {
							$result = 'success';
						}
					}
				} else {
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'insertShopifyProductLink bind ' . $db->error;
				}
				if ($stmt) {
					$stmt->free_result();
					$stmt->close();
				}
			} else {
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'insertShopifyProductLink prepare ' . $db->error;
			}

			$db->close();
		} else {
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'insertShopifyProductLink connect ' . $db->error;
		}

		$resultArray['result'] = $result;
		$resultArray['resultParam'] = $resultParam;

		return $resultArray;
	}

	/**
	 * get data from CONNECTORSPRODUCTCOLLECTIONLINK table
	 *
	 * @return array
	 */
	public function getShopifyProductLinkData(): array
	{
		$vendor = $this->getVendorNameFromShopURL();
		$db = $this->getUtils()->getGlobalDBConnection();

		$result = '';
		$resultParam = '';
		$resultArray = array();

		$collectioncode = '';
		$metadata = '';
		$metadataDataLength = 0;

		if ($db) {
			$stmt = $db->prepare('	SELECT `collectioncode`, `metadata`, `metadatalength`
									FROM `CONNECTORSPRODUCTCOLLECTIONLINK`
									WHERE `connectorurl` = ?
								');

			if ($stmt)
			{
				// bind param d
				if ($stmt->bind_param('s', $vendor))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0) {
								if ($stmt->bind_result($collectioncode, $metadata, $metadataDataLength)) {
									while ($stmt->fetch()) {

										if ($metadataDataLength > 0) {
											$metadata = gzuncompress($metadata, $metadataDataLength);
										}

										$resultArray[$collectioncode] = [
											'collectioncode' => $collectioncode,
											'metadata' => $metadata
										];
									}
								} else {
									$returnArray['error'] = __FUNCTION__ . ' bind result error: ' . $db->error;
								}
							}
						}
						else
						{
							$resultArray['result'] = __FUNCTION__ . ' store result ' . $db->error;
						}

						$stmt->free_result();
						$stmt->close();
						$stmt = null;
					}
					else
					{
						$resultArray['result'] = __FUNCTION__ . ' execute  ' . $db->error;
					}
				}
				else
				{
					$resultArray['result'] = __FUNCTION__ . ' bind params  ' . $db->error;
				}
			}
			else
			{
				$resultArray['result'] = __FUNCTION__ . ' prepare  ' . $db->error;
			}
			$db->close();
		} else {
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = __FUNCTION__ . ' connect ' . $db->error;
		}

		$resultArray['result'] = $result;
		$resultArray['resultParam'] = $resultParam;

		return $resultArray;
	}

	/**
	 * Update results of productUpdate bulk operation to CONNECTORSPRODUCTCOLLECTIONLINK table
	 *
	 * @param array pArrayCollectionData array of data to update the CONNECTORSPRODUCTCOLLECTIONLINK
	 * @return bool success
	 */
	public function updateShopifyProductLink($pArrayCollectionData): array
	{
		$vendor = $this->getVendorNameFromShopURL();
		$db = $this->getUtils()->getGlobalDBConnection();

		$result = '';
		$resultParam = '';
		$resultArray = array();

		$connectorproduct_dateupdated = '';
		$metadata = '';
		$metadataDataLength = '';
		$collectioncode = '';
		$connectorproduct_id = '';

		if ($db) {
			$stmt = $db->prepare('	UPDATE `CONNECTORSPRODUCTCOLLECTIONLINK`
									SET	`connectorproduct_dateupdated` = ?
										,`metadata` = ?
										,`metadatalength` = ?
									WHERE `collectioncode` = ?
									AND `connectorproduct_id` = ?
									AND `connectorurl` = ?
								');

			if ($stmt) {
				if ($stmt->bind_param(

					'ssisss',
					$connectorproduct_dateupdated, $metadata, $metadataDataLength, $collectioncode, $connectorproduct_id, $vendor
				)) {
					foreach ($pArrayCollectionData as $collection) {
						$collectioncode = $collection[0];
						$connectorproduct_id = $collection[1];
						$metadata = $collection[2];
						$connectorproduct_dateupdated = $collection[3];

						$metadataDataLength = strlen($metadata);

						if ($metadataDataLength > 15728640) {
							$metadata = gzcompress($metadata, 9);
						} else {
							$metadataDataLength = 0;
						}

						if (!$stmt->execute()) {
							$result = 'str_DatabaseError';
							$resultParam = 'updateShopifyProductLink execute ' . $db->error;
						} else {
							$result = 'success';
						}
					}
				} else {

					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'updateShopifyProductLink bind ' . $db->error;
				}
				if ($stmt) {
					$stmt->free_result();
					$stmt->close();
				}
			} else {
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'updateShopifyProductLink prepare ' . $db->error;
			}

			$db->close();
		} else {
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'updateShopifyProductLink connect ' . $db->error;
		}

		$resultArray['result'] = $result;
		$resultArray['resultParam'] = $resultParam;

		return $resultArray;
	}

	/**
	 * Add a list of products to a collection.
	 *
	 * @param string $pCollectionID Collection ID to add the products to.
	 * @param array $pProductIDList List of product IDs to add to the collection.
	 * @return \stdClass Result object.
	 */
	public function addProductsToCollection(string $pCollectionID, array $pProductIDList)
	{
		$graphQL = $this->initGraphQL();

		$collection = new ProductCollection($graphQL);
		return $collection->addProductsToCollection($pCollectionID, $pProductIDList);
	}

	/**
	 * Generates the Shopify customer hash to be used as the account code in Taopix.
	 *
	 * @param int $pCustomerID Shopify customer ID.
	 * @param string $pSecret Shop secrret to use as a salt.
	 * @return string The hashed customer ID.
	 */
	public function generateUserIDHash(int $pCustomerID, string $pSecret): string
	{
		return hash('sha1', $pCustomerID . $pSecret);
	}

	/**
	 * Creates a data deletion task for Taopix customer linked to the Shopify customer account.
	 * Shopify will call the webhook after 10 days if not cancelled.
	 *
	 * @param array $pPayloadArray Data received from the webhook.
	 */
	public function deleteShopifyCustomerData(array $pPayloadArray): void
	{
		// Initialise the Shopify SDK so we can make requests.
		$this->initShopifySDK();

		// Get the shop secret.
		$shopMetaFields = $this->requestShopMetaFields();
		$secretMetaField = $shopMetaFields->getByNameSpaceAndKey('taopix', 'secret')[0];

		$utils = $this->getUtils();
		$customerID = $this->generateUserIDHash($pPayloadArray['customer']['id'], $secretMetaField->getValue());

		// Get user from account code.
		$userAccount = $utils->getUserAccountFromAccountCode($customerID);

		if ($userAccount['result'] === '')
		{
			$userID = $userAccount['recordid'];

			// Mark the user readaction as TPX_REDACTION_AUTHORISED_BY_LICENSEE.
			$authoriseRedaction2Result = $utils->authoriseRedaction2([$userID], 1);

			if ($authoriseRedaction2Result['result'] === '')
			{
				// Set the task to run after the number of days based on the setting on the brand.
				// This should match what is set on the user account to be redacted but at the end of the day.
				$utils->createEvent('TAOPIX_DATADELETION', '', '', '', date('Y-m-d 23:59:00', strtotime('+' . $this->getRedactionNotificationDays() . ' days')), 0, '', '', $userID, TPX_REDACTION_AUTHORISED_BY_USER, '', '', '', '', 0, 0, 0, '', '', $userID);
			}
		}
	}

	public function deleteShopifyShopData(array $pPayloadArray): void
	{

	}

	public function requestShopifyCustomerData(array $pPayloadArray): void
	{

	}

	/**
	 * If a product is deleted from Shopify then we must remove it from the link table
	 *
	 * @param array $pPayloadArray containing the shopify global id of the product
	 * @return void
	 */
	public function productDeletedViaShopify(array $pPayloadArray): void
	{
		$productID = 'gid://shopify/Product/' . $pPayloadArray['id'];
		$discountDataResult = $this->deleteProductFromDiscountCacheData($productID);
		if (count($discountDataResult)>0)
		{
			$this->updateShopifyDiscountData($discountDataResult);
		}

		$connectorUrl = $this->getVendorNameFromShopURL($this->shopURL);
		$db = $this->getUtils()->getGlobalDBConnection();

		$result = '';
		$resultParam = '';
		$resultArray = array();

		if ($db) {

			$stmt = $db->prepare('DELETE FROM `CONNECTORSPRODUCTCOLLECTIONLINK`
								 	WHERE `connectorproduct_id` = ?
									AND `connectorurl` = ?
			 					');

			if ($stmt) {
				if ($stmt->bind_param('ss', $productID, $connectorUrl)) {
					if (!$stmt->execute()) {
						$result = 'str_DatabaseError';
						$resultParam = 'updateShopifyProductLink execute ' . $db->error;
					} else {
						$result = 'success';
					}
				} else {

					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'updateShopifyProductLink bind ' . $db->error;
				}
				if ($stmt) {
					$stmt->free_result();
					$stmt->close();
				}
			} else {
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'updateShopifyProductLink prepare ' . $db->error;
			}

			$db->close();
		} else {
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'updateShopifyProductLink connect ' . $db->error;
		}

		$resultArray['result'] = $result;
		$resultArray['resultParam'] = $resultParam;
	}

	/**
	 * Update results of productUpdate bulk operation to CONNECTORSPRODUCTCOLLECTIONLINK table
	 *
	 * @param array pArrayCollectionData array of collections to remove resources
	 * @return bool success
	 */
	private function cleanUpConnectorResources($pArrayCollectionData)
	{
		$ac_config = $this->getACConfig();
		$utils = $this->getUtils();

		foreach ($pArrayCollectionData as $collection) {
			$collectioncode = $collection[0];
			$collectionResourceFolder = $utils->correctPath($ac_config['CONNECTORRESOURCESPATH'], DIRECTORY_SEPARATOR, true) . 'resources' . DIRECTORY_SEPARATOR . $collectioncode;

			$utils->deleteFolder($collectionResourceFolder);
		}
	}

	/**
	 * Fires when Shopify bulk operation is complete
	 *
	 * @param array $pPayloadArray containing path to the results file
	 * @return void
	 */
	public function bulkQueryComplete(array $pPayloadArray): void
	{
		$graphQL = $this->initGraphQL();
		$product = new Product($graphQL);
		$query = '';

		$bulkOperationId = $pPayloadArray['admin_graphql_api_id'];
		$pollResult = $product->pollBulkOperationStatus('QUERY')->currentBulkOperation;

		if (isset($pollResult->query)) {
			$query = $pollResult->query;
		}
		$mode = 'automaticBasicDiscount';

		$storeResult = '';

		if (str_contains($query, 'priceRules'))
		{
			$mode = 'priceRules';
		} else if (str_contains($query, 'DiscountAutomaticBxgy') && !str_contains($query, 'DiscountAutomaticBasic')) {
			$mode = 'automaticBxgyDiscount';
		}

		if (str_contains($query, 'deliveryProfiles'))
		{
			$mode = 'deliveryProfiles';
		}

		if (isset($pollResult->id))
		{
			if ($pollResult->id === $bulkOperationId)
			{
				$storeResult = $product->storeBulkOperationResults((array) $pollResult, $this->brandCode, $mode);
			}
		}

		[$extension, $filePath, $files] = $this->discountCacheFileSetup();
		$allFilesExist = true;

		foreach ($files as $file) {
			if (!file_exists($filePath . $file['filename'])) {
				$allFilesExist = false;
			}
		}

		if (($storeResult != '') && ($allFilesExist))
		{
			//All discount syncing has completed so we can proceed to update the DB
			$data = $this->generateDiscountDataCombined();
			$updateResult = $this->updateShopifyDiscountData($data);

			if ($updateResult['result'] != 'success')
			{
				error_log('Discounts cache update error on bulkOperation ' . $bulkOperationId);
			}
		}
		elseif (($storeResult != '') && ($mode == 'deliveryProfiles'))
		{
			//deliveryProfiles query has completed so we can proceed to parse it and update the DB
			$updateResult = $this->updateDeliveryProfiles();

			if ($updateResult['result'] != '')
			{
				error_log('Shipping Profile cache update error on bulkOperation ' . $bulkOperationId);
			}
		}
	}

	/**
	 * If a product is updated from Shopify then we must check if a variant has been deleted and update metadata accordingly
	 *
	 * @param array $pPayloadArray containing the shopify global id of the product
	 */
	public function productUpdatedViaShopify(array $pPayloadArray): void
	{
		$productID = 'gid://shopify/Product/' . $pPayloadArray['id'];
		$taopix_product_id = '';

		$tagsArray = explode(", ", $pPayloadArray['tags']);

		//Only try to update taopix catalog products
		if (in_array("taopix",$tagsArray)) {
			$variantArray = $pPayloadArray['variants'];
			$variantArrayCount = count($variantArray);

			$option1Array = [];
			$updateVariantArray = [];

			foreach($variantArray as $variant) {
				$updateRequired = true;

				if(!isset($option1Array[$variant['option1']])) {
					$option1Array[$variant['option1']] = ['productid' => ''];
				}

				if (isset($variant['metafields'])) {
					foreach($variant['metafields'] as $metafield) {
						if ($metafield['key'] == 'taopix_product_id') {
							$option1Array[$variant['option1']] = ['productid' => $metafield['value']];
							$updateRequired = false;
						}
					}
				}

				if ($updateRequired) {
					$updateVariantArray[$variant['id']] = [	'id' => $variant['id'], 'option1' => $variant['option1']	];
				}
			}

			//we need to update the metafield on this newly created variant
			if (count($updateVariantArray) > 0) {
				foreach($updateVariantArray as $updateVariant) {
					//there is no metafield for the productid

					if (isset($option1Array[$updateVariant['option1']]['productid'])) {
						if ($option1Array[$updateVariant['option1']]['productid'] != '') {
							$prodIdKey = "taopix_product_id";
							$metaFieldMutation =
								[
									'ownerId' => 'gid://shopify/ProductVariant/'.$updateVariant['id'],
									'namespace' => 'taopix',
									'key' => $prodIdKey,
									'value' => $option1Array[$updateVariant['option1']]['productid'],
									'type' => 'single_line_text_field'
								];

							$result = $this->addMetaField($metaFieldMutation);

							//now add this new info to the cache too
							if (isset($result->metafieldsSet->metafields))
							{
								$newMetafieldid = $result->metafieldsSet->metafields[0]->id;
								$index = array_search($updateVariant['id'], array_column($variantArray, 'id'));
								$pPayloadArray['variants'][$index]['metafields'][] = [	'id' => $newMetafieldid, 'value' => $option1Array[$updateVariant['option1']]['productid'], 'key' => $prodIdKey,  'admin_graphql_api_id' => 'gid://shopify/Metafield/' . $newMetafieldid	];
							}
						}
					}
				}
			}

			$modifiedProductArray = [];
			$modifiedProductArray['id'] = 'gid://shopify/Product/' . $pPayloadArray['id'];
			$modifiedProductArray['createdAt'] = $pPayloadArray['created_at'];
			$modifiedProductArray['updatedAt'] = $pPayloadArray['updated_at'];
			$modifiedProductArray['title'] = $pPayloadArray['title'];

			$modifiedProductArray['options'] = [];
			foreach($pPayloadArray['options'] as $option) {
				$modifiedProductArray['options'][] = [	'name' => $option['name'],
														'values' => $option['values']
													 ];
			}

			$modifiedProductArray['metafield'] = [];
			foreach($pPayloadArray['metafields'] as $metafield) {
				$modifiedProductArray['metafield'][] = [
					'id' => $metafield['admin_graphql_api_id'],
					'value' => $metafield['value']
				];
				if ($metafield['key'] == 'taopix_product_id') {
					$taopix_product_id = $metafield['value'];
				}
			}

			$modifiedProductArray['variants'] = [];
			$modifiedProductArray['variants']['edges'] = [];
			$node = [];
			foreach($pPayloadArray['variants'] as $variant) {
				$node['id'] = $variant['admin_graphql_api_id'];
				$node['title'] = $variant['title'];
				$imageid = '';
				if ($variant['image_id'] != '') {
					$imageid = 'gid://shopify/ProductImage/' . $variant['image_id'];
				}
				$node['image']['id'] = $imageid;
				foreach($variant['metafields'] as $variantMetafield) {
					if ($variantMetafield['key'] == 'taopix_product_id') {
						$node['metafield_taopix_product_id'] = [
							'id' => $variantMetafield['admin_graphql_api_id'],
							'value' => $variantMetafield['value']
						];
					}
					if ($variantMetafield['key'] == 'taopix_product_description') {
						$node['taopix_product_description'] = NULL;
						if (isset($variantMetafield['admin_graphql_api_id'])) {
							$node['taopix_product_description'] = [
								'id' => $variantMetafield['admin_graphql_api_id'],
								'value' => $variantMetafield['value']
							];
						}
					}
					$modifiedProductArray['variants']['edges'][] = [
						'node' => $node
					];
				}
			}

			//update the images
			$modifiedProductArray['images'] = [];
			foreach($pPayloadArray['images'] as $image) {
				$modifiedProductArray['images'][] = [
					'id' => $image['id'],
					'admin_graphql_api_id' => $image['admin_graphql_api_id'],
					'src' => $image['src'],
					'position' => $image['position'],
					'alt' => $image['alt']
				];
			}

			$metadata = json_encode($modifiedProductArray);

			if ($taopix_product_id != '') {
				$updatedCollectionDataArray = [];
				$updatedCollectionDataArray[0] = $taopix_product_id;
				$updatedCollectionDataArray[1] = $productID;
				$updatedCollectionDataArray[2] = $metadata;
				$updatedCollectionDataArray[3] = $modifiedProductArray['updatedAt'];

				$result = $this->updateShopifyProductLink([$updatedCollectionDataArray]);
			}
		}
	}

	/**
	 * Get current stored metadata for product
	 *
	 * @param string $pProductID containing the shopify global id of the product
	 * @return array Product variant data
	 */
	public function getProductVariantData(string $pProductID): array
	{
		$connectorUrl = $this->getVendorNameFromShopURL($this->shopURL);
		$db = $this->getUtils()->getGlobalDBConnection();
		$metadata = [];

		$result = '';
		$resultParam = '';
		$dataArray = array();
		$resultArray = array();

		if ($db)
		{

			$stmt = $db->prepare('SELECT `metadata` FROM `CONNECTORSPRODUCTCOLLECTIONLINK`
								 	WHERE `connectorproduct_id` = ? AND `connectorurl` = ?
			 					');

				if ($stmt)
				{
					// bind param d
					if ($stmt->bind_param('ss',$pProductID, $connectorUrl))
					{
						if ($stmt->execute())
						{
							if ($stmt->store_result())
							{
								if ($stmt->num_rows > 0) {
									if ($stmt->bind_result($metadata)) {
										while ($stmt->fetch()) {
											$dataArray[] = $metadata;
										}
									} else {
										$returnArray['error'] = __FUNCTION__ . ' bind result error: ' . $db->error;
									}
								}
							}
							else
							{
								$resultArray['result'] = __FUNCTION__ . ' store result ' . $db->error;
							}

							$stmt->free_result();
							$stmt->close();
							$stmt = null;
						}
						else
						{
							$resultArray['result'] = __FUNCTION__ . ' execute  ' . $db->error;
						}
					}
					else
					{
						$resultArray['result'] = __FUNCTION__ . ' bind params  ' . $db->error;
					}
				}
				else
				{
					$resultArray['result'] = __FUNCTION__ . ' prepare  ' . $db->error;
				}
				$db->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'getProductVariantData connect ' . $db->error;
		}

		$resultArray['data'] = $dataArray;
		$resultArray['result'] = $result;
		$resultArray['resultParam'] = $resultParam;

		return $resultArray;
	}

	/**
	 * bulkOperationRunQuery to retrieve data
	 *
	 * @param string pQuery contains stagedUploadPath
	 * @return \stdClass The query result object
	 */
	public function submitBulkQuery(string $pQuery): \stdClass
	{
		$bulkOperationQuery = (new \GraphQL\Mutation('bulkOperationRunQuery'))
			->setArguments(['query' => preg_replace("\t|\n|\r", " ", $pQuery)])
			->setSelectionSet(
				[
					(new \GraphQL\Query('bulkOperation'))->setSelectionSet([
						'id',
						'url',
						'status'
					]),
					(new \GraphQL\Query('userErrors'))->setSelectionSet([
						'field',
						'message'
					])
				]
			);

		return $this->runGraphQLQuery($this->initGraphQL(), $bulkOperationQuery, false, []);
	}

	/**
	 * Update Store Discount Data
	 *
	 * @param Array $pDiscountDataArray containing the discount data for the store
	 * @return array Result
	 */
	public function updateShopifyDiscountData(array $pDiscountDataArray): array
	{
		$vendor = $this->getVendorNameFromShopURL();
		$db = $this->getUtils()->getGlobalDBConnection();

		$result = '';
		$resultParam = '';
		$resultArray = array();

		$discountdata = json_encode($pDiscountDataArray);

		$discountdataLength = strlen($discountdata);
		if ($discountdataLength > 15728640) {
			$discountdata = gzcompress($discountdata, 9);
		} else {
			$discountdataLength = 0;
		}

		if ($db) {
			$stmt = $db->prepare('	UPDATE `CONNECTORS`
									SET
										`discountdataupdated` = NOW()
										,`discountdata` = ?
										,`discountdatalength` = ?
									WHERE `connectorurl` = ?
								');

			if ($stmt) {
				if ($stmt->bind_param(
					'sis',
					$discountdata, $discountdataLength, $vendor
				)) {
						if (!$stmt->execute()) {
							$result = 'str_DatabaseError';
							$resultParam = 'updateShopifyDiscountData execute ' . $db->error;
						} else {
							$result = 'success';
						}
				} else {

					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'updateShopifyDiscountData bind ' . $db->error;
				}
				if ($stmt) {
					$stmt->free_result();
					$stmt->close();
				}
			} else {
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'updateShopifyDiscountData prepare ' . $db->error;
			}

			$db->close();
		} else {
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'updateShopifyDiscountData connect ' . $db->error;
		}

		$resultArray['result'] = $result;
		$resultArray['resultParam'] = $resultParam;

		return $resultArray;
	}

	/**
	 * Check if temp products can be added to discounts (based on parent catalog product discounts)
	 *
	 * @param array pTempProductData - Product data to check if discounts apply
	 * @param string pRemoveProductID - Product ID of temp product to be removed from cache
	 * @return array result
	 */
	public function doDiscounts(array $pTempProductData, string $pRemoveProductID): array
    {
		$discountData = json_decode($this->getShopifyDiscountData()['data']);
        $priceRules = [];
		$automaticDiscounts = [];
		$result = [];
		$updateCache = false;

		if ($pRemoveProductID != '')
		{
			$deleteResult = $this->deleteProductFromDiscountCacheData($pRemoveProductID, $discountData);
			if (count($deleteResult)>0)
			{
				$updateCache = true;
				$discountData = $deleteResult;
			}
		}

		if (isset($discountData->discounts->pricerules))
		{
			$priceRules = $discountData->discounts->pricerules;
		}

		if (isset($discountData->discounts->automaticdiscounts))
		{
        	$automaticDiscounts = $discountData->discounts->automaticdiscounts;
		}

        if (count((array)$priceRules) > 0)
        {
            $priceRulesToUpdate = $this->discounts($priceRules, $pTempProductData);

			if (count($priceRulesToUpdate['collectionupdates']) > 0)
			{
				foreach ($priceRulesToUpdate['collectionupdates'] as $update)
				{
					$updateCache = true;
					$id = $update['id'];
					$title = $update['title'];

					$taopixDiscountCollectionID = $update['taopixdiscountcollection'];
					$collectionIds = $update['collectionids'];
					$newCollection = false;

					//if we do not have a taopix collection already then create one
					if ($taopixDiscountCollectionID === -1) {
						$graphQL = $this->initGraphQL();

						$productCollection = new ProductCollection($graphQL);
						$collectionsToCreate = new ProductCollectionCollection(
							[
								[
									'title' => 'Taopix Discount Collection for ' . $title,
									'metafields' => [
										[
											'namespace' => 'taopix',
											'key' => 'taopix_discount',
											'value' => $update['id'],
											'type' => 'single_line_text_field'
										]
									],
								]
							]
						);

						$collectionToCreate = $collectionsToCreate[0];
						$result = $productCollection->createCollection($collectionToCreate->getProperties());

						if (isset($result->collectionCreate->collection))
						{
							$taopixDiscountCollectionID = $result->collectionCreate->collection->id;
							$newCollection = true;
						}

						//some error occured
						if (isset($result->collectionCreate->userErrors))
						{
							if (count($result->collectionCreate->userErrors) > 0)
							{
								error_log(print_r($result->collectionCreate->userErrors,true));
							}
						}
					}

					if ($taopixDiscountCollectionID !== -1) {
						$updateCache = true;

						$addProductsToCollectionResult = $this->addProductsToCollection($taopixDiscountCollectionID, [$update['productid']]);

						//some error occured
						if (isset($addProductsToCollectionResult->collectionAddProducts->userErrors))
						{
							if (count($addProductsToCollectionResult->collectionAddProducts->userErrors) > 0)
							{
								error_log(print_r($addProductsToCollectionResult->collectionAddProducts->userErrors,true));
								error_log(print_r([$taopixDiscountCollectionID, $update['productid']],true));
							}
						}

						//if this is a newly created collection it needs adding to the discount code
						if ($newCollection) {
							$input = [ 'itemEntitlements' => [    "collectionIds" => array_merge($collectionIds, [$taopixDiscountCollectionID])	], "combinesWith" => $update['combineswith']   ];
							$this->updatePriceRule($id, $input);

							if (!isset($discountData->discounts->pricerules->$id->taopix_discount_collection)) {
								$discountData->discounts->pricerules->$id->taopix_discount_collection = $taopixDiscountCollectionID;
							}
						}

						$prodID = $update['productid'];
						$prodData = new stdClass();
						$prodData->id = $prodID;

						//now add it to the cache
						if (!isset($discountData->discounts->pricerules->$id->collections->$taopixDiscountCollectionID))
						{
							$collectionData = new stdClass();
							$collectionData->id = $taopixDiscountCollectionID;
							$collectionData->products = new stdClass();
							$collectionData->products->$prodID = $prodData;

							$discountData->discounts->pricerules->$id->collections->$taopixDiscountCollectionID = $collectionData;
						}
						else
						{
							$discountData->discounts->pricerules->$id->collections->$taopixDiscountCollectionID->products->$prodID = $prodData;
						}
					}
				}
			}

			if (count($priceRulesToUpdate['productupdates']) > 0)
			{
				foreach ($priceRulesToUpdate['productupdates'] as $update)
				{
					$updateCache = true;
					$id = $update['id'];
					$input = [ 'itemEntitlements' => [    "productIds" => $update['productids']   ], "combinesWith" => $update['combineswith']   ];
					$this->updatePriceRule($id, $input);

					//ADD TO discountData THEN UPDATE cache
					foreach ($update['productids'] as $productid)
					{
						if (!isset($discountData->discounts->pricerules->$id->products->$productid))
						{
							$discountData->discounts->pricerules->$id->products->$productid = ['id' => $productid];
						}
					}
				}
			}

			if (count($priceRulesToUpdate['variantupdates']) > 0)
			{
				foreach ($priceRulesToUpdate['variantupdates'] as $update)
				{
					$updateCache = true;
					$id = $update['id'];
					$updateVariantids = [];

					//ADD TO discountData THEN UPDATE cache
					foreach ($update['variantids'] as $variantid)
					{
						$variantDataArray = explode('@@',$variantid);

						$thisparentproductid = $variantDataArray[0];
						$thisvariantid = $variantDataArray[1];
						$updateVariantids[] = $thisvariantid;

						if (!isset($discountData->discounts->pricerules->$id->productVariants->$thisvariantid))
						{
							$discountData->discounts->pricerules->$id->productVariants->$thisvariantid = ['id' => $thisvariantid, 'parent_product_id' => $thisparentproductid];
						}
					}

					$input = [ 'itemEntitlements' => [    "productVariantIds" => $updateVariantids   ], "combinesWith" => $update['combineswith']   ];
					$this->updatePriceRule($id, $input);
				}
			}
        }

		if (count((array)$automaticDiscounts) > 0)
        {
			$automaticDiscountsToUpdate = $this->discounts($automaticDiscounts, $pTempProductData);

			if (count((array)$automaticDiscountsToUpdate['collectionupdates']) > 0)
        	{
				foreach ($automaticDiscountsToUpdate['collectionupdates'] as $update)
				{
					$updateCache = true;
					$id = $update['id'];
					$title = $update['title'];

					$taopixDiscountCollectionID = $update['taopixdiscountcollection'];
					$collectionIds = $update['collectionids'];
					$newCollection = false;

					//if we do not have a taopix collection already then create one
					if ($taopixDiscountCollectionID === -1) {
						$graphQL = $this->initGraphQL();

						$productCollection = new ProductCollection($graphQL);
						$collectionsToCreate = new ProductCollectionCollection(
							[
								[
									'title' => 'Taopix Discount Collection for ' . $title,
									'metafields' => [
										[
											'namespace' => 'taopix',
											'key' => 'taopix_discount',
											'value' => $id,
											'type' => 'single_line_text_field'
										]
									],
								]
							]
						);

						$collectionToCreate = $collectionsToCreate[0];
						$result = $productCollection->createCollection($collectionToCreate->getProperties());

						if (isset($result->collectionCreate->collection))
						{
							$taopixDiscountCollectionID = $result->collectionCreate->collection->id;
							$newCollection = true;
						}

						//some error occured
						if (isset($result->collectionCreate->userErrors))
						{
							if (count($result->collectionCreate->userErrors) > 0)
							{
								error_log(print_r($result->collectionCreate->userErrors,true));
							}
						}
					}

					if ($taopixDiscountCollectionID !== -1) {
						$updateCache = true;

						$addProductsToCollectionResult = $this->addProductsToCollection($taopixDiscountCollectionID, [$update['productid']]);

						//some error occured
						if (isset($addProductsToCollectionResult->collectionAddProducts->userErrors))
						{
							if (count($addProductsToCollectionResult->collectionAddProducts->userErrors) > 0)
							{
								error_log(print_r($addProductsToCollectionResult->collectionAddProducts->userErrors,true));
							}
						}

						//if this is a newly created collection it needs adding to the discount code
						if ($newCollection) {
							$inputNode = ($update['discounttype'] == 'DiscountAutomaticBxgy') ? 'customerBuys' : 'customerGets';
							$input = [ $inputNode => [ 'items' => [    "collections" =>  [ "add" => [$taopixDiscountCollectionID]   ] ] ], "combinesWith" => $update['combineswith'] ];
							$result = $this->updateAutoDiscount($id, $input, $update['discounttype']);

							if (!isset($discountData->discounts->automaticdiscounts->$id->taopix_discount_collection)) {
								$discountData->discounts->automaticdiscounts->$id->taopix_discount_collection = $taopixDiscountCollectionID;
							}
						}

						$prodID = $update['productid'];
						$prodData = new stdClass();
						$prodData->id = $prodID;

						//now add it to the cache
						if (!isset($discountData->discounts->automaticdiscounts->$id->collections->$taopixDiscountCollectionID))
						{
							$collectionData = new stdClass();
							$collectionData->id = $taopixDiscountCollectionID;
							$collectionData->products = new stdClass();
							$collectionData->products->$prodID = $prodData;

							$discountData->discounts->automaticdiscounts->$id->collections->$taopixDiscountCollectionID = $collectionData;
						}
						else
						{
							$discountData->discounts->automaticdiscounts->$id->collections->$taopixDiscountCollectionID->products->$prodID = $prodData;
						}
					}
				}
			}


			if (count($automaticDiscountsToUpdate['productupdates']) > 0)
			{
				foreach ($automaticDiscountsToUpdate['productupdates'] as $update)
				{
					$updateCache = true;
					$id = $update['id'];
					$inputNode = ($update['discounttype'] == 'DiscountAutomaticBxgy') ? 'customerBuys' : 'customerGets';
					$input = [ $inputNode => [ 'items' => [    "products" =>  [ "productsToAdd" => $update['productids']   ] ] ], "combinesWith" => $update['combineswith'] ];
					$result = $this->updateAutoDiscount($id, $input, $update['discounttype']);

					//ADD TO discountData THEN UPDATE cache
					foreach ($update['productids'] as $productid)
					{
						if (!isset($discountData->discounts->automaticdiscounts->$id->products->$productid))
						{
							$discountData->discounts->automaticdiscounts->$id->products->$productid = ['id' => $productid];
						}
					}
				}
			}

			if (count($automaticDiscountsToUpdate['variantupdates']) > 0)
			{
				foreach ($automaticDiscountsToUpdate['variantupdates'] as $update)
				{
					$updateCache = true;
					$id = $update['id'];
					$inputNode = ($update['discounttype'] == 'DiscountAutomaticBxgy') ? 'customerBuys' : 'customerGets';

					$updateVariantids = [];

					//ADD TO discountData THEN UPDATE cache
					foreach ($update['variantids'] as $variantid)
					{
						$variantDataArray = explode('@@',$variantid);

						$thisparentproductid = $variantDataArray[0];
						$thisvariantid = $variantDataArray[1];
						$updateVariantids[] = $thisvariantid;

						if (!isset($discountData->discounts->automaticdiscounts->$id->productVariants->$thisvariantid))
						{
							$discountData->discounts->automaticdiscounts->$id->productVariants->$thisvariantid = ['id' => $thisvariantid, 'parent_product_id' => $thisparentproductid];
						}
					}

					$input = [ $inputNode => [ 'items' => [    "products" =>  [ "productVariantsToAdd" => $updateVariantids   ] ] ], "combinesWith" => $update['combineswith'] ];
					$this->updateAutoDiscount($id, $input, $update['discounttype']);
				}
			}
        }

		if ($updateCache)
		{
			//we are updating the cache so need to remove any files which the scheduled task has generated as they will be out of date
			[$extension, $filePath, $files] = $this->discountCacheFileSetup();

			foreach ($files as $file)
			{
				$dateNow = date("YmdHis");
				$completedFilePath = $filePath . 'removed' . DIRECTORY_SEPARATOR . str_replace($extension, '-' . $dateNow . $extension, $file['filename']);

				if (file_exists($filePath . $file['filename']))
				{
					$this->getUtils()->moveUploadedFile($filePath . $file['filename'], $completedFilePath);
				}
			}

			$discountDataArray = (array) $discountData;
			$result = $this->updateShopifyDiscountData($discountDataArray);
		}

		return $result;
    }

	/**
	 * Setup file data for discount data caching
	 *
	 * @return array
	 */
	function discountCacheFileSetup(): array
	{
		$extension = '.jsonl';
		$filePath = $this->getACConfig()['CONNECTORRESOURCESPATH'];
		$filePath = $this->getUtils()->correctPath($filePath, DIRECTORY_SEPARATOR, true);

		if ($this->getBrandCode() != '')
		{
			$filePath .=  $this->getBrandCode() . DIRECTORY_SEPARATOR;
		}

		$filePath .= 'discounts' . DIRECTORY_SEPARATOR;

		$files[] = ['filename' => 'priceRules' . $extension, 'node' => 'pricerules'];
		$files[] = ['filename' => 'automaticBasicDiscount' . $extension, 'node' => 'automaticdiscounts'];
		$files[] = ['filename' => 'automaticBxgyDiscount' . $extension, 'node' => 'automaticdiscounts'];

		return [$extension, $filePath, $files];
	}

	/**
	 * Update Price Rule
	 *
	 * @param string pPriceRuleId - id of the price rule to be updated
	 * @param array pInput - PriceRuleInput data to add
	 */
	function updatePriceRule(string $pPriceRuleId, array $pInput): void
	{
		$mutation = (new \GraphQL\Mutation('priceRuleUpdate'))
					->setVariables([new \GraphQL\Variable('id', 'ID', true), new \GraphQL\Variable('priceRule', 'PriceRuleInput', true)])
					->setArguments(['id' => '$id', 'priceRule' => '$priceRule'])
					->setSelectionSet(
						[
							(new \GraphQL\Query('userErrors'))->setSelectionSet([
								'field',
								'message'
							])
						]
					);

		$result = $this->runGraphQLQuery($this->initGraphQL(), $mutation, false, ['id' => $pPriceRuleId, 'priceRule' => $pInput]);

		if (isset($result->priceRuleUpdate->userErrors))
		{
			if (count($result->priceRuleUpdate->userErrors) > 0)
			{
				error_log(print_r($result->priceRuleUpdate->userErrors,true));
			}
		}
	}

	/**
	 * Update  Automatic discount
	 *
	 * @param string pAutomaticDiscountId - id of the discount to be updated
	 * @param array pInput - automaticDiscount data to add
	 * @param string pSubType - type of automaticDiscount, Basic or BXGY
	 */
	function updateAutoDiscount(string $pAutomaticDiscountId, array $pInput, string $pSubType): void
	{

		if ($pSubType === 'DiscountAutomaticBxgy')
		{
			$mutation = (new \GraphQL\Mutation('discountAutomaticBxgyUpdate'))
						->setVariables([new \GraphQL\Variable('id', 'ID', true), new \GraphQL\Variable('automaticDiscount', 'DiscountAutomaticBxgyInput', true)])
						->setArguments(['id' => '$id', 'automaticBxgyDiscount' => '$automaticDiscount'])
						->setSelectionSet(
							[
								(new \GraphQL\Query('automaticDiscountNode'))->setSelectionSet([
									'__typename'
								]),
								(new \GraphQL\Query('userErrors'))->setSelectionSet([
									'field',
									'message'
								])
							]
						);
		}
		else
		{
			$mutation = (new \GraphQL\Mutation('discountAutomaticBasicUpdate'))
						->setVariables([new \GraphQL\Variable('id', 'ID', true), new \GraphQL\Variable('automaticDiscount', 'DiscountAutomaticBasicInput', true)])
						->setArguments(['id' => '$id', 'automaticBasicDiscount' => '$automaticDiscount'])
						->setSelectionSet(
							[
								(new \GraphQL\Query('automaticDiscountNode'))->setSelectionSet([
									'__typename'
								]),
								(new \GraphQL\Query('userErrors'))->setSelectionSet([
									'field',
									'message'
								])
							]
						);
		}

		$result = $this->runGraphQLQuery($this->initGraphQL(), $mutation, false, ['id' => $pAutomaticDiscountId, 'automaticDiscount' => $pInput]);

		if (isset($result->userErrors))
		{
			if (count($result->userErrors) > 0)
			{
				error_log(print_r($result->userErrors,true));
			}
		}
	}

	/**
	 * Works out which discounts need temp product adding to them
	 *
	 * @param \stdClass pDiscountData - current discounts
	 * @param array pTempProductData - temp product to check if they need to be added
	 * @return array Products and Variants to be added to discount
	 */
	function discounts(\stdClass $pDiscountData, array $pTempProductData): array
	{
		$catalogCollectionCode = $pTempProductData['collectioncode'];
        $catalogLayoutCode = $pTempProductData['layoutcode'];
        $tempProductId = $pTempProductData['tempproductid'];
        $tempVariantId = $pTempProductData['tempvariantid'];

        $variantCheck = false;

        if ($catalogLayoutCode != '')
        {
            $variantCheck = true;
        }

		$returnArray = [];
		$returnArray['productupdates'] = [];
		$returnArray['variantupdates'] = [];
		$returnArray['collectionupdates'] = [];

		foreach($pDiscountData as $discount)
		{
			$type = isset($discount->type) ? $discount->type : 'priceRule';
			$combinesWith = isset($discount->combinesWith) ? $discount->combinesWith : ['orderDiscounts'=>false,'shippingDiscounts'=>false,'productDiscounts'=>false];

			if (isset($discount->id))
			{
				$id = $discount->id;
				$title = isset($discount->title) ? $discount->title : 'discount';
				$taopixDiscountCollection = -1;

				if (isset($discount->collections))
				{
					$productIds = [];
					$collectionIds = [];
					$doUpdate = false;

					if (isset($discount->taopix_discount_collection)){
						$taopixDiscountCollection = $discount->taopix_discount_collection;
					}

					foreach($discount->collections as $collection)  {
						if ($type == 'priceRule')
						{
							$collectionIds[] = $collection->id;
						}

						if (isset($collection->products)) {
							foreach($collection->products as $product)  {
								$collectionCode = '';

								if (isset($product->collection_code))
								{
									$collectionCode = $product->collection_code;
								}

								if ($catalogCollectionCode === $collectionCode)
								{
									$doUpdate = true;
								}
							}
						}
					}

					if ($doUpdate)
					{
						$returnArray['collectionupdates'][$id]['id'] = $id;
						$returnArray['collectionupdates'][$id]['title'] = $title;
						$returnArray['collectionupdates'][$id]['productid'] = $tempProductId;
						$returnArray['collectionupdates'][$id]['collectionids'] = $collectionIds;
						$returnArray['collectionupdates'][$id]['discounttype'] = $type;
						$returnArray['collectionupdates'][$id]['taopixdiscountcollection'] = $taopixDiscountCollection;
						$returnArray['collectionupdates'][$id]['combineswith'] = $combinesWith;
					}
				}

				if (isset($discount->products))
				{
					$productIds = [];
					$doUpdate = false;

					foreach($discount->products as $product) {
						if ($type == 'priceRule')
						{
							$productIds[] = $product->id;
						}

						$collectionCode = '';

						if (isset($product->collection_code))
						{
							$collectionCode = $product->collection_code;
						}

						if ($catalogCollectionCode === $collectionCode)
						{
							$productIds[] = $tempProductId;
							$doUpdate = true;
						}
					}

					if ($doUpdate)
					{
						$returnArray['productupdates'][$id]['id'] = $id;
						$returnArray['productupdates'][$id]['productids'] = $productIds;
						$returnArray['productupdates'][$id]['discounttype'] = $type;
						$returnArray['productupdates'][$id]['combineswith'] = $combinesWith;
					}
				}

				if (isset($discount->productVariants) && $variantCheck)
				{
					$variantIds = [];
					$doUpdate = false;

					foreach($discount->productVariants as $productVariant) {
						if ($type == 'priceRule')
						{
							$variantIds[] = $productVariant->parent_product_id . '@@' . $productVariant->id;
						}

						$collectionCode = '';
						$layoutCode = '';

						if (isset($productVariant->collection_code) && isset($productVariant->layout_code))
						{
							$collectionCode = $productVariant->collection_code;
							$layoutCode = $productVariant->layout_code;
						}

						if (    ($catalogCollectionCode === $collectionCode) && ($catalogLayoutCode === $layoutCode)    )
						{
							$variantIds[] = $tempProductId . '@@' . $tempVariantId;
							$doUpdate = true;
						}

						if ($doUpdate)
						{
							$returnArray['variantupdates'][$id]['id'] = $id;
							$returnArray['variantupdates'][$id]['variantids'] = $variantIds;
							$returnArray['variantupdates'][$id]['discounttype'] = $type;
							$returnArray['variantupdates'][$id]['combineswith'] = $combinesWith;
						}
					}
				}
			}
		}
		return $returnArray;
	}

	/**
	 * Get current stored discount data for store
	 *
	 * @return array Discount data
	 */
	public function getShopifyDiscountData() : array
	{
		$connectorUrl = $this->getVendorNameFromShopURL($this->shopURL);
		$db = $this->getUtils()->getGlobalDBConnection();
		$discountData = '';
		$discountDataLength = 0;
		$resultArray = array();

		if ($db)
		{
			$stmt = $db->prepare('SELECT `discountdata`, `discountdatalength` FROM `CONNECTORS`
								 	WHERE `connectorurl` = ?
			 					');

				if ($stmt)
				{
					// bind param d
					if ($stmt->bind_param('s', $connectorUrl))
					{
						if ($stmt->execute())
						{
							if ($stmt->store_result())
							{
								if ($stmt->num_rows > 0) {
									if ($stmt->bind_result($discountData, $discountDataLength)) {
										while ($stmt->fetch()) {

											if ($discountDataLength > 0) {
												$discountData = gzuncompress($discountData, $discountDataLength);
											}
										}
									} else {
										$returnArray['result'] = __FUNCTION__ . ' bind result error: ' . $db->error;
									}
								}
							}
							else
							{
								$resultArray['result'] = __FUNCTION__ . ' store result ' . $db->error;
							}

							$stmt->free_result();
							$stmt->close();
							$stmt = null;
						}
						else
						{
							$resultArray['result'] = __FUNCTION__ . ' execute  ' . $db->error;
						}
					}
					else
					{
						$resultArray['result'] = __FUNCTION__ . ' bind params  ' . $db->error;
					}
				}
				else
				{
					$resultArray['result'] = __FUNCTION__ . ' prepare  ' . $db->error;
				}
				$db->close();
		}
		else
		{
			$resultArray['result'] = __FUNCTION__ . ' connect ' . $db->error;
		}

		$resultArray['data'] = $discountData;

		return $resultArray;
	}

	/**
	 * Gets graphql query for discount type needed & submits query
	 *
	 * @param string pMode discount mode
	 * @return \stdClass Result of the bulk query
	 */
	public function discountDataBulkQuery(string $pMode): \stdClass
	{
		if ($pMode == 'AUTOMATICBASIC')
		{
			$query = $this->automaticBasicDiscountQuery;
		}
		else if ($pMode == 'AUTOMATICBXGY')
		{
			$query = $this->automaticBxgyDiscountQuery;
		} else {
			$query = $this->priceRulesQuery;
		}

		return $this->submitBulkQuery($query);
	}

	/**
	 * Reads in pricerules & automaticdiscounts from bulk query results and generates data
	 *
	 * @return array The discount data
	 */
	public function generateDiscountDataCombined() : array
	{
		$data = [];
		$data['discounts'] = [];
		$data['discounts']['pricerules'] = [];
		$data['discounts']['automaticdiscounts'] = [];

		$fileData = function($filePath) {
			$file = fopen($filePath, 'r');

			if (!$file)
				die('file does not exist or cannot be opened');

			while (($line = fgets($file)) !== false) {
				yield $line;
			}

			fclose($file);
		};

		[$extension, $filePath, $files] = $this->discountCacheFileSetup();

		foreach ($files as $file)
		{
			$node = $file['node'];

			//data order is preserved by the results file so collection children will always follow it
			$collectionData = [];

			foreach ($fileData($filePath . $file['filename']) as $line)
			{
				$line_item = json_decode($line);
				$isCollectionProduct = false;
				$isCollection = false;

				if (isset($line_item->__parentId))
				{
					//Our parent is a collection
					if (strpos($line_item->__parentId, "Collection") !== false)
					{
						$isCollectionProduct = true;
					}

					//We are a collection
					if (strpos($line_item->id, "Collection") !== false)
					{
						$isCollection = true;
						$collectionData = [
							'collectionId' => $line_item->id,
							'priceRuleId' => $line_item->__parentId
						];
					}

					if (!$isCollectionProduct)
					{
						if (!isset($data['discounts'][$node][$line_item->__parentId]))
						{
							$data['discounts'][$node][$line_item->__parentId] = [];
						}
						$data['discounts'][$node][$line_item->__parentId]['id'] = $line_item->__parentId;
					}

					if ($isCollection)
					{
						if (!isset($data['discounts'][$node][$line_item->__parentId]['collections'][$line_item->id]))
						{
							$data['discounts'][$node][$line_item->__parentId]['collections'][$line_item->id]['id'] = $line_item->id;
							$data['discounts'][$node][$line_item->__parentId]['collections'][$line_item->id]['products'] = [];

							if (isset($line_item->taopix_discount) && $line_item->taopix_discount !== null)
							{
								if ($line_item->taopix_discount->value === $line_item->__parentId)
								{
									$data['discounts'][$node][$line_item->__parentId]['taopix_discount_collection'] = $line_item->id;
								}
							}
						}
					}
					else
					{
						//We are a normal product node
						if (!$isCollectionProduct)
						{
							if (property_exists($line_item, 'product_id'))
							{
								$data['discounts'][$node][$line_item->__parentId]['products'][$line_item->id]['id'] = $line_item->id;
								if (isset($line_item->product_id->value))
								{
									$data['discounts'][$node][$line_item->__parentId]['products'][$line_item->id]['collection_code'] = $line_item->product_id->value;
								}
							}

							if (property_exists($line_item, 'variant_id'))
							{
								$data['discounts'][$node][$line_item->__parentId]['productVariants'][$line_item->id]['id'] = $line_item->id;

								if (isset($line_item->parent_product_id->id))
								{
									$parentProductID = $line_item->parent_product_id->id;
									$data['discounts'][$node][$line_item->__parentId]['productVariants'][$line_item->id]['parent_product_id'] = $parentProductID;
								}

								if (isset($line_item->variant_id->value))
								{
									$variantDataArray = explode(".", $line_item->variant_id->value);
									$collectionCode = $variantDataArray[0];
									$layoutCode = $variantDataArray[1];

									$data['discounts'][$node][$line_item->__parentId]['productVariants'][$line_item->id]['collection_code'] = $collectionCode;
									$data['discounts'][$node][$line_item->__parentId]['productVariants'][$line_item->id]['layout_code'] = $layoutCode;
								}
							}
						}
						//We are a collection product
						else
						{
							if (property_exists($line_item, 'product_id'))
							{
								$data['discounts'][$node][$collectionData['priceRuleId']]['collections'][$collectionData['collectionId']]['products'][$line_item->id]['id'] = $line_item->id;
								if (isset($line_item->product_id->value))
								{
									$data['discounts'][$node][$collectionData['priceRuleId']]['collections'][$collectionData['collectionId']]['products'][$line_item->id]['collection_code'] = $line_item->product_id->value;
								}
							}
						}
					}
				}
				else
				{
					if (!isset($data['discounts'][$node][$line_item->id]))
					{
						$data['discounts'][$node][$line_item->id] = [];
					}

					if ((isset($line_item->automaticDiscount->__typename)) && (!isset($data['discounts'][$node][$line_item->id]['type'])))
					{
						$data['discounts'][$node][$line_item->id]['type'] = $line_item->automaticDiscount->__typename;
					}

					if ((!isset($data['discounts'][$node][$line_item->id]['combinesWith'])))
					{
						if (isset($line_item->combinesWith) || (isset($line_item->automaticDiscount->combinesWith)))
						{
							$data['discounts'][$node][$line_item->id]['combinesWith'] = isset($line_item->automaticDiscount->combinesWith) ? $line_item->automaticDiscount->combinesWith :  $line_item->combinesWith;
						}
					}

					if (isset($line_item->title) || isset($line_item->automaticDiscount->title))
					{
						$data['discounts'][$node][$line_item->id]['title'] = isset($line_item->title) ? $line_item->title : (isset($line_item->automaticDiscount->title) ? $line_item->automaticDiscount->title : '');
					}
				}
			}

			$dateNow = date("YmdHis");
			$completedFilePath = $filePath . 'complete' . DIRECTORY_SEPARATOR . str_replace($extension, '-' . $dateNow . $extension, $file['filename']);

			$this->getUtils()->moveUploadedFile($filePath . $file['filename'], $completedFilePath);
		}

		return $data;
	}

	/**
	 * Uploads Image to shopify files section
	 *
	 * @param array pInput FileCreateInput data
	 * @return array The mutation result object.
	 */
	public function uploadFile(array $pInput): array
	{
		$mutation = (new \GraphQL\Mutation('fileCreate'))
					->setVariables([new \GraphQL\Variable('files', '[FileCreateInput!]', true)])
					->setArguments(['files' => '$files'])
					->setSelectionSet([
						(new \GraphQL\Query('files'))->setSelectionSet([
							'fileStatus',
							(new \GraphQL\Query('... on MediaImage'))->setSelectionSet([
								'id'
							])
						]),
						(new \GraphQL\Query('userErrors'))->setSelectionSet([
							'field',
							'message'
						])
					]);

		return $this->runGraphQLQuery($this->initGraphQL(), $mutation, true, ['files' => $pInput]);
	}

	/**
	 * Gets File URL of project thumbnail on metafield
	 *
	 * @param String pInput Id of the product
	 * @return \stdClass The query result object.
	 */
	public function fileSrcFromProduct(string $pInput): \stdClass
	{
		$query = (new \GraphQL\Query('product'))
					->setArguments(['id' => $pInput])
					->setSelectionSet([
						(new \GraphQL\Query('metafield'))
							->setArguments(["namespace" => "taopix", "key" => "taopix_project_thumbnail"])
							->setSelectionSet([
								(new \GraphQL\Query('reference'))->setSelectionSet([
									(new \GraphQL\Query('... on MediaImage'))->setSelectionSet([
										(new \GraphQL\Query('image'))->setSelectionSet([
											'originalSrc'
										])
									])
								])
							])
						]);

		return $this->runGraphQLQuery($this->initGraphQL(), $query, false, ['ID' => $pInput]);
	}

	/**
	 * Reorders the uploaded image on a product.
	 *
	 * @param \GraphQL\Client $pGraphQL GraphQL client instance.
	 * @param string $pProductID Product ID to assign the image to.
	 * @param array $pProductMutation GraphQL Mutation query array.
	 * @return \stdClass The query result object.
	 */
	public function reorderProductImages(\GraphQL\Client $pGraphQL, string $pProductID, array $pProductMutation): \stdClass
	{
		$mutation = (new \GraphQL\Mutation('productReorderImages'))
			->setVariables([
				new \GraphQL\Variable('id', 'ID', true),
				new \GraphQL\Variable('moves', '[MoveInput!]', true)
			])
			->setArguments(['id' => '$id', 'moves' => '$moves'])
			->setSelectionSet(
				[
					(new \GraphQL\Query('job'))->setSelectionSet([
						'id'
					]),
					(new \GraphQL\Query('userErrors'))->setSelectionSet([
						'field',
						'message'
					])
				]
			);

		return $this->runGraphQLQuery($pGraphQL, $mutation, false, ['id' => 'gid://shopify/Product/'.$pProductID, 'moves' => $pProductMutation]);
	}

	/**
	 * addMetaField to object
	 *
	 * @param \GraphQL\Client $pGraphQL GraphQL client instance.
	 * @param array $pMetafieldMutation GraphQL Mutation query array.
	 * @return \stdClass The query result object.
	 */
	public function addMetaField(array $pMetafieldMutation): \stdClass
	{
		$mutation = (new \GraphQL\Mutation('metafieldsSet'))
			->setVariables([
				new \GraphQL\Variable('metafields', '[MetafieldsSetInput!]', true)
			])
			->setArguments(['metafields' => '$metafields'])
			->setSelectionSet(
				[
					(new \GraphQL\Query('metafields'))->setSelectionSet([
						'id'
					]),
					(new \GraphQL\Query('userErrors'))->setSelectionSet([
						'field',
						'message'
					])
				]
			);

		return $this->runGraphQLQuery($this->initGraphQL(), $mutation, false, ['metafields' => $pMetafieldMutation]);
	}

	/**
	 * Record Webhook data
	 *
	 * @param string $pConnectorType connector type
	 * @param string $pTopic webhook topic
	 * @param Array $pWebhookData containing the webhook payload data
	 * @param string $pOrderNumber if applicable the order number
	 * @return array Result of sql update
	 */
	private function recordWebhookData(string $pConnectorType, string $pTopic, array $pWebhookData, string $pOrderNumber = '0'): array
	{
		$db = $this->getUtils()->getGlobalDBConnection();

		$result = '';
		$resultParam = '';
		$resultArray = array();

		$webhookdata = json_encode($pWebhookData, JSON_NUMERIC_CHECK);

		$webhookdataLength = strlen($webhookdata);
		if ($webhookdataLength > 15728640) {
			$webhookdata = gzcompress($webhookdata, 9);
		} else {
			$webhookdataLength = 0;
		}

		if ($db) {
			$stmt = $db->prepare('	INSERT INTO `CONNECTORSWEBHOOKDATA`
									(	`connectortype`
										,`webhooktopic`
										,`ordernumber`
										,`data`
										,`datalength`
									)
									VALUES
									(
										?, ?, ?, ?, ?
									)
								');

			if ($stmt) {
				if ($stmt->bind_param(
					'sssss',
					$pConnectorType, $pTopic, $pOrderNumber, $webhookdata, $webhookdataLength
				)) {
						if (!$stmt->execute()) {
							$result = 'str_DatabaseError';
							$resultParam = 'recordWebhookData execute ' . $db->error;
						} else {
							$result = 'success';
						}
				} else {

					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'recordWebhookData bind ' . $db->error;
				}
				if ($stmt) {
					$stmt->free_result();
					$stmt->close();
				}
			} else {
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'recordWebhookData prepare ' . $db->error;
			}

			$db->close();
		} else {
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'recordWebhookData connect ' . $db->error;
		}

		$resultArray['result'] = $result;
		$resultArray['resultParam'] = $resultParam;

		return $resultArray;
	}

	/**
	 * Get shopify productids from projectred in projectorderdatacache
	 *
	 * @param string $pProjectRef projectref to search for
	 * @return string Shopify Global ProductID
	 */
	public function getTempProductID(string $pProjectRef): string
	{
		$db = $this->getUtils()->getGlobalDBConnection();
		$connectorId = $this->getConnectorID();
		$productId = '';
		$returnId = '';

		$result = '';
		$resultParam = '';
		$resultArray = array();

		if ($db)
		{

			$stmt = $db->prepare('
									SELECT `externalproductid` FROM `PROJECTORDERDATACACHE`
								 	WHERE `projectref` = ? AND `connectorid` = ?
			 					');

				if ($stmt)
				{
					// bind params
					if ($stmt->bind_param('si',$pProjectRef, $connectorId))
					{
						if ($stmt->execute())
						{
							if ($stmt->store_result())
							{
								if ($stmt->num_rows > 0) {
									if ($stmt->bind_result($productId)) {
										while ($stmt->fetch())
										{
											$returnId = $productId;
										}
									} else {
										$returnArray['error'] = __FUNCTION__ . ' bind result error: ' . $db->error;
									}
								}
							}
							else
							{
								$resultArray['result'] = __FUNCTION__ . ' store result ' . $db->error;
							}

							$stmt->free_result();
							$stmt->close();
							$stmt = null;
						}
						else
						{
							$resultArray['result'] = __FUNCTION__ . ' execute  ' . $db->error;
						}
					}
					else
					{
						$resultArray['result'] = __FUNCTION__ . ' bind params  ' . $db->error;
					}
				}
				else
				{
					$resultArray['result'] = __FUNCTION__ . ' prepare  ' . $db->error;
				}
				$db->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = __FUNCTION__ . ' connect ' . $db->error;
		}

		$resultArray['result'] = $result;
		$resultArray['resultParam'] = $resultParam;

		return $returnId;
	}

	/**
	 * Check for shopify productid for the project in projectorderdatacache
	 * if it exists then the project has been edited so we can delete the temp product
	 *
	 * @param string $pProjectRef of the project
	 * @return string gid of the temp product deleted
	 */
	public function deleteExisitingTempProduct(string $pProjectRef): string
	{
		$tempProductID = $this->getTempProductID($pProjectRef);

		if ($tempProductID != '')
		{
			$product = new Product($this->initGraphQL());
			$product->deleteTempProduct($tempProductID);
		}

		return $tempProductID;
	}

	/**
	 * Check for shopify productid or variantid in the discount cache
	 * if it exists then we need to remove it as the temp product has been deleted
	 *
	 * @param string $pProductID of the temp product
	 * @param \stdClass $pCacheData passed in if cache data already retrieved
	 * @return array Discount Data
	 */
	public function deleteProductFromDiscountCacheData(string $pProductID, \stdClass $pCacheData = null): array
	{
		$data = $pCacheData;

		if (is_null($pCacheData)) {
			$data = json_decode($this->getShopifyDiscountData()['data']);
		}

		$autoDiscounts = isset($data->discounts->automaticdiscounts) ? $data->discounts->automaticdiscounts : [];
		$pricerules = isset($data->discounts->pricerules) ? $data->discounts->pricerules : [];
		$updateData = false;
		$updatedData = $data;
		$return = [];

		if ($pProductID != '')
		{
			foreach($autoDiscounts as $autoDiscount)
			{
				if (isset($autoDiscount->id))
				{
					$discountid = $autoDiscount->id;

					if (isset($autoDiscount->collections))
					{
						foreach($autoDiscount->collections as $collection)
						{
							$collectionId = $collection->id;

							if (isset($collection->products))
							{
								foreach($collection->products as $product)
								{
									if ($product->id === $pProductID)
									{
										unset($updatedData->discounts->automaticdiscounts->$discountid->collections->$collectionId->products->$pProductID);
										$updateData = true;
									}
								}
							}
						}
					}

					if (isset($autoDiscount->products))
					{
						foreach($autoDiscount->products as $product)
						{
							if ($product->id === $pProductID)
							{
								unset($updatedData->discounts->automaticdiscounts->$discountid->products->$pProductID);
								$updateData = true;
							}
						}
					}

					if (isset($autoDiscount->productVariants))
					{
						foreach($autoDiscount->productVariants as $variant)
						{
							if (isset($variant->parent_product_id))
							{
								if ($variant->parent_product_id === $pProductID)
								{
										$variantID = $variant->id;
										unset($updatedData->discounts->automaticdiscounts->$discountid->productVariants->$variantID);
										$updateData = true;
								}
							}
						}
					}
				}
			}

			foreach($pricerules as $pricerule)
			{
				if (isset($pricerule->id))
				{
					$ruleid = $pricerule->id;

					if (isset($pricerule->collections))
					{
						foreach($pricerule->collections as $collection)
						{
							$collectionId = $collection->id;

							if (isset($collection->products))
							{
								foreach($collection->products as $product)
								{
									if ($product->id === $pProductID)
									{
										unset($updatedData->discounts->pricerules->$ruleid->collections->$collectionId->products->$pProductID);
										$updateData = true;
									}
								}
							}
						}
					}

					if (isset($pricerule->products))
					{
						foreach($pricerule->products as $product)
						{
							if ($product->id === $pProductID)
							{
								unset($updatedData->discounts->pricerules->$ruleid->products->$pProductID);
								$updateData = true;
							}
						}
					}

					if (isset($pricerule->productVariants))
					{
						foreach($pricerule->productVariants as $variant)
						{
							if (isset($variant->parent_product_id))
							{
								if ($variant->parent_product_id === $pProductID)
								{
									$variantID = $variant->id;
									unset($updatedData->discounts->pricerules->$ruleid->productVariants->$variantID);
									$updateData = true;
								}
							}
						}
					}
				}
			}
		}

		if ($updateData)
		{
			$return = (array) $updatedData;
		}

		return $return;
	}

	/**
	 * Add a variant to a delivery profile
	 *
	 * @param string $pProfileId - id of the profile to update
	 * @param array $pInput - variant data to add
	 * @return void
	 */
	public function updateDeliveryProfile(string $pProfileId, array $pInput): void
	{
		$mutation = (new \GraphQL\Mutation('deliveryProfileUpdate'))
					->setVariables([new \GraphQL\Variable('id', 'ID', true), new \GraphQL\Variable('profile', 'DeliveryProfileInput', true)])
					->setArguments(['id' => '$id', 'profile' => '$profile'])
					->setSelectionSet(
						[
							(new \GraphQL\Query('profile'))->setSelectionSet([
								'id'
							]),
							(new \GraphQL\Query('userErrors'))->setSelectionSet([
								'field',
								'message'
							])
						]
					);

		$result = $this->runGraphQLQuery($this->initGraphQL(), $mutation, true, ['id' => $pProfileId, 'profile' => $pInput]);

		if (isset($result->userErrors))
		{
			if (count($result->userErrors) > 0)
			{
				error_log(print_r($result->userErrors,true));
			}
		}
	}

	/**
	 * Get current stored shipping data for store
	 *
	 * @return array Shipping data
	 */
	public function getShopifyShippingData() : array
	{
		$connectorUrl = $this->getVendorNameFromShopURL($this->shopURL);
		$db = $this->getUtils()->getGlobalDBConnection();
		$shippingData = '';
		$shippingDataLength = 0;
		$resultArray = array();

		if ($db)
		{
			$stmt = $db->prepare('SELECT `shippingprofiledata`, `shippingprofiledatalength` FROM `CONNECTORS`
								 	WHERE `connectorurl` = ?
			 					');

				if ($stmt)
				{
					// bind param d
					if ($stmt->bind_param('s', $connectorUrl))
					{
						if ($stmt->execute())
						{
							if ($stmt->store_result())
							{
								if ($stmt->num_rows > 0) {
									if ($stmt->bind_result($shippingData, $shippingDataLength)) {
										while ($stmt->fetch()) {

											if ($shippingDataLength > 0) {
												$shippingData = gzuncompress($shippingData, $shippingDataLength);
											}
										}
									} else {
										$returnArray['result'] = __FUNCTION__ . ' bind result error: ' . $db->error;
									}
								}
							}
							else
							{
								$resultArray['result'] = __FUNCTION__ . ' store result ' . $db->error;
							}

							$stmt->free_result();
							$stmt->close();
							$stmt = null;
						}
						else
						{
							$resultArray['result'] = __FUNCTION__ . ' execute  ' . $db->error;
						}
					}
					else
					{
						$resultArray['result'] = __FUNCTION__ . ' bind params  ' . $db->error;
					}
				}
				else
				{
					$resultArray['result'] = __FUNCTION__ . ' prepare  ' . $db->error;
				}
				$db->close();
		}
		else
		{
			$resultArray['result'] = __FUNCTION__ . ' connect ' . $db->error;
		}

		$resultArray['data'] = $shippingData;

		return $resultArray;
	}

	/**
	 * Check if temp products can be added to shipping profiles (based on parent catalog product being present)
	 *
	 * @param array pTempProductData - Product data to check if shipping profiles apply
	 * @return array result
	 */
	public function doShippingProfiles(array $pTempProductData): array
    {
		$shippingData = (array) json_decode($this->getShopifyShippingData()['data'],true);
		$result = [];
		$shippingProfileId = '';

		$productCode = implode('.', [$pTempProductData['collectioncode'], $pTempProductData['layoutcode']]);

		if (isset($shippingData['deliveryProfiles'])) {
			foreach ($shippingData['deliveryProfiles'] as $profileKey => $profile) {
				if (isset($profile['productVariants'])) {
					if ($profile['productVariants'][$productCode]) {
						$shippingProfileId = $profileKey;
						break;
					}
				}
			}
		}

        if ($shippingProfileId !== '')
        {
			$input = [ 'variantsToAssociate' => [    $pTempProductData['tempvariantid']   ]	];
			$this->updateDeliveryProfile($shippingProfileId, $input);
        }

		return $result;
    }

	/**
	 * Update Store Shipping Profile Data
	 *
	 * @param Array $pShippingDataArray containing the shipping data for the store
	 * @return array Result
	 */
	public function updateShopifyShippingData(array $pShippingDataArray): array
	{
		$vendor = $this->getVendorNameFromShopURL();
		$db = $this->getUtils()->getGlobalDBConnection();

		$result = '';
		$resultParam = '';
		$resultArray = array();

		$shippingdata = json_encode($pShippingDataArray);

		$shippingdataLength = strlen($shippingdata);
		if ($shippingdataLength > 15728640) {
			$shippingdata = gzcompress($shippingdata, 9);
		} else {
			$shippingdataLength = 0;
		}

		if ($db) {
			$stmt = $db->prepare('	UPDATE `CONNECTORS`
									SET
										`shippingprofiledata` = ?
										,`shippingprofiledatalength` = ?
									WHERE `connectorurl` = ?
								');

			if ($stmt) {
				if ($stmt->bind_param(
					'sis',
					$shippingdata, $shippingdataLength, $vendor
				)) {
						if (!$stmt->execute()) {
							$result = 'str_DatabaseError';
							$resultParam = 'updateShopifyShippingData execute ' . $db->error;
						}
				} else {

					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'updateShopifyShippingData bind ' . $db->error;
				}
				if ($stmt) {
					$stmt->free_result();
					$stmt->close();
				}
			} else {
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'updateShopifyShippingData prepare ' . $db->error;
			}

			$db->close();
		} else {
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'updateShopifyShippingData connect ' . $db->error;
		}

		$resultArray['result'] = $result;
		$resultArray['resultParam'] = $resultParam;

		return $resultArray;
	}

	/**
	 * Create task to get cache data for delivery profiles from bulk query
	 *
	 * @return array
	 */
	public function deliveryProfileUpdateTask()
	{
		$licenseKeyCode = $this->getLicenseKeyCode();
		$brandCode = $this->getBrandCode();
		$param1 = '';
		$param2 = '';
		$param3 = $this->getConnectorURL() . '.myshopify.com';

		return $this->getUtils()->createEvent(
			'TAOPIX_CONNECTORSHIPPINGPROFILECACHE',
			'',
			$licenseKeyCode,
			$brandCode,
			'',
			0,
			$param1,
			$param2,
			$param3,
			'',
			'',
			'',
			'',
			'',
			0,
			0,
			0,
			'',
			'',
			0
		);
	}

	/**
	 * Parse bulk query for delivery profiles and store in the DB
	 *
	 * @return void
	 */
	public function updateDeliveryProfiles()
	{
		$parsedDeliveryProfileData = $this->readDeliveryProfilesData();
		$dbResult = $this->updateShopifyShippingData($parsedDeliveryProfileData);
		return $dbResult;
	}

	/**
	 * Reads in delivery profile data from bulk query results and massages data into format we require
	 *
	 * @return array The parsed delivery profile data
	 */
	public function readDeliveryProfilesData() : array
	{
		$data = [];

		$fileData = function($filePath) {
			$file = fopen($filePath, 'r');

			if (!$file)
				die('file does not exist or cannot be opened');

			while (($line = fgets($file)) !== false) {
				yield $line;
			}

			fclose($file);
		};

		$extension = '.jsonl';
		$filePath = $this->getACConfig()['CONNECTORRESOURCESPATH'];
		$filePath = $this->getUtils()->correctPath($filePath, DIRECTORY_SEPARATOR, true);

		if ($this->getBrandCode() != '')
		{
			$filePath .=  $this->getBrandCode() . DIRECTORY_SEPARATOR;
		}

		$filePath .= 'deliveryProfiles' . DIRECTORY_SEPARATOR;

		$files[] = ['filename' => 'deliveryProfiles' . $extension, 'node' => 'deliveryProfiles'];

		foreach ($files as $file)
		{
			$node = $file['node'];

			foreach ($fileData($filePath . $file['filename']) as $line)
			{
				$line_item = json_decode($line);

				if (isset($line_item->__parentId))
				{
					if (property_exists($line_item, 'taopix_id'))
					{
						$profileId = explode('=', explode('?', $line_item->__parentId)[1])[1];
						$parentKey = "gid://shopify/DeliveryProfile/" . $profileId;

						if (isset($line_item->taopix_id->id))
						{
							$variantDataArray = explode(".", $line_item->taopix_id->value);
							$collectionCode = $variantDataArray[0];
							$layoutCode = $variantDataArray[1];

							$data[$node][$parentKey]['productVariants'][$collectionCode . '.' . $layoutCode] = [
								'id' => $line_item->id
							];
						}
					}
				}
				else
				{
					if (!isset($data[$node][$line_item->id]))
					{
						$data[$node][$line_item->id] = ['name' => $line_item->name];
					}
				}
			}

			$dateNow = date("YmdHis");
			$completedFilePath = $filePath . 'complete' . DIRECTORY_SEPARATOR . str_replace($extension, '-' . $dateNow . $extension, $file['filename']);

			$this->getUtils()->moveUploadedFile($filePath . $file['filename'], $completedFilePath);
		}

		return $data;
	}

	/**
	* @var string
	*/
	public $shippingProfilesQuery = "query {
		deliveryProfiles {
			edges {
				node {
					id
					name
					productVariantsCountV2 {
						capped
						count
					}
					profileItems {
						edges {
							node {
								id
								variants {
									edges {
										node {
											id
											taopix_id: metafield(namespace:\"taopix\", key:\"taopix_product_id\") {
												id,
												value
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	  }";

}
