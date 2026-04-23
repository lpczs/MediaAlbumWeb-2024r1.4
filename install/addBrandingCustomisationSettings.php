<?php

class addBrandingCustomisationSettings extends ExternalScript
{
	public function run()
	{
		$error = '';
		$errorParamList = array();
		$newConfigString = '';
		$newConfigPath = '';

		// Read the config file for Control Centre.
		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

		// Check if the CONTROLCENTREASSETSPATH option exists, if not it needs to be added.
		if (! array_key_exists('CONTROLCENTREASSETSPATH', $ac_config))
		{
			// Get the directory of the current script, this should be .../Taopix/controlcentre/install
			$filePath = dirname(__FILE__);

			// Remove the last directory name, until install does not exist
			while (strpos($filePath, 'install') !== false)
			{
				$filePath = dirname($filePath);
			}

			// Result should now be /Taopix/controlcentre, remove the controlcentre directory.
			$filePath = dirname($filePath);

			// Construct the default path for the CONTROLCENTREASSETSPATH.
			$newConfigPath = $filePath . DIRECTORY_SEPARATOR . 'taopixdata' . DIRECTORY_SEPARATOR . 'controlcentre' . DIRECTORY_SEPARATOR . 'assets';

			$newConfigString .= "CONTROLCENTREASSETSPATH=" . $newConfigPath . PHP_EOL;
		}

		if ($newConfigString != '')
		{
			if ($fp = @fopen('../config/mediaalbumweb.conf', 'a'))
			{
				$result = fwrite($fp, PHP_EOL . $newConfigString);

				fclose($fp);
			}
			else
			{
				$error = 'Unable to open mediaalbumweb.conf to add the asset path settings';
			}
		}

		if ($error != '')
		{
			$this->printMsg($error);
		}
		else
		{
			$this->printMsg('Done - Please update the mediaalbumweb.conf CONTROLCENTREASSETSPATH setting.');
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