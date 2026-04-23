<?php

namespace DataExport\Writer;

use Exception;

abstract class AbstractExportFileWriter implements WriterInterface
{
	/**
	 * @var string
	 */
	protected $filename;

	/**
	 * @var resource
	 */
	protected $handle;

	/**
	 * @var string
	 */
	protected $fileSpec;

	/**
	 * @var bool
	 */
	protected $headersWritten = false;

	/**
	 * Get the file extension for this writer format
	 *
	 * @return string
	 */
	abstract public function getExtension();

	/**
	 * Constructor
	 *
	 * @param string $filename
	 * @param DataFileSpec $pFileSpec
	 * @throws Exception
	 */
	public function __construct($filename, $pFileSpec)
	{
		$this->filename = $filename . '.' . $this->getExtension();
		$this->fileSpec = $pFileSpec;
	}

	/**
	 * Abstract method to write the record to the export file.
	 * Must be implemented by the appropriate export writer implementation.
	 *
	 * @param $pRecord
	 * @return boolean
	 */
	public abstract function writeRecord($pRecord);

	/**
	 * openFile
	 *
	 * @throws Exception
	 */
	public function openFile()
	{
		// Open the output file and make an exclusive lock
		$handle = fopen($this->filename, 'x');
		if (false === $handle)
		{
			throw new Exception(sprintf('Failed to open export file "%s" for writing export file data.', $this->filename));
		}

		if (false === flock($handle, LOCK_EX))
		{
			fclose($handle);
			throw new Exception(sprintf('Failed to obtain an exclusive write lock on export file "%s".', $this->filename));
		}

		return $handle;
	}

	/**
	 * Get the export file name
	 *
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}

	/**
	 * Append the header record to the exported data file
	 *
	 * @param mixed[] $headerRecord
	 * @throws Exception
	 */
	public function writeHeaders(array $headerRecord)
	{
		$this->write($headerRecord);
	}

	/**
	 * Append the record to the exported data file
	 *
	 * @param mixed[] $record
	 * @throws Exception
	 */
	public function write(array $record)
	{
		if (null === $this->handle)
		{
			// open the file and get the handle
			$this->handle = $this->openFile();

			if (null === $this->handle) {
				throw new Exception(sprintf('Export file "%s" has been finalised and cannot be written to.',
					$this->filename));
			}
		}

		// Write the headers
		if (!$this->headersWritten) {
			$this->headersWritten = true;
			$this->writeHeaders($this->fileSpec->headers);
		}

		if (false === $this->writeRecord($record))
		{
			throw new Exception(sprintf('Failed to write record to export file "%s".', $this->filename));
		}
	}

	/**
	 * Finish the file by closing it
	 *
	 * @throws Exception
	 */
	public function finalise()
	{
		if ($this->handle) {
			fclose($this->handle);
			$this->handle = null;
		}
	}
}
