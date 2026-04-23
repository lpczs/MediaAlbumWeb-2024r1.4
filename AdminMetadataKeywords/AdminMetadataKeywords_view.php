<?php

class AdminMetadataKeywords_view
{
	static function initialize()
	{
		global $gConstants;
		
        $smarty = SmartyObj::newSmarty('AdminMetadataKeywords');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        		
        $smarty->displayLocale('admin/metadatakeywords/metadatakeywordsgrid.tpl');
	}
	
	
	static function displayEntry($resultArray)
	{
		global $gConstants;
		
		$id = $resultArray['id'];
		$title = ($id > 0) ? 'str_TitleEditKeyword' : 'str_TitleNewKeyword';
		
        $smarty = SmartyObj::newSmarty('AdminMetadataKeywords');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        
        $smarty->assign('title', $smarty->get_config_vars($title));
        
        $smarty->assign('keywordId', $id);
        $smarty->assign('code', $resultArray['code']);
        $smarty->assign('name', utilsObj::encodeString($resultArray['name'],true));
        $smarty->assign('description', utilsObj::encodeString($resultArray['description'],true));
        $smarty->assign('maxlength', $resultArray['maxlength']);
        $smarty->assign('height', $resultArray['height']);
        $smarty->assign('width', $resultArray['width']);
        $smarty->assign('uppsercase', $resultArray['uppsercase']);
        $smarty->assign('values', $resultArray['values']);
        $smarty->assign('kwRef', $resultArray['ref']);
        
        $smarty->assign('valuerequired', $resultArray['required']);
        
        //SINGLELINE, MULTILINE, POPUP, RADIOGROUP or CHECKBOX
        $typelist = array();
        $typelist[] = array('id' => 'CHECKBOX', 'name' => $smarty->get_config_vars('str_LabelCheckBox'));
 		$typelist[] = array('id' => 'MULTILINE', 'name' => $smarty->get_config_vars('str_LabelMultiLine'));
        $typelist[] = array('id' => 'POPUP', 'name' => $smarty->get_config_vars('str_LabelPopupList'));
        $typelist[] = array('id' => 'RADIOGROUP', 'name' => $smarty->get_config_vars('str_LabelRadioButtonGroup'));
        $typelist[] = array('id' => 'SINGLELINE', 'name' => $smarty->get_config_vars('str_LabelSingleLine'));
        $smarty->assign('typelist', $typelist);
        $smarty->assign('keywordType', $resultArray['type']);
        
        LocalizationObj::initAdminEditLocalizedNames($smarty, 'localizednametable', '', '');
        
        $smarty->assign('defaultlanguagecode', $gConstants['defaultlanguagecode']);
        		
        $smarty->displayLocale('admin/metadatakeywords/metadatakeywordsedit.tpl');
	}

	/**
	 * Saves the image for metadata keywords.
	 * 
	 * @param array $pResultArray Array with the result from the model.
	 */
	static function keywordUploadImage($pResultArray)
	{
		if ($pResultArray['error'] == '')
    	{
			echo '{"success": true, "path": "' . $pResultArray['path'] . '"}';
    	}
    	else
    	{
			echo '{success: false, "path": ""}';
    	}
	}

	/**
	 * Clears the session data for uplaoded image paths.
	 */
	static function clearSessionImagePath()
	{
		echo '{"success": true}';
	}
}

?>
