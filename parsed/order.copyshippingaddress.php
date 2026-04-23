<?php

/* Order.copyShippingAddress: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.copyShippingAddress: <fusebox:set name="myFusebox['thisFuseaction']" value="copyShippingAddress"> */
$myFusebox['thisFuseaction'] = "copyShippingAddress";
/* Order.copyShippingAddress: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.copyShippingAddress: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Order.copyShippingAddress: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Order.copyShippingAddress: <fusebox:invoke object="control" methodcall="copyShippingAddress()"> */
$control->copyShippingAddress();
/* Order.copyShippingAddress: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.copyShippingAddress: <fusebox:set name="myFusebox['thisFuseaction']" value="copyShippingAddress"> */
$myFusebox['thisFuseaction'] = "copyShippingAddress";
/* Order.copyShippingAddress: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.copyShippingAddress: <fusebox:set name="myFusebox['thisFuseaction']" value="copyShippingAddress"> */
$myFusebox['thisFuseaction'] = "copyShippingAddress";

?>