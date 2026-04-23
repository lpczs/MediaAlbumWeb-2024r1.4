<?php

namespace PricingEngine\PriceBreakSet\Component;

use PricingEngine\BCMath;
use PricingEngine\CurrencyInterface;
use PricingEngine\Enum\FinancialPrecision;
use PricingEngine\Price\Price;
use PricingEngine\PriceBreak\Component\PerPageProductQuantityPriceBreak;
use PricingEngine\PriceBreakSet\AbstractPriceBreakSet;
use PricingEngine\PriceBreakSet\Exception\PriceBreakNotFoundException;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Price model for Per Product, Per Page Quantity
 * pricing model
 *
 * This pricing model takes the product quantity and
 * the page count and calculates the price. Used by
 * components only.
 *
 * The equation is:
 *   price = base + (unit * pageQuantity) - lineSubtract
 *   price = productQuantity * price
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class PerPageProductQuantityPriceBreakSet extends AbstractPriceBreakSet
{
	/**
	 * @var PerPageProductQuantityPriceBreak[]
	 */
	protected $priceBreaks;

	/**
	 * Constructor
	 *
	 * @param PerPageProductQuantityPriceBreak[] $priceBreaks
	 * @param string $cost
	 * @param string $weight
	 * @param bool $inheritParentQuantity
	 * @param CurrencyInterface $currency
	 * @param TaxRateInterface|null $grossTaxRate
	 */
	public function __construct(array $priceBreaks, $cost, $weight, $inheritParentQuantity, CurrencyInterface $currency, TaxRateInterface $grossTaxRate = null)
	{
		$this->priceBreaks = $priceBreaks;
		parent::__construct($cost, $weight, false, $inheritParentQuantity, $currency, $grossTaxRate);
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
		foreach ($this->priceBreaks as $priceBreak) {
			if ($productQuantity >= $priceBreak->getStartQuantity() && $productQuantity <= $priceBreak->getEndQuantity() &&
				(0 === $pageCount || ($pageCount >= $priceBreak->getStartPageCount() && $pageCount <= $priceBreak->getEndPageCount()))
			) {
				// If page count is null, default to start count
				if (0 === $pageCount) {
					$pageCount = $priceBreak->getStartPageCount();
				}

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
		$fullPrice = bcmul($unitPrice, $pageCount, FinancialPrecision::PLACES);

		if ($canApplyBase) {
			$fullPrice = bcadd($selectedPriceBreak->getBase(), $fullPrice, FinancialPrecision::PLACES);
		}

		if ($canApplyLineSubtract) {
			$fullPrice = bcsub($fullPrice, $selectedPriceBreak->getLineSubtract(), FinancialPrecision::PLACES);
		}

		$fullPrice = bcmul($productQuantity, $fullPrice, FinancialPrecision::PLACES);

		// Ensure the price is not negative and recalculate the unit price
		if (bccomp($fullPrice, 0, FinancialPrecision::PLACES) === -1) {
			$fullPrice = '0';
		} else {
			$unitPrice = BCMath::round(bcdiv($fullPrice, $productQuantity, FinancialPrecision::PLACES), $this->currency->getDecimalPlaces());
		}

		// Convert to the clients currency and round to their currency places
		$fullPrice = $this->currency->exchange($fullPrice);
		$cost = bcmul(bcmul($productQuantity, $pageCount, FinancialPrecision::COST_PLACES), $this->cost, FinancialPrecision::COST_PLACES);
		$weight = bcmul(bcmul($productQuantity, $pageCount, FinancialPrecision::WEIGHT_PLACES), $this->weight, FinancialPrecision::WEIGHT_PLACES);

		// Calculate the quantity of units
		$unitQuantity = $pageCount * $productQuantity;

		return Price::createFromSellPrice($this->currency, $fullPrice, $unitPrice, $unitQuantity, $cost, $weight, $orderTaxRate, $this->grossTaxRate);
	}
}
