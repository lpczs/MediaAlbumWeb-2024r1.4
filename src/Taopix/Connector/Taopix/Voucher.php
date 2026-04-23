<?php

namespace Taopix\Connector\Taopix;

use DateTime;
use Taopix\API\AppData\API as AppDataAPI;
use Taopix\Core\Utils\TaopixUtils;
use Taopix\Connector\Taopix\Entity\Voucher as VoucherEntity;


class Voucher
{
	
	/**
	 * @var TaopixUtils
	 */
	protected $utils;

	/**
	 * @var Array
	 */
	private $allowedVoucherProperties = ['code','startdate','enddate','repeattype','discounttype','discountsection',
										'discountvalue','name','groupcode', 'productcode', 'productgroup','userid','minimumqty','maximumqty', 
										'lockqty','applicationmethod','maxqtytoapplydiscountto', 'minordervalue', 'minordervalueinctax', 'minordervalueincshipping','active'];
	/**
	 * @var Array
	 */
	private $validRepeatTypes = ['SINGLE','MULTI','MULTIONCECUSTOMER','MULTIONCEKEY'];

	/**
	 * @var Array
	 */
	private $vailidDiscountSections = ['PRODUCT','SHIPPING','TOTAL'];

	/**
	 * @var Array
	 */
	private $vailidDiscountTypes = ['VALUESET','VALUE','PERCENT', 'FOC', 'BOGOF', 'BOGPOFF', 'BOGVOFF'];

	/**
	 * @var Array
	 */
	private $vailidApplicationMethods = [TPX_VOUCHER_APPLY_EACH_MATCHING_PRODUCT, TPX_VOUCHER_APPLY_SPREAD_OVER_ORDER, TPX_VOUCHER_APPLY_LOWEST_PRICED, TPX_VOUCHER_APPLY_HIGHEST_PRICED];

	/**
	 * Sets the TaopixUtils instance
	 *
	 * @param TaopixUtils $pUtils TaopixUtils instance to set 
	 * @return Voucher Voucher instance.
	 */	
	public function setUtils(TaopixUtils $pUtils): Voucher
	{
		$this->utils = $pUtils;
		return $this;
	}

	/**
	 * Returns the TaopixUtils instance.
	 *
	 * @return TaopixUtils instance.
	 */		
	public function getUtils(): TaopixUtils
	{
		return $this->utils;
	}
	
	public function __construct(TaopixUtils $pUtils)
	{
		$this->setUtils($pUtils);
	}

