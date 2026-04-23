<?php

class updateDesktopDependencies extends ExternalScript
{
	public function run()
	{
		if ($this->mode == 'upgrade')
		{
			// remove the script timeout
			set_time_limit(0);

			$error = '';

			$dependencyDataArray = self::getDependencyData();

			if ($dependencyDataArray['error'] == '')
			{
				$dataArray = $dependencyDataArray['data'];
				
				foreach ($dataArray as $dependencyItemArray)
				{
					$dependencyItemArray['data'] = self::repairDependencyData($dependencyItemArray['data']);
					
					$updateDataError = self::updateDependencyData($dependencyItemArray);
					if ($updateDataError != '')
					{
						$error = $updateDataError;
						break;
					}
				
				}
			}
			else
			{
				$error = $dependencyDataArray['error'];
			}

			if ($error == '')
			{
				echo "\nUpdateDesktopDependenciesResult process complete.\n";
			}
			
		}
		
		$this->setResult($error);
	}


	private function getDependencyData()
	{
		echo "\nRetrieving dependency data...\n";

		$resultArray = array();
		$error = '';

		$dependenciesArray = array();
	
		if ($this->dbConnection)
		{
			$sql = 'SELECT `id`, `ref`, `dependencies` FROM `APPLICATIONFILES` where `type` = 0';
			if ($stmt = $this->dbConnection->prepare($sql))
			{
				if ($stmt->bind_result($id, $ref, $dependencies))
				{
					if ($stmt->execute())
					{
						while ($stmt->fetch())
						{
							$itemArray = array();
							$itemArray['id'] = $id;
							$itemArray['ref'] = $ref;
							$itemArray['data'] = $dependencies;

							$dependenciesArray[] = $itemArray;
						}
					}
					else
					{
						$error = __FUNCTION__ . ' execute error: ' . $this->dbConnection->error;
					}
				}
				else
				{
					$error = __FUNCTION__ . ' bind result error: ' . $this->dbConnection->error;
				}
				
				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$error = __FUNCTION__ . ' prepare error: ' . $this->dbConnection->error;
			}
		}
		else
		{
			$error = __FUNCTION__ . ' connection error: ' . $this->dbConnection->error;
		}

		$resultArray['error'] = $error;
		$resultArray['data'] = $dependenciesArray;

		echo "...Complete\n\n";

		return $resultArray;
	}


	private function repairDependencyData($pDependencyData)
	{
		$resultData = '';

		// correct data should be sets of 4 tabbed elements separated by a carriage return
		// the broken data consists of a single line with tabbed elements (ie: no carriage return)
		// the lack of carriage returns gives the appearance that there are only 3 elements with an additional tab at the start of the first element
		// luckily, the first element is currently always empty we can repair the data rather than throwing it all away
		if ($pDependencyData != '')
		{
			$rowCount = substr_count($pDependencyData, chr(13));
			if ($rowCount == 0)
			{
				// we either have broken data or repaired data with 1 entry
				// (the format of repaired data with 1 entry is the same as broken data so we can just allow 1 entry to be repaired)
		
				// convert the single line into an array and remove the leading tab
				$dependencyArray = explode(chr(9), $pDependencyData);
				unset($dependencyArray[0]);
		
				// determine the number of complete elements we have
				$count = floor(count($dependencyArray) / 3);
				if ($count > 0)
				{
					// repair the data by grabbing each 3 elements and converting them into 4
					$repairedDataArray = array();
		
					for ($i = 0; $i < $count; $i++)
					{
						// grab 3 elements and convert them back to a line
						$item = array_slice($dependencyArray, $i * 3, 3);
			
						// creator was accidently including user pictures in the dependency list so only include non-user pictures
						// we also perform some additional validation to confirm that both the first and second elements are numeric
						if ((is_numeric($item[0])) && (is_numeric($item[1])) && ($item[0] != '0'))
						{
							// convert the 3 elements back into a string with a new empty 4th element at the front
							$repairedDataArray[] = '' . chr(9) . implode(chr(9), $item);
						}
					}
		
					// convert the repaired data array into a carriage return terminated string
					$resultData = implode(chr(13), $repairedDataArray);
				}
			}
			else
			{
				// the data is multiline so it must have been repaired
				$resultData = $pDependencyData;
			}
		}
		
		return $resultData;
	}


	private function updateDependencyData($pParamArray)
	{
		echo "Updating dependency data " . $pParamArray['ref'] . "\n";

		$error = '';

		if ($this->dbConnection)
		{
			$sql = 'UPDATE `APPLICATIONFILES` SET `dependencies` = ? WHERE `id` = ?';
			if ($stmt = $this->dbConnection->prepare($sql))
			{
				if ($stmt->bind_param('si', $pParamArray['data'], $pParamArray['id']))
				{
					if (! $stmt->execute())
					{
						$error = __FUNCTION__ . ' execute error: ' . $this->dbConnection->error;
					}
				}
				else
				{
					$error = __FUNCTION__ . ' bind_param error: ' . $this->dbConnection->error;
				}
				
				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$error = __FUNCTION__ . ' prepare error: ' . $this->dbConnection->error;
			}
		}
		else
		{
			$error = __FUNCTION__ . ' connection error: ' . $this->dbConnection->error;
		}

		return $error;
	}
}
?>