<?php

namespace PricingEngine;

use PricingEngine\Order\FooterCheckboxComponentAssociation;
use PricingEngine\Order\FooterSectionComponentAssociation;
use PricingEngine\Voucher\DiscountableInterface;
use PricingEngine\Voucher\ReverseDiscountableInterface;
use PricingEngine\Tax\TaxBreakdownInterface;

/**
 * Order interface
 *
 * Represents an order made by a customer for the purposes
 * of calculating the price, which is persisted to a session
 * reference variable, supplied upon construction.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
interface OrderInterface extends ReverseDiscountableInterface, TaxBreakdownInterface
{
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
	 * Check if the order is a reorder
	 *
	 * Check if the order is a reorder of an existing order.
	 *
	 * @return bool
	 */
	public function isReorder();

	/**
	 * Get currency
	 *
	 * Get the currency used by the order
	 *
	 * @return CurrencyInterface
	 */
	public function getCurrency();

	/**
	 * Get license key code
	 *
	 * Get the associated license key code of the license key
	 * used in the order.
	 *
	 * @return string
	 */
	public function getLicenseKeyCode();

	/**
	 * Get company code
	 *
	 * Get the associated company code of the license key
	 * used in the order.
	 *
	 * @return string
	 */
	public function getCompanyCode();

	/**
	 * Get the order lines
	 *
	 * Get all the order line object instances
	 * in an array. Returns an empty array if none
	 * added.
	 *
	 * @return OrderLineInterface[]
	 */
	public function getOrderLines();

	/**
	 * Get the number of order lines
	 *
	 * Get the number of order lines in the order.
	 * Returns 0 if none added.
	 *
	 * @return int
	 */
	public function getOrderLineCount();

	/**
	 * Get a specific order line
	 *
	 * Get a specific order line using the line
	 * number given. Returns null not found.
	 *
	 * @param int $orderLineNumber
	 * @return OrderLineInterface
	 */
	public function getOrderLine($orderLineNumber);

	/**
	 * Get a section or checkbox component
	 *
	 * Get the order footer component association for
	 * the given component code that has been added
	 * to the order. Returns null if not found.
	 *
	 * @param string $componentCode
	 * @return FooterCheckboxComponentAssociation|FooterSectionComponentAssociation|null
	 */
	public function getFooterComponentAssociation($componentCode);

	/**
	 * Get all footer checkbox component associations
	 *
	 * Get all the order footer checkbox component
	 * associations added to the order. Returns an
	 * empty array if non have been added.
	 *
	 * @return FooterCheckboxComponentAssociation[]
	 */
	public function getFooterCheckboxComponentAssociations();

	/**
	 * Get all footer section component associations
	 *
	 * Get all the order footer section component
	 * associations added to the order. Returns an
	 * empty array if non have been added.
	 *
	 * @return FooterSectionComponentAssociation[]
	 */
	public function getFooterSectionComponentAssociations();

	/**
	 * Get the shipping method
	 *
	 * Get the shipping method associated to the order.
	 * Returns null if no shipping method has been added.
	 *
	 * @return ShippingMethodInterface
	 */
	public function getShippingMethod();

	/**
	 * Apply the discount, only if the voucher is a non-script voucher type
	 *
	 * If the voucher added to the order is not a script voucher,
	 * apply the discount. Returns true if the voucher discount was attempted,
	 * false otherwise. Note that a discount may still not be applied by
	 * the voucher instance if no matching lines are found.
	 *
	 * @param DiscountableInterface $discountable
	 * @return bool
	 */
	public function discount(DiscountableInterface $discountable);

	/**
	 * Apply the discount, only if the voucher is a script voucher type
	 *
	 * If the voucher added to the order is a script voucher,
	 * apply the discount. Returns true if the voucher discount was attempted,
	 * false otherwise. Note that a discount may still not be applied by the
	 * voucher script.
	 *
	 * @param DiscountableInterface $discountable
	 * @return bool
	 */
	public function scriptDiscount(DiscountableInterface $discountable);

	/**
	 * Set voucher discount type
	 *
	 * Set the voucher discount type for the voucher applied.
	 * This is made available for voucher scripting, which
	 * sets the voucher dependent on the configuration and
	 * provided by the voucher script.
	 * Returns null if no voucher discount type has been set.
	 *
	 * @param string $discountType
	 * @return $this
	 */
	public function setVoucherDiscountType($discountType);

	/**
	 * Get voucher type
	 *
	 * Get the voucher type of the voucher applied.
	 * This is made available for voucher scripting, which
	 * sets the voucher dependent on the configuration and
	 * provided by the voucher script.
	 *
	 * @return string
	 */
	public function getVoucherType();

	/**
	 * Set voucher type
	 *
	 * Set the voucher type for the voucher applied.
	 * This is made available for voucher scripting, which
	 * sets the voucher dependent on the configuration and
	 * provided by the voucher script.
	 * Returns null if no voucher type has been set.
	 *
	 * @param string $voucherType
	 * @return $this
	 */
	public function setVoucherType($voucherType);

	/**
	 * Get voucher sell price
	 *
	 * Get the voucher sell price for the voucher applied.
	 * This is made available for voucher scripting, which
	 * sets the voucher dependent on the configuration and
	 * provided by the voucher script.
	 * Returns null if no voucher sell price has been set.
	 *
	 * @return string
	 */
	public function getVoucherSellPrice();

	/**
	 * Set voucher sell price
	 *
	 * Get the voucher sell price for the voucher applied.
	 * This is made available for voucher scripting, which
	 * sets the voucher dependent on the configuration and
	 * provided by the voucher script.
	 *
	 * @param string $sellPrice
	 * @return $this
	 */
	public function setVoucherSellPrice($sellPrice);

	/**
	 * Get voucher agent fee
	 *
	 * Get the voucher agent fee for the voucher applied.
	 * This is made available for voucher scripting, which
	 * sets the voucher dependent on the configuration and
	 * provided by the voucher script.
	 * Returns null if no voucher agent fee has been set.
	 *
	 * @return string
	 */
	public function getVoucherAgentFee();

	/**
	 * Set voucher agent fee
	 *
	 * Set the voucher agent fee for the voucher applied.
	 * This is made available for voucher scripting, which
	 * sets the voucher dependent on the configuration and
	 * provided by the voucher script.
	 *
	 * @param string $agentFee
	 * @return $this
	 */
	public function setVoucherAgentFee($agentFee);
}
