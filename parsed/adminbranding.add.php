<?php

/* AdminBranding.add: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.add: <fusebox:set name="myFusebox['thisFuseaction']" value="add"> */
$myFusebox['thisFuseaction'] = "add";
/* AdminBranding.add: <fusebox:instantiate object="control" class="AdminBranding_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminBranding/AdminBranding_control.php");
$control = new AdminBranding_control;
/* AdminBranding.add: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminBranding.add: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminBranding.add: <fusebox:invoke object="control" methodcall="brandingAdd()"> */
$control->brandingAdd();
/* AdminBranding.add: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.add: <fusebox:set name="myFusebox['thisFuseaction']" value="add"> */
$myFusebox['thisFuseaction'] = "add";
/* AdminBranding.add: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.add: <fusebox:set name="myFusebox['thisFuseaction']" value="add"> */
$myFusebox['thisFuseaction'] = "add";

?>