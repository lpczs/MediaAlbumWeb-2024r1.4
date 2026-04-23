<?php

/* AdminProducts.refreshProductTree: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.refreshProductTree: <fusebox:set name="myFusebox['thisFuseaction']" value="refreshProductTree"> */
$myFusebox['thisFuseaction'] = "refreshProductTree";
/* AdminProducts.refreshProductTree: <fusebox:instantiate object="control" class="AdminProducts_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."AdminProducts/AdminProducts_control.php");
$control = new AdminProducts_control;
/* AdminProducts.refreshProductTree: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* AdminProducts.refreshProductTree: <fusebox:invoke object="control" methodcall="refreshProductTree()"> */
$control->refreshProductTree();
/* AdminProducts.refreshProductTree: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.refreshProductTree: <fusebox:set name="myFusebox['thisFuseaction']" value="refreshProductTree"> */
$myFusebox['thisFuseaction'] = "refreshProductTree";
/* AdminProducts.refreshProductTree: <fusebox:set name="myFusebox['thisCircuit']" value="AdminProducts"> */
$myFusebox['thisCircuit'] = "AdminProducts";
/* AdminProducts.refreshProductTree: <fusebox:set name="myFusebox['thisFuseaction']" value="refreshProductTree"> */
$myFusebox['thisFuseaction'] = "refreshProductTree";

?>