<?php

namespace PricingEngine\OrderLine;

use PricingEngine\Enum\FinancialPrecision;
use PricingEngine\OrderLine;
use PricingEngine\Price\Price;

/**
 * External asset collection
 *
 * Handles pricing of all external assets in to
 * a single price.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class ExternalAssetCollection
{
	/**
	 * @var OrderLine
	 */
	private $orderLine;

	/**
	 * @var mixed[]
	 */
	private $assetSession;

	/**
	 * Constructor
	 *
	 * @param OrderLine $orderLine
	 * @param mixed[] $assetSession
	 */
	public function __construct(OrderLine $orderLine, &$assetSession)
	{
		$this->orderLine = $orderLine;
		$this->assetSession = &$assetSession;
	}

	/**
	 * Calculate price
	 *
	 * Calculate the price of the external assets and return.
	 *
	 * @return Price|null
	 */
	public function calculatePrice()
	{
		$runningPrice = Price::createZeroPrice($this->orderLine->getCurrency(), $this->orderLine->getTaxRate());
		$productQuantity = $this->orderLine->getProductQuantity();
		$isReorder = $this->orderLine->isReorder();
		$places = $this->orderLine->getPlaces();

		foreach ($this->assetSession as &$asset) {
			// In a reorder the unit sell will include tax from the previous order so it must be taken from the raw asset cost
			if ($isReorder) {
				// Pricing type is handled by online or desktop for non-single print
				// products so the non-first image of a pay once image will have the
				// unit sell set to zero with the full price in the asset unit so we
				// need to use the unit sell when this is the case to avoid charging
				// multiple times for pay once images.
				if (bccomp((string) $asset['unitsell'], '0', $places) === 0) {
					$cost = $asset['assetunitcost'];
					$fullPrice = $asset['unitsell'];
				} else {
					$cost = $asset['assetunitcost'];
					$fullPrice = $asset['assetunitsell'];
				}
			} else {
				$cost = bcmul($asset['unitcost'], $productQuantity, FinancialPrecision::PLACES);
				$fullPrice = bcmul($asset['unitsell'], $productQuantity, FinancialPrecision::PLACES);
			}

			$price = Price::createFromSellPrice($this->orderLine->getCurrency(), $fullPrice, $asset['unitsell'],
				'0', $cost, '0', $this->orderLine->getTaxRate());

			$asset['totalcost'] = $price->getFullCost();
			$asset['totalsell'] = $price->getFullSell($this->orderLine->isShowPricesWithTax());
			$asset['totaltax'] = $price->getFullTax();
			$asset['totalsellnotax'] = $price->getFullNet();
			$asset['totalsellwithtax'] = $price->getFullGross();

			$runningPrice->addPrice($price);
		}

		return $runningPrice;
	}
}
