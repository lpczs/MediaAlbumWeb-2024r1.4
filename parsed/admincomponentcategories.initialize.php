<?php

/* AdminComponentCategories.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentCategories"> */
$myFusebox['thisCircuit'] = "AdminComponentCategories";
/* AdminComponentCategories.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminComponentCategories.initialize: <fusebox:instantiate object="control" class="AdminComponentCategories_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminComponentCategories/AdminComponentCategories_control.php");
$control = new AdminComponentCategories_control;
/* AdminComponentCategories.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminComponentCategories.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminComponentCategories.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentCategories"> */
$myFusebox['thisCircuit'] = "AdminComponentCategories";
/* AdminComponentCategories.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminComponentCategories.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentCategories"> */
$myFusebox['thisCircuit'] = "AdminComponentCategories";
/* AdminComponentCategories.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>