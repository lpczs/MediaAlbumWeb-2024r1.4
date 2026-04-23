<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

class onlineAssetPush
{
	// define default settings for this task
	static function register()
	{
		$defaultSettings = array();

	   /*
		* $defaultSettings('type') defines type of tasks
		* 0 - scheduled
		* 1 - service
		* 2 - manual
		*/

		$defaultSettings['type'] = '0';
		$defaultSettings['code'] = 'TAOPIX_ONLINEASSETPUSH';
		$defaultSettings['name'] = 'it asset push in italian<p>fr asset push in french<p>es asset push in spanish';

	   /*
		* $defaultSettings('intervalType') defines inteval value
		* 1 - Number of minutes
		* 2 - Exact time of the day
		* 3 - Number of days
		*/
		$defaultSettings['intervalType']  = '1';
		$defaultSettings['intervalValue'] = '5';
		$defaultSettings['maxRunCount']  = '10';
		$defaultSettings['deleteCompletedDays'] = '5';

		return $defaultSettings;
	}

    static function processProductCollection($pEventRecordID, $pApplicationFileRecordID, $pApplicationFileType, $pDeletedApplicationAssetRef,
		$pSystemConfigArray, $pProductCollectionsInQueue)
    {
    	global $ac_config;

    	$resultArray = array();
    	$resultArray['result'] = 2;
    	$resultArray['resultparam'] = '';
    	$resultArray['data'] = array();

    	$result = 2;
    	$resultParam = '';
    	$prdFileName = '';
    	$assetData = '';
    	$applicationFile = self::getApplicationFileFromID($pApplicationFileRecordID);
		$processProductCollectionPut = true;
    	$processProductCollectionPush = true;
    	$processProductCollectionData = true;
    	$overWrite = false;

    	if (array_key_exists($pSystemConfigArray['key'] . '.' . $applicationFile['assetref'], $pProductCollectionsInQueue))
    	{
    		if (strtotime($pProductCollectionsInQueue[$pSystemConfigArray['key'] . '.' . $applicationFile['assetref']]['datemodified']) > strtotime($applicationFile['datemodified']))
    		{
				$processProductCollectionData = false;
    		}
    		else
    		{
    			$overWrite = true;
    		}
    	}

		if ($processProductCollectionData)
		{
			// open the zip file to read the asset.dat file
			$zipFilePath = $ac_config['INTERNALPRODUCTSROOTPATH'] . $applicationFile['assetref'] . '.zip';

            $archive = new ZipArchive();
            $openResult = $archive->open($zipFilePath);
			if (true === $openResult)
			{
                $index = 0;
                while (false !== ($entry = $archive->statIndex($index))) {
                    if (str_ends_with($entry['name'], "{$applicationFile['assetref']}.prd")) {
                        // Found the prd file put the zip entry content into a temp file, and exit the loop.
                        $prdFileName = tempnam(sys_get_temp_dir(), "TAOPIXPRDFile" . $applicationFile['assetref']);
                        file_put_contents($prdFileName, $archive->getFromIndex($index, $entry['size']));
                        break;
                    }
                    $index++;
                }
                $archive->close();
			}
			else
			{
				$processProductCollectionPut = false;
				$processProductCollectionPush = false;
				$result = 1;
				$resultParam = '- Unable to open file ' .  $applicationFile['assetref'] . '.zip Error: ' . $openResult;
			}

			if ($processProductCollectionPut)
			{
				$assetData = $applicationFile['onlinedependencies'];
				$assetDataArray = explode(chr(13), $assetData);

				$onlineAssetData = array();
				$onlineAssetData['assets'] = array();

				$fileCount = count($assetDataArray);
				$serverAssetArray = array();

				if ($fileCount > 1)
				{
					for ($i = 1; $i <= $fileCount-1; $i++)
					{
						$line = $assetDataArray[$i];

						if ($line != '')
						{

							$pieces = explode("\t", $line, 10);

							if ($pieces[1] != '')
							{
								$assetManagementArray = explode('.', $pieces[1]);
								$assetManagementServiceRef = $assetManagementArray[0];
								$assetManagementRef = $assetManagementArray[1];
							}
							else
							{
								$assetManagementServiceRef = '';
								$assetManagementRef = '';
							}

							$assetType = $pieces[0];

							if ($assetType != TPX_APPLICATION_FILE_TYPE_FRAME)
							{
								$keyName = $pieces[2];
								$serverAssetArray[$keyName]['assettype'] = $assetType;
								$serverAssetArray[$keyName]['assetmanagementserviceref'] = $assetManagementServiceRef;
								$serverAssetArray[$keyName]['assetmanagementref'] = $assetManagementRef;
								$serverAssetArray[$keyName]['assetref'] = $pieces[2];
								$serverAssetArray[$keyName]['categoryname'] = $pieces[3];
								$serverAssetArray[$keyName]['name'] = $pieces[4];
								$serverAssetArray[$keyName]['filename'] = $pieces[5];
								$serverAssetArray[$keyName]['filesizebytes'] = 0;
								$serverAssetArray[$keyName]['hiddenfromuser'] = $pieces[6];
								$serverAssetArray[$keyName]['encrypted'] = $pieces[7];
								$serverAssetArray[$keyName]['assetversiondate'] = $pieces[8];
								$serverAssetArray[$keyName]['productcollectioncode'] = $applicationFile['assetref'];
								$serverAssetArray[$keyName]['ownercode'] = $pSystemConfigArray['ownercode'];
								$serverAssetArray[$keyName]['products'] = '';
								$serverAssetArray[$keyName]['active'] = $applicationFile['active'];
								$serverAssetArray[$keyName]['deleted'] = $applicationFile['deleted'];
								$serverAssetArray[$keyName]['webbrandcode'] = $applicationFile['webbrandcode'];
							}
						}
					}

					// query the main server to get a list of online asset data for the collection
					$dataToEncrypt = array('productcollectioncode' => $applicationFile['assetref'], 'tenantid' => $pSystemConfigArray['tenantid']);

					$getAssetsReturn = TaskObj::sendToTaopixOnline('GETASSETSBYPRODUCT', $dataToEncrypt);

					if ($getAssetsReturn['error'] == '')
					{
						$onlineAssetData = $getAssetsReturn['data'];

						if ($onlineAssetData['error'] != 0)
						{
							$processProductCollectionPush = false;
						}
					}
				}
			}

			if ($processProductCollectionPush)
			{
				$onlineProductCollectionAssetArray = Array();
				$onlineAssetCount = count($onlineAssetData['assets']);

				for ($i = 0; $i < $onlineAssetCount; $i++)
				{
					$onlineProductCollectionAssetArray[$onlineAssetData['assets'][$i]['assetref']]['productcollectioncode'] = $applicationFile['assetref'];
				}

				$assetArray = Array();
				$assetArray['new'] = Array();
				$assetArray['updated'] = Array();
				$assetsToBeUpdatedArray = Array();
				$deprecatedAssetArray = Array();

				// store a list of the asset data and temp location of any new assets
				$assetsInZip = array();

				// flag in the zip file needs checking. this is quicker than counting the size of the array
				$checkZipFile = false;

				// cache the temp locations of all the file types which are to be created
				$tempPathCacheArray = array(TPX_APPLICATION_FILE_TYPE_MASK => '', TPX_APPLICATION_FILE_TYPE_BACKGROUND => '', TPX_APPLICATION_FILE_TYPE_PICTURE => '');

				// now need to loop round serverArray and the array
				// to see if we need to update or just add.
				foreach ($serverAssetArray as $asset => $assetData)
				{
					/* If the Asset on the local machine is Online then update*/
					if (array_key_exists($asset, $onlineProductCollectionAssetArray))
					{
						$updateAssetItem['assetref'] = $asset;
						$updateAssetItem['webbrandcode'] = $assetData['webbrandcode'];
						$updateAssetItem['tenantid'] = $pSystemConfigArray['tenantid'];
						$updateAssetItem['productcollectioncode'] = $assetData['productcollectioncode'];
						$updateAssetItem['assetrecord'] = Array(
							'categoryname' => $assetData['categoryname'],
							'name' => $assetData['name'],
							'hiddenfromuser' => $assetData['hiddenfromuser'],
							'encrypted' => $assetData['encrypted'],
							'assetversiondate' => $assetData['assetversiondate'],
							'ownercode' => $pSystemConfigArray['ownercode'],
							'tenantid' => $pSystemConfigArray['tenantid'],
							'products' => '',
							'webbrandcode' => $applicationFile['webbrandcode']);

						$assetArray['updated'][] = $updateAssetItem;
					}
					else /* If the Asset on the local machine is not Online then add it */
					{
						$checkZipFile = true;
						$filePath = $assetData['productcollectioncode'] . "/%s/" . $assetData['filename'];
						$subDirectory = 'Products' . DIRECTORY_SEPARATOR . $assetData['productcollectioncode'] . DIRECTORY_SEPARATOR . "%s";
						$typeString = '';

						switch ($assetData['assettype'])
						{
							case TPX_APPLICATION_FILE_TYPE_MASK:
							{
								$typeString = 'Masks';
								break;
							}
							case TPX_APPLICATION_FILE_TYPE_BACKGROUND:
							{
								$typeString = 'Backgrounds';
								break;
							}
							case TPX_APPLICATION_FILE_TYPE_PICTURE:
							{
								$typeString = 'Scrapbook';
								break;
							}
						}

						$filePath = strtolower(sprintf($filePath, $typeString));
						$subDirectory = sprintf($subDirectory, $typeString);

						$tempFileDir = '';

						if ($tempPathCacheArray[$assetData['assettype']] == '')
						{
							$tempFileDir = self::createTempPath($pSystemConfigArray['ownercode'], $subDirectory, $applicationFile['webbrandcode']);
							$tempPathCacheArray[$assetData['assettype']] = $tempFileDir;
						}
						else
						{
							$tempFileDir = $tempPathCacheArray[$assetData['assettype']];
						}

						$assetsInZip[$filePath] = array('temp' => $tempFileDir . $assetData['filename'], 'assetdata' => $assetData);
					}
				}

				if ($checkZipFile)
				{
                    $archive = new ZipArchive();
                    $openResult = $archive->open($zipFilePath);

					if (true === $openResult)
					{
                        $index = 0;
						while (($zip_entry = $archive->statIndex($index)) !== false)
						{
							if (stripos($zip_entry['name'], '.dat') === false)
							{
								// convert the file path of the item in the zip file to lower case so that using it as a key
								// works
								$zipFileEntry = strtolower($zip_entry['name']);

								// has the asseting in zip array got an entry for the file in the zip file
								if (!empty($assetsInZip[$zipFileEntry]))
								{
									// populate a local variable with the array item
									$assetRecord = $assetsInZip[$zipFileEntry];

									$assetRecord['assetdata']['tenantid'] = $pSystemConfigArray['tenantid'];
									$assetRecord['assetdata']['filesizebytes'] = $zip_entry['size'];
									$assetRecord['assetdata']['uploadid'] = 'Push_'.rand();

									// flag that the asset is new so that it gets uploaded
									$assetArray['new'][] = $assetRecord['assetdata'];

									// write the asset to the temp location
									$return = @file_put_contents($assetRecord['temp'], $archive->getFromIndex($index));
								}
							}
                            $index++;
						}

						$archive->close();
					}
					else
					{
						$result = 1;
						$resultParam = '- Unable to open file ' .  $applicationFile['assetref'] . '.zip';
					}
				}

				// We now need to deprecate the assets that are online but are no longer in the product collection
				foreach ($onlineProductCollectionAssetArray as $onlineAsset => $onlineAssetData)
				{
					if (!array_key_exists($onlineAsset, $serverAssetArray))
					{
						$deprecateItem = array();
						$deprecateItem['assetref'] = $onlineAsset;
						$deprecateItem['webbrandcode'] = '';
						$deprecateItem['tenantid'] = $pSystemConfigArray['tenantid'];
						$deprecateItem['productcollectioncode'] = $onlineAssetData['productcollectioncode'];
						$deprecateItem['assetrecord'] = Array('deprecated' => 1);
						$assetArray['updated'][] = $deprecateItem;
					}
				}
			}

			$resultArray['result'] = $result;
			$resultArray['resultparam'] = $resultParam;

			if ($result == 2)
			{
				$resultArray['data'][$pSystemConfigArray['key'] . '.' . $applicationFile['assetref']]['assetdata'] = $assetArray;
				$resultArray['data'][$pSystemConfigArray['key'] . '.' . $applicationFile['assetref']]['datemodified'] = $applicationFile['datemodified'];
				$resultArray['data'][$pSystemConfigArray['key'] . '.' . $applicationFile['assetref']]['filename'] = $prdFileName;
				$resultArray['data'][$pSystemConfigArray['key'] . '.' . $applicationFile['assetref']]['assetref'] = $applicationFile['assetref'];
				$resultArray['data'][$pSystemConfigArray['key'] . '.' . $applicationFile['assetref']]['applicationfilerecordid'] = $pApplicationFileRecordID;
				$resultArray['data'][$pSystemConfigArray['key'] . '.' . $applicationFile['assetref']]['eventrecordid'] = $pEventRecordID;
				$resultArray['data'][$pSystemConfigArray['key'] . '.' . $applicationFile['assetref']]['secret'] = $pSystemConfigArray['secret'];
				$resultArray['data'][$pSystemConfigArray['key'] . '.' . $applicationFile['assetref']]['ownercode'] = $pSystemConfigArray['ownercode'];
				$resultArray['data'][$pSystemConfigArray['key'] . '.' . $applicationFile['assetref']]['tenantid'] = $pSystemConfigArray['tenantid'];
				$resultArray['data'][$pSystemConfigArray['key'] . '.' . $applicationFile['assetref']]['key'] = $pSystemConfigArray['key'];
				$resultArray['overwrite'] = $overWrite;
			}
		}
		else
		{
			$resultArray['data'] = array();
			$resultArray['overwrite'] = false;
		}

		return $resultArray;
    }

