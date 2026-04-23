<?php

namespace PricingEngine\PriceBreakSet;

use PricingEngine\Enum\PriceBreakTransformationStage;
use PricingEngine\Price\Price;
use PricingEngine\Tax\TaxRateInterface;
use PricingEngine\PriceBreakSet\Exception\PriceBreakNotFoundException;

/**
 * Price representation
 *
 * Represent a price for an order line
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
interface PriceBreakSetInterface
{
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
		$canApplyBase = true, $canApplyLineSubtract = true);

	/**
	 * Get gross tax rate
	 *
	 * Get the tax rate of a gross price break set configuration.
	 * Returns null if the price break set is configured with net
	 * prices.
	 *
	 * @return TaxRateInterface|null
	 */
	public function getGrossTaxRate();

	/**
	 * Check if using inherit parent quantity
	 *
	 * Check if using the inherit parent quantity pricing
	 * feature.
	 *
	 * @return bool
	 */
	public function isInheritParentQuantity();

	/**
	 * Get unit cost
	 *
	 * Get the unit cost of the price break set.
	 *
	 * @return string
	 */
	public function getCost();

	/**
	 * Set unit cost
	 *
	 * Set the unit cost of the price break set.
	 *
	 * @param string $cost
	 * @return $this
	 */
	public function setCost($cost);

	/**
	 * Get unit weight
	 *
	 * Get the unit weight of the price break set.
	 *
	 * @return string
	 */
	public function getWeight();

	/**
	 * Set unit weight
	 *
	 * Set the unit weight of the price break set.
	 *
	 * @param string $weight
	 * @return $this
	 */
	public function setWeight($weight);
}
