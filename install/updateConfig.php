<?php

class updateConfig extends ExternalScript
{
	/**
	 * Value with maximum brand count set to do a bit shift AND operation on later.
	 * 
	 * @var string
	 */
	static $brandCountAllBits = 16711680;

	/**
	 * Value when all features are enabled to do a bit shift AND operation on later.
	 * 
	 * @var string
	 */
	static $featureAllBits = 65535;

	/**
	 * @inheritDoc
	 */
	public function run()
	{
		if ($this->mode == 'upgrade')
		{
			$error = '';

			// Shift the brand count bits to the new position.
			// We need to work out what brand count bits are set, shift them and then re-add the feature bits.

			$sql = 'UPDATE `systemconfig` SET `config` = 
					(
						(SELECT `config64`
						 FROM 
						 	(SELECT `config`, 
								(? & `config`) `brandcodebits`, 
								(SELECT `brandcodebits` << 32) as `brandcodebitsshifted`,
								(? & `config`) `featurebits`,
								((SELECT `brandcodebitsshifted`) + (SELECT `featurebits`)) `config64`,
								(SELECT `config64`)
							FROM `systemconfig` `sc2`) `config`
						)
					)';

			if (($stmt = $this->dbConnection->prepare($sql)))
			{
				if ($stmt->bind_param('ii', self::$brandCountAllBits, self::$featureAllBits))
				{
					if (! $stmt->execute())
					{
						$error = __METHOD__ . ' execute error: ' . $this->dbConnection->error;
					}
				}
				else
				{
					$error = __METHOD__ . ' bind_param error: ' . $this->dbConnection->error;
				}
			}
			else
			{
				$error = __METHOD__ . ' prepare error: ' . $this->dbConnection->error;
			}

			if ($error !== '')
			{
				$this->printMsg($error);
			}
		}
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