	/**
	 * Creates a single voucher in Taopix
	 *
	 * @param array $pPayloadArray json decoded data from the createVoucher webhook
	 * @return array $returnArray consists of error and data keys. Data will be populated with the voucher details if no error has occurred
	 */
	public function createVoucher(array $pPayloadArray): array
	{
		$returnArray = array();
        $returnArray['error'] = '';
        $returnArray['data'] = array();
		
		$voucherCreated = false;
		$generateVoucherCode = false;

		$voucherCode = $pPayloadArray['code'] ?? '';
		$productGroup = $pPayloadArray['productgroup'] ?? '';
		$hasProductGroup = ($productGroup != '') ? true : false;
		
		$returnArray['error'] = $this->validateVoucherPayload($pPayloadArray, 'CREATE');


		if ($returnArray['error'] == '')
		{
			$startDate = new DateTime($pPayloadArray['startdate']);
			$endDate = new DateTime($pPayloadArray['enddate']);

			if ($startDate > $endDate)
			{
				$returnArray['error'] = 'Invalid end date';
			}
		}

		// if we have been provided with a voucher code make sure that it does not exceed 50 characters
		if ($voucherCode != '')
		{
			if (strlen($pPayloadArray['code']) > 50)
			{
				$returnArray['error'] = 'Voucher code must be 50 characters or less';
			}
		}
		else
		{ 
			$voucherPrefix = $pPayloadArray['voucherprefix'] ?? '';
			$voucherSuffix = $pPayloadArray['vouchersuffix'] ?? '';

			// check to make sure the combined length of the prefix and suffix do not exceed 38 characters.
			// this is to reserve 12 characters for the voucher code and to make sure it does not exceed the 50 character limit.
			if (strlen($voucherPrefix . $voucherSuffix) > 38)
			{
				$returnArray['error'] = 'Voucher prefix and suffix combined must be 38 characters or less';
			}
			
			if ($returnArray['error'] == '')
			{
				$generateVoucherCode = true;
				$voucherCode = $this->getUtils()->createRandomString(12, true);
				$voucherCode = trim(strtoupper($voucherPrefix . $voucherCode . $voucherSuffix));
				$pPayloadArray['code'] = $voucherCode;
			}
		}

		if ($returnArray['error'] == '')
		{
			// we need to map licensekey to groupcode due to the nature of how the data api maps columnnames to array keys
			if (array_key_exists('licensekey', $pPayloadArray))
			{
				$pPayloadArray['groupcode'] = $pPayloadArray['licensekey'];
				unset($pPayloadArray['licensekey']);
			}

			$newVoucherPropsArray = array_filter($pPayloadArray, function($k) {
				return in_array($k, $this->allowedVoucherProperties);
			}, ARRAY_FILTER_USE_KEY);

			$newVoucherPropsArray['hasproductgroup'] = $hasProductGroup;
			$voucherEntity = VoucherEntity::make($newVoucherPropsArray);
			
			$voucherProperties = $voucherEntity->getProperties();
			$voucherProperties['datecreated'] = '[currentdatetime]';
			unset($voucherProperties['id']);
			
			$voucherDataArray['action'] = 'INSERT';
			$voucherDataArray['schema'] = 'VOUCHERS';
			$voucherDataArray['extra'] = '';
			$voucherDataArray['fields'] = $voucherProperties;

			$dataAPI = new AppDataAPI();

			$authenticationResult = $dataAPI->authenticate();
			$result = $authenticationResult['error'];
			
			if ($result == '')
			{
				// attempt to create the voucher using the data api.
				$apiCallResult = $dataAPI->createVoucher($voucherDataArray);
				$insertResultSet = $apiCallResult['data']['resultset'][0];

				// if we  get an error we need to check the error code for duplicate entry
				if ($insertResultSet['error'] != '')
				{	
					// if we get a duplicated entry and we are auto generating a code we should try again with a new voucher code
					if ($insertResultSet['errorcode'] == '1062' && $generateVoucherCode)
					{
						$repeatCount = 0;
						
						while ($repeatCount <= 20)
						{
							$voucherDataArray['code'] = $voucherPrefix . $this->getUtils()->createRandomString(12, true) . $voucherSuffix;
							
							$apiCallResult = $dataAPI->createVoucher($voucherDataArray);
							$insertResultSet = $apiCallResult['data']['resultset'][0];

							if ($insertResultSet['errorcode'] == '0')
							{
								$voucherCreated = true;
								$repeatCount = 20;
							}

							$repeatCount++;
						}

						if (!$voucherCreated)
						{
							$returnArray['error'] = 'Unable to generate unique voucher code';
						}
					}
					else if ($insertResultSet['errorcode'] == '1062' && !$generateVoucherCode)
					{
						$returnArray['error'] = 'Voucher code already exists';
					}
				}
				else
				{
					$voucherCreated = true;
				}

				// if we have created the voucher and it is using a productgroup we must insert the product group link record
				if ($voucherCreated && $hasProductGroup)
				{
					$productGroupIDArray = $this->getUtils()->getProductGroupIDFromName($productGroup);
					$productGroupID = $productGroupIDArray['data'];

					if ($productGroupIDArray['error'] == '' && $productGroupID > 0 )
					{
						$linkRecordResultArray = $this->getUtils()->insertProductGroupLinkRecord($productGroupID, $voucherCode, TPX_PRODUCTGROUP_ASSIGNEETYPE_VOUCHER);
						$result = $linkRecordResultArray['error'];
					}
				}
			}
		}

		if ($voucherCreated)
		{
			$voucherProperties['productgroup'] = $productGroup;					
			$voucherReturnArray = array_filter($voucherProperties, function($k) {
				return in_array($k, $this->allowedVoucherProperties);
			}, ARRAY_FILTER_USE_KEY);

			$voucherReturnArray['licensekey'] = $voucherReturnArray['groupcode'];
			unset($voucherReturnArray['groupcode']);

			$returnArray['data'] = $voucherReturnArray;
		}
		
		return $returnArray;
	}

