<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsDatabase.php');
require_once(__DIR__ . '/../AdminTaopixOnlineFontLists/AdminTaopixOnlineFontLists_model.php');

/**
* @class AdminAutoUpdate_model
*
* @version 3.0.0
* @since Version 1.0.0
* @author Kevin Gale
*
* @addtogroup AutoUpdate
* @{
*/
class AdminAutoUpdate_model
{

	/**
 	* Checks if License Key is being used in Product Prices, Cover Prices, Paper Prices, or if Users, Vouchers or Shipping Rates have been assigned to it.
 	*
 	* When deleting License Keys need to check whether particular license key is not used by other records as Product, Cover, Paper pricings,
 	* Users, Vouchers or Shipping Rates and can be deleted.
 	*
 	* @since Version 2.5.2
 	* @author Dasha Salo, Simon Pearson
 	* @param  $code
 	*  License Key code
 	* @return bool $canDelete
 	*  True if License Key hasn't been used by another record, False otherwise
 	*/
 	static function canDelete($code)
	{
		$canDelete = true;

		$tablesArray = array('PRICELINK', 'USERS', 'VOUCHERS', 'SHIPPINGRATES');
		$max = count($tablesArray);

		// Loop through each of the defined tables and check for any records that are using the specifieid license key
		for ($i = 0; (($i < $max) && ($canDelete)); $i++)
		{
			$canDelete = self::checkLicenseKeyUsage($tablesArray[$i], $code);
		}

		return $canDelete;
	}

