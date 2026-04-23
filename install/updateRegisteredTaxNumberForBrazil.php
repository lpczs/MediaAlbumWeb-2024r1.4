<?php

class updateRegisteredTaxNumberForBrazil
{
	static function run()
	{
		// remove the script timeout
		set_time_limit(0);

		$resultArray = array();
		$licenseKeyCache = array();
	
		$error = '';
		$errorParam = '';

		$getOrderHeaderDataResult = self::getOrderHeaderData();

		if ($getOrderHeaderDataResult['error'] == '')
		{
			foreach($getOrderHeaderDataResult['data'] as $orderHeaderData)
			{
				$paramDataArray = array('id' => $orderHeaderData['id'], 'ordernumber' => $orderHeaderData['ordernumber']);
				$getCustomerSettingsResult = array('error' => '', 'errorparam' => '', 'data' => array());
				$licenseKey = '';

				if ($error == '')
				{
					// check the customer's settings

					if ($orderHeaderData['uselicensekeyforbillingaddress'] == 1)
					{
						// use license key settings

						if (! isset($licenseKeyCache[$orderHeaderData['groupcode']]))
						{
							// get license key data from database

							$getLicenseKeySettingsResult = self::getLicenseKeySettings($orderHeaderData['groupcode']);

							if ($getLicenseKeySettingsResult['error'] == '')
							{
								$licenseKey = $getLicenseKeySettingsResult['data'];

								$paramDataArray['registeredtaxnumbertype'] = $licenseKey['registeredtaxnumbertype'];
								$paramDataArray['registeredtaxnumber'] = $licenseKey['registeredtaxnumber'];

								// cache license key data
								$licenseKeyCache[$orderHeaderData['groupcode']] = $licenseKey;
							}
							else
							{
								$error = $getLicenseKeySettingsResult['error'];
								$errorParam = $getLicenseKeySettingsResult['errorparam'];
							}
						}
						else
						{
							// get license key data from cache

							$licenseKey = $licenseKeyCache[$orderHeaderData['groupcode']];

							$paramDataArray['registeredtaxnumbertype'] = $licenseKey['registeredtaxnumbertype'];
							$paramDataArray['registeredtaxnumber'] = $licenseKey['registeredtaxnumber'];
						}
					}
					else
					{
						// use customer account settings
						$paramDataArray['registeredtaxnumbertype'] = $orderHeaderData['registeredtaxnumbertype'];
						$paramDataArray['registeredtaxnumber'] = $orderHeaderData['registeredtaxnumber'];
					}

				}

				if ($error == '')
				{
					$updateOrderHeaderDataResult = self::updateOrderHeaderData($paramDataArray);

					if ($updateOrderHeaderDataResult['error'] != '')
					{
						$error = $updateOrderHeaderDataResult['error'];
						$errorParam = $updateOrderHeaderDataResult['errorparam'];
					}
				}
			}
		}
		else
		{
			$error = $getOrderHeaderDataResult['error'];
			$errorParam = $getOrderHeaderDataResult['errorparam'];
		}
		
		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;
		
		return $resultArray;
	}


