<?php
class InstallFlagsObj
{
	static $flagNames = array();

	/**
	 * updateInstallFlags
	 */
	static function updateInstallFlags($pConnection, $pSchemaName, $pFlagArray, $pTurnAllOn)
	{
		if ($pTurnAllOn)
		{
			foreach (self::$flagNames as $flagKey => $flagValue)
			{
				$currentFlags['flags'][$flagKey] = $flagKey;
			}
		}
		else
		{
			// read the existing install flags from the database
			$currentFlags = self::getInstallFlags($pConnection, $pSchemaName);

			// calculate the new value to insert
			// for each of the flags passed, overwrite that value in the install flags array
			foreach ($pFlagArray as $installKey => $installFlag)
			{
				$currentFlags['flags'][$installKey] = $installFlag;
			}
		}

		// write values back to database
		return self::setInstallFlags($pConnection, $pSchemaName, $currentFlags);
	}

	/**
	 * getInstallFlags
	 */
	static function getInstallFlags($pConnection, $pSchemaName)
	{
		// read the install flags from the SYSTEMCONFIG table
		$resultArray = array('flags' => array(), 'missing' => 0, 'message' => '');
		$flagValue = 0;

		$sql = 'SELECT installflags FROM `' . $pSchemaName . '`.`SYSTEMCONFIG`';

		if ($pConnection)
		{
			if ($stmt = $pConnection->prepare($sql))
			{
				if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						if ($stmt->num_rows > 0)
						{
							if ($stmt->bind_result($flagValue))
							{
								if (! $stmt->fetch())
								{
									$error = __FUNCTION__ . ' fetch: ' . $pConnection->error;
								}
							}
							else
							{
								$error = __FUNCTION__ . ' bind_result: ' . $pConnection->error;
							}
						}
					}
					else
					{
						$error = __FUNCTION__ . ' store_result: ' . $pConnection->error;
					}
				}
				else
				{
					$error = __FUNCTION__ . ' execute: ' . $pConnection->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$error = __FUNCTION__ . ' prepare: ' . $pConnection->error;
			}
		}
		else
		{
			$error = __FUNCTION__ . ' connection error: ' . $pConnection->error;
		}


		// set each of the flag values in the result array to 0 if not set or the flag value if set
		// only need to test for 0 values
		foreach (self::$flagNames as $flagKey => $flagText)
		{
			$resultArray['flags'][$flagKey] = $flagValue & $flagKey;
		}

		self::getMissingFlagsMessage($resultArray);

