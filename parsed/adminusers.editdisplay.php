<?php

/* AdminUsers.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminUsers"> */
$myFusebox['thisCircuit'] = "AdminUsers";
/* AdminUsers.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";
/* AdminUsers.editDisplay: <fusebox:instantiate object="control" class="AdminUsers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminUsers/AdminUsers_control.php");
$control = new AdminUsers_control;
/* AdminUsers.editDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminUsers.editDisplay: <fusebox:invoke object="control" methodcall="userEditDisplay()"> */
$control->userEditDisplay();
/* AdminUsers.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminUsers"> */
$myFusebox['thisCircuit'] = "AdminUsers";
/* AdminUsers.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";
/* AdminUsers.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminUsers"> */
$myFusebox['thisCircuit'] = "AdminUsers";
/* AdminUsers.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";

?>