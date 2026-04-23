<?php

/* AdminProductPricing.getPriceListGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.getPriceListGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getPriceListGridData"> */
$myFusebox['thisFuseaction'] = "getPriceListGridData";
/* AdminProductPricing.getPriceListGridData: <fusebox:instantiate object="control" class="AdminProductPricing_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProductPricing/AdminProductPricing_control.php");
$control = new AdminProductPricing_control;
/* AdminProductPricing.getPriceListGridData: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminProductPricing.getPriceListGridData: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProductPricing.getPriceListGridData: <fusebox:invoke object="control" methodcall="getPriceListGridData()"> */
$control->getPriceListGridData();
/* AdminProductPricing.getPriceListGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.getPriceListGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getPriceListGridData"> */
$myFusebox['thisFuseaction'] = "getPriceListGridData";
/* AdminProductPricing.getPriceListGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProductPricing"> */
$myFusebox['thisCircuit'] = "AdminProductPricing";
/* AdminProductPricing.getPriceListGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getPriceListGridData"> */
$myFusebox['thisFuseaction'] = "getPriceListGridData";

?>