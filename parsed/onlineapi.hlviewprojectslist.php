<?php

/* OnlineAPI.hlViewProjectsList: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlViewProjectsList: <fusebox:set name="myFusebox['thisFuseaction']" value="hlViewProjectsList"> */
$myFusebox['thisFuseaction'] = "hlViewProjectsList";
/* OnlineAPI.hlViewProjectsList: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.hlViewProjectsList: <fusebox:invoke object="control" methodcall="highLevelViewProjectsList()"> */
$control->highLevelViewProjectsList();
/* OnlineAPI.hlViewProjectsList: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlViewProjectsList: <fusebox:set name="myFusebox['thisFuseaction']" value="hlViewProjectsList"> */
$myFusebox['thisFuseaction'] = "hlViewProjectsList";
/* OnlineAPI.hlViewProjectsList: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlViewProjectsList: <fusebox:set name="myFusebox['thisFuseaction']" value="hlViewProjectsList"> */
$myFusebox['thisFuseaction'] = "hlViewProjectsList";

?>