<?php

namespace Extension\Script\Callback;

use DatabaseObj;
use Order_model;
use UtilsAddressObj;
use UtilsObj;

class ShippingExtensionScriptCallback
{
	/**
	 * @param $shippingMethodEntry
	 */
	public static function setShippingMethodDefaults(&$shippingMethodEntry)
	{
		Order_model::setShippingMethodDefaults($shippingMethodEntry);
	}

	/**
	 * @param $shippingMethodCode
	 * @param $shippingMethodName
	 * @param $shippingMethodEntry
	 * @param $groupCode
	 */
	public static function setCollectFromStoreValues($shippingMethodCode, $shippingMethodName, &$shippingMethodEntry, $groupCode)
	{
		Order_model::setCollectFromStoreValues(
			$shippingMethodCode,
			$shippingMethodName,
			$shippingMethodEntry,
			$groupCode
		);
	}

	/**
	 * @param $taxCode
	 * @return mixed[]
	 */
	public static function getTaxRate($taxCode)
	{
		return DatabaseObj::getTaxRate($taxCode);
	}

	/**
	 * @param $sourceAddressArray
	 * @param $sourceKeyPrefix
	 * @param $destinationArray
	 * @param $destinationKeyPrefix
	 * @param $copyingDefaultAddress
	 * @param $addressFieldsOnly
	 */
	public static function copyArrayAddressFields($sourceAddressArray, $sourceKeyPrefix, &$destinationArray,
		$destinationKeyPrefix, $copyingDefaultAddress, $addressFieldsOnly)
	{
		UtilsAddressObj::copyArrayAddressFields($sourceAddressArray, $sourceKeyPrefix, $destinationArray,
			$destinationKeyPrefix, $copyingDefaultAddress, $addressFieldsOnly);
	}

	/**
	 * @param string $methodCode
	 * @param string $rateCode
	 * @param string $groupCode
	 * @param string $companyCode
	 * @return mixed[]
	 */
	public static function getShippingRateFromMethodAndRateCode($methodCode, $rateCode, $groupCode, $companyCode)
	{
		$resultArray = UtilsObj::getReturnArray();

		$shippingRateID = 0;
		$parentID = 0;
		$shippingRateCode = '';
		$shippingMethodCode = '';
		$shippingMethodName = '';
		$productCode = '';
		$info = '';
		$shippingRates = '';
		$orderValueType = '';
		$orderMinValue = 0.00;
		$orderMaxValue = 0.00;
		$isActive = 0;
		$taxCode = '';
		$shippingZoneCode = '';
		$orderValueIncludesDiscount = '';
		$taxRate = 0.00;
		$useDefaultBillingAddress = 0;
		$useDefaultShippingAddress = 0;
		$isDefault = 0;
		$collectFromStore = 0;
		$payInStoreAllowed = 0;
		$canModifyContactDetails = 0;
		$requiresDelivery = 0;

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			$stmt = $dbObj->prepare('
				SELECT 
					`sr`.`id`, 
					`sr`.`parentid`, 
					`sr`.`companycode`, 
					`sr`.`code`,
					`sr`.`shippingmethodcode`, 
					`sr`.`shippingzonecode`,
					`sr`.`productcode`, 
					`sr`.`groupcode`,
					`sr`.`info`, 
					`sr`.`rate`,
					`sr`.`ordervaluetype`,
				    `sr`.`orderminimumvalue`,
					`sr`.`ordermaximumvalue`, 
					`sr`.`ordervalueincludesdiscount`, 
					`sr`.`payinstoreallowed`, 
					`sr`.`taxcode`, 
					`sr`.`active`,
					IF (tr.rate IS NULL, "0.00", tr.rate), 
					`sm`.`usedefaultbillingaddress`, 
					`sm`.`usedefaultshippingaddress`,
					`sm`.`default`, 
					`sm`.`collectfromstore`, 
					`sm`.`canmodifycontactdetails`, 
					`sm`.`requiresdelivery`, 
					`sm`.`name`
				
				FROM `SHIPPINGRATES` sr
				LEFT JOIN `SHIPPINGMETHODS` sm ON `sm`.`code` = `sr`.`shippingmethodcode`
				LEFT JOIN `TAXRATES` tr ON `sr`.`taxcode` = `tr`.`code`
				WHERE `sr`.`shippingmethodcode` = ?
				AND `sr`.`code` = ?
				AND `sr`.`groupcode` = ?
				AND `sr`.`active` = 1
				AND (`sr`.`companycode` = ? OR `sr`.`companycode` = "")
				ORDER BY `sr`.`companycode` DESC
				LIMIT 1
			');

			if ($stmt)
			{
				if ($stmt->bind_param('ssss', $methodCode, $rateCode, $groupCode, $companyCode))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0)
							{
								if ($stmt->bind_result($shippingRateID, $parentID, $companyCode, $shippingRateCode, $shippingMethodCode,
									$shippingZoneCode, $productCode, $groupCode, $info, $shippingRates, $orderValueType,
									$orderMinValue, $orderMaxValue, $orderValueIncludesDiscount, $payInStoreAllowed, $taxCode,
									$isActive, $taxRate, $useDefaultBillingAddress, $useDefaultShippingAddress, $isDefault, $collectFromStore,
									$canModifyContactDetails, $requiresDelivery, $shippingMethodName))
								{
									if ($stmt->fetch())
									{
										$row = array();
										$row['ratecode'] = $shippingRateCode;
										$row['zonecode'] = $shippingZoneCode;
										$row['methodcode'] = $shippingMethodCode;
										$row['methodname'] = $shippingMethodName;
										$row['info'] = $info;
										$row['usedefaultbillingaddress'] = $useDefaultBillingAddress;
										$row['usedefaultshippingaddress'] = $useDefaultShippingAddress;
										$row['canmodifycontactdetails'] = $canModifyContactDetails;
										$row['requiresdelivery'] = $requiresDelivery;
										$row['isdefault'] = $isDefault;
										$row['cost'] = 0.00;
										$row['payinstoreallowed'] = $payInStoreAllowed;
										$row['sell'] = 0.00;
										$row['taxcode'] = $taxCode;
										$row['taxrate'] = $taxRate;
										$row['collectfromstore'] = $collectFromStore;
										$row['orderminvalue'] = $orderMinValue;
										$row['ordermaxvalue'] = $orderMaxValue;

										$resultArray['data'] = $row;
									}
									else
									{
										$resultArray['error'] = __FUNCTION__ . ' fetch ' . $dbObj->error;
									}
								}
								else
								{
									$resultArray['error'] = __FUNCTION__ . ' bind result ' . $dbObj->error;
								}
							}
							else
							{
								$resultArray['error'] = __FUNCTION__ . ' num rows ';
							}
						}
						else
						{
							$resultArray['error'] = __FUNCTION__ . ' store result ' . $dbObj->error;
						}
					}
					else
					{
						$resultArray['error'] = __FUNCTION__ . ' execute ' . $dbObj->error;
					}
				}
				else
				{
					$resultArray['error'] = __FUNCTION__ . ' bind params ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$resultArray['error'] = __FUNCTION__ . ' prepare ' . $dbObj->error;
			}

			$dbObj->close();
		}

		return $resultArray;
	}
}
