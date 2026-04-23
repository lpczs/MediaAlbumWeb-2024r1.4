<?php

function metaDataKeywordListSort($a, $b)
{
    // custom sort function to sort the keywords into the correct order
    // this has to sit outside the object

	if ($a['sortorder'] < $b['sortorder'])
	{
		return -1;
	}
	else if ($a['sortorder'] == $b['sortorder'])
	{
		return 0;
	}
	else
	{
		return 1;
	}
}

class MetaDataObj
{
    static function getKeywordList($pSection, $pGroupCode, $pProductCodes, $pKeywordGroupId)
    {
		// return an array containing the keywords based on the specified parameters
    	$resultArray = Array();

        $keywordGroupIDArray = Array();
        $productCode = '';

        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
        	// if it's the order matadata then get the list of keyword header ids. we dont need this for component metadata as
        	// only one keyword group can be assigned to a product
        	if ($pSection == 'ORDER')
        	{
	        	if ($stmt = $dbObj->prepare('SELECT `id`, `groupcode`, `productcodes` FROM `KEYWORDGROUPHEADER` WHERE (`section` = ?) AND ((`groupcode` = "") OR (`groupcode` = ?)) ORDER BY `groupcode` DESC'))
	        	{
	        		if ($stmt->bind_param('ss', $pSection, $pGroupCode))
	                {
	                	if ($stmt->bind_result($id, $groupCode, $productCodes))
	                	{
	                		if ($stmt->execute())
							{
	        					// process each item
								while ($stmt->fetch())
								{
									for ($i = 0; $i < count($pProductCodes); $i++)
									{
										$productCode = $pProductCodes[$i];
										if (($productCodes != '') && ($productCodes != '**ALL**'))
										{
											$productCodeList = explode(',', $productCodes);
											if (in_array($productCode, $productCodeList) == true)
											{
												// the product code matches so we have found the keyword group
												array_push($keywordGroupIDArray, $id);
												break;
											}
										}
										else
										{
											// the keywords belong to all products
											array_push($keywordGroupIDArray, $id);
											break;
										}

									}

								}
	        				}
							else
							{
								// could not execute statement
								$result = 'str_DatabaseError';
								$resultParam = 'getKeywordList execute ' . $dbObj->error;
							}

	                	}
	                	else
	                	{
	                		// could not bind result
							$result = 'str_DatabaseError';
							$resultParam = 'getKeywordList bind result ' . $dbObj->error;
	                	}
	                }
	                else
					{
						// could not bind result
						$result = 'str_DatabaseError';
						$resultParam = 'getKeywordList bind param ' . $dbObj->error;
					}

	                $stmt->free_result();
	                $stmt->close();
	            }
	            else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = 'getKeywordList prepare ' . $dbObj->error;
				}
	        	$stmt = null;
        	}
        	else
        	{
        		// if it's a component matadata then just use group header id
        		$keywordGroupIDArray[] = $pKeywordGroupId;
        	}

        	// if we have found a keyword group we must now get the keywords in the group
        	$itemCount = count($keywordGroupIDArray);
        	$keywordCodesArray = array();
        	if ($itemCount > 0)
        	{
        		if ($stmt = $dbObj->prepare('SELECT KEYWORDS.ref, KEYWORDS.code, KEYWORDS.name, KEYWORDS.description, KEYWORDS.type, KEYWORDS.maxlength, KEYWORDS.height, KEYWORDS.width, KEYWORDS.flags,
        			KEYWORDGROUP.sortorder, KEYWORDGROUP.defaultvalue
        			FROM `KEYWORDS`
        			JOIN `KEYWORDGROUP` ON KEYWORDS.code = KEYWORDGROUP.keywordcode
        			WHERE `keywordgroupheaderID` = ? ORDER BY `sortorder`'))
        		{
        			for ($i = 0; $i < $itemCount; $i++)
        			{
						if ($stmt->bind_param('i', $keywordGroupIDArray[$i]))
						{
							if ($stmt->bind_result($ref, $code, $name, $description, $type, $maxLength, $height, $width, $flags, $sortOrder, $defaultValue))
							{
								if ($stmt->execute())
								{
									// process each item
									while ($stmt->fetch())
									{
										// make sure keywords are unique per order/component
										if (!in_array($code, $keywordCodesArray))
										{
											$keywordItem['ref'] = $ref;
											$keywordItem['code'] = $code;
											$keywordItem['name'] = $name;
											$keywordItem['description'] = $description;
											$keywordItem['type'] = $type;
											$keywordItem['maxlength'] = $maxLength;
											$keywordItem['height'] = $height;
											$keywordItem['width'] = $width;
											$keywordItem['flags'] = $flags;
											$keywordItem['sortorder'] = $sortOrder;
											$keywordItem['defaultvalue'] = $defaultValue;

											array_push($resultArray, $keywordItem);

											$keywordCodesArray[] = $code;
										}
									}
								}
								else
								{
									// could not execute statement
									$result = 'str_DatabaseError';
									$resultParam = 'getKeywordList execute2 ' . $dbObj->error;
								}

							}
							else
							{
								// could not bind result
								$result = 'str_DatabaseError';
								$resultParam = 'getKeywordList bind result2 ' . $dbObj->error;
							}
							$stmt->free_result();
						}
						else
						{
							// could not bind param
							$result = 'str_DatabaseError';
							$resultParam = 'getKeywordList bind param2 ' . $dbObj->error;
						}
					}

					$stmt->free_result();
					$stmt->close();
        		}
				else
				{
					// could not prepare statement
					$result = 'str_DatabaseError';
					$resultParam = 'getKeywordList prepare2 ' . $dbObj->error;
				}

            }
            $dbObj->close();
        }
        else
		{
			// could not open database connection
			$result = 'str_DatabaseError';
			$resultParam = 'getKeywordList connect ' . $dbObj->error;
		}

        usort($resultArray, 'metaDataKeywordListSort');

        return $resultArray;
	}


