<?php

/* AdminBranding.edit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.edit: <fusebox:set name="myFusebox['thisFuseaction']" value="edit"> */
$myFusebox['thisFuseaction'] = "edit";
/* AdminBranding.edit: <fusebox:instantiate object="control" class="AdminBranding_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminBranding/AdminBranding_control.php");
$control = new AdminBranding_control;
/* AdminBranding.edit: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminBranding.edit: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminBranding.edit: <fusebox:invoke object="control" methodcall="brandingEdit()"> */
$control->brandingEdit();
/* AdminBranding.edit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.edit: <fusebox:set name="myFusebox['thisFuseaction']" value="edit"> */
$myFusebox['thisFuseaction'] = "edit";
/* AdminBranding.edit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.edit: <fusebox:set name="myFusebox['thisFuseaction']" value="edit"> */
$myFusebox['thisFuseaction'] = "edit";

?>