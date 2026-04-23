<?php

/* AdminBranding.getAuthentication: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.getAuthentication: <fusebox:set name="myFusebox['thisFuseaction']" value="getAuthentication"> */
$myFusebox['thisFuseaction'] = "getAuthentication";
/* AdminBranding.getAuthentication: <fusebox:instantiate object="control" class="AdminBranding_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminBranding/AdminBranding_control.php");
$control = new AdminBranding_control;
/* AdminBranding.getAuthentication: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminBranding.getAuthentication: <fusebox:invoke object="control" methodcall="getAuthentication()"> */
$control->getAuthentication();
/* AdminBranding.getAuthentication: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.getAuthentication: <fusebox:set name="myFusebox['thisFuseaction']" value="getAuthentication"> */
$myFusebox['thisFuseaction'] = "getAuthentication";
/* AdminBranding.getAuthentication: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.getAuthentication: <fusebox:set name="myFusebox['thisFuseaction']" value="getAuthentication"> */
$myFusebox['thisFuseaction'] = "getAuthentication";

?>