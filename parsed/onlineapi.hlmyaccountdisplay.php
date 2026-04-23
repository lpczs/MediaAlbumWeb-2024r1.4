<?php

/* OnlineAPI.hlMyAccountDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlMyAccountDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="hlMyAccountDisplay"> */
$myFusebox['thisFuseaction'] = "hlMyAccountDisplay";
/* OnlineAPI.hlMyAccountDisplay: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.hlMyAccountDisplay: <fusebox:invoke object="control" methodcall="highLevelMyAccountDisplay()"> */
$control->highLevelMyAccountDisplay();
/* OnlineAPI.hlMyAccountDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlMyAccountDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="hlMyAccountDisplay"> */
$myFusebox['thisFuseaction'] = "hlMyAccountDisplay";
/* OnlineAPI.hlMyAccountDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.hlMyAccountDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="hlMyAccountDisplay"> */
$myFusebox['thisFuseaction'] = "hlMyAccountDisplay";

?>