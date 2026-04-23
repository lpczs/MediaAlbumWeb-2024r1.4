<?php

/* AdminComponents.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponents"> */
$myFusebox['thisCircuit'] = "AdminComponents";
/* AdminComponents.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";
/* AdminComponents.editDisplay: <fusebox:instantiate object="control" class="AdminComponents_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminComponents/AdminComponents_control.php");
$control = new AdminComponents_control;
/* AdminComponents.editDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminComponents.editDisplay: <fusebox:invoke object="control" methodcall="componentsEditDisplay()"> */
$control->componentsEditDisplay();
/* AdminComponents.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponents"> */
$myFusebox['thisCircuit'] = "AdminComponents";
/* AdminComponents.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";
/* AdminComponents.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponents"> */
$myFusebox['thisCircuit'] = "AdminComponents";
/* AdminComponents.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";

?>