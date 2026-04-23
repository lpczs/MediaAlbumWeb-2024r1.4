<?php

class CurlObj
{
	private static $curl_error_codes = array(
		0 => '???',
		1 => 'CURLE_UNSUPPORTED_PROTOCOL',
		2 => 'CURLE_FAILED_INIT',
		3 => 'CURLE_URL_MALFORMAT',
		4 => 'CURLE_URL_MALFORMAT_USER',
		5 => 'CURLE_COULDNT_RESOLVE_PROXY',
		6 => 'CURLE_COULDNT_RESOLVE_HOST',
		7 => 'CURLE_COULDNT_CONNECT',
		8 => 'CURLE_FTP_WEIRD_SERVER_REPLY',
		9 => 'CURLE_REMOTE_ACCESS_DENIED',
		11 => 'CURLE_FTP_WEIRD_PASS_REPLY',
		13 => 'CURLE_FTP_WEIRD_PASV_REPLY',
		14 => 'CURLE_FTP_WEIRD_227_FORMAT',
		15 => 'CURLE_FTP_CANT_GET_HOST',
		17 => 'CURLE_FTP_COULDNT_SET_TYPE',
		18 => 'CURLE_PARTIAL_FILE',
		19 => 'CURLE_FTP_COULDNT_RETR_FILE',
		21 => 'CURLE_QUOTE_ERROR',
		22 => 'CURLE_HTTP_RETURNED_ERROR',
		23 => 'CURLE_WRITE_ERROR',
		25 => 'CURLE_UPLOAD_FAILED',
		26 => 'CURLE_READ_ERROR',
		27 => 'CURLE_OUT_OF_MEMORY',
		28 => 'CURLE_OPERATION_TIMEDOUT',
		30 => 'CURLE_FTP_PORT_FAILED',
		31 => 'CURLE_FTP_COULDNT_USE_REST',
		33 => 'CURLE_RANGE_ERROR',
		34 => 'CURLE_HTTP_POST_ERROR',
		35 => 'CURLE_SSL_CONNECT_ERROR',
		36 => 'CURLE_BAD_DOWNLOAD_RESUME',
		37 => 'CURLE_FILE_COULDNT_READ_FILE',
		38 => 'CURLE_LDAP_CANNOT_BIND',
		39 => 'CURLE_LDAP_SEARCH_FAILED',
		41 => 'CURLE_FUNCTION_NOT_FOUND',
		42 => 'CURLE_ABORTED_BY_CALLBACK',
		43 => 'CURLE_BAD_FUNCTION_ARGUMENT',
		45 => 'CURLE_INTERFACE_FAILED',
		47 => 'CURLE_TOO_MANY_REDIRECTS',
		48 => 'CURLE_UNKNOWN_TELNET_OPTION',
		49 => 'CURLE_TELNET_OPTION_SYNTAX',
		51 => 'CURLE_PEER_FAILED_VERIFICATION',
		52 => 'CURLE_GOT_NOTHING',
		53 => 'CURLE_SSL_ENGINE_NOTFOUND',
		54 => 'CURLE_SSL_ENGINE_SETFAILED',
		55 => 'CURLE_SEND_ERROR',
		56 => 'CURLE_RECV_ERROR',
		58 => 'CURLE_SSL_CERTPROBLEM',
		59 => 'CURLE_SSL_CIPHER',
		60 => 'CURLE_SSL_CACERT',
		61 => 'CURLE_BAD_CONTENT_ENCODING',
		62 => 'CURLE_LDAP_INVALID_URL',
		63 => 'CURLE_FILESIZE_EXCEEDED',
		64 => 'CURLE_USE_SSL_FAILED',
		65 => 'CURLE_SEND_FAIL_REWIND',
		66 => 'CURLE_SSL_ENGINE_INITFAILED',
		67 => 'CURLE_LOGIN_DENIED',
		68 => 'CURLE_TFTP_NOTFOUND',
		69 => 'CURLE_TFTP_PERM',
		70 => 'CURLE_REMOTE_DISK_FULL',
		71 => 'CURLE_TFTP_ILLEGAL',
		72 => 'CURLE_TFTP_UNKNOWNID',
		73 => 'CURLE_REMOTE_FILE_EXISTS',
		74 => 'CURLE_TFTP_NOSUCHUSER',
		75 => 'CURLE_CONV_FAILED',
		76 => 'CURLE_CONV_REQD',
		77 => 'CURLE_SSL_CACERT_BADFILE',
		78 => 'CURLE_REMOTE_FILE_NOT_FOUND',
		79 => 'CURLE_SSH',
		80 => 'CURLE_SSL_SHUTDOWN_FAILED',
		81 => 'CURLE_AGAIN',
		82 => 'CURLE_SSL_CRL_BADFILE',
		83 => 'CURLE_SSL_ISSUER_ERROR',
		84 => 'CURLE_FTP_PRET_FAILED',
		84 => 'CURLE_FTP_PRET_FAILED',
		85 => 'CURLE_RTSP_CSEQ_ERROR',
		86 => 'CURLE_RTSP_SESSION_ERROR',
		87 => 'CURLE_FTP_BAD_FILE_LIST',
		88 => 'CURLE_CHUNK_FAILED');

