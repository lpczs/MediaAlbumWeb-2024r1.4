<?php

/* AdminProductPricing.getLicenseKeyFromCompany: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.getLicenseKeyFromCompany: <fusebox:set name="myFusebox['thisFuseaction']" value="getLicenseKeyFromCompany"> */
$myFusebox['thisFuseaction'] = "getLicenseKeyFromCompany";
/* AdminProductPricing.getLicenseKeyFromCompany: <fusebox:instantiate object="control" class="AdminProductPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductPricing/AdminProductPricing_control.php");
$control = new AdminProductPricing_control;
/* AdminProductPricing.getLicenseKeyFromCompany: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminProductPricing.getLicenseKeyFromCompany: <fusebox:invoke object="control" methodcall="getLicenseKeyFromCompany()"> */
$control->getLicenseKeyFromCompany();
/* AdminProductPricing.getLicenseKeyFromCompany: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.getLicenseKeyFromCompany: <fusebox:set name="myFusebox['thisFuseaction']" value="getLicenseKeyFromCompany"> */
$myFusebox['thisFuseaction'] = "getLicenseKeyFromCompany";
/* AdminProductPricing.getLicenseKeyFromCompany: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.getLicenseKeyFromCompany: <fusebox:set name="myFusebox['thisFuseaction']" value="getLicenseKeyFromCompany"> */
$myFusebox['thisFuseaction'] = "getLicenseKeyFromCompany";

?>