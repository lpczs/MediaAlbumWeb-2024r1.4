<?php

require_once('../Utils/UtilsAddress.php');
require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../AdminAutoUpdate/AdminAutoUpdate_model.php');
require_once('../AdminAutoUpdate/AdminAutoUpdate_view.php');

use Security\RequestValidationTrait;

/** 
* @class AdminAutoUpdate_control
* 
* Software auto update logic
* 
* @version 3.0.0
* @author Kevin Gale
* 
* @defgroup AutoUpdate 
* @{
*/

class AdminAutoUpdate_control 
{

	use RequestValidationTrait;

	/**
 	* Shows initial Auto Update frameset.
 	*
 	* @author Kevin Gale
 	*/
	static function initialize() 
	{
		if (AuthenticateObj::adminSessionActive() == 1)	
		{ 
			AdminAutoUpdate_view::initialize(); 
		}
	} 	


	/**
 	* Shows Auto Update left hand side menu links page.
 	*
 	* @author Kevin Gale
 	*/
	static function initialize2()
	{
		if (AuthenticateObj::adminSessionActive() == 1)	
		{ 
			AdminAutoUpdate_view::initialize2();
		}
	} 	


	/**
 	* Shows Auto Update Application initial page in the right hand side frame.
 	*
 	* @author Kevin Gale
 	*/
	static function initializeApplication() 
	{
		if (AuthenticateObj::adminSessionActive() == 1)	
		{ 
			AdminAutoUpdate_view::initializeApplication(); 
		}    
    }
    
    
    /**
 	* Returns list of Auto Update Application data.
 	*
 	* @author Kevin Gale
 	*/
	static function listApplication() 
    {
		if (AuthenticateObj::adminSessionActive() == 1)	
		{
			$resultArray = AdminAutoUpdate_model::listApplication();
		}    
    }
    
    
    /**
 	* Deletes Auto Update Application data.
 	*
 	* @author Kevin Gale
 	*/
	static function deleteApplication() 
    {
		if (AuthenticateObj::adminSessionActive() == 1) 
		{	
			AdminAutoUpdate_model::deleteApplication();	
		}    
    }
    
    static function deleteProduct()   
    {
		if (AuthenticateObj::adminSessionActive() == 1)	
		{   
			AdminAutoUpdate_model::deleteProduct(); 
		}    
    }
    
    /**
 	* Shows Auto Update Products initial page in the right hand side frame.
 	*
 	* @author Kevin Gale
 	*/
    static function initializeProducts()   
    {
		if (AuthenticateObj::adminSessionActive() == 1)	
		{	
			AdminAutoUpdate_view::initializeProducts();	
		} 
    }
    
    
    /**
 	* Returns list of Auto Update Products data.
 	*
 	* @author Kevin Gale
 	*/
    static function listProducts()   
    {
		if (AuthenticateObj::adminSessionActive() == 1)	
		{
			$resultArray = AdminAutoUpdate_model::listProducts();
			AdminAutoUpdate_view::listProducts($resultArray);
		}    
    }


