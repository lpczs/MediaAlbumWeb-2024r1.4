<?php

require_once('../Utils/UtilsConstants.php');

define('__ROOT__', dirname(dirname(__FILE__)));

// Unlimited memory.
ini_set('memory_limit', '-1');

// Remove the script timeout.
set_time_limit(0);

class updateSessionPicturesStructure extends ExternalScript
{
	public function run()
	{
		// Read the config file for Control Centre.
		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

		$error = '';
		$errorParamList = array();

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			// Session data.
			$getSessionResultArray = $this->getSessionData($dbObj);

			if ($getSessionResultArray['error'] == '')
			{
				if (count($getSessionResultArray['data']) > 0)
				{
					foreach ($getSessionResultArray['data'] as $sessionID => $sessionArray)
					{
						$sessionNeedsUpdating = false;
						$serializedDataLength = $sessionArray['serializeddatalength'];
						$sessionDataArray = $sessionArray['sessiondataarray'];

						if ($serializedDataLength > 0)
						{
							$sessionDataArray = gzuncompress($sessionDataArray, $serializedDataLength);
						}

						$unserialisedSessionData = unserialize($sessionDataArray);

						if ($unserialisedSessionData != false)
						{
							foreach ($unserialisedSessionData['items'] as $key => $item)
							{
								if (count($item['pictures']) > 0 && (! array_key_exists('key', $item['pictures'])))
								{
									$convertedPictureData = $this->convertSessionPicturesData($item['pictures']);

									$unserialisedSessionData['items'][$key]['pictures'] = $convertedPictureData['pictures'];
									$sessionNeedsUpdating = true;
								}
							}

							if ($sessionNeedsUpdating)
							{
								$this->printMsg('Updating session ID: '. $sessionID . '...');

								$updateSessionDataResult = $this->updateSessionData($dbObj, $sessionID, $unserialisedSessionData);

								if ($updateSessionDataResult['error'] != '')
								{
									$error = $updateSessionDataResult['error'];
									$errorParamList[] = $updateSessionDataResult['errorparam'];
								}
							}
							else
							{
								$this->printMsg('Session ID '. $sessionID . ' does not need updating...');
							}
						}
					}
				}
			}
			else
			{
				$error = $getSessionResultArray['error'];
				$errorParamList[] = $getSessionResultArray['errorparam'];
			}

			// Online basket data.
			$getOnlineBasketResultArray = $this->getOnlineBasketData($dbObj);

			if ($getOnlineBasketResultArray['error'] == '')
			{
				if (count($getOnlineBasketResultArray['data']) > 0)
				{
					foreach ($getOnlineBasketResultArray['data'] as $basketID => $sessionArray)
					{
						$projectDataNeedsUpdating = false;
						$serializedDataLength = $sessionArray['serializeddatalength'];
						$sessionDataArray = $sessionArray['sessiondataarray'];

						if ($serializedDataLength > 0)
						{
							$sessionDataArray = gzuncompress($sessionDataArray, $serializedDataLength);
						}

						$unserialisedSessionData = unserialize($sessionDataArray);

						if ($unserialisedSessionData != false)
						{
							foreach ($unserialisedSessionData['items'] as $key => $item)
							{
								if (count($item['pictures']) > 0 && (! array_key_exists('key', $item['pictures'])))
								{
									$convertedPictureData = $this->convertSessionPicturesData($item['pictures']);

									$unserialisedSessionData['items'][$key]['pictures'] = $convertedPictureData['pictures'];
									$projectDataNeedsUpdating = true;
								}
							}

							if ($projectDataNeedsUpdating)
							{
								$this->printMsg('Updating Onlinebasket ID: '. $basketID . '...');

								$updateSessionDataResult = $this->updateOnlineBasketData($dbObj, $basketID, $unserialisedSessionData);

								if ($updateSessionDataResult['error'] != '')
								{
									$error = $updateSessionDataResult['error'];
									$errorParamList[] = $updateSessionDataResult['errorparam'];
								}
							}
							else
							{
								$this->printMsg('Onlinebasket ID '. $basketID . ' does not need updating...');
							}
						}
					}
				}
			}
			else
			{
				$error = $getSessionResultArray['error'];
				$errorParamList[] = $getSessionResultArray['errorparam'];
			}
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParamList[] = __FUNCTION__ . ' connection error: ' . $dbObj->error;
		}

