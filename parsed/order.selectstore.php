<?php

/* Order.selectStore: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.selectStore: <fusebox:set name="myFusebox['thisFuseaction']" value="selectStore"> */
$myFusebox['thisFuseaction'] = "selectStore";
/* Order.selectStore: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.selectStore: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Order.selectStore: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Order.selectStore: <fusebox:invoke object="control" methodcall="selectStore()"> */
$control->selectStore();
/* Order.selectStore: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.selectStore: <fusebox:set name="myFusebox['thisFuseaction']" value="selectStore"> */
$myFusebox['thisFuseaction'] = "selectStore";
/* Order.selectStore: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.selectStore: <fusebox:set name="myFusebox['thisFuseaction']" value="selectStore"> */
$myFusebox['thisFuseaction'] = "selectStore";

?>