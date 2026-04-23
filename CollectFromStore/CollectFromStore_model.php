<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsEmail.php');
require_once('../Utils/UtilsDataExport.php');

class CollectFromStore_model
{
	static function markShipped()
	{
		global $gSession, $ac_config;

		$recIds = isset($_POST['ids']) ? $_POST['ids'] : '';
		$shippedStatus = isset($_POST['shipped']) ? $_POST['shipped'] : '0';
		$shippedDate = isset($_POST['shippedDate']) ? $_POST['shippedDate'] : '';
		$shippedTime = isset($_POST['shippedTime']) ? $_POST['shippedTime'] : '';

		$recIds  = explode(',',$recIds);

		$shippedStatusOrig = $shippedStatus;

		if ($shippedStatus == '1')
		{
			$shippedDate = date('Y-m-d H:i:s', strtotime( $shippedDate .' '.date('H:i:s', strtotime($shippedTime)) ));
			$userId = $gSession['userid'];
			$shippedStatus = TPX_ITEM_STATUS_SHIPPED_TO_STORE_FROM_DISTRIBUTION_CENTRE;
		}
		else
		{
			$shippedDate = '0000-00-00 00:00:00';
			$userId = 0;
			$shippedStatus = TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_DISTRIBUTION_CENTRE;
		}

		// need to add sending emails and status update
		$dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
    	{
        	for($i = 0; $i < count($recIds); $i++)
        	{
        		if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `shippeddistributioncentreshippedtimestamp` = now(), `shippeddistributioncentreshippeddate` = ?, `shippeddistributioncentreshippeduserid` = ?,
        									`status` = ?, `statusdescription` = "", `statustimestamp` = now(), `shippedstorereceiveddate` = "0000-00-00 00:00:00", `shippedstorereceiveduserid` = 0,
        									`shippedcustomercollecteddate` = "0000-00-00 00:00:00", `shippedcustomercollecteduserid` = 0  WHERE `id` = ?'))
       			{
       				if (($stmt->bind_param('siii', $shippedDate, $userId, $shippedStatus, $recIds[$i])))
       				{
                   		if ($stmt->execute())
                   		{
                   			if ($shippedStatusOrig == '1')
                   			{
                   				self::sendEmail($recIds[$i], '0');
                   				DataExportObj::EventTrigger(TPX_TRIGGER_SHIPPED_DISTRIBUTION_CENTRE_SHIPPED, 'ORDERITEM', $recIds[$i], 0);
                   			}
                   		}
               		}
       			}
				$stmt->close();
        	}
			$dbObj->close();
       	}
       	echo '{"success":true, "data":[{"id":' . '1' . ',"active":"'.'1'.'"}]}';
	}


