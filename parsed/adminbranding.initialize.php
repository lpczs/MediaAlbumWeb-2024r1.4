<?php

/* AdminBranding.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminBranding.initialize: <fusebox:instantiate object="control" class="AdminBranding_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminBranding/AdminBranding_control.php");
$control = new AdminBranding_control;
/* AdminBranding.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminBranding.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* AdminBranding.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* AdminBranding.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>