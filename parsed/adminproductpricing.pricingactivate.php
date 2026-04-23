<?php

/* AdminProductPricing.pricingActivate: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.pricingActivate: <fusebox:set name="myFusebox['thisFuseaction']" value="pricingActivate"> */
$myFusebox['thisFuseaction'] = "pricingActivate";
/* AdminProductPricing.pricingActivate: <fusebox:instantiate object="control" class="AdminProductPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductPricing/AdminProductPricing_control.php");
$control = new AdminProductPricing_control;
/* AdminProductPricing.pricingActivate: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminProductPricing.pricingActivate: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProductPricing.pricingActivate: <fusebox:invoke object="control" methodcall="pricingActivate()"> */
$control->pricingActivate();
/* AdminProductPricing.pricingActivate: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.pricingActivate: <fusebox:set name="myFusebox['thisFuseaction']" value="pricingActivate"> */
$myFusebox['thisFuseaction'] = "pricingActivate";
/* AdminProductPricing.pricingActivate: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.pricingActivate: <fusebox:set name="myFusebox['thisFuseaction']" value="pricingActivate"> */
$myFusebox['thisFuseaction'] = "pricingActivate";

?>