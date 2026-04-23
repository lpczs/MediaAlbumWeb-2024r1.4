<?php

/* Order.ccCancelCallback: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.ccCancelCallback: <fusebox:set name="myFusebox['thisFuseaction']" value="ccCancelCallback"> */
$myFusebox['thisFuseaction'] = "ccCancelCallback";
/* Order.ccCancelCallback: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.ccCancelCallback: <fusebox:invoke object="control" methodcall="ccCancelCallback()"> */
$control->ccCancelCallback();
/* Order.ccCancelCallback: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.ccCancelCallback: <fusebox:set name="myFusebox['thisFuseaction']" value="ccCancelCallback"> */
$myFusebox['thisFuseaction'] = "ccCancelCallback";
/* Order.ccCancelCallback: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.ccCancelCallback: <fusebox:set name="myFusebox['thisFuseaction']" value="ccCancelCallback"> */
$myFusebox['thisFuseaction'] = "ccCancelCallback";

?>