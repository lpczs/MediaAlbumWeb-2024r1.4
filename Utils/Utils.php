<?php

class UtilsObj
{
    static function decryptData($pData, $pSecret, $pURLSafe)
    {
        $decryptedData = "";

        try
        {
            $decryptedData = Security\Encryption\TAOCrypt::decryptData($pData, $pSecret, $pURLSafe);
        }
        catch (Exception $e)
        {
            error_log("Error when trying to decrypt: [" . $pData . " : " . $pSecret . " :  " . $pURLSafe . "] - ");
            error_log($e);
        }

        return $decryptedData;
    }

    static function encryptData($pData, $pSecret, $pURLSafe, $pIV = '')
    {
        $encryptedData = "";

        try
        {
            $encryptedData =  Security\Encryption\TAOCrypt::encryptData($pData, $pSecret, $pURLSafe, $pIV);
        }
        catch (Exception $e)
        {
            error_log("Error when trying to encrypt: [" . $pData . " : " . $pSecret . " :  " . $pURLSafe . "] - ");
            error_log($e);
        }

        return $encryptedData;
    }

    static function xmlPost($pUrl, $pRequestData)
    {
        $parsedUrl = parse_url($pUrl);

        if (empty($parsedUrl['port']))
        {
            $parsedUrl['port'] = strtolower($parsedUrl['scheme']) == 'https' ? 443 : 80;
        }

        // generate request
        $header = 'POST ' . $parsedUrl['path'] . " HTTP/1.1\r\n";
        $header .= 'Host: ' . $parsedUrl['host'] . "\r\n";
        $header .= "Content-Type: application/xml\r\n";
        $header .= 'Content-Length: ' . strlen($pRequestData) . "\r\n";
        $header .= "Connection: close\r\n";
        $header .= "\r\n";
        $request = $header . $pRequestData;

        $replyData = '';
        $errno = 0;
        $errstr = '';

        // open socket to filehandle and retry up to 3 times with a 20 second timeout
        $retryCount = 3;
        $_start = microtime();
        $success = '';
        $errno = '';
        $errstr = '';

        while ($retryCount > 0)
        {
            // increase the standard php timeout
            UtilsObj::resetPHPScriptTimeout(60);

            $fp = fsockopen(($parsedUrl['scheme'] == 'https' ? 'ssl://' : '') . $parsedUrl['host'], $parsedUrl['port'], $errno, $errstr, 30);
            if ($fp)
            {
                $success = 'success';
                $retryCount = 0;
            }
            else
            {
                $success = 'failed';
                $retryCount--;
            }
            $_end = microtime();
            $_start = microtime();
        }

        if ($fp)
        {
            // read the response from MultiSafepay
            // while content exists, keep retrieving document in 1K chunks
            fwrite($fp, $request);
            fflush($fp);

            while (! feof($fp))
            {
                $replyData .= fread($fp, 1024);
            }

            fclose($fp);


            if (! $errno)
            {

                $headerSize = strpos($replyData, "\r\n\r\n");
                $headerData = substr($replyData, 0, $headerSize);
                $header = explode("\r\n", $headerData);
                $statusLine = explode(" ", $header[0]);
                $contentType = "application/octet-stream";

                foreach($header as $headerLine)
                {
                    $headerParts = explode(":", $headerLine);

                    if (strtolower($headerParts[0]) == "content-type")
                    {
                        $contentType = trim($headerParts[1]);
                        break;
                    }
                }

                $replyInfo = array(
                    'httpCode' => (int) $statusLine[1],
                    'contentType' => $contentType,
                    'headerSize' => $headerSize + 4);

                if ($replyInfo['httpCode'] != 200)
                {
                    $pErrorString = 'HTTP code is ' . $replyInfo['httpCode'] . ', expected 200';
                    return false;
                }

                if (strstr($replyInfo['contentType'], "/xml") === false)
                {
                    $pErrorString = 'Content type is ' . $replyInfo['contentType'] . ', expected */xml';
                    return false;
                }

                // split header and body
                $replyHeader = substr($replyData, 0, $replyInfo['headerSize'] - 4);
                $replyXml = substr($replyData, $replyInfo['headerSize']);
            }
            else
            {
                $pErrorString = $errstr;
                return false;
            }
        }
        else
        {
            if ($errno)
            {
                $pErrorString = $errstr . '(' . $errno . ')';
                return false;
            }
        }

        return $replyXml; // Xml as plain text
    }

    static function transferEncodingChunkedDecode($in)
    {
        $out = '';
        while($in != '')
        {
            $lf_pos = strpos($in, "\012");
            if ($lf_pos === false)
            {
                $out .= $in;
                break;
            }
            $chunk_hex = trim(substr($in, 0, $lf_pos));
            $sc_pos = strpos($chunk_hex, ';');
            if ($sc_pos !== false) $chunk_hex = substr($chunk_hex, 0, $sc_pos);
            if ($chunk_hex == '')
            {
                $out .= substr($in, 0, $lf_pos);
                $in = substr($in, $lf_pos + 1);
                continue;
            }
            $chunk_len = hexdec($chunk_hex);
            if ($chunk_len)
            {
                $out .= substr($in, $lf_pos + 1, $chunk_len);
                $in = substr($in, $lf_pos + 2 + $chunk_len);
            }
            else
            {
                $in = '';
            }
        }
        return $out;
    }

    static function sortRows(&$a_aaRows, $a_aaSortCriteria)
    {
        global $g_aaSortArray;

        function compare($a_aRow1, $a_aRow2, $a_lField = 0)
        {
            global $g_aaSortArray;
            $lCompareVal = 0;
            if ($a_lField < count($g_aaSortArray))
            {
                $sSortFieldName = $g_aaSortArray[$a_lField]['name'];
                $sSortFieldDir = $g_aaSortArray[$a_lField]['dir'];
                $vValue1 = eval('return $a_aRow1["' . $sSortFieldName . '"];');
                $vValue2 = eval('return $a_aRow2["' . $sSortFieldName . '"];');

                if ($vValue1 == $vValue2) $lCompareVal = compare($a_aRow1, $a_aRow2, $a_lField + 1);
                else
                {
                    $lCompareVal = $vValue1 > $vValue2 ? 1 : -1;
                    if (strtolower(substr($sSortFieldDir, 0, 4)) == 'desc') $lCompareVal = -$lCompareVal;
                }
            }
            return $lCompareVal;
        }

        $g_aaSortArray = $a_aaSortCriteria;
        usort($a_aaRows, 'compare');
    }

    static function bround($dVal, $iDec)
    {
        /*
          Banker's Rounding v1.01, 2006-08-15
          Copyright 2006 Michael Boone
          mike@Xboonedocks.net (remove the X)
          http://boonedocks.net/

          Provided under the a BSD-style License
          A GPL licensed version is available at:
          http://boonedocks.net/code/bround.inc.phps
          Contact me for use outside the bounds of these licenses

          ---------------------------------------------------------------
          Copyright (c) 2006 Michael Boone

          All rights reserved.

          Redistribution and use in source and binary forms, with or
          without modification, are permitted provided that the following
          conditions are met:
         * Redistributions of source code must retain the above
          copyright notice, this list of conditions and the
          following disclaimer.
         * Redistributions in binary form must reproduce the above
          copyright notice, this list of conditions and the
          following disclaimer in the documentation and/or other
          materials provided with the distribution.
         * Neither the name of boonedocks.net nor the name of
          Michael Boone may be used to endorse or promote products
          derived from this software without specific prior
          written permission.

          THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
          "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
          LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
          A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
          CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
          EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
          PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
          PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
          LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
          NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
          SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
          ---------------------------------------------------------------

          Release History:
          2006-01-05: v1.00: Initial Release
          2006-08-15: v1.01: Updated with faster even/odd test

         */

        // banker's style rounding or round-half-even
        // (round down when even number is left of 5, otherwise round up)
        // $dVal is value to round
        // $iDec specifies number of decimal places to retain
        static $dFuzz = 0.00001; // to deal with floating-point precision loss
        $iRoundup = 0; // amount to round up by

        $iSign = ($dVal != 0.0) ? intval($dVal / abs($dVal)) : 1;
        $dVal = abs($dVal);

        // get decimal digit in question and amount to right of it as a fraction
        $dWorking = $dVal * pow(10.0, $iDec + 1) - floor($dVal * pow(10.0, $iDec)) * 10.0;
        $iEvenOddDigit = floor($dVal * pow(10.0, $iDec)) - floor($dVal * pow(10.0, $iDec - 1)) * 10.0;

        if (abs($dWorking - 5.0) < $dFuzz) $iRoundup = ($iEvenOddDigit & 1) ? 1 : 0;
        else $iRoundup = ($dWorking > 5.0) ? 1 : 0;

        return $iSign * ((floor($dVal * pow(10.0, $iDec)) + $iRoundup) / pow(10.0, $iDec));
    }

    static function formatCurrencyNumber($pTheNumber, $pDecimalPlaces, $pLocale = '', $pSymbol = '', $pSymbolAtFront = 1)
    {
        // format a number with the currency symbol and correct number of decimal places
        if ($pTheNumber < 0.00)
        {
            $isNegative = true;
            $pTheNumber = abs($pTheNumber);
        }
        else
        {
            $isNegative = false;
        }

        $result = number_format($pTheNumber, $pDecimalPlaces, LocalizationObj::getLocaleDecimalPoint($pLocale),
                LocalizationObj::getLocaleThousandsSeparator($pLocale));

        if ($pSymbol != '')
        {
            if ($pSymbolAtFront == 1)
            {
                $result = $pSymbol . $result;
            }
            else
            {
                $result = $result . $pSymbol;
            }
        }

        if ($isNegative == true)
        {
            $result = '-' . $result;
        }

        return $result;
    }

    static function formatNumber($pNumber, $pDecimalPlaces = -1)
    {
        // format a number to the required number of decimal places without a thousands separator
        // (useful for entry fields as javascript parseFloat does not like thousands separators)
        $locale = localeconv();

        if ($pDecimalPlaces == -1) $pDecimalPlaces = $locale['frac_digits'];

        return number_format($pNumber, $pDecimalPlaces, $locale['decimal_point'], '');
    }

    static function str2Number($pStr, $pDecimalPoint = '', $pThousandsSeparator = '')
    {
        // convert a string back to a number taking into account the decimal point and thousands separator
        if (($pDecimalPoint == '') || ($pThousandsSeparator == ''))
        {
            $locale = localeconv();
            if ($pDecimalPoint == '')
            {
                $pDecimalPoint = $locale['decimal_point'];
            }

            if ($pThousandsSeparator == '')
            {
                $pThousandsSeparator = $locale['thousands_sep'];
            }
        }

        $number = (float) str_replace($pDecimalPoint, '.', str_replace($pThousandsSeparator, '', $pStr));

        if ($number == (int) $number)
        {
            return (int) $number;
        }
        else
        {
            return $number;
        }
    }

    static function createRandomString($pLength, $pUppercase = false)
    {
        // create a random alpha-numerical string (passwords / voucher codes)

        $result = '';

        if ($pUppercase)
        {
            $salt = 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        }
        else
        {
            $salt = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        }

        $saltLength = strlen($salt) - 1;

        for($i = 0; $i < $pLength; $i++)
        {
            $result .= $salt[mt_rand(0, $saltLength)];
        }

        return $result;
    }

    static function readConfigFile($pConfigFilePath)
    {
        // read a config file and return it as an exploded array
        $resultArray = Array();
        $comment = '#';

        if (file_exists($pConfigFilePath))
        {
            if ($fp = fopen($pConfigFilePath, 'rb'))
            {
                // determine if the file has a utf-8 bom and skip it if it does
                $bom = fread($fp, 3);
                if ($bom != "\xEF\xBB\xBF") rewind($fp);

                while(!feof($fp))
                {
                    $line = trim(fgets($fp));
                    if ($line && !preg_match("/^$comment/", $line))
                    {
                        $pieces = explode('=', $line, 2);

                        $option = trim($pieces[0]);
                        if (count($pieces) > 1)
                        {
                            $value = trim($pieces[1]);
                        }
                        else
                        {
                            $value = '';
                        }
                        $resultArray[$option] = $value;
                    }
                }

                fclose($fp);
            }
        }

        return $resultArray;
    }

    static function readWebBrandConfigFile($pConfigFilePath, $pWebBrandCode)
	{
		$resultArray = array();
		// read a High Level config file,
		// select file based web brand code
		// file name can be <configfile>_<webbrand>.conf
		$branding = '';

		if ($pWebBrandCode != '')
		{
			$branding = '_' . $pWebBrandCode;   // use brand code if possible
		}

		// determine filename to use

		// extract path and extension
		$pos = strrpos($pConfigFilePath, '.');
		$ext = substr($pConfigFilePath, $pos);
		$path = substr($pConfigFilePath, 0, $pos);

		// try in this order
		//      <integration>_<webbrand>.conf
		//      <integration>.conf

		if (file_exists($path . $branding . $ext))
		{
			$path .= $branding . $ext;
		}
		else
		{
			$path = $pConfigFilePath; // if all else fails
		}

		if (file_exists($path))
		{
			$resultArray = UtilsObj::readConfigFile($path);
		}

		return $resultArray;
	}

    static function readTextFile($pTextFilePath)
    {
        // read a text file and return it as a string
        $result = '';

        if ($fp = fopen($pTextFilePath, 'rb'))
        {
            // determine if the file has a utf-8 bom and skip it if it does
            $bom = fread($fp, 3);
            if ($bom != b"\xEF\xBB\xBF") rewind($fp);

            $result = fread($fp, filesize($pTextFilePath));

            fclose($fp);
        }

        return $result;
    }

    static function readUnicodeTextFile($pTextFilePath)
    {
        // read a text file and return it as a string
        $result = '';

        if ($fp = fopen($pTextFilePath, 'rb'))
        {
            // determine the file encoding
            $inputEncoding = '';

            $bom = fread($fp, 3);

            if ($bom == b"\xEF\xBB\xBF")
            {
                // utf8
                // no need to change the encoding
            }
            elseif (substr($bom, 0, 2) == b"\xFE\xFF")
            {
                // utf16 be
                $inputEncoding = 'UTF-16BE';
                $result = substr($bom, 2, 1);
            }
            elseif (substr($bom, 0, 2) == b"\xFF\xFE")
            {
                // utf16 le
                $inputEncoding = 'UTF-16LE';
                $result = substr($bom, 2, 1);
            }
            else
            {
                // no bom

                // rewind back to the start
                rewind($fp);
            }

            $result .= fread($fp, filesize($pTextFilePath));

            fclose($fp);

            if ($inputEncoding != '')
            {
                $result = mb_convert_encoding($result, 'UTF-8', $inputEncoding);
            }
        }

        return $result;
    }

