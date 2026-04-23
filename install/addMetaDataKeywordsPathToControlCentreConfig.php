<?php

class addMetaDataKeywordsPathToControlCentreConfig extends ExternalScript
{
	public function run()
	{
		$error = '';
		$newConfigString = '';

		// Read the config file for Control Centre.
		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

		if (! array_key_exists('CONTROLCENTREKEYWORDSIMAGEPATH', $ac_config))
		{
			if (array_key_exists('CONTROLCENTREASSETSPATH', $ac_config))
			{
				$filePath = dirname($ac_config['CONTROLCENTREASSETSPATH']);
			}
			else
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

				$filePath = $filePath . DIRECTORY_SEPARATOR . 'taopixdata' . DIRECTORY_SEPARATOR . 'controlcentre';
			}

			// Construct the default path for the CONTROLCENTREKEYWORDSIMAGEPATH.
			$newConfigPath = $filePath . DIRECTORY_SEPARATOR . 'keywords' . DIRECTORY_SEPARATOR;
			$newConfigString = 'CONTROLCENTREKEYWORDSIMAGEPATH=' . $newConfigPath . PHP_EOL;
			
			if ($fp = @fopen('../config/mediaalbumweb.conf', 'a'))
			{
				fwrite($fp, PHP_EOL . $newConfigString);
				fclose($fp);
			}
			else
			{
				$error = 'Unable to open mediaalbumweb.conf to add the metadata keyword image path settings';
			}
		}

		if ($error != '')
		{
			$this->printMsg($error);
		}
		else
		{
			$this->printMsg('Done');
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