	/**
	 * Updates a single voucher in Taopix
	 *
	 * @param array $pPayloadArray json decoded data from the updateVoucher webhook
	 * @return array $returnArray consists of error and data keys. Data will be populated with the voucher details if no error has occurred
	 */
	public function updateVoucher($pPayloadArray)
	{
		$returnArray = array();
        $returnArray['error'] = '';
        $returnArray['data'] = array();

		$voucherUpdated = false;
		$voucherCode = $pPayloadArray['code'] ?? '';

		$productGroup = $pPayloadArray['productgroup'] ?? '';
		$hasProductGroup = ($productGroup != '') ? true : false;

		// check to make sure the voucher code has been supplied
		if ($voucherCode == '')
		{
			$returnArray['error'] = 'Voucher code has not been supplied';
		}

		$returnArray['error'] = $this->validateVoucherPayload($pPayloadArray, 'UPDATE');

		if ($returnArray['error'] == '')
		{
			// delete any pre-existing product group link records
			$linkDeleteResultArray = $this->getUtils()->deleteProductGroupLinkRecordsByAssigneeCode($voucherCode, TPX_PRODUCTGROUP_ASSIGNEETYPE_VOUCHER);

			$voucherArray = $this->getVoucherFromCode($voucherCode);

			if ($voucherArray['error'] == '')
			{
				$voucherEntity = VoucherEntity::make($voucherArray);

				// we need to map licensekey to groupcode due to the nature of how the data api maps columnnames to array keys
				if (array_key_exists('licensekey', $pPayloadArray))
				{
					$pPayloadArray['groupcode'] = $pPayloadArray['licensekey'];
					unset($pPayloadArray['licensekey']);
				}

				$newVoucherPropsArray = array_filter($pPayloadArray, function($k) {
					return in_array($k, $this->allowedVoucherProperties);
				}, ARRAY_FILTER_USE_KEY);
				
				$newVoucherPropsArray['hasproductgroup'] = $hasProductGroup;
				$voucherEntity->populateInstance($newVoucherPropsArray);

				$voucherProperties = $voucherEntity->getProperties();

				$startDate = new DateTime($voucherProperties['startdate']);
				$endDate = new DateTime($voucherProperties['enddate']);

				if ($startDate > $endDate)
				{
					$returnArray['error'] = 'Invalid end date';
				}
				
				// check to make sure that the discount value provided is correct for the discount type.
				// Free of charge and buy one get on free can have a discoutnt value of 0			
				if ($voucherProperties['discounttype'] != 'FOC' && $voucherProperties['discounttype'] != 'BOGOF')
				{
					if ($voucherProperties['discountvalue'] <= 0)
					{
						$returnArray['error'] = 'Please supply a valid discount value';
					}

					// if we are percentage off or buy one get percentage off to make sure the discount value is not greater than 100
					if ($voucherProperties['discounttype'] == 'PERCENT' || $voucherProperties['discounttype'] == 'BOGPOFF')
					{
						if ($voucherProperties['discountvalue'] > 100)
						{
							$returnArray['error'] = 'Please supply a valid discount value';
						}
					}
				}
				else
				{
					$voucherProperties['discountvalue'] = 0.00;
				}

				if ($returnArray['error'] == '')
				{
					unset($voucherProperties['id']);
					unset($voucherProperties['code']);
					
					$voucherDataArray['action'] = 'UPDATE';
					$voucherDataArray['schema'] = 'VOUCHERS';
					$voucherDataArray['extra'] = '';
					$voucherDataArray['ref'] = 'code';
					$voucherDataArray['refvalue'] = $voucherCode;
					$voucherDataArray['operator'] = '=';
					$voucherDataArray['fields'] = $voucherProperties;

					$dataAPI = new AppDataAPI();

					$authenticationResult = $dataAPI->authenticate();
					$result = $authenticationResult['error'];
					
					if ($result == '')
					{
						// attempt to update the voucher using the data api.
						$apiCallResult = $dataAPI->updateVoucher($voucherDataArray);
						$updateResultSet = $apiCallResult['data']['resultset'][0];

						// if we  get an error we need to check the error code for duplicate entry
						if ($updateResultSet['error'] == '')
						{	
							// if we have updated the voucher and it is using a productgroup we must insert the product group link record
							if ($hasProductGroup)
							{
								$productGroupIDArray = $this->getUtils()->getProductGroupIDFromName($productGroup);
								$productGroupID = $productGroupIDArray['data'];

								if ($productGroupIDArray['error'] == '' && $productGroupID > 0 )
								{
									$linkRecordResultArray = $this->getUtils()->insertProductGroupLinkRecord($productGroupID, $voucherCode, TPX_PRODUCTGROUP_ASSIGNEETYPE_VOUCHER);
									$result = $linkRecordResultArray['error'];
								}
							}
						}

						$voucherUpdated = true;
					}
				}
			}
			else if ($voucherArray['error'] == 'NORECORD')
			{
				$returnArray['error'] = 'No voucher with this code found';
			}
		}

		if ($voucherUpdated)
		{
			$voucherProperties['productgroup'] = $productGroup;			
			
			$voucherReturnArray = array_filter($voucherProperties, function($k) {
				return in_array($k, $this->allowedVoucherProperties);
			}, ARRAY_FILTER_USE_KEY);

			$voucherReturnArray['licensekey'] = $voucherReturnArray['groupcode'];
			unset($voucherReturnArray['groupcode']);

			$returnArray['data'] = $voucherReturnArray;


		}

		return $returnArray;
	}

