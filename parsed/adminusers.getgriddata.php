<?php

/* AdminUsers.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminUsers"> */
$myFusebox['thisCircuit'] = "AdminUsers";
/* AdminUsers.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminUsers.getGridData: <fusebox:instantiate object="control" class="AdminUsers_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminUsers/AdminUsers_control.php");
$control = new AdminUsers_control;
/* AdminUsers.getGridData: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminUsers.getGridData: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminUsers.getGridData: <fusebox:invoke object="control" methodcall="getGridData()"> */
$control->getGridData();
/* AdminUsers.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminUsers"> */
$myFusebox['thisCircuit'] = "AdminUsers";
/* AdminUsers.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminUsers.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminUsers"> */
$myFusebox['thisCircuit'] = "AdminUsers";
/* AdminUsers.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";

?>