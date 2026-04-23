<?php
$FUSEBOX_APPLICATION_PATH = "../";
$FUSEBOX_APPLICATION_NAME = "TAOPIX";

$gDefaultSiteBrandingCode = '';

// bring in the $application scope
@include($FUSEBOX_APPLICATION_PATH.'parsed/app_'.$FUSEBOX_APPLICATION_NAME.'.php');
include("../libs/phpfb41/fusebox4.runtime.php4.php");

?>
