<?php

use Security\Encryption\MCryptFactory;

require_once __DIR__ . '/TaopixAbstractGateway.php';
require_once __DIR__ . '/Interfaces/EncryptionInterface.php';



/**
 * Payment gateway integration for Neweb
 *
 * @author Anthony Dodds <anthony.dodds@taopix.com>
 * @version 1
 * @since 2020r1
 */

class Neweb extends TaopixAbstractGateway implements EncryptionInterface
{
	protected $keySuffix = '';
	protected $encryptor = null;

	public function __construct($pConfig, &$pSession, &$pGetVars, &$pPostVars)
	{
		parent::__construct($pConfig, $pSession, $pGetVars, $pPostVars);
		$this->keySuffix = ($this->config['TRANSACTIONMODE'] === 'TEST') ? 'TEST' : '';
		$this->encryptor = MCryptFactory::build(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	}

	public function configure()
	{

		$resultArray = [
			'active' => true,
			'form' => '',
			'scripturl' => '',
			'script' => '',
			'action' => ''
		];

		// if the transactionmode is set to test config keys will be appended with test
		if (($this->config['MERCHANTID' . $this->keySuffix] == '')
				|| ($this->config['HASHKEY' . $this->keySuffix] == '')
				|| ($this->config['HASHIV' . $this->keySuffix] == '')
				|| ($this->config['SERVER' . $this->keySuffix] == ''))
		{
			$resultArray['active'] = false;
		}

		// accepted currencies are not dependant on which transaction mode we are using
		if (! in_array(strtoupper($this->session['order']['currencycode']), explode(',', $this->config['CURRENCYLIST'])))
		{
			$resultArray['active'] = false;
		}

		AuthenticateObj::clearSessionCCICookie();

		return $resultArray;
	}

	public function confirm($pCallbackType)
	{
		$return = [];

		if ($pCallbackType === 'automatic')
		{
			$return = $this->automaticCallback();
		}
		else
		{
			$return = $this->manualCallback();
		}

		return $return;
	}

	private function automaticCallback()
	{

		// build default array to return
		$resultArray = $this->cciEmptyResultArray();
		$resultArray['showerror'] = false;

		$cciRef = array_key_exists('ref', $this->get) ? $this->get['ref'] : $this->session['ref'];
		// hash success
		$cciEntry = PaymentIntegrationObj::getCciLogEntry($cciRef);

		if ($cciEntry === [])
		{
			// empty first callback
			$resultArray['webbrandcode'] = $this->session['webbrandcode'];
			$resultArray['currencycode'] = $this->session['order']['currencycode'];
			$resultArray['amount'] = $this->session['order']['ordertotaltopay'];
			$resultArray['parentlogid'] = -1;
			$resultArray['orderid'] = -1;
			$resultArray['update'] = false;
			$this->updateStatus = false;
		}
		else
		{
			// additional callback
			$resultArray['webbrandcode'] = $cciEntry['webbrandcode'];
			$resultArray['currencycode'] = $cciEntry['currencycode'];
			$resultArray['amount'] = $cciEntry['formattedamount'];
			$resultArray['parentlogid'] = $cciEntry['id'];
			$resultArray['orderid'] = $cciEntry['orderid'];
			$resultArray['update'] = true;
			$this->updateStatus = true;
		}

		$returnParams = $this->processReturnData();

		// Process the payment response.
		$this->processPayment($resultArray, $returnParams);
		$resultArray['ref'] = $cciRef;

		$serverTimestamp = DatabaseObj::getServerTime();
		// Log Payment information.
		PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimestamp, '', $returnParams);

		// Set the loadsession value.
		$this->loadSession = true;

		return $resultArray;
	}

