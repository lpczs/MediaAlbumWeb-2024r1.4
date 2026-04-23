<?php

/* AdminProducts.productConfigDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.productConfigDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="productConfigDisplay"> */
$myFusebox['thisFuseaction'] = "productConfigDisplay";
/* AdminProducts.productConfigDisplay: <fusebox:instantiate object="control" class="AdminProducts_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProducts/AdminProducts_control.php");
$control = new AdminProducts_control;
/* AdminProducts.productConfigDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminProducts.productConfigDisplay: <fusebox:invoke object="control" methodcall="productConfigDisplay()"> */
$control->productConfigDisplay();
/* AdminProducts.productConfigDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.productConfigDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="productConfigDisplay"> */
$myFusebox['thisFuseaction'] = "productConfigDisplay";
/* AdminProducts.productConfigDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.productConfigDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="productConfigDisplay"> */
$myFusebox['thisFuseaction'] = "productConfigDisplay";

?>