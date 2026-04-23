<?php

namespace PricingEngine\Component\Callback;

use DatabaseObj;
use MetaDataObj;

/**
 * Metadata callback class
 *
 * Handles the synchronisation of metadata on request.
 * Tightly couples to the DatabaseObj class.
 *
 * @author Simon Paulger <simon.paulger@taopix.com>
 * @copyright Taopix Limited
 */
class MetadataCallback
{
	/**
	 * Synchronise metadata
	 *
	 * Synchronise existing metadata for the given
	 * component code using the database. The synchronised
	 * metadata is returned.
	 *
	 * @param string $componentCode
	 * @param mixed[] $existingMetadata
	 * @return mixed[]
	 */
	public static function sync($componentCode, $existingMetadata)
	{
		$metadata = [];
		$componentData = DatabaseObj::getComponentByCode($componentCode);

		if ($componentData['keywordgroupheaderid'] > 0) {
			$metadata = MetaDataObj::getKeywordList('COMPONENT', '', '', $componentData['keywordgroupheaderid']);

			foreach ($metadata as &$metadataItem) {
				foreach ($existingMetadata as $existingMetadataItem) {
					if ($metadataItem['ref'] == $existingMetadataItem['ref']) {
						$metadataItem['defaultvalue'] = $existingMetadataItem['defaultvalue'];
						break;
					}
				}
			}
		}

		return $metadata;
	}
}
