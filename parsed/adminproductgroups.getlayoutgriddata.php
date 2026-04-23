<?php

/* AdminProductGroups.getLayoutGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductGroups"> */
$myFusebox['thisCircuit'] = "AdminProductGroups";
/* AdminProductGroups.getLayoutGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getLayoutGridData"> */
$myFusebox['thisFuseaction'] = "getLayoutGridData";
/* AdminProductGroups.getLayoutGridData: <fusebox:instantiate object="control" class="AdminProductGroups_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductGroups/AdminProductGroups_control.php");
$control = new AdminProductGroups_control;
/* AdminProductGroups.getLayoutGridData: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminProductGroups.getLayoutGridData: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProductGroups.getLayoutGridData: <fusebox:invoke object="control" methodcall="getLayoutGridData()"> */
$control->getLayoutGridData();
/* AdminProductGroups.getLayoutGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductGroups"> */
$myFusebox['thisCircuit'] = "AdminProductGroups";
/* AdminProductGroups.getLayoutGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getLayoutGridData"> */
$myFusebox['thisFuseaction'] = "getLayoutGridData";
/* AdminProductGroups.getLayoutGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductGroups"> */
$myFusebox['thisCircuit'] = "AdminProductGroups";
/* AdminProductGroups.getLayoutGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getLayoutGridData"> */
$myFusebox['thisFuseaction'] = "getLayoutGridData";

?>