<?php

class addOAuthStatusCacheSettings extends ExternalScript
{
	public function run()
	{
		$error = '';
		$newConfigString = '';
		$newConfigPath = '';

		// Read the config file for Control Centre.
		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

		// Check if the CONTROLCENTREOAUTHSTATUSCACHEPATH option exists, if not it needs to be added.
		if (! array_key_exists('CONTROLCENTREOAUTHSTATUSCACHEPATH', $ac_config))
		{
            // Read the value of the CONTROLCENTREASSETSPATH setting, this should be .../taopixdata/controlcentre/assets
            $filePath = $ac_config['CONTROLCENTREASSETSPATH'];

            // Remove the 'assets' directory from the end.
            $filePath = dirname($filePath);

			// Construct the default path for the CONTROLCENTREOAUTHSTATUSCACHEPATH.
			$newConfigPath = $filePath . DIRECTORY_SEPARATOR . 'oauthstatuscache' . DIRECTORY_SEPARATOR;

			$newConfigString = "CONTROLCENTREOAUTHSTATUSCACHEPATH=" . $newConfigPath . PHP_EOL;

			$oAuthStatusCacheFolderExists = file_exists($newConfigPath);

			// Create the folder if it doesn't already exist.
			if (! $oAuthStatusCacheFolderExists)
			{
				$oAuthStatusCacheFolderExists = UtilsObj::createAllFolders($newConfigPath);
			}

			// Make sure folder has succesfully been created
			if (! $oAuthStatusCacheFolderExists)
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
				$error = 'Unable to open mediaalbumweb.conf to add the oauth status  path settings';
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