    static function processApplicationFile($pEventRecordID, $pApplicationFileRecordID, $pApplicationFileType, $pDeletedApplicationAssetRef,
    																					$pDeletedApplicationWebBrandCode, $pSystemConfigArray)
    {
    	global $ac_config;

    	$result = 2;
		$resultParam  = '';
    	$assetArray = Array();
    	$assetArray['new'] = Array();
		$assetArray['updated'] = Array();
		$applicationFile = Array();

    	if ($pDeletedApplicationAssetRef != '')
    	{
			$updateAssetItem['assetref'] = $pDeletedApplicationAssetRef;
			$updateAssetItem['webbrandcode'] = $pDeletedApplicationWebBrandCode;
			$updateAssetItem['productcollectioncode'] = '';
			$updateAssetItem['tenantid'] = $pSystemConfigArray['tenantid'];
			$updateAssetItem['assetrecord'] = Array('active' => 0);
			$assetArray['updated'][] = $updateAssetItem;
    	}
    	else
    	{
    		$applicationFile = self::getApplicationFileFromID($pApplicationFileRecordID);

			if ($applicationFile['recordid'] != '')
			{
		    	if ($applicationFile['datemodified'] != $applicationFile['datemodifiedonline'])
				{

					$assetTypeName = '';
					$brandPath = '';

					if ($applicationFile['webbrandcode'] != '')
					{
						$brandPath = $applicationFile['webbrandcode'] . DIRECTORY_SEPARATOR;
					}

					switch ($applicationFile['assettype'])
					{
						case TPX_APPLICATION_FILE_TYPE_MASK:
						{
							$filePath = $ac_config['INTERNALAPPLICATIONMASKSROOTPATH'] . $brandPath . $applicationFile['assetref'] . '.zip';
							$assetTypeName = 'Masks';
							break;
						}
						case TPX_APPLICATION_FILE_TYPE_BACKGROUND:
						{
							$filePath = $ac_config['INTERNALAPPLICATIONBACKGROUNDSROOTPATH'] . $brandPath . $applicationFile['assetref'] . '.zip';
							$assetTypeName = 'Backgrounds';
							break;
						}
						case TPX_APPLICATION_FILE_TYPE_PICTURE:
						{
							$filePath = $ac_config['INTERNALAPPLICATIONSCRAPBOOKPICTURESROOTPATH'] . $brandPath . $applicationFile['assetref'] . '.zip';
							$assetTypeName = 'Scrapbook';
							break;
						}
					}

					$tempFileDir = self::createTempPath($pSystemConfigArray['ownercode'], $assetTypeName, $applicationFile['webbrandcode']);

					$tempFileName = $tempFileDir . $applicationFile['assetref'];

					$archive = new ZipArchive();
					$zip = $archive->open($filePath);

					if (true === $zip)
					{
						$index = 0;
						while (false !== ($entry = $archive->statIndex($index)))
						{
							if (stripos($entry['name'], '.dat') === false)
							{
								if ((substr($entry['name'], 0, 1) != '_') && (substr($entry['name'], 0, 1) != '.'))
								{
									$applicationFile['uploadid'] = 'Push_'.rand();
									$applicationFile['ownercode'] = $pSystemConfigArray['ownercode'];
									$applicationFile['tenantid'] = $pSystemConfigArray['tenantid'];
									$applicationFile['productcollectioncode'] = '';
									$applicationFile['assetversiondate'] = $applicationFile['datemodified'];
									unset($applicationFile['datemodified']);

									$return = file_put_contents($tempFileName, $archive->getFromIndex($index, $entry['size']));

									$applicationFile['filesizebytes'] = filesize($tempFileName);

									$assetArray['new'][] = $applicationFile;

								}
							}
							$index++;
						}
						$archive->close();
					}
				}
				else
				{
					$updateAssetItem['assetref'] = $applicationFile['assetref'];
					$updateAssetItem['webbrandcode'] = $applicationFile['webbrandcode'];
					$updateAssetItem['productcollectioncode'] = '';
					$updateAssetItem['tenantid'] = $pSystemConfigArray['tenantid'];
					$updateAssetItem['assetrecord'] = Array('categoryname' => $applicationFile['categoryname'],
															'name' => $applicationFile['name'],
															'hiddenfromuser' => (int) $applicationFile['hiddenfromuser'],
															'encrypted' => (int) $applicationFile['encrypted'],
															'ownercode' => $pSystemConfigArray['ownercode'],
															'products' => $applicationFile['products'],
															'webbrandcode' => $applicationFile['webbrandcode'],
															'active' => $applicationFile['active']);
					$assetArray['updated'][] = $updateAssetItem;
				}
			}
			else
			{
				$resultParam = 'Missing application files record: ' . $pApplicationFileRecordID;
			}
		}

		if ($resultParam == '')
		{

			$assetResultArray = self::uploadOnlineSystemData('', '', '', $assetArray, $pApplicationFileRecordID, $pEventRecordID, true, false,
																$pSystemConfigArray['ownercode'], $pSystemConfigArray['tenantid'],
																$pSystemConfigArray['key'], $pSystemConfigArray['secret']);

			if ($assetResultArray['result'] != '')
			{
				$result = $assetResultArray['result'];
				$resultParam = $assetResultArray['resultparam'];
			}

		}

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;

		return $resultArray;
    }

