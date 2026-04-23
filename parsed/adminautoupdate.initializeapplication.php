<?php

/* AdminAutoUpdate.initializeApplication: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeApplication: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeApplication"> */
$myFusebox['thisFuseaction'] = "initializeApplication";
/* AdminAutoUpdate.initializeApplication: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.initializeApplication: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminAutoUpdate.initializeApplication: <fusebox:invoke object="control" methodcall="initializeApplication()"> */
$control->initializeApplication();
/* AdminAutoUpdate.initializeApplication: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeApplication: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeApplication"> */
$myFusebox['thisFuseaction'] = "initializeApplication";
/* AdminAutoUpdate.initializeApplication: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeApplication: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeApplication"> */
$myFusebox['thisFuseaction'] = "initializeApplication";

?>