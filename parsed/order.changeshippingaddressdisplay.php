<?php

/* Order.changeShippingAddressDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeShippingAddressDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="changeShippingAddressDisplay"> */
$myFusebox['thisFuseaction'] = "changeShippingAddressDisplay";
/* Order.changeShippingAddressDisplay: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.changeShippingAddressDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Order.changeShippingAddressDisplay: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Order.changeShippingAddressDisplay: <fusebox:invoke object="control" methodcall="changeShippingAddressDisplay()"> */
$control->changeShippingAddressDisplay();
/* Order.changeShippingAddressDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeShippingAddressDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="changeShippingAddressDisplay"> */
$myFusebox['thisFuseaction'] = "changeShippingAddressDisplay";
/* Order.changeShippingAddressDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeShippingAddressDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="changeShippingAddressDisplay"> */
$myFusebox['thisFuseaction'] = "changeShippingAddressDisplay";

?>