<?php

require_once('../AdminSitesOrderRouting/AdminSitesOrderRouting_model.php');
require_once('../AdminSitesOrderRouting/AdminSitesOrderRouting_view.php');
require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsRoute.php');

use Security\RequestValidationTrait;

class AdminSitesOrderRouting_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminSitesOrderRouting_view::displayGrid();
		}
	} 

	static function getGridData()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesOrderRouting_model::getGridData();
			AdminSitesOrderRouting_view::getGridData($resultArray);
		}
	} 

	static function orderRoutingAddDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminSitesOrderRouting_view::displayAdd();
		}
	}
	
	static function orderRoutingEditDisplay()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$routingRuleId = $_GET['id'];
			
			if ($routingRuleId)
			{
			    $resultArray = AdminSitesOrderRouting_model::displayEdit($routingRuleId);
				AdminSitesOrderRouting_view::displayEdit($resultArray);
			}
		}	
	}
	
	static function getConditionValueStore()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesOrderRouting_model::getConditionValueStore();
			AdminSitesOrderRouting_view::getConditionValueStore($resultArray);
		}
	}

	static function routingAdd()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesOrderRouting_model::routingRuleAdd();
			AdminSitesOrderRouting_view::routingRuleSave($resultArray);
			
		}	
	}
	
	static function routingRulesEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesOrderRouting_model::routingRuleEdit();
			AdminSitesOrderRouting_view::routingRuleSave($resultArray);			
		}
	}
	
	static function routingRuleDelete()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesOrderRouting_model::routingRuleDelete();
			AdminSitesOrderRouting_view::routingRuleDelete($resultArray);
		}
	}  
	
	static function routingRulesTogglePriority()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			$resultArray = AdminSitesOrderRouting_model::routingRulesTogglePriority();
			AdminSitesOrderRouting_view::routingRulesTogglePriority($resultArray);
		}
	}
}

?>