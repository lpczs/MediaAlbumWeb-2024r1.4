<?php

namespace DataExport\Writer;

use Exception;

interface WriterInterface
{
	/**
	 * Get the export file name
	 *
	 * @return string
	 */
	public function getFilename();

	/**
	 * Append the record to the exported data file
	 *
	 * @param mixed[] $record
	 */
	public function write(array $record);

	/**
	 * Finish the file by appending any trailing data needed
	 * and close the file.
	 *
	 * @throws Exception
	 */
	public function finalise();
}
