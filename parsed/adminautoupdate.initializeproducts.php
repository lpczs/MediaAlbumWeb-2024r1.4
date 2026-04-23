<?php

/* AdminAutoUpdate.initializeProducts: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeProducts: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeProducts"> */
$myFusebox['thisFuseaction'] = "initializeProducts";
/* AdminAutoUpdate.initializeProducts: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.initializeProducts: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminAutoUpdate.initializeProducts: <fusebox:invoke object="control" methodcall="initializeProducts()"> */
$control->initializeProducts();
/* AdminAutoUpdate.initializeProducts: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeProducts: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeProducts"> */
$myFusebox['thisFuseaction'] = "initializeProducts";
/* AdminAutoUpdate.initializeProducts: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.initializeProducts: <fusebox:set name="myFusebox['thisFuseaction']" value="initializeProducts"> */
$myFusebox['thisFuseaction'] = "initializeProducts";

?>