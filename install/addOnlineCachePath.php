<?php

class addOnlineCachePath extends ExternalScript
{

	public function run() {
		$error = '';
		$configAppend = '';
		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

		if (!isset($ac_config['CONTROLCENTREONLINECACHEDATAPATH'])) {
			$folder = $ac_config['CONTROLCENTREASSETSPATH'];
			$configAppend .= "\nCONTROLCENTREONLINECACHEDATAPATH=" . str_replace('assets', 'onlinecache', $folder) . "\n";
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