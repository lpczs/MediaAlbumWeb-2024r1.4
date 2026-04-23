<?php
/**
 * Converts old metadata data to new structure.
 */

define('__ROOT__', realpath(dirname(dirname(__FILE__))));

// Include required files.
require_once __ROOT__ . '/Utils/UtilsDatabase.php';
require_once __ROOT__ . '/Utils/UtilsConstants.php';
require_once __ROOT__ . '/Utils/Utils.php';

// Set unlimited script timeout.
set_time_limit(0);

/**
 * Class used to perform the data conversion.
 */
class updateMetaDataStorage
{
	const BLOCKSIZE = 500;
	protected $keywordCodeMap = [];
	protected $processedMetaData = [];
	protected $dbConnection = null;
	protected $validateCols = true;

	protected $insertCount = 0;
	protected $alreadyProcessed = 0;

	/**
	 * Constructor.
	 *
	 * @returns void.
	 */
	public function __construct()
	{
		$this->dbConnection = DatabaseObj::getGlobalDBConnection();
		$this->getKeywordCodeMap();
		$this->getProcessedMetaValues();
	}

	/**
	 * Destructor.
	 *
	 * @returns void.
	 */
	public function __destruct()
	{
		$this->dbConnection->close();
	}

	/**
	 * Performs the data conversion.
	 *
	 * @returns void.
	 */
	public function run($validateCols)
	{
		echo 'Starting metadata conversion', PHP_EOL;

		$startTime = microtime(true);
		$this->validateCols = $validateCols;

		try
		{
			$this->processMetaDataValues();
		}
		catch (Exception $ex)
		{
			echo $ex->getMessage();
		}

		$endTime = microtime(true);

		echo 'Time Taken : ', ($endTime - $startTime), PHP_EOL;
	}

	/**
	 * Reads data from original metadata table, orderheader and orderitemcomponents.
	 * This uses the above data to populate the metadatavalues table.
	 *
	 * @throws \Exception
	 */
	protected function processMetaDataValues()
	{
		$processStartTime = microtime(true);
		$id = -1;
		$orderId = -1;
		$orderItemComponentId = -1;
		$section = '';
		$orderHeaderCodeList = null;
		$orderItemCodeList = null;

		// Build the query to get the ids and mapped keywords for each item in the metadata_orig table.
		$query = "SELECT md.id, md.orderid, md.orderitemcomponentid, md.section, oh.metadatacodelist, oic.metadatacodelist FROM `METADATA_ORIG` md
				LEFT JOIN `ORDERHEADER` oh ON oh.id=md.orderid
				LEFT JOIN `ORDERITEMCOMPONENTS` oic ON oic.id=md.orderitemcomponentid
				ORDER BY md.id ASC";

		if ($stmt = $this->dbConnection->prepare($query))
		{
			if ($stmt->execute())
			{
				if ($stmt->store_result())
				{
					if ($stmt->bind_result($id, $orderId, $orderItemComponentId, $section, $orderHeaderCodeList, $orderItemCodeList))
					{
						echo 'Processing original data', PHP_EOL;
						$insertData = [];
						$counter = 0;
						$dataConversionStartTime = microtime(true);

						while ($stmt->fetch())
						{
							// Check if we are validating columns for this conversion process.
							if ($this->validateCols)
							{
								if ('ORDER' === $section)
								{
									if ((null !== $orderHeaderCodeList) && ('' !== trim($orderHeaderCodeList)))
									{
										$this->populateMetaDataValues($id, $orderHeaderCodeList, $insertData);
									}
								}
								else
								{
									if ((null !== $orderItemCodeList) && ('' !== trim($orderItemCodeList)))
									{
										$this->populateMetaDataValues($id, $orderItemCodeList, $insertData);
									}
								}
							}
							else
							{
								$this->populateAllMetaDataValues($id, $insertData);
							}

							// Echo something to the console every 2000 items.
							if ((0 === ($counter % 2000)) && ($counter > 0))
							{
								echo '.';
							}
							$counter++;
						}

						echo PHP_EOL, 'Inserting new Data', PHP_EOL;

						// Split into blocks of self::BLOCKSIZE.
						$chunks = array_chunk($insertData, self::BLOCKSIZE);
						$chunkCount = count($chunks);

						// Loop over each chunk of items and process the data to insert.
						for ($i = 0; $i < $chunkCount; $i++)
						{
							$this->processDataBlock($chunks[$i]);
						}

						echo PHP_EOL, 'Data conversion time ', (microtime(true) - $dataConversionStartTime), PHP_EOL;

						echo $this->insertCount, ' rows inserted - data size ', count($insertData), PHP_EOL;

						if ($this->alreadyProcessed > 0)
						{
							echo 'We had already processed ', $this->alreadyProcessed, ' items', PHP_EOL;
						}
					}
					else
					{
						throw new \Exception('Populate bind error: ' . $this->dbConnection->error);
					}
				}
				else
				{
					throw new \Exception('Store result error: ' . $this->dbConnection->error);
				}
			}
			else
			{
				throw new \Exception('Populate execute error: ' . $this->dbConnection->error);
			}
		}
		else
		{
			throw new \Exception('Populate prepare error: ' . $this->dbConnection->error);
		}
	}

