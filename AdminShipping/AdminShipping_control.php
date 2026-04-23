<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../AdminShipping/AdminShipping_model.php');
require_once('../AdminShipping/AdminShipping_view.php');

class AdminShipping_control
{
	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminShipping_view::initialize();
		}
	} 	

	static function initialize2()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminShipping_view::initialize2();
		}
	} 	

}

?>