<?php

/* AdminProducts.getLinkedProductCode: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.getLinkedProductCode: <fusebox:set name="myFusebox['thisFuseaction']" value="getLinkedProductCode"> */
$myFusebox['thisFuseaction'] = "getLinkedProductCode";
/* AdminProducts.getLinkedProductCode: <fusebox:instantiate object="control" class="AdminProducts_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProducts/AdminProducts_control.php");
$control = new AdminProducts_control;
/* AdminProducts.getLinkedProductCode: <fusebox:invoke object="control" methodcall="getLinkedProductCode()"> */
$control->getLinkedProductCode();
/* AdminProducts.getLinkedProductCode: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.getLinkedProductCode: <fusebox:set name="myFusebox['thisFuseaction']" value="getLinkedProductCode"> */
$myFusebox['thisFuseaction'] = "getLinkedProductCode";
/* AdminProducts.getLinkedProductCode: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.getLinkedProductCode: <fusebox:set name="myFusebox['thisFuseaction']" value="getLinkedProductCode"> */
$myFusebox['thisFuseaction'] = "getLinkedProductCode";

?>