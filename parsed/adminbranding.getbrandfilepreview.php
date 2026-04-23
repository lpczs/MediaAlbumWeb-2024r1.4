<?php

/* AdminBranding.getBrandFilePreview: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.getBrandFilePreview: <fusebox:set name="myFusebox['thisFuseaction']" value="getBrandFilePreview"> */
$myFusebox['thisFuseaction'] = "getBrandFilePreview";
/* AdminBranding.getBrandFilePreview: <fusebox:instantiate object="control" class="AdminBranding_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminBranding/AdminBranding_control.php");
$control = new AdminBranding_control;
/* AdminBranding.getBrandFilePreview: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminBranding.getBrandFilePreview: <fusebox:invoke object="control" methodcall="getBrandFilePreview()"> */
$control->getBrandFilePreview();
/* AdminBranding.getBrandFilePreview: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.getBrandFilePreview: <fusebox:set name="myFusebox['thisFuseaction']" value="getBrandFilePreview"> */
$myFusebox['thisFuseaction'] = "getBrandFilePreview";
/* AdminBranding.getBrandFilePreview: <fusebox:set name="myFusebox['thisCircuit']" value="AdminBranding"> */
$myFusebox['thisCircuit'] = "AdminBranding";
/* AdminBranding.getBrandFilePreview: <fusebox:set name="myFusebox['thisFuseaction']" value="getBrandFilePreview"> */
$myFusebox['thisFuseaction'] = "getBrandFilePreview";

?>