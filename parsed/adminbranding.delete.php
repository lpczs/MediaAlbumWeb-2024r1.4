<?php

/* AdminBranding.delete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.delete: <fusebox:set name="myFusebox['thisFuseaction']" value="delete"> */
$myFusebox['thisFuseaction'] = "delete";
/* AdminBranding.delete: <fusebox:instantiate object="control" class="AdminBranding_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminBranding/AdminBranding_control.php");
$control = new AdminBranding_control;
/* AdminBranding.delete: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminBranding.delete: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminBranding.delete: <fusebox:invoke object="control" methodcall="brandingDelete()"> */
$control->brandingDelete();
/* AdminBranding.delete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.delete: <fusebox:set name="myFusebox['thisFuseaction']" value="delete"> */
$myFusebox['thisFuseaction'] = "delete";
/* AdminBranding.delete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.delete: <fusebox:set name="myFusebox['thisFuseaction']" value="delete"> */
$myFusebox['thisFuseaction'] = "delete";

?>