	static function postFile($pServer, $pFields, $pRetries, $pTimeouts)
	{
		global $ac_config;

		$returnData = UtilsObj::getReturnArray();

		$retry = true;
		$retryCount = 1;

		while ($retry)
		{
			// increase the standard php timeout
			$incrementTime = $pTimeouts + 10;
            UtilsObj::resetPHPScriptTimeout($incrementTime);

			$resource = curl_init();
			curl_setopt($resource, CURLOPT_URL, $pServer );
			curl_setopt($resource, CURLOPT_HEADER, false);
			curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);

			curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, $ac_config['SSLVERIFYPEER']);

			// set CURLOPT_SSL_VERIFYHOST to 0 if verify peer is off
			// we can set CURLOPT_SSL_VERIFYHOST to 1 since this isn't the default value so we don't set it 
			// and allow the default value to be set
			if ($ac_config['SSLVERIFYPEER'] == 0)
			{
				curl_setopt($resource, CURLOPT_SSL_VERIFYHOST, 0);
			}

			curl_setopt($resource, CURLOPT_CAINFO, UtilsObj::getCurlPEMFilePath());

			if (!empty($pFields))
			{
				curl_setopt($resource, CURLOPT_POST, true);
				curl_setopt($resource, CURLOPT_POSTFIELDS, $pFields);
			}

			curl_setopt($resource, CURLOPT_TIMEOUT, $pTimeouts);

			$result = curl_exec($resource);

			$httpCode = curl_getinfo($resource, CURLINFO_HTTP_CODE);
			$errorNumber = curl_errno($resource);

			if ( ($errorNumber!=0) || ( ($httpCode!=200) && ($httpCode!=202) && ($httpCode != 201)) )
			{
				$retry = $retryCount < $pRetries;

		    	if ($retry)
		    	{
		    		$retryCount++;
		    	}
			}
			else
			{
				$retry = false;
			}

