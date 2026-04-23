<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsDatabase.php');

require_once('../AppProductionAPI/AppProductionAPI_model.php');

class AdminProduction_model {

	static function initialize()
	{
		$resultArray = [];
		$resultArray['productionsites'] = AppProductionAPI_model::getProductionSites();	
        $resultArray['prefdata'] = self::getPrefDataForCurrentUser();
		return $resultArray;
	}

	static function getListData()
	{
		$resultArray = AppProductionAPI_model::getProductionQueue();
		return $resultArray;
	}

	static function orderDetailsDisplay()
	{
		$resultArray = AppProductionAPI_model::getJobInfo();
		$resultArray['originalorder'] = DatabaseObj::getOriginalOrderLineFromUploadRef($resultArray['jobticket']['uploadref'], UtilsObj::getGETParam('id'));
		return $resultArray;
	}

    static function preferencesDisplay()
    {
        return self::getPrefDataForCurrentUser();
    }

	static function getPrefDataForCurrentUser()
	{
        global $gSession;
        $userID = $gSession['userid'];
        $data = '';
        $dataLength = 0;
        $prefType = 'PRODUCTIONVIEW';
        $prefArray = Array();
        $result = '';
        $resultParam = '';
		$resultArray = Array();

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$query = 'SELECT `data`,`datalength` FROM `USERSYSTEMPREFERENCES` WHERE `userid` = ? AND `type` = ?';

			$stmt = $dbObj->prepare($query);
			if ($stmt)
			{
				if ($stmt->bind_param('is', $userID, $prefType))
				{
					if (! $stmt->execute())
					{
                        $result = 'str_DatabaseError';
						$resultParam = 'preferencesDisplay execute ' . $dbObj->error;
					}
					else
					{
						if ($stmt->store_result())
						{
							if ($stmt->bind_result($data, $dataLength))
							{
								if ($stmt->fetch())
								{
                                    if ($dataLength > 0)
                                    {
									    $data = gzuncompress($data, $dataLength);
                                    }

                                    $prefArray[] = ['data' => $data];
								}
								else
								{
									$result = 'str_DatabaseError';
						            $resultParam = 'preferencesDisplay fetch ' . $dbObj->error;
								}
							}
							else
							{
								$result = 'str_DatabaseError';
	                            $resultParam = 'preferencesDisplay bind result ' . $dbObj->error;
							}
						}
					}
				}
				else
				{
					$result = 'str_DatabaseError';
	                $resultParam = 'preferencesDisplay bind params ' . $dbObj->error;
				}
			}
			else
			{
				$result = 'str_DatabaseError';
	            $resultParam = 'preferencesDisplay prepare statement ' . $dbObj->error;
			}
		}
		else
		{
			$result = 'str_DatabaseError';
	        $resultParam = 'preferencesDisplay dbconn ' . $dbObj->error;
		}

        $resultArray['data'] = $prefArray;
        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