	static function markCollected()
	{
		global $gSession, $ac_config;

		$recIds = isset($_POST['ids']) ? $_POST['ids'] : '';
		$recPaidIds = isset($_POST['markAsPaid']) ? $_POST['markAsPaid'] : '';
		$recNotPaidIds = isset($_POST['markAsNotPaid']) ? $_POST['markAsNotPaid'] : '';
		$collectedStatus = isset($_POST['collected']) ? $_POST['collected'] : '0';
		$collectedDate = isset($_POST['collectedDate']) ? $_POST['collectedDate'] : '';
		$collectedTime = isset($_POST['collectedTime']) ? $_POST['collectedTime'] : '';

		$recIds  = explode(',',$recIds);
		$recPaidIds  = explode(',',$recPaidIds);
		$recNotPaidIds  = explode(',',$recNotPaidIds);

		if ($collectedStatus == '1')
		{
			$shippedStatus = TPX_ITEM_STATUS_SHIPPED_COLLECTED_BY_CUSTOMER;
			$collectedDate = date('Y-m-d H:i:s', strtotime( $collectedDate .' '.date('H:i:s', strtotime($collectedTime)) ));
			$userId = $gSession['userid'];
		}
		else
		{
			$collectedDate = '0000-00-00 00:00:00';
			$userId = 0;
			$shippedStatus = TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE;
		}

		// need to add sending emails and status update
		$dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
    	{
        	for($i = 0, $paid = 1; $i < count($recPaidIds); $i++)
        	{
        		if ($stmt = $dbObj->prepare('UPDATE `ORDERHEADER` SET `paymentreceived` = ?, `paymentreceivedtimestamp` = now(), `paymentreceiveddate` = ?, `paymentreceiveduserid` = ? WHERE `id` = ?'))
				{
					if ($stmt->bind_param('isii', $paid, $collectedDate, $userId, $recPaidIds[$i]))
					{
					   if ($stmt->execute())
					   {
							// Trigger order paid when payment received.
							DataExportObj::EventTrigger(TPX_TRIGGER_ORDER_PAID, 'ORDER', $recPaidIds[$i], $recPaidIds[$i]);
					   }
					}
					$stmt->close();
				}
			}

			for($i = 0, $paid = 0, $userId = 0; $i < count($recNotPaidIds); $i++)
        	{
        		if ($stmt = $dbObj->prepare('UPDATE `ORDERHEADER` SET `paymentreceived` = 0, `paymentreceivedtimestamp` = "0000-00-00 00:00:00", `paymentreceiveddate` = "0000-00-00 00:00:00",
        								`paymentreceiveduserid` = 0 WHERE `id` = ?'))
				{
					if ($stmt->bind_param('i', $recNotPaidIds[$i]))
					{
					   $stmt->execute();
					}
					$stmt->close();
				}
			}

        	for($i = 0; $i < count($recIds); $i++)
        	{
        		if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET `shippedcustomercollectedtimestamp` = now(), `shippedcustomercollecteddate` = ?, `shippedcustomercollecteduserid` = ?,
        									`status` = ?, `statusdescription` = "", `statustimestamp` = now() WHERE `id` = ?'))
       			{
       				if (($stmt->bind_param('siii', $collectedDate, $userId, $shippedStatus, $recIds[$i])))
       				{
                   		$stmt->execute();
                   		if ($collectedStatus == '1')
						{
                   			DataExportObj::EventTrigger(TPX_TRIGGER_SHIPPED_STORE_CUSTOMER_COLLECTED, 'ORDERITEM', $recIds[$i], 0);
						}
               		}
					$stmt->close();
       			}
        	}
        	$dbObj->close();
       	}
       	echo '{"success":true, "data":[{"id":' . '1' . ',"active":"'.'1'.'"}]}';
	}


	static function markBooked()
	{
		global $gSession;

		$recIds = isset($_POST['ids']) ? $_POST['ids'] : '';
		$bookedStatus = isset($_POST['bookedIn']) ? $_POST['bookedIn'] : '0';
		$bookedInDate = isset($_POST['bookedInDate']) ? $_POST['bookedInDate'] : '';
		$bookedInTime = isset($_POST['bookedInTime']) ? $_POST['bookedInTime'] : '';
		$bookedStoreType = isset($_POST['storeType']) ? $_POST['storeType'] : '1'; // 1 -store, 0 - distribution centre

		$recIds = explode(',', $recIds);

		if ($bookedStoreType == '0')
		{
			// distribution centre
			$updateFields = '`shippeddistributioncentrereceivedtimestamp` = now(), `shippeddistributioncentrereceiveddate` = ?, `shippeddistributioncentrereceiveduserid` = ?,
							`shippeddistributioncentreshippeddate` = "0000-00-00 00:00:00", `shippeddistributioncentreshippeduserid` = 0, `shippedstorereceiveddate` = "0000-00-00 00:00:00",
							`shippedstorereceiveduserid` = 0, `shippedcustomercollecteddate` = "0000-00-00 00:00:00", `shippedcustomercollecteduserid` = 0 ';
			$shippedStatus = TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_DISTRIBUTION_CENTRE;
		}
		else
		{
			// store
			$updateFields = '`shippedstorereceivedtimestamp` = now(), `shippedstorereceiveddate` = ?, `shippedstorereceiveduserid` = ?, `shippedcustomercollecteddate` = "0000-00-00 00:00:00",
							`shippedcustomercollecteduserid` = 0';
            $shippedStatus = TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE;
		}

		if ($bookedStatus == '1')
		{
			$bookedInDate = date('Y-m-d H:i:s', strtotime($bookedInDate . ' ' . date('H:i:s', strtotime($bookedInTime))));
			$userId = $gSession['userid'];
		}
		else
		{
			$bookedInDate = '0000-00-00 00:00:00';
			$userId = 0;
		}
		// need to add sending emails and status update
		$dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
    	{
        	$shipped = false;
        	for ($i = 0; $i < count($recIds); $i++)
        	{
        		if ($bookedStatus == '0')
        		{
        			if ($bookedStoreType == '0')
					{
						// distribution centre
						$shippedStatus = TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE;
					}
					else
					{
						// store
                        $sql = 'SELECT `ORDERSHIPPING`.`distributioncentrecode`
                                    FROM `ORDERSHIPPING`
                                        LEFT JOIN `ORDERITEMS` ON `ORDERITEMS`.`orderid`= `ORDERSHIPPING`.`orderid`
                                    WHERE `ORDERITEMS`.`id` = ?';
						if ($stmt = $dbObj->prepare($sql))
						{
							if ($stmt->bind_param('i', $recIds[$i]))
							{
								if ($stmt->bind_result($distrCentreCode))
								{
									if (($stmt->execute()) && ($stmt->fetch()))
									{
										if ($distrCentreCode != '')
										{
											$shippedStatus = TPX_ITEM_STATUS_SHIPPED_TO_STORE_FROM_DISTRIBUTION_CENTRE;
										}
										else
										{
											$shippedStatus = TPX_ITEM_STATUS_SHIPPED_TO_STORE_DIRECTLY;
										}
									}
								}
							}
							$stmt->free_result();
                    		$stmt->close();
                    		$stmt = null;
						}
					}
        		}

        		if ($stmt = $dbObj->prepare('UPDATE `ORDERITEMS` SET ' . $updateFields . ', `status` = ?, `statusdescription` = "", `statustimestamp` = now() WHERE `id` = ?'))
       			{
       				if (($stmt->bind_param('siii', $bookedInDate, $userId, $shippedStatus, $recIds[$i])))
       				{
                   		if ($stmt->execute())
                   		{
							$shipped = true;
                   		}
               		}
       				$stmt->close();
       			}

        		if (($bookedStatus == '1') && ($shipped == true))
				{
					if ($bookedStoreType == '1')
					{
						self::sendEmail($recIds[$i], '1');
						DataExportObj::EventTrigger(TPX_TRIGGER_SHIPPED_STORE_RECEIVED, 'ORDERITEM', $recIds[$i], 0);
					}
					else
					{
						DataExportObj::EventTrigger(TPX_TRIGGER_SHIPPED_DISTRIBUTION_CENTRE_RECEIVED, 'ORDERITEM', $recIds[$i], 0);
					}
				}
        	}
        	$dbObj->close();
       	}

		echo '{"success":true, "data":[{"id":' . '1' . ',"active":"'.'1'.'"}]}';
	}


	static function listOrders()
	{
		global $gSession;

		/* different statuses for store, distribution center and production */
		$siteType = ($gSession['userdata']['usertype'] == TPX_LOGIN_DISTRIBUTION_CENTRE_USER) ? '0' : '1';
		$statusFilter = isset($_POST['statusFilter']) ? $_POST['statusFilter'] : '';
		$itemStatus = Array();
		$sort = (isset($_POST['sort'])) ? $_POST['sort'] : '';
        $dir = (isset($_POST['dir'])) ? $_POST['dir'] : '';

		$smarty = SmartyObj::newSmarty('CollectFromStore');

		switch ($statusFilter)
		{
			case 'S_SHIPPED_NOT_RECEIVED':
				$itemStatus[] = TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE;
				$itemStatus[] = TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_DISTRIBUTION_CENTRE;
				$itemStatus[] = TPX_ITEM_STATUS_SHIPPED_TO_STORE_FROM_DISTRIBUTION_CENTRE;
				$itemStatus[] = TPX_ITEM_STATUS_SHIPPED_TO_STORE_DIRECTLY;
				break;
			case 'S_RECEIVED_NOT_COLLECTED':
				$itemStatus[] = TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE;
				break;
			case 'S_COLLECTED':
				$itemStatus[] = TPX_ITEM_STATUS_SHIPPED_COLLECTED_BY_CUSTOMER;
				break;
			case 'DC_SHIPPED_NOT_RECEIVED':
				$itemStatus[] = TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE;
				break;
			case 'DC_RECEIVED':
				$itemStatus[] = TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_DISTRIBUTION_CENTRE;
				break;
			case 'DC_SHIPPED_TO_STORE':
				$itemStatus[] = TPX_ITEM_STATUS_SHIPPED_TO_STORE_FROM_DISTRIBUTION_CENTRE;
				$itemStatus[] = TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE;
				$itemStatus[] = TPX_ITEM_STATUS_SHIPPED_COLLECTED_BY_CUSTOMER;
				break;
			default:
				$itemStatus[] = TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE;
				$itemStatus[] = TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_DISTRIBUTION_CENTRE;
				$itemStatus[] = TPX_ITEM_STATUS_SHIPPED_TO_STORE_FROM_DISTRIBUTION_CENTRE;
				$itemStatus[] = TPX_ITEM_STATUS_SHIPPED_TO_STORE_DIRECTLY;
		}
		$itemStatus = join(',', $itemStatus);

		if ($siteType == '0')
		{
			$storeOwner = '-1';
			$dcOwner = $gSession['userdata']['userowner'];
		}
		else
		{
			$storeOwner = $gSession['userdata']['userowner'];
			$dcOwner = '-1' ;
		}

		$searchFields = UtilsObj::getPOSTParam('fields');
		$searchQuery = isset($_POST['query']) ? $_POST['query'] : '';

		$ordersArray = DatabaseObj::getOrderListCollectFromStore($dcOwner, $storeOwner, '**ALL**', 0, '', 0, '0', $itemStatus, '0', $dir, $gSession['browserlanguagecode'], $sort, $searchFields, $searchQuery);

		$resultArray = Array();
		$bufArray = Array();

		for ($i = 0, $address = ''; $i < count($ordersArray['items']); $i++)
		{
			$theOrderItem = $ordersArray['items'][$i];

			$bufArray['id'] = '"' . $theOrderItem['id'] . '"';
			$bufArray['orderDate'] = '"' . $theOrderItem['orderdate'] . '"';
			$bufArray['orderNumber'] = '"' . $theOrderItem['ordernumber'] . '"';
			$bufArray['productName'] = '"' . UtilsObj::encodeString($theOrderItem['productname'], true) . '"';
			$bufArray['qty'] = '"' . $theOrderItem['qty'] . '"';
			$bufArray['paymentConfirmed'] = '"' . $theOrderItem['paymentreceived'] . '"';

			switch ($theOrderItem['status'])
			{
				case TPX_ITEM_STATUS_SHIPPED_TO_DISTRIBUTION_CENTRE:
					$bufArray['status'] = '"' . $smarty->get_config_vars('str_ShippedToDistributionCentre') . '"';
					break;
				case TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_DISTRIBUTION_CENTRE:
					$bufArray['status'] = '"' . $smarty->get_config_vars('str_ReceivedAtDistributionCentre') . '"';
					break;
				case TPX_ITEM_STATUS_SHIPPED_TO_STORE_FROM_DISTRIBUTION_CENTRE:
					$bufArray['status'] = '"' . $smarty->get_config_vars('str_ShippedToStoreFromDistributionCentre') . '"';
					break;
				case TPX_ITEM_STATUS_SHIPPED_TO_STORE_DIRECTLY:
					$bufArray['status'] = '"' . $smarty->get_config_vars('str_ShippedToStoreDirectly') . '"';
					break;
				case TPX_ITEM_STATUS_SHIPPED_RECEIVED_AT_STORE:
					$bufArray['status'] = '"' . $smarty->get_config_vars('str_ReceivedAtStore') . '"';
					break;
				case TPX_ITEM_STATUS_SHIPPED_COLLECTED_BY_CUSTOMER:
					$bufArray['status'] = '"' . $smarty->get_config_vars('str_CollectedByCustomer') . '"';
					break;
				default:
					$bufArray['status'] = '""';
			}

			$bufArray['orderTotal'] = '"' . $theOrderItem['ordertotal'] . '"';
			$bufArray['billingCompany'] = '"' . UtilsObj::encodeString($theOrderItem['companyname'], true) . '"';

			$address = '';
			if ($theOrderItem['billingcustomeraddress1'] != '')
			{
				$address .= UtilsObj::encodeString($theOrderItem['billingcustomeraddress1'], true) . '<br>';
			}

			if ($theOrderItem['billingcustomeraddress2'] != '')
			{
				$address .= UtilsObj::encodeString($theOrderItem['billingcustomeraddress2'], true) . '<br>';
			}

			if ($theOrderItem['billingcustomeraddress3'] != '')
			{
				$address .= UtilsObj::encodeString($theOrderItem['billingcustomeraddress3'], true) . '<br>';
			}

			if ($theOrderItem['billingcustomeraddress4'] != '')
			{
				$address .= UtilsObj::encodeString($theOrderItem['billingcustomeraddress4'], true) . '<br>';
			}

			if ($theOrderItem['billingcustomercity'] != '')
			{
				$address .= UtilsObj::encodeString($theOrderItem['billingcustomercity'], true) . '<br>';
			}

			if ($theOrderItem['billingcustomercounty'] != '')
			{
				$address .= UtilsObj::encodeString($theOrderItem['billingcustomercounty'], true) . '<br>';
			}

			if ($theOrderItem['billingcustomerstate'] != '')
			{
				$address .= UtilsObj::encodeString($theOrderItem['billingcustomerstate'], true) . '<br>';
			}

			if ($theOrderItem['billingcustomerregion'] != '')
			{
				$address .= UtilsObj::encodeString($theOrderItem['billingcustomerregion'], true) . '<br>';
			}

			if ($theOrderItem['billingcustomerpostcode'] != '')
			{
				$address .= UtilsObj::encodeString($theOrderItem['billingcustomerpostcode'], true) . '<br>';
			}

			if ($theOrderItem['billingcustomercountryname'] != '')
			{
				$address .= UtilsObj::encodeString($theOrderItem['billingcustomercountryname'], true) . '<br>';
			}

			$bufArray['billingAddress'] = '"' . $address . '"';
			$bufArray['billingAddress1'] = '"' . UtilsObj::encodeString($theOrderItem['billingcustomeraddress1'], true) . '"';
			$bufArray['billingAddress2'] = '"' . UtilsObj::encodeString($theOrderItem['billingcustomeraddress2'], true) . '"';
			$bufArray['billingAddress3'] = '"' . UtilsObj::encodeString($theOrderItem['billingcustomeraddress3'], true) . '"';
			$bufArray['billingAddress4'] = '"' . UtilsObj::encodeString($theOrderItem['billingcustomeraddress4'], true) . '"';
			$bufArray['billingCity'] = '"' . UtilsObj::encodeString($theOrderItem['billingcustomercity'], true) . '"';
			$bufArray['billingCounty'] = '"' . UtilsObj::encodeString($theOrderItem['billingcustomercounty'], true) . '"';
			$bufArray['billingState'] = '"' . UtilsObj::encodeString($theOrderItem['billingcustomerstate'], true) . '"';
			$bufArray['billingPostCode'] = '"' . UtilsObj::encodeString($theOrderItem['billingcustomerpostcode'], true) . '"';
			$bufArray['billingCountry'] = '"' . UtilsObj::encodeString($theOrderItem['billingcustomercountryname'], true) . '"';
			$bufArray['billingTelephone'] = '"' . UtilsObj::encodeString($theOrderItem['billingcustomertelephonenumber'], true) . '"';
			$bufArray['billingEmail'] = '"' . UtilsObj::encodeString($theOrderItem['billingcustomeremailaddress'], true) . '"';
			$bufArray['billingContactFirstName'] = '"'.UtilsObj::encodeString($theOrderItem['contactfirstname'], true) . '"';
			$bufArray['billingContactLastName']  = '"' . UtilsObj::encodeString($theOrderItem['contactlastname'], true) . '"';
			$bufArray['paymentConfirmedDate'] = '"' . $theOrderItem['paymentreceiveddate'] . '"';
			$bufArray['bookedConfirmed'] = '"' . $theOrderItem['bookedconfirmed'] . '"';
			$bufArray['bookedConfirmedDate'] = '"' . $theOrderItem['bookedconfirmeddate'] . '"';
			$bufArray['bookedConfirmedTime'] = '"' . $theOrderItem['bookedconfirmedtime'] . '"';
			$bufArray['collectedConfirmed'] = '"' . $theOrderItem['collectedConfirmed'] . '"';
			$bufArray['collectedConfirmedDate'] = '"' . $theOrderItem['collectedConfirmedDate'] . '"';
			$bufArray['collectedConfirmedTime'] = '"' . $theOrderItem['collectedConfirmedTime'] . '"';
			$bufArray['storeCode'] = '"' . $theOrderItem['storeCode'] . '"';
			$bufArray['bookedDCConfirmed'] = '"' . $theOrderItem['bookedDCConfirmed'] . '"';
			$bufArray['bookedDCConfirmedDate'] = '"' . $theOrderItem['bookedDCConfirmedDate'] . '"';
			$bufArray['bookedDCConfirmedTime'] = '"' . $theOrderItem['bookedDCConfirmedTime'] . '"';
			$bufArray['shippedToStoreConfirmed'] = '"' . $theOrderItem['shippedToStoreConfirmed'] . '"';
			$bufArray['shippedToStoreConfirmedDate'] = '"' . $theOrderItem['shippedToStoreConfirmedDate'] . '"';
			$bufArray['shippedToStoreConfirmedTime'] = '"' . $theOrderItem['shippedToStoreConfirmedTime'] . '"';
			$bufArray['statusOriginal'] = '"' . $theOrderItem['status'] .'"';
			$bufArray['shippingRef'] = '"' . $theOrderItem['shippingRef'] . '"';

			$resultArray[] = $bufArray;
		}

		return $resultArray;
	}

	static function sendEmail($recordId, $bookedStoreType)
	{
		$shipped = false;

		$dbObj = DatabaseObj::getGlobalDBConnection();
	    if ($dbObj)
    	{
		if ($stmt = $dbObj->prepare('SELECT oi.currentowner, oi.orderid, os.distributioncentrecode, `storecode`, ds.name, ds.siteonline, ds.smtpproductionname, ds.smtpproductionaddress,
			ss.telephonenumber, ss.emailaddress, ss.contactfirstname, ss.contactlastname, ss.siteonline, ss.openingtimes, ss.storeurl, ss.smtpproductionname, ss.smtpproductionaddress,
			ss.address1, ss.address2, ss.address3, ss.address4, ss.city, ss.county, ss.state, ss.regioncode, ss.region, ss.postcode, ss.countryname, ss.name
		    FROM `ORDERITEMS` oi
		    LEFT JOIN `ORDERSHIPPING` os ON os.orderid = oi.orderid
		    LEFT JOIN `SITES` ds ON ds.code = os.distributioncentrecode
		    LEFT JOIN `SITES` ss ON ss.code = `storecode`
		    WHERE oi.id = ?'))
		{
			if ($stmt->bind_param('i', $recordId))
			{
				if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						if ($stmt->num_rows > 0)
						{
							if ($stmt->bind_result($currentOwnerSite, $orderID, $distributionCentreCode, $storeCode, $distributionCentreName, $distributionCentreOnline, $distributionCentreNotifyEmailName,
								$distributionCentreNotifyEmailAddress, $storeTelephoneNumber, $storeEmailAddress, $storeContactFirstName, $storeContactLastName, $storeOnline, $storeOpeningTimes,
								$storeURL, $storeNotifyEmailName, $storeNotifyEmailAddress,
								$storeAddress1, $storeAddress2, $storeAddress3, $storeAddress4, $storeCity, $storeCounty, $storeState, $storeRegionCode, $storeRegion, $storePostCode, $storeCountry, $storeName
								))
							{
								if ($stmt->fetch())
								{
									if ($storeCode != '')
									{
										$storeContactName = $storeContactFirstName . ' ' . $storeContactLastName;
									}
								}
								else
								{
									$error = 'sendEmail fetch ' . $dbObj->error;
								}
							}
							else
							{
								$error = 'sendEmail bind result ' . $dbObj->error;
							}
						}
						else
						{
							$error = 'sendEmail num_rows ' . $dbObj->error;
						}
					}
					else
					{
						$error = 'sendEmail store results ' . $dbObj->error;
					}
				}
				else
				{
					$error = 'sendEmail execute ' . $dbObj->error;
				}
			}
			else
			{
				$error = 'sendEmail bind params ' . $dbObj->error;
			}
			$stmt->free_result();
            $stmt->close();
            $stmt = null;
		}
		else
		{
			$error = 'sendEmail prepare ' . $dbObj->error;
		}


		if ($bookedStoreType == '1')
		{
			// shipped from store to customer
			if ($stmt = $dbObj->prepare('SELECT `useremaildestination` FROM `ORDERHEADER` WHERE `id` = ?'))
            {
            	if ($stmt->bind_param('i', $orderID))
                {
        			if ($stmt->execute())
					{
        				if ($stmt->store_result())
						{
            				if ($stmt->num_rows > 0)
							{
			                	if ($stmt->bind_result($userEmailDestination))
			                    {
		                        	if ($stmt->fetch())
		                            {
	                                	$shipped = true;
		                            }
		                            else
		                            {
		                            	$error = 'sendEmail fetch ' . $dbObj->error;
		                            }
			                    }
			                    else
			                    {
			                    	$error = 'functionName bind result ' . $dbObj->error;
			                    }
			                }
		                }
		                else
		                {
		                	$error = 'functionName store result ' . $dbObj->error;
		                }
	                }
	                else
	                {
	                	$error = 'functionName execute ' . $dbObj->error;
	                }
                }
                else
                {
                	$error = 'functionName bind params ' . $dbObj->error;
                }
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }
            else
            {
            	$error = 'functionName prepare ' . $dbObj->error;
            }
		}

		if (($shipped == true) && ($storeOnline == '1'))
		{
			$orderLanguage = DatabaseObj::getOrderLanguage($orderID);
			$jobTicketArray = DatabaseObj::getJobTicket($recordId, $orderLanguage);
			$webBrandArray = AuthenticateObj::getWebBrandData($jobTicketArray['webbrandcode']);
			$useraccount = DatabaseObj::getUserAccountFromID($jobTicketArray['userid']);
			$loginname = $useraccount['login'];
			$billingAddress = UtilsAddressObj::formatAddress($jobTicketArray, 'billing', "\n");

			if ($bookedStoreType == '0')
			{
				// shipped from DC to store
				$emailTemplate = 'store_ordershipped';
				$emailName = $storeNotifyEmailName;
				$emailAddress = $storeNotifyEmailAddress;
				$emailNameBCC = '';
				$emailAddressBCC = '';
			}
			else
			{
				// Since we are using collect from store we only have billing email address (shipping address is the store address)
				// so it is not neccessary to check the email desitination settings, we always use the billing email to notify customer.
				$emailTemplate = 'customer_orderreadytocollect';
				$emailName = $jobTicketArray['billingcontactfirstname'] . ' ' . $jobTicketArray['billingcontactlastname'];
				$emailAddress = $jobTicketArray['billingcustomeremailaddress'];
				$emailNameBCC = '';
				$emailAddressBCC = '';
			    $storeOpeningTimes = LocalizationObj::getLocaleString($storeOpeningTimes, $orderLanguage, true);
			    $storeOpeningTimes = str_replace('\\n', "\n", $storeOpeningTimes);

				$jobTicketArray['shippingcontactfirstname'] = $storeContactFirstName;
				$jobTicketArray['shippingcontactlastname']	= $storeContactLastName;
				$jobTicketArray['shippingcustomername']	= $storeName;
				$jobTicketArray['shippingcustomeraddress1']	= $storeAddress1;
				$jobTicketArray['shippingcustomeraddress2']	= $storeAddress2;
				$jobTicketArray['shippingcustomeraddress3']	= $storeAddress3;
				$jobTicketArray['shippingcustomeraddress4']	= $storeAddress4;
				$jobTicketArray['shippingcustomercity']	= $storeCity;
				$jobTicketArray['shippingcustomercounty']	= $storeCounty;
				$jobTicketArray['shippingcustomerstate']	= $storeState;
				$jobTicketArray['shippingcustomerregioncode']	= $storeRegionCode;
				$jobTicketArray['shippingcustomerregion']	= $storeRegion;
				$jobTicketArray['shippingcustomerpostcode']	= $storePostCode;
				$jobTicketArray['shippingcustomercountryname']	= $storeCountry;
			}

			$shippingAddress = UtilsAddressObj::formatAddress($jobTicketArray, 'shipping', "\n");

			// check if shipping email should be sent
			if ($emailTemplate == 'store_ordershipped')
			{
				if (($webBrandArray['webbrandcode'] != '') && ($webBrandArray['usedefaultemailsettings'] == 0))
                {
                	$sendNotification = $webBrandArray['smtpshippingactive'];
                }
                else
                {
                	$brandingDefaults = DatabaseObj::getBrandingFromCode('');
                	$sendNotification = $brandingDefaults['smtpshippingactive'];
                }
			}
			else
			{
				$sendNotification = true;
			}

		   	if ($sendNotification == true)
		   	{
		   		// only send the email if we have an email address
				if ($emailAddress != '')
				{
					$emailObj = new TaopixMailer();
           	 		$emailObj->sendTemplateEmail($emailTemplate, $webBrandArray['webbrandcode'], $webBrandArray['webbrandapplicationname'],
                		$webBrandArray['webbranddisplayurl'], $orderLanguage,
                    	$emailName, $emailAddress,
                    	$emailNameBCC, $emailAddressBCC, 0,
                    	Array(
                    		'orderid' => $jobTicketArray['orderid'],
							'orderitemid' => $jobTicketArray['recordid'],
                        	'userid' => $jobTicketArray['userid'],
                        	'loginname' => $loginname,
                        	'currencycode' => $jobTicketArray['currencycode'],
                        	'currencyname' => $jobTicketArray['currencyname'],
        			    	'ordernumber' => $jobTicketArray['ordernumber'],
                			'qty' => $jobTicketArray['qty'],
               				'pagecount' => $jobTicketArray['pagecount'],
							'projectname' => $jobTicketArray['projectname'],
               				'productcode' => $jobTicketArray['productcode'],
                        	'productname' => $jobTicketArray['productname'],
                        	'defaultcovercode' => $jobTicketArray['defaultcovercode'],
                        	'defaultpapercode' => $jobTicketArray['defaultpapercode'],
                        	'defaultpagecount' => $jobTicketArray['defaultpagecount'],
                        	'covercount' => $jobTicketArray['covercount'],
                        	'covercode' => $jobTicketArray['covercode'],
                        	'covername' => $jobTicketArray['covername'],
                        	'papercount' => $jobTicketArray['papercount'],
                        	'papercode' => $jobTicketArray['papercode'],
                        	'papername' => $jobTicketArray['papername'],
                        	'vouchercode' => $jobTicketArray['vouchercode'],
                        	'vouchername' => $jobTicketArray['vouchername'],
                        	'ordertotal' => $jobTicketArray['ordertotal'],
                        	'formattedordertotal' => $jobTicketArray['formattedordertotal'],
              				'shippingcontactname' => $jobTicketArray['shippingcontactfirstname'] . ' ' . $jobTicketArray['shippingcontactlastname'],
                        	'shippingcontactfirstname' => $jobTicketArray['shippingcontactfirstname'],
                        	'shippingcontactlastname' => $jobTicketArray['shippingcontactlastname'],
                       		'shippingaddress' => $shippingAddress,
                        	'shippingmethodname' => $jobTicketArray['shippingmethodname'],
                        	'shippingmethod' => $jobTicketArray['shippingmethodname'], // leave 'shippingmethod' in in order not to break existing templates, but really it should be 'shippingmethodname'
                        	'shippingqty' => $jobTicketArray['shippingqty'],
                        	'shippingcustomername' => $jobTicketArray['shippingcustomername'],
                        	'shippingcustomeraddress1' => $jobTicketArray['shippingcustomeraddress1'],
                        	'shippingcustomeraddress2' => $jobTicketArray['shippingcustomeraddress2'],
                        	'shippingcustomeraddress3' => $jobTicketArray['shippingcustomeraddress3'],
                        	'shippingcustomeraddress4' => $jobTicketArray['shippingcustomeraddress4'],
                        	'shippingcustomercity' => $jobTicketArray['shippingcustomercity'],
                        	'shippingcustomercounty' => $jobTicketArray['shippingcustomercounty'],
                        	'shippingcustomerstate' => $jobTicketArray['shippingcustomerstate'],
                        	'shippingcustomerregioncode' => $jobTicketArray['shippingcustomerregioncode'],
                       		'shippingcustomerregion' => $jobTicketArray['shippingcustomerregion'],
                        	'shippingcustomerpostcode' => $jobTicketArray['shippingcustomerpostcode'],
                        	'shippingcustomercountrycode' => $jobTicketArray['shippingcustomercountrycode'],
                        	'shippingcustomercountryname' => $jobTicketArray['shippingcustomercountryname'],
                        	'shippingcustomertelephonenumber' => $jobTicketArray['shippingcustomertelephonenumber'],
                        	'shippingcustomeremailaddress' => $jobTicketArray['shippingcustomeremailaddress'],
                        	'shippingmethodcode' => $jobTicketArray['shippingmethodcode'],
                        	'shippingratecode' => $jobTicketArray['shippingratecode'],
                        	'shippingrateinfo' => $jobTicketArray['shippingrateinfo'],
                        	'shippingratecost' => $jobTicketArray['shippingratecost'],
                        	'shippingratesell' => $jobTicketArray['shippingratesell'],
                        	'shippingratetaxcode' => $jobTicketArray['shippingratetaxcode'],
                       		'shippingratetaxname' => $jobTicketArray['shippingratetaxname'],
                       		'shippingratetaxrate' => $jobTicketArray['shippingratetaxrate'],
                        	'shippingratecalctax' => $jobTicketArray['shippingratecalctax'],
                        	'shippingratetaxtotal' => $jobTicketArray['shippingratetaxtotal'],
                        	'shippeddate' => $jobTicketArray['shippeddate'],
                        	'formattedshippeddatetime' => $jobTicketArray['formattedshippeddatetime'],
                        	'formattedshippeddate' => $jobTicketArray['formattedshippeddate'],
                        	'formattedshippedtime' => $jobTicketArray['formattedshippedtime'],
                        	'shippingtrackingreference' => $jobTicketArray['shippingtrackingreference'],
                        	'orderdate' => $jobTicketArray['orderdate'],
                        	'formattedorderdatetime' => $jobTicketArray['formattedorderdatetime'],
                        	'formattedorderdate' => $jobTicketArray['formattedorderdate'],
                        	'formattedordertime' => $jobTicketArray['formattedordertime'],
                        	'distributioncentrecode' => $distributionCentreCode,
                        	'distributioncentrename' => $distributionCentreName,
                        	'storecode' => $storeCode,
                        	'storeopeningtimes' => $storeOpeningTimes,
                        	'storeurl' => $storeURL,
                        	'storeemailaddress' => $storeEmailAddress,
                        	'storetelephonenumber' => $storeTelephoneNumber,
                        	'storecontactname' => $storeContactName,
                        	'storeonline' => $storeOnline,
                        	'billingcontactname' => $jobTicketArray['billingcontactfirstname'] . ' ' . $jobTicketArray['billingcontactlastname'],
                        	'billingcontactfirstname' => $jobTicketArray['billingcontactfirstname'],
                        	'billingcontactlastname' => $jobTicketArray['billingcontactlastname'],
							'billingcustomerregisteredtaxnumbertype' => $jobTicketArray['billingcustomerregisteredtaxnumbertype'],
							'billingcustomerregisteredtaxnumber' => $jobTicketArray['billingcustomerregisteredtaxnumber'],
                        	'billingaddress' => $billingAddress,
                        	'billingcustomeraccountcode' => $jobTicketArray['billingcustomeraccountcode'],
                        	'billingcustomername' => $jobTicketArray['billingcustomername'],
                        	'billingcustomeraddress1' => $jobTicketArray['billingcustomeraddress1'],
                        	'billingcustomeraddress2' => $jobTicketArray['billingcustomeraddress2'],
                        	'billingcustomeraddress3' => $jobTicketArray['billingcustomeraddress3'],
                        	'billingcustomeraddress4' => $jobTicketArray['billingcustomeraddress4'],
                        	'billingcustomercity' => $jobTicketArray['billingcustomercity'],
                        	'billingcustomercounty' => $jobTicketArray['billingcustomercounty'],
                       		'billingcustomerstate' => $jobTicketArray['billingcustomerstate'],
                       		'billingcustomerregioncode' => $jobTicketArray['billingcustomerregioncode'],
                      		'billingcustomerregion' => $jobTicketArray['billingcustomerregion'],
                     		'billingcustomerpostcode' => $jobTicketArray['billingcustomerpostcode'],
                  			'billingcustomercountrycode' => $jobTicketArray['billingcustomercountrycode'],
                  			'billingcustomercountryname' => $jobTicketArray['billingcustomercountryname'],
                   			'billingcustomertelephonenumber' => $jobTicketArray['billingcustomertelephonenumber'],
                  			'billingcustomeremailaddress' => $jobTicketArray['billingcustomeremailaddress'],
                  			'paymentmethodname' => $jobTicketArray['paymentmethodname'],
							'targetuserid' => $jobTicketArray['userid']),
                    		'', ''
                  		);
            		}
				}
			}
		}
    }
}

?>