    static function processLicenseKey($pEventRecordID, $pGroupCode, $pSystemConfigArray)
    {
    	global $ac_config;

		$result = '';
		$resultParam = '';
		$uploadLicenseKeyData = array();

		$licenseKeyDataArray = TaskObj::getLicenseKeyFromCode($pGroupCode);
		$licenseKeyFileName = $licenseKeyDataArray['keyfilename'];

		$callData = array();
		$callData['groupcode'] = $pGroupCode;
		$callData['keyfilename'] = $licenseKeyFileName;

		// check to see if the PHP version being used is 5.5.0 or higher
		// if it is then we need to use the curl_file_create method.
		// this is because the old way of prefixing an @ character to the filename is deprecated

		$postFileArray = array();
		$postFileArray['name'] = 'licensekey';

		if (version_compare(PHP_VERSION, '5.5.0') >= 0)
		{
			$postFileArray['value'] = curl_file_create($ac_config['INTERNALLICENSEKEYSROOTPATH'] . $licenseKeyFileName);
		}
		else
		{
			$postFileArray['value'] = '@'. $ac_config['INTERNALLICENSEKEYSROOTPATH'] . $licenseKeyFileName;
		}

		$uploadLicenseKeyReturn = TaskObj::sendToTaopixOnline('UPLOADLICENSEKEY', $callData, $postFileArray);

		if ($uploadLicenseKeyReturn['error'] == 0)
		{
			$uploadLicenseKeyData = $uploadLicenseKeyReturn['data'];

			if ($uploadLicenseKeyData['error'] != '')
			{
				$result = $uploadLicenseKeyData['error'];
				$resultParam = $uploadLicenseKeyData['error'];
			}
		}
		else
		{
			$result = $uploadLicenseKeyReturn['errorparam'];
			$resultParam = $uploadLicenseKeyReturn['error'];
		}

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;

		return $resultArray;
    }

