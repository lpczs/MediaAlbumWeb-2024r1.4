<?php

namespace PricingEngine\Price;

use InvalidArgumentException;
use PricingEngine\BCMath;
use PricingEngine\CurrencyInterface;
use PricingEngine\Enum\FinancialPrecision;
use PricingEngine\Tax\TaxRateInterface;

/**
 * Product or component price
 *
 * Represents the price of something, such as a product
 * or component. The price as stored in the database
 * will be converted to a net, tax and gross value
 * based on whether the price is stored as net or gross
 * and if gross, whether the order tax rate is different
 * to the product tax rate.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class Price
{
	/**
	 * @var CurrencyInterface
	 */
	protected $currency;

	/**
	 * @var string
	 */
	protected $net = '0';

	/**
	 * @var string
	 */
	protected $tax = '0';

	/**
	 * @var string
	 */
	protected $gross = '0';

	/**
	 * @var string
	 */
	protected $unit = '0';

	/**
	 * @var string
	 */
	protected $unitQuantity = '0';

	/**
	 * @var string
	 */
	protected $cost = '0';

	/**
	 * @var string
	 */
	protected $weight = '0';

	/**
	 * @var TaxRateInterface
	 */
	protected $taxRate;

	/**
	 * @var TaxRateInterface
	 */
	protected $grossTaxRate;

	/**
	 * Convenience function to create a Price instance using zero
	 * for all values
	 *
	 * @param CurrencyInterface $currency
	 * @param TaxRateInterface $taxRate
	 * @return Price
	 */
	public static function createZeroPrice(CurrencyInterface $currency, TaxRateInterface $taxRate)
	{
		return new self($currency, '0', '0', '0', '0', '0', '0', '0', $taxRate);
	}

	/**
	 * Convenience function to create a Price instance based
	 * on the sell price, a desired tax rate and an optional
	 * gross price tax rate
	 *
	 * @param CurrencyInterface $currency
	 * @param string $price
	 * @param string $unit
	 * @param string $unitQuantity
	 * @param string $cost
	 * @param string $weight
	 * @param TaxRateInterface $taxRate
	 * @param TaxRateInterface|null $grossTaxRate
	 * @return Price
	 */
	public static function createFromSellPrice(CurrencyInterface $currency, $price, $unit, $unitQuantity, $cost, $weight,
		TaxRateInterface $taxRate, TaxRateInterface $grossTaxRate = null)
	{
		if (!is_numeric($price)) {
			throw new InvalidArgumentException('Price is not numeric.');
		}

		$places = $currency->getDecimalPlaces();

		if (null !== $grossTaxRate && $taxRate->getRate() !== $grossTaxRate->getRate()) {
			$price = NetGrossConversion::convertGrossToNet($price, $grossTaxRate->getRate(), $places);
			$grossTaxRate = null;
		}

		if (null !== $grossTaxRate) {
			list ($net, $tax, $gross) = NetGrossConversion::breakdownGrossToNet($price, $grossTaxRate->getRate(), $places);
		} else {
			list ($net, $tax, $gross) = NetGrossConversion::breakdownNetToGross($price, $taxRate->getRate(), $places);
		}

		return new self($currency, $net, $tax, $gross, $unit, $unitQuantity, $cost, $weight, $taxRate, $grossTaxRate);
	}

	/**
	 * Constructor
	 *
	 * @param CurrencyInterface $currency
	 * @param string $net
	 * @param string $tax
	 * @param string $gross
	 * @param string $unit
	 * @param string $unitQuantity
	 * @param string $cost
	 * @param string $weight
	 * @param TaxRateInterface $taxRate
	 * @param TaxRateInterface|null $grossTaxRate
	 */
	public function __construct(CurrencyInterface $currency, $net, $tax, $gross, $unit, $unitQuantity, $cost, $weight,
		TaxRateInterface $taxRate, TaxRateInterface $grossTaxRate = null)
	{
		$this->currency = $currency;
		$this->net = $net;
		$this->tax = $tax;
		$this->gross = $gross;
		$this->unit = $unit;
		$this->unitQuantity = $unitQuantity;
		$this->cost = $cost;
		$this->weight = $weight;
		$this->taxRate = $taxRate;
		$this->grossTaxRate = $grossTaxRate;
	}

	/**
	 * @param int $quantity
	 * @param bool $includeFirstUnit
	 * @return Price
	 * @throws InvalidArgumentException
	 */
	public function priceForUnits($quantity, $includeFirstUnit)
	{
		if ($quantity <= 0 || $quantity > $this->unitQuantity) {
			throw new InvalidArgumentException();
		}

		$places = $this->currency->getDecimalPlaces();
		$net = '0';
		$tax = '0';
		$gross = '0';

		$subsequentUnitNet = BCMath::floor(bcdiv($this->getFullNet(), $this->unitQuantity, $places), $places);
		$subsequentUnitTax = BCMath::floor(bcdiv($this->getFullTax(), $this->unitQuantity, $places), $places);
		$subsequentUnitGross = BCMath::floor(bcdiv($this->getFullGross(), $this->unitQuantity, $places), $places);

		if ($includeFirstUnit) {
			$subsequentUnitQuantity = $this->unitQuantity - 1;

			$firstUnitNet = bcsub($this->getFullNet(), bcmul($subsequentUnitNet, $subsequentUnitQuantity, $places),$places);
			$net = bcadd($net, $firstUnitNet, $places);

			$firstUnitTax = bcsub($this->getFullTax(), bcmul($subsequentUnitTax, $subsequentUnitQuantity, $places),$places);
			$tax = bcadd($tax, $firstUnitTax, $places);

			$firstUnitGross = bcsub($this->getFullGross(), bcmul($subsequentUnitGross, $subsequentUnitQuantity, $places),$places);
			$gross = bcadd($gross, $firstUnitGross, $places);

			$quantity--;
		}

		if ($quantity) {
			$net = bcadd($net, bcmul($subsequentUnitNet, $quantity, $places), $places);
			$tax = bcadd($tax, bcmul($subsequentUnitTax, $quantity, $places), $places);
			$gross = bcadd($gross, bcmul($subsequentUnitGross, $quantity, $places), $places);
		}

		$cost = bcmul(bcdiv($this->getFullCost(), $this->unitQuantity, FinancialPrecision::COST_PLACES), $quantity, FinancialPrecision::COST_PLACES);
		$weight = bcmul(bcdiv($this->getFullWeight(), $this->unitQuantity, FinancialPrecision::COST_PLACES), $quantity, FinancialPrecision::COST_PLACES);

		return new self($this->currency, $net, $tax, $gross, $this->unit, $quantity, $cost, $weight,
			$this->taxRate, $this->grossTaxRate);
	}

	/**
	 * Add to the price
	 *
	 * Add another price to this price. The tax rate used between
	 * price instances must be the same.
	 *
	 * @param Price|null $price
	 */
	public function addPrice(Price $price = null)
	{
		if (null === $price) {
			return;
		}

		$places = $this->getPlaces();

		$this->net = bcadd($this->net, $price->getFullNet(), $places);
		$this->tax = bcadd($this->tax, $price->getFullTax(), $places);
		$this->gross = bcadd($this->gross, $price->getFullGross(), $places);
		$this->cost = bcadd($this->cost, $price->getFullCost(), 4);
		$this->weight = bcadd($this->weight, $price->getFullWeight(), 4);
	}

	/**
	 * Get the net
	 *
	 * Get the full net price.
	 *
	 * @return string
	 */
	public function getFullNet()
	{
		return $this->net;
	}

	/**
	 * Get the tax
	 *
	 * Get the full tax amount for the net price.
	 *
	 * @return string
	 */
	public function getFullTax()
	{
		return $this->tax;
	}

	/**
	 * Get the gross
	 *
	 * Get the gross amount for the net and tax.
	 *
	 * @return string
	 */
	public function getFullGross()
	{
		return $this->gross;
	}

	/**
	 * Get the sell price
	 *
	 * Using the $isShowPricesWithTax, return
	 * a sell price for the instance or either the
	 * net or gross.
	 *
	 * @param bool $isShowPricesWithTax
	 * @return string
	 */
	public function getFullSell($isShowPricesWithTax)
	{
		return $isShowPricesWithTax ? $this->getFullGross() : $this->getFullNet();
	}

	/**
	 * Get the unit
	 *
	 * Get the price break unit price. This is NOT the unit price
	 * of an order line.
	 *
	 * @return string
	 */
	public function getUnit()
	{
		return $this->unit;
	}

	/**
	 * Get tax rate
	 *
	 * Get the tax rate used for calculation of the gross
	 *
	 * @return TaxRateInterface
	 */
	public function getTaxRate()
	{
		return $this->taxRate;
	}

	/**
	 * Get gross pricing tax rate
	 *
	 * Get the tax rate specified when a gross price was configured.
	 * Note that the tax rate used may not be the same.
	 *
	 * @return TaxRateInterface
	 */
	public function getGrossTaxRate()
	{
		return $this->grossTaxRate;
	}

	/**
	 * Get full cost
	 *
	 * @return string
	 */
	public function getFullCost()
	{
		return $this->cost;
	}

	/**
	 * Set full weight
	 *
	 * @param $weight
	 * @return $this
	 */
	public function setFullWeight($weight)
	{
		$this->weight = $weight;
		return $this;
	}

	/**
	 * Get full weight
	 *
	 * @return string
	 */
	public function getFullWeight()
	{
		return $this->weight;
	}

	/**
	 * Get the currency decimal places
	 *
	 * Get the currency rounding decimal places to be used
	 * for the order when rounding all monetary values.
	 *
	 * @return int
	 */
	public function getPlaces()
	{
		return $this->currency->getDecimalPlaces();
	}
}
