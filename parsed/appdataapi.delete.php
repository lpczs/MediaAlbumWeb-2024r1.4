<?php

/* AppDataAPI.Delete: <fusebox:set name="myFusebox['thisCircuit']" value="AppDataAPI"> */
$myFusebox['thisCircuit'] = "AppDataAPI";
/* AppDataAPI.Delete: <fusebox:set name="myFusebox['thisFuseaction']" value="Delete"> */
$myFusebox['thisFuseaction'] = "Delete";
/* AppDataAPI.Delete: <fusebox:instantiate object="control" class="AppDataAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AppDataAPI/AppDataAPI_control.php");
$control = new AppDataAPI_control;
/* AppDataAPI.Delete: <fusebox:invoke object="control" methodcall="delete()">  */
$control->delete();
/* AppDataAPI.Delete: <fusebox:set name="myFusebox['thisCircuit']" value="AppDataAPI"> */
$myFusebox['thisCircuit'] = "AppDataAPI";
/* AppDataAPI.Delete: <fusebox:set name="myFusebox['thisFuseaction']" value="Delete"> */
$myFusebox['thisFuseaction'] = "Delete";
/* AppDataAPI.Delete: <fusebox:set name="myFusebox['thisCircuit']" value="AppDataAPI"> */
$myFusebox['thisCircuit'] = "AppDataAPI";
/* AppDataAPI.Delete: <fusebox:set name="myFusebox['thisFuseaction']" value="Delete"> */
$myFusebox['thisFuseaction'] = "Delete";

?>