    static function processCalendarData($pEventRecordID, $pWebBrandCode, $pAssetID, $pSystemConfigArray)
    {
    	global $ac_config;

		$result = '';
		$resultParam = '';
		$calendarData = '';
		$uploadCalendarData = array();
		$uploadCalendarDataReturnArray = array();

		$calendarDataArray = DatabaseObj::getAssetFromID($pAssetID);

		if ($calendarDataArray['result'] == '')
		{
			$tempFileDir = self::createTempPath($pSystemConfigArray['ownercode'], TPX_APPLICATION_FILE_TYPE_CALENDARDATA, $pWebBrandCode);

			$tempFileName = $tempFileDir . $pAssetID;

			TaskObj::writeLogEntry('tempFileName: ' . $tempFileName);

			if (file_put_contents($tempFileName, $calendarDataArray['data'] ) !== false)
			{
				$callData = array();
				$callData['webbrandcode'] = $pWebBrandCode;
				$callData['assetid'] = $pAssetID;

				$postFileArray = array();
				$postFileArray['name'] = 'calendardatafile';

				// check to see if the PHP version being used is 5.5.0 or higher
				// if it is then we need to use the curl_file_create method.
				// this is because the old way of prefixing an @ character to the filename is deprecated
				if (version_compare(PHP_VERSION, '5.5.0') >= 0)
				{
					$postFileArray['value'] = curl_file_create($tempFileName);
				}
				else
				{
					$postFileArray['value'] = '@' . $tempFileName;
				}

				$uploadCalendarDataReturnArray = TaskObj::sendToTaopixOnline('UPLOADCALENDARDATA', $callData, $postFileArray);

				if ($uploadCalendarDataReturnArray['error'] == '')
				{
					$uploadCalendarData = $uploadCalendarDataReturnArray['data'];

					if ($uploadCalendarData['error'] != 0)
					{
						$result = $uploadCalendarData['error'];
						$resultParam = $uploadCalendarData['error'];
					}
				}
				else
				{
					$result = $uploadCalendarDataReturnArray['error'];
					$resultParam = $uploadCalendarDataReturnArray['errorparam'];
				}
			}
			else
			{
				$resultParam = 'Unable to create temp file for assetdata record: ' . $pAssetID;
			}
		}
		else
		{
			$resultParam = 'Missing assetdata record: ' . $pAssetID;
		}

		// clean up temporary file
		UtilsObj::deleteFile($tempFileName);

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;

		return $resultArray;
    }

