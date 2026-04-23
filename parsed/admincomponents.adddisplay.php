<?php

/* AdminComponents.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponents"> */
$myFusebox['thisCircuit'] = "AdminComponents";
/* AdminComponents.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";
/* AdminComponents.addDisplay: <fusebox:instantiate object="control" class="AdminComponents_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminComponents/AdminComponents_control.php");
$control = new AdminComponents_control;
/* AdminComponents.addDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminComponents.addDisplay: <fusebox:invoke object="control" methodcall="componentTypesAddDisplay()"> */
$control->componentTypesAddDisplay();
/* AdminComponents.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponents"> */
$myFusebox['thisCircuit'] = "AdminComponents";
/* AdminComponents.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";
/* AdminComponents.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponents"> */
$myFusebox['thisCircuit'] = "AdminComponents";
/* AdminComponents.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";

?>