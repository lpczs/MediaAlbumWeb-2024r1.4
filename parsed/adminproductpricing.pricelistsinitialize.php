<?php

/* AdminProductPricing.priceListsInitialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.priceListsInitialize: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListsInitialize"> */
$myFusebox['thisFuseaction'] = "priceListsInitialize";
/* AdminProductPricing.priceListsInitialize: <fusebox:instantiate object="control" class="AdminProductPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductPricing/AdminProductPricing_control.php");
$control = new AdminProductPricing_control;
/* AdminProductPricing.priceListsInitialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminProductPricing.priceListsInitialize: <fusebox:invoke object="control" methodcall="priceListsInitialize()"> */
$control->priceListsInitialize();
/* AdminProductPricing.priceListsInitialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.priceListsInitialize: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListsInitialize"> */
$myFusebox['thisFuseaction'] = "priceListsInitialize";
/* AdminProductPricing.priceListsInitialize: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.priceListsInitialize: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListsInitialize"> */
$myFusebox['thisFuseaction'] = "priceListsInitialize";

?>