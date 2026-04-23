<?php

/* AdminProductPricing.edit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.edit: <fusebox:set name="myFusebox['thisFuseaction']" value="edit"> */
$myFusebox['thisFuseaction'] = "edit";
/* AdminProductPricing.edit: <fusebox:instantiate object="control" class="AdminProductPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductPricing/AdminProductPricing_control.php");
$control = new AdminProductPricing_control;
/* AdminProductPricing.edit: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminProductPricing.edit: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProductPricing.edit: <fusebox:invoke object="control" methodcall="pricingEdit()"> */
$control->pricingEdit();
/* AdminProductPricing.edit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.edit: <fusebox:set name="myFusebox['thisFuseaction']" value="edit"> */
$myFusebox['thisFuseaction'] = "edit";
/* AdminProductPricing.edit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.edit: <fusebox:set name="myFusebox['thisFuseaction']" value="edit"> */
$myFusebox['thisFuseaction'] = "edit";

?>