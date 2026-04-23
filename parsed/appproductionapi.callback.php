<?php

/* AppProductionAPI.callback: <fusebox:set name="myFusebox['thisCircuit']" value="AppProductionAPI"> */
$myFusebox['thisCircuit'] = "AppProductionAPI";
/* AppProductionAPI.callback: <fusebox:set name="myFusebox['thisFuseaction']" value="callback"> */
$myFusebox['thisFuseaction'] = "callback";
/* AppProductionAPI.callback: <fusebox:instantiate object="control" class="AppProductionAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AppProductionAPI/AppProductionAPI_control.php");
$control = new AppProductionAPI_control;
/* AppProductionAPI.callback: <fusebox:invoke object="control" methodcall="callback()"> */
$control->callback();
/* AppProductionAPI.callback: <fusebox:set name="myFusebox['thisCircuit']" value="AppProductionAPI"> */
$myFusebox['thisCircuit'] = "AppProductionAPI";
/* AppProductionAPI.callback: <fusebox:set name="myFusebox['thisFuseaction']" value="callback"> */
$myFusebox['thisFuseaction'] = "callback";
/* AppProductionAPI.callback: <fusebox:set name="myFusebox['thisCircuit']" value="AppProductionAPI"> */
$myFusebox['thisCircuit'] = "AppProductionAPI";
/* AppProductionAPI.callback: <fusebox:set name="myFusebox['thisFuseaction']" value="callback"> */
$myFusebox['thisFuseaction'] = "callback";

?>