	private function manualCallback()
	{
		$returnParams = $this->processReturnData();

		// build default array to return
		$resultArray = $this->cciEmptyResultArray();
		$resultArray['showerror'] = false;

		$cciRef = array_key_exists('ref', $this->post) ? $this->post['ref'] : $this->session['ref'];
		// hash success
		$cciEntry = PaymentIntegrationObj::getCciLogEntry($cciRef);

		if ($cciEntry === [])
		{
			// empty first callback
			$resultArray['webbrandcode'] = $this->session['webbrandcode'];
			$resultArray['currencycode'] = $this->session['order']['currencycode'];
			$resultArray['amount'] = $this->session['order']['ordertotaltopay'];
			$resultArray['parentlogid'] = -1;
			$resultArray['orderid'] = -1;
			$resultArray['update'] = false;
			$this->updateStatus = false;
		}
		else
		{
			// additional callback
			$resultArray['webbrandcode'] = $cciEntry['webbrandcode'];
			$resultArray['currencycode'] = $cciEntry['currencycode'];
			$resultArray['amount'] = $cciEntry['formattedamount'];
			$resultArray['parentlogid'] = $cciEntry['id'];
			$resultArray['orderid'] = $cciEntry['orderid'];
			$resultArray['formattedpaymentdate'] = $cciEntry['formattedpaymentdate'];
			$resultArray['update'] = true;
			$this->updateStatus = true;
		}

		// If the manual request is successful we display the appropriate information.
		if (strtoupper($returnParams['Status']) === 'SUCCESS')
		{
			// Success returned in manual, this does not mean the order is paid for this needs to be confirmed by the automatic callback.
			$resultArray['paymentmeans'] = $returnParams['Result']['PaymentType'];

			if (strtoupper($returnParams['Result']['PaymentType']) === 'CREDIT')
			{
				$resultArray['formattedcardnumber'] = $returnParams['Result']['Card6No'] . str_repeat('*', 6) . $returnParams['Result']['Card4No'];
			}

			// Assign additional key => values based on current values or values returned from the gateway
			$resultArray['amount'] = $returnParams['Result']['Amt'];
			$resultArray['formattedamount'] = $this->session['order']['ordertotaltopay'];
			$resultArray['transactionid'] = $returnParams['Result']['TradeNo'];
			$resultArray['formattedtransactionid'] = $resultArray['transactionid']; //set to auth trans number
			$resultArray['formattedpaymentdate'] = date('Y-m-d H:i:s');
			$resultArray['paymentdate'] = date('Y-m-d');
			$resultArray['paymenttime'] = date('H:i:s');
			$resultArray['responsecode'] = $returnParams['Result']['RespondCode'];
			$resultArray['authorised'] = 1;
			$resultArray['authorisedstatus'] = true;
		}
		else
		{
			// SHA Check Failed or There was another error.
			$resultArray['showerror'] = true;
			$resultArray['authorised'] = 0;
			$resultArray['authorisedstatus'] = false;

			$errorCode = $returnParams['Status'];
			$errorMessage = $returnParams['Message'];

			if ($errorCode === 'FAILEDSHA')
			{
				$errorMessage = SmartyObj::getParamValue('CreditCardPayment', 'str_OrderAdyenSignatureFailed');
			}

			$resultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $errorCode;
			// the language string str_orderadyensignaturefailed gives the correct error message of signature check failed
			$resultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $errorMessage;
			$resultArray['data3'] = '';
			$resultArray['data4'] = '';
			$resultArray['errorform'] = 'error.tpl';
		}

		$serverTimestamp = DatabaseObj::getServerTime();

		// Log Payment information.
		PaymentIntegrationObj::logPaymentGatewayData($this->config, $serverTimestamp, '', $returnParams);

		// Add additional fields as required
		$resultArray['ref'] = $this->session['ref'];
		$resultArray['paymentreceived'] = ($resultArray['authorisedstatus']) ? 1 : 0;
		$resultArray['resultisarray'] = false;
		$resultArray['resultlist'] = [];

		return $resultArray;
	}

