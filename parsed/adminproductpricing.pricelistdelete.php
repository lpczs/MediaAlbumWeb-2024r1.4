<?php

/* AdminProductPricing.priceListDelete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.priceListDelete: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListDelete"> */
$myFusebox['thisFuseaction'] = "priceListDelete";
/* AdminProductPricing.priceListDelete: <fusebox:instantiate object="control" class="AdminProductPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductPricing/AdminProductPricing_control.php");
$control = new AdminProductPricing_control;
/* AdminProductPricing.priceListDelete: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminProductPricing.priceListDelete: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProductPricing.priceListDelete: <fusebox:invoke object="control" methodcall="productPriceListDelete()"> */
$control->productPriceListDelete();
/* AdminProductPricing.priceListDelete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.priceListDelete: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListDelete"> */
$myFusebox['thisFuseaction'] = "priceListDelete";
/* AdminProductPricing.priceListDelete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.priceListDelete: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListDelete"> */
$myFusebox['thisFuseaction'] = "priceListDelete";

?>