	/**
	 * Delete a single voucher in Taopix
	 *
	 * @param array $pPayloadArray json decoded data from the deleteVoucher webhook
	 * @return array $returnArray consists of error key. Error will be populated with the did not exist or could not be deleted
	 */
	public function deleteVoucher($pPayloadArray)
	{
		$returnArray = array();
        $returnArray['error'] = '';
        $returnArray['data'] = array();

		$voucherCode = $pPayloadArray['code'] ?? '';

		// check to make sure a voucher code has been supplied
		if ($voucherCode == '')
		{
			$returnArray['error'] = 'Voucher code has not been supplied';
		}

		if ($returnArray['error'] == '')
		{
			$voucherDataArray['action'] = 'DELETE';
			$voucherDataArray['schema'] = 'VOUCHERS';
			$voucherDataArray['extra'] = '';
			$voucherDataArray['ref'] = 'code';
			$voucherDataArray['refvalue'] = $voucherCode;
			$voucherDataArray['operator'] = '=';

			$dataAPI = new AppDataAPI();

			$authenticationResult = $dataAPI->authenticate();
			$result = $authenticationResult['error'];
			
			if ($result == '')
			{
				// attempt to create the voucher using the data api.
				$apiCallResult = $dataAPI->deleteVoucher($voucherDataArray);
				$deleteResultSet = $apiCallResult['data']['resultset'][0];

				// if we get no error we must delete product group link record
				if ($deleteResultSet['error'] == '' && $deleteResultSet['affected'] == '1')
				{							
					// delete any pre-existing product group link records
					$linkDeleteResultArray = $this->getUtils()->deleteProductGroupLinkRecordsByAssigneeCode($voucherCode, TPX_PRODUCTGROUP_ASSIGNEETYPE_VOUCHER);
				}
				else
				{
					$returnArray['error'] = 'Voucher does not exist';
				}
			}
		}

		return $returnArray;
	}

	/**
	 * Validates the date strings from the payload are a valid date format that Taopix expects.
	 *
	 * @param string $date date string received from the voucher webhook payload
	 * @param string $format the format of the string that the $date param must match
	 * @return bool 
	 */
	private function validateVoucherDate(string $date, string $format = 'Y-m-d H:i:s'): bool
	{
		$d = \DateTime::createFromFormat($format, $date);
    	return $d && $d->format($format) === $date;
	}

