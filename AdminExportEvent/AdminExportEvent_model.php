<?php

require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsDataExport.php');


class AdminExportEvent_model
{

	static function displayList() 
	{
	    $resultArray = Array();
	    
        $dbObj = DatabaseObj::getGlobalDBConnection();
        if ($dbObj)  
        {
            if ($stmt = $dbObj->prepare('SELECT `id`, `eventcode`, `language`, `exportformat`, `subfolderformat`, `filenameformat`, `webhook1url`, `webhook2url`, `active`  FROM `TRIGGERS` ORDER BY `eventcode`')) 
            {
                if ($stmt->bind_result($id, $eventcode, $language, $exportformat, $subfolderformat, $filenameformat, $webhook1url, $webhook2url, $isactive))  
                {
                    if ($stmt->execute()) 
                    {
                        while ($stmt->fetch()) 
                        {
							$eventItem['id'] = $id;
                            $eventItem['eventcode'] = $eventcode;
                            $eventItem['language'] = $language;
                            $eventItem['exportformat'] = $exportformat;
                            $eventItem['subfolderformat'] = $subfolderformat;
                            $eventItem['filenameformat'] = $filenameformat;
                            $eventItem['webhook1url'] = $webhook1url;
                            $eventItem['webhook2url'] = $webhook2url;
                            $eventItem['active'] = $isactive;
                            $eventDataArray = DataExportObj::getEventTriggerFromNameOrID($id);
                            $eventItem['paymentdata'] = $eventDataArray['paymentdata'];
							$eventItem['beautified'] = $eventDataArray['beautified'];
		                    array_push($resultArray, $eventItem);
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
            }
            $dbObj->close();
        }

        return $resultArray;
	}
	
	static function getEventList() 
	{
	 	global $gConstants;
        $summaryArray = Array();
	 	$bufArr = Array();	
	 	$smarty = SmartyObj::newSmarty('AdminExportEvent');
	    $dbObj = DatabaseObj::getGlobalDBConnection();
		$id = 0;
		$eventcode = '';
		$language = '';
		$exportformat = '';
		$subfolderformat = '';
		$filenameformat = '';
		$webhook1URL = '';
		$webhook2URL = '';
		$isactive = 0;
	    $filter = '';
	    
	    if ($gConstants['optioncfs'] == 0)
	    {
	    	$filter = 'WHERE (`eventcode` <> "SHIPPEDDISTRIBUTIONCENTRERECEIVED" AND `eventcode` <> "SHIPPEDDISTRIBUTIONCENTRESHIPPED" AND `eventcode` <> "SHIPPEDSTORERECEIVED" AND `eventcode` <> "SHIPPEDSTORECUSTOMERCOLLECTED")';
	    }
	    
        if ($dbObj) 
        {
            if ($stmt = $dbObj->prepare('SELECT `id`, `eventcode`, `language`, `exportformat`, `subfolderformat`, `filenameformat`, `webhook1url`, `webhook2url`, `active`  FROM `TRIGGERS` '.$filter.' ORDER BY `eventcode`')) 
            {
                if ($stmt->bind_result($id, $eventcode, $language, $exportformat, $subfolderformat, $filenameformat, $webhook1URL, $webhook2URL, $isactive)) 
                {
                    if ($stmt->execute()) 
                    {
                        while ($stmt->fetch()) 
                        {
                            $eventDataArray 	      = DataExportObj::getEventTriggerFromNameOrID($id);
                            $bufArr['id'] 			  = '"' . $id .'"';
        					$bufArr['eventCode'] 	  = '"' . $eventcode .'"';
        					$origLang 				  = $language;
        					if ($language == '00') $language = "Order";
       						elseif ($language == 'Default')	$language = $smarty->get_config_vars('str_LabelDefault').' ('. LocalizationObj::getLanguageNameFromCode($smarty, $gConstants['defaultlanguagecode']) .')';
       						else $language = LocalizationObj::getLanguageNameFromCode($smarty, $language);
        					$bufArr['eventLang'] 	  = '"' . $language .'"';
        					$bufArr['exportFormat']   = "'" . UtilsObj::ExtJSEscape($exportformat) ."'";
        					$bufArr['pathFormat'] 	  = "'" . UtilsObj::ExtJSEscape($subfolderformat) ."'";
        					$bufArr['filenameFormat'] = "'" . UtilsObj::ExtJSEscape($filenameformat) ."'";
                            $bufArr['webhook1url'] = "'" . UtilsObj::ExtJSEscape($webhook1URL) ."'";
							$bufArr['webhook2url'] = "'" . UtilsObj::ExtJSEscape($webhook2URL) ."'";
        					$bufArr['active'] 		  = '"' . $isactive .'"';
        					$bufArr['paymentdata']    = '"' . $eventDataArray['paymentdata'] .'"';
        					$bufArr['beautified']     = '"' . $eventDataArray['beautified'] .'"';
        					$bufArr['originalLang']   = '"' . $origLang .'"';

        					
        					array_push($summaryArray, '['.join(',', $bufArr).']');
                        }
                    }
                }
                $stmt->free_result();
                $stmt->close();
            }
            $dbObj->close();
        }
        echo '['.join(',', $summaryArray).']';
        return;
	}
	
    static function eventActivate()  
    {
        global $gSession;
        
        $ids = $_POST['ids'];
        $active = $_POST['active'];
        
        if($ids) 
        {
        	$ids = explode(',',$ids);
        	$dbObj = DatabaseObj::getGlobalDBConnection();
        	
        	if (($dbObj) && ($stmt = $dbObj->prepare('UPDATE `TRIGGERS` SET `active` = ? WHERE `id` = ?'))) 
        	{
        		for ($i = 0; $i < count($ids); $i++)
        		{
        			$id = $ids[$i];
        			$eventDataArray = DataExportObj::getEventTriggerFromNameOrID($id);
        			if ($stmt->bind_param('ii',$active, $id)) 
        			{
                   		if ($stmt->execute()) 
                   		{
                       		if ($eventDataArray['isactive'] == 1)
                            {
                           		DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                   'ADMIN', 'EVENT-DEACTIVATE', $id . ' ' . $eventDataArray['eventcode'], 1);
                            }
                            else
                            {
                           		DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0,
                                   'ADMIN', 'EVENT-ACTIVATE', $id . ' ' . $eventDataArray['eventcode'], 1);
                            }    
                   		}
                	}
                	$stmt->free_result();
               }
        	}
        	$stmt->close();
        	$dbObj->close();
        	echo '{"success":true, "data":[]}';
        } 
        else
        {
            echo '{"success":false,	"msg":"' . 'No records selected.' . '"}';
        }
        return;
    }
    
    
    static function getGridData()  
    {
       	$startDate = $_GET['startdate'];
        $endDate = $_GET['enddate'];
        $filterType = $_GET['filtertype'];
        $filterValue = $_GET['filtervalue'];
        $languagecode = $_GET['languagecode'];
        $dateType = $_GET['datetype'];
        $exportFormat = $_GET['exportformat'];
        $includePaymentData = $_GET['includepaymentdata'];
        $beautifyXML = $_GET['beautifyxml'];
        
       	$reportArray = DataExportObj::getExportCompleteLong($startDate, $endDate, $filterType, $filterValue, $languagecode, $dateType, $includePaymentData);
       	$text = DataExportObj::exportDataGenerate($reportArray, $exportFormat, $beautifyXML, 'order');
       	
       	$fileName = 'Report_' . date('d_M_Y_His');
       	switch ($exportFormat) 
       	{
			case 'XML':
				header('Content-Type: text/xml');
				header('Content-Disposition: Attachment; filename=' . $fileName . '.xml');
				break;
			case 'TXT':
				header('Content-Type: text/tab-separated-values');
				header('Content-Disposition: Attachment; filename=' . $fileName . '.txt');
				break;
		}		
		header('Pragma: no-cache'); 
		header('Expires: 0');
		echo $text;
       	return;
	}
    
    
    static function eventEdit()
    {
        global $gSession;
 		
 		$result = '';
        $resultParam = '';
 		$id = $_POST['id'];
        $eventCode = $_POST['eventcode'];
 		$subFolder = $_POST['subfolder'];
		$fileName = $_POST['filename'];
		$languageCode = $_POST['languagecode'];
		$exportFormat = $_POST['exportformat'];
		$includePaymentData = $_POST['includepaymentdata'];
		$beautifyXml = $_POST['beautifyxml'];
        $isActive = $_POST['isactive'];
		$webhook1URL = $_POST['webhook1url'];
		$webhook2URL = $_POST['webhook2url'];
        
        $smarty = SmartyObj::newSmarty('AdminExportEvent');
        
        if ($id > 0)
        {
            $dbObj = DatabaseObj::getGlobalDBConnection();
            if ($dbObj) 
            {
                if ($stmt = $dbObj->prepare('UPDATE `TRIGGERS` SET `language` = ?, `exportformat` = ?, `includepaymentdata` = ?, `beautifiedxml` = ?, 
                	`subfolderformat` = ?, `filenameformat` = ?, `webhook1url` = ?, `webhook2url` = ?, `active` = ? WHERE `id` = ?')) 
                {
                    if ($stmt->bind_param('ssiissssii', $languageCode, $exportFormat, $includePaymentData, $beautifyXml, $subFolder, $fileName, $webhook1URL, $webhook2URL, $isActive, $id))  
                    {
                        if ($stmt->execute())  
                        {
                            DatabaseObj::updateActivityLog($gSession['ref'], 0, $gSession['userid'], $gSession['userlogin'], $gSession['username'], 0, 'ADMIN', 'EVENT-UPDATE', $id . ' ' . $eventCode, 1);
                        } 
                        else  
                        {
                            $result = 'str_DatabaseError';
                            $resultParam = 'productEdit execute ' . $dbObj->error;
                        }
                    } 
                    else 
                    {
                        // could not bind parameters
                        $result = 'str_DatabaseError';
                        $resultParam = 'productEdit bind ' . $dbObj->error;
                    }
                    $stmt->free_result();
                } 
                else 
                {
                    // could not prepare statement
                    $result = 'str_DatabaseError';
                    $resultParam = 'productEdit prepare ' . $dbObj->error;
                }
                $dbObj->close();
            } 
            else 
            {
                // could not open database connection
                $result = 'str_DatabaseError';
                $resultParam = 'productEdit connect ' . $dbObj->error;
            }
        }
 		
 		if ($result == '')	
 		{ 		
			$item['eventId'] = $id;
			$item['eventCode'] = $eventCode;
			$item['exportFormat'] = $exportFormat;
			$item['filePath'] = $subFolder;
			$item['filenameFormat'] = $fileName;
			$item['active'] = $isActive;
			$item['paymentdata'] = $includePaymentData;
			$item['beautified'] = $beautifyXml;
			$item['originalLang'] = $languageCode;
			$item['webhook1url'] = $webhook1URL;
			$item['webhook2url'] = $webhook2URL;
			
            if ($languageCode == '00')
            {    
                $languageCode = "Order";
            }    
            elseif ($languageCode == 'Default')
            {    
                $languageCode == 'Default';
            }    
            else
            {    
                $languageCode = LocalizationObj::getLanguageNameFromCode($smarty, $languageCode);
            }    
       		$item['eventLang'] = $languageCode;
		}
		
		$resultArray['result'] 		= $result;
		$resultArray['resultparam'] = $resultParam;
		$resultArray['item'] 		= $item;

		return $resultArray;
    } 
}
      
?>
