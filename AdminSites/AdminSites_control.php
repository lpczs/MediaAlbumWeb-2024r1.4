<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../AdminSites/AdminSites_model.php');
require_once('../AdminSites/AdminSites_view.php');

class AdminSites_control
{
	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminSites_view::initialize();
		}
	} 	

	static function initialize2()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminSites_view::initialize2();
		}
	} 	

}

?>