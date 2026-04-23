<?php

class addResetPasswordExpirySettingsToControlCentreConfig extends ExternalScript
{
	public function run()
	{
		$error = '';
		$errorParamList = array();
		$newConfigString = '';

		// Read the config file for Control Centre.
		$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

		if (!array_key_exists('RESETPASSWORDEXPIRYDURATION', $ac_config))
		{
			$newConfigString .= 'RESETPASSWORDEXPIRYDURATION=60'. PHP_EOL;
		}

		if (!array_key_exists('RESETPASSWORDAUTHCODEENABLED', $ac_config))
		{
			$newConfigString .= 'RESETPASSWORDAUTHCODEENABLED=1'. PHP_EOL;
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
				$error = 'Unable to open mediaalbumweb.conf to add reset password expiry settings';
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