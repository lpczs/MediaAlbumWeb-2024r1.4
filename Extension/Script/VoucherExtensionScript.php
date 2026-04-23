<?php

namespace Extension\Script;

use ArrayAccess;
use Extension\Script\Exception\ExtensionScriptMethodNotFoundException;
use Extension\Script\Exception\ExtensionScriptNotLoadedException;
use Extension\Script\Exception\UnhandledExtensionScriptErrorException;

class VoucherExtensionScript extends AbstractExtensionScript
{
	const FILE_NAME = 'EDL_Voucher.php';
	const CLASS_NAME = 'EDL_VoucherScriptObj';
	const BASE_CLASS_NAME = '_Voucher';

	/**
	 * Get the extension bootstrap file to include
	 *
	 * @return string
	 */
	protected function getExtensionFilePath()
	{
		return $this->extensionPath . DIRECTORY_SEPARATOR . self::FILE_NAME;
	}

	/**
	 * Get the extension class name
	 *
	 * @return string
	 */
	protected function getExtensionClassName()
	{
		return self::CLASS_NAME;
	}

	/**
	 * Get the extension base class name
	 *
	 * @return string
	 */
	protected function getExtensionBaseClassName()
	{
		return self::BASE_CLASS_NAME;
	}

	/**
	 * Validate a voucher promotion or voucher code
	 *
	 * @param string $voucherPromotionCode
	 * @param string $voucherCode
	 * @param int $lineNumber
	 * @return mixed[]
	 * @throws ExtensionScriptMethodNotFoundException
	 * @throws ExtensionScriptNotLoadedException
	 * @throws UnhandledExtensionScriptErrorException
	 */
	public function validate($voucherPromotionCode, $voucherCode, $lineNumber)
	{
		$status = false;
		$errorMessage = null;
		$result = $this->callExtension('validate', $voucherPromotionCode, $voucherCode, $lineNumber);

		// Filter the response to ensure we return an array of two values of a boolean, and a string or null
		if (is_bool($result)) {
			$status = $result;
		} elseif (is_array($result)) {
			if (is_bool(@$result[0])) {
				$status = $result[0];
			}

			if (is_scalar(@$result[1])) {
				$errorMessage = (string) $result[1];
			}
		}

		return [$status, $errorMessage];
	}

	/**
	 * Discount the order line using the voucher promotion or voucher code
	 *
	 * @param string $voucherPromotionCode
	 * @param string $voucherCode
	 * @param int $lineNumber
	 * @return mixed[]
	 * @throws ExtensionScriptMethodNotFoundException
	 * @throws ExtensionScriptNotLoadedException
	 * @throws UnhandledExtensionScriptErrorException
	 */
	public function discount($voucherPromotionCode, $voucherCode, $lineNumber)
	{
		$result = $this->callExtension('calcDiscountedValue', $voucherPromotionCode, $voucherCode, $lineNumber);

		// Filter the response to ensure we only return an associate array of string values with the recognised keys
		$response = [
			'discountvalue' => '',
			'discountname' => '0',
			'ordertotaldiscountvalue' => '0',
			'shippingdiscountvalue' => '0',
			'sellprice' => '0',
			'agentfee' => '0',
		];

		if (is_array($result) || $result instanceof ArrayAccess) {
			foreach(['ordertotaldiscountvalue', 'shippingdiscountvalue', 'sellprice', 'agentfee'] as $key) {
				if (isset($result[$key]) && is_numeric($response[$key])) {
					$response[$key] = (string) $result[$key];
				}
			};

			foreach(['discountvalue', 'discountname'] as $key) {
				if (isset($result[$key])) {
					$response[$key] = (string) $result[$key];
				}
			};
		}

		return $response;
	}
}
