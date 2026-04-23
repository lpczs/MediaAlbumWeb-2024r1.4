<?php
use Security\CSPConfigBuilder;

require_once('../Utils/UtilsDatabase.php');

class AdminMetadataKeywords_model
{
	static $imageFolderName = 'keywords';

    static function getGridData()
	{
        global $gSession;

        $resultArray  = array();
        $summaryArray = array();
        $keywordItem = array();
        $typesArray = array();
		$paramArray = array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
		$smarty = SmartyObj::newSmarty('AdminMetadataKeywords');
    	if ($dbObj)
		{
			$stmt = $dbObj->prepare('SELECT id, ref, code, name, description, type, maxlength, height, width, flags FROM `KEYWORDS` ORDER BY code');

			if ($stmt)
			{
				$bindOK = DatabaseObj::bindParams($stmt, $typesArray, $paramArray);

				if ($bindOK)
				{
					if ($stmt->bind_result($id, $ref, $code, $name, $description, $type, $maxlength, $height, $width, $flags))
					{
						if ($stmt->execute())
						{
							while ($stmt->fetch())
							{
								$values = '';
								switch ($type)
								{
									case 'SINGLELINE':
									case 'MULTILINE':
										$flags = explode('<br>', $flags);
										$valuesArray = array();
										for ($i = 0; $i < count($flags); $i++)
										{
											if ($flags[$i] == 'U')
											{
												$valuesArray[] = 'Uppercase';
											}
											if ($flags[$i] == 'M')
											{
												$valuesArray[] = 'Required';
											}
										}
										$values = implode(", ", $valuesArray);

										break;

									default:
										$names = explode('<br>', $name);
										$flags = explode('<br>', $flags);

										// get keyword multilingual name
										if (count($names) > 0)
										{
											$name = $names[0];
											array_shift($names);
										}

										// get options values
										for ($i = 0; $i < count($names); $i++)
										{
											$names[$i] = LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $names[$i], 'black');
											if (isset($flags[$i]))
											{
												$flagsData = explode('<p>', $flags[$i]);
												$previewImagePath = '';

												if (isset($flagsData[1]))
												{
													$previewImagePath = str_replace('[WEBROOT]', UtilsObj::correctPath($gSession['webbrandweburl'], '/', false), $flagsData[1]);
												}

												$names[$i] = (isset($flagsData[0]) ? '<b>'.$flagsData[0] . '</b>' . '<div class="marginLeft10">' : '<div>') .
															 (($previewImagePath !== '') ? '<img class="previewImage" src="' . $previewImagePath . '" alt="">' . $flagsData[1] : '') .
															 $names[$i] . '</div>';
											}
										}
										$values = implode('<br>', $names);
								}

								$keywordItem['id'] = "'" . UtilsObj::ExtJSEscape($id) . "'";
								$keywordItem['ref'] = "'" . UtilsObj::ExtJSEscape($ref) . "'";
								$keywordItem['code'] = "'" . UtilsObj::ExtJSEscape($code) . "'";
								$keywordItem['name'] = "'" . UtilsObj::ExtJSEscape(LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $name, 'black')) . "'";
								$keywordItem['description'] = "'" . UtilsObj::ExtJSEscape(LocalizationObj::initAdminDisplayLocalizedNamesList($smarty, $description, 'black')) . "'";
								$keywordItem['type'] = "'" . UtilsObj::ExtJSEscape($type) . "'";
								$keywordItem['maxlength'] = "'" . UtilsObj::ExtJSEscape($maxlength) . "'";
								$keywordItem['height'] = "'" . UtilsObj::ExtJSEscape($height) . "'";
								$keywordItem['width'] = "'" . UtilsObj::ExtJSEscape($width) . "'";
								$keywordItem['values'] = "'" . UtilsObj::ExtJSEscape($values) . "'";
								array_push($resultArray, '['.join(',', $keywordItem).']');
							}
						}
					}

					$stmt->free_result();
					$stmt->close();
				}
			}