	static function buildKeywordHTML($pSection, $pSubSection, $pMetaDataArray, $pLocale, $orderLineId, $pIsReadOnly)
	{
		// build the html required to handle the keywords
		global $gSession;
		global $ac_config;

		$resultArray = Array();

		$layoutHTML = '';
		$submitHTML = '';
		$submitJavaScript = '';
		$isUpperCase = false;
		$isManditory = false;
        $isOneKeywordMandatory = false;

        $smarty = SmartyObj::newSmarty('Order', $gSession['webbrandcode'], $gSession['webbrandapplicationname']);

        if (($gSession['ismobile'] == true) && ($pSubSection == 'PAYMENT') && ($pSection != 'ORDER'))
        {
            $count = count($pMetaDataArray);
            if ($count > 0)
            {
                $layoutHTML .= '<ul class="componentList">';

                for ($i = 0; $i < $count; $i++)
                {
                    if (($pMetaDataArray[$i]['type'] == 'RADIOGROUP') || ($pMetaDataArray[$i]['type'] == 'POPUP'))
                    {
                        $labelArray = explode('<br>', $pMetaDataArray[$i]['name']);
                        $labelCodeArray = explode('<br>', $pMetaDataArray[$i]['flags']);
                    }
                    else
                    {
                        $labelName = LocalizationObj::getLocaleString($pMetaDataArray[$i]['name'], $pLocale, true);
                    }

                    switch ($pMetaDataArray[$i]['type'])
                    {
                        case 'SINGLELINE':
                            if ($pMetaDataArray[$i]['defaultvalue'] != '')
                            {
                                $layoutHTML .= '<li>' . $labelName . ': ' . UtilsObj::encodeString($pMetaDataArray[$i]['defaultvalue']) . '</li>';
                            }
                            break;
                        case 'MULTILINE':
                            if ($pMetaDataArray[$i]['defaultvalue'] != '')
                            {
                                $layoutHTML .= '<li>' . $labelName . ': ' . nl2br($pMetaDataArray[$i]['defaultvalue']) . '</li>';
                            }
                            break;
                        case 'CHECKBOX':
                            if ($pMetaDataArray[$i]['defaultvalue'] == "1")
                            {
                                $layoutHTML .= '<li>' . $labelName . '</li>';
                            }
                            break;
                        case 'RADIOGROUP':

                            $itemCount = count($labelCodeArray);
                            if ($itemCount > 0)
                            {
                                $labelName = LocalizationObj::getLocaleString($labelArray[0], $pLocale, true);

                                // determine the item to select as the default
                                $labelCodeItemArray = explode('<p>', $labelCodeArray[0]);
                                $defaultValue = '';

                                if (!empty($pMetaDataArray))
                                {
                                    for ($j = 0; $j < $itemCount; $j++)
                                    {
                                        $labelCodeItemArray = explode('<p>', $labelCodeArray[$j]);

                                        if ($labelCodeItemArray[0] == $pMetaDataArray[$i]['defaultvalue'])
                                        {
                                            $defaultValue = $pMetaDataArray[$i]['defaultvalue'];
                                            break;
                                        }
                                    }
                                }

                                for ($j = 0; $j < $itemCount; $j++)
                                {
                                    $labelCodeItemArray = explode('<p>', $labelCodeArray[$j]);
                                    $labelCode = $labelCodeItemArray[0];

                                    if ($labelCode != '')
                                    {

                                        if ($labelCode == $defaultValue)
                                        {
                                            $layoutHTML .= '<li>' . $labelName . ': ' . LocalizationObj::getLocaleString($labelArray[$j + 1], $pLocale, true) . '</li>';
                                        }
                                    }
                                }
                            }
                            break;
                        case 'POPUP':
                            $itemCount = count($labelCodeArray);
                            if ($itemCount > 0)
                            {
                                $labelName = LocalizationObj::getLocaleString($labelArray[0], $pLocale, true);

                                // determine the item to select as the default
                                $defaultValue = '';
                                if (!empty($pMetaDataArray))
                                {
                                    for ($j = 0; $j < $itemCount; $j++)
                                    {
                                        if ($labelCodeArray[$j] == $pMetaDataArray[$i]['defaultvalue'])
                                        {
                                            $defaultValue = $pMetaDataArray[$i]['defaultvalue'];
                                            break;
                                        }
                                    }
                                }

                                for ($j = 0; $j < $itemCount; $j++)
                                {
                                    $labelCode = $labelCodeArray[$j];
                                    if ($labelCode != '')
                                    {
                                        if ($labelCode == $defaultValue)
                                        {
                                             $layoutHTML .= '<li>' . $labelName . ': ' . LocalizationObj::getLocaleString($labelArray[$j + 1], $pLocale, true) . '</li>';
                                        }
                                    }
                                }
                            }
                            break;
                    }

                }

                $layoutHTML .= '</ul>';
            }
        }
        else
        {
			if ((($pSection == 'ORDER') && ($pSubSection == 'PAYMENT')) || ($pSection == 'COMPONENT'))
            {
				$brandingFolder = UtilsObj::getArrayParam($ac_config, 'WEBBRANDFOLDERNAME', 'Branding');

                $count = count($pMetaDataArray);
                if ($count > 0)
                {
                    $layoutHTML .= '<div class="legendMeta">
                                        <span>' . $smarty->get_config_vars('str_LabelMetadataLegend') . '</span>
                                    </div>
                                    <div class="metadataHolder">';

                    for ($i = 0; $i < $count; $i++)
                    {
                        $disabled = '';
                        $onblur = '';
                        $dataDecorator = '';

                        if (($pMetaDataArray[$i]['type'] == 'RADIOGROUP') || ($pMetaDataArray[$i]['type'] == 'POPUP'))
                        {
                            $labelArray = explode('<br>', $pMetaDataArray[$i]['name']);
                            $labelCodeArray = explode('<br>', $pMetaDataArray[$i]['flags']);
							$isOneKeywordMandatory = true;
                        }
                        else
                        {
                            $labelName = LocalizationObj::getLocaleString($pMetaDataArray[$i]['name'], $pLocale, true);

                            $flags = $pMetaDataArray[$i]['flags'];

                            if (strpos($flags, 'U') === false)
                            {
                                $isUpperCase = false;
                            }
                            else
                            {
                                $isUpperCase = true;
                            }

                            if (strpos($flags, 'M') === false)
                            {
                                $isManditory = false;
                            }
                            else
                            {
                                $isManditory = true;
                                $isOneKeywordMandatory = true;
                            }
                        }
                        $labelDescription = LocalizationObj::getLocaleString($pMetaDataArray[$i]['description'], $pLocale, true);

                        $keywordRef = 'keyword' . $pMetaDataArray[$i]['ref'];
                        if ($pSection == 'COMPONENT')
                        {
                            $keywordRef = $keywordRef . '_' . $orderLineId;

                            if ($pSubSection == 'PAYMENT')
                            {
                                $pIsReadOnly = true;
                            }
                        }

                        if ($isUpperCase == true)
                        {
                            $dataDecorator = "fnForceUpperAlphaNumericMetaData";
                        }

                        switch ($pMetaDataArray[$i]['type'])
                        {
                            case 'SINGLELINE':
                            case 'MULTILINE':

                                $dataDecorator = '';
                                $dataTrigger = '';
                                $dataParams = '';
                            
                                if ($gSession['ismobile'] == true)
                                {
                                    if ($pSubSection == 'QTY')
                                    {
                                        $onblur .= 'onblur="checkMetadataComponent(this);"';
                                        $dataDecorator = 'data-decorator="fnCheckMetadataComponent';
                                    }
                                    else
                                    {
                                        $onblur .= 'onblur="checkMetadataValidity(\'contentPanelPayment\', false);"';
                                        $dataDecorator = 'data-decorator="fnCheckMetadataValidity';
                                        $dataParams = 'data-divid="contentPanelPayment" data-displaymessage="false"';
                                    }

                                    $dataTrigger = 'data-trigger="blur"';
                                }

                                $layoutHTML .= '<div class="metadataItem innerBox innerBoxNoMarginTop innerBoxPadding" id="metadataItem' . $keywordRef .'">';
                                $layoutHTML .= '<div class="metadatatitle">
                                                    <label for="' . $keywordRef . '_' . $i .'">' . $labelName . '</label>
                                                </div>';
                                $layoutHTML .= '<div class="metadatacontent">';
                                if ($labelDescription !='')
                                {
                                    $mandatoryImg = '';
                                    if ($isManditory)
                                    {
									   if($gSession['webbrandcode'] == '')
									   {
										   $asteriks = UtilsObj::correctPath($gSession['webbrandwebroot']) . 'images/asterisk.png';
									   }
									   else
									   {
										   $asteriks = '../../' . $brandingFolder. '/' . $gSession['webbrandname'] . '/images/asterisk.png';
									   }
									   
										$mandatoryImg = '<img class="valueRequiredImg" src=" ' . $asteriks . '" alt="*" />';
                                    }
                                    $layoutHTML .= '<div class="description">' . $labelDescription . $mandatoryImg . '</div>';
                                }

                                $layoutHTML .= '<div class="formLine2">';

                                $additionalClasses = '';
                                if ($isManditory)
                                {
                                    $additionalClasses = ' required';
                                }

                                if ($isUpperCase)
                                {
                                    $additionalClasses .= ' valueUppercaseMetadata';
                                }

                                if ($pMetaDataArray[$i]['type'] == 'SINGLELINE')
                                {
                                    if ($pIsReadOnly)
                                    {
                                        $disabled = 'disabled="disabled"';
                                    }

                                    if ($pMetaDataArray[$i]['width'] == 0)
                                    {
                                        $cols = '660';// '800';
                                    }
                                    else
                                    {
                                        $cols = $pMetaDataArray[$i]['width'];
                                    }

                                    $layoutHTML .= '<input type="text" id="' . $keywordRef . '_' . $i .'" style="width: '.$cols.'px;" name="' . $keywordRef . '"' . $disabled . ' class="inputFullSize' . $additionalClasses . '" maxlength="' .
                                    $pMetaDataArray[$i]['maxlength'] . '" value="' . UtilsObj::encodeString($pMetaDataArray[$i]['defaultvalue']) . '" ' . $dataDecorator . ' ' . $dataParams . ' ' . $dataTrigger . ' />';
                                }
                                else // multine input
                                {
                                    if ($pIsReadOnly)
                                    {
                                        $disabled = 'disabled="disabled"';
                                    }

                                    if ($pMetaDataArray[$i]['width'] == 0)
                                    {
										// default width of the input text, changed from 800
                                        $cols = '724';
                                    }
                                    else
                                    {
                                        $cols = $pMetaDataArray[$i]['width'];
                                    }


                                    $layoutHTML .= '<textarea id="' . $keywordRef . '_' . $i .'" name="' . $keywordRef . '"' . $disabled . ' class="inputFullSize' . $additionalClasses . '" rows="' . $pMetaDataArray[$i]['height'] .
                                        '" cols="' . $cols . '" maxlength="' . $pMetaDataArray[$i]['maxlength'] . '" ' . $dataDecorator . ' ' . $dataParams . ' ' . $dataTrigger . '>' .
                                        str_replace("\n", '<nl>', $pMetaDataArray[$i]['defaultvalue']) . '</textarea>';
                                }

                                $layoutHTML .= '</div>';
                                $layoutHTML .= '<div class="clear"></div>';
                                $layoutHTML .= '</div>';
                                $layoutHTML .= '</div>';

                                $submitJavaScript .= 'document.submitform.' . $keywordRef . '.value = document.orderform.' . $keywordRef . '.value; ';
                                break;
                            case 'CHECKBOX':
                                $layoutHTML .= '<div class="metadataItem innerBox innerBoxNoMarginTop innerBoxPadding" id="metadataItem' . $keywordRef .'">';

                                if ($pIsReadOnly)
                                {
                                    $disabled = 'disabled="disabled"';
                                }

                                if ($pMetaDataArray[$i]['defaultvalue'] == "1")
                                {
                                    $checked = 'checked="checked"';
                                }
                                else
                                {
                                    $checked = '';
                                }

                                $layoutHTML .= '<div class="metadatatitlecheckbox">';
                                $layoutHTML .= '<input type="checkbox" id="' . $keywordRef . '_' . $i .'" name="' . $keywordRef . '" class="text inputCheckbox" ' . $checked . ' ' . $disabled .' />';
                                $layoutHTML .= '<label class="metadataCheckboxLabel" for="' . $keywordRef . '_' . $i .'">' . $labelName . '</label>';
                                $layoutHTML .= '<div class="clear"></div>';
                                $submitJavaScript .= 'document.submitform.' . $keywordRef . '.value = (document.orderform.' . $keywordRef . '.checked) ? 1:0; ';
                                $layoutHTML .= '</div>';
                                $layoutHTML .= '<div class="metadatacontent">';

                                if ($labelDescription !='')
                                {
                                    $layoutHTML .= '<div class="description">' . $labelDescription . '</div>';
                                }

                                $layoutHTML .= '</div>';
                                $layoutHTML .= '<div class="clear"></div>';
                                $layoutHTML .= '</div>';
                                break;
                            case 'RADIOGROUP':
                                $itemCount = count($labelCodeArray);
                                if ($itemCount > 0)
                                {
                                    $layoutHTML .= '<div class="metadataItem innerBox innerBoxNoMarginTop metadataItemRadio" id="metadataItem' . $keywordRef . '">';
									
									if($gSession['webbrandcode'] == '')
									{
										$asteriks = UtilsObj::correctPath($gSession['webbrandwebroot']) . 'images/asterisk.png';
									}
									else
									{
										$asteriks = '../../' . $brandingFolder. '/' . $gSession['webbrandname'] . '/images/asterisk.png';
									}

                                    $labelName = LocalizationObj::getLocaleString($labelArray[0], $pLocale, true);
                                    $layoutHTML .= '<div class="metadatatitleRadio outerBoxPadding">
                                                        ' . $labelName . '<img src="' . $asteriks . '" alt="*" />
                                                    </div>';
                                    $layoutHTML .= '<div class="metadatacontent">';
                                    if ($labelDescription !='')
                                    {
                                        $layoutHTML .= '<div class="descriptionRadio outerBoxPadding">' . $labelDescription . '</div>';
                                    }

                                    // determine the item to select as the default
                                    $labelCodeItemArray = explode('<p>', $labelCodeArray[0]);
                                    $defaultValue = '';

                                    if (!empty($pMetaDataArray))
                                    {
                                        for ($j = 0; $j < $itemCount; $j++)
                                        {
                                            $labelCodeItemArray = explode('<p>', $labelCodeArray[$j]);

                                            if ($labelCodeItemArray[0] == $pMetaDataArray[$i]['defaultvalue'])
                                            {
                                                $defaultValue = $pMetaDataArray[$i]['defaultvalue'];
                                                break;
                                            }
                                        }
                                    }

                                    for ($j = 0; $j < $itemCount; $j++)
                                    {
                                        $labelCodeItemArray = explode('<p>', $labelCodeArray[$j]);
                                        $labelCode = $labelCodeItemArray[0];
                                        $labelPictureHTML = '';
                                        $labelPictureURL = '';

                                        if ($labelCode != '')
                                        {
                                            if (count($labelCodeItemArray) > 1)
                                            {
                                                $labelPicture = $labelCodeItemArray[1];
                                                if ((substr($labelPicture, 0, 7) == 'http://') || (substr($labelPicture, 0, 8) == 'https://'))
                                                {
                                                    $labelPictureURL = $labelPicture;
                                                }
                                                else
                                                {
                                                    $labelPictureURL = str_replace('[WEBROOT]', UtilsObj::getBrandedWebUrl(), $labelPicture);
                                                }

                                                if ($labelPictureURL != '')
                                                {
                                                    $labelPictureHTML = '<img class="radioImage" src="' . $labelPictureURL . '" alt="" />';
                                                }
                                            }

                                            if ($pIsReadOnly)
                                            {
                                                $disabled = 'disabled="disabled"';
                                            }

                                            if ($labelCode == $defaultValue)
                                            {
                                                $checked = ' checked="checked"';
                                                $classSelected = 'optionSelected';
                                            }
                                            else
                                            {
                                                $checked = '';
                                                $classSelected = '';
                                            }

                                            if ($gSession['ismobile'] == true)
                                            {
                                                $dataDivID = ($pSubSection == 'QTY') ? '' : 'contentPanelPayment';

                                                $layoutHTML .= '<div class="radioBloc outerBoxPadding ' . $classSelected . '">';
                                                $layoutHTML .= '<div class="checkboxImage"></div>';
                                                $layoutHTML .= '<input type="radio" id="' . $keywordRef . '_' . $i .'_' . $j .'" name="' . $keywordRef . '" value="' . $labelCode . '"' . $checked . ' style="display:none;" data-decorator="fnMetadataMethodClick" data-divid="' . $dataDivID . '" />';

                                                if ($labelPictureHTML != '')
                                                {
                                                    $layoutHTML .= '<label for="' . $keywordRef . '_' . $i .'_' . $j .'" class="labelRadio listLabel">' . $labelPictureHTML . '<span>' . LocalizationObj::getLocaleString($labelArray[$j + 1], $pLocale, true) . '</span></label>';
                                                }
                                                else
                                                {
                                                    $layoutHTML .= '<label for="' . $keywordRef . '_' . $i .'_' . $j .'" class="labelRadio listLabel"><span>' . LocalizationObj::getLocaleString($labelArray[$j + 1], $pLocale, true) . '</span></label>';
                                                }

                                            }
                                            else
                                            {
                                                $layoutHTML .= '<div class="radioBloc">';
                                                $layoutHTML .= '<input type="radio" id="' . $keywordRef . '_' . $i .'_' . $j .'" name="' . $keywordRef . '"' . $disabled . ' value="' . $labelCode . '"' . $checked . ' />';

                                                if ($labelPictureHTML != '')
                                                {
                                                    $layoutHTML .=  '<label for="' . $keywordRef . '_' . $i .'_' . $j .'" class="labelRadio listLabel">' . $labelPictureHTML . LocalizationObj::getLocaleString($labelArray[$j + 1], $pLocale, true) . '</label>';
                                                }
                                                else
                                                {
                                                    $layoutHTML .= '<label for="' . $keywordRef . '_' . $i .'_' . $j .'" class="labelRadio listLabel">' . LocalizationObj::getLocaleString($labelArray[$j + 1], $pLocale, true) . '</label>';
                                                }
                                            }


                                            $layoutHTML .= '<div class="clear"></div>';
                                            $layoutHTML .= '</div>';
                                        }
                                    }

                                    $submitJavaScript .= 'var radioCode = "";';
                                    $submitJavaScript .= 'var radioObj = document.orderform.' . $keywordRef . ';';
                                    $submitJavaScript .= 'var radioLength = radioObj.length;';
                                    $submitJavaScript .= 'if (radioLength == undefined)';
                                    $submitJavaScript .= '{';
                                    $submitJavaScript .= 'if (radioObj.checked)';
                                    $submitJavaScript .= '{';
                                    $submitJavaScript .= 'radioCode = radioObj.value;';
                                    $submitJavaScript .= '}';
                                    $submitJavaScript .= '}';
                                    $submitJavaScript .= 'else';
                                    $submitJavaScript .= '{';
                                    $submitJavaScript .= 'for (i = 0; i < radioLength; i++)';
                                    $submitJavaScript .= '{';
                                    $submitJavaScript .= 'if (radioObj[i].checked)';
                                    $submitJavaScript .= '{';
                                    $submitJavaScript .= 'radioCode = radioObj[i].value;';
                                    $submitJavaScript .= 'break;';
                                    $submitJavaScript .= '}';
                                    $submitJavaScript .= '}';
                                    $submitJavaScript .= '}';
                                    $submitJavaScript .= 'document.submitform.' . $keywordRef . '.value = radioCode;';
                                    $layoutHTML .= '</div>';
                                    $layoutHTML .= '</div>';
                                }
                                break;
                            case 'POPUP':
                                $itemCount = count($labelCodeArray);
                                if ($itemCount > 0)
                                {
									if($gSession['webbrandcode'] == '')
									{
										$asteriks = UtilsObj::correctPath($gSession['webbrandwebroot']) . 'images/asterisk.png';
									}
									else
									{
										$asteriks = '../../' . $brandingFolder . '/' . $gSession['webbrandname'] . '/images/asterisk.png';
									}
									
									$layoutHTML .= '<div class="metadataItem innerBox innerBoxNoMarginTop innerBoxPadding" id="metadataItem' . $keywordRef .'">';
                                    $labelName = LocalizationObj::getLocaleString($labelArray[0], $pLocale, true);
                                    $layoutHTML .= '<div class="metadatatitle">
                                                        ' . $labelName . '<img src="' . $asteriks . '" alt="*" />
                                                    </div>';
                                    $layoutHTML .= '<div class="metadatacontent">';
                                    if ($labelDescription !='')
                                    {
                                        $layoutHTML .= '<div class="description">' . $labelDescription . '</div>';
                                    }

                                    // determine the item to select as the default
                                    $defaultValue = '';
                                    if (!empty($pMetaDataArray))
                                    {
                                        for ($j = 0; $j < $itemCount; $j++)
                                        {
                                            if ($labelCodeArray[$j] == $pMetaDataArray[$i]['defaultvalue'])
                                            {
                                                $defaultValue = $pMetaDataArray[$i]['defaultvalue'];
                                                break;
                                            }
                                        }
                                    }

                                    if ($pIsReadOnly)
                                    {
                                        $disabled = 'disabled="disabled"';
                                    }

                                    $dataDecorator = '';
                                    $dataTrigger = '';
                                    $dataParams = '';

                                    $onChange = '';
                                    if ($gSession['ismobile'] == true)
                                    {
                                        if ($pSubSection == 'QTY')
                                        {
                                            $onChange = 'onchange="checkMetadataComponent(this);"';
                                            $dataDecorator = 'data-decorator="fnCheckMetadataComponent"';
                                        }
                                        else
                                        {
                                            $onChange = 'onchange="checkMetadataValidity(\'contentPanelPayment\', false);"';
                                            $dataDecorator = 'data-decorator="fnCheckMetadataValidity"';
                                            $dataParams = 'data-divid="contentPanelPayment" data-displaymessage="false"';
                                        }

                                        $dataTrigger = 'data-trigger="change"';
                                    }

                                    $layoutHTML .= '<div class="wizard-dropdown"> <select id="' . $keywordRef . '_' . $i .'" name="' . $keywordRef . '"' . $disabled . ' class="text wizard-dropdown" ' . $dataDecorator . ' ' . $dataTrigger . ' ' . $dataParams . '>';

                                    if ($defaultValue=='')
                                    {
                                        $checked = ' selected="selected"';
                                    }
                                    else
                                    {
                                        $checked = '';
                                    }

                                    $layoutHTML .= '<option '.$checked.' value=""></option>';

                                    for ($j = 0; $j < $itemCount; $j++)
                                    {
                                        $labelCode = $labelCodeArray[$j];
                                        if ($labelCode != '')
                                        {
                                            if ($labelCode == $defaultValue)
                                            {
                                                $checked = ' selected="selected"';
                                            }
                                            else
                                            {
                                                $checked = '';
                                            }

                                            $layoutHTML .= '<option ' . $checked . ' value="' . $labelCode . '">';
                                            $layoutHTML .= LocalizationObj::getLocaleString($labelArray[$j + 1], $pLocale, true);
                                            $layoutHTML .= '</option>';
                                        }
                                    }
                                    $layoutHTML .= '</select></div>';

                                    $submitJavaScript .= 'document.submitform.' . $keywordRef . '.value = document.orderform.' . $keywordRef . '.options[document.orderform.' . $keywordRef . '.selectedIndex].value;';
                                    $layoutHTML .= '</div>';
                                    $layoutHTML .= '</div>';
                                }
                                break;
                        }

                        $submitHTML .= '<input type="hidden" name="' . $keywordRef . '" value=""/>';
                    }
                    $layoutHTML .= '</div>';
                    $layoutHTML .= '<div class="legendMeta">&nbsp;</div>';
				}
			}
        }

        if ($pSection == 'ORDER')
        {
        	$resultArray['layouthtml'] = $layoutHTML;
	        $resultArray['submitform'] = $submitHTML;
	        $resultArray['submitjavascript'] = $submitJavaScript;
        }
        else
        {
            $resultArray['metadatahtml'] = $layoutHTML;
            $resultArray['isonekeywordmandatory'] = $isOneKeywordMandatory;
        }
        return $resultArray;
	}

