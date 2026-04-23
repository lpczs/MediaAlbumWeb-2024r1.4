<?php

/* Order.changeBillingAddressDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeBillingAddressDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="changeBillingAddressDisplay"> */
$myFusebox['thisFuseaction'] = "changeBillingAddressDisplay";
/* Order.changeBillingAddressDisplay: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.changeBillingAddressDisplay: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Order.changeBillingAddressDisplay: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Order.changeBillingAddressDisplay: <fusebox:invoke object="control" methodcall="changeBillingAddressDisplay()"> */
$control->changeBillingAddressDisplay();
/* Order.changeBillingAddressDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeBillingAddressDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="changeBillingAddressDisplay"> */
$myFusebox['thisFuseaction'] = "changeBillingAddressDisplay";
/* Order.changeBillingAddressDisplay: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeBillingAddressDisplay: <fusebox:set name="myFusebox['thisFuseaction']" value="changeBillingAddressDisplay"> */
$myFusebox['thisFuseaction'] = "changeBillingAddressDisplay";

?>