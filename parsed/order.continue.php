<?php

/* Order.continue: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.continue: <fusebox:set name="myFusebox['thisFuseaction']" value="continue"> */
$myFusebox['thisFuseaction'] = "continue";
/* Order.continue: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.continue: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Order.continue: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Order.continue: <fusebox:invoke object="control" methodcall="orderContinue()"> */
$control->orderContinue();
/* Order.continue: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.continue: <fusebox:set name="myFusebox['thisFuseaction']" value="continue"> */
$myFusebox['thisFuseaction'] = "continue";
/* Order.continue: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.continue: <fusebox:set name="myFusebox['thisFuseaction']" value="continue"> */
$myFusebox['thisFuseaction'] = "continue";

?>