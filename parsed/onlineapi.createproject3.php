<?php

/* OnlineAPI.createProject3: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.createProject3: <fusebox:set name="myFusebox['thisFuseaction']" value="createProject3"> */
$myFusebox['thisFuseaction'] = "createProject3";
/* OnlineAPI.createProject3: <fusebox:instantiate object="control" class="OnlineAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."OnlineAPI/OnlineAPI_control.php");
$control = new OnlineAPI_control;
/* OnlineAPI.createProject3: <fusebox:invoke object="control" methodcall="create3()"> */
$control->create3();
/* OnlineAPI.createProject3: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.createProject3: <fusebox:set name="myFusebox['thisFuseaction']" value="createProject3"> */
$myFusebox['thisFuseaction'] = "createProject3";
/* OnlineAPI.createProject3: <fusebox:set name="myFusebox['thisCircuit']" value="OnlineAPI"> */
$myFusebox['thisCircuit'] = "OnlineAPI";
/* OnlineAPI.createProject3: <fusebox:set name="myFusebox['thisFuseaction']" value="createProject3"> */
$myFusebox['thisFuseaction'] = "createProject3";

?>