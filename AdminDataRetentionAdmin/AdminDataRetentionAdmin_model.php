<?php

require_once('../Utils/UtilsDatabase.php');

class AdminDataRetentionAdmin_model
{
	private static $lookupOrder = array('guest', 'unsaved', 'notordered', 'ordered', 'orderedunused', 'notorderedunused');

	/**
	 * Format the policy data into the format required when being passed to the grid.
	 *
	 * @return string
	 */
    static function getGridData()
    {
		$policyList = DatabaseObj::getOnlineDataPolicyList();
		$policyList['grid'] = array();

		foreach ($policyList['data'] as $thePolicy)
		{
			$gridDataArray = array();

			$gridDataArray[] = "'" . $thePolicy['id'] . "'";
			$gridDataArray[] = "'" . $thePolicy['datecreated'] . "'";
			$gridDataArray[] = "'" . $thePolicy['code'] . "'";
			$gridDataArray[] = "'" . $thePolicy['name'] . "'";
			$gridDataArray[] = "'" . $thePolicy['active'] . "'";

			$gridDataArray[] = "''";

			$policyList['grid'][] = '[' . implode(',', $gridDataArray) . ']';
		}

        return $policyList;
	}

	/**
	 * Build a default configuration for a new data policy
	 *
	 * @return array
	 */
    static function getDefaultValues()
    {
    	// set up the defaults for a new data policy
		$resultArray = array();

		$resultArray['id'] = 0;
		$resultArray['datecreated'] = '';
		$resultArray['code'] = '';
		$resultArray['name'] = '';
		$resultArray['active'] = 0;
		$resultArray['assignedtobrandslist'] = '';

		$resultArray['ordered'] = array('projects' => 0, 'age' => 365, 'days' => 60, 'email' => 0, 'emailfrequency' => 1, 'archiveactive' => 0, 'archivedays' => 7, 'total' => 365);
		$resultArray['notordered'] = array('projects' => 0, 'age' => 90, 'days' => 60, 'email' => 0, 'emailfrequency' => 1, 'archiveactive' => 0, 'archivedays' => 30, 'total' => 90);
		$resultArray['unsaved'] = array('projects' => 0, 'age' => 7, 'days' => 14, 'email' => 0, 'emailfrequency' => 1, 'total' => 7);
		$resultArray['guest'] = array('projects' => 0, 'age' => 4);
		$resultArray['orderedunused'] = array('assets' => 0, 'assetsage' => 90, 'total' => 90);
		$resultArray['notorderedunused'] = array('assets' => 0, 'assetsage' => 90, 'total' => 90);

        return $resultArray;
    }

	/**
	 * Create a 1 dimensional array of the data policy.
	 *
	 * @param array $pPolicyData
	 * @return array
	 */
	static function deflatePolicy($pPolicyData)
	{
		$resultArray['id'] = $pPolicyData['id'];
		$resultArray['code'] = $pPolicyData['code'];
		$resultArray['name'] = $pPolicyData['name'];
		$resultArray['active'] = $pPolicyData['active'];

		foreach (self::$lookupOrder as $policyKey)
		{
			foreach ($pPolicyData[$policyKey] as $policySettingKey => $policySetting)
			{
				$resultArray[$policyKey . $policySettingKey] = $policySetting;
			}
		}

        return $resultArray;
    }

	/**
	 * Create a string representation of the policy data, used to pass the policy data to the edit form.
	 *
	 * @param array $pPolicyData
	 * @return string
	 */
	static function flattenPolicy($pPolicyData)
	{
		$serialArray = array();

		$serialArray[] = '"id":"' . $pPolicyData['id'] . '"';
		$serialArray[] = '"code":"' . $pPolicyData['code'] . '"';
		$serialArray[] = '"name":"' . UtilsObj::encodeString($pPolicyData['name'], false) . '"';
		$serialArray[] = '"active":"' . $pPolicyData['active'] . '"';

		foreach (self::$lookupOrder as $policyKey)
		{
			foreach ($pPolicyData[$policyKey] as $policySettingKey => $policySetting)
			{
				$serialArray[] = '"' . $policyKey . $policySettingKey . '":"' . $pPolicyData[$policyKey][$policySettingKey] . '"';
			}
		}

		$resultString = '"data": {' . implode(',', $serialArray) . '}';

		return $resultString;
	}


