<?php

class convertProductCategoriesToGroups extends ExternalScript
{
	public function run()
	{	
		$error = '';
		$errorParam = '';

		$collectionResultArray = self::getProductsAndCategories();

		$error = $collectionResultArray['error'];
		
		if ($error === '')
		{
			$categoriesArray = $collectionResultArray['data'];

			if (count($categoriesArray) > 0)
			{
				$categoryNamesArray = array_keys($categoriesArray);
				$insertHeaderResultArray = self::insertNewProductGroupHeaders($categoryNamesArray);

				$error = $insertHeaderResultArray['error'];

				if ($error === '')
				{
					foreach($collectionResultArray['data'] as $groupName => $group)
					{
						$insertProductsResultArray = self::insertGroupProductRecords($group, $groupName);

						if ($insertProductsResultArray['error'] !== '')
						{
							//something has gone wrong, set the error variables and bail out
							$error = $insertProductsResultArray['error'];
							$errorParam = $insertProductsResultArray['errorparam'];
							break;
						}
					}
				}
				else
				{
					$errorParam = $insertHeaderResultArray['errorparam'];
				}

				if ($error === '')
				{
					$voucherCategoryResultArray = self::getVouchersByCategory($categoryNamesArray);

					if ($voucherCategoryResultArray['error'] === "")
					{
						$vouchersArray = $voucherCategoryResultArray['data'];

						foreach($vouchersArray as $groupName => $group)
						{
							$insertVoucherResultArray = self::insertProductGroupLinkRecords($groupName, $group);

							if ($insertVoucherResultArray['error'] !== '')
							{
								//something has gone wrong, set the error variables and bail out
								$error = $insertVoucherResultArray['error'];
								$errorParam = $insertVoucherResultArray['errorparam'];
								break;
							}
						}

						if ($error === '')
						{
							$updateVouchersResultArray = self::setVoucherTableFlag();

							if ($updateVouchersResultArray['error'] !== '')
							{
								$error = $updateVouchersResultArray['error'];
								$errorParam = $updateVouchersResultArray['errorparam'];
							}
						}
					}
					else
					{
						$error = $voucherCategoryResultArray['error'];
						$error = $voucherCategoryResultArray['errorparam'];
					}
				}
			}
			else
			{
				self::printMsg("No categories to convert to groups, continuing");
			}
		}
		else
		{
			$errorParam = $collectionResultArray['errorparam'];
		}

		$this->setResult($error . $errorParam);
	}

	/**
	 * prints a message to the screen.
	 *
	 * @param string $pMsg The message text.
	 */
	static private function printMsg($pMsg)
	{
		echo $pMsg . PHP_EOL;
	}

