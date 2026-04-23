<?php

/* AdminProducts.productActivate: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.productActivate: <fusebox:set name="myFusebox['thisFuseaction']" value="productActivate"> */
$myFusebox['thisFuseaction'] = "productActivate";
/* AdminProducts.productActivate: <fusebox:instantiate object="control" class="AdminProducts_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProducts/AdminProducts_control.php");
$control = new AdminProducts_control;
/* AdminProducts.productActivate: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminProducts.productActivate: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProducts.productActivate: <fusebox:invoke object="control" methodcall="productActivate()"> */
$control->productActivate();
/* AdminProducts.productActivate: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.productActivate: <fusebox:set name="myFusebox['thisFuseaction']" value="productActivate"> */
$myFusebox['thisFuseaction'] = "productActivate";
/* AdminProducts.productActivate: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.productActivate: <fusebox:set name="myFusebox['thisFuseaction']" value="productActivate"> */
$myFusebox['thisFuseaction'] = "productActivate";

?>