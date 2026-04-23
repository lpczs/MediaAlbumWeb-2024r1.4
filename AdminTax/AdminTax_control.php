<?php

use Security\RequestValidationTrait;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../AdminTax/AdminTax_model.php');
require_once('../AdminTax/AdminTax_view.php');

class AdminTax_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminTax_view::initialize();
		}
	} 	

	static function initialize2()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminTax_view::initialize2();
		}
	} 	

}

?>