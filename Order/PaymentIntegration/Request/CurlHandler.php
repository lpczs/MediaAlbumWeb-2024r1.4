<?php

require_once __DIR__ . '/../Interfaces/CurlInterface.php';

/**
 * Curl Wrapper, built to allow mocking of this object so return values can be simulated easier
 * Allows better unit testing and makes this available to more than just payment gateways
 *
 * @author Anthony Dodds
 */
class CurlHandler implements CurlInterface
{
	protected $connection = false;
	protected $defaultOptions = [];
	protected $sendMethod = '';
	protected $errorCodes = [
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
		88 => 'CURLE_CHUNK_FAILED'
	];

	/**
	 * 
	 * @param string $sendMethod
	 * @param array $pDefaultParams
	 */
	public function __construct($sendMethod, $pDefaultParams = [])
	{
		$this->defaultOptions = $pDefaultParams;
		$this->sendMethod = $sendMethod;
	}

	/**
	 * Documentation in ./Interfaces/CurlInterface
	 * {@inheritDoc}
	 */
	public function connectionSend($pServer, $pEndpoint, $pMethod, $pParams, $pRetries)
	{
		$return = null;

		if (false === $this->connection)
		{
			$this->connectionInit([]);
		}
		// define request specific options such as url, values, and method
		$options = $this->processOptions($pMethod, $pParams);
		if (!isset($options['error']))
		{
			$additionalCurlOptions = [CURLOPT_URL => $pServer . $pEndpoint,] + $options;
			curl_setopt_array($this->connection, $additionalCurlOptions);

			$retry = true;
			$currentRetryCount = 1;

			while ($retry)
			{
				$return = curl_exec($this->connection);
				$responseCode = curl_getinfo($this->connection, CURLINFO_HTTP_CODE);
				$errNo = curl_errno($this->connection);

				// no error and a 2xx header this relates to a success response
				// or of the request is an exists and the responseCode is 350
				if ((0 === $errNo) && ('2' === substr($responseCode, 0, 1)) || (('EXISTS' === $pMethod) && (350 === $responseCode))
				)
				{
					$retry = false;
				}
				else
				{
					if ($currentRetryCount < $pRetries)
					{
						$currentRetryCount++;
					}
					else
					{
						$retry = false;
						/**
						 * The last response will be the error that is passed if this is
						 * a curl error we need to push that in to the return value
						 */
						if (0 !== $errNo)
						{
							// if after our retrys we are getting a curl error code pass that back to us in the same format
							$return = $this->formatData([
								'errordescription' => curl_error($this->connection),
								'errornumber' => $errNo,
								'errorname' => (isset($this->errorCodes[$errNo]) ? $this->errorCodes[$errNo] : ''),
								'info' => curl_getinfo($this->connection)
							]);
						}
					}
				}
			}
		}
		else
		{
			//set return to be the error from processOptions
			$return = $options;
		}
		return $return;
	}

	/**
	 * Documentation in ./Interfaces/CurlInterface
	 * {@inheritDoc}
	 */
	public function connectionClose()
	{
		if ((null !== $this->connection) && (false !== $this->connection))
		{
			curl_close($this->connection);
			$this->connection = false;
		}
	}

	/**
	 * Documentation in ./Interfaces/CurlInterface
	 * {@inheritDoc}
	 */
	public function connectionInit($pOptions)
	{
		$this->connection = curl_init();
		if (false !== $this->connection)
		{
			$mergedOptions = $this->defaultOptions + $pOptions;
			curl_setopt_array($this->connection, $mergedOptions);
		}
	}

	/**
	 * Documentation in ./Interfaces/CurlInterface
	 * {@inheritDoc}
	 */
	public function getConnection()
	{
		return $this->connection;
	}

