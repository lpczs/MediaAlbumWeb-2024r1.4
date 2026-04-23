<?php

namespace PricingEngine\Price;

use InvalidArgumentException;
use LogicException;
use PricingEngine\BCMath;
use PricingEngine\CurrencyInterface;
use PricingEngine\Enum\FinancialPrecision;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Discountable price
 *
 * Represents totals of cost, weight, net, tax and
 * gross values, and allows discounting of the net,
 * tax and gross by net or gross pricing.
 *
 * Once a discount has been applied, the full price
 * cannot be changed as the full price is marked
 * immutable.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class DiscountablePrice implements DiscountablePriceInterface
{
	/**
	 * @var int
	 */
	private $productQuantity;

	/**
	 * @var CurrencyInterface
	 */
	private $currency;

	/**
	 * @var TaxRateInterface
	 */
	private $taxRate;

	/**
	 * @var string
	 */
	private $unitNet = '0';

	/**
	 * @var string
	 */
	private $unRoundedUnitNet = '0';

	/**
	 * @var string
	 */
	private $unitGross = '0';

	/**
	 * @var string
	 */
	private $unRoundedUnitGross = '0';

	/**
	 * @var string
	 */
	private $fullNet = '0';

	/**
	 * @var string
	 */
	private $fullTax = '0';

	/**
	 * @var string
	 */
	private $fullGross = '0';

	/**
	 * @var string
	 */
	private $discountNetAmount = '0';

	/**
	 * @var string
	 */
	private $discountedNetPricingNet = '0';

	/**
	 * @var string
	 */
	private $discountedNetPricingTax = '0';

	/**
	 * @var string
	 */
	private $discountedNetPricingGross = '0';

	/**
	 * @var string
	 */
	private $discountGrossAmount = '0';

	/**
	 * @var string
	 */
	private $discountedGrossPricingNet = '0';

	/**
	 * @var string
	 */
	private $discountedGrossPricingTax = '0';

	/**
	 * @var string
	 */
	private $discountedGrossPricingGross = '0';

	/**
	 * @var string
	 */
	private $totalCost = '0';

	/**
	 * @var string
	 */
	private $totalWeight = '0';

	/**
	 * @var bool
	 */
	private $isFullPriceImmutable = false;

	/**
	 * Constructor
	 *
	 * @param int $productQuantity
	 * @param TaxRateInterface $taxRate
	 * @param CurrencyInterface $currency
	 */
	public function __construct($productQuantity, TaxRateInterface $taxRate, CurrencyInterface $currency)
	{
		if (!is_numeric($productQuantity) && $productQuantity < 1) {
			throw new LogicException('Product quantity must be greater than 0');
		}

		$this->productQuantity = $productQuantity;
		$this->taxRate = $taxRate;
		$this->currency = $currency;
	}

	/**
	 * Test if the full price has been made immutable
	 *
	 * Test if the full price has been made immutable
	 * and return the status. If the full price is
	 * immutable, the full price may no longer be
	 * modified by adding new pricing.
	 *
	 * @return bool
	 */
	public function isFullPriceImmutable()
	{
		return $this->isFullPriceImmutable;
	}

	/**
	 * Mark the full price as immutable to prevent further changes
	 *
	 * Once the full price is marked as immutable, the full price can no
	 * longer be added to. This is used in conjunction with
	 * discounting. If the full price is discounted by a relative
	 * amount (e.g. a percentage) then the price should not be changed
	 * in order to protected it from modification.
	 */
	public function markFullPriceAsImmutable()
	{
		$this->isFullPriceImmutable = true;
	}

	/**
	 * Add a single price
	 *
	 * Add a single price to the discountable instance.
	 * The new instance will be added to the total cost,
	 * weight, net, tax and gross values, and will form
	 * the price that will be used when applying any
	 * discounts to the discountable object instance.
	 *
	 * Prices added to the discountable must use a tax
	 * rate that matches the tax rate passed at the
	 * discountable's construction. This is needed so
	 * that recalculation of tax can be done after
	 * discounting.
	 *
	 * @param Price|null $price
	 * @throws LogicException
	 */
	public function addPrice(Price $price = null)
	{
		// Check that the price can still be added to
		if ($this->isFullPriceImmutable()) {
			throw new LogicException(
				'Price is now immutable. Cannot add additional pricing to the price.'
			);
		}

		if (null === $price) {
			return;
		}

		// Check for a tax rate mismatch
		$places = $this->currency->getDecimalPlaces();
		if (bccomp($this->taxRate->getRate(), $price->getTaxRate()->getRate(), $places) !== 0) {
			throw new LogicException(
				'Incoming tax rate of Price instance differs to own tax rate. Tax rates must be equal to be able to perform discounting.'
			);
		}

		// Calculate the full order line price
		$this->fullNet = bcadd($this->fullNet, $price->getFullNet(), $places);
		$this->fullTax = bcadd($this->fullTax, $price->getFullTax(), $places);
		$this->fullGross = bcadd($this->fullGross, $price->getFullGross(), $places);

		// Calculate the unit price
		$this->unRoundedUnitNet = bcdiv($this->fullNet, $this->productQuantity, FinancialPrecision::PLACES);
		$this->unitNet = BCMath::round($this->unRoundedUnitNet, $places);
		$this->unRoundedUnitGross = bcdiv($this->fullGross, $this->productQuantity, FinancialPrecision::PLACES);
		$this->unitGross = BCMath::round($this->unRoundedUnitGross, $places);

		// Copy over full prices to net discounted prices
		$this->discountedNetPricingNet = $this->fullNet;
		$this->discountedNetPricingTax = $this->fullTax;
		$this->discountedNetPricingGross = $this->fullGross;

		// Copy over full prices to gross discounted prices
		$this->discountedGrossPricingNet = $this->fullNet;
		$this->discountedGrossPricingTax = $this->fullTax;
		$this->discountedGrossPricingGross = $this->fullGross;

		// Calculate the full order line cost and weight
		$this->totalCost = bcadd($this->totalCost, $price->getFullCost(), FinancialPrecision::COST_PLACES);
		$this->totalWeight = bcadd($this->totalWeight, $price->getFullWeight(), FinancialPrecision::WEIGHT_PLACES);
	}

	/**
	 * Get the tax rate of the discountable price
	 *
	 * Returns an implementation of the TaxRateInterface interface,
	 * that makes up the underlying tax rate of the prices added to
	 * the discountable price.
	 *
	 * @return TaxRateInterface
	 */
	public function getTaxRate()
	{
		return $this->taxRate;
	}

	/**
	 * Compare the given TaxRateInterface implementation against
	 * discountable's own tax rate
	 *
	 * Compares the given TaxRateInterface implementation against
	 * the discountable's own tax rate and returns true or false,
	 * depending on whether the rates match.
	 *
	 * @param TaxRateInterface $taxRate
	 * @return bool
	 */
	public function compareTaxRate(TaxRateInterface $taxRate)
	{
		return $taxRate->getRate() === $this->taxRate->getRate();
	}

	/**
	 * Check if the pricing for the discountable is made
	 * up of a single tax rate
	 *
	 * Return whether the pricing for the discountable
	 * is made up of a single tax rate, or if the price is made
	 * up of multiple tax rates. Returns true or false, depending
	 * whether a single tax rate is used.
	 *
	 * @return bool
	 */
	public function hasSingleTaxRate()
	{
		return true;
	}

	/**
	 * Discount the net price
	 *
	 * Discount the net price. Current discounted values
	 * can be obtained through the getDiscountedNetPricingNet
	 * method.
	 *
	 * @param string $discountAmount
	 * @return string
	 * @throws LogicException
	 * @throws InvalidArgumentException
	 */
	public function discountNetPrice($discountAmount)
	{
		$discountAmount = (string) $discountAmount;

		// Validate discount amount
		$places = $this->currency->getDecimalPlaces();

		// Return quickly if the amount to be discounted is 0
		if (bccomp(0, $discountAmount, $places) >= 0) {
			return '0';
		} elseif (bccomp($discountAmount, $this->discountedNetPricingNet, $places) === 1) {
			$discountAmount = $this->discountedNetPricingNet;
		}

		// Mark as immutable to prevent further changes
		$this->markFullPriceAsImmutable();

		// Recalculate the net, tax and gross
		$this->discountNetAmount = bcadd($this->discountNetAmount, $discountAmount, $places);
		$discountedNetPricingNet = bcsub($this->discountedNetPricingNet, $discountAmount, $places);
		list($this->discountedNetPricingNet, $this->discountedNetPricingTax, $this->discountedNetPricingGross)
			= NetGrossConversion::breakdownNetToGross(
			$discountedNetPricingNet,
			$this->taxRate->getRate(),
			$places
		);

		return $discountAmount;
	}

	/**
	 * Discount the gross price
	 *
	 * Discount the gross price. Current discounted values
	 * can be obtained through the getDiscountedGrossPricingGross
	 * method.
	 *
	 * @param string $discountAmount
	 * @return string
	 * @throws LogicException
	 * @throws InvalidArgumentException
	 */
	public function discountGrossPrice($discountAmount)
	{
		$discountAmount = (string) $discountAmount;

		// Validate discount amount
		$places = $this->currency->getDecimalPlaces();

		// Return quickly if the amount to be discounted is 0
		if (bccomp(0, $discountAmount, $places) >= 0) {
			return '0';
		} elseif (bccomp($discountAmount, $this->discountedGrossPricingGross, $places) === 1) {
			$discountAmount = $this->discountedGrossPricingGross;
		}

		// Mark as immutable to prevent further changes
		$this->markFullPriceAsImmutable();

		// Recalculate the net, tax and gross
		$this->discountGrossAmount = bcadd($this->discountGrossAmount, $discountAmount, $places);
		$discountedGrossPricingGross = bcsub($this->discountedGrossPricingGross, $discountAmount, $places);
		list ($this->discountedGrossPricingNet, $this->discountedGrossPricingTax, $this->discountedGrossPricingGross)
			= NetGrossConversion::breakdownGrossToNet(
			$discountedGrossPricingGross,
			$this->taxRate->getRate(),
			$places
		);

		return $discountAmount;
	}

	/**
	 * Get the total cost
	 *
	 * A discountable tracks internal cost of the associated prices
	 * added to the discountable price. This method will return
	 * all the individual costs as one total.
	 *
	 * @return string
	 */
	public function getTotalCost()
	{
		return $this->totalCost;
	}

	/**
	 * Get the total weight
	 *
	 * A discountable tracks the weight of the associated prices
	 * added to the discountable price. This method will return
	 * all the individual weights as one total.
	 *
	 * @return string
	 */
	public function getTotalWeight()
	{
		return $this->totalWeight;
	}

	/**
	 * Get the full net value
	 *
	 * Returns the full net value of all prices added to the
	 * discountable, regardless of any discounts that may have
	 * been applied.
	 *
	 * @return string
	 */
	public function getFullNet()
	{
		return $this->fullNet;
	}

	/**
	 * Get the full tax value
	 *
	 * Returns the full tax value of all prices added to the
	 * discountable, regardless of any discounts that may have
	 * been applied.
	 *
	 * @return string
	 */
	public function getFullTax()
	{
		return $this->fullTax;
	}

	/**
	 * Get the full gross value
	 *
	 * Returns the full gross value of all prices added to the
	 * discountable, regardless of any discounts that may have
	 * been applied.
	 *
	 * @return string
	 */
	public function getFullGross()
	{
		return $this->fullGross;
	}

	/**
	 * Get the full net or gross value
	 *
	 * Returns the full net or gross value of all prices added
	 * to the discountable, regardless of any discounts that
	 * may have been applied, based on the $isShowPricesWithTax
	 * parameter passed. If the parameter is false, the net is
	 * returned. If the parameter is true, the gross is returned.
	 *
	 * @param string $isShowPricesWithTax
	 * @return string
	 */
	public function getFullSell($isShowPricesWithTax)
	{
		return $isShowPricesWithTax
			? $this->getFullGross()
			: $this->getFullNet()
		;
	}

	/**
	 * Get the unit net price
	 *
	 * Get the net price of each unit.
	 *
	 * @return string
	 */
	public function getUnitNet()
	{
		return $this->unitNet;
	}

	/**
	 * Get the unrounded unit net price
	 *
	 * Get the net price of each unit.
	 *
	 * @return string
	 */
	public function getUnRoundedNetUnit()
	{
		return $this->unRoundedUnitNet;
	}

	/**
	 * Get the unit gross price
	 *
	 * Get the gross price of each unit.
	 *
	 * @return string
	 */
	public function getUnitGross()
	{
		return $this->unitGross;
	}

	/**
	 * Get the unrounded unit gross price
	 *
	 * Get the gross price of each unit.
	 *
	 * @return string
	 */
	public function getUnRoundedGrossUnit()
	{
		return $this->unRoundedUnitGross;
	}

	/**
	 * Get the amount discounted from the net
	 *
	 * Return the amount discounted from the net when using
	 * net pricing. If no discounts have yet to be made, the
	 * value returned will be 0.
	 *
	 * @return string
	 */
	public function getDiscountNetAmount()
	{
		return $this->discountNetAmount;
	}

	/**
	 * Get the discounted net pricing net value
	 *
	 * Returns the discounted net value for net pricing.
	 * This value is the net price with discounts applied,
	 * resulting in recalculation of the tax and gross.
	 *
	 * @return string
	 */
	public function getDiscountedNetPricingNet()
	{
		return $this->discountedNetPricingNet;
	}

	/**
	 * Get the discounted net pricing tax value
	 *
	 * Returns the discounted tax value for net pricing.
	 * This value is the tax value with discounts applied
	 * to the net, resulting in the recalculating of the tax
	 * price.
	 *
	 * @return string
	 */
	public function getDiscountedNetPricingTax()
	{
		return $this->discountedNetPricingTax;
	}

	/**
	 * Get the discounted net pricing gross value
	 *
	 * Returns the discounted gross value for net pricing.
	 * This value is the gross value with discounts applied
	 * to the net, resulting in the recalculation of the gross
	 * price.
	 *
	 * @return string
	 */
	public function getDiscountedNetPricingGross()
	{
		return $this->discountedNetPricingGross;
	}

	/**
	 * Get the discounted net pricing net or gross value
	 *
	 * Returns the discounted net or gross value for net pricing.
	 * This value is the net or gross value with discounts applied,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the net is returned. If the
	 * parameter is true, the gross is returned.
	 *
	 * @param string $isShowPricesWithTax
	 * @return string
	 */
	public function getDiscountedNetPricingSell($isShowPricesWithTax)
	{
		return $isShowPricesWithTax
			? $this->getDiscountedNetPricingGross()
			: $this->getDiscountedNetPricingNet()
		;
	}

	/**
	 * Get the amount discounted from the gross
	 *
	 * Return the amount discounted from the gross when using
	 * gross pricing. If no discounts have yet to be made, the
	 * value returned will be 0.
	 *
	 * @return string
	 */
	public function getDiscountGrossAmount()
	{
		return $this->discountGrossAmount;
	}

	/**
	 * Get the discounted gross pricing net value
	 *
	 * Returns the discounted net value for gross pricing.
	 * This value is the net value with discounts applied
	 * to the gross, resulting in the recalculation of the net
	 * price.
	 *
	 * @return string
	 */
	public function getDiscountedGrossPricingNet()
	{
		return $this->discountedGrossPricingNet;
	}

	/**
	 * Get the discounted gross pricing tax value
	 *
	 * Returns the discounted tax value for gross pricing.
	 * This value is the tax value with discounts applied
	 * to the gross, resulting in the recalculating of the tax
	 * price.
	 *
	 * @return string
	 */
	public function getDiscountedGrossPricingTax()
	{
		return $this->discountedGrossPricingTax;
	}

	/**
	 * Get the discounted gross pricing gross value
	 *
	 * Get the discounted gross value for gross pricing.
	 * This value is the gross price with discounts applied,
	 * resulting in recalculation of the tax and net.
	 *
	 * @return string
	 */
	public function getDiscountedGrossPricingGross()
	{
		return $this->discountedGrossPricingGross;
	}

	/**
	 * Get the discounted gross pricing net or gross value
	 *
	 * Returns the discounted net or gross value for gross pricing.
	 * This value is the net or gross value with discounts applied,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the net is returned. If the
	 * parameter is true, the gross is returned.
	 *
	 * @param string $isShowPricesWithTax
	 * @return string
	 */
	public function getDiscountedGrossPricingSell($isShowPricesWithTax)
	{
		return $isShowPricesWithTax
			? $this->getDiscountedGrossPricingGross()
			: $this->getDiscountedGrossPricingNet()
		;
	}

	/**
	 * Get the discounted net or gross pricing tax value
	 *
	 * Returns the tax value for net or gross pricing, based on the
	 * $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the tax for net pricing is returned.
	 * If the parameter is true, the tax for gross pricing is returned.
	 *
	 * @param string $isShowPricesWithTax
	 * @return string
	 */
	public function getDiscountedTax($isShowPricesWithTax)
	{
		return $isShowPricesWithTax
			? $this->getDiscountedGrossPricingTax()
			: $this->getDiscountedNetPricingTax()
		;
	}

	/**
	 * Get the discount amount for net or gross pricing.
	 *
	 * Returns the discount amount for net or gross pricing,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the net pricing discount
	 * amount is returned. If the parameter is true, the gross
	 * pricing discount amount is returned.
	 *
	 * @param bool $isShowPricesWithTax
	 * @return string
	 */
	public function getDiscountSellAmount($isShowPricesWithTax)
	{
		return $isShowPricesWithTax
			? $this->getDiscountGrossAmount()
			: $this->getDiscountNetAmount()
		;
	}

	/**
	 * Get the discounted net or gross value for net or gross pricing
	 *
	 * Returns the discounted net or gross value for net or gross pricing,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, the net is returned. If the
	 * parameter is true, the gross is returned.
	 *
	 * @param string $isShowPricesWithTax
	 * @return string
	 */
	public function getDiscountedSell($isShowPricesWithTax)
	{
		return $isShowPricesWithTax
			? $this->getDiscountedGrossPricingGross()
			: $this->getDiscountedNetPricingNet()
		;
	}

	/**
	 * Get the discounted net value for net or gross pricing
	 *
	 * Returns the discounted net value for net or gross pricing,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, net pricing net is returned. If the
	 * parameter is true, gross pricing net is returned.
	 *
	 * @param bool $isShowPricesWithTax
	 * @return string
	 */
	public function getDiscountedNet($isShowPricesWithTax)
	{
		return $isShowPricesWithTax
			? $this->getDiscountedGrossPricingNet()
			: $this->getDiscountedNetPricingNet()
		;
	}

	/**
	 * Get the discounted gross value for net or gross pricing
	 *
	 * Returns the discounted net value for net or gross pricing,
	 * based on the $isShowPricesWithTax parameter passed.
	 * If the parameter is false, net pricing net is returned. If the
	 * parameter is true, gross pricing net is returned.
	 *
	 * @param bool $isShowPricesWithTax
	 * @return string
	 */
	public function getDiscountedGross($isShowPricesWithTax)
	{
		return $isShowPricesWithTax
			? $this->getDiscountedGrossPricingGross()
			: $this->getDiscountedNetPricingGross()
			;
	}

	/**
	 * Reset the net discount to full
	 */
	public function resetNetDiscountToFull()
	{
		$this->discountNetAmount = '0';
		$this->discountedNetPricingNet = $this->fullNet;
		$this->discountedNetPricingTax = $this->fullTax;
		$this->discountedNetPricingGross = $this->fullGross;
	}

	/**
	 * Reset the gross discount to full
	 */
	public function resetGrossDiscountToFull()
	{
		$this->discountGrossAmount = '0';
		$this->discountedGrossPricingNet = $this->fullNet;
		$this->discountedGrossPricingTax = $this->fullTax;
		$this->discountedGrossPricingGross = $this->fullGross;
	}
}
