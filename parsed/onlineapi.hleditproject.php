<?php

/* OnlineAPI.hlEditProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlEditProject: <fusebox:set name="myFusebox['thisFuseaction']" value="hlEditProject"> */
$myFusebox['thisFuseaction'] = "hlEditProject";
/* OnlineAPI.hlEditProject: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.hlEditProject: <fusebox:invoke object="control" methodcall="highLevelEditProject()"> */
$control->highLevelEditProject();
/* OnlineAPI.hlEditProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlEditProject: <fusebox:set name="myFusebox['thisFuseaction']" value="hlEditProject"> */
$myFusebox['thisFuseaction'] = "hlEditProject";
/* OnlineAPI.hlEditProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlEditProject: <fusebox:set name="myFusebox['thisFuseaction']" value="hlEditProject"> */
$myFusebox['thisFuseaction'] = "hlEditProject";

?>