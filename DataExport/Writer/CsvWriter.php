<?php

namespace DataExport\Writer;

use Exception;

class CsvWriter extends AbstractExportFileWriter
{
	/**
	 * Get the file extension for this writer format
	 *
	 * @return string
	 */
	public function getExtension()
	{
		return 'csv';
	}

	/**
	 * Append the record to the exported data file
	 *
	 * @param mixed[] $pRecord
	 * @return boolean
	 * @throws Exception
	 */
	public function writeRecord($pRecord)
	{
		return (fputcsv($this->handle, $pRecord) > 0);
	}	
}
