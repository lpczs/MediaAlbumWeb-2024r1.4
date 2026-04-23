<?php

/* AdminComponents.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponents"> */
$myFusebox['thisCircuit'] = "AdminComponents";
/* AdminComponents.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminComponents.getGridData: <fusebox:instantiate object="control" class="AdminComponents_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminComponents/AdminComponents_control.php");
$control = new AdminComponents_control;
/* AdminComponents.getGridData: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminComponents.getGridData: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminComponents.getGridData: <fusebox:invoke object="control" methodcall="getGridData()"> */
$control->getGridData();
/* AdminComponents.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponents"> */
$myFusebox['thisCircuit'] = "AdminComponents";
/* AdminComponents.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";
/* AdminComponents.getGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponents"> */
$myFusebox['thisCircuit'] = "AdminComponents";
/* AdminComponents.getGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getGridData"> */
$myFusebox['thisFuseaction'] = "getGridData";

?>