<?php

namespace Taopix\Connector\Shopify;

use Taopix\Connector\Shopify\Collection\ProductCollectionCollection;

class ProductCollection
{
	use GraphQLTrait;

	/**
	 * @var \GraphQL\Client
	 */
	private $graphQL;

	/**
	 * Sets the GraphQL instance,
	 *
	 * @param \GraphQL\Client $pGraphQL GraphQL instance to set.
	 * @return Product Product instance.
	 */
	public function setGraphQL(\GraphQL\Client $pGraphQL): ProductCollection
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

	public function __construct(\GraphQL\Client $pGraphQL)
	{
		$this->setGraphQL($pGraphQL);
	}

	/**
	 * Creates a product collection in Shopify.
	 *
	 * @param array $pCollectionsToCreate Array of collections to create.
	 * @return object GraphQL query result object.
	 */
	public function createCollection(array $pCollectionsToCreate)
	{
		$mutation = (new \GraphQL\Mutation('collectionCreate'))
			->setVariables([new \GraphQL\Variable('input', 'CollectionInput', true)])
			->setArguments(['input' => '$input'])
			->setSelectionSet(
				[
					(new \GraphQL\Query('collection'))->setSelectionSet([
						'id'
					]),
					(new \GraphQL\Query('userErrors'))->setSelectionSet([
						'field',
						'message'
					])
				]
			);

		return $this->runGraphQLQuery($this->graphQL, $mutation, false, ['input' => $pCollectionsToCreate]);
	}

	/**
	 * Returns a collection of tax level collections from Shopify.
	 *
	 * @return ProductCollectionCollection Collection containing tax level collections.
	 */
	public function getTaxLevelCollections(): ProductCollectionCollection
	{
		$mutation = (new \GraphQL\Query('collections'))
			->setArguments(['first' => 5, 'query' => 'title:Tax Level*'])
			->setSelectionSet(
				[
					(new \GraphQL\Query('edges'))->setSelectionSet([
						(new \GraphQL\Query('node'))->setSelectionSet([
							'id',
							'storefrontId',
							'title'
						])
					])
				]
			);

		$collectionsResult = $this->runGraphQLQuery($this->graphQL, $mutation, true, []);

		$taxLevelCollections = [];

		if (count($collectionsResult['collections']['edges']) > 0)
		{
			$taxLevelCollections = array_map(function($pNode)
			{
				return $pNode['node'];
			}, $collectionsResult['collections']['edges']);
		}

		return new ProductCollectionCollection($taxLevelCollections);
	}

	/**
	 * Adds a list of products to a Shopify collection.
	 *
	 * @param string $pCollectionID Collection ID to add products to. This is the base64 ID.
	 * @param array $pProductIDList List of prodict IDs to add to the collection.
	 * @return object GraphQL query result object.
	 */
	public function addProductsToCollection(string $pCollectionID, array $pProductIDList)
	{
		$mutation = (new \GraphQL\Mutation('collectionAddProducts'))
			->setVariables([new \GraphQL\Variable('id', 'ID', true), new \GraphQL\Variable('productIds', '[ID!]', true)])
			->setArguments(['id' => '$id', 'productIds' => '$productIds'])
			->setSelectionSet(
				[
					(new \GraphQL\Query('userErrors'))->setSelectionSet([
						'field',
						'message'
					])
				]
			);

		return $this->runGraphQLQuery($this->graphQL, $mutation, false, ['id' => $pCollectionID, 'productIds' => $pProductIDList]);
	}
}
