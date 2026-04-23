<?php
class moveAssetsFromDatabase extends ExternalScript
{
	public function run()
	{
		// Add the config to the conf file.
		list($error, $assetPath) = $this->addConfigSetting();

		// Print errors.
		if ($error === '')
		{
			// Run migration.
			list($migrationError, $dbError) = $this->runMigration($assetPath);

			if ($migrationError !== '')
			{
				$this->printMsg($migrationError);
			}

			if ($dbError !== '')
			{
				$this->printMsg($dbError);
			}
		}
		else
		{
			$this->printMsg($error);
		}
	}

	/**
	 * Add the new config to medialbumwebconfig file.
	 *
	 * @return error and the location path.
	 */
	private function addConfigSetting()
	{
		$assetPath = '';
		$error = '';

		// Read the config file for Control Centre.
		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

		// Check if the CONTROLCENTREPREVIEWSPATH option exists, if not it needs to be added.
		if (! array_key_exists('CONTROLCENTREPREVIEWSPATH', $ac_config))
		{
			// Read the value of the CONTROLCENTREASSETSPATH setting, this should be .../taopixdata/controlcentre/assets and the 'assets' directory from the end.
			$filePath = dirname($ac_config['CONTROLCENTREASSETSPATH']);

			if ($filePath !== '')
			{
				// Construct the default path for the CONTROLCENTREPREVIEWSPATH.
				$assetPath = $filePath . DIRECTORY_SEPARATOR . 'previews';
				$assetConfigPath = "CONTROLCENTREPREVIEWSPATH=" . $assetPath . PHP_EOL;

				if ($fp = @fopen('../config/mediaalbumweb.conf', 'a'))
				{
					fwrite($fp, PHP_EOL . $assetConfigPath);
					fclose($fp);
				}
				else
				{
					$error = 'Unable to open mediaalbumweb.conf to add the asset path settings';
				}
			}
			else
			{
				$error = "Missing CONTROLCENTREASSETSPATH from your mediaalbumweb.conf";
			}
		}
		else
		{
			$assetPath = $ac_config['CONTROLCENTREPREVIEWSPATH'];
		}

		return [$error, $assetPath];
	}

	/**
	 * Copy file from the database to physical files.
	 *
	 * @param $pAssetPath Server asset root location.
	 * @return Error strings;
	 */
	private function runMigration($pAssetPath)
	{
		// Product assets.
		$assetProductList = $this->getAssetsList('products');

        // Nothing to do here.
        if (empty($assetProductList)) {
            return ['', ''];
        }

		list($error, $removeIDList) = $this->createFiles($pAssetPath, $assetProductList, 'products');

		if ($error === '')
		{
			// Component assets.
			$componentAssetList = $this->getAssetsList('components');
			list($error, $removeComponentIDList) = $this->createFiles($pAssetPath, $componentAssetList, 'components');
			$removeIDList = array_merge($removeIDList, $removeComponentIDList);
		}

		// Remove assets from database.
		$dbError = $this->removeAssets($removeIDList);

		return [$error, $dbError];
	}

	/**
	 * Create folders and file.
	 *
	 * @param $pAssetPath Server asset root location.
	 * @param $pAssetList Asset list to be used.
	 * @param $pElementType Element type to be created.
	 * @return List of assetID moved as file system and migration errors.
	 */
	private function createFiles($pAssetPath, $pAssetList, $pElementType)
	{
		$removeIDList = [];
		$error = '';

		foreach ($pAssetList as $assetId => $assetData)
		{
			$uniqueID = md5($assetData['code']);
			$destinationFolder = UtilsObj::correctPath($pAssetPath, DIRECTORY_SEPARATOR, true) . $pElementType . DIRECTORY_SEPARATOR . $uniqueID . DIRECTORY_SEPARATOR;
			$extension = UtilsObj::getExtensionFromImageType($assetData['previewtype']);

			$createFileResult = UtilsObj::createAllFolders($destinationFolder);

			if ($createFileResult)
			{
				if ($fp = @fopen($destinationFolder . $uniqueID . $extension, 'a'))
				{
					fwrite($fp, $assetData['data']);
					fclose($fp);

					$removeIDList[] = $assetId;
				}
				else
				{
					$error = "Impossible to create file :" . $destinationFolder . $uniqueID . $extension;
					break;
				}
			}
			else
			{
				$error = "Impossible to create folder :" . $createFileResult;
				break;
			}
		}

		return [$error, $removeIDList];
	}

	/**
	 * Get assets list from the table passed by paramter.
	 *
	 * @param $pTable Table name to be looked at.
	 * @return List of asset object store by assetID [data, elementCode]
	 */
	private function getAssetsList($pTable)
	{
		$assetID = 0;
		$blobData = '';
		$code = '';
		$assetList = [];
		$previewType = '';

		$sql = 'SELECT `' . $pTable . '`.`assetid`, `assetdata`.`data`, `' . $pTable . '`.`code`, `assetdata`.`previewtype`
				FROM  `assetdata`
					INNER JOIN `' . $pTable . '` on `' . $pTable . '`.`assetid` = `assetdata`.`id`';

		if ($stmt = $this->dbConnection->prepare($sql))
		{
			if ($stmt->bind_result($assetID, $blobData, $code, $previewType))
			{
				if ($stmt->execute())
				{
					while($stmt->fetch())
					{
						$assetList[$assetID] = ['data' => $blobData, 'code' => $code, 'previewtype' => $previewType];
					}
				}
			}
		}

		return $assetList;
	}

	/**
	 * Remove a list of assets from the database.
	 *
	 * @param $pRemoveIDList List of ID's to be duplicated.
	 * @return Error string.
	 */
	private function removeAssets($pRemoveIDList)
	{
		$error =  '';
		$sql = 'DELETE FROM `assetdata` WHERE id in (' . implode(',' , $pRemoveIDList) . ')';

		if ($stmt = $this->dbConnection->prepare($sql))
		{
			$stmt->execute();
		}
		else
		{
			$error =  'Deletion of assets error';
		}

		return $error;
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
?>
