<?php

/* AdminAutoUpdate.initializeBackgrounds: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeBackgrounds: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeBackgrounds"> */
$myFusebox['thisFuseaction'] = "initializeBackgrounds";
/* AdminAutoUpdate.initializeBackgrounds: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.initializeBackgrounds: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminAutoUpdate.initializeBackgrounds: <fusebox:invoke object="control" methodcall="initializeBackgrounds()"> */
$control->initializeBackgrounds();
/* AdminAutoUpdate.initializeBackgrounds: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeBackgrounds: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeBackgrounds"> */
$myFusebox['thisFuseaction'] = "initializeBackgrounds";
/* AdminAutoUpdate.initializeBackgrounds: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeBackgrounds: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeBackgrounds"> */
$myFusebox['thisFuseaction'] = "initializeBackgrounds";

?>