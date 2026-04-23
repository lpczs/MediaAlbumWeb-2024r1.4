<?php

use Security\RequestValidationTrait;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminExportEvent/AdminExportEvent_model.php');
require_once('../AdminExportEvent/AdminExportEvent_view.php');

/**
 * AdminExportEvent - Class for Data Export
 * NOTE: Requires PHP version 5 or later
 * @package AdminExportEvent
 * @author Kevin Gale
 * @copyright Taopix Ltd.
 * @version 3.0.0
 * @since Version 1.0.0
 */
class AdminExportEvent_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminExportEvent_model::displayList();
			AdminExportEvent_view::displayList($resultArray);
		}
	}

	static function getEventList()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminExportEvent_model::getEventList();
		}
	}

	static function eventActivate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)	
		{
		    $result = AdminExportEvent_model::eventActivate();
		}
	}

	static function eventShowGrid()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminExportEvent_model::getGridData();
		}
	}

	static function eventEdit()
	{
        if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminExportEvent_model::eventEdit();
			AdminExportEvent_view::eventEdit($resultArray);
		}
  	}
}

?>