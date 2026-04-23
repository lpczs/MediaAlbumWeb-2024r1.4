<?php

use League\OAuth2\Client\Provider\GenericProvider;
use TheNetworg\OAuth2\Client\Provider\Azure;

class TaopixOAuthProvider
{
	// Id of the auth provider.
	private int $providerId = 0;

	private $provider = null;

	// Database connection object.
	private $dbObj = null;

	private $data = [];

	public function __construct($providerId, $dbObj, array $options = [])
	{
		$this->providerId = $providerId;
		$this->dbObj = $dbObj;

		$this->populateDetails();

		if (!empty($this->data)) {
			$options = array_merge($options, [
				'clientId' => $this->data['clientid'],
				'clientSecret' => base64_decode($this->data['clientsecret']),
				'accessType' => 'offline',
				'scopes' => explode(',', $this->data['scopes'])
			]);

			if (GenericProvider::class === ltrim($this->data['provider'], " \t\n\r\0\x0B\\")) {
				$options['urlAuthorize'] = $this->data['authurl'];
				$options['urlAccessToken'] = $this->data['tokenurl'];
				$options['urlResourceOwnerDetails'] = $this->data['ownerurl'];
			} else if (Azure::class === ltrim($this->data['provider'], " \t\n\r\0\x0B\\")) {
				$options['defaultEndPointVersion'] = '2.0';
			}

			$this->provider = new $this->data['provider']($options, []);

			// Tenant id for azure based systems may need to be specified if the app is not a multitenant app.
			if (Azure::class === ltrim($this->data['provider'], " \t\n\r\0\x0B\\") && '' !== trim($this->data['tenantid'])) {
				$this->provider->tenant = $this->data['tenantid'];
			}
		}
	}

	public function getValue($key, $default = '')
	{
		return $this->data[$key] ?? $default;
	}

	private function populateDetails()
	{
		// No provider id supplied so just return.
		if (0 === $this->providerId) {
			return;
		}

		$provider = DatabaseObj::getDataFromTable([
			'id',
			'providername',
			'scopes',
			'clientid',
			'clientsecret',
			'provider',
			'authurl',
			'tokenurl',
			'ownerurl',
			'tenantid',
		], 'oauthprovider', $this->dbObj, '', true, ['id' => $this->providerId]);

		if ('' !== $provider['error']) {
			return;
		}

		$this->data = $provider['data'][0];
	}

	public function getData()
	{
		$returnInfo = $this->data;

		// If the data property is empty just return now no extra processing is needed.
		if (empty($this->data)) {
			return [];
		}

		$returnInfo['provider'] = ltrim($returnInfo['provider'], " \t\n\r\0\x0B\\");
		return $returnInfo;
	}

	public function setData(array $data)
	{
		$this->data = $data;
		return $this;
	}

	public function getProvider()
	{
		return $this->provider;
	}

	public function save()
	{
		if (0 === $this->data['id']) {
			return $this->insertProvider();
		}

		return $this->updateProvider();
	}

	public function listAll()
	{
		$query = "SELECT `id`, `providername`, `provider` FROM `oauthprovider`";
		$resultSet = [];
		$id = 0;
		$providerName = '';
		$provider = '';

		try {
			$stmt = $this->dbObj->prepare($query);
			if (!$stmt) {
				throw new Exception(__METHOD__ . ': prepare ' . $this->dbObj->error);
			}

			if (!$stmt->bind_result($id, $providerName, $provider)) {
				throw new Exception(__METHOD__ . ': bind result ' . $this->dbObj->error);
			}

			if (!$stmt->execute()) {
				throw new Exception(__METHOD__ . ': execute ' . $this->dbObj->error);
			}

			while ($stmt->fetch()) {
				$resultSet[] = [
					$id,
					$providerName,
					ltrim($provider, " \t\n\r\0\x0B\\"),
				];
			}

			$stmt->free_result();
			$stmt->close();
			$stmt = null;

		} catch (Exception $exception) {
			return [
				'error' => 'str_DatabaseError',
				'errorparam' => $exception->getMessage(),
			];
		}

		return [
			'error' => '',
			'errorparam' => '',
			'data' => $resultSet,
		];
	}

	public function deleteMultiple(array $providerIds)
	{
		// No ids are passed so just say everything was fine.
		if (empty($providerIds)) {
			return [
				'success' => true,
				'msg' => '',
			];
		}

		$deleteCount = count($providerIds);
		$query = "DELETE FROM `oauthprovider` WHERE id IN (". implode(',', array_fill(0, $deleteCount, '?')) . ")";

		try {
			$stmt = $this->dbObj->prepare($query);
			if (!$stmt) {
				throw new Exception(__METHOD__ . ': prepare ' . $this->dbObj->error);
			}

			if (!$stmt->bind_param(str_repeat('i', $deleteCount), ...$providerIds)) {
				throw new Exception(__METHOD__ . ': bind param ' . $this->dbObj->error);
			}

			if (!$stmt->execute()) {
				throw new Exception(__METHOD__ . ': execute ' . $this->dbObj->error);
			}
		} catch (Exception $exception) {
			return [
				'success' => false,
				'msg' => 'str_DatabaseError',
				'errorparam' => $exception->getMessage(),
			];
		}

		return [
			'success' => true,
			'msg' => '',
		];
	}

	public function delete(?int $providerId = null)
	{
		// If the passed provider id is null try deleting the provider that we currently have instanced.
		if (null === $providerId) {
			$providerId = $this->data['id'];
		}

		$query = "DELETE FROM `oauthprovider` WHERE `id` = ?";

		try {
			$stmt = $this->dbObj->prepare($query);
			if (!$stmt) {
				throw new Exception(__METHOD__ . ': prepare ' . $this->dbObj->error);
			}

			if (!$stmt->bind_param('i', $providerId)) {
				throw new Exception(__METHOD__ . ': bind ' . $this->dbObj->error);
			}

			if (!$stmt->execute()) {
				throw new Exception(__METHOD__ . ': execute ' . $this->dbObj->error);
			}
		} catch (Exception $exception) {
			return [
				'success' => false,
				'msg' => 'str_DatabaseError',
				'errorparam' => $exception->getMessage(),
			];
		}

		return [
			'success' => true,
			'msg' => '',
		];
	}

	public function assigedProviders()
	{
		$assignedProviderData = DatabaseObj::getDataFromTable([
			'oauthprovider',
		], 'branding', $this->dbObj, '', true, []);

		if ('' !== $assignedProviderData['error']) {
			return [
				'error' => $assignedProviderData['error'],
			];
		}
		$assignedProviders = [];

		foreach ($assignedProviderData['data'] as $key => $providerAssigned) {
			$assignedProviders[] = $providerAssigned['oauthprovider'];
		}

		return array_unique($assignedProviders);
	}

	private function insertProvider()
	{
		$dateTime = new DateTime('now', new DateTimeZone('UTC'));
		$this->data['datecreated'] = $dateTime->format('Y-m-d H:i:s');
		$dataFields = count($this->data);
		$query = "INSERT INTO `oauthprovider` (`" . join('`,`', array_keys($this->data)) . "`) VALUES (" . join(',', array_fill(0, $dataFields, '?')) . ")";

		try {
			$stmt = $this->dbObj->prepare($query);
			if (!$stmt) {
				throw new Exception(__METHOD__ . ': prepare ' . $this->dbObj->error);
			}

			$valueArray = array_values($this->data);
			if (!$stmt->bind_param(str_repeat('s', $dataFields), ...$valueArray)) {
				throw new Exception(__METHOD__ . ': bind ' . $this->dbObj->error);
			}

			if (!$stmt->execute()) {
				throw new Exception(__METHOD__ . ': Execute ' . $this->dbObj->error);
			}
			$this->data['id'] = $this->dbObj->insert_id;
		} catch (Exception $exception) {
			return [
				'success' => false,
				'msg' => 'str_DatabaseError',
				'errorparam' => $exception->getMessage(),
			];
		}

		return [
			'success' => true,
			'data' => $this->data,
		];
	}

	private function updateProvider()
	{
		$updateCols = array_map(function($field) {
			return "`{$field}` = ?";
		}, array_keys($this->data));
		$fieldCount = count($this->data);
		$where = [
			'id = ?'
		];
		$query = "UPDATE `oauthprovider` SET " . join(', ', $updateCols) . ' WHERE ' . join(' AND ', $where);

		try {
			$stmt = $this->dbObj->prepare($query);
			if (!$stmt) {
				throw new Exception(__METHOD__ . ': prepare ' . $this->dbObj->error . " ({$query})");
			}

			$valueArray = array_values($this->data);
			$valueArray[] = $this->data['id'];
			if (!$stmt->bind_param(str_repeat('s', $fieldCount) . 'i', ...$valueArray)) {
				throw new Exception(__METHOD__ . ': bind ' . $this->dbObj->error);
			}

			if (!$stmt->execute()) {
				throw new Exception(__METHOD__ . ': execute ' . $this->dbObj->error);
			}
		} catch (Exception $exception) {
			return [
				'success' => false,
				'msg' => 'str_DatabaseError',
				'errorparam' => $exception->getMessage(),
			];
		}

		return [
			'success' => true,
			'msg' => '',
			'errorparam' => '',
			'data' => $this->data,
		];
	}
}