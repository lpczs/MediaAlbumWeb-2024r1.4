<?php

require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');

class AdminTaopixOnlineFontLists_view
{
	public static function displayGrid()
	{
		$smarty = SmartyObj::newSmarty('AdminTaopixOnlineFontLists');
		$smarty->displayLocale('admin/taopixonlinefontlists/grid.tpl');
	}

	public static function getGridData($pData)
	{
		$return = [];

		if (! empty($pData))
		{
			// Remove one from the item count as we have the cachehash value in the data array.
			$return[] = [count($pData['data']) - 1];

			foreach ($pData['data'] as $key => $listDetails)
			{
				if ('cachehash' === $key || null === $listDetails['id'])
				{
					continue;
				}

				$return[] = [$listDetails['id'], $listDetails['name']];
			}
		}

		echo json_encode($return);
	}

	public static function displayAddEditForm($item, $fontList, $collectionList, $dataList = [])
	{
		$smarty = SmartyObj::newSmarty(['AdminTaopixOnlineFontLists', 'AdminSitesOrderRouting']);

		$fontList[0]['text'] = $smarty->getConfigVariable('str_LabelAll');
		$title = $smarty->getConfigVars(null !== $item ? 'str_ButtonEdit' : 'str_LabelAdd');
		$smarty->assign('id', $item['id'] ?? 0);
		$smarty->assign('name', $item['name'] ?? '');
		$smarty->assign('fontlist', json_encode($fontList ?? []));
		$smarty->assign('rulelist', json_encode(
			array_map(function($rule) { return array_values($rule); }, $item['rules'] ?? [])
		));
		$smarty->assign('title', $title);
		$smarty->assign('collectionList', json_encode($collectionList));

		if (! empty($dataList))
		{
			array_map(function($key, $data) use ($smarty) {
				$smarty->assign($key, json_encode($data['data']));
			}, array_keys($dataList), $dataList);
		}

		$smarty->displayLocale('admin/taopixonlinefontlists/edit.tpl');
	}

	public static function displayServerResponseResult($pResult)
	{
		$smarty = SmartyObj::newSmarty('AdminTaopixOnlineFontLists');
		$return = [
			'success' => true,
			'title' => '',
			'msg' => '',
			'data' => [],
		];

		if ('' !== $pResult['error'])
		{
			$return['success'] = false;
			$return['title'] = $smarty->get_config_vars('str_TitleWarning');
			if ('str_DuplicateEntryError' !== $pResult['errorparam'])
			{
				$return['msg'] = str_replace('^0', $pResult['errorparam'], $smarty->get_config_vars($pResult['error']));
			}
			else
			{
				$return['msg'] = str_replace(['^0', '^1'],
					[strtolower($smarty->get_config_vars('str_TitleFontList')), strtolower($smarty->get_config_vars('str_ExtJsPropertyColumnModelName'))],
					$smarty->get_config_vars($pResult['errorparam']));
			}
		}

		echo json_encode($return);
	}
}