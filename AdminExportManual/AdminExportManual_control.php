<?php

use Security\RequestValidationTrait;

require_once('../Utils/UtilsAuthenticate.php');
require_once('../AdminExportManual/AdminExportManual_model.php');
require_once('../AdminExportManual/AdminExportManual_view.php');

class AdminExportManual_control
{
	use RequestValidationTrait;

	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminExportManual_model::displayForm();
			AdminExportManual_view::displayForm($resultArray);
		}
	}

	static function report()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminExportManual_model::report();
		}
	}

	static function export()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
		    $resultArray = AdminExportManual_model::reportExport();
		}
	}
}

?>