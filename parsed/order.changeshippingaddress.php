<?php

/* Order.changeShippingAddress: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeShippingAddress: <fusebox:set name="myFusebox['thisFuseaction']" value="changeShippingAddress"> */
$myFusebox['thisFuseaction'] = "changeShippingAddress";
/* Order.changeShippingAddress: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.changeShippingAddress: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Order.changeShippingAddress: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Order.changeShippingAddress: <fusebox:invoke object="control" methodcall="changeShippingAddress()"> */
$control->changeShippingAddress();
/* Order.changeShippingAddress: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeShippingAddress: <fusebox:set name="myFusebox['thisFuseaction']" value="changeShippingAddress"> */
$myFusebox['thisFuseaction'] = "changeShippingAddress";
/* Order.changeShippingAddress: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeShippingAddress: <fusebox:set name="myFusebox['thisFuseaction']" value="changeShippingAddress"> */
$myFusebox['thisFuseaction'] = "changeShippingAddress";

?>