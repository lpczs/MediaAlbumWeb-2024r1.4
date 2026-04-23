<?php

namespace DataExport\Exporter;

use Iterator;

interface DataSetExporterInterface extends Iterator
{
	/**
	 * Get normalised header list
	 *
	 * @return string[]
	 */
	public function getNormalizedHeader();

	/**
	 * Get header list
	 *
	 * @return string[]
	 */
	public function getHeaders();

	/**
	 * Get root element name
	 *
	 * @return string
	 */
	public function getRootElementName();

	/**
	 * Get data element name
	 *
	 * @return string
	 */
	public function getDataElementName();

	/**
	 * Get the number of records processed (so far)
	 *
	 * @return int
	 */
	public function getRecordCount();

	/**
	 * Get the last record id processed
	 *
	 * @return int
	 */
	public function getLastId();
}
