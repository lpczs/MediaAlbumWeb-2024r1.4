<?php

namespace PricingEngine\Tax;

use PricingEngine\CurrencyInterface;
use PricingEngine\Enum\ExtensionScript;

/**
 * Tax breakdown
 *
 * Tracks total net and tax values for each supplied
 * tax code.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class TaxBreakdown implements TaxBreakdownInterface
{
	/**
	 * @var CurrencyInterface
	 */
	private $currency;

	/**
	 * @var mixed[]
	 */
	private $session;

	/**
	 * Constructor
	 *
	 * @param CurrencyInterface $currency
	 * @param mixed[] $session
	 */
	public function __construct(CurrencyInterface $currency, &$session)
	{
		$this->currency = $currency;
		$this->session = &$session;

		// Reset all values
		foreach ($this->session as $item => &$value) {
			$value['nettotal'] = '0';
			$value['taxtotal'] = '0';
		}
	}

	/**
	 * Record the net and tax amounts for the given tax rate
	 * in the tax breakdown
	 *
	 * Record the net and tax monetary values within the tax
	 * breakdown for the given tax rate instance supplied.
	 * The tax rate code must already be recognised in the
	 * the breakdown for the values to be recorded.
	 *
	 * @param TaxRateInterface $taxRate
	 * @param string $net
	 * @param string $tax
	 */
	public function recordTaxInBreakdown(TaxRateInterface $taxRate, $net, $tax)
	{
		$places = $this->currency->getDecimalPlaces();

		foreach ($this->session as &$taxBreakdownSummary) {
			if ($taxBreakdownSummary['taxratecode'] === $taxRate->getCode()) {
				$taxBreakdownSummary['nettotal'] = bcadd($taxBreakdownSummary['nettotal'], $net, $places);
				$taxBreakdownSummary['taxtotal'] = bcadd($taxBreakdownSummary['taxtotal'], $tax, $places);
				break;
			}
		}
	}

	/**
	 * Check if a single tax rate exists
	 *
	 * Checks if the tax breakdown contains just a single tax rate
	 * and whether that tax rate code is for a custom tax script.
	 *
	 * If only a single rate exists, and we're not using tax scripting
	 * then true is returned, false otherwise.
	 *
	 * @return bool
	 */
	public function hasSingleTaxRate()
	{
		return count($this->session) === 1 && $this->session[0]['taxratecode'] !== ExtensionScript::TAX_SCRIPT_TAX_CODE;
	}
}
