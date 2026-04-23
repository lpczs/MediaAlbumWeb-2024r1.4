<?php

/* Order.selectStoreDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.selectStoreDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="selectStoreDisplay"> */
$myFusebox['thisFuseaction'] = "selectStoreDisplay";
/* Order.selectStoreDisplay: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.selectStoreDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* Order.selectStoreDisplay: <fusebox:invoke object="control" methodcall="selectStoreDisplay()"> */
$control->selectStoreDisplay();
/* Order.selectStoreDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.selectStoreDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="selectStoreDisplay"> */
$myFusebox['thisFuseaction'] = "selectStoreDisplay";
/* Order.selectStoreDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.selectStoreDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="selectStoreDisplay"> */
$myFusebox['thisFuseaction'] = "selectStoreDisplay";

?>