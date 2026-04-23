<?php

class sessionUpdatePhotoPrintsTransformationFlag extends ExternalScript
{
	public function run()
	{
		if ($this->mode == 'upgrade')
		{
			$error = '';
			$errorParamList = array();
			$dbObj = DatabaseObj::getGlobalDBConnection();

			if ($dbObj) {
				// Session data.
				$getSessionResultArray = $this->getSessionData($dbObj);

				if ($getSessionResultArray['error'] == '')
				{
					if (count($getSessionResultArray['data']) > 0)
					{
						foreach ($getSessionResultArray['data'] as $sessionID => $sessionArray)
						{
							$serializedDataLength = $sessionArray['serializeddatalength'];
							$sessionDataArray = $sessionArray['sessiondataarray'];

							if ($serializedDataLength > 0)
							{
								$sessionDataArray = gzuncompress($sessionDataArray, $serializedDataLength);
							}

							$unserialisedSessionData = unserialize($sessionDataArray);

							if ($unserialisedSessionData != false)
							{
								foreach ($unserialisedSessionData['items'] as &$item)
								{
									// Default transformation stage to Post
									$item['pricetransformationstage'] = TPX_PRICETRANSFORMATIONSTAGE_POST;

									$this->printMsg('Updating session ID: '. $sessionID . '...');
									$updateSessionDataResult = $this->updateSessionData($dbObj, $sessionID, $unserialisedSessionData);

									if ($updateSessionDataResult['error'] != '')
									{
										$error = $updateSessionDataResult['error'];
										$errorParamList[] = $updateSessionDataResult['errorparam'];
									}
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

			if ($error != '')
			{
				$this->printMsg(implode(PHP_EOL, $errorParamList));
			}
			else
			{
				$this->printMsg('Done');
			}
		}
	}

	/**
	 * Returns the active order sessions data.
	 *
	 * @param mysqli $pDBObj Database instance object.
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

		$sql = "
			SELECT `id`, `serializeddatalength`, `sessionarraydata`
			FROM `SESSIONDATA`
			WHERE `ordersession` = 1
			AND `sessionactive`  = 1
			AND `sessionexpiredate` > NOW()
		";

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
								$returnArray['data'][$sessionID] = array(
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
	 * @param mysqli $pDBObj
	 * @param int $pSessionID
	 * @param mixed[] $pNewSessionData
	 * @return array
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

			$sql = "
				UPDATE `SESSIONDATA`
				SET `serializeddatalength` = ?, `sessionarraydata` = ?
				WHERE `id` = ?
			";

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
			$this->printMsg('Serialize failed for session ID: ' . $pSessionID);
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