	/**
	 * Create a multi-dimensional array from a 1 dimensional array received from the edit form submit (form post parameters)
	 *
	 * @param array $pPolicyData
	 * @return array
	 */
	static function inflatePolicy($pPolicyData)
	{
		$resultArray['id'] = $pPolicyData['id'];
		$resultArray['code'] = $pPolicyData['code'];
		$resultArray['name'] = $pPolicyData['name'];
		$resultArray['active'] = $pPolicyData['active'];

		$resultArray['ordered'] = array('projects' => 0, 'age' => 365, 'days' => 60, 'email' => 0, 'emailfrequency' => 0, 'archiveactive' => 0, 'archivedays' => 7, 'total' => 365);
		$resultArray['notordered'] = array('projects' => 0, 'age' => 90, 'days' => 60, 'email' => 0, 'emailfrequency' => 0, 'archiveactive' => 0, 'archivedays' => 30, 'total' => 90);
		$resultArray['unsaved'] = array('projects' => 0, 'age' => 7, 'days' => 14, 'email' => 0, 'emailfrequency' => 0, 'total' => 7);
		$resultArray['guest'] = array('projects' => 0, 'age' => 4);
		$resultArray['orderedunused'] = array('assets' => 0, 'assetsage' => 90, 'total' => 90);
		$resultArray['notorderedunused'] = array('assets' => 0, 'assetsage' => 90, 'total' => 90);

		foreach (self::$lookupOrder as $policyKey)
		{
			foreach ($resultArray[$policyKey] as $policySettingKey => $policySetting)
			{
				if (array_key_exists($policyKey . $policySettingKey, $pPolicyData))
				{
					$value = $pPolicyData[$policyKey . $policySettingKey];

					$resultArray[$policyKey][$policySettingKey] = $value;
				}
			}
		}

		return $resultArray;
	}

	/**
	 * Get a list of brand codes that has the data policy assigned.
	 *
	 * @param int $pPolicyID
	 * @return array List of brand codes.
	 */
	static function getBrandCodesLinkedToPolicy($pPolicyID)
	{
		$returnArray = UtilsObj::getReturnArray();
		$error = '';
		$brandCode = '';
		$brandCodeArray = array();
		$smarty = SmartyObj::newSmarty('');

		$sql = 'SELECT `id`, `code` FROM `BRANDING` WHERE `onlinedataretentionpolicy` = ?';

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare($sql))
			{
				$bindOK = $stmt->bind_param('i', $pPolicyID);
				if ($bindOK)
				{
					if ($stmt->execute())
					{
						if ($stmt->bind_result($id, $brandCode))
						{
							while ($stmt->fetch())
							{
								if ($brandCode == '')
								{
									$brandCode = $smarty->get_config_vars('str_LabelDefault');
								}

								$brandCodeArray[] = $brandCode;
							}
						}
						else
						{
							$error = __METHOD__ . ' bind result ' . $dbObj->error;
						}
                    }
                    else
                    {
                        $error = __METHOD__ . ': execute ' . $dbObj->error;
                    }
				}
				else
				{
					$error = __METHOD__ . ': bind_params ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$error = __METHOD__ . ': prepare ' . $dbObj->error;
			}

			$dbObj->close();
		}
		else
		{
			$error = __METHOD__ . ': connect ' . $dbObj->error;
		}

		$smarty = null;

