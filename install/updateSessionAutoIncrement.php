<?php

class updateSessionAutoIncrement extends ExternalScript
{
	/**
	 * Performs the update for possibly updating the auto increment value on the sessiondata table.
	 */
	public function run()
	{
		$error = false;

		try {
			// If we are not an upgrade exit out we don't have this issue.
			if ('upgrade' !== $this->mode) {
				$this->setResult('');
				return;
			}

			$maxCreatorBatchRef = $this->getMaxCreatorUploadBatch();

			// Exit out of the process.
			if (0 > $maxCreatorBatchRef) {
				$this->setResult('');
				return;
			}

			$autoIncrement = $this->getCurrentAutoIncrementValue();

			if ($maxCreatorBatchRef > $autoIncrement) {
				$this->updateAutoIncrementValue(($maxCreatorBatchRef + 1));
			}
		} catch (\Exception $ex) {
			// Check what the exception is.
			$error = true;
			$errorParam = 'Auto increment value error: '. $ex->getMessage();
		}

		if (! $error) {
			$this->printMsg('Auto increment value updated');
		}

		$this->setResult($errorParam);
	}

	/**
	 * Returns the max uploadbatchref for desktop orders.
	 *
	 * @return int|void
	 * @throws \Exception
	 */
	private function getMaxCreatorUploadBatch()
	{
		$sql = 'SELECT `uploadbatchref` from `orderitems` where length(`uploadbatchref`) <10 order by CAST(`uploadbatchref` AS unsigned) desc limit 1';
		return $this->runSql($sql, [], true);
	}

	/**
	 * Gets the autoincrement value for the sessiondata table.
	 *
	 * @return int|void
	 * @throws \Exception
	 */
	private function getCurrentAutoIncrementValue()
	{
		$sql = 'SELECT `AUTO_INCREMENT` FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = ? AND `TABLE_NAME` = ?';
		return $this->runSql($sql, [$this->config['DBNAME'], 'SESSIONDATA'], true);
	}

	/**
	 * Updates the sessiondata table and increases the autoincrement value
	 * @param $newValue
	 * @throws \Exception
	 */
	private function updateAutoIncrementValue($newValue)
	{
		$sql = 'ALTER TABLE `' . $this->config['DBNAME'] . '`.`SESSIONDATA` AUTO_INCREMENT = ' . $newValue;

		$this->runSql($sql, [], false);
	}

	/**
	 * Performs a query.
	 *
	 * @param $query Query to run
	 * @param $params Parameters used in the query
	 * @param bool $returnResult If we want the result back.
	 * @return int|void
	 * @throws \Exception
	 */
	private function runSql($query, $params, $returnResult = true)
	{
		$bindValue = -1;
		$stmt = $this->dbConnection->prepare($query);

		if (false === $stmt) {
			throw new \Exception('Prepare error ' . $this->dbConnection->error);
		}

		if (! empty($params)) {
			if (false === $stmt->bind_param(str_repeat('s', count($params)), ...$params)) {
				throw new \Exception('Bind param ' . $this->dbConnection->error);
			}
		}

		if (false === $stmt->execute()) {
			throw new \Exception('Execute error ' . $this->dbConnection->error);
		}

		// Exit out if we are not bothered about any return values.
		if (false === $returnResult) {
			return;
		}

		if (false === $stmt->store_result()) {
			throw new \Exception('Store result ' . $this->dbConnection->error);
		}

		if (false === $stmt->bind_result($bindValue)) {
			throw new \Exception('Bind result ' . $this->dbConnection->error);
		}

		if (false === $stmt->fetch()) {
			throw new \Exception('Fetch error ' . $this->dbConnection->error);
		}

		return $bindValue;
	}

	/**
	 * prints a message to the screen.
	 *
	 * @param string $pMsg The message text.
	 */
	private function printMsg($pMsg)
	{
		echo $pMsg . PHP_EOL;
	}
}