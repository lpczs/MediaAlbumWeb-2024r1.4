<?php

/* AdminProductPricing.priceListAddDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.priceListAddDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListAddDisplay"> */
$myFusebox['thisFuseaction'] = "priceListAddDisplay";
/* AdminProductPricing.priceListAddDisplay: <fusebox:instantiate object="control" class="AdminProductPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductPricing/AdminProductPricing_control.php");
$control = new AdminProductPricing_control;
/* AdminProductPricing.priceListAddDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminProductPricing.priceListAddDisplay: <fusebox:invoke object="control" methodcall="priceListAddDisplay()"> */
$control->priceListAddDisplay();
/* AdminProductPricing.priceListAddDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.priceListAddDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListAddDisplay"> */
$myFusebox['thisFuseaction'] = "priceListAddDisplay";
/* AdminProductPricing.priceListAddDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.priceListAddDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListAddDisplay"> */
$myFusebox['thisFuseaction'] = "priceListAddDisplay";

?>