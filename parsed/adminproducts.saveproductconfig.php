<?php

/* AdminProducts.saveProductConfig: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.saveProductConfig: <fusebox:set name="myFusebox['thisFuseaction']" value="saveProductConfig"> */
$myFusebox['thisFuseaction'] = "saveProductConfig";
/* AdminProducts.saveProductConfig: <fusebox:instantiate object="control" class="AdminProducts_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProducts/AdminProducts_control.php");
$control = new AdminProducts_control;
/* AdminProducts.saveProductConfig: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminProducts.saveProductConfig: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminProducts.saveProductConfig: <fusebox:invoke object="control" methodcall="saveProductConfig()"> */
$control->saveProductConfig();
/* AdminProducts.saveProductConfig: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.saveProductConfig: <fusebox:set name="myFusebox['thisFuseaction']" value="saveProductConfig"> */
$myFusebox['thisFuseaction'] = "saveProductConfig";
/* AdminProducts.saveProductConfig: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.saveProductConfig: <fusebox:set name="myFusebox['thisFuseaction']" value="saveProductConfig"> */
$myFusebox['thisFuseaction'] = "saveProductConfig";

?>