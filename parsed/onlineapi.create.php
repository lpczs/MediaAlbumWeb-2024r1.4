<?php

/* OnlineAPI.create: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.create: <fusebox:set name="myFusebox['thisFuseaction']" value="create"> */
$myFusebox['thisFuseaction'] = "create";
/* OnlineAPI.create: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.create: <fusebox:invoke object="control" methodcall="create()">   */
$control->create();
/* OnlineAPI.create: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.create: <fusebox:set name="myFusebox['thisFuseaction']" value="create"> */
$myFusebox['thisFuseaction'] = "create";
/* OnlineAPI.create: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.create: <fusebox:set name="myFusebox['thisFuseaction']" value="create"> */
$myFusebox['thisFuseaction'] = "create";

?>