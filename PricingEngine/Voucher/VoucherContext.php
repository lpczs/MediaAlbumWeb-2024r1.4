<?php

namespace PricingEngine\Voucher;

use PricingEngine\Enum\Voucher\DiscountMethod;
use PricingEngine\OrderInterface;
use PricingEngine\OrderLineInterface;

/**
 * Voucher context (state) information for a specific
 * voucher attached to an order
 *
 * For each individually unique voucher added to an
 * order, the voucher context stores information about
 * the order that is unique and relevant to the voucher
 * that assists the voucher in its business logic but
 * keeps that state data outside of the voucher itself.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class VoucherContext
{
	/**
	 * @var OrderInterface
	 */
	private $order;

	/**
	 * @var VoucherInterface
	 */
	private $voucher;

	/**
	 * @var string
	 */
	private $discountSection;

	/**
	 * @var OrderLineInterface
	 */
	private $lowestLine;

	/**
	 * @var OrderLineInterface
	 */
	private $highestLine;

	/**
	 * @var string
	 */
	private $netValue;

	/**
	 * @var string
	 */
	private $grossValue;

	/**
	 * @var int
	 */
	private $maxOrderQuantity;

	/**
	 * @var int
	 */
	private $maxDiscountQuantity;

	/**
	 * Constructor
	 *
	 * @param OrderInterface $order
	 * @param VoucherInterface $voucher
	 */
	public function __construct(OrderInterface $order, VoucherInterface $voucher)
	{
		$this->order = $order;
		$this->voucher = $voucher;
	}

	/**
	 * Test if the voucher is a script voucher type
	 *
	 * Check if the voucher implementation is a script
	 * voucher type. Returns true if a script voucher
	 * type, false otherwise.
	 *
	 * @return bool
	 */
	public function isScriptVoucher()
	{
		return $this->voucher->isScriptVoucher();
	}

	/**
	 * Apply the voucher discount to the discountable
	 *
	 * Apply the discount logic of the voucher type to the discountable instance passed.
	 *
	 * @param DiscountableInterface $discountable
	 */
	public function discount(DiscountableInterface $discountable)
	{
		$this->voucher->discount($this, $discountable);
	}

	/**
	 * Get the order
	 *
	 * Get the associated order instance that this
	 * context has been created for.
	 *
	 * @return OrderInterface
	 */
	public function getOrder()
	{
		return $this->order;
	}

	/**
	 * Set discount type
	 *
	 * Set the discount type of the voucher.
	 * Used for voucher scripting.
	 *
	 * @param string $discountType
	 * @return $this
	 */
	public function setDiscountType($discountType)
	{
		$this->order->setVoucherDiscountType($discountType);
		return $this;
	}

	/**
	 * Get discount section
	 *
	 * Get the discount section that the discount should
	 * be applied to.
	 *
	 * @return string
	 */
	public function getDiscountSection()
	{
		return $this->discountSection;
	}

	/**
	 * Set discount section
	 *
	 * Set the discount section that the discount should
	 * be applied to.
	 *
	 * @param string $discountSection
	 * @return $this
	 */
	public function setDiscountSection($discountSection)
	{
		$this->discountSection = $discountSection;
		return $this;
	}

	/**
	 * Get voucher type
	 *
	 * Get the voucher type of the underlying voucher.
	 *
	 * @return string
	 */
	public function getVoucherType()
	{
		return $this->order->getVoucherType();
	}

	/**
	 * Set voucher type
	 *
	 * Set the voucher type of the underlying voucher.
	 * Used for voucher scripting.
	 *
	 * @param string $voucherType
	 * @return $this
	 */
	public function setVoucherType($voucherType)
	{
		$this->order->setVoucherType($voucherType);
		return $this;
	}

	/**
	 * Get sell price
	 *
	 * Get the sell price of the voucher, assuming
	 * it is a pre-paid voucher type.
	 * Used for voucher scripting.
	 *
	 * @return string
	 */
	public function getSellPrice()
	{
		return $this->order->getVoucherSellPrice();
	}

	/**
	 * Set sell price
	 *
	 * Set the sell price of the voucher, assuming
	 * it is a pre-paid voucher type.
	 * Used for voucher scripting.
	 *
	 * @param string $sellPrice
	 * @return $this
	 */
	public function setSellPrice($sellPrice)
	{
		$this->order->setVoucherSellPrice((string) $sellPrice);
		return $this;
	}

	/**
	 * Get agent fee
	 *
	 * Get the agent fee of the voucher, assuming
	 * it is a pre-paid voucher type.
	 * Used for voucher scripting.
	 *
	 * @return string
	 */
	public function getAgentFee()
	{
		return $this->order->getVoucherAgentFee();
	}

	/**
	 * Set agent fee
	 * Set the agent fee of the voucher, assuming
	 * it is a pre-paid voucher type.
	 * Used for voucher scripting.
	 *
	 * @param string $agentFee
	 * @return $this
	 */
	public function setAgentFee($agentFee)
	{
		$this->order->setVoucherAgentFee((string) $agentFee);
		return $this;
	}

	/**
	 * Get the net voucher value
	 *
	 * Note that this value may be different to the voucher that the context
	 * represents, since the value can be modified.
	 *
	 * @return string
	 */
	public function getNetValue()
	{
		return $this->netValue;
	}

	/**
	 * Set a new net value for the voucher
	 *
	 * If the value of the discount to apply needs to change, this method
	 * allows the value to be overwritten. This might if the voucher holds
	 * a monetary value that must be deducted as it is applied.
	 *
	 * @param string $netValue
	 * @return $this
	 */
	public function setNetValue($netValue)
	{
		$this->netValue = (string) $netValue;
		return $this;
	}

	/**
	 * Get the gross voucher value
	 *
	 * Note that this value may be different to the voucher that the context
	 * represents, since the value can be modified.
	 *
	 * @return string
	 */
	public function getGrossValue()
	{
		return $this->grossValue;
	}

	/**
	 * Set a new gross value for the voucher
	 *
	 * If the value of the discount to apply needs to change, this method
	 * allows the value to be overwritten. This might if the voucher holds
	 * a monetary value that must be deducted as it is applied.
	 *
	 * @param string $grossValue
	 * @return $this
	 */
	public function setGrossValue($grossValue)
	{
		$this->grossValue = (string) $grossValue;
		return $this;
	}

	/**
	 * Get max order quantity
	 *
	 * Get the max order quantity of the voucher.
	 *
	 * @return int
	 */
	public function getMaxOrderQuantity()
	{
		return $this->maxOrderQuantity;
	}

	/**
	 * Set max order quantity
	 *
	 * Set the max order quantity of the voucher.
	 *
	 * @param int $quantity
	 * @return $this
	 */
	public function setMaxOrderQuantity($quantity)
	{
		$this->maxOrderQuantity = (int)$quantity;
		return $this;
	}

	/**
	 * Get max discount quantity
	 *
	 * Get the max discount quantity of the order.
	 *
	 * @return int
	 */
	public function getMaxDiscountQuantity()
	{
		return $this->maxDiscountQuantity;
	}

	/**
	 * Set max discount quantity
	 *
	 * Set the max discount quantity of the order.
	 *
	 * @param int $maxDiscountQuantity
	 * @return $this
	 */
	public function setMaxDiscountQuantity($maxDiscountQuantity)
	{
		$this->maxDiscountQuantity = $maxDiscountQuantity;
		return $this;
	}

	/**
	 * Decrement max discount quantity
	 *
	 * Decrement the max discount quantity of the voucher context
	 * by the amount given by $quantity.
	 *
	 * @param int $quantity
	 * @return $this
	 */
	public function decMaxDiscountQuantity($quantity)
	{
		$this->maxDiscountQuantity -= $quantity;

		if ($this->maxDiscountQuantity < 0) {
			$this->maxDiscountQuantity = 0;
		}

		return $this;
	}

	/**
	 * Get the lowest priced order line
	 *
	 * Get the lowest priced order line of the associated order.
	 *
	 * @return OrderLineInterface
	 */
	public function getLowestPricedLine()
	{
		if (null === $this->lowestLine) {
			$lowestLinePrice = '0';
			$places = $this->order->getPlaces();

			foreach ($this->order->getOrderLines() as $line) {
				if (!$line->canApplyVoucher()) {
					continue;
				}

				$linePrice = $line->getFullNet();
				if (null === $this->lowestLine || bccomp($linePrice, $lowestLinePrice, $places) === -1) {
					$this->lowestLine = $line;
					$lowestLinePrice = $linePrice;
				}
			}
		}

		return $this->lowestLine;
	}

	/**
	 * Confirm the lowest priced line
	 *
	 * Of the matching lines passed to the context, find the lowest priced line.
	 * If the matching lines has only one entry then this will be both the
	 * highest and lowest priced line.
	 *
	 * This is only relevant when the discount method used by the voucher applies
	 * to the lowest priced line only.
	 *
	 * @param OrderLineInterface $discountable
	 * @return bool
	 */
	public function isLowestPricedLine(OrderLineInterface $discountable)
	{
		return $discountable === $this->getLowestPricedLine();
	}

	/**
	 * Get the highest priced order line
	 *
	 * Get the highest priced order line of the associated order.
	 *
	 * @return OrderLineInterface
	 */
	public function getHighestPricedLine()
	{
		if (null === $this->highestLine) {
			$highestLinePrice = '0';
			$places = $this->order->getPlaces();

			foreach ($this->order->getOrderLines() as $line) {
				if (!$line->canApplyVoucher()) {
					continue;
				}

				$linePrice = $line->getFullNet();
				if (null === $this->highestLine || bccomp($linePrice, $highestLinePrice, $places) === 1) {
					$this->highestLine = $line;
					$highestLinePrice = $linePrice;
				}
			}
		}

		return $this->highestLine;
	}

	/**
	 * Get the highest priced line
	 *
	 * Of the matching lines passed to the context, find the highest priced line.
	 * If the matching lines has only one entry then this will be both the
	 * highest and lowest priced line.
	 *
	 * This is only relevant when the discount method used by the voucher applies
	 * to the highest priced line only.
	 *
	 * @param OrderLineInterface $discountable
	 * @return bool
	 */
	public function isHighestPricedLine(OrderLineInterface $discountable)
	{
		return $discountable === $this->getHighestPricedLine();
	}

	/**
	 * Get matching order lines
	 *
	 * Get all the matching order lines for the given discount method.
	 *
	 * @param $discountMethod
	 * @return OrderLineInterface[]
	 */
	public function getMatchingLines($discountMethod)
	{
		if ($discountMethod === DiscountMethod::HIGHEST_PRICED_MATCHING_LINE) {
			return [$this->getHighestPricedLine()];
		} elseif($discountMethod === DiscountMethod::LOWEST_PRICED_MATCHING_LINE) {
			return [$this->getLowestPricedLine()];
		} else {
			$matchingLines = [];
			foreach ($this->order->getOrderLines() as $line) {
				if (!$line->canApplyVoucher()) {
					continue;
				}

				$matchingLines[] = $line;
			}

			return $matchingLines;
		}
	}

	/**
	 * Get eligible order line count
	 *
	 * Get the number of order lines that are eligible for discount
	 * by the voucher.
	 *
	 * @return int
	 */
	public function getEligibleOrderLineCount()
	{
		$count = 0;
		foreach ($this->order->getOrderLines() as $line) {
			if ($line->canApplyVoucher()) {
				$count++;
			}
		}

		return $count;
	}
}
