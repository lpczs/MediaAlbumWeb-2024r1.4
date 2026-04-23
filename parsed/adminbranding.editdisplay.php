<?php

/* AdminBranding.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";
/* AdminBranding.editDisplay: <fusebox:instantiate object="control" class="AdminBranding_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminBranding/AdminBranding_control.php");
$control = new AdminBranding_control;
/* AdminBranding.editDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminBranding.editDisplay: <fusebox:invoke object="control" methodcall="brandingEditDisplay()"> */
$control->brandingEditDisplay();
/* AdminBranding.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";
/* AdminBranding.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";

?>