    static function uploadOnlineSystemData($pPRDFileName, $pProductCollectionCode, $pProductCollectionDateModified, $pAssets, $pApplicationFileRecordID,
													$pEventID, $pIsGlobalAsset, $pIsProductCollection, $pOwnerCode, $pTenantID, $pKey, $pSecret)
    {
    	global $gSession;
    	global $ac_config;

    	$result = '';
		$resultParam = '';
		$callData = array();
		$assetFilePathsArray = Array();
		$filesToDeleteArray = array();

		if (!empty($pAssets['new']))
		{
			foreach ($pAssets['new'] as $asset => $assetData)
			{
				$assetFilePathsArray[$assetData['assetref']]['tempfilepath'] = self::getTempPath($assetData, $pIsGlobalAsset, $pOwnerCode);
				$assetFilePathsArray[$assetData['assetref']]['datemodified'] = $assetData['assetversiondate'];
			}
		}

		$postFileArray = array('name' => 'productcollection', 'value' => '');

		if ($pIsProductCollection)
		{
			$callData['productcollectioncode'] = $pProductCollectionCode;

			// check to see if the PHP version being used is 5.5.0 or higher
			// if it is then we need to use the curl_file_create method.
			// this is because the old way of prefixing an @ character to the filename is deprecated
			if (version_compare(PHP_VERSION, '5.5.0') >= 0)
			{
				$postFileArray['value'] = curl_file_create($pPRDFileName);
			}
			else
			{
				$postFileArray['value'] = '@'. $pPRDFileName;
			}

		}
		else
		{
			$callData['productcollectioncode'] = '';
		}

		$callData['assetsmetadata'] = $pAssets;
		$callData['batchref'] = 'Push_'. $pEventID;
		$callData['eventid'] = $pEventID;

		$pushWhichServerReturn = TaskObj::sendToTaopixOnline('UPLOADSYSTEMDATA', $callData, $postFileArray);

		if ($pIsProductCollection)
		{
			if (file_exists($pPRDFileName))
			{
				// clean up the unzipped prd file
				unlink($pPRDFileName);
			}
		}

		if ($pushWhichServerReturn['error'] == '')
		{
			$whichServerData = $pushWhichServerReturn['data'];

			if ($whichServerData['error'] == '')
			{
				$currentTimeOut = 600;

				if (!empty($whichServerData['assets']))
				{
					// upload the assets to the image servers
					foreach ($whichServerData['assets'] as $asset => $assetData)
					{
						UtilsObj::resetPHPScriptTimeout($currentTimeOut);

						if (file_exists($assetFilePathsArray[$assetData['assetref']]['tempfilepath']))
						{
							$startUploadSeconds = time();

							TaskObj::writeLogEntry("Sending asset to URL: " . $assetData['serverurl']);

							// set the curl timeout 600 seconds. This means it will allow upto 10 minutes for the image to upload

							// if the method is set to POST assume we are uploading to our ImageServer
							// else assume we are uploading to AWS S3
							if ($assetData['method'] == 'POST')
							{
								$curlFields = Array();

								// check to see if the PHP version being used is 5.5.0 or higher
								// if it is then we need to use the curl_file_create method.
								// this is because the old way of prefixing an @ character to the filename is deprecated
								if (version_compare(PHP_VERSION, '5.5.0') >= 0)
								{
									$curlFields['asset'] = curl_file_create($assetFilePathsArray[$assetData['assetref']]['tempfilepath']);
								}
								else
								{
									$curlFields['asset'] = '@'. $assetFilePathsArray[$assetData['assetref']]['tempfilepath'];
								}

								if (! empty($assetData['formfields']))
								{
									$file = $curlFields['asset'];
									$curlFields = $assetData['formfields'];
									// set the tags to an empty string until the decision is made to use them again
									$curlFields['tagging'] = '<Tagging><TagSet></TagSet></Tagging>';
									$curlFields['file'] = $file;
								}

								$pushStoreAssetReturn = CurlObj::postFile($assetData['serverurl'], $curlFields, 5, 600);
							}

							if ($pushStoreAssetReturn['errorparam'] == '')
							{
								$storeImageData = array('success'=>true);

								// AWS doesn't return JSON so don't try and parse it
								if ($assetData['method'] == 'POST')
								{
									if (strstr($pushStoreAssetReturn['data'], '<?xml ') === false)
									{
										$storeImageData = json_decode($pushStoreAssetReturn['data'], true);
									}
								}

								if ($storeImageData['success'])
								{
									$uploadDuration = time() - $startUploadSeconds;

									/* Queue the job to the TQueue server */
									$callData = Array();
									$callData['assetid'] = $assetData['id'];
									$callData['uploadduration'] = $uploadDuration;
									$callData['filesizebytes'] = $assetData['filesizebytes'];
									$callData['volumeid'] = $assetData['volumeid'];
									$callData['volumetype'] = $assetData['volumetype'];

									$pushQueueJobReturn = TaskObj::sendToTaopixOnline('QUEUEJOB', $callData);

									if ($pushQueueJobReturn['error'] == '')
									{
										$queueJobData = $pushQueueJobReturn['data'];

										if ($queueJobData['error'] != 0)
										{
											$result = $queueJobData['error'] . ' - ' . $assetData['assetref'];
											$resultParam = $queueJobData['error'] . ' - ' . $assetData['assetref'];
										}
										else
										{
											$dbObj = DatabaseObj::getGlobalDBConnection();

											if ($stmt = $dbObj->prepare('UPDATE `APPLICATIONFILES` SET `versiondateonline` = ? WHERE `id` = ?'))
											{
												$stmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);

												if ($stmt->bind_param('si', $assetFilePathsArray[$assetData['assetref']]['datemodified'], $pApplicationFileRecordID))
												{
													$stmt->execute();
												}

												$stmt->free_result();
												$stmt->close();
											}

											UtilsObj::deleteFile($assetFilePathsArray[$assetData['assetref']]['tempfilepath']);

										}
									}
									else
									{
										$result = $pushQueueJobReturn['error'] . ' - ' . $assetData['assetref'];
										$resultParam = $pushQueueJobReturn['error'] . ' - ' . $assetData['assetref'];
									}
								}
								else
								{
									$result = $storeImageData['error'] . ' - ' . $assetData['assetref'];
									$resultParam = $storeImageData['error'] . ' - ' . $assetData['assetref'];
								}
							}
							else
							{
								$result = $pushStoreAssetReturn['errorparam'] . ' - ' . $assetData['assetref'];
								$resultParam = $pushStoreAssetReturn['errorparam'] . ' - ' . $assetData['assetref'];
							}
						}
						else
						{
							$result = 'Missing file: ' . $assetFilePathsArray[$assetData['assetref']]['tempfilepath'];
							$resultParam = 'Missing file: ' . $assetFilePathsArray[$assetData['assetref']]['tempfilepath'];
						}

						// if there is an error with one of the assets and it is part of a product collection stop looping and return
						// the error
						if (($result != '') && ($pIsProductCollection))
						{
							break;
						}

						// increment the timeout by 5 minutes for each asset
						$currentTimeOut += 300;
					}
				}

				if (($result == '') && ($pIsProductCollection))
				{
					$dbObj = DatabaseObj::getGlobalDBConnection();

					if ($stmt = $dbObj->prepare('UPDATE `APPLICATIONFILES` SET `versiondateonline` = ? WHERE `id` = ?'))
					{
						if ($stmt->bind_param('si', $pProductCollectionDateModified, $pApplicationFileRecordID))
						{
							$stmt->execute();
						}

						$stmt->free_result();
						$stmt->close();
					}
				}
			}
			else
			{
				$result = 'Data upload failed: ' . $whichServerData['error'];
				$resultParam = 'Data upload failed: ' . $whichServerData['error'];
			}

		}
		else
		{
			$result = 'Data upload failed: ' . $pushWhichServerReturn['error'];
			$resultParam = 'Data upload failed: ' . $pushWhichServerReturn['error'];
		}

