<?php

/* OnlineAPI.previewSharedProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.previewSharedProject: <fusebox:set name="myFusebox['thisFuseaction']" value="previewSharedProject"> */
$myFusebox['thisFuseaction'] = "previewSharedProject";
/* OnlineAPI.previewSharedProject: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.previewSharedProject: <fusebox:invoke object="control" methodcall="previewSharedProject()"> */
$control->previewSharedProject();
/* OnlineAPI.previewSharedProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.previewSharedProject: <fusebox:set name="myFusebox['thisFuseaction']" value="previewSharedProject"> */
$myFusebox['thisFuseaction'] = "previewSharedProject";
/* OnlineAPI.previewSharedProject: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.previewSharedProject: <fusebox:set name="myFusebox['thisFuseaction']" value="previewSharedProject"> */
$myFusebox['thisFuseaction'] = "previewSharedProject";

?>