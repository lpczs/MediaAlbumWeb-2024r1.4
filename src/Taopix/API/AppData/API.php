<?php
namespace Taopix\API\AppData;

require_once __DIR__ . '/../../../../Utils/Utils.php';

class API
{
	private $config = array();
	private $apiSessionRef = "";
	private $nextTransactionRef = 0;
	private $outputIsEncrypted = 1;
	private $debugMode = 0;

	public function __construct()
	{
		$this->config = $this->readConfig();
		$this->nextTransactionRef = rand();
	}

	private function readConfig()
	{
		return \UtilsObj::readConfigFile(__DIR__ . '/../../../../config/breakOutConf.conf');
	}

	// authenticate before any API call (select, insert, update, delete)
	public function authenticate()
	{
		$encryptedData = $this->encryptAuthenticateData($this->nextTransactionRef);
		$result = $this->postData($encryptedData, 'Authenticate');

		if ($this->outputIsEncrypted == 1)
		{
			$this->decodeTransmissionString($result, $resultLength);
			$result = mcrypt_decrypt(MCRYPT_BLOWFISH, $this->config['DATAAPISECRETKEY'], $result, MCRYPT_MODE_CBC, $encryptedData['iv']);
			$result = substr($result, 0, $resultLength);
			
		}

		$authData = json_decode($result, true);

		$this->apiSessionRef = $authData['ref'];

		return $authData;
	}

