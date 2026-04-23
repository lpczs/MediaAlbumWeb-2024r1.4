<?php

/* AdminProductPricing.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";
/* AdminProductPricing.editDisplay: <fusebox:instantiate object="control" class="AdminProductPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductPricing/AdminProductPricing_control.php");
$control = new AdminProductPricing_control;
/* AdminProductPricing.editDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminProductPricing.editDisplay: <fusebox:invoke object="control" methodcall="pricingEditDisplay()"> */
$control->pricingEditDisplay();
/* AdminProductPricing.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";
/* AdminProductPricing.editDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.editDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="editDisplay"> */
$myFusebox['thisFuseaction'] = "editDisplay";

?>