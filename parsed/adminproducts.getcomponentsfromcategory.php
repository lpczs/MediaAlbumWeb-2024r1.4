<?php

/* AdminProducts.getComponentsFromCategory: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.getComponentsFromCategory: <fusebox:set name="myFusebox['thisFuseaction']" value="getComponentsFromCategory"> */
$myFusebox['thisFuseaction'] = "getComponentsFromCategory";
/* AdminProducts.getComponentsFromCategory: <fusebox:instantiate object="control" class="AdminProducts_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProducts/AdminProducts_control.php");
$control = new AdminProducts_control;
/* AdminProducts.getComponentsFromCategory: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminProducts.getComponentsFromCategory: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProducts.getComponentsFromCategory: <fusebox:invoke object="control" methodcall="getComponentsFromCategory()"> */
$control->getComponentsFromCategory();
/* AdminProducts.getComponentsFromCategory: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.getComponentsFromCategory: <fusebox:set name="myFusebox['thisFuseaction']" value="getComponentsFromCategory"> */
$myFusebox['thisFuseaction'] = "getComponentsFromCategory";
/* AdminProducts.getComponentsFromCategory: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.getComponentsFromCategory: <fusebox:set name="myFusebox['thisFuseaction']" value="getComponentsFromCategory"> */
$myFusebox['thisFuseaction'] = "getComponentsFromCategory";

?>