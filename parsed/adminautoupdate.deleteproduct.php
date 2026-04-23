<?php

/* AdminAutoUpdate.deleteProduct: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.deleteProduct: <fusebox:set name="myFusebox['thisFuseaction']" value="deleteProduct"> */
$myFusebox['thisFuseaction'] = "deleteProduct";
/* AdminAutoUpdate.deleteProduct: <fusebox:instantiate object="control" class="AdminAutoUpdate_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminAutoUpdate/AdminAutoUpdate_control.php");
$control = new AdminAutoUpdate_control;
/* AdminAutoUpdate.deleteProduct: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* AdminAutoUpdate.deleteProduct: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* AdminAutoUpdate.deleteProduct: <fusebox:invoke object="control" methodcall="deleteProduct()"> */
$control->deleteProduct();
/* AdminAutoUpdate.deleteProduct: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.deleteProduct: <fusebox:set name="myFusebox['thisFuseaction']" value="deleteProduct"> */
$myFusebox['thisFuseaction'] = "deleteProduct";
/* AdminAutoUpdate.deleteProduct: <fusebox:set name="myFusebox['thisCircuit']" value="AdminAutoUpdate"> */
$myFusebox['thisCircuit'] = "AdminAutoUpdate";
/* AdminAutoUpdate.deleteProduct: <fusebox:set name="myFusebox['thisFuseaction']" value="deleteProduct"> */
$myFusebox['thisFuseaction'] = "deleteProduct";

?>