			curl_close($resource);
		}

		if ($retryCount != $pRetries)
		{

			$returnData['data'] = $result;
		}
		else
		{
			$returnData['error'] = $errorNumber;
			$returnData['errorparam'] = '[' . $httpCode . '] - ' . self::$curl_error_codes[$errorNumber];
		}

		return $returnData;
	}

	static function post($pServer, $pFields, $pRetries, $pTimeouts, $pFileParam = array())
	{
		global $ac_config;

		$returnData = UtilsObj::getReturnArray();

		$retry = true;
		$retryCount = 1;

		while ($retry)
		{
			// increase the standard php timeout
			$incrementTime = $pTimeouts + 10;
            UtilsObj::resetPHPScriptTimeout($incrementTime);

			$resource = curl_init();
			curl_setopt($resource, CURLOPT_URL, $pServer );
			curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, $ac_config['SSLVERIFYPEER']);

			// set CURLOPT_SSL_VERIFYHOST to 0 if verify peer is off
			// we can set CURLOPT_SSL_VERIFYHOST to 1 since this isn't the default value so we don't set it 
			// and allow the default value to be set
			if ($ac_config['SSLVERIFYPEER'] == 0)
			{
				curl_setopt($resource, CURLOPT_SSL_VERIFYHOST, 0);
			}

			curl_setopt($resource, CURLOPT_CAINFO, UtilsObj::getCurlPEMFilePath());

			if (! empty($pFields))
			{
				curl_setopt($resource, CURLOPT_POST, true);

				if (count($pFileParam) > 0)
				{
					$pFields[$pFileParam['name']] = $pFileParam['value'];

					curl_setopt($resource, CURLOPT_POSTFIELDS, $pFields);
				}
				else
				{
					curl_setopt($resource, CURLOPT_POSTFIELDS, http_build_query($pFields));
				}
			}

			curl_setopt($resource, CURLOPT_TIMEOUT, $pTimeouts);

			$result = curl_exec($resource);

			$httpCode = curl_getinfo($resource, CURLINFO_HTTP_CODE);
			$errorNumber = curl_errno($resource);

			if (($errorNumber!=0) || (($httpCode!=200) && ($httpCode!=202)))
			{
				$retry = $retryCount < $pRetries;

		    	if ($retry)
		    	{
		    		$retryCount++;
		    	}
			}
			else
			{
				$retry = false;
			}

			curl_close($resource);
		}

		if ($retryCount!=$pRetries)
		{
			$returnData['data'] = $result;
		}
		else
		{
			$returnData['error'] = $errorNumber;
			$returnData['errorparam'] = '[' . $httpCode . '] - ' . self::$curl_error_codes[$errorNumber];

			error_log("CURL ERROR: " . "\nTime: " . date('H:i:s d/m/Y', time()) . "\nServer: " . $pServer . " \nError number: " . $errorNumber . " (". $returnData['errorparam'] . ")");
		}

		return $returnData;
	}


	static function put($pURL, $pFields, $pRetries, $pTimeouts)
	{
		global $ac_config;

		$returnData = UtilsObj::getReturnArray();

		$retry = true;
		$retryCount = 1;

		$pCompressedData = UtilsObj::compressArray($pFields);

		$dataLength = strlen($pCompressedData);

		$memoryHandle = fopen('php://temp/maxmemory:256000', 'w');

		if ($memoryHandle)
		{
			while ($retry)
			{
				// increase the standard php timeout
				$incrementTime = $pTimeouts + 10;
            	UtilsObj::resetPHPScriptTimeout($incrementTime);

				fwrite($memoryHandle, $pCompressedData);
				fseek($memoryHandle, 0);

				$resource = curl_init();

				// Binary transfer i.e. --data-BINARY
				curl_setopt($resource, CURLOPT_BINARYTRANSFER, true);
				curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, $ac_config['SSLVERIFYPEER']);

				// set CURLOPT_SSL_VERIFYHOST to 0 if verify peer is off
				// we can set CURLOPT_SSL_VERIFYHOST to 1 since this isn't the default value so we don't set it 
				// and allow the default value to be set
				if ($ac_config['SSLVERIFYPEER'] == 0)
				{
					curl_setopt($resource, CURLOPT_SSL_VERIFYHOST, 0);
				}

				curl_setopt($resource, CURLOPT_CAINFO, UtilsObj::getCurlPEMFilePath());

				curl_setopt($resource, CURLOPT_URL, $pURL);
				// Using a PUT method i.e. -XPUT
				curl_setopt($resource, CURLOPT_PUT, true);
				// Instead of POST fields use these settings
				curl_setopt($resource, CURLOPT_INFILE, $memoryHandle);
				curl_setopt($resource, CURLOPT_INFILESIZE, $dataLength);

				curl_setopt($resource, CURLOPT_TIMEOUT, $pTimeouts);

				$result = curl_exec($resource);

				$httpCode = curl_getinfo($resource, CURLINFO_HTTP_CODE);
				$errorNumber = curl_errno($resource);

				if (($errorNumber!=0) || (($httpCode!=200) && ($httpCode!=202)))
				{
					$retry = $retryCount < $pRetries;

			    	if ($retry)
			    	{
			    		$retryCount++;
			    	}
				}
				else
				{
					$retry = false;
				}

				curl_close($resource);
			}

			if ($retryCount != $pRetries)
			{
				$returnData['data'] = $result;
			}
			else
			{
				$returnData['error'] = $errorNumber;
				$returnData['errorparam'] = '[' . $httpCode . '] - ' . self::$curl_error_codes[$errorNumber];
			}
		}
		else
		{
			$returnData['error'] = 0;
			$returnData['errorparam'] = 'Unable to get handle to memory.';
		}

		return $returnData;
	}

	static function putFile($pURL, $pFilePath, $pRetries, $pTimeouts, $pHeaders)
	{
		global $ac_config;

		$returnData = UtilsObj::getReturnArray();

		$retry = true;
		$retryCount = 1;
		$fileSize = filesize($pFilePath);
		$fileHandle = fopen($pFilePath, 'r');

		if ($fileHandle)
		{
			while ($retry)
			{
				// increase the standard php timeout
				$incrementTime = $pTimeouts + 10;
            	UtilsObj::resetPHPScriptTimeout($incrementTime);

				$resource = curl_init();

				// Binary transfer i.e. --data-BINARY
				curl_setopt($resource, CURLOPT_BINARYTRANSFER, true);
				curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, $ac_config['SSLVERIFYPEER']);


				// set CURLOPT_SSL_VERIFYHOST to 0 if verify peer is off
				// we can set CURLOPT_SSL_VERIFYHOST to 1 since this isn't the default value so we don't set it 
				// and allow the default value to be set
				if ($ac_config['SSLVERIFYPEER'] == 0)
				{
					curl_setopt($resource, CURLOPT_SSL_VERIFYHOST, 0);
				}
				
				curl_setopt($resource, CURLOPT_CAINFO, UtilsObj::getCurlPEMFilePath());

				curl_setopt($resource, CURLOPT_URL, $pURL);
				// Using a PUT method i.e. -XPUT
				curl_setopt($resource, CURLOPT_PUT, true);
				// Instead of POST fields use these settings
				curl_setopt($resource, CURLOPT_INFILE, $fileHandle);
				curl_setopt($resource, CURLOPT_INFILESIZE, $fileSize);

				curl_setopt($resource, CURLOPT_TIMEOUT, $pTimeouts);

				if (count($pHeaders) > 0)
				{
					curl_setopt($resource, CURLOPT_HTTPHEADER, $pHeaders);
				}

				$result = curl_exec($resource);

				$httpCode = curl_getinfo($resource, CURLINFO_HTTP_CODE);
				$errorNumber = curl_errno($resource);

				if ( ($errorNumber!=0) || ( ($httpCode!=200) && ($httpCode!=202)) )
				{
					$retry = $retryCount < $pRetries;

			    	if ($retry)
			    	{
			    		$retryCount++;
			    	}
				}
				else
				{
					$retry = false;
				}

				curl_close($resource);
			}

			if ($retryCount!=$pRetries)
			{
				$returnData['data'] = $result;
			}
			else
			{
				$returnData['error'] = $errorNumber;
				$returnData['errorparam'] = '[' . $httpCode . '] - ' . self::$curl_error_codes[$errorNumber];
			}

			fclose($fileHandle);
		}
		else
		{
			$returnData['error'] = 0;
			$returnData['errorparam'] = 'Unable to get handle to memory.';
		}

		return $returnData;
	}

	static function ftpDelete($pOrderRoot, $pPaths, $pFiles, $pRetries, $pTimeouts)
	{
		global $ac_config;

		$returnData = UtilsObj::getReturnArray();

		$retry = true;
		$retryCount = $pRetries;
		$result = array();
		$returnData['error'] = 0;

		while ($retry)
		{
			// increase the standard php timeout
			$incrementTime = $pTimeouts + 30;
			UtilsObj::resetPHPScriptTimeout($incrementTime);

			$url = 'ftp://' . $ac_config['FTPURL'];
			$ftpCreds = $ac_config['FTPUSER'] . ':' . $ac_config['FTPPASS'];

			// each path part has the directory separator appended
			$filePath = implode('', $pPaths);
			$pathDepth = count($pPaths);

			$fileList = array();
			foreach ($pFiles as $theFile)
			{
				$fileList[] = $pOrderRoot . $filePath . $theFile;
			}

			// create paths to be removed
			$dirList = array();
			for ($fp = ($pathDepth - 1), $dl = 0; $fp >= 0; $fp--, $dl++)
			{
				if ($dl == 0)
				{
					$dirList[$fp] = $pOrderRoot . $pPaths[$dl];
				}
				else
				{
					$dirList[$fp] = $dirList[($fp + 1)] . $pPaths[$dl];
				}
			}

			// build a list of commands to exectute via ftp
			$cmdArray = array();

			$performDeleteAction = false;

			// check if the files exist on the ftp server, if so, delete them
			for ($fe = 0; $fe < 2; $fe++)
			{
				// check if files exist
				$urlFile = $url . '/' . $fileList[$fe];

				$resource = curl_init();
				curl_setopt($resource, CURLOPT_NOBODY, true);
				curl_setopt($resource, CURLOPT_URL, $urlFile);
				curl_setopt($resource, CURLOPT_RETURNTRANSFER, false);
				curl_setopt($resource, CURLOPT_USERPWD, $ftpCreds);

				$result = curl_exec($resource);
				$httpCode = curl_getinfo($resource, CURLINFO_HTTP_CODE);
				$errorNumber = curl_errno($resource);

				curl_close($resource);

				// does the file exist? a result of
				// 350 = file exists
				// 550 = no file
				if ($httpCode == 350)
				{
					// the file exists, delete it
					$performDeleteAction = true;
					$cmdArray[] = 'DELE /' . $fileList[$fe];
				}
			}

			// add the remove directories commands
			if ($performDeleteAction)
			{
				for ($dl = 0; $dl < count($dirList); $dl++)
				{
					$cmdArray[] = 'RMD /' . $dirList[$dl];
				}
			}

			// reset the error number
			$errorNumber = 0;

			// execute each of the delete commands
			foreach ($cmdArray as $cmd)
			{
				$resource = curl_init();
				curl_setopt($resource, CURLOPT_URL, $url);
				curl_setopt($resource, CURLOPT_USERPWD, $ftpCreds);
				curl_setopt($resource, CURLOPT_RETURNTRANSFER, false);
				curl_setopt($resource, CURLOPT_QUOTE, array($cmd));

				$result = curl_exec($resource);
				$httpCode = curl_getinfo($resource, CURLINFO_HTTP_CODE);
				$errorNumber = curl_errno($resource);

				curl_close($resource);

				// check the httpCode for a successful delete operation (226)
				if ($httpCode == 226)
				{
					// delete or remove was successful
					$errorNumber = 0;
				}
			}


			if ($result['result'] != 2)
			{
				$retry = $retryCount < $pRetries;

				if ($retry)
				{
					$retryCount++;
				}

			}
			else
			{
				$retry = false;
			}
		}


		if ($retryCount != $pRetries)
		{
			$returnData['data'] = $result;
		}
		else
		{
			$returnData['error'] = $errorNumber;
			$returnData['errorparam'] = '[' . $errorNumber . '] - ' . self::$curl_error_codes[$errorNumber];
		}

		return $returnData;
	}


	static function listDirectoryContent($pOrderRoot, $pPaths, $pUseFTPS, $pRetries, $pTimeouts)
	{
		global $ac_config;

		$returnData = UtilsObj::getReturnArray();
		$returnData['error'] = 0;
		$returnList = array();

		// increase the standard php timeout
		$incrementTime = $pTimeouts + 30;
		UtilsObj::resetPHPScriptTimeout($incrementTime);

		if ($pUseFTPS)
		{
			$url = 'ftps://' . $ac_config['FTPURL'];
		}
		else
		{
			$url = 'ftp://' . $ac_config['FTPURL'];
		}
		$ftpCreds = $ac_config['FTPUSER'] . ':' . $ac_config['FTPPASS'];

		// each path part has the directory separator appended
		$filePath = implode('/', $pPaths) . '/';
		
		$deletePath = '/' . $pOrderRoot . $filePath;
		$listPath = $url . $deletePath;

		// get directory list
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $listPath);
		curl_setopt($curl, CURLOPT_USERPWD, $ftpCreds);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1) ;
		
		// command list files in a specified directory
		// use LIST -a (not NLST) to get attributes, including directory attribute
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'LIST -a');

		if ($pUseFTPS)
		{
			curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_FTPS);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($curl, CURLOPT_FTP_SSL, CURLFTPSSL_ALL);
			curl_setopt($curl, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_SSL);
			curl_setopt($curl, CURLOPT_CAINFO, UtilsObj::getCurlPEMFilePath());
			curl_setopt($curl, CURLOPT_SSLVERSION, 0);
			
		}
		else
		{
			curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_FTP);
		}

		$dirRaw = curl_exec($curl);

		curl_close ($curl);

		// substitute any "\r" for "\n"
		$dirRaw = str_replace("\r", "\n", $dirRaw);

		// split the directory listing into an array, exploding on "\n"
		$dirArray = explode("\n", $dirRaw);

		// filter directory listing array, removing any empty lines
		// array_filter iterates over each value in $dirArray, passing it to 'strlen',
		// if 'strlen' returns a value, the line is added to $dirContent, otherwise ignored
		$dirContent = array_filter($dirArray, 'strlen');

		foreach ($dirContent as $fileData)
		{
			// determine is the listing is a file or directory, based on first character being 'd' or '-'
			$fileType = 'ignore';

			// assumed line format
			// -rw-r--r--    1 501        20              89997 Mar  9 14:34 Order_TRUNK-CORE090320171430000507_c.eop
			// splitting the string on space may not be correct, as file names may contain spaces and an unknown number of spaces will be returned,
			// so split off first part of the string and trim result, expect 9 elements, first being attributes, last being file name
			$testStr = $fileData;
			$lineElements = array();
			for ($i = 0; $i < 8; $i++)
			{
				// make sure the string has content to test
				if ($testStr != '')
				{
					$lineParts = explode(" ", $testStr, 2);
					$lineElements[] = $lineParts[0];

					// make sure the string has been split into 2 parts
					if (count($lineParts) == 2)
					{
						// set the string for the next test
						$testStr = trim($lineParts[1]);
					}
					else
					{
						// set the string to empty, preventing more tests
						$testStr = '';
					}
				}
			}

			// if 9 elements were returned from ftp list, testStr will contain data, assume it is the file name
			if ($testStr != '')
			{
				$lineElements[] = $testStr;
			}
			$lastElement = end($lineElements);

			

			// determine if file or directory only if not '.' or '..' and only if 9 elements were returned from ftp list
			if ((count($lineElements) == 9) && (($lastElement != '.') && ($lastElement != '..')))
			{
				$fc = substr($lineElements[0], 0, 1);
				if ($fc == 'd')
				{
					$fileType = 'dir';
				}
				else if ($fc == '-')
				{
					$fileType = 'file';
				}
			}

			// create an action based upon the type of file
			if ($fileType == 'dir')
			{
				$returnList[] = array('path' => $deletePath, 'name' => $lastElement, 'cmd' => 'RMD');
				$subPath = $pPaths;
				$subPath[] = trim($lastElement);

				$subDir = self::listDirectoryContent($pOrderRoot, $subPath, $pUseFTPS, $pRetries, $pTimeouts);
				
				// add each file disovered to the front of the list of files to be removed
				foreach ($subDir as $theDir)
				{
					array_unshift($returnList, $theDir);
				}
			}
			else if ($fileType == 'file')
			{
				// add to the list of files to be removed
				$returnList[] = array('path' => $deletePath, 'name' => $lastElement, 'cmd' => 'DELE');
			}
		}

		return $returnList;
	}


	static function ftpDeleteRecursive($pOrderRoot, $pPaths, $pRetries, $pTimeouts)
	{
		// get content of directory
		global $ac_config;
		$returnData = array('error' => 0, 'errorparam' => '');

		// try and connect using SSL
		$useFTPS = true;
		$url = 'ftps://' . $ac_config['FTPURL'];
		$ftpCreds = $ac_config['FTPUSER'] . ':' . $ac_config['FTPPASS'];

		// try and connect using ftps
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERPWD, $ftpCreds);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1) ;
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'LIST -a');
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_FTP_SSL, CURLFTPSSL_ALL);
		curl_setopt($curl, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_SSL);
        curl_setopt($curl, CURLOPT_CAINFO, UtilsObj::getCurlPEMFilePath());
        curl_setopt($curl, CURLOPT_SSLVERSION, 0);

		$result = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$errorNumber = curl_errno($curl);

		curl_close($curl);

		// no ftps support, fall back to ftp
		if (($errorNumber != 0))
		{
			// connection failed
			// try and connect without SSL
			$useFTPS = false;
			$url = 'ftp://' . $ac_config['FTPURL'];
			$ftpCreds = $ac_config['FTPUSER'] . ':' . $ac_config['FTPPASS'];

			// get directory list
			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_USERPWD, $ftpCreds);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1) ;
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'LIST -a');

			$result = curl_exec($curl);

			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$errorNumber = curl_errno($curl);
			
			curl_close($curl);
		}


		// get a list of files in the upload directory for the order
		$deleteList = self::listDirectoryContent($pOrderRoot, $pPaths, $useFTPS, $pRetries, $pTimeouts);

		$cmdArray = array();
	
		if (count($deleteList) > 0)
		{
			// create a list of commands to delete the files, followed by the (empty) directories
			foreach ($deleteList as $fileName)
			{
				$cmdStr = $fileName['cmd'] . ' ' . $fileName['path'] . $fileName['name'];

				if ($fileName['cmd'] == 'RMD')
				{
					$cmdArray[] = $cmdStr;
				}
				else
				{
					array_unshift($cmdArray, $cmdStr);
				}
	
			}
			// send the curl to remove the files and directories
			$returnData = UtilsObj::getReturnArray();
			$returnData['error'] = 0;

			if (count($cmdArray) > 0)
			{
				// files have been found, delete them
				$retry = true;
				$retryCount = $pRetries;
				$result = array();
				$errorNumber = 0;

				// increase the standard php timeout
				$incrementTime = $pTimeouts + 30;
				UtilsObj::resetPHPScriptTimeout($incrementTime);

				while ($retry)
				{
					$resource = curl_init();
					curl_setopt($resource, CURLOPT_URL, $url);
					curl_setopt($resource, CURLOPT_USERPWD, $ftpCreds);
					curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($resource, CURLOPT_QUOTE, $cmdArray);
				
					if ($useFTPS)
					{
						curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($resource, CURLOPT_SSL_VERIFYHOST, 2);
						curl_setopt($resource, CURLOPT_FTP_SSL, CURLFTPSSL_ALL);
						curl_setopt($resource, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_SSL);
						curl_setopt($resource, CURLOPT_CAINFO, UtilsObj::getCurlPEMFilePath());
						curl_setopt($resource, CURLOPT_SSLVERSION, 0);
					}

					$result = curl_exec($resource);
					$httpCode = curl_getinfo($resource, CURLINFO_HTTP_CODE);
					$errorNumber = curl_errno($resource);

					curl_close($resource);

					//Get a list of the main order line folders and delete if empty

					$uploadPath = self::listDirectoryContent($pOrderRoot, $pPaths, $useFTPS, $pRetries, $pTimeouts);
					
					if(count($uploadPath) == 0)
					{
						$uploadOrderCmd = array();
						
						$uploadOrderCmd[] = 'RMD /' . $pOrderRoot . implode('/', $pPaths) . '/';

						$resource = curl_init();
						curl_setopt($resource, CURLOPT_URL, $url);
						curl_setopt($resource, CURLOPT_USERPWD, $ftpCreds);
						curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($resource, CURLOPT_QUOTE, $uploadOrderCmd);
				
						if ($useFTPS)
						{
							curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);
							curl_setopt($resource, CURLOPT_SSL_VERIFYHOST, 2);
							curl_setopt($resource, CURLOPT_FTP_SSL, CURLFTPSSL_ALL);
							curl_setopt($resource, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_SSL);
							curl_setopt($resource, CURLOPT_CAINFO, UtilsObj::getCurlPEMFilePath());
							curl_setopt($resource, CURLOPT_SSLVERSION, 0);
						}
					
						$result = curl_exec($resource);
						$httpCode = curl_getinfo($resource, CURLINFO_HTTP_CODE);
						$errorNumber = curl_errno($resource);

						curl_close($resource);

					}

					// check the httpCode for a successful delete operation (226)
					if ($httpCode == 226)
					{
						// delete or remove was successful
						$errorNumber = 0;
						$retry = false;
					}
					else
					{
						$retry = $retryCount < $pRetries;

						if ($retry)
						{
							$retryCount++;
						}
					}
				}


				if ($retryCount != $pRetries)
				{
					$returnData['data'] = $result;
				}
				else
				{
					$returnData['error'] = $errorNumber;
					$returnData['errorparam'] = '[' . $errorNumber . '] - ' . self::$curl_error_codes[$errorNumber];
				}
			}

			//Check if the order number fodler is empty and if so delete

			$cmdChildArray = array();
			$parentDeleteList = self::listDirectoryContent($pOrderRoot, [$pPaths[0]], $useFTPS, $pRetries, $pTimeouts);
			
			if(count($parentDeleteList) == 0)
			{
				
				$cmdChildArray[] = 'RMD /' . $pOrderRoot . $pPaths[0] . '/';
				
				$resource = curl_init();
				curl_setopt($resource, CURLOPT_URL, $url);
				curl_setopt($resource, CURLOPT_USERPWD, $ftpCreds);
				curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($resource, CURLOPT_QUOTE, $cmdChildArray);
	
				if ($useFTPS)
				{
					curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($resource, CURLOPT_SSL_VERIFYHOST, 2);
					curl_setopt($resource, CURLOPT_FTP_SSL, CURLFTPSSL_ALL);
					curl_setopt($resource, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_SSL);
					curl_setopt($resource, CURLOPT_CAINFO, UtilsObj::getCurlPEMFilePath());
					curl_setopt($resource, CURLOPT_SSLVERSION, 0);
				}

				$result = curl_exec($resource);
				$httpCode = curl_getinfo($resource, CURLINFO_HTTP_CODE);
				$errorNumber = curl_errno($resource);

				curl_close($resource);

				if($httpCode == 226)
				{
					$returnData['error'] = 0;
				}
				else
				{
					$returnData['error'] = $errorNumber;
				}
			}
			
		}

		return $returnData;
	}

	static function sendToTaopixOnline($pMethod, $pServer, $pAction, $pFields, $pFileParam = array(), $pCompress = false)
	{
		// Encrypt data before sending
		$systemConfigDataArray = DatabaseObj::getSystemConfig();

		$resultArray = array('error'=> '', 'data' => array());

		if ($pCompress)
		{
			$serialised = serialize($pFields);
			$length = strlen($serialised);
			$compressedSerialised = gzcompress($serialised, 9);
			$pFields = ['data' => $compressedSerialised, 'length' => $length];
		}

		$curlFields = array();
		$curlFields['action'] = $pAction;
		$curlFields['__k__'] = UtilsObj::encryptData($systemConfigDataArray['key'], TPX_DONOTSTEAL, false);
		$curlFields['data'] = UtilsObj::encryptData(serialize($pFields), $systemConfigDataArray['secret'], false);

		if ($pMethod == 'POST')
		{
			$returnData = self::post($pServer, $curlFields, TPX_CURL_RETRY, TPX_CURL_TIMEOUT, $pFileParam);
		}
		else
		{
			$returnData = self::put($pServer, $curlFields, TPX_CURL_RETRY, TPX_CURL_TIMEOUT);
		}

		// Decrypt the return data
		if ($returnData['error'] === '')
		{
 			$decryptData = UtilsObj::decryptData($returnData['data'], $systemConfigDataArray['secret'], false);

			if ($decryptData != '')
			{
				$unserializedData = unserialize($decryptData);

				if ($unserializedData === false)
				{
					$resultArray['error'] = 'Unable to unserialize the data';
				}
				else
				{
					$resultArray['data'] = $unserializedData;
				}
			}
			else
			{
				$resultArray['error'] = 'Unable to decrypt the data';
			}
		}
		else
		{
			$resultArray['error'] = $returnData['errorparam'];
		}

		return $resultArray;
	}

	static function sendByPut($pServer, $pAction, $pFields, $pCompressed = false)
	{
		return self::sendToTaopixOnline("PUT", $pServer, $pAction, $pFields, [], $pCompressed);
	}

	static function sendByPost($pServer, $pAction, $pFields, $pFileParam = array())
	{
		return self::sendToTaopixOnline("POST", $pServer, $pAction, $pFields, $pFileParam);
	}

	/**
	 * Executes a GET request to the privided URL.
	 *
	 * @global array $ac_config Array containing the config settings.
	 * @param string $pServer URL to send the request to.
	 * @param int $pRetries Number of retries to attempt on error.
	 * @param int $pTimeouts How long to wait in seconds.
	 * @return array The results of the request.
	 */
	static function get($pServer, $pRetries, $pTimeouts)
	{
		global $ac_config;

		$returnData = UtilsObj::getReturnArray();

		$retry = true;
		$retryCount = 1;

		while ($retry)
		{
			// increase the standard php timeout
			$incrementTime = $pTimeouts + 10;
            UtilsObj::resetPHPScriptTimeout($incrementTime);

			$resource = curl_init();
			curl_setopt($resource, CURLOPT_URL, $pServer );
			curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, $ac_config['SSLVERIFYPEER']);

			// set CURLOPT_SSL_VERIFYHOST to 0 if verify peer is off
			// we can set CURLOPT_SSL_VERIFYHOST to 1 since this isn't the default value so we don't set it 
			// and allow the default value to be set
			if ($ac_config['SSLVERIFYPEER'] == 0)
			{
				curl_setopt($resource, CURLOPT_SSL_VERIFYHOST, 0);
			}

			curl_setopt($resource, CURLOPT_CAINFO, UtilsObj::getCurlPEMFilePath());
			curl_setopt($resource, CURLOPT_TIMEOUT, $pTimeouts);

			$result = curl_exec($resource);

			$httpCode = curl_getinfo($resource, CURLINFO_HTTP_CODE);
			$errorNumber = curl_errno($resource);

			if (($errorNumber!=0) || (($httpCode!=200) && ($httpCode!=202)))
			{
				$retry = $retryCount < $pRetries;

		    	if ($retry)
		    	{
		    		$retryCount++;
		    	}
			}
			else
			{
				$retry = false;
			}

			curl_close($resource);
		}

		if ($retryCount!=$pRetries)
		{
			$returnData['data'] = $result;
		}
		else
		{
			$returnData['error'] = $errorNumber;
			$returnData['errorparam'] = '[' . $httpCode . '] - ' . self::$curl_error_codes[$errorNumber];

			error_log("CURL ERROR: " . "\nTime: " . date('H:i:s d/m/Y', time()) . "\nServer: " . $pServer . " \nError number: " . $errorNumber . " (". $returnData['errorparam'] . ")");
		}

		return $returnData;
	}
}
?>