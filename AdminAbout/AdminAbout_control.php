<?php

use Security\RequestValidationTrait;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../AdminAbout/AdminAbout_model.php');
require_once('../AdminAbout/AdminAbout_view.php');

class AdminAbout_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)	
		{
			AdminAbout_view::initialize();
		}
	}
}


?>