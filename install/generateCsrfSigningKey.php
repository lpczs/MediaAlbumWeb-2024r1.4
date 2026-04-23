<?php

class generateCsrfSigningKey extends ExternalScript
{
	public function run()
	{
		$error = '';
		$configAppend = '';
		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

		if (!isset($ac_config['CSRF_SIGNING_KEY'])) {
			$configAppend = "\n# Signing key for generating CSRF tokens - must be set to allow authentication of users\n";
			$configAppend .= 'CSRF_SIGNING_KEY=' . bin2hex(random_bytes(128));
		}

		if (!empty($configAppend)) {
			if ($fp = @fopen('../config/mediaalbumweb.conf', 'a'))
			{
				fwrite($fp, PHP_EOL . $configAppend);
				fclose($fp);
			}
			else
			{
				$error = 'Unable to open mediaalbumweb.conf to add CSRF signing key.';
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
