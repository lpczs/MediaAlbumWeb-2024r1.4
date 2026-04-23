<?php

/* AppDataAPI.Authenticate: <fusebox:set name="myFusebox['thisCircuit']" value="AppDataAPI"> */
$myFusebox['thisCircuit'] = "AppDataAPI";
/* AppDataAPI.Authenticate: <fusebox:set name="myFusebox['thisFuseaction']" value="Authenticate"> */
$myFusebox['thisFuseaction'] = "Authenticate";
/* AppDataAPI.Authenticate: <fusebox:instantiate object="control" class="AppDataAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AppDataAPI/AppDataAPI_control.php");
$control = new AppDataAPI_control;
/* AppDataAPI.Authenticate: <fusebox:invoke object="control" methodcall="login()"> */
$control->login();
/* AppDataAPI.Authenticate: <fusebox:set name="myFusebox['thisCircuit']" value="AppDataAPI"> */
$myFusebox['thisCircuit'] = "AppDataAPI";
/* AppDataAPI.Authenticate: <fusebox:set name="myFusebox['thisFuseaction']" value="Authenticate"> */
$myFusebox['thisFuseaction'] = "Authenticate";
/* AppDataAPI.Authenticate: <fusebox:set name="myFusebox['thisCircuit']" value="AppDataAPI"> */
$myFusebox['thisCircuit'] = "AppDataAPI";
/* AppDataAPI.Authenticate: <fusebox:set name="myFusebox['thisFuseaction']" value="Authenticate"> */
$myFusebox['thisFuseaction'] = "Authenticate";

?>