	private function processReturnData()
	{
		$returnInfo = [];
		$returnParts = [];
		$rawReturn = file_get_contents("php://input");

		parse_str($rawReturn, $returnParts);

		// If there is a sha value to vlaidate and it validates correctly decrypt and json decode the data.
		if ((isset($returnParts['TradeSha'])) && ($this->verifyHash($returnParts['TradeSha'], $returnParts, '')))
		{
			$returnInfo = json_decode($this->decryptData($returnParts['TradeInfo']), true);
		}
		else
		{
			// SHA failed to match or was missing give an error.
			$returnInfo['Status'] = 'FAILEDSHA';
			$returnInfo['Message'] = '';
		}

		return $returnInfo;
	}

	private function processPayment(&$pResultArray, $pDecodedData)
	{
		$serverTimestamp = DatabaseObj::getServerTime();
		list($serverDate, $serverTime) = explode(' ', $serverTimestamp, 2);

		// If the Status is success we know we need to continue checking for an authorised payment.
		if ($pDecodedData['Status'] === 'SUCCESS')
		{
			// All payments are not authorised by default.
			$paymentAuthorised = false;

			// We get different values back based on the payment type.
			switch ($pDecodedData['Result']['PaymentType'])
			{
				case 'CREDIT':
				case 'ANDROIDPAY':
				case 'SAMSUNGPAY':
				{
					if ((isset ($pDecodedData['Result']['RespondCode'])) && ($pDecodedData['Result']['RespondCode'] !== null))
					{
						$paymentAuthorised = true;
						$pResultArray['authorisationid'] = $pDecodedData['Result']['Auth'];
						$pResultArray['transactionid'] = $pDecodedData['Result']['TradeNo'];

						$pResultArray['formattedauthorisationid'] = $pResultArray['transactionid'];
						$pResultArray['formattedtransactionid'] = $pResultArray['transactionid'];
						$pResultArray['bankresponsecode'] = $pDecodedData['Result']['RespondCode'];
						$pResultArray['formattedamount'] = $this->session['order']['ordertotaltopay'];

						if (strtoupper($pDecodedData['Result']['PaymentType']) === 'CREDIT')
						{
							$pResultArray['formattedcardnumber'] = $pDecodedData['Result']['Card6No'] . str_repeat('*', 6) . $pDecodedData['Result']['Card4No'];
						}
					}
					break;
				}
				case 'WEBATM':
				{
					$paymentAuthorised = true;
					$pResultArray['authorisationid'] = $pDecodedData['Result']['PayBankCode'];
					$pResultArray['formattedauthorisationid'] = $pResultArray['transactionid'];
					break;
				}
				case 'CVS':
				{
					$paymentAuthorised = true;
					$pResultArray['authorisationid'] = $pDecodedData['Result']['CodeNo'] . '-' . $pDecodedData['Result']['StoreType'] . '-' . $pDecodedData['Result']['StoreID'];
					break;
				}
				case 'BARCODE':
				{
					$paymentAuthorised = true;
					$pResultArray['authorisationid'] = $pDecodedData['Result']['PayStore'];
					break;
				}
			}

			// Set that if the payment has been received, and the method we captured the payment with,
			$pResultArray['paymentreceived'] = ($paymentAuthorised) ? 1 : 0;
			$pResultArray['paymentmeans'] = $pDecodedData['Result']['PaymentType'];

			// If we have an authorised payment complete the date/time of the payment.
			if ($paymentAuthorised)
			{
				$pResultArray['paymentdate'] = $serverDate;
				$pResultArray['paymenttime'] = $serverTime;
				$pResultArray['formattedpaymentdate'] = $serverTimestamp;
				$pResultArray['authorised'] = 1;
				$pResultArray['authorisedstatus'] = true;
			}
		}
		else
		{
			// Status was something other than success that means there was an error.
			$pResultArray['showerror'] = true;
			$pResultArray['authorised'] = false;
			$pResultArray['authorisedstatus'] = 0;

			$errorCode = $pDecodedData['Status'];
			$errorMessage = $pDecodedData['Message'];

			if ($errorCode === 'FAILEDSHA')
			{
				$errorMessage = SmartyObj::getParamValue('CreditCardPayment', 'str_OrderAdyenSignatureFailed');
			}

			$pResultArray['data1'] = SmartyObj::getParamValue('Order', 'str_LabelErrorCode') . ': ' . $errorCode;
			// the language string str_orderadyensignaturefailed gives the correct error message of signature check failed
			$pResultArray['data2'] = SmartyObj::getParamValue('Order', 'str_LabelErrorMessage') . ': ' . $errorMessage;
			$pResultArray['data3'] = '';
			$pResultArray['data4'] = '';
			$pResultArray['errorform'] = 'error.tpl';
		}
	}

