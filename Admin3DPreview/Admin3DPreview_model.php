<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../libs/internal/curl/Curl.php');

class Admin3DPreview_model
{
	static function modelListGrid()
	{
		$resultArray = array();
		$result = '';
        $resultParam = '';

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;

		return $resultArray;
	}

	static function getGridData()
	{
		global $ac_config;

		$returnArray = UtilsObj::getReturnArray();
		$orderBy = UtilsObj::getPOSTParam('sort', '');
		$orderDirection = UtilsObj::getPOSTParam('dir', 'ASC');

		if ($orderBy === 'modeltype')
		{
			$orderBy = 'subtype';
		}
		
		$dataToSend = array(
			'orderby' => $orderBy,
			'orderdirection' => $orderDirection
		);

		$serverURL = $ac_config['TAOPIXONLINEURL'];
		$dataToEncrypt = array('cmd' => 'GET3DMODELSLIST', 'data' => $dataToSend);

		$modelListDataArray = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt, false);

		if ($modelListDataArray['error'] == '')
        {
            $returnArray = $modelListDataArray['data'];
        }
        else
        {
        	$returnArray['error'] = 'str_ErrorConnectFailure';
        }

		return $returnArray;
	}

	static function addDisplay($modelID)
	{
		global $ac_config;

		$returnArray = UtilsObj::getReturnArray('data');
		$modelCode = '';
		$modelName = '';
		$modelFileName = '';
		$active = 0;

		if ($modelID)
		{
			$dataToSend = array (
				'modelid' => $modelID,
				'modelcode' => $modelCode,
				'modelname' => $modelName,
				'modelfilename' => $modelFileName,
				'active' => $active
			);

			$serverURL = $ac_config['TAOPIXONLINEURL'];
			$dataToEncrypt = array('cmd' => 'GET3DMODEL', 'data' => $dataToSend);

			$modelDataArray = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt, false);

			if ($modelDataArray['error'] == '')
			{
				$returnArray = $modelDataArray['data'];
			}
			else
			{
				$returnArray['error'] = $modelDataArray['error'];
			}
		}

		return $returnArray;
	}

	static function deleteModel()
	{
		global $ac_config;
		global $gSession;

		$systemConfigArray = DatabaseObj::getSystemConfig();
		$returnArray = UtilsObj::getReturnArray();
		$resourceCodeList = UtilsObj::getPOSTParam('resourcecodelist', array());

		$returnArray['data']['deletefailed'] = false;

		$dataToSend = array(
			'resourcecodelist' => $resourceCodeList
		);

		$serverURL = $ac_config['TAOPIXONLINEURL'];
		$dataToEncrypt = array('cmd' => 'DELETE3DMODEL', 'data' => $dataToSend);

		$deleteModelResult = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt, false);

		if ($deleteModelResult['error'] == '')
        {
			$returnArray['data'] = $deleteModelResult['data']['data'];

			$deleteFailedCount = count($returnArray['data']['deletefailed']);

			if ($deleteFailedCount > 0)
			{
				foreach($returnArray['data']['deletefailed'] as $modelcode)
				{
					DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
															'ADMIN', '3DPREVIEWMODEL-DELETE', 'Delete failed for 3D model code: ' . $modelcode, 1);
				}

				if ($deleteFailedCount == 0)
				{
					$returnArray['error'] = 'str_WarningUnableToDeleteModelSingle';
				}
				else
				{
					$returnArray['error'] = 'str_WarningUnableToDeleteModelPlural';
					$returnArray['errorparam'] = count($returnArray['data']['deletefailed']);
				}
			}

			if (count($returnArray['data']['deletedmodels']) > 0)
			{
				foreach($returnArray['data']['deletedmodels'] as $modelcode)
				{
					DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
															'ADMIN', '3DPREVIEWMODEL-DELETE', 'Deleted 3D model code: ' . $modelcode, 1);
				}

				// delete link into control centre
				$dbObj = DatabaseObj::getGlobalDBConnection();
				if ($dbObj)
				{
					$resourceCode = '';

					if ($stmt = $dbObj->prepare('DELETE FROM `PRODUCTONLINESYSTEMRESOURCELINK` WHERE `resourcecode` = ? AND `type` = ' . TPX_SYSTEM_RESOURCE_TYPE_3DMODEL))
					{
						if ($stmt->bind_param('s', $resourceCode))
						{
							foreach($returnArray['data']['deletedmodels'] as $dm)
							{
								$resourceCode = $dm;
								if (! $stmt->execute())
								{
									$returnArray['error']  = 'PRODUCTONLINESYSTEMRESOURCELINK delete execute error: ' . $dbObj->error;
								}
							}
						}
						else
						{
							$returnArray['error']  = 'PRODUCTONLINESYSTEMRESOURCELINK delete bind param error: ' . $dbObj->error;
						}

						$stmt->free_result();
						$stmt->close();
						$stmt = null;
					}
					else
					{
						$returnArray['error']  = 'PRODUCTONLINESYSTEMRESOURCELINK delete prepare error: ' . $dbObj->error;
					}

					$dbObj->close();
				}
				else
				{
					$returnArray['error']  = __FUNCTION__ . 'Unable to connect to the database: ' . $dbObj->error;
				}
			}
		}
		else
        {
        	$returnArray['error'] = 'str_ErrorConnectFailure';
        }

		return $returnArray;
	}

	static function link3DPreviewModelToProducts($pModelCode, $pProductCodes)
	{
		global $ac_config;
		global $gSession;

		$returnArray = UtilsObj::getReturnArray();

		$systemConfigArray = DatabaseObj::getSystemConfig();

		$tenantID = UtilsObj::getArrayParam($systemConfigArray, 'tenantid', -1);

		$dataToSend = array (
			'modelcode' => $pModelCode,
			'productcodes' => $pProductCodes,
			'tenantid' => $tenantID
		);

		$serverURL = $ac_config['TAOPIXONLINEURL'];
		$dataToEncrypt = array('cmd' => 'LINK3DMODEL', 'data' => $dataToSend);

		$addModelDataArray = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt, false);
		
		if ($addModelDataArray['error'] == '')
		{
			$addModelResult = $addModelDataArray['data'];

			if ($addModelResult['error'] == '')
			{
				$returnArray['data'] = $addModelDataArray['data']['data'];

				foreach($returnArray['data']['deletedlink'] as $dl)
				{
					DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
															'ADMIN', '3DPREVIEWMODEL-LINK3DMODELTOPRODUCT', 'Removed link previous link to ' . $dl['productcode'], 1);
				}

				foreach($returnArray['data']['createdlink'] as $cl)
				{
					DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
															'ADMIN', '3DPREVIEWMODEL-LINK3DMODELTOPRODUCT', 'Created link ' . $cl['resourcecode'] . ' model to ' . $cl['productcode'], 1);
				}

				// insert link to control centre
				$dbObj = DatabaseObj::getGlobalDBConnection();
				if ($dbObj)
				{
					$productCode = '';

					if ($stmt = $dbObj->prepare('DELETE FROM `PRODUCTONLINESYSTEMRESOURCELINK` WHERE `productcode` = ? AND `type` = ' . TPX_SYSTEM_RESOURCE_TYPE_3DMODEL))
					{
						if ($stmt->bind_param('s', $productCode))
						{
							$productCodesArray = explode(',', $pProductCodes);

							foreach ($productCodesArray as $product)
							{
								$productCode = $product;

								if (! $stmt->execute())
								{
									$returnArray['error']  = 'PRODUCTONLINESYSTEMRESOURCELINK delete execute error: ' . $dbObj->error;
								}
							}
						}
						else
						{
							$returnArray['error']  = 'PRODUCTONLINESYSTEMRESOURCELINK delete bind param error: ' . $dbObj->error;
						}

						$stmt->free_result();
						$stmt->close();
						$stmt = null;
					}
					else
					{
						$returnArray['error']  = 'PRODUCTONLINESYSTEMRESOURCELINK delete prepare error: ' . $dbObj->error;
					}

					if ($stmt = $dbObj->prepare('INSERT INTO `PRODUCTONLINESYSTEMRESOURCELINK` (`resourcecode`, `productcode`, `type`) VALUES (?, ?, ' . TPX_SYSTEM_RESOURCE_TYPE_3DMODEL . ')'))
					{
						if ($stmt->bind_param('ss', $pModelCode, $productCode))
						{
							$productCodesArray = explode(',', $pProductCodes);

							foreach ($productCodesArray as $product)
							{
								$productCode = $product;

								if (! $stmt->execute())
								{
									$returnArray['error']  = 'PRODUCTONLINESYSTEMRESOURCELINK insert execute error: ' . $dbObj->error;
								}
							}
						}
						else
						{
							$returnArray['error']  = 'PRODUCTONLINESYSTEMRESOURCELINK insert bind param error: ' . $dbObj->error;
						}

						$stmt->free_result();
						$stmt->close();
						$stmt = null;
					}
					else
					{
						$returnArray['error']  = 'PRODUCTONLINESYSTEMRESOURCELINK insert prepare error: ' . $dbObj->error;
					}
					$dbObj->close();
				}
				else
				{
					$returnArray['error']  = __FUNCTION__ . 'Unable to connect to the database: ' . $dbObj->error;
				}
			}
			else
			{
				$returnArray['error'] = $addModelResult['error'];
			}
		}
		else
        {
        	$returnArray['error'] = 'str_ErrorConnectFailure';
        }

		return $returnArray;
	}

	static function unLink3DPreviewModelToProducts($pProductCodes)
	{
		global $ac_config;
		global $gSession;

		$returnArray = UtilsObj::getReturnArray();

		$systemConfigArray = DatabaseObj::getSystemConfig();

		$tenantID = UtilsObj::getArrayParam($systemConfigArray, 'tenantid', -1);

		$dataToSend = array (
			'productcodes' => $pProductCodes,
			'tenantid' => $tenantID
		);

		$serverURL = $ac_config['TAOPIXONLINEURL'];
		$dataToEncrypt = array('cmd' => 'UNLINK3DMODEL', 'data' => $dataToSend);

		$addModelDataArray = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt, false);

		if ($addModelDataArray['error'] == '')
		{
			$addModelResult = $addModelDataArray['data'];

			if ($addModelResult['error'] == '')
			{
				$returnArray['data'] = $addModelDataArray['data']['data'];
				
				// if this method call has been triggered by a task, gSession won't exist
				if (null !== $gSession)
				{ 
					foreach($returnArray['data']['deletedlink'] as $dl)
					{
						DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
																'ADMIN', '3DPREVIEWMODEL-UNLINK3DMODELTOPRODUCT', 'Removed link to ' . $dl['productcode'], 1);
					}
				}

				// delete link in control centre
				$dbObj = DatabaseObj::getGlobalDBConnection();
				if ($dbObj)
				{
					$productCode = '';

					if ($stmt = $dbObj->prepare('DELETE FROM `PRODUCTONLINESYSTEMRESOURCELINK` WHERE `productcode` = ? AND `type` = ' . TPX_SYSTEM_RESOURCE_TYPE_3DMODEL))
					{
						if ($stmt->bind_param('s', $productCode))
						{
							$productCodesArray = explode(',', $pProductCodes);

							foreach ($productCodesArray as $product)
							{
								$productCode = $product;

								if (! $stmt->execute())
								{
									$returnArray['error']  = 'PRODUCTONLINESYSTEMRESOURCELINK delete execute error: ' . $dbObj->error;
								}
							}
						}
						else
						{
							$returnArray['error']  = 'PRODUCTONLINESYSTEMRESOURCELINK delete bind param error: ' . $dbObj->error;
						}

						if ($stmt)
						{
							$stmt->free_result();
							$stmt->close();
							$stmt = null;
						}
					}
					else
					{
						$returnArray['error']  = 'PRODUCTONLINESYSTEMRESOURCELINK delete prepare error: ' . $dbObj->error;
					}

					$dbObj->close();
				}
				else
				{
					$returnArray['error']  = __FUNCTION__ . 'Unable to connect to the database: ' . $dbObj->error;
				}
			}
			else
			{
				$returnArray['error'] = $addModelResult['error'];
			}
		}
		else
        {
        	$returnArray['error'] = 'str_ErrorConnectFailure';
        }

		return $returnArray;
	}

	static function upload3DPreviewModel($pModelData, $pModelFile, $pAction)
	{
		global $ac_config;

		$returnArray = UtilsObj::getReturnArray();
		$fileUploadErrorCode = (isset($pModelFile)) ? UtilsObj::getArrayParam($pModelFile, 'error') : UPLOAD_ERR_NO_FILE;

		if (($fileUploadErrorCode == UPLOAD_ERR_NO_FILE) || ($fileUploadErrorCode == UPLOAD_ERR_OK))
		{
			// post the original file info as posting it via curl loses the original file info

			$modelPath = UtilsObj::getArrayParam($pModelFile, 'tmp_name');

			$callData = array();
			$callData['cmd'] = 'UPLOAD3DMODEL';
			$callData['data']['filename'] = UtilsObj::getArrayParam($pModelFile, 'name');
			$callData['data']['mimetype'] = UtilsObj::getArrayParam($pModelFile, 'type');
			$callData['data']['filesize'] = UtilsObj::getArrayParam($pModelFile, 'size');
			$callData['data']['modelid'] = UtilsObj::getArrayParam($pModelData, 'modelid', -1);
			$callData['data']['action'] = $pAction;
			$callData['data']['modelcode'] = UtilsObj::getArrayParam($pModelData, 'modelcode');
			$callData['data']['modelname'] = UtilsObj::getArrayParam($pModelData, 'modelname');
			$callData['data']['modelfilename'] = UtilsObj::getArrayParam($pModelData, 'modelfilename');
			$callData['data']['active'] = UtilsObj::getArrayParam($pModelData, 'active');

			$postFileArray = array();

			// $_FILES may be empty if the model has been editted but not had a new file uploaded
			if ($fileUploadErrorCode == UPLOAD_ERR_OK)
			{
				$postFileArray['name'] = 'filehandle';

				// check to see if the PHP version being used is 5.5.0 or higher
				// if it is then we need to use the curl_file_create method.
				// this is because the old way of prefixing an @ character to the filename is deprecated
				if (version_compare(PHP_VERSION, '5.5.0') >= 0)
				{
					$postFileArray['value'] = curl_file_create($modelPath);
				}
				else
				{
					$postFileArray['value'] = '@' . $modelPath;
				}
			}

			$postFileResult = CurlObj::sendByPost(UtilsObj::correctPath($ac_config['TAOPIXONLINEURL']), 'AdminAPI.callback', $callData, $postFileArray);

			if ($postFileResult['error'] == '')
			{
				if ($postFileResult['data']['error'] != '')
				{
					$returnArray = $postFileResult['data'];
				}
			}
			else
			{
				$returnArray['error'] = $postFileResult['error'];
			}
		}
		else
		{
			// we may get the UPLOAD_ERR_NO_FILE status due to editing a model without uploading a new model file
			$returnArray['error'] = 'File upload error: ' . UtilsObj::translateUploadError($fileUploadErrorCode);
		}
		
		return $returnArray;
	}

	static function setModelActivateStatus($pModelIDList, $pActive)
	{
		global $ac_config;
		global $gSession;

		$returnArray = UtilsObj::getReturnArray();

		$modelIDArray = explode(',', $pModelIDList);

		$dataToSend = array(
			'modelids' => $modelIDArray,
			'active' => $pActive
		);

		$serverURL = $ac_config['TAOPIXONLINEURL'];
		$dataToEncrypt = array('cmd' => 'SET3DMODELACTIVESTATUS', 'data' => $dataToSend);

		$modelListDataArray = CurlObj::sendByPut($serverURL, 'AdminAPI.callback', $dataToEncrypt, false);

		if ($modelListDataArray['error'] == '')
        {
            $returnArray = $modelListDataArray['data'];

			foreach($modelIDArray as $modelID)
			{
				if ($pActive == 1)
				{
					$action = '3DMODEL-ACTIVATE';
				}
				else
				{
					$action = '3DMODEL-DEACTIVATE';
				}
				
				DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
							'ADMIN', $action, 'MODELID = ' . $modelID, 1);
			}
        }
        else
        {
        	$returnArray['error'] = 'str_ErrorConnectFailure';
        }

		return $returnArray;
	}
}
