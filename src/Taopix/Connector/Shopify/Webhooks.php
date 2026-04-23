<?php

namespace Taopix\Connector\Shopify;

class Webhooks
{
	use GraphQLTrait;

	/**
	 * @var \GraphQL\Client
	 */
	private $graphQL;
	private $subscriptions;
	private $controlCentreURL;

	/**
	 * Sets the GraphQL instance,
	 *
	 * @param \GraphQL\Client $pGraphQL GraphQL instance to set.
	 * @return Product Product instance.
	 */
	public function setGraphQL(\GraphQL\Client $pGraphQL): Webhooks
	{
		$this->graphQL = $pGraphQL;

		return $this;
	}

	/**
	 * Returns the GraphQL instance.
	 *
	 * @return \GraphQL\Client GraphQL instance.
	 */
	public function getGraphQL($pGraphQL): \GraphQL\Client
	{
		return $pGraphQL;
	}
	
	public function __construct(\GraphQL\Client $pGraphQL, $pControlCentreURL)
	{
		$this->controlCentreURL = $pControlCentreURL;
		
		$this->subscriptions = array(
			'ORDERS_PAID' => $this->controlCentreURL . '/Connectors/Shopify/webhooks.php',
			'PRODUCTS_DELETE' => $this->controlCentreURL . '/Connectors/Shopify/webhooks.php',
			'PRODUCTS_UPDATE' => $this->controlCentreURL . '/Connectors/Shopify/webhooks.php',
			'BULK_OPERATIONS_FINISH' => $this->controlCentreURL . '/Connectors/Shopify/webhooks.php',
			'PROFILES_UPDATE' => $this->controlCentreURL . '/Connectors/Shopify/webhooks.php',
			'PROFILES_CREATE' => $this->controlCentreURL . '/Connectors/Shopify/webhooks.php',
			'PROFILES_DELETE' => $this->controlCentreURL . '/Connectors/Shopify/webhooks.php',
			'DISCOUNTS_UPDATE' => $this->controlCentreURL . '/Connectors/Shopify/webhooks.php',
			'DISCOUNTS_CREATE' => $this->controlCentreURL . '/Connectors/Shopify/webhooks.php',
			'DISCOUNTS_DELETE' => $this->controlCentreURL . '/Connectors/Shopify/webhooks.php'
		);

		$this->setGraphQL($pGraphQL);
	}

	/**
	 * subscribe to webhooks in shopify
	 *
	 * @param Array pTopics - if specific topics to install
	 */
	public function subscribe(array $pTopics = []): void
	{
		$mutation = (new \GraphQL\Mutation('webhookSubscriptionCreate'))
			->setVariables([new \GraphQL\Variable('topic', 'WebhookSubscriptionTopic', true), new \GraphQL\Variable('webhookSubscription', 'WebhookSubscriptionInput', true)])
			->setArguments(['topic' => '$topic', 'webhookSubscription' => '$webhookSubscription'])
			->setSelectionSet(
				[
					(new \GraphQL\Query('webhookSubscription'))->setSelectionSet([
						'id'
					]),
					(new \GraphQL\Query('userErrors'))->setSelectionSet([
						'field',
						'message'
					])
				]
			);

		if (count($pTopics) > 0)
		{
			$subscriptions = $pTopics;
		} else {
			$subscriptions = $this->subscriptions;
		}

		foreach ($subscriptions as $webhook => $subscription)
		{
			if ($webhook == 'PRODUCTS_UPDATE') 
			{
				$subscriptionData = [
					'format' => 'JSON',
					'callbackUrl' => $subscription,
					'metafieldNamespaces' => ['taopix'],
					'includeFields' => [
						"id",
						"title",
						"descriptionHTML",
						"productType",
						"created_at",
						"updated_at",
						"variants",
						"metafields",
						"options",
						"tags",
						"images"
					]
				];
			}
			else 
			{
				$subscriptionData = [
					'format' => 'JSON',
					'callbackUrl' => $subscription
				];
			}
			
			$result = $this->runGraphQLQuery($this->graphQL, $mutation, false, ['topic' => $webhook, 'webhookSubscription' => $subscriptionData]);
			
			if (isset($result->userErrors))
			{
				if (count($result->userErrors) > 0) 
				{
					error_log(print_r($result->userErrors,true));
				}
			}
		}
	}

	/**
	 * update webhooks in shopify
	 * 
	 * @param string pWebhookId - id of webhook to be updated
	 */
	public function updateSubscription(string $pWebhookId): void
	{
		$mutation = (new \GraphQL\Mutation('webhookSubscriptionUpdate'))
			->setVariables([new \GraphQL\Variable('id', 'ID', true), new \GraphQL\Variable('webhookSubscription', 'WebhookSubscriptionInput', true)])
			->setArguments(['id' => '$id', 'webhookSubscription' => '$webhookSubscription'])
			->setSelectionSet(
				[
					(new \GraphQL\Query('webhookSubscription'))->setSelectionSet([
						'id'
					]),
					(new \GraphQL\Query('userErrors'))->setSelectionSet([
						'field',
						'message'
					])
				]
			);

			$subscriptionData = [
				'format' => 'JSON',
				'metafieldNamespaces' => ['taopix'],
				'includeFields' => [
					"id",
					"title",
					"descriptionHTML",
					"productType",
					"created_at",
					"updated_at",
					"variants",
					"metafields",
					"options",
					"tags",
					"images"
				]
			];
		
		$this->runGraphQLQuery($this->graphQL, $mutation, false, ['id' => $pWebhookId, 'webhookSubscription' => $subscriptionData]);
	}
}
