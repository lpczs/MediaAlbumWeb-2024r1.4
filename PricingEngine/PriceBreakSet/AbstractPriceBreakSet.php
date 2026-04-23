<?php

namespace PricingEngine\PriceBreakSet;

use PricingEngine\CurrencyInterface;
use PricingEngine\Enum\PriceBreakTransformationStage;
use PricingEngine\Price\Price;
use PricingEngine\PriceBreakSet\Exception\PriceBreakNotFoundException;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Abstract price break set
 *
 * Abstract class functionality for price break set.
 * Sub classes should extend this class to get base
 * functionality for handling price calculation logic.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
abstract class AbstractPriceBreakSet implements PriceBreakSetInterface
{
	/**
	 * @var CurrencyInterface
	 */
	protected $currency;

	/**
	 * @var TaxRateInterface
	 */
	protected $grossTaxRate;

	/**
	 * @var bool
	 */
	protected $isFixedQuantityRanges;

	/**
	 * @var bool
	 */
	protected $inheritParentQuantity;

	/**
	 * @var string
	 */
	protected $cost;

	/**
	 * @var string
	 */
	protected $weight;

	/**
	 * Constructor
	 *
	 * @param string $cost
	 * @param string $weight
	 * @param bool $isFixedQuantityRanges
	 * @param bool $inheritParentQuantity
	 * @param CurrencyInterface $currency
	 * @param TaxRateInterface|null $grossTaxRate
	 */
	public function __construct($cost, $weight, $isFixedQuantityRanges, $inheritParentQuantity,
		CurrencyInterface $currency, TaxRateInterface $grossTaxRate = null)
	{
		$this->cost = $cost;
		$this->weight = $weight;
		$this->isFixedQuantityRanges = (bool) $isFixedQuantityRanges;
		$this->inheritParentQuantity = (bool) $inheritParentQuantity;
		$this->currency = $currency;
		$this->grossTaxRate = $grossTaxRate;
	}

	/**
	 * Create a running price instance
	 *
	 * Use the price break set specific implementation to calculate prices
	 * and load them in to a new Price instance and return.
	 *
	 * @param TaxRateInterface $orderTaxRate
	 * @param bool $showPricesWithTax
	 * @param int $productQuantity
	 * @param int $pageCount
	 * @param int $componentQuantity
	 * @param int $priceBreakQuantity
	 * @param int $transformStage
	 * @param bool $canUpdateQuantities
	 * @param bool $canApplyBase
	 * @param bool $canApplyLineSubtract
	 * @return Price
	 * @throws PriceBreakNotFoundException
	 */
	public function createPrice(TaxRateInterface $orderTaxRate, $showPricesWithTax,
		&$productQuantity, &$pageCount = 0, &$componentQuantity = 0, $priceBreakQuantity = null,
		$transformStage = PriceBreakTransformationStage::POST_TRANSFORM, $canUpdateQuantities = true,
		$canApplyBase = true, $canApplyLineSubtract = true)
	{
		return $this->calculatePrice(
			$orderTaxRate,
			$showPricesWithTax,
			$productQuantity,
			$pageCount,
			$componentQuantity,
			$priceBreakQuantity,
			$transformStage,
			$canUpdateQuantities,
			$canApplyBase,
			$canApplyLineSubtract
		);
	}

	/**
	 * Get gross tax rate
	 *
	 * Get the tax rate of a gross price break set configuration.
	 * Returns null if the price break set is configured with net
	 * prices.
	 *
	 * @return TaxRateInterface|null
	 */
	public function getGrossTaxRate()
	{
		return $this->grossTaxRate;
	}

	/**
	 * Check if using inherit parent quantity
	 *
	 * Check if using the inherit parent quantity pricing
	 * feature.
	 *
	 * @return bool
	 */
	public function isInheritParentQuantity()
	{
		return $this->inheritParentQuantity;
	}

	/**
	 * Get unit cost
	 *
	 * Get the unit cost of the price break set.
	 *
	 * @return string
	 */
	public function getCost()
	{
		return $this->cost;
	}

	/**
	 * Set unit cost
	 *
	 * Set the unit cost of the price break set.
	 *
	 * @param string $cost
	 * @return $this
	 */
	public function setCost($cost)
	{
		$this->cost = $cost;
		return $this;
	}

	/**
	 * Get unit weight
	 *
	 * Get the unit weight of the price break set.
	 *
	 * @return string
	 */
	public function getWeight()
	{
		return $this->weight;
	}

	/**
	 * Set unit weight
	 *
	 * Set the unit weight of the price break set.
	 *
	 * @param string $weight
	 * @return $this
	 */
	public function setWeight($weight)
	{
		$this->weight = $weight;
		return $this;
	}

	/**
	 * Check if using fixed quantity ranges
	 *
	 * Check if the price break is using fixed quantity
	 * ranges, e.g. 10, 20, 30, 40.
	 *
	 * @return bool
	 */
	protected function isFixedQuantityRanges()
	{
		return $this->isFixedQuantityRanges;
	}

	/**
	 * Calculate the product price
	 *
	 * Apply the pricing model to the base, unit and line subtract values for the product
	 * and page quantity to calculate the line price.
	 *
	 * @param TaxRateInterface $orderTaxRate
	 * @param bool $isShowPricesWithTax
	 * @param int $productQuantity
	 * @param int $pageCount
	 * @param int $componentQuantity
	 * @param int $priceBreakQuantity
	 * @param string $transformStage,
	 * @param bool $canUpdateQuantities
	 * @param bool $canApplyBase
	 * @param bool $canApplyLineSubtract
	 * @return Price
	 * @throws PriceBreakNotFoundException
	 */
	abstract protected function calculatePrice(TaxRateInterface $orderTaxRate, $isShowPricesWithTax,
		&$productQuantity, &$pageCount, &$componentQuantity,
		$priceBreakQuantity, $transformStage, $canUpdateQuantities, $canApplyBase, $canApplyLineSubtract);
}