	/**
	 * Validates the payload properties that Taopix expects.
	 *
	 * @param array $pPayloadArray received from the voucher webhook payload
	 * @param string $pMode what API methods is it CREATE/UPDATE some checks only need to be performed for a Create
	 * @return string error relating to the reason validation failed
	 */
	private function validateVoucherPayload(array $pPayloadArray, string $pMode): string
	{
		$error = '';

		if ($pMode == 'CREATE')
		{
			// check to make sure we have a vaild date for the start date.
			if (!array_key_exists('startdate', $pPayloadArray))
			{
				$error = 'No start date provided';
			}

			// check to make sure we have a vaild date for the start date.
			if (!array_key_exists('enddate', $pPayloadArray))
			{
				$error = 'No end date provided';
			}

			// check to make sure we have a discount value for the voucher.
			if (!array_key_exists('discountvalue', $pPayloadArray))
			{
				$error = 'No voucher discount value provided';
			}

			// check to make sure we have a name for the voucher.
			if (!array_key_exists('name', $pPayloadArray))
			{
				$error = 'No voucher name provided';
			}
		}

		// check to make sure only the productcode or the productgroup name has been sent.
		if (array_key_exists('productcode', $pPayloadArray) && array_key_exists('productgroup', $pPayloadArray))
		{
			$error = 'Only productcode or productgroup can be supplied. Both have been supplied';
		}
		
		// check to make sure we have a vaild date for the start date.
		if (array_key_exists('startdate', $pPayloadArray) && ! $this->validateVoucherDate($pPayloadArray['startdate']))
		{
			$error = 'Invalid start date format';
		}

		// check to make sure we have a vaild date for the end date.
		if (array_key_exists('enddate', $pPayloadArray) && ! $this->validateVoucherDate($pPayloadArray['enddate']))
		{
			$error = 'Invalid end date format';
		}
		
		// check to make sure that the discount value provided is correct for the discount type.
		// Free of charge and buy one get on free can have a discoutnt value of 0
		if (array_key_exists('discountvalue', $pPayloadArray)  && $pMode == 'CREATE')
		{				
			if (!array_key_exists('discounttype', $pPayloadArray))
			{
				$pPayloadArray['discounttype'] = 'VALUE';
			}

			if ($pPayloadArray['discounttype'] != 'FOC' && $pPayloadArray['discounttype'] != 'BOGOF')
			{
				if ($pPayloadArray['discountvalue'] <= 0)
				{
					$error = 'Please supply a valid discount value';
				}

				// if we are percentage off or buy one get percentage off to make sure the discount value is not greater than 100
				if ($pPayloadArray['discounttype'] == 'PERCENT' || $pPayloadArray['discounttype'] == 'BOGPOFF')
				{
					if ($pPayloadArray['discountvalue'] > 100)
					{
						$error = 'Please supply a valid discount value';
					}
				}
			}
		}

		// check to make sure the minorder value is not a negative.
		if (array_key_exists('repeattype', $pPayloadArray) && ! in_array(strtoupper($pPayloadArray['repeattype']), $this->validRepeatTypes))
		{
			$error = 'Invalid repeat type';
		}

		// check to make sure the minorder value is not a negative.
		if (array_key_exists('discounttype', $pPayloadArray) && ! in_array(strtoupper($pPayloadArray['discounttype']), $this->vailidDiscountTypes))
		{
			$error = 'Invalid discount type';
		}

		// check to make sure the minorder value is not a negative.
		if (array_key_exists('discountsection', $pPayloadArray) && ! in_array(strtoupper($pPayloadArray['discountsection']), $this->vailidDiscountSections))
		{
			$error = 'Invalid discount section';
		}

		// check to make sure the minorder value is not a negative.
		if (array_key_exists('applicationmethod', $pPayloadArray) && ! in_array($pPayloadArray['applicationmethod'], $this->vailidApplicationMethods))
		{
			$error = 'Invalid application method';
		}

		// check to make sure the minorder value is not a negative.
		if (array_key_exists('minordervalue', $pPayloadArray) && $pPayloadArray['minordervalue'] < 0)
		{
			$error = 'Minimum order value cannot be less than 0';
		}

		return $error;
	}

