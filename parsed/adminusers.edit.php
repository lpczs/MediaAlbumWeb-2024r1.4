<?php

/* AdminUsers.edit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminUsers"> */
$myFusebox['thisCircuit'] = "AdminUsers";
/* AdminUsers.edit: <fusebox:set name="myFusebox['thisFuseaction']" value="edit"> */
$myFusebox['thisFuseaction'] = "edit";
/* AdminUsers.edit: <fusebox:instantiate object="control" class="AdminUsers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminUsers/AdminUsers_control.php");
$control = new AdminUsers_control;
/* AdminUsers.edit: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminUsers.edit: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminUsers.edit: <fusebox:invoke object="control" methodcall="userEdit()">  */
$control->userEdit();
/* AdminUsers.edit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminUsers"> */
$myFusebox['thisCircuit'] = "AdminUsers";
/* AdminUsers.edit: <fusebox:set name="myFusebox['thisFuseaction']" value="edit"> */
$myFusebox['thisFuseaction'] = "edit";
/* AdminUsers.edit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminUsers"> */
$myFusebox['thisCircuit'] = "AdminUsers";
/* AdminUsers.edit: <fusebox:set name="myFusebox['thisFuseaction']" value="edit"> */
$myFusebox['thisFuseaction'] = "edit";

?>