<?php

require_once('../Utils/UtilsConstants.php');

define('__ROOT__', dirname(dirname(__FILE__)));

// Unlimited memory.
ini_set('memory_limit', '-1');

// Remove the script timeout.
set_time_limit(0);

class addBulkEmailSettingsToControlCentreConfig extends ExternalScript
{
	public function run()
	{
		$error = '';
		$errorParamList = array();
		$newConfigString = '';

		// Read the config file for Control Centre.
		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

		if (!array_key_exists('BULKEMAILTASKBATCHSIZE', $ac_config))
		{
			$newConfigString .= 'BULKEMAILTASKBATCHSIZE=100'. PHP_EOL;
		}

		if (!array_key_exists('BULKEMAILTASKDELAY', $ac_config))
		{
			$newConfigString .= 'BULKEMAILTASKDELAY=3'. PHP_EOL;
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
				$error = 'Unable to open mediaalbumweb.conf to add TAOPIX_BULKEMAIL settings';
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