	private function encryptAuthenticateData()
	{
		$result = Array();

		$iv = $this->createRandomString(8);

		// encrypt password
		$password = strlen($this->config['PASSWORD']) . '=' . base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, $this->config['DATAAPISECRETKEY'], $this->config['PASSWORD'], MCRYPT_MODE_CBC, $iv));

		// encrypt the next transaction ref for the next transaction
		$transRefString = strlen($this->nextTransactionRef) . '=' . base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, $this->config['DATAAPISECRETKEY'], $this->nextTransactionRef, MCRYPT_MODE_CBC, $iv));

		// join them together then encrypt to make the finda data being posted.
		$postData = $this->config['USERNAME'] . "~~~~" . $password . "~~~~" . $transRefString;
		$ecryptedData = $iv . '=' . strlen($postData) . '=' . base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, $this->config['DATAAPISECRETKEY'], $postData, MCRYPT_MODE_CBC, $iv));

		$result['iv'] = $iv;
		$result['login'] = $ecryptedData;

		return $result;
	}

	private function apiCall($pJSON, $pAction)
	{
		$encryptedData = $this->encryptDataAPICall($pJSON, $this->nextTransactionRef);
		$result = $this->postData($encryptedData, $pAction);

		if ($this->outputIsEncrypted == 1)
		{
			$this->decodeTransmissionString($result, $resultLength);
			$result = mcrypt_decrypt(MCRYPT_BLOWFISH, $this->config['DATAAPISECRETKEY'], $result, MCRYPT_MODE_CBC, $encryptedData['iv']);
			$result = substr($result, 0, $resultLength);
		}

		return json_decode($result, true);
	}

	// encrypt json string
	private function encryptDataAPICall($pJSON)
	{
		$result = Array();

		$iv = $this->createRandomString(8);

		// transref is the pervious Next transaction ref
		$transRefString = strlen($this->nextTransactionRef) . '=' . base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, $this->config['DATAAPISECRETKEY'], $this->nextTransactionRef, MCRYPT_MODE_CBC, $iv));

		// generate a new next transaction ref for the next transaction (will be saved in web api session)
		// this is for client side call (will be used as a parameter for this function next time)
		$this->nextTransactionRef = rand();

		$nextTransactionRef = strlen($this->nextTransactionRef) . '=' . base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, $this->config['DATAAPISECRETKEY'], $this->nextTransactionRef, MCRYPT_MODE_CBC, $iv));

		// encrypt json data
		$pJSON = strlen($pJSON) . '=' . base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, $this->config['DATAAPISECRETKEY'], $pJSON, MCRYPT_MODE_CBC, $iv));

		// join them together then encrypt to make a header for the transaction
		$header = $this->apiSessionRef . "~~~~" . $transRefString . "~~~~" . $nextTransactionRef;
		$header = $iv . '=' . strlen($header) . '=' . base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, $this->config['DATAAPISECRETKEY'], $header, MCRYPT_MODE_CBC, $iv));

		// return result
		$result['iv'] = $iv;
		$result['json'] = $pJSON;
		$result['header'] = $header;

		return $result;
	}

	private function postData($pPostData, $pAction)
	{
		$serverURL = $this->config['SERVERURL'] . '?fsaction=AppDataAPI.' . $pAction;

		if ($this->debugMode == 1)
		{
			$serverURL .= '&debug=1';
		}

		// initialise a CURL session
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_URL, $serverURL);

		// stop CURL from verifying the peer's certificate
		curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);

		// set the headers using the array of headers
		curl_setopt($connection, CURLOPT_HEADER, false);

		// set method as POST
		curl_setopt($connection, CURLOPT_POST, 1);

		// set the XML body of the request
		curl_setopt($connection, CURLOPT_POSTFIELDS,$pPostData);

		// set it to return the transfer as a string from curl_exec
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

		// send the request
		if (! $response = curl_exec($connection))
		{
			$response = curl_error($connection);
		}

		// close the connection
		curl_close($connection);

		// return the response
		return $response;
	}


	private function decodeTransmissionString(&$pString, &$pOrigLength)
	{
		// decode a blowfish encrypted string that has been transmitted to taopix control centre ready for decrypting
		$pos = strpos($pString, '=');
		$strLen = substr($pString, 0, $pos);
		$strLen = (int)$strLen;
		$pString = base64_decode(substr($pString, $pos + 1));
		$pOrigLength = $strLen;
	}

	private function createRandomString($pLength)
    {
        // create a random alpha-numerical string
        $salt = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        srand((double)microtime() * 1000000);
        $result = '';

        for ($i = 0; $i < $pLength; $i++)
        {
            $result .= substr($salt, rand() % strlen($salt), 1);
        }

        return $result;
    }

	public function cancelOrder($pOrderSessionID)
	{
		$JSONString =
		'[
			 {
					"sessionref": "' . $pOrderSessionID . '"
				}
			]';

		$resultArray = $this->apiCall($JSONString, 'CancelOrder');

		return $resultArray;
	}

	public function endSession()
	{
		$JSONString =
		'[
			 {
					"sessionref": "' . $this->apiSessionRef . '"
				}
			]';

		$resultArray = $this->apiCall($JSONString, 'EndSession');

		return $resultArray;
	}

	public function getProjectOrderData($pOrderNumber, $pProjectRefArray)
	{
		$JSONString =
		'[
			 {
					"ordernumber": "' . $pOrderNumber . '",
					"projectreflist": ["' . implode('","', $pProjectRefArray) . '"]
				}
			]';

		$resultArray = $this->apiCall($JSONString, 'getProjectOrderData');

		return $resultArray;
	}

	public function insertOrder($pOrderData)
	{
		$JSONString = json_encode($pOrderData);

		$resultArray = $this->apiCall('[' . $JSONString . ']', 'insertOrder');

		return $resultArray;
	}

	public function createVoucher($pVoucherData)
	{
		$JSONString = json_encode($pVoucherData);

		$resultArray = $this->apiCall('[' . $JSONString . ']', 'Insert');

		return $resultArray;
	}

	public function updateVoucher($pVoucherData)
	{
		$JSONString = json_encode($pVoucherData);

		$resultArray = $this->apiCall('[' . $JSONString . ']', 'Update');

		return $resultArray;
	}

	public function deleteVoucher($pVoucherData)
	{
		$JSONString = json_encode($pVoucherData);

		$resultArray = $this->apiCall('[' . $JSONString . ']', 'Delete');

		return $resultArray;
	}

}
