<?php

namespace DataExport\Writer;

class DataFileSpec
{
	/**
	 * @var string[]
	 */
	public $normalizedHeaders;

	/**
	 * @var string[]
	 */
	public $headers;

	/**
	 * @var string
	 */
	public $rootElementName;

	/**
	 * @var string
	 */
	public $dataElementName;

	/**
	 * Constructor
	 *
	 * @param string[] $pNormalizedHeaders
	 * @param string[] $pHeaders
	 * @param string $pRootElementName
	 * @param string $pDataElementName
	 */
	public function __construct($pNormalizedHeaders, $pHeaders, $pRootElementName, $pDataElementName)
	{
		$this->normalizedHeaders = $pNormalizedHeaders;
		$this->headers = $pHeaders;
		$this->rootElementName = $pRootElementName;
		$this->dataElementName = $pDataElementName;
	}
}
