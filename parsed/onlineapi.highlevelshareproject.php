<?php

/* OnlineAPI.highLevelShareProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.highLevelShareProject: <fusebox:set name="myFusebox['thisFuseaction']" value="highLevelShareProject"> */
$myFusebox['thisFuseaction'] = "highLevelShareProject";
/* OnlineAPI.highLevelShareProject: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.highLevelShareProject: <fusebox:invoke object="control" methodcall="highLevelShareProject()"> */
$control->highLevelShareProject();
/* OnlineAPI.highLevelShareProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.highLevelShareProject: <fusebox:set name="myFusebox['thisFuseaction']" value="highLevelShareProject"> */
$myFusebox['thisFuseaction'] = "highLevelShareProject";
/* OnlineAPI.highLevelShareProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.highLevelShareProject: <fusebox:set name="myFusebox['thisFuseaction']" value="highLevelShareProject"> */
$myFusebox['thisFuseaction'] = "highLevelShareProject";

?>