<?php

class validateOnlineCachePath extends ExternalScript
{

	public function run() {
		$error = '';
		$configAppend = '';
		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
		$folderToCheck = '';

		if (!isset($ac_config['CONTROLCENTREONLINECACHEDATAPATH'])) {
			$folder = $ac_config['CONTROLCENTREASSETSPATH'];
			$configAppend .= "\nCONTROLCENTREONLINECACHEDATAPATH=" . str_replace('assets', 'onlinecache', $folder) . "\n";
			$folderToCheck = str_replace('assets', 'onlinecache', $folder);
		} else {
			$folderToCheck = $ac_config['CONTROLCENTREONLINECACHEDATAPATH'];
		}

		if (!empty($configAppend)) {
			if ($fp = @fopen('../config/mediaalbumweb.conf', 'a'))
			{
				fwrite($fp, PHP_EOL . $configAppend);
				fclose($fp);
			}
			else
			{
				$error = 'Unable to open mediaalbumweb.conf to add Online Cache data.';
			}
		}

		if ('' !== $folderToCheck) {
			if (!is_dir($folderToCheck)) {
				mkdir($folderToCheck, 0777, true);
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