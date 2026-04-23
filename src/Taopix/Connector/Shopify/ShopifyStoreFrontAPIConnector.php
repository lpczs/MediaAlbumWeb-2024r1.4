<?php
namespace Taopix\Connector\Shopify;

class ShopifyStoreFrontAPIConnector extends \Taopix\Connector\Shopify\ShopifyConnector
{
	/**
	 * Shopify API version to use.
	 * 
	 * @var string
	 */
	static $APIVERSION = '2024-01';

	/**
	 * @var string
	 */
	private $storeFrontAccessToken = '';

	/**
	 * Sets the Store Front APi access token.
	 *
	 * @param string $pAccessToken Access token to set.
	 * @return ShopifyStoreFrontAPIConnector ShopifyStoreFrontAPIConnector instance.
	 */
	public function setStoreFrontAccessToken(string $pAccessToken): ShopifyStoreFrontAPIConnector
	{
		$this->storeFrontAccessToken = $pAccessToken;
		return $this;
	}

	/**
	 * Returns the Store Front API Access token value.
	 *
	 * @return string Store Front API access token.
	 */
	public function getStoreFrontAccessToken(): string
	{
		return $this->storeFrontAccessToken;
	}

	/**
	 * Returns if there is a Store Front API access token set.
	 *
	 * @return bool True if a Store Front APi token is set.
	 */
	private function hasStoreFrontAccessToken(): bool
	{
		return $this->storeFrontAccessToken !== '';
	}

	public function __construct(string $pShopURL)
	{
		$connectorDetails = parent::__construct($pShopURL);

		$accessToken = $connectorDetails['connectoraccesstoken2'];

		if ($accessToken === '')
		{
			$accessToken = $this->requestStoreFrontAccessToken();

			// Store the Store Front API access token against the connector.
			$this->updateConnector($connectorDetails['brandcode'], ['connectoraccesstoken2' => $accessToken]);
		}

		$this->setStoreFrontAccessToken($accessToken);
	}

	/**
	 * Does a request to get the Store Front API access token.
	 * 
	 * @return string The access token.
	 */
	private function requestStoreFrontAccessToken(): string
	{
		$postData = [
			'storefront_access_token' => 
			[
				"title" => "test"
			]
		];

		$postResult = $this->post('storefront_access_tokens.json', $postData);

		return $postResult->storefront_access_token->access_token;
	}

	/**
	 * Publishes a product to all stores.
	 *
	 * @param string $pProductID Product ID of the product to publish.
	 */
	public function publishStoreFrontProduct(string $pProductID): void
	{
		$endPoint = 'product_listings/' . $pProductID . '.json';
		$this->put($endPoint, []);
	}

	/**
	 * Creates the Shopify checkout instance.
	 *
	 * @throws \Exception If checkoutCreate response contains an error.
	 * @param array $pVariantList Array of store front IDs and projectrefs of the products to add to the checkout.
	 * @return \stdClass Object containing the GraphQL query result.
	 */
	public function createCheckout(array $pVariantList): \stdClass
	{
		$graphQL = $this->initStoreFrontGraphQL();

		$input = ['lineItems' => array_map(function($pVariant)
		{
			return [
				'variantId' => $pVariant['storefrontid'],
				'quantity' => 1,
				'customAttributes' => [
					['key' => '__taopix_project_id', 'value' => $pVariant['projectref']],
					['key' => '__taopix_product_code', 'value' => $pVariant['productcode']],
					['key' => '__taopix_product_sku', 'value' => $pVariant['productskucode']],
					['key' => '__taopix_product_name', 'value' => $pVariant['productname']],
					['key' => '__taopix_product_collection_code', 'value' => $pVariant['collectioncode']],
					['key' => '__taopix_product_collection_name', 'value' => $pVariant['collectionname']]
				]
			];
		}, $pVariantList)];

		if (isset($pVariantList[0]['emailaddress'])) {
			$input['email'] = $pVariantList[0]['emailaddress'];
		}

		$mutation = (new \GraphQL\Mutation('checkoutCreate'))
			->setVariables([new \GraphQL\Variable('input', 'CheckoutCreateInput', true)])
			->setArguments(['input' => '$input'])
			->setSelectionSet(
				[
					(new \GraphQL\Query('checkout'))
					->setSelectionSet([
						'id',
						'webUrl',
						(new \GraphQL\Query('lineItems'))
						->setArguments(['first' => 20])
						->setSelectionSet([
							(new \GraphQL\Query('edges'))->setSelectionSet([
								(new \GraphQL\Query('node'))->setSelectionSet([
									'id',
									'title'
								])
							])
						])
					]),
					(new \GraphQL\Query('checkoutUserErrors'))->setSelectionSet([
						'code',
						'field',
						'message'
					])
				]
			);

		$checkoutCreateResult = $this->runGraphQLQuery($graphQL, $mutation, false, ['input' => $input]);

		if (array_key_exists('code', $checkoutCreateResult->checkoutCreate->checkoutUserErrors))
		{
			$errorObj = $checkoutCreateResult->checkoutCreate->checkoutUserErrors;
			throw new \Exception($errorObj->message . ' ' . implode(',', $errorObj->field), $errorObj->code);
		}

		return $checkoutCreateResult;
	}

