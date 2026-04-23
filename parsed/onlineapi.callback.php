<?php

/* OnlineAPI.callback: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.callback: <fusebox:set name="myFusebox['thisFuseaction']" value="callback"> */
$myFusebox['thisFuseaction'] = "callback";
/* OnlineAPI.callback: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.callback: <fusebox:invoke object="control" methodcall="callback()"> */
$control->callback();
/* OnlineAPI.callback: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.callback: <fusebox:set name="myFusebox['thisFuseaction']" value="callback"> */
$myFusebox['thisFuseaction'] = "callback";
/* OnlineAPI.callback: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.callback: <fusebox:set name="myFusebox['thisFuseaction']" value="callback"> */
$myFusebox['thisFuseaction'] = "callback";

?>