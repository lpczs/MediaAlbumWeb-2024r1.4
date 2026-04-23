<?php

require_once('../Utils/UtilsDatabase.php');

class AdminMetadataKeywordsGroups_model
{
    static function getGridData()
	{
        global $gSession;

        $resultArray  = array();
        $summaryArray = array();
        $keywordItem = array();
        $keywords = array();
        $totalCount = 0;

        $typesArray = array();
		$paramArray = array();
		$stmtArray = array();
		$sectionQuery = '';

		$keywordsColSize = isset($_GET['size']) ? $_GET['size'] : '250';
		$keywordsDefaultsCol = isset($_GET['defaultscol']) ? $_GET['defaultscol'] : 0;
		$keywordsSection = isset($_GET['section']) ? $_GET['section'] : '';

		$dbObj = DatabaseObj::getGlobalDBConnection();
		$smarty = SmartyObj::newSmarty('AdminMetadataKeywordsGroups');
    	if ($dbObj)
		{
			if ($keywordsSection)
			{
				$typesArray[] = 's';
				$paramArray[] = $keywordsSection;
				$sectionQuery = ' AND kgh.section = ?';
			}

			$stmt = $dbObj->prepare('SELECT kg.id, kg.keywordcode, kg.sortorder, kg.defaultvalue, kgh.id, kgh.groupcode, kgh.productcodes, kgh.section FROM KEYWORDGROUP kg LEFT JOIN KEYWORDGROUPHEADER kgh ON kg.keywordgroupheaderid = kgh.id WHERE kgh.id is not null '.$sectionQuery.' ORDER BY kgh.section, kgh.id, kg.sortorder');
			if ($stmt)
			{
				$bindOK = DatabaseObj::bindParams($stmt, $typesArray, $paramArray);

				if ($bindOK)
				{
					if ($stmt->bind_result($id, $keywordCode, $sortOrder, $defaultValue, $groupId, $groupCode, $productCodes, $section))
					{
						if ($stmt->execute())
						{
							while ($stmt->fetch())
							{
								$keywords[$groupId]['data'] = array('id'=>$groupId, 'groupCode'=>$groupCode, 'productCodes'=>$productCodes, 'section' => $section);
								$keywords[$groupId]['keywords'][] = array('keywordCode'=>$keywordCode, 'defaultValue'=>$defaultValue);
							}
						}
					}
					$stmt->free_result();
					$stmt->close();
				}
			}

			$dbObj->close();
		}

		foreach ($keywords as $key => $keyword)
		{
			$keywordItem['id'] = "'" . UtilsObj::ExtJSEscape($keyword['data']['id']) . "'";
			$keywordItem['groupCode'] = "'" . UtilsObj::ExtJSEscape($keyword['data']['groupCode']) . "'";
			$keywordItem['productCodes'] = "'" . UtilsObj::ExtJSEscape(str_replace(',', '<br>', $keyword['data']['productCodes'])) . "'";

			$keywordCodes = array();
			$keywordDefaults = array();
			for ($j = 0; $j < count($keyword['keywords']); $j++)
			{
				if ($keywordsDefaultsCol)
				{
					$keywordCodes[] = '<tr><td style="width: '.$keywordsColSize.'px">'.$keyword['keywords'][$j]['keywordCode'] . '</td></tr>';
					$keywordDefaults[] = '<tr><td>' . (($keyword['keywords'][$j]['defaultValue'] != '') ? $keyword['keywords'][$j]['defaultValue'] : '-') . '</td></tr>';
				}
				else
				{
					$keywordCodes[] = '<tr><td style="width: '.$keywordsColSize.'px">'.$keyword['keywords'][$j]['keywordCode'] . '</td><td>' . (($keyword['keywords'][$j]['defaultValue'] != '') ? $keyword['keywords'][$j]['defaultValue'] : '-') . '</td></tr>';
				}
			}
			$keywordItem['keywordCode'] = "'" . UtilsObj::ExtJSEscape('<table>'.join('', $keywordCodes).'</table>') . "'";

			if ($keywordsDefaultsCol)
			{
				$keywordItem['keywordDefaults'] = "'" . UtilsObj::ExtJSEscape('<table>'.join('', $keywordDefaults).'</table>') . "'";
			}

			$keywordItem['section'] = "'" . UtilsObj::ExtJSEscape($keyword['data']['section']) . "'";

			array_push($resultArray, '['.join(',', $keywordItem).']');
		}

		$summaryArray = join(',', $resultArray);
        if ($summaryArray != '')
        {
        	$summaryArray = ', ' . $summaryArray;
        }

        echo '[['.count($resultArray).']'.$summaryArray.']';
        return;
    }