    static function writeTextFile($pTextFilePath, $pText)
    {
        // write the string to a text file
        $result = false;

        if ($fp = @fopen($pTextFilePath, 'w'))
        {
            $result = fwrite($fp, $pText);

            fclose($fp);

            $result = true;
        }
		else
		{
			error_log('fopen issue');
		}

        return $result;
    }

    static function deleteFile($pPath)
    {
        if (file_exists($pPath))
        {
            @unlink($pPath);
        }
    }

    static function deleteFolder($pSource)
    {
        // recursively delete a folder and its contents

        if (file_exists($pSource))
        {
            if (is_dir($pSource))
            {
                foreach(scandir($pSource) as $item)
                {
                    if (!strcmp($item, '.') || !strcmp($item, '..')) continue;

                    self::deleteFolder($pSource . "/" . $item);
                }
                rmdir($pSource);
            }
            else
            {
                unlink($pSource);
            }
        }
    }

    static function createFolder($pSource)
    {
        // create a folder
        $result = false;

        $pSource = self::correctPath($pSource, '/', false);

        if (! file_exists($pSource))
        {
            $origMask = umask(0);
            if (mkdir($pSource, 0777))
            {
                $result = true;
            }

            umask($origMask);
        }
        else
        {
            $result = true;
        }

        return $result;
    }

    static function createAllFolders($pSource)
    {
        // create a folder
        $result = false;

        $pSource = self::correctPath($pSource, '/', false);

        if (!file_exists($pSource))
        {
            $origMask = umask(0);
            if (mkdir($pSource, 0777, true))
            {
                $result = true;
            }

            umask($origMask);
        }
        else
        {
            $result = true;
        }

        return $result;
    }

    /**
	 * Moves a temporary file into the correct location, also creating the directory if necessary.
	 *
	 * @param string $pTempPath Location of the temporary file to move.
	 * @param string $pDestinationPath Location to move the file to.
	 * @return string Empty if no errors, else contains the error string key.
	 */
	static function moveUploadedFile($pTempPath, $pDestinationPath)
	{
        $result = '';

        if ($pTempPath != '')
        {
            $createFileResult = UtilsObj::createAllFolders(dirname($pDestinationPath));

            if ($createFileResult)
            {
                $renameResult = rename($pTempPath, $pDestinationPath);

                if (! $renameResult)
                {
                    $result = 'str_ErrorUnableToMoveFile';
                }
            }
            else
            {
                $result = 'str_ErrorUnableToMoveFile';
            }
        }
        return $result;
    }

    static function correctPath($pSourcePath, $pSeparator = "/", $pTrailing = true)
    {
        // correct the supplied path making sure it either has or has not got a trailing separator
        $lastChar = substr($pSourcePath, -1, 1);

        if (($pTrailing == true) && ($lastChar != $pSeparator))
        {
            $pSourcePath = $pSourcePath . $pSeparator;
        }
        elseif (($pTrailing == false) && ($lastChar == $pSeparator))
        {
            $pSourcePath = substr($pSourcePath, 0, strlen($pSourcePath) - 1);
        }

        return $pSourcePath;
    }

    static function dircopy($srcdir, $dstdir, $subdirs = false)
    {
        // copy the source directory to the destination directory
        $copied = true;

        if (!is_dir($dstdir))
        {
            mkdir($dstdir);
        }

        if ($curdir = opendir($srcdir))
        {
            while($file = readdir($curdir))
            {
                if ($file != '.' && $file != '..')
                {
                    $srcfile = $srcdir . '/' . $file;
                    $dstfile = $dstdir . '/' . $file;

                    if (is_file($srcfile))
                    {
                        if (is_file($dstfile))
                        {
                            $ow = filemtime($srcfile) - filemtime($dstfile);
                        }
                        else
                        {
                            $ow = 1;
                        }

                        if ($ow > 0)
                        {
                            if (copy($srcfile, $dstfile))
                            {
                                touch($dstfile, filemtime($srcfile));
                            }
                            else
                            {
                                $copied = false;
                            }
                        }
                    }
                    elseif (is_dir($srcfile))
                    {
                        if ($subdirs)
                        {
                            $copied = self::dircopy($srcfile, $dstfile, true);
                        }
                    }
                }
            }
            closedir($curdir);
        }

        return $copied;
    }

	static function deleteOldFiles($pSourcePath, $pMaxAge)
	{
		// delete files over a certain number of minutes old
		$result = 0;

		if ($pSourcePath != '')
		{
			if (is_dir($pSourcePath))
			{
				$dirHandle = @opendir($pSourcePath);
				if ($dirHandle)
				{
					while (false !== ($dirEntry = readdir($dirHandle)))
					{
						if (substr($dirEntry, 0, 1) != '.')
						{
							$dirEntryPath = $pSourcePath . $dirEntry;

							if (! is_dir($dirEntryPath))
							{
								$timeDiff = (time() - filemtime($dirEntryPath)) / 60;

								if ($timeDiff > $pMaxAge)
								{
									if (@unlink($dirEntryPath))
									{
										$result++;
									}
								}
							}
						}
					}

					closedir($dirHandle);
				}
			}
		}

		return $result;
	}

    static function calcOrderFilePath($pGroupCode, $pOrderNumber = '', $pUploadRef = '')
    {
        // calculate the path to the folder containing the order data
        global $ac_config;

        $orderFilePath = self::correctPath($ac_config['INTERNALORDERSROOTPATH']);
        if ($ac_config['FTPGROUPORDERSBYCODE'] == '1')
        {
            $orderFilePath .= $pGroupCode . '/';
        }

        if ($pOrderNumber != '')
        {
            $orderFilePath .= $pOrderNumber . '/';
        }

        if ($pUploadRef != '')
        {
            $orderFilePath .= 'Order_' . $pUploadRef . '/';
        }

        return $orderFilePath;
    }

    static function getPOSTParam($pParam, $pDefaultValue = '')
    {
        // return the POST parameter's value or the default value if it isn't present
        if (array_key_exists($pParam, $_POST))
        {
            return $_POST[$pParam];
        }
        else
        {
            return $pDefaultValue;
        }
    }

    static function getGETParam($pParam, $pDefaultValue = '')
    {
        // return the GET parameter's value or the default value if it isn't present
        if (array_key_exists($pParam, $_GET))
        {
            return $_GET[$pParam];
        }
        else
        {
            return $pDefaultValue;
        }
    }

    static function getArrayParam($paramArray, $key, $pDefaultValue = '')
    {
        // return the array's parameter value or the default value if it isn't present
        if (array_key_exists($key, $paramArray))
        {
            return $paramArray[$key];
        }
        else
        {
            return $pDefaultValue;
        }
    }

	static function getHighLevelBasketAPIPOSTParams($pParam, $pDefaultValue)
	{
		if (array_key_exists($pParam, $_POST))
        {
            if ($_POST[$pParam] != '')
            {
            	return $_POST[$pParam];
            }
            else
            {
            	return $pDefaultValue;
            }
        }
        else
        {
            return $pDefaultValue;
        }
	}

	static function getHighLevelBasketAPIGETParams($pParam, $pDefaultValue)
	{
		if (array_key_exists($pParam, $_GET))
		{
			if ($_GET[$pParam] != '')
			{
				return $_GET[$pParam];
			}
			else
			{
				return $pDefaultValue;
			}
		}
		else
		{
			return $pDefaultValue;
		}
	}

    static function wait($pDelay)
    {
        // wait (sleep) for the specified number of seconds
        usleep(floor($pDelay * 1000000));
    }

    static function debugString($pSourceString)
    {
        // write the string to a debug text file
        // useful when debuggin background processes with no interace (eg: payment callbacks)
        $fp = fopen('mawebdebug.txt', 'a');
        if ($fp)
        {
            fwrite($fp, $pSourceString . "\n");
            fclose($fp);
        }
    }

    static function processPreviewFileUpload()
    {
        // process an image file which has been uploaded via the browser
        $resultArray = Array();
        $result = '';

        $previewTempFileName = $_FILES['preview']['tmp_name'];
        $previewFileType = $_FILES['preview']['type'];
        $previewFileSize = $_FILES['preview']['size'];
        $previewFileData = '';

        $validImageTypes = Array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png');

        if ($previewFileSize > 0)
        {
            // first make sure that we are dealing with a file that has been uploaded
            if (is_uploaded_file($previewTempFileName))
            {
                // make sure that the file is a valid type
                if (in_array(strtolower($previewFileType), $validImageTypes))
                {
                    // read the image data
                    $fp = fopen($previewTempFileName, 'rb');
                    if ($fp)
                    {
                        $previewFileData = fread($fp, filesize($previewTempFileName));
                        fclose($fp);
                    }
                    else
                    {
                        $previewFileSize = 0;
                    }
                }
                else
                {
                    $result = 'str_ErrorUploadInvalidFileType';
                }

                // remove the temp upload file
                self::deleteFile($previewTempFileName);
            }
            else
            {
                $previewFileSize = 0;
            }
        }

        $resultArray['result'] = $result;
        $resultArray['filetype'] = $previewFileType;
        $resultArray['filesize'] = $previewFileSize;
        $resultArray['filedata'] = $previewFileData;

        return $resultArray;
    }

