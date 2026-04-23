<?php

class createDataExportFolder extends ExternalScript
{
	public function run()
	{
		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

		$message = '';
		$exportPath = $ac_config['PRIVATEDATAEXPORTPATH'] . '/ExportData/';

		if (! is_dir($exportPath))
		{
			if (mkdir($exportPath, 0777))
			{
				chmod($exportPath, 0777);
				$message = 'Export folder created';
			}
			else
			{
				$message = 'Please create ' . $exportPath . ' and make it writable by the apache user.';
			}
		}
		else
		{
			chmod($exportPath, 0777);
			$message = 'Export folder exists already';
		}

		$this->printMsg($message);
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