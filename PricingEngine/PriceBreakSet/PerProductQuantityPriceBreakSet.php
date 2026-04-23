<?php

namespace PricingEngine\PriceBreakSet;

use PricingEngine\BCMath;
use PricingEngine\CurrencyInterface;
use PricingEngine\Enum\FinancialPrecision;
use PricingEngine\Price\Price;
use PricingEngine\PriceBreak\PerProductQuantityPriceBreak;
use PricingEngine\PriceBreakSet\Exception\PriceBreakNotFoundException;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Pricing model for Per Product Quantity pricing
 *
 * This pricing model takes the product quantity and
 * calculates the price. Used by both products and
 * components.
 *
 * The equation is:
 *   price = base + (unit * productQuantity) - lineSubtract
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class PerProductQuantityPriceBreakSet extends AbstractPriceBreakSet
{
	/**
	 * @var PerProductQuantityPriceBreak[]
	 */
	protected $priceBreaks;

	/**
	 * @var int
	 */
	protected $priceBreakCount;

	/**
	 * Constructor
	 *
	 * @param PerProductQuantityPriceBreak[] $priceBreaks
	 * @param string $cost
	 * @param string $weight
	 * @param bool $isFixedQuantityRanges
	 * @param bool $inheritParentQuantity
	 * @param CurrencyInterface $currency
	 * @param TaxRateInterface|null $taxRate
	 */
	public function __construct(array $priceBreaks, $cost, $weight, $isFixedQuantityRanges, $inheritParentQuantity, CurrencyInterface $currency, TaxRateInterface $taxRate = null)
	{
		$this->priceBreaks = $priceBreaks;
		$this->priceBreakCount = count($this->priceBreaks);
		parent::__construct($cost, $weight, $isFixedQuantityRanges, $inheritParentQuantity, $currency, $taxRate);
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
	 * @param string $transformStage
	 * @param bool $canUpdateQuantities
	 * @param bool $canApplyBase
	 * @param bool $canApplyLineSubtract
	 * @return Price
	 * @throws PriceBreakNotFoundException
	 */
	protected function calculatePrice(TaxRateInterface $orderTaxRate, $isShowPricesWithTax,
		&$productQuantity, &$pageCount, &$componentQuantity, $priceBreakQuantity,
		$transformStage, $canUpdateQuantities, $canApplyBase, $canApplyLineSubtract)
	{
		// Select a price break with a quantity in range
		$selectedPriceBreak = null;
		foreach ($this->priceBreaks as $i => $priceBreak) {
			// Make product quantity updates if required
			if ($canUpdateQuantities && 0 === $i && $this->isFixedQuantityRanges() && $productQuantity < $priceBreak->getEndQuantity()) {
				$productQuantity = $priceBreak->getEndQuantity();
			} elseif ($canUpdateQuantities && 0 === $i && $productQuantity < $priceBreak->getStartQuantity()) {
				$productQuantity = $priceBreak->getStartQuantity();
			} elseif ($canUpdateQuantities && $this->priceBreakCount - 1 === $i && $productQuantity > $priceBreak->getEndQuantity()) {
				$productQuantity = $priceBreak->getEndQuantity();
			}

			if ($productQuantity >= $priceBreak->getStartQuantity() && $productQuantity <= $priceBreak->getEndQuantity()) {
				$selectedPriceBreak = $priceBreak;
				break;
			}
		}

		// If no price break was found, abort by throwing an exception
		if (null === $selectedPriceBreak) {
			throw new PriceBreakNotFoundException();
		}

		// Calculate the product price
		$unitPrice = $selectedPriceBreak->getUnit();
		$fullPrice = bcmul($unitPrice, $productQuantity, FinancialPrecision::PLACES);

		if ($canApplyBase) {
			$fullPrice = bcadd($selectedPriceBreak->getBase(), $fullPrice, FinancialPrecision::PLACES);
		}

		if ($canApplyLineSubtract) {
			$fullPrice = bcsub($fullPrice, $selectedPriceBreak->getLineSubtract(), FinancialPrecision::PLACES);
		}

		// Ensure the price is not negative
		if (bccomp($fullPrice, 0, FinancialPrecision::PLACES) === -1) {
			$fullPrice = '0';
		} else {
			$unitPrice = BCMath::round(bcdiv($fullPrice, $productQuantity, FinancialPrecision::PLACES), $this->currency->getDecimalPlaces());
		}

		// Convert to the clients currency and round to their currency places
		$fullPrice = $this->currency->exchange($fullPrice);
		$cost = bcmul($productQuantity, $this->cost, FinancialPrecision::COST_PLACES);
		$weight = bcmul($productQuantity, $this->weight, FinancialPrecision::WEIGHT_PLACES);

		return Price::createFromSellPrice($this->currency, $fullPrice, $unitPrice, $productQuantity, $cost, $weight, $orderTaxRate, $this->grossTaxRate);
	}
}
