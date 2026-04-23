<?php

/* AdminProductGroups.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductGroups"> */
$myFusebox['thisCircuit'] = "AdminProductGroups";
/* AdminProductGroups.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminProductGroups.initialize: <fusebox:instantiate object="control" class="AdminProductGroups_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductGroups/AdminProductGroups_control.php");
$control = new AdminProductGroups_control;
/* AdminProductGroups.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminProductGroups.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminProductGroups.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductGroups"> */
$myFusebox['thisCircuit'] = "AdminProductGroups";
/* AdminProductGroups.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminProductGroups.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductGroups"> */
$myFusebox['thisCircuit'] = "AdminProductGroups";
/* AdminProductGroups.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>