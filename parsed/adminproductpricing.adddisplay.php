<?php

/* AdminProductPricing.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";
/* AdminProductPricing.addDisplay: <fusebox:instantiate object="control" class="AdminProductPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductPricing/AdminProductPricing_control.php");
$control = new AdminProductPricing_control;
/* AdminProductPricing.addDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminProductPricing.addDisplay: <fusebox:invoke object="control" methodcall="pricingAddDisplay()"> */
$control->pricingAddDisplay();
/* AdminProductPricing.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";
/* AdminProductPricing.addDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.addDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="addDisplay"> */
$myFusebox['thisFuseaction'] = "addDisplay";

?>