		$resultArray['result'] = $result;
		$resultArray['resultparam'] = $resultParam;

		return $resultArray;
    }

    static function createTempPath($pOwnerCode, $pSubDirectory, $pWebBrandCode)
    {

		$dc = DIRECTORY_SEPARATOR;

		$tempFileDir = sys_get_temp_dir() . $dc . 'TAOPIX' . $dc . $pOwnerCode . $dc . $pSubDirectory . $dc;

		if ($pWebBrandCode != '')
		{
			$tempFileDir .= $pWebBrandCode . $dc;
		}

		if (!file_exists($tempFileDir))
		{
			UtilsObj::createAllFolders($tempFileDir);
		}

		return $tempFileDir;
    }

    static function getTempPath($pAssetData, $pIsGlobaAsset, $pOwnerCode)
    {
    	global $ac_config;

    	$dc = DIRECTORY_SEPARATOR;

		$assetTypeName = '';
		$fileName = '';

		switch ($pAssetData['assettype'])
		{
			case TPX_APPLICATION_FILE_TYPE_MASK:
			{
				$assetTypeName = 'Masks';
				break;
			}
			case TPX_APPLICATION_FILE_TYPE_BACKGROUND:
			{
				$assetTypeName = 'Backgrounds';
				break;
			}
			case TPX_APPLICATION_FILE_TYPE_PICTURE:
			{
				$assetTypeName = 'Scrapbook';
				break;
			}
		}

    	$subDirectory = '';

    	if ($pIsGlobaAsset)
    	{
    		$fileName = $pAssetData['assetref'];
    		$subDirectory = $assetTypeName;
    	}
    	else
    	{
    		$fileName = $pAssetData['filename'];
			$subDirectory = 'Products' . $dc . $pAssetData['productcollectioncode'] . $dc . $assetTypeName;
    	}

    	return self::createTempPath($pOwnerCode, $subDirectory, $pAssetData['webbrandcode']) . $fileName;

    }

	static function getApplicationFileFromID($pApplicationFileRecordID)
	{
		$result = '';
        $resultParam = '';
		$id = 0;
		$dateCreated = '';
		$type = 0;
		$ref = '';
		$categoryCode = '';
		$categoryName = '';
		$name = '';
		$description = '';
		$products = '';
		$fileName = '';
		$versionDate = '';
		$versionDateOnline = '';
		$encrypted = 0;
		$size = 0;
		$hiddenFromUser = 0;
		$webBrandCode = '';
		$active = 0;
		$deleted = 0;
		$onlineDependencies = '';
		$onlineActive = 0;

		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('SELECT `id`, `datecreated`, `type`, `ref`, `categorycode`, `categoryName`, `name`,
					`description`, `products`, `filename`, `versiondate`, `versiondateonline`, `encrypted`, `onlinedependencies`, `size`, `hiddenfromuser`, `webbrandcode`, `active`, `onlineactive`, `deleted`
					FROM `APPLICATIONFILES` WHERE `id` = ?'))
			{
				if ($stmt->bind_param('i', $pApplicationFileRecordID))
				{
					if ($stmt->bind_result($id, $dateCreated, $type, $ref, $categoryCode, $categoryName, $name,
						$description, $products, $fileName, $versionDate, $versionDateOnline, $encrypted, $onlineDependencies, $size, $hiddenFromUser,
						$webBrandCode, $active, $onlineActive, $deleted))
					{
						if ($stmt->execute())
						{
							$stmt->fetch();
						}
					}
				}

				$stmt->free_result();
				$stmt->close();
			}
		}

		$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['recordid'] = $id;
        $resultArray['datecreated'] = $dateCreated;
        $resultArray['assettype'] = $type;
        $resultArray['assetref'] = $ref;
        $resultArray['categorycode'] = $categoryCode;
        $resultArray['categoryname'] = $categoryName;
        $resultArray['name'] = $name;
        $resultArray['description'] = $description;
        $resultArray['products'] = $products;
        $resultArray['filename'] = $fileName;
        $resultArray['datemodified'] = $versionDate;
        $resultArray['datemodifiedonline'] = $versionDateOnline;
        $resultArray['encrypted'] = (int) $encrypted;
        $resultArray['filesizebytes'] = $size;
        $resultArray['hiddenfromuser'] = (int) $hiddenFromUser;
        $resultArray['webbrandcode'] = $webBrandCode;
        $resultArray['onlinedependencies'] = $onlineDependencies;
        $resultArray['active'] = $onlineActive;
        $resultArray['deleted'] = $deleted;

        return $resultArray;
	}

	// function to run this task
	static function run($pEventID)
	{
		$resultArray = array();
		$resultMessage = '';

		$groupCode = '';
		$applicationFileRecordID = 0;
		$applicationFileType = 0;
		$deletedApplicationAssetRef = '';
		$assetID = 0;

		$productCollectionArray = array();

		try
		{
			$systemConfigArray = TaskObj::getSystemConfig();

			if (($systemConfigArray['secret'] != '') && ($systemConfigArray['key'] != ''))
			{
				$pEventID = (int)$pEventID[0];

				// get list of events for the task
				$taskCode = self::register();
				$taskCode = $taskCode['code'];

				TaskObj::writeLogEntry('Task: ' . $taskCode . '. Retrieving Events.');

				if ($pEventID > 0)
				{
					$eventsList = TaskObj::getEventByID($pEventID);
				}
				else
				{
					$eventsList = TaskObj::getEventsByTaskCode($taskCode, 200);
				}

				if ($eventsList['result'] == '')
				{
					$eventsList = $eventsList['events'];
					$eventCount = count($eventsList);

					TaskObj::writeLogEntry('Task: ' . $taskCode . '. Found ' . $eventCount . ' Events.');

					for ($i = 0; $i < $eventCount; $i++)
					{
						if ($applicationFileType == TPX_APPLICATION_FILE_TYPE_PRODUCTCOLLECTION)
						{
							// set the timeout to 5 minutes for product collections
							UtilsObj::resetPHPScriptTimeout(300);
						}
						else
						{
							UtilsObj::resetPHPScriptTimeout(0);
						}

						$event = &$eventsList[$i];
						$eventRecordID = $event['id'];
						$applicationFileType = (int)$event['param2'];

						if ($applicationFileType == TPX_APPLICATION_FILE_TYPE_LICENSEKEY)
						{
							$groupCode = (string)$event['param1'];
						}
						else if ($applicationFileType == TPX_APPLICATION_FILE_TYPE_CALENDARDATA)
						{
							$assetID = (int)$event['param1'];
						}
						else
						{
							$applicationFileRecordID = (int)$event['param1'];
						}

						$deletedApplicationAssetRef = (string)$event['param3'];

						$deletedApplicationWebBrandCode = (string)$event['param4'];

						TaskObj::writeLogEntry('Task: ' . $taskCode . '. Executing Event ' . ($i + 1) .  ' Of ' . $eventCount . ' (' . $eventRecordID . ').');

						// defer all product collection events until the end
						if ($applicationFileType == TPX_APPLICATION_FILE_TYPE_PRODUCTCOLLECTION)
						{

							TaskObj::writeLogEntry('Product collection found. Consolidating event to be ran after all other events.');

							$productCollectionData = self::processProductCollection($eventRecordID, $applicationFileRecordID, $applicationFileType,
																							$deletedApplicationAssetRef, $systemConfigArray, $productCollectionArray);

							if ($productCollectionData['result'] == 2)
							{
								if (!empty($productCollectionData['data']))
								{
									$productCollectionArray = array_merge($productCollectionArray, $productCollectionData['data']);

									if (!$productCollectionData['overwrite'])
									{
										TaskObj::updateEvent($eventRecordID, 2, 'en Product collection consolidated');
									}
								}
								else
								{
									TaskObj::updateEvent($eventRecordID, 2, 'en Product collection consolidated');
								}
							}
							else
							{
								$resultMessage = 'en ' . $productCollectionData['resultparam'];
								TaskObj::updateEvent($eventRecordID, 1, 'en Product collection ' . $productCollectionData['resultparam']);
							}
						}
						else if ($applicationFileType == TPX_APPLICATION_FILE_TYPE_3DMODEL)
						{
							require_once('../Admin3DPreview/Admin3DPreview_model.php');

							if ((string) $event['param3'] != '')
							{
								$eventType = 'en 3D preview product link update [' . (string)$event['param3'] . ']';

								TaskObj::updateEvent($eventRecordID, 3, $eventType);

								$unLink3DPreviewModelToProductsResult = Admin3DPreview_model::unLink3DPreviewModelToProducts((string) $event['param3']);

								if ($unLink3DPreviewModelToProductsResult['error'] == '')
								{
									TaskObj::updateEvent($eventRecordID, 2, $eventType);
								}
								else
								{
									$resultMessage = $unLink3DPreviewModelToProductsResult['error'];

									TaskObj::updateEvent($eventRecordID, 1, $eventType . ' - ' . $resultMessage);
								}
							}
						}
						else
						{
							$eventType = '';

							try
		       		 		{

								TaskObj::updateEvent($eventRecordID, 3, '');

								if ($applicationFileType == TPX_APPLICATION_FILE_TYPE_LICENSEKEY)
								{
									$eventType = 'en License Key';

									TaskObj::updateEvent($eventRecordID, 3, $eventType);

									$resultArray = self::processLicenseKey($eventRecordID, $groupCode, $systemConfigArray);
								}
								else if ($applicationFileType == TPX_APPLICATION_FILE_TYPE_CALENDARDATA)
								{
									$eventType = 'en Calendar Data [' . (string)$event['param3'] . ']';

									TaskObj::updateEvent($eventRecordID, 3, $eventType);

									$resultArray = self::processCalendarData($eventRecordID, $event['webBrandCode'], $assetID, $systemConfigArray);
								}
								else
								{
									$eventType = 'en Global Asset';

									TaskObj::updateEvent($eventRecordID, 3, $eventType);

									$resultArray = self::processApplicationFile($eventRecordID, $applicationFileRecordID, $applicationFileType,
																					$deletedApplicationAssetRef, $deletedApplicationWebBrandCode, $systemConfigArray);
								}

								if ($resultArray['resultparam'] != '')
								{

									TaskObj::writeLogEntry('Error with Event: ' . $eventRecordID);
									TaskObj::writeLogEntry($resultArray['resultparam']);

									TaskObj::updateEvent($eventRecordID, 1, $eventType . ' - ' . $resultArray['resultparam']);
								}
								else
								{
									TaskObj::updateEvent($eventRecordID, 2, $eventType);
								}

							}
							catch(Exception $e)
							{
								$resultMessage = 'en ' . $e->getMessage();

								TaskObj::updateEvent($eventRecordID, 1, $eventType . ' - ' . $resultMessage);
							}
						}
					}

					if (!empty($productCollectionArray))
					{

						$resultParam = '';

						//got through all the product colleciton events and process them
						foreach($productCollectionArray as $key => $prdEvent)
						{
							TaskObj::writeLogEntry('Product Collection Event: ' . $prdEvent['eventrecordid']);

							try
		       		 		{
								TaskObj::updateEvent($prdEvent['eventrecordid'], 3, 'en Product collection');

								TaskObj::writeLogEntry('Uploading Product Collection: ' . $prdEvent['filename'] . ' - ' . $prdEvent['assetref']);

								$assetStoreResultArray = self::uploadOnlineSystemData($prdEvent['filename'],
																						$prdEvent['assetref'],
																						$prdEvent['datemodified'],
																						$prdEvent['assetdata'],
																						$prdEvent['applicationfilerecordid'],
																						$prdEvent['eventrecordid'], false, true,
																						$prdEvent['ownercode'],
																						$prdEvent['tenantid'],
																						$prdEvent['key'],
																						$prdEvent['secret']);

								if ($assetStoreResultArray['result'] != '')
								{
									$result = $assetStoreResultArray['result'];
									$resultParam = $assetStoreResultArray['resultparam'];

									TaskObj::writeLogEntry('Error with Product Collection Event: ' . $prdEvent['eventrecordid']);
									TaskObj::writeLogEntry($resultParam);

									TaskObj::updateEvent($prdEvent['eventrecordid'], 1, 'en Product collection - ' . $prdEvent['assetref'] . ' - ' . $resultParam);
								}
								else
								{
									TaskObj::updateEvent($prdEvent['eventrecordid'], 2, 'en Product collection - ' . $prdEvent['assetref']);
								}
							}
							catch(Exception $e)
							{
								$resultMessage = 'en ' . $e->getMessage();

								TaskObj::updateEvent($prdEvent['eventrecordid'], 1, 'en Product collection - ' . $prdEvent['assetref'] . ' - ' . $resultMessage);
							}

							// clean up temp files
							UtilsObj::deleteFile($prdEvent['filename']);
						}
					}

				}
				else
				{
					//return error message to taskManager
					$resultMessage = $eventsList['resultparam'];
				}
			}
			else
			{
				$resultMessage = 'en No encryption details set.';
			}
		}
		catch(Exception $e)
		{
			$resultMessage = 'en ' . $e->getMessage();
		}

		return $resultMessage;
	}
}
