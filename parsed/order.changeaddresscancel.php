<?php

/* Order.changeAddressCancel: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeAddressCancel: <fusebox:set name="myFusebox['thisFuseaction']" value="changeAddressCancel"> */
$myFusebox['thisFuseaction'] = "changeAddressCancel";
/* Order.changeAddressCancel: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.changeAddressCancel: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Order.changeAddressCancel: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Order.changeAddressCancel: <fusebox:invoke object="control" methodcall="changeAddressCancel()"> */
$control->changeAddressCancel();
/* Order.changeAddressCancel: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeAddressCancel: <fusebox:set name="myFusebox['thisFuseaction']" value="changeAddressCancel"> */
$myFusebox['thisFuseaction'] = "changeAddressCancel";
/* Order.changeAddressCancel: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeAddressCancel: <fusebox:set name="myFusebox['thisFuseaction']" value="changeAddressCancel"> */
$myFusebox['thisFuseaction'] = "changeAddressCancel";

?>