	/**
	 * Method to decorate data to be sent via curl
	 * Additional formatting methods may need to be added here
	 * 
	 * @param array $pData
	 * @return string
	 */
	public function formatData($pData)
	{
		$return = null;
		if ('json' === $this->sendMethod)
		{
			$return = json_encode($pData);
		}
		else if ('serialize' === $this->sendMethod)
		{
			$return = serialize($pData);
		}
		else if ('array' === $this->sendMethod)
		{
			$return = $pData;
		}
		else
		{
			$return = http_build_query($pData);
		}
		return $return;
	}

	/**
	 * Method used to configure curl options based on specific request
	 * 
	 * @param string $pMethod method we wish to use
	 * @param array $pData data specific to this request
	 * @return array
	 */
	protected function processOptions($pMethod, $pData)
	{
		$return = [];

		switch (strtoupper($pMethod))
		{
			case 'GET':

				break;

			case 'POST':
				$return = [
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => $this->formatData($pData),
				];
				break;

			case 'PUT':
				$compressedData = UtilsObj::compressArray($pData);
				$memoryHandle = fopen('php://temp/maxmemory:256000', 'w');

				if ($memoryHandle)
				{
					fwrite($memoryHandle, $compressedData);
					fseek($memoryHandle, 0);

					$return = [
						CURLOPT_BINARYTRANSFER => true,
						CURLOPT_PUT => true,
						CURLOPT_INFILE => $memoryHandle,
						CURLOPT_INFILESIZE => strlen($compressedData)
					];
				}
				else
				{
					$return = [
						'error' => 0,
						'errorparam' => 'Unable to get memory handle'
					];
				}
				break;

			case 'PUTFILE':
				if (isset($pData['filepath']))
				{
					$fileHandle = fopen($pData['filepath'], 'r');

					if ($fileHandle)
					{
						$fileSize = filesize($pData['filepath']);
						$return = [
							CURLOPT_BINARYTRANSFER => true,
							CURLOPT_PUT => true,
							CURLOPT_INFILE => $fileHandle,
							CURLOPT_INFILESIZE => $fileSize
						];
						fclose($fileHandle);
					}
					else
					{
						$return = [
							'error' => 0,
							'errorparam' => 'Unable to open file'
						];
					}
				}
				break;

			case 'DIRLIST':
				$credentials = $pData['FTPUSER'] . ':' . $pData['FTPPASS'];

				$return = [
					CURLOPT_CUSTOMREQUEST => 'LIST -a',
					CURLOPT_USERPWD => $credentials,
					CURLOPT_FTP_SSL => CURLFTPSSL_ALL,
					CURLOPT_FTPSSLAUTH => (true === $pData['useftps'] ? CURLFTPAUTH_SSL : CURLFTPAUTH_DEFAULT)
				];
				if (isset($pData['useplain']) && (true === $pData['useplain']))
				{
					//if we are configured to use plain text auth remove the ssl opts
					unset($return[CURLOPT_FTPSSLAUTH], $return[CURLOPT_FTP_SSL]);
				}
				break;

			case 'DELETE':
				$credentials = $pData['FTPUSER'] . ':' . $pData['FTPPASS'];
				$return = [
					CURLOPT_QUOTE => $pData['commands'],
					CURLOPT_USERPWD => $credentials,
					CURLOPT_FTP_SSL => CURLFTPSSL_ALL,
					CURLOPT_FTPSSLAUTH => (true === $pData['useftps'] ? CURLFTPAUTH_SSL : CURLFTPAUTH_DEFAULT)
				];
				if (isset($pData['useplain']) && (true === $pData['useplain']))
				{
					//if we are configured to use plain text auth remove the ssl opts
					unset($return[CURLOPT_FTPSSLAUTH], $return[CURLOPT_FTP_SSL]);
				}
				break;

			case 'EXISTS':
				$return = [
					CURLOPT_NOBODY => true,
					CURLOPT_RETURNTRANSFER => false,
				];
				break;
		}

		return $return;
	}

	public function setSendMethod($method)
	{
		$this->sendMethod = $method;
	}
}
?>