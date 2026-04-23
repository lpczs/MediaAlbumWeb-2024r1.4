<?php

/* AdminUsers.add: <fusebox:set name="myFusebox['thisCircuit']" value="AdminUsers"> */
$myFusebox['thisCircuit'] = "AdminUsers";
/* AdminUsers.add: <fusebox:set name="myFusebox['thisFuseaction']" value="add"> */
$myFusebox['thisFuseaction'] = "add";
/* AdminUsers.add: <fusebox:instantiate object="control" class="AdminUsers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminUsers/AdminUsers_control.php");
$control = new AdminUsers_control;
/* AdminUsers.add: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminUsers.add: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminUsers.add: <fusebox:invoke object="control" methodcall="userAdd()">    */
$control->userAdd();
/* AdminUsers.add: <fusebox:set name="myFusebox['thisCircuit']" value="AdminUsers"> */
$myFusebox['thisCircuit'] = "AdminUsers";
/* AdminUsers.add: <fusebox:set name="myFusebox['thisFuseaction']" value="add"> */
$myFusebox['thisFuseaction'] = "add";
/* AdminUsers.add: <fusebox:set name="myFusebox['thisCircuit']" value="AdminUsers"> */
$myFusebox['thisCircuit'] = "AdminUsers";
/* AdminUsers.add: <fusebox:set name="myFusebox['thisFuseaction']" value="add"> */
$myFusebox['thisFuseaction'] = "add";

?>