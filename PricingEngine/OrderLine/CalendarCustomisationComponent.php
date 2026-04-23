<?php

namespace PricingEngine\OrderLine;

use PricingEngine\OrderLine;
use PricingEngine\Price\Price;
use PricingEngine\PriceBreakSet\Exception\PriceBreakNotFoundException;
use PricingEngine\PriceBreakSet\PriceBreakSetInterface;

/**
 * Calendar customisation
 *
 * Calendar customisation component representation,
 * handling calendar customisation pricing.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class CalendarCustomisationComponent
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
	 * @var PriceBreakSetInterface
	 */
	private $priceBreakSet;

	/**
	 * @var mixed[]
	 */
	private $calendarSession;

	/**
	 * Constructor
	 *
	 * @param OrderLine $orderLine
	 * @param string $componentCode
	 * @param PriceBreakSetInterface $priceBreakSet
	 * @param mixed[] $calendarSession
	 */
	public function __construct(OrderLine $orderLine, $componentCode, PriceBreakSetInterface $priceBreakSet, &$calendarSession)
	{
		$this->orderLine = $orderLine;
		$this->componentCode = $componentCode;
		$this->priceBreakSet = $priceBreakSet;
		$this->calendarSession = &$calendarSession;
	}

	/**
	 * Calculate price
	 *
	 * Calculate the price of the calendar customisation and return.
	 *
	 * @return Price|null
	 */
	public function calculatePrice()
	{
		$runningPrice = Price::createZeroPrice($this->orderLine->getCurrency(), $this->orderLine->getTaxRate());
		$lineTaxRate = $this->orderLine->getTaxRate();
		$productQuantity = $this->orderLine->getProductQuantity();
		$pageCount = $this->orderLine->getPageCount();

		foreach ($this->calendarSession as &$calendarCustomisation) {
			if ($this->componentCode !== $calendarCustomisation['componentcode']) {
				continue;
			}

			$componentQuantity = $calendarCustomisation['componentqty'];
			try {
				$price = $this->priceBreakSet->createPrice($lineTaxRate, $this->orderLine->isShowPricesWithTax(),
					$productQuantity, $pageCount, $componentQuantity, $componentQuantity);

				$calendarCustomisation['unitsell'] = $price->getUnit();
				$calendarCustomisation['totalcost'] = $price->getFullCost();
				$calendarCustomisation['totalsell'] = $price->getFullSell($this->orderLine->isShowPricesWithTax());
				$calendarCustomisation['totaltax'] = $price->getFullTax();
				$calendarCustomisation['totalsellnotax'] = $price->getFullNet();
				$calendarCustomisation['totalsellwithtax'] = $price->getFullGross();
				$calendarCustomisation['totalweight'] = $price->getFullWeight();

				$runningPrice->addPrice($price);
			} catch(PriceBreakNotFoundException $ex) {
				// Do nothing
			}
		}

		return $runningPrice;
	}
}
