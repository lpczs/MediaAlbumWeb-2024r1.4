<?php

/* AdminBranding.list: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.list: <fusebox:set name="myFusebox['thisFuseaction']" value="list"> */
$myFusebox['thisFuseaction'] = "list";
/* AdminBranding.list: <fusebox:instantiate object="control" class="AdminBranding_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminBranding/AdminBranding_control.php");
$control = new AdminBranding_control;
/* AdminBranding.list: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminBranding.list: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminBranding.list: <fusebox:invoke object="control" methodcall="brandList()"> */
$control->brandList();
/* AdminBranding.list: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.list: <fusebox:set name="myFusebox['thisFuseaction']" value="list"> */
$myFusebox['thisFuseaction'] = "list";
/* AdminBranding.list: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.list: <fusebox:set name="myFusebox['thisFuseaction']" value="list"> */
$myFusebox['thisFuseaction'] = "list";

?>