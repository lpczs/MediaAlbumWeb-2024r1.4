<?php

/* AdminProducts.productLinkingList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.productLinkingList: <fusebox:set name="myFusebox['thisFuseaction']" value="productLinkingList"> */
$myFusebox['thisFuseaction'] = "productLinkingList";
/* AdminProducts.productLinkingList: <fusebox:instantiate object="control" class="AdminProducts_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProducts/AdminProducts_control.php");
$control = new AdminProducts_control;
/* AdminProducts.productLinkingList: <fusebox:invoke object="control" methodcall="productLinkingList()"> */
$control->productLinkingList();
/* AdminProducts.productLinkingList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.productLinkingList: <fusebox:set name="myFusebox['thisFuseaction']" value="productLinkingList"> */
$myFusebox['thisFuseaction'] = "productLinkingList";
/* AdminProducts.productLinkingList: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.productLinkingList: <fusebox:set name="myFusebox['thisFuseaction']" value="productLinkingList"> */
$myFusebox['thisFuseaction'] = "productLinkingList";

?>