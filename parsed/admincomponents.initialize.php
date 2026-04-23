<?php

/* AdminComponents.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponents"> */
$myFusebox['thisCircuit'] = "AdminComponents";
/* AdminComponents.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminComponents.initialize: <fusebox:instantiate object="control" class="AdminComponents_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminComponents/AdminComponents_control.php");
$control = new AdminComponents_control;
/* AdminComponents.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminComponents.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminComponents.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponents"> */
$myFusebox['thisCircuit'] = "AdminComponents";
/* AdminComponents.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminComponents.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponents"> */
$myFusebox['thisCircuit'] = "AdminComponents";
/* AdminComponents.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>