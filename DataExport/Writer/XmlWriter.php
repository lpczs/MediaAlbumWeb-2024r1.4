<?php

namespace DataExport\Writer;

use Exception;

class XmlWriter extends AbstractExportFileWriter
{
	/**
	 * @var \XMLWriter
	 */
	private $xmlWriter;

	/**
	 * @var boolean
	 */
	private $beautify;

	/**
	 * Get the file extension for this writer format
	 *
	 * @return string
	 */
	public function getExtension()
	{
		return 'xml';
	}

	/**
	 * Constructor
	 *
	 * @param string $filename
	 * @param DataFileSpec $pFileSpec
	 * @param string $pBeautify
	 * @throws Exception
	 */
	public function __construct($filename, $pFileSpec, $pBeautify)
	{
		parent::__construct($filename, $pFileSpec);
		$this->beautify = $pBeautify;

		// remove the space from the header name and lower case it
		foreach ($this->fileSpec->normalizedHeaders as &$header)
		{
			$header = strtolower(str_replace(' ', '', $header));
		}
	}

	/**
	 * openFile
	 *
	 * @throws Exception
	 */
	public function openFile()
	{
		$handle = parent::openFile();

		$this->xmlWriter = new \XMLWriter();

		if (false === $this->xmlWriter->openURI($this->filename))
		{
			throw new Exception(sprintf('Failed to open export file "%s" for writing export file data.', $this->filename));
		}

		// beautify the xml output
		$this->xmlWriter->setIndent($this->beautify);

		return $handle;
	}

	/**
	 * Append the header record to the exported data file
	 *
	 * @param mixed[] $headerRecord
	 */
	public function writeHeaders(array $headerRecord)
	{
		$this->xmlWriter->startDocument("1.0");
		$this->xmlWriter->startElement($this->fileSpec->rootElementName);
	}

	/**
	 * Append the record to the exported data file
	 *
	 * @param mixed[] $record
	 * @return boolean
	 * @throws Exception
	 */
	public function writeRecord($record)
	{
		$this->xmlWriter->startElement($this->fileSpec->dataElementName);

		foreach ($record as $dataKey => $dataRow)
		{
			$this->xmlWriter->startElement($this->fileSpec->normalizedHeaders[$dataKey]);
			$this->xmlWriter->text($dataRow);
			$this->xmlWriter->endElement();
		}

		$this->xmlWriter->endElement();

		// Flush out to the file
		$this->xmlWriter->flush();

		// Flush doesn't always return bytes written, as it could have flushed between the last record and
		// the flush call so there's no bytes to write. All we can do here is return true in light that
		// nothing went wrong.
		return true;
	}

	/**
	 * Finish the file by appending any trailing data needed
	 * and close the file.
	 *
	 * @throws Exception
	 */
	public function finalise()
	{
		if ($this->handle) {
			$this->xmlWriter->endElement();
			$this->xmlWriter->endDocument();
			$this->xmlWriter->flush();

			$this->xmlWriter = null;

			parent::finalise();
		}
	}
}
