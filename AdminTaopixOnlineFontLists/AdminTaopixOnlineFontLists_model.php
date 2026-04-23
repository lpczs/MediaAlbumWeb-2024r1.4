<?php

require_once('../libs/internal/curl/Curl.php');
require_once(__DIR__ . '/AdminTaopixOnlineFontLists_control.php');

class AdminTaopixOnlineFontLists_model
{
	public static function getGridData($pConfig)
	{
		// Check cache valid, if not get new data and save this as the cached data.
		$cachePath = $pConfig['CONTROLCENTREONLINECACHEDATAPATH'] . 'onlinefontlists.php';

		return self::getOnlineData($cachePath, 'GETFONTLISTS', $pConfig);
	}

	public static function getAllFonts($pConfig, $pUsedFonts, $pLang)
	{
		$returnList = [];

		$skipStyleAdditions = [
			'normal',
			'regular'
		];

		// Check cache valid, if not get new data and save this as the cached data.
		$cachePath = $pConfig['CONTROLCENTREONLINECACHEDATAPATH'] . 'onlineinstalledfonts.php';
		$fontList = self::getOnlineData($cachePath, 'GETINSTALLEDFONTS', $pConfig);
		$allChecked = true;

		foreach ($fontList['data'] as $key => $details)
		{
			if ('cachehash' === $key)
			{
				continue;
			}

			$familyNameLocale = self::stringToLocale($details['details']['locale'], $pLang);
			if (1 === count($details['fonts']))
			{
				/*
				 * For fonts where they are a family of one, this may be due to font data being incorrect,
				 * we attach the locale style name if it is not Normal, or Regular, and it does not exist in the name of the font.
				 * We get the english localisation of the style to check for Normal/Regular.
				 */
				$enStyleLocale = self::stringToLocale($details['fonts'][0]['localestylename'], 'en');
				if (! in_array(mb_strtolower($enStyleLocale, 'UTF-8'), $skipStyleAdditions)) {
					$localeStyle = self::stringToLocale($details['fonts'][0]['localestylename'], $pLang);
					$familyNameLocale .= false === mb_strpos($familyNameLocale, $localeStyle) ? ' ' . $localeStyle : '';
				}
				$returnList[] = [
					'id' => $details['fonts'][0]['fontid'],
					'text' => $familyNameLocale,
					'leaf' => true,
					'checked' => in_array($details['fonts'][0]['fontid'], $pUsedFonts),
					'allowdrag' => false,
					'allowdrop' => false,
					'iconCls' => 'no-icon',
				];

				if ($allChecked) {
					$allChecked = in_array($details['fonts'][0]['fontid'], $pUsedFonts);
				}
			}
			else
			{
				$fontItem = [
					'id' => 'p-' . $key,
					'text' => $familyNameLocale,
					'leaf' => false,
					'checked' => !empty($pUsedFonts),
					'allowdrag' => false,
					'allowdrop' => false,
					'expanded' => true,
				];

				foreach ($details['fonts'] as $key => $fontStyle)
				{
					$styleNameLocale = self::stringToLocale($fontStyle['localestylename'], $pLang);
					$checkedByDefault = !empty($pUsedFonts) && in_array($fontStyle['fontid'], $pUsedFonts);
					if ($fontItem['checked'])
					{
						$fontItem['checked'] = $checkedByDefault;

						if ($allChecked && !$checkedByDefault) {
							$allChecked = false;
						}
					}
					$fontItem['children'][] = [
						'id' => $fontStyle['fontid'],
						'text' => $styleNameLocale,
						'leaf' => true,
						'checked' => $checkedByDefault,
						'allowdrag' => false,
						'allowdrop' => false,
						'iconCls' => 'no-icon',
					];
				}

				$returnList[] = $fontItem;
			}
		}

		return [
			[
				'id' => -1,
				'text' => '',
				'leaf' => false,
				'checked' => $allChecked,
				'allowdrag' => false,
				'allowdrop' => false,
				'expanded' => true,
				'children' => $returnList,
			]
		];
	}

	public static function getCollectionList($pDbObj, $pUsedItems, $pLang)
	{
		$createdBranches = [];

		$productList = DatabaseObj::getDataFromTable(['collectioncode', 'productcode', 'collectionname', 'productname'], 'productcollectionlink', $pDbObj, '',true);

		// Return empty list if we got an error or there are no products.
		if ('' !== $productList['error'] || empty($productList['data']))
		{
			return [];
		}

		foreach ($productList['data'] as $key => $details)
		{
			if (! array_key_exists($details['collectioncode'], $createdBranches))
			{
				$createdBranches[$details['collectioncode']] = [
					'id' => 'p-' . $details['collectioncode'],
					'text' => self::stringToLocale($details['collectionname'], $pLang),
					'leaf' => false,
					'checked' => false,
					'allowdrag' => false,
					'allowdrop' => false,
					'expanded' => false,
				];
			}

			$createdBranches[$details['collectioncode']]['children'][] = [
				'id' => $details['productcode'],
				'text' => self::stringToLocale($details['productname'], $pLang),
				'leaf' => true,
				'checked' => false,
				'allowdrag' => false,
				'allowdrop' => false,
			];
		}

		return array_values($createdBranches);
	}

