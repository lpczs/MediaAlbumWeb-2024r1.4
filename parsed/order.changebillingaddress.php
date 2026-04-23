<?php

/* Order.changeBillingAddress: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeBillingAddress: <fusebox:set name="myFusebox['thisFuseaction']" value="changeBillingAddress"> */
$myFusebox['thisFuseaction'] = "changeBillingAddress";
/* Order.changeBillingAddress: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.changeBillingAddress: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Order.changeBillingAddress: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Order.changeBillingAddress: <fusebox:invoke object="control" methodcall="changeBillingAddress()"> */
$control->changeBillingAddress();
/* Order.changeBillingAddress: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeBillingAddress: <fusebox:set name="myFusebox['thisFuseaction']" value="changeBillingAddress"> */
$myFusebox['thisFuseaction'] = "changeBillingAddress";
/* Order.changeBillingAddress: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeBillingAddress: <fusebox:set name="myFusebox['thisFuseaction']" value="changeBillingAddress"> */
$myFusebox['thisFuseaction'] = "changeBillingAddress";

?>