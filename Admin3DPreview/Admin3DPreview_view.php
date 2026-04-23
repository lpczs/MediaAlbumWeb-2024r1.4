<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');

class Admin3DPreview_view
{
	static function modelListGrid($pResultArray)
	{
		$smarty = SmartyObj::newSmarty('Admin3DPreview');

		$smarty->assign('TPX_PRODUCTCOLLECTIONTYPE_PHOTOBOOK', TPX_PRODUCTCOLLECTIONTYPE_PHOTOBOOK);

		$smarty->displayLocale('admin/3dpreview/3dpreview.tpl');
	}

	static function assignModelToProductsList($pResultArray)
	{
		$smarty = SmartyObj::newSmarty('Admin3DPreview');
		$smarty->assign('TPX_PRODUCTCOLLECTIONTYPE_PHOTOBOOK', TPX_PRODUCTCOLLECTIONTYPE_PHOTOBOOK);

		$smarty->assign('modellist', $pResultArray['3dmodellist']);

		$smarty->displayLocale('admin/3dpreview/3dpreviewassign.tpl');
	}

	static function getGridData($pResultArray)
	{
		$data = $pResultArray['3dmodellist'];
		$dataArray = array();

		echo '[';
		echo '[' . count($data) . '],';

		foreach ($data as $model)
		{
			$dataArray[] = "['" . $model['modelid'] . "','" . UtilsObj::ExtJSEscape($model['modelcode']) . "','" . UtilsObj::ExtJSEscape($model['modelname']) . "','" . $model['active'] . "', '" . $model['modeltype'] . "', '" . $model['hasfileerror'] . "']";
		}

		echo implode(',', $dataArray);

		echo ']';
	}

	static function addDisplay($pResultArray)
	{
		$smarty = SmartyObj::newSmarty('Admin3DPreview');
		$modelData = $pResultArray['data'];
		
		if (count($modelData) > 0)
		{
			$smarty->assign('title', $smarty->get_config_vars('str_LabelEdit3DPreviewModel'));
			$smarty->assign('modelID', $modelData['id']);
			$smarty->assign('modelCode', $modelData['modelcode']);
			$smarty->assign('modelName', $modelData['modelname']);
			$smarty->assign('modelFilename', $modelData['uploadfilename']);
			$smarty->assign('isActive', $modelData['active']);
			$smarty->assign('isEdit', 1);
		}
		else
		{
			$smarty->assign('title', $smarty->get_config_vars('str_LabelAdd3DPreviewModel'));
			$smarty->assign('modelID', 0);
			$smarty->assign('modelCode', '');
			$smarty->assign('modelName', '');
			$smarty->assign('modelFilename', '');
			$smarty->assign('isActive', 0);
			$smarty->assign('isEdit', 0);
		}
		
		$smarty->displayLocale('admin/3dpreview/3dpreviewedit.tpl');
	}

	static function display($pResultArray)
	{
		if ($pResultArray['error'] != '')
        {
			$smarty = SmartyObj::newSmarty('Admin3DPreview');
			$title = $smarty->get_config_vars('str_TitleWarning');

			if (substr($pResultArray['error'], 0, 4) == 'str_')
			{
				$msg = $smarty->get_config_vars($pResultArray['error']);

				switch ($pResultArray['error'])
				{
					case 'str_Error3DModelDoesnotContainImageReference':
					{
						$msg = str_replace('^0', implode(',<br />', array_keys($pResultArray['data']['missingmapdiffuse'])), $msg);
						break;
					}
					case 'str_Error3DModelZipDoesNotContainFile':
					{
						$msg = str_replace('^0', implode(',<br />', array_keys($pResultArray['data']['missingimage'])), $msg);
						break;
					}
				}
			}
			else
			{
				$msg = $pResultArray['error'];
			}

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			echo '{"success":true}';
        } 
	}

	static function addModelDisplay($pResultArray)
	{
		if ($pResultArray['error'] != '')
        {
			$smarty = SmartyObj::newSmarty('Admin3DPreview');
			$title = $smarty->get_config_vars('str_TitleWarning');

			if (substr($pResultArray['error'], 0, 4) == 'str_')
			{
				$msg = $smarty->get_config_vars($pResultArray['error']);
			}
			else
			{
				$msg = $pResultArray['error'];
			}

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			echo '{"success":true, "modelid":"' . $pResultArray['data']['modelid'] . '"}';
        }
	}

	static function deleteModelDisplay($pResultArray)
	{
		if ($pResultArray['error'] != '')
        {
			$smarty = SmartyObj::newSmarty('Admin3DPreview');
			$title = $smarty->get_config_vars('str_TitleWarning');

			if ($pResultArray['data']['deletefailed'])
			{
				$msg = '';

				if (substr($pResultArray['error'], 0, 4) == 'str_')
				{
					$msg = $smarty->get_config_vars($pResultArray['error']);

					if ($pResultArray['error'] == 'str_WarningUnableToDeleteModelPlural')
					{
						$msg = UtilsObj::replaceParams($msg, $pResultArray['errorparam']);
					}
				}
				else
				{
					$msg = $pResultArray['error'];
				}
			}
			else
			{
				$msg = $smarty->get_config_vars($pResultArray['error']);
			}

			echo '{"success":false, "title":"' . $title . '", "msg":"' . $msg . '"}';
        }
        else
        {
			echo '{"success":true}';
        }
	}
}