	public function generateHash($pString)
	{
		$hashString = 'HashKey=' . $this->config['HASHKEY' . $this->keySuffix];
		$hashString .= '&' . $pString;
		$hashString .= '&HashIV=' . $this->config['HASHIV' . $this->keySuffix];

		return strtoupper(hash("SHA256", $hashString));
	}

	public function hashString($pParams, $pType)
	{
		return $pParams['TradeInfo'];
	}

	public function initialize()
	{
		$resultArray = [];

		$smarty = SmartyObj::newSmarty('Order', $this->session['webbrandcode'], $this->session['webbrandapplicationname']);

		$fixedUrlPath = UtilsObj::correctPath($this->session['webbrandweburl']);
		$cancelReturnPath = $fixedUrlPath . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'];

		// first check if we have any ccidata. this is set when the call is made the first time.
		// if the data is set then the user must have hit the back button on their browser
		if ($this->session['order']['ccidata'] == '')
		{
			$rawOrderInformation = $this->generateOrderInfo();
			$orderData = $this->encryptData($rawOrderInformation);

			// initialize action
			$params = [
				'MerchantID' => $this->config['MERCHANTID' . $this->keySuffix],
				'TradeInfo' => $orderData,
				'TradeSha' => $this->generateHash($orderData),
				'Version' => '1.5',
			];

			$smarty->assign('payment_url', $this->config['SERVER' . $this->keySuffix]);
			$smarty->assign('method', 'POST');
			$smarty->assign('parameter', $params);
		}
		else
		{
			// the user may have clicked the back button
			AuthenticateObj::clearSessionCCICookie();

			$smarty->assign('server', $cancelReturnPath);
		}

		// mobile browser check then return appropriate content
		if ($this->session['ismobile'] === true)
		{
			$resultArray = [
				'template' => $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest_small.tpl'),
				'javascript' => $smarty->fetchLocale('order/PaymentIntegration/PaymentRequest.tpl')
			];
			return $resultArray;
		}
		else
		{
			$smarty->displayLocale('order/PaymentIntegration/PaymentRequest_large.tpl');
		}
	}

	public function verifyHash($pSuppliedHash, $pParams, $pType)
	{
		$ourHash = $this->generateHash($this->hashString($pParams, $pType), $pType);

		return ($ourHash === $pSuppliedHash);
	}

	public function decryptData($pEncryptedString)
	{
		$encryptionKey = $this->config['HASHKEY' . $this->keySuffix];
		$encryptionIV = $this->config['HASHIV' . $this->keySuffix];

		// Convert the passed hex string to binary
		$binString = hex2bin($pEncryptedString);

		// Using the encryptor decrypt this string.
		$decryptedString = $this->encryptor->decrypt($encryptionKey, $binString, $encryptionIV);

		// Remove the pkcs padding, and return the string.
		return $this->pkcs7Remove(32, $decryptedString);
	}

	public function encryptData($pData)
	{
		$encryptionKey = $this->config['HASHKEY' . $this->keySuffix];
		$encryptionIV = $this->config['HASHIV' . $this->keySuffix];
		$prePadString = '';

		if (! empty($pData))
		{
			$prePadString = http_build_query($pData);
		}

		$padString = $this->pkcs7Padding(32, $prePadString);
		$encryptString = $this->encryptor->encrypt($encryptionKey, $padString, $encryptionIV);

		return trim(bin2hex($encryptString));
	}