    static function getBrowserLocale()
    {
        global $gConstants;

        // determine the browser locale either from our cookie or the headers sent from the browser
        $browserLanguage = '';

        if (isset($_COOKIE['maweblocale']))
        {
            $browserLanguage = $_COOKIE['maweblocale'];
        }

        if ($browserLanguage == '' && isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
        {
            // get the first (main) browser language
            $browserLanguageArray = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $browserLanguage = strtolower($browserLanguageArray[0]);

			switch ($browserLanguage)
			{
				case 'zh-tw':
				case 'zh-cn':
				{
					// don't do anything if the language is set to Chinese as we need to detect
					// if it is Chinese Traditional or Chinese Simplified

					break;
				}
				default:
				{
					// if the language code is longer than 2 characters (i.e en-GB) then
					// we only need the first 2 characters (en) for the language code

					if (strlen($browserLanguage) > 2)
					{
						$browserLanguage = substr($browserLanguage, 0, 2);
					}
					break;
				}
			}
        }

        // still no language code? use the system's default language
        if ($browserLanguage == '')
        {
            $browserLanguage = $gConstants['defaultlanguagecode'];
        }

        // remove any potentially malicious characters from the cookie as it will be used to influence file paths
        $browserLanguage = str_replace(Array("\0", "\\", '/', '.', ':', ' ', '<', '>', '|', '"', "'", '?', '*', '&', ';', ',', '~', '%',
            'CON', 'PRN', 'AUX', 'NUL', 'COM1', 'COM2', 'COM3', 'COM4', 'COM5', 'COM6', 'COM7', 'COM8', 'COM9', 'LPT1', 'LPT2',
            'LPT3', 'LPT4', 'LPT5', 'LPT6', 'LPT7', 'LPT8', 'LPT9'), '', $browserLanguage);

        return str_replace('-', '_', $browserLanguage);
    }

    static function calcClientCookieExpiryTime($pDuration)
    {
        // calculate the expiry time for the client cookie
        $expirationTime = time() + $pDuration;

        // determine if we have our time zone cookie which contains the user's local time
        // this allows us to calculate the time difference and correct the expiration date/time sent back to the browser
        // this fixes issues with the user's date/time settings being incorrect
        if (isset($_COOKIE['mawebtz']))
        {
            $hourOffset = round(((int) $_COOKIE['mawebtz'] - time()) / (60 * 60), 2);
            $expirationTime = $expirationTime + ($hourOffset * 60 * 60);
        }

        return $expirationTime;
    }

	static function calcHighLevelClientCookieExpiryTime($pDuration, $pProductSelectorUTC)
    {
        // calculate the expiry time for the client cookie
        $expirationTime = time() + $pDuration;

        // this allows us to calculate the time difference and correct the expiration date/time sent back to the browser
        // this fixes issues with the user's date/time settings being incorrect
        $hourOffset = round(((int) $pProductSelectorUTC - time()) / (60 * 60), 2);
        $expirationTime = $expirationTime + ($hourOffset * 60 * 60);

        return $expirationTime;
    }

	static function calcResetPasswordLinkExpiryTime($pRequestTime, $pDuration)
	{
		// calculate the expiry time for the reset password link
        $expirationTime = $pRequestTime + $pDuration;

		// determine if we have our time zone cookie which contains the user's local time
        // this allows us to calculate the time difference and correct the expiration date/time
        // this fixes issues with the user's date/time settings being incorrect
        if (isset($_COOKIE['mawebtz']))
        {
            $hourOffset = round(((int) $_COOKIE['mawebtz'] - $pRequestTime) / (60 * 60), 2);
            $expirationTime = $expirationTime + ($hourOffset * 60 * 60);
        }

		return $expirationTime;
	}

    static function trimLeftChars($pSourceString, $pCharCount = 1)
    {
        return substr($pSourceString, $pCharCount);
    }

    static function trimRightChars($pSourceString, $pCharCount = 1)
    {
        return substr_replace($pSourceString, '', ($pCharCount * -1));
    }

    static function leftChars($pSourceString, $pCharCount = 1)
    {
        return substr_replace($pSourceString, '', $pCharCount);
    }

    static function decodeString($pSourceString, $pEscape = false)
    {
        if ($pEscape == true)
        {
            return str_replace("\\'", "'", str_replace('\\"', '"', $pSourceString));
        }
        else
        {
            return htmlspecialchars_decode($pSourceString, ENT_QUOTES);
        }
    }

    static function encodeString($pSourceString, $pEscape = false)
    // pEscape == false : for use in HTML
    // pEscape == true  : for use in JavaScript, e.g. var county = {$county};
    {
        if (is_null($pSourceString)) return '';

        if ($pEscape == true)
        {
            $from = array('\\', "'", '"');
            $to = array('\\\\', "\\'", '\\"');
            return !\is_array($pSourceString) ? str_replace($from, $to, $pSourceString) : \array_map(function($string) use ($from, $to) { return str_replace($from, $to, $string); }, $pSourceString);
        }
        else
        {
            $result = htmlspecialchars($pSourceString, ENT_QUOTES, 'UTF-8');
            $from = array('\\');
            $to = array('\\\\');
            return str_replace($from, $to, $result);
        }
    }

    static function escapeForJavascript($pSourceString)
    // escape quotes for use in javascript, e.g. onclick="return alert('Delete Product "Bob's Photobook"')"
    {
        $from = array('\\', "&", "'", '"');
        $to = array('\\\\', "&amp;", "\\'", "&quot;");
        $string = !\is_array($pSourceString) ? str_replace($from, $to, $pSourceString) : \array_map(function($string) use ($from, $to) { return str_replace($from, $to, $string); }, $pSourceString);

        return $string;
    }

    /**
	 * Get the HTTP scheme of the url which has been called.
	 *
     * @param string $pServerArray
	 * @return string
	 */
    static function getHTTPScheme($pServerArray)
    {
        $scheme = 'http';

        // Determine the HTTP scheme by checking the server vars HTTPS and HTTP_X_FORMWARDED_PROTO
        // Only set the scheme to https if HTTPS is present and not set to off
        // OR
        // Only set the scheme to https if HTTP_X_FORWARDED_PROTO is present and set to HTTPS
        // Checking HTTP_X_FORWARDED_PROTO is required so that the system works behind an AWS ELB (load balancer) since in this case apache will be serving out http not https.
        // The load balancer will be processing the https traffic and forwarding it on to http
        if ((self::getArrayParam($pServerArray, 'HTTPS', 'off') !== 'off') || (strtoupper(self::getArrayParam($pServerArray, 'HTTP_X_FORWARDED_PROTO', '')) == 'HTTPS'))
        {
            $scheme .= 's';
        }

        $scheme .= '://';

        return $scheme;
    }

    // client IP address
    static function getClientIPAddress()
    {
        $clientIPAddress = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
            $clientIPAddress = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
            $clientIPAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        elseif (!empty($_SERVER['REMOTE_ADDR']))
        {
            $clientIPAddress = $_SERVER['REMOTE_ADDR'];
		}
		else
		{
			// Task scheduler calls from the CLI which will not have a populated $_SERVER
			// Set the client IP to localhost so we know that the client is the CLI
			$clientIPAddress = "127.0.0.1";
		}

        return $clientIPAddress;
    }

    static function compareApplicationVersions($pApplicationVersion1, $pApplicationVersion2)
    {
        $theResult = '=';

        $version1Array = explode('.', $pApplicationVersion1);
        $version2Array = explode('.', $pApplicationVersion2);

        // add the pre-release version numbers if they are not present
        if (count($version1Array) == 3)
        {
            array_push($version1Array, '999');
        }

        if (count($version2Array) == 3)
        {
            array_push($version2Array, '999');
        }

        if ((count($version1Array) == 4) && (count($version2Array) == 4))
        {
            for($i = 0; $i < 4; $i++)
            {
                if ((is_numeric($version1Array[$i])) && (is_numeric($version2Array[$i])))
                {
                    $digit1 = (int) $version1Array[$i];
                    $digit2 = (int) $version2Array[$i];

                    if ($digit1 > $digit2)
                    {
                        $theResult = '>';
                        break;
                    }
                    elseif ($digit1 < $digit2)
                    {
                        $theResult = '<';
                        break;
                    }
                }
                else
                {
                    $theResult = '';
                    break;
                }
            }
        }
        else
        {
            $theResult = '';
        }

        return $theResult;
    }

    /**
     * return the url pass by parameter or empty if the url is not correct
     *
     * @param $pUrl string // url to test
     */
    static function getValidUrl($pUrl)
    {
        if (($pUrl != 'http://') && ($pUrl != 'https://') && ($pUrl != ''))
        {
            return $pUrl;
        }
        else
        {
            return '';
        }
    }

    static function utf8ToHtmlCodepoints($string)
    // convert UTF-8 to codepoints to be used in HTML
    {
        $codepoints = '';
        $len = strlen($string);
        $i = 0;
        while($i < $len)
        {
            $string1 = substr($string, $i, 1);
            $byte1 = ord($string1);
            if ($byte1 < 128) // ASCII
            {
                $codepoints .= $string1;
            }
            else if ($byte1 >= 128 && $byte1 <= 191)  // 2nd, 3rd, or 4th byte of a multi-byte sequence
            {
                // can only appear as the second or later byte in a multi-byte sequence
            }
            else if ($byte1 >= 192 && $byte1 <= 193)  // Overlong encoding: start of a 2-byte sequence, but code point <= 127
            {
                // can never appear in a legal UTF-8 sequence
            }
            else if ($byte1 >= 194 && $byte1 <= 223)  // two byte character
            {
                $byte2 = ord(substr($string, ++$i, 1));
                $codepoint = ($byte1 - 192) * 64 + ($byte2 - 128);
                $codepoints .= '&#' . $codepoint . ';';
            }
            else if ($byte1 >= 224 && $byte1 <= 239) // three byte character
            {
                $byte2 = ord(substr($string, ++$i, 1));
                $byte3 = ord(substr($string, ++$i, 1));
                $codepoint = ($byte1 - 224) * 4096 + ($byte2 - 128) * 64 + ($byte3 - 128);
                $codepoints .= '&#' . $codepoint . ';';
            }
            else if ($byte1 >= 240 && $byte1 <= 244) // four byte character
            {
                $byte2 = ord(substr($string, ++$i, 1));
                $byte3 = ord(substr($string, ++$i, 1));
                $byte4 = ord(substr($string, ++$i, 1));
                $codepoint = ($byte1 - 224) * 262144 + ($byte2 - 224) * 4096 + ($byte3 - 128) * 64 + ($byte4 - 128);
                $codepoints .= '&#' . $codepoint . ';';
            }
            else if ($byte1 >= 245 && $byte1 <= 253) // Restricted by RFC 3629
            {
                // can never appear in a legal UTF-8 sequence
            }
            else if ($byte1 >= 254 && $byte1 <= 255) // Invalid: not defined by original UTF-8 specification
            {
                // can never appear in a legal UTF-8 sequence
            }
            $i++;
        }

        return $codepoints;
    }

    /**
     * Depending on parameters, does one of the following
     * - removes all image tags
     * - removes only relative image tags
     * - replaces all image tags with provided image.
     * Optionally convert to Unicode code points.
     *
     * @since Version 2.5.3
     * @author Steffen Haugk
     * @return String
     */
    static function cleanHTML($pHTML, $pRemoveAll = false, $pConvertToUnicodeCodePoints = false, $pImage = '')
    {
        // remove all style includes
        $returnHTML = preg_replace('/<style.*?<\/style>/', '', $pHTML);

        // remove all javascript includes
        $returnHTML = preg_replace('/<script.*?<\/script>/', '', $returnHTML);

        // Replace newlines, tabulators and superfluous spaces
        $search = array("\t", "\n", '        ', '    ', '  ', '  ', '> <');
        $replace = array(' ', ' ', ' ', ' ', ' ', ' ', "><");
        $returnHTML = str_replace($search, $replace, $returnHTML);

        if ($pImage == '')
        { // no image
            if ($pRemoveAll)
            {
                // remove all image tags
                $returnHTML = preg_replace('/<img[^>]*?src=["\'].*?["\'].*?>/', '', $returnHTML);
            }
            else
            {
                // remove only relative image tags
                $returnHTML = preg_replace('/<img[^>]*?src=["\'][^>:]*?["\'].*?>/', '', $returnHTML);
            }
        }
        else
        {
            // replace all image tags with provided tag
            $returnHTML = preg_replace('/<img[^>]*?src=["\'].*?["\'].*?>/', $pImage, $returnHTML);
        }

        if ($pConvertToUnicodeCodePoints)
        {
            $returnHTML = self::utf8ToHtmlCodepoints($returnHTML);
        }

        // Replace newlines, tabulators and superfluous spaces
        $search = array("\t", "\n", '        ', '    ', '  ', '  ', '> <');
        $replace = array(' ', ' ', ' ', ' ', ' ', ' ', "><");
        $returnHTML = str_replace($search, $replace, $returnHTML);

        return $returnHTML;
    }

    static function getWebURl($pWebBrandCode = '')
    {
        global $gSession;
        $webBrandURL = '';

        if ($pWebBrandCode == '')
        {
            $webBrandCode = $gSession['webbrandcode'];
        }
        else
        {
            $webBrandCode = $pWebBrandCode;
        }

        $webBrandArray = DatabaseObj::getBrandingFromCode($webBrandCode);

        if (($webBrandArray['isactive'] == 1) && ($webBrandArray['weburl'] != ''))
        {
            $webBrandURL = $webBrandArray['weburl'];
        }

        // there is no url so we need to use the url from the default brand.
        if ($webBrandURL === '')
        {
            $defaultBrand = DatabaseObj::getBrandingFromCode('');
            $webBrandURL = $defaultBrand['weburl'];
        }

        return self::correctPath($webBrandURL);
    }

    static function getBrandedWebUrl($pWebBrandCode = '')
    {
        return self::getBrandedUrl('weburl', $pWebBrandCode);
    }

    static function getBrandedDisplayUrl($pWebBrandCode = '')
    {
        return self::getBrandedUrl('displayurl', $pWebBrandCode);
    }

    static function getBrandedUrl($pURLType, $pWebBrandCode = '')
    // return Web URL either from Config or Branding
    {
        global $ac_config;
        global $gSession;

        if ($pWebBrandCode == '')
        {
            $webBrandCode = (isset($gSession['webbrandcode']) ? $gSession['webbrandcode'] : '');
        }
        else
        {
            $webBrandCode = $pWebBrandCode;
        }

        $webBrandArray = DatabaseObj::getBrandingFromCode($webBrandCode);

        if ($webBrandArray['isactive'] == 1)
        {
            if ($webBrandArray[$pURLType] != '')
            {
                $webBrandURL = $webBrandArray[$pURLType];
            }
            else
            {
                // there is no url so we need to create the default one which requires the url from the default brand
                $defaultBrand = DatabaseObj::getBrandingFromCode('');

                $webBrandURL = self::correctPath($defaultBrand[$pURLType]);

                if ($pURLType !== 'weburl')
                {
                    $webBrandURL .= ($ac_config['WEBBRANDFOLDERNAME'] == '' ? 'Branding' : $ac_config['WEBBRANDFOLDERNAME']);
                    $webBrandURL .= '/' . $webBrandArray['name'] . '/';
                }
            }
        }
        else
        {
            // if the brand is not active get the default brand
            $defaultBrand = DatabaseObj::getBrandingFromCode('');
            $webBrandURL = $defaultBrand[$pURLType];
        }

        return self::correctPath($webBrandURL);
    }

    static function FormatEmailSettings($pAddress)
    {
        $emailAddress = str_replace("\r\n", ";", $pAddress);
        $emailAddress = str_replace("\n", ";", $emailAddress);
        $emailAddress = str_replace("\r", ";", $emailAddress);
        $emailAddress = str_replace(";;", ";", $emailAddress);

        //get string length to see if the last part is a ; if it is strip it if not don't
        $emailAddressLastChar = substr($pAddress, -1);

        if ($emailAddressLastChar == ";")
        {
            $emailAddress = substr($pAddress, 0, -1);
            $emailAddressLastChar = substr($emailAddress, -1);
        }

        return $emailAddress;
    }

    static function FormatEmailNameSettings($pName)
    {
        $emailNames = str_replace(";;", ";", $pName);
        //get string length to see if the last part is a ; if it is strip it if not don't
        $emailNamesLastChar = substr($pName, -1);

        if ($emailNamesLastChar == ";")
        {
            $emailNames = substr($pName, 0, -1);
            $emailNamesLastChar = substr($emailNames, -1);
        }

        return $emailNames;
    }

    static function ExtJSEscape($pString)
    {
        if (is_null($pString)) return '';

        $pString = str_replace("\n\r", "\n", $pString);
        $pString = str_replace("\r", "\n", $pString);
        $pString = str_replace("\n", '<br>', $pString);
        $pString = str_replace("\\", "\\\\", $pString);
        $pString = str_replace("'", "\\'", $pString);

        return $pString;
    }

    static function getUserLoginConstants()
    {
        global $gSession;
        global $gConstants;

        //get a list of routing constants and put them into an array
        $smarty = SmartyObj::newSmarty('AdminUsers');

        if ($gConstants['optioncfs'] && $gConstants['optionms'])
        {
            switch($gSession['userdata']['usertype'])
            {

                case TPX_LOGIN_SYSTEM_ADMIN:
                    $userLoginConstants = array
                        (
                        array('id' => TPX_LOGIN_SYSTEM_ADMIN, 'name' => $smarty->get_config_vars('str_LabelSystemAdministrator')),
                        array('id' => TPX_LOGIN_COMPANY_ADMIN, 'name' => $smarty->get_config_vars('str_LabelCompanyAdmin')),
                        array('id' => TPX_LOGIN_SITE_ADMIN, 'name' => $smarty->get_config_vars('str_LabelSiteAdmin')),
                        array('id' => TPX_LOGIN_CREATOR_ADMIN, 'name' => $smarty->get_config_vars('str_LabelCreatorAdmin')),
                        array('id' => TPX_LOGIN_PRODUCTION_USER, 'name' => $smarty->get_config_vars('str_LabelProductionUser')),
                        array('id' => TPX_LOGIN_DISTRIBUTION_CENTRE_USER, 'name' => $smarty->get_config_vars('str_LabelDistributionCentreLogin')),
                        array('id' => TPX_LOGIN_STORE_USER, 'name' => $smarty->get_config_vars('str_LabelStoreUser')),
                        array('id' => TPX_LOGIN_BRAND_OWNER, 'name' => $smarty->get_config_vars('str_LabelBrandOwner')),
                        array('id' => TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER, 'name' => $smarty->get_config_vars('str_LabelUnlockSystemAccountUser'))
                    );
                    break;
                case TPX_LOGIN_COMPANY_ADMIN:
                    $userLoginConstants = array
                        (
                        array('id' => TPX_LOGIN_SITE_ADMIN, 'name' => $smarty->get_config_vars('str_LabelSiteAdmin')),
                        array('id' => TPX_LOGIN_PRODUCTION_USER, 'name' => $smarty->get_config_vars('str_LabelProductionUser')),
                        array('id' => TPX_LOGIN_BRAND_OWNER, 'name' => $smarty->get_config_vars('str_LabelBrandOwner'))
                    );
                    break;
                case TPX_LOGIN_SITE_ADMIN:
                    $userLoginConstants = array
                        (
                        array('id' => TPX_LOGIN_PRODUCTION_USER, 'name' => $smarty->get_config_vars('str_LabelProductionUser'))
                    );

                    break;
            }
        }
        else if ($gConstants['optionms'])
        {
            switch($gSession['userdata']['usertype'])
            {

                case TPX_LOGIN_SYSTEM_ADMIN:
                    $userLoginConstants = array
                        (
                        array('id' => TPX_LOGIN_SYSTEM_ADMIN, 'name' => $smarty->get_config_vars('str_LabelSystemAdministrator')),
                        array('id' => TPX_LOGIN_COMPANY_ADMIN, 'name' => $smarty->get_config_vars('str_LabelCompanyAdmin')),
                        array('id' => TPX_LOGIN_SITE_ADMIN, 'name' => $smarty->get_config_vars('str_LabelSiteAdmin')),
                        array('id' => TPX_LOGIN_CREATOR_ADMIN, 'name' => $smarty->get_config_vars('str_LabelCreatorAdmin')),
                        array('id' => TPX_LOGIN_PRODUCTION_USER, 'name' => $smarty->get_config_vars('str_LabelProductionUser')),
                        array('id' => TPX_LOGIN_BRAND_OWNER, 'name' => $smarty->get_config_vars('str_LabelBrandOwner')),
						array('id' => TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER, 'name' => $smarty->get_config_vars('str_LabelUnlockSystemAccountUser'))
                    );
                    break;
                case TPX_LOGIN_COMPANY_ADMIN:
                    $userLoginConstants = array
                        (
                        array('id' => TPX_LOGIN_SITE_ADMIN, 'name' => $smarty->get_config_vars('str_LabelSiteAdmin')),
                        array('id' => TPX_LOGIN_PRODUCTION_USER, 'name' => $smarty->get_config_vars('str_LabelProductionUser')),
                        array('id' => TPX_LOGIN_BRAND_OWNER, 'name' => $smarty->get_config_vars('str_LabelBrandOwner'))
                    );
                    break;
                case TPX_LOGIN_SITE_ADMIN:
                    $userLoginConstants = array
                        (
                        array('id' => TPX_LOGIN_PRODUCTION_USER, 'name' => $smarty->get_config_vars('str_LabelProductionUser'))
                    );

                    break;
            }
        }
        elseif ($gConstants['optioncfs'])
        {
            switch($gSession['userdata']['usertype'])
            {
                case TPX_LOGIN_SYSTEM_ADMIN:
                    $userLoginConstants = array
                        (
                        array('id' => TPX_LOGIN_SYSTEM_ADMIN, 'name' => $smarty->get_config_vars('str_LabelSystemAdministrator')),
                        array('id' => TPX_LOGIN_CREATOR_ADMIN, 'name' => $smarty->get_config_vars('str_LabelCreatorAdmin')),
                        array('id' => TPX_LOGIN_PRODUCTION_USER, 'name' => $smarty->get_config_vars('str_LabelProductionUser')),
                        array('id' => TPX_LOGIN_DISTRIBUTION_CENTRE_USER, 'name' => $smarty->get_config_vars('str_LabelDistributionCentreLogin')),
                        array('id' => TPX_LOGIN_STORE_USER, 'name' => $smarty->get_config_vars('str_LabelStoreUser')),
                        array('id' => TPX_LOGIN_BRAND_OWNER, 'name' => $smarty->get_config_vars('str_LabelBrandOwner')),
						array('id' => TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER, 'name' => $smarty->get_config_vars('str_LabelUnlockSystemAccountUser'))
                    );
                    break;
            }
        }
        else
        {
            $userLoginConstants = array
                (
                array('id' => TPX_LOGIN_SYSTEM_ADMIN, 'name' => $smarty->get_config_vars('str_LabelSystemAdministrator')),
                array('id' => TPX_LOGIN_CREATOR_ADMIN, 'name' => $smarty->get_config_vars('str_LabelCreatorAdmin')),
                array('id' => TPX_LOGIN_PRODUCTION_USER, 'name' => $smarty->get_config_vars('str_LabelProductionUser')),
                array('id' => TPX_LOGIN_BRAND_OWNER, 'name' => $smarty->get_config_vars('str_LabelBrandOwner')),
				array('id' => TPX_LOGIN_UNLOCKSYSTEMACCOUNT_USER, 'name' => $smarty->get_config_vars('str_LabelUnlockSystemAccountUser'))
            );
        }

        if (($gSession['userdata']['usertype'] == TPX_LOGIN_SYSTEM_ADMIN) && ($gConstants['optionscbo']))
        {
            array_push($userLoginConstants, array('id' => TPX_LOGIN_API, 'name' => $smarty->get_config_vars('str_LabelAPILogin')));
        }

        $resultArray = $userLoginConstants;

        return $resultArray;
    }

    /**
     * Decodes the base64 encoded data that contains a blowfish encrypted string
     *
     * To decrypt the data correctly we need to know the original length of the decrypted data
     * This is provided at the start of the string and terminated with an equals sign
     * eg: len=base64data
     * NOTE. this function does not actually decrypt the string
     *
     * @static
     *
     * @param string $pString
     *   the base64 encoded string containing the encrypted data. NOTE. the decoded string is returned in this parameter (by ref)
     *
     * @param string $pOrigLength
     *   the variable that will hold the original length of the decrypted data (by ref)
     *
     * @author Kevin Gale
     * @since Version 3.0.0
     */
    static function decodeTransmissionString(&$pString, &$pOrigLength)
    {
        // decode a blowfish encrypted string that has been transmitted to taopix web ready for decrypting
        $pos = strpos($pString, '=');
        $strLen = substr($pString, 0, $pos);
        $strLen = (int) $strLen;
        $pString = base64_decode(substr($pString, $pos + 1));
        $pOrigLength = $strLen;
    }

    static function includeStoreLocatorScript()
    {
    	if (file_exists('../Customise/scripts/EDL_StoreLocator.php')) // failing that use generic script name
        {
            require_once('../Customise/scripts/EDL_StoreLocator.php');
        }
    }

    static function getTaopixWebInstallPath($pSubFolder = '')
    {
        // get the path to the current script (this script)
        $getFilePath = __FILE__;

        // get the parent directory, it's length and the platform separator character
        $directoryName = dirname($getFilePath);
        $dirLen = strlen($directoryName);
        $platformSeparator = substr($getFilePath, $dirLen, 1);

        // step back one more directory to the root folder and get it's path
        $directoryName = $directoryName . '/../';
        $realPath = realpath($directoryName);

        // correct the path so that we always have a trailing separator
        $realPath = self::correctPath($realPath, $platformSeparator);

        // check to see if there has been a sub-folder path passed
        // if so build the correct Taopix Web install path
        if ($pSubFolder != '')
        {
            // make the sub-folder separators the same as the platform separator
            if ($platformSeparator == '/')
            {
                $pSubFolder = str_replace("\\", '/', $pSubFolder);
            }
            else
            {
                $pSubFolder = str_replace('/', "\\", $pSubFolder);
            }

            // if the sub-folder also starts with a platform separator we need to exclude it when appending it
            if (substr($pSubFolder, 0, 1) == $platformSeparator)
            {
                $realPath .= substr($pSubFolder, 1);
            }
            else
            {
                $realPath .= $pSubFolder;
            }
        }

        return $realPath;
	}

	/**
	 * Returns the OrderStatusCache folder from the config file.
	 *
	 * @global array $ac_config The global config file array.
	 * @return string The path to the order status cache folder.
	 */
	static function getOrderStatusCachePath()
	{
		global $ac_config;

		$orderStatusCachePath = '';

		if (array_key_exists('CONTROLCENTREORDERSTATUSCACHEPATH', $ac_config))
		{
			$orderStatusCachePath = self::correctPath($ac_config['CONTROLCENTREORDERSTATUSCACHEPATH']);
		}

		return $orderStatusCachePath;
	}

	/**
	 * Returns the full URL to the order status cache file so it can be accessed via the web.
	 *
	 * @param string $pWebURL The web URL to build the URL.
	 * @param string $pBatchRef The batch ref to build the URL to.
	 * @return string The full URL to the order status cache file.
	 */
	static function getOrderStatusCacheURL($pWebURL, $pBatchRef)
	{
		return self::correctPath($pWebURL) . 'orderstatus/' . rawurlencode($pBatchRef) . '.inf';
	}

    static function generateAssetRetrievalID($pAssetID)
    {
        $md5Hash = md5(rand());
        $retrievalID = strtoupper($pAssetID . 'x' . $md5Hash);
        return $retrievalID;
    }

    static function getPreviewImage($pResultArray)
    {
        global $gSession;

        if ($pResultArray['previewtype'] != '')
        {
            Header('Content-type:' . $pResultArray['previewtype']);
            echo $pResultArray['image'];
        }
        elseif ($_GET['no'] == '1')
        {
            Header('Location: ' . UtilsObj::correctPath($gSession['webbrandwebroot']) . 'images/admin/nopreview.gif');
        }
    }

    /**
     * Generate a path for the order thumbnails.
     *
     * @param string $pUploadRef - upload ref of the order.
     * @param boolean $pCreatePath - create the path if required.
     */
    static function generateOrderThumbnailsPath($pUploadRef, $pCreatePath)
    {
        global $ac_config;

        $thumbnailsPathData = array(
            'root' => $ac_config['CONTROLCENTREORDERPREVIEWPATH'],
            'subpath' => '',
            'web' => '',
            'actual' => ''
        );

        // Get the date of the order to generate the directory structure.
        $orderDate = DatabaseObj::getOrderDateFromUploadRefs(array($pUploadRef));

        if ('' == $orderDate['result'])
        {
            // Generate the date path for the thumbnails.
            $dirPathFormat = 'Y/m/d/H';
            $thumbnailsPathData['subpath'] = '/' . date($dirPathFormat, $orderDate['data'][$pUploadRef]);

            // Create a path for use in the previews.
            $thumbnailsPathData['web'] = '/orderpreviews' . $thumbnailsPathData['subpath'];

            // Add the upload ref to complete the path to the destination folder.
            $thumbnailsPathData['actual'] = $thumbnailsPathData['root'] . $thumbnailsPathData['subpath'] . '/' . $pUploadRef;

            // Create the path if required.
            if ($pCreatePath)
            {
                // If the folder exists, delete it first.
                UtilsObj::deleteFolder($thumbnailsPathData['actual']);

                // Create the full folder structure for the thumbnails.
                UtilsObj::createAllFolders($thumbnailsPathData['actual']);
            }
        }

        return $thumbnailsPathData;
    }

    static function processOrderThumbnails($pAPIVersion, $pUploadRef)
    {
        // Create the thumbnail folder structure.
        $thumbsFolderData = UtilsObj::generateOrderThumbnailsPath($pUploadRef, true);

        $thumbsFolder = $thumbsFolderData['actual'];

        // check the source parameters and data
        $thumbnailArray = Array();
        $thumbnailsInFile = false;
        $thumbFilePath = '';

        $thumbnailCount = UtilsObj::getPOSTParam('thumbcount', 0);
        if ($thumbnailCount > 0)
        {
            // check to see if the thumbnail data has been provided as a file
            if (array_key_exists('thumbdata', $_FILES))
            {
                $thumbFileDataArray = $_FILES['thumbdata'];

                // check for a temporary file path
                $thumbFilePath = $thumbFileDataArray['tmp_name'];
                if ($thumbFilePath != '')
                {
                    // make sure the data was uploaded without any errors
                    if ($thumbFileDataArray['error'] == 0)
                    {
                        // attempt to open the temporary file
                        $fp = @fopen($thumbFilePath, 'rb');
                        if ($fp)
                        {
                            $thumbnailsInFile = true;
                        }
                        else
                        {
                            // the temporary file could not be opened so we cannot continue
                            $thumbnailCount = 0;
                        }
                    }
                    else
                    {
                        // an error occurred while uploading the thumbnail data so we cannot continue
                        $thumbnailCount = 0;
                    }
                }
                else
                {
                    // we do not have a temporary file path so we cannot continue
                    $thumbnailCount = 0;
                }
            }
        }


        // determine the format of the thumbnail data
        if ($pAPIVersion < 3)
        {
            // if the apiversion is older than 3 then we must base64 decode the data if provided as a parameter
            // we also set the preview version to only support slide shows
            $base64DecodeData = (!$thumbnailsInFile);
            $previewVersion = 1;
        }
        elseif ($pAPIVersion == 3)
        {
            // version 3 of the api does not have the data base64 encoded
            // but it does have page turning
            $base64DecodeData = false;
            $previewVersion = 2;
        }
        else
        {
            // later versions of the api have the data base64 encoded if provided as a parameter
            // and supports page turning
            $base64DecodeData = (!$thumbnailsInFile);
            $previewVersion = 2;
        }

		// Generate a time limit for the processing of over 3000 images.
		if ($thumbnailCount > 3000)
		{
			// For every block of 3000 thumbnails we add 60 seconds so we can produce the thumbnails.
			$timeLimit = 60 * (ceil(($thumbnailCount/3000)));
			set_time_limit($timeLimit);
		}

        // write the thumbnail data to the server
        for($i = 1; $i <= $thumbnailCount; $i++)
        {
            $thumbData = '';

            if ($thumbnailsInFile)
            {
                $thumbRef = substr(fgets($fp), 0, -1);
                $thumbName = substr(fgets($fp), 0, -1);
                $thumbWidth = substr(fgets($fp), 0, -1);
                $thumbHeight = substr(fgets($fp), 0, -1);
                $thumbDataLength = (int) substr(fgets($fp), 0, -1);
                $thumbData = fread($fp, $thumbDataLength);
                $skip = fread($fp, 1);
            }
            else
            {
                $itemCountString = str_pad($i, 3, '0', STR_PAD_LEFT);
                $thumbRef = UtilsObj::getPOSTParam('thumbref' . $itemCountString, '');
                $thumbName = UtilsObj::getPOSTParam('thumbname' . $itemCountString, '');
                $thumbWidth = UtilsObj::getPOSTParam('thumbwidth' . $itemCountString, 0);
                $thumbHeight = UtilsObj::getPOSTParam('thumbheight' . $itemCountString, 0);
                $thumbData = UtilsObj::getPOSTParam('thumbdata' . $itemCountString, '');
            }

            if ($thumbData != '')
            {
                if ($base64DecodeData)
                {
                    $thumbData = base64_decode($thumbData);
                }

                UtilsObj::writeTextFile($thumbsFolder . '/' . $thumbRef . '.jpg', $thumbData);

                $item = Array();
                $item['pageref'] = $thumbRef;
                $item['pagename'] = $thumbName;
                $item['thumbwidth'] = $thumbWidth;
                $item['thumbheight'] = $thumbHeight;
                $item['previewversion'] = $previewVersion;
                $thumbnailArray[] = $item;
            }
        }

        // close the temporary file handle if the thumbnails were from a file
        if ($thumbnailsInFile)
        {
            fclose($fp);
        }

        // delete the temporary file if the thumbnails were from a file
        if ($thumbFilePath != '')
        {
            self::deleteFile($thumbFilePath);
        }

        // write the thumbnail data to the database
        DatabaseObj::insertOrderThumbnails($pUploadRef, $thumbnailArray);
    }

	/**
	 * Escape input string for use in JavaScript output.
	 *
     * Characters chosen from best practice advice from OWASP.
	 * https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet#Output_Encoding_Rules_Summary
     *
     * Function ported from the Twig template engine
     * https://github.com/twigphp/Twig/blob/055dea2c6da65c3131171d91a9ca41e2e5ff0a6f/lib/Twig/Extension/Core.php
     *
	 * @param string $input
	 * @return string
	 */
    static function escapeInputForJavaScript($input)
    {
		return preg_replace_callback('#[^a-zA-Z0-9,\._]#Su', function ($matches) {
			$char = $matches[0];
			/*
			 * A few characters have short escape sequences in JSON and JavaScript.
			 * Escape sequences supported only by JavaScript, not JSON, are omitted.
			 * \" is also supported but omitted, because the resulting string is not HTML safe.
			 */
			static $shortMap = array(
				'\\' => '\\\\',
				'/' => '\\/',
				"\x08" => '\b',
				"\x0C" => '\f',
				"\x0A" => '\n',
				"\x0D" => '\r',
				"\x09" => '\t',
			);

			if (isset($shortMap[$char])) {
				return $shortMap[$char];
			}

			// \uHHHH
			$char = iconv('UTF-8', 'UTF-16BE', $char);
			$char = strtoupper(bin2hex($char));
			if (4 >= strlen($char)) {
				return sprintf('\u%04s', $char);
			}

			return sprintf('\u%04s\u%04s', substr($char, 0, -4), substr($char, -4));
		}, $input);
    }

	/**
     * Escape input string for use in HTML output.
     * Characters chosen from best practice advice from OWASP.
     * https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet#Output_Encoding_Rules_Summary
     *
	 * @param string $input
	 * @return string
	 */
    static function escapeInputForHTML($input)
    {
		return str_replace('\\', '\\\\', htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Clean up POST string to avoid XSS attack
     *
     * @static
     *
     * @param string $pSourceString
     *   the POST string
     *
     * @return string
     *   the POST string after removed malicious characters
     *
     * @author Kevin Gale
     * @since Version 3.0.0
     */
    static function cleanseInput($pSourceString, $pStrict = true)
    {
        $theResult = $pSourceString;
        $performCleanse = true;

        while($performCleanse)
        {
            $origLength = strlen($theResult);

            // first check to see if we have any percent signs as these could be url encoded values or unicode escapes
            if (strpos($theResult, '%') !== false)
            {
                $theResult = urldecode($theResult);
            }


            // decode any html entities
            $theResult = html_entity_decode($theResult, ENT_QUOTES, 'UTF-8');


            // remove any duplicate spaces
            $newLength = 0;
            while($newLength != strlen($theResult))
            {
                $newLength = strlen($theResult);

                $theResult = str_replace('  ', ' ', $theResult);
            }


            // remove any characters or words that could be used in xss attacks
            if ($pStrict)
            {
                $theResult = str_replace(Array('<', '>', ';', "\\", "//", '=', ':', 'javascript', 'vbscript', 'img src', 'onerror',
                    'alert(', 'window.location', 'document.createElement', 'createElement', 'document.body.appendChild',
                    'document.body.innerHTML', 'document.body', 'html', 'setAttribute', 'document.getElementsByTagName',
                    'getElementsByTagName', 'getElementByID', 'getElementsByName', 'appendChild'), '', $theResult);
            }
            else
            {
                $aPattern = Array(
                    '<',
                    '>',
                    "\\",
                    "//",
                    'javascript',
                    'vbscript',
                    'img src',
                    'onerror',
                    'alert(',
                    'window.location',
                    'document.createElement',
                    'createElement',
                    'document.body.appendChild',
                    'document.body.innerHTML',
                    'document.body',
                    'html',
                    'setAttribute',
                    'document.getElementsByTagName',
                    'getElementsByTagName',
                    'getElementByID',
                    'getElementsByName',
                    'appendChild');
                $theResult = str_replace($aPattern, '', $theResult);
            }

            // check to see if the string length has changed and if not there is no need to perform the cleanse again
            $newLength = strlen($theResult);
            if (($newLength == $origLength) || ($newLength == 0))
            {
                $performCleanse = false;
            }
        }

        return $theResult;
    }

    static function correctFileName($pFileName)
	{
		$theResult = '';

		$theResult = self::stripControlCharacters($pFileName);

		$aPattern = Array(
                    "<",
                    ">",
                    "\"",
                    "/",
                    "\\",
                    "//",
                    ":",
                    "*",
                    "?",
                    "|");

    	$theResult = str_replace($aPattern, '', $theResult);
		$theResult = trim($theResult);

		return $theResult;
	}

	static function stripControlCharacters($pSourceFileName)
	{
		$theResult = '';

		$theResult = self::multiByteStringStripControlCharacters($pSourceFileName);

		$aPattern = Array(
                    "<br>",
                    "<br />",
                    "<br/>",
                    "<body>",
                    "</body>",
                    "<p>",
                    "</p>",
                    "<eol>",
                    "<eof>",
                    "\n",
                    "\r",
                    "\r\n");


		$theResult = str_replace($aPattern, '', $theResult);

		return $theResult;
	}

	static function multiByteStringStripControlCharacters($pString)
	{
		$returnString = '';

		$count = mb_strlen($pString);
		for ($i = 0; $i < $count; $i++)
		{
			$char = mb_substr($pString, $i, 1, 'UTF-8');

			$k = mb_convert_encoding($char, 'UCS-4LE', 'UTF-8');
			$k1 = ord(substr($k, 0, 1));
			$k2 = ord(substr($k, 1, 1));
			$k3 = ord(substr($k, 2, 1));
			$k4 = ord(substr($k, 3, 1));

			$charCode = $k4 * 16777216 + $k3 * 65536 + $k2 * 256 + $k1;

			if ($charCode > 31)
			{
				$returnString .= $char;
			}
		}

		return $returnString;
	}

	static function cleanseLanguageCode($pLangCode, $pDefault)
	{
		$langCode = UtilsObj::cleanseInput($pLangCode, true);

		// make sure the lang code is either like "en" or like "zh_tw"
		// anything else is not allowed
		$matchesCount = preg_match("/^([A-Za-z]{2})(_[A-Za-z]{2})?$/", $langCode);

		if ($matchesCount == 0)
		{
			$langCode = $pDefault;
		}

		return $langCode;
	}

    static function readCompressedArray($pArray)
    {
        $uncompressedData = self::readCompressedData($pArray);

        return unserialize($uncompressedData);
    }

    static function readCompressedData($pData)
    {
        $base64 = base64_decode($pData);

        return gzinflate($base64);
    }

    static function compressArray($pArray)
    {
        $serialized = serialize($pArray);

        $gz = gzdeflate($serialized, 9);

        return base64_encode($gz);
    }

    static function getReturnArray($pDataItemName = 'data')
    {
        $returnArray = array();
        $returnArray['error'] = '';
        $returnArray['errorparam'] = '';
        $returnArray[$pDataItemName] = array();

        return $returnArray;
    }

    static function convertBytesToMB($pValue)
    {
        $converted = (float) $pValue / 1024 / 1024;

        return round($converted, 5);
    }

    static function compressData($pData)
    {
        $gz = gzdeflate($pData, 9);

        return base64_encode($gz);
    }

    static function formatVolumeSize($pMegaBytes, $unit = "", $decimals = 2)
	{
        bcscale(15);

		$unitRef = 0;
		$units = array('MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

		$bytes = $pMegaBytes;

		while (self::taopix_bccomp($bytes, 1024) == 1)
		{
			$bytes = self::taopix_bcdiv($bytes, 1024);
			$unitRef++;

			if ($unitRef == 6)
			{
				break;
			}
		}

		$bytes = number_format($bytes, 2);

		return $bytes . ' ' . $units[$unitRef];
	}

	static function getCurlPEMFilePath($pSubFolder = '')
    {
        return UtilsObj::getTaopixWebInstallPath($pSubFolder) .'libs' . DIRECTORY_SEPARATOR . 'internal' . DIRECTORY_SEPARATOR . 'curl' . DIRECTORY_SEPARATOR . 'curl-ca-bundle.pem';
    }

    static function getWebURLBaseDomain($webURL)
	{
		$url = @parse_url($webURL);

		if (empty($url['host'])) return;

		$parts = explode('.', $url['host']);
        $arraySlice = array_slice($parts, -2, 1);
		$slice = (strlen(reset($arraySlice)) == 2) && (count($parts) > 2) ? 3 : 2;

		return implode('.', array_slice($parts, (0 - $slice), $slice));
	}


	// resets the php script timeout either to 60 seconds or to the previous timeout + the increment
	static function resetPHPScriptTimeout($pIncrementTime = 0)
	{
		static $nextScriptTimeout = -1;

		if (($pIncrementTime == 0) || ($nextScriptTimeout == -1))
		{
			$newTimeout = 60;
		}
		else
		{
			$newTimeout = ($nextScriptTimeout - time()) + $pIncrementTime;
		}

		$newTimeout = max($newTimeout, 1);
		$nextScriptTimeout = time() + $newTimeout;

		set_time_limit($newTimeout);
	}

    static function detectDevice($pRequestFromLowLevelAPI)
    {
        //Detect the device parameters i.e. mobile status etc.
        $deviceDataArray = UtilsDeviceDetection::determineDevice($pRequestFromLowLevelAPI);

        if(($deviceDataArray['isactive'] == 0) && ($pRequestFromLowLevelAPI == false))
        {
            UtilsDeviceDetection::pingJS();
        }

        return $deviceDataArray;
    }

    static function setSessionDeviceData($pRequestFromLowLevelAPI = false)
    {
    	global $gSession;

    	//Detecting the device parameters
        $browserDataArray = self::detectDevice($pRequestFromLowLevelAPI);

        $gSession['ismobile'] = ($browserDataArray['ismobiledevice'] == "1") ? true : false;
        $gSession['islargescreen'] = ($browserDataArray['screensize'] == "1") ? true : false;

        if ($pRequestFromLowLevelAPI)
        {
        	return $browserDataArray;
        }
    }

    static function writeLogEntry($pMessage)
	{
		if ($pMessage != '')
		{
			echo date('Y-m-d H:i:s', time()) . '  ';
		}

		echo $pMessage . "\n";
	}

    /**
     * Create a referenced array
     *
     * @param mixed[] $arr
     * @return mixed[]
     */
    static function makeValuesReferenced($arr)
    {
        $refs = array();
        foreach ($arr as $key => $value)
        {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }

    static function getAllowedOriginForHighLevelBasketAPI($pWebBrandCode)
	{
		// this function will return the host of the calling server if it is in the allowed origin config list
		$allowedOrigin = '';
		$configAllowedOrigins = '';

		$hl_config = UtilsObj::readWebBrandConfigFile('../config/onlinebaskethighlevelapi.conf', $pWebBrandCode);

		if (array_key_exists('HIGHLEVELBASKETAPIALLOWREDORIGINS', $hl_config))
		{
			$configAllowedOrigins = $hl_config['HIGHLEVELBASKETAPIALLOWREDORIGINS'];
		}

		// get the list of allowed origins from the config file and split them by a comma
		$allowedOriginsArray = explode(',', $configAllowedOrigins);

		// Loop around all the allowed origins and remove the training forward slash
		$i = 0;
		$allowedOriginCount = count($allowedOriginsArray);
		for ($i = 0; $i < $allowedOriginCount; $i++)
		{
			$allowedOriginsArray[$i] = trim(self::correctPath($allowedOriginsArray[$i], "/", false));
		}

		// if the config contains a * then set the allowed origins to *
		if (in_array("*", $allowedOriginsArray))
		{
			$allowedOrigin = "*";
		}
		else
		{
			// get the current host orgin from the calling server
			$currentOrigin = self::getArrayParam($_SERVER, 'HTTP_ORIGIN', '');

			if ($currentOrigin == '')
			{
				// if the current origin is missing then try and use the referer
				$httpReferer = self::getArrayParam($_SERVER, 'HTTP_REFERER', '');

				if ($httpReferer != '')
				{
					// parse the referer
					$refererParts = parse_url($httpReferer);

					// if the parse worked then create the currentOrigin with the relevant parts
					if ($refererParts)
					{
						$currentOrigin = $refererParts['scheme'] . '://' . $refererParts['host'];
					}
				}
			}

			if ($currentOrigin != '')
			{
				// if the calling server is in the list of allowed origins then assign the server to the allowed origins value
				// this will be added to the header and allow cross domain scripting etc...
				if (in_array($currentOrigin, $allowedOriginsArray))
				{
					$allowedOrigin = $currentOrigin;
				}
			}
		}

		return $allowedOrigin;
	}

	static function isBrandUsingHighLevelAPI($pWebBrandCode)
	{
		$isHighLevel = 0;

		$brandingArray =  DatabaseObj::getBrandingFromCode($pWebBrandCode);
		$isHighLevel = $brandingArray['usemultilinebasketworkflow'];

		return $isHighLevel;
	}
    /**
    * return the current url (ie: the url which triggered this request)
    */
    static function getCurrentURL()
    {

        $httpsOn = '';

        if (!empty($_SERVER['HTTPS']))
        {
            $httpsOn = strtolower($_SERVER['HTTPS']);
        }

        $protocol = $httpsOn == 'on' ? 'https' : 'http';

        $currentURL = $protocol . '://' . $_SERVER['HTTP_HOST'] . str_replace('/index.php', '/', $_SERVER['SCRIPT_NAME']);

        if (strlen($_SERVER['QUERY_STRING']) > 0)
        {
            $currentURL .= '?' . $_SERVER['QUERY_STRING'];
        }

        return $currentURL;
	}

    static function isHighLevelSSOReason($pReason)
    {
        $isHighLevel = false;

        switch ($pReason)
        {
            case TPX_USER_AUTH_REASON_HIGHLEVEL_CHECK_SESSION:
            case TPX_USER_AUTH_REASON_HIGHLEVEL_ONLINE_PROJECT_CREATE:
            case TPX_USER_AUTH_REASON_HIGHLEVEL_ONLINE_PROJECT_EDIT:
            case TPX_USER_AUTH_REASON_HIGHLEVEL_LOGIN:
            case TPX_USER_AUTH_REASON_HIGHLEVEL_CHECKOUT:
            {
                $isHighLevel = true;
                break;
            }
            default:
            {
                $isHighLevel = false;
                break;
            }
        }

        return $isHighLevel;
    }

    static function addURLParameter($pURL, $pKey, $pValue)
    {
        $connector = "&";

        if (strrpos($pURL, "?") === false)
        {
            $connector = "?";
        }

        return $pURL .= $connector . $pKey . "=" . $pValue;
    }


	static function getAutoUpdateFTPDetails()
	{
		global $ac_config;

		$ftpArray = array(
			'ftpurl' => UtilsObj::getArrayParam($ac_config, 'FTPAUTOUPDATEURL', ''),
			'ftpuser' => UtilsObj::getArrayParam($ac_config, 'FTPAUTOUPDATEUSER', ''),
			'ftppass' => UtilsObj::getArrayParam($ac_config, 'FTPAUTOUPDATEPASS', '')
		);

		if (($ftpArray['ftpurl'] == '') || ($ftpArray['ftpuser'] == '') || ($ftpArray['ftppass'] == ''))
		{
			$ftpArray['ftpurl'] = $ac_config['FTPURL'];
			$ftpArray['ftpuser'] = $ac_config['FTPUSER'];
			$ftpArray['ftppass'] = $ac_config['FTPPASS'];
		}

		return $ftpArray;
	}

    static function readKeyFromPOST()
    {
        return self::readKeyFromURL("POST");
    }

    static function readKeyFromGET()
    {
        return self::readKeyFromURL("GET");
    }

    static function readKeyFromPUT()
    {
        return self::readKeyFromURL("PUT");
    }

    static function readKeyFromURL($pType)
    {
        $tenantKey = '';

        switch ($pType)
        {
            case "POST":
            {
                $tenantKey = self::getPOSTParam('__k__', 0);
                break;
            }
            case "GET":
            {
                $tenantKey = self::getGETParam('__k__', 0);
                break;
            }
            case "PUT":
            {
                $tenantKey = self::getPUTParam('__k__', 0);
                break;
            }
        }

        return self::decryptData($tenantKey, TPX_DONOTSTEAL, false);
    }

    static function replaceParams($pString, $pParam1, $pEncode = false)
    {
        $text = str_replace('^0', $pParam1, $pString);

        if ($pEncode)
        {
            $text = UtilsObj::encodeString($text, true);
        }

        return $text;
    }

	static function translateUploadError($pError)
    {
        $message = '';

        switch ($pError)
        {

            case UPLOAD_ERR_INI_SIZE :
                {
                    $message = 'The uploaded file exceeds the upload_max_filesize directive';
                    break;
                }

            case UPLOAD_ERR_FORM_SIZE:
                {
                    $message = 'The uploaded file exceeds the MAX_FILE_SIZE directive';
                    break;
                }

            case UPLOAD_ERR_PARTIAL:
                {
                    $message = 'The uploaded file was only partially uploaded';
                    break;
                }

            case UPLOAD_ERR_NO_FILE:
                {
                    $message = 'No file was uploaded';
                    break;
                }

            case UPLOAD_ERR_NO_TMP_DIR:
                {
                    $message = 'Missing a temporary folder';
                    break;
                }

            case UPLOAD_ERR_CANT_WRITE:
                {
                    $message = 'Failed to write file to disk';
                    break;
                }

            default:
                {
                    $message = 'Unknown error code';
                    break;
                }
        }

        return $message;
    }

    static function parseProductURLIdentData($pProductIdentDataArray, $pURLParamArray)
    {
    	// legacy params i.e URLS generated prior to 2017r3 have the
    	// params in position 0 of the pProductIdentDataArray

    	$processingArray = explode(chr(9), $pProductIdentDataArray[0]);

    	$parsedParamArray = array();
		$parsedParamArray['collectioncode'] = $processingArray[0];
		$parsedParamArray['layoutcode'] = $processingArray[1];
		$parsedParamArray['groupcode'] = $processingArray[2];
		$parsedParamArray['groupdata']['status'] = 0;
		$parsedParamArray['groupdata']['code'] = '';
		$parsedParamArray['customparams']['status'] = 0;
		$parsedParamArray['customparams']['count'] = 0;
		$parsedParamArray['customparams']['params'] = array();
		$parsedParamArray['wizardmodeoverride']['status'] = 0;
		$parsedParamArray['wizardmodeoverride']['params']['wmo'] = -1;
		$parsedParamArray['wizardmodeoverride']['params']['wmoss'] = -1;
		$parsedParamArray['wizardmodeoverride']['params']['wmols'] = -1;
		$parsedParamArray['wizardmodeoverride']['params']['wmosd'] = -1;
        $parsedParamArray['uioverridemode'] = -1;
        $parsedParamArray['aimodeoverride'] = -1;

		$processingArray = array_slice($processingArray, 3);
		$newArrayCount = count($processingArray);

		// group data
		if ($newArrayCount > 0)
		{
			// dealing with a URL that contains group data status.
			$parsedParamArray['groupdata']['status'] = $processingArray[0];

			if ($parsedParamArray['groupdata']['status'] == 1)
			{
				if (array_key_exists('gd', $pURLParamArray))
				{
					$parsedParamArray['groupdata']['code'] = strtoupper($pURLParamArray['gd']);
				}
			}
			else if ($parsedParamArray['groupdata']['status'] == 2)
			{
				$parsedParamArray['groupdata']['code'] = strtoupper($processingArray[1]);
			}

			$processingArray = array_slice($processingArray, 2);
			$newArrayCount = count($processingArray);
		}

		// custom params
		if ($newArrayCount > 0)
		{
			$parsedParamArray['customparams']['status'] = $processingArray[0];
			$parsedParamArray['customparams']['count'] = $processingArray[1];

			$processingArray = array_slice($processingArray, 2);
			$newArrayCount = count($processingArray);

			if ($parsedParamArray['customparams']['status'] == 1)
			{
				$parsedParamArray['customparams']['count'] = 0;

				foreach ($pURLParamArray as $cpKey => $cpValue)
				{
					$isCustomParam = substr($cpKey, 0, 2);
					$subKey = substr($cpKey, 2);

					if ($isCustomParam == 'cp')
					{
						$parsedParamArray['customparams']['params'][$subKey] = $cpValue;
					}
				}
			}
			else if ($parsedParamArray['customparams']['status'] == 2)
			{
				 // read cpc
				for ($i = 0; $i < $parsedParamArray['customparams']['count']; $i++)
				{
					// loop and process custom params

					$cpText = explode('=', $processingArray[0]);

					$isCustomParam = substr($cpText[0], 0, 2);
					$subKey = substr($cpText[0], 2);

					if ($isCustomParam == 'cp')
					{
						$parsedParamArray['customparams']['params'][$subKey] = $cpText[1];
					}

					array_shift($processingArray);
				}
			}
		}

		if (count($pProductIdentDataArray) > 1)
		{
			$newURLFormatDataArray = explode(chr(10), $pProductIdentDataArray[1]);

			foreach ($newURLFormatDataArray as $paramString)
			{
				$paramPair = explode('=', $paramString, 2);
				$paramKey = $paramPair[0];
				$paramValue = $paramPair[1] ?? '';

				switch ($paramKey)
				{
                    case 'aimo':
                        $parsedParamArray['aimodeoverride'] = $paramValue;
                    break;
					case 'uio':
						$parsedParamArray['uioverridemode'] = $paramValue;
					break;
					case 'wms':
						$parsedParamArray['wizardmodeoverride']['status'] = $paramValue;
					break;
					case 'wmp':
						if ($parsedParamArray['wizardmodeoverride']['status'] == 1)
						{
							// the wmo is an appended to the url
							if (array_key_exists('wmo', $pURLParamArray))
							{
								$parsedParamArray['wizardmodeoverride']['params']['wmo'] = $pURLParamArray['wmo'];
							}

							if (array_key_exists('wmols', $pURLParamArray))
							{
								$parsedParamArray['wizardmodeoverride']['params']['wmols'] = $pURLParamArray['wmols'];
							}

							if (array_key_exists('wmoss', $pURLParamArray))
							{
								$parsedParamArray['wizardmodeoverride']['params']['wmoss'] = $pURLParamArray['wmoss'];
							}

							if (array_key_exists('wmosd', $pURLParamArray))
							{
								$parsedParamArray['wizardmodeoverride']['params']['wmosd'] = $pURLParamArray['wmosd'];
							}
						}
						else if ($parsedParamArray['wizardmodeoverride']['status'] == 2)
						{
							$wizardModeParamArray = explode('&', $paramValue);

							foreach ($wizardModeParamArray as $wizardParamString)
							{
								if ($wizardParamString != '')
								{
									$param = explode('=', $wizardParamString);
									$parsedParamArray['wizardmodeoverride']['params'][$param[0]] = $param[1];
								}
							}
						}

						if ($parsedParamArray['wizardmodeoverride']['params']['wmo'] != -1)
						{
							$parsedParamArray['wizardmodeoverride']['params']['wmols'] = $parsedParamArray['wizardmodeoverride']['params']['wmo'];
							$parsedParamArray['wizardmodeoverride']['params']['wmoss'] = $parsedParamArray['wizardmodeoverride']['params']['wmo'];
						}

					break;
				}
			}
        }

    	return $parsedParamArray;
    }

	static function getFrameHeaders()
	{
		global $gSession;
		global $ac_config;

		// set the default to deny access
		$returnArray = array('xframeoption' => 'DENY', 'frameancestor' => "'none'");

		// grab the webbrandcode from the session
		$webBrandCode = $gSession['webbrandcode'] == '' ? 'DEFAULT' : $gSession['webbrandcode'];
		// build the name of the option in the config file
		$brandSetting = 'FRAMESECURITY_' . $webBrandCode;

		if (array_key_exists($brandSetting, $ac_config))
		{
			$configSetting = $ac_config[$brandSetting];
			$multiDomainArray = explode(',', $configSetting);

			// if the licensee chooses to use multiple domains we can not support x-frame-options
			if (count($multiDomainArray) == 1)
			{
				if ($configSetting == 'ALLOW')
				{
					$returnArray['xframeoption'] = 'ALLOW';
					$returnArray['frameancestor'] = 'ALLOW';
				}
				else if (filter_var($configSetting,FILTER_VALIDATE_URL))
				{
					$returnArray['xframeoption'] = 'ALLOW-FROM ' . $configSetting;
					$returnArray['frameancestor'] = $configSetting;
				}
			}
			else
			{
				$returnArray['frameancestor'] = $multiDomainArray;
				$returnArray['xframeoption'] = 'MULTI';
			}
		}
		else if (array_key_exists('GLOBALFRAMESENABLED', $ac_config))
		{
			$globalSetting = $ac_config['GLOBALFRAMESENABLED'];

			if ($globalSetting == 'ALLOW')
			{
				$returnArray['xframeoption'] = 'ALLOW';
				$returnArray['frameancestor'] = 'ALLOW';
			}
		}

		return $returnArray;
	}

	static function readCompanionConfigFile($pGroupCode)
	{
		$companionConfig = array('headerdescription' => '', 'companions' => array());

		$companionConfigFilePath = "../config/companionproducts_" . $pGroupCode . ".txt";

		if (file_exists($companionConfigFilePath))
		{
			$textData = self::readUnicodeTextFile($companionConfigFilePath);

			$textData = str_replace("\r\n", "\n", $textData);
			$textData = str_replace("\r", "\n", $textData);
			$textData = explode("\n", $textData);
			$lineCount = count($textData);

			for ($i = 0; $i < $lineCount; $i++)
			{
				$txtArray = explode("\t", $textData[$i] . "\t");

				if ($txtArray[0] != '')
				{
					// first check to see if we are processing a companion page title
					if (substr($txtArray[0], 0, 14) == 'COMPANIONTITLE')
					{
						$companionConfig['headerdescription'] = $txtArray[1];
					}
					// check to see if we are processing a parent item
					else if (substr($txtArray[0], 0, 1) == '[')
					{
						$parentCompanionKey = substr($txtArray[0], 1, -1);

						$companionConfig['companions'][$parentCompanionKey] = array('description' => '', 'children' => array());
					}
					else if (substr($txtArray[0], 0, 20) == 'COMPANIONDESCRIPTION')
					{
						// we are processing the companion section description
						$companionConfig['companions'][$parentCompanionKey]['description'] = $txtArray[1];
					}
					else
					{
						// we are processing the companion items for the parent
						$childArray['code'] = $txtArray[0];
						$childArray['price'] = $txtArray[1];

						$companionConfig['companions'][$parentCompanionKey]['children'][] = $childArray;
					}
				}
			}
		}

		return $companionConfig;
	}

	/**
	 * Converts old pictures format to new smaller format keynames and removes no longer needed keys.
	 *
	 * @param array $pPictureData The picture data to convert.
	 * @return array
	 */
	static function convertPicturesDataToSmallerFormat($pPictureData)
	{
		$convertedArray = $pPictureData;
		$convertedArray['category'] = $pPictureData['componentcategory'];
		$convertedArray['code'] = $pPictureData['componentcode'];
		$convertedArray['name'] = $pPictureData['componentname'];
		$convertedArray['qty'] = $pPictureData['componentqty'];
		$convertedArray['tc'] = $pPictureData['totalcost'];
		$convertedArray['ts'] =  $pPictureData['totalsell'];
		$convertedArray['tt'] = $pPictureData['totaltax'];
		$convertedArray['tsnt'] = $pPictureData['totalsellnotax'];
		$convertedArray['tswt'] = $pPictureData['totalsellwithtax'];
		$convertedArray['tw'] = $pPictureData['totalweight'];
		$convertedArray['subtc'] = $pPictureData['subtotalcost'];
		$convertedArray['subts'] = $pPictureData['subtotalsell'];
		$convertedArray['subtt'] = $pPictureData['subtotaltax'];
		$convertedArray['subtsnt'] = $pPictureData['subtotalsellnotax'];
		$convertedArray['subtswt'] = $pPictureData['subtotalsellwithtax'];
		$convertedArray['subtw'] = $pPictureData['subtotalweight'];
		$convertedArray['subcategory'] = $pPictureData['subcomponentcategory'];
		$convertedArray['subcode'] = $pPictureData['subcomponentcode'];
		$convertedArray['subname'] = $pPictureData['subcomponentname'];
		$convertedArray['subskucode'] = $pPictureData['subcomponentskucode'];
		$convertedArray['subus'] = $pPictureData['subcomponentunitsell'];
		$convertedArray['subunitcost'] = $pPictureData['subcomponentunitcost'];
		$convertedArray['subunitweight'] = $pPictureData['subcomponentunitweight'];
		$convertedArray['subpricetaxcode'] = $pPictureData['subcomponentpricetaxcode'];
		$convertedArray['subpricetaxrate'] = $pPictureData['subcomponentpricetaxrate'];
		$convertedArray['asc'] = $pPictureData['assetservicecode'];
		$convertedArray['asn'] = $pPictureData['assetservicename'];
		$convertedArray['aid'] = $pPictureData['assetid'];
		$convertedArray['apt'] = $pPictureData['assetpricetype'];
		$convertedArray['ac'] = $pPictureData['assetcost'];
		$convertedArray['as'] = $pPictureData['assetsell'];

		unset($convertedArray['componentcategory']);
		unset($convertedArray['componentcode']);
		unset($convertedArray['componentname']);
		unset($convertedArray['componentqty']);
		unset($convertedArray['subcomponentcategory']);
		unset($convertedArray['subcomponentcode']);
		unset($convertedArray['subcomponentname']);
		unset($convertedArray['subcomponentskucode']);
		unset($convertedArray['subcomponentunitsell']);
		unset($convertedArray['subcomponentunitcost']);
		unset($convertedArray['subcomponentunitweight']);
		unset($convertedArray['subcomponenttotalcost']);
		unset($convertedArray['subcomponenttotalsell']);
		unset($convertedArray['subcomponenttotaltax']);
		unset($convertedArray['subcomponenttotalsellnotax']);
		unset($convertedArray['subcomponenttotalsellwithtax']);
		unset($convertedArray['subcomponenttotalweight']);
		unset($convertedArray['subcomponentpricetaxcode']);
		unset($convertedArray['subcomponentpricetaxrate']);
		unset($convertedArray['assetservicecode']);
		unset($convertedArray['assetservicename']);
		unset($convertedArray['assetid']);
		unset($convertedArray['assetpricetype']);
		unset($convertedArray['assetcost']);
		unset($convertedArray['assetsell']);
		unset($convertedArray['subtotal']);
		unset($convertedArray['totalcost']);
		unset($convertedArray['totalsell']);
		unset($convertedArray['totaltax']);
		unset($convertedArray['totalsellnotax']);
		unset($convertedArray['totalsellwithtax']);
		unset($convertedArray['totalweight']);
		unset($convertedArray['subtotalcost']);
		unset($convertedArray['subtotalsell']);
		unset($convertedArray['subtotaltax']);
		unset($convertedArray['subtotalsellnotax']);
		unset($convertedArray['subtotalsellwithtax']);
		unset($convertedArray['subtotalweight']);
		unset($convertedArray['assetservicecode']);
		unset($convertedArray['assetservicename']);
		unset($convertedArray['assetid']);
		unset($convertedArray['assetpricetype']);
		unset($convertedArray['assetcost']);
		unset($convertedArray['assetsell']);

		return $convertedArray;
	}

    static function getVersionedFileName($pName)
    {
        require_once('../libs/internal/AssetSourceMap.php');
        $fileName = '';
        if (array_key_exists($pName, AssetSourceMap::$sourceMap))
        {
            $fileName = AssetSourceMap::$sourceMap[$pName];
        }

        return $fileName;
    }

    static function checkNumberCSV($pCSVString)
    {
        // this function will make sure that the CSV which have been inputted is of type number
        $matchesCount = 0;

        $matchesCount = preg_match('/^[0-9,]+$/', $pCSVString);

        return $matchesCount == 0;
    }

    static function needSecureCookies()
    {
        global $ac_config;

        $secureCookieNeeded = false;

        // allow the configuration setting to override
        // the override is needed if you are behind a load balancer and the protocol is not being forwarded over correctly
        if (!array_key_exists('SECURECOOKIES', $ac_config))
        {
            // check that the server is on HTTPS
            if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
                isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
            {
                $secureCookieNeeded = true;
            }
        }
        else
        {
            // use the configuration override
            $secureCookieNeeded = ($ac_config['SECURECOOKIES'] == 1);
        }

        return $secureCookieNeeded;
    }

	/**
	 * Taopix BC math functions.
	 */
    static function taopix_bcdiv($pLeft, $pRight, $pScale = -1)
    {
		$returnVal = '0';

        if ($pScale != -1)
		{
			$returnVal = bcdiv(strval($pLeft), strval($pRight), $pScale);
		}
		else
		{
			$returnVal = bcdiv(strval($pLeft), strval($pRight));
		}

		return $returnVal;
    }

    static function taopix_bcmul($pLeft, $pRight, $pScale = -1)
    {
		$returnVal = '0';

        if ($pScale != -1)
		{
			$returnVal = bcmul(strval($pLeft), strval($pRight), $pScale);
		}
		else
		{
			$returnVal = bcmul(strval($pLeft), strval($pRight));
		}

		return $returnVal;
    }

    static function taopix_bcadd($pLeft, $pRight, $pScale = -1)
    {
		$returnVal = '0';

        if ($pScale != -1)
		{
			$returnVal = bcadd(strval($pLeft), strval($pRight), $pScale);
		}
		else
		{
			$returnVal = bcadd(strval($pLeft), strval($pRight));
		}

		return $returnVal;
    }

    static function taopix_bcsub($pLeft, $pRight, $pScale = -1)
    {
		$returnVal = '0';

        if ($pScale != -1)
		{
			$returnVal = bcsub(strval($pLeft), strval($pRight), $pScale);
		}
		else
		{
			$returnVal = bcsub(strval($pLeft), strval($pRight));
		}

		return $returnVal;
    }

    static function taopix_bccomp($pLeft, $pRight)
    {
        return bccomp(strval($pLeft), strval($pRight));
    }

    static function taopix_bcmod($pLeft, $pRight)
    {
        return bcmod(strval($pLeft), strval($pRight));
    }

	static  function taopix_bcceil($pNumber)
	{
		$returnVal = $pNumber;

		if (strpos($returnVal, '.') !== false) {
			if (preg_match("~\.[0]+$~", $returnVal)) {
				$returnVal = self::taopix_bcround($returnVal, 0);
			} else {
				if ($returnVal[0] != '-') {
					$returnVal = self::taopix_bcadd($returnVal, 1, 0);
				} else {
					$returnVal = self::taopix_bcsub($returnVal, 0, 0);
				}
			}
		}

		return $returnVal;
	}

	static function taopix_bcfloor($pNumber)
	{
		$returnVal = $pNumber;

		if (strpos($returnVal, '.') !== false) {
			if (preg_match("~\.[0]+$~", $returnVal)) {
				$returnVal = self::taopix_bcround($returnVal, 0);
			} else {
				if ($returnVal[0] != '-') {
					$returnVal = self::taopix_bcadd($returnVal, 0, 0);
				} else {
					$returnVal = self::taopix_bcsub($returnVal, 1, 0);
				}
			}
		}

		return $returnVal;
	}


	static function taopix_bcround($pNumber, $precision = 0)
	{
		$returnVal = $pNumber;

		if (strpos($returnVal, '.') !== false) {
			if ($returnVal[0] != '-') {
				$returnVal = self::taopix_bcadd($returnVal, '0.' . str_repeat('0', $precision) . '5', $precision);
			} else {
				$returnVal = self::taopix_bcsub($returnVal, '0.' . str_repeat('0', $precision) . '5', $precision);
			}
		}

		return $returnVal;
	}

	/**
     * Scale a number up to a large number stored as a string.
     * @param int $pValue // integer to scale up
     * @return int64 //large number
     */
    static function scaleNumberUp($pValue, $pScale = -1)
    {
        // we use a double instead of a single to hold the accuracy of the floating point number better
		$returnVal = '0';

        if ($pScale != -1)
		{
			$returnVal = UtilsObj::taopix_bcmul($pValue, '100000000000000', $pScale);
		}
		else
		{
			$returnVal = UtilsObj::taopix_bcmul($pValue, '100000000000000');
		}

		return $returnVal;
	}

	/**
     * Scale a number down from a large number stored as a string.
     * @param string $pValue // integer to scale down
     * @return int64 //large number
     */
    static function scaleNumberDown($pValue, $pScale = -1)
    {
        // we use a double instead of a single to hold the accuracy of the floating point number better
		$returnVal = '0';

        if ($pScale != -1)
		{
			$returnVal = UtilsObj::taopix_bcdiv($pValue, '100000000000000', $pScale);
		}
		else
		{
			$returnVal = UtilsObj::taopix_bcdiv($pValue, '100000000000000');
		}

		return $returnVal;
	}

    static function padConvertCoordinate($pSourceCoordinate, $pSourceCoordinateScale, $pDestCoordinateScale, $pDecimalPlaces = -1)
    {
        require_once('UtilsConstants.php');

		// Determine how many decimal places to use.
        $dp = 0;
        if ($pDecimalPlaces == -1)
        {
            $dp = ($pDestCoordinateScale == TPX_COORDINATE_SCALE_INCHES) ? 6 : 2;
		}
        else
        {
            $dp = $pDecimalPlaces;
        }

		$return = self::convertCoordinate($pSourceCoordinate, $pSourceCoordinateScale, $pDestCoordinateScale);

		$return = self::taopix_bcround($return, $dp);

        return $return;
    }

    static function convertCoordinate($pSourceCoordinate, $pSourceCoordinateScale, $pDestCoordinateScale, $pScale = -1)
    {
        require_once('UtilsConstants.php');

		$convertedValue = $pSourceCoordinate;
        $modifierValue = '25.4';

		if (($pSourceCoordinateScale == TPX_COORDINATE_SCALE_INCHES) && ($pDestCoordinateScale == TPX_COORDINATE_SCALE_MILLIMETRES))
        {
            $convertedValue = self::taopix_bcmul($pSourceCoordinate, $modifierValue, $pScale);
        }

        if (($pSourceCoordinateScale == TPX_COORDINATE_SCALE_MILLIMETRES) && ($pDestCoordinateScale == TPX_COORDINATE_SCALE_INCHES))
        {
            $convertedValue = UtilsObj::taopix_bcdiv($pSourceCoordinate, $modifierValue, $pScale);
        }

        return $convertedValue;
    }

	/**
	 * Gets the requested brand asset details.
	 *
	 * @global array $ac_config MediaAlbumWeb config values.
	 * @param int $pBrandID The ID of the brand to lookup the asset for.
	 * @param int $pObjectType The type of asset to retrieve.
	 * @return array Requested brand asset details.
	 */
	static function getBrandImage($pBrandID, $pObjectType)
	{
        global $ac_config;

        // Return the path to the brand asset file.
		$brandDataArray = DatabaseObj::getBrandingFromID($pBrandID);

		$brandingFileName = '';
		$brandingFilePath = '';
		$brandingFileDefault = '';
        $foundFile = '';
		$brandingFolderRootPath = '';

        // read the branding folder name from the configuration file
        $brandingFolderName = "Branding";
        if ($ac_config['WEBBRANDFOLDERNAME'] != '')
        {
            $brandingFolderName = $ac_config['WEBBRANDFOLDERNAME'];
        }

		$resultArray = self::getReturnArray();
		$result = '';

		switch ($pObjectType)
		{
			case TPX_BRANDING_FILE_TYPE_CC_LOGO:
			{
				$brandingFileName = 'logo_v2.png';
				$brandingFolderRootPath = '../webroot/' . $brandingFolderName;
				$brandingFilePath = 'images';
				$brandingFileDefault = '../webroot/images';
				break;
			}

			case TPX_BRANDING_FILE_TYPE_MARKETING:
			{
				$brandingFileName = 'leftsidebar.png';
				$brandingFolderRootPath = '../webroot/' . $brandingFolderName;
				$brandingFilePath = 'images';
				$brandingFileDefault = '../webroot/images';

				break;
			}

			case TPX_BRANDING_FILE_TYPE_EMAIL_LOGO:
			{
				$brandingFileName = 'logo.png';
				$brandingFolderRootPath = '../' . $brandingFolderName;
				$brandingFilePath = 'email/resources';
				$brandingFileDefault = '../Customise/email/resources';

				break;
			}
		}

        // Check Branding folder
        if ($brandDataArray['name'] != '')
        {
            $testFileName = $brandingFolderRootPath . '/' . $brandDataArray['name'] . '/' . $brandingFilePath . '/' . $brandingFileName;

            if (file_exists($testFileName))
            {
                if ($brandDataArray['displayurl'] !== '')
                {
                    $foundFile = $brandingFilePath . '/' . $brandingFileName;
                }
                else
                {
                    $foundFile = $testFileName;
                }
            }
        }

        // If not found in the branding folder, check the default.
        if ($foundFile == '')
        {
            $testFileName = $brandingFileDefault . '/' . $brandingFileName;
            if (file_exists($testFileName))
            {
                $foundFile = $testFileName;
            }
        }

		if ($foundFile != '')
		{
			// A branding file has been found.
			$foundFile = self::correctPath($foundFile, DIRECTORY_SEPARATOR, false);
			$imageData = getimagesize($foundFile);

			$assetData['id'] = 0;
			$assetData['name'] = $foundFile;
			$assetData['mime'] = $imageData['mime'];
			$assetData['width'] = $imageData[0];
			$assetData['height'] = $imageData[1];
			$assetData['path'] = $foundFile;
		}
		else
		{
			// The default file has not been found.
			$result = 'str_ErrorDefaultBrandFileNotFound';
			$assetData['id'] = -1;
			$assetData['name'] = '';
			$assetData['mime'] = '';
			$assetData['width'] = 0;
			$assetData['height'] = 0;
			$assetData['path'] = '';
		}

		$resultArray['result'] = $result;
		$resultArray['data'] = $assetData;

		return $resultArray;
	}

	static function getBrandAssetDetails($pType)
	{
		$resultArray = array();

		// Control centre logo image dimensions.
		$assetData['recommended'] = array('width' => 240, 'height' => 48);
		$assetData['maximums'] = array('width' => 400, 'height' => 80);

		$resultArray[TPX_BRANDING_FILE_TYPE_CC_LOGO] = $assetData;

		// Control centre side bar image dimensions.
		$assetData['recommended'] = array('width' => 200, 'height' => 500);
		$assetData['maximums'] = array('width' => 400, 'height' => 1000);

		$resultArray[TPX_BRANDING_FILE_TYPE_MARKETING] = $assetData;

		// Email logo image dimensions.
		$assetData['recommended'] = array('width' => 240, 'height' => 48);
		$assetData['maximums'] = array('width' => 400, 'height' => 80);

		$resultArray[TPX_BRANDING_FILE_TYPE_EMAIL_LOGO] = $assetData;

		// Online logo image dimensions.
		$assetData['recommended'] = array('width' => 240, 'height' => 48);
		$assetData['maximums'] = array('width' => 400, 'height' => 80);

		$resultArray[TPX_BRANDING_FILE_TYPE_OL_LOGO] = $assetData;
        $resultArray[TPX_BRANDING_FILE_TYPE_OL_LOGO_DARK] = $assetData;

		if ($pType == 0)
		{
			return $resultArray;
		}
		else
		{
			return $resultArray[$pType];
		}
	}

	/**
	 * Returns a value from the global variables.
	 *
	 * @param string $pGlobal name of the global we want to get
	 * @param mixed $pDefault default value to use
	 * @return mixed
	 */
	static function getGlobalValue($pGlobal, $pDefault = '')
	{
		// Check if global value we are trying to get is set, if it is return it.
		if (isset($GLOBALS[$pGlobal]))
		{
			return $GLOBALS[$pGlobal];
		}

		// We didn't have a value return the default we passed.
		return $pDefault;
    }

	/**
	 * Validate the email address using the same method as the form confirmation.
	 *
	 * @param $pEmailAddress email address to validate format.
	 * @return boolean
	 */
	static function validateEmailAddress($pEmailAddress)
	{
		// Strip any leading or trailing spaces.
		$emailAddress = trim($pEmailAddress);

		// Regex used to validate the email address.
		$reg = '/^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!\.)){0,61}[a-zA-Z0-9_-]?\.)+[a-zA-Z0-9_](?:[a-zA-Z0-9_\-](?!$)){0,61}[a-zA-Z0-9_]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/';

		// Return the result of the validation.
		return (preg_match($reg, $emailAddress) === 1);
	}

	/**
	 * Build the URL to call the ProjectThumbnail API in Online.
	 *
	 * @global $ac_config Array containing the config settings.
	 * @param string $pCommand Which command to execute on the API.
	 * @param array $pParams Array containing each parameter to add to the API call.
	 * @return string The full URL to call the ProjectThumbnailAPI.
	 */
	static function getProjectThumbnailAPIPath($pCommand, $pParams)
	{
		global $ac_config;

		$onlineURL = self::correctPath($ac_config['TAOPIXONLINEURL'], '/', true);

		// Encrypt data before sending
		$systemConfigDataArray = DatabaseObj::getSystemConfig();
		$key = UtilsObj::encryptData($systemConfigDataArray['key'], TPX_DONOTSTEAL, true);
		$data = UtilsObj::encryptData(serialize($pParams), $systemConfigDataArray['secret'], true);

		return $onlineURL . '?action=ProjectThumbnailAPI.' . $pCommand . '&__k__=' . $key . '&data=' . $data;
	}

	/**
	 * Build the details for performing a PUT request. This function should be used when requesting
	 * a large number of project refs.
	 *
	 * @global $ac_config Array containing the config settings.
	 * @param string $pCommand Which command to execute on the API.
	 * @param array $pParams Array containing each parameter to add to the API call.
	 * @return array
	 * 				'url' => Path to the ProjectThumbnailAPI call.
	 * 				'params' => Array containing the params.
	 * 					'__k__' => Key to decrypt the data.
	 * 					'data' => Encrypted and serialized data array.
	 */
	static function getProjectThumbnailAPIPutParams($pCommand, $pParams)
	{
		global $ac_config;

		$onlineURL = self::correctPath($ac_config['TAOPIXONLINEURL'], '/', true);

		// Encrypt data before sending
		$systemConfigDataArray = DatabaseObj::getSystemConfig();
		$key = UtilsObj::encryptData($systemConfigDataArray['key'], TPX_DONOTSTEAL, false);
		$data = UtilsObj::encryptData(serialize($pParams), $systemConfigDataArray['secret'], false);

		return ['url' => $onlineURL . '?action=ProjectThumbnailAPI.' . $pCommand, 'params' => ['__k__' => $key, 'data' => $data]];
	}

	/**
	 * Sends the request to get the project preview thumbnail URLs for the supplied project refs.
	 *
	 * @param array $pProjectRefList List of projectref to get the project preview thumbnail for.
	 * @return array Array containing the results of the call.
	 */
	static function requestProjectPreviewThumbnail($pProjectRefList)
	{
		require_once('../libs/internal/curl/Curl.php');

		$returnArray = UtilsObj::getReturnArray();
		$returnArray['data'] = array();

		// Request the thumbnail URLs.
		$getProjectThumbnailAPIPutParamsResult = UtilsObj::getProjectThumbnailAPIPutParams('displayThumbnail', ['projectreflist' => $pProjectRefList, 'displaymode' => 1]);
		$projectThumbnailAPIResult = CurlObj::put($getProjectThumbnailAPIPutParamsResult['url'], $getProjectThumbnailAPIPutParamsResult['params'], TPX_CURL_RETRY, TPX_CURL_TIMEOUT);

		if ($projectThumbnailAPIResult['error'] === '')
		{
			$returnArray['data'] = json_decode($projectThumbnailAPIResult['data'], true);
		}
		else
		{
			$returnArray['error'] = $projectThumbnailAPIResult['error'];
			$returnArray['errorparam'] = $projectThumbnailAPIResult['errorparam'];
		}

		return $returnArray;
	}

	/**
	 * Gets a preview path.
	 *
	 * @param string $pCode Element code.
	 * @param string $pType Element type.
	 * @param string $pWebURL (Optional) Prevent DB hit by providing weburl
	 * @return string The path to the preview if it exists.
	 */
	static function getAssetRequest($pCode, $pType, $pWebURL = '')
	{
		global $ac_config;

		$path = '';
		$uniqueID = md5($pCode);
		$folderPath = self::correctPath($ac_config['CONTROLCENTREPREVIEWSPATH'], DIRECTORY_SEPARATOR, true) . $pType . DIRECTORY_SEPARATOR . $uniqueID;
		$webURL = ($pWebURL == '') ? self::getWebURl() : $pWebURL;

		if (is_dir($folderPath)) {
			// We don't know the image extension so do a glob search.
			// The extension is set by getExtensionFromImageType so they should match what can return from there and will always be lowercase.
			$files = glob($folderPath . DIRECTORY_SEPARATOR . $uniqueID . '.{gif,jpeg,png}', GLOB_BRACE);

			// There should only be 1 image in the directory at a time, but check in case there is somehow another file in the folder with a different file extension.
			if (count($files) >= 1)
			{
				// Use the first result.
				$path = $webURL . '/previews/'. $pType . '/' . $uniqueID . '/' . basename($files[0]) . '?version=' . time();
			}
		}

		return $path;
	}

    /**
     * Get the unique portion of the desktop project thumbnail path from the ref and secret
     *
     * @param string $pProjectRef The project ref
     * @param string $pSecret The secret to use as part of the hash
     * @return string The unique portion of a desktop project thumbnails file path
     */
    static function getDesktopProjectThumbnailUniquePath($pProjectRef, $pSecret)
    {
        // Build the file path and name.
        $resourceHash = hash('md5', $pSecret . $pProjectRef);
        $resourceHashArray = str_split($resourceHash);
        // Replace certain keys with a slash to build the directory structure.
        $replacements = [0 => '/', 3 => '/', 6 => '/', 9 => '/', 12 => '/'];
        $resourcePath = implode('', array_replace($resourceHashArray, $replacements));

        //append the last part of the projectref to prevent any collisions
        $resourcePath .= substr($pProjectRef, strrpos($pProjectRef, "_")) . ".jpg";

        return $resourcePath;
    }

    /**
     * Gets the web URL for the project thumbnail of the passed desktop project ref
     *
     * @param string $pProjectRef The project ref of the project
     * @return string The URL path to the thumbnail
     */
    static function buildDesktopProjectThumbnailWebURL($pProjectRef)
    {
        global $ac_config;
        $systemConfigArray = DatabaseObj::getSystemConfig();

        return UtilsObj::correctPath($ac_config['WEBURL'], "/", true) . "desktopprojectthumbnails" . UtilsObj::getDesktopProjectThumbnailUniquePath($pProjectRef, $systemConfigArray['secret']);
    }

    /**
     * Gets the complete filesystem path to the desktop project thumbnail of the passed projectref
     *
     * @param string $pProjectRef The projectref of the project
     * @return string The filesystem path to the project thumbnail
     */
    static function getFullDesktopProjectThumbnailPath($pProjectRef)
    {
        global $ac_config;
        $systemConfigArray = DatabaseObj::getSystemConfig();

        return $ac_config['CONTROLCENTREDESKTOPPROJECTTHUMBNAILSPATH'] . '/' . UtilsObj::getDesktopProjectThumbnailUniquePath($pProjectRef, $systemConfigArray['secret']);
    }

    /**
     * Deletes passed desktop project thumnbnails and removes their records from the database
     *
     * @param array $pProjectRefArray Array of projectrefs in string form to delete thumbnails for
     * @return array Standard error array
     */
    static function deleteDesktopProjectThumbnails($pProjectRefArray)
    {
        $resultArray = UtilsObj::getReturnArray();
        $succesfulDeletionProjectRefArray = Array();
        $failedDeletionProjectRefArray = Array();
        $projectRefCount = count($pProjectRefArray);
        $error = '';
        $errorParam = '';

        //loop around the project refs we have been passed and attempt to delete them
        for ($i = 0; $i < $projectRefCount; $i++)
        {
            $theProjectRef = $pProjectRefArray[$i];
            $theFileName = self::getFullDesktopProjectThumbnailPath($theProjectRef);

            if (file_exists($theFileName))
            {
                $unlinkResult = unlink($theFileName);

                if ($unlinkResult === true)
                {
                    $succesfulDeletionProjectRefArray[] = $theProjectRef;
                }
                else
                {
                    //we have failed to delete the file
                    $failedDeletionProjectRefArray[] = $theProjectRef;
                }
            }
            else
            {
                //file already doesn't exist
                //mark it as succesful so it is removed from the database
                $succesfulDeletionProjectRefArray[] = $theProjectRef;
            }
        }

        //delete the database records for any deleted thumbnails
        if (count($succesfulDeletionProjectRefArray))
        {
            $recordDeletionResultArray = DatabaseObj::deleteDesktopProjectThumbnailRecordsByProjectRef($succesfulDeletionProjectRefArray);

            if ($recordDeletionResultArray['error'] !== '')
            {
                $error = $recordDeletionResultArray['error'];
                $errorParam = $recordDeletionResultArray['errorparam'];
            }
        }

        //mark any failed deletes as unavailable as we cannot be sure of its status
        if (($error === '') && (count($failedDeletionProjectRefArray)))
        {
            //ignore any returned errors from the mark as unavailable as we already have an error
            DatabaseObj::markDesktopProjectThumbnailsUnavailable($failedDeletionProjectRefArray);

            $error = 1;
            $errorParam = 'Failed to delete desktop project thumbnails';
        }

        $resultArray['error'] = $error;
        $resultArray['errorparam'] = $errorParam;

        return $resultArray;
    }
	/**
	 * Determine if the Content Security Policy is active.
	 *
	 * @global array $ac_config Config settings array.
	 * @return boolean True if CSP is active, false if not.
	 */
	static function getCSPActive()
	{
		global $ac_config;

		$cspActive = true;

		if ((array_key_exists('CONTENTSECURITYPOLICY', $ac_config)) && ($ac_config['CONTENTSECURITYPOLICY'] === 'DISABLED'))
		{
			$cspActive = false;
		}

		return $cspActive;
	}

    /**
     * Returns the path for the folder containing the product collection resources for a specific version date
     *
     * @param string $pCollectionCode The collection code for the resources
     * @param string $pCollectionVersionDate The version date for the resources
     * @global array $ac_config Config settings array
     * @return string The path to the folder containing the resources
     */
    static function getProductCollectionResourceFolderPath($pCollectionCode, $pCollectionVersionDate)
    {
        global $ac_config;

        $datePath = date("YmdHis", strtotime($pCollectionVersionDate));
        $collectionResourcePath = UtilsObj::correctPath($ac_config['PRODUCTCOLLECTIONRESOURCESPATH'], DIRECTORY_SEPARATOR, true) . $pCollectionCode . DIRECTORY_SEPARATOR . $datePath;

        return $collectionResourcePath;
    }

    /*
     * Return a file extension compare to the image type.
     *
     * @param $pImageMimeType File mime type.
     * @return File extension.
     */
    static function getExtensionFromImageType($pImageMimeType)
    {
        $validImageTypes = [
            'image/jpeg' => '.jpeg',
            'image/pjpeg' => '.jpeg',
            'image/gif' => '.gif',
            'image/png' => '.png',
            'image/x-png' => '.png'
        ];

        return $validImageTypes[$pImageMimeType];
    }

    /*
    * Write data to a given file in the logs directory.
    *
    * @param $pFileName name of the file to write to.
    * @param $pError the error string or an error array to log.
    * @param $pExtraData any extra data to be logged against the error.
    */
    static function writeToDebugFileInLogsFolder($pFileName, $pError = '', $pExtraData = array())
    {
        $serverTime = DatabaseObj::getServerTime();

        $path = __FILE__;
        $dir = dirname($path);

        $path = $dir . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'logs';
        $path = realpath($path);
        $path .= DIRECTORY_SEPARATOR . $pFileName;

        $fp = fopen($path, 'a');

        if ($fp)
        {
            fwrite($fp, 'DateTime: ' . $serverTime . "\n");

            if(is_array($pError))
            {
                    fwrite($fp, 'Error: ' . print_r($pError, TRUE));
            }
            else
            {
                if ($pError != '')
                {
                    fwrite($fp, 'Error: ' . $pError . "\n");
                }
            }
            fwrite($fp, 'Extra datas::' . print_r($pExtraData,TRUE));
            fwrite($fp, "---------------------------------------------------\n");
            fclose($fp);
        }
    }

    /**
	 * Converts an numeric array to a associative array based on a map file.
	 *
	 * @param array $pItem Item we want to convert.
	 * @param array $pMap Map we are using for the conversion.
	 * @return Array of converted items.
	 */
	static function convertFromNumericToAssociativeUsingMap($pItem, $pMap)
	{
		$associativeKeyedItem = array();

		foreach($pMap as $key => $value)
		{
			// If the $key has a '.' then it is part of an array.
			if (strpos($key, '.') === false)
			{
				if (isset($pItem[$value[TPX_DATAMAP_POSITION]]))
				{
					// If the page item has the property, restore it.
					$associativeKeyedItem[$key] = $pItem[$value[TPX_DATAMAP_POSITION]];
				}
				else
				{
					// Restore from the default.
					$associativeKeyedItem[$key] = $value[TPX_DATAMAP_DEFAULT_VALUE];
				}
			}
			else
			{
				$keyParts = explode('.', $key);
				$primaryKey = $keyParts[0];
				$subKey = $keyParts[1];
				$subMapKeys = $pMap[$key][TPX_DATAMAP_POSITION];

				if (! array_key_exists($primaryKey, $associativeKeyedItem))
				{
					$associativeKeyedItem[$primaryKey] = array();
				}

				if ((array_key_exists($subMapKeys[0], $pItem)) && (array_key_exists($subMapKeys[1], $pItem[$subMapKeys[0]])))
				{
					// If the page item has the property, restore it.
					$associativeKeyedItem[$primaryKey][$subKey] = $pItem[$subMapKeys[0]][$subMapKeys[1]];
				}
				else
				{
					// Restore from the default.
					$associativeKeyedItem[$primaryKey][$subKey] = $value[TPX_DATAMAP_DEFAULT_VALUE];
				}
			}
		}

		return $associativeKeyedItem;
	}

    /**
	 * Convert associative array to indexed array to shrink data size
	 *
	 * @param array pArray associative array to convert
	 * @param bool pTop optional if true then we are at the top level
	 *
	 * @return array indexed array to return
	 */
	static function reMapKeys($pArray, $pTop = false)
	{
		$passArray = $pArray;

		if ((!$pTop) && (is_array($pArray)))
		{
			$passArray = array_values($pArray);
		}

		if (is_array($passArray))
		{
			return array_map('UtilsObj::remapKeys', $passArray);
		}
		else
		{
			return $passArray;
		}
	}

    static function getPromoPanelFilePath($pGroupCode)
    {
        return self::getPromoPanelFolderPath($pGroupCode) . DIRECTORY_SEPARATOR . $pGroupCode . ".org";
    }

    static function getPromoPanelFolderPath($pGroupCode)
    {
        global $ac_config;

        return $ac_config['CONTROLCENTREPROMOPANELSPATH'] . DIRECTORY_SEPARATOR . $pGroupCode;
    }

    static function getPromoPanelMetadataPath($pGroupCode)
    {
        return self::getPromoPanelFolderPath($pGroupCode) . DIRECTORY_SEPARATOR . $pGroupCode . ".ppd";
    }

    static function getPromoPanelOrgURL($pGroupCode)
    {
        global $ac_config;

        return UtilsObj::correctPath($ac_config['WEBURL'], "/", true) . "promopanels" . "/" . $pGroupCode . "/" . $pGroupCode . ".org";
    }
}
?>
