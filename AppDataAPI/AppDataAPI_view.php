<?php

class AppDataAPI_view
{
	static function echoBack($pResultArray, $pIV, $pIVRegenerated)
	{
		global $ac_config;

		$resultString = json_encode($pResultArray);

		if ($ac_config['DATAAPIENCRYPTRESULT'] == 1)
		{
			$resultString = strlen($resultString) . '=' . base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, $ac_config['DATAAPISECRETKEY'], 
					$resultString, MCRYPT_MODE_CBC, $pIV));

            UtilsObj::debugString($resultString);
			// we only send the IV if we are sure that the receiving system is expecting one
			if ($pIVRegenerated === true)
			{
				// the IV must be transmitted in the clear and placing at the start maximises compatability
				// base 64 encode as IV will be in binary format
				$resultString = '!0' . base64_encode($pIV) . '!' . $resultString;
			}
		}

		echo $resultString;
	}
}

?>