	/**
	 * Removes provided line items from a checkout. If all items are removed
	 * then the checkout will not be able to be completed.
	 *
	 * @throws \Exception If checkoutUserErrors response contains an error.
	 * @param string $pCheckoutID Checkout ID to remove line items from.
	 * @param array $pLineItemIDList List of line item IDs to remove from the checkout.
	 */
	public function removeProjectsFromCheckout(string $pCheckoutID, array $pLineItemIDList): void
	{
		$graphQL = $this->initStoreFrontGraphQL();

		$mutation = (new \GraphQL\Mutation('checkoutLineItemsRemove'))
			->setVariables([
				new \GraphQL\Variable('checkoutId', 'ID', true),
				new \GraphQL\Variable('lineItemIds', '[ID!]', true)
			])
			->setArguments(['checkoutId' => '$checkoutId', 'lineItemIds' => '$lineItemIds'])
			->setSelectionSet(
				[
					(new \GraphQL\Query('checkoutUserErrors'))->setSelectionSet([
						'code',
						'field',
						'message'
					])
				]
			);

		$checkoutLineItemsRemoveResult = $this->runGraphQLQuery($graphQL, $mutation, false, ['checkoutId' => $pCheckoutID, 'lineItemIds' => $pLineItemIDList]);

		if (array_key_exists('code', $checkoutLineItemsRemoveResult->checkoutLineItemsRemove->checkoutUserErrors))
		{
			$errorObj = $checkoutLineItemsRemoveResult->checkoutLineItemsRemove->checkoutUserErrors;
			throw new \Exception($errorObj->message . ' ' . implode(',', $errorObj->field), $errorObj->code);
		}
	}

	/**
	 * Builds the URL to the Admin API. If there is no Store Front access token then we added the authentication details to the URL.
	 * 
	 * @return string The base Admin API URL.
	 */
	private function buildAPIURL($pEndPoint): string
	{
		return sprintf('https://%s.myshopify.com/admin/api/%s/%s', $this->getVendorNameFromShopURL(), self::$APIVERSION, $pEndPoint);;
	}

	/**
	 * Executes a POST request to a Shopify API endpoint.
	 *
	 * @param string $pEndpoint Shopify endpoint to call.
	 * @param array $pPostFields Extra POST field parameters to pass.
	 * @return \stdClass Result object.
	 */
	private function post(string $pEndPoint, array $pPostFields): \stdClass
	{
		$header = [
			'Content-Type: application/JSON'
		];

		if (! $this->hasStoreFrontAccessToken())
		{
			// If the Store Front Access token isn't set, then we are trying to get one.
			$header[] = sprintf('X-Shopify-Access-Token: %s', $this->getAccessToken());
		}
		else
		{
			$header[] = sprintf('X-Shopify-StoreFront-Access-Token: %s', $this->getStoreFrontAccessToken());
		}

		$url = $this->buildAPIURL($pEndPoint);

		list(, $responseObj) = $this->initCurl($url, [
			CURLOPT_HTTPHEADER => $header,
			CURLOPT_POSTFIELDS => json_encode($pPostFields)
		]);

		return $responseObj;
	}

	/**
	 * Executes a PUT request to a Shopify API endpoint.
	 *
	 * @param string $pEndpoint Shopify endpoint to call.
	 * @param array $pPutFields Extra PUT field parameters to pass.
	 * @return \stdClass Result object.
	 */
	private function put(string $pEndpoint, array $pPutFields): \stdClass
	{
		$header = [
			'Content-Type: application/JSON',
			sprintf('X-Shopify-Access-Token: %s', $this->getAccessToken())
		];

		$url = $this->buildAPIURL($pEndpoint);

		list(, $responseObj) = $this->initCurl($url, [
			CURLOPT_HTTPHEADER => $header,
			CURLOPT_CUSTOMREQUEST => 'PUT',
			CURLOPT_POSTFIELDS => json_encode($pPutFields)
		]);

		return $responseObj;
	}

	/**
	 * @inheritDoc
	 * @throws \Exception If a status code 200 is not received.
	 */
	protected function initCURL(string $pURL, array $pCurlOptions): array
	{
		$utils = $this->getUtils();
		$headers = [];

		$pCurlOptions[CURLOPT_HEADERFUNCTION] = function($pCurl, $pHeader) use (&$headers)
		{
			$len = strlen($pHeader);
			$header = explode(':', $pHeader, 2);

			if (count($header) < 2)
			{
				return $len;
			}

			$headers[strtolower(trim($header[0]))][] = trim($header[1]);

			return $len;
		};

		list($httpCode, $responseObj) = parent::initCURL($pURL, $pCurlOptions);

		if ($httpCode === 429)
		{
			// Handle too many requests response (429).
			// https://shopify.dev/concepts/about-apis/rate-limits#resource-based-rate-limits

			$retryAfter = (int) $utils->getArrayParam($headers, 'retry-after', [1])[0];
	
			// Wait and retry the call.
			set_time_limit(30 + $retryAfter);
			sleep($retryAfter);
			list($httpCode, $responseObj) = $this->initCURL($pURL, $pCurlOptions);
		}
		else if ($httpCode !== 200)
		{
			// Handle all other error responses.

			$message = '';

			if ($responseObj !== null)
			{
				$message = $responseObj->errors;
			}
			else
			{
				$message = 'HTTP ERROR';
			}

			throw new \Exception($message, $httpCode);
		}

		return [$httpCode, $responseObj];
	}
}