	/**
	 * Returns a list of columns that start with keyword
	 *
	 * @return array of column names.
	 */
	protected function getOrigMetaDataColNames()
	{
		$columns = DatabaseObj::getTableColumnNames('METADATA_ORIG');
		$colCount = count($columns);
		$keywordCols = [];

		// Loop over each column
		for ($i = 0; $i < $colCount; $i++)
		{
			// Check that the column name starts with keyword.
			if (0 === strpos($columns[$i], 'keyword'))
			{
				$keywordCols[] = $columns[$i];
			}
		}

		return $keywordCols;
	}

	/**
	 * Populates the insertData variable to contain the correct information for inserting metadata.
	 * Uses the data from the original table and loops over each keyword[x] column to generate the information
	 * that is needed in the new format.
	 * This is used when a client has assigned a large number of metadata to a component or order.
	 *
	 * @param int $id ID for the metadata row we are working on.
	 * @param type $insertData associative array containing the data for all metadata.
	 * @throws \Exception
	 */
	protected function populateAllMetaDataValues($id, &$insertData)
	{
		// Set intial values for variables used.
		$colNames = [];
		$bindResult = [];
		$itemList = [];
		$position = 0;

		// Get the column names from the original metadata table.
		$origKeywordCols = $this->getOrigMetaDataColNames();

		// Create the columnNames, itemList, and bindResult arrays for use later.
		foreach ($origKeywordCols as $colName)
		{
			$colNames[] = $colName;
			$itemList[$position] = '';
			$bindResult[$colName] = &$itemList[$position];
			$position++;
		}

		// Build the query to based on the column names.
		$query = "SELECT `" . implode('`, `', $colNames) . "` FROM `METADATA_ORIG` WHERE `id`=?";

		if ($newStmt = $this->dbConnection->prepare($query))
		{
			if ($newStmt->bind_param('i', $id))
			{
				if ($newStmt->execute())
				{
					// Bind the result to the bindResult array.
					$bindOk = call_user_func_array([$newStmt, 'bind_result'], $bindResult);

					if ($bindOk)
					{
						while ($newStmt->fetch())
						{
							// Loop over each item in the keywordCodeMap.
							foreach ($origKeywordCols as $colName)
							{
								// Get the ref for the column.
								$ref = substr($colName, 7);

								// If the value is not empty or null save this in the new db format.
								if (('' !== $bindResult[$colName]) && (null !== $bindResult[$colName]))
								{
									if ($this->canProcess($id, $ref))
									{
										// If we have not inserted this item previously do so now.
										$insertData[] = [$id, $ref, $bindResult[$colName]];
									}
									else
									{
										$this->alreadyProcessed++;
									}
								}
							}
						}
					}
					else
					{
						throw new \Exception('Populate metadata bind result error: ' . $this->dbConnection->error);
					}
				}
				else
				{
					throw new \Exception('Populate metadata execute error: ' . $this->dbConnection->error);
				}
			}
			else
			{
				throw new \Exception('Populate metadata bind param error: ' . $this->dbConnection->error);
			}
		}
		else
		{
			throw new \Exception('Populate metadata prepare error: ' . $this->dbConnection->error);
		}
	}

