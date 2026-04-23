<?php

namespace PricingEngine;

use PricingEngine\OrderLine\CheckboxComponentAssociation;
use PricingEngine\OrderLine\SectionComponentAssociation;
use PricingEngine\Tax\TaxRateInterface;
use PricingEngine\Voucher\DiscountableQuantityInterface;

/**
 * Order line handling
 *
 * Represents an order line attached to an order.
 * An order line is a project being ordered by the customer
 * as well as associated assets/components.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
interface OrderLineInterface extends DiscountableQuantityInterface
{
	/**
	 * Get product code
	 *
	 * Get the associated product code of the project
	 * that this order line is for.
	 *
	 * @return string
	 */
	public function getProductCode();

	/**
	 * Get tax rate
	 *
	 * Get the tax rate of the order line. This is
	 * the tax rate that will be used to calculate
	 * tax for all assets/components that are
	 * associated with the order line that make up
	 * its price.
	 *
	 * @return TaxRateInterface
	 */
	public function getTaxRate();

	/**
	 * Get the project name
	 *
	 * Get the project name of the order line.
	 *
	 * @return string
	 */
	public function getProjectName();

	/**
	 * Get currency
	 *
	 * Get the currency used by the order
	 *
	 * @return CurrencyInterface
	 */
	public function getCurrency();

	/**
	 * Get line number
	 *
	 * Get the line number of the order line.
	 *
	 * @return int
	 */
	public function getLineNumber();

	/**
	 * Check if show prices with tax is enabled
	 *
	 * Check if the license key option, "show prices with tax"
	 * is enabled. The option affects the way the order price
	 * and the amounts discounted from the order are applied.
	 *
	 * @return bool
	 */
	public function isShowPricesWithTax();

	/**
	 * Set whether a voucher can be applied.
	 *
	 * Set whether a voucher can be applied to the order line.
	 * This is used by the voucher later to decide if the voucher
	 * should be applied. The actual discount is not applied by
	 * this method.
	 *
	 * @param bool $canApplyVoucher
	 * @return $this
	 */
	public function canApplyVoucher();

	/**
	 * Check if the order line has been discounted
	 *
	 * Check if the order line has been discounted by a voucher.
	 *
	 * @return bool
	 */
	public function isDiscounted();

	/**
	 * Get a section or checkbox component
	 *
	 * Get a component for the component code given. Returns
	 * null if the component is not found.
	 *
	 * @param string $componentCode
	 * @return CheckboxComponentAssociation|SectionComponentAssociation|null
	 */
	public function getComponentAssociation($componentCode);

	/**
	 * Get a section or checkbox footer component
	 *
	 * Get a footer component for the component code given.
	 * Returns null if the component is not found.
	 *
	 * @param string $componentCode
	 * @return CheckboxComponentAssociation|SectionComponentAssociation|null
	 */
	public function getFooterComponentAssociation($componentCode);

	/**
	 * Get section component associations
	 *
	 * Get all section component associations added to the
	 * order line.
	 *
	 * @return SectionComponentAssociation[]
	 */
	public function getSectionComponentAssociations();

	/**
	 * Get a section component association
	 *
	 * Get a section component association by its component code
	 *
	 * @param string $componentCode
	 * @return SectionComponentAssociation
	 */
	public function getSectionComponentAssociation($componentCode);

	/**
	 * Get checkbox component associations
	 *
	 * Get all checkbox component associations added to the
	 * order line.
	 *
	 * @return CheckboxComponentAssociation[]
	 */
	public function getCheckboxComponentAssociations();

	/**
	 * Get a checkbox component association
	 *
	 * Get a checkbox component association by its component code
	 *
	 * @param string $componentCode
	 * @return CheckboxComponentAssociation
	 */
	public function getCheckboxComponentAssociation($componentCode);

	/**
	 * Get section footer component associations
	 *
	 * Get all section component footer associations added to the
	 * order line.
	 *
	 * @return SectionComponentAssociation[]
	 */
	public function getFooterSectionComponentAssociations();

	/**
	 * Get a section footer component association
	 *
	 * Get a section footer component association by its component code
	 *
	 * @param string $componentCode
	 * @return SectionComponentAssociation
	 */
	public function getFooterSectionComponentAssociation($componentCode);

	/**
	 * Get all footer checkbox component associations
	 *
	 * @return CheckboxComponentAssociation[]
	 */
	public function getFooterCheckboxComponentAssociations();

	/**
	 * Get a footer checkbox component association by its component code
	 *
	 * @param string $componentCode
	 * @return CheckboxComponentAssociation
	 */
	public function getFooterCheckboxComponentAssociation($componentCode);

	/**
	 * Get the original quantity of units that make up the order,
	 * before it may have updated by price break business rules.
	 *
	 * If the calculate price has not yet been called, then this value
	 * will match the current session item quantity.
	 *
	 * @return int
	 */
	public function getRequestedProductQuantity();
}
