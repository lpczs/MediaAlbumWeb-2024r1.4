<?php

class addOrderPreviewsSettings extends ExternalScript
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
		if (! array_key_exists('CONTROLCENTREORDERPREVIEWPATH', $ac_config))
		{
            // Read the value of the CONTROLCENTREASSETSPATH setting, this should be .../taopixdata/controlcentre/assets
            $filePath = $ac_config['CONTROLCENTREASSETSPATH'];

            // Remove the 'assets' directory from the end.
            $filePath = dirname($filePath);

			// Construct the default path for the CONTROLCENTREORDERPREVIEWPATH.
			$newConfigPath = $filePath . DIRECTORY_SEPARATOR . 'orderpreviews';

			$newConfigString = "CONTROLCENTREORDERPREVIEWPATH=" . $newConfigPath . PHP_EOL;
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
			$this->printMsg('Done - Please update the mediaalbumweb.conf CONTROLCENTREORDERPREVIEWPATH setting.');
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