	/**
	 * Runs the specified sql query to check if the specified license key code is being used
	 *
	 * @since Version 2016r3
	 * @author Simon Pearson
	 *
	 * @param string $pTable
	 * @param string $pCode
	 *
	 * @return boolean $notUsed
	 */
	static function checkLicenseKeyUsage($pTable, $pCode)
	{
		$dbObj = DatabaseObj::getGlobalDBConnection();
		$notUsed = true;
		$error = '';

		if ($dbObj)
		{
			$sql = 'SELECT `id` FROM `' . $pTable . '` WHERE `groupcode` = ? LIMIT 1';

			if ($stmt = $dbObj->prepare($sql))
			{
				if ($stmt->bind_param('s', $pCode))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0)
							{
								// We do not need to do a bind result or fetch here, as we only need to know whether or not any rows were returned.
								$notUsed = false;
							}
						}
						else
						{
							$error = __FUNCTION__ . ' store_result: ' . $dbObj->error;
						}
					}
					else
					{
						$error = __FUNCTION__ . ' execute: ' . $dbObj->error;
					}
				}
				else
				{
					$error = __FUNCTION__ . ' bind_param: ' . $dbObj->error;
				}

				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$error = __FUNCTION__ . ' prepare: ' . $dbObj->error;
			}
		}
		else
		{
			$error = __FUNCTION__ . ' database object not set';
		}

		// If any database errors were encountered, set the $notUsed flag to false to force this test to fail
		if ($error != '')
		{
			$notUsed = false;
		}

		return $notUsed;
	}

    /**
 	* Lists all Auto Update Application records.
 	*
 	* Gets records for Auto Update Application sorted by specified field name.
 	*
 	* Post parameters:
 	* - start: start record number
 	* - limit: end record number
 	* - sort: sort column name
 	* - dir: sort direction
 	* - companyCode - company code
 	*
 	*
 	* @author Kevin Gale
 	* @return
 	*  Array in the format [[ record count ], [ data ]]
 	*/
    static function listApplication()
    {
        $summaryArray = Array();
        $pagedArray = Array();
        $start = (integer)$_POST['start'];
        $limit = (integer)$_POST['limit'];
        $sort = (isset($_POST['sort'])) ? $_POST['sort'] : '';
        $dir = (isset($_POST['dir'])) ? $_POST['dir'] : '';
        $companyCode = (isset($_POST['companyCode'])) ? $_POST['companyCode'] : '';
        $resultArray = DatabaseObj::getAutoUpdateApplicationBuildDetails('**ALL**', '**ALL**', '', '', $companyCode, $sort, $dir);
        $itemList = $resultArray['applist'];
        $itemCount = count($itemList);
        $smarty = SmartyObj::newSmarty('AdminAutoUpdateApplication');

        for ($i = 0; $i < $itemCount; $i++)
        {
        	$appItem = $itemList[$i];
        	$brandName = '';
            if ($appItem['webbrandcode'] != '')
            {
            	$brandName = $appItem['webbrandcode'] . ' - ' . $appItem['webbrandapplicationname'];
            }
            else
            {
            	$brandName = $smarty->get_config_vars('str_LabelNone');
            }

            if ($appItem['macarchivefilename'] != '')
            {
            	$bufArr['appId'] = '"' . 'mac'.$i .'"';
            	$bufArr['brandCode'] = "'" . UtilsObj::ExtJSEscape($appItem['webbrandcode']) ."'";
            	$bufArr['brandName'] = "'" . UtilsObj::ExtJSEscape($brandName) ."'";
            	$bufArr['osCode'] = "'" . '0' ."'";
            	$bufArr['osName'] = "'" . 'Macintosh' ."'";
            	$bufArr['versionCode'] = "'" . UtilsObj::ExtJSEscape($appItem['macversion']) ."'";
            	$bufArr['archiveName'] = "'" . UtilsObj::ExtJSEscape($appItem['macarchivefilename']) ."'";
            	$bufArr['exeName'] = "'" . UtilsObj::ExtJSEscape($appItem['macexecutablefilename']) ."'";
				$bufArr['priority'] = "'" . UtilsObj::ExtJSEscape($appItem['macupdatepriority']) ."'";
            	array_push($summaryArray, $bufArr);
            }

            if ($appItem['win32archivefilename'] != '')
            {
            	$bufArr['appId'] = '"' . 'win'.$i .'"';
               	$bufArr['brandCode'] = "'" . UtilsObj::ExtJSEscape($appItem['webbrandcode']) ."'";
            	$bufArr['brandName'] = "'" . UtilsObj::ExtJSEscape($brandName) ."'";
            	$bufArr['osCode'] = "'" . '1' ."'";
            	$bufArr['osName'] = "'" . 'Windows' ."'";
            	$bufArr['versionCode'] = "'" . UtilsObj::ExtJSEscape($appItem['win32version']) ."'";
            	$bufArr['archiveName'] = "'" . UtilsObj::ExtJSEscape($appItem['win32archivefilename']) ."'";
            	$bufArr['exeName'] = "'" . UtilsObj::ExtJSEscape($appItem['win32executablefilename']) ."'";
				$bufArr['priority'] = "'" . UtilsObj::ExtJSEscape($appItem['win32updatepriority']) ."'";
            	array_push($summaryArray, $bufArr);
            }
        }

        if($limit > count($summaryArray))
        {
        	$limit = count($summaryArray);
        }

        for ($i = 0; $i < count($summaryArray); $i++)
        {
        	$summaryArray[$i] = '['.join(',', $summaryArray[$i]).']';
        }
        $pagedArray = array_slice($summaryArray, $start, $limit,true);

        $jointArray = join(',', $pagedArray);
        if ($jointArray != '')
        {
        	$jointArray = ', ' . $jointArray;
        }

        echo '[['.count($summaryArray).']'.$jointArray.']';
        return;
    }

    static function deleteApplication()
    {
        try
        {
        	global $gSession;
        	global $ac_config;

        	$appList = explode(',', $_POST['idlist']);
            $appCount = count($appList);
            $oslist = explode(',', $_POST['oslist']);
            $osCount = count($oslist);

        	$dbObj = DatabaseObj::getGlobalDBConnection();
        	if ($dbObj)
        	{
        		for($i = 0; $i < $appCount; $i++)
        		{
        			$webBrandCode    = $appList[$i];
        			$operatingSystem = $oslist[$i];

        			$applicationDataArray = DatabaseObj::getAutoUpdateApplicationBuildDetails($webBrandCode, '**ALL**');

        			if ($operatingSystem == 0)
        			{
        				if ($stmt = $dbObj->prepare('UPDATE `APPLICATIONBUILD` SET `macarchivefilename` = "", `macexecutablefilename` = "", `macversion` = "" WHERE `webbrandcode` = ?'))
        				{
        					if (($stmt->bind_param('s', $webBrandCode)) && ($stmt->execute()))
        					{
                        		DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'AUTOUPDATEAPPLICATION-DELETE', 'Mac OS X', 1);
                       		 	$sourceFilePath = $ac_config['INTERNALCLIENTSROOTPATH'];
                        		if ($applicationDataArray['webbrandcode'] != '')
                        		{
                        			$sourceFilePath = $sourceFilePath . $applicationDataArray['webbrandcode'] . '/';
                        		}
                        		$sourceFilePath = $sourceFilePath . $applicationDataArray['macarchivefilename'];
                        		UtilsObj::deleteFile($sourceFilePath);
                    		}
                    		$stmt->free_result();
        				}
        			}
        			else
        			if ($operatingSystem == 1)
        			{
        				if ($stmt = $dbObj->prepare('UPDATE `APPLICATIONBUILD` SET `win32archivefilename` = "", `win32executablefilename` = "", `win32version` = "" WHERE `webbrandcode` = ?'))
        				{
                    		if (($stmt->bind_param('s', $webBrandCode)) && ($stmt->execute()))
                    		{
                        		DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'AUTOUPDATEAPPLICATION-DELETE', 'Win32', 1);
                        		$sourceFilePath = $ac_config['INTERNALCLIENTSROOTPATH'];
                            	if ($applicationDataArray['webbrandcode'] != '')
                            	{
                            		$sourceFilePath = $sourceFilePath . $applicationDataArray['webbrandcode'] . '/';
                            	}
                            	$sourceFilePath = $sourceFilePath . $applicationDataArray['win32archivefilename'];
                            	UtilsObj::deleteFile($sourceFilePath);
                    		}
                    		$stmt->free_result();
                		}
        			}
        		}
        		$dbObj->close();
        	}
        	echo '{"success":true, "data":[{"id":' . '0' . ',"active":"'.'1'.'"},]}';
        }
        catch (Exception $e)
        {
        	echo '{"success":false,	"msg":"' . $e->getMessage() . '"}';
        }
    }

    static function listProducts()
    {
        global $gSession;

        $sort = (isset($_POST['sort'])) ? $_POST['sort'] : '';
        $dir = (isset($_POST['dir'])) ? $_POST['dir'] : '';
        $companyCode = (isset($_POST['companyCode'])) ? $_POST['companyCode'] : '';
		$hideInactive = 0;

		// check that hideinactive has been sent before safely retrieving it
		if (isset($_POST['hideInactive']))
		{
			$hideInactive = filter_input(INPUT_POST, 'hideInactive', FILTER_SANITIZE_NUMBER_INT);
		}

        /**
         * If company code passed from the company filter combobox is empty then need to change it to **ALL** because otherwise getAutoUpdateProductList
         * would confuse empty string with Global company passed from designer or shopping cart
         */
        if ($companyCode == '')
        {
        	$companyCode = '**ALL**';
        }

        if ($gSession['userdata']['usertype'] == TPX_LOGIN_COMPANY_ADMIN)
		{
			$companyCode = $gSession['userdata']['companycode'];
		}

		$resultArray  = self::getAutoUpdateProductCollectionsList($companyCode, $sort, $dir, $hideInactive);

        return $resultArray;
    }

    static function deleteProduct()
    {
        global $ac_config;

        $smarty = SmartyObj::newSmarty('AdminAutoUpdateProducts');

        try
        {
        	$filename= '';
        	$colelctionCodes  = explode(',',$_POST['collectioncodes']);
        	$collectionCount = count($colelctionCodes);
        	$dbObj = DatabaseObj::getGlobalDBConnection();

        	if ($dbObj)
        	{
        		for ($i = 0; $i < $collectionCount; $i++)
        		{
        			$collectionCode = $colelctionCodes[$i];

					if ($stmt = $dbObj->prepare('SELECT `filename`, `id` FROM `APPLICATIONFILES` WHERE `ref` = ? AND `type` = 0'))
					{
						$stmt->attr_set(MYSQLI_STMT_ATTR_CURSOR_TYPE, MYSQLI_CURSOR_TYPE_READ_ONLY);

						if ($stmt->bind_param('s', $colelctionCodes[$i]))
						{
							if ($stmt->bind_result($filename, $id))
							{
								if ($stmt->execute())
								{
									while ($stmt->fetch())
									{
										if ($stmt2 = $dbObj->prepare('UPDATE `APPLICATIONFILES` SET `appversion` = "", `dataversion` = 0, `filename` = "", `deleted` = 1 WHERE `id` = ?'))
										{
					                		if ($stmt2->bind_param('i', $id))
					                		{
					                    		if ($stmt2->execute())
					                    		{
					                    			$sourceFilePath = $ac_config['INTERNALPRODUCTSROOTPATH'] . $filename;
					                        		UtilsObj::deleteFile($sourceFilePath);

													// deprecate the collection resources
													DatabaseObj::deprecateProductCollectionResources($dbObj, $collectionCode);
					                    		}
					                    	}

					                		$stmt2->free_result();
					                		$stmt2->close();
					                		$stmt2 = null;
					            		}
									}
								}
							}
						}

						$stmt->free_result();
        				$stmt->close();
        				$stmt = null;
					}
    	    	}
        		$dbObj->close();
        	}
        	else
        	{
        		$errorMes = $smarty->get_config_vars('str_DatabaseError');
        		echo '{"success":false,	"msg":"' . str_replace('^0', $smarty->get_config_vars('str_ConnectionError'), $errorMes) . '"}';
        		return;
        	}

        	echo '{"success":true, "data":[{"id":' . '1' . ',"active":"'.'1'.'"},]}';
        	return;
        }
        catch (Exception $e)
        {
        	$errorMes = $smarty->get_config_vars('str_DatabaseError');
        	echo '{"success":false,	"msg":"' . str_replace('^0', $e->getMessage(), $errorMes) . '"}';
        	return;
        }
    }

    static function getFileList()
    {
    	global $gSession;
    	global $gConstants;

    	$summaryArray = Array();
    	$pagedArray = Array();
    	$start = (integer)$_POST['start'];
        $limit = (integer)$_POST['limit'];
        $filesType = (isset($_GET['filetype'])) ? $_GET['filetype'] : '0';
        $sort = (isset($_POST['sort'])) ? $_POST['sort'] : '';
        $dir = (isset($_POST['dir'])) ? $_POST['dir'] : '';
		$companyCode = (isset($_POST['companyCode'])) ? $_POST['companyCode'] : '';
		$hideInactive = 0;

		// check that hideinactive has been sent before safely retrieving it
		if (isset($_POST['hideInactive']))
		{
			$hideInactive = filter_input(INPUT_POST, 'hideInactive', FILTER_SANITIZE_NUMBER_INT);
		}

        if ($filesType)
    	{
    		$smarty = SmartyObj::newSmarty('AdminAutoUpdateApplicationFiles');
    		$applicationDataArray = DatabaseObj::getAutoUpdateApplicationFilesList('**ALL**', '**ALL**', $filesType, 99, $companyCode, $sort, $dir, $hideInactive);
    		$itemList = $applicationDataArray['filelist'];
        	$totalItemCount = count($itemList);
    		$itemList = array_slice($itemList, $start, $limit, true);
    		$itemCount = count($itemList);

    		for ($i = $start; $i < $start + $itemCount; $i++)
    		{
            	$brandName = '';
            	if ($itemList[$i]['webbrandcode'] != '')
            	{
            		$brandName = $itemList[$i]['webbrandcode'] . ' - ' . $itemList[$i]['webbrandapplicationname'];
            	}
            	else
            	{
            		$brandName = $smarty->get_config_vars('str_LabelNone') ;
            	}

            	$id = $itemList[$i]['id'];
            	$name = LocalizationObj::getLocaleString($itemList[$i]['name'], $gSession['browserlanguagecode'], true);
            	$dateValue = $itemList[$i]['datemodified'];
            	$updatePriority = $itemList[$i]['updatepriority'];

            	if ($dateValue != '0000-00-00 00:00:00')
            	{
            		$dateString = LocalizationObj::formatLocaleDateTime($dateValue);
            	}
            	else
            	{
            		$dateString = '';
            	}

            	$hiddenfromUser = $itemList[$i]['hiddenfromuser'];
            	$active = $itemList[$i]['isactive'];
            	$onlineActive = $itemList[$i]['onlineisactive'];

            	if ($gConstants['optiondesdt'] && $gConstants['optiondesol'])
				{
					$categoryName = LocalizationObj::getLocaleString($itemList[$i]['categoryname'], $gSession['browserlanguagecode'], true);
					$bufArr['fileId'] = "'" . $id ."'";
					$bufArr['brandName'] = "'" . UtilsObj::ExtJSEscape($brandName) ."'";
					$bufArr['categoryName'] = "'" . UtilsObj::ExtJSEscape($categoryName) ."'";
					$bufArr['nameName'] = "'" . UtilsObj::ExtJSEscape($name) ."'";
					$bufArr['fileName'] = "'" . UtilsObj::ExtJSEscape($itemList[$i]['filename']) ."'";
					$bufArr['versionName'] = "'" . UtilsObj::ExtJSEscape($dateString) ."'";
					$bufArr['privateName'] = "'" . $hiddenfromUser ."'";
					$bufArr['activeName'] = "'" . $active ."'";

					if ($filesType != 4)
					{
						$bufArr['onlineActive'] = "'" . $onlineActive ."'";
					}

					$bufArr['updatePriority'] = "'" . $updatePriority ."'";
				}
				else if ($gConstants['optiondesol'])
				{
					$categoryName = LocalizationObj::getLocaleString($itemList[$i]['categoryname'], $gSession['browserlanguagecode'], true);
					$bufArr['fileId'] = "'" . $id ."'";
					$bufArr['brandName'] = "'" . UtilsObj::ExtJSEscape($brandName) ."'";
					$bufArr['categoryName'] = "'" . UtilsObj::ExtJSEscape($categoryName) ."'";
					$bufArr['nameName'] = "'" . UtilsObj::ExtJSEscape($name) ."'";
					$bufArr['fileName'] = "'" . UtilsObj::ExtJSEscape($itemList[$i]['filename']) ."'";
					$bufArr['versionName'] = "'" . UtilsObj::ExtJSEscape($dateString) ."'";
					$bufArr['privateName'] = "'" . $hiddenfromUser ."'";

					if ($filesType != 4)
					{
						$bufArr['onlineActive'] = "'" . $onlineActive ."'";
					}

					$bufArr['updatePriority'] = "'" . $updatePriority ."'";
				}
				else if ($gConstants['optiondesdt'])
				{
					$categoryName = LocalizationObj::getLocaleString($itemList[$i]['categoryname'], $gSession['browserlanguagecode'], true);
					$bufArr['fileId'] = "'" . $id ."'";
					$bufArr['brandName'] = "'" . UtilsObj::ExtJSEscape($brandName) ."'";
					$bufArr['categoryName'] = "'" . UtilsObj::ExtJSEscape($categoryName) ."'";
					$bufArr['nameName'] = "'" . UtilsObj::ExtJSEscape($name) ."'";
					$bufArr['fileName'] = "'" . UtilsObj::ExtJSEscape($itemList[$i]['filename']) ."'";
					$bufArr['versionName'] = "'" . UtilsObj::ExtJSEscape($dateString) ."'";
					$bufArr['privateName'] = "'" . $hiddenfromUser ."'";
					$bufArr['activeName'] = "'" . $active ."'";
					$bufArr['updatePriority'] = "'" . $updatePriority ."'";
				}

        		array_push($summaryArray, $bufArr);
       		}
    	}

    	for ($i = 0; $i < count($summaryArray); $i++)
        {
        	$summaryArray[$i] = '['.join(',', $summaryArray[$i]).']';
        }

        $jointArray = join(',', $summaryArray);
        if ($jointArray != '')
        {
        	$jointArray = ', ' . $jointArray;
        }

        echo '[['.$totalItemCount.']'.$jointArray.']';
        return;
    }

    static function activateApplicationFile()
    {
        try
        {
        	$fileList = explode(',', $_POST['idlist']);
            $fileCount = count($fileList);
            $command = $_POST['command'];
            $dbObj = DatabaseObj::getGlobalDBConnection();

        	if (($dbObj) && ($stmt = $dbObj->prepare('UPDATE `APPLICATIONFILES` SET `active` = ? WHERE `id` = ?')))
        	{
        		for ($i = 0; $i < $fileCount; $i++)
        		{
        			$id = $fileList[$i];
        			if ($stmt->bind_param('ii',$command, $id))
        			{
        				$stmt->execute();
        			}
        		}
                $stmt->close();
        		$dbObj->close();
        	}
        	echo '{"success":true, "data":[{"id":' . '0' . ',"active":"'.'1'.'"},]}';
        }
        catch (Exception $e)
        {
        	echo '{"success":false,	"msg":"' . $e->getMessage() . '"}';
        }
    }



    static function activateApplicationFileOnline()
    {
        global $gSession;
        global $gConstants;

        try
        {
        	$fileType = $_POST['filetype'];
        	$fileList = explode(',', $_POST['idlist']);
            $fileCount = count($fileList);
            $command = $_POST['command'];
            $dbObj = DatabaseObj::getGlobalDBConnection();

        	if (($dbObj) && ($stmt = $dbObj->prepare('UPDATE `APPLICATIONFILES` SET `onlineactive` = ? WHERE `id` = ?')))
        	{
        		for ($i = 0; $i < $fileCount; $i++)
        		{
        			$id = $fileList[$i];
        			if ($stmt->bind_param('ii',$command, $id))
        			{
        				if ($stmt->execute())
        				{
							if ($gConstants['optiondesol'] && $fileType != TPX_APPLICATION_FILE_TYPE_FRAME)
                			{
								$taskInfo = DatabaseObj::getTask('TAOPIX_ONLINEASSETPUSH');
								if ($taskInfo['result'] == '')
								{
									$eventResultArray = DatabaseObj::createEvent('TAOPIX_ONLINEASSETPUSH', '', '', '', $taskInfo['nextRunTime'], 0, $id, $fileType, '', '', '', '', '', '', 0, 0, $gSession['userid'], '', '', $gSession['userid']);
								}
							}
        				}
        			}
        		}
                $stmt->close();
        		$dbObj->close();
        	}
        	echo '{"success":true, "data":[{"id":' . '0' . ',"active":"'.'1'.'"},]}';
        }
        catch (Exception $e)
        {
        	echo '{"success":false,	"msg":"' . $e->getMessage() . '"}';
        }
    }

    static function changeApplicationFilePriority()
    {
        try
        {
        	$fileList  = explode(',',$_POST['idlist']);
        	$fileCount = count($fileList);
        	$command   = $_POST['command'];
        	$dbObj 	   = DatabaseObj::getGlobalDBConnection();

        	if (($dbObj) && ($stmt = $dbObj->prepare('UPDATE `APPLICATIONFILES` SET `updatepriority` = ? WHERE `id` = ?')))
        	{
        		for ($i = 0; $i < $fileCount; $i++)
        		{
        			$id = $fileList[$i];
        			if ($stmt->bind_param('ii',$command, $id))
        			{
        				$stmt->execute();
        			}
        		}
                $stmt->close();
        		$dbObj->close();
        	}
        	echo '{"success":true, "data":[{"id":' . '0' . ',"active":"'. $command. '"},]}';
        }
        catch (Exception $e)
        {
        	echo '{"success":false,	"msg":"' . $e->getMessage() . '"}';
        }
    }

    static function deleteApplicationFile()
    {
        global $ac_config;
        global $gSession;
        global $gConstants;
        try
        {
        	$fileList = explode(',', $_POST['idlist']);
            $fileCount = count($fileList);
            $dbObj = DatabaseObj::getGlobalDBConnection();

        	if($dbObj)
        	{
        		for($i = 0; $i < $fileCount; $i++)
        		{
        			$id       = $fileList[$i];
        			$resultID = 0;
        			$ref      = '';
                    $webBrandCode = '';

        			if ($stmt = $dbObj->prepare('SELECT `id`, `ref`, `type`, `webbrandcode` FROM `APPLICATIONFILES` WHERE (`id` = ?)'))
        			{
        				if (($stmt->bind_param('i',$id)) && ($stmt->bind_result($resultID, $ref, $type, $webBrandCode)))
        				{
        					if ($stmt->execute())
        					{
        						if (!$stmt->fetch())
        						{
        							$resultID = 0;
        						}
        					}
        				}
        				$stmt->free_result();
                		$stmt->close();
        			}
        			if (($resultID > 0) && ($stmt = $dbObj->prepare('DELETE FROM `APPLICATIONFILES` WHERE `id` = ?')))
        			{
                    	if (($stmt->bind_param('i', $id)) && ($stmt->execute()))
                    	{
                           	$sourceFilePath = '';
                            if ($type == 1)
                            {
                                $sourceFilePath = $ac_config['INTERNALAPPLICATIONMASKSROOTPATH'];
                            }
                            else if ($type == 2)
                            {
                                $sourceFilePath = $ac_config['INTERNALAPPLICATIONBACKGROUNDSROOTPATH'];
                            }
                            else if ($type == 3)
                            {
                                $sourceFilePath = $ac_config['INTERNALAPPLICATIONSCRAPBOOKPICTURESROOTPATH'];
                            }
                            else if ($type == 4)
                            {
                                $sourceFilePath = $ac_config['INTERNALAPPLICATIONFRAMESROOTPATH'];
                            }
                           	if ($sourceFilePath != '')
                           	{
                               	if ($webBrandCode != '')
                               	{
                               		$sourceFilePath = $sourceFilePath . $webBrandCode . '/';
                               	}
                                $sourceFilePath = $sourceFilePath . $ref . '.zip';
                                UtilsObj::deleteFile($sourceFilePath);
                            }
                        }
                        $stmt->free_result();
                        $stmt->close();

                        if ($gConstants['optiondesol'] && $type != TPX_APPLICATION_FILE_TYPE_FRAME)
                		{
							$taskInfo = DatabaseObj::getTask('TAOPIX_ONLINEASSETPUSH');
							if ($taskInfo['result'] == '')
							{
								$eventResultArray = DatabaseObj::createEvent('TAOPIX_ONLINEASSETPUSH', '', '', '', $taskInfo['nextRunTime'], 0, $id,
                                    $type, $ref, $webBrandCode, '', '', '', '', 0, 0, $gSession['userid'], '', '', $gSession['userid']);
							}
                        }
                	}
        		}
        		$dbObj->close();
        	}
        	echo '{"success":true, "data":[{"id":' . '1' . ',"active":"'.'1'.'"},]}';
        }
        catch (Exception $e)
        {
        	echo '{"success":false,	"msg":"' . $e->getMessage() . '"}';
        }
    }

    static function listLicenseKeys()
    {
		global $gConstants;

        $smarty = SmartyObj::newSmarty('AdminAutoUpdateLicenseKeys');
        $summaryArray = Array();
        $start = (integer)$_POST['start'];
        $limit = (integer)$_POST['limit'];
        $sort = (isset($_POST['sort'])) ? $_POST['sort'] : '';
        $dir = (isset($_POST['dir'])) ? $_POST['dir'] : '';
        $companyCode = (isset($_POST['companyCode'])) ? $_POST['companyCode'] : '';
        $searchQuery = UtilsObj::getPOSTParam('query');
		$searchFields = UtilsObj::getPOSTParam('fields');
		$hideInactive = 0;

		// check that hideinactive has been sent before safely retrieving it
		if (isset($_POST['hideInactive']))
		{
			$hideInactive = filter_input(INPUT_POST, 'hideInactive', FILTER_SANITIZE_NUMBER_INT);
		}

        $resultArray = DatabaseObj::getAutoUpdateLicenseKeyList('**ALL**', $companyCode, $sort, $dir, $searchFields, $searchQuery, $hideInactive);
        $itemList = $resultArray['licensekeylist'];
        $itemCountTotal = count($itemList);

        $itemList = array_slice($itemList, $start, $limit, true);
        $itemCount = count($itemList);

        for ($i = $start; $i < $start+$itemCount; $i++)
        {
            $id = $itemList[$i]['id'];
            $name = $itemList[$i]['groupcode'];
            $dateValue = $itemList[$i]['version'];

            if ($dateValue != '0000-00-00 00:00:00')
            {
            	$dateString = LocalizationObj::formatLocaleDateTime($dateValue);
            }
            else
            {
            	$dateString = '';
            }

            $availableOnline = $itemList[$i]['availableonline'];
            $active = $itemList[$i]['isactive'];
            $updatePriority = $itemList[$i]['updatepriority'];
            $groupName = $itemList[$i]['groupname'];
        	$webBrandCode = $itemList[$i]['webbrandcode'];
        	$fileName = $itemList[$i]['filename'];
        	$currencyCode = $itemList[$i]['currencycode'];

        	if ($itemList[$i]['usedefaultcurrency'] == 1)
        	{
        		$currencyCode = $smarty->get_config_vars('str_LabelDefault'). ' ('. $currencyCode .')';
        	}

        	$company = $itemList[$i]['company'];

            if ($gConstants['optiondesdt'] && $gConstants['optiondesol'])
            {
            	$bufArr['id'] = "'" . UtilsObj::ExtJSEscape($id) . "'";
            	$bufArr['canDelete'] = "'" . ((self::canDelete($name) == true) ? '1' : '0') . "'";
            	$bufArr['groupcode'] = "'" . UtilsObj::ExtJSEscape($name) . "'";
            	$bufArr['groupname'] = "'" . UtilsObj::ExtJSEscape($groupName) . "'";
            	$bufArr['filename'] = "'" . UtilsObj::ExtJSEscape($fileName) . "'";
            	$bufArr['version'] = "'" . UtilsObj::ExtJSEscape($dateString) . "'";
            	$bufArr['webbrandcode'] = "'" . UtilsObj::ExtJSEscape($webBrandCode) . "'";
            	$bufArr['currencycode'] = "'" . UtilsObj::ExtJSEscape($currencyCode) . "'";
            	$bufArr['availableonline'] = "'" . UtilsObj::ExtJSEscape($availableOnline) . "'";
            	$bufArr['isactive'] = "'" . UtilsObj::ExtJSEscape($active) . "'";
            	$bufArr['updatepriority'] = "'" . UtilsObj::ExtJSEscape($updatePriority) . "'";
            	$bufArr['company'] = "'" . UtilsObj::ExtJSEscape($company) . "'";
            }
            else if ($gConstants['optiondesol'])
            {
            	$bufArr['id'] = "'" . UtilsObj::ExtJSEscape($id) . "'";
            	$bufArr['canDelete'] = "'" . ((self::canDelete($name) == true) ? '1' : '0') . "'";
            	$bufArr['groupcode'] = "'" . UtilsObj::ExtJSEscape($name) . "'";
            	$bufArr['groupname'] = "'" . UtilsObj::ExtJSEscape($groupName) . "'";
            	$bufArr['filename'] = "'" . UtilsObj::ExtJSEscape($fileName) . "'";
            	$bufArr['version'] = "'" . UtilsObj::ExtJSEscape($dateString) . "'";
            	$bufArr['webbrandcode'] = "'" . UtilsObj::ExtJSEscape($webBrandCode) . "'";
            	$bufArr['currencycode'] = "'" . UtilsObj::ExtJSEscape($currencyCode) . "'";
            	$bufArr['availableonline'] = "'" . UtilsObj::ExtJSEscape($availableOnline) . "'";
            	$bufArr['updatepriority'] = "'" . UtilsObj::ExtJSEscape($updatePriority) . "'";
            	$bufArr['company'] = "'" . UtilsObj::ExtJSEscape($company) . "'";
            }
            else if ($gConstants['optiondesdt'])
            {
            	$bufArr['id'] = "'" . UtilsObj::ExtJSEscape($id) . "'";
            	$bufArr['canDelete'] = "'" . ((self::canDelete($name) == true) ? '1' : '0') . "'";
            	$bufArr['groupcode'] = "'" . UtilsObj::ExtJSEscape($name) . "'";
            	$bufArr['groupname'] = "'" . UtilsObj::ExtJSEscape($groupName) . "'";
            	$bufArr['filename'] = "'" . UtilsObj::ExtJSEscape($fileName) . "'";
            	$bufArr['version'] = "'" . UtilsObj::ExtJSEscape($dateString) . "'";
            	$bufArr['webbrandcode'] = "'" . UtilsObj::ExtJSEscape($webBrandCode) . "'";
            	$bufArr['currencycode'] = "'" . UtilsObj::ExtJSEscape($currencyCode) . "'";
            	$bufArr['isactive'] = "'" . UtilsObj::ExtJSEscape($active) . "'";
            	$bufArr['updatepriority'] = "'" . UtilsObj::ExtJSEscape($updatePriority) . "'";
            	$bufArr['company'] = "'" . UtilsObj::ExtJSEscape($company) . "'";
            }

            array_push($summaryArray, '[' . join(',', $bufArr) . ']');
        }

        $jointArray = join(',', $summaryArray);
        if ($jointArray != '')
        {
        	$jointArray = ', ' . $jointArray;
        }

        echo '[['.$itemCountTotal.']'.$jointArray.']';
        return;
    }

    static function activateLicenseKey()
    {
        global $gSession;

        try
        {
        	$licenceList = explode(',',$_POST['idlist']);
        	$licenceCount = count($licenceList);
        	$command = $_POST['command'];
        	$dbObj = DatabaseObj::getGlobalDBConnection();

			$stmt = $dbObj->prepare('UPDATE `LICENSEKEYS` SET `active` = ? WHERE `id` = ?');
			$bindOK = $stmt->bind_param('ii',$command, $id);

        	if (($dbObj) && ($stmt))
        	{
        		for ($i = 0; $i < $licenceCount; $i++)
        		{
        			$id = $licenceList[$i];
        			$licenseKeyArray = DatabaseObj::getLicenseKeyFromID($id);

        			if ($bindOK)
        			{
        				if ($stmt->execute())
        				{
        					if ($command == 1)
        					{
                            	DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'LICENSEKEY-DEACTIVATE', $licenseKeyArray['recordid'] . ' ' . $licenseKeyArray['groupcode'], 1);
                        	}
                        	else
                        	{
                            	DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'LICENSEKEY-ACTIVATE', $licenseKeyArray['recordid'] . ' ' . $licenseKeyArray['groupcode'], 1);
                        	}
                        }
        			}
        		}
                $stmt->close();
        		$dbObj->close();
        		echo '{"success":true, "data":[{"id":' . $command . ',"active":"'.'1'.'"},]}';
        	}
        }
        catch (Exception $e)
        {
        	echo '{"success":false,	"msg":"' . $e->getMessage() . '"}';
    	}
    }

    static function activateLicenseKeyOnline()
    {
        global $gSession;
        global $gConstants;

        try
        {
        	$licenceList = explode(',',$_POST['idlist']);
        	$licenceCount = count($licenceList);
        	$command = $_POST['command'];
        	$dbObj = DatabaseObj::getGlobalDBConnection();

			// if we have desol enabled and not desdt then we need to update both the active flag and the availableonline flag
			if (($gConstants['optiondesol']) && (!$gConstants['optiondesdt']))
			{
				$stmt = $dbObj->prepare('UPDATE `LICENSEKEYS` SET `active` = ?, `availableonline` = ? WHERE `id` = ?');
				$bindOK = $stmt->bind_param('iii',$command, $command, $id);
			}
			else
			{
				$stmt = $dbObj->prepare('UPDATE `LICENSEKEYS` SET `availableonline` = ? WHERE `id` = ?');
				$bindOK = $stmt->bind_param('ii',$command, $id);
			}

        	if (($dbObj) && ($stmt))
        	{
        		for ($i = 0; $i < $licenceCount; $i++)
        		{
        			$id = $licenceList[$i];
        			$licenseKeyArray = DatabaseObj::getLicenseKeyFromID($id);

        			if ($bindOK)
        			{
        				if ($stmt->execute())
        				{
        					if ($command == 1)
        					{
                            	DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'ONLINE-LICENSEKEY-DEACTIVATE', $licenseKeyArray['recordid'] . ' ' . $licenseKeyArray['groupcode'], 1);
                        	}
                        	else
                        	{
                            	DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'ONLINE-LICENSEKEY-ACTIVATE', $licenseKeyArray['recordid'] . ' ' . $licenseKeyArray['groupcode'], 1);
                        	}
        				}
        			}
        		}
                $stmt->close();
        		$dbObj->close();
        		echo '{"success":true, "data":[{"id":' . $command . ',"active":"'.'1'.'"},]}';
        	}
        }
        catch (Exception $e)
        {
        	echo '{"success":false,	"msg":"' . $e->getMessage() . '"}';
    	}
    }

    static function activateProductCollection()
    {
        global $gSession;

        try
        {
        	$collectionList = explode(',', $_POST['idlist']);
            $collectionCount = count($collectionList);
            $command = $_POST['command'];
            $dbObj = DatabaseObj::getGlobalDBConnection();
            $productCodesArray = array();

        	for ($i = 0; $i < $collectionCount; $i++)
        	{
	        	if ($stmt = $dbObj->prepare('SELECT `collectioncode` FROM `PRODUCTCOLLECTIONLINK` WHERE `id` = ?'))
		        {
	                if ($stmt->bind_param('i', $collectionList[$i]))
	                {
	                    if ($stmt->bind_result($collectionProductCode))
	                    {
	                        if ($stmt->execute())
	                        {
	                            if ($stmt->fetch())
	                            {
	                            	$productCodesArray [] = $collectionProductCode;
	                            }
	                        }
	                    }
	                }
	                $stmt->free_result();
	                $stmt->close();
	                $stmt = null;
		        }
        	}

			$productCodesCount = count($productCodesArray);

        	if (($dbObj) && ($stmt = $dbObj->prepare('UPDATE `APPLICATIONFILES` SET `active` = ? WHERE `ref` = ?')))
        	{
        		for ($i = 0; $i < $productCodesCount; $i++)
        		{
        			if ($stmt->bind_param('is',$command, $productCodesArray[$i]))
        			{
        				if ($stmt->execute())
        				{
        					if ($command == 1)
        					{
                            	DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'PRODUCTCOLLECTION-DEACTIVATE', $productCodesArray[$i], 1);
                        	}
                        	else
                        	{
                            	DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'PRODUCTCOLLECTION-ACTIVATE', $productCodesArray[$i], 1);
                        	}
        				}
        			}
        		}
                $stmt->close();
        		$dbObj->close();
        		echo '{"success":true, "data":[{"id":' . $command . ',"active":"'.'1'.'"},]}';
        	}
        }
        catch (Exception $e)
        {
        	echo '{"success":false,	"msg":"' . $e->getMessage() . '"}';
    	}
    }

    static function changeProductCollectionPriority()
    {
        global $gSession;

        try
        {
        	$collectionList = explode(',', $_POST['idlist']);
            $collectionCount = count($collectionList);
            $command = $_POST['command'];
            $dbObj = DatabaseObj::getGlobalDBConnection();
            $productCodesArray = array();

        	for ($i = 0; $i < $collectionCount; $i++)
        	{
	        	if ($stmt = $dbObj->prepare('SELECT `collectioncode` FROM `PRODUCTCOLLECTIONLINK` WHERE `id` = ?'))
		        {
	                if ($stmt->bind_param('i', $collectionList[$i]))
	                {
	                    if ($stmt->bind_result($collectionProductCode))
	                    {
	                        if ($stmt->execute())
	                        {
	                            if ($stmt->fetch())
	                            {
	                            	$productCodesArray [] = $collectionProductCode;
	                            }
	                        }
	                    }
	                }
	                $stmt->free_result();
	                $stmt->close();
	                $stmt = null;
		        }
        	}

			$productCodesCount = count($productCodesArray);

        	if (($dbObj) && ($stmt = $dbObj->prepare('UPDATE `APPLICATIONFILES` SET `updatepriority` = ? WHERE `ref` = ?')))
        	{
        		for ($i = 0; $i < $productCodesCount; $i++)
        		{
        			if ($stmt->bind_param('is',$command, $productCodesArray[$i]))
        			{
        				if ($stmt->execute())
        				{
        					if ($command == 1000)
        					{
                            	DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'PRODUCTCOLLECTION-CHANGEHIGHPRIORITY', $productCodesArray[$i], 1);
                        	}
                        	else
                        	{
                            	DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'PRODUCTCOLLECTION-CHANGLOWPRIORITY', $productCodesArray[$i], 1);
                        	}
        				}
        			}
        		}
                $stmt->close();
        		$dbObj->close();
        		echo '{"success":true, "data":[{"id":' . $command . ',"active":"'.'1'.'"},]}';
        	}
        }
        catch (Exception $e)
        {
        	echo '{"success":false,	"msg":"' . $e->getMessage() . '"}';
    	}
    }

	static function changeApplicationPriority($pCommand, $pIDList, $pOSList)
	{
		global $gSession;

		$resultArray = UtilsObj::getReturnArray();
		$error = false;
		$errorParam = '';

		try
		{
			$applicationList = explode(',', $pIDList);
			$applicationCount = count($applicationList);
			$oslist = explode(',', $pOSList);
			$command = $pCommand;

			$dbObj = DatabaseObj::getGlobalDBConnection();

			if ($dbObj)
        	{
        		for($i = 0; $i < $applicationCount; $i++)
        		{
        			$webBrandCode = $applicationList[$i];
        			$operatingSystem = $oslist[$i];

        			if ($operatingSystem == 0)
        			{
						$sql = 'UPDATE `APPLICATIONBUILD` SET `macupdatepriority` = ? WHERE `webbrandcode` = ?';
						$stmt = $dbObj->prepare($sql);

        				if ($stmt)
        				{
        					if ($stmt->bind_param('is', $command, $webBrandCode))
        					{
								if ($stmt->execute())
								{
                        			DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'AUTOUPDATEAPPLICATION-CHANGEPRIORITY', 'Mac OS X', 1);
								}
								else
								{
									$error = true;
									$errorParam = __FUNCTION__ . "execute";
									
									break;
								}
                    		}
							else
							{
								$error = true;
								$errorParam = __FUNCTION__ . " bindparam";

								break;
							}

                    		$stmt->free_result();
        				}
						else
						{
							$error = true;
							$errorParam = __FUNCTION__ . " prepare";

							break;
						}
        			}
        			elseif ($operatingSystem == 1)
        			{
        				if ($stmt = $dbObj->prepare('UPDATE `APPLICATIONBUILD` SET `win32updatepriority` = ? WHERE `webbrandcode` = ?'))
        				{
                    		if ($stmt->bind_param('is', $command, $webBrandCode))
                    		{
								if ($stmt->execute())
								{
                        			DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'AUTOUPDATEAPPLICATION-CHANGEPRIORITY', 'Win32', 1);
								}
								else
								{
									$error = true;
									$errorParam = __FUNCTION__ . "execute";
									
									break;
								}
                    		}
							else
							{
								$error = true;
								$errorParam = __FUNCTION__ . " bindparam";

								break;
							}
                    		$stmt->free_result();
                		}
						else
						{
							$error = true;
							$errorParam = __FUNCTION__ . " prepare";

							break;
						}
        			}
        		}
				$stmt->close();
        		$dbObj->close();
        	}
			else
			{
				$error = true;
				$errorParam = __FUNCTION__ . " getGlobalDBConnection";
			}
		}
		catch (Exception $e)
		{
			$error = true;
			$resultArray['errorparam'] = $e->getMessage();
		}

		$resultArray['error'] = $error;
		$resultArray['errorparam'] = $errorParam;

		return $resultArray;
	}

    static function changeLicenseKeyPriority()
    {
        global $gSession;

        try
        {
        	$licenceList = explode(',', $_POST['idlist']);
            $licenceCount = count($licenceList);
            $command = $_POST['command'];
            $dbObj = DatabaseObj::getGlobalDBConnection();

        	if (($dbObj) && ($stmt = $dbObj->prepare('UPDATE `LICENSEKEYS` SET `keyupdatepriority` = ? WHERE `id` = ?')))
        	{
        		for ($i = 0; $i < $licenceCount; $i++)
        		{
        			$id = $licenceList[$i];
        			$licenseKeyArray = DatabaseObj::getLicenseKeyFromID($id);

        			if ($stmt->bind_param('ii',$command, $id))
        			{
        				if ($stmt->execute())
        				{
        					if ($command == 1000)
        					{
                            	DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'LICENSEKEY-CHANGEHIGHPRIORITY', $licenseKeyArray['recordid'] . ' ' . $licenseKeyArray['groupcode'], 1);
                        	}
                        	else
                        	{
                            	DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'LICENSEKEY-CHANGLOWPRIORITY', $licenseKeyArray['recordid'] . ' ' . $licenseKeyArray['groupcode'], 1);
                        	}
        				}
        			}
        		}
                $stmt->close();
        		$dbObj->close();
        		echo '{"success":true, "data":[{"id":' . $command . ',"active":"' . $command . '"},]}';
        	}
        }
        catch (Exception $e)
        {
        	echo '{"success":false,	"msg":"' . $e->getMessage() . '"}';
    	}
    }

    static function deleteLicenseKey()
    {
        global $gSession;
        global $ac_config;

        try
        {
        	$idList = explode(',', $_POST['idlist']);
            $idCount = count($idList);
            $codeList = explode(',', $_POST['codelist']);
            $codeCount = count($codeList);
            $dbObj = DatabaseObj::getGlobalDBConnection();

        	if ($dbObj)
        	{
        		for($i = 0; $i < $idCount; $i++)
        		{
        			$resultID = 0;
        			$filename = '';
        			$result = '';
        			$id = $idList[$i];
        			$code = $codeList[$i];
        			$canDelete = self::canDelete($code);

        			if ($canDelete == true)
        			{
                		if ($stmt = $dbObj->prepare('SELECT `id`, `keyfilename` FROM `LICENSEKEYS` WHERE (`id` = ?)'))
                		{
                    		if ($stmt->bind_param('i', $id))
                    		{
                        		if ($stmt->bind_result($resultID, $filename))
                        		{
                            		if ($stmt->execute())
                            		{
                                		if (! $stmt->fetch())
                                		{
                                			$resultID = 0;
                                		}
                            		}
                        		}
                    		}
                    		$stmt->free_result();
                    		$stmt->close();
                    		if ($resultID > 0)
                    		{
                        		if ($stmt = $dbObj->prepare('DELETE FROM `LICENSEKEYS` WHERE `id` = ?'))
                        		{
                            		if ($stmt->bind_param('i', $id))
                            		{
                                		if ($stmt->execute())
                                		{
                                    		DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                        		'ADMIN', 'LICENSEKEY-DELETE', $id . ' ' . $code, 1);
                                    		$sourceFilePath = $ac_config['INTERNALLICENSEKEYSROOTPATH'] . $filename;
                                    		UtilsObj::deleteFile($sourceFilePath);

											// delete the promo panel
											self::deletePromoPanel($code);
                                		}
                            		}
                            		$stmt->free_result();
                            		$stmt->close();
                        		}
                    		}
                		}
            		}
        		}
        		$dbObj->close();
        		echo '{"success":true, "data":[{"id":' . '1' . ',"active":"'.'1'.'"},]}';
        	}
        	else
        	{
        		echo '{"success":false,	"msg":"' . 'error message' . '"}';
        	}
        }
        catch (Exception $e)
        {
        	echo '{"success":false,	"msg":"' . $e->getMessage() . '"}';
        }
    }

    static function editLicenseKeyDisplay()
    {
        global $ac_config;
        $resultArray = Array();
        $result = '';
        $licenseKeyID = UtilsObj::getGETParam('id', 0);

        if ($licenseKeyID)
        {
            $resultArray = DatabaseObj::getLicenseKeyFromID($licenseKeyID);
            $result = $resultArray['result'];

            if ($result == '')
            {
				$additionalAddressFields = UtilsAddressObj::getAdditionalAddressFields($resultArray['countrycode'], $resultArray['address4']);
				$resultArray['add41'] = $additionalAddressFields['add41'];
				$resultArray['add42'] = $additionalAddressFields['add42'];
				$resultArray['add43'] = $additionalAddressFields['add43'];
                $resultArray['webbrandinglist'] = DatabaseObj::getBrandingList($resultArray['companyCode']);
                $resultArray['currencylist'] = DatabaseObj::getCurrencyList();
                $resultArray['paymentmethodslist'] = DatabaseObj::getPaymentMethodsList();
                $resultArray['allowimagescalingbefore'] = UtilsObj::getArrayParam($ac_config, "ALLOWIMAGESCALINGBEFORE", 0) == 1;

				$componentUpsellSettings = $resultArray['componentupsellsettings'];     
				$resultArray['componentupsellenabled'] = ($componentUpsellSettings & TPX_COMPONENT_UPSELL_ENABLED) ? 1 : 0;
				$resultArray['componentupsellproductquantity'] = ($componentUpsellSettings & TPX_COMPONENT_UPSELL_ALLOW_PRODUCT_QTY) ? 1 : 0;

				$fontListDetails = AdminTaopixOnlineFontLists_model::getFontListData($ac_config, 'groupcode', $resultArray['groupcode']);
				$resultArray['fontlists'] = $fontListDetails['fontlists'];
				$resultArray['fontlistselected'] = $fontListDetails['selected'];
            }
            else
            {
                $result = '.';
            }
        }
        else
        {
            $result = '.';
        }
        $resultArray['result'] = $result;
        return $resultArray;
    }

    static function editLicenseKey()
    {
        global $gSession;
        global $gConstants;
        global $ac_config;

        $resultArray = Array();
        $licenseKeyID = $_GET['id'];

        if ($licenseKeyID)
        {
        	$resultArray = DatabaseObj::getLicenseKeyFromID($licenseKeyID);
        	if ($resultArray['result'] == '')
        	{
        		$licenseKeyCode = $resultArray['groupcode'];
        		$origPassword = $resultArray['password'];
        		$origAvailableOnlineStatus = $resultArray['availableonline'];

            	$resultArray['login'] = $_POST['login'];
            	$password = $_POST['password'];

            	if ($password != '**UNCHANGED**')
            	{
            		$resultArray['password'] = $password;
            	}
            	else
            	{
            		$resultArray['password'] = $origPassword;
            	}

				// see if there are special address fields like
				// add1=add41, add42 - add43
				// meaning address1 = add41 + ", "  + add42 + " - " + add43
				// and     address4 = add41 + "<p>" + add42 + "<p>" + add43
				UtilsAddressObj::specialAddressFields($_POST['countryCode']);

                $resultArray['contactfirstname'] = $_POST['contactFirstName'];
				$resultArray['contactlastname'] = $_POST['contactLastName'];
                $resultArray['companyname'] = $_POST['companyName'];
                $resultArray['address1'] = $_POST['address1'];
                $resultArray['address2'] = $_POST['address2'];
                $resultArray['address3'] = $_POST['address3'];

    			// we need to check to see if the string contains @@TAOPIXTAG@@. If it does then this means that it is a special address field.
				// we then need to convert @@TAOPIXTAG@@ back to a <p> so that it can be stored correctly in the database.
				$address4 = implode('<p>', mb_split('@@TAOPIXTAG@@', $_POST['address4']));

                $resultArray['address4'] = $address4;
                $resultArray['city'] = $_POST['city'];
                $resultArray['state'] = $_POST['stateName'];
                $resultArray['county'] = $_POST['countyName'];
                $resultArray['regioncode'] = $_POST['regionCode'];
                $resultArray['region'] = $_POST['region'];
                $resultArray['postcode'] = $_POST['postCode'];
                $resultArray['countrycode'] = $_POST['countryCode'];
                $resultArray['countryname'] = $_POST['countryName'];
                $resultArray['telephonenumber'] = $_POST['telephonenumber'];
                $resultArray['emailaddress'] = $_POST['email'];
                $resultArray['webbrandcode'] = $_POST['webbrandcode'];
                $resultArray['usedefaultpaymentmethods'] = $_POST['usedefaultpaymentmethods'];
                $resultArray['paymentmethods'] = $_POST['paymentmethods'];
                $resultArray['showpriceswithtax'] = $_POST['showpriceswithtax'];
                $resultArray['showtaxbreakdown'] = $_POST['showtaxbreakdown'];
                $resultArray['showzerotax'] = $_POST['showzerotax'];
                $resultArray['showalwaystaxtotal'] = $_POST['showalwaystaxtotal'];
                $resultArray['cancreateaccounts'] = $_POST['cancreateaccounts'];
                $resultArray['useaddressforbilling'] = $_POST['useaddressforbilling'];
                $resultArray['useremaildestination'] = $_POST['useremaildestination'];
                $resultArray['orderfrompreview'] = $_POST['orderfrompreview'];
                $resultArray['useaddressforshipping'] = $_POST['useaddressforshipping'];
                $resultArray['canmodifyshippingaddress'] = $_POST['canmodifyshippingaddress'];
                $resultArray['canmodifybillingaddress'] = $_POST['canmodifybillingaddress'];
                $resultArray['canmodifyshippingcontactdetails'] = $_POST['canmodifyshippingcontactdetails'];
                $resultArray['usedefaultcurrency'] = $_POST['usedefaultcurrency'];
                $resultArray['currencycode'] = $_POST['currencycode'];
                $resultArray['taxcode'] = $_POST['taxcode'];
				$resultArray['shippingtaxcode'] = $_POST['shippingtaxcode'];
				$resultArray['registeredtaxnumbertype'] = $_POST['validregisteredtaxnumbertype'];
				$resultArray['registeredtaxnumber'] = $_POST['validregisteredtaxnumber'];

                // if we have desol enabled and not desdt then we need to update both the active flag and the availableonline flag
                if (($gConstants['optiondesol']) && (!$gConstants['optiondesdt']))
				{
					$resultArray['isactive'] = $_POST['isactive'];
                	$resultArray['availableonline'] = $_POST['isactive'];
				}
				else
				{
					$resultArray['isactive'] = $_POST['isactive'];
                	$resultArray['availableonline'] = $origAvailableOnlineStatus;
				}

                $resultArray['splashscreenupdate'] = $_POST['previewupdate'];
                $resultArray['splashscreenremove'] = $_POST['previewremove'];
                $resultArray['splashscreenassetid'] = $_POST['assetid'];
                $resultArray['splashscreenstartdate'] = $_POST['splashscreenstartdatevalue'];
                $resultArray['splashscreenenddate'] = $_POST['splashscreenenddatevalue'];
                $resultArray['bannerupdate'] = $_POST['bannerupdate'];
                $resultArray['bannerremove'] = $_POST['bannerremove'];
                $resultArray['bannerassetid'] = $_POST['bannerassetid'];
                $resultArray['bannerstartdate'] = $_POST['bannerstartdatevalue'];
                $resultArray['bannerenddate'] = $_POST['bannerenddatevalue'];
                $resultArray['guestworkflowmode'] = $_POST['guestworkflowmode'];
                $resultArray['imagescalingafter'] = $_POST['imagescalingafter'];
                $resultArray['imagescalingafterenabled'] = $_POST['imagescalingafterenabled'];
                $resultArray['usedefaultimagescalingafter'] = $_POST['usedefaultimagescalingafter'];
                $resultArray['imagescalingbefore'] = 0.0;
                $resultArray['imagescalingbeforeenabled'] = 0;
                $resultArray['usedefaultimagescalingbefore'] = 0;

                // only allow the image scaling values to be set if the ALLOWIMAGESACING option is switched on
                if (UtilsObj::getArrayParam($ac_config, "ALLOWIMAGESCALINGBEFORE", 0) == 1)
                {
                    $resultArray['imagescalingbefore'] = $_POST['imagescalingbefore'];
                    $resultArray['imagescalingbeforeenabled'] = $_POST['imagescalingbeforeenabled'];
                    $resultArray['usedefaultimagescalingbefore'] = $_POST['usedefaultimagescalingbefore'];
                }

                // make sure that the imagescaling value is set and is greater than 0 and less thant 999.99
                if (($resultArray['imagescalingbefore'] == '') || ($resultArray['imagescalingbefore'] > 999.99))
                {
                    $resultArray['imagescalingbefore'] = 0.0;
                }

                // make sure that the imagescaling value is set and is greater than 0 and less thant 999.99
                if (($resultArray['imagescalingafter'] == '') || ($resultArray['imagescalingafter'] > 999.99))
                {
                    $resultArray['imagescalingafter'] = 0.0;
                }

				$resultArray['onlinedesignerlogolinkurl'] = $_POST['onlinedesignerlogolinkurl'];
				$resultArray['usedefaultlogolinkurl'] = $_POST['usedefaultlogolinkurl'];
				$resultArray['onlinedesignerlogolinktooltip'] = $_POST['onlinedesignerlogolinktooltip'];

				$resultArray['usedefaultvouchersettings'] = $_POST['usedefaultvouchersettings'];
				$resultArray['allowvouchers'] = $_POST['allowvouchers'];
				$resultArray['allowgiftcards'] = $_POST['allowgiftcards'];

				$resultArray['usedefaultautomaticallyapplyperfectlyclear'] = UtilsObj::getPOSTParam('usedefaultautomaticallyapplyperfectlyclear');
				$resultArray['automaticallyapplyperfectlyclear'] = UtilsObj::getPOSTParam('automaticallyapplyperfectlyclear');
				$resultArray['allowuserstotoggleperfectlyclear'] = UtilsObj::getPOSTParam('toggleperfectlyclear');

				// desktop designer settings
				$resultArray['usedefaultaccountpagesurl'] = UtilsObj::getPOSTParam('usedefaultaccountpagesurl', 0);
				$resultArray['accountpagesurl'] = UtilsObj::getPOSTParam('accountpagesurl', '');
				$resultArray['promopanelmode'] = UtilsObj::getPOSTParam('promopanelmode', TPX_DESKTOPDESIGNER_PROMOPANELOVERRIDEMODE_USEDEFAULT);
				$resultArray['promopanelremove'] = UtilsObj::getPOSTParam('promopanelremove', 0);
				$resultArray['promopanelupdate'] = UtilsObj::getPOSTParam('promopanelupdate', 0);
				$resultArray['promopaneldirty'] = UtilsObj::getPOSTParam('promopaneldirty', 0);
				$resultArray['promopanelstartdate'] = UtilsObj::getPOSTParam('promopanelstartdate', '0000-00-00 00:00:00');
				$resultArray['promopanelenddate'] = UtilsObj::getPOSTParam('promopanelenddate', '0000-00-00 00:00:00');
				$resultArray['promopanelurl'] = UtilsObj::getPOSTParam('promopanelurl', '');
				$resultArray['promopanelheight'] = UtilsObj::getPOSTParam('promopanelheight', 0);
				$resultArray['promopaneldevicepixelratio'] = UtilsObj::getPOSTParam('promopaneldevicepixelratio', 1);
				$resultArray['promopanelhidpicantoggle'] = UtilsObj::getPOSTParam('promopanelhidpicantoggle', 0);

            	$resultArray = self::updateLicenseKey($resultArray);
                $result = $resultArray['result'];
                $resultParam = $resultArray['resultparam'];

                if ($result == '')
                {
					$fontListDetails = [
						'type' => UtilsObj::getPOSTParam('fontlisttype', -1),
						'fontlist' => UtilsObj::getPOSTParam('fontlist', null),
						'codes' => [$licenseKeyCode],
						'checkfield' => 'groupcode',
					];
					AdminTaopixOnlineFontLists_model::updateAssignments($fontListDetails, $ac_config);
                    DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'LICENSEKEY-UPDATE', $licenseKeyID . ' ' . $licenseKeyCode, 1);
                	echo '{"success":true, "data":[{"id":' . '0' . ',"active":"'.'1'.'"},]}';
                }
                else
                {
                	$smarty = SmartyObj::newSmarty('AdminAutoUpdateLicenseKeys');
                    echo '{"success":false,	"msg":"' . str_replace('^0', $resultParam, $smarty->get_config_vars($result)) . '"}';
                }
            }
            else
            {
                echo '{"success":false,	"msg":"' . 'error' . '"}';
            }
        }
        else
        {
            echo '{"success":false,	"msg":"' . 'no licence key' . '"}';
        }
    }

	static function updateLicenseKey($resultArray)
    {
        global $gSession;

        // update the license key database record
        $result = '';
        $resultParam = '';
        $duplicateLogin = true;
		$groupCode = $resultArray['groupcode'];
		$promoPanelDirty = $resultArray['promopaneldirty'];

        if ($resultArray['login'] == '')
        {
            $duplicateLogin = false;
        }
        else
        {
            $loginArray = DatabaseObj::getLicenseKeyFromLogin($resultArray['login']);
            if (($loginArray['recordid'] == 0) || ($loginArray['recordid'] == $resultArray['recordid']))
            {
                $duplicateLogin = false;
            }
        }

        if ($duplicateLogin == false)
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                $dbObj->query('START TRANSACTION');

                if ($resultArray['promopanelupdate'] == 1)
                {
                    // update the promo panel on the file system if required
					$result = self::savePromoPanel($groupCode);

					if ($result == '')
					{
						// update the metadata file
						$result = self::buildPromoPanelMetadataFile($groupCode, $resultArray['promopanelmode'], $resultArray['promopanelstartdate'], $resultArray['promopanelenddate'], 
							$resultArray['promopanelurl'], $resultArray['promopaneldevicepixelratio'], $resultArray['promopanelheight']);
					}
                }
				elseif ($resultArray['promopanelremove'] == 1)
				{
					// delete the promo panel and its metadata file on the file system if required
					$result = self::deletePromoPanel($groupCode);

					// build a new metadata file
					$result = self::buildPromoPanelMetadataFile($groupCode, $resultArray['promopanelmode'], $resultArray['promopanelstartdate'], $resultArray['promopanelenddate'], 
						$resultArray['promopanelurl'], $resultArray['promopaneldevicepixelratio'], $resultArray['promopanelheight']);
				}
				elseif ($promoPanelDirty == 1)
				{
					$result = self::buildPromoPanelMetadataFile($groupCode, $resultArray['promopanelmode'], $resultArray['promopanelstartdate'], $resultArray['promopanelenddate'], 
						$resultArray['promopanelurl'], $resultArray['promopaneldevicepixelratio'], $resultArray['promopanelheight']);
				}
				

				if ($result == '')
				{
					// if the license key brand has changed we need to update all customer accounts
					$originalLicenseKeyArray = DatabaseObj::getLicenseKeyFromID($resultArray['recordid']);
					if ($originalLicenseKeyArray['webbrandcode'] != $resultArray['webbrandcode'])
					{
						if ($stmt = $dbObj->prepare('UPDATE `USERS` SET `webbrandcode` = ? WHERE (`groupcode` = ?) AND (`customer` = 1)'))
						{
							if ($stmt->bind_param('ss', $resultArray['webbrandcode'], $originalLicenseKeyArray['groupcode']))
							{
								if (!$stmt->execute())
								{
									// could not execute the statement which probably means there is already a customer for the brand now associated with the license key
									if ($stmt->errno == 1062)
									{
										$result = 'str_ErrorUsernameAlreadyExistsInBrand';
									}
									else
									{
										$result = 'str_DatabaseError';
										$resultParam = 'updateLicenseKeys update users execute ' . $dbObj->error;
									}
								}
							}
							else
							{
								// could not bind parameters
								$result = 'str_DatabaseError';
								$resultParam = 'updateLicenseKeys update users bind ' . $dbObj->error;
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'updateLicenseKeys update users prepare ' . $dbObj->error;
						}
					}
				}


                // if the customers have been updated we need to update all asset records for the license key
                if ($result == '')
                {
                    $assetID = $resultArray['splashscreenassetid'];
                    $bannerAssetID = $resultArray['bannerassetid'];

                    if ($resultArray['splashscreenupdate'] == '1')
                    {
                        $logoPath = $gSession['previewpath'];
                        $logoType = $gSession['previewtype'];

                        $assetName = 'SPLASHSCREEN IMAGE FOR LICENSEKEY ' . $groupCode;
                        $result1 = DatabaseObj::updatePreviewImage($resultArray['splashscreenassetid'], $logoPath, $logoType, $assetName);
                        $result = $result1['result'];
                        $resultParam = $result1['resultparam'];

                        $assetID = $result1['assetid'];
                    }

                    if ($resultArray['splashscreenremove'] == '1')
                    {
                        $logoPath = '';
                        $logoType = '';
                        $result1 = DatabaseObj::deleteAssetRecord($resultArray['splashscreenassetid']);
                        $result = $result1['result'];
                        $resultParam = $result1['resultparam'];

                        if ($result1['result'] == '')
                        {
                            $assetID = 0;
                        }
                    }

                    if ($resultArray['bannerupdate'] == '1')
                    {
                        $logoPath = $gSession['bannerpreviewpath'];
                        $logoType = $gSession['bannerpreviewtype'];

                        $assetName = 'BANNER IMAGE FOR LICENSEKEY ' . $groupCode;
                        $result1 = DatabaseObj::updatePreviewImage($resultArray['bannerassetid'], $logoPath, $logoType, $assetName);
                        $result = $result1['result'];
                        $resultParam = $result1['resultparam'];

                        $bannerAssetID = $result1['assetid'];
                    }

                    if ($resultArray['bannerremove'] == '1')
                    {
                        $logoPath = '';
                        $logoType = '';
                        $result1 = DatabaseObj::deleteAssetRecord($resultArray['splashscreenassetid']);
                        $result = $result1['result'];
                        $resultParam = $result1['resultparam'];

                        if ($result1['result'] == '')
                        {
                            $bannerAssetID = 0;
                        }
                    }
                }

                // if the assets have been updated we need to update the license key record
                if ($result == '')
                {
					$sql = 'UPDATE `LICENSEKEYS` SET `name` = ?, `address1` = ?, `address2` = ?, `address3` = ?, `address4` = ?,
						`city` = ?, `county` = ?, `state` = ?, `regioncode` = ?, `region` = ?, `postcode` = ?, `countrycode` = ?, `countryname` = ?,
						`telephonenumber` = ?, `emailaddress` = ?, `contactfirstname` = ?, `contactlastname` = ?, `createaccounts` = ?,
						`useaddressforbilling` = ?, `useaddressforshipping` = ?, `modifyshippingaddress` = ?, `modifybillingaddress` = ?,
						`modifyshippingcontactdetails` = ?, `useremaildestination` = ?, `orderfrompreview` = ?, `showpriceswithtax` = ?,
						`showtaxbreakdown` = ?, `showzerotax` = ?, `showalwaystaxtotal` = ?, `login` = ?, `password` = ?, `webbrandcode` = ?,
						`usedefaultcurrency` = ?, `currencycode` = ?, `taxcode` = ?, `shippingtaxcode` = ?, `registeredtaxnumbertype` = ?,
						`registeredtaxnumber` = ?, `usedefaultpaymentmethods` = ?, `paymentmethods` = ?, `active` = ?, `designersplashscreenassetid` = ?,
						`designerbannerassetid` = ?, `designersplashscreenstartdate` = ?, `designersplashscreenenddate` = ?, `designerbannerstartdate` = ?,
						`designerbannerenddate` = ?, `onlinedesignerguestworkflowmode` = ?, `availableonline` = ?,
                        `imagescalingbefore` = ?, `imagescalingbeforeenabled` = ?, `usedefaultimagescalingbefore` = ?,
                        `imagescalingafter` = ?, `imagescalingafterenabled` = ?, `usedefaultimagescalingafter` = ?,
						`onlinedesignerlogolinkurl` = ?, `usedefaultonlinedesignerlogolinkurl` = ?, `onlinedesignerlogolinktooltip` = ?,
						`usedefaultvouchersettings` = ?, `allowvouchers` = ?, `allowgiftcards` = ?,
						`usedefaultautomaticallyapplyperfectlyclear` = ?, `automaticallyapplyperfectlyclear` = ?, `allowuserstotoggleperfectlyclear` = ?,
                        `usedefaultaccountpagesurl` = ?, `accountpagesurl` = ?, `promopaneloverridemode` = ?,
                        `promopaneloverridestartdate` = ?, `promopaneloverrideenddate` = ?, `promopaneloverrideurl` = ?, `promopaneloverrideheight` = ?,
						`promopaneloverridepixelratio` = ?, `promopaneloverridehidpicantoggle` = ?
						WHERE `id` = ?';

                    if ($stmt = $dbObj->prepare($sql))
                    {
                        if ($stmt->bind_param('sssss' . 'ssssssss' . 'ssssi' . 'iiii' . 'iiii' . 'iiisss' . 'isssi' . 'sisii' . 'isss' . 'sii' .
                                                'dii' . 'dii' . 'sis' .  'iii' . 'iii' . 'isi' . 'sssi' . 'ii' . 'i',
                                        $resultArray['companyname'], $resultArray['address1'], $resultArray['address2'],
                                        $resultArray['address3'], $resultArray['address4'], $resultArray['city'], $resultArray['county'],
                                        $resultArray['state'], $resultArray['regioncode'], $resultArray['region'], $resultArray['postcode'],
                                        $resultArray['countrycode'], $resultArray['countryname'], $resultArray['telephonenumber'],
                                        $resultArray['emailaddress'], $resultArray['contactfirstname'], $resultArray['contactlastname'],
                                        $resultArray['cancreateaccounts'], $resultArray['useaddressforbilling'],
                                        $resultArray['useaddressforshipping'], $resultArray['canmodifyshippingaddress'],
                                        $resultArray['canmodifybillingaddress'], $resultArray['canmodifyshippingcontactdetails'],
                                        $resultArray['useremaildestination'], $resultArray['orderfrompreview'], $resultArray['showpriceswithtax'],
                                        $resultArray['showtaxbreakdown'], $resultArray['showzerotax'], $resultArray['showalwaystaxtotal'],
                                        $resultArray['login'], $resultArray['password'], $resultArray['webbrandcode'],
                                        $resultArray['usedefaultcurrency'], $resultArray['currencycode'], $resultArray['taxcode'],
                                        $resultArray['shippingtaxcode'], $resultArray['registeredtaxnumbertype'],
                                        $resultArray['registeredtaxnumber'], $resultArray['usedefaultpaymentmethods'],
                                        $resultArray['paymentmethods'], $resultArray['isactive'], $assetID, $bannerAssetID,
                                        $resultArray['splashscreenstartdate'], $resultArray['splashscreenenddate'],
                                        $resultArray['bannerstartdate'], $resultArray['bannerenddate'], $resultArray['guestworkflowmode'],
                                        $resultArray['availableonline'],
                                        $resultArray['imagescalingbefore'], $resultArray['imagescalingbeforeenabled'], $resultArray['usedefaultimagescalingbefore'],
                                        $resultArray['imagescalingafter'], $resultArray['imagescalingafterenabled'], $resultArray['usedefaultimagescalingafter'],
										$resultArray['onlinedesignerlogolinkurl'], $resultArray['usedefaultlogolinkurl'], $resultArray['onlinedesignerlogolinktooltip'],
										$resultArray['usedefaultvouchersettings'], $resultArray['allowvouchers'], $resultArray['allowgiftcards'],
										$resultArray['usedefaultautomaticallyapplyperfectlyclear'], $resultArray['automaticallyapplyperfectlyclear'], $resultArray['allowuserstotoggleperfectlyclear'],
                                        $resultArray['usedefaultaccountpagesurl'], $resultArray['accountpagesurl'], $resultArray['promopanelmode'],
                                        $resultArray['promopanelstartdate'], $resultArray['promopanelenddate'], $resultArray['promopanelurl'], $resultArray['promopanelheight'],
										$resultArray['promopaneldevicepixelratio'], $resultArray['promopanelhidpicantoggle'],
                                        $resultArray['recordid']))
                        {
                            if (!$stmt->execute())
                            {
                                // could not execute statement
                                $result = 'str_DatabaseError';
                                $resultParam = 'updateLicenseKey execute ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            // could not bind parameters
                            $result = 'str_DatabaseError';
                            $resultParam = 'updateLicenseKey bind ' . $dbObj->error;
                        }

                        $stmt->free_result();
                        $stmt->close();
                    }
                    else
                    {
                        // could not prepare statement
                        $result = 'str_DatabaseError';
                        $resultParam = 'updateLicenseKey prepare ' . $dbObj->error;
                    }
                }


                // if no errors have occurred commit the transaction, otherwise roll it back
                if ($result == '')
                {
                    $dbObj->query('COMMIT');
                }
                else
                {
                    $dbObj->query('ROLLBACK');
                }

                $dbObj->close();
            }
            else
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'updateLicenseKey connect ' . $dbObj->error;
            }
        }
        else
        {
            // a license key with this login exists
            $result = 'str_ErrorLoginExists';
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
		
        return $resultArray;
    }

    static function getAutoUpdateProductCollectionsList($pCompanyCode, $pSort, $pSortDirection, $pHideInactive)
	{
	    // return an array containing each product collection and its products
	    global $gSession;

        $result = '';
        $resultParam = '';
        $filelist = Array();

		$id = 0;
		$collectionCompanyCode = '';
		$collectionCode = '';
		$collectionProductCode = '';
    	$fileCategoryCode = '';
    	$fileName = '';
    	$fileDateModified = '';
    	$isActive = 0;
		$fileUpdatePriority = '';
		$hideInactiveString = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
            if ($gSession['userdata']['usertype'] != TPX_LOGIN_SYSTEM_ADMIN)
			{
                if ($pCompanyCode == '')
				{
					$pCompanyCode = $gSession['userdata']['companycode'];
				}
            }
            else
            {
                if ($pCompanyCode == 'GLOBAL')
	    		{
	    			$pCompanyCode = '';
	    		}
            }

            $searchFields = UtilsObj::getPOSTParam('fields');
            $typesArray = array();
            $paramArray = array();
            $stmtArray = array();
            if ($searchFields != '')
            {
                $searchQuery = $_POST['query'];
                $selectedfields = explode(',', str_replace('"', "", str_replace("]", "", str_replace("[", "",$_POST['fields']))));

                if ($searchQuery != '')
                {
                    foreach ($selectedfields as $value)
                    {
                        switch ($value)
    				{
                        case 'code':
                            $value = 'collectioncode';
                            break;
                        case 'products':
                            $value = 'productcode';
                            break;
    				}

					$stmtArray[] = '(`'.$value.'` LIKE ?)';
					$paramArray[] = '%'.$searchQuery.'%';
					$typesArray[] = 's';
                    }
				}
				else
				{
					if ($pHideInactive == 1)
					{
						$hideInactiveString = ' AND (`af`.`active` = 1)';
					}
				}
			}
			else
			{
				if ($pHideInactive == 1)
				{
					$hideInactiveString = ' AND (`af`.`active` = 1)';
				}
			}

            if (count($stmtArray) > 0)
            {
                $stmtArray = ' AND (' . join(' OR ', $stmtArray) . ')';

            }
            else
            {
                $stmtArray = '';
            }

            if ($pCompanyCode != '**ALL**')
            {
                $stmt = $dbObj->prepare('SELECT pcl.id, pcl.companycode, pcl.collectioncode, pcl.productcode,
				af.name, af.versiondate, af.updatepriority, af.active
				FROM  `PRODUCTCOLLECTIONLINK` pcl
                    INNER JOIN `APPLICATIONFILES` AS af ON af.ref = pcl.collectioncode
                    LEFT JOIN `BRANDING` ON BRANDING.code = af.webbrandcode
				WHERE (af.type = 0)
                    AND af.deleted = 0 ' . $hideInactiveString . $stmtArray . ' AND (pcl.companycode = ?)
                ORDER BY pcl.companycode, pcl.collectioncode, pcl.productcode');
                $paramArray[] = $pCompanyCode;
                $typesArray[] = 's';
			}
			else
			{
				$stmt = $dbObj->prepare('SELECT pcl.id, pcl.companycode, pcl.collectioncode, pcl.productcode,
				af.name, af.versiondate, af.updatepriority, af.active
				FROM  `PRODUCTCOLLECTIONLINK` pcl
                    INNER JOIN `APPLICATIONFILES` af ON af.ref = pcl.collectioncode
				WHERE (af.type = 0) AND (af.deleted = 0) ' . $hideInactiveString . $stmtArray . '
                ORDER BY pcl.companycode, pcl.collectioncode, pcl.productcode');

			}

			if ($stmt)
			{
                DatabaseObj::bindParams($stmt,$typesArray, $paramArray);
                if ($stmt->bind_result($id, $collectionCompanyCode, $collectionCode, $collectionProductCode, $fileName,
                        $fileDateModified, $fileUpdatePriority, $isActive))
                {
                    if (! $stmt->execute())
                    {
                        // could not execute statement
                        $result = 'str_DatabaseError';
                        $resultParam = 'getAutoUpdateProductCollectionsList execute ' . $dbObj->error;
                    }
                }
                else
                {
                    // could not bind result
                    $result = 'str_DatabaseError';
                    $resultParam = 'getAutoUpdateProductCollectionsList bind result ' . $dbObj->error;
                }
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'getAutoUpdateProductCollectionsList prepare ' . $dbObj->error;
			}

            if ($result == '')
            {
                // process each file
                while ($stmt->fetch())
                {

					$fileItem['id'] = $id;
					$fileItem['collectioncode'] = $collectionCode;
					$fileItem['name'] = $fileName;
					$fileItem['productcode'] = $collectionProductCode;
					$fileItem['datemodified'] = $fileDateModified;
                    $fileItem['updatepriority'] = $fileUpdatePriority;
					$fileItem['isactive'] = $isActive;
					$fileItem['company'] = $collectionCompanyCode;

					array_push($filelist, $fileItem);
                }
            }

            if ($stmt)
            {
                $stmt->free_result();
                $stmt->close();
                $stmt = null;
            }

            $dbObj->close();
        }
        else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'getAutoUpdateProductCollectionsList connect ' . $dbObj->error;
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['productlist'] = $filelist;

        return $resultArray;
	}

	static function getDesignerSplashScreenImage()
    {
		$resultArray = DatabaseObj::getPreviewImage();

        return $resultArray;
    }

    static function uploadPreviewImage($pSection)
    {
    	$resultArray = DatabaseObj::uploadPreviewImage($pSection);

	    return $resultArray;
    }


    static function getBannerImage()
    {
		global $gSession;

        $resultArray = Array();
        $previewType = '';
        $previewWidth = '';
        $previewHeight = '';
        $image = '';

        $assetID = $_GET['id'];
        $showTempFile = $_GET['tmp'];

        if (($showTempFile == '1') && ($gSession['bannerpreviewpath'] != ''))
        {
            // a temporary image file has been uploaded
            $previewPath = $gSession['bannerpreviewpath'];
            $previewType = $gSession['bannerpreviewtype'];
            $previewSize = filesize($previewPath);

            list($previewWidth, $previewHeight) = getimagesize($previewPath);

            // read the image data
            $fp = fopen($previewPath, 'rb');
            if ($fp)
            {
                $image = fread($fp, $previewSize);
                fclose($fp);
            }
        }
        else
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj)
            {
                if ($stmt = $dbObj->prepare('SELECT `previewtype`, `data`, `previewwidth`, `previewheight` FROM `ASSETDATA` WHERE (`id` = ?)'))
                {
                    if ($stmt->bind_param('i', $assetID))
                    {
                        if ($stmt->execute())
                        {
                            if ($stmt->store_result())
                            {
                                if ($stmt->num_rows > 0)
                                {
                                    if ($stmt->bind_result($previewType, $image, $previewWidth, $previewHeight))
                                    {
                                        if(!$stmt->fetch())
                                        {
                                            $error =  'getBannerImage fetch ' . $dbObj->error;
                                        }
                                    }
                                    else
                                    {
                                        $error =  'getBannerImage bind_result ' . $dbObj->error;
                                    }
                                }
                            }
                            else
                            {
                                $error =  'getBannerImage store result ' . $dbObj->error;
                            }
                        }
                        else
                        {
                            $error =  'getBannerImage execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $error =  'getBannerImage bind params ' . $dbObj->error;
                    }
                    $stmt->free_result();
                    $stmt->close();
                    $stmt = null;
                }
                else
                {
                    $error =  'getBannerImage prepare: ' . $dbObj->error;
                }
                $dbObj->close();
            }
        }

        $resultArray['previewtype'] = $previewType;
        $resultArray['image'] = $image;
        $resultArray['previewwidth'] = $previewWidth;
        $resultArray['previewheight'] = $previewHeight;

        return $resultArray;
    }

    static function uploadBannerImage()
    {
    	global $gSession;

        $result = '';
        $resultParam = '';
        $width = 0;
        $height = 0;
        $logoPath = $_FILES['bannerpreview']['tmp_name'];
        $logoPathNew = '';
        $logoType = $_FILES['bannerpreview']['type'];
        $fileUploadResultArray = Array('result' => '', 'filetype' => '', 'filedata' => '');

        list($width, $height) = getimagesize($logoPath);

        $validImageTypes = Array('image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png');

        // make sure that the file is a valid type
        if (in_array(strtolower($logoType), $validImageTypes))
        {
            // first make sure that we are dealing with a file that has been uploaded
            // create a new temporary file
            $logoPathNew = tempnam(sys_get_temp_dir(), 'LGO');
            if (move_uploaded_file($logoPath, $logoPathNew))
            {
                $gSession['bannerpreviewpath'] = $logoPathNew;
                $gSession['bannerpreviewtype'] = $logoType;
                DatabaseObj::updateSession();
            }
        }
        else
        {
            $result = 'str_ErrorUploadInvalidFileType';
        }

        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['width'] = $width;
        $resultArray['height'] = $height;

        return $resultArray;
    }

	/**
	 * Saves an uploaded promo panel to a temporary location and stores the path in the session
	 */
	static function uploadPromoPanelImage()
	{
		global $gSession;

        $result = '';
        $resultParam = '';
        $promoPanelPath = $_FILES['promopanelpreview']['tmp_name'];
        $promoPanelPathNew = '';
        $promoPanelType = $_FILES['promopanelpreview']['type'];
		$requiresHiDPI = 0;
		$height = 0;
		$width = 0;

        $validImageTypes = Array('image/jpeg', 'image/pjpeg');

        // make sure that the file is a valid type
        if (in_array(strtolower($promoPanelType), $validImageTypes))
        {
			list($width, $height) = getimagesize($promoPanelPath);

			if (($width <= 2040) && ($height <= 600))
			{
				if (($width > 1020) || ($height > 300))
				{
					$requiresHiDPI = 1;
				}

				// first make sure that we are dealing with a file that has been uploaded
				// create a new temporary file
				$promoPanelPathNew = tempnam(sys_get_temp_dir(), 'LGO');
				if (move_uploaded_file($promoPanelPath, $promoPanelPathNew))
				{
					$gSession['promopanelpreviewpath'] = $promoPanelPathNew;
					DatabaseObj::updateSession();
				}
			}
			else
			{
				$result = 'INVALIDDIMENSIONS';
			}
        }
        else
        {
            $result = 'str_ErrorUploadInvalidFileType';
        }

		$resultArray['hidpi'] = $requiresHiDPI;
		$resultArray['height'] = $height;
		$resultArray['width'] = $width;
        $resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;

        return $resultArray;
	}

	/**
	 * Get the promo panel image override to display in the configuration screen
	 * @param string $pGroupCode The license key code to display the image for
	 * @param int $pShowTempFile Whether to return an unsaved but previously uploaded promo panel image
	 * @return array standard taopix return array with either raw image bytes in the image key or a url to redirect to in the url key
	 */
	static function getPromoPanelImage($pGroupCode, $pShowTempFile)
	{
		global $gSession;

        $resultArray = Array();
        $image = '';
		$url = '';

		// if we have a temporary preview then load it in and return it
        if (($pShowTempFile == '1') && ($gSession['promopanelpreviewpath'] != ''))
        {
            // a temporary image file has been uploaded
            $previewPath = $gSession['promopanelpreviewpath'];
            $previewSize = filesize($previewPath);

            // read the image data
            $fp = fopen($previewPath, 'rb');
            if ($fp)
            {
                $image = fread($fp, $previewSize);
                fclose($fp);
            }
        }
		else
		{
			if (file_exists(UtilsObj::getPromoPanelFilePath($pGroupCode)) == True)
			{
				$url = UtilsObj::getPromoPanelOrgURL($pGroupCode);
			}
			else
			{
				$url = UtilsObj::correctPath($gSession['webbrandwebroot']) . 'images/admin/nopreview.gif';
			}
		}

        $resultArray['image'] = $image;
		$resultArray['url'] = $url;

        return $resultArray;
	}

	/**
	 * Save a promo panel image resource using the location stored in the gSession as the source
	 * @param string $pGroupCode The group code to save the promo panel against
	 * @return string result
	 */
	static function savePromoPanel($pGroupCode)
	{
		global $gSession;

		$result = '';
		$promoPanelTempPath = $gSession['promopanelpreviewpath'];
		$promoPanelPermanentPath = UtilsObj::getPromoPanelFilePath($pGroupCode);
		$promoPanelFolderPath = UtilsObj::getPromoPanelFolderPath($pGroupCode);

		// generate the folder structure
		$folderCreationResult = UtilsObj::createAllFolders($promoPanelFolderPath);

		if ($folderCreationResult == true)
		{
			// move the temporary image to the permanent location
			if (! rename($promoPanelTempPath, $promoPanelPermanentPath))
			{
				$result = 'str_ErrorUnableToWritePromoPanelMetadata';
			}
		}
		else
		{
			$result = 'str_ErrorUnableToWritePromoPanelMetadata';
		}

		return $result;
	}

	/**
	 * Delete the promo panel raw resource and metadata file for the specified license key
	 * @param string $pGroupCode The code for the license key to delete the promo panel for
	 * @return string result
	 */
	static function deletePromoPanel($pGroupCode)
	{
		$result = '';
		$promoPanelPermanentPath = UtilsObj::getPromoPanelFilePath($pGroupCode);
		$promoPanelMetaDataPath = UtilsObj::getPromoPanelMetadataPath($pGroupCode);

		// delete the image
		if (file_exists($promoPanelPermanentPath))
		{
			if (! @unlink($promoPanelPermanentPath))
			{
				$result = 'str_ErrorUnableToWritePromoPanelMetadata';
			}
		}


		// delete the metadata file
		if (file_exists($promoPanelMetaDataPath))
		{
			if (! @unlink($promoPanelMetaDataPath))
			{
				$result = 'str_ErrorUnableToWritePromoPanelMetadata';
			}
		}

		return $result;
	}

	/**
	 * Builds and writes a metadata file for the promo image for the desktop designer to download
	 * @param string $pGroupCode the license key code to generate the metadata file for
	 * @param int $pMode the override mode
	 * @param string $pStartDate the start date for the promo panel campaign
	 * @param string $pEndDate the end date for the promo panel campaign
	 * @param string $pURL the url for the promo panel for url mode overrides
	 * @param int $pDevicePixelRatio The device pixel ratio to store (hidpi toggle)
	 * @param int $pHeight the height of the promo area to display for the url mode override
	 * @return string result
	 */
	static function buildPromoPanelMetadataFile($pGroupCode, $pMode, $pStartDate, $pEndDate, $pURL, $pDevicePixelRatio, $pHeight)
	{
		$result = "";
		$folderCreationResult = false;
		$promoPanelFolderPath = UtilsObj::getPromoPanelFolderPath($pGroupCode);
		$promoPanelImagePath = UtilsObj::getPromoPanelFilePath($pGroupCode);
		$promoPanelMetaDataPath = UtilsObj::getPromoPanelMetadataPath($pGroupCode);
		$promoPanelMetaData = "";
		$promoPanelImageData = "";

		// generate the folder structure
		$folderCreationResult = UtilsObj::createAllFolders($promoPanelFolderPath);

		if ($folderCreationResult == true)
		{
			if ($pMode == TPX_DESKTOPDESIGNER_PROMOPANELOVERRIDEMODE_URL)
			{
				// build the data
				$promoPanelMetaData = json_encode(array(
					'mode' => TPX_DESKTOPDESIGNER_PROMOPANELOVERRIDEMODE_URL,
					'startdate' => $pStartDate,
					'enddate' => $pEndDate,
					'url' => $pURL,
					'height' => $pHeight)) . "\r\n" . 
					"<TPXEOH>\r\n";
			}
			elseif ($pMode == TPX_DESKTOPDESIGNER_PROMOPANELOVERRIDEMODE_IMAGE)
			{
				// read the image in for inclusion in the metadatafile
				$fp = fopen($promoPanelImagePath, 'rb');
				if ($fp)
				{
					$imageSize = filesize($promoPanelImagePath);
	
					$promoPanelImageData = fread($fp, $imageSize);
					fclose($fp);
				}
				else
				{
					$result = 'str_ErrorUnableToWritePromoPanelMetadata';
				}
	
				// build the data
				if ($promoPanelImageData != '')
				{
					$promoPanelMetaData = json_encode(array(
						'mode' => TPX_DESKTOPDESIGNER_PROMOPANELOVERRIDEMODE_IMAGE,
						'startdate' => $pStartDate,
						'enddate' => $pEndDate,
						'devicepixelratio' => $pDevicePixelRatio)) . "\r\n" .
						"<TPXEOH>\r\n" .
						$promoPanelImageData;
				}
				else
				{
					$result = 'str_ErrorUnableToWritePromoPanelMetadata';
				}
			}

			if ($promoPanelMetaData != '')
			{
				// write the metadata file
				$fp = fopen($promoPanelMetaDataPath, 'w');
				if ($fp)
				{
					fwrite($fp, $promoPanelMetaData);
				}
				else
				{
					$result = 'str_ErrorUnableToWritePromoPanelMetadata';
				}
			}
		}
		else
		{
			$result = 'str_ErrorUnableToWritePromoPanelMetadata';
		}
		
		return $result;
	}
}

/**
 * @} End of "addtogroup AutoUpdate".
 */
?>
