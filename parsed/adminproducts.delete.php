<?php

/* AdminProducts.delete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.delete: <fusebox:set name="myFusebox['thisFuseaction']" value="delete"> */
$myFusebox['thisFuseaction'] = "delete";
/* AdminProducts.delete: <fusebox:instantiate object="control" class="AdminProducts_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProducts/AdminProducts_control.php");
$control = new AdminProducts_control;
/* AdminProducts.delete: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminProducts.delete: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProducts.delete: <fusebox:invoke object="control" methodcall="productDelete()"> */
$control->productDelete();
/* AdminProducts.delete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.delete: <fusebox:set name="myFusebox['thisFuseaction']" value="delete"> */
$myFusebox['thisFuseaction'] = "delete";
/* AdminProducts.delete: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.delete: <fusebox:set name="myFusebox['thisFuseaction']" value="delete"> */
$myFusebox['thisFuseaction'] = "delete";

?>