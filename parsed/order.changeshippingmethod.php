<?php

/* Order.changeShippingMethod: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeShippingMethod: <fusebox:set name="myFusebox['thisFuseaction']" value="changeShippingMethod"> */
$myFusebox['thisFuseaction'] = "changeShippingMethod";
/* Order.changeShippingMethod: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.changeShippingMethod: <fusebox:invoke object="control" methodcall="changeShippingMethod()"> */
$control->changeShippingMethod();
/* Order.changeShippingMethod: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeShippingMethod: <fusebox:set name="myFusebox['thisFuseaction']" value="changeShippingMethod"> */
$myFusebox['thisFuseaction'] = "changeShippingMethod";
/* Order.changeShippingMethod: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.changeShippingMethod: <fusebox:set name="myFusebox['thisFuseaction']" value="changeShippingMethod"> */
$myFusebox['thisFuseaction'] = "changeShippingMethod";

?>