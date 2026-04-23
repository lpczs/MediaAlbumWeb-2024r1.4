<?php
require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminMetadataKeywords/AdminMetadataKeywords_model.php');
require_once('../AdminMetadataKeywords/AdminMetadataKeywords_view.php');

use Security\RequestValidationTrait;

class AdminMetadataKeywords_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminMetadataKeywords_view::initialize();
		}
	} 
	
	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminMetadataKeywords_model::getGridData();
		}
	}
	
	static function addDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminMetadataKeywords_model::addDisplay();
			AdminMetadataKeywords_view::displayEntry($resultArray);
		}
	}
	
	static function editDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminMetadataKeywords_model::editDisplay();
			AdminMetadataKeywords_view::displayEntry($resultArray);
		}
	}
	
	static function keywordAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminMetadataKeywords_model::keywordAdd();
		}
	}
	
	static function keywordEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminMetadataKeywords_model::keywordEdit();
		}
	}
	
	/**
	 * Endpoint for uploading a metadata keywork image.
	 */
	static function keywordUploadImage()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$tempFile = UtilsObj::getPOSTParam('tmpfile');
			$resultArray = AdminMetadataKeywords_model::keywordUploadImage($_FILES['keywordimage']);
			AdminMetadataKeywords_view::keywordUploadImage($resultArray);
		}
	}

	/**
	 * Clears the session data for uplaoded image paths.
	 */
	static function clearSessionImagePath() 
	{
		global $gSession;

		if ($gSession['previewpath'] !== '')
		{
			AdminMetadataKeywords_model::resetSessionImagePath();
		}

		AdminMetadataKeywords_view::clearSessionImagePath();
	}
}

?>