<?php

/* AdminProductGroups.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductGroups"> */
$myFusebox['thisCircuit'] = "AdminProductGroups";
/* AdminProductGroups.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminProductGroups.getGridData: <fusebox:instantiate object="control" class="AdminProductGroups_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductGroups/AdminProductGroups_control.php");
$control = new AdminProductGroups_control;
/* AdminProductGroups.getGridData: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminProductGroups.getGridData: <fusebox:invoke object="control" methodcall="getGridData()"> */
$control->getGridData();
/* AdminProductGroups.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductGroups"> */
$myFusebox['thisCircuit'] = "AdminProductGroups";
/* AdminProductGroups.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminProductGroups.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductGroups"> */
$myFusebox['thisCircuit'] = "AdminProductGroups";
/* AdminProductGroups.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";

?>