<?php

require_once('../AdminTaopixOnlineVolumesAdmin/AdminTaopixOnlineVolumesAdmin_model.php');
require_once('../AdminTaopixOnlineVolumesAdmin/AdminTaopixOnlineVolumesAdmin_view.php');
require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsRoute.php');

use Security\RequestValidationTrait;

class AdminTaopixOnlineVolumesAdmin_control
{
	use RequestValidationTrait;
	
	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminTaopixOnlineVolumesAdmin_view::displayGrid();
		}
	}
	
	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaopixOnlineVolumesAdmin_model::getGridData();
			AdminTaopixOnlineVolumesAdmin_view::getGridData($resultArray);
		}
	}
	
	static function volumeAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$selectedServerID = $_GET['serverid'];
			AdminTaopixOnlineVolumesAdmin_view::displayAdd($selectedServerID);
		}
	}
	
	static function volumeEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$selectedVolumeID = $_GET['volumeid'];
			$selectedServerID = $_GET['serverid'];
			$resultArray = AdminTaopixOnlineVolumesAdmin_model::displayEdit($selectedVolumeID);
			AdminTaopixOnlineVolumesAdmin_view::displayEdit($resultArray, $selectedServerID);
		}
	}
	
	static function addEditVolume()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaopixOnlineVolumesAdmin_model::addEditVolume();
        	AdminTaopixOnlineVolumesAdmin_view::volumeSave($resultArray);
		}
	}
	
	static function activateVolume()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaopixOnlineVolumesAdmin_model::activateVolume();
        	AdminTaopixOnlineVolumesAdmin_view::updateVolumesGrid($resultArray);
		}
	} 
	
	static function deleteVolume()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminTaopixOnlineVolumesAdmin_model::deleteVolume();
        	AdminTaopixOnlineVolumesAdmin_view::updateVolumesGrid($resultArray);
		}
	}
}

?>