<?php

use Security\RequestValidationTrait;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../AdminScheduledTasks/AdminScheduledTasks_model.php');
require_once('../AdminScheduledTasks/AdminScheduledTasks_view.php');

class AdminScheduledTasks_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminScheduledTasks_model::initialize();
			AdminScheduledTasks_view::initialize($resultArray);
		}
	}

	static function displayList()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminScheduledTasks_model::displayList();
		}
	}

	static function taskEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $taskID = $_GET['id'];
            if ($taskID)
            {
                $resultArray = AdminScheduledTasks_model::displayEdit($taskID);
                AdminScheduledTasks_view::displayEdit($resultArray);
            }
        }
	}

	static function taskAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminScheduledTasks_model::displayEdit(-1);
			AdminScheduledTasks_view::displayAdd($resultArray);
		}
	}

	static function taskEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminScheduledTasks_model::taskEdit();
        }
	}

	static function taskAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminScheduledTasks_model::taskAdd();
        }
	}

	static function taskActivate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminScheduledTasks_model::taskActivate();
        }
	}

	static function taskDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminScheduledTasks_model::taskDelete();
        }
	}

	static function getScriptInfo()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminScheduledTasks_model::getScriptInfo();
        }
	}

	static function taskSchedulerActivate()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminScheduledTasks_model::taskSchedulerActivate();
        }
	}

	static function taskRunNow()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            AdminScheduledTasks_model::taskRunNow();
        }
	}

}

?>