	static function storeHTMLKeywords($pMetaDataArray, $pMetadataType = 'ORDER')
	{
		global $gSession;

		/* if no order line provided then it's a metadata for the order */
		if ($pMetadataType == 'ORDER')
		{
			$count = count($pMetaDataArray);
			for ($i = 0; $i < $count; $i++)
			{
				$keywordRef = 'keyword' . $pMetaDataArray[$i]['ref'];
				$pMetaDataArray[$i]['defaultvalue'] = UtilsObj::getPOSTParam($keywordRef);
			}
			return $pMetaDataArray;
		}
		else
		{
			foreach ($_POST as $key => $value)
        	{
        		if (strpos($key, 'keyword') !== false)
				{
					$sectionOrderLineIdArray = explode ('_', $key);
					$keywordId = array_shift($sectionOrderLineIdArray);
					$sectionOrderLineId = implode('_', $sectionOrderLineIdArray);

					// we need to check to see if sectionOrderLineId contains a -1_
					// if it does then we know we are processing the orderfooter
					if (substr($sectionOrderLineId, 0, 3) == '-1_')
					{
						// we need to pass both sections that belong to an orderfooter section and the orderfooter root
						// as we are unable to determine what the clicked checkbox belongs to.
						$tempArray = Array();
						$tempArray['sections'] = &$gSession['order']['orderFooterSections'];
						$tempArray['footer'] = &$gSession['order']['orderFooterCheckboxes'];

						$section = &DatabaseObj::getSectionByOrderLineId($sectionOrderLineId, $tempArray);
					}
					else
					{
						$section = &DatabaseObj::getSectionByOrderLineId($sectionOrderLineId);
					}

					$metaDataArray = isset($section['metadata']) ? $section['metadata'] : array();

					$keywordId = str_replace('keyword', '', $keywordId) * 1;

					$keywordsCount = count($metaDataArray);
					for ($i = 0; $i < $keywordsCount; $i++)
					{
						if (($metaDataArray[$i]['ref'] * 1) == $keywordId)
						{
							$section['metadata'][$i]['defaultvalue'] = UtilsObj::cleanseInput($value, false);
							break;
						}
					}
				}
        	}
        	DatabaseObj::updateSession();
		}

	}

