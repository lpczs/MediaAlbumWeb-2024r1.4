<?php

namespace PricingEngine\Voucher\Script;

use Exception;
use Extension\Script\VoucherExtensionScript;
use PricingEngine\BCMath;
use PricingEngine\Enum\Voucher\DiscountSection;
use PricingEngine\Enum\Voucher\DiscountType;
use PricingEngine\Enum\Voucher\VoucherType;
use PricingEngine\OrderLineInterface;
use PricingEngine\Voucher\AbstractVoucher;
use PricingEngine\Voucher\DiscountableInterface;
use PricingEngine\Voucher\VoucherContext;

/**
 * Script voucher
 *
 * Apply a discount using an external extension script.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class ScriptVoucher extends AbstractVoucher
{
	/**
	 * @var VoucherExtensionScript
	 */
	private $voucherExtensionScript;

	/**
	 * Constructor
	 *
	 * @param array $options
	 * @param VoucherExtensionScript $voucherExtensionScript
	 */
	public function __construct($options, VoucherExtensionScript $voucherExtensionScript)
	{
		parent::__construct($options);
		$this->voucherExtensionScript = $voucherExtensionScript;
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
		return true;
	}

	/**
	 * Apply the voucher discount to the discountable using the context
	 *
	 * Apply the discount logic of the voucher type to the discountable instance passed.
	 *
	 * @param VoucherContext $voucherContext
	 * @param DiscountableInterface $discountable
	 */
	public function discount(VoucherContext $voucherContext, DiscountableInterface $discountable)
	{
		// The discountable object must be an order line
		if (!$discountable instanceof OrderLineInterface || !$discountable->canApplyVoucher()) {
			return;
		}

		// Attempt to load the extension script and if it can't be loaded, abort quickly
		if (!$this->voucherExtensionScript->load()) {
			return;
		}

		// Ask the script for the discount amounts
		$order = $voucherContext->getOrder();
		$places = $order->getPlaces();
		$lineNumber = $discountable->getLineNumber();

		try {
			$result = $this->voucherExtensionScript->discount(
				$this->voucherPromotionCode,
				$this->voucherCode,
				$lineNumber
			);
		} catch (Exception $ex) {
			return;
		}

		// Apply the discounts returned (if any) to the correct parts of the order
		if (bccomp($result['discountvalue'], 0, $places) === 1) {
			$this->applyDiscount($discountable, $result['discountvalue'], $result['discountname']);

			$voucherContext
				->setDiscountType(DiscountType::VALUE_OFF)
				->setDiscountSection(DiscountSection::PRODUCT)
			;
		} elseif (bccomp($result['shippingdiscountvalue'], 0, $places) === 1) {
			$shippingMethod = $order->getShippingMethod();
			$this->applyDiscount($shippingMethod, $result['shippingdiscountvalue'], $result['discountname']);

			$voucherContext
				->setDiscountType(DiscountType::VALUE_OFF)
				->setDiscountSection(DiscountSection::SHIPPING)
			;
		} elseif (bccomp($result['ordertotaldiscountvalue'], 0, $places) === 1) {
			$this->applyDiscount($order, $result['ordertotaldiscountvalue'], $result['discountname']);

			$voucherContext
				->setDiscountType(DiscountType::VALUE_OFF)
				->setDiscountSection(DiscountSection::TOTAL)
			;
		} else {
			// emulate what the legacy pricing engine sets when being returned a 0 discount 
			$voucherContext
				->setDiscountType(DiscountType::VALUE_OFF)
				->setDiscountSection(DiscountSection::PRODUCT)
			;
		}

		// Convert the voucher type to pre-paid if necessary
		if (bccomp($result['sellprice'], 0, $places) === 1) {
			$voucherContext
				->setVoucherType(VoucherType::PRE_PAID)
				->setSellPrice($result['sellprice'])
				->setAgentFee(@$result['agentfee']);
		}
	}

	/**
	 * Apply the discount to the discountable object
	 *
	 * Helper method to apply the discount for the script voucher to the discountable.
	 *
	 * @param DiscountableInterface $discountable
	 * @param string $value
	 * @param string $voucherName
	 */
	private function applyDiscount(DiscountableInterface $discountable, $value, $voucherName)
	{
		$places = $discountable->getPlaces();
		$value = BCMath::round($value, $places);

		$discountable->discountNetPrice($value);
		$discountable->discountGrossPrice($value);

		// If the description from the script is empty we are using the one already set from CC.
		if (! empty($voucherName))
		{
			$discountable->setVoucherName($voucherName);
		}
	}
}
