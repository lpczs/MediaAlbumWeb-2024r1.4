<?php

/* OnlineAPI.checkUserSession: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.checkUserSession: <fusebox:set name="myFusebox['thisFuseaction']" value="checkUserSession"> */
$myFusebox['thisFuseaction'] = "checkUserSession";
/* OnlineAPI.checkUserSession: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.checkUserSession: <fusebox:invoke object="control" methodcall="highLevelCheckUserSession()"> */
$control->highLevelCheckUserSession();
/* OnlineAPI.checkUserSession: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.checkUserSession: <fusebox:set name="myFusebox['thisFuseaction']" value="checkUserSession"> */
$myFusebox['thisFuseaction'] = "checkUserSession";
/* OnlineAPI.checkUserSession: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.checkUserSession: <fusebox:set name="myFusebox['thisFuseaction']" value="checkUserSession"> */
$myFusebox['thisFuseaction'] = "checkUserSession";

?>