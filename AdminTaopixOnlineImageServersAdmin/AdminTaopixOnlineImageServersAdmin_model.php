<?php

require_once('../libs/internal/curl/Curl.php');

class AdminTaopixOnlineImageServersAdmin_model
{
    static function getGridData()
    {
        global $ac_config;

        $resultArray = Array();
        $serverURL = $ac_config['TAOPIXONLINEURL'];
		$dataToEncrypt = array('cmd' => 'GETSERVERS', 'data' => array());

		$serverListDataArray = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt);

        if ($serverListDataArray['error'] == '')
        {
			$resultArray['data'] = $serverListDataArray['data'];
        }
        else
        {
        	$resultArray['data']['result'] = 'str_ErrorConnectFailure';
        }

        return $resultArray;
	}

	static function displayEdit($pServerID)
    {
        global $ac_config;

        $resultArray = Array();
        $serverURL = $ac_config['TAOPIXONLINEURL'];
		$dataToEncrypt = array('cmd' => 'GETSERVER', 'data' => array('id' => $pServerID));

		$serverListDataArray = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt);

        if ($serverListDataArray['error'] == '')
        {
            $resultArray = $serverListDataArray['data'];
        }
        else
        {
        	$resultArray['data']['result'] = 'str_ErrorConnectFailure';
        }

        return $resultArray;
	}

	static function addEditImageServer()
    {
        global $ac_config;

        $resultArray = Array();
        $serverURL = $ac_config['TAOPIXONLINEURL'];

        $serverID = UtilsObj::getPOSTParam('serverid');
        $serverCode = UtilsObj::getPOSTParam('imageservercode');
        $imageServerURL = UtilsObj::correctPath(UtilsObj::getPOSTParam('serverurl'));
        $preference = UtilsObj::getPOSTParam('preference');
        $active = UtilsObj::getPOSTParam('isactive');

        if ($serverID > 0)
		{
			$dataToEncrypt = array('cmd' => 'EDITSERVER',
					'data' => array('id' => $serverID, 'code' => $serverCode, 'url' => $imageServerURL, 'active' => $active, 'preference' => $preference));
		}
		else
		{
			$dataToEncrypt = array('cmd' => 'ADDSERVER',
					'data' => array('code' => $serverCode, 'url' => $imageServerURL, 'active' => $active, 'preference' => $preference));
		}

		$addEditServerData = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt);

        if ($addEditServerData['error'] == '')
        {
			$resultArray = $addEditServerData['data'];

            if (!\is_array($resultArray['data'])) {
                $resultArray['data'] = [];
            }
            
            if (($resultArray['success']) && ($serverID == 0))
            {
            	$serverID = $resultArray['serverid'];
            }
        }
        else
        {
            $resultArray['success'] = false;

            if ($serverID > 0)
            {
                $resultArray['error'] = 'str_WarningUnableToUpdateServer';
            }
            else
            {
                $resultArray['error'] = 'str_WarningUnableToAddServer';
            }
        }

        $resultArray['errorparam'] = $addEditServerData['error'];
        $resultArray['data']['id'] = $serverID;
        $resultArray['data']['code'] = $serverCode;
        $resultArray['data']['url'] = $imageServerURL;
        $resultArray['data']['preference'] = $preference;
        $resultArray['data']['active'] = $active;

        return $resultArray;
	}

	static function activateImageServer()
	{
        global $ac_config;

        $resultArray = Array();
		$serverURL = $ac_config['TAOPIXONLINEURL'];

		$ids = UtilsObj::getPOSTParam('ids');
        $idListArray = explode(',', $ids);
        $active = UtilsObj::getPOSTParam('active');

        if ($active == '0')
        {
        	$cmd = 'DEACTIVATESERVERS';
        }
        else
        {
        	$cmd = 'ACTIVATESERVERS';
        }

		$dataToEncrypt = array('cmd' => $cmd, 'data' => array('ids' => $idListArray));

		$serverDataArray = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt);

        if ($serverDataArray['error'] == '')
        {
			$resultArray = $serverDataArray['data'];
        }
        else
        {
            $resultArray['success'] = false;
            $resultArray['error'] = 'str_WarningUnableToActivateServer';

            if ($active == '0')
            {
                $resultArray['error'] = 'str_WarningUnableToDeactivateServer';
            }
        }

        return $resultArray;
	}

	static function deleteImageServer()
	{
        global $ac_config;

        $resultArray = Array();
		$serverURL = $ac_config['TAOPIXONLINEURL'];

		$ids = UtilsObj::getPOSTParam('ids');
        $idListArray = explode(',', $ids);

		$dataToEncrypt = array('cmd' => 'DELETESERVERS', 'data' => array('ids' => $idListArray));

		$serverDataArray = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt);

        if ($serverDataArray['error'] == '')
        {
            $resultArray = $serverDataArray['data'];
        }
        else
        {
            $resultArray['success'] = false;
            $resultArray['error'] = 'str_WarningUnableToDeleteServer';
        }

        return $resultArray;
	}
}
?>
