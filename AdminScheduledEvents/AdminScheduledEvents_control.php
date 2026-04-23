<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../AdminScheduledEvents/AdminScheduledEvents_model.php');
require_once('../AdminScheduledEvents/AdminScheduledEvents_view.php');

use Security\RequestValidationTrait;

class AdminScheduledEvents_control 
{
	use RequestValidationTrait;

	static function initialize() 
	{
		if (AuthenticateObj::adminSessionActive() == 1)	
		{ 
			AdminScheduledEvents_view::initialize(); 
		}
	} 	
	
	static function displayList() 
	{
		if (AuthenticateObj::adminSessionActive() == 1)	
		{ 
			AdminScheduledEvents_model::displayList(); 
		}
	} 	
	
	static function eventDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminScheduledEvents_model::eventDelete();
        }
	}
	
	static function eventActivate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminScheduledEvents_model::eventActivate();
        }
	}
	
	static function detailsDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $eventID = $_GET['id'];
            if ($eventID)
            {
                $resultArray = AdminScheduledEvents_model::detailsDisplay($eventID);
                AdminScheduledEvents_view::detailsDisplay($resultArray);
            }
        }
	}  
	
	static function eventEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminScheduledEvents_model::eventEdit();
        }      
	}
	
	static function eventRun()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
        	AdminScheduledEvents_model::eventRun();
        }  
	}
	
}
?>