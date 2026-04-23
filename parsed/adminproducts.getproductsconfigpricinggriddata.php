<?php

/* AdminProducts.getProductsConfigPricingGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.getProductsConfigPricingGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getProductsConfigPricingGridData"> */
$myFusebox['thisFuseaction'] = "getProductsConfigPricingGridData";
/* AdminProducts.getProductsConfigPricingGridData: <fusebox:instantiate object="control" class="AdminProducts_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProducts/AdminProducts_control.php");
$control = new AdminProducts_control;
/* AdminProducts.getProductsConfigPricingGridData: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminProducts.getProductsConfigPricingGridData: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProducts.getProductsConfigPricingGridData: <fusebox:invoke object="control" methodcall="getProductsConfigPricingGridData()"> */
$control->getProductsConfigPricingGridData();
/* AdminProducts.getProductsConfigPricingGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.getProductsConfigPricingGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getProductsConfigPricingGridData"> */
$myFusebox['thisFuseaction'] = "getProductsConfigPricingGridData";
/* AdminProducts.getProductsConfigPricingGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.getProductsConfigPricingGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getProductsConfigPricingGridData"> */
$myFusebox['thisFuseaction'] = "getProductsConfigPricingGridData";

?>