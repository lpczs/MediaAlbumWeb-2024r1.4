<?php

/* AppAPI.callback: <fusebox:set name="myFusebox['thisCircuit']" value="AppAPI"> */
$myFusebox['thisCircuit'] = "AppAPI";
/* AppAPI.callback: <fusebox:set name="myFusebox['thisFuseaction']" value="callback"> */
$myFusebox['thisFuseaction'] = "callback";
/* AppAPI.callback: <fusebox:instantiate object="control" class="AppAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AppAPI/AppAPI_control.php");
$control = new AppAPI_control;
/* AppAPI.callback: <fusebox:invoke object="control" methodcall="callback()">  */
$control->callback();
/* AppAPI.callback: <fusebox:set name="myFusebox['thisCircuit']" value="AppAPI"> */
$myFusebox['thisCircuit'] = "AppAPI";
/* AppAPI.callback: <fusebox:set name="myFusebox['thisFuseaction']" value="callback"> */
$myFusebox['thisFuseaction'] = "callback";
/* AppAPI.callback: <fusebox:set name="myFusebox['thisCircuit']" value="AppAPI"> */
$myFusebox['thisCircuit'] = "AppAPI";
/* AppAPI.callback: <fusebox:set name="myFusebox['thisFuseaction']" value="callback"> */
$myFusebox['thisFuseaction'] = "callback";

?>