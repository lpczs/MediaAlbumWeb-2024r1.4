<?php

/* Order.ccManualCallback: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.ccManualCallback: <fusebox:set name="myFusebox['thisFuseaction']" value="ccManualCallback"> */
$myFusebox['thisFuseaction'] = "ccManualCallback";
/* Order.ccManualCallback: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.ccManualCallback: <fusebox:invoke object="control" methodcall="ccManualCallback()"> */
$control->ccManualCallback();
/* Order.ccManualCallback: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.ccManualCallback: <fusebox:set name="myFusebox['thisFuseaction']" value="ccManualCallback"> */
$myFusebox['thisFuseaction'] = "ccManualCallback";
/* Order.ccManualCallback: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.ccManualCallback: <fusebox:set name="myFusebox['thisFuseaction']" value="ccManualCallback"> */
$myFusebox['thisFuseaction'] = "ccManualCallback";

?>