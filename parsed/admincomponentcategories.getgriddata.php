<?php

/* AdminComponentCategories.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentCategories"> */
$myFusebox['thisCircuit'] = "AdminComponentCategories";
/* AdminComponentCategories.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminComponentCategories.getGridData: <fusebox:instantiate object="control" class="AdminComponentCategories_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminComponentCategories/AdminComponentCategories_control.php");
$control = new AdminComponentCategories_control;
/* AdminComponentCategories.getGridData: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminComponentCategories.getGridData: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminComponentCategories.getGridData: <fusebox:invoke object="control" methodcall="getGridData()"> */
$control->getGridData();
/* AdminComponentCategories.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentCategories"> */
$myFusebox['thisCircuit'] = "AdminComponentCategories";
/* AdminComponentCategories.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminComponentCategories.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentCategories"> */
$myFusebox['thisCircuit'] = "AdminComponentCategories";
/* AdminComponentCategories.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";

?>