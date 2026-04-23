<?php

namespace PricingEngine\PriceBreakSet;

use PricingEngine\CurrencyInterface;
use PricingEngine\Enum\PricingModel;
use PricingEngine\PriceBreak\AbstractPriceBreak;
use PricingEngine\PriceBreakSet\Component\PerPageProductQuantityPriceBreakSet;
use PricingEngine\PriceBreakSet\Component\PerProductComponentPageQuantityPriceBreakSet;
use PricingEngine\PriceBreakSet\Component\PerProductComponentQuantityPriceBreakSet;
use PricingEngine\PriceBreakSet\Exception\UnsupportedPricingModelException;
use PricingEngine\PriceBreak\Component\PerProductComponentPageQuantityPriceBreak;
use PricingEngine\PriceBreak\Component\PerProductComponentQuantityPriceBreak;
use PricingEngine\PriceBreak\Component\PerPageProductQuantityPriceBreak;
use PricingEngine\PriceBreak\PerProductQuantityPriceBreak;
use PricingEngine\Tax\TaxRate;

/**
 * Price break set factory
 *
 * Factory for the construction of price break sets,
 * using a price break set database record previously
 * obtained from the database.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class PriceBreakSetFactory
{
	/**
	 * Select a price record from the database for a component,
	 * construct the correct pricing model price class, and
	 * return the instance
	 *
	 * Factory method to use to build to create the correct price break
	 * set using a raw database record directly obtained from the
	 * database.
	 *
	 * @param string[] $priceBreakRecordSet
	 * @param CurrencyInterface $currency
	 * @return PriceBreakSetInterface
	 * @throws UnsupportedPricingModelException
	 */
	public static function factory($priceBreakRecordSet, CurrencyInterface $currency)
	{
		$priceBreaks = self::parsePriceBreaks($priceBreakRecordSet['pricingmodel'], $priceBreakRecordSet['pricedata']);
		$isFixedQuantityRanges = $priceBreakRecordSet['quantityisdropdown'];
		$inheritParentQuantity = @$priceBreakRecordSet['inheritparentqty'];
		$cost = isset($priceBreakRecordSet['unitcost']) ? $priceBreakRecordSet['unitcost'] : '0';
		$weight = isset($priceBreakRecordSet['unitweight']) ? $priceBreakRecordSet['unitweight'] : '0';

		$tax = null;
		if ('' != $priceBreakRecordSet['taxcode']) {
			$tax = new TaxRate($priceBreakRecordSet['taxcode'], $priceBreakRecordSet['taxrate']);
		}

		switch ($priceBreakRecordSet['pricingmodel']) {
			case PricingModel::PER_QUANTITY:
				return new PerProductQuantityPriceBreakSet($priceBreaks, $cost, $weight,
					$isFixedQuantityRanges, $inheritParentQuantity, $currency, $tax);

			case PricingModel::PER_SIDE_QUANTITY:
				return new PerPageProductQuantityPriceBreakSet($priceBreaks, $cost, $weight,
					$inheritParentQuantity, $currency, $tax);

			case PricingModel::PER_PRODUCT_COMPONENT_QUANTITY:
				return new PerProductComponentQuantityPriceBreakSet($priceBreaks, $cost, $weight,
					$isFixedQuantityRanges, $inheritParentQuantity, $currency, $tax);

			case PricingModel::PER_SIDE_PER_PRODUCT_PER_COMPONENT_QUANTITY:
				return new PerProductComponentPageQuantityPriceBreakSet($priceBreaks, $cost, $weight,
					$isFixedQuantityRanges, $inheritParentQuantity, $currency, $tax);
		}

		throw new UnsupportedPricingModelException();
	}

	/**
	 * Parse the price breaks that make up the price break set
	 *
	 * Parses an encoded string of price break recorded in to a list
	 * of price break instances.
	 *
	 * @param string $pricingModel
	 * @param string $encodedPriceBreaks
	 * @return AbstractPriceBreak[]
	 */
	private static function parsePriceBreaks($pricingModel, $encodedPriceBreaks)
	{
		$prices = [];
		$priceBreaks = explode(' ', $encodedPriceBreaks);

		foreach ($priceBreaks as $priceBreak) {
			if ('' != $priceBreak) {
				$priceBreakParts = explode('*', $priceBreak);

				switch ($pricingModel) {
					case PricingModel::PER_QUANTITY:
						$prices[] = new PerProductQuantityPriceBreak(
							$priceBreakParts[0],
							$priceBreakParts[1],
							$priceBreakParts[2],
							$priceBreakParts[3],
							$priceBreakParts[4]
						);

						break;

					case PricingModel::PER_SIDE_QUANTITY:
						$prices[] = new PerPageProductQuantityPriceBreak(
							$priceBreakParts[0],
							$priceBreakParts[1],
							$priceBreakParts[2],
							$priceBreakParts[3],
							$priceBreakParts[4],
							$priceBreakParts[5],
							$priceBreakParts[6]
						);

						break;

					case PricingModel::PER_PRODUCT_COMPONENT_QUANTITY:
						$prices[] = new PerProductComponentQuantityPriceBreak(
							$priceBreakParts[0],
							$priceBreakParts[1],
							$priceBreakParts[2],
							$priceBreakParts[3],
							$priceBreakParts[4],
							$priceBreakParts[5],
							$priceBreakParts[6]
						);

						break;

					case PricingModel::PER_SIDE_PER_PRODUCT_PER_COMPONENT_QUANTITY:
						$prices[] = new PerProductComponentPageQuantityPriceBreak(
							$priceBreakParts[0],
							$priceBreakParts[1],
							$priceBreakParts[2],
							$priceBreakParts[3],
							$priceBreakParts[4],
							$priceBreakParts[5],
							$priceBreakParts[6],
							$priceBreakParts[7],
							$priceBreakParts[8]
						);

						break;
				}
			}
		}

		return $prices;
	}
}
