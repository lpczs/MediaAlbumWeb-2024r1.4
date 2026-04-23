<?php

namespace Taopix\Core\Outputter;

class OutputterFile implements IOutputter
{
	protected $fileName;

	function __construct($pFileName)
	{
		$this->fileName = $pFileName;
	}

	public function output($pMessage = "", $pNoCarriageReturn = false)
	{
		$dataToWrite = '';

		if (is_array($pMessage))
		{
			$dataToWrite = var_export($pMessage, true);
		}
		else
		{
			$dataToWrite = $pMessage;
		}

		if (!$pNoCarriageReturn)
		{
			$dataToWrite .= PHP_EOL;
		}

		$this->writeDataToDisk($dataToWrite);
	}

	protected function writeDataToDisk($pData)
	{
		file_put_contents($this->fileName, $pData, FILE_APPEND);
	}
}

?>