<?php

use Security\RequestValidationTrait;

require_once(__DIR__ . '/AdminTaopixOnlineFontLists_model.php');
require_once(__DIR__ . '/AdminTaopixOnlineFontLists_view.php');
require_once('../Utils/UtilsAuthenticate.php');

class AdminTaopixOnlineFontLists_control
{
	use RequestValidationTrait;

	public static function initialize()
	{
		if (1 === AuthenticateObj::adminSessionActive())
		{
			AdminTaopixOnlineFontLists_view::displayGrid();
		}
	}

	public static function getGridData()
	{
		if (1 === AuthenticateObj::adminSessionActive())
		{
			$constants = self::getConfigVars();
			$resultArray = AdminTaopixOnlineFontLists_model::getGridData($constants['ac_config']);
			AdminTaopixOnlineFontLists_view::getGridData($resultArray);
		}
	}

	public static function formDisplay()
	{
		if (1 === AuthenticateObj::adminSessionActive())
		{
			$dbObj = DatabaseObj::getGlobalDBConnection();
			$dataList = [
				'brandCodes' => DatabaseObj::getDataFromTable(['code'], 'branding', $dbObj, 'Default Brand'),
				'groupCodes' => DatabaseObj::getDataFromTable(['groupcode'], 'licensekeys', $dbObj),
				'productCodes' => DatabaseObj::getDataFromTable(['code'], 'products', $dbObj),
			];

			$constants = self::getConfigVars();
			$itemToEdit = UtilsObj::getGETParam('editItem', null);
			if (null !== $itemToEdit)
			{
				$itemToEdit = AdminTaopixOnlineFontLists_model::getItemById((int)$itemToEdit, $constants['ac_config']);
			}

			$fontList = AdminTaopixOnlineFontLists_model::getAllFonts($constants['ac_config'], ($itemToEdit['fonts'] ?? []), $constants['lang']);
			$collectionList = AdminTaopixOnlineFontLists_model::getCollectionList($dbObj, [], $constants['lang']);
			AdminTaopixOnlineFontLists_view::displayAddEditForm($itemToEdit, $fontList, $collectionList, $dataList);
		}
	}

	public static function saveFontList()
	{
		if (1 === AuthenticateObj::adminSessionActive())
		{
			$id = (int)UtilsObj::getPOSTParam('id', -1);
			$constants = self::getConfigVars();
			$itemToEdit = 0 < $id ? AdminTaopixOnlineFontLists_model::getItemById($id, $constants['ac_config']) : [];
			$fontList = self::correctFontList(UtilsObj::getPOSTParam('fonts', ''));
			$ruleList = self::generateRuleList(json_decode(UtilsObj::getPOSTParam('rules', []), true), $itemToEdit['rules'] ?? []);
			// Build the data to send to online.
			$data = [
				'id' => $id,
				'name' => UtilsObj::getPOSTParam('name', ''),
				'fonts' => json_encode(array_values(array_diff($fontList, ($itemToEdit['fonts'] ?? [])))),
				'removedfonts' => json_encode(array_values(array_diff(($itemToEdit['fonts'] ?? []), $fontList))),
				'rules' => json_encode($ruleList['updated'] ?? []),
				'removedrules' => json_encode($ruleList['removed'] ?? []),
			];

			$result = self::sendToOnline($data, 'SAVEFONTLIST');
			AdminTaopixOnlineFontLists_view::displayServerResponseResult($result);
		}
	}

	public static function deleteFontLists()
	{
		if (1 === AuthenticateObj::adminSessionActive())
		{
			$data = [
				'lists' => json_encode(explode(',', UtilsObj::getPOSTParam('selectedLists', ''))),
			];

			$result = self::sendToOnline($data, 'DELETEFONTLISTS');
			AdminTaopixOnlineFontLists_view::displayServerResponseResult($result);
		}
	}

	public static function getConfigVars()
	{
		// Get the global values we use else where so we can pass them in rather than just using globals.
		global $ac_config;

		return [
			'ac_config' => $ac_config,
			'lang' => UtilsObj::getBrowserLocale(),
		];
	}

	private static function correctFontList($pFonts)
	{
		$fonts = explode(',', $pFonts);
		return array_values(array_filter($fonts, function($value) { return ('' !== $value && 'p-' !== substr($value, 0, 2)); }));
	}

	public static function sendToOnline($pData, $pCmd)
	{
		$config = self::getConfigVars();
		$dataToEncrypt = [
			'cmd' => $pCmd,
			'data' => $pData,
		];

		$return = CurlObj::sendByPut($config['ac_config']['TAOPIXONLINEURL'], 'AdminAPI.callback', $dataToEncrypt);

		if ('' !== $return['error'])
		{
			return $return;
		}

		if (! $return['data']['success'])
		{
			$return['error'] = 'str_DatabaseError';
			$return['errorparam'] = $return['data']['error'];
			return $return;
		}

		self::invalidateCacheFile($pCmd, $return['data']);

		return $return;
	}

	private static function invalidateCacheFile($pCmd, $pData)
	{
		$fileMap = [
			'DELETEFONTLISTS' => 'onlinefontlists.php',
			'SAVEFONTLIST' => 'onlinefontlists.php',
			'DUPLICATEFONTLISTS' => 'onlinefontlists.php',
			'UPDATEFONTLISTASSIGNMENTS' => 'onlinefontlists.php',
		];

		$config = self::getConfigVars();
		$cacheFile = $config['ac_config']['CONTROLCENTREONLINECACHEDATAPATH'] . $fileMap[$pCmd];

		if (! empty($pData))
		{
			AdminTaopixOnlineFontLists_model::generateCacheFile($pData['data']['data'], $cacheFile);
		}

		$opCacheStatus = opcache_get_status();
		if (false !== $opCacheStatus)
		{
			opcache_invalidate($cacheFile);
		}
	}

	private static function generateRuleList($pSubmitted, $pExisting)
	{
		$return = [
			'updated' => [],
			'removed' => [],
		];
		$keptIds = [];

		foreach ($pSubmitted as $key => $details)
		{
			if (0 > $details['id'])
			{
				// Remove id as this is a new item.
				unset($details['id']);
				$return['updated'][] = $details;
			}
			else
			{
				$keptIds[] = $details['id'];
			}
		}
		$existingIds = array_column($pExisting, 'id');

		$return['removed'] = array_values(array_diff($existingIds, $keptIds));

		return $return;
	}
}