<?php

/* AdminAutoUpdate.listProducts: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.listProducts: <fusebox:set name="myFusebox['thisFuseaction']" value="listProducts"> */
$myFusebox['thisFuseaction'] = "listProducts";
/* AdminAutoUpdate.listProducts: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.listProducts: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminAutoUpdate.listProducts: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminAutoUpdate.listProducts: <fusebox:invoke object="control" methodcall="listProducts()"> */
$control->listProducts();
/* AdminAutoUpdate.listProducts: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.listProducts: <fusebox:set name="myFusebox['thisFuseaction']" value="listProducts"> */
$myFusebox['thisFuseaction'] = "listProducts";
/* AdminAutoUpdate.listProducts: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.listProducts: <fusebox:set name="myFusebox['thisFuseaction']" value="listProducts"> */
$myFusebox['thisFuseaction'] = "listProducts";

?>