	public static function getItemById($pId, $pConfig)
	{
		$cachePath = $pConfig['CONTROLCENTREONLINECACHEDATAPATH'] . 'onlinefontlists.php';
		$listData = include $cachePath;

		return array_values(array_filter($listData['data'], function($details) use ($pId) { return ($pId === ($details['id'] ?? null)); }))[0] ?? [];
	}

	private static function getOnlineData($pCacheFile, $pCommand, $pConfig)
	{
		if (!array_key_exists('TAOPIXONLINEURL', $pConfig) || '' === trim($pConfig['TAOPIXONLINEURL'])) {
			return [];
		}

		$cacheKey = '';

		if (file_exists(($pCacheFile)))
		{
			$fontListData = include $pCacheFile;
			$cacheKey = $fontListData['data']['cachehash'];
		}

		$dataToEncrypt = array('cmd' => $pCommand, 'data' => array('cachehash' => $cacheKey));

		$onlineData = CurlObj::sendByPut($pConfig['TAOPIXONLINEURL'], 'AdminAPI.callback', $dataToEncrypt);

		if ('' === $onlineData['error'])
		{
			// If we got no data back either no font lists defined or data cached is up todate.
			if (empty($onlineData['data']['data']))
			{
				return $fontListData ?? [];
			}

			if ($cacheKey !== $onlineData['data']['data']['cachehash'])
			{
				self::generateCacheFile($onlineData['data'], $pCacheFile);
			}

			return $onlineData['data'];
		}

		return null;
	}

	private static function stringToLocale($pString, $pLocale)
	{
		$locales = explode('<p>', $pString);
		$filtered = array_values(array_filter($locales, function($value) use ($pLocale) {
			list($locale, $string) = explode(' ', $value, 2);
			return false !== strstr($locale, $pLocale);
		}));
		$locale = explode(' ', $filtered[0] ?? $locales[0], 2);

		return $locale[1];
	}

	public static function generateCacheFile($pData, $pPath)
	{
		file_put_contents($pPath, '<?php' . PHP_EOL . 'return ' . var_export($pData, true) . ';');
	}

	public static function getFontListData($config, $checkKey, $checkValue)
	{
		$returnArray = [
			'fontlists' => [],
			'selected' => -1,
		];

		// If there is no taopixonline url key or it is empty just return no need to process any further.
		if (!array_key_exists('TAOPIXONLINEURL', $config) || '' === trim($config['TAOPIXONLINEURL'])) {
			return $returnArray;
		}

		$fontData = AdminTaopixOnlineFontLists_model::getGridData($config);

		if ('' !== $fontData['error'] && 0 !== $fontData['error'])
		{
			return $returnArray;
		}

		return self::getSelectedFontList($fontData['data'], $checkKey, $checkValue);
	}

	public static function getSelectedFontList($fontList, $checkKey, $checkValue)
	{
		$returnArray = [
			'fontlists' => [],
			'selected' => -1,
			'matched' => false,
		];

		foreach ($fontList as $key => $listInfo)
		{
			if ('cachehash' === $key) {
				continue;
			}

			if (null !== $listInfo['id']) {
				$returnArray['fontlists'][] = [$listInfo['id'], $listInfo['name']];
			}

			$isSelected = array_filter($listInfo['rules'], function($value) use ($checkKey, $checkValue) {
				return $checkValue === $value[$checkKey];
			});

			if (! empty($isSelected)) {
				$returnArray['matched'] = true;
				$returnArray['selected'] = $listInfo['id'];
			}
		}

		return $returnArray;
	}

	public static function getFontList($fontLists, $requiredId)
	{
		return array_values(array_filter($fontLists, function($list) use ($requiredId) {
			// This skips the cache key in the list.
			if (is_string($list)) { return false; }

			return $requiredId === $list['id'];
		}));
	}

	public static function getMatchedRule($fontList, $checkKey, $checkValue)
	{
		return array_values(array_filter($fontList['rules'], function($rule) use($checkKey, $checkValue) {
			return $rule[$checkKey] === $checkValue;
		}));
	}