    static function addDisplay()
    {
    	$resultArray = array();

    	/* get the list of keywords */
    	$resultKeywordsArray = Array();
        $keywordItem = array();

		$dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('SELECT id, code, type, flags FROM `KEYWORDS` ORDER BY code'))
			{
				if ($stmt->bind_result($id, $code, $type, $flags))
				{
					if ($stmt->execute())
					{
						while ($stmt->fetch())
						{
							$keywordItem['id'] = $id;
							$keywordItem['code'] = $code;
							$keywordItem['type'] = $type;
							$items = array();
							if (($type == 'RADIOGROUP') || ($type == 'POPUP'))
							{
								$flags = explode('<br>', $flags);

								for ($i = 0; $i < count($flags); $i++)
								{
									$flagsData = explode('<p>', $flags[$i]);
									$items[] = "['" . UtilsObj::ExtJSEscape($flagsData[0]) . "', '" . UtilsObj::ExtJSEscape($flagsData[0]) . "']";
								}
							}
							$keywordItem['values'] = '['.join(',', $items).']';

							array_push($resultKeywordsArray, $keywordItem);
						}
					}
				}
				$stmt->free_result();
				$stmt->close();
                $stmt = null;
			}
			$dbObj->close();
		}

    	$resultArray['id'] = 0;
    	$resultArray['code'] = '';
    	$resultArray['allKeywords'] = $resultKeywordsArray;
    	$resultArray['keywords'] = array();

    	$resultArray['productlist'] = DatabaseObj::getProductList();
    	$resultArray['acceptedproducts'] = array();

    	$resultArray['licensekeylist'] = DatabaseObj::getLicenseKeysList();
    	$resultArray['licenseKey'] = '';

    	$resultArray['section'] = '';

    	return $resultArray;
    }

    static function editDisplay()
    {
    	$resultArray = array();

    	$kwGroupHeaderId = $_GET['id'];

    	/* get the list of all keywords */
    	$resultKeywordsArray = Array();
        $keywordItem = array();
        $keywordsArray = array();

        $dbObj = DatabaseObj::getGlobalDBConnection();
		if ($dbObj)
		{
			if ($stmt = $dbObj->prepare('SELECT id, code, type, flags FROM `KEYWORDS` ORDER BY code'))
			{
				if ($stmt->bind_result($id, $code, $type, $flags))
				{
					if ($stmt->execute())
					{
						while ($stmt->fetch())
						{
							$keywordItem['id'] = $id;
							$keywordItem['code'] = $code;
							$keywordItem['type'] = $type;

							$items = array();
							if (($type == 'RADIOGROUP') || ($type == 'POPUP'))
							{
								$flags = explode('<br>', $flags);

								for ($i = 0; $i < count($flags); $i++)
								{
									$flagsData = explode('<p>', $flags[$i]);
									$items[] = "['" . UtilsObj::ExtJSEscape($flagsData[0]) . "', '" . UtilsObj::ExtJSEscape($flagsData[0]) . "']";
								}
							}
							$keywordItem['values'] = '['.join(',', $items).']';

							array_push($resultKeywordsArray, $keywordItem);
						}
					}
				}
				$stmt->free_result();
				$stmt->close();
                $stmt = null;
			}

			/* get keywords for this group */
			$keywordItem = array();
			if ($stmt = $dbObj->prepare('SELECT kg.id, kg.keywordgroupheaderid, kg.keywordcode, kg.sortorder, kg.defaultvalue, kgh.groupcode, kgh.productcodes, kgh.section
				FROM KEYWORDGROUP kg LEFT JOIN KEYWORDGROUPHEADER kgh ON kg.keywordgroupheaderid = kgh.id WHERE kgh.id is not null AND kg.keywordgroupheaderid = ?
				ORDER BY kg.sortorder'))
			{
				if (($stmt->bind_param('i', $kwGroupHeaderId)) && ($stmt->bind_result($kwId, $kwGroupHeaderId, $kwCode, $sortOrder, $defaultValue, $groupCode, $products, $section)))
				{
					if ($stmt->execute())
					{
						while ($stmt->fetch())
						{
							$keywordItem['id'] = $kwId;
							$keywordItem['code'] = $kwCode;
							$keywordItem['type'] = '';
							$keywordItem['values'] = '';
							$keywordItem['defaultValue'] = $defaultValue;
							$keywordItem['sortOrder'] = $sortOrder;

							array_push($keywordsArray, $keywordItem);
						}
					}
				}
				$stmt->free_result();
				$stmt->close();
                $stmt = null;
			}

			$dbObj->close();
		}

		for ($i=0; $i < count($keywordsArray); $i++)
		{
			$kwItem = $keywordsArray[$i];

			for ($j = 0; $j < count($resultKeywordsArray); $j++)
			{
				$allKwItem = $resultKeywordsArray[$j];

				if ($kwItem['code'] == $allKwItem['code'])
				{
					$keywordsArray[$i]['type'] = $allKwItem['type'];
					$keywordsArray[$i]['values'] = $allKwItem['values'];
					array_splice($resultKeywordsArray, $j, 1);
					break;
				}
			}
		}

		$resultArray['id'] = $kwGroupHeaderId;
    	$resultArray['allKeywords'] = $resultKeywordsArray;
    	$resultArray['keywords'] = $keywordsArray;

    	$resultArray['productlist'] = DatabaseObj::getProductList();
    	$resultArray['acceptedproducts'] = explode(',', $products);

    	$resultArray['licensekeylist'] = DatabaseObj::getLicenseKeysList();
    	$resultArray['licenseKey'] = $groupCode;

    	$resultArray['section'] = $section;

    	return $resultArray;
    }


    static function keywordGroupAdd()
    {
    	global $gConstants;
		global $gSession;

		$result = '';
		$headerId = 0;

		$groupCode = $_POST['groupCode'];
		$products = $_POST['products'];

		$keywordsCodes  = explode(',',$_POST['keywordsCodes']);
		$defaultValues  = explode(',',$_POST['defaultValues']);
		$sortOrder  = explode(',',$_POST['sortOrder']);

		$section = $_POST['keywordSection'];

		if ($section != 'ORDER')
		{
			$groupCode = '';
			$products = '';
		}

		// first create a keyword group header record
		$dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
        	if ($stmt = $dbObj->prepare('INSERT INTO `KEYWORDGROUPHEADER` (`id`, `datecreated`, `groupcode`, `section`, `productcodes`)
				VALUES (0, now(), ?, ?, ?)'))
			{
				if ($stmt->bind_param('sss', $groupCode, $section, $products))
				{
					if ($stmt->execute())
					{
						$headerId = $dbObj->insert_id;
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'keywordGroupAdd insert header execute ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'keywordGroupAdd insert header bind ' . $dbObj->error;
				}
				$stmt->free_result();
				$stmt->close();
			}
			else
			{
				$result = 'str_DatabaseError';
				$resultParam = 'keywordGroupAdd insert header prepare ' . $dbObj->error;
			}

			/* if record was inserted successfully then create records in keyword groups */
			if ($headerId > 0)
			{
				if ($stmt = $dbObj->prepare('INSERT INTO `KEYWORDGROUP` (`id`, `datecreated`, `keywordgroupheaderid`, `keywordcode`, `sortorder`, `defaultvalue`)
					VALUES (0, now(), ?, ?, ?, ?)'))
				{
					for ($i = 0; $i< count($keywordsCodes); $i++)
					{
						if ($stmt->bind_param('isis', $headerId, $keywordsCodes[$i], $sortOrder[$i], $defaultValues[$i]))
						{
							if (!$stmt->execute())
							{
								$result = 'str_DatabaseError';
								$resultParam = 'keywordGroupAdd insert execute ' . $dbObj->error;
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'keywordGroupAdd insert bind ' . $dbObj->error;
						}
						$stmt->free_result();
						//$stmt->close();
					}
				}
			}
			else
			{
				$result = 'str_DatabaseError';
				$resultParam = 'keywordGroupAdd insert ' . $dbObj->error;
			}

			$dbObj->close();
        }
        else
		{
			// could not prepare statement
			$result = 'str_DatabaseError';
			$resultParam = 'keywordGroupAdd connect ' . $dbObj->error;
		}

        if ($result == '')
		{
			echo '{"success": true,	"msg":"", "action":"keywordgroupadd"}';
		}
		else
		{
			$smarty = SmartyObj::newSmarty('AdminMetadataKeywordsGroups');
        	echo '{"success":false,	"msg":"' . $smarty->get_config_vars($result) . '"}';
        }
		return;
    }



    static function keywordGroupEdit()
    {
    	global $gConstants;
		global $gSession;

		$headerId = $_POST['id'];

		$result = '';
		$section = $_POST['keywordSection'];

		$groupCode = $_POST['groupCode'];
		$products = $_POST['products'];

		$keywordsModified = $_POST['keywordsModified'];

		$keywordsCodes  = explode(',',$_POST['keywordsCodes']);
		$defaultValues  = explode(',',$_POST['defaultValues']);
		$sortOrder  = explode(',',$_POST['sortOrder']);

    	if ($section != 'ORDER')
		{
			$groupCode = '';
			$products = '';
		}

		$dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)
        {
           	// update the group header
        	if ($stmt = $dbObj->prepare('UPDATE `KEYWORDGROUPHEADER` SET `groupcode` = ?, `section` = ?, `productcodes` = ? WHERE id = ?'))
			{
				if ($stmt->bind_param('sssi', $groupCode, $section, $products, $headerId))
				{
					if (!$stmt->execute())
					{
						$result = 'str_DatabaseError';
						$resultParam = 'keywordGroupEdit update header execute ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'keywordGroupEdit update header bind ' . $dbObj->error;
				}
				$stmt->free_result();
				$stmt->close();
			}
			else
			{
				$result = 'str_DatabaseError';
				$resultParam = 'keywordGroupEdit update header prepare ' . $dbObj->error;
			}

			if ($keywordsModified == 1)
			{
        		// first delete all keywords for this group
        		if ($stmt = $dbObj->prepare('DELETE FROM `KEYWORDGROUP` WHERE keywordgroupheaderid = ?'))
				{
					if ($stmt->bind_param('i', $headerId))
					{
						if (!$stmt->execute())
						{
							$result = 'str_DatabaseError';
							$resultParam = 'keywordGroupEdit delete execute ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'keywordGroupEdit delete bind ' . $dbObj->error;
					}
					$stmt->free_result();
					$stmt->close();
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'keywordGroupEdit delete prepare ' . $dbObj->error;
				}

				/* create new records in keyword groups */
				if ($stmt = $dbObj->prepare('INSERT INTO `KEYWORDGROUP` (`id`, `datecreated`, `keywordgroupheaderid`, `keywordcode`, `sortorder`, `defaultvalue`)
					VALUES (0, now(), ?, ?, ?, ?)'))
				{
					for ($i = 0; $i< count($keywordsCodes); $i++)
					{
						if ($stmt->bind_param('isis', $headerId, $keywordsCodes[$i], $sortOrder[$i], $defaultValues[$i]))
						{
							if (!$stmt->execute())
							{
								$result = 'str_DatabaseError';
								$resultParam = 'keywordGroupEdit insert execute ' . $dbObj->error;
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'keywordGroupEdit insert bind ' . $dbObj->error;
						}
						$stmt->free_result();
						//$stmt->close();
					}
				}
        	}

			$dbObj->close();
        }
        else
		{
			// could not prepare statement
			$result = 'str_DatabaseError';
			$resultParam = 'keywordGroupEdit connect ' . $dbObj->error;
		}

        if ($result == '')
		{
			echo '{"success": true,	"msg":"", "action":"keywordgroupedit"}';
		}
		else
		{
			$smarty = SmartyObj::newSmarty('AdminMetadataKeywordsGroups');
        	echo '{"success":false,	"msg":"' . $smarty->get_config_vars($result) . '. ' . $resultParam . '"}';
        }
		return;
	}

	static function getProductList($pGroupCode = '')
	{
		$projectItem = array();
		$projectResult = array();

        $productListArray = DatabaseObj::getProductList($pGroupCode);

		foreach($productListArray as $key => $project)
		{
			$projectItem['id'] = "'" . UtilsObj::ExtJSEscape($project['id']) . "'";
			$projectItem['code'] =  "'" . UtilsObj::ExtJSEscape($project['code']) . "'";
			$projectItem['projectname'] =  "'" . UtilsObj::ExtJSEscape($project['name']) . "'";
			$projectItem['active'] =  "'" . UtilsObj::ExtJSEscape($project['active']) . "'";

			array_push($projectResult, '[' . join(',', $projectItem) . ']');
		}

		$summaryArray = join(',', $projectResult);
        if ($summaryArray != '')
        {
        	$summaryArray = ', ' . $summaryArray;
        }

		echo '[[' . count($projectResult) . ']' . $summaryArray . ']';

        return;
    }

	static function getProductListByGroupCode($pGroupCode = '', $pOrderBy = '', $pOrderDir = 'ASC')
	{
		global $gSession;

		$projectItem = array();
		$projectResult = array();
		$id = 0;
		$productCode = '';
		$productName = '';
		$active = 0;
		$groupCodeSQL = '';
		$bindParamArray = array();

		// build the SQL

		if ($pGroupCode != '')
		{
			$groupCodeSQL = '((`pl`.`groupcode` = ?) OR (`pl`.`groupcode` = "")) AND ';
			$bindParamArray[] = 's';
			$bindParamArray[] = $pGroupCode;
		}

		$sql = 'SELECT `pr`.`id`, `pr`.`code`, `pr`.`name`, `pr`.`active`
			FROM `PRODUCTS` `pr`
			LEFT JOIN
			(
				SELECT `pl`.`groupcode`, `pl`.`productcode`, `pl`.`componentcode`
				FROM `PRICELINK` `pl`
				WHERE ' . $groupCodeSQL . '(`pl`.`componentcode` = "") AND (`pl`.`active` = 1)
				GROUP BY `pl`.`productcode`
			) AS `pl2`
			ON `pl2`.`productcode` = `pr`.`code`
			WHERE (NOT ISNULL(`pl2`.`productcode`)) AND (`pr`.`deleted` = 0)';

		if ($pOrderBy != '')
		{
			$sql .= ' ORDER BY `pr`.`' . $pOrderBy . '` ' . $pOrderDir;
		}
		else
		{
			$sql .= ' ORDER BY `pr`.`code` ASC';
		}

		$dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			$stmt = $dbObj->prepare($sql);

			if ($stmt)
			{
				if (count($bindParamArray) > 1)
				{
					$bindOK = call_user_func_array(array(&$stmt, 'bind_param'), UtilsObj::makeValuesReferenced($bindParamArray));
				}
				else
				{
					$bindOK = true;
				}

				if ($bindOK)
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->bind_result($id, $productCode, $productName, $active))
							{
								while ($stmt->fetch())
								{
									$projectItem['id'] = "'" . UtilsObj::ExtJSEscape($id) . "'";
									$projectItem['code'] =  "'" . UtilsObj::ExtJSEscape($productCode) . "'";
									$projectItem['projectname'] =  "'" . UtilsObj::ExtJSEscape(UtilsObj::encodeString(LocalizationObj::getLocaleString($productName,
                                                    $gSession['browserlanguagecode'], true), false)) . "'";
									$projectItem['active'] =  "'" . UtilsObj::ExtJSEscape($active) . "'";

									array_push($projectResult, '[' . join(',', $projectItem) . ']');
								}
							}
							else
							{
								error_log(__FUNCTION__ . ' Bind result failed ' . $dbObj->error);
							}
						}
						else
						{
							error_log(__FUNCTION__ . ' store result failed ' . $dbObj->error);
						}
					}
					else
					{
						error_log(__FUNCTION__ . ' execute failed ' . $dbObj->error);
					}
				}
				else
				{
					error_log(__FUNCTION__ . ' Bind param failed ' . $dbObj->error);
				}

				$stmt->free_result();
				$stmt->close();
                $stmt = null;
			}
			else
			{
				error_log(__FUNCTION__ . ' Prepare failed ' . $dbObj->error);
			}

			$dbObj->close();

			$summaryArray = join(',', $projectResult);
			if ($summaryArray != '')
			{
				$summaryArray = ', ' . $summaryArray;
			}

			echo '[[' . count($projectResult) . ']' . $summaryArray . ']';
		}

        return;
    }

	static function getAssociatedComponentList($pHeaderGroupID)
	{
		global $gSession;

		$componentList = array();
		$names = '';
		$result = '';

		// build the SQL
		$sql = 'SELECT `name`
				FROM `COMPONENTS`
				WHERE `keywordgroupheaderid` = ?';

		$dbObj = DatabaseObj::getGlobalDBConnection();

        if ($dbObj)
        {
			$stmt = $dbObj->prepare($sql);

			if ($stmt)
			{
				if ($stmt->bind_param('i', $pHeaderGroupID))
				{
					if ($stmt->execute())
					{
						if ($stmt->store_result())
						{
							if ($stmt->num_rows > 0)
							{
								if ($stmt->bind_result($names))
								{
									while ($stmt->fetch())
									{
										$componentList[] = LocalizationObj::getLocaleString($names, $gSession['browserlanguagecode'], true);
									}
								}
								else
								{
									$result = 'str_DatabaseError';
									$resultParam = 'getAssociatedComponent bindresult ' . $dbObj->error;
								}
							}
						}
						else
						{
							$result = 'str_DatabaseError';
							$resultParam = 'getAssociatedComponent store result ' . $dbObj->error;
						}
					}
					else
					{
						$result = 'str_DatabaseError';
						$resultParam = 'getAssociatedComponent execute ' . $dbObj->error;
					}
				}
				else
				{
					$result = 'str_DatabaseError';
					$resultParam = 'getAssociatedComponent bind param' . $dbObj->error;
				}
				$stmt->free_result();
				$stmt->close();
				$stmt = null;
			}
			else
			{
				$result = 'str_DatabaseError';
				$resultParam = 'getAssociatedComponent prepare ' . $dbObj->error;
			}

			$dbObj->close();
		}

        if ($result == '')
		{
			$smarty = SmartyObj::newSmarty('AdminMetadataKeywordsGroups');
			$title = $smarty->get_config_vars('str_TitleKeywordGroupSectionCannotBeModified');
			$msg = '';
			$countComponentList = count($componentList);
			if ($countComponentList > 0)
			{
				if ($countComponentList == 1)
				{
					$msg = $smarty->get_config_vars('str_LabelKeywordGroupUsedByComponent');
				}
				else
				{
					$msg = $smarty->get_config_vars('str_LabelKeywordGroupUsedByComponents');
				}

				$msg = str_replace('^0', join(', ', $componentList), $msg);
			}
			echo "{'success':'true', 'title':'" . UtilsObj::ExtJSEscape($title) . "', 'msg':'" . UtilsObj::ExtJSEscape($msg) . "', 'action':'getassociatedcomponentlist'}";
		}
		else
		{
			echo '{"success":false,	"msg":"' . $resultParam . '"}';
		}
		return;
    }
}
?>
