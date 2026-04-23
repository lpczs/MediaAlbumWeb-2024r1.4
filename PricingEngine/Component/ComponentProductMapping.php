<?php

namespace PricingEngine\Component;

use PricingEngine\Price\Price;
use PricingEngine\PriceBreakSet\Exception\PriceBreakNotFoundException;
use PricingEngine\PriceBreakSet\PriceBreakSetInterface;
use PricingEngine\Tax\TaxRateInterface;

/**
 * A component to product mapping
 *
 * Represents the mapping between a component
 * and a product, encapsulating the relevant
 * data that makes up the mapping.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class ComponentProductMapping
{
	/**
	 * @var AbstractComponent
	 */
	private $component;

	/**
	 * @var PriceBreakSetInterface
	 */
	private $priceBreakSet;

	/**
	 * @var ComponentProductMapping[]
	 */
	private $subComponentMappings;

	/**
	 * Constructor
	 *
	 * @param AbstractComponent $component
	 * @param PriceBreakSetInterface $priceBreakSet
	 */
	public function __construct(AbstractComponent $component, PriceBreakSetInterface $priceBreakSet)
	{
		$this->component = $component;
		$this->priceBreakSet = $priceBreakSet;
	}

	/**
	 * Check if inherit parent quantity is enabled
	 * for the component
	 *
	 * Check if inherit parent quantity is enabled
	 * in the price break set used in the product/component
	 * mapping.
	 *
	 * @return bool
	 */
	public function isInheritParentQuantity()
	{
		return $this->priceBreakSet->isInheritParentQuantity();
	}

	/**
	 * Get mapped components code
	 *
	 * Get the mapped component code from the component.
	 *
	 * @return string
	 */
	public function getComponentCode()
	{
		return $this->component->getComponentCode();
	}

	/**
	 * Get component
	 *
	 * Get the component mapped to the product.
	 *
	 * @return AbstractComponent
	 */
	public function getComponent()
	{
		return $this->component;
	}

	/**
	 * Add a sub component mapping
	 *
	 * Add a sub component mapping to this mapping. Each
	 * mapping is namespaced to the component code under this
	 * component. Note that the same sub component could be used in
	 * another mapping.
	 *
	 * @param ComponentProductMapping $subComponentMapping
	 * @return $this
	 */
	public function addSubComponentMapping(ComponentProductMapping $subComponentMapping)
	{
		$componentCode = $subComponentMapping->getComponentCode();
		$this->subComponentMappings[$componentCode] = $subComponentMapping;
		return $this;
	}

	/**
	 * Get a subcomponent mapping
	 *
	 * Get a subcomponent mapping for the given component code.
	 *
	 * @param string $componentCode
	 * @return ComponentProductMapping
	 */
	public function getSubComponentMapping($componentCode)
	{
		return @$this->subComponentMappings[$componentCode];
	}

	/**
	 * Calculate the component price
	 *
	 * Using the supplied parameters, calculate the price of the
	 * product/component. Returns null if the price cannot be
	 * calculated.
	 *
	 * @param TaxRateInterface $taxRate
	 * @param bool $isShowPricesWithTax
	 * @param int $productQuantity
	 * @param int $pageCount
	 * @param int $componentQuantity
	 * @return Price|null
	 */
	public function calculatePrice(TaxRateInterface $taxRate, $isShowPricesWithTax, $productQuantity, $pageCount, &$componentQuantity)
	{
		try {
			return $this->priceBreakSet->createPrice($taxRate, $isShowPricesWithTax, $productQuantity, $pageCount, $componentQuantity, $componentQuantity);
		} catch(PriceBreakNotFoundException $ex) {
			return null;
		}
	}
}
