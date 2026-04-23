<?php

use Security\RequestValidationTrait;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../AdminHome/AdminHome_model.php');
require_once('../AdminHome/AdminHome_view.php');

class AdminHome_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminHome_view::initialize();
		}
	}
}


?>
