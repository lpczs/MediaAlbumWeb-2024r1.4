<?php

/* AppDataAPI.Insert: <fusebox:set name="myFusebox['thisCircuit']" value="AppDataAPI"> */
$myFusebox['thisCircuit'] = "AppDataAPI";
/* AppDataAPI.Insert: <fusebox:set name="myFusebox['thisFuseaction']" value="Insert"> */
$myFusebox['thisFuseaction'] = "Insert";
/* AppDataAPI.Insert: <fusebox:instantiate object="control" class="AppDataAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AppDataAPI/AppDataAPI_control.php");
$control = new AppDataAPI_control;
/* AppDataAPI.Insert: <fusebox:invoke object="control" methodcall="insert()">  */
$control->insert();
/* AppDataAPI.Insert: <fusebox:set name="myFusebox['thisCircuit']" value="AppDataAPI"> */
$myFusebox['thisCircuit'] = "AppDataAPI";
/* AppDataAPI.Insert: <fusebox:set name="myFusebox['thisFuseaction']" value="Insert"> */
$myFusebox['thisFuseaction'] = "Insert";
/* AppDataAPI.Insert: <fusebox:set name="myFusebox['thisCircuit']" value="AppDataAPI"> */
$myFusebox['thisCircuit'] = "AppDataAPI";
/* AppDataAPI.Insert: <fusebox:set name="myFusebox['thisFuseaction']" value="Insert"> */
$myFusebox['thisFuseaction'] = "Insert";

?>