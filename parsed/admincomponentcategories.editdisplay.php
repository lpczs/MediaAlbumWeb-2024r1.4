<?php

/* AdminComponentCategories.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentCategories"> */
$myFusebox['thisCircuit'] = "AdminComponentCategories";
/* AdminComponentCategories.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";
/* AdminComponentCategories.editDisplay: <fusebox:instantiate object="control" class="AdminComponentCategories_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminComponentCategories/AdminComponentCategories_control.php");
$control = new AdminComponentCategories_control;
/* AdminComponentCategories.editDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminComponentCategories.editDisplay: <fusebox:invoke object="control" methodcall="componentCategoriesEditDisplay()"> */
$control->componentCategoriesEditDisplay();
/* AdminComponentCategories.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentCategories"> */
$myFusebox['thisCircuit'] = "AdminComponentCategories";
/* AdminComponentCategories.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";
/* AdminComponentCategories.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentCategories"> */
$myFusebox['thisCircuit'] = "AdminComponentCategories";
/* AdminComponentCategories.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";

?>