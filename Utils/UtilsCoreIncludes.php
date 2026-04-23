<?php
require_once('../Utils/UtilsConstants.php');
require_once('../Utils/Utils.php');
require_once('../Utils/UtilsLocalization.php');
require_once('../Utils/UtilsAuthenticate.php');
require_once('../Utils/UtilsDatabase.php');
require_once('../Utils/UtilsSmarty.php');

// set the standard php timeout here as the core includes are referenced by all parts of the system
UtilsObj::resetPHPScriptTimeout();
?>