	/**
	 * Gets the voucher details from a given voucher code.
	 *
	 * @param string $pVoucherCode the voucher code we are looking upa voucher record off.
	 * @return array $resultArray an array containing all the voucher details
	 */
	private function getVoucherFromCode(string $pVoucherCode): array
    {
        $db = $this->getUtils()->getGlobalDBConnection();

		$resultArray = Array();
		$error = '';
        $id = 0;
		$dateCreated = '';
        $promotionCode = '';
		$companyCode = '';
		$owner = '';
		$promotionCode = '';
        $code = '';
		$type = TPX_VOUCHER_TYPE_PREPAID;
		$defaultDiscount = 0;
        $name = '';
        $description = '';
        $startDate = '';
        $endDate = '';
        $productCode = '';
        $groupCode = '';
        $userID = 0;
		$hasProductGroup = 0;
		$minQty = 0;
		$maxQty = 0;
		$lockQty = 0;
		$minimumValue = 0;
        $repeatType = '';
        $discountSection = '';
        $discountType = '';
        $discountValue = 0.00;
		$applicationMethod = TPX_VOUCHER_APPLY_EACH_MATCHING_PRODUCT;
		$maxQtyToApplyDiscountTo = 0;
        $sellprice = 0.00;
        $agentfee = 0.00;
		$redeemedUserId = 0;
		$redeemedDate = '';
		$sessionRef = 0;
		$orderID = 0;
		$isActive = 0;
		$minOrderValue = 0.00;
		$minOrderValueIncTax = 0;
		$minOrderValueIncShipping = 0;
        
		if ($db)
        {
            $stmt = $db->prepare('SELECT * FROM `VOUCHERS` WHERE `code` = ?');
            
			if ($stmt)
            {
                if ($stmt->bind_param('s', $pVoucherCode))
                {
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($id, $dateCreated, $companyCode, $owner, $promotionCode, $code, $type, $defaultDiscount, $name, $description, $startDate,
                                                $endDate, $productCode, $groupCode, $userID, $hasProductGroup,
                                                $minQty, $maxQty, $lockQty, $minimumValue, $repeatType, $discountSection, $discountType, $discountValue,
                                                $applicationMethod, $maxQtyToApplyDiscountTo, $sellprice, $agentfee, $redeemedUserId, $redeemedDate, $sessionRef, $orderID,
												$minOrderValue, $minOrderValueIncShipping, $minOrderValueIncTax, $isActive))
                                {
                                    if (!$stmt->fetch())
									{
										$error = __FUNCTION__ . ' fetch result error: ' . $db->error;
									}
                                }
								else
								{
									$error = __FUNCTION__ . ' bind result error: ' . $db->error;
								}
                            }
							else
							{
								$error = 'NORECORD';
							}
                        }
						else
						{
							$error = __FUNCTION__ . ' store result error: ' . $db->error;
						}
                    }
                }
				else
				{
					$error = __FUNCTION__ . ' bind result error: ' . $db->error;
				}

                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
			else
			{
				$error = __FUNCTION__ . ' prepare result error: ' . $db->error;
			}
            $db->close();
        }
		else
		{
			$error = __FUNCTION__ . ' connection  error: ' . $db->error;
		}
		
		$resultArray['error'] = $error;
		$resultArray['id'] = $id;
		$resultArray['datecreated'] = $dateCreated;
        $resultArray['promotioncode'] = $promotionCode;
		$resultArray['companycode'] = $companyCode;
		$resultArray['owner'] = $owner;
		$resultArray['promotioncode'] = $promotionCode;
        $resultArray['code'] = $code;
		$resultArray['type'] = $type;
		$resultArray['defaultdiscount'] = $defaultDiscount;
        $resultArray['name'] = $name;
        $resultArray['description'] = $description;
        $resultArray['startdate'] = $startDate;
        $resultArray['enddate'] = $endDate;
        $resultArray['productcode'] = $productCode;
        $resultArray['groupcode'] = $groupCode;
        $resultArray['userid'] = $userID;
		$resultArray['hasproductgroup'] = $hasProductGroup;
		$resultArray['minimumqty'] = $minQty;
		$resultArray['maximumqty'] = $maxQty;
		$resultArray['lockqty'] = $lockQty;
		$resultArray['minimumvalue'] = $minimumValue;
        $resultArray['repeattype'] = $repeatType;
        $resultArray['discountsection'] = $discountSection;
        $resultArray['discounttype'] = $discountType;
        $resultArray['discountvalue'] = $discountValue;
		$resultArray['applicationmethod'] = $applicationMethod;
		$resultArray['maxqtytoapplydiscountto'] = $maxQtyToApplyDiscountTo;;
        $resultArray['sellprice'] = $sellprice;
        $resultArray['agentfee'] = $agentfee;
		$resultArray['redeemeduserid'] = $redeemedUserId;
		$resultArray['redeemeddate'] = $redeemedDate;
		$resultArray['sessionref'] = $sessionRef;
		$resultArray['orderid'] = $orderID;
		$resultArray['minordervalue'] = $minOrderValue;
		$resultArray['minordervalueinctax'] = $minOrderValueIncTax;
		$resultArray['minordervalueincshipping'] = $minOrderValueIncShipping;
		$resultArray['active'] = $isActive;

		return $resultArray;
	}
}