	/**
	 * Gets an array of categories and the product collections within them
	 * @return array Standard taopix return array with data in format of categorycode => collectioncode 
	 */
	static private function getProductsAndCategories()
	{
		$resultArray = UtilsObj::getReturnArray();
		$error = '';
		$errorParam = '';
		$categoryArray = Array();
		$collectionCode = '';
		$collectionCategoryCode = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$sql = 'SELECT DISTINCT `pcl`.`collectioncode`, `pcl`.`collectioncategorycode`
					FROM `PRODUCTCOLLECTIONLINK` AS `pcl`
					INNER JOIN `products` AS `p` ON (`p`.`code` = `pcl`.`productcode` AND `p`.`deleted` = 0)
					WHERE `collectioncategorycode` != ""';

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_result($collectionCode, $collectionCategoryCode))
				{
					if ($stmt->execute())
					{
						while($stmt->fetch())
						{
							$categoryArray[strtoupper(trim($collectionCategoryCode))][] = $collectionCode;
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

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
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
		$resultArray['data'] = $categoryArray;

		return $resultArray;
	}

	/**
	 * Inserts header records for product groups
	 * 
	 * @param Array $pGroupsToInsertArray consisting of the name of the product groups insert
	 * @return Array Standard taopix error array 
	 */
	static function insertNewProductGroupHeaders($pGroupsToInsertArray)
	{
		$resultArray = UtilsObj::getReturnArray();
		$error = '';
		$errorParam = '';
		$insertValues = '';
		$bindDataType = '';
		$firstLoop = true;

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$groupCount = count($pGroupsToInsertArray);

			for ($i = 0; $i < $groupCount; $i++)
			{
				if ($firstLoop !== true)
				{
					$insertValues .= ", ";
				}
				else
				{
					$firstLoop = false;
				}

				$insertValues .= " (NOW(), '', ?, 1)";
				
				$bindDataType .= "s";
			}

			$sql = 'INSERT INTO `productgroupheader` (`datecreated`, `companycode`, `name`, `active`)
					VALUES' . $insertValues;

			if ($stmt = $dbObj->prepare($sql))
			{
				$bindOK = DatabaseObj::bindParams($stmt, $bindDataType, $pGroupsToInsertArray);

				if ($bindOK)
				{
					if (!$stmt->execute())
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' execute error: ' . $dbObj->error;
					}
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' bindparam error: ' . $dbObj->error;
				}

				$stmt->close();
				$stmt = null;
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' connect error: ' . $dbObj->error;
		}
		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;

		return $resultArray;
	}

	/**
	 * Inserts product records for passed array
	 * @param Array $pGroupArray Array of product collection codes to insert records for
	 * @param string $pGroupName Name of group to insert records for
	 * @return array Standard taopix error array
	 */
	static function insertGroupProductRecords($pGroupArray, $pGroupName)
	{
		$resultArray = UtilsObj::getReturnArray();
		$error = '';
		$errorParam = '';
		$insertValues = '';
		$bindValues = array();
		$bindDataTypes = '';
		$firstLoop = true;

		$productGroupIDArray = self::getProductGroupIDFromName($pGroupName);

		if ($productGroupIDArray['error'] === '')
		{
			$productGroupID = $productGroupIDArray['data'];
			$dbObj = DatabaseObj::getGlobalDBConnection();

			if ($dbObj)
			{
				$groupCount = count($pGroupArray);

				for ($i = 0; $i < $groupCount; $i++)
				{
					if (! $firstLoop)
					{
						$insertValues .= ", ";
					}
					else
					{
						$firstLoop = false;
					}

					$insertValues .= " (NOW(), ?, ?, '*')";
					$bindValues[] = $productGroupID;
					$bindValues[] = $pGroupArray[$i];
					$bindDataTypes .= "is";
				}

				$sql = "INSERT INTO `productgroupproducts` (`datecreated`, `productgroupid`, `collectioncode`, `productcode`) VALUES " . $insertValues;

				if ($stmt = $dbObj->prepare($sql))
				{
					$bindOk = DatabaseObj::bindParams($stmt, $bindDataTypes, $bindValues);

					if ($bindOk)
					{
						if (! $stmt->execute())
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' execute error: ' . $dbObj->error;
						}
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' bind error: ' . $dbObj->error;
					}

					$stmt->close();
					$stmt = null;
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
				}

				$dbObj->close();
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' connect error: ' . $dbObj->error;
			}
		}
		else
		{
			$error = $productGroupIDArray['error'];
			$errorParam = $productGroupIDArray['errorparam'];
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;
		
		return $resultArray;
	}

	/**
	 * Gets the group header record ID from the passed name
	 * @param string $pName The name of the group
	 * @return array standard taopix return array with the group id in the data key
	 */
	static function getProductGroupIDFromName($pName)
	{
		$resultArray = UtilsObj::getReturnArray();
		$error = '';
		$errorParam = '';
		$resultID = 0;

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$sql = 'SELECT `id` FROM `productgroupheader` WHERE `name` = ?';
			$bindDataType = "s";
			
			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_param($bindDataType, $pName))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows == 1)
							{
								if ($stmt->bind_result($resultID))
								{
									if ($stmt->fetch())
                                    {
										$resultArray['data'] = $resultID;
									}
									else
									{
										$error = 'str_DatabaseError';
										$errorParam = __FUNCTION__ . ' fetch error: ' . $dbObj->error;
									}
								}
								else
								{
									$error = 'str_DatabaseError';
									$errorParam = __FUNCTION__ . ' bindresult error: ' . $dbObj->error;
								}
							}
							else
							{
								$error = 'str_DatabaseError';
								$errorParam = __FUNCTION__ . ' numrows error: ' . $dbObj->error;			
							}
						}
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' storeresult error: ' . $dbObj->error;
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
					$errorParam = __FUNCTION__ . ' bindparam error: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' connect error: ' . $dbObj->error;
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;

		return $resultArray;
	}

	/**
	 * gets a list of vouchers by the category they are assigned to
	 * @param array $pCategories The categories we want to search for
	 * @return array standard taopix return array with categories in data key in format of categorycode => vouchercode
	 */
	static function getVouchersByCategory($pCategories)
	{
		$resultArray = UtilsObj::getReturnArray();
		$error = '';
		$errorParam = '';
		$categoryArray = Array();
		$categoryCode = '';
		$voucherCode = '';
		$firstLoop = true;
		$bindDataTypes = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$inClause = '';
			$categoryCount = count($pCategories);

			for ($i = 0; $i < $categoryCount; $i++)
			{
				if ($firstLoop === false)
				{
					$inClause.= ', ';
				}
				else
				{
					$firstLoop = false;
				}
				
				$inClause .= '?';
				$bindDataTypes .= 's';
			}

			$sql = "SELECT `code`, `productcategorycode` FROM `vouchers` 
					WHERE `productcategorycode` IN (" . $inClause . ")";

			if ($stmt = $dbObj->prepare($sql))
			{
				$bindOK = DatabaseObj::bindParams($stmt, $bindDataTypes, $pCategories);

				if ($bindOK)
				{
					if ($stmt->bind_result($voucherCode, $categoryCode))
					{
						if ($stmt->execute())
						{
							while($stmt->fetch())
							{
								$categoryArray[$categoryCode][] = $voucherCode;
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
						$errorParam = __FUNCTION__ . ' bindresult error: ' . $dbObj->error;
					}
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' bindparam error: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' connect error: ' . $dbObj->error;
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;
		$resultArray['data'] = $categoryArray;

		return $resultArray;
	}

	/**
	 * updates voucher table with group flag
	 * @return array standard taopix error array
	 */
	static function setVoucherTableFlag()
	{
		$resultArray = UtilsObj::getReturnArray();
		$error = '';
		$errorParam = '';

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$sql = 'UPDATE `vouchers` 
				SET `productcategorycode` = "", `productcategoryname` = "", `hasproductgroup` = 1
				WHERE `productcategorycode` != ""';

			if ($stmt = $dbObj->prepare($sql))
			{
				if (! $stmt->execute())
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' execute error: ' . $dbObj->error;
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
			$errorParam = __FUNCTION__ . ' connect error: ' . $dbObj->error;
		}
		
		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;

		return $resultArray;
	}

	/**
	 * inserts product group link records for passed group and vouchers
	 * @param string $pGroupname The name of the group to assign the vouchers to
	 * @param array $pGroup an array containing the vouchers to assign records to
	 * @return array standard taopix error array
	 */
	static function insertProductGroupLinkRecords($pGroupName, $pGroup)
	{
		$resultArray = UtilsObj::getReturnArray();
		$error = '';
		$errorParam = '';
		$firstLoop = true;
		$insertValues = '';
		$bindDataTypes = '';
		$bindValuesArray = array();

		$productGroupIDArray = self::getProductGroupIDFromName($pGroupName);

		if ($productGroupIDArray['error'] === '')
		{
			$productGroupID = $productGroupIDArray['data'];

			// if we have a productgroupid of 0 the group has no valid products and thus we don't want to insert the link records
			if ($productGroupID !== 0)
			{
				$dbObj = DatabaseObj::getGlobalDBConnection();

				if ($dbObj)
				{
					$voucherCount = count($pGroup);
	
					for ($i = 0; $i < $voucherCount; $i++)
					{
						if ($firstLoop === false)
						{
							$insertValues .= ", ";
						}
						else
						{
							$firstLoop = false;
						}
	
						$insertValues .= "(NOW(), ?, ?, ?)";
						$bindDataTypes .= "isi";
						$bindValuesArray[] = $productGroupID;
						$bindValuesArray[] = $pGroup[$i];
						$bindValuesArray[] = TPX_PRODUCTGROUP_ASSIGNEETYPE_VOUCHER;
					}
					
					$sql = "INSERT INTO `productgrouplink` (`datecreated`, `productgroupid`, `assigneecode`, `assigneetype`)
							VALUES " . $insertValues;
	
					if ($stmt = $dbObj->prepare($sql))
					{
						$bindOK = DatabaseObj::bindParams($stmt, $bindDataTypes, $bindValuesArray);
	
						if ($bindOK)
						{
							if (! $stmt->execute())
							{
								$error = 'str_DatabaseError';
								$errorParam = __FUNCTION__ . ' execute error: ' . $dbObj->error;
							}
						}
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' bindparam error: ' . $dbObj->error;
						}
	
						$stmt->close();
						$stmt = null;
					}
					else
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' prepare error: ' . $dbObj->error;
					}
	
					$dbObj->close();
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' connect error: ' . $dbObj->error;
				}
			}
		}
		else
		{
			$error = $productGroupIDArray['error'];
			$errorParam = $productGroupIDArray['errorparam'];
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;

		return $resultArray;
	}
}