		if ($error != '')
		{
			$this->printMsg(implode(PHP_EOL, $errorParamList));
		}
		 else
		{
			$this->printMsg('Done');
		}
	}

	/**
	 * Returns the active order sessions data.
	 *
	 * @param DatabaseObj $pDBObj Database instance object.
	 * @return array
	 */
	private function getSessionData($pDBObj)
	{
		$returnArray = array('error' => '', 'errorparam' => '', 'data' => array());
		$sessionID = 0;
		$sessionData = array();
		$serializedDataLength = 0;
		$error = '';
		$errorParam = '';

		$sql = "SELECT
					`id`, `serializeddatalength`, `sessionarraydata`
				FROM
					`SESSIONDATA`
				WHERE
					`ordersession` = 1
				AND
					`sessionactive`  = 1
				AND
					`sessionexpiredate` > NOW()";

		if ($stmt = $pDBObj->prepare($sql))
		{
			if ($stmt->execute())
			{
				if ($stmt->store_result())
				{
					if ($stmt->num_rows > 0)
					{
						if ($stmt->bind_result($sessionID, $serializedDataLength, $sessionData))
						{
							while ($stmt->fetch())
							{
								$returnArray['data'][$sessionID] =
								array(
									'serializeddatalength' => $serializedDataLength,
									'sessiondataarray' => $sessionData
								);
							}
						}
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' bind_result error: ' . $pDBObj->error;
						}
					}
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' store_result error: ' . $pDBObj->error;
				}
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' execute error: ' . $pDBObj->error;
			}

			$stmt->free_result();
			$stmt->close();
			$stmt = null;
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' prepare error: ' . $pDBObj->error;
		}

		$returnArray['error'] = $error;
		$returnArray['errorparam'] = $errorParam;
		return $returnArray;
	}

	/**
	 * Updates the session data with the new pictures structure.
	 *
	 * @param DatabaseObj $pDBObj Database instance object.
	 * @param int $pSessionID The session ID to update.
	 * @param array $pNewSessionData Thew new session data.
	 */
	private function updateSessionData($pDBObj, $pSessionID, $pNewSessionData)
	{
		$returnArray = array('error' => '', 'errorparam' => '');
		$error = '';
		$errorParam = '';
		$sessionData = serialize($pNewSessionData);
		$serializedDataLength = strlen($sessionData);

		if ($sessionData !== false)
		{
			if ($serializedDataLength > 49152)
			{
				$sessionData = gzcompress($sessionData, 9);
			}
			else
			{
				$serializedDataLength = 0;
			}

			$sql = "UPDATE
						`SESSIONDATA`
					SET
						`serializeddatalength` = ?, `sessionarraydata` = ?
					WHERE
						`id` = ?";

			if (($stmt = $pDBObj->prepare($sql)))
			{
				if ($stmt->bind_param('isi', $serializedDataLength, $sessionData, $pSessionID))
				{
					if (! $stmt->execute())
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' execute error: ' . $pDBObj->error;
					}
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' bind_param error: ' . $pDBObj->error;
				}
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' prepare error: ' . $pDBObj->error;
			}
		}
		else
		{
			printMsg('Serialize failed for session ID: ' . $pSessionID);
		}

		$returnArray['error'] = $error;
		$returnArray['errorparam'] = $errorParam;
		return $returnArray;
	}

	/**
	 * Converts the pictures array into the new lookup format.
	 *
	 * @param array $pPictureData The pictures array to convert.
	 * @return array
	 */
	private function convertSessionPicturesData($pPictureData)
	{
		$pictures = array();
		$pictures['key'] = array();
		$pictures['data'] = array();
		$pictures['printdata'] = array();
		$pictures['pname'] = array();
		$pictures['asset'] = array();
		$picturesAsset = array();
		$picturesData = array();
		$pictureNames = array();
		$picturesCount = count($pPictureData);
		$pictureNamesIndex = 0;
		$pictureNamesLookup = array();

		for ($i = 0; $i < $picturesCount; $i++)
		{
			$picture = $pPictureData[$i];

			$lookUpKey = TPX_PICTURES_LOOKUP_CATEGORY_KEY . TPX_PICTURES_LOOKUP_SEPERATOR . $picture['componentcode'] . TPX_PICTURES_LOOKUP_SEPERATOR .  $picture['componentqty']
											. TPX_PICTURES_LOOKUP_SEPERATOR . $picture['subcomponentcode'];

			$uniqueLookup = $lookUpKey . TPX_PICTURES_LOOKUP_SEPERATOR . $i;

			// We need to know the key so we can store it in the print data so we can look it up later.
			if (! array_key_exists($picture['picturename'], $pictureNamesLookup))
			{
				$pictureNamesLookup[$picture['picturename']] = $pictureNamesIndex;
				$pictureNames[$pictureNamesIndex] = $picture['picturename'];
				$pictureNameKey = $pictureNamesIndex;
				$pictureNamesIndex++;
			}
			else
			{
				$pictureNameKey = $pictureNamesLookup[$picture['picturename']];
			}

			$picturesData[$uniqueLookup] = array(
				'fn' => $pictureNameKey,
				'us' => $picture['unitsell'],
				'tc' => $picture['totalcost'],
				'ts' => $picture['totalsell'],
				'tt' => $picture['totaltax'],
				'tsnt' => $picture['totalsellnotax'],
				'tswt' => $picture['totalsellwithtax'],
				'tw' => $picture['totalweight'],
				'subus' => $picture['subunitsell'],
				'subtc' => $picture['subtotalcost'],
				'subts' => $picture['subtotalsell'],
				'subtt' => $picture['subtotaltax'],
				'subtsnt' => $picture['subtotalsellnotax'],
				'subtswt' => $picture['subtotalsellwithtax'],
				'subtw' => $picture['subtotalweight'],
				'subtotal' => $picture['subtotal'],
				'pageref' => $picture['pageref'],
				'pagenumber' => $picture['pagenumber'],
				'boxref' => $picture['boxref']
			);

			// Check it has an asset service code.
			if ($picture['assetservicecode'] != '')
			{
				$assetDataArray = array();
				$assetDataArray['asc'] = $picture['assetservicecode'];
				$assetDataArray['asn'] = $picture['assetservicename'];
				$assetDataArray['aid'] = $picture['assetid'];
				$assetDataArray['apt'] = $picture['assetpricetype'];
				$assetDataArray['ac'] = $picture['assetcost'];
				$assetDataArray['as'] = $picture['assetsell'];

				$picturesAsset[$uniqueLookup] = $assetDataArray;
			}

			if (! array_key_exists($lookUpKey, $pictures['data']))
			{
				// Convert long keys names to short ones.
				$convertedPicturesArray = UtilsObj::convertPicturesDataToSmallerFormat($picture);

				// Add data to lookup table.
				$pictures['data'][$lookUpKey] = $convertedPicturesArray;
			}

			// Add new entry for this lookup.
			$pictures['key'][] = $lookUpKey;
			$pictures['printdata'] = $picturesData;
			$pictures['pname'] = $pictureNames;
			$pictures['asset'] = $picturesAsset;
		}

		$returnArray['pictures'] = $pictures;

		return $returnArray;
	}

	/**
	 * Returns the active order sessions data.
	 *
	 * @param DatabaseObj $pDBObj Database instance object.
	 * @return array
	 */
	private function getOnlineBasketData($pDBObj)
	{
		$returnArray = array('error' => '', 'errorparam' => '', 'data' => array());
		$sessionID = 0;
		$sessionData = array();
		$serializedDataLength = 0;
		$error = '';
		$errorParam = '';

		$sql = "SELECT
					`id`, `projectdatalength`, `projectdata`
				FROM
					`ONLINEBASKET`
				WHERE
					`basketexpiredate` > NOW()
				AND
					`projectdata` != ''";

		if ($stmt = $pDBObj->prepare($sql))
		{
			if ($stmt->execute())
			{
				if ($stmt->store_result())
				{
					if ($stmt->num_rows > 0)
					{
						if ($stmt->bind_result($sessionID, $serializedDataLength, $sessionData))
						{
							while ($stmt->fetch())
							{
								$returnArray['data'][$sessionID] =
								array(
									'serializeddatalength' => $serializedDataLength,
									'sessiondataarray' => $sessionData
								);
							}
						}
						else
						{
							$error = 'str_DatabaseError';
							$errorParam = __FUNCTION__ . ' bind_result error: ' . $pDBObj->error;
						}
					}
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' store_result error: ' . $pDBObj->error;
				}
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' execute error: ' . $pDBObj->error;
			}

			$stmt->free_result();
			$stmt->close();
			$stmt = null;
		}
		else
		{
			$error = 'str_DatabaseError';
			$errorParam = __FUNCTION__ . ' prepare error: ' . $pDBObj->error;
		}

		$returnArray['error'] = $error;
		$returnArray['errorparam'] = $errorParam;
		return $returnArray;
	}

	/**
	 * Updates the session data with the new pictures structure.
	 *
	 * @param DatabaseObj $pDBObj Database instance object.
	 * @param int $pBasketID The session ID to update.
	 * @param array $pNewOnlineBasketData Thew new session data.
	 * @param return array
	 */
	private function updateOnlineBasketData($pDBObj, $pBasketID, $pNewOnlineBasketData)
	{
		$returnArray = array('error' => '', 'errorparam' => '');
		$error = '';
		$errorParam = '';
		$projectData = serialize($pNewOnlineBasketData);
		$serializedDataLength = strlen($projectData);

		if ($sessionData !== false)
		{
			if ($serializedDataLength > 49152)
			{
				$sessionData = gzcompress($sessionData, 9);
			}
			else
			{
				$serializedDataLength = 0;
			}

			$sql = "UPDATE
						`ONLINEBASKET`
					SET
						`projectdatalength` = ?, `projectdata` = ?
					WHERE
						`id` = ?";

			if (($stmt = $pDBObj->prepare($sql)))
			{
				if ($stmt->bind_param('isi', $serializedDataLength, $projectData, $pBasketID))
				{
					if (! $stmt->execute())
					{
						$error = 'str_DatabaseError';
						$errorParam = __FUNCTION__ . ' execute error: ' . $pDBObj->error;
					}
				}
				else
				{
					$error = 'str_DatabaseError';
					$errorParam = __FUNCTION__ . ' bind_param error: ' . $pDBObj->error;
				}
			}
			else
			{
				$error = 'str_DatabaseError';
				$errorParam = __FUNCTION__ . ' prepare error: ' . $pDBObj->error;
			}
		}
		else
		{
			printMsg('Serialize failed for Onlinebasket ID: ' . $pBasketID);
		}

		$returnArray['error'] = $error;
		$returnArray['errorparam'] = $errorParam;
		return $returnArray;
	}

	/**
	 * prints a message to the screen.
	 *
	 * @param string $pMsg The message text.
	 */
	private function printMsg($pMsg)
	{
		echo $pMsg . PHP_EOL;
	}
}