	/**
 	* Shows Auto Update Masks initial page in the right hand side frame.
 	*
 	* @author Kevin Gale
 	*/
	static function initializeMasks()  
	{
		if (AuthenticateObj::adminSessionActive() == 1)	
		{ 
			AdminAutoUpdate_view::initializeApplicationFiles(TPX_APPLICATION_FILE_TYPE_MASK); 
		}    
    }
	
	
	/**
 	* Shows Auto Update Backgrounds initial page in the right hand side frame.
 	*
 	* @author Kevin Gale
 	*/
	static function initializeBackgrounds() 
	{
		if (AuthenticateObj::adminSessionActive() == 1) 
		{ 
			AdminAutoUpdate_view::initializeApplicationFiles(TPX_APPLICATION_FILE_TYPE_BACKGROUND); 
		}    
    }
	
	
	/**
 	* Shows Auto Update Scapbook Pictures initial page in the right hand side frame.
 	*
 	* @author Kevin Gale
 	*/
	static function initializeScrapbookPictures()  
	{
		if (AuthenticateObj::adminSessionActive() == 1) 
		{ 
			AdminAutoUpdate_view::initializeApplicationFiles(TPX_APPLICATION_FILE_TYPE_PICTURE);	
		}    
    }
    
    
    /**
 	* Shows Auto Update Frames initial page in the right hand side frame.
 	*
 	* @author Kevin Gale
 	*/
	static function initializeFrames()  
	{
		if (AuthenticateObj::adminSessionActive() == 1)	
		{ 
			AdminAutoUpdate_view::initializeApplicationFiles(TPX_APPLICATION_FILE_TYPE_FRAME);	
		}    
    }
    
	    
	/**
 	* Returns list of Auto Update Files data.
 	*
 	* @author Kevin Gale
 	*/
    static function getFileList()  
    {
		if (AuthenticateObj::adminSessionActive() == 1)	
		{	
			AdminAutoUpdate_model::getFileList(); 
		}    
    }
    
    
    /**
 	* Makes selected Auto Update File records active.
 	*
 	* @author Kevin Gale
 	*/
    static function activateApplicationFile()  
    {
		if (AuthenticateObj::adminSessionActive() == 1)	
		{  
			AdminAutoUpdate_model::activateApplicationFile(); 
		}    
    }
    

    /**
 	* Makes selected Auto Update File records active.
 	*
 	* @author Stuart Milne
 	*/
    static function activateApplicationFileOnline()  
    {
		if (AuthenticateObj::adminSessionActive() == 1)	
		{  
			AdminAutoUpdate_model::activateApplicationFileOnline(); 
		}    
    }
    


	/**
 	* Deletes Auto Update Files data.
 	*
 	* @author Kevin Gale
 	*/
    static function deleteApplicationFile() 
    {
		if (AuthenticateObj::adminSessionActive() == 1)	
		{  
			AdminAutoUpdate_model::deleteApplicationFile();	
		}    
    }
    
    
    /**
 	* Shows Auto Update License Keys initial page in the right hand side frame.
 	*
 	* @author Kevin Gale
 	*/
    static function initializeLicenseKeys() 
    {
		if (AuthenticateObj::adminSessionActive() == 1) 
		{
			AdminAutoUpdate_view::initializeLicenseKeys(); 
		}    
    }
    
    
    /**
 	* Returns list of Auto Update License Key data.
 	*
 	* @author Kevin Gale
 	*/
    static function listLicenseKeys() 
    {
		if (AuthenticateObj::adminSessionActive() == 1)	
		{
			AdminAutoUpdate_model::listLicenseKeys();
		}    
    }
    
    
    /**
 	* Makes selected Auto Update License Key records active.
 	*
 	* @author Kevin Gale
 	*/
    static function activateLicenseKey() 
    {
		if (AuthenticateObj::adminSessionActive() == 1) 
		{ 
			$resultArray = AdminAutoUpdate_model::activateLicenseKey(); 
		}    
    }
    
    static function activateLicenseKeyOnline() 
    {
		if (AuthenticateObj::adminSessionActive() == 1) 
		{ 
			$resultArray = AdminAutoUpdate_model::activateLicenseKeyOnline(); 
		}    
    }
    
    static function activateProductCollection() 
    {
		if (AuthenticateObj::adminSessionActive() == 1) 
		{ 
			$resultArray = AdminAutoUpdate_model::activateProductCollection(); 
		}    
    }
    
    static function changeLicenseKeyPriority() 
    {
		if (AuthenticateObj::adminSessionActive() == 1) 
		{ 
			$resultArray = AdminAutoUpdate_model::changeLicenseKeyPriority(); 
		}    
    }
    
    static function changeProductCollectionPriority() 
    {
		if (AuthenticateObj::adminSessionActive() == 1)
		{ 
			$resultArray = AdminAutoUpdate_model::changeProductCollectionPriority();
		}    
    }

