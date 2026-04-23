<?php

class AdminMetadataKeywordsGroups_view
{
	static function initialize()
	{
		global $gConstants;
		
        $smarty = SmartyObj::newSmarty('AdminMetadataKeywordsGroups');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        		
        $smarty->displayLocale('admin/metadatakeywordsgroups/metadatakeywordsgroupsgrid.tpl');
	}
	
	
	static function displayEntry($pResultArray)
	{
		global $gConstants;
		
		$id = $pResultArray['id'];
		$title = ($id > 0) ? 'str_TitleEditKeywordGroup' : 'str_TitleNewKeywordGroup';
		
        $smarty = SmartyObj::newSmarty('AdminMetadataKeywordsGroups');
        $smarty->assign('optioncfs', ($gConstants['optioncfs'] ? true : false));
        $smarty->assign('optionms', ($gConstants['optionms'] ? true : false));
        
        $smarty->assign('title', $smarty->get_config_vars($title));
        
        $smarty->assign('keywordGroupId', $id);
        $smarty->assign('allkeywordslist', $pResultArray['allKeywords']);
        $smarty->assign('keywordslist', $pResultArray['keywords']);
        
        $smarty->assign('productlist', UtilsObj::encodeString($pResultArray['productlist'], true));
        $smarty->assign('acceptedproducts', UtilsObj::encodeString($pResultArray['acceptedproducts'], true));
        
        $smarty->assign('licensekeylist', UtilsObj::encodeString($pResultArray['licensekeylist'], true));
        $smarty->assign('licenseKey', UtilsObj::encodeString($pResultArray['licenseKey'], true));
        		
        $smarty->assign('keywordSection', $pResultArray['section']);
        
        $smarty->displayLocale('admin/metadatakeywordsgroups/metadatakeywordsgroupsedit.tpl');
	}
}

?>