	/**
	 * Updates the assignments that a font list applies to, this is called from the edit/add panels in the following
	 * Brands, License Keys, and Products.
	 *
	 * [
	 * 		'type' => int | null (-1 All, 0, selected list, null use defaults)
	 * 		'fontlist' => int (Id of the font list to use
	 * 		'codes' => string[] (Array of codes to update)
	 * 		'checkfield' => string (string detailing which field the codes relate to)
	 * ]
	 * @param $fontListDetails array formatted as above.
	 * @param $config array Passed config, this is generally ac_config
	 */
	public static function updateAssignments(array $fontListDetails, array $config)
	{
		$updates = [];
		$defaultRule = [
			'brandcode' => null,
			'groupcode' =>  null,
			'collectioncode' => null,
			'productcode' => null,
		];

		// Taopixonline url not configured bail, as we cant do updates
		if (!array_key_exists('TAOPIXONLINEURL', $config) || '' === trim($config['TAOPIXONLINEURL'])) {
			return;
		}

		$fontData = self::getGridData($config);

		foreach ($fontListDetails['codes'] as $key => $code) {
			$selectedInfo = self::getSelectedFontList($fontData['data'], $fontListDetails['checkfield'], $code);
			$fontList = $selectedInfo['matched'] ? self::getFontList($fontData['data'], $selectedInfo['selected'])[0] : [];

			$currentRule = $defaultRule;
			$currentRule[$fontListDetails['checkfield']] = $code;

			switch ($fontListDetails['type'])
			{
				case -1:
					// switch to all
					if ($selectedInfo['selected'] !== $fontListDetails['type'])
					{
						if (!empty($fontList))
						{
							if (! array_key_exists($fontList['id'], $updates))
							{
								$updates[$fontList['id']] = [
									'rules' => [],
									'removedrules' => [],
								];
							}
							$rule = self::getMatchedRule($fontList, $fontListDetails['checkfield'], $code)[0];
							$updates[$fontList['id']]['removedrules'][] = $rule['id'];
						}
					}
					break;

				case 0:
					// Setting to specific list.
					if ($selectedInfo['selected'] !== $fontListDetails['fontlist'])
					{
						if (!empty($fontList) && !array_key_exists($selectedInfo['selected'], $updates))
						{
							$updates[$selectedInfo['selected']] = [
								'rules' => [],
								'removedrules' => [],
							];
						}
						if (!array_key_exists($fontListDetails['fontlist'], $updates))
						{
							$updates[$fontListDetails['fontlist']] = [
								'rules' => [],
								'removedrules' => [],
							];
						}
						if (! empty($fontList))
						{
							$rule = self::getMatchedRule($fontList, $fontListDetails['checkfield'], $code)[0];
							$updates[$selectedInfo['selected']]['removedrules'][] = $rule['id'];
						}
						$updates[$fontListDetails['fontlist']]['rules'][] = $currentRule;
					}
					break;

				case 1:
					// Setting to use default.
					if ($selectedInfo['selected'] !== $fontListDetails['type'])
					{
						if (!empty($fontList))
						{
							if (! array_key_exists($fontList['id'], $updates))
							{
								$updates[$fontList['id']] = [
									'rules' => [],
									'removedrules' => [],
								];
							}
							$rule = self::getMatchedRule($fontList, $fontListDetails['checkfield'], $code)[0];
							$updates[$fontList['id']]['removedrules'][] = $rule['id'];
						}

						if (!array_key_exists('', $updates))
						{
							$updates[''] = [
								'rules' => [],
								'removedrules' => [],
							];
						}

						$updates['']['rules'][] = $currentRule;
					}
					break;
			}
		}

		if (! empty($updates))
		{
			AdminTaopixOnlineFontLists_control::sendToOnline(['assignments' => $updates], 'UPDATEFONTLISTASSIGNMENTS');
		}
	}

	/**
	 * Removes assignemnts for font lists, this is called from the delete operation on Brands, and from updateAssignments
	 *
	 * [
	 * 		'codes' => string[] (Array of codes to remove assignments for)
	 * 		'checkfield' => string (String detailing which field the codes relate to)
	 * ]
	 * @param $fontListDetails array formatted as above.
	 * @param $config array Passed config, this is generally ac_config
	 */
	public static function removeAssignments(array $fontListDetails, array $config)
	{
		// Taopixonline url not configured bail, as we cant do updates
		if (!array_key_exists('TAOPIXONLINEURL', $config) || '' === trim($config['TAOPIXONLINEURL'])) {
			return;
		}

		if (!empty($fontListDetails['codes']))
		{
			AdminTaopixOnlineFontLists_control::sendToOnline(['remove' => $fontListDetails], 'DELETEFONTLISTS');
		}
	}
}
