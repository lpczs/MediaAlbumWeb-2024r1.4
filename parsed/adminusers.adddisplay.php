<?php

/* AdminUsers.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminUsers"> */
$myFusebox['thisCircuit'] = "AdminUsers";
/* AdminUsers.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";
/* AdminUsers.addDisplay: <fusebox:instantiate object="control" class="AdminUsers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminUsers/AdminUsers_control.php");
$control = new AdminUsers_control;
/* AdminUsers.addDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminUsers.addDisplay: <fusebox:invoke object="control" methodcall="userAddDisplay()"> */
$control->userAddDisplay();
/* AdminUsers.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminUsers"> */
$myFusebox['thisCircuit'] = "AdminUsers";
/* AdminUsers.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";
/* AdminUsers.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminUsers"> */
$myFusebox['thisCircuit'] = "AdminUsers";
/* AdminUsers.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";

?>