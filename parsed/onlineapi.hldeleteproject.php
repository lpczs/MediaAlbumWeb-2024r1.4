<?php

/* OnlineAPI.hlDeleteProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlDeleteProject: <fusebox:set name="myFusebox['thisFuseaction']" value="hlDeleteProject"> */
$myFusebox['thisFuseaction'] = "hlDeleteProject";
/* OnlineAPI.hlDeleteProject: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.hlDeleteProject: <fusebox:invoke object="control" methodcall="highLevelDeleteProject()"> */
$control->highLevelDeleteProject();
/* OnlineAPI.hlDeleteProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlDeleteProject: <fusebox:set name="myFusebox['thisFuseaction']" value="hlDeleteProject"> */
$myFusebox['thisFuseaction'] = "hlDeleteProject";
/* OnlineAPI.hlDeleteProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlDeleteProject: <fusebox:set name="myFusebox['thisFuseaction']" value="hlDeleteProject"> */
$myFusebox['thisFuseaction'] = "hlDeleteProject";

?>