	/**
	 * Reads columns from orig metadata table and builds array for a specific id.
	 *
	 * @param int $id id for the row we want data from
	 * @param string $metaDataCodeList List of keyword codes we are interested in.
	 * @param array $insertData Passed by ref array where we are storing the data.
	 * @throws \Exception
	 * @returns void
	 */
	protected function populateMetaDataValues($id, $metaDataCodeList, &$insertData)
	{
		$colNames = [];
		$bindResult = [];
		$codeList = explode(',', $metaDataCodeList);
		$itemList = $codeList;
		$colCount = 0;

		if (count($codeList) > 0)
		{
			// Loop over each code and build the columns to use.
			foreach ($codeList as $key => $code)
			{
				// Check that the code exists and is available.
				if (isset($this->keywordCodeMap[$code]))
				{
					$colName = 'keyword' . $this->keywordCodeMap[$code];
					$colNames[] = $colName;
					$bindResult[$colName] = &$itemList[$key];
					$colCount++;
				}
				else
				{
					echo 'Unable to find ', $code, ' in keywords', PHP_EOL, 'metaDataCodeList: ', $metaDataCodeList, ' for id ', $id, PHP_EOL;
				}
			}

			if ($colCount > 0)
			{
				// Build the query to based on the column names generated.
				$query = "SELECT `" . implode('`, `', $colNames) . "` FROM `METADATA_ORIG` WHERE `id`=?";

				if ($newStmt = $this->dbConnection->prepare($query))
				{
					if ($newStmt->bind_param('i', $id))
					{
						if ($newStmt->execute())
						{
							$bindOk = call_user_func_array([$newStmt, 'bind_result'], $bindResult);

							if ($bindOk)
							{
								while ($newStmt->fetch())
								{
									// Loop over each code for this item.
									foreach ($codeList as $code)
									{
										// If the code exists in the codeMap add the values to the insertData array.
										if (isset($this->keywordCodeMap[$code]))
										{
											$ref = $this->keywordCodeMap[$code];
											$colName = 'keyword' . $ref;

											if ($this->canProcess($id, $ref))
											{
												$insertData[] = [$id, $ref, $bindResult[$colName]];
											}
											else
											{
												$this->alreadyProcessed++;
											}
										}
									}
								}
							}
							else
							{
								throw new \Exception('Populate metadata bind result error: ' . $this->dbConnection->error);
							}
						}
						else
						{
							throw new \Exception('Populate metadata execute error: ' . $this->dbConnection->error);
						}
					}
					else
					{
						throw new \Exception('Populate metadata bind param error: ' . $this->dbConnection->error);
					}
				}
				else
				{
					throw new \Exception('Populate metadata prepare error: ' . $this->dbConnection->error);
				}
			}
		}
	}

	/**
	 * Generates an associative array containing code => ref.
	 *
	 * @returns array Associative array of code => ref.
	 */
	protected function getKeywordCodeMap()
	{
		$ref = -1;
		$code = '';

		$query = "SELECT `ref`, `code` FROM `keywords`";

		if ($stmt = $this->dbConnection->prepare($query))
		{
			if ($stmt->bind_result($ref, $code))
			{
				if ($stmt->execute())
				{
					while($stmt->fetch())
					{
						$this->keywordCodeMap[$code] = $ref;
					}
				}
			}
		}
	}

