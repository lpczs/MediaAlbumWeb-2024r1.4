<?php

/* AdminProducts.getLinkingPreviewGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.getLinkingPreviewGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getLinkingPreviewGridData"> */
$myFusebox['thisFuseaction'] = "getLinkingPreviewGridData";
/* AdminProducts.getLinkingPreviewGridData: <fusebox:instantiate object="control" class="AdminProducts_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProducts/AdminProducts_control.php");
$control = new AdminProducts_control;
/* AdminProducts.getLinkingPreviewGridData: <fusebox:invoke object="control" methodcall="getLinkingPreviewGridData()"> */
$control->getLinkingPreviewGridData();
/* AdminProducts.getLinkingPreviewGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.getLinkingPreviewGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getLinkingPreviewGridData"> */
$myFusebox['thisFuseaction'] = "getLinkingPreviewGridData";
/* AdminProducts.getLinkingPreviewGridData: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.getLinkingPreviewGridData: <fusebox:set name="myFusebox['thisFuseaction']" value="getLinkingPreviewGridData"> */
$myFusebox['thisFuseaction'] = "getLinkingPreviewGridData";

?>