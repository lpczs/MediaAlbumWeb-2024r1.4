<?php

/* AdminProductGroups.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductGroups"> */
$myFusebox['thisCircuit'] = "AdminProductGroups";
/* AdminProductGroups.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";
/* AdminProductGroups.editDisplay: <fusebox:instantiate object="control" class="AdminProductGroups_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductGroups/AdminProductGroups_control.php");
$control = new AdminProductGroups_control;
/* AdminProductGroups.editDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminProductGroups.editDisplay: <fusebox:invoke object="control" methodcall="editDisplay()"> */
$control->editDisplay();
/* AdminProductGroups.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductGroups"> */
$myFusebox['thisCircuit'] = "AdminProductGroups";
/* AdminProductGroups.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";
/* AdminProductGroups.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductGroups"> */
$myFusebox['thisCircuit'] = "AdminProductGroups";
/* AdminProductGroups.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";

?>