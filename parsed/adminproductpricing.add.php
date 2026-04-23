<?php

/* AdminProductPricing.add: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.add: <fusebox:set name="myFusebox['thisFuseaction']" value="add"> */
$myFusebox['thisFuseaction'] = "add";
/* AdminProductPricing.add: <fusebox:instantiate object="control" class="AdminProductPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductPricing/AdminProductPricing_control.php");
$control = new AdminProductPricing_control;
/* AdminProductPricing.add: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminProductPricing.add: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProductPricing.add: <fusebox:invoke object="control" methodcall="pricingAdd()"> */
$control->pricingAdd();
/* AdminProductPricing.add: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.add: <fusebox:set name="myFusebox['thisFuseaction']" value="add"> */
$myFusebox['thisFuseaction'] = "add";
/* AdminProductPricing.add: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.add: <fusebox:set name="myFusebox['thisFuseaction']" value="add"> */
$myFusebox['thisFuseaction'] = "add";

?>