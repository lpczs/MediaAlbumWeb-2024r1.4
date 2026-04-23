<?php

/* AdminProducts.linkingPreviewDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.linkingPreviewDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="linkingPreviewDisplay"> */
$myFusebox['thisFuseaction'] = "linkingPreviewDisplay";
/* AdminProducts.linkingPreviewDisplay: <fusebox:instantiate object="control" class="AdminProducts_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProducts/AdminProducts_control.php");
$control = new AdminProducts_control;
/* AdminProducts.linkingPreviewDisplay: <fusebox:invoke object="control" methodcall="linkingPreviewDisplay()"> */
$control->linkingPreviewDisplay();
/* AdminProducts.linkingPreviewDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.linkingPreviewDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="linkingPreviewDisplay"> */
$myFusebox['thisFuseaction'] = "linkingPreviewDisplay";
/* AdminProducts.linkingPreviewDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.linkingPreviewDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="linkingPreviewDisplay"> */
$myFusebox['thisFuseaction'] = "linkingPreviewDisplay";

?>