	/**
	 * getOrderHeaderData
	 *  - get order header data for orders where the billing country is Brazil so we can update the tax fields
	 */
	static function getOrderHeaderData()
	{
		echo "Retrieving order header data for update...\n";

		$resultArray = array('error' => '', 'errorparam' =>'', 'data' => array());
		$error = '';
		$errorParam = '';
		$orderListArray = array();
	
		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$sql = "SELECT oh.`id`, oh.`userid`, oh.`ordernumber`,
					u.`groupcode`, u.`registeredtaxnumbertype`, u.`registeredtaxnumber` , u.`uselicensekeyforbillingaddress`
					FROM `ORDERHEADER` oh
					LEFT JOIN `USERS` u ON oh.`userid` = u.`id`
					WHERE
						oh.`billingcustomercountrycode` = 'BR'
					AND oh.`billingcustomerregisteredtaxnumber` = 0
					AND oh.`billingcustomeraddress1` = u.`address1`
					AND oh.`billingcontactfirstname` = u.`contactfirstname`
					AND oh.`billingcontactlastname` = u.`contactlastname`
					AND u.`registeredtaxnumber` != ''";

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_result($orderHeaderId, $userID, $orderNumber, $groupCode, $registeredTaxNumberType, $registeredTaxNumber, $useLicenseKeyForBillingAddress))
				{
					if ($stmt->execute())
					{
						while($stmt->fetch())
						{
							$orderHeader = array();
							$orderHeader['id'] = $orderHeaderId;
							$orderHeader['userid'] = $userID;
							$orderHeader['ordernumber'] = $orderNumber;
							$orderHeader['groupcode'] = $groupCode;
							$orderHeader['registeredtaxnumbertype'] = $registeredTaxNumberType;
							$orderHeader['registeredtaxnumber'] = $registeredTaxNumber;
							$orderHeader['uselicensekeyforbillingaddress'] = $useLicenseKeyForBillingAddress;

							$orderListArray[$orderHeaderId] = $orderHeader;
						}
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' execute error: ' . $dbObj->error;
					}
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' bind result error: ' . $dbObj->error;
				}
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
			}

			if ($stmt)
			{
				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' connection error: ' . $dbObj->error;
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;
		$resultArray['data'] = $orderListArray;

		echo "...Complete\n\n";

		return $resultArray;
	}

	/**
	 * getCustomerSettings
	 *  - get a customers license key settins
	 * @param string $pGroupCode License key groupcode
	 */
	static function getLicenseKeySettings($pGroupCode)
	{
		echo "Retrieving license key data for update...\n";

		$resultArray = array('error' => '', 'errorparam' =>'', 'data' => array());
		$error = '';
		$errorParam = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$sql = "SELECT `registeredtaxnumbertype`, `registeredtaxnumber` FROM `LICENSEKEYS`
					WHERE `groupcode` = ?";

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_param('s', $pGroupCode))
				{
					if ($stmt->bind_result($registeredTaxNumberType, $registeredTaxNumber))
					{
						if ($stmt->execute())
						{
							if ($stmt->store_result())
							{
								if ($stmt->num_rows == 1)
								{
									if ($stmt->fetch())
									{
										$key = array();
										$key['registeredtaxnumbertype'] = $registeredTaxNumberType;
										$key['registeredtaxnumber'] = $registeredTaxNumber;

										$resultArray['data'] = $key;
									}
									else
									{
										$error = 'str_DatabaseError';
										$errorParam = __FUNCTION__ . ' fetch result error: ' . $dbObj->error;
									}
								}
								else
								{
									$error = 'str_DatabaseError';
									$errorParam = __FUNCTION__ . ' no records returned.';
								}
							}
							else
							{
								$error = 'str_DatabaseError';
								$errorParam = __FUNCTION__ . ' store result: ' . $dbObj->error;
							}
						}
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' execute error: ' . $dbObj->error;
						}
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' bind result error: ' . $dbObj->error;
					}
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' bind param error: ' . $dbObj->error;
				}
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
			}

			if ($stmt)
			{
				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' connection error: ' . $dbObj->error;
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;

		echo "...Complete\n\n";

		return $resultArray;
	}

	static function updateOrderHeaderData($pParamArray)
	{
		echo "Updating orderheader data order " . $pParamArray['ordernumber'] . "\n";

		$resultArray = array('error' => 0, 'errorparam' =>'');
		$error = 0;
		$errorParam = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$sql = "UPDATE `ORDERHEADER` SET `billingcustomerregisteredtaxnumbertype` = ?, `billingcustomerregisteredtaxnumber` = ? WHERE `id` = ?";

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_param('isi', $pParamArray['registeredtaxnumbertype'], $pParamArray['registeredtaxnumber'], $pParamArray['id']))
				{
					if (! $stmt->execute())
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' execute error: ' . $dbObj->error;
					}

					$stmt->free_result();
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' bind_param error: ' . $dbObj->error;
				}
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
			}

		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' connection error: ' . $dbObj->error;
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;

		return $resultArray;
	}
}
?>