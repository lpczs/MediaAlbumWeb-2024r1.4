<?php

/* AdminProductPricing.priceListEdit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.priceListEdit: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListEdit"> */
$myFusebox['thisFuseaction'] = "priceListEdit";
/* AdminProductPricing.priceListEdit: <fusebox:instantiate object="control" class="AdminProductPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductPricing/AdminProductPricing_control.php");
$control = new AdminProductPricing_control;
/* AdminProductPricing.priceListEdit: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminProductPricing.priceListEdit: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProductPricing.priceListEdit: <fusebox:invoke object="control" methodcall="priceListEdit()"> */
$control->priceListEdit();
/* AdminProductPricing.priceListEdit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.priceListEdit: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListEdit"> */
$myFusebox['thisFuseaction'] = "priceListEdit";
/* AdminProductPricing.priceListEdit: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.priceListEdit: <fusebox:set name="myFusebox['thisFuseaction']" value="priceListEdit"> */
$myFusebox['thisFuseaction'] = "priceListEdit";

?>