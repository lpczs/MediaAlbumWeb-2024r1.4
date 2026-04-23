<?php

namespace DataExport\Writer;

use Exception;

class TsvWriter extends AbstractExportFileWriter
{
	/**
	 * Get the file extension for this writer format
	 *
	 * @return string
	 */
	public function getExtension()
	{
		return 'tsv';
	}

	/**
	 * Append the record to the exported data file
	 *
	 * @param mixed[] $pRecord
	 * @return int|boolean
	 * @throws Exception
	 */
	public function writeRecord($pRecord)
	{
		return (fputcsv($this->handle, $pRecord, "\t") > 0);
	}
}