	/**
	 * Looks over the metadatavalues table and stores which items have already been processed,
	 * This stops us duplicating data if someone were to run the conversion script more than once.
	 *
	 * @returns void.
	 */
	protected function getProcessedMetaValues()
	{
		$metaDataId = -1;
		$ref = -1;

		$query = "SELECT `metadataid`, `keywordref` FROM `metadatavalues`";

		if ($stmt = $this->dbConnection->prepare($query))
		{
			if ($stmt->bind_result($metaDataId, $ref))
			{
				if ($stmt->execute())
				{
					while($stmt->fetch())
					{
						// If the key does not exist for the current item we are processing generate it.
						if (! isset($this->processedMetaData[$metaDataId]))
						{
							$this->processedMetaData[$metaDataId] = [];
						}

						// Say that we have processed this item.
						$this->processedMetaData[$metaDataId][] = $ref;
					}
				}
			}
		}
	}

	/**
	 * Checks if we have previously converted an item.
	 *
	 * @param int $metaDataId Id from the metadata table.
	 * @param int $ref Ref of the item we are processing.
	 * @return boolean
	 */
	protected function canProcess($metaDataId, $ref)
	{
		$canProcess = true;

		if (isset($this->processedMetaData[$metaDataId]))
		{
			// If the item is in the processedMetaData array we do not want to process it.
			$canProcess = ! in_array($ref, $this->processedMetaData[$metaDataId]);
		}

		return $canProcess;
	}

	/**
	 * Processes a block of data, generates and performs an insert for the passed data.
	 *
	 * @param array $data Data to parse and insert.
	 * @throws \Exception
	 * @returns void
	 */
	protected function processDataBlock($data)
	{
		$baseQuery = "INSERT INTO `metadatavalues` (`metadataid`, `keywordref`, `value`) VALUES ";

		$chunkSize = count($data);

		echo '.';

		$bindParams = [''];
		$queryPlaceHolders = [];

		// Loop over each item in the chunk and configure place holders and bind params.
		for ($j = 0; $j < $chunkSize; $j++)
		{
			$queryPlaceHolders[] = '(?, ?, ?)';
			$bindParams[0] .= 'iis';
			$bindParams[] = &$data[$j][0];
			$bindParams[] = &$data[$j][1];
			$bindParams[] = &$data[$j][2];

			// If we have not set the array of processed items for the metadata key do so.
			if (! isset($this->processedMetaData[$data[$j][0]]))
			{
				$this->processedMetaData[$data[$j][0]] = [];
			}

			// Add that we have processed this ref.
			$this->processedMetaData[$data[$j][0]][] = $data[$j][1];
		}

		// Build the query to run from the base query and placeholders.
		$query = $baseQuery . implode(', ', $queryPlaceHolders);

		// Prepare the query.
		if ($insertStmt = $this->dbConnection->prepare($query))
		{
			// Call bind_param with the generated list of parameters.
			$bindOk = call_user_func_array([$insertStmt, 'bind_param'], $bindParams);

			if ($bindOk)
			{
				if (!$insertStmt->execute())
				{
					throw new \Exception('Insert query error: ' . $this->dbConnection->error);
				}
				else
				{
					$this->insertCount += $this->dbConnection->affected_rows;
				}
			}
			else
			{
				throw new \Exception('Insert query bind error: ' . $this->dbConnection->error);
			}
		}
		else
		{
			throw new \Exception('Insert query prepare error:  ' . $this->dbConnection->error);
		}
	}
}

// Read the config file.
$ac_config = UtilsObj::readConfigFile(__ROOT__ . '/config/mediaalbumweb.conf');

$upgrader = new updateMetaDataStorage();

$validateColumns = true;

// Check if we have passed any arguments.
if ($argc > 1)
{
	// If we have called the process with false we are not wanting to validate the columns.
	if ('false' === $argv[1])
	{
		$validateColumns = false;
	}
}

// Run the upgrade process.
$upgrader->run($validateColumns);