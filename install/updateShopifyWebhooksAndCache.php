<?php
use Taopix\Connector\Connector;
use Taopix\Connector\Shopify\GraphQLTrait;
use Taopix\Connector\Shopify\Webhooks;

class updateShopifyWebhooksAndCache extends ExternalScript
{
	use GraphQLTrait;
	private $shopURL = '';
	private $accessToken = '';

	public function setShopURL($pShopURL)
	{
		$this->shopURL = $pShopURL;
	}

	public function getShopURL()
	{
		return $this->shopURL;
	}

	public function setAccessToken($pAccessToken)
	{
		$this->accessToken = $pAccessToken;
	}

	public function getAccessToken()
	{
		return $this->accessToken;
	}

	private function queryWebhooks($pGraphQL): \stdClass
	{
		$this->printMsg('queryWebhooks');
		$query = (new \GraphQL\Query('webhookSubscriptions'))
			->setArguments(['first' => 25])
				->setSelectionSet([
					(new \GraphQL\Query('edges'))->setSelectionSet([
						(new \GraphQL\Query('node'))->setSelectionSet([
							'id',
							'topic'
						])
					])
				]);

		return $this->runGraphQLQuery($pGraphQL, $query, false);
	}

	public function run()
	{
		$this->printMsg('Get Connector Details');
		$connectors = $this->getShopifyConnectors();
		
		foreach($connectors as $connector)
		{
			$this->printMsg('Connector - ' . $connector['url']);

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
				'refvalue' => [$connector['url']],
				'reftype' => ['s']
			];

			$this->setShopURL('https://' . $connector['url'] . '.myshopify.com/');
			$this->setAccessToken($connector['accesstoken1']);

			// Don't call Shopify Connector directly as it may contain queries
			// to database columns that don't exists yet.
			// i.e. Upgrading to 2022r1 from a version before 2021r3.4 will attempt
			// to use columsn used by Component Upsell.
			$shopifyConnector = new Connector('shopify', $queryArray);
			$graphQL = $this->initGraphQL();
			$webhooks = new Webhooks($graphQL, $shopifyConnector->getControlCentreURL());
			$result = $this->queryWebhooks($graphQL);

			$count = count($result->webhookSubscriptions->edges);

			$this->printMsg('Found ' . $count . ' webhook subscriptions');

			foreach($result->webhookSubscriptions->edges as $webhook)
			{
				if ($webhook->node->topic == 'PRODUCTS_UPDATE')
				{
					$this->printMsg('Updating PRODUCTS_UPDATE webhook subscription id ' . $webhook->node->id);
					$webhooks->updateSubscription($webhook->node->id);
				}
			}
		}

		if (count($connectors) == 0)
		{
			$this->printMsg('No Connectors Found');
		}

	}

	/**
	 * Get connector URLs
	 *
	 * @returns array
	 */
	private function getShopifyConnectors()
	{
		$connectorURL = '';
		$connectorAccessToken1 = '';
		$connectors = [];

		$query = "	SELECT `connectorurl`, `connectoraccesstoken1`
					FROM `connectors` 
					WHERE `connectorname` = 'SHOPIFY' 
					AND `connectoraccesstoken1` <> ''
				 ";

		if ($stmt = $this->dbConnection->prepare($query))
		{
			if ($stmt->bind_result($connectorURL, $connectorAccessToken1))
			{
				if ($stmt->execute())
				{
					while($stmt->fetch())
					{
						$connectors[] = ['url' => $connectorURL, 'accesstoken1' => $connectorAccessToken1];
					}
				}
			}
		}

		return $connectors;
	}

	/**
	 * prints a message to the screen.
	 *
	 * @param string $pMsg The message text.
	 */
	private function printMsg($pMsg)
	{
		echo $pMsg . PHP_EOL;
	}
}
