<?php

/* AppDataAPI.Query: <fusebox:set name="myFusebox['thisCircuit']" value="AppDataAPI"> */
$myFusebox['thisCircuit'] = "AppDataAPI";
/* AppDataAPI.Query: <fusebox:set name="myFusebox['thisFuseaction']" value="Query"> */
$myFusebox['thisFuseaction'] = "Query";
/* AppDataAPI.Query: <fusebox:instantiate object="control" class="AppDataAPI_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AppDataAPI/AppDataAPI_control.php");
$control = new AppDataAPI_control;
/* AppDataAPI.Query: <fusebox:invoke object="control" methodcall="query()">    */
$control->query();
/* AppDataAPI.Query: <fusebox:set name="myFusebox['thisCircuit']" value="AppDataAPI"> */
$myFusebox['thisCircuit'] = "AppDataAPI";
/* AppDataAPI.Query: <fusebox:set name="myFusebox['thisFuseaction']" value="Query"> */
$myFusebox['thisFuseaction'] = "Query";
/* AppDataAPI.Query: <fusebox:set name="myFusebox['thisCircuit']" value="AppDataAPI"> */
$myFusebox['thisCircuit'] = "AppDataAPI";
/* AppDataAPI.Query: <fusebox:set name="myFusebox['thisFuseaction']" value="Query"> */
$myFusebox['thisFuseaction'] = "Query";

?>