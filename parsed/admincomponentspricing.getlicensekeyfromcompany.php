<?php

/* AdminComponentsPricing.getLicenseKeyFromCompany: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.getLicenseKeyFromCompany: <fusebox:set name="myFusebox['thisFuseaction']" value="getLicenseKeyFromCompany"> */
$myFusebox['thisFuseaction'] = "getLicenseKeyFromCompany";
/* AdminComponentsPricing.getLicenseKeyFromCompany: <fusebox:instantiate object="control" class="AdminComponentsPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminComponentsPricing/AdminComponentsPricing_control.php");
$control = new AdminComponentsPricing_control;
/* AdminComponentsPricing.getLicenseKeyFromCompany: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminComponentsPricing.getLicenseKeyFromCompany: <fusebox:invoke object="control" methodcall="getLicenseKeyFromCompany()"> */
$control->getLicenseKeyFromCompany();
/* AdminComponentsPricing.getLicenseKeyFromCompany: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.getLicenseKeyFromCompany: <fusebox:set name="myFusebox['thisFuseaction']" value="getLicenseKeyFromCompany"> */
$myFusebox['thisFuseaction'] = "getLicenseKeyFromCompany";
/* AdminComponentsPricing.getLicenseKeyFromCompany: <fusebox:set name="myFusebox['thisCircuit']" value="AdminComponentsPricing"> */
$myFusebox['thisCircuit'] = "AdminComponentsPricing";
/* AdminComponentsPricing.getLicenseKeyFromCompany: <fusebox:set name="myFusebox['thisFuseaction']" value="getLicenseKeyFromCompany"> */
$myFusebox['thisFuseaction'] = "getLicenseKeyFromCompany";

?>