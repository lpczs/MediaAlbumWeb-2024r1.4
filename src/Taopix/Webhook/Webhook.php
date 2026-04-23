<?php

namespace Taopix\Webhook;

use Taopix\Core\Utils\TaopixUtils;

class Webhook {

	private $webhookURL = '';
	private $topic = '';
	private $curl = null;
	private $acConfig = [];
	private $utils;
	private $webhookData = [];
	private $connectorType = '';
	private $orderNumber = 0;

	/**
	 * Sets the webhookurl
	 *
	 * @param string $pWebhookURL the webhook url to post to
	 * @return Webhook instance.
	 */
	public function setWebhookURL(string $pWebhookURL): Webhook
	{
		$this->webhookURL = $pWebhookURL;
		return $this;
	}

	/**
	 * Returns the webhook url
	 *
	 * @return string webhookurl
	 */
	public function getWebhookURL(): string
	{
		return $this->webhookURL;
	}

	/**
	 * Sets the webhook topic
	 *
	 * @param string $pTopic the webhook topic
	 * @return Webhook instance.
	 */
	public function setTopic(string $pTopic): Webhook
	{
		$this->topic = $pTopic;
		return $this;
	}

	/**
	 * Returns the webhook topic
	 *
	 * @return string webhook topic
	 */
	public function getTopic(): string
	{
		return $this->topic;
	}

	/**
	 * Sets the TaopixUtils instance
	 *
	 * @param TaopixUtils $pUtils TaopixUtils instance to set
	 * @return Webhook Webhook instance.
	 */
	public function setUtils(TaopixUtils $pUtils): Webhook
	{
		$this->utils = $pUtils;
		return $this;
	}

	/**
	 * Returns the TaopixUtils instance.
	 *
	 * @return TaopixUtils instance.
	 */
	public function getUtils(): TaopixUtils
	{
		return $this->utils;
	}

	/**
	 * Sets the AC Config instance
	 *
	 * @param array $pACConfig AC Config to set
	 * @return Webhook Webhook instance.
	 */
	public function setACConfig(array $pACConfig): Webhook
	{
		$this->acConfig = $pACConfig;
		return $this;
	}

	/**
	 * Returns the Ac Config
	 *
	 * @return array system config.
	 */
	public function getACConfig(): array
	{
		return $this->acConfig;
	}

	/**
	 * Sets the curl instance for the webhook
	 *
	 * @return Webhook  instance.
	 */
	public function setCURL(): Webhook
	{
		$curl = curl_init();

		// Common settings.
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_USERAGENT, \UtilsObj::getArrayParam($_SERVER, 'HTTP_USER_AGENT'));
		curl_setopt($curl, CURLOPT_CAINFO, \UtilsObj::getCurlPEMFilePath());
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