		return $resultArray;
	}

	static function updatePreferences($pParamArray)
	{	
        global $gSession;
        $dataLength = 0;
        $data = $pParamArray['data'];
        $userID = $gSession['userid'];
        $prefType = 'PRODUCTIONVIEW';

        $dataLength = strlen($data);

		if ($dataLength > 49152)
		{
			$data = gzcompress($data, 9);
		}
        else 
        {
            $dataLength = 0;
        }

        $result = '';
        $resultParam = '';
		$resultArray = Array();
		
        $dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
	        if ($stmt = $dbObj->prepare('INSERT INTO `USERSYSTEMPREFERENCES` (`type`,`userid`,`data`,`datalength`) 
                                        VALUES (?, ?, ?, ?)
                                        ON DUPLICATE KEY UPDATE `data` = ?,`datalength` = ?'))
	        {
	            if ($stmt->bind_param('sssisi', $prefType, $userID, $data, $dataLength, $data, $dataLength))
	            {
	                if (!$stmt->execute())
	                {
	                	// could not execute statement
						$result = 'str_DatabaseError';
						$resultParam = 'updatePreferences execute ' . $dbObj->error;
	                }
	            }
	            else
	            {
	                // could not bind parameters
	                $result = 'str_DatabaseError';
	                $resultParam = 'updatePreferences bind ' . $dbObj->error;
	            }
				$stmt->free_result();
	            $stmt->close();
	            $stmt = null;
	        }
	        else
	        {
	            // could not prepare statement
	            $result = 'str_DatabaseError';
	            $resultParam = 'updatePreferences prepare ' . $dbObj->error;
	        }
	    }

	    $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

	    return $resultArray;
	}

	static function updateItemOnHoldStatus($pParamArray)
    {
        global $gSession;

        $orderItemIDList = (string) $pParamArray['orderitemidlist'];
        $itemOnHoldStatus = (int) $pParamArray['onholdstatus'];
        $itemOnHoldReason = $pParamArray['onholdreason'];
        $userID = $gSession['userid'];

        return AppProductionAPI_model::updateItemOnHoldStatus($orderItemIDList, $userID, $itemOnHoldStatus, $itemOnHoldReason);
    }

	static function updateOrderPaymentStatus($pParamArray)
    {
        global $gSession;

        $orderIDList = (string) $pParamArray['orderidlist'];
        $orderPaymentReceived = (int) $pParamArray['paymentreceived'];
        $orderPaymentReceivedDate = $pParamArray['paymentreceiveddate'];
        $userID = $gSession['userid'];

        AppProductionAPI_model::updateOrderPaymentStatus($orderIDList, $userID, $orderPaymentReceived, $orderPaymentReceivedDate);
    }

	static function updateItemCanUploadFilesStatus($pParamArray)
    {
        $orderItemID = $pParamArray['orderitemid'];
        $canUploadFiles = $pParamArray['canuploadfiles'];

        AppProductionAPI_model::updateItemCanUploadFilesStatus($orderItemID, $canUploadFiles);
    }

	static function updateOverrideSaveStatus($pParamArray)
    {
        $orderItemID = $pParamArray['orderitemid'];
        $overrideSave = $pParamArray['overridesave'];

        AppProductionAPI_model::updateItemCanUploadFilesOverrideSaveStatus($orderItemID, $overrideSave);
    }

	static function updateItemCanModifyStatus($pParamArray)
	{
		$orderItemID = $pParamArray['orderitemid'];
        $canModify = $pParamArray['canmodify'];

		AppProductionAPI_model::updateItemCanModifyStatus($orderItemID, $canModify);
	}

	static function updateItemCanUploadFilesOverrideProductCodeStatus($pParamArray)
	{
		$orderItemID = $pParamArray['orderitemid'];
        $overrideProductCode = $pParamArray['overrideproductcode'];

		AppProductionAPI_model::updateItemCanUploadFilesOverrideProductCodeStatus($orderItemID, $overrideProductCode);
	}

	static function updateItemCanUploadFilesOverridePageCountStatus($pParamArray)
	{
		$orderItemID = $pParamArray['orderitemid'];
		$overridePageCount = $pParamArray['overridepagecount'];

		AppProductionAPI_model::updateItemCanUploadFilesOverridePageCountStatus($orderItemID, $overridePageCount);
	}

	static function updateItemActiveStatus($pParamArray)
	{
		$orderItemID = $pParamArray['orderitemid'];
		$userId = $pParamArray['userid'];
		$itemActiveStatus = $pParamArray['itemactivestatus'];

		AppProductionAPI_model::updateItemActiveStatus($orderItemID, $userId, $itemActiveStatus);
	}

	static function updateItemStatus($pParamArray)
	{
		$orderItemID = $pParamArray['orderitemid'];
		$itemStatus = $pParamArray['itemstatus'];
		$itemDescription = '';

		AppProductionAPI_model::updateItemStatus($orderItemID, $itemStatus, $itemDescription);
	}

	static function updateItemShippingStatus($pParamArray)
	{
		$userID = $pParamArray['userid'];
		$itemShippingDate = $pParamArray['itemshippingdate'];
		$itemTrackingReference = $pParamArray['itemtrackingref'];
		$sendEmail = true;

		$idArray = explode(",", $pParamArray['idlist']);

		foreach ($idArray as $orderData)
		{
			$orderItem = explode(":", $orderData);
			AppProductionAPI_model::updateItemShippingStatus($orderItem[0], $orderItem[1], $userID, $itemShippingDate, $itemTrackingReference, $sendEmail);
		}
	}

	static function statusCheck($pParamArray)
	{
		$dataArray = json_decode($pParamArray['data']);
		$idlist = [];
		$match = true;

		for ($i = 0; $i < count($dataArray); $i++)
		{
			$idlist[] = $dataArray[$i]->id;
		}

		$currentStatusData = self::getCurrentData($idlist);

		for ($i = 0; $i < count($dataArray); $i++)
		{
			$id = $dataArray[$i]->id;
			$statusid = $dataArray[$i]->statusid;
			$activestatus = $dataArray[$i]->activestatus;

			$currentstatusid = $currentStatusData['data'][$id]['status'];
			$currentactivestatus = $currentStatusData['data'][$id]['activestatus'];

			if (($statusid != $currentstatusid) || ($activestatus != $currentactivestatus)) 
			{
				$match = false;
			}
		}

		return $match;
		
	}

	static function getCurrentData($pIdList)
	{
		$result = '';
        $resultParam = '';
		$resultArray = Array();

		$data = Array();
		$orderItem = Array();
		$id = 0;
		$activestatus = 0;
		$status = 0;

		$itemCount = count($pIdList);

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{

			$sql = 'SELECT `id`,`active`, `status` FROM `ORDERITEMS` WHERE `id` IN (';
			$bindParamArray = Array();
			$bindParamArray[0] = '';

			for ($i = 0; $i < $itemCount; $i++)
			{
				$bindParamArray[0] .= 'i';
				$bindParamArray[] = $pIdList[$i];
			}

			$sql .= str_repeat('?,', $itemCount - 1) . '?)';

			$stmt = $dbObj->prepare($sql);

			if ($stmt) {
				$bindOK = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParamArray));
				if ($bindOK) {
					if ($stmt->execute()) {
						if ($stmt->store_result()) {
							if ($stmt->num_rows > 0) {
								if ($stmt->bind_result($id, $activestatus, $status)) {
									while ($stmt->fetch()) {
										$orderItem['id'] = $id;
										$orderItem['activestatus'] = $activestatus;
										$orderItem['status'] = $status;
										$data[$id] = $orderItem;
									}
								} else {
									$returnArray['error'] = __FUNCTION__ . ' bind result error: ' . $dbObj->error;
								}
							}
						} else {
							$returnArray['error'] = __FUNCTION__ . ' store result error: ' . $dbObj->error;
						}
					} else {
						$returnArray['error'] = __FUNCTION__ . ' execute error: ' . $dbObj->error;
					}
				} else {
					$returnArray['error'] = __FUNCTION__ . ' bind param error: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}

			$dbObj->close();
		}
		else
		{
			$result = 'str_DatabaseError';
	        $resultParam = 'getCurrentData dbconn ' . $dbObj->error;
		}

        $resultArray['data'] = $data;
        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

		return $resultArray;
	}
}

?>