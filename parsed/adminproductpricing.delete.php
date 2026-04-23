<?php

/* AdminProductPricing.delete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.delete: <fusebox:set name="myFusebox['thisFuseaction']" value="delete"> */
$myFusebox['thisFuseaction'] = "delete";
/* AdminProductPricing.delete: <fusebox:instantiate object="control" class="AdminProductPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductPricing/AdminProductPricing_control.php");
$control = new AdminProductPricing_control;
/* AdminProductPricing.delete: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminProductPricing.delete: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProductPricing.delete: <fusebox:invoke object="control" methodcall="pricingDelete()"> */
$control->pricingDelete();
/* AdminProductPricing.delete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.delete: <fusebox:set name="myFusebox['thisFuseaction']" value="delete"> */
$myFusebox['thisFuseaction'] = "delete";
/* AdminProductPricing.delete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.delete: <fusebox:set name="myFusebox['thisFuseaction']" value="delete"> */
$myFusebox['thisFuseaction'] = "delete";

?>