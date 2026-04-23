<?php

namespace PricingEngine\PriceBreakSet\Component;

use PricingEngine\BCMath;
use PricingEngine\CurrencyInterface;
use PricingEngine\Enum\FinancialPrecision;
use PricingEngine\Enum\PriceBreakTransformationStage;
use PricingEngine\Price\NetGrossConversion;
use PricingEngine\Price\Price;
use PricingEngine\PriceBreak\Component\PerProductComponentQuantityPriceBreak;
use PricingEngine\PriceBreakSet\AbstractPriceBreakSet;
use PricingEngine\PriceBreakSet\Exception\PriceBreakNotFoundException;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Price for Per Product, Per Component,
 * Quantity pricing model
 *
 * This pricing model takes the product quantity, and
 * the page count and calculates the price. Used by
 * components only.
 *
 * The equation is:
 *   price = base + (unit * componentQuantity) - lineSubtract
 *   price = productQuantity * price
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class PerProductComponentQuantityPriceBreakSet extends AbstractPriceBreakSet
{
	/**
	 * @var PerProductComponentQuantityPriceBreak[]
	 */
	protected $priceBreaks;

	/**
	 * @var int
	 */
	protected $priceBreakCount;

	/**
	 * Constructor
	 *
	 * @param PerProductComponentQuantityPriceBreak[] $priceBreaks
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

		// Select price break
		list($startQuantity, $priceBreakQuantity, $selectedPriceBreak) = $this->selectPriceBreak($productQuantity, $priceBreakQuantity, $canUpdateQuantities);

		// Update the original quantity if the price break quantity has been updated
		if ($canUpdateQuantities && $startQuantity !== $priceBreakQuantity) {
			$componentQuantity = $priceBreakQuantity;
		}

		// If no price break was found, abort by throwing an exception
		if (null === $selectedPriceBreak) {
			throw new PriceBreakNotFoundException();
		}

		// Calculate the product price
		$places = $this->currency->getDecimalPlaces();
		$unitPrice = $selectedPriceBreak->getUnit();
		$basePrice = $selectedPriceBreak->getBase();
		$lineSubtract = $selectedPriceBreak->getLineSubtract();

		// Ideally these checks should be done as part of a single set of business logic
		$scale = PriceBreakTransformationStage::PRE_TRANSFORM === $transformStage ? $places : FinancialPrecision::PLACES;
		$grossTaxRate = $this->grossTaxRate;

		// Perform unit price pre transformation
		if (PriceBreakTransformationStage::PRE_TRANSFORM === $transformStage) {
			list ($basePrice, $unitPrice, $lineSubtract, $grossTaxRate) = $this->preTransform(
				$basePrice,
				$unitPrice,
				$lineSubtract,
				$orderTaxRate,
				$isShowPricesWithTax
			);
		}

		// Apply unit price part of the price break
		$fullPrice = bcmul($unitPrice, $componentQuantity, $scale);

		// If required, apply the base price part of the price break to the calculation
		if ($canApplyBase) {
			$fullPrice = bcadd($basePrice, $fullPrice, $scale);
		}

		// If required, apply the line subtract part of the price break to the calculation
		if ($canApplyLineSubtract) {
			$fullPrice = bcsub($fullPrice, $lineSubtract, $scale);
		}

		if (bccomp($fullPrice, 0, $scale) === -1) {
			$fullPrice = 0;
		}

		$fullPrice = bcmul($productQuantity, $fullPrice, $scale);

		// Convert to the clients currency and round to their currency places
		if (PriceBreakTransformationStage::POST_TRANSFORM === $transformStage) {
			// Ensure the price is not negative
			$unitPrice = BCMath::round(bcdiv($fullPrice, $productQuantity, $scale), $this->currency->getDecimalPlaces());
			$fullPrice = $this->currency->exchange($fullPrice);
		}

		$cost = bcmul($productQuantity, bcmul($componentQuantity, $this->cost, FinancialPrecision::COST_PLACES), FinancialPrecision::COST_PLACES);
		$weight = bcmul($productQuantity, bcmul($componentQuantity, $this->weight, FinancialPrecision::WEIGHT_PLACES), FinancialPrecision::WEIGHT_PLACES);

		// Calculate the quantity of units
		// Note: this has been fixed for photo prints for now, which is the only product that uses the unit quantity
		// value in the Price class. For other products this may well be wrong.
		$unitQuantity = $componentQuantity;

		return Price::createFromSellPrice($this->currency, $fullPrice, $unitPrice, $unitQuantity, $cost, $weight, $orderTaxRate, $grossTaxRate);
	}

	/**
	 * @param $productQuantity
	 * @param $priceBreakQuantity
	 * @param $canUpdateQuantities
	 * @return mixed[]
	 */
	protected function selectPriceBreak($productQuantity, $priceBreakQuantity, $canUpdateQuantities)
	{
		// Select a price break with a quantity in range
		$startQuantity = $priceBreakQuantity;
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
				} else {
					if ($priceBreakQuantity < $priceBreak->getStartComponentQuantity()) {
						$priceBreakQuantity = $priceBreak->getStartComponentQuantity();
					} elseif ((($nextPriceBreak && ($productQuantity < $nextPriceBreak->getStartQuantity() || $productQuantity > $nextPriceBreak->getEndQuantity())) || (null === $nextPriceBreak)) &&
						$priceBreakQuantity > $priceBreak->getEndComponentQuantity()) {
						$priceBreakQuantity = $priceBreak->getEndComponentQuantity();
					}
				}
			}

			if ($productQuantity >= $priceBreak->getStartQuantity() && $productQuantity <= $priceBreak->getEndQuantity() &&
				$priceBreakQuantity >= $priceBreak->getStartComponentQuantity() && $priceBreakQuantity <= $priceBreak->getEndComponentQuantity()) {
				$selectedPriceBreak = $priceBreak;
				break;
			}
		}

		return [$startQuantity, $priceBreakQuantity, $selectedPriceBreak];
	}

	/**
	 * Perform a pre pricing model transformation of an individual price break value
	 *
	 * @param string $basePrice
	 * @param string $unitPrice
	 * @param string $lineSubtract
	 * @param TaxRateInterface $orderTaxRate
	 * @param bool $isShowPricesWithTax
	 * @return mixed[]
	 */
	protected function preTransform($basePrice, $unitPrice, $lineSubtract, TaxRateInterface $orderTaxRate, $isShowPricesWithTax)
	{
		$grossTaxRate = $this->grossTaxRate;

		// 1. Currency conversion
		$basePrice = $this->currency->exchange($basePrice, FinancialPrecision::PLACES);
		$unitPrice = $this->currency->exchange($unitPrice, FinancialPrecision::PLACES);
		$lineSubtract = $this->currency->exchange($lineSubtract, FinancialPrecision::PLACES);

		// 2. Tax conversion
		//
		// It's not a case of having both the net and gross values output from NetGrossConversion or using the Price class.
		// The transformed money value is based on the value stored in the price break, which is either recorded net or gross.
		// This value needs to be converted to net or gross in line with what is needed for the license keys show prices with tax flag
		// as part of this transformation step and prior to any pricing model calculations.
		//
		// This means we could be downgrading a gross price to net every time if pre transformation is enabled
		// and show prices with tax is disabled, which may not be what the licensee expects and may result in a gross
		// price that doesn't reflect the gross price entered, however this is a shortcoming of displaying gross price
		// price break values with net pricing on the front end.
		if ($isShowPricesWithTax) {
			if (null === $this->grossTaxRate) {
				// Convert a net price to gross
				$basePrice = NetGrossConversion::convertNetToGross($basePrice, $orderTaxRate->getRate(), FinancialPrecision::PLACES);
				$unitPrice = NetGrossConversion::convertNetToGross($unitPrice, $orderTaxRate->getRate(), FinancialPrecision::PLACES);
				$lineSubtract = NetGrossConversion::convertNetToGross($lineSubtract, $orderTaxRate->getRate(), FinancialPrecision::PLACES);

				$grossTaxRate = $orderTaxRate;
			} elseif ($orderTaxRate->getRate() !== $this->grossTaxRate->getRate()) {
				// Fix the tax on a gross price for the order
				$basePrice = NetGrossConversion::convertGrossToNet($basePrice, $this->grossTaxRate->getRate(), FinancialPrecision::PLACES);
				$basePrice = NetGrossConversion::convertNetToGross($basePrice, $orderTaxRate->getRate(), FinancialPrecision::PLACES);

				$unitPrice = NetGrossConversion::convertGrossToNet($unitPrice, $this->grossTaxRate->getRate(), FinancialPrecision::PLACES);
				$unitPrice = NetGrossConversion::convertNetToGross($unitPrice, $orderTaxRate->getRate(), FinancialPrecision::PLACES);

				$lineSubtract = NetGrossConversion::convertGrossToNet($lineSubtract, $this->grossTaxRate->getRate(), FinancialPrecision::PLACES);
				$lineSubtract = NetGrossConversion::convertNetToGross($lineSubtract, $orderTaxRate->getRate(), FinancialPrecision::PLACES);

				$grossTaxRate = $orderTaxRate;
			}
		} elseif (null !== $this->grossTaxRate) {
			// Convert a gross price to net
			$basePrice = NetGrossConversion::convertGrossToNet($basePrice, $this->grossTaxRate->getRate(), FinancialPrecision::PLACES);
			$unitPrice = NetGrossConversion::convertGrossToNet($unitPrice, $this->grossTaxRate->getRate(), FinancialPrecision::PLACES);
			$lineSubtract = NetGrossConversion::convertGrossToNet($lineSubtract, $this->grossTaxRate->getRate(), FinancialPrecision::PLACES);

			$grossTaxRate = null;
		}

		// 3. Round
		return [
			BCMath::round($basePrice, $this->currency->getDecimalPlaces()),
			BCMath::round($unitPrice, $this->currency->getDecimalPlaces()),
			BCMath::round($lineSubtract, $this->currency->getDecimalPlaces()),
			$grossTaxRate
		];
	}
}
