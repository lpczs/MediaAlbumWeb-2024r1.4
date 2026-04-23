<?php

/* AdminProduction.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProduction"> */
$myFusebox['thisCircuit'] = "AdminProduction";
/* AdminProduction.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminProduction.initialize: <fusebox:instantiate object="control" class="AdminProduction_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProduction/AdminProduction_control.php");
$control = new AdminProduction_control;
/* AdminProduction.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminProduction.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProduction"> */
$myFusebox['thisCircuit'] = "AdminProduction";
/* AdminProduction.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminProduction.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProduction"> */
$myFusebox['thisCircuit'] = "AdminProduction";
/* AdminProduction.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>