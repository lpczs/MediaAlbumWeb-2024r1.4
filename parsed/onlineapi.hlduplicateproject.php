<?php

/* OnlineAPI.hlDuplicateProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlDuplicateProject: <fusebox:set name="myFusebox['thisFuseaction']" value="hlDuplicateProject"> */
$myFusebox['thisFuseaction'] = "hlDuplicateProject";
/* OnlineAPI.hlDuplicateProject: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.hlDuplicateProject: <fusebox:invoke object="control" methodcall="highLevelDuplicateProject()"> */
$control->highLevelDuplicateProject();
/* OnlineAPI.hlDuplicateProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlDuplicateProject: <fusebox:set name="myFusebox['thisFuseaction']" value="hlDuplicateProject"> */
$myFusebox['thisFuseaction'] = "hlDuplicateProject";
/* OnlineAPI.hlDuplicateProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlDuplicateProject: <fusebox:set name="myFusebox['thisFuseaction']" value="hlDuplicateProject"> */
$myFusebox['thisFuseaction'] = "hlDuplicateProject";

?>