		return $resultArray;
	}

	/**
	 * setInstallFlags
	 */
	static function setInstallFlags($pConnection, $pSchemaName, $pInstallFlags)
	{
		// write the install flags to the SYSTEMCONFIG table
		$flagValue = 0;
		$error = '';

		foreach ($pInstallFlags['flags'] as $flagKey => $flagSet)
		{
			if ($flagSet != 0)
			{
				$flagValue = $flagValue | $flagKey;
			}
		}

		$sql = 'UPDATE `' . $pSchemaName . '`.`SYSTEMCONFIG` SET installflags = ?';

		if ($pConnection)
		{
			if ($stmt = $pConnection->prepare($sql))
			{
				if ($stmt->bind_param('i', $flagValue))
				{
					if (! $stmt->execute())
					{
						$error = __FUNCTION__ . ' execute: ' . $pConnection->error;
					}
				}
				else
				{
					$error = __FUNCTION__ . ' bind_param: ' . $pConnection->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$error = __FUNCTION__ . ' prepare: ' . $pConnection->error;
			}

		}
		else
		{
			$error = __FUNCTION__ . ' connection error: ' . $pConnection->error;
		}

		return $error;
	}

	/**
	 * getMissingFlagsMessage
	 */
	static function getMissingFlagsMessage(&$pInstallFlags)
	{
		$messageArray = array();

		foreach ($pInstallFlags['flags'] as $flagKey => $flagValue)
		{
			if ($flagValue == 0)
			{
				$pInstallFlags['missing']++;

				$messageArray[] = "\t" . self::$flagNames[$flagKey];
			}
		}

		if ($pInstallFlags['missing'] > 0)
		{
			$messageText = "\n Please update the following before continuing with the upgrade:\n";
			$messageText .= implode("\n", $messageArray);
			$messageText .= "\n";

			$pInstallFlags['message'] = $messageText;
		}
	}


	static function addInstallFlagsColumn()
	{
		self::addColumnIfNotExist(UtilsObj::getDBName(), 'SYSTEMCONFIG', 'installflags', 'INT(11)', 0);
	}

	/**
	* addColumnIfNotExist
	*/
	static function addColumnIfNotExist($pSchemaName, $pTableName, $pColumnName, $pColumnType, $pDefaultValue, $pAfterColumn)
	{
		// check if the field exists in the specified table, if not, add the column with the default value
		$columnFoundData = self::columnExists($pSchemaName, $pTableName, $pColumnName);

		if (! $columnFoundData['exists'])
		{
			$fullTableName = '';

			if ($pSchemaName != '')
			{
				$fullTableName = '`' . $pSchemaName . '`.';
			}

			$fullTableName .= '`' . $pTableName . '`';

			$dbObj = DatabaseObj::getGlobalDBConnection();

			$sql = "ALTER TABLE " . $fullTableName . "
					ADD `" . $pColumnName . "` " . $pColumnType . " NOT NULL DEFAULT " . $pDefaultValue;
			
			if ($pAfterColumn != '')
			{
				$sql .= " AFTER `" . $pAfterColumn . "`"; 
			}
			
			if ($dbObj)
			{
				if ($stmt = $dbObj->prepare($sql))
				{
					if (! $stmt->execute())
					{
						$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
				}
			}
			else
			{
				$error = __FUNCTION__ . ' connection error: ' . $dbObj->error;
			}
		}
	}

	/**
	* columnExists
	*/
	static function columnExists($pSchemaName, $pTableName, $pColumnName)
	{
		$resultArray = array('exists' => false, 'value' => 0);
		$fullTableName = '';
		$exists = 0;
		$columnExists = false;
		$tableExists = false;
		$sqlConditionArray = array();

		if ($pSchemaName != '')
		{
			$fullTableName = '`' . $pSchemaName . '`.';
			$sqlConditionArray[] = "(`TABLE_SCHEMA` = '" . $pSchemaName . "')";
		}

		$fullTableName .= '`' . $pTableName . '`';
		$sqlConditionArray[] = "(`TABLE_NAME` = '" . $pTableName . "')";


		$dbObj = DatabaseObj::getGlobalDBConnection();

		// does the table exist
		$sqlTable = "SELECT count(*) AS exist
					 FROM `information_schema`.`tables`
					 WHERE " . implode(' AND ', $sqlConditionArray);

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare($sqlTable))
			{
				if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						if ($stmt->num_rows > 0)
						{
							if ($stmt->bind_result($exists))
							{
								if ($stmt->fetch())
								{
									if ($exists != 0)
									{
										$tableExists = true;
									}
								}
							}
							else
							{
								$error = __FUNCTION__ . ' bind_result: ' . $dbObj->error;
							}
						}
					}
					else
					{
						$error = __FUNCTION__ . ' store_result: ' . $dbObj->error;
					}
				}
				else
				{
					$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
			}


			if ($tableExists)
			{
				// does the table exist
				$sqlColumns = "SHOW columns FROM " . $fullTableName;

				if ($stmt = $dbObj->prepare($sqlColumns))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0)
							{
								$data = array();
								$variables = array();
								$meta = $stmt->result_metadata();

								while ($field = $meta->fetch_field())
								{
									$variables[] = &$data[$field->name];
								}

								call_user_func_array(array($stmt, 'bind_result'), $variables);

								while (($stmt->fetch()) && (! $columnExists))
								{
									foreach ($data as $k => $v)
									{
										if (($k == 'Field') && ($v == $pColumnName))
										{
											$columnExists = true;
											break;
										}
									}
								}
							}
						}
						else
						{
							$error = __FUNCTION__ . ' store_result: ' . $dbObj->error;
						}
					}
					else
					{
						$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
				}
			}
		}
		else
		{
			$error = __FUNCTION__ . ' connection error: ' . $dbObj->error;
		}

		$resultArray['exists'] = $columnExists;

		return $resultArray;
	}

}
?>
