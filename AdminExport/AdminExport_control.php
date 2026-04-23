<?php

require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../AdminExport/AdminExport_model.php');
require_once('../AdminExport/AdminExport_view.php');

class AdminExport_control
{
	static function initialize()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminExport_view::initialize();
		}
	}

	static function initialize2()
	{
		if (AuthenticateObj::adminSessionActive() == 1)
		{
			AdminExport_view::initialize2();
		}
	}

}

?>