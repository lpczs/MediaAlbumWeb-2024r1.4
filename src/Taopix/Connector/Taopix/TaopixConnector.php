<?php

namespace Taopix\Connector\Taopix;

use Taopix\API\AppData\API as AppDataAPI;
use Taopix\Connector\Connector;
use Taopix\Connector\Shopify\EDLTrait;
use Taopix\Core\Traits\HeaderTrait;
use Taopix\Core\Utils\TaopixUtils;
use PricingEngine\BCMath;
use Taopix\Connector\Taopix\Entity\Voucher as VoucherEntity;


class TaopixConnector extends \Taopix\Connector\Connector
{
	use EDLTrait;
	use HeaderTrait;

	private $ordersPaidSchema = ['currency' => '', 'order_number' => '', 'order_total' => '',
								'line_items' => [
									['projectref' => '',
									'quantity' => '',
									'tax_rate' => '',
									'tax_code' => '',
									'item_total_sell' => '']
								],
								'customer' => ['account_code' => ''],
								'shipping_method' => ['code' => '', 'name' => ''],
								];

	private $webhookLogFile = 'ordersPaidWebhookErrorLogFile.log';

	private $webhookError = '';

	private $webhookRecordID = 0;

	/**
	 * Sets the orders paid error message
	 *
	 * @param string error message
	 * @return TaopixConnector TaopixConnector instance.
	 */
	public function setWebhookError(string $pError): TaopixConnector
	{
		$this->webhookError = $pError;
		return $this;
	}

	/**
	 * Returns the webhook error.
	 */
	public function getWebhookError(): string
	{
		return $this->webhookError;
	}

	/**
	 * Sets the record id of the webhook in the Taopix database
	 *
	 * @param string error message
	 * @return int TaopixConnector instance.
	 */
	public function setWebhookRecordID(int $pID): TaopixConnector
	{
		$this->webhookRecordID = $pID;
		return $this;
	}

	/**
	 * Returns the webhook record id.
	 */
	public function getWebhookRecordID(): int
	{
		return $this->webhookRecordID;
	}

	public function __construct($pConnectorName, $pQueryArray)
	{
		$this->setUtils(new TaopixUtils());
		$acConfig = $this->getUtils()->getACConfig();
		$this->setACConfig($acConfig);
		$this->setApiSecret($acConfig['TAOPIXCONNECTORWEBHOOKSECRET']);
	}

	public function __destruct()
	{
		$error = $this->getWebhookError();

		if ($error != '')
		{
			$error .= ' Taopix webhook record id ' . $this->getWebhookRecordID();
			$this->getUtils()->writeToDebugFileInLogsFolder($this->webhookLogFile, $error);
		}
	}

	/**
	 * Verifies the signature for a webhook call is valid.
	 *
	 * @return bool True if the signature matches.
	 */
	public function verifyWebhookHash(string $pPayload): bool
	{
		// perform HMAC vaidation for webhooks
		$hmac_header = $_SERVER['HTTP_X_TAOPIX_SIGNATURE'];
		$hmac = base64_encode(hash_hmac('sha256', $pPayload, $this->getApiSecret(), true));
		return hash_equals($hmac, $hmac_header);
	}