	public function getBlockSize()
	{
		return null;
	}

	public function pkcs7Padding($pBlockSize, $pString)
	{
		// Work out string length.
		$stringLength = strlen($pString);

		// Calculate what the pad character should be.
		$padChar = $pBlockSize - ($stringLength % $pBlockSize);

		// Return the string with the padChar repeated that many times.
		return $pString . str_repeat(chr($padChar), $padChar);
	}

	public function pkcs7Remove($pBlockSize, $pString)
	{
		// Get the last character.
		$padChar = ord(substr($pString, -1));

		// Remove the pad characters from end of the string;
		$checkString = substr($pString, -$padChar);

		if (str_repeat(chr($padChar), $padChar) === $checkString)
		{
			$endPoint = strlen($pString) - $padChar;
			return substr($pString, 0, $endPoint);
		}
		else
		{
			return false;
		}
	}

	private function generateOrderInfo()
	{
		$baseUrlPath = $this->session['webbrandweburl'];

		return array_merge([
			'MerchantID' => $this->config['MERCHANTID' . $this->keySuffix],
			'RespondType' => 'JSON',
			'TimeStamp' => time(),
			'Version' => '1.5',
			'MerchantOrderNo' => $this->session['ref'],
			'Amt' => number_format($this->session['order']['ordertotaltopay'], 0, '.', ''),
			'ItemDesc' => $this->getOrderDescription(),
			'Email' => $this->session['order']['billingcustomeremailaddress'],
			'LoginType' => $this->config['NEWEBPAYMEMBERSONLY' . $this->keySuffix],
			'LangType' => $this->session['browserlanguagecode'],
			'ReturnURL' => $baseUrlPath . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'],
			'NotifyURL' => $baseUrlPath . '?fsaction=Order.ccAutomaticCallback&ref=' . $this->session['ref'],
			'CustomerURL' => $baseUrlPath . '?fsaction=Order.ccManualCallback&ref=' . $this->session['ref'],
			'ClientBackURL' => $baseUrlPath . '?fsaction=Order.ccCancelCallback&ref=' . $this->session['ref'],
			'InstFlag' => 0,
			'TradeLimit' => 0,
		], $this->getPaymentTypes());
	}

	private function getOrderDescription()
	{
		$orderDescription = $this->config['ORDERDESCRIPTION'];

		if ($orderDescription === '')
		{
			$orderDescription = $this->session['items'][0]['itemproductname'];
		}

		// Return the localised name for the product.
		return str_replace(array('"', '"'), array('&#34', '&#39'), LocalizationObj::getLocaleString($orderDescription, $this->session['browserlanguagecode'], true));
	}

	private function getPaymentTypes()
	{
		$methods = [
			'CREDIT' => 1,
			'ANDROIDPAY' => 1,
			'SAMSUNGPAY' => 1,
			'CREDRED' => 1,
			'UNIONPAY' => 1,
			'WEBATM' => 1,
			'VACC' => 1,
			'CVS' => 1,
			'BARCODE' => 1,
			'P2G' => 1,
			'CVSCOM' => 0
		];

		// If we have been supplied a list of payment methods that are available update this.
		if (strtoupper($this->config['PAYMENTMETHODS']) !== 'ALL')
		{
			// Generate the list of passed payment methods.
			$selectedList = explode(',', $this->config['PAYMENTMETHODS']);

			// Loop over each method supported by the gateway.
			foreach ($methods as $paymentType => $status)
			{
				// If we have a payment method that is not listed in the config file, disable it.
				if (! in_array($paymentType, $selectedList))
				{
					$methods[$paymentType] = 0;
				}
			}
		}

		// Return the active payment methods.
		return $methods;
	}
}
