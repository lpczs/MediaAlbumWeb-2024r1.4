<?php

namespace PricingEngine\OrderLine;

use PricingEngine\Enum\PriceBreakTransformationStage;
use PricingEngine\OrderLine;
use PricingEngine\Price\Price;
use PricingEngine\PriceBreakSet\Exception\PriceBreakNotFoundException;
use PricingEngine\PriceBreakSet\PriceBreakSetInterface;


/**
 * AI Component
 *
 * AI component representation,
 * handling AI Component pricing.
 *
 * @author James Moore <James.Moore@taopix.com>
 * @copyright Taopix Limited
 */
Class AIComponent
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
	 * @var Mixed[]
	 */
	private $AISession;

	/**
	 * Constructor
	 *
	 * @param OrderLine $orderLine
	 * @param string $componentCode
	 * @param PriceBreakSetInterface $priceBreakSet
	 * @param mixed[] $AISession
	 */
	public function __construct(OrderLine $orderLine, $componentCode, PriceBreakSetInterface $priceBreakSet, &$AISession)
	{
		$this->orderLine = $orderLine;
		$this->componentCode = $componentCode;
		$this->priceBreakSet = $priceBreakSet;
		$this->AISession = &$AISession;
	}

	/**
	 * Calculate price
	 *
	 * Calculate the price of the AI Component and return.
	 *
	 * @return Price|null
	 */
	public function calculatePrice()
	{
		$runningPrice = Price::createZeroPrice($this->orderLine->getCurrency(), $this->orderLine->getTaxRate());
		$lineTaxRate = $this->orderLine->getTaxRate();
		$productQuantity = $this->orderLine->getProductQuantity();
		$pageCount = $this->orderLine->getPageCount();
		$AIComponent = &$this->AISession;

		if ($this->componentCode === $AIComponent['componentcode']) {
			$componentQuantity = $AIComponent['componentqty'];
			try {
				$price = $this->priceBreakSet->createPrice($lineTaxRate, $this->orderLine->isShowPricesWithTax(),
					$productQuantity, $pageCount, $componentQuantity, $componentQuantity, PriceBreakTransformationStage::POST_TRANSFORM, false);

				$AIComponent['unitsell'] = $price->getUnit();
				$AIComponent['totalcost'] = $price->getFullCost();
				$AIComponent['totalsell'] = $price->getFullSell($this->orderLine->isShowPricesWithTax());
				$AIComponent['totaltax'] = $price->getFullTax();
				$AIComponent['totalsellnotax'] = $price->getFullNet();
				$AIComponent['totalsellwithtax'] = $price->getFullGross();
				$AIComponent['totalweight'] = $price->getFullWeight();
				$AIComponent['subtotal'] = $price->getFullSell($this->orderLine->isShowPricesWithTax());
				$AIComponent['used'] = true;

				$runningPrice->addPrice($price);
			} catch(PriceBreakNotFoundException $ex) {
				// the product is out of range for the price
				// this will probably be due to misconfiguration
				// as the AI component does not represent a physical process or component we do not want to prevent the end-user from ordering
				// therefore we want to set the AI componant to not used so that it is not charged but is stored for future re-orders
				$this->AISession['used'] = false;
				//we want to store that it has been priced but is out of range so that the component can be readded if the end user puts it back in range
				$this->AISession['outofrange'] = true;
			}
		}

		return $runningPrice;
	}
}

?>