<?php

/* AdminAutoUpdate.initializeFrames: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeFrames: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeFrames"> */
$myFusebox['thisFuseaction'] = "initializeFrames";
/* AdminAutoUpdate.initializeFrames: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.initializeFrames: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminAutoUpdate.initializeFrames: <fusebox:invoke object="control" methodcall="initializeFrames()"> */
$control->initializeFrames();
/* AdminAutoUpdate.initializeFrames: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeFrames: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeFrames"> */
$myFusebox['thisFuseaction'] = "initializeFrames";
/* AdminAutoUpdate.initializeFrames: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeFrames: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeFrames"> */
$myFusebox['thisFuseaction'] = "initializeFrames";

?>