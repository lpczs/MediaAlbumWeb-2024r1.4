<?php

/* AppDataAPI.Update: <fusebox:set name="myFusebox['thisCircuit']" value="AppDataAPI"> */
$myFusebox['thisCircuit'] = "AppDataAPI";
/* AppDataAPI.Update: <fusebox:set name="myFusebox['thisFuseaction']" value="Update"> */
$myFusebox['thisFuseaction'] = "Update";
/* AppDataAPI.Update: <fusebox:instantiate object="control" class="AppDataAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AppDataAPI/AppDataAPI_control.php");
$control = new AppDataAPI_control;
/* AppDataAPI.Update: <fusebox:invoke object="control" methodcall="update()">  */
$control->update();
/* AppDataAPI.Update: <fusebox:set name="myFusebox['thisCircuit']" value="AppDataAPI"> */
$myFusebox['thisCircuit'] = "AppDataAPI";
/* AppDataAPI.Update: <fusebox:set name="myFusebox['thisFuseaction']" value="Update"> */
$myFusebox['thisFuseaction'] = "Update";
/* AppDataAPI.Update: <fusebox:set name="myFusebox['thisCircuit']" value="AppDataAPI"> */
$myFusebox['thisCircuit'] = "AppDataAPI";
/* AppDataAPI.Update: <fusebox:set name="myFusebox['thisFuseaction']" value="Update"> */
$myFusebox['thisFuseaction'] = "Update";

?>