	static function changeApplicationPriority()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$IDList = $_POST['idlist'];
			$OSList = $_POST['oslist'];
			$command = $_POST['command'];

			$resultArray = AdminAutoUpdate_model::changeApplicationPriority($command, $IDList, $OSList);
			AdminAutoUpdate_view::changeApplicationPriority($resultArray);
		}
	}
    
    static function changeApplicationFilePriority() 
    {
		if (AuthenticateObj::adminSessionActive() == 1) 
		{ 
			$resultArray = AdminAutoUpdate_model::changeApplicationFilePriority(); 
		}    
    }

    /**
 	* Deletes Auto Update License Key data.
 	*
 	* @author Kevin Gale
 	*/
    static function deleteLicenseKey()  
    {
		if (AuthenticateObj::adminSessionActive() == 1) 
		{   
			$resultArray = AdminAutoUpdate_model::deleteLicenseKey();	
		}    
    }
    
    
    /**
 	* Shows License Key edit screen template with required values filled in.
 	*
 	* @author Kevin Gale
 	*/
    static function editLicenseKeyDisplay() 
    {
		if (AuthenticateObj::adminSessionActive() == 1)	
		{
            $resultArray = AdminAutoUpdate_model::editLicenseKeyDisplay();
            if ($resultArray['result'] == '') 
            { 
            	AdminAutoUpdate_view::editLicenseKeyDisplay($resultArray); 
            }
            else 
            {
                $licenseKeyDataArray = DatabaseObj::getAutoUpdateLicenseKeyList('**ALL**');
                AdminAutoUpdate_view::initializeLicenseKeys($licenseKeyDataArray);
            }
		}    
    }


	/**
 	* Validates and saves updated License Key records.
 	*
 	* @author Kevin Gale
 	*/
    static function editLicenseKey() 
    {
		if (AuthenticateObj::adminSessionActive() == 1) 
		{
            AdminAutoUpdate_model::editLicenseKey();
		}    
    }
    
    static function getDesignerSplashScreenImage()
	{
        if (AuthenticateObj::WebSessionActive() == 1)
        {
			$resultArray = AdminAutoUpdate_model::getDesignerSplashScreenImage();
			UtilsObj::getPreviewImage($resultArray);
        }
	}
	
	static function uploadDesignerSplashScreenImage()
	{
        if (AuthenticateObj::WebSessionActive() == 1)
        {
			$resultArray = AdminAutoUpdate_model::uploadPreviewImage('splashscreen');
			AdminAutoUpdate_view::uploadDesignerSplashScreenImage($resultArray);
        }
	}
	
	static function getBannerImage()
	{
        if (AuthenticateObj::WebSessionActive() == 1)
        {
			$resultArray = AdminAutoUpdate_model::getBannerImage();
			UtilsObj::getPreviewImage($resultArray);
        }
	}
	
	static function uploadBannerImage()
	{
        if (AuthenticateObj::WebSessionActive() == 1)
        {
			$resultArray = AdminAutoUpdate_model::uploadBannerImage();
			AdminAutoUpdate_view::uploadDesignerBannerImage($resultArray);
        }
	}
	
	static function getPromoPanelImage()
	{
		if (AuthenticateObj::WebSessionActive() == 1)
        {
			$groupCode = UtilsObj::getGETParam('groupcode', '');
			$showTempFile = UtilsObj::getGETParam('tmp', 0);
			$resultArray = AdminAutoUpdate_model::getPromoPanelImage($groupCode, $showTempFile);
			AdminAutoUpdate_view::getPromoPanelImage($resultArray);
        }
	}

	static function uploadPromoPanelImage()
	{
		if (AuthenticateObj::WebSessionActive() == 1)
        {
			$resultArray = AdminAutoUpdate_model::uploadPromoPanelImage();
			AdminAutoUpdate_view::uploadPromoPanelImage($resultArray);
        }
	}
}
/** 
 * @} End of "defgroup AutoUpdate". 
 */

?>