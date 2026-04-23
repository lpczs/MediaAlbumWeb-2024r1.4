<?php

namespace DataExport\Writer;

use Exception;

final class ExportWriterFactory
{
    /**
     * @param string $pFileFormat
     * @param DataFileSpec $pDataFileSpec
     * @param string $pFileName
     * @param boolean $pBeautify
     * @return WriterInterface
	 * @throws Exception
     */
    public static function create($pFileFormat, $pDataFileSpec, $pFileName, $pBeautify = false)
    {
        switch ($pFileFormat)
        {
            case 'csv': return new CsvWriter($pFileName, $pDataFileSpec);
            case 'xml': return new XmlWriter($pFileName, $pDataFileSpec, $pBeautify);
            case 'tsv': return new TsvWriter($pFileName, $pDataFileSpec);
        }

        throw new Exception(sprintf('Unsupported export file format "%s"', $pFileFormat));
    }
}
