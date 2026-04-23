<?php

class addDesktopResourcesSettings extends ExternalScript
{
	public function run()
	{
		$error = '';
		$newConfigString = '';
		$newConfigPath = '';

		// Read the config file for Control Centre.
		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

		// Check if the PRODUCTCOLLECTIONRESOURCESPATH option exists, if not it needs to be added.
		if (! array_key_exists('PRODUCTCOLLECTIONRESOURCESPATH', $ac_config))
		{
            // Read the value of the CONTROLCENTREASSETSPATH setting, this should be .../taopixdata/controlcentre/assets
            $filePath = $ac_config['CONTROLCENTREASSETSPATH'];

            // Remove the 'assets' directory from the end.
            $filePath = dirname($filePath);

			// Construct the default path for the PRODUCTCOLLECTIONRESOURCESPATH.
			$newConfigPath = $filePath . DIRECTORY_SEPARATOR . 'collectionresources' . DIRECTORY_SEPARATOR;

			$newConfigString = "PRODUCTCOLLECTIONRESOURCESPATH=" . $newConfigPath . PHP_EOL;

			$collectionResourceFolderExists = file_exists($newConfigPath);

			// Create the folder if it doesn't already exist.
			if (! $collectionResourceFolderExists)
			{
				$collectionResourceFolderExists = UtilsObj::createAllFolders($newConfigPath);
			}

			// Make sure folder has succesfully been created
			if (! $collectionResourceFolderExists)
			{
				$this->printMsg("Unable to automatically create folder: " . $newConfigPath);
			}
		}

		if ($newConfigString != '')
		{
			if ($fp = @fopen('../config/mediaalbumweb.conf', 'a'))
			{
				fwrite($fp, PHP_EOL . $newConfigString);
				fclose($fp);
			}
			else
			{
				$error = 'Unable to open mediaalbumweb.conf to add the product collection resource path settings';
			}
		}

		if ($error != '')
		{
			$this->printMsg($error);
		}
		else
		{
			$this->printMsg('Done - Please update the mediaalbumweb.conf PRODUCTCOLLECTIONRESOURCESPATH setting.');
		}
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