	/**
	 * Inserts the order into Taopix and deleted the temporary product.
	 * Routes the project if needed.
	 *
	 * @param array $pPayloadArray Data from the Shopify webhook.
	 */
	public function ordersPaid(array $pPayloadArray): void
	{
		$orderNumber = isset($pPayloadArray['order_number']) ? $pPayloadArray['order_number'] : '';
		$orderedProjectRefList = [];
		$userAccount = [];
		$userID = 0;
		$topic = 'orders/paid';
		$priceMap = [];
		$totalToPay = 0.00;
		$lineQtyMismatch = false;

		$webhookArray = $this->recordWebhookData('TAOPIX', $topic, $pPayloadArray, $orderNumber);
		$this->setWebhookRecordID($webhookArray['webhookid']);

		if (!$this->validatePayload($pPayloadArray, $this->ordersPaidSchema))
		{
			header("HTTP/1.1 400");
			return;
		}

		$customer = $pPayloadArray['customer'];
		$customerID = $customer['account_code'];
		$orderLineItems = $pPayloadArray['line_items'];

		foreach ($orderLineItems as $lineItem) {
			$orderedProjectRefList[] = $lineItem['projectref'];

			$priceMap[$lineItem['projectref']] = [
				'taxrate' => $lineItem['tax_rate'],
				'taxcode' => $lineItem['tax_code'],
				'qty' => $lineItem['quantity'],
				'itemtotal' => $lineItem['item_total_sell']
			];
		}

		$lineItemCount = count($orderedProjectRefList);

		if ($lineItemCount > 0) {
			$dataAPI = new AppDataAPI();

			$authenticationResult = $dataAPI->authenticate();
			$result = $authenticationResult['error'];

			if ($result == '') {
				$projectOrderDataResult = $dataAPI->getProjectOrderData($orderNumber, $orderedProjectRefList);
				$result = $projectOrderDataResult['error'];
				$orderData = $projectOrderDataResult['orderdata'];
				$shoppingCartSessionRef = $projectOrderDataResult['orderdata']['sessionref'];

				// check to make sure we have either no error or a partial order
				if ($result == 0 || $result == 4) {

					$this->setLicenseKeyCode($orderData['headerarray']['groupcode']);

					try {
						// Set the customer ID as the hashed version.
						$customerAccount = [];
						$customerAccount['id'] = $customerID;
						$customerAccount['firstname'] = $customer['first_name'];
						$customerAccount['lastname'] = $customer['last_name'];
						$customerAccount['email'] = $customer['email'] ?? '';
						$customerAccount['phone'] = $customer['phone'] ?? '';

						$userAccount = $this->createUserAccount($customerAccount, true);
						$userID = $userAccount['recordid'];
					} catch (\Exception $pError) {
						throw new \Exception($pError->getMessage(), $pError->getCode());
					}

					// check to see if we should be performing a line item quantity check
					$componentUpsellSettings = $this->getComponentUpSellConfig($this->getLicenseKeyCode());

					$lineItemQtyProtected = ($componentUpsellSettings & TPX_COMPONENT_UPSELL_ALLOW_PRODUCT_QTY);

					$orderData['headerarray']['userid'] = $userID;
					$orderData['headerarray']['itemcount'] = $lineItemCount;
					$orderData['cartitemcount'] = $lineItemCount;
					$itemNumber = 0;

					foreach ($orderData['cartarray'] as &$lineItem) {

						$priceData = $priceMap[$lineItem['projectref']];

						// if quantity has been selected in the designer we must check to make sure what has been sent back matches.
						// quantities cannot be set for photoprints so we can skip
						if (($lineItemQtyProtected) && ($lineItem['qty'] != $priceData['qty']) &&
							($lineItem['producttype']!= TPX_PRODUCTCOLLECTIONTYPE_PHOTOPRINTS) && ($lineItem['source'] == 1) && ($orderData['reorder'] == 0))
						{
							$lineQtyMismatch = true;
							break;
						}

						$lineItem['userid'] = $userID;

						// Copy price data scaled to use the currency decimal number.
						$itemTotal = BCMath::round($priceData['itemtotal'] *  $priceData['qty'], $orderData['headerarray']['currencydecimalplaces']);
						$lineItem['totalsell'] = $itemTotal;
						$lineItem['producttotalsell'] = $itemTotal;
						$lineItem['subtotal'] = $itemTotal;
						$lineItem['taxname'] = $priceData['taxcode'];
						$lineItem['taxrate'] = $priceData['taxrate'];
						$lineItem['qty'] = $priceData['qty'];
						$lineItem['itemnumber'] = ++$itemNumber;

					}

					if (!$lineQtyMismatch)
					{
						$orderData = $this->routeOrderItems($orderData);

						// update order totals.
						$totalToPay = BCMath::round($pPayloadArray['order_total'], $orderData['headerarray']['currencydecimalplaces']);
						$orderData['headerarray']['ordertotalitemsell'] = $totalToPay;
						$orderData['headerarray']['ordertotal'] = $totalToPay;
						$orderData['headerarray']['totalbeforediscount'] = $totalToPay;
						$orderData['headerarray']['totalsell'] = $totalToPay;

						// update shipping data
						$orderData['shippingdata']['shippingmethodcode'] = (is_null($pPayloadArray['shipping_method']['code']) ? '' : $pPayloadArray['shipping_method']['code']);
						$orderData['shippingdata']['shippingmethodname'] = (is_null($pPayloadArray['shipping_method']['name']) ? '' : $pPayloadArray['shipping_method']['name']);
						$orderData['shippingdata']['shippingcustomeraddress1'] = (is_null($pPayloadArray['shipping_address']['address1']) ? '' : $pPayloadArray['shipping_address']['address1']);
						$orderData['shippingdata']['shippingcustomeraddress2'] = (is_null($pPayloadArray['shipping_address']['address2']) ? '' : $pPayloadArray['shipping_address']['address2']);
						$orderData['shippingdata']['shippingcustomercity'] = (is_null($pPayloadArray['shipping_address']['city']) ? '' : $pPayloadArray['shipping_address']['city']);
						$orderData['shippingdata']['shippingcustomerstate'] = (is_null($pPayloadArray['shipping_address']['state']) ? '' : $pPayloadArray['shipping_address']['state']);
						$orderData['shippingdata']['shippingcustomerregioncode'] = (is_null($pPayloadArray['shipping_address']['region_code']) ? '' : $pPayloadArray['shipping_address']['region_code']);
						$orderData['shippingdata']['shippingcustomerregion'] = (is_null($pPayloadArray['shipping_address']['region']) ? '' : $pPayloadArray['shipping_address']['region']);
						$orderData['shippingdata']['shippingcustomerpostcode'] = (is_null($pPayloadArray['shipping_address']['post_code']) ? '' : $pPayloadArray['shipping_address']['post_code']);
						$orderData['shippingdata']['shippingcustomercountrycode'] = (is_null($pPayloadArray['shipping_address']['country_code']) ? '' : $pPayloadArray['shipping_address']['country_code']);
						$orderData['shippingdata']['shippingcustomercountryname'] = (is_null($pPayloadArray['shipping_address']['country_name']) ? '' : $pPayloadArray['shipping_address']['country_name']);
						$orderData['shippingdata']['shippingcontactfirstname'] = (is_null($pPayloadArray['shipping_address']['first_name']) ? '' : $pPayloadArray['shipping_address']['first_name']);
						$orderData['shippingdata']['shippingcontactlastname'] = (is_null($pPayloadArray['shipping_address']['last_name']) ? '' : $pPayloadArray['shipping_address']['last_name']);

						$insertOrderDataResult = $dataAPI->insertOrder($orderData);
						$result = $insertOrderDataResult['error'];

						if ($result == '') {
							$orderDate = date('Y-m-d H:i:s', time());

							$this->getUtils()->updateProjectOrderDataCache($orderedProjectRefList, $orderDate, $orderNumber);

							// Delete the cache file for Desktop orders.
							self::deleteCheckoutFile($shoppingCartSessionRef);

							header("HTTP/1.1 201 Created");
						} else {
							/*
							an error occurred while inserting the order
							*/
							/*
							call cancelOrder action to kill the shopping cart session
							*/
							$dataAPI->cancelOrder($shoppingCartSessionRef);
							$this->setWebhookError('Failed to insert order ' . $result);
							header("HTTP/1.1 400");
						}
					}
					else
					{
						header("HTTP/1.1 400");
						$this->setWebhookError('Line quantity mismatch');
					}
				} else {
					/*
					an error occurred while requesting the order data
					*/

					/*
					call cancelOrder action to kill the shopping cart session
					*/
					$dataAPI->cancelOrder($shoppingCartSessionRef);
					$this->setWebhookError('Unable to get project data ' . implode(',', $orderedProjectRefList));

					header("HTTP/1.1 400");
				}

				/*
            	call endSession action to end the api session
            	*/
				$dataAPI->endSession();
			} else {

				$this->setWebhookError('Unable to authenticate');
				//authentication result error occured;
				header("HTTP/1.1 400");
			}
		} else {
			// order does not contain any Taopix projects so we can ignore the webhook
		}
	}



