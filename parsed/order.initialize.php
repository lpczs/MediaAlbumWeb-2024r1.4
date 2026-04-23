<?php

/* Order.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* Order.initialize: <fusebox:instantiate object="control" class="Order_control"> */
include_once($application["fusebox"]["WebRootToAppRootPath"]."Order/Order_control.php");
$control = new Order_control;
/* Order.initialize: <fusebox:invoke object="control" methodcall="assertRequestMethod(['GET'])"> */
$control->assertRequestMethod(['GET']);
/* Order.initialize: <fusebox:invoke object="control" methodcall="initialize()"> */
$control->initialize();
/* Order.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";
/* Order.initialize: <fusebox:set name="myFusebox['thisCircuit']" value="Order"> */
$myFusebox['thisCircuit'] = "Order";
/* Order.initialize: <fusebox:set name="myFusebox['thisFuseaction']" value="initialize"> */
$myFusebox['thisFuseaction'] = "initialize";

?>