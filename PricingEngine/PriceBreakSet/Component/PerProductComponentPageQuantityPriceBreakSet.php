<?php

namespace PricingEngine\PriceBreakSet\Component;

use PricingEngine\BCMath;
use PricingEngine\CurrencyInterface;
use PricingEngine\Enum\FinancialPrecision;
use PricingEngine\Price\Price;
use PricingEngine\PriceBreak\Component\PerProductComponentPageQuantityPriceBreak;
use PricingEngine\PriceBreakSet\AbstractPriceBreakSet;
use PricingEngine\PriceBreakSet\Exception\PriceBreakNotFoundException;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Price for Per Product, Per Component,
 * Per Page Quantity pricing model
 *
 * This pricing model takes the product quantity, the
 * component quantity and the page count and calculates
 * the price. Used by components only.
 *
 * The equation is:
 *   price = base + (unit * pageQuantity) - lineSubtract
 *   price = productQuantity * componentQuantity * price
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class PerProductComponentPageQuantityPriceBreakSet extends AbstractPriceBreakSet
{
	/**
	 * @var PerProductComponentPageQuantityPriceBreak[]
	 */
	protected $priceBreaks;

	/**
	 * @var int
	 */
	protected $priceBreakCount;

	/**
	 * Constructor
	 *
	 * @param PerProductComponentPageQuantityPriceBreak[] $priceBreaks
	 * @param string $cost
	 * @param string $weight
	 * @param bool $isFixedQuantityRanges
	 * @param bool $inheritParentQuantity
	 * @param CurrencyInterface $currency
	 * @param TaxRateInterface|null $grossTaxRate
	 */
	public function __construct(array $priceBreaks, $cost, $weight, $isFixedQuantityRanges, $inheritParentQuantity, CurrencyInterface $currency, TaxRateInterface $grossTaxRate = null)
	{
		$this->priceBreaks = $priceBreaks;
		$this->priceBreakCount = count($this->priceBreaks);
		parent::__construct($cost, $weight, $isFixedQuantityRanges, $inheritParentQuantity, $currency, $grossTaxRate);
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
		if (null === $priceBreakQuantity) {
			$priceBreakQuantity = $componentQuantity;
		}

		// Select a price break with a quantity in range
		$selectedPriceBreak = null;
		$priceBreakCount = count($this->priceBreaks);

		for ($i = 0; $i < $priceBreakCount; $i++) {
			$priceBreak = $this->priceBreaks[$i];
			$nextPriceBreak = isset($this->priceBreaks[$i + 1]) ? $this->priceBreaks[$i + 1] : null;

			// If the product quantity is out of range, skip the price break
			if ($productQuantity < $priceBreak->getStartQuantity() || $productQuantity > $priceBreak->getEndQuantity()) {
				continue;
			}

			// Make component quantity updates if required
			if ($canUpdateQuantities) {
				if ($this->isFixedQuantityRanges()) {
					if ($priceBreakQuantity >= $priceBreak->getStartComponentQuantity() && $priceBreakQuantity < $priceBreak->getEndComponentQuantity()) {
						$priceBreakQuantity = $priceBreak->getEndComponentQuantity();
					}
					else if ($priceBreak->getStartComponentQuantity() > $priceBreakQuantity){
						// Snap to the lowest value on the dropdown if the component quantity pricing starts above the default quantity
						$priceBreakQuantity = $priceBreak->getStartComponentQuantity();
					}
				} else {
					if ($priceBreakQuantity < $priceBreak->getStartComponentQuantity()) {
						$priceBreakQuantity = $priceBreak->getStartComponentQuantity();
					} elseif ((($nextPriceBreak && ($productQuantity < $nextPriceBreak->getStartQuantity() || $productQuantity > $nextPriceBreak->getEndQuantity())) || (null === $nextPriceBreak)) &&
						$priceBreakQuantity > $priceBreak->getEndComponentQuantity()) {
						$priceBreakQuantity = $priceBreak->getEndComponentQuantity();
					}
				}
			}

			if ($priceBreakQuantity >= $priceBreak->getStartComponentQuantity() && $priceBreakQuantity <= $priceBreak->getEndComponentQuantity() &&
				(0 === $pageCount || ($pageCount >= $priceBreak->getStartPageCount() && $pageCount <= $priceBreak->getEndPageCount()))
			) {
				// If page count is 0, default to start count
				if (0 === $pageCount) {
					$pageCount = $priceBreak->getStartPageCount();
				}

				$selectedPriceBreak = $priceBreak;
				break;
			}
		}

		// Update the original quantity if the price break quantity has been updated
		if ($canUpdateQuantities) {
			$componentQuantity = $priceBreakQuantity;
		}

		// If no price break was found, abort by throwing an exception
		if (null === $selectedPriceBreak) {
			throw new PriceBreakNotFoundException();
		}

		// Calculate the product price
		$unitPrice = $selectedPriceBreak->getUnit();
		$fullPrice = bcmul($unitPrice, $pageCount, FinancialPrecision::PLACES);

		if ($canApplyBase) {
			$fullPrice = bcadd($selectedPriceBreak->getBase(), $fullPrice, FinancialPrecision::PLACES);
		}

		if ($canApplyLineSubtract) {
			$fullPrice = bcsub($fullPrice, $selectedPriceBreak->getLineSubtract(), FinancialPrecision::PLACES);
		}

		$fullPrice = bcmul(bcmul($productQuantity, $componentQuantity, FinancialPrecision::PLACES), $fullPrice, FinancialPrecision::PLACES);

		// Ensure the price is not negative
		if (bccomp($fullPrice, 0, FinancialPrecision::PLACES) === -1) {
			$fullPrice = 0;
		}else {
			$unitPrice = BCMath::round(bcdiv($fullPrice, $productQuantity, FinancialPrecision::PLACES), $this->currency->getDecimalPlaces());
		}

		// Convert to the clients currency and round to their currency places
		$fullPrice = $this->currency->exchange($fullPrice);
		$cost = bcmul($productQuantity, bcmul($componentQuantity, bcmul($pageCount, $this->cost, FinancialPrecision::COST_PLACES), FinancialPrecision::COST_PLACES), FinancialPrecision::COST_PLACES);
		$weight = bcmul($productQuantity, bcmul($componentQuantity, bcmul($pageCount, $this->weight, FinancialPrecision::WEIGHT_PLACES), FinancialPrecision::WEIGHT_PLACES), FinancialPrecision::WEIGHT_PLACES);

		// Calculate the quantity of units
		$unitQuantity = $pageCount * $productQuantity * $componentQuantity;

		return Price::createFromSellPrice($this->currency, $fullPrice, $unitPrice, $unitQuantity, $cost, $weight, $orderTaxRate, $this->grossTaxRate);
	}
}