	/**
	 * Record Webhook data
	 *
	 * @param string $pConnectorType connector type
	 * @param string $pTopic webhook topic
	 * @param Array $pWebhookData containing the webhook payload data
	 * @param string $pOrderNumber if applicable the order number
	 * @return array Result of sql update
	 */
	private function recordWebhookData(string $pConnectorType, string $pTopic, array $pWebhookData, string $pOrderNumber = '0'): array
	{
		$db = $this->getUtils()->getGlobalDBConnection();

		$result = '';
		$resultParam = '';
		$resultArray = array('result' => '', 'resultparam' => '', 'webhookid' => 0);

		$webhookdata = json_encode($pWebhookData, JSON_NUMERIC_CHECK);

		$webhookdataLength = strlen($webhookdata);
		if ($webhookdataLength > 15728640) {
			$webhookdata = gzcompress($webhookdata, 9);
		} else {
			$webhookdataLength = 0;
		}

		if ($db) {
			$stmt = $db->prepare('	INSERT INTO `CONNECTORSWEBHOOKDATA`
									(	`connectortype`
										,`webhooktopic`
										,`ordernumber`
										,`data`
										,`datalength`
									)
									VALUES
									(
										?, ?, ?, ?, ?
									)
								');

			if ($stmt) {
				if ($stmt->bind_param(
					'sssss',
					$pConnectorType, $pTopic, $pOrderNumber, $webhookdata, $webhookdataLength
				)) {
						if (!$stmt->execute()) {
							$result = 'str_DatabaseError';
							$resultParam = 'recordWebhookData execute ' . $db->error;
						} else {
							$result = 'success';
							$resultArray['webhookid'] = $db->insert_id;
						}
				} else {

					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'recordWebhookData bind ' . $db->error;
				}
				if ($stmt) {
					$stmt->free_result();
					$stmt->close();
				}
			} else {
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'recordWebhookData prepare ' . $db->error;
			}

			$db->close();
		} else {
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'recordWebhookData connect ' . $db->error;
		}

		$resultArray['result'] = $result;
		$resultArray['resultParam'] = $resultParam;

		return $resultArray;
	}

	/**
	 * Validate the orders paid array dereived from a JSON schema to check it has all of the required array keys
	 * @param Array $pDataArray the array recieved derived from a JSON decoded packet
	 * @param Array $pRequired webhook topic
	 * @return bool True if all required array keys are present
	 */

	private function validatePayload(array $pDataArray, array $pRequired): bool
	{
		$valid = true;

		foreach ($pRequired as $key => $value)
		{
			// check to see if the required key exists in the recieved data
			if (!isset($pDataArray[$key]))
			{
				$this->setWebhookError($key . ' does not exist ');
				$valid = false;
				break;
			}

			if (is_array($pDataArray[$key]))
			{
				// check each line item to make sure each one has the correct array key structure
				if ($key == 'line_items')
				{
					foreach ($pDataArray[$key] as $lineNumber => $lineItem)
					{
						if(!$this->validatePayload($lineItem, $pRequired['line_items'][0]))
						{
							$valid = false;
							break 2;
						}
					}
				}
				else
				{
					if (! $this->validatePayload($pDataArray[$key], $value))
					{
						$valid = false;
						break;
					}
				}
			}
			else
			{
				// check to make sure each required key has a value
				if (trim($pDataArray[$key]) == '')
				{
					$this->setWebhookError($key . ' is empty');
					$valid = false;
					break;
				}
			}
		}
		return $valid;
	}

	/**
	 * Creates a single voucher in Taopix.
	 *
	 * @param array $pPayloadArray json decoded data from the createVoucher webhook
	 * @return void
	 */
	public function createVoucher(array $pPayloadArray): void
	{
		$voucher = new Voucher($this->getUtils());

		$createVoucherResult = $voucher->createVoucher($pPayloadArray);

		if ($createVoucherResult['error'] == '')
		{
			$this->outputJSONData(TPX_HTTP_RESPONSE_STATUSCODE_CREATED, $createVoucherResult);
		}
		else
		{
			$this->outputJSONData('400', $createVoucherResult);
		}
	}

	/**
	 * Updates a single voucher in Taopix based off the voucher code.
	 *
	 * @param array $pPayloadArray json decoded data from the updateVoucher webhook
	 * @return void
	 */
	public function updateVoucher(array $pPayloadArray): void
	{
		$voucher = new Voucher($this->getUtils());

		$updateVoucherResult = $voucher->updateVoucher($pPayloadArray);

		if ($updateVoucherResult['error'] == '')
		{
			$this->outputJSONData(TPX_HTTP_RESPONSE_STATUSCODE_OK, $updateVoucherResult);
		}
		else
		{
			$this->outputJSONData(TPX_HTTP_RESPONSE_STATUSCODE_BADREQUEST, $updateVoucherResult);
		}
	}

	/**
	 * Deletes a single voucher in Taopix based off the voucher code.
	 *
	 * @param array $pPayloadArray json decoded data from the deleteVoucher webhook
	 * @return void
	 */
	public function deleteVoucher(array $pPayloadArray): void
	{
		$voucher = new Voucher($this->getUtils());

		$deleteVoucherResult = $voucher->deleteVoucher($pPayloadArray);

		if ($deleteVoucherResult['error'] == '')
		{
			$this->outputJSONData(TPX_HTTP_RESPONSE_STATUSCODE_OK, $deleteVoucherResult);
		}
		else
		{
			$this->outputJSONData(TPX_HTTP_RESPONSE_STATUSCODE_BADREQUEST, $deleteVoucherResult);
		}
	}

    public function createUserAccountFromWebView(array $pPayloadArray): void
    {
        $utils = $this->getUtils();
        $licenseKey = $utils->getLicenseKeyFromCode($pPayloadArray['groupcode']);

        // Set the customer ID as the hashed version.
        $customerAccount = [];
        $customerAccount['addressupdated'] = TPX_UPDATEADDRESSMODE_FULL;
        $customerAccount['sendmarketinginfo'] = (!is_null($pPayloadArray['accountdetails']['sendmarketinginfo'])) ? $pPayloadArray['accountdetails']['sendmarketinginfo'] : 0;
        $customerAccount['basketapiworkflowtype'] = TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI;
        $customerAccount['webbrandcode'] = $licenseKey['webbrandcode'];
        $customerAccount['licensekeydata']['groupcode'] = $licenseKey['groupcode'];

        $pPayloadArray['accountdetails']['countrycode'] = '';
        $pPayloadArray['accountdetails']['format'] = TPX_PASSWORDFORMAT_CLEARTEXT;

        $customerAccount['accountdetails'] = $pPayloadArray['accountdetails'];

        $userAccountResult = $utils->createNewAccount($customerAccount);

        if ($userAccountResult['result'] == '')
        {
            $loginParam = [];
            $loginParam['groupcode'] = $licenseKey['groupcode'];
            $loginParam['login'] =  $customerAccount['accountdetails']['login'];
            $loginParam['password'] = $customerAccount['accountdetails']['password'];

            $this->processLoginFromWebView($loginParam);
        }
        else
        {
            $result = ['resultmessage' => $this->translateCCErrorMessage($userAccountResult, $licenseKey['webbrandcode'])];
            $this->outputJSONData(TPX_HTTP_RESPONSE_STATUSCODE_BADREQUEST, $result);
        }
    }

    public function processLoginFromWebView(array $pPayloadArray): void
    {
        $utils = $this->getUtils();
        $licenseKey = $utils->getLicenseKeyFromCode($pPayloadArray['groupcode']);

        // Set the customer ID as the hashed version.
        $processLoginParameters = array(
            'login' => $pPayloadArray['login'],
            'password' =>  $pPayloadArray['password'],
            'format' => TPX_PASSWORDFORMAT_CLEARTEXT,
            'webbrandcode' => $licenseKey['webbrandcode'],
            'groupcode' => $licenseKey['groupcode'],
            'basketapiworkflowtype' => TPX_BASKETWORKFLOWTYPE_LOWLEVELAPI,
            'basketref' =>'',
            'reauthenticate' => '0',
            'ipaddress' => $utils->getClientIPAddress()
        );

        $processLoginResult = $utils->processLogin($processLoginParameters);

        if ($processLoginResult['result'] == '')
        {
            $ssoKey = $processLoginResult['ssokey'];

            if ($ssoKey == '') {

                $privateData['ssotoken'] = $processLoginResult['ssotoken'];
                $privateData['ssoprivatedata'] = $processLoginResult['ssoprivatedata'];
                $authenticationInsertArray = $utils->createDataStoreRecord($privateData,'','',TPX_AUTHENTICATIONTYPE_LOWLEVEL, TPX_USER_AUTH_REASON_WEBVIEW_SSO, $processLoginResult['useraccountid'], true);
                $ssoKey = $authenticationInsertArray['authkey'];
            }

            $this->outputJSONData(TPX_HTTP_RESPONSE_STATUSCODE_OK, ['authkey' => $ssoKey, 'username'=> $processLoginResult['username']]);
        }
        else
        {
            $result = ['resultmessage' => $this->translateCCErrorMessage($processLoginResult, $licenseKey['webbrandcode'])];
            $this->outputJSONData(TPX_HTTP_RESPONSE_STATUSCODE_BADREQUEST, $result);
        }
    }

    public function processForgotPasswordFromWebView(array $pPayloadArray): void
    {
        $utils = $this->getUtils();
        $licenseKey = $utils->getLicenseKeyFromCode($pPayloadArray['groupcode']);

        $processRestPasswordResult = $utils->resetPasswordRequest($licenseKey['webbrandcode'], $pPayloadArray['login'], TPX_PASSWORDFORMAT_CLEARTEXT);

        if ($processRestPasswordResult['result'] == '')
        {
            $response['resetpasswordauthcode'] = $processRestPasswordResult['resetpasswordauthcode'];
            $response['redirecturl'] = $processRestPasswordResult['redirecturl'];

            $this->outputJSONData(TPX_HTTP_RESPONSE_STATUSCODE_OK, $response);
        }
        else
        {
            $result = ['resultmessage' => $this->translateCCErrorMessage($processRestPasswordResult, $licenseKey['webbrandcode'])];
            $this->outputJSONData(TPX_HTTP_RESPONSE_STATUSCODE_BADREQUEST, $result);
        }
    }

    public function translateCCErrorMessage(array $pResultArray, string $pWebBrandCode): string
    {
        $utils = $this->getUtils();

        $error = $pResultArray['result'];

        if (substr($error, 0, 4) == 'str_')
        {
            $smarty = $utils->newSmartyObj('Login', $pWebBrandCode, '', $utils->getBrowserLocale());
            $error = $smarty->get_config_vars($error);
        }

        return $error;
    }
}
