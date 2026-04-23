<?php

use Security\RequestValidationTrait;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminConstants/AdminConstants_model.php');
require_once('../AdminConstants/AdminConstants_view.php');

class AdminConstants_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminConstants_model::displayEdit();
			AdminConstants_view::displayEdit($resultArray);
		}
	}

	static function constantsEdit()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
            $resultArray = AdminConstants_model::constantsEdit();
			AdminConstants_view::constantsEdit($resultArray);
        }
	}

}

?>