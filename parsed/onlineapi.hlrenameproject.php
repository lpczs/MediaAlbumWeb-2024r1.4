<?php

/* OnlineAPI.hlRenameProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlRenameProject: <fusebox:set name="myFusebox['thisFuseaction']" value="hlRenameProject"> */
$myFusebox['thisFuseaction'] = "hlRenameProject";
/* OnlineAPI.hlRenameProject: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.hlRenameProject: <fusebox:invoke object="control" methodcall="highLevelRenameProject()"> */
$control->highLevelRenameProject();
/* OnlineAPI.hlRenameProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlRenameProject: <fusebox:set name="myFusebox['thisFuseaction']" value="hlRenameProject"> */
$myFusebox['thisFuseaction'] = "hlRenameProject";
/* OnlineAPI.hlRenameProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlRenameProject: <fusebox:set name="myFusebox['thisFuseaction']" value="hlRenameProject"> */
$myFusebox['thisFuseaction'] = "hlRenameProject";

?>