		$returnArray['error'] = $error;
		$returnArray['data'] = $brandCodeArray;
		return $returnArray;
	}

	/**
	 * Insert a new policy into the database.
	 *
	 * @global array $gSession
	 * @param array $pPolicyData
	 * @return array
	 */
    static function dataPolicyAdd($pPolicyData)
    {
        global $gSession;

        $result = '';
        $resultParam = '';

		if (($pPolicyData['code'] != '') && ($pPolicyData['name'] != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
				$sqlQueryFields = array('`code`', '`name`', 'active');
				$sqlQueryParams = array('?', '?', '?');

				$bindParamArray = array('ssi');
				$bindParamArray[] = $pPolicyData['code'];
				$bindParamArray[] = $pPolicyData['name'];
				$bindParamArray[] = $pPolicyData['active'];

				foreach (self::$lookupOrder as $policyKey)
				{
					foreach ($pPolicyData[$policyKey] as $policySettingKey => $policySetting)
					{
						// Only include keys that are not called total as this is only used to display information in the admin panel.
						if (($policySettingKey != 'total') && (trim($policySetting) != ''))
						{
							$sqlQueryFields[] = '`' . $policyKey . $policySettingKey . '`';
							$sqlQueryParams[] = '?';
							$bindParamArray[0] .= 'i';
							$bindParamArray[] = $policySetting;
						}
					}
				}

				$sql = 'INSERT INTO `DATAPOLICIES`
							(`datecreated`, ' . implode(', ', $sqlQueryFields) . ')
						    VALUES (now(), ' . implode(', ', $sqlQueryParams) . ')';

				if ($stmt = $dbObj->prepare($sql))
                {
					$bindOK = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParamArray));
					if ($bindOK)
					{
						if ($stmt->execute())
						{
                            $pPolicyData['id'] = $dbObj->insert_id;

                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                'ADMIN', 'DATA-POLICY-ADD', $pPolicyData['id'] . ' ' . $pPolicyData['code'], 1);
						}
						else
						{
                            // could not execute statement
                            // first check for a duplicate key (Policy code)
                            if ($stmt->errno == 1062)
                            {
                                $result = 'str_ErrorPolicyExists';
								$resultParam = 'str_ErrorPolicyExists';
                            }
                            else
                            {
								$result = 'str_DatabaseError';
								$resultParam = __FUNCTION__ . ': execute ' . $dbObj->error;
                            }
						}
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = __FUNCTION__ . ': bind_params ' . $dbObj->error;
                    }
                    $stmt->free_result();
	                $stmt->close();
	                $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = __FUNCTION__ . ': prepare ' . $dbObj->error;
                }
				$dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = __FUNCTION__ . ': connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['data'] = $pPolicyData;
        $resultArray['serialized'] = '' === $result ? self::flattenPolicy($pPolicyData) : '';

        return $resultArray;
    }


	static function displayAdd()
	{
		// set up the defaults for a new data policy
		return self::getDefaultValues();
    }


	static function displayEdit($pPolicyID)
	{
        $result = DatabaseObj::getOnlineDataPolicy($pPolicyID);

		if ($result['error'] == '')
		{
			$onlineDatePolicy = &$result['data'][0];

			$getBrandCodesLinkedToPolicyResult = self::getBrandCodesLinkedToPolicy($pPolicyID);

			if ($getBrandCodesLinkedToPolicyResult['error'] == '')
			{
				$brandsList = '';

				// Build string of brand codes.
				if (count($getBrandCodesLinkedToPolicyResult['data']) > 0)
				{
					$brandsList = implode(', ', $getBrandCodesLinkedToPolicyResult['data']);
				}

				$onlineDatePolicy['assignedtobrandslist'] = $brandsList;

			}
			else
			{
				$result['error'] = $getBrandCodesLinkedToPolicyResult['error'];
			}
		}

		return $result;
	}


	/**
	 * Update an existing policy.
	 *
	 * @global array $gSession
	 * @return type
	 */
    static function dataPolicyEdit()
    {
		global $gSession;

        $result = '';
        $resultParam = '';

		// Create a policy array from the form POST data.
		$policyData = filter_input_array(INPUT_POST);
		$policyData['id'] = filter_input(INPUT_GET, 'id');
		$policyData['code'] = strtoupper($policyData['code']);
		$policyData['name'] = html_entity_decode($policyData['name'], ENT_QUOTES);
		$policyData['active'] = $policyData['active'];

		// Format the policy data.
		$policyData = self::inflatePolicy($policyData);

		if (($policyData['code'] != '') && ($policyData['name'] != ''))
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
				$sqlQueryContent = array('`name` = ?', '`active` = ?');

				$bindParamArray = array('si');
				$bindParamArray[] = $policyData['name'];
				$bindParamArray[] = $policyData['active'];

				foreach (self::$lookupOrder as $policyKey)
				{
					foreach ($policyData[$policyKey] as $policySettingKey => $policySetting)
					{
						// Only include keys that are not called total.
						if (($policySettingKey != 'total') && (trim($policySetting) != ''))
						{
							$sqlQueryContent[] = '`' . $policyKey . $policySettingKey . '` = ?';
							$bindParamArray[0] .= 'i';
							$bindParamArray[] = $policySetting;
						}
					}
				}

				// Add the WHERE clause
				$bindParamArray[0] .= 'i';
				$bindParamArray[] = $policyData['id'];

				$sql = 'UPDATE `DATAPOLICIES` SET ' . implode(', ', $sqlQueryContent) . '
							WHERE `id` = ?';

				if ($stmt = $dbObj->prepare($sql))
                {
					$bindOK = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParamArray));
					if ($bindOK)
					{
						if ($stmt->execute())
						{
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                'ADMIN', 'DATA-POLICY-UPDATE', $policyData['id'] . ' ' . $policyData['code'], 1);
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = __FUNCTION__ . ': execute ' . $dbObj->error;
						}
                    }
                    else
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = __FUNCTION__ . ': bind_params ' . $dbObj->error;
                    }
                    $stmt->free_result();
	                $stmt->close();
	                $stmt = null;
                }
                else
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = __FUNCTION__ . ': prepare ' . $dbObj->error;
                }
				$dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = __FUNCTION__ . ': connect ' . $dbObj->error;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['data'] = $policyData;
        $resultArray['serialized'] = self::flattenPolicy($policyData);

        return $resultArray;
    }

	/**
	 * Deletes a selection of data retention policies if possible, if the policy is assigned we do not delete it.
	 *
	 * @global array $gSession Current session.
	 * @return array
	 */
    static function dataPolicyDelete()
    {
        global $gSession;

        $resultArray = Array();
        $result = '';
        $resultParam = '';

		$deletedPolicyIDs = array();
		$allDeleted = 1;

		$input = filter_input_array(INPUT_POST);
		$dataPolicyIDList = explode(',', $input['idlist']);
		$dataPolicyCodeList = explode(',', $input['codelist']);

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
			$policyIDCount = count($dataPolicyCodeList);

			for ($i = 0; $i < $policyIDCount; $i++)
			{
				$canDelete = true;

				$thePolicyID = $dataPolicyIDList[$i];
				$thePolicyCode = $dataPolicyCodeList[$i];

	            // check the policy has not been used in a brand
	            if ($stmt = $dbObj->prepare('SELECT `id` FROM `BRANDING` WHERE `onlinedataretentionpolicy` = ?'))
	            {
	                if ($stmt->bind_param('i', $thePolicyID))
	                {
						if ($stmt->execute())
						{
							if ($stmt->store_result())
							{
								if ($stmt->num_rows > 0)
								{
									// if the policy has been assigned to a brand, do not remove it.
									$result = 'str_ErrorUsedInBrand';
									$allDeleted = 0;
									$canDelete = false;
								}
							}
	                    }
	                }
	                $stmt->free_result();
	                $stmt->close();
	                $stmt = null;
	            }

	            if ($canDelete == true)
	            {
	                if ($stmt = $dbObj->prepare('DELETE FROM `DATAPOLICIES` WHERE `code` = ?'))
	                {
	                    if ($stmt->bind_param('s', $thePolicyCode))
	                    {
	                        if ($stmt->execute())
	                        {
	                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
	                                'ADMIN', 'DATA-POLICY-DELETE', $thePolicyID . ' ' . $thePolicyCode, 1);

								$deletedPolicyIDs[] = $thePolicyID;
	                        }
							else
							{
								$allDeleted = 0;

								$result = 'str_DatabaseError';
								$resultParam = __FUNCTION__ . ': execute (delete) ' . $dbObj->error;
							}
	                    }
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = __FUNCTION__ . ': bind_param (delete) ' . $dbObj->error;
						}

	                   	$stmt->free_result();
	                    $stmt->close();
	                    $stmt = null;
	                }
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = __FUNCTION__ . ': prepare (delete) ' . $dbObj->error;
					}
	            }
			}
			$dbObj->close();
        }

    	$resultArray['allDeleted'] = $allDeleted;
        $resultArray['policyIDs'] = $deletedPolicyIDs;
        $resultArray['result'] = $result;
        $resultArray['resultParam'] = $resultParam;

        return $resultArray;
    }

	/**
	 * Returns the lookup order array.
	 *
	 * @return array
	 */
	static function getLookupOrder()
	{
		return self::$lookupOrder;
	}

	/**
	 * Updates the active status of a policy.
	 *
	 * @global array $gSession Current session.
	 * @return array
	 */
	static function setPolicyActiveStatus()
	{
        global $gSession;

        $resultArray = Array();
        $result = '';
        $resultParam = '';

		$input = filter_input_array(INPUT_POST);
		$dataPolicyIDList = explode(',', $input['idlist']);
		$policyIDCount = count($dataPolicyIDList);

		if ($policyIDCount > 0)
		{
			$dbObj = DatabaseObj::getGlobalDBConnection();
			if ($dbObj)
			{
				$sql = 'UPDATE `DATAPOLICIES` SET `active` = ? WHERE `id` IN (';

				$bindParamArray = array('i');
				$bindParamArray[] = $input['active'];

				for ($i = 0; $i < $policyIDCount; $i++)
				{
					$bindParamArray[0] .= 'i';
					$bindParamArray[] = $dataPolicyIDList[$i];
				}

				$sql .= str_repeat('?,', $policyIDCount - 1) . '?)';

				if (($stmt = $dbObj->prepare($sql)))
				{
					$bindOK = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParamArray));
					if ($bindOK)
					{
						if ($stmt->execute())
						{
							DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
								'ADMIN', 'DATA-POLICY-UPDATE', 'setPolicyActiveStatus (' . $input['active'] . '): ' . $input['idlist'], 1);
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = __FUNCTION__ . ': execute ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = __FUNCTION__ . ': bind_param ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = __FUNCTION__ . ': prepare ' . $dbObj->error;
				}
			}
			$dbObj->close();
        }

        $resultArray['result'] = $result;
        $resultArray['resultParam'] = $resultParam;

        return $resultArray;
	}


	/**
	 * Check to see if the task scheduler is active.
	 *
	 * @return int 1 if on, 0 otherwise.
	 */
	static function taskSchedulerActive()
	{
		$error = false;
        $schedulerActive = 0;
		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('SELECT `cronactive` FROM `systemconfig`'))
	        {
    			if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						if ($stmt->num_rows > 0)
						{
				        	if ($stmt->bind_result($schedulerActive))
				            {
		                    	if (! $stmt->fetch())
		                    	{
									$error = true;
		                    	}
			                }
			                else
			                {
								$error = true;
			                }
			            }
		            }
		            else
		            {
						$error = true;
		            }
	            }
	            else
	            {
					$error = true;
		        }
                $stmt->free_result();
	            $stmt->close();
	            $stmt = null;
            }
            else
            {
				$error = true;
            }

			$dbObj->close();
		}
		else
		{
			$error = true;
		}

		return ($error) ? 0 : $schedulerActive;
	}

	/**
	 * Check to see if the specified task is active.
	 *
	 * @return int 1 if on, 0 otherwise.
	 */
	static function getTaskActive($pTaskCode)
	{
		$error = false;
        $taskActive = 0;
		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare("SELECT `active` FROM `TASKS` WHERE `taskcode` = ?"))
	        {
				if ($stmt->bind_param('s', $pTaskCode))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->bind_result($taskActive))
							{
								if (! $stmt->fetch())
								{
									$error = true;
								}
							}
							else
							{
								$error = true;
							}
						}
						else
						{
							$error = true;
						}
					}
					else
					{
						$error = true;
					}
				}
				else
				{
					$error = true;
				}
                $stmt->free_result();
	            $stmt->close();
	            $stmt = null;
            }
            else
            {
            	$error = true;
            }

			$dbObj->close();
		}
		else
		{
			$error = true;
		}

		return ($error) ? 0 : $taskActive;
	}

	/**
	 * Check an s3 archive volume has been configured and is active.
	 *
	 * @global type $ac_config
	 * @return int 1 if found, 0 otherwise.
	 */
	static function archiveVolumeActive()
	{
		require_once('../libs/internal/curl/Curl.php');

        global $ac_config;

        $volumeData = array();
		$volumeFound = 0;
		$serverURL = $ac_config['TAOPIXONLINEURL'];

		$dataToEncrypt = array('cmd' => 'GETVOLUMES', 'data' => array());

		$volumeListDataArray = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt);

        if ($volumeListDataArray['error'] == '')
        {
			$volumeData = $volumeListDataArray['data']['volumes'];
        }

		foreach ($volumeData as $volume)
		{
			// Check is the volume is S3, the volume type is an archive volume and that the volume is active.
			if (($volume['assettype'] === 8) && ($volume['active'] === 1))
			{
				$volumeFound = 1;
				break;
			}
		}

		return $volumeFound;
	}

}
?>
