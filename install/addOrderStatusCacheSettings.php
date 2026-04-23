<?php

class addOrderStatusCacheSettings extends ExternalScript
{
	public function run()
	{
		$error = '';
		$newConfigString = '';
		$newConfigPath = '';

		// Read the config file for Control Centre.
		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

		// Check if the CONTROLCENTREORDERSTATUSCACHEPATH option exists, if not it needs to be added.
		if (! array_key_exists('CONTROLCENTREORDERSTATUSCACHEPATH', $ac_config))
		{
            // Read the value of the CONTROLCENTREASSETSPATH setting, this should be .../taopixdata/controlcentre/assets
            $filePath = $ac_config['CONTROLCENTREASSETSPATH'];

            // Remove the 'assets' directory from the end.
            $filePath = dirname($filePath);

			// Construct the default path for the CONTROLCENTREORDERSTATUSCACHEPATH.
			$newConfigPath = $filePath . DIRECTORY_SEPARATOR . 'orderstatuscache' . DIRECTORY_SEPARATOR;

			$newConfigString = "CONTROLCENTREORDERSTATUSCACHEPATH=" . $newConfigPath . PHP_EOL;

			$orderStatsCacheFolderExists = file_exists($newConfigPath);

			// Create the folder if it doesn't already exist.
			if (! $orderStatsCacheFolderExists)
			{
				$orderStatsCacheFolderExists = UtilsObj::createAllFolders($newConfigPath);
			}

			// Folder exists or was created automatically.
			if ($orderStatsCacheFolderExists)
			{
				// Move existing files to the new folder.
				$this->printMsg("Copying order status cache files to new location.");

				$oldPath = '../webroot/Cache/Orders/';
				if (file_exists($oldPath))
				{
					foreach(scandir($oldPath) as $item)
					{
						// Ignore folder tree structure files.
						if ((! strcmp($item, '.')) || (! strcmp($item, '..'))) continue;

						if (! rename($oldPath . $item, $newConfigPath . $item))
						{
							$this->printMsg("Unable to move" . $oldPath . $item);
						}
					}

					// Delete the old folder.
					$this->printMsg("Removing old order status cache files location.");
					@rmdir($oldPath);
				}
			}
			else
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
				$error = 'Unable to open mediaalbumweb.conf to add the order status cache path settings';
			}
		}

		if ($error != '')
		{
			$this->printMsg($error);
		}
		else
		{
			$this->printMsg('Done - Please update the mediaalbumweb.conf CONTROLCENTREORDERSTATUSCACHEPATH setting.');
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
