<?php

namespace Taopix\Connector\Shopify;

require_once __DIR__ . '/../../../../libs/external/vendor/autoload.php';

trait GraphQLTrait
{
	/**
	 * Shopify API version to use.
	 * 
	 * @var string
	 */
	static $APIVERSION = '2024-01';

	/**
	 * Initialises a GraphQL client instance.
	 *
	 * @return \GraphQL\Client The client instance.
	 */
	public function initGraphQL($pApiVersion = ''): \GraphQL\Client
	{
		$apiVersion = ($pApiVersion == '') ? self::$APIVERSION : $pApiVersion;

		return new \GraphQL\Client(
			$this->getShopURL() . 'admin/api/' . $apiVersion . '/graphql.json',
			['X-Shopify-Access-Token' => $this->getAccessToken()]
		);
	}

	/**
	 * Initialised a GraphQL client instance for the Store Front API.
	 *
	 * @return \GraphQL\Client The client instance.
	 */
	public function initStoreFrontGraphQL(): \GraphQL\Client
	{
		return new \GraphQL\Client(
			$this->getShopURL() . '/api/' . self::$APIVERSION . '/graphql.json',
			['X-Shopify-Storefront-Access-Token' => $this->getStoreFrontAccessToken()]
		);
	}

	/**
	 * Executes a given GraphQL query. Will attempt to retry automatically if the requests are throttled.
	 *
	 * @throws \Exception if an error is encountered when running the query.
	 * @param \GraphQL\Client $pGraphQL The GraphQL instance to use.
	 * @param \GraphQL\Query $pQuery The GraphQL query to execute.
	 * @param bool $pResultsAsArray Optional. True to return the results as an array. False to return as an object.
	 * @param array $pVariables Optional. GraphQL query variables.
	 * @return mixed Result data as an array or an object.
	 */
	public function runGraphQLQuery(\GraphQL\Client $pGraphQL, \GraphQL\Query $pQuery, bool $pResultsAsArray = false, array $pVariables = [])
	{
		$data = new \stdClass();

		try
		{
			$runQueryResult = $pGraphQL->runQuery($pQuery, $pResultsAsArray, $pVariables);
			$data = $runQueryResult->getData();	
		}
		catch (\GraphQL\Exception\QueryError $pError)
		{
			if (strtolower($pError->getMessage()) === 'throttled')
			{
				// If the request is throttled, wait a random number of seconds and retry.
				// https://help.shopify.com/api/graphql-admin-api/graphql-admin-api-rate-limits
				$timeLimit = rand(1, 4);
				set_time_limit(30 + $timeLimit);
				sleep($timeLimit);
		
				// Re-run the query.
				$data = $this->runGraphQLQuery($pGraphQL, $pQuery, $pResultsAsArray, $pVariables);
			}
			else
			{
				throw new \Exception($pError->getMessage(), $pError->getCode());
			}
		}

		return $data;
	}
}
