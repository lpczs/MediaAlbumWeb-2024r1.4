<?php

class updateSessionProductLinking extends ExternalScript
{
	/**
	 * Performs the update for possibly updating the auto increment value on the sessiondata table.
	 */
	public function run()
	{
		$error = false;
        $errorParam = '';

		try {
			// If we are not an upgrade exit out we don't have this issue.
			if ('upgrade' !== $this->mode) {
				$this->setResult('');
				return;
			}

            $dbObj = DatabaseObj::getGlobalDBConnection();

            if ($dbObj)
            {
                $sessionDataResultArray = $this->getSessionData($dbObj);
            }
            else
            {
                $errorParam = "Product linking session update db connect error";
            }

            if (($errorParam == '') && ($sessionDataResultArray['error'] == ''))
            {
                if (count($sessionDataResultArray['data']) > 0)
                {
                    foreach ($sessionDataResultArray['data'] as $sessionID => $sessionArray)
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
                                // in progress sessions can not have linked products so set to product code
                                $item['componenttreeproductcode'] = $item['itemproductcode'];
                                
                                $this->printMsg('Updating session ID: '. $sessionID . '...');
                                $updateSessionDataResult = $this->updateSessionData($dbObj, $sessionID, $unserialisedSessionData);

                                if ($updateSessionDataResult['error'] != '')
                                {
                                    $error = $updateSessionDataResult['error'];
                                    $errorParamList[] = $updateSessionDataResult['errorparam'];
                                }
                            }
                        }
                        else
                        {
                            $errorParam = 'Product linking session update deserialize error for: ' . $sessionID;
                            break;
                        }
                    }
                }
            }
            else
            {
                $errorParam = $sessionDataResultArray['errorparam'];
            }

		} catch (\Exception $ex) {
			// Check what the exception is.
			$error = true;
			$errorParam = 'Product linking session update value error: '. $ex->getMessage();
		}

		if (! $error) {
			$this->printMsg('Order sessions updated for product linking');
		}

		$this->setResult($errorParam);
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

			$sql = "UPDATE `SESSIONDATA`
				SET `serializeddatalength` = ?, `sessionarraydata` = ?
				WHERE `id` = ?";

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