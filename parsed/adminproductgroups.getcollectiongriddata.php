<?php

/* AdminProductGroups.getCollectionGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductGroups"> */
$myFusebox['thisCircuit'] = "AdminProductGroups";
/* AdminProductGroups.getCollectionGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getCollectionGridData"> */
$myFusebox['thisFuseaction'] = "getCollectionGridData";
/* AdminProductGroups.getCollectionGridData: <fusebox:instantiate object="control" class="AdminProductGroups_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductGroups/AdminProductGroups_control.php");
$control = new AdminProductGroups_control;
/* AdminProductGroups.getCollectionGridData: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminProductGroups.getCollectionGridData: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProductGroups.getCollectionGridData: <fusebox:invoke object="control" methodcall="getCollectionGridData()"> */
$control->getCollectionGridData();
/* AdminProductGroups.getCollectionGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductGroups"> */
$myFusebox['thisCircuit'] = "AdminProductGroups";
/* AdminProductGroups.getCollectionGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getCollectionGridData"> */
$myFusebox['thisFuseaction'] = "getCollectionGridData";
/* AdminProductGroups.getCollectionGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductGroups"> */
$myFusebox['thisCircuit'] = "AdminProductGroups";
/* AdminProductGroups.getCollectionGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getCollectionGridData"> */
$myFusebox['thisFuseaction'] = "getCollectionGridData";

?>