	static function storeMetaData($pOrderID, $pOrderItemID, $pItemComponentID, $pUserID, $pSection, $pMetaDataArray)
	{
		$resultArray = array();
		$count = count($pMetaDataArray);
		$refList = array();
		$result = '';
		$resultParam = '';

		if ($count > 0)
		{
			$metaDataId = -1;

			$dbObj = DatabaseObj::getGlobalDBConnection();
			$metaDataInsert = "INSERT INTO `METADATA` (`id`, `datecreated`, `orderid`, `orderitemid`, `orderitemcomponentid`, `userid`, `section`)
					VALUES (0, now(), ?, ?, ?, ?, ?)";

			if ($stmt = $dbObj->prepare($metaDataInsert))
			{
				// Bind params for the metadata insert.
				$bindOk = $stmt->bind_param('iiiis', $pOrderID, $pOrderItemID, $pItemComponentID, $pUserID, $pSection);

				if ($bindOk)
				{
					if ($stmt->execute())
					{
						$metaDataId = $dbObj->insert_id;

						$metaDataValueInsert = "INSERT INTO `metadatavalues` (`metadataid`, `keywordref`, `value`) VALUES ";
						$bindArray = array('');
						$paramHolder = array();

						for ($i = 0; $i < $count; $i++)
						{
							// Generate the params to hold values.
							$paramHolder[] = "(?, ?, ?)";

							// Update the bind array for params we are passing.
							$bindArray[0] .= 'iis';

							// Add the values to use by ref.
							$bindArray[] = &$metaDataId;
							$bindArray[] = &$pMetaDataArray[$i]['ref'];
							$bindArray[] = &$pMetaDataArray[$i]['defaultvalue'];

							// Add to the refList.
							$refList[] = $pMetaDataArray[$i]['code'];
						}

						$metaDataValueInsert .= implode(', ', $paramHolder);

						$metaDataValuesStmt = $dbObj->prepare($metaDataValueInsert);

						if ($metaDataValuesStmt)
						{
							// Use call_user_func_array as we do not know how many params we are passing.
							$bindOk = call_user_func_array(array($metaDataValuesStmt, 'bind_param'), $bindArray);

							if ($bindOk)
							{
								if (! $metaDataValuesStmt->execute())
								{
									$result = 'str_DatabaseError';
									$resultParam = 'Metadatavalues execute error: ' . $dbObj->error;
								}
							}
							else
							{
								$result = 'str_DatabaseError';
								$resultParam = 'Metadatavalues bind error: ' . $dbObj->error;
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'Metadatavalues prepare error: ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'Metadata execute error: ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'Metadata bind error: ' . $dbObj->error;
				}
			}
			else
			{
				$result = 'str_DatabaseError';
				$resultParam = 'Metadata prepare error: ' . $dbObj->error;
			}
		}

		$resultArray['result'] = $result;
        $resultArray['resultparam'] = $resultParam;
        $resultArray['reflist'] = implode(',', $refList);

        return $resultArray;
	}

	static function getMetaData($pOrderID, $pOrderItemID, $pOrderItemComponentID, $pSection, $pMetaDataCodes, $pLocale = '')
	{
		$resultArray = array();

		$id = -1;
		$orderItemComponentID = -1;
		$keywordRef = -1;
		$keywordName = '';
		$keywordDescription = '';
		$keywordType = -1;
		$keywordFlags = '';
		$keywordCode = '';
		$metaDataValue = '';

		$keywordsArray = [];
		$count = 0;

		// If we have been passed a list of metadata codes populate keywordsArray and count.
		if ('' !== $pMetaDataCodes)
		{
			$keywordsArray = explode(',', $pMetaDataCodes);
			$count = count($keywordsArray);
		}
		
		$stmt = null;
		$bindOk = false;

		if ($count > 0)
		{
			$dbObj = DatabaseObj::getConnection();

			if ($dbObj)
			{
				$query = "SELECT md.id, md.orderitemcomponentid, kw.ref, kw.name, kw.description, kw.type, kw.flags, kw.code, mdv.value
						FROM `METADATA` md
						LEFT JOIN `metadatavalues` mdv ON mdv.metadataid=md.id
						LEFT JOIN `keywords` kw ON kw.ref=mdv.keywordref AND kw.code IN ('" . implode("','", $keywordsArray) . "')
						WHERE md.orderid=? AND md.section=?";

				// Prepare and bind the required params based on the section.
				if ($pSection == 'ORDER')
				{
					$query .= " ORDER BY mdv.id ASC";
					$stmt = $dbObj->prepare($query);

					if ($stmt)
					{
						$bindOk = $stmt->bind_param('is', $pOrderID, $pSection);
					}
				}
				else
				{
					// We are getting items for the component section so we need to add the following where conditions.
					$query .= " AND md.orderitemid = ? AND md.orderitemcomponentid = ?";
					$query .= " ORDER BY mdv.id ASC";

					// Prepare and bind the parameters.
					$stmt = $dbObj->prepare($query);

					if ($stmt)
					{
						$bindOk = $stmt->bind_param('isii', $pOrderID, $pSection, $pOrderItemID, $pOrderItemComponentID);
					}
				}

				if ($bindOk)
				{
					if ($stmt->bind_result($id, $orderItemComponentID, $keywordRef, $keywordName,
							$keywordDescription, $keywordType, $keywordFlags, $keywordCode, $metaDataValue))
					{
						if ($stmt->execute())
						{
							while ($stmt->fetch())
							{
								$metaDataItem = [];

								$metaDataItem['code'] = $keywordCode;
								$thumbnailURL = '';

								if (($keywordType == 'RADIOGROUP') || ($keywordType == 'POPUP'))
								{
									$nameArray = explode('<br>', $keywordName);
									$keywordName = $nameArray[0];
									$valueCode = $metaDataValue;
									$value = $valueCode;

									$codeArray = explode('<br>', $keywordFlags);
									$count2 = count($codeArray);

									for ($j = 0; $j < $count2; $j++)
									{
										$itemValue = $codeArray[$j];
										if ($keywordType == 'RADIOGROUP')
										{
											$itemValueArray = explode('<p>', $itemValue);
											$itemValue = $itemValueArray[0];
											if(count($itemValueArray) == 2)
											{
												$thumbnailURL = $itemValueArray[1];
											}
										}

										if ($itemValue == $valueCode)
										{
											$value = $nameArray[$j + 1];

											if ($pLocale != '')
											{
												$value = LocalizationObj::getLocaleString($value, $pLocale, true);
											}

											break;
										}
									}

								}
								else
								{
									$valueCode = '';
									$value = $metaDataValue;
								}

								if ($pLocale != '')
								{
									$metaDataItem['name'] = LocalizationObj::getLocaleString($keywordName, $pLocale, true);
								}
								else
								{
									$metaDataItem['name'] = $keywordName;
								}

								$metaDataItem['id'] = $id;
								$metaDataItem['ref'] = $keywordRef;
								$metaDataItem['orderitemcomponentid'] = $orderItemComponentID;
								$metaDataItem['description'] = LocalizationObj::getLocaleString($keywordDescription, $pLocale, true);
								$metaDataItem['type'] = $keywordType;
								$metaDataItem['valuecode'] = $valueCode;
								$metaDataItem['value'] = $value;
								$metaDataItem['thumbnailurl'] = $thumbnailURL;
								$resultArray[] = $metaDataItem;
							}
						}
					}
				}

				$stmt = null;
				$dbObj->close();
			}
		}

		return $resultArray;
	}

    /**
     * Get the mata data for a previous order
     *
     * @param array $pcomponentsFromOriginalOrder
     * @param array $pSectionMetadata
     * @param string $pPath
     */
    static function getMetaDataComponentValue($pcomponentsFromOriginalOrder, &$pSectionMetadata, $pPath)
    {
		if (!empty($pcomponentsFromOriginalOrder) && !empty($pSectionMetadata))
        {
            $iCountDefaultMeta = count($pSectionMetadata);
            foreach ($pcomponentsFromOriginalOrder as $aData)
            {
				if ($aData['islist'] == 0)
				{
					$aData['componentpath'] .= $aData['componentcode'];
				}

				if ($aData['componentpath'] == $pPath)
                {
                    // if the componet data contains the keyword array then this has been past from the designer
					if (array_key_exists('keywords', $aData))
					{
						$aMeta = $aData['keywords'];
					}
					else
					{
						$aMeta = MetaDataObj::getMetaData(
							$aData['orderid'], $aData['orderitemid'], $aData['id'], 'COMPONENT', $aData['codelist']
						);
					}

                    for ($iIncDefaultMeta = 0; $iIncDefaultMeta < $iCountDefaultMeta; $iIncDefaultMeta++)
                    {
                        $aDefaultMeta = $pSectionMetadata[$iIncDefaultMeta];
                        $iCountMeta = count($aMeta);
                        for ($iIncMeta = 0; $iIncMeta < $iCountMeta; $iIncMeta++)
                        {
                            $aDataMata = $aMeta[$iIncMeta];

                            if ($aDefaultMeta['code'] == $aDataMata['code'])
                            {
                                switch ($pSectionMetadata[$iIncDefaultMeta]['type'])
                                {
                                    case 'POPUP':
                                    case 'RADIOGROUP':
										if ($pSectionMetadata[$iIncDefaultMeta]['ref'] == $aDataMata['ref'])
										{
											$pSectionMetadata[$iIncDefaultMeta]['defaultvalue'] = $aDataMata['valuecode'];
										}
                                        break;
                                    default :
									{
										if ($pSectionMetadata[$iIncDefaultMeta]['ref'] == $aDataMata['ref'])
										{
											$pSectionMetadata[$iIncDefaultMeta]['defaultvalue'] = $aDataMata['value'];
										}
										break;
									}
								}
                                $iIncMeta = $iCountDefaultMeta;
                            }
                        }
                    }
                }
            }
        }
    }


   	static function buildDisplayOrderKeywordHTML($pMetaDataArray, $pSection)
	{
		$layoutHTML = '';
		if(!empty($pMetaDataArray))
		{
			if($pSection == 'ORDER')
			{
				$layoutHTML .= "<div class='headerbar_metadata'><div class='headertext'><span class='txt_sectionHeader'>" . SmartyObj::getParamValue('', 'str_LabelAdditionalInformation') . "</span></div></div>";
				$layoutHTML .= "<div class='order_metadataHolder'> ";
			}
			else
			{
				$layoutHTML .= "<div class= 'legendMeta'><span class='txt_hidden'>" . SmartyObj::getParamValue('Order', 'str_LabelMetadataLegend') . "</span></div>";
				$layoutHTML .= "<div class='component_metadataHolder'> ";
			}

			foreach ($pMetaDataArray as $key => $value)
			{
				if ($value['type'] == 'CHECKBOX')
				{
					if ($value['value'] == 1)
					{
						$value['value'] = SmartyObj::getParamValue('', 'str_LabelYes');
					}
					else if ($value['value'] == 0)
					{
						$value['value'] = SmartyObj::getParamValue('', 'str_LabelNo');
					}
				}

				$layoutHTML .= "<div class='metadataItem'>";

				if($pSection == 'ORDER')
				{
					$layoutHTML .= "<div class='metadataname'><span class='txt_ordermetadataname'><label for = '" . $value['code'] . "'>" . $value['name'] . "</label></span></div>";
				}
				else
				{
					$layoutHTML .= "<div class='metadataname'><span class='txt_metadataname'><label for = '" . $value['code'] . "'>" . $value['name'] . "</label></span></div>";
				}

				if ($value['description'] != '')
				{
					$layoutHTML .= "<div class='metadatadescription'><span class='txt_metadatadescription'><span class='description'>" . $value['description'] . "</span></span></div>";
				}

				if ($value['type'] == 'MULTILINE')
				{
					// replace \n with <br> so it display on mutiple line on HTML email.
					$valueArray = mb_split("\n", $value['value']);
					$value['value'] = implode("<br />", $valueArray);
				}

				if($value['value'] == '')
				{
					$value['value'] = strtoupper(SmartyObj::getParamValue('', 'str_LabelNone'));
				}

				$layoutHTML .= "<div class='metadatavalue'><span class='txt_metadatavalue'>";

				$labelPictureHTML = '';
				if ($value['type'] == 'RADIOGROUP' && $value['thumbnailurl'] != '')
				{
					$labelPicture = $value['thumbnailurl'];
					if ((substr($labelPicture, 0, 7) == 'http://') || (substr($labelPicture, 0, 8) == 'https://'))
					{
						$labelPictureURL = $labelPicture;
					}
					else
					{
						$labelPictureURL = str_replace('[WEBROOT]', UtilsObj::getBrandedWebUrl(), $labelPicture);
					}

					if ($labelPictureURL != '')
					{
						$labelPictureHTML = '<img class="radioImage" src="' . $labelPictureURL . '" alt="" />';
					}
				}
				$layoutHTML.= $labelPictureHTML.$value['value'] . "</span></div>";

				$layoutHTML .= "</div>";
			}
			$layoutHTML .= "</div>";

		}
		return $layoutHTML;
	}



}

?>