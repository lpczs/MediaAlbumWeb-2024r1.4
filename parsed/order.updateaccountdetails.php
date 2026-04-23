<?php

/* Order.updateAccountDetails: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.updateAccountDetails: <fusebox:set name="myFusebox['thisFuseaction']" value="updateAccountDetails"> */
$myFusebox['thisFuseaction'] = "updateAccountDetails";
/* Order.updateAccountDetails: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.updateAccountDetails: <fusebox:invoke object="control" methodcall="assertRequestMethod(['POST'])"> */
$control->assertRequestMethod(['POST']);
/* Order.updateAccountDetails: <fusebox:invoke object="control" methodcall="assertCsrfToken()"> */
$control->assertCsrfToken();
/* Order.updateAccountDetails: <fusebox:invoke object="control" methodcall="updateAccountDetails()"> */
$control->updateAccountDetails();
/* Order.updateAccountDetails: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.updateAccountDetails: <fusebox:set name="myFusebox['thisFuseaction']" value="updateAccountDetails"> */
$myFusebox['thisFuseaction'] = "updateAccountDetails";
/* Order.updateAccountDetails: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.updateAccountDetails: <fusebox:set name="myFusebox['thisFuseaction']" value="updateAccountDetails"> */
$myFusebox['thisFuseaction'] = "updateAccountDetails";

?>