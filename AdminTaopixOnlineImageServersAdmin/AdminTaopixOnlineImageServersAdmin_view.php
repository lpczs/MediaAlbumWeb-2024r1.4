<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');
require_once('../Utils/UtilsLocalization.php');
require_once('../Utils/UtilsRoute.php');

class AdminTaopixOnlineImageServersAdmin_view
{
	static function displayGrid()
	{
		global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminTaopixOnlineServers');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        $smarty->displayLocale('admin/taopixonlineimageservers/imageserversgrid.tpl');
	}

	static function getGridData($pServerDataArray)
    {
    	global $gConstants;

		$smarty = SmartyObj::newSmarty('AdminTaopixOnlineServers');

		echo '[';

		if ($pServerDataArray['data']['result'] == '')
		{
			if (array_key_exists('servers', $pServerDataArray['data']))
			{
				$itemCount = count($pServerDataArray['data']['servers']);

				echo '[' . $itemCount . '],';

				for ($i = 0; $i < $itemCount; $i++)
				{
					$server = $pServerDataArray['data']['servers'][$i];

					echo "['" . $server['id'] . "',";
					echo "'" . UtilsObj::encodeString($server['code'], true) . "',";
					echo "'" . UtilsObj::encodeString($server['url'], true) . "',";
					echo "'" . $server['preference'] . "',";
					echo "'" . $server['lastconnection'] . "',";
					echo "'" . $server['lastsuccess'] . "',";
					echo "'" . $server['ping'] . "',";
					echo "'" . UtilsObj::encodeString($server['error'], true) . "',";
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
			echo "[0],['', '', '', '', '', '', '', '', '', '" . $smarty->get_config_vars($pServerDataArray['data']['result']) . "']";
		}

		echo ']';
	}

	static function displayEntry($pTitle, $pID, $pCode, $pURL, $pActive, $pPreference)
	{
		$smarty = SmartyObj::newSmarty('AdminTaopixOnlineServers');

		$smarty->assign('serverID', $pID);
		$smarty->assign('title', $smarty->get_config_vars($pTitle));
		$smarty->assign('serverurl', UtilsObj::encodeString($pURL, true));
		$smarty->assign('code', UtilsObj::encodeString($pCode, true));
		$smarty->assign('preference', $pPreference);
		$smarty->assign('active', $pActive);

		$smarty->displayLocale('admin/taopixonlineimageservers/imageserversedit.tpl');
	}

	static function displayAdd()
	{
		self::displayEntry('str_TitleAddServer', 0, '', '', 0, 0);
	}

	static function displayEdit($pResultArray)
	{
		$server = $pResultArray['server'];
		self::displayEntry('str_TitleEditServer', $server['id'], $server['code'], $server['url'], $server['active'], $server['preference']);
	}

	static function imageServerSave($pResultArray)
    {

       	$smarty = SmartyObj::newSmarty('AdminTaopixOnlineServers');

       	$resultData = '';

    	if ($pResultArray['success'])
        {
			$resultData = '{
					"success":true,
					"data":
					{
						"id":' . $pResultArray['data']['id'] . ',
						"code":"' . UtilsObj::encodeString($pResultArray['data']['code'],true) . '",
						"url":"' . UtilsObj::encodeString($pResultArray['data']['url'],true) . '",
						"preference":"' . $pResultArray['data']['preference'] . '",
        				"lastconnection":"",
        				"lastsuccess":"",
        				"ping":"",
        				"error":"",
        				"active":"' . $pResultArray['data']['active'] . '"
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

    static function updateImageServerGrid($pResultArray)
    {

        global $gSession;

		$resultData = '{
							"success":true,
							"data":{}
						}';

        if (!$pResultArray['success'])
        {
        	$smarty = SmartyObj::newSmarty('AdminTaopixOnlineServers');
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
}

?>