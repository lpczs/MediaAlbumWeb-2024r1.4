<?php

require_once(__DIR__ . '/../../AssetSourceMap.php');

/**
 * Smarty plugin to lookup the location of an asset file using a mapping file.
 * No guarantees are made on the value of the URL returned as this is
 * dependent on the contents of the mapping file itself.
 *
 * If the mapping is not found, null is returned.
 *
 * @param array $params
 * @param Smarty_Internal_Template $template
 * @return string|null
 */

function smarty_function_asset(array $params, Smarty_Internal_Template $template)
{
	if (!isset($params['file'])) {
		// File path is missing, complain
		trigger_error('asset: missing \'file\' parameter');
		return null;
	}

	if (!isset(AssetSourceMap::$sourceMap[$params['file']])) {
		// Mapping entry is missing, return null
		trigger_error('asset: mapping entry for \'' . $params['file'] . '\' not found');
		return null;
	}

	return AssetSourceMap::$sourceMap[$params['file']];
}
