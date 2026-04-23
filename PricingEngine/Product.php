<?php

namespace PricingEngine;

use PricingEngine\Component\ComponentProductMapping;
use PricingEngine\PriceBreakSet\PriceBreakSetInterface;

/**
 * Represents a product
 *
 * Product class for encapsulating logic to
 * represent a product instance.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class Product
{
	/**
	 * @var PriceBreakSetInterface
	 */
	private $priceBreakSet;

	/**
	 * @var string
	 */
	private $cost;

	/**
	 * @var string
	 */
	private $weight;

	/**
	 * @var ComponentProductMapping[]
	 */
	private $orderLineComponentMappings = [];

	/**
	 * @var ComponentProductMapping[]
	 */
	private $orderLineFooterComponentMappings = [];

	/**
	 * @var ComponentProductMapping[]
	 */
	private $orderFooterComponentMappings = [];

	/**
	 * Constructor
	 *
	 * @param PriceBreakSetInterface $priceBreakSet
	 * @param string $cost
	 * @param string $weight
	 */
	public function __construct(PriceBreakSetInterface $priceBreakSet, $cost, $weight)
	{
		$this->priceBreakSet = $priceBreakSet;
		$this->cost = $cost;
		$this->weight = $weight;
	}

	/**
	 * Get the product price break set
	 *
	 * Get the price break set used to calculate the product price.
	 *
	 * @return PriceBreakSetInterface
	 */
	public function getPriceBreakSet()
	{
		return $this->priceBreakSet;
	}

	/**
	 * Get the product cost
	 *
	 * Get the product cost of the product.
	 *
	 * @return string
	 */
	public function getCost()
	{
		return $this->cost;
	}

	/**
	 * Get the product weight
	 *
	 * Get the product weight of the product.
	 *
	 * @return string
	 */
	public function getWeight()
	{
		return $this->weight;
	}

	/**
	 * Set the order line component mappings
	 *
	 * Set a list of order line component mappings.
	 * Any existing mappings are first removed.
	 *
	 * @param array $componentProductMappings
	 * @return $this
	 */
	public function setOrderLineComponentMappings(array $componentProductMappings)
	{
		$this->orderLineComponentMappings = [];
		foreach ($componentProductMappings as $componentProductMapping) {
			$this->addOrderLineComponentMapping($componentProductMapping);
		}

		return $this;
	}

	/**
	 * Add an order line component mapping
	 *
	 * Add a new order line component mapping to the product.
	 * Any existing mapping is overwritten.
	 *
	 * @param ComponentProductMapping $componentProductMapping
	 * @return $this
	 */
	public function addOrderLineComponentMapping(ComponentProductMapping $componentProductMapping)
	{
		$componentCode = $componentProductMapping->getComponentCode();
		$this->orderLineComponentMappings[$componentCode] = $componentProductMapping;
		return $this;
	}

	/**
	 * Get order line component mapping
	 *
	 * Get an order line component mapping for the
	 * given component code.
	 *
	 * @param string $componentCode
	 * @return ComponentProductMapping
	 */
	public function getMappedOrderLineComponent($componentCode)
	{
		return @$this->orderLineComponentMappings[$componentCode];
	}

	/**
	 * Set the order line footer component mappings
	 *
	 * Set a list of order line footer component mappings.
	 * Any existing mappings are first removed.
	 *
	 * @param array $componentProductMappings
	 * @return $this
	 */
	public function setOrderLineFooterComponentMappings(array $componentProductMappings)
	{
		$this->orderLineFooterComponentMappings = [];
		foreach ($componentProductMappings as $componentProductMapping) {
			$this->addOrderLineFooterComponentMapping($componentProductMapping);
		}

		return $this;
	}

	/**
	 * Add an order line footer component mapping
	 *
	 * Add a new order line footer component mapping to the product.
	 * Any existing mapping is overwritten.
	 *
	 * @param ComponentProductMapping $componentProductMapping
	 * @return $this
	 */
	public function addOrderLineFooterComponentMapping(ComponentProductMapping $componentProductMapping)
	{
		$componentCode = $componentProductMapping->getComponentCode();
		$this->orderLineFooterComponentMappings[$componentCode] = $componentProductMapping;
		return $this;
	}

	/**
	 * Get order line footer component mapping
	 *
	 * Get an order line footer component mapping for the
	 * given component code.
	 *
	 * @param string $componentCode
	 * @return ComponentProductMapping
	 */
	public function getMappedOrderLineFooterComponent($componentCode)
	{
		return @$this->orderLineFooterComponentMappings[$componentCode];
	}

	/**
	 * Set the order footer component mappings
	 *
	 * Set a list of order footer component mappings.
	 * Any existing mappings are first removed.
	 *
	 * @param array $componentProductMappings
	 * @return $this
	 */
	public function setOrderFooterComponentMappings(array $componentProductMappings)
	{
		foreach ($componentProductMappings as $componentProductMapping) {
			$this->addOrderFooterComponentMapping($componentProductMapping);
		}

		return $this;
	}

	/**
	 * Add an order footer component mapping
	 *
	 * Add a new order footer component mapping to the product.
	 * Any existing mapping is overwritten.
	 *
	 * @param ComponentProductMapping $componentProductMapping
	 * @return $this
	 */
	public function addOrderFooterComponentMapping(ComponentProductMapping $componentProductMapping)
	{
		$componentCode = $componentProductMapping->getComponentCode();
		$this->orderFooterComponentMappings[$componentCode] = $componentProductMapping;
		return $this;
	}

	/**
	 * Get order footer component mapping
	 *
	 * Get an order footer component mapping for the
	 * given component code.
	 *
	 * @param string $componentCode
	 * @return ComponentProductMapping
	 */
	public function getMappedOrderFooterComponent($componentCode)
	{
		return @$this->orderFooterComponentMappings[$componentCode];
	}
}