		$this->curl = $curl;
		return $this;
	}

	/**
	 * Returns the curl instance
	 *
	 * @return  curl
	 */
	public function getCURL()
	{
		return $this->curl;
	}

	/**
	 * Sets the webhook connector type
	 *
	 * @param string $pConnectorType the connector that the webhook relates to
	 * @return Webhook instance.
	 */
	public function setConnectorType(string $pConnectorType) : Webhook
	{
		$this->connectorType = $pConnectorType;
		return $this;
	}

	/**
	 * Returns the connectortype
	 *
	 * @return string connectortype
	 */
	public function getConnectorType() : string
	{
		return $this->connectorType;
	}

	/**
	 * Sets the webhook data for post
	 *
	 * @param array $pWebHookData the data to be used with the webhook
	 * @return Webhook instance.
	 */
	public function setwebhookData(array $pWebHookData) : Webhook
	{
		$this->webhookData = $pWebHookData;
		return $this;
	}

	/**
	 * Returns the webhookdata
	 *
	 * @return array webhookdata
	 */
	public function getwebhookData() : array
	{
		return $this->webhookData;
	}

	/**
	 * Sets the order number for any order related webhooks
	 *
	 * @param $pOrderNumber the order number related to the webhook
	 * @return Webhook  instance.
	 */
	public function setOrderNumber($pOrderNumber) : Webhook
	{
		$this->orderNumber = $pOrderNumber;
		return $this;
	}

	/**
	 * Returns the ordernumber
	 *
	 * @return ordernumber
	 */
	public function getOrderNumber()
	{
		return $this->orderNumber;
	}

	public function __construct(string $pConnectorType, string $pTopic, array $pWebHookData)
	{
		$this
			->setUtils(new TaopixUtils())
			->setCURL()
			->setConnectorType($pConnectorType)
			->setTopic(strtolower($pTopic))
			->setwebhookData($pWebHookData)
			->setACConfig($this->getUtils()->getACConfig());
	}

	public function __destruct()
	{
		curl_close($this->curl);
	}

	/**
	 * Builds the hash string to authenicate requests.
	 *
	 * @param array $pPayload Data to use to build the hash string.
	 * @return string The generated hash string.
	 */
	private function generateHMAC(array $pPayload): string
	{
		return base64_encode(hash_hmac('sha256', $this->toJSON($pPayload), $this->getACConfig()['TAOPIXCONNECTORWEBHOOKSECRET'], true));
	}

	/**
	 * Builds the json string from the webhook data.
	 *
	 * @param array $pData Data to use to build the json string.
	 * @return string The generated json string.
	 */
	public function toJSON(array $pData)
	{
		return json_encode($pData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}

	/**
	 * Will execute a POST to the webhook url witht the webhhok data.
	 * Required Taopix headers are also created and sent.
	 *
	 * @return array The curl response.
	 */
	public function post(): array
	{
		return $this->executeCURL($this->getWebhookURL(), [
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $this->toJSON($this->getwebhookData())
		], [
			'X-TAOPIX-SIGNATURE: ' .  $this->generateHMAC($this->getwebhookData()),
			'X-TAOPIX-TOPIC: ' . $this->getTopic(),
			'Content-Type: application/json'
		]);
	}

	/**
	 * Will execute a curl request for the webhook.
	 *
	 * @param array $pURL webhook url to post to.
	 * @param array $pCurlOptions curl options including post fields.
	 * @param array $pHeaders json header including required Taopix headers.
	 * @return array The curl response.
	 */
	protected function executeCURL(string $pURL, array $pCurlOptions, array $pHeaders = []): array
	{
		$curl = $this->getCURL();

		curl_setopt($curl, CURLOPT_URL, $pURL);

		// Set options passed to the function.
		curl_setopt_array($curl, $pCurlOptions);

		// Set headers.
		curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge($pHeaders));

		$response = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		$responseObj = json_decode($response);

		return [$httpCode, $responseObj];
	}

	/**
	 * Record Webhook data
	 *
	 * @return array Result of sql update
	 */
	public function recordWebhookData(): array
	{
		$db = $this->getUtils()->getGlobalDBConnection();

		$result = '';
		$resultParam = '';
		$recordID = 0;
		$resultArray = array();
		$connectorType = $this->getConnectorType();
		$topic = $this->getTopic();
		$orderNumber = $this->getOrderNumber();
		$webhookdata = json_encode($this->getWebhookData());

		$webhookdataLength = strlen($webhookdata);
		if ($webhookdataLength > 15728640) {
			$webhookdata = gzcompress($webhookdata, 9);
		} else {
			$webhookdataLength = 0;
		}

		if ($db) {
			$stmt = $db->prepare('INSERT INTO `CONNECTORSWEBHOOKDATA`
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
					'ssssi',
					$connectorType, $topic, $orderNumber, $webhookdata, $webhookdataLength
				)) {
						if (!$stmt->execute()) {
							$result = 'str_DatabaseError';
							$resultParam = 'recordWebhookData execute ' . $db->error;
						} else {
							$recordID = $db->insert_id;
							$result = 'success';
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
		$resultArray['id'] = $recordID;

		return $resultArray;
	}

	/**
	 * Creates the Webhook
	 *
	 * @return array Result of sql update
	 */
	public function loadWebhook(int $pWebhookID, string $pWebhookURL): array
	{
		$db = $this->getUtils()->getGlobalDBConnection();

		$result = '';
		$resultParam = '';
		$resultArray = array();
		$id = 0;
		$dateCreated = '';
		$connectorType = '';
		$webhookTopic = '';
		$orderNumber = '';
		$webhookData = '';
		$webhookDataLength = 0;

		if ($db)
		{
			$stmt = $db->prepare('SELECT * FROM `CONNECTORSWEBHOOKDATA` WHERE `id` = ?');

			if ($stmt)
			{
				// bind params
				if ($stmt->bind_param('i', $pWebhookID))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0)
							{
								if ($stmt->bind_result($id, $dateCreated, $connectorType, $webhookTopic, $orderNumber, $webhookData, $webhookDataLength))
								{
									if ($stmt->fetch())
									{
										if ($webhookDataLength > 0)
										{
											$webHookData = gzuncompress($webhookData, $webhookDataLength);
										}

										$webHookData = json_decode($webhookData, true);
										$this
											->setConnectorType($connectorType)
											->setTopic($webhookTopic)
											->setOrderNumber($orderNumber)
											->setwebhookData($webHookData)
											->setWebhookURL($pWebhookURL);
									}
								}
								else
								{
									$returnArray['error'] = __FUNCTION__ . ' bind result error: ' . $db->error;
								}
							}
						}
						else
						{
							$resultArray['result'] = __FUNCTION__ . ' store result ' . $db->error;
						}

						$stmt->free_result();
						$stmt->close();
						$stmt = null;
					}
					else
					{
						$resultArray['result'] = __FUNCTION__ . ' execute  ' . $db->error;
					}
				}
				else
				{
					$resultArray['result'] = __FUNCTION__ . ' bind params  ' . $db->error;
				}
			}
			else
			{
				$resultArray['result'] = __FUNCTION__ . ' prepare  ' . $db->error;
			}
			$db->close();
		}
		else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = __FUNCTION__ . ' connect ' . $db->error;
		}

		$resultArray['result'] = $result;
		$resultArray['resultParam'] = $resultParam;

		return $resultArray;
	}

	/**
	 * Remove successfully played webhook records
	 *
	 * @param array $pWebhookRecordIDArray containing the id's of webhooks that were successfully played.
	 * @return void
	 */
	public function deleteSuccessfulWebhookRecords(array $pWebhookRecordIDArray): void
	{
		$db = $this->getUtils()->getGlobalDBConnection();

		$result = '';
		$resultParam = '';
		$resultArray = array();

		if ($db)
        {
			if ($stmt = $db->prepare("DELETE FROM `CONNECTORSWEBHOOKDATA` WHERE `id` in ( " . implode(',', $pWebhookRecordIDArray) . " )"))
			{
				if (!$stmt->execute())
				{
					// could not execute statement
					$result = 'str_DatabaseError';
					$resultParam = __FUNCTION__ . ' execute: ' . $db->error;
				}

				$stmt->close();
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = __FUNCTION__ . ' prepare: ' . $db->error;
			}

            $db->close();
        }

		$resultArray['result'] = $result;
		$resultArray['resultParam'] = $resultParam;
	}
}
