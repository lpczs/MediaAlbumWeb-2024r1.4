<?php

namespace PricingEngine\OrderLine;

use PricingEngine\Enum\FinancialPrecision;
use PricingEngine\Enum\PriceBreakTransformationStage;
use PricingEngine\OrderLine;
use PricingEngine\PriceBreakSet\Exception\PriceBreakNotFoundException;
use PricingEngine\PriceBreakSet\PriceBreakSetInterface;
use PricingEngine\Price\Price;

/**
 * Photo prints size component
 *
 * Handles pricing of photo prints on a
 * per size basis.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class PerPicturePhotoPrintsComponent
{
	/**
	 * @var OrderLine
	 */
	private $orderLine;

	/**
	 * @var string
	 */
	private $componentCode;

	/**
	 * @var string
	 */
	private $subComponentCode;

	/**
	 * @var PriceBreakSetInterface
	 */
	private $componentPriceBreakSet;

	/**
	 * @var PriceBreakSetInterface
	 */
	private $subComponentPriceBreakSet;

	/**
	 * @var mixed[]
	 */
	private $pictureSession;

	/**
	 * Constructor
	 *
	 * @param OrderLine $orderLine
	 * @param string $componentCode
	 * @param string $subComponentCode
	 * @param PriceBreakSetInterface $componentPriceBreakSet
	 * @param PriceBreakSetInterface $subComponentPriceBreakSet
	 * @param mixed[] $pictureSession
	 */
	public function __construct(OrderLine $orderLine, $componentCode, $subComponentCode,
		PriceBreakSetInterface $componentPriceBreakSet, PriceBreakSetInterface $subComponentPriceBreakSet = null, &$pictureSession)
	{
		$this->orderLine = $orderLine;
		$this->componentCode = $componentCode;
		$this->subComponentCode = $subComponentCode;
		$this->componentPriceBreakSet = $componentPriceBreakSet;
		$this->subComponentPriceBreakSet = $subComponentPriceBreakSet;
		$this->pictureSession = &$pictureSession;
	}

	/**
	 * Calculate price
	 *
	 * Calculate the price of the picture size and return.
	 *
	 * @return Price|null
	 */
	public function calculatePrice()
	{
		$combinedPrice = Price::createZeroPrice($this->orderLine->getCurrency(), $this->orderLine->getTaxRate());
		$componentPrice = null;
		$subComponentPrice = null;

		$productQuantity = $this->orderLine->getProductQuantity();
		$pageCount = $this->orderLine->getPageCount();

		$applyBaseLineSubtract = true;

		/** @var Price[] $cachedPrices */
		$cachedPrices = [];

		foreach ($this->pictureSession['key'] as $index => $key) {
			$picture = &$this->pictureSession['data'][$key];
			$uniqueLookup = $key . TPX_PICTURES_LOOKUP_SEPERATOR . $index;

			// Skip non-matching component code
			if ($this->componentCode !== $picture['code']) {
				continue;
			}

			// Skip non matching sub component code
			if (null !== $this->subComponentCode && isset($picture['subcode'])
				&& $this->subComponentCode !== $picture['subcode']) {
				continue;
			}

			$quantity = $picture['qty'];

			// Lookup price for component
			$componentCacheKey = $this->generatePriceCacheKey(
				$this->componentCode,
				$quantity,
				$applyBaseLineSubtract
			);

			if (!isset($cachedPrices[$componentCacheKey])) {
				try {
					$cachedPrices[$componentCacheKey] = $this->componentPriceBreakSet->createPrice(
						$this->orderLine->getTaxRate(),
						$this->orderLine->isShowPricesWithTax(),
						$productQuantity,
						$pageCount,
						$quantity,
						$quantity,
						PriceBreakTransformationStage::POST_TRANSFORM,
						false
					);
				} catch (PriceBreakNotFoundException $ex) {
					// Do nothing
				}
			}

			if (isset($cachedPrices[$componentCacheKey])) {
				$componentPrice = $cachedPrices[$componentCacheKey];
			}

			// Lookup price for subcomponent
			if ($this->subComponentCode) {
				$subComponentCacheKey = $this->generatePriceCacheKey(
					$this->subComponentCode,
					$quantity,
					$applyBaseLineSubtract
				);

				if (!isset($cachedPrices[$subComponentCacheKey])) {
					try {
						$cachedPrices[$subComponentCacheKey] = $this->subComponentPriceBreakSet->createPrice(
							$this->orderLine->getTaxRate(),
							$this->orderLine->isShowPricesWithTax(),
							$productQuantity,
							$pageCount,
							$quantity,
							$quantity,
							PriceBreakTransformationStage::POST_TRANSFORM,
							false
						);
					} catch (PriceBreakNotFoundException $ex) {
						// Do nothing
					}
				}

				if (isset($cachedPrices[$subComponentCacheKey])) {
					$subComponentPrice = $cachedPrices[$subComponentCacheKey];
				}
			}

			// Normal weight logic does not apply to single prints
			$componentPrice->setFullWeight(0);

			if ($subComponentPrice) {
				$subComponentPrice->setFullWeight(0);
			}

			// Combine $componentPrice, $subComponentPrice
			$combinedPrice->addPrice($componentPrice);
			$combinedPrice->addPrice($subComponentPrice);

			if ($componentPrice && (null === $this->subComponentCode || (null !== $this->subComponentCode && $subComponentPrice))) {
				// Mark picture as priced
				$picture['componenthasprice'] = 1;

				$componentSellUnit = $componentPrice->getUnit();
				$componentSellNet = $componentPrice->getFullNet();
				$componentSellGross = $componentPrice->getFullGross();
				$componentSell = $componentPrice->getFullSell($this->orderLine->isShowPricesWithTax());

				// Calculate external asset pricing
				$places = $this->orderLine->getPlaces();
				$assetPrice = $this->calculateAssetPrice($uniqueLookup, $productQuantity, $quantity, $picture);
				if (null !== $assetPrice) {
					$componentSellUnit = bcadd($componentSellUnit, $assetPrice->getUnit(), $places);
					$componentSellNet = bcadd($componentSellNet, $assetPrice->getFullNet(), $places);
					$componentSellGross = bcadd($componentSellGross, $assetPrice->getFullGross(), $places);
					$componentSell = bcadd($componentSell, ($this->orderLine->isShowPricesWithTax() ? $assetPrice->getFullGross() : $assetPrice->getFullNet()), $places);
				}

				// Update the session fields
				$printData = &$this->pictureSession['printdata'][$uniqueLookup];

				$printData['us'] = $componentSellUnit; // with asset
				$printData['tc'] = $componentPrice->getFullCost();
				$printData['ts'] = $componentSell; // with asset
				$printData['tt'] = $componentPrice->getFullTax();
				$printData['tsnt'] = $componentSellNet; // with asset
				$printData['tswt'] = $componentSellGross; // with asset
				$printData['tw'] = $componentPrice->getFullWeight();
				$printData['subtotal'] = $componentSell; // with asset

				// Sub component
				if ($this->subComponentCode) {
					$printData['subus'] = $subComponentPrice->getUnit();
					$printData['subtc'] = $subComponentPrice->getFullCost();
					$printData['subts'] = $subComponentPrice->getFullSell($this->orderLine->isShowPricesWithTax());
					$printData['subtt'] = $subComponentPrice->getFullTax();
					$printData['subtsnt'] = $subComponentPrice->getFullNet();
					$printData['subtswt'] = $subComponentPrice->getFullGross();
					$printData['subtw'] = $subComponentPrice->getFullWeight();

					$printData['subtotal'] = $subComponentPrice->getFullSell($this->orderLine->isShowPricesWithTax());
				}
			} else {
				$picture['componenthasprice'] = 0;
			}
		}

		return $combinedPrice;
	}

	/**
	 * Calculate asset price
	 *
	 * Calculate the price of an external asset used.
	 *
	 * @param string $uniqueLookup
	 * @param int $productQuantity
	 * @param int $componentQuantity
	 * @param mixed[] $picture
	 * @return Price|null
	 */
	private function calculateAssetPrice($uniqueLookup, $productQuantity, $componentQuantity, &$picture)
	{
		$asset = array(
			'aid' => '', // asset id
			'asc' => '', // asset service code
			'asn' => '', // asset service name
			'apt' => 0, // asset price type
			'ac' => 0.00, // asset cost
			'as' => 0.00 // asset sell
		);

		if (array_key_exists($uniqueLookup, $this->pictureSession['asset'])) {
			$asset = $this->pictureSession['asset'][$uniqueLookup];
		}

		// Copy data in to component
		$picture['asc'] = $asset['asc'];
		$picture['asn'] = $asset['asn'];
		$picture['apt'] = $asset['apt'];
		$picture['ac'] = $asset['ac'];
		$picture['as'] = $asset['as'];

		switch ($picture['apt']) {
			case TPX_EXTERNALASSETPRICETYPE_ONCE:
				$assetPrice = bcmul($productQuantity, $picture['as'], FinancialPrecision::PLACES);
				$assetCost = bcmul(bcmul($productQuantity, $componentQuantity, 0), $picture['ac'], FinancialPrecision::PLACES);

				return Price::createFromSellPrice($this->orderLine->getCurrency(), $assetPrice,
					$picture['as'], '0', $assetCost, '0', $this->orderLine->getTaxRate());

			case TPX_EXTERNALASSETPRICETYPE_EACHTIME:
				$assetPrice = bcmul(bcmul($productQuantity, $componentQuantity, 0), $picture['as'], FinancialPrecision::PLACES);
				$assetCost = bcmul(bcmul($productQuantity, $componentQuantity, 0), $picture['ac'], FinancialPrecision::PLACES);

				return Price::createFromSellPrice($this->orderLine->getCurrency(), $assetPrice,
					$picture['as'], '0', $assetCost, '0', $this->orderLine->getTaxRate());
		}

		return null;
	}

	/**
	 * Generate a price cache key
	 *
	 * Generates a price cache key for storing the price lookup
	 * to improve performance.
	 *
	 * @param string $componentOrSubComponentCode
	 * @param int $quantity
	 * @param bool $applyBaseLineSubtract
	 * @return string
	 */
	private function generatePriceCacheKey($componentOrSubComponentCode, $quantity, $applyBaseLineSubtract)
	{
		return sprintf('%s.%s.%s.%s.%d.%d.%d.%d',
			$this->orderLine->getCompanyCode(),
			$this->orderLine->getLicenseKeyCode(),
			$this->orderLine->getProductCode(),
			$componentOrSubComponentCode,
			$this->orderLine->getProductQuantity(),
			$this->orderLine->getPageCount(),
			$quantity,
			$applyBaseLineSubtract ? 1 : 0
		);
	}
}