			$dbObj->close();
		}

		$summaryArray = join(',', $resultArray);
        if ($summaryArray != '')
        {
        	$summaryArray = ', ' . $summaryArray;
        }

        echo '[['. count($resultArray) .']'.$summaryArray.']';
        return;
    }


    static function addDisplay()
    {
    	$resultArray = array();

    	$resultArray['id'] = 0;
    	$resultArray['code'] = '';
    	$resultArray['name'] = '';
    	$resultArray['description'] = '';
    	$resultArray['type'] = 'SINGLELINE';
    	$resultArray['maxlength'] = 0;
    	$resultArray['height'] = 0;
    	$resultArray['width'] = 0;
    	$resultArray['values'] = '[]';
    	$resultArray['uppsercase'] = 0;
    	$resultArray['required'] = 0;
    	$resultArray['ref'] = 0;

    	return $resultArray;
    }


    static function editDisplay()
    {
    	$resultArray = array();
    	$id = $_GET['id'];
		$ref = 0;
		$code = '';
		$name = '';
		$description = '';
		$type = '';
		$maxlength = 0;
		$height = 0;
		$width = 0;
		$flags = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('SELECT ref, code, name, description, type, maxlength, height, width, flags FROM `KEYWORDS` WHERE id = ?'))
            {
            	if ($stmt->bind_param('i', $id))
                {
                	if ($stmt->execute())
                	{
        				if ($stmt->store_result())
        				{
							if ($stmt->num_rows > 0)
							{
								if ($stmt->bind_result($ref, $code, $name, $description, $type, $maxlength, $height, $width, $flags))
			                    {
			                    	if (!$stmt->fetch())
			                    	{
			                    		$result = 'str_DatabaseError';
			                        	$resultParam = 'editDisplay fetch ' . $dbObj->error;
			                    	}
			                    }
			                    else
			                    {
			                        $result = 'str_DatabaseError';
			                        $resultParam = 'editDisplay bind result ' . $dbObj->error;
			                    }
        					}
        				}
        				else
        				{
        					$result = 'str_DatabaseError';
		                    $resultParam = 'editDisplay store result ' . $dbObj->error;
        				}
                	}
                    else
                    {
                    	$result = 'str_DatabaseError';
                        $resultParam = 'editDisplay execute ' . $dbObj->error;
                    }
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'editDisplay bind params ' . $dbObj->error;
                }
                $stmt->free_result();
                $stmt->close();
       	   		$stmt = null;
            }
            else
            {
            	$result = 'str_DatabaseError';
                $resultParam = 'editDisplay prepare ' . $dbObj->error;
            }

            $uppsercase = 0;
			$valueRequired = 0;

            $itemsArray = array();

            switch ($type)
			{
				case 'SINGLELINE':
				case 'MULTILINE':
					$flags = explode('<br>', $flags);
					for ($i = 0; $i < count($flags); $i++)
					{
						if ($flags[$i] == 'U')
						{
							$uppsercase = 1;
						}
						if ($flags[$i] == 'M')
						{
							$valueRequired = 1;
						}
					}
					break;
				default:
					$names = explode('<br>', $name);
					$flags = explode('<br>', $flags);

					// get keyword multilingual name
					if (count($names) > 0)
					{
						$name = $names[0];
						array_shift($names);
					}

					// get options values
					for ($i = 0; $i < count($names); $i++)
					{
						$itemCode = "''";
						$itemPic = "''";
						$itemName = "'" . UtilsObj::ExtJSEscape($names[$i]) . "'";

						if (isset($flags[$i]))
						{
							$flagsData = explode('<p>', $flags[$i]);

							$itemCode = "'" . UtilsObj::ExtJSEscape($flagsData[0]) . "'";
							$itemPic = (isset($flagsData[1]) ? "'" . UtilsObj::ExtJSEscape($flagsData[1]) . "'" : "''");
						}
						array_push($itemsArray, '['.$itemCode . ',' . $itemName . ',' .$itemPic .']');
					}
			}
		}

		$resultArray['id'] = $id;
    	$resultArray['code'] = $code;
    	$resultArray['name'] = $name;
    	$resultArray['description'] = $description;
    	$resultArray['type'] = $type;
    	$resultArray['maxlength'] = $maxlength;
    	$resultArray['height'] = $height;
    	$resultArray['width'] = $width;
    	$resultArray['values'] = '['.join(',', $itemsArray).']';
    	$resultArray['ref'] = $ref;
    	$resultArray['uppsercase'] = $uppsercase;
    	$resultArray['required'] = $valueRequired;

    	return $resultArray;
    }


    static function keywordAdd()
    {
		global $gSession;

		$result = '';
		$resultParam = '';

		// Filter and sanitize input values before using them.
		$code = strtoupper($_POST['code']);
		$keywordType = filter_input(INPUT_POST, 'keywordType', FILTER_DEFAULT);
		$maxLength = filter_input(INPUT_POST, 'maxLength', FILTER_VALIDATE_INT);
		$height = filter_input(INPUT_POST, 'height', FILTER_VALIDATE_INT);
		$width = filter_input(INPUT_POST, 'width', FILTER_VALIDATE_INT);

		// name: en Popup Options<p>de Font Options in German<br>fr Le Item 1<p>en Item1<br>en Item 2<br>en Item 3<br>en Item 4
		// flags: ITEM1<p>image<br>ITEM2<p>image1<br>ITEM3<p>image2<br>ITEM4<p>image3

		// Process name and description values.
		$rawName = html_entity_decode(filter_input(INPUT_POST, 'name'), ENT_QUOTES);
		$name = strip_tags($rawName, '<br><p>');
		$rawDesc = html_entity_decode(filter_input(INPUT_POST, 'desc'), ENT_QUOTES);
		$desc = strip_tags($rawDesc, '<br><p>');

		// Process flag values
		$rawFlags = filter_input(INPUT_POST, 'flags');
		$flags = strip_tags($rawFlags, '<br><p>');

		// Images to remove, e.g. the remove or reset buttons were pressed.
		$imagesToRemove = filter_input(INPUT_POST, 'imagestoremove');

		$dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
			/* check if the keyword with this code already exists */
			if ($stmt = $dbObj->prepare('SELECT count(id) as num FROM `KEYWORDS` WHERE `code` = ?'))
            {
            	if ($stmt->bind_param('s', $code))
                {
                	if ($stmt->bind_result($recCount))
                    {
                    	if ($stmt->execute())
                    	{
                    		$stmt->fetch();
                    		if (intval($recCount) > 0)
                    		{
                    			$result = 'str_ErrorDuplicateCode';
                    		}
                    	}
                        else
                        {
                        	$result = 'str_DatabaseError';
                            $resultParam = 'keywordAdd execute ' . $dbObj->error;
                        }
                    }
                    else
                    {
                        $result = 'str_DatabaseError';
                        $resultParam = 'keywordAdd bind result ' . $dbObj->error;
                    }
                }
                else
                {
                    $result = 'str_DatabaseError';
                    $resultParam = 'keywordAdd bind params ' . $dbObj->error;
                }
                $stmt->close();
       	   		$stmt = null;
            }
            else
            {
            	$result = 'str_DatabaseError';
                $resultParam = 'keywordAdd prepare ' . $dbObj->error;
            }

			/* if code doesnt already exist then try to create a new field in Metadata table */
			if ($result == '')
			{
				$lastRef = -1;
				$lastRefQuery = "SELECT MAX(`ref`) FROM `keywords`";

				if ($stmt = $dbObj->prepare($lastRefQuery))
				{
					if ($stmt->execute())
					{
						if ($stmt->bind_result($lastRef))
						{
							$stmt->fetch();
						}
					}
				}
                $stmt->close();
       	   		$stmt = null;

				if ($lastRef != -1)
				{
					// Set the new ref to be one higher than the last ref.
					$newRef = ++$lastRef;

					if ($stmt = $dbObj->prepare('INSERT INTO `KEYWORDS` (`id`, `datecreated`, `ref`, `code`, `name`, `description`, `type`, `maxlength`, `height`, `width`, `flags`)
						VALUES (0, now(), ?, ?, ?, ?, ?, ?, ?, ?, ?)'))
					{
						if ($stmt->bind_param('issssiiis', $newRef, $code, $name, $desc, $keywordType, $maxLength, $height, $width, $flags))
						{
							if (!$stmt->execute())
							{
								// first check for a duplicate key (login name)
								if ($stmt->errno == 1062)
								{
									$result = 'str_ErrorDuplicateCode';
								}
								else
								{
									$result = 'str_DatabaseError';
									$resultParam = 'keywordAdd execute ' . $dbObj->error;
								}
							}
						}
						else
						{
							// could not bind parameters
							$result = 'str_DatabaseError';
							$resultParam = 'keywordAdd bind ' . $dbObj->error;
						}
						$stmt->close();
						$stmt = null;
					}
					else
					{
						// could not prepare statement
						$result = 'str_DatabaseError';
						$resultParam = 'keywordAdd prepare ' . $dbObj->error;
					}
				}
	       	 	else
	       	 	{
	       	 		$result = 'str_DatabaseError';
					$resultParam = 'Unable to get last keyword ref: ' . $dbObj->error;
	       	 	}
	   		}
		    $dbObj->close();
        }
        else
        {
            // could not open database connection
            $result = 'str_DatabaseError';
            $resultParam = 'keywordAdd connect ' . $dbObj->error;
        }

		if ($result == '')
		{
			if ($imagesToRemove !== '')
			{
				self::processImagesToDelete($imagesToRemove);
			}
		}

		if ($gSession['previewpath'] !== '')
		{
			// Reset the stored image path in the session.
			self::resetSessionImagePath();
		}

        if ($result == '')
		{
			// If we do not have an error and we are a radiogroup update csp with imagepaths that may have been supplied.
			if ($keywordType == 'RADIOGROUP')
			{
				self::addCSPKeywordImagePaths();
			}
			echo '{"success": true,	"msg":""}';
		}
		else
		{
			$smarty = SmartyObj::newSmarty('AdminMetadataKeywords');

			// Get error string to display.
			$errorString = $smarty->get_config_vars($result);

			// If the error is a database error replace ^0 with the resultParam message.
			if ($result == 'str_DatabaseError')
			{
				$errorString = str_replace('^0', $resultParam, $errorString);
			}

        	echo '{"success":false,	"msg":"' . $errorString . '"}';
        }
		return;
    }

    static function keywordEdit()
    {
		global $gSession;

		$result = '';
		$resultParam = '';

		// Filter and sanitize input values before using them.
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

		$keywordType = filter_input(INPUT_POST, 'keywordType', FILTER_DEFAULT);
		$maxLength = filter_input(INPUT_POST, 'maxLength', FILTER_VALIDATE_INT);
		$height = filter_input(INPUT_POST, 'height', FILTER_VALIDATE_INT);
		$width = filter_input(INPUT_POST, 'width', FILTER_VALIDATE_INT);

		// name: en Popup Options<p>de Font Options in German<br>fr Le Item 1<p>en Item1<br>en Item 2<br>en Item 3<br>en Item 4
		// flags: ITEM1<p>image<br>ITEM2<p>image1<br>ITEM3<p>image2<br>ITEM4<p>image3

		// Name and desc are localised strings and are split by <br> and <p> tags so allow these.
		$rawName = html_entity_decode(filter_input(INPUT_POST, 'name'), ENT_QUOTES);
		$name = strip_tags($rawName, '<br><p>');
		$rawDesc = html_entity_decode(filter_input(INPUT_POST, 'desc'), ENT_QUOTES);
		$desc = strip_tags($rawDesc, '<br><p>');

		// Flags are split by <br> and <p> tags
		$rawFlags = filter_input(INPUT_POST, 'flags');
		$flags = strip_tags($rawFlags, '<br><p>');

		// Old images to remove.
		$imagesToRemove = filter_input(INPUT_POST, 'imagestoremove');

		$dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
			// update keywords data
        	if ($stmt = $dbObj->prepare('UPDATE `KEYWORDS` SET `name` = ?, `description` = ?, `type` = ?, `maxlength` = ?, `height` = ?, `width` = ?, `flags` = ?
        		WHERE id = ?'))
			{
				if ($stmt->bind_param('sssiiisi', $name, $desc, $keywordType, $maxLength, $height, $width, $flags, $id))
				{
					if (!$stmt->execute())
					{

						$result = 'str_DatabaseError';
						$resultParam = 'keywordEdit execute ' . $dbObj->error;
					}
				}
				else
				{
					// could not bind parameters
					$result = 'str_DatabaseError';
					$resultParam = 'keywordEdit bind ' . $dbObj->error;
				}
				$stmt->close();
       	   		$stmt = null;
			}
			else
			{
				// could not prepare statement
				$result = 'str_DatabaseError';
				$resultParam = 'keywordEdit prepare ' . $dbObj->error;
			}

	   		$dbObj->close();
        }
        else
        {
            $result = 'str_DatabaseError';
            $resultParam = 'keywordEdit connect ' . $dbObj->error;
        }

		if ($result == '')
		{
			if ($imagesToRemove !== '')
			{
				self::processImagesToDelete($imagesToRemove);
			}

			if ($gSession['previewpath'] !== '')
			{
				self::resetSessionImagePath();
			}
		}

        if ($result == '')
		{
			// If we do not have an error and we are a radiogroup update csp with imagepaths that may have been supplied.
			if ($keywordType == 'RADIOGROUP')
			{
				self::addCSPKeywordImagePaths();
			}
			echo '{"success": true,	"msg":""}';
		}
		else
		{
			$smarty = SmartyObj::newSmarty('AdminMetadataKeywords');
        	echo '{"success":false,	"msg":"' . $smarty->get_config_vars($result) . '"}';
        }
		return;
    }

	/**
	 * Removes images from the filesystem.
	 *
	 * @param string $pImagesToRemove A string of images to be removed, split by <br> tag.
	 */
	public static function processImagesToDelete($pImagesToRemove)
	{
		global $ac_config;

		$imageFolder = '[WEBROOT]/' . self::$imageFolderName;

		// Remove previously uploaded images.
		array_map(function($pImage) use ($imageFolder, $ac_config)
		{
			// Only delete images on the filesystem.
			if (strpos($pImage, $imageFolder) === 0)
			{
				$imageToDeletePath = UtilsObj::correctPath(UtilsObj::getArrayParam($ac_config, 'CONTROLCENTREKEYWORDSIMAGEPATH'), DIRECTORY_SEPARATOR, false) . str_replace($imageFolder, '', $pImage);
				UtilsObj::deleteFile($imageToDeletePath);
			}
		}, explode('<br>', $pImagesToRemove));
	}

	/**
	 * Saves the image for metadata keywords.
	 *
	 * @param array $pFilesArray The $_FILES array for the uploaded image.
	 * @return array Result array containing the uploaded file path and error status, if any.
	 */
	public static function keywordUploadImage($pFilesArray)
	{
		global $gSession;
		global $ac_config;

		$resultKey = 'path';
		$resultArray = UtilsObj::getReturnArray($resultKey);
        $imageTempPath = UtilsObj::getArrayParam($pFilesArray, 'tmp_name');
        $imageType = UtilsObj::getArrayParam($pFilesArray, 'type');

        $validImageTypes = ['image/jpeg', 'image/pjpeg', 'image/gif', 'image/png', 'image/x-png'];

        // Make sure that the file is a valid type.
        if (in_array(strtolower($imageType), $validImageTypes))
        {
			$rootDir = UtilsObj::correctPath(UtilsObj::getArrayParam($ac_config, 'CONTROLCENTREKEYWORDSIMAGEPATH'), DIRECTORY_SEPARATOR, false);
			$retries = 5;
			while ($retries > 0)
			{
				$imageFileNameArray = str_split(md5(time()));

				// Replace certain keys with a slash to build the directory structure.
				$replacements = [0 => '/', 3 => '/', 6 => '/', 9 => '/', 12 => '/'];
				$imagePath = implode('', array_replace($imageFileNameArray, $replacements));
				$imagePath .= UtilsObj::getExtensionFromImageType($imageType);

				// Put image in temp location until the keyword is saved.
				$destinationFolder = $rootDir . $imagePath;

				// Test for collisions.
				if (file_exists($destinationFolder))
				{
					$retries--;
				}
				else
				{
					$retries = 0;
				}
			}

			$moveUploadedFileResult = UtilsObj::moveUploadedFile($imageTempPath, $destinationFolder);

			if ($moveUploadedFileResult !== '')
			{
				$resultArray['error'] = $moveUploadedFileResult['error'];
			}
			else
			{
				// Preview path is not empty and it's a metadata image preview path.
				if (($gSession['previewpath'] !== '') && (strpos($gSession['previewpath'], $rootDir) === 0))
				{
					// Remove the previous temp image.
					UtilsObj::deleteFile($gSession['previewpath']);
				}

				// Store the path in the session.
				$gSession['previewpath'] = $destinationFolder;

				// Return the URL to the image.
				$resultArray[$resultKey] = '[WEBROOT]/' . self::$imageFolderName . $imagePath;

				DatabaseObj::updateSession();
			}
        }
        else
        {
            $resultArray['error'] = 'str_ErrorUploadInvalidFileType';
        }

		return $resultArray;
	}

	/**
	 * Adds any image paths for RADIOGROUPS to the csp config file so we do not need to work this out all the time.
	 * We rebuild the whole list of urls as when removed we do not pass the ruls around so we may be able to remove urls that are no longer in the list.
	 *
	 * @return bool|int Number of bytes written to the csp config file or false on error.
	 */
	public static function addCSPKeywordImagePaths()
	{
		$imagePaths = [];
		$flags = '';
		$returnInfo = false;

		$cspConfigBuilder = new CSPConfigBuilder();
		$dbObject = DatabaseObj::getGlobalDBConnection();

		if ($dbObject)
		{
			$query = "SELECT `flags` FROM `keywords` WHERE `type`='RADIOGROUP' AND `flags` LIKE '%<p>%'";

			$stmt = $dbObject->prepare($query);

			if ($stmt)
			{
				if ($stmt->execute())
				{
					if ($stmt->store_result())
					{
						if ($stmt->num_rows > 0)
						{
							if ($stmt->bind_result($flags))
							{
								while ($stmt->fetch())
								{
									$options = explode('<br>', $flags);

									foreach ($options as $key => $option)
									{
										$optionInfo = explode('<p>', $option);

										if ((isset($optionInfo[1])) && (trim($optionInfo[1]) != '') && (strpos($optionInfo[1], '[WEBROOT]') === false))
										{
											$url = $cspConfigBuilder->parseUrl(trim($optionInfo[1]));

											if (($url != '') && (! in_array($url, $imagePaths)))
											{
												$imagePaths[] = $url;
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		// If we have some image paths update the csp details.
		if (! empty($imagePaths))
		{
			$cspConfig = [
				'ALL' => [
					'urls' => $imagePaths
				]
			];

			$returnInfo = $cspConfigBuilder->buildCSPConfig($cspConfig);
		}

		return $returnInfo;
	}

	/**
	 * Resets the previewpath session value.
	 */
	static function resetSessionImagePath()
	{
		global $gSession;

		if ($gSession['previewpath'] !== '')
		{
			$gSession['previewpath'] = '';
			DatabaseObj::updateSession();
		}
	}
}
?>
