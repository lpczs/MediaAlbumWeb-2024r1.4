<?php

namespace Security\Encryption;

class TAOCrypt extends TAOBlowfish
{

    /**
     * Parse the data length from the data passed into the function
     * Data format [data_length_before_encryption].[IV].[encrypted_blowfish_data]
     *
     * @param {string} $pData Data you wish to parse.
     * @param {number} $pPos The position the data is found.
     * @return {number}
     */
	static function getDataLength($pData, &$pPos)
	{
		$strLen = -1;
		$pos = strpos($pData, '.');

		if ($pos !== false)
		{
			$strLen = (int)substr($pData, 0, $pos);
			$pPos = $pos;
		}

		return $strLen;
	}

    /**
     * Parse the IV from the data passed into the function
     * Data format [data_length_before_encryption].[IV].[encrypted_blowfish_data]
     *
     * @param {string} $pData Data you wish to parse.
     * @param {number} $pStart The position to start looking for the IV.
     * @param {number} $pPos The position the data is found.
     * @return {number}
     */
	static function getIV($pData, $pStart, &$pPos)
	{
		$iv = "";
		$pos = strpos($pData, '.', $pStart);

		if ($pos !== false)
		{
			$iv = substr($pData, $pStart, $pos - $pStart);
			$pPos = $pos;
		}

		return $iv;
	}

    /**
     * If base64 data has come from the url it will have been made URL safe. Convert the data back to base64 so that it
     * can be decoded. Convert the following:
     *  - to +
     *  _ to /
     *  , to = if present else work out by the data length if the = needs adding back on since it is only used if the data is not big enough to fit
     *  into the block size
     *
     * @param {string} $pData Data you wish to convert.
     * @return {string}
     */
    static function makeURLUnsafe($pData)
    {
        return str_pad(strtr($pData, '-_,', '+/='), strlen($pData) + (4 - strlen($pData) % 4) % 4, '=', STR_PAD_RIGHT);
    }

    /**
     * So that base64 data can appear in the URL convert the following:
     *  + to -
     *  / to _
     *  remove =
     *
     * @param {string} $pData Data you wish to convert.
     * @return {string}
     */
    static function makeURLSafe($pData)
    {
        return rtrim(strtr($pData, '+/', '-_'), '=');
    }

    /**
     * Decrypt the taopix blowfish data
     * Data format [data_length_before_encryption].[IV].[encrypted_blowfish_data]
     *
     * @param {string} $pData Data you wish to decrypt.
     * @param {string} $pSecret The key to decrypt the data with.
     * @param {boolean} $pURLSafe Convert the base64 data so that it can be used on the URL.
     * @return {string}
     */
	static function decryptData($pData, $pSecret, $pURLSafe)
    {
        $dataString = '';

        if ($pData != '')
        {
            if ($pSecret != '')
            {
            	$pos = 0;
            	$strLen = self::getDataLength($pData, $pos);

            	if ($strLen != -1)
            	{
            		$dataString = substr($pData, $pos + 1);

            		$iv = self::getIV($dataString, 0, $pos);

            		if ($iv != "")
            		{
            			$dataString = substr($dataString, $pos + 1);

    		            if ($pURLSafe)
    		            {
    		            	$iv = base64_decode(self::makeURLUnsafe($iv));
    		            }
    		            else
    		            {
    		            	$iv = base64_decode($iv);
    		            }

    		            if ($pURLSafe)
    		            {
    		                $dataString = base64_decode(self::makeURLUnsafe($dataString));
    		            }

    		            $dataString = self::blowfishDecrypt($dataString, $pSecret, $iv);
    		            $dataString = substr($dataString, 0, $strLen);
    		        }
    		        else
    		        {
    					throw new MissingIVException("Missing IV from encrypted string");
    		        }
    		    }
    		    else
    		    {
    		    	throw new MissingDataLengthException("Missing data length from encrypted string");
    		    }
            }
    		else
    		{
    			throw new MissingSecretException("Missing secret");
    		}
        }

        return $dataString;
    }

    /**
     * Encrypt data into taopix blowfish data
     * Data format [data_length_before_encryption].[IV].[encrypted_blowfish_data]
     *
     * @param {string} $pData Data you wish to encrypt.
     * @param {string} $pSecret The key to encrypt the data with.
     * @param {boolean} $pURLSafe Convert the base64 data so that it can be used on the URL.
     * @param {string} $pIV A specified IV (optional)
     * @return {string}
     */
    static function encryptData($pData, $pSecret, $pURLSafe, $pIV = '')
    {
        $encryptedData = '';
        $encryptedFormat = ($pURLSafe ? "base64" : "");

        if ($pData != '')
        {
            if ($pSecret != '')
            {
                // use the supplied IV if there is one
                if (!empty($pIV))
                {
                    $iv = $pIV;
                }

                $mcryptData = self::blowfishEncrypt($pData, $pSecret, $iv, $encryptedFormat);

                $iv = base64_encode($iv);

                if ($pURLSafe)
                {
                    $iv = self::makeURLSafe($iv);
                    $mcryptData = self::makeURLSafe($mcryptData);
                }

                $encryptedData = strlen($pData) . '.' . $iv . '.' . $mcryptData;
            }
            else
            {
                throw new MissingSecretException("Missing secret");
            }
        }

        return $encryptedData;
    }

    /**
     * Encrypt data into taopix blowfish data but with added compression
     * Data format [data_length_before_encryption_and_after_compression].[IV].[encrypted_blowfish_data]
     *
	 * @param {string} $pKey The key to encrypt the data with.
     * @param {string} $pData Data you wish to encrypt.
     * @param {boolean} $pCompress Compress the data.
     * @return {string}
     */
    static function encryptStringForTransmission($pKey, $pData, $pCompress)
    {
		$theData = $pData;

        if ($pData != '')
        {
            if ($pKey != '')
            {
    	        if ($pCompress == true)
    	        {
    	            $theDataLen = strlen($theData);

    	            $compressedData = gzcompress($theData, 9);

    	            if ( (strlen($compressedData) > 0) &&  (strlen($compressedData) < $theDataLen))
    	            {
    	                $theData = $theDataLen . "." . $compressedData;
    	                $theDataLen = "-" . strlen($theData);
    	            }
    	        }
    	        else
    	        {
    	            $theDataLen = strlen($theData);
    	        }

    	        $iv = "";

    	        $encryptedData = self::blowfishEncrypt($theData, $pKey, $iv, "base64");

    	        $theData = $theDataLen . "." . base64_encode($iv) . "." . $encryptedData;
            }
            else
            {
                throw new MissingSecretException("Missing secret");
            }
        }

        return $theData;
    }

}