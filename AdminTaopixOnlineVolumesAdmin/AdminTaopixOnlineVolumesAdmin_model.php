<?php

require_once('../libs/internal/curl/Curl.php');

class AdminTaopixOnlineVolumesAdmin_model
{
    static function getGridData()
    {
        global $ac_config;

        $resultArray = Array();
		$serverURL = $ac_config['TAOPIXONLINEURL'];

        $serverID = UtilsObj::getGETParam('serverid');
		$dataToEncrypt = array('cmd' => 'GETSERVERVOLUMES', 'data' => array('serverid' => $serverID));

		$volumeListDataArray = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt);

        if ($volumeListDataArray['error'] == '')
        {
			$resultArray = $volumeListDataArray['data'];
        }

        return $resultArray;
	}

	static function displayEdit($pVolumeID)
    {
        global $gSession;
        global $ac_config;

        $systemConfigArray = DatabaseObj::getSystemConfig();
        $resultArray = Array();
        $serverURL = $ac_config['TAOPIXONLINEURL'];
		$dataToEncrypt = array('cmd' => 'GETVOLUME', 'data' => array('id' => $pVolumeID));

		$volumeListDataArray = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt);

        if ($volumeListDataArray['error'] == '')
        {
            $resultArray = $volumeListDataArray['data'];
            $volume = &$resultArray['volume'];

            if (($volume['type'] == 2) && ($volume['accesskey'] != '') && ($volume['secret'] != ''))
            {
            	$volume['accesskey'] = UtilsObj::decryptData($volume['accesskey'], $systemConfigArray['secret'], true);
            	$volume['secret'] = UtilsObj::decryptData($volume['secret'], $systemConfigArray['secret'], true);
            }
        }

        return $resultArray;
	}

	static function addEditVolume()
    {
        global $gSession;
        global $ac_config;

        $resultArray = Array();
        $serverURL = $ac_config['TAOPIXONLINEURL'];

		$volumeType = UtilsObj::getPOSTParam('volumetype');
        $volumeID =  UtilsObj::getPOSTParam('volumeid');
        $serverID = UtilsObj::getPOSTParam('serverid');
        $volumeCode = UtilsObj::getPOSTParam('volumecode');
        $volumeRoot = trim(UtilsObj::getPOSTParam('root'));
        $preference = UtilsObj::getPOSTParam('preference');
        $active = UtilsObj::getPOSTParam('isactive');
		$headRoom = floatval(UtilsObj::getPOSTParam('headroom'));
		$path = '';
		$free = 0.00;
		$storageRegion = UtilsObj::getPOSTParam('storageregion');
		$storageName = UtilsObj::getPOSTParam('storagename');
		$accessKey = trim(UtilsObj::getPOSTParam('accesskey'));
	    $secret = trim(UtilsObj::getPOSTParam('secret'));

        // resolve the storeage class from the the front end into the constant
        // missing or incorrect value will result in the storeage class being standard
        switch (UtilsObj::getPOSTParam('storageclass', 'storageclassstandard'))
        {
            case 'storageclassstandard':
            {
                $storageClass = TPX_BUCKET_STORAGE_CLASS_STANDARD;
                break;
            }
            case 'storageclassreduced':
            {
                $storageClass = TPX_BUCKET_STORAGE_CLASS_REDUCED_REDUNDANCY;
                break;
            }
            case 'storageclassinfrequent':
            {
                $storageClass = TPX_BUCKET_STORAGE_CLASS_INFREQUENT_ACCESS;
                break;
            }
            default:
            {
                $storageClass = TPX_BUCKET_STORAGE_CLASS_STANDARD;
                break;
            }
        }

		$uploadurl = trim(UtilsObj::getPOSTParam('uploadurl'), "\\");
		$downloadurl = trim(UtilsObj::getPOSTParam('downloadurl'), "\\");
		$assetype = UtilsObj::getPOSTParam('assettype');

		if ($volumeType == 2)
		{
			$volumeRoot = '';
			$headRoom = 0.00;

			if (($accessKey != '') && ($secret != ''))
			{
				$accessKey = UtilsObj::encryptData($accessKey, $gSession['licensekeydata']['systemkey'], true);
				$secret = UtilsObj::encryptData($secret, $gSession['licensekeydata']['systemkey'], true);
			}
		}
		else
		{
			$storageRegion = '';
			$storageName = '';
			$accessKey = '';
			$secret = '';
			$storageClass = 0;
			$uploadurl = '';
			$downloadurl = '';

			// Remove trailing \ from volume root.
			$volumeRoot = rtrim($volumeRoot, '\\');
		}

        if ($volumeID > 0)
        {
			$dataToEncrypt = array('cmd' => 'EDITVOLUME',
					'data' => array('id' => $volumeID, 'code' => $volumeCode, 'root' => $volumeRoot, 'serverid' => $serverID, 'headroom' => $headRoom,
					'preference' => $preference, 'active' => $active, 'type' => $volumeType, 'storageregion' => $storageRegion, 'storagename' => $storageName, 'accesskey' => $accessKey,
					'secret' => $secret, 'storageclass' => $storageClass, 'uploadurl' => $uploadurl, 'downloadurl' => $downloadurl, 'assettype' => $assetype));
        }
        else
        {
        	$volumeID = 0;
			$dataToEncrypt = array('cmd' => 'ADDVOLUME',
					'data' => array('owner' => '', 'code' => $volumeCode, 'root' => $volumeRoot, 'serverid' => $serverID, 'headroom' => $headRoom,
					'preference' => $preference, 'path' => $path, 'free' => $free, 'volumeid' => $volumeID, 'active' => $active,
					'type' => $volumeType, 'storageregion' => $storageRegion, 'storagename' => $storageName, 'accesskey' => $accessKey, 'secret' => $secret, 'storageclass' => $storageClass,
					'uploadurl' => $uploadurl, 'downloadurl' => $downloadurl, 'assettype' => $assetype));
        }

		$addEditVolumeData = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt);

        if ($addEditVolumeData['error'] == '')
        {
			$resultArray = $addEditVolumeData['data'];

            if ($resultArray['success'])
            {
            	if ($volumeID == 0)
            	{
            		$volumeID = $resultArray['volumeid'];
				}
				else
				{
					// online returns an empty string instead of data on an edit so we need to replace it with an array
					$resultArray['data'] = array();
				}
            }
            else
            {
            	$resultArray['errorparam'] = $resultArray['error'];
            }
        }
        else
        {
            $resultArray['success'] = false;

            if ($volumeID > 0)
            {
				$resultArray['errorparam'] = 'str_WarningUnableToUpdateVolume';
				// online returns an empty string instead of data on an edit so we need to replace it with an array
				$resultArray['data'] = array();
            }
            else
            {
                $resultArray['errorparam'] = 'str_WarningUnableToAddVolume';
            }
        }

        $resultArray['data']['id'] = $volumeID;
        $resultArray['data']['code'] = $volumeCode;
        $resultArray['data']['root'] = $volumeRoot;
        $resultArray['data']['headroom'] = $headRoom;
        $resultArray['data']['preference'] = $preference;
        $resultArray['data']['active'] = $active;

        return $resultArray;
	}

	static function activateVolume()
	{
        global $ac_config;

        $resultArray = Array();
        $serverURL = $ac_config['TAOPIXONLINEURL'];

		$ids = UtilsObj::getPOSTParam('ids');
        $idListArray = explode(',', $ids);
        $active = UtilsObj::getPOSTParam('active');

        if ($active == '0')
        {
        	$cmd = 'DEACTIVATEVOLUMES';
        }
        else
        {
        	$cmd = 'ACTIVATEVOLUMES';
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
            $resultArray['error'] = 'str_WarningUnableToActivateVolume';

            if ($active == '0')
            {
                $resultArray['error'] = 'str_WarningUnableToDeactivateVolume';
            }
        }

        return $resultArray;
	}

	static function deleteVolume()
	{
        global $ac_config;

        $resultArray = Array();
        $serverURL = $ac_config['TAOPIXONLINEURL'];

		$ids = UtilsObj::getPOSTParam('ids');
        $idListArray = explode(',', $ids);
		$dataToEncrypt = array('cmd' => 'DELETEVOLUMES', 'data' => array('ids' => $idListArray));

		$serverDataArray = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt);

        if ($serverDataArray['error'] == '')
        {
            $resultArray = $serverDataArray['data'];
        }
        else
        {
            $resultArray['success'] = false;
            $resultArray['error'] = 'str_WarningUnableToDeleteVolume';
        }

        return $resultArray;
	}
}
?>
