<?php

/* AdminProducts.checkProductDeletionWarnings: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.checkProductDeletionWarnings: <fusebox:set name="myFusebox['thisFuseaction']" value="checkProductDeletionWarnings"> */
$myFusebox['thisFuseaction'] = "checkProductDeletionWarnings";
/* AdminProducts.checkProductDeletionWarnings: <fusebox:instantiate object="control" class="AdminProducts_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProducts/AdminProducts_control.php");
$control = new AdminProducts_control;
/* AdminProducts.checkProductDeletionWarnings: <fusebox:invoke object="control" methodcall="checkProductDeletionWarnings()"> */
$control->checkProductDeletionWarnings();
/* AdminProducts.checkProductDeletionWarnings: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.checkProductDeletionWarnings: <fusebox:set name="myFusebox['thisFuseaction']" value="checkProductDeletionWarnings"> */
$myFusebox['thisFuseaction'] = "checkProductDeletionWarnings";
/* AdminProducts.checkProductDeletionWarnings: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.checkProductDeletionWarnings: <fusebox:set name="myFusebox['thisFuseaction']" value="checkProductDeletionWarnings"> */
$myFusebox['thisFuseaction'] = "checkProductDeletionWarnings";

?>