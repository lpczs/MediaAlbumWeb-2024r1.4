<?php

namespace Taopix\Core\Outputter;

class OutputterLogger extends OutputterFile
{
	protected function writeDataToDisk($pData)
	{
		$date = date("M d H:i:s");
		file_put_contents($this->fileName, $date . " " . $pData, FILE_APPEND);
	}
}

?>