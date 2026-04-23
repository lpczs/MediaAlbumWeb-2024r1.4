<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');
require_once('../Utils/UtilsRoute.php');

class AdminTaopixOnlineVolumesAdmin_view
{
	static function displayGrid()
	{
		global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminTaopixOnlineVolumes');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->displayLocale('admin/taopixonlinevolumes/volumesgrid.tpl');
	}

	static function getGridData($pVolumeDataArray)
    {
    	global $gConstants;
		$smarty = SmartyObj::newSmarty('AdminTaopixOnlineVolumes');

		echo '[';

		if ($pVolumeDataArray['result'] == '')
		{
			if (array_key_exists('volumes', $pVolumeDataArray))
			{
				$itemCount = count($pVolumeDataArray['volumes']);

				echo '[' . $itemCount . '],';

				for ($i = 0; $i < $itemCount; $i++)
				{
					$server = $pVolumeDataArray['volumes'][$i];

					echo "['" . $server['id'] . "',";
					echo "'" . UtilsObj::encodeString($server['code'], true) . "',";
					echo "'" . UtilsObj::encodeString($server['root'], true) . "',";
					echo "'" . $server['assettype'] . "',";
					echo "'" . UtilsObj::formatVolumeSize($server['headroom']) . "',";
					echo "'" . UtilsObj::formatVolumeSize($server['free']) . "',";
					echo "'" . UtilsObj::formatVolumeSize($server['size']) . "',";
					echo "'" . $server['statslastupdated'] . "',";
					echo "'" . $server['preference'] . "',";
					echo "'" . $server['active'] . "',";
					echo "'']";

					if ($i != $itemCount - 1)
					{
						echo ",";
					}
				}
			}
			else
			{
				echo "[0],['', '', '', '', '', '', '', '', '', '" . $smarty->get_config_vars("str_ErrorConfigMismatch") . "']";
			}
		}
		else
		{
			echo "[0],['', '', '', '', '', '', '', '', '', '" . $smarty->get_config_vars($pVolumeDataArray['result']) . "']";
		}


		echo ']';
	}

	static function displayEntry($pTitle, $pServerID, $pID, $pCode, $pRoot, $pHeadRoom, $pFreeSpace, $pSize, $pPreference, $pActive, $pVolumeType,
									$pStorageRegion, $pStorageName, $pAccessKey, $pSecret, $pStorageClass, $pUploadUrl, $pDownloadUrl, $pAssetType)
	{
		$smarty = SmartyObj::newSmarty('AdminTaopixOnlineVolumes');

		$smarty->assign('serverid', $pServerID);
		$smarty->assign('volumeid', $pID);
		$smarty->assign('code', $pCode);
		$smarty->assign('title', $smarty->get_config_vars($pTitle));
		$smarty->assign('root',  $pRoot);
		$smarty->assign('headroom', $pHeadRoom);
		$smarty->assign('freespace', UtilsObj::formatVolumeSize($pFreeSpace));
		$smarty->assign('size', UtilsObj::formatVolumeSize($pSize));
		$smarty->assign('preference', $pPreference);
		$smarty->assign('active', $pActive);

		if ($pID > 0 && $pVolumeType != 2)
		{
			$voluemTypeData = '["0", "'. $smarty->get_config_vars('str_LabelNonSharedDisk') .'"], ["1", "'. $smarty->get_config_vars('str_LabelSharedDisk') .'"]';
		}
		else if ($pID > 0 && $pVolumeType == 2)
		{
			$voluemTypeData = '["2", "'. $smarty->get_config_vars('str_LabelAmazonS3Bucket') .'"]';
		}
		else
		{
			$voluemTypeData = '["0", "'. $smarty->get_config_vars('str_LabelNonSharedDisk') .'"], ["1", "'. $smarty->get_config_vars('str_LabelSharedDisk') .'"], ["2", "'. $smarty->get_config_vars('str_LabelAmazonS3Bucket') .'"]';
		}

		$smarty->assign('volumetype', $pVolumeType);
		$smarty->assign('awsregions', self::getAwsRegions());
		$smarty->assign('volumetypedata', $voluemTypeData);
		$smarty->assign('storageregion', $pStorageRegion);
		$smarty->assign('storagename', $pStorageName);
		$smarty->assign('accesskey', $pAccessKey);
		$smarty->assign('secret', $pSecret);
		$smarty->assign('storageclass', $pStorageClass);
		$smarty->assign('uploadurl', $pUploadUrl);
		$smarty->assign('downloadurl', $pDownloadUrl);
		$smarty->assign('assettype', $pAssetType);

		$smarty->displayLocale('admin/taopixonlinevolumes/volumesedit.tpl');
	}

	static function displayAdd($pServerID)
	{
		self::displayEntry('str_TitleAddVolume', $pServerID, 0, '', '', 0, 0, 0, 0, 0, 0, '', '', '', '', 0, '', '', 7);
	}

	static function displayEdit($pResultArray, $pServerID)
	{
		$volume = $pResultArray['volume'];

		self::displayEntry('str_TitleEditVolume', $pServerID, $volume['id'], UtilsObj::encodeString($volume['code'],true),
							UtilsObj::encodeString($volume['root'], true), $volume['headroom'], $volume['free'], $volume['size'],
							$volume['preference'], $volume['active'], $volume['type'], $volume['storageregion'], $volume['storagename'], $volume['accesskey'], $volume['secret'],
							$volume['storageclass'], $volume['uploadurl'], $volume['downloadurl'], $volume['assettype']);
	}

	static function volumeSave($pResultArray)
    {
       	$smarty = SmartyObj::newSmarty('AdminTaopixOnlineVolumes');

        $resultData = '';

    	if ($pResultArray['error'] == '')
        {
			$resultData = '{
							"success":true,
							"data":
							{
								"id":' . $pResultArray['data']['id'] . ',
								"code":"' . UtilsObj::encodeString($pResultArray['data']['code'],true) . '",
								"root":"' .  UtilsObj::encodeString($pResultArray['data']['root'], true) . '",
								"headroom":"' . $pResultArray['data']['headroom'] . '",
        						"free":"",
        						"size":"",
        						"lastupdated":"",
        						"preference":"' . $pResultArray['data']['preference'] . '"
        					}
        				}';
        }
        else
        {
			$msg = $smarty->get_config_vars($pResultArray['error']);
			$title = $smarty->get_config_vars('str_TitleWarning');

			$resultData = '{
								"success":false,
								"title":"' . $title . '",
								"msg":"' . $msg . '"
							}';
        }

        echo $resultData;

    }

	static function updateVolumesGrid($pResultArray)
    {
        global $gSession;

		$resultData = '{
							"success":true,
							"data":{}
						}';

        if (!$pResultArray['success'])
        {
        	$smarty = SmartyObj::newSmarty('AdminTaopixOnlineVolumes');
        	$title = $smarty->get_config_vars('str_TitleWarning');
        	$msg = $smarty->get_config_vars($pResultArray['error']);

			$resultData = '{
				"success":false,
				"title":"' . $title . '",
				"msg":"' . $msg . '"
			}';

        }

        echo $resultData;

	}

	static private function getAwsRegions()
	{
		$path = "../config/s3regions.conf";
		$serverList = UtilsObj::readConfigFile($path);
		$formattedServerList = [];

		foreach ($serverList as $regionCode => $regionName)
		{
			$formattedServerList[] = '["' . trim($regionCode) . '", "' . trim($regionName) . '"]';
		}

		return join(', ', $formattedServerList);
	}
}

?>