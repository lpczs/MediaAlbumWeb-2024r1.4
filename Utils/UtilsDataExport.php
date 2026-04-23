<?php

require_once('../Utils/UtilsMetaData.php');

class DataExportObj
{
	static function getExportSummary($pStartDate, $pEndDate, $pDateType, $pFilterType, $pFilterValue, $conditionParam, $pCompanyCode = '')
	{
		global $gSession;

		$reportList = Array();
		$tempArray = Array();

	    if ($gSession['userdata']['companycode'] != '')
	    {
	    	$pCompanyCode = $gSession['userdata']['companycode'];
	    }

	    $brandCode = ($gSession['userdata']['webbrandcode'] != '') ? $gSession['userdata']['webbrandcode'] : '';

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			// first build temporary array with order IDs
			$tempArray = self::selectExportIdListByDate($pStartDate, $pEndDate, $pDateType, $pFilterType, $pFilterValue, $pCompanyCode);

			// now get corresponding data from the other two tables
			foreach ($tempArray as $value)
			{
				UtilsObj::resetPHPScriptTimeout(30);
				// get data from orderheader table
				if ($pCompanyCode == '')
				{
					if ($brandCode == '')
					{
						if ($stmt = $dbObj->prepare('SELECT `ordernumber`, `orderdate`, `outputtimestamp`, `shippeddate`, `productname`, oi.qty, `webbrandcode`, `total`,
						`currencysymbol`, `currencysymbolatfront`, `currencydecimalplaces`,
						`billingcustomertelephonenumber`, `billingcustomeremailaddress`, `billingcontactfirstname`, `billingcontactlastname`,
						`shippingcustomertelephonenumber`, `shippingcustomeremailaddress`, `shippingcontactfirstname`, `shippingcontactlastname`
						FROM `ORDERHEADER` oh
						JOIN `ORDERITEMS` oi ON (oh.id = oi.orderid)
						JOIN `ORDERSHIPPING` os ON (oi.orderid = os.orderid)
						WHERE oh.id = ? ' . $conditionParam))
						{
							$stmt->bind_param('i', $value);
						}
					}
					else
					{
						if ($stmt = $dbObj->prepare('SELECT `ordernumber`, `orderdate`, `outputtimestamp`, `shippeddate`, `productname`, oi.qty, `webbrandcode`, `total`,
						`currencysymbol`, `currencysymbolatfront`, `currencydecimalplaces`,
						`billingcustomertelephonenumber`, `billingcustomeremailaddress`, `billingcontactfirstname`, `billingcontactlastname`,
						`shippingcustomertelephonenumber`, `shippingcustomeremailaddress`, `shippingcontactfirstname`, `shippingcontactlastname`
						FROM `ORDERHEADER` oh
						JOIN `ORDERITEMS` oi ON (oh.id = oi.orderid)
						JOIN `ORDERSHIPPING` os ON (oi.orderid = os.orderid)
						WHERE oh.id = ? AND oh.webbrandcode = ? ' . $conditionParam))
						{
							$stmt->bind_param('is', $value, $brandCode);
						}
					}
				}
				else
				{
					if ($brandCode == '')
					{
						if ($stmt = $dbObj->prepare('SELECT `ordernumber`, `orderdate`, `outputtimestamp`, `shippeddate`, `productname`, oi.qty, `webbrandcode`, `total`,
						`currencysymbol`, `currencysymbolatfront`, `currencydecimalplaces`,
						`billingcustomertelephonenumber`, `billingcustomeremailaddress`, `billingcontactfirstname`, `billingcontactlastname`,
						`shippingcustomertelephonenumber`, `shippingcustomeremailaddress`, `shippingcontactfirstname`, `shippingcontactlastname`
						FROM `ORDERHEADER` oh
						JOIN `ORDERITEMS` oi ON (oh.id = oi.orderid)
						JOIN `ORDERSHIPPING` os ON (oi.orderid = os.orderid)
						WHERE oh.id = ? AND oi.currentcompanycode = ? ' . $conditionParam))
						{
							$stmt->bind_param('is', $value, $pCompanyCode);
						}
					}
					else
					{
						if ($stmt = $dbObj->prepare('SELECT `ordernumber`, `orderdate`, `outputtimestamp`, `shippeddate`, `productname`, oi.qty, `webbrandcode`, `total`,
						`currencysymbol`, `currencysymbolatfront`, `currencydecimalplaces`,
						`billingcustomertelephonenumber`, `billingcustomeremailaddress`, `billingcontactfirstname`, `billingcontactlastname`,
						`shippingcustomertelephonenumber`, `shippingcustomeremailaddress`, `shippingcontactfirstname`, `shippingcontactlastname`
						FROM `ORDERHEADER` oh
						JOIN `ORDERITEMS` oi ON (oh.id = oi.orderid)
						JOIN `ORDERSHIPPING` os ON (oi.orderid = os.orderid)
						WHERE oh.id = ? AND oi.currentcompanycode = ? AND oh.webbrandcode = ? ' . $conditionParam))
						{
							$stmt->bind_param('iss', $value, $pCompanyCode, $brandCode);
						}
					}
				}

				if ($stmt->bind_result($ordernumber, $orderdate, $outputtimestamp, $shippeddate, $productname, $qty, $webBrandCode, $total,
											$currencysymbol, $currencysymbolatfront, $currencydecimalplaces,
											$billingcustomertelephonenumber, $billingcustomeremailaddress, $billingcontactfirstname, $billingcontactlastname,
											$shippingcustomertelephonenumber, $shippingcustomeremailaddress, $shippingcontactfirstname, $shippingcontactlastname))
				{
					if ($stmt->execute())
					{
						while ($stmt->fetch())
						{
							$reportItem['ordernumber'] = $ordernumber;
							$reportItem['orderdate'] = $orderdate;
							$reportItem['outputtimestamp'] = $outputtimestamp;
							$reportItem['shippeddate'] = $shippeddate;
							$reportItem['productname'] = LocalizationObj::getLocaleString($productname, $gSession['browserlanguagecode'], true);
							$reportItem['qty'] = $qty;
							$reportItem['webbrandcode'] = $webBrandCode;
							$reportItem['total'] = $total;
							$reportItem['currencysymbol'] = $currencysymbol;
							$reportItem['currencysymbolatfront'] = $currencysymbolatfront;
							$reportItem['currencydecimalplaces'] = $currencydecimalplaces;
							$reportItem['billingtelephonenumber'] = $billingcustomertelephonenumber;
							$reportItem['billingemailaddress'] = $billingcustomeremailaddress;
							$reportItem['billingcontactname'] = $billingcontactfirstname . ' ' . $billingcontactlastname;
							$reportItem['shippingtelephonenumber'] = $shippingcustomertelephonenumber;
							$reportItem['shippingemailaddress'] = $shippingcustomeremailaddress;
							$reportItem['shippingcontactname'] = $shippingcontactfirstname . ' ' . $shippingcontactlastname;
							$reportList[] = $reportItem;
						}
					}
				}

				$stmt->free_result();
				$stmt->close();
			}

			$dbObj->close();
		}

		return $reportList;
	}


	static function getExportCompleteShort($pStartDate, $pEndDate, $pDateType, $pFilterType, $pFilterValue)
	{
		global $gSession;
		// return an array containing

		$reportList = Array();
		$tempArray = Array();

		$dbObj = DatabaseObj::getConnection();
		if ($dbObj)
		{

			// first build temporary array with order IDs
			$tempArray = self::selectExportIdListByDate($pStartDate, $pEndDate, $pDateType, $pFilterType, $pFilterValue);

			// now get corresponding data from the other two tables
			foreach ($tempArray as $value)
			{

				// get data from orderheader table
				if ($stmt = $dbObj->prepare('SELECT `ordernumber`, `orderdate`, `outputtimestamp`, `shippeddate`, `productname`, oi.qty, `webbrandcode`, `total`,
						`currencysymbol`, `currencysymbolatfront`, `currencydecimalplaces`,
						`billingcustomertelephonenumber`, `billingcustomeremailaddress`, `billingcontactfirstname`, `billingcontactlastname`,
						`shippingcustomertelephonenumber`, `shippingcustomeremailaddress`, `shippingcontactfirstname`, `shippingcontactlastname`
						FROM `ORDERHEADER` oh
						JOIN `ORDERITEMS` oi ON (oh.id = oi.orderid)
						JOIN `ORDERSHIPPING` os ON (oi.orderid = os.orderid)
						WHERE oh.id = ? '))
				{
				   if ($stmt->bind_param('i', $value))
				   {
						if ($stmt->bind_result($ordernumber, $orderdate, $outputtimestamp, $shippeddate, $productname, $qty, $webBrandCode, $total,
												$currencysymbol, $currencysymbolatfront, $currencydecimalplaces,
												$billingcustomertelephonenumber, $billingcustomeremailaddress, $billingcontactfirstname, $billingcontactlastname,
												$shippingcustomertelephonenumber, $shippingcustomeremailaddress, $shippingcontactfirstname, $shippingcontactlastname))
						{
							if ($stmt->execute())
							{
								while ($stmt->fetch())
								{
									$reportItem['ordernumber'] = $ordernumber;
									$reportItem['orderdate'] = $orderdate;
									$reportItem['outputtimestamp'] = $outputtimestamp;
									$reportItem['shippeddate'] = $shippeddate;
									$reportItem['productname'] = LocalizationObj::getLocaleString($productname, $gSession['browserlanguagecode'], true);
									$reportItem['qty'] = $qty;
									$reportItem['webbrandcode'] = $webBrandCode;
									$reportItem['total'] = $total;
									$reportItem['currencysymbol'] = $currencysymbol;
									$reportItem['currencysymbolatfront'] = $currencysymbolatfront;
									$reportItem['currencydecimalplaces'] = $currencydecimalplaces;
									$reportItem['billingtelephonenumber'] = $billingcustomertelephonenumber;
									$reportItem['billingemailaddress'] = $billingcustomeremailaddress;
									$reportItem['billingcontactname'] = $billingcontactfirstname . ' ' . $billingcontactlastname;
									$reportItem['shippingtelephonenumber'] = $shippingcustomertelephonenumber;
									$reportItem['shippingemailaddress'] = $shippingcustomeremailaddress;
									$reportItem['shippingcontactname'] = $shippingcontactfirstname . ' ' . $shippingcontactlastname;
									$reportList[] = $reportItem;
								}
							}
						}
					}
					$stmt->free_result();
					$stmt->close();
				}

			}
			$dbObj->close();
		}

		return $reportList;
	}


    static function generateOrderExportData($pIDList, $pIDListIsHeader, $pLanguageCode, $pIncludePaymentData, $pCompanyCode)
	{
		global $gSession;

		$resultArray = Array();
		$tempArray = Array();
		$reportItem = Array();

		$languageCode = $pLanguageCode;

		// if the supplied company code is *NONE* then we always ignore it (used when exporting via a trigger as the company code is not relevant)
		if ($pCompanyCode == '*NONE*')
		{
			$pCompanyCode = '';
		}
		else
		{
			if (($gSession['userdata']['companycode'] != '') && ($gSession['userdata']['usertype'] != TPX_LOGIN_CUSTOMER))
			{
				$pCompanyCode = $gSession['userdata']['companycode'];
			}
	    }

	    $localisedFields = Array('header' => Array('currencyname', 'paymentmethodname', 'vouchername'),
								 'items' => Array('productcollectionname' , 'productname', 'productinfo', 'taxname'),
								 'shipping' => Array('shippingmethodname', 'shippingratetaxname', 'shippingrateinfo'),
								 'components' => Array('componentname', 'componentcategoryname', 'componentdescription', 'componentinfo', 'componentpriceinfo', 'componenttaxname')
								 );
		// are the IDs header IDs or Item IDs?
		if ($pIDListIsHeader)
		{
			if ($pCompanyCode == '')
			{
				$tables = Array('header' => 'SELECT * FROM ORDERHEADER WHERE id = ?',
					'items' => 'SELECT * FROM ORDERITEMS WHERE orderid = ?',
					'shipping' => 'SELECT * FROM ORDERSHIPPING WHERE orderid = ?');
			}
			else
			{
				$tables = Array('header' => 'SELECT * FROM ORDERHEADER WHERE id = ?',
					'items' => 'SELECT * FROM ORDERITEMS WHERE orderid = ? AND currentcompanycode = ?',
					'shipping' => 'SELECT * FROM ORDERSHIPPING WHERE orderid = ?');
			}

			if ($pIncludePaymentData == 1)
			{
				$tables["ccilog"] = 'SELECT ccilog.* FROM ORDERITEMS LEFT JOIN CCILOG USING (orderid) WHERE orderid = ?';
			}
		}
		else
		{
			if ($pCompanyCode == '')
			{
				$tables = Array('header' => 'SELECT * FROM ORDERHEADER oh WHERE oh.id = (SELECT oi.orderid FROM ORDERITEMS oi where oi.id = ?)',
					'items' => 'SELECT * FROM ORDERITEMS WHERE `id` = ?',
					'shipping' => 'SELECT * FROM ORDERSHIPPING WHERE `orderid` = (SELECT oi.orderid FROM ORDERITEMS oi where oi.id = ?)');
			}
			else
			{
				$tables = Array('header' => 'SELECT * FROM ORDERHEADER oh WHERE oh.id = (SELECT oi.orderid FROM ORDERITEMS oi WHERE oi.id = ?)',
					'items' => 'SELECT * FROM ORDERITEMS WHERE `id` = ? AND `currentcompanycode` = ?',
					'shipping' => 'SELECT * FROM ORDERSHIPPING WHERE `orderid` = (SELECT oi.orderid FROM ORDERITEMS oi where oi.id = ?)');
			}

			if ($pIncludePaymentData == 1)
			{
				$tables["ccilog"] = 'SELECT ccilog.* FROM ORDERITEMS oi LEFT JOIN ccilog USING (orderid) WHERE oi.id = ?';
			}
		}

		$componentsStatement = 'SELECT * FROM ORDERITEMCOMPONENTS WHERE orderitemid = ?';

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			// prepare the statement that filters out records which aren't for the specified company

			$bindParamOk = false;
			if ($pCompanyCode != '')
			{
				// are the IDs header IDs or Item IDs?
				if ($pIDListIsHeader)
				{
					$statement = 'SELECT count(*) as recCount FROM ORDERITEMS WHERE (`orderid` = ?) AND (`currentcompanycode` = ?)';
				}
				else
				{
					$statement = 'SELECT count(*) as recCount FROM ORDERITEMS WHERE (`id` = ?) AND (`currentcompanycode` = ?)';
				}
				if ($orderIDSForCompanyStmt = $dbObj->prepare($statement))
				{
					$bindParamOk = true;
				}
			}

			// build the data for each id
			foreach ($pIDList as $orderid)
			{
                // record the orderid of the order for use when reading the orderfooter.
  			    $orderFooterOrderID = $orderid;

                if (!$pIDListIsHeader)
                {
                    // find order id, only if the export is triggered, not from manual export.
                    $getOrderIDSQL = 'SELECT orderid FROM `ORDERITEMS` WHERE (id = ?)';
                    if($getOrderIDStmt = $dbObj->prepare($getOrderIDSQL))
                    {
                        if($getOrderIDStmt->bind_param('i', $orderid))
                        {
                            if($getOrderIDStmt->bind_result($orderFooterOrderID))
                            {
                                if($getOrderIDStmt->execute())
                                {
                                    $getOrderIDStmt->fetch();
                                }
                            }
                            $getOrderIDStmt->free_result();
                            $getOrderIDStmt->close();
                        }
                    }
                }

				$reportItem = Array();

				// increase time limit to prevent time out on large data exports.
				UtilsObj::resetPHPScriptTimeout(30);

				// if query with company code returns rows then set flag to true and add these records to the report

				if ($bindParamOk)
				{
					$flag = false;
					$recordCount = 0;

					if ($orderIDSForCompanyStmt->bind_param('is', $orderid, $pCompanyCode))
					{
						if ($orderIDSForCompanyStmt->bind_result($recordCount))
						{
							if ($orderIDSForCompanyStmt->execute())
							{
								$orderIDSForCompanyStmt->fetch();
							}
						}
						$orderIDSForCompanyStmt->free_result();
					}

					if ($recordCount * 1 > 0)
					{
						 $flag = true;
					}
				}
				else
				{
					// we don't need to filter by company
					$flag = true;
				}

				if ($flag == true)
				{
					// loop through tables and get data
					foreach ($tables as $section => $statement)
					{
						if ($stmt = $dbObj->prepare($statement))
						{
							$stmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);
							if (($section == 'items') && ($pCompanyCode != ''))
							{
								$stmt->bind_param('is', $orderid, $pCompanyCode);
							}
							else
							{
								$stmt->bind_param('i', $orderid);
							}

							if ($stmt->execute())
							{
								DatabaseObj::stmt_bind_assoc($stmt, $row);
								while ($stmt->fetch())
								{
									UtilsObj::resetPHPScriptTimeout(30);

									$componentArray = array();

									if ($section == 'items')
									{
										// localise items
										$licalisedTable = $localisedFields['items'];

										// set languagecode if by customer browser
										if ($pLanguageCode == '00')
										{
											$languageCode = $reportItem['header']['languagecode'];

											// split language items
											$charPos = strpos($languageCode, ',');

											// check if comma present, i.e. more than one language specified
											if ($charPos !== false)
											{
												$languageCode = substr($languageCode, 0, $charPos);
											}
										}

										// remove projectlsdata from results as for internal use only
										unset($row['projectlsdata']);

										foreach ($licalisedTable as $field)
										{
											$row[$field] = LocalizationObj::getLocaleString($row[$field], $languageCode, true);
										}

										// get components for each order item
										if ($stmt2 = $dbObj->prepare($componentsStatement))
										{
											$stmt2->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);
											$stmt2->bind_param('i', $row['id']);

											if ($stmt2->execute())
											{
												$row['components'] = Array();

												DatabaseObj::stmt_bind_assoc($stmt2, $subRow);

												while ($stmt2->fetch())
												{
													UtilsObj::resetPHPScriptTimeout(30);

													// if we have metadata codes retrieve and process the metadata
													// (we test here to avoid potentially thousands of unnecessary calls when processing large numbers of components)
													if ($subRow['metadatacodelist'] != '')
													{
														$metaData = MetaDataObj::getMetaData($reportItem['header']['id'], $row['id'], $subRow['id'], 'COMPONENT', $subRow['metadatacodelist'], $languageCode);
														
														$itemCount = count($metaData);
														if ($itemCount > 0)
														{
															for ($i = 0; $i < $itemCount; $i++)
															{
																unset($metaData[$i]['description']);
															}
															
															$subRow['metadatacodelist'] = $metaData;
														}
													}

													// localise items
													$licalisedTable = $localisedFields['components'];
													foreach ($licalisedTable as $field)
													{
														$subRow[$field] = LocalizationObj::getLocaleString($subRow[$field], $languageCode, true);
													}

													// we have to take an actual copy as there seems to be pass by reference issues
													$rowCopy = Array();
													foreach ($subRow as $rowKey=>$rowValue)
													{
														$rowCopy[$rowKey] = $rowValue;
													}

													$row['components'][] = $rowCopy;
												}
											}
										}
										// we have to take an actual copy as there seems to be pass by reference issues
										$rowCopy = Array();
										foreach ($row as $rowKey=>$rowValue)
										{
											$rowCopy[$rowKey] = $rowValue;
										}

										$reportItem[$section][] = $rowCopy;
									}
									else
									{
										// we have to take an actual copy as there seems to be pass by reference issues
										$rowCopy = Array();
										foreach ($row as $rowKey=>$rowValue)
										{
											$rowCopy[$rowKey] = $rowValue;
										}

										$reportItem[$section] = $rowCopy;
									}
								}
							}
							$stmt->free_result();
							$stmt->close();
						}
					}

					// set languagecode if by customer browser
					if ($pLanguageCode == '00')
					{
						$languageCode = $reportItem['header']['languagecode'];
						// split language items
						$charPos = strpos($languageCode, ',');

						// check if comma present, i.e. more than one language specified
						if ($charPos !== false)
						{
							$languageCode = substr($languageCode, 0, $charPos);
						}
					}

					// add metadata if present
					$metaData = MetaDataObj::getMetaData($reportItem['header']['id'], 0, 0, 'ORDER', $reportItem['header']['metadatacodelist'], $languageCode);

					// remove description field
					$itemCount = count($metaData);
					for ($i = 0; $i < $itemCount; $i++)
					{
						unset($metaData[$i]['description']);
					}

					if (count($metaData) > 0)
					{
						$reportItem['metadata'] = $metaData;
					}

					// localise
					foreach ($localisedFields as $key => $table)
					{
						if (($key != 'items') && ($key != 'components'))
						{
							foreach ($table as $field)
							{
								// localise
								$reportItem[$key][$field] = LocalizationObj::getLocaleString($reportItem[$key][$field], $languageCode, true);
							}
						}
					}

                    // get order footer components for each order item
                    $sqlStatementFooter = 'SELECT *
                                            FROM ORDERITEMCOMPONENTS
                                            WHERE orderitemid = -1
                                                AND orderid= ?';
                    $stmtFooter = $dbObj->prepare($sqlStatementFooter);
                    if ($stmtFooter)
                    {
                        $stmtFooter->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);
                        $stmtFooter->bind_param('i', $orderFooterOrderID);
                        if ($stmtFooter->execute())
                        {
                            DatabaseObj::stmt_bind_assoc($stmtFooter, $orderComponent);
                            $rowCopy = Array();
                            $row = array();
                            while ($stmtFooter->fetch())
                            {
                                UtilsObj::resetPHPScriptTimeout(30);

                                if (isset($orderComponent['metadatacodelist']))
                                {
                                    $metaData = MetaDataObj::getMetaData($reportItem['header']['id'], '-1', $orderComponent['id'], 'COMPONENT', $orderComponent['metadatacodelist'], $languageCode);
                                    $itemCount = count($metaData);
                                    for ($i = 0; $i < $itemCount; $i++)
                                    {
                                        unset($metaData[$i]['description']);
                                    }
                                    if (count($metaData) > 0)
                                    {
                                        $orderComponent['metadatacodelist'] = $metaData;
                                    }
                                }

                                // localise items
                                $licalisedTable = $localisedFields['components'];
                                foreach ($licalisedTable as $field)
                                {
                                    $orderComponent[$field] = LocalizationObj::getLocaleString($orderComponent[$field], $languageCode, true);
                                }

                                // we have to take an actual copy as there seems to be pass by reference issues

                                foreach ($orderComponent as $rowKey=>$rowValue)
                                {
                                    $rowCopy[$rowKey] = $rowValue;
                                }
                                $row['components'][] = $rowCopy;
                            }
                            $reportItem['orderfooter'] = $row;
                        }
                    }

					// append to result array
					$resultArray[] = $reportItem;
				}
			}
			$dbObj->close();
		}

		return $resultArray;
	}


	static function generateCustomerExportData($pID)
	{
		$resultArray = Array();

		$dbObj = DatabaseObj::getConnection();
		if ($dbObj)
		{
			$statement = 'SELECT * FROM `USERS` WHERE `id` = ?';

			if ($stmt = $dbObj->prepare($statement))
			{
				if ($stmt->bind_param('i', $pID))
				{
					$stmt->execute();
					DatabaseObj::stmt_bind_assoc($stmt, $row);
					$stmt->fetch();

					foreach ($row as $key=>$value)
					{
						$resultArray[$key] = $value;
					}
				}

				$stmt->free_result();
				$stmt->close();
			}

		$dbObj->close();
		}

		return $resultArray;
	}


	static function getExportCompleteLong($pStartDate, $pEndDate, $pFilterType, $pFilterValue, $pLanguageCode, $pDateType, $pIncludePaymentData, $pCompanyCode = '')
	{
		 $tempArray = self::selectExportIdListByDate($pStartDate, $pEndDate, $pDateType, $pFilterType, $pFilterValue, $pCompanyCode);

		 return self::generateOrderExportData($tempArray, true, $pLanguageCode, $pIncludePaymentData, $pCompanyCode);
	}


	static function selectExportIdListByDate($pStartDate, $pEndDate, $pDateType, $pFilterType, $pFilterValue, $pCompanyCode = '')
	{
		global $gSession;

		if ($gSession['userdata']['companycode'] != '')
		{
			$pCompanyCode = $gSession['userdata']['companycode'];
		}

		// return an array containing order IDs of selected orders
		$idList = Array();

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			// first build temporary array with order IDs
			switch ($pDateType)
			{
				case 'OR': // Orders Received
					$statement = 'SELECT DISTINCT `oh`.`id` FROM `ORDERHEADER` oh USE INDEX(`orderdate`) LEFT JOIN `ORDERITEMS` oi ON oh.id = oi.orderid
						WHERE DATE(`orderdate`) >= ? AND DATE(`orderdate`) <= ? ';

					if ($pFilterType == 'BRAND')
					{
						$statement .= 'AND (`webbrandcode` = ?)';
					}
					elseif ($pFilterType == 'GROUPCODE')
					{
						$statement .= 'AND (`groupcode` = ?)';
					}
					break;
				case 'OP': // Orders Printed
					if ($pFilterType == 'BRAND')
					{
						$statement = 'SELECT DISTINCT `orderid` FROM `ORDERITEMS` oi LEFT JOIN `ORDERHEADER` oh ON oh.id = oi.orderid
							WHERE (DATE(outputtimestamp) >= ?) AND (DATE(outputtimestamp) <= ?) AND (`webbrandcode` = ?)';
					}
					elseif ($pFilterType == 'GROUPCODE')
					{

						$statement = 'SELECT  DISTINCT `orderid` FROM `ORDERITEMS` oi LEFT JOIN `ORDERHEADER` oh ON oh.id = oi.orderid
							WHERE (DATE(outputtimestamp) >= ?) AND (DATE(outputtimestamp) <= ?) AND (`groupcode` = ?)';
					}
					else
					{
						$statement = 'SELECT DISTINCT `orderid` FROM `ORDERITEMS` WHERE (DATE(outputtimestamp) >= ?) AND (DATE(outputtimestamp) <= ?) ';
					}
					break;
				case 'OS': // Orders Shipped
					if ($pFilterType == 'BRAND')
					{
						$statement = 'SELECT DISTINCT `orderid` FROM `ORDERITEMS` oi LEFT JOIN `ORDERHEADER` oh ON oh.id = oi.orderid
							WHERE (DATE(shippeddate) >= ?) AND (DATE(shippeddate) <= ?) AND (`webbrandcode` = ?) ';

					}
					elseif ($pFilterType == 'GROUPCODE')
					{

						$statement = 'SELECT DISTINCT `orderid` FROM `ORDERITEMS` oi LEFT JOIN `ORDERHEADER` oh ON oh.id = oi.orderid
							WHERE (DATE(shippeddate)>= ?) AND (DATE(shippeddate) <= ?) AND (`groupcode` = ?) ';
					}
					else
					{
						$statement = 'SELECT DISTINCT `orderid` FROM `ORDERITEMS` WHERE (DATE(shippeddate)>= ?) AND (DATE(shippeddate) <= ?) ';
					}
			}

			// filter out temporary orders that have been converted
			$statement .= ' AND (`active` < ' . TPX_ORDER_STATUS_CONVERTED . ')';

			// if brand owner is logged in then they can only export data that relates to the brand they are assigned to.
			if  (($gSession['userdata']['usertype'] == TPX_LOGIN_BRAND_OWNER) && ($pFilterType == 'BRAND'))
			{
				$pFilterValue == $gSession['userdata']['webbrandcode'];
			}

			if ($stmt = $dbObj->prepare($statement))
			{
				if ($pFilterType != '')
				{
					$bindResult = $stmt->bind_param('sss', $pStartDate, $pEndDate, $pFilterValue);
				}
				else
				{
					$bindResult = $stmt->bind_param('ss', $pStartDate, $pEndDate);
				}
				if ($bindResult)
				{
					if ($stmt->bind_result($id))
					{
						if ($stmt->execute())
						{
							while ($stmt->fetch())
							{
								UtilsObj::resetPHPScriptTimeout(30);
								$idList[] = $id;
							}
						}
					}
				}
				$stmt->free_result();
				$stmt->close();
			}

			$dbObj->close();
		}

		return $idList;
	}


	static function getEventTriggerFromNameOrID($pTrigger)
	{
		// return an array containing the product based on the supplied parameters
		$isactive = 0;
		$result = '';
        $id = 0;
        $datecreated = '';
        $eventcode = '';
        $language = '';
        $exportformat = '';
        $includepaymentdata = 0;
        $beautifiedxml = 0;
        $subfolderformat = '';
        $filenameformat = '';
		$webhook1url = '';
		$webhook2url = '';
        $bindParam = 'i';

		$idType = ($pTrigger);

		$dbObj = DatabaseObj::getConnection();
		if ($dbObj)
		{
			//retrieve event data based on either an eventcode being passed or an event code -->
			//checking to see if parameter is an integer or a string

			if (is_string($idType) == true)
            {
                $sqlStatement = 'SELECT * FROM `TRIGGERS` WHERE (`eventcode` = ?)';
                $bindParam = 's';
            }
            else
            {
                $sqlStatement = 'SELECT * FROM `TRIGGERS` WHERE (`id` = ?)';
                $bindParam = 'i';
            }

            if ($stmt = $dbObj->prepare($sqlStatement))
			{
				if ($stmt->bind_param($bindParam, $pTrigger))
				{
                    if ($stmt->execute())
                    {
                        if ($stmt->store_result())
                        {
                            if ($stmt->num_rows > 0)
                            {
                                if ($stmt->bind_result($id, $datecreated, $eventcode, $language, $exportformat, $includepaymentdata, $beautifiedxml, $subfolderformat, $filenameformat
								, $webhook1url, $webhook2url, $isactive))
                                {
                                    if (!$stmt->fetch())
                                    {
                                        // no matching event
                                        $result = 'str_InvalidProductCode';
                                    }
                                }
                                else
                                {
                                    // could not bind result
                                    $result = 'str_DatabaseError';
                                    $resultParam = 'getEventTriggerFromName bind result ' . $dbObj->error;
                                }
                            }
                            else
                            {
                                // no rows found
                                $result = 'str_InvalidProductCode';
                            }
                        }
                        else
                        {
                            // could not store result
                            $result = 'str_DatabaseError';
                            $resultParam = 'getEventTriggerFromName store result ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        // could not execute statement
                        $result = 'str_DatabaseError';
                        $resultParam = 'getEventTriggerFromName execute ' . $dbObj->error;
                    }
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'getEventTriggerFromName bind params ' . $dbObj->error;
				}
				$stmt->free_result();
				$stmt->close();
                $stmt = null;
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'getEventTriggerFromName prepare ' . $dbObj->error;
			}
			$dbObj->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'getEventTriggerFromName connect ' . $dbObj->error;
		}

        $resultArray['result'] = $result;
		$resultArray['id'] = $id;
		$resultArray['eventcode'] = $eventcode;
		$resultArray['language'] = $language;
		$resultArray['exportformat'] = $exportformat;
		$resultArray['paymentdata'] = $includepaymentdata;
		$resultArray['beautified'] = $beautifiedxml;
		$resultArray['subfolderformat'] = $subfolderformat;
		$resultArray['filenameformat'] = $filenameformat;
		$resultArray['webhook1url'] = $webhook1url;
		$resultArray['webhook2url'] = $webhook2url;
		$resultArray['isactive'] = $isactive;

		return $resultArray;
	}


    static function EventTrigger($pTriggerCode, $pItemIDType, $pItemID, $pOrderHeaderID, $pCompanyCode = '')
    {
    	global $gSession;
    	global $gConstants;
		global $ac_config;

        $result = '';
        $uploadRef = '';
		$text = '';
		$exportTriggerArray = Array();
		$date = date('Ymd');
		$time = date('His');
		$orderNumber = '';
		$orderItemID = 0;
		$userID = $gSession['userid'];
		$XMLChildHeader = ($pItemIDType == 'CUSTOMER') ? 'customer' : 'order';

        // retrieving event data
        $eventArray = self::getEventTriggerFromNameOrID($pTriggerCode);

        if ($eventArray['isactive'] == 1)
        {
			$languageCode = $eventArray['language'];

			if ($languageCode == 'Default')
			{
				$languageCode = $gConstants['defaultlanguagecode'];
			}

			$includePaymentData = $eventArray['paymentdata'];
			$beautifyXML = $eventArray['beautified'];
			$exportFormat = strtolower($eventArray['exportformat']);
			$fileNameFormat = $eventArray['filenameformat'];
			$subFolderFormat = $eventArray['subfolderformat'];
			$task1WebhookURL = $eventArray['webhook1url'];
			$task2WebhookURL = $eventArray['webhook2url'];

			// if the company code is empty attempt to retrieve it from the supplied record id
			if (($pCompanyCode == '') && ($pItemIDType != 'CUSTOMER'))
			{
				$dbObj = DatabaseObj::getGlobalDBConnection();
				if ($dbObj)
				{
					if ($pItemIDType == 'ORDER')
					{
						$sqlStatement = 'SELECT `currentcompanycode` FROM `ORDERITEMS` WHERE `orderid` = ? LIMIT 1';
					}
					else
					{
						$sqlStatement = 'SELECT `currentcompanycode` FROM `ORDERITEMS` WHERE `id` = ?';
					}

					if ($stmt = $dbObj->prepare($sqlStatement))
					{
						if ($stmt->bind_param('i', $pItemID))
						{
							if ($stmt->bind_result($companyCode))
							{
								if ($stmt->execute())
								{
									if ($stmt->fetch())
									{
										$pCompanyCode = $companyCode;
									}
								}
								else
								{
									// could not execute statement
									$result = 'Error: Find orderitem failed: ' . $dbObj->error;
								}
							}
							else
							{
								// could not bind result
								$result = 'Error: Unable to bind result: ' . $dbObj->error;
							}
						}
						else
						{
							// could not bind parameters
							$result = 'Error: Unable to bind parameters: ' . $dbObj->error;
						}
						$stmt->free_result();
						$stmt->close();
						$stmt = null;
					}
					else
					{
						$result = 'Error: Unable to prepare statement: ' . $dbObj->error;
					}

					$dbObj->close();
				}
				else
				{
					$result = 'Error: Cannot connect to database';
				}
			}

			// get data
			if ($pItemIDType == 'CUSTOMER')
			{
				$customerData = self::generateCustomerExportData($pItemID);
				$exportTriggerArray[0]['customer'] = $customerData;
				$exportTriggerArray[0]['items'] = Array();
				$exportTriggerArray[0]['shipping'] = Array();

				$login = $customerData['login'];

				// handling [TAGS] in subfolderformat
				$subFolderFormat = str_ireplace('[EVENT]', $pTriggerCode, $subFolderFormat);
				$subFolderFormat = str_ireplace('[DATE]', $date, $subFolderFormat);
				$subFolderFormat = str_ireplace('[TIME]', $time, $subFolderFormat);
				$subFolderFormat = str_ireplace('[LOGIN]', $login, $subFolderFormat);
			}
			else
			{
				if ($pItemIDType == 'ORDER')
				{
					$headerItem = true;
				}
				else
				{
					$headerItem = false;
					$orderItemID = $pItemID;
				}

				$exportTriggerArray = self::generateOrderExportData(Array($pItemID), $headerItem, $languageCode, $includePaymentData, '*NONE*');

				if (count($exportTriggerArray) > 0)
				{
					$orderNumber = $exportTriggerArray[0]['header']['ordernumber'];
					$productCode = $exportTriggerArray[0]['items'][0]['productcode'];
					$uploadRef = $exportTriggerArray[0]['items'][0]['uploadref'];
					$groupCode = $exportTriggerArray[0]['header']['groupcode'];
					$brandCode = $exportTriggerArray[0]['header']['webbrandcode'];
					$lineCount = $exportTriggerArray[0]['header']['itemcount'];
					$lineNumber = $exportTriggerArray[0]['items'][0]['itemnumber'];
					$orderAndLine = $orderNumber . '_' . $lineNumber;

					// handling [TAGS] in subfolderformat
					$subFolderFormat = str_ireplace('[EVENT]', $pTriggerCode, $subFolderFormat);
					$subFolderFormat = str_ireplace('[DATE]', $date, $subFolderFormat);
					$subFolderFormat = str_ireplace('[TIME]', $time, $subFolderFormat);
					$subFolderFormat = str_ireplace('[PRODUCT]', $productCode, $subFolderFormat);
					$subFolderFormat = str_ireplace('[ORDER]', $orderNumber, $subFolderFormat);
					$subFolderFormat = str_ireplace('[REF]', $uploadRef, $subFolderFormat);
					$subFolderFormat = str_ireplace('[LICENSE]', $groupCode, $subFolderFormat);
					$subFolderFormat = str_ireplace('[BRAND]', $brandCode, $subFolderFormat);
					$subFolderFormat = str_ireplace('[ORDERANDLINE]', $orderAndLine, $subFolderFormat);
					$subFolderFormat = str_ireplace('[LINECOUNT]', $lineCount, $subFolderFormat);
					$subFolderFormat = str_ireplace('[LINENUMBER]', $lineNumber, $subFolderFormat);
				}
			}


			$filePath = str_replace("\\", '/', $subFolderFormat);

			if ($filePath != '')
			{
				$filePath = UtilsObj::correctPath($filePath);
				$exportDirectory = realpath($ac_config['PRIVATEDATAEXPORTPATH']) . '/ExportData/';

				// If the export folder doesn't exist make it.
				if (! is_dir($exportDirectory))
				{
					@mkdir($exportDirectory, 0777);
					@chmod($exportDirectory, 0777);
				}
				$dataPath = $exportDirectory . $pTriggerCode . '-' . ($pItemIDType == 'CUSTOMER' ? ($login . '-' . $date . '-' . $time) : $orderNumber) . '-' . microtime(true) . '-data.php';

				// Save the dataset array in a php file. This is so any updates to the database between now and the export task run time do not change what the export file contains.
				file_put_contents($dataPath, '<?php' . PHP_EOL . '$dataSet = ' . var_export($exportTriggerArray, true) . ';');
				$text = $dataPath;
		 	}
		 	else
		 	{
		 		$result = 'EXPORT PATH EMPTY';
		 	}

		 	// make sure we don't have an error and can continue
		 	if ($result == '')
		 	{
				if ($fileNameFormat != '')
				{
					if ($pItemIDType == "CUSTOMER")
					{
						// handling [TAGS] in filenameformat
						$fileNameFormat = str_ireplace('[EVENT]', $pTriggerCode, $fileNameFormat);
						$fileNameFormat = str_ireplace('[DATE]', $date, $fileNameFormat);
						$fileNameFormat = str_ireplace('[TIME]', $time, $fileNameFormat);
						$fileNameFormat = str_ireplace('[LOGIN]', $login, $fileNameFormat);
						$fileNameFormat = str_ireplace('[USERID]', $pItemID, $fileNameFormat);
					}
					else
					{
						// handling [TAGS] in filenameformat
						$fileNameFormat = str_ireplace('[EVENT]', $pTriggerCode, $fileNameFormat);
						$fileNameFormat = str_ireplace('[DATE]', $date, $fileNameFormat);
						$fileNameFormat = str_ireplace('[TIME]', $time, $fileNameFormat);
						$fileNameFormat = str_ireplace('[PRODUCT]', $productCode, $fileNameFormat);
						$fileNameFormat = str_ireplace('[ORDER]', $orderNumber, $fileNameFormat);
						$fileNameFormat = str_ireplace('[REF]', $uploadRef, $fileNameFormat);
						$fileNameFormat = str_ireplace('[LICENSE]', $groupCode, $fileNameFormat);
						$fileNameFormat = str_ireplace('[BRAND]', $brandCode, $fileNameFormat);
						$fileNameFormat = str_ireplace('[ORDERANDLINE]', $orderAndLine, $fileNameFormat);
						$fileNameFormat = str_ireplace('[LINECOUNT]', $lineCount, $fileNameFormat);
						$fileNameFormat = str_ireplace('[LINENUMBER]', $lineNumber, $fileNameFormat);
						$fileNameFormat = str_ireplace('[USERID]', $exportTriggerArray[0]['header']['userid'], $fileNameFormat);
					}
				}
				else
				{
					$fileNameFormat = $pTriggerCode . '-' . $date . '-' . $time;
				}

				$filename = $filePath . $fileNameFormat . '.' . $exportFormat;

				// check to see if file exists
				if (file_exists($filename))
				{
					@unlink($filename);
				}

				// the file should not exist at this point but check again
				if (! file_exists($filename))
				{
					$taskInfo = DatabaseObj::getTask(TPX_EXPORT);
					if ($taskInfo['result'] == '')
					{
						$eventResultArray = DatabaseObj::createEvent(TPX_EXPORT, $pCompanyCode, $gSession['licensekeydata']['groupcode'],
							$gSession['webbrandcode'], $taskInfo['nextRunTime'], 0, $text, '', $filename, $exportFormat, $beautifyXML, $XMLChildHeader, '', '', $pOrderHeaderID,
							$orderItemID, $userID, '', '', $userID);

						if ($eventResultArray['result'] == '')
						{
							$result = $eventResultArray['resultparam'];
						}
					}
				}
				else
				{
					$result = 'EXPORT FILE ALREADY EXISTS: ' . $filename;
				}
			}

			if ($result == '')
			{
				DatabaseObj::updateActivityLog($gSession['ref'], 0, -1, $gSession['userlogin'], $gSession['username'], 0,
                                'ADMIN', 'DATAEXPORTEVENT-SUCCESSFUL', $filename . "\n" . $pItemID . ' ' . $pTriggerCode, 1);
			}
			else
			{
				DatabaseObj::updateActivityLog($gSession['ref'], 0, -1, $gSession['userlogin'], $gSession['username'], 0,
                     'ADMIN', 'DATAEXPORTEVENT-FAILURE', $result . "\n" . $pItemID . ' ' . $pTriggerCode, 1);
			}

			if (($task1WebhookURL !== '') || ($task2WebhookURL !== ''))
			{
				if ($pItemIDType == 'CUSTOMER')
				{
					$postData = $exportTriggerArray[0]['customer'];
					unset($postData['password']);
				} 
				else
				{
					$postData = $exportTriggerArray;
				}

				$topic = $pItemIDType . '/' . $pTriggerCode;

				if ($task1WebhookURL !== '')
				{
					self::fireWebhook($pCompanyCode, $gSession['webbrandcode'], $gSession['licensekeydata']['groupcode'], $pOrderHeaderID,
							$orderItemID, $userID, $topic, $task1WebhookURL, $postData);
				}

				if ($task2WebhookURL !== '')
				{
					self::fireWebhook($pCompanyCode, $gSession['webbrandcode'], $gSession['licensekeydata']['groupcode'], $pOrderHeaderID,
							$orderItemID, $userID, $topic, $task2WebhookURL, $postData);
				}
			}
		}
    }


    /**
     * Generates export data and returns it as text.
     *
     * Full description - please update.
     *
     * EncodeString is required to html-encode characters that have to be included in CDATA section of XML (&, ", ', <, >).
     * As Simple XML doesn't support CDATA these characters have to be html-encoded in order to be correctly included in XML document.
     *
     * @param $pReportArray
     * @param $pExportFormat
     * @param $pBeautifyXML
     * @param $pChildHeader
     *
     * @return $text
     */
    static function exportDataGenerate($pReportArray, $pExportFormat, $pBeautifyXML, $pChildHeader)
    {
    	$pExportFormat = strtoupper($pExportFormat);
		$separator = "\t";
		$text = '';

    	if (empty($pReportArray))
		{
			$text = null;
		}
		else
		{
			// turn into XLS or XML
			switch ($pExportFormat)
			{
				case 'XML':
					// turn reportArray into xml text

					$xmltext = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<export></export>";
					$xmlobj = simplexml_load_string($xmltext);

					foreach ($pReportArray as $row)
					{
						UtilsObj::resetPHPScriptTimeout(30);
						$orderobj = $xmlobj->addChild($pChildHeader);
						foreach ($row as $key => $value) // order item
						{
							$tableobj = $orderobj->addChild($key);
							if ($key == 'metadata')
							{
								$tableobj->addAttribute('length', count($value));
							}
							foreach ($value as $key1 => $value1) // values inside header, items and shipping
							{
								if (is_array($value1) && $key != 'orderfooter') // item inside <items>
								{
									$fieldobj = $tableobj->addChild('item');

									foreach ($value1 as $key2 => $value2)
									{
										if (is_array($value2)) // components
										{
											$componenstobj = $fieldobj->addChild('components');

											foreach ($value2 as $key3 => $value3) // component inside <components>
											{
												if (is_array($value3))  // component
												{
													$componentobj = $componenstobj->addChild('component');
													foreach ($value3 as $key4 => $value4)
													{
														if (is_array($value4))  // metadata
														{
															$matadatlistaobj = $componentobj->addChild('metadatacodelist');
															foreach ($value4 as $key5 => $value5)
															{
																if (is_array($value5))  // metadata
																{
																	$matadataobj = $matadatlistaobj->addChild('metadata');
																	foreach ($value5 as $key6 => $value6)
																	{
																		$matadataobj->addChild($key6, UtilsObj::encodeString($value6));
																	}
																}
																else
																{
																	$matadatlistaobj->addChild($key5, UtilsObj::encodeString($value5));
																}
															}
														}
														else
														{
															$componentobj->addChild($key4, UtilsObj::encodeString($value4));
														}
													}
												}
												else
												{
													$componenstobj->addChild($key3, UtilsObj::encodeString($value3));
												}
											}
										}
										else
										{
											$fieldobj->addChild($key2, UtilsObj::encodeString($value2));
										}
									}
								}
								else
								{
                                    if (is_array($value1) && $key == 'orderfooter')
									{
                                        $fieldobj = $tableobj->addChild('components');
                                        foreach ($value1 as $key2 => $value2)
                                        {
                                            if (is_array($value2)) // components
                                            {
                                                $componenstobj = $fieldobj->addChild('component');

                                                foreach ($value2 as $key3 => $value3) // component inside <components>
                                                {
                                                    if (is_array($value3))  // component
                                                    {
                                                        $matadatlistaobj = $componenstobj->addChild('metadatacodelist');
                                                        foreach ($value3 as $key4 => $value4)
                                                        {
                                                            if (is_array($value4))  // metadata
                                                            {
                                                                $matadataobj = $matadatlistaobj->addChild('metadata');
                                                                foreach ($value4 as $key5 => $value5)
                                                                {
                                                                    $matadataobj->addChild($key5, UtilsObj::encodeString($value5));
                                                                }
                                                            }
                                                            else
                                                            {
                                                                $matadatlistaobj->addChild($key4, UtilsObj::encodeString($value4));
                                                            }
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $componenstobj->addChild($key3, UtilsObj::encodeString($value3));
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                $fieldobj->addChild($key2, UtilsObj::encodeString($value2));
                                            }
                                        }

                                    } 
									else
									{
                                        $tableobj->addChild($key1, UtilsObj::encodeString($value1));
                                    }
								}
							}
						}
					}

					// convert XML to String
					$text = $xmlobj->asXML();

					// pretty print if needed
					if ($pBeautifyXML == 1)
					{
						// line breaks between tags
						$text = str_replace('><', ">\n<", $text);
												
						// explode text into array
						$lines = explode("\n", $text);
												
						for ($i = 1, $size = sizeof($lines); $i < $size; ++$i)
						{
							// check if opening tag is followed by closing tag
							if ((strlen($lines[$i-1]) +1 == strlen($lines[$i])) &&
								($lines[$i-1] == str_replace('/', '', $lines[$i])))
							{
								$lines[$i-1] = str_replace('>', '/>', $lines[$i-1]);	// <tag/>
								$lines[$i] = ''; // this empty line needs to be removed later on
							}
						}
												
						$indent = ''; // the indent corresponding to stepping depth
						for ($i = 1, $size = sizeof($lines); $i < $size; ++$i)
						{
							if ($lines[$i] != '')
							{
								$pos = strpos($lines[$i], '/');
								if (($pos) && ($pos > 0)) // 2nd condition superfluous, for if $pos was 0 then ($pos) wouldn't evaluate to true
								// there is a backslash
								{
									if ($pos == 1)
									{
										// if backslash is in second position, it's a closing tag
										// and we need to shorten the indent
										if ($indent == "\t")
										{
											$indent = '';
										}
										else
										{
											$indent = substr($indent, 1);
										}
									}
									$lines[$i] = $indent . $lines[$i];
								}
								// if there is no backslash at all, it must be an opening tag
								else
								{
									// not for multi-line text
									if (strpos($lines[$i], "&#13;") === false)
									{
										// apply indent
										$lines[$i] = $indent . $lines[$i];
										// only then increase indent
										$indent .= "\t";
									}
								}
							}
						}
												
						// turn into text again
						$text = implode("\n", $lines);
						// remove empty lines
						while (strpos($text, "\n\n") !== false)
						{
							$text = str_replace("\n\n", "\n", $text);
						}
						// un-indent multi-line text
						while (strpos($text, "&#13;\n\t") !== false)
						{
							$text = str_replace("&#13;\n\t", "&#13;\n", $text);
						}						
					}
					break;
				case 'TXT':
                    // turn reportArray into tab separated text
                    // write header row
                    $row = $pReportArray[0];
                    foreach ($row as $key => $value)
                    {
                        foreach ($value as $key1 => $value1)
                        {
                            // metadata and orderfooter available only in XML format
                            if ($key != 'metadata' && $key !='orderfooter' && $key != 'items')
                            {
                                $text .= $key . '::' . $key1 . $separator;
                            }
                            else
                            {
                                if ($key == 'items')
                                {
                                    foreach ($row['items'][0] as $sKey => $sValue2)
                                    {
                                        if ($sKey != 'components')
                                        {
                                            $text .= $key . '::' . $sKey . $separator;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $text .= "\n";

                    // now write data
                    $isize = count($pReportArray);
                    $i = 0;
                    $iTreatItem = 0;
                    $iItemCount = 0;
                    $bTreat = true;
                    while ($i < $isize)
                    {
                        $row = $pReportArray[$i];
                        foreach ($row as $key => $value)
                        {
                            foreach ($value as $key1 => $value1)
                            {
                                // metadata and orderfooter available only in XML format
                                if ($key != 'metadata'  && $key !='orderfooter' && $key != 'items')
                                {
                                    $text .= $value1 . $separator;
                                }
                                else
                                {
                                    if ($key == 'items' && $bTreat)
                                    {
                                        $iItemCount = count($row['items']);
                                        foreach ($row['items'][$iTreatItem] as $sKey => $sValue)
                                        {
                                            if ($sKey != 'components')
                                            {
                                                $text .= $sValue . $separator;
                                            }
                                        }
                                        $iTreatItem++;
                                        $bTreat = false;
                                    }
                                }
                            }
                        }
                        $text .= "\n";
                        $bTreat = true;
                        if ($iTreatItem == $iItemCount)
                        {
                            $i++;
                            $iTreatItem = 0;
                        }
                    }
                    break;
			}
		}

    	return $text;
    }

	static function fireWebhook($pCompanyCode, $pBrandCode, $pGroupCode, $pOrderHeaderID, $pOrderItemID, $pUserID, $pTriggerCode, $pWebhookURL, $pPostData) 
	{
		try 
		{
			$webhookTrigger = new \Taopix\Webhook\Webhook('TAOPIX', $pTriggerCode, $pPostData);
			$webHookReturnArray = $webhookTrigger->recordWebhookData();
			$webHookRecordID = $webHookReturnArray['id'];

			$taskInfo = DatabaseObj::getTask('TAOPIX_WEBHOOK');
				
			if ($taskInfo['result'] == '')
			{
				$eventResultArray = DatabaseObj::createEvent('TAOPIX_WEBHOOK', $pCompanyCode, $pGroupCode,
				$pBrandCode, $taskInfo['nextRunTime'], 0, '', '', $webHookRecordID, $pWebhookURL, '', '', '', '', $pOrderHeaderID,
				$pOrderItemID, $pUserID, '', '', $pUserID);

				if ($eventResultArray['result'] == '')
				{
					$result = $eventResultArray['resultparam'];
				}
			}
		} 
		catch (\Throwable $pError)
		{
			error_log(print_r($pError, true));
		}
	}
}

?>