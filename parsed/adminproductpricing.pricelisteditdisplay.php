<?php

/* AdminProductPricing.priceListEditDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.priceListEditDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListEditDisplay"> */
$myFusebox['thisFuseaction'] = "priceListEditDisplay";
/* AdminProductPricing.priceListEditDisplay: <fusebox:instantiate object="control" class="AdminProductPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductPricing/AdminProductPricing_control.php");
$control = new AdminProductPricing_control;
/* AdminProductPricing.priceListEditDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminProductPricing.priceListEditDisplay: <fusebox:invoke object="control" methodcall="priceListEditDisplay()"> */
$control->priceListEditDisplay();
/* AdminProductPricing.priceListEditDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.priceListEditDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListEditDisplay"> */
$myFusebox['thisFuseaction'] = "priceListEditDisplay";
/* AdminProductPricing.priceListEditDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.priceListEditDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListEditDisplay"> */
$myFusebox['thisFuseaction'] = "priceListEditDisplay";

?>