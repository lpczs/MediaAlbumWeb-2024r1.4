<?php

class TaopixOAuthRefreshToken
{
	private $tableName = 'oauthrefreshtoken';
	private $dbObj = null;

	public function __construct($dbObj) {
		$this->dbObj = $dbObj;
	}

	public function findByParams(array $criteria)
	{
		$existingToken = DatabaseObj::getDataFromTable(['id', 'refreshtoken'], $this->tableName, $this->dbObj, '', true, $criteria);

		if ('' !== $existingToken['error']) {
			return $existingToken;
		}

		if (!empty($existingToken['data'])) {
			return $existingToken['data'][0];
		}

		return [];
	}

	public function insert(array $details)
	{
		$query = "INSERT INTO `" . $this->tableName ."` (`id`, `providerid`, `authemail`, `refreshtoken`, `datecreated`) VALUES (null, ?, ?, ?, ?)";
		$tokenId = -1;

		try {
			$details['refreshtoken'] = base64_encode($details['refreshtoken']);
			$stmt = $this->dbObj->prepare($query);
			if (!$stmt) {
				throw new Exception(__METHOD__ . ' prepare ' . $this->dbObj->error);
			}
			$dateCreated = date('Y-m-d H:i:s', time());
			if (!$stmt->bind_param('ssss', $details['providerid'], $details['authemail'], $details['refreshtoken'], $dateCreated)) {
				throw new Exception(__METHOD__ . ' bind params ' . $this->dbObj->error);
			}

			if (!$stmt->execute()) {
				throw new Exception(__METHOD__ . ' execute ' . $this->dbObj->error);
			}
			$tokenId = $this->dbObj->insert_id;
		} catch (Exception $ex) {
			error_log('got an error here: ' . $ex->getMessage());
			return [
				'error' => 'str_DatabaseError',
				'errorparam' => $ex->getMessage(),
			];
		}

		return [
			'id' => $tokenId,
			'refreshtoken' => $details['refreshtoken'],
		];
	}

	public function clearExistingTokens(array $details)
	{
		$query = "DELETE FROM `" . $this->tableName ."` WHERE `providerid` = ? AND `authemail` = ?";

		try {
			$stmt = $this->dbObj->prepare($query);
			if (!$stmt) {
				throw new Exception(__METHOD__ . ' prepare ' . $this->dbObj->error);
			}

			if (!$stmt->bind_param('ss', $details['providerid'], $details['authemail'])) {
				throw new Exception(__METHOD__ . ' bind params ' . $this->dbObj->error);
			}

			if (!$stmt->execute()) {
				throw new Exception(__METHOD__ . ' execute ' . $this->dbObj->error);
			}

		} catch (Exception $ex) {
			return [
				'error' => 'str_DatabaseError',
				'errorparam' => $ex->getMessage(),
			];
		}

		return true;
	}

	public function getCount(array $criteria)
	{
		$details = DatabaseObj::getDataFromTable(['id'], $this->tableName, $this->dbObj, '', true, $criteria);

		if ('' !== $details['error']) {
